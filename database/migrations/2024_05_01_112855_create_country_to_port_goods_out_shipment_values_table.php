<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryToPortGoodsOutShipmentValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.COUNTRY_TO_PORT_GOODS_OUT_SHIPMENT_VALUES_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_country_to_port_goods_out_master_id');
        	$table->text('v_invoice');
        	$table->decimal('d_amount');
        	$table->integer('i_currency_id');
        	$table->decimal('d_cov_rate');
        	$table->decimal('d_total_value_of_container');
        	$table->longText('v_attachment_path')->nullable();
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
        Schema::dropIfExists(config('constants.COUNTRY_TO_PORT_GOODS_OUT_SHIPMENT_VALUES_TABLE'));
    }
}