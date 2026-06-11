<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrasnferEuropeInvoiceMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_europe_transfer_master_id');
            $table->longText('v_invoice_name');
            $table->longText('v_invoice_no');
            $table->double('d_freight_charge')->nullable();
            $table->double('d_custom_charge')->nullable();
            $table->double('d_duty_charge')->nullable();
            $table->double('d_other_charge')->nullable();
            $table->double('d_vat_charge')->nullable();
            $table->double('d_total_charge')->nullable();
            $table->double('d_conversion_rate')->nullable();
            $table->double('d_final_charge')->nullable();
            $table->integer('i_invoice_currency_id');
            $table->longText('v_invoice_file_path')->nullable();
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
        Schema::dropIfExists(config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE'));
    }
}
