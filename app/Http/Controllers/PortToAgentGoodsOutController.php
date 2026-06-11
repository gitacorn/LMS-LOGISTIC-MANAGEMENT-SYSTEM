<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\DB;
use App\CountryToPortGoodsOutModel;
use App\PortToAgentWarehouseModel;
use App\Login;
use App\LogisticPartnerMasterModel;
use App\WarehouseMasterModel;
use App\LogisticPartnerDetailModel;
use App\CurrencyMasterModel;
use App\StatusMasterModel;
use App\Document_Type_Master_Model;
use Illuminate\Support\Facades\Response;
use App\CountryMasterModel;
use Illuminate\Support\Facades\Log;

class PortToAgentGoodsOutController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'port-to-agent-warehouse/';
		$this->moduleName = trans('messages.good-out-port-to-agent');
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_URL');
		$this->crudModel = new PortToAgentWarehouseModel();
		$this->countryToPortGoodsOut = new CountryToPortGoodsOutModel();
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data['pageTitle'] = trans('messages.us-port-to-agent-warehouse');
	
		$data['fromPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['logisticPartners'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->orderBy('i_logictic_partner_id', 'ASC')->get();
		$data['ownFromWarehouseDetails'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		$data['usaGoodOutWarehouseTypeDetails'] = getUsaGoodOutWarehouseType();
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT') , config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT')] );
		$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
		$data['countryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type', config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		
		$whereData = [];
		$whereData['t_is_active'] = 1;
		$data['getCountryToPortGoodsOutDetails'] = $this->countryToPortGoodsOut->getRecordDetails($whereData);
		
		$where = $statusIds = [];
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		$where['default_status'] = $statusIds;
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		$data['statusInfo'] = $statusIds;
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
	
		return view($this->folderName . 'us-port-to-agent-warehouse')->with($data);
	}
	
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
			//search record
		if (!empty($request->post('search_by_us_port_to_agent_warehouse'))) {
			$searchByName = trim($request->post('search_by_us_port_to_agent_warehouse'));
			$likeData['searchBy'] = $searchByName;
		}
		
		if(!empty($request->post('search_way_of_transport') )){
			$whereData['transport_way'] = ( $request->post('search_way_of_transport') );
		}
		if(!empty($request->post('search_from_port_airport') )){
			$whereData['from_port_airport'] = (int)Wild_tiger::decode($request->post('search_from_port_airport'));
		}
		if(!empty($request->post('search_logistic_partner') )){
			$whereData['logistic_partner'] = (int)Wild_tiger::decode($request->post('search_logistic_partner'));
		}
		if(!empty($request->post('search_warehouse_type'))){
			$whereData['warehouse_type'] = $request->post('search_warehouse_type');
		}
		if(!empty($request->post('search_from_warehouse_country') )){
			$whereData['from_warehouse_country'] = (int)Wild_tiger::decode($request->post('search_from_warehouse_country'));
		}
		if(!empty($request->post('search_warehouse') )){
			$whereData['warehouse'] = (int)Wild_tiger::decode($request->post('search_warehouse'));
		}
		if(!empty($request->post('search_to_own_location'))){
			$whereData['own_location'] = (int)Wild_tiger::decode($request->post('search_to_own_location'));
		}
		if(!empty($request->post('search_to_agent_location') )){
			$whereData['agent_location'] = (int)Wild_tiger::decode($request->post('search_to_agent_location'));
		}
		if(!empty($request->post('search_select_containers') )){
			$whereData['select_containers'] = (int)Wild_tiger::decode($request->post('search_select_containers') );
		}
		if(!empty($request->post('search_book_by') )){
			$whereData['book_by'] = (int)Wild_tiger::decode($request->post('search_book_by'));
		}
		if(!empty($request->post('container_discharged_from_date') )){
			$whereData['container_discharged_from_date'] = dbDate($request->post('container_discharged_from_date'));
		}
		if(!empty($request->post('container_discharged_to_date') )){
			$whereData['container_discharged_to_date'] = dbDate($request->post('container_discharged_to_date'));
		}
		if(!empty($request->post('booking_from_date') )){
			$whereData['booking_from_date'] = dbDate($request->post('booking_from_date'));
		}
		if(!empty($request->post('booking_to_date') )){
			$whereData['booking_to_date'] = dbDate($request->post('booking_to_date'));
		}
		if(!empty($request->post('collection_from_date') )){
			$whereData['collection_from_date'] = dbDate($request->post('collection_from_date'));
		}
		if(!empty($request->post('collection_to_date') )){
			$whereData['collection_to_date'] = dbDate($request->post('collection_to_date'));
		}
		if(!empty($request->post('delivery_from_date') )){
			$whereData['delivery_from_date'] = dbDate($request->post('delivery_from_date'));
		}
		if(!empty($request->post('delivery_to_date') )){
			$whereData['delivery_to_date'] = dbDate($request->post('delivery_to_date'));
		}
		/* if(!empty($request->post('search_status') )){
			$whereData['status'] = (int)Wild_tiger::decode($request->post('search_status'));
		} */
		if( ( !empty($request->post('search_status') ) ) ){
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
		
		if ($exportAction == 'export') {
			$finalExportData = [];
			$whereData['count_record'] = true;
			$getExportRecordDetails = $this->crudModel->getRecordDetails( $whereData, $likeData );
				
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					$contanierValue = ( isset($getExportRecordDetail->countryToPortMaster) ? json_decode(json_encode($getExportRecordDetail->countryToPortMaster),true) : [] );
					$contanierColumn = (!empty($contanierValue) ? array_column($contanierValue, 'v_country_to_port_record_no') : []);
					$contanierName = ( isset($contanierColumn) ? ( implode(', ', $contanierColumn)) : '');
					
					$allInvoiceDetails = ( isset($getExportRecordDetail->invoiceInfo) ? json_decode(json_encode($getExportRecordDetail->invoiceInfo),true) : [] );
					$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
					$paymentValue = $finalCharge;
					
					$transportColumn = (!empty($contanierValue) ? array_column($contanierValue, 'e_transport_way') : []);
					$transportName = ( isset($transportColumn) ? ( implode(', ', $transportColumn)) : '');
					
					$WarehouseTypeLocation = $getExportRecordDetail->e_warehose_type . ' - ';
					if (!empty($getExportRecordDetail->e_warehose_type) && $getExportRecordDetail->e_warehose_type == config('constants.OWN_WAREHOUSE_TYPE')){
						$WarehouseTypeLocation .= (isset($getExportRecordDetail->ownLocation) && !empty($getExportRecordDetail->ownLocation->v_warehouse_name) ? $getExportRecordDetail->ownLocation->v_warehouse_name : '');
					} else {
						$WarehouseTypeLocation .= (isset($getExportRecordDetail->agentLocation->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->agentLocation->logisticPartnerMaster->v_logistic_partner_name . ( isset($getExportRecordDetail->agentLocation->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->agentLocation->v_logistic_partner_code . ')' : '' )  :'');
					}
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['entry_no'] = (isset($getExportRecordDetail->v_port_to_agent_record_no) ? ($getExportRecordDetail->v_port_to_agent_record_no) : '');
					$rowExcelData['way_of_transport'] = (isset($getExportRecordDetail->e_transport_way) ? ($getExportRecordDetail->e_transport_way) : '' );
					$rowExcelData['from_port'] = (isset($getExportRecordDetail->fromPortInfo->v_warehouse_name) ? $getExportRecordDetail->fromPortInfo->v_warehouse_name :'');
					$rowExcelData['broker_(custom_agent)'] = (isset($getExportRecordDetail->v_brocker) ? $getExportRecordDetail->v_brocker :'');
					$rowExcelData['logistic_partner'] = (isset($getExportRecordDetail->logisticPartnerDetail->v_logistic_partner_name) ? $getExportRecordDetail->logisticPartnerDetail->v_logistic_partner_name :'');
					$rowExcelData['from_warehouse_country_-_warehouse'] = (isset($getExportRecordDetail->countryToPortMaster[0]->fromWarehouseCountry) && !empty($getExportRecordDetail->countryToPortMaster[0]->fromWarehouseCountry->v_country_name) ? $getExportRecordDetail->countryToPortMaster[0]->fromWarehouseCountry->v_country_name : '') . (isset($getExportRecordDetail->countryToPortMaster[0]->warehouseInfo) && !empty($getExportRecordDetail->countryToPortMaster[0]->warehouseInfo->v_warehouse_name) ? ' - ' . $getExportRecordDetail->countryToPortMaster[0]->warehouseInfo->v_warehouse_name : '');
					$rowExcelData['warehouse_type_-_location'] = $WarehouseTypeLocation;
					$rowExcelData['select_containers'] = (isset($contanierName) ? $contanierName .(isset($transportName) ? ' ('.$transportName .')' : ''): '');
					$rowExcelData['book_by'] =  (isset($getExportRecordDetail->bookEmployeeInfo->v_name) ?  $getExportRecordDetail->bookEmployeeInfo->v_name : '' );
					$rowExcelData['booking_date'] = (isset($getExportRecordDetail->dt_booking_date) ?  clientDate($getExportRecordDetail->dt_booking_date) : '' );
					$rowExcelData['ref._no.'] = (isset($getExportRecordDetail->v_ref_no) ? $getExportRecordDetail->v_ref_no : '' );
					$rowExcelData['tracking_no.'] = (isset($getExportRecordDetail->v_tracking_no) ?  ( $getExportRecordDetail->v_tracking_no )  : '' );
					$rowExcelData['tracking_link'] = (isset($getExportRecordDetail->v_tracking_link) ?  ( $getExportRecordDetail->v_tracking_link )  : '' );
					$rowExcelData['collection_date'] = (isset($getExportRecordDetail->dt_collection_date) ?  clientDate($getExportRecordDetail->dt_collection_date) : '' );
					$rowExcelData['delivery_date'] = (isset($getExportRecordDetail->dt_delivery_date) ? clientDate($getExportRecordDetail->dt_delivery_date) : '' );
					$rowExcelData['status'] = (isset($getExportRecordDetail->statusInfo->v_status) ? ($getExportRecordDetail->statusInfo->v_status) : '');
					$rowExcelData['total_logistic_cost_('.config('constants.GOODS_OUT_DEFAULT_CURRENCY').')'] = (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_DEFAULT_CURRENCY')  : '' );
					$finalExportData[] = $rowExcelData;
				}
			}
		
			if (!empty($finalExportData)) {
				$fileName = str_replace("/", "-", trans('messages.export-module-file-name', ['moduleName' => $this->moduleName]));
				
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => str_replace(" / ", "-", trans('messages.us-port-to-agent-warehouse')) ]);
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
	
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'port-to-agent-warehouse/good-out-port-agent-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	
	public function create(){
		if(checkPermission(config('permission_constants.ADD_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-good-out-port-to-agent');
	
		$data['fromPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['userRecordDetails'] = Login::where('t_is_active',1)->where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['logisticPartners'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();		
		//$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->where('t_is_active',1)->orderBy('i_logictic_partner_id', 'ASC')->get();
		$data['ownFromWarehouseDetails'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::where('t_is_active',1)->orderBy('i_sequence', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->where('t_is_active',1)->orderBy('v_document_type_name', 'ASC')->get();
		$data['logisticPartnerRecordDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
		        $whereData = [];
        $data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')
            ->orderBy('i_logictic_partner_id', 'ASC')->get();
		
		$whereData = [];
		$data['getCountryToPortGoodsOutDetails'] = $this->countryToPortGoodsOut->getRecordDetails($whereData);
		
		$where = [];
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		$data['usaGoodOutWarehouseTypeDetails'] = getUsaGoodOutWarehouseType();
		return view($this->folderName . 'add-us-port-to-agent-warehouse')->with($data);
	}
	
	public function edit($id){
		if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		if( $recordId > 0 ){
			$whereData = $data = [];
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			$data['pageTitle'] = trans('messages.update-good-out-port-to-agent');
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			


			if(count($recordInfo) > 0){
	
				$errorFound = false;
	
				$data['fromPortInfo'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.PORT'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				$data['logisticPartners'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();		
				$data['logisticPartnerDetails'] = LogisticPartnerDetailModel::with('logisticPartnerMaster')->orderBy('i_logictic_partner_id', 'ASC')->get();
				
				$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
				$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
				$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
				$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
				$data['logisticPartnerRecordDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				//$whereData = [];
				//$whereData['process_status'] = config('constants.PENDING_STATUS');
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				$data['getCountryToPortGoodsOutDetails'] = $this->countryToPortGoodsOut->getRecordDetails([ 'master_id' => explode("," ,$recordInfo[0]->v_container_ids)]);
				
				$data['usaGoodOutWarehouseTypeDetails'] = getUsaGoodOutWarehouseType();
				$data['ownFromWarehouseDetails'] = WarehouseMasterModel::whereIn('i_country_id', [config ('constants.USA')])->where('e_record_type', config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data['countryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
				$data['warehouseDetails'] = WarehouseMasterModel::where('i_country_id', $recordInfo[0]->i_from_warehouse_country_id)->where('e_record_type', config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
                
                // Ensure saved values appear in dropdowns even if not in filtered lists
                $currentRecord = (!empty($recordInfo) ? $recordInfo[0] : null);
                if ($currentRecord) {
                    // From - Port/Airport: include saved port if not in USA/PORT filtered list
                    if (!empty($currentRecord->i_transport_from_id)) {
                        $hasPort = $data['fromPortInfo']->contains('i_id', $currentRecord->i_transport_from_id);
                        if (!$hasPort) {
                            $missingPort = WarehouseMasterModel::where('i_id', $currentRecord->i_transport_from_id)->first();
                            if ($missingPort) {
                                $data['fromPortInfo'] = $data['fromPortInfo']->concat([$missingPort])->sortBy('v_warehouse_name')->values();
                            }
                        }
                    }
                    // Logistic Partner: include saved logistic partner detail if not present
                    if (!empty($currentRecord->i_logistic_partner_detail_id)) {
                        $hasPartner = $data['logisticPartnerDetails']->contains('i_id', $currentRecord->i_logistic_partner_detail_id);
                        if (!$hasPartner) {
                            $missingPartner = LogisticPartnerDetailModel::with('logisticPartnerMaster')->where('i_id', $currentRecord->i_logistic_partner_detail_id)->first();
                            if ($missingPartner) {
                                $data['logisticPartnerDetails'] = $data['logisticPartnerDetails']->concat([$missingPartner])->sortBy('i_logictic_partner_id')->values();
                            }
                        }
                    }
                    // Book By: include saved employee if not present in logistic users list
                    if (!empty($currentRecord->i_book_by_employee_id)) {
                        $hasUser = $data['userRecordDetails']->contains('i_id', $currentRecord->i_book_by_employee_id);
                        if (!$hasUser) {
                            $missingUser = Login::where('i_id', $currentRecord->i_book_by_employee_id)->first();
                            if ($missingUser) {
                                $data['userRecordDetails'] = $data['userRecordDetails']->concat([$missingUser])->sortBy('v_name')->values();
                            }
                        }
                    }
                }
                
                $disableForm = '';
                $documentForm = '';
                $statusDisableForm = '';
				if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
					$data ['pageTitle'] = trans('messages.view-us-port-to-agent-warehouse');
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
				return view ( $this->folderName . 'add-us-port-to-agent-warehouse' )->with ( $data );
			}
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	
	public function add(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['way_of_transport'] = 'required';
		$formValidation['from_port_airport'] = 'required';
		$formValidation['logistic_partner'] = 'required';
		        $formValidation['to_agent_location'] = 'required_if:warehouse_type,'. config("constants.AGENT_WAREHOUSE_TYPE");
		$formValidation['select_containers'] = 'required';
		$formValidation['book_by'] = 'required';
		$formValidation['container_discharged_date'] = 'required';
		$formValidation['ref_no'] = 'required';
		$formValidation['tracking_no'] = 'required';
		$formValidation['booking_date'] = 'required';
		$formValidation['warehouse_type'] = 'required';
		        $formValidation['to_own_location'] = 'required_if:warehouse_type,'. config("constants.OWN_WAREHOUSE_TYPE");
		
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			$formValidation['collection_date'] = 'required';
			$formValidation['delivery_date'] = 'required';
		}
		$validator = Validator::make ( $request->all (), $formValidation , [
				'way_of_transport.required' => __ ( 'messages.require-way-of-transport' ),
				'from_port_airport.required' => __ ( 'messages.require-from-port-airport' ),
				'logistic_partner.required' => __ ( 'messages.require-logistic-partner' ),
				'to_agent_location.required_if' => __ ( 'messages.require-to-agent-location' ),
				'select_containers.required' => __ ( 'messages.require-select-containers' ),
				'book_by.required' => __ ( 'messages.require-book-by' ),
				'container_discharged_date.required' => __ ( 'messages.require-container-discharged-date' ),
				'ref_no.required' => __ ( 'messages.require-ref-no' ),
				'tracking_no.required' => __ ( 'messages.require-tracking-no' ),
				'booking_date.required' => __ ( 'messages.require-booking-date' ),
				'collection_date.required' => __ ( 'messages.require-collection-date' ),
				'delivery_date.required' => __ ( 'messages.require-delivery-date' ),
				'warehouse_type.required' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.warehouse-type') ] ),
				'to_own_location.required_if' => trans('messages.required-select-field-validation', ['fieldName' => trans('messages.to-own-location') ] ),
		] );
	
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=>trans('messages.good-out-port-to-agent')]);
		$errorMessages = trans('messages.error-create',['module'=>trans('messages.good-out-port-to-agent')]);
	
		
		DB::beginTransaction();
		try{
	
			$recordData = [];
			$goodOutPortAgentDocumentTypeCount = (!empty($request->input('good_out_port_agent_document_type_count')) ? (int)($request->input('good_out_port_agent_document_type_count')) : 1 );
			$goodOutPortAgentTransporterCount = (!empty($request->input('good_out_port_agent_transporter_count')) ? (int)($request->input('good_out_port_agent_transporter_count')) : 1 );
				
			$recordData['e_transport_way'] = (!empty($request->input('way_of_transport')) ? ($request->input('way_of_transport')) : '' );
			$recordData['i_transport_from_id'] = (!empty($request->input('from_port_airport')) ? (int)Wild_tiger::decode($request->input('from_port_airport')) : 0 );
			$recordData['v_brocker'] = (!empty($request->input('broker_custom_agent')) ? ($request->input('broker_custom_agent')) : null );
			$recordData['i_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner')) ? (int)Wild_tiger::decode($request->input('logistic_partner')) : 0 );
			
			$recordData['e_warehose_type'] = ( isset($request->warehouse_type) && !empty($request->warehouse_type) && $request->warehouse_type == config("constants.OWN_WAREHOUSE_TYPE") ? config("constants.OWN_WAREHOUSE_TYPE") : config("constants.AGENT_WAREHOUSE_TYPE") );
			$recordData['i_own_warehouse_location_id'] = null;
			$recordData['i_agent_location_id'] = null;
			
			if(isset($request->warehouse_type) && !empty($request->warehouse_type) && $request->warehouse_type == config("constants.OWN_WAREHOUSE_TYPE")){
				$recordData['i_own_warehouse_location_id'] = (!empty($request->input('to_own_location')) ? (int)Wild_tiger::decode($request->input('to_own_location')) : null );				
			} else {
				$recordData['i_agent_location_id'] = (!empty($request->input('to_agent_location')) ? (int)Wild_tiger::decode($request->input('to_agent_location')) : null );
			}
			
			$recordData['v_container_ids'] = (!empty($request->input('select_containers')) ? (int)Wild_tiger::decode($request->input('select_containers')) : 0);
			$recordData['i_book_by_employee_id'] = (!empty($request->input('book_by')) ? (int)Wild_tiger::decode($request->input('book_by')) : 0 );			
			$recordData['dt_contanier_discharge_date'] = (!empty($request->input('container_discharged_date')) ? dbDate($request->input('container_discharged_date')) : null );
			$recordData['v_container_theft_missing'] = (!empty($request->input('container_theft_missing')) ? ($request->input('container_theft_missing')) : null );
			$recordData['dt_booking_date'] = (!empty($request->input('booking_date')) ? dbDate($request->input('booking_date')) : null );
			$recordData['v_ref_no'] = (!empty($request->input('ref_no')) ? ($request->input('ref_no')) : null );
			$recordData['i_total_pallets'] = (!empty($request->input('no_of_pallets')) ? ($request->input('no_of_pallets')) : 0 );
			$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? ($request->input('tracking_no')) : null );
			$recordData['v_tracking_link'] = (!empty($request->input('tracking_link')) ? ($request->input('tracking_link')) : null );
			$recordData['dt_collection_date'] = (!empty($request->input('collection_date')) ? dbDate($request->input('collection_date')) : null );
			$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
			$recordData['i_from_warehouse_country_id'] = (!empty($request->input('from_warehouse_country')) ? (int)Wild_tiger::decode($request->input('from_warehouse_country')) : null);
			$recordData['i_warehouse_id'] = (!empty($request->input('warehouse')) ? (int)Wild_tiger::decode($request->input('warehouse')) : null);
			$recordData['v_personal_ref'] = (!empty($request->post('personal_ref')) ? trim($request->post('personal_ref')) : null);
			$recordData['i_status_id'] = $statusRecordId;
			$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? ($request->input('status_comments')) :null );
	
			$selectContainersIdRecord = (!empty($recordData['v_container_ids']) ? [$recordData['v_container_ids']] : []);
			
			if($recordId > 0){
	
				$successMessage =  trans('messages.success-update',['module'=>trans('messages.good-out-port-to-agent')]);
				$errorMessages = trans('messages.error-update',['module'=>trans('messages.good-out-port-to-agent')]);
	
				$whereData = [];
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
					
				$goodOutPortAgentRecordDetail = $this->crudModel->getRecordDetails($whereData);
	
				$goodOutPortAgentDetails = (!empty($goodOutPortAgentRecordDetail) ? $goodOutPortAgentRecordDetail[0] : []);
	
				$countryPortProcessStatusData = $oldContainerIds = $goodOutCountryPortData = [];
				
				if(!empty($goodOutPortAgentDetails->v_container_ids)){
					$oldContainerIds = explode(',', $goodOutPortAgentDetails->v_container_ids );
					
					//new added container record ids
					$containersIdRecord = array_diff( $selectContainersIdRecord, $oldContainerIds );
					
					//remove container record ids
					$remvoeContainerRecordDetails = array_diff( $oldContainerIds, $selectContainersIdRecord );
					
					if(!empty($containersIdRecord)){
						foreach ($containersIdRecord as $containersId){
							
							//check new container is already used into some else port to agent record or not
							$getMasterContainerRecordInfo = $this->crudModel->getSingleRecordById(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'),['i_id' , 'e_process_status'] , [ 't_is_deleted != ' => 1 , 'i_id' =>  $containersId ] );
							
							if(empty($getMasterContainerRecordInfo) || ( isset($getMasterContainerRecordInfo->e_process_status) && ( $getMasterContainerRecordInfo->e_process_status == config('constants.COMPLETED_STATUS') ) )){
								DB::rollback();
								Wild_tiger::setFlashMessage ( 'danger', trans('messages.error-selected-container-into-completed-status') );
								return redirect ( $this->redirectUrl );
							}
							
							$countryPortProcessStatusData['e_process_status'] = config('constants.COMPLETED_STATUS');
							$this->crudModel->updateTableData(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') , $countryPortProcessStatusData , [ 'i_id' => $containersId ] );
							
							$portToAgentContainerInfo  = [];
							$portToAgentContainerInfo['i_port_to_agent_goods_out_master_id'] = $recordId;
							$portToAgentContainerInfo['i_container_id'] = $containersId;
							
							$this->crudModel->insertTableData(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE') , $portToAgentContainerInfo );
						}
					}
					
					if(!empty($remvoeContainerRecordDetails)){
						foreach($remvoeContainerRecordDetails as $remvoeContainerRecordDetail){
							$getRecordPortToAgentStatusInfo = $this->crudModel->getSingleRecordById(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE'),['i_id' , 'e_container_status' , 'i_port_to_agent_goods_out_master_id'] , [ 't_is_deleted != ' => 1 , 'i_container_id' =>  $remvoeContainerRecordDetail ] );
							if( (empty($getRecordPortToAgentStatusInfo)) || ( ( isset($getRecordPortToAgentStatusInfo->e_container_status) && ( $getRecordPortToAgentStatusInfo->e_container_status != config('constants.PENDING_STATUS') ) ) ) ){
								DB::rollback();
								Wild_tiger::setFlashMessage ( 'danger', trans('messages.error-selected-container-used-into-agento-warehouse') );
								return redirect ( $this->redirectUrl );
							} else {
								$deleteRecordData = [];
								$deleteRecordData ['t_is_active'] = 0;
								$deleteRecordData ['t_is_deleted'] = 1;
								$this->crudModel->deleteTableData( config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE') , $deleteRecordData , [ 'i_container_id' => $remvoeContainerRecordDetail ] );
								$this->crudModel->updateTableData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') , ['e_process_status' => config('constants.PENDING_STATUS')] , [ 'i_id' => $remvoeContainerRecordDetail ] );
							}
						}
					}
				}
				
				if(!empty($goodOutPortAgentDetails->invoiceInfo)){
					foreach ($goodOutPortAgentDetails->invoiceInfo as $goodOutPortAgentInvoiceDetail){
						if(!empty($request->input('edit_name_'.$goodOutPortAgentInvoiceDetail->i_id))){
							$goodInInvoice = [];
							$goodInInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$goodOutPortAgentInvoiceDetail->i_id)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$goodOutPortAgentInvoiceDetail->i_id)) : 0 );
							$goodInInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_inv_no_'.$goodOutPortAgentInvoiceDetail->i_id) :'' );
							$goodInInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_freight_'.$goodOutPortAgentInvoiceDetail->i_id) : 0 );
							$goodInInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_custom_'.$goodOutPortAgentInvoiceDetail->i_id) :0 );
							$goodInInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_duty_'.$goodOutPortAgentInvoiceDetail->i_id)  : 0 );
							$goodInInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_other_'.$goodOutPortAgentInvoiceDetail->i_id) :0 );
							$goodInInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_vat_'.$goodOutPortAgentInvoiceDetail->i_id) : 0 );
							$totalCharges = $goodInInvoice['d_freight_charge'] + $goodInInvoice['d_custom_charge'] + $goodInInvoice['d_duty_charge'] + $goodInInvoice['d_other_charge'] + $goodInInvoice['d_vat_charge'];
							$goodInInvoice['d_total_charge'] = $totalCharges;
							$goodInInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_amount_'.$goodOutPortAgentInvoiceDetail->i_id)) ? (int)Wild_tiger::decode($request->input('edit_amount_'.$goodOutPortAgentInvoiceDetail->i_id)) : 0);
							$goodInInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$goodOutPortAgentInvoiceDetail->i_id)) ? $request->input('edit_cov_rate_'.$goodOutPortAgentInvoiceDetail->i_id) : 0);
							$finalCharges = ($totalCharges * $goodInInvoice['d_conversion_rate']);
							$goodInInvoice['d_final_charge'] = $finalCharges;
							if($request->hasFile('edit_invoice_file_'.$goodOutPortAgentInvoiceDetail->i_id)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$goodOutPortAgentInvoiceDetail->i_id,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$goodInInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							}else {
								$removeFiles = (!empty($request->input('remove_invoice_'.$goodOutPortAgentInvoiceDetail->i_id)) ? explode("," , $request->input('remove_invoice_'.$goodOutPortAgentInvoiceDetail->i_id) ) : []  );
								$previousUploadFiles = (!empty($goodOutPortAgentInvoiceDetail->v_invoice_file_path) ? json_decode($goodOutPortAgentInvoiceDetail->v_invoice_file_path,true) : [] );
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
							if(( $goodInInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($goodInInvoice['v_invoice_no']) ) ){
								$goodInInvoiceUpdate = $this->crudModel->updateTableData( config('constants.PORT_TO_AGENT_GOODS_OUT_INVOICE_MASTER_TABLE') , $goodInInvoice , [ 'i_id' => $goodOutPortAgentInvoiceDetail->i_id] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.PORT_TO_AGENT_GOODS_OUT_INVOICE_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $goodOutPortAgentInvoiceDetail->i_id] );
						}
					}
				}
	
				if(!empty($goodOutPortAgentDetails->documentInfo)){
					foreach ($goodOutPortAgentDetails->documentInfo as $goodOutPortAgentDocumentDetail){
						if(!empty($request->input('edit_type_'.$goodOutPortAgentDocumentDetail->i_id))){
							$goodInDocument = [];
							$goodInDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$goodOutPortAgentDocumentDetail->i_id)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$goodOutPortAgentDocumentDetail->i_id)) :0);
							$goodInDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$goodOutPortAgentDocumentDetail->i_id)) ? $request->input('edit_remarks_'.$goodOutPortAgentDocumentDetail->i_id) : null);
							if($request->hasFile('edit_file_'.$goodOutPortAgentDocumentDetail->i_id)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$goodOutPortAgentDocumentDetail->i_id,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$goodInDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							}else {
								$removeFiles = (!empty($request->input('remove_document_'.$goodOutPortAgentDocumentDetail->i_id)) ? explode("," , $request->input('remove_document_'.$goodOutPortAgentDocumentDetail->i_id) ) : []  );
								$previousUploadFiles = (!empty($goodOutPortAgentDocumentDetail->v_document_file_path) ? json_decode($goodOutPortAgentDocumentDetail->v_document_file_path,true) : [] );
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
							$goodOutPortAgentDocumentDetailUpdate = $this->crudModel->updateTableData( config('constants.PORT_TO_AGENT_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $goodInDocument , [ 'i_id' => $goodOutPortAgentDocumentDetail->i_id] );
							
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.PORT_TO_AGENT_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $goodOutPortAgentDocumentDetail->i_id] );
						}
					}
				}
				$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
	
			} else {
				$goodOPortAgentGenerateNo = config('constants.GOOD_OUT_PORT_TO_AGENT_PTA').'-'. config('constants.GOOD_IN_BUYER_NUMBER').'-'.$this->todayDate;
					
				$goodOutPortAgentMasterRecordDetails = $this->crudModel->selectData(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE') ,['i_id']);
					
				if(!empty($goodOutPortAgentMasterRecordDetails)){
					$goodOutPortAgentMasterRecordCount = count($goodOutPortAgentMasterRecordDetails);
					$count = ( ( (!empty($goodOutPortAgentMasterRecordCount)) && ( $goodOutPortAgentMasterRecordCount > 0 ) ) ? ( $goodOutPortAgentMasterRecordCount + 1  ) : 1 );
					$generateNumber = threeNumberSeries($count);
					$goodOPortAgentGenerateNo = config('constants.GOOD_OUT_PORT_TO_AGENT_PTA').'-'. $generateNumber.'-'.$this->todayDate;
	
				}
				
				
				$recordData['e_process_status'] = config('constants.PENDING_STATUS');
				$recordData['v_port_to_agent_record_no'] = $goodOPortAgentGenerateNo;
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				
				$goodOutCountryPortData = [];
				if(!empty($selectContainersIdRecord)){
					foreach ($selectContainersIdRecord as $selectContainersRecordId){
						$getMasterRecordInfo = $this->crudModel->getSingleRecordById(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'),['i_id' , 'e_process_status'] , [ 't_is_deleted != ' => 1 , 'i_id' =>  $selectContainersRecordId ] );
						if(empty($getMasterRecordInfo) || ( isset($getMasterRecordInfo->e_process_status) && ( $getMasterRecordInfo->e_process_status == config('constants.COMPLETED_STATUS') ) )){
							DB::rollback();
							Wild_tiger::setFlashMessage ( 'danger', trans('messages.error-selected-container-into-completed-status') );
							return redirect ( $this->redirectUrl );
						}
				
						$goodOutCountryPortData['e_process_status'] = config('constants.COMPLETED_STATUS');
						$this->crudModel->updateTableData(config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') , $goodOutCountryPortData , [ 'i_id' => $selectContainersRecordId ] );
				
						$portToAgentContainerInfo  = [];
						$portToAgentContainerInfo['i_port_to_agent_goods_out_master_id'] = $insertRecord;
						$portToAgentContainerInfo['i_container_id'] = $selectContainersRecordId;
							
						$this->crudModel->insertTableData(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE') , $portToAgentContainerInfo );
				
					}
				}
				
				
			}
	
			if( $insertRecord > 0 ){
				$result = true;
			}
			for ($i = 0; $i <= $goodOutPortAgentTransporterCount;$i++){
				$rowData = [];
				$rowData['i_port_to_agent_goods_out_master_id'] = $insertRecord;
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
	
				if(( $rowData ['i_logistic_partner_master_id'] > 0 ) && (!empty($rowData ['v_invoice_no']))){
					$insertGoodOutPortAgentInvoice = $this->crudModel->insertTableData( config('constants.PORT_TO_AGENT_GOODS_OUT_INVOICE_MASTER_TABLE') , $rowData);
				}
			}
	
	
			for ($i = 0; $i <= $goodOutPortAgentDocumentTypeCount;$i++){
				$rowData = [];
				$rowData['i_port_to_agent_goods_out_master_id'] = $insertRecord;
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
					$insertGoodOutPortDocumentDetail = $this->crudModel->insertTableData( config('constants.PORT_TO_AGENT_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $rowData);
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
		if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$goodOutPortAgentDetailData['t_is_active'] = 0;
			$goodOutPortAgentDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=>trans('messages.good-out-port-to-agent')]);
			$errorMessages = trans('messages.error-delete',['module'=>trans('messages.good-out-port-to-agent')]);
	
			$getMasterRecordInfo = $this->crudModel->getSingleRecordById(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'),['i_id' , 'e_process_status'] , [ 't_is_deleted != ' => 1 , 'i_id' => $recordId ] );
				
			if(!empty($getMasterRecordInfo) && ($getMasterRecordInfo->e_process_status != config('constants.PENDING_STATUS')) ){
				$this->ajaxResponse(101, $this->lang->line('error-record-used-into-port-to-agent'));
			}
			
			DB::beginTransaction();
	
			$result = false;
			try{
				
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
				$recordInfo = $this->crudModel->getRecordDetails($whereData);
				
				$oldContainerIds = isset($recordInfo[0]->v_container_ids) ? explode("," , $recordInfo[0]->v_container_ids ) : [] ;
				
				if(!empty($oldContainerIds)){
					foreach ($oldContainerIds as $oldContainerId){
						$countryToPortData = [];
						$countryToPortData['e_process_status'] = config('constants.PENDING_STATUS');
						$this->crudModel->updateTableData(  config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') ,  $countryToPortData , [ 'i_id' => $oldContainerId ] );
						
					}
				}
				$this->crudModel->deleteTableData(  config('constants.PORT_TO_AGENT_GOODS_OUT_DOCUMENT_MASTER_TABLE') ,  $goodOutPortAgentDetailData , [ 'i_port_to_agent_goods_out_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.PORT_TO_AGENT_GOODS_OUT_INVOICE_MASTER_TABLE') ,  $goodOutPortAgentDetailData , [ 'i_port_to_agent_goods_out_master_id' => $recordId ] );
				$this->crudModel->deleteTableData($this->tableName,  $goodOutPortAgentDetailData , [ 'i_id' => $recordId ] );
	
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
	public function getContainerRecordDetails(Request $request){
		
		$logisticPartnerId = (!empty($request->input('logistic_parner_detail_id')) ? (int)Wild_tiger::decode($request->input('logistic_parner_detail_id')) : 0 );
		$formPortId = (!empty($request->input('from_port_airport')) ? (int)Wild_tiger::decode($request->input('from_port_airport')) : 0 );
		
		// Require at least from port to be selected
		if($formPortId > 0){
			
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			
			$containerWhere = $containerAdditionalWhere = [];
			$containerWhere['cpgm.t_is_deleted != '] = 1;
			$containerWhere['cpgm.i_lastet_import_file_id >'] = 0;
			$containerWhere['import.e_status'] = config('constants.SUCCESS_STATUS');
			$previousSelectedContainerIds = [];
			
			if( $recordId > 0 ){
				$getMasterRecordInfo = $this->crudModel->getSingleRecordById(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'),['i_id' , 'v_container_ids'] , [ 't_is_deleted != ' => 1 , 'i_id' =>  $recordId ] );
				
				if( isset($getMasterRecordInfo) && ( isset($getMasterRecordInfo->v_container_ids) && (!empty($getMasterRecordInfo->v_container_ids)) ) ){
					$previousSelectedContainerIds = explode("," ,  $getMasterRecordInfo->v_container_ids );
					$containerAdditionalWhere['whereIn'] = [ 'cpgm.i_id' , $previousSelectedContainerIds ] ;
				}
			} else {
				$containerWhere['cpgm.e_process_status'] = config('constants.PENDING_STATUS');
			}
			
			// Filter by from port (required)
			$containerWhere['i_transport_to_id'] = $formPortId;
			
			// Filter by logistic partner only if provided (optional)
			if($logisticPartnerId > 0){
				$containerWhere['i_logistic_partner_detail_id'] = $logisticPartnerId;
			}
			
			$containerWhere['custom_function'] = "( i_status_id = '".config('constants.DELIVERED_STATUS_ID')."' or  i_status_id = '".config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID')."')";
			
			$getCountryToPortGoodsOutRecordDetails = $this->crudModel->selectJoinData( config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') . ' as cpgm' , [ 'cpgm.v_country_to_port_record_no' , 'cpgm.i_id' ,'cpgm.e_transport_way', 'cpgm.v_personal_ref'] , [  [ 'tableName' =>  config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE') . ' as import' , 'condition' => 'import.i_id = cpgm.i_lastet_import_file_id'  ] ] ,   $containerWhere , [] , $containerAdditionalWhere );
			$html = '<option value="">'.trans("messages.select").'</option>';
			if(!empty($getCountryToPortGoodsOutRecordDetails)){
				foreach ($getCountryToPortGoodsOutRecordDetails as $getCountryToPortGoodsOutRecordDetail){
					$encodeRecordId  = Wild_tiger::encode($getCountryToPortGoodsOutRecordDetail->i_id);
					$selected = '';
					if( $recordId > 0 ){
						if(in_array( $getCountryToPortGoodsOutRecordDetail->i_id , $previousSelectedContainerIds)){
							//$selected = 'selected';
						}
					}
					$html .= '<option value="'.$encodeRecordId.'" '.$selected.' >'
						.(!empty($getCountryToPortGoodsOutRecordDetail->v_country_to_port_record_no)
							? $getCountryToPortGoodsOutRecordDetail->v_country_to_port_record_no
								.( !empty($getCountryToPortGoodsOutRecordDetail->e_transport_way) ? ' (' .$getCountryToPortGoodsOutRecordDetail->e_transport_way . ')' : '' )
							: ''
						)
						.( !empty($getCountryToPortGoodsOutRecordDetail->v_personal_ref) ? ' — ' . $getCountryToPortGoodsOutRecordDetail->v_personal_ref : '' )
						.'</option>';
				}
			}
			echo $html;die;
		} else {
			// Return empty select if no port is selected
			echo '<option value="">'.trans("messages.select").'</option>';die;
		}
	}
	public function containerWiseFromWarehouseCountryAndWarehouse(Request $request){
		if (!empty($request->post())){
			$containerId = (!empty($request->input('container_id')) ? (int)Wild_tiger::decode($request->input('container_id')) : 0 );
			
			$where = [];
			$where['master_id'] = $containerId;
			$recordDetails = $this->countryToPortGoodsOut->getRecordDetails($where);
			$recordInfo = (isset($recordDetails[0]) ? $recordDetails[0] : []);
			
			$personalRefValue = (!empty($recordInfo->v_personal_ref) ? $recordInfo->v_personal_ref : '');
			$fromWarehouseCountryOption = $warehouseOption = '<option value="">'.trans("messages.select").'</option>';
			if (!empty($recordInfo)){
				if (isset($recordInfo->fromWarehouseCountry)){
					$fromWarehouseCountryOption .= '<option value="'.Wild_tiger::encode($recordInfo->fromWarehouseCountry->i_id).'" selected >'.(!empty($recordInfo->fromWarehouseCountry->v_country_name) ? $recordInfo->fromWarehouseCountry->v_country_name : '').'</option>';
				}
				if (isset($recordInfo->warehouseInfo)){
					$warehouseOption .= '<option value="'.Wild_tiger::encode($recordInfo->warehouseInfo->i_id).'" selected >'.(!empty($recordInfo->warehouseInfo->v_warehouse_name) ? $recordInfo->warehouseInfo->v_warehouse_name : '').'</option>';
				}
			}
			
			$this->ajaxResponse(1, 'success', ['from_warehouse_country_option' => $fromWarehouseCountryOption, 'warehouse_option' => $warehouseOption, 'personal_ref_value' => $personalRefValue]);
		}
	}
}
