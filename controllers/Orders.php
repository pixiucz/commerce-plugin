<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Pixiu\Commerce\Classes\InvoiceRobot;
use Pixiu\Commerce\Models\Order;
use Illuminate\Support\Facades\Response;
use Pixiu\Commerce\Models\OrderStatus;

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

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Pixiu.Commerce', 'commerce', 'orders');
    }

    public function manageStocks($model)
    {
        $canceledFlag = $model->order_status->is_canceled;
        if ($canceledFlag === 0) {
            $model->variants()->withPivot('lowered_stock', 'quantity')->get()->each(function ($item, $key) use ($canceledFlag) {
                if ($item->pivot->lowered_stock === 0){
                    $item->in_stock -= $item->pivot->quantity;
                    $item->pivot->lowered_stock = true;
                    $item->pivot->save();
                    $item->save();
                }
            });
        } else {
            $model->variants()->withPivot('lowered_stock', 'quantity')->get()->each(function ($item, $key) use ($canceledFlag) {
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
        $this->manageStocks($model);
    }

    public function createInvoice($id)
    {
        return Response::download(storage_path((new InvoiceRobot('cs', Order::find($id)))->generateInvoice()));
    }
}
