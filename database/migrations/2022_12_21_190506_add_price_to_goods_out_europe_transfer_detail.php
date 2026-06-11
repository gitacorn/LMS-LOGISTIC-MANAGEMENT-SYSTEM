<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToGoodsOutEuropeTransferDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE'), function (Blueprint $table) {
          $table->longText('v_price')->after('v_units')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
