<?php namespace Pixiu\Commerce\Models;

use Faker\Provider\Payment;
use Model;
use Pixiu\Commerce\Models\{Address, ProductVariant, CommerceSettings, OrderLog};
use Pixiu\Commerce\Classes\{TaxHandler, OrderStatus, PaymentStatus, OrderStatusFSM};
use Illuminate\Support\Facades\Lang;
use Pixiu\Commerce\Classes\CurrencyHandler;
use Pixiu\Commerce\Traits\LocalizedValidation;

/**
 * Order Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use LocalizedValidation;

    public $rules = [];

    private $currencyHandler;
    private $taxHandler;

    public function __construct()
    {
        parent::__construct();

        $this->currencyHandler = \App::make('CurrencyHandler');
        $this->taxHandler = \App::make('TaxHandler');

        $this->localizeValidation();

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

    public function filterFields($fields, $context = null)
    {
        if ($context === "update"){
            //
        }
    }

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

        if ($this->payment_status === PaymentStatus::CASH_ON_DELIVERY) {
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
        return $sum;
    }

    public function getSumWithoutTaxAttribute()
    {
        return $this->taxHandler->getWithoutTax($this->sum);
    }

    public function getSumTaxOnlyAttribute()
    {
        return $this->taxHandler->getTax($this->sum);
    }

    public function removeReservedStock()
    {
        $this->variants->each(function($item, $key) {
            $item->removeReservedStock();
            $item->save();
        });
    }

    public function variantsLeftWarehouse()
    {
        $this->variants()->withPivot('quantity')->get()->each(function ($item, $key) {
            $item->changeStock(-$item->pivot->quantity);
            $item->changeReservedStock(-$item->pivot->quantity);
            $item->save();
        });
    }

    public function returnVariantsToStock()
    {
        $this->variants()->withPivot('quantity')->get()->each(function($item, $key) {
            $item->removeReservedStock();
        });
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
        $fsm = new OrderStatusFSM($this);
        $fsm->changeStateToNew();

        $this->logs()->create([
            'title' => Lang::get('pixiu.commerce::lang.orderlog.created'),
            'style' => 'text-info'
        ]);
    }
}
