<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;
class SupplierMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.SUPPLIER_MASTER_TABLE');
	}
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['sm.t_is_deleted != ' ] = 1;
		$defaultWhere['sd.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['sm.i_id'=>'desc'];
	
		$tableName = config('constants.SUPPLIER_MASTER_TABLE'). ' as sm';
			
		$selectData = [
				'sm.i_id',
				'sm.v_supplier_name',
				'sm.t_is_active',
				DB::raw("group_concat(c.v_country_name SEPARATOR ', ')  AS country_name"),
				DB::raw("group_concat(sd.v_supplier_code SEPARATOR ', ')  AS supplier_code"),
				DB::raw("group_concat(sd.v_supplier_address SEPARATOR ', ')  AS supplier_address"),
				DB::raw("group_concat(sd.v_contact_person_name SEPARATOR ', ')  AS supplier_contact_person_name"),
				DB::raw("group_concat(sd.v_contact_email SEPARATOR ', ')  AS supplier_contact_email"),
				DB::raw("group_concat(sd.v_contact_mobile SEPARATOR ', ')  AS supplier_contact_mobile"),
				'sd.i_id  as supplier_detail_id',
				'c.v_country_name',
				'sd.v_supplier_code',
				'sd.v_supplier_address',
				'sd.i_country_id',
				DB::raw("group_concat(sd.e_record_status SEPARATOR ', ')  AS e_record_status"),
	
		];
	
		$joinData[]= ['tableName' =>config('constants.SUPPLIER_DETAIL_TABLE'). ' as sd','condition'=>'sd.i_supplier_id = sm.i_id','type'=> 'left', ];
	
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'sd.i_country_id = c.i_id','type'=> 'left', ];
	
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
			
		return $data;
			
	}
	public function getSupplierRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['sm.t_is_deleted != ' ] = 1;
		$defaultWhere['sd.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['sm.i_id'=>'desc'];
	
		$tableName = config('constants.SUPPLIER_MASTER_TABLE'). ' as sm';
			
		$selectData = [
				'sm.i_id',
				'sm.v_supplier_name',
				'sm.t_is_active',
				'c.v_country_name',
				'sd.v_supplier_code',
				'sd.v_contact_mobile',
				'sd.v_contact_email',
				'sd.v_contact_person_name',
				'sd.v_supplier_address',
				'sd.i_id  as supplier_detail_id',
				'sd.i_country_id',
				'sd.e_record_status',
				'sd.v_timings',
				'sd.e_record_status'
				
	
		];
	
		$joinData[]= ['tableName' =>config('constants.SUPPLIER_DETAIL_TABLE'). ' as sd','condition'=>'sd.i_supplier_id = sm.i_id','type'=> 'left', ];
	
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'sd.i_country_id = c.i_id','type'=> 'left', ];
	
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
			
		return $data;
			
	}

	public function supplierDetail(){
	
		return $this->hasMany(SupplierDetailModel::class,'i_supplier_id');
	
	} 
	public function goodInLogisticMaster(){
		return $this->hasMany(GoodInLogisticMasterModel::class,'i_supplier_id');
	}
	/* public function goodInLogisticInfo(){
		return $this->hasMany(GoodInLogisticMasterModel::class,'v_supplier_ids');
	} */
	/* public function goodInBuyerMaster(){
	
		return $this->hasMany(GoodInBuyerMasterModel::class,'i_id');
	
	}
	
	public function goodInBuyerDetail(){
	
		return $this->hasMany(GoodInBuyerDetailModel::class,'i_id');
	
	} */
	
	
}
