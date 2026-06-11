<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use DB;
use App\Traits\MySoftDeletes;
use Illuminate\Support\Facades\Config;

class LoginHistory extends BaseModel
{
    //
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = 'login_history';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	protected $softDelete = true;
	
	public function getRecordDetail( $whereData = [] , $likeData = [] , $additionalData = [] ){
		
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		}
			
		$defaultWhere = [];
		$defaultWhere['lh.t_is_deleted != ' ] = 1 ;
		$defaultWhere['lm.t_is_deleted != ' ] = 1 ;
		$defaultWhere['order_by']= ['lh.i_id' => 'desc'];
		
		if(session()->get('role') !=  config('constants.ROLE_ADMIN') ){
			$defaultWhere['lh.i_login_id'] = session()->get('user_id');
		}
		
		$tableName = config('constants.LOGIN_HISTORY_TABLE'). ' as lh';
			
		$selectData = [
				'lh.i_id',
				'lh.dt_created_at',
				'lh.v_ip',
				'lm.v_name',
		];
			
		$joinData = [
				[
						'tableName' =>	config('constants.LOGIN_MASTER_TABLE') . ' as lm',
						'condition' =>	"lm.i_id = lh.i_login_id",
				],
		];
			
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
			
		//dd($tableName);
		//DB::enableQueryLog();
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		}
		//$query = DB::getQueryLog();
		//$query = end($query);
		///print_r($query);die;
			
		return $data;
		
	}
	
}
