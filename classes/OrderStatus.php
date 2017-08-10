<?php namespace Pixiu\Commerce\Classes;

use Illuminate\Support\Facades\Lang;


abstract class OrderStatus
{
    const NEW = "new";
    const READY_FOR_COLLECTION = "ready for collection";
    const SHIPPED = "shipped";
    const FINISHED = "finished";
    const CANCELED = "canceled";

    public static function getNew()
    {
        return Lang::get('pixiu.commerce::lang.orderstatus.new');
    }

    public static function getReadyForCollection()
    {
        return Lang::get('pixiu.commerce::lang.orderstatus.ready_for_collection');
    }

    public static function getShipped()
    {
        return Lang::get('pixiu.commerce::lang.orderstatus.shipped');
    }

    public static function getCanceled()
    {
        return Lang::get('pixiu.commerce::lang.orderstatus.canceled');
    }

    public static function getFinished()
    {
        return Lang::get('pixiu.commerce::lang.orderstatus.finished');
    }
}