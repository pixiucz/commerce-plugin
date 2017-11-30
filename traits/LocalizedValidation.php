<?php namespace Pixiu\Commerce\Traits;

use Illuminate\Support\Facades\Lang;

trait LocalizedValidation
{
    public $attributeNames = [];

    public function localizeValidation()
    {
        $this->attributeNames = Lang::get('pixiu.commerce::lang.fields');
    }
}