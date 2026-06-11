<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBoxPalletTypeToUsWarehouseToAmazonMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'), function (Blueprint $table) {
        	$table->enum('e_box_pallet_type', [config('constants.BOX'),config('constants.PALLET')])->after('v_remarks')->nullable();
        	$table->integer('i_total_no_of_pallets')->after('e_box_pallet_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
