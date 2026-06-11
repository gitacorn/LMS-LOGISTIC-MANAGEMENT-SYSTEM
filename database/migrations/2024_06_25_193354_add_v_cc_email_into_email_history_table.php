<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVCcEmailIntoEmailHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table(config('constants.EMAIL_HISTORY_TABLE'), function (Blueprint $table) {
    		$table->longText('v_cc_email')->after('v_received_email')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table(config('constants.EMAIL_HISTORY_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('v_cc_email');
    	});
    }
}
