<?php namespace Pixiu\Commerce\Classes;

use Illuminate\Support\Facades\Lang;


abstract class PaymentStatus
{
    const PAID = "paid";
    const AWAITING_PAYMENT = "awaiting payment";
    const CASH_ON_DELIVERY = "cash on delivery";

    public static function getAll() : array
    {
        $array = [];
        $array = array_add($array, PaymentStatus::PAID, Lang::get('pixiu.commerce::lang.payment_status.paid'));
        $array = array_add($array, PaymentStatus::AWAITING_PAYMENT, Lang::get('pixiu.commerce::lang.payment_status.awaiting_payment'));
        $array = array_add($array, PaymentStatus::CASH_ON_DELIVERY, Lang::get('pixiu.commerce::lang.payment_status.cash_on_delivery'));
        return $array;
    }

    public static function getCashOnDelivery() : array
    {
        return [PaymentStatus::CASH_ON_DELIVERY => Lang::get('pixiu.commerce::lang.payment_status.cash_on_delivery')];
    }
}