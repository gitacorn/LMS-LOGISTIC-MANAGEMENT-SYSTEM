<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelatedCodeColumnIntoFbaSheetDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->integer('i_amazon_company_short_code_id')->after('e_status')->nullable();
    		$table->integer('i_customer_company_name_id')->after('e_status')->nullable();
    		$table->integer('i_amazon_location_code_id')->after('e_status')->nullable();
    		$table->integer('i_warehouse_warehouse_code_id')->after('e_status')->nullable();
    		$table->integer('i_customer_customer_code_id')->after('e_status')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
