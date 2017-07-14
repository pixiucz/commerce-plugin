<?php namespace Pixiu\Commerce\Classes;

use Pixiu\Commerce\Models\CommerceSettings;

class Tax
{
    public $rate;

    public function __construct()
    {
        $this->rate = CommerceSettings::get('tax');
    }

    public function getWithoutTax($price)
    {
        return $price * (1 - ($this->rate/100));
    }

    public function getTax($price)
    {
        return $price * ($this->rate/100);
    }
}