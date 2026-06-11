<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\SupplierDetailModel;

class GoodInBuyerDetailModel extends BaseModel
{
	use MySoftDeletes;
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_BUYER_DETAIL_TABLE');
	}
	
	public function goodInBuyerMaster(){
		
		return $this->belongsTo(GoodInBuyerMasterModel::class,'i_goods_in_buyer_master_id');
		
	}
	
	public function supplierMaster(){
	
		return $this->belongsTo(SupplierDetailModel::class,'i_goods_in_buyer_supplier_id');
	
	}
	
	public function goodInLogisticCollection(){
	
		return $this->hasMany(GoodInLogisticCollectionModel::class,'i_goods_in_buyer_detail_id');
	
	}
	
	public function goodInLogisticMaster(){
	
		return $this->hasMany(GoodInLogisticMasterModel::class,'i_goods_in_buyer_detail_id');
	
	}
}
