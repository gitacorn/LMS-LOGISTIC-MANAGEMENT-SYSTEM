<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordTypeToGoodsInBuyerDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(config('constants.GOODS_IN_BUYER_DETAIL_TABLE'), function (Blueprint $table) {
       	$table->enum('e_buyer_record_status',[config('constants.FULL_DELIVERY_TYPE'),config('constants.PARTIAL_DELIVERY_TYPE'),config('constants.CANCELLED_DELIVERY_TYPE')])->after('i_goods_in_buyer_supplier_id');
       	$table->enum('e_logistic_record_status',[config('constants.FULL_DELIVERY_TYPE'),config('constants.PARTIAL_DELIVERY_TYPE'),config('constants.CANCELLED_DELIVERY_TYPE')])->after('e_buyer_record_status')->nullable();
       	 
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
