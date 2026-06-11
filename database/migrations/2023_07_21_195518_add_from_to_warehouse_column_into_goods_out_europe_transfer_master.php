<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromToWarehouseColumnIntoGoodsOutEuropeTransferMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_OUT_EUROPE_TRANSFER_MASTER_TABLE'), function (Blueprint $table) {
            //
            $table->integer('i_from_warehouse_id')->after('d_weight');
            $table->integer('i_to_warehouse_id')->after('i_from_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_OUT_EUROPE_TRANSFER_MASTER_TABLE'), function (Blueprint $table) {
            //
            $table->dropColumn('i_from_warehouse_id');
            $table->dropColumn('i_to_warehouse_id');
        });
    }
}
