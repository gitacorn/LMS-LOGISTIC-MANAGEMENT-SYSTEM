<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\Login;
use App\CountrytoPortEuropeModel;

class CountrytoPortEuropeInvoiceModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_INVOICE_MASTER_TABLE');
    }
    
	public function countryToPortEurope(){
    	return $this->belongsTo(CountrytoPortEuropeModel::class , 'i_country_to_port_europe_goods_master_id');
    }
}
