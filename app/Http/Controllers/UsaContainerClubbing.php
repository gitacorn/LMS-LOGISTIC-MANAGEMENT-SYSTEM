<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UsaContainerClubbingModel;
use App\Document_Type_Master_Model;
use App\LogisticPartnerMasterModel;
use App\CurrencyMasterModel;
use App\StatusMasterModel;
use App\LookupMaster;
use App\WarehouseMasterModel;
use Illuminate\Support\Facades\Log;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Validator;
use App\FBASheeteDetailModel;
use Illuminate\Support\Facades\DB;
use App\UsWarehouseToAmazonMasterModel;
use App\UsWarehouseToAmazonDetailsModel;

class UsaContainerClubbing extends MasterController
{
	public function __construct(){	
		parent::__construct();
		$this->crudModel = new UsaContainerClubbingModel();
		$this->tableName = config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE');
		$this->detailTableName = config('constants.USA_CONTAINER_CLUBBING_DETAIL_TABLE');
		$this->documentTableName = config('constants.USA_CONTAINER_CLUBBING_DOCUMENT_MASTER_TABLE');
		$this->invoiceTableName = config('constants.USA_CONTAINER_CLUBBING_INVOICE_MASTER_TABLE');
		$this->moduleName = trans('messages.usa-container-clubbing');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'usa-container-clubbing/';
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.USA_CONTAINER_CLUBBING_MASTER_URL');	
	}
	
	public function index(){
		
		if(checkPermission(config('permission_constants.VIEW_USA_CONTAINER_CLUBBING')) != true){
			return redirect('access-denied');
		}
		
		$data = $where = [];
		$data ['pageTitle'] = trans('messages.usa-container-clubbing');
			
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();			
		$data['fromWarehouseDetails'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['locationMasterCodeDetails'] = WarehouseMasterModel::where('e_record_type' , config('constants.LOCATION'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['bookingPortalDetails'] = LookupMaster::where('v_module_name',config('constants.BOOKING_PORTAL_LOOKUP'))->orderBy('v_value', 'ASC')->get();		
		
        // Exclude Delivered (Doc Pending), Delivered, Cancelled by default on initial load
        $statusIds = [
            config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'),
            config('constants.DELIVERED_STATUS_ID'),
            config('constants.STATIC_STATUS_CANCELLED_ID'),
        ];
        $where['exclude_status'] = $statusIds;
        
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
		
		return view($this->folderName . 'usa-container-clubbing')->with($data);		
	}
	
	public function filter(Request $request){
		
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		if (!empty($request->post('search_by'))) {
			$searchByName = trim($request->post('search_by'));
			$likeData ['searchBy'] = $searchByName;
		}
		if( ( !empty($request->post('search_from_warehouse') ) ) && ( $request->post('search_from_warehouse') ) ){
			$whereData['from_warehouse'] = (int) Wild_tiger::decode( trim( $request->post('search_from_warehouse') ) );
		}
		if( ( !empty($request->post('search_to_location') ) ) && ( $request->post('search_to_location') ) ){
			$whereData['to_location'] = (int) Wild_tiger::decode( trim( $request->post('search_to_location') ) );
		}
		if( ( !empty($request->post('search_booking_from_date') ) ) && ( $request->post('search_booking_from_date') ) ){
			$whereData['booking_from_date'] = dbDate( trim( $request->post('search_booking_from_date') ) );
		}		
		if( ( !empty($request->post('search_booking_to_date') ) ) && ( $request->post('search_booking_to_date') ) ){
			$whereData['booking_to_date'] = dbDate( trim( $request->post('search_booking_to_date') ) );
		}		
		if( ( !empty($request->post('search_booking_portal') ) ) && ( $request->post('search_booking_portal') ) ){
			$whereData['booking_portal'] = (int) Wild_tiger::decode( trim( $request->post('search_booking_portal') ) );
		}
		if( ( !empty($request->post('search_carrier_company') ) ) && ( $request->post('search_carrier_company') ) ){
			$whereData['carrier_company'] = (int) Wild_tiger::decode( trim( $request->post('search_carrier_company') ) );
		}
		if( ( !empty($request->post('search_collection_from_date') ) ) && ( $request->post('search_collection_from_date') ) ){
			$whereData['collection_from_date'] = dbDate( trim( $request->post('search_collection_from_date') ) );
		}
		if( ( !empty($request->post('search_collection_to_date') ) ) && ( $request->post('search_collection_to_date') ) ){
			$whereData['collection_to_date'] = dbDate( trim( $request->post('search_collection_to_date') ) );
		}
		if( ( !empty($request->post('search_delivery_from_date') ) ) && ( $request->post('search_delivery_from_date') ) ){
			$whereData['delivery_from_date'] = dbDate( trim( $request->post('search_delivery_from_date') ) );
		}
		if( ( !empty($request->post('search_delivery_to_date') ) ) && ( $request->post('search_delivery_to_date') ) ){
			$whereData['delivery_to_date'] = dbDate( trim( $request->post('search_delivery_to_date') ) );
		}
		if( ( !empty($request->post('search_status') ) ) && ( $request->post('search_status') ) ){
			$whereData['status'] = (int) Wild_tiger::decode( trim( $request->post('search_status') ) );
		} else {
            // Keep default exclusion on pagination or filter loads when no explicit status is chosen
            $whereData['exclude_status'] = [
                config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'),
                config('constants.DELIVERED_STATUS_ID'),
                config('constants.STATIC_STATUS_CANCELLED_ID'),
            ];
        }
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		
		if ($exportAction == 'export') {
			$finalExportData = [];
			$whereData['count_record'] = true;
			$getExportRecordDetails = $this->crudModel->getRecordDetails( $whereData, $likeData );
		
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$finalExportData[] = $rowExcelData;
				}
			}
		
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.usa-container-clubbing')]);
				$fileName = str_replace("/", "-", $fileName);
		
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'usa-container-clubbing/usa-container-clubbing-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	
	public function create(){
		if(checkPermission(config('permission_constants.ADD_USA_CONTAINER_CLUBBING')) != true){
			$this->ajaxResponse(101, trans('messages.access-denied'));
		}
		
		$data = [];
		$data ['pageTitle'] = trans('messages.add-usa-container-clubbing');
		
		$commonFormDetails = $this->commonFormDetails(config('constants.ACTIVE_STATUS'));
		$data = array_merge($data, $commonFormDetails);
		
		return view($this->folderName . 'add-usa-container-clubbing')->with($data);
	}
	
	public function edit($recordId = null){
		$data = [];
		
		$errorFound = true;
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			
			if($recordId > 0) {
				if(checkPermission(config('permission_constants.EDIT_USA_CONTAINER_CLUBBING')) != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			} else {
				if(checkPermission(config('permission_constants.ADD_USA_CONTAINER_CLUBBING')) != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			}
				
			if( $recordId > 0 ){
				$whereData = [];
				$whereData['edit_record'] = true;
				$whereData['master_id'] = $recordId;
				$recordInfo = $this->crudModel->getRecordDetails ( $whereData );
		
				if(!empty($recordInfo) && count($recordInfo) > 0){
					$errorFound = false;
						
					$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
					$data ['pageTitle'] = trans('messages.update-usa-container-clubbing');
					
					$commonFormDetails = $this->commonFormDetails();
					$data = array_merge($data, $commonFormDetails);
					
					$disableForm = '';
					$documentForm = '';
					$statusDisableForm = '';
					if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
						$data ['pageTitle'] = trans('messages.view-usa-container-clubbing');
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
						
					return view($this->folderName . 'add-usa-container-clubbing')->with($data);
				}
			}
		}
		
		if( $errorFound != false ){
			return redirect(config('constants.404_PAGE'));
		}
	}
	
	protected function commonFormDetails($status = null){
		$activeCheck = (isset($status) && !empty($status) && $status == config('constants.ACTIVE_STATUS') ? true : false);
		
		$data = [];
		
		$data ['typeDetails'] = shipmentTypeDetails();
		
		$fromWarehouseQuery = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'));
		
		if($activeCheck != false){
			$fromWarehouseQuery->where('t_is_active',1);
		}
			
		$data['fromWarehouseDetails'] = $fromWarehouseQuery->orderBy('v_warehouse_name', 'ASC')->get();
		
		$locationMasterCodeQuery = WarehouseMasterModel::where('e_record_type' , config('constants.LOCATION'));
		
		if($activeCheck != false){
			$locationMasterCodeQuery->where('t_is_active',1);
		}
			
		$data['locationMasterCodeDetails'] = $locationMasterCodeQuery->orderBy('v_warehouse_name', 'ASC')->get();
		
		$data ['boxPalletDetails'] = typeInfo();
		
		$documentTypeRecordQuery = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'));
		
		if($activeCheck != false){
			$documentTypeRecordQuery->where('t_is_active',1);
		}
		
		$data['documentTypeRecordDetails'] = $documentTypeRecordQuery->orderBy('v_document_type_name', 'ASC')->get();
		
		$logisticPartnerQuery = LogisticPartnerMasterModel::query();
		
		if($activeCheck != false){
			$logisticPartnerQuery->where('t_is_active',1);
		}
			
		$data['logisticPartnerDetails'] = $logisticPartnerQuery->orderBy('v_logistic_partner_name', 'ASC')->get();
		
		$currencyRecordQuery = CurrencyMasterModel::query();
		
		if($activeCheck != false){
			$currencyRecordQuery->where('t_is_active',1);
		}
			
		$data['currencyRecordDetails'] = $currencyRecordQuery->orderBy('v_currency_name', 'ASC')->get();
		
		$statusMasterRecordQuery = StatusMasterModel::query();
		
		if($activeCheck != false){
			$statusMasterRecordQuery->where('t_is_active',1);
		}
			
		$data['statusMasterRecordDetails'] = $statusMasterRecordQuery->orderBy('i_sequence', 'ASC')->get();
		
		$bookingPortalQuery = LookupMaster::where('v_module_name',config('constants.BOOKING_PORTAL_LOOKUP'));
		
		if($activeCheck != false){
			$bookingPortalQuery->where('t_is_active',1);
		}
			
		$data['bookingPortalDetails'] = $bookingPortalQuery->orderBy('v_value', 'ASC')->get();
		
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		
		return $data;
	}
	
	public function add(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		if($recordId > 0) {
			if(checkPermission(config('permission_constants.EDIT_USA_CONTAINER_CLUBBING')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_USA_CONTAINER_CLUBBING')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		}
		
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		
		$formValidation = [];
		$formValidation['type'] = 'required';
		$formValidation['from_warehouse'] = 'required';
		$formValidation['to_location'] = 'required';
		$formValidation['box_pallet'] = 'required';
		$formValidation['total_box'] = 'required';
		$formValidation['total_pallet'] = 'required';
		$formValidation['booking_date'] = 'required';		
		$formValidation['carrier_company'] = 'required';
		$formValidation['logistic_cost_usd'] = 'required';
		$formValidation['collection_date'] = 'required';
		$formValidation['status'] = 'required';
		
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			$formValidation['delivery_date'] = 'required';
		}
		
		$validator = Validator::make ( $request->all (), $formValidation , [
			'type.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.type')]),
			'from_warehouse.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.from-warehouse')]),
			'to_location.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.to-location')]),
			'box_pallet.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.box-pallet')]),
			'total_box.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.total-box')]),
			'total_pallet.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.total-pallet')]),
			'booking_date.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.booking-date')]),
			'status.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.status')]),
			'carrier_company.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.carrier-company')]),
			'logistic_cost_usd.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.logistic-cost-usd')]),
			'collection_date.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.collection-date')]),
			'delivery_date.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.delivery-date')])
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
		$errorMessages = trans('messages.error-create',['module'=> $this->moduleName ]);
		
		$recordData = $containerId = $selectedFbaId = [];
		$documentTypeCount = (!empty($request->input('usa_container_clubbing_document_type_count')) ? (int)($request->input('usa_container_clubbing_document_type_count')) : 1 );
		$transporterCount = (!empty($request->input('usa_container_clubbing_transporter_count')) ? (int)($request->input('usa_container_clubbing_transporter_count')) : 1 );
		$usaContainerCount = (!empty($request->input('usa_container_checked_row_number')) ? (int)($request->input('usa_container_checked_row_number')) : 1 );
			
		$selectedFbaRecordsIds = (!empty($request->input('checkbox')) ? ($request->input('checkbox')) :'');
		
		if(!empty($selectedFbaRecordsIds)){
			foreach($selectedFbaRecordsIds as $selectedFbaRecordsId){
				$selectedFbaId[] = (int)Wild_tiger::decode($selectedFbaRecordsId);
			}	
		}
		
		$usaContainerClubbingType = (!empty($request->input('type')) ? ($request->input('type')) : null );
		$recordData['e_type'] = (!empty($usaContainerClubbingType) ? ($usaContainerClubbingType) : null );
		$recordData['i_from_warehouse_id'] = (!empty($request->input('from_warehouse')) ? (int) Wild_tiger::decode($request->input('from_warehouse')) : null );
		$recordData['i_to_location_id'] = (!empty($request->input('to_location')) ? (int) Wild_tiger::decode($request->input('to_location')) : null );
		$recordData['v_box_pallet_type'] = (!empty($request->input('box_pallet')) ? implode(',', $request->input('box_pallet')) : null );
		
		$recordData['dt_booking_date'] = (!empty($request->input('booking_date')) ? dbDate($request->input('booking_date')) : null );
		$recordData['i_booking_portal_id'] = (!empty($request->input('booking_portal')) ? (int) Wild_tiger::decode($request->input('booking_portal')) : null );
		$recordData['i_carrier_company_id'] = (!empty($request->input('carrier_company')) ? (int) Wild_tiger::decode($request->input('carrier_company')) : null );
		$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? trim($request->input('tracking_no')) : null );
		$recordData['v_pro_number'] = (!empty($request->input('pro_number')) ? trim($request->input('pro_number')) : null );
		
		$recordData['d_logistic_cost_in_usd'] = (!empty($request->input('logistic_cost_usd')) ? ($request->input('logistic_cost_usd')) : null );
		
		$recordData['dt_collection_date'] = (!empty($request->input('collection_date')) ? dbDate($request->input('collection_date')) : null );
		$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
		$recordData['d_weight'] = (!empty($request->input('weight_lbs')) ? trim($request->input('weight_lbs')) : null );
		$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? trim($request->input('status_comments')) : null );
		$recordData['i_status_id'] = (!empty($request->input('status')) ? (int) Wild_tiger::decode($request->input('status')) : null );
		
		$usaContainerInsertTableData = $usaContainerUpdateTableData = $containerFBAType = $containerNotFBAType = [];
		$finalBoxPalletType = '';
		
		if(isset($recordId) && !empty($recordId)){
			$whereData = [];
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			$usaContainerClubbingRecordDetails = $this->crudModel->getRecordDetails($whereData);
			
			$usaContainerClubbingDetails = (!empty($usaContainerClubbingRecordDetails) ? $usaContainerClubbingRecordDetails[0] : []);
		}	
		
		$previousSelectedUsaContainerClubbingFBARecords = ( (isset($usaContainerClubbingDetails) && (!empty($usaContainerClubbingDetails->v_fba_sheet_ids))) ? explode(',', $usaContainerClubbingDetails->v_fba_sheet_ids) : [] );
		$previousSelectedUsaContainerClubbingNotFBARecords = ( (isset($usaContainerClubbingDetails) && (!empty($usaContainerClubbingDetails->v_usa_warehouse_container_ids))) ? explode(',', $usaContainerClubbingDetails->v_usa_warehouse_container_ids) : [] );
			
		for ($i = 0; $i <= $usaContainerCount; $i++){
			$containerId = (!empty($request->input('checkbox_'.$i)) ? (int)Wild_tiger::decode($request->input('checkbox_'.$i)) : 0);
				
			$recordType = (!empty($request->input('container_type_'.$i)) ? trim($request->input('container_type_'.$i)) : '');
			$recordIdArray = (!empty($request->input('checkbox_'.$i)) ? explode(',', $request->input('checkbox_'.$i)) : []);
			
			$encodedRecordIds = [];
				
			$encodedRecordIds = array_map(function($recordId) {
				return (int) Wild_tiger::decode($recordId);
			}, $recordIdArray);
			
			foreach ($encodedRecordIds as $encodedRecordId){
				
				if($encodedRecordId > 0){
					
					$containerId = $encodedRecordId;
					
					$rowData = [];
					$rowData['i_fba_sheet_detail_id'] = $encodedRecordId;
					$rowData['e_final_box_pallet_type'] = (!empty($request->input('final_boxes_pallets_'.$i)) ? trim($request->input('final_boxes_pallets_'.$i)) : '');
					$rowData['e_record_type'] = (!empty($recordType) ? trim($recordType) : '');
					$rowData['i_number_of_box_pallet'] = (!empty($request->input('number_of_box_pallet_'.$i)) ? $request->input('number_of_box_pallet_'.$i) : 0);
					$rowData['d_unit_pallet_box_cost'] = '';
					
					$finalBoxPalletType = $rowData['e_final_box_pallet_type'];				
						
					if( isset($rowData['e_record_type']) && !empty($rowData['e_record_type']) && isset($rowData['i_fba_sheet_detail_id']) && !empty($rowData['i_fba_sheet_detail_id']) && isset($rowData['i_number_of_box_pallet']) && !empty($rowData['i_number_of_box_pallet']) && isset($rowData['e_final_box_pallet_type']) && !empty($rowData['e_final_box_pallet_type']) ){
						if($rowData['e_record_type'] == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
							$containerFBAType[] = $containerId;
						} else {
							$containerNotFBAType[] = $containerId;
						}
						
						if($rowData['e_record_type'] == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
							if(in_array($containerId, $previousSelectedUsaContainerClubbingFBARecords)){
								$collectExistingDetailInfo = ( isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails->detailInfo) ? collect($usaContainerClubbingDetails->detailInfo)->where('i_fba_sheet_detail_id', $containerId)->where('e_record_type', config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD"))->first() : []  );
								$rowData['i_id'] = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->i_id) ? $collectExistingDetailInfo->i_id : 0 );
								$usaContainerUpdateTableData[] = $rowData;							
							} else {
								$usaContainerInsertTableData[] = $rowData;													
							}
						} else {
							if(in_array($containerId, $previousSelectedUsaContainerClubbingNotFBARecords)){
								$collectExistingDetailInfo = ( isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails->detailInfo) ? collect($usaContainerClubbingDetails->detailInfo)->where('i_fba_sheet_detail_id', $containerId)->where('e_record_type', config("constants.USA_CONTAINER_CLUBBING_NOT_FBA_RECORD"))->first() : []  );
								$rowData['i_id'] = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->i_id) ? $collectExistingDetailInfo->i_id : 0 );
								$usaContainerUpdateTableData[] = $rowData;
							} else {
								$usaContainerInsertTableData[] = $rowData;
							}
						}
					}
				}
			}
		}
		
		$recordData['e_final_box_pallet_type'] = ( isset($finalBoxPalletType) && !empty($finalBoxPalletType) ? $finalBoxPalletType : config("constants.PALLET") );
		
		$recordData['d_total_boxes'] = null;
		$recordData['d_total_pallets'] = null;
		
		$palletBoxNumber = 0;
		
		if($recordData['e_final_box_pallet_type'] == config("constants.BOX")){
			$recordData['d_total_boxes'] = (!empty($request->input('total_box')) ? trim($request->input('total_box')) : null );
			$palletBoxNumber = ( isset($recordData['d_total_boxes']) && !empty($recordData['d_total_boxes']) ? $recordData['d_total_boxes'] : 0 );
		} else {
			$recordData['d_total_pallets'] = (!empty($request->input('total_pallet')) ? trim($request->input('total_pallet')) : null );
			$palletBoxNumber = ( isset($recordData['d_total_pallets']) && !empty($recordData['d_total_pallets']) ? $recordData['d_total_pallets'] : 0 );
		}
		
		$recordData['d_unit_pallet_box_cost'] = ( $palletBoxNumber / (isset($recordData['d_logistic_cost_in_usd']) && !empty($recordData['d_logistic_cost_in_usd']) ? $recordData['d_logistic_cost_in_usd'] : 1));
		
		$recordData['v_fba_sheet_ids'] = (isset($containerFBAType) && !empty($containerFBAType) ? implode(',', $containerFBAType) : null);
		$recordData['v_usa_warehouse_container_ids'] = (isset($containerNotFBAType) && !empty($containerNotFBAType) ? implode(',', $containerNotFBAType) : null);
			
		DB::beginTransaction();
		try{			
			if($recordId > 0){
				$successMessage =  trans('messages.success-update',['module'=>$this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=>$this->moduleName]);
				
				if(isset($usaContainerUpdateTableData) && !empty($usaContainerUpdateTableData)){
					foreach ($usaContainerUpdateTableData as $usaContainerUpdateDetail){
						if(isset($usaContainerUpdateDetail) && !empty($usaContainerUpdateDetail) && !empty($usaContainerUpdateDetail['i_id'])){
							$usaContainerUpdateDetail['d_unit_pallet_box_cost'] = $recordData['d_unit_pallet_box_cost'];									
							$usaContainerClubbingDetailUpdate = $this->crudModel->updateTableData( $this->detailTableName , $usaContainerUpdateDetail , [ 'i_usa_container_clubbing_master_id' => $recordId, 'i_id' => $usaContainerUpdateDetail['i_id'] ] );							
						}
					}	
				}
				
				if(!empty($usaContainerClubbingDetails->documentInfo)){
					foreach ($usaContainerClubbingDetails->documentInfo as $usaContainerClubbingDetail){
						$usaContainerClubbingDetailId = $usaContainerClubbingDetail->i_id;
						if(!empty($request->input('edit_type_'.$usaContainerClubbingDetailId))){
							$usaContainerClubbingDocument = [];
							$usaContainerClubbingDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$usaContainerClubbingDetailId)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$usaContainerClubbingDetailId)) :0);
							$usaContainerClubbingDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$usaContainerClubbingDetailId)) ? $request->input('edit_remarks_'.$usaContainerClubbingDetailId) : null);
								
							if($request->hasFile('edit_file_'.$usaContainerClubbingDetailId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$usaContainerClubbingDetailId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$usaContainerClubbingDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
								$removeFiles = (!empty($request->input('remove_document_'.$usaContainerClubbingDetailId)) ? explode("," , $request->input('remove_document_'.$usaContainerClubbingDetailId) ) : []  );
								$previousUploadFiles = (!empty($usaContainerClubbingDetail->v_document_file_path) ? json_decode($usaContainerClubbingDetail->v_document_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$usaContainerClubbingDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if((!empty($usaContainerClubbingDocument ['i_document_type_id']))){
								$usaContainerClubbingDetailUpdate = $this->crudModel->updateTableData( $this->documentTableName , $usaContainerClubbingDocument , [ 'i_id' => $usaContainerClubbingDetailId] );
				
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( $this->documentTableName , $deleteRecordData , [ 'i_id' => $usaContainerClubbingDetailId] );
						}
					}
				}
				if(!empty($usaContainerClubbingDetails->invoiceInfo)){
					foreach ($usaContainerClubbingDetails->invoiceInfo as $usaContainerClubbingInvoiceDetail){
				
						$usaContainerClubbingInvoiceRecordId = $usaContainerClubbingInvoiceDetail->i_id;
						if(!empty($request->input('edit_name_'.$usaContainerClubbingInvoiceRecordId))){
							$usaContainerClubbingInvoice = [];
							$usaContainerClubbingInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$usaContainerClubbingInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$usaContainerClubbingInvoiceRecordId)) : 0 );
							$usaContainerClubbingInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_inv_no_'.$usaContainerClubbingInvoiceRecordId) :'' );
							$usaContainerClubbingInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_freight_'.$usaContainerClubbingInvoiceRecordId) : 0 );
							$usaContainerClubbingInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_custom_'.$usaContainerClubbingInvoiceRecordId) :0 );
							$usaContainerClubbingInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_duty_'.$usaContainerClubbingInvoiceRecordId)  : 0 );
							$usaContainerClubbingInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_other_'.$usaContainerClubbingInvoiceRecordId) :0 );
							$usaContainerClubbingInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_vat_'.$usaContainerClubbingInvoiceRecordId) : 0 );
							$totalCharges = $usaContainerClubbingInvoice['d_freight_charge'] + $usaContainerClubbingInvoice['d_custom_charge'] + $usaContainerClubbingInvoice['d_duty_charge'] + $usaContainerClubbingInvoice['d_other_charge'] + $usaContainerClubbingInvoice['d_vat_charge'];
							$usaContainerClubbingInvoice['d_total_charge'] = $totalCharges;
							$usaContainerClubbingInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_currency_id_'.$usaContainerClubbingInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_currency_id_'.$usaContainerClubbingInvoiceRecordId)) : 0);
							$usaContainerClubbingInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$usaContainerClubbingInvoiceRecordId)) ? $request->input('edit_cov_rate_'.$usaContainerClubbingInvoiceRecordId) : 0);
							$finalCharges = ($totalCharges * $usaContainerClubbingInvoice['d_conversion_rate']);
							$usaContainerClubbingInvoice['d_final_charge'] = $finalCharges;
								
							if($request->hasFile('edit_invoice_file_'.$usaContainerClubbingInvoiceRecordId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$usaContainerClubbingInvoiceRecordId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$usaContainerClubbingInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
				
								$removeFiles = (!empty($request->input('remove_invoice_'.$usaContainerClubbingInvoiceRecordId)) ? explode("," , $request->input('remove_invoice_'.$usaContainerClubbingInvoiceRecordId) ) : []  );
								$previousUploadFiles = (!empty($usaContainerClubbingInvoiceDetail->v_invoice_file_path) ? json_decode($usaContainerClubbingInvoiceDetail->v_invoice_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$usaContainerClubbingInvoice['v_invoice_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if(( $usaContainerClubbingInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($usaContainerClubbingInvoice['v_invoice_no']) ) ){
								$usaContainerClubbingInvoiceUpdate = $this->crudModel->updateTableData( $this->invoiceTableName , $usaContainerClubbingInvoice , [ 'i_id' => $usaContainerClubbingInvoiceRecordId] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( $this->invoiceTableName , $deleteRecordData , [ 'i_id' => $usaContainerClubbingInvoiceRecordId] );
						}
					}
				}
				$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
			} else {
				$usaContainerClubbingMasterDetails = $this->crudModel->selectData(config('constants.USA_CONTAINER_CLUBBING_MASTER_TABLE') ,['i_id']);
				
				
				$usaContainerClubbingMasterRecordCount = count($usaContainerClubbingMasterDetails);
				
				$count = ( ( (!empty($usaContainerClubbingMasterRecordCount)) && ( $usaContainerClubbingMasterRecordCount > 0 ) ) ? ( $usaContainerClubbingMasterRecordCount + 1  ) : 1 );
				$generateNumber = threeNumberSeries($count);
					
				$generateusaContainerClubbingMasterEntryNo = $generateNumber.'-'.$this->todayDate;
					
				$usaContainerClubbingGenerateNo = '';
				
				if($usaContainerClubbingType == config('constants.CUSTOMER_FBA_SHEET')){
					$usaContainerClubbingGenerateNo = config('constants.AGENT_WAREHOUSE_TO_CUSTOMER').'-'. $generateusaContainerClubbingMasterEntryNo;
				}
				if($usaContainerClubbingType == config('constants.AMAZON_FBA_SHEET')){
					$usaContainerClubbingGenerateNo = config('constants.AGENT_WAREHOUSE_TO_AMAZON').'-'. $generateusaContainerClubbingMasterEntryNo;
				}
				$recordData['v_usa_container_clubbing_record_no'] = $usaContainerClubbingGenerateNo;
					
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				
			}
			
			if( $insertRecord > 0 ){
				$result = true;
			}
			
			if(isset($usaContainerInsertTableData) && !empty($usaContainerInsertTableData)){
				
				$usaContainerInsertTableData = array_map(function($usaContainerInsert) use ($recordData, $insertRecord){
					$usaContainerInsert['d_unit_pallet_box_cost'] = $recordData['d_unit_pallet_box_cost'];
					$usaContainerInsert['i_usa_container_clubbing_master_id'] = $insertRecord;
					
					$usaContainerInsert = array_merge ( $this->crudModel->insertDateTimeData () , $usaContainerInsert );
					
					return $usaContainerInsert;
				}, $usaContainerInsertTableData);
				
				foreach ($usaContainerInsertTableData as $usaContainerInsertInfo){
					if(isset($usaContainerInsertInfo['i_fba_sheet_detail_id']) && !empty($usaContainerInsertInfo['i_fba_sheet_detail_id'])){
						if($usaContainerInsertInfo['e_record_type'] == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
							$this->crudModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), ['e_usa_container_clubbing_status' => config('constants.COMPLETED_STATUS') ]  , [ 'i_id' => $usaContainerInsertInfo['i_fba_sheet_detail_id']] );																		
						} else {
							$this->crudModel->updateTableData(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), ['e_usa_container_clubbing_status' => config('constants.COMPLETED_STATUS') ]  , [ 'i_id' => $usaContainerInsertInfo['i_fba_sheet_detail_id']] );
						}
					}
				}
				
				DB::table($this->detailTableName)->insert($usaContainerInsertTableData);				
			}
			
			if(isset($previousSelectedUsaContainerClubbingFBARecords) && !empty($previousSelectedUsaContainerClubbingFBARecords)){
				foreach ($previousSelectedUsaContainerClubbingFBARecords as $previousSelectedUsaContainerClubbingFBARecordId){
					if(!in_array($previousSelectedUsaContainerClubbingFBARecordId, $containerFBAType)){
						$collectExistingDetailInfo = ( isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails->detailInfo) ? collect($usaContainerClubbingDetails->detailInfo)->where('i_fba_sheet_detail_id', $previousSelectedUsaContainerClubbingFBARecordId)->where('e_record_type', config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD"))->first() : []  );
						$deleteRecordId = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->i_id) ? $collectExistingDetailInfo->i_id : 0 );
						$deleteRecordType = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->e_record_type) ? $collectExistingDetailInfo->e_record_type : '' );
						
						if($deleteRecordId > 0){
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( $this->detailTableName , $deleteRecordData , [ 'i_id' => $deleteRecordId ] );							
						}
						
						if(isset($previousSelectedUsaContainerClubbingFBARecordId) && !empty($previousSelectedUsaContainerClubbingFBARecordId)){
							if($deleteRecordType == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
								$this->crudModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingFBARecordId] );
							} else {
								$this->crudModel->updateTableData(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingFBARecordId] );
							}
						}
					}
				}
			}
			
			if(isset($previousSelectedUsaContainerClubbingNotFBARecords) && !empty($previousSelectedUsaContainerClubbingNotFBARecords)){
				foreach ($previousSelectedUsaContainerClubbingNotFBARecords as $previousSelectedUsaContainerClubbingNotFBARecordId){
					if(!in_array($previousSelectedUsaContainerClubbingNotFBARecordId, $containerNotFBAType)){
						$collectExistingDetailInfo = ( isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails->detailInfo) ? collect($usaContainerClubbingDetails->detailInfo)->where('i_fba_sheet_detail_id', $previousSelectedUsaContainerClubbingNotFBARecordId)->where('e_record_type', config("constants.USA_CONTAINER_CLUBBING_NOT_FBA_RECORD"))->first() : []  );
						$deleteRecordId = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->i_id) ? $collectExistingDetailInfo->i_id : 0 );
						$deleteRecordType = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->e_record_type) ? $collectExistingDetailInfo->e_record_type : '' );
			
						if($deleteRecordId > 0){
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( $this->detailTableName , $deleteRecordData , [ 'i_id' => $deleteRecordId ] );
						}
						
						if(isset($previousSelectedUsaContainerClubbingNotFBARecordId) && !empty($previousSelectedUsaContainerClubbingNotFBARecordId)){
							if($deleteRecordType == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
								$this->crudModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingNotFBARecordId] );
							} else {
								$this->crudModel->updateTableData(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingNotFBARecordId] );
							}
						}
					}
				}
			}
			
			$usaContainerDocumentInsertTableData = [];
			
			for ($i = 0; $i <= $documentTypeCount; $i++){
				$rowData = [];
				$rowData['i_usa_container_clubbing_master_id'] = $insertRecord;
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
					$usaContainerDocumentInsertTableData[] = $rowData;
				}
			
			}
			
			if(isset($usaContainerDocumentInsertTableData) && !empty($usaContainerDocumentInsertTableData)){
				
				$usaContainerDocumentInsertTableData = array_map(function($usaContainerDocumentInsert) use ($recordData){						
					$usaContainerDocumentInsert = array_merge ( $this->crudModel->insertDateTimeData () , $usaContainerDocumentInsert );
						
					return $usaContainerDocumentInsert;
				}, $usaContainerDocumentInsertTableData);
				
				DB::table($this->documentTableName)->insert($usaContainerDocumentInsertTableData);
			}
			
			$usaContainerTransporterInsertTableData = [];
			
			for ($i = 0; $i <= $transporterCount;$i++){
				$rowData = [];
				$rowData['i_usa_container_clubbing_master_id'] = $insertRecord;
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
			
				if(( $rowData ['i_logistic_partner_master_id'] > 0 ) && (!empty($rowData ['v_invoice_no']))){
					$usaContainerTransporterInsertTableData[] = $rowData;
				}
			}
			
			if(isset($usaContainerTransporterInsertTableData) && !empty($usaContainerTransporterInsertTableData)){
				$usaContainerTransporterInsertTableData = array_map(function($usaContainerTransporterInsert) use ($recordData){
					$usaContainerTransporterInsert = array_merge ( $this->crudModel->insertDateTimeData () , $usaContainerTransporterInsert );
				
					return $usaContainerTransporterInsert;
				}, $usaContainerTransporterInsertTableData);
				DB::table($this->invoiceTableName)->insert($usaContainerTransporterInsertTableData);
			}
			
			$result = true;
		}catch(\Exception $e){
			DB::rollback();
			Log::error($e->getMessage());
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
	
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_USA_CONTAINER_CLUBBING')) != true){
			$this->ajaxResponse(101, trans('messages.access-denied'));
		}
		
		$errorFound = true;
		if(!empty($request->input())){
			
			if(!empty($request->input())){
				$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			
				if( $recordId  > 0 ){
					$errorFound = false;
					
					$whereData = [];
					$whereData['master_id'] = $recordId;
					$whereData['edit_record'] = true;
					$usaContainerClubbingRecordDetails = $this->crudModel->getRecordDetails($whereData);
						
					$usaContainerClubbingDetails = (!empty($usaContainerClubbingRecordDetails) ? $usaContainerClubbingRecordDetails[0] : []);
					
					if(isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails)){	
						
						$previousSelectedUsaContainerClubbingFBARecords = ( (isset($usaContainerClubbingDetails) && (!empty($usaContainerClubbingDetails->v_fba_sheet_ids))) ? explode(',', $usaContainerClubbingDetails->v_fba_sheet_ids) : [] );
						$previousSelectedUsaContainerClubbingNotFBARecords = ( (isset($usaContainerClubbingDetails) && (!empty($usaContainerClubbingDetails->v_usa_warehouse_container_ids))) ? explode(',', $usaContainerClubbingDetails->v_usa_warehouse_container_ids) : [] );
						
						$result = false;
						DB::beginTransaction();
				
						try{
							
							if(isset($previousSelectedUsaContainerClubbingFBARecords) && !empty($previousSelectedUsaContainerClubbingFBARecords)){
								foreach ($previousSelectedUsaContainerClubbingFBARecords as $previousSelectedUsaContainerClubbingFBARecordId){
									$collectExistingDetailInfo = ( isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails->detailInfo) ? collect($usaContainerClubbingDetails->detailInfo)->where('i_fba_sheet_detail_id', $previousSelectedUsaContainerClubbingFBARecordId)->where('e_record_type', config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD"))->first() : []  );
									$deleteRecordType = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->e_record_type) ? $collectExistingDetailInfo->e_record_type : '' );
						
									if(isset($previousSelectedUsaContainerClubbingFBARecordId) && !empty($previousSelectedUsaContainerClubbingFBARecordId)){
										if($deleteRecordType == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
											$this->crudModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingFBARecordId] );
										} else {
											$this->crudModel->updateTableData(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingFBARecordId] );
										}
									}
								}
							}
								
							if(isset($previousSelectedUsaContainerClubbingNotFBARecords) && !empty($previousSelectedUsaContainerClubbingNotFBARecords)){
								foreach ($previousSelectedUsaContainerClubbingNotFBARecords as $previousSelectedUsaContainerClubbingNotFBARecordId){
									$collectExistingDetailInfo = ( isset($usaContainerClubbingDetails) && !empty($usaContainerClubbingDetails->detailInfo) ? collect($usaContainerClubbingDetails->detailInfo)->where('i_fba_sheet_detail_id', $previousSelectedUsaContainerClubbingNotFBARecordId)->where('e_record_type', config("constants.USA_CONTAINER_CLUBBING_NOT_FBA_RECORD"))->first() : []  );
									$deleteRecordType = ( isset($collectExistingDetailInfo) && !empty($collectExistingDetailInfo->e_record_type) ? $collectExistingDetailInfo->e_record_type : '' );
						
									if(isset($previousSelectedUsaContainerClubbingNotFBARecordId) && !empty($previousSelectedUsaContainerClubbingNotFBARecordId)){
										if($deleteRecordType == config("constants.USA_CONTAINER_CLUBBING_FBA_RECORD")){
											$this->crudModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingNotFBARecordId] );
										} else {
											$this->crudModel->updateTableData(config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE'), ['e_usa_container_clubbing_status' => config('constants.PENDING_STATUS') ]  , [ 'i_id' => $previousSelectedUsaContainerClubbingNotFBARecordId] );
										}
									}
								}
							}
							
							$whereData = [];
							$whereData['i_usa_container_clubbing_master_id'] = $recordId;
							
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
				
							$this->crudModel->deleteTableData($this->detailTableName, $deleteRecordData, $whereData);
							$this->crudModel->deleteTableData($this->documentTableName, $deleteRecordData, $whereData);
							$this->crudModel->deleteTableData($this->invoiceTableName, $deleteRecordData, $whereData);
							
							unset($whereData['i_usa_container_clubbing_master_id']);
								
							$whereData['i_id'] = $recordId;
							$this->crudModel->deleteTableData($this->tableName, $deleteRecordData, $whereData);
				
							$result = true;
						} catch (\Exception $e){
							$result = false;
							DB::rollBack();
							Log::error($e->getMessage());
						}
				
						if( $result != false){
							DB::commit();
							Wild_tiger::setFlashMessage('success', trans ( 'messages.success-delete', [ 'module' => $this->moduleName ]));
						} else {
							DB::rollBack();
							Wild_tiger::setFlashMessage('danger', trans ( 'messages.error-delete', [ 'module' => $this->moduleName ]));
						}
						return redirect( $this->redirectUrl );
					}			
				}
			}			
		}
		
		if( $errorFound != false ){
			return redirect(config('constants.PAGE_NOT_FOUND_URL'));
		}
	}
	
	public function getFbaRecordDetails(Request $request){
		$whereData = $joinData = [];
		$html = "";
		$fromWarehouseId = (!empty($request->input('from_warehouse_id')) ? (int) Wild_tiger::decode( $request->input('from_warehouse_id') ) : '' );
		$toLocationId = (!empty($request->input('to_location_id')) ? (int) Wild_tiger::decode( $request->input('to_location_id') ) : '' );
		$status = (!empty($request->input('status')) ? (int) Wild_tiger::decode( $request->input('status') ) : '' );
		$recordId = (!empty($request->input('record_id')) ? (int) Wild_tiger::decode( $request->input('record_id') ) : '' );
		$disableForm = (!empty($request->input('disable_form')) ? $request->input('disable_form') : '' );
		// $boxPalletType = (!empty($request->input('box_pallet_type')) ? explode(',', $request->input('box_pallet_type')) : [] );
	
		if( !empty($fromWarehouseId) || !empty($toLocationId) ){
			
			// Get current status
			/* $statusMasterInfo = [];
			if(isset($status) && !empty($status)){
				$statusWhereData = [];
				$statusWhereData['t_is_active'] = 1;
				$statusWhereData['i_id'] = $status;
			
				$statusMasterInfo = StatusMasterModel::where($statusWhereData)->first();
			} */
			
			// Get current record Info
			$useContainerMasterInfo = [];
			if(isset($recordId) && !empty($recordId)){
				$usaContainerMasterWhereData = [];
				$usaContainerMasterWhereData['t_is_active'] = 1;
				$usaContainerMasterWhereData['i_id'] = $recordId;
					
				$useContainerMasterInfo = UsaContainerClubbingModel::where($usaContainerMasterWhereData)->first();
			}
			
			$data = [];
			$data['recordInfo'] = (isset($useContainerMasterInfo) && !empty($useContainerMasterInfo) ? $useContainerMasterInfo : []);
			$data['statusMasterInfo'] = (isset($statusMasterInfo) && !empty($statusMasterInfo) ? $statusMasterInfo : []);
			$data ['boxPalletDetails'] = typeInfo();
			$data ['disableForm'] = $disableForm;
			
			/* if(isset($useContainerMasterInfo) && empty($useContainerMasterInfo)){
				$whereData['status_usa_container_clubbing'] = config('constants.PENDING_STATUS');
			} */
			
			if(isset($fromWarehouseId) && !empty($fromWarehouseId)){
				$whereData['from_warehouse_id'] = $fromWarehouseId;				
			}
			
			if(isset($toLocationId) && !empty($toLocationId)){
				$whereData['to_location_id'] = $toLocationId;
			}
			
			if(isset($recordId) && !empty($recordId)){
				$whereData['usa_master_record_id'] = $recordId;
			}
			
			$whereData['usa_clubbing_container_search'] = true;
				
			$whereData['order_by'] = [ 'i_id' => 'asc' ];
				
			$fbdDetailModal = new FBASheeteDetailModel();
			$getFbaRecordDetails =  $fbdDetailModal->getFBASheetDetails($whereData);
			
			if(!empty($getFbaRecordDetails)){
				$data['getFbaRecordDetails'] = $getFbaRecordDetails;
				$html .= view (config('constants.AJAX_VIEW_FOLDER') . 'usa-container-clubbing/usa-container-clubbing-fba-goods' )->with ( $data )->render();
			}
			
			// Get 4th step records
			$usWarehouseToAmazonWhereData = [];
			$usWarehouseToAmazonWhereData['t_is_active'] = 1;
			// $usWarehouseToAmazonWhereData['e_usa_container_clubbing_status'] = config('constants.PENDING_STATUS');
				
			if(isset($fromWarehouseId) && !empty($fromWarehouseId)){
				$usWarehouseToAmazonWhereData['i_amazon_from_warehouse_id'] = $fromWarehouseId;
			}
			if(isset($toLocationId) && !empty($toLocationId)){
				$usWarehouseToAmazonWhereData['i_to_amazon_location_id'] = $toLocationId;
			}
				
			$usWarehouseToAmazonQuery = UsWarehouseToAmazonDetailsModel::where($usWarehouseToAmazonWhereData);
			
			$usWarehouseToAmazonQuery->where(function($q1) use ($recordId){
				$q1->orWhere('e_usa_container_clubbing_status', config('constants.PENDING_STATUS'));
				if($recordId > 0){
					$q1->orWhere(function ($q2) use ($recordId){
						$q2->where('e_usa_container_clubbing_status', config('constants.COMPLETED_STATUS'));
						$q2->whereHas('usaContainerDetailInfo', function($q3) use ($recordId){
							$q3->where('i_usa_container_clubbing_master_id', $recordId);
						});
					});
				}
			});
			
			$usWarehouseToAmazonQuery->whereHas('usWarehouseToAmazonMaster', function($q1){
				$q1->where('t_is_active', 1);
				$q1->where('e_to_location', config('constants.AMAZON_FBA_SHEET'));
				$q1->where('i_status_id', config('constants.DELIVERED_STATUS_ID'));
			});
			
			$usWarehouseToAmazonRecordDetails = $usWarehouseToAmazonQuery->get();
			
			if(!empty($usWarehouseToAmazonRecordDetails)){
				$data['getFbaRecordDetails'] = $usWarehouseToAmazonRecordDetails;
				$data['startIndex'] = (isset($getFbaRecordDetails) && count($getFbaRecordDetails) > 0 ? count($getFbaRecordDetails) : 0);
				$html .= view (config('constants.AJAX_VIEW_FOLDER') . 'usa-container-clubbing/usa-container-clubbing-warehouse-to-amazon' )->with ( $data )->render();
			}
		}
		echo $html;die;
	}
	
}