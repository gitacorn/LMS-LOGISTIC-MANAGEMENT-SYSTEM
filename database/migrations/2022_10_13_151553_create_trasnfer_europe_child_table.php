<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrasnferEuropeChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_europe_transfer_master_id');
            $table->longText('v_invoice_ref_no');
            $table->integer('i_account_company_id');
            $table->integer('i_warehouse_id');
            $table->integer('i_location_id');
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
        Schema::dropIfExists(config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE'));
    }
}
