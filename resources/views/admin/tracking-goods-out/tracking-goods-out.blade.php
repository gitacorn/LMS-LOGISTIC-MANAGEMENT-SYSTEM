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
			@if((checkPermission(config('permission_constants.EXCEL_EXPORT_TRACKING_GOODS_OUT_REPORT')) != false))
            	<button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
            	<?php /* ?><button type="button" title="{{ trans('messages.export-summary') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData('{{ config('constants.ACTION_SUMMARY_EXPORT') }}');"><i class="fas fa-file-excel mr-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-summary") }}</span></button><?php */ ?>
            @endif
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			$tableSearchPlaceholder = "Search By Entry No., PO No. / Sales Invoice No., Logistic Entry No., Tracking No., Tracking Link";
			?>

			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-4 col-md-4">
							<div class="form-group">
								<label for="search_by_good_out_report" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_good_out_report" id="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.entry-no') }}, {{ trans('messages.tracking-no') }}, {{ trans('messages.workflow-id') }}, {{ trans('messages.shipment-id') }}">
							</div>
						</div>
						<div class="col-lg-4 col-md-6 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_way_of_transport">{{ trans("messages.way-of-transport") }}</label>
								<select name="search_way_of_transport" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                       	if(!empty($wayOfTransportDetails)){
                                       		foreach ($wayOfTransportDetails as $key => $wayOfTransportDetail) {
                                       			?>
                                       			<option value="{{ $key  }}">{{ $wayOfTransportDetail }}</option>
                                       			<?php 
                                       		}
                                       	}
                                    ?>
								</select>
							</div>
						</div>
				

						<div class="col-lg-4 col-md-4">
							<div class="form-group">
								<label for="search_book_by" class="control-label">{{ trans("messages.book-by") }}</label>
								<select name="search_book_by" class="form-control select2" onchange="filterData();">
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
						<div class="col-xl-4 col-lg-3">
							<div class="form-group">
								<label class="control-label" for="search_logistic_partner">{{ trans("messages.logistic-partner") }}</label>
								<select name="search_logistic_partner" class="form-control select2" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
									if(!empty($logisticPartnerDetails)){
										foreach ($logisticPartnerDetails as $logisticPartnerDetail){
											$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
											?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name : '' ) .' ('.$logisticPartnerDetail->v_logistic_partner_code.')' }}</option>
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
								<input type="text" name="search_booking_to_date" class="form-control date-format" placeholder="{{ trans('messages.booking-to-date') }}">
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
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_amazon_appointment_from_date">{{ trans("messages.amazon-appointment-from-date") }}</label>
								<input type="text" name="search_amazon_appointment_from_date" class="form-control date-format" placeholder="{{ trans('messages.amazon-appointment-from-date') }}">
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_amazon_appointment_to_date">{{ trans("messages.amazon-appointment-to-date") }}</label>
								<input type="text" name="search_amazon_appointment_to_date" class="form-control date-format" placeholder="{{ trans('messages.amazon-appointment-to-date') }}">
							</div>
						</div>
						
						<div class="col-xl-4 col-lg-4">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
								<select class="form-control select2" multiple name="search_status" onchange="filterData();">
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
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData()">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body card-pagination-items-class">
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover" id="report-good-out-table">
							<thead>
								<tr>
									<th class="sr-col text-center">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.entry-number") }}</th>
									<th class="text-left">{{ trans("messages.workflow-id") }}</th>
									<th class="text-left">{{ trans("messages.fba-id") }}</th>
									<th class="text-left">{{ trans("messages.account-name") }}</th>
									<th class="text-left">{{ trans("messages.sku") }}</th>
									<th class="text-left">{{ trans("messages.unit") }}</th>
									<th class="text-left">{{ trans("messages.shipment-value") }}</th>
									<th class="text-left">{{ trans("messages.box-pallet") }}</th>
									<th class="text-left">{{ trans("messages.no-of-box-pallet") }}</th>
									<th class="text-left">{{ trans("messages.from-warehouse") }}</th>
									<th class="text-left">{{ trans("messages.to-warehouse") }}</th>
									<th class="text-left">{{ trans("messages.to-country") }}</th>
									<th class="text-left">{{ trans("messages.way-of-transport") }}</th>
									<th class="text-left">{{ trans("messages.book-by") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
									<th class="text-left">{{ trans("messages.booking-date") }}</th>
									<th class="text-left">{{ trans("messages.collection-date") }}</th>
									<th class="text-left">{{ trans("messages.delivery-date") }}</th>
									<th class="text-left">{{ trans("messages.tracking-number") }}</th>
									<th class="text-left">{{ trans("messages.transporter-invoice-cost-gbp") }}</th>
									<?php /* ?><th class="text-center">{{ trans("messages.actions") }}</th><?php */ ?>
								</tr>
							</thead>
							<tbody class="ajax-view">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<script>

	$(document).ready(function() {
		// Parse URL parameters for cross-dashboard filtering
		const params = new URLSearchParams(window.location.search);
		const scfd = params.get('search_collection_from_date');
		const sctd = params.get('search_collection_to_date');
		const ss = params.get('search_status');
		
		if (scfd) $('[name="search_collection_from_date"]').val(scfd);
		if (sctd) $('[name="search_collection_to_date"]').val(sctd);
		if (ss) {
			$('[name="search_status"]').val([ss]);
			if ($('[name="search_status"]').hasClass('select2')) {
				$('[name="search_status"]').trigger('change');
			}
		}
	
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
		
	
		$("[name='search_collection_from_date'],[name='search_collection_to_date']").datetimepicker({
			useCurrent: false,
			viewMode: 'days',
			ignoreReadonly: true,
			widgetPositioning: {
				vertical: 'bottom'
			},
			format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
	
		});
	
		$("[name='search_amazon_appointment_from_date'],[name='search_amazon_appointment_to_date']").datetimepicker({
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
	
	    $("[name='search_amazon_appointment_from_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
		 	$("[name='search_amazon_appointment_to_date']").data('DateTimePicker').minDate(incrementDay);
			}else{
				$("[name='search_amazon_appointment_to_date']").data('DateTimePicker').minDate(false);
			}
		    $(this).data("DateTimePicker").hide();
		});
	
	    $("[name='search_amazon_appointment_to_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
	        var decrementDay = moment((e.date)).endOf('d');
	        $("[name='search_amazon_appointment_from_date']").data('DateTimePicker').maxDate(decrementDay);
			}else{
				$("[name='search_amazon_appointment_from_date']").data('DateTimePicker').maxDate(false);
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
	});
 	var tracking_goods_out_url = '{{config("constants.TRACKING_GOODS_OUT_MASTER_URL")}}' + '/';
 	function searchField(){
		var search_by_good_out_report = $.trim($('[name="search_by_good_out_report"]').val());
		var search_way_of_transport = $.trim($('[name="search_way_of_transport"]').val());
	 	var search_book_by = $.trim($('[name="search_book_by"]').val());
	 	var search_logistic_partner = $.trim($('[name="search_logistic_partner"]').val());
	 	var search_booking_from_date = $.trim($('[name="search_booking_from_date"]').val());
	 	var search_booking_to_date = $.trim($('[name="search_booking_to_date"]').val());
	 	var search_collection_from_date = $.trim($('[name="search_collection_from_date"]').val());
	 	var search_collection_to_date = $.trim($('[name="search_collection_to_date"]').val());
	 	var search_amazon_appointment_from_date = $.trim($('[name="search_amazon_appointment_from_date"]').val());
	 	var search_amazon_appointment_to_date = $.trim($('[name="search_amazon_appointment_to_date"]').val());
	 	var search_status = $.trim($('[name="search_status"]').val());
	 	var search_delivery_from_date = $.trim($('[name="search_delivery_from_date"]').val());
	 	var search_delivery_to_date = $.trim($('[name="search_delivery_to_date"]').val());
	 	
	 	var searchData = {
			'search_by_good_out_report':search_by_good_out_report,
			'search_way_of_transport': search_way_of_transport,
	        'search_book_by': search_book_by,
	        'search_logistic_partner':search_logistic_partner,
	        'search_booking_from_date': search_booking_from_date,
	        'search_booking_to_date': search_booking_to_date,
	        'search_collection_from_date': search_collection_from_date,
	        'search_collection_to_date': search_collection_to_date,
	        'search_amazon_appointment_from_date': search_amazon_appointment_from_date,
	        'search_amazon_appointment_to_date': search_amazon_appointment_to_date,
	        'search_status': search_status,
	        'search_delivery_from_date': search_delivery_from_date,
		    'search_delivery_to_date': search_delivery_to_date,        
	 	}
		return searchData;
	}
	 
	function filterData(){
		if ($.fn.DataTable.isDataTable('#report-good-out-table')) {
			$('#report-good-out-table').DataTable().destroy();
		}
		reintDataTable('report-good-out-table');
	}
	 
	$(document).ready(function() {
		 reintDataTable('report-good-out-table');
	})
	   
	var paginationUrl = tracking_goods_out_url + 'filter'
	function exportData(type){
		var searchData = searchField();
		var export_info = {};
		export_info.url = tracking_goods_out_url + 'filter';
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

		var paginationUrl = tracking_goods_out_url + "filter";
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
			"iDisplayLength": 10,
			"order": [],
			"order": [],
			"ajax": {
				url: paginationUrl, // json datasource
				type: "post", // type of method  , by default would be get
				data: searchData,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
					data: 'entry_number',
					orderable: false
				},
				{
					data: 'workflow_id'
				},
				{
					data: 'fba_id',
				},
				{
					data:'account_name',
					orderable: false
				},
				{
					data:'sku'
				},
				{
					data:'unit'
				},
				{
					data:'shipment_value'
				},
				{
					data:'box_pallet'
				},
				{
					data:'no_of_box_pallet'
				},
				{
					data:'from_warehouse',
					orderable: false
				},
				{
					data:'to_warehouse',
					orderable: false
				},
				{
					data:'to_country',
					orderable: false
				},
				{
					data:'way_of_transport',
					orderable: false
				},
				{
					data:'book_by',
					orderable: false
				},
				{
					data:'logistic_partner',
					orderable: false
				},
				{
					data:'booking_date',
					orderable: false
				},
				{
					data:'collection_date',
					orderable: false
				},
				{
					data:'delivery_date',
					orderable: false
				},
				{
					data:'tracking_number',
					orderable: false
				},
				{
					data:'transporter_invoice_cost_gbp',
					orderable: false
				},
				<?php /* ?>
				{
					data: 'action',
					orderable: false
				},
				<?php */ ?>
			],
		});
	}
</script>

@endsection