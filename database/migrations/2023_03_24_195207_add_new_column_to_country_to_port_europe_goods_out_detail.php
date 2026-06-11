<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToCountryToPortEuropeGoodsOutDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
        	$table->date('dt_booking_date')->after('v_price')->nullable();
        	$table->date('dt_collection_date')->after('dt_booking_date')->nullable();
        	$table->date('dt_delivery_date')->after('dt_collection_date')->nullable();
        	$table->longText('v_tracking_link')->after('dt_delivery_date')->nullable();
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
        Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
