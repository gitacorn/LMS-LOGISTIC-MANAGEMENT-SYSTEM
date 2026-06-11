<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\WarehousePalletMasterModel;
use App\GoodInBuyerMasterModel;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Twt\Wild_tiger;
class ValidatePalletLimit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
	protected $input;
    public function __construct(array $input)
    {
        $this->input = $input;
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
    	$deliveryLocation = (!empty($this->input['warehouse']) ? (int)Wild_tiger::decode($this->input['warehouse']) : 0);
        $buyerDate = (!empty($this->input['buyer_delivery_date']) ? dbDate($this->input['buyer_delivery_date']) : null);
		$palletType = (!empty($this->input['record_type']) ? $this->input['record_type'] : null);
        $recordId = (!empty($this->input['record_id']) ? (int)Wild_tiger::decode($this->input['record_id']) : 0);
		
		
        if(empty($palletType) || $palletType != config('constants.PALLET')){
        	return true;
        }
		
        $whereWareHouseData = [
            'dt_pallet_date' => $buyerDate,
            'i_warehouse_id' => $deliveryLocation,
            'singleRecord' => true,
        ];

        $warehouseModel = new WarehousePalletMasterModel();
        $wareHousePalletLimit = $warehouseModel->getRecordDetails($whereWareHouseData);

        if (empty($wareHousePalletLimit) || !isset($wareHousePalletLimit->i_pallet_limit)) {
            return true;
        }
		
        $whereBuyerData = [];
        $whereBuyerData['gdm.i_id !='] = $recordId;
        $whereBuyerData['gdm.i_delivery_location_id'] = $deliveryLocation;
        $whereBuyerData['gdm.dt_delivery_date'] = $buyerDate;
        $buyerModel = new GoodInBuyerMasterModel();
        $goodInBuyerPallets = $buyerModel->getTotalLimitWareHouse($whereBuyerData)->first();
		
        $totalAvailableLimit = $wareHousePalletLimit->i_pallet_limit;
        
        if( isset($goodInBuyerPallets->total_pallets) ){
	        $totalAvailableLimit = $wareHousePalletLimit->i_pallet_limit - $goodInBuyerPallets->total_pallets;
        }
        
		return $value <= $totalAvailableLimit;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.pallet-limit-validation');
    }
}
