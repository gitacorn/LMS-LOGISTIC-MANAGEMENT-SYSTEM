<?php 
if(isset($dateArray) && isset($buyerDetails)){ ?>
	<tr>
		<th class="sr-col text-center align-middle sticky-left border-color" rowspan="2"> <div class="border-color-div border-right-0 text-center justify-content-center"> {{ trans("messages.sr-no") }}</div></th>
		<th class="title-row text-center align-middle sticky-left sticky-sr border-color" rowspan="2"> <div class="border-color-div text-center justify-content-center"> {{ trans("messages.warehouse-date") }}</div></th>
		<?php 
		if(isset($dateArray) && !empty($dateArray)){
			foreach ($dateArray as $key => $date){
				?>
			<th class="text-left text-center align-middle border-color" colspan="4" onclick="openBuyerDelivery('', '{{ $date }}')" style="cursor: pointer; color: #007bff; text-decoration: underline;" title="Click to view all delivery data for {{ $date }}">{{ $date }}</th>
				<?php 
			}	
		?>
		<?php }?>
	</tr>
	<tr>
		<?php
		if(isset($dateArray) && (!empty($dateArray))){
			foreach ($dateArray as $key => $date){
				?>
				<td class="table-title border-color">{{ trans("messages.units") }}</td>
				<td class="table-title border-color">{{ trans("messages.box") }}</td>
				<td class="table-title border-color">{{ trans("messages.pallet") }}</td>
				<td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
				<?php 
			}
		}
		?>
	</tr>
	<?php 
	if(!empty($buyerDetails)){
		$index = 1;
		foreach ($buyerDetails as $key => $buyerDetail){
			?>
			<tr>
				<td class="sr-col text-center td-bg sticky-left border-color"> <div class="border-color-div border-right-0 border-top-0 text-center justify-content-center"> {{ $index++ }}</div></td>
				<td class="td-bg table-title sticky-left sticky-sr border-color"> 
    <div class="border-color-div border-top-0 clickable-warehouse" onclick="openBuyerDelivery('{{ $key }}', '')" style="cursor: pointer; color: #007bff; text-decoration: underline;" title="Click to view all {{ $key }} delivery data">
        {{ $key }}
    </div>
</td>
				<?php 
					
					if(isset($dateArray) && (!empty($dateArray))){
						foreach ($dateArray as $date){
							if(isset($buyerDetail['data'][$date])){
								$data =  $buyerDetail['data'][$date];
								?>
								<td class="min-td-100 clickable-data" onclick="openBuyerDelivery('{{ $key }}', '{{ $date }}')" style="cursor: pointer;">{{ decimalAmount($data['total_units']) }}</td>
								<td class="min-td-100 clickable-data" onclick="openBuyerDelivery('{{ $key }}', '{{ $date }}')" style="cursor: pointer;">{{ (decimalAmount($data['total_boxes'])) }} </td>
								<td class="min-td-100 clickable-data" onclick="openBuyerDelivery('{{ $key }}', '{{ $date }}')" style="cursor: pointer;">
									{{ decimalAmount($data['total_pallets']) }}
									({{ (isset($data['pallet_limit']) && $data['pallet_limit'] !== '') ? $data['pallet_limit'] : '-' }})
								</td>
								<td class="min-td-100 border-color-right clickable-data" onclick="openBuyerDelivery('{{ $key }}', '{{ $date }}')" style="cursor: pointer;">{{ '£' . decimalAmount($data['po_amount_with_vat_gbp']) }}</td>
								<?php 
							} else {
								?>
								<td class="min-td-100">-</td>
								<td class="min-td-100">-</td>
								<td class="min-td-100">-</td>
								<td class="min-td-100 border-color-right">-</td>
								<?php 
							}
						}
					}
					
				?>
				
			</tr>
			<?php 
		}
	} else {
		?>
		<td class="min-td-100 text-center" colspan="30">{{ trans("messages.no-record-found") }}</td>
		<?php 
	}
}
?>