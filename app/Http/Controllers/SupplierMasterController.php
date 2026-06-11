<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\SupplierMasterModel;
use App\Helpers\Twt\Wild_tiger;
use DB;
use App\Rules\UniqueSupplierName;
use App\Rules\UniqueSupplierCode;
use App\Rules\UniqueSupplierRegisterCountry;
class SupplierMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.SUPPLIER_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'supplier-master/';
		$this->moduleName = trans('messages.supplier');
		$this->crudModel = new SupplierMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.SUPPLIER_MASTER_URL');
	
	} 
	public function index(){
		if(checkPermission(config('permission_constants.VIEW_SUPPLIER')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.supplier-master');
	
		$page = $this->defaultPage;
	
		#store pagination data array
		$whereData = $paginationData = [];
		$whereData['group_by'] = 'sm.i_id';
	
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
		$data['typeDetails'] = registeredCollectionInfo();

		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
	
		$data['totalRecordCount'] = $totalRecords;
	
		return view($this->folderName . 'supplier-master')->with($data);
	
	}
	public function create() {
		if(checkPermission(config('permission_constants.ADD_SUPPLIER')) != true ){
			return redirect('access-denied');
		}
		$data = $countryWhere = [];
		$data ['pageTitle'] = trans('messages.add-supplier');
		$countryWhere['t_is_active'] = 1;
		$countryWhere['t_is_deleted != '] = 1;
		$countryWhere['order_by']= ['v_country_name' => 'asc'];
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
		$data['registeredCollectionInfo'] = registeredCollectionInfo();
		return view ( $this->folderName . 'add-supplier-master' )->with ( $data );
	
	
	}
	function add(Request $request){
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_SUPPLIER')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_SUPPLIER')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['supplier_partner_name'] = ['required', new UniqueSupplierName($recordId)];
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'supplier_partner_name.required' => __ ( 'messages.require-supplier-name' ),
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		$result = false;
		$successMessage =  trans('messages.success-create',['module'=>$this->moduleName]);
		$errorMessages = trans('messages.error-create',['module'=>$this->moduleName]);
		
		DB::beginTransaction();
		try{
			$recordData = [];
			$supplierRecordCount = (!empty($request->input('supplier_count')) ? (int)($request->input('supplier_count')) : 1 );
			
			$allSupplierCodeDetails = [];
			$allSupplierAddressDetails = [];
			$allSupplierCountryDetails = [];
			$allSupplierContactPersonNameDetails = [];
			$allSupplierContactEmailDetails = [];
			$allSupplierContactMobileDetails = [];
			
			
			$recordData['v_supplier_name'] =  (!empty($request->input('supplier_partner_name')) ? trim($request->input('supplier_partner_name')) : null );
			
			if($recordId > 0){
				$recordDetails  = $this->crudModel->getSupplierRecordDetails(['sm.i_id' => $recordId ]);
				if(!empty($recordDetails)){
					foreach ($recordDetails as $recordDetail){
						if(!empty($request->input('edit_supplier_address_'.$recordDetail->supplier_detail_id))){
							$supplierDetail = [];
							$countryId = (!empty($request->input('edit_supplier_country_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_supplier_country_'.$recordDetail->supplier_detail_id) : '');
							$supplierDetail['i_country_id'] = (int)Wild_tiger::decode($countryId);
							$supplierDetail['v_supplier_code'] = $recordDetail->v_supplier_code;
							//$supplierDetail['v_supplier_code'] = (!empty($request->input('edit_supplier_code_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_supplier_code_'.$recordDetail->supplier_detail_id) :'');
							$supplierDetail['v_supplier_address'] = (!empty($request->input('edit_supplier_address_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_supplier_address_'.$recordDetail->supplier_detail_id)  :'');
							$supplierDetail['v_contact_person_name'] = (!empty($request->input('edit_supplier_contact_person_name_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_supplier_contact_person_name_'.$recordDetail->supplier_detail_id)  : null );
							$supplierDetail['v_contact_mobile'] = (!empty($request->input('edit_supplier_contact_mobile_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_supplier_contact_mobile_'.$recordDetail->supplier_detail_id)  : null );
							$supplierDetail['v_contact_email'] = (!empty($request->input('edit_supplier_contact_email_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_supplier_contact_email_'.$recordDetail->supplier_detail_id)  : null );
							$supplierDetail['e_record_status'] = (!empty($request->input('edit_registered_collection_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_registered_collection_'.$recordDetail->supplier_detail_id) : null );
							$supplierDetail['v_timings'] = (!empty($request->input('edit_timings_'.$recordDetail->supplier_detail_id)) ? $request->input('edit_timings_'.$recordDetail->supplier_detail_id) : null );
							
							$allSupplierCodeDetails[] = $supplierDetail['v_supplier_code'];
							$allSupplierAddressDetails[] = $supplierDetail['v_supplier_address'];
							$allSupplierCountryDetails[] = $supplierDetail['i_country_id'];
							$allSupplierContactPersonNameDetails[] = $supplierDetail['v_contact_person_name'];
							$allSupplierContactEmailDetails[] = $supplierDetail['v_contact_email'];
							$allSupplierContactMobileDetails[] = $supplierDetail['v_contact_mobile'];
							
							
							$supplierDetailUpdate = $this->crudModel->updateTableData( config('constants.SUPPLIER_DETAIL_TABLE') , $supplierDetail , [ 'i_id' => $recordDetail->supplier_detail_id] );
						}else {
							$supplierRecordData['t_is_active'] = 0;
							$supplierRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData ( config('constants.SUPPLIER_DETAIL_TABLE') , $supplierRecordData,
									['i_id' => $recordDetail->supplier_detail_id] );
						}
					}
				}
				
				$insertRecord = $recordId;
				$successMessage =  trans('messages.success-update',['module'=>$this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=>$this->moduleName]);
				$result = $this->crudModel->updateTableData( config('constants.SUPPLIER_MASTER_TABLE') , $recordData , [ 'i_id' => $recordId] );
					
			} else {
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				if( $insertRecord > 0 ){
				 	$result = true;
				}
				
			}
			for ($i = 1; $i <= $supplierRecordCount; $i++){
			
				$rowData = [];
				$rowData['i_supplier_id'] = $insertRecord;
				$rowData['i_country_id'] =(!empty($request->input('supplier_country_'.$i)) ? (int)Wild_tiger::decode($request->input('supplier_country_'.$i)) :0);
				//$rowData['v_supplier_code'] =(!empty($request->input('supplier_code_'.$i)) ? $request->input('supplier_code_'.$i) :'');
				$rowData['v_supplier_code'] = $this->getSupplierCode();
				$rowData['v_supplier_address'] =(!empty($request->input('supplier_address_'.$i)) ? $request->input('supplier_address_'.$i) :'');
				$rowData['v_contact_person_name'] =(!empty($request->input('supplier_contact_person_name_'.$i)) ? $request->input('supplier_contact_person_name_'.$i) : null );
				$rowData['v_contact_mobile'] =(!empty($request->input('supplier_contact_mobile_'.$i)) ? $request->input('supplier_contact_mobile_'.$i) : null );
				$rowData['v_contact_email'] =(!empty($request->input('supplier_contact_email_'.$i)) ? $request->input('supplier_contact_email_'.$i) : null );
				$rowData['e_record_status'] = (!empty($request->input('registered_collection_'.$i)) ? $request->input('registered_collection_'.$i) : null );
				$rowData['v_timings'] = (!empty($request->input('timings_'.$i)) ? $request->input('timings_'.$i) : null );
				
				 if( (!empty($rowData ['i_country_id'])) && (!empty($rowData ['v_supplier_code'])) && (!empty($rowData ['v_supplier_address']))){
				 	
				 	$allSupplierCodeDetails[] = $rowData['v_supplier_code'];
					$allSupplierAddressDetails[] = $rowData['v_supplier_address'];
					$allSupplierCountryDetails[] = $rowData['i_country_id'];
					$allSupplierContactPersonNameDetails[] = $rowData['v_contact_person_name'];
					$allSupplierContactEmailDetails[] = $rowData['v_contact_email'];
					$allSupplierContactMobileDetails[] = $rowData['v_contact_mobile'];
							
				 	
					$insertSupplierDetail = $this->crudModel->insertTableData( config('constants.SUPPLIER_DETAIL_TABLE') , $rowData);
					
				} 
				
			}
			
			$additionalSupplierDetail = [];
			$additionalSupplierDetail['v_supplier_codes'] = (!empty(array_filter( $allSupplierCodeDetails ) ) ? implode("," , array_filter ( $allSupplierCodeDetails ) ) : null );
			$additionalSupplierDetail['v_supplier_address'] = (!empty(array_filter( $allSupplierAddressDetails ) ) ? implode("," , array_filter ( $allSupplierAddressDetails ) ) : null );
			$additionalSupplierDetail['v_supplier_country_ids'] = (!empty(array_filter( $allSupplierCountryDetails ) ) ? implode("," , array_filter ( $allSupplierCountryDetails ) ) : null );
			$additionalSupplierDetail['v_supplier_contact_person_names'] = (!empty(array_filter( $allSupplierContactPersonNameDetails ) ) ? implode("," , array_filter ( $allSupplierContactPersonNameDetails ) ) : null );
			$additionalSupplierDetail['v_supplier_contact_emails'] = (!empty(array_filter( $allSupplierContactEmailDetails ) ) ? implode("," , array_filter ( $allSupplierContactEmailDetails ) ) : null );
			$additionalSupplierDetail['v_supplier_contact_mobiles'] = (!empty(array_filter( $allSupplierContactMobileDetails ) ) ? implode("," , array_filter ( $allSupplierContactMobileDetails ) ) : null );
				
			$this->crudModel->updateTableData( config('constants.SUPPLIER_MASTER_TABLE') , $additionalSupplierDetail , [ 'i_id' => $insertRecord] );
			
			$result = true;
		}catch(\Exception $e){
			DB::rollback();
			var_dump($e->getMessage());die;
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
		
		dd($request->all());
	}
	
	private function getSupplierCode(){
	
		$getRecordCount = $this->crudModel->getSingleRecordById(config('constants.SUPPLIER_DETAIL_TABLE') , [ DB::Raw('count(i_id) as record_count ') ] );
		$count = (!empty($getRecordCount) ? ( $getRecordCount->record_count + 1 )  : 1 );
		$count = sprintf("%'04d", $count);
		$code = config('constants.SUPPLIER_CODE_PREFIX') . $count;
		return $code;
	
	}
	
	public function edit($id){
		if(checkPermission(config('permission_constants.EDIT_SUPPLIER')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		$data ['pageTitle'] = trans('messages.update-supplier');
		if( $recordId > 0 ){
			$countryWhere = $whereData = [];
			$whereData['sm.i_id'] = $recordId;
			$recordInfo = $this->crudModel->getSupplierRecordDetails($whereData);
			$countryWhere['t_is_deleted != '] = 1;
			$countryWhere['order_by']= ['v_country_name' => 'asc'];
			$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
			$data['registeredCollectionInfo'] = registeredCollectionInfo();
			if(count($recordInfo) > 0){
				$errorFound = false;
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				$data['recordDetails'] = (!empty($recordInfo)  ? $recordInfo : [] );
				return view ( $this->folderName . 'add-supplier-master' )->with ( $data );
	
			}
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateMasterStatus($request,$this->tableName,trans('messages.supplier'));
	
		}
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		$whereData['group_by'] = 'sm.i_id';
		//search record
		if (!empty($request->post('search_by_supplier_name'))) {
			$searchByName = trim($request->post('search_by_supplier_name'));
			$whereData['custom_function'][] = "( sm.v_supplier_name like '%".$searchByName."%' or find_in_set('$searchByName' , sm.v_supplier_codes ) or  find_in_set('$searchByName' , sm.v_supplier_address ) or  find_in_set('$searchByName' , sm.v_supplier_contact_person_names ) or  find_in_set('$searchByName' , sm.v_supplier_contact_emails ) or  find_in_set('$searchByName' , sm.v_supplier_contact_mobiles )  )";
			//$likeData ['sm.v_supplier_name'] = $searchByName;
			//$likeData ['sd.v_supplier_code'] = $searchByName;
			//$likeData ['sd.v_supplier_address'] = $searchByName;
				
	
		}
		if(!empty($request->post('search_supplier_country'))){
			$countryId = (int)Wild_tiger::decode( trim($request->post('search_supplier_country')) );
		 	//$whereData['sd.i_country_id'] =  (int)Wild_tiger::decode( trim($request->post('search_supplier_country')) );
		 	$whereData['custom_function'][] = "( find_in_set('$countryId' , sm.v_supplier_country_ids ))";
		} 
		if(!empty($request->post('search_type'))){
			$type = ( trim($request->post('search_type')) );
			$whereData['sd.e_record_status'] = $type;
		}
		if(!empty($request->post('search_status'))){
			$whereData['sm.t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 );
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'supplier-master/supplier-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_SUPPLIER')) != true ){
			return redirect('access-denied');
		}
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$supplierDetailData['t_is_active'] = 0;
			$supplierDetailData['t_is_deleted'] = 1;
			
			$this->crudModel->deleteTableData(  config('constants.SUPPLIER_DETAIL_TABLE') ,  $supplierDetailData , [ 'i_supplier_id' => $recordId ] );
			return $this->removeRecord($this->tableName, $recordId, trans('messages.supplier') );
	
		}
	}
	public function checkUniqueSupplierName(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		
		$validator = Validator::make ( $request->all (), [
				'supplier_partner_name' => [ 'required' , new UniqueSupplierName($recordId) ]  ,
		], [
				'supplier_partner_name.required' => __ ( 'messages.require-supplier-name' ),
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
	public function checkUniqueSupplierCode(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'supplier_code' => [ 'required' , new UniqueSupplierCode($recordId) ]  ,
		], [
				'supplier_code.required' => __ ( 'messages.require-supplier-code' ),
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
	
	public function checkUniqueSupplierType(Request $request){
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$countryRecordId = (!empty($request->country_id) ? ($request->country_id) : "" );
		$registeredCollection = (!empty($request->registered_collection) ? ($request->registered_collection) : "" );
		
		if((!empty($registeredCollection)) && ($registeredCollection == config('constants.REGISTERED_STATUS'))){
			$validator = Validator::make ( $request->all (), [
					'country_id' => [ 'required' , new UniqueSupplierRegisterCountry($recordId,$registeredCollection) ]  ,
			], [
					'country_id.required' => __ ( 'messages.require-supplier-country' ),
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
}
