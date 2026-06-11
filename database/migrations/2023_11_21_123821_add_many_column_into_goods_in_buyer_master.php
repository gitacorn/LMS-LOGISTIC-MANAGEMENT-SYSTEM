<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManyColumnIntoGoodsInBuyerMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
            //
            $table->longText('v_vendor_number')->after('i_buyer_employee_id');
            $table->longText('v_invoice_no')->after('v_po_sales_invoice_no')->nullable();
            $table->integer('i_po_create_user_id')->after('dt_order_date');
            $table->date('dt_po_creation_date')->after('v_supplier_ids')->nullable();
            $table->double('d_po_amount_with_vat')->after('d_po_amount');
            $table->double('d_prepayment_percentage')->after('d_payment_amount')->nullable();
            $table->longText('v_user_buyer_ids')->after('v_buyer_employee_ids');
            $table->integer('i_payment_terms_id')->after('dt_payment_date');
            $table->integer('i_goods_remark_id')->after('dt_delivery_date');
            $table->integer('i_supplier_country_id')->after('v_supplier_ids')->nullable();
            $table->enum('e_customs_procedure', [config('constants.CUSTOMS_PROCEDURE_EXPORT') , config('constants.CUSTOMS_PROCEDURE_IMPORT') , config('constants.CUSTOMS_PROCEDURE_BOTH') , config('constants.CUSTOMS_PROCEDURE_DASH')])->after('i_goods_remark_id')->nullable();
            $table->integer('i_dangerous_goods_id')->after('e_customs_procedure');
            $table->longText('v_dimension_ids')->after('i_dangerous_goods_id')->nullable();
            $table->enum('e_pallet_box_type', [config('constants.PALLET') , config('constants.BOX')])->after('i_dangerous_goods_id')->nullable();
            $table->integer('i_no_of_pallet_box')->after('e_pallet_box_type');
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
            $table->dropColumn('v_vendor_number');
            $table->dropColumn('v_invoice_no');
            $table->dropColumn('i_po_create_user_id');
            $table->dropColumn('dt_po_creation_date');
            $table->dropColumn('d_po_amount_with_vat');
            $table->dropColumn('d_prepayment_percentage');
            $table->dropColumn('v_user_buyer_ids');
            $table->dropColumn('i_payment_terms_id');
            $table->dropColumn('i_goods_remark_id');
            $table->dropColumn('i_supplier_country_id');
            $table->dropColumn('e_customs_procedure');
            $table->dropColumn('i_dangerous_goods_id');
            $table->dropColumn('v_dimension_ids');
            $table->dropColumn('e_pallet_box_type');
            $table->dropColumn('i_no_of_pallet_box');
        });
    }
}
