<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableETransportWayIBookByEmployeeIdAndILogisticPartnerDetailIdColumnIntoUsWarehouseToAmazonMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'), function (Blueprint $table) {
    		$table->integer('i_book_by_employee_id')->nullable()->change();
    		$table->integer('i_logistic_partner_detail_id')->nullable()->change();
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
    		$table->integer('i_book_by_employee_id')->nullable(false)->change();
    		$table->integer('i_logistic_partner_detail_id')->nullable(false)->change();
    	});
    }
}
