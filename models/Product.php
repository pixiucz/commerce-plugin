<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\{Tax, ProductVariant, Attribute, AttributeGroup};

/**
 * Product Model
 */
class Product extends Model
{

    public function getTaxIdOptions()
    {
        return Tax::all()->pluck('name', 'id')->toArray();
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_commerce_products';

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
    public $hasMany = [
        'productvariants' => ['Pixiu\Commerce\Models\ProductVariant']
    ];
    public $belongsTo = [
        'brand' => ['Pixiu\Commerce\Models\Brand'],
        'tax' => ['Pixiu\Commerce\Models\Tax']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => 'System\Models\File'
    ];

}