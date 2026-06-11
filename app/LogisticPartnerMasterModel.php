<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;
class LogisticPartnerMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.LOGISTIC_PARTNER_MASTER_TABLE');
	}
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['lm.t_is_deleted != ' ] = 1;
		$defaultWhere['ld.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['lm.i_id'=>'desc'];
	
		$tableName = config('constants.LOGISTIC_PARTNER_MASTER_TABLE'). ' as lm';
			
		$selectData = [
				'lm.i_id',
				'lm.v_logistic_partner_name',
				'lm.v_logistic_multi_address_info',
				'lm.t_is_active',
				'c.v_country_name',
				DB::raw("group_concat(c.v_country_name SEPARATOR ', ')  AS country_name"),
				DB::raw("group_concat(ld.v_logistic_partner_code SEPARATOR ', ')  AS logistic_partner_code"),
				DB::raw("group_concat(ld.v_logistic_partner_address SEPARATOR ', ')  AS logistic_partner_address"),
				DB::raw("group_concat(ld.v_contact_mobile SEPARATOR ', ')  AS logistic_partner_contact_mobile"),
				DB::raw("group_concat(ld.v_contact_email SEPARATOR ', ')  AS logistic_partner_contact_email"),
				DB::raw("group_concat(ld.v_contact_person_name SEPARATOR ', ')  AS logistic_partner_contact_person_name"),
				'ld.v_logistic_partner_code',
				'ld.v_logistic_partner_address',
				'ld.i_id  as logistic_detail_id',
				'ld.i_country_id'
				
		];
		
		$joinData[]= ['tableName' =>config('constants.LOGISTIC_PARTNER_DETAIL_TABLE'). ' as ld','condition'=>'ld.i_logictic_partner_id = lm.i_id','type'=> 'left', ];
		
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'ld.i_country_id = c.i_id','type'=> 'left', ];
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
			
		return $data;
			
	}
	public function getLogisticRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['lm.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['lm.i_id'=>'desc'];
		$defaultWhere['ld.t_is_deleted != ' ] = 1;
		$tableName = config('constants.LOGISTIC_PARTNER_MASTER_TABLE'). ' as lm';
			
		$selectData = [
				'lm.i_id',
				'lm.v_logistic_partner_name',
				'lm.v_logistic_multi_address_info',
				'lm.t_is_active',
				'c.v_country_name',
				'ld.v_logistic_partner_code',
				'ld.v_contact_mobile',
				'ld.v_contact_email',
				'ld.v_contact_person_name',
				'ld.v_logistic_partner_address',
				'ld.i_id  as logistic_detail_id',
				'ld.i_country_id'
	
		];
	
		$joinData[]= ['tableName' =>config('constants.LOGISTIC_PARTNER_DETAIL_TABLE'). ' as ld','condition'=>'ld.i_logictic_partner_id = lm.i_id','type'=> 'left', ];
	
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'ld.i_country_id = c.i_id','type'=> 'left', ];
	
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
		
		return $data;
			
	}
	
	public function goodInLogisticMaster(){
	
		return $this->hasMany(GoodInLogisticMasterModel::class,'i_logistic_partner_id');
	
	}
	
	public function logisticPartnerDetail(){
	
		return $this->hasMany(LogisticPartnerDetailModel::class,'i_logictic_partner_id');
	
	}
	/* public function usWarehouseInfo(){
		return $this->hasMany(UsWarehouseToAmazonMasterModel::class,'i_logistic_partner_detail_id');
	} */
}
