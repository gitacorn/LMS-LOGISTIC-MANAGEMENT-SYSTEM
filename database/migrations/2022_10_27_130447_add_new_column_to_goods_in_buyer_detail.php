<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToGoodsInBuyerDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.GOODS_IN_BUYER_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->tinyInteger('t_in_use')->default('0')->after('e_buyer_record_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_in_buyer_detail', function (Blueprint $table) {
            //
        });
    }
}
