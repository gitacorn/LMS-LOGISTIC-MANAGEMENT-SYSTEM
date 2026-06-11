<div class="card card-body card-pagination-items-class">
{{ Wild_tiger::readMessage() }}
	<div class="table-responsive fixed-tabel">
		<table class="table table-sm table-bordered table-hover" id="user-table">
			<thead>
				<tr>
					<th style="max-width:100px;min-width:40px;">{{ trans("messages.sr-no") }}</th>
					<th class="text-left">{{ trans("messages.entry-no") }}</th>
					<th class="text-left">{{ trans("messages.way-of-transport") }} </th>
					<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.from-port") }} <br> {{ trans("messages.broker-custom-agent") }} </th>
					<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
					<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.from-warehouse-country") }} - {{ trans("messages.warehouse") }}</th>
					<th class="text-left" style="max-width:110px;min-width:110px;">{{ trans("messages.personal-ref") }}</th>
					<th class="text-left" style="max-width:140px;min-width:140px;">{{ trans("messages.warehouse-type") }} - {{ trans("messages.location") }} </th>
					<th class="text-left" style="max-width:140px;min-width:140px;">{{ trans("messages.select-containers") }} <br> {{ trans("messages.view-fba-sheet") }} </th>
					<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.book-by") }}</th>
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.booking-date") }} <br> {{ trans("messages.ref-no") }}</th>
					<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }} </th>
					<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.collection-date") }} <br> {{ trans("messages.delivery-date") }} </th>
					<th class="text-center" style="max-width:110px;min-width:110px;">{{ trans("messages.status") }}</th>
					<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-usd") }}</th>
					<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false) ){?>
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
						$processStatus = (isset($recordDetail->e_process_status) ? $recordDetail->e_process_status : '' );
						$contanierValue = ( isset($recordDetail->countryToPortMaster) ? json_decode(json_encode($recordDetail->countryToPortMaster),true) : [] );
						$contanierColumn = (!empty($contanierValue) ? array_column($contanierValue, 'v_country_to_port_record_no') : []);
						$contanierName = ( isset($contanierColumn) ? ( implode(', ', $contanierColumn)) : '');
						$contanierIds= (!empty($contanierValue) ? array_column($contanierValue, 'i_id') : []);
						$allInvoiceDetails = ( isset($recordDetail->invoiceInfo) ? json_decode(json_encode($recordDetail->invoiceInfo),true) : [] );
						$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
						$paymentValue = $finalCharge;
						
						$WarehouseTypeLocation = $recordDetail->e_warehose_type . ' - ';
						if (!empty($recordDetail->e_warehose_type) && $recordDetail->e_warehose_type == config('constants.OWN_WAREHOUSE_TYPE')){
							$WarehouseTypeLocation .= (isset($recordDetail->ownLocation) && !empty($recordDetail->ownLocation->v_warehouse_name) ? $recordDetail->ownLocation->v_warehouse_name : '');
						} else {
							$WarehouseTypeLocation .= (isset($recordDetail->agentLocation->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->agentLocation->logisticPartnerMaster->v_logistic_partner_name . ( isset($recordDetail->agentLocation->v_logistic_partner_code) ? ' ('.$recordDetail->agentLocation->v_logistic_partner_code . ')' : '' )  :'');
						}
					?>
						<tr>
							<td class="sr-col text-center">{{++$index}}</td>
							<td class="text-left"><?php echo (isset($recordDetail->v_port_to_agent_record_no) ? ($recordDetail->v_port_to_agent_record_no) : '')?></td>
							<td class="text-left"><?php echo (isset($recordDetail->e_transport_way) ? ($recordDetail->e_transport_way) : '' ) ?></td>
							<td class="text-left"><?php echo (isset($recordDetail->fromPortInfo->v_warehouse_name) ? $recordDetail->fromPortInfo->v_warehouse_name :'') .(isset($recordDetail->v_brocker) ? ' <br> '.$recordDetail->v_brocker :'') ?></td>
							<td class="text-left"><?php echo (isset($recordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name :'')?></td>
							<td class="text-left"><?php echo (isset($recordDetail->countryToPortMaster[0]->fromWarehouseCountry) && !empty($recordDetail->countryToPortMaster[0]->fromWarehouseCountry->v_country_name) ? $recordDetail->countryToPortMaster[0]->fromWarehouseCountry->v_country_name : '')?>  <?php echo (isset($recordDetail->countryToPortMaster[0]->warehouseInfo) && !empty($recordDetail->countryToPortMaster[0]->warehouseInfo->v_warehouse_name) ? ' - ' . $recordDetail->countryToPortMaster[0]->warehouseInfo->v_warehouse_name : '') ?></td>
							<td class="text-left"><?php echo (!empty($recordDetail->v_personal_ref) ? $recordDetail->v_personal_ref : '')?></td>
							<td class="text-left"><?php echo $WarehouseTypeLocation ?></td>
							<td class="text-left"> 
							<?php 
							if(!empty($recordDetail->countryToPortMaster)){
								foreach ($recordDetail->countryToPortMaster as $selectedContainer){
									$encodeContanierId = Wild_tiger::encode($selectedContainer->i_id);
									?>
									<?php echo (!empty($selectedContainer->v_country_to_port_record_no) ? $selectedContainer->v_country_to_port_record_no .(!empty($selectedContainer->e_transport_way) ? ' (' .$selectedContainer->e_transport_way.')' :'') : '') ?>
									<?php if(checkPermission(config('permission_constants.VIEW_FBA_SHEET_MASTER')) != false){?>
										 - <a href="{{ route('country-to-port.view-fba-sheet', $encodeContanierId )  }}" target="_blank">{{ trans('messages.view') }}</a>
									<?php } ?>
									<?php if( $selectedContainer != last($recordDetail->countryToPortMaster) )?> 
									<?php echo PHP_EOL ?>
									<?php 
								}
							}
							?>
							</td>
							<td class="text-left"><?php echo (isset($recordDetail->bookEmployeeInfo->v_name) ?  $recordDetail->bookEmployeeInfo->v_name .( isset($recordDetail->bookEmployeeInfo->v_department) ? ' ('.$recordDetail->bookEmployeeInfo->v_department.')'  : '' ): '' ) ?></td>
							<td class="text-left"><?php echo (isset($recordDetail->dt_booking_date) ?  clientDate($recordDetail->dt_booking_date) : '' )
													.(isset($recordDetail->v_ref_no) ?  ' <br> '.$recordDetail->v_ref_no : '' )?>
							</td>
							<td class="text-left" style="max-width:120px;min-width:120px;"><?php echo (isset($recordDetail->v_tracking_no) ?  ( $recordDetail->v_tracking_no ) : '' ) ?>
									<a href="<?php echo ( isset($recordDetail->v_tracking_link) ? $recordDetail->v_tracking_link  :'' ) ?>" target="_blank"> <?php echo ( isset($recordDetail->v_tracking_link) ? ' <br> '. $recordDetail->v_tracking_link  :'' )?></a>
							</td>
							<td class="text-left"><?php echo (isset($recordDetail->dt_collection_date) ?  clientDate($recordDetail->dt_collection_date) : '' )
													.(isset($recordDetail->dt_delivery_date) ?  ' <br> '.clientDate($recordDetail->dt_delivery_date) : '' )?>
							</td>
							<td class="text-left"><?php echo ( isset($recordDetail->statusInfo->v_status) ? $recordDetail->statusInfo->v_status  :'' ) ?></td>
							<td class="text-left"><?php echo (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_DEFAULT_CURRENCY')  : '' ) ?></td>
							<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false) ){?>
								<td class="actions-col">
									<?php if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false){?>
									<a href="{{route('port-to-agent-warehouse.edit', $encodeRecordId )}}" title="{{trans('messages.edit-record')}}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
								<?php }?>
								<a target="_blank" href="{{route('port-to-agent-warehouse.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
								<?php if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false){?>
										<?php if($processStatus == config('constants.PENDING_STATUS')){?>
											<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="port-to-agent-warehouse" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
										<?php }?>
									<?php }?>
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
				}?>	
				@include('admin/common-display-count')
			</tbody>
			
		</table>
	</div>
		{{ $recordDetails->onEachSide(1)->links() }}
</div>
