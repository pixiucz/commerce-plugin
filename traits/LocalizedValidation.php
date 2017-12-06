<?php namespace Pixiu\Commerce\Traits;

use Illuminate\Support\Facades\Lang;

trait LocalizedValidation
{
    public $attributeNames = [];

    public function localizeValidation()
    {
        if (is_array(Lang::get('pixiu.commerce::lang.fields'))){
            $this->attributeNames = Lang::get('pixiu.commerce::lang.fields');
        }
    }
}