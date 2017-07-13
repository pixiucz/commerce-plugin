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

class InvoiceRobot {
    private $data;
    private $fileName;
    private $twig;
    private $language;

    public function __construct(string $language, $model)
    {
        $this->language = $language;
        $this->data = $this->generateData($model);
        $this->fileName = $this->generateFilename($model);
        $this->twig = new Twig();
    }

    private function generateFilename($model) : string {
        return date("Ydmhmi") . '_' . $model->id . '.pdf';
    }

    private function generateData($model) : array {
        $order = $model
            ->with('billing_address')
            ->with('user')
            ->with('delivery_address')
            ->with('delivery_option')
            ->with('payment_method')
            ->with('order_status')
            ->find($model->id)->toArray();
        $order['variants'] = [];
        $model->variants()
            ->withPivot('quantity')
            ->with('attributes')
            ->with('product.brand')
            ->get()
            ->each(function ($item, $key) use (&$order) {
                $attributes = '';
                foreach ($item->toArray()['attributes'] as $attribute){
                    $attributes .= $attribute['value'] . ';';
                }
                array_push($order['variants'], [
                    'name' => $item->product->brand->name . ' ' . $item->product->name . ' (' . $attributes . ')',
                    'ean' => $item->ean,
                    'tax_rate' => CommerceSettings::get('tax'),
                    'price' => $item->resolved_price,
                    'price_without_tax' => $item->resolved_price_without_tax,
                    'sum_without_tax' => $item->resolved_price_without_tax * $item->pivot->quantity,
                    'tax' => $item->tax_only * $item->pivot->quantity,
                    'quantity' => $item->pivot->quantity,
                    'sum' => $item->resolved_price * $item->pivot->quantity
                ]);
            });

        $order['tax'] = CommerceSettings::get('tax');
        $order['currency'] = CommerceSettings::get('currency');
        $order['sum'] = $model->sum;
        $order['sum_without_tax'] = $model->sum_without_tax;
        $order['sum_tax_only'] = $model->sum_tax_only;
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

        Storage::put($filePath, $invoicePDF);

        $pdfInvoice = new PdfInvoice();
        $pdfInvoice->path = 'app/'.$filePath;
        $pdfInvoice->order_id = $this->data['id'];
        $pdfInvoice->save();

        return 'app/'.$filePath;
    }

}