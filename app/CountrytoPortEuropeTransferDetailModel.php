<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\CountrytoPortEuropeTransferModel;
use App\CompanyMasterModel;
use App\WarehouseMasterModel;


class CountrytoPortEuropeTransferDetailModel extends BaseModel
{
use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE');
    }
    
    public function countryToPortEuropeTransfer(){
    	return $this->belongsTo(CountrytoPortEuropeTransferModel::class , 'i_europe_transfer_master_id');
    }
    
    public function accountCompany(){
    	return $this->belongsTo(CompanyMasterModel::class , 'i_account_company_id');
    }
    
    public function location(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_location_id');
    }
    
    public function warehouse(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_warehouse_id');
    }
}
