<?php namespace Pixiu\Commerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('pixiu_com_carts', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
            $table->string('token')->nullable();

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });


        Schema::create('pixiu_com_carts_variants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('cart_id')->unsigned();
            $table->foreign('cart_id')->references('id')->on('pixiu_com_carts');

            $table->integer('variant_id')->unsigned();
            $table->foreign('variant_id')->references('id')->on('pixiu_com_product_variants');


            $table->integer('quantity')->unsigned();
            $table->integer('price')->unsigned();
            $table->string('slug');
            $table->string('name');
            $table->string('picture')->nullable();

            $table->enum('status', [
                'requested token', 'redirect sent', 'paid', 'cash on delivery', 'abandoned'
            ]);


            $table->integer('tax_rate')->unsigned();
            $table->string('tax_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pixiu_commerce_carts');
    }
}
