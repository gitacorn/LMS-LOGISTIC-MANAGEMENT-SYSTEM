<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegesteredTypeToSupplierDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.SUPPLIER_DETAIL_TABLE'), function (Blueprint $table) {
        	$table->enum('e_record_status', [config('constants.REGISTERED_STATUS'),config('constants.COLLECTION')])->after('v_contact_person_name')->nullable();
        	$table->longText('v_timings')->after('e_record_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.SUPPLIER_DETAIL_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
