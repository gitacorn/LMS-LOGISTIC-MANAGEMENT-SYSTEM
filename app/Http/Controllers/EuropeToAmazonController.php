<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CountrytoPortEuropeModel;
use App\Login;
use App\LogisticPartnerMasterModel;
use App\CompanyMasterModel;
use App\WarehouseMasterModel;
use App\Document_Type_Master_Model;
use App\CurrencyMasterModel;
use App\StatusMasterModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueAmazonShimentId;
use App\LogisticPartnerDetailModel;
use Illuminate\Support\Facades\Response;
use App\CountryMasterModel;
use Carbon\Carbon;
use App\ReportModel;

class EuropeToAmazonController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'europe-to-amazon/';
		$this->moduleName = trans('messages.europe-to-amazon');
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.EUROPE_TO_AMAZON_MASTER_URL');
		$this->crudModel = new CountrytoPortEuropeModel();
		$this->reportModel = new ReportModel();
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_GOODS_OUT_TO_AMAZON')) != true){
			return redirect('access-denied');
		}
		$data = $where = [];
		$data ['pageTitle'] = trans('messages.to-amazon');
		
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		//$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->get();
		$data['companyDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['locationDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['countryMasterDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
		
		$statusIds = [];
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		$where['default_status'] = $statusIds;
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		$data['statusInfo'] = $statusIds;
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
		
		return view($this->folderName . 'europe-to-amazon')->with($data);
	}
	public function create() {
		if(checkPermission(config('permission_constants.ADD_GOODS_OUT_TO_AMAZON')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-to-amazon');
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->where('t_is_active',1)->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['comapnyMasterDetails'] = CompanyMasterModel::where('t_is_active',1)->orderBy('v_company_name', 'ASC')->get();
		$data['warehouseMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['locationMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['countryMasterDetails'] = CountryMasterModel::where('t_is_active',1)->orderBy('v_country_name', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->where('t_is_active',1)->orderBy('v_document_type_name', 'ASC')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::where('t_is_active',1)->orderBy('i_sequence', 'ASC')->get();
		$whareData = 1 ;
		$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whareData){
											$query->where('t_is_active',$whareData); 
										})->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		return view ( $this->folderName . 'add-europe-to-amazon' )->with ( $data );
	
	}
	public function add(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_TO_AMAZON')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_GOODS_OUT_TO_AMAZON')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['way_of_transport'] = 'required';
		$formValidation['book_by'] = 'required';
		$formValidation['logistic_partner'] = 'required';
		//$formValidation['booking_date'] = 'required';
		//$formValidation['tracking_no'] = 'required';
		$formValidation['status'] = 'required';
		
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			//$formValidation['collection_date'] = 'required';
			//$formValidation['delivery_date'] = 'required';
		}
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'way_of_transport.required' => __ ( 'messages.require-way-of-transport' ),
				'book_by.required' => __ ( 'messages.require-book-by' ),
				'logistic_partner.required' => __ ( 'messages.require-logistic-partner' ),
				'booking_date.required' => __ ( 'messages.require-booking-date' ),
				'tracking_no.required' => __ ( 'messages.require-tracking-no' ),
				'status.required' => __ ( 'messages.require-status' ),
				'collection_date.required' => __ ( 'messages.require-collection-date' ),
				'delivery_date.required' => __ ( 'messages.require-delivery-date' ),
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=>trans('messages.to-amazon')]);
		$errorMessages = trans('messages.error-create',['module'=>trans('messages.to-amazon')]);
		
		$toAmazonShipmentCount = (!empty($request->input('europe_to_amazon_shipment_count')) ? (int)($request->input('europe_to_amazon_shipment_count')) : 1 );
		$toAmazonDocumentTypeCount = (!empty($request->input('europe_to_amazon_document_type_count')) ? (int)($request->input('europe_to_amazon_document_type_count')) : 1 );
		$toAmazonTransporterCount = (!empty($request->input('europe_to_amazon_transporter_count')) ? (int)($request->input('europe_to_amazon_transporter_count')) : 1 );
		
		DB::beginTransaction();
		try{
			$recordData = [];
			$recordData['e_transport_way'] = (!empty($request->input('way_of_transport')) ? ($request->input('way_of_transport')) : '' );
			$recordData['i_book_by_employee_id'] = (!empty($request->input('book_by')) ? (int)Wild_tiger::decode($request->input('book_by')) : 0 );
			$recordData['i_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner')) ? (int)Wild_tiger::decode($request->input('logistic_partner')) : 0 );
			//$recordData['dt_booking_date'] = (!empty($request->input('booking_date')) ? dbDate($request->input('booking_date')) : '' );
			//$recordData['dt_collection_date'] = (!empty($request->input('collection_date')) ? dbDate($request->input('collection_date')) : null );
			//$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
			//$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? ($request->input('tracking_no')) : '' );
			//$recordData['v_tracking_link'] = (!empty($request->input('tracking_link')) ? ($request->input('tracking_link')) : null );
			//$recordData['dt_amazon_shipment_date'] = (!empty($request->input('amazon_appointment_date')) ? dbDate($request->input('amazon_appointment_date')) : null );
			//$recordData['v_amazon_appointment_id'] = (!empty($request->input('amazon_appointment_id')) ? ($request->input('amazon_appointment_id')) : null );
			$recordData['i_status_id'] = $statusRecordId;
			$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? ($request->input('status_comments')) : null );
			
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=>trans('messages.to-amazon')]);
				$errorMessages = trans('messages.error-update',['module'=>trans('messages.to-amazon')]);
				$whereData = [];
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
				$europeToAmazonRecordDetails = $this->crudModel->getRecordDetails($whereData);
				$europeToAmazonDetails = (!empty($europeToAmazonRecordDetails) ? $europeToAmazonRecordDetails[0] : []);
				
				if(!empty($europeToAmazonDetails->detailInfo)){
					foreach ($europeToAmazonDetails->detailInfo as $europeToAmazonDetail){
						$europeToAmazonDetailtId = $europeToAmazonDetail->i_id;
						//$recordData['v_tracking_no'] = (!empty($request->input('edit_tracking_no_'.$europeToAmazonDetailtId)) ? ($request->input('edit_tracking_no_'.$europeToAmazonDetailtId)) : null);
							
						$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
						if(!empty($request->input('edit_shipment_id_'.$europeToAmazonDetailtId))){
							$europeToAmazonRecordDetail = [];
							$europeToAmazonRecordDetail['v_workflow_id'] = (!empty($request->input('edit_workflow_id_'.$europeToAmazonDetailtId)) ? $request->input('edit_workflow_id_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['v_shipment_id'] = (!empty($request->input('edit_shipment_id_'.$europeToAmazonDetailtId)) ? $request->input('edit_shipment_id_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['v_ref_id'] = (!empty($request->input('edit_ref_id_'.$europeToAmazonDetailtId)) ? $request->input('edit_ref_id_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['i_account_company_id'] = (!empty($request->input('edit_account_'.$europeToAmazonDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_account_'.$europeToAmazonDetailtId)) : 0);
							$europeToAmazonRecordDetail['i_packing_warehouse_id'] = (!empty($request->input('edit_packing_warehouse_'.$europeToAmazonDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_packing_warehouse_'.$europeToAmazonDetailtId)) : 0);
							$europeToAmazonRecordDetail['i_warehouse_id'] = (!empty($request->input('edit_from_warehouse_'.$europeToAmazonDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_from_warehouse_'.$europeToAmazonDetailtId)) : 0);
							$europeToAmazonRecordDetail['i_location_id'] = (!empty($request->input('edit_to_amazon_location_'.$europeToAmazonDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_to_amazon_location_'.$europeToAmazonDetailtId)) : 0);
							$europeToAmazonRecordDetail['i_to_country_id'] = (!empty($request->input('edit_to_country_delivery_'.$europeToAmazonDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_to_country_delivery_'.$europeToAmazonDetailtId)) : 0);
							$europeToAmazonRecordDetail['v_sku'] = (!empty($request->input('edit_sku_'.$europeToAmazonDetailtId)) ? $request->input('edit_sku_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['v_units'] = (!empty($request->input('edit_unit_'.$europeToAmazonDetailtId)) ? $request->input('edit_unit_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['i_currency_id'] = (!empty($request->input('edit_shipment_currency_'.$europeToAmazonDetailtId)) ? (int)Wild_tiger::decode($request->input('edit_shipment_currency_'.$europeToAmazonDetailtId)) : '');
							$europeToAmazonRecordDetail['v_price'] = (!empty($request->input('edit_price_'.$europeToAmazonDetailtId)) ? $request->input('edit_price_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['d_weight'] = (!empty($request->input('edit_weight_'.$europeToAmazonDetailtId)) ? $request->input('edit_weight_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['i_no_of_pallet_box'] = (!empty($request->input('edit_no_of_pallets_boxes_'.$europeToAmazonDetailtId)) ? $request->input('edit_no_of_pallets_boxes_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['e_dimension'] = (!empty($request->input('edit_pallets_boxes_type_'.$europeToAmazonDetailtId)) ? $request->input('edit_pallets_boxes_type_'.$europeToAmazonDetailtId) : '');
							$europeToAmazonRecordDetail['dt_booking_date'] = (!empty($request->input('edit_amazon_booking_date_'.$europeToAmazonDetailtId)) ? dbDate($request->input('edit_amazon_booking_date_'.$europeToAmazonDetailtId)) : null);
							$europeToAmazonRecordDetail['dt_collection_date'] = (!empty($request->input('edit_amazon_collection_date_'.$europeToAmazonDetailtId)) ? dbDate($request->input('edit_amazon_collection_date_'.$europeToAmazonDetailtId)) : null);
							$europeToAmazonRecordDetail['dt_delivery_date'] = (!empty($request->input('edit_amazon_delivery_date_'.$europeToAmazonDetailtId)) ? dbDate($request->input('edit_amazon_delivery_date_'.$europeToAmazonDetailtId)) : null);
							$europeToAmazonRecordDetail['v_tracking_link'] = (!empty($request->input('edit_tracking_link_'.$europeToAmazonDetailtId)) ? $request->input('edit_tracking_link_'.$europeToAmazonDetailtId) : null);
							$europeToAmazonRecordDetail['dt_amazon_appointment_date'] = (!empty($request->input('edit_amazon_appointment_date_'.$europeToAmazonDetailtId)) ? dbDate($request->input('edit_amazon_appointment_date_'.$europeToAmazonDetailtId)) : null);
							$europeToAmazonRecordDetail['v_amazon_appointment_id'] = (!empty($request->input('edit_amazon_appointment_id_'.$europeToAmazonDetailtId)) ? $request->input('edit_amazon_appointment_id_'.$europeToAmazonDetailtId) : null);
								
							if(!empty($europeToAmazonRecordDetail['v_shipment_id'])){
								$europeToAmazonDetailUpdate = $this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE') , $europeToAmazonRecordDetail , [ 'i_id' => $europeToAmazonDetailtId] );
							}
						} else{
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE') , $deleteRecordData , [ 'i_id' => $europeToAmazonDetailtId] );
							
							#delete Shipment Info table
							$shipmentWhereData = [];
							$shipmentWhereData['v_ref_record_type'] = config('constants.WAREHOUSE_TO_AMAZON');
							$shipmentWhereData['i_ref_table_id'] = $europeToAmazonDetailtId;
							$this->crudModel->deleteTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $deleteRecordData , $shipmentWhereData );
						}
					}
				}
				if(!empty($europeToAmazonDetails->documentInfo)){
					foreach ($europeToAmazonDetails->documentInfo as $europeToAmazonDocumentDetail){
						$europeToAmazonDocumentId = $europeToAmazonDocumentDetail->i_id;
						if(!empty($request->input('edit_type_'.$europeToAmazonDocumentId))){
							$europeToAmazonDocument = [];
							$europeToAmazonDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$europeToAmazonDocumentId)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$europeToAmazonDocumentId)) :0);
							$europeToAmazonDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$europeToAmazonDocumentId)) ? $request->input('edit_remarks_'.$europeToAmazonDocumentId) : null);
				
							if($request->hasFile('edit_file_'.$europeToAmazonDocumentId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$europeToAmazonDocumentId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$europeToAmazonDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
								$removeFiles = (!empty($request->input('remove_document_'.$europeToAmazonDocumentId)) ? explode("," , $request->input('remove_document_'.$europeToAmazonDocumentId) ) : []  );
								$previousUploadFiles = (!empty($europeToAmazonDocumentDetail->v_document_file_path) ? json_decode($europeToAmazonDocumentDetail->v_document_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$europeToAmazonDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if((!empty($europeToAmazonDocument ['i_document_type_id']))){
								$europeToAmazonDocumentDetailUpdate = $this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $europeToAmazonDocument , [ 'i_id' => $europeToAmazonDocumentId] );
				
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $europeToAmazonDocumentId] );
						}
					}
				}
				if(!empty($europeToAmazonDetails->invoiceInfo)){
					foreach ($europeToAmazonDetails->invoiceInfo as $europeToAmazonInvoiceDetail){
				
						$europeToAmazonInvoiceRecordId = $europeToAmazonInvoiceDetail->i_id;
						if(!empty($request->input('edit_name_'.$europeToAmazonInvoiceRecordId))){
							$europeToAmazonInvoice = [];
							$europeToAmazonInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$europeToAmazonInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$europeToAmazonInvoiceRecordId)) :0 );
							$europeToAmazonInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_inv_no_'.$europeToAmazonInvoiceRecordId) :'' );
							$europeToAmazonInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_freight_'.$europeToAmazonInvoiceRecordId) : 0 );
							$europeToAmazonInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_custom_'.$europeToAmazonInvoiceRecordId) :0 );
							$europeToAmazonInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_duty_'.$europeToAmazonInvoiceRecordId)  : 0 );
							$europeToAmazonInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_other_'.$europeToAmazonInvoiceRecordId) :0 );
							$europeToAmazonInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_vat_'.$europeToAmazonInvoiceRecordId) : 0 );
							$totalCharges = $europeToAmazonInvoice['d_freight_charge'] + $europeToAmazonInvoice['d_custom_charge'] + $europeToAmazonInvoice['d_duty_charge'] + $europeToAmazonInvoice['d_other_charge'] + $europeToAmazonInvoice['d_vat_charge'];
							$europeToAmazonInvoice['d_total_charge'] = $totalCharges;
							$europeToAmazonInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_currency_id_'.$europeToAmazonInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_currency_id_'.$europeToAmazonInvoiceRecordId)) : 0);
							$europeToAmazonInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$europeToAmazonInvoiceRecordId)) ? $request->input('edit_cov_rate_'.$europeToAmazonInvoiceRecordId) : 0);
							$finalCharges = ($totalCharges * $europeToAmazonInvoice['d_conversion_rate']);
							$europeToAmazonInvoice['d_final_charge'] = $finalCharges;
				
							if($request->hasFile('edit_invoice_file_'.$europeToAmazonInvoiceRecordId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$europeToAmazonInvoiceRecordId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$europeToAmazonInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
				
								$removeFiles = (!empty($request->input('remove_invoice_'.$europeToAmazonInvoiceRecordId)) ? explode("," , $request->input('remove_invoice_'.$europeToAmazonInvoiceRecordId) ) : []  );
								$previousUploadFiles = (!empty($europeToAmazonInvoiceDetail->v_invoice_file_path) ? json_decode($europeToAmazonInvoiceDetail->v_invoice_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$europeToAmazonInvoice['v_invoice_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if( ( $europeToAmazonInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($europeToAmazonInvoice['v_invoice_no']) ) ){
								$agentWarehouseInvoiceUpdate = $this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_INVOICE_MASTER_TABLE') , $europeToAmazonInvoice , [ 'i_id' => $europeToAmazonInvoiceRecordId] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_INVOICE_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $europeToAmazonInvoiceRecordId] );
						}
					}
				}
				//$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
			}
			
			/* if( $insertRecord > 0 ){
				$result = true;
			} */
			$amazonShipmentDetails = [];
			for($i = 1; $i<= $toAmazonShipmentCount; $i++){
				
				$rowData = [];
				$rowData['workflow_id'] = (!empty($request->input('workflow_id_'.$i)) ? $request->input('workflow_id_'.$i) : '');
				$rowData['shipment_id'] = (!empty($request->input('shipment_id_'.$i)) ? $request->input('shipment_id_'.$i) : '');
				$rowData['ref_id'] = (!empty($request->input('ref_id_'.$i)) ? $request->input('ref_id_'.$i) : '');
				$rowData['account_company_id'] = (!empty($request->input('account_'.$i)) ? (int)Wild_tiger::decode($request->input('account_'.$i)) : 0);
				$rowData['packing_warehouse_id'] = (!empty($request->input('packing_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('packing_warehouse_'.$i)) : 0);
				$rowData['warehouse_id'] = (!empty($request->input('from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('from_warehouse_'.$i)) : 0);
				$rowData['location_id'] = (!empty($request->input('to_amazon_location_'.$i)) ? (int)Wild_tiger::decode($request->input('to_amazon_location_'.$i)) : 0);
				$rowData['to_country_id'] = (!empty($request->input('to_country_delivery_'.$i)) ? (int)Wild_tiger::decode($request->input('to_country_delivery_'.$i)) : 0);
				$rowData['sku'] = (!empty($request->input('sku_'.$i)) ? $request->input('sku_'.$i) : '');
				$rowData['units'] = (!empty($request->input('unit_'.$i)) ? $request->input('unit_'.$i) : '');
				$rowData['shipment_currency'] = (!empty($request->input('shipment_currency_'.$i)) ? (int)Wild_tiger::decode($request->input('shipment_currency_'.$i)) : '');
				$rowData['price'] = (!empty($request->input('price_'.$i)) ? $request->input('price_'.$i) : '');
				$rowData['weight'] = (!empty($request->input('weight_'.$i)) ? $request->input('weight_'.$i) : '');
				$rowData['no_of_pallets_boxes'] = (!empty($request->input('no_of_pallets_boxes_'.$i)) ? $request->input('no_of_pallets_boxes_'.$i) : '');
				$rowData['pallets_boxes_type'] = (!empty($request->input('pallets_boxes_type_'.$i)) ? $request->input('pallets_boxes_type_'.$i) : '');
				$rowData['amazon_booking_date'] = (!empty($request->input('amazon_booking_date_'.$i)) ? $request->input('amazon_booking_date_'.$i) : null);
				$rowData['amazon_collection_date'] = (!empty($request->input('amazon_collection_date_'.$i)) ? $request->input('amazon_collection_date_'.$i) : null);
				$rowData['amazon_delivery_date'] = (!empty($request->input('amazon_delivery_date_'.$i)) ? $request->input('amazon_delivery_date_'.$i) : null);
				$rowData['amazon_tracking_no'] = (!empty($request->input('amazon_tracking_no_'.$i)) ? $request->input('amazon_tracking_no_'.$i) : null);
				$rowData['amazon_tracking_link'] = (!empty($request->input('amazon_tracking_link_'.$i)) ? $request->input('amazon_tracking_link_'.$i) : null);
				$rowData['amazon_appointment_date'] = (!empty($request->input('amazon_appointment_date_'.$i)) ? $request->input('amazon_appointment_date_'.$i) : null);
				$rowData['amazon_appointment_id'] = (!empty($request->input('amazon_appointment_id_'.$i)) ? $request->input('amazon_appointment_id_'.$i) : null);
				## shipment id nai hoi and tracking number hase to master ma entry thai jati hati but child ma entry nati padti so ana lidhe condition muki che
				if(!empty($rowData['shipment_id'])){
					$amazonShipmentDetails[$rowData['amazon_tracking_no']][] = $rowData;
				}
			}
			
			if(!empty($amazonShipmentDetails)){
				foreach ($amazonShipmentDetails as $key => $amazonShipmentDetail){
					
					$toAmazonMasterRecordDetails = $this->crudModel->selectData(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_MASTER_TABLE') ,['i_id']);
					$toAmazonMasterRecordCount = count($toAmazonMasterRecordDetails);
					$count = ( ( (!empty($toAmazonMasterRecordCount)) && ( $toAmazonMasterRecordCount > 0 ) ) ? ( $toAmazonMasterRecordCount + 1  ) : 1 );
					$generateNumber = threeNumberSeries($count);
					$toAmazonGenerateNo = config('constants.WAREHOUSE_TO_AMAZON').'-'. $generateNumber.'-'.$this->todayDate;
					$recordData['v_country_to_port_europe_record_no'] = $toAmazonGenerateNo;
					$recordData['v_tracking_no'] = (!empty($key) ? $key : null);
					
					if($recordId > 0 ){
						
					} else {
						
						if(!empty($recordData['v_tracking_no'])){
							$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
						}	
					}
					
					foreach ($amazonShipmentDetail as $amazonShipmentInfo){
						/* if($recordId > 0 ){
						
						} else {
						
							if(!empty($recordData['v_tracking_no'])){
								$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
							}
						} */
						
						$rowData = [];
						$rowData['i_country_to_port_europe_goods_master_id'] = $insertRecord;
						$rowData['v_workflow_id'] = (!empty($amazonShipmentInfo['workflow_id']) ? $amazonShipmentInfo['workflow_id'] : null);
						$rowData['v_shipment_id'] = (!empty($amazonShipmentInfo['shipment_id']) ? $amazonShipmentInfo['shipment_id'] : null);
						$rowData['v_ref_id'] = (!empty($amazonShipmentInfo['ref_id']) ? $amazonShipmentInfo['ref_id'] : null);
						$rowData['i_account_company_id'] = (!empty($amazonShipmentInfo['account_company_id']) ? $amazonShipmentInfo['account_company_id'] : null);
						$rowData['i_packing_warehouse_id'] = (!empty($amazonShipmentInfo['packing_warehouse_id']) ? $amazonShipmentInfo['packing_warehouse_id'] : 0);
						$rowData['i_warehouse_id'] = (!empty($amazonShipmentInfo['warehouse_id']) ? $amazonShipmentInfo['warehouse_id'] : "");
						$rowData['i_location_id'] = (!empty($amazonShipmentInfo['location_id']) ? $amazonShipmentInfo['location_id'] : "");
						$rowData['i_to_country_id'] = (!empty($amazonShipmentInfo['to_country_id']) ? $amazonShipmentInfo['to_country_id'] : 0);
						$rowData['v_sku'] = (!empty($amazonShipmentInfo['sku']) ? $amazonShipmentInfo['sku'] : null);
						$rowData['v_units'] = (!empty($amazonShipmentInfo['units']) ? $amazonShipmentInfo['units'] : null);
						$rowData['i_currency_id'] = (!empty($amazonShipmentInfo['shipment_currency']) ? $amazonShipmentInfo['shipment_currency'] : 0);
						$rowData['v_price'] = (!empty($amazonShipmentInfo['price']) ? $amazonShipmentInfo['price'] : '');
						$rowData['d_weight'] = (!empty($amazonShipmentInfo['weight']) ? $amazonShipmentInfo['weight'] : '');
						$rowData['i_no_of_pallet_box'] = (!empty($amazonShipmentInfo['no_of_pallets_boxes']) ? $amazonShipmentInfo['no_of_pallets_boxes'] : '');
						$rowData['e_dimension'] = (!empty($amazonShipmentInfo['pallets_boxes_type']) ? $amazonShipmentInfo['pallets_boxes_type'] : '');
						$rowData['dt_booking_date'] = (!empty($amazonShipmentInfo['amazon_booking_date']) ? dbDate($amazonShipmentInfo['amazon_booking_date']) : null);
						$rowData['dt_collection_date'] = (!empty($amazonShipmentInfo['amazon_collection_date']) ? dbDate($amazonShipmentInfo['amazon_collection_date']) : null);
						$rowData['dt_delivery_date'] = (!empty($amazonShipmentInfo['amazon_delivery_date']) ? dbDate($amazonShipmentInfo['amazon_delivery_date']) : null);
						$rowData['v_tracking_link'] = (!empty($amazonShipmentInfo['amazon_tracking_link']) ? $amazonShipmentInfo['amazon_tracking_link'] : null);
						$rowData['dt_amazon_appointment_date'] = (!empty($amazonShipmentInfo['amazon_appointment_date']) ? dbDate($amazonShipmentInfo['amazon_appointment_date']) : null);
						$rowData['v_amazon_appointment_id'] = (!empty($amazonShipmentInfo['amazon_appointment_id']) ? $amazonShipmentInfo['amazon_appointment_id'] : null);
			
						if(!empty($rowData['v_shipment_id'])){
							$insertShipmentDetail = $this->crudModel->insertTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE') , $rowData);
							
							$fbaSheetData = [];
							$fbaSheetData['v_shipment_no'] = $rowData['v_shipment_id'];
							$fbaSheetData['i_ref_table_id'] = $insertShipmentDetail;
							$fbaSheetData['v_ref_record_type'] = config('constants.WAREHOUSE_TO_AMAZON');
							$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
						}
					}
						
				}
			}
		
			for ($i = 0; $i <= $toAmazonDocumentTypeCount; $i++){
				$rowData = [];
				$rowData['i_country_to_port_europe_goods_master_id'] = $insertRecord;
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
					$insertDocumentDetail = $this->crudModel->insertTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $rowData);
				}
					
			}
			for ($i = 0; $i <= $toAmazonTransporterCount;$i++){
				$rowData = [];
				$rowData['i_country_to_port_europe_goods_master_id'] = $insertRecord;
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
					
				if( ( $rowData['i_logistic_partner_master_id'] > 0 ) && (!empty($rowData['v_invoice_no'])) ){
					$insertTransporterInvoice = $this->crudModel->insertTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_INVOICE_MASTER_TABLE') , $rowData);
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
	public function edit($id){
		if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_TO_AMAZON')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		
		if( $recordId > 0 ){
			$whereData = $data = [];
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			$data['pageTitle'] = trans('messages.update-to-amazon');
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
				
			if(count($recordInfo) > 0){
				$errorFound = false;
				$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
				$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				//$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				$data['comapnyMasterDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
				$data['warehouseMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['locationMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['countryMasterDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
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
					$data ['pageTitle'] = trans('messages.view-to-amazon');
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
				return view ( $this->folderName . 'add-europe-to-amazon' )->with ( $data );
			}	
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		if (!empty($request->post('search_by_agent_warehouse'))) {
			$searchByName = trim($request->post('search_by_agent_warehouse'));
			$likeData ['searchBy'] = $searchByName;
		}
		if( ( !empty($request->post('search_way_of_transport') ) )){
			$whereData['way_of_transport'] = ( $request->post('search_way_of_transport') );
		}
		if( ( !empty($request->post('search_book_by') ) ) && ( $request->post('search_book_by') ) ){
			$whereData['book_by'] = (int)Wild_tiger::decode($request->post('search_book_by'));
		}
		if( ( !empty($request->post('search_logistic_partner') ) ) ){
			$whereData['logistic_partner'] = (int)Wild_tiger::decode($request->post('search_logistic_partner'));
		}
		if( ( !empty($request->post('search_booking_from_date') ) )){
			$whereData['booking_form_date'] = ($request->post('search_booking_from_date'));
		}
		if( ( !empty($request->post('search_booking_to_date') ) )){
			$whereData['booking_to_date'] = ($request->post('search_booking_to_date'));
		}
		if( ( !empty($request->post('search_collection_from_date') ) )){
			$whereData['collection_form_date'] = ($request->post('search_collection_from_date'));
		}
		if( ( !empty($request->post('search_collection_to_date') ) ) ){
			$whereData['collection_to_date'] = ($request->post('search_collection_to_date'));
		}
		if(!empty($request->post('search_amazon_appointment_from_date') )){
			$whereData['appointment_from_date'] = ($request->post('search_amazon_appointment_from_date'));
		}
		if(!empty($request->post('search_amazon_appointment_to_date'))){
			$whereData['appointment_to_date'] = ($request->post('search_amazon_appointment_to_date'));
		}
		/* if(!empty($request->post('search_status'))){
			$whereData['status'] = (int)Wild_tiger::decode($request->post('search_status'));
		} */
		if(!empty($request->post('search_delivery_from_date') ) ){
			$whereData['delivery_from_date'] = ($request->post('search_delivery_from_date'));
		}
		if( ( !empty($request->post('search_delivery_to_date') ) ) && ( $request->post('search_delivery_to_date') ) ){
			$whereData['delivery_to_date'] = ($request->post('search_delivery_to_date'));
		}
		
		if(!empty($request->post('search_account_company'))){
			$whereData['account_company'] = (int)Wild_tiger::decode($request->post('search_account_company'));
		}
		if(!empty($request->post('search_from_warehouse') ) ){
			$whereData['from_warehouse'] = (int)Wild_tiger::decode($request->post('search_from_warehouse'));
		}
		if(!empty($request->post('search_to_location'))){
			$whereData['to_amazon_location'] = (int)Wild_tiger::decode($request->post('search_to_location'));
		}
		
		if(!empty($request->post('search_to_country_delivery'))){
			$whereData['to_country_delivery'] = (int)Wild_tiger::decode($request->post('search_to_country_delivery'));
		}
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
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		$exportTypeAction = (!empty($request->input('custom_export_type_action')) ? trim($request->input('custom_export_type_action')) : '');
		
		if ($exportAction == 'export') {
			
			$finalExportData = $summaryData = $warehouseWiseCountryDetails = $individualTabSummaryDetails =[];
			
			$whereData['count_record'] = true;
			
			if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
				$getExportRecordDetails = $this->crudModel->getRecordDetailsBaseOnDetailTable( $whereData, $likeData );
				$finalExportData['Summary'] = [];
			} else {
				//$getExportRecordDetails = $this->crudModel->getRecordDetails( $whereData, $likeData );
				$getExportRecordDetails = $this->reportModel->getTrackingGoodOutDetail($whereData, $likeData , [] );
			}
				
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					
					$rowExcelData = [];
					
					if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
						
						$masterInfo = (isset($getExportRecordDetail->countryToPortEurope) && !empty($getExportRecordDetail->countryToPortEurope) ? $getExportRecordDetail->countryToPortEurope : []);
						$logisticPartnerInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->logisticPartnerDetail) && !empty($masterInfo->logisticPartnerDetail) && isset($masterInfo->logisticPartnerDetail->logisticPartnerMaster) && !empty($masterInfo->logisticPartnerDetail->logisticPartnerMaster) ? $masterInfo->logisticPartnerDetail->logisticPartnerMaster : []);
						$countryInfo = (isset($getExportRecordDetail->country) && !empty($getExportRecordDetail->country) ? $getExportRecordDetail->country : []);
						$locationInfo = (isset($getExportRecordDetail->location) && !empty($getExportRecordDetail->location) ? $getExportRecordDetail->location : []);
						$warehouseInfo = (isset($getExportRecordDetail->warehouse) && !empty($getExportRecordDetail->warehouse) ? $getExportRecordDetail->warehouse : []);
						$accountCompanyInfo = (isset($getExportRecordDetail->accountCompany) && !empty($getExportRecordDetail->accountCompany) ? $getExportRecordDetail->accountCompany : []);
						$detailInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->detailInfo) && !empty($masterInfo->detailInfo) ? $masterInfo->detailInfo : []);
						
						$invoiceDetails = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->invoiceInfo) && !empty($masterInfo->invoiceInfo) ? $masterInfo->invoiceInfo : []);
						$totalTrasportPrice = (isset($invoiceDetails) && !empty($invoiceDetails) && count($invoiceDetails) > 0 ? collect($invoiceDetails)->sum('d_final_charge') : 0);
						
						
						/* $totalPallets = (isset($detailInfo) && !empty($detailInfo) && count($detailInfo) > 0 ? collect($invoiceDetails)->where('e_dimension', config('constants.PALLET'))->sum('i_no_of_pallet_box') : '');
						$totalBoxes = (isset($detailInfo) && !empty($detailInfo) && count($detailInfo) > 0 ? collect($invoiceDetails)->where('e_dimension', config('constants.BOX'))->sum('i_no_of_pallet_box') : ''); */
						
						$totalBoxesAndPallets = (isset($detailInfo) && !empty($detailInfo) && count($detailInfo) > 0 ? collect($detailInfo)->sum('i_no_of_pallet_box') : 0);
						
						$unitCost = ($totalTrasportPrice / (isset($totalBoxesAndPallets) && !empty($totalBoxesAndPallets) ? $totalBoxesAndPallets : 1));
						
						$finalUnitCost = (isset($unitCost) && !empty( $unitCost) ?  $unitCost : 0) * (isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ? $getExportRecordDetail->i_no_of_pallet_box : 0);
						$finalUnitCost = number_format($finalUnitCost, 2, '.', '');
						
						$wareHouseCode = (isset($warehouseInfo) && !empty($warehouseInfo) && isset($warehouseInfo->v_warehouse_code) && !empty($warehouseInfo->v_warehouse_code) ? $warehouseInfo->v_warehouse_code : '');
						$logisticPartner = (isset($logisticPartnerInfo) && !empty($logisticPartnerInfo) && isset($logisticPartnerInfo->v_logistic_partner_name) && !empty($logisticPartnerInfo->v_logistic_partner_name) ? $logisticPartnerInfo->v_logistic_partner_name . (isset($logisticPartnerInfo->v_partner_codes) && !empty($logisticPartnerInfo->v_partner_codes) ? ' (' . $logisticPartnerInfo->v_partner_codes . ')' : '') : '' );
						$countryCode = (isset($countryInfo) && !empty($countryInfo) && isset($countryInfo->v_country_code) && !empty($countryInfo->v_country_code) ? $countryInfo->v_country_code : '');
						
						$rowExcelData['sr_no'] = ++$excelIndex;
						$rowExcelData['logistic_ref_id'] = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_country_to_port_europe_record_no) && !empty($masterInfo->v_country_to_port_europe_record_no) ? $masterInfo->v_country_to_port_europe_record_no : '');
						$rowExcelData['fba_id'] = (isset($getExportRecordDetail->v_shipment_id) && !empty($getExportRecordDetail->v_shipment_id) ? $getExportRecordDetail->v_shipment_id : '');
						$rowExcelData['ref.'] = (isset($getExportRecordDetail->v_ref_id) && !empty($getExportRecordDetail->v_ref_id) ? $getExportRecordDetail->v_ref_id : '');
						$rowExcelData['carrier_company'] = (isset($logisticPartner) && !empty($logisticPartner) ? $logisticPartner : '');
						$rowExcelData['boxes'] = (isset($getExportRecordDetail->e_dimension) && !empty($getExportRecordDetail->e_dimension) && $getExportRecordDetail->e_dimension == config('constants.BOX') ? (isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ? $getExportRecordDetail->i_no_of_pallet_box : 0) : 0);
						$rowExcelData['pallets'] = (isset($getExportRecordDetail->e_dimension) && !empty($getExportRecordDetail->e_dimension) && $getExportRecordDetail->e_dimension == config('constants.PALLET') ? (isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ? $getExportRecordDetail->i_no_of_pallet_box : 0) : 0);
						$rowExcelData['country'] = (isset($countryCode) && !empty($countryCode) ? $countryCode : '');
						$rowExcelData['delivery_/_location'] = (isset($locationInfo) && !empty($locationInfo) && isset($locationInfo->v_warehouse_code) && !empty($locationInfo->v_warehouse_code) ? $locationInfo->v_warehouse_code : '');
						$rowExcelData['weight'] = (isset($getExportRecordDetail->d_weight) && !empty($getExportRecordDetail->d_weight) ? (float) $getExportRecordDetail->d_weight : '');
						$rowExcelData['account'] = (isset($accountCompanyInfo) && !empty($accountCompanyInfo) && isset($accountCompanyInfo->v_company_code) && !empty($accountCompanyInfo->v_company_code) ? $accountCompanyInfo->v_company_code : '');
						$rowExcelData['warehouse'] = (isset($wareHouseCode) && !empty($wareHouseCode) ? $wareHouseCode : '');
						$rowExcelData['cost_in_(gbp)'] = (isset($finalUnitCost) && !empty($finalUnitCost) ? $finalUnitCost : '');
						$rowExcelData['booking_date'] = (isset($getExportRecordDetail) && !empty($getExportRecordDetail) && isset($getExportRecordDetail->dt_booking_date) && !empty($getExportRecordDetail->dt_booking_date) ? clientDate($getExportRecordDetail->dt_booking_date) : '');
						$rowExcelData['collection_date'] = (isset($getExportRecordDetail) && !empty($getExportRecordDetail) && isset($getExportRecordDetail->dt_collection_date) && !empty($getExportRecordDetail->dt_collection_date) ? clientDate($getExportRecordDetail->dt_collection_date) : '');
						$rowExcelData['delivery_date'] = (isset($getExportRecordDetail) && !empty($getExportRecordDetail) && isset($getExportRecordDetail->dt_delivery_date) && !empty($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '');
						
						if(isset($wareHouseCode) && !empty($wareHouseCode) && isset($logisticPartner) && !empty($logisticPartner) && isset($countryCode) && !empty($countryCode)){
							$warehouseWiseCountryDetails[$wareHouseCode][] = $countryCode;
							$summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_box'] = (isset($summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_box']) && !empty($summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_box']) ? $summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_box'] : 0) + (isset($rowExcelData['boxes']) && !empty($rowExcelData['boxes']) ? $rowExcelData['boxes'] : 0);
							$summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_pallet'] = (isset($summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_pallet']) && !empty($summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_pallet']) ? $summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_-_pallet'] : 0) + (isset($rowExcelData['pallets']) && !empty($rowExcelData['pallets']) ? $rowExcelData['pallets'] : 0);
							$summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_total_cost'] = (isset($summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_total_cost']) && !empty($summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_total_cost']) ? $summaryData[$wareHouseCode][$logisticPartner][$countryCode . '_total_cost'] : 0) + (isset($rowExcelData['cost_in_(gbp)']) && !empty($rowExcelData['cost_in_(gbp)']) ? $rowExcelData['cost_in_(gbp)'] : 0);						
						}
						
						$transitDays = 0;
						if(isset($rowExcelData['collection_date']) && !empty($rowExcelData['collection_date']) && isset($rowExcelData['delivery_date']) && !empty($rowExcelData['delivery_date'])){
							$collectionDate = Carbon::parse(dbDate($rowExcelData['collection_date']));
							$deliveryDate = Carbon::parse(dbDate($rowExcelData['delivery_date']));
							
							$transitDays = $deliveryDate->diffInDays($collectionDate); 
						}
						
						$rowExcelData['transit_days'] = (isset($transitDays) && !empty($transitDays) ? $transitDays : '');
						
						$finalExportData['Report'][] = $rowExcelData;
						
						$individualTabSummaryDetails['Report'][2] =  (isset($individualTabSummaryDetails['Report'][2]) && !empty($individualTabSummaryDetails['Report'][2]) ? $individualTabSummaryDetails['Report'][2] : 0) + (isset($rowExcelData['boxes']) && !empty($rowExcelData['boxes']) ? $rowExcelData['boxes'] : 0);
						$individualTabSummaryDetails['Report'][3] =  (isset($individualTabSummaryDetails['Report'][3]) && !empty($individualTabSummaryDetails['Report'][3]) ? $individualTabSummaryDetails['Report'][3] : 0) + (isset($rowExcelData['pallets']) && !empty($rowExcelData['pallets']) ? $rowExcelData['pallets'] : 0);
						$individualTabSummaryDetails['Report'][4] =  (isset($individualTabSummaryDetails['Report'][4]) && !empty($individualTabSummaryDetails['Report'][4]) ? $individualTabSummaryDetails['Report'][4] : 0) + (isset($rowExcelData['cost_in_(gbp)']) && !empty($rowExcelData['cost_in_(gbp)']) ? $rowExcelData['cost_in_(gbp)'] : 0);
					} else {
						
						/*
						$allInvoiceDetails = ( isset($getExportRecordDetail->invoiceInfo) ? json_decode(json_encode($getExportRecordDetail->invoiceInfo),true) : [] );
						$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
						$paymentValue = $finalCharge;
						$invoiceReference = ( isset($getExportRecordDetail->detailInfo) ? json_decode(json_encode($getExportRecordDetail->detailInfo),true) : [] );
						$transferDetail = (!empty($invoiceReference[0]) ? $invoiceReference[0] : '');
						
						$rowExcelData['sr_no'] = ++$excelIndex;
						$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_country_to_port_europe_record_no) ?  ($getExportRecordDetail->v_country_to_port_europe_record_no) :'' );
						$rowExcelData['way_of_transport'] = (isset($getExportRecordDetail->e_transport_way) ? ($getExportRecordDetail->e_transport_way) : '' );
						$rowExcelData['book_by'] =  (isset($getExportRecordDetail->bookEmployeeInfo->v_name) ? $getExportRecordDetail->bookEmployeeInfo->v_name :'');
						$rowExcelData['logistic_partner'] = (isset($getExportRecordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name . ( isset($getExportRecordDetail->logisticPartnerDetail->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->logisticPartnerDetail->v_logistic_partner_code.')'  : '' ): '' );
						//$rowExcelData['booking_date'] = (isset($getExportRecordDetail->dt_booking_date) ?  clientDate($getExportRecordDetail->dt_booking_date) : '' );
						//$rowExcelData['collection_date'] = (isset($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) : '' );
						//$rowExcelData['delivery_date'] = (isset($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '' );
						$rowExcelData['tracking_no.'] = (isset($getExportRecordDetail->v_tracking_no) ?  ( $getExportRecordDetail->v_tracking_no )  : '' );
						//$rowExcelData['tracking_link'] = (isset($getExportRecordDetail->v_tracking_link) ?  ( $getExportRecordDetail->v_tracking_link )  : '' );
						//$rowExcelData['account'] = ( isset($transferDetail['account_company']['v_company_name']) ?  ( $transferDetail['account_company']['v_company_name'] ) : '' );
						//$rowExcelData['from_(_warehouse_)'] = ( isset($transferDetail['warehouse']['v_warehouse_name']) ?  ( $transferDetail['warehouse']['v_warehouse_name'] ) .(!empty($transferDetail['warehouse']['v_warehouse_code']) ? ' (' .$transferDetail['warehouse']['v_warehouse_code']. ')'  :'') : '' );
						//$rowExcelData['to_(_amazon_location_)'] = ( isset($transferDetail['location']['v_warehouse_name']) ?  ( $transferDetail['location']['v_warehouse_name'] ) .(!empty($transferDetail['location']['v_warehouse_code']) ? ' (' .$transferDetail['location']['v_warehouse_code']. ')'  :''): '' );
						//$rowExcelData['amazon_appointment_date'] = (isset($getExportRecordDetail->dt_amazon_shipment_date) ? clientDate($getExportRecordDetail->dt_amazon_shipment_date) : '' );
						$rowExcelData['status'] = (isset($getExportRecordDetail->statusInfo->v_status) ? ($getExportRecordDetail->statusInfo->v_status) : '');
						$rowExcelData['total_logistic_cost_('.config('constants.GOODS_OUT_GBP_CURRENCY').')'] = (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_GBP_CURRENCY')  : '' );
						$finalExportData[] = $rowExcelData;	
						*/
						
						$masterInfo = (isset($getExportRecordDetail->countryToPortEurope) && !empty($getExportRecordDetail->countryToPortEurope) ? $getExportRecordDetail->countryToPortEurope : []);
						$invoiceDetails = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->invoiceInfo) && !empty($masterInfo->invoiceInfo) ? $masterInfo->invoiceInfo : []);
						$detailInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->detailInfo) && !empty($masterInfo->detailInfo) ? $masterInfo->detailInfo : []);
						
						$totalTrasportPrice = (isset($invoiceDetails) && !empty($invoiceDetails) && count($invoiceDetails) > 0 ? collect($invoiceDetails)->sum('d_final_charge') : 0);
						
						$totalBoxesAndPallets = (isset($detailInfo) && !empty($detailInfo) && count($detailInfo) > 0 ? collect($detailInfo)->sum('i_no_of_pallet_box') : 0);
						
						$unitCost = ($totalTrasportPrice / (isset($totalBoxesAndPallets) && !empty($totalBoxesAndPallets) ? $totalBoxesAndPallets : 1));
						
						// pallet and box wise unit cost
						$finalUnitCost = (isset($unitCost) && !empty( $unitCost) ?  $unitCost : 0) * (isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ? $getExportRecordDetail->i_no_of_pallet_box : 0);
						$finalUnitCost = round($finalUnitCost, 2);
						
						// unit wise per unit cost
						$perUnitCost = $finalUnitCost / ( isset($getExportRecordDetail->v_units) && !empty($getExportRecordDetail->v_units) && ((float) ($getExportRecordDetail->v_units) > 0) ? (float) ($getExportRecordDetail->v_units)  : 1 );
						
						$rowExcelData = [];
						
						$encodeRecordId = Wild_tiger::encode($getExportRecordDetail->i_id);
						$masterInfo = (isset($getExportRecordDetail->countryToPortEurope) && !empty($getExportRecordDetail->countryToPortEurope) ? $getExportRecordDetail->countryToPortEurope : []);
						$accountCompanyinfo = (isset($getExportRecordDetail->accountCompany) && !empty($getExportRecordDetail->accountCompany) ? $getExportRecordDetail->accountCompany : []);
						$fromWhereHouseinfo = (isset($getExportRecordDetail->warehouse) && !empty($getExportRecordDetail->warehouse) ? $getExportRecordDetail->warehouse : []);
						$toWhereHouseInfo = (isset($getExportRecordDetail->location) && !empty($getExportRecordDetail->location) ? $getExportRecordDetail->location : []);
						$toCountryInfo = (isset($getExportRecordDetail->country) && !empty($getExportRecordDetail->country) ? $getExportRecordDetail->country : []);
						$bookByInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->bookEmployeeInfo) && !empty($masterInfo->bookEmployeeInfo) ? $masterInfo->bookEmployeeInfo : []);
						$logisticPartnerInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->logisticPartnerDetail) && !empty($masterInfo->logisticPartnerDetail) ? $masterInfo->logisticPartnerDetail : []);
						$logisticPartnerMasterInfo = (isset($logisticPartnerInfo) && !empty($logisticPartnerInfo) && isset($logisticPartnerInfo->logisticPartnerMaster) && !empty($logisticPartnerInfo->logisticPartnerMaster) ? $logisticPartnerInfo->logisticPartnerMaster : []);
						$transportInvoiceDetails = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->invoiceInfo) && !empty($masterInfo->invoiceInfo) ? $masterInfo->invoiceInfo : []);
							
						$totalTransportPrice = 0;
						/* if(isset($transportInvoiceDetails) && !empty($transportInvoiceDetails) && count($transportInvoiceDetails) > 0){
							foreach ($transportInvoiceDetails as $transportInvoiceDetail){
								$totalTransportPrice = $totalTransportPrice + (isset($transportInvoiceDetail->d_total_charge) && !empty($transportInvoiceDetail->d_total_charge) ? $transportInvoiceDetail->d_total_charge : 0);
							}
						} */
						
						$totalTransportPrice = (isset($transportInvoiceDetails) && !empty($transportInvoiceDetails) && count($transportInvoiceDetails) > 0 ? collect($transportInvoiceDetails)->sum('d_final_charge') : 0);
						
						$rowExcelData['sr_no'] = ++$excelIndex;
						$rowExcelData['entry_number'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_country_to_port_europe_record_no) && !empty($masterInfo->v_country_to_port_europe_record_no) ?  ($masterInfo->v_country_to_port_europe_record_no)  :'' );
						$rowExcelData['workflow_id'] = ( isset($getExportRecordDetail->v_workflow_id) && !empty($getExportRecordDetail->v_workflow_id) ?  ($getExportRecordDetail->v_workflow_id)  :'' );
						$rowExcelData['fba_id'] = ( isset($getExportRecordDetail->v_shipment_id) && !empty($getExportRecordDetail->v_shipment_id) ?  ($getExportRecordDetail->v_shipment_id)  :'' );
						$rowExcelData['account_name'] = ( isset($accountCompanyinfo) && !empty($accountCompanyinfo) && isset($accountCompanyinfo->v_company_name) && !empty($accountCompanyinfo->v_company_name) ?  ($accountCompanyinfo->v_company_name)  :'' );
						$rowExcelData['sku'] = ( isset($getExportRecordDetail->v_sku) && !empty($getExportRecordDetail->v_sku) ?  ($getExportRecordDetail->v_sku)  :'' );
						$rowExcelData['unit'] = ( isset($getExportRecordDetail->v_units) && !empty($getExportRecordDetail->v_units) ?  ($getExportRecordDetail->v_units)  :'' );
						$rowExcelData['shipment_currency'] = ( isset($getExportRecordDetail->shipmentCurrency->v_currency_code) && !empty($getExportRecordDetail->shipmentCurrency->v_currency_code) ? $getExportRecordDetail->shipmentCurrency->v_currency_code  : '' );
						$rowExcelData['shipment_value'] = ( isset($getExportRecordDetail->v_price) && !empty($getExportRecordDetail->v_price) ?  $getExportRecordDetail->v_price  :'' );
						$rowExcelData['box_/_pallet'] = ( isset($getExportRecordDetail->e_dimension) && !empty($getExportRecordDetail->e_dimension) ?  ($getExportRecordDetail->e_dimension)  :'' );
						$rowExcelData['no._of_box_/_pallet'] = ( isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ?  $getExportRecordDetail->i_no_of_pallet_box  :'' );
						$rowExcelData['packing_warehouse'] = ( isset($getExportRecordDetail->packingWarehouse) && !empty($getExportRecordDetail->packingWarehouse->v_warehouse_name) ?  $getExportRecordDetail->packingWarehouse->v_warehouse_name  :'' );
						$rowExcelData['from_warehouse'] = ( isset($fromWhereHouseinfo) && !empty($fromWhereHouseinfo) && isset($fromWhereHouseinfo->v_warehouse_name) && !empty($fromWhereHouseinfo->v_warehouse_name) ?  ($fromWhereHouseinfo->v_warehouse_name)  :'' );
						$rowExcelData['to_warehouse'] = ( isset($toWhereHouseInfo) && !empty($toWhereHouseInfo) && isset($toWhereHouseInfo->v_warehouse_name) && !empty($toWhereHouseInfo->v_warehouse_name) ?  ($toWhereHouseInfo->v_warehouse_name) . ( isset($toWhereHouseInfo->v_warehouse_code) ? ' ('.$toWhereHouseInfo->v_warehouse_code . ')' : '' )  :'' );
						$rowExcelData['to_country'] = ( isset($toCountryInfo) && !empty($toCountryInfo) && isset($toCountryInfo->v_country_name) && !empty($toCountryInfo->v_country_name) ?  ($toCountryInfo->v_country_name)  :'' );
						$rowExcelData['way_of_transport'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->e_transport_way) && !empty($masterInfo->e_transport_way) ?  ($masterInfo->e_transport_way)  :'' );
						$rowExcelData['book_by'] = ( isset($bookByInfo) && !empty($bookByInfo) && isset($bookByInfo->v_name) && !empty($bookByInfo->v_name) ?  ($bookByInfo->v_name)  :'' );
						$rowExcelData['logistic_partner'] = ( isset($logisticPartnerMasterInfo) && !empty($logisticPartnerMasterInfo) && isset($logisticPartnerMasterInfo->v_logistic_partner_name) && !empty($logisticPartnerMasterInfo->v_logistic_partner_name) ?  ($logisticPartnerMasterInfo->v_logistic_partner_name)  :'' );
						$rowExcelData['booking_date'] = ( isset($getExportRecordDetail) && !empty($getExportRecordDetail) && isset($getExportRecordDetail->dt_booking_date) && !empty($getExportRecordDetail->dt_booking_date) ?  clientDate($getExportRecordDetail->dt_booking_date)  :'' );
						$rowExcelData['collection_date'] = ( isset($getExportRecordDetail) && !empty($getExportRecordDetail) && isset($getExportRecordDetail->dt_collection_date) && !empty($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date)  :'' );
						$rowExcelData['delivery_date'] = ( isset($getExportRecordDetail) && !empty($getExportRecordDetail) && isset($getExportRecordDetail->dt_delivery_date) && !empty($getExportRecordDetail->dt_delivery_date) ?  clientDate($getExportRecordDetail->dt_delivery_date)  :'' );
						$rowExcelData['tracking_number'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_tracking_no) && !empty($masterInfo->v_tracking_no) ?  ($masterInfo->v_tracking_no)  :'' );
						$rowExcelData['transporter_invoice_cost_(gbp)'] = (isset($finalUnitCost) && !empty($finalUnitCost) ? $finalUnitCost : ''); //(isset($totalTransportPrice) && !empty($totalTransportPrice) ? ($totalTransportPrice) : '');
						
						$collectionVsDeliveryDateDays = 0;
						if(isset($rowExcelData['collection_date']) && !empty($rowExcelData['collection_date']) && isset($rowExcelData['delivery_date']) && !empty($rowExcelData['delivery_date'])){
							$collectionDate = Carbon::parse(dbDate($rowExcelData['collection_date']));
							$deliveryDate = Carbon::parse(dbDate($rowExcelData['delivery_date']));
								
							$collectionVsDeliveryDateDays = $deliveryDate->diffInDays($collectionDate);
						}
						
						$rowExcelData['collection_vs_delivery_date'] = (isset($collectionVsDeliveryDateDays) && !empty($collectionVsDeliveryDateDays) ? $collectionVsDeliveryDateDays : '');
						$rowExcelData['per_unit_cost'] = (isset($perUnitCost) && !empty($perUnitCost) ? round($perUnitCost, 2) : '');
						$rowExcelData['per_pallet_/_box_cost'] = (isset($unitCost) && !empty($unitCost) ? round($unitCost, 2) : '');
						
						$finalExportData[] = $rowExcelData;
					}
				}
			}
			
			foreach ($summaryData as $wareHouse => $summaryDetails){
				$warehouseCountries = (isset($warehouseWiseCountryDetails[$wareHouse]) && !empty($warehouseWiseCountryDetails[$wareHouse]) ? $warehouseWiseCountryDetails[$wareHouse] : []);
				$warehouseCountries = array_unique($warehouseCountries);
				
				foreach ($summaryDetails as $partner => $summaryInfo){
					foreach ($warehouseCountries as $warehouseCountry){
						if(!array_key_exists($warehouseCountry . '_-_box', $summaryInfo)){
							$summaryData[$wareHouse][$partner][$warehouseCountry . '_-_box'] = 0;
						}
						if(!array_key_exists($warehouseCountry . '_-_pallet', $summaryInfo)){
							$summaryData[$wareHouse][$partner][$warehouseCountry . '_-_pallet'] = 0;
						}
						if(!array_key_exists($warehouseCountry . '_total_cost', $summaryInfo)){
							$summaryData[$wareHouse][$partner][$warehouseCountry . '_total_cost'] = 0;
						}
					}
				}
			}
			
			$summaryTotalAmountWarehouseDetails = [];
			foreach ($summaryData as $wareHouse => $summaryDetails){
				foreach ($summaryDetails as $partner => $summaryDetail){
					$rowData = [];
					$rowData['partner'] = $partner;
					foreach ($summaryDetail as $key => $summaryInfo){
						$rowData[$key] = $summaryInfo;
						
						if(str_contains($key, 'total')){
							$summaryTotalAmountWarehouseDetails[$wareHouse][$key] = (isset($summaryTotalAmountWarehouseDetails[$wareHouse][$key]) && !empty($summaryTotalAmountWarehouseDetails[$wareHouse][$key]) ? $summaryTotalAmountWarehouseDetails[$wareHouse][$key] : 0) + (isset($summaryInfo) && !empty($summaryInfo) ? $summaryInfo : 0 ); 							
						}
					}
					
					$finalExportData['Summary'][$wareHouse][] = $rowData; 
				}
			}
			
			foreach ($summaryTotalAmountWarehouseDetails as $wareHouse => $summaryTotalAmountWarehouseDetail){
				$rowData = [];
				$rowData['partner'] = 'Total';

				foreach ($summaryTotalAmountWarehouseDetail as $key => $totalCost){
					$keyToArray = explode('_', $key);
					$countryCode = (isset($keyToArray) && !empty($keyToArray) && isset($keyToArray[0]) && !empty($keyToArray[0]) ? $keyToArray[0] : '');
					$rowData[$countryCode . '_-_box'] = '';
					$rowData[$countryCode . '_-_pallet'] = '';
					$rowData[$key] = $totalCost;		
				}
					
				$finalExportData['Summary'][$wareHouse][] = $rowData;
			}
			
			if($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT') && isset($finalExportData['Report']) && !empty($finalExportData['Report']) && count($finalExportData['Report']) > 0 && count($finalExportData['Report']) < 5){
				$emptyRow = 4 - count($finalExportData['Report']);
				if($emptyRow > 0){
					for ($i = 1; $i <= $emptyRow; $i++){
						$rowEmptyData = [];
						$rowEmptyData['sr_no'] = '';
						$rowEmptyData['logistic_ref_id'] = '';
						$rowEmptyData['fba_id'] = '';
						$rowEmptyData['ref.'] = '';
						$rowEmptyData['carrier_company'] = '';
						$rowEmptyData['boxes'] = '';
						$rowEmptyData['pallets'] = '';
						$rowEmptyData['country'] = '';
						$rowEmptyData['delivery_/_location'] = '';
						$rowEmptyData['weight'] = '';
						$rowEmptyData['account'] = '';
						$rowEmptyData['warehouse'] = '';
						$rowEmptyData['cost_in_(gbp)'] = '';
						$rowEmptyData['booking_date'] = '';
						$rowEmptyData['collection_date'] = '';
						$rowEmptyData['delivery_date'] = '';
						$rowEmptyData['transit_days'] = '';
						
						$finalExportData['Report'][] = $rowEmptyData;
					}					
				}
			}
		
			if ((!empty($finalExportData) && $exportTypeAction != config('constants.ACTION_SUMMARY_EXPORT')) || (!empty($finalExportData) && isset($finalExportData['Report']) && !empty($finalExportData['Report']))) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.to-amazon')]);
				
				//$fileName = str_replace("/", "-", $fileName);
				if ($exportTypeAction == config('constants.ACTION_SUMMARY_EXPORT')) {
					$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.europe-to-amazon-summary')]);
					
					$exportInfo = [
							'record_detail' => $finalExportData,
							'title' => trans('messages.to-amazo-excel-title'),
							'additional_summary' => isset($individualTabSummaryDetails) && !empty($individualTabSummaryDetails) ? $individualTabSummaryDetails : [],
					];
					
					$xlsData = $this->generateGoodOutSummarySpreadsheetMultiple($exportInfo);
				} else {
					$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.to-amazo-excel-title') ]);					
				}
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
		
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
		
			return Response::json($response);
		}
		$paginationData = [];
	
		$whereData['page'] = $page;
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData, $likeData );
	
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
	
		$totalRecords = $data['recordDetails']->total();
	
		if(isset($totalRecords)){
			$data['totalRecordCount'] = $totalRecords;
		}
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'europe-to-amazon/europe-to-amazon-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_TO_AMAZON')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$europeToAmazonDetailData = [];
			$europeToAmazonDetailData['t_is_active'] = 0;
			$europeToAmazonDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=>trans('messages.to-amazon')]);
			$errorMessages = trans('messages.error-delete',['module'=>trans('messages.to-amazon')]);
	
			DB::beginTransaction();
	
			$result = false;
			
			$whereData = [];
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			#get record 
			$europeToAmazonRecordDetails = $this->crudModel->getRecordDetails($whereData);
			try{
				$this->crudModel->deleteTableData(  config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE') ,  $europeToAmazonDetailData , [ 'i_country_to_port_europe_goods_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DOCUMENT_MASTER_TABLE') ,  $europeToAmazonDetailData , [ 'i_country_to_port_europe_goods_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_INVOICE_MASTER_TABLE') ,  $europeToAmazonDetailData , [ 'i_country_to_port_europe_goods_master_id' => $recordId ] );
	
				$this->crudModel->deleteTableData($this->tableName,  $europeToAmazonDetailData , [ 'i_id' => $recordId ] );
				if(!empty($europeToAmazonRecordDetails)){
					#get column 
					$europeToAmazonDetailDatailIds = ( isset($europeToAmazonRecordDetails->detailInfo) ?  array_column(objectToArray($europeToAmazonRecordDetails->detailInfo) , 'i_id') : []  );
					
					#delete Shipment Info table
					$europeToAmazonDetailData ['i_deleted_id'] = session()->get('user_id');
					$europeToAmazonDetailData ['dt_deleted_at'] = date('Y-m-d H:i:s');
					
					DB::table(config('constants.SHIPMENT_NO_INFO_TABLE'))->whereIn('i_ref_table_id', $europeToAmazonDetailDatailIds )->where('v_ref_record_type' ,config('constants.WAREHOUSE_TO_AMAZON') )->update($europeToAmazonDetailData);
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
	
	/* public function checkUniqueShipmentId(Request $request){
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0 );
		//$amazonShipmentId = (!empty($request->amazon_shipment_id) ? ($request->amazon_shipment_id) : '' );
		$validator = Validator::make ( $request->all (), [
				'amazon_shipment_id' => [ 'required' , new UniqueAmazonShimentId($recordId) ],
		], [
				'amazon_shipment_id.required' => __ ('messages.require-shipment-id'),
		] );
		
		$result = [];
		$result['status_code'] = 1;
		$result['message'] = trans('messages.success');
		if ($validator->fails ()) {
			$result['status_code'] = 101;
			$result['message'] = trans('messages.error-unique-shipment-id');
		}
		echo json_encode($result);die; 
	} */
}
