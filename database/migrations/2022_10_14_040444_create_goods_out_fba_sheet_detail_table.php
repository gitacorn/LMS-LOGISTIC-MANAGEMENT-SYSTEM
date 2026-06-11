<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsOutFbaSheetDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_fba_sheet_master_id');
            $table->longText('v_fba_po_no');
            $table->enum('e_destination',[config('constants.AMAZON_FBA_SHEET'),config('constants.CUSTOMER_FBA_SHEET'),config('constants.WAREHOUSE_FBA_SHEET')]);
            $table->longText('v_ref_id');
            $table->longText('v_company_code');
            $table->integer('i_company_id');
            $table->longText('v_location_code');
            $table->integer('i_warehouse_location_id');
            $table->longText('v_product');
            $table->longText('v_sku')->nullable();;
            $table->longText('v_units')->nullable();;
            $table->longText('v_amazon_address')->nullable();;
            $table->integer('i_boxes_units')->nullable();;
            $table->longText('v_boxes')->nullable();;
            $table->longText('v_pallet')->nullable();;
            $table->integer('i_total_no_of_pallets')->nullable();;
            $table->longText('v_pallet_dimension')->nullable();;
            $table->integer('v_pallet_weight')->nullable();;
            $table->integer('i_pallet_no')->nullable();;
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
        Schema::dropIfExists(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'));
    }
}
