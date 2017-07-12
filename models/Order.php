<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\{Address, ProductVariant};

/**
 * Order Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\Validation;
    public $rules = [];
    public $customMessages = [];
    public $attributeNames = [];

    public function __construct()
    {
        parent::__construct();

        //TODO: Billing address should be optional
        $this->rules = [
            'user' => 'required',
            'delivery_address_id' => 'required',
            'billing_address_id' => 'required',
            'delivery_option' => 'required',
            'payment_method' => 'required',
            'order_status' => 'required'
        ];
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_orders';


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
        'logs' => 'Pixiu\Commerce\Models\OrderLog',
        'invoices' => 'Pixiu\Commerce\Models\PdfInvoice'
    ];
    public $belongsTo = [
        'payment_method' => 'Pixiu\Commerce\Models\PaymentMethod',
        'delivery_option' => 'Pixiu\Commerce\Models\DeliveryOption',
        'delivery_address' => 'Pixiu\Commerce\Models\Address',
        'billing_address' => 'Pixiu\Commerce\Models\Address',
        'order_status' => 'Pixiu\Commerce\Models\OrderStatus',
        'user' => 'RainLab\User\Models\User'
    ];
    public $belongsToMany = [
        'variants' => [
            'Pixiu\Commerce\Models\ProductVariant',
            'table' => 'pixiu_com_orders_variants',
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
        if ($this->user === null) {
            return [];
        }

        $addressArray = [];
        Address::where('user_id', $this->user->id)->get()->each(function ($address, $key) use (&$addressArray) {
            $addressArray[$address->id] = $address->full_address;
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

        // TODO: Potential resources bottleneck - cache candidate
        // This will be called every time somebody tries to add item to repeater
        ProductVariant::get()->each(function ($item, $key) use (&$variantsArray) {
            $variantsArray = array_add($variantsArray, $item->id, $item->full_name);
        });
        return $variantsArray;
    }

    public function getVariantsRepeaterAttribute()
    {
        $variants = [];
        $this->variants()->withPivot('quantity')->get()->each(function ($item, $key) use(&$variants) {
            array_push($variants, [
                "variant_repeater_id" => (int) $item->id,
                "variant_repeater_quantity" => $item->pivot->quantity
            ]);
        });
        return $variants;
    }

    public function setVariantsRepeaterAttribute($variants)
    {
        // TODO: Deferred binding problem - move to formAfterSave?
        $this->variants()->detach();
        foreach ($variants as $variant) {
            $this->variants()->attach(ProductVariant::find($variant['variant_repeater_id']), ["quantity" => $variant['variant_repeater_quantity']]);
        }
    }

    public function getSumAttribute()
    {
        return 499.99;
    }
}
