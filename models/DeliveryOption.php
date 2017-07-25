<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Classes\TaxHandler;

/**
 * DeliveryOption Model
 */
class DeliveryOption extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_delivery_options';

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
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getPriceWithoutTaxAttribute()
    {
        return (new TaxHandler())->getWithoutTax($this->price);
    }

    public function getTaxAttribute()
    {
        return (new TaxHandler())->getTax($this->price);
    }
}
