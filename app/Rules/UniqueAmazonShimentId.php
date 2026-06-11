<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;

class UniqueAmazonShimentId implements Rule
{
	private $recordId;
	private $shipmentRecordId;
	private $recordType;
	private $shipmentNo;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId = null ,$shipmentRecordId = null,$recordType= null,$shipmentNo = null)
    {
    	$this->shipmentRecordId = $shipmentRecordId;
    	$this->recordType = $recordType;
    	$this->shipmentNo = $shipmentNo;
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
    		$checkUniqueWhere['v_shipment_no'] = $value;
    		$checkUniqueWhere['t_is_deleted != '] = 1;
    		
    		/* if(!empty($this->shipmentRecordId)){
    			$checkUniqueWhere['i_ref_table_id != '] = $this->shipmentRecordId;
    			if(!empty($this->recordType)){
    				$checkUniqueWhere['v_ref_record_type'] = $this->recordType;
    			}
    		} */
    		$dbObject = new BaseModel();
    		 
    		$getUserDetails =  $dbObject->getSingleRecordById(  config('constants.SHIPMENT_NO_INFO_TABLE'),
    				
    				[ 'i_id','v_shipment_no','i_ref_table_id','v_ref_record_type'] ,
    				$checkUniqueWhere  );
    		
    		if( (!empty($getUserDetails)) && count(Wild_tiger::objectToArray($getUserDetails)) > 0 ){
    			if($getUserDetails->v_ref_record_type == $this->recordType && $getUserDetails->i_ref_table_id == $this->shipmentRecordId){
    				return false;
    			}
    			
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
        return trans('messages.error-unique-shipment-id');
    }
}
