<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;

class StatusMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.STATUS_MASTER_TABLE');
	}
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['t_is_deleted != ' ] = 1;
		$defaultWhere['order_by'] = ['i_id'=>'desc'];
	
		$tableName = config('constants.STATUS_MASTER_TABLE');
			
		$selectData = [
				'i_id',
				'v_status',
				'i_sequence',
				't_is_active'
		];
	
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
	
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordById( $tableName, $selectData,  $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectData( $tableName, $selectData,   $whereData, $likeData, $additionalData );
		}
	
		
		return $data;
			
	}

	public function goodInLogisticMaster(){
	
		return $this->hasMany(GoodInLogisticMasterModel::class,'i_status_id');
	
	}
	public function usWarehouseMasterRecord(){
		return $this->hasMany(UsWarehouseToAmazonMasterModel::class,'i_status_id');
	}
}
