@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php if((checkPermission(config('permission_constants.ADD_CURRENCY')) != false)){?>
				<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" onclick="openCurrencyModel(this)" title="{{ trans('messages.add-currency') }}"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-currency') }} </span></button>
		<?php }?>
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
								<label for="search_by_currency" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_currency" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.currency-name") }}, {{ trans("messages.symbol") }}">
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
						<table class="table table-sm table-bordered table-hover" id="currency-table">
							<thead>
								<tr>
									<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.currency-name") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.currency-symbol") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.gbp-conversation-rate") }}</th>
									<th class="text-center" width="30%">{{ trans("messages.status") }}</th>
									<?php if( (checkPermission(config('permission_constants.EDIT_CURRENCY')) != false) || (checkPermission(config('permission_constants.DELETE_CURRENCY')) != false) ){?>
											<th class="actions-col" width="20%">{{ trans("messages.actions") }}</th>
									<?php }?>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'currency-master/currency-master-list')
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>




<div class="modal fade bd-example-modal-lg" id="add-currency-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-currency') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id '=> 'add-currency-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
				
				<div class="modal-body add-currency-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button currency-modal-action-button" onclick="addCurrencyModel()" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
var currency_module_url = '{{config("constants.CURRENCY_MASTER_URL")}}' + '/';
	$("#add-currency-master-form").validate({
		errorClass: "invalid-input",
		rules: {
			currency_name: {
				required: true,
				noSpace: true
			},
			currency_code: {
				required: true,
				noSpace: true,
				validateUniqueCurrencyCode: true
			},
			gbp_conversation_rate: {
				required: true,
			}

		},
		messages: {
			currency_name: {
				required: "{{ trans('messages.require-currency-name') }}"
			},
			currency_code: {
				required: "{{ trans('messages.require-currency-symbol') }}"
			},
			gbp_conversation_rate: {
				required: "{{ trans('messages.require-gbp-conversation-rate') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
	function openCurrencyModel(thisitem){
		editCurrencyModel();
	}

	function addCurrencyModel(){
		if($('#add-currency-master-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var currency_name = $.trim($('[name="currency_name"]').val());
		var currency_code = $.trim($('[name="currency_code"]').val());
		var gbp_conversation_rate = $.trim($('[name="gbp_conversation_rate"]').val());
		var confirm_box = "";
        var confirm_box_msg = "";
		if(record_id == 0){
	    	confirm_box = "{{ trans('messages.add-currency') }}";
	    	confirm_box_msg = "{{ trans ( 'messages.confirm-add-currency-msg') }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-currency') }}";
	    	confirm_box_msg = "{{ trans ( 'messages.confirm-update-currency-msg') }}";
	    } 
	    alertify.confirm(confirm_box,confirm_box_msg,function() {  
			$.ajax({
				type: "POST",
				dataType: "json",
				url: currency_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'currency_name':currency_name,'currency_code':currency_code,'record_id':record_id,'gbp_conversation_rate':gbp_conversation_rate,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						
						$("#add-currency-modal").modal('hide');
						if(record_id != '' && record_id != null){
							alertifyMessage('success',response.message);
							$(current_row).parents('.currency-record').html(response.data.html);
						}else{
							
							$("[name='session_redirect_module_name']").val("{{ trans('messages.currency') }}");
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
	function editCurrencyModel(thisitem){
		current_row=thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		
		$.ajax({
			type: "POST",
			url: currency_module_url + 'edit',
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
					var header_name = "{{ trans('messages.update-currency') }}";
					var button_name = "{{ trans('messages.update') }}";
					$('.add-currency-modal-html').html("");
					$('.add-currency-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("#add-currency-modal").find('.currency-modal-action-button').html(button_name);
					$('.currency-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
					$("#add-currency-modal").find('.twt-modal-header-name').html(header_name);
				} else {
					var header_name = "{{ trans('messages.add-currency') }}" ;
					var button_name = "{{ trans('messages.submit') }}" ;
					$('.add-currency-modal-html').html("");
					$('.add-currency-modal-html').html(response);
					$("[name='record_id']").val("");
					$("#add-currency-modal").find('.currency-modal-action-button').html(button_name);
					$('.currency-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
					$("#add-currency-modal").find('.twt-modal-header-name').html(header_name);
				}
				openBootstrapModal('add-currency-modal');
			
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function searchField(){
		var search_by_currency = $.trim($('[name="search_by_currency"]').val());
		var search_status = $.trim($('[name="search_status"]').val());
		var searchData = {
	            'search_by_currency':search_by_currency,
	            'search_status': search_status,
	        }
	        return searchData;
	}
	function filterData(){
		var searchFieldName = searchField();

		searchAjax(currency_module_url + 'filter' , searchFieldName);
	}
	var paginationUrl = currency_module_url + 'filter'

	$.validator.addMethod("validateUniqueCurrencyCode", function (value, element) {
		 
		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: currency_module_url +'checkUniqueCurrencyCode',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'currency_code': $.trim($("[name='currency_code']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-currency-symbol")?>');

</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
	

@endsection