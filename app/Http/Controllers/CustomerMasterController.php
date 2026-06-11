<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CustomerMasterModel;
use App\Helpers\Twt\Wild_tiger;
use DB;
use App\Rules\UniqueCustomerName;
use App\Rules\UniqueCustomerCode;

class CustomerMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.CUSTOMER_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'customer-master/';
		$this->moduleName = trans('messages.customer');
		$this->crudModel = new CustomerMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.CUSTOMER_MASTER_URL');
	
	} 
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_CUSTOMER')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.customer-master');
		$page = $this->defaultPage;
	
		#store pagination data array
		$whereData = $paginationData = [];
		$whereData['group_by'] = 'cm.i_id';
	
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
	
		$countryWhere['t_is_deleted != '] = 1;
		$countryWhere['order_by']= ['v_country_name' => 'asc'];
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
	
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
	
		$data['totalRecordCount'] = $totalRecords;
		return view($this->folderName . 'customer-master')->with($data);
	
	}
	public function create() {
		if(checkPermission(config('permission_constants.ADD_CUSTOMER')) != true ){
			return redirect('access-denied');
		}
		$data = $countryWhere = [];
		$data ['pageTitle'] = trans('messages.add-customer');
		$countryWhere['t_is_active'] = 1;
		$countryWhere['t_is_deleted != '] = 1;
		$countryWhere['order_by']= ['v_country_name' => 'asc'];
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
		
		return view ( $this->folderName . 'add-customer-master' )->with ( $data );
	
	}
	function add(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_CUSTOMER')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_CUSTOMER')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['customer_partner_name'] = ['required',new UniqueCustomerName($recordId)];
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'customer_partner_name.required' => __ ( 'messages.require-customer-name' ),
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
		$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
		
		DB::beginTransaction();
		try{
			$recordData = [];
			$customerRecordCount = (!empty($request->input('customer_count')) ? (int)($request->input('customer_count')) : 1 );
			
			$allCustomerCodeDetails = [];
			$allCustomerAddressDetails = [];
			$allCustomerCountryDetails = [];
			$allCustomerContactPersonNameDetails = [];
			$allCustomerContactEmailDetails = [];
			$allCustomerContactMobileDetails = [];
			
			$recordData['v_customer_name'] =  (!empty($request->input('customer_partner_name')) ? trim($request->input('customer_partner_name')) : null );
			
			if($recordId > 0){
				$recordDetails  = $this->crudModel->getCustomerRecordDetails(['cm.i_id' => $recordId ]);
				if(!empty($recordDetails)){
					foreach ($recordDetails as $recordDetail){
						if(!empty($request->input('edit_customer_address_'.$recordDetail->customer_detail_id))){
							$customerDetail = [];
							$countryId = (!empty($request->input('edit_customer_country_'.$recordDetail->customer_detail_id)) ? $request->input('edit_customer_country_'.$recordDetail->customer_detail_id) : '');
							$customerDetail['i_country_id'] = (int)Wild_tiger::decode($countryId);
							$customerDetail['v_customer_code'] = $recordDetail->v_customer_code;
							//$customerDetail['v_customer_code'] = (!empty($request->input('edit_customer_code_'.$recordDetail->customer_detail_id)) ? $request->input('edit_customer_code_'.$recordDetail->customer_detail_id) :'');
							$customerDetail['v_customer_address'] = (!empty($request->input('edit_customer_address_'.$recordDetail->customer_detail_id)) ? $request->input('edit_customer_address_'.$recordDetail->customer_detail_id)  :'');
							$customerDetail['v_contact_person_name'] = (!empty($request->input('edit_customer_contact_person_name_'.$recordDetail->customer_detail_id)) ? $request->input('edit_customer_contact_person_name_'.$recordDetail->customer_detail_id)  : null );
							$customerDetail['v_contact_mobile'] = (!empty($request->input('edit_customer_contact_mobile_'.$recordDetail->customer_detail_id)) ? $request->input('edit_customer_contact_mobile_'.$recordDetail->customer_detail_id)  : null );
							$customerDetail['v_contact_email'] = (!empty($request->input('edit_customer_contact_email_'.$recordDetail->customer_detail_id)) ? $request->input('edit_customer_contact_email_'.$recordDetail->customer_detail_id)  : null );
							
							$allCustomerCodeDetails[] = $customerDetail['v_customer_code'];
							$allCustomerAddressDetails[] = $customerDetail['v_customer_address'];
							$allCustomerCountryDetails[] = $customerDetail['i_country_id'];
							$allCustomerContactPersonNameDetails[] = $customerDetail['v_contact_person_name'];
							$allCustomerContactEmailDetails[] = $customerDetail['v_contact_email'];
							$allCustomerContactMobileDetails[] = $customerDetail['v_contact_mobile'];
							
							$customerDetailUpdate = $this->crudModel->updateTableData( config('constants.CUSTOMER_DETAIL_TABLE') , $customerDetail , [ 'i_id' => $recordDetail->customer_detail_id] );
						}else {
							$customerRecordData['t_is_active'] = 0;
							$customerRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData ( config('constants.CUSTOMER_DETAIL_TABLE') , $customerRecordData,['i_id' => $recordDetail->customer_detail_id] );
						}
					}
				}
				$insertRecord = $recordId;
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
				$result = $this->crudModel->updateTableData( config('constants.CUSTOMER_MASTER_TABLE') , $recordData , [ 'i_id' => $recordId] );
					
			} else {
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				if( $insertRecord > 0 ){
					$result = true;
				}
		
			}
			for ($i = 1; $i <= $customerRecordCount; $i++){
					
				$rowData = [];
				$rowData['i_customer_id'] = $insertRecord;
				$rowData['i_country_id'] =(!empty($request->input('customer_country_'.$i)) ? (int)Wild_tiger::decode($request->input('customer_country_'.$i)) :0);
				//$rowData['v_customer_code'] =(!empty($request->input('customer_code_'.$i)) ? $request->input('customer_code_'.$i) :'');
				$rowData['v_customer_code'] = $this->getCustomerCode();
				$rowData['v_customer_address'] =(!empty($request->input('customer_address_'.$i)) ? $request->input('customer_address_'.$i) :'');
				$rowData['v_contact_person_name'] =(!empty($request->input('customer_contact_person_name_'.$i)) ? $request->input('customer_contact_person_name_'.$i) : null );
				$rowData['v_contact_mobile'] =(!empty($request->input('customer_contact_mobile_'.$i)) ? $request->input('customer_contact_mobile_'.$i) : null );
				$rowData['v_contact_email'] =(!empty($request->input('customer_contact_email_'.$i)) ? $request->input('customer_contact_email_'.$i) : null );
					
				if( (!empty($rowData ['i_country_id'])) && (!empty($rowData ['v_customer_address'])) && (!empty($rowData ['v_customer_address']))){
					
					$allCustomerCodeDetails[] = $rowData['v_customer_code'];
					$allCustomerAddressDetails[] = $rowData['v_customer_address'];
					$allCustomerCountryDetails[] = $rowData['i_country_id'];
					$allCustomerContactPersonNameDetails[] = $rowData['v_contact_person_name'];
					$allCustomerContactEmailDetails[] = $rowData['v_contact_email'];
					$allCustomerContactMobileDetails[] = $rowData['v_contact_mobile'];
					
					$insertCustomerDetail = $this->crudModel->insertTableData( config('constants.CUSTOMER_DETAIL_TABLE') , $rowData);
						
				}
		
			}
			
			$additionalCustomerDetail = [];
			$additionalCustomerDetail['v_customer_codes'] = (!empty(array_filter( $allCustomerCodeDetails ) ) ? implode("," , array_filter ( $allCustomerCodeDetails ) ) : null );
			$additionalCustomerDetail['v_customer_address'] = (!empty(array_filter( $allCustomerAddressDetails ) ) ? implode("," , array_filter ( $allCustomerAddressDetails ) ) : null );
			$additionalCustomerDetail['v_customer_country_ids'] = (!empty(array_filter( $allCustomerCountryDetails ) ) ? implode("," , array_filter ( $allCustomerCountryDetails ) ) : null );
			$additionalCustomerDetail['v_customer_contact_person_names'] = (!empty(array_filter( $allCustomerContactPersonNameDetails ) ) ? implode("," , array_filter ( $allCustomerContactPersonNameDetails ) ) : null );
			$additionalCustomerDetail['v_customer_contact_emails'] = (!empty(array_filter( $allCustomerContactEmailDetails ) ) ? implode("," , array_filter ( $allCustomerContactEmailDetails ) ) : null );
			$additionalCustomerDetail['v_customer_contact_mobiles'] = (!empty(array_filter( $allCustomerContactMobileDetails ) ) ? implode("," , array_filter ( $allCustomerContactMobileDetails ) ) : null );
			
			//echo "<pre>";print_r($request->all());
			
			//echo "<pre>";print_r($additionalCustomerDetail);die;
			$this->crudModel->updateTableData( config('constants.CUSTOMER_MASTER_TABLE') , $additionalCustomerDetail , [ 'i_id' => $insertRecord] );
				
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
	
	private function getCustomerCode(){
	
		$getRecordCount = $this->crudModel->getSingleRecordById(config('constants.CUSTOMER_DETAIL_TABLE') , [ DB::Raw('count(i_id) as record_count ') ] );
		$count = (!empty($getRecordCount) ? ( $getRecordCount->record_count + 1 )  : 1 ); 
		$count = sprintf("%'04d", $count);
		$code = config('constants.CUSTOMER_CODE_PREFIX') . $count;
		return $code;
	
	}
	
	public function edit($id){
		if(checkPermission(config('permission_constants.EDIT_CUSTOMER')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		$data ['pageTitle'] = trans('messages.update-customer');
		if( $recordId > 0 ){
			$countryWhere = $whereData = [];
			$whereData['cm.i_id'] = $recordId;
			$recordInfo = $this->crudModel->getCustomerRecordDetails($whereData);
			$countryWhere['t_is_deleted != '] = 1;
			$countryWhere['order_by']= ['v_country_name' => 'asc'];
			$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
	
			if(count($recordInfo) > 0){
				$errorFound = false;
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				$data['recordDetails'] = (!empty($recordInfo)  ? $recordInfo : [] );
				return view ( $this->folderName . 'add-customer-master' )->with ( $data );
	
			}
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateMasterStatus($request,$this->tableName,trans('messages.customer'));
	
		}
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		$whereData['group_by'] = 'cm.i_id';
		//search record
		if (!empty($request->post('search_by_customer_name'))) {
			$searchByName = trim($request->post('search_by_customer_name'));
			$whereData['custom_function'][] = "(  cm.v_customer_name like '%".$searchByName."%' or find_in_set('$searchByName' , cm.v_customer_codes ) or  find_in_set('$searchByName' , cm.v_customer_address ) or  find_in_set('$searchByName' , cm.v_customer_contact_person_names ) or  find_in_set('$searchByName' , cm.v_customer_contact_emails ) or  find_in_set('$searchByName' , cm.v_customer_contact_mobiles )  )";
			//$likeData ['cm.v_customer_name'] = $searchByName;
			//$likeData ['cd.v_customer_code'] = $searchByName;
			///$likeData ['cd.v_customer_address'] = $searchByName;
	
	
		}
		if(!empty($request->post('search_customer_country'))){
			$countryId = (int)Wild_tiger::decode( trim($request->post('search_customer_country')) );
			//$whereData['cd.i_country_id'] =  (int)Wild_tiger::decode( trim($request->post('search_customer_country')) );
			$whereData['custom_function'][] = "( find_in_set('$countryId' , cm.v_customer_country_ids ))";
		}
		if(!empty($request->post('search_status') )){
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'customer-master/customer-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_CUSTOMER')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$customerDetailData['t_is_active'] = 0;
			$customerDetailData['t_is_deleted'] = 1;
				
			$this->crudModel->deleteTableData(  config('constants.CUSTOMER_DETAIL_TABLE') ,  $customerDetailData , [ 'i_customer_id' => $recordId ] );
			return $this->removeRecord($this->tableName, $recordId, trans('messages.customer') );
	
		}
	}
	public function checkUniqueCustomerName(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		
		$validator = Validator::make ( $request->all (), [
				'customer_partner_name' => [ 'required' , new UniqueCustomerName($recordId) ]  ,
		], [
				'customer_partner_name.required' => __ ( 'messages.require-customer-name' ),
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
	public function checkUniqueCustomerCode(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'customer_code' => [ 'required' , new UniqueCustomerCode($recordId) ]  ,
		], [
				'customer_code.required' => __ ( 'messages.require-customer-code' ),
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
