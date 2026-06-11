<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
class GoodInLogisticCollectionModel extends BaseModel
{
	use MySoftDeletes;
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_LOGISTIC_COLLECTION_TABLE');
	}
	
	public function goodInLogisticMaster(){
		
		return $this->belongsTo(GoodInLogisticMasterModel::class,'i_goods_in_logistic_master_id');
		
	}
	
	public function goodInBuyerDetail(){
	
		return $this->belongsTo(GoodInBuyerDetailModel::class,'i_goods_in_buyer_detail_id');
	
	}
	
	public function warehouseMaster(){
	
		return $this->belongsTo(WarehouseMasterModel::class,'i_collection_delivery_location_id');
	
	}
	
	public function goodInLogisticDocument(){
		
		return $this->hasMany(GoodInLogisticDocumentModel::class,'i_goods_in_logistic_collection_id');
		
	}
	
	
	
	
}
