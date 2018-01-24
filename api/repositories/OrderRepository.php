<?php namespace Pixiu\Commerce\Api\Repositories;

use Illuminate\Http\Request;
use Pixiu\Commerce\Models\DeliveryOption;
use Pixiu\Commerce\Models\Order;
use Pixiu\Commerce\Models\PaymentMethod;
use Pixiu\Commerce\Models\ProductVariant;
use Pixiu\Commerce\Models\Address;
use RainLab\User\Facades\Auth;

/**
 * Class OrderRepository
 * @package Pixiu\Commerce\Api\Repositories
 */
class OrderRepository
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var Request
     */
    private $request;


    /**
     * OrderRepository constructor.
     * @param Order $order
     * @param Request $request
     * @param AddressRepository $addressRepository
     */
    public function __construct(Order $order, Request $request, AddressRepository $addressRepository)
    {
        $this->order = $order;
        $this->request = $request;
        $this->addressRepository = $addressRepository;
    }

    /**
     *
     * @return OrderRepository
     */
    public function setAllFromRequest()
    {
        $this->setDeliveryOptions();
        $this->setPaymentMethod();
        $this->setUser();
        $this->setAddresses();
        $this->setOrderItems();

        return $this;
    }

    /**
     * @param Address|null $addressDelivery
     * @param Address|null $addressBilling
     * @return OrderRepository
     */
    public function setAddresses(Address $addressDelivery = null, Address $addressBilling = null)
    {
        $this->order->delivery_address()->associate(
            $addressDelivery ?? $this->addressRepository->new()->fillDeliveryFromRequest()->get()
        );

        $this->order->billing_address()->associate(
            $addressDelivery ?? $this->addressRepository->new()->fillBillingFromRequest()->get()
        );

        return $this;
    }

    /**
     * @param DeliveryOption|null $deliveryOption
     * @return OrderRepository
     */
    public function setDeliveryOptions(DeliveryOption $deliveryOption = null)
    {
        $this->order->delivery_option()->associate(
            $deliveryOption ?? DeliveryOption::findOrFail($this->request->input('deliveryOption'))
        );

        return $this;
    }

    /**
     * @param PaymentMethod|null $paymentMethod
     * @return OrderRepository
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod = null)
    {
        $this->order->payment_method()->associate(
            $paymentMethod ?? PaymentMethod::findOrFail($this->request->input('paymentMethod'))
        );

        return $this;
    }

    /**
     * @param array $orderItems
     * @return OrderRepository
     */
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

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setUser()
    {
        if (!$user = Auth::getUser()) {
            throw new \Exception('User not authorized');
        }

        $this->order->user()->associate(
            $user
        );

        return $this;
    }

    /**
     * @return Order
     */
    public function get()
    {
        return $this->order;
    }
}