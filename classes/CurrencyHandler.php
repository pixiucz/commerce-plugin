<?php namespace Pixiu\Commerce\Classes;

use Pixiu\Commerce\Models\CommerceSettings;

class CurrencyHandler
{
    public $currencySymbol;
    public $decimalSymbol;
    public $format;

    /**
     * CurrencyHandler constructor.
     * @param $currencySymbol
     * @param $decimalSymbol
     * @param $format
     */
    public function __construct()
    {
        $this->currencySymbol = CommerceSettings::get('currency');
        $this->decimalSymbol = CommerceSettings::get('decimalSymbol');
        $this->format = "%n %s";
    }

    public function getValueFromInput($input) : int
    {

        return (int) str_replace(' ', '', str_replace($this->decimalSymbol, '', substr($input, 0, -(strlen($this->currencySymbol)+1))));
    }

    public function getValueForInput($value) : float
    {
        return ($value / 100);
    }






}