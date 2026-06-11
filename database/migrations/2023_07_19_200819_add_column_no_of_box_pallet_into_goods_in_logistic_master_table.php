<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNoOfBoxPalletIntoGoodsInLogisticMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
            //
        	$table->integer('i_no_boxes')->after('dt_delivery_date')->nullable();
        	$table->integer('i_no_palltes')->after('i_no_boxes')->nullable();
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
            $table->dropColumn('i_no_boxes');
            $table->dropColumn('i_no_palltes');
        });
    }
}
