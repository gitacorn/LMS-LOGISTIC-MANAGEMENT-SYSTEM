<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnsIntoCountryToPortGoodsOutMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->text('v_personal_ref')->after('e_insurance_status');
        	$table->integer('i_from_warehouse_country_id')->after('v_personal_ref');
        	$table->integer('i_warehouse_id')->after('i_from_warehouse_country_id');
        	$table->date('dt_pick_up_date_from_warehouse')->after('i_warehouse_id')->nullable();
        	$table->longText('v_comments')->after('dt_pick_up_date_from_warehouse')->nullable();
        	$table->text('v_booking_ref')->after('v_comments');
        	$table->integer('d_total_value_of_container')->after('v_booking_ref');
        	$table->date('dt_arrival_date_at_usa_port')->after('d_total_value_of_container')->nullable();
        });
        
        Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->integer('i_from_warehouse_country_id')->after('dt_delivery_date')->nullable();
        	$table->integer('i_warehouse_id')->after('i_from_warehouse_country_id')->nullable();
        });
        
    	Schema::table(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
    		$table->integer('i_to_warehouse_id')->after('v_container_ids')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->dropColumn('v_personal_ref');
        	$table->dropColumn('i_from_warehouse_country_id');
        	$table->dropColumn('i_warehouse_id');
        	$table->dropColumn('dt_pick_up_date_from_warehouse');
        	$table->dropColumn('v_comments');
        	$table->dropColumn('v_booking_ref');
        	$table->dropColumn('d_total_value_of_container');
        	$table->dropColumn('dt_arrival_date_at_usa_port');
        });
        
        Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->dropColumn('i_from_warehouse_country_id');
        	$table->dropColumn('i_warehouse_id');
        });
        
        Schema::table(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
        	$table->dropColumn('i_to_warehouse_id');
		});
    }
}