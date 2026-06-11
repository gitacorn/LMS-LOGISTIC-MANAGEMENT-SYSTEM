@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.country-master") }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<?php 
			if((checkPermission(config('permission_constants.ADD_COUNTRY')) != false)){
        	?>
				<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" onclick="openCountryModel(this);" title="{{ trans('messages.add-country') }}"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-country') }} </span></button>
				<?php 
			}?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-4 col-lg-6">
							<div class="form-group">
								<label for="search_by_country" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_country" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.country-name') }}, {{ trans('messages.code') }}">
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
						<table class="table table-sm table-bordered table-hover" id="country-table">
							<thead>
								<tr>
									<th class="sr-col" style="min-width:80px; max-width:80px; ">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" style="min-width:100px; max-width:100px; ">{{ trans("messages.country-name") }}</th>
									<th class="text-left" style="min-width:100px; max-width:100px;">{{ trans("messages.country-code") }}</th>
									<th class="text-center" style="min-width:50px; max-width:50px; ">{{ trans("messages.status") }}</th>
									<?php 
									if( (checkPermission(config('permission_constants.EDIT_COUNTRY')) != false) || (checkPermission(config('permission_constants.DELETE_COUNTRY')) != false) ){?>
                                    	<th class="actions-col" style="min-width:180px; max-width:180px; ">{{ trans("messages.actions") }}</th>
									<?php 
									}?>
								</tr>
							</thead>

							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'country-master/country-master-list')
							</tbody>

						</table>
					</div>
				</div>
			</div>

		</div>
	</section>
</main>

<div class="modal fade bd-example-modal-lg" id="add-country-master-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-country') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id '=> 'add-country-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
				<div class="modal-body add-country-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button country-modal-action-button" title="{{ trans('messages.submit') }}" onclick="addContryMasterModel()">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
var country_module_url = '{{config("constants.COUNTRY_MASTER_URL")}}' + '/';
	$("#add-country-master-form").validate({
		errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false, 
		rules: {
			country_name: {
				required: true,
				noSpace: true,
				validateUniqueCountryName:true
			},
			country_code: {
				required: true,
				noSpace: true,
				validateUniqueCountryCode:true
			},

		},
		messages: {
			country_name: {
				required: "{{ trans('messages.require-country-name') }}"
			},
			country_code: {
				required: "{{ trans('messages.require-country-code') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
function openCountryModel(thisitem){
	
	editCountryModel();
}

function addContryMasterModel(){
	if($('#add-country-master-form').valid() != true){
		return false;
	}
	var record_id = $.trim($('[name="record_id"]').val());
	var country_name = $.trim($('[name="country_name"]').val());
	var country_code = $.trim($('[name="country_code"]').val());
	var confirm_box = "";
    var confirm_box_msg = "";
    if(record_id == 0){
    	confirm_box = "{{ trans('messages.add-country') }}";
    	confirm_box_msg = "{{ trans ( 'messages.confirm-add-country-msg') }}";
    } else {
    	confirm_box = "{{ trans('messages.update-country') }}";
    	confirm_box_msg = "{{ trans ( 'messages.confirm-update-country-msg') }}";
    } 
    alertify.confirm(confirm_box,confirm_box_msg,function() {   
		$.ajax({
			type: "POST",
			dataType: "json",
			url: country_module_url + 'add',
			data: {"_token": "{{ csrf_token() }}",
				'country_name':country_name,'country_code':country_code,'record_id':record_id,
				'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
			
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response.status_code == 1 ){
					
					$("#add-country-master-modal").modal('hide');
					if(record_id != '' && record_id != null){
						alertifyMessage('success',response.message);
						$(current_row).parents('.country-record').html(response.data.html);
					}else{
						//window.location.reload();
						$("[name='session_redirect_module_name']").val("{{ trans('messages.country') }}");
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
function editCountryModel(thisitem){
	current_row=thisitem;
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	
	$.ajax({
		type: "POST",
		url: country_module_url + 'edit',
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
				var header_name = "{{ trans('messages.update-country') }}";
				var button_name = "{{ trans('messages.update') }}";
				$('.add-country-modal-html').html("");
				$('.add-country-modal-html').html(response);
				$("[name='record_id']").val(record_id);
				$("#add-country-master-modal").find('.country-modal-action-button').html(button_name);
				$('.country-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
				$("#add-country-master-modal").find('.twt-modal-header-name').html(header_name);
			} else {
				var header_name = "{{ trans('messages.add-country') }}" ;
				var button_name = "{{ trans('messages.submit') }}" ;
				$('.add-country-modal-html').html("");
				$('.add-country-modal-html').html(response);
				$("[name='record_id']").val("");
				$("#add-country-master-modal").find('.country-modal-action-button').html(button_name);
				$('.country-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
				$("#add-country-master-modal").find('.twt-modal-header-name').html(header_name);
			}
			openBootstrapModal('add-country-master-modal');
		
		},
		error: function() {
			hideLoader();
		}
	});
}
function searchField(){
	var search_by_country = $.trim($('[name="search_by_country"]').val());
	var search_status = $.trim($('[name="search_status"]').val());
	var searchData = {
            'search_by_country':search_by_country,
            'search_status': search_status,
        }
        return searchData;
}
function filterData(){
	var searchFieldName = searchField();

	searchAjax(country_module_url + 'filter' , searchFieldName);
}
var paginationUrl = country_module_url + 'filter'

$.validator.addMethod("validateUniqueCountryName", function (value, element) {
	 
	var result = true;
	$.ajax({
		type: "POST",
		async: false,
		url: country_module_url +'checkUniqueCountryName',
		dataType: "json",
		data: {
			"_token": "{{ csrf_token() }}",
			'country_name': $.trim($("[name='country_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
}, '<?php echo trans("messages.error-unique-country-name")?>');
		
$.validator.addMethod("validateUniqueCountryCode", function (value, element) {
	 
	var result = true;
	$.ajax({
		type: "POST",
		async: false,
		url: country_module_url +'checkUniqueCountryCode',
		dataType: "json",
		data: {
			"_token": "{{ csrf_token() }}",
			'country_code': $.trim($("[name='country_code']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
}, '<?php echo trans("messages.error-unique-country-code")?>');
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection