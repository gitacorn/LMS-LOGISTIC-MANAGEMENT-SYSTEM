<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToUsWarehouseToAmazonDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
        	$table->date('dt_booking_date')->after('v_uk_box_pallet')->nullable();
        	$table->date('dt_collection_date')->after('dt_booking_date')->nullable();
        	$table->date('dt_delivery_date')->after('dt_collection_date')->nullable();
        	$table->longText('v_remarks')->after('dt_delivery_date')->nullable();
        	$table->longText('v_tracking_link')->after('v_remarks')->nullable();
        	$table->date('dt_amazon_appointment_date')->after('v_tracking_link')->nullable();
        	$table->longText('v_amazon_appointment_id')->after('dt_amazon_appointment_date')->nullable();
        	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
