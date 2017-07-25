<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\CommerceSettings;
use Pixiu\Commerce\Classes\TaxHandler;

/**
 * ProductCombination Model
 */
class ProductVariant extends Model
{
    private $taxHandler;
    public function __construct()
    {
        parent::__construct();

        $this->taxHandler = \App::make('TaxHandler');
    }

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

    protected $jsonable = ['specifications'];

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
            'otherKey' => 'order_id',
            'pivot' => ['price','quantity','refunded_quantity']
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

        $product->brand !== null ? $productName = $product->brand->name . ' ' . $product->name : $productName = $product->name;


        $this->attributes()->get()->each(function($item, $key) use (&$productName){
            $productName .= ' - ' . $item->value;
        });
        return $productName;
    }

    public function getResolvedPriceAttribute()
    {
        return $this->price;
    }

    public function getResolvedPriceWithoutTaxAttribute()
    {
        return $this->taxHandler->getWithoutTax($this->resolved_price);
    }

    public function getTaxOnlyAttribute()
    {
        return $this->taxHandler->getTax($this->resolved_price);
    }


    public function setChangeStockAttribute()
    {
        $change = post('ProductVariant.change_stock');

        if ($change <> 0 && $change !== "") {
            $this->increment('in_stock', $change);
        }
    }
}