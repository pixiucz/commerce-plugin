<?php namespace Pixiu\Commerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateAttributeGroupsTable extends Migration
{
    public function up()
    {
        //Taxes
        Schema::create('pixiu_com_taxes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('name');
            $table->integer('rate');
        });

        // Brands
        Schema::create('pixiu_com_brands', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('name');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
        });

        //Products
        Schema::create('pixiu_com_products', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('tax_id')->unsigned()->default(1);
            $table->foreign('tax_id')->references('id')->on('pixiu_com_taxes');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('pixiu_com_brands');

            $table->string('name');
            $table->string('ean')->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('active')->default(true);
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->bigInteger('retail_price')->unsigned();
            $table->longText('specifications')->nullable();
            $table->boolean('has_variants')->default(true);
            $table->integer('tax_rate')->nullable();

            $table->timestamps();
        });

        //Product variants
        Schema::create('pixiu_com_product_variants', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('pixiu_com_products')->onDelete('cascade');

            $table->integer('primary_picture_id')->unsigned()->nullable();
            $table->foreign('primary_picture_id')->references('id')->on('system_files');

            $table->integer('in_stock')->default(0);
            $table->integer('reserved_stock')->unsigned()->default(0);


            $table->string('ean')->nullable();
            $table->bigInteger('price')->unsigned()->nullable();
            $table->longText('specifications')->nullable();
            $table->string('slug')->unique();
        });

        // Images pivot
        Schema::create('pixiu_com_variant_images', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();

            $table->integer('variant_id')->unsigned();
            $table->foreign('variant_id')->references('id')->on('pixiu_com_product_variants')->onDelete('cascade');
            $table->integer('system_file_id')->unsigned();
            $table->foreign('system_file_id')->references('id')->on('system_files')->onDelete('cascade');

            $table->primary(['variant_id', 'system_file_id']);
        });

        //Attribute groups
        Schema::create('pixiu_com_attribute_groups', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        //Attributes
        Schema::create('pixiu_com_attributes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('value');
            $table->timestamps();

            $table->integer('attribute_group_id')->unsigned();
            $table->foreign('attribute_group_id')->references('id')->on('pixiu_com_attribute_groups')->onDelete('cascade');
        });

        //Variant-attributes pivot
        Schema::create('pixiu_com_variant_attributes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();

            $table->integer('variant_id')->unsigned();
            $table->foreign('variant_id', 'product_variant_foreign')->references('id')->on('pixiu_com_product_variants')->onDelete('cascade');

            $table->integer('attribute_id')->unsigned();
            $table->foreign('attribute_id')->references('id')->on('pixiu_com_attributes');

            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('pixiu_com_attribute_groups');

            $table->unique(['variant_id', 'group_id']);
            $table->primary(['variant_id', 'attribute_id'], 'id');
        });

        //Categories
        Schema::create('pixiu_com_categories', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 128);
            $table->string('slug')->unique();

            #Nested tree
            $table->integer('parent_id')->unsigned()->nullable();
            #FIXME ONDELETE?
            $table->foreign('parent_id')->references('id')->on('pixiu_com_categories');

            $table->integer('nest_left')->unsigned()->nullable();
            $table->integer('nest_right')->unsigned()->nullable();
            $table->tinyInteger('nest_depth')->unsigned()->default(0);
        });

        //Category products
        Schema::create('pixiu_com_category_products', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('pixiu_com_products')->onDelete('cascade');

            $table->integer('category_id')->unsigned();
            #FIXME ONDELETE?
            $table->foreign('category_id')->references('id')->on('pixiu_com_categories')->onDelete('cascade');

            $table->primary(['product_id', 'category_id'], 'id');
        });

        // Product pictures
        Schema::create('pixiu_com_product_pictures', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('url');

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('pixiu_com_products')->onDelete('cascade');

            $table->integer('variant_id')->unsigned()->nullable();
            $table->foreign('variant_id')->references('id')->on('pixiu_com_product_variants')->onDelete('cascade');

            $table->string('name');
        });

        // Paymend methods
        Schema::create('pixiu_com_payment_methods', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('name');
            $table->boolean('cash_on_delivery')->default(false);

            // TODO
        });

        // Delivery options
        Schema::create('pixiu_com_delivery_options', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('name');
            $table->integer('shipping_time');
            $table->bigInteger('price')->unsigned();

            $table->boolean('personal_collection');

            // TODO: Rules for pricing, Size rules,...
        });

        // Order statuses
        Schema::create('pixiu_com_order_statuses', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('title');
            $table->string('color');

            // Requires mail-templates implementation
            // $table->boolean('sends_email')->default(false);

        });

        // Addresses
        Schema::create('pixiu_com_addresses', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('city');

//            TODO: Is state necessary
//            $table->string('state');

            // String because of UK
            $table->string('zip');
            $table->string('country');

            $table->integer('ic')->nullable();
            $table->string('dic')->nullable();

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

        });

        // Orders
        Schema::create('pixiu_com_orders', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('delivery_address_id')->unsigned();
            $table->foreign('delivery_address_id')->references('id')->on('pixiu_com_addresses');

            $table->integer('billing_address_id')->unsigned()->nullable();
            $table->foreign('billing_address_id')->references('id')->on('pixiu_com_addresses');

            $table->integer('payment_method_id')->unsigned();
            $table->foreign('payment_method_id')->references('id')->on('pixiu_com_payment_methods');

            $table->integer('order_status_id')->unsigned()->nullable();
            $table->foreign('order_status_id')->references('id')->on('pixiu_com_order_statuses');

            $table->integer('delivery_option_id')->unsigned();
            $table->foreign('delivery_option_id')->references('id')->on('pixiu_com_delivery_options');

            $table->enum('status', [
                'new', 'ready for collection', 'shipped', 'finished', 'canceled'
            ]);

            $table->enum('payment_status', [
                'paid', 'awaiting payment', 'cash on delivery'
            ]);

        });

        // OrderLog
        Schema::create('pixiu_com_order_logs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('pixiu_com_orders');

            $table->string('title');
            $table->mediumText('content')->nullable();
            $table->string('style')->nullable();

        });

        // Order notes
        Schema::create('pixiu_com_order_notes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');

            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('pixiu_com_orders')->onDelete('cascade');

            $table->text('title');
            $table->text('text');
        });

        // Pivot to connect Products <--> Attribute Groups
        Schema::create('pixiu_com_products_groups', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('pixiu_com_products');

            $table->integer('attribute_group_id')->unsigned();
            $table->foreign('attribute_group_id')->references('id')->on('pixiu_com_attribute_groups');

            $table->primary(['product_id', 'attribute_group_id']);
        });

        // Pivot to connect Orders <--> Variants
        Schema::create('pixiu_com_orders_variants', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('variant_id')->unsigned();
            $table->foreign('variant_id')->references('id')->on('pixiu_com_product_variants');

            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('pixiu_com_orders');

            $table->integer('quantity')->default(0);
            $table->integer('refunded_quantity')->default(0);
            $table->integer('price')->default(0);
            $table->boolean('lowered_stock')->default(false);

            $table->primary(['variant_id', 'order_id']);
        });

        // Pdf invoices
        Schema::create('pixiu_com_pdf_invoices', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->increments('id');
            $table->string('type');
            $table->string('path');
            $table->string('invoice_number');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('pixiu_com_orders');
        });

        // Invoices -> Commerce Product Variants
    }

    public function down()
    {
        Schema::dropIfExists('pixiu_com_pdf_invoices');
        Schema::dropIfExists('pixiu_com_orders_variants');
        Schema::dropIfExists('pixiu_com_products_groups');
        Schema::dropIfExists('pixiu_com_variant_images');
        Schema::dropIfExists('pixiu_com_order_variants');
        Schema::dropIfExists('pixiu_com_order_logs');
        Schema::dropIfExists('pixiu_com_order_notes');
        Schema::dropIfExists('pixiu_com_orders');
        Schema::dropIfExists('pixiu_com_payment_methods');
        Schema::dropIfExists('pixiu_com_delivery_options');
        Schema::dropIfExists('pixiu_com_category_products');
        Schema::dropIfExists('pixiu_com_categories');
        Schema::dropIfExists('pixiu_com_variant_attributes');
        Schema::dropIfExists('pixiu_com_attributes');
        Schema::dropIfExists('pixiu_com_attribute_groups');
        Schema::dropIfExists('pixiu_com_product_pictures');
        Schema::dropIfExists('pixiu_com_product_variants');
        Schema::dropIfExists('pixiu_com_products');
        Schema::dropIfExists('pixiu_com_taxes');
        Schema::dropIfExists('pixiu_com_brands');
        Schema::dropIfExists('pixiu_com_order_statuses');
        Schema::dropIfExists('pixiu_com_addresses');
//        Schema::dropIfExists('pixiu_invoices');
    }
}
