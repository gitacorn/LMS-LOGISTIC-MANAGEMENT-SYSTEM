<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\Login;
use App\LogisticPartnerDetailModel;
use App\StatusMasterModel;
use App\CountrytoPortEuropeTransferInvoiceModel;
use App\CountrytoPortEuropeTransferDocumentModel;
use App\CountrytoPortEuropeTransferDetailModel;


class CountrytoPortEuropeTransferModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.GOODS_OUT_EUROPE_TRANSFER_MASTER_TABLE');
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
    	return $this->hasMany(CountrytoPortEuropeTransferDocumentModel::class,'i_europe_transfer_master_id');
    }
    
    public function invoiceInfo(){
    	return $this->hasMany(CountrytoPortEuropeTransferInvoiceModel::class,'i_europe_transfer_master_id');
    }
    
    public function detailInfo(){
    	return $this->hasMany(CountrytoPortEuropeTransferDetailModel::class,'i_europe_transfer_master_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] ){
    
    	$query = CountrytoPortEuropeTransferModel::with( [
    			'documentInfo','invoiceInfo','detailInfo.accountCompany','detailInfo.location','detailInfo.warehouse','bookEmployeeInfo','statusInfo','logisticPartnerDetail.logisticPartnerMaster'
    	]);
    
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    	
    	if (isset($where['warehouse_id']) && !empty($where['warehouse_id'])){
    		$warehouseId = $where['warehouse_id'];
    		$query->whereHas('detailInfo' , function ($q) use($warehouseId){
    			$q->where('i_from_warehouse_id' , '=' , $warehouseId);
    			$q->orWhere('i_to_warehouse_id' , '=' , $warehouseId);
    		});
    	}
    	
    	if(isset($where['way_of_transport']) && (!empty($where['way_of_transport'])) ){
    		$transportWay = $where['way_of_transport'];
    		$query->where('e_transport_way',$transportWay);
    	}
    
    	if(isset($where['book_by']) && (!empty($where['book_by'])) ){
    		$bookEmployeeId = $where['book_by'];
    		$query->where('i_book_by_employee_id',$bookEmployeeId);
    	}
    	if(isset($where['from_warehouse']) && (!empty($where['from_warehouse'])) ){
    		$query->where('i_from_warehouse_id',$where['from_warehouse']);
    	}
    	if(isset($where['to_warehouse']) && (!empty($where['to_warehouse'])) ){
    		$query->where('i_to_warehouse_id',$where['to_warehouse']);
    	}
    	if(isset($where['logistic_partner']) && (!empty($where['logistic_partner'])) ){
    		$logisticPartnerId = $where['logistic_partner'];
    		$query->where('i_logistic_partner_detail_id',$logisticPartnerId);
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
    	}
    
    	if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
    		$collectionToDate = dbDate( $where['collection_to_date'] );
    		$query->where('dt_collection_date','<=',$collectionToDate);
    	}
    	
    	/* if(isset($where['from_warehouse']) && (!empty($where['from_warehouse'])) ){
    		$fromWarehouse = $where['from_warehouse'];
    		$query->whereHas('detailInfo' , function($q) use($fromWarehouse) {
    			$q->where('i_warehouse_id','=',$fromWarehouse);
    		});
    	}
    	
    	if(isset($where['to_amazon_location']) && (!empty($where['to_amazon_location'])) ){
    		$toAmazonLocation = $where['to_amazon_location'];
    		$query->whereHas('detailInfo' , function($query) use($toAmazonLocation) {
    			$query->where('i_location_id','=',$toAmazonLocation);
    		});
    	} */
    	
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_from_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);
    	}
    	
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);
    	}
    	
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$statusId = ($where['status']);
    		$query->whereIn('i_status_id',$statusId);
    	}
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
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    		 
    		$allLikeColumns = [ 'v_tracking_no' , 'v_tracking_link', 'v_europe_transfer_record_no' ];
    		 
    		$query->where(function($q) use ($allLikeColumns,$searchString){
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
}
