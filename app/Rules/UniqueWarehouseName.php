<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;
class UniqueWarehouseName implements Rule
{
	private $recordId;
	private $recordType;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId = null,$recordType = "")
    {
    	$this->recordType = $recordType;
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
    	if(!empty($value)){
    		$value = trim($value);
    		$checkUniqueWhere =[];
    		$checkUniqueWhere['v_warehouse_name'] = $value;
    		$checkUniqueWhere['e_record_type'] = $this->recordType;
    		$checkUniqueWhere['t_is_deleted != '] = 1;
    		if(!empty($this->recordId)){
    			$checkUniqueWhere['i_id != '] = $this->recordId;
    			 
    		}
    		$dbObject = new BaseModel();
    		
    		/* if( (!empty($this->recordType) && $this->recordType == config("constants.WAREHOUSE"))){
    			$checkUniqueWhere['e_record_type'] = config("constants.WAREHOUSE");
    		}else{
    			$checkUniqueWhere['e_record_type'] = config("constants.LOCATION");
    		} 
    		  */
    		$getUserDetails =  $dbObject->getSingleRecordById(  config('constants.WAREHOUSE_MASTER_TABLE'),
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
    	if( (!empty($this->recordType) && $this->recordType == config("constants.WAREHOUSE"))){
    		return trans('messages.error-unique-warehouse-name');
    	}else if((!empty($this->recordType) && $this->recordType == config("constants.PORT"))){
    		return trans('messages.error-unique-port-name');
    	}else{
    		return trans('messages.error-unique-location-name');
    	}
        
    }
}
