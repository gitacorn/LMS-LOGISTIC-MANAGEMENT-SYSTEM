<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogisticPartnerIdToCountryToPortGoodsOutInvoiceMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            $table->integer('i_logistic_partner_master_id')->after('i_country_to_port_goods_out_master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
