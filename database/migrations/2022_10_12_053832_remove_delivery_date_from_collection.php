<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeliveryDateFromCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_COLLECTION_TABLE'), function (Blueprint $table) {
            $table->dropColumn('dt_collection_delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_COLLECTION_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
