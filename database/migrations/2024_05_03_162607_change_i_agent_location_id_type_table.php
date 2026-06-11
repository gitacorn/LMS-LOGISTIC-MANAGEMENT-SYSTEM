<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIAgentLocationIdTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->integer('i_agent_location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->longText('i_agent_location_id')->nullable()->change();
        });
    }
}
