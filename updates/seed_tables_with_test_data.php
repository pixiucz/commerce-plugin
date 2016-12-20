<?php namespace Pixiu\Crm\Models\Updates;

use Seeder;
use Pixiu\Commerce\Models\Category;

class SeedUsersTable extends Seeder
{
    public function run()
    {
        $category = Category::create([
            'name'                  => 'root'
        ]);

        $category = Category::create([
            'name'                  => 'sss',
            'parent_id'             => $category->id
        ]);
    }
}