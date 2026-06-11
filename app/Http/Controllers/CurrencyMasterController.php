<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CurrencyMasterModel;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueCurrencyCode;
class CurrencyMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.CURRENCY_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'currency-master/';
		$this->moduleName = trans('messages.currency');
		$this->crudModel = new CurrencyMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.CURRENCY_MASTER_URL');
	
	}
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_CURRENCY')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.currency-master');
	
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
	
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
	
		$data['totalRecordCount'] = $totalRecords;
	
		return view($this->folderName . 'currency-master')->with($data);
	
	}
	public function add(Request $request){
	
		if(!empty($request->input())){
			
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			if($recordId > 0) {
				if(checkPermission(config('permission_constants.EDIT_CURRENCY')) != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			} else {
				if(checkPermission(config('permission_constants.ADD_CURRENCY')) != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			}
			$formValidation =[];
			$formValidation['currency_name'] = ['required'];
			$formValidation['currency_code'] = ['required', new UniqueCurrencyCode($recordId)];
			$formValidation['gbp_conversation_rate'] = ['required'];
			
			$checkValidation =Validator::make($request->all(),$formValidation,
					[
							'currency_name.required'=>trans('messages.require-currency-name'),
							'currency_code.required'=>trans('messages.require-currency-symbol'),
							'gbp_conversation_rate.required'=>trans('messages.require-gbp-conversation-rate'),
	
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
			
			$recordData['v_currency_name'] = (!empty($request->input('currency_name')) ? trim($request->input('currency_name')) :'');
			$recordData['v_currency_code'] = (!empty($request->input('currency_code')) ? trim($request->input('currency_code')) :'');
			$recordData['d_gbp_conversation_rate'] = (!empty($request->input('gbp_conversation_rate')) ? trim($request->input('gbp_conversation_rate')) :'');
			
			if($recordId > 0){
					
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
					
				$result = $this->crudModel->updateTableData( $this->tableName , $recordData ,['i_id'=> $recordId] );
				$recordDetail = $this->crudModel->getRecordDetails( [ 'i_id' => $recordId , 'singleRecord' => true  ] );
					
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'currency-master/single-currency-master')->with ( $recordInfo )->render();
					
					
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
			if(checkPermission(config('permission_constants.EDIT_CURRENCY')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_CURRENCY')) != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
		}
		$data = [];
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$recordInfo = $this->crudModel->getRecordDetails(['i_id' => $recordId ,'singleRecord'=> true ]);
	
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
			}
		}
		
		$html = view ($this->folderName . 'add-currency-master')->with ( $data )->render();
		echo $html;die;
	
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		//search record
		if (!empty($request->post('search_by_currency'))) {
			$searchByName = trim($request->post('search_by_currency'));
			$likeData ['v_currency_name'] = $searchByName;
			$likeData ['v_currency_code'] = $searchByName;
		}
	
		if(!empty($request->post('search_status'))){
			$whereData['t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 );
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
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'currency-master/currency-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateMasterStatus($request,$this->tableName,trans('messages.currency'));
	
		}
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_CURRENCY')) != true){
			$this->ajaxResponse(101, trans('messages.access-denied'));
		}
		if(!empty($request->input())){
	
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
	
			return $this->removeRecord($this->tableName, $recordId, trans('messages.currency') );
	
		}
	}
	public function checkUniqueCurrencyCode(Request $request){
	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'currency_code' => [ 'required' , new UniqueCurrencyCode($recordId) ]  ,
		], [
				'currency_code.required' => __ ( 'messages.require-currency-symbol' ),
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
