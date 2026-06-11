@if(!empty($shipmentDetails))
	@php
		$index = (isset($importCsvAddRowCount) && $importCsvAddRowCount > 0 ? $importCsvAddRowCount : 0);
	@endphp
	@foreach ($shipmentDetails as $shipmentDetail)
		<tr class="filled-record">
			<td class="table-index text-center" style="width:70px;min-width:70px;">{{ ++$index}}</td>
			@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON")))
			<td class="text-left"><input type="text" {{ $disableForm }} class="form-control amazon-workflow-id-status" name="workflow_id_{{ $index }}" value="{{ (!empty($shipmentDetail['workflow_id']) ? $shipmentDetail['workflow_id'] : '')}}"></td>
			@endif
	    	<td class="text-left"><input type="text" class="form-control amazon-shipment-id-status" onchange="checkUniqueShipmentId(this)" name="shipment_id_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['shipment_id']) ? $shipmentDetail['shipment_id'] : '')}}"></td>
	 		<td class="text-left"><input type="text" class="form-control ref-amazon-row amazon-ref-id-status" name="ref_id_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['ref_id']) ? $shipmentDetail['ref_id'] : '')}}"></td>
	  		<td class="text-left">
	 			<select name="account_{{ $index }}" class="form-control account-amazon-row amazon-account-status" {{ $disableForm }}>
	    			<option value="">{{ trans("messages.select") }}</option>
	            	@if(!empty($companyMasterRecordDetails))
	                	@foreach ($companyMasterRecordDetails as $companyMasterRecordDetail)
	                		@php
	                 			$encodeId =  Wild_tiger::encode($companyMasterRecordDetail->i_id);
	                 			$companyName = (!empty($companyMasterRecordDetail->v_company_name) ? $companyMasterRecordDetail->v_company_name . (!empty($companyMasterRecordDetail->v_company_short_code) ? ' ('. $companyMasterRecordDetail->v_company_short_code . ')' : '' )  : '');
	                 			$selected = '';
	                 		@endphp	
	                 		@if(!empty($shipmentDetail['account']) && $shipmentDetail['account'] == $companyName)
	                 			@php $selected="selected='selected'" ; @endphp
	                 		@endif
	                  		<option value='{{ $encodeId }}' {{ $selected }}>{{ $companyName }}</option>
	               		@endforeach
 					@endif
	         	</select>
			</td>
			@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.US_TO_WAREHOUSE_AMAZON")))
				<td class="text-left">
					<select name="amazon_from_warehouse_{{ $index }}" class="form-control from-warehouse-amazon-row" {{ $disableForm }}>
		        		<option value="">{{ trans("messages.select") }}</option>
		          		@if(!empty($amazonFromWarehouseDetails))
		 					@foreach ($amazonFromWarehouseDetails as $amazonFromWarehouseDetail)
		 						@php
									$amazonFromWarehouseEncodeId =  Wild_tiger::encode($amazonFromWarehouseDetail->i_id);
									$amazonFromWarehouseName = (!empty($amazonFromWarehouseDetail->v_warehouse_name) ? $amazonFromWarehouseDetail->v_warehouse_name . (!empty($amazonFromWarehouseDetail->v_warehouse_code) ? ' ('. $amazonFromWarehouseDetail->v_warehouse_code . ')' : '' )  : '') ;
									$selected = '';
								@endphp
								@if(!empty($shipmentDetail['from(us_warehouse)']) && $shipmentDetail['from(us_warehouse)'] == $amazonFromWarehouseName)
									@php $selected="selected='selected'" ; @endphp
								@endif
		                      	
		                   		<option value="{{ $amazonFromWarehouseEncodeId }}" {{ $selected }}>{{ $amazonFromWarehouseName }}</option>
		                  	@endforeach
	 					@endif
					</select>
				</td>
			@endif
			@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON")))
				<td class="text-left">
					<select name="packing_warehouse_{{ $index }}" class="form-control amazon-packing-warehouse-status" {{ $disableForm }}>
		        		<option value="">{{ trans("messages.select") }}</option>
		          		@if(!empty($warehouseMasterDetails))
		 					@foreach ($warehouseMasterDetails as $warehouseMasterDetail)
		 						@php
									$amazonFromWarehouseEncodeId = Wild_tiger::encode($warehouseMasterDetail->i_id);
									$amazonFromWarehouseName = (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name . (!empty($warehouseMasterDetail->v_warehouse_code) ? ' ('. $warehouseMasterDetail->v_warehouse_code . ')' : '' )  : '') ;
									$selected = '';
								@endphp
								@if(!empty($shipmentDetail['packing_warehouse']) && $shipmentDetail['packing_warehouse'] == $amazonFromWarehouseName)
									@php $selected="selected='selected'" ; @endphp
								@endif
		                   		<option value="{{ $amazonFromWarehouseEncodeId }}" {{ $selected }}>{{ $amazonFromWarehouseName }}</option>
		                  	@endforeach
	 					@endif
					</select>
				</td>
				<td class="text-left">
					<select name="from_warehouse_{{ $index }}" class="form-control amazon-from-status" {{ $disableForm }}>
		        		<option value="">{{ trans("messages.select") }}</option>
		          		@if(!empty($warehouseMasterDetails))
		 					@foreach ($warehouseMasterDetails as $warehouseMasterDetail)
		 						@php
									$amazonFromWarehouseEncodeId =  Wild_tiger::encode($warehouseMasterDetail->i_id);
									$amazonFromWarehouseName = (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name . (!empty($warehouseMasterDetail->v_warehouse_code) ? ' ('. $warehouseMasterDetail->v_warehouse_code . ')' : '' )  : '') ;
									$selected = '';
								@endphp
								@if(!empty($shipmentDetail['from(us_warehouse)']) && $shipmentDetail['from(us_warehouse)'] == $amazonFromWarehouseName)
									@php $selected="selected='selected'" ; @endphp
								@endif
		                   		<option value="{{ $amazonFromWarehouseEncodeId }}" {{ $selected }}>{{ $amazonFromWarehouseName }}</option>
		                  	@endforeach
	 					@endif
					</select>
				</td>
			@endif
            <td class="text-left">
 				<select name="to_amazon_location_{{ $index }}" class="form-control to-location-amazon-row amazon-to-status select2" {{ $disableForm }}>
					<option value="">{{ trans("messages.select") }}</option>
          			@if(!empty($locationMasterDetails))
                    	@foreach ($locationMasterDetails as $locationMasterDetail)
                    		@php
	                     		$locationMasterDetailEncodeId =  Wild_tiger::encode($locationMasterDetail->i_id);
	                     		$locationName = (!empty($locationMasterDetail->v_warehouse_name) ? $locationMasterDetail->v_warehouse_name . (!empty($locationMasterDetail->v_warehouse_code) ? ' ('. $locationMasterDetail->v_warehouse_code . ')' : '' ) : '');
	                     		$selected = '';
                     		@endphp
                     		@if(!empty($shipmentDetail['to(amazon_location)']) && $shipmentDetail['to(amazon_location)'] == $locationName)
                     			@php $selected="selected='selected'" ; @endphp
                     		@endif
                   			<option value="{{ $locationMasterDetailEncodeId }}" {{ $selected }}>{{ $locationName }}</option>
                      		@endforeach
 					@endif
        		</select>
			</td>
			@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON")))
			<td class="text-left">
				<select name="to_country_delivery_{{ $index }}" class="form-control amazon-to-country-cell-status select2" {{ $disableForm }} >
					<option value="">{{ trans("messages.select") }}</option>
					@if(!empty($countryMasterDetails))
						@foreach ($countryMasterDetails as $countryMasterDetail)
							@php
								$encodeToCountryId = Wild_tiger::encode($countryMasterDetail->i_id);
								$toCountryName = (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '');
								$selected = '';
							@endphp
							@if(!empty($shipmentDetail['to_country(delivery)']) && strtolower($shipmentDetail['to_country(delivery)']) == strtolower($toCountryName))
								@php $selected = 'selected'; @endphp
							@endif
							<option value="{{ $encodeToCountryId }}" {{ $selected }}>{{ (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '') }}</option>
						@endforeach
					@endif
				</select>
			</td>
			@endif
			@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.US_TO_WAREHOUSE_AMAZON")))
			<td class="text-left"><input type="text" class="form-control product-amazon-row" name="product_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['product']) ? $shipmentDetail['product'] : '')}}"></td>
			@endif
 			<td class="text-left"><input type="text" class="form-control sku-amazon-row amazon-sku-status" name="sku_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['sku']) ? $shipmentDetail['sku'] : '')}}"></td>
			<td class="text-left"><input type="text" class="form-control unit-amazon-row amazon-unit-status" name="unit_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['unit']) ? $shipmentDetail['unit'] : '')}}"></td>
			<td class="text-left">
				@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON")))
				<div class="d-flex align-items-center justify-content-center">
					<select name="shipment_currency_{{ $index }}" class="form-control amazon-shipment-currency-status mr-2" {{ $disableForm }} >
						<option value="">{{ trans("messages.select") }}</option>
						@if(!empty($currencyRecordDetails))
							@foreach ($currencyRecordDetails as $currencyRecordDetail)
								@php
									$encodeId = Wild_tiger::encode($currencyRecordDetail->i_id);
									$shipmentCurrency = (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '');
									$selected = '';
								@endphp
								@if(!empty($shipmentDetail['shipment_currency']) && strtolower($shipmentDetail['shipment_currency']) == strtolower($shipmentCurrency))
									@php $selected = 'selected'; @endphp
								@endif
								?>
								<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '') }}</option>
							@endforeach
						@endif
					</select>
				@endif
					<input type="text" class="form-control amazon-shipment-value-status" {{ $disableForm }} name="price_{{ $index }}" onkeyup="onlyDecimal(this)" onchange="onlyDecimal(this)" value="{{ (!empty($shipmentDetail['shipment_value']) && is_numeric($shipmentDetail['shipment_value']) && ($shipmentDetail['shipment_value'] > 0) ? $shipmentDetail['shipment_value'] : '')}}">
				@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON")))
				</div>
				@endif
			</td>
			@if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON")))
			<td class="text-left"><input type="text" class="form-control amazon-weight-status" name="weight_{{ $index }}" {{ $disableForm }} onkeyup="onlyDecimal(this)" onchange="onlyDecimal(this)" value="{{ (!empty($shipmentDetail['weight']) && is_numeric($shipmentDetail['weight']) && (($shipmentDetail['weight']) > 0) ? $shipmentDetail['weight'] : '')}}"></td>
			<td class="text-left">
				<select name="pallets_boxes_type_{{ $index }}" {{ $disableForm }} class="form-control amazon-pallet-box-type-status">
					<option value="">{{ trans("messages.select") }}</option>
					<option value="{{ config('constants.BOX') }}" {{ (!empty($shipmentDetail['boxes_pallets'] && strtolower($shipmentDetail['boxes_pallets']) == strtolower(trans("messages.box"))) ? 'selected' : '') }}>{{ trans("messages.boxes") }}</option>
					<option value="{{ config('constants.PALLET') }}" {{ (!empty($shipmentDetail['boxes_pallets'] && strtolower($shipmentDetail['boxes_pallets']) == strtolower(trans("messages.pallet"))) ? 'selected' : '') }} >{{ trans("messages.pallets") }}</option>
				</select>
			</td>
			<td class="text-left"><input type="text" class="form-control amazon-no-of-pallet-box-status" name="no_of_pallets_boxes_{{ $index }}" {{ $disableForm }} onchange="naturalNumber(this)" value="{{ (!empty($shipmentDetail['no_of_boxes_pallets']) && (($shipmentDetail['no_of_boxes_pallets']) > 0) ? (int)$shipmentDetail['no_of_boxes_pallets'] : '')}}"></td>
			@endif
			<td class="text-left"><input type="text" {{ $disableForm }} name="amazon_booking_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['booking_date']) ? $shipmentDetail['booking_date'] : '')}}"></td>
			<td class="text-left"><input type="text" {{ $disableForm }} name="amazon_collection_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['collection_date']) ? $shipmentDetail['collection_date'] : '')}}"></td>
			<td class="text-left"><input type="text" {{ $disableForm }} name="amazon_delivery_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['delivery_date']) ? $shipmentDetail['delivery_date'] : '')}}"></td>
            @if(!empty($shipmentRequestType) && ($shipmentRequestType == config("constants.US_TO_WAREHOUSE_AMAZON")))
            	<td class="text-left"><textarea name="amazon_remarks_{{ $index }}" {{ $disableForm }} class="form-control" rows="1">{{ (!empty($shipmentDetail['remarks']) ? $shipmentDetail['remarks'] : '')}}</textarea></td>
	            <td class="text-left">
	            	<select name="amazon_box_pallet_type_{{ $index }}" class="form-control amazon-box-pallet-type-row" {{ $disableForm }}>
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
            	<td class="text-left"><input type="text" name="amazon_total_no_of_pallets_boxes_{{ $index }}" {{ $disableForm }} class="form-control amazon-total-no-of-pallet-row" value="{{ (!empty($shipmentDetail['total_no_of_box_pallet']) ? $shipmentDetail['total_no_of_box_pallet'] : '')}}"></td>
            @endif         
            <td class="text-left"><input type="text" name="amazon_tracking_no_{{ $index }}" {{ $disableForm }} class="form-control amazon-tracking-no-row"  value="{{ (!empty($shipmentDetail['tracking_no']) ? $shipmentDetail['tracking_no'] : '')}}"></td>
			<td class="text-left"><input type="text" name="amazon_tracking_link_{{ $index }}" {{ $disableForm }} class="form-control" value="{{ (!empty($shipmentDetail['tracking_link']) ? $shipmentDetail['tracking_link'] : '')}}"></td>
			<td class="text-left"><input type="text" name="amazon_appointment_date_{{ $index }}"  {{ $disableForm }} class="form-control date-format" value="{{ (!empty($shipmentDetail['amazon_appointment_date ']) ? $shipmentDetail['amazon_appointment_date '] : '')}}"></td>
            <td class="text-left"><input type="text" name="amazon_appointment_id_{{ $index }}" {{ $disableForm }} class="form-control" value="{{ (!empty($shipmentDetail['amazon_appointment_id']) ? $shipmentDetail['amazon_appointment_id'] : '')}}"></td>
                                     
			<td style="width:70px;min-width:70px;">
				@if(empty($disableForm ))
					<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this,'<?php echo ($shipmentRequestType == config("constants.EUROPE_TO_AMAZON") ? 'europe-to-amazon-tbody' : 'shipment-details-amazon-record')?>')"><i class="fa fa-trash fa-fw"></i></button>
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