<?php

namespace App;
use App\BaseModel;
use App\Traits\MySoftDeletes;

class CompanyMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.COMPANY_MASTER_TABLE');
	}
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
		
	
		$tableName = config('constants.COMPANY_MASTER_TABLE'). ' as cm';
			
		$selectData = [
				'cm.i_id',
				'cm.v_company_name',
				'cm.v_company_code',
				'cm.v_company_short_code',
				'cm.i_country_id',
				'cm.v_email',
				'c.v_country_name',
				'cm.t_is_active'
		];
		$defaultWhere = [];
		$defaultWhere['cm.t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['cm.i_id'=>'desc'];
		
		$joinData[]= ['tableName' =>config('constants.COUNTRY_MASTER_TABLE'). ' as c','condition'=>'cm.i_country_id = c.i_id' ];
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData,$joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData,  $whereData, $likeData, $additionalData );
		}
	
		return $data;
			
	}

	public function goodInBuyerMaster(){
	
		return $this->hasMany(GoodInBuyerMasterModel::class,'i_buyer_company_id');
	
	}
	public function usWarehouseToAmazonDetail(){
		
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_account_company_id');
	}
	public function usWarehouseToAmazonUkCompanyInfo(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_uk_account_id');
	}
}
