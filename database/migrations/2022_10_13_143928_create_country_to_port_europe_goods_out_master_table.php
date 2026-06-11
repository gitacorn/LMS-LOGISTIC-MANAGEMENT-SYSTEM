<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryToPortEuropeGoodsOutMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_country_to_port_europe_record_no');
            $table->enum('e_transport_way',[config('constants.ROAD_TRANSPORT'),config('constants.TRUCK_TRANSPORT'),config('constants.AIR_TRANSPORT'),config('constants.SEA_TRANSPORT')]);
            $table->integer('i_book_by_employee_id');
            $table->integer('i_logistic_partner_detail_id');
            $table->date('dt_booking_date');
            $table->date('dt_collection_date')->nullable();
            $table->longText('v_tracking_no');
            $table->longText('v_tracking_link')->nullable();
            $table->date('dt_amazon_shipment_date')->nullable();
            $table->longText('v_amazon_appointment_id')->nullable();
            $table->integer('i_status_id');
            $table->longText('v_status_comment')->nullable();
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
        Schema::dropIfExists(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_MASTER_TABLE'));
    }
}
