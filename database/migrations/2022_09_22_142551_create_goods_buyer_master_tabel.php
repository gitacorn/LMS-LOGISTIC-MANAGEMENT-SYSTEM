<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsBuyerMasterTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.GOODS_IN_BUYER_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_goods_in_buyer_master_no');
            $table->integer('i_buyer_company_id');
            $table->longText('v_user_company_ids');
            $table->integer('i_buyer_employee_id')->nullable();
            $table->integer('i_main_supplier_id');
            $table->longText('v_supplier_ids');
            $table->date('dt_order_date');
            $table->longText('v_po_sales_invoice_no');
            $table->integer('i_po_currency_id');
            $table->double('d_po_amount');
            $table->longText('v_brand');
            $table->enum('e_payment_status',[config('constants.NOT_PAID_PAYMENT_STATUS'),config('constants.PAID_PAYMENT_STATUS'),config('constants.PARTIAL_PAID_PAYMENT_STATUS')]);
            $table->date('dt_payment_date')->nullable();
            $table->integer('i_payment_currency_id')->nullable();
            $table->double('d_payment_amount')->nullable();
            $table->longText('v_payment_remark');
            $table->enum('e_collection_type',[config('constants.COLLECTION'),config('constants.DELIVERY')]);
            $table->enum('e_delivery_type',[config('constants.FULL_DELIVERY_TYPE'),config('constants.PARTIAL_DELIVERY_TYPE'),config('constants.CANCELLED_DELIVERY_TYPE')])->nullable();
            $table->longText('v_booking_ref_no')->nullable();
            $table->integer('i_delivery_location_id')->nullable();
            $table->date('dt_delivery_date')->nullable();
            $table->longText('v_delivery_remarks');
            $table->enum('e_customer_procedure_export',[config('constants.CONSIGNER_SUPPLIER'),config('constants.CONSIGNER_OUTSIDE')]);
            $table->enum('e_customer_procedure_import',[config('constants.CONSIGNER_SUPPLIER'),config('constants.CONSIGNER_OUTSIDE')]);
            $table->enum('e_dangerous_goods',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')]);
            $table->longText('v_goods_remarks')->nullable();
            $table->integer('i_no_boxes')->nullable();
            $table->integer('i_box_dimension_id')->nullable();
            $table->integer('i_no_palltes')->nullable();
            $table->integer('i_pallet_dimension_id')->nullable();
            $table->enum('e_pallet_type',[config('constants.STACKABLE_PALLET_TYPE'),config('constants.NOT_STACKABLE_PALLET_TYPE')])->nullable();
            $table->double('d_weight');
            $table->enum('e_weight_unit',[config('constants.KGS_WEIGHT_UNIT'),config('constants.LBD_WEIGHT_UNIT')]);
            $table->tinyInteger('t_is_active')->default('1');
            $table->tinyInteger('t_is_deleted')->default('0');
            $table->integer('i_created_id');
            $table->dateTime('dt_created_at');
            $table->integer('i_updated_id')->nullable();;
            $table->dateTime('dt_updated_at')->nullable();
            $table->integer('i_deleted_id')->nullable();
            $table->dateTime('dt_deleted_at')->nullable();
            $table->ipAddress('v_ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('constants.GOODS_IN_MASTER_TABLE'));
    }
}
