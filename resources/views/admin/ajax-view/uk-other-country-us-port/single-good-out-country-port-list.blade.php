								<?php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
								<?php 
								$paymentValue = ( isset($recordDetail->d_payment_value) ? decimalAmount($recordDetail->d_payment_value).' ' : '');
								$allInvoiceDetails = ( isset($recordDetail->invoiceInfo) ? json_decode(json_encode($recordDetail->invoiceInfo),true) : [] );
								$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
								$paymentValue = $finalCharge;
								$containerStatus = (!empty($recordDetail->portToAgentaContainerInfo->e_container_status) ? $recordDetail->portToAgentaContainerInfo->e_container_status :'');
								
								$fbaStatus = "";
								if((isset($recordDetail->uploadFBASheetInfo->e_status)) && ($recordDetail->uploadFBASheetInfo->e_status == config("constants.SUCCESS_STATUS"))){
									switch ($containerStatus){
										case config('constants.PENDING_STATUS') :
											$fbaStatus = config("constants.PORT_TO_AGENT_WAREHOUSE_NO");
											break;
										case config('constants.PARTIAL_DELIVERY_TYPE') :
											$fbaStatus = config("constants.AGENT_WAREHOUSE_TO_AMAZON_NO");
											break;
										case config('constants.COMPLETED_STATUS') :
											$fbaStatus = config("constants.AGENT_WAREHOUSE_TO_AMAZON_COMPLETED_NO");
											break;
										default :
											$fbaStatus = config("constants.UK_OTHER_COUNTRY_TO_PORT_NO");
									}
								} 
								?>
								<td class="sr-col text-center">{{ $rowIndex }}</td>
								<td class="text-left"><?php echo (isset($recordDetail->v_country_to_port_record_no) ? ($recordDetail->v_country_to_port_record_no) : '')?></td>
								<td class="text-left"><?php echo (isset($recordDetail->e_transport_way) ? 
										($recordDetail->e_transport_way).' <br> ' : '' )
										.(isset($recordDetail->fromPortInfo->v_warehouse_name) ? ($recordDetail->fromPortInfo->v_warehouse_name).' <br> ' : '' ) 
										.(isset($recordDetail->toPortInfo->v_warehouse_name) ? ($recordDetail->toPortInfo->v_warehouse_name).' <br> ' : '' ) ?>
								</td>
								<td class="text-left"><?php echo (isset($recordDetail->bookEmployeeInfo->v_name) ? $recordDetail->bookEmployeeInfo->v_name .( isset($recordDetail->bookEmployeeInfo->v_department) ? ' ('.$recordDetail->bookEmployeeInfo->v_department.')'  : '' ) :'')?></td>
								<td class="text-left"><?php echo (isset($recordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $recordDetail->logisticPartnerMaster->v_logistic_partner_name : '' ) ?></td>
								<td class="text-left"><?php echo (isset($recordDetail->v_container_air_waybill_no) ? $recordDetail->v_container_air_waybill_no :'')?></td>
								<td class="text-left">
									<?php echo (isset($recordDetail->dt_est_dispatch_date) ? clientDate($recordDetail->dt_est_dispatch_date) :'')
									 		.(isset($recordDetail->dt_est_port_arrival_date) ? '<br>' . clientDate($recordDetail->dt_est_port_arrival_date) :'')
									 		.(isset($recordDetail->dt_delivery_date) ? '<br>' . clientDate($recordDetail->dt_delivery_date) :'')
									?>
								</td>
								<td class="text-left">
									<?php echo (!empty($paymentValue) ? decimalAmount( $paymentValue ) . ' ' . config('constants.GOODS_OUT_DEFAULT_CURRENCY') . '<br>' : '' ) . (isset($recordDetail->i_total_pallets) ? decimalAmount($recordDetail->i_total_pallets).' ' : '') ?>
								</td>
								<td class="text-left"><?php echo (isset($recordDetail->e_dangerous_goods) ?  strtoupper( $recordDetail->e_dangerous_goods ) : '' ) ?></td>
								<td class="text-left"><?php echo (isset($recordDetail->v_tracking_no) ?  ( $recordDetail->v_tracking_no ) . ' <br> ' : '' ) ?>
										<a href="<?php echo ( isset($recordDetail->v_tracking_link) ?  $recordDetail->v_tracking_link  :'' ) ?>" target="_blank"> <?php echo ( isset($recordDetail->v_tracking_link) ?  $recordDetail->v_tracking_link  :'' )?></a>
								</td>
								<td class="text-left"><?php echo (isset($recordDetail->statusInfo->v_status) ? ($recordDetail->statusInfo->v_status) : '')?></td>
								<td class="text-center">
									<?php if(checkPermission(config('permission_constants.VIEW_FBA_SHEET_MASTER')) != false){?>
										<?php if( $recordDetail->e_process_status == config('constants.PENDING_STATUS') ) {  ?>
										<a href="javascript:void(0)" onclick="openUploadSheetModal(this);" data-record-id="{{ $encodeRecordId }}"  class="btn btn-success border-0 mb-1">{{ trans('messages.upload') }}</a> <br>
										<?php } ?>
										<?php if( !empty($recordDetail->i_lastet_import_file_id) ) { ?>
										<?php if( isset($recordDetail->uploadFBASheetInfo->i_id) && ( $recordDetail->uploadFBASheetInfo->i_id > 0 ) ) { ?>
											
												<a href="{{ route('country-to-port.view-fba-sheet', $encodeRecordId )  }}" target="_blank" title="{{ trans('messages.view') }}" data-record-id="<?php echo $encodeRecordId ?>" onclick="showFbaSheetRecordDetails(this)">{{ trans('messages.view') }}</a>
												<?php } ?>
										<?php } ?>
									<?php } ?>
								</td>
								
								<td class="text-left">
									<?php if( isset($recordDetail->uploadFBASheetInfo->i_id) && ( $recordDetail->uploadFBASheetInfo->i_id > 0 ) ) { ?>
										<?php if( $recordDetail->uploadFBASheetInfo->e_status == config('constants.FAILED_STATUS') ) { ?>
											<?php echo $recordDetail->uploadFBASheetInfo->e_status ?> <a href="javascript:void(0)" class="btn failed-btn ml-1" data-recoprd-date="<?php echo (!empty($recordDetail->uploadFBASheetInfo->dt_created_at) ? clientDate($recordDetail->uploadFBASheetInfo->dt_created_at) : '')?>" data-record-name="<?php echo (!empty($recordDetail->uploadFBASheetInfo->v_file_path) ? basename($recordDetail->uploadFBASheetInfo->v_file_path) : '') ?>" title="{{ trans('messages.view-details') }}" data-record-id="{{ $encodeRecordId }}" onclick="showRemarkModal(this);"><i class="fa fa-list"></i></a>		
										<?php } else {  ?>
												<?php echo $recordDetail->uploadFBASheetInfo->e_status ?> <br> {{ $fbaStatus }}
													<?php 
												}?>
										<?php } ?>
								</td>
								
								<td class="text-left"><?php echo (!empty($recordDetail->v_personal_ref) ? $recordDetail->v_personal_ref : '')?></td>
								<td class="text-left"><?php echo (isset($recordDetail->fromWarehouseCountry) && !empty($recordDetail->fromWarehouseCountry->v_country_name) ? $recordDetail->fromWarehouseCountry->v_country_name : '')?><?php echo (isset($recordDetail->warehouseInfo) && !empty($recordDetail->warehouseInfo->v_warehouse_name) ? ' - ' . $recordDetail->warehouseInfo->v_warehouse_name : '') ?></td>
								<td class="text-left"><?php echo (!empty($recordDetail->dt_pick_up_date_from_warehouse) ? clientDate($recordDetail->dt_pick_up_date_from_warehouse) : '')?></td>
								<td class="actions-col">
								<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false) ){?>
										<?php if(checkPermission(config('permission_constants.EDIT_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false){?>
										<?php if($recordDetail->i_status_id != config('constants.DELIVERED_STATUS_ID') ){?>
										<a href="{{route('uk-other-country-us-port.edit', $encodeRecordId )}}" title="{{trans('messages.edit-record')}}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<?php } ?>
									<?php } ?>
									<?php if(checkPermission(config('permission_constants.DELETE_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false){?>
										<?php if( $recordDetail->e_process_status == config('constants.PENDING_STATUS') ) {  ?>
										<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="uk-other-country-us-port" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
										<?php } ?> 
									<?php } ?>
								<?php } ?>
									<a target="_blank" href="{{route('uk-other-country-us-port.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
								</td>
								