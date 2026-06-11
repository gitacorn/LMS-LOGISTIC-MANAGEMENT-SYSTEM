<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactColumnIntocustomerDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.CUSTOMER_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->longText('v_contact_person_name')->after('v_customer_address')->nullable();
    		$table->longText('v_contact_email')->after('v_customer_address')->nullable();
    		$table->longText('v_contact_mobile')->after('v_customer_address')->nullable();
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
