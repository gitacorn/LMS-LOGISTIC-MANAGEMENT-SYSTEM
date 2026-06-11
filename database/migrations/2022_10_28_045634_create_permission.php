<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.PERMISSION_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_group_id');
            $table->longText('v_name');
            $table->tinyInteger('t_sort')->default('1');
            $table->longText('v_title');
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
        Schema::dropIfExists(config('constants.PERMISSION_TABLE'));
    }
}
