<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsOutMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_country_to_port_record_no');
            $table->enum('e_transport_way',[config('constants.AIR_TRANSPORT'),config('constants.SEA_TRANSPORT')]);
            $table->integer('i_transport_from_id');
            $table->integer('i_transport_to_id');
            $table->integer('i_book_by_employee_id');
            $table->integer('i_logistic_partner_detail_id');
            $table->longText('v_container_air_waybill_no');
            $table->longText('v_seal_house_waybill_no');
            $table->date('dt_est_dispatch_date');
            $table->date('dt_est_port_arrival_date');
            $table->integer('i_goods_out_currency_id');
            $table->double('d_payment_value')->nullable();
            $table->integer('i_total_pallets');
            $table->enum('e_dangerous_goods',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')]);
            $table->longText('v_goods_remark');
            $table->longText('v_tracking_no');
            $table->longText('v_tracking_link')->nullable();
            $table->enum('e_insurance_status',[config('constants.IN_HOUSE_INSURANCE_STATUS'),config('constants.THIRD_PARTY_INSURANCE_STATUS')]);
            $table->integer('i_status_id');
            $table->longText('v_status_comment')->nullable();
            $table->enum('e_process_status',[config('constants.COMPLETED_STATUS'),config('constants.PENDING_STATUS')])->default(config('constants.PENDING_STATUS'));
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
        Schema::dropIfExists(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'));
    }
}
