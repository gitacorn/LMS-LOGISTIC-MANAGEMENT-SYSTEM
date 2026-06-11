<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsaContainerClubbingDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.USA_CONTAINER_CLUBBING_DETAIL_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_usa_container_clubbing_master_id');
        	$table->integer('i_fba_sheet_detail_id');
        	$table->enum('e_final_box_pallet_type',[config('constants.BOX'),config('constants.PALLET')])->default(config('constants.PALLET'));
        	$table->enum('e_record_type',[config('constants.USA_CONTAINER_CLUBBING_FBA_RECORD'),config('constants.USA_CONTAINER_CLUBBING_NOT_FBA_RECORD')])->default(config('constants.USA_CONTAINER_CLUBBING_FBA_RECORD'));
        	$table->integer('i_number_of_box_pallet');
        	$table->double('d_unit_pallet_box_cost');
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
        Schema::dropIfExists(config('constants.USA_CONTAINER_CLUBBING_DETAIL_TABLE'));
    }
}
