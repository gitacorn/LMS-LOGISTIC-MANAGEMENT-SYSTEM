<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CountrytoPortEuropeTransferModel;
use App\Login;
use App\LogisticPartnerMasterModel;
use App\StatusMasterModel;
use App\CompanyMasterModel;
use App\WarehouseMasterModel;
use App\Document_Type_Master_Model;
use App\CurrencyMasterModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use DB;
use App\LogisticPartnerDetailModel;
use Illuminate\Support\Facades\Response;
class EuropeInternalTransferController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.GOODS_OUT_EUROPE_TRANSFER_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'europe-internal-transfer/';
		$this->moduleName = trans('messages.internal-transfer');
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.EUROPE_INTERNAL_TRANSFER_MASTER_URL');
		$this->crudModel = new CountrytoPortEuropeTransferModel();
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_GOODS_OUT_INTERNAL_TRANSFER')) != true){
			return redirect('access-denied');
		}
		$data = $where = [];
		$data ['pageTitle'] = trans('messages.internal-transfer');
	
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->get();
		$data['companyDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
		
		$statusIds = [];
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		$where['default_status'] = $statusIds;
		
		$userLogin = (session()->has('user_id') ? session()->get('user_id') : 0);
		if ($userLogin > 0){
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
			$warehouseId = '';
			if (count($userLoginDetails) > 0){
				if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
					$warehouseId = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0);
				}
			}
			if (!empty($warehouseId)){
				$where['warehouse_id'] = $warehouseId;
				$data['warehouseId'] = $warehouseId;
			}
		}
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		$data['statusInfo'] = $statusIds;
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
	
		return view($this->folderName . 'europe-internal-transfer')->with($data);
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		if (!empty($request->post('search_by_internal_transfer'))) {
			$searchByName = trim($request->post('search_by_internal_transfer'));
			$likeData ['searchBy'] = $searchByName;
		}
		if(!empty($request->post('search_way_of_transport'))){
			$whereData['way_of_transport'] = ( $request->post('search_way_of_transport') );
		}
		if(!empty($request->post('search_book_by'))){
			$whereData['book_by'] = (int)Wild_tiger::decode($request->post('search_book_by'));
		}
		if(!empty($request->post('search_from_warehouse'))){
			$whereData['from_warehouse'] = (int)Wild_tiger::decode($request->post('search_from_warehouse'));
		}
		if(!empty($request->post('search_to_warehouse'))){
			$whereData['to_warehouse'] = (int)Wild_tiger::decode($request->post('search_to_warehouse'));
		}
		if(!empty($request->post('search_logistic_partner'))){
			$whereData['logistic_partner'] = (int)Wild_tiger::decode($request->post('search_logistic_partner'));
		}
		if( !empty($request->post('search_booking_from_date') )){
			$whereData['booking_form_date'] = ($request->post('search_booking_from_date'));
		}
		if(!empty($request->post('search_booking_to_date'))){
			$whereData['booking_to_date'] = ($request->post('search_booking_to_date'));
		}
		if(!empty($request->post('search_collection_from_date'))){
			$whereData['collection_form_date'] = ($request->post('search_collection_from_date'));
		}
		if(!empty($request->post('search_collection_to_date') )){
			$whereData['collection_to_date'] = ($request->post('search_collection_to_date'));
		}
		if(!empty($request->post('search_delivery_from_date'))){
			$whereData['delivery_from_date'] = ($request->post('search_delivery_from_date'));
		}
		if(!empty($request->post('search_delivery_to_date') )){
			$whereData['delivery_to_date'] = ($request->post('search_delivery_to_date'));
		}
		/* if(!empty($request->post('search_status') )){
			$whereData['status'] = (int)Wild_tiger::decode($request->post('search_status'));
		} */
		if(!empty($request->post('search_account_company'))){
			$whereData['account_company'] = (int)Wild_tiger::decode($request->post('search_account_company'));
		}
		/* if(!empty($request->post('search_from_warehouse') ) ){
			$whereData['from_warehouse'] = (int)Wild_tiger::decode($request->post('search_from_warehouse'));
		}
		if(!empty($request->post('search_to_warehouse'))){
			$whereData['to_amazon_location'] = (int)Wild_tiger::decode($request->post('search_to_warehouse'));
		} */
		if( !empty($request->post('search_status') )){
			$allStatusIds = explode("," , $request->post('search_status') );
			if(!empty($allStatusIds)){
				$allStatusIds = array_map(function($allStatusId){
					return (int)Wild_tiger::decode($allStatusId);
				}, $allStatusIds);
			}
			if(!empty($allStatusIds)){
				$whereData['status'] =  $allStatusIds;
			}
		}
		
		$userLogin = (session()->has('user_id') ? session()->get('user_id') : 0);
		if ($userLogin > 0){
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
			$warehouseId = '';
			if (count($userLoginDetails) > 0){
				if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
					$warehouseId = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0);
				}
			}
			if (!empty($warehouseId)){
				$whereData['warehouse_id'] = $warehouseId;
			}
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		
		if ($exportAction == 'export') {
			$finalExportData = [];
			$whereData['count_record'] = true;
			$getExportRecordDetails = $this->crudModel->getRecordDetails( $whereData, $likeData );
		
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					$invoiceReference = ( isset($getExportRecordDetail->detailInfo) ? json_decode(json_encode($getExportRecordDetail->detailInfo),true) : [] );
					$invoiceReferenceNo = (isset($invoiceReference) ? array_column($invoiceReference, 'v_invoice_ref_no') : []);
					$invoiceNoReferenceNo = (isset($invoiceReferenceNo) ? implode(', ', $invoiceReferenceNo) : '');
					
					$allInvoiceDetails = ( isset($getExportRecordDetail->invoiceInfo) ? json_decode(json_encode($getExportRecordDetail->invoiceInfo),true) : [] );
					$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
					$paymentValue = $finalCharge;
					$invoiceReference = ( isset($getExportRecordDetail->detailInfo) ? json_decode(json_encode($getExportRecordDetail->detailInfo),true) : [] );
					$transferDetail = (!empty($invoiceReference[0]) ? $invoiceReference[0] : '');
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_europe_transfer_record_no) ?  ($getExportRecordDetail->v_europe_transfer_record_no) :'' );
					$rowExcelData['way_of_transport'] = (isset($getExportRecordDetail->e_transport_way) ? ($getExportRecordDetail->e_transport_way) : '' );
					$rowExcelData['book_by'] =  (isset($getExportRecordDetail->bookEmployeeInfo->v_name) ? $getExportRecordDetail->bookEmployeeInfo->v_name :'');
					$rowExcelData['logistic_partner'] = (isset($getExportRecordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name . ( isset($getExportRecordDetail->logisticPartnerDetail->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->logisticPartnerDetail->v_logistic_partner_code.')'  : '' ): '' );
					$rowExcelData['booking_date'] = (isset($getExportRecordDetail->dt_booking_date) ?  clientDate($getExportRecordDetail->dt_booking_date) : '' );
					$rowExcelData['no._of_pallets'] = ( isset($getExportRecordDetail->i_no_of_pallets) ?  ($getExportRecordDetail->i_no_of_pallets) :'' );
					$rowExcelData['invoice_no_/_ref._no'] = ( isset($invoiceNoReferenceNo) ?  ($invoiceNoReferenceNo) :'' );
					$rowExcelData['tracking_no.'] = (isset($getExportRecordDetail->v_tracking_no) ?  ( $getExportRecordDetail->v_tracking_no )  : '' );
					$rowExcelData['tracking_link'] = (isset($getExportRecordDetail->v_tracking_link) ?  ( $getExportRecordDetail->v_tracking_link )  : '' );
					$rowExcelData['account'] = ( isset($transferDetail['account_company']['v_company_name']) ?  ( $transferDetail['account_company']['v_company_name'] ) : '' );
					$rowExcelData['from_(_warehouse_)'] = ( isset($transferDetail['warehouse']['v_warehouse_name']) ?  ( $transferDetail['warehouse']['v_warehouse_name'] ) .(!empty($transferDetail['warehouse']['v_warehouse_code']) ? ' (' .$transferDetail['warehouse']['v_warehouse_code']. ')'  :'') : '' );
					$rowExcelData['to_(_warehouse_)'] = ( isset($transferDetail['location']['v_warehouse_name']) ?  ( $transferDetail['location']['v_warehouse_name'] ) .(!empty($transferDetail['location']['v_warehouse_code']) ? ' (' .$transferDetail['location']['v_warehouse_code']. ')'  :''): '' );
					$rowExcelData['collection_date'] = (isset($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) : '' );
					$rowExcelData['delivery_date'] = (isset($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '' );
					$rowExcelData['status'] = (isset($getExportRecordDetail->statusInfo->v_status) ? ($getExportRecordDetail->statusInfo->v_status) : '');
					$rowExcelData['total_logistic_cost_('.config('constants.GOODS_OUT_GBP_CURRENCY').')'] = (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_GBP_CURRENCY')  : '' );
					$finalExportData[] = $rowExcelData;
				}
			}
		
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.internal-transfer')]);
				
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => $fileName ]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
		
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
		
			return Response::json($response);
			die;
		}
		$paginationData = [];
	
		$whereData['page'] = $page;
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData, $likeData );
	
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
	
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'europe-internal-transfer/europe-internal-transfer-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function create() {
		if(checkPermission(config('permission_constants.ADD_GOODS_OUT_INTERNAL_TRANSFER')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-internal-transfer');
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->where('t_is_active',1)->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		
		$userLogin = (session()->has('user_id') ? session()->get('user_id') : 0);
		if ($userLogin > 0){
			$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
			$warehouseId = '';
			if (count($userLoginDetails) > 0){
				if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
					$warehouseId = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0);
				}
			}
			if (!empty($warehouseId)){
				$data['warehouseId'] = $warehouseId;
			}
		}
		
		$data['comapnyMasterDetails'] = CompanyMasterModel::where('t_is_active',1)->orderBy('v_company_name', 'ASC')->get();
		$data['warehouseMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->where('t_is_active',1)->orderBy('v_document_type_name', 'ASC')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::where('t_is_active',1)->orderBy('i_sequence', 'ASC')->get();
		
		$whareData = 1;
		$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whareData){
											$query->where('t_is_active',$whareData);
										})->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();						
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		return view ( $this->folderName . 'add-europe-internal-transfer' )->with ( $data );
	}
	public function add(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_INTERNAL_TRANSFER')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_GOODS_OUT_INTERNAL_TRANSFER')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['way_of_transport'] = 'required';
		$formValidation['book_by'] = 'required';
		$formValidation['logistic_partner'] = 'required';
		$formValidation['booking_date'] = 'required';
		$formValidation['from_warehouse'] = 'required';
		$formValidation['to_warehouse'] = 'required';
		$formValidation['tracking_no'] = 'required';
		$formValidation['status'] = 'required';
		
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			$formValidation['collection_date'] = 'required';
			$formValidation['delivery_date'] = 'required';
		}
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'way_of_transport.required' => __ ( 'messages.require-way-of-transport' ),
				'book_by.required' => __ ( 'messages.require-book-by' ),
				'logistic_partner.required' => __ ( 'messages.require-logistic-partner' ),
				'booking_date.required' => __ ( 'messages.require-booking-date' ),
				'from_warehouse.required' => __ ( 'messages.require-from-warehouse' ),
				'to_warehouse.required' => __ ( 'messages.require-to-warehouse' ),
				'tracking_no.required' => __ ( 'messages.require-tracking-no' ),
				'status.required' => __ ( 'messages.require-status' ),
				'collection_date.required' => __ ( 'messages.require-collection-date' ),
				'delivery_date.required' => __ ( 'messages.require-delivery-date' ),
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$result = false;
		$successMessage =  trans('messages.success-create',[ 'module' => $this->moduleName ]);
		$errorMessages = trans('messages.error-create',[ 'module' => $this->moduleName ]);
		
		$europeInternalTransferCount = (!empty($request->input('europe_internal_transfer_count')) ? (int)($request->input('europe_internal_transfer_count')) : 1 );
		$europeInternalDocumentTypeCount = (!empty($request->input('europe_internal_document_type_count')) ? (int)($request->input('europe_internal_document_type_count')) : 1 );
		$europeInternalTransporterCount = (!empty($request->input('europe_internal_transporter_count')) ? (int)($request->input('europe_internal_transporter_count')) : 1 );
		 
		DB::beginTransaction();
		try{
			$recordData = [];
			$recordData['e_transport_way'] = (!empty($request->input('way_of_transport')) ? ($request->input('way_of_transport')) : '' );
			$recordData['i_book_by_employee_id'] = (!empty($request->input('book_by')) ? (int)Wild_tiger::decode($request->input('book_by')) : 0 );
			$recordData['i_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner')) ? (int)Wild_tiger::decode($request->input('logistic_partner')) : 0 );
			$recordData['dt_booking_date'] = (!empty($request->input('booking_date')) ? dbDate($request->input('booking_date')) : '' );
			$recordData['v_reference_no'] = (!empty($request->input('reference_no')) ? ($request->input('reference_no')) : null );
			$recordData['i_no_of_pallets'] = (!empty($request->input('no_of_pallets')) ? ($request->input('no_of_pallets')) : null );
			$recordData['d_weight'] = (!empty($request->input('weight')) ? ($request->input('weight')) : null );
			$recordData['i_from_warehouse_id'] = (!empty($request->input('from_warehouse')) ? (int)Wild_tiger::decode($request->input('from_warehouse')) : 0);
			$recordData['i_to_warehouse_id'] = (!empty($request->input('to_warehouse')) ? (int)Wild_tiger::decode($request->input('to_warehouse')) : 0);
			$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? ($request->input('tracking_no')) : '' );
			$recordData['v_tracking_link'] = (!empty($request->input('tracking_link')) ? ($request->input('tracking_link')) : null );
			$recordData['dt_collection_date'] = (!empty($request->input('collection_date')) ? dbDate($request->input('collection_date')) : null );
			$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
			$recordData['i_status_id'] = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
			$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? ($request->input('status_comments')) : null );
			
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName ]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName ]);
				$whereData = [];
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
				$europeInternalTransferRecordDetails = $this->crudModel->getRecordDetails($whereData);
				$europeInternalTransferDetails = (!empty($europeInternalTransferRecordDetails) ? $europeInternalTransferRecordDetails[0] : []);
				
				if(!empty($europeInternalTransferDetails->detailInfo)){
					foreach ($europeInternalTransferDetails->detailInfo as $europeInternalTransferDetail){
						$europeInternalTransferDetailtId = $europeInternalTransferDetail->i_id;
						if(!empty($request->input('edit_invoice_no_ref_no_'.$europeInternalTransferDetailtId))){
							$europeInternalTransferRow = [];
							$europeInternalTransferRow['v_invoice_ref_no'] = (!empty($request->input('edit_invoice_no_ref_no_'.$europeInternalTransferDetailtId)) ? $request->input('edit_invoice_no_ref_no_'.$europeInternalTransferDetailtId) : '');
							$europeInternalTransferRow['i_account_company_id'] = (!empty($request->input('edit_account_'.$europeInternalTransferDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_account_'.$europeInternalTransferDetailtId)) : 0);
							//$europeInternalTransferRow['i_warehouse_id'] = (!empty($request->input('edit_from_warehouse_'.$europeInternalTransferDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_from_warehouse_'.$europeInternalTransferDetailtId)) : 0);
							//$europeInternalTransferRow['i_location_id'] = (!empty($request->input('edit_to_warehouse_'.$europeInternalTransferDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_to_warehouse_'.$europeInternalTransferDetailtId)) : 0);
							$europeInternalTransferRow['v_units'] = (!empty($request->input('edit_unit_'.$europeInternalTransferDetailtId)) ? $request->input('edit_unit_'.$europeInternalTransferDetailtId) : '');
							$europeInternalTransferRow['v_price'] = (!empty($request->input('edit_price_'.$europeInternalTransferDetailtId)) ? $request->input('edit_price_'.$europeInternalTransferDetailtId) : '');
							if(!empty($europeInternalTransferRow['v_invoice_ref_no'])){
								$europeInternalTransferDetailUpdate = $this->crudModel->updateTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE') , $europeInternalTransferRow , [ 'i_id' => $europeInternalTransferDetailtId] );
							}
						} else{
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE') , $deleteRecordData , [ 'i_id' => $europeInternalTransferDetailtId] );
								
						}
					}
				}
				if(!empty($europeInternalTransferDetails->documentInfo)){
					foreach ($europeInternalTransferDetails->documentInfo as $europeInternalDocumentDetail){
						$europeInternalDocumentId = $europeInternalDocumentDetail->i_id;
						if(!empty($request->input('edit_type_'.$europeInternalDocumentId))){
							$europeInternalDocument = [];
							$europeInternalDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$europeInternalDocumentId)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$europeInternalDocumentId)) :0);
							$europeInternalDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$europeInternalDocumentId)) ? $request->input('edit_remarks_'.$europeInternalDocumentId) : null);
				
							if($request->hasFile('edit_file_'.$europeInternalDocumentId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$europeInternalDocumentId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$europeInternalDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
								$removeFiles = (!empty($request->input('remove_document_'.$europeInternalDocumentId)) ? explode("," , $request->input('remove_document_'.$europeInternalDocumentId) ) : []  );
								$previousUploadFiles = (!empty($europeInternalDocumentDetail->v_document_file_path) ? json_decode($europeInternalDocumentDetail->v_document_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$europeInternalDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if((!empty($europeInternalDocument ['i_document_type_id']))){
								$europeInternalDocumentDetailUpdate = $this->crudModel->updateTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DOCUMENT_MASTER_TABLE') , $europeInternalDocument , [ 'i_id' => $europeInternalDocumentId] );
				
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DOCUMENT_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $europeInternalDocumentId] );
						}
					}
				}
				if(!empty($europeInternalTransferDetails->invoiceInfo)){
					foreach ($europeInternalTransferDetails->invoiceInfo as $europeInternalInvoiceDetail){
				
						$europeInternalInvoiceRecordId = $europeInternalInvoiceDetail->i_id;
						if(!empty($request->input('edit_name_'.$europeInternalInvoiceRecordId))){
							$europeInternalInvoice = [];
							$europeInternalInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$europeInternalInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$europeInternalInvoiceRecordId)) :0 );
							$europeInternalInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_inv_no_'.$europeInternalInvoiceRecordId) :'' );
							$europeInternalInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_freight_'.$europeInternalInvoiceRecordId) : 0 );
							$europeInternalInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_custom_'.$europeInternalInvoiceRecordId) :0 );
							$europeInternalInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_duty_'.$europeInternalInvoiceRecordId)  : 0 );
							$europeInternalInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_other_'.$europeInternalInvoiceRecordId) :0 );
							$europeInternalInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_vat_'.$europeInternalInvoiceRecordId) : 0 );
							$totalCharges = $europeInternalInvoice['d_freight_charge'] + $europeInternalInvoice['d_custom_charge'] + $europeInternalInvoice['d_duty_charge'] + $europeInternalInvoice['d_other_charge'] + $europeInternalInvoice['d_vat_charge'];
							$europeInternalInvoice['d_total_charge'] = $totalCharges;
							$europeInternalInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_currency_id_'.$europeInternalInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_currency_id_'.$europeInternalInvoiceRecordId)) : 0);
							$europeInternalInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$europeInternalInvoiceRecordId)) ? $request->input('edit_cov_rate_'.$europeInternalInvoiceRecordId) : 0);
							$finalCharges = ($totalCharges * $europeInternalInvoice['d_conversion_rate']);
							$europeInternalInvoice['d_final_charge'] = $finalCharges;
				
							if($request->hasFile('edit_invoice_file_'.$europeInternalInvoiceRecordId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$europeInternalInvoiceRecordId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$europeInternalInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
				
								$removeFiles = (!empty($request->input('remove_invoice_'.$europeInternalInvoiceRecordId)) ? explode("," , $request->input('remove_invoice_'.$europeInternalInvoiceRecordId) ) : []  );
								$previousUploadFiles = (!empty($europeInternalInvoiceDetail->v_invoice_file_path) ? json_decode($europeInternalInvoiceDetail->v_invoice_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$europeInternalInvoice['v_invoice_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if(( $europeInternalInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($europeInternalInvoice['v_invoice_no']) ) ){
								$europeInternalInvoiceUpdate = $this->crudModel->updateTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE') , $europeInternalInvoice , [ 'i_id' => $europeInternalInvoiceRecordId] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $europeInternalInvoiceRecordId] );
						}
					}
				}
				$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
			} else {
				$internalTransferMasterRecordDetails = $this->crudModel->selectData(config('constants.GOODS_OUT_EUROPE_TRANSFER_MASTER_TABLE') ,['i_id']);
				$internalTransferMasterRecordCount = count($internalTransferMasterRecordDetails);
				$count = ( ( (!empty($internalTransferMasterRecordCount)) && ( $internalTransferMasterRecordCount > 0 ) ) ? ( $internalTransferMasterRecordCount + 1  ) : 1 );
				$generateNumber = threeNumberSeries($count);
				$europeTransferGenerateNo = config('constants.INTERNAL_WAREHOUSE_TRANSFER').'-'. $generateNumber.'-'.$this->todayDate;
				$recordData['v_europe_transfer_record_no'] = $europeTransferGenerateNo;
				
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
			}
			if( $insertRecord > 0 ){
				$result = true;
			}
			
			for ($i = 0; $i <= $europeInternalTransferCount; $i++){
				$rowData = [];
				$rowData['i_europe_transfer_master_id'] = $insertRecord;
				$rowData['v_invoice_ref_no'] = (!empty($request->input('invoice_no_ref_no_'.$i)) ? $request->input('invoice_no_ref_no_'.$i) : '');
				$rowData['i_account_company_id'] = (!empty($request->input('account_'.$i)) ? (int)Wild_tiger::decode($request->input('account_'.$i)) : 0);
				//$rowData['i_warehouse_id'] = (!empty($request->input('from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('from_warehouse_'.$i)) : 0);
				//$rowData['i_location_id'] = (!empty($request->input('to_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('to_warehouse_'.$i)) : 0);
				$rowData['v_units'] = (!empty($request->input('unit_'.$i)) ? $request->input('unit_'.$i) : '');
				$rowData['v_price'] = (!empty($request->input('price_'.$i)) ? $request->input('price_'.$i) : '');
				if(!empty($rowData['v_invoice_ref_no'])){
					$insertInternalTransferDetail = $this->crudModel->insertTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE') , $rowData);
					
					$fbaSheetData = [];
					$fbaSheetData['v_shipment_no'] = $rowData['v_invoice_ref_no'];
					$fbaSheetData['i_ref_table_id'] = $insertInternalTransferDetail;
					$fbaSheetData['v_ref_record_type'] = config('constants.INTERNAL_WAREHOUSE_TRANSFER');
					$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
						
				}
			}
			
			
			for ($i = 0; $i <= $europeInternalDocumentTypeCount; $i++){
				$rowData = [];
				$rowData['i_europe_transfer_master_id'] = $insertRecord;
				$rowData['i_document_type_id'] = (!empty($request->input('type_'.$i)) ? (int)Wild_tiger::decode($request->input('type_'.$i)) :0);
				$rowData['v_document_remark'] = (!empty($request->input('remarks_'.$i)) ? $request->input('remarks_'.$i) : null);
				$rowData['v_document_file_path'] = '';
					
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
					$insertDocumentDetail = $this->crudModel->insertTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DOCUMENT_MASTER_TABLE') , $rowData);
				}
					
			}
			for ($i = 0; $i <= $europeInternalTransporterCount;$i++){
				$rowData = [];
				$rowData['i_europe_transfer_master_id'] = $insertRecord;
				$rowData['i_logistic_partner_master_id'] = (!empty($request->input('name_'.$i)) ? (int)Wild_tiger::decode($request->input('name_'.$i)) : 0);
				$rowData['v_invoice_no'] = (!empty($request->input('inv_no_'.$i)) ? $request->input('inv_no_'.$i) : '');
				$rowData['d_freight_charge'] = (!empty($request->input('freight_'.$i)) ? ($request->input('freight_'.$i)) : null );
				$rowData['d_custom_charge'] = (!empty($request->input('custom_'.$i)) ? ($request->input('custom_'.$i)) : null );
				$rowData['d_duty_charge'] = (!empty($request->input('duty_'.$i)) ? ($request->input('duty_'.$i)) : null );
				$rowData['d_other_charge'] = (!empty($request->input('other_'.$i)) ? ($request->input('other_'.$i)) : null );
				$rowData['d_vat_charge'] = (!empty($request->input('vat_'.$i)) ? ($request->input('vat_'.$i)) : null );
				$rowData['i_invoice_currency_id'] = (!empty($request->input('currency_id_'.$i)) ? (int)Wild_tiger::decode($request->input('currency_id_'.$i)) : 0 );
				$rowData['d_conversion_rate'] = (!empty($request->input('cov_rate_'.$i)) ? ($request->input('cov_rate_'.$i)) : null );
				$totalValue = $rowData['d_freight_charge'] + $rowData['d_custom_charge'] + $rowData['d_duty_charge'] + $rowData['d_other_charge'] + $rowData['d_vat_charge'];
				$rowData['d_total_charge'] = $totalValue;
				$totalCharges = $totalValue * $rowData['d_conversion_rate'];
				$rowData['d_final_charge'] = $totalCharges;
				$rowData['v_invoice_file_path'] = null;
					
				if($request->hasFile('invoice_file_'.$i)){
					$uploadFile = $this->uploadMultipleFile($request, 'invoice_file_'.$i,'image_doc_pdf_xls');
					if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
						$rowData['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
					} else {
						DB::rollback();
						Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
						return redirect ( $this->redirectUrl );
					}
				}
					
				if(( $rowData['i_logistic_partner_master_id'] > 0 ) && (!empty($rowData['v_invoice_no']))){
					$insertTransporterInvoice = $this->crudModel->insertTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE') , $rowData);
				}
			}
		$result = true;
		}catch(\Exception $e){
			//var_dump($e->getMessage());die;
			DB::rollback();
			$result = false;
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
	
	public function edit($id = null){
		if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_INTERNAL_TRANSFER')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		if (!empty($id)){
			$recordId = (int) Wild_tiger::decode($id);
			if( $recordId > 0 ){
				$whereData = $data = [];
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
				$data['pageTitle'] = trans('messages.update-internal-transfer');
					
				$userLogin = (session()->has('user_id') ? session()->get('user_id') : 0);
				if ($userLogin > 0){
					$userLoginDetails = Login::where('t_is_active',1)->where('i_id',$userLogin)->where('v_role',config ( 'constants.ROLE_USER'))->orderBy('v_name', 'ASC')->get();
					$warehouseId = '';
					if (count($userLoginDetails) > 0){
						if( (!empty($userLoginDetails[0]->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails[0]->v_record_type ) ) ) ){
							$warehouseId = (!empty($userLoginDetails[0]->i_warehouse_id) ? $userLoginDetails[0]->i_warehouse_id : 0);
						}
					}
					if (!empty($warehouseId)){
						$data['warehouseId'] = $warehouseId;
						$whereData['warehouse_id'] = $warehouseId;
					}
				}
			
				$recordInfo = $this->crudModel->getRecordDetails($whereData);
			
				if(count($recordInfo) > 0){
					$errorFound = false;
					$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
					$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
					//$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
					$data['comapnyMasterDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
					$data['warehouseMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
					$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
					$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
					$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
					$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->get();
					$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
			
					$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
					$disableForm = '';
					$documentForm = '';
					$statusDisableForm = '';
					if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
						$data ['pageTitle'] = trans('messages.view-internal-transfer');
						$disableForm = 'disabled';
						$documentForm = 'disabled';
						$statusDisableForm = 'disabled';
					}
			
					if( isset($data ['recordInfo']->i_status_id) && (  in_array( $data ['recordInfo']->i_status_id , [ config('constants.DELIVERED_STATUS_ID')  ] ) ) ){
						if(empty($documentForm) && ( session()->get('role') == config('constants.ROLE_ADMIN') ) ){
							$statusDisableForm = '';
						} else {
							$statusDisableForm = 'disabled';
						}
						$disableForm = 'disabled';
						$documentForm = 'disabled';
					}
			
					if( isset($data ['recordInfo']->i_status_id) && (  in_array( $data ['recordInfo']->i_status_id , [ config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') ] ) ) ){
						$disableForm = 'disabled';
					}
			
					$data['disableForm'] = $disableForm;
					$data['documentForm'] = $documentForm;
					$data['statusDisableForm'] = $statusDisableForm;
			
				}
			}
		}
		
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
		return view ( $this->folderName . 'add-europe-internal-transfer' )->with ( $data );
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_INTERNAL_TRANSFER')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$europeInternalDetailData['t_is_active'] = 0;
			$europeInternalDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-delete',['module'=> $this->moduleName]);
	
			DB::beginTransaction();
	
			$result = false;
			$whereData = [];
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			#get record
			$europeInternalRecordDetails = $this->crudModel->getRecordDetails($whereData);
			
			try{
				$this->crudModel->deleteTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE') ,  $europeInternalDetailData , [ 'i_europe_transfer_master_id' => $recordId ] );
				$this->crudModel->deleteTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_DOCUMENT_MASTER_TABLE') ,  $europeInternalDetailData , [ 'i_europe_transfer_master_id' => $recordId ] );
				$this->crudModel->deleteTableData( config('constants.GOODS_OUT_EUROPE_TRANSFER_INVOICE_MASTER_TABLE') ,  $europeInternalDetailData , [ 'i_europe_transfer_master_id' => $recordId ] );
	
				$this->crudModel->deleteTableData($this->tableName,  $europeInternalDetailData , [ 'i_id' => $recordId ] );
	
				if(!empty($europeInternalRecordDetails)){
					#get column
					$europeInternalDetailDatailIds = ( isset($europeInternalRecordDetails->detailInfo) ?  array_column(objectToArray($europeInternalRecordDetails->detailInfo) , 'i_id') : []  );
						
					#delete Shipment Info table
					$europeInternalDetailData ['i_deleted_id'] = session()->get('user_id');
					$europeInternalDetailData ['dt_deleted_at'] = date('Y-m-d H:i:s');
					DB::table(config('constants.SHIPMENT_NO_INFO_TABLE'))->whereIn('i_ref_table_id', $europeInternalDetailDatailIds )->where('v_ref_record_type' ,config('constants.INTERNAL_WAREHOUSE_TRANSFER') )->update($europeInternalDetailData);
				}
				$result = true;
			}catch(\Exception $e){
					
			}
			if( $result != false ){
					
				DB::commit();
					
				Wild_tiger::setFlashMessage ( 'success', $successMessage );
					
				return redirect()->back();
			}
			else {
					
				DB::rollback();
					
				Wild_tiger::setFlashMessage ( 'danger',$errorMessages);
					
				return redirect()->back();
			}
		}
	}
	public function getFromWarehouseDetails(Request $request){
		if (!empty($request->input())){
			$bookById = (!empty($request->input('book_by_id')) ? (int)Wild_tiger::decode($request->input('book_by_id')) : 0);
			$warehouseAllocatedData = Login::select('i_warehouse_id')->where('i_id',$bookById)->where('t_is_active',1)->whereRaw("find_in_set('".config('constants.GOODS_IN_WAREHOUSE')."',v_record_type)")->first();
			
			$html = '<option value="">'.trans("messages.select").'</option>';
			
			$where = [];
			$where['e_record_type'] = config ('constants.WAREHOUSE');
			
			if (!empty($warehouseAllocatedData->i_warehouse_id)){
				$where['i_id'] = $warehouseAllocatedData->i_warehouse_id;
			}
			
			$warehouseDetails = WarehouseMasterModel::where($where)->orderBy('v_warehouse_name', 'ASC')->get();
			if (!empty($warehouseDetails)){
				foreach ($warehouseDetails as $warehouseDetail){
					$html .= '<option value="'.(!empty($warehouseDetail->i_id) ? Wild_tiger::encode($warehouseDetail->i_id) : 0).'">'.(!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ) : '' ).'</option>';
				}
			}
			echo $html;die;
		}
	}
}
