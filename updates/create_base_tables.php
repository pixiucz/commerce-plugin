<?php namespace Pixiu\Commerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateAttributeGroupsTable extends Migration
{
    public function up()
    {
        //Products
        Schema::create('pixiu_commerce_products', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });

        //Product combinations
        Schema::create('pixiu_commerce_product_combinations', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('pixiu_commerce_products')->onDelete('cascade');
        });

        //Attribute groups
        Schema::create('pixiu_commerce_attribute_groups', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });

        //Attributes
        Schema::create('pixiu_commerce_attributes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->integer('attribute_group_id')->unsigned();
            $table->foreign('attribute_group_id')->references('id')->on('pixiu_commerce_attribute_groups')->onDelete('cascade');
        });

        //Combination attributes
        Schema::create('pixiu_commerce_combination_attributes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();

            $table->integer('product_combination_id')->unsigned();
            $table->foreign('product_combination_id', 'product_combination_foreign')->references('id')->on('pixiu_commerce_product_combinations')->onDelete('cascade');

            $table->integer('attribute_id')->unsigned();
            $table->foreign('attribute_id')->references('id')->on('pixiu_commerce_attributes');

            $table->primary(['product_combination_id', 'attribute_id'], 'id');
        });

        //Categories
        Schema::create('pixiu_commerce_categories', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 128);

            #Nested tree
            $table->integer('parent_id')->unsigned()->nullable();
            #FIXME ONDELETE?
            $table->foreign('parent_id')->references('id')->on('pixiu_commerce_categories');

            $table->integer('nest_left')->unsigned()->nullable();
            $table->integer('nest_right')->unsigned()->nullable();
            $table->tinyInteger('nest_depth')->unsigned()->default(0);
        });

        //Category products
        Schema::create('pixiu_commerce_category_products', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('pixiu_commerce_products')->onDelete('cascade');

            $table->integer('category_id')->unsigned();
            #FIXME ONDELETE?
            $table->foreign('category_id')->references('id')->on('pixiu_commerce_categories')->onDelete('cascade');

            $table->primary(['product_id', 'category_id'], 'id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pixiu_commerce_category_products');
        Schema::dropIfExists('pixiu_commerce_categories');
        Schema::dropIfExists('pixiu_commerce_combination_attributes');
        Schema::dropIfExists('pixiu_commerce_attributes');
        Schema::dropIfExists('pixiu_commerce_attribute_groups');
        Schema::dropIfExists('pixiu_commerce_product_combinations');
        Schema::dropIfExists('pixiu_commerce_products');
    }
}