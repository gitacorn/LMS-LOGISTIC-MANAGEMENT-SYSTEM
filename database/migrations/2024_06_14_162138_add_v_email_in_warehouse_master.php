<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVEmailInWarehouseMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.WAREHOUSE_MASTER_TABLE'), function (Blueprint $table) {
    		$table->text('v_warehouse_email')->after('v_warehouse_short_code')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table(config('constants.WAREHOUSE_MASTER_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('v_warehouse_email');
    	});
    }
}
