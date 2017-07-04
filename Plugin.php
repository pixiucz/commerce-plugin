<?php namespace Pixiu\Commerce;

use Backend;
use System\Classes\PluginBase;

/**
 * Commerce Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Commerce',
            'description' => 'No description provided yet...',
            'author'      => 'Pixiu',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Pixiu\Commerce\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'pixiu.commerce.some_permission' => [
                'tab' => 'Commerce',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'commerce' => [
                'label'       => 'Commerce',
                'url'         => Backend::url('pixiu/commerce/Products'),
                'icon'        => 'icon-leaf',
                'permissions' => ['pixiu.commerce.*'],
                'order' => 500,
                'sideMenu' => [
                    'products' => [
                        'label' => 'Products',
                        'url'         => Backend::url('pixiu/commerce/Products'),
                        'icon'        => 'icon-leaf',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'categories' => [
                        'label' => 'Categories',
                        'url'         => Backend::url('pixiu/commerce/Categories'),
                        'icon'        => 'icon-leaf',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'taxes' => [
                        'label' => 'Taxes',
                        'url'         => Backend::url('pixiu/commerce/Taxes'),
                        'icon'        => 'icon-leaf',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'brands' => [
                        'label' => 'Brands',
                        'url'         => Backend::url('pixiu/commerce/Brands'),
                        'icon'        => 'icon-leaf',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'delivery_options' => [
                        'label' => 'Delivery options',
                        'url'         => Backend::url('pixiu/commerce/DeliveryOptions'),
                        'icon'        => 'icon-leaf',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'payment_methods' => [
                        'label' => 'Payment methods',
                        'url'         => Backend::url('pixiu/commerce/PaymentMethods'),
                        'icon'        => 'icon-leaf',
                        'permissions' => ['pixiu.commerce.*']
                    ]
                ]
            ]
        ];
    }

}
