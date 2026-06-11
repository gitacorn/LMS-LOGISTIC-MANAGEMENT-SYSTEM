<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordTypeToWarehouseMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table(config('constants.WAREHOUSE_MASTER_TABLE'), function (Blueprint $table) {
        	//$table->dropColumn('votes');
    		//$table->enum('e_record_type',[config('constants.LOCATION'),config('constants.WAREHOUSE'),config('constants.PORT')])->after('v_warehouse_short_code');
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
            //
        });
    }
}
