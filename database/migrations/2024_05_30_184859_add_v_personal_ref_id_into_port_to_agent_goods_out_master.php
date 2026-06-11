<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVPersonalRefIdIntoPortToAgentGoodsOutMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
    		$table->text('v_personal_ref')->after('i_warehouse_id')->nullable();
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
    		$table->dropColumn('v_personal_ref');
    	});
    }
}
