<?php namespace Pixiu\Commerce\api\Controllers;

use Illuminate\Http\Request;
use Pixiu\Commerce\Models\Address;
use Pixiu\Commerce\Models\DeliveryOption;
use Pixiu\Commerce\Models\PaymentMethod;
use Pixiu\Commerce\Models\ProductVariant;
use RainLab\User\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Pixiu\Commerce\Models\Order;

class OrderController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function store()
    {
//        trace_log($this->request->input('address'));
//        return;
        // Validate request
        if ($errorsResponse = $this->validate()) {
            return $errorsResponse;
        }

        // Store order
        if (!$user = Auth::getUser()) {
            return response(['msg' => 'Prozatim je potreba byt prihlasen'], 403);
        }

        // Create order
        $order = new Order();

        // Attach addresses
        $addressDelivery = new Address();
        $addressDelivery->fill($this->request->input('delivery_address'));
        $addressDelivery->save();

        $order->billing_address()->associate(
            $addressDelivery
        );
        $order->delivery_address()->associate(
            $addressDelivery
        );

        $order->delivery_option()->associate(
            DeliveryOption::findOrFail($this->request->input('deliveryOption'))
        );

        $order->payment_method()->associate(
            PaymentMethod::findOrFail($this->request->input('paymentMethod'))
        );

        // Attach user
        $order->user()->associate(
            $user
        );

        $order->save();

        $cartItems = $this->request->input('cartItems');

        $variants = ProductVariant::find(
            array_pluck($cartItems, 'product_id')
        );

        $syncArray = [];

        foreach ($cartItems as $item) {
            $syncArray[$item['product_id']] = [
                'price' => $variants->find($item['product_id'])->price,
                'quantity' => $item['amount'],
            ];
        }


        $order->variants()->sync($syncArray);

        return response(['msg' => 'Checkout logic -_^', 'order' => $order], 201);
    }

    private function validate()
    {
        return null;
    }

}