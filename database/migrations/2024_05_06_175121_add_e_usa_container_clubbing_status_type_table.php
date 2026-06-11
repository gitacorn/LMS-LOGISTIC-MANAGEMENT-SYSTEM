<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEUsaContainerClubbingStatusTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), function (Blueprint $table) {
        	$table->enum('e_usa_container_clubbing_status',[config('constants.PENDING_STATUS'),config('constants.COMPLETED_STATUS')])->after('i_total_no_of_pallets')->default(config('constants.PENDING_STATUS'));
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
        	$table->dropColumn('e_usa_container_clubbing_status');
        });
    }
}
