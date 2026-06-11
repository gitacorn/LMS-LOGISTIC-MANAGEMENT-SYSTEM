<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use DB;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class LookupMaster extends BaseModel
{
    //
    use SoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	protected $softDelete = true;
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.LOOKUP_MASTER_TABLE');
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
	
		$tableName = config('constants.LOOKUP_MASTER_TABLE'). ' as lm';
			
		$selectData = [
				'lm.i_id',
				'lm.v_value',
				'lm.v_module_name',
				'lm.t_is_active',
				'lm.i_sequence',
		];
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
		//DB::enableQueryLog();
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordById( $tableName, $selectData,  $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectData( $tableName, $selectData,   $whereData, $likeData, $additionalData );
		}
	
		//$query = DB::getQueryLog();
		//$query = end($query);
		//print_r($query);die;
			
		return $data;
			
	}
}
