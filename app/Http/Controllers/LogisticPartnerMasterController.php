<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\LogisticPartnerMasterModel;
use App\Helpers\Twt\Wild_tiger;
use DB;
use App\Rules\UniqueLogisticPartnerName;
use App\Rules\UniqueLogisticPartnerCode;

class LogisticPartnerMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->tableName = config('constants.LOGISTIC_PARTNER_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'logistic-partner-master/';
		$this->moduleName = trans('messages.logistic-partner');
		$this->crudModel = new LogisticPartnerMasterModel();
		$this->perPageRecord = config ( 'constants.PER_PAGE' );
		$this->defaultPage = config ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->redirectUrl = config('constants.LOGISTIC_PARTNER_MASTER_URL');
	
	}
	public function index(){
		
		if(checkPermission(config('permission_constants.VIEW_LOGISTIC_PARTNER')) != true){
			return redirect('access-denied');
		}
		$data = [];
		$data ['pageTitle'] = trans('messages.logistic-partner-master');
	
		$page = $this->defaultPage;
	
		#store pagination data array
		$whereData = $paginationData = [];
		$whereData['group_by'] = 'lm.i_id';
		
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
	 
		return view($this->folderName . 'logistic-partner-master')->with($data);
	
	}
	public function create() {
		if(checkPermission(config('permission_constants.ADD_LOGISTIC_PARTNER')) != true ){
			return redirect('access-denied');
		}
		$data = $countryWhere = [];
		$data ['pageTitle'] = trans('messages.add-logistic-partner');
		$countryWhere['t_is_active'] = 1;
		$countryWhere['t_is_deleted != '] = 1;
		$countryWhere['order_by']= ['v_country_name' => 'asc'];
		$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
		
		return view ( $this->folderName . 'add-logistic-partner-master' )->with ( $data );
	
	
	}
	function add(Request $request){
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		if($recordId > 0 ){
			if(checkPermission(config('permission_constants.EDIT_LOGISTIC_PARTNER')) != true ){
				return redirect('access-denied');
			}
		} else {
			if(checkPermission(config('permission_constants.ADD_LOGISTIC_PARTNER')) != true ){
				return redirect('access-denied');
			}
		}
		$formValidation = [];
		$formValidation['logistic_partner_name'] = ['required', new UniqueLogisticPartnerName($recordId)];
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'logistic_partner_name.required' => __ ( 'messages.require-logistic-partner-name' ),
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
			$logisticPartnerRecordCount = (!empty($request->input('logistic_partner_count')) ? (int)($request->input('logistic_partner_count')) : 1 );
			$recordData['v_logistic_partner_name'] =  (!empty($request->input('logistic_partner_name')) ? trim($request->input('logistic_partner_name')) : null );
			
			$allPartnerCodeDetails = [];
			$allPartnerAddressDetails = [];
			$allPartnerCountryDetails = [];
			$allPartnerContactPersonNameDetails = [];
			$allPartnerContactEmailDetails = [];
			$allPartnerContactMobileDetails = [];
			
			$logisticPartnerRecordId = 0;
			if($recordId > 0 ){
				$logisticPartnerRecordId = $recordId;
				$recordDetails  = $this->crudModel->getLogisticRecordDetails(['lm.i_id' => $recordId ]);
				
				
				
				if(!empty($recordDetails)){
					foreach ($recordDetails as $recordDetail){
						if(!empty($request->input('edit_logistic_partner_address_'.$recordDetail->logistic_detail_id))){
							$logisticDetail = [];
							$countryId = (!empty($request->input('edit_logistic_partner_country_'.$recordDetail->logistic_detail_id)) ? $request->input('edit_logistic_partner_country_'.$recordDetail->logistic_detail_id) : '');
							$logisticDetail['i_country_id'] = (int)Wild_tiger::decode($countryId);
							$logisticDetail['v_logistic_partner_code'] = $recordDetail->v_logistic_partner_code;
							//$logisticDetail['v_logistic_partner_code'] = (!empty($request->input('edit_logistic_partner_code_'.$recordDetail->logistic_detail_id)) ? $request->input('edit_logistic_partner_code_'.$recordDetail->logistic_detail_id) :'');
							$logisticDetail['v_logistic_partner_address'] = (!empty($request->input('edit_logistic_partner_address_'.$recordDetail->logistic_detail_id)) ? $request->input('edit_logistic_partner_address_'.$recordDetail->logistic_detail_id)  :'');
							$logisticDetail['v_contact_person_name'] = (!empty($request->input('edit_logistic_partner_contact_person_name_'.$recordDetail->logistic_detail_id)) ? $request->input('edit_logistic_partner_contact_person_name_'.$recordDetail->logistic_detail_id)  : null );
							$logisticDetail['v_contact_mobile'] = (!empty($request->input('edit_logistic_partner_contact_mobile_'.$recordDetail->logistic_detail_id)) ? $request->input('edit_logistic_partner_contact_mobile_'.$recordDetail->logistic_detail_id)  : null );
							$logisticDetail['v_contact_email'] = (!empty($request->input('edit_logistic_partner_contact_email_'.$recordDetail->logistic_detail_id)) ? $request->input('edit_logistic_partner_contact_email_'.$recordDetail->logistic_detail_id)  : null );
							
							
							$allPartnerCodeDetails[] = $logisticDetail['v_logistic_partner_code'];
							$allPartnerAddressDetails[] = $logisticDetail['v_logistic_partner_address'];
							$allPartnerCountryDetails[] = $logisticDetail['i_country_id'];
							$allPartnerContactPersonNameDetails[] = $logisticDetail['v_contact_person_name'];
							$allPartnerContactEmailDetails[] = $logisticDetail['v_contact_email'];
							$allPartnerContactMobileDetails[] = $logisticDetail['v_contact_mobile'];
							
							$logisticDetailUpdate = $this->crudModel->updateTableData( config('constants.LOGISTIC_PARTNER_DETAIL_TABLE') , $logisticDetail , [ 'i_id' => $recordDetail->logistic_detail_id] );
						}else {
							$logisticRecordData['t_is_active'] = 0;
							$logisticRecordData ['t_is_deleted'] = 1;
							$this->crudModel->deleteTableData ( config('constants.LOGISTIC_PARTNER_DETAIL_TABLE') , $logisticRecordData,
									['i_id' => $recordDetail->logistic_detail_id] );
						} 
					}
				}
				$insertRecord = $recordId;
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
				
				
				
				$result = $this->crudModel->updateTableData( config('constants.LOGISTIC_PARTNER_MASTER_TABLE') , $recordData , [ 'i_id' => $recordId] );
				
			} else {
				
				$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				if( $insertRecord > 0 ){
					$logisticPartnerRecordId = $insertRecord;
					$result = true;
				}
			}
			for ($i=1; $i <= $logisticPartnerRecordCount; $i++){
				$rowData = [];
				$rowData['i_logictic_partner_id'] = $insertRecord;
				$rowData['i_country_id'] =(!empty($request->input('logistic_partner_country_'.$i)) ? (int)Wild_tiger::decode($request->input('logistic_partner_country_'.$i)) :0);
				$rowData['v_logistic_partner_code'] =(!empty($request->input('logistic_partner_code_'.$i)) ? $request->input('logistic_partner_code_'.$i) :'');
				$rowData['v_logistic_partner_code'] = $this->getLogisticPartnerCode();
				$rowData['v_logistic_partner_address'] =(!empty($request->input('logistic_partner_address_'.$i)) ? $request->input('logistic_partner_address_'.$i) :'');
				$rowData['v_contact_person_name'] =(!empty($request->input('logistic_partner_contact_person_name_'.$i)) ? $request->input('logistic_partner_contact_person_name_'.$i) : null );
				$rowData['v_contact_mobile'] =(!empty($request->input('logistic_partner_contact_mobile_'.$i)) ? $request->input('logistic_partner_contact_mobile_'.$i) : null );
				$rowData['v_contact_email'] =(!empty($request->input('logistic_partner_contact_email_'.$i)) ? $request->input('logistic_partner_contact_email_'.$i) : null );
					
				if( (!empty($rowData ['i_country_id'])) && (!empty($rowData ['v_logistic_partner_code'])) && (!empty($rowData ['v_logistic_partner_address']))){
					
					$allPartnerCodeDetails[] = $rowData['v_logistic_partner_code'];
					$allPartnerAddressDetails[] = $rowData['v_logistic_partner_address'];
					$allPartnerCountryDetails[] = $rowData['i_country_id'];
					$allPartnerContactPersonNameDetails[] = $rowData['v_contact_person_name'];
					$allPartnerContactEmailDetails[] = $rowData['v_contact_email'];
					$allPartnerContactMobileDetails[] = $rowData['v_contact_mobile'];
					
					$insertLogisticDetail = $this->crudModel->insertTableData( config('constants.LOGISTIC_PARTNER_DETAIL_TABLE') , $rowData);
				}
			}
			$additionalPartnerDetail = [];
			$additionalPartnerDetail['v_partner_codes'] = (!empty($allPartnerCodeDetails) ? implode("," , $allPartnerCodeDetails ) : null );
			$additionalPartnerDetail['v_partner_address'] = (!empty($allPartnerAddressDetails) ? implode("," , $allPartnerAddressDetails ) : null );
			$additionalPartnerDetail['v_partner_country_ids'] = (!empty($allPartnerCountryDetails) ? implode("," , $allPartnerCountryDetails ) : null );
			$additionalPartnerDetail['v_partner_contact_person_names'] = (!empty(array_filter( $allPartnerContactPersonNameDetails ) ) ? implode("," , array_filter ( $allPartnerContactPersonNameDetails ) ) : null );
			$additionalPartnerDetail['v_partner_contact_emails'] = (!empty(array_filter( $allPartnerContactEmailDetails ) ) ? implode("," , array_filter ( $allPartnerContactEmailDetails ) ) : null );
			$additionalPartnerDetail['v_partner_contact_mobiles'] = (!empty(array_filter( $allPartnerContactMobileDetails ) ) ? implode("," , array_filter ( $allPartnerContactMobileDetails ) ) : null );
			
			$this->crudModel->updateTableData( config('constants.LOGISTIC_PARTNER_MASTER_TABLE') , $additionalPartnerDetail , [ 'i_id' => $logisticPartnerRecordId] );
			
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
		
		Wild_tiger::setFlashMessage ( 'danger', $errorMessage  );
		return redirect()->back()->withErrors ( $validator )->withInput ();
		
		dd($request->all());
	}
	
	
	private function getLogisticPartnerCode(){
		
		$getRecordCount = $this->crudModel->getSingleRecordById(config('constants.LOGISTIC_PARTNER_DETAIL_TABLE') , [ DB::Raw('count(i_id) as record_count ') ] );
		$count = (!empty($getRecordCount) ? ( $getRecordCount->record_count + 1 )  : 1 );
		$count = sprintf("%'03d", $count);
		$code = config('constants.LOGISTIC_PARTNER_CODE_PREFIX') . $count;
		return $code;
		
	}
	public function edit($id){
		if(checkPermission(config('permission_constants.EDIT_LOGISTIC_PARTNER')) != true ){
			return redirect('access-denied');
		}
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		$data ['pageTitle'] = trans('messages.update-logistic-partner');
		if( $recordId > 0 ){
			$countryWhere = $whereData = [];
			//$whereData['singleRecord'] = true;
			$whereData['lm.i_id'] = $recordId;
			$recordInfo = $this->crudModel->getLogisticRecordDetails($whereData);
			$countryWhere['t_is_deleted != '] = 1;
			$countryWhere['order_by']= ['v_country_name' => 'asc'];
			$data['countryRecordDetails'] = $this->crudModel->selectData(config('constants.COUNTRY_MASTER_TABLE'),['i_id','v_country_name'],$countryWhere);
			
			if(count($recordInfo) > 0){
				$errorFound = false;
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo[0] : [] );
				$data['recordDetails'] = (!empty($recordInfo)  ? $recordInfo : [] );
				return view ( $this->folderName . 'add-logistic-partner-master' )->with ( $data );
					
			}	
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateMasterStatus($request,$this->tableName,trans('messages.logistic-partner'));
	
		}
	}
	public function delete(Request $request){
		if(checkPermission(config('permission_constants.DELETE_LOGISTIC_PARTNER')) != true ){
			return redirect('access-denied');
		}
		
		if(!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$partnerdDetailData['t_is_active'] = 0;
			$partnerdDetailData['t_is_deleted'] = 1;
			$this->crudModel->deleteTableData(  config('constants.LOGISTIC_PARTNER_DETAIL_TABLE') ,  $partnerdDetailData , [ 'i_logictic_partner_id' => $recordId ] );
			return $this->removeRecord($this->tableName, $recordId, trans('messages.logistic-partner') );
	
		}
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData =  [];
			
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		$whereData['group_by'] = 'lm.i_id';
		//search record
		if (!empty($request->post('search_by_logistic_partner_name'))) {
			$searchByName = trim($request->post('search_by_logistic_partner_name'));
			//$likeData ['lm.v_logistic_partner_name'] = $searchByName;
			$whereData['custom_function'][] = "( lm.v_logistic_partner_name like '%".$searchByName."%' or find_in_set('$searchByName' , lm.v_partner_codes ) or  find_in_set('$searchByName' , lm.v_partner_address ) or  find_in_set('$searchByName' , lm.v_partner_contact_person_names ) or  find_in_set('$searchByName' , lm.v_partner_contact_emails ) or  find_in_set('$searchByName' , lm.v_partner_contact_mobiles )  )";
			//$whereData['find_in_set'] = [  'lm.v_partner_codes' , $searchByName ]  ;
			//$likeData ['ld.v_logistic_partner_code'] = $searchByName;
			///$likeData ['ld.v_logistic_partner_address'] = $searchByName;
			
				
		}
		if(!empty($request->post('search_logistic_partner_country'))){
			$countryId = (int)Wild_tiger::decode( trim($request->post('search_logistic_partner_country')) );
			//$whereData['ld.i_country_id'] =  $countryId;
			//$whereData['find_in_set'] = [  'lm.v_partner_country_ids' , $countryId ]  ;
			$whereData['custom_function'][] = "( find_in_set('$countryId' , lm.v_partner_country_ids ))";
		}  
		if(!empty($request->post('search_status'))){
			$whereData['lm.t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 );
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'logistic-partner-master/logistic-partner-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function checkUniqueLogisticPartnerName(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		
		$validator = Validator::make ( $request->all (), [
				'logistic_partner_name' => [ 'required' , new UniqueLogisticPartnerName($recordId) ]  ,
		], [
				'logistic_partner_name.required' => __ ( 'messages.require-logistic-partner-name' ),
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
	
	public function checkUniqueLogisticPartnerCode(Request $request){
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'logistic_partner_code' => [ 'required' , new UniqueLogisticPartnerCode($recordId) ]  ,
		], [
				'logistic_partner_code.required' => __ ( 'messages.require-logistic-partner-code' ),
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
