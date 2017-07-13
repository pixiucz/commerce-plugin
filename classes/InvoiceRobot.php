<?php namespace Pixiu\Commerce\Classes;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use October\Rain\Support\Facades\File;
use October\Rain\Parse\Twig;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\PDF;
use Pixiu\Commerce\Models\PdfInvoice;

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
        return date("Y-d-m-H-i-s", strtotime($model->updated_at)) . '_' . $model->id . '.pdf';
    }

    private function generateData($model) : array {
        $order = $model
            ->with('billing_address')
            ->with('user')
            ->with('delivery_address')
            ->with('payment_method')
            ->with('order_status')
            ->find($model->id)->toArray();
        $order['variants'] = $model->variants()
            ->withPivot('quantity')
            ->with('product.brand')
            ->get()->toArray();
        return $order;
    }

    public function generateInvoice() {
        // TODO: Parse data into template
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
    }

}