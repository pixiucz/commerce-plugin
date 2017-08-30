<?php namespace Pixiu\Commerce\api\Controllers;

use Illuminate\Http\Request;
use Pixiu\Commerce\Models\ProductVariant;
use RainLab\User\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Pixiu\Commerce\Models\Order;

class OrderController
{

    public function store(Request $request)
    {
        // proletet checkout a pak az vytvaret objednavku
        // $this->createNewOrder($request);
        return response(['msg' => 'Checkout logic -_^'], 201);
    }

    public function makeStoreOrderValidator($request)
    {
        return Validator::make($request, [

        ], [

        ]);
    }

    private function createNewOrder(Request $request): void
    {
        $order = new Order();
        if ($user = Auth::getUser()) {
            $user_id = $user->id;
        }

        $items = json_decode($request->input('items'), true);
        $variants = ProductVariant::
        select('id', 'price')
            ->find(array_column($items, 'variant_id'));

        $pivots = [];
        foreach ($items as $item) {
            $variant = $variants->where('id', $item['variant_id'])->first();

            $pivots[$variant->id] = [
                'quantity' => $item['quantity'],
                'price' => $variant->price
            ];
        }

        $order->save();
        $order->sync($pivots);
    }
}