<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticLatestStatusIntoGoodsInBuyerDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->longText('v_latest_logistic_status_id')->after('e_logistic_record_status')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_DETAIL_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
