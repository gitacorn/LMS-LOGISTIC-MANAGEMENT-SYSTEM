<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseTypeAndOwnWarehouseLocationColumnToPortToAgentGoodsOutMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->enum('e_warehose_type',[config('constants.OWN_WAREHOUSE_TYPE'),config('constants.AGENT_WAREHOUSE_TYPE')])->default(config('constants.AGENT_WAREHOUSE_TYPE'))->after('i_logistic_partner_detail_id');
        	$table->integer('i_own_warehouse_location_id')->after('e_warehose_type')->nullable();
        	$table->longText('i_agent_location_id')->nullable()->change();
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
        	$table->dropColumn('e_warehose_type');
        	$table->dropColumn('i_own_warehouse_location_id');
        	$table->integer('i_agent_location_id')->nullable(false)->change();
        });
    }
}
