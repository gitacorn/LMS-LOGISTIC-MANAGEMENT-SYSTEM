<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryDateIntoLogistic extends Migration
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
    		$table->date('dt_delivery_date')->nullable()->after('dt_collection_date');
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
