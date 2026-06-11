<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;

class GoodInBuyerDocumentModel extends BaseModel
{
	use MySoftDeletes;
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_BUYER_DOCUMENT_TABLE');
	}
	
	public function goodInBuyerMaster(){
		
		return $this->belongsTo(GoodInBuyerMasterModel::class,'i_goods_in_buyer_master_id');
		
	}
	/*
	public function goodInBuyerDetail(){
		
		return $this->belongsTo(GoodInBuyerDetailModel::class);
		
	}
	 */
	
	public function documentTypeMaster(){
	
		return $this->belongsTo(Document_Type_Master_Model::class,'i_document_type_id');
	
	}
}
