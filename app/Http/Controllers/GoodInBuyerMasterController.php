<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\GoodInBuyerMasterModel;
use App\CompanyMasterModel;
use App\Login;
use App\SupplierMasterModel;
use App\CurrencyMasterModel;
use App\WarehouseMasterModel;
use App\DimensionMasterModel;
use App\Document_Type_Master_Model;
use Illuminate\Http\Request;
use App\Helpers\Twt\Wild_tiger;
use App\SupplierDetailModel;
use App\GoodInBuyerDetailModel;
use Illuminate\Support\Facades\Response;
use App\StatusMasterModel;
use App\CountryMasterModel;
use App\Rules\UniquePoSalesInvoiceNumber;
use Illuminate\Support\Facades\DB;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\Log;
use App\LookupMaster;
use App\GoodInLogisticMasterModel;
use App\Models\WarehousePalletMasterModel;
use App\Rules\ValidatePalletLimit;
use App\ReportModel;

class GoodInBuyerMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.GOODS_IN_BUYER_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'good-in-buyer/';
		$this->moduleName = trans('messages.good-in-buyer');
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.GOODS_IN_BUYER_MASTER_URL');
		$this->crudModel = new GoodInBuyerMasterModel();
		$this->reportModel = new ReportModel();
	
	}
	public function index(Request $request){
		if(checkPermission(config('permission_constants.VIEW_GOODS_IN_BUYER')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.good-in-buyer');
		//$data['paymentStatusInfo'] = paymentStatus();
		$data['collectionDeliveryInfo'] = collectionDeliveryInfo();
		$data['deliveryTypeInfo'] = deliveryTypeInfo();
		//$data['customProcedureInfo'] = customProcedureInfo();
		$data['customsProcedureInfo'] = customsProcedureDropdown();
		$data['dangerousGoodsInfo'] = dangerousGoodsInfo();
		$data['palletsTypeInfo'] = palletsTypeInfo();
		$data['palletBoxInfo'] = typeInfo();
		$data['companyRecordDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['userBuyerRecordDetails'] = Login::where('v_role' , config('constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['supplierRecordDetails'] = SupplierMasterModel::orderBy('v_supplier_name', 'ASC')->get();
		$data['dimensionBoxRecordDetails'] = DimensionMasterModel::where('e_dimension',config ( 'constants.BOX'))->orderBy('v_dimension_name', 'ASC')->get();
		$data['dangerousGoodsDetails'] = LookupMaster::where('v_module_name',config('constants.DANGEROUS_GOODS_LOOKUP'))->orderBy('v_value')->get();
		$data['dimensionPalletRecordDetails'] = DimensionMasterModel::where('e_dimension',config ( 'constants.PALLET'))->orderBy('v_dimension_name', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type' , config ( 'constants.BUYER') )->orderBy('v_document_type_name', 'ASC')->get();
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['readyForCollectionInfo'] = dangerousGoodsInfo();
		$data['dimensionRecordDetails'] = DimensionMasterModel::orderBy('v_dimension_name', 'ASC')->get();
		$data['countryMasterDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
		$data['poCreateUserDetails'] = Login::where('v_role',config('constants.ROLE_USER'))->orderBy('v_name' , 'asc')->get();
		$data['paymentTermsDetails'] = LookupMaster::where('v_module_name',config('constants.PAYMENT_TERMS_LOOKUP'))->orderBy('v_value')->get();
		$data['goodsRemarksDetails'] = LookupMaster::where('v_module_name',config('constants.GOODS_REMARK_LOOKUP'))->orderBy('v_value')->get();
		$warehouseWhere = [];
		$userLogin = session()->get('user_id');
		$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
		$loggedBuyerUserRole = ( isset($userLoginDetails[0]->v_record_type) ? explode("," , $userLoginDetails[0]->v_record_type ) : [] );
		
		
		if(count($userLoginDetails) > 0){
			if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
				$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :0 );
				$warehouseWhere['i_id'] = $warehouseIds;
			}
		}
		$data['warehouseDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['statusMasterDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		
		$where = $statusIds = [];
		//$where['show_not_completed_record'] = true;
		$where['all_delivered_cancelled_ststus'] = 0;
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		//$where['default_status'] = $statusIds;
		$where['warehouse_id'] = (isset($warehouseWhere['i_id']) ? $warehouseWhere['i_id'] : '');
		$where['loggedUserBuyerRoles'] = $loggedBuyerUserRole;
		$data['loggedUserBuyerRoles'] = $loggedBuyerUserRole;
		
		// Handle URL parameters for filtering
		$warehouse = $request->get('warehouse');
		$deliveryDate = $request->get('delivery_date');
		
		// Debug logging
		\Log::info('URL Parameters - Warehouse: ' . $warehouse);
		\Log::info('URL Parameters - Delivery Date: ' . $deliveryDate);

		if($warehouse) {
			// Extract warehouse name from URL parameter
			$warehouseName = urldecode($warehouse);
			\Log::info('Decoded Warehouse Name: ' . $warehouseName);
			
			// Strip suffixes like (NEIL-W/H), (TFC), etc. to match database names
			$cleanWarehouseName = preg_replace('/\s*\([^)]*\)/', '', $warehouseName);
			\Log::info('Cleaned Warehouse Name: ' . $cleanWarehouseName);
			
			$where['delivery_location_name'] = $cleanWarehouseName;
		}
		
		if($deliveryDate) {
			// Use single date as both from and to date for filtering that specific date
			\Log::info('Setting delivery date range: ' . $deliveryDate);
			$where['buyer_delivery_from_date'] = $deliveryDate;
			$where['buyer_delivery_to_date'] = $deliveryDate;
		}
		
		$data['recordDetails'] = $this->crudModel->getGoodsInBuyerDetails($where);
		
		// Get total record count (not just current page)
		$where['count_record'] = true;
		$totalRecords = $this->crudModel->getGoodsInBuyerDetails($where);
		$data['totalRecordCount'] = $totalRecords->count();
		
		//$data['statusInfo'] = $statusIds;
		
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		return view($this->folderName . 'good-in-buyer')->with($data);
	
	}
	
	public function filter(Request $request){
		
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		//$whereData['group_by'] = 'sm.i_id';
		//search record
		if (!empty($request->post('search_by_logistic_partner_name'))) {
			$likeData['searchBy'] = trim($request->post('search_by_logistic_partner_name'));
		}
		if(!empty($request->post('search_buyer_company'))){
			$allBuyerCompanyIds = explode("," , $request->post('search_buyer_company') );
			if(!empty($allBuyerCompanyIds)){
				$allBuyerCompanyIds = array_map(function($allBuyerCompanyId){
					return (int)Wild_tiger::decode($allBuyerCompanyId);
				}, $allBuyerCompanyIds);
			}
			if(!empty($allBuyerCompanyIds)){
				$whereData['company'] =  $allBuyerCompanyIds;
			}
		}
		
		/* if(!empty($request->post('search_user_company'))){
			$allUserCompanyIds = explode("," , $request->post('search_user_company') );
			if(!empty($allUserCompanyIds)){
				$allUserCompanyIds = array_map(function($allUserCompanyId){
					return (int)Wild_tiger::decode($allUserCompanyId);
				}, $allUserCompanyIds);
			}
			if(!empty($allUserCompanyIds)){
				$whereData['user_company'] =  $allUserCompanyIds;
			}
		} */
		
		/* if( ( !empty($request->post('search_buyer_name') ) ) && ( $request->post('search_buyer_name') ) ){
			$allUserIds = explode("," , $request->post('search_buyer_name') );
			if(!empty($allUserIds)){
				$allUserIds = array_map(function($allUserId){
					return (int)Wild_tiger::decode($allUserId);
				}, $allUserIds);
			}
			if(!empty($allUserIds)){
				$whereData['employee'] =  $allUserIds;
			}
		
		} */
		if(!empty($request->post('search_supplier_name'))){
			$supplierIds = explode("," , $request->post('search_supplier_name') );
			if(!empty($supplierIds)){
				$supplierIds = array_map(function($supplierId){
					return (int)Wild_tiger::decode($supplierId);
				}, $supplierIds);
			}
			if(!empty($supplierIds)){
				$whereData['supplier'] =  $supplierIds;
			}
		
		}
		if(!empty($request->post('search_supplier_location'))){
			$supplierLocationIds = explode("," , $request->post('search_supplier_location') );
			if(!empty($supplierLocationIds)){
				$supplierLocationIds = array_map(function($supplierLocationId){
					return (int)Wild_tiger::decode($supplierLocationId);
				}, $supplierLocationIds);
			}
			if(!empty($supplierLocationIds)){
				$whereData['supplier_location'] =  $supplierLocationIds;
			}
		}
		
		if(!empty($request->post('search_order_from_date'))){
			// Use UI field as Buyer Delivery From Date
			$whereData['buyer_delivery_from_date'] = $request->post('search_order_from_date');
		}
		if(!empty($request->post('search_order_to_date') )){
			// Use UI field as Buyer Delivery To Date
			$whereData['buyer_delivery_to_date'] = $request->post('search_order_to_date');
		}

		// Handle URL parameters persisted through AJAX for pagination and excel export
		if (!empty($request->post('url_delivery_date'))) {
			// Only use URL date if UI fields are empty (meaning user hasn't explicitly overridden them)
			if (empty($whereData['buyer_delivery_from_date']) && empty($whereData['buyer_delivery_to_date'])) {
				$whereData['buyer_delivery_from_date'] = $request->post('url_delivery_date');
				$whereData['buyer_delivery_to_date'] = $request->post('url_delivery_date');
			}
		}

		if (!empty($request->post('url_warehouse'))) {
			// Only apply URL warehouse if no specific location is selected in the UI
			if (empty($whereData['delivery_collection_location'])) {
				$warehouseName = urldecode($request->post('url_warehouse'));
				$cleanWarehouseName = preg_replace('/\s*\([^)]*\)/', '', $warehouseName);
				$whereData['delivery_location_name'] = $cleanWarehouseName;
			}
		}
		/* if(!empty($request->post('search_payment_status') )){
			$whereData['payment_status'] = ($request->post('search_payment_status'));
		} */
		if(!empty($request->post('search_collection_delivery') )){
			$whereData['collection_type'] = $request->post('search_collection_delivery');
			
			if ($request->post('search_collection_delivery') == config('constants.DELIVERY')){
				if(!empty($request->post('search_buyer_delivery_from_date'))){
					$whereData['buyer_delivery_from_date'] = $request->post('search_buyer_delivery_from_date');
				}
				if(!empty($request->post('search_buyer_delivery_to_date') )){
					$whereData['buyer_delivery_to_date'] = $request->post('search_buyer_delivery_to_date');
				}
			}
			if ($request->post('search_collection_delivery') == config('constants.COLLECTION')){
				if(!empty($request->post('search_ready_for_collection') )){
					$whereData['ready_for_collection'] = ($request->post('search_ready_for_collection'));
				}
			}
		}
		/* if(!empty($request->post('search_payment_from_date') )){
			$whereData['payment_from_date'] = ($request->post('search_payment_from_date'));
		}
		if(!empty($request->post('search_payment_to_date') )){
			$whereData['payment_to_date'] = ($request->post('search_payment_to_date'));
		}
		if(!empty($request->post('search_delivery_type') )){
			$whereData['delivery_type'] = ($request->post('search_delivery_type'));
		}
		if(!empty($request->post('search_delivery_location'))){
			$whereData['delivery_location'] = (int)Wild_tiger::decode($request->post('search_delivery_location'));
		} 
		if(!empty($request->post('search_custom_procedure_export') )){
			$whereData['custome_procedure_export'] = ($request->post('search_custom_procedure_export'));
		}
		if(!empty($request->post('search_custom_procedure_import') )){
			$whereData['custome_procedure_import'] = ($request->post('search_custom_procedure_import'));
		}
		if(!empty($request->post('search_payment_terms'))){
			$whereData['payment_terms'] = (int)Wild_tiger::decode($request->post('search_payment_terms'));
		}
		if(!empty($request->post('search_dangerous_goods'))){
			$whereData['dangerous_goods'] = (int)Wild_tiger::decode($request->post('search_dangerous_goods'));
		}
		if(!empty($request->post('search_goods_remark'))){
			$goodsRemarkIds = explode("," , $request->post('search_goods_remark') );
			if(!empty($goodsRemarkIds)){
				$allGoodsRemarkIds = array_map(function($allGoodsRemarkId){
					return (int)Wild_tiger::decode($allGoodsRemarkId);
				}, $goodsRemarkIds);
				
				if(!empty($allGoodsRemarkIds)){
					$whereData['goods_remark'] = $allGoodsRemarkIds;
				}
			}
		}
		if(!empty($request->post('search_pallet_box_dimension') )){
			$alldimensionIds = explode("," , $request->post('search_pallet_box_dimension') );
			if(!empty($alldimensionIds)){
				$allimensionIds = array_map(function($alldimensionId){
					return (int)Wild_tiger::decode($alldimensionId);
				}, $alldimensionIds);
			}
			if(!empty($allimensionIds)){
				$whereData['pallet_box_dimension'] = $allimensionIds;
			}
		}
		if(!empty($request->post('search_boxes_dimension'))){
			$allBoxDimensionIds = explode("," , $request->post('search_boxes_dimension') );
			if(!empty($allBoxDimensionIds)){
				$allBoxDimensionIds = array_map(function($allBoxDimensionId){
					return (int)Wild_tiger::decode($allBoxDimensionId);
				}, $allBoxDimensionIds);
			}
			if(!empty($allBoxDimensionIds)){
				$whereData['box_dimension'] =  $allBoxDimensionIds;
			}
		}
		if(!empty($request->post('search_pallets_dimension') )){
			$allPalletDimensionIds = explode("," , $request->post('search_pallets_dimension') );
			if(!empty($allPalletDimensionIds)){
				$allPalletDimensionIds = array_map(function($allPalletDimensionId){
					return (int)Wild_tiger::decode($allPalletDimensionId);
				}, $allPalletDimensionIds);
			}
			if(!empty($allPalletDimensionIds)){
				$whereData['pallet_dimension'] =  $allPalletDimensionIds;
			}
		} */
		if(!empty($request->post('search_box_pallet_type') )){
			$whereData['box_pallet_type'] = ($request->post('search_box_pallet_type'));
		}
		if(!empty($request->post('search_invoice_from_date') )){
			$whereData['invoice_from_date'] = trim($request->post('search_invoice_from_date'));
		}
		if(!empty($request->post('search_invoice_to_date'))){
			$whereData['invoice_to_date'] = $request->post('search_invoice_to_date');
		}
		if(!empty($request->post('search_actual_payment_from_date'))){
			$whereData['actual_payment_from_date'] = $request->post('search_actual_payment_from_date');
		}
		if(!empty($request->post('search_actual_payment_to_date') )){
			$whereData['actual_payment_to_date'] = trim($request->post('search_actual_payment_to_date'));
		}
		if(!empty($request->post('search_buyer_name'))){
			$allEmployeeIds = explode("," , $request->post('search_buyer_name') );
			if(!empty($allEmployeeIds)){
				$allEmployeeIds = array_map(function($allEmployeeId){
					return (int)Wild_tiger::decode($allEmployeeId);
				}, $allEmployeeIds);
			}
			if(!empty($allEmployeeIds)){
				$whereData['employee_name'] =  $allEmployeeIds;
			}
		}
		if(!empty($request->post('search_user_buyer_name'))){
			$allUserBuyerIds = explode("," , $request->post('search_user_buyer_name') );
			if(!empty($allUserBuyerIds)){
				$allUserBuyerIds = array_map(function($allUserBuyerId){
					return (int)Wild_tiger::decode($allUserBuyerId);
				}, $allUserBuyerIds);
			}
			if(!empty($allUserBuyerIds)){
				$whereData['user_buyer_name'] =  $allUserBuyerIds;
			}
		}
		/* if(!empty($request->post('search_pallets_type') )){
			$whereData['pallets_type'] = ($request->post('search_pallets_type'));
		} */
		if(!empty($request->post('search_delivery_collection_location'))){
			$whereData['delivery_collection_location'] = (int)Wild_tiger::decode($request->post('search_delivery_collection_location'));
		}
		if( ( !empty($request->post('search_status') ) )){
			$allStatusIds = explode("," , $request->post('search_status') );
			if(!empty($allStatusIds)){
				$allStatusIds = array_map(function($allStatusId){
					return (int)Wild_tiger::decode($allStatusId);
				}, $allStatusIds);
				if(!empty($allStatusIds)){
					$whereData['status'] =  $allStatusIds;
				}
			}
		} else {
			$whereData['all_delivered_cancelled_ststus'] = 0;
		}
		if(!empty($request->post('search_supplier_country'))){
			$whereData['supplier_country'] = (int)Wild_tiger::decode($request->post('search_supplier_country'));
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		
		$userLogin =  session()->get('user_id');
		$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
		$loggedBuyerUserRole = ( isset($userLoginDetails[0]->v_record_type) ? explode("," , $userLoginDetails[0]->v_record_type ) : [] );
		$whereData['loggedUserBuyerRoles'] = $loggedBuyerUserRole;
		if(count($userLoginDetails) > 0){
			if( (!empty($loggedBuyerUserRole)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , $loggedBuyerUserRole ) ) ){
				$warehouseId = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :'');
			}
		}
		$whereData['warehouse_id'] = (isset($warehouseId) ? $warehouseId :'');
		
		if ($exportAction == 'export') {
			$finalExportData = [];
			$whereData['count_record'] = true;
			$getExportRecordDetails = $this->crudModel->getGoodsInBuyerDetails($whereData, $likeData);
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					$goodsRemarksArray = (isset($getExportRecordDetail->goodInBuyerMaster->buyerGoodsRemark) && !empty($getExportRecordDetail->goodInBuyerMaster->buyerGoodsRemark) ? array_column(objectToArray($getExportRecordDetail->goodInBuyerMaster->buyerGoodsRemark), 'v_value') : []);
					$goodsRemarks = (!empty($goodsRemarksArray) ? implode(', ', $goodsRemarksArray) : '');
					
					$userCompanyValue = ( isset($getExportRecordDetail->goodInBuyerMaster->companyUserMaster) ? json_decode(json_encode($getExportRecordDetail->goodInBuyerMaster->companyUserMaster),true) : [] );
					$userCompanyName = (!empty($userCompanyValue) ? array_column($userCompanyValue, 'v_company_name') : []);
					$userCompany = ( isset($userCompanyName) ? ( implode(', ', $userCompanyName)) : '');
					
					$buyerNameValue = ( isset($getExportRecordDetail->goodInBuyerMaster->employeeBuyerNameMaster) ? json_decode(json_encode($getExportRecordDetail->goodInBuyerMaster->employeeBuyerNameMaster),true) : []);
					
					$userBuyerNameValue = ( isset($getExportRecordDetail->goodInBuyerMaster->userBuyerNameMaster) ? json_decode(json_encode($getExportRecordDetail->goodInBuyerMaster->userBuyerNameMaster),true) : []);
					
					$supplierCountryDetails = (isset($getExportRecordDetail->goodInBuyerMaster->supplierMaster->supplierDetail) && !empty($getExportRecordDetail->goodInBuyerMaster->supplierMaster->supplierDetail) ? $getExportRecordDetail->goodInBuyerMaster->supplierMaster->supplierDetail : []);
					$supplierCountry = '';
					if (!empty($supplierCountryDetails) && count($supplierCountryDetails) > 0){
						foreach ($supplierCountryDetails as $key => $supplierCountryDetail){
							$supplierCountry .= (isset($supplierCountryDetail->countryMaster->v_country_name) && !empty($supplierCountryDetail->countryMaster->v_country_name) ? ($key > 0 ? ',' : '') . $supplierCountryDetail->countryMaster->v_country_name : '');
						}
					}
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['entry_no.'] = ( isset($getExportRecordDetail->v_goods_in_buyer_detail_no) ? $getExportRecordDetail->v_goods_in_buyer_detail_no : '' );
					$rowExcelData['buyer_company'] =  ( isset($getExportRecordDetail->goodInBuyerMaster->companyMaster->v_company_name) ? $getExportRecordDetail->goodInBuyerMaster->companyMaster->v_company_name :'' );
					$rowExcelData['user_company'] = (isset($userCompany) ? $userCompany : '');
					$rowExcelData['buyer_name'] = '';
					if(!empty($buyerNameValue)){
						foreach($buyerNameValue as $key => $buyerName){
							$rowExcelData['buyer_name'] .= ($key > 0 ? ',' : '') . ( isset($buyerName['v_name']) ?  ($buyerName['v_name']) : '' ) ;
						}
					}
					$rowExcelData['user_buyer_name'] = '';
					if(!empty($userBuyerNameValue)){
						foreach($userBuyerNameValue as $key => $userBuyer){
							$rowExcelData['user_buyer_name'] .= ($key > 0 ? ',' : '') . ( isset($userBuyer['v_name']) ? ($userBuyer['v_name']) : '');
						}
					}
					$rowExcelData['supplier_name'] = ( isset($getExportRecordDetail->supplierMaster->supplierMaster->v_supplier_name) ?  ( $getExportRecordDetail->supplierMaster->supplierMaster->v_supplier_name )  : '' );
					$rowExcelData['supplier_country'] = (isset($getExportRecordDetail->supplierMaster->countryMaster->v_country_name) && !empty($getExportRecordDetail->supplierMaster->countryMaster->v_country_name) ? $getExportRecordDetail->supplierMaster->countryMaster->v_country_name : '');
					/* $rowExcelData['supplier_location'] = ( isset($getExportRecordDetail->supplierMaster->v_supplier_address) ?  ( $getExportRecordDetail->supplierMaster->v_supplier_address )  :'' );
					 */
					$rowExcelData['po_number'] = (isset($getExportRecordDetail->goodInBuyerMaster->v_po_sales_invoice_no) ? $getExportRecordDetail->goodInBuyerMaster->v_po_sales_invoice_no : '' );
					$rowExcelData['vendor_number'] = (isset($getExportRecordDetail->goodInBuyerMaster->v_vendor_number) ? $getExportRecordDetail->goodInBuyerMaster->v_vendor_number : '' );
					$rowExcelData['invoice_number'] = (isset($getExportRecordDetail->goodInBuyerMaster->v_invoice_no) ?  $getExportRecordDetail->goodInBuyerMaster->v_invoice_no  : '');
					$rowExcelData['po_create_user_name'] = (isset($getExportRecordDetail->goodInBuyerMaster->buyerPOCreatedUser->v_name) ?  $getExportRecordDetail->goodInBuyerMaster->buyerPOCreatedUser->v_name  : '');
					$rowExcelData['po_amount'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_po_amount) ? ( $getExportRecordDetail->goodInBuyerMaster->d_po_amount ) : '') ;
					$rowExcelData['po_amount_with_vat'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_po_amount_with_vat) ? ( $getExportRecordDetail->goodInBuyerMaster->d_po_amount_with_vat ) : '');
					$rowExcelData['payment_terms'] = (isset($getExportRecordDetail->goodInBuyerMaster->buyerPaymentTerm->v_value) ? ( $getExportRecordDetail->goodInBuyerMaster->buyerPaymentTerm->v_value ) : '') ;
					$rowExcelData['currency_code'] = (isset($getExportRecordDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code) ? ( $getExportRecordDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code ) : '') ;
					$rowExcelData['prepayment_%'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_prepayment_percentage) ?  ( $getExportRecordDetail->goodInBuyerMaster->d_prepayment_percentage )  : '');
					
					$dimensionaValue = "";
					$dimensionaValueDetails = ( isset($getExportRecordDetail->goodInBuyerMaster->dimensionMaster) ? json_decode(json_encode($getExportRecordDetail->goodInBuyerMaster->dimensionMaster),true) : [] );
					if(!empty($dimensionaValueDetails)){
						foreach($dimensionaValueDetails as $dimensionaValueDetail){
							$dimensionaValue .= ( isset($dimensionaValueDetail['v_dimension_name']) ? $dimensionaValueDetail['v_dimension_name'] . ( isset($dimensionaValueDetail['v_dimension_size']) ? ' ('.$dimensionaValueDetail['v_dimension_size'].')' : '' ) . ', ' : '' );
						}
						$dimensionaValue = rtrim($dimensionaValue , ', ');
					}
					
					$rowExcelData['collection_/_delivery'] = ( ( isset($getExportRecordDetail->goodInBuyerMaster->e_collection_type) ?  ($getExportRecordDetail->goodInBuyerMaster->e_collection_type) :'' ) ) ;
					$rowExcelData['mode_of_transport'] = ( isset($getExportRecordDetail->goodInBuyerMaster->e_mode_of_transport) ? $getExportRecordDetail->goodInBuyerMaster->e_mode_of_transport : '' );
					$rowExcelData['ready_for_collection'] = ( ( ( isset($getExportRecordDetail->goodInBuyerMaster->e_ready_for_collection_status) && ( isset($getExportRecordDetail->goodInBuyerMaster->e_collection_type) ) && ($getExportRecordDetail->goodInBuyerMaster->e_collection_type  == config('constants.COLLECTION')) ) ?  ($getExportRecordDetail->goodInBuyerMaster->e_ready_for_collection_status) :'' ) ) ;
					$rowExcelData['goods_remarks'] = $goodsRemarks;
					$rowExcelData['brand'] = (isset($getExportRecordDetail->goodInBuyerMaster->v_brand) ?  $getExportRecordDetail->goodInBuyerMaster->v_brand  : '');
					$rowExcelData['po_creation_date'] = (isset($getExportRecordDetail->goodInBuyerMaster->dt_po_creation_date) ?  clientDate( $getExportRecordDetail->goodInBuyerMaster->dt_po_creation_date )  : '');
					$rowExcelData['order_date'] = ( isset($getExportRecordDetail->goodInBuyerMaster->dt_order_date) ?  clientDate($getExportRecordDetail->goodInBuyerMaster->dt_order_date) : '');
					$rowExcelData['invoice_date'] = ( isset($getExportRecordDetail->goodInBuyerMaster->dt_invoice_date) ?  clientDate($getExportRecordDetail->goodInBuyerMaster->dt_invoice_date) : '');
					$rowExcelData['payment_date'] = (isset($getExportRecordDetail->goodInBuyerMaster->dt_payment_date) ?  clientDate( $getExportRecordDetail->goodInBuyerMaster->dt_payment_date )  : '');
					$rowExcelData['actual_payment_date'] = (isset($getExportRecordDetail->goodInBuyerMaster->dt_actual_payment_date) ?  clientDate( $getExportRecordDetail->goodInBuyerMaster->dt_actual_payment_date )  : '');
					$rowExcelData['buyer_delivery_date'] = ( isset($getExportRecordDetail->goodInBuyerMaster->dt_delivery_date)  ? clientDate($getExportRecordDetail->goodInBuyerMaster->dt_delivery_date) : ""  );
					$rowExcelData['customs_procedure'] = (isset($getExportRecordDetail->goodInBuyerMaster->e_customs_procedure) ?  $getExportRecordDetail->goodInBuyerMaster->e_customs_procedure  : '');
					$rowExcelData['dangerous_goods'] = (isset($getExportRecordDetail->goodInBuyerMaster->buyerDangerousGoods->v_value) ?  $getExportRecordDetail->goodInBuyerMaster->buyerDangerousGoods->v_value  : '');
					$rowExcelData['pallet_/_box'] = (!empty($getExportRecordDetail->goodInBuyerMaster->e_pallet_box_type) ? $getExportRecordDetail->goodInBuyerMaster->e_pallet_box_type : '');
					$rowExcelData['no.of_pallets_/_box'] = (!empty($getExportRecordDetail->goodInBuyerMaster->i_no_of_pallet_box) ? $getExportRecordDetail->goodInBuyerMaster->i_no_of_pallet_box : '');
					$rowExcelData['delivery_location'] = (isset($getExportRecordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_name) ? $getExportRecordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_name .(isset($getExportRecordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_code) ? ' (' .( $getExportRecordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_code ) .')' :'' ) :'' );
					$rowExcelData['dimension'] = $dimensionaValue;
					$rowExcelData['pallet_type'] = (isset($getExportRecordDetail->goodInBuyerMaster->e_pallet_type) ?  $getExportRecordDetail->goodInBuyerMaster->e_pallet_type  : '');
					$rowExcelData['gross_weight'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_weight) ?  $getExportRecordDetail->goodInBuyerMaster->d_weight  : '');
					$rowExcelData['net_weight'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_net_weight) ?  $getExportRecordDetail->goodInBuyerMaster->d_net_weight  : '');
					$rowExcelData['total_units'] = (isset($getExportRecordDetail->goodInBuyerMaster->i_total_units) && !empty($getExportRecordDetail->goodInBuyerMaster->i_total_units) ?  $getExportRecordDetail->goodInBuyerMaster->i_total_units  : '');
					$rowExcelData['buyer_comments'] = (isset($getExportRecordDetail->goodInBuyerMaster->v_buyer_comments) && !empty($getExportRecordDetail->goodInBuyerMaster->v_buyer_comments) ?  $getExportRecordDetail->goodInBuyerMaster->v_buyer_comments  : '');
					//$rowExcelData['po_amount'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_po_amount) ? decimalAmount( $getExportRecordDetail->goodInBuyerMaster->d_po_amount ) . (isset($getExportRecordDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code) ? ' '. ( $getExportRecordDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code )  :'' ) :'' );
					//$rowExcelData['payment_amount'] = (isset($getExportRecordDetail->goodInBuyerMaster->d_payment_amount) ? decimalAmount( $getExportRecordDetail->goodInBuyerMaster->d_payment_amount ) . (isset($getExportRecordDetail->goodInBuyerMaster->paymentCurrencyMaster->v_currency_code) ? ' '.   ( $getExportRecordDetail->goodInBuyerMaster->paymentCurrencyMaster->v_currency_code ) :'' )  :'' );
					
					$finalExportData[] = $rowExcelData;
				}
			}
			
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.good-in-buyer')]);
		
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.good-in-buyer')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
		
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
		
			return Response::json($response);
			die;
		}
		
		$paginationData = [];
		
		$whereData['page'] = $page;
		
		$data['recordDetails'] = $this->crudModel->getGoodsInBuyerDetails( $whereData, $likeData );
		
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
		
		$totalRecords = count($data['recordDetails']);
		
		if(isset($totalRecords)){
			$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
		}
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'good-in-buyer/good-in-buyer-list' )->with ( $data )->render();
		echo $html;die;
	}
	public function create() {
		//return redirect ( config('constants.404_PAGE') );
		
		if(checkPermission(config('permission_constants.ADD_GOODS_IN_BUYER')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-buyer');
		
		$data['companyRecordDetails'] = CompanyMasterModel::where('t_is_active',1)->orderBy('v_company_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('t_is_active',1)->where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['userBuyerRecordDetails'] = Login::where('t_is_active' , 1)->where('v_role' , config('constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['supplierRecordDetails'] = SupplierMasterModel::where('t_is_active',1)->orderBy('v_supplier_name', 'ASC')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['dimensionRecordDetails'] = DimensionMasterModel::where('t_is_active',1)->orderBy('v_dimension_name', 'ASC')->get();
		$data['poCreateUserDetails'] = Login::where('t_is_active' , 1)->where('v_role',config('constants.ROLE_USER'))->orderBy('v_name' , 'asc')->get();
		$data['paymentTermsDetails'] = LookupMaster::where('t_is_active',1)->where('v_module_name',config('constants.PAYMENT_TERMS_LOOKUP'))->orderBy('v_value')->get();
		$data['dangerousGoodsDetails'] = LookupMaster::where('t_is_active',1)->where('v_module_name',config('constants.DANGEROUS_GOODS_LOOKUP'))->orderBy('v_value')->get();
		$data['goodsRemarksDetails'] = LookupMaster::where('t_is_active',1)->where('v_module_name',config('constants.GOODS_REMARK_LOOKUP'))->orderBy('v_value')->get();
		//$data['dimensionPalletRecordDetails'] = DimensionMasterModel::where('t_is_active',1)->where('e_dimension',config ( 'constants.PALLET'))->orderBy('v_dimension_name', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('t_is_active',1)->where('e_document_type' , config ( 'constants.BUYER') )->orderBy('v_document_type_name', 'ASC')->get();
		//$data['paymentStatusInfo'] = paymentStatus();
		$data['collectionDeliveryInfo'] = collectionDeliveryInfo();
		$data['deliveryTypeInfo'] = deliveryTypeInfo();
		//$data['customProcedureInfo'] = customProcedureInfo();
		$data['customsProcedureInfo'] = customsProcedureDropdown();
		
		//$data['dangerousGoodsInfo'] = dangerousGoodsInfo();
		$data['palletsTypeInfo'] = palletsTypeInfo();
		$data['palletBoxInfo'] = typeInfo();
		//$data['weightUnitInfo'] = weightUnitInfo();
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['readyForCollectionInfo'] = dangerousGoodsInfo();
		
		$warehouseWhere = [];
		$userLogin =  session()->get('user_id');
		$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
		
		if(count($userLoginDetails) > 0){
			$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :'');
			if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
				$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :0 );
				$warehouseWhere['i_id'] = $warehouseIds;
			}
		}
		
		$data['warehouseDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$viewForm = false;
		$data['viewForm'] = $viewForm;
		return view ( $this->folderName . 'add-good-in-buyer' )->with ( $data );
	}
	public function getSupplierLocation(Request $request){
		
		$data = $whereData = [];
		if(!empty($request->input('supplier_record_id'))){
			$whereData ['i_supplier_id'] =  (int)Wild_tiger::decode($request->input('supplier_record_id'));
			$whereData['e_record_status'] = config('constants.COLLECTION');
			//$whereData['t_is_active'] = 1;
			$getSupplierLocationDetails = SupplierDetailModel::with(['supplierMaster'])->whereHas('supplierMaster', function($query)use ($whereData)
			{
				$query->where($whereData );
					
			})->get();
			
			$html = '<option value="">'.trans("messages.select").'</option>';
			if(!empty($getSupplierLocationDetails)){
				foreach ($getSupplierLocationDetails as $getSupplierLocationDetail){
					$encodeRecordId  = Wild_tiger::encode($getSupplierLocationDetail->i_id);
					$supplierAddres = $getSupplierLocationDetail->v_supplier_address;
					$html .= '<option value="'.$encodeRecordId.'">'.$getSupplierLocationDetail->supplierMaster->v_supplier_name. ' (' .( $supplierAddres ).')'.'</option>';
				}
			}
			echo $html;die;
		}
	}
	public function add(Request $request){
		
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_GOODS_IN_BUYER')) != true ){
				return redirect('access-denied');
			}
		} else {
			return redirect ( config('constants.404_PAGE') );
			if(checkPermission(config('permission_constants.ADD_GOODS_IN_BUYER')) != true ){
				return redirect('access-denied');
			}
		}
		//$goodInBuyerPaymentStatus = (!empty($request->input('payment_status')) ? ($request->input('payment_status')) : "" );
		$goodInBuyerCollection = (!empty($request->input('collection_delivery')) ? ($request->input('collection_delivery')) : "" );
		//$goodInBuyerPickupReferenceStatus = (!empty($request->input('pickup_reference')) ? ($request->input('pickup_reference')) : null );
		
		$formValidation = [];
		$formValidation['buyer_company'] = 'required';
		//$formValidation['buyer_company[]'] = 'required';
		$formValidation['buyer_name'] = 'required';
		$formValidation['user_company'] = 'required';
		$formValidation['user_buyer_name'] = 'required';
		$formValidation['supplier_name'] = 'required';
		//$formValidation['supplier_location[]'] = 'required';
		$formValidation['order_date'] = 'required';
		$formValidation['po_no_sales_invoice_no'] = ['required' , new UniquePoSalesInvoiceNumber($recordId)];
		$formValidation['po_no_sales_invoice_amount'] = 'required';
		$formValidation['po_amount_with_vat'] = 'required';
		$formValidation['vendor_number'] = 'required';
		$formValidation['brand'] = 'required';
		//$formValidation['payment_status'] = 'required';
		
		$formValidation['payment_date'] = 'required';
		$formValidation['po_create_user_name'] = 'required';
		
		/* if($goodInBuyerPaymentStatus == config('constants.PAID_PAYMENT_STATUS') || $goodInBuyerPaymentStatus == config('constants.PARTIAL_PAID_PAYMENT_STATUS')){
			$formValidation['amount'] = 'required';
			$formValidation['sales_invoice_amount'] = 'required';
		} */
		$formValidation['payment_terms'] = 'required';
		$formValidation['customs_procedure'] = 'required';
		$formValidation['collection_delivery'] = 'required';
		$formValidation['delivery_date'] = 'required';
		$formValidation['delivery_location'] = 'required';
		/* if($goodInBuyerCollection == config('constants.DELIVERY')){
			$formValidation['delivery_type'] = 'required';
			$formValidation['booking_ref_no'] = 'required';
			$formValidation['delivery_remarks'] = 'required';
		} */
		
		/* $formValidation['custom_procedure_export'] = 'required';
		$formValidation['custom_procedure_import'] = 'required'; */
		$formValidation['dangerous_goods'] = 'required';
		$formValidation['goods_remarks'] = 'required';
		$formValidation['no_of_pallets_boxes'] = 'required';
		$formValidation['pallets_boxes_type'] = 'required';
		//$formValidation['boxes_dimension'] = 'required';
		//$formValidation['weight_unit'] = 'required';
		//$formValidation['file.*'] = 'mimes:docx,doc,pdf,jpg,jpeg,png,xls,xlsx';
		//$formValidation['edit_file.*'] = 'mimes:docx,doc,pdf,jpg,jpeg,png,xls,xlsx';
		//$formValidation['file.*'] = 'mimes:jpg,jpeg,png';
		//$formValidation['edit_file.*'] = 'mimes:jpg,jpeg,png';
		$formValidation['po_no_amount'] = 'required';
		$formValidation['total_unit'] = 'required';
		//$formValidation['net_weight_unit'] = 'required';
		if($goodInBuyerCollection == config('constants.COLLECTION')){
			/* $formValidation['pickup_reference'] = 'required';
			if($goodInBuyerPickupReferenceStatus == config('constants.SELECTION_YES')){
				$formValidation['reference'] = 'required';
			} */
			$formValidation['ready_for_collection'] = 'required';
		}
		$validator = Validator::make ( $request->all (), $formValidation , [
				'buyer_company.required' => __ ( 'messages.require-buyer-company' ),
				'buyer_name.required' => __ ( 'messages.require-buyer-name' ),
				'user_company.required' => __ ( 'messages.require-user-company' ),
				'user_buyer_name.required' => __ ( 'messages.require-user-buyer-name' ),
				'supplier_name.required' => __ ( 'messages.require-supplier-name' ),
				'order_date.required' => __ ( 'messages.require-order-date' ),
				'po_no_sales_invoice_no.required' => __ ( 'messages.require-po-no-sales-invoice-no' ),
				'po_no_sales_invoice_amount.required' => __ ( 'messages.require-currency' ),
				'po_amount_with_vat.required' => __ ( 'messages.require-po-amount-with-vat' ),
				'vendor_number.required' => __ ( 'messages.require-vendor-number' ),
				'brand.required' => __ ( 'messages.require-brand' ),
				//'payment_status.required' => __ ( 'messages.require-payment-status' ),
				'payment_date.required' => __ ( 'messages.require-payment-date' ),
				'po_create_user_name.required' => __ ( 'messages.require-po-create-user-name' ),
				//'amount.required' => __ ( 'messages.require-currency' ),
				'payment_terms.required' => __ ( 'messages.require-payment-terms' ),
				'customs_procedure.required' => __ ( 'messages.require-customs-procedure' ),
				'collection_delivery.required' => __ ( 'messages.require-collection-delivery' ),
				'ready_for_collection.required' => __ ( 'messages.require-ready-for-collection' ),
				//'delivery_type.required' => __ ( 'messages.require-delivery-type' ),
				//'booking_ref_no.required' => __ ( 'messages.require-booking-ref-no' ),
				'delivery_location.required' => __ ( 'messages.require-delivery-location' ),
				'delivery_date.required' => __ ( 'messages.require-delivery-date' ),
				//'delivery_remarks.required' => __ ( 'messages.require-delivery-remarks' ),
				/* 'custom_procedure_export.required' => __ ( 'messages.require-custom-procedure-export' ),
				'custom_procedure_import.required' => __ ( 'messages.require-custom-procedure-import' ), */
				'dangerous_goods.required' => __ ( 'messages.require-dangerous-goods' ),
				'goods_remarks.required' => __ ( 'messages.require-goods-remarks' ),
				'no_of_pallets_boxes.required' => __ ( 'messages.require-no-of-pallet-box' ),
				'pallets_boxes_type.required' => __ ( 'messages.require-pallet-box' ),
				//'weight_unit.required' => __ ( 'messages.require-weight-unit' ),
				'po_no_amount.required' => __ ( 'messages.require-amount' ),
				//'sales_invoice_amount.required' => __ ( 'messages.require-amount' ),
				'total_unit.required' => __ ( 'messages.require-total-units' ),
				//'net_weight_unit.required' => __ ( 'messages.require-weight-unit' ),
				//'pickup_reference.required' => __ ( 'messages.require-pickup-reference' ),
				//'reference.required' => __ ( 'messages.require-reference' ),
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=>trans('messages.good-in-buyer')]);
		$errorMessages = trans('messages.error-create',['module'=>trans('messages.good-in-buyer')]);
			
		$recordData = $userCompanyIdRecord = $supplierIdRecord = $buyerNameIdRecord = $boxesDimensionIdRecord = $palletsDimensionIdRecord = [];
		DB::beginTransaction();
		
		try{
			$userCompanyIds = (!empty($request->input('user_company')) ? ($request->input('user_company')) : "" );
			if(!empty($userCompanyIds)){
				foreach ($userCompanyIds as $userCompanyId){
					$userCompanyIdRecord[] = (int)Wild_tiger::decode($userCompanyId);
				}
			}
			$supplierIds = (!empty($request->input('supplier_location')) ? ($request->input('supplier_location')) : "" );
			
			if(!empty($supplierIds)){
				foreach ($supplierIds as $supplierId){
					$supplierIdRecord[] = (int)Wild_tiger::decode($supplierId);
				}
			}
			
			$buyerNameIds = (!empty($request->input('buyer_name')) ? ($request->input('buyer_name')) : [] );
			if(!empty($buyerNameIds)){
				foreach ($buyerNameIds as $buyerNameId){
					$buyerNameIdRecord[] = (int)Wild_tiger::decode($buyerNameId);
				}
			}
			
			$userBuyerIds = (!empty($request->input('user_buyer_name')) ? ($request->input('user_buyer_name')) : []);
			$userBuyerIdRecord = [];
			if(!empty($userBuyerIds)){
				foreach ($userBuyerIds as $userBuyerId){
					$userBuyerIdRecord[] = (int)Wild_tiger::decode($userBuyerId);
				}
			}
			
			$goodsRemarkIds = (!empty($request->input('goods_remarks')) ? ($request->input('goods_remarks')) : []);
			
			$goodsRemarkIdRecord = [];
			if(!empty($goodsRemarkIds)){
				foreach ($goodsRemarkIds as $goodsRemarkId){
					$goodsRemarkIdRecord[] = (int)Wild_tiger::decode($goodsRemarkId);
				}
			}
			$dimensionIds = (!empty($request->input('pallet_box_dimension')) ? ($request->input('pallet_box_dimension')) : []);
			$dimensionIdRecord = [];
			if(!empty($dimensionIds)){
				foreach ($dimensionIds as $dimensionId){
					$dimensionIdRecord[] = (int)Wild_tiger::decode($dimensionId);
				}
			}
			
			/* $boxesDimensionIds = (!empty($request->input('boxes_dimension')) ? ($request->input('boxes_dimension')) : "" );
			if(!empty($boxesDimensionIds)){
				foreach ($boxesDimensionIds as $boxesDimensionId){
					$boxesDimensionIdRecord[] = (int)Wild_tiger::decode($boxesDimensionId);
				}
			}
			$palletsDimensionIds = (!empty($request->input('pallets_dimension')) ? ($request->input('pallets_dimension')) : "" );
			if(!empty($palletsDimensionIds)){
				foreach ($palletsDimensionIds as $palletsDimensionId){
					$palletsDimensionIdRecord[] = (int)Wild_tiger::decode($palletsDimensionId);
				}
			} */
			
			/* $goodIbuyerGenerateNo = config('constants.GOOD_IN_BUYER_GIB').'-'. config('constants.GOOD_IN_BUYER_NUMBER').'-'.config('constants.TODAY_DATE');
			$goodInBuyerMasterRecordDetails = $this->crudModel->selectData(config('constants.GOODS_IN_BUYER_MASTER_TABLE') ,['i_id']);
			if(!empty($goodInBuyerMasterRecordDetails)){
				$goodInBuyerMasterRecordCount = count(objectToArray($goodInBuyerMasterRecordDetails));
				$count = ( ( (!empty($goodInBuyerMasterRecordCount)) && ( $goodInBuyerMasterRecordCount > 0 ) ) ? ( $goodInBuyerMasterRecordCount + 1  ) : 1 );
				$generateNumber = threeNumberSeries($count);
				$goodIbuyerGenerateNo = config('constants.GOOD_IN_BUYER_GIB').'-'. $generateNumber.'-'.config('constants.TODAY_DATE');
					
			} */
			
			//$recordData['v_goods_in_buyer_master_no'] = $goodIbuyerGenerateNo;
			$recordData['i_buyer_company_id'] = (!empty($request->input('buyer_company')) ? (int)Wild_tiger::decode($request->input('buyer_company')) : 0 );
			$recordData['v_user_company_ids'] = (!empty($userCompanyIdRecord) ? implode(',', $userCompanyIdRecord) : '');
			$recordData['v_buyer_employee_ids'] = (!empty($buyerNameIdRecord) ? implode(',', $buyerNameIdRecord) : '');
			$recordData['v_user_buyer_ids'] = (!empty($userBuyerIdRecord) ? implode(',', $userBuyerIdRecord) : '');
			$recordData['i_main_supplier_id'] = (!empty($request->input('supplier_name')) ? (int)Wild_tiger::decode($request->input('supplier_name')) : 0 );
			$recordData['i_po_create_user_id'] = (!empty($request->input('po_create_user_name')) ? (int)Wild_tiger::decode($request->input('po_create_user_name')) : 0 );
			$recordData['dt_po_creation_date'] = (!empty($request->input('po_creation_date')) ? dbDate($request->input('po_creation_date')) : null);
			$recordData['v_supplier_ids'] = implode(',', $supplierIdRecord);
			$recordData['dt_order_date'] = (!empty($request->input('order_date')) ? dbDate($request->input('order_date')) : "" );
			$recordData['dt_invoice_date'] = (!empty($request->input('invoice_date')) ? dbDate($request->input('invoice_date')) : null );
			$recordData['v_po_sales_invoice_no'] = (!empty($request->input('po_no_sales_invoice_no')) ? trim($request->input('po_no_sales_invoice_no')) : "" );
			$recordData['v_invoice_no'] = (!empty($request->input('invoice_no')) ? trim($request->input('invoice_no')) : "");
			$recordData['v_vendor_number'] = (!empty($request->input('vendor_number')) ? trim($request->input('vendor_number')) : "" );
			$recordData['i_po_currency_id'] = (!empty($request->input('po_no_sales_invoice_amount')) ? (int)Wild_tiger::decode($request->input('po_no_sales_invoice_amount')) : 0 );
			$recordData['d_po_amount'] = (!empty($request->input('po_no_amount')) ? $request->input('po_no_amount') : "" );
			$recordData['d_po_amount_with_vat'] = (!empty($request->input('po_amount_with_vat')) ? $request->input('po_amount_with_vat') : "" );
			$recordData['v_brand'] = (!empty($request->input('brand')) ? trim($request->input('brand')) : '');
			//$recordData['e_payment_status'] = $goodInBuyerPaymentStatus;
			//if($goodInBuyerPaymentStatus == config('constants.PAID_PAYMENT_STATUS') || $goodInBuyerPaymentStatus == config('constants.PARTIAL_PAID_PAYMENT_STATUS')){
				$recordData['dt_payment_date'] = (!empty($request->input('payment_date')) ? dbDate($request->input('payment_date')) : null );
				$recordData['dt_actual_payment_date'] = (!empty($request->input('actual_payment_date')) ? dbDate($request->input('actual_payment_date')) : null );
				$recordData['d_prepayment_percentage'] = ( !empty($request->input('prepayment_percentage')) ? $request->input('prepayment_percentage') : null );
				//$recordData['i_payment_currency_id'] = (!empty($request->input('amount')) ? (int)Wild_tiger::decode($request->input('amount')) : null );
				//$recordData['d_payment_amount'] = (!empty($request->input('sales_invoice_amount')) ? trim($request->input('sales_invoice_amount')) : null);
			//}
			/* else {
				$recordData['dt_payment_date'] = null;
				$recordData['i_payment_currency_id'] = null;
				$recordData['d_payment_amount'] = null;
			} */
			
			$recordData['i_payment_terms_id'] = (!empty($request->input('payment_terms')) ? (int)Wild_tiger::decode($request->input('payment_terms')) : 0);
			$recordData['e_customs_procedure'] = (!empty($request->input('customs_procedure')) ? $request->input('customs_procedure') : '');
			$recordData['i_dangerous_goods_id'] = (!empty($request->input('dangerous_goods')) ? (int)Wild_tiger::decode($request->input('dangerous_goods')) : 0);
			$recordData['v_goods_remark_ids'] = implode(',', $goodsRemarkIdRecord);
			$recordData['i_no_of_pallet_box'] = (!empty($request->input('no_of_pallets_boxes')) ? trim($request->input('no_of_pallets_boxes')) : 0);
			$recordData['e_pallet_box_type'] = (!empty($request->input('pallets_boxes_type')) ? $request->input('pallets_boxes_type') : '');
			$recordData['e_collection_type'] =  $goodInBuyerCollection;
			$recordData['e_mode_of_transport'] = (!empty($request->input('mode_of_transport')) ? $request->input('mode_of_transport') : null);
			$recordData['i_delivery_location_id'] = (!empty($request->input('delivery_location')) ? (int)Wild_tiger::decode($request->input('delivery_location')) : 0 );
			/* if($goodInBuyerCollection == config('constants.DELIVERY')){
				$recordData['e_delivery_type'] = (!empty($request->input('delivery_type')) ? $request->input('delivery_type') : null );
				$recordData['v_booking_ref_no'] = (!empty($request->input('booking_ref_no')) ? $request->input('booking_ref_no') : null );
				$recordData['v_collection_reference_no'] = (!empty($request->input('collection_reference_no')) ? $request->input('collection_reference_no') : null );
				
				$recordData['v_delivery_remarks'] = (!empty($request->input('delivery_remarks')) ? trim($request->input('delivery_remarks')) : "" );
				$recordData['v_reference'] = null;
			} */
			
			$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null);
			
			$readyForCollection = (!empty($request->input('ready_for_collection')) ? $request->input('ready_for_collection') : null);
			if($goodInBuyerCollection == config('constants.COLLECTION')){
				//$recordData['i_delivery_location_id'] = (!empty($request->input('collection_location')) ? (int)Wild_tiger::decode($request->input('collection_location')) : 0 );
				//$recordData['e_delivery_type'] = null;
				//$recordData['v_booking_ref_no'] = null;
				//$recordData['v_collection_reference_no'] = "";
				//$recordData['dt_delivery_date'] = null;
				//$recordData['v_delivery_remarks'] = "";
				$recordData['e_ready_for_collection_status'] = $readyForCollection;
				/* $recordData['e_pickup_reference'] = $goodInBuyerPickupReferenceStatus;
				if($goodInBuyerPickupReferenceStatus == config('constants.SELECTION_YES')){
					$recordData['v_reference'] = (!empty($request->input('reference')) ? trim($request->input('reference')) : null);
				} else {
					$recordData['v_reference'] = null;
				} */
			}
			/* $recordData['e_customer_procedure_export'] = (!empty($request->input('custom_procedure_export')) ? $request->input('custom_procedure_export') : "" );
			$recordData['e_customer_procedure_import'] = (!empty($request->input('custom_procedure_import')) ? $request->input('custom_procedure_import') : "" ); */
			//$recordData['e_dangerous_goods'] = (!empty($request->input('dangerous_goods')) ? $request->input('dangerous_goods') : "" );
			//$recordData['v_goods_remarks'] = (!empty($request->input('goods_remarks')) ? trim($request->input('goods_remarks')) : null );
			//$recordData['i_no_boxes'] = (!empty($request->input('no_of_boxes')) ? $request->input('no_of_boxes') : null );
			$recordData['v_dimension_ids'] = implode(',', $dimensionIdRecord);
			//$recordData['v_box_dimension_ids'] = implode(',', $boxesDimensionIdRecord);
			//$recordData['i_no_palltes'] = (!empty($request->input('no_of_pallets')) ? $request->input('no_of_pallets') : null );
			//$recordData['v_pallet_dimension_ids'] = implode(',', $palletsDimensionIdRecord);
			$recordData['e_pallet_type'] = (!empty($request->input('pallets_type')) ? $request->input('pallets_type') : null );
			$recordData['d_weight'] = (!empty($request->input('weight')) ? trim($request->input('weight')) : null );
			//$recordData['e_weight_unit'] = (!empty($request->input('weight_unit')) ? $request->input('weight_unit') : '' );
			
			$recordData['d_net_weight'] = (!empty($request->input('net_weight')) ? trim($request->input('net_weight')) : null );
			$recordData['i_total_units'] = (!empty($request->input('total_unit')) ? $request->input('total_unit') : '' );
			$recordData['v_buyer_comments'] = (!empty($request->input('buyer_comments')) ? $request->input('buyer_comments') : null );
			//$recordData['e_net_weight_unit'] = (!empty($request->input('net_weight_unit')) ? $request->input('net_weight_unit') : '' );
			
			//echo '<pre>';print_r($recordData);die;	
			$goodInBuyerCount = (!empty($request->input('good_in_buyer_count')) ? (int)($request->input('good_in_buyer_count')) : 1 );
			$packingListSlip = true;
			$invoiceSlip = true;
			
			if($recordId > 0){
				$successMessage =  trans('messages.success-update',['module'=>trans('messages.good-in-buyer')]);
				$errorMessages = trans('messages.error-update',['module'=>trans('messages.good-in-buyer')]);
				$whereData = [];
				$whereData['buyerMasterId'] = $recordId;
				
				$goodsInBuyerDetails = $this->crudModel->getGoodsInBuyerDetails($whereData);
				
				$getMasterGoodsInNo = ( isset($goodsInBuyerDetails[0]->v_goods_in_buyer_detail_no) ? $goodsInBuyerDetails[0]->v_goods_in_buyer_detail_no : "" );
				$getMasterGoodsInArray = (!empty($getMasterGoodsInNo) ? explode("-" , $getMasterGoodsInNo ) : [] );
				
				if(!empty($goodsInBuyerDetails) && count($goodsInBuyerDetails) > 0){
					foreach ($goodsInBuyerDetails as $goodsInBuyerDetail){
						$useStatus = ($goodsInBuyerDetail->t_in_use); 
						if(in_array($goodsInBuyerDetail->i_goods_in_buyer_supplier_id, $supplierIdRecord)){
							$goodInBuyerDetail['i_goods_in_buyer_supplier_id'] = $goodsInBuyerDetail->i_goods_in_buyer_supplier_id ;
							//$goodInBuyerDetail['e_buyer_record_status'] = (!empty($request->input('delivery_type')) ? $request->input('delivery_type') : null);
							
							if($goodInBuyerCollection == config('constants.COLLECTION')){
								$goodInBuyerDetail['e_buyer_record_status'] = config('constants.FULL_DELIVERY_TYPE');
							}
							
							if( count($goodsInBuyerDetails) == 1 && (count($supplierIdRecord)  > 1 ) ){
								$goodInBuyerDetailNoarrayCount = count(explode("-" , $goodsInBuyerDetail->v_goods_in_buyer_detail_no ) );
								$goodInBuyerDetail['v_goods_in_buyer_detail_no'] = ( $goodInBuyerDetailNoarrayCount == 3 ) ? $goodsInBuyerDetail->v_goods_in_buyer_detail_no . '-1' : $goodsInBuyerDetail->v_goods_in_buyer_detail_no ;
							}
							$goodInBuyerDetailUpdate = $this->crudModel->updateTableData( config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , $goodInBuyerDetail , [ 'i_id' => $goodsInBuyerDetail->i_id] );
							
							unset($supplierIdRecord[array_search($goodsInBuyerDetail->i_goods_in_buyer_supplier_id, $supplierIdRecord)]);
						} else {
							/* if($useStatus == 1){
								DB::rollback();
								Wild_tiger::setFlashMessage ( 'danger',  $errorMessages);
								return redirect ( $this->redirectUrl );
							} else { */
								$goodInBuyerDetailRecordInfo['t_is_active'] = 0;
								$goodInBuyerDetailRecordInfo['t_is_deleted'] = 1;
								$this->crudModel->deleteTableData ( config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , $goodInBuyerDetailRecordInfo, ['i_id' => $goodsInBuyerDetail->i_id] );
							/* } */
						}
					}
					
					if(!empty($supplierIdRecord)){
						//$rowValue = 0;
						foreach ($supplierIdRecord as $supplierId){
							$goodInBuyerDetailRecord = $this->crudModel->selectData(config('constants.GOODS_IN_BUYER_DETAIL_TABLE') ,['i_id'] ,[ 'i_goods_in_buyer_master_id' => $recordId ]);
							$goodInBuyerDetailRecordCount = count($goodInBuyerDetailRecord);
							$count = ( ( (!empty($goodInBuyerDetailRecordCount)) && ( $goodInBuyerDetailRecordCount > 0 ) ) ? ( $goodInBuyerDetailRecordCount + 1  ) : 1 );
							$rowValue = $count;
							$goodInBuyerDetail['i_goods_in_buyer_master_id'] = $recordId;
							$goodInBuyerDetail['i_goods_in_buyer_supplier_id'] = $supplierId;
							$goodInBuyerDetail['v_goods_in_buyer_detail_no'] = config('constants.GOOD_IN_BUYER_GIB').'-'. (isset($getMasterGoodsInArray[1]) ? $getMasterGoodsInArray[1] :  config('constants.GOOD_IN_BUYER_NUMBER') )  .'-'.(isset($getMasterGoodsInArray[2]) ? $getMasterGoodsInArray[2] : $this->todayDate ).'-'.$rowValue++;
							/* if($goodInBuyerCollection == config('constants.DELIVERY')){
								$goodInBuyerDetail['e_buyer_record_status'] = (!empty($request->input('delivery_type')) ? $request->input('delivery_type') : null );
							} */
							$this->crudModel->insertTableData(config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , $goodInBuyerDetail);
								
						}
					}
					
					$goodsInBuyerDocumentDetails = (!empty($goodsInBuyerDetails) ? $goodsInBuyerDetails[0] : []);
					
					if(!empty($goodsInBuyerDocumentDetails->goodInBuyerMaster->goodInBuyerDocument)){
						foreach ($goodsInBuyerDocumentDetails->goodInBuyerMaster->goodInBuyerDocument as $goodsInBuyerDetail){
							$goodsInBuyerDocumentId = $goodsInBuyerDetail->i_id;
					
							if(!empty($request->input('edit_type_'.$goodsInBuyerDocumentId))){
									
								$goodInBuyerDocument = [];
								$documentTypeId =  (!empty($request->input('edit_type_'.$goodsInBuyerDocumentId)) ? $request->input('edit_type_'.$goodsInBuyerDocumentId) : '');
								$decodeDocumentTypeId = (int)Wild_tiger::decode($documentTypeId);
								//echo '<pre>';print_r($decodeDocumentTypeId);die;
								$goodInBuyerDocument['i_document_type_id'] = $decodeDocumentTypeId;
								$goodInBuyerDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$goodsInBuyerDocumentId)) ? trim($request->input('edit_remarks_'.$goodsInBuyerDocumentId)) : '');
									
								if($request->hasFile('edit_file_'.$goodsInBuyerDocumentId)){
									$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$goodsInBuyerDocumentId,'image_doc_pdf_xls');
									if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
										$goodInBuyerDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
									} else {
										DB::rollback();
										Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
										return redirect ( $this->redirectUrl );
									}
								} else {
									$removeFiles = (!empty($request->input('remove_document_'.$goodsInBuyerDocumentId)) ? explode("," , $request->input('remove_document_'.$goodsInBuyerDocumentId) ) : []  );
									$previousUploadFiles = (!empty($goodsInBuyerDetail->v_document_file_path) ? json_decode($goodsInBuyerDetail->v_document_file_path,true) : [] );
									$newFilesArray = [];
									if(!empty($previousUploadFiles)){
										foreach($previousUploadFiles as $previousUploadFile){
											if(!in_array(basename($previousUploadFile) , $removeFiles )){
												$newFilesArray[] = $previousUploadFile;
											}
										}
									}
									$goodInBuyerDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
								}
								if(($goodInBuyerCollection == config('constants.COLLECTION')) && ( $readyForCollection == config('constants.SELECTION_YES'))){
									if($decodeDocumentTypeId == config('constants.DOCUMENT_TYPE_PACKING_LIST_ID')){
										$packingListSlip = false;
										if(!empty($goodInBuyerDocument['v_document_file_path'])){
											$packingListSlip = true;
										}
									}
									if($decodeDocumentTypeId == config('constants.DOCUMENT_TYPE_INVOICE_ID')){
										$invoiceSlip = false;
										if(!empty($goodInBuyerDocument['v_document_file_path'])){
											$invoiceSlip = true;
										}
									}
								}
								$goodInBuyerDocumentUpdate = $this->crudModel->updateTableData( config('constants.GOODS_IN_BUYER_DOCUMENT_TABLE') , $goodInBuyerDocument , [ 'i_id' => $goodsInBuyerDocumentId] );
							} else {
								$goodInBuyerDocumentRecordData['t_is_active'] = 0;
								$goodInBuyerDocumentRecordData['t_is_deleted'] = 1;
								$this->crudModel->deleteTableData ( config('constants.GOODS_IN_BUYER_DOCUMENT_TABLE') , $goodInBuyerDocumentRecordData, ['i_id' => $goodsInBuyerDocumentId] );
							}
						}
							
					}
					
					$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
					$insertRecord = $recordId;
					
				}else {
					
					DB::rollBack();
					
					Wild_tiger::setFlashMessage ( 'danger', $errorMessages );
					
					return redirect ( $this->redirectUrl );
				}
				
			} else {
				$goodInBuyerDetailRecord = $this->crudModel->selectData(config('constants.GOODS_IN_BUYER_MASTER_TABLE') ,['i_id']);
				
				$goodsInBuyerMasterRecordCount = 1;
				
				if(!empty($goodInBuyerDetailRecord)){
					$goodInBuyerDetailRecordCount = count(objectToArray($goodInBuyerDetailRecord));
					$count = ( ( (!empty($goodInBuyerDetailRecordCount)) && ( $goodInBuyerDetailRecordCount > 0 ) ) ? ( $goodInBuyerDetailRecordCount + 1  ) : 1 );
					$goodsInBuyerMasterRecordCount = $count;
				}
				
				$goodsInBuyerMasterRecordCount = threeNumberSeries($goodsInBuyerMasterRecordCount);
				
				$goodInBuyerDetailGenerateNo = config('constants.GOOD_IN_BUYER_GIB').'-'. $goodsInBuyerMasterRecordCount .'-'.$this->todayDate;
				
				$recordData['v_goods_in_buyer_master_no'] = $goodInBuyerDetailGenerateNo;
				
				//$recordData['v_supplier_ids'] = implode(',', $supplierIdRecord);
				
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				if( $insertRecord > 0 ){
					$result = true;
				}
				
				$goodInBuyerDetail = [];
				
				if(!empty($supplierIds)){
					$rowValue = 1;
					foreach ($supplierIds as $supplierRecordKey => $supplierId){
						$goodInBuyerDetail['i_goods_in_buyer_master_id'] = $insertRecord;
						$goodInBuyerDetail['i_goods_in_buyer_supplier_id'] = (int)Wild_tiger::decode($supplierId);
						
						if(count($supplierIds) == 1 ){
							$goodInBuyerDetail['v_goods_in_buyer_detail_no'] = $goodInBuyerDetailGenerateNo;
						} else {
							$goodInBuyerDetail['v_goods_in_buyer_detail_no'] = $goodInBuyerDetailGenerateNo.'-'.$rowValue++;
						}
						/* if($goodInBuyerCollection == config('constants.DELIVERY')){
							$goodInBuyerDetail['e_buyer_record_status'] = (!empty($request->input('delivery_type')) ? $request->input('delivery_type') : '' );
						} */		
						$insertGoodInBuyerDetail = $this->crudModel->insertTableData(config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , $goodInBuyerDetail);
						$rowValue;
					}
				}
			}
			
			for ($i = 1; $i <= $goodInBuyerCount; $i++){
				$documentTypeId = (!empty($request->input('type_'.$i)) ? (int)Wild_tiger::decode($request->input('type_'.$i)) :0);
				$rowData = [];
				$rowData['i_goods_in_buyer_master_id'] = $insertRecord;
				$rowData['i_document_type_id'] = $documentTypeId;
				$rowData['v_document_remark'] = (!empty($request->input('remarks_'.$i)) ? trim($request->input('remarks_'.$i)) : null);
				$rowData['v_document_file_path'] = null;
			
				if($request->hasFile('file_'.$i)){
					$uploadFile = $this->uploadMultipleFile($request, 'file_'.$i,'image_doc_pdf_xls');
					if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
						$rowData['v_document_file_path'] = json_encode($uploadFile['filePath']);
					} else {
						DB::rollback();
						Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
						return redirect ( $this->redirectUrl );
					}
				}
				if(($goodInBuyerCollection == config('constants.COLLECTION')) && ( $readyForCollection == config('constants.SELECTION_YES')) && ($packingListSlip != true) ){
					$packingListSlip = false;
					if($documentTypeId == config('constants.DOCUMENT_TYPE_PACKING_LIST_ID')){
						if(!empty($rowData['v_document_file_path'])){
							$packingListSlip = true;
						}
					} 
				}
				if(($goodInBuyerCollection == config('constants.COLLECTION')) && ( $readyForCollection == config('constants.SELECTION_YES')) && ($invoiceSlip != true) ){
					$invoiceSlip = false;
					if($documentTypeId == config('constants.DOCUMENT_TYPE_INVOICE_ID')){
						if(!empty($rowData['v_document_file_path'])){
							$invoiceSlip = true;
						}
					}
				}
				if( (!empty($rowData['v_document_file_path'])) && (!empty($rowData ['i_document_type_id'])) ){
					$insertGoodInBuyerDocumentDetail = $this->crudModel->insertTableData( config('constants.GOODS_IN_BUYER_DOCUMENT_TABLE') , $rowData);
				}
				
			}
			
			if($packingListSlip != true){
				DB::rollback();
				Wild_tiger::setFlashMessage ( 'danger', trans('messages.require-document-packing-list') );
				return redirect ( $this->redirectUrl );
			}
			if($invoiceSlip != true){
				DB::rollback();
				Wild_tiger::setFlashMessage ( 'danger', trans('messages.require-document-invoice') );
				return redirect ( $this->redirectUrl );
			}
			/* for ($i = 0; $i <= $goodInBuyerCount; $i++){
				$rowData = [];
				$rowData['i_goods_in_buyer_master_id'] = $insertRecord;
				$rowData['i_document_type_id'] = (!empty($request->input('type_'.$i)) ? (int)Wild_tiger::decode($request->input('type_'.$i)) :0);
				$rowData['v_document_remark'] = (!empty($request->input('remarks_'.$i)) ? $request->input('remarks_'.$i) : null);
				$rowData['v_document_file_path'] = null;
				
				if($request->hasFile('file_'.$i)){
					$uploadFile = $this->uploadMultipleFile($request, 'file_'.$i,'image_doc_pdf_xls');
					if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
						$rowData['v_document_file_path'] = json_encode($uploadFile['filePath']);
					} else {
						DB::rollback();
						Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
						return redirect ( $this->redirectUrl );
					}
				}
				if( (!empty($rowData['v_document_file_path'])) && (!empty($rowData ['i_document_type_id'])) ){
					$insertGoodInBuyerDocumentDetail = $this->crudModel->insertTableData( config('constants.GOODS_IN_BUYER_DOCUMENT_TABLE') , $rowData);
				}
				
			} */
			$result = true;
		}catch(\Exception $e){
			$result = false;
			DB::rollback();
			Log::error($e->getMessage());
			Log::error($e->getLine());
			Log::error($e->getFile());
			
		}
		
		if( $result != false ){
			DB::commit();
			Wild_tiger::setFlashMessage ( 'success', $successMessage  );
			return redirect ( $this->redirectUrl );
		}

		DB::rollback();
		Wild_tiger::setFlashMessage ( 'danger', $errorMessages  );
		return redirect()->back()->withErrors ( $validator )->withInput ();
		
	}
	public function edit($id){
		if(isset($this->secondUriSegment) && ( $this->secondUriSegment == 'edit' )){
			if(checkPermission(config('permission_constants.EDIT_GOODS_IN_BUYER')) != true ){
				return redirect('access-denied');
			}
		}
		
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		if( $recordId > 0 ){
			
			$whereData = $where = [];
			$whereData['i_id'] = $recordId;
			$data ['pageTitle'] = trans('messages.update-good-in-buyer');
			
			$whereData['edit_record'] = true;
			$whereData['master_id'] = $recordId;
			
			$userLogin =  session()->get('user_id');
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
			$loggedBuyerUserRole = ( isset($userLoginDetails[0]->v_record_type) ? explode("," , $userLoginDetails[0]->v_record_type ) : [] );
			$whereData['loggedUserBuyerRoles'] = $loggedBuyerUserRole;
			
			$recordInfo = $this->crudModel->getGoodsInBuyerDetails( $whereData );;
			
			if(count($recordInfo) > 0){
				$errorFound = false;
				$data['companyRecordDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
				$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				$data['userBuyerRecordDetails'] = Login::where('v_role' , config('constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				$data['poCreateUserDetails'] = Login::where('v_role',config('constants.ROLE_USER'))->orderBy('v_name' , 'asc')->get();
				$data['paymentTermsDetails'] = LookupMaster::where('v_module_name',config('constants.PAYMENT_TERMS_LOOKUP'))->orderBy('v_value')->get();
				$data['dangerousGoodsDetails'] = LookupMaster::where('v_module_name',config('constants.DANGEROUS_GOODS_LOOKUP'))->orderBy('v_value')->get();
				$data['goodsRemarksDetails'] = LookupMaster::where('v_module_name',config('constants.GOODS_REMARK_LOOKUP'))->orderBy('v_value')->get();
				$data['supplierRecordDetails'] = SupplierMasterModel::orderBy('v_supplier_name', 'ASC')->get();
				$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
				//$data['dimensionPalletRecordDetails'] = DimensionMasterModel::where('e_dimension',config ( 'constants.PALLET'))->orderBy('v_dimension_name', 'ASC')->get();
				$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type' , config ( 'constants.BUYER') )->orderBy('v_document_type_name', 'ASC')->get();
				$data['collectionDeliveryInfo'] = collectionDeliveryInfo();
				$data['deliveryTypeInfo'] = deliveryTypeInfo();
				$data['customsProcedureInfo'] = customsProcedureDropdown();
				$data['palletsTypeInfo'] = palletsTypeInfo();
				$data['palletBoxInfo'] = typeInfo();
				//$data['weightUnitInfo'] = weightUnitInfo();
				$data['warehouseRecordDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['readyForCollectionInfo'] = dangerousGoodsInfo();
				
				$warehouseWhere = [];
				
				if(count($userLoginDetails) > 0){
					if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
						$warehouseIds = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id :0 );
						$warehouseWhere['i_id'] = $warehouseIds;
					}
				}
				$data['warehouseDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['warehouseRecordDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				
				$dimensionWhere = [];
				$dimensionWhere['t_is_deleted'] = 0;
				if (isset($recordInfo[0]->goodInBuyerMaster->e_pallet_box_type) && !empty($recordInfo[0]->goodInBuyerMaster->e_pallet_box_type)){
					$dimensionWhere['e_dimension'] = $recordInfo[0]->goodInBuyerMaster->e_pallet_box_type;
				}
				$data['dimensionRecordDetails'] = DimensionMasterModel::where($dimensionWhere)->orderBy('v_dimension_name', 'ASC')->get();				
				
				$mainSupplierId = ( isset($data ['recordInfo']->goodInBuyerMaster->i_main_supplier_id) ? $data ['recordInfo']->goodInBuyerMaster->i_main_supplier_id : 0 );
				
				$data['supplierLocationRecordDetails'] = [];
				if( $mainSupplierId > 0 ){
					$allSupplierDetailIds = ( isset($data ['recordInfo']->goodInBuyerMaster->v_supplier_ids) ? explode("," , $data ['recordInfo']->goodInBuyerMaster->v_supplier_ids ) : [] );
					
					if(!empty($allSupplierDetailIds)){
						$data['supplierLocationRecordDetails'] = SupplierDetailModel::whereIn('i_id',$allSupplierDetailIds)->get();
					} else {
						$data['supplierLocationRecordDetails'] = SupplierDetailModel::with(['supplierMaster'])->whereHas('supplierMaster', function($query)use ($mainSupplierId){
							$query->where('i_supplier_id',$mainSupplierId);
						})->get();
					}
				}
				
				$disableForm = '';
				$documentForm = '';
				$viewForm = false;
				if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
					$data ['pageTitle'] = trans('messages.view-good-in-buyer');
					$disableForm = 'disabled';
					$documentForm = 'disabled';
					$viewForm = true;
				}
				if( $recordInfo[0]->t_in_use == 1 ){
					$disableForm = 'disabled';
				}
				$data['disableForm'] = $disableForm;
				$data['documentForm'] = $documentForm;
				$data['viewForm'] = $viewForm;
			}
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
		return view ( $this->folderName . 'add-good-in-buyer' )->with ( $data );
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_GOODS_IN_BUYER')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$goodInBuyerDetailData['t_is_active'] = 0;
			$goodInBuyerDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=>trans('messages.good-in-buyer')]);
			$errorMessages = trans('messages.error-delete',['module'=>trans('messages.good-in-buyer')]);
			
			$whereData['edit_record'] = true;
			$whereData['master_id'] = $recordId;
			
			$recordInfo = $this->crudModel->getGoodsInBuyerDetails( $whereData );
			
			$goodInBuyerMasterId = (isset($recordInfo[0]->goodInBuyerMaster) && $recordInfo[0]->goodInBuyerMaster->i_id > 0 ? $recordInfo[0]->goodInBuyerMaster->i_id : 0);
			
			
			if( (!empty($recordInfo)) && (isset($recordInfo[0]->t_in_use)) && ( $recordInfo[0]->t_in_use == 1 ) ){
				/* Wild_tiger::setFlashMessage ( 'danger',trans('messages.error-logistic-used-record-delete'));
				return redirect()->back(); */
			}
			
			DB::beginTransaction();
				
			$result = false; 
			
			try{
				$oldSupplierIds = isset($recordInfo[0]['goodInBuyerMaster']->v_supplier_ids) ? explode("," , $recordInfo[0]['goodInBuyerMaster']->v_supplier_ids ) : [] ;
				$removeSuppierId =  isset($recordInfo[0]->i_goods_in_buyer_supplier_id) ? $recordInfo[0]->i_goods_in_buyer_supplier_id : null ;
				$mainGoodsInBuyerRecordId =  isset($recordInfo[0]->i_goods_in_buyer_master_id) ? $recordInfo[0]->i_goods_in_buyer_master_id : null ;
				
				if(!empty($oldSupplierIds)){
					foreach($oldSupplierIds as $supplierKey =>  $oldSupplierId){
						if( $oldSupplierId == $removeSuppierId ){
							unset($oldSupplierIds[$supplierKey]);
						}
					}
				}
				
				if(!empty($mainGoodsInBuyerRecordId)){
					$this->crudModel->updateTableData(  config('constants.GOODS_IN_BUYER_MASTER_TABLE') ,  ['v_supplier_ids' => (!empty($oldSupplierIds) ? implode("," , $oldSupplierIds ) : null ) ] , [ 'i_id' => $mainGoodsInBuyerRecordId ] );
				}
				
				$this->crudModel->deleteTableData(  config('constants.GOODS_IN_BUYER_DETAIL_TABLE') ,  $goodInBuyerDetailData , [ 'i_id' => $recordId ] );
				
				$updateGoodsInLogisticData = [];
				$updateGoodsInLogisticData['t_is_active'] = 0;
				$updateGoodsInLogisticData['t_is_deleted'] = 1;
				$updateGoodsInLogisticData['i_deleted_id'] = session()->get('user_id');
				$updateGoodsInLogisticData['dt_deleted_at'] = date('Y-m-d H:i:s');
				GoodInLogisticMasterModel::whereRaw("find_in_set('".$recordId."' , i_goods_in_buyer_detail_id)")->update($updateGoodsInLogisticData);
				
				//$this->crudModel->deleteTableData(  config('constants.GOODS_IN_BUYER_DOCUMENT_TABLE') ,  $goodInBuyerDetailData , [ 'i_goods_in_buyer_master_id' => $recordId ] );
				//$this->crudModel->deleteTableData( $this->tableName,  $goodInBuyerDetailData , [ 'i_id' => $recordId ] );
				
				if( $goodInBuyerMasterId > 0 ){
					$goodInBuyerDeletedRecordDetails = GoodInBuyerDetailModel::where('i_goods_in_buyer_master_id' , $goodInBuyerMasterId)->where('t_is_deleted',0)->get();
					
					$goodInBuyerMasterDeleteData['t_is_active'] = 0;
					$goodInBuyerMasterDeleteData['t_is_deleted'] = 1;
						
					if (count($goodInBuyerDeletedRecordDetails) == 0){
						$this->crudModel->deleteTableData(config('constants.GOODS_IN_BUYER_MASTER_TABLE') , $goodInBuyerMasterDeleteData ,  ['i_id' => $goodInBuyerMasterId]);
					}
				}
				
				$result = true;
			}catch(\Exception $e){
				$result = false;
				Log::error($e->getMessage());
			}
			
			if( $result != false ){
				DB::commit();
				Wild_tiger::setFlashMessage ( 'success', $successMessage );
			} else {
				DB::rollback();
				Wild_tiger::setFlashMessage ( 'danger',$errorMessages);
			}
			return redirect()->back();
		}
	}
	public function getSupplierLocationDetails(Request $request){
		
		if( ( !empty($request->post('supplier_record_id') ) ) && ( $request->post('supplier_record_id') ) ){
			$supplierIds = explode("," , $request->post('supplier_record_id') );
			$whereData = [];
			//$whereData['e_record_status'] = config('constants.COLLECTION');
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
	
	public function updateDetailCancelledStatus(Request $request){
		
		$buyerDetailRecordId = (!empty($request->post('buyer_detail_record_id')) ? (int)Wild_tiger::decode($request->post('buyer_detail_record_id')) : 0 );
		
		if( $buyerDetailRecordId > 0 ) {
			
			$whereData['edit_record'] = true;
			$whereData['master_id'] = $buyerDetailRecordId;
			$recordInfo = $this->crudModel->getGoodsInBuyerDetails( $whereData );;
			/* if( (!empty($recordInfo)) && (isset($recordInfo[0]->t_in_use)) && ( $recordInfo[0]->t_in_use == 1 ) ){
				$this->ajaxResponse(101, trans('messages.error-logistic-used-record-delete'));
			} */
			
			$updateRecordInfo  = [];
			$updateRecordInfo['e_buyer_record_status'] = config('constants.CANCELLED_DELIVERY_TYPE');
			
			$result = $this->crudModel->updateTableData(  config('constants.GOODS_IN_BUYER_DETAIL_TABLE') ,  $updateRecordInfo , [ 'i_goods_in_buyer_master_id' => $recordInfo[0]->i_goods_in_buyer_master_id ] );
			
			//$this->crudModel->updateTableData(  config('constants.GOODS_IN_BUYER_MASTER_TABLE') ,  ['e_delivery_type' =>  config('constants.CANCELLED_DELIVERY_TYPE') ] , [ 'i_id' => $recordInfo[0]->i_goods_in_buyer_master_id ] );
			
			if( $result != false ){
				$this->ajaxResponse(1, trans('messages.success-update-cancelled-status'));
			} else {
				$this->ajaxResponse(101, trans('messages.error-update-cancelled-status'));
			}
			
		}
		
	}
	public function shipmentQuotePdf($id = null){
		$whereData = [];
		$recordId = (!empty($id) ? (int)Wild_tiger::decode($id) : 0);
		if(!empty($recordId)){
			$whereData['edit_record'] = true;
			$whereData['master_id'] = $recordId;
			$recordInfo = $this->crudModel->getGoodsInBuyerDetails( $whereData );
			
			$getRecordInfo = (!empty($recordInfo)  ? $recordInfo[0] : [] );
			$result = generatePdf($getRecordInfo);
			return $result;
		}
	}
	public function getSupplierDetails(Request $request){
		$supplierCountryId = (!empty($request->post('supplier_country_id')) ? (int)Wild_tiger::decode($request->post('supplier_country_id')) : 0);
		$whereData = [];
		$whereData['i_country_id'] = $supplierCountryId;
		
		$getSupplierDetails = SupplierMasterModel::with(['supplierDetail'])->whereHas('supplierDetail', function($query)use ($whereData)
		{
			$query->where($whereData);
		
		})->get();
		$html = '';
		if(!empty($getSupplierDetails)){
			foreach ($getSupplierDetails as $getSupplierDetail){
				$encodeSupplierId  = Wild_tiger::encode($getSupplierDetail->i_id);
				$html .= '<option value="'.$encodeSupplierId.'">'.(!empty($getSupplierDetail->v_supplier_name) ? $getSupplierDetail->v_supplier_name : '').'</option>';
			}
		}
		echo $html;die;
	} 
	
	public function checkUniquePoSalesInvoiceNumber(Request $request){
		if (!empty($request->input())){
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
			
			$validator = Validator::make ( $request->all (), [
					'po_no_sales_invoice_no' => [ 'required' , new UniquePoSalesInvoiceNumber($recordId) ]  ,
			], [
					'po_no_sales_invoice_no.required' => __ ( 'messages.require-po-no-sales-invoice-no' ),
			] );
			
			$result = [];
			$result['status_code'] = 1;
			$result['message'] = trans('messages.success');
			
			if ($validator->fails()){
				$result['status_code'] = 101;
				$result['message'] = trans('messages.error');
			}
			echo json_encode($result);die;
		}
	}
	
	public function importExcel(Request $request) {
	
		if(checkPermission(config('permission_constants.ADD_GOODS_IN_BUYER')) != true ){
			return redirect('access-denied');
		}
	
		if (!empty($_FILES)){
			$importFileUpload = null;
				
			if( !empty( $_FILES['good_in_buyer_excel']['name'] ) ){
				$importFile = $this->uploadFile( $request , 'good_in_buyer_excel', 'xlsx' );
				$importFileUpload = (isset($importFile['status']) && $importFile['status'] != false ? $importFile['filePath'] : '');
			}
				
			$requiredColoumns = [
					'PO Number',
					'Vendor Number',
					'Supplier Name',
					'Invoice Number',
					'PO Create User Name',
					'PO Creation Date',
					'PO Amount',
					'PO Amount with VAT',
					'Payment Terms',
					'Currency Code',
					'Prepayment %',
					'Buyer Company',
					'User Company',
					'Buyer Name',
					'User Buyer Name',
					'Collection / Delivery',
					'Mode Of Transport',
					'Ready For Collection',
					'Delivery Location',
					'Buyer Delivery Date',
					'Goods Remark',
					'Order Date',
					'Brand',
					'Payment Date',
					'Customs Procedure',
					'Dangerous Goods',
					'Pallet / Box',
					'No. Of Pallet / Box',
					'Dimension',
					'Pallets Type',
					'Gross Weight',
					'Net Weight',
					'Total Units',
					'Buyer Comments',
			];
				
			if(!empty($importFileUpload)){
				$inputFileName = config('constants.FILE_STORAGE_FILE_PATH') . config('constants.UPLOAD_FOLDER') . $importFileUpload;
	
				$filePath = $inputFileName;
	
				$reader = ReaderEntityFactory::createReaderFromFile($filePath);
				$reader->open($filePath);
				
				$finalData = [];
				foreach ($reader->getSheetIterator() as $sheet) {
					foreach ($sheet->getRowIterator() as $rowKey =>  $row) {
						$cells = $row->getCells();
						$rowData = [];
						for($i = 0; $i < count($cells); $i++ ){
							$rowData[] = $cells[$i]->getValue();
						}
	
						if(!empty($rowData)){
							$finalData[] = $rowData;
						}
					}
				}
				
				// Validate sheet column
				if(isset($finalData) && !empty($finalData) && isset($finalData[0]) && !empty($finalData[0]) && isset($requiredColoumns) && !empty($requiredColoumns) &&  $requiredColoumns != $finalData[0]){
					//Wild_tiger::setFlashMessage('danger', trans('messages.upload-sheet-not-valid' , ['module' => $this->moduleName]));
					//return redirect($this->redirectUrl);
				}
			
				$reader->close();
				$rowDetails = [];
				$allDataInSheet = $finalData;
				
				if(!empty($allDataInSheet)){
					foreach ($allDataInSheet as $key => $value) {
						if( $key == 0 ){
							$excelKeys = array_values($value);
						} else {
							$rowDetail = [];
							$rowDetail = array_combine($excelKeys, $value);
								
							if(!empty($rowDetail)){
								$rowDetails[] = $rowDetail;
							}
						}
					}
				}
				
				$finalExcelData = $rowExcelData = [];
				if (!empty($rowDetails)){
					foreach ($rowDetails as $rowDetail){
						foreach( $rowDetail as $rowKey => $rowValue){
							$rowKey = strtolower( trim($rowKey) );
							$rowKey = str_replace(" ", "_", $rowKey);
							
							/* if (is_object($rowValue)){
								Wild_tiger::setFlashMessage('danger', trans('messages.upload-sheet-not-valid' , ['module' => $this->moduleName]));
								return redirect($this->redirectUrl);
							} */
							
							$rowValue = (isset($rowValue) && !empty(($rowValue)) ? ($rowValue) : '');
							switch(trim($rowKey)){
								case 'po_number':
									$rowExcelData['v_po_sales_invoice_no'] = $rowValue;
									break;
								case 'vendor_number':
									$rowExcelData['v_vendor_number'] = $rowValue;
									break;
								case 'supplier_name':
									$rowExcelData['i_main_supplier_id'] = $rowValue;
									break;
								case 'invoice_number':
									$rowExcelData['v_invoice_no'] = $rowValue;
									break;
								case 'po_create_user_name':
									$rowExcelData['i_po_create_user_id'] = $rowValue;
									break;
								case 'po_creation_date':
									$rowExcelData['dt_po_creation_date'] = $rowValue;
									break;
								case 'po_amount':
									$rowExcelData['d_po_amount'] = $rowValue;
									break;
								case 'po_amount_with_vat':
									$rowExcelData['d_po_amount_with_vat'] = $rowValue;
									break;
								case 'payment_terms':
									$rowExcelData['i_payment_terms_id'] = $rowValue;
									break;
								case 'currency_code':
									$rowExcelData['i_po_currency_id'] = $rowValue;
									break;
								case 'prepayment_%':
									$rowExcelData['d_prepayment_percentage'] = $rowValue;
									break;
								case 'buyer_company':
									$rowExcelData['i_buyer_company_id'] = $rowValue;
									break;
								case 'user_company':
									$rowExcelData['v_user_company_ids'] = $rowValue;
									break;
								case 'buyer_name':
									$rowExcelData['v_buyer_employee_ids'] = $rowValue;
									break;
								case 'user_buyer_name':
									$rowExcelData['v_user_buyer_ids'] = $rowValue;
									break;
								case 'collection_/_delivery':
									$rowExcelData['e_collection_type'] = $rowValue;
									break;
								case 'mode_of_transport':
									$rowExcelData['e_mode_of_transport'] = $rowValue;
									break;
								case 'ready_for_collection':
									$rowExcelData['e_ready_for_collection_status'] = $rowValue;
									break;
								case 'delivery_location':
									$rowExcelData['i_delivery_location_id'] = $rowValue;
									break;
								case 'buyer_delivery_date':
									$rowExcelData['dt_delivery_date'] = $rowValue;
									break;
								case 'goods_remark':
									$rowExcelData['v_goods_remark_ids'] = $rowValue;
									break;
								case 'supplier_location':
									$rowExcelData['v_supplier_ids'] = $rowValue;
									break;
								/* case 'supplier_country':
									$rowExcelData['i_supplier_country_id'] = $rowValue;
									break; */
								case 'order_date':
									$rowExcelData['dt_order_date'] = $rowValue;
									break;
								case 'brand':
									$rowExcelData['v_brand'] = $rowValue;
									break;
								case 'payment_date':
									$rowExcelData['dt_payment_date'] = $rowValue;
									break;
								case 'customs_procedure':
									$rowExcelData['e_customs_procedure'] = $rowValue;
									break;
								case 'dangerous_goods':
									$rowExcelData['i_dangerous_goods_id'] = $rowValue;
									break;
								case 'pallet_/_box':
									$rowExcelData['e_pallet_box_type'] = $rowValue;
									break;
								case 'no._of_pallet_/_box':
									$rowExcelData['i_no_of_pallet_box'] = $rowValue;
									break;
								case 'dimension':
									$rowExcelData['v_dimension_ids'] = $rowValue;
									break;
								case 'pallets_type':
									$rowExcelData['e_pallet_type'] = $rowValue;
									break;
								case 'gross_weight':
									$rowExcelData['d_weight'] = $rowValue;
									break;
								case 'net_weight':
									$rowExcelData['d_net_weight'] = $rowValue;
									break;
								case 'total_units':
									$rowExcelData['i_total_units'] = $rowValue;
									break;
								case 'buyer_comments':
									$rowExcelData['v_buyer_comments'] = $rowValue;
									break;
							}
						}
	
						$finalExcelData[] = $rowExcelData;
					}
				}
	
				$poNumberInsertedInSheet = [];
				
				$sumOfPreviousRecords = [];
				
				$goodInBuyerDetailRecord = $this->crudModel->selectData(config('constants.GOODS_IN_BUYER_MASTER_TABLE') ,['i_id'], ['getCount' => true]);
				$goodInBuyerDetailRecordCount = (isset($goodInBuyerDetailRecord) && !empty($goodInBuyerDetailRecord) && isset($goodInBuyerDetailRecord[0]) && !empty($goodInBuyerDetailRecord[0]) && isset($goodInBuyerDetailRecord[0]->record_count) && !empty($goodInBuyerDetailRecord[0]->record_count) ? $goodInBuyerDetailRecord[0]->record_count : 1 );
				$model = new WarehousePalletMasterModel();
				
				$wareHousePalletLimit = $model->getRecordDetails();
				$whereGoodsIn = [];
				$whereGoodsIn['group_by'] = [ 'gdm.i_delivery_location_id' , 'gdm.dt_delivery_date' ];
				$goodInTotalDetails = $this->crudModel->getTotalLimitWareHouse($whereGoodsIn);
				
				DB::beginTransaction();
	
				$result = false;
	
				try {
					$allExcelErrors = [];
					if (!empty($finalExcelData)){
						foreach ($finalExcelData as $srKey => $finalExcel){
							$excelRecordNo = $srKey + 2;
								
							$rowData = [];
								
							if (!empty($finalExcel['v_po_sales_invoice_no'])){
								$where = [];
								$where['t_is_deleted'] = 0;
								$where['t_is_active'] = 1;
								$where['v_po_sales_invoice_no'] = trim($finalExcel['v_po_sales_invoice_no']);
								$checkPoExist = GoodInBuyerMasterModel::where($where)->first();
	
								if (empty($checkPoExist) && !in_array($finalExcel['v_po_sales_invoice_no'], $poNumberInsertedInSheet)){
									$poNumberInsertedInSheet[] = $finalExcel['v_po_sales_invoice_no'];
									$rowData['v_po_sales_invoice_no'] = $finalExcel['v_po_sales_invoice_no'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-duplicate-sheet-row-info', [ 'columnName' => trans('messages.po-number')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							$rowData['v_vendor_number'] = (!empty($finalExcel['v_vendor_number']) ? $finalExcel['v_vendor_number'] : '');
								
							if (!empty($finalExcel['i_main_supplier_id'])){
								$where = [];
								$where['t_is_deleted'] = 0;
								$where['t_is_active'] = 1;
								$where['v_supplier_name'] = $finalExcel['i_main_supplier_id'];
	
								$supplierInfo = SupplierMasterModel::where($where)->first();
								$supplierId = isset($supplierInfo) && !empty($supplierInfo) && isset($supplierInfo->i_id) && !empty($supplierInfo->i_id) ? $supplierInfo->i_id : '';
	
								if (!empty($supplierId)){
									$rowData['i_main_supplier_id'] = $supplierId;
									
									$supplierLocationwhere = [];
									$supplierLocationwhere['i_supplier_id'] = $rowData['i_main_supplier_id'];
									$supplierLocationwhere['e_record_status'] = config('constants.COLLECTION');
									$supplierLocationwhere['t_is_active'] = 1;
									$supplierLocationwhere['t_is_deleted'] = 0;

									$supplierLocationInfo = SupplierDetailModel::where($supplierLocationwhere)->first();
									$supplierLocationId = (isset($supplierLocationInfo) && !empty($supplierLocationInfo) && isset($supplierLocationInfo->i_id) && !empty($supplierLocationInfo->i_id) ? $supplierLocationInfo->i_id : '');
										
									if (!empty($supplierLocationId)){
										$rowData['v_supplier_ids'] = $supplierLocationId;
									}else {
										$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-for-selected-info', [ 'columnName' => trans('messages.supplier-location') , 'masterColumn' =>  trans('messages.supplier-name') , 'srNo' => $excelRecordNo ] );
									}
									
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.supplier-name')  , 'srNo' => $excelRecordNo ] );
								}
							}
								
							$rowData['v_invoice_no'] = (!empty($finalExcel['v_invoice_no']) ? $finalExcel['v_invoice_no'] : null);
								
							if (!empty($finalExcel['i_po_create_user_id'])){
									
								$poCreateUserWhere = [];
								$poCreateUserWhere['t_is_active'] = 1;
								$poCreateUserWhere['t_is_deleted'] = 0;
								$poCreateUserWhere['v_role'] = config('constants.ROLE_USER');
								$poCreateUserWhere['v_name'] = $finalExcel['i_po_create_user_id'];
								
								$poCreateUserInfo = Login::where($poCreateUserWhere)->first();
								$poCreateUserId = (isset($poCreateUserInfo) && !empty($poCreateUserInfo) && isset($poCreateUserInfo->i_id) && !empty($poCreateUserInfo->i_id) ? $poCreateUserInfo->i_id : '');
									
								if (!empty($poCreateUserId)){
									$rowData['i_po_create_user_id'] = $poCreateUserId;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.po-create-user-name')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['dt_po_creation_date'])){
								if (validateDate($finalExcel['dt_po_creation_date'])){
									$rowData['dt_po_creation_date'] = excelDateFormat($finalExcel['dt_po_creation_date']);
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.po-creation-date')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['d_po_amount'])){
								if (checkNumericValue($finalExcel['d_po_amount']) != false){
									$rowData['d_po_amount'] = $finalExcel['d_po_amount'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.po-no-sales-invoice-amount')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['d_po_amount_with_vat'])){
								if (checkNumericValue($finalExcel['d_po_amount_with_vat']) != false){
									$rowData['d_po_amount_with_vat'] = $finalExcel['d_po_amount_with_vat'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.po-amount-with-vat')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['i_po_currency_id'])){
								$where = [];
								$where['t_is_deleted'] = 0;
								$where['t_is_active'] = 1;
								$where['v_currency_code'] = $finalExcel['i_po_currency_id'];
								
								$currencyCodeinfo = CurrencyMasterModel::where($where)->first();
								$currencyCodeId = (isset($currencyCodeinfo) && !empty($currencyCodeinfo) && isset($currencyCodeinfo->i_id) && !empty($currencyCodeinfo->i_id) ? $currencyCodeinfo->i_id : '');
									
								if (!empty($currencyCodeId)){
									$rowData['i_po_currency_id'] = $currencyCodeId;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.currency-code')  , 'srNo' => $excelRecordNo ] );
								}
							}
								
							if (!empty($finalExcel['i_payment_terms_id'])){
									
								$paymentTermsWhere = [];
								$paymentTermsWhere['t_is_active'] = 1;
								$paymentTermsWhere['t_is_deleted'] = 0;
								$paymentTermsWhere['v_module_name'] = config('constants.PAYMENT_TERMS_LOOKUP');
								$paymentTermsWhere['v_value'] = $finalExcel['i_payment_terms_id'];
								
								$paymentTermsInfo = LookupMaster::where($paymentTermsWhere)->first();
								$paymentTermsId = (isset($paymentTermsInfo) && !empty($paymentTermsInfo) && isset($paymentTermsInfo->i_id) && !empty($paymentTermsInfo->i_id) ? $paymentTermsInfo->i_id : '');
									
								if (!empty($paymentTermsId)){
									$rowData['i_payment_terms_id'] = $paymentTermsId;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.payment-terms')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['d_prepayment_percentage'])){
								if ($finalExcel['d_prepayment_percentage'] >= 0 && $finalExcel['d_prepayment_percentage'] <= 100 && checkNumericValue($finalExcel['d_prepayment_percentage'] , true) != false){
									$rowData['d_prepayment_percentage'] = $finalExcel['d_prepayment_percentage'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.prepayment-percentage')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['i_buyer_company_id'])){
								$where = [];
								$where['t_is_deleted'] = 0;
								$where['t_is_active'] = 1;
								$where['v_company_name'] = $finalExcel['i_buyer_company_id'];
								
								$buyerCompanyInfo = CompanyMasterModel::where($where)->first();
								$buyerCompanyId = (isset($buyerCompanyInfo) && !empty($buyerCompanyInfo) && isset($buyerCompanyInfo->i_id) && !empty($buyerCompanyInfo->i_id) ? $buyerCompanyInfo->i_id : '');
	
								if (!empty($buyerCompanyId)){
									$rowData['i_buyer_company_id'] = $buyerCompanyId;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.buyer-company')  , 'srNo' => $excelRecordNo ] );
								}
							}
								
							if (!empty($finalExcel['v_user_company_ids'])){
								$userCompanyNamesArray = explode(',', $finalExcel['v_user_company_ids']);
								$userCompanyNames = array_map(function ($companyDetails){
									return trim($companyDetails);
								}, $userCompanyNamesArray);
								
								$where = [];
								$where['t_is_deleted'] = 0;
								$where['t_is_active'] = 1;
							
								$userCompanyIds = CompanyMasterModel::where($where)->whereIn('v_company_name' , $userCompanyNames)->get()->pluck('i_id')->toArray();
									
								if ( (!empty($userCompanyIds)) && ( count($userCompanyIds) > 0 ) && ( count($userCompanyNames) == count($userCompanyIds) ) ){
									$finalUserCompanyIds = implode(',', $userCompanyIds);
									$rowData['v_user_company_ids'] = $finalUserCompanyIds;
								} else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.user-company')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['v_buyer_employee_ids'])){
								$buyerNamesArray = explode(',', $finalExcel['v_buyer_employee_ids']);
								$buyerNames = array_map(function ($buyerDetails){
									return trim($buyerDetails);
								}, $buyerNamesArray);
								
								$buyerNameWhere = [];
								$buyerNameWhere['t_is_active'] = 1;
								$buyerNameWhere['t_is_deleted'] = 0;
								$buyerNameWhere['v_role'] = config('constants.ROLE_USER');
								
								$buyerNameIds = Login::where($buyerNameWhere)->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->whereIn('v_name' , $buyerNames)->get()->pluck('i_id')->toArray();
								
								if ( (!empty($buyerNameIds)) && ( count($buyerNameIds) > 0 ) && ( count($buyerNames) == count($buyerNameIds) ) ){
									$finalBuyerNameIds = implode(',', $buyerNameIds);
									$rowData['v_buyer_employee_ids'] = $finalBuyerNameIds;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.buyer-name')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['v_user_buyer_ids'])){
								$userBuyerNamesArray = explode(',', $finalExcel['v_user_buyer_ids']);
								$userBuyerNames = array_map(function ($userBuyerDetails){
									return trim($userBuyerDetails);
								}, $userBuyerNamesArray);
	
								$userBuyerNameWhere = [];
								$userBuyerNameWhere['t_is_active'] = 1;
								$userBuyerNameWhere['t_is_deleted'] = 0;
								$userBuyerNameWhere['v_role'] = config('constants.ROLE_USER');
									
								$userBuyerNameIds = Login::select('i_id')->where($userBuyerNameWhere)->whereIn('v_name',$userBuyerNames)->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->get()->pluck('i_id')->toArray();

								if ( (!empty($userBuyerNameIds)) && ( count($userBuyerNameIds) > 0 ) && ( count($userBuyerNames) == count($userBuyerNameIds) ) ){
									$finalUserBuyerNameIds = implode(',', $userBuyerNameIds);
									$rowData['v_user_buyer_ids'] = $finalUserBuyerNameIds;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.user-buyer-name')  , 'srNo' => $excelRecordNo ] );
								}
							}
								
							$rowData['e_collection_type'] = null;
							if (!empty($finalExcel['e_collection_type'])){
								if (in_array(strtolower($finalExcel['e_collection_type']), array_map('strtolower', collectionDeliveryInfo()))){
									$rowData['e_collection_type'] = array_search(strtolower($finalExcel['e_collection_type']), array_map('strtolower', collectionDeliveryInfo()));
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.collection-delivery')  , 'srNo' => $excelRecordNo ] );
								}
								
								if (!empty($finalExcel['e_ready_for_collection_status']) && $rowData['e_collection_type'] == config('constants.COLLECTION')){
									if (in_array(strtolower($finalExcel['e_ready_for_collection_status']), array_map('strtolower', dangerousGoodsInfo()))){
										$rowData['e_ready_for_collection_status'] = array_search(strtolower($finalExcel['e_ready_for_collection_status']), array_map('strtolower', dangerousGoodsInfo()));
									}else {
										$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.ready-for-collection')  , 'srNo' => $excelRecordNo ] );
									}
								}
							}
							
							if (!empty($finalExcel['i_delivery_location_id'])){
								$warehouseWhere = $userLoginWhere = [];
									
								$userLoginDetails = null;
								if (session()->has('role') && !empty(session()->get('role')) && (session()->get('role') != config('constants.ROLE_ADMIN'))){
									$userLoginWhere['t_is_active'] = 1;
									$userLoginWhere['t_is_deleted'] = 0;
									$userLoginWhere['i_id'] = session()->get('user_id');
									$userLoginWhere['v_role'] = config('constants.ROLE_USER');
									$userLoginDetails = Login::where($userLoginWhere)->whereRaw("find_in_set('".config("constants.GOODS_IN_WAREHOUSE")."',v_record_type)")->first();
								}
								
								$warehouseWhere['e_record_type'] = config('constants.WAREHOUSE');
								$warehouseWhere['t_is_active'] = 1;
								$warehouseWhere['t_is_deleted'] = 0;
								if (!empty($userLoginDetails) && !empty($userLoginDetails->i_warehouse_id)){
									$warehouseWhere['i_id'] = $userLoginDetails->i_warehouse_id;
								}
								$warehouseWhere['v_warehouse_name'] = $finalExcel['i_delivery_location_id'];
								$deliveryLocationId = WarehouseMasterModel::where($warehouseWhere)->value('i_id');
								
								if (!empty($deliveryLocationId)){
									$rowData['i_delivery_location_id'] = $deliveryLocationId;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.delivery-location')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['dt_delivery_date'])){
								if (validateDate($finalExcel['dt_delivery_date'])){
									$rowData['dt_delivery_date'] = excelDateFormat($finalExcel['dt_delivery_date']);
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.buyer-delivery-date')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							
							if (!empty($finalExcel['v_user_buyer_ids'])){
								$userBuyerNamesArray = explode(',', $finalExcel['v_user_buyer_ids']);
								$userBuyerNames = array_map(function ($userBuyerDetails){
									return trim($userBuyerDetails);
								}, $userBuyerNamesArray);
							
								$userBuyerNameWhere = [];
								$userBuyerNameWhere['t_is_active'] = 1;
								$userBuyerNameWhere['t_is_deleted'] = 0;
								$userBuyerNameWhere['v_role'] = config('constants.ROLE_USER');
									
								$userBuyerNameIds = Login::select('i_id')->where($userBuyerNameWhere)->whereIn('v_name',$userBuyerNames)->whereRaw("find_in_set('".config("constants.BUYER")."',v_record_type)")->get()->pluck('i_id')->toArray();
						
								if ( (!empty($userBuyerNameIds)) && ( count($userBuyerNameIds) > 0 ) && ( count($userBuyerNames) == count($userBuyerNameIds) ) ){
									$finalUserBuyerNameIds = implode(',', $userBuyerNameIds);
									$rowData['v_user_buyer_ids'] = $finalUserBuyerNameIds;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.user-buyer-name')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['v_goods_remark_ids'])){
								$goodsRemarkArray = explode(',', $finalExcel['v_goods_remark_ids']);
								$goodsRemark = array_map(function ($goodsRemarkDetails){
									return trim($goodsRemarkDetails);
								}, $goodsRemarkArray);
								
								$goodsRemarkWhere = [];
								$goodsRemarkWhere['t_is_active'] = 1;
								$goodsRemarkWhere['t_is_deleted'] = 0;
								$goodsRemarkWhere['v_module_name'] = config('constants.GOODS_REMARK_LOOKUP');
								
								$goodsRemarkIds = LookupMaster::where($goodsRemarkWhere)->whereIn('v_value' , $goodsRemark)->get()->pluck('i_id')->toArray();
									
								if ( (!empty($goodsRemarkIds)) && ( count($goodsRemarkIds) > 0 ) && ( count($goodsRemarkIds) == count($goodsRemark) ) ){
									$finalGoodsRemarkIds = implode(',', $goodsRemarkIds);
									$rowData['v_goods_remark_ids'] = $finalGoodsRemarkIds;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.goods-remark')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['dt_order_date'])){
								if (validateDate($finalExcel['dt_order_date'])){
									if ( excelDateFormat($finalExcel['dt_order_date']) <= date('Y-m-d')){
										$rowData['dt_order_date'] = excelDateFormat($finalExcel['dt_order_date']);
									}else {
										$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-future-date-info', [ 'columnName' => trans('messages.order-date')  , 'srNo' => $excelRecordNo ] );
									}
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.order-date')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							$rowData['v_brand'] = (!empty($finalExcel['v_brand']) ? $finalExcel['v_brand'] : '');
							
							if (!empty($finalExcel['dt_payment_date'])){
								if (validateDate($finalExcel['dt_payment_date'])){
									$rowData['dt_payment_date'] = excelDateFormat($finalExcel['dt_payment_date']);
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.payment-date')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['e_customs_procedure'])){
								if (in_array(strtolower($finalExcel['e_customs_procedure']), array_map('strtolower', customsProcedureDropdown()))){
									$rowData['e_customs_procedure'] = array_search(strtolower($finalExcel['e_customs_procedure']), array_map('strtolower', customsProcedureDropdown()));
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.customs-procedure')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['i_dangerous_goods_id'])){
								$dangerousGoodsWhere = [];
								$dangerousGoodsWhere['t_is_active'] = 1;
								$dangerousGoodsWhere['t_is_deleted'] = 0;
								$dangerousGoodsWhere['v_module_name'] = config('constants.DANGEROUS_GOODS_LOOKUP');
								$dangerousGoodsWhere['v_value'] = $finalExcel['i_dangerous_goods_id'];
								
								$dangerousGoodsInfo = LookupMaster::where($dangerousGoodsWhere)->first();
								$dangerousGoodsId = (isset($dangerousGoodsInfo) && !empty($dangerousGoodsInfo) && isset($dangerousGoodsInfo->i_id) && !empty($dangerousGoodsInfo->i_id) ? $dangerousGoodsInfo->i_id : '');
									
								if (!empty($dangerousGoodsId)){
									$rowData['i_dangerous_goods_id'] = $dangerousGoodsId;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.dangerous-goods')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['e_pallet_box_type'])){
								if (in_array(strtolower($finalExcel['e_pallet_box_type']), array_map('strtolower', typeInfo()))){
									$rowData['e_pallet_box_type'] = array_search(strtolower($finalExcel['e_pallet_box_type']), array_map('strtolower', typeInfo()));
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.pallet-box')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['i_no_of_pallet_box'])){
								if (is_int($finalExcel['i_no_of_pallet_box']) && $finalExcel['i_no_of_pallet_box'] > 0){
									if(( isset($rowData['i_delivery_location_id']) && !empty($rowData['i_delivery_location_id']) &&  $rowData['i_delivery_location_id'] > 0  ) && (isset($rowData['dt_delivery_date']) && !empty($rowData['dt_delivery_date']))){
										if( !empty($finalExcel['e_pallet_box_type']) && (strtolower($finalExcel['e_pallet_box_type']) == strtolower(config('constants.PALLET')) ) && ( is_int($finalExcel['i_no_of_pallet_box']) && ($finalExcel['i_no_of_pallet_box'] > 0)) ){
											$wareHousePalletLimitCheck = collect($wareHousePalletLimit)->where('i_warehouse_id', $rowData['i_delivery_location_id'])->where('dt_pallet_date' , date($rowData['dt_delivery_date']))->first();
											if(!empty($wareHousePalletLimitCheck) && (isset($wareHousePalletLimitCheck->i_pallet_limit))){
												$goodInTotalDetailsSum = collect($goodInTotalDetails)->where('i_delivery_location_id' , $rowData['i_delivery_location_id'])->where('dt_delivery_date' , date($rowData['dt_delivery_date']))->first();
													
												if( isset($sumOfPreviousRecords) && ( isset($sumOfPreviousRecords[$rowData['dt_delivery_date']] ) ) && (isset( $sumOfPreviousRecords[$rowData['dt_delivery_date']][$rowData['i_delivery_location_id']] )) ){
													$sumOfPreviousRecords[$rowData['dt_delivery_date']][$rowData['i_delivery_location_id']] += $finalExcel['i_no_of_pallet_box'];
												} else {
													$sumOfPreviousRecords[$rowData['dt_delivery_date']][$rowData['i_delivery_location_id']] = $finalExcel['i_no_of_pallet_box'];
												}
													
												$totalLimit = ( isset($goodInTotalDetailsSum) && isset($goodInTotalDetailsSum->total_pallets) && ($goodInTotalDetailsSum->total_pallets > 0) ? $goodInTotalDetailsSum->total_pallets : 0 )  + $sumOfPreviousRecords[$rowData['dt_delivery_date']][$rowData['i_delivery_location_id']];
							
												if( $totalLimit > $wareHousePalletLimitCheck->i_pallet_limit){
													$allExcelErrors[] =  trans ( 'messages.pallet-limit-for-excel', [ 'srNo' => $excelRecordNo ] );
												} else {
													$rowData['i_no_of_pallet_box'] = $finalExcel['i_no_of_pallet_box'];
												}
													
											} else {
												$rowData['i_no_of_pallet_box'] = $finalExcel['i_no_of_pallet_box'];
											}
										} else {
											$rowData['i_no_of_pallet_box'] = $finalExcel['i_no_of_pallet_box'];
										}
									} else {
										$rowData['i_no_of_pallet_box'] = $finalExcel['i_no_of_pallet_box'];
									}
										
								} else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.no-of-pallet-box')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['v_dimension_ids']) && isset($rowData['e_pallet_box_type']) && !empty($rowData['e_pallet_box_type'])){
								
								$dimentionNamesArray = explode(',', $finalExcel['v_dimension_ids']);
								$dimentionNames = array_map(function ($dimentionDetails){
									return trim($dimentionDetails);
								}, $dimentionNamesArray);
								
								$where = [];
								$where['t_is_active'] = 1;
								$where['t_is_deleted'] = 0;
								$where['e_dimension'] = $rowData['e_pallet_box_type'];

								$dimentionNameIds = DimensionMasterModel::where($where)->whereIn('v_dimension_size' , $dimentionNames)->orderBy('v_dimension_name', 'ASC')->get()->pluck('i_id')->toArray();

								if ( (!empty($dimentionNameIds)) && ( count($dimentionNameIds) > 0 ) && ( count($dimentionNames) == count($dimentionNameIds) ) ){
									$finaldimentionNameIds = implode(',', $dimentionNameIds);
									$rowData['v_dimension_ids'] = $finaldimentionNameIds;
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.dimension')  , 'srNo' => $excelRecordNo ] );
								}
							}
							//var_dump($finalExcel['e_pallet_type']);
							//echo "<pre>";print_r(array_map('strtolower', palletsTypeInfo()));
							if (!empty($finalExcel['e_pallet_type'])){
								if (in_array(strtolower($finalExcel['e_pallet_type']), array_map('strtolower', palletsTypeInfo()))){
									$rowData['e_pallet_type'] = array_search(strtolower($finalExcel['e_pallet_type']), array_map('strtolower', palletsTypeInfo()));
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.pallets-type')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['d_weight'])){
								if (checkNumericValue($finalExcel['d_weight'] , true) != false){
									$rowData['d_weight'] = $finalExcel['d_weight'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.gross-weight')  , 'srNo' => $excelRecordNo ] );
								}
							}
							if (!empty($finalExcel['d_net_weight'])){
								if (checkNumericValue($finalExcel['d_net_weight'] , true) != false){
									$rowData['d_net_weight'] = $finalExcel['d_net_weight'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.net-weight')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							if (!empty($finalExcel['i_total_units'])){
								if ((int) $finalExcel['i_total_units'] > 0){
									$rowData['i_total_units'] = $finalExcel['i_total_units'];
								}else {
									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-valid-info', [ 'columnName' => trans('messages.total-units')  , 'srNo' => $excelRecordNo ] );
								}
							}
							
							$rowData['v_buyer_comments'] = (!empty($finalExcel['v_buyer_comments']) ? $finalExcel['v_buyer_comments'] : null);
							
							if(empty($finalExcel['v_po_sales_invoice_no'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.po-number')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['v_vendor_number'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.vendor-number')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_main_supplier_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.supplier-name')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_po_create_user_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.po-create-user-name')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['d_po_amount'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.po-amount')  , 'srNo' => $excelRecordNo ] );
							}
							
							if(empty($finalExcel['d_po_amount_with_vat'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.po-amount-with-vat')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_payment_terms_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.payment-terms')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_po_currency_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.currency-code')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_buyer_company_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.buyer-company')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['v_user_company_ids'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.user-company')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['v_buyer_employee_ids'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.buyer-name')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['v_user_buyer_ids'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.user-buyer-name')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['e_collection_type'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.collection-delivery')  , 'srNo' => $excelRecordNo ] );
							}
							if(!empty($rowData['e_collection_type']) && $rowData['e_collection_type'] == config('constants.COLLECTION') && empty($finalExcel['e_ready_for_collection_status'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.ready-for-collection')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_delivery_location_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.delivery-location')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['dt_delivery_date'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.buyer-delivery-date')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['v_goods_remark_ids'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.goods-remark')  , 'srNo' => $excelRecordNo ] );
							}
							/* if(empty($finalExcel['v_supplier_ids'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.supplier-location')  , 'srNo' => $excelRecordNo ] );
							} */
							if(empty($finalExcel['dt_order_date'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.order-date')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['v_brand'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.brand')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['dt_payment_date'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.payment-date')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['e_customs_procedure'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.customs-procedure')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_dangerous_goods_id'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.dangerous-goods')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['e_pallet_box_type'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.pallet-box')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_no_of_pallet_box'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.no-of-pallet-box')  , 'srNo' => $excelRecordNo ] );
							}
							if(empty($finalExcel['i_total_units'])){
								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.total-units')  , 'srNo' => $excelRecordNo ] );
							}
							
							if(!empty($allExcelErrors)){
								continue;
							}
								
							$goodInBuyerDetailRecordCount++;
							$goodsInBuyerMasterRecordCount = threeNumberSeries($goodInBuyerDetailRecordCount);
							$goodInBuyerDetailGenerateNo = config('constants.GOOD_IN_BUYER_GIB').'-'. $goodsInBuyerMasterRecordCount .'-'.$this->todayDate;
							$rowData['v_goods_in_buyer_master_no'] = $goodInBuyerDetailGenerateNo;
								
							$insertRecord = $this->crudModel->insertTableData($this->tableName, $rowData);
								
							$goodInBuyerDetail = [];
							if(isset($supplierLocationId) && !empty($supplierLocationId)){
								$goodInBuyerDetail['i_goods_in_buyer_master_id'] = $insertRecord;
								$goodInBuyerDetail['i_goods_in_buyer_supplier_id'] = $supplierLocationId;
								$goodInBuyerDetail['v_goods_in_buyer_detail_no'] = $goodInBuyerDetailGenerateNo;
	
								$this->crudModel->insertTableData(config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , $goodInBuyerDetail);
							}
						}
						//echo "<pre>";print_r($allExcelErrors);die;
						if (!empty($allExcelErrors)){
							DB::rollBack();
							Wild_tiger::setFlashMessage('danger', implode("<br>", $allExcelErrors));
							return redirect($this->redirectUrl);
						}
					} else {
						Wild_tiger::setFlashMessage('danger', trans('messages.upoload-sheet-no-data-found' , ['module' => $this->moduleName]));
						DB::rollBack();
						return redirect($this->redirectUrl);
					}
						
					$result = true;
				}catch (\Exception $e){
					$result = false;
					Log::error($e->getMessage());
					Log::error($e->getLine());
					DB::rollBack();
				}
	
				if ($result != false){
					DB::commit();
					Wild_tiger::setFlashMessage('success', trans('messages.success-file-data-imported' , ['module' => $this->moduleName]));
				}else {
					Wild_tiger::setFlashMessage('danger', trans('messages.error-file-data-imported' , ['module' => $this->moduleName]));
					DB::rollBack();
				}
	
				return redirect($this->redirectUrl);
			}
			
			Wild_tiger::setFlashMessage('danger', trans('messages.system-error-file-upload'));
			return redirect($this->redirectUrl);
		}
	}
	public function getDimensionDetails(Request $request){
		if (!empty($request->input())){
			$palletBoxType = (!empty($request->input('pallet_box_type')) ? $request->input('pallet_box_type') : '');
			
			$where = [];
			$where['t_is_deleted'] = 0;
			if (!empty($palletBoxType)){
				$where['e_dimension'] = $palletBoxType;
			}
			
			$html = '';
			$dimensionDetails = DimensionMasterModel::where($where)->orderBy('v_dimension_name', 'ASC')->get();
			if (!empty($dimensionDetails)){
				foreach ($dimensionDetails as $dimensionDetail){
					$encodedDimensionId = (!empty($dimensionDetail->i_id) ? Wild_tiger::encode($dimensionDetail->i_id) : 0);
					$html .= '<option value="'.$encodedDimensionId.'" >'.(!empty($dimensionDetail->v_dimension_name) ? $dimensionDetail->v_dimension_name : '' ) . ' (' .(!empty($dimensionDetail->v_dimension_size) ? $dimensionDetail->v_dimension_size : '' ) . ')' .'</option>';
				}
			}
			echo $html;die;
		}
	}
	
	public function checkPalletLimit(Request $request){
		if (!empty($request->input())){
		
		    $validator = Validator::make($request->all(), [
		        'no_of_pallets_boxes' => [
		            'required',
		            new ValidatePalletLimit($request->input())
		        ],
		    ], [
		        'no_of_pallets_boxes.required' => __('messages.require-no-of-pallet-box'),
		    ]);
		
		    if ($validator->fails()) {
		        return response()->json([
		            'status_code' => 101,
		            'message' => $validator->errors()->first(),
		        ]);
		    }

		}
	}
	
}
