@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php if((checkPermission(config('permission_constants.ADD_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false)){?>
			<a href="{{ config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL') . '/create' }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-uk-other-country-us-port') }}"><i class=" fas fa-plus mr-md-1"></i> <span class="d-md-block d-none">{{ trans('messages.add') }}</span></a>
		<?php } ?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center mr-2" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			@if((checkPermission(config('permission_constants.EXCEL_EXPORT_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false))
            	<button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-md-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			$tableSearchPlaceholder = "Search By";
			?>

			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center dependent-field-div">
						<div class="col-xl-6 col-md-12">
							<div class="form-group">
								<label for="search_by_uk_other_country" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_uk_other_country" id="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.entry-no') }}, {{ trans('messages.tracking-no') }}, {{ trans('messages.seal-no-house-waybill-no') }}, {{ trans('messages.container-no-air-waybill-no') }}, {{ trans('messages.personal-ref') }}, {{ trans('messages.fba') }}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_way_of_transport">{{ trans("messages.way-of-transport") }}</label>
								<select name="search_way_of_transport" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($wayOfTransportDetails)){
                                        	foreach ($wayOfTransportDetails as  $key => $wayOfTransportDetail){
                                        		?>
                                        		<option value="{{ $key}}">{{ $wayOfTransportDetail }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_from_port_airport">{{ trans("messages.from-port-airport") }}</label>
								<select name="search_from_port_airport" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($fromPortInfo)){
                                        	foreach ($fromPortInfo as $fromPort){
                                        		$encodeFromPortId  = Wild_tiger::encode($fromPort->i_id);
                                        		$warehouseName = $fromPort->v_warehouse_name;
                                        		
                                        		?>
                                        		<option value="{{ $encodeFromPortId }}">{{ $warehouseName }}</option>
                                        		<?php 
                                        	}
                                        }
                                        
                                        ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_to_port_airport">{{ trans("messages.to-port-airport") }}</label>
								<select name="search_to_port_airport" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($toPortInfo)){
                                        	foreach ($toPortInfo as $toPort){
                                        		$encodeToPortId  = Wild_tiger::encode($toPort->i_id);
                                        		$warehouseName = $toPort->v_warehouse_name;
                                        		?>
                                        		<option value="{{ $encodeToPortId }}">{{ $warehouseName }}</option>
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
                                        		<option value="{{ $encodevUserId }}"><?php echo  (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name . (!empty($userRecordDetail->v_department) ?  ' ('. $userRecordDetail->v_department . ')' : '' ) : '' ) ?></option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_logistic_partner_uk">{{ trans("messages.logistic-partner-uk") }}</label>
								<select name="search_logistic_partner_uk" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($logisticPartnerDetails)){
                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
                                        		$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
                                        		
                                        		?>
                                        		<option value="{{ $encodeId }}" >{{ (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_etd_dispatch_from_date">{{ trans("messages.etd-dispatch-from-date") }}</label>
								<input type="text" name="search_etd_dispatch_from_date" class="form-control date-format" placeholder="{{ trans('messages.etd-dispatch-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_etd_dispatch_to_date">{{ trans("messages.etd-dispatch-to-date") }}</label>
								<input type="text" name="search_etd_dispatch_to_date" class="form-control date-format" placeholder="{{ trans('messages.etd-dispatch-to-date') }}">
							</div>
						</div>

						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_eta_arrival_from_date">{{ trans("messages.eta-arrival-from-date") }}</label>
								<input type="text" name="search_eta_arrival_from_date" class="form-control date-format" placeholder="{{ trans('messages.eta-arrival-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_eta_arrival_to_date">{{ trans("messages.eta-arrival-to-date") }}</label>
								<input type="text" name="search_eta_arrival_to_date" class="form-control date-format" placeholder="{{ trans('messages.eta-arrival-to-date') }}">
							</div>
						</div>

						<div class="col-lg-2 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_dangerous_goods" class="control-label">{{ trans("messages.dangerous-goods") }}</label>
								<select name="search_dangerous_goods" class="form-control" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($dangerousGoodsInfo)){
                                        	foreach ($dangerousGoodsInfo as  $key => $dangerousGoods){
                                        		
                                        		?>
                                        		<option value="{{ $key }}">{{ $dangerousGoods }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
								</select>
							</div>
						</div>

						<div class="col-lg-2 col-md-4 col-sm-6">
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
						<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
								<select class="form-control select2" multiple name="search_status" onchange="filterData()">
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
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_fba_sheet_status">{{ trans("messages.fba-sheet-status") }}</label>
								<select class="form-control" name="search_fba_sheet_status" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
								        if(!empty($fbaSheetStatusInfo)){
								        	foreach ($fbaSheetStatusInfo as $key => $fbaSheetStatus){
								           		?>
								             	<option value="{{ $key }}">{{ (!empty($fbaSheetStatus) ? $fbaSheetStatus : '' ) }}</option>
								              	<?php 
								           	}
								        } 
								        ?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_from_date">{{ trans("messages.delivery-from-date") }}</label>
								<input type="text" name="search_delivery_from_date" class="form-control" placeholder="{{ trans('messages.delivery-from-date') }}">
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
								<label class="control-label" for="search_process_status">{{ trans("messages.process-status") }}</label>
								<select class="form-control" name="search_process_status" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if(!empty($porcessStatusInfo))
								    	@foreach ($porcessStatusInfo as $key => $porcessStatus)
								        	<option value="{{ $key }}">{{ (!empty($porcessStatus) ? $porcessStatus : '' ) }}</option>
								    	@endforeach
								    @endif
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_from_warehouse_country">{{ trans("messages.from-warehouse-country") }}</label>
								<select class="form-control select2" name="search_from_warehouse_country" onchange="relatedWarehouseByWarehouseCountry(this, true)">
									<option value="">{{ trans("messages.select") }}</option>
									@if (!empty($countryDetails) && count($countryDetails) > 0)
										@foreach ($countryDetails as $countryDetail)
											<option value="{{ Wild_tiger::encode($countryDetail->i_id) }}">{{ (!empty($countryDetail->v_country_name) ? $countryDetail->v_country_name : '') }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_warehouse">{{ trans("messages.warehouse") }}</label>
								<select class="form-control warehouse-name-list select2" name="search_warehouse" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if (!empty($warehouseDetails) && count($warehouseDetails) > 0)
										@foreach ($warehouseDetails as $warehouseDetail)
											<option value="{{ Wild_tiger::encode($warehouseDetail->i_id) }}">{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name : '') }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_pick_up_from_date_from_warehouse">{{ trans("messages.pick-up-from-date-from-warehouse") }}</label>
								<input type="text" name="search_pick_up_from_date_from_warehouse" class="form-control" placeholder="{{ trans('messages.pick-up-from-date-from-warehouse') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_pick_up_to_date_from_warehouse">{{ trans("messages.pick-up-to-date-from-warehouse") }}</label>
								<input type="text" name="search_pick_up_to_date_from_warehouse" class="form-control" placeholder="{{ trans('messages.pick-up-to-date-from-warehouse') }}">
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end align-self-end gap">
							<div class="form-group">
								<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData();" >{{ trans("messages.search") }}</button>
								<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper ajax-view">
				@include( config('constants.AJAX_VIEW_FOLDER') . 'uk-other-country-us-port/good-out-country-port-list')
			</div>

		</div>

	</section>



	<div class="modal fade bd-example-modal-lg" id="upload-fba-file-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.upload-fba-sheet') }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
				</div>
				 {!! Form::open(array( 'id '=> 'upload-fba-sheet-form' , 'method' => 'post' , 'files' => true )) !!}
				<form method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group mb-0">
									<label for="upload_fba_file" class="control-label">{{trans('messages.upload-file')}} <span class="text-danger">*</span></label>
									<div class="custom-file">
										<input type="file" onchange="validFile(this,'excel');" class="custom-file-input" name="upload_fba_file">
										<label class="custom-file-label">{{ trans('messages.choose-file') }}</label>
									</div>
									<label id="upload_fba_file-error" class="invalid-input" for="upload_fba_file" style="display: none;"></label>
								</div>
							</div>
							<div class="col-lg-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <a href="{{ asset('public/sample-excel/fba-sheet/fba-sheet.xlsx') }}" download class="text-theme btn shadow-none p-0 text-decoration-underline" title="{{ trans('messages.download-sample-file') }}">
                                    <span class="text-theme ml-1">{{ trans('messages.download-sample-file') }} </span></a>
                                </div>
                            </div>
						</div>
					</div>
					<input type="hidden" name="country_to_port_goods_out_record_id" value="">
					<div class="modal-footer justify-content-center">
						<button type="button" onclick="uploadFBASheet(this);"  class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>

	<div class="modal fade bd-example-modal-lg" id="track-file-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">Error List <span> - FBA_1664255873_021022.xlsx - 14-10-2022</span></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-hover mb-0">
								<thead>
									<tr>
										<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
										<th class="text-left">{{ trans("messages.description") }}</th>
									</tr>
								</thead>
								<tbody class="track-file-modal-body">
									<tr>
										<td class="sr-col">1</td>
										<td class="text-left">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
									</tr>
									<tr>
										<td class="sr-col">2</td>
										<td class="text-left">It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</td>
									</tr>
									<tr>
										<td class="sr-col">3</td>
										<td class="text-left">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
									</tr>

								</tbody>
							</table>
						</div>
						<div class="notes-class pt-2">
							<p class="track-notes-title mb-0"><b style="color: red;">Note :</b> <span>Please Correct Above Errors and Upload Again.</span></p>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</main>

<script>
$(document).ready(function() {
 	$("[name='search_etd_dispatch_from_date'],[name='search_etd_dispatch_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
   	});
 	$("[name='search_eta_arrival_from_date'],[name='search_eta_arrival_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});

	$("[name='search_delivery_from_date'],[name='search_delivery_to_date']").datetimepicker({
		useCurrent: false,
		viewMode: 'days',
		ignoreReadonly: true,
		widgetPositioning: {
		vertical: 'bottom'
		},
		format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

	});
	$("[name='search_pick_up_from_date_from_warehouse'],[name='search_pick_up_to_date_from_warehouse']").datetimepicker({
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
	$("[name='search_etd_dispatch_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_etd_dispatch_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_etd_dispatch_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_etd_dispatch_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_etd_dispatch_from_date']").data('DateTimePicker').maxDate(decrementDay);
	}else{
        $("[name='search_etd_dispatch_from_date']").data('DateTimePicker').maxDate(false);
	}
        $(this).data("DateTimePicker").hide();
    });
    
    $("[name='search_eta_arrival_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_eta_arrival_to_date']").data('DateTimePicker').minDate(incrementDay);
	}else{
		$("[name='search_eta_arrival_to_date']").data('DateTimePicker').minDate(false);
	}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_eta_arrival_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_eta_arrival_from_date']").data('DateTimePicker').maxDate(decrementDay);
	}else{
        $("[name='search_eta_arrival_from_date']").data('DateTimePicker').maxDate(false);
	}
        $(this).data("DateTimePicker").hide();
    });	

    $("[name='search_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
    	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
		 	$("[name='search_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
    	} else {
    		$("[name='search_delivery_to_date']").data('DateTimePicker').minDate(false);
        }
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
    	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
	        var decrementDay = moment((e.date)).endOf('d');
	        $("[name='search_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
        } else {
        	$("[name='search_delivery_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });


		$("[name='search_pick_up_from_date_from_warehouse']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_pick_up_to_date_from_warehouse']").data('DateTimePicker').minDate(incrementDay);
			}else{
				$("[name='search_pick_up_to_date_from_warehouse']").data('DateTimePicker').minDate(false);
			}
				$(this).data("DateTimePicker").hide();
		});

		$("[name='search_pick_up_to_date_from_warehouse']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
				var decrementDay = moment((e.date)).endOf('d');
				$("[name='search_pick_up_from_date_from_warehouse']").data('DateTimePicker').maxDate(decrementDay);
			}else{
				$("[name='search_pick_up_from_date_from_warehouse']").data('DateTimePicker').maxDate(false);
			}
			$(this).data("DateTimePicker").hide();
		});
	
});
 

 var good_out_country_port_module_url = '{{config("constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL")}}' + '/';
 function searchField(){
 	var search_by_uk_other_country = $.trim($('[name="search_by_uk_other_country"]').val());
 	var search_way_of_transport = $.trim($('[name="search_way_of_transport"]').val());
 	var search_from_port_airport = $.trim($('[name="search_from_port_airport"]').val());
 	var search_to_port_airport = $.trim($('[name="search_to_port_airport"]').val());
 	var search_book_by = $.trim($('[name="search_book_by"]').val());
 	var search_logistic_partner_uk = $.trim($('[name="search_logistic_partner_uk"]').val());
 	var search_etd_dispatch_from_date = $.trim($('[name="search_etd_dispatch_from_date"]').val());
 	var search_etd_dispatch_to_date = $.trim($('[name="search_etd_dispatch_to_date"]').val());
 	var search_eta_arrival_from_date = $.trim($('[name="search_eta_arrival_from_date"]').val());
 	var search_eta_arrival_to_date = $.trim($('[name="search_eta_arrival_to_date"]').val());
 	var search_dangerous_goods = $.trim($('[name="search_dangerous_goods"]').val());
 	var search_insurance_status = $.trim($('[name="search_insurance_status"]').val());
 	var search_status = $.trim($('[name="search_status"]').val());
 	var search_fba_sheet_status = $.trim($('[name="search_fba_sheet_status"]').val());
 	var search_delivery_from_date = $.trim($('[name="search_delivery_from_date"]').val());
 	var search_delivery_to_date = $.trim($('[name="search_delivery_to_date"]').val());
 	var search_process_status = $.trim($('[name="search_process_status"]').val());
 	var search_from_warehouse_country = $.trim($('[name="search_from_warehouse_country"]').val());
 	var search_warehouse = $.trim($('[name="search_warehouse"]').val());
 	var search_pick_up_from_date_from_warehouse = $.trim($('[name="search_pick_up_from_date_from_warehouse"]').val());
 	var search_pick_up_to_date_from_warehouse = $.trim($('[name="search_pick_up_to_date_from_warehouse"]').val());
 	
 	var searchData = {
 		 'search_by_uk_other_country':search_by_uk_other_country,
     	 'search_way_of_transport':search_way_of_transport,
         'search_from_port_airport': search_from_port_airport,
         'search_to_port_airport': search_to_port_airport,
         'search_book_by': search_book_by,
         'search_logistic_partner_uk': search_logistic_partner_uk,
         'search_etd_dispatch_from_date': search_etd_dispatch_from_date,
         'search_etd_dispatch_to_date': search_etd_dispatch_to_date,
         'search_eta_arrival_from_date': search_eta_arrival_from_date,
         'search_eta_arrival_to_date': search_eta_arrival_to_date,
         'search_dangerous_goods': search_dangerous_goods,
         'search_insurance_status': search_insurance_status,
         'search_status': search_status,
         'search_fba_sheet_status':search_fba_sheet_status,
         'search_delivery_from_date': search_delivery_from_date,
	     'search_delivery_to_date': search_delivery_to_date,
	     'search_process_status': search_process_status,
	     'search_from_warehouse_country': search_from_warehouse_country,
	     'search_warehouse': search_warehouse,
	     'search_pick_up_from_date_from_warehouse': search_pick_up_from_date_from_warehouse,
	     'search_pick_up_to_date_from_warehouse': search_pick_up_to_date_from_warehouse,
	        
 	}
     return searchData;
 }
 function filterData(){
 	var searchFieldName = searchField();
 	searchAjax(good_out_country_port_module_url + 'filter' , searchFieldName);
 }
 var paginationUrl = good_out_country_port_module_url + 'filter'

 $("#upload-fba-sheet-form").validate({
	 errorClass: "invalid-input",
	rules: {
		upload_fba_file: {
			required : true,  
			extension : 'xlsx|xls'
		},
	},
	messages: {
		upload_fba_file: {
			required : "{{ trans('messages.required-upload-excel-file') }}",
			extension : "{{ trans('messages.only-allowed-file-types' , [ 'fileTypes' => 'Excel' ] )  }}"	
		},
	},
 });

 function uploadFBASheet(){
	if($("#upload-fba-sheet-form").valid() != true ){
		return false;
	}
	var row_index = $.trim($(current_record).parents('tr').find('.sr-index').html());
	var formData = new FormData( $('#upload-fba-sheet-form')[0] );
	formData.append('row_index' , row_index );
	$.ajax({
		type : 'post',
		url :  good_out_country_port_module_url + 'uploadFBASheet',
		dataType : 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		data : formData,
		processData: false,
		contentType: false,
		beforeSend : function(){
			showLoader();
		},
		success : function(response){
			hideLoader();
			if( response.status_code == 1 ){
				alertifyMessage('success' , response.message);
				$("#upload-fba-file-modal").modal('hide');
				$("#upload-fba-sheet-form").validate().resetForm();
				var html = ( ( response.data.html != "" && response.data.html != null ) ? response.data.html : "" );
				if( html != "" && html != null ){
					$(current_record).parents('tr').html(html);
				}
			} else if( response.status_code == 101 ){
				alertifyMessage('error' , response.message  );
				
			}
		
		}
	});
}
 var current_record = '';
 function openUploadSheetModal(thisitem){
	 
	 var record_id = $.trim($(thisitem).attr('data-record-id'));
	 if( record_id != "" && record_id != null ){
		 current_record = thisitem;
		$("[name='country_to_port_goods_out_record_id']").val(record_id);
		openBootstrapModal("upload-fba-file-modal");	
     }
 }
 
 
 	function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = good_out_country_port_module_url + 'filter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}

 	function showRemarkModal(thisitem){
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var header_name = $.trim($(thisitem).attr('data-recoprd-date'));
		var header_title_name = $.trim($(thisitem).attr('data-record-name'));
		
		 $("#track-file-modal").find('.twt-modal-header-name').html( "Error List - " + header_title_name + ' - '+ header_name);
	  
		if( record_id != "" && record_id != null ){
			$.ajax({
	 			type : 'post',
	 			url :  good_out_country_port_module_url + 'get-failed-sheet-info',
	 			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	 			data : { record_id : record_id },
	 			beforeSend : function(){
	 				showLoader();
	 			},
	 			success : function(response){
	 				hideLoader();
	 				if( response != "" && response != null ){
	 					$("#track-file-modal").find('.track-file-modal-body').html(response);
	 			 		openBootstrapModal("track-file-modal");
	 	 			}
	 			}
	 		});
		}
	}
function showFbaSheetRecordDetails(thisitem){
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	if( record_id != "" && record_id != null ){
		$.ajax({
 			type : 'post',
 			url :  good_out_country_port_module_url + 'view-fba-sheet',
 			data: {"_token": "{{ csrf_token() }}",record_id: record_id},
 			beforeSend : function(){
 				//showLoader();
 			},
 			success : function(response){
 				hideLoader();
 			}
 		});
	}
}	


</script>
<script type="text/javascript" src="{{ asset ('js/twt_fixed_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection