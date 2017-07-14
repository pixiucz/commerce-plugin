<?php namespace Pixiu\Crm\Models\Updates;

use Seeder;
use Pixiu\Commerce\Models\{Category, Tax, Brand, Address, PaymentMethod, DeliveryOption, OrderStatus};
use RainLab\User\Models\User;
use Faker;

class SeedUsersTable extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => 'Pocitace',
            'nest_depth' => 1
        ]);

        Category::create([
            'name' => 'Tablety',
            'nest_depth' => 2,
            'parent_id' => 1
        ]);

        Category::create([
            'name' => 'Notebooky',
            'nest_depth' => 2,
            'parent_id' => 1
        ]);

        Tax::create([
            'name' => 'DPH21',
            'rate' => 21
        ]);

        Tax::create([
            'name' => 'Snizena sazba',
            'rate' => 11
        ]);

        Brand::create([
            'name' => 'Apple',
            'description' => 'Jabka jo?'
        ]);

        Brand::create([
            'name' => 'Microsoft',
            'description' => 'Vokna jo?'
        ]);

        DeliveryOption::create([
            'name' => 'Post',
            'shipping_time' => 12345,
            'price' => 99.9
        ]);

        PaymentMethod::create([
            'name' => 'Platba pri dobirce'
        ]);

        OrderStatus::create([
            'title' => 'Stornovano',
            'color' => 'red',
            'is_canceled' => true
        ]);

        OrderStatus::create([
            'title' => 'Nove',
            'color' => 'blue',
            'is_canceled' => false
        ]);

        OrderStatus::create([
            'title' => 'Vyrizene',
            'color' => 'green',
            'is_canceled' => false
        ]);
    }
}