<div class="card card-body card-pagination-items-class">
	{{ Wild_tiger::readMessage() }}
	<div class="table-responsive fixed-table-x">
		<table class="table table-sm table-bordered table-hover" id="user-table">
			<thead>
				<tr>
					<th class="sr-col">{{ trans("messages.sr-no") }}</th>
					<th class="text-left">{{ trans("messages.entry-no") }}</th>
					<th class="text-left">{{ trans("messages.way-of-transport") }}</th>
					<th class="text-left">{{ trans("messages.from") }}</th>
					<th class="text-left">{{ trans("messages.to") }}</th>
					<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.book-by") }}</th>
					<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
					<th class="text-left">Personal Ref</th>
					<th class="text-left">{{ trans("messages.tracking-no") }}
					<?php /*?>
					<th class="text-left">{{ trans("messages.booking-date") }} <br> {{ trans("messages.collection-date") }} <br> {{ trans("messages.delivery-date") }}</th>
					<th class="text-left">{{ trans("messages.type") }} <br> {{ trans("messages.total-no-of-pallets") }}</th>
					<th class="text-left">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
					<th class="text-left">{{ trans("messages.amazon-appointment-date") }}</th>
					<?php */?>
					<th class="text-left">{{ trans("messages.status") }}</th>
					<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false) ){?>
						<th style="text-align:center; max-width:100px;min-width:100px;">{{ trans("messages.actions") }}</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody class="">
				<?php 
				if(count($recordDetails) > 0){
					$index = ($page_no - 1) * $perPageRecord;
					foreach ($recordDetails as $recordDetail){
						$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
						?>
						<tr>
							<td class="sr-col text-center">{{ ++$index }}</td>
							<td class="text-left">{{ (!empty($recordDetail->v_us_warehouse_to_amazon_record_no) ? $recordDetail->v_us_warehouse_to_amazon_record_no : '')}}</td>
							<td class="text-left">{{ (!empty($recordDetail->e_transport_way) ? $recordDetail->e_transport_way : '')}}</td>
							<td>{{ (isset($recordDetail->fromUsWarehouseInfo->v_warehouse_name) ? $recordDetail->fromUsWarehouseInfo->v_warehouse_name :'') }}</td>
							<td>{{ (isset($recordDetail->e_to_location) ? $recordDetail->e_to_location :'') }}</td>
							<td class="text-left">{{ (!empty($recordDetail->bookByEmployee->v_name) ? $recordDetail->bookByEmployee->v_name .( isset($recordDetail->bookByEmployee->v_department) ? ' ('.$recordDetail->bookByEmployee->v_department.')'  : '' ) :'')}}</td>
							<td class="text-left">{{ (!empty($recordDetail->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->logisticPartnerMasterInfo->logisticPartnerMaster->v_logistic_partner_name .( isset($recordDetail->logisticPartnerMasterInfo->v_logistic_partner_code) ? ' ('.$recordDetail->logisticPartnerMasterInfo->v_logistic_partner_code.')'  : '' ) :'')}}</td>
							<td class="text-left">{{ !empty($recordDetail->v_personal_ref) ? $recordDetail->v_personal_ref : '' }}</td>
							<td class="text-left">{{ (!empty($recordDetail->v_tracking_no) ? ($recordDetail->v_tracking_no) : '')}}</td>
							<?php /*?>
							<td class="text-left">{{ (!empty($recordDetail->dt_booking_date) ? clientDate($recordDetail->dt_booking_date) : '')}} <br> {{ (!empty($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) : '')}} <br> {{ (!empty($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) : '')}}</td>
							<td class="text-left">{{ (!empty($recordDetail->e_box_pallet_type) ? ($recordDetail->e_box_pallet_type) .(!empty($recordDetail->i_total_no_of_pallets) ? ' ' .($recordDetail->i_total_no_of_pallets) : '') : '')}}</td>
							<td class="text-left">{{ (!empty($recordDetail->v_tracking_no) ? ($recordDetail->v_tracking_no) : '')}} <br> <a href="{{ (!empty($recordDetail->v_tracking_link) ? ($recordDetail->v_tracking_link) : '')}}" target="_blank">{{ (!empty($recordDetail->v_tracking_link) ? ($recordDetail->v_tracking_link) : '')}}</a></td>
							<td class="text-left">{{ (!empty($recordDetail->dt_amazon_appointment_date) ? clientDate($recordDetail->dt_amazon_appointment_date) : '')}}</td>
							<?php */?>
							<td class="text-left">{{ (!empty($recordDetail->statusInfo->v_status) ? ($recordDetail->statusInfo->v_status) : '')}}</td>
							<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false) ){?>
								<td class="text-center">
									<?php if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false){?>
										<a title="{{trans('messages.edit-record')}}" href="{{route('us-warehouse-to-amazon.edit', $encodeRecordId )}}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
									<?php } ?>
									<a target="_blank" href="{{route('us-warehouse-to-amazon.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
									<?php if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false){?>
										<button title="{{trans('messages.delete')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="us-warehouse-to-amazon" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
									<?php } ?>
			
								</td>
							<?php } ?>
						</tr>
						<?php 
					}
				} else { ?>
					<tr>
						<td colspan="10" class="text-center">{{ trans('messages.no-record-found')}}</td>
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