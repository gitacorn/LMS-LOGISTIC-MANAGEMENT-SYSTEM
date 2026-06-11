<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Twt\Wild_tiger;

class Announcement extends Model
{
    protected $table = 'announcements';
    
    protected $fillable = [
        'v_announcement_text',
        'dt_event_start_date',
        'dt_expiry_date',
        'warehouse_ids',
        'v_marquee_text',
        'email_schedule',
        'email_enabled',
        'email_subject',
        'email_message',
        't_is_active',
        'i_created_by',
        'i_updated_by'
    ];
    
    protected $casts = [
        'dt_event_start_date' => 'date',
        'dt_expiry_date' => 'date',
        'warehouse_ids' => 'array',
        'email_schedule' => 'array',
        'email_enabled' => 'boolean',
        't_is_active' => 'boolean',
        'i_created_by' => 'integer',
        'i_updated_by' => 'integer'
    ];
    
    /**
     * Scope to get active announcements that haven't expired and have started
     */
    public function scopeActive($query)
    {
        return $query->where('t_is_active', 1)
                    ->where('dt_event_start_date', '<=', date('Y-m-d'))
                    ->where('dt_expiry_date', '>=', date('Y-m-d'));
    }
    
    /**
     * Scope to get announcements for display
     */
    public function scopeForDisplay($query)
    {
        return $query->active()
                    ->orderBy('created_at', 'desc');
    }
    
    /**
     * Generate marquee text from announcement data
     */
    public function generateMarqueeText()
    {
        $warehouseNames = [];
        if (!empty($this->warehouse_ids)) {
            foreach ($this->warehouse_ids as $encodedId) {
                $decodedId = Wild_tiger::decode($encodedId);
                $warehouse = \DB::table('warehouse_master')
                    ->where('i_id', $decodedId)
                    ->where('e_record_type', 'Warehouse')
                    ->first();
                if ($warehouse) {
                    $warehouseNames[] = $warehouse->v_warehouse_name;
                }
            }
        }
        
        $warehouseText = !empty($warehouseNames) ? implode(', ', $warehouseNames) : '';
        $eventDate = date('d-m-Y', strtotime($this->dt_expiry_date));
        
        return trim($this->v_announcement_text) . ' on ' . $eventDate . 
               (!empty($warehouseText) ? ' at ' . $warehouseText : '');
    }
}
