<?php namespace Pixiu\Commerce\api\Tasks;

use Pixiu\Commerce\api\Classes\CheckoutApi;
use Pixiu\Commerce\api\Enums\CartStatusEnum;
use Pixiu\Commerce\Models\Cart;


class CheckCarts
{
    public static function run()
    {
        $checkout = app('CheckoutApi');
        $carts = Cart::
            where('status', CartStatusEnum::REDIRECT_SENT)
            ->where('created_at', '>', Carbon::now()->subMinute(10)->toDateTimeString())
            ->get();

        $carts->each(function($cart) use ($checkout) {
            $status = $checkout->checkStatusFor($cart->token);

            if ($status === "fuck you") {
                $cart->status = CartStatusEnum::ABANDONED;
                $cart->save();
            }
            // TODO: Handle all situations
        });
    }
}