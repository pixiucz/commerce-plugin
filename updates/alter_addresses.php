<?php namespace Pixiu\Commerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AlterAddresses extends Migration
{
    public function up()
    {
        Schema::table('pixiu_com_addresses', function(Blueprint $table) {
            $table->dropColumn('ic');
            $table->string('ico')->nullable();
            $table->string('dic')->nullable()->change();
            $table->string('company')->nullable();
            $table->string('telephone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pixiu_com_addresses', function(Blueprint $table) {
            $table->dropColumn(['ico', 'dic', 'company', 'telephone']);
        });
    }
}
