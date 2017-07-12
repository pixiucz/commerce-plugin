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
            'name' => 'Tricka',
            'nest_depth' => 1
        ]);

        Category::create([
            'name' => 'S dlouhym rukavem',
            'nest_depth' => 2,
            'parent_id' => 1
        ]);

        Category::create([
            'name' => 'S kratkym rukavem',
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
            'name' => 'Nike',
            'description' => 'Lorem ipsum'
        ]);

        Brand::create([
            'name' => 'Adidas',
            'description' => 'Boty jo?'
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
            'title' => 'Canceled',
            'color' => 'red',
            'decreases_stock' => false
        ]);

        OrderStatus::create([
            'title' => 'Done',
            'color' => 'green',
            'decreases_stock' => false
        ]);
    }
}