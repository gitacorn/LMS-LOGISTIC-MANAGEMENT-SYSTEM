<?php
if(count($getFbaRecordDetails) > 0 ){
	$index = 1;
	?>
	@foreach ($getFbaRecordDetails as $getFbaRecordDetail)
		<?php
			$encodeRecordId = Wild_tiger::encode($getFbaRecordDetail->i_id);
			$encodeMultipleRecordIdsArray = isset($getFbaRecordDetail->fba_ids) && !empty($getFbaRecordDetail->fba_ids) ? explode(',', $getFbaRecordDetail->fba_ids) : [];
			
			$encodeMultipleRecordIds = [];
			
			$encodeMultipleRecordIds = array_map(function($encodeMultipleRecordId) {
				return Wild_tiger::encode($encodeMultipleRecordId);
			}, $encodeMultipleRecordIdsArray);
			
			$encodeRecordId = isset($encodeMultipleRecordIds) && !empty($encodeMultipleRecordIds) ? implode(',', $encodeMultipleRecordIds) : $encodeRecordId;
			
			$checkboxIds = ( isset($recordInfo) && !empty($recordInfo->v_fba_sheet_ids) ? explode(',', $recordInfo->v_fba_sheet_ids) : [] );
			$detailInfo = ( isset($recordInfo) && !empty($recordInfo->detailInfo) ? collect($recordInfo->detailInfo)->where('i_fba_sheet_detail_id', $getFbaRecordDetail->i_id)->first() : [] );
			$rowIndex = $index++;
			
			$checked = "";
			if( isset($checkboxIds) && (in_array($getFbaRecordDetail->i_id, $checkboxIds)) ) {
				$checked = "checked='checked'";
			}
			$additionalCompanyCode = $additionalLocationCode = "";
			switch($getFbaRecordDetail->e_destination){
				case config('constants.AMAZON_FBA_SHEET'):
					if(isset($getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_short_code) && (!empty($getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_short_code)) ){
						$additionalCompanyCode = '<br> (' .$getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_name.' )';
					}
					if(isset($getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_code) && (!empty($getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_code)) ){
						$additionalLocationCode = '<br> (' .$getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_name.' )';
					}
					break;
				case config('constants.WAREHOUSE_FBA_SHEET'):
					if(isset($getFbaRecordDetail->wareHouseUSACodeInfo->v_warehouse_code) && (!empty($getFbaRecordDetail->wareHouseUSACodeInfo->v_warehouse_code)) ){
						$additionalLocationCode = '<br> (' .$getFbaRecordDetail->wareHouseUSACodeInfo->v_warehouse_name.' )';
					}
					break;
				case config('constants.CUSTOMER_FBA_SHEET'):
					if(isset($getFbaRecordDetail->customerCustomerNameInfo->v_customer_name) && (!empty($getFbaRecordDetail->customerCustomerNameInfo->v_customer_name)) ){
						$additionalCompanyCode = '<br> (' .$getFbaRecordDetail->customerCustomerNameInfo->v_customer_name.' )';
					}
					if(isset($getFbaRecordDetail->customerCustomerCodeInfo->v_customer_code) && (!empty($getFbaRecordDetail->customerCustomerCodeInfo->v_customer_code)) ){
						if( isset($getFbaRecordDetail->customerCustomerCodeInfo->customerMaster->v_customer_name) && (!empty($getFbaRecordDetail->customerCustomerCodeInfo->customerMaster->v_customer_name)) ){
							$additionalLocationCode = '<br> (' .$getFbaRecordDetail->customerCustomerCodeInfo->customerMaster->v_customer_name.' )';
						}
					}
					break;
					
			}
		?>
		<tr>
			<td class="text-center">
				<input type="hidden" id="container_type_{{ $rowIndex }}" name="container_type_{{ $rowIndex }}" value="{{ config('constants.USA_CONTAINER_CLUBBING_FBA_RECORD') }}">
				<div class="form-check form-check-inline pt-2 pb-2 mr-0">
					<input class="form-check-input usa-container-clubbing-selection" type="checkbox" id="checkbox_{{ $rowIndex }}" name="checkbox_{{ $rowIndex}}" value="{{ $encodeRecordId }}" {{ $checked }} <?php echo $disableForm ?> onchange="countTotalRecord()">
					<label class="form-check-label" for="checkbox_{{ $rowIndex }}"></label>
				</div>
			</td>
			<td class="text-center" style="width:70px;min-width:70px;">{{ $rowIndex}}</td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no) ? $getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_company_code) ? $getFbaRecordDetail->v_company_code . $additionalCompanyCode : '')?></td>
			<td class="text-left search-fba-id"><?php echo (!empty($getFbaRecordDetail->v_fba_po_no) ? $getFbaRecordDetail->v_fba_po_no : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_ref_id) ? $getFbaRecordDetail->v_ref_id : '')?></td>
			<td class="text-left"><?php echo (isset($getFbaRecordDetail->fbaSheetMaster) && isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster) && !empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_personal_ref) ? $getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_personal_ref : '')?></td>
			<?php /* ?>
			<td class="text-left"><?php echo ( isset($statusMasterInfo) && !empty($statusMasterInfo) && isset($statusMasterInfo->v_status) && !empty($statusMasterInfo->v_status) ? $statusMasterInfo->v_status : '-')?></td>
			<?php */ ?>
			<td class="text-left">
			<?php
				if(isset($getFbaRecordDetail->fbaSheetMaster) && isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster) && isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo) && isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel) && isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel->ownLocation) && !empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel->ownLocation)) {
					echo (isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel->ownLocation) && !empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel->ownLocation) && !empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel->ownLocation->v_warehouse_name) ? $getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->PortToContainerMasterInfoModel->ownLocation->v_warehouse_name : '');
				} elseif(isset($getFbaRecordDetail->AgentToWarehouseDetailInfo) && isset($getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse) && isset($getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse->toWarehouseInfo) && !empty($getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse->toWarehouseInfo)){
					echo (isset($getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse->toWarehouseInfo) && !empty($getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse->toWarehouseInfo) && !empty($getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse->toWarehouseInfo->v_warehouse_name) ? $getFbaRecordDetail->AgentToWarehouseDetailInfo->agentToWarehouse->toWarehouseInfo->v_warehouse_name : '');
				}
			?>
			</td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_location_code) ? $getFbaRecordDetail->v_location_code . $additionalLocationCode : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_sku) ? $getFbaRecordDetail->v_sku : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_units) ? $getFbaRecordDetail->v_units : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_product) ? $getFbaRecordDetail->v_product : '')?></td>
			<td class="text-left">{{ trans("messages.pallet") }} - <?php echo (!empty($getFbaRecordDetail->total_pallet) ? $getFbaRecordDetail->total_pallet : '0') ?></td>
			<td class="text-left">
				<select name="final_boxes_pallets_{{ $rowIndex }}" class="form-control final-boxes-pallets" onchange="countTotalRecord()" readOnly disabled>
					<option value="">{{ trans("messages.select") }}</option>
					<?php 
					if(isset($boxPalletDetails) && !empty($boxPalletDetails)){
						foreach ($boxPalletDetails as $boxPalletKey => $boxPalletValue){
							$selected = '';
							$existingValue = (isset($detailInfo) && !empty($detailInfo->e_final_box_pallet_type) ? $detailInfo->e_final_box_pallet_type : config('constants.PALLET'));
							if( $existingValue == $boxPalletKey ){
								$selected = "selected='selected'";
							}
							?>
							<option value="{{ $boxPalletKey }}" {{ $selected }}><?php echo  (!empty($boxPalletValue) ? $boxPalletValue : '' ) ?></option>
							<?php 
						}
					}
				?>
				</select>
			</td>
			<td class="text-left"><input type="text" name="number_of_box_pallet_{{ $rowIndex }}" class="form-control number-of-box-pallet" placeholder="{{ trans('messages.number-of-box-pallet') }}" onchange="onlyNumber(this),countTotalRecord()" onkeyup="onlyNumber(this),countTotalRecord()" value="{{ (isset($detailInfo) && !empty($detailInfo->i_number_of_box_pallet) ? $detailInfo->i_number_of_box_pallet : (!empty($getFbaRecordDetail->total_pallet) ? $getFbaRecordDetail->total_pallet : '1') ) }}" <?php echo $disableForm ?> readOnly></td>
        </tr>                                                  
	@endforeach
<?php } ?>
                                                         