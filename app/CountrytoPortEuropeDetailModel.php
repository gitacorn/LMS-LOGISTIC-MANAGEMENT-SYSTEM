<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\CompanyMasterModel;
use App\CountrytoPortEuropeModel;
use App\WarehouseMasterModel;

class CountrytoPortEuropeDetailModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE');
    }
    
    public function countryToPortEurope(){
    	return $this->belongsTo(CountrytoPortEuropeModel::class , 'i_country_to_port_europe_goods_master_id');
    }
    
    public function accountCompany(){
    	return $this->belongsTo(CompanyMasterModel::class , 'i_account_company_id');
    }
    
    public function location(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_location_id');
    }
    public function packingWarehouse(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_packing_warehouse_id');
    }
    public function warehouse(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_warehouse_id');
    }
    public function shipmentCurrency(){
    	return $this->belongsTo(CurrencyMasterModel::class, 'i_currency_id');
    }
    public function getRecordDetails( $where = [] , $likeData = [] ){
    		
    	$query = CountrytoPortEuropeDetailModel::with( [
    			'countryToPortEurope.detailInfo','countryToPortEurope','accountCompany','location','warehouse'
    	]);
    	
    	if( isset($where['search_fba_no']) && (!empty($where['search_fba_no'])) ){
    		$searchFbaNos = $where['search_fba_no'];
    		
    		if(!empty($searchFbaNos)){
    			$query->where(function($q) use ($searchFbaNos){
    				foreach($searchFbaNos as $key => $searchFbaNo){
    					$searchFbaNo = (!empty($searchFbaNo) ? trim($searchFbaNo) : '');
    					$q->orWhere('v_shipment_id', 'like', "%" .$searchFbaNo . "%");
    				}
    			});
    		}
    		
    		//$query->whereIn('v_shipment_id',$searchFBANo);
    		 
    	}
    	if( isset($where['fba_po_no']) && ( ($where['fba_po_no']) == true ) ){
    		$query->orderBy('v_shipment_id', "asc" );
    		
    	}
    	$query->orderBy('i_id', "DESC" ) ;
    	$pageNo = ( ( isset($where['page']) && (!empty($where['page'])) ) ? $where['page'] : 1 );
    		
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else if(isset($where['count_record']) && ( ($where['count_record']) == true )){
    		$data = $query->get();
    	} else{
    		$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
    	}
    	return $data;
    		
    }
    
    public function fbaMasterInfo(){
    	return $this->hasMany(FBASheetMasterModel::class,'i_country_to_port_goods_out_master_id', 'i_country_to_port_europe_goods_master_id');
    }
    public function country(){
    	return $this->belongsTo(CountryMasterModel::class , 'i_to_country_id');
    }
}
