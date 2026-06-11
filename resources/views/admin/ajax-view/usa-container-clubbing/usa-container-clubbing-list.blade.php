<div class="card card-body card-pagination-items-class">
{{ Wild_tiger::readMessage() }}
	<div class="table-responsive fixed-tabel">
		<table class="table table-sm table-bordered table-hover" id="usa-container-clubbing-table">
			<thead>
				<tr>
					<th class="sr-col">{{ trans("messages.sr-no") }}</th>
					<th class="text-left">{{ trans("messages.booking-date") }}</th>
					<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.fba") }} </th>
					<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.booking-portal") }}</th>
					<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.carrier-company") }}</th>
					<th class="text-left">{{ trans("messages.tracking-no") }}</th>
					<th class="text-left">{{ trans("messages.pro-number") }}</th>
					<th class="text-left">{{ trans("messages.logistic-cost-usd") }}</th>
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.collection-date") }} <br> {{ trans("messages.delivery-date") }}</th>
					<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.from-warehouse") }} <br> {{ trans("messages.to-location") }} </th>
					<th class="text-left">{{ trans("messages.status") }}</th>
					<?php if( (checkPermission(config('permission_constants.EDIT_USA_CONTAINER_CLUBBING')) != false) || (checkPermission(config('permission_constants.DELETE_USA_CONTAINER_CLUBBING')) != false) ){?>
						<th style="max-width:100px;min-width:100px;">{{ trans("messages.actions") }}</th>
					<?php }?>
				</tr>
			</thead>

			<tbody class="ajax-view">
			<?php 
			if(count($recordDetails) > 0){
				$index= ($page_no - 1) * $perPageRecord;
				foreach ($recordDetails as $recordDetail){
					$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
					$collectFbaSheetDetails = (isset($recordDetail) && isset($recordDetail->fbaSheetDetails) && !empty($recordDetail->fbaSheetDetails) ? collect($recordDetail->fbaSheetDetails)->pluck('v_fba_po_no')->toArray() : []);
					$collectUsWarehouseSheetDetails = (isset($recordDetail) && isset($recordDetail->usaWarehouseSheetDetails) && !empty($recordDetail->usaWarehouseSheetDetails) ? collect($recordDetail->usaWarehouseSheetDetails)->pluck('v_shipment_id')->toArray() : []);
					$allSheetDetails = array_merge($collectFbaSheetDetails, $collectUsWarehouseSheetDetails);
					?>
					<tr>
						<td class="sr-col text-center">{{++$index}}</td>
						<td class="text-left"><?php echo (!empty($recordDetail->dt_booking_date) ? clientDate($recordDetail->dt_booking_date) :'')?></td>
						<td><?php echo (isset($allSheetDetails) && !empty($allSheetDetails) ? implode(', ', $allSheetDetails) : '')?></td>
						<td class="text-left"><?php echo (isset($recordDetail->bookingPortalInfo) && !empty($recordDetail->bookingPortalInfo->v_value) ? $recordDetail->bookingPortalInfo->v_value :'')?></td>
						<td class="text-left"><?php echo (isset($recordDetail->carrierCompanyInfo) && !empty($recordDetail->carrierCompanyInfo->v_logistic_partner_name) ? $recordDetail->carrierCompanyInfo->v_logistic_partner_name :'')?></td>					
						<td class="text-left"><?php echo (!empty($recordDetail->v_tracking_no) ? $recordDetail->v_tracking_no :'')?></td>
						<td class="text-left"><?php echo (!empty($recordDetail->v_pro_number) ? $recordDetail->v_pro_number :'')?></td>
						<td class="text-left"><?php echo (strlen($recordDetail->d_logistic_cost_in_usd) > 0 ? decimalAmount($recordDetail->d_logistic_cost_in_usd) : 0)?></td>
						<td class="text-left"><?php echo (!empty($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) :'') . ( !empty($recordDetail->dt_collection_date) && !empty($recordDetail->dt_delivery_date) ? '<br>' : '' ) . (!empty($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) :'')?></td>
						<td class="text-left"><?php echo (isset($recordDetail->fromWarehouseInfo) && !empty($recordDetail->fromWarehouseInfo->v_warehouse_name) ? $recordDetail->fromWarehouseInfo->v_warehouse_name . (!empty($recordDetail->fromWarehouseInfo->v_warehouse_code) ? ' (' . $recordDetail->fromWarehouseInfo->v_warehouse_code . ')' : '') :'') . ( isset($recordDetail->fromWarehouseInfo) && !empty($recordDetail->fromWarehouseInfo->v_warehouse_name) && isset($recordDetail->toLocationInfo) && !empty($recordDetail->toLocationInfo->v_warehouse_name) ? '<br>' : '' ) . (isset($recordDetail->toLocationInfo) && !empty($recordDetail->toLocationInfo->v_warehouse_name) ? $recordDetail->toLocationInfo->v_warehouse_name . (!empty($recordDetail->toLocationInfo->v_warehouse_code) ? ' (' . $recordDetail->toLocationInfo->v_warehouse_code . ')' : '') :'')?></td>
						<td class="text-left"><?php echo (isset($recordDetail->statusInfo->v_status) ? $recordDetail->statusInfo->v_status :'')?></td>
						<?php if( (checkPermission(config('permission_constants.EDIT_USA_CONTAINER_CLUBBING')) != false) || (checkPermission(config('permission_constants.DELETE_USA_CONTAINER_CLUBBING')) != false) ){?>
							<td style="max-width:150px;min-width:100px;" class="text-center">
							<?php if(checkPermission(config('permission_constants.EDIT_USA_CONTAINER_CLUBBING')) != false){?>
								<a title="{{trans('messages.edit-record')}}" href="{{  config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') }}/edit/{{ $encodeRecordId }}"  class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
							<?php }?>
							<a target="_blank" href="{{route('usa-container-clubbing.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
							<?php if(checkPermission(config('permission_constants.DELETE_USA_CONTAINER_CLUBBING')) != false){?>
								<button title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="us-container-clubbing" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
							<?php }?>	
							</td>
						<?php }?>
					</tr>
					<?php 
				}
			} else {
				?>
				<tr>
					<td colspan="12" class="text-center">{{ trans('messages.no-record-found')}}</td>
				</tr>
				<?php 
			}
			?>
			@include('admin/common-display-count')
			</tbody>
		</table>
	</div>
	{{ $recordDetails->onEachSide(1)->links() }}
</div>