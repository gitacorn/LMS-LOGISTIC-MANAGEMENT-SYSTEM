<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
class GoodInLogisticInvoiceModel extends BaseModel
{
	use MySoftDeletes;
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_LOGISTIC_INVOICE_TABLE');
	}
	
	public function goodInLogisticMaster(){
	
		return $this->belongsTo(GoodInLogisticMasterModel::class,'i_goods_in_logistic_master_id');
	
	}
	
	public function logisticPartnerInfo() {
		return $this->belongsTo(LogisticPartnerMasterModel::class, 'i_logistic_partner_master_id');
	}
	
}
