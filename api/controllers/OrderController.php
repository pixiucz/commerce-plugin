<?php namespace Pixiu\Commerce\api\Controllers;

use Illuminate\Http\Request;
use Pixiu\Commerce\Api\Repositories\OrderRepository;
use Pixiu\Commerce\Models\Address;
use Pixiu\Commerce\Models\DeliveryOption;
use Pixiu\Commerce\Models\PaymentMethod;
use Pixiu\Commerce\Models\ProductVariant;
use RainLab\User\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Pixiu\Commerce\Models\Order;

class OrderController
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(Request $request, OrderRepository $orderRepository)
    {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
    }

    public function store()
    {
        if ($errorsResponse = $this->validate()) {
            return $errorsResponse;
        }

        $this->orderRepository->setAllFromRequest();

        return response(['msg' => 'Checkout logic -_^', 'order' => $this->orderRepository->get()], 201);
    }

    private function validate()
    {
        // TODO: Validate request
        return null;
    }

}