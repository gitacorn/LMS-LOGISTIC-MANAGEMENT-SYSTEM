<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use DB;
use GhanuZ\FindInSet\FindInSetRelationTrait;

class GoodInBuyerMasterModel extends BaseModel
{
	use MySoftDeletes,FindInSetRelationTrait;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.GOODS_IN_BUYER_MASTER_TABLE');
		$this->perPage = config ( 'constants.PER_PAGE' );
	}
	
	public function goodInBuyerDetail(){
		return $this->hasMany(GoodInBuyerDetailModel::class,'i_goods_in_buyer_master_id');
	}
	
	public function goodInBuyerDocument(){
		return $this->hasMany(GoodInBuyerDocumentModel::class,'i_goods_in_buyer_master_id');
	}
	
	public function companyMaster(){
		return $this->belongsTo(CompanyMasterModel::class,'i_buyer_company_id');
	}
	
	/* public function companyUserMaster(){
		return $this->belongsTo(CompanyMasterModel::class);
	} */
 
	public function loginMaster(){
		return $this->belongsTo(Login::class,'i_buyer_employee_id');
	}
	
	public function supplierMaster(){
		return $this->belongsTo(SupplierMasterModel::class,'i_main_supplier_id');
	}
	
	/* public function supplierMaster(){
		return $this->hasMany(SupplierMasterModel::class,'v_supplier_ids');
	} */
	
	public function poCurrencyMaster(){
		return $this->belongsTo(CurrencyMasterModel::class,'i_po_currency_id');
	}
	
	public function paymentCurrencyMaster(){
		return $this->belongsTo(CurrencyMasterModel::class,'i_payment_currency_id');
	}
	
	public function warehouseMaster(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_delivery_location_id');
	}
	
	public function boxDimensionGoodInBuyerMaster(){
		return $this->belongsTo(DimensionMasterModel::class,'i_box_dimension_id');
	}
	
	public function palletDimensionGoodInBuyerMaster(){
		return $this->belongsTo(DimensionMasterModel::class,'i_pallet_dimension_id');
	}
	public function companyUserMaster(){
		return $this->FindInSetMany( 'App\CompanyMasterModel', 'v_user_company_ids', 'i_id');
	}
	public function employeeBuyerNameMaster(){
		return $this->FindInSetMany('App\Login', 'v_buyer_employee_ids', 'i_id');
	}
	public function userBuyerNameMaster(){
		return $this->FindInSetMany('App\Login', 'v_user_buyer_ids', 'i_id');
	}
	public function boxDimensionMaster(){
		return $this->FindInSetMany('App\DimensionMasterModel', 'v_box_dimension_ids', 'i_id');
	}
	public function palletDimensionMaster(){
		return $this->FindInSetMany('App\DimensionMasterModel', 'v_pallet_dimension_ids', 'i_id');
	}
	public function dimensionMaster(){
		return $this->FindInSetMany('App\DimensionMasterModel', 'v_dimension_ids', 'i_id');
	}
	
	public function buyerGoodsRemark(){
		return $this->FindInSetMany('App\LookupMaster', 'v_goods_remark_ids', 'i_id');
	}
	
	public function buyerPaymentTerm(){
		return $this->belongsTo(LookupMaster::class , 'i_payment_terms_id');
	}
	
	public function buyerDangerousGoods(){
		return $this->belongsTo(LookupMaster::class , 'i_dangerous_goods_id');
	}
	
	
	
	public function buyerPOCreatedUser(){
		return $this->belongsTo(Login::class , 'i_po_create_user_id');
	}
	
	
	public function getGoodsInBuyerDetails($where = [] , $likeData = [] ){
		$query = GoodInBuyerDetailModel::with( [ 'supplierMaster' , 'supplierMaster.countryMaster'  , 'goodInLogisticMaster.statusMaster','goodInBuyerMaster' , 'goodInBuyerMaster.companyMaster' , 'goodInBuyerMaster.goodInBuyerDocument.documentTypeMaster' , 'goodInBuyerMaster.loginMaster' , 'goodInBuyerMaster.supplierMaster' , 'goodInBuyerMaster.supplierMaster.supplierDetail.countryMaster','goodInBuyerMaster.poCurrencyMaster','goodInBuyerMaster.paymentCurrencyMaster','goodInBuyerMaster.companyUserMaster' ,'goodInBuyerMaster.employeeBuyerNameMaster','goodInBuyerMaster.boxDimensionMaster','goodInBuyerMaster.palletDimensionMaster','goodInBuyerMaster.warehouseMaster.countryMaster' , 'goodInLogisticCollection.goodInLogisticMaster' , 'goodInLogisticMaster' , 'goodInBuyerMaster.buyerPaymentTerm' , 'goodInBuyerMaster.buyerGoodsRemark' , 'goodInBuyerMaster.buyerPOCreatedUser' , 'goodInBuyerMaster.buyerDangerousGoods'  , 'goodInBuyerMaster.dimensionMaster']);
		
		if(isset($where['company']) && (!empty($where['company'])) ){
			$companyId = $where['company'];
			$query->whereHas('goodInBuyerMaster.companyMaster' , function($query) use($companyId) {
				$query->whereIn('i_buyer_company_id',$companyId);
			});
		}
		if(isset($where['warehouse_id']) && (!empty($where['warehouse_id'])) ){
			$warehouseId = ( $where['warehouse_id'] );
			$query->whereHas('goodInBuyerMaster.warehouseMaster' , function($query) use($warehouseId) {
				$query->where('i_delivery_location_id','=',$warehouseId);
			});
		}
		if(isset($where['buyerMasterId']) && (!empty($where['buyerMasterId'])) ){
			$buyerMasterId = $where['buyerMasterId'];
			$query->whereHas('goodInBuyerMaster' , function($query) use($buyerMasterId) {
				$query->where('i_id',$buyerMasterId);
			});
		}
	
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			/* $query->whereHas('goodInBuyerMaster' , function($query) use($masterRecordId) {
				$query->where('i_id','=',$masterRecordId);
			}); */
			if( is_array($masterRecordId)){
				$query->whereIn('i_id', $masterRecordId);
			} else {
				$query->where('i_id','=',$masterRecordId);
			}
			
		}
		
		if(isset($where['order_from_date']) && (!empty($where['order_from_date'])) ){
			$orderFromDate = dbDate( $where['order_from_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($orderFromDate) {
				$query->where('dt_order_date','>=',$orderFromDate);
			});
		}
		
		if(isset($where['order_to_date']) && (!empty($where['order_to_date'])) ){
			$orderToDate = dbDate( $where['order_to_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($orderToDate) {
				$query->where('dt_order_date','<=',$orderToDate);
			});
		}
		
		if(isset($where['collection_type']) && (!empty($where['collection_type'])) ){
			$collectionType = ( $where['collection_type'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($collectionType) {
				$query->where('e_collection_type','=',$collectionType);
			});
		}
	
		if( isset($where['document_type']) && (!empty($where['document_type'])) ){
			$documentTypeId = $where['document_type'];
			$query->whereHas('goodInBuyerMaster.goodInBuyerDocument' , function($query) use($documentTypeId) {
				$query->where('i_document_type_id','=',$documentTypeId);
			});
		}
	
		if(isset($where['employee']) && (!empty($where['employee'])) ){
			$employeeId = $where['employee'];
			$query->whereHas('goodInBuyerMaster.loginMaster' , function($query) use($employeeId) {
				$query->whereIn('i_buyer_employee_id',$employeeId);
			});
		}
		if(isset($where['supplier']) && ((!empty($where['supplier']))) ){
			$supplierId = $where['supplier'];
			$query->whereHas('goodInBuyerMaster.supplierMaster' , function($query) use ($supplierId){
				$query->whereIn('i_main_supplier_id',$supplierId);
			});
		}
		
		if(isset($where['supplier_location']) && ((!empty($where['supplier_location']))) ){
			$supplierLocationId = $where['supplier_location'];
			$query->whereHas('supplierMaster' , function($query) use ($supplierLocationId){
				$query->whereIn('i_goods_in_buyer_supplier_id',$supplierLocationId);
			});
		}
		
		/* if(isset($where['payment_status']) && (!empty($where['payment_status'])) ){
			$paymentStatus = ( $where['payment_status'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($paymentStatus) {
				$query->where('e_payment_status','=',$paymentStatus);
			});
		} */
		
		if(isset($where['payment_from_date']) && (!empty($where['payment_from_date'])) ){
			$paymentFromDate = dbDate( $where['payment_from_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($paymentFromDate) {
				$query->where('dt_payment_date','>=',$paymentFromDate);
			});
		}
		
		
		
		if(isset($where['payment_to_date']) && (!empty($where['payment_to_date'])) ){
			$paymentToDate = dbDate( $where['payment_to_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($paymentToDate) {
				$query->where('dt_payment_date','<=',$paymentToDate);
			});
		}
		
		if(isset($where['delivery_type']) && (!empty($where['delivery_type'])) ){
			$deliveryType = ( $where['delivery_type'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($deliveryType) {
				$query->where('e_delivery_type','=',$deliveryType);
			});
		}
		
		if(isset($where['delivery_location']) && (!empty($where['delivery_location'])) ){
			$deliveryLocation = ( $where['delivery_location'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($deliveryLocation) {
				$query->where('i_delivery_location_id','=',$deliveryLocation);
			});
		}
		
		// Filter by delivery location name (warehouse name)
		if(isset($where['delivery_location_name']) && (!empty($where['delivery_location_name'])) ){
			$deliveryLocationName = $where['delivery_location_name'];
			\Log::info('Model: Filtering by warehouse name: ' . $deliveryLocationName);
			$query->whereHas('goodInBuyerMaster.warehouseMaster' , function($query) use($deliveryLocationName) {
				// Try exact match first
				$query->where('v_warehouse_name','=',$deliveryLocationName);
				// Also try with different formats (in case of encoding differences)
				$query->orWhere('v_warehouse_name','LIKE','%'.$deliveryLocationName.'%');
				// Try without special characters
				$cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $deliveryLocationName);
				if($cleanName != $deliveryLocationName) {
					$query->orWhere('v_warehouse_name','LIKE','%'.$cleanName.'%');
				}
			});
		}
		
		// Exact Buyer Delivery Date filter
		if(isset($where['buyer_delivery_date']) && (!empty($where['buyer_delivery_date'])) ){
			$deliveryExactDate = dbDate( $where['buyer_delivery_date'] );
			\Log::info('Model: Exact delivery date after dbDate conversion: ' . $deliveryExactDate);
			$query->whereHas('goodInBuyerMaster' , function($query) use($deliveryExactDate) {
				$query->where('dt_delivery_date','=',$deliveryExactDate);
			});
		}
		
		if(isset($where['buyer_delivery_from_date']) && (!empty($where['buyer_delivery_from_date'])) ){
			$deliveryFromDate = dbDate( $where['buyer_delivery_from_date'] );
			\Log::info('Model: Delivery from date after dbDate conversion: ' . $deliveryFromDate);
			$query->whereHas('goodInBuyerMaster' , function($query) use($deliveryFromDate) {
				$query->where('dt_delivery_date','>=',$deliveryFromDate);
			});
		}
		
		if(isset($where['buyer_delivery_to_date']) && (!empty($where['buyer_delivery_to_date'])) ){
			$deliveryToDate = dbDate( $where['buyer_delivery_to_date'] );
			\Log::info('Model: Delivery to date after dbDate conversion: ' . $deliveryToDate);
			$query->whereHas('goodInBuyerMaster' , function($query) use($deliveryToDate) {
				$query->where('dt_delivery_date','<=',$deliveryToDate);
			});
		}
		
		// Filter by logistic delivery date (for DELIVERED status records)
		if(isset($where['logistic_delivery_from_date']) && (!empty($where['logistic_delivery_from_date'])) ){
			$logisticDeliveryFromDate = dbDate( $where['logistic_delivery_from_date'] );
			\Log::info('Model: Logistic delivery from date after dbDate conversion: ' . $logisticDeliveryFromDate);
			$query->whereHas('goodInLogisticMaster' , function($query) use($logisticDeliveryFromDate) {
				$query->where('dt_delivery_date','>=',$logisticDeliveryFromDate);
			});
		}
		
		if(isset($where['logistic_delivery_to_date']) && (!empty($where['logistic_delivery_to_date'])) ){
			$logisticDeliveryToDate = dbDate( $where['logistic_delivery_to_date'] );
			\Log::info('Model: Logistic delivery to date after dbDate conversion: ' . $logisticDeliveryToDate);
			$query->whereHas('goodInLogisticMaster' , function($query) use($logisticDeliveryToDate) {
				$query->where('dt_delivery_date','<=',$logisticDeliveryToDate);
			});
		}
		
		// Filter by collection dates (for COLLECTION records)
		if(isset($where['collection_from_date']) && (!empty($where['collection_from_date'])) ){
			$collectionFromDate = dbDate( $where['collection_from_date'] );
			\Log::info('Model: Collection from date after dbDate conversion: ' . $collectionFromDate);
			$query->whereHas('goodInLogisticMaster' , function($query) use($collectionFromDate) {
				$query->where('dt_collection_date','>=',$collectionFromDate);
			});
		}
		
		if(isset($where['collection_to_date']) && (!empty($where['collection_to_date'])) ){
			$collectionToDate = dbDate( $where['collection_to_date'] );
			\Log::info('Model: Collection to date after dbDate conversion: ' . $collectionToDate);
			$query->whereHas('goodInLogisticMaster' , function($query) use($collectionToDate) {
				$query->where('dt_collection_date','<=',$collectionToDate);
			});
		}
		
		if(isset($where['payment_terms']) && (!empty($where['payment_terms'])) ){
			$paymentTermId = ( $where['payment_terms'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($paymentTermId) {
				$query->where('i_payment_terms_id','=',$paymentTermId);
			});
		}
		
		if(isset($where['goods_remark']) && (!empty($where['goods_remark'])) ){
			$goodsRemarkIds = $where['goods_remark'];
			$query->whereHas('goodInBuyerMaster.companyMaster' , function($query) use($goodsRemarkIds) {
				$customerSearch = " ( ";
				if(!empty($goodsRemarkIds)){
					foreach($goodsRemarkIds as $goodsRemarkId){
						$customerSearch .= "find_in_set('".$goodsRemarkId."',v_goods_remark_ids) OR ";
						//$query->whereRaw( "find_in_set('".$userCompany."',v_user_company_ids)" );
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$query->whereRaw( $customerSearch );
				}
				//$query->whereIn('v_user_company_ids',$userCompanyId);
			});
		}
		
		if(isset($where['po_create_user_name']) && (!empty($where['po_create_user_name'])) ){
			$poCreatedUserId = ( $where['po_create_user_name'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($poCreatedUserId) {
				$query->where('i_po_create_user_id','=',$poCreatedUserId);
			});
		}
		
		if(isset($where['buyer_delivery_to_date']) && (!empty($where['buyer_delivery_to_date'])) ){
			$deliveryToDate = dbDate( $where['buyer_delivery_to_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($deliveryToDate) {
				$query->where('dt_delivery_date','<=',$deliveryToDate);
			});
		}
		
		if(isset($where['box_pallet_type']) && (!empty($where['box_pallet_type'])) ){
			$boxPallettype = trim( $where['box_pallet_type'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($boxPallettype) {
				$query->where('e_pallet_box_type',$boxPallettype);
			});
		}
		
		if(isset($where['invoice_from_date']) && (!empty($where['invoice_from_date'])) ){
			$invoiceFromDate = dbDate( $where['invoice_from_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($invoiceFromDate) {
				$query->where('dt_invoice_date','>=',$invoiceFromDate);
			});
		}
		
		if(isset($where['invoice_to_date']) && (!empty($where['invoice_to_date'])) ){
			$invoiceToDate = dbDate( $where['invoice_to_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($invoiceToDate) {
				$query->where('dt_invoice_date','<=',$invoiceToDate);
			});
		}
		
		if(isset($where['actual_payment_from_date']) && (!empty($where['actual_payment_from_date'])) ){
			$actualPaymentFromDate = dbDate( $where['actual_payment_from_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($actualPaymentFromDate) {
				$query->where('dt_actual_payment_date','>=',$actualPaymentFromDate);
			});
		}
		
		if(isset($where['actual_payment_to_date']) && (!empty($where['actual_payment_to_date'])) ){
			$actualPaymentToDate = dbDate( $where['actual_payment_to_date'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($actualPaymentToDate) {
				$query->where('dt_actual_payment_date','<=',$actualPaymentToDate);
			});
		}
		
		/* if(isset($where['custome_procedure_export']) && (!empty($where['custome_procedure_export'])) ){
			$customeProcedureExport = ( $where['custome_procedure_export'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($customeProcedureExport) {
				$query->where('e_customer_procedure_export','=',$customeProcedureExport);
			});
		}
		if(isset($where['custome_procedure_import']) && (!empty($where['custome_procedure_import'])) ){
			$customeProcedureImport = ( $where['custome_procedure_import'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($customeProcedureImport) {
				$query->where('e_customer_procedure_import','=',$customeProcedureImport);
			});
		}
		*/
		if(isset($where['dangerous_goods']) && (!empty($where['dangerous_goods'])) ){
			$dangerousGoods = ( $where['dangerous_goods'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($dangerousGoods) {
				$query->where('i_dangerous_goods_id',$dangerousGoods);
			});
		}
		
		/* if(isset($where['boxes_dimension']) && (!empty($where['boxes_dimension'])) ){
			$boxesDimension = ( $where['boxes_dimension'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($boxesDimension) {
				$query->where('i_box_dimension_id','=',$boxesDimension);
			});
		} */
		
		/* if(isset($where['pallets_dimension']) && (!empty($where['pallets_dimension'])) ){
			$palletsDimension = ( $where['pallets_dimension'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($palletsDimension) {
				$query->where('i_pallet_dimension_id','=',$palletsDimension);
			});
		} */
		
		if(isset($where['pallets_type']) && (!empty($where['pallets_type'])) ){
			$palletsType = ( $where['pallets_type'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($palletsType) {
				$query->where('e_pallet_type','=',$palletsType);
			});
		}
		if(isset($where['record_status_type']) && (!empty($where['record_status_type'])) ){
			$recordStatusType = ( $where['record_status_type'] );
			$query->whereIn('e_buyer_record_status', [ config('constants.PARTIAL_DELIVERY_TYPE') , config('constants.FULL_DELIVERY_TYPE') ] );
			//( e_logistic_record_status is null ) or (  e_logistic_record_status is not null and e_logistic_record_status != e_buyer_record_status )
			$query->where(function($query) use($recordStatusType){
				$query->whereRaw( "( e_logistic_record_status is null ) or (  e_logistic_record_status is not null and e_logistic_record_status != e_buyer_record_status )" );
				/* $query->where('e_logistic_record_status','=' ,  $recordStatusType)
				->orWhere('e_logistic_record_status', null); */
			}); 
			
			/* $query->where(function($query) use($recordStatusType){
				$query->whereIn('e_buyer_record_status', [ config('constants.PARTIAL_DELIVERY_TYPE') , config('constants.FULL_DELIVERY_TYPE') ] )->where('e_logistic_record_status','=' ,  config('constants.PARTIAL_DELIVERY_TYPE'))
				->orWhere('e_logistic_record_status', null);
			}); */
			
		}
		
		if(isset($where['un_process_record']) && (!empty($where['un_process_record'])) && ( $where['un_process_record'] != false ) ){
			$query->whereIn('e_buyer_record_status', [ config('constants.PARTIAL_DELIVERY_TYPE') , config('constants.FULL_DELIVERY_TYPE') ] );
			
		}
		if(isset($where['user_company']) && (!empty($where['user_company'])) ){
			$userCompanyId = $where['user_company'];
			$query->whereHas('goodInBuyerMaster.companyMaster' , function($query) use($userCompanyId) {
				$customerSearch = " ( ";
				if(!empty($userCompanyId)){
					foreach($userCompanyId as $userCompany){
						$customerSearch .= "find_in_set('".$userCompany."',v_user_company_ids) OR ";
						//$query->whereRaw( "find_in_set('".$userCompany."',v_user_company_ids)" );
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$query->whereRaw( $customerSearch );
				}
				//$query->whereIn('v_user_company_ids',$userCompanyId);
			});
		}
		
		/* if(isset($where['box_dimension']) && (!empty($where['box_dimension'])) ){
			$boxDimensionId = $where['box_dimension'];
			$query->whereHas('goodInBuyerMaster.boxDimensionMaster' , function($query) use($boxDimensionId) {
				$customerSearch = " ( ";
				if(!empty($boxDimensionId)){
					foreach($boxDimensionId as $boxDimension){
						$customerSearch .= "find_in_set('".$boxDimension."',v_box_dimension_ids) OR ";
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$query->whereRaw( $customerSearch );
				}
				
			});
		}
		if(isset($where['pallet_dimension']) && (!empty($where['pallet_dimension'])) ){
			$palletDimensionId = $where['pallet_dimension'];
			$query->whereHas('goodInBuyerMaster.palletDimensionMaster' , function($query) use($palletDimensionId) {
				$customerSearch = " ( ";
				if(!empty($palletDimensionId)){
					foreach($palletDimensionId as $palletDimension){
						$customerSearch .= "find_in_set('".$palletDimension."',v_pallet_dimension_ids) OR ";
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$query->whereRaw( $customerSearch );
				}
		
			});
		} */
		
		if(isset($where['pallet_box_dimension']) && (!empty($where['pallet_box_dimension'])) ){
			$dimensionIds = $where['pallet_box_dimension'];
			$query->whereHas('goodInBuyerMaster.dimensionMaster' , function($query) use($dimensionIds) {
				$customerSearch = " ( ";
				if(!empty($dimensionIds)){
					foreach($dimensionIds as $dimensionId){
						$customerSearch .= "find_in_set('".$dimensionId."',v_dimension_ids) OR ";
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$query->whereRaw( $customerSearch );
				}
			});
		}
		
		if(isset($where['employee_name']) && (!empty($where['employee_name'])) ){
			$employeeBuyerId = $where['employee_name'];
			$query->whereHas('goodInBuyerMaster.employeeBuyerNameMaster' , function($query) use($employeeBuyerId) {
				$customerSearch = " ( ";
				if(!empty($employeeBuyerId)){
					foreach($employeeBuyerId as $employeeBuyer){
						$customerSearch .= "find_in_set('".$employeeBuyer."',v_buyer_employee_ids) OR ";
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$query->whereRaw( $customerSearch );
				}
		
			});
		}
		
		if(isset($where['user_buyer_name']) && (!empty($where['user_buyer_name'])) ){
			$userBuyerIds = $where['user_buyer_name'];
			$query->whereHas('goodInBuyerMaster.userBuyerNameMaster' , function($q) use($userBuyerIds) {
				$customerSearch = " ( ";
				if(!empty($userBuyerIds)){
					foreach($userBuyerIds as $userBuyer){
						$customerSearch .= "find_in_set('".$userBuyer."',v_user_buyer_ids) OR ";
					}
					$customerSearch = rtrim($customerSearch,"OR ");
					$customerSearch .= " ) ";
					$q->whereRaw( $customerSearch );
				}
			});
		}
		
		if(isset($where['delivery_collection_location']) && (!empty($where['delivery_collection_location'])) ){
			$warehouseId = ( $where['delivery_collection_location'] );
			$query->whereHas('goodInBuyerMaster' , function($query) use($warehouseId) {
				$query->where('i_delivery_location_id','=',$warehouseId);
			});
		}
		if( isset($where['show_not_completed_record']) && ($where['show_not_completed_record'] != false ) ){
			$query->whereRaw( "( e_logistic_record_status is null ) or (  e_logistic_record_status is not null and e_logistic_record_status != e_buyer_record_status )" );
		}
		if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
			$query->groupBy('i_goods_in_buyer_master_id');
		}
		if(isset($where['status']) && (!empty($where['status'])) ){
			$statusId = $where['status'];
			$query->where(function ($q)use($statusId){
				$q->orWhere(function($q3)use($statusId){
					$q3->whereHas('goodInLogisticMaster' , function($q4) use($statusId) {
						$q4->whereIn('i_status_id',$statusId);
					});
				});
				$q->orWhere(function($q1)use($statusId){
					$q1->whereHas('goodInLogisticCollection.goodInLogisticMaster' , function($q2) use($statusId) {
						$q2->whereIn('i_status_id',$statusId);
					});
				});
				
			});
		}
		
		if(isset($where['ready_for_collection']) && (!empty($where['ready_for_collection'])) ){
			$readyForCollection = $where['ready_for_collection'];
			$query->whereHas('goodInBuyerMaster' , function($query) use($readyForCollection) {
				$query->where('e_ready_for_collection_status',$readyForCollection);
			});
		}
		
		if(isset($where['supplier_country']) && (!empty($where['supplier_country'])) ){
			$supplierCountryId = ( $where['supplier_country'] );
			$query->whereHas('goodInBuyerMaster.supplierMaster.supplierDetail' , function($query) use($supplierCountryId) {
				$query->where('i_country_id','=',$supplierCountryId);
			});
		}
		if(isset($where['all_delivered_cancelled_ststus'])){
			$deliveredCancelledStstus = $where['all_delivered_cancelled_ststus'];
			$query->where('t_is_all_delivered_cancelled_ststus','=',$deliveredCancelledStstus);
		}
		if(isset($where['default_status']) && (!empty($where['default_status'])) ){
			$statusIds = $where['default_status'];
			$query->whereHas('goodInLogisticMaster.statusMaster' , function($query) use($statusIds) {
				$query->whereNotIn('i_status_id',$statusIds);
			});
		
		}
		if(isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
			
			$searchString = ( $likeData['searchBy'] );
			//$query->orWhere('v_goods_in_buyer_detail_no', 'like', '%' .$searchString . '%');
			
			$query->where(function ($q1) use($searchString){
				$q1->whereHas('goodInBuyerMaster' , function($query) use($searchString) {
					$allLikeColumns = [ 'v_goods_in_buyer_master_no' , 'v_po_sales_invoice_no' , 'v_brand' , 'v_invoice_no', 'v_buyer_comments' ];
				
					$query->where(function($q) use ($allLikeColumns,$searchString){
						foreach($allLikeColumns as $key => $allLikeColumn){
							$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
						}
					});
					/*
					 $query->orWhere('v_po_sales_invoice_no', 'like', '%' .$searchString . '%');
					 $query->orWhere('v_brand1', 'like', '%' .$searchString . '%');
					 $query->orWhere('v_payment_remark', 'like', '%' .$searchString . '%');
					 $query->orWhere('v_booking_ref_no', 'like', '%' .$searchString . '%');
					 $query->orWhere('v_collection_reference_no', 'like', '%' .$searchString . '%');
						$query->orWhere('v_goods_remarks1', 'like', '%' .$searchString . '%'); */
				});
						
				$q1->orWhere(function($q) use ($searchString){
					$q->orWhere('v_goods_in_buyer_detail_no', 'like', "%" .$searchString . "%");
				});
			});
		}
		
		if(session()->has('role') && session()->has('user_id') && !empty(session()->get('user_id')) && session()->get('role') != config('constants.ROLE_ADMIN')){
			$query->whereHas('goodInBuyerMaster', function($q1) use ($where){
				if( isset($where['loggedUserBuyerRoles']) && in_array( config('constants.BUYER') , $where['loggedUserBuyerRoles']  ) ){
					$q1->whereRaw( ( "( find_in_set('".session()->get('user_id')."',v_buyer_employee_ids) or find_in_set('".session()->get('user_id')."',v_user_buyer_ids) ) " ) );							
				}
			});
		}
		
		$query->orderBy('i_id', "DESC" ) ;
		
		$pageNo = ( ( isset($where['page']) && (!empty($where['page'])) ) ? $where['page'] : 1 );
		
		if( isset($where['count_record']) && ( ($where['count_record']) == true ) ){
			$data = $query->get();
		} else {
			if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
				$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
			} else {
				$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
			}
		}
		
		return $data;
	}
	
	public function getTotalLimitWareHouse( $whereData = [] , $likeData = [] , $additionalData = [] ){
		$defaultWhere = [
				'gbd.t_is_deleted != ' => 1,
				'gbd.t_is_all_delivered_cancelled_ststus' => 0, 
				'group_by' =>  'gdm.i_delivery_location_id',
				'gdm.e_pallet_box_type' => config('constants.PALLET')
		];
			
		$selectData = [
				DB::raw('SUM(gdm.i_no_of_pallet_box) as total_pallets'),
				'gdm.i_id',
				'gdm.i_delivery_location_id',
				'gdm.dt_delivery_date'
		];
			
		$joinData = [
				[
						'tableName' =>	config('constants.GOODS_IN_BUYER_MASTER_TABLE') . ' as gdm',
						'condition' =>	"gdm.i_id = gbd.i_goods_in_buyer_master_id",
				]
		];
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
		
		$tableName = config('constants.GOODS_IN_BUYER_DETAIL_TABLE'). ' as gbd';
		$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		return $data;
		
	}
	
}
