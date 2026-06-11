<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;

class CustomerMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.CUSTOMER_MASTER_TABLE');
	}
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['cm.t_is_deleted != ' ] = 1;
		$defaultWhere['cd.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['cm.i_id'=>'desc'];
	
		$tableName = config('constants.CUSTOMER_MASTER_TABLE'). ' as cm';
			
		$selectData = [
				'cm.i_id',
				'cm.v_customer_name',
				'cm.t_is_active',
				DB::raw("group_concat(c.v_country_name SEPARATOR ', ')  AS country_name"),
				DB::raw("group_concat(cd.v_customer_code SEPARATOR ', ')  AS customer_code"),
				DB::raw("group_concat(cd.v_customer_address SEPARATOR ', ')  AS customer_address"),
				DB::raw("group_concat(cd.v_contact_person_name SEPARATOR ', ')  AS customer_contact_person_name"),
				DB::raw("group_concat(cd.v_contact_email SEPARATOR ', ')  AS customer_contact_email"),
				DB::raw("group_concat(cd.v_contact_mobile SEPARATOR ', ')  AS customer_contact_mobile"),
				'cd.i_id  as customer_detail_id',
				'c.v_country_name',
				'cd.v_customer_code',
				'cd.v_customer_address',
				'cd.i_country_id'
	
		];
	
		$joinData[]= ['tableName' =>config('constants.CUSTOMER_DETAIL_TABLE'). ' as cd','condition'=>'cd.i_customer_id = cm.i_id','type'=> 'left', ];
	
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'cd.i_country_id = c.i_id','type'=> 'left', ];
	
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
			
		return $data;
			
	}
	public function getCustomerRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['cm.t_is_deleted != ' ] = 1;
		$defaultWhere['cd.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['cm.i_id'=>'desc'];
	
		$tableName = config('constants.CUSTOMER_MASTER_TABLE'). ' as cm';
			
		$selectData = [
				'cm.i_id',
				'cm.v_customer_name',
				'cm.t_is_active',
				'c.v_country_name',
				'cd.v_customer_code',
				'cd.v_contact_person_name',
				'cd.v_contact_email',
				'cd.v_contact_mobile',
				'cd.v_customer_address',
				'cd.i_id  as customer_detail_id',
				'cd.i_country_id',
	
	
		];
	
		$joinData[]= ['tableName' =>config('constants.CUSTOMER_DETAIL_TABLE'). ' as cd','condition'=>'cd.i_customer_id = cm.i_id','type'=> 'left', ];
	
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'cd.i_country_id = c.i_id','type'=> 'left', ];
	
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
			
		return $data;
			
	}
	public function usWarehousToAmazonCustomer(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_customer_id');
	}
	public function usWarehousToAmazonCustomerInfo(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_to_customer_id');
	}
	public function customerInfo(){
		return $this->hasMany(CustomerDetailModel::class,'i_customer_id');
	}
}
