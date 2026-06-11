<?php

use Illuminate\Support\Facades\Log;
use App\BaseModel;

function excleColumn($index = 0 ){

	$array = array('AA');
	$current = 'AA';
	while ($current != 'ZZ') {
	    $array[] = ++$current;
	}
	return $array[$index];
}

function monthStartDate(){

	$result = date('Y-m-01');
	return $result;

}

function monthEndDate(){

	$result = date('Y-m-t');
	return $result;

}

function threeNumberSeries($value) {
	
	$result = sprintf("%'03d", $value);
	return $result;
}


function dbDate($value, $dbFormat = true)
{
	$result = null;
	if(!empty($value)){
		$value = str_replace("/", "-", $value);
		$result = date('Y-m-d', strtotime($value));
	}
	return $result;
}

function enumText($value) {
	$result = '';
	if(!empty($value)){
		$result = ucwords(str_replace("_",  " ", $value));
	}
	return $result;
}

function clientDateTime($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('d-m-Y h:i A', strtotime($value));
	}

	return $result;
}

function clientDate($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('d-m-Y', strtotime($value));
	}

	return $result;
}

function apiResponse($status , $messages , $data = [] ){
	$result = [];
	$result['status_code'] = $status;
	$result['message'] = $messages;
	
	//Log::info(print_r($data,true));
	
	if(!empty($data)){
		$result['data'] = (!empty($data) ? $data : null );
	}
	header('Content-Type: application/json');
	echo json_encode($result);die;
}

function last_query(){
	echo BaseModel::last_query();
}

function clientTime($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('h:i A', strtotime($value));
	}

	return $result;
}

function  decimalAmount($value){

	$result = 0;
	if(!empty($value)){
		$value = round($value,2);
		$result = number_format(  $value , 0 , ".", "," );
		$fmt = new \NumberFormatter($locale = 'en_GB', NumberFormatter::DECIMAL);
		$result = $fmt->format($value);
	} else {
		$result = 0.00;
	}

	return $result;
}

function  objectToArray($value){

	$result  = [];
	if(!empty($value)){
		$result = json_decode(json_encode($value) , true ); 
	}
	return $result;
}
function paymentStatus(){
	$data  = [];
	$data[config('constants.PAID_PAYMENT_STATUS')] = trans('messages.paid');
	$data[config('constants.PARTIAL_PAID_PAYMENT_STATUS')] = trans('messages.partial-paid');
	$data[config('constants.NOT_PAID_PAYMENT_STATUS')] = trans('messages.not-paid');
	
	return $data;
}
function collectionDeliveryInfo(){
	$data  = [];
	$data[config('constants.COLLECTION')] = trans('messages.collection');
	$data[config('constants.DELIVERY')] = trans('messages.delivery');
	
	return $data;
}
function deliveryTypeInfo(){
	$data  = [];
	$data[config('constants.FULL_DELIVERY_TYPE')] = trans('messages.full');
	$data[config('constants.PARTIAL_DELIVERY_TYPE')] = trans('messages.partial');
	$data[config('constants.CANCELLED_DELIVERY_TYPE')] = trans('messages.cancelled');
	
	return $data;
}
function customProcedureInfo(){
	$data  = [];
	
	$data[config('constants.CONSIGNER_SUPPLIER')] = trans('messages.consigner-supplier');
	$data[config('constants.CONSIGNER_OUTSIDE')] = trans('messages.consignee-ourside');
	$data[config('constants.NOT_APPLICABLE')] = trans('messages.not-applicable');
	return $data;
}

function dangerousGoodsInfo(){
	$data  = [];
	$data[config('constants.SELECTION_YES')] = trans('messages.yes');
	$data[config('constants.SELECTION_NO')] = trans('messages.no');

	return $data;
}
function palletsTypeInfo(){
	$data  = [];
	$data[config('constants.STACKABLE_PALLET_TYPE')] = trans('messages.stackable');
	$data[config('constants.NOT_STACKABLE_PALLET_TYPE')] = trans('messages.not-stackable');

	return $data;
}
function weightUnitInfo(){
	$data  = [];
	$data[config('constants.KGS_WEIGHT_UNIT')] = trans('messages.kgs');
	$data[config('constants.LBD_WEIGHT_UNIT')] = trans('messages.lbs');

	return $data;
}
function insuranceStatus(){
	$data  = [];
	$data[config('constants.STATUS_IN_HOUSE')] = trans('messages.in-house');
	$data[config('constants.STATUS_THIRD_PARTY')] = trans('messages.third-party');
	$data[config('constants.NOT_APPLICABLE')] = trans('messages.not-applicable');
	return $data;
}
function wayOfTransport(){
	$data  = [];
	$data[config('constants.AIR_TRANSPORT')] = trans('messages.air');
	$data[config('constants.SEA_TRANSPORT')] = trans('messages.sea');
	return $data;
}

function clientDateFormat($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('d/m/Y', strtotime($value));
	}

	return $result;
}
if (! function_exists('checkPermission')) {

	function checkPermission( $moduleName ){
		$response = false;
		$moduleName = trim($moduleName);
		if( (!empty($moduleName))){
			$model = new BaseModel();
			$response  = $model->validatePermission( $moduleName );
		}

		return $response;
	}
}
function wayOfTransportDetails( $selectedValue = []  ){
	$data  = [];
	if(in_array(config('constants.AIR_TRANSPORT'),$selectedValue)){
		$data[config('constants.AIR_TRANSPORT')] = trans('messages.air');
	}
	if(in_array(config('constants.SEA_TRANSPORT'),$selectedValue)){
		$data[config('constants.SEA_TRANSPORT')] = trans('messages.sea');
	}
	if(in_array(config('constants.TRUCK_TRANSPORT'),$selectedValue)){
		//$data[config('constants.TRUCK_TRANSPORT')] = trans('messages.truck');
	}
	if(in_array(config('constants.ROAD_TRANSPORT'),$selectedValue)){
		$data[config('constants.ROAD_TRANSPORT')] = trans('messages.road');
	}
	return $data;
}
function wayToWarehouseDetails(){
	$data  = [];
	$data[config('constants.AMAZON_FBA_SHEET')] = trans('messages.amazon');
	$data[config('constants.WAREHOUSE_FBA_SHEET')] = trans('messages.warehouse');
	$data[config('constants.CUSTOMER_FBA_SHEET')] = trans('messages.customer');
	
	return $data;
}
function fbaSheetStatusInfo(){
	$data  = [];
	$data[config('constants.PENDING_STATUS')] = trans('messages.pending');
	$data[config('constants.FAILED_STATUS')] = trans('messages.failed');
	$data[config('constants.PROCESSING_STATUS')] = trans('messages.processing');
	$data[config('constants.SUCCESS_STATUS')] = trans('messages.success');
	$data[config('constants.NOT_UPLOADLED_STATUS')] = trans('messages.not-uploaded');
	
	return $data;
}
function wayToWarehouseInfo(){
	$data  = [];
	$data[config('constants.AMAZON_FBA_SHEET')] = trans('messages.amazon');
	$data[config('constants.CUSTOMER_FBA_SHEET')] = trans('messages.customer');
	$data[config('constants.UK_WAREHOUSE_FBA_SHEET')] = trans('messages.uk-warehouse');

	return $data;
}

if (! function_exists('createSlug')) {

	function createSlug($title) {

		// Convert all dashes/underscores into separator
		$flip = $separator = '-';

		$title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

		// Replace @ with the word 'at'
		$title = str_replace('@', $separator.'at'.$separator, $title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', twt_lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return $title;
	}


}

if (! function_exists('twt_lower')) {

	function twt_lower($value) {
		return mb_strtolower($value, 'UTF-8');
	}
}
if (! function_exists('generateOTP')) {

	function generateOTP($length = 6) {

		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;

	}
}
if (! function_exists('sendMailSMTP')) {
	function sendMailSMTP($data)
	{
		$mailResult = false;
		try {
			// Handle OTP emails (single recipient with template)
			if (isset($data['viewName']) && !empty($data['viewName'])) {
				// OTP/Forgot Password emails
				$toEmail = $data['receiverEmail'] ?? config('constants.LOGIN_OTP_RECEIVE_CC_EMAIL');
				$subject = $data['subject'] ?? 'No Subject';
				$viewName = $data['viewName'] ?? '';
				$mailData = $data['mailData'] ?? [];
				
				$result = Mail::send($viewName, $mailData, function ($message) use ($toEmail, $subject, $data) {
					$message->to($toEmail)->subject($subject);
					
					// Handle attachments for template emails
					if (isset($data['attachment']) && !empty($data['attachment'])) {
						foreach ($data['attachment'] as $attachment) {
							if (file_exists($attachment)) {
								$message->attach($attachment);
							}
						}
					}
				});
			} 
			// Handle announcement emails (multiple recipients with HTML content)
			else {
				// Announcement emails
				$toEmails = $data['receiverEmail'] ?? config('constants.LOGIN_OTP_RECEIVE_CC_EMAIL');
				$subject = $data['subject'] ?? 'No Subject';
				$body = $data['mail_content'] ?? '';
				$ccEmails = $data['ccEmail'] ?? [];
				
				$result = Mail::html($body, function ($message) use ($toEmails, $subject, $ccEmails, $data) {
					// Handle multiple TO recipients
					if (is_array($toEmails)) {
						$message->to($toEmails);
					} else {
						$message->to($toEmails);
					}
					
					$message->subject($subject);
					
					// Add CC recipients
					if (!empty($ccEmails)) {
						if (is_array($ccEmails)) {
							foreach ($ccEmails as $ccEmail) {
								if (!empty($ccEmail)) {
									$message->cc($ccEmail);
								}
							}
						} else {
							if (!empty($ccEmails)) {
								$message->cc($ccEmails);
							}
						}
					}
					
					// Handle attachments for announcement emails
					if (isset($data['attachment']) && !empty($data['attachment'])) {
						foreach ($data['attachment'] as $attachment) {
							if (file_exists($attachment)) {
								$message->attach($attachment);
							}
						}
					}
				});
			}
			
			$mailResult = true;
		} catch (\Exception $e) {
			Log::error('Email sending failed: ' . $e->getMessage());
			$mailResult = false;
			$result['msg'] = $e->getMessage();
		}
		
		if ($mailResult != false) {
			$result['status'] = true;
		} else {
			$result['status'] = false;
		}
		
		return $result;
	}
}

if (! function_exists('removeSession')) {
	function removeSession($requestUserId = null , $sessionId = null ){
	
		$allSessionFiles = glob(storage_path( 'framework/sessions/*'));
		//Log::info('requested_user_id = ' . $requestUserId );
		//Log::info(print_r($allSessionFiles , true));
		if(!empty($allSessionFiles)){
			foreach($allSessionFiles as $allSessionFile){
				$fileData = file_get_contents($allSessionFile);
				$fileArray = unserialize($fileData);
				//Log::info( 'file_name = ' . basename($allSessionFile) );
				//Log::info(print_r($fileArray , true));
				//Log::info('loop_id = ' . $requestUserId );
				//Log::info('session_id = ' . $fileArray['user_id'] );
				//Log::info(print_r( session()->all() , true ) );
				
				if( (!empty($fileArray)) && isset($fileArray['user_id'])  && ( $requestUserId ==  $fileArray['user_id'] ) ){
					//Log::info('session file = ' . $allSessionFile );
					
					if(!empty($sessionId)){
						if( isset($fileArray['_token']) && ( $fileArray['_token'] == $sessionId ) ){
							unlink($allSessionFile);
						}
					} else {
						if(!empty($requestUserId)){
							unlink($allSessionFile);
						}
					}
					
					
					//break;
				}
			}
		}
		return true;
	}
}
function typeInfo(){
	$data  = [];
	$data[config('constants.BOX')] = trans('messages.box');
	$data[config('constants.PALLET')] = trans('messages.pallet');

	return $data;
}
function registeredCollectionInfo(){
	$data  = [];
	$data[config('constants.REGISTERED_STATUS')] = trans('messages.registered');
	$data[config('constants.COLLECTION')] = trans('messages.collection');

	return $data;
}

function generatePdf($recordInfo = ''){
	
	$fontdata = [
			'poppins-regular' => [
					'R' => 'Poppins-Regular.ttf',
			],
	];
	
	$fontdata = [
			'poppins-medium' => [
					'R' => 'Poppins-Medium.ttf',
			],
	];
	
	$fontdata = [
			'poppins-bold' => [
					'R' => 'Poppins-Bold.ttf',
			],
	];
	
	$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
	
	$fontDirs = $defaultConfig['fontDir'];
	$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
	$fontDirs = $defaultConfig['fontDir'];
	$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
	$fontData = $defaultFontConfig['fontdata'];
	$fontData += [
			'poppins-regular' => [
					'R' => 'Poppins-Regular.ttf',
			]
	];
	$fontData += [
			'poppins-medium' => [
					'R' => 'Poppins-Medium.ttf',
			]
	];
	$fontdata = [
			'poppins-bold' => [
					'R' => 'Poppins-Bold.ttf',
			],
	];
	$data = [];
	$data['pdfRecordInfo'] = $recordInfo ;
	
	$html = view('admin/shipment-quote-pdf')->with($data);
	
	$mpdf = new \Mpdf\Mpdf([
			'mode' => 'c',
			'format' => 'A4',
			'margin_left' => 3,
			'margin_right' => 3,
			'margin_top' => 3,
			'margin_bottom' => 3,
			'margin_header' => 3,
			'margin_footer' => 3,
			'fontDir' => array_merge($fontDirs, [
					dirname(dirname(__DIR__)) . '/assets/css/fonts/',
			]),
			'fontdata' => $fontData,
			'mode' => 'utf-8',
	]);
	
	$mpdf->SetWatermarkImage(
			('images/shipment-favicon.png'),
			0.1,
			''
			//    array(160,10)
	);
	$mpdf->autoPageBreak = true;
	$fileName =  (!empty($recordInfo->goodInBuyerMaster->v_goods_in_buyer_master_no) ? $recordInfo->goodInBuyerMaster->v_goods_in_buyer_master_no :''). '.pdf';
	
	$header  = '';
	$header .= '<div class="main-page-border-outer vh100">';
	$header .= '<div class="px-20" style="padding:3px;">';
	$footer = '';
	
	$mpdf->SetHTMLHeader($header);
	$mpdf->SetHTMLFooter($footer);
	$mpdf->SetTitle($fileName);
	
	// echo $html;die;
	// $mpdf->setFooter('{PAGENO}');
	$mpdf->showWatermarkImage = true;
	$mpdf->WriteHTML($html, 2);
	//$mpdf->Output();
	$mpdf->Output($fileName, 'I');
}

function getPorcessStatus(){
	$data  = [];
	$data[config('constants.UK_OTHER_COUNTRY_TO_PORT_NO')] = trans('messages.us01');
	$data[config('constants.PORT_TO_AGENT_WAREHOUSE_NO')] = trans('messages.us02');
	$data[config('constants.AGENT_WAREHOUSE_TO_AMAZON_NO')] = trans('messages.us03');
	$data[config('constants.AGENT_WAREHOUSE_TO_AMAZON_COMPLETED_NO')] = trans('messages.us04');
	
	return $data;
}

if (!function_exists('excelDateFormat')) {

	function excelDateFormat($value){
		$finalResult = '';

		if(!empty($value)){
			if( $value instanceof DateTime != false ){
				$value = date_format($value,'Y-m-d');
			}
			
			$result = str_replace('/', '-', $value);
			$date = date('Y-m-d' , strtotime($result));
			$finalResult = $date;
		}
		return $finalResult;
	}
}

if (!function_exists('customsProcedureDropdown')) {
	function customsProcedureDropdown(){
		$data  = [];
		$data[config('constants.CUSTOMS_PROCEDURE_EXPORT')] = trans('messages.export');
		$data[config('constants.CUSTOMS_PROCEDURE_IMPORT')] = trans('messages.import');
		$data[config('constants.CUSTOMS_PROCEDURE_BOTH')] = trans('messages.both');
		$data[config('constants.CUSTOMS_PROCEDURE_DASH')] = trans('messages.dash');
		return $data;
	}	
}
if (!function_exists('checkNumericValue')) {
	function checkNumericValue($value = null , $allowZero = false){
		$result = false;
		
		if (!empty($value)){
			$value = str_replace(',', '', $value);
			
			if (is_numeric($value)){
				switch ($allowZero){
					case true:
						if ($value >= 0){
							$result = true;
						}
						break;
					case false:
						if ($value >= 1){
							$result = true;
						}
						break;
				}
			}
		}
		
		return $result;
	}
}

if (!function_exists('validateDate')) {
	function validateDate($inputDate, $format = 'd-m-Y'){
		$result = false;
		if( $inputDate instanceof DateTime != false ){
			$inputDate = date_format($inputDate,'d-m-Y');
		}
		
		if (preg_match("/([0-9]{0,2})-([0-9]{0,2})-([0-9]{4})/", $inputDate, $matches)) {
			if (!checkdate($matches[2], $matches[1], $matches[3])) {
				$result = false;
    		} else {
    			$result = true;
    		}
		}
		if( $result != true ){
			if (preg_match("/([0-9]{0,2})\/([0-9]{0,2})\/([0-9]{4})/", $inputDate, $matches)) {
				if (!checkdate($matches[2], $matches[1], $matches[3])) {
					$result = false;
				} else {
					$result = true;
				}
			}
		}
		return $result;
	}
}
if (!function_exists('getCommaSeparatedFormattedString')){
	function getCommaSeparatedFormattedString($string){
		$result = '';
		if (!empty($string)){
			$result = implode(',', array_unique(array_filter(array_map('trim', explode(',', trim($string))))));
		}
		return $result;
	}
}
if (!function_exists('getUsaGoodOutWarehouseType')) {
	function getUsaGoodOutWarehouseType(){
		$data  = [];
		$data[config('constants.OWN_WAREHOUSE_TYPE')] = trans('messages.own');
		$data[config('constants.AGENT_WAREHOUSE_TYPE')] = trans('messages.agent');
		return $data;
	}
}


function shipmentTypeDetails(){
	$data  = [];
	$data[config('constants.AMAZON_FBA_SHEET')] = trans('messages.amazon');
	//$data[config('constants.WAREHOUSE_FBA_SHEET')] = trans('messages.warehouse');
	$data[config('constants.CUSTOMER_FBA_SHEET')] = trans('messages.customer');

	return $data;
}

if(!function_exists('uploadedFileExists') ){
	function uploadedFileExists($fileName){
		if(!empty($fileName)){
			return file_exists(config('constants.FILE_STORAGE_FILE_PATH') . config('constants.UPLOAD_FOLDER') . $fileName);
		}
		return false;
	}
}

if(!function_exists('allFifteenDates')){
	function allFifteenDates(){
		$today = new DateTime();
		$allDates = [];
		for( $i=0;$i<=14;$i++ ){
			$allDates[] = $today->format('d-m-Y');
			$today->modify('+1 day');
		}
		return $allDates;
	}
}

if ( ! function_exists('getAllDatesOfMonth')) {
    function getAllDatesOfMonth($monthYear) {
        
    	list($month, $year) = explode('-', $monthYear);

        $startDate = new DateTime("$year-$month-01");
        $endDate = new DateTime("$year-$month-01");
        $endDate->modify('first day of next month');
        $dates = [];

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        foreach ($period as $dt) {
            $dates[] = $dt->format("d-m-Y");
        }

        return $dates;
    }
}

if (! function_exists('checkUserRoleBase')) {

	function checkUserRoleBase( $userId ){
		$response = false;
		$userId = trim($userId);
		if( (!empty($userId))){
			$model = new BaseModel();
			$response  = $model->validateUser( $userId );
		}
		return $response;
	}
}

if(!function_exists('getFirstLastDayOfMonth')){
	function getFirstLastDayOfMonth( $date , $type = 'first'){
		$result = '';
		$dateObj = DateTime::createFromFormat('m-Y', $date);
		if( !empty($date) && (!empty($type)) ){
			$dateObj->modify($type.' day of this month');
			$result = $dateObj->format('Y-m-d');
		}
		return $result;
	}
}

if(!function_exists('getUpcomingSixDates')){
	function getUpcomingSixDates(){
		$today = new DateTime();
		$allDates = [];
		for( $i=0;$i<=6;$i++ ){
			$allDates[] = $today->format('d-m-Y');
			$today->modify('+1 day');
		}
		return $allDates;
	}
}

if (! function_exists('decryptPassword')) {

	function decryptPassword($encryptPassword){
		$decodePasword = "";
		if(!empty($encryptPassword)){
			$decodePasword = trim(Wild_tiger::decode( $encryptPassword , config('constants.ENCRYPTION_KEY')));
		}
		return $decodePasword;

	}
}

if (!function_exists('checkNotEmptyString')){
	function checkNotEmptyString($string){
		return strlen(trim($string)) > 0 ? true : false;
	}
}

?>