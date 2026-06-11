<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefNoPortToAgentGoodOutMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'), function (Blueprint $table) {
            $table->text('v_ref_no')->nullable()->after('dt_booking_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_good_out_master', function (Blueprint $table) {
            //
        });
    }
}
