<?php namespace Pixiu\Commerce\Classes\Invoice;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Pixiu\Commerce\Models\PdfInvoice;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\TaxHandler;
use Illuminate\Support\Facades\Lang;
use Pixiucz\Invoices\InvoiceGenerator;


class NormalInvoiceManager extends InvoiceManager
{
    protected function prepareSum(): array
    {
        $order['sum'] = $this->sum;
        $order['sum_without_tax'] = $this->sumWithoutTax;
        $order['sum_tax_only'] = $this->sumTaxOnly;
        return $order;
    }

    public function generateInvoice()
    {
        $order = $this->generateData();
        $order['variants'] = $this->prepareVariants();
        $order['delivery_option'] = $this->prepareDeliveryOption();
        $order['sum'] = $this->prepareSum();
        $fileName = $this->generateFilename();
        $filePath = 'media/invoices/' . $fileName;
        $invoiceGenerator = app('InvoiceGenerator');

        $existingInvoice = $this->model->invoices()->first();

        if ($existingInvoice) {
            $invoice = $invoiceGenerator->generateInvoice($this->invoiceLine, $order, null, $existingInvoice->invoice_number);
        } else {
            $invoice = $invoiceGenerator->generateInvoice($this->invoiceLine, $order);
        }

        $this->saveInvoice($filePath, $invoice, $order['id']);
    }

    protected function saveInvoice($filePath, $invoice, $orderId): void
    {
        Storage::put($filePath, $invoice['pdf']);

        if (!$pdfInvoice = PdfInvoice::where('order_id', $orderId)
            ->where('type', Lang::get('pixiu.commerce::lang.other.normal_invoice'))
            ->first()) {
            $pdfInvoice = new PdfInvoice();
        }

        $pdfInvoice->path = 'app/' . $filePath;
        $pdfInvoice->order_id = $orderId;
        $pdfInvoice->type = Lang::get('pixiu.commerce::lang.other.normal_invoice');
        $pdfInvoice->invoice_number = $invoice['invoice_number'];

        $pdfInvoice->save();
    }
}