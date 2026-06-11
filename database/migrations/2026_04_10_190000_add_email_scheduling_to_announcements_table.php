<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailSchedulingToAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->json('email_schedule')->nullable()->after('v_marquee_text');
            $table->boolean('email_enabled')->default(false)->after('email_schedule');
            $table->string('email_subject')->nullable()->after('email_enabled');
            $table->text('email_message')->nullable()->after('email_subject');
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
            $table->dropColumn(['email_schedule', 'email_enabled', 'email_subject', 'email_message']);
        });
    }
}
