<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use Illuminate\Database\Eloquent\Model;
use App\LoginHistory;
use App\ReportModel;
use App\CompanyMasterModel;
use App\Login;
use App\SupplierMasterModel;
use App\SupplierDetailModel;
use App\LogisticPartnerMasterModel;
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
class ReportGoodOutController extends MasterController
{
    //
	public function __construct(){
		//parent::__construct();
		$this->middleware('checklogin');
		$this->crudModel = new ReportModel();
		$this->folderName = config('constants.ADMIN_FOLDER'). 'tracking-goods-out/';
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->goodInBuyerDetailModel = new GoodInBuyerMasterModel();
		$this->goodInLogisticModel = new GoodInLogisticMasterModel();
	}
	
	public function trackingGoodsOut(){
	
		if(checkPermission(config('permission_constants.VIEW_TRACKING_GOODS_OUT_REPORT')) != true){
			return redirect('access-denied');
		}
	
		$data = [];
		$data ['pageTitle'] = trans('messages.tracking-goods-out'). ' ' . trans('messages.report');
		
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		//$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->get();
		$data['companyDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['locationDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.LOCATION'))->orderBy('v_warehouse_name', 'ASC')->get();
		
		$page = $this->defaultPage;
	
		return view($this->folderName . 'tracking-goods-out')->with($data);
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
			
			if (!empty($request->post('search_by_good_out_report'))) {
				$searchByName = trim($request->post('search_by_good_out_report'));
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
			// $exportTypeAction = (!empty($request->input('custom_export_type_action')) ? trim($request->input('custom_export_type_action')) : '');
				
			if ($exportAction == 'export') {
				$finalExportData = [];
				
				$getExportRecordDetails = $this->crudModel->getTrackingGoodOutDetail($whereData, $likeData , $additionalData );
	
				if (!empty($getExportRecordDetails)) {
					$excelIndex = 0;
						
					foreach ($getExportRecordDetails as $getExportRecordDetail) {
							
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
						if(isset($transportInvoiceDetails) && !empty($transportInvoiceDetails) && count($transportInvoiceDetails) > 0){
							foreach ($transportInvoiceDetails as $transportInvoiceDetail){
								$totalTransportPrice = $totalTransportPrice + (isset($transportInvoiceDetail->d_total_charge) && !empty($transportInvoiceDetail->d_total_charge) ? $transportInvoiceDetail->d_total_charge : 0);
							}
						}
						
						$rowExcelData['sr_no'] = ++$excelIndex;
						$rowExcelData['entry_number'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_country_to_port_europe_record_no) && !empty($masterInfo->v_country_to_port_europe_record_no) ?  ($masterInfo->v_country_to_port_europe_record_no)  :'' );
						$rowExcelData['workflow_id'] = ( isset($getExportRecordDetail->v_workflow_id) && !empty($getExportRecordDetail->v_workflow_id) ?  ($getExportRecordDetail->v_workflow_id)  :'' );
						$rowExcelData['fba_id'] = ( isset($getExportRecordDetail->v_shipment_id) && !empty($getExportRecordDetail->v_shipment_id) ?  ($getExportRecordDetail->v_shipment_id)  :'' );
						$rowExcelData['account_name'] = ( isset($accountCompanyinfo) && !empty($accountCompanyinfo) && isset($accountCompanyinfo->v_company_name) && !empty($accountCompanyinfo->v_company_name) ?  ($accountCompanyinfo->v_company_name)  :'' );
						$rowExcelData['sku'] = ( isset($getExportRecordDetail->v_sku) && !empty($getExportRecordDetail->v_sku) ?  ($getExportRecordDetail->v_sku)  :'' );
						$rowExcelData['unit'] = ( isset($getExportRecordDetail->v_units) && !empty($getExportRecordDetail->v_units) ?  ($getExportRecordDetail->v_units)  :'' );
						$rowExcelData['shipment_value'] = ( isset($getExportRecordDetail->v_price) && !empty($getExportRecordDetail->v_price) ?  decimalAmount($getExportRecordDetail->v_price)  :'' );
						$rowExcelData['box_/_pallet'] = ( isset($getExportRecordDetail->e_dimension) && !empty($getExportRecordDetail->e_dimension) ?  ($getExportRecordDetail->e_dimension)  :'' );
						$rowExcelData['no._of_box_/_pallet'] = ( isset($getExportRecordDetail->i_no_of_pallet_box) && !empty($getExportRecordDetail->i_no_of_pallet_box) ?  decimalAmount($getExportRecordDetail->i_no_of_pallet_box)  :'' );
						$rowExcelData['from_warehouse'] = ( isset($fromWhereHouseinfo) && !empty($fromWhereHouseinfo) && isset($fromWhereHouseinfo->v_warehouse_name) && !empty($fromWhereHouseinfo->v_warehouse_name) ?  ($fromWhereHouseinfo->v_warehouse_name)  :'' );
						$rowExcelData['to_warehouse'] = ( isset($toWhereHouseInfo) && !empty($toWhereHouseInfo) && isset($toWhereHouseInfo->v_warehouse_name) && !empty($toWhereHouseInfo->v_warehouse_name) ?  ($toWhereHouseInfo->v_warehouse_name)  :'' );
						$rowExcelData['to_country'] = ( isset($toCountryInfo) && !empty($toCountryInfo) && isset($toCountryInfo->v_country_name) && !empty($toCountryInfo->v_country_name) ?  ($toCountryInfo->v_country_name)  :'' );
						$rowExcelData['way_of_transport'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->e_transport_way) && !empty($masterInfo->e_transport_way) ?  ($masterInfo->e_transport_way)  :'' );
						$rowExcelData['book_by'] = ( isset($bookByInfo) && !empty($bookByInfo) && isset($bookByInfo->v_name) && !empty($bookByInfo->v_name) ?  ($bookByInfo->v_name)  :'' );
						$rowExcelData['logistic_partner'] = ( isset($logisticPartnerMasterInfo) && !empty($logisticPartnerMasterInfo) && isset($logisticPartnerMasterInfo->v_logistic_partner_name) && !empty($logisticPartnerMasterInfo->v_logistic_partner_name) ?  ($logisticPartnerMasterInfo->v_logistic_partner_name)  :'' );
						$rowExcelData['booking_date'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->dt_booking_date) && !empty($masterInfo->dt_booking_date) ?  clientDate($masterInfo->dt_booking_date)  :'' );
						$rowExcelData['collection_date'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->dt_collection_date) && !empty($masterInfo->dt_collection_date) ?  clientDate($masterInfo->dt_collection_date)  :'' );
						$rowExcelData['delivery_date'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->dt_delivery_date) && !empty($masterInfo->dt_delivery_date) ?  clientDate($masterInfo->dt_delivery_date)  :'' );
						$rowExcelData['tracking_number'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_tracking_no) && !empty($masterInfo->v_tracking_no) ?  ($masterInfo->v_tracking_no)  :'' );
						$rowExcelData['transporter_invoice_cost_(gbp)'] = (isset($totalTransportPrice) && !empty($totalTransportPrice) ? decimalAmount($totalTransportPrice) : '');
						
						$finalExportData[] = $rowExcelData;
					}
				}
					
				if (!empty($finalExportData)) {
					$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.tracking-goods-out')]);
					$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.tracking-goods-out')]);
						
					$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
				} else {
						
					$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
				}
				return Response::json($response);
				die;
			}
			if(!empty($columnName)) {
				switch($columnName){
					case 'entry_number':
						$columnName = 'i_id';
						break;
					case 'workflow_id':
						$columnName = 'v_workflow_id';
						break;
					case 'fba_id':
						$columnName = 'v_shipment_id';
						break;
					case 'account_name':
						$columnName = 'i_id';
						break;						
					case 'sku':
						$columnName = 'v_sku';
						break;
					case 'unit':
						$columnName = 'v_units';
						break;
					case 'shipment_value':
						$columnName = 'v_price';
						break;
					case 'box_pallet':
						$columnName = 'e_dimension';
						break;
					case 'no_of_box_pallet':
						$columnName = 'i_no_of_pallet_box';
						break;
					case 'from_warehouse':
						$columnName = 'i_id';
						break;
					case 'to_warehouse':
						$columnName = 'i_id';
						break;
					case 'to_country':
						$columnName = 'i_id';
						break;
					case 'way_of_transport':
						$columnName = 'i_id';
						break;
					case 'book_by':
						$columnName = 'i_id';
						break;
					case 'logistic_partner':
						$columnName = 'i_id';
						break;
					case 'booking_date':
						$columnName = 'i_id';
						break;
					case 'collection_date':
						$columnName = 'i_id';
						break;
					case 'delivery_date':
						$columnName = 'i_id';
						break;
					case 'tracking_number':
						$columnName = 'i_id';
						break;
					case 'transporter_invoice_cost_gbp':
						$columnName = 'i_id';
						break;
				}
				$whereData['order_by'] = [ $columnName =>  ( (!empty($columnSortOrder)) ? $columnSortOrder : 'DESC' ) ];
			} else {
				$whereData['order_by'] =  [ 'i_id' =>  'desc'] ;
			}
			
			$totalRecords = count($this->crudModel->getTrackingGoodOutDetail ( $whereData  , $likeData  , $additionalData ));
			$whereData['offset'] = $offset ;
				
			$whereData['limit'] = $limit;
	
			$recordDetails = $this->crudModel->getTrackingGoodOutDetail ( $whereData , $likeData , $additionalData  );
			
			$finalData = [];
			if(!empty($recordDetails)){
				$index = $offset;
				$allSalesRole = [];
				foreach($recordDetails as $key => $recordDetail){
	
					$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
					$masterInfo = (isset($recordDetail->countryToPortEurope) && !empty($recordDetail->countryToPortEurope) ? $recordDetail->countryToPortEurope : []);
					$accountCompanyinfo = (isset($recordDetail->accountCompany) && !empty($recordDetail->accountCompany) ? $recordDetail->accountCompany : []);
					$fromWhereHouseinfo = (isset($recordDetail->warehouse) && !empty($recordDetail->warehouse) ? $recordDetail->warehouse : []);
					$toWhereHouseInfo = (isset($recordDetail->location) && !empty($recordDetail->location) ? $recordDetail->location : []);
					$toCountryInfo = (isset($recordDetail->country) && !empty($recordDetail->country) ? $recordDetail->country : []);
					$bookByInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->bookEmployeeInfo) && !empty($masterInfo->bookEmployeeInfo) ? $masterInfo->bookEmployeeInfo : []);
					$logisticPartnerInfo = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->logisticPartnerDetail) && !empty($masterInfo->logisticPartnerDetail) ? $masterInfo->logisticPartnerDetail : []);
					$logisticPartnerMasterInfo = (isset($logisticPartnerInfo) && !empty($logisticPartnerInfo) && isset($logisticPartnerInfo->logisticPartnerMaster) && !empty($logisticPartnerInfo->logisticPartnerMaster) ? $logisticPartnerInfo->logisticPartnerMaster : []);
					$transportInvoiceDetails = (isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->invoiceInfo) && !empty($masterInfo->invoiceInfo) ? $masterInfo->invoiceInfo : []);
					
					$totalTransportPrice = 0;
					if(isset($transportInvoiceDetails) && !empty($transportInvoiceDetails) && count($transportInvoiceDetails) > 0){
						foreach ($transportInvoiceDetails as $transportInvoiceDetail){
							$totalTransportPrice = $totalTransportPrice + (isset($transportInvoiceDetail->d_total_charge) && !empty($transportInvoiceDetail->d_total_charge) ? $transportInvoiceDetail->d_total_charge : 0);
						}						
					}
							
					$rowData = [];
					$rowData['sr_no'] = '<span style="text-align:center !important;display:block">'.++$index.'</span>';
					$rowData['entry_number'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_country_to_port_europe_record_no) && !empty($masterInfo->v_country_to_port_europe_record_no) ?  ($masterInfo->v_country_to_port_europe_record_no)  :'' );
					$rowData['workflow_id'] = ( isset($recordDetail->v_workflow_id) && !empty($recordDetail->v_workflow_id) ?  ($recordDetail->v_workflow_id)  :'' );
					$rowData['fba_id'] = ( isset($recordDetail->v_shipment_id) && !empty($recordDetail->v_shipment_id) ?  ($recordDetail->v_shipment_id)  :'' );
					$rowData['account_name'] = ( isset($accountCompanyinfo) && !empty($accountCompanyinfo) && isset($accountCompanyinfo->v_company_name) && !empty($accountCompanyinfo->v_company_name) ?  ($accountCompanyinfo->v_company_name)  :'' );
					$rowData['sku'] = ( isset($recordDetail->v_sku) && !empty($recordDetail->v_sku) ?  ($recordDetail->v_sku)  :'' );
					$rowData['unit'] = ( isset($recordDetail->v_units) && !empty($recordDetail->v_units) ?  ($recordDetail->v_units)  :'' );
					$rowData['shipment_value'] = ( isset($recordDetail->v_price) && !empty($recordDetail->v_price) ?  decimalAmount($recordDetail->v_price)  :'' );
					$rowData['box_pallet'] = ( isset($recordDetail->e_dimension) && !empty($recordDetail->e_dimension) ?  ($recordDetail->e_dimension)  :'' );
					$rowData['no_of_box_pallet'] = ( isset($recordDetail->i_no_of_pallet_box) && !empty($recordDetail->i_no_of_pallet_box) ?  decimalAmount($recordDetail->i_no_of_pallet_box)  :'' );
					$rowData['from_warehouse'] = ( isset($fromWhereHouseinfo) && !empty($fromWhereHouseinfo) && isset($fromWhereHouseinfo->v_warehouse_name) && !empty($fromWhereHouseinfo->v_warehouse_name) ?  ($fromWhereHouseinfo->v_warehouse_name)  :'' );
					$rowData['to_warehouse'] = ( isset($toWhereHouseInfo) && !empty($toWhereHouseInfo) && isset($toWhereHouseInfo->v_warehouse_name) && !empty($toWhereHouseInfo->v_warehouse_name) ?  ($toWhereHouseInfo->v_warehouse_name)  :'' );
					$rowData['to_country'] = ( isset($toCountryInfo) && !empty($toCountryInfo) && isset($toCountryInfo->v_country_name) && !empty($toCountryInfo->v_country_name) ?  ($toCountryInfo->v_country_name)  :'' );
					$rowData['way_of_transport'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->e_transport_way) && !empty($masterInfo->e_transport_way) ?  ($masterInfo->e_transport_way)  :'' );
					$rowData['book_by'] = ( isset($bookByInfo) && !empty($bookByInfo) && isset($bookByInfo->v_name) && !empty($bookByInfo->v_name) ?  ($bookByInfo->v_name)  :'' );
					$rowData['logistic_partner'] = ( isset($logisticPartnerMasterInfo) && !empty($logisticPartnerMasterInfo) && isset($logisticPartnerMasterInfo->v_logistic_partner_name) && !empty($logisticPartnerMasterInfo->v_logistic_partner_name) ?  ($logisticPartnerMasterInfo->v_logistic_partner_name)  :'' );
					$rowData['booking_date'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->dt_booking_date) && !empty($masterInfo->dt_booking_date) ?  clientDate($masterInfo->dt_booking_date)  :'' );
					$rowData['collection_date'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->dt_collection_date) && !empty($masterInfo->dt_collection_date) ?  clientDate($masterInfo->dt_collection_date)  :'' );
					$rowData['delivery_date'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->dt_delivery_date) && !empty($masterInfo->dt_delivery_date) ?  clientDate($masterInfo->dt_delivery_date)  :'' );
					$rowData['tracking_number'] = ( isset($masterInfo) && !empty($masterInfo) && isset($masterInfo->v_tracking_no) && !empty($masterInfo->v_tracking_no) ?  ($masterInfo->v_tracking_no)  :'' );
					$rowData['transporter_invoice_cost_gbp'] = (isset($totalTransportPrice) && !empty($totalTransportPrice) ? decimalAmount($totalTransportPrice) : '');	
					
					$rowData['action'] = '';
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

}
