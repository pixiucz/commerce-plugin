<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Pixiu\Commerce\Classes\InvoiceRobot;

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
        (new InvoiceRobot('cs', $model))->generateInvoice();
    }
}
