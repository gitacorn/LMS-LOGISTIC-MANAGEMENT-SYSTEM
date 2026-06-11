<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticPartnerIdToGoodsInLogisticInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_INVOICE_TABLE'), function (Blueprint $table) {
            $table->integer('i_logistic_partner_master_id')->after('i_goods_in_logistic_master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_INVOICE_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
