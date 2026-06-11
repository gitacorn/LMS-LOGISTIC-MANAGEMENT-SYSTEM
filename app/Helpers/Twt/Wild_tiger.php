<?php
namespace App\Helpers\Twt;

use Illuminate\Support\Facades\Session;
use Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;



class Wild_tiger{
	
	// private key for encryption
	public static $key = 'TWT_LARAVEL'; //Config::get('constants.ENCRYPTION_KEY');
	
	/**
	 * This function used to display message
	 *
	 * @param string $type
	 *            'message type'
	 * @param string $message
	 *            text'
	 */
	public static function setFlashMessage($type, $message)
	{
		
		$output = '<div class="alert alert-' . $type . ' alert-dismissible text-center" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>' . ucwords( $message )  . '</div>';
		Session::flash('message', $output );
	}
	
	public static function readMessage()
	{
		if (Session::has('message')){
			echo Session::get('message');
		}
		
	}
	
	public static function createdReadableLink($link)
	{
		$link =str_replace(" ", "_", $link);
		return $link;
	}
	
	public static function sendMailSMTP( $data )
	{
		$mailResult = false;
		try{
			$result = Mail::send($data['viewName'],$data['mailData'], function ( $message ) use ($data) {
				$message->from( config('constants.SEND_EMAIL_USER' ),config('constants.SEND_EMAIL_TITLE' ));
				$message->to( config('constants.CONTACT_RECEIVE_EMAIL') );
				$message->subject($data['subject']);
			});
			$mailResult = true;
		}catch(\Exception $e){
			$mailResult = false;
			$result['msg'] = $e->getMessage();
		}
		//var_dump($mailResult);
		if( $mailResult != false ){
			$result['status'] = true;
		}else{
			$result['status'] = false;
		}
		//$result['status'] = true;
		return $result;
	}
	
	
	/**
	 * This function used to encode input text
	 *
	 * encode input value
	 *
	 * @param string $plainText
	 *            value'
	 * @return string
	 */
	public static function encode($plainText)
	{  	
		if (empty($plainText)) {
			return '';
		}
		$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plainText, $cipher, self::$key, $options = OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, self::$key , $as_binary = true);
		$ciphertext = self::safebase64_encode($iv . $hmac . $ciphertext_raw);
		return $ciphertext;
	}
	
	/**
	 * This function used to decode input text
	 *
	 * @param string $plainText
	 *            input text'
	 * @return string
	 */
	public static function decode($plainText)
	{
		if (empty($plainText)) {
			return '';
		}
		$c = self::safebase64_decode($plainText);
		$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len = 32);
		$ciphertext_raw = substr($c, $ivlen + $sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, self::$key, $options = OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, self::$key , $as_binary = true);
		if (hash_equals($hmac, $calcmac)) // PHP 5.6+ timing attack safe comparison
		{
			return $original_plaintext . "\n";
		}
	}
	
	/**
	 * safe64_encode value
	 *
	 * @param string $val
	 * @return string
	 */
	public static function safebase64_encode($val)
	{
		// return strtr ( base64_encode ( $val ), '+/=', '-_ ' );
		return rtrim(strtr(base64_encode($val), '+/', '-_'), '=');
	}
	
	/**
	 * safe64_decode value
	 *
	 * @param string $val
	 * @return string
	 */
	public static function safebase64_decode($val)
	{
		// return base64_decode ( strtr ( $val, '-_ ', '+/=' ) );
		return base64_decode(str_pad(strtr($val, '-_', '+/'), strlen($val) % 4, '=', STR_PAD_RIGHT));
	}
	
	public static function enumText($value) {
		$result = "";
		if(!empty($value)){
			$result =  ucwords(str_replace("_"," ", $value));
		}



		return $result;
	}
	
	
	
	
	public static  function dbDate($inputDate , $inputFormat = 'd/m/Y'){
	
		$dbDate = null;
		
		if(!empty($inputDate)){
			
			$inputDate = str_replace("/", "-", $inputDate);
			
			$inputFormat = (!empty($inputFormat) ? $inputFormat : config('constants.MONTH_DATE_FORMAT') );
			
			$dbDate = \DateTime::createFromFormat($inputFormat, $inputDate)->format('Y-m-d');
		
		}
	
		return $dbDate;
	
	}
	
	
	
	public static  function dbDateTime($inputDate){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$dbDate = date('Y-m-d H:i:s' , strtotime($inputDate));
				
		}
	
		return $dbDate;
	
	}
	
	public static  function clientDate($inputDate , $format = 'd-m-Y' ){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$dbDate = date($format , strtotime($inputDate));
				
		}
	
		return $dbDate;
	
	}
	
	public static  function clientDateTime($inputDate){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$dbDate = date('d-m-Y h:i A' , strtotime($inputDate));
				
		}
	
		return $dbDate;
	
	}
	
	
	
	public static function  decimalAmount($value){
		
		$result = "";
		if(!empty($value)){
			$value = round($value,2);
			$result = number_format(  $value , 0 , "." , "," );
			//$fmt = new \NumberFormatter($locale = 'en_IN', NumberFormatter::DECIMAL);
			//$result = $fmt->format($value);
		} else {
			$result = 0.00;
		}
		
		
		
		return $result;
		
	}
	
	public static function objectToArray($value){
		
		$result = json_decode(json_encode($value) , true);
		
		return $result;
		
	}
	
	public static function DefaultExcelRow(){
		$data = [ 'A' , 'B' , 'C' , 'D' , 'E' ,'F' , 'G' , 'H' ,'I' , 'J' , 'K' , 'L' , 'M' , 'N' , 'O' , 'P' , 'Q' , 'R' , 'S' , 'T' , 'U' , 'V' , 'W' , 'X' , 'Y' , 'Z', 'AA' , 'AB' , 'AC' , 'AD' , 'AE' , 'AF' , 'AG' , 'AH' , 'AI' , 'AJ' , 'AK' , 'AL' , 'AM' , 'AN' , 'AO' , 'AP' , 'AQ' ,'AR' ,'AS', 'AT', 'AU' , 'AV' , 'AW' , 'AX' , 'AY' , 'AZ' ,  'BA' , 'BB' , 'BC' , 'BD' , 'BE' ,'BF' , 'BG' , 'BH' ,'BI' , 'BJ' , 'BK' , 'BL' , 'BM' , 'BN' , 'BO' , 'BP' , 'BQ' , 'BR' , 'BS' , 'BT' , 'BU' , 'BV' , 'BW' , 'BX' , 'BY' , 'BZ' ];
		return $data;
	}
	
	public static function removeSession($requestUserId = null){
	
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
					if(!empty($requestUserId)){
						unlink($allSessionFile);
					}
					//break;
				}
			}
		}
		return true;
	}
}

