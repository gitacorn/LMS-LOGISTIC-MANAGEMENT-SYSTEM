<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsWarehouseToAmazonMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->enum('e_transport_way',[config('constants.AIR_TRANSPORT'),config('constants.SEA_TRANSPORT'),config('constants.TRUCK_TRANSPORT'),config('constants.ROAD_TRANSPORT')]);
            $table->integer('i_from_warehouse_id');
            $table->enum('e_to_location',[config('constants.AMAZON_FBA_SHEET'),config('constants.CUSTOMER_FBA_SHEET'),config('constants.UK_WAREHOUSE_FBA_SHEET')]);
            $table->integer('i_book_by_employee_id');
            $table->integer('i_logistic_partner_detail_id');
            $table->date('dt_booking_date');
            $table->date('dt_collection_date')->nullable();
            $table->longText('v_remarks')->nullable();
            $table->longText('v_tracking_no');
            $table->longText('v_tracking_link')->nullable();
            $table->date('dt_amazon_appointment_date')->nullable();
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
        Schema::dropIfExists(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'));
    }
}
