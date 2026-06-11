<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\WarehouseMasterModel;
use App\LogisticPartnerDetailModel;
use App\StatusMasterModel;
use App\Login;
use App\PortToAgentWarehouseInvoiceModel;
use App\PortToAgentWarehouseDocumentModel;
use GhanuZ\FindInSet\FindInSetRelationTrait;

class PortToAgentWarehouseModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
	use MySoftDeletes,FindInSetRelationTrait;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE');
    	$this->perPage = config ( 'constants.PER_PAGE' );
    }
    
    public function fromPortInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class,'i_transport_from_id');
    }
    
    public function logisticPartnerDetail(){
    	return $this->belongsTo(LogisticPartnerDetailModel::class,'i_logistic_partner_detail_id');
    }

    public function ownLocation(){
    	return $this->belongsTo(WarehouseMasterModel::class,'i_own_warehouse_location_id');
    }
    
    public function agentLocation(){
    	return $this->belongsTo(LogisticPartnerDetailModel::class,'i_agent_location_id');
    }
    
    public function statusInfo(){
    	return $this->belongsTo(StatusMasterModel::class,'i_status_id');
    }
    
    public function bookEmployeeInfo(){
    	return $this->belongsTo(Login::class,'i_book_by_employee_id');
    }
    
    public function documentInfo(){
    	return $this->hasMany(PortToAgentWarehouseDocumentModel::class,'i_port_to_agent_goods_out_master_id');
    }
    
    public function invoiceInfo(){
    	return $this->hasMany(PortToAgentWarehouseInvoiceModel::class,'i_port_to_agent_goods_out_master_id');
    }
    public function countryToPortMaster(){
    	return $this->FindInSetMany( 'App\CountryToPortGoodsOutModel', 'v_container_ids', 'i_id');
    }
    public function getRecordDetails( $where = [] , $likeData = [] ){
    	 
    	$query = PortToAgentWarehouseModel::with( [
    			'documentInfo','invoiceInfo','fromPortInfo','logisticPartnerDetail','agentLocation','statusInfo','bookEmployeeInfo','countryToPortMaster'
    	]);
    	 
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	 
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    	 
    	if(isset($where['transport_way']) && (!empty($where['transport_way'])) ){
    		$transportWay = $where['transport_way'];
    		$query->where('e_transport_way','=',$transportWay);
    	}
    	 
    	if(isset($where['from_port_airport']) && (!empty($where['from_port_airport'])) ){
    		$fromPortId = (int)($where['from_port_airport']);
    		$query->where('i_transport_from_id',$fromPortId);
    	}
    	
    	if(isset($where['logistic_partner']) && (!empty($where['logistic_partner'])) ){
    		$logisticPartnerId = (int)($where['logistic_partner']);
    		$query->where('i_logistic_partner_detail_id',$logisticPartnerId);
    	}
    	if(isset($where['warehouse_type']) && (!empty($where['warehouse_type'])) ){
    		$query->where('e_warehose_type',$where['warehouse_type']);
    	}
    	if(isset($where['from_warehouse_country']) && (!empty($where['from_warehouse_country'])) ){
    		$fromWarehouseCountryId = $where['from_warehouse_country'];
    		$query->whereHas('countryToPortMaster' , function($q) use($fromWarehouseCountryId) {
    			$q->where('i_from_warehouse_country_id', $fromWarehouseCountryId);
    		});
    	}
    	if(isset($where['warehouse']) && (!empty($where['warehouse'])) ){
    		$warehouseId = $where['warehouse'];
    		$query->whereHas('countryToPortMaster' , function($q) use($warehouseId) {
    			$q->where('i_warehouse_id', $warehouseId);
    		});
    	}
    	if(isset($where['own_location']) && (!empty($where['own_location'])) ){
    		$query->where('i_own_warehouse_location_id',$where['own_location']);
    	}
    	if(isset($where['agent_location']) && (!empty($where['agent_location'])) ){
    		$agentLocationId = (int)($where['agent_location']);
    		$query->where('i_agent_location_id',$agentLocationId);
    	}
    	
    	if(isset($where['select_containers']) && (!empty($where['select_containers'])) ){
    		$containerId = $where['select_containers'];
    		$query->whereHas('countryToPortMaster' , function($q) use($containerId) {
    			$q->where('i_id', $containerId);
    		});
    	}
    	
    	if(isset($where['book_by']) && (!empty($where['book_by'])) ){
    		$bookEmployeeId = $where['book_by'];
    		$query->where('i_book_by_employee_id',$bookEmployeeId);
    	}
    	
    	if(isset($where['container_discharged_from_date']) && (!empty($where['container_discharged_from_date'])) ){
    		$containerDischargedFromDate = dbDate( $where['container_discharged_from_date'] );
    		$query->where('dt_contanier_discharge_date','>=',$containerDischargedFromDate);
    	}
    	 
    	if(isset($where['container_discharged_to_date']) && (!empty($where['container_discharged_to_date'])) ){
    		$containerDischargedToDate = dbDate( $where['container_discharged_to_date'] );
    		$query->where('dt_contanier_discharge_date','<=',$containerDischargedToDate);
    	}
    	 
    	if(isset($where['booking_from_date']) && (!empty($where['booking_from_date'])) ){
    		$bookingFromDate = dbDate( $where['booking_from_date'] );
    		$query->where('dt_booking_date','>=',$bookingFromDate);
    	}
    	 
    	if(isset($where['booking_to_date']) && (!empty($where['booking_to_date'])) ){
    		$bookingToDate = dbDate( $where['booking_to_date'] );
    		$query->where('dt_booking_date','<=',$bookingToDate);
    	}
    	
    	if(isset($where['collection_from_date']) && (!empty($where['collection_from_date'])) ){
    		$collectionFromDate = dbDate( $where['collection_from_date'] );
    		$query->where('dt_collection_date','>=',$collectionFromDate);
    	}
    	
    	if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
    		$collectionToDate = dbDate( $where['collection_to_date'] );
    		$query->where('dt_collection_date','<=',$collectionToDate);
    	}
    	
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_from_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);
    	}
    	 
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);
    	}
    	
    	if(isset($where['process_status']) && (!empty($where['process_status'])) ){
    		$processStatus = ( $where['process_status']);
    		$query->whereIn('e_process_status', $processStatus);
    	}
    	 
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$statusId = ($where['status']);
    		if(is_array($statusId)){
    			$query->whereIn('i_status_id',$statusId);
    		} else {
    			$query->where('i_status_id',$statusId);
    		}
    		
    	}
    	if(isset($where['default_status']) && (!empty($where['default_status'])) ){
    		$statusIds = $where['default_status'];
    		$query->whereNotIn('i_status_id',$statusIds);
    	
    	}
    	if(isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    		 
    		$allLikeColumns = [ 'v_port_to_agent_record_no' , 'v_container_theft_missing' , 'v_tracking_no' ,'v_ref_no', 'v_personal_ref'];
    		 
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			$q->orWhereHas('countryToPortMaster.fbaSheetMaster.fbaSheetDetail', function($q1) use($searchString) {
    				$fbaSheetLikeColumns = [ 'v_fba_po_no' ];
    					
    				$q1->where(function($q2) use ($fbaSheetLikeColumns,$searchString){
    					foreach($fbaSheetLikeColumns as $key => $allLikeColumn){
    						$q2->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    			
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
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
