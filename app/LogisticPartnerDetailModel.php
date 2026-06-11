<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;

class LogisticPartnerDetailModel extends BaseModel
{
    use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.LOGISTIC_PARTNER_DETAIL_TABLE');
    }
    
    public function logisticPartnerMaster(){
    
    	return $this->belongsTo(LogisticPartnerMasterModel::class,'i_logictic_partner_id');
    
    }
    /* public function usWarehouseToAmazonInfo(){
    	return $this->hasMany(UsWarehouseToAmazonMasterModel::class,'i_from_logistic_partner_detail_id');
    } */
    public function usWarehouseLogisticDetails(){
    	return $this->hasMany(UsWarehouseToAmazonMasterModel::class,'i_logistic_partner_detail_id');
    }
}
