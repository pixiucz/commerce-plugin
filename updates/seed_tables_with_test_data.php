<?php namespace Pixiu\Crm\Models\Updates;

use Seeder;
use Pixiu\Commerce\Models\{
    Category, Brand, Address, PaymentMethod, DeliveryOption, OrderStatus, Product, ProductVariant
};
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

        Brand::create([
            'name' => 'Apple',
            'description' => 'Jabka jo?'
        ]);

        Brand::create([
            'name' => 'Microsoft',
            'description' => 'Vokna jo?'
        ]);

        $product = new Product();
        $product->brand_id = 1;
        $product->name = "iPhone 7";
        $product->ean = 8439893823;
        $product->visible = true;
        $product->active = true;
        $product->retail_price = 21999;
        $product->save();

        $address = new Address();
        $address->user_id = 1;
        $address->first_name = 'Karel';
        $address->last_name = "Obecnik";
        $address->address = "Svatovaclavske 432/21";
        $address->city = "Brno";
        $address->zip = "620 02";
        $address->country = "Czech republic";
        $address->save();

        DeliveryOption::create([
            'name' => 'Česká pošta',
            'shipping_time' => 12345,
            'price' => 9900,
            'personal_collection' => false
        ]);

        DeliveryOption::create([
            'name' => 'Osobní vyzvednutí',
            'shipping_time' => 12345,
            'price' => 2500,
            'personal_collection' => true
        ]);

        PaymentMethod::create([
            'name' => 'Dobírka',
            'cash_on_delivery' => true
        ]);

        PaymentMethod::create([
            'name' => 'Platba kartou'
        ]);

    }
}