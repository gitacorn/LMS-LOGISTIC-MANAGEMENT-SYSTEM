<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingListToGoodsInBuyerMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
           $table->enum('e_packing_status', [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->after('i_delivery_location_id')->nullable();
        	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
