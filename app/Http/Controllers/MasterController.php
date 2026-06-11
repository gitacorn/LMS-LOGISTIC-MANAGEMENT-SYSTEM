<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Helpers\Twt\Wild_tiger;
use App\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GuestController;
use App\Helpers\Twt\Zoho_crm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Providers\ComposerServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueAmazonShimentId;




class MasterController extends GuestController
{
	public $loggedUserRole;
	public $perPageRecord;
	public $firstUriSegment;
	public $secondUriSegment;
	
	public function __construct(){
		$this->BaseModel = new BaseModel();
		$this->guestMethod = new GuestController();
		$this->middleware('checklogin');
		$allUrlSegmentDetails = (!empty( request()->segments()) ?  request()->segments() : [] );
		$this->firstUriSegment = (isset($allUrlSegmentDetails[0]) ? $allUrlSegmentDetails[0] : "" );
		$this->secondUriSegment = (isset($allUrlSegmentDetails[1]) ? $allUrlSegmentDetails[1] : "" );
		$this->todayDate = config('constants.TODAY_DATE');
		$this->todayDate = date('dmY');
	}
	
	//
    public function ajaxResponse($status , $messages , $data = [] ){
    	$result = [];
    	$result['status_code'] = $status;
    	$result['message'] = $messages;
    	if(!empty($data)){
    		$result['data'] = (!empty($data) ? $data : null );
    	}
    	echo json_encode($result);die;
    }
    
    public function updateMasterStatus( $request , $tableName , $moduleName ){
    	
    	$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
    	
    	$currentStatus = trim($request->current_status);
    	
    	$updateData = [];
    	if( strtolower( $currentStatus ) ==  strtolower ( config('constants.ENABLE_STATUS') ) ){
    		$updateStatus = trans('messages.disable');
    		$updateData['t_is_active']  = 0;
    	} else if( strtolower( $currentStatus ) ==  strtolower ( config('constants.DISABLE_STATUS') ) ){
    		$updateStatus = trans('messages.enable');
    		$updateData['t_is_active']  = 1;
    	}
    	
    	$updatedmodule =  $moduleName;
    	
    	if($updatedmodule == trans('messages.status')){
    		$message = trans ( 'messages.success-update', [ 'module' => $updatedmodule ] );
    	} else {
    		$message = trans ( 'messages.success-status-update', [ 'module' => $updatedmodule ] );
    	}
    	if(!empty($updateData)){
    	
    		$result = $this->BaseModel->updateTableData(  $tableName , $updateData , [ 'i_id' => $recordId ]);
    		
    		if( $result != false ){
    			//$message = trans ( 'messages.success-status-update', [ 'module' => $updatedmodule ] );
    			$this->ajaxResponse( 1 , $message , [ 'update_status'  =>  ( $updateStatus ) ] );
    		}
    	
    	}
    	$message = trans ( 'messages.error-status-update', [ 'module' => $updatedmodule ] );
    	$this->ajaxResponse( 101 , $message );
    	
    }
    
    public function setLoggedUserData(){
    	$this->loggedUserRole = Session::get('role');
    	
    	if( ( $this->loggedUserRole == config('constants.ROLE_SUPERADMIN')) ||  ( $this->loggedUserRole == config('constants.ROLE_TEAM')) || (  ( $this->loggedUserRole == config('constants.ROLE_PARTNER')  ) && ( session()->get('user_type') == config('constants.ADMIN_USER_TYPE')  ) ) || (  ( $this->loggedUserRole == config('constants.ROLE_DISTRIBUTOR')  ) && ( session()->get('user_type') == config('constants.ADMIN_USER_TYPE')  ) )  ){
    		$this->checkAllowedOpt = true;
    	} else {
    		$this->checkAllowedOpt = false;
    	}
    	
    }
    
    public function uploadFile( $request , $fieldName , $allowedType  = 'image' ){
    	$uploadedImagePath = '';
    	
    	$response = [];
    	$response['status'] = false;
    	
    	if($request->hasFile($fieldName)) {
    		
    		$file = $request->file($fieldName);
    		$fileMIMEType = $file->getMimeType();
    		
    		
    		$fileTypes = [];
    		$message = "";
    		switch($allowedType){
    			case 'image':
    				$fileTypes = [ 'image/jpeg' , 'image/jpg'  , 'image/png'  ];
    				$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Images"  ] );
    				break;
    			case 'image_doc_pdf_xls':
    				$fileTypes = [ 'image/jpeg' , 'image/jpg'  , 'image/png'  , 'application/pdf' , 'application/msword' , 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' , 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ];
    				$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Images, PDF, Word, Excel"  ] );
    				break;
    			case 'xls':
    				$fileTypes = [ 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ];
    				$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Excel"  ] );
    				break;
    			case 'xlsx':
    				$fileTypes = [ 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ];
    				$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Excel"  ] );
    				break;
    			case 'csv':
    				$fileTypes = [ 'application/vnd.csv' , 'text/csv' , 'text/plain' ];
    				$message = trans ( 'messages.only-csv-file-allowed', [ 'fileTypes' => "csv"  ] );
    				break;
    		}
    		
    		if(!in_array($fileMIMEType,$fileTypes)){
    			$response['message'] = $message;
    			return $response;
    		}
    		
    		Log::info(print_r($fileTypes,true));
    		
    		// Get filename with extension
    		$filenameWithExt = $request->file($fieldName)->getClientOriginalName();
    		// Get just filename
    		$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    		$filename = createSlug($filename);
    		// Get just ext
    		$extension = $request->file($fieldName)->getClientOriginalExtension();
    		//Filename to store
    		$fileNameToStore = $filename.'_'.time().'.'.$extension;
    		// Upload Image
    		$uploadedImagePath = $request->file($fieldName)->storeAs( Config::get('constants.UPLOAD_FOLDER') . $fieldName , $fileNameToStore);
    		
    		$response['status'] = true;
    		$response['filePath'] = $fieldName . DIRECTORY_SEPARATOR . $fileNameToStore;
    	}
    	
    	return $response;
    }
    
    public function uploadMultipleFile( $request , $fieldName  , $allowedType  = 'image'  ){
    	 
    	$uploadedImagePath = [];
    	$response['status'] = false;
    	if($request->hasFile($fieldName)) {
    		
    		foreach($request->file($fieldName) as $file){
    			
    			$fileMIMEType = $file->getMimeType();
    			
    			$message = "";
    			switch($allowedType){
    				case 'image':
    					$fileTypes = [ 'image/jpeg' , 'image/jpg'  , 'image/png'  ];
    					$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Images"  ] );
    					break;
    				case 'image_doc_pdf_xls':
    					$fileTypes = [ 'image/jpeg' , 'image/jpg'  , 'image/png'  , 'application/pdf' , 'application/msword' ,  'application/octet-stream' ,  'application/vnd.openxmlformats-officedocument.wordprocessingml.document' , 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ];
    					$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Images, PDF, Word, Excel"  ] );
    					break;
					case 'xls':
    					$fileTypes = [ 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ];
    					$message = trans ( 'messages.only-allowed-file-types', [ 'fileTypes' => "Excel"  ] );
    					break;
    			}
    			
    			if(!in_array($fileMIMEType,$fileTypes)){
    				$response['status'] = false;
    				$response['message'] = $message;
    				return $response;
    			}
    			
    			
    			$filenameWithExt = $file->getClientOriginalName();
    			
    			$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    			$filename = createSlug($filename);
    			
    			$extension = $file->getClientOriginalExtension();
    			
    			$fileNameToStore = $filename.'_'.time().'.'.$extension;
    			
    			$uploadedImagePath[] = $file->storeAs( Config::get('constants.UPLOAD_FOLDER') . $fieldName, $fileNameToStore);
    		}
    		
    		$response['status'] = true;
    		$response['filePath'] = $uploadedImagePath;
    	}
    	 
    	return $response;
    }
    
    public function removeRecord($tableName , $recordId , $messageModuleName ){
    	
    	if( $recordId > 0 && (!empty($tableName)) ){
    		$updateTableData = [];
    		$updateTableData['t_is_active'] = 0;
    		$updateTableData['t_is_deleted'] = 1;
    		$deletedRecord = false;
    		
    		DB::beginTransaction();
    		
    		$deletedRecord = $this->BaseModel->deleteTableData(  $tableName ,  $updateTableData , [ 'i_id' => $recordId ] );
    		
    		if( $deletedRecord != false ){
    				
    			DB::commit();
    			
    			Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-delete', [
    				'module' => $messageModuleName
				] ) );
    			
    			return redirect()->back();
    		} else {
				
				DB::rollback();
    			
    			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-delete', [
    					'module' => $messageModuleName
    			] ) );
    			
    			return redirect()->back();
    		}
    		
    	}
    	DB::rollback();
    	Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-delete', [
    			'module' => $moduleName
    	] ) );
    	return redirect()->back();
    }
    
    public function CallRaw($procName, $parameters = [], $isExecute = false)
    {
    	
    	$syntax = '';
    	for ($i = 0; $i < count($parameters); $i++) {
    		$syntax .= (!empty($syntax) ? ',' : '') . '?';
    	}
    	$syntax = 'CALL ' . $procName . '(' . $syntax . ');';
    
    	$pdo = DB::connection()->getPdo();
    	$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
    	$stmt = $pdo->prepare($syntax,[\PDO::ATTR_CURSOR=>\PDO::CURSOR_SCROLL]);
    	for ($i = 0; $i < count($parameters); $i++) {
    		$stmt->bindValue((1 + $i), $parameters[$i]);
    	}
    	$exec = $stmt->execute();
    	if (!$exec) return $pdo->errorInfo();
    	if ($isExecute) return $exec;
    
    	$results = [];
    	do {
    		try {
    			$results[] = $stmt->fetchAll(\PDO::FETCH_OBJ);
    		} catch (\Exception $ex) {
    
    		}
    	} while ($stmt->nextRowset());
    
    
    	if (1 === count($results)) return $results[0];
    	return $results;
    }
    
    public function printLastQuery(){
    	echo BaseModel::last_query();
    }
    
    public function logLastQuery(){
    	return BaseModel::last_query();
    }
    
    public function multipleSearch( $fieldData , $columnName , $condition = 'OR'){
    	$searchRegion = explode("," , $fieldData );
    	$customWhere = ' ( ';
    	foreach($searchRegion as $region){
    		$customWhere.= "find_in_set(  '".$region."' , ".$columnName." ) ".$condition." ";
    	}
    	$customWhere = rtrim($customWhere , $condition.' ');
    	$customWhere .= ' ) ';
    	return $customWhere;
    }
    public function manageSessionMessages(Request $request){
    	
    	if(!empty($request->all())){
    		$sessionModuleName = (!empty($request->input('session_redirect_module_name')) ? trim($request->input('session_redirect_module_name')) : "" );
    		
    		if(!empty($sessionModuleName)){
    			$successMessage =  trans ( 'messages.success-create', [ 'module' => $sessionModuleName ] );
    			Wild_tiger::setFlashMessage ( 'success', $successMessage  );
    		}
    	}
    	return redirect()->back();
    }
    
    public function generateSpreadsheet( $exportInfo , $breakColumnArray = []){
    	require_once 'vendor/autoload.php';
    
    	$recordDetails = (!empty($exportInfo['record_detail']) ? $exportInfo['record_detail'] : [] );
    
    	$objPHPExcel = new Spreadsheet ();
    	$objPHPExcel->setActiveSheetIndex ( 0 );
    	if(!empty($exportInfo['title'])){
    		$objPHPExcel->getActiveSheet()->setTitle($exportInfo['title']);
    	}
    
    	$rowCount = 1;
    
    	$excelRows = Wild_tiger::DefaultExcelRow ();
    	$getHeaderData = array_keys($recordDetails[0]);
    
    	$headercolumnWithKey = array_keys($recordDetails[0]);
    
    	foreach ( $getHeaderData as $key => $header ) {
    
    		if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    			$columnValue = $excelRows[ $key ];
    			$objPHPExcel->getActiveSheet()->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    		}
    
    
    		$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$key] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    	}
    	$rowCount++;
    
    	foreach($recordDetails as $k => $v)
    	{
    		$col = 1;
    		foreach ($headercolumnWithKey as $field)
    		{
    			$value = $v[$field];
    			//$value = ( is_float( $value ) != false ?  twoDigitAmount($value)  : $value ) ;
    			$value = str_replace('\n', "\n", $value);
    			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,  $value );
    
    			$col++;
    
    		}
    		$rowCount++;
    		 
    	}
    
    	foreach($excelRows as $excelRow) {
    		$objPHPExcel->getActiveSheet()->getColumnDimension($excelRow)->setAutoSize(true);
    	}
    
    	$objPHPExcel->getActiveSheet()->getStyle("1")->getFont()->setBold(true);
    	//$objPHPExcel->getActiveSheet()->getStyle("2")->getFont()->setBold(true);
    
    
    
    	$style = array(
    			'alignment' => array(
    					//'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    			)
    	);
    
    	$objPHPExcel->getActiveSheet()->getStyle("2")->applyFromArray($style);
    	$objPHPExcel->getActiveSheet()->getStyle("1")->applyFromArray($style);
    	$objPHPExcel->getDefaultStyle()->applyFromArray($style);
    
    	$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
    
    
    	ob_start ();
    	//$objWriter->save ( "php://output" );
    	$writer->save('php://output');
    	$xlsData = ob_get_contents ();
    	ob_end_clean ();
    
    	return $xlsData;
    }
    
    public function generateSpreadsheetMultiple( $exportInfo , $breakColumnArray = []){
    	require_once 'vendor/autoload.php';
    
    	$multiRecordDetails = (!empty($exportInfo['record_detail']) ? $exportInfo['record_detail'] : [] );
    
    	$objPHPExcel = new Spreadsheet ();
    	 
    	//First sheet
    	//$sheet = $objPHPExcel->getActiveSheet();
    	 
    	$excelTitle = array_keys($multiRecordDetails);
    	 
    	if(array_key_exists('Summary', $multiRecordDetails)){
    		if(isset($excelTitle) && !empty($excelTitle) && isset($excelTitle[0]) && !empty($excelTitle[0])){
    			$objPHPExcel->getActiveSheet()->setTitle($excelTitle[0]);
    		}
    
    		// Default summary column
    		$recordDetails = $multiRecordDetails['Summary'];
    
    		$rowCount = 1;
    
    		$excelRows = Wild_tiger::DefaultExcelRow ();
    		$getHeaderData = array_keys($recordDetails[0]);
    
    		$headercolumnWithKey = array_keys($recordDetails[0]);
    
    		foreach ( $excelTitle as $key => $header ) {
    
    			if(isset($header) && !empty($header) && $header != 'Summary'){
    				if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    					$columnValue = $excelRows[ $key ];
    					$objPHPExcel->getActiveSheet()->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    				}
    
    				if($key != 1){
    					$keyExcel = $keyExcel + (isset($exportInfo) && !empty($exportInfo) && isset($exportInfo['summary_common_header_gap']) && !empty($exportInfo['summary_common_header_gap']) ? $exportInfo['summary_common_header_gap'] : 1);
    				} else {
    					$keyExcel = $key;
    				}
    				
    				$actualExcelRowIndex = $keyExcel - 1;
    				
    				$rowMergerGroup = $excelRows[$actualExcelRowIndex] . '1:' .$excelRows [($actualExcelRowIndex + (isset($exportInfo) && !empty($exportInfo) && isset($exportInfo['summary_common_header_gap']) && !empty($exportInfo['summary_common_header_gap']) ? $exportInfo['summary_common_header_gap'] : 1) - 1)] . '1';
    				
    				$objPHPExcel->getActiveSheet()->mergeCells($rowMergerGroup);
    				$objPHPExcel->getActiveSheet()->getStyle($rowMergerGroup)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    				$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$actualExcelRowIndex] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    
    				$keyExcel = $keyExcel + 1;
    				$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$keyExcel] . $rowCount,  '' );
    			}
    		}
    		$rowCount++;
    
    		$keyExcel = $extracolAdded = 0;
    		$extracol = 1;
    		foreach ( $getHeaderData as $key => $header ) {
    
    			if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    				$columnValue = $excelRows[ $key ];
    				$objPHPExcel->getActiveSheet()->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    			}
    	   
    			$valueExplode = explode('__', $header);
    			$header = isset($valueExplode[0]) && !empty($valueExplode[0]) ? $valueExplode[0] : $header;
    	   
    			if($extracol > (isset($exportInfo) && !empty($exportInfo) && isset($exportInfo['summary_common_header_gap']) && !empty($exportInfo['summary_common_header_gap']) ? $exportInfo['summary_common_header_gap'] : 1)){
    				$keyExcel = $key + $extracolAdded;
    				$extracol = 1;
    				$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$keyExcel] . $rowCount,  '' );
    				$objPHPExcel->getActiveSheet()->getStyle($excelRows [$keyExcel] . $rowCount)->getFill()->getStartColor()->setARGB('ffffff');
    				$extracolAdded++;
    			}
    	   
    			if($extracolAdded > 0){
    				$key = $key + $extracolAdded;
    			}
    	   
    			$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$key] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows [$key] . $rowCount)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    	   
    			$extracol++;
    		}
    		$rowCount++;
    		
    		foreach($recordDetails as $k => $v)
    		{
    			$col = $extracol = 1;
    			foreach ($headercolumnWithKey as $field)
    			{
    				$value = $v[$field];
    				//$value = ( is_float( $value ) != false ?  twoDigitAmount($value)  : $value ) ;
    				$value = str_replace('\n', "\n", $value);
    				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,  $value );
    				
    				$objPHPExcel->getActiveSheet()->getStyle($excelRows [($col - 1)] . $rowCount)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    				if(str_contains($field, 'total_amount_(gbp)')){
    					$objPHPExcel->getActiveSheet()->getStyle($excelRows [($col - 1)] . $rowCount)->getFont()->setBold(true);
    				}
    				
    				if($value == 'Total'){
    					$objPHPExcel->getActiveSheet()->getStyle($rowCount)->getFont()->setBold(true);
    				}
    				
    				$col++;
    				$extracol++;
    				
    
    				if($extracol > (isset($exportInfo) && !empty($exportInfo) && isset($exportInfo['summary_common_header_gap']) && !empty($exportInfo['summary_common_header_gap']) ? $exportInfo['summary_common_header_gap'] : 1)){
    					$extracol = 1;
    					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,  '' );
    					$col++;
    				}
    				
    	    
    			}
    			$rowCount++;
    		}
    		
    		 
    		foreach($excelRows as $excelRow) {
    			$objPHPExcel->getActiveSheet()->getColumnDimension($excelRow)->setAutoSize(true);
    		}
    		 
    		$objPHPExcel->getActiveSheet()->getStyle("1")->getFont()->setBold(true);
    		$objPHPExcel->getActiveSheet()->getStyle("2")->getFont()->setBold(true);
    		 
    		 
    		 
    		$style = array(
    				'alignment' => array(
    						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    				)
    		);
    		
    		 
    		$objPHPExcel->getActiveSheet()->getStyle("2")->applyFromArray($style);
    		$objPHPExcel->getActiveSheet()->getStyle("1")->applyFromArray($style);
    		$objPHPExcel->getDefaultStyle()->applyFromArray($style);
    
    		$objPHPExcel->getActiveSheet()->getStyle("2")
    		->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    		$objPHPExcel->getActiveSheet()->getStyle("2")
    		->getFill()->getStartColor()->setARGB('d9e1f2');
    
    
    		$objPHPExcel->getActiveSheet()->getStyle("1")
    		->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    		$objPHPExcel->getActiveSheet()->getStyle("1")
    		->getFill()->getStartColor()->setARGB('d9e1f2');
    
    		$lastRaw = $rowCount - 1;
    
    		$objPHPExcel->getActiveSheet()->getStyle("$lastRaw")
    		->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    		$objPHPExcel->getActiveSheet()->getStyle("$lastRaw")
    		->getFill()->getStartColor()->setARGB('e2efda');
    	}   	 
    	 
    	 
    	//Start adding next sheets
    	$i=1;
    	while ($i < count($multiRecordDetails)) {
    		 
    		// Add new sheet
    		$objWorkSheet = $objPHPExcel->createSheet($i); //Setting index when creating
    
    		// Rename sheet
    		$sheetTitle = isset($excelTitle) && !empty($excelTitle) && isset($excelTitle[$i]) && !empty($excelTitle[$i]) ? $excelTitle[$i] : '';
    		
    		$additionSummaryDetails = (isset($exportInfo) && !empty($exportInfo) && isset($exportInfo['additional_summary']) && !empty($exportInfo['additional_summary']) && isset($exportInfo['additional_summary'][$sheetTitle]) && !empty($exportInfo['additional_summary'][$sheetTitle]) ? $exportInfo['additional_summary'][$sheetTitle] : []);
    
    		$recordDetails = $multiRecordDetails[$sheetTitle];
    
    		$rowCount = 1;
    		$excelRows = Wild_tiger::DefaultExcelRow ();
    
    		$getHeaderData = array_keys($recordDetails[0]);
    		$headercolumnWithKey = array_keys($recordDetails[0]);
    	   
    		foreach ( $getHeaderData as $key => $header ) {
    				 
    			if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    				$columnValue = $excelRows[ $key ];
    				$objWorkSheet->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    			}
    				 
    			$objWorkSheet->SetCellValue ( $excelRows [$key] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    		}
    			
    		if(isset($additionSummaryDetails) && !empty($additionSummaryDetails)){
    			$loopcount = $key + 3;
    			$key++;
    			for($key; $key <= $loopcount; $key++){
    				$objWorkSheet->SetCellValue ( $excelRows [$key] . $rowCount, '');
    			}
    		}
    		$rowCount++;
    
    
    		foreach($recordDetails as $k => $v)
    		{
    			$col = 1;
    			foreach ($headercolumnWithKey as $field)
    			{
    				$value = $v[$field];
    				$value = str_replace('\n', "\n", $value);
    				$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  $value );
    
    				$col++;
    
    			}
    			
    			if(isset($additionSummaryDetails) && !empty($additionSummaryDetails)){
    				$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  '' );
    				$col++;
    				
    				$summaryLabel = $summaryValue = '';
    				
    				if($rowCount <= 6){
    					switch ($rowCount){
    						case 2:
    							$summaryLabel = 'Total PO';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    							
    						case 3:
    							$summaryLabel = 'Collection PO';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    							
    						case 4:
    							$summaryLabel = 'Delivery PO';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    							
    						case 5:
    							$summaryLabel = '';
    							$summaryValue = '';
    							break;
    							
    						case 6:
    							$summaryLabel = 'Total Amount';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    					}
    					
    					$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  $summaryLabel );
    					$col++;
    					$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  $summaryValue );
    					$col++;
    				}
    			}
    			$rowCount++;
    
    		}
    
    		foreach(range('A','Z') as $columnID) {
    			$objWorkSheet->getColumnDimension($columnID)->setAutoSize(true);
    		}
    
    		$objWorkSheet->getStyle("1")->getFont()->setBold(true);
    
    
    		$style = array(
    				'alignment' => array(
    						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    				)
    		);
    
    		$objWorkSheet->getStyle("2")->applyFromArray($style);
    		$objWorkSheet->getStyle("1")->applyFromArray($style);
    		$objPHPExcel->getDefaultStyle()->applyFromArray($style);
    
    		$sheetTitle = isset($sheetTitle) && !empty($sheetTitle) ? str_replace('/', '', $sheetTitle) : '';
    		$objWorkSheet->setTitle($sheetTitle);
    		 
    		$i++;
    	}
    	$objPHPExcel->setActiveSheetIndex (0);
    	$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
    
    	ob_start ();
    	//$objWriter->save ( "php://output" );
    	$writer->save('php://output');
    	$xlsData = ob_get_contents ();
    	ob_end_clean ();
    	 
    	return $xlsData;
    }
    
    public function generateGoodOutSummarySpreadsheetMultiple( $exportInfo , $breakColumnArray = []){
    	require_once 'vendor/autoload.php';
    
    	$multiRecordDetails = (!empty($exportInfo['record_detail']) ? $exportInfo['record_detail'] : [] );
    
    	$objPHPExcel = new Spreadsheet ();
    
    	//First sheet
    	//$sheet = $objPHPExcel->getActiveSheet();
    
    	$excelTitle = array_keys($multiRecordDetails);
    
    	if(array_key_exists('Summary', $multiRecordDetails)){
    		if(isset($excelTitle) && !empty($excelTitle) && isset($excelTitle[0]) && !empty($excelTitle[0])){
    			$objPHPExcel->getActiveSheet()->setTitle($excelTitle[0]);
    		}
    
    		// Default summary column
    		$recordDetails = $multiRecordDetails['Summary'];
    
    		$rowCount = $tableCount = 1;
    		$excelRows = Wild_tiger::DefaultExcelRow ();
    		
    		foreach ($recordDetails as $tableTitle => $recordDetail){
    			$countTablecol = (isset($recordDetail[0]) && !empty($recordDetail[0]) ? count($recordDetail[0]) : 1);
    			$countTableExcelRow = $countTablecol - 1;
    			
    			$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows[0] . $rowCount,  strtoupper(Wild_tiger::enumText( $tableTitle )) );				
    			$objPHPExcel->getActiveSheet()->mergeCells($excelRows[0] . $rowCount . ':' .$excelRows [$countTableExcelRow] . $rowCount);    			
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $rowCount . ':' .$excelRows [$countTableExcelRow] . $rowCount)->getFont()->setBold(true);
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $rowCount . ':' .$excelRows [$countTableExcelRow] . $rowCount)->getAlignment()->setHorizontal('center');
    			
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $rowCount . ':' .$excelRows [$countTableExcelRow] . $rowCount)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $rowCount . ':' .$excelRows [$countTableExcelRow] . $rowCount)->getFill()->getStartColor()->setARGB('DAF1F3');
    			
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $rowCount . ':' .$excelRows [$countTableExcelRow] . $rowCount)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    			
    			$rowCount++;
    			
    			$getHeaderData = array_keys($recordDetail[0]);
    			
    			$headercolumnWithKey = array_keys($recordDetail[0]);
    			
    			foreach ( $getHeaderData as $key => $header ) {
    			
    				if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    					$columnValue = $excelRows[ $key ];
    					$objPHPExcel->getActiveSheet()->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    				}    		
    				
    				if(str_contains($header, 'total')){
	    				$strToArray = explode('_', $header);
	    				if(isset($strToArray) && !empty($strToArray) && isset($strToArray[0]) && !empty($strToArray[0])){
		    				unset($strToArray[0]);	    					
	    				}
	    				$header = implode('_', $strToArray);
    				}
    			
    				$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$key] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    				
    				$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$key]$rowCount")->getFont()->setBold(true);
    				$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$key]$rowCount")->getAlignment()->setHorizontal('center');
    				 
    				$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$key]$rowCount")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    				$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$key]$rowCount")->getFill()->getStartColor()->setARGB('DAF1F3');
    				
    				$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$key]$rowCount")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    			}
    			$rowCount++;
    			
    			foreach($recordDetail as $k => $v)
    			{
    				$col = 1;
    				foreach ($headercolumnWithKey as $field)
    				{
    					$value = $v[$field];
    					//$value = ( is_float( $value ) != false ?  twoDigitAmount($value)  : $value ) ;
    					$value = str_replace('\n', "\n", $value);
    					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,  $value );
    					
    					$rowIndex = $col - 1;    					
    					$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$rowIndex]$rowCount")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    					
    					if(str_contains($field, 'total')){
    						$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$rowIndex]$rowCount")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    						$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$rowIndex]$rowCount")->getFill()->getStartColor()->setARGB('DAF1F3');
    						$objPHPExcel->getActiveSheet()->getStyle("$excelRows[$rowIndex]$rowCount")->getFont()->setBold(true);
    					}
    					
    					$col++;
    			
    				}    				
    				
    				$rowCount++;    				 
    			}
    			
    			
    			$lastTableraw = $rowCount - 1;
    			
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $lastTableraw . ':' .$excelRows [$countTableExcelRow] . $lastTableraw)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
    			$objPHPExcel->getActiveSheet()->getStyle("$excelRows[0]$lastTableraw")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $lastTableraw . ':' .$excelRows [$countTableExcelRow] . $lastTableraw)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $lastTableraw . ':' .$excelRows [$countTableExcelRow] . $lastTableraw)->getFill()->getStartColor()->setARGB('DAF1F3');
    			$objPHPExcel->getActiveSheet()->getStyle($excelRows[0] . $lastTableraw . ':' .$excelRows [$countTableExcelRow] . $lastTableraw)->getFont()->setBold(true);
    			
    			foreach(range('A','Z') as $columnID) {
    				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    			}
    			
    			$rowCount++;
    			$tableCount++;
    		}
    	}
    
    
    	//Start adding next sheets
    	$i=1;
    	while ($i < count($multiRecordDetails)) {
    		 
    		// Add new sheet
    		$objWorkSheet = $objPHPExcel->createSheet($i); //Setting index when creating
    
    		// Rename sheet
    		$sheetTitle = isset($excelTitle) && !empty($excelTitle) && isset($excelTitle[$i]) && !empty($excelTitle[$i]) ? $excelTitle[$i] : '';
    
    		$additionSummaryDetails = (isset($exportInfo) && !empty($exportInfo) && isset($exportInfo['additional_summary']) && !empty($exportInfo['additional_summary']) && isset($exportInfo['additional_summary'][$sheetTitle]) && !empty($exportInfo['additional_summary'][$sheetTitle]) ? $exportInfo['additional_summary'][$sheetTitle] : []);
    
    		$recordDetails = $multiRecordDetails[$sheetTitle];
    
    		$rowCount = 1;
    		$excelRows = Wild_tiger::DefaultExcelRow ();
    
    		$getHeaderData = array_keys($recordDetails[0]);
    		$headercolumnWithKey = array_keys($recordDetails[0]);
    
    		foreach ( $getHeaderData as $key => $header ) {
    				
    			if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    				$columnValue = $excelRows[ $key ];
    				$objWorkSheet->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    			}
    				
    			$objWorkSheet->SetCellValue ( $excelRows [$key] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    		}
    		 
    		if(isset($additionSummaryDetails) && !empty($additionSummaryDetails)){
    			$loopcount = $key + 3;
    			$key++;
    			for($key; $key <= $loopcount; $key++){
    				$objWorkSheet->SetCellValue ( $excelRows [$key] . $rowCount, '');
    			}
    		}
    		$rowCount++;
    
    
    		foreach($recordDetails as $k => $v)
    		{
    			$col = 1;
    			foreach ($headercolumnWithKey as $field)
    			{
    				$value = $v[$field];
    				$value = str_replace('\n', "\n", $value);
    				$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  $value );
    
    				$col++;
    
    			}
    			 
    			if(isset($additionSummaryDetails) && !empty($additionSummaryDetails)){
    				$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  '' );
    				$col++;
    
    				$summaryLabel = $summaryValue = '';
    
    				if($rowCount <= 6){
    					switch ($rowCount){
    						case 2:
    							$summaryLabel = 'Total Boxes';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    								
    						case 3:
    							$summaryLabel = 'Total Pallets';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    								
    						case 4:
    							$summaryLabel = 'Total Cost';
    							$summaryValue = (isset($additionSummaryDetails[$rowCount]) && !empty($additionSummaryDetails[$rowCount]) ? $additionSummaryDetails[$rowCount] : 0);
    							break;
    					}
    						
    					$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  $summaryLabel );
    					$col++;
    					$objWorkSheet->setCellValueByColumnAndRow($col, $rowCount,  $summaryValue );
    					$col++;
    				}
    			}
    			$rowCount++;
    
    		}
    
    		foreach(range('A','Z') as $columnID) {
    			$objWorkSheet->getColumnDimension($columnID)->setAutoSize(true);
    		}
    
    		$objWorkSheet->getStyle("1")->getFont()->setBold(true);
    
    
    		$style = array(
    				'alignment' => array(
    						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    				)
    		);
    
    		$objWorkSheet->getStyle("2")->applyFromArray($style);
    		$objWorkSheet->getStyle("1")->applyFromArray($style);
    		$objPHPExcel->getDefaultStyle()->applyFromArray($style);
    
    		$sheetTitle = isset($sheetTitle) && !empty($sheetTitle) ? str_replace('/', '', $sheetTitle) : '';
    		$objWorkSheet->setTitle($sheetTitle);
    		 
    		$i++;
    	}
    	$objPHPExcel->setActiveSheetIndex (0);
    	$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
    
    	ob_start ();
    	//$objWriter->save ( "php://output" );
    	$writer->save('php://output');
    	$xlsData = ob_get_contents ();
    	ob_end_clean ();
    
    	return $xlsData;
    }
    
    public function checkUniqueShipmentId(Request $request){
    	
    	$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0 );
    	$shipmentRecordId = (!empty($request->shipment_record_id) ? (int)($request->shipment_record_id) : null );
    	$recordType = (!empty($request->shipment_record_type) ? trim($request->shipment_record_type) : null );
    	$shipmentNo = (!empty($request->shipment_no) ? trim($request->shipment_no) : null );
    	
    	$validator = Validator::make ( $request->all (), [
    			'shipment_no' => [ 'required' , new UniqueAmazonShimentId($recordId,$shipmentRecordId,$recordType,$shipmentNo) ],
    	], [
    			'shipment_no.required' => __ ('messages.require-shipment-id'),
    	] );
    
    	$result = [];
    	$result['status_code'] = 1;
    	$result['message'] = trans('messages.success');
    
    	if ($validator->fails ()) {
    		$result['status_code'] = 101;
    		$result['message'] = trans('messages.error-unique-shipment-id');
    	}
    	echo json_encode($result);die;
    }
}
