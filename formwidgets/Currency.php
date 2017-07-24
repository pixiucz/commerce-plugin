<?php namespace Pixiu\Commerce\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * Currency Form Widget
 */
class Currency extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'pixiu_commerce_currency';

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('currency');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/currency.css', 'Pixiu.commerce');
        $this->addJs('js/jquery.formatcurrency.min.js', 'Pixiu.commerce');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }
}
