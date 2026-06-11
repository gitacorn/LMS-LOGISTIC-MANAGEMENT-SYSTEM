<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsaContainerClubbingMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->longText('v_usa_container_clubbing_record_no');
        	$table->enum('e_type',[config('constants.AMAZON_FBA_SHEET'),config('constants.CUSTOMER_FBA_SHEET')]);
        	$table->integer('i_from_warehouse_id');
        	$table->integer('i_to_location_id');
        	$table->longText('v_box_pallet_type');
        	$table->enum('e_final_box_pallet_type',[config('constants.BOX'),config('constants.PALLET')]);
        	$table->integer('d_total_boxes')->nullable();
        	$table->integer('d_total_pallets')->nullable();
        	$table->date('dt_booking_date');
        	$table->integer('i_booking_portal_id')->nullable();
        	$table->integer('i_carrier_company_id');
        	$table->longText('v_tracking_no')->nullable();
        	$table->longText('v_pro_number')->nullable();
        	$table->double('d_logistic_cost_in_usd');
        	$table->date('dt_collection_date');
        	$table->date('dt_delivery_date')->nullable();
        	$table->double('d_weight')->nullable();
        	$table->longText('v_comments')->nullable();
        	$table->longText('v_fba_container_ids')->nullable();
        	$table->longText('v_not_fba_container_ids')->nullable();
        	$table->double('d_unit_pallet_box_cost');
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
        Schema::dropIfExists(config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE'));
    }
}
