<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPalletBoxDimensionIdToGoodsInBuyerMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
        	$table->longText('v_buyer_employee_ids')->after('v_user_company_ids');
           $table->longText('v_box_dimension_ids')->after('i_no_boxes')->nullable();
           $table->longText('v_pallet_dimension_ids')->after('i_no_palltes')->nullable();
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
