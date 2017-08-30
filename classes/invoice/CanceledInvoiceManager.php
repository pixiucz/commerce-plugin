<?php namespace Pixiu\Commerce\Classes\Invoice;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Pixiu\Commerce\Models\PdfInvoice;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\TaxHandler;
use Illuminate\Support\Facades\Lang;
use Pixiucz\Invoices\InvoiceGenerator;


class CanceledInvoiceManager extends InvoiceManager
{
    public function generateInvoice()
    {
        $order = $this->generateData();
        $order['variants'] = $this->prepareVariants();
        $order['sum'] = $this->prepareSum();
        $order['status'] = 'canceled';
        $fileName = $this->generateFilename();
        $filePath = 'media/invoices/' . $fileName;
        $invoiceGenerator = app('InvoiceGenerator');

        $existingInvoice = $this->model->invoices()->first();
        $invoice = $invoiceGenerator->generateInvoice($this->invoiceLine, $order, null, $existingInvoice->invoice_number);
        $this->saveInvoice($filePath, $invoice, $order['id']);
    }

    protected function saveInvoice($filePath, $invoice, $orderId): void
    {
        Storage::put($filePath, $invoice['pdf']);

        if (!$pdfInvoice = PdfInvoice::where('order_id', $orderId)
            ->where('type', Lang::get('pixiu.commerce::lang.other.cancel_invoice'))
            ->first()){
            $pdfInvoice = new PdfInvoice();
        }
        $pdfInvoice->path = 'app/' . $filePath;
        $pdfInvoice->order_id = $orderId;
        $pdfInvoice->type = Lang::get('pixiu.commerce::lang.other.cancel_invoice');
        $pdfInvoice->invoice_number = $invoice['invoice_number'];

        $pdfInvoice->save();
    }
}