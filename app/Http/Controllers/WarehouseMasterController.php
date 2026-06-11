<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\WarehouseMasterModel;
use App\Helpers\Twt\Wild_tiger;
//use App\Rules\UniqueWarehouseName;
use App\Rules\UniqueWarehouseCode;
use DB;

class WarehouseMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.WAREHOUSE_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'warehouse-master/';
		$this->moduleName = trans('messages.warehouse');
		$this->crudModel = new WarehouseMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.WAREHOUSE_MASTER_URL');
	
	}
	public function index(){
		$data = [];
		$data ['pageTitle'] = trans('messages.warehouse-master');
		$data['addPageTitle'] =  trans('messages.add-warehouse');
		$data['recordType'] = config('constants.WAREHOUSE');
		#store pagination data array
		$whereData = $paginationData = [];
		if(  $this->firstUriSegment == config('constants.LOCATION_MODULE_SLUG')  ){
			if(checkPermission(config('permission_constants.VIEW_LOCATION')) != true){
				return redirect('access-denied');
			}
			$whereData['wm.e_record_type'] = config('constants.LOCATION');
			$data ['pageTitle'] = trans('messages.location-master');
			$data['addPageTitle'] =  trans('messages.add-location');
			$data['recordType'] = config('constants.LOCATION');
			
		}else if(  $this->firstUriSegment == config('constants.PORT_MODULE_SLUG')  ){
			if(checkPermission(config('permission_constants.VIEW_PORT')) != true){
				return redirect('access-denied');
			}
			$whereData['wm.e_record_type'] = config('constants.PORT');
			$data ['pageTitle'] = trans('messages.port-master');
			$data['addPageTitle'] =  trans('messages.add-port');
			$data['recordType'] = config('constants.PORT');
			
		} else {
			if(checkPermission(config('permission_constants.VIEW_WAREHOUSE')) != true){
				return redirect('access-denied');
			}
			$whereData['wm.e_record_type'] = config('constants.WAREHOUSE');
		}
		
		$page = $this->defaultPage;
	
		
	
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
	
		return view($this->folderName . 'warehouse-master')->with($data);
	
	}
	public function add(Request $request){
		
		if(!empty($request->input())){
			$recordType = (!empty($request->input('record_type')) ? trim($request->input('record_type')) :'');
			if($recordType == config('constants.WAREHOUSE')){
				$moduleName = trans('messages.warehouse');
				$warehouseName = trans('messages.require-warehouse-name');
				$warehouseCode = trans('messages.require-warehouse-code');
				$warehouseAddress = trans('messages.require-warehouse-address');
				$checkEditPermissionRecord = checkPermission(config('permission_constants.EDIT_WAREHOUSE'));
				$checkAddPermissionRecord = checkPermission(config('permission_constants.ADD_WAREHOUSE'));
				
			} else if($recordType == config('constants.PORT')){
				$moduleName = trans('messages.port');
				$warehouseName = trans('messages.require-port-name');
				$warehouseCode = trans('messages.require-port-code');
				$warehouseAddress = trans('messages.require-port-address');
				$checkEditPermissionRecord = checkPermission(config('permission_constants.EDIT_PORT'));
				$checkAddPermissionRecord = checkPermission(config('permission_constants.ADD_PORT'));
			} else {
				$moduleName = trans('messages.location');
				$warehouseName = trans('messages.require-location-name');
				$warehouseCode = trans('messages.require-location-code');
				$warehouseAddress = trans('messages.require-location-address');
				$checkEditPermissionRecord = checkPermission(config('permission_constants.EDIT_LOCATION'));
				$checkAddPermissionRecord = checkPermission(config('permission_constants.ADD_LOCATION'));
			}
			
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			if($recordId > 0) {
				if($checkEditPermissionRecord != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			} else {
				if($checkAddPermissionRecord != true){
					$this->ajaxResponse(101, trans('messages.access-denied'));
				}
			}
			$formValidation =[];
			$formValidation['warehouse_name'] = ['required'];
			if(!in_array($recordType, [ config('constants.PORT') ] ) ){
				$formValidation['warehouse_code'] = ['required', new UniqueWarehouseCode($recordId, $recordType)];
			}
			
			$formValidation['warehouse_short_code'] = ['required'];
			$formValidation['select_country_name'] = ['required'];
			$checkValidation =Validator::make($request->all(),$formValidation,
					[
							'warehouse_name.required'=> $warehouseName,
							'warehouse_code.required'=> $warehouseCode,
							'warehouse_short_code.required'=> $warehouseAddress,
							'select_country_name.required'=> trans('messages.required-country-name'),
	
					]
			);
	
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $moduleName ] ) ) );
			}
			
			$successMessage =  trans('messages.success-create',['module'=> $moduleName ]);
			$errorMessages = trans('messages.error-create',['module'=> $moduleName ]);
			$result = false;
			$html=null;
			$recordData = [];
			$recordData['v_warehouse_name'] = (!empty($request->input('warehouse_name')) ? trim($request->input('warehouse_name')) :'');
			$recordData['v_warehouse_code'] = (!empty($request->input('warehouse_code')) ? trim($request->input('warehouse_code')) :'');
			$recordData['v_warehouse_short_code'] = (!empty($request->input('warehouse_short_code')) ? trim($request->input('warehouse_short_code')) :'');
			$recordData['i_country_id'] = (!empty($request->input('select_country_name')) ? (int)Wild_tiger::decode($request->input('select_country_name')) :0);
				
			if($recordType == config('constants.WAREHOUSE')){
				$recordData['v_warehouse_email'] = (!empty($request->input('warehouse_mail')) ? getCommaSeparatedFormattedString($request->input('warehouse_mail')) : null);
				$recordData['e_record_type'] = config('constants.WAREHOUSE');
			} else if ($recordType == config('constants.PORT')){
				$recordData['e_record_type'] = config('constants.PORT');
			} else {
				$recordData['e_record_type'] = config('constants.LOCATION');
			}
			
			if($recordId > 0){
				if($recordType == config('constants.WAREHOUSE')){
					$successMessage =  trans('messages.success-update',['module'=>trans('messages.warehouse')]);
					$errorMessages = trans('messages.error-update',['module'=>trans('messages.warehouse')]);
				
				} else if ($recordType == config('constants.PORT')) {
					unset($recordData['v_warehouse_code']);
					$successMessage =  trans('messages.success-update',['module'=>trans('messages.port')]);
					$errorMessages = trans('messages.error-update',['module'=>trans('messages.port')]);
					
				} else {
					$successMessage =  trans('messages.success-update',['module'=>trans('messages.location')]);
					$errorMessages = trans('messages.error-update',['module'=>trans('messages.location')]);
						
				}
					
				$result = $this->crudModel->updateTableData( $this->tableName , $recordData ,['i_id'=> $recordId] );
	
				$recordDetail = $this->crudModel->getRecordDetails( [ 'wm.i_id' => $recordId , 'singleRecord' => true  ] );
					
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'warehouse-master/single-warehouse-master')->with ( $recordInfo )->render();
					
					
			}else{
				if($recordType == config('constants.PORT')){
					$recordData['v_warehouse_code'] = $this->getWarehouseCode();
				}
				
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
	
	private function getWarehouseCode(){
	
		$getRecordCount = $this->crudModel->getSingleRecordById(config('constants.WAREHOUSE_MASTER_TABLE') , [ DB::Raw('count(i_id) as record_count ') ] , [ 'e_record_type' => config('constants.PORT') ] );
		$count = (!empty($getRecordCount) ? ( $getRecordCount->record_count + 1 )  : 1 );
		$count = sprintf("%'02d", $count);
		$code = config('constants.PORT_CODE_PREFIX') . $count;
		return $code;
	
	}
	
	public function edit(Request $request){
		
		$errorFound = true;
		$recordId = (!empty($request->input('record_id')) ? $request->input('record_id') : '' );
		$recordType = (!empty($request->input('record_type')) ? $request->input('record_type') : '' );
		$data = $countryWhere = [];
		$countryWhere['t_is_active'] = 1;
		$countryWhere['t_is_deleted != '] = 1;
		$countryWhere['order_by']= ['v_country_name' => 'asc'];
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			if($recordId > 0){
				unset($countryWhere['t_is_active']);
			}
			$recordInfo = $this->crudModel->getRecordDetails(['wm.i_id' => $recordId ,'singleRecord'=> true ]);
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
			}
		}
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
		$data['recordType'] = $recordType;
		$html = view ($this->folderName . 'add-warehouse-master')->with ( $data )->render();
		echo $html;die;
	
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		$recordType = (! empty($request->post('record_type')) ? $request->post('record_type') : '');
		//search record
		if (!empty($request->post('search_by_warehouse'))) {
			$searchByName = trim($request->post('search_by_warehouse'));
			$likeData ['wm.v_warehouse_name'] = $searchByName;
			$likeData ['wm.v_warehouse_code'] = $searchByName;
			$likeData ['wm.v_warehouse_short_code'] = $searchByName;
			if ($recordType == config('constants.WAREHOUSE')){
				$likeData ['wm.v_warehouse_email'] = $searchByName;
			}
		}
		if(!empty($request->post('search_country'))){
			$whereData['wm.i_country_id'] =  (int)Wild_tiger::decode( trim($request->post('search_country')) );
		}
		if(!empty($request->post('search_status'))){
			$whereData['wm.t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 );
		}
		if( $recordType == config('constants.LOCATION')  ){
			$whereData['wm.e_record_type'] = config('constants.LOCATION');
				
		}else if( $recordType == config('constants.PORT')  ){
			$whereData['wm.e_record_type'] = config('constants.PORT');
		} else {
			$whereData['wm.e_record_type'] = config('constants.WAREHOUSE');
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'warehouse-master/warehouse-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function updateStatus(Request $request){
		
		if(!empty($request->input())){
			$moduleName = (!empty($request->input('lookup_module_name')) ? $request->input('lookup_module_name'): '' );
			if($moduleName == config('constants.LOCATION_MASTER')){
				$updateStatus = trans('messages.location');
				
			} else if($moduleName == config('constants.PORT_MASTER')){
				$updateStatus = trans('messages.port');
			}else {
				$updateStatus = trans('messages.warehouse');
			}
			return $this->updateMasterStatus($request,$this->tableName,$updateStatus);
	
		}
	}
	public function delete(Request $request){
		
		if(!empty($request->input())){
	
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$moduleName = (!empty($request->input('delete_module_name')) ? $request->input('delete_module_name'): '' );
			if($moduleName == config('constants.LOCATION_MASTER')){
				$deleteModule = trans('messages.location');
				$deletePermission = checkPermission(config('permission_constants.DELETE_LOCATION'));
			} else if($moduleName == config('constants.PORT_MASTER')){
				$deleteModule = trans('messages.port');
				$deletePermission = checkPermission(config('permission_constants.DELETE_PORT'));
			}else {
				$deleteModule = trans('messages.warehouse');
				$deletePermission = checkPermission(config('permission_constants.DELETE_WAREHOUSE'));
			}
			if( $deletePermission != true){
				$this->ajaxResponse(101, trans('messages.access-denied'));
			}
			return $this->removeRecord($this->tableName, $recordId, $deleteModule );
	
		}
	}
	/* public function checkUniqueWarehouseName(Request $request){
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$recordType = (!empty($request->input('record_type')) ? trim($request->input('record_type')) :'');;
		
		$validator = Validator::make ( $request->all (), [
				'warehouse_name' => [ 'required' , new UniqueWarehouseName($recordId,$recordType) ]  ,
		], [
				'warehouse_name.required' => __ ( 'messages.require-warehouse-name' ),
		] );
	
		$result = [];
		$result['status_code'] = 1;
		$result['message'] = trans('messages.success');
		if ($validator->fails ()) {
	
			$result['status_code'] = 101;
			$result['message'] = trans('messages.error');
		}
		echo json_encode($result);die;
	} */
	public function checkUniqueWarehouseCode(Request $request){
	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$recordType = (!empty($request->input('record_type')) ? trim($request->input('record_type')) :'');;
		
		$validator = Validator::make ( $request->all (), [
				'warehouse_code' => [ 'required' , new UniqueWarehouseCode($recordId,$recordType) ]  ,
		], [
				'warehouse_code.required' => __ ( 'messages.require-warehouse-code' ),
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
