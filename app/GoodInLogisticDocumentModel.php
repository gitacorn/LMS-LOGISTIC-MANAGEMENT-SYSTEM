<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
class GoodInLogisticDocumentModel extends BaseModel
{
	use MySoftDeletes;
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_LOGISTIC_DOCUMENT_TABLE');
	}
	
	public function goodInLogisticMaster(){
	
		return $this->belongsTo(GoodInLogisticMasterModel::class,'i_goods_in_logistic_master_id');
	
	}
	
	public function goodInLogisticCollection(){
	
		return $this->belongsTo(GoodInLogisticCollectionModel::class,'i_goods_in_logistic_collection_id');
	
	}
	
	public function documentTypeMaster(){
	
		return $this->belongsTo(Document_Type_Master_Model::class,'i_document_type_id');
	
	}
	
	
}
