<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIWorkflowIdIntoCountryToPortEuropeGoodsOutDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
        	//$table->longText('v_price')->nullable(false)->change();
        	$table->longText('v_workflow_id')->after('i_country_to_port_europe_goods_master_id');
        	$table->integer('i_no_of_pallet_box')->after('v_price');
        	$table->enum('e_dimension',[config('constants.BOX'),config('constants.PALLET')])->after('i_no_of_pallet_box')->nullable();
        	$table->integer('i_to_country_cell_id')->after('i_location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), function (Blueprint $table) {
    		//$table->longText('v_price')->nullable()->change();
    		$table->dropColumn('v_workflow_id');
    		$table->dropColumn('i_no_of_pallet_box');
    		$table->dropColumn('e_dimension');
    		$table->dropColumn('i_to_country_cell_id');
        });
    }
}
