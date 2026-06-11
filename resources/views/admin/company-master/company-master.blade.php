@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php if((checkPermission(config('permission_constants.ADD_COMPANY')) != false)){?>
        		<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" title="{{ trans('messages.add-company') }}" onclick="openComapnyModel(this);"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-company') }} </span></button>
        <?php }?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-5 col-lg-6">
							<div class="form-group">
								<label for="search_by_company" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_company" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.company-name") }}, {{ trans("messages.code") }}, {{ trans("messages.short-code") }}, {{ trans("messages.email") }}">
							</div>
						</div>
						<div class=" col-xl-3">
							<div class="form-group">
								
								<label for="search_country" class="control-label">{{ trans('messages.company-country') }}</label>
								<select class="form-control" name="search_country" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
										if(!empty($countryRecordDetails)){
											foreach ($countryRecordDetails as $countryRecordDetail){
												?>
												<option value="<?php echo Wild_tiger::encode($countryRecordDetail->i_id)?>"><?php echo $countryRecordDetail->v_country_name?></option>
	                         
												<?php 
											}
										}
									?>
								</select>
							</div>
							
						</div>
						<div class="col-xl-2 col-lg-6">
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
									<th class="sr-col" style="min-width:50px; max-width:50px;">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" style="min-width:150px; max-width:150px;">{{ trans("messages.company-name") }}</th>
									<th class="text-left"style="min-width:100px; max-width:100px;">{{ trans("messages.company-code") }}</th>
									<th class="text-left" style="min-width:100px; max-width:100px;">{{ trans("messages.company-short-code") }}</th>
									<th class="text-left"style="min-width:100px; max-width:100px;">{{ trans("messages.company-country") }}</th>
									<th class="text-center" style="min-width:120px; max-width:120px;">{{ trans("messages.email") }}</th>
									<th class="text-center" style="min-width:20px; max-width:20px;">{{ trans("messages.status") }}</th>
									<?php if( (checkPermission(config('permission_constants.EDIT_COMPANY')) != false) || (checkPermission(config('permission_constants.DELETE_COMPANY')) != false) ){?>
											<th class="actions-col" style="min-width:90px; max-width:90px;">{{ trans("messages.actions") }}</th>
									<?php }?>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'company-master/company-master-list')
							</tbody>

						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>




<div class="modal fade bd-example-modal-lg" id="add-company-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-company') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id '=> 'add-company-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
				<div class="modal-body add-company-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button company-modal-action-button" title="{{ trans('messages.submit') }}" onclick="addCompanyModel();">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
var company_module_url = '{{config("constants.COMPANY_MASTER_URL")}}' + '/';
	$("#add-company-master-form").validate({
		errorClass: "invalid-input",
		rules: {
			company_name: {
				required: true,
				noSpace: true,
				validateUniqueCompanyName: true
			},
			company_code: {
				required: true,
				noSpace: true,
				validateUniqueCompanyCode: true
			},
			company_short_code:{
				required: true,
				noSpace: true,
				validateUniqueCompanyShortCode: true
			},
			select_country_name: {
				required: true,
				noSpace: true
			},
			email: {
				comma_separated_email_regex : true
			},
		},
		messages: {
			company_name: {
				required: "{{ trans('messages.require-company-name') }}"
			},
			company_code: {
				required: "{{ trans('messages.require-company-code') }}"
			},
			company_short_code: {
				required: "{{ trans('messages.require-company-short-code') }}"
			},
			select_country_name: {
				required: "{{ trans('messages.require-company-country') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
	function openComapnyModel(thisitem){
		editCompanyModel();
	}

	function addCompanyModel(){
		if($('#add-company-master-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var company_name = $.trim($('[name="company_name"]').val());
		var company_code = $.trim($('[name="company_code"]').val());
		var company_short_code = $.trim($('[name="company_short_code"]').val());
		var select_country_name = $.trim($('[name="select_country_name"]').val());
		var email = $.trim($('[name="email"]').val());
		
		var confirm_box = "";
        var confirm_box_msg = "";
        if(record_id == 0){
        	confirm_box = "{{ trans('messages.add-company') }}";
        	confirm_box_msg = "{{ trans ( 'messages.confirm-add-company-msg') }}";
        } else {
        	confirm_box = "{{ trans('messages.update-company') }}";
        	confirm_box_msg = "{{ trans ( 'messages.confirm-update-company-msg') }}";
        }  
        alertify.confirm(confirm_box,confirm_box_msg,function() {  
			$.ajax({
				type: "POST",
				dataType: "json",
				url: company_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'company_name':company_name,'company_code':company_code,'company_short_code':company_short_code,'select_country_name':select_country_name,'email':email,'record_id':record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						
						$("#add-company-modal").modal('hide');
						if(record_id != '' && record_id != null){
							alertifyMessage('success',response.message);
							$(current_row).parents('.company-record').html(response.data.html);
						}else{
							
							$("[name='session_redirect_module_name']").val("{{ trans('messages.company') }}");
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
	function editCompanyModel(thisitem){
		current_row=thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		
		$.ajax({
			type: "POST",
			url: company_module_url + 'edit',
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
					var header_name = "{{ trans('messages.update-company') }}";
					var button_name = "{{ trans('messages.update') }}";
					$('.add-company-modal-html').html("");
					$('.add-company-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("#add-company-modal").find('.company-modal-action-button').html(button_name);
					$('.company-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
					$("#add-company-modal").find('.twt-modal-header-name').html(header_name);
				} else {
					var header_name = "{{ trans('messages.add-company') }}" ;
					var button_name = "{{ trans('messages.submit') }}" ;
					$('.add-company-modal-html').html("");
					$('.add-company-modal-html').html(response);
					$("[name='record_id']").val("");
					$("#add-company-modal").find('.company-modal-action-button').html(button_name);
					$('.company-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
					$("#add-company-modal").find('.twt-modal-header-name').html(header_name);
				}
				openBootstrapModal('add-company-modal');
			
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function searchField(){
		var search_by_company = $.trim($('[name="search_by_company"]').val());
		var search_country = $.trim($('[name="search_country"]').val());
		var search_status = $.trim($('[name="search_status"]').val());
		var searchData = {
	            'search_by_company':search_by_company,
	            'search_country': search_country,
	            'search_status': search_status,
	        }
	        return searchData;
	}
	function filterData(){
		var searchFieldName = searchField();

		searchAjax(company_module_url + 'filter' , searchFieldName);
	}
	var paginationUrl = company_module_url + 'filter'

	$.validator.addMethod("validateUniqueCompanyName", function (value, element) {
		 
		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: company_module_url +'checkUniqueCompanyName',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'company_name': $.trim($("[name='company_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-company-name")?>');
	
	$.validator.addMethod("validateUniqueCompanyCode", function (value, element) {
		 
		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: company_module_url +'checkUniqueCompanyCode',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'company_code': $.trim($("[name='company_code']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-company-code")?>');

	$.validator.addMethod("validateUniqueCompanyShortCode", function (value, element) {
		 
		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: company_module_url +'checkUniqueCompanyShortCode',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'company_short_code': $.trim($("[name='company_short_code']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-company-short-code")?>');

</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
	
@endsection