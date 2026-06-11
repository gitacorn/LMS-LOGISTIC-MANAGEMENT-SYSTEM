<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Announcement;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendAnnouncementEmailJob;

class AnnouncementController extends Controller
{
    private $folderName = 'admin.announcement.';

    public function __construct()
    {
        $this->middleware('checklogin');
    }

    public function index()
    {
        if (strtolower(session()->get('role')) != strtolower(config('constants.ROLE_ADMIN')) && strtolower(session()->get('role')) != 'DEVELOPER') {
            return redirect('access-denied');
        }

        $data['pageTitle'] = 'Announcement';
        
        // Get announcements with optimized query - no email processing
        $announcements = Announcement::where('t_is_active', 1)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'v_announcement_text', 'dt_event_start_date', 'dt_expiry_date', 'warehouse_ids', 'v_marquee_text', 'email_enabled', 'created_at']);
        
        // Add status efficiently
        $today = now();
        foreach ($announcements as $announcement) {
            $eventDate = \Carbon\Carbon::parse($announcement->dt_expiry_date);
            
            if ($eventDate < $today) {
                $announcement->status = 'Inactive';
            } elseif ($eventDate->isSameDay($today)) {
                $announcement->status = 'Active';
            } else {
                $announcement->status = 'Upcoming';
            }
        }
        
        $data['announcements'] = $announcements;
        return view($this->folderName . 'announcement-index')->with($data);
    }

    public function create()
    {
        if (strtolower(session()->get('role')) != strtolower(config('constants.ROLE_ADMIN')) && strtolower(session()->get('role')) != 'DEVELOPER') {
            return redirect('access-denied');
        }

        $data['pageTitle'] = 'Add Announcement';
        $data['wareHouseDetails'] = \DB::table('warehouse_master')
            ->where('t_is_active', 1)
            ->where('e_record_type', 'Warehouse')
            ->orderBy('v_warehouse_name')
            ->get();
        return view($this->folderName . 'announcement-create')->with($data);
    }

    public function store(Request $request)
    {
        if (strtolower(session()->get('role')) != strtolower(config('constants.ROLE_ADMIN')) && strtolower(session()->get('role')) != 'DEVELOPER') {
            return redirect('access-denied');
        }

        // Handle checkbox values
        $request->merge([
            'email_enabled' => $request->has('email_enabled') ? true : false,
        ]);

        $validator = Validator::make($request->all(), [
            'v_announcement_text' => 'required|string|max:1000',
            'dt_expiry_date' => 'required|date|after_or_equal:today',
            'warehouse_ids' => 'required|array|min:1',
            'warehouse_ids.*' => 'required|string',
            'email_enabled' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate marquee text
        $warehouseNames = [];
        foreach ($request->warehouse_ids as $encodedId) {
            $decodedId = Wild_tiger::decode($encodedId);
            $warehouse = \DB::table('warehouse_master')
                ->where('i_id', $decodedId)
                ->first();
            if ($warehouse) {
                $warehouseNames[] = $warehouse->v_warehouse_name;
            }
        }
        
        $warehouseText = !empty($warehouseNames) ? implode(', ', $warehouseNames) : '';
        $eventDate = date('d-m-Y', strtotime($request->dt_expiry_date));
        $marqueeText = trim($request->v_announcement_text) . ' on ' . $eventDate . 
                     (!empty($warehouseText) ? ' at ' . $warehouseText : '');

        // Generate email schedule
        $eventDateParsed = \Carbon\Carbon::parse($request->dt_expiry_date);
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $offsets = [31, 24, 17, 10, 3];
        $emailSchedule = [];
        
        foreach ($offsets as $days) {
            $sendDate = $eventDateParsed->copy()->subDays($days)->format('Y-m-d');
            $status = ($sendDate < $today) ? 'Skipped' : 'Pending';
            $emailSchedule[] = [
                'days_before' => $days,
                'send_date' => $sendDate,
                'status' => $status,
                'sent_at' => null
            ];
        }
        $announcement = Announcement::create([
            'v_announcement_text' => $request->v_announcement_text,
            'dt_event_start_date' => \Carbon\Carbon::now()->format('Y-m-d'),
            'dt_expiry_date' => $request->dt_expiry_date,
            'warehouse_ids' => $request->warehouse_ids,
            'v_marquee_text' => $marqueeText,
            'email_enabled' => $request->email_enabled ?? false,
            'email_schedule' => $emailSchedule,
            'email_subject' => $request->email_subject ?? 'Warehouse Closure Update - [Eventdate] - [Day]',
            'email_message' => $request->email_message ?? $this->getDefaultEmailMessage(),
            't_is_active' => 1,
            'i_created_by' => session()->get('user_id')
        ]);

        // Send email notifications immediately if today is the send date and status is Pending
        if ($request->email_enabled) {
            foreach ($emailSchedule as $checkpoint) {
                if ($checkpoint['status'] === 'Pending' && $checkpoint['send_date'] === $today) {
                    SendAnnouncementEmailJob::dispatch($announcement->id, $checkpoint['days_before']);
                }
            }
        }

        return redirect('announcement')->with('success', 'Announcement created successfully!');
    }

    public function edit($id)
    {
        if (strtolower(session()->get('role')) != strtolower(config('constants.ROLE_ADMIN')) && strtolower(session()->get('role')) != 'DEVELOPER') {
            return redirect('access-denied');
        }

        $data['pageTitle'] = 'Edit Announcement';
        $data['announcement'] = Announcement::find($id);
        $data['wareHouseDetails'] = \DB::table('warehouse_master')
            ->where('t_is_active', 1)
            ->where('e_record_type', 'Warehouse')
            ->orderBy('v_warehouse_name')
            ->get();
            
        if (!$data['announcement']) {
            return redirect('announcement')->with('error', 'Announcement not found.');
        }
        
        return view($this->folderName . 'announcement-edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        if (strtolower(session()->get('role')) != strtolower(config('constants.ROLE_ADMIN')) && strtolower(session()->get('role')) != 'DEVELOPER') {
            return redirect('access-denied');
        }

        // Handle checkbox values
        $request->merge([
            'email_enabled' => $request->has('email_enabled') ? true : false,
        ]);

        $validator = Validator::make($request->all(), [
            'v_announcement_text' => 'required|string|max:1000',
            'dt_expiry_date' => 'required|date',
            'warehouse_ids' => 'required|array|min:1',
            'warehouse_ids.*' => 'required|string',
            'email_enabled' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $announcement = Announcement::find($id);

        // Generate marquee text
        $warehouseNames = [];
        foreach ($request->warehouse_ids as $encodedId) {
            $decodedId = Wild_tiger::decode($encodedId);
            $warehouse = \DB::table('warehouse_master')
                ->where('i_id', $decodedId)
                ->first();
            if ($warehouse) {
                $warehouseNames[] = $warehouse->v_warehouse_name;
            }
        }
        
        $warehouseText = !empty($warehouseNames) ? implode(', ', $warehouseNames) : '';
        $eventDate = date('d-m-Y', strtotime($request->dt_expiry_date));
        $marqueeText = trim($request->v_announcement_text) . ' on ' . $eventDate . 
                     (!empty($warehouseText) ? ' at ' . $warehouseText : '');

        // Calculate and preserve schedule
        $eventDateParsed = \Carbon\Carbon::parse($request->dt_expiry_date);
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $offsets = [31, 24, 17, 10, 3];
        $emailSchedule = [];
        
        // Check if event date changed
        $oldExpiryDate = \Carbon\Carbon::parse($announcement->dt_expiry_date)->format('Y-m-d');
        $newExpiryDate = $eventDateParsed->format('Y-m-d');
        $dateChanged = $oldExpiryDate !== $newExpiryDate;
        
        $existingSchedule = $announcement->email_schedule;
        
        foreach ($offsets as $days) {
            $sendDate = $eventDateParsed->copy()->subDays($days)->format('Y-m-d');
            
            $status = ($sendDate < $today) ? 'Skipped' : 'Pending';
            $sentAt = null;
            
            if (!$dateChanged && !empty($existingSchedule) && is_array($existingSchedule)) {
                foreach ($existingSchedule as $oldCheckpoint) {
                    if (isset($oldCheckpoint['days_before']) && $oldCheckpoint['days_before'] == $days) {
                        $status = $oldCheckpoint['status'] ?? $status;
                        $sentAt = $oldCheckpoint['sent_at'] ?? null;
                        break;
                    }
                }
            }
            
            $emailSchedule[] = [
                'days_before' => $days,
                'send_date' => $sendDate,
                'status' => $status,
                'sent_at' => $sentAt
            ];
        }


        $announcement->update([
            'v_announcement_text' => $request->v_announcement_text,
            'dt_event_start_date' => $announcement->dt_event_start_date ? $announcement->dt_event_start_date->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d'),
            'dt_expiry_date' => $request->dt_expiry_date,
            'warehouse_ids' => $request->warehouse_ids,
            'v_marquee_text' => $marqueeText,
            'email_enabled' => $request->email_enabled ?? false,
            'email_schedule' => $emailSchedule,
            'email_subject' => $request->email_subject ?? 'Warehouse Closure Update - [Eventdate] - [Day]',
            'email_message' => $request->email_message ?? $this->getDefaultEmailMessage(),
            'i_updated_by' => session()->get('user_id')
        ]);

        // Send email notifications immediately if today is the send date and status is Pending
        if ($request->email_enabled) {
            foreach ($emailSchedule as $checkpoint) {
                if ($checkpoint['status'] === 'Pending' && $checkpoint['send_date'] === $today) {
                    SendAnnouncementEmailJob::dispatch($announcement->id, $checkpoint['days_before']);
                }
            }
        }

        return redirect('announcement')->with('success', 'Announcement updated successfully!');
 
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

    public function getActiveAnnouncements()
    {
        $today = date('Y-m-d');
        $maxShowDate = date('Y-m-d', strtotime('+31 days'));

        // Get active announcements (where expiry date is in the future/today but within 31 days)
        $announcements = Announcement::where('t_is_active', 1)
            ->whereDate('dt_expiry_date', '>=', $today)
            ->whereDate('dt_expiry_date', '<=', $maxShowDate)
            ->orderBy('created_at', 'desc')
            ->get();

            if ($announcements->isEmpty()) {
            return response()->json([
                [
                    'v_marquee_text' => "Leadership isn't a privilege to do less. It's a responsibility to do more -Kristan Hadeed ✨ • Stay hydrated, Stay undefeated! •"
                ]
            ]);
        }
            
        return response()->json($announcements);
    }

    public function destroy($id)
    {
        try {
            $announcement = Announcement::find($id);
            
            if (!$announcement) {
                return redirect()->back()->with('error', 'Announcement not found.');
            }
            
            // Soft delete by setting t_is_active to 0
            $announcement->t_is_active = 0;
            $announcement->i_updated_by = session()->get('user_id');
            $announcement->save();
            
            return redirect('announcement')->with('success', 'Announcement deleted successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting announcement: ' . $e->getMessage());
        }
    }
}
