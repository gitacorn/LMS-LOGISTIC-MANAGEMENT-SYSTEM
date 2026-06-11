<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\CountryToPortGoodsOutModel;

class UniquePersonalReferenceNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
	private $recordId;
    public function __construct($recordId = null)
    {
        if ($recordId > 0){
        	$this->recordId = $recordId;
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
        if (!empty($value)){
        	$where['t_is_deleted'] = 0;
        	$where['v_personal_ref'] = trim($value);
        	
        	$result = CountryToPortGoodsOutModel::where($where);
        	if ($this->recordId > 0){
        		$result->where('i_id' , '!=' , $this->recordId); 
        	}
        	
        	if (!empty($result->first())){
        		return false;
        	}else {
        		return true;
        	}
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.unique-personal-reference-number');
    }
}
