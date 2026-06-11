@if(!empty($shipmentDetails))
	@php
		$index = (isset($importCsvAddRowCount) && $importCsvAddRowCount > 0 ? $importCsvAddRowCount : 0);
	@endphp
	@foreach ($shipmentDetails as $shipmentDetail)
		<tr class="filled-record">
        	<td class="table-index text-center">{{ ++$index }}</td>
        	<td class="text-left">
         		<input type="text" class="form-control invoice-no-ref-no-row amazon-shipment-id-status" {{ $disableForm }} name="invoice_no_ref_no_{{ $index }}" onchange="checkUniqueShipmentId(this)" value="{{ (!empty($shipmentDetail['invoice_no_ref_no']) ? $shipmentDetail['invoice_no_ref_no']  : '') }}">
        	</td>
           	<td class="text-left">
           		<select name="uk_account_{{ $index }}[]" class="form-control uk-account-row select2" multiple {{ $disableForm }}>
           			@php $companyNameSelected = (!empty($shipmentDetail['account']) ? explode(',', $shipmentDetail['account']) : []); @endphp
	        		@if(!empty($companyMasterRecordDetails))
	           			@foreach ($companyMasterRecordDetails as $companyMasterRecordDetail)
	          				@php 
	          					$compnayRecordDetailEncodedId = Wild_tiger::encode($companyMasterRecordDetail->i_id); 
	          					$companyNameCode = (!empty($companyMasterRecordDetail->v_company_name) ? $companyMasterRecordDetail->v_company_name  . (!empty($companyMasterRecordDetail->v_company_short_code) ? ' ('. $companyMasterRecordDetail->v_company_short_code . ')' : '' )  : '');
	          					$selected = '';	
	          				@endphp
	          				@if((!empty($companyNameSelected)) && (in_array($companyNameCode ,$companyNameSelected)))
	          					@php $selected="selected='selected'" ; @endphp
	          				@endif
	             			<option value="{{ $compnayRecordDetailEncodedId }}" {{ $selected  }}>{{ $companyNameCode}}</option>
	               		@endforeach
 					@endif
				</select>
        	</td>
          	<td class="text-left">
            	<select name="uk_from_warehouse_{{ $index }}" class="form-control uk-from-warehouse-row" {{ $disableForm }}>
             		<option value="">{{ trans("messages.select") }}</option>
               		@if(!empty($amazonFromWarehouseDetails))
                   		@foreach ($amazonFromWarehouseDetails as $amazonFromWarehouseDetail)
                   			@php 
                   				$amazonFromWarehouseEncodeId =  Wild_tiger::encode($amazonFromWarehouseDetail->i_id);
                   				$amazonFromWarehouseName = (!empty($amazonFromWarehouseDetail->v_warehouse_name) ? $amazonFromWarehouseDetail->v_warehouse_name  . (!empty($amazonFromWarehouseDetail->v_warehouse_code) ? ' ('. $amazonFromWarehouseDetail->v_warehouse_code . ')' : '' ) : '');
                   				$selected = '';
                   			@endphp
                   			@if((!empty($shipmentDetail['from(us_warehouse)'])) && ( $shipmentDetail['from(us_warehouse)'] == $amazonFromWarehouseName))
	          					@php $selected="selected='selected'" ; @endphp
	          				@endif
                    		<option value="{{ $amazonFromWarehouseEncodeId }}" {{ $selected  }}>{{ $amazonFromWarehouseName }}</option>
                     	@endforeach
 					@endif
          		</select>
        	</td>
			<td class="text-left">
	          	<select name="uk_to_warehouse_{{ $index }}" class="form-control uk-to-warehouse-row" {{ $disableForm }}>
	          		<option value="">{{ trans("messages.select") }}</option>
	  				@if(!empty($ukWarehouseDetails))
	             		@foreach ($ukWarehouseDetails as $ukWarehouseDetail)
	               			@php 
	               				$uWwarehouseEncodeId =  Wild_tiger::encode($ukWarehouseDetail->i_id); 
	               				$warehouseNameCode = (!empty($ukWarehouseDetail->v_warehouse_name) ? $ukWarehouseDetail->v_warehouse_name . (!empty($ukWarehouseDetail->v_warehouse_code) ? ' ('. $ukWarehouseDetail->v_warehouse_code . ')' : '' ) : '') ;
	               				$selected = '';
	               			@endphp
	               			@if((!empty($shipmentDetail['to(uk_warehouse)'])) && ( $shipmentDetail['to(uk_warehouse)'] == $warehouseNameCode))
	          					@php $selected="selected='selected'" ; @endphp
	          				@endif
	                 		<option value="{{ $uWwarehouseEncodeId }}" {{ $selected  }}>{{ $warehouseNameCode }}</option>
	                   		@endforeach
 					@endif
	    		</select>
	 		</td>
	      	<td class="text-left"><input type="text" class="form-control uk-unit-row" name="uk_unit_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['unit']) ? $shipmentDetail['unit']  : '') }}"></td>
	       	<?php /*?><td class="text-left"><input type="text" class="form-control" name="uk_box_pallet_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['box_pallet']) ? $shipmentDetail['box_pallet']  : '') }}"></td><?php */?>
	      	<td class="text-left"><input type="text" class="form-control date-format" {{ $disableForm }} name="warehouse_booking_date_{{ $index }}"  value="{{ (!empty($shipmentDetail['booking_date']) ? $shipmentDetail['booking_date'] : '')}}"></td>
			<td class="text-left"><input type="text" class="form-control date-format" {{ $disableForm }} name="warehouse_collection_date_{{ $index }}"  value="{{ (!empty($shipmentDetail['collection_date']) ? $shipmentDetail['collection_date'] : '')}}"></td>
			<td class="text-left"><input type="text" {{ $disableForm }} name="warehouse_delivery_date_{{ $index }}" class="form-control date-format" value="{{ (!empty($shipmentDetail['delivery_date']) ? $shipmentDetail['delivery_date'] : '')}}"></td>
            <td class="text-left"><textarea name="warehouse_remarks_{{ $index }}" {{ $disableForm }} class="form-control" rows="1">{{ (!empty($shipmentDetail['remarks']) ? $shipmentDetail['remarks'] : '')}}</textarea></td>
            <td class="text-left">
	       		<select name="warehouse_box_pallet_type_{{ $index }}" class="form-control uk-warehouse-type-row" {{ $disableForm }}>
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
	        <td class="text-left"><input type="text" name="warehouse_total_no_of_pallets_boxes_{{ $index }}" {{ $disableForm }} class="form-control uk-warehouse-total-box-pallets-row" value="{{ (!empty($shipmentDetail['total_no_of_box_pallet']) ? $shipmentDetail['total_no_of_box_pallet'] : '')}}"></td>
	              
            <td class="text-left"><input type="text" class="form-control uk-tracking-no-warehouse" name="warehouse_tracking_no_{{ $index }}" {{ $disableForm }}   value="{{ (!empty($shipmentDetail['tracking_no']) ? $shipmentDetail['tracking_no'] : '')}}"></td>
			<td class="text-left"><input type="text" class="form-control" name="warehouse_tracking_link_{{ $index }}" {{ $disableForm }} value="{{ (!empty($shipmentDetail['tracking_link']) ? $shipmentDetail['tracking_link'] : '')}}"></td>
			
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
$(function(){
	$('.select2').select2();
})
</script>                                                                
                                                        