<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGbpConversationRateToCurrencyMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.CURRENCY_MASTER_TABLE'), function (Blueprint $table) {
        	$table->decimal('d_gbp_conversation_rate')->after('v_currency_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.CURRENCY_MASTER_TABLE'), function (Blueprint $table) {
        	$table->dropColumn('d_gbp_conversation_rate');
        });
    }
}
