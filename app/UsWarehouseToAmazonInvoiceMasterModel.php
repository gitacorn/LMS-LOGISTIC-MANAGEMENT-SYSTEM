<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;

class UsWarehouseToAmazonInvoiceMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE');
	}
	public function usWarehouseToAmazonInvoiceInfo(){
		return $this->belongsTo(UsWarehouseToAmazonMasterModel::class,'i_us_warehouse_to_amazon_master_id');
	}
}
