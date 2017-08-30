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
    protected $sum;
    protected $sumWithoutTax;
    protected $sumTaxOnly;

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

        $currSum = $this->currencyHandler->getValueForInput($delivery_option->price);
        $currSumWithouTax = $this->currencyHandler->getValueForInput($delivery_option->price_without_tax);
        $currTax = $this->currencyHandler->getValueForInput($delivery_option->tax);

        $this->sum += $currSum;
        $this->sumTaxOnly += $currTax;
        $this->sumWithoutTax += $currSumWithouTax;

        $order = [
            'name' => $delivery_option->name,
            'price' => $currSum,
            'price_without_tax' => $currSumWithouTax,
            'tax' => $currTax
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
            ->with('product.tax')
            ->get()
            ->each(function ($item, $key) use (&$order) {
                $attributes = '';
                foreach ($item->toArray()['attributes'] as $attribute) {
                    $attributes .= $attribute['value'] . ';';
                }

                $sums = $this->handleAndGetSums($item);

                array_push($order, [
                    'name' => $item->product->brand !== null ?
                        $item->product->brand->name . ' ' . $item->product->name . ' (' . $attributes . ')' :
                        $item->product->name,
                    'ean' =>
                        $item->ean,
                    'tax_rate' =>
                        $item->product->tax->rate,
                    'price' =>
                        $this->currencyHandler->getValueForInput($item->pivot->price),
                    'price_without_tax' =>
                        $this->currencyHandler->getValueForInput($this->taxHandler->getWithoutTax($item->pivot->price, $item->product->tax->rate)),
                    'sum_without_tax' =>
                        $sums['without_tax'],
                    'tax' =>
                        $sums['tax_only'],
                    'quantity' =>
                        $item->pivot->quantity,
                    'sum' =>
                        $sums['sum']
                ]);
            });
        return $order;
    }

    protected function handleAndGetSums($item, $quantity = null): array
    {
        $currSumWithoutTax =
            $quantity ?
                $this->currencyHandler
                    ->getValueForInput($this->taxHandler->getWithoutTax($item->pivot->price, $item->product->tax->rate) * $quantity)
                :
                $this->currencyHandler
                ->getValueForInput($this->taxHandler->getWithoutTax($item->pivot->price, $item->product->tax->rate) * $item->pivot->quantity);
        $this->sumWithoutTax += $currSumWithoutTax;

        $currSumTaxOnly =
            $quantity ?
                $this->currencyHandler->getValueForInput($this->taxHandler->getTax($item->pivot->price, $item->product->tax->rate) * $quantity)
                :
                $this->currencyHandler->getValueForInput($this->taxHandler->getTax($item->pivot->price, $item->product->tax->rate) * $item->pivot->quantity);
        $this->sumTaxOnly += $currSumTaxOnly;

        $currSum =
            $quantity ?
                $this->currencyHandler->getValueForInput($item->pivot->price * $quantity)
                :
                $this->currencyHandler->getValueForInput($item->pivot->price * $item->pivot->quantity);
        $this->sum += $currSum;

        return [
            'without_tax' => $currSumWithoutTax,
            'tax_only' => $currSumTaxOnly,
            'sum' => $currSum
        ];
    }

    protected function prepareSum(): array
    {
        $order['sum'] = $this->sum;
        $order['sum_without_tax'] = $this->sumWithoutTax;
        $order['sum_tax_only'] = $this->sumTaxOnly;
        return $order;
    }

    protected abstract function saveInvoice($filePath, $invoice, $orderId): void;

}