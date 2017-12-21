<?php namespace Pixiu\Commerce\Updates;

use Seeder;
use Illuminate\Support\Facades\DB;
use Faker;

class CommerceSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        echo "Commerce seeder started" . PHP_EOL;

        DB::table('pixiu_com_categories')->insert(
            [
                'name' => 'Zrušené známky',
                'slug' => str_slug('Zrušené známky'),
                'nest_depth' => 1
            ]
        );

        DB::table('pixiu_com_categories')->insert(
            [
                'name' => 'Výročné známky',
                'slug' => 'vyrocne-znamky',
                'nest_depth' => 1,
            ]
        );

        DB::table('pixiu_com_taxes')->insert([
            'name' => 'Základní sazba',
            'rate' => 21
        ]);

        foreach (range(1, 30) as $i) {
            $name = $faker->sentence(3);
            DB::table('pixiu_com_products')->insert([
                'tax_id' => 1,
                'name' => $name,
                'short_description' => $faker->paragraph(8),
                'long_description' => $faker->paragraph(15),
                'retail_price' => 199,
                'has_variants' => false,
            ]);

            DB::table('pixiu_com_product_variants')->insert([
                'product_id' => $i,
                'in_stock' => 10,
                'reserved_stock' => 0,
                'price' => 199,
                'slug' => str_slug($name)
            ]);

            DB::table('pixiu_com_category_products')->insert([
                'product_id' => $i,
                'category_id' => 1
            ]);
        }

        foreach (range(31, 60) as $i) {
            $name = $faker->sentence(3);
            DB::table('pixiu_com_products')->insert([
                'tax_id' => 1,
                'name' => $name,
                'short_description' => $faker->paragraph(8),
                'long_description' => $faker->paragraph(15),
                'retail_price' => 199,
                'has_variants' => false,
            ]);

            DB::table('pixiu_com_product_variants')->insert([
                'product_id' => $i,
                'in_stock' => 10,
                'reserved_stock' => 0,
                'price' => 199,
                'slug' => str_slug($name)
            ]);

            DB::table('pixiu_com_category_products')->insert([
                'product_id' => $i,
                'category_id' => 2
            ]);
        }

        // ADD USER
        DB::table('users')->insert([
            'email' => 'test@test.cz',
            'password' => bcrypt('test'),
            'name' => 'Josef',
            'surname' => 'Novak',
            'is_activated' => 1,
        ]);

        // ADD ADDRESS TO USER

        DB::table('pixiu_com_addresses')->insert([
            'first_name' => 'Jan',
            'last_name' => 'Testowicz',
            'address' => 'Palachova 1337',
            'city' => 'Brno',
            'zip' => '613 00',
            'country' => 'Czech republic',
            'user_id' => 1
        ]);

        DB::table('pixiu_com_delivery_options')->insert(
            [
                'name' => 'Česká pošta',
                'shipping_time' => 12345,
                'price' => 12345,
                'personal_collection' => false
            ]
        );

        DB::table('pixiu_com_delivery_options')->insert(
            [
                'name' => 'Osobní vyzvednutí',
                'shipping_time' => 12345,
                'price' => 2500,
                'personal_collection' => true
            ]
        );



        DB::table('pixiu_com_payment_methods')->insert(
            [
                'name' => 'Dobírka',
                'cash_on_delivery' => true
            ]
        );

        DB::table('pixiu_com_payment_methods')->insert(
            [
                'name' => 'Platba kartou'
            ]
        );

        // TODO: seed of test orders

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

        echo "Commerce seeder finished" . PHP_EOL;
    }
}