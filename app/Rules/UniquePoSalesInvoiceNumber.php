<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\GoodInBuyerMasterModel;

class UniquePoSalesInvoiceNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
	private $recordId;
    public function __construct($recordId = null)
    {
        //
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
        //
        if (!empty($value)){
        	$where['t_is_deleted'] = 0;
        	$where['v_po_sales_invoice_no'] = trim($value);
        	
        	$result = GoodInBuyerMasterModel::where($where);
        	if ($this->recordId > 0){
        		$result->where('i_id' , '!=' , $this->recordId); 
        	}
        	
        	if (count($result->get()) > 0){
        		return false;
        	}else {
        		return true;
        	}
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.unique-po-sales-invoice-number');
    }
}
