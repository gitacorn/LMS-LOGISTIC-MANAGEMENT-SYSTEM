<div class="card card-body card-pagination-items-class">
	{{ Wild_tiger::readMessage() }}
		<div class="table-responsive fixed-table-x">
			<table class="table table-sm table-bordered table-hover" id="user-table">
				<thead>
					<tr>
						<th style="max-width:100px;min-width:45px;">{{ trans("messages.sr-no") }}</th>
						<th class="text-left" style="max-width:100px;min-width:80px;">{{ trans("messages.logistic-entry-no") }}</th>
						<th class="text-left" style="max-width:100px;min-width:60px;">{{ trans("messages.supplier-name") }}</th>
						<th class="text-left" style="max-width:100px;min-width:80px;">{{ trans("messages.collection-delivery") }}</th>
						<th class="text-left" style="max-width:100px;min-width:80px;">{{ trans("messages.mode-of-transport") }}</th>
						<th class="text-left" style="max-width:100px;min-width:70px;">{{ trans("messages.book-by") }}</th>
						<th class="text-left" style="max-width:100px;min-width:80px;">{{ trans("messages.logistic-partner") }}</th>
						<th class="text-left" style="max-width:100px;min-width:80px;">{{ trans("messages.collection-date") }}</th>
						<th class="text-left" style="max-width:100px;min-width:80px;">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
						<th class="text-left" style="max-width:100px;min-width:70px;">{{ trans("messages.delivery-date") }}</th>
						<th class="text-left" style="max-width:100px;min-width:60px;">{{ trans("messages.final-total") }}</th>
						<th class="text-center" style="max-width:100px;min-width:65px;">{{ trans("messages.status") }}</th>
						<th class="text-left" style="max-width:100px;min-width:60px;">{{ trans("messages.goods-in-date") }}</th>
						<th class="text-center" style="max-width:100px;min-width:90px;">{{ trans("messages.actions") }}</th>
					</tr>
				</thead>
				<tbody class="">
					<?php 
					if(count($recordDetails) > 0){
						$index= ($page_no - 1) * $perPageRecord;
						foreach ($recordDetails as $recordDetail){
							$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
							$allInvoiceDetails = ( isset($recordDetail->goodInLogisticInvoice) ? json_decode(json_encode($recordDetail->goodInLogisticInvoice),true) : [] );
							$finalCharge = (!empty($allInvoiceDetails) ?  array_sum(array_column($allInvoiceDetails, 'd_final_charge')) : "" ) ;
							$allSupplierDetails = ( isset($recordDetail->supplierMaster) ? json_decode(json_encode($recordDetail->supplierMaster),true) : [] );
							$supplierNameInfo = (!empty($allSupplierDetails) ? array_column($allSupplierDetails, 'v_supplier_name') : []);
							$supplierName = ( isset($supplierNameInfo) ? ( implode(', ', $supplierNameInfo)) : '');
						
							$allModeOfTransport = [];
							if(isset($recordDetail->allGoodInBuyerDetail) && count($recordDetail->allGoodInBuyerDetail) > 0) {
								foreach($recordDetail->allGoodInBuyerDetail as $buyerDetail) {
									if(isset($buyerDetail->goodInBuyerMaster->e_mode_of_transport) && !empty($buyerDetail->goodInBuyerMaster->e_mode_of_transport)) {
										$allModeOfTransport[] = $buyerDetail->goodInBuyerMaster->e_mode_of_transport;
									}
								}
							}
							$modeOfTransport = implode(', ', array_unique($allModeOfTransport));
						?>
							<tr>
								<td class="text-center">{{++$index}}</td>
								<td class="text-left"><?php echo (isset($recordDetail->v_goods_in_logistic_master_no) ? ($recordDetail->v_goods_in_logistic_master_no) : '')?></td>
								<td class="text-left">{{ $supplierName }}</td>
								<td class="text-left"><?php echo (isset($recordDetail->e_logistic_collection_type) ? $recordDetail->e_logistic_collection_type :'')?></td>
								<td class="text-left">{{ $modeOfTransport }}</td>
								<td class="text-left"><?php echo (isset($recordDetail['employeeMaster']->v_name) ? $recordDetail['employeeMaster']->v_name . ( isset($recordDetail['employeeMaster']->v_department) ? ' ('.$recordDetail['employeeMaster']->v_department.')'  : '' ) :'')?></td>
								<td class="text-left"><?php echo (isset($recordDetail['logisticPartnerDetail']['logisticPartnerMaster']->v_logistic_partner_name) ? $recordDetail['logisticPartnerDetail']['logisticPartnerMaster']->v_logistic_partner_name . ( isset($recordDetail['logisticPartnerDetail']->v_logistic_partner_code) ? ' ('.$recordDetail['logisticPartnerDetail']->v_logistic_partner_code.')'  : '' ) :'')?></td>
								<td class="text-left"><?php echo (isset($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) : '')?></td>
								<td class="text-left"><?php echo ( isset($recordDetail->v_tracking_no) ?  ( $recordDetail->v_tracking_no ) . '<br>' : '' ) . ( isset($recordDetail->v_tracking_link) ?  ( $recordDetail->v_tracking_link )  :'' )  ?></td>
								<td class="text-left"><?php echo (isset($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) : '')?></td>
								<td class="text-left"><?php echo (!empty($finalCharge) ? decimalAmount($finalCharge)  : 0) ?></td>
								<td class="text-left"><?php echo (isset($recordDetail['statusMaster']->v_status) ? ($recordDetail['statusMaster']->v_status) : '')?></td>
								<td class="text-left"><?php echo (isset($recordDetail->dt_goods_in_date) ? clientDate($recordDetail->dt_goods_in_date) : '')?></td>
								<td class="text-center">
								<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_IN_LOGISTIC')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_IN_LOGISTIC')) != false) ){?>
									<?php if(checkPermission(config('permission_constants.EDIT_GOODS_IN_LOGISTIC')) != false){?>
											<a href="{{route('good-in-logistic.edit', $encodeRecordId )}}" title="{{trans('messages.edit-record')}}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
									<?php } ?>
									<?php if(checkPermission(config('permission_constants.DELETE_GOODS_IN_LOGISTIC')) != false){?>
											<?php /* ?>
											<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="good-in-logistic" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
											<?php */ ?>
									<?php } ?>
								<?php } ?>
								<a target="_blank" href="{{route('good-in-logistic.view', $encodeRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme text-white mb-1" ><i class="fa fa-eye"></i></a>
								
								</td>
							</tr>
						<?php 
						}
					} else {
					?>
					<tr>
						<td colspan="13" class="text-center">{{ trans('messages.no-record-found')}}</td>
					</tr>
					<?php 
					}?>
					@include('admin/common-display-count')
				</tbody>
			</table>
		</div>
		{{ $recordDetails->onEachSide(1)->links() }}
	</div>
