<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * PdfInvoice Model
 */
class PdfInvoice extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_pdf_invoices';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'order' => 'Pixiu\Commerce\Models\Order'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
