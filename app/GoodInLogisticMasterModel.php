<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use GhanuZ\FindInSet\FindInSetRelationTrait;

class GoodInLogisticMasterModel extends BaseModel
{
	use MySoftDeletes,FindInSetRelationTrait;
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE');
		$this->perPage = config ( 'constants.PER_PAGE' );
	}
	
	public function supplierDetail(){
	
		return $this->belongsTo(SupplierDetailModel::class,'i_supplier_id');
	
	}
	
	public function goodInLogisticCollection(){
	
		return $this->hasMany(GoodInLogisticCollectionModel::class,'i_goods_in_logistic_master_id');
	
	}
	
	/*
	public function goodInBuyerDetailMaster(){
	
		return $this->belongsTo(GoodInBuyerDetailModel::class,'i_goods_in_buyer_master_id');
	
	}
	*/
	
	public function goodInBuyerDetail(){
	
		return $this->belongsTo(GoodInBuyerDetailModel::class,'i_goods_in_buyer_detail_id');
	
	}
	
	public function employeeMaster(){
	
		return $this->belongsTo(Login::class,'i_book_employee_id');
	
	}
	
	public function logisticPartnerMaster(){
	
		return $this->belongsTo(LogisticPartnerMasterModel::class,'i_logistic_partner_id');
	
	}
	
	public function statusMaster(){
	
		return $this->belongsTo(StatusMasterModel::class,'i_status_id');
	
	}
	
	public function goodInLogisticDocument(){
	
		return $this->hasMany(GoodInLogisticDocumentModel::class,'i_goods_in_logistic_master_id');
	
	}
	
	public function goodInLogisticInvoice(){
	
		return $this->hasMany(GoodInLogisticInvoiceModel::class,'i_goods_in_logistic_master_id');
	
	}
	public function logisticPartnerDetail(){
	
		return $this->belongsTo(LogisticPartnerDetailModel::class,'i_logistic_partner_id');
	
	}
	
	public function allGoodInBuyerDetail(){
		return $this->FindInSetMany( 'App\GoodInBuyerDetailModel', 'i_goods_in_buyer_detail_id', 'i_id');
		//return $this->FindInSetMany('App\GoodInBuyerDetailModel', 'v_pallet_dimension_ids', 'i_id');
	}
	public function supplierMasterRecord(){
		return $this->belongsTo(SupplierMasterModel::class,'i_supplier_id');
	}
	public function supplierMaster(){
		//return $this->belongsTo(SupplierMasterModel::class,'v_supplier_ids');
		return $this->FindInSetMany( 'App\SupplierMasterModel', 'v_supplier_ids', 'i_id');
	}
	public function getGoodsInLogisticMaster($where = [] , $likeData = [] ){
	
	
		$query = GoodInLogisticMasterModel::with( [
					'supplierMaster.supplierDetail.countryMaster','goodInLogisticCollection','goodInLogisticCollection.goodInBuyerDetail','supplierDetail.supplierMaster','logisticPartnerMaster','employeeMaster','allGoodInBuyerDetail',
					'statusMaster', 'goodInLogisticInvoice','goodInLogisticDocument.documentTypeMaster','logisticPartnerDetail.logisticPartnerMaster','supplierMasterRecord.goodInLogisticMaster','supplierMaster'
				]);
	
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			/* $query->whereHas('goodInBuyerMaster' , function($query) use($masterRecordId) {
			 $query->where('i_id','=',$masterRecordId);
			}); */
			$query->where('i_id','=',$masterRecordId);
		}
		
		if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
			$query->groupBy('i_id');
		}
		
		$userLogin = (session()->has('user_id') ? session()->get('user_id') : 0);
		if ($userLogin > 0){
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
			
			if (count($userLoginDetails) > 0){
				if (!empty($userLoginDetails[0]->v_record_type) && in_array(config('constants.BUYER'), explode(',', $userLoginDetails[0]->v_record_type))){
					$query->whereHas('goodInBuyerDetail.goodInBuyerMaster', function ($q){
						$q->whereRaw("( find_in_set('".session()->get('user_id')."',v_buyer_employee_ids) or find_in_set('".session()->get('user_id')."',v_user_buyer_ids) ) ");
					});
				}
				if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
					$warehouseId = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0);
					
					if($warehouseId > 0){
						$query->where(function ($q)use($warehouseId){
							$q->orWhere(function($q1)use($warehouseId){
								$q1->where('e_logistic_collection_type', config('constants.COLLECTION'));
								$q1->whereHas('goodInLogisticCollection' , function($q2) use($warehouseId) {
									$q2->where('i_collection_delivery_location_id',$warehouseId);
								});
							});
							$q->orWhere(function($q3)use($warehouseId){
								$q3->where('e_logistic_collection_type', config('constants.DELIVERY'));
								$q3->whereHas('allGoodInBuyerDetail.goodInBuyerMaster' , function($q4) use($warehouseId) {
									$q4->where('i_delivery_location_id',$warehouseId)->where('e_collection_type' , config('constants.DELIVERY'));
								});
							});
							
							/* $q->orWhere(function($q1)use($warehouseId){
								$q1->whereHas('allGoodInBuyerDetail.goodInBuyerMaster' , function($q2) use($warehouseId) {
									//$q2->where('i_warehouse_id',$warehouseId);
									$q2->where('i_delivery_location_id',$warehouseId);
								});
							});
							$q->orWhere(function($q3)use($warehouseId){
								$q3->whereHas('allGoodInBuyerDetail.goodInBuyerMaster' , function($q4) use($warehouseId) {
									$q4->where('i_delivery_location_id',$warehouseId)->where('e_collection_type' , config('constants.DELIVERY'));
								});
							}); */
						});
					}
				}
			}
		}
		
		/* if(isset($where['supplier_detail']) && (!empty($where['supplier_detail'])) ){
			$supplierDetailId = $where['supplier_detail'];
			$query->where('i_supplier_id',$supplierDetailId);
		}  */
		if(isset($where['supplier_name']) && (!empty($where['supplier_name'])) ){
			$supplierId = $where['supplier_name'];
				$query->whereIn('v_supplier_ids',$supplierId);
			
		}
		if(isset($where['collection_type']) && (!empty($where['collection_type'])) ){
			$collectionType = $where['collection_type'];
			$query->where('e_logistic_collection_type','=',$collectionType);
		}
		
		if(isset($where['book_by']) && (!empty($where['book_by'])) ){
			$bookBy = $where['book_by'];
			$query->where('i_book_employee_id',$bookBy);
		}
		
		if(isset($where['logistic_partner']) && (!empty($where['logistic_partner'])) ){
			$logisticPartner = $where['logistic_partner'];
			$query->where('i_logistic_partner_id',$logisticPartner);
		}
		
		if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
			$deliveryFromDate = dbDate( $where['delivery_from_date'] );
			//$query->whereHas('goodInLogisticCollection' , function($query) use($deliveryFromDate) {
				$query->where('dt_delivery_date','>=',$deliveryFromDate);
			//});
			
		}
		
		if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
			$deliveryToDate = dbDate( $where['delivery_to_date'] );
			//$query->whereHas('goodInLogisticCollection' , function($query) use($deliveryToDate) {
				$query->where('dt_delivery_date','<=',$deliveryToDate);
			//});
			
		}
		
		if(isset($where['collection_from_date']) && (!empty($where['collection_from_date'])) ){
			$collectionFromDate = dbDate( $where['collection_from_date'] );
			$query->where('dt_collection_date','>=',$collectionFromDate);
		}
		
		if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
			$collectionToDate = dbDate( $where['collection_to_date'] );
			$query->where('dt_collection_date','<=',$collectionToDate);
		}
		
		if(isset($where['insurance_status']) && (!empty($where['insurance_status'])) ){
			$insuranceStatus = $where['insurance_status'];
			$query->where('e_insurance_status','=',$insuranceStatus);
		}
		if(isset($where['goods_in_from_date']) && (!empty($where['goods_in_from_date'])) ){
			$query->where('dt_goods_in_date','>=',dbDate( $where['goods_in_from_date'] ));
		}
		if(isset($where['goods_in_to_date']) && (!empty($where['goods_in_to_date'])) ){
			$query->where('dt_goods_in_date','<=',dbDate( $where['goods_in_to_date'] ));
		}
		if(isset($where['status']) && (!empty($where['status'])) ){
			$statusId = $where['status'];
			$query->whereIn('i_status_id',$statusId);
				
		}
		if(isset($where['default_status']) && (!empty($where['default_status'])) ){
			$statusIds = $where['default_status'];
			$query->whereNotIn('i_status_id',$statusIds);
		}
		$query->whereHas('statusMaster',function ($q){
			$q->where('t_is_deleted',0);
		});
		if(isset($where['supplier_country']) && (!empty($where['supplier_country'])) ){
			$supplierCountryId = ( $where['supplier_country'] );
			$query->whereHas('supplierMaster.supplierDetail' , function($query) use($supplierCountryId) {
				$query->where('i_country_id','=',$supplierCountryId);
			});
		}
		if(isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
				
			$searchString = ( $likeData['searchBy'] );
			
			$allLikeColumns = [ 'v_goods_in_logistic_master_no' , 'v_tracking_no' , 'v_tracking_link' ];
	
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			}); 
			
		}
		
		if(isset($likeData['searchByBuyerEntryNo']) && (!empty($likeData['searchByBuyerEntryNo'])) ){
			$searchString = ( $likeData['searchByBuyerEntryNo'] );
			
			$query->where(function ($q)use($searchString){
				$q->orWhere(function($q1)use($searchString){
					$q1->whereHas('goodInLogisticCollection.goodInBuyerDetail' , function($q2) use($searchString) {
						$q2->where('v_goods_in_buyer_detail_no',$searchString);
					});
				});
				$q->orWhere(function($q3)use($searchString){
					$q3->whereHas('allGoodInBuyerDetail' , function($q4) use($searchString) {
						$q4->where('v_goods_in_buyer_detail_no',$searchString);
					});
				});
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
