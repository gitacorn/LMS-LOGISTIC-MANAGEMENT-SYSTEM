<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticReferenceColum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.GOODS_IN_LOGISTIC_COLLECTION_TABLE'), function (Blueprint $table) {
    		$table->integer('i_goods_in_buyer_detail_id')->after('i_goods_in_logistic_master_id');
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
