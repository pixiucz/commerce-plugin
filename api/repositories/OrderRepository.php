<?php namespace Pixiu\Commerce\Api\Repositories;

use Illuminate\Http\Request;
use Pixiu\Commerce\Models\DeliveryOption;
use Pixiu\Commerce\Models\Order;
use Pixiu\Commerce\Models\PaymentMethod;
use Pixiu\Commerce\Models\ProductVariant;
use Pixiu\Commerce\Models\Address;
use RainLab\User\Facades\Auth;

class OrderRepository
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var Request
     */
    private $request;


    /**
     * OrderRepository constructor.
     */
    public function __construct(Order $order, Request $request)
    {
        $this->order = $order;
        $this->request = $request;
    }

    public function setAllFromRequest()
    {
        $this->setDeliveryOptions();
        $this->setPaymentMethod();
        $this->setUser();
        $this->setAddresses();
        $this->setOrderItems();
    }

    public function setAddresses($addressDelivery = null)
    {
        if (!$addressDelivery) {
            $addressDelivery = new Address();
            $addressDelivery->fill($this->request->input('delivery_address'));
            $addressDelivery->save();
        }

        $this->order->billing_address()->associate(
            $addressDelivery
        );
        $this->order->delivery_address()->associate(
            $addressDelivery
        );
    }

    public function setDeliveryOptions(DeliveryOption $deliveryOption = null)
    {
        $this->order->delivery_option()->associate(
            $deliveryOption ?? DeliveryOption::findOrFail($this->request->input('deliveryOption'))
        );
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod = null)
    {
        $this->order->payment_method()->associate(
            $paymentMethod ?? PaymentMethod::findOrFail($this->request->input('paymentMethod'))
        );
    }

    public function setOrderItems(array $orderItems = [])
    {
        $this->order->save();

        $cartItems =
            empty($orderItems) ?
                $this->request->input('orderItems') :
                $orderItems;

        $variants = ProductVariant::find(
            array_pluck($cartItems, 'product_id')
        );

        $syncArray = [];

        foreach ($cartItems as $item) {
            $variant = $variants->find($item['product_id']);

            $syncArray[$item['product_id']] = [
                'price' => $variant->price,
                'quantity' => $item['amount'],
            ];

            $variant->changeReservedStock($item['amount']);
        }

        $this->order->variants()->sync($syncArray);
    }

    public function setUser()
    {
        if (!$user = Auth::getUser()) {
            return false;
        }

        $this->order->user()->associate(
            $user
        );
    }

    public function getOrder()
    {
        return $this->order;
    }
}