<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\BaseModel;
use App\UsWarehouseToAmazonDetailsModel;
class ShipmentInfoModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.SHIPMENT_NO_INFO_TABLE');
	}
	/* public function usWarehouseDetails(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_ref_table_id');
	} */
}
