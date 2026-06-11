<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\PortToAgentWarehouseModel;

class PortToAgentWarehouseInvoiceModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PORT_TO_AGENT_GOODS_OUT_INVOICE_MASTER_TABLE');
    }
    
    public function portToAgentMaster(){
    	return $this->belongsTo(PortToAgentWarehouseModel::class , 'i_port_to_agent_goods_out_master_id');
    }
}
