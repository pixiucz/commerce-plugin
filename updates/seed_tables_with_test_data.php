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
            'name' => 'Známky',
            'slug' => str_slug('Známky'),
            'nest_depth' => 1
        ]);

        Category::create([
            'name' => 'Výročné známky',
            'slug' => str_slug('Výročné známky'),
            'nest_depth' => 2,
            'parent_id' => 1
        ]);

        Category::create([
            'name' => 'Zrušené známky',
            'slug' => str_slug('Zrušené známky'),
            'nest_depth' => 2,
            'parent_id' => 1
        ]);

//        $address = new Address();
//        $address->user_id = 1;
//        $address->first_name = 'Karel';
//        $address->last_name = "Obecnik";
//        $address->address = "Svatovaclavske 432/21";
//        $address->city = "Brno";
//        $address->zip = "620 02";
//        $address->country = "Czech republic";
//        $address->save();

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