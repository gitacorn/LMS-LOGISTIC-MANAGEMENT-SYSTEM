@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/dataTables.bootstrap4.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset ('css/fixedheader-datatables.min.css') }}">
<script type="text/javascript" src="{{ asset ('js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/dataTables.bootstrap4.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/datatables-fixedheader.min.js') }}"></script>


<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			@if((checkPermission(config('permission_constants.EXCEL_EXPORT_TRACKING_GOODS_IN_REPORT')) != false))
            	<button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
            	<button type="button" title="{{ trans('messages.export-summary') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData('{{ config('constants.ACTION_SUMMARY_EXPORT') }}');"><i class="fas fa-upload mr-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-summary") }}</span></button>
            @endif
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			//$tableSearchPlaceholder = "Search By Entry No., PO No. / Sales Invoice No., Logistic Entry No., Tracking No., Tracking Link";
			$tableSearchPlaceholder = "Search By PO No. / Sales Invoice No.";
			?>

			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-6 col-md-12">
							<div class="form-group">
								<label for="search_by_logistic_partner_name" class="control-label">{{ trans("messages.search-by") }} <span data-toggle="tooltip" title="{{ trans('messages.search-by') . ' ' . trans('messages.po-number') . ', ' . trans('messages.vendor-number') . ', ' . trans('messages.invoice-number') . ', ' . trans('messages.brand') . ', ' . trans('messages.entry-no') . ', ' . trans('messages.logistic-entry-no') . ', ' . trans('messages.buyer-comments') }}"><i class="fas fa-info-circle text-black"></i></span></label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_logistic_partner_name" id="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by') . ' ' . trans('messages.po-number') . ', ' . trans('messages.vendor-number') . ', ' . trans('messages.invoice-number') . ', ' . trans('messages.brand') . ', ' . trans('messages.entry-no') . ', ' . trans('messages.logistic-entry-no') . ', ' . trans('messages.buyer-comments') }}" >
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_company">{{ trans("messages.buyer-company") }}</label>
								<select name="search_buyer_company" class="form-control select2" multiple="" onchange="filterData()">
									<?php 
									if(!empty($companyRecordDetails)){
										foreach ($companyRecordDetails as $companyRecordDetail){
											$encodeCompanyId  = Wild_tiger::encode($companyRecordDetail->i_id);
											?>
                                        		<option value="{{ $encodeCompanyId }}">{{ (!empty($companyRecordDetail->v_company_name) ? $companyRecordDetail->v_company_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
									?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_user_company">{{ trans("messages.user-company") }}</label>
								<select name="search_user_company" class="form-control select2" multiple="" onchange="filterData()">
									<?php 
									if(!empty($companyRecordDetails)){
										foreach ($companyRecordDetails as $companyRecordDetail){
											$encodeCompanyId  = Wild_tiger::encode($companyRecordDetail->i_id);
											?>
                                        		<option value="{{ $encodeCompanyId }}">{{ (!empty($companyRecordDetail->v_company_name) ? $companyRecordDetail->v_company_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
									?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_name">{{ trans("messages.buyer-name") }}</label>
								<select name="search_buyer_name" class="form-control select2" multiple onchange="filterData()">
									<?php 
                                    if(!empty($userRecordDetails)){
                                    	foreach ($userRecordDetails as $userRecordDetail){
                                        	$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodevUserId }}"><?php echo (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name : '' ) .(!empty($userRecordDetail->v_department) ? ' ( '. $userRecordDetail->v_department .')' :'') ?></option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}</label>
								<select name="search_collection_delivery" class="form-control" onchange="showCollectionDeliveryData(this),filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                   	if(!empty($collectionDeliveryInfo)){
                                    	foreach ($collectionDeliveryInfo as $key=>$collectionDelivery){
                                     		?>
                                        	<option value="{{$key}}">{{ $collectionDelivery }}</option>
                                        	<?php 
                                        }
                                    }
                                   ?>
								</select>
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_delivery_collection_location" class="control-label">{{ trans("messages.delivery-location") }}</label>
								<select name="search_delivery_collection_location" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
                                     if(!empty($warehouseRecordDetails)){
                                     	foreach ($warehouseRecordDetails as $warehouseRecordDetail){
                                       		$encodeId = Wild_tiger::encode($warehouseRecordDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($warehouseRecordDetail->v_warehouse_name) ? $warehouseRecordDetail->v_warehouse_name .(!empty($warehouseRecordDetail->v_warehouse_code) ? ' (' .$warehouseRecordDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        <?php 
                                    	}
                                     }
                                     ?>
								</select>
							</div>
						</div>
						<?php /*?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-location" style="display: none">
							<div class="form-group">
								<label for="search_delivery_collection_location" class="control-label">{{ trans("messages.collection-location") }}</label>
								<select name="search_delivery_collection_location" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
                                     if(!empty($warehouseRecordDetails)){
                                     	foreach ($warehouseRecordDetails as $warehouseRecordDetail){
                                       		$encodeId = Wild_tiger::encode($warehouseRecordDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($warehouseRecordDetail->v_warehouse_name) ? $warehouseRecordDetail->v_warehouse_name .(!empty($warehouseRecordDetail->v_warehouse_code) ? ' (' .$warehouseRecordDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        <?php 
                                    	}
                                     }
                                     ?>
								</select>
							</div>
						</div>
						<?php */?>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 delivery-collection-location"  style="display: none">
							<div class="form-group">
								<label class="control-label" for="search_ready_for_collection">{{ trans("messages.ready-for-collection") }}</label>
								<select name="search_ready_for_collection" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 @if(!empty($readyForCollectionInfo))
	                                     	@foreach($readyForCollectionInfo as $key => $readyForCollection)
	                                        	<option value='{{ $key }}'>{{ $readyForCollection}}</option>
	                                       	@endforeach
	                                 	@endif
								</select>
							</div>
						</div>
						<?php /*?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-row" style="display: none">
							<div class="form-group">
								<label for="search_delivery_type" class="control-label">{{ trans("messages.delivery-type") }}</label>
								<select name="search_delivery_type" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
									if(!empty($deliveryTypeInfo)){
										foreach ($deliveryTypeInfo as $key => $deliveryType){
											?>
                                       		<option value="{{$key}}">{{ $deliveryType }}</option>
                                        	<?php 
                                   		}
                                 	}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-row" style="display: none">
							<div class="form-group">
								<label for="search_delivery_location" class="control-label">{{ trans("messages.delivery-location") }}</label>
								<select name="search_delivery_location" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($warehouseRecordDetails)){
                                        	foreach ($warehouseRecordDetails as $warehouseRecordDetail){
                                        		$encodeWarehouseId  = Wild_tiger::encode($warehouseRecordDetail->i_id);
                                        		?>
                                        		<option value="{{ $encodeWarehouseId }}">{{ (!empty($warehouseRecordDetail->v_warehouse_name) ? $warehouseRecordDetail->v_warehouse_name .(!empty($warehouseRecordDetail->v_warehouse_code) ? ' (' .$warehouseRecordDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
								</select>
							</div>
						</div>
						<?php */?>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_name">{{ trans("messages.supplier-name") }}</label>
								<select name="search_supplier_name" class="form-control supplier-name-list select2" multiple onchange="getSupplierLocationDetails(this),filterData()">
									<?php /*
									if(!empty($supplierRecordDetails)){
										foreach ($supplierRecordDetails as $supplierRecordDetail){
											$encodeSupplierId  = Wild_tiger::encode($supplierRecordDetail->i_id);
											?>
	                                        <option value="{{ $encodeSupplierId }}">{{ (!empty($supplierRecordDetail->v_supplier_name) ? $supplierRecordDetail->v_supplier_name : '' ) }}</option>
	                                        <?php 
	                                 	}
	                                } 
									*/?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_supplier_country" class="control-label">{{ trans("messages.supplier-country") }}</label>
								<select name="search_supplier_country" class="form-control select2" onchange="getSupplierNameDetails(this),filterData()">
									
									<option value="">{{ trans("messages.select") }}</option>
                                    <?php 
                                    if(!empty($supplierCountryDetails)){
                                    	foreach ($supplierCountryDetails as $supplierCountryDetail){
                                        	$encodeId  = Wild_tiger::encode($supplierCountryDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($supplierCountryDetail->v_country_name) ? $supplierCountryDetail->v_country_name : '' ) }}</option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_location">{{ trans("messages.supplier-location") }}</label>
								<select name="search_supplier_location" class="form-control select2 supplier-location-list" multiple onchange="filterData()">
									<?php /*
									if(!empty($supplierlocationDetails)){
										foreach ($supplierlocationDetails as $supplierlocationDetail){
											$encodeSupplierLocationId  = Wild_tiger::encode($supplierlocationDetail->i_id);
											?>
											<option value="{{ $encodeSupplierLocationId }}"><?php echo (!empty($supplierlocationDetail->v_supplier_address) ? $supplierlocationDetail->v_supplier_address : '' ) . (!empty($supplierlocationDetail->supplierMaster->v_supplier_name) ? ' (' .$supplierlocationDetail->supplierMaster->v_supplier_name .')' : '' ) ?></option>
											<?php 
										}
									}
									*/ ?>
								</select>
							</div>
						</div>
						
						<?php /* 
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_payment_status" class="control-label">{{ trans("messages.payment-status") }}</label>
								<select name="search_payment_status" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    if(!empty($paymentStatusInfo)){
                                    	foreach ($paymentStatusInfo as $paymentStatus){
                                        	?>
                                        	<option value="{{$paymentStatus}}">{{ $paymentStatus }}</option>
                                        	<?php 
                                  		}
                               		}
                                    ?>
								</select>
							</div>
						</div>
						 */?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_order_from_date">{{ trans("messages.order-from-date") }}</label>
								<input type="text" name="search_order_from_date" class="form-control" placeholder="{{ trans('messages.order-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_order_to_date">{{ trans("messages.order-to-date") }}</label>
								<input type="text" name="search_order_to_date" class="form-control" placeholder="{{ trans('messages.order-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_invoice_from_date">{{ trans("messages.invoice-from-date") }}</label>
								<input type="text" name="search_invoice_from_date" class="form-control" placeholder="{{ trans('messages.invoice-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_invoice_to_date">{{ trans("messages.invoice-to-date") }}</label>
								<input type="text" name="search_invoice_to_date" class="form-control" placeholder="{{ trans('messages.invoice-to-date') }}">
							</div>
						</div>
						
						<div class="col-lg-3  col-md-4 col-sm-6 book-by-filter">
							<div class="form-group">
								<label for="search_book_by" class="control-label">{{ trans("messages.book-by") }}</label>
								<select name="search_book_by" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="None">None</option>
									<?php 
                                    if(!empty($userRecordDetails)){
                                    	foreach ($userRecordDetails as $userRecordDetail){
                                        	$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodevUserId }}"><?php echo (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name : '' ) .(!empty($userRecordDetail->v_department) ? ' ( '. $userRecordDetail->v_department .')' :'') ?></option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_logistic_partner" class="control-label">{{ trans("messages.logistic-partner") }}</label>
								<select name="search_logistic_partner" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    if(!empty($logisticPartnerDetails)){
                                    	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
                                        	$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{  (!empty($logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerDetail->v_logistic_partner_code) ?  ' ('. $logisticPartnerDetail->v_logistic_partner_code : '' ) .')' : '' ) }}</option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_collection_form_date">{{ trans("messages.collection-from-date") }}</label>
								<input type="text" name="search_collection_form_date" class="form-control" placeholder="{{ trans('messages.collection-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_collection_to_date">{{ trans("messages.collection-to-date") }}</label>
								<input type="text" name="search_collection_to_date" class="form-control" placeholder="{{ trans('messages.collection-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_form_date">{{ trans("messages.logistic-delivery-from-date") }}</label>
								<input type="text" name="search_delivery_form_date" class="form-control" placeholder="{{ trans('messages.logistic-delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_to_date">{{ trans("messages.logistic-delivery-to-date") }}</label>
								<input type="text" name="search_delivery_to_date" class="form-control" placeholder="{{ trans('messages.logistic-delivery-to-date') }}">
							</div>
						</div>

						
						<?php /* ?>	
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select name="search_status" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    if(!empty($statusDetails)){
                                    	foreach ($statusDetails as $statusDetail){
                                        	$encodeId  = Wild_tiger::encode($statusDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}"><?php echo (!empty($statusDetail->v_status) ? $statusDetail->v_status : '' ) ?></option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<?php */ ?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_logistic_delivery_type" class="control-label">{{ trans("messages.logistic-delivery-type") }}</label>
								<select name="search_logistic_delivery_type" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
									if(!empty($deliveryTypeInfo)){
										foreach ($deliveryTypeInfo as $key => $deliveryType){
											?>
                                       		<option value="{{$key}}">{{ $deliveryType }}</option>
                                        	<?php 
                                   		}
                                 	}
									?>
								</select>
							</div>
						</div>
						<div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.logistic") }} {{ trans("messages.status") }}</label>
								<a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
								<select name="search_status"  multiple class="form-control select2" onchange="filterData();">
									@if(!empty($statusMasterDetails))
								    	@foreach ($statusMasterDetails as $statusMasterDetail)
								    		<option value="{{ Wild_tiger::encode($statusMasterDetail->i_id)  }}">{{ (!empty($statusMasterDetail->v_status) ? $statusMasterDetail->v_status : '' ) }}</option>
								    	@endforeach
								    @endif
								</select>
								
							</div>
							
							
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_po_creation_from_date">{{ trans("messages.po-creation-from-date") }}</label>
								<input type="text" name="search_po_creation_from_date" class="form-control" placeholder="{{ trans('messages.po-creation-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_po_creation_to_date">{{ trans("messages.po-creation-to-date") }}</label>
								<input type="text" name="search_po_creation_to_date" class="form-control" placeholder="{{ trans('messages.po-creation-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_customs_procedure">{{ trans("messages.customs-procedure") }}</label>
								<select name="search_customs_procedure" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(isset($customsProcedureInfo) && !empty($customsProcedureInfo))
										@foreach($customsProcedureInfo as $customsProcedureKey => $customsProcedureValue)
                                    		<option value="{{ $customsProcedureKey }}"><?php echo (!empty($customsProcedureValue) ? $customsProcedureValue : '' ) ?></option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<?php /* ?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_currency_code">{{ trans("messages.currency-code") }}</label>
								<select name="search_currency_code" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(isset($currencyRecordDetails) && !empty($currencyRecordDetails))
										@foreach($currencyRecordDetails as $currencyRecordDetail)
											@php
												$encodeCurrencyId  = Wild_tiger::encode($currencyRecordDetail->i_id);
											@endphp
                                    		<option value="{{ $encodeCurrencyId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<?php */ ?>
						<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_dangerous_goods">{{ trans("messages.dangerous-goods") }}</label>
								<select name="search_dangerous_goods" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(isset($dangerousGoodsDetails) && !empty($dangerousGoodsDetails))
										@foreach($dangerousGoodsDetails as $dangerousGoodsDetail)
											@php
												$encodeDangerousGoodsId  = Wild_tiger::encode($dangerousGoodsDetail->i_id);
											@endphp
                                    		<option value="{{ $encodeDangerousGoodsId }}"><?php echo (!empty($dangerousGoodsDetail->v_value) ? $dangerousGoodsDetail->v_value : '' ) ?></option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_user_buyer_name">{{ trans("messages.user-buyer-name") }}</label>
								<select name="search_user_buyer_name" multiple class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(isset($userBuyerRecordDetails) && !empty($userBuyerRecordDetails))
										@foreach($userBuyerRecordDetails as $userBuyerRecordDetail)
											@php
												$encodeUserBuyerId  = Wild_tiger::encode($userBuyerRecordDetail->i_id);
											@endphp
                                    		<option value="{{ $encodeUserBuyerId }}"><?php echo (!empty($userBuyerRecordDetail->v_name) ? $userBuyerRecordDetail->v_name . (!empty($userBuyerRecordDetail->v_department) ?  ' ('. $userBuyerRecordDetail->v_department . ')' : '' ) : '' ) ?></option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_payment_terms">{{ trans("messages.payment-terms") }}</label>
								<select name="search_payment_terms" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(isset($paymentTermsDetails) && !empty($paymentTermsDetails))
										@foreach($paymentTermsDetails as $paymentTermsDetail)
											@php
												$encodePaymentTermsId  = Wild_tiger::encode($paymentTermsDetail->i_id);
											@endphp
                                    		<option value="{{ $encodePaymentTermsId }}"><?php echo (!empty($paymentTermsDetail->v_value) ? $paymentTermsDetail->v_value : '' ) ?></option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_payment_from_date">{{ trans("messages.payment-from-date") }}</label>
								<input type="text" name="search_payment_from_date" class="form-control" placeholder="{{ trans('messages.payment-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_payment_to_date">{{ trans("messages.payment-to-date") }}</label>
								<input type="text" name="search_payment_to_date" class="form-control" placeholder="{{ trans('messages.payment-to-date') }}">
							</div>
						</div>

						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_actual_payment_from_date">{{ trans("messages.actual-payment-from-date") }}</label>
								<input type="text" name="search_actual_payment_from_date" class="form-control" placeholder="{{ trans('messages.actual-payment-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_actual_payment_to_date">{{ trans("messages.actual-payment-to-date") }}</label>
								<input type="text" name="search_actual_payment_to_date" class="form-control" placeholder="{{ trans('messages.actual-payment-to-date') }}">
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_delivery_from_date">{{ trans("messages.buyer-delivery-from-date") }}</label>
								<input type="text" name="search_buyer_delivery_from_date" class="form-control" placeholder="{{ trans('messages.buyer-delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_delivery_to_date">{{ trans("messages.buyer-delivery-to-date") }}</label>
								<input type="text" name="search_buyer_delivery_to_date" class="form-control" placeholder="{{ trans('messages.buyer-delivery-to-date') }}">
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_goods_in_from_date">{{ trans("messages.goods-in-from-date") }}</label>
								<input type="text" name="search_goods_in_from_date" class="form-control" placeholder="{{ trans('messages.goods-in-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_goods_in_to_date">{{ trans("messages.goods-in-to-date") }}</label>
								<input type="text" name="search_goods_in_to_date" class="form-control" placeholder="{{ trans('messages.goods-in-to-date') }}">
							</div>
						</div>
						
						<div class="col-lg-2 col-md-3 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_pallets_boxes_type">{{ trans("messages.pallet-box") }}</label>
								<select name="search_pallets_boxes_type" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(isset($palletBoxInfo) && !empty($palletBoxInfo))
										@foreach($palletBoxInfo as $palletBoxKey => $palletBoxValue)
											<option value="{{ $palletBoxKey }}"><?php echo (!empty($palletBoxValue) ? $palletBoxValue : '' ) ?></option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_goods_remark" class="control-label">{{ trans("messages.goods-remark") }}</label>
								<select name="search_goods_remark[]" class="form-control select2" multiple onchange="filterData();">
									<?php 
										if(!empty($goodsRemarksDetails)){
											foreach ($goodsRemarksDetails as $goodsRemarksDetail){
												$encodeGoodsRemarkId = Wild_tiger::encode($goodsRemarksDetail->i_id);
												?>
												<option value="{{ $encodeGoodsRemarkId }}">{{ $goodsRemarksDetail->v_value }}</option>
												<?php 
											}
										}
									?>
								</select>
							</div>
						</div>
						
						<?php /* ?>
						<div class="col-lg-2 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_no_of_pallets_boxes">{{ trans("messages.no-of-pallet-box") }}<span class="text-danger">*</span></label>
								<input type="text" name="search_no_of_pallets_boxes" class="form-control" onkeyup="naturalNumber(this)" placeholder="{{ trans('messages.no-of-pallet-box') }}" value="{{old('no_of_pallets_boxes',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->i_no_of_pallet_box))) ? $recordInfo->goodInBuyerMaster->i_no_of_pallet_box : '' ) )}}">
							</div>
						</div>
						<?php */ ?>
						
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-xl-3">
							<button type="button" title="{{ trans('messages.search') }}" onclick="filterData()" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body card-pagination-items-class">
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover" id="user-table">
							<thead>
								<tr>
									<th class="sr-col text-center">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.po-number") }}</th>
									<th class="text-left">{{ trans("messages.entry-no") }}</th>
									<th class="text-left">{{ trans("messages.logistic-entry-no") }}</th>
									<th class="text-left">{{ trans("messages.vendor-number") }}</th>
									<th class="text-left">{{ trans("messages.supplier-name") }}</th>
									<th class="text-left">{{ trans("messages.supplier-country") }}</th>
									<th class="text-left">{{ trans("messages.invoice-number") }}</th>
									<th class="text-left">{{ trans("messages.buyer-company") }}</th>
									<th class="text-left">{{ trans("messages.user-company") }}</th>
									<th class="text-left">{{ trans("messages.buyer-name") }}</th>
									<th class="text-left">{{ trans("messages.user-buyer-name") }}</th>
									<th class="text-left">{{ trans("messages.goods-remark") }}</th>
									<th class="text-left">{{ trans("messages.brand") }}</th>
									<th class="text-left">{{ trans("messages.customs-procedure") }}</th>
									<th class="text-left">{{ trans("messages.dangerous-goods") }}</th>
									<th class="text-left">{{ trans("messages.po-amount-gbp") }}</th>
									<th class="text-left">{{ trans("messages.po-amount-with-vat-gbp") }}</th>
									<?php /* ?>
									<th class="text-left">{{ trans("messages.currency-code") }}</th>
									<?php */ ?>
									<th class="text-left">{{ trans("messages.payment-terms") }}</th>
									<th class="text-left">{{ trans("messages.collection-delivery") }}</th>
									<th class="text-left">{{ trans("messages.mode-of-transport") }}</th>
									<th class="text-left">{{ trans("messages.delivery-location") }}</th>
									<th class="text-left">{{ trans("messages.transporter-invoice-amount-gbp") }}</th>
									<th class="text-left">{{ trans("messages.po-creation-date") }}</th>
									<th class="text-left">{{ trans("messages.order-date") }}</th>
									<th class="text-left">{{ trans("messages.invoice-date") }}</th>
									<th class="text-left">{{ trans("messages.payment-date") }}</th>
									<th class="text-left">{{ trans("messages.actual-payment-date") }}</th>
									<th class="text-left">{{ trans("messages.collection-date") }}</th>
									<th class="text-left">{{ trans("messages.buyer-delivery-date") }}</th>
									<th class="text-left">{{ trans("messages.logistic-delivery-date") }}</th>
									<th class="text-left">{{ trans("messages.goods-in-date") }}</th>
									<th class="text-left">{{ trans("messages.pallet-box") }}</th>
									<th class="text-left">{{ trans("messages.no-of-pallet-box") }}</th>
									<th class="text-left">{{ trans("messages.buyer-comments") }}</th>
									<th class="text-left">{{ trans("messages.warehouse-comments") }}</th>
									<th class="text-center">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>
							<tbody class="ajax-view">
								<?php /*?>@include( config('constants.AJAX_VIEW_FOLDER') . 'tracking-goods-in/tracking-goods-in-list') */?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>
<div class="modal fade bd-example-modal-lg" id="view-document-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<h5 class="modal-title twt-document-modal-header-name" id="exampleModalLabel"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-hover mb-0">
								<thead>
									<tr>
										<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
										<th class="text-left">{{ trans("messages.type") }}</th>
										<th class="text-left">{{ trans("messages.remarks") }}</th>
										<th class="text-left">{{ trans("messages.view-documents") }}</th>
									</tr>
								</thead>
								<tbody class="view-file-modal-body">
									
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<script>
$(document).ready(function() {
	$("[name='search_order_from_date'],[name='search_order_to_date'], [name='search_actual_payment_from_date'], [name='search_actual_payment_to_date'], [name='search_invoice_from_date'], [name='search_invoice_to_date'],[name='search_goods_in_from_date'], [name='search_goods_in_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});
   	
 	$("[name='search_collection_form_date'],[name='search_collection_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});
 	$("[name='search_delivery_form_date'],[name='search_delivery_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});

 	$("[name='search_po_creation_from_date'],[name='search_po_creation_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});
   	
 	$("[name='search_payment_from_date'],[name='search_payment_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});
   	
 	$("[name='search_buyer_delivery_from_date'],[name='search_buyer_delivery_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});
});
 $(function(){
	 $("[name='search_order_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_order_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_order_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_order_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_order_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_order_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });	

		$("[name='search_actual_payment_from_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_actual_payment_to_date']").data('DateTimePicker').minDate(incrementDay);
			}else{
				$("[name='search_actual_payment_to_date']").data('DateTimePicker').minDate(false);
			}
				$(this).data("DateTimePicker").hide();
		});

		$("[name='search_actual_payment_to_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					var decrementDay = moment((e.date)).endOf('d');
					$("[name='search_actual_payment_from_date']").data('DateTimePicker').maxDate(decrementDay);
			}else{
				$("[name='search_actual_payment_from_date']").data('DateTimePicker').maxDate(false);
			}
					$(this).data("DateTimePicker").hide();
    });	

		
		$("[name='search_invoice_from_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_invoice_to_date']").data('DateTimePicker').minDate(incrementDay);
			}else{
				$("[name='search_invoice_to_date']").data('DateTimePicker').minDate(false);
			}
				$(this).data("DateTimePicker").hide();
		});

		$("[name='search_invoice_to_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					var decrementDay = moment((e.date)).endOf('d');
					$("[name='search_invoice_from_date']").data('DateTimePicker').maxDate(decrementDay);
			}else{
				$("[name='search_invoice_from_date']").data('DateTimePicker').maxDate(false);
			}
					$(this).data("DateTimePicker").hide();
    });	
	    
	$("[name='search_collection_form_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_collection_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_collection_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_collection_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_collection_form_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_collection_form_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });

    $("[name='search_delivery_form_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_delivery_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_delivery_form_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_delivery_form_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });	

    $("[name='search_po_creation_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_po_creation_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_po_creation_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_po_creation_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_po_creation_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_po_creation_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });	
    
    $("[name='search_payment_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_payment_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_payment_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_payment_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_payment_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_payment_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });	
    
    $("[name='search_buyer_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_buyer_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_buyer_delivery_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_buyer_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_buyer_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_buyer_delivery_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });	
    $("[name='search_goods_in_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_goods_in_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_goods_in_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_goods_in_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_goods_in_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_goods_in_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });
});

 	var tracking_goods_in_url = '{{config("constants.TRACKING_GOODS_IN_MASTER_URL")}}' + '/';
 	function searchField(){
	 	var search_by_logistic_partner_name = $.trim($('[name="search_by_logistic_partner_name"]').val());
	 	var search_buyer_company = $.trim($('[name="search_buyer_company"]').val());
	 	var search_user_company = $.trim($('[name="search_user_company"]').val());
	 	var search_buyer_name = $.trim($('[name="search_buyer_name"]').val());
	 	var search_supplier_name = $.trim($('[name="search_supplier_name"]').val());
	 	var search_supplier_location = $.trim($('[name="search_supplier_location"]').val());
	 	//var search_payment_status = $.trim($('[name="search_payment_status"]').val());
	 	var search_order_from_date = $.trim($('[name="search_order_from_date"]').val());
	 	var search_order_to_date = $.trim($('[name="search_order_to_date"]').val());
	 	var search_invoice_from_date = $.trim($('[name="search_invoice_from_date"]').val());
		var search_invoice_to_date = $.trim($('[name="search_invoice_to_date"]').val());
	 	var search_collection_delivery = $.trim($('[name="search_collection_delivery"]').val());
	 	//var search_delivery_type = $.trim($('[name="search_delivery_type"]').val());
	 	var search_book_by = $.trim($('[name="search_book_by"]').val());
	 	var search_logistic_partner = $.trim($('[name="search_logistic_partner"]').val());
	 	var search_collection_form_date = $.trim($('[name="search_collection_form_date"]').val());
	 	var search_collection_to_date = $.trim($('[name="search_collection_to_date"]').val());
	 	var search_delivery_form_date = $.trim($('[name="search_delivery_form_date"]').val());
	 	var search_delivery_to_date = $.trim($('[name="search_delivery_to_date"]').val());
	 	var search_status = $.trim($('[name="search_status"]').val());
	 	var search_logistic_delivery_type = $.trim($('[name="search_logistic_delivery_type"]').val());
	 	var search_delivery_collection_location = $.trim($('[name="search_delivery_collection_location"]').val());
	 	//var search_delivery_location = $.trim($('[name="search_delivery_location"]').val());
	 	var search_supplier_country = $.trim($('[name="search_supplier_country"]').val());
	 	var search_ready_for_collection = $.trim($('[name="search_ready_for_collection"]').val());
		var search_tracking_status = $.trim($('[name="search_tracking_status"]').val());
	 	var search_po_creation_from_date = $.trim($('[name="search_po_creation_from_date"]').val());
	 	var search_po_creation_to_date = $.trim($('[name="search_po_creation_to_date"]').val());
		var search_customs_procedure = $.trim($('[name="search_customs_procedure"]').val());
		var search_dangerous_goods = $.trim($('[name="search_dangerous_goods"]').val());
		var search_user_buyer_name = $.trim($('[name="search_user_buyer_name"]').val());
		var search_payment_terms = $.trim($('[name="search_payment_terms"]').val());
		var search_payment_from_date = $.trim($('[name="search_payment_from_date"]').val());
		var search_payment_to_date = $.trim($('[name="search_payment_to_date"]').val());
		var search_actual_payment_from_date = $.trim($('[name="search_actual_payment_from_date"]').val());
		var search_actual_payment_to_date = $.trim($('[name="search_actual_payment_to_date"]').val());
		var search_buyer_delivery_from_date = $.trim($('[name="search_buyer_delivery_from_date"]').val());
		var search_buyer_delivery_to_date = $.trim($('[name="search_buyer_delivery_to_date"]').val());
		var search_goods_in_from_date = $.trim($('[name="search_goods_in_from_date"]').val());
	 	var search_goods_in_to_date = $.trim($('[name="search_goods_in_to_date"]').val());
		var search_pallets_boxes_type = $.trim($('[name="search_pallets_boxes_type"]').val());
		var search_goods_remark = $.trim($('[name="search_goods_remark[]"]').val());
		<?php /* ?>
		var search_currency_code = $.trim($('[name="search_currency_code"]').val());
		var search_no_of_pallets_boxes = $.trim($('[name="search_no_of_pallets_boxes"]').val());
		<?php */ ?>
	 	
	 	var searchData = {
	     	'search_by_logistic_partner_name':search_by_logistic_partner_name,
	         'search_buyer_company': search_buyer_company,
	         'search_user_company': search_user_company,
	         'search_buyer_name': search_buyer_name,
	         'search_supplier_name': search_supplier_name,
	         'search_supplier_location': search_supplier_location,
	         //'search_payment_status': search_payment_status,
	         'search_order_from_date': search_order_from_date,
	         'search_order_to_date': search_order_to_date,
	         'search_invoice_from_date':search_invoice_from_date,
	         'search_invoice_to_date':search_invoice_to_date,
	         'search_collection_delivery': search_collection_delivery,
	         //'search_delivery_type': search_delivery_type,
	         'search_book_by': search_book_by,
	         'search_logistic_partner': search_logistic_partner,
	         'search_collection_form_date': search_collection_form_date,
	         'search_collection_to_date': search_collection_to_date,
	         'search_delivery_form_date': search_delivery_form_date,
	         'search_delivery_to_date':search_delivery_to_date,
	         'search_status':search_status,
	         'search_logistic_delivery_type':search_logistic_delivery_type,
	         'search_delivery_collection_location':search_delivery_collection_location,
	        // 'search_delivery_location':search_delivery_location,
	         'search_supplier_country':search_supplier_country,
	         'search_ready_for_collection':search_ready_for_collection,
		    // 'search_tracking_status':search_tracking_status    
	         'search_po_creation_from_date': search_po_creation_from_date,
	         'search_po_creation_to_date': search_po_creation_to_date,
	         'search_customs_procedure':search_customs_procedure,
	         'search_dangerous_goods':search_dangerous_goods,
	         'search_user_buyer_name':search_user_buyer_name,
	         'search_payment_terms':search_payment_terms,
	         'search_payment_from_date':search_payment_from_date,
	         'search_payment_to_date':search_payment_to_date,
	         'search_actual_payment_from_date':search_actual_payment_from_date,
	         'search_actual_payment_to_date':search_actual_payment_to_date,
	         'search_buyer_delivery_from_date':search_buyer_delivery_from_date,
	         'search_buyer_delivery_to_date':search_buyer_delivery_to_date,
	         'search_goods_in_from_date': search_goods_in_from_date,
	         'search_goods_in_to_date': search_goods_in_to_date,
	         'search_pallets_boxes_type':search_pallets_boxes_type,
	         'search_goods_remark':search_goods_remark,
	         <?php /* ?>
	         'search_currency_code':search_currency_code,
	         'search_no_of_pallets_boxes':search_no_of_pallets_boxes,
	         <?php */ ?>
	        
	 	}
	     return searchData;
	 }
	 function filterData(){
	 	//var searchFieldName = searchField();
	 	//searchAjax(tracking_goods_in_url + 'filter' , searchFieldName);
		 if ($.fn.DataTable.isDataTable('#user-table')) {
	            $('#user-table').DataTable().destroy();
	        }

	        reintDataTable('user-table');
	 }
	 $(document).ready(function() {
	        reintDataTable('user-table');
	   })
	 var paginationUrl = tracking_goods_in_url + 'filter'
	 function exportData(type){
			var searchData = searchField();
			var export_info = {};
			export_info.url = tracking_goods_in_url + 'filter';
			export_info.searchData = searchData;
			export_info.searchData.custom_export_type_action = ((typeof type != 'undefined' & type != null && type != '') ? type : 'export');
			dataExportIntoExcel(export_info);
		}

		$("[name='search_collection_delivery']").on('change', function(){
			var selected_value = $.trim($("[name='search_collection_delivery']").val());
			if( selected_value != "" && selected_value != null && selected_value == "{{ config('constants.COLLECTION') }}"){
				//$(".book-by-filter").hide();
			} else {
				//$(".book-by-filter").show();
			}
		});

	  function reintDataTable(className = null) {

	        var paginationUrl = tracking_goods_in_url + "filter";

	        var searchData = searchField();

	        $('#' + className).DataTable({
	            "bProcessing": true,
	            "searching": false,
	            "bServerSide": true,
				"fixedHeader":{
                    "header": true,
                    "headerOffset": 40
                },
                "scrollX": true,
                "scrollY": 300,
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    $(".dataTables_scrollBody").addClass('no-record');
                    if (aiDisplay.length > 6) {
                        $(".dataTables_scrollBody").removeClass('no-record');
                    }
                    else {
                        $(".dataTables_scrollBody").addClass('no-record');
                    }
                },
	            "language": {
	                "searchPlaceholder": "<?php echo $tableSearchPlaceholder ?>"
	            },
	            "iDisplayLength": 25,
	            "order": [],
	            "order": [],
	            "ajax": {
	                url: paginationUrl, // json datasource
	                type: "post", // type of method  , by default would be get
	                data: searchData,
	                headers: {
	                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	                },
	                beforeSend: function() {
	        			showLoader();
	        	    },
	                dataFilter: function(response) {
		            	hideLoader();
		            	if( response != "" && response  != null ){
		            		var response_json_data = JSON.parse(response);
		            		var total_display_record = ( ( response_json_data.iTotalDisplayRecords != "" && response_json_data.iTotalDisplayRecords != null ) ? response_json_data.iTotalDisplayRecords : 0 );
		            		$(".total-record-count").html(total_display_record);
			            } else {
			            	$(".total-record-count").html(0);	
				        }
		            	return response;
		            },
	                error: function() { // error handling code

	                }
	            },
	            'columns': [{
	                    data: 'sr_no',
	                    orderable: false
	                },
	                {
	                    data: 'po_number'
	                },
	                {
	                    data: 'entry_no'
	                },
	                {
	                    data: 'logistic_entry_no'
	                },
	                {
	                    data: 'vendor_number'
	                },
	                {
	                    data: 'supplier_name'
	                },
	                {
		                data:'supplier_country'
	                },
	                {
		                data:'invoice_number'
	                },
	                {
		                data:'buyer_company'
	                },
	                {
		                data:'user_company'
	                },
	                {
		                data:'buyer_name'
	                },
	                {
		                data:'user_buyer_name'
	                },
	                {
		                data:'goods_remark'
	                },
	                {
		                data:'brand'
	                },
	                {
		                data:'customs_procedure'
	                },
	                {
		                data:'dangerous_goods'
	                },
	                {
		                data:'po_amount'
	                },
	                {
		                data:'po_amount_with_vat'
	                },
	                <?php /* ?>
	                {
		                data:'currency_code'
	                },
	                <?php */ ?>
	                {
		                data:'payment_terms'
	                },
	                {
		                data:'collection_delivery'
	                },
					{
		                data:'mode_of_transport'
	                },
	                {
		                data:'delivery_location'
		            },
		            {
			            data:'transporter_invoice_amount_gbp'
		            },
	                {
		                data:'document_date'
	                },
	                {
		                data:'order_date'
	                },
	                {
		                data:'invoice_date'
	                },
	                {
		                data:'payment_date'
	                },
	                {
		                data:'actual_payment_date'
	                },
	                {
		                data:'collection_date'
	                },
	                {
		                data:'buyer_delivery_date'
		            },
		            {
			            data:'logistic_delivery_date'
		            },
		            {
			            data:'goods_in_to_date'
		            },
	                {
		                data:'pallet_box'
	                },
	                {
		                data:'no_of_pallet_box'
		            },
		            {
		                data:'buyer_comments',
		                orderable: false
		            },
		            {
		                data:'warehouse_comments',
		                orderable: false
		            },
		            {
	                    data: 'action',
	                    orderable: false
	                },
	            ],
	        });
	    } 

	  function getSupplierLocationDetails(thisitem){
			var supplier_record_id = $.trim($('[name="search_supplier_name"]').val());
			//$('.supplier-location-list').trigger('change');
			if(supplier_record_id != "" && supplier_record_id != null){
				$.ajax({
			    	type:'post',
			    	data:{"_token": "{{ csrf_token() }}",'supplier_record_id':supplier_record_id},
			    	url: tracking_goods_in_url + 'getSupplierLocationDetails',
			    	beforeSend: function() {
			    		//block ui
			    		showLoader();
			    	},	
			    	success: function(response) {
			    		hideLoader();
			    		response = $.trim(response);
			    		if(response !="" && response != null){
			    			$('.supplier-location-list').html(response);
			    			
						} else {
							$('.supplier-location-list').html("");
						}
			    	},
			    	error: function() {
						hideLoader();
					}
			    });
			}
		}
	  var good_in_buyer_module_url = '{{config("constants.GOODS_IN_BUYER_MASTER_URL")}}' + '/';
	  function getSupplierNameDetails(thisitem){
			var supplier_country_id = $.trim($('[name="search_supplier_country"]').val());
			$('.supplier-name-list').trigger('change');
			if(supplier_country_id !="" && supplier_country_id != null){
				$.ajax({
			    	type:'post',
			    	data:{"_token": "{{ csrf_token() }}",'supplier_country_id':supplier_country_id},
			    	url: good_in_buyer_module_url + 'getSupplierDetails',
			    	beforeSend: function() {
			    		//block ui
			    		showLoader();
			    	},	
			    	success: function(response) {
			    		hideLoader();
			    		response = $.trim(response);
			    		if(response !="" && response != null){
			    			$('.supplier-name-list').html(response);
			    			$('.supplier-location-list').html("");
			    			
						} else {
							$('.supplier-name-list').html("");
							
						}
			    	},
			    	error: function() {
						hideLoader();
					}
			    });
				
			}
		} 
	  
		<?php /*?>
		function getSupplierCountry(thisitem){
			var supplier_location_id = $.trim($('[name="search_supplier_name"]').val());
			if(supplier_location_id != "" && supplier_location_id != null){
				$.ajax({
			    	type:'post',
			    	data:{"_token": "{{ csrf_token() }}",'supplier_location_id':supplier_location_id},
			    	url: tracking_goods_in_url + 'getSupplierCountry',
			    	beforeSend: function() {
			    		//block ui
			    		showLoader();
			    	},	
			    	success: function(response) {
			    		hideLoader();
			    		if(response !="" && response != null){
			    			$('.supplier-country-list').html(response);
			    			
						}
			    	},
			    	error: function() {
						hideLoader();
					}
			    });
			}
		} 
		*/?>

	function openDocumentModel(thisitem){
		var buyer_record_id = $.trim($(thisitem).attr('data-record-id'));
		var logistic_record_id = $.trim($(thisitem).attr('data-logistic-id'));
		var header_name = $.trim($(thisitem).attr('data-buyer-name'))
		
		if(buyer_record_id != "" && buyer_record_id != null){
			$.ajax({
	 			type : 'post',
	 			url :  tracking_goods_in_url + 'viewDocumentDetails',
	 			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	 			data : { buyer_record_id : buyer_record_id,logistic_record_id:logistic_record_id },
	 			beforeSend : function(){
	 				showLoader();
	 			},
	 			success : function(response){
	 				hideLoader();
	 				if( response != "" && response != null ){
	 					$("#view-document-modal").find('.view-file-modal-body').html(response);
	 					$("#view-document-modal").find('.twt-document-modal-header-name').html( '{{ trans("messages.view-documents")}}' + ' - ' + header_name );
	 					openBootstrapModal('view-document-modal');
	 	 			}
	 			}
	 		});
		}
		
	}
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	})
		
</script>

@endsection