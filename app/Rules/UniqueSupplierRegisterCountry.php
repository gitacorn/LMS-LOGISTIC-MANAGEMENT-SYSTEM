<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;
class UniqueSupplierRegisterCountry implements Rule
{
	private $recordId;
	private $registerType;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
 	public function __construct($userId = null,$registerType ='')
    {
    	$this->registerType = $registerType;
    	if($userId > 0 ){
    		$this->recordId = $userId;
    	}
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
    	//echo '<pre>';print_r($value);die;
    	if(!empty($value)){
    		$value = trim($value);
    		$checkUniqueWhere =[];
    		$checkUniqueWhere['e_record_status'] = $this->registerType;
    		$checkUniqueWhere['i_country_id'] = $value;
    		$checkUniqueWhere['t_is_deleted != '] = 1;
    		if(!empty($this->recordId)){
    			$checkUniqueWhere['i_id != '] = $this->recordId;
    	
    		}
    		$dbObject = new BaseModel();
    		 
    		$getUserDetails =  $dbObject->getSingleRecordById(  config('constants.SUPPLIER_DETAIL_TABLE'),
    				[ 'i_id' ] ,
    				$checkUniqueWhere  );
    		
    		if( (!empty($getUserDetails)) && count(Wild_tiger::objectToArray($getUserDetails)) > 0 ){
    			return false;
    		} else {
    			return true;
    		}
    	}
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.error-unique-country');
    }
}
