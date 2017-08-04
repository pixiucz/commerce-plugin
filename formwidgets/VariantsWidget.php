<?php namespace Pixiu\Commerce\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Pixiu\Commerce\Models\{AttributeGroup, Attribute, ProductVariant};
use Backend;
use Pixiu\Commerce\Classes\CurrencyHandler;

/**
 * VariantsWidget Form Widget
 */
class VariantsWidget extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'pixiu_commerce_variants_widget';

    private $currencyHandler;
    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->currencyHandler = \App::make('CurrencyHandler');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('variantswidget');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
        $this->vars['updateOptions'] = null;

        $data['update'] = false;
        $data['currencySettings'] = [
            'currencySymbol' => $this->currencyHandler->currencySymbol,
            'decimalSymbol' => $this->currencyHandler->decimalSymbol,
            'format' => $this->currencyHandler->format
        ];

        if (isset($this->model['attributes']['id'])){
            if($productVariants = ProductVariant::with('attributes.attributegroup')->where('product_id', '=', $this->model['attributes']['id'])->get()->toArray()){
                $data['update'] = true;
                $this->vars['updateOptions'] = array_pluck($productVariants[0]['attributes'], 'attributegroup');

                foreach ($productVariants as &$productVariant){
                    $productVariant['price'] = $this->currencyHandler->getValueForInput($productVariant['price']);
                }

                $data['variants'] = $productVariants;
                $data['backendUrl'] = Backend::url('pixiu/commerce/productvariants/');
            };

        }

        $this->vars['updateData'] = json_encode($data);
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/variantswidget.css', 'pixiu.commerce');
        $this->addJs('js/variantswidget.js', 'pixiu.commerce');
//        $this->addJs('js/build.js', 'pixiu.commerce');

    }

}
