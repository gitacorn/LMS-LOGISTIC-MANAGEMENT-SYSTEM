<?php 
if (count($recordDetails) > 0) {
	$index = 1;
	foreach ($recordDetails as $fbaRecord) {
		$fbaStatus = "";
		if((isset($fbaRecord->fbaSheetMaster->countryToPortMaster->uploadFBASheetInfo->e_status)) && ($fbaRecord->fbaSheetMaster->countryToPortMaster->uploadFBASheetInfo->e_status == config("constants.SUCCESS_STATUS"))){
			$containerStatus = (!empty($fbaRecord->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->e_container_status) ? $fbaRecord->fbaSheetMaster->countryToPortMaster->portToAgentaContainerInfo->e_container_status :'');
			
			switch ($containerStatus){
				case config('constants.PENDING_STATUS') :
					$fbaStatus = config("constants.PORT_TO_AGENT_WAREHOUSE_NO");
					break;
				case config('constants.PARTIAL_DELIVERY_TYPE') :
					$fbaStatus = config("constants.AGENT_WAREHOUSE_TO_AMAZON_NO");
					break;
				case config('constants.COMPLETED_STATUS') :
					$fbaStatus = config("constants.AGENT_WAREHOUSE_TO_AMAZON_COMPLETED_NO");
					break;
				default :
					$fbaStatus = config("constants.UK_OTHER_COUNTRY_TO_PORT_NO");
			}
		}
		?>
		<tr>
			<td class="text-center">{{ $index++ }}</td>
			<td>{{ (!empty($fbaRecord->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no) ? $fbaRecord->fbaSheetMaster->countryToPortMaster->v_country_to_port_record_no : '') }}</td>
			<td>{{ (!empty($fbaRecord->fbaSheetMaster->countryToPortMaster->v_container_air_waybill_no) ? $fbaRecord->fbaSheetMaster->countryToPortMaster->v_container_air_waybill_no .(!empty($fbaStatus) ? ' (' .$fbaStatus .')' : ''): '') }}</td>
			<td>{{ (!empty($fbaRecord->fbaSheetMaster->countryToPortMaster->e_transport_way) ? $fbaRecord->fbaSheetMaster->countryToPortMaster->e_transport_way : '') }}</td>
			<td>{{ (!empty($fbaRecord->fbaSheetMaster->countryToPortMaster->fromPortInfo->v_warehouse_name) ? $fbaRecord->fbaSheetMaster->countryToPortMaster->fromPortInfo->v_warehouse_name : '') }}</td>
			<td>{{ (!empty($fbaRecord->fbaSheetMaster->countryToPortMaster->toPortInfo->v_warehouse_name) ? $fbaRecord->fbaSheetMaster->countryToPortMaster->toPortInfo->v_warehouse_name : '') }}</td>
			<td>{{ (!empty($fbaRecord->v_fba_po_no) ? $fbaRecord->v_fba_po_no : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->e_destination) ? $fbaRecord->e_destination : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_ref_id) ? $fbaRecord->v_ref_id : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_company_code) ? $fbaRecord->v_company_code . (isset($fbaRecord->amazonCompanyShortCodeInfo->v_company_name) ? ' (' . $fbaRecord->amazonCompanyShortCodeInfo->v_company_name . ')'  : '') : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_product) ? $fbaRecord->v_product : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_fba_value) ? ($fbaRecord->v_fba_value) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_location_code) ? $fbaRecord->v_location_code . (isset($fbaRecord->amazonLocationCodeInfo->v_warehouse_name) ? ' (' . $fbaRecord->amazonLocationCodeInfo->v_warehouse_name . ')'  : '') : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_sku) ? $fbaRecord->v_sku : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_units) ? decimalAmount($fbaRecord->v_units) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_amazon_address) ? $fbaRecord->v_amazon_address : '') }}</td>
			<td class="">{{ (!empty($fbaRecord->i_boxes_units) ? decimalAmount($fbaRecord->i_boxes_units) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_boxes) ? decimalAmount($fbaRecord->v_boxes) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_pallet) ? $fbaRecord->v_pallet : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->i_total_no_of_pallets) ? decimalAmount($fbaRecord->i_total_no_of_pallets) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_pallet_dimension) ? $fbaRecord->v_pallet_dimension : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->v_pallet_weight) ? decimalAmount($fbaRecord->v_pallet_weight) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->i_pallet_no) ? decimalAmount($fbaRecord->i_pallet_no) : '') }}</td>
			<td class="text-left">{{ (!empty($fbaRecord->e_status) ? ($fbaRecord->e_status) : '') }}</td>
			
		</tr>
	<?php
	}
} else {
	?>
	<tr>
		<td colspan="24" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
<?php
}
?>
@include('admin/common-display-count')