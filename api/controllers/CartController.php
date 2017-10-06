<?php namespace Pixiu\Commerce\api\Controllers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Pixiu\Commerce\Models\Address;
use Pixiu\Commerce\Models\Cart;
use Pixiu\Commerce\Models\ProductVariant;
use RainLab\User\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Pixiu\Commerce\api\Enums\CartStatusEnum;
use GuzzleHttp\Client;
use Pixiu\Commerce\api\Classes\CheckoutApi;

class CartController
{
    const SLUG = "turisticke-znamky";

    public function store(Request $request)
    {
        // TODO: validate request?

        $user = Auth::getUser();

        $orderItems = $this->parseRequestItems($request);

        $cart = $this->createCart($orderItems, $user);

        // Parse data for checkout
        $checkoutData = $this->getCheckoutData($orderItems, $user);

        // Send data to checkout and get token
        try {
            $token = $this->getCheckoutToken($checkoutData);
        } catch (\Exception $e){
            trace_log($e->getMessage());
            return response([
                'msg' => 'Tvorba tokenu selhala, udalost zalogovana.'
            ], 500);
        }

        // Save token to cart
        tap($cart, function($cart) use ($token) {
            $cart->token = $token;
            $cart->status = CartStatusEnum::REDIRECT_SENT;
            $cart->save();
        });

        // Create redirect based on token
        $redirect = $this->getRedirect($token);

        return response([
            'msg' => 'Success',
            'redirect' => $redirect
        ], 200);
    }

    private function createCart($orderItems, $user)
    {
        $cart = Cart::create([
            'status' => CartStatusEnum::REQUESTED_TOKEN,
            'user_id' => $user->id ?? null
        ]);
        $cart->variants()->sync($orderItems);
        return $cart;
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
        ->with('product.tax')
        ->select('id', 'price', 'primary_picture_id', 'product_id', 'slug')
        ->find(array_column($items, 'variant_id'));

        $pivots = [];
        foreach ($items as $item) {
            $variant = $variants->where('id', $item['variant_id'])->first();

            $pivots[$variant->id] = [
                'name' => $variant->full_name,
                'quantity' => $item['quantity'],
                'price' => $variant->price,
                'tax_name' => $variant->product->tax->name,
                'tax_rate' => $variant->product->tax->rate,
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

    private function getCommerceToken(){
        // TODO: Get token from settings in prod.
        return 'CljNPzDIWMOn4B8zN9IABlAj4ele0HYO';
    }

    private function getCheckoutToken($checkoutData):string{
        $checkoutData['slug'] = self::SLUG;
        $checkoutData['key'] = $this->getCommerceToken();

        $response = app('CheckoutApi')->createCheckout($checkoutData);
        return $response['token'];
    }

    private function getRedirect(string $token):string{
        return "http://checkout.dev/checkout?token=" . $token;
    }


}