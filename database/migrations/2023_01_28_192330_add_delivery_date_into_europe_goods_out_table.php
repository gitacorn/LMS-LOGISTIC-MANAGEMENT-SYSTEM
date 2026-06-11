<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryDateIntoEuropeGoodsOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
    		$table->date('dt_delivery_date')->before('t_is_active')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
