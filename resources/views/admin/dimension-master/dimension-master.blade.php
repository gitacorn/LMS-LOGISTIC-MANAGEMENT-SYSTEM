@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php 
		if((checkPermission(config('permission_constants.ADD_DIMENSION')) != false)){
        ?>
			<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" onclick="openDimensionModel();" title="{{ trans('messages.add-dimension') }}"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-dimension') }} </span></button>
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
								<label for="search_by_dimension" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_dimension" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.dimension-name") }}, {{ trans("messages.dimension-size") }}">
							</div>
						</div>
					<div class="col-xl-3 col-lg-6">
							<div class="form-group">
								<label class="control-label" for="search_dimension_type">{{ trans("messages.dimension-type") }}</label>
								<select class="form-control" name="search_dimension_type" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.BOX')}}">{{ trans("messages.box") }}</option>
									<option value="{{ config('constants.PALLET')}}">{{ trans("messages.pallet") }}</option>
								</select>
							</div>
						</div>
						
						
						<div class="col-xl-3 col-lg-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status"onchange="filterData()">
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
						<table class="table table-sm table-bordered table-hover" id="dimension-table">
							<thead>
								<tr>
									<th class="sr-col" style="min-width:80px; max-width:80px;">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" style="min-width:70px; max-width:70px;">{{ trans("messages.dimension-type") }}</th>
									<th class="text-left" style="min-width:80px; max-width:80px;">{{ trans("messages.dimension-name") }}</th>
									<th class="text-left" style="min-width:80px; max-width:80px;">{{ trans("messages.dimension-size") }}</th>
									<th class="text-center" style="min-width:40px; max-width:40px;">{{ trans("messages.status") }}</th>
									<?php 
									if( (checkPermission(config('permission_constants.EDIT_DIMENSION')) != false) || (checkPermission(config('permission_constants.DELETE_DIMENSION')) != false) ){?>
	                                	<th class="actions-col" style="min-width:120px; max-width:120px;">{{ trans("messages.actions") }}</th>
	                                <?php 
									}?>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'dimension-master/dimension-master-list')
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>




<div class="modal fade bd-example-modal-lg" id="add-dimension-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-dimension') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id '=> 'add-dimension-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
			
				<div class="modal-body add-dimension-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}" onclick="addDimensionMasterModel()">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
	var dimension_module_url = '{{config("constants.DIMENSION_MASTER_URL")}}' + '/';
	$("#add-dimension-master-form").validate({
		errorClass: "invalid-input",
		rules: {
			dimension_name: {
				required: true,
				noSpace: true,
				validateUniqueDimensionName: true
			},
			dimension_size: {
				required: true,
				noSpace: true,
				validateUniqueDimensionSize: true
			},
			dimension_type: {
				required: true,
				noSpace: true
			},

		},
		messages: {
			dimension_name: {
				required: "{{ trans('messages.require-dimension-name') }}"
			},
			dimension_size: {
				required: "{{ trans('messages.require-dimension-size') }}"
			},
			dimension_type: {
				required: "{{ trans('messages.require-dimension-type') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
	function openDimensionModel(){
		editDimensionModel();
	}

	function addDimensionMasterModel(){
		if($('#add-dimension-master-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var dimension_type = $.trim($('[name="dimension_type"]:checked').val());
		var dimension_name = $.trim($('[name="dimension_name"]').val());
		var dimension_size = $.trim($('[name="dimension_size"]').val());
		var confirm_box = "";
	    var confirm_box_msg = "";
	    if(record_id == 0){
	    	confirm_box = "{{ trans('messages.add-dimension') }}";
	    	confirm_box_msg = "{{ trans ( 'messages.confirm-add-dimension-msg') }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-dimension') }}";
	    	confirm_box_msg = "{{ trans ( 'messages.confirm-update-dimension-msg') }}";
	    } 
	    alertify.confirm(confirm_box,confirm_box_msg,function() {  
			$.ajax({
				type: "POST",
				dataType: "json",
				url: dimension_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'dimension_type':dimension_type,'dimension_name':dimension_name,'dimension_size':dimension_size,'record_id':record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						
						$("#add-dimension-modal").modal('hide');
						if(record_id != '' && record_id != null){
							alertifyMessage('success',response.message);
							$(current_row).parents('.dimension-record').html(response.data.html);
						}else{
							//window.location.reload();
							$("[name='session_redirect_module_name']").val("{{ trans('messages.dimension') }}");
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
	function editDimensionModel(thisitem){
		current_row=thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		
		$.ajax({
			type: "POST",
			url: dimension_module_url + 'edit',
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
					var header_name = "{{ trans('messages.update-dimension') }}";
					var button_name = "{{ trans('messages.update') }}";
					$('.add-dimension-modal-html').html("");
					$('.add-dimension-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("#add-dimension-modal").find('.dimension-modal-action-button').html(button_name);
					$('.dimension-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
					$("#add-dimension-modal").find('.twt-modal-header-name').html(header_name);
				} else {
					var header_name = "{{ trans('messages.add-dimension') }}" ;
					var button_name = "{{ trans('messages.submit') }}" ;
					$('.add-dimension-modal-html').html("");
					$('.add-dimension-modal-html').html(response);
					$("[name='record_id']").val("");
					$("#add-dimension-modal").find('.dimension-modal-action-button').html(button_name);
					$('.dimension-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
					$("#add-dimension-modal").find('.twt-modal-header-name').html(header_name);
				}
				openBootstrapModal('add-dimension-modal');
			
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function searchField(){
		var search_dimension_type = $.trim($('[name="search_dimension_type"]').val());
		var search_by_dimension = $.trim($('[name="search_by_dimension"]').val());
		var search_status = $.trim($('[name="search_status"]').val());
		var searchData = {
	            'search_dimension_type':search_dimension_type,
	            'search_by_dimension':search_by_dimension,
	            'search_status': search_status,
	        }
	        return searchData;
	}
	function filterData(){
		var searchFieldName = searchField();

		searchAjax(dimension_module_url + 'filter' , searchFieldName);
	}
	var paginationUrl = dimension_module_url + 'filter'

	$.validator.addMethod("validateUniqueDimensionName", function (value, element) {

		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: dimension_module_url +'checkUniqueDimensionName',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'dimension_type': $.trim($('[name="dimension_type"]:checked').val()), 'dimension_name': $.trim($('[name="dimension_name"]').val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-dimension-name")?>');

	$.validator.addMethod("validateUniqueDimensionSize", function (value, element) {

		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: dimension_module_url +'checkUniqueDimensionSize',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'dimension_type': $.trim($('[name="dimension_type"]:checked').val()), 'dimension_size': $.trim($('[name="dimension_size"]').val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-dimension-size")?>');
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection