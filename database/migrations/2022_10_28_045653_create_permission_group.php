<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.PERMISSION_GROUP_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_module_id');
            $table->longText('v_group_name');
            $table->integer('i_sequence')->nullable();
            $table->tinyInteger('t_is_active')->default('1');
            $table->tinyInteger('t_is_deleted')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('constants.PERMISSION_GROUP_TABLE'));
    }
}
