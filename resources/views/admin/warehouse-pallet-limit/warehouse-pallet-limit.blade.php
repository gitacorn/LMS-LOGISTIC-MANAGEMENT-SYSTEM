@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.warehouse-pallet-limit") }}</h1>
    </div>
	{{ Wild_tiger::readMessage() }}
    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card card-body mb-3">
                {!! Form::open(array( 'id '=> 'warehouse-filter-form' , 'method' => 'post' , 'url' => 'warehouse-pallet-limit/add')) !!}
                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                            <label for="warehouse_name" class="control-label">{{ trans('messages.warehouse') }}<span class="text-danger">*</span></label>
                                <select class="form-control select2" name="warehouse_name" onchange="filterData(this);showHideSubmitDiv(this);" {{strtolower(session()->get('role')) == strtolower(config('constants.ROLE_USER')) ? 'disabled' : ''}}>
                                    <option value="">{{ trans("messages.select") }}</option>
                                    	@if(!empty($warehouseDetails))
											@foreach ($warehouseDetails as $warehouseDetail)
												{{ $encodeId = Wild_tiger::encode($warehouseDetail->i_id);}}
												{{ $selected = ''; }}
												@if( isset($wareHouseId) && ( $wareHouseId == $warehouseDetail->i_id) )
                                        			{{ $selected = "selected='selected'"; }}
                                        		@endif
												<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        	@endforeach
	                                 	@endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="d-flex align-items-start">
                                <button type="button" onclick="openHistoryModal();" class="btn btn bg-theme text-white mr-3 mt-4" title="{{ trans('messages.view-history') }}">{{ trans("messages.view-history") }}</button>
                                <div class="form-row w-100">
                                    <div class="col-auto pr-2">
                                        <label class="control-label d-block mb-1">From Date</label>
                                        <input type="text" name="from_date_export" class="form-control" placeholder="DD-MM-YYYY" autocomplete="off" style="max-width:150px;">
                                    </div>
                                    <div class="col-auto pr-2">
                                        <label class="control-label d-block mb-1">To Date</label>
                                        <input type="text" name="to_date_export" class="form-control" placeholder="DD-MM-YYYY" autocomplete="off" style="max-width:150px;">
                                    </div>
                                    <div class="col-auto">
                                        <label class="d-block mb-1">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-success" title="Export Excel" onclick="exportExcel();">Export Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
							<div class="col-lg-6 mt-4">
								<div class="table-responsive ajax-view">
								</div>
							</div>
							<div class="col-md-12 submit-sticky pallet-submit-div"  {{ strtolower(session()->get('role')) == strtolower(config('constants.ROLE_USER')) ? '' : 'style=display:none;'}} >
								<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
							</div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>

    
    <div class="modal fade bd-example-modal-lg" id="warehouse-pallet-limit-history" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.warehouse-pallet-limit-history') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body warehouse-pallet-history-body">
                   
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script>

		$(document).ready(function(){
			filterData();
		});
	
        $("#warehouse-filter-form").validate({
            errorClass: "invalid-input",
            rules: {
                warehouse_name: {
                    required: true
                },
            },
            messages: {
                warehouse_name: {
                    required: "{{ trans('messages.require-select-warehouse-name') }}"
                },
            },
            submitHandler: function(form) {
                showLoader()
                form.submit();
            }
        });
		var module_url = '{{config("constants.WAREHOUSE_PALLET_MASTER_URL")}}';
        function filterData(thisitem){
          var searchFieldName = { 'search_warehouse_name' :  $.trim($('[name="warehouse_name"]').val()) };
          searchAjax(module_url + '/filter' , searchFieldName);
        }

		function openHistoryModal(){
			$.ajax({
				type: "POST",
				url: module_url + '/showHistoryModal',
				data: {
					"_token": "{{ csrf_token() }}",
					'warehouse_name': $.trim($('[name="warehouse_name"]').val())
				},
				beforeSend: function() {
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if(response !="" && response != null){
						$('.warehouse-pallet-history-body').html(response);
					}
					openBootstrapModal('warehouse-pallet-limit-history');
				},
				error: function() {
					hideLoader();
				}
			});
		}        function showHideSubmitDiv(thisitem){
            var selected_value = $.trim($(thisitem).val());
            if( selected_value !== null && selected_value !== '' ){
                $('.pallet-submit-div').show();
            } else {
                $('.pallet-submit-div').hide();
            }
        }
        // Export Excel: JS-driven POST to avoid submitting the main form
        function exportExcel(){
            var fromD = $.trim($('[name="from_date_export"]').val());
            var toD = $.trim($('[name="to_date_export"]').val());
            if(fromD === '' || toD === ''){
                alert('Please select From and To dates to export.');
                return false;
            }
            var wh = $.trim($('[name="warehouse_name"]').val()); // optional
            // Build a temporary form and submit
            var $form = $('<form>', { method: 'POST', action: '{{ url('warehouse-pallet-limit/export') }}' });
            $form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
            $form.append($('<input>', { type: 'hidden', name: 'warehouse_name_export', value: wh }));
            $form.append($('<input>', { type: 'hidden', name: 'from_date', value: fromD }));
            $form.append($('<input>', { type: 'hidden', name: 'to_date', value: toD }));
            $('body').append($form);
            $form.submit();
        }
        // Initialize datepickers for export filters
        $(function(){
            if(typeof $.fn.datetimepicker === 'function'){
                $('[name="from_date_export"],[name="to_date_export"]').datetimepicker({
                    useCurrent: false,
                    ignoreReadonly: true,
                    format: 'DD-MM-YYYY',
                    widgetPositioning: { vertical: 'bottom' }
                });
            }
        });
        
    </script>
</main>
@endsection