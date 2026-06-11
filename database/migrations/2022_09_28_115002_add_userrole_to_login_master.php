<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserroleToLoginMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.LOGIN_MASTER_TABLE'), function (Blueprint $table) {
        	$table->longText('v_record_type')->after('v_department')->nullable();
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
