<?php
/**
 * Created by PhpStorm.
 * User: martinchvila
 * Date: 06/08/2017
 * Time: 17:53
 */

namespace Pixiu\Commerce\Classes\Invoice;

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

abstract class InvoiceManager
{
    protected $taxHandler;
    protected $currencyHandler;
    protected $invoiceLine;
    protected $model;

    public function __construct($model)
    {
        $this->invoiceLine = "commerce";
        $this->model = $model;

        $this->taxHandler = \App::make('TaxHandler');
        $this->currencyHandler = \App::make('CurrencyHandler');
    }

    protected function generateFilename() : string {
        return date("Ydmhmi") . '_' . $this->model->id . '.pdf';
    }

    protected function generateData() : array {
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

        $order['updated_at'] = date('d. m. Y', strtotime(strtok($order['updated_at'], ' ')));
        return $order;
    }

    protected function prepareDeliveryOption() : array
    {
        $delivery_option = $this->model->delivery_option;
        $order = [
            'name' => $delivery_option->name,
            'price' => $this->currencyHandler->getValueForInput($delivery_option->price),
            'price_without_tax' => $this->currencyHandler->getValueForInput($delivery_option->price_without_tax),
            'tax' => $this->currencyHandler->getValueForInput($delivery_option->tax)
        ];
        return $order;
    }

    protected function prepareVariants() : array
    {
        $order = [];
        $this->model->variants()
            ->withPivot('quantity', 'price')
            ->with('attributes')
            ->with('product.brand')
            ->get()
            ->each(function ($item, $key) use (&$order) {
                $attributes = '';
                foreach ($item->toArray()['attributes'] as $attribute) {
                    $attributes .= $attribute['value'] . ';';
                }
                array_push($order, [
                    'name' => $item->product->brand !== null ?
                        $item->product->brand->name . ' ' . $item->product->name . ' (' . $attributes . ')' :
                        $item->product->name,
                    'ean' => $item->ean,
                    'tax_rate' => $this->taxHandler->rate,
                    'price' => $this->currencyHandler->getValueForInput($item->pivot->price),
                    'price_without_tax' => $this->currencyHandler->getValueForInput($this->taxHandler->getWithoutTax($item->pivot->price)),
                    'sum_without_tax' => $this->currencyHandler->getValueForInput($this->taxHandler->getWithoutTax($item->pivot->price) * $item->pivot->quantity),
                    'tax' => $this->currencyHandler->getValueForInput($this->taxHandler->getTax($item->pivot->price) * $item->pivot->quantity),
                    'quantity' => $item->pivot->quantity,
                    'sum' => $this->currencyHandler->getValueForInput($item->pivot->price * $item->pivot->quantity)
                ]);
            });
        return $order;
    }

    protected abstract function saveInvoice($filePath, $invoice, $orderId): void;

}