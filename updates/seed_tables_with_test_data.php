<?php namespace Pixiu\Crm\Models\Updates;

use Seeder;
use Pixiu\Commerce\Models\{
    Category, Brand, Address, PaymentMethod, DeliveryOption, OrderStatus, Product, ProductVariant, Tax
};
use RainLab\User\Models\User;
use Faker;
use Illuminate\Support\Facades\DB;

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

        Tax::create([
            'name' => 'Zakladni sazba',
            'rate' => 21
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


        // Defaultni settings a prepnuti do cestiny
        DB::table('system_settings')->insert([
            'item' => 'commerce_settings',
            'value' => json_encode([
                'tax' => 21,
                'currency' => 'Kč',
                'decimalSymbol' => ',',
                'company_name' => 'Turisticke Znamky',
                'commerce_email' => 'test@test.cz',
                'address' => 'Testovaci adresa 123',
                'zip' => '12345',
                'ico' => '12345',
                'dic' => '12345',
                'ic_dph' => '???',
                'bank' => '1234 1234 1234 1234',
                'account_number' => '1234',
                'swift' => '1234',
                'iban' => '1234'
            ])
        ]);

        DB::table('backend_user_preferences')->insert([
            'user_id' => 1,
            'namespace' => 'backend',
            'group' => 'backend',
            'item' => 'preferences',
            'value' => json_encode([
                'locale' => 'cs',
                'fallback_locale' => 'en'
            ])
        ]);

    }
}