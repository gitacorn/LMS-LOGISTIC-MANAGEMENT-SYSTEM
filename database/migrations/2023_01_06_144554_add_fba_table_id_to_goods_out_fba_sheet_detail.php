<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFbaTableIdToGoodsOutFbaSheetDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), function (Blueprint $table) {
            $table->integer('ref_table_id')->after('i_amazon_company_short_code_id')->nullable();
            $table->enum('ref_record_type',[config('constants.US_WAREHOUSE_TO_AMAZON'),config('constants.US_WAREHOUSE_TO_UK_WAREHOUSE'),config('constants.US_WAREHOUSE_TO_CUSTOMER'),config('constants.WAREHOUSE_TO_AMAZON'),config('constants.INTERNAL_WAREHOUSE_TRANSFER')])->after('ref_table_id')->nullable();
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
            //
        });
    }
}
