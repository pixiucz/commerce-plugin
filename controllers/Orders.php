<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Pixiu\Commerce\Classes\InvoiceRobot;
use Pixiu\Commerce\Models\Order;
use Illuminate\Support\Facades\Response;

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

    public function formBeforeSave($model)
    {
        // TODO: Stocks
    }

    public function formAfterSave($model)
    {
        //
    }

    public function createInvoice($id)
    {
        return Response::download(storage_path((new InvoiceRobot('cs', Order::find($id)))->generateInvoice()));
    }
}
