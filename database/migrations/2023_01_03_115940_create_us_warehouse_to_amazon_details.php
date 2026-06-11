<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsWarehouseToAmazonDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_us_warehouse_to_amazon_master_id');
            $table->longText('v_shipment_id')->nullable();
            $table->longText('v_ref_id')->nullable();
            $table->integer('i_account_company_id')->nullable();
            $table->integer('i_amazon_from_warehouse_id')->nullable();
            $table->integer('i_to_amazon_location_id')->nullable();
            $table->longText('v_sku')->nullable();
            $table->longText('v_units')->nullable();
            $table->decimal('d_price')->nullable();
            $table->longText('v_shipment_invoice_no')->nullable();
            $table->integer('i_customer_id')->nullable();
            $table->integer('i_customer_from_warehouse_id')->nullable();
            $table->integer('i_to_customer_id')->nullable();
            $table->longText('v_customer_unit')->nullable();
            $table->longText('v_box_pallet')->nullable();
            $table->longText('v_invoice_no_ref_no')->nullable();
            $table->integer('i_uk_account_id')->nullable();
            $table->integer('i_uk_from_warehouse_id')->nullable();
            $table->integer('i_uk_to_warehouse_id')->nullable();
            $table->longText('v_uk_unit')->nullable();
            $table->longText('v_uk_box_pallet')->nullable();
            $table->tinyInteger('t_is_active')->default('1');
            $table->tinyInteger('t_is_deleted')->default('0');
            $table->integer('i_created_id');
            $table->dateTime('dt_created_at');
            $table->integer('i_updated_id')->nullable();
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
        Schema::dropIfExists(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'));
    }
}
