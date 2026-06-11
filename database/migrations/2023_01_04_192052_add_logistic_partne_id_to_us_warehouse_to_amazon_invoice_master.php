<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticPartneIdToUsWarehouseToAmazonInvoiceMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
        	$table->integer('i_logistic_partner_master_id')->after('i_us_warehouse_to_amazon_master_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
