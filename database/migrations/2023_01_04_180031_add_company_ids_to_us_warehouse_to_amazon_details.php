<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdsToUsWarehouseToAmazonDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
            $table->longText('v_uk_account_ids')->after('i_uk_account_id')->nullable();
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
            //
        });
    }
}
