<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
class WarehouseMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.WAREHOUSE_MASTER_TABLE');
	}
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
		
	
		$tableName = config('constants.WAREHOUSE_MASTER_TABLE'). ' as wm';
			
		$selectData = [
				'wm.i_id',
				'wm.v_warehouse_name',
				'wm.v_warehouse_code',
				'wm.v_warehouse_short_code',
				'wm.i_country_id',
				'c.v_country_name',
				'wm.t_is_active',
				'wm.e_record_type',
				'wm.v_warehouse_email'
		];
		$defaultWhere = [];
		$defaultWhere['wm.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['wm.i_id'=>'desc'];
		
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'wm.i_country_id = c.i_id' ];
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
	
		return $data;
			
	}

	public function goodInBuyerMaster(){
	
		return $this->hasMany(GoodInBuyerMasterModel::class,'i_delivery_location_id');
	
	}
	
	public function countryMaster(){
	
		return $this->belongsTo(CountryMasterModel::class,'i_country_id');
	
	}
	
	public function goodInLogisticCollection(){
	
		return $this->hasMany(GoodInLogisticCollectionModel::class,'i_collection_delivery_location_id');
	
	}
	public function usWarehouseToAmazonFrom(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_amazon_from_warehouse_id');
	}
	public function usWarehouseToAmazonLocation(){
		
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_to_amazon_location_id');
	}
	public function usWarehouseToAmazonFromWarhouseInfo(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_customer_from_warehouse_id');
	}
	/* public function usWarehouseToAmazonLocationInfo(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_to_customer_id');
	} */
	public function usWarehouseToAmazonFromWarehouseInfo(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_uk_from_warehouse_id');
	}
	public function usWarehouseToAmazonUkToWarehouseInfo(){
		return $this->hasMany(WarehouseMasterModel::class,'i_uk_to_warehouse_id');
	}
	public function usWarehouseMaster(){
		return $this->hasMany(UsWarehouseToAmazonMasterModel::class,'i_from_warehouse_id');
	}
	
}
