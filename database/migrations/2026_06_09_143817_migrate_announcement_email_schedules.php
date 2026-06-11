<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateAnnouncementEmailSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $announcements = DB::table('announcements')->get();
        $today = Carbon::now()->format('Y-m-d');
        $offsets = [31, 24, 17, 10, 3];

        foreach ($announcements as $announcement) {
            // Check if it's already in the new format (JSON array of objects containing 'days_before')
            $existingSchedule = json_decode($announcement->email_schedule, true);
            $isNewFormat = false;
            if (is_array($existingSchedule) && !empty($existingSchedule)) {
                $first = reset($existingSchedule);
                if (is_array($first) && isset($first['days_before'])) {
                    $isNewFormat = true;
                }
            }

            if (!$isNewFormat) {
                $schedule = [];
                $eventDate = Carbon::parse($announcement->dt_expiry_date);
                
                foreach ($offsets as $days) {
                    $sendDate = $eventDate->copy()->subDays($days)->format('Y-m-d');
                    // If the schedule day is in the past, set status as Skipped, otherwise Pending
                    $status = ($sendDate < $today) ? 'Skipped' : 'Pending';
                    
                    $schedule[] = [
                        'days_before' => $days,
                        'send_date' => $sendDate,
                        'status' => $status,
                        'sent_at' => null
                    ];
                }

                DB::table('announcements')
                    ->where('id', $announcement->id)
                    ->update([
                        'email_schedule' => json_encode($schedule)
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot easily restore old checkbox states, but we can set them to empty JSON
        DB::table('announcements')->update(['email_schedule' => null]);
    }
}
