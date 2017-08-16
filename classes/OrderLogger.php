<?php namespace Pixiu\Commerce\Classes;


class OrderLogger
{
    public static function addLog($order, string $title, string $style) {
        $order->logs()->create([
            'title' => $title,
            'style' => $style
        ]);
    }
}