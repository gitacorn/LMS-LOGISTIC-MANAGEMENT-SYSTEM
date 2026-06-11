<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\LogisticPartnerDetailModel;
use App\StatusMasterModel;
use App\Login;
use App\AgentToWarehouseDocumentModel;
use App\AgentToWarehouseInvoiceModel;
use App\AgentToWarehouseDetailModel;
use App\AgentToWarehouseModel;
use App\FBASheeteDetailModel;
use GhanuZ\FindInSet\FindInSetRelationTrait;

class AgentToWarehouseModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
	use MySoftDeletes,FindInSetRelationTrait;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE');
    	$this->perPage = config ( 'constants.PER_PAGE' );
    }
    
    public function formLogisticInfo(){
    	return $this->belongsTo(LogisticPartnerDetailModel::class,'i_from_logistic_partner_detail_id');
    }
    
    public function toLogisticInfo(){
    	return $this->belongsTo(LogisticPartnerDetailModel::class,'i_logistic_partner_detail_id');
    }
    public function toWarehouseInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class,'i_to_warehouse_id');
    }
    public function statusInfo(){
    	return $this->belongsTo(StatusMasterModel::class,'i_status_id');
    }
    
    public function bookEmployeeInfo(){
    	return $this->belongsTo(Login::class,'i_book_by_employee_id');
    }
    
    public function documentInfo(){
    	return $this->hasMany(AgentToWarehouseDocumentModel::class,'i_agent_to_warehouse_goods_out_master_id');
    }
    
    public function invoiceInfo(){
    	return $this->hasMany(AgentToWarehouseInvoiceModel::class,'i_agent_to_warehouse_goods_out_master_id');
    }
    
    public function detailInfo(){
    	return $this->hasMany(AgentToWarehouseDetailModel::class,'i_agent_to_warehouse_master_id');
    }
    public function countryToPortMaster(){
    	return $this->FindInSetMany( 'App\CountryToPortGoodsOutModel', 'v_container_ids', 'i_id');
    }
    public function getRecordDetails( $where = [] , $likeData = [] ){
    
    	$query = AgentToWarehouseModel::with( [
    			'documentInfo','invoiceInfo', 'detailInfo', 'detailInfo.agentToWarehousefbaSheetDetail', 'detailInfo.agentToWarehousefbaSheetDetail.fbaSheetMaster', 'detailInfo.agentToWarehousefbaSheetDetail.fbaSheetMaster.countryToPortMaster', 'bookEmployeeInfo','statusInfo','formLogisticInfo.logisticPartnerMaster','countryToPortMaster','toLogisticInfo.logisticPartnerMaster'
    	]);
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    
    	if(isset($where['from_port']) && (!empty($where['from_port'])) ){
    		$fromPortId = $where['from_port'];
    		$query->where('i_from_logistic_partner_detail_id',$fromPortId);
    	}
    
    	if(isset($where['to_location']) && (!empty($where['to_location'])) ){
    		$toLocation = $where['to_location'];
    		$query->where('e_to_location','=',$toLocation);
    	}
    	if(isset($where['to_warehouse']) && (!empty($where['to_warehouse'])) ){
    		$query->where('i_to_warehouse_id','=',$where['to_warehouse']);
    	}
    	if(isset($where['book_by']) && (!empty($where['book_by'])) ){
    		$bookEmployeeId = $where['book_by'];
    		$query->where('i_book_by_employee_id',$bookEmployeeId);
    	}
    
    	if(isset($where['logistic_partner']) && (!empty($where['logistic_partner'])) ){
    		$logisticPartnerId = $where['logistic_partner'];
    		$query->where('i_logistic_partner_detail_id',$logisticPartnerId);
    	}
    
    	if(isset($where['collection_form_date']) && (!empty($where['collection_form_date'])) ){
    		$collectionFromDate = dbDate( $where['collection_form_date'] );
    		$query->where('dt_collection_date','>=',$collectionFromDate);
    	}
    
    	if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
    		$collectionToDate = dbDate( $where['collection_to_date'] );
    		$query->where('dt_collection_date','<=',$collectionToDate);
    	}
    
    	if(isset($where['delivery_form_date']) && (!empty($where['delivery_form_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_form_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);
    	}
    
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);
    	}
    	if(isset($where['booking_form_date']) && (!empty($where['booking_form_date'])) ){
    		$bookingFromDate = dbDate( $where['booking_form_date'] );
    		$query->where('dt_booking_date','>=',$bookingFromDate);
    	}
    	
    	if(isset($where['booking_to_date']) && (!empty($where['booking_to_date'])) ){
    		$bookingToDate = dbDate( $where['booking_to_date'] );
    		$query->where('dt_booking_date','<=',$bookingToDate);
    	}
    	if(isset($where['way_of_transport']) && (!empty($where['way_of_transport'])) ){
    		$wayOfTransport = $where['way_of_transport'];
    		$query->where('e_transport_way','=',$wayOfTransport);
    	}
    	if(isset($where['select_containers']) && (!empty($where['select_containers'])) ){
    		$containerId = $where['select_containers'];
    		$query->whereHas('countryToPortMaster' , function($query) use($containerId) {
    			$customerSearch = " ( ";
    			if(!empty($containerId)){
    				foreach($containerId as $container){
    					$customerSearch .= "find_in_set('".$container."',v_container_ids) OR ";
    				}
    				$customerSearch = rtrim($customerSearch,"OR ");
    				$customerSearch .= " ) ";
    				$query->whereRaw( $customerSearch );
    			}
    		});
    	}
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$statusId = $where['status'];
    		$query->whereIn('i_status_id',$statusId);
    	
    	}
    	if(isset($where['default_status']) && (!empty($where['default_status'])) ){
    		$statusIds = $where['default_status'];
    		$query->whereNotIn('i_status_id',$statusIds);
    	
    	}
    	if(isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    		 
    		$allLikeColumns = [ 'v_tracking_no' , 'v_tracking_link' ,'v_agent_to_warehouse_record_no' ];
    		 
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			$q->orWhereHas('countryToPortMaster', function($q1) use($searchString) {
    				$allOtherLikeColumns = [ 'v_personal_ref' ];
    					
    				$q1->where(function($q2) use ($allOtherLikeColumns,$searchString){
    					foreach($allOtherLikeColumns as $key => $allLikeColumn){
    						$q2->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    			
    			$q->orWhereHas('countryToPortMaster.fbaSheetMaster.fbaSheetDetail', function($q1) use($searchString) {
    				$allOtherLikeColumns = [ 'v_fba_po_no' ];
    					
    				$q1->where(function($q2) use ($allOtherLikeColumns,$searchString){
    					foreach($allOtherLikeColumns as $key => $allLikeColumn){
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
