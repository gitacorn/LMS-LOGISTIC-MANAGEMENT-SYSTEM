<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\AgentToWarehouseModel;

class AgentToWarehouseInvoiceModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE');
    }
    
    public function agentToWarehouse(){
    	return $this->belogs(AgentToWarehouseModel::class , 'i_agent_to_warehouse_goods_out_master_id');
    }
}
