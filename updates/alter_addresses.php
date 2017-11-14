<?php namespace Pixiu\Commerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AlterAddresses extends Migration
{
    public function up()
    {
        Schema::table('pixiu_com_addresses', function(Blueprint $table) {
            $table->boolean('is_billing')->default(false);
        });
    }

    public function down()
    {
        Schema::table('pixiu_com_addresses', function(Blueprint $table) {
            $table->dropColumn('is_billing');
        });
    }
}
