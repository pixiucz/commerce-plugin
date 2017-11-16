<?php namespace Pixiu\Commerce\Classes;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Pixiu\Commerce\Classes\OrderStatus as OS;
use Pixiu\Commerce\Classes\PaymentStatus as PS;
use Pixiu\Commerce\Classes\OrderLogger;
use Pixiu\Commerce\Models\OrderLog;
use Illuminate\Support\Facades\Mail;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\Invoice\CanceledInvoiceManager;

class OrderStatusFSM
{
    private $allButtons;
    private $order;


    /**
     * OrderStatusFSM constructor.
     * @param $state
     * @param $paymentMethod
     * @param $paymentStatus
     */
    public function __construct(\Pixiu\Commerce\Models\Order $order)
    {
        $this->order = $order;
        $this->allButtons = $this->generateButtons();
    }

    public function getAvailableActions() : array
    {
        $buttons = [];

        if ($this->order->status === OS::FINISHED OR $this->order->status === OS::CANCELED) { return []; }

        if (count($this->order->variants)) {
            $buttons = [
                $this->allButtons['canceled']
            ];

            if ($this->order->status === OS::NEW) {
                if ($this->order->payment_status == PS::AWAITING_PAYMENT) {
                    array_push($buttons, $this->allButtons[PS::PAID]);
                    return array_reverse($buttons);
                }



                if ($this->order->delivery_option->personal_collection === 1) {
                    array_push($buttons, $this->allButtons[OS::READY_FOR_COLLECTION]);
                    return array_reverse($buttons);
                } else {
                    array_push($buttons, $this->allButtons[OS::SHIPPED]);
                    return array_reverse($buttons);
                }
            }
        }

        if ($this->order->status === OS::SHIPPED OR $this->order->status === OS::READY_FOR_COLLECTION) {
            array_push($buttons, $this->allButtons[OS::FINISHED]);
            return array_reverse($buttons);
        }
        return $buttons;
    }

    /**
     * @param string $action
     */
    public function manageStateChange(string $action): bool
    {
        switch ($action) {
            case str_slug(OS::READY_FOR_COLLECTION):
                $this->changeStateToReady();
                break;
            case str_slug(OS::CANCELED):
                $this->changeStateToCanceled();
                break;
            case str_slug(OS::SHIPPED):
                $this->changeStateToShipped();
                break;
            case str_slug(OS::FINISHED):
                $this->changeStateToFinished();
                break;
            case str_slug(PS::PAID):
                $this->changeStateToPaid();
                break;
        }
        $this->order->save();
        return true;
    }

    /**
     * @return array
     */
    private function generateButtons(): array
    {
        return [
            OS::FINISHED => [
                'label' => OS::getFinished(),
                'id' => str_slug(OS::FINISHED, '-')
            ],
            OS::SHIPPED => [
                'label' => OS::getShipped(),
                'id' => str_slug(OS::SHIPPED, '-')
            ],
            OS::CANCELED => [
                'label' => OS::getCanceled(),
                'id' => str_slug(OS::CANCELED, '-'),
                'class' => 'btn-outline-canceled'
            ],
            OS::READY_FOR_COLLECTION => [
                'label' => OS::getReadyForCollection(),
                'id' => str_slug(OS::READY_FOR_COLLECTION, '-')
            ],
            PS::PAID => [
                'label' => 'Zaplaceno',
                'id' => str_slug(PS::PAID, '-')
            ]
        ];
    }

    public function changeStateToNew()
    {
        $this->order->status = OS::NEW;

        if ($this->order->payment_method->cash_on_delivery === 1) {
            $this->order->payment_status = PS::CASH_ON_DELIVERY;
        }

        $this->order->save();
    }

    public function changeStateToPaid()
    {
        $this->order->payment_status = PS::PAID;
        OrderLogger::addLog($this->order, Lang::get('pixiu.commerce::lang.orderlog.paid'), 'text-info');
    }

    public function changeStateToReady()
    {
        $this->order->status = OS::READY_FOR_COLLECTION;
        OrderLogger::addLog($this->order, Lang::get('pixiu.commerce::lang.orderlog.ready'), 'text-info');
        $this->order->variantsLeftWarehouse();
    }

    public function changeStateToCanceled()
    {
        $this->order->status = OS::CANCELED;
        OrderLogger::addLog($this->order, Lang::get('pixiu.commerce::lang.orderlog.canceled'), 'text-danger');
        $this->order->returnVariantsToStock();
        (new CanceledInvoiceManager($this->order))->generateInvoice();
    }

    public function changeStateToShipped()
    {
        $this->order->status = OS::SHIPPED;
        OrderLogger::addLog($this->order, Lang::get('pixiu.commerce::lang.orderlog.shipped'), 'text-info');
        $this->order->variantsLeftWarehouse();
    }

    public function changeStateToFinished()
    {
        $this->order->status = OS::FINISHED;
        OrderLogger::addLog($this->order, Lang::get('pixiu.commerce::lang.orderlog.finished'), 'text-success');
    }
}