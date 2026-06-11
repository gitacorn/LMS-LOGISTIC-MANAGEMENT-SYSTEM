<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceToGoodsInBuyerMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
        	$table->enum('e_pickup_reference', [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->after('e_net_weight_unit')->nullable();
        	$table->longText('v_reference')->after('e_pickup_reference')->nullable();
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
