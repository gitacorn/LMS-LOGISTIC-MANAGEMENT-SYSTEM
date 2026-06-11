<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactInfoIntoLosisticPartnerMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.LOGISTIC_PARTNER_MASTER_TABLE'), function (Blueprint $table) {
    		$table->longText('v_partner_contact_person_names')->after('v_partner_country_ids')->nullable();
    		$table->longText('v_partner_contact_emails')->after('v_partner_country_ids')->nullable();
    		$table->longText('v_partner_contact_mobiles')->after('v_partner_country_ids')->nullable();
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
