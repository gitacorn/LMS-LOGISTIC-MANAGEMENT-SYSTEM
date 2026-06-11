<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\FBASheeteDetailModel;
use App\CountryToPortGoodsOutModel;

class FBASheetMasterModel extends BaseModel
{
    use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE');
    }
    
    public function fbaSheetDetail(){
    	return $this->hasMany(FBASheeteDetailModel::class , 'i_fba_sheet_master_id');
    }
    
    public function countryToPortMaster(){
    	return $this->belongsTo(CountryToPortGoodsOutModel::class , 'i_country_to_port_goods_out_master_id');
    }
}
