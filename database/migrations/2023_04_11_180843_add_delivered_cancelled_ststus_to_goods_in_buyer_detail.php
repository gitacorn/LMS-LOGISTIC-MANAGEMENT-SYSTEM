<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveredCancelledStstusToGoodsInBuyerDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_DETAIL_TABLE'), function (Blueprint $table) {
            $table->tinyInteger('t_is_all_delivered_cancelled_ststus')->after('v_latest_logistic_status_id')->default('0');
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
