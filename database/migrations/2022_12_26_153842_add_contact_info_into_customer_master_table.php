<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactInfoIntoCustomerMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	
    	Schema::table(config('constants.CUSTOMER_MASTER_TABLE'), function (Blueprint $table) {
    		$table->longText('v_customer_codes')->after('v_customer_name')->nullable();
    		$table->longText('v_customer_address')->after('v_customer_name')->nullable();
    		$table->longText('v_customer_country_ids')->after('v_customer_name')->nullable();
    		$table->longText('v_customer_contact_person_names')->after('v_customer_name')->nullable();
    		$table->longText('v_customer_contact_emails')->after('v_customer_name')->nullable();
    		$table->longText('v_customer_contact_mobiles')->after('v_customer_name')->nullable();
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
