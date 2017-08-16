<?php namespace Pixiu\Commerce\Classes\Invoice;

use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Pixiu\Commerce\Models\PdfInvoice;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\TaxHandler;
use Illuminate\Support\Facades\Lang;
use Pixiucz\Invoices\InvoiceGenerator;


class RefundInvoiceManager extends InvoiceManager
{
    private $refundedVariants;
    private $quantities;

    protected function prepareVariants(): array
    {
        $order = [];
        $this->refundedVariants->each(function ($item, $key) use (&$order) {
                $attributes = '';
                foreach ($item->toArray()['attributes'] as $attribute) {
                    $attributes .= $attribute['value'] . ';';
                }

                $sums = $this->handleAndGetSums($item, $this->quantities[$key]);

                array_push($order, [
                    'name' => $item->product->brand !== null ?
                        $item->product->brand->name . ' ' . $item->product->name . ' (' . $attributes . ')' :
                        $item->product->name,
                    'ean' => $item->ean,
                    'tax_rate' => $item->product->tax->rate,
                    'price' => $this->currencyHandler->getValueForInput($item->pivot->price),
                    'price_without_tax' => $this->currencyHandler->getValueForInput($this->taxHandler->getWithoutTax($item->pivot->price)),
                    'sum_without_tax' => $sums['without_tax'],
                    'tax' => $sums['tax_only'],
                    'quantity' => $this->quantities[$key],
                    'sum' => $sums['sum']
                ]);
            });
        return $order;
    }

    public function generateInvoice($variants)
    {
        $ids = [];
        $this->quantities = [];
        
        foreach ($variants as $variant) {
            array_push($ids, $variant['variant']->id);
            array_push($this->quantities, $variant['refunded_quantity']);
        }

        $this->getAllVariants($ids);
        $order = $this->generateData();
        $order['variants'] = $this->prepareVariants();
        $order['sum'] = $this->prepareSum();
        $order['status'] = 'refunded';

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

        $pdfInvoice = new PdfInvoice();
        $pdfInvoice->path = 'app/' . $filePath;
        $pdfInvoice->order_id = $orderId;
        $pdfInvoice->type = Lang::get('pixiu.commerce::lang.other.refund_invoice');
        $pdfInvoice->invoice_number = $invoice['invoice_number'];

        $pdfInvoice->save();
    }

    /**
     * @param $ids
     */
    private function getAllVariants($ids): void
    {
        $this->refundedVariants = $this->model->variants()
            ->withPivot('quantity', 'price')
            ->with('attributes')
            ->with('product.brand')
            ->with('product.tax')
            ->find($ids);
    }
}