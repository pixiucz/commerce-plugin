<?php namespace Pixiu\Commerce\Classes;

use Pixiu\Commerce\Models\CommerceSettings;

class TaxHandler
{
    public $rate;

    public function __construct()
    {
        $this->rate = CommerceSettings::get('tax');
    }

    public function getWithoutTax($price, $rate = null)
    {
        if ($rate) {
            $this->rate = $rate;
        }
        return $price * (1 - ($this->rate/100));
    }

    public function getTax($price, $rate = null)
    {
        if ($rate) {
            $this->rate = $rate;
        }
        return $price * ($this->rate/100);
    }
}