<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Helpers\Twt\Wild_tiger;

class SendAnnouncementEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $announcementId;
    protected $days;

    public function __construct($announcementId, $days)
    {
        $this->announcementId = $announcementId;
        $this->days = $days;
    }

    public function handle()
    {
        $announcement = \App\Announcement::find($this->announcementId);
        if (!$announcement || !$announcement->t_is_active || !$announcement->email_enabled) {
            return;
        }
        // Get warehouse names
        $warehouseNames = [];
        $warehouseIds = $announcement->warehouse_ids;
        
        // Ensure warehouse_ids is an array
        if (is_string($warehouseIds)) {
            $warehouseIds = json_decode($warehouseIds, true);
        }
        
        if (!empty($warehouseIds) && is_array($warehouseIds)) {
            foreach ($warehouseIds as $encodedId) {
                $decodedId = Wild_tiger::decode($encodedId);
                $warehouse = DB::table('warehouse_master')
                    ->where('i_id', $decodedId)
                    ->where('e_record_type', 'Warehouse')
                    ->first();
                if ($warehouse) {
                    $warehouseNames[] = $warehouse->v_warehouse_name;
                }
            }
        }
        
        $warehouseText = !empty($warehouseNames) ? implode(', ', $warehouseNames) : '';
        $eventDate = date('d-m-Y', strtotime($announcement->dt_expiry_date));
        $dayName = date('l', strtotime($announcement->dt_expiry_date));
        
        // Prepare email content - use dynamic format from announcement record
        $emailSubject = str_replace(
            ['[Eventdate]', '[Day]'],
            [$eventDate, $dayName],
            $announcement->email_subject
        );
        
        $emailMessage = str_replace(
            ['[Warehouse Name]', '[Event Date]', '[Day(s)]'],
            [$warehouseText, $eventDate, $dayName],
             $announcement->email_message
        );
        
        // Static email lists for announcements only
        $toUsers = [
            'prerak.patel@futurecentrestorage.co.uk',
            'yash.mistry@futurecentrestorage.co.uk',
            'neha.p@futurecentrestorage.co.uk'
        ];
            
        $ccUsers = [
            'Aniruddh.Toke@acornuniversalconsultancy.com',
            'parth.suthar@futurecentrestorage.co.uk'
        ];
            
        // Send single email with proper TO and CC lists
        if (!empty($toUsers)) {
            try {
                Mail::raw($emailMessage, function ($message) use ($toUsers, $ccUsers, $emailSubject) {
                    $message->to($toUsers)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($emailSubject);
                    
                    // Add CC for admin users
                    if (!empty($ccUsers)) {
                        $message->cc($ccUsers);
                    }
                });

                 // Update status in the database to 'Sent'
                $schedule = $announcement->email_schedule;
                $updated = false;
                foreach ($schedule as &$checkpoint) {
                    if (isset($checkpoint['days_before']) && $checkpoint['days_before'] == $this->days) {
                        $checkpoint['status'] = 'Sent';
                        $checkpoint['sent_at'] = \Carbon\Carbon::now()->toDateTimeString();
                        $updated = true;
                        break;
                    }
                }
                if ($updated) {
                    $announcement->email_schedule = $schedule;
                    $announcement->save();
                }

            } catch (\Exception $e) {
                \Log::error('Failed to send announcement email: ' . $e->getMessage());

                // Update status in the database to 'Failed'
                $schedule = $announcement->email_schedule;
                $updated = false;
                foreach ($schedule as &$checkpoint) {
                    if (isset($checkpoint['days_before']) && $checkpoint['days_before'] == $this->days) {
                        $checkpoint['status'] = 'Failed';
                        $updated = true;
                        break;
                    }
                }
                if ($updated) {
                    $announcement->email_schedule = $schedule;
                    $announcement->save();
                }

                throw $e;
            }
        }
    }
    
    private function getDefaultEmailMessage()
    {
        return 'Dear Team,

This is an automated notification to inform you that the warehouse will remain closed on account of a holiday.

Details:
Warehouse: [Warehouse Name]
Closure Date(s): [Event Date]
Day(s): [Day(s)]

During this period, no warehouse operations, dispatches, or deliveries will be processed. Regular operations will resume on reopening.

Please plan your activities accordingly. For any urgent concerns, reach out to the relevant point of contact.

Thank you for your cooperation.

Regards,
LMS Team';
    }
}
