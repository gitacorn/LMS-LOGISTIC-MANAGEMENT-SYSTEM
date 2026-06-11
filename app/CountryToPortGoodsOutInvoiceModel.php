<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\CountryToPortGoodsOutModel;

class CountryToPortGoodsOutInvoiceModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE');
    }
    
    public function countryToPortGoodsOutMasterInfo(){
    	return $this->belogs(CountryToPortGoodsOutModel::class , 'i_country_to_port_goods_out_master_id');
    }
}
