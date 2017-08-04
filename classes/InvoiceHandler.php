<?php namespace Pixiu\Commerce\Classes;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use October\Rain\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Pixiu\Commerce\Models\PdfInvoice;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\TaxHandler;
use Illuminate\Support\Facades\Lang;
use Pixiucz\Invoices\InvoiceGenerator;

class InvoiceHandler {
    private $language;
    private $taxHandler;
    private $currencyHandler;
    private $invoiceLine;
    private $model;

    public function __construct(string $language, $model)
    {
        $this->invoiceLine = "commerce";

        $this->model = $model;
        $this->language = $language;

        $this->taxHandler = \App::make('TaxHandler');
        $this->currencyHandler = \App::make('CurrencyHandler');
    }

    private function generateFilename() : string {
        return date("Ydmhmi") . '_' . $this->model->id . '.pdf';
    }

    private function generateData() : array {
        $taxHandler = $this->taxHandler;
        $currencyHandler = $this->currencyHandler;
        $model = $this->model;

        $delivery_option = $model->delivery_option;
        $order = $model
            ->with('billing_address')
            ->with('user')
            ->with('delivery_address')
            ->with('payment_method')
            ->find($model->id)->toArray();

        $order['tax'] = CommerceSettings::get('tax');
        $order['currency'] = CommerceSettings::get('currency');
        $order['delivery_option'] = [
            'name' => $delivery_option->name,
            'price' => $currencyHandler->getValueForInput($delivery_option->price),
            'price_without_tax' => $currencyHandler->getValueForInput($delivery_option->price_without_tax),
            'tax' => $currencyHandler->getValueForInput($delivery_option->tax)
        ];
        $order['sum'] = $currencyHandler->getValueForInput($model->sum);
        $order['sum_without_tax'] = $currencyHandler->getValueForInput($model->sum_without_tax);
        $order['sum_tax_only'] = $currencyHandler->getValueForInput($model->sum_tax_only);
        $order['updated_at'] = date('d. m. Y', strtotime(strtok($order['updated_at'], ' ')));

        $order['company'] = [
            'name' => CommerceSettings::get('company_name'),
            'address' => CommerceSettings::get('address'),
            'zip' => CommerceSettings::get('zip'),
            'ico' => CommerceSettings::get('ico'),
            'dic' => CommerceSettings::get('dic'),
            'ic_dph' => CommerceSettings::get('ic_dph'),
            'bank' => CommerceSettings::get('bank'),
            'account_number' => CommerceSettings::get('account_number'),
            'swift' => CommerceSettings::get('swift'),
            'iban' => CommerceSettings::get('iban')
        ];
        return $order;
    }

    public function generateInvoice(string $type) {
        $data = $this->generateData();
        $data['variants'] = $this->prepareVariants();
        $fileName = $this->generateFilename();
        $filePath = 'media/invoices/' . $fileName;
        $invoiceGenerator = app('InvoiceGenerator');

        if (!$existingInvoice = $this->model->invoices()->where('type', Lang::get('pixiu.commerce::lang.other.canceled_invoice'))->first()) {
            $existingInvoice = $this->model->invoices()->where('type', Lang::get('pixiu.commerce::lang.other.normal_invoice'))->first();
        }

        if ($existingInvoice) {
            $invoice = $invoiceGenerator->generateInvoice($this->invoiceLine, $data, null, $existingInvoice->invoice_number);
        } else {
            $invoice = $invoiceGenerator->generateInvoice($this->invoiceLine, $data);
        }

        $this->saveInvoice($filePath, $invoice, $data['id'], $type);
    }

    public function generateRefundInvoice($refunded)
    {
        $data = $this->generateData();
        $data['variants'] = $this->prepareVariants();
        $fileName = $this->generateFilename();
        $filePath = 'media/invoices/refunds/' . $fileName;
    }

    private function prepareRefundVariants($refunded)
    {

    }

    private function saveInvoice($filePath, $invoice, $orderId, string $type): void
    {
        Storage::put($filePath, $invoice['pdf']);

        $pdfInvoice = PdfInvoice::where('order_id', $orderId)->where('type', $type)->first();
        if (!$pdfInvoice) { $pdfInvoice = new PdfInvoice(); }

        $pdfInvoice->path = 'app/' . $filePath;
        $pdfInvoice->order_id = $orderId;
        $pdfInvoice->type = $type;
        $pdfInvoice->invoice_number = $invoice['invoice_number'];

        $pdfInvoice->save();
    }

    private function prepareVariants()
    {
        $model = $this->model;
        $currencyHandler = $this->currencyHandler;
        $taxHandler = $this->taxHandler;

        $order = [];
        $model->variants()
            ->withPivot('quantity', 'price')
            ->with('attributes')
            ->with('product.brand')
            ->get()
            ->each(function ($item, $key) use ($currencyHandler, &$taxHandler, &$order) {
                $attributes = '';
                foreach ($item->toArray()['attributes'] as $attribute) {
                    $attributes .= $attribute['value'] . ';';
                }
                array_push($order, [
                    'name' => $item->product->brand !== null ?
                        $item->product->brand->name . ' ' . $item->product->name . ' (' . $attributes . ')' :
                        $item->product->name,
                    'ean' => $item->ean,
                    'tax_rate' => $taxHandler->rate,
                    'price' => $currencyHandler->getValueForInput($item->pivot->price),
                    'price_without_tax' => $currencyHandler->getValueForInput($taxHandler->getWithoutTax($item->pivot->price)),
                    'sum_without_tax' => $currencyHandler->getValueForInput($taxHandler->getWithoutTax($item->pivot->price) * $item->pivot->quantity),
                    'tax' => $currencyHandler->getValueForInput($taxHandler->getTax($item->pivot->price) * $item->pivot->quantity),
                    'quantity' => $item->pivot->quantity,
                    'sum' => $currencyHandler->getValueForInput($item->pivot->price * $item->pivot->quantity)
                ]);
            });
        return $order;
    }

}