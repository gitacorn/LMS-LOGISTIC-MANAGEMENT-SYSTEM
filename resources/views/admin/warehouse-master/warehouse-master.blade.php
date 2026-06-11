@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php 
		if($pageTitle == trans('messages.warehouse-master')){
			$countryName = trans('messages.warehouse-country');
			$searchabyName = trans("messages.warehouse-name");
			$checkPermission = checkPermission(config('permission_constants.ADD_WAREHOUSE'));
		} else if($pageTitle == trans('messages.port-master')){
			$countryName = trans('messages.port-country');
			$searchabyName = trans("messages.port-name");
			$checkPermission = checkPermission(config('permission_constants.ADD_PORT'));
		} else {
			$countryName = trans('messages.location-country');
			$searchabyName = trans("messages.location-name");
			$checkPermission = checkPermission(config('permission_constants.ADD_LOCATION'));
		}
		?>
		<?php if( $checkPermission != false ){?>
			<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" title="{{ $addPageTitle }}" data-record-type="{{ $recordType }}" onclick="openWarehouseModel(this);"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ $addPageTitle }} </span></button>
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
								<label for="search_by_warehouse" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_warehouse" placeholder="{{ trans("messages.search-by") }} {{ $searchabyName }}, {{ trans("messages.code") }}, {{ trans("messages.address") }}{{(isset($recordType) && !empty($recordType) && $recordType == config('constants.WAREHOUSE') ? ', ' . trans("messages.email") : '')}}">
							</div>
						</div>
						<div class=" col-xl-3">
							<div class="form-group">
								<label for="search_country" class="control-label">{{ $countryName }}</label>
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
									<th class="sr-col" style="min-width:80px; max-width:80px;">{{ trans("messages.sr-no") }}</th>
									<?php if($pageTitle == trans('messages.warehouse-master')){
										$editPermission = checkPermission(config('permission_constants.EDIT_WAREHOUSE'));
										$deletePermission = checkPermission(config('permission_constants.DELETE_WAREHOUSE'));
									?>
										<th class="text-left" style="min-width:120px; max-width:120px;">{{ trans("messages.warehouse-name") }}</th>
										<th class="text-left" style="min-width:200px; max-width:200px;">{{ trans("messages.warehouse-code") }}</th>
										<th class="text-left" style="min-width:80px; max-width:80px;">{{ trans("messages.warehouse-address") }}</th>
										<th class="text-left" style="min-width:100px; max-width:100px;">{{ trans("messages.warehouse-country") }}</th>
										<th class="text-left" style="min-width:100px; max-width:100px;">{{ trans("messages.email") }}</th>
									<?php 
									} else if($pageTitle == trans('messages.port-master')){
										$editPermission = checkPermission(config('permission_constants.EDIT_PORT'));
										$deletePermission = checkPermission(config('permission_constants.DELETE_PORT'));
									?>
										<th class="text-left" style="min-width:120px; max-width:120px;">{{ trans("messages.port-name") }}</th>
										<th class="text-left" style="min-width:200px; max-width:200px;">{{ trans("messages.port-code") }}</th>
										<th class="text-left" style="min-width:80px; max-width:80px;">{{ trans("messages.port-address") }}</th>
										<th class="text-left" style="min-width:100px; max-width:100px;">{{ trans("messages.port-country") }}</th>
									<?php 
									}else {
										$editPermission = checkPermission(config('permission_constants.EDIT_LOCATION'));
										$deletePermission = checkPermission(config('permission_constants.DELETE_LOCATION'));?>
										<th class="text-left" style="min-width:120px; max-width:120px;">{{ trans("messages.location-name") }}</th>
										<th class="text-left" style="min-width:200px; max-width:200px;">{{ trans("messages.location-code") }}</th>
										<th class="text-left" style="min-width:80px; max-width:80px;">{{ trans("messages.location-address") }}</th>
										<th class="text-left" style="min-width:100px; max-width:100px;">{{ trans("messages.location-country") }}</th>
									<?php }?>
									<th class="text-center" style="min-width:60px; max-width:60px;">{{ trans("messages.status") }}</th>
									<?php if( ($editPermission != false) || ($deletePermission != false) ){?>
											<th class="actions-col" style="min-width:120px; max-width:120px;">{{ trans("messages.actions") }}</th>
									<?php }?>
								</tr>
							</thead>
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'warehouse-master/warehouse-master-list')
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>




<div class="modal fade bd-example-modal-lg" id="add-warehouse-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ $addPageTitle }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
				{!! Form::open(array( 'id'=> 'add-warehouse-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
				<div class="modal-body add-warehouse-modal-html">
					
				</div>
				<input type="hidden" name="record_id" value="">
				<input type="hidden" name="record_type" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button warehouse-modal-action-button" title="{{ trans('messages.submit') }}" onclick="addWarehouseModel();">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
		</div>

	</div>
</div>



<script>
var twt_record_type = "{{ isset($recordType)  ? $recordType : "" }}";

var warehouse_module_url = '{{config("constants.WAREHOUSE_MASTER_URL")}}' + '/';
function openWarehouseModel(thisitem){
	editWarehouseModel(thisitem);
}

$("#add-warehouse-master-form").validate({
	errorClass: "invalid-input",
	rules: {
		warehouse_name: {
			required: true,
			noSpace: true,
			//validateUniqueWarehouseName: true
		},
		warehouse_code: {
			required: function(){
				return  ( ( $.trim($('[name="record_type"]').val()) == "{{config('constants.PORT')}}" ) ? false : true );
			},
			noSpace: true,
			/* validateUniqueWarehouseCode: function(){
				return  ( ( $.trim($('[name="record_type"]').val()) == "{{config('constants.PORT')}}" ) ? false : true );
			}, */
		},
		warehouse_short_code:{
			required: true,
			noSpace: true
		},
		select_country_name: {
			required: true,
			noSpace: true
		},
		warehouse_mail: {
			comma_separated_email_regex : true
		}
	},
	messages: {
		warehouse_name: {
			required: function(){
				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.WAREHOUSE")}}' ) ?  "{{ trans('messages.require-warehouse-name') }}" : ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.PORT")}}' ) ? "{{ trans('messages.require-port-name') }}" : "{{ trans('messages.require-location-name') }}" ));
			} 
		},
		warehouse_code: {
			required: function(){
				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.WAREHOUSE")}}' ) ?  "{{ trans('messages.require-warehouse-code') }}" : ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.PORT")}}' ) ? "{{ trans('messages.require-port-code') }}" : "{{ trans('messages.require-location-code') }}" ));
			} 
		},
		warehouse_short_code: {
			required: function(){
				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.WAREHOUSE")}}' ) ?  "{{ trans('messages.require-warehouse-address') }}" : ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.PORT")}}' ) ? "{{ trans('messages.require-port-address') }}" : "{{ trans('messages.require-location-address') }}" ));
			} 
		},
		select_country_name: {
			required: function(){
				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.WAREHOUSE")}}' ) ?  "{{ trans('messages.required-country-name') }}" : ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.PORT")}}' ) ? "{{ trans('messages.required-country-name') }}" : "{{ trans('messages.required-country-name') }}" ));
			} 
		},

	}
});

	function addWarehouseModel(){
		var record_type = $.trim($('[name="record_type"]').val());
		var warehouse_mail = '';
		if(record_type == '{{config("constants.WAREHOUSE")}}'){
			var location_name = "{{ trans('messages.require-warehouse-name') }}";
			var location_code = "{{ trans('messages.require-warehouse-code') }}";
			var location_address = "{{ trans('messages.require-warehouse-address') }}";
			warehouse_mail = $.trim($('[name="warehouse_mail"]').val());
		}else {
			var location_name = "{{ trans('messages.require-location-name') }}";
			var location_code = "{{ trans('messages.require-location-code') }}";
			var location_address = "{{ trans('messages.require-location-address') }}";
			
		}
		
		if($('#add-warehouse-master-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var warehouse_name = $.trim($('[name="warehouse_name"]').val());
		var warehouse_code = $.trim($('[name="warehouse_code"]').val());
		var warehouse_short_code = $.trim($('[name="warehouse_short_code"]').val());
		var select_country_name = $.trim($('[name="select_country_name"]').val());
		
		
		var confirm_box = "";
        var confirm_box_msg = "";
        if(record_id == 0){
        	if(record_type == '{{config("constants.WAREHOUSE")}}'){
        		confirm_box = "{{ trans('messages.add-warehouse') }}";
        		confirm_box_msg = "{{ trans ( 'messages.confirm-add-warehouse-msg') }}";
        		
        	} else if(record_type == '{{config("constants.PORT")}}'){
        		confirm_box = "{{ trans('messages.add-port') }}";
        		confirm_box_msg = "{{ trans ( 'messages.confirm-add-port-msg') }}";
        		
            } else {
        		confirm_box = "{{ trans('messages.add-location') }}";
        		confirm_box_msg = "{{ trans ( 'messages.confirm-add-location-msg') }}";
            }
        } else {
        	if(record_type == '{{config("constants.WAREHOUSE")}}'){
        		confirm_box = "{{ trans('messages.update-warehouse') }}";
            	confirm_box_msg = "{{ trans ( 'messages.confirm-update-warehouse-msg') }}";
            	
        	} else if(record_type == '{{config("constants.PORT")}}'){
        		confirm_box = "{{ trans('messages.update-port') }}";
            	confirm_box_msg = "{{ trans ( 'messages.confirm-update-port-msg') }}";
            }else {
        		confirm_box = "{{ trans('messages.update-location') }}";
            	confirm_box_msg = "{{ trans ( 'messages.confirm-update-location-msg') }}";
            }
        	
        }  
        alertify.confirm(confirm_box,confirm_box_msg,function() {  
			$.ajax({
				type: "POST",
				dataType: "json",
				url: warehouse_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'warehouse_name':warehouse_name,'warehouse_code':warehouse_code,'warehouse_short_code':warehouse_short_code,'select_country_name':select_country_name,'warehouse_mail':warehouse_mail,'record_id':record_id,
					'record_type':record_type,'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						
						$("#add-warehouse-modal").modal('hide');
						if(record_id != '' && record_id != null){
							alertifyMessage('success',response.message);
							$(current_row).parents('.warehouse-record').html(response.data.html);
						}else{
							if(record_type == '{{config("constants.WAREHOUSE")}}'){
								$("[name='session_redirect_module_name']").val("{{ trans('messages.warehouse') }}");
							} else if(record_type == '{{config("constants.PORT")}}'){
								$("[name='session_redirect_module_name']").val("{{ trans('messages.port') }}");
							}else {
								$("[name='session_redirect_module_name']").val("{{ trans('messages.location') }}");
								
							}
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
	function editWarehouseModel(thisitem){
		current_row=thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var record_type = $.trim($(thisitem).attr('data-record-type'));
		
		$("[name='record_type']").val(record_type);
		$.ajax({
			type: "POST",
			url: warehouse_module_url + 'edit',
			data: {
				"_token": "{{ csrf_token() }}",
				record_id: record_id,
				record_type:record_type
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				
				if(record_id !="" && record_id != null){
					if(record_type == '{{config("constants.WAREHOUSE")}}'){
						var header_name = "{{ trans('messages.update-warehouse') }}";
					} else if(record_type == '{{config("constants.PORT")}}'){
						var header_name = "{{ trans('messages.update-port') }}";
					}else {
						var header_name = "{{ trans('messages.update-location') }}";
						
					}
					var button_name = "{{ trans('messages.update') }}";
					$('.add-warehouse-modal-html').html("");
					$('.add-warehouse-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("#add-warehouse-modal").find('.warehouse-modal-action-button').html(button_name);
					$('.warehouse-modal-action-button').attr('title' , "{{ trans('messages.update') }}");
					$("#add-warehouse-modal").find('.twt-modal-header-name').html(header_name);
					
				} else {
					if(record_type == '{{config("constants.WAREHOUSE")}}'){
						var header_name = "{{ trans('messages.add-warehouse') }}";
					} else if(record_type == '{{config("constants.PORT")}}'){
						var header_name = "{{ trans('messages.add-port') }}";
					}else {
						var header_name = "{{ trans('messages.add-location') }}";
					}
					var button_name = "{{ trans('messages.submit') }}" ;
					$('.add-warehouse-modal-html').html("");
					$('.add-warehouse-modal-html').html(response);
					$("[name='record_id']").val("");
					$("#add-warehouse-modal").find('.warehouse-modal-action-button').html(button_name);
					$('.warehouse-modal-action-button').attr('title' , "{{ trans('messages.submit') }}");
					$("#add-warehouse-modal").find('.twt-modal-header-name').html(header_name);
				}
				openBootstrapModal('add-warehouse-modal');
			
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function searchField(){
		var search_by_warehouse = $.trim($('[name="search_by_warehouse"]').val());
		var search_country = $.trim($('[name="search_country"]').val());
		var search_status = $.trim($('[name="search_status"]').val());
		var record_type = twt_record_type;
		var searchData = {
	            'search_by_warehouse':search_by_warehouse,
	            'search_country': search_country,
	            'search_status': search_status,
	            'record_type':record_type
	        }
	        return searchData;
	}
	function filterData(){
		var searchFieldName = searchField();

		searchAjax(warehouse_module_url + 'filter' , searchFieldName);
	}
	var paginationUrl = warehouse_module_url + 'filter'
	var warehouse_name = '<?php echo trans("messages.error-unique-warehouse-name")?>';
	var warehouse_code = '<?php echo trans("messages.error-unique-warehouse-code")?>';
	var record_type = twt_record_type;
	if(record_type == '{{config("constants.WAREHOUSE")}}'){
		warehouse_name = '<?php echo trans("messages.error-unique-warehouse-name")?>';
		warehouse_code = '<?php echo trans("messages.error-unique-warehouse-code")?>';
		
	} else if(record_type == '{{config("constants.PORT")}}'){
		warehouse_name = '<?php echo trans("messages.error-unique-port-name")?>';
		warehouse_code = '<?php echo trans("messages.error-unique-port-code")?>';
	}else {
		warehouse_name = '<?php echo trans("messages.error-unique-location-name")?>';
		warehouse_code = '<?php echo trans("messages.error-unique-location-code")?>';
	}
	<?php /*?>
	$.validator.addMethod("validateUniqueWarehouseName", function (value, element) {
		
		var result = true;
		
		$.ajax({
			type: "POST",
			async: false,
			url: warehouse_module_url +'checkUniqueWarehouseName',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'warehouse_name': $.trim($("[name='warehouse_name']").val()),'record_type': $.trim($("[name='record_type']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
		
	}, warehouse_name); */?>
	
	$.validator.addMethod("validateUniqueWarehouseCode", function (value, element) {
		 
		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: warehouse_module_url +'checkUniqueWarehouseCode',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'warehouse_code': $.trim($("[name='warehouse_code']").val()),'record_type': $.trim($("[name='record_type']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
	}, warehouse_code);
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection