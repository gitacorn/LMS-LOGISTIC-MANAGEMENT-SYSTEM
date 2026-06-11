<?php

if (count($getFbaRecordDetails) > 0) {
	$index = 0;
	foreach ($getFbaRecordDetails as $getFbaRecord) {
		$fbaRecordIndex = 1;
?>
		<tbody class="border-class fba-recod-start">
			<?php
			foreach ($getFbaRecord as $getFbaRecordDetail) {
				$getFbaRecordDetail = (object)$getFbaRecordDetail;
				$additionalClass = "";
				if ($fbaRecordIndex == 1) {
					$additionalClass = "border-class";
				}
			?>
				<tr class="<?php echo (($fbaRecordIndex == 1) ? 'view-fb-record' : '') ?>">
					<td class="text-center sr-col" style="max-width:150px;min-width:100px;">
						<?php echo ++$index ?>
						<?php //if( session()->get('role') == config('constants.ROLE_ADMIN')){
						?>
						<?php if ($getFbaRecordDetail->e_status == config('constants.PENDING_STATUS')) { ?>
							<div class="d-flex align-items-center justify-content-center">
								<?php if (checkPermission(config('permission_constants.ADD_FBA_SHEET_MASTER')) != false) { ?>
									<button type="button" title='{{ trans("messages.clone") }}' data-designation-type='{{ $getFbaRecordDetail->e_destination }}' data-fba-master-id="<?php echo Wild_tiger::encode($getFbaRecordDetail->i_fba_sheet_master_id) ?>" data-fba-no="<?php echo (!empty($getFbaRecordDetail->v_fba_po_no) ? $getFbaRecordDetail->v_fba_po_no : '') ?>" data-ref-no="<?php echo (!empty($getFbaRecordDetail->v_ref_id) ? $getFbaRecordDetail->v_ref_id : '') ?>" class="btn btn-sm btn-success mb-1 mr-1" onclick="editFBASheetModel(this);" data-record-id="<?php echo (!empty($getFbaRecordDetail->i_id) ? Wild_tiger::encode($getFbaRecordDetail->i_id) : 0) ?>"><i class="fas fa-fw fa-clone"></i></button>
								<?php } ?>
								<?php if (checkPermission(config('permission_constants.EDIT_FBA_SHEET_MASTER')) != false) { ?>
									<button type="button" title='{{ trans("messages.edit-record") }}' data-fba-master-id="<?php echo Wild_tiger::encode($getFbaRecordDetail->i_fba_sheet_master_id) ?>" data-fba-no="<?php echo (!empty($getFbaRecordDetail->v_fba_po_no) ? $getFbaRecordDetail->v_fba_po_no : '') ?>" data-ref-no="<?php echo (!empty($getFbaRecordDetail->v_ref_id) ? $getFbaRecordDetail->v_ref_id : '') ?>" class="btn btn-sm btn-info mb-1 mr-1" onclick="editFBASheetModel(this)" data-status='<?php echo config('constants.SELECTION_YES') ?>' data-record-id="<?php echo (!empty($getFbaRecordDetail->i_id) ? Wild_tiger::encode($getFbaRecordDetail->i_id) : 0) ?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
								<?php } ?>
								<?php if (checkPermission(config('permission_constants.DELETE_FBA_SHEET_MASTER')) != false) { ?>
									<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="<?php echo (!empty($getFbaRecordDetail->i_id) ? Wild_tiger::encode($getFbaRecordDetail->i_id) : 0) ?>" data-module-name="uk-other-country-us-port" onclick="deleteFBARecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
								<?php } ?>
							</div>
						<?php } ?>
						<?php //} 
						?>
					</td>
					<?php if ($fbaRecordIndex == 1) { ?>
						<td class="text-left common-row-for-no" rowspan="<?php echo count($getFbaRecord) ?>"><?php echo (!empty($getFbaRecordDetail->v_fba_po_no) ? $getFbaRecordDetail->v_fba_po_no : '') ?></td>
					<?php } ?>
					<td class="text-left destination-row"><?php echo (!empty($getFbaRecordDetail->e_destination) ? $getFbaRecordDetail->e_destination : '') ?></td>
					<td class="text-left ref-row"><?php echo (!empty($getFbaRecordDetail->v_ref_id) ? $getFbaRecordDetail->v_ref_id : '') ?></td>
					<td class="text-left company-row"><?php echo (!empty($getFbaRecordDetail->v_company_code) ? $getFbaRecordDetail->v_company_code . (isset($getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_name) ? ' (' . $getFbaRecordDetail->amazonCompanyShortCodeInfo->v_company_name . ')'  : '') : '') ?></td>
					<td class="text-left product-row"><?php echo (!empty($getFbaRecordDetail->v_product) ? $getFbaRecordDetail->v_product : '') ?></td>
					<td class="text-left fba-value-row"><?php echo (!empty($getFbaRecordDetail->v_fba_value) ? ($getFbaRecordDetail->v_fba_value) : '') ?></td>
					<td class="text-left location-code-row"><?php echo (!empty($getFbaRecordDetail->v_location_code) ? $getFbaRecordDetail->v_location_code . (isset($getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_name) ? ' (' . $getFbaRecordDetail->amazonLocationCodeInfo->v_warehouse_name . ')'  : '') : '') ?></td>
					<td class="text-left sku-row"><?php echo (!empty($getFbaRecordDetail->v_sku) ? $getFbaRecordDetail->v_sku : '') ?></td>
					<td class="text-left untits-row"><?php echo (!empty($getFbaRecordDetail->v_units) ? decimalAmount($getFbaRecordDetail->v_units) : '') ?></td>
					<td class="text-left amazon-address-row"><?php echo (!empty($getFbaRecordDetail->v_amazon_address) ? $getFbaRecordDetail->v_amazon_address : '') ?></td>
					<td class="boxes-units"><?php echo (!empty($getFbaRecordDetail->i_boxes_units) ? decimalAmount($getFbaRecordDetail->i_boxes_units) : '') ?></td>
					<td class="text-left boxes-row"><?php echo (!empty($getFbaRecordDetail->v_boxes) ? decimalAmount($getFbaRecordDetail->v_boxes) : '') ?></td>
					<td class="text-left pallet-row"><?php echo (!empty($getFbaRecordDetail->v_pallet) ? $getFbaRecordDetail->v_pallet : '') ?></td>
					<td class="text-left total-no-of-pallet"><?php echo (!empty($getFbaRecordDetail->i_total_no_of_pallets) ? decimalAmount($getFbaRecordDetail->i_total_no_of_pallets) : '') ?></td>
					<td class="text-left pallet-dimension"><?php echo (!empty($getFbaRecordDetail->v_pallet_dimension) ? $getFbaRecordDetail->v_pallet_dimension : '') ?></td>
					<td class="text-left pallet-weight"><?php echo (!empty($getFbaRecordDetail->v_pallet_weight) ? decimalAmount($getFbaRecordDetail->v_pallet_weight) : '') ?></td>
					<td class="text-left pallet-no"><?php echo (!empty($getFbaRecordDetail->i_pallet_no) ? decimalAmount($getFbaRecordDetail->i_pallet_no) : '') ?></td>
					<td class="text-left"><?php echo (!empty($getFbaRecordDetail->e_status) ? ($getFbaRecordDetail->e_status) : '') ?></td>


				</tr>
			<?php
				$fbaRecordIndex++;
			}
			?>
		</tbody>
	<?php
	}
} else {
	?>
	<tr>
		<td colspan="19" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
<?php
}
?>
@include('admin/common-display-count')