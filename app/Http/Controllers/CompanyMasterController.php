<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CompanyMasterModel;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueCompanyName;
use App\Rules\UniqueCompanyCode;
use App\Rules\UniqueCompanyShortCode;

class CompanyMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.COMPANY_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'company-master/';
		$this->moduleName = trans('messages.company');
		$this->crudModel = new CompanyMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.COMPANY_MASTER_URL');
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_COMPANY')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.company-master');
	
		$page = $this->defaultPage;
	
		#store pagination data array
		$whereData = $paginationData = [];
	
		#get pagination data for first page
		if($page == $this->defaultPage ){
	
			$totalRecords = count($this->crudModel->getRecordDetails($whereData));
	
			$lastPage = ceil($totalRecords/$this->perPageRecord);
	
			$paginationData['current_page'] = $this->defaultPage;
	
			$paginationData['per_page'] = $this->perPageRecord;
	
			$paginationData ['last_page'] = $lastPage;
	
		}
		$whereData ['limit'] = $this->perPageRecord;
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData );
		
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],[ 't_is_deleted !=' => 1 , 'order_by' => [ 'v_country_name' => 'asc' ] ]);
		
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
	
		$data['totalRecordCount'] = $totalRecords;
	
		return view($this->folderName . 'company-master')->with($data);
	
	}
	public function add(Request $request){
	
		if(!empty($request->input())){
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			if($recordId > 0) {
				if(checkPermission(config('permission_constants.EDIT_COMPANY')) != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			} else {
				if(checkPermission(config('permission_constants.ADD_COMPANY')) != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			}
			$formValidation =[];
			$formValidation['company_name'] = ['required', new UniqueCompanyName($recordId)];
			$formValidation['company_code'] = ['required', new UniqueCompanyCode($recordId)];
			$formValidation['company_short_code'] = ['required', new UniqueCompanyShortCode($recordId)];
			$formValidation['select_country_name'] = ['required'];
			$checkValidation =Validator::make($request->all(),$formValidation,
					[
							'company_name.required'=>trans('messages.require-company-name'),
							'company_code.required'=>trans('messages.require-company-code'),
							'company_short_code.required'=>trans('messages.require-company-short-code'),
							'select_country_name.required'=>trans('messages.required-country-name'),
	
					]
			);
	
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $this->moduleName ] ) ) );
					
			}
			
			$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
			$result = false;
			$html=null;
			$recordData = [];
			$recordData['v_company_name'] = (!empty($request->input('company_name')) ? trim($request->input('company_name')) :'');
			$recordData['v_company_code'] = (!empty($request->input('company_code')) ? trim($request->input('company_code')) :'');
			$recordData['v_company_short_code'] = (!empty($request->input('company_short_code')) ? trim($request->input('company_short_code')) :'');
			$recordData['i_country_id'] = (!empty($request->input('select_country_name')) ? (int)Wild_tiger::decode($request->input('select_country_name')) :0);
			$recordData['v_email'] = (!empty($request->input('email')) ? getCommaSeparatedFormattedString($request->input('email')) : null);
			
			if($recordId > 0){
					
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
					
				$result = $this->crudModel->updateTableData( $this->tableName , $recordData ,['i_id'=> $recordId] );
				
				$recordDetail = $this->crudModel->getRecordDetails( [ 'cm.i_id' => $recordId , 'singleRecord' => true  ] );
					
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'company-master/single-company-master')->with ( $recordInfo )->render();
					
					
			}else{
				$insertRecord = $this->crudModel->insertTableData( $this->tableName , $recordData  );
				if($insertRecord > 0){
					Wild_tiger::setFlashMessage('success', $successMessage);
					$result = true;
				}
			}
			if($result != false){
					
				$this->ajaxResponse(1, $successMessage,[ 'html' => $html ]);
			}else {
					
				$this->ajaxResponse(101, $errorMessages);
			}
		}
		//return redirect($this->redirectUrl);
	}
	public function edit(Request $request){
	
		$errorFound = true;
		$recordId = (!empty($request->input('record_id')) ? $request->input('record_id') : '' );
		if($recordId > 0) {
			if(checkPermission(config('permission_constants.EDIT_COMPANY')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_COMPANY')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		}
		$data = $countryWhere = [];
		$countryWhere['t_is_active'] = 1;
		$countryWhere['t_is_deleted != '] = 1;
		$countryWhere['order_by']= ['v_country_name' => 'asc'];
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			if($recordId > 0){
				unset($countryWhere['t_is_active']);
			}
			$recordInfo = $this->crudModel->getRecordDetails(['cm.i_id' => $recordId ,'singleRecord'=> true ]);
	
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
			}
		}
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
		
		$html = view ($this->folderName . 'add-company-master')->with ( $data )->render();
		echo $html;die;
	
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		//search record
		if (!empty($request->post('search_by_company'))) {
			$searchByName = trim($request->post('search_by_company'));
			$likeData ['cm.v_company_name'] = $searchByName;
			$likeData ['cm.v_company_code'] = $searchByName;
			$likeData ['cm.v_company_short_code'] = $searchByName;
			$likeData ['cm.v_email'] = $searchByName;
		}
		if(!empty($request->post('search_country'))){
			$whereData['cm.i_country_id'] =  (int)Wild_tiger::decode( trim($request->post('search_country')) );
		}
		if(!empty($request->post('search_status'))){
			$whereData['cm.t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 );
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'company-master/company-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateMasterStatus($request,$this->tableName,trans('messages.company'));
	
		}
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_COMPANY')) != true){
			$this->ajaxResponse(101, trans('messages.access-denied'));
		}
		if(!empty($request->input())){
	
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
	
			return $this->removeRecord($this->tableName, $recordId, trans('messages.company') );
	
		}
	}
	public function checkUniqueCompanyName(Request $request){
	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'company_name' => [ 'required' , new UniqueCompanyName($recordId) ]  ,
		], [
				'company_name.required' => __ ( 'messages.require-company-name' ),
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
	public function checkUniqueCompanyCode(Request $request){
	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'company_code' => [ 'required' , new UniqueCompanyCode($recordId) ]  ,
		], [
				'company_code.required' => __ ( 'messages.require-company-code' ),
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
	public function checkUniqueCompanyShortCode(Request $request){
	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'company_short_code' => [ 'required' , new UniqueCompanyShortCode($recordId) ]  ,
		], [
				'company_short_code.required' => __ ( 'messages.require-company-short-code' ),
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
	
}
