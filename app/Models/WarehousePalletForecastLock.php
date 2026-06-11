<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehousePalletForecastLock extends Model
{
    protected $table = 'warehouse_pallet_forecast_locks';
    protected $primaryKey = 'i_id';
    public $timestamps = false;

    protected $fillable = [
        'i_warehouse_id',
        'dt_forecast_date',
        'i_pallet_forecast',
        'dt_locked_at',
        't_is_active',
        't_is_deleted',
        'i_created_id',
        'i_updated_id',
        'i_deleted_id',
        'dt_created_at',
        'dt_updated_at',
        'dt_deleted_at',
    ];
}
