<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\AgentToWarehouseModel;
use App\FBASheeteDetailModel; 

class AgentToWarehouseDetailModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DETAIL_TABLE');
    }
    
    public function agentToWarehouse(){
    	return $this->belongsTo(AgentToWarehouseModel::class , 'i_agent_to_warehouse_master_id');
    }
    
    public function agentToWarehousefbaSheetDetail(){
    	return $this->belongsTo(FBASheeteDetailModel::class , 'i_fba_sheet_detail_id');
    }
}
