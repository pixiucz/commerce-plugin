<?php namespace Pixiu\Commerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Response;
use Pixiu\Commerce\Models\PdfInvoice;

/**
 * Pdf Invoices Back-end Controller
 */
class PdfInvoices extends Controller
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

        BackendMenu::setContext('Pixiu.Commerce', 'commerce', 'pdfinvoices');
    }

    public function download($param)
    {
        return Response::download(storage_path(PdfInvoice::find($param)->path));
    }
}
