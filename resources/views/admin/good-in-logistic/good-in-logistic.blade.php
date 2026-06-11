@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<?php if((checkPermission(config('permission_constants.ADD_GOODS_IN_LOGISTIC')) != false)){?>
					<a href="{{ config('constants.GOODS_IN_LOGITIC_MASTER_URL') . '/create' }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-logistic') }}"><i class=" fas fa-plus mr-md-2"></i> <span class="d-md-block d-none">{{ trans('messages.add-logistic') }}</span></a>
			<?php }?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center mr-2" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			@if((checkPermission(config('permission_constants.EXCEL_EXPORT_GOODS_IN_LOGISTIC')) != false))
            	<button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
           	@endif
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-4 col-md-12">
							<div class="form-group">
								<label for="search_by_logistic_partner_name" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_logistic_partner_name" id="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by') }} {{trans('messages.logistic')}} {{ trans('messages.entry-no') }}, {{ trans('messages.tracking-no') }}, {{ trans('messages.tracking-link') }}">
							</div>
						</div>
						<div class="col-xl-4 col-md-12">
							<div class="form-group">
								<label for="search_by_logistic_partner_name" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_buyer_no" id="search_by_buyer_no" placeholder="{{ trans('messages.search-by') }} {{trans('messages.buyer-entry-no')}}" value="{{ (isset($buyerEntryNo) && !empty($buyerEntryNo) ? $buyerEntryNo : '') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_country">{{ trans("messages.supplier-country") }}</label>
								<select name="search_supplier_country" class="form-control select2" onchange="getSupplierDetails(this);">
									<option value="">{{ trans("messages.select")}}</option>
									 @if(!empty($supplierCountryDetails))
                                     	@foreach ($supplierCountryDetails as $supplierCountryDetail)
                                        	@php $encodeSupplierCountryId  = Wild_tiger::encode($supplierCountryDetail->i_id); @endphp
                                        	<option value="{{ $encodeSupplierCountryId }}">{{ (!empty($supplierCountryDetail->v_country_name) ? $supplierCountryDetail->v_country_name : '') }}</option>
                                		@endforeach
                                	@endif
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_name">{{ trans("messages.supplier-name") }}</label>
								<select name="search_supplier_name" class="form-control select2 supplier-name-list" multiple onchange="filterData()">
									 <?php /*
                                        if(!empty($supplierRecordDetails)){
                                        	foreach ($supplierRecordDetails as $supplierRecordDetail){
                                        		$encodeSupplierId  = Wild_tiger::encode($supplierRecordDetail->i_id);
                                        		?>
                                        		<option value="{{ $encodeSupplierId }}"><?php echo (!empty($supplierRecordDetail->v_supplier_name) ? $supplierRecordDetail->v_supplier_name : '') ?></option>
                                        		<?php 
                                        	}
                                        }
                                        
                                       */ ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2  col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}</label>
								<select name="search_collection_delivery" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($collectionDeliveryInfo)){
                                        	foreach ($collectionDeliveryInfo as  $key => $collectionDelivery){
                                        		?>
                                        		<option value="{{ $key}}">{{ $collectionDelivery }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
								</select>
							</div>
						</div>
						
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_book_by" class="control-label">{{ trans("messages.book-by") }}</label>
								<select name="search_book_by" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    	if(!empty($userRecordDetails)){
                                        	foreach ($userRecordDetails as $userRecordDetail){
                                        		$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                        		?>
                                        		<option value="{{ $encodevUserId }}"><?php echo (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name . ( isset($userRecordDetail->v_department) ? ' ('.$userRecordDetail->v_department.')' : '' ) : '' ) ?></option>
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
                                    	if(!empty($logisticPartnerRecordDetails)){
                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                        		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                        		?>
                                        		<option value="{{ $encodeId }}"><?php echo  (!empty($logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerRecordDetail->v_logistic_partner_code) ?  ' ('. $logisticPartnerRecordDetail->v_logistic_partner_code : '' ) .')' : '' ) ?></option>
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
								<label class="control-label" for="search_delivery_form_date">{{ trans("messages.delivery-from-date") }}</label>
								<input type="text" name="search_delivery_form_date" class="form-control" placeholder="{{ trans('messages.delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_to_date">{{ trans("messages.delivery-to-date") }}</label>
								<input type="text" name="search_delivery_to_date" class="form-control" placeholder="{{ trans('messages.delivery-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_insurance_status" class="control-label">{{ trans("messages.insurance-status") }}</label>
								<select name="search_insurance_status" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($insuranceStatusDetails)){
                                        	foreach ($insuranceStatusDetails as  $key => $insuranceStatusDetail){
                                        		?>
                                        		<option value="{{ $key}}">{{ $insuranceStatusDetail }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
								</select>
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
						<div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
								<select name="search_status" class="form-control select2" multiple onchange="filterData()">
									<?php 
							        if(!empty($statusMasterRecordDetails)){
							        	foreach ($statusMasterRecordDetails as $statusMasterRecordDetail){
							           		$encoderId  = Wild_tiger::encode($statusMasterRecordDetail->i_id);
							           		$selected = '';
							           		if((isset($statusInfo)) && (!in_array($statusMasterRecordDetail->i_id, $statusInfo))){
							           			$selected ="selected='selected'";
							           		}
							           		?>
							             	<option value="{{ $encoderId }}" {{ $selected }}>{{ (!empty($statusMasterRecordDetail->v_status) ? $statusMasterRecordDetail->v_status : '' ) }}</option>
							              	<?php 
							           	}
							        } 
							        ?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-xl-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData();" >{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper ajax-view">
				@include( config('constants.AJAX_VIEW_FOLDER') . 'good-in-logistic/good-in-logistic-list')
				
			</div>

		</div>

	</section>
</main>
<script>
	$(document).ready(function() {
	 	$("[name='search_collection_form_date'],[name='search_collection_to_date'],[name='search_delivery_form_date'],[name='search_delivery_to_date'],[name='search_goods_in_from_date'],[name='search_goods_in_to_date']").datetimepicker({
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
		$("[name='search_collection_form_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
		 	$("[name='search_collection_to_date']").data('DateTimePicker').minDate(incrementDay);
		} else {
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

	 var good_in_logistic_module_url = '{{config("constants.GOODS_IN_LOGITIC_MASTER_URL")}}' + '/';
	 function searchField(){
	 	var search_by_logistic_partner_name = $.trim($('[name="search_by_logistic_partner_name"]').val());
	 	var search_by_buyer_no = $.trim($('[name="search_by_buyer_no"]').val());
	 	var search_supplier_name = $.trim($('[name="search_supplier_name"]').val());
	 	var search_collection_delivery = $.trim($('[name="search_collection_delivery"]').val());
	 	var search_book_by = $.trim($('[name="search_book_by"]').val());
	 	var search_logistic_partner = $.trim($('[name="search_logistic_partner"]').val());
	 	var search_collection_form_date = $.trim($('[name="search_collection_form_date"]').val());
	 	var search_collection_to_date = $.trim($('[name="search_collection_to_date"]').val());
	 	var search_delivery_form_date = $.trim($('[name="search_delivery_form_date"]').val());
	 	var search_delivery_to_date = $.trim($('[name="search_delivery_to_date"]').val());
	 	var search_insurance_status = $.trim($('[name="search_insurance_status"]').val());
	 	var search_goods_in_from_date = $.trim($('[name="search_goods_in_from_date"]').val());
	 	var search_goods_in_to_date = $.trim($('[name="search_goods_in_to_date"]').val());
	 	var search_status = $.trim($('[name="search_status"]').val());
	 	var search_supplier_country = $.trim($('[name="search_supplier_country"]').val());
	 	
	 	
	 	var searchData = {
	     	'search_by_logistic_partner_name':search_by_logistic_partner_name,
	     	'search_by_buyer_no':search_by_buyer_no,
	         'search_supplier_name': search_supplier_name,
	         'search_collection_delivery': search_collection_delivery,
	         'search_book_by': search_book_by,
	         'search_logistic_partner': search_logistic_partner,
	         'search_collection_form_date': search_collection_form_date,
	         'search_collection_to_date': search_collection_to_date,
	         'search_delivery_form_date': search_delivery_form_date,
	         'search_delivery_to_date': search_delivery_to_date,
	         'search_insurance_status': search_insurance_status,
	         'search_goods_in_from_date': search_goods_in_from_date,
	         'search_goods_in_to_date': search_goods_in_to_date,
	         'search_status': search_status,
	         'search_supplier_country': search_supplier_country,
	        
	 	}
	     return searchData;
	 }
	 function filterData(){
	 	var searchFieldName = searchField();
	 	searchAjax(good_in_logistic_module_url + 'filter' , searchFieldName);
	 }
	 var paginationUrl = good_in_logistic_module_url + 'filter'

	 function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = good_in_logistic_module_url + 'filter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}

</script>
<script type="text/javascript" src="{{ asset ('js/twt_fixed_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection