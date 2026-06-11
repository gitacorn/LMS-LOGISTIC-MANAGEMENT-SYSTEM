<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticPartnerIdToGoodsOutEuropeTransferInvoiceMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            $table->integer('i_logistic_partner_master_id')->after('i_europe_transfer_master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
