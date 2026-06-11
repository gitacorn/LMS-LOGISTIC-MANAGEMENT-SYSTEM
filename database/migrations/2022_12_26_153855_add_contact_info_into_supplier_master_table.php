<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactInfoIntoSupplierMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.SUPPLIER_MASTER_TABLE'), function (Blueprint $table) {
    		$table->longText('v_supplier_codes')->after('v_supplier_name')->nullable();
    		$table->longText('v_supplier_address')->after('v_supplier_name')->nullable();
    		$table->longText('v_supplier_country_ids')->after('v_supplier_name')->nullable();
    		$table->longText('v_supplier_contact_person_names')->after('v_supplier_name')->nullable();
    		$table->longText('v_supplier_contact_emails')->after('v_supplier_name')->nullable();
    		$table->longText('v_supplier_contact_mobiles')->after('v_supplier_name')->nullable();
    		
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
