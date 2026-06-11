@if(count($shipmentDetails) > 0 )
	@php
		$index = (isset($importCsvAddRowCount) && $importCsvAddRowCount > 0 ? $importCsvAddRowCount : 0);
	@endphp
	@foreach($shipmentDetails as $shipmentDetail)
 	<tr class="filled-record">
		<td class="table-index text-center">{{ ++$index }}</td>
		<td class="text-left">
			<input type="text" class="form-control invoice-no-customer-record amazon-shipment-id-status"  onchange="checkUniqueShipmentId(this)" name="invoice_no_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['invoice_no']) ? $shipmentDetail['invoice_no'] :'') }}">
		</td>
		<td class="text-left">
			<select name="customer_name_{{ $index }}" class="form-control customer-name-record select2" {{ $disableForm }}>
				<option value="">{{ trans("messages.select") }}</option>
				@if(!empty($customerRecordDetails))
					@foreach($customerRecordDetails as $customerRecordDetail)
						@php   
							$customerRecordDetailEncodedId = Wild_tiger::encode($customerRecordDetail->i_id); 
							$selected =''; 
						@endphp
						@if( (!empty($shipmentDetail['customer_name'])) && ($shipmentDetail['customer_name'] == $customerRecordDetail->v_customer_name) )
							@php $selected="selected='selected'" ; @endphp
						@endif
						<option value="{{ $customerRecordDetailEncodedId }}" {{ $selected }}>{{ (!empty($customerRecordDetail->v_customer_name) ? $customerRecordDetail->v_customer_name : '') }}</option>
					@endforeach
 				@endif
			</select>
		</td>
		<td class="text-left">
			<select name="customer_from_warehouse_{{ $index }}" class="form-control customer-from-warehouse-record" {{ $disableForm }}>
				<option value="">{{ trans("messages.select") }}</option>
				@if(!empty($amazonFromWarehouseDetails))
					@foreach ($amazonFromWarehouseDetails as $amazonFromWarehouseDetail)
						@php
							$amazonFromWarehouseEncodeId =  Wild_tiger::encode($amazonFromWarehouseDetail->i_id);
							$amazonFromWarehouseName = (!empty($amazonFromWarehouseDetail->v_warehouse_name) ? $amazonFromWarehouseDetail->v_warehouse_name . (!empty($amazonFromWarehouseDetail->v_warehouse_code) ? ' ('. $amazonFromWarehouseDetail->v_warehouse_code . ')' : '' )  : '');
							$selected ='';
						@endphp
						@if( (!empty($shipmentDetail['from(us_warehouse)'])) && ($shipmentDetail['from(us_warehouse)'] == $amazonFromWarehouseName) )
							@php $selected="selected='selected'" ; @endphp
						@endif
						<option value="{{ $amazonFromWarehouseEncodeId }}" {{ $selected }}>{{ $amazonFromWarehouseName }}</option>
					@endforeach
 				@endif
			</select>
		</td>
		<td class="text-left">
			<select name="to_customer_{{ $index }}" class="form-control to-customer-record select2" {{ $disableForm }}>
				<option value="">{{ trans("messages.select") }}</option>
				@if(!empty($customerLocationRecordDetails))
					@foreach ($customerLocationRecordDetails as $customerLocationRecordDetail)
						@php 
							$customerLocationRecordDetailId = Wild_tiger::encode($customerLocationRecordDetail->i_id);
							$customerLocationName = (!empty($customerLocationRecordDetail->v_customer_code) ? $customerLocationRecordDetail->v_customer_code .(!empty($customerLocationRecordDetail->v_customer_address) ? ' - ' .$customerLocationRecordDetail->v_customer_address :''): '' );
						 	$selected =''; 
						@endphp
						@if( (!empty($shipmentDetail['to(customer_location)'])) && ($shipmentDetail['to(customer_location)'] == $customerLocationName) )
							@php $selected="selected='selected'" ; @endphp
						@endif
						<option value="{{ $customerLocationRecordDetailId }}" {{ $selected }}>{{ $customerLocationName }}</option>
					@endforeach
 				@endif	
			</select>	
		</td>
		<td class="text-left">
			<input type="text" class="form-control customer-unit-record" name="customer_unit_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['unit']) ? $shipmentDetail['unit'] :'') }}">
		</td>
		<?php /*?>
		<td class="text-left">
			<input type="text" class="form-control" name="box_pallet_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['box_pallet']) ? $shipmentDetail['box_pallet'] :'') }}">
		</td>
		<?php */?>
		<td class="text-left"><input type="text" {{ $disableForm }} name="customer_booking_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['booking_date']) ? $shipmentDetail['booking_date'] : '')}}"></td>
		<td class="text-left"><input type="text" {{ $disableForm }} name="customer_collection_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['collection_date']) ? $shipmentDetail['collection_date'] : '')}}"></td>
		<td class="text-left"><input type="text" {{ $disableForm }} name="customer_delivery_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['delivery_date']) ? $shipmentDetail['delivery_date'] : '')}}"></td>
        <td class="text-left"><textarea name="customer_remarks_{{ $index }}" {{ $disableForm }} class="form-control" rows="1">{{ (!empty($shipmentDetail['remarks']) ? $shipmentDetail['remarks'] : '')}}</textarea></td>
        <td class="text-left">
       		<select name="customer_box_pallet_type_{{ $index }}" class="form-control customer-box-pallets-row" {{ $disableForm }}>
            	<option value="">{{ trans("messages.select") }}</option>
               	@if(!empty($boxPalletTypeInfo))
					@foreach($boxPalletTypeInfo as $key => $boxPalletType)
						{{ $selected = "";}}
						@if(!empty($shipmentDetail['type']) && $shipmentDetail['type'] == $boxPalletType)
                     		@php $selected="selected='selected'" ; @endphp
                     	@endif
						<option value='{{ $key }}' {{ $selected }}>{{ $boxPalletType }}</option>
					@endforeach
				@endif
            </select>
        </td>
        <td class="text-left"><input type="text" name="customer_total_no_of_pallets_boxes_{{ $index }}" {{ $disableForm }} class="form-control customer-total-no-of-box-pallets-row" value="{{ (!empty($shipmentDetail['total_no_of_box_pallet']) ? $shipmentDetail['total_no_of_box_pallet'] : '')}}"></td>
                         
		<td class="text-left"><input type="text" name="customer_tracking_no_{{ $index }}" {{ $disableForm }} class="form-control customer-tracking-no-record"  value="{{ (!empty($shipmentDetail['tracking_no']) ? $shipmentDetail['tracking_no'] : '')}}"></td>
		<td class="text-left"><input type="text" name="customer_tracking_link_{{ $index }}" {{ $disableForm }} class="form-control" value="{{ (!empty($shipmentDetail['tracking_link']) ? $shipmentDetail['tracking_link'] : '')}}"></td>
		
		<td class="text-left">
			@if(empty($disableForm ))
			<a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></a>
			@endif
		</td>
		
	</tr>
 	
 	@endforeach
 @endif
<script>

$(document).ready(function() {
    //init date time picker
    $(".date-format").datetimepicker({
        useCurrent: false,
        viewMode: 'days',
        ignoreReadonly: true,
        widgetPositioning: {
            vertical: 'bottom'
        },
        //minDate: moment().startOf('d'),
        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

    });
});
</script>                                                                
              