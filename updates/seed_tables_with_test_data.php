<?php namespace Pixiu\Crm\Models\Updates;

use Seeder;
use Pixiu\Commerce\Models\{Category, Tax, Brand};

class SeedUsersTable extends Seeder
{
    public function run()
    {
//        $category = Category::create([
//            'name'                  => 'root'
//        ]);
//
//        $category = Category::create([
//            'name'                  => 'sss',
//            'parent_id'             => $category->id
//        ]);

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

    }
}