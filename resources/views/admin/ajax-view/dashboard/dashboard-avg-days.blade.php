<tr>
	<th class="sr-col align-middle" style="width: 40px; min-width: 40px; max-width: 40px;">{{ trans("messages.sr-no") }}</th>
	<th class="text-left align-middle" style="min-width:170px;max-width:170px;">{{ trans("messages.warehouse") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.pallet") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.avg-days") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.avg-cost-pallet") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.total-cost") }}</th>
</tr>
<?php 
if( isset($averageDetails) && (!empty($averageDetails)) && count($averageDetails) > 0  ){
	foreach ($averageDetails as $key => $averageDetail){ ?>
		<tr>
			<td class="sr-col text-center" style="width: 40px; min-width: 40px; max-width: 40px;">{{ ++$key }}</td>
			<td>{{ (isset($averageDetail->v_warehouse_name) && (!empty($averageDetail->v_warehouse_name)) ? ($averageDetail->v_warehouse_name) . ( isset($averageDetail->v_warehouse_code) && (!empty($averageDetail->v_warehouse_code)) ? ' ('.$averageDetail->v_warehouse_code.')' : '' ) : null ) }}</td>
			<td class="min-td-100">{{ (isset($averageDetail->total_pallets) && (!empty($averageDetail->total_pallets)) ? decimalAmount($averageDetail->total_pallets) : 0 ) }}</td>
			<td class="min-td-100">{{ (isset($averageDetail->total_date_diff) && (!empty($averageDetail->total_date_diff)) ? decimalAmount($averageDetail->total_date_diff) : 0 ) }}</td>
			<td class="min-td-100">{{ (isset($averageDetail->invoice_per_pallet) && (!empty($averageDetail->invoice_per_pallet)) ? decimalAmount($averageDetail->invoice_per_pallet) : 0 ) }}</td>
			<td class="min-td-100">{{ (isset($averageDetail->total_invoice_amount) && (!empty($averageDetail->total_invoice_amount)) ? decimalAmount($averageDetail->total_invoice_amount) : 0 ) }}</td>
		</tr>
<?php }
} else { ?>
	<tr>
		<td colspan="6" class="text-center">{{ trans('messages.no-record-found') }}</td>
	</tr>
<?php }?>                                 