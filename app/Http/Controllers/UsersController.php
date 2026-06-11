<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Helpers\Twt\Wild_tiger;
use App\Users;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Rules\UniqueEmail;
use App\Rules\UniqueSalesEmail;
use App\Rules\FreeEmailCheck;
use App\Rules\InternationMobileFormat;
use App\WarehouseMasterModel;

class UsersController extends MasterController
{
    //
	public function __construct(){
		
		parent::__construct();
		$this->middleware('checklogin');
		$this->curdModel =  New Users();
		$this->moduleName = 'employee';
		$this->perPageRecord = Config::get ( 'constants.PER_PAGE' );
		$this->tableName = Config::get('constants.LOGIN_MASTER_TABLE') ;
		$this->defaultPage = Config::get ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->folderName = Config::get ( 'constants.ADMIN_FOLDER' ) . 'users/' ;
		$this->redirectUrl = config('constants.USERS_URL');
		
	}
	
	public function index(){
		
		if(checkPermission(config('permission_constants.VIEW_EMPLOYEE_MASTER')) != true){
			return redirect('access-denied');
		}
		
		$data = [];
		$data ['pageTitle'] = trans('messages.employee-master');
		
		$page = $this->defaultPage;
		
		#store pagination data array
		$whereData = $paginationData = [];
		
		#get pagination data for first page
		if($page == $this->defaultPage ){
		
			$totalRecords = count($this->curdModel->getUserDetail($whereData));
		
			$lastPage = ceil($totalRecords/$this->perPageRecord);
		
			$paginationData['current_page'] = $this->defaultPage;
		
			$paginationData['per_page'] = $this->perPageRecord;
		
			$paginationData ['last_page'] = $lastPage;
		
		}
		$whereData ['limit'] = $this->perPageRecord;
		
		$data['recordDetails'] = $this->curdModel->getUserDetail( $whereData );
		
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = $totalRecords;
		
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
			
		return view($this->folderName . 'users')->with($data);
			
	}
	
	public function filter(Request $request) {
		
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		if (!empty($request->post('search_user'))) {
			$searchByName = trim($request->post('search_user'));
			$likeData ['lm.v_name']  = $searchByName;
			$likeData ['lm.v_mobile'] = $searchByName;
			$likeData ['lm.v_email'] = $searchByName;
		}	
		if( (!empty($request->input('search_status'))) ){
			$whereData['lm.t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 );
		}
		if( (!empty($request->input('search_role'))) ){
			$searchByRole = trim($request->post('search_role'));
			$whereData['find_in_set'] = ['lm.v_record_type',"'".$searchByRole."'"];
		}
		if(!empty($request->input('search_added_password'))){
			switch ($request->input('search_added_password')){
				case config('constants.SELECTION_YES'):
					$whereData['lm.v_password != '] = '';
					break;
				case config('constants.SELECTION_NO'):
					$whereData['lm.v_password'] = '';
					break;
			}
		}
		
		if(!empty($request->input('search_permission_given'))){
			switch ($request->input('search_permission_given')){
				case config('constants.SELECTION_YES'):
					$whereData['null_not_column'] = 'lm.v_permission';
					break;
				case config('constants.SELECTION_NO'):
					$whereData['null_column'] = 'lm.v_permission';
					break;
			}
		}
		if(!empty($request->input('search_warehouse_name'))){
			$whereData['lm.i_warehouse_id'] = (int)Wild_tiger::decode($request->input('search_warehouse_name'));
		}
		$paginationData = [];
		
		if ($page == $this->defaultPage) {
		
			$totalRecords = count($this->curdModel->getUserDetail( $whereData , $likeData ));
			
			
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
		
		$data['recordDetails'] = $this->curdModel->getUserDetail( $whereData, $likeData );
		
		$data['pagination'] = $paginationData;
		
		$data['page_no'] = $page;
		
		$data['perPageRecord'] = $this->perPageRecord;
		
		if(isset($totalRecords)){
			$data['totalRecordCount'] = $totalRecords;
		}
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'user-master/user-master-list' )->with ( $data )->render();
		
		echo $html;die;
		
	}
	
	public function create() {
	
		if(checkPermission(config('permission_constants.ADD_EMPLOYEE_MASTER')) != true){
			return redirect('access-denied');
		}
		
		$data['pageTitle'] = trans ( 'messages.add-employee');
		
		$data['allPermission'] = $this->curdModel->userPermission();
		
		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
		return view ( $this->folderName . 'add-users' )->with ( $data );
	
		
	}
	
	public function edit($id) {
		
		if(checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != true){
			return redirect('access-denied');
		}
		
		$id = (int) Wild_tiger::decode($id);
	
		if( $id > 0 ){
			$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config ( 'constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
			$whereData = [];
			$whereData['singleRecord'] = true;
			$whereData['lm.i_id'] = $id;
			$userInfo = $this->curdModel->getUserDetail (  $whereData);
			
			$data ['recordInfo'] = $userInfo;
				
			$data['pageTitle'] = trans ( 'messages.update-employee');
			
			$data['allPermission'] = $this->curdModel->userPermission();
			
			return view ( $this->folderName . 'add-users' )->with ( $data );
	
		}
	}
	
	public function add(Request $request){
		
		$formValidation = [];
		$formValidation['name'] = 'required';
		
		//$formValidation['mobile'] = [ 'required' ] ;
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_EMPLOYEE_MASTER')) != true ){
				return redirect('access-denied');
			}
		}
		
		$formValidation['email'] = [ 'required' , new UniqueEmail($recordId) ];
		//$formValidation['role'] = 'required';
		if( $recordId == 0 ){
			//$formValidation['password'] = 'required';
			//$formValidation['confirm_password'] = 'required';
		}
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'name.required' => __ ( 'messages.required-sales-person-name' ),
				'password.required' => __ ( 'messages.required-password' ),
				'confirm_password.required' => __ ( 'messages.required-confirm-password' ),
				'email.required' => __ ( 'messages.required-login-email' ),
				'mobile.required' => __ ( 'messages.required-enter-mobile' ),
				'role.required' => __ ( 'messages.required-role' ),
		] );
		
		
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$salesPersonData = [];
		
		$name = (!empty($request->input('name')) ? trim($request->input('name')) : null );
		$password = (!empty($request->input('password')) ? trim($request->input('password')) : null );
		$email = (!empty($request->input('email')) ? trim($request->input('email')) : null );
		$mobile = (!empty($request->input('mobile')) ? trim($request->input('mobile')) : null );
		$department = (!empty($request->input('department')) ? trim($request->input('department')) : null );
		$userRole = (!empty($request->input('role')) ? ($request->input('role')) : null );
		
		$loginData = [];
		$loginData['v_name'] =  $name;
		$loginData['v_email'] =  $email;
		$loginData['v_mobile'] =  $mobile;
		if(!empty($password)){
			$loginData['v_password'] =  password_hash($password, PASSWORD_DEFAULT);
		}
		
		$loginData['v_record_type'] = (!empty($userRole) ? implode(',', $userRole) : null ) ;
		$loginData['v_department'] = $department;
		if( session()->get('role') ==  config('constants.ROLE_ADMIN') ){
			$loginData['v_permission'] = ( ( !empty( $request->post ( 'permission' ) ) ) ? implode( ",", $request->post ( 'permission' ) ) : null );;
		}
		$loginData['i_warehouse_id'] = (!empty($request->post('warehouse_name')) ? (int)Wild_tiger::decode($request->post('warehouse_name')) : 0);
		$result = false;
		
		$successMessage = trans ( 'messages.success-module-create', [ 'module' => $this->moduleName ] );
		$errorMessage = trans ( 'messages.error-create', [ 'module' => $this->moduleName ] );
		
		DB::beginTransaction();
		try{
			
			if( $recordId > 0 ){
				
				$successMessage = trans ( 'messages.success-update', [ 'module' => $this->moduleName ] );
				$errorMessage = trans ( 'messages.error-update', [ 'module' => $this->moduleName ] );
				
				$this->curdModel->updateTableData( config('constants.LOGIN_MASTER_TABLE') , $loginData , [ 'i_id' =>  $recordId ] );
			} else {
				$loginData['v_role'] =  config('constants.ROLE_USER');
				$this->curdModel->insertTableData( config('constants.LOGIN_MASTER_TABLE') , $loginData );
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
		
		Wild_tiger::setFlashMessage ( 'danger', $errorMessage  );
		return redirect()->back()->withErrors ( $validator )->withInput ();
	}
	
	public function delete(Request $request){
		
		if(checkPermission(config('permission_constants.DELETE_EMPLOYEE_MASTER')) != true){
			return redirect('access-denied');
		}
		
		if(!empty($request->input())){
			
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			
			return $this->removeRecord($this->tableName, $recordId, trans('messages.employee'));
			
		}
	}
	
	public function updateStatus(Request $request){
		
		if(checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != true){
			$this->ajaxResponse( 101 , trans('messages.access-denied') );
		}
		
		if(!empty($request->input())){
		
			return $this->updateMasterStatus($request , $this->tableName,  trans('messages.employee'));
		
		}
	}
}
