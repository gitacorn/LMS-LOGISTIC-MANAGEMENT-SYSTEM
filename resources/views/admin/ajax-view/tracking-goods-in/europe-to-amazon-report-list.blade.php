<?php 
if (count($recordDetails) > 0) {
	$index = 1;
	foreach ($recordDetails as $recordDetail) {
		?>
		<tr>
			<td class="text-center">{{ $index++ }}</td>
			<td>{{ (!empty($recordDetail->v_shipment_id) ? $recordDetail->v_shipment_id : '')  }}</td>
			<td>{{ (!empty($recordDetail->v_ref_id) ? $recordDetail->v_ref_id :'') }}</td>
			<td>{{ (!empty($recordDetail->accountCompany->v_company_name) ? $recordDetail->accountCompany->v_company_name .(!empty($recordDetail->accountCompany->v_company_code) ? ' ('.$recordDetail->accountCompany->v_company_code.')' :'') :'') }}</td>
			<td>{{ (!empty($recordDetail->warehouse->v_warehouse_name) ? $recordDetail->warehouse->v_warehouse_name .(!empty($recordDetail->warehouse->v_warehouse_code) ? ' ('.$recordDetail->warehouse->v_warehouse_code.')' :'') :'') }}</td>
			<td>{{ (!empty($recordDetail->location->v_warehouse_name) ? $recordDetail->location->v_warehouse_name .(!empty($recordDetail->location->v_warehouse_code) ? ' ('.$recordDetail->location->v_warehouse_code.')' :'') :'') }}</td>
			<td>{{ (!empty($recordDetail->v_sku) ? $recordDetail->v_sku :'') }}</td>
			<td>{{ (!empty($recordDetail->v_units) ? $recordDetail->v_units :'') }}</td>
			<td>{{ (!empty($recordDetail->v_price) ? decimalAmount($recordDetail->v_price) :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_booking_date) ? clientDate($recordDetail->dt_booking_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->countryToPortEurope->v_tracking_no) ? $recordDetail->countryToPortEurope->v_tracking_no :'') }}</td>
			<td>{{ (!empty($recordDetail->v_tracking_link) ? $recordDetail->v_tracking_link :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_amazon_appointment_date) ? clientDate($recordDetail->dt_amazon_appointment_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->v_amazon_appointment_id) ? $recordDetail->v_amazon_appointment_id :'') }}</td>
		</tr>
		<?php 		
	}
} else {
	?>
	<tr>
		<td colspan="16" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
<?php
}
?>
@include('admin/common-display-count')