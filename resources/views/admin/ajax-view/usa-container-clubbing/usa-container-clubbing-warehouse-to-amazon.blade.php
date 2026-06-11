<?php
if(count($getFbaRecordDetails) > 0 ){
	$index = (isset($startIndex) && $startIndex > 0 ? ($startIndex + 1) : 1);
	?>
	@foreach ($getFbaRecordDetails as $getFbaRecordDetail)
		<?php
			$encodeRecordId = Wild_tiger::encode($getFbaRecordDetail->i_id);
			$checkboxIds = ( isset($recordInfo) && !empty($recordInfo->v_usa_warehouse_container_ids) ? explode(',', $recordInfo->v_usa_warehouse_container_ids) : [] );
			$detailInfo = ( isset($recordInfo) && !empty($recordInfo->detailInfo) ? collect($recordInfo->detailInfo)->where('i_fba_sheet_detail_id', $getFbaRecordDetail->i_id)->first() : [] );
			$rowIndex = $index++;
			
			$checked = "";
			if( isset($checkboxIds) && (in_array($getFbaRecordDetail->i_id, $checkboxIds)) ) {
				$checked = "checked='checked'";
			}
		?>
		<tr>
			<td class="text-center">
				<input type="hidden" id="container_type_{{ $rowIndex }}" name="container_type_{{ $rowIndex }}" value="{{ config('constants.USA_CONTAINER_CLUBBING_NOT_FBA_RECORD') }}">
				<div class="form-check form-check-inline pt-2 pb-2 mr-0">
					<input class="form-check-input usa-container-clubbing-selection" type="checkbox" id="checkbox_{{ $rowIndex }}" name="checkbox_{{ $rowIndex }}" value="{{ $encodeRecordId }}" {{ $checked }} <?php echo $disableForm ?> onchange="countTotalRecord()">
					<label class="form-check-label" for="checkbox_{{ $rowIndex }}"></label>
				</div>
			</td>
			<td class="text-center" style="width:70px;min-width:70px;">{{ $rowIndex }}</td>
			<td class="text-left"><?php echo (isset($getFbaRecordDetail->usWarehouseToAmazonMaster) && !empty($getFbaRecordDetail->usWarehouseToAmazonMaster) && !empty($getFbaRecordDetail->usWarehouseToAmazonMaster->v_us_warehouse_to_amazon_record_no) ? $getFbaRecordDetail->usWarehouseToAmazonMaster->v_us_warehouse_to_amazon_record_no : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->accountComapnyInfo->v_company_name) ? $getFbaRecordDetail->accountComapnyInfo->v_company_name : '')?></td>
			<td class="text-left search-fba-id"><?php echo (!empty($getFbaRecordDetail->v_shipment_id) ? $getFbaRecordDetail->v_shipment_id : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_ref_id) ? $getFbaRecordDetail->v_ref_id : '')?></td>
            <td class="text-left">
                <?php
                // Personal Ref. is not directly on Warehouse-to-Amazon detail; render blank by default to keep column alignment.
                // If in future there is a relationship carrying personal reference, populate it here.
                echo (isset($getFbaRecordDetail->fbaSheetMaster) && isset($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster) && !empty($getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_personal_ref)
                    ? $getFbaRecordDetail->fbaSheetMaster->countryToPortMaster->v_personal_ref
                    : '');
                ?>
            </td>
			<?php /* ?>
			<td class="text-left"><?php echo ( isset($statusMasterInfo) && !empty($statusMasterInfo) && isset($statusMasterInfo->v_status) && !empty($statusMasterInfo->v_status) ? $statusMasterInfo->v_status : '-')?></td>
			<?php */ ?>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->amazonFromWarehouseInfo->v_warehouse_name) ? $getFbaRecordDetail->amazonFromWarehouseInfo->v_warehouse_name : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->toAmazonLocationInfo->v_warehouse_name) ? $getFbaRecordDetail->toAmazonLocationInfo->v_warehouse_name : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_sku) ? $getFbaRecordDetail->v_sku : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_units) ? $getFbaRecordDetail->v_units : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->v_product) ? $getFbaRecordDetail->v_product : '')?></td>
			<td class="text-left"><?php echo (!empty($getFbaRecordDetail->e_box_pallet_type) ? $getFbaRecordDetail->e_box_pallet_type : '')?> - <?php echo (!empty($getFbaRecordDetail->i_total_no_of_pallets) ? $getFbaRecordDetail->i_total_no_of_pallets : '0') ?></td>
			<td class="text-left">
				<select name="final_boxes_pallets_{{ $rowIndex }}" class="form-control final-boxes-pallets" <?php echo $disableForm ?> onchange="countTotalRecord()" {{ (!empty($getFbaRecordDetail->e_box_pallet_type) && $getFbaRecordDetail->e_box_pallet_type == config('constants.PALLET') ? 'readonly' . (isset($disableForm) && !empty($disableForm) ? '' : ' disabled') : '') }}>
					<option value="">{{ trans("messages.select") }}</option>
					<?php 
					if(isset($boxPalletDetails) && !empty($boxPalletDetails)){
						foreach ($boxPalletDetails as $boxPalletKey => $boxPalletValue){
							$selected = '';
							$existingValue = (isset($detailInfo) && !empty($detailInfo->e_final_box_pallet_type) ? $detailInfo->e_final_box_pallet_type : (!empty($getFbaRecordDetail->e_box_pallet_type) && $getFbaRecordDetail->e_box_pallet_type == config('constants.PALLET') ? config('constants.PALLET') : ''));
							if( !empty($existingValue) && $existingValue == $boxPalletKey ){
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
			<td class="text-left"><input type="text" name="number_of_box_pallet_{{ $rowIndex }}" class="form-control number-of-box-pallet" placeholder="{{ trans('messages.number-of-box-pallet') }}" onchange="onlyNumber(this),countTotalRecord()" onkeyup="onlyNumber(this),countTotalRecord()" value="{{ (isset($detailInfo) && !empty($detailInfo->i_number_of_box_pallet) ? $detailInfo->i_number_of_box_pallet : (!empty($getFbaRecordDetail->i_total_no_of_pallets) ? $getFbaRecordDetail->i_total_no_of_pallets : '1')) }}" <?php echo $disableForm ?> {{ (!empty($getFbaRecordDetail->e_box_pallet_type) && $getFbaRecordDetail->e_box_pallet_type == config('constants.PALLET') ? 'readonly' : '') }}></td>
        </tr>                                                  
	@endforeach
	<?php 
} else if(isset($startIndex) && $startIndex == 0) {
	?>
	<tr>
		<td colspan="16" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
<?php 
}
?>
                                                         