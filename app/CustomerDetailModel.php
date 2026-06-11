<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;

class CustomerDetailModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.CUSTOMER_DETAIL_TABLE');
	}
	public function customerMaster(){
		return $this->belongsTo(CustomerMasterModel::class,'i_customer_id');
	}
}
