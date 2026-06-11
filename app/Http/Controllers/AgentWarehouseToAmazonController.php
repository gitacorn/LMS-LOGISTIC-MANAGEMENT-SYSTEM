<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\AgentToWarehouseModel;
use App\Document_Type_Master_Model;
use App\CurrencyMasterModel;
use App\LogisticPartnerMasterModel;
use App\Login;
use App\StatusMasterModel;
use App\LogisticPartnerDetailModel;
use App\Helpers\Twt\Wild_tiger;
use App\FBASheetMasterModel;
use App\FBASheeteDetailModel;
use Illuminate\Support\Facades\Validator;
use App\PortToAgentWarehouseModel;
use DB;
use Illuminate\Support\Facades\Response;
use App\CountryToPortGoodsOutModel;
use App\WarehouseMasterModel;
class AgentWarehouseToAmazonController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'agent-warehouse-to-amazon/';
		$this->moduleName = trans('messages.agent-warehouse-to-amazon-warehouse-customer');
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL');
		$this->crudModel = new AgentToWarehouseModel();	
		$this->countryToPortGoodsOut = new CountryToPortGoodsOutModel();
		
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != true){
			return redirect('access-denied');
		}
		$data = $where = [];
		$data ['pageTitle'] = trans('messages.agent-warehouse-to-amazon-warehouse-customer');
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['wayToWarehouseDetails'] = wayToWarehouseDetails();
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
		$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->get();
		$whereData['pt.t_is_deleted !='] = 1;
		$whereData['cp.t_is_deleted !='] = 1;
		
		$joindata[]= ['tableName' =>config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'). ' as cp','condition'=>'pt.v_container_ids = cp.i_id'];
		$data['getCountryToPortGoodsOutRecordDetails'] = $this->crudModel->selectJoinData(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'). ' as pt'  , ['pt.i_id','cp.i_id as country_port_master_id','pt.v_port_to_agent_record_no','pt.v_container_ids','pt.i_logistic_partner_detail_id','cp.v_country_to_port_record_no','cp.e_transport_way'] , $joindata , $whereData);
		$data['toWarehouseDetails'] = WarehouseMasterModel::where('i_country_id', config ('constants.USA'))->where('e_record_type', config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		
		$statusIds = [];
		$statusIds = [config('constants.DELIVERED_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID')];
		$where['default_status'] = $statusIds;
		$data['statusInfo'] = $statusIds;
		//$where['master_id'] = 10;
		$data['recordDetails'] = $this->crudModel->getRecordDetails($where);
		//echo "<pre>";print_r($data['recordDetails']);die;
		$data['page_no'] = 1;
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = ( !empty($data['recordDetails']->total()) ? $data['recordDetails']->total() : 0 );
		
		return view($this->folderName . 'agent-warehouse-to-amazon')->with($data);
	
	}
	public function create() {
		if(checkPermission(config('permission_constants.ADD_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != true ){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.add-agent-warehouse-to-amazon');
		//$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->where('t_is_active',1)->orderBy('v_document_type_name', 'ASC')->get();
		$data['currencyRecordDetails'] = CurrencyMasterModel::where('t_is_active',1)->orderBy('v_currency_name', 'ASC')->get();
		$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
		$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->where('t_is_active',1)->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
		$data['statusMasterRecordDetails'] = StatusMasterModel::where('t_is_active',1)->orderBy('i_sequence', 'ASC')->get();
		$whereData = [];
		$whereData['t_is_active'] = 1;
		$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->whereHas('logisticPartnerMaster', function($query)use ($whereData){
			$query->where('t_is_active',$whereData);
		})->get();
		$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::where('t_is_active',1)->orderBy('v_logistic_partner_name', 'ASC')->get();
		$data['toWarehouseDetails'] = WarehouseMasterModel::where('i_country_id', config ('constants.USA'))->where('e_record_type', config ('constants.WAREHOUSE'))->where('t_is_active',1)->orderBy('v_warehouse_name', 'ASC')->get();
		$data['disableForm'] = '';
		$data['documentForm'] = '';
		$data['statusDisableForm'] = '';
		return view ( $this->folderName . 'add-agent-warehouse-to-amazon' )->with ( $data );
	
	
	}
	public function getPortToagentContainerDetails(Request $request){
		$whereData = $joinData = [];
		$logisticPartnerDetailId = (!empty($request->input('logistic_parner_detail_id')) ? (int)Wild_tiger::decode($request->input('logistic_parner_detail_id')) : 0 );
		$filterRecordId = (!empty($request->input('data_record_filter_id')) ? $request->input('data_record_filter_id') : null );
		if($logisticPartnerDetailId > 0){
			$whereData['pt.i_agent_location_id'] = $logisticPartnerDetailId;
			$whereData['pt.t_is_deleted !='] = 1;
			$whereData['cp.t_is_deleted !='] = 1;
			if(empty($filterRecordId)){
				$whereData['cp.t_is_active'] = 1;
				$whereData['pt.t_is_active'] = 1;
			}
			$joindata[]= ['tableName' =>config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'). ' as cp','condition'=>'pt.v_container_ids = cp.i_id'];
			
			$getCountryToPortGoodsOutRecordDetails = $this->crudModel->selectJoinData(config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_TABLE'). ' as pt'  , ['pt.i_id','cp.i_id as country_port_master_id','pt.v_port_to_agent_record_no','pt.v_container_ids','pt.i_logistic_partner_detail_id','cp.v_country_to_port_record_no','cp.e_transport_way'] , $joindata , $whereData);
			
			$portToAgentWarehouseModel = new PortToAgentWarehouseModel();
			
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			
			$getContainerWhere = [];
			$getContainerWhere['agent_location'] = $logisticPartnerDetailId;
			$containerAdditionalWhere = [];
			if( $recordId > 0 ){
				/* $getMasterRecordInfo = $this->crudModel->getSingleRecordById(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE'),['i_id' , 'v_container_ids'] , [ 't_is_deleted != ' => 1 , 'i_id' =>  $recordId ] );
				if( isset($getMasterRecordInfo) && ( isset($getMasterRecordInfo->v_container_ids) && (!empty($getMasterRecordInfo->v_container_ids)) ) ){
					$previousSelectedContainerIds = explode("," ,  $getMasterRecordInfo->v_container_ids );
					$containerAdditionalWhere['whereIn'] = [ 'i_id' , $previousSelectedContainerIds ] ;
				} */
			} else {
				$getContainerWhere['process_status'] = [ config('constants.PARTIAL_DELIVERY_TYPE') , config('constants.PENDING_STATUS') ];
			}
			$getContainerWhere['process_status'] = [ config('constants.PARTIAL_DELIVERY_TYPE') , config('constants.PENDING_STATUS') ];
			//$getContainerWhere['status'] = [ config('constants.DELIVERED_STATUS_ID') , config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') ];
			//\DB::enableQueryLog();
			$getCountryToPortGoodsOutRecordDetails = $portToAgentWarehouseModel->getRecordDetails($getContainerWhere);
			//dd(\DB::getQueryLog());
			
			$html = '';
			//echo "<pre>";print_r($getCountryToPortGoodsOutRecordDetails);die;
			if(!empty($getCountryToPortGoodsOutRecordDetails)){
				foreach ($getCountryToPortGoodsOutRecordDetails as $getCountryToPortGoodsOutRecordDetail){
					if( isset($getCountryToPortGoodsOutRecordDetail->countryToPortMaster) && (!empty($getCountryToPortGoodsOutRecordDetail->countryToPortMaster)) ){
						foreach($getCountryToPortGoodsOutRecordDetail->countryToPortMaster as $countryToPortMaster){
							$encodeRecordId  = Wild_tiger::encode($countryToPortMaster->i_id);
							$html .= '<option value="'.$encodeRecordId.'">'.(!empty($countryToPortMaster->v_country_to_port_record_no) ? $countryToPortMaster->v_country_to_port_record_no .(!empty($countryToPortMaster->e_transport_way) ? ' (' .$countryToPortMaster->e_transport_way. ')' : ''):'').'</option>';
						}
					}
					
				}
			}
			
			echo $html;die;
		}
		
	}
	public function getFbaRecordDetails(Request $request){
		$whereData = [];
		$html = "";
		$countryPortMasterIds = (!empty($request->input('country_port_master_id')) ? explode("," , $request->input('country_port_master_id') ) : [] );
		
		if(!empty($countryPortMasterIds) ){
			$countryPortMasterIds = array_map(function($countryPortMasterId){
				return (int)Wild_tiger::decode($countryPortMasterId);
			}, $countryPortMasterIds);
			
			$whereData['status'] = config('constants.PENDING_STATUS');
			$whereData['country_to_port_goods_out_id'] = $countryPortMasterIds;
			
			$fbdDetailModal = new FBASheeteDetailModel();
			$getFbaRecordDetails = $fbdDetailModal->getFBASheetDetails($whereData);
			if(!empty($getFbaRecordDetails)){
				$data['getFbaRecordDetails'] = $getFbaRecordDetails;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'agent-warehouse-to-amazon/agent-warehouse-to-amazon-fba-goods' )->with ( $data )->render();
			}
		}
		echo $html;die;
	}
	public function add(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		$statusRecordId = (!empty($request->input('status')) ? (int)Wild_tiger::decode($request->input('status')) : 0 );
		
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['way_of_transport'] = 'required';
		$formValidation['logistic_partner_name'] = 'required';
		$formValidation['to'] = 'required';
		//$formValidation['select_containers'] = 'required';
		$formValidation['to_warehouse'] = 'required_if:to,==,'.config('constants.WAREHOUSE_FBA_SHEET');
		$formValidation['book_by'] = 'required';
		$formValidation['logistic_partner'] = 'required';
		$formValidation['booking_date'] = 'required';
		$formValidation['tracking_no'] = 'required';
		$formValidation['status'] = 'required';
		if(($statusRecordId == config('constants.DELIVERED_STATUS_ID')) || ($statusRecordId == config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'))){
			$formValidation['collection_date'] = 'required';
			$formValidation['delivery_date'] = 'required';
		}
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'way_of_transport.required' => __ ( 'messages.require-way-of-transport' ),
				'logistic_partner_name.required' => __ ( 'messages.require-from' ),
				'to.required' => __ ( 'messages.require-to' ),
				//'select_containers.required' => __ ( 'messages.require-select-containers' ),
				'to_warehouse.required_if' => __ ( 'messages.require-warehouse' ),
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
		$successMessage =  trans('messages.success-create',['module'=>trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
		$errorMessages = trans('messages.error-create',['module'=>trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
		
		DB::beginTransaction();
		try{
			$recordData = $containerId = $selectedFbaId = [];
			$agentWarehouseAmazonDocumentTypeCount = (!empty($request->input('agent_warehouse_document_type_count')) ? (int)($request->input('agent_warehouse_document_type_count')) : 1 );
			$agentWarehouseAmazonTransporterCount = (!empty($request->input('agent_warehouse_transporter_count')) ? (int)($request->input('agent_warehouse_transporter_count')) : 1 );
			
			$selectedFbaRecordsIds = (!empty($request->input('checkbox')) ? ($request->input('checkbox')) :'');
			$selectedContainerIds = (!empty($request->input('select_containers')) ? ($request->input('select_containers')) : '' );
			
			if(!empty($selectedContainerIds)){
				foreach($selectedContainerIds as $selectedContainerId){
					$containerId[] = (int)Wild_tiger::decode($selectedContainerId);
				}
					
			}
			if(!empty($selectedFbaRecordsIds)){
				foreach($selectedFbaRecordsIds as $selectedFbaRecordsId){
					$selectedFbaId[] = (int)Wild_tiger::decode($selectedFbaRecordsId);
				}
					
			}
			
			$warehouseToAmazonToCustomer = (!empty($request->input('to')) ? ($request->input('to')) : '' );
			$recordData['e_transport_way'] = (!empty($request->input('way_of_transport')) ? ($request->input('way_of_transport')) : '' );
			$recordData['i_from_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner_name')) ? (int)Wild_tiger::decode($request->input('logistic_partner_name')) : 0 );
			$recordData['v_container_ids'] = implode(',', $containerId);
			$recordData['e_to_location'] = $warehouseToAmazonToCustomer;
			$recordData['i_to_warehouse_id'] = null;
			if ($recordData['e_to_location'] == config("constants.WAREHOUSE_FBA_SHEET")){
				$recordData['i_to_warehouse_id'] = (!empty($request->input('to_warehouse')) ? (int)Wild_tiger::decode($request->input('to_warehouse')) : null);
			}
			$recordData['i_book_by_employee_id'] = (!empty($request->input('book_by')) ? (int)Wild_tiger::decode($request->input('book_by')) : 0);
			$recordData['i_logistic_partner_detail_id'] = (!empty($request->input('logistic_partner')) ? (int)Wild_tiger::decode($request->input('logistic_partner')) : 0 );
			$recordData['dt_booking_date'] = (!empty($request->input('booking_date')) ? dbDate($request->input('booking_date')) : '' );
			$recordData['v_tracking_no'] = (!empty($request->input('tracking_no')) ? ($request->input('tracking_no')) : '' );
			$recordData['v_tracking_link'] = (!empty($request->input('tracking_link')) ? ($request->input('tracking_link')) : null );
			$recordData['dt_collection_date'] = (!empty($request->input('collection_date')) ? dbDate($request->input('collection_date')) : null );
			$recordData['dt_delivery_date'] = (!empty($request->input('delivery_date')) ? dbDate($request->input('delivery_date')) : null );
			$recordData['i_status_id'] = $statusRecordId;
			$recordData['v_status_comment'] = (!empty($request->input('status_comments')) ? ($request->input('status_comments')) : null );
			
			if($recordId > 0){
				$successMessage =  trans('messages.success-update',['module'=>trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
				$errorMessages = trans('messages.error-update',['module'=>trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
				$whereData = [];
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
				$agentWarehouseRecordDetails = $this->crudModel->getRecordDetails($whereData);
				
				$agentWarehouseDetails = (!empty($agentWarehouseRecordDetails) ? $agentWarehouseRecordDetails[0] : []);
				$previousSelectedAgentWarehouseDetailIds = [];
				$agentWarehouseRecords = ( (isset($agentWarehouseDetails->detailInfo) && (!empty($agentWarehouseDetails->detailInfo))) ? objectToArray($agentWarehouseDetails->detailInfo) : [] );
				
				if(!empty($agentWarehouseRecords)){
					$previousSelectedAgentWarehouseDetailIds = array_column($agentWarehouseRecords, 'i_fba_sheet_detail_id');
						
				}
				
				if(!empty($agentWarehouseDetails->documentInfo)){
					foreach ($agentWarehouseDetails->documentInfo as $agentWarehouseDocumentDetail){
						$agentWarehouseDocumentDetailId = $agentWarehouseDocumentDetail->i_id;
						if(!empty($request->input('edit_type_'.$agentWarehouseDocumentDetailId))){
							$agentWarehouseDocument = [];
							$agentWarehouseDocument['i_document_type_id'] = (!empty($request->input('edit_type_'.$agentWarehouseDocumentDetailId)) ? (int)Wild_tiger::decode($request->input('edit_type_'.$agentWarehouseDocumentDetailId)) :0);
							$agentWarehouseDocument['v_document_remark'] = (!empty($request->input('edit_remarks_'.$agentWarehouseDocumentDetailId)) ? $request->input('edit_remarks_'.$agentWarehouseDocumentDetailId) : null);
								
							if($request->hasFile('edit_file_'.$agentWarehouseDocumentDetailId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_file_'.$agentWarehouseDocumentDetailId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$agentWarehouseDocument['v_document_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
								$removeFiles = (!empty($request->input('remove_document_'.$agentWarehouseDocumentDetailId)) ? explode("," , $request->input('remove_document_'.$agentWarehouseDocumentDetailId) ) : []  );
								$previousUploadFiles = (!empty($agentWarehouseDocumentDetail->v_document_file_path) ? json_decode($agentWarehouseDocumentDetail->v_document_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$agentWarehouseDocument['v_document_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if((!empty($agentWarehouseDocument ['i_document_type_id']))){
								$agentWarehouseDocumentDetailUpdate = $this->crudModel->updateTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $agentWarehouseDocument , [ 'i_id' => $agentWarehouseDocumentDetailId] );
				
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $agentWarehouseDocumentDetailId] );
						}
					}
				}
				if(!empty($agentWarehouseDetails->invoiceInfo)){
					foreach ($agentWarehouseDetails->invoiceInfo as $agentWarehouseInvoiceDetail){
				
						$agentWarehouseInvoiceRecordId = $agentWarehouseInvoiceDetail->i_id;
						if(!empty($request->input('edit_name_'.$agentWarehouseInvoiceRecordId))){
							$agentWarehouseInvoice = [];
							$agentWarehouseInvoice['i_logistic_partner_master_id'] = (!empty($request->input('edit_name_'.$agentWarehouseInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_name_'.$agentWarehouseInvoiceRecordId)) : 0 );
							$agentWarehouseInvoice['v_invoice_no'] = (!empty($request->input('edit_inv_no_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_inv_no_'.$agentWarehouseInvoiceRecordId) :'' );
							$agentWarehouseInvoice['d_freight_charge'] = (!empty($request->input('edit_freight_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_freight_'.$agentWarehouseInvoiceRecordId) : 0 );
							$agentWarehouseInvoice['d_custom_charge'] = (!empty($request->input('edit_custom_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_custom_'.$agentWarehouseInvoiceRecordId) :0 );
							$agentWarehouseInvoice['d_duty_charge'] = (!empty($request->input('edit_duty_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_duty_'.$agentWarehouseInvoiceRecordId)  : 0 );
							$agentWarehouseInvoice['d_other_charge'] = (!empty($request->input('edit_other_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_other_'.$agentWarehouseInvoiceRecordId) :0 );
							$agentWarehouseInvoice['d_vat_charge'] = (!empty($request->input('edit_vat_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_vat_'.$agentWarehouseInvoiceRecordId) : 0 );
							$totalCharges = $agentWarehouseInvoice['d_freight_charge'] + $agentWarehouseInvoice['d_custom_charge'] + $agentWarehouseInvoice['d_duty_charge'] + $agentWarehouseInvoice['d_other_charge'] + $agentWarehouseInvoice['d_vat_charge'];
							$agentWarehouseInvoice['d_total_charge'] = $totalCharges;
							$agentWarehouseInvoice['i_invoice_currency_id'] = (!empty($request->input('edit_currency_id_'.$agentWarehouseInvoiceRecordId)) ? (int)Wild_tiger::decode($request->input('edit_currency_id_'.$agentWarehouseInvoiceRecordId)) : 0);
							$agentWarehouseInvoice['d_conversion_rate'] = (!empty($request->input('edit_cov_rate_'.$agentWarehouseInvoiceRecordId)) ? $request->input('edit_cov_rate_'.$agentWarehouseInvoiceRecordId) : 0);
							$finalCharges = ($totalCharges * $agentWarehouseInvoice['d_conversion_rate']);
							$agentWarehouseInvoice['d_final_charge'] = $finalCharges;
								
							if($request->hasFile('edit_invoice_file_'.$agentWarehouseInvoiceRecordId)){
								$uploadFile = $this->uploadMultipleFile($request, 'edit_invoice_file_'.$agentWarehouseInvoiceRecordId,'image_doc_pdf_xls');
								if(isset($uploadFile['status']) && ( $uploadFile['status'] != false ) ){
									$agentWarehouseInvoice['v_invoice_file_path'] = json_encode($uploadFile['filePath']);
								} else {
									DB::rollback();
									Wild_tiger::setFlashMessage ( 'danger', isset($uploadFile['message']) ? $uploadFile['message'] : trans('messages.error-file-upload') );
									return redirect ( $this->redirectUrl );
								}
							} else {
				
								$removeFiles = (!empty($request->input('remove_invoice_'.$agentWarehouseInvoiceRecordId)) ? explode("," , $request->input('remove_invoice_'.$agentWarehouseInvoiceRecordId) ) : []  );
								$previousUploadFiles = (!empty($agentWarehouseInvoiceDetail->v_invoice_file_path) ? json_decode($agentWarehouseInvoiceDetail->v_invoice_file_path,true) : [] );
								$newFilesArray = [];
								if(!empty($previousUploadFiles)){
									foreach($previousUploadFiles as $previousUploadFile){
										if(!in_array(basename($previousUploadFile) , $removeFiles )){
											$newFilesArray[] = $previousUploadFile;
										}
									}
								}
								$agentWarehouseInvoice['v_invoice_file_path'] = (!empty($newFilesArray) ? json_encode($newFilesArray) : null );
							}
							if(( $agentWarehouseInvoice['i_logistic_partner_master_id'] > 0 ) && (!empty($agentWarehouseInvoice['v_invoice_no']) ) ){
								$agentWarehouseInvoiceUpdate = $this->crudModel->updateTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE') , $agentWarehouseInvoice , [ 'i_id' => $agentWarehouseInvoiceRecordId] );
							}
						} else {
							$deleteRecordData = [];
							$deleteRecordData ['t_is_active'] = 0;
							$deleteRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE') , $deleteRecordData , [ 'i_id' => $agentWarehouseInvoiceRecordId] );
						}
					}
				}
				$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' => $recordId] );
				$insertRecord = $recordId;
			} else {
				$agentToWarehouseMasterRecordDetails = $this->crudModel->selectData(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE') ,['i_id']);
				
				$agentToWarehouseMasterRecordCount = count($agentToWarehouseMasterRecordDetails);
				$count = ( ( (!empty($agentToWarehouseMasterRecordCount)) && ( $agentToWarehouseMasterRecordCount > 0 ) ) ? ( $agentToWarehouseMasterRecordCount + 1  ) : 1 );
				$generateNumber = threeNumberSeries($count);
					
				$generateAgentWarehouseEntryNo = $generateNumber.'-'.$this->todayDate;
					
				if($warehouseToAmazonToCustomer == config('constants.WAREHOUSE_FBA_SHEET')){
					$agentToWarehouseGenerateNo = config('constants.AGENT_WAREHOUSE_TO_WAREHOUSE').'-'. $generateAgentWarehouseEntryNo;
				}
				if($warehouseToAmazonToCustomer == config('constants.CUSTOMER_FBA_SHEET')){
					$agentToWarehouseGenerateNo = config('constants.AGENT_WAREHOUSE_TO_CUSTOMER').'-'. $generateAgentWarehouseEntryNo;
				}
				if($warehouseToAmazonToCustomer == config('constants.AMAZON_FBA_SHEET')){
					$agentToWarehouseGenerateNo = config('constants.AGENT_WAREHOUSE_TO_AMAZON').'-'. $generateAgentWarehouseEntryNo;
				}
				$recordData['v_agent_to_warehouse_record_no'] = $agentToWarehouseGenerateNo;
					
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				
			}
			if( $insertRecord > 0 ){
				$result = true;
			}
			$agentToWarehouseDetail = $fbaRecordDetails = [];
			if(!empty($selectedFbaRecordsIds)){
				foreach ($selectedFbaRecordsIds as $selectedFbaRecordsId){
					$checkboxFbaDetailId = (int)Wild_tiger::decode($selectedFbaRecordsId);
					
					$agentToWarehouseDetail['i_fba_sheet_detail_id'] = $checkboxFbaDetailId;
					if((!empty($previousSelectedAgentWarehouseDetailIds) && (in_array($checkboxFbaDetailId, $previousSelectedAgentWarehouseDetailIds)))){
						$searchPreviousRecordKey = array_search($checkboxFbaDetailId,$previousSelectedAgentWarehouseDetailIds);
						if(strlen($searchPreviousRecordKey) > 0 ){
							$this->crudModel->updateTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DETAIL_TABLE') , $agentToWarehouseDetail, [ 'i_id' => $agentWarehouseRecords[$searchPreviousRecordKey]['i_id'] ]);
							unset($agentWarehouseRecords[$searchPreviousRecordKey]);
						}
					} else {
						$getFbaRecordInfo = $this->crudModel->getSingleRecordById(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') ,['e_status'] ,['t_is_deleted !=' =>1,'i_id' => $checkboxFbaDetailId]);
						
						if(!empty($getFbaRecordInfo) && ($getFbaRecordInfo->e_status == config('constants.COMPLETED_STATUS') )){
							Wild_tiger::setFlashMessage ( 'danger',trans('messages.error-record-used-into-agent-warehouse-to'));
							return redirect ($this->redirectUrl );
						} 
						$agentToWarehouseDetail['i_agent_to_warehouse_master_id'] = $insertRecord;
						$insertAgentToWarehouseDetail = $this->crudModel->insertTableData(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DETAIL_TABLE') , $agentToWarehouseDetail);
						$fbaRecordDetails['e_status'] = config('constants.COMPLETED_STATUS');
						$fbaRecordDetailsUpdate = $this->crudModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), $fbaRecordDetails , [ 'i_id' => $checkboxFbaDetailId] );
							
					}
					
				}
			}
			if(!empty($agentWarehouseRecords)){
				foreach ($agentWarehouseRecords as $agentWarehouseRecord){
					$deleteRecordData = [];
					$deleteRecordData ['t_is_active'] = 0;
					$deleteRecordData ['t_is_deleted'] = 1;
					$this->crudModel->deleteTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DETAIL_TABLE') , $deleteRecordData , [ 'i_id' => $agentWarehouseRecord['i_id'] ] );
					$fbaRecordDetail['e_status'] = config('constants.PENDING_STATUS');
					$fbaDetailUpdate = $this->crudModel->updateTableData( config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') , $fbaRecordDetail , [ 'i_id' => $agentWarehouseRecord['i_fba_sheet_detail_id'] ] );
					
				}
			}
			for ($i = 0; $i <= $agentWarehouseAmazonDocumentTypeCount; $i++){
				$rowData = [];
				$rowData['i_agent_to_warehouse_goods_out_master_id'] = $insertRecord;
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
					$insertAgentWarehouseDocumentDetail = $this->crudModel->insertTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DOCUMENT_MASTER_TABLE') , $rowData);
				}
			
			}
			for ($i = 0; $i <= $agentWarehouseAmazonTransporterCount;$i++){
				$rowData = [];
				$rowData['i_agent_to_warehouse_goods_out_master_id'] = $insertRecord;
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
					$insertAgentToWarehouseInvoice = $this->crudModel->insertTableData( config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE') , $rowData);
				}
			}
			//echo "<pre>";print_r($containerId);
			if(!empty($containerId)){
				foreach ($containerId as $containerRecordId){
					$whereData['status'] = config('constants.PENDING_STATUS');
					$whereData['country_to_port_goods_out_id'] = $containerRecordId;
						
					$fbdDetailModal = new FBASheeteDetailModel();
					$getFbaRecordDetails =  $fbdDetailModal->getFBASheetDetails($whereData);
					
					if(count($getFbaRecordDetails) > 0 ){
						$updateCountryToPort['e_process_status'] = config('constants.PARTIAL_DELIVERY_TYPE');
						$this->crudModel->updateTableData(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE'), ['e_container_status' => config('constants.PARTIAL_DELIVERY_TYPE') ]  , [ 'i_container_id' => $containerRecordId] );
					} else {
						$updateCountry['e_process_status'] = config('constants.COMPLETED_STATUS');
						$this->crudModel->updateTableData(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE'), ['e_container_status' => config('constants.COMPLETED_STATUS') ]  , [ 'i_container_id' => $containerRecordId] );
			
					}
					
					
				}
				
				
				$getAllContainerStatusIds = $this->crudModel->getSingleRecordById(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE') , [ 'i_id' ] , [ 't_is_deleted != ' => 1 , 'e_container_status' => config('constants.PARTIAL_DELIVERY_TYPE') ] , [] , [ 'whereIn' => [  'i_container_id' , $containerId  ] ] );
				
				foreach ($containerId as $containerRecordId){
					if(!empty($getAllContainerStatusIds)){
						PortToAgentWarehouseModel::whereRaw('FIND_IN_SET("'.$containerRecordId.'", v_container_ids)')->update([
								'e_process_status' => config('constants.PARTIAL_DELIVERY_TYPE'),
								'i_updated_id' => session()->get('user_id'),
								'dt_updated_at' => date('Y-m-d H:i:s')
						]);
					} else {
						PortToAgentWarehouseModel::whereRaw('FIND_IN_SET("'.$containerRecordId.'", v_container_ids)')->update([
								'e_process_status' => config('constants.COMPLETED_STATUS'),
								'i_updated_id' => session()->get('user_id'),
								'dt_updated_at' => date('Y-m-d H:i:s')
						]);
							
					}
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
		if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		
		if( $recordId > 0 ){
			$whereData = $data = $fbaWhereData = $where = [];
			$whereData['master_id'] = $recordId;
			$whereData['edit_record'] = true;
			$data['pageTitle'] = trans('messages.update-agent-warehouse-to-amazon');
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			
			if(count($recordInfo) > 0){
				
				$errorFound = false;
				
				//$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				$data['documentTypeRecordDetails'] = Document_Type_Master_Model::where('e_document_type',config('constants.LOGISTIC'))->orderBy('v_document_type_name', 'ASC')->get();
				$data['currencyRecordDetails'] = CurrencyMasterModel::orderBy('v_currency_name', 'ASC')->get();
				$data['wayOfTransportDetails'] = wayOfTransportDetails( [  config('constants.AIR_TRANSPORT') , config('constants.SEA_TRANSPORT'), config('constants.TRUCK_TRANSPORT'), config('constants.ROAD_TRANSPORT') ] );
				$data['wayToWarehouseDetails'] = wayToWarehouseDetails();
				$data['userRecordDetails'] = Login::where('v_role',config ( 'constants.ROLE_USER'))->whereRaw("find_in_set('".config("constants.LOGISTIC")."',v_record_type)")->orderBy('v_name', 'ASC')->get();
				$data['statusMasterRecordDetails'] = StatusMasterModel::orderBy('i_sequence', 'ASC')->get();
				$data['logisticPartnerRecordDetails'] = LogisticPartnerDetailModel::with(['logisticPartnerMaster'])->get();
				$data['logisticPartnerDetails'] = LogisticPartnerMasterModel::orderBy('v_logistic_partner_name', 'ASC')->get();
				$data['toWarehouseDetails'] = WarehouseMasterModel::where('i_country_id', config ('constants.USA'))->where('e_record_type', config ('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				
				
				if(isset($data ['recordInfo']->detailInfo)){
					$allDetails = json_decode(json_encode($data ['recordInfo']->detailInfo),true);
					$allUsedFBASheetDetailIds =  (!empty($allDetails) ? array_column($allDetails, 'i_fba_sheet_detail_id') : [] );
					if(!empty($allUsedFBASheetDetailIds)){
						$fbaWhereData['selectedRecordIds'] = $allUsedFBASheetDetailIds;
					}
				}
				
				
				$fbdDetailModal = new FBASheeteDetailModel();
				$getFbaRecordDetails =  $fbdDetailModal->getFBASheetDetails($fbaWhereData);
				$data['getFbaRecordDetails'] = $getFbaRecordDetails;
				
				$mainContainerId = ( isset($data ['recordInfo']->v_container_ids) ? $data ['recordInfo']->v_container_ids : 0 );
				//echo '<pre>';print_r($mainContainerId);
				//$data['getCountryToPortGoodsOutRecordDetails'] = [];
				if( $mainContainerId > 0 ){
					$where['pt.i_id'] = $recordId;
					$where['pt.t_is_deleted !='] = 1;
					$where['cp.t_is_deleted !='] = 1;
					//$joindata[]= ['tableName' =>config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'). ' as cp','condition'=>'pt.v_container_ids = cp.i_id'];
					$joindata[]= ['tableName' =>config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE'). ' as cp','condition'=>   [ 'custom' =>  "find_in_set(cp.i_id , pt.v_container_ids)" ] ];
					
					$data['getCountryToPortGoodsOutRecordDetails'] = $this->crudModel->selectJoinData(config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_MASTER_TABLE'). ' as pt'  , ['pt.i_id','cp.i_id as country_port_master_id','pt.v_container_ids','cp.v_country_to_port_record_no','cp.e_transport_way'] , $joindata , $where);
					
				} 
				//$data['getCountryToPortGoodsOutRecordDetails'] = $this->countryToPortGoodsOut->getRecordDetails($whereData);
				
				//$data['getCountryToPortGoodsOutRecordDetails'] = $this->crudModel->getRecordDetails($whereData);
			
				//echo '<pre>';print_r($data['getCountryToPortGoodsOutRecordDetails']);die;
				$disableForm = '';
				$documentForm = '';
				$statusDisableForm = '';
				if( isset($this->secondUriSegment) && ( $this->secondUriSegment == 'view' ) ){
					$data ['pageTitle'] = trans('messages.view-agent-warehouse-to-amazon-warehouse-customer');
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
				return view ( $this->folderName . 'add-agent-warehouse-to-amazon' )->with ( $data );
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
		if( ( !empty($request->post('search_way_of_transport') ) ) && ( $request->post('search_way_of_transport') ) ){
			$whereData['way_of_transport'] = ( $request->post('search_way_of_transport') );
		}
		if( ( !empty($request->post('logistic_partner_name') ) ) && ( $request->post('logistic_partner_name') ) ){
			$whereData['from_port'] = (int)Wild_tiger::decode($request->post('logistic_partner_name'));
		}
		if( ( !empty($request->post('search_to') ) ) && ( $request->post('search_to') ) ){
			$whereData['to_location'] = ($request->post('search_to'));
		}
		if( ( !empty($request->post('search_to_warehouse') ) ) && ( $request->post('search_to_warehouse') ) ){
			$whereData['to_warehouse'] = (int)Wild_tiger::decode($request->post('search_to_warehouse'));
		}
		if( ( !empty($request->post('search_book_by') ) ) && ( $request->post('search_book_by') ) ){
			$whereData['book_by'] = (int)Wild_tiger::decode($request->post('search_book_by'));
		}
		if( ( !empty($request->post('search_logistic_partner') ) ) && ( $request->post('search_logistic_partner') ) ){
			$whereData['logistic_partner'] = (int)Wild_tiger::decode($request->post('search_logistic_partner'));
		}
		if( ( !empty($request->post('search_booking_from_date') ) ) && ( $request->post('search_booking_from_date') ) ){
			$whereData['booking_form_date'] = ($request->post('search_booking_from_date'));
		}
		if( ( !empty($request->post('search_booking_to_date') ) ) && ( $request->post('search_booking_to_date') ) ){
			$whereData['booking_to_date'] = ($request->post('search_booking_to_date'));
		}
		if( ( !empty($request->post('search_collection_form_date') ) ) && ( $request->post('search_collection_form_date') ) ){
			$whereData['collection_form_date'] = ($request->post('search_collection_form_date'));
		}
		if( ( !empty($request->post('search_collection_to_date') ) ) && ( $request->post('search_collection_to_date') ) ){
			$whereData['collection_to_date'] = ($request->post('search_collection_to_date'));
		}
		if( ( !empty($request->post('search_delivery_form_date') ) ) && ( $request->post('search_delivery_form_date') ) ){
			$whereData['delivery_form_date'] = ($request->post('search_delivery_form_date'));
		}
		if( ( !empty($request->post('search_delivery_to_date') ) ) && ( $request->post('search_delivery_to_date') ) ){
			$whereData['delivery_to_date'] = ($request->post('search_delivery_to_date'));
		}
		/* if( ( !empty($request->post('search_status') ) ) && ( $request->post('search_status') ) ){
			$whereData['status'] = (int)Wild_tiger::decode($request->post('search_status'));
		} */
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
		if( ( !empty($request->post('search_select_containers') ) ) && ( $request->post('search_select_containers') ) ){
			$allContainerIds = explode("," , $request->post('search_select_containers') );
				
			if(!empty($allContainerIds)){
				$allContainerIds = array_map(function($allContainerId){
					return (int)Wild_tiger::decode($allContainerId);
				}, $allContainerIds);
			}
			if(!empty($allContainerIds)){
				$whereData['select_containers'] =  $allContainerIds;
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
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['entry_no'] = (isset($getExportRecordDetail->v_agent_to_warehouse_record_no) ? ($getExportRecordDetail->v_agent_to_warehouse_record_no) : '');
					$rowExcelData['way_of_transport'] = (isset($getExportRecordDetail->e_transport_way) ? ($getExportRecordDetail->e_transport_way) : '' );
					$rowExcelData['from'] = (isset($getExportRecordDetail->formLogisticInfo->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->formLogisticInfo->logisticPartnerMaster->v_logistic_partner_name :'') . (isset($getExportRecordDetail->formLogisticInfo->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->formLogisticInfo->v_logistic_partner_code.')'  : '' );
					$rowExcelData['to'] = (isset($getExportRecordDetail->e_to_location) ? $getExportRecordDetail->e_to_location :'');
					$rowExcelData['container_number'] = (isset($contanierName) ? $contanierName .(isset($transportName) ? ' ('.$transportName .')' : ''): '');
					$rowExcelData['book_by'] =  (isset($getExportRecordDetail->bookEmployeeInfo->v_name) ?  $getExportRecordDetail->bookEmployeeInfo->v_name : '' );
					$rowExcelData['logistic_partner'] = (isset($getExportRecordDetail->toLogisticInfo->logisticPartnerMaster->v_logistic_partner_name) ? $getExportRecordDetail->toLogisticInfo->logisticPartnerMaster->v_logistic_partner_name . ( isset($getExportRecordDetail->toLogisticInfo->v_logistic_partner_code) ? ' ('.$getExportRecordDetail->toLogisticInfo->v_logistic_partner_code.')'  : '' ) :'');
					$rowExcelData['booking_date'] = (isset($getExportRecordDetail->dt_booking_date) ?  clientDate($getExportRecordDetail->dt_booking_date) : '' );
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
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'agent-warehouse-to-amazon/agent-warehouse-to-amazon-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$agentWarehouseDetailData['t_is_active'] = 0;
			$agentWarehouseDetailData['t_is_deleted'] = 1;
			$successMessage =  trans('messages.success-delete',['module'=>trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
			$errorMessages = trans('messages.error-delete',['module'=>trans('messages.agent-warehouse-to-amazon-warehouse-customer')]);
		
			DB::beginTransaction();
		
			$result = false;
			try{
				$whereData['master_id'] = $recordId;
				$whereData['edit_record'] = true;
				$recordInfo = $this->crudModel->getRecordDetails($whereData);
				
				
				$fbaRecordDetails = (isset($recordInfo[0]->detailInfo) ? $recordInfo[0]->detailInfo  : []);
				
				if(!empty($fbaRecordDetails)){
					foreach ($fbaRecordDetails as $fbaRecordDetail){
						$fbaRecordData = [];
						$fbaRecordData['e_status'] = config('constants.PENDING_STATUS');
						$this->crudModel->updateTableData(  config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') ,  $fbaRecordData , [ 'i_id' => $fbaRecordDetail->i_fba_sheet_detail_id ] );
						
					}
				}
				
				if(isset($recordInfo[0]->v_container_ids) && (!empty($recordInfo[0]->v_container_ids))){
					$allUsedContainerIds = explode("," , $recordInfo[0]->v_container_ids );
					$partialStatus = false;
					if(!empty($allUsedContainerIds)){
						foreach($allUsedContainerIds as $allUsedContainerId){
							$whereData = [];
							$whereData['status'] = config('constants.COMPLETED_STATUS');
							$whereData['country_to_port_goods_out_id'] = $allUsedContainerId;
							
							$fbdDetailModal = new FBASheeteDetailModel();
							$getFbaRecordDetails =  $fbdDetailModal->getFBASheetDetails($whereData);
							
							if(count($getFbaRecordDetails) > 0 ){
								$partialStatus = true;
								$this->crudModel->updateTableData(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE'), ['e_container_status' => config('constants.PARTIAL_DELIVERY_TYPE') ]  , [ 'i_container_id' => $allUsedContainerId] );
							} else {
								$this->crudModel->updateTableData(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE'), ['e_container_status' => config('constants.PENDING_STATUS') ]  , [ 'i_container_id' => $allUsedContainerId] );
							}
						}
					}
					$portToAgentRecordStatus = [];
					$portToAgentRecordStatus['e_process_status'] = config('constants.PENDING_STATUS') ;
					if( $partialStatus != false ){
						$portToAgentRecordStatus['e_process_status'] = config('constants.PARTIAL_DELIVERY_TYPE') ;
					}
					if(!empty($allUsedContainerIds)){
						foreach ($allUsedContainerIds as $containerRecordId){
							if(!empty($getAllContainerStatusIds)){
								PortToAgentWarehouseModel::whereRaw('FIND_IN_SET("'.$containerRecordId.'", v_container_ids)')->update([
										'e_process_status' => $portToAgentRecordStatus['e_process_status'],
										'i_updated_id' => session()->get('user_id'),
										'dt_updated_at' => date('Y-m-d H:i:s')
								]);
							} else {
								PortToAgentWarehouseModel::whereRaw('FIND_IN_SET("'.$containerRecordId.'", v_container_ids)')->update([
										'e_process_status' => $portToAgentRecordStatus['e_process_status'],
										'i_updated_id' => session()->get('user_id'),
										'dt_updated_at' => date('Y-m-d H:i:s')
								]);
						
							}
						}
					}
				}
				
				
				
				$this->crudModel->deleteTableData(  config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DETAIL_TABLE') ,  $agentWarehouseDetailData , [ 'i_agent_to_warehouse_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_DOCUMENT_MASTER_TABLE') ,  $agentWarehouseDetailData , [ 'i_agent_to_warehouse_goods_out_master_id' => $recordId ] );
				$this->crudModel->deleteTableData(  config('constants.AGENT_TO_WAREHOUSE_GOODS_OUT_INVOICE_MASTER_TABLE') ,  $agentWarehouseDetailData , [ 'i_agent_to_warehouse_goods_out_master_id' => $recordId ] );
		
				$this->crudModel->deleteTableData($this->tableName,  $agentWarehouseDetailData , [ 'i_id' => $recordId ] );
		
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
}
