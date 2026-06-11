<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEUsaContainerClubbingStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), function (Blueprint $table) {
            $table->enum('e_usa_container_clubbing_status',[config('constants.PENDING_STATUS'),config('constants.COMPLETED_STATUS')])->after('e_status')->default(config('constants.PENDING_STATUS'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), function (Blueprint $table) {
        	$table->dropColumn('e_usa_container_clubbing_status');
        });
    }
}
