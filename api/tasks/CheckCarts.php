<?php namespace Pixiu\Commerce\api\Tasks;

use Pixiu\Commerce\api\Classes\CheckoutApi;
use Pixiu\Commerce\api\Enums\CartStatusEnum;
use Pixiu\Commerce\Models\Cart;
use Carbon\Carbon;


class CheckCarts
{
    public static function run()
    {
        $checkout = new CheckoutApi();

        $carts = Cart::
            where('updated_at', '<', Carbon::now()->subDay()->toDateTimeString())
            ->whereIn('status', [CartStatusEnum::REDIRECT_SENT, CartStatusEnum::REQUESTED_TOKEN])
            ->get();


        $carts->where('status', CartStatusEnum::REQUESTED_TOKEN)->each(function($cart) {
             $cart->status = CartStatusEnum::ABANDONED;
             $cart->save();
        });

        $tokens = $carts->where('status', CartStatusEnum::REDIRECT_SENT)->pluck('token')->toArray();
        $orders = $checkout->getStatusesFor($tokens);

        if (!empty($orders)){
            foreach ($orders as $order) {
                $cart = $carts->where('token', $order['token'])->first();
                if ($order['status'] === 'done'){
                    // TODO: Create order in eshop from api data (delegate)
                    $cart->status = CartStatusEnum::DONE;
                    $cart->save();
                    return 'done';
                }
                $cart->status = CartStatusEnum::ABANDONED;
                $cart->save();
            }
        }
    }
}