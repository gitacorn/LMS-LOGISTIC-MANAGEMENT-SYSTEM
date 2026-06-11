@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<?php 
			if((checkPermission(config('permission_constants.ADD_DOCUMENT_TYPE')) != false)){
        	?>
				<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" title="{{ trans('messages.add-document-type') }}" onclick="openDocumentTypeModel();"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none" >{{ trans('messages.add-document-type') }} </span></button>
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
						<div class="col-xl-3 col-lg-6">
							<div class="form-group">
								<label for="search_by_document" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_document" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.document-name") }}">
							</div>
						</div>
						<div class="col-xl-3 col-lg-6">
							<div class="form-group">
								<label class="control-label" for="search_document_type">{{ trans("messages.document-type") }}</label>
								<select class="form-control" name="search_document_type" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.LOGISTIC')}}">{{ trans("messages.logistic") }}</option>
									<option value="{{ config('constants.BUYER')}}">{{ trans("messages.buyer") }}</option>
								</select>
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
						<table class="table table-sm table-bordered table-hover" id="user-table">
							<thead>
								<tr>
									<th class="sr-col" style="min-width:80px; max-width:80px;">{{ trans("messages.sr-no") }}</th>
									<th class="text-left"style="min-width:80px; max-width:80px;">{{ trans("messages.document-type") }}</th>
									<th class="text-left" style="min-width:100px; max-width:100px;" >{{ trans("messages.document-name") }}</th>
									<th class="text-center" style="min-width:30px; max-width:30px;">{{ trans("messages.status") }}</th>
									<?php if( (checkPermission(config('permission_constants.EDIT_DOCUMENT_TYPE')) != false) || (checkPermission(config('permission_constants.DELETE_DOCUMENT_TYPE')) != false) ){?>
                                    <th class="actions-col" style="min-width:170px; max-width:170px;">{{ trans("messages.actions") }}</th>
                                    <?php }?>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'document-type-master/document-type-master-list')
							</tbody>

						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>

<div class="modal fade bd-example-modal-lg" id="add-document-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-document-type') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id '=> 'add-document-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
				<div class="modal-body add-document-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button document-modal-action-button" title="{{ trans('messages.submit') }}" onclick="addDocumentTypeModel()">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
var document_type_url = '{{config("constants.DOCUMENT_TYPE_MASTER_URL")}}' + '/';
	$("#add-document-master-form").validate({
		errorClass: "invalid-input",
		rules: {
			document_type: {
				required: true
			},
			document_name: {
				required: true,
				validateUniqueDocumentName: true
			},

		},
		messages: {
			document_type: {
				required: "{{ trans('messages.require-document-type') }}"
			},
			document_name: {
				required: "{{ trans('messages.require-document-name') }}"
			},

		}
	});
	function openDocumentTypeModel(){
		editDocumentTypeModel();
	}

	function addDocumentTypeModel(){
		if($('#add-document-master-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var document_type = $.trim($('[name="document_type"]:checked').val());
		var document_name = $.trim($('[name="document_name"]').val());
		if(record_id == 0){
        	confirm_box = "{{ trans('messages.add-document-type') }}";
        	confirm_box_msg = "{{ trans ( 'messages.confirm-add-document-type-msg') }}";
        } else {
        	confirm_box = "{{ trans('messages.update-document-type') }}";
        	confirm_box_msg = "{{ trans ( 'messages.confirm-update-document-type-msg') }}";
        }  
        alertify.confirm(confirm_box,confirm_box_msg,function() {  
			$.ajax({
				type: "POST",
				dataType: "json",
				url: document_type_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'document_type':document_type,'document_name':document_name,'record_id':record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						
						$("#add-document-modal").modal('hide');
						if(record_id != '' && record_id != null){
							alertifyMessage('success',response.message);
							$(current_row).parents('.document-record').html(response.data.html);
						}else{
							//window.location.reload();
							$("[name='session_redirect_module_name']").val("{{ trans('messages.document-type') }}");
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
	function editDocumentTypeModel(thisitem){
		current_row=thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		
		$.ajax({
			type: "POST",
			url: document_type_url + 'edit',
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
					var header_name = "{{ trans('messages.update-document-type') }}";
					var button_name = "{{ trans('messages.update') }}";
					$('.add-document-modal-html').html("");
					$('.add-document-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("#add-document-modal").find('.document-modal-action-button').html(button_name);
					$('.document-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
					$("#add-document-modal").find('.twt-modal-header-name').html(header_name);
				} else {
					var header_name = "{{ trans('messages.add-document-type') }}" ;
					var button_name = "{{ trans('messages.submit') }}" ;
					$('.add-document-modal-html').html("");
					$('.add-document-modal-html').html(response);
					$("[name='record_id']").val("");
					$("#add-document-modal").find('.document-modal-action-button').html(button_name);
					$('.document-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
					$("#add-document-modal").find('.twt-modal-header-name').html(header_name);
				}
				openBootstrapModal('add-document-modal');
			
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function searchField(){
		var search_by_document = $.trim($('[name="search_by_document"]').val());
		var search_document_type = $.trim($('[name="search_document_type"]').val());
		var search_status = $.trim($('[name="search_status"]').val());
		var searchData = {
	            'search_by_document':search_by_document,
	            'search_document_type':search_document_type,
	            'search_status': search_status,
	        }
	        return searchData;
	}
	function filterData(){
		var searchFieldName = searchField();

		searchAjax(document_type_url + 'filter' , searchFieldName);
	}
	var paginationUrl = document_type_url + 'filter'

	$.validator.addMethod("validateUniqueDocumentName", function (value, element) {

		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: document_type_url +'checkUniqueDocumentName',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'document_type': $.trim($('[name="document_type"]:checked').val()), 'document_name': $.trim($('[name="document_name"]').val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, '<?php echo trans("messages.error-unique-document-type")?>');
	
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection