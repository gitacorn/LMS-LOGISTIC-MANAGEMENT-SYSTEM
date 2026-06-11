<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MasterController;
use Illuminate\Http\Request;
use App\LookupMaster;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueLookupValue;
use Illuminate\Support\Facades\Validator;

class LookupMasterController extends MasterController
{
    //
	public $urlFirstSegment;
	public function __construct(){
	
		parent::__construct();
	
		$this->middleware('checklogin');
	
		$this->crudModel =  New LookupMaster();
		$this->moduleName = 'lookup';
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->tableName = config ( 'constants.LOOKUP_MASTER_TABLE' );
		$this->folderName = config ( 'constants.ADMIN_FOLDER' ) . 'lookup-master/' ;
		$this->urlFirstSegment = (!empty(request()->segment(1)) ? request()->segment(1) : '');
	}
	
	public function index()
	{
		$where = [];
		
		switch($this->urlFirstSegment){
			case config('constants.LOCATION_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.location') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.location') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.location') ] ) ;
				$data['columnName'] = trans('messages.location') ;
				$where['v_module_name'] = config('constants.LOCATION_LOOKUP');
				$data['moduleName'] = config('constants.LOCATION_LOOKUP');
				break;
			case config('constants.TYPE_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.type') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.type') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.type') ] ) ;
				$data['columnName'] = trans('messages.type') ;
				$where['v_module_name'] = config('constants.TYPE_LOOKUP');
				$data['moduleName'] = config('constants.TYPE_LOOKUP');
				break;
			case config('constants.PAYMENT_TERMS_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.payment-terms') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.payment-terms') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.payment-terms') ] ) ;
				$data['columnName'] = trans('messages.payment-terms') ;
				$where['v_module_name'] = config('constants.PAYMENT_TERMS_LOOKUP');
				$data['moduleName'] = config('constants.PAYMENT_TERMS_LOOKUP');
				break;
			case config('constants.GOODS_REMARK_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.goods-remarks') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.goods-remarks') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.goods-remarks') ] ) ;
				$data['columnName'] = trans('messages.goods-remarks') ;
				$where['v_module_name'] = config('constants.GOODS_REMARK_LOOKUP');
				$data['moduleName'] = config('constants.GOODS_REMARK_LOOKUP');
				break;
			case config('constants.DANGEROUS_GOODS_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.dangerous-goods') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.dangerous-goods') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.dangerous-goods') ] ) ;
				$data['columnName'] = trans('messages.dangerous-goods') ;
				$where['v_module_name'] = config('constants.DANGEROUS_GOODS_LOOKUP');
				$data['moduleName'] = config('constants.DANGEROUS_GOODS_LOOKUP');
				break;
			case config('constants.BOOKING_PORTAL_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.booking-portal') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.booking-portal') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.booking-portal-name') ] ) ;
				$data['columnName'] = trans('messages.booking-portal-name');
				$where['v_module_name'] = config('constants.BOOKING_PORTAL_LOOKUP');
				$data['moduleName'] = config('constants.BOOKING_PORTAL_LOOKUP');
				break;
			case config('constants.DAILY_MAIL_MASTER'):
				$data['pageTitle'] = trans ( 'messages.master-module', [ 'module' => trans('messages.daily-mail') ] );
				$data['addTitle'] = trans ( 'messages.add-module', [ 'module' => trans('messages.daily-mail') ] ) ;
				$data['searchByTitle'] = trans ( 'messages.search-by-module', [ 'module' => trans('messages.daily-mail') ] ) ;
				$data['columnName'] = trans('messages.daily-mail');
				$where['v_module_name'] = config('constants.DAILY_MAIL_LOOKUP');
				$data['moduleName'] = config('constants.DAILY_MAIL_LOOKUP');
				break;
		}
		// defaulr page is first page
		$page = $this->defaultPage;
	
		// store pagination data array
		$paginationData =  [];
		// get pagination dat for first page
		if ($page == $this->defaultPage) {
			//$where['getCount'] = true;
			$totalRecords = count($this->crudModel->getRecordDetails( $where ));
			//$totalRecords = (!empty($totalRecords) ? $totalRecords[0]->record_count : 0 );
			//unset($where['getCount']);
			$lastpage = ceil($totalRecords / $this->perPageRecord);
			
			$paginationData['current_page'] = $this->defaultPage;
			
			$paginationData['per_page'] = $this->perPageRecord;
			
			$paginationData['last_page'] = $lastpage;
		}
	
		//$where['limit'] = $this->perPageRecord;
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $where );
		
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = $totalRecords;
		
		return view( $this->folderName . 'lookup-master')->with($data);
	
	}
	
	public function filter(Request $request) {
	
		//variable defined
		$whereData = $likeData = $additionalData =  [ ];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		//search record
		if (! empty ( $request->post ( 'search_by_value' ) )) {
			$searchByName = trim ( $request->post ( 'search_by_value' ) );
			$likeData ['v_value'] = $searchByName;
		}
	
		//check status
		if( ( !empty($request->post('search_status') ) ) && ( $request->post('search_status') != 'all' ) ){
			$whereData['t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 ); 
		}
	
		if( ( !empty($request->post('module_name') ) ) ){
			$whereData ['v_module_name'] =  trim($request->post('module_name'));
		}
	
		$paginationData = [];
	
		if ($page == $this->defaultPage) {
	
			$totalRecords = count($this->crudModel->getRecordDetails( $whereData , $likeData ));
			
			$lastpage = ceil($totalRecords / $this->perPageRecord);
		
			$paginationData['current_page'] = $this->defaultPage;
		
			$paginationData['per_page'] = $this->perPageRecord;
		
			$paginationData['last_page'] = $lastpage;
		}
	
		if ($page == $this->defaultPage) {
			$whereData['offset'] = 0;
			$whereData['limit'] = $this->perPageRecord;
		} else if ($page > $this->defaultPage) {
			$whereData['offset'] = ($page - 1) * $this->perPageRecord;
			$whereData['limit'] = $this->perPageRecord;
		} 
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData, $likeData );
	
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
		
		if(isset($totalRecords)){
			$data['totalRecordCount'] = $totalRecords;
		}
		
		$html = view (  config('constants.AJAX_VIEW_FOLDER') . 'lookup-master/lookup-master-list' )->with ( $data )->render();
	
		echo $html;die;
	
	}
	
	public function addLookupMaster(Request $request){
		if(!empty($request->post())){
			
			$moduleName = ( (!empty($request->post('lookup_module_name'))) ? trim($request->post('lookup_module_name')) : '' );
			$moduleValue = ( (!empty($request->post('module_value'))) ? trim($request->post('module_value')) : '' );
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0 );
			
			$formValidation = [];
			$formValidation['module_value'] = [ 'required'  , new UniqueLookupValue($request) ] ;
				
			$validator = Validator::make ( $request->all (), $formValidation , [
					'module_value.required' => trans("messages.required-enter-field-validation" , [ "fieldName" => enumText($moduleName) ]),
			] );
				
			if ($validator->fails ()) {
				$this->ajaxResponse(101, $validator->errors()->first());
			}
			
			if( (!empty($moduleName)) && (!empty($moduleValue)) ){
	
				$checkRecordWhere = [];
				$checkRecordWhere['v_module_name'] = $moduleName;
				$checkRecordWhere['v_value'] = $moduleValue;
				$checkRecordWhere['t_is_deleted != '] = 1;
	
				if( $recordId > 0 ){
					$checkRecordWhere['i_id != '] = $recordId;
				}
	
				$checkRecordExist = $this->crudModel->getSingleRecordById( $this->tableName , [ 'i_id' ] , $checkRecordWhere);
				$statusCode = 101;
				
				$statusMessage = trans ( 'messages.duplicate-module-value', [ 'module' => enumText($moduleName) ] ) ;
				if(empty($checkRecordExist)){
					
					$recordData = [];
					$recordData['v_module_name'] = $moduleName;
					$recordData['v_value'] = $moduleValue;
					
					if( $recordId > 0 ){
						
						$successMessage = trans ( 'messages.success-update', [ 'module' => Wild_tiger::enumText($moduleName) ] ) ;
						$errorMessage = trans ( 'messages.error-update', [ 'module' => Wild_tiger::enumText($moduleName) ] ) ;
						
						$result = $this->crudModel->updateTableData( $this->tableName , $recordData , [ 'i_id' => $recordId ] );
					} else {
						
						$successMessage = trans ( 'messages.success-create', [ 'module' => Wild_tiger::enumText($moduleName) ] ) ;
						$errorMessage = trans ( 'messages.error-create', [ 'module' => Wild_tiger::enumText($moduleName) ] ) ;
						
						
						$sequence = $this->crudModel->getSequence();
						$recordData['i_sequence'] = $sequence;
						$insertLookup = $this->crudModel->insertTableData( $this->tableName , $recordData  );
						if( $insertLookup > 0 ){
							$result = true;
						}
					}
						
						
					if( $result != false ){
						$statusCode = 1;
						$statusMessage = $successMessage;
					}
				}
	
				$lookupWhere = [];
				$lookupWhere['t_is_deleted != '] = 1;
				$lookupWhere['v_module_name'] = $moduleName;
				$lookupWhere['order_by'] = [ 'v_value' => 'asc' ];
	
				if( (!empty($request->post('request_type'))) && ( $request->post('request_type') == config('constants.ADD_REQUEST') ) ){
					$lookupWhere['t_is_active'] = 1;
				}
	
				$lookupList = $this->crudModel->selectData( $this->tableName , [ 'i_id' , 'v_value' ] , $lookupWhere );
				$html = '<option value="">'.trans('messages.select').'</option>';
				if(!empty($lookupList)){
					foreach($lookupList as $lookup){
						$encodeId = Wild_tiger::encode($lookup->i_id);
						$html .= '<option value="'.$encodeId.'" data-id="'.$lookup->i_id.'">'.$lookup->v_value.'</option>';
					}
				}
	
				$this->ajaxResponse($statusCode, $statusMessage ,[ 'html' => $html ] );
	
			}
		}
		$this->ajaxResponse(101, trans('messages.system-error') ); 
	}
	
	public function delete(Request $request){
		
		if(!empty($request->input())){
			
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			
			$displayModulename = (!empty($request->input('delete_module_name')) ? $request->input('delete_module_name') : trans('messages.record') );
			
			return $this->removeRecord($this->tableName, $recordId, $displayModulename );
			
		}
	}
	
	public function updateStatus(Request $request){
		
		if(!empty($request->input())){
			
			$displayModulename = (!empty($request->input('lookup_module_name')) ? $request->input('lookup_module_name') : trans('messages.record') ); 
			
			return $this->updateMasterStatus($request , $this->tableName,  $displayModulename );
		
		}
	}
	
	public function getLookupRecordInfo(Request $request){
	
		$moduleName = ( (!empty($request->post('module_name'))) ? trim($request->post('module_name')) : '' );
		$recordId = ( (!empty($request->post('record_id'))) ? (int)Wild_tiger::decode($request->post('record_id')) : 0 );
	
		if( (!empty($recordId)) ){
				
			$data['recordInfo'] = $this->crudModel->getRecordDetails( [ 'i_id' =>  $recordId , 'singleRecord' => true ] );
				
			$this->ajaxResponse(1, 'aa' , $data );
				
		}
	
	}
	
	public function sequenceUpdate(){
	
		$result = [];
		$result['status_code'] = 101;
		$result['message'] = sprintf($this->lang->line('error-sequence-update') , $this->moduleName);
		if(!empty($this->input->post())){
	
			$whereData = $likeData = [];
	
			//new sequence update array
			$newSequenceArray = ( !empty($this->input->post('new_sequence')) ? explode(',', $this->input->post('new_sequence')) : '' );
	
	
			if (! empty ( $this->input->post ( 'search_by_value' ) )) {
				$searchByName = trim ( $this->input->post ( 'search_by_value' ) );
				$likeData ['v_value'] = $searchByName;
			}
	
			//check status
			if( ( !empty($this->input->post('status') ) ) && ( $this->input->post('status') != 'all' ) ){
				$whereData ['t_is_active'] =  ( $this->input->post('status') == 'Inactive' ? 0 : 1 );
			}
				
				
			$moduleName = null;
			if( ( !empty($this->input->post('module_name') ) ) ){
				$whereData ['v_module_name'] =  trim($this->input->post('module_name'));
				$moduleName = trim($this->input->post('module_name'));
			}
				
			if( !empty($moduleName) ){
				$this->moduleName =  str_replace("_", " ", $moduleName);
				$this->redirectUrl = $moduleName;
			}
	
			// get category sequence data
			$recordDetails = $this->crud_model->getRecordDetails($whereData , $likeData);
	
			//old category sequence data
			$previousSequenceData = (!empty($recordDetails) ? array_column(json_decode(json_encode($recordDetails),true), 'i_sequence') : []);
				
			//log_message('debug', print_r($previousSequenceData,true));
			$updateSequence = false;
			if(!empty($recordDetails)){
				foreach ($recordDetails as $key => $recordDetail){
	
					$updateSequenceData = [];
	
					$updateSequenceData['i_sequence'] = (!empty($previousSequenceData[$key]) ? $previousSequenceData[$key] : 0 );
	
					//update squence query
					$updateSequence = $this->crud_model->updateTableData($this->tableName , $updateSequenceData , ['i_id'=> $newSequenceArray[$key]] );
					//log_message('debug', $this->db->last_query());
	
				}
			}
	
			if($updateSequence != false){
				$this->ajaxResponse(1, sprintf($this->lang->line('success-sequence-update') , $this->moduleName));
			} else {
				$this->ajaxResponse(101, sprintf($this->lang->line('error-sequence-update') , $this->moduleName));
			}
		}
		$this->ajaxResponse(101, sprintf($this->lang->line('error-sequence-update') , $this->moduleName));
	}
	
	public function checkUniqueLookupValue(Request $request) {
		$moduleName = ( (!empty($request->post('lookup_module_name'))) ? trim($request->post('lookup_module_name')) : '' );
		
		$validator = Validator::make ( $request->all (), [
				'module_value' => [ 'required', new UniqueLookupValue($request) ]  ,
		], [
				'module_value.required' => trans("messages.required-enter-field-validation" , [ "fieldName" => trans("messages.value") ]),
		] );
	
		if ($validator->fails ()) {
			$this->ajaxResponse(101, $validator->errors()->first());
		}
	
		$this->ajaxResponse(1, trans('messages.success'));
	}
}
