<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use App\ReportModel;
use App\CompanyMasterModel;
use App\Login;
use App\SupplierMasterModel;
use App\SupplierDetailModel;
use App\StatusMasterModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Response;
use App\WarehouseMasterModel;
use App\CountryMasterModel;
use App\LogisticPartnerDetailModel;
use App\FBASheeteDetailModel;
use App\UsWarehouseToAmazonDetailsModel;
use App\CountrytoPortEuropeDetailModel;
use App\GoodInBuyerMasterModel;
use App\GoodInLogisticMasterModel;
use App\LookupMaster;
use App\CurrencyMasterModel;
use Carbon\Carbon;

class ReportController extends MasterController
{
    //
	public function __construct(){
		//parent::__construct();
		$this->middleware('checklogin');
		$this->crudModel = new ReportModel();
		$this->folderName = config('constants.ADMIN_FOLDER'). 'tracking-goods-in/';
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->goodInBuyerDetailModel = new GoodInBuyerMasterModel();
		$this->goodInLogisticModel = new GoodInLogisticMasterModel();
	}
	
	public function trackingGoodsIn(){
	
		if(checkPermission(config('permission_constants.VIEW_TRACKING_GOODS_IN_REPORT')) != true){
			return redirect('access-denied');
		}
	
		$data = [];
		$data ['pageTitle'] = trans('messages.tracking-goods-in'). ' ' . trans('messages.report');
		$page = $this->defaultPage;
	
		$data['paymentStatusInfo'] = paymentStatus();
		$data['companyRecordDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['supplierRecordDetails'] = SupplierMasterModel::orderBy('v_supplier_name', 'ASC')->get();
		$data['supplierlocationDetails'] = SupplierDetailModel::with(['supplierMaster'])->get();
		$data['collectionDeliveryInfo'] = collectionDeliveryInfo();
		$data['deliveryTypeInfo'] = deliveryTypeInfo();
		
		$data['customsProcedureInfo'] = customsProcedureDropdown();
		$data['dangerousGoodsDetails'] = LookupMaster::where('v_module_name',config('constants.DANGEROUS_GOODS_LOOKUP'))->orderBy('v_value')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
		$data['userBuyerRecordDetails'] = Login::where('v_role' , config('constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['paymentTermsDetails'] = LookupMaster::where('v_module_name',config('constants.PAYMENT_TERMS_LOOKUP'))->orderBy('v_value')->get();
		$data['palletBoxInfo'] = typeInfo();
		
		$data['statusDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$warehouseWhere = [];
		$userLogin =  session()->get('user_id');
		$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
	
		if(count($userLoginDetails) > 0){
			if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
				$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0 );
				$warehouseWhere['i_id'] = $warehouseIds;
			}
		}
	
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['supplierCountryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
		$whereData = [];
		$whereData['t_is_active'] = 1;
		$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whereData){
			$query->where('t_is_active',$whereData);
		})->get();
		$data['readyForCollectionInfo'] = dangerousGoodsInfo();
		$data['statusMasterDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['goodsRemarksDetails'] = LookupMaster::where('v_module_name',config('constants.GOODS_REMARK_LOOKUP'))->where('t_is_deleted' , 0)->orderBy('v_value')->get();
		return view($this->folderName . 'tracking-goods-in')->with($data);
	}
	
	public function filter(Request $request){
	
		if ($request->ajax ()) {
			$whereData = $likeData  = $additionalData =  [];
				
			$draw = (!empty($request->input('draw')) ? $request->input('draw') : 1 );
				
			$offset = (!empty($request->input('start')) ? $request->input('start') : 0 );
				
			$limit = (!empty($request->input('length')) ? $request->input('length') : 10 );// Rows display per page
				
			$columnName = $columnIndex =  $columnSortOrder = '';
				
			$columnIndex = (!empty($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : '' )  ; // Column index
			$columnName = (!empty($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : '' );// Column name
			$columnSortOrder = (!empty($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : '' )  ;// asc or desc
				
			$searchValue = (!empty($_POST['search']['value']) ? $_POST['search']['value'] : '' );
				
			$page_no = (! empty ( $request->input ( 'page' ) )) ? ( int ) $request->input ( 'page' ) : 1;
			if (!empty($request->post('search_by_logistic_partner_name'))) {
				$searchByName = trim($request->post('search_by_logistic_partner_name'));
				$likeData ['gdm.v_po_sales_invoice_no'] = $searchByName;
				$likeData ['gdm.v_vendor_number'] = $searchByName;
				$likeData ['gdm.v_invoice_no'] = $searchByName;
				$likeData ['gdm.v_brand'] = $searchByName;
				$likeData ['gbd.v_goods_in_buyer_detail_no'] = $searchByName;
				$likeData ['glm.v_goods_in_logistic_master_no'] = $searchByName;
				$likeData ['gdm.v_buyer_comments'] = $searchByName;
				
				/* $likeData ['gdm.d_po_amount'] = $searchByName;
				$likeData ['glm.v_tracking_no'] = $searchByName;
				$likeData ['glm.v_tracking_link'] = $searchByName; */
					
			}
				
			/* if(!empty($request->post('search_payment_status') )){
				$whereData['gdm.e_payment_status'] =  trim($request->post('search_payment_status'));
					
			} */
				
			if (!empty($request->post('search_order_from_date'))) {
				$orderFromDate = dbDate($request->input('search_order_from_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_order_date) >=  '" . $orderFromDate . "'";
			}
			if (!empty($request->post('search_order_to_date'))) {
				$orderToDate = dbDate($request->input('search_order_to_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_order_date) <=  '" . $orderToDate . "'";
			}
			if (!empty($request->post('search_invoice_from_date'))) {
				$whereData['custom_function'][] =  "date(gdm.dt_invoice_date) >=  '" . dbDate($request->input('search_invoice_from_date')) . "'";
			}
			if (!empty($request->post('search_invoice_to_date'))) {
				$whereData['custom_function'][] =  "date(gdm.dt_invoice_date) <=  '" . dbDate($request->input('search_invoice_to_date')) . "'";
			}
			if( ( !empty($request->post('search_collection_delivery') ) ) && ( $request->post('search_collection_delivery') ) ){
				$whereData['gdm.e_collection_type'] =  trim($request->post('search_collection_delivery'));
					
			}
			/* if( ( !empty($request->post('search_delivery_type') ) ) && ( $request->post('search_delivery_type') ) ){
				$whereData['gdm.e_delivery_type'] =  trim($request->post('search_delivery_type'));
			} */
			if(!empty($request->post('search_book_by') )){
				if( $request->post('search_book_by') == "None" ){
					$whereData['null_column'] =  'glm.i_id';
					//$whereData['gdm.e_collection_type'] =  config('constants.COLLECTION');
				} else {
					$whereData['glm.i_book_employee_id'] =  (int)Wild_tiger::decode($request->post('search_book_by'));
				}
			}
				
			if(!empty($request->post('search_logistic_partner') )){
				$whereData['glm.i_logistic_partner_id'] =  (int)Wild_tiger::decode($request->post('search_logistic_partner'));
					
			}
			if (!empty($request->post('search_collection_form_date'))) {
				$collectionFromDate = dbDate($request->input('search_collection_form_date'));
				$whereData['custom_function'][] =  "date(glm.dt_collection_date) >=  '" . $collectionFromDate . "'";
			}
			if (!empty($request->post('search_collection_to_date'))) {
				$collectionToDate = dbDate($request->input('search_collection_to_date'));
				$whereData['custom_function'][] =  "date(glm.dt_collection_date) <=  '" . $collectionToDate . "'";
			}
				
			if (!empty($request->post('search_delivery_form_date'))) {
				$deliveryFromDate = dbDate($request->input('search_delivery_form_date'));
				$whereData['custom_function'][] =  "date(glm.dt_delivery_date) >=  '" . $deliveryFromDate . "'";
			}
			if (!empty($request->post('search_delivery_to_date'))) {
				$deliveryToDate = dbDate($request->input('search_delivery_to_date'));
				$whereData['custom_function'][] =  "date(glm.dt_delivery_date) <=  '" . $deliveryToDate . "'";
			}
			//remove due to duplicate status
			if( ( !empty($request->post('search_status') ) ) && ( $request->post('search_status') ) ){
				//$whereData['glm.i_status_id'] =  (int)Wild_tiger::decode($request->post('search_status'));
			}
				
			if (!empty($request->post('search_buyer_company'))) {
				$searchBuyerCompanyIds =explode("," , $request->input('search_buyer_company') );
					
				if(!empty($searchBuyerCompanyIds)){
					$searchBuyerCompanyIds = array_map(function($searchBuyerCompanyId){
						return (int)Wild_tiger::decode($searchBuyerCompanyId);
					}, $searchBuyerCompanyIds);
				}
				$whereData['custom_function'][] =  "( gdm.i_buyer_company_id in (".implode("," , $searchBuyerCompanyIds )."))";
			}
				
			if (!empty($request->post('search_user_company'))) {
				$searchUserCompanyIds =explode("," , $request->input('search_user_company') );
					
				if(!empty($searchUserCompanyIds)){
					$searchUserCompanyIds = array_map(function($searchUserCompanyId){
						return (int)Wild_tiger::decode($searchUserCompanyId);
					}, $searchUserCompanyIds);
				}
				if(!empty($searchUserCompanyIds)){
					$customerSearch = " ( ";
					if(!empty($searchUserCompanyIds)){
						foreach($searchUserCompanyIds as $userCompany){
							$customerSearch .= "find_in_set('".$userCompany."',gdm.v_user_company_ids) OR ";
						}
						$customerSearch = rtrim($customerSearch,"OR ");
						$customerSearch .= " ) ";
						$whereData['custom_function'][] = $customerSearch;
					}
				}
			}
				
			if (!empty($request->post('search_buyer_name'))) {
				$searchBuyerNameIds =explode("," , $request->input('search_buyer_name') );
					
				if(!empty($searchBuyerNameIds)){
					$searchBuyerNameIds = array_map(function($searchBuyerNameId){
						return (int)Wild_tiger::decode($searchBuyerNameId);
					}, $searchBuyerNameIds);
							
						//added multiple buyer selected name
						if(!empty($searchBuyerNameIds)){
							$customerSearch = " ( ";
							foreach($searchBuyerNameIds as $searchBuyerNameId){
								$customerSearch .= "find_in_set('".$searchBuyerNameId."',gdm.v_buyer_employee_ids) OR ";
							}
							$customerSearch = rtrim($customerSearch,"OR ");
							$customerSearch .= " ) ";
							$whereData['custom_function'][] = $customerSearch;
						}
				}
				//$whereData['custom_function'][] =  "( gdm.i_buyer_employee_id in (".implode("," , $searchBuyerNameIds )."))";
			}
				
			if (!empty($request->post('search_supplier_name'))) {
				$searchSupplierMasterIds =explode("," , $request->input('search_supplier_name') );
					
				if(!empty($searchSupplierMasterIds)){
					$searchSupplierMasterIds = array_map(function($searchSupplierMasterId){
						return (int)Wild_tiger::decode($searchSupplierMasterId);
					}, $searchSupplierMasterIds);
				}
				$whereData['custom_function'][] =  "( gdm.i_main_supplier_id in (".implode("," , $searchSupplierMasterIds )."))";
			}
				
			if (!empty($request->post('search_supplier_location'))) {
				$searchSupplierLocationIds =explode("," , $request->input('search_supplier_location') );
					
				if(!empty($searchSupplierLocationIds)){
					$searchSupplierLocationIds = array_map(function($searchSupplierLocationId){
						return (int)Wild_tiger::decode($searchSupplierLocationId);
					}, $searchSupplierLocationIds);
				}
				$whereData['custom_function'][] =  "( gbd.i_goods_in_buyer_supplier_id in (".implode("," , $searchSupplierLocationIds )."))";
			}
			if( ( !empty($request->post('search_logistic_delivery_type') ) ) && ( $request->post('search_logistic_delivery_type') ) ){
				$whereData['gld.e_collection_delivery_type'] =  trim($request->post('search_logistic_delivery_type'));
					
			}
			if(!empty($request->post('search_delivery_collection_location'))){
				$whereData['gdm.i_delivery_location_id'] = (int)Wild_tiger::decode($request->post('search_delivery_collection_location'));
			}
			/* if(!empty($request->post('search_delivery_location'))){
			 $whereData['gdm.i_delivery_location_id'] = (int)Wild_tiger::decode($request->post('search_delivery_location'));
				} */
			/* if(!empty($request->post('search_supplier_country'))){
			 $whereData['sd.i_country_id'] = (int)Wild_tiger::decode($request->post('search_supplier_country'));
				} */
			if (!empty($request->post('search_supplier_country'))) {
				$searchSupplierCountryIds =explode("," , $request->input('search_supplier_country') );
					
				if(!empty($searchSupplierCountryIds)){
					$searchSupplierCountryIds = array_map(function($searchSupplierCountryId){
						return (int)Wild_tiger::decode($searchSupplierCountryId);
					}, $searchSupplierCountryIds);
				}
				$whereData['custom_function'][] =  "( sd.i_country_id in (".implode("," , $searchSupplierCountryIds )."))";
			}
			if(!empty($request->post('search_ready_for_collection') )){
				$whereData['gdm.e_ready_for_collection_status'] = ($request->post('search_ready_for_collection'));
			}
			# new filter add same as good in buyer
			//change key due sync with all filter
			if(!empty($request->post('search_status') )){
				$allStatusIds = explode("," , $request->post('search_status') );
				if(!empty($allStatusIds)){
					$allStatusIds = array_map(function($allStatusId){
						return (int)Wild_tiger::decode($allStatusId);
					}, $allStatusIds);
				}
				if(!empty($allStatusIds)){
					# multiple value
					$additionalData['whereIn'] = [ 'glm.i_status_id' , $allStatusIds ];
				}
	
					
			} else {
				$whereData['gbd.t_is_all_delivered_cancelled_ststus'] = 0;
			}
				
			$warehouseWhere = [];
			$userLogin =  session()->get('user_id');
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
				
			if(count($userLoginDetails) > 0){
				if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
					$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :0 );
					$warehouseWhere['i_id'] = $warehouseIds;
				}
			}
				
			if(!empty($warehouseWhere['i_id'])){
				$whereData['wh.i_id'] = (isset($warehouseWhere['i_id']) ? $warehouseWhere['i_id'] :'');
			}
			
			if (!empty($request->post('search_po_creation_from_date'))) {
				$poCreationFromDate = dbDate($request->input('search_po_creation_from_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_po_creation_date) >=  '" . $poCreationFromDate . "'";
			}
			if (!empty($request->post('search_po_creation_to_date'))) {
				$poCreationToDate = dbDate($request->input('search_po_creation_to_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_po_creation_date) <=  '" . $poCreationToDate . "'";
			}
			
			if(!empty($request->post('search_customs_procedure') )){
				$whereData['gdm.e_customs_procedure'] = trim($request->post('search_customs_procedure'));
			}
			
			if(!empty($request->post('search_dangerous_goods'))){
				$searchDangerousGoodsid = (int) Wild_tiger::decode($request->post('search_dangerous_goods'));
				$whereData['gdm.i_dangerous_goods_id'] = $searchDangerousGoodsid;
			}
			
			if(!empty($request->post('search_currency_code'))){
				$searchCurrencyCodeid = (int) Wild_tiger::decode($request->post('search_currency_code'));
				$whereData['gdm.i_po_currency_id'] = $searchCurrencyCodeid;
			}
				
			if (!empty($request->post('search_user_buyer_name'))) {
				$searchUserBuyerNameIds =explode("," , $request->input('search_user_buyer_name') );
					
				if(!empty($searchUserBuyerNameIds)){
					$searchUserBuyerNameIds = array_map(function($searchUserBuyerNameId){
						return (int)Wild_tiger::decode($searchUserBuyerNameId);
					}, $searchUserBuyerNameIds);
							
						//added multiple buyer selected name
						if(!empty($searchUserBuyerNameIds)){
							$customerSearch = " ( ";
							foreach($searchUserBuyerNameIds as $searchUserBuyerNameId){
								$customerSearch .= "find_in_set('".$searchUserBuyerNameId."',gdm.v_user_buyer_ids) OR ";
							}
							$customerSearch = rtrim($customerSearch,"OR ");
							$customerSearch .= " ) ";
							$whereData['custom_function'][] = $customerSearch;
						}
				}
				//$whereData['custom_function'][] =  "( gdm.i_buyer_employee_id in (".implode("," , $searchBuyerNameIds )."))";
			}
			
			if(!empty($request->post('search_payment_terms'))){
				$searchPaymentTermsid = (int) Wild_tiger::decode($request->post('search_payment_terms'));
				$whereData['gdm.i_payment_terms_id'] = $searchPaymentTermsid;
			}
			
			if (!empty($request->post('search_payment_from_date'))) {
				$paymentFromDate = dbDate($request->input('search_payment_from_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_payment_date) >=  '" . $paymentFromDate . "'";
			}
			if (!empty($request->post('search_payment_to_date'))) {
				$paymentToDate = dbDate($request->input('search_payment_to_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_payment_date) <=  '" . $paymentToDate . "'";
			}
			
			if (!empty($request->post('search_actual_payment_from_date'))) {
				$whereData['custom_function'][] =  "date(gdm.dt_actual_payment_date) >=  '" . dbDate($request->input('search_actual_payment_from_date')) . "'";
			}
			if (!empty($request->post('search_actual_payment_to_date'))) {
				$whereData['custom_function'][] =  "date(gdm.dt_actual_payment_date) <=  '" . dbDate($request->input('search_actual_payment_to_date')) . "'";
			}
			
			if (!empty($request->post('search_buyer_delivery_from_date'))) {
				$buyerDeliveryFromDate = dbDate($request->input('search_buyer_delivery_from_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_delivery_date) >=  '" . $buyerDeliveryFromDate . "'";
			}
			if (!empty($request->post('search_buyer_delivery_to_date'))) {
				$buyerDeliveryToDate = dbDate($request->input('search_buyer_delivery_to_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_delivery_date) <=  '" . $buyerDeliveryToDate . "'";
			}
			
			if (!empty($request->post('search_goods_in_from_date'))) {
				$whereData['custom_function'][] =  "date(glm.dt_goods_in_date) >=  '" . dbDate($request->input('search_goods_in_from_date')) . "'";
			}
			if (!empty($request->post('search_goods_in_to_date'))) {
				$whereData['custom_function'][] =  "date(glm.dt_goods_in_date) <=  '" . dbDate($request->input('search_goods_in_to_date')) . "'";
			}
			
			if (!empty($request->post('search_pallets_boxes_type'))) {
				// $whereData['gdm.e_pallet_box_type'] = trim($request->input('search_pallets_boxes_type'));
				$whereData['custom_function'][] =  " ( (gdm.e_collection_type = '".config('constants.DELIVERY')."' AND glm.i_no_of_pallet_box >= 0 AND glm.e_dimension = '".trim($request->input('search_pallets_boxes_type'))."') OR (gdm.e_collection_type = '".config('constants.DELIVERY')."' AND glm.i_no_of_pallet_box IS NULL AND gdm.e_pallet_box_type = '".trim($request->input('search_pallets_boxes_type'))."') OR (gdm.e_collection_type = '".config('constants.COLLECTION')."' AND gdm.e_pallet_box_type = '".trim($request->input('search_pallets_boxes_type'))."') ) ";
			}
			
			if (!empty($request->post('search_no_of_pallets_boxes'))) {
				$whereData['gdm.i_no_of_pallet_box'] = trim($request->input('search_no_of_pallets_boxes'));
			}
			
			if(!empty($request->post('search_goods_remark'))){
				$goodsRemarkIds = explode("," , $request->post('search_goods_remark') );
				if(!empty($goodsRemarkIds)){
					$allGoodsRemarkIds = array_map(function($allGoodsRemarkId){
						return (int)Wild_tiger::decode($allGoodsRemarkId);
					}, $goodsRemarkIds);
					
					$goodsRemarkSearch = "( ";
					if (!empty($allGoodsRemarkIds)){
						foreach($allGoodsRemarkIds as $goodsRemarkId){
							$goodsRemarkSearch .= "find_in_set(". $goodsRemarkId." , gdm.v_goods_remark_ids) OR ";
						}
						$goodsRemarkSearch = rtrim($goodsRemarkSearch,"OR ");
						$goodsRemarkSearch .= " )";
						$whereData['custom_function'][] = $goodsRemarkSearch;
					}
				}
			}
			
			$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
			$exportTypeAction = (!empty($request->input('custom_export_type_action')) ? trim($request->input('custom_export_type_action')) : '');
				
			if ($exportAction == 'export') {
				$finalExportData = [];
	
				$summaryData = $individualTabSummaryDetails =[];
	
				$getAllCountryDetails = [];
				if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
					$finalExportData['Summary'] = [];
					$getAllCountryDetails = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name', 'v_country_code'],[ 't_is_deleted !=' => 1 , 'order_by' => [ 'v_country_name' => 'asc' ] ]);
				}
					
				$getExportRecordDetails = $this->crudModel->getTrackingGoodInDetail($whereData, $likeData , $additionalData );
	
				if (!empty($getExportRecordDetails)) {
					$excelIndex = 0;
						
					foreach ($getExportRecordDetails as $getExportRecordDetail) {
							
						$rowExcelData = [];
	
						if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
							$warehouseCode = (isset($getExportRecordDetail->v_warehouse_code) && !empty($getExportRecordDetail->v_warehouse_code) ? $getExportRecordDetail->v_warehouse_code :'' );
							$supplierCountryCode = ( isset($getExportRecordDetail->v_country_code) && !empty($getExportRecordDetail->v_country_code) ?  ( $getExportRecordDetail->v_country_code ) :'' ) ;
								
							if(isset($warehouseCode) && !empty($warehouseCode)){
								$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_goods_in_buyer_detail_no) && !empty($getExportRecordDetail->v_goods_in_buyer_detail_no) ? $getExportRecordDetail->v_goods_in_buyer_detail_no : '' );
								$rowExcelData['logistic_entry_no'] = ( ( isset($getExportRecordDetail->v_goods_in_logistic_master_no) && !empty($getExportRecordDetail->v_goods_in_logistic_master_no) ?  ($getExportRecordDetail->v_goods_in_logistic_master_no) :'' ) ) ;
								$rowExcelData['buyer_company'] =  ( isset($getExportRecordDetail->v_company_name) && !empty($getExportRecordDetail->v_company_name) ? $getExportRecordDetail->v_company_name :'' );
								$rowExcelData['user_company'] = ( isset($getExportRecordDetail->buyer_user_company_name) && !empty($getExportRecordDetail->buyer_user_company_name) ? $getExportRecordDetail->buyer_user_company_name :'' );
								$rowExcelData['supplier_name'] = ( isset($getExportRecordDetail->v_supplier_name) && !empty($getExportRecordDetail->v_supplier_name) ?  ( $getExportRecordDetail->v_supplier_name )  : '' );
								$rowExcelData['supplier_country'] = ( isset($getExportRecordDetail->v_country_name) && !empty($getExportRecordDetail->v_country_name) ?  ( $getExportRecordDetail->v_country_name ) :'' ) ;
								$rowExcelData['po_no./_sales_invoice_no.'] = ( isset($getExportRecordDetail->v_po_sales_invoice_no) && !empty($getExportRecordDetail->v_po_sales_invoice_no) ?  ( $getExportRecordDetail->v_po_sales_invoice_no )  :'' );
								$rowExcelData['po_amount_(gbp)'] = (isset($getExportRecordDetail->d_po_amount) && !empty($getExportRecordDetail->d_po_amount) ? round($getExportRecordDetail->d_po_amount, 2) : 0); // .' '.(isset($getExportRecordDetail->po_currency_code) && !empty($getExportRecordDetail->po_currency_code) ? $getExportRecordDetail->po_currency_code : '' );
								
								if(isset($getExportRecordDetail->po_gbp_conversation_rate) && !empty($getExportRecordDetail->po_gbp_conversation_rate) && $getExportRecordDetail->po_gbp_conversation_rate > 0){
									$rowExcelData['po_amount_(gbp)'] = $rowExcelData['po_amount_(gbp)'] * $getExportRecordDetail->po_gbp_conversation_rate;
								}
								
								$collectPalletBoxNo = (isset($getExportRecordDetail->no_of_pallet_box_final) && !empty($getExportRecordDetail->no_of_pallet_box_final) ? $getExportRecordDetail->no_of_pallet_box_final : 0);
								$collectPalletBoxType = (isset($getExportRecordDetail->type_of_pallet_box_final) && !empty($getExportRecordDetail->type_of_pallet_box_final) ? $getExportRecordDetail->type_of_pallet_box_final : '');
								
								$summaryData[$warehouseCode][$supplierCountryCode]['total_amount'] = ((isset($summaryData[$warehouseCode][$supplierCountryCode]['total_amount']) && !empty($summaryData[$warehouseCode][$supplierCountryCode]['total_amount'])) ? $summaryData[$warehouseCode][$supplierCountryCode]['total_amount'] : 0) + ((isset($rowExcelData['po_amount_(gbp)']) && !empty($rowExcelData['po_amount_(gbp)']) ? round($rowExcelData['po_amount_(gbp)'], 2) : 0));
								$summaryData[$warehouseCode][$supplierCountryCode]['total_pallets'] = ((isset($summaryData[$warehouseCode][$supplierCountryCode]['total_pallets']) && !empty($summaryData[$warehouseCode][$supplierCountryCode]['total_pallets'])) ? $summaryData[$warehouseCode][$supplierCountryCode]['total_pallets'] : 0) + (isset($collectPalletBoxType) && !empty($collectPalletBoxType) && $collectPalletBoxType == config('constants.PALLET') && isset($collectPalletBoxNo) && !empty($collectPalletBoxNo) ? $collectPalletBoxNo : 0);
								$summaryData[$warehouseCode][$supplierCountryCode]['total_boxes'] = ((isset($summaryData[$warehouseCode][$supplierCountryCode]['total_boxes']) && !empty($summaryData[$warehouseCode][$supplierCountryCode]['total_boxes'])) ? $summaryData[$warehouseCode][$supplierCountryCode]['total_boxes'] : 0) + (isset($collectPalletBoxType) && !empty($collectPalletBoxType) && $collectPalletBoxType == config('constants.BOX') && isset($collectPalletBoxNo) && !empty($collectPalletBoxNo) ? $collectPalletBoxNo : 0);
								
								$rowExcelData['po_amount_(gbp)'] = isset($rowExcelData['po_amount_(gbp)']) && !empty($rowExcelData['po_amount_(gbp)']) ? $rowExcelData['po_amount_(gbp)'] : '';
								//$rowExcelData['total_pallet'] = isset($getExportRecordDetail->e_pallet_box_type) && !empty($getExportRecordDetail->e_pallet_box_type) && $getExportRecordDetail->e_pallet_box_type == config('constants.PALLET') && isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ? $getExportRecordDetail->i_no_of_pallet_box : '';
								//$rowExcelData['total_box'] = isset($getExportRecordDetail->e_pallet_box_type) && !empty($getExportRecordDetail->e_pallet_box_type) && $getExportRecordDetail->e_pallet_box_type == config('constants.BOX') && isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ? $getExportRecordDetail->i_no_of_pallet_box : '';
								$rowExcelData['collection_/_delivery'] = ( ( isset($getExportRecordDetail->e_collection_type) && !empty($getExportRecordDetail->e_collection_type) ?  ($getExportRecordDetail->e_collection_type) :'' ) ) ;
								$rowExcelData['mode_of_transport'] = ( ( isset($getExportRecordDetail->e_mode_of_transport) && !empty($getExportRecordDetail->e_mode_of_transport) ?  ($getExportRecordDetail->e_mode_of_transport) :'' ) ) ;
								$rowExcelData['buyer_delivery_date'] = ( (isset($getExportRecordDetail->gib_delivery_date) && !empty($getExportRecordDetail->gib_delivery_date)) ? clientDate($getExportRecordDetail->gib_delivery_date) : '');
								$rowExcelData['collection_date'] = ( isset($getExportRecordDetail->dt_collection_date) && !empty($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) :'' );
								$rowExcelData['logistic_delivery_date'] = ( isset($getExportRecordDetail->dt_delivery_date) && !empty($getExportRecordDetail->dt_delivery_date) ?  clientDate($getExportRecordDetail->dt_delivery_date) :'' );
								$rowExcelData['transporter_invoice_info'] =  (isset($getExportRecordDetail->invoice_details) && !empty($getExportRecordDetail->invoice_details) ? ( $getExportRecordDetail->invoice_details ) :'' );
								$rowExcelData['final_total_(gbp)'] = ( isset($getExportRecordDetail->d_invoice_total) && !empty($getExportRecordDetail->d_invoice_total) ?  round($getExportRecordDetail->d_invoice_total, 2) : '' );
	
								$finalExportData[$warehouseCode][] = $rowExcelData;
	
								$individualTabSummaryDetails[$warehouseCode][2] =  (isset($individualTabSummaryDetails[$warehouseCode][2]) && !empty($individualTabSummaryDetails[$warehouseCode][2]) ? $individualTabSummaryDetails[$warehouseCode][2] : 0) + 1;
								$individualTabSummaryDetails[$warehouseCode][3] =  (isset($individualTabSummaryDetails[$warehouseCode][3]) && !empty($individualTabSummaryDetails[$warehouseCode][3]) ? $individualTabSummaryDetails[$warehouseCode][3] : 0) + (isset($rowExcelData['collection_/_delivery']) && !empty($rowExcelData['collection_/_delivery']) && $rowExcelData['collection_/_delivery'] == config('constants.COLLECTION') ? 1 : 0);
								$individualTabSummaryDetails[$warehouseCode][4] =  (isset($individualTabSummaryDetails[$warehouseCode][4]) && !empty($individualTabSummaryDetails[$warehouseCode][4]) ? $individualTabSummaryDetails[$warehouseCode][4] : 0) + (isset($rowExcelData['collection_/_delivery']) && !empty($rowExcelData['collection_/_delivery']) && $rowExcelData['collection_/_delivery'] == config('constants.DELIVERY') ? 1 : 0);
								$individualTabSummaryDetails[$warehouseCode][5] =  '';
								$individualTabSummaryDetails[$warehouseCode][6] =  (isset($individualTabSummaryDetails[$warehouseCode][6]) && !empty($individualTabSummaryDetails[$warehouseCode][6]) ? $individualTabSummaryDetails[$warehouseCode][6] : 0) + (isset($rowExcelData['po_amount_(gbp)']) && !empty($rowExcelData['po_amount_(gbp)']) ? $rowExcelData['po_amount_(gbp)'] : 0);
							}
						} else {
							$rowExcelData['sr_no'] = ++$excelIndex;
							$rowExcelData['po_number'] = ( isset($getExportRecordDetail->v_po_sales_invoice_no) && !empty($getExportRecordDetail->v_po_sales_invoice_no) ?  ($getExportRecordDetail->v_po_sales_invoice_no)  :'' );
							$rowExcelData['vendor_number'] = ( isset($getExportRecordDetail->v_vendor_number) && !empty($getExportRecordDetail->v_vendor_number) ?  ($getExportRecordDetail->v_vendor_number)  :'' );
							$rowExcelData['supplier_name'] = (isset($getExportRecordDetail->v_supplier_name) && !empty($getExportRecordDetail->v_supplier_name) ? $getExportRecordDetail->v_supplier_name : '');
							$rowExcelData['supplier_country'] = (isset($getExportRecordDetail->v_country_name) && !empty($getExportRecordDetail->v_country_name) ? $getExportRecordDetail->v_country_name : '' );
							$rowExcelData['invoice_number'] = ( isset($getExportRecordDetail->v_invoice_no) && !empty($getExportRecordDetail->v_invoice_no) ?  ($getExportRecordDetail->v_invoice_no)  :'' );
							$rowExcelData['buyer_company'] = (isset($getExportRecordDetail->v_company_name) && !empty($getExportRecordDetail->v_company_name) ? $getExportRecordDetail->v_company_name :'');
							$rowExcelData['user_company'] = (isset($getExportRecordDetail->buyer_user_company_name) && !empty($getExportRecordDetail->buyer_user_company_name) ? $getExportRecordDetail->buyer_user_company_name :'');
							$rowExcelData['buyer_name'] = (isset($getExportRecordDetail->goods_buyer_name) && !empty($getExportRecordDetail->goods_buyer_name) ? $getExportRecordDetail->goods_buyer_name :'');
							$rowExcelData['user_buyer_name'] = (isset($getExportRecordDetail->user_buyer_name) && !empty($getExportRecordDetail->user_buyer_name) ? $getExportRecordDetail->user_buyer_name :'');
							$rowExcelData['goods_remark'] = (isset($getExportRecordDetail->goods_remark_value) && !empty($getExportRecordDetail->goods_remark_value) ? $getExportRecordDetail->goods_remark_value :'');
							$rowExcelData['brand'] = (isset($getExportRecordDetail->v_brand) && !empty($getExportRecordDetail->v_brand) ? $getExportRecordDetail->v_brand :'');
							$rowExcelData['customs_procedure'] = (isset($getExportRecordDetail->e_customs_procedure) && !empty($getExportRecordDetail->e_customs_procedure) ? $getExportRecordDetail->e_customs_procedure :'');
							$rowExcelData['dangerous_goods'] = (isset($getExportRecordDetail->dangerous_goods_value) && !empty($getExportRecordDetail->dangerous_goods_value) ? $getExportRecordDetail->dangerous_goods_value :'');
							
							$rowExcelData['po_amount_(gbp)'] = ( isset($getExportRecordDetail->d_po_amount) && !empty($getExportRecordDetail->d_po_amount) ? $getExportRecordDetail->d_po_amount : '' );
							$rowExcelData['po_amount_with_vat(gbp)'] = ( isset($getExportRecordDetail->d_po_amount_with_vat) && !empty($getExportRecordDetail->d_po_amount_with_vat) ? $getExportRecordDetail->d_po_amount_with_vat : '' );
							
							if(isset($getExportRecordDetail->po_gbp_conversation_rate) && !empty($getExportRecordDetail->po_gbp_conversation_rate) && $getExportRecordDetail->po_gbp_conversation_rate > 0){
								$rowExcelData['po_amount_(gbp)'] = ( isset($getExportRecordDetail->d_po_amount) && !empty($getExportRecordDetail->d_po_amount) ? $getExportRecordDetail->d_po_amount * $getExportRecordDetail->po_gbp_conversation_rate : '' );
								$rowExcelData['po_amount_with_vat(gbp)'] = ( isset($getExportRecordDetail->d_po_amount_with_vat) && !empty($getExportRecordDetail->d_po_amount_with_vat) ? $getExportRecordDetail->d_po_amount_with_vat * $getExportRecordDetail->po_gbp_conversation_rate : '' );
							}
							
							$rowExcelData['currency_code'] = (isset($getExportRecordDetail->po_currency_code) && !empty($getExportRecordDetail->po_currency_code) ? ( $getExportRecordDetail->po_currency_code ) :'' );
							$rowExcelData['payment_terms'] = (isset($getExportRecordDetail->payment_term_value) && !empty($getExportRecordDetail->payment_term_value) ? ( $getExportRecordDetail->payment_term_value ) :'' );
							$rowExcelData['collection_/_delivery'] = (isset($getExportRecordDetail->e_collection_type) && !empty($getExportRecordDetail->e_collection_type) ? $getExportRecordDetail->e_collection_type : '');
							$rowExcelData['mode_of_transport'] = (isset($getExportRecordDetail->e_mode_of_transport) && !empty($getExportRecordDetail->e_mode_of_transport) ? $getExportRecordDetail->e_mode_of_transport : '');
							$rowExcelData['delivery_location'] = (isset($getExportRecordDetail->v_warehouse_name) && !empty($getExportRecordDetail->v_warehouse_name) ? $getExportRecordDetail->v_warehouse_name  .(isset($getExportRecordDetail->v_warehouse_code) && !empty($getExportRecordDetail->v_warehouse_code) ? ' (' .( $getExportRecordDetail->v_warehouse_code ) .')' :'' ) :'' );
							$rowExcelData['transporter_invoice_info'] =  (isset($getExportRecordDetail->invoice_details) && !empty($getExportRecordDetail->invoice_details) ? ( $getExportRecordDetail->invoice_details ) :'' );
							$rowExcelData['transporter_invoice_amount_(GBP)'] = (isset($getExportRecordDetail->d_invoice_total) && !empty($getExportRecordDetail->d_invoice_total) ? $getExportRecordDetail->d_invoice_total : '');
							$rowExcelData['po_creation_date'] = (isset($getExportRecordDetail->dt_po_creation_date)  && !empty($getExportRecordDetail->dt_po_creation_date) ? clientDate($getExportRecordDetail->dt_po_creation_date) : '');
							$rowExcelData['order_date'] = (isset($getExportRecordDetail->dt_order_date)  && !empty($getExportRecordDetail->dt_order_date) ? clientDate($getExportRecordDetail->dt_order_date) : '');
							$rowExcelData['invoice_date'] = (isset($getExportRecordDetail->dt_invoice_date)  && !empty($getExportRecordDetail->dt_invoice_date) ? clientDate($getExportRecordDetail->dt_invoice_date) : '');
							$rowExcelData['payment_date'] = (isset($getExportRecordDetail->dt_payment_date)  && !empty($getExportRecordDetail->dt_payment_date) ? clientDate($getExportRecordDetail->dt_payment_date) : '');
							$rowExcelData['actual_payment_date'] = (isset($getExportRecordDetail->dt_actual_payment_date)  && !empty($getExportRecordDetail->dt_actual_payment_date) ? clientDate($getExportRecordDetail->dt_actual_payment_date) : '');
							$rowExcelData['collection_date'] = (isset($getExportRecordDetail->dt_collection_date) && !empty($getExportRecordDetail->dt_collection_date) ? clientDate($getExportRecordDetail->dt_collection_date) : '');
							$rowExcelData['buyer_delivery_date'] = (isset($getExportRecordDetail->gib_delivery_date) && !empty($getExportRecordDetail->gib_delivery_date) ? clientDate($getExportRecordDetail->gib_delivery_date) : '');
							$rowExcelData['logistic_delivery_date'] = (isset($getExportRecordDetail->dt_delivery_date) && !empty($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '');
							$rowExcelData['goods_in_date'] = (isset($getExportRecordDetail->dt_goods_in_date) && !empty($getExportRecordDetail->dt_goods_in_date) ? clientDate($getExportRecordDetail->dt_goods_in_date) : '');
							
							$paymentVsCollectionDateDiff = 0;
							if(isset($rowExcelData['collection_date']) && !empty($rowExcelData['collection_date']) && isset($rowExcelData['payment_date']) && !empty($rowExcelData['payment_date'])){
								$collectionDate = Carbon::parse(dbDate($rowExcelData['collection_date']));
								$paymentDate = Carbon::parse(dbDate($rowExcelData['payment_date']));
							
								$paymentVsCollectionDateDiff = $collectionDate->diffInDays($paymentDate);
							}							
							$rowExcelData['payment_vs_collection_date_diff.'] = (isset($paymentVsCollectionDateDiff) && !empty(($paymentVsCollectionDateDiff) ? $paymentVsCollectionDateDiff : ''));
							
							$paymentVsLogisticDeliveryDateDiff = 0;
							if(isset($rowExcelData['logistic_delivery_date']) && !empty($rowExcelData['logistic_delivery_date']) && isset($rowExcelData['payment_date']) && !empty($rowExcelData['payment_date'])){
								$logisticDeliveryDate = Carbon::parse(dbDate($rowExcelData['logistic_delivery_date']));
								$paymentDate = Carbon::parse(dbDate($rowExcelData['payment_date']));
									
								$paymentVsLogisticDeliveryDateDiff = $logisticDeliveryDate->diffInDays($paymentDate);
							}
							
							$rowExcelData['payment_vs_logi._delivery_date_diff.'] = (isset($paymentVsLogisticDeliveryDateDiff) && !empty($paymentVsLogisticDeliveryDateDiff) ? $paymentVsLogisticDeliveryDateDiff : '');
							
							$collectionVsLogisticDeliveryDateDiff = 0;
							if(isset($rowExcelData['logistic_delivery_date']) && !empty($rowExcelData['logistic_delivery_date']) && isset($rowExcelData['collection_date']) && !empty($rowExcelData['collection_date'])){
								$logisticDeliveryDate = Carbon::parse(dbDate($rowExcelData['logistic_delivery_date']));
								$collectionDate = Carbon::parse(dbDate($rowExcelData['collection_date']));
									
								$collectionVsLogisticDeliveryDateDiff = $logisticDeliveryDate->diffInDays($collectionDate);
							}
							
							$rowExcelData['collection_vs_logi._delivery_date_diff.'] = (isset($collectionVsLogisticDeliveryDateDiff) && !empty($collectionVsLogisticDeliveryDateDiff) ? $collectionVsLogisticDeliveryDateDiff : '');
							$rowExcelData['total_units'] = (isset($getExportRecordDetail->i_total_units) && !empty($getExportRecordDetail->i_total_units) ? $getExportRecordDetail->i_total_units : '');;
							
							$collectPalletBoxNo = (isset($getExportRecordDetail->no_of_pallet_box_final) && !empty($getExportRecordDetail->no_of_pallet_box_final) ? $getExportRecordDetail->no_of_pallet_box_final : 0);
							$collectPalletBoxType = (isset($getExportRecordDetail->type_of_pallet_box_final) && !empty($getExportRecordDetail->type_of_pallet_box_final) ? $getExportRecordDetail->type_of_pallet_box_final : '');
							
							$rowExcelData['pallet_/_box'] = (isset($collectPalletBoxType) && !empty($collectPalletBoxType) ? $collectPalletBoxType : '');						
							$rowExcelData['no._of_pallet_/_box'] = (isset($collectPalletBoxNo) && !empty($collectPalletBoxNo) ? $collectPalletBoxNo : '');
							$rowExcelData['buyer_comments'] = (isset($getExportRecordDetail->v_buyer_comments) && !empty($getExportRecordDetail->v_buyer_comments) ? $getExportRecordDetail->v_buyer_comments : '');
							$rowExcelData['warehouse_comments'] = (isset($getExportRecordDetail->v_status_comment) && !empty($getExportRecordDetail->v_status_comment) ? $getExportRecordDetail->v_status_comment : '');
							//$totalTrasportPrice = (isset($invoiceDetails) && !empty($invoiceDetails) && count($invoiceDetails) > 0 ? collect($invoiceDetails)->sum('d_final_charge') : 0);
							//$totalBoxesAndPallets = (isset($detailInfo) && !empty($detailInfo) && count($detailInfo) > 0 ? collect($detailInfo)->sum('i_no_of_pallet_box') : 0);
							$unitCost = ((isset($rowExcelData['transporter_invoice_amount_(GBP)']) && !empty($rowExcelData['transporter_invoice_amount_(GBP)']) ? $rowExcelData['transporter_invoice_amount_(GBP)'] : 0) / (isset($rowExcelData['total_units']) && !empty($rowExcelData['total_units']) ? $rowExcelData['total_units'] : 1));
							
							// pallet and box wise unit cost
							$finalUnitCost = ((isset($rowExcelData['transporter_invoice_amount_(GBP)']) && !empty($rowExcelData['transporter_invoice_amount_(GBP)']) ? $rowExcelData['transporter_invoice_amount_(GBP)'] : 0) / (isset($rowExcelData['no._of_pallet_/_box']) && !empty($rowExcelData['no._of_pallet_/_box']) ? $rowExcelData['no._of_pallet_/_box'] : 1));
							
							$rowExcelData['per_unit_cost'] = (isset($unitCost) && !empty($unitCost) ? round($unitCost, 2) : '');
							$rowExcelData['per_pallet_/_box_cost'] = (isset($finalUnitCost) && !empty($finalUnitCost) ? round($finalUnitCost, 2) : '');
							
							/*
							$rowExcelData['order_date'] = ( isset($getExportRecordDetail->dt_order_date) ?  clientDate($getExportRecordDetail->dt_order_date) :'' );
							$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_goods_in_buyer_detail_no) ? $getExportRecordDetail->v_goods_in_buyer_detail_no : '' );
							$rowExcelData['buyer_company'] =  ( isset($getExportRecordDetail->v_company_name) ? $getExportRecordDetail->v_company_name :'' );
							$rowExcelData['user_company'] = ( isset($getExportRecordDetail->buyer_user_company_name) ? $getExportRecordDetail->buyer_user_company_name :'' );
							$rowExcelData['buyer_name'] = ( isset($getExportRecordDetail->goods_buyer_name) ?  ($getExportRecordDetail->goods_buyer_name) :'' ) ;
							$rowExcelData['supplier_name'] = ( isset($getExportRecordDetail->v_supplier_name) ?  ( $getExportRecordDetail->v_supplier_name )  : '' );
							$rowExcelData['supplier_location'] = ( isset($getExportRecordDetail->v_supplier_address) ?  ( $getExportRecordDetail->v_supplier_address )  :'' ) ;
							$rowExcelData['supplier_country'] = ( isset($getExportRecordDetail->v_country_name) ?  ( $getExportRecordDetail->v_country_name ) :'' ) ;
							$rowExcelData['po_no./_sales_invoice_no.'] = ( isset($getExportRecordDetail->v_po_sales_invoice_no) ?  ( $getExportRecordDetail->v_po_sales_invoice_no )  :'' );
							$rowExcelData['invoice_amount'] = (isset($getExportRecordDetail->d_po_amount) ? decimalAmount( $getExportRecordDetail->d_po_amount ) : '' ) .' '.(isset($getExportRecordDetail->po_currency_code) ? $getExportRecordDetail->po_currency_code : '' );
							$rowExcelData['payment_status'] = (isset($getExportRecordDetail->e_payment_status) ? $getExportRecordDetail->e_payment_status : '' );
							$rowExcelData['payment_amount'] = (isset($getExportRecordDetail->d_payment_amount) ? decimalAmount( $getExportRecordDetail->d_payment_amount ) :'' ) . ' ' .(isset($getExportRecordDetail->payment_currency_code) ? $getExportRecordDetail->payment_currency_code : '');
							$rowExcelData['collection_/_delivery'] = ( ( isset($getExportRecordDetail->e_collection_type) ?  ($getExportRecordDetail->e_collection_type) :'' ) ) ;
							$rowExcelData['ready_for_collection'] = "";
							$rowExcelData['buyer_delivery_date'] = "";
							if( isset($getExportRecordDetail->e_collection_type) && ( $getExportRecordDetail->e_collection_type == config('constants.COLLECTION') ) ){
								$rowExcelData['ready_for_collection'] = ( isset($getExportRecordDetail->e_ready_for_collection_status) ? $getExportRecordDetail->e_ready_for_collection_status : '' );
							}
								
							if( isset($getExportRecordDetail->e_collection_type) && ( $getExportRecordDetail->e_collection_type == config('constants.DELIVERY') ) && (!empty($getExportRecordDetail->gib_delivery_date)) ){
								$rowExcelData['buyer_delivery_date'] =  clientDate($getExportRecordDetail->gib_delivery_date);
							}
								
								
							$rowExcelData['delivery_type'] = ( ( isset($getExportRecordDetail->e_delivery_type) ?  ($getExportRecordDetail->e_delivery_type) :'' ) ) ;
							$rowExcelData['logistic_entry_no'] = ( ( isset($getExportRecordDetail->v_goods_in_logistic_master_no) ?  ($getExportRecordDetail->v_goods_in_logistic_master_no) :'' ) ) ;
							$rowExcelData['book_by'] = ( isset($getExportRecordDetail->logistic_book_by_name) ?  ($getExportRecordDetail->logistic_book_by_name) :'' );
							$rowExcelData['logistic_partner'] = ( isset($getExportRecordDetail->v_logistic_partner_name) ?  ($getExportRecordDetail->v_logistic_partner_name) :'' );
							$rowExcelData['collection_date'] = ( isset($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) :'' );
							$rowExcelData['tracking_no.'] = ( isset($getExportRecordDetail->v_tracking_no) ?  ($getExportRecordDetail->v_tracking_no) :'' );
							$rowExcelData['tracking_link'] = ( isset($getExportRecordDetail->v_tracking_link) ?  ($getExportRecordDetail->v_tracking_link) :'' );
							$rowExcelData['logistic_delivery_date'] = ( isset($getExportRecordDetail->dt_delivery_date) ?  clientDate($getExportRecordDetail->dt_delivery_date) :'' );
							$rowExcelData['final_total_(gbp)'] = ( isset($getExportRecordDetail->d_invoice_total) ?  decimalAmount($getExportRecordDetail->d_invoice_total) :'' );
							$rowExcelData['status'] = ( isset($getExportRecordDetail->v_status) ?  ($getExportRecordDetail->v_status) :'' );
							$rowExcelData['logistic_delivery_type'] = (isset($getExportRecordDetail->e_collection_delivery_type) ? ($getExportRecordDetail->e_collection_delivery_type) : '');
							$rowExcelData['delivery_/_collection_location'] = (isset($getExportRecordDetail->v_warehouse_name) ? $getExportRecordDetail->v_warehouse_name .(isset($getExportRecordDetail->v_warehouse_code) ? ' (' .( $getExportRecordDetail->v_warehouse_code ) .')' :'' ) :'' );
							*/
							
							
							$finalExportData[] = $rowExcelData;
						}
					}
				}
				
				if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
					/* foreach ($individualTabSummaryDetails as $warehouseCode => $individualTabSummaryDetail){
						$rowExcelData = [];
	
						$rowExcelData['entry_no'] = '';
						$rowExcelData['logistic_entry_no'] = '';
						$rowExcelData['buyer_company'] =  '';
						$rowExcelData['user_company'] = '';
						$rowExcelData['supplier_name'] = '';
						$rowExcelData['supplier_country'] = '';
						$rowExcelData['po_no./_sales_invoice_no.'] = '';
						$rowExcelData['invoice_amount_(gbp)'] = '';
						$rowExcelData['collection_/_delivery'] = '';
						$rowExcelData['buyer_delivery_date'] = '';
						$rowExcelData['collection_date'] = '';
						$rowExcelData['logistic_delivery_date'] = '';
						$rowExcelData['final_total_(gbp)'] = '';
	
						$finalExportData[$warehouseCode][] = $rowExcelData;
	
						foreach ($individualTabSummaryDetail as $individualTabSummaryKey => $individualTabSummaryValue){
							$rowExcelData = [];
								
							$rowExcelData['entry_no'] = '';
							$rowExcelData['logistic_entry_no'] = (isset($individualTabSummaryKey) && !empty($individualTabSummaryKey) ? $individualTabSummaryKey : '');
							$rowExcelData['buyer_company'] =  (isset($individualTabSummaryValue) && !empty($individualTabSummaryValue) ? $individualTabSummaryValue : 0);
							$rowExcelData['user_company'] = '';
							$rowExcelData['supplier_name'] = '';
							$rowExcelData['supplier_country'] = '';
							$rowExcelData['po_no./_sales_invoice_no.'] = '';
							$rowExcelData['invoice_amount_(gbp)'] = '';
							$rowExcelData['collection_/_delivery'] = '';
							$rowExcelData['buyer_delivery_date'] = '';
							$rowExcelData['collection_date'] = '';
							$rowExcelData['logistic_delivery_date'] = '';
							$rowExcelData['final_total_(gbp)'] = '';
								
							$finalExportData[$warehouseCode][] = $rowExcelData;
						}
	
					}
						
						
					if(isset($getAllCountryDetails) && !empty($getAllCountryDetails)){
					 foreach ($summaryData as $warehouseCode => $summaryInfo){
					 $totalAmountWarehouse = 0;
					 foreach ($getAllCountryDetails as $getAllCountryDetail){
					 $rowData = [];
					 if(isset($getAllCountryDetail) && !empty($getAllCountryDetail)){
					 	
					 $rowData['supp._country'] = $getAllCountryDetail->v_country_code;
					 $rowData['total_pallets'] = '';
					 $rowData['total_amount'] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]) ? round($summaryInfo[$getAllCountryDetail->v_country_code], 2) : '-');
					 $totalAmountWarehouse = $totalAmountWarehouse + ((isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]) ? round($summaryInfo[$getAllCountryDetail->v_country_code], 2) : 0));
					 	
					 $finalExportData['Summary'][$warehouseCode][] = $rowData;
					 }
					 }
					 	
					 $finalExportData['Summary'][$warehouseCode][] = [
					 'supp._country' => 'total',
					 'total_pallets' => '',
					 'total_amount' => (isset($totalAmountWarehouse) && !empty($totalAmountWarehouse) ? round($totalAmountWarehouse, 2) : 0)
					 ];
					 }
						} */
					
					foreach ($finalExportData as $sheetTab => $finalExport){
						if($sheetTab != 'Summary'){
							$emptyRow = 6 - count($finalExport);
							
							if($emptyRow > 0){
								for ($i = 1; $i <= $emptyRow; $i++){
									$rowEmptyData = [];
									$rowEmptyData['entry_no'] = '';
									$rowEmptyData['logistic_entry_no'] = '';
									$rowEmptyData['buyer_company'] = '';
									$rowEmptyData['user_company'] = '';
									$rowEmptyData['supplier_name'] = '';
									$rowEmptyData['supplier_country'] = '';
									$rowEmptyData['po_no./_sales_invoice_no.'] = '';
									$rowEmptyData['po_amount_(gbp)'] = '';
									$rowEmptyData['collection_/_delivery'] = '';
									$rowEmptyData['buyer_delivery_date'] = '';
									$rowEmptyData['collection_date'] = '';
									$rowEmptyData['logistic_delivery_date'] = '';
									$rowEmptyData['transporter_invoice_info'] = '';
									$rowEmptyData['final_total_(gbp)'] = '';
										
									$finalExportData[$sheetTab][] = $rowEmptyData;
								}								
							}
						}
					}
						
					$summaryTotalAmountWarehouseDetails = [];
						
					foreach ($getAllCountryDetails as $getAllCountryDetail){
						$rowData = [];
						foreach ($summaryData as $warehouseCode => $summaryInfo){
							if(isset($getAllCountryDetail) && !empty($getAllCountryDetail)){
								$rowData['supp._country__' . $warehouseCode] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) ? $getAllCountryDetail->v_country_code : '');
								$rowData['total_pallets__' . $warehouseCode] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]['total_pallets']) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]['total_pallets']) ? $summaryInfo[$getAllCountryDetail->v_country_code]['total_pallets'] : '-');
								$rowData['total_boxes__' . $warehouseCode] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]['total_boxes']) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]['total_boxes']) ? $summaryInfo[$getAllCountryDetail->v_country_code]['total_boxes'] : '-');;
								$rowData['total_amount_(gbp)__' . $warehouseCode] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]['total_amount']) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]['total_amount']) ? round($summaryInfo[$getAllCountryDetail->v_country_code]['total_amount'], 2) : '-');
								
								$summaryTotalAmountWarehouseDetails[$warehouseCode]['total_pallets'] = (isset($summaryTotalAmountWarehouseDetails[$warehouseCode]['total_pallets']) && !empty($summaryTotalAmountWarehouseDetails[$warehouseCode]['total_pallets']) ? $summaryTotalAmountWarehouseDetails[$warehouseCode]['total_pallets'] : 0) + ((isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]['total_pallets']) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]['total_pallets']) ? $summaryInfo[$getAllCountryDetail->v_country_code]['total_pallets'] : 0));
								$summaryTotalAmountWarehouseDetails[$warehouseCode]['total_boxes'] = (isset($summaryTotalAmountWarehouseDetails[$warehouseCode]['total_boxes']) && !empty($summaryTotalAmountWarehouseDetails[$warehouseCode]['total_boxes']) ? $summaryTotalAmountWarehouseDetails[$warehouseCode]['total_boxes'] : 0) + ((isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]['total_boxes']) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]['total_boxes']) ? $summaryInfo[$getAllCountryDetail->v_country_code]['total_boxes'] : 0));
								$summaryTotalAmountWarehouseDetails[$warehouseCode]['total_amount'] = (isset($summaryTotalAmountWarehouseDetails[$warehouseCode]['total_amount']) && !empty($summaryTotalAmountWarehouseDetails[$warehouseCode]['total_amount']) ? $summaryTotalAmountWarehouseDetails[$warehouseCode]['total_amount'] : 0) + ((isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]['total_amount']) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]['total_amount']) ? round($summaryInfo[$getAllCountryDetail->v_country_code]['total_amount'], 2) : 0));
							}
						}
						$finalExportData['Summary'][] = $rowData;
					}
						
					$rowTotalData = [];
					foreach ($summaryTotalAmountWarehouseDetails as $warehouseCode => $summaryTotalAmountWarehouseDetail){
						$rowTotalData['supp._country__' . $warehouseCode] = 'Total';
						$rowTotalData['total_pallets__' . $warehouseCode] = isset($summaryTotalAmountWarehouseDetail['total_pallets']) && !empty($summaryTotalAmountWarehouseDetail['total_pallets']) ? $summaryTotalAmountWarehouseDetail['total_pallets'] : 0;
						$rowTotalData['total_boxes__' . $warehouseCode] = isset($summaryTotalAmountWarehouseDetail['total_boxes']) && !empty($summaryTotalAmountWarehouseDetail['total_boxes']) ? $summaryTotalAmountWarehouseDetail['total_boxes'] : 0;
						$rowTotalData['total_amount_(gbp)__' . $warehouseCode] = isset($summaryTotalAmountWarehouseDetail['total_amount']) && !empty($summaryTotalAmountWarehouseDetail['total_amount']) ? $summaryTotalAmountWarehouseDetail['total_amount'] : 0;
					}
						
					if (isset($rowTotalData) && !empty($rowTotalData)){
						$finalExportData['Summary'][] = $rowTotalData;
					}
				}
					
				if (!empty($finalExportData)) {
						
					if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
						$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.good-in-summary-report')]);
						
						$exportInfo = [
								'record_detail' => $finalExportData, 
								'title' => trans('messages.good-in-buyer'),
								'additional_summary' => isset($individualTabSummaryDetails) && !empty($individualTabSummaryDetails) ? $individualTabSummaryDetails : [],
								'summary_common_header_gap' => 4
						];
						
						$xlsData = $this->generateSpreadsheetMultiple($exportInfo);
					} else {
						$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.tracking-goods-in')]);
						$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.good-in-buyer')]);
					}
						
					$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
				} else {
						
					$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
				}
				return Response::json($response);
				die;
			}
			if(!empty($columnName)) {
				switch($columnName){
					case 'po_number':
						$columnName = 'v_po_sales_invoice_no';
						break;
					case 'entry_no':
						$columnName = 'gbd.v_goods_in_buyer_detail_no';
						break;
					case 'logistic_entry_no':
						$columnName = 'glm.v_goods_in_logistic_master_no';
						break;
					case 'vendor_number':
						$columnName = 'v_vendor_number';
						break;
					case 'supplier_name':
						$columnName = 'sm.v_supplier_name';
						break;
					case 'supplier_country':
						$columnName = 'scm.v_country_name';
						break;						
					case 'invoice_number':
						$columnName = 'gdm.v_invoice_no';
						break;
					case 'buyer_company':
						$columnName = 'cm.v_company_name';
						break;
					case 'user_company':
						$columnName = 'buyer_user_company_name';
						break;
					case 'buyer_name':
						$columnName = 'goods_buyer_name';
						break;
					case 'user_buyer_name':
						$columnName = 'user_buyer_name';
						break;
					case 'goods_remark':
						$columnName = 'goods_remark_value';
						break;
					case 'brand':
						$columnName = 'gdm.v_brand';
						break;
					case 'customs_procedure':
						$columnName = 'gdm.e_customs_procedure';
						break;
					case 'dangerous_goods':
						$columnName = 'dangerous_goods_value';
						break;
					case 'po_amount':
						$columnName = 'gdm.d_po_amount';
						break;
					case 'po_amount_with_vat':
						$columnName = 'gdm.d_po_amount_with_vat';
						break;
					/* case 'currency_code':
						$columnName = 'po_currency_code';
						break; */
					case 'payment_terms':
						$columnName = 'payment_term_value';
						break;
					case 'collection_delivery':
						$columnName = 'gdm.e_collection_type';
						break;
					case 'delivery_location':
						$columnName = 'wh.v_warehouse_name';
						break;
					case 'transporter_invoice_amount_gbp':
						$columnName = 'glm.d_invoice_total';
						break;
					case 'document_date':
						$columnName = 'dt_po_creation_date';
						break;						
					case 'order_date':
						$columnName = 'gdm.dt_order_date';
						break;
					case 'invoice_date':
						$columnName = 'gdm.dt_invoice_date';
						break;
					case 'payment_date':
						$columnName = 'gdm.dt_payment_date';
						break;
					case 'actual_payment_date':
						$columnName = 'gdm.dt_actual_payment_date';
						break;
					case 'collection_date':
						$columnName = 'glm.dt_collection_date';
						break;
					case 'buyer_delivery_date':
						$columnName = 'gib_delivery_date';
						break;
					case 'logistic_delivery_date':
						$columnName = 'glm.dt_delivery_date';
						break;
					case 'goods_in_to_date':
						$columnName = 'glm.dt_goods_in_date';
						break;
					case 'pallet_box':
						$columnName = 'type_of_pallet_box_final';
						break;
					case 'no_of_pallet_box':
						$columnName = 'no_of_pallet_box_final';
						break;							
				}
				$whereData['order_by'] = [ $columnName =>  ( (!empty($columnSortOrder)) ? $columnSortOrder : 'DESC' ) ];
			} else {
				$whereData['order_by'] =  [ 'buyer_id' =>  'desc'] ;
			}
			$totalRecords = count($this->crudModel->getTrackingGoodInDetail ( $whereData  , $likeData  , $additionalData ));
			//echo $this->crudModel->last_query();echo "<br><br><br>";die;
			$whereData['offset'] = $offset ;
				
			$whereData['limit'] = $limit;
	
			$recordDetails = $this->crudModel->getTrackingGoodInDetail ( $whereData , $likeData , $additionalData  );
			//echo $this->crudModel->last_query();die;
			$finalData = [];
			
			if(!empty($recordDetails)){
				$index = $offset;
				$allSalesRole = [];
				foreach($recordDetails as $key => $recordDetail){
	
					$encodeRecordId = Wild_tiger::encode($recordDetail->buyer_id);
					$logisticRecordId = Wild_tiger::encode($recordDetail->logistic_record_id);
						
					$rowData = [];
					$rowData['sr_no'] = '<span style="text-align:center !important;display:block">'.++$index.'</span>';
					$rowData['po_number'] = ( isset($recordDetail->v_po_sales_invoice_no) && !empty($recordDetail->v_po_sales_invoice_no) ?  ($recordDetail->v_po_sales_invoice_no)  :'' );
					$rowData['entry_no'] = (isset($recordDetail->v_goods_in_buyer_detail_no) ? ($recordDetail->v_goods_in_buyer_detail_no) : '');
					$rowData['logistic_entry_no'] = (isset($recordDetail->v_goods_in_logistic_master_no) ? $recordDetail->v_goods_in_logistic_master_no : '');
					$rowData['vendor_number'] = ( isset($recordDetail->v_vendor_number) && !empty($recordDetail->v_vendor_number) ?  ($recordDetail->v_vendor_number)  :'' );
					$rowData['supplier_name'] = (isset($recordDetail->v_supplier_name) && !empty($recordDetail->v_supplier_name) ? $recordDetail->v_supplier_name : '');
					$rowData['supplier_country'] = (isset($recordDetail->v_country_name) && !empty($recordDetail->v_country_name) ? $recordDetail->v_country_name : '' );
					$rowData['invoice_number'] = ( isset($recordDetail->v_invoice_no) && !empty($recordDetail->v_invoice_no) ?  ($recordDetail->v_invoice_no)  :'' );
					$rowData['buyer_company'] = (isset($recordDetail->v_company_name) && !empty($recordDetail->v_company_name) ? $recordDetail->v_company_name :'');
					$rowData['user_company'] = (isset($recordDetail->buyer_user_company_name) && !empty($recordDetail->buyer_user_company_name) ? $recordDetail->buyer_user_company_name :'');
					$rowData['buyer_name'] = (isset($recordDetail->goods_buyer_name) && !empty($recordDetail->goods_buyer_name) ? $recordDetail->goods_buyer_name :'');
					$rowData['user_buyer_name'] = (isset($recordDetail->user_buyer_name) && !empty($recordDetail->user_buyer_name) ? $recordDetail->user_buyer_name :'');
					$rowData['goods_remark'] = (isset($recordDetail->goods_remark_value) && !empty($recordDetail->goods_remark_value) ? $recordDetail->goods_remark_value :'');
					$rowData['brand'] = (isset($recordDetail->v_brand) && !empty($recordDetail->v_brand) ? $recordDetail->v_brand :'');
					$rowData['customs_procedure'] = (isset($recordDetail->e_customs_procedure) && !empty($recordDetail->e_customs_procedure) ? $recordDetail->e_customs_procedure :'');
					$rowData['dangerous_goods'] = (isset($recordDetail->dangerous_goods_value) && !empty($recordDetail->dangerous_goods_value) ? $recordDetail->dangerous_goods_value :'');
					
					$rowData['po_amount'] = ( isset($recordDetail->d_po_amount) && !empty($recordDetail->d_po_amount) ?  decimalAmount($recordDetail->d_po_amount) : 0 );
					$rowData['po_amount_with_vat'] = ( isset($recordDetail->d_po_amount_with_vat) && !empty($recordDetail->d_po_amount_with_vat) ? decimalAmount($recordDetail->d_po_amount_with_vat) : '' );
					
					if(isset($recordDetail->po_gbp_conversation_rate) && !empty($recordDetail->po_gbp_conversation_rate) && $recordDetail->po_gbp_conversation_rate > 0){
						$rowData['po_amount'] = ( isset($recordDetail->d_po_amount) && !empty($recordDetail->d_po_amount) ? decimalAmount($recordDetail->d_po_amount * $recordDetail->po_gbp_conversation_rate) : '');
						$rowData['po_amount_with_vat'] = ( isset($recordDetail->d_po_amount_with_vat) && !empty($recordDetail->d_po_amount_with_vat) ? decimalAmount($recordDetail->d_po_amount_with_vat * $recordDetail->po_gbp_conversation_rate) : '');
					}
					
					//$rowData['currency_code'] = (isset($recordDetail->po_currency_code) && !empty($recordDetail->po_currency_code) ? ( $recordDetail->po_currency_code ) :'' );
					$rowData['payment_terms'] = (isset($recordDetail->payment_term_value) && !empty($recordDetail->payment_term_value) ? ( $recordDetail->payment_term_value ) :'' );
					$rowData['collection_delivery'] = (isset($recordDetail->e_collection_type) && !empty($recordDetail->e_collection_type) ? $recordDetail->e_collection_type : '');
					$rowData['mode_of_transport'] = (isset($recordDetail->e_mode_of_transport) && !empty($recordDetail->e_mode_of_transport) ? $recordDetail->e_mode_of_transport : '');
					$rowData['delivery_location'] = (isset($recordDetail->v_warehouse_name) && !empty($recordDetail->v_warehouse_name) ? $recordDetail->v_warehouse_name  .(isset($recordDetail->v_warehouse_code) && !empty($recordDetail->v_warehouse_code) ? ' (' .( $recordDetail->v_warehouse_code ) .')' :'' ) :'' );
					$rowData['transporter_invoice_amount_gbp'] = (isset($recordDetail->d_invoice_total) && !empty($recordDetail->d_invoice_total) ? decimalAmount ( $recordDetail->d_invoice_total ) : '');
					$rowData['document_date'] = (isset($recordDetail->dt_po_creation_date)  && !empty($recordDetail->dt_po_creation_date) ? clientDate($recordDetail->dt_po_creation_date) : '');
					$rowData['order_date'] = (isset($recordDetail->dt_order_date)  && !empty($recordDetail->dt_order_date) ? clientDate($recordDetail->dt_order_date) : '');
					$rowData['invoice_date'] = (isset($recordDetail->dt_invoice_date)  && !empty($recordDetail->dt_invoice_date) ? clientDate($recordDetail->dt_invoice_date) : '');
					$rowData['payment_date'] = (isset($recordDetail->dt_payment_date)  && !empty($recordDetail->dt_payment_date) ? clientDate($recordDetail->dt_payment_date) : '');
					$rowData['actual_payment_date'] = (isset($recordDetail->dt_actual_payment_date)  && !empty($recordDetail->dt_actual_payment_date) ? clientDate($recordDetail->dt_actual_payment_date) : '');
					$rowData['collection_date'] = (isset($recordDetail->dt_collection_date) && !empty($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) : '');
					$rowData['buyer_delivery_date'] = (isset($recordDetail->gib_delivery_date) && !empty($recordDetail->gib_delivery_date) ? clientDate($recordDetail->gib_delivery_date) : '');
					$rowData['logistic_delivery_date'] = (isset($recordDetail->dt_delivery_date) && !empty($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) : '');
					$rowData['goods_in_to_date'] = (isset($recordDetail->dt_goods_in_date) && !empty($recordDetail->dt_goods_in_date) ? clientDate($recordDetail->dt_goods_in_date) : '');
					
					$collectPalletBoxNo = (isset($recordDetail->no_of_pallet_box_final) && !empty($recordDetail->no_of_pallet_box_final) ? $recordDetail->no_of_pallet_box_final : 0);
					$collectPalletBoxType = (isset($recordDetail->type_of_pallet_box_final) && !empty($recordDetail->type_of_pallet_box_final) ? $recordDetail->type_of_pallet_box_final : '');				
					
					$rowData['pallet_box'] = (isset($collectPalletBoxType) && !empty($collectPalletBoxType) ? $collectPalletBoxType : '');
					$rowData['no_of_pallet_box'] = (isset($collectPalletBoxNo) && !empty($collectPalletBoxNo) ? decimalAmount($collectPalletBoxNo) : '');
					$rowData['buyer_comments'] = (isset($recordDetail->v_buyer_comments) && !empty($recordDetail->v_buyer_comments) ? $recordDetail->v_buyer_comments : '');
					$rowData['warehouse_comments'] = (isset($recordDetail->v_status_comment) && !empty($recordDetail->v_status_comment) ? $recordDetail->v_status_comment : '');
					$rowData['action'] = '';
					$rowData['action'] .= '<div class="custome-icon text-center">';
					$rowData['action'] .= '<button title="'.trans('messages.view-documents').'"  data-logistic-id="'.$logisticRecordId.'" data-buyer-name="'.(!empty($recordDetail->v_goods_in_buyer_detail_no) ? $recordDetail->v_goods_in_buyer_detail_no :'').'" data-record-id="'.$encodeRecordId.'" onclick="openDocumentModel(this)" class="btn btn-sm bg-theme btn-submit-class text-white"><i class="fa fa-eye"></i></button>';
					$rowData['action'] .= '</div>';	
					
					
					/*
					
					
					$rowData['entry_no'] = (isset($recordDetail->v_goods_in_buyer_detail_no) ? ($recordDetail->v_goods_in_buyer_detail_no) : '');
					
					$rowData['payment_status'] = (isset($recordDetail->e_payment_status) ? $recordDetail->e_payment_status . (isset($recordDetail->d_payment_amount) ? '<br>' . decimalAmount($recordDetail->d_payment_amount ). (isset($recordDetail->payment_currency_code) ? ' '.$recordDetail->payment_currency_code : '' ) : '' ) : '' );
					$rowData['collection_delivery'] = (isset($recordDetail->e_collection_type) ? $recordDetail->e_collection_type : '').(!empty($recordDetail->e_ready_for_collection_status) ? '<br>' .$recordDetail->e_ready_for_collection_status :'');
						
					if( isset($recordDetail->e_collection_type) && ( $recordDetail->e_collection_type == config('constants.DELIVERY') ) && (!empty($recordDetail->gib_delivery_date)) ){
						$rowData['collection_delivery'] .= '<br>'.clientDate($recordDetail->gib_delivery_date);
					}
						
					
					$rowData['logistic_entry_no'] = (isset($recordDetail->v_goods_in_logistic_master_no) ? $recordDetail->v_goods_in_logistic_master_no : '');
					$rowData['book_by'] = (isset($recordDetail->logistic_book_by_name) ? $recordDetail->logistic_book_by_name : '');
					$rowData['logistic_partner'] = (isset($recordDetail->v_logistic_partner_name) ? $recordDetail->v_logistic_partner_name : '') . (isset($recordDetail->v_logistic_partner_code) ? '(' .$recordDetail->v_logistic_partner_code.')' : '');
					$rowData['collection_date'] = (isset($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) : '');
					$rowData['tracking_no'] = (isset($recordDetail->v_tracking_no) ? ($recordDetail->v_tracking_no) : '') .(isset($recordDetail->v_tracking_link) ? '<br>' .($recordDetail->v_tracking_link) : '');
					$rowData['delivery_date'] = (isset($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) : '');
					$rowData['final_total'] = (isset($recordDetail->d_invoice_total) ? decimalAmount($recordDetail->d_invoice_total) : '');
					$rowData['status'] = (isset($recordDetail->v_status) ? ($recordDetail->v_status) : '');
					$rowData['logistic_delivery_type'] = (isset($recordDetail->e_collection_delivery_type) ? ($recordDetail->e_collection_delivery_type) : '');
					$rowData['delivery_collection_location'] = (isset($recordDetail->v_warehouse_name) ? $recordDetail->v_warehouse_name  .(isset($recordDetail->v_warehouse_code) ? ' (' .( $recordDetail->v_warehouse_code ) .')' :'' ) :'' );
					*/
					
					$finalData[] = $rowData;
				}
			}
				
			$response = array(
					"draw" => intval($draw),
					"iTotalRecords" => count($finalData),
					"iTotalDisplayRecords" => $totalRecords,
					"aaData" => $finalData
			);
				
			return Response::json($response);die;
		}
	
	}
	
	public function trackingGoodsInOld(){
		
		if(checkPermission(config('permission_constants.VIEW_TRACKING_GOODS_IN_REPORT')) != true){
			return redirect('access-denied');
		}
		
		$data = [];
		$data ['pageTitle'] = trans('messages.tracking-goods-in'). ' ' . trans('messages.report') . ' Old';
		$page = $this->defaultPage;
		
		$data['paymentStatusInfo'] = paymentStatus();
		$data['companyRecordDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['supplierRecordDetails'] = SupplierMasterModel::orderBy('v_supplier_name', 'ASC')->get();
		$data['supplierlocationDetails'] = SupplierDetailModel::with(['supplierMaster'])->get();
		$data['collectionDeliveryInfo'] = collectionDeliveryInfo();
		$data['deliveryTypeInfo'] = deliveryTypeInfo();
		$data['statusDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$warehouseWhere = [];
		$userLogin =  session()->get('user_id');
		$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
		
		if(count($userLoginDetails) > 0){
			if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
				$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0 );
				$warehouseWhere['i_id'] = $warehouseIds;
			}
		}
		
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['supplierCountryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
		$whereData = [];
		$whereData['t_is_active'] = 1;
		$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whereData){
			$query->where('t_is_active',$whereData);
		})->get();
		$data['readyForCollectionInfo'] = dangerousGoodsInfo();
		$data['statusMasterDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		return view($this->folderName . 'tracking-goods-in-old')->with($data);
	}
	public function filterOld(Request $request){
		
		if ($request->ajax ()) {
			$whereData = $likeData  = $additionalData =  [];
			
			$draw = (!empty($request->input('draw')) ? $request->input('draw') : 1 );
			
			$offset = (!empty($request->input('start')) ? $request->input('start') : 0 );
			
			$limit = (!empty($request->input('length')) ? $request->input('length') : 10 );// Rows display per page
			
			$columnName = $columnIndex =  $columnSortOrder = '';
			
			$columnIndex = (!empty($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : '' )  ; // Column index
			$columnName = (!empty($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : '' );// Column name
			$columnSortOrder = (!empty($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : '' )  ;// asc or desc
			
			$searchValue = (!empty($_POST['search']['value']) ? $_POST['search']['value'] : '' );
			
			$page_no = (! empty ( $request->input ( 'page' ) )) ? ( int ) $request->input ( 'page' ) : 1;
			if (!empty($request->post('search_by_logistic_partner_name'))) {
				$searchByName = trim($request->post('search_by_logistic_partner_name'));
				$likeData ['gdm.v_po_sales_invoice_no'] = $searchByName;
				$likeData ['gdm.d_po_amount'] = $searchByName;
				$likeData ['gbd.v_goods_in_buyer_detail_no'] = $searchByName;
				$likeData ['glm.v_goods_in_logistic_master_no'] = $searchByName;
				$likeData ['glm.v_tracking_no'] = $searchByName;
				$likeData ['glm.v_tracking_link'] = $searchByName;
					
			}
			
			if(!empty($request->post('search_payment_status') )){
				$whereData['gdm.e_payment_status'] =  trim($request->post('search_payment_status'));
			
			}
			
			if (!empty($request->post('search_order_from_date'))) {
				$orderFromDate = dbDate($request->input('search_order_from_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_order_date) >=  '" . $orderFromDate . "'";
			}
			if (!empty($request->post('search_order_to_date'))) {
				$orderToDate = dbDate($request->input('search_order_to_date'));
				$whereData['custom_function'][] =  "date(gdm.dt_order_date) <=  '" . $orderToDate . "'";
			}
			if( ( !empty($request->post('search_collection_delivery') ) ) && ( $request->post('search_collection_delivery') ) ){
				$whereData['gdm.e_collection_type'] =  trim($request->post('search_collection_delivery'));
			
			}
			/* if( ( !empty($request->post('search_delivery_type') ) ) && ( $request->post('search_delivery_type') ) ){
				$whereData['gdm.e_delivery_type'] =  trim($request->post('search_delivery_type'));
			} */
			if(!empty($request->post('search_book_by') )){
				if( $request->post('search_book_by') == "None" ){
					$whereData['null_column'] =  'glm.i_id';
					//$whereData['gdm.e_collection_type'] =  config('constants.COLLECTION');
				} else {
					$whereData['glm.i_book_employee_id'] =  (int)Wild_tiger::decode($request->post('search_book_by'));
				}
			}
			
			if(!empty($request->post('search_logistic_partner') )){
				$whereData['glm.i_logistic_partner_id'] =  (int)Wild_tiger::decode($request->post('search_logistic_partner'));
			
			}
			if (!empty($request->post('search_collection_form_date'))) {
				$collectionFromDate = dbDate($request->input('search_collection_form_date'));
				$whereData['custom_function'][] =  "date(glm.dt_collection_date) >=  '" . $collectionFromDate . "'";
			}
			if (!empty($request->post('search_collection_to_date'))) {
				$collectionToDate = dbDate($request->input('search_collection_to_date'));
				$whereData['custom_function'][] =  "date(glm.dt_collection_date) <=  '" . $collectionToDate . "'";
			}
			
			if (!empty($request->post('search_delivery_form_date'))) {
				$deliveryFromDate = dbDate($request->input('search_delivery_form_date'));
				$whereData['custom_function'][] =  "date(glm.dt_delivery_date) >=  '" . $deliveryFromDate . "'";
			}
			if (!empty($request->post('search_delivery_to_date'))) {
				$deliveryToDate = dbDate($request->input('search_delivery_to_date'));
				$whereData['custom_function'][] =  "date(glm.dt_delivery_date) <=  '" . $deliveryToDate . "'";
			}
			//remove due to duplicate status
			if( ( !empty($request->post('search_status') ) ) && ( $request->post('search_status') ) ){
				//$whereData['glm.i_status_id'] =  (int)Wild_tiger::decode($request->post('search_status'));
			}
			
			if (!empty($request->post('search_buyer_company'))) {
				$searchBuyerCompanyIds =explode("," , $request->input('search_buyer_company') );
					
				if(!empty($searchBuyerCompanyIds)){
					$searchBuyerCompanyIds = array_map(function($searchBuyerCompanyId){
						return (int)Wild_tiger::decode($searchBuyerCompanyId);
					}, $searchBuyerCompanyIds);
				}
				$whereData['custom_function'][] =  "( gdm.i_buyer_company_id in (".implode("," , $searchBuyerCompanyIds )."))";
			}
			
			if (!empty($request->post('search_user_company'))) {
				$searchUserCompanyIds =explode("," , $request->input('search_user_company') );
			
				if(!empty($searchUserCompanyIds)){
					$searchUserCompanyIds = array_map(function($searchUserCompanyId){
						return (int)Wild_tiger::decode($searchUserCompanyId);
					}, $searchUserCompanyIds);
				}
				if(!empty($searchUserCompanyIds)){
					$customerSearch = " ( ";
					if(!empty($searchUserCompanyIds)){
						foreach($searchUserCompanyIds as $userCompany){
							$customerSearch .= "find_in_set('".$userCompany."',gdm.v_user_company_ids) OR ";
						}
						$customerSearch = rtrim($customerSearch,"OR ");
						$customerSearch .= " ) ";
						$whereData['custom_function'][] = $customerSearch;
					}
				}
			}
			
			if (!empty($request->post('search_buyer_name'))) {
				$searchBuyerNameIds =explode("," , $request->input('search_buyer_name') );
			
				if(!empty($searchBuyerNameIds)){
					$searchBuyerNameIds = array_map(function($searchBuyerNameId){
						return (int)Wild_tiger::decode($searchBuyerNameId);
					}, $searchBuyerNameIds);
					
					//added multiple buyer selected name
					if(!empty($searchBuyerNameIds)){
						$customerSearch = " ( ";
						foreach($searchBuyerNameIds as $searchBuyerNameId){
							$customerSearch .= "find_in_set('".$searchBuyerNameId."',gdm.v_buyer_employee_ids) OR ";
						}
						$customerSearch = rtrim($customerSearch,"OR ");
						$customerSearch .= " ) ";
						$whereData['custom_function'][] = $customerSearch;
					}
				}
				//$whereData['custom_function'][] =  "( gdm.i_buyer_employee_id in (".implode("," , $searchBuyerNameIds )."))";
			}
			
			if (!empty($request->post('search_supplier_name'))) {
				$searchSupplierMasterIds =explode("," , $request->input('search_supplier_name') );
			
				if(!empty($searchSupplierMasterIds)){
					$searchSupplierMasterIds = array_map(function($searchSupplierMasterId){
						return (int)Wild_tiger::decode($searchSupplierMasterId);
					}, $searchSupplierMasterIds);
				}
				$whereData['custom_function'][] =  "( gdm.i_main_supplier_id in (".implode("," , $searchSupplierMasterIds )."))";
			}
			
			if (!empty($request->post('search_supplier_location'))) {
				$searchSupplierLocationIds =explode("," , $request->input('search_supplier_location') );
			
				if(!empty($searchSupplierLocationIds)){
					$searchSupplierLocationIds = array_map(function($searchSupplierLocationId){
						return (int)Wild_tiger::decode($searchSupplierLocationId);
					}, $searchSupplierLocationIds);
				}
				$whereData['custom_function'][] =  "( gbd.i_goods_in_buyer_supplier_id in (".implode("," , $searchSupplierLocationIds )."))";
			}
			if( ( !empty($request->post('search_logistic_delivery_type') ) ) && ( $request->post('search_logistic_delivery_type') ) ){
				$whereData['gld.e_collection_delivery_type'] =  trim($request->post('search_logistic_delivery_type'));
					
			}
			if(!empty($request->post('search_delivery_collection_location'))){
				$whereData['gdm.i_delivery_location_id'] = (int)Wild_tiger::decode($request->post('search_delivery_collection_location'));
			}
			/* if(!empty($request->post('search_delivery_location'))){
				$whereData['gdm.i_delivery_location_id'] = (int)Wild_tiger::decode($request->post('search_delivery_location'));
			} */
			/* if(!empty($request->post('search_supplier_country'))){
				$whereData['sd.i_country_id'] = (int)Wild_tiger::decode($request->post('search_supplier_country'));
			} */
			if (!empty($request->post('search_supplier_country'))) {
				$searchSupplierCountryIds =explode("," , $request->input('search_supplier_country') );
					
				if(!empty($searchSupplierCountryIds)){
					$searchSupplierCountryIds = array_map(function($searchSupplierCountryId){
						return (int)Wild_tiger::decode($searchSupplierCountryId);
					}, $searchSupplierCountryIds);
				}
				$whereData['custom_function'][] =  "( sd.i_country_id in (".implode("," , $searchSupplierCountryIds )."))";
			}
			if(!empty($request->post('search_ready_for_collection') )){
				$whereData['gdm.e_ready_for_collection_status'] = ($request->post('search_ready_for_collection'));
			}
			# new filter add same as good in buyer
			//change key due sync with all filter
			if(!empty($request->post('search_status') )){
				$allStatusIds = explode("," , $request->post('search_status') );
				if(!empty($allStatusIds)){
					$allStatusIds = array_map(function($allStatusId){
						return (int)Wild_tiger::decode($allStatusId);
					}, $allStatusIds);
				}
				if(!empty($allStatusIds)){
					# multiple value
					$additionalData['whereIn'] = [ 'glm.i_status_id' , $allStatusIds ];
				}
				
			
			} else {
				$whereData['gbd.t_is_all_delivered_cancelled_ststus'] = 0;
			}
			
			$warehouseWhere = [];
			$userLogin =  session()->get('user_id');
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
			
			if(count($userLoginDetails) > 0){
				if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
					$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :0 );
					$warehouseWhere['i_id'] = $warehouseIds;
				}
			}
			
			if(!empty($warehouseWhere['i_id'])){
				$whereData['wh.i_id'] = (isset($warehouseWhere['i_id']) ? $warehouseWhere['i_id'] :'');
			}
			
			$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
			$exportTypeAction = (!empty($request->input('custom_export_type_action')) ? trim($request->input('custom_export_type_action')) : '');
			
			if ($exportAction == 'export') {
				$finalExportData = [];
				
				$summaryData = $individualTabSummaryDetails =[];
				
				$getAllCountryDetails = [];
				if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
					$finalExportData['Summary'] = [];
					$getAllCountryDetails = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name', 'v_country_code'],[ 't_is_deleted !=' => 1 , 'order_by' => [ 'v_country_name' => 'asc' ] ]);
				}				
					
				$getExportRecordDetails = $this->crudModel->getTrackingGoodInDetail($whereData, $likeData , $additionalData );
				
				if (!empty($getExportRecordDetails)) {
					$excelIndex = 0;
					
					foreach ($getExportRecordDetails as $getExportRecordDetail) {
							
						$rowExcelData = [];
						
						if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
							$warehouseCode = (isset($getExportRecordDetail->v_warehouse_code) && !empty($getExportRecordDetail->v_warehouse_code) ? $getExportRecordDetail->v_warehouse_code :'' );
							$supplierCountryCode = ( isset($getExportRecordDetail->v_country_code) && !empty($getExportRecordDetail->v_country_code) ?  ( $getExportRecordDetail->v_country_code ) :'' ) ;							
							
							if(isset($warehouseCode) && !empty($warehouseCode)){
								$summaryData[$warehouseCode][$supplierCountryCode] = ((isset($summaryData[$warehouseCode][$supplierCountryCode]) && !empty($summaryData[$warehouseCode][$supplierCountryCode])) ? $summaryData[$warehouseCode][$supplierCountryCode] : 0) + ((isset($getExportRecordDetail->d_po_amount) && !empty($getExportRecordDetail->d_po_amount) ? round($getExportRecordDetail->d_po_amount, 2) : ''));
								
								$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_goods_in_buyer_detail_no) && !empty($getExportRecordDetail->v_goods_in_buyer_detail_no) ? $getExportRecordDetail->v_goods_in_buyer_detail_no : '' );
								$rowExcelData['logistic_entry_no'] = ( ( isset($getExportRecordDetail->v_goods_in_logistic_master_no) && !empty($getExportRecordDetail->v_goods_in_logistic_master_no) ?  ($getExportRecordDetail->v_goods_in_logistic_master_no) :'' ) ) ;
								$rowExcelData['buyer_company'] =  ( isset($getExportRecordDetail->v_company_name) && !empty($getExportRecordDetail->v_company_name) ? $getExportRecordDetail->v_company_name :'' );
								$rowExcelData['user_company'] = ( isset($getExportRecordDetail->buyer_user_company_name) && !empty($getExportRecordDetail->buyer_user_company_name) ? $getExportRecordDetail->buyer_user_company_name :'' );
								$rowExcelData['supplier_name'] = ( isset($getExportRecordDetail->v_supplier_name) && !empty($getExportRecordDetail->v_supplier_name) ?  ( $getExportRecordDetail->v_supplier_name )  : '' );
								$rowExcelData['supplier_country'] = ( isset($getExportRecordDetail->v_country_name) && !empty($getExportRecordDetail->v_country_name) ?  ( $getExportRecordDetail->v_country_name ) :'' ) ;
								$rowExcelData['po_no./_sales_invoice_no.'] = ( isset($getExportRecordDetail->v_po_sales_invoice_no) && !empty($getExportRecordDetail->v_po_sales_invoice_no) ?  ( $getExportRecordDetail->v_po_sales_invoice_no )  :'' );
								$rowExcelData['invoice_amount_(gbp)'] = (isset($getExportRecordDetail->d_po_amount) && !empty($getExportRecordDetail->d_po_amount) ? round($getExportRecordDetail->d_po_amount, 2) : ''); // .' '.(isset($getExportRecordDetail->po_currency_code) && !empty($getExportRecordDetail->po_currency_code) ? $getExportRecordDetail->po_currency_code : '' );
								$rowExcelData['collection_/_delivery'] = ( ( isset($getExportRecordDetail->e_collection_type) && !empty($getExportRecordDetail->e_collection_type) ?  ($getExportRecordDetail->e_collection_type) :'' ) ) ;
								
								$rowExcelData['buyer_delivery_date'] = "";								
								if( isset($getExportRecordDetail->e_collection_type) && !empty($getExportRecordDetail->e_collection_type) && ( $getExportRecordDetail->e_collection_type == config('constants.DELIVERY') ) && (isset($getExportRecordDetail->gib_delivery_date) && !empty($getExportRecordDetail->gib_delivery_date)) ){
									$rowExcelData['buyer_delivery_date'] =  clientDate($getExportRecordDetail->gib_delivery_date);
								}
								
								$rowExcelData['collection_date'] = ( isset($getExportRecordDetail->dt_collection_date) && !empty($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) :'' );
								$rowExcelData['logistic_delivery_date'] = ( isset($getExportRecordDetail->dt_delivery_date) && !empty($getExportRecordDetail->dt_delivery_date) ?  clientDate($getExportRecordDetail->dt_delivery_date) :'' );
								$rowExcelData['final_total_(gbp)'] = ( isset($getExportRecordDetail->d_invoice_total) && !empty($getExportRecordDetail->d_invoice_total) ?  round($getExportRecordDetail->d_invoice_total, 2) : '' );
								
								$finalExportData[$warehouseCode][] = $rowExcelData;
								
								$individualTabSummaryDetails[$warehouseCode]['Total PO'] =  (isset($individualTabSummaryDetails[$warehouseCode]['Total PO']) && !empty($individualTabSummaryDetails[$warehouseCode]['Total PO']) ? $individualTabSummaryDetails[$warehouseCode]['Total PO'] : 0) + 1;
								$individualTabSummaryDetails[$warehouseCode]['Collection PO'] =  (isset($individualTabSummaryDetails[$warehouseCode]['Collection PO']) && !empty($individualTabSummaryDetails[$warehouseCode]['Collection PO']) ? $individualTabSummaryDetails[$warehouseCode]['Collection PO'] : 0) + (isset($rowExcelData['collection_/_delivery']) && !empty($rowExcelData['collection_/_delivery']) && $rowExcelData['collection_/_delivery'] == config('constants.COLLECTION') ? 1 : 0);
								$individualTabSummaryDetails[$warehouseCode]['Delivery PO'] =  (isset($individualTabSummaryDetails[$warehouseCode]['Delivery PO']) && !empty($individualTabSummaryDetails[$warehouseCode]['Delivery PO']) ? $individualTabSummaryDetails[$warehouseCode]['Delivery PO'] : 0) + (isset($rowExcelData['collection_/_delivery']) && !empty($rowExcelData['collection_/_delivery']) && $rowExcelData['collection_/_delivery'] == config('constants.DELIVERY') ? 1 : 0);
								$individualTabSummaryDetails[$warehouseCode]['Total Amount'] =  (isset($individualTabSummaryDetails[$warehouseCode]['Total Amount']) && !empty($individualTabSummaryDetails[$warehouseCode]['Total Amount']) ? $individualTabSummaryDetails[$warehouseCode]['Total Amount'] : 0) + (isset($rowExcelData['invoice_amount_(gbp)']) && !empty($rowExcelData['invoice_amount_(gbp)']) ? $rowExcelData['invoice_amount_(gbp)'] : 0);
							}
							
							
						} else {
							$rowExcelData['sr_no'] = ++$excelIndex;
							$rowExcelData['order_date'] = ( isset($getExportRecordDetail->dt_order_date) ?  clientDate($getExportRecordDetail->dt_order_date) :'' );
							$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_goods_in_buyer_detail_no) ? $getExportRecordDetail->v_goods_in_buyer_detail_no : '' );
							$rowExcelData['buyer_company'] =  ( isset($getExportRecordDetail->v_company_name) ? $getExportRecordDetail->v_company_name :'' );
							$rowExcelData['user_company'] = ( isset($getExportRecordDetail->buyer_user_company_name) ? $getExportRecordDetail->buyer_user_company_name :'' );
							$rowExcelData['buyer_name'] = ( isset($getExportRecordDetail->goods_buyer_name) ?  ($getExportRecordDetail->goods_buyer_name) :'' ) ;
							$rowExcelData['supplier_name'] = ( isset($getExportRecordDetail->v_supplier_name) ?  ( $getExportRecordDetail->v_supplier_name )  : '' );
							$rowExcelData['supplier_location'] = ( isset($getExportRecordDetail->v_supplier_address) ?  ( $getExportRecordDetail->v_supplier_address )  :'' ) ;
							$rowExcelData['supplier_country'] = ( isset($getExportRecordDetail->v_country_name) ?  ( $getExportRecordDetail->v_country_name ) :'' ) ;
							$rowExcelData['po_no./_sales_invoice_no.'] = ( isset($getExportRecordDetail->v_po_sales_invoice_no) ?  ( $getExportRecordDetail->v_po_sales_invoice_no )  :'' );
							$rowExcelData['invoice_amount'] = (isset($getExportRecordDetail->d_po_amount) ? decimalAmount( $getExportRecordDetail->d_po_amount ) : '' ) .' '.(isset($getExportRecordDetail->po_currency_code) ? $getExportRecordDetail->po_currency_code : '' );
							$rowExcelData['payment_status'] = (isset($getExportRecordDetail->e_payment_status) ? $getExportRecordDetail->e_payment_status : '' );
							$rowExcelData['payment_amount'] = (isset($getExportRecordDetail->d_payment_amount) ? decimalAmount( $getExportRecordDetail->d_payment_amount ) :'' ) . ' ' .(isset($getExportRecordDetail->payment_currency_code) ? $getExportRecordDetail->payment_currency_code : '');
							$rowExcelData['collection_/_delivery'] = ( ( isset($getExportRecordDetail->e_collection_type) ?  ($getExportRecordDetail->e_collection_type) :'' ) ) ;
							$rowExcelData['ready_for_collection'] = "";
							$rowExcelData['buyer_delivery_date'] = "";
							if( isset($getExportRecordDetail->e_collection_type) && ( $getExportRecordDetail->e_collection_type == config('constants.COLLECTION') ) ){
								$rowExcelData['ready_for_collection'] = ( isset($getExportRecordDetail->e_ready_for_collection_status) ? $getExportRecordDetail->e_ready_for_collection_status : '' );
							}
							
							if( isset($getExportRecordDetail->e_collection_type) && ( $getExportRecordDetail->e_collection_type == config('constants.DELIVERY') ) && (!empty($getExportRecordDetail->gib_delivery_date)) ){
								$rowExcelData['buyer_delivery_date'] =  clientDate($getExportRecordDetail->gib_delivery_date);
							}  
							
							
							$rowExcelData['delivery_type'] = ( ( isset($getExportRecordDetail->e_delivery_type) ?  ($getExportRecordDetail->e_delivery_type) :'' ) ) ;
							$rowExcelData['logistic_entry_no'] = ( ( isset($getExportRecordDetail->v_goods_in_logistic_master_no) ?  ($getExportRecordDetail->v_goods_in_logistic_master_no) :'' ) ) ;
							$rowExcelData['book_by'] = ( isset($getExportRecordDetail->logistic_book_by_name) ?  ($getExportRecordDetail->logistic_book_by_name) :'' );
							$rowExcelData['logistic_partner'] = ( isset($getExportRecordDetail->v_logistic_partner_name) ?  ($getExportRecordDetail->v_logistic_partner_name) :'' );
							$rowExcelData['collection_date'] = ( isset($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) :'' );
							$rowExcelData['tracking_no.'] = ( isset($getExportRecordDetail->v_tracking_no) ?  ($getExportRecordDetail->v_tracking_no) :'' );
							$rowExcelData['tracking_link'] = ( isset($getExportRecordDetail->v_tracking_link) ?  ($getExportRecordDetail->v_tracking_link) :'' );
							$rowExcelData['logistic_delivery_date'] = ( isset($getExportRecordDetail->dt_delivery_date) ?  clientDate($getExportRecordDetail->dt_delivery_date) :'' );
							$rowExcelData['final_total_(gbp)'] = ( isset($getExportRecordDetail->d_invoice_total) ?  decimalAmount($getExportRecordDetail->d_invoice_total) :'' );
							$rowExcelData['status'] = ( isset($getExportRecordDetail->v_status) ?  ($getExportRecordDetail->v_status) :'' );
							$rowExcelData['logistic_delivery_type'] = (isset($getExportRecordDetail->e_collection_delivery_type) ? ($getExportRecordDetail->e_collection_delivery_type) : '');
							$rowExcelData['delivery_/_collection_location'] = (isset($getExportRecordDetail->v_warehouse_name) ? $getExportRecordDetail->v_warehouse_name .(isset($getExportRecordDetail->v_warehouse_code) ? ' (' .( $getExportRecordDetail->v_warehouse_code ) .')' :'' ) :'' );
							
							$finalExportData[] = $rowExcelData;
						}						
					}
				}
				
				if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
					foreach ($individualTabSummaryDetails as $warehouseCode => $individualTabSummaryDetail){
						$rowExcelData = [];
						
						$rowExcelData['entry_no'] = '';
						$rowExcelData['logistic_entry_no'] = '';
						$rowExcelData['buyer_company'] =  '';
						$rowExcelData['user_company'] = '';
						$rowExcelData['supplier_name'] = '';
						$rowExcelData['supplier_country'] = '';
						$rowExcelData['po_no./_sales_invoice_no.'] = '';
						$rowExcelData['invoice_amount_(gbp)'] = '';
						$rowExcelData['collection_/_delivery'] = '';
						$rowExcelData['buyer_delivery_date'] = '';
						$rowExcelData['collection_date'] = '';
						$rowExcelData['logistic_delivery_date'] = '';
						$rowExcelData['final_total_(gbp)'] = '';
						
						$finalExportData[$warehouseCode][] = $rowExcelData;
						
						foreach ($individualTabSummaryDetail as $individualTabSummaryKey => $individualTabSummaryValue){
							$rowExcelData = [];
							
							$rowExcelData['entry_no'] = '';
							$rowExcelData['logistic_entry_no'] = (isset($individualTabSummaryKey) && !empty($individualTabSummaryKey) ? $individualTabSummaryKey : '');
							$rowExcelData['buyer_company'] =  (isset($individualTabSummaryValue) && !empty($individualTabSummaryValue) ? $individualTabSummaryValue : 0);
							$rowExcelData['user_company'] = '';
							$rowExcelData['supplier_name'] = '';
							$rowExcelData['supplier_country'] = '';
							$rowExcelData['po_no./_sales_invoice_no.'] = '';
							$rowExcelData['invoice_amount_(gbp)'] = '';
							$rowExcelData['collection_/_delivery'] = '';
							$rowExcelData['buyer_delivery_date'] = '';					
							$rowExcelData['collection_date'] = '';
							$rowExcelData['logistic_delivery_date'] = '';
							$rowExcelData['final_total_(gbp)'] = '';
							
							$finalExportData[$warehouseCode][] = $rowExcelData;						
						}
						
					}
					
									
					/* if(isset($getAllCountryDetails) && !empty($getAllCountryDetails)){
						foreach ($summaryData as $warehouseCode => $summaryInfo){
							$totalAmountWarehouse = 0;
							foreach ($getAllCountryDetails as $getAllCountryDetail){
								$rowData = [];
								if(isset($getAllCountryDetail) && !empty($getAllCountryDetail)){
									
									$rowData['supp._country'] = $getAllCountryDetail->v_country_code;
									$rowData['total_pallets'] = '';
									$rowData['total_amount'] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]) ? round($summaryInfo[$getAllCountryDetail->v_country_code], 2) : '-');
									$totalAmountWarehouse = $totalAmountWarehouse + ((isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]) ? round($summaryInfo[$getAllCountryDetail->v_country_code], 2) : 0));
									
									$finalExportData['Summary'][$warehouseCode][] = $rowData;							
								}
							}
							
							$finalExportData['Summary'][$warehouseCode][] = [
									'supp._country' => 'total',
									'total_pallets' => '',
									'total_amount' => (isset($totalAmountWarehouse) && !empty($totalAmountWarehouse) ? round($totalAmountWarehouse, 2) : 0)
							];
						}					
					} */
					
					$summaryTotalAmountWarehouseDetails = [];
					
					foreach ($getAllCountryDetails as $getAllCountryDetail){
						$rowData = [];
						foreach ($summaryData as $warehouseCode => $summaryInfo){
							if(isset($getAllCountryDetail) && !empty($getAllCountryDetail)){						
								$rowData['supp._country__' . $warehouseCode] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) ? $getAllCountryDetail->v_country_code : '');
								$rowData['total_pallets__' . $warehouseCode] = '';
								$rowData['total_amount__' . $warehouseCode] = (isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]) ? round($summaryInfo[$getAllCountryDetail->v_country_code], 2) : '-');
								$summaryTotalAmountWarehouseDetails[$warehouseCode] = (isset($summaryTotalAmountWarehouseDetails[$warehouseCode]) && !empty($summaryTotalAmountWarehouseDetails[$warehouseCode]) ? $summaryTotalAmountWarehouseDetails[$warehouseCode] : 0) + ((isset($getAllCountryDetail->v_country_code) && !empty($getAllCountryDetail->v_country_code) && isset($summaryInfo[$getAllCountryDetail->v_country_code]) && !empty($summaryInfo[$getAllCountryDetail->v_country_code]) ? round($summaryInfo[$getAllCountryDetail->v_country_code], 2) : 0));
							}
						}
						$finalExportData['Summary'][] = $rowData;
					}
					
					$rowTotalData = [];
					foreach ($summaryTotalAmountWarehouseDetails as $warehouseCode => $summaryTotalAmountWarehouseDetail){
						$rowTotalData['supp._country__' . $warehouseCode] = 'Total';
						$rowTotalData['total_pallets__' . $warehouseCode] = '';
						$rowTotalData['total_amount__' . $warehouseCode] = $summaryTotalAmountWarehouseDetail;
					}
					
					if (isset($rowTotalData) && !empty($rowTotalData)){
						$finalExportData['Summary'][] = $rowTotalData;					
					}
				}
			
				if (!empty($finalExportData)) {
			
					if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {						
						$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.good-in-summary-report')]);
						$xlsData = $this->generateSpreadsheetMultiple(['record_detail' => $finalExportData, 'title' => trans('messages.good-in-buyer')]);
					} else {						
						$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.tracking-goods-in')]);
						$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.good-in-buyer')]);
					}
			
					$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
				} else {
			
					$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
				}
				return Response::json($response);
				die;
			}
			if(!empty($columnName)) {
				switch($columnName){
					case 'order_date':
						$columnName = 'gdm.dt_order_date';
						break;
					case 'entry_no':
						$columnName = 'gbd.v_goods_in_buyer_detail_no';
						break;
					case 'buyer_company':
						$columnName = 'cm.v_company_name';
						break;
					case 'user_company':
						$columnName = 'buyer_user_company_name';
						break;
					case 'buyer_name':
						$columnName = 'goods_buyer_name.v_name';
						break;
					case 'supplier_name':
						$columnName = 'sm.v_supplier_name';
						break;
					case 'po_no_sales_invoice_no':
						$columnName = 'gdm.v_po_sales_invoice_no';
						break;
					case 'payment_status':
						$columnName = 'gdm.e_payment_status';
						break;
					case 'collection_delivery':
						$columnName = 'gdm.e_collection_type';
						break;
					case 'delivery_type':
						$columnName = 'gdm.e_delivery_type';
						break;
					case 'logistic_entry_no':
						$columnName = 'glm.v_goods_in_logistic_master_no';
						break;
					case 'book_by':
						$columnName = 'logistic_book_by.logistic_book_by_name';
						break;
					case 'logistic_partner':
						$columnName = 'logistic_partner_master.v_logistic_partner_name';
						break;
					case 'collection_date':
						$columnName = 'glm.dt_collection_date';
						break;
					case 'tracking_no':
						$columnName = 'glm.v_tracking_no';
						break;
					case 'delivery_date':
						$columnName = 'glm.dt_delivery_date';
						break;
					case 'final_total':
						$columnName = 'glm.d_invoice_total';
						break;
					case 'status':
						$columnName = 'logistic_status.v_status';
						break;
					case 'logistic_delivery_type':
						$columnName = 'gld.e_collection_delivery_type';
						break;
					case 'delivery_collection_location':
						$columnName = 'wh.v_warehouse_name';
						break;
					
				}
				$whereData['order_by'] = [ $columnName =>  ( (!empty($columnSortOrder)) ? $columnSortOrder : 'DESC' ) ];
			} else {
				$whereData['order_by'] =  [ 'buyer_id' =>  'desc'] ;
			}
			$totalRecords = count($this->crudModel->getTrackingGoodInDetail ( $whereData  , $likeData  , $additionalData ));
			//echo $this->crudModel->last_query();echo "<br><br><br>";die;
			$whereData['offset'] = $offset ;
			
			$whereData['limit'] = $limit;
				
			$recordDetails = $this->crudModel->getTrackingGoodInDetail ( $whereData , $likeData , $additionalData  );
			//echo $this->crudModel->last_query();die;
			$finalData = [];
			if(!empty($recordDetails)){
				$index = $offset;
				$allSalesRole = [];
				foreach($recordDetails as $key => $recordDetail){
						
					$encodeRecordId = Wild_tiger::encode($recordDetail->buyer_id);
					$logisticRecordId = Wild_tiger::encode($recordDetail->logistic_record_id);
					
					$rowData = [];
					$rowData['sr_no'] = '<span style="text-align:center !important;display:block">'.++$index.'</span>';
					$rowData['order_date'] = (isset($recordDetail->dt_order_date) ? clientDate($recordDetail->dt_order_date) : '');
					$rowData['entry_no'] = (isset($recordDetail->v_goods_in_buyer_detail_no) ? ($recordDetail->v_goods_in_buyer_detail_no) : '');
					$rowData['buyer_company'] = (isset($recordDetail->v_company_name) ? $recordDetail->v_company_name :'');
					$rowData['user_company'] =(isset($recordDetail->buyer_user_company_name) ? $recordDetail->buyer_user_company_name :'');
					$rowData['buyer_name'] =(isset($recordDetail->goods_buyer_name) ? $recordDetail->goods_buyer_name :'');
					$rowData['supplier_name'] = (isset($recordDetail->v_supplier_name) ? $recordDetail->v_supplier_name . '<br>' .(isset($recordDetail->v_supplier_address) ? ' (' .$recordDetail->v_supplier_address.')' . '<br>' .(!empty($recordDetail->v_country_name) ? $recordDetail->v_country_name : '' ):'') :'');
					$rowData['po_no_sales_invoice_no'] = ( isset($recordDetail->v_po_sales_invoice_no) ?  ($recordDetail->v_po_sales_invoice_no) . '<br>'  :'' ) . ( isset($recordDetail->d_po_amount) ?  decimalAmount($recordDetail->d_po_amount) :'' ).' '.(isset($recordDetail->po_currency_code) ? ( $recordDetail->po_currency_code ) :'' );
					$rowData['payment_status'] = (isset($recordDetail->e_payment_status) ? $recordDetail->e_payment_status . (isset($recordDetail->d_payment_amount) ? '<br>' . decimalAmount($recordDetail->d_payment_amount ). (isset($recordDetail->payment_currency_code) ? ' '.$recordDetail->payment_currency_code : '' ) : '' ) : '' );
					$rowData['collection_delivery'] = (isset($recordDetail->e_collection_type) ? $recordDetail->e_collection_type : '').(!empty($recordDetail->e_ready_for_collection_status) ? '<br>' .$recordDetail->e_ready_for_collection_status :'');
					
					if( isset($recordDetail->e_collection_type) && ( $recordDetail->e_collection_type == config('constants.DELIVERY') ) && (!empty($recordDetail->gib_delivery_date)) ){
						$rowData['collection_delivery'] .= '<br>'.clientDate($recordDetail->gib_delivery_date);
					}
					
					$rowData['delivery_type'] = (isset($recordDetail->e_delivery_type) ? $recordDetail->e_delivery_type : '');
					$rowData['logistic_entry_no'] = (isset($recordDetail->v_goods_in_logistic_master_no) ? $recordDetail->v_goods_in_logistic_master_no : '');
					$rowData['book_by'] = (isset($recordDetail->logistic_book_by_name) ? $recordDetail->logistic_book_by_name : '');
					$rowData['logistic_partner'] = (isset($recordDetail->v_logistic_partner_name) ? $recordDetail->v_logistic_partner_name : '') . (isset($recordDetail->v_logistic_partner_code) ? '(' .$recordDetail->v_logistic_partner_code.')' : '');
					$rowData['collection_date'] = (isset($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) : '');
					$rowData['tracking_no'] = (isset($recordDetail->v_tracking_no) ? ($recordDetail->v_tracking_no) : '') .(isset($recordDetail->v_tracking_link) ? '<br>' .($recordDetail->v_tracking_link) : '');
					$rowData['delivery_date'] = (isset($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) : '');
					$rowData['final_total'] = (isset($recordDetail->d_invoice_total) ? decimalAmount($recordDetail->d_invoice_total) : '');
					$rowData['status'] = (isset($recordDetail->v_status) ? ($recordDetail->v_status) : '');
					$rowData['logistic_delivery_type'] = (isset($recordDetail->e_collection_delivery_type) ? ($recordDetail->e_collection_delivery_type) : '');
					$rowData['delivery_collection_location'] = (isset($recordDetail->v_warehouse_name) ? $recordDetail->v_warehouse_name  .(isset($recordDetail->v_warehouse_code) ? ' (' .( $recordDetail->v_warehouse_code ) .')' :'' ) :'' );
					$rowData['action'] = '';
					$rowData['action'] .= '<div class="custome-icon text-center">';
					$rowData['action'] .= '<button title="'.trans('messages.view-documents').'"  data-logistic-id="'.$logisticRecordId.'" data-buyer-name="'.(!empty($recordDetail->v_goods_in_buyer_detail_no) ? $recordDetail->v_goods_in_buyer_detail_no :'').'" data-record-id="'.$encodeRecordId.'" onclick="openDocumentModel(this)" class="btn btn-sm bg-theme btn-submit-class text-white"><i class="fa fa-eye"></i></button>';
					$rowData['action'] .= '</div>';
					$finalData[] = $rowData;
				}
			}
			
			$response = array(
					"draw" => intval($draw),
					"iTotalRecords" => count($finalData),
					"iTotalDisplayRecords" => $totalRecords,
					"aaData" => $finalData
			);
			
			return Response::json($response);die;
		}
		
	}
	public function getSupplierLocationDetails(Request $request){
		
		if( ( !empty($request->post('supplier_record_id') ) ) && ( $request->post('supplier_record_id') ) ){
			$supplierIds = explode("," , $request->post('supplier_record_id') );
			$whereData = [];
			if(!empty($supplierIds)){
				$supplierIds = array_map(function($supplierIds){
					return (int)Wild_tiger::decode($supplierIds);
				}, $supplierIds);
			}
				
			$getSupplierLocationDetails = SupplierDetailModel::with(['supplierMaster'])->whereHas('supplierMaster', function($query)use ($supplierIds)
			{
				$query->whereIn('i_supplier_id',$supplierIds);
					
			})->get();
			$html = '';
			if(!empty($getSupplierLocationDetails)){
				foreach ($getSupplierLocationDetails as $getSupplierLocationDetail){
					$encodeRecordId  = Wild_tiger::encode($getSupplierLocationDetail->i_id);
					$supplierAddres = $getSupplierLocationDetail->v_supplier_address;
					if((!empty($getSupplierLocationDetail->e_record_status)) && ($getSupplierLocationDetail->e_record_status == config('constants.COLLECTION'))){
						$html .= '<option value="'.$encodeRecordId.'">'.$getSupplierLocationDetail->supplierMaster->v_supplier_name. ' (' .( $supplierAddres ).')'. '</option>';
					}
				}
			}
		
			echo $html;die;
		}
	}
	/* public function getSupplierCountry(Request $request){
		if( ( !empty($request->post('supplier_location_id') ) ) && ( $request->post('supplier_location_id') ) ){
			$supplierLocationIds = explode("," , $request->post('supplier_location_id') );
			$whereData = [];
			if(!empty($supplierLocationIds)){
				$supplierLocationIds = array_map(function($supplierLocationIds){
					return (int)Wild_tiger::decode($supplierLocationIds);
				}, $supplierLocationIds);
			}
			
			$getSupplierLocationDetails = SupplierDetailModel::with(['supplierMaster','countryMaster'])->whereHas('supplierMaster', function($query)use ($supplierLocationIds)
			{
				$query->whereIn('i_supplier_id',$supplierLocationIds);
					
			})->get();
			$html = '';
			if(!empty($getSupplierLocationDetails)){
				foreach ($getSupplierLocationDetails as $getSupplierLocationDetail){
					$encodeRecordId  = Wild_tiger::encode($getSupplierLocationDetail->countryMaster->i_id);
					$html .= '<option value="'.$encodeRecordId.'">'.$getSupplierLocationDetail->countryMaster->v_country_name. '</option>';
				}
			}	
			echo $html;die;
		}
	} */
	public function fbaReportIndex(){
		if(checkPermission(config('permission_constants.VIEW_UK_TO_AMAZON_USA_FBA')) != true){
			return redirect('access-denied');
		}
		
		$data = [];
		$data ['pageTitle'] = trans('messages.fba').' '.trans('messages.report').' - '.trans('messages.uk-to-amazon-usa');
		
		$data['recordDetails'] = [];
		$data['totalRecordCount'] = (!empty($data['recordDetails']) ? count($data['recordDetails']) : 0 );
		return view($this->folderName . 'fba-report')->with($data);
	}
	
	public function fbaReportFilter(Request $request){
		
		$where = $whereData = $likeData = $data = [];
		
		$fbdDetailModal = new FBASheeteDetailModel();
		$recordDetails = [];
		
		if(!empty($request->post('search_by_fba_no'))){
			$searchShipmentInfo = explode(',',  $request->post('search_by_fba_no'));
			$whereData['search_fba_no'] = $searchShipmentInfo;
			$whereData['fba_po_no'] = true;
			$recordDetails = $fbdDetailModal->getFBASheetDetails($whereData);
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		
		if ($exportAction == 'export') {
			$finalExportData = [];
			$getExportRecordDetails = $recordDetails;
			if(!empty($getExportRecordDetails)){
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail){
					$fbaStatus = "";
					if((isset($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->uploadFBASheetInfo->e_status)) && ($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->uploadFBASheetInfo->e_status == config("constants.SUCCESS_STATUS"))){
						$containerStatus = (!empty($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->e_container_status) ? $getExportRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->e_container_status :'');
			
						switch ($containerStatus){
							case config('constants.PENDING_STATUS') :
								$fbaStatus = config("constants.PORT_TO_AGENT_WAREHOUSE_NO");
								break;
							case config('constants.PARTIAL_DELIVERY_TYPE') :
								$fbaStatus = config("constants.AGENT_WAREHOUSE_TO_AMAZON_NO");
								break;
							case config('constants.COMPLETED_STATUS') :
								$fbaStatus = config("constants.AGENT_WAREHOUSE_TO_AMAZON_COMPLETED_NO");
								break;
							default :
								$fbaStatus = config("constants.UK_OTHER_COUNTRY_TO_PORT_NO");
						}
					}
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['entry_no'] = (!empty($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no) ? $getExportRecordDetail->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no : '');
					$rowExcelData['container_no_(_status_)'] = (!empty($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->v_container_air_waybill_no) ? $getExportRecordDetail->fbaSheetMaster->countryToPortMaster->v_container_air_waybill_no .(!empty($fbaStatus) ? ' (' .$fbaStatus . ')' : ''): '');
					$rowExcelData['way_of_transport'] = (!empty($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->e_transport_way) ? $getExportRecordDetail->fbaSheetMaster->countryToPortMaster->e_transport_way : '');
					$rowExcelData['from_-_ort_/_airport'] = (!empty($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->fromPortInfo->v_warehouse_name) ? $getExportRecordDetail->fbaSheetMaster->countryToPortMaster->fromPortInfo->v_warehouse_name : '');
					$rowExcelData['to_-_port_/_airport'] = (!empty($getExportRecordDetail->fbaSheetMaster->countryToPortMaster->toPortInfo->v_warehouse_name) ? $getExportRecordDetail->fbaSheetMaster->countryToPortMaster->toPortInfo->v_warehouse_name : '');
					$rowExcelData['fba_/_po_or_invoice_/_wh_ref_no'] = (!empty($getExportRecordDetail->v_fba_po_no) ? $getExportRecordDetail->v_fba_po_no : '');
					$rowExcelData['destination'] = (!empty($getExportRecordDetail->e_destination) ? $getExportRecordDetail->e_destination : '');
					$rowExcelData['ref_id'] = (!empty($getExportRecordDetail->v_ref_id) ? $getExportRecordDetail->v_ref_id : '');
					$rowExcelData['company'] = (!empty($getExportRecordDetail->v_company_code) ? $getExportRecordDetail->v_company_code : '');
					$rowExcelData['products'] = (!empty($getExportRecordDetail->v_product) ? $getExportRecordDetail->v_product : '');
					$rowExcelData['fba_value'] = (!empty($getExportRecordDetail->v_fba_value) ? $getExportRecordDetail->v_fba_value : '');
					$rowExcelData['location'] = (!empty($getExportRecordDetail->v_location_code) ? $getExportRecordDetail->v_location_code : '');
					$rowExcelData['sku'] = (!empty($getExportRecordDetail->v_sku) ? $getExportRecordDetail->v_sku : '');
					$rowExcelData['units'] = (!empty($getExportRecordDetail->v_units) ? $getExportRecordDetail->v_units : '');
					$rowExcelData['amazon_address'] = (!empty($getExportRecordDetail->v_amazon_address) ? $getExportRecordDetail->v_amazon_address : '');
					$rowExcelData['boxes_(units)'] = (!empty($getExportRecordDetail->i_boxes_units) ? $getExportRecordDetail->i_boxes_units : '');
					$rowExcelData['boxes'] = (!empty($getExportRecordDetail->v_boxes) ? $getExportRecordDetail->v_boxes : '');
					$rowExcelData['pallet'] = (!empty($getExportRecordDetail->v_pallet) ? $getExportRecordDetail->v_pallet : '');
					$rowExcelData['total_no_of_pallets'] = (!empty($getExportRecordDetail->i_total_no_of_pallets) ? decimalAmount($getExportRecordDetail->i_total_no_of_pallets) : '');
					$rowExcelData['pallet_dimension'] = (!empty($getExportRecordDetail->v_pallet_dimension) ? $getExportRecordDetail->v_pallet_dimension : '');
					$rowExcelData['pallet_weight_(kg)'] = (!empty($getExportRecordDetail->v_pallet_weight) ? $getExportRecordDetail->v_pallet_weight : '');
					$rowExcelData['pallet_number'] = (!empty($getExportRecordDetail->i_pallet_no) ? $getExportRecordDetail->i_pallet_no : '');
					$rowExcelData['delivery_status'] = (!empty($getExportRecordDetail->e_status) ? $getExportRecordDetail->e_status : '');
					
					$finalExportData[] = $rowExcelData;
				}
			}
			
			if (!empty($finalExportData)) {
			
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.uk-to-amazon-usa')]);
			
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.uk-to-amazon-usa')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
			
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
			
			return Response::json($response);
			die;
		}
		
		$data['recordDetails'] = $recordDetails;
		//echo '<pre>';print_r($data['recordDetails']);
		$data['totalRecordCount'] = count($data['recordDetails']);
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'tracking-goods-in/fba-report-list' )->with ( $data )->render();
		echo $html;die;
		
	}
	public function usWarehouseFBAReportIndex(){
		if(checkPermission(config('permission_constants.VIEW_US_WAREHOUSE_FBA')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.fba').' '.trans('messages.report').' - '.trans('messages.us-warehouse');
		
		$data['recordDetails'] = [];
		$data['totalRecordCount'] = (!empty($data['recordDetails']) ? count($data['recordDetails']) : 0 );
		return view($this->folderName . 'us-warehouse-fba-report')->with($data);
	}
	public function usWarehouseFBAReportFilter(Request $request){
		$whereData = $data = [];
		$recordDetails = [];
		$usWarehouseDetails = new UsWarehouseToAmazonDetailsModel();
		
		if(!empty($request->post('search_by_fba_no'))){
			$searchShipmentInfo = explode(',',  $request->post('search_by_fba_no'));
			$whereData['fba_po_no'] = true;
			if(!empty($searchShipmentInfo)){
				$customWhere = "";
				$customWhere .= " ( ";
				foreach ($searchShipmentInfo as $searchShipment){
					$searchShipment = (!empty($searchShipment) ? trim($searchShipment) : '');
					$customWhere .= "(v_shipment_id Like '%".$searchShipment."%' or v_shipment_invoice_no Like '%".$searchShipment."%' or v_invoice_no_ref_no Like '%".$searchShipment."%' )";
					$customWhere .= " or ";
				}
				$customWhere = rtrim($customWhere,"or ");
				$customWhere .= " ) ";
				$whereData['custom_where'] = $customWhere;
				$recordDetails = $usWarehouseDetails->getRecordDetails($whereData);
			}
		}
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		if ($exportAction == 'export') {
			$finalExportData = [];
			$getExportRecordDetails = $recordDetails;
				
			if(!empty($getExportRecordDetails)){
				
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail){
					$userCompanyValue = ( isset($getExportRecordDetail->ukAccountCompnyMaster) ? json_decode(json_encode($getExportRecordDetail->ukAccountCompnyMaster),true) : [] );
					$userCompanyName = (!empty($userCompanyValue) ? array_column($userCompanyValue, 'v_company_name') : []);
					$userCompany = ( isset($userCompanyName) ? ( implode(', ', $userCompanyName)) : '');
					$shipmentName = '';
					if(!empty($getExportRecordDetail->v_shipment_id)){
						$shipmentName = $getExportRecordDetail->v_shipment_id;
					}
					if(!empty($getExportRecordDetail->v_shipment_invoice_no)){
					 $shipmentName = $getExportRecordDetail->v_shipment_invoice_no ;
					}
					if(!empty($getExportRecordDetail->v_invoice_no_ref_no)){
					 $shipmentName = $getExportRecordDetail->v_invoice_no_ref_no ;
					}
					$usWarehouseName = '';
					if(!empty($getExportRecordDetail->amazonFromWarehouseInfo->v_warehouse_name)){
						$usWarehouseName = (!empty($getExportRecordDetail->amazonFromWarehouseInfo->v_warehouse_name) ? $getExportRecordDetail->amazonFromWarehouseInfo->v_warehouse_name .(!empty($getExportRecordDetail->amazonFromWarehouseInfo->v_warehouse_code) ? ' ('.$getExportRecordDetail->amazonFromWarehouseInfo->v_warehouse_code.')' :'') :'');
					}
					if(!empty($getExportRecordDetail->fromWarehouseInfo->v_warehouse_name)){
						$usWarehouseName = (!empty($getExportRecordDetail->fromWarehouseInfo->v_warehouse_name) ? $getExportRecordDetail->fromWarehouseInfo->v_warehouse_name .(!empty($getExportRecordDetail->fromWarehouseInfo->v_warehouse_code) ? ' ('.$getExportRecordDetail->fromWarehouseInfo->v_warehouse_code.')' :'') :'');
					}
					if(!empty($getExportRecordDetail->ukFromWarehouseInfo->v_warehouse_name)){
						$usWarehouseName = (!empty($getExportRecordDetail->ukFromWarehouseInfo->v_warehouse_name) ? $getExportRecordDetail->ukFromWarehouseInfo->v_warehouse_name .(!empty($getExportRecordDetail->ukFromWarehouseInfo->v_warehouse_code) ? ' ('.$getExportRecordDetail->ukFromWarehouseInfo->v_warehouse_code.')' :'') :'');
					}
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['entry_no'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->v_us_warehouse_to_amazon_record_no) ? $getExportRecordDetail->usWarehouseToAmazonMaster->v_us_warehouse_to_amazon_record_no :'');
					$rowExcelData['way_of_transport'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->e_transport_way) ? $getExportRecordDetail->usWarehouseToAmazonMaster->e_transport_way : '');
					$rowExcelData['book_by'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->bookByEmployee->v_name) ? $getExportRecordDetail->usWarehouseToAmazonMaster->bookByEmployee->v_name .( isset($getExportRecordDetail->bookByEmployee->v_department) ? ' ('.$getExportRecordDetail->bookByEmployee->v_department.')'  : '' ) :'');
					$rowExcelData['logistic_partner'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name .( isset($getExportRecordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->v_logistic_partner_code.')'  : '' ) :'');
					$rowExcelData['status'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->statusInfo->v_status) ? $getExportRecordDetail->usWarehouseToAmazonMaster->statusInfo->v_status :'');
					$rowExcelData['fba_/_po_or_invoice_/_wh_ref_no'] = (!empty($shipmentName) ? $shipmentName : '');
					$rowExcelData['to'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->e_to_location) ? $getExportRecordDetail->usWarehouseToAmazonMaster->e_to_location : '');
					$rowExcelData['ref_id'] = (!empty($getExportRecordDetail->v_ref_id) ? $getExportRecordDetail->v_ref_id : '');
					if(!empty($getExportRecordDetail->accountComapnyInfo->v_company_name)){
						$rowExcelData['account'] = (!empty($getExportRecordDetail->accountComapnyInfo->v_company_name) ? $getExportRecordDetail->accountComapnyInfo->v_company_name . (!empty($getExportRecordDetail->accountComapnyInfo->v_company_code) ? ' ('  .$getExportRecordDetail->accountComapnyInfo->v_company_code. ')' :'') :'');
					}
					if(!empty($userCompany)){
						$rowExcelData['account'] = $userCompany ;
					};
					$rowExcelData['customer_name'] = (!empty($getExportRecordDetail->customerInfo->v_customer_name) ? $getExportRecordDetail->customerInfo->v_customer_name : '');
					$rowExcelData['products'] = (!empty($getExportRecordDetail->v_product) ? $getExportRecordDetail->v_product : '');
					$rowExcelData['from_(us_warehouse)'] = (!empty($usWarehouseName) ? $usWarehouseName : '');
					$rowExcelData['to_(amazon_location)'] = (!empty($getExportRecordDetail->toAmazonLocationInfo->v_warehouse_name) ? $getExportRecordDetail->toAmazonLocationInfo->v_warehouse_name .(!empty($getExportRecordDetail->toAmazonLocationInfo->v_warehouse_code) ? ' ('.$getExportRecordDetail->toAmazonLocationInfo->v_warehouse_code.')' :'') :'');
					$rowExcelData['to_(customer_location)'] = (!empty($getExportRecordDetail->toCustomerLocationInfo->v_customer_codes) ? $getExportRecordDetail->toCustomerLocationInfo->v_customer_codes .(!empty($getExportRecordDetail->toCustomerLocationInfo->v_customer_address) ? ' ('.$getExportRecordDetail->toCustomerLocationInfo->v_customer_address.')' :'') :'');
					$rowExcelData['to(uk_warehouse)'] = (!empty($getExportRecordDetail->ukToWarehouseInfo->v_warehouse_name) ? $getExportRecordDetail->ukToWarehouseInfo->v_warehouse_name .(!empty($getExportRecordDetail->ukToWarehouseInfo->v_warehouse_code) ? ' ('.$getExportRecordDetail->ukToWarehouseInfo->v_warehouse_code.')' :'') :'');
					$rowExcelData['sku'] = (!empty($getExportRecordDetail->v_sku) ? $getExportRecordDetail->v_sku : '');
					if(!empty($getExportRecordDetail->v_units)){
						$rowExcelData['units'] = (!empty($getExportRecordDetail->v_units) ? $getExportRecordDetail->v_units :'');
					}
					if(!empty($getExportRecordDetail->v_uk_unit)){
						$rowExcelData['units'] = (!empty($getExportRecordDetail->v_uk_unit) ? $getExportRecordDetail->v_uk_unit :'');
					}
					if(!empty($getExportRecordDetail->v_uk_box_pallet)){
						$rowExcelData['boxes_/_pallet'] = (!empty($getExportRecordDetail->v_uk_box_pallet) ? $getExportRecordDetail->v_uk_box_pallet :'');
					}
					if(!empty($getExportRecordDetail->v_box_pallet)){
						$rowExcelData['boxes_/_pallet'] = (!empty($getExportRecordDetail->v_box_pallet) ? $getExportRecordDetail->v_box_pallet :'');
					}
					$rowExcelData['price'] = (!empty($getExportRecordDetail->d_price) ? decimalAmount($getExportRecordDetail->d_price) : '');
					$rowExcelData['booking_date'] = (!empty($getExportRecordDetail->dt_booking_date) ? clientDate($getExportRecordDetail->dt_booking_date) : '');
					$rowExcelData['collection_date'] = (!empty($getExportRecordDetail->dt_collection_date) ? clientDate($getExportRecordDetail->dt_collection_date) : '');
					$rowExcelData['delivery_date'] = (!empty($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '');
					$rowExcelData['remarks'] = (!empty($getExportRecordDetail->v_remarks) ? $getExportRecordDetail->v_remarks : '');
					$rowExcelData['tracking_no'] = (!empty($getExportRecordDetail->usWarehouseToAmazonMaster->v_tracking_no) ? $getExportRecordDetail->usWarehouseToAmazonMaster->v_tracking_no : '');
					$rowExcelData['tracking_link'] = (!empty($getExportRecordDetail->v_tracking_link) ? $getExportRecordDetail->v_tracking_link : '');
					$rowExcelData['amazon_appointment_date'] = (!empty($getExportRecordDetail->dt_amazon_appointment_date) ? clientDate($getExportRecordDetail->dt_amazon_appointment_date) : '');
					$rowExcelData['amazon_appointment_id'] = (!empty($getExportRecordDetail->v_amazon_appointment_id) ? $getExportRecordDetail->v_amazon_appointment_id : '');
					
					$finalExportData[] = $rowExcelData;
				}
			}
			if (!empty($finalExportData)) {
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.us-warehouse')]);
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.us-warehouse')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
			
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
			
			return Response::json($response);
			die;
		}
		$data['recordDetails'] = $recordDetails;
		
		$data['totalRecordCount'] = count($data['recordDetails']);
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'tracking-goods-in/us-warehouse-fba-report-list' )->with ( $data )->render();
		echo $html;die;
		
	}
	public function europeToAmazonReportIndex(){
		if(checkPermission(config('permission_constants.VIEW_AMAZON_EU_FBA')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.fba').' '.trans('messages.report').' - '.trans('messages.amazon-eu');
		
		$data['recordDetails'] = [];
		$data['totalRecordCount'] = (!empty($data['recordDetails']) ? count($data['recordDetails']) : 0 );
		return view($this->folderName . 'europe-to-amazon-report')->with($data);
	}
	public function europeToAmazonFilter(Request $request){
		$whereData = $data = [];
		$recordDetails = [];
		$toAmazonDetails = new CountrytoPortEuropeDetailModel();
		if(!empty($request->post('search_by_fba_no'))){
			$searchShipmentInfo = explode(',',  $request->post('search_by_fba_no'));
			$whereData['search_fba_no'] = $searchShipmentInfo;
			$whereData['fba_po_no'] = true;
			$recordDetails = $toAmazonDetails->getRecordDetails($whereData);
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		if ($exportAction == 'export') {
			$finalExportData = [];
			$getExportRecordDetails = $recordDetails;
				
			if(!empty($getExportRecordDetails)){
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail){
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['fba_/_po_or_invoice_/_wh_ref_no'] = (!empty($getExportRecordDetail->v_shipment_id) ? $getExportRecordDetail->v_shipment_id : '');
					$rowExcelData['ref_id'] = (!empty($getExportRecordDetail->v_ref_id) ? $getExportRecordDetail->v_ref_id : '');
					$rowExcelData['account'] = (!empty($getExportRecordDetail->accountCompany->v_company_name) ? $getExportRecordDetail->accountCompany->v_company_name : '');
					$rowExcelData['from_(warehouse)'] = (!empty($getExportRecordDetail->warehouse->v_warehouse_name) ? $getExportRecordDetail->warehouse->v_warehouse_name : '');
					$rowExcelData['to_(amazon_location)'] = (!empty($getExportRecordDetail->location->v_warehouse_name) ? $getExportRecordDetail->location->v_warehouse_name : '');
					$rowExcelData['sku'] = (!empty($getExportRecordDetail->v_sku) ? $getExportRecordDetail->v_sku : '');
					$rowExcelData['units'] = (!empty($getExportRecordDetail->v_units) ? $getExportRecordDetail->v_units : '');
					$rowExcelData['price'] = (!empty($getExportRecordDetail->v_price) ? decimalAmount($getExportRecordDetail->v_price) : '');
					$rowExcelData['booking_date'] = (!empty($getExportRecordDetail->dt_booking_date) ? clientDate($getExportRecordDetail->dt_booking_date) : '');
					$rowExcelData['collection_date'] = (!empty($getExportRecordDetail->dt_collection_date) ? clientDate($getExportRecordDetail->dt_collection_date) : '');
					$rowExcelData['delivery_date'] = (!empty($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '');
					$rowExcelData['tracking_no'] = (!empty($getExportRecordDetail->countryToPortEurope->v_tracking_no) ? $getExportRecordDetail->countryToPortEurope->v_tracking_no : '');
					$rowExcelData['tracking_link'] = (!empty($getExportRecordDetail->v_tracking_link) ? $getExportRecordDetail->v_tracking_link : '');
					$rowExcelData['amazon_appointment_date'] = (!empty($getExportRecordDetail->dt_amazon_appointment_date) ? clientDate($getExportRecordDetail->dt_amazon_appointment_date) : '');
					$rowExcelData['amazon_appointment_id'] = (!empty($getExportRecordDetail->v_amazon_appointment_id) ? $getExportRecordDetail->v_amazon_appointment_id : '');
					
					$finalExportData[] = $rowExcelData;
				}
			}
				
			if (!empty($finalExportData)) {
					
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.amazon-eu')]);
					
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.amazon-eu')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
					
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
				
			return Response::json($response);
			die;
		}
		$data['recordDetails'] = $recordDetails;
		$data['totalRecordCount'] = count($data['recordDetails']);
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'tracking-goods-in/europe-to-amazon-report-list' )->with ( $data )->render();
		echo $html;die;
		
	}
	
	public function viewDocumentDetails(Request $request){
		
		$buyerRecordId = (!empty($request->input('buyer_record_id')) ? (int)Wild_tiger::decode($request->input('buyer_record_id')) : 0 );
		$logicticRecordId = (!empty($request->input('logistic_record_id')) ? (int)Wild_tiger::decode($request->input('logistic_record_id')) : 0 );
		$html  = "";
		if($buyerRecordId > 0 ){
			$logisticWhere = $buyerWehre = [];
			$buyerWehre['master_id'] = $buyerRecordId;
			$logisticWhere['master_id'] = $logicticRecordId;
			
			$goodInBuyerDetails = $this->goodInBuyerDetailModel->getGoodsInBuyerDetails($buyerWehre);
			$goodInLogisticDetails = [];
			if( $logicticRecordId > 0 ){
				$goodInLogisticDetails = $this->goodInLogisticModel->getGoodsInLogisticMaster($logisticWhere);
			}
			
			
			$allGoodInBuyerInfo = (!empty($goodInBuyerDetails) ? $goodInBuyerDetails[0] : []);
			$allGoodInLogisticInfo = (!empty($goodInLogisticDetails) ? $goodInLogisticDetails[0] : []);
			$index = 0;
			if(!empty($allGoodInBuyerInfo->goodInBuyerMaster->goodInBuyerDocument)){
				
				foreach ($allGoodInBuyerInfo->goodInBuyerMaster->goodInBuyerDocument as $goodInBuyerDocumentInfo){
					$documentFiles = (json_decode($goodInBuyerDocumentInfo->v_document_file_path));
					$imagePath = '';
					
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$index.'</td>';
					$html .= '<td>'.(!empty($goodInBuyerDocumentInfo->documentTypeMaster->v_document_type_name) ? $goodInBuyerDocumentInfo->documentTypeMaster->v_document_type_name :'').'</td>';
					$html .= '<td>'.(!empty($goodInBuyerDocumentInfo->v_document_remark) ? $goodInBuyerDocumentInfo->v_document_remark :'').'</td>';
					$html .= '<td class="actions-col">';
					if(!empty( $documentFiles)){
						foreach ($documentFiles as $documentFile){
							//if(!empty( $documentFile) && file_exists( config('constants.FILE_STORAGE_URL_PATH') . $documentFile ) ){
							$imagePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
							$html .= '<a title="'.basename($imagePath).'" href="'.$imagePath.'" target="_blank" class="btn btn-sm btn-danger mr-1 download-icon-items"><i class="fa fa-download"></i></a>';
								
							//}
						}
					} 
					$html .= '</td>';
					$html .= '</tr>';
				}
			}
			if(!empty($allGoodInLogisticInfo->goodInLogisticDocument)){
				foreach ($allGoodInLogisticInfo->goodInLogisticDocument as $goodInLogisticDocumentInfo){
					$documentFiles = (json_decode($goodInLogisticDocumentInfo->v_document_file_path));
					$imagePath = '';
					
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$index.'</td>';
					$html .= '<td>'.(!empty($goodInLogisticDocumentInfo->documentTypeMaster->v_document_type_name) ? $goodInLogisticDocumentInfo->documentTypeMaster->v_document_type_name :'').'</td>';
					$html .= '<td>'.(!empty($goodInLogisticDocumentInfo->v_document_remark) ? $goodInLogisticDocumentInfo->v_document_remark :'').'</td>';
					$html .= '<td class="actions-col">';
					if(!empty( $documentFiles)){
						foreach ($documentFiles as $documentFile){
							//if(!empty( $documentFile) && file_exists( config('constants.FILE_STORAGE_URL_PATH') . $documentFile ) ){
							$imagePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
							$html .= '<a title="'.basename($imagePath).'" href="'.$imagePath.'" target="_blank" class="btn btn-sm btn-danger mr-1 download-icon-items"><i class="fa fa-download"></i></a>';
								
							//}
						}
					} 
					$html .= '</td>';
					$html .= '</tr>';
				}
			}
		}
		if(empty($html)){
			$html = '<tr><td colspan="4" class="text-center">'.trans('messages.no-record-found').'</td></tr>';
		}
		
		echo $html;die;
	}
}
