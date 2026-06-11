<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Helpers\Twt\Wild_tiger;
use App\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueEmail;
use App\Rules\UniqueSalesEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuestController extends Controller
{
    //
	public function __construct(){
    	$this->BaseModel = new BaseModel();
    	
    }
	
	public function customLogEntry(){
		Log::info(print_r($request->all(),true));
	}
	
	public function expectionLogEntry($postData , $e){
		Log::info(print_r($postData,true));
		Log::info(print_r($e->getMessage(),true));
	}
	
	public function checkUniqueUserEmail(Request $request) {
    	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
    	
    	$validator = Validator::make ( $request->all (), [
    			'email' => [ 'required' ,new UniqueEmail($recordId) ]  ,
    	], [
    			'email.required' => __ ( 'messages.required-login-email' ),
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
    public function customErrorPage() {
    	$data['pageTitle'] = trans ( 'messages.page-not-found');
    	return view ( 'errors/custom-error' )->with ( $data );
    }
    
    
    public function getExcelData( $uploadedFilePath , $demerge = true){
    	
    	//require_once 'vendor/autoload.php';
    	$rowDetails = [];
    	$response = [];
    	$response['status'] = false;
    	try {
    		 
    		$spreadsheet = IOFactory::load($uploadedFilePath);
    		$sheet = $spreadsheet->getActiveSheet();
    	
    		if( $demerge != false ){
    			$allCellDetails = [];
    			foreach($sheet->getMergeCells() as $cells){
    				$allCellDetails[] =  $cells;
    				$sheet->unmergeCells($cells);
    			}
    			 
    			for($i=0; $i<count($allCellDetails);$i++){
    				// explode merge cells range //
    				$CellIndex = explode(":", $allCellDetails[$i]);
    			
    				// get main cell with value, ex N25:N27 , the value only stored in N25 //
    				$CellValue = $spreadsheet->getActiveSheet()->getCell($CellIndex[0])->getValue();
    			
    				// starting index //
    				$StartIndex = (int) substr($CellIndex[0],1,2);
    			
    				// ending index //
    				$EndIndex = (int) substr($CellIndex[1],1,2);
    			
    				// column name example "A" ,"B" //
    				$ColumnName = substr($CellIndex[0],0,1);
    			
    				// loop to copy the value from main cell, to other cell //
    				for($j = $StartIndex;$j <= $EndIndex; $j++){
    					$spreadsheet->getActiveSheet()->setCellValue($ColumnName.$j , $CellValue);
    				}
    			
    			}
    		}
			$allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    		 
    		
    	
    		if(!empty($allDataInSheet)){
    			foreach ($allDataInSheet as $key => $value) {
    	
    				if( $key > 0 ){
    	
    					if( $key == 1 ){
    						$excelKeys = array_values($value);
    					} else {
    	
    						$rowDetail = [];
    						$rowDetail = array_combine($excelKeys, $value);
    						if(!empty($rowDetail)){
    							$rowDetails[] = $rowDetail;
    						}
    					}
    	
    	
    				}
    			}
    		}
    	}catch (Exception $e) {
    		$response['message'] =  $e->getMessage();
    	}
    	
    	if(!empty($rowDetails)){
    		$response['status'] = true;
    		$response['data'] = $rowDetails;
    	}
    	return $response;
    	
    }
    public function accessDenidePage() {
    	$data['pageTitle'] = trans ( 'messages.access-denied');
    	return view (  config('constants.ADMIN_FOLDER') . 'access-denied' )->with ( $data );
    }
    
}
