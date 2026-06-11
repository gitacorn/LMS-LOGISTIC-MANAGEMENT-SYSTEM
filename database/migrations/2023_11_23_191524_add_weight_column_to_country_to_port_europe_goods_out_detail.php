<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightColumnToCountryToPortEuropeGoodsOutDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
        	$table->decimal('d_weight')->after('v_price');
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
        	$table->dropColumn('d_weight');
        });
    }
}
