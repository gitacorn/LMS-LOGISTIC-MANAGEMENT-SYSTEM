<?php
if(count($getFbaRecordDetails) > 0 ){
	$index = 0;
	foreach ($getFbaRecordDetails as $getFbaRecordDetail){
		$encodeRecordId = Wild_tiger::encode($getFbaRecordDetail->i_id);
		$checkBoxValue = ( isset($recordInfo->detailInfo) ? json_decode(json_encode($recordInfo->detailInfo),true) : [] );
		$checkboxId = (!empty($checkBoxValue) ? (array_column($checkBoxValue, 'i_fba_sheet_detail_id')) :[] );
		
		$checked = "";
		if( isset($checkboxId) && (in_array($getFbaRecordDetail->i_id, $checkboxId)) ) {
			$checked = "checked='checked'";
		}
		$disabled = '';
		if (!empty($getFbaRecordDetail->e_usa_container_clubbing_status) && $getFbaRecordDetail->e_usa_container_clubbing_status == config('constants.COMPLETED_STATUS')){
			$disabled = 'disabled';
		}
		
		$additionalCompanyCode = $additionalLocationCode = "";
		switch($getFbaRecordDetail->e_destination){
			case config('constants.AMAZON_FBA_SHEET'):
				if(isset($getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_short_code) && (!empty($getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_short_code)) ){
					$additionalCompanyCode = '<br> (' .$getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_name.')';
				}
				if(isset($getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_code) && (!empty($getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_code)) ){
					$additionalLocationCode = '<br> (' .$getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_name.')';
				}
				break;
			case config('constants.WAREHOUSE_FBA_SHEET'):
				if(isset($getFbaRecordDetail->wareHouseUSACodeInfo->v_warehouse_code) && (!empty($getFbaRecordDetail->wareHouseUSACodeInfo->v_warehouse_code)) ){
					$additionalLocationCode = '<br> (' .$getFbaRecordDetail->wareHouseUSACodeInfo->v_warehouse_name.')';
				}
				break;
			case config('constants.CUSTOMER_FBA_SHEET'):
				if(isset($getFbaRecordDetail->customerCustomerNameInfo->v_customer_name) && (!empty($getFbaRecordDetail->customerCustomerNameInfo->v_customer_name)) ){
					$additionalCompanyCode = '<br> (' .$getFbaRecordDetail->customerCustomerNameInfo->v_customer_name.')';
				}
				if(isset($getFbaRecordDetail->customerCustomerCodeInfo->v_customer_code) && (!empty($getFbaRecordDetail->customerCustomerCodeInfo->v_customer_code)) ){
					if( isset($getFbaRecordDetail->customerCustomerCodeInfo->customerMaster->v_customer_name) && (!empty($getFbaRecordDetail->customerCustomerCodeInfo->customerMaster->v_customer_name)) ){
						$additionalLocationCode = '<br> (' .$getFbaRecordDetail->customerCustomerCodeInfo->customerMaster->v_customer_name.')';
					}
				}
				break;
				
		}
		?>
		<tr >
			<td class="text-center">
	         	<div class="form-check form-check-inline pt-2 pb-2 mr-0">
	            	<input class="form-check-input agent-warehouse-to-amazon-selection" type="checkbox" id="checkbox_<?php echo ($getFbaRecordDetail->i_id)?>" name="checkbox[]" value="<?php echo (!empty($encodeRecordId) ? $encodeRecordId : 0 )?>" <?php echo $checked?> <?php echo (isset($recordInfo) && $recordInfo->i_id > 0 ? '' : 'onchange="getWayToDropdownDetails(this)"')?> data-destination="{{ (!empty($getFbaRecordDetail->e_destination) ? $getFbaRecordDetail->e_destination : '') }}" <?php echo $disabled ?>>
	            	<label class="form-check-label" for="checkbox_<?php echo ($getFbaRecordDetail->i_id)?>"></label>
	          	</div>
			</td>
			<td class="text-center" style="width:70px;min-width:70px;">{{ ++$index }}</td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no) ? $getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_fba_po_no) ? $getFbaRecordDetail->v_fba_po_no : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_ref_id) ? $getFbaRecordDetail->v_ref_id : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_company_code) ? $getFbaRecordDetail->v_company_code . $additionalCompanyCode : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_location_code) ? $getFbaRecordDetail->v_location_code . $additionalLocationCode : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_sku) ? $getFbaRecordDetail->v_sku : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_units) ? $getFbaRecordDetail->v_units : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_pallet) ? $getFbaRecordDetail->v_pallet : '')?></td>
			
        </tr>                                                  
	<?php 
	}
} else {
	?>
	<tr>
		<td colspan="10" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
<?php 
}
?>
                                                         