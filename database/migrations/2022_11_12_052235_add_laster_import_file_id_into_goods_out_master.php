<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLasterImportFileIdIntoGoodsOutMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
    		$table->integer('i_lastet_import_file_id')->nullable();
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
