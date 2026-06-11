<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnInWarehousePalletMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.WAREHOUSE_PALLET_MASTER_TABLE'), function (Blueprint $table) {
        	$table->integer('i_pallet_limit')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.WAREHOUSE_PALLET_MASTER_TABLE'), function (Blueprint $table) {
            $table->integer('i_pallet_limit')->nullable(false)->change();
        });
    }
}
