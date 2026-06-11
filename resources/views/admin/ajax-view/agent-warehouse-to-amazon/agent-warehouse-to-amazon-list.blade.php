<div class="card card-body card-pagination-items-class">
{{ Wild_tiger::readMessage() }}
	<div class="table-responsive fixed-tabel">
		<table class="table table-sm table-bordered table-hover" id="user-table">
			<thead>
				<tr>
					<th style="max-width:100px;min-width:40px;">{{ trans("messages.sr-no") }}</th>
					<th class="text-left">{{ trans("messages.entry-no") }}</th>
					<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.way-of-transport") }} <br> {{ trans("messages.from") }} <br> {{ trans("messages.to") }} - {{ trans("messages.warehouse") }} </th>
					<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.container-number") }} <br> {{ trans("messages.view-fba") }} </th>
					<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.book-by") }}</th>
					<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
					<th class="text-left">{{ trans("messages.booking-date") }}</th>
					<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.collection-date") }} <br> {{ trans("messages.delivery-date") }}</th>
					<th class="text-left">{{ trans("messages.status") }}</th>
					<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-usd") }}</th>
					<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false) ){?>
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
					$contanierValue = ( isset($recordDetail->countryToPortMaster) ? json_decode(json_encode($recordDetail->countryToPortMaster),true) : [] );
					$contanierColumn = (!empty($contanierValue) ? array_column($contanierValue, 'v_country_to_port_record_no') : []);
					$contanierName = ( isset($contanierColumn) ? ( implode(', ', $contanierColumn)) : '');
					$allInvoiceDetails = ( isset($recordDetail->invoiceInfo) ? json_decode(json_encode($recordDetail->invoiceInfo),true) : [] );
					$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
					$paymentValue = $finalCharge;
					$totalPallets = 0;
					if( isset($totalPallets) ){
						
					}
					?>
					<tr>
						<td class="sr-col text-center">{{++$index}}</td>
						<td class="text-left"><?php echo (!empty($recordDetail->v_agent_to_warehouse_record_no) ? $recordDetail->v_agent_to_warehouse_record_no :'')?></td>
						<td class="text-left"><?php echo ( isset($recordDetail->e_transport_way) ?  ( $recordDetail->e_transport_way ) . '<br>' : '' ) . (isset($recordDetail->formLogisticInfo->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->formLogisticInfo->logisticPartnerMaster->v_logistic_partner_name . ( isset($recordDetail->formLogisticInfo->v_logistic_partner_code) ? ' ('.$recordDetail->formLogisticInfo->v_logistic_partner_code.')'  : '' ) . '<br>' :'') . ( isset($recordDetail->e_to_location) ?  ( $recordDetail->e_to_location . (isset($recordDetail->toWarehouseInfo) && !empty($recordDetail->toWarehouseInfo->v_warehouse_name) ? ' - ' . $recordDetail->toWarehouseInfo->v_warehouse_name : '') )  :'' )  ?></td>
						<td class="text-left">
							
							<?php 
							if(!empty($recordDetail->countryToPortMaster)){
								foreach ($recordDetail->countryToPortMaster as $selectedContainer){
									$encodeContanierId = Wild_tiger::encode($selectedContainer->i_id);
									$allLocation = [];
									$allPalltets = [];
									if( isset($recordDetail->detailInfo) && (!empty($recordDetail->detailInfo)) ){
										foreach($recordDetail->detailInfo as $detailInfo){
											if( isset($detailInfo->agentToWarehousefbaSheetDetail->fbaSheetMaster->i_country_to_port_goods_out_master_id) && ( $detailInfo->agentToWarehousefbaSheetDetail->fbaSheetMaster->i_country_to_port_goods_out_master_id == $selectedContainer->i_id ) ){
												if( isset($detailInfo->agentToWarehousefbaSheetDetail->i_pallet_no) ){
													$allPalltets[] = $detailInfo->agentToWarehousefbaSheetDetail->i_pallet_no;
												}
												if( isset($detailInfo->agentToWarehousefbaSheetDetail->v_location_code) ){
													$allLocation[] = $detailInfo->agentToWarehousefbaSheetDetail->v_location_code;
												}
												
											}
										}
									}
									?>
									{{ (!empty($selectedContainer->v_country_to_port_record_no) ? $selectedContainer->v_country_to_port_record_no .(!empty($selectedContainer->e_transport_way) ? ' ('.$selectedContainer->e_transport_way.')' :'') :'') }}
									<?php if(checkPermission(config('permission_constants.VIEW_FBA_SHEET_MASTER')) != false) { ?>
										<?php echo (!empty($allLocation) ? ' - '. implode(", " , array_unique($allLocation) ) : '' ) ?>
										<?php echo (!empty($allPalltets) ? ' - '.trans('messages.total-pallets').' = '. count(array_unique($allPalltets)) : '' ) ?>
										<a href="{{ route('port-to-agent.view-fba-sheet', [ $encodeContanierId , $encodeRecordId ]  )  }}" target="_blank">{{ trans('messages.view') }}</a><br>
									 <?php } ?>
									<?php if( $selectedContainer != last($recordDetail->countryToPortMaster) )?> 
									<?php echo PHP_EOL ?>
									<?php 
								}
							}
							?>
						</td>	
						<td class="text-left"><?php echo (!empty($recordDetail->bookEmployeeInfo->v_name) ? $recordDetail->bookEmployeeInfo->v_name .( isset($recordDetail->bookEmployeeInfo->v_department) ? ' ('.$recordDetail->bookEmployeeInfo->v_department.')'  : '' ) :'')?></td>
						<td class="text-left"><?php echo (isset($recordDetail->toLogisticInfo->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->toLogisticInfo->logisticPartnerMaster->v_logistic_partner_name . ( isset($recordDetail->toLogisticInfo->v_logistic_partner_code) ? ' ('.$recordDetail->toLogisticInfo->v_logistic_partner_code.')'  : '' ) :'')?></td>
						<td class="text-left"><?php echo (isset($recordDetail->dt_booking_date) ? clientDate($recordDetail->dt_booking_date) : '')?></td>
						<td class="text-left" style="max-width:200px;min-width:200px;"><?php echo ( isset($recordDetail->v_tracking_no) ?  ( $recordDetail->v_tracking_no ) : '' ) ?> <br> <a href="<?php echo ( isset($recordDetail->v_tracking_link) ?  ( $recordDetail->v_tracking_link ) : '' ) ?>" target="_blank"><?php echo ( isset($recordDetail->v_tracking_link) ?  ( $recordDetail->v_tracking_link ) : '' ) ?></a></td>
						<td class="text-left"><?php echo ( isset($recordDetail->dt_collection_date) ?  clientDate( $recordDetail->dt_collection_date ) . '<br>' : '' ) . ( isset($recordDetail->dt_delivery_date) ?  clientDate( $recordDetail->dt_delivery_date )  :'' )  ?></td>
						<td class="text-left"><?php echo (isset($recordDetail->statusInfo->v_status) ? $recordDetail->statusInfo->v_status :'')?></td>
						<td class="text-left"><?php echo (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_DEFAULT_CURRENCY')  : '' ) ?></td>
						<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false) ){?>
							<td style="max-width:150px;min-width:100px;" class="text-center">
							<?php if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false){?>
								<a title="{{trans('messages.edit-record')}}" href="{{route('agent-warehouse-to-amazon.edit', $encodeRecordId )}}"  class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
							<?php }?>
								<a target="_blank" href="{{route('agent-warehouse-to-amazon.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
								<?php if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false){?>
								<button title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="agent-warehouse-to-amazon" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
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