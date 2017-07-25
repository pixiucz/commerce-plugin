<?php namespace Pixiu\Commerce\Classes;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use October\Rain\Support\Facades\File;
use October\Rain\Parse\Twig;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\PDF;
use Pixiu\Commerce\Models\PdfInvoice;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\TaxHandler;
use Illuminate\Support\Facades\Lang;

class InvoiceBuilder {
    private $data;
    private $fileName;
    private $twig;
    private $language;
    private $taxHandler;
    private $currencyHandler;
    private $model;

    public function __construct(string $language, $model)
    {
        $this->model = $model;
        $this->language = $language;

        // FIXME: Posunout do generate invoice a prejmenovat, pro generovani storno invoice pouzit jinou metodu!!!
        $this->data = $this->generateData($model);
        // FIXME:

        $this->fileName = $this->generateFilename($model);
        $this->twig = new Twig();
        $this->taxHandler = \App::make('TaxHandler');
        $this->currencyHandler = \App::make('CurrencyHandler');
    }

    private function generateFilename($model) : string {
        return date("Ydmhmi") . '_' . $model->id . '.pdf';
    }

    private function generateData($model) : array {
        $taxHandler = \App::make('TaxHandler');
        $currencyHandler = \App::make('CurrencyHandler');

        $delivery_option = $model->delivery_option;
        $order = $model
            ->with('billing_address')
            ->with('user')
            ->with('delivery_address')
            ->with('payment_method')
            ->find($model->id)->toArray();
        $order['variants'] = [];
        $model->variants()
            ->withPivot('quantity', 'price')
            ->with('attributes')
            ->with('product.brand')
            ->get()
            ->each(function ($item, $key) use ($currencyHandler, &$taxHandler, &$order) {
                $attributes = '';
                foreach ($item->toArray()['attributes'] as $attribute){
                    $attributes .= $attribute['value'] . ';';
                }
                array_push($order['variants'], [
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

    public function generateInvoice() {
        $invoiceTemplatePath = '/pixiu/commerce/views/invoice_' . $this->language . '.html';
        $html = $this->twig->parse(File::get(plugins_path().$invoiceTemplatePath), $this->data);
        $pdf = App::make('dompdf.wrapper');
        $invoicePDF = @$pdf->setOptions(['isFontSubsettingEnabled' => true, 'isRemoteEnabled' => true])->loadHTML($html)->output();
        $filePath = 'invoices/' . $this->fileName;

        $this->saveInvoice($filePath, $invoicePDF, Lang::get('pixiu.commerce::lang.other.normal_invoice'));

        return 'app/'.$filePath;
    }

    public function generateStornoInvoice($stornoVariants) {
        /*
         *  majstrštyk
         */
        $this->data['variants'] = [];
        $this->data['delivery_option'] = [];
        $this->data['sum'] = $this->currencyHandler->getValueForInput($this->model->refunded_sum);
        $this->data['sum_without_tax'] = $this->currencyHandler->getValueForInput($this->model->refunded_sum_without_tax);
        $this->data['sum_tax_only'] = $this->currencyHandler->getValueForInput($this->model->refunded_sum_tax_only);
        $this->data['status'] = "canceled";

        $this->fileName = 'storno_' . $this->fileName;
        $tax = new TaxHandler();

        foreach ($stornoVariants as $stornoVariant) {
            array_push($this->data['variants'], [
                'name' => $stornoVariant->product->brand !== null ?
                    $stornoVariant->product->brand->name . ' ' . $stornoVariant->product->name  :
                    $stornoVariant->product->name,
                'ean' => $stornoVariant->ean,
                'tax_rate' => $tax->rate,
                'price' => $stornoVariant->pivot->price,
                'price_without_tax' => $tax->getWithoutTax($stornoVariant->pivot->price),
                'sum_without_tax' => $tax->getWithoutTax($stornoVariant->pivot->price) * $stornoVariant->pivot->refunded_quantity,
                'tax' => $tax->getTax($stornoVariant->pivot->price) * $stornoVariant->pivot->refunded_quantity,
                'quantity' => $stornoVariant->pivot->refunded_quantity,
                'sum' => $stornoVariant->pivot->price * $stornoVariant->pivot->refunded_quantity
            ]);
        }

        $invoiceTemplatePath = '/pixiu/commerce/views/invoice_' . $this->language . '.html';
        $html = $this->twig->parse(File::get(plugins_path().$invoiceTemplatePath), $this->data);
        $pdf = App::make('dompdf.wrapper');
        $invoicePDF = @$pdf->setOptions(['isFontSubsettingEnabled' => true, 'isRemoteEnabled' => true])->loadHTML($html)->output();
        $filePath = 'invoices/' . $this->fileName;

        $this->saveInvoice($filePath, $invoicePDF, Lang::get('pixiu.commerce::lang.other.cancel_invoice'));

        return 'app/'.$filePath;
    }

    /**
     * @param $filePath
     * @param $invoicePDF
     * @param string $type
     */
    private function saveInvoice($filePath, $invoicePDF, string $type): void
    {
        Storage::put($filePath, $invoicePDF);

        $pdfInvoice = new PdfInvoice();
        $pdfInvoice->path = 'app/' . $filePath;
        $pdfInvoice->order_id = $this->data['id'];
        $pdfInvoice->type = $type;

        /*
         *  Get rid of all non-storno invoices and create a new one
         */
        if ($type === Lang::get('pixiu.commerce::lang.other.normal_invoice')) {
            PdfInvoice::where('order_id', $this->data['id'])->where('type', Lang::get('pixiu.commerce::lang.other.normal_invoice'))->delete();
        }

        $pdfInvoice->save();
    }

}