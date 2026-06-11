<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;
class SupplierDetailModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.SUPPLIER_DETAIL_TABLE');
	}
	

	public function supplierMaster(){
	
		return $this->belongsTo(SupplierMasterModel::class,'i_supplier_id');
	
	}
	
	public function goodInLogisticMaster(){
	
		return $this->hasMany(GoodInLogisticMasterModel::class,'i_supplier_id');
	
	}
	public function countryMaster(){
	
		return $this->belongsTo(CountryMasterModel::class,'i_country_id');
	
	}
	
}
