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
        echo 'Znamky #1' . PHP_EOL;
        DB::table('pixiu_com_categories')->insert(
            [
                'name' => 'Známky',
                'slug' => str_slug('Známky'),
                'nest_depth' => 1
            ]
        );

        echo 'Znamky #2' . PHP_EOL;
        DB::table('pixiu_com_categories')->insert(
            [
                'name' => 'Výročné známky',
                'slug' => 'vyrocne znamky',
                'nest_depth' => 2,
                'parent_id' => 1
            ]
        );

        echo 'Tax #1' . PHP_EOL;
        DB::table('pixiu_com_taxes')->insert([
            'name' => 'Základní sazba',
            'rate' => 21
        ]);

        echo 'DELIVERY OPTION #1' . PHP_EOL;
        DB::table('pixiu_com_delivery_options')->insert(
            [
                'name' => 'Česká pošta',
                'shipping_time' => 12345,
                'price' => 12345,
                'personal_collection' => false
            ]
        );

        echo "DELIVERY OPTION #2" . PHP_EOL;
        DB::table('pixiu_com_delivery_options')->insert(
            [
                'name' => 'Osobní vyzvednutí',
                'shipping_time' => 12345,
                'price' => 2500,
                'personal_collection' => true
            ]
        );

        echo 'PAYMENT METHOD #1' . PHP_EOL;
        DB::table('pixiu_com_payment_methods')->insert(
            [
                'name' => 'Dobírka',
                'cash_on_delivery' => true
            ]
        );

        echo 'PAYMENT METHOD #2' . PHP_EOL;
        DB::table('pixiu_com_payment_methods')->insert(
            [
                'name' => 'Platba kartou'
            ]
        );

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