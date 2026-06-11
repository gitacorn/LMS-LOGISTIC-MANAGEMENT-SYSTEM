<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;

class WarehousePalletMasterModel extends BaseModel
{
	use HasFactory, MySoftDeletes;
    protected $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.WAREHOUSE_PALLET_MASTER_TABLE');
    }
    
	public function warehouseInfo(){
		return $this->hasMany(UsWarehouseToAmazonMasterModel::class,'i_warehouse_id');
	}
	
	public function getRecordDetails($whereData = [], $likeData = [], $additionalData = []){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['t_is_deleted != '] = 1;
		$defaultWhere['order_by'] = ['i_id'=>'desc'];
	
		$tableName = $this->table;
			
		$selectData = [
				'i_id',
				'i_warehouse_id',
				'dt_pallet_date',
				'i_pallet_limit',
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
}