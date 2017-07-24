<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Redirect;
use Pixiu\Commerce\Classes\InvoiceRobot;
use Pixiu\Commerce\Models\Order;
use Illuminate\Support\Facades\Response;
use Pixiu\Commerce\classes\OrderStatus;
use Pixiu\Commerce\Classes\OrderStatusFSM as FSM;
use Illuminate\Support\Facades\Lang;
use Pixiu\Commerce\Models\ProductVariant;

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
    }

    public function update($id = null)
    {
        $this->relationConfig = "config_relation_finished.yaml";

        parent::update($id);
        $this->fsm = new FSM(Order::find($id));
        $this->vars['buttons'] = $this->fsm->getAvailableActions();

    }

    public function preview($id = null)
    {
        parent::preview($id);
        $this->fsm = new FSM(Order::find($id));
        $this->vars['buttons'] = $this->fsm->getAvailableActions();
        $this->vars['logs'] = Order::find($id)->logs()->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function changeState($id, $action)
    {
        $this->fsm = new FSM(Order::find($id));
        if ($this->fsm->manageStateChange($action)) {
            \Flash::success('Status zmenen');
            return Redirect::back();
        };

        \Flash::error('Oooops');
        return Redirect::back();
    }

    public function manageStocks($model)
    {
        if ($model->status === "canceled") {
            $model->variants()->withPivot('lowered_stock', 'quantity')->get()->each(function ($item, $key) {
                if ($item->pivot->lowered_stock === 0){
                    $item->in_stock -= $item->pivot->quantity;
                    $item->pivot->lowered_stock = true;
                    $item->pivot->save();
                    $item->save();
                }
            });
        } else {
            $model->variants()->withPivot('lowered_stock', 'quantity')->get()->each(function ($item, $key) {
                if ($item->pivot->lowered_stock === 1){
                    $item->in_stock += $item->pivot->quantity;
                    $item->pivot->lowered_stock = false;
                    $item->pivot->save();
                    $item->save();
                }
            });
        }
    }

    public function formAfterSave($model)
    {
        // $this->manageStocks($model);
        $this->createInvoice($model->id);
    }

    public function createInvoice($id)
    {
        (new InvoiceRobot('cs', Order::find($id)))->generateInvoice();
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
        $variants = [];
        $order = Order::findOrFail($orderId);


        foreach ($postVariants as $postVariant){
            $orderVariant = $order->variants()->withPivot('refunded_quantity', 'quantity')->find($postVariant['id']);
            if (array_key_exists('checked', $postVariant)) {
                array_push($variants, $orderVariant);
                if ($postVariant['quantity'] === "") {
                    $orderVariant->pivot->refunded_quantity = $orderVariant->pivot->quantity;
                    $orderVariant->pivot->save();
                } else {
                    $orderVariant->pivot->refunded_quantity = $postVariant['quantity'];
                    $orderVariant->pivot->save();
                }
            } else {
                $orderVariant->pivot->refunded_quantity = 0;
                $orderVariant->pivot->save();
            }
        }

        (new InvoiceRobot('cs', $order))->generateStornoInvoice($variants);

        \Flash::success('Vratky zapocitany');
        return \redirect()->refresh();

    }
}
