<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;

class Document_Type_Master_Model extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.DOCUMENT_TYPE_MASTER_TABLE');
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
	
		$tableName = config('constants.DOCUMENT_TYPE_MASTER_TABLE');
			
		$selectData = [
				'i_id',
				'e_document_type',
				'v_document_type_name',
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

	public function goodInBuyerDocument(){
	
		return $this->hasMany(GoodInBuyerDocumentModel::class,'i_document_type_id');
	
	}
	
	public function goodInLogisticDocument(){
	
		return $this->hasMany(GoodInLogisticDocumentModel::class,'i_document_type_id');
	
	}

}
