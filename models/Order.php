<?php namespace Pixiu\Commerce\Models;

use Faker\Provider\Payment;
use Model;
use Pixiu\Commerce\Models\{Address, ProductVariant, CommerceSettings, OrderLog};
use Pixiu\Commerce\Classes\{Tax, OrderStatus, PaymentStatus};
use Illuminate\Support\Facades\Lang;

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
            'payment_method' => 'required'
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
        'notes' => 'Pixiu\Commerce\Models\OrderNote',
        'invoices' => 'Pixiu\Commerce\Models\PdfInvoice',
        'logs' => 'Pixiu\Commerce\Models\OrderLog'
    ];
    public $belongsTo = [
        'payment_method' => 'Pixiu\Commerce\Models\PaymentMethod',
        'delivery_option' => 'Pixiu\Commerce\Models\DeliveryOption',
        'delivery_address' => 'Pixiu\Commerce\Models\Address',
        'billing_address' => 'Pixiu\Commerce\Models\Address',
        'user' => 'RainLab\User\Models\User'
    ];
    public $belongsToMany = [
        'variants' => [
            'Pixiu\Commerce\Models\ProductVariant',
            'table' => 'pixiu_com_orders_variants',
            'key' => 'order_id',
            'otherKey' => 'variant_id',
            'pivot' => ['price','quantity','refunded_quantity']
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getStatusOptions()
    {
        return [
            OrderStatus::NEW => OrderStatus::getNew(),
            OrderStatus::SHIPPED => OrderStatus::getShipped(),
            OrderStatus::READY_FOR_COLLECTION => OrderStatus::getReadyForCollection(),
            OrderStatus::CANCELED => OrderStatus::getCanceled(),
            OrderStatus::FINISHED => OrderStatus::getFinished()
        ];
    }

    public function getPaymentStatusOptions()
    {
        $options = PaymentStatus::getAll();
        if ($this->id === null) {
            return $options;
        }

        if (strtolower($this->payment_method->name) == "cash on delivery") {
            return PaymentStatus::getCashOnDelivery();
        }

        unset($options[PaymentStatus::CASH_ON_DELIVERY]);
        return $options;
    }

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

    public function getSumAttribute()
    {
        $sum = 0;
        $this->variants()->withPivot('quantity', 'price')->get()->each(function ($item, $key) use (&$sum) {
            $sum += $item->pivot->price * $item->pivot->quantity;
        });
        return floor($sum + $this->delivery_option->price);
    }

    public function getSumWithoutTaxAttribute()
    {
        return round((new Tax())->getWithoutTax($this->sum), 2);
    }

    public function getSumTaxOnlyAttribute()
    {
        return round((new Tax())->getTax($this->sum), 2);
    }

    public function getRefundedSumAttribute()
    {
        $sum = 0;
        $this->variants()->withPivot('refunded_quantity', 'price')->get()->each(function ($item, $key) use (&$sum) {
            $sum += $item->pivot->price * $item->pivot->refunded_quantity;
        });
        return floor($sum);
    }

    public function getRefundedSumWithoutTaxAttribute()
    {
        return round((new Tax())->getWithoutTax($this->refunded_sum), 2);
    }

    public function getRefundedSumTaxOnlyAttribute()
    {
        return round((new Tax())->getTax($this->refunded_sum), 2);
    }

    public function beforeCreate()
    {
        if ($this->payment_method->name === "Cash on delivery") {
            $this->payment_status = "cash on delivery";
        } else {
            $this->payment_status = "awaiting payment";
        }
    }

    public function afterCreate()
    {
        $this->logs()->create([
            'title' => Lang::get('pixiu.commerce::lang.orderlog.created'),
            'style' => 'text-info'
        ]);
    }
}
