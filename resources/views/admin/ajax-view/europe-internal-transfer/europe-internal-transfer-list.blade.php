<div class="card card-body card-pagination-items-class">
	{{ Wild_tiger::readMessage() }}
	<div class="table-responsive fixed-table-x">
		<table class="table table-sm table-bordered table-hover" id="user-table">
			<thead>
				<tr>
					<th style="max-width:100px;min-width:40px;">{{ trans("messages.sr-no") }}</th>
					<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.entry-no") }}</th>
					<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.way-of-transport") }}</th>
					<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.book-by") }}</th> 
					<th class="text-left"> {{ trans("messages.logistic-partner") }}</th> 
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.booking-date") }}<br>{{ trans("messages.collection-date") }}<br>{{ trans("messages.delivery-date") }} </th>
					<th class="text-left">{{ trans("messages.no-of-pallets") }} </th>
					<th class="text-left">{{ trans("messages.invoice-no-ref-no") }} </th>
					<th class="text-left">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
					<?php /* ?>
					<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.account") }} </th>
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.from-warehouse") }}</th>
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.to-warehouse") }}</th>
					<?php */ ?>
					<th class="text-left">{{ trans("messages.status") }}</th>
					<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-gbp") }}</th>
					<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_INTERNAL_TRANSFER')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_INTERNAL_TRANSFER')) != false) ){?>
					 	<th class="text-center" style="max-width:100px;min-width:100px;">{{ trans("messages.actions") }}</th>
					<?php }?>
				</tr>
			</thead>

			<tbody class="ajax-view">
			<?php 
				if(count($recordDetails) > 0){
					$index= ($page_no - 1) * $perPageRecord;
					foreach ($recordDetails as $recordDetail){
						$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
						$invoiceReference = ( isset($recordDetail->detailInfo) ? json_decode(json_encode($recordDetail->detailInfo),true) : [] );
						$invoiceReferenceNo = (isset($invoiceReference) ? array_column($invoiceReference, 'v_invoice_ref_no') : []);
						$invoiceNoReferenceNo = (isset($invoiceReferenceNo) ? implode(', ', $invoiceReferenceNo) : '');
						$allInvoiceDetails = ( isset($recordDetail->invoiceInfo) ? json_decode(json_encode($recordDetail->invoiceInfo),true) : [] );
						$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
						$paymentValue = $finalCharge;
						$transferDetail = (!empty($invoiceReference[0]) ? $invoiceReference[0] : '');
						?>
						<tr>
							<td class="sr-col text-center">{{++$index}}</td>
							<td class="text-left"><?php echo (!empty($recordDetail->v_europe_transfer_record_no) ? $recordDetail->v_europe_transfer_record_no :'')?></td>
							<td class="text-left"><?php echo (!empty($recordDetail->e_transport_way) ? $recordDetail->e_transport_way :'')?></td>
							<td class="text-left"><?php echo (!empty($recordDetail->bookEmployeeInfo->v_name) ? $recordDetail->bookEmployeeInfo->v_name .( isset($recordDetail->bookEmployeeInfo->v_department) ? ' ('.$recordDetail->bookEmployeeInfo->v_department.')'  : '' ) :'')?></td>
							<td class="text-left"><?php echo (isset($recordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name . ( isset($recordDetail->logisticPartnerDetail->v_logistic_partner_code) ? ' ('.$recordDetail->logisticPartnerDetail->v_logistic_partner_code.')'  : '' ) :'')?></td>									
							<td class="text-left"><?php echo ( isset($recordDetail->dt_booking_date) ?  clientDate( $recordDetail->dt_booking_date ) .'<br>' .( isset($recordDetail->dt_collection_date) ?  clientDate( $recordDetail->dt_collection_date ) .'<br>' .( isset($recordDetail->dt_delivery_date) ?  clientDate( $recordDetail->dt_delivery_date ) : '' ): '' ): '' ) ?></td>
							<td class="text-left"><?php echo (!empty($recordDetail->i_no_of_pallets) ? $recordDetail->i_no_of_pallets :'')?> </td>
							<td class="text-left"><?php echo (!empty($invoiceNoReferenceNo) ? $invoiceNoReferenceNo :'')?></td>
							<td class="text-left"><?php echo ( isset($recordDetail->v_tracking_no) ?  ( $recordDetail->v_tracking_no ) : '' ) ?> <br> <a href="<?php echo ( isset($recordDetail->v_tracking_link) ?  ( $recordDetail->v_tracking_link ) : '' ) ?>" target="_blank"><?php echo ( isset($recordDetail->v_tracking_link) ?  ( $recordDetail->v_tracking_link ) : '' ) ?></a></td>
							<?php /* ?>
							<td class="text-left">{{ ( isset($transferDetail['account_company']['v_company_name']) ?  ( $transferDetail['account_company']['v_company_name'] ) : '' )}}</td>
							<td class="text-left">{{ ( isset($transferDetail['warehouse']['v_warehouse_name']) ?  ( $transferDetail['warehouse']['v_warehouse_name'] ) .(!empty($transferDetail['warehouse']['v_warehouse_code']) ? ' (' .$transferDetail['warehouse']['v_warehouse_code']. ')'  :'') : '' ) }}</td>
							<td class="text-left">{{ ( isset($transferDetail['location']['v_warehouse_name']) ?  ( $transferDetail['location']['v_warehouse_name'] ) .(!empty($transferDetail['location']['v_warehouse_code']) ? ' (' .$transferDetail['location']['v_warehouse_code']. ')'  :''): '' ) }}</td>
							<?php */ ?>
							<td class="text-left"><?php echo (isset($recordDetail->statusInfo->v_status) ? $recordDetail->statusInfo->v_status :'')?></td>
							<td class="text-left"><?php echo (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_GBP_CURRENCY')  : '' ) ?></td>
							<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_INTERNAL_TRANSFER')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_INTERNAL_TRANSFER')) != false) ){?>
								<td style="max-width:150px;min-width:100px;" class="text-center">
								<?php if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_INTERNAL_TRANSFER')) != false){?>
									<a title="{{trans('messages.edit-record')}}" href="{{route('europe-internal-transfer.edit', $encodeRecordId )}}"  class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
								<?php } ?>
								<a target="_blank" href="{{route('europe-internal-transfer.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
								<?php if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_INTERNAL_TRANSFER')) != false){?>
									<button title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="europe-internal-transfer" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
								<?php } ?>
			
								</td>						
							<?php }?>
						</tr>
						<?php 
					}
				} else {
					?>
					<tr>
						<td colspan="15" class="text-center">{{ trans('messages.no-record-found')}}</td>
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