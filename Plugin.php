<?php namespace Pixiu\Commerce;

use Backend;
use Carbon\Carbon;
use Pixiu\Commerce\api\Classes\CategoryTreeAdapter;
use Pixiu\Commerce\api\Classes\CheckoutApi;
use Pixiu\Commerce\api\Enums\CartStatusEnum;
use Pixiu\Commerce\api\Tasks\CheckCarts;
use Pixiu\Commerce\Classes\Invoice\NormalInvoiceManager;
use Pixiu\Commerce\Classes\TaxHandler;
use Pixiu\Commerce\Models\Cart;
use System\Classes\PluginBase;
use Illuminate\Support\Facades\Event;
use RainLab\User\Models\User;
use Barryvdh\DomPDF\ServiceProvider;
use Pixiucz\Invoices\InvoicesServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Lang;
use Pixiu\Commerce\Classes\CurrencyHandler;

/**
 * Commerce Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'RainLab.User'
    ];
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
        \App::bind('CurrencyHandler', function($app) {
            return new CurrencyHandler;
        });
        \App::bind('TaxHandler', function($app) {
            return new TaxHandler;
        });
        \App::bind('CategoryTreeAdapter', function($app) {
            return new CategoryTreeAdapter;
        });
        \App::bind('CheckoutApi', function ($app) {
            return new CheckoutApi;
        });

        $this->app->register(InvoicesServiceProvider::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        // $this->logQueries();

        Event::listen('backend.menu.extendItems', function($manager) {
            $manager->removeMainMenuItem('RainLab.User', 'user');
        });

        User::extend(function($model) {
            $model->hasMany['addresses'] = ['Pixiu\Commerce\Models\Address'];
        });

        $this->app->register(ServiceProvider::class);

        AliasLoader::getInstance()->alias('PDF', 'Barryvdh\DomPDF\Facade');
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
                'url'         => Backend::url('pixiu/commerce/Orders'),
                'icon'        => 'icon-balance-scale',
                'permissions' => ['pixiu.commerce.*'],
                'order' => 500,
                'sideMenu' => [
                    'orders' => [
                        'label'       => Lang::get('pixiu.commerce::lang.menu.orders'),
                        'url'         => Backend::url('pixiu/commerce/Orders'),
                        'icon'        => 'icon-shopping-basket',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'products' => [
                        'label'     => Lang::get('pixiu.commerce::lang.menu.products'),
                        'url'         => Backend::url('pixiu/commerce/Products'),
                        'icon'        => 'icon-cube',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'categories' => [
                        'label' => Lang::get('pixiu.commerce::lang.menu.categories'),
                        'url'         => Backend::url('pixiu/commerce/Categories'),
                        'icon'        => 'icon-cubes',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'brands' => [
                        'label' => Lang::get('pixiu.commerce::lang.menu.brands'),
                        'url'         => Backend::url('pixiu/commerce/Brands'),
                        'icon'        => 'icon-diamond',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'users' => [
                        'label' => Lang::get('pixiu.commerce::lang.menu.users'),
                        'url'         => Backend::url('pixiu/commerce/Users'),
                        'icon'        => 'icon-male',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                    'taxes' => [
                        'label' => Lang::get('pixiu.commerce::lang.menu.taxes'),
                        'url'         => Backend::url('pixiu/commerce/Taxes'),
                        'icon'        => 'icon-money',
                        'permissions' => ['pixiu.commerce.*']
                    ],
                ]
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'commerce_settings' => [
                'label' => 'eShop settings',
                'description' => 'Názov, adresa, IČO,...',
                'category'    => 'Všeobecné nastavenia',
                'icon'        => 'icon-cog',
                'class'       => 'Pixiu\Commerce\Models\CommerceSettings',
                'order'       => 500,
                'keywords'    => 'security location',
                'permissions' => ['pixiu.turistickeznamky.*']
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule->call(function() {
            CheckCarts::run();
        })->daily();
    }

    private function logQueries()
    {
        \DB::listen(function($query) {
            \Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });
    }

}
