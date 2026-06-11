<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\CountryToPortGoodsOutModel;
use App\PortToAgentWarehouseModel;
class PortToContainerInfoModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE');
    }
    public function countryToPortInfo(){
    	return $this->belongsTo(CountryToPortGoodsOutModel::class,'i_container_id');
    }
    
    public function PortToContainerMasterInfoModel(){
    	return $this->belongsTo(PortToAgentWarehouseModel::class,'i_port_to_agent_goods_out_master_id');
    }
}
