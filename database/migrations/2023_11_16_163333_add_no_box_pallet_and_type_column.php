<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoBoxPalletAndTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
    		$table->integer('i_no_of_pallet_box')->after('dt_delivery_date')->nullable();
    		$table->enum('e_dimension',[config('constants.BOX'),config('constants.PALLET')])->after('i_no_of_pallet_box')->nullable();
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
    	Schema::table(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('i_no_of_pallet_box');
    		$table->dropColumn('e_dimension');
    	});
    }
}
