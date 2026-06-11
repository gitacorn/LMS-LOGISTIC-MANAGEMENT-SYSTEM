<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsWarehouseRecordNoToUsWarehouseToAmazonMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'), function (Blueprint $table) {
            $table->longText('v_us_warehouse_to_amazon_record_no')->after('i_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'), function (Blueprint $table) {
            
        });
    }
}
