<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsInLogisticMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_supplier_id');
            $table->enum('e_logistic_collection_type',[config('constants.COLLECTION'),config('constants.DELIVERY')]);
            
            $table->integer('i_goods_in_buyer_master_id')->nullable();
            $table->longText('i_goods_in_buyer_detail_id')->nullable();
            $table->enum('e_logistic_delivery_type',[config('constants.FULL_DELIVERY_TYPE'),config('constants.PARTIAL_DELIVERY_TYPE'),config('constants.CANCELLED_DELIVERY_TYPE')])->nullable();
            
            $table->integer('i_book_employee_id')->nullable();
            $table->integer('i_logistic_partner_id')->nullable();
            $table->date('dt_collection_date')->nullable();
            $table->longText('v_booking_ref_no')->nullable();
            
            $table->longText('v_tracking_no');
            $table->longText('v_tracking_link')->nullable();
            $table->enum('e_insurance_status',[config('constants.IN_HOUSE_INSURANCE_STATUS'),config('constants.THIRD_PARTY_INSURANCE_STATUS')])->nullable();
            $table->longText('v_insurance_comment')->nullable();
            
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
        Schema::dropIfExists(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'));
    }
}
