	@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<?php if ((checkPermission(config('permission_constants.ADD_USA_CONTAINER_CLUBBING')) != false)) { ?>
			<a href="{{ config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') }}/create" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-usa-container-clubbing') }}"><i class=" fas fa-plus mr-md-1"></i> <span class="d-md-block d-none">{{ trans('messages.add') }}</span></a>
			<?php } ?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			$tableSearchPlaceholder = "Search By Entry No., FBA, Tracking No.,Pro Number, Logistic Cost (USD)";
			?>

			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-4 col-lg-6 col-md-12">
							<div class="form-group">
								<label for="search_by" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by" id="search_by_logistic_partner_name" placeholder="<?php echo $tableSearchPlaceholder ?>">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_from_warehouse">{{ trans("messages.from-warehouse") }}</label>
								<select class="form-control select2" name="search_from_warehouse" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if( isset( $fromWarehouseDetails ) && !empty( $fromWarehouseDetails ) ){
                                       		foreach ($fromWarehouseDetails as $fromWarehouseDetail) {
                                       			$encoderId  = Wild_tiger::encode($fromWarehouseDetail->i_id);
                                       			?>
                                       			<option value="{{ $encoderId  }} ">{{ (isset($fromWarehouseDetail->v_warehouse_name) && !empty($fromWarehouseDetail->v_warehouse_name) ? $fromWarehouseDetail->v_warehouse_name . (!empty($fromWarehouseDetail->v_warehouse_code) ? ' (' . $fromWarehouseDetail->v_warehouse_code . ')' : '' ) : '') }}</option>
                                       			<?php 
                                       		}
                                       	}
                                     ?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_to_location">{{ trans("messages.to-location") }}</label>
								<select class="form-control select2" name="search_to_location" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if( isset( $locationMasterCodeDetails ) && !empty( $locationMasterCodeDetails ) ){
                                       		foreach ($locationMasterCodeDetails as $locationMasterCodeDetail) {
                                       			$encoderId  = Wild_tiger::encode($locationMasterCodeDetail->i_id);
                                       			?>
                                       			<option value="{{ $encoderId  }} ">{{ (isset($locationMasterCodeDetail->v_warehouse_name) && !empty($locationMasterCodeDetail->v_warehouse_name) ? $locationMasterCodeDetail->v_warehouse_name . (!empty($locationMasterCodeDetail->v_warehouse_code) ? ' (' . $locationMasterCodeDetail->v_warehouse_code . ')' : '' ) : '') }}</option>
                                       			<?php 
                                       		}
                                       	}
                                     ?>
								</select>
							</div>
						</div>				
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_booking_from_date">{{ trans("messages.booking-from-date") }}</label>
								<input type="text" name="search_booking_from_date" class="form-control date-format" placeholder="{{ trans('messages.booking-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_booking_to_date">{{ trans("messages.booking-to-date") }}</label>
								<input type="text" name="search_booking_to_date" class="form-control date-format" placeholder="{{ trans('messages.booking-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_booking_portal">{{ trans("messages.booking-portal") }}</label>
								<select class="form-control select2" name="search_booking_portal" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if( isset( $bookingPortalDetails ) && !empty( $bookingPortalDetails ) ){
                                       		foreach ($bookingPortalDetails as $bookingPortalDetail) {
                                       			$encoderId  = Wild_tiger::encode($bookingPortalDetail->i_id);
                                       			?>
                                       			<option value="{{ $encoderId  }} ">{{ (isset($bookingPortalDetail->v_value) && !empty($bookingPortalDetail->v_value) ? $bookingPortalDetail->v_value : '') }}</option>
                                       			<?php 
                                       		}
                                       	}
                                     ?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_carrier_company">{{ trans("messages.carrier-company") }}</label>
								<select class="form-control select2" name="search_carrier_company" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if( isset( $logisticPartnerDetails ) && !empty( $logisticPartnerDetails ) ){
                                       		foreach ($logisticPartnerDetails as $logisticPartnerDetail) {
                                       			$encoderId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
                                       			?>
                                       			<option value="{{ $encoderId  }} ">{{ (isset($logisticPartnerDetail->v_logistic_partner_name) && !empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '') }}</option>
                                       			<?php 
                                       		}
                                       	}
                                     ?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_collection_from_date">{{ trans("messages.collection-from-date") }}</label>
								<input type="text" name="search_collection_from_date" class="form-control date-format" placeholder="{{ trans('messages.collection-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_collection_to_date">{{ trans("messages.collection-to-date") }}</label>
								<input type="text" name="search_collection_to_date" class="form-control date-format" placeholder="{{ trans('messages.collection-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_from_date">{{ trans("messages.delivery-from-date") }}</label>
								<input type="text" name="search_delivery_from_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_to_date">{{ trans("messages.delivery-to-date") }}</label>
								<input type="text" name="search_delivery_to_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control select2" name="search_status" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
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
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData();">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper ajax-view">
				@include( config('constants.AJAX_VIEW_FOLDER') . 'usa-container-clubbing/usa-container-clubbing-list')
			</div>
		</div>
	</section>
</main>

<script>
	var module_url = "{{ config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') }}" + '/';

	$(document).ready(function() {
        $('[name="search_booking_from_date"], [name="search_booking_to_date"], [name="search_collection_from_date"], [name="search_collection_to_date"], [name="search_delivery_from_date"], [name="search_delivery_to_date"]').datetimepicker({
        useCurrent: false,
        viewMode: 'days',
        ignoreReadonly: true,
        format: 'DD-MM-YYYY',
				widgetPositioning: {
						vertical: 'bottom',
						horizontal: 'auto',
					},
        });
        
        $("[name='search_booking_from_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            	var incrementDay = moment((e.date)).startOf('d');
            $("[name='search_booking_to_date']").data('DateTimePicker').minDate(incrementDay);
        }else{
            $("[name='search_booking_to_date']").data('DateTimePicker').minDate(false);
        }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_booking_to_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            	var decrementDay = moment((e.date)).endOf('d');
            $("[name='search_booking_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
            $("[name='search_booking_from_date']").data('DateTimePicker').maxDate(false);
        }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_collection_from_date']").datetimepicker().on('dp.change', function(e) {
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
            $("[name='search_collection_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
            $("[name='search_collection_from_date']").data('DateTimePicker').maxDate(false);
        }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
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
            $("[name='search_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
            $("[name='search_delivery_from_date']").data('DateTimePicker').maxDate(false);
        }
            $(this).data("DateTimePicker").hide();
        });
	});

	function searchField(){
	 	var search_by = $.trim($('[name="search_by"]').val());
	 	var search_from_warehouse = $.trim($('[name="search_from_warehouse"]').val());
	 	var search_to_location = $.trim($('[name="search_to_location"]').val());
	 	var search_booking_from_date = $.trim($('[name="search_booking_from_date"]').val());
	 	var search_booking_to_date = $.trim($('[name="search_booking_to_date"]').val());
	 	var search_booking_portal = $.trim($('[name="search_booking_portal"]').val());
	 	var search_carrier_company = $.trim($('[name="search_carrier_company"]').val());
	 	var search_collection_from_date = $.trim($('[name="search_collection_from_date"]').val());
	 	var search_collection_to_date = $.trim($('[name="search_collection_to_date"]').val());
	 	var search_delivery_from_date = $.trim($('[name="search_delivery_from_date"]').val());
	 	var search_delivery_to_date = $.trim($('[name="search_delivery_to_date"]').val());
	 	var search_status = $.trim($('[name="search_status"]').val());
	 	
	 	
	 	var searchData = {
	     	 'search_by':search_by,
	     	 'search_from_warehouse':search_from_warehouse,
	     	 'search_to_location':search_to_location,
	         'search_booking_from_date': search_booking_from_date,
	         'search_booking_to_date': search_booking_to_date,
	         'search_booking_portal': search_booking_portal,
	         'search_carrier_company': search_carrier_company,
	         'search_collection_from_date': search_collection_from_date,
	         'search_collection_to_date': search_collection_to_date,
	         'search_delivery_from_date':search_delivery_from_date,
	         'search_delivery_to_date': search_delivery_to_date,
	         'search_status': search_status,
	        
	 	}
	     return searchData;
	 }
	 function filterData(){
	 	var searchFieldName = searchField();
	 	searchAjax(module_url + 'filter' , searchFieldName);
	 }
	 
	var paginationUrl = module_url + 'filter';
	
 	function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = module_url + 'filter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}
</script>
<script type="text/javascript" src="{{ asset ('js/twt_fixed_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection