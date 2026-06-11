<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventStartDateAndWarehouseIdsToAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->date('dt_event_start_date')->nullable()->after('v_announcement_text');
            $table->json('warehouse_ids')->nullable()->after('dt_expiry_date');
            $table->text('v_marquee_text')->nullable()->after('warehouse_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['dt_event_start_date', 'warehouse_ids', 'v_marquee_text']);
        });
    }
}
