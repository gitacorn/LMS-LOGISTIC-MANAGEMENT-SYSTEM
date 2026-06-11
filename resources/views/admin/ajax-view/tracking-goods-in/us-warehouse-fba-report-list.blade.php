<?php 
if (count($recordDetails) > 0) {
	$index = 1;
	foreach ($recordDetails as $recordDetail) {
		$userCompanyValue = ( isset($recordDetail->ukAccountCompnyMaster) ? json_decode(json_encode($recordDetail->ukAccountCompnyMaster),true) : [] );
		$userCompanyName = (!empty($userCompanyValue) ? array_column($userCompanyValue, 'v_company_name') : []);
		$userCompany = ( isset($userCompanyName) ? ( implode(', ', $userCompanyName)) : '');
		?>
		<tr>
			<td class="text-center">{{ $index++ }}</td>
			<td class="text-left">{{ (!empty($recordDetail->usWarehouseToAmazonMaster->v_us_warehouse_to_amazon_record_no) ? $recordDetail->usWarehouseToAmazonMaster->v_us_warehouse_to_amazon_record_no : '')}}</td>
			<td class="text-left">{{ (!empty($recordDetail->usWarehouseToAmazonMaster->e_transport_way) ? $recordDetail->usWarehouseToAmazonMaster->e_transport_way : '')}}</td>
			<td class="text-left">{{ (!empty($recordDetail->usWarehouseToAmazonMaster->bookByEmployee->v_name) ? $recordDetail->usWarehouseToAmazonMaster->bookByEmployee->v_name .( isset($recordDetail->bookByEmployee->v_department) ? ' ('.$recordDetail->bookByEmployee->v_department.')'  : '' ) :'')}}</td>
			<td class="text-left">{{ (!empty($recordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name .( isset($recordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->v_logistic_partner_code) ? ' ('.$recordDetail->usWarehouseToAmazonMaster->logisticPartnerMasterInfo->v_logistic_partner_code.')'  : '' ) :'')}}</td>
			<td class="text-left">{{ (!empty($recordDetail->usWarehouseToAmazonMaster->statusInfo->v_status) ? $recordDetail->usWarehouseToAmazonMaster->statusInfo->v_status :'')}}</td>						
			<td>
				@if(!empty($recordDetail->v_shipment_id))
					{{ $recordDetail->v_shipment_id  }}
				@endif
				@if(!empty($recordDetail->v_shipment_invoice_no))
					{{ $recordDetail->v_shipment_invoice_no  }}
				@endif
				@if(!empty($recordDetail->v_invoice_no_ref_no))
					{{ $recordDetail->v_invoice_no_ref_no  }}
				@endif
			</td>
			<td>{{ (!empty($recordDetail->usWarehouseToAmazonMaster->e_to_location)  ? $recordDetail->usWarehouseToAmazonMaster->e_to_location :'') }}</td>
			<td>{{ (!empty($recordDetail->v_ref_id) ? $recordDetail->v_ref_id :'') }}</td>
			<td>
				@if(!empty($recordDetail->accountComapnyInfo->v_company_name))
					{{ (!empty($recordDetail->accountComapnyInfo->v_company_name) ? $recordDetail->accountComapnyInfo->v_company_name . (!empty($recordDetail->accountComapnyInfo->v_company_code) ? ' ('  .$recordDetail->accountComapnyInfo->v_company_code. ')' :'') :'') }}
				@endif
				@if(!empty($userCompany))
					{{ $userCompany }}
				@endif
				
			</td>
			<td>{{ (!empty($recordDetail->customerInfo->v_customer_name) ? $recordDetail->customerInfo->v_customer_name :'') }}</td>
			<td>
				@if(!empty($recordDetail->amazonFromWarehouseInfo->v_warehouse_name))
					{{ (!empty($recordDetail->amazonFromWarehouseInfo->v_warehouse_name) ? $recordDetail->amazonFromWarehouseInfo->v_warehouse_name .(!empty($recordDetail->amazonFromWarehouseInfo->v_warehouse_code) ? ' ('.$recordDetail->amazonFromWarehouseInfo->v_warehouse_code.')' :'') :'') }}
				@endif
				@if(!empty($recordDetail->fromWarehouseInfo->v_warehouse_name))
					{{ (!empty($recordDetail->fromWarehouseInfo->v_warehouse_name) ? $recordDetail->fromWarehouseInfo->v_warehouse_name .(!empty($recordDetail->fromWarehouseInfo->v_warehouse_code) ? ' ('.$recordDetail->fromWarehouseInfo->v_warehouse_code.')' :'') :'') }}
				@endif
				@if(!empty($recordDetail->ukFromWarehouseInfo->v_warehouse_name))
					{{ (!empty($recordDetail->ukFromWarehouseInfo->v_warehouse_name) ? $recordDetail->ukFromWarehouseInfo->v_warehouse_name .(!empty($recordDetail->ukFromWarehouseInfo->v_warehouse_code) ? ' ('.$recordDetail->ukFromWarehouseInfo->v_warehouse_code.')' :'') :'') }}
				@endif
			</td>	
			<td>{{ (!empty($recordDetail->toAmazonLocationInfo->v_warehouse_name) ? $recordDetail->toAmazonLocationInfo->v_warehouse_name .(!empty($recordDetail->toAmazonLocationInfo->v_warehouse_code) ? ' ('.$recordDetail->toAmazonLocationInfo->v_warehouse_code.')' :'') :'') }}</td>
			<td>{{ (!empty($recordDetail->toCustomerLocationInfo->v_customer_codes) ? $recordDetail->toCustomerLocationInfo->v_customer_codes .(!empty($recordDetail->toCustomerLocationInfo->v_customer_address) ? ' ('.$recordDetail->toCustomerLocationInfo->v_customer_address.')' :'') :'') }}</td>
			<td>{{ (!empty($recordDetail->ukToWarehouseInfo->v_warehouse_name) ? $recordDetail->ukToWarehouseInfo->v_warehouse_name .(!empty($recordDetail->ukToWarehouseInfo->v_warehouse_code) ? ' ('.$recordDetail->ukToWarehouseInfo->v_warehouse_code.')' :'') :'') }}</td>
			<td>{{ (!empty($recordDetail->v_sku) ? $recordDetail->v_sku :'') }}</td>
			<td>
				@if(!empty($recordDetail->v_units))
						{{ (!empty($recordDetail->v_units) ? $recordDetail->v_units :'') }}
				@endif	
				@if(!empty($recordDetail->v_uk_unit))
					{{ (!empty($recordDetail->v_uk_unit) ? $recordDetail->v_uk_unit :'') }}
				@endif	
			</td>
			<td>
				@if(!empty($recordDetail->v_uk_box_pallet))
					{{ (!empty($recordDetail->v_uk_box_pallet) ? $recordDetail->v_uk_box_pallet :'') }}
				@endif	
				@if(!empty($recordDetail->v_box_pallet))
					{{ (!empty($recordDetail->v_box_pallet) ? $recordDetail->v_box_pallet :'') }}
				@endif	
			</td>
			<td>{{ (!empty($recordDetail->d_price) ? decimalAmount($recordDetail->d_price) :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_booking_date) ? clientDate($recordDetail->dt_booking_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->v_remarks) ? $recordDetail->v_remarks :'') }}</td>
			<td>{{ (!empty($recordDetail->usWarehouseToAmazonMaster->v_tracking_no) ? $recordDetail->usWarehouseToAmazonMaster->v_tracking_no :'') }}</td>
			<td>{{ (!empty($recordDetail->v_tracking_link) ? $recordDetail->v_tracking_link :'') }}</td>
			<td>{{ (!empty($recordDetail->dt_amazon_appointment_date) ? clientDate($recordDetail->dt_amazon_appointment_date) :'') }}</td>
			<td>{{ (!empty($recordDetail->v_amazon_appointment_id) ? $recordDetail->v_amazon_appointment_id :'') }}</td>
		</tr>
		<?php 		
	}
} else {
	?>
	<tr>
		<td colspan="27" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
<?php
}
?>
@include('admin/common-display-count')