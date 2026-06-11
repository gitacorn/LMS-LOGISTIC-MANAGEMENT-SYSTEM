<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;

class AgentWarehouseToAmazonModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE');
	}
	
}
