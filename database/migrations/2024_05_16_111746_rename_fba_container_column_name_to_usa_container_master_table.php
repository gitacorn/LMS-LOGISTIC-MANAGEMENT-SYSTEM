<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFbaContainerColumnNameToUsaContainerMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE'), function (Blueprint $table) {
        	$table->renameColumn('v_fba_container_ids', 'v_fba_sheet_ids');
        	$table->renameColumn('v_not_fba_container_ids', 'v_usa_warehouse_container_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE'), function (Blueprint $table) {
        	$table->renameColumn('v_fba_sheet_ids', 'v_fba_container_ids');
        	$table->renameColumn('v_usa_warehouse_container_ids', 'v_not_fba_container_ids');
        });
    }
}
