	<div class="card card-body card-pagination-items-class">
		{{ Wild_tiger::readMessage() }}
		<div class="table-responsive fixed-table-x fixed-table-height">
			<table class="table table-sm table-bordered table-hover" id="user-table">
				<thead>
					<tr>
						<th style="max-width:100px;min-width:40px;">{{ trans("messages.sr-no") }}</th>
						<th class="text-left" style="max-width:80px;min-width:80px;">{{ trans("messages.entry-no") }}</th>
						<th class="text-left" style="max-width:100px;min-width:85px;">{{ trans("messages.buyer-company") }}</th>
						<th class="text-left" style="max-width:70px;min-width:90px;">{{ trans("messages.buyer-name") }}</th>
						<th class="text-left" style="max-width:70px;min-width:90px;">{{ trans("messages.user-buyer-name") }}</th>
						<th class="text-left" style="max-width:80px;min-width:75px;">{{ trans("messages.supplier-name") }}</th>
						<th class="text-left" style="max-width:77px;min-width:70px;">{{ trans("messages.supplier-location") }}</th>
						<th class="text-left" style="max-width:80px;min-width:80px;width:80px">{{ trans("messages.supplier-country") }}</th>
						<th class="text-left" style="max-width:80px;min-width:80px;width:80px">{{ trans("messages.po-number") }}</th>
						<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.invoice-number") }}</th>
						<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.po-amount-with-vat") }}</th>
						<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.collection-delivery") }}</th>
						<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.mode-of-transport") }}</th>
						<th class="text-left" style="max-width:70px;min-width:70px;">{{ trans("messages.brand") }}</th>
						<th class="text-left" style="max-width:98px;min-width:98px;">{{ trans("messages.order-date") }}</th>
						<th class="text-left" style="max-width:77px;min-width:70px;">{{ trans("messages.buyer-delivery-date") }}</th>
						<th class="text-left" style="max-width:77px;min-width:70px;">{{ trans("messages.pallet-box") }}</th>
						<th class="text-left" style="max-width:77px;min-width:70px;">{{ trans("messages.delivery-location") }}</th>
						<th class="text-left" style="max-width:100px;min-width:70px;">{{ trans("messages.buyer-comments") }}</th>
						<th style="max-width:150px;min-width:80px;" class="text-center">{{ trans("messages.actions") }}</th>
					</tr>
				</thead>
				<tbody class="ajax-view">
					<?php 
					if(count($recordDetails) > 0){
						$index = ($page_no - 1) * $perPageRecord;
						foreach ($recordDetails  as $key => $recordDetail){
							$encodeRecordId = Wild_tiger::encode($recordDetail->goodInBuyerMaster->i_id);
							$deleteRecordId = Wild_tiger::encode($recordDetail->i_id);
							
							$userCompanyValue = ( isset($recordDetail->goodInBuyerMaster->companyUserMaster) ? json_decode(json_encode($recordDetail->goodInBuyerMaster->companyUserMaster),true) : [] );
							$userCompanyName = (!empty($userCompanyValue) ? array_column($userCompanyValue, 'v_company_name') : []);
							$userCompany = ( isset($userCompanyName) ? ( implode(', ', $userCompanyName)) : '');
							
							$buyerNameValue = ( isset($recordDetail->goodInBuyerMaster->employeeBuyerNameMaster) ? json_decode(json_encode($recordDetail->goodInBuyerMaster->employeeBuyerNameMaster),true) : [] );
							$employeeBuyerName = (!empty($buyerNameValue) ? array_column($buyerNameValue, 'v_name') : []);
							$employeeBuyer = ( isset($employeeBuyerName) ? ( implode(', ', $employeeBuyerName)) : '');
							
							$userBuyerNameValue = ( isset($recordDetail->goodInBuyerMaster->userBuyerNameMaster) ? json_decode(json_encode($recordDetail->goodInBuyerMaster->userBuyerNameMaster),true) : [] );
							$userBuyerName = (!empty($userBuyerNameValue) ? array_column($userBuyerNameValue, 'v_name') : []);
							$userBuyer = ( isset($userBuyerName) ? ( implode(', ', $userBuyerName)) : '');
							
							$collectionDelevery = ( ( isset($recordDetail->goodInBuyerMaster->e_collection_type) ?  ($recordDetail->goodInBuyerMaster->e_collection_type) :'' ) );	
							
							$supplierCountryDetails = (isset($recordDetail->goodInBuyerMaster->supplierMaster->supplierDetail) && !empty($recordDetail->goodInBuyerMaster->supplierMaster->supplierDetail) ? $recordDetail->goodInBuyerMaster->supplierMaster->supplierDetail : []);
							$supplierCountry = '';
							if (!empty($supplierCountryDetails) && count($supplierCountryDetails) > 0){
								foreach ($supplierCountryDetails as $key => $supplierCountryDetail){
									$supplierCountry .= (isset($supplierCountryDetail->countryMaster->v_country_name) && !empty($supplierCountryDetail->countryMaster->v_country_name) ? ($key > 0 ? ',' : '') . $supplierCountryDetail->countryMaster->v_country_name : '');
								}
							}
							
							$dimensionaValue = "";
							
							$dimensionaValueDetails = ( isset($recordDetail->goodInBuyerMaster->dimensionMaster) ? json_decode(json_encode($recordDetail->goodInBuyerMaster->dimensionMaster),true) : [] );
							if(!empty($dimensionaValueDetails)){
								foreach($dimensionaValueDetails as $dimensionaValueDetail){
									$dimensionaValue .= ( isset($dimensionaValueDetail['v_dimension_name']) ? $dimensionaValueDetail['v_dimension_name'] . ( isset($dimensionaValueDetail['v_dimension_size']) ? ' ('.$dimensionaValueDetail['v_dimension_size'].')' : '' ) . ', ' : '' );
								}
								$dimensionaValue = rtrim($dimensionaValue , ', ');
							}
							?>
							<tr data-record-id="{{ ( ( isset($recordDetail->goodInBuyerMaster->i_id) ?  ($recordDetail->goodInBuyerMaster->i_id) :'' ) ) }}">
								<td class="text-center">{{++$index}}</td>
								<td class="text-center">{{ ( isset($recordDetail->v_goods_in_buyer_detail_no) ? $recordDetail->v_goods_in_buyer_detail_no : '' ) }} <br> <?php echo  ( ( ( $recordDetail->t_in_use == 1 ) && ( checkPermission(config('permission_constants.VIEW_GOODS_IN_LOGISTIC')) != false ) ) ? '<a target="_blank" href="'.config("constants.GOODS_IN_LOGITIC_MASTER_URL")."/".$recordDetail->v_goods_in_buyer_detail_no.'" title="'.trans('messages.view-record').'" class="btn btn-sm bg-theme btn-submit-class text-white" ><i class="fa fa-eye"></i></a>' : ''  ) ?></td>
								<td>{{ ( isset($recordDetail->goodInBuyerMaster->companyMaster->v_company_name) ? $recordDetail->goodInBuyerMaster->companyMaster->v_company_name :'' ) }} </td>
								<td>{{ ( isset($employeeBuyer) ? ($employeeBuyer) : '' )}}</td>
								<td>{{ ( isset($userBuyer) ? ($userBuyer) : '' )}}</td>
								<td><?php echo ( isset($recordDetail->supplierMaster->supplierMaster->v_supplier_name) ?  ( $recordDetail->supplierMaster->supplierMaster->v_supplier_name ) : '') ?></td>
								<td><?php echo (isset($recordDetail->supplierMaster->v_supplier_address) && !empty($recordDetail->supplierMaster->v_supplier_address) ? $recordDetail->supplierMaster->v_supplier_address : '')?></td>
								<td><?php echo  (isset($recordDetail->supplierMaster->countryMaster->v_country_name) && !empty($recordDetail->supplierMaster->countryMaster->v_country_name) ? $recordDetail->supplierMaster->countryMaster->v_country_name : '') ?></td>
								<td><?php echo  ( isset($recordDetail->goodInBuyerMaster->v_po_sales_invoice_no) ?  ($recordDetail->goodInBuyerMaster->v_po_sales_invoice_no) : '') ?></td>
								<td><?php echo (isset($recordDetail->goodInBuyerMaster->v_invoice_no) ? $recordDetail->goodInBuyerMaster->v_invoice_no : '')?></td>
								<td><?php echo (isset($recordDetail->goodInBuyerMaster->d_po_amount_with_vat) ? decimalAmount( $recordDetail->goodInBuyerMaster->d_po_amount_with_vat ) : '') . '<br>' . (isset($recordDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code) ? ( $recordDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code ) : '') ?></td>
								<td>{{ (!empty($collectionDelevery) ? $collectionDelevery : '') }} <?php echo ( ( ( isset($recordDetail->goodInBuyerMaster->e_ready_for_collection_status) && ( isset($recordDetail->goodInBuyerMaster->e_collection_type) ) && ($recordDetail->goodInBuyerMaster->e_collection_type  == config('constants.COLLECTION')) ) ? '<br>' . ($recordDetail->goodInBuyerMaster->e_ready_for_collection_status) :'' ) ) ?></td>
								<td>{{ ( isset($recordDetail->goodInBuyerMaster->e_mode_of_transport) ? $recordDetail->goodInBuyerMaster->e_mode_of_transport : '' ) }}</td>
								
								<td><?php echo (isset($recordDetail->goodInBuyerMaster->v_brand) ? $recordDetail->goodInBuyerMaster->v_brand : '')?></td>
								<td>{{ ( isset($recordDetail->goodInBuyerMaster->dt_order_date) ?  clientDate($recordDetail->goodInBuyerMaster->dt_order_date) :'' )}}</td>
								<td><?php echo (isset($recordDetail->goodInBuyerMaster->dt_delivery_date) ? clientDate($recordDetail->goodInBuyerMaster->dt_delivery_date) : '') ?></td>
								<td><?php echo (isset($recordDetail->goodInBuyerMaster->e_pallet_box_type) ? $recordDetail->goodInBuyerMaster->e_pallet_box_type : '' ) . '<br>' . (isset($recordDetail->goodInBuyerMaster->i_no_of_pallet_box) ? $recordDetail->goodInBuyerMaster->i_no_of_pallet_box : '')   ?></td>
								
								<td>{{ (isset($recordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_name) ? $recordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_name .(isset($recordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_code) ? ' (' .( $recordDetail->goodInBuyerMaster->warehouseMaster->v_warehouse_code ) .')' :'' ) :'' ) }}</td>
								<td>{{ (!empty($recordDetail->goodInBuyerMaster->v_buyer_comments) ? $recordDetail->goodInBuyerMaster->v_buyer_comments : '') }}</td>
								<td style="max-width:150px;min-width:100px;" class="text-center">
								<?php if( (checkPermission(config('permission_constants.EDIT_GOODS_IN_BUYER')) != false) || (checkPermission(config('permission_constants.DELETE_GOODS_IN_BUYER')) != false) ){?>
									<?php if(checkPermission(config('permission_constants.EDIT_GOODS_IN_BUYER')) != false){?>
											<a href="{{route('good-in-buyer.edit', $deleteRecordId )}}" title="{{trans('messages.edit-record')}}" class="btn btn-sm btn-submit-class btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
									<?php }?>
									<?php if(checkPermission(config('permission_constants.DELETE_GOODS_IN_BUYER')) != false){?>
										<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $deleteRecordId }}" data-module-name="good-in-buyer" onclick="deleteRecord(this);" class="btn btn-sm btn-submit-class btn-danger mb-1 delete-record-button"><i class="fa fa-trash fa-fw"></i></button>
									<?php } ?>
								<?php }?>
									<a target="_blank" href="{{route('good-in-buyer.view', $deleteRecordId )}} " title="{{trans('messages.view-record')}}" class="btn btn-sm bg-theme btn-submit-class text-white mb-1" ><i class="fa fa-eye"></i></a>
									<?php if(checkPermission(config('permission_constants.ADD_GOODS_IN_LOGISTIC')) != false && $recordDetail->t_in_use == 0){?>
									<a target="_blank" href="{{ config('constants.GOODS_IN_LOGITIC_MASTER_URL') .'/create/' .Wild_tiger::encode($recordDetail->goodInBuyerMaster->i_main_supplier_id).'/'.$recordDetail->goodInBuyerMaster->e_collection_type}} " title="{{trans('messages.create-logistic-entry')}}" class="btn btn-sm btn-submit-class btn-info mb-1" ><i class="fas fa-plus mr-md-2"></i></a>
									<?php } ?>
									<?php if( ( session()->get('role') == config('constants.ROLE_ADMIN') ) && /* ( $recordDetail->e_buyer_record_status != config('constants.CANCELLED_DELIVERY_TYPE') )  && */ ( $recordDetail->goodInBuyerMaster->e_collection_type == config('constants.DELIVERY')) ) { ?>
									<button title="{{trans('messages.cancel')}}" onclick="updateCancelledStatus(this);" data-record-id="{{ $deleteRecordId }}" class="btn btn-sm btn-warning btn-submit-class mb-1"><i class="fa fa-times"></i></button>
									<?php } ?>
									@if($collectionDelevery == config('constants.COLLECTION'))
										<a href="{{ config('constants.SHIPMENT_QUOTE_PDF_URL') .'/'.$deleteRecordId }}" target="_blank" title="{{trans('messages.pdf')}}"  class="btn btn-sm btn-info btn-submit-class mb-1"><i class="fa fa-file-pdf"></i></a>
									@endif
								</td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan="20" class="text-center">{{ trans('messages.no-record-found')}}</td>
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
</div>
	