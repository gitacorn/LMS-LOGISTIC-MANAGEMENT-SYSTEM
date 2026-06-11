<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\LookupMaster;
use App\Helpers\Twt\Wild_tiger;

class UniqueLookupValue implements Rule
{
	private $requestUserId;
	private $requestData = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request = [])
    {
    	$this->requestData = $request->all();
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
    		$model = new LookupMaster();
    		
    		$value = trim($value);
    		
    		$where = [];
    		
    		if( isset($this->requestData['lookup_module_name']) &&  (!empty($this->requestData['lookup_module_name'])) ){
    			$where['v_module_name'] = trim($this->requestData['lookup_module_name']);
    		}
    		
    		if( isset($this->requestData['module_value']) &&  (!empty($this->requestData['module_value'])) ){
    			$where['v_value'] = trim($this->requestData['module_value']);
    		}
    	
    		if(!empty($where)){
    			$where['t_is_deleted'] = 0;
    			$where['singleRecord'] = true;
    			
    			if( isset($this->requestData['record_id']) && (!empty($this->requestData['record_id'])) ){
    				$where['i_id !='] = (int)Wild_tiger::decode($this->requestData['record_id']);
    			}
    			
    			$getRecordInfo = $model->getRecordDetails($where);
    			
    			if(!empty($getRecordInfo)){
    				return false;
    			} else {
    				return true;
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
    	$moduleName = ( isset($this->requestData['lookup_module_name']) ? enumText($this->requestData['lookup_module_name']) : null );
    	
    	return trans ( 'messages.duplicate-module-value', [ 'module' => enumText($moduleName) ] ) ;
    	
    }
}
