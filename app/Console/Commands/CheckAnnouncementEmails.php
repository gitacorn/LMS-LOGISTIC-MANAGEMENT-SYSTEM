<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendAnnouncementEmailJob;
use Carbon\Carbon;

class CheckAnnouncementEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcement:check-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check announcements once when application starts and send emails for events within days criteria';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking announcements for email scheduling...');
        
        $today = Carbon::now()->format('Y-m-d');
        
        // Get all active announcements with email enabled
        $announcements = DB::table('announcements')
            ->where('t_is_active', 1)
            ->where('email_enabled', 1)
            ->whereNotNull('email_schedule')
            ->get();
            
        $emailsSent = 0;
        
        foreach ($announcements as $announcement) {
            $schedule = json_decode($announcement->email_schedule, true);
            if (!is_array($schedule)) {
                continue;
            }
            
            foreach ($schedule as $checkpoint) {
                $status = $checkpoint['status'] ?? 'Pending';
                $sendDate = $checkpoint['send_date'] ?? null;
                $days = $checkpoint['days_before'] ?? null;
                
                if ($status === 'Pending' && $sendDate && $sendDate <= $today) {
                    $this->info("Dispatching email for announcement ID {$announcement->id} - {$days} days before event ({$announcement->dt_expiry_date})");
                    
                    SendAnnouncementEmailJob::dispatch($announcement->id, $days);
                    $emailsSent++;
                }
            }
        }
        
        // Log that emails were sent today (keeping compatibility with email_logs table)
        if ($emailsSent > 0) {
            DB::table('email_logs')->insert([
                'date_sent' => $today,
                'emails_count' => $emailsSent,
                'created_at' => now()
            ]);
        }
        
        $this->info("Email check completed. {$emailsSent} emails sent today.");
        
        return Command::SUCCESS;
    }
}
