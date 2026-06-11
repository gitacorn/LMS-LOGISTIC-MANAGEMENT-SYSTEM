<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use GhanuZ\FindInSet\FindInSetRelationTrait;

class UsaContainerClubbingModel extends BaseModel
{
	use HasFactory,MySoftDeletes, FindInSetRelationTrait;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE');
    	$this->perPage = config ( 'constants.PER_PAGE' );
    }
    
    public function bookingPortalInfo(){
    	return $this->belongsTo(LookupMaster::class, 'i_booking_portal_id');
    }
    public function carrierCompanyInfo(){
    	return $this->belongsTo(LogisticPartnerMasterModel::class, 'i_carrier_company_id');
    }
    public function fromWarehouseInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class, 'i_from_warehouse_id');
    }
    public function toLocationInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class, 'i_to_location_id');
    }
    public function statusInfo(){
    	return $this->belongsTo(StatusMasterModel::class, 'i_status_id');
    }
    public function documentInfo(){
    	return $this->hasMany( UsaContainerClubbingDocumentModel::class, 'i_usa_container_clubbing_master_id' );
    }
    public function invoiceInfo(){
    	return $this->hasMany( UsaContainerClubbingInvoiceModel::class, 'i_usa_container_clubbing_master_id' );
    }
    public function detailInfo(){
    	return $this->hasMany( UsaContainerClubbingDetailModel::class, 'i_usa_container_clubbing_master_id' );
    }
    
    public function fbaSheetDetails(){
    	return $this->FindInSetMany( 'App\FBASheeteDetailModel', 'v_fba_sheet_ids', 'i_id');
    }
    
    public function usaWarehouseSheetDetails(){
    	return $this->FindInSetMany( 'App\UsWarehouseToAmazonDetailsModel', 'v_usa_warehouse_container_ids', 'i_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] ){    
    	$query = UsaContainerClubbingModel::where('t_is_deleted', 0);
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    	
    	if(isset($where['from_warehouse']) && (!empty($where['from_warehouse'])) ){
    		$query->where('i_from_warehouse_id', $where['from_warehouse']);
    	}
    	
    	if(isset($where['to_location']) && (!empty($where['to_location'])) ){
    		$query->where('i_to_location_id', $where['to_location']);
    	}
    	
    	if(isset($where['booking_from_date']) && (!empty($where['booking_from_date'])) ){
    		$query->whereDate('dt_booking_date', '>=', $where['booking_from_date']);
    	}
    	
    	if(isset($where['booking_to_date']) && (!empty($where['booking_to_date'])) ){
    		$query->whereDate('dt_booking_date', '<=', $where['booking_to_date']);
    	}
    	
    	if(isset($where['booking_portal']) && (!empty($where['booking_portal'])) ){
    		$query->where('i_booking_portal_id', $where['booking_portal']);
    	}
    	
    	if(isset($where['carrier_company']) && (!empty($where['carrier_company'])) ){
    		$query->where('i_carrier_company_id', $where['carrier_company']);
    	}
    	
    	if(isset($where['collection_from_date']) && (!empty($where['collection_from_date'])) ){
    		$query->whereDate('dt_collection_date', '>=', $where['collection_from_date']);
    	}
    	
    	if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
    		$query->whereDate('dt_collection_date', '<=', $where['collection_to_date']);
    	}
    	
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$query->whereDate('dt_delivery_date', '>=', $where['delivery_from_date']);
    	}
    	 
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$query->whereDate('dt_delivery_date', '<=', $where['delivery_to_date']);
    	}
    	
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		// Explicit status filter from UI
    		$query->where('i_status_id', $where['status']);
    	} elseif (isset($where['default_status']) && !empty($where['default_status'])) {
    		// Default statuses for initial load (include only these)
    		$query->whereIn('i_status_id', (array) $where['default_status']);
    	} elseif (isset($where['exclude_status']) && !empty($where['exclude_status'])) {
    		// Default behavior: exclude provided statuses
    		$query->whereNotIn('i_status_id', (array) $where['exclude_status']);
    	}
    	
    	if(isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		$searchBy = trim($likeData['searchBy']);
    		$likeColumns = ['v_tracking_no', 'v_pro_number', 'd_logistic_cost_in_usd'];
    		if(!empty($searchBy)){
    			$query->where(function($q) use ($searchBy, $likeColumns){
    				foreach($likeColumns as $likeColumn){
    					$q->orWhere($likeColumn, 'like', "%".$searchBy."%");
    				}
    				$q->orWhereHas('usaWarehouseSheetDetails', function($q) use ($searchBy) {
    					$q->where('v_shipment_id', 'like', "%".$searchBy."%");
    				});
    				$q->orWhereHas('fbaSheetDetails', function($q) use ($searchBy) {
    					$q->where('v_fba_po_no', 'like', "%".$searchBy."%");
    				});
    				$q->orWhereHas('fbaSheetDetails.fbaSheetMaster.countryToPortMaster', function($q) use ($searchBy) {
    					$q->where('v_country_to_port_record_no', 'like', "%".$searchBy."%");
    				});
    				$q->orWhereHas('usaWarehouseSheetDetails.usWarehouseToAmazonMaster', function($q) use ($searchBy) {
    					$q->where('v_us_warehouse_to_amazon_record_no', 'like', "%".$searchBy."%");
    				});
    				/* $q->orWhereHas('carrierCompanyInfo', function($q) use ($searchBy) {
    					$q->where('v_logistic_partner_name', 'like', "%".$searchBy."%");
    				});
    				$q->orWhereHas('fromWarehouseInfo', function($q) use ($searchBy) {
    					$q->where('v_warehouse_name', 'like', "%".$searchBy."%");
    				});
    				$q->orWhereHas('toLocationInfo', function($q) use ($searchBy) {
    					$q->where('v_warehouse_name', 'like', "%".$searchBy."%");
    				}); */
    			});
    		}
    	}
    	
    	$query->orderBy('i_id', "DESC" ) ;
    	$pageNo = ( ( isset($where['page']) && (!empty($where['page'])) ) ? $where['page'] : 1 );
    	 
    	if( isset($where['count_record']) && ( ($where['count_record']) == true ) ){
    		$data = $query->get( );
    	} else {
    		$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
    	
    	}
    	 
    	return $data;
    }
}
