<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToLoginMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.LOGIN_MASTER_TABLE'), function (Blueprint $table) {
            $table->integer('i_warehouse_id')->after('v_record_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.LOGIN_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
