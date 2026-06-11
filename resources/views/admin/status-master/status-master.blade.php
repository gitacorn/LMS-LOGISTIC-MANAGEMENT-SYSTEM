@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<?php if((checkPermission(config('permission_constants.ADD_STATUS')) != false)){?>
        		<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" onclick="openStatusModel(this);" title="{{ trans('messages.add-status') }}"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-status') }} </span></button>
			<?php }?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-3 col-lg-6">
							<div class="form-group">
								<label for="search_by_status" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_status" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.status") }} ">
							</div>
						</div>
						<div class="col-xl-3 col-lg-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.ENABLE_STATUS')}}">{{ trans("messages.enable") }}</option>
									<option value="{{ config('constants.DISABLE_STATUS')}}">{{ trans("messages.disable") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-6 d-flex align-items-end gap pt-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData()">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body">
					{{ Wild_tiger::readMessage() }}
					<div class="table-responsive fixed-table-x">
						<table class="table table-sm table-bordered table-hover" id="status-table">
							<thead>
								<tr>
									<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.status") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.sequence") }}</th>
									<th class="text-center" width="30%">{{ trans("messages.status") }}</th>
									<?php
									if( (checkPermission(config('permission_constants.EDIT_STATUS')) != false) || (checkPermission(config('permission_constants.DELETE_STATUS')) != false) ){?>
									 	<th class="actions-col" width="20%">{{ trans("messages.actions") }}</th>
									<?php 
									}?>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'status-master/status-master-list')
							</tbody>

						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>

<div class="modal fade bd-example-modal-lg" id="add-status-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-status') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id '=> 'add-status-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
				<div class="modal-body add-status-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button status-modal-action-button" onclick="addStatusModel();" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
			{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
var status_module_url = '{{config("constants.STATUS_MASTER_URL")}}' + '/';
	$("#add-status-master-form").validate({
		errorClass: "invalid-input",
		rules: {
			status: {
				required: true,
				noSpace: true,
				validateUniqueStatus: true
			},
			sequence: {
				required: true,
				noSpace: true
			},

		},
		messages: {
			status: {
				required: "{{ trans('messages.require-enter-status') }}"
			},
			sequence: {
				required: "{{ trans('messages.require-sequence') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
	function openStatusModel(thisitem){
		editStatusModel();
	}

	function addStatusModel(){
		if($('#add-status-master-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var status = $.trim($('[name="status"]').val());
		var sequence = $.trim($('[name="sequence"]').val());
		var confirm_box = "";
        var confirm_box_msg = "";
		if(record_id == 0){
	    	confirm_box = "{{ trans('messages.add-status') }}";
	    	confirm_box_msg = "{{ trans ( 'messages.confirm-add-status-msg') }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-status') }}";
	    	confirm_box_msg = "{{ trans ( 'messages.confirm-update-status-msg') }}";
	    } 
	    alertify.confirm(confirm_box,confirm_box_msg,function() {  
			$.ajax({
				type: "POST",
				dataType: "json",
				url: status_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'status':status,'sequence':sequence,'record_id':record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						
						$("#add-status-modal").modal('hide');
						if(record_id != '' && record_id != null){
							alertifyMessage('success',response.message);
							$(current_row).parents('.status-record').html(response.data.html);
						}else{
							
							$("[name='session_redirect_module_name']").val("{{ trans('messages.status') }}");
							$("#manage-session-messages-form").submit();
						}
					} else {
						alertifyMessage('error',response.message);
					}
					
				},
				error: function() {
					hideLoader();
				}
			});
	    },function() {});	
	}
	var current_row ='';
	function editStatusModel(thisitem){
		current_row=thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		
		$.ajax({
			type: "POST",
			url: status_module_url + 'edit',
			data: {
				"_token": "{{ csrf_token() }}",
				record_id: record_id,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if(record_id !="" && record_id != null){
					var header_name = "{{ trans('messages.update-status') }}";
					var button_name = "{{ trans('messages.update') }}";
					$('.add-status-modal-html').html("");
					$('.add-status-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("#add-status-modal").find('.status-modal-action-button').html(button_name);
					$('.status-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
					$("#add-status-modal").find('.twt-modal-header-name').html(header_name);
				} else {
					var header_name = "{{ trans('messages.add-status') }}" ;
					var button_name = "{{ trans('messages.submit') }}" ;
					$('.add-status-modal-html').html("");
					$('.add-status-modal-html').html(response);
					$("[name='record_id']").val("");
					$("#add-status-modal").find('.status-modal-action-button').html(button_name);
					$('.status-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
					$("#add-status-modal").find('.twt-modal-header-name').html(header_name);
				}
				openBootstrapModal('add-status-modal');
			
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function searchField(){
		var search_by_status = $.trim($('[name="search_by_status"]').val());
		var search_status = $.trim($('[name="search_status"]').val());
		var searchData = {
	            'search_by_status':search_by_status,
	            'search_status': search_status,
	        }
	        return searchData;
	}
	function filterData(){
		var searchFieldName = searchField();

		searchAjax(status_module_url + 'filter' , searchFieldName);
	}
	var paginationUrl = status_module_url + 'filter'

	$.validator.addMethod("validateUniqueStatus", function (value, element) {
		 
		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: status_module_url +'checkUniqueStatus',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'status': $.trim($("[name='status']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
			     },
			beforeSend: function() {
				
			},
			success: function (response) {
				
				if (response.status_code == 1) {
					return false;
				} else {
					result = false;
					return true;
				}
			}
		});
		return result;
	}, '<?php echo trans("messages.error-unique-status")?>');
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection