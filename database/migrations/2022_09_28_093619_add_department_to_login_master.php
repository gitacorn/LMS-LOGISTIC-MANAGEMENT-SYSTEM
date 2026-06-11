<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentToLoginMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.LOGIN_MASTER_TABLE'), function (Blueprint $table) {
        	$table->longText('v_department')->after('v_role')->nullable();
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
