<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\UsWarehouseToAmazonMasterModel;
use App\LogisticPartnerDetailModel;
use App\Login;
use App\LogisticPartnerMasterModel;
use App\StatusMasterModel;
use App\CompanyMasterModel;
use App\WarehouseMasterModel;
use App\Document_Type_Master_Model;
use App\CustomerMasterModel;
use App\CurrencyMasterModel;
use App\CustomerDetailModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use DB;
use Illuminate\Support\Facades\Response;
use App\UsWarehouseToAmazonDetailsModel;
use App\CountryMasterModel;
class UsWarehouseToAmazonController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'us-warehouse-to-amazon/';
		$this->moduleName = trans('messages.us-warehouse-to-amazon-customer-uk-warehouse');
		$this->crudModel = new UsWarehouseToAmazonMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_URL');
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != true){
			return redirect('access-denied');
		}
		$data = $where = $whereData = [];
		$data ['pageTitle'] = trans('messages.us-warehouse-to-amazon-customer-uk-warehouse');
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [ config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['wayToWarehouseInfo'] = wayToWarehouseInfo();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['amazonFromWarehouseDetails'] = WarehouseMasterModel::where('i_country_id',config ( 'constants.USA'))->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whereData){
			$query;
		})->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['boxPalletTypeInfo'] = typeInfo();
		$statusIds = [];
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		$where['default_status'] = $statusIds;
		$data['statusInfo'] = $statusIds;
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
		
		
		return view($this->folderName . 'us-warehouse-to-amazon')->with($data);
	
	}
	public function create(Request $requset){
		if(checkPermission(config('permission_constants.ADD_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-us-warehouse-to-amazon-customer-uk-warehouse');
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [ config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['wayToWarehouseInfo'] = wayToWarehouseInfo();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->where('t_is_active',1)->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::where('t_is_active',1)->orderBy('i_sequence', 'ASC')->get();
		$data['companyMasterRecordDetails'] = CompanyMasterModel::where('t_is_active',1)->orderBy('v_company_name', 'ASC')->get();
		$data['amazonFromWarehouseDetails'] = WarehouseMasterModel::where('i_country_id',config ( 'constants.USA'))->where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['locationMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->where('t_is_active',1)->orderBy('v_document_type_name', 'ASC')->get();
		$data['customerRecordDetails'] = CustomerMasterModel::where('t_is_active',1)->orderBy('v_customer_name', 'ASC')->get();
		$whereData = [];
		$whereData['t_is_active'] = 1;
		$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whereData){
			$query->where('t_is_active',$whereData);
		})->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['ukWarehouseDetails'] = WarehouseMasterModel::whereNotIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['customerLocationRecordDetails'] = CustomerDetailModel::where('t_is_active',1)->orderBy('v_customer_code', 'ASC')->get();
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		$data['boxPalletTypeInfo'] = typeInfo();
		return view($this->folderName . 'add-us-warehouse-to-amazon')->with($data);
	}
	public function add(Request $request){
		
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['way_of_transport'] = 'required_unless:to,==,'.config('constants.AMAZON_FBA_SHEET');
		$formValidation['from'] = 'required';
		$formValidation['to'] = 'required';
		$formValidation['book_by'] = 'required_unless:to,!=,'.config('constants.AMAZON_FBA_SHEET');
		$formValidation['logistic_partner'] = 'required_unless:to,!=,'.config('constants.AMAZON_FBA_SHEET');
		//$formValidation['booking_date'] = 'required';
		//$formValidation['tracking_no'] = 'required';
		$formValidation['status'] = 'required';
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			//$formValidation['collection_date'] = 'required';
			//$formValidation['delivery_date'] = 'required';
		}
		//$formValidation['box_pallet_type'] = 'required';
		$validator = Validator::make ( $request->all (), $formValidation , [
				'way_of_transport.required_unless' => __ ( 'messages.require-way-of-transport' ),
				'book_by.required_unless' => __ ( 'messages.require-book-by' ),
				'from.required' => __ ( 'messages.require-from' ),
				'to.required' => __ ( 'messages.require-to' ),
				'logistic_partner.required_unless' => __ ( 'messages.require-logistic-partner' ),
				'booking_date.required' => __ ( 'messages.require-booking-date' ),
				'tracking_no.required' => __ ( 'messages.require-tracking-no' ),
				'status.required' => __ ( 'messages.require-status' ),
				'collection_date.required' => __ ( 'messages.require-collection-date' ),
				'delivery_date.required' => __ ( 'messages.require-delivery-date' ),
				'box_pallet_type.required' => __ ( 'messages.require-type' ),
		
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=> $this->moduleName ]);
		$errorMessages = trans('messages.error-create',['module'=> $this->moduleName ]);
		
		$usWarehouseDocumentTypeCount = (!empty($request->input('us_warehouse_to_amazon_document_type_count')) ? (int)($request->input('us_warehouse_to_amazon_document_type_count')) : 1 );
		$usWarehouseTransporterCount = (!empty($request->input('us_warehouse_to_amazon_transporter_count')) ? (int)($request->input('us_warehouse_to_amazon_transporter_count')) : 1 );
		$usWarehouseShipmentAmazonCount = (!empty($request->input('us_warehouse_to_amazon_shipment_amazon_count')) ? (int)($request->input('us_warehouse_to_amazon_shipment_amazon_count')) : 1 );
		$usWarehouseShipmentCustomerCount = (!empty($request->input('us_warehouse_to_amazon_shipment_customer_count')) ? (int)($request->input('us_warehouse_to_amazon_shipment_customer_count')) : 1 );
		$usWarehouseShipmentUkWarehouseCount = (!empty($request->input('us_warehouse_to_amazon_shipment_ukwarehouse_count')) ? (int)($request->input('us_warehouse_to_amazon_shipment_ukwarehouse_count')) : 1 );
		
		$usWarehouseTo = (!empty($request->input('to')) ? ($request->input('to')) : '' );
		
		DB::beginTransaction();
		try{
			$recordData = [];
			
			$recordData['i_from_warehouse_id'] = (!empty($request->input('from')) ? (int)Wild_tiger::decode($request->input('from')) : 0 );
			$recordData['e_to_location'] = $usWarehouseTo;
			$recordData['e_transport_way'] = $recordData['i_book_by_employee_id'] = $recordData['i_logistic_partner_detail_id'] = null;
			$recordData['v_personal_ref'] = (!empty($request->input('personal_ref')) ? trim($request->input('personal_ref')) : null );
			if ($recordData['e_to_location'] != config("constants.AMAZON_FBA_SHEET")){
				$recordData['e_transport_way'] = (!empty($request->input('way_of_transport')) ? ($request->input('way_of_transport')) : null );
				$recordData['i_book_by_employee_id'] = (!empty($request->input('book_by')) ? (int)Wild_tiger::decode($request->input('book_by')) : null );
				$recordData['i_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner')) ? (int)Wild_tiger::decode($request->input('logistic_partner')) : null );
			}
			
			//$recordData['dt_booking_date'] = (!empty($request->input('booking_date')) ? dbDate($request->input('booking_date')) : '' );
			//$recordData['dt_collection_date'] = (!empty($request->input('collection_date')) ? dbDate($request->input('collection_date')) : null );
			//$recordData['v_remarks'] = (!empty($request->input('remarks')) ? ($request->input('remarks')) : null );
			//$recordData['e_box_pallet_type'] = (!empty($request->input('box_pallet_type')) ? ($request->input('box_pallet_type')) : null);
			//$recordData['i_total_no_of_pallets'] = (!empty($request->input('total_no_of_pallets_boxes')) ? ($request->input('total_no_of_pallets_boxes')) : null);
			//$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? ($request->input('tracking_no')) : '');
			//$recordData['v_tracking_link'] = (!empty($request->input('tracking_link')) ? ($request->input('tracking_link')) : null );
			//$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
			//$recordData['dt_amazon_appointment_date'] = (!empty($request->input('amazon_appointment_date')) ? dbDate($request->input('amazon_appointment_date')) : null );
			//$recordData['v_amazon_appointment_id'] = (!empty($request->input('amazon_appointment_id')) ? ($request->input('amazon_appointment_id')) : null );
			$recordData['i_status_id'] = $statusRecordId;
			$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? ($request->input('status_comments')) : null );
			
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName ]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName ]);
				$whereData = [];
				$whereData['master_id'] = $recordId;
				$whereData['singleRecord'] = true;
				$usWarehouseRecordDetails = $this->crudModel->getRecordDetails($whereData);
			
				if(!empty($usWarehouseRecordDetails->usWarehouseToAmazonDetails)){
					foreach ($usWarehouseRecordDetails->usWarehouseToAmazonDetails as $usWarehouseShipmentRecordDetail){
						$decodedId = [];
						$usWarehouseShipmentRecordId = $usWarehouseShipmentRecordDetail->i_id;
						$shipmentAmazonId = (!empty($request->input('edit_shipment_id_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_shipment_id_'.$usWarehouseShipmentRecordId) : null );
						$invoiceNoRefNoUkWarehouse = (!empty($request->input('edit_invoice_no_ref_no_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_invoice_no_ref_no_'.$usWarehouseShipmentRecordId) : null );
						$invoiceNoCustomer = (!empty($request->input('edit_invoice_no_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_invoice_no_'.$usWarehouseShipmentRecordId) : null );
						if((!empty($shipmentAmazonId)) || (!empty($invoiceNoRefNoUkWarehouse)) || (!empty($invoiceNoCustomer))){
							
							$usWarehouseDetails = [];
							switch ($usWarehouseTo){
								case config('constants.AMAZON_FBA_SHEET'):
									//$recordData['v_tracking_no'] = (!empty($request->input('edit_amazon_tracking_no_'.$usWarehouseShipmentRecordId)) ? ($request->input('edit_amazon_tracking_no_'.$usWarehouseShipmentRecordId)) : null);
									$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
									
									$usWarehouseDetails['v_shipment_id'] = (!empty($request->input('edit_shipment_id_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_shipment_id_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['v_ref_id'] = (!empty($request->input('edit_ref_id_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_ref_id_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['i_account_company_id'] = (!empty($request->input('edit_account_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_account_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['i_amazon_from_warehouse_id'] = (!empty($request->input('edit_amazon_from_warehouse_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_amazon_from_warehouse_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['i_to_amazon_location_id'] = (!empty($request->input('edit_to_amazon_location_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_to_amazon_location_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['v_product'] = (!empty($request->input('edit_product_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_product_'.$usWarehouseShipmentRecordId) : '' );
									$usWarehouseDetails['v_sku'] = (!empty($request->input('edit_sku_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_sku_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['v_units'] = (!empty($request->input('edit_unit_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_unit_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['d_price'] = (!empty($request->input('edit_price_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_price_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['dt_booking_date'] = (!empty($request->input('edit_amazon_booking_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_amazon_booking_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['dt_collection_date'] = (!empty($request->input('edit_amazon_collection_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_amazon_collection_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['dt_delivery_date'] = (!empty($request->input('edit_amazon_delivery_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_amazon_delivery_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['v_remarks'] = (!empty($request->input('edit_amazon_remarks_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_amazon_remarks_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['v_tracking_link'] = (!empty($request->input('edit_amazon_tracking_link_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_amazon_tracking_link_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['dt_amazon_appointment_date'] = (!empty($request->input('edit_amazon_appointment_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_amazon_appointment_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['v_amazon_appointment_id'] = (!empty($request->input('edit_amazon_appointment_id_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_amazon_appointment_id_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['e_box_pallet_type'] = (!empty($request->input('edit_amazon_box_pallet_type_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_amazon_box_pallet_type_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['i_total_no_of_pallets'] = (!empty($request->input('edit_amazon_total_no_of_pallets_boxes_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_amazon_total_no_of_pallets_boxes_'.$usWarehouseShipmentRecordId) : null);
										
									break;
								case config('constants.CUSTOMER_FBA_SHEET'):
									//$recordData['v_tracking_no'] = (!empty($request->input('edit_customer_tracking_no_'.$usWarehouseShipmentRecordId)) ? ($request->input('edit_customer_tracking_no_'.$usWarehouseShipmentRecordId)) : null);
									$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
										
									$usWarehouseDetails['v_shipment_invoice_no'] = (!empty($request->input('edit_invoice_no_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_invoice_no_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['i_customer_id'] = (!empty($request->input('edit_customer_name_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_customer_name_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['i_customer_from_warehouse_id'] = (!empty($request->input('edit_customer_from_warehouse_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_customer_from_warehouse_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['i_to_customer_id'] = (!empty($request->input('edit_to_customer_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_to_customer_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['v_customer_unit'] = (!empty($request->input('edit_customer_unit_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_customer_unit_'.$usWarehouseShipmentRecordId) : null );
									//$usWarehouseDetails['v_box_pallet'] = (!empty($request->input('edit_box_pallet_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_box_pallet_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['dt_booking_date'] = (!empty($request->input('edit_customer_booking_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_customer_booking_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['dt_collection_date'] = (!empty($request->input('edit_customer_collection_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_customer_collection_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['dt_delivery_date'] = (!empty($request->input('edit_customer_delivery_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_customer_delivery_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['v_remarks'] = (!empty($request->input('edit_customer_remarks_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_customer_remarks_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['v_tracking_link'] = (!empty($request->input('edit_customer_tracking_link_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_customer_tracking_link_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['e_box_pallet_type'] = (!empty($request->input('edit_customer_box_pallet_type_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_customer_box_pallet_type_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['i_total_no_of_pallets'] = (!empty($request->input('edit_customer_total_no_of_pallets_boxes_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_customer_total_no_of_pallets_boxes_'.$usWarehouseShipmentRecordId) : null);
										
									break;
								case config('constants.UK_WAREHOUSE_FBA_SHEET'):
									//$recordData['v_tracking_no'] = (!empty($request->input('edit_warehouse_tracking_no_'.$usWarehouseShipmentRecordId)) ? ($request->input('edit_warehouse_tracking_no_'.$usWarehouseShipmentRecordId)) : null);
									$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
										
									$usWarehouseDetails['v_invoice_no_ref_no'] = (!empty($request->input('edit_invoice_no_ref_no_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_invoice_no_ref_no_'.$usWarehouseShipmentRecordId) : null );
									$ukAccountCompanyIds = (!empty($request->input('edit_uk_account_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_uk_account_'.$usWarehouseShipmentRecordId) : [] );
									if(!empty($ukAccountCompanyIds)){
										foreach ($ukAccountCompanyIds as $ukAccountCompanyId){
											$decodedId[] = (int)Wild_tiger::decode($ukAccountCompanyId);
										}
									}
									$usWarehouseDetails['v_uk_account_ids'] = (!empty($decodedId) ? implode(',', $decodedId) : null);
									$usWarehouseDetails['i_uk_from_warehouse_id'] = (!empty($request->input('edit_uk_from_warehouse_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_uk_from_warehouse_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['i_uk_to_warehouse_id'] = (!empty($request->input('edit_uk_to_warehouse_'.$usWarehouseShipmentRecordId)) ? (int)Wild_tiger::decode($request->input('edit_uk_to_warehouse_'.$usWarehouseShipmentRecordId)) : 0 );
									$usWarehouseDetails['v_uk_unit'] = (!empty($request->input('edit_uk_unit_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_uk_unit_'.$usWarehouseShipmentRecordId) : null );
									//$usWarehouseDetails['v_uk_box_pallet'] = (!empty($request->input('edit_uk_box_pallet_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_uk_box_pallet_'.$usWarehouseShipmentRecordId) : null );
									$usWarehouseDetails['dt_booking_date'] = (!empty($request->input('edit_warehouse_booking_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_warehouse_booking_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['dt_collection_date'] = (!empty($request->input('edit_warehouse_collection_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_warehouse_collection_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['dt_delivery_date'] = (!empty($request->input('edit_warehouse_delivery_date_'.$usWarehouseShipmentRecordId)) ? dbDate($request->input('edit_warehouse_delivery_date_'.$usWarehouseShipmentRecordId)) : null);
									$usWarehouseDetails['v_remarks'] = (!empty($request->input('edit_warehouse_remarks_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_warehouse_remarks_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['v_tracking_link'] = (!empty($request->input('edit_warehouse_tracking_link_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_warehouse_tracking_link_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['e_box_pallet_type'] = (!empty($request->input('edit_warehouse_box_pallet_type_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_warehouse_box_pallet_type_'.$usWarehouseShipmentRecordId) : null);
									$usWarehouseDetails['i_total_no_of_pallets'] = (!empty($request->input('edit_warehouse_total_no_of_pallets_boxes_'.$usWarehouseShipmentRecordId)) ? $request->input('edit_warehouse_total_no_of_pallets_boxes_'.$usWarehouseShipmentRecordId) : null);
										
							}
							if((!empty($usWarehouseDetails['v_shipment_id'])) || (!empty($usWarehouseDetails['v_shipment_invoice_no'])) || (!empty($usWarehouseDetails['v_invoice_no_ref_no']))){
								$usToAmazonDetailUpdate = $this->crudModel->updateTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $usWarehouseDetails , [ 'i_id' => $usWarehouseShipmentRecordId] );
							}
							
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $deleteRecordData , [ 'i_id' => $usWarehouseShipmentRecordId] );
								
						}
					}
				}
				if(!empty($usWarehouseRecordDetails->documentInfo)){
					foreach ($usWarehouseRecordDetails->documentInfo as $usWarehouseDocumentDetail){
						$usWarehouseDocumentDetailId = $usWarehouseDocumentDetail->i_id;
						if(!empty($request->input('edit_type_'.$usWarehouseDocumentDetailId))){
							$usWarehouseDocument = [];
							$usWarehouseDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$usWarehouseDocumentDetailId)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$usWarehouseDocumentDetailId)) :0);
							$usWarehouseDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$usWarehouseDocumentDetailId)) ? $request->input('edit_remarks_'.$usWarehouseDocumentDetailId) : null);
				
							if($request->hasFile('edit_file_'.$usWarehouseDocumentDetailId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$usWarehouseDocumentDetailId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$usWarehouseDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
								$removeFiles = (!empty($request->input('remove_document_'.$usWarehouseDocumentDetailId)) ? explode("," , $request->input('remove_document_'.$usWarehouseDocumentDetailId) ) : []  );
								$previousUploadFiles = (!empty($usWarehouseDocumentDetail->v_document_file_path) ? json_decode($usWarehouseDocumentDetail->v_document_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$usWarehouseDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if((!empty($usWarehouseDocument ['i_document_type_id']))){
								$usWarehouseDocumentDetailUpdate = $this->crudModel->updateTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DOCUMENT_MASTER_TABLE') , $usWarehouseDocument , [ 'i_id' => $usWarehouseDocumentDetailId] );
				
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DOCUMENT_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $usWarehouseDocumentDetailId] );
						}
					}
				}
				if(!empty($usWarehouseRecordDetails->invoiceInfo)){
					foreach ($usWarehouseRecordDetails->invoiceInfo as $usWarehouseInvoiceDetail){
				
						$usWarehouseInvoiceRecordId = $usWarehouseInvoiceDetail->i_id;
						if(!empty($request->input('edit_name_'.$usWarehouseInvoiceRecordId))){
							$usWarehouseInvoice = [];
							$usWarehouseInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$usWarehouseInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$usWarehouseInvoiceRecordId)) : 0 );
							$usWarehouseInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_inv_no_'.$usWarehouseInvoiceRecordId) :'' );
							$usWarehouseInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_freight_'.$usWarehouseInvoiceRecordId) : 0 );
							$usWarehouseInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_custom_'.$usWarehouseInvoiceRecordId) :0 );
							$usWarehouseInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_duty_'.$usWarehouseInvoiceRecordId)  : 0 );
							$usWarehouseInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_other_'.$usWarehouseInvoiceRecordId) :0 );
							$usWarehouseInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_vat_'.$usWarehouseInvoiceRecordId) : 0 );
							$totalCharges = $usWarehouseInvoice['d_freight_charge'] + $usWarehouseInvoice['d_custom_charge'] + $usWarehouseInvoice['d_duty_charge'] + $usWarehouseInvoice['d_other_charge'] + $usWarehouseInvoice['d_vat_charge'];
							$usWarehouseInvoice['d_total_charge'] = $totalCharges;
							$usWarehouseInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_currency_id_'.$usWarehouseInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_currency_id_'.$usWarehouseInvoiceRecordId)) : 0);
							$usWarehouseInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$usWarehouseInvoiceRecordId)) ? $request->input('edit_cov_rate_'.$usWarehouseInvoiceRecordId) : 0);
							$finalCharges = ($totalCharges * $usWarehouseInvoice['d_conversion_rate']);
							$usWarehouseInvoice['d_final_charge'] = (!empty($finalCharges) ? round($finalCharges,2) : 0.00 );
				
							if($request->hasFile('edit_invoice_file_'.$usWarehouseInvoiceRecordId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$usWarehouseInvoiceRecordId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$usWarehouseInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
				
								$removeFiles = (!empty($request->input('remove_invoice_'.$usWarehouseInvoiceRecordId)) ? explode("," , $request->input('remove_invoice_'.$usWarehouseInvoiceRecordId) ) : []  );
								$previousUploadFiles = (!empty($usWarehouseInvoiceDetail->v_invoice_file_path) ? json_decode($usWarehouseInvoiceDetail->v_invoice_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$usWarehouseInvoice['v_invoice_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if(( $usWarehouseInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($usWarehouseInvoice['v_invoice_no']) ) ){
								$usWarehouseInvoiceUpdate = $this->crudModel->updateTableData( config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE') , $usWarehouseInvoice , [ 'i_id' => $usWarehouseInvoiceRecordId] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $usWarehouseInvoiceRecordId] );
						}
					}
				}
				//$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
				
			} else {
				
				//$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
					
			}
			/* if( $insertRecord > 0 ){
				$result = true;
			} */
			//die;
			$amazonShipmentDetails = [];
			for($i = 1; $i<= $usWarehouseShipmentAmazonCount; $i++){
				
				$rowData = [];
				$rowData['shipment_id'] = (!empty($request->input('shipment_id_'.$i)) ? $request->input('shipment_id_'.$i) : null);
				$rowData['ref_id'] = (!empty($request->input('ref_id_'.$i)) ? $request->input('ref_id_'.$i) : null);
				$rowData['account_company_id'] = (!empty($request->input('account_'.$i)) ? (int)Wild_tiger::decode($request->input('account_'.$i)) : 0);
				$rowData['amazon_from_warehouse_id'] = (!empty($request->input('amazon_from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('amazon_from_warehouse_'.$i)) : 0);
				$rowData['to_amazon_location_id'] = (!empty($request->input('to_amazon_location_'.$i)) ? (int)Wild_tiger::decode($request->input('to_amazon_location_'.$i)) : 0);
				$rowData['product'] = (!empty($request->input('product_'.$i)) ? $request->input('product_'.$i) : '');
				$rowData['sku'] = (!empty($request->input('sku_'.$i)) ? $request->input('sku_'.$i) : null);
				$rowData['units'] = (!empty($request->input('unit_'.$i)) ? $request->input('unit_'.$i) : null);
				$rowData['price'] = (!empty($request->input('price_'.$i)) ? $request->input('price_'.$i) : null);
				$rowData['amazon_booking_date'] = (!empty($request->input('amazon_booking_date_'.$i)) ? $request->input('amazon_booking_date_'.$i) : null);
				$rowData['amazon_collection_date'] = (!empty($request->input('amazon_collection_date_'.$i)) ? $request->input('amazon_collection_date_'.$i) : null);
				$rowData['amazon_delivery_date'] = (!empty($request->input('amazon_delivery_date_'.$i)) ? $request->input('amazon_delivery_date_'.$i) : null);
				$rowData['amazon_remarks'] = (!empty($request->input('amazon_remarks_'.$i)) ? $request->input('amazon_remarks_'.$i) : null);
				$rowData['amazon_tracking_no'] = (!empty($request->input('amazon_tracking_no_'.$i)) ? $request->input('amazon_tracking_no_'.$i) : null);
				$rowData['amazon_tracking_link'] = (!empty($request->input('amazon_tracking_link_'.$i)) ? $request->input('amazon_tracking_link_'.$i) : null);
				$rowData['amazon_appointment_date'] = (!empty($request->input('amazon_appointment_date_'.$i)) ? $request->input('amazon_appointment_date_'.$i) : null);
				$rowData['amazon_appointment_id'] = (!empty($request->input('amazon_appointment_id_'.$i)) ? $request->input('amazon_appointment_id_'.$i) : null);
				$rowData['amazon_box_pallet_type_'] = (!empty($request->input('amazon_box_pallet_type_'.$i)) ? $request->input('amazon_box_pallet_type_'.$i) : null);
				$rowData['amazon_pallets_boxes_'] = (!empty($request->input('amazon_total_no_of_pallets_boxes_'.$i)) ? $request->input('amazon_total_no_of_pallets_boxes_'.$i) : null);
				
				## shipment id nai hoi and tracking number hase to master ma entry thai jati hati but child ma entry nati padti so ana lidhe condition muki che
				if(!empty($rowData['shipment_id'])){
					$amazonShipmentDetails[$rowData['amazon_tracking_no']][] = $rowData;
				}
			}
			
			
			$customerShipmentDetails = [];
			for($i = 1; $i<= $usWarehouseShipmentCustomerCount; $i++){
				$rowData = [];
				$rowData['shipment_invoice_no'] = (!empty($request->input('invoice_no_'.$i)) ? $request->input('invoice_no_'.$i) : null);
				$rowData['customer_id'] = (!empty($request->input('customer_name_'.$i)) ? (int)Wild_tiger::decode($request->input('customer_name_'.$i)) : 0);
				$rowData['customer_from_warehouse'] = (!empty($request->input('customer_from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('customer_from_warehouse_'.$i)) : 0);
				$rowData['to_customer_id'] = (!empty($request->input('to_customer_'.$i)) ? (int)Wild_tiger::decode($request->input('to_customer_'.$i)) : 0);
				$rowData['customer_unit'] = (!empty($request->input('customer_unit_'.$i)) ? $request->input('customer_unit_'.$i) : null);
				//$rowData['box_pallet'] = (!empty($request->input('box_pallet_'.$i)) ? $request->input('box_pallet_'.$i) : null);
				$rowData['customer_booking_date'] = (!empty($request->input('customer_booking_date_'.$i)) ? dbDate($request->input('customer_booking_date_'.$i)) : null);
				$rowData['customer_collection_date'] = (!empty($request->input('customer_collection_date_'.$i)) ? dbDate($request->input('customer_collection_date_'.$i)) : null);
				$rowData['customer_delivery_date'] = (!empty($request->input('customer_delivery_date_'.$i)) ? dbDate($request->input('customer_delivery_date_'.$i)) : null);
				$rowData['customer_remarks'] = (!empty($request->input('customer_remarks_'.$i)) ? $request->input('customer_remarks_'.$i) : null);
				$rowData['customer_tracking_no'] = (!empty($request->input('customer_tracking_no_'.$i)) ? $request->input('customer_tracking_no_'.$i) : null);
				$rowData['customer_tracking_link'] = (!empty($request->input('customer_tracking_link_'.$i)) ? $request->input('customer_tracking_link_'.$i) : null);
				$rowData['customer_box_pallet_type_'] = (!empty($request->input('customer_box_pallet_type_'.$i)) ? $request->input('customer_box_pallet_type_'.$i) : null);
				$rowData['customer_pallets_boxes_'] = (!empty($request->input('customer_total_no_of_pallets_boxes_'.$i)) ? $request->input('customer_total_no_of_pallets_boxes_'.$i) : null);

				## shipment invoice no nai hoi and tracking number hase to master ma entry thai jati hati but child ma entry nati padti so ana lidhe condition muki che
				if(!empty($rowData['shipment_invoice_no'])){
					$customerShipmentDetails[$rowData['customer_tracking_no']][] = $rowData;
				}
			}
			
			$warehouseShipmentDetails = [];
			for($i = 1; $i<= $usWarehouseShipmentUkWarehouseCount; $i++){
				$rowData = $decodedId = [];
				$rowData['invoice_no_ref_no'] = (!empty($request->input('invoice_no_ref_no_'.$i)) ? $request->input('invoice_no_ref_no_'.$i) : null);
				$ukAccountCompanyIds = (!empty($request->input('uk_account_'.$i)) ? $request->input('uk_account_'.$i) : []);
				if(!empty($ukAccountCompanyIds)){
					foreach ($ukAccountCompanyIds as $ukAccountCompanyId){
						$decodedId[] = (int)Wild_tiger::decode($ukAccountCompanyId);
					}
				}
				$rowData['uk_account_ids'] = (!empty($decodedId) ? implode(',', $decodedId) : null);
				$rowData['uk_from_warehouse_id'] = (!empty($request->input('uk_from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('uk_from_warehouse_'.$i)) : 0);
				$rowData['uk_to_warehouse_id'] = (!empty($request->input('uk_to_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('uk_to_warehouse_'.$i)) : 0);
				$rowData['uk_unit'] = (!empty($request->input('uk_unit_'.$i)) ? $request->input('uk_unit_'.$i) : null);
				//$rowData['uk_box_pallet'] = (!empty($request->input('uk_box_pallet_'.$i)) ? $request->input('uk_box_pallet_'.$i) : null);
				$rowData['warehouse_booking_date'] = (!empty($request->input('warehouse_booking_date_'.$i)) ? dbDate($request->input('warehouse_booking_date_'.$i)) : null);
				$rowData['warehouse_collection_date'] = (!empty($request->input('warehouse_collection_date_'.$i)) ? dbDate($request->input('warehouse_collection_date_'.$i)) : null);
				$rowData['warehouse_delivery_date'] = (!empty($request->input('warehouse_delivery_date_'.$i)) ? dbDate($request->input('warehouse_delivery_date_'.$i)) : null);
				$rowData['warehouse_remarks'] = (!empty($request->input('warehouse_remarks_'.$i)) ? $request->input('warehouse_remarks_'.$i) : null);
				$rowData['warehouse_tracking_no'] = (!empty($request->input('warehouse_tracking_no_'.$i)) ? $request->input('warehouse_tracking_no_'.$i) : null);
				$rowData['warehouse_tracking_link'] = (!empty($request->input('warehouse_tracking_link_'.$i)) ? $request->input('warehouse_tracking_link_'.$i) : null);
				$rowData['warehouse_box_pallet_type_'] = (!empty($request->input('warehouse_box_pallet_type_'.$i)) ? $request->input('warehouse_box_pallet_type_'.$i) : null);
				$rowData['warehouse_pallets_boxes_'] = (!empty($request->input('warehouse_total_no_of_pallets_boxes_'.$i)) ? $request->input('warehouse_total_no_of_pallets_boxes_'.$i) : null);
				
				## invoice no ref no nai hoi and tracking number hase to master ma entry thai jati hati but child ma entry nati padti so ana lidhe condition muki che
				if(!empty($rowData['invoice_no_ref_no'])){
					$warehouseShipmentDetails[$rowData['warehouse_tracking_no']][] = $rowData;
				}
			}
			
			if(!empty($usWarehouseTo)){
				switch($usWarehouseTo){
					case config('constants.AMAZON_FBA_SHEET'):
						if(!empty($amazonShipmentDetails)){
							foreach ($amazonShipmentDetails as $amazonKey =>$amazonShipmentDetail){
								$usWarehouseGenerateNo = $this->generateUsWarehouseNo(config('constants.US_WAREHOUSE_TO_AMAZON'));
								$recordData['v_us_warehouse_to_amazon_record_no'] = $usWarehouseGenerateNo;
								$recordData['v_tracking_no'] = (!empty($amazonKey) ? $amazonKey : null);
								if($recordId > 0 ){
									
								} else {
									if(!empty($recordData['v_tracking_no'])){
										$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
									}
								} 
								foreach ($amazonShipmentDetail as $amazonShipmentInfo){
									/* if($recordId > 0 ){
											
									} else {
										if((!empty($recordData['v_tracking_no']))){
											$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
										}
									} */
									$rowData = [];
									$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
									$rowData['v_shipment_id'] = (!empty($amazonShipmentInfo['shipment_id']) ? $amazonShipmentInfo['shipment_id'] : null);
									$rowData['v_ref_id'] = (!empty($amazonShipmentInfo['ref_id']) ? $amazonShipmentInfo['ref_id'] : null);
									$rowData['i_account_company_id'] = (!empty($amazonShipmentInfo['account_company_id']) ? $amazonShipmentInfo['account_company_id'] : null);
									$rowData['i_amazon_from_warehouse_id'] = (!empty($amazonShipmentInfo['amazon_from_warehouse_id']) ? $amazonShipmentInfo['amazon_from_warehouse_id'] : null);
									$rowData['i_to_amazon_location_id'] = (!empty($amazonShipmentInfo['to_amazon_location_id']) ? $amazonShipmentInfo['to_amazon_location_id'] : null);
									$rowData['v_product'] = (!empty($amazonShipmentInfo['product']) ? $amazonShipmentInfo['product'] : '');
									$rowData['v_sku'] = (!empty($amazonShipmentInfo['sku']) ? $amazonShipmentInfo['sku'] : null);
									$rowData['v_units'] = (!empty($amazonShipmentInfo['units']) ? $amazonShipmentInfo['units'] : null);
									$rowData['d_price'] = (!empty($amazonShipmentInfo['price']) ? $amazonShipmentInfo['price'] : null);
									$rowData['dt_booking_date'] = (!empty($amazonShipmentInfo['amazon_booking_date']) ? dbDate($amazonShipmentInfo['amazon_booking_date']) : null);
									$rowData['dt_collection_date'] = (!empty($amazonShipmentInfo['amazon_collection_date']) ? dbDate($amazonShipmentInfo['amazon_collection_date']) : null);
									$rowData['dt_delivery_date'] = (!empty($amazonShipmentInfo['amazon_delivery_date']) ? dbDate($amazonShipmentInfo['amazon_delivery_date']) : null);
									$rowData['v_remarks'] = (!empty($amazonShipmentInfo['amazon_remarks']) ? $amazonShipmentInfo['amazon_remarks'] : null);
									$rowData['v_tracking_link'] = (!empty($amazonShipmentInfo['amazon_tracking_link']) ? $amazonShipmentInfo['amazon_tracking_link'] : null);
									$rowData['dt_amazon_appointment_date'] = (!empty($amazonShipmentInfo['amazon_appointment_date']) ? dbDate($amazonShipmentInfo['amazon_appointment_date']) : null);
									$rowData['v_amazon_appointment_id'] = (!empty($amazonShipmentInfo['amazon_appointment_id']) ? $amazonShipmentInfo['amazon_appointment_id'] : null);
									$rowData['e_box_pallet_type'] = (!empty($amazonShipmentInfo['amazon_box_pallet_type_']) ? ($amazonShipmentInfo['amazon_box_pallet_type_']) : null);
									$rowData['i_total_no_of_pallets'] = (!empty($amazonShipmentInfo['amazon_pallets_boxes_']) ? $amazonShipmentInfo['amazon_pallets_boxes_'] : null);
									
									if(!empty($rowData['v_shipment_id'])){
										$insertShipmentAmazonDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $rowData);
										$fbaSheetData = [];
										$fbaSheetData['v_shipment_no'] = $rowData['v_shipment_id'];
										$fbaSheetData['i_ref_table_id'] = $insertShipmentAmazonDetail;
										$fbaSheetData['v_ref_record_type'] = config('constants.US_WAREHOUSE_TO_AMAZON');
										$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
									}
								}
									
							}
						}
					break;
					case config('constants.CUSTOMER_FBA_SHEET'):
						if(!empty($customerShipmentDetails)){
							foreach ($customerShipmentDetails as $customerKey => $customerShipmentDetail){
								$usWarehouseGenerateNo = $this->generateUsWarehouseNo(config('constants.US_WAREHOUSE_TO_CUSTOMER'));
								$recordData['v_us_warehouse_to_amazon_record_no'] = $usWarehouseGenerateNo;
								$recordData['v_tracking_no'] = (!empty($customerKey) ? $customerKey : null);
								if($recordId > 0 ){
										
								} else {
									if(!empty($recordData['v_tracking_no'])){
										$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
									}
								} 
								foreach ($customerShipmentDetail as $customerShipmentInfo){
									/* if($recordId > 0 ){
									
									} else {
										if((!empty($recordData['v_tracking_no']))){
											$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
										}
									} */
									$rowData = [];
									$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
									$rowData['v_shipment_invoice_no'] = (!empty($customerShipmentInfo['shipment_invoice_no']) ? $customerShipmentInfo['shipment_invoice_no'] : null);
									$rowData['i_customer_id'] = (!empty($customerShipmentInfo['customer_id']) ? $customerShipmentInfo['customer_id'] : null);
									$rowData['i_customer_from_warehouse_id'] = (!empty($customerShipmentInfo['customer_from_warehouse']) ? $customerShipmentInfo['customer_from_warehouse'] : null);
									$rowData['i_to_customer_id'] = (!empty($customerShipmentInfo['to_customer_id']) ? $customerShipmentInfo['to_customer_id'] : null);
									$rowData['v_customer_unit'] = (!empty($customerShipmentInfo['customer_unit']) ? $customerShipmentInfo['customer_unit'] : null);
									//$rowData['v_box_pallet'] = (!empty($customerShipmentInfo['box_pallet']) ? $customerShipmentInfo['box_pallet'] : null);
									$rowData['dt_booking_date'] = (!empty($customerShipmentInfo['customer_booking_date']) ? $customerShipmentInfo['customer_booking_date'] : null);
									$rowData['dt_collection_date'] = (!empty($customerShipmentInfo['customer_collection_date']) ? $customerShipmentInfo['customer_collection_date'] : null);
									$rowData['dt_delivery_date'] = (!empty($customerShipmentInfo['customer_delivery_date']) ? $customerShipmentInfo['customer_delivery_date'] : null);
									$rowData['v_remarks'] = (!empty($customerShipmentInfo['customer_remarks']) ? $customerShipmentInfo['customer_remarks'] : null);
									$rowData['v_tracking_link'] = (!empty($customerShipmentInfo['customer_tracking_link']) ? $customerShipmentInfo['customer_tracking_link'] : null);
									$rowData['e_box_pallet_type'] = (!empty($customerShipmentInfo['customer_box_pallet_type_']) ? ($customerShipmentInfo['customer_box_pallet_type_']) : null);
									$rowData['i_total_no_of_pallets'] = (!empty($customerShipmentInfo['customer_pallets_boxes_']) ? $customerShipmentInfo['customer_pallets_boxes_'] : null);
										
									if(!empty($rowData['v_shipment_invoice_no'])){
										$insertShipmentCustomerDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $rowData);
										$fbaSheetData = [];
										$fbaSheetData['v_shipment_no'] = $rowData['v_shipment_invoice_no'];
										$fbaSheetData['i_ref_table_id'] = $insertShipmentCustomerDetail;
										$fbaSheetData['v_ref_record_type'] = config('constants.US_WAREHOUSE_TO_CUSTOMER');
										$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
									}
								}
							}
						}
					break;
					case config('constants.UK_WAREHOUSE_FBA_SHEET'):
						if(!empty($warehouseShipmentDetails)){
							foreach ($warehouseShipmentDetails as $warehouseKey => $warehouseShipmentDetail){
								$usWarehouseGenerateNo = $this->generateUsWarehouseNo(config('constants.US_WAREHOUSE_TO_UK_WAREHOUSE'));
								$recordData['v_us_warehouse_to_amazon_record_no'] = $usWarehouseGenerateNo;
								$recordData['v_tracking_no'] = (!empty($warehouseKey) ? $warehouseKey : null);
								if($recordId > 0 ){
										
								} else {
									if(!empty($recordData['v_tracking_no'])){
										$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
									}
								} 
								foreach ( $warehouseShipmentDetail as $warehouseShipmentInfo){
									/* if($recordId > 0 ){
									
									} else {
										if((!empty($recordData['v_tracking_no']))){
											$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
										}
									} */
									$rowData = [];
									$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
									$rowData['v_invoice_no_ref_no'] = (!empty($warehouseShipmentInfo['invoice_no_ref_no']) ? $warehouseShipmentInfo['invoice_no_ref_no'] : null);
									$rowData['v_uk_account_ids'] = (!empty($warehouseShipmentInfo['uk_account_ids']) ? $warehouseShipmentInfo['uk_account_ids'] : null);
									$rowData['i_uk_from_warehouse_id'] = (!empty($warehouseShipmentInfo['uk_from_warehouse_id']) ? $warehouseShipmentInfo['uk_from_warehouse_id'] : null);
									$rowData['i_uk_to_warehouse_id'] = (!empty($warehouseShipmentInfo['uk_to_warehouse_id']) ? $warehouseShipmentInfo['uk_to_warehouse_id'] : null);
									$rowData['v_uk_unit'] = (!empty($warehouseShipmentInfo['uk_unit']) ? $warehouseShipmentInfo['uk_unit'] : null);
									//$rowData['v_uk_box_pallet'] = (!empty($warehouseShipmentInfo['uk_box_pallet']) ? $warehouseShipmentInfo['uk_box_pallet'] : null);
									$rowData['dt_booking_date'] = (!empty($warehouseShipmentInfo['warehouse_booking_date']) ? $warehouseShipmentInfo['warehouse_booking_date'] : null);
									$rowData['dt_collection_date'] = (!empty($warehouseShipmentInfo['warehouse_collection_date']) ? $warehouseShipmentInfo['warehouse_collection_date'] : null);
									$rowData['dt_delivery_date'] = (!empty($warehouseShipmentInfo['warehouse_delivery_date']) ? $warehouseShipmentInfo['warehouse_delivery_date'] : null);
									$rowData['v_remarks'] = (!empty($warehouseShipmentInfo['warehouse_remarks']) ? $warehouseShipmentInfo['warehouse_remarks'] : null);
									$rowData['v_tracking_link'] = (!empty($warehouseShipmentInfo['warehouse_tracking_link']) ? $warehouseShipmentInfo['warehouse_tracking_link'] : null);
									$rowData['e_box_pallet_type'] = (!empty($warehouseShipmentInfo['warehouse_box_pallet_type_']) ? ($warehouseShipmentInfo['warehouse_box_pallet_type_']) : null);
									$rowData['i_total_no_of_pallets'] = (!empty($warehouseShipmentInfo['warehouse_pallets_boxes_']) ? $warehouseShipmentInfo['warehouse_pallets_boxes_'] : null);
									
									if(!empty($rowData['v_invoice_no_ref_no'])){
										$insertShipmentUkWarehouseDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $rowData);
										$fbaSheetData = [];
										$fbaSheetData['v_shipment_no'] = $rowData['v_invoice_no_ref_no'];
										$fbaSheetData['i_ref_table_id'] = $insertShipmentUkWarehouseDetail;
										$fbaSheetData['v_ref_record_type'] = config('constants.US_WAREHOUSE_TO_UK_WAREHOUSE');
										$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
									}
								}
							}
						}
					break;
				}
			}
			
			/* for($i = 1; $i<= $usWarehouseShipmentAmazonCount; $i++){
				$rowData = [];
				$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
				$rowData['v_shipment_id'] = (!empty($request->input('shipment_id_'.$i)) ? $request->input('shipment_id_'.$i) : null);
				$rowData['v_ref_id'] = (!empty($request->input('ref_id_'.$i)) ? $request->input('ref_id_'.$i) : null);
				$rowData['i_account_company_id'] = (!empty($request->input('account_'.$i)) ? (int)Wild_tiger::decode($request->input('account_'.$i)) : 0);
				$rowData['i_amazon_from_warehouse_id'] = (!empty($request->input('amazon_from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('amazon_from_warehouse_'.$i)) : 0);
				$rowData['i_to_amazon_location_id'] = (!empty($request->input('to_amazon_location_'.$i)) ? (int)Wild_tiger::decode($request->input('to_amazon_location_'.$i)) : 0);
				$rowData['v_sku'] = (!empty($request->input('sku_'.$i)) ? $request->input('sku_'.$i) : null);
				$rowData['v_units'] = (!empty($request->input('unit_'.$i)) ? $request->input('unit_'.$i) : null);
				$rowData['d_price'] = (!empty($request->input('price_'.$i)) ? $request->input('price_'.$i) : null);
				if(!empty($rowData['v_shipment_id'])){
					$insertShipmentAmazonDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $rowData);
					$fbaSheetData = [];
					$fbaSheetData['v_shipment_no'] = $rowData['v_shipment_id'];
					$fbaSheetData['i_ref_table_id'] = $insertShipmentAmazonDetail;
					$fbaSheetData['v_ref_record_type'] = config('constants.US_WAREHOUSE_TO_AMAZON');
					$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
				}
			} */
			/* for($i = 1; $i<= $usWarehouseShipmentCustomerCount; $i++){
				$rowData = [];
				$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
				$rowData['v_shipment_invoice_no'] = (!empty($request->input('invoice_no_'.$i)) ? $request->input('invoice_no_'.$i) : null);
				$rowData['i_customer_id'] = (!empty($request->input('customer_name_'.$i)) ? (int)Wild_tiger::decode($request->input('customer_name_'.$i)) : 0);
				$rowData['i_customer_from_warehouse_id'] = (!empty($request->input('customer_from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('customer_from_warehouse_'.$i)) : 0);
				$rowData['i_to_customer_id'] = (!empty($request->input('to_customer_'.$i)) ? (int)Wild_tiger::decode($request->input('to_customer_'.$i)) : 0);
				$rowData['v_customer_unit'] = (!empty($request->input('customer_unit_'.$i)) ? $request->input('customer_unit_'.$i) : null);
				//$rowData['v_box_pallet'] = (!empty($request->input('box_pallet_'.$i)) ? $request->input('box_pallet_'.$i) : null);
				if(!empty($rowData['v_shipment_invoice_no'])){
					$insertShipmentCustomerDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $rowData);
					$fbaSheetData = [];
					$fbaSheetData['v_shipment_no'] = $rowData['v_shipment_invoice_no'];
					$fbaSheetData['i_ref_table_id'] = $insertShipmentCustomerDetail;
					$fbaSheetData['v_ref_record_type'] = config('constants.US_WAREHOUSE_TO_CUSTOMER');
					$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
				}
				
			}
			for($i = 1; $i<= $usWarehouseShipmentUkWarehouseCount; $i++){
				$rowData = $decodedId = [];
				$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
				$rowData['v_invoice_no_ref_no'] = (!empty($request->input('invoice_no_ref_no_'.$i)) ? $request->input('invoice_no_ref_no_'.$i) : null);
				$ukAccountCompanyIds = (!empty($request->input('uk_account_'.$i)) ? $request->input('uk_account_'.$i) : []);
				if(!empty($ukAccountCompanyIds)){
					foreach ($ukAccountCompanyIds as $ukAccountCompanyId){
						$decodedId[] = (int)Wild_tiger::decode($ukAccountCompanyId);
					}
				}
				$rowData['v_uk_account_ids'] = (!empty($decodedId) ? implode(',', $decodedId) : null); 
				$rowData['i_uk_from_warehouse_id'] = (!empty($request->input('uk_from_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('uk_from_warehouse_'.$i)) : 0);
				$rowData['i_uk_to_warehouse_id'] = (!empty($request->input('uk_to_warehouse_'.$i)) ? (int)Wild_tiger::decode($request->input('uk_to_warehouse_'.$i)) : 0);
				$rowData['v_uk_unit'] = (!empty($request->input('uk_unit_'.$i)) ? $request->input('uk_unit_'.$i) : null);
				//$rowData['v_uk_box_pallet'] = (!empty($request->input('uk_box_pallet_'.$i)) ? $request->input('uk_box_pallet_'.$i) : null);
				
				if(!empty($rowData['v_invoice_no_ref_no'])){
					$insertShipmentUkWarehouseDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $rowData);
					$fbaSheetData = [];
					$fbaSheetData['v_shipment_no'] = $rowData['v_invoice_no_ref_no'];
					$fbaSheetData['i_ref_table_id'] = $insertShipmentUkWarehouseDetail;
					$fbaSheetData['v_ref_record_type'] = config('constants.US_WAREHOUSE_TO_UK_WAREHOUSE');
					$fbaSheetRecordInsert = $this->crudModel->insertTableData( config('constants.SHIPMENT_NO_INFO_TABLE') , $fbaSheetData);
				}
			} */
			
			for ($i = 0; $i <= $usWarehouseDocumentTypeCount; $i++){
				$rowData = [];
				$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
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
					$insertDocumentDetail = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DOCUMENT_MASTER_TABLE') , $rowData);
				}
					
			}
			for ($i = 0; $i <= $usWarehouseTransporterCount;$i++){
				$rowData = [];
				$rowData['i_us_warehouse_to_amazon_master_id'] = $insertRecord;
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
					$insertTransporterInvoice = $this->crudModel->insertTableData( config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE') , $rowData);
				}
			}
			$result = true;
		}catch(\Exception $e){
			var_dump($e->getMessage());die;
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
		if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		if($recordId > 0){
			$whereData = $where = [];
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$data['pageTitle'] = trans('messages.update-us-warehouse-to-amazon');
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			
			if(!empty($recordInfo)){
				$errorFound = false;
				$data['wayOfTransportDetails'] = wayOfTransportDetails( [ config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
				$data['wayToWarehouseInfo'] = wayToWarehouseInfo();
				$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
				$data['companyMasterRecordDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
				$data['amazonFromWarehouseDetails'] = WarehouseMasterModel::where('i_country_id',config ( 'constants.USA'))->where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['locationMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
				$data['customerRecordDetails'] = CustomerMasterModel::orderBy('v_customer_name', 'ASC')->get();
				
				$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($where){
					$query;
				})->get();
				$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
				$data['ukWarehouseDetails'] = WarehouseMasterModel::whereNotIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				$data['customerLocationRecordDetails'] = CustomerDetailModel::orderBy('v_customer_code', 'ASC')->get();
				$data['boxPalletTypeInfo'] = typeInfo();
				$data['recordInfo'] = $recordInfo;
				
				$disableForm = '';
				$documentForm = '';
				$statusDisableForm = '';
				if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
					$data ['pageTitle'] = trans('messages.view-us-warehouse-to-amazon');
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
				$data['viewRequest'] = ( $this->secondUriSegment == 'view' ?  true : false );
				return view($this->folderName . 'add-us-warehouse-to-amazon')->with($data);
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
		if(!empty($request->post('search_way_of_transport') )){
			$whereData['way_of_transport'] = ( $request->post('search_way_of_transport') );
		}
		if(!empty($request->post('search_from') )){
			$whereData['warehouse_from'] = (int)Wild_tiger::decode($request->post('search_from'));
		}
		if(!empty($request->post('search_to') )){
			$whereData['amazon_customer_to'] = ($request->post('search_to'));
		}
		if( !empty($request->post('search_book_by') ) ){
			$whereData['book_by'] = (int)Wild_tiger::decode($request->post('search_book_by'));
		}
		
		if(!empty($request->post('search_logistic_partner') )){
			$whereData['logistic_partner'] = (int)Wild_tiger::decode($request->post('search_logistic_partner'));
		}
		if(!empty($request->post('search_booking_from_date') )){
			$whereData['booking_form_date'] = ($request->post('search_booking_from_date'));
		}
		if(!empty($request->post('search_booking_to_date') )){
			$whereData['booking_to_date'] = ($request->post('search_booking_to_date'));
		}
		if(!empty($request->post('search_collection_from_date') )){
			$whereData['collection_form_date'] = ($request->post('search_collection_from_date'));
		}
		if(!empty($request->post('search_collection_to_date') )){
			$whereData['collection_to_date'] = ($request->post('search_collection_to_date'));
		}
		if(!empty($request->post('search_amazon_appointment_from_date') )){
			$whereData['appointment_from_date'] = ($request->post('search_amazon_appointment_from_date'));
		}
		if(!empty($request->post('search_amazon_appointment_to_date') )){
			$whereData['appointment_to_date'] = ($request->post('search_amazon_appointment_to_date'));
		}
		/* if(!empty($request->post('search_status') )){
			$whereData['status'] = (int)Wild_tiger::decode($request->post('search_status'));
		} */
		if(!empty($request->post('search_delivery_from_date') )){
			$whereData['delivery_from_date'] = ($request->post('search_delivery_from_date'));
		}
		if(!empty($request->post('search_delivery_to_date') )){
			$whereData['delivery_to_date'] = ($request->post('search_delivery_to_date'));
		}
		if(!empty($request->post('search_box_pallet_type') )){
			$whereData['box_pallet_type'] = trim($request->post('search_box_pallet_type'));
		}
		if( ( !empty($request->post('search_status') ) ) && ( $request->post('search_status') ) ){
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
		if (!empty($request->post('search_total_no_of_pallet'))) {
			$searchByTotalNoOfPallets = trim($request->post('search_total_no_of_pallet'));
			$likeData ['search_total_no_of_pallet'] = $searchByTotalNoOfPallets;
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
					$rowExcelData['entry_no'] = (isset($getExportRecordDetail->v_us_warehouse_to_amazon_record_no) ? ($getExportRecordDetail->v_us_warehouse_to_amazon_record_no) : '' );
					$rowExcelData['way_of_transport'] = (isset($getExportRecordDetail->e_transport_way) ? ($getExportRecordDetail->e_transport_way) : '' );
					$rowExcelData['from'] = (isset($getExportRecordDetail->fromUsWarehouseInfo->v_warehouse_name) ? $getExportRecordDetail->fromUsWarehouseInfo->v_warehouse_name :'');
					$rowExcelData['to'] = (isset($getExportRecordDetail->e_to_location) ? $getExportRecordDetail->e_to_location :'');
					$rowExcelData['book_by'] = (isset($getExportRecordDetail->bookByEmployee->v_name) ? $getExportRecordDetail->bookByEmployee->v_name . ( isset($getExportRecordDetail->bookByEmployee->v_department) ? ' ('.$getExportRecordDetail->bookByEmployee->v_department.')'  : '' ) :'');
					$rowExcelData['logistic_partner'] = (isset($getExportRecordDetail->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name . ( isset($getExportRecordDetail->logisticPartnerMasterInfo->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->logisticPartnerMasterInfo->v_logistic_partner_code.')'  : '' ) :'');
					//$rowExcelData['booking_date'] = (isset($getExportRecordDetail->dt_booking_date) ?  clientDate($getExportRecordDetail->dt_booking_date) : '' );
					//$rowExcelData['collection_date'] = (isset($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) : '' );
					//$rowExcelData['delivery_date'] = (isset($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '' );
					//$rowExcelData['type'] = (isset($getExportRecordDetail->e_box_pallet_type) ?  ( $getExportRecordDetail->e_box_pallet_type )  : '' );
					//$rowExcelData['total_no_of_pallets'] = (isset($getExportRecordDetail->i_total_no_of_pallets) ?  ( $getExportRecordDetail->i_total_no_of_pallets )  : '' );
					$rowExcelData['tracking_no.'] = (isset($getExportRecordDetail->v_tracking_no) ?  ( $getExportRecordDetail->v_tracking_no )  : '' );
					//$rowExcelData['tracking_link'] = (isset($getExportRecordDetail->v_tracking_link) ?  ( $getExportRecordDetail->v_tracking_link )  : '' );
					//$rowExcelData['amazon_appointment_date'] = (isset($getExportRecordDetail->dt_amazon_appointment_date) ? clientDate($getExportRecordDetail->dt_amazon_appointment_date) : '' );
					$rowExcelData['status'] = (isset($getExportRecordDetail->statusInfo->v_status) ? ($getExportRecordDetail->statusInfo->v_status) : '');
					$finalExportData[] = $rowExcelData;
				}
			}
		
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.us-warehouse-to-amazon-customer-uk-warehouse')]);
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
		
		$totalRecords = count($data['recordDetails']);
	
		if(isset($totalRecords)){
			$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
		}
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'us-warehouse-to-amazon/us-warehouse-to-amazon-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$usToAmazonDetailData['t_is_active'] = 0;
			$usToAmazonDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-delete',['module'=> $this->moduleName]);
			
			DB::beginTransaction();
	
			$result = false;
			//$usWarehouseDetails = new UsWarehouseToAmazonDetailsModel();
			//$getShipmentAmazonInfo = $usWarehouseDetails->getRecordDetails(['master_id' => $recordId ,'ref_record_type' => config('constants.US_WAREHOUSE_TO_AMAZON'),'singleRecord' => true ]);
			//$getShipmentCustomerInfo = $usWarehouseDetails->getRecordDetails(['master_id' => $recordId ,'ref_record_type' => config('constants.US_WAREHOUSE_TO_CUSTOMER'),'singleRecord' => true ]);
			//$getShipmentUkWarehouseInfo = $usWarehouseDetails->getRecordDetails(['master_id' => $recordId ,'ref_record_type' => config('constants.US_WAREHOUSE_TO_UK_WAREHOUSE'),'singleRecord' => true ]);
			
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$existingRecordDetails = $this->crudModel->getRecordDetails($whereData);
			
			$allChildRecordIds = ( isset($existingRecordDetails->usWarehouseToAmazonDetails) ?  array_column(objectToArray($existingRecordDetails->usWarehouseToAmazonDetails) , 'i_id') : []  );
			
			
			try{
				$this->crudModel->deleteTableData(  config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') ,  $usToAmazonDetailData , [ 'i_us_warehouse_to_amazon_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.US_WAREHOUSE_TO_AMAZON_DOCUMENT_MASTER_TABLE') ,  $usToAmazonDetailData , [ 'i_us_warehouse_to_amazon_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.US_WAREHOUSE_TO_AMAZON_INVOICE_MASTER_TABLE') ,  $usToAmazonDetailData , [ 'i_us_warehouse_to_amazon_master_id' => $recordId ] );
	
				$this->crudModel->deleteTableData($this->tableName,  $usToAmazonDetailData , [ 'i_id' => $recordId ] );
				/* if(!empty($getShipmentAmazonInfo)){
					$amazonRefTableId = (!empty($getShipmentAmazonInfo->shipmentRecordIfo->i_ref_table_id) ? $getShipmentAmazonInfo->shipmentRecordIfo->i_ref_table_id : "");
					$this->crudModel->deleteTableData(  config('constants.SHIPMENT_NO_INFO_TABLE') ,  $usToAmazonDetailData , [ 'i_ref_table_id' => $amazonRefTableId ] );
					
				}
				if(!empty($getShipmentCustomerInfo)){
					$customerRefTableId = (!empty($getShipmentCustomerInfo->shipmentRecordIfo->i_ref_table_id) ? $getShipmentCustomerInfo->shipmentRecordIfo->i_ref_table_id : "");
					$this->crudModel->deleteTableData(  config('constants.SHIPMENT_NO_INFO_TABLE') ,  $usToAmazonDetailData , [ 'i_ref_table_id' => $customerRefTableId ] );
						
				}
				if(!empty($getShipmentUkWarehouseInfo)){
					$ukWarehouseRefTableId = (!empty($getShipmentUkWarehouseInfo->shipmentRecordIfo->i_ref_table_id) ? $getShipmentUkWarehouseInfo->shipmentRecordIfo->i_ref_table_id : "");
					$this->crudModel->deleteTableData(  config('constants.SHIPMENT_NO_INFO_TABLE') ,  $usToAmazonDetailData , [ 'i_ref_table_id' => $ukWarehouseRefTableId ] );
				} */
				
				if(!empty($existingRecordDetails)){
					$recordType = "";
					switch($existingRecordDetails->e_to_location){
						case config('constants.AMAZON_FBA_SHEET'):
							$recordType = config('constants.US_WAREHOUSE_TO_AMAZON'); 
							break;
						case config('constants.CUSTOMER_FBA_SHEET'):
							$recordType = config('constants.US_WAREHOUSE_TO_CUSTOMER');
							break;
						case config('constants.UK_WAREHOUSE_FBA_SHEET'):
							$recordType = config('constants.US_WAREHOUSE_TO_UK_WAREHOUSE');
							break;
					}
					if(!empty($recordType)){
						$usToAmazonDetailData ['i_deleted_id'] = session()->get('user_id');
						$usToAmazonDetailData ['dt_deleted_at'] = date('Y-m-d H:i:s');
						DB::table(config('constants.SHIPMENT_NO_INFO_TABLE'))->whereIn('i_ref_table_id', $allChildRecordIds )->where('v_ref_record_type' ,$recordType )->update($usToAmazonDetailData);
					}
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
	public function uploadCSVFile(Request $request){
		//echo '<pre>';print_r($request->all());die;
		/* $formValidation = [];
		$formValidation['upload_csv_file'] = 'required|mimes:csv,txt';
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'upload_csv_file.mimes' => __ ( 'messages.only-csv-file-allowed' ),
		] );
		if ($validator->fails ()) {
			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.csv-file') ] ) ) );
		} */
		$shipmentType = (!empty($request->post('shipment_type')) ? $request->post('shipment_type') : '');
		$shipmentRequestType = (!empty($request->post('shipment_request')) ? $request->post('shipment_request') : '');
		$html = '';
		$importFile = "";
		$fileName = "";
		if( !empty( $_FILES['upload_csv_file']['name'] ) ){
			$fileName = $_FILES['upload_csv_file']['name'];
			$importFile = $this->uploadFile($request , 'upload_csv_file' , 'csv');
		}
		
		if(!empty($importFile)){
			if( isset($importFile['status']) && ( $importFile['status'] != false ) ){
				$inputFileName = config('constants.FILE_STORAGE_FILE_PATH') . config('constants.UPLOAD_FOLDER')  . ($importFile['filePath']);
					
				$row = 1;
				$excelKeys = [];
				$data = [];
				$rowDetails = [];
				if (($handle = fopen($inputFileName, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						if( $row == 1 ){
							$excelKeys = array_values($data);
						} else {
							$rowDetail = [];
							$rowDetail = array_combine($excelKeys, $data);
							if(!empty($rowDetail)){
								$rowDetails[] = $rowDetail;
							}
						}
						$row++;
					}
					fclose($handle);
					
					if(!empty($rowDetails)){
						$data['shipmentDetails'] = $rowDetails;
						$data['disableForm'] = '';
						$data['companyMasterRecordDetails'] = CompanyMasterModel::where('t_is_active',1)->orderBy('v_company_name', 'ASC')->get();
						$data['amazonFromWarehouseDetails'] = WarehouseMasterModel::where('i_country_id',config ( 'constants.USA'))->where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
						$data['locationMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
						$data['countryMasterDetails'] = CountryMasterModel::where('t_is_active',1)->orderBy('v_country_name', 'ASC')->get();
						$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
						$data['customerRecordDetails'] = CustomerMasterModel::where('t_is_active',1)->orderBy('v_customer_name', 'ASC')->get();
						$data['customerLocationRecordDetails'] = CustomerDetailModel::where('t_is_active',1)->orderBy('v_customer_code', 'ASC')->get();
						$data['ukWarehouseDetails'] = WarehouseMasterModel::whereNotIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
						$data['boxPalletTypeInfo'] = typeInfo();
						$data['shipmentRequestType'] = $shipmentRequestType;
						$data['warehouseMasterDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
						$data['importCsvAddRowCount'] = (!empty($request->post('import_csv_add_row_count')) ? (int)$request->post('import_csv_add_row_count') : 2);
						
						$fileNameTypeWise = '';
						if(!empty($shipmentType)){
							switch ($shipmentType){
								case config('constants.AMAZON_FBA_SHEET'):
									$fileNameTypeWise = 'us-warehouse-to-amazon/us-warehouse-to-amazon-info';
									break;
								case config('constants.CUSTOMER_FBA_SHEET'):
									$fileNameTypeWise = 'us-warehouse-to-amazon/us-warehouse-to-customer-info';
									break;
								case config('constants.UK_WAREHOUSE_FBA_SHEET'):
									$fileNameTypeWise = 'us-warehouse-to-amazon/us-warehouse-to-uk-warehouse-info';
									break;
							}
						}
						$html = view (config('constants.AJAX_VIEW_FOLDER') .  $fileNameTypeWise)->with ( $data )->render();
						$this->ajaxResponse(1, trans('messages.success') , [ 'html' =>  $html, 'import_sheet_count' => count($rowDetails) ] );
					}
					$this->ajaxResponse(101, trans('messages.no-record-found') );
				}
			} else {
				$this->ajaxResponse(101, ( isset($importFile['message']) ? $importFile['message'] : trans('messages.system-error') ) );
			}
		}
		$this->ajaxResponse(101, trans('messages.error-file-upload') );
		
	}
	public function generateUsWarehouseNo( $usWarehouseToAmazonGenerateNo = ''){
		$usWarehouseToAmazonMasterRecordDetails = $this->crudModel->selectData($this->tableName ,['i_id']);
		$usWarehouseToAmazonMasterRecordCount = count($usWarehouseToAmazonMasterRecordDetails);
		$count = ( ( (!empty($usWarehouseToAmazonMasterRecordCount)) && ( $usWarehouseToAmazonMasterRecordCount > 0 ) ) ? ( $usWarehouseToAmazonMasterRecordCount + 1  ) : 1 );
		$generateNumber = threeNumberSeries($count);
		$generateUsWarehouseEntryNo = $generateNumber.'-'.$this->todayDate;
		$usWarehouseGenerateNo = $usWarehouseToAmazonGenerateNo.'-'. $generateUsWarehouseEntryNo;
		return $usWarehouseGenerateNo;
		
	}
}
