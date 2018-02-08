<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Redirect;
use Pixiu\Commerce\Classes\Invoice\NormalInvoiceManager;
use Pixiu\Commerce\Classes\Invoice\CanceledInvoiceManager;
use Pixiu\Commerce\Classes\Invoice\RefundInvoiceManager;
use Pixiu\Commerce\Classes\InvoiceHandler;
use Pixiu\Commerce\Classes\PaymentStatus;
use Pixiu\Commerce\Models\Address;
use Pixiu\Commerce\Models\Order;
use Illuminate\Support\Facades\Response;
use Pixiu\Commerce\classes\OrderStatus;
use Pixiu\Commerce\Classes\OrderStatusFSM as FSM;
use Illuminate\Support\Facades\Lang;

/**
 * Orders Back-end Controller
 */
class Orders extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    private $fsm;

    public function __construct($id = null)
    {
        parent::__construct();
        BackendMenu::setContext('Pixiu.Commerce', 'commerce', 'orders');

        $this->vars['variantsOptions'] = [];

    }

    public function update($id = null)
    {
        parent::update($id);
        $order = Order::find($id);

        if (($order->status !== OrderStatus::NEW) OR ($order->payment_status === PaymentStatus::PAID)){
            $this->vars['variantsOptions'] = ['readOnly' => true];
        } else {
            $this->vars['variantsOptions'] = ['readOnly' => false];
        }

        $this->fsm = new FSM($order);
        $this->vars['buttons'] = $this->fsm->getAvailableActions();

    }

    public function preview($id = null)
    {
        parent::preview($id);
        $order = Order::find($id);
        $this->fsm = new FSM($order);

        // Setting up buttons for state change
        $this->vars['buttons'] = $this->fsm->getAvailableActions();
        $this->vars['logs'] = $order->logs()->orderBy('created_at', 'desc')->get()->toArray();
        $this->vars['variantsOptions'] = ['readOnly' => true];
    }

    public function changeState($id, $action)
    {
        $this->fsm = new FSM(Order::find($id));
        if ($this->fsm->manageStateChange($action)) {
            \Flash::success('Status zmenen');
            return Redirect::back();
        };

        \Flash::error('Změna stavu selhala');
        return Redirect::back();
    }

    public function manageStocks($model)
    {
        $model->variants()->withPivot('lowered_stock', 'quantity')->get()->each(function ($item, $key) {
            if ($item->pivot->lowered_stock === 0) {
                // $item->changeStock($item->pivot->quantity);
                $item->changeReservedStock($item->pivot->quantity);
                $item->pivot->lowered_stock = true;
                $item->pivot->save();
                $item->save();
            }
        });
    }

    public function formAfterSave($model)
    {
        if ($model->status === OrderStatus::NEW) {
            $this->manageStocks($model);
        }
        $this->createInvoice($model);
    }

    public function createInvoice($model) {
        if ($model->status === OrderStatus::NEW){
            (new NormalInvoiceManager($model))->generateInvoice();
        }
    }

    public function onRefundsPartial($id = null)
    {
        $this->vars['orderId'] = $id;
        $this->vars['variants'] = Order::find($id)->variants;
        return $this->makePartial('refunds_popup');
    }

    public function onRefundVariants()
    {
        $orderId = post('orderId');
        $postVariants = post('variants');
        $order = Order::findOrFail($orderId);


        $variants = $this->handleStocksOnRefund($postVariants, $order);

        // TODO: Fix invoices
        // (new InvoiceHandler('cs', $order))->generateRefundInvoice($variants);
        (new RefundInvoiceManager($order))->generateInvoice($variants);

        \Flash::success('Vratky zapocitany');
        return \redirect()->refresh();

    }

    /**
     * @param $postVariants
     * @param $order
     * @return mixed
     */
    private function handleStocksOnRefund($postVariants, $order)
    {
        $variants = [];

        foreach ($postVariants as $postVariant) {
            $orderVariant = $order->variants()->withPivot('refunded_quantity', 'quantity')->find($postVariant['id']);
            if (array_key_exists('checked', $postVariant)) {
                if ($postVariant['quantity'] === "") {
                    $invoiceRefundedQuantity = $this->refundFullOrderAmount($orderVariant);
                } else {
                    $invoiceRefundedQuantity = $this->refundOnlyProvidedAmount($orderVariant, $postVariant);
                }
                array_push($variants, ['variant' => $orderVariant, 'refunded_quantity' => $invoiceRefundedQuantity]);
            }
        }

        return $variants;
    }

    /**
     * @param $orderVariant
     * @return mixed
     */
    private function refundFullOrderAmount($orderVariant)
    {
        $orderVariant->changeStock($orderVariant->pivot->quantity);
        $orderVariant->pivot->refunded_quantity += $orderVariant->pivot->quantity;
        $invoiceRefundedQuantity = $orderVariant->pivot->quantity;
        $orderVariant->pivot->quantity = 0;
        $orderVariant->pivot->save();
        return $invoiceRefundedQuantity;
    }

    /**
     * @param $orderVariant
     * @param $postVariant
     * @return mixed
     */
    private function refundOnlyProvidedAmount($orderVariant, $postVariant)
    {
        $orderVariant->changeStock($postVariant['quantity']);
        $orderVariant->pivot->quantity -= $postVariant['quantity'];
        $orderVariant->pivot->refunded_quantity += $postVariant['quantity'];
        $invoiceRefundedQuantity = $postVariant['quantity'];
        $orderVariant->pivot->save();
        return $invoiceRefundedQuantity;
    }
}
