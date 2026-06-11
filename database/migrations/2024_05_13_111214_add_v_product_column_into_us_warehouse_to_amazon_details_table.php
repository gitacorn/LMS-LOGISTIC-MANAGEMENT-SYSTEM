<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVProductColumnIntoUsWarehouseToAmazonDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
        	$table->text('v_product')->after('i_to_amazon_location_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
        	$table->dropColumn('v_product');
        });
    }
}
