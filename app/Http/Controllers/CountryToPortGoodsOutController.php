<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CountryToPortGoodsOutModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\DB;
use App\WarehouseMasterModel;
use App\Login;
use App\LogisticPartnerMasterModel;
use App\CurrencyMasterModel;
use App\Document_Type_Master_Model;
use App\StatusMasterModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use App\FBASheeteDetailModel;
use App\CompanyMasterModel;
use App\CustomerMasterModel;
use App\AgentToWarehouseModel;
use App\Rules\UniqueFBAInvoiceNo;
use App\CustomerDetailModel;
use App\CountryMasterModel;
use App\Rules\UniquePersonalReferenceNumber;
use Illuminate\Support\Facades\Log;
use function Ramsey\Uuid\v1;

class CountryToPortGoodsOutController extends MasterController
{
	
	protected $excludeStatusIds;
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE');
		$this->shipmentTableName = config('constants.COUNTRY_TO_PORT_GOODS_OUT_SHIPMENT_VALUES_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'uk-other-country-us-port/';
		$this->moduleName = trans('messages.uk-other-country-us-port');
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL');
		$this->crudModel = new CountryToPortGoodsOutModel();
		$this->warehouseMasterModel = new  WarehouseMasterModel();
		$this->customerMasterModel = new CustomerMasterModel();
		$this->excludeStatusIds = [ config ( 'constants.DELIVERED_STATUS_ID' ) , config ( 'constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID' ) , config ( 'constants.IN_TRANSIT_STATUS_ID' )   ];
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data['pageTitle'] = trans('messages.uk-other-country-us-port');
		
		$data['fromPortInfo'] = WarehouseMasterModel::whereNotIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['toPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
		$data['insuranceStatusDetails'] = insuranceStatus();
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT') ] );
		$data['dangerousGoodsInfo'] = dangerousGoodsInfo();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['fbaSheetStatusInfo'] = fbaSheetStatusInfo();
		$data['porcessStatusInfo'] = getPorcessStatus();
		$data['countryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type', config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		
		$where = $statusIds = [];
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		$where['default_status'] = $statusIds;
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		$data['statusInfo'] = $statusIds;
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
	
		return view($this->folderName . 'uk-other-country-us-port')->with($data);
	}
	
	public function filter(Request $request){
		
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		//search record
		if (!empty($request->post('search_by_uk_other_country'))) {
			$searchByName = trim($request->post('search_by_uk_other_country'));
			$likeData['searchBy'] = $searchByName;
		}
		
		if(!empty($request->post('search_way_of_transport') )){
			$whereData['transport_way'] = ( $request->post('search_way_of_transport') );
		}
		if(!empty($request->post('search_from_port_airport') )){
			$whereData['from_port_airport'] = (int)Wild_tiger::decode($request->post('search_from_port_airport'));
		}
		if(!empty($request->post('search_to_port_airport') )){
			$whereData['to_port_airport'] = (int)Wild_tiger::decode($request->post('search_to_port_airport'));
		}
		if(!empty($request->post('search_book_by') )){
			$whereData['book_by'] = (int)Wild_tiger::decode($request->post('search_book_by'));
		}
		if(!empty($request->post('search_logistic_partner_uk') )){
			$whereData['logistic_partner_uk'] = (int)Wild_tiger::decode($request->post('search_logistic_partner_uk'));
		}
		if(!empty($request->post('search_etd_dispatch_from_date') )){
			$whereData['etd_dispatch_from_date'] = dbDate($request->post('search_etd_dispatch_from_date'));
		}
		if(!empty($request->post('search_etd_dispatch_to_date') ) ){
			$whereData['etd_dispatch_to_date'] = dbDate($request->post('search_etd_dispatch_to_date'));
		}
		if(!empty($request->post('search_eta_arrival_from_date') )){
			$whereData['eta_arrival_from_date'] = ($request->post('search_eta_arrival_from_date'));
		}
		if(!empty($request->post('search_eta_arrival_to_date') )){
			$whereData['eta_arrival_to_date'] = ($request->post('search_eta_arrival_to_date'));
		}
		if(!empty($request->post('search_dangerous_goods') )){
			$whereData['dangerous_goods'] = ($request->post('search_dangerous_goods'));
		}
		if(!empty($request->post('search_insurance_status') )){
			$whereData['insurance_status'] = ($request->post('search_insurance_status'));
		}
		/* if(!empty($request->post('search_status') )){
			$whereData['status'] = (int)Wild_tiger::decode($request->post('search_status'));
		} */
		if(!empty($request->post('search_fba_sheet_status') )){
			$whereData['fba_status'] = ($request->post('search_fba_sheet_status'));
		} 
		if(!empty($request->post('search_delivery_from_date') )){
			$whereData['delivery_from_date'] = ($request->post('search_delivery_from_date'));
		}
		if(!empty($request->post('search_delivery_to_date') )){
			$whereData['delivery_to_date'] = ($request->post('search_delivery_to_date'));
		}
		if( ( !empty($request->post('search_status') ) )){
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
		if(!empty($request->post('search_process_status') )){
			$processStatus = ($request->post('search_process_status'));
			$fbaStatus = '';
			switch ($processStatus){
				case config('constants.PORT_TO_AGENT_WAREHOUSE_NO') :
					$fbaStatus = config("constants.PENDING_STATUS");
					break;
				case config('constants.AGENT_WAREHOUSE_TO_AMAZON_NO') :
					$fbaStatus = config("constants.PARTIAL_DELIVERY_TYPE");
					break;
				case config('constants.AGENT_WAREHOUSE_TO_AMAZON_COMPLETED_NO') :
					$fbaStatus = config("constants.COMPLETED_STATUS");
					break;
			}
			if(!empty($fbaStatus)){
				$whereData['container_process_status'] = $fbaStatus;
			} else{
				if($processStatus == config('constants.UK_OTHER_COUNTRY_TO_PORT_NO')){
					$whereData['fba_status'] = config('constants.SUCCESS_STATUS');
					$whereData['process_status'] = config("constants.PENDING_STATUS");
				}
			}
		}
		
		if(!empty($request->post('search_from_warehouse_country') )){
			$whereData['from_warehouse_country'] = (int)Wild_tiger::decode($request->post('search_from_warehouse_country'));
		}
		if(!empty($request->post('search_warehouse') )){
			$whereData['warehouse'] = (int)Wild_tiger::decode($request->post('search_warehouse'));
		}
		if(!empty($request->post('search_pick_up_from_date_from_warehouse') ) ){
			$whereData['pick_up_from_date_from_warehouse'] = $request->post('search_pick_up_from_date_from_warehouse');
		}
		if(!empty($request->post('search_pick_up_to_date_from_warehouse') ) ){
			$whereData['pick_up_to_date_from_warehouse'] = $request->post('search_pick_up_to_date_from_warehouse');
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		
		if ($exportAction == 'export') {
			$finalExportData = [];
			$whereData['count_record'] = true;
			$getExportRecordDetails = $this->crudModel->getRecordDetails( $whereData, $likeData );
			
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					
					$allInvoiceDetails = ( isset($getExportRecordDetail->invoiceInfo) ? json_decode(json_encode($getExportRecordDetail->invoiceInfo),true) : [] );
					$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
					$paymentValue = $finalCharge;
					
					$containerStatus = (!empty($getExportRecordDetail->portToAgentaContainerInfo->e_container_status) ? $getExportRecordDetail->portToAgentaContainerInfo->e_container_status :'');
					
					$fbaStatus = "";
					if((isset($getExportRecordDetail->uploadFBASheetInfo->e_status)) && ($getExportRecordDetail->uploadFBASheetInfo->e_status == config("constants.SUCCESS_STATUS"))){
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
					$rowExcelData['entry_no'] = ( isset($getExportRecordDetail->v_country_to_port_record_no) ?  ($getExportRecordDetail->v_country_to_port_record_no) :'' );
					$rowExcelData['way_of_transport'] = (isset($getExportRecordDetail->e_transport_way) ? ($getExportRecordDetail->e_transport_way) : '' );
					$rowExcelData['from_-_port_/_airport'] = (isset($getExportRecordDetail->fromPortInfo->v_warehouse_name) ? ($getExportRecordDetail->fromPortInfo->v_warehouse_name) : '' );
					$rowExcelData['to_-_port_/_airport'] = (isset($getExportRecordDetail->toPortInfo->v_warehouse_name) ? ($getExportRecordDetail->toPortInfo->v_warehouse_name) : '' );
					$rowExcelData['book_by'] =  (isset($getExportRecordDetail->bookEmployeeInfo->v_name) ? $getExportRecordDetail->bookEmployeeInfo->v_name :'');
					$rowExcelData['logistic_partner_uk'] = (isset($getExportRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->logisticPartnerMaster->v_logistic_partner_name : '' );
					$rowExcelData['container_no._/_air_waybill_no.'] = (isset($getExportRecordDetail->v_container_air_waybill_no) ? $getExportRecordDetail->v_container_air_waybill_no :'');
					$rowExcelData['etd_dispatch_(port)_date'] = (isset($getExportRecordDetail->dt_est_dispatch_date) ? clientDate($getExportRecordDetail->dt_est_dispatch_date) :'');
					$rowExcelData['eta_arrival_(port)_date'] = (isset($getExportRecordDetail->dt_est_port_arrival_date) ? clientDate($getExportRecordDetail->dt_est_port_arrival_date) :'');
					$rowExcelData['delivery_date'] = (isset($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '' );
					$rowExcelData['total_logistic_cost_('.config('constants.GOODS_OUT_DEFAULT_CURRENCY').')'] = (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_DEFAULT_CURRENCY')  : '' );
					$rowExcelData['total_pallets'] = (isset($getExportRecordDetail->i_total_pallets) ? decimalAmount($getExportRecordDetail->i_total_pallets) : '');
					$rowExcelData['dangerous_goods'] = (isset($getExportRecordDetail->e_dangerous_goods) ?  strtoupper( $getExportRecordDetail->e_dangerous_goods ) : '' );
					$rowExcelData['tracking_no.'] = (isset($getExportRecordDetail->v_tracking_no) ?  ( $getExportRecordDetail->v_tracking_no )  : '' );
					$rowExcelData['tracking_link'] = (isset($getExportRecordDetail->v_tracking_link) ?  ( $getExportRecordDetail->v_tracking_link )  : '' );
					$rowExcelData['status'] = (isset($getExportRecordDetail->statusInfo->v_status) ? ($getExportRecordDetail->statusInfo->v_status) : '');
					$rowExcelData['fba_sheet_status'] = (isset($getExportRecordDetail->uploadFBASheetInfo->e_status) ? ($getExportRecordDetail->uploadFBASheetInfo->e_status) : '') .' '.$fbaStatus;
					$rowExcelData['personal_ref.'] = (isset($getExportRecordDetail->v_personal_ref) && !empty($getExportRecordDetail->v_personal_ref) ?   $getExportRecordDetail->v_personal_ref   : '' );
					$rowExcelData['from_warehouse_country_-_warehouse'] = (isset($getExportRecordDetail->fromWarehouseCountry) && !empty($getExportRecordDetail->fromWarehouseCountry->v_country_name) ? $getExportRecordDetail->fromWarehouseCountry->v_country_name : '') . (isset($getExportRecordDetail->warehouseInfo) && !empty($getExportRecordDetail->warehouseInfo->v_warehouse_name) ? ' - ' . $getExportRecordDetail->warehouseInfo->v_warehouse_name : '');
					$rowExcelData['pick-up_date_from_warehouse'] = (isset($getExportRecordDetail->dt_pick_up_date_from_warehouse) && !empty($getExportRecordDetail->dt_pick_up_date_from_warehouse) ?  clientDate( $getExportRecordDetail->dt_pick_up_date_from_warehouse )  : '' );
					
					$finalExportData[] = $rowExcelData;
				}
			}
		
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.good-out-country-to-port')]);
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'uk-other-country-us-port/good-out-country-port-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	
	public function create(){
		if(checkPermission(config('permission_constants.ADD_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-uk-other-country-us-port');
		
		$data['fromPortInfo'] = WarehouseMasterModel::whereNotIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['toPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('t_is_active',1)->where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['insuranceStatusDetails'] = insuranceStatus();
		$data['dangerousGoodsInfo'] = dangerousGoodsInfo();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->where('t_is_active',1)->orderBy('v_document_type_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::where('t_is_active',1)->whereNotIn('i_id' , $this->excludeStatusIds )->orderBy('i_sequence', 'ASC')->get();
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT') ] );
		$data['logisticPartnerRecordDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['countryDetails'] = CountryMasterModel::where('t_is_active',1)->orderBy('v_country_name', 'ASC')->get();
		$data['warehouseDetails'] = WarehouseMasterModel::where('t_is_active',1)->where('e_record_type', config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		return view($this->folderName . 'add-uk-other-country-us-port')->with($data);
	}
	
	public function edit($id){
		if(isset($this->secondUriSegment) && ( $this->secondUriSegment == 'edit' )){
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != true ){
				return redirect('access-denied');
			}
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		if( $recordId > 0 ){
			$whereData = $data = [];
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			$data['pageTitle'] = trans('messages.update-good-out-country-to-port');
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
				
			if(count($recordInfo) > 0){
	
				$errorFound = false;
				
				$data['fromPortInfo'] = WarehouseMasterModel::whereNotIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['toPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
				$data['insuranceStatusDetails'] = insuranceStatus();
				$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT') ] );
				$data['dangerousGoodsInfo'] = dangerousGoodsInfo();
				$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
				$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
				$data['logisticPartnerRecordDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				$data['countryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
				$data['warehouseDetails'] = WarehouseMasterModel::where('i_country_id', $recordInfo[0]->i_from_warehouse_country_id)->where('e_record_type', config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				
				$fbaSheetMasterDetails = (isset($data ['recordInfo']->fbaSheetMaster) && !empty($data ['recordInfo']->fbaSheetMaster) ? $data ['recordInfo']->fbaSheetMaster : []);
				
				$totalUnits = 0;
				$allUnitDetails = []; 
				
				if(!empty($fbaSheetMasterDetails)){
					foreach ($fbaSheetMasterDetails as $fbaSheetMasterDetail){
						if (!empty($fbaSheetMasterDetail->fbaSheetDetail) && count($fbaSheetMasterDetail->fbaSheetDetail) > 0){
							$allUnitDetails = ($fbaSheetMasterDetail->fbaSheetDetail->groupBy('v_fba_po_no')->toArray());
							
							$firstUnits = array_map( function($group){
								 return $group[0]['v_units'];
							} , $allUnitDetails);
							
							$totalUnits += array_sum($firstUnits);
						}
					}
				}
			
				$data['totalUnits'] = $totalUnits;
				
				$disableForm = '';
				$documentForm = '';
				$statusDisableForm = '';
				if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
					$data['pageTitle'] = trans('messages.view-uk-other-country-us-port');
					$disableForm = 'disabled';
					$documentForm = 'disabled';
					$statusDisableForm = 'disabled';
				}
				
				if( isset($data ['recordInfo']->i_status_id) && (  in_array( $data ['recordInfo']->i_status_id , [ config('constants.DELIVERED_STATUS_ID') ] ) ) ){
					if(empty($documentForm) && ( session()->get('role') == config('constants.ROLE_ADMIN') ) ){
						$statusDisableForm = '';
					} else {
						$statusDisableForm = 'disabled';
					}
					$disableForm = 'disabled';
					$documentForm = 'disabled';
				}
				
				if( empty($data ['recordInfo']->i_lastet_import_file_id) || (  (isset($data ['recordInfo']->uploadFBASheetInfo->e_status)) && ( $data ['recordInfo']->uploadFBASheetInfo->e_status != config('constants.SUCCESS_STATUS')) ) ){
					$data['statusMasterRecordDetails'] = StatusMasterModel::whereNotIn('i_id' , $this->excludeStatusIds )->orderBy('i_sequence', 'ASC')->get();
				} else {
					$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
				}
				
				if( isset($data ['recordInfo']->i_status_id) && (  in_array( $data ['recordInfo']->i_status_id , [ config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') ] ) ) ){
					$disableForm = 'disabled';
				}
				
				$data['disableForm'] = $disableForm;
				$data['documentForm'] = $documentForm;
				$data['statusDisableForm'] = $statusDisableForm;
				return view ( $this->folderName . 'add-uk-other-country-us-port' )->with ( $data );
			}
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	
	public function add(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		
		$disableForm = false;
		if($recordId > 0 ){
			$whereData = [];
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			
			$goodOutCountryPortRecordDetail = $this->crudModel->getRecordDetails($whereData);
			
			if( isset($goodOutCountryPortRecordDetail[0]->i_status_id) && (  in_array( $goodOutCountryPortRecordDetail[0]->i_status_id , [ config('constants.DELIVERED_STATUS_ID'), config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') ] ) ) ){
				$disableForm = true;
			}
			
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['way_of_transport'] = 'required';
		$formValidation['from_port_airport'] = 'required';
		$formValidation['to_port_airport'] = 'required';
		$formValidation['book_by'] = 'required';
		$formValidation['logistic_partner_uk'] = 'required';
		$formValidation['container_no_air_waybill_no'] = 'required';
		$formValidation['seal_no_house_waybill_no'] = 'required';
		$formValidation['etd_dispatch_date'] = 'required';
		$formValidation['eta_arrival_date'] = 'required';
		//$formValidation['total_amount'] = 'required';
		//$formValidation['total_curruncy'] = 'required';
		$formValidation['total_pallets'] = 'required';
		$formValidation['insurance_status'] = 'required';
		if ($disableForm != true){
			$formValidation['personal_ref'] = ['required', new UniquePersonalReferenceNumber($recordId)];
			$formValidation['from_warehouse_country'] = 'required';
			$formValidation['warehouse'] = 'required';
			$formValidation['pick_up_date_from_warehouse'] = 'required';
			$formValidation['booking_reference'] = 'required';
			$formValidation['arrival_date_at_usa_port'] = 'required';			
		} else {
			$formValidation['personal_ref'] = [new UniquePersonalReferenceNumber($recordId)];
		}
		$formValidation['dangerous_goods'] = 'required';
		$formValidation['goods_remarks'] = 'required';
		$formValidation['tracking_no'] = 'required';
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			$formValidation['delivery_date'] = 'required';
		}
		$validator = Validator::make ( $request->all (), $formValidation , [
				'way_of_transport.required' => __ ( 'messages.require-way-of-transport' ),
				'from_port_airport.required' => __ ( 'messages.require-from-port-airport' ),
				'to_port_airport.required' => __ ( 'messages.require-to-port-airport' ),
				'book_by.required' => __ ( 'messages.require-book-by' ),
				'logistic_partner_uk.required' => __ ( 'messages.require-logistic-partner-uk' ),
				'container_no_air_waybill_no.required' => __ ( 'messages.require-container-no-air-waybill-no' ),
				'seal_no_house_waybill_no.required' => __ ( 'messages.require-seal-no-house-waybill-no' ),
				'etd_dispatch_date.required' => __ ( 'messages.require-etd-dispatch-date' ),
				'eta_arrival_date.required' => __ ( 'messages.require-eta-arrival-date' ),
				'total_amount.required' => __ ( 'messages.require-amount' ),
				'total_curruncy.required' => __ ( 'messages.require-currency' ),
				'total_pallets.required' => __ ( 'messages.require-total-pallets' ),
				'tracking_no.required' => __ ( 'messages.require-tracking-no' ),
				'insurance_status.required' => __ ( 'messages.require-insurance-status' ),
				'personal_ref.required' => trans('messages.require-personal-ref'),
				'from_warehouse_country.required' => trans('messages.require-from-warehouse-country'),
				'warehouse.required' => trans('messages.require-warehouse'),
				'pick_up_date_from_warehouse.required' => trans('messages.require-pick-up-date-from-warehouse'),
				'booking_reference.required' => trans('messages.require-booking-reference'),
				'arrival_date_at_usa_port.required' => trans('messages.require-arrival-date-at-usa-port'),
				'dangerous_goods.required' => __ ( 'messages.require-dangerous-goods' ),
				'goods_remarks.required' => __ ( 'messages.require-goods-remarks' ),
				'delivery_date.required' => __ ( 'messages.require-delivery-date' ),
		] );
	
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=>trans('messages.good-out-country-to-port')]);
		$errorMessages = trans('messages.error-create',['module'=>trans('messages.good-out-country-to-port')]);
		
		
		DB::beginTransaction();
		try{
				
			$recordData = [];
			$goodOutCountryPortDocumentTypeCount = (!empty($request->input('good_out_country_port_document_type_count')) ? (int)($request->input('good_out_country_port_document_type_count')) : 1 );
			$goodOutCountryPortTransporterCount = (!empty($request->input('good_out_country_port_transporter_count')) ? (int)($request->input('good_out_country_port_transporter_count')) : 1 );
			$goodOutCountryPortShipmentValueCount = (!empty($request->input('good_out_country_port_shipment_value_count')) ? (int)($request->input('good_out_country_port_shipment_value_count')) : 1 );
			
			$recordData['e_transport_way'] = (!empty($request->input('way_of_transport')) ? ($request->input('way_of_transport')) : '' );
			
			$recordData['i_transport_from_id'] = (!empty($request->input('from_port_airport')) ? (int)Wild_tiger::decode($request->input('from_port_airport')) : 0 );
			$recordData['i_transport_to_id'] = (!empty($request->input('to_port_airport')) ? (int)Wild_tiger::decode($request->input('to_port_airport')) : 0 );
			$recordData['i_book_by_employee_id'] = (!empty($request->input('book_by')) ? (int)Wild_tiger::decode($request->input('book_by')) : 0 );
			$recordData['i_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner_uk')) ? (int)Wild_tiger::decode($request->input('logistic_partner_uk')) : 0 );
			
			$recordData['v_container_air_waybill_no'] = (!empty($request->input('container_no_air_waybill_no')) ? ($request->input('container_no_air_waybill_no')) : null );
			$recordData['v_seal_house_waybill_no'] = (!empty($request->input('seal_no_house_waybill_no')) ? ($request->input('seal_no_house_waybill_no')) : null );
			$recordData['dt_est_dispatch_date'] = (!empty($request->input('etd_dispatch_date')) ? dbDate($request->input('etd_dispatch_date')) : null ); 
			$recordData['dt_est_port_arrival_date'] = (!empty($request->input('eta_arrival_date')) ? dbDate($request->input('eta_arrival_date')) : null );
			$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
			$recordData['i_goods_out_currency_id'] = (!empty($request->input('total_curruncy')) ? (int)Wild_tiger::decode($request->input('total_curruncy')) : 0 );
			$recordData['d_payment_value'] = (!empty($request->input('total_amount')) ? ($request->input('total_amount')) : 0 );
			$recordData['i_total_pallets'] = (!empty($request->input('total_pallets')) ? ($request->input('total_pallets')) : 0 );
			$recordData['e_dangerous_goods'] = (!empty($request->input('dangerous_goods')) ? ($request->input('dangerous_goods')) : null );
			$recordData['v_goods_remark'] = (!empty($request->input('goods_remarks')) ? ($request->input('goods_remarks')) : null );
			$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? ($request->input('tracking_no')) : null );
			$recordData['v_tracking_link'] = (!empty($request->input('tracking_link')) ? ($request->input('tracking_link')) : null );
			$recordData['e_insurance_status'] = (!empty($request->input('insurance_status')) ? ($request->input('insurance_status')) : null );
			$recordData['v_personal_ref'] = (!empty($request->input('personal_ref')) ? trim($request->input('personal_ref')) : '');
			$recordData['i_from_warehouse_country_id'] = (!empty($request->input('from_warehouse_country')) ? (int)Wild_tiger::decode($request->input('from_warehouse_country')) : 0);
			$recordData['i_warehouse_id'] = (!empty($request->input('warehouse')) ? (int)Wild_tiger::decode($request->input('warehouse')) : 0);
			$recordData['dt_pick_up_date_from_warehouse'] = (!empty($request->input('pick_up_date_from_warehouse')) ? dbDate($request->input('pick_up_date_from_warehouse')) : '');
			$recordData['v_comments'] = (!empty($request->input('comments')) ? trim($request->input('comments')) : null);
			$recordData['v_booking_ref'] = (!empty($request->input('booking_reference')) ? trim($request->input('booking_reference')) : '');
			$recordData['d_total_value_of_container'] = (!empty($request->input('total_value_of_container_invoice_no')) ? $request->input('total_value_of_container_invoice_no') : 0);
			$recordData['dt_arrival_date_at_usa_port'] = (!empty($request->input('arrival_date_at_usa_port')) ? dbDate($request->input('arrival_date_at_usa_port')) : '');
			$recordData['i_status_id'] = $statusRecordId;
			$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? ($request->input('status_comments')) :null );
				
			if($recordId > 0){
				
				$successMessage =  trans('messages.success-update',['module'=>trans('messages.good-out-country-to-port')]);
				$errorMessages = trans('messages.error-update',['module'=>trans('messages.good-out-country-to-port')]);
	
				$goodOutCountryPortDetails = (isset($goodOutCountryPortRecordDetail) && !empty($goodOutCountryPortRecordDetail) ? $goodOutCountryPortRecordDetail[0] : []);
	
				if(!empty($goodOutCountryPortDetails->shipmentInfo)){
					$removeCountryShipmentIds = [];
					foreach ($goodOutCountryPortDetails->shipmentInfo as $shipmentDetail){
						$shipmentInfoId = (!empty($shipmentDetail->i_id) ? $shipmentDetail->i_id : '');
						
						if(!empty($request->input('edit_shipment_invoices_'.$shipmentInfoId))){
							$rowData = [];
							$rowData['v_invoice'] = trim($request->input('edit_shipment_invoices_'.$shipmentInfoId));
							$rowData['d_amount'] = (!empty($request->input('edit_shipment_amount_'.$shipmentInfoId)) ? trim($request->input('edit_shipment_amount_'.$shipmentInfoId)) : 0);
							$rowData['i_currency_id'] = (!empty($request->input('edit_shipment_currency_'.$shipmentInfoId)) ? (int)Wild_tiger::decode($request->input('edit_shipment_currency_'.$shipmentInfoId)) : 0);
							$rowData['d_cov_rate'] = (!empty($request->input('edit_shipment_cov_rate_'.$shipmentInfoId)) ? trim($request->input('edit_shipment_cov_rate_'.$shipmentInfoId)) : 0);
							$rowData['d_total_value_of_container'] = $rowData['d_amount'] * $rowData['d_cov_rate'];
							
							if($request->hasFile('edit_shipment_attachment_'.$shipmentInfoId)){
								$uploadFile = $this->uploadFile($request, 'edit_shipment_attachment_'.$shipmentInfoId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$rowData['v_attachment_path'] = $uploadFile['filePath'];
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ($this->redirectUrl);
								}
							}
							
							$this->crudModel->updateTableData(config('constants.COUNTRY_TO_PORT_GOODS_OUT_SHIPMENT_VALUES_TABLE'), $rowData, ['i_id' => $shipmentInfoId]);
						} else {
							$removeCountryShipmentIds[] = $shipmentInfoId;
						}
					}
					
					if (!empty($removeCountryShipmentIds)){
						$deleteData = [];
						$deleteData['where_in'] = ['i_id' => $removeCountryShipmentIds];
						$this->crudModel->deleteTableData($this->shipmentTableName, [], $deleteData);
					}
				}
				
				if(!empty($goodOutCountryPortDetails->invoiceInfo)){
					foreach ($goodOutCountryPortDetails->invoiceInfo as $goodOutCountryPortInvoiceDetail){
						if(!empty($request->input('edit_name_'.$goodOutCountryPortInvoiceDetail->i_id))){
							$goodInInvoice = [];
							$goodInInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$goodOutCountryPortInvoiceDetail->i_id)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$goodOutCountryPortInvoiceDetail->i_id)) : 0 );
							$goodInInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_inv_no_'.$goodOutCountryPortInvoiceDetail->i_id) :'' );
							$goodInInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_freight_'.$goodOutCountryPortInvoiceDetail->i_id) : 0 );
							$goodInInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_custom_'.$goodOutCountryPortInvoiceDetail->i_id) :0 );
							$goodInInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_duty_'.$goodOutCountryPortInvoiceDetail->i_id)  : 0 );
							$goodInInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_other_'.$goodOutCountryPortInvoiceDetail->i_id) :0 );
							$goodInInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_vat_'.$goodOutCountryPortInvoiceDetail->i_id) : 0 );
							$totalCharges = $goodInInvoice['d_freight_charge'] + $goodInInvoice['d_custom_charge'] + $goodInInvoice['d_duty_charge'] + $goodInInvoice['d_other_charge'] + $goodInInvoice['d_vat_charge'];
							$goodInInvoice['d_total_charge'] = $totalCharges;
							$goodInInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_amount_'.$goodOutCountryPortInvoiceDetail->i_id)) ? (int)Wild_tiger::decode($request->input('edit_amount_'.$goodOutCountryPortInvoiceDetail->i_id)) : 0);
							$goodInInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$goodOutCountryPortInvoiceDetail->i_id)) ? $request->input('edit_cov_rate_'.$goodOutCountryPortInvoiceDetail->i_id) : 0);
							$finalCharges = ($totalCharges * $goodInInvoice['d_conversion_rate']);
							$goodInInvoice['d_final_charge'] = $finalCharges;
							if($request->hasFile('edit_invoice_file_'.$goodOutCountryPortInvoiceDetail->i_id)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$goodOutCountryPortInvoiceDetail->i_id,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$goodInInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							}else {
								$removeFiles = (!empty($request->input('remove_invoice_'.$goodOutCountryPortInvoiceDetail->i_id)) ? explode("," , $request->input('remove_invoice_'.$goodOutCountryPortInvoiceDetail->i_id) ) : []  );
								$previousUploadFiles = (!empty($goodOutCountryPortInvoiceDetail->v_invoice_file_path) ? json_decode($goodOutCountryPortInvoiceDetail->v_invoice_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$goodInInvoice['v_invoice_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							
							if( ( $goodInInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($goodInInvoice['v_invoice_no']) ) ){
								$goodInInvoiceUpdate = $this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE') , $goodInInvoice , [ 'i_id' => $goodOutCountryPortInvoiceDetail->i_id] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $goodOutCountryPortInvoiceDetail->i_id] );
						}
					}
				}
	
				if(!empty($goodOutCountryPortDetails->documentInfo)){
					foreach ($goodOutCountryPortDetails->documentInfo as $goodOutCountryPortDocumentDetail){
						if(!empty($request->input('edit_type_'.$goodOutCountryPortDocumentDetail->i_id))){
							$goodInDocument = [];
							$goodInDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$goodOutCountryPortDocumentDetail->i_id)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$goodOutCountryPortDocumentDetail->i_id)) :0);
							$goodInDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$goodOutCountryPortDocumentDetail->i_id)) ? $request->input('edit_remarks_'.$goodOutCountryPortDocumentDetail->i_id) : null);
							if($request->hasFile('edit_file_'.$goodOutCountryPortDocumentDetail->i_id)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$goodOutCountryPortDocumentDetail->i_id,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$goodInDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							}else {
								$removeFiles = (!empty($request->input('remove_document_'.$goodOutCountryPortDocumentDetail->i_id)) ? explode("," , $request->input('remove_document_'.$goodOutCountryPortDocumentDetail->i_id) ) : []  );
								$previousUploadFiles = (!empty($goodOutCountryPortDocumentDetail->v_document_file_path) ? json_decode($goodOutCountryPortDocumentDetail->v_document_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$goodInDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null ); 
							}
							$goodOutCountryPortDocumentDetailUpdate = $this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $goodInDocument , [ 'i_id' => $goodOutCountryPortDocumentDetail->i_id] );
							
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $goodOutCountryPortDocumentDetail->i_id] );
						}
					}
				}
				$this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
	
			} else {
				$goodOCountryPortGenerateNo = config('constants.GOOD_OUT_COUNTRY_TO_PORT_CON').'-'. config('constants.GOOD_IN_BUYER_NUMBER').'-'.$this->todayDate;
					
				$goodOutCountryPortMasterRecordDetails = $this->crudModel->selectData(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') ,['i_id']);
					
				if(!empty($goodOutCountryPortMasterRecordDetails)){
					$goodInCountryPortMasterRecordCount = count($goodOutCountryPortMasterRecordDetails);
					$count = ( ( (!empty($goodInCountryPortMasterRecordCount)) && ( $goodInCountryPortMasterRecordCount > 0 ) ) ? ( $goodInCountryPortMasterRecordCount + 1  ) : 1 );
					$generateNumber = threeNumberSeries($count);
					$goodOCountryPortGenerateNo = config('constants.GOOD_OUT_COUNTRY_TO_PORT_CON').'-'. $generateNumber.'-'.$this->todayDate;
	
				}
				$recordData['e_process_status'] = config('constants.PENDING_STATUS');
				$recordData['v_country_to_port_record_no'] = $goodOCountryPortGenerateNo;
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
			}
			
			$finalShipmentDetails = [];
			for ($i = 0; $i <= $goodOutCountryPortShipmentValueCount; $i++){
				if (!empty($request->input('shipment_invoices_'.$i))){
					$rowData = [];
					$rowData['v_invoice'] = trim($request->input('shipment_invoices_'.$i));
					$rowData['d_amount'] = (!empty($request->input('shipment_amount_'.$i)) ? trim($request->input('shipment_amount_'.$i)) : 0);
					$rowData['i_currency_id'] = (!empty($request->input('shipment_currency_'.$i)) ? (int)Wild_tiger::decode($request->input('shipment_currency_'.$i)) : 0);
					$rowData['d_cov_rate'] = (!empty($request->input('shipment_cov_rate_'.$i)) ? trim($request->input('shipment_cov_rate_'.$i)) : 0);
					$rowData['d_total_value_of_container'] = $rowData['d_amount'] * $rowData['d_cov_rate'];
					if($request->hasFile('shipment_attachment_'.$i)){
						$uploadFile = $this->uploadFile($request, 'shipment_attachment_'.$i,'image_doc_pdf_xls');
						if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
							$rowData['v_attachment_path'] = $uploadFile['filePath'];
						} else {
							DB::rollback();
							Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
							return redirect ($this->redirectUrl);
						}
					}
					
					$finalShipmentDetails[] = $rowData;
				}
			}
			
			if (!empty($finalShipmentDetails)){
				$finalShipmentDetails = array_map(function($finalShipmentDetail) use ($insertRecord){
					$finalShipmentDetail['i_country_to_port_goods_out_master_id'] = $insertRecord;
					$finalShipmentDetail['i_created_id'] = session()->get('user_id');
					$finalShipmentDetail['dt_created_at'] = date('Y-m-d H:i:s');
					$finalShipmentDetail['v_ip'] = \Illuminate\Support\Facades\Request::ip();
					return $finalShipmentDetail;
				}, $finalShipmentDetails);
			
				DB::table($this->shipmentTableName)->insert($finalShipmentDetails);
			}
				
			for ($i = 0; $i <= $goodOutCountryPortTransporterCount;$i++){
				$rowData = [];
				$rowData['i_country_to_port_goods_out_master_id'] = $insertRecord;
				$rowData['i_logistic_partner_master_id'] = (!empty($request->input('name_'.$i)) ? (int)Wild_tiger::decode($request->input('name_'.$i)) : 0);
				$rowData['v_invoice_no'] = (!empty($request->input('inv_no_'.$i)) ? $request->input('inv_no_'.$i) : '');
				$rowData['d_freight_charge'] = (!empty($request->input('freight_'.$i)) ? ($request->input('freight_'.$i)) : null );
				$rowData['d_custom_charge'] = (!empty($request->input('custom_'.$i)) ? ($request->input('custom_'.$i)) : null );
				$rowData['d_duty_charge'] = (!empty($request->input('duty_'.$i)) ? ($request->input('duty_'.$i)) : null );
				$rowData['d_other_charge'] = (!empty($request->input('other_'.$i)) ? ($request->input('other_'.$i)) : null );
				$rowData['d_vat_charge'] = (!empty($request->input('vat_'.$i)) ? ($request->input('vat_'.$i)) : null );
				$rowData['i_invoice_currency_id'] = (!empty($request->input('amount_'.$i)) ? (int)Wild_tiger::decode($request->input('amount_'.$i)) : 0 );
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
				if( ( $rowData ['i_logistic_partner_master_id'] > 0 ) && (!empty($rowData ['v_invoice_no']))){
					$insertGoodOutCountryPortInvoice = $this->crudModel->insertTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE') , $rowData);
				}
			}
				
				
			for ($i = 0; $i <= $goodOutCountryPortDocumentTypeCount;$i++){
				$rowData = [];
				$rowData['i_country_to_port_goods_out_master_id'] = $insertRecord;
				$rowData['i_document_type_id'] = (!empty($request->input('type_'.$i)) ? (int)Wild_tiger::decode($request->input('type_'.$i)) :0);
				$rowData['v_document_remark'] = (!empty($request->input('remarks_'.$i)) ? $request->input('remarks_'.$i) : null);
				$rowData['v_document_file_path'] = "";
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
				if( (!empty($rowData ['v_document_file_path'])) && (!empty($rowData ['i_document_type_id']))){
					$insertGoodOutCountryPortDocumentDetail = $this->crudModel->insertTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $rowData);
				}
			}
			$result = true;
		}catch(\Exception $e){
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
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != true ){
			return redirect('access-denied');
		}
		$getFBASheetDetilasModel = new FBASheeteDetailModel();
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$goodOutCountryPortDetailData['t_is_active'] = 0;
			$goodOutCountryPortDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=>trans('messages.good-out-country-to-port')]);
			$errorMessages = trans('messages.error-delete',['module'=>trans('messages.good-out-country-to-port')]);
	
			$getMasterRecordInfo = $this->crudModel->getSingleRecordById(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'),['i_id' , 'e_process_status'] , [ 't_is_deleted != ' => 1 , 'i_id' => $recordId ] );
			
			if(!empty($getMasterRecordInfo) && ($getMasterRecordInfo->e_process_status == config('constants.COMPLETED_STATUS')) ){
				Wild_tiger::setFlashMessage ( 'danger',trans('messages.error-record-used-into-port-to-agent'));
				return redirect()->back();
			}
			$fbaWhere = [];
			$fbaWhere['country_to_port_goods_out_id'] = $recordId;
			$getFBASheetDetails = $getFBASheetDetilasModel->getFBASheetDetails($fbaWhere); 
			$getFBASheetDetails = ( isset($getFBASheetDetails) ? json_decode(json_encode($getFBASheetDetails),true) : [] );
				
			DB::beginTransaction();
	
			$result = false;
			try{
				$this->crudModel->deleteTableData(  config('constants.COUNTRY_TO_PORT_GOODS_OUT_SHIPMENT_VALUES_TABLE') ,  $goodOutCountryPortDetailData , [ 'i_country_to_port_goods_out_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.COUNTRY_TO_PORT_GOODS_OUT_DOCUMENT_MASTER_TABLE') ,  $goodOutCountryPortDetailData , [ 'i_country_to_port_goods_out_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.COUNTRY_TO_PORT_GOODS_OUT_INVOICE_MASTER_TABLE') ,  $goodOutCountryPortDetailData , [ 'i_country_to_port_goods_out_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE') ,  $goodOutCountryPortDetailData , [ 'i_container_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE') ,  $goodOutCountryPortDetailData , [ 'i_country_to_port_goods_out_master_id' => $recordId ] );
				
				if(!empty($getFBASheetDetails)){
					$fbaMasterId = (!empty($getFBASheetDetails) ? array_column($getFBASheetDetails, 'i_fba_sheet_master_id') : []);
					$goodOutCountryPortDetailData ['i_deleted_id'] = session()->get('user_id');
					$goodOutCountryPortDetailData ['dt_deleted_at'] = date('Y-m-d H:i:s');
					DB::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'))->whereIn('i_fba_sheet_master_id', $fbaMasterId )->update($goodOutCountryPortDetailData);
				}
				
				
				$this->crudModel->deleteTableData( $this->tableName,  $goodOutCountryPortDetailData , [ 'i_id' => $recordId ] );
	
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
	
	public function uploadFBASheet(Request $request){
		
		
		$formValidation =[];
		$formValidation['country_to_port_goods_out_record_id'] = ['required'];
		$formValidation['upload_fba_file'] = ['required' ];
		
		$checkValidation =Validator::make($request->all(),$formValidation,
				[
						'country_to_port_goods_out_record_id.required'=>trans('messages.require-record'),
						'upload_fba_file.required'=> trans('messages.required-upload-excel-file'),
						'upload_fba_file.mimes'=> trans('messages.only-allowed-file-types' , [ 'fileTypes' => 'Excel' ] ),
							
				]
		);
			
		if($checkValidation->fails() != false){
			$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $this->moduleName ] ) ) );
		}
		
		if($request->hasFile('upload_fba_file')){
			$uploadFilePath = ''; 
			$uploadFile = $this->uploadFile($request, 'upload_fba_file' ,'xls');
			if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
				$uploadFilePath = $uploadFile['filePath'];
			} else {
				$this->ajaxResponse(101, isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.system-error'));
			}
			
			if(empty($uploadFilePath)){
				$this->ajaxResponse(101, trans('messages.required-upload-excel-file'));
			}
			
			$recordId =  (!empty($request->input('country_to_port_goods_out_record_id')) ? (int)Wild_tiger::decode($request->input('country_to_port_goods_out_record_id')) : 0 );
			
			if(!empty($uploadFilePath)){
				
				$getMasterRecordInfo = $this->crudModel->getSingleRecordById(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'),['i_id' , 'e_process_status'] , [ 't_is_deleted != ' => 1 ] );
				
				if(!empty($getMasterRecordInfo) && ($getMasterRecordInfo->e_process_status == config('constants.COMPLETED_STATUS')) ){
					//$this->ajaxResponse(101, $this->lang->line('system-error'));
				}
				 
				$importFileData = [];
				$importFileData['i_country_to_port_goods_out_master_id'] = $recordId;
				$importFileData['v_file_name'] = basename($uploadFilePath);
				$importFileData['v_file_path'] = $uploadFilePath;
				$importFileData['e_status'] = config('constants.PENDING_STATUS');
				
				DB::beginTransaction();
				
				$result = false;
				
				try{
					
					$insertRecord = $this->crudModel->insertTableData( config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE') , $importFileData  );
					$this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') , [ 'i_lastet_import_file_id' =>  $insertRecord ] , [ 'i_id' =>  $recordId  ] );
					$result = true;
					
				}catch(\Exception $e){
					DB::rollback();
					$this->ajaxResponse(101, $e->getMessage());
				}
				
				if( $result != false ){
					
					$where = [];
					$where['master_id'] = $recordId;
					$where['edit_record'] = true;
					
					$recordDetail = $this->crudModel->getRecordDetails($where);
					
					$recordInfo = [];
					$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
					$recordInfo['recordDetail'] = ( isset($recordDetail[0]) ? $recordDetail[0] : "" );
					
					$html = view (config('constants.AJAX_VIEW_FOLDER') . 'uk-other-country-us-port/single-good-out-country-port-list')->with ( $recordInfo )->render();
					
					DB::commit();
					$this->ajaxResponse(1, trans('messages.success-file-added-into'),['html' => $html ]);
				} else {
					DB::rollback();
					$this->ajaxResponse(101, trans('messages.error-file-added-into'));
				}
			}
		}
		$this->ajaxResponse(101, trans('messages.required-upload-excel-file'));
		
	}
	
	public function getUploadedSheedFailedData(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		
		$html  = "";
		if( $recordId > 0 ){
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			$recordDetail = ( isset($recordInfo[0]) ? $recordInfo[0] : [] );
			
			if( (!empty($recordDetail)) && (isset($recordDetail->uploadFBASheetInfo->i_id)) && ( $recordDetail->uploadFBASheetInfo->i_id > 0 )  ){
				$errorDetails = ( ( isset($recordDetail->uploadFBASheetInfo->v_response) && (!empty($recordDetail->uploadFBASheetInfo->v_response)) ) ? explode("," , $recordDetail->uploadFBASheetInfo->v_response ) : [] );
				if(!empty($errorDetails)){
					$errorIndex = 0;
					foreach($errorDetails as $errorDetail){
						$html .= '<tr>';
						$html .= '<td class="text-center">'.++$errorIndex.'</td>';
						$html .= '<td>'.$errorDetail.'</td>';
						$html .= '</tr>';
					}
				}
			} 
		}
		if(empty($html)){
			$html = '<tr><td>'.trans('messages.no-record-found').'</td></tr>';
		}
		
		echo $html;die;
	}
	public function showFbaSheetRecordDetails($countryToPortRecordId , $agentToWarehouseId = null ){
		if(checkPermission(config('permission_constants.VIEW_FBA_SHEET_MASTER')) != true){
			return redirect('access-denied');
		}
		$whereData = $data = $where = [];
		
		$data['pageTitle'] = trans('messages.view-fba-sheet');
		$countryToPortRecordId = (!empty($countryToPortRecordId) ? (int)Wild_tiger::decode($countryToPortRecordId) : 0 );
		
		if($countryToPortRecordId > 0 ){
			
			$agentToWarehouseId = (!empty($agentToWarehouseId) ? (int)Wild_tiger::decode($agentToWarehouseId) : 0 );
			
			$whereData['country_to_port_goods_out_id'] = $countryToPortRecordId;
			
			$fbdDetailModal = new FBASheeteDetailModel();
			
			$getFbaRecordDetails =  $fbdDetailModal->getFBASheetDetails($whereData);
			
			$allUsedFBASheetDetailIds = [];
			if( $agentToWarehouseId > 0 ){
				$this->agentToWarehouseModel = new AgentToWarehouseModel();
				$whereData['master_id'] = $agentToWarehouseId;
				$whereData['edit_record'] = true;
				$data['pageTitle'] = trans('messages.view-fba-sheet');
				$agentToWarehouseRecordInfo = $this->agentToWarehouseModel->getRecordDetails($whereData);
				if(isset($agentToWarehouseRecordInfo[0]->detailInfo)){
					$allDetails = json_decode(json_encode($agentToWarehouseRecordInfo[0]->detailInfo),true);
					$allUsedFBASheetDetailIds =  (!empty($allDetails) ? array_column($allDetails, 'i_fba_sheet_detail_id') : [] );
				}
				
			}
			
			if(!empty($getFbaRecordDetails)){
				$finalFBARecordDetails = [];
				if(!empty($getFbaRecordDetails)){
					foreach($getFbaRecordDetails as $getFbaRecordDetail){
						if( $agentToWarehouseId > 0 ){
							if( in_array($getFbaRecordDetail->i_id,$allUsedFBASheetDetailIds) ){
								$finalFBARecordDetails[$getFbaRecordDetail->v_fba_po_no][] = $getFbaRecordDetail;
							}
						} else {
							$finalFBARecordDetails[$getFbaRecordDetail->v_fba_po_no][] = $getFbaRecordDetail;
						}
						
					}
				}
				$data['getFbaRecordInfo'] = $getFbaRecordDetails;
				$data['getFbaRecordDetails'] = $finalFBARecordDetails;
				$data['totalRecordCount'] = (!empty($getFbaRecordDetails) ? array_sum(array_map("count", $finalFBARecordDetails)) : 0 );
				
				return view($this->folderName . 'view-fba-sheet')->with($data);
			}
		}
	}
	public function editFBASheetModel(Request $request){
		
		$fbaSheetRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$dataStatus = (!empty($request->input('data_status')) ? ($request->input('data_status')) : null);
		
		if($dataStatus == config('constants.SELECTION_YES')){
			if(checkPermission(config('permission_constants.EDIT_FBA_SHEET_MASTER')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_FBA_SHEET_MASTER')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		}
		if($fbaSheetRecordId > 0){
			
			$fbaSheetRecordInfo = $this->crudModel->getFbaSheetRecordDetails([ 'i_id' => $fbaSheetRecordId ] );
			if(!empty($fbaSheetRecordInfo)){
				$data ['fbaSheetRecordInfo']= $fbaSheetRecordInfo;
				
			}
		}
		$data['designationDetails'] = wayToWarehouseDetails();
		$data['statusInfo'] = $dataStatus;
		
		$html = view ($this->folderName . 'add-fba-sheet-model')->with ( $data )->render();
		echo $html;die;
	}
	public function addFBASheetModelDetails(Request $request){
		/* if(checkPermission(config('permission_constants.ADD_FBA_SHEET_MASTER')) != true){
			$this->ajaxResponse(101, trans('messages.access-denied'));
		} */
		$fbaSheetRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$oldFbaInvoiceNo = (!empty($request->fba_po_invoice_no) ? ($request->fba_po_invoice_no) : '');
		$dataStatus = (!empty($request->input('data_status')) ? ($request->input('data_status')) : null);
		$designation = (!empty($request->input('destination')) ? ($request->input('destination')) : '' );
		$units = (!empty($request->input('units')) ? ($request->input('units')) : null );
		$company = (!empty($request->input('company')) ? ($request->input('company')) : '' );
		$location = (!empty($request->input('location')) ? ($request->input('location')) : '' );
		$fbaNo = (!empty($request->input('fba_no')) ? $request->input('fba_no') : '');
		
		
		$formValidation =[];
		if($dataStatus != config('constants.SELECTION_YES')){
			$formValidation['fba_no'] = ['required',new UniqueFBAInvoiceNo($fbaSheetRecordId,$oldFbaInvoiceNo)];
			if( in_array ( $designation  , [ config('constants.AMAZON_FBA_SHEET') , config('constants.CUSTOMER_FBA_SHEET') ] ) ){
				$formValidation['company'] = 'required';
			}
			
			$formValidation['products'] = 'required';
			$formValidation['location'] = 'required';
			
		}
		$formValidation['ref_id'] = 'required';
		
		
		$checkValidation =Validator::make($request->all(),$formValidation,
				[
						'fba_no.required'=>trans('messages.require-fba-invoice'),
						'ref_id.required'=>trans('messages.require-ref-id'),
						'company.required'=>trans('messages.require-compnay'),
						'products.required'=>trans('messages.require-product'),
						'location.required'=>trans('messages.require-location'),
				]
		);
			
		if($checkValidation->fails() != false){
			$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.fba-sheet') ] ) ) );
		}
		
		
		$successMessage =  trans('messages.success-create',['module'=> trans('messages.fba-sheet')]);
		$errorMessages = trans('messages.error-create',['module'=> trans('messages.fba-sheet')]);
		
		$result = false;
		$recordData = [];
		
		$companyShortCodeDetails = CompanyMasterModel::where('t_is_deleted' , 0 )->get();
		$customerMasterDetails = CustomerMasterModel::where('t_is_deleted' , 0 )->get();
		$locationMasterCodeDetails = WarehouseMasterModel::where('t_is_deleted' , 0 )->where('e_record_type' , config('constants.LOCATION'))->get();
		$warehouseDetails = WarehouseMasterModel::where('t_is_deleted' , 0 )->where('e_record_type' , config('constants.WAREHOUSE'))->where('i_country_id' ,config('constants.USA') )->get();
		$customerMasterCodeDetails = CustomerDetailModel::where('t_is_deleted' , 0 )->get();
		
		$companyShortCodeInfo = (!empty($companyShortCodeDetails) ? array_column(objectToArray($companyShortCodeDetails), 'v_company_short_code') : [] );
		$customerNameMasterInfo = (!empty($customerMasterDetails) ? array_column(objectToArray($customerMasterDetails), 'v_customer_name') : [] );
		$locationMasterCodeInfo = (!empty($locationMasterCodeDetails) ? array_column(objectToArray($locationMasterCodeDetails), 'v_warehouse_code') : [] );
		$warehouseCodeInfo = (!empty($warehouseDetails) ? array_column(objectToArray($warehouseDetails), 'v_warehouse_code') : [] );
		$customerMasterCodeInfo = (!empty($customerMasterCodeDetails) ? array_column(objectToArray($customerMasterCodeDetails), 'v_customer_code') : [] );
		
		if($dataStatus != config('constants.SELECTION_YES')){
			if(!empty($designation)){
				switch ($designation){
					case (config('constants.AMAZON_FBA_SHEET')):
						$searchCompanyCodeKey = array_search($company, $companyShortCodeInfo);
						$searchLocationMasterCodeKey = array_search($location, $locationMasterCodeInfo);
						
						if(strlen($searchCompanyCodeKey) > 0 ){
							$recordData['i_amazon_company_short_code_id'] = ( isset($companyShortCodeDetails[$searchCompanyCodeKey]->i_id) ? $companyShortCodeDetails[$searchCompanyCodeKey]->i_id : null );
						} else {
							$this->ajaxResponse(101, trans ( 'messages.error-not-into-master-info', [ 'columnName' => trans('messages.company') ] ) );
						}
						
						
						if(strlen($searchLocationMasterCodeKey) > 0 ){
							$recordData['i_amazon_location_code_id'] = ( isset($locationMasterCodeDetails[$searchLocationMasterCodeKey]->i_id) ? $locationMasterCodeDetails[$searchLocationMasterCodeKey]->i_id : null );
						} else {
							$this->ajaxResponse(101, trans ( 'messages.error-not-into-master-info', [ 'columnName' => trans('messages.location')  ] ) );
						}
						
						break;
					case (config('constants.CUSTOMER_FBA_SHEET')):
						$searchCustomerNameKey = array_search($company, $customerNameMasterInfo);
						$searchCustomerCodeKey = array_search($location, $customerMasterCodeInfo);
						
						if(strlen($searchCustomerNameKey) > 0 ){
							$recordData['i_customer_company_name_id'] = ( isset($customerMasterDetails[$searchCustomerNameKey]->i_id) ? $customerMasterDetails[$searchCustomerNameKey]->i_id : null );
						} else {
							$this->ajaxResponse(101, trans ( 'messages.error-not-into-master-info', [ 'columnName' => trans('messages.company') ] ) );
						}
						
						if(strlen($searchCustomerCodeKey) > 0 ){
							$recordData['i_customer_customer_code_id'] = ( isset($customerMasterCodeDetails[$searchCustomerCodeKey]->i_id) ? $customerMasterCodeDetails[$searchCustomerCodeKey]->i_id : null );
						} else {
							$this->ajaxResponse(101, trans ( 'messages.error-not-into-master-info', [ 'columnName' => trans('messages.location')  ] ) );
						}
						break;
					case (config('constants.WAREHOUSE_FBA_SHEET')):
						$searchWarehouseCodeKey = array_search($location, $warehouseCodeInfo);
						if(strlen($searchWarehouseCodeKey) > 0 ){
							$recordData['i_warehouse_warehouse_code_id'] = ( isset($warehouseDetails[$searchWarehouseCodeKey]->i_id) ? $warehouseDetails[$searchWarehouseCodeKey]->i_id : null );
						} else {
							$this->ajaxResponse(101, trans ( 'messages.error-not-into-master-info', [ 'columnName' => trans('messages.location')  ] ) );
						}
						break;
				}
			}
		}
		$recordData['e_destination'] = $designation;
		$recordData['v_units'] = $units;
		$recordData['v_ref_id'] = (!empty($request->input('ref_id')) ? ($request->input('ref_id')) : '' );
		$recordData['v_company_code'] = $company;
		$recordData['v_product'] = (!empty($request->input('products')) ? ($request->input('products')) : '' );
		$recordData['v_location_code'] = $location;
		$recordData['v_sku'] = (!empty($request->input('sku')) ? ($request->input('sku')) : null );
		$recordData['i_boxes_units'] = (!empty($request->input('boxes_units')) ? ($request->input('boxes_units')) : null );
		$recordData['v_amazon_address'] = (!empty($request->input('amazon_address')) ? ($request->input('amazon_address')) : null );
		$recordData['v_boxes'] = (!empty($request->input('boxes')) ? ($request->input('boxes')) : null );
		$recordData['v_pallet'] = (!empty($request->input('pallet')) ? ($request->input('pallet')) : null );
		$recordData['i_total_no_of_pallets'] = (!empty($request->input('total_no_of_pallets')) ? ($request->input('total_no_of_pallets')) : null );
		$recordData['v_pallet_dimension'] = (!empty($request->input('pallet_dimension')) ? ($request->input('pallet_dimension')) : null );
		$recordData['v_pallet_weight'] = (!empty($request->input('pallet_weight')) ? ($request->input('pallet_weight')) : null );
		$recordData['i_pallet_no'] = (!empty($request->input('pallet_weight')) ? ($request->input('pallet_number')) : null );
		$recordData['v_fba_po_no'] = (!empty($request->input('fba_po_invoice_no')) ? ($request->input('fba_po_invoice_no')) : '' );
		$recordData['i_fba_sheet_master_id'] =(!empty($request->input('fba_master_id')) ? (int)Wild_tiger::decode($request->input('fba_master_id')) : 0 );
		$recordData['v_fba_value'] =(!empty($request->input('fba_value')) ? $request->input('fba_value') : null );
		
		if($fbaSheetRecordId > 0){
			
			if($dataStatus == config('constants.SELECTION_YES')){
				$successMessage =  trans('messages.success-update',['module'=> trans('messages.fba-sheet')]);
				$errorMessages = trans('messages.error-update',['module'=> trans('messages.fba-sheet')]);
				
				$result = $this->crudModel->updateTableData( config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') , $recordData,['i_id' => $fbaSheetRecordId] );
				
				
			} else {
				$recordData['v_fba_po_no'] = $fbaNo;
				$insertRecord = $this->crudModel->insertTableData( config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') , $recordData );
				
				if($insertRecord > 0){
					$result = true;
				}
			}
			
		}
		
		if($result != false){
			$this->ajaxResponse(1, $successMessage);
		}else {
				
			$this->ajaxResponse(101, $errorMessages);
		}
		
	}
	public function deleteFBARecord(Request $request){
		if(checkPermission(config('permission_constants.DELETE_FBA_SHEET_MASTER')) != true){
			$this->ajaxResponse(101, trans('messages.access-denied'));
		}
		if(!empty($request->input())){
			$fbaSheetRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$moduleName = (!empty($request->input('module_name')) ? ($request->input('module_name')) : '' );
			$fbaSheetRecordData = [];
			$successMessage =  trans('messages.success-delete',['module'=>trans('messages.record-fba-sheet')]);
			$errorMessages = trans('messages.error-delete',['module'=>trans('messages.record-fba-sheet')]);
			$result = false;
			
			if($fbaSheetRecordId > 0){
				$fbaSheetRecordData['t_is_active'] = 0;
				$fbaSheetRecordData['t_is_deleted'] = 1;
				$result = $this->crudModel->deleteTableData(  config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') ,  $fbaSheetRecordData , [ 'i_id' => $fbaSheetRecordId ] );
				
			}
			if($result != false){
			
				$this->ajaxResponse(1, $successMessage);
			}else {
			
				$this->ajaxResponse(101, $errorMessages);
			}
		}
	}
	public function checkUniqueFBAInvoiceNo(Request $request){
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$oldFbaInvoiceNo = (!empty($request->fba_po_invoice_no) ? ($request->fba_po_invoice_no) : '');
		
		$validator = Validator::make ( $request->all (), [
				'fba_no' => [ 'required' , new UniqueFBAInvoiceNo($recordId,$oldFbaInvoiceNo)] ,
		], [
				'fba_no.required' => __ ( 'messages.require-fba-invoice' ),
		] );
		
		$result = [];
		$result['status_code'] = 1;
		$result['message'] = trans('messages.success');
		if ($validator->fails ()) {
		
			$result['status_code'] = 101;
			$result['message'] = trans('messages.error');
		}
		echo json_encode($result);die;
	}
	
	public function getDestinationTypeDetails(Request $request){
		if(!empty($request->input())){
			$whereData = $customerWhere = [];
			$cutomerRecordDetails = $warehouseRecordDetails = '';
			$designation = (!empty($request->input('destination_type')) ? ($request->input('destination_type')) : '' );
			if(!empty($designation)){
				switch ($designation){
					case (config('constants.AMAZON_FBA_SHEET')):
						 $warehouseRecordDetails = WarehouseMasterModel::where('t_is_deleted' , 0 )->where('e_record_type' , config('constants.LOCATION'))->orderBy('v_warehouse_name' , 'asc')->get();
						 break;
					case (config('constants.WAREHOUSE_FBA_SHEET')):
						$warehouseRecordDetails = WarehouseMasterModel::where('t_is_deleted' , 0 )->where('e_record_type' , config('constants.WAREHOUSE'))->where('i_country_id' ,config('constants.USA') )->orderBy('v_warehouse_name' , 'asc')->get();
						break;
					case (config('constants.CUSTOMER_FBA_SHEET')):
						$customerMasterCodeDetails = CustomerDetailModel::with(['customerMaster'])->where('t_is_deleted' , 0 )->orderBy('v_customer_code' , 'asc')->get();
						break; 
				}
				
			}
			$html = "";
			$html .= '<option value="">'.trans ( 'messages.select'). '</option>';
			if( ($designation == config('constants.AMAZON_FBA_SHEET')) || ($designation == config('constants.WAREHOUSE_FBA_SHEET')) ){
				if(!empty($warehouseRecordDetails)){
					foreach ($warehouseRecordDetails as $warehouseRecordDetail){
						$html .= '<option value="'.$warehouseRecordDetail->v_warehouse_code.'">'.$warehouseRecordDetail->v_warehouse_name . ' - ' .$warehouseRecordDetail->v_warehouse_code. '</option>';
					}
				}
			}
			
			if( $designation == config('constants.CUSTOMER_FBA_SHEET')  ){
				if(!empty($customerMasterCodeDetails)){
					foreach ($customerMasterCodeDetails as $cutomerRecordDetail){
						$html .= '<option value="'.$cutomerRecordDetail->v_customer_code.'">'.$cutomerRecordDetail->customerMaster->v_customer_name . ' - ' . $cutomerRecordDetail->v_customer_code . '</option>';
					}
				}
			}
			echo $html;die();
		}
		
	}
	
	public function clubbingSummary(){
	
		if(checkPermission(config('permission_constants.VIEW_FBA_SHEET_MASTER')) != true){
			return redirect('access-denied');
		}
		$whereData = $data = $where = [];
		
		$data['pageTitle'] = trans('messages.clubbing-summary');
		$data['designationDetails'] = wayToWarehouseDetails();
		$data['fromPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['warehouseRecordDetails'] = WarehouseMasterModel::where('e_record_type' , config('constants.LOCATION'))->orderBy('v_warehouse_name' , 'asc')->get();
		
		return view($this->folderName . 'clubbing-summary')->with($data);
	
	}
	
	public function relatedWarehouseByWarehouseCountry(Request $request){
		if (!empty($request->post())){
			$countryId = (!empty($request->post('from_warehouse_country')) ? (int)Wild_tiger::decode($request->post('from_warehouse_country')) : 0);
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
			$filterRequest = (!empty($request->input('filter_request') && $request->input('filter_request') == 'true') ? true : false);
			
			$where = [];
			if ($countryId > 0){
				$where['i_country_id'] = $countryId;
			}
			$where['t_is_active'] = 1;
			$where['e_record_type'] = config('constants.WAREHOUSE');
			if (($recordId > 0) || ($filterRequest != false)){
				unset($where['t_is_active']);
			}
			
			$recordDetails = WarehouseMasterModel::where($where)->orderBy('v_warehouse_name', 'ASC')->get();
			
			$html = '<option value="">'.trans("messages.select").'</option>';
			if(!empty($recordDetails)){
				foreach ($recordDetails as $recordDetail){
					$html .= '<option value="'.Wild_tiger::encode($recordDetail->i_id).'">'.(!empty($recordDetail->v_warehouse_name) ? $recordDetail->v_warehouse_name : '').'</option>';
				}
			}
			echo $html;die;
		}
	}
	
	public function checkUniquePersonalReferenceNumber(Request $request){
		if (!empty($request->input())){
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
				
			$validator = Validator::make ( $request->all (), [
					'personal_ref' => [ 'required' , new UniquePersonalReferenceNumber($recordId) ]  ,
			], [
					'personal_ref.required' => trans('messages.require-personal-ref' ),
			] );
				
			if ($validator->fails()){
				$this->ajaxResponse(101, $validator->errors()->first());
			}
			$this->ajaxResponse(1, trans('messages.success'));
		}
	}
}