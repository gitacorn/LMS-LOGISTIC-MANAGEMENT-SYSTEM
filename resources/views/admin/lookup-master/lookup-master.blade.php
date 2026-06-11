@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<button type="button" onclick="openLookupModal(this)" data-module-name="{{ $moduleName }}" class="btn btn btn-theme text-white button-actions-top-bar d-flex align-items-center  border btn-sm mr-2" title="{{ $addTitle }}"><i class="fas fa-plus mr-md-2"></i> <span class="d-md-block d-none"> {{ $addTitle }} </span></a>
				<button class="btn btn button-actions-top-bar filter-btn d-flex align-items-center  border btn-sm" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-lg-3 col-md-6">
							<div class="form-group">
								<label for="search_user" class="control-label">{{ trans('messages.search-by') }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by" id="search_user" placeholder="{{ $searchByTitle }}">
							</div>
						</div>
						<div class="col-lg-2 col-md-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status" onchange="filterData(this);">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.ENABLE_STATUS') }}">{{ trans("messages.enable") }}</option>
									<option value="{{ config('constants.DISABLE_STATUS') }}">{{ trans("messages.disable") }}</option>
								</select>
							</div>
						</div>
						<div class="col-lg-4 d-flex align-items-end gap pt-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData(this);">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body shadow-sm">
					{{ Wild_tiger::readMessage() }}
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover" id="user-table">
							<thead>
								<tr>
									<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
									<th>{{ $columnName }}</th>
									<th width="15%" class="text-center">{{ trans("messages.status") }}</th>
									<th class="actions-col" width="20%">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'lookup-master/lookup-master-list')
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- </div>
            </div> -->

		</div>

	</section>
</main>
<input type="hidden" name="module_name" value="<?php echo $moduleName ?>">

<?php 
$requiredModuleName = trans('messages.required-module-value');
if(isset($moduleName) && !empty($moduleName)){
	switch ($moduleName){
		case config('constants.BOOKING_PORTAL_LOOKUP'):
			$requiredModuleName = trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.booking-portal-name')]);
			break;
		case config('constants.DAILY_MAIL_LOOKUP'):
			$requiredModuleName = trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.email')]);
			break;
	}
}
?>

<script>
	function searchField() {
		var search_by_value = $.trim($('[name="search_by"]').val());
		var module_name = $.trim($('[name="module_name"]').val());
		var search_status = $.trim($('[name="search_status"]').val());

		var searchData = {
			'search_by_value': search_by_value,
			'search_status': search_status,
			'module_name': module_name
		}

		return searchData;
	}

	var module_url = '{{ config("constants.LOOKUP_MASTER_URL") }}' + '/';

	function filterData() {

		var searchFieldName = searchField();

		searchAjax(module_url + 'filter', searchFieldName);
	}

	function openLookupModal(thisitem) {
		var module_name = $.trim($(thisitem).attr('data-module-name'));
		var header_name = 'Add ' + enumText(module_name);

		$("[name='lookup_module_name']").val(module_name);
		var action_type = $.trim($(thisitem).attr('data-action'));
		if (action_type != "" && action_type != null) {
			$("[name='action_type']").val(action_type);
		}
		$("[name='lookup_module_record_id']").val('');
		$('.lookup-modal-action-button').html("{{ trans('messages.submit') }}");
		$('.lookup-modal-action-button').attr('title', "{{ trans('messages.submit') }}");
		$("#add-lookup-modal").find('.twt-modal-header-name').html(header_name);
		$("[name='module_value']").val("");
		$("[name='request_type']").val("{{ config('constants.ADD_REQUEST') }}");
		openBootstrapModal('add-lookup-modal');
		$("#add-lookup-form").validate({
			onkeyup: false,
			errorClass: "invalid-input",
			rules: {
				module_value: {
					required: true,
					noSpace: true,
					email_regex: {{ (isset($moduleName) && !empty($moduleName) && $moduleName == config('constants.DAILY_MAIL_LOOKUP') ? 'true' : 'false')}},
					validateUniqueLookupValue : true
				},
			},
			messages: {
				module_value: {
					required: "{{ $requiredModuleName }}"
				},
			},
		});

	}

	function editLookupModal(thisitem) {
		var module_name = $.trim($(thisitem).attr('data-module-name'));
		var record_id = $.trim($(thisitem).attr('data-record-id'));

		$.ajax({
			type: "POST",
			dataType: "json",
			url: module_url + 'getLookupRecordInfo',
			data: {
				"_token": "{{ csrf_token() }}",
				record_id: record_id,
				module_name: module_name,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if (response.status_code == 1) {
					var value = response.data.recordInfo.v_value;
					var header_name = 'Update ' + enumText(module_name);
					$("[name='module_value']").val(value);
					$("[name='lookup_module_name']").val(module_name);
					$("#add-lookup-modal").find('.twt-modal-header-name').html(header_name);
					$("[name='lookup_module_record_id']").val(record_id);
					$('.lookup-modal-action-button').html("{{ trans('messages.update') }}");
					$('.lookup-modal-action-button').attr('title', "{{ trans('messages.update') }}");
					openBootstrapModal('add-lookup-modal');
					$("#add-lookup-form").validate({
						onkeyup: false,
						errorClass: "invalid-input",
						rules: {
							module_value: {
								required: true,
								noSpace: true,
								email_regex: {{ (isset($moduleName) && !empty($moduleName) && $moduleName == config('constants.DAILY_MAIL_LOOKUP') ? 'true' : 'false')}},
								validateUniqueLookupValue : true
							},
						},
						messages: {
							module_value: {
								required: "{{ $requiredModuleName }}"
							},
						},
					});
				} else {
					alertifyMessage('error', response.message);
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}

	function addLookup() {
		if ($("#add-lookup-form").valid() != true) {
			return false;
		}

		var lookup_module_name = $.trim($("[name='lookup_module_name']").val());
		var module_value = $.trim($("[name='module_value']").val());
		var record_id = $.trim($("[name='lookup_module_record_id']").val());
		var action_type = $("[name='action_type']").val();

		var confirm_box = "";
        var confirm_box_msg = "";
        
        if(record_id != '' && record_id != null){
        	confirm_box = 'Update ' + enumText(lookup_module_name);
        	confirm_box_msg = "Are you sure you want to Update " + enumText(lookup_module_name) + " ?";
        } else {
        	confirm_box = 'Add ' + enumText(lookup_module_name);
        	confirm_box_msg = "Are you sure you want to Add " + enumText(lookup_module_name) + " ?";
        }
        
		alertify.confirm(confirm_box,confirm_box_msg,function() {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: site_url + "add-lookup-master",
				data: {
					"_token": "{{ csrf_token() }}",
					lookup_module_name: lookup_module_name,
					module_value: module_value,
					record_id: record_id,
					request_type: $.trim($("[name='request_type']").val())
				},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if (response.status_code == 1) {
						alertifyMessage('success', response.message);
						$("#add-lookup-modal").modal('hide');
					} else {
						alertifyMessage('error', response.message);
					}
					if (action_type != "" && action_type != null) {
						filterData();
					} else if (record_id != "" && record_id != null) {
						filterData();
					} else {
						var html = response.data.html;
						var related_class_list = lookup_module_name + '-list';
						$('.' + related_class_list).each(function() {
							var selected_list = $.trim($(this).find('option:selected').attr('data-id'));
							$(this).html(html);
							$(this).find("option[data-id='" + selected_list + "']").prop("selected", true);
						})
					}
				},
				error: function() {
					hideLoader();
				}
			});
		},function() {});
	}
</script>
@endsection