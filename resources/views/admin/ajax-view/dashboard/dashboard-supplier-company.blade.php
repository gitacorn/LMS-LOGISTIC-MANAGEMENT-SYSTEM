<?php 
$index = 1;
if(isset($supplierDetails) && (!empty($supplierDetails)) ){
	$totalUnits = $totalBoxes = $totalPallets = $totalAmount = 0;
?>
<tr>
	<th class="sr-col align-middle">{{ trans("messages.sr-no") }}</th>
	<th class="text-left align-middle" style="min-width:200px;max-width:200px;">{{ trans("messages.company") }}</th>
	<th class="text-left align-middle" style="min-width:200px;max-width:200px;">{{ trans("messages.supplier") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.units") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.box") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.pallet") }}</th>
	<th class="text-left align-middle min-td-100">{{ trans("messages.value-vat") }}</th>
</tr>
<?php 
   if( ( count($supplierDetails) > 0 )){
	foreach ($supplierDetails as $key => $supplierDetail){
		if( !empty($supplierDetail['total_units']) || (!empty($supplierDetail['po_amount_with_vat_gbp']))   ){
			$totalUnits += $supplierDetail['total_units'];
			$totalAmount += $supplierDetail['po_amount_with_vat_gbp'];
		}
		if(!empty($supplierDetail['total_boxes']) || (!empty($supplierDetail['total_pallets']))){
			$totalBoxes += (isset($supplierDetail['total_boxes']) ? $supplierDetail['total_boxes']  : 0);
			$totalPallets += ( isset($supplierDetail['total_pallets']) ? $supplierDetail['total_pallets'] : 0  );
		}
	?>
		<tr>
			<td class="sr-col text-center">{{ $index ++ }}</td>
			<td>{{ ( $key ) }}</td>
			<td class="min-td-100">{{ ( isset($supplierDetail['total_units']) && (!empty($supplierDetail['total_units'])) ?  decimalAmount($supplierDetail['total_units']) : 0 ) }}</td>
			<td class="min-td-100">{{ ( isset($supplierDetail['total_boxes']) && (!empty($supplierDetail['total_boxes']))  ?  decimalAmount($supplierDetail['total_boxes']) : 0 ) }}</td>
			<td class="min-td-100">{{ ( isset($supplierDetail['total_pallets']) && (!empty($supplierDetail['total_pallets'])) ?  decimalAmount($supplierDetail['total_pallets']) : 0 ) }}</td>
			<td class="min-td-100">{{ ( isset($supplierDetail['po_amount_with_vat_gbp']) && (!empty($supplierDetail['po_amount_with_vat_gbp'])) ?  decimalAmount($supplierDetail['po_amount_with_vat_gbp']) : 0 ) }}</td>
		</tr>
<?php } ?> 
<tr class="total-tr">
	<td class="text-center td-bg table-title" colspan="2">{{ trans("messages.total")}}</td>
	<td class="min-td-100 td-bg table-title">{{ decimalAmount($totalUnits) }}</td>
	<td class="min-td-100 td-bg table-title">{{ decimalAmount($totalBoxes) }}</td>
	<td class="min-td-100 td-bg table-title">{{ decimalAmount($totalPallets) }}</td>
	<td class="min-td-100 td-bg table-title">{{ decimalAmount($totalAmount) }}</td>
</tr>
<?php 
	} else { ?>
		<tr>
			<td colspan="6" class="text-center">{{ trans('messages.no-record-found') }}</td>
		</tr>
	<?php }
} 
?>
