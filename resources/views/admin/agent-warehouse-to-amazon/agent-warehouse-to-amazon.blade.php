@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php if((checkPermission(config('permission_constants.ADD_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false)){?>
			<a href="{{ config('constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL') . '/create' }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-agent-warehouse-to-amazon') }}"><i class=" fas fa-plus mr-md-1"></i> <span class="d-md-block d-none">{{ trans('messages.add') }}</span></a>
		<?php }?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center mr-2" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			@if((checkPermission(config('permission_constants.EXCEL_EXPORT_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false))
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
					<div class="row align-items-center warehouse-amazon-list">
						<div class="col-xl-6 col-lg-6 col-md-12">
							<div class="form-group">
								<label for="search_by_agent_warehouse" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_agent_warehouse" id="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.entry-no') }}, {{ trans('messages.tracking-no') }}, {{ trans('messages.tracking-link') }}, {{ trans('messages.personal-ref') }}, {{ trans('messages.fba') }}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_way_of_transport">{{ trans("messages.way-of-transport") }}</label>
								<select name="search_way_of_transport" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if(!empty($wayOfTransportDetails)){
                                       		foreach ($wayOfTransportDetails as $key => $wayOfTransportDetail) {
                                       			?>
                                       			<option value="{{ $key  }} ">{{ $wayOfTransportDetail }}</option>
                                       			<?php 
                                       		}
                                       	}
                                       	?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="logistic_partner_name">{{ trans("messages.from") }}</label>
								<select name="logistic_partner_name" class="form-control select2" data-record-id="filter" onchange="getPortToagentContainerDetails(this),filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
 										if(!empty($logisticPartnerRecordDetails)){
                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                        		$encodeLogisticPartnerDetailId = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                        		
                                        		?>
                                        		<option value="{{ $encodeLogisticPartnerDetailId }}"><?php echo  (!empty($logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerRecordDetail->v_logistic_partner_code) ?  ' - '. $logisticPartnerRecordDetail->v_logistic_partner_code : '' ) : '' ) ?></option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_to">{{ trans("messages.to") }}</label>
								<select name="search_to" class="form-control"  onchange="hideShowWarehouseField(this);">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if(!empty($wayToWarehouseDetails)){
                                       		foreach ($wayToWarehouseDetails as $key => $wayToWarehouseDetail) {
                                       			?>
                                       			<option value="{{ $key  }} ">{{ $wayToWarehouseDetail }}</option>
                                       			<?php 
                                       		}
                                       	}
                                       	?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6 to-warehouse-details-hide-show-div" style="display: none">
							<div class="form-group">
								<label class="control-label" for="search_to_warehouse">{{ trans("messages.warehouse") }}</label>
								<select name="search_to_warehouse" class="form-control select2" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									@if (!empty($toWarehouseDetails))
										@foreach ($toWarehouseDetails as $toWarehouseDetail)
											<option value="{{ Wild_tiger::encode($toWarehouseDetail->i_id) }}">{{ (!empty($toWarehouseDetail->v_warehouse_name) ? $toWarehouseDetail->v_warehouse_name : '') }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_select_containers" class="control-label">{{ trans("messages.select-containers") }}</label>
								<select name="search_select_containers[]" class="form-control select2 container-list" multiple  onchange="filterData();">
									<?php 
									if(!empty($getCountryToPortGoodsOutRecordDetails)){
										foreach ($getCountryToPortGoodsOutRecordDetails  as $getCountryToPortGoodsOutRecordDetail){
											$encodeRecordId  = Wild_tiger::encode($getCountryToPortGoodsOutRecordDetail->country_port_master_id);
											
											?>
											<option value="{{ $encodeRecordId }}">{{ (!empty($getCountryToPortGoodsOutRecordDetail->v_country_to_port_record_no) ? $getCountryToPortGoodsOutRecordDetail->v_country_to_port_record_no .(!empty($getCountryToPortGoodsOutRecordDetail->e_transport_way) ? ' ('.$getCountryToPortGoodsOutRecordDetail->e_transport_way .')' :''):'') }}</option>
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
								<select name="search_book_by" class="form-control select2"  onchange="filterData();">
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
								<label class="control-label" for="search_logistic_partner">{{ trans("messages.logistic-partner") }}</label>
								<select name="search_logistic_partner" class="form-control select2"  onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
 										if(!empty($logisticPartnerRecordDetails)){
                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                        		$encodeLogisticPartnerDetailId = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                        		
                                        		?>
                                        		<option value="{{ $encodeLogisticPartnerDetailId }}"><?php echo  (!empty($logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerRecordDetail->v_logistic_partner_code) ?  ' - '. $logisticPartnerRecordDetail->v_logistic_partner_code : '' ) : '' ) ?></option>
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
								<input type="text" name="search_booking_from_date" class="form-control" placeholder="{{ trans('messages.booking-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_booking_to_date">{{ trans("messages.booking-to-date") }}</label>
								<input type="text" name="search_booking_to_date" class="form-control" placeholder="{{ trans('messages.booking-to-date') }}">
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
						<div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
								<select class="form-control select2" multiple name="search_status"  onchange="filterData();">
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
				@include( config('constants.AJAX_VIEW_FOLDER') . 'agent-warehouse-to-amazon/agent-warehouse-to-amazon-list')
			</div>

		</div>

	</section>



	<div class="modal fade bd-example-modal-lg" id="upload-file" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">Upload FBA Sheet</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
				</div>
				<form method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group mb-0">
									<label for="upload_file" class="control-label">Upload File <span class="text-danger">*</span></label>
									<div class="custom-file">
										<input type="file" class="custom-file-input" id="upload_file" name="upload_file">
										<label class="custom-file-label" for="upload_file" name="upload_file">Choose file...</label>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="submit" class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="modal fade bd-example-modal-lg" id="track-file-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">FBA <span>- f_ampp_1664255873_021022.xlsx - 27-09-2022</span></h5>
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
								<tbody>
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
	$("form").validate({
		errorClass: "invalid-input",
		rules: {
			upload_file: {
				required: true
			}

		},
		messages: {
			upload_file: {
				required: "{{ trans('messages.require-upload-file') }}"
			}
		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
</script>

<script>
	$(document).ready(function() {

		//init date time picker
		$("[name='search_booking_from_date'],[name='search_booking_to_date']").datetimepicker({
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
		
	});
	 $(function(){
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
		 
		$("[name='search_collection_form_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
		 	$("[name='search_collection_to_date']").data('DateTimePicker').minDate(incrementDay);
			}else {
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
			} else {
				$("[name='search_delivery_to_date']").data('DateTimePicker').minDate(false);	
			}

			
		    $(this).data("DateTimePicker").hide();
		});

	    $("[name='search_delivery_to_date']").datetimepicker().on('dp.change', function(e) {

			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
				var decrementDay = moment((e.date)).endOf('d');
	        	$("[name='search_delivery_form_date']").data('DateTimePicker').maxDate(decrementDay);	
			} else {
				$("[name='search_delivery_form_date']").data('DateTimePicker').maxDate(false);	
			}

	        
	        $(this).data("DateTimePicker").hide();
	    });	
	});
	 var agent_warehouse_module_url = '{{config("constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL")}}' + '/';

	 function searchField(){
	 	var search_by_agent_warehouse = $.trim($('[name="search_by_agent_warehouse"]').val());
	 	var search_way_of_transport = $.trim($('[name="search_way_of_transport"]').val());
	 	var logistic_partner_name = $.trim($('[name="logistic_partner_name"]').val());
	 	var search_to = $.trim($('[name="search_to"]').val());
	 	var search_to_warehouse = $.trim($('[name="search_to_warehouse"]').val());
	 	var search_select_containers = $.trim($('[name="search_select_containers[]"]').val());
	 	var search_book_by = $.trim($('[name="search_book_by"]').val());
	 	var search_logistic_partner = $.trim($('[name="search_logistic_partner"]').val());
	 	var search_booking_from_date = $.trim($('[name="search_booking_from_date"]').val());
	 	var search_booking_to_date = $.trim($('[name="search_booking_to_date"]').val());
	 	var search_collection_form_date = $.trim($('[name="search_collection_form_date"]').val());
	 	var search_collection_to_date = $.trim($('[name="search_collection_to_date"]').val());
	 	var search_delivery_form_date = $.trim($('[name="search_delivery_form_date"]').val());
	 	var search_delivery_to_date = $.trim($('[name="search_delivery_to_date"]').val());
	 	var search_status = $.trim($('[name="search_status"]').val());
	 	
	 	var searchData = {
	     	'search_by_agent_warehouse':search_by_agent_warehouse,
	         'search_way_of_transport': search_way_of_transport,
	         'logistic_partner_name': logistic_partner_name,
	         'search_to': search_to,
	         'search_to_warehouse': search_to_warehouse,
	         'search_select_containers': search_select_containers,
	         'search_book_by': search_book_by,
	         'search_logistic_partner':search_logistic_partner,
	         'search_booking_from_date': search_booking_from_date,
	         'search_booking_to_date': search_booking_to_date,
	         'search_collection_form_date': search_collection_form_date,
	         'search_collection_to_date': search_collection_to_date,
	         'search_delivery_form_date': search_delivery_form_date,
	         'search_delivery_to_date': search_delivery_to_date,
	         'search_status': search_status,
	        
	 	}
	     return searchData;
	 }
	 function filterData(){
	 	var searchFieldName = searchField();
	 	searchAjax(agent_warehouse_module_url + 'filter' , searchFieldName);
	 }
	 var paginationUrl = agent_warehouse_module_url + 'filter'
 	function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = agent_warehouse_module_url + 'filter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}
	 function hideShowWarehouseField(thisitem){
		var to_warehouse = $.trim($(thisitem).val());

		if(to_warehouse != '' && to_warehouse != null && to_warehouse == '{{ config("constants.WAREHOUSE_FBA_SHEET") }}'){
			$('[name="search_to_warehouse"]').val('');
			$('[name="search_to_warehouse"]').trigger('change');
			$('.to-warehouse-details-hide-show-div').show();
		} else {
			$('.to-warehouse-details-hide-show-div').hide();
		}
		filterData();
	}
</script>
<script type="text/javascript" src="{{ asset ('js/twt_fixed_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection