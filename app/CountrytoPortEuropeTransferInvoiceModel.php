<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\CountrytoPortEuropeTransferModel;

class CountrytoPortEuropeTransferInvoiceModel extends BaseModel
{
use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE');
    }
    
    public function countryToPortEuropeTransfer(){
    	return $this->belongsTo(CountrytoPortEuropeTransferModel::class , 'i_europe_transfer_master_id');
    }
}
