<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuyerCommentsActualPaymentDateInvoiceDateAndNullableGrossNetWeightIntoGoodinbuyermaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
    		$table->decimal('d_weight')->nullable()->change();
    		$table->decimal('d_net_weight')->nullable()->change();
    		$table->longText('v_buyer_comments')->after('i_total_units')->nullable();
    		$table->date('dt_actual_payment_date')->after('dt_payment_date')->nullable();
    		$table->date('dt_invoice_date')->after('dt_order_date')->nullable();
    	});
    	Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
    		$table->date('dt_goods_in_date')->after('v_insurance_comment')->nullable();
    	});
    	Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->longText('i_packing_warehouse_id')->after('i_account_company_id');
    	});
    	Schema::table(config('constants.COMPANY_MASTER_TABLE'), function (Blueprint $table) {
    		$table->longText('v_email')->after('v_company_short_code')->nullable();
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
    		$table->decimal('d_weight')->nullable(false)->change();
    		$table->decimal('d_net_weight')->nullable(false)->change();
    		$table->dropColumn('v_buyer_comments');
    		$table->dropColumn('dt_actual_payment_date');
    		$table->dropColumn('dt_invoice_date');
    	});
    	Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('dt_goods_in_date');
    	});
    	Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('i_packing_warehouse_id');
    	});
    	Schema::table(config('constants.COMPANY_MASTER_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('v_email');
    	});
    }
}
