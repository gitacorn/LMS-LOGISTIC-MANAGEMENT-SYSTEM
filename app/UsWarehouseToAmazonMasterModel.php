<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsWarehouseToAmazonMasterModel extends BaseModel
{
	use MySoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE');
		$this->perPage = config ( 'constants.PER_PAGE' );
	}
	public function usWarehouseToAmazonDetails(){
		return $this->hasMany(UsWarehouseToAmazonDetailsModel::class,'i_us_warehouse_to_amazon_master_id');
	}
	/* public function fromLogisticPartnerInfo(){
		return $this->belongsTo(LogisticPartnerDetailModel::class,'i_from_logistic_partner_detail_id');
	} */
	public function fromUsWarehouseInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_from_warehouse_id');
	}
	public function bookByEmployee(){
		return $this->belongsTo(Login::class,'i_book_by_employee_id');
	}
	/* public function logisticPartnerMasterInfo(){
		return $this->belongsTo(LogisticPartnerMasterModel::class,'i_logistic_partner_detail_id	');
	} */
	public function logisticPartnerMasterInfo(){
		return $this->belongsTo(LogisticPartnerDetailModel::class,'i_logistic_partner_detail_id');
	}
	public function statusInfo(){
		return $this->belongsTo(StatusMasterModel::class,'i_status_id');
	}
	public function documentInfo(){
		return $this->hasMany(UsWarehouseToAmazonDocumentMasterModel::class,'i_us_warehouse_to_amazon_master_id');
	}
	public function invoiceInfo(){
		return $this->hasMany(UsWarehouseToAmazonInvoiceMasterModel::class,'i_us_warehouse_to_amazon_master_id');
	}
	
	public function getRecordDetails( $where = [] , $likeData = [] ){
		 
		$query = UsWarehouseToAmazonMasterModel::with( ['usWarehouseToAmazonDetails.shipmentRecordIfo',
				'usWarehouseToAmazonDetails','fromUsWarehouseInfo','bookByEmployee','logisticPartnerMasterInfo.logisticPartnerMaster','statusInfo','documentInfo','invoiceInfo'
		]);
		
		$query->whereHas('statusInfo' , function($query){
			$query->where('t_is_deleted', 0);
		});
		
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		if(isset($where['way_of_transport']) && (!empty($where['way_of_transport'])) ){
			$transportWay = $where['way_of_transport'];
			$query->where('e_transport_way',$transportWay);
		}
		if(isset($where['amazon_customer_to']) && (!empty($where['amazon_customer_to'])) ){
			$amazonCustomerTo = $where['amazon_customer_to'];
			$query->where('e_to_location',$amazonCustomerTo);
		}
		if(isset($where['logistic_partner']) && (!empty($where['logistic_partner'])) ){
			$logisticPartnerId = $where['logistic_partner'];
			$query->where('i_logistic_partner_detail_id',$logisticPartnerId);
		}
		
		/* if(isset($where['booking_form_date']) && (!empty($where['booking_form_date'])) ){
			$bookingFromDate = dbDate( $where['booking_form_date'] );
			$query->where('dt_booking_date','>=',$bookingFromDate);
		}
		
		if(isset($where['booking_to_date']) && (!empty($where['booking_to_date'])) ){
			$bookingToDate = dbDate( $where['booking_to_date'] );
			$query->where('dt_booking_date','<=',$bookingToDate);
		}
		
		if(isset($where['collection_form_date']) && (!empty($where['collection_form_date'])) ){
			$collectionFromDate = dbDate( $where['collection_form_date'] );
			$query->where('dt_collection_date','>=',$collectionFromDate);
		}
		
		if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
			$collectionToDate = dbDate( $where['collection_to_date'] );
			$query->where('dt_collection_date','<=',$collectionToDate);
		} */
		
		if(isset($where['status']) && (!empty($where['status'])) ){
			$statusId = ($where['status']);
			$query->whereIn('i_status_id',$statusId);
		}
		/* if(isset($where['appointment_from_date']) && (!empty($where['appointment_from_date'])) ){
			$appointmentFromDate = dbDate( $where['appointment_from_date'] );
			$query->where('dt_amazon_appointment_date','>=',$appointmentFromDate);
		}
		 
		if(isset($where['appointment_to_date']) && (!empty($where['appointment_to_date'])) ){
			$appointmentToDate = dbDate( $where['appointment_to_date'] );
			$query->where('dt_amazon_appointment_date','<=',$appointmentToDate);
		} */
		if(isset($where['warehouse_from']) && (!empty($where['warehouse_from'])) ){
			$warehouseFromId = (int)($where['warehouse_from']);
			$query->where('i_from_warehouse_id',$warehouseFromId);
		}
		if(isset($where['book_by']) && (!empty($where['book_by'])) ){
			$bookEmployeeId = $where['book_by'];
			$query->where('i_book_by_employee_id',$bookEmployeeId);
		}
		/* if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
			$deliveryFromDate = dbDate( $where['delivery_from_date'] );
			$query->where('dt_delivery_date','>=',$deliveryFromDate);
		}
		
		if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
			$deliveryToDate = dbDate( $where['delivery_to_date'] );
			$query->where('dt_delivery_date','<=',$deliveryToDate);
		}
		if(isset($where['box_pallet_type']) && (!empty($where['box_pallet_type'])) ){
			$boxPalletType = $where['box_pallet_type'];
			$query->where('e_box_pallet_type',$boxPalletType);
		} */
		if(isset($where['default_status']) && (!empty($where['default_status'])) ){
			$statusIds = $where['default_status'];
			$query->whereNotIn('i_status_id',$statusIds);
		
		}
		if(isset($where['search_fba_no']) && (!empty($where['search_fba_no'])) ){
			$searchFBANo = ( $where['search_fba_no'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($searchFBANo) {
				$query->whereRaw( "( v_shipment_id='".$searchFBANo."') or ( v_shipment_invoice_no='".$searchFBANo."') or ( v_invoice_no_ref_no='".$searchFBANo."')" );
				
			});
		}
		
		if( isset($where['custom_where']) && (!empty($where['custom_where'])) ){
			
			$searchFBANo = ( $where['custom_where'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($searchFBANo) {
				$query->whereRaw($where['custom_where']);
			
			});
		}
		if( isset($where['booking_form_date']) && (!empty($where['booking_form_date'])) ){
			$bookingFromDate = dbDate( $where['booking_form_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($bookingFromDate) {
				$query->where('dt_booking_date','>=',$bookingFromDate);
					
			});
		}
		if( isset($where['booking_to_date']) && (!empty($where['booking_to_date'])) ){
			$bookingToDate = dbDate( $where['booking_to_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($bookingToDate) {
				$query->where('dt_booking_date','<=',$bookingToDate);
					
			});
		}
		if(isset($where['collection_form_date']) && (!empty($where['collection_form_date'])) ){
			$collectionFromDate = dbDate( $where['collection_form_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($collectionFromDate) {
				$query->where('dt_collection_date','>=',$collectionFromDate);
					
			});
		}
		
		if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
			$collectionToDate = dbDate( $where['collection_to_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($collectionToDate) {
				$query->where('dt_collection_date','<=',$collectionToDate);
					
			});
		}
		if(isset($where['appointment_from_date']) && (!empty($where['appointment_from_date'])) ){
			$appointmentFromDate = dbDate( $where['appointment_from_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($appointmentFromDate) {
				$query->where('dt_amazon_appointment_date','>=',$appointmentFromDate);
					
			});
		}
			
		if(isset($where['appointment_to_date']) && (!empty($where['appointment_to_date'])) ){
			$appointmentToDate = dbDate( $where['appointment_to_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($appointmentToDate) {
				$query->where('dt_amazon_appointment_date','<=',$appointmentToDate);
					
			});
		}
		
		if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
			$deliveryFromDate = dbDate( $where['delivery_from_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($deliveryFromDate) {
				$query->where('dt_delivery_date','>=',$deliveryFromDate);
					
			});
		}
		
		if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
			$deliveryToDate = dbDate( $where['delivery_to_date'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($deliveryToDate) {
				$query->where('dt_delivery_date','<=',$deliveryToDate);
					
			});
		}
		if(isset($where['box_pallet_type']) && (!empty($where['box_pallet_type'])) ){
			$boxPalletType = $where['box_pallet_type'];
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($boxPalletType) {
				$query->where('e_box_pallet_type',$boxPalletType);
					
			});
		}
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
			 
			$searchString = ( $likeData['searchBy'] );
			 
			$allLikeColumns = [ 'v_us_warehouse_to_amazon_record_no','v_tracking_no'];
			 
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			});
		
		}
		if(isset($likeData['search_total_no_of_pallet']) && (!empty($likeData['search_total_no_of_pallet'])) ){
			$searchString = ( $likeData['search_total_no_of_pallet'] );
			$query->whereHas('usWarehouseToAmazonDetails' , function($query) use($searchString) {
				$allLikeColumns = ['i_total_no_of_pallets'];
		
				$query->where(function($q) use ($allLikeColumns,$searchString){
					foreach($allLikeColumns as $key => $allLikeColumn){
						$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
					}
				});
				
			});	
						
						
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
		/* if( isset($where['count_record']) && ( ($where['count_record']) == true ) ){
			$data = $query->get( );
		} else {
			$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
		} */
		
		return $data;
		 
	}
}
