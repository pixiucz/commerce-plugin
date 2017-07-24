<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Order Logs Back-end Controller
 */
class OrderNotes extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Pixiu.Commerce', 'commerce', 'ordernotes');
    }
}
