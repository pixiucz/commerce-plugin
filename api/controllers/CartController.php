<?php namespace Pixiu\Commerce\api\Controllers;

use Illuminate\Http\Request;
use Pixiu\Commerce\Models\Address;
use Pixiu\Commerce\Models\Cart;
use Pixiu\Commerce\Models\ProductVariant;
use RainLab\User\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController
{
    public function store(Request $request)
    {
        $cart = new Cart();
        if ($user = Auth::getUser()) {
            $cart->user_id = $user->id;
        }

        $orderItems = $this->parseRequestItems($request);

        $cart->save();
        $cart->variants()->sync($orderItems);

        // Parse data for checkout
        $checkoutData = $this->getCheckoutData($orderItems, $user);

        // Send data to checkout and get token
        $token = $this->getCheckoutToken($checkoutData);

        // Create redirect based on token
        $redirect = $this->getRedirect($token);


        return response(['msg' => 'Fsechno fici',], 201);
    }

    /**
     * @param $items
     * @return array
     */
    private function parseRequestItems(Request $request): array
    {
        $items = json_decode($request->input('items'), true);

        $variants = ProductVariant::
        with('primary_picture')
        ->with('tax')
        ->select('id', 'price', 'primary_picture_id', 'product_id', 'slug', 'tax_id')
        ->find(array_column($items, 'variant_id'));

        $pivots = [];
        foreach ($items as $item) {
            $variant = $variants->where('id', $item['variant_id'])->first();

            $pivots[$variant->id] = [
                'name' => $variant->full_name,
                'quantity' => $item['quantity'],
                'price' => $variant->price,
                'tax_name' => $variant->tax->name,
                'tax_rate' => $variant->tax->rate,
                'slug' => $variant->slug,
                'picture' => $variant->primary_picture->path ?? null
            ];
        }
        return $pivots;
    }

    private function getCheckoutData($orderItems, $user):array{
        if ($user) {
            $user = $user->toArray();
            $user['addresses'] = Address::where('user_id', $user['id'])->get()->toArray();
        }

        return [
            'order' => $orderItems,
            'user' => $user ?? null,
            'eshop_token' => $this->getCommerceToken()
        ];
    }

    private function getCommerceToken($checkoutData){return '123456';}

    private function getCheckoutToken():string{}

    private function getRedirect(string $token):string{}


}