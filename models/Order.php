<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\{Address, ProductVariant};

/**
 * Order Model
 */
class Order extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_commerce_orders';

    protected $jsonable = ['variants_repeater'];

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
        'logs' => 'Pixiu\Commerce\Models\OrderLog'
    ];
    public $belongsTo = [
        'payment_method' => 'Pixiu\Commerce\Models\PaymentMethod',
        'delivery_option' => 'Pixiu\Commerce\Models\DeliveryOption',
        'delivery_address' => 'Pixiu\Commerce\Models\Address',
        'billing_address' => 'Pixiu\Commerce\Models\Address',
        'order_status' => 'Pixiu\Commerce\Models\OrderStatus'
    ];
    public $belongsToMany = [
        'variants' => [
            'Pixiu\Commerce\Models\ProductVariant',
            'table' => 'pixiu_commerce_orders_variants',
            'key' => 'order_id',
            'otherKey' => 'variant_id'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getDeliveryAddressIdOptions()
    {
        $addressArray = [];
        Address::get()->each(function ($address, $key) use (&$addressArray) {
            array_push($addressArray, $address->full_address);
        });
        return $addressArray;
    }

    public function getBillingAddressIdOptions()
    {
        return $this->getDeliveryAddressIdOptions();
    }

    public function getVariantRepeaterIdOptions()
    {
        $variantsArray = [];
        ProductVariant::get()->each(function ($item, $key) use (&$variantsArray) {
            $variantsArray = array_add($variantsArray, $item->id, $item->full_name);
        });
        return $variantsArray;
    }

    public function getVariantsRepeaterAttribute()
    {
        $temp = [];
        $this->variants()->withPivot('quantity')->get()->each(function ($item, $key) use(&$temp) {
            array_push($temp, [
                "variant_repeater_id" => (int) $item->id,
                "variant_repeater_quantity" => $item->pivot->quantity
            ]);
        });
        return $temp;
    }

    public function setVariantsRepeaterAttribute($variants)
    {
        $this->variants()->detach();
        foreach ($variants as $variant) {
            $this->variants()->attach(ProductVariant::find($variant['variant_repeater_id']), ["quantity" => $variant['variant_repeater_quantity']]);
        }
    }
}
