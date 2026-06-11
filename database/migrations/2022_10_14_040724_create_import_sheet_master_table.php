<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportSheetMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_country_to_port_goods_out_master_id');
            $table->longText('v_file_name');
            $table->longText('v_file_path');
            $table->longText('v_response')->nullable();
            $table->enum('e_status',[config('constants.FAILED_STATUS'),config('constants.PENDING_STATUS'),config('constants.PROCESSING_STATUS'),config('constants.SUCCESS_STATUS')]);
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
        Schema::dropIfExists(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'));
    }
}
