<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticPartnerIdToAgentToWarehouseGoodsOutInvoiceMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            $table->integer('i_logistic_partner_master_id')->after('i_agent_to_warehouse_goods_out_master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
