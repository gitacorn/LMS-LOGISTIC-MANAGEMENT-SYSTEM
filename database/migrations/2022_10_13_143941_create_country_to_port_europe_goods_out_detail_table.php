<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryToPortEuropeGoodsOutDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_country_to_port_europe_goods_master_id');
            $table->longText('v_shipment_id');
            $table->longText('v_ref_id');
            $table->integer('i_account_company_id');
            $table->integer('i_warehouse_id');
            $table->integer('i_location_id');
            $table->longText('v_sku');
            $table->longText('v_units');
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
        Schema::dropIfExists(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'));
    }
}
