<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\Login;
use App\LogisticPartnerDetailModel;
use App\CountrytoPortEuropeInvoiceModel;
use App\CountrytoPortEuropeDocumentModel;
use App\CountrytoPortEuropeDetailModel;
use App\StatusMasterModel;

class CountrytoPortEuropeModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_MASTER_TABLE');
    	$this->perPage = config ( 'constants.PER_PAGE' );
    }
    
    public function bookEmployeeInfo(){
    	return $this->belongsTo(Login::class,'i_book_by_employee_id');
    }
    
    public function logisticPartnerDetail(){
    	return $this->belongsTo(LogisticPartnerDetailModel::class,'i_logistic_partner_detail_id');
    }
    
    public function statusInfo(){
    	return $this->belongsTo(StatusMasterModel::class,'i_status_id');
    }
    
    public function documentInfo(){
    	return $this->hasMany(CountrytoPortEuropeDocumentModel::class,'i_country_to_port_europe_goods_master_id');
    }
    
    public function invoiceInfo(){
    	return $this->hasMany(CountrytoPortEuropeInvoiceModel::class,'i_country_to_port_europe_goods_master_id');
    }
    
    public function detailInfo(){
    	return $this->hasMany(CountrytoPortEuropeDetailModel::class,'i_country_to_port_europe_goods_master_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] ){
    
    	$query = CountrytoPortEuropeModel::with( [
    			'documentInfo','invoiceInfo','detailInfo.accountCompany','detailInfo.location','detailInfo.warehouse','statusInfo','bookEmployeeInfo','logisticPartnerDetail.logisticPartnerMaster'
    	]);
    
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    
    	if(isset($where['way_of_transport']) && (!empty($where['way_of_transport'])) ){
    		$transportWay = $where['way_of_transport'];
    		$query->where('e_transport_way',$transportWay);
    	}
    
    	if(isset($where['book_by']) && (!empty($where['book_by'])) ){
    		$bookEmployeeId = $where['book_by'];
    		$query->where('i_book_by_employee_id',$bookEmployeeId);
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
    	
    	if(isset($where['from_warehouse']) && (!empty($where['from_warehouse'])) ){
    		$fromWarehouse = $where['from_warehouse'];
    		$query->whereHas('detailInfo' , function($query) use($fromWarehouse) {
    			$query->where('i_warehouse_id','=',$fromWarehouse);
    		});
    	}
    	
    	if(isset($where['to_amazon_location']) && (!empty($where['to_amazon_location'])) ){
    		$toAmazonLocation = $where['to_amazon_location'];
    		$query->whereHas('detailInfo' , function($query) use($toAmazonLocation) {
    			$query->where('i_location_id','=',$toAmazonLocation);
    		});
    	}
    	
    	if(isset($where['to_country_delivery']) && (!empty($where['to_country_delivery'])) ){
    		$toCountryDelivery = $where['to_country_delivery'];
    		$query->whereHas('detailInfo.country' , function ($q) use ($toCountryDelivery){
    			$q->where('i_id', $toCountryDelivery);
    		});
    	}
    	
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$statusId = ($where['status']);
    		$query->whereIn('i_status_id',$statusId);
    	}
    	/* if(isset($where['appointment_from_date']) && (!empty($where['appointment_from_date'])) ){
    		$appointmentFromDate = dbDate( $where['appointment_from_date'] );
    		$query->where('dt_amazon_shipment_date','>=',$appointmentFromDate);
    	}
    	
    	if(isset($where['appointment_to_date']) && (!empty($where['appointment_to_date'])) ){
    		$appointmentToDate = dbDate( $where['appointment_to_date'] );
    		$query->where('dt_amazon_shipment_date','<=',$appointmentToDate);
    	}
    	
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_from_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);
    	}
    	 
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);
    	} */
    	if(isset($where['account_company']) && (!empty($where['account_company'])) ){
    		$accountCompnay = $where['account_company'];
    		$query->whereHas('detailInfo' , function($query) use($accountCompnay) {
    			$query->where('i_account_company_id','=',$accountCompnay);
    		});
    	}
    	if(isset($where['default_status']) && (!empty($where['default_status'])) ){
    		$statusIds = $where['default_status'];
    		$query->whereNotIn('i_status_id',$statusIds);
    	
    	}
    	
    	if(isset($where['booking_form_date']) && (!empty($where['booking_form_date'])) ){
    		$bookingFromDate = dbDate( $where['booking_form_date'] );
    		$query->whereHas('detailInfo' , function($query) use($bookingFromDate) {
    			$query->where('dt_booking_date','>=',$bookingFromDate);
    		});
    		
    	}
    	
    	if(isset($where['booking_to_date']) && (!empty($where['booking_to_date'])) ){
    		$bookingToDate = dbDate( $where['booking_to_date'] );
    		$query->whereHas('detailInfo' , function($query) use($bookingToDate) {
    			$query->where('dt_booking_date','<=',$bookingToDate);
    		});
    	}
    	
    	if(isset($where['collection_form_date']) && (!empty($where['collection_form_date'])) ){
    		$collectionFromDate = dbDate( $where['collection_form_date'] );
    		$query->whereHas('detailInfo' , function($query) use($collectionFromDate) {
    			$query->where('dt_collection_date','>=',$collectionFromDate);
    		});
    	}
    	
    	if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
    		$collectionToDate = dbDate( $where['collection_to_date'] );
    		$query->whereHas('detailInfo' , function($query) use($collectionToDate) {
    			$query->where('dt_collection_date','<=',$collectionToDate);
    		});
    	}
    	
    	if(isset($where['appointment_from_date']) && (!empty($where['appointment_from_date'])) ){
    		$appointmentFromDate = dbDate( $where['appointment_from_date'] );
    		$query->whereHas('detailInfo' , function($query) use($appointmentFromDate) {
    			$query->where('dt_amazon_shipment_date','>=',$appointmentFromDate);
    		});
    	}
    	 
    	if(isset($where['appointment_to_date']) && (!empty($where['appointment_to_date'])) ){
    		$appointmentToDate = dbDate( $where['appointment_to_date'] );
    		$query->whereHas('detailInfo' , function($query) use($appointmentToDate) {
    			$query->where('dt_amazon_shipment_date','<=',$appointmentToDate);
    		});
    		
    	}
    	 
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_from_date'] );
    		$query->whereHas('detailInfo' , function($query) use($deliveryFromDate) {
    			$query->where('dt_delivery_date','>=',$deliveryFromDate);
    		});
    		
    	}
    	
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->whereHas('detailInfo' , function($query) use($deliveryToDate) {
    			$query->where('dt_delivery_date','<=',$deliveryToDate);
    		});
    		
    	}
    	
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		$searchString = ( $likeData['searchBy'] );
    		 
    		$query->where(function($q) use ($searchString){
    			$q->orWhereHas('detailInfo' , function($q1) use($searchString) {
    				$allLikeDetailInfoColumns = [ 'v_workflow_id' , 'v_shipment_id' ];
    					
    				$q1->where(function($q2) use ($allLikeDetailInfoColumns,$searchString){
    					foreach($allLikeDetailInfoColumns as $key => $allLikeColumn){
    						$q2->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    			
    			$allLikeColumns = [ 'v_tracking_no' , 'v_tracking_link', 'v_country_to_port_europe_record_no' ];
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    			
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
    	return $data;
    
    }
    
    public function getRecordDetailsBaseOnDetailTable( $where = [] , $likeData = [] ){
    
    	$query = CountrytoPortEuropeDetailModel::with([
    		'countryToPortEurope', 'countryToPortEurope.logisticPartnerDetail.logisticPartnerMaster', 'country', 'location', 'warehouse', 'accountCompany'
    	]);
    	
    	/* $query = CountrytoPortEuropeModel::with( [
    			'documentInfo','invoiceInfo','detailInfo.accountCompany','detailInfo.location','detailInfo.warehouse','statusInfo','bookEmployeeInfo','logisticPartnerDetail.logisticPartnerMaster'
    	]); */
    
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->whereHas('countryToPortEurope' , function($query) use($masterRecordId) {
    			$query->where('i_id','=',$masterRecordId);    			
    		});
    	}
    
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    
    	if(isset($where['way_of_transport']) && (!empty($where['way_of_transport'])) ){
    		$transportWay = $where['way_of_transport'];
    		$query->whereHas('countryToPortEurope' , function($query) use($transportWay) {
    			$query->where('e_transport_way',$transportWay);    			
    		});
    	}
    
    	if(isset($where['book_by']) && (!empty($where['book_by'])) ){
    		$bookEmployeeId = $where['book_by'];
    		$query->whereHas('countryToPortEurope' , function($query) use($bookEmployeeId) {
    			$query->where('i_book_by_employee_id',$bookEmployeeId);    			
    		});
    	}
    
    	if(isset($where['logistic_partner']) && (!empty($where['logistic_partner'])) ){
    		$logisticPartnerId = $where['logistic_partner'];
    		$query->whereHas('countryToPortEurope' , function($query) use($logisticPartnerId) {
    			$query->where('i_logistic_partner_detail_id',$logisticPartnerId);    			
    		});
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
    	 
    	if(isset($where['from_warehouse']) && (!empty($where['from_warehouse'])) ){
    		$fromWarehouse = $where['from_warehouse'];
    		$query->where('i_warehouse_id','=',$fromWarehouse);
    	}
    	 
    	if(isset($where['to_amazon_location']) && (!empty($where['to_amazon_location'])) ){
    		$toAmazonLocation = $where['to_amazon_location'];
    		$query->where('i_location_id','=',$toAmazonLocation);    		
    	}
    	 
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$statusId = ($where['status']);
    		$query->whereHas('countryToPortEurope' , function($query) use($statusId) {
    			$query->whereIn('i_status_id',$statusId);    			
    		});
    	}
    	/* if(isset($where['appointment_from_date']) && (!empty($where['appointment_from_date'])) ){
    	 $appointmentFromDate = dbDate( $where['appointment_from_date'] );
    	 $query->where('dt_amazon_shipment_date','>=',$appointmentFromDate);
    	 }
    	  
    	 if(isset($where['appointment_to_date']) && (!empty($where['appointment_to_date'])) ){
    	 $appointmentToDate = dbDate( $where['appointment_to_date'] );
    	 $query->where('dt_amazon_shipment_date','<=',$appointmentToDate);
    	 }
    	  
    	 if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    	 $deliveryFromDate = dbDate( $where['delivery_from_date'] );
    	 $query->where('dt_delivery_date','>=',$deliveryFromDate);
    	 }
    
    	 if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    	 $deliveryToDate = dbDate( $where['delivery_to_date'] );
    	 $query->where('dt_delivery_date','<=',$deliveryToDate);
    	 } */
    	if(isset($where['account_company']) && (!empty($where['account_company'])) ){
    		$accountCompnay = $where['account_company'];
    		$query->where('i_account_company_id','=',$accountCompnay);
    	}
    	if(isset($where['default_status']) && (!empty($where['default_status'])) ){
    		$statusIds = $where['default_status'];
    		$query->whereHas('countryToPortEurope' , function($query) use($statusIds) {
    			$query->whereNotIn('i_status_id',$statusIds);    			
    		});
    		 
    	}
    	 
    	if(isset($where['booking_form_date']) && (!empty($where['booking_form_date'])) ){
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
    		/* $query->whereHas('detailInfo' , function($query) use($collectionFromDate) {
    		}); */
    	}
    	 
    	if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
    		$collectionToDate = dbDate( $where['collection_to_date'] );
    		$query->where('dt_collection_date','<=',$collectionToDate);
    	}
    	 
    	if(isset($where['appointment_from_date']) && (!empty($where['appointment_from_date'])) ){
    		$appointmentFromDate = dbDate( $where['appointment_from_date'] );
    		$query->whereHas('countryToPortEurope' , function($query) use($appointmentFromDate) {
    			$query->where('dt_amazon_shipment_date','>=',$appointmentFromDate);		
    		});
    	}
    
    	if(isset($where['appointment_to_date']) && (!empty($where['appointment_to_date'])) ){
    		$appointmentToDate = dbDate( $where['appointment_to_date'] );
    		$query->whereHas('countryToPortEurope' , function($query) use($appointmentToDate) {
    			$query->where('dt_amazon_shipment_date','<=',$appointmentToDate);        			
    		});
    	}
    
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_from_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);    
    	}
    	 
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);    
    	}
    	 
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		$searchString = ( $likeData['searchBy'] );
    		
    		$query->where(function($q) use ($searchString){
    			$q->orWhereHas('countryToPortEurope' , function($q1) use($searchString) {
    				$allLikeDetailInfoColumns = [ 'v_tracking_no' , 'v_tracking_link', 'v_country_to_port_europe_record_no' ];
    					
    				$q1->where(function($q2) use ($allLikeDetailInfoColumns,$searchString){
    					foreach($allLikeDetailInfoColumns as $key => $allLikeColumn){
    						$q2->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    				 
    				$allLikeColumns = [ 'v_workflow_id' , 'v_shipment_id' ];
    				foreach($allLikeColumns as $key => $allLikeColumn){
    					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    				}
    				 
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
    	return $data;
    
    }
    
    public function fbaMasterInfo(){
    	return $this->hasMany(FBASheetMasterModel::class,'i_country_to_port_goods_out_master_id', 'i_id');
    }
}
