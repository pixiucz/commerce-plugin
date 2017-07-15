<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\Tax;

/**
 * ProductCombination Model
 */
class ProductVariant extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_product_variants';

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
        'product' => ['Pixiu\Commerce\Models\Product'],
        'primary_picture' => ['System\Models\File']
    ];
    public $belongsToMany = [
        'attributes' => [
            'Pixiu\Commerce\Models\Attribute',
            'table' => 'pixiu_com_variant_attributes',
            'key' => 'variant_id',
            'otherKey' => 'attribute_id',
            'pivot' => ['group_id']
        ],
        'images' => [
            'System\Models\File',
            'table' => 'pixiu_com_variant_images',
            'key' => 'variant_id',
            'otherKey' => 'system_file_id'
        ],
        'orders' => [
            'Pixiu\Commerce\Models\Orders',
            'table' => 'pixiu_com_orders_variants',
            'key' => 'variant_id',
            'otherKey' => 'order_id'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getFullNameAttribute()
    {
        $product = $this->product()->with('brand')->first();
        $productName = $product->brand->name . ' ' . $product->name;

        $this->attributes()->get()->each(function($item, $key) use (&$productName){
            $productName .= ' - ' . $item->value;
        });
        return $productName;
    }

    public function getResolvedPriceAttribute()
    {
        if ($this->price === null){
            return round($this->product->retail_price, 2);
        }
        return round($this->price, 2);
    }

    public function getResolvedPriceWithoutTaxAttribute()
    {
        return round((new Tax())->getWithoutTax($this->resolved_price), 2);
    }

    public function getTaxOnlyAttribute()
    {
        return round((new Tax())->getTax($this->resolved_price), 2);
    }

}