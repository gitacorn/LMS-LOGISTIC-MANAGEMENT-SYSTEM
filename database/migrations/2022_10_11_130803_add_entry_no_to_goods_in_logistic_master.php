<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntryNoToGoodsInLogisticMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
    		$table->longText('v_goods_in_logistic_master_no')->after('i_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
