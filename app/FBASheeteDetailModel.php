<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\FBASheetMasterModel;
use Illuminate\Support\Facades\DB;

class FBASheeteDetailModel extends BaseModel
{
    use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE');
    }
    
    public function fbaSheetMaster(){
    	return $this->belongsTo(FBASheetMasterModel::class , 'i_fba_sheet_master_id');
    }
    
    public function AgentToWarehouseDetailInfo(){
    	return $this->hasOne(AgentToWarehouseDetailModel::class , 'i_fba_sheet_detail_id');
    }
    
    public function amazonCompanyShortCodeInfo(){
    	return $this->belongsTo(CompanyMasterModel::class , 'i_amazon_company_short_code_id');
    }
    
    public function amazonLocationCodeInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_amazon_location_code_id');
    }
    
    public function wareHouseUSACodeInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class , 'i_warehouse_warehouse_code_id');
    }
    
    public function customerCustomerNameInfo(){
    	return $this->belongsTo(CustomerMasterModel::class , 'i_customer_company_name_id');
    }
   
    public function customerCustomerCodeInfo(){
    	return $this->belongsTo(CustomerDetailModel::class , 'i_customer_customer_code_id');
    }
    
    public function locationInfo(){
    	return $this->hasOne(WarehouseMasterModel::class , 'v_warehouse_code', 'v_location_code');
    }
    
    public function usaContainerDetailInfo(){
    	return $this->hasOne(UsaContainerClubbingDetailModel::class , 'i_fba_sheet_detail_id')->where('e_record_type', config('constants.USA_CONTAINER_CLUBBING_FBA_RECORD'));
    }
    
    public function getFBASheetDetails( $where = [] , $likeData = [] ){
    	 
    	$query = FBASheeteDetailModel::with( [
    			'fbaSheetMaster.countryToPortMaster.uploadFBASheetInfo','fbaSheetMaster.countryToPortMaster.portToAgentaContainerInfo','fbaSheetMaster', 'customerCustomerCodeInfo' , 'customerCustomerCodeInfo.customerMaster' , 'fbaSheetMaster.countryToPortMaster','amazonCompanyShortCodeInfo','amazonLocationCodeInfo' , 'wareHouseUSACodeInfo','customerCustomerNameInfo','fbaSheetMaster.countryToPortMaster.fromPortInfo','fbaSheetMaster.countryToPortMaster.toPortInfo','usaContainerDetailInfo'
    	]);
    	
    	$query->whereHas('fbaSheetMaster.countryToPortMaster' , function($query){
    		$query->where('t_is_deleted', 0);
    	});
    	
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$status = $where['status'];
    		$query->where('e_status','=',$status);
    	}
    	
    	if(isset($where['agent_warehouse_to']) && (!empty($where['agent_warehouse_to'])) ){
    		$agentWarehouseTo = $where['agent_warehouse_to'];
    		$query->where('e_destination','=',$agentWarehouseTo);
    	}
    	
    	if(isset($where['selectedRecordIds']) && (!empty($where['selectedRecordIds'])) ){
    		$recordIds = $where['selectedRecordIds'];
    		$query->whereIn('i_id',$recordIds);
    	}
    	 
    	if(isset($where['country_to_port_goods_out_id']) && (!empty($where['country_to_port_goods_out_id'])) ){
    		$countryToPortId = $where['country_to_port_goods_out_id'];
    		$query->whereHas('fbaSheetMaster.countryToPortMaster' , function($query) use($countryToPortId) {
				if(is_array($countryToPortId)){
					$query->whereIn('i_country_to_port_goods_out_master_id',$countryToPortId);
				} else {
					$countryToPortId = (int)$countryToPortId;
					$query->where('i_country_to_port_goods_out_master_id','=',$countryToPortId);
				}
				
			});
		}
		if(isset($where['search_fba_no']) && (!empty($where['search_fba_no'])) ){
			$searchFbaNos = $where['search_fba_no'];
			if(!empty($searchFbaNos)){
				$query->where(function($q) use ($searchFbaNos){
					foreach($searchFbaNos as $key => $searchFbaNo){
						$searchFbaNo = (!empty($searchFbaNo) ? trim($searchFbaNo) : '');
						$q->orWhere('v_fba_po_no', 'like', "%" .$searchFbaNo . "%");
						
					}
				});
			}
		}
		
		if((!empty($where)) && array_key_exists('order_by', $where)){
			$orderByColumn = $where['order_by'];
				
			if(!empty($orderByColumn)){
				foreach($orderByColumn as  $key => $value){
					$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
				}
			}
		} else {
			$query->orderBy('i_id', "asc" ) ;
			if( isset($where['fba_po_no']) && ( ($where['fba_po_no']) == true ) ){
    			$query->orderBy('v_fba_po_no', "asc" ) ;
			}
			
		}
		
		if(isset($where['status_usa_container_clubbing']) && (!empty($where['status_usa_container_clubbing'])) ){
			$status = $where['status_usa_container_clubbing'];
			$query->where('e_usa_container_clubbing_status','=',$status);
		}
		
		if(isset($where['usa_clubbing_container_search']) && $where['usa_clubbing_container_search'] != false){
			$fromWarehouseId = (isset($where['from_warehouse_id']) && !empty($where['from_warehouse_id']) ? $where['from_warehouse_id'] : '' );
			$toLocationId = (isset($where['to_location_id']) && !empty($where['to_location_id']) ? $where['to_location_id'] : '' );
			$boxPalletType = (isset($where['box_pallet_type']) && !empty($where['box_pallet_type']) ? $where['box_pallet_type'] : [] );
			$usaMasterRecordId = (isset($where['usa_master_record_id']) && !empty($where['usa_master_record_id']) ? $where['usa_master_record_id'] : 0 );
			
			$query->select('*', DB::raw('count("v_fba_po_no") as total_pallet'), DB::raw('GROUP_CONCAT(DISTINCT i_id) as fba_ids'));
			$query->where('e_destination', config('constants.AMAZON_FBA_SHEET'));
			
			/* if(isset($where['status_usa_container_clubbing']) && (!empty($where['status_usa_container_clubbing'])) ){
				$status = $where['status_usa_container_clubbing'];
				$query->where('e_usa_container_clubbing_status', $status);
			} */
			
			// $query->where('e_usa_container_clubbing_status', config('constants.PENDING_STATUS'));
			
			$query->where(function($q1) use ($usaMasterRecordId){
				$q1->orWhere('e_usa_container_clubbing_status', config('constants.PENDING_STATUS'));
				if($usaMasterRecordId > 0){
					$q1->orWhere(function ($q2) use ($usaMasterRecordId){
						$q2->where('e_usa_container_clubbing_status', config('constants.COMPLETED_STATUS'));
						$q2->whereHas('usaContainerDetailInfo', function($q3) use ($usaMasterRecordId){
							$q3->where('i_usa_container_clubbing_master_id', $usaMasterRecordId);
						});
					});					
				}
			});
			
			$query->groupBy('v_fba_po_no');
			$query->where(function($q1) use ($fromWarehouseId, $toLocationId, $boxPalletType) {
				
				// 2nd step if own warehouse
				$q1->orWhere(function($q11) use ($fromWarehouseId, $toLocationId, $boxPalletType) {
					$q11->whereHas('fbaSheetMaster.countryToPortMaster.portToAgentaContainerInfo.PortToContainerMasterInfoModel', function($q111) use( $fromWarehouseId ) {
						if(  isset($fromWarehouseId) && !empty($fromWarehouseId) ){
							$q111->where('i_own_warehouse_location_id', $fromWarehouseId);
						}
						$q111->where('e_warehose_type', config('constants.OWN_WAREHOUSE_TYPE'));
						$q111->where('i_status_id', config('constants.DELIVERED_STATUS_ID'));
					});
					if( isset($toLocationId) && !empty($toLocationId) ){
						$q11->whereHas('locationInfo', function($q112) use( $toLocationId ) {
							$q112->where('i_id', $toLocationId);
						});
					}
				});
				
				// 3rd step if agent warehouse
				$q1->orWhere(function($q11) use ($fromWarehouseId, $toLocationId, $boxPalletType) {
					$q11->whereHas('AgentToWarehouseDetailInfo.agentToWarehouse', function($q111) use( $fromWarehouseId ) {
						$q111->where('e_to_location', config('constants.WAREHOUSE_FBA_SHEET'));
						$q111->where('i_status_id', config('constants.DELIVERED_STATUS_ID'));
						if( isset($fromWarehouseId) && !empty($fromWarehouseId) ){
							$q111->where('i_to_warehouse_id', $fromWarehouseId);
						}
					});
					if( isset($toLocationId) && !empty($toLocationId) ){
						$q11->whereHas('locationInfo', function($q112) use( $toLocationId ) {
							$q112->where('i_id', $toLocationId);
						});
					}
				});
			});
		}
		
    	$data = $query->get();
    	 
    	return $data;
    	 
    }
}
