<div class="row align-items-center">
 	<div class="col-md-4">
		<div class="form-group">
		<label for="search_warehouse" class="control-label">{{ trans('messages.warehouse') }}</label>
		<select class="form-control" name="search_warehouse" disabled>
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
	<div class="col-md-4">
		<div class="form-group">
			<label class="control-label" for="search_month">{{ trans("messages.month") }}</label>
			<input type="text" name="search_month" value="{{date('m-Y')}}" class="form-control" placeholder="{{ trans('messages.month') }}">
		</div>
	</div>
	<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-3">
		<button type="button" title="{{ trans('messages.search') }}" onclick="filterHistoryData();" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
		<button type="button" title="{{ trans('messages.reset') }}" onclick="resetHistoryData();" class="btn btn-outline-secondary">{{ trans("messages.reset") }}</button>
	</div>
</div>
<div class="row mt-4">
	<div class="col-12">
		<div class="table-responsive table-head-sticky table-height-sticky">
			<table class="table table-hover table-bordered table-sm pb-4">
				<thead>
					<tr>
						<th class="sr-col">{{ trans("messages.sr-no") }}</th>
						<th style="max-width:100px;min-width:100px;">{{ trans("messages.date") }}</th>
						<th style="max-width:100px;min-width:100px;">{{ trans("messages.pallet-limit") }}</th>
						<th style="max-width:120px;min-width:120px;">Pallet Forecasted</th>
						<th style="max-width:120px;min-width:120px;">Pallet Received</th>
					</tr>
				</thead>
				<tbody class="history-ajax-view">
					@include( config('constants.AJAX_VIEW_FOLDER') . 'warehouse-pallet-limit/warehouse-pallet-history-list')
				</tbody>
			</table>                           
		</div>
	</div>
</div>              
<script type="text/javascript">

$(document).ready(function() {
	$('[name="search_month"]').datetimepicker({
        useCurrent: false,
        viewMode: 'days',
        ignoreReadonly: true,
        widgetPositioning: {
            vertical: 'bottom'
        },
        format: 'MM-YYYY',
    });
});

function resetHistoryData(){
	$('[name="search_month"]').val("{{date('m-Y')}}");
	filterHistoryData();
}

function filterHistoryData(){
	var searchFieldName = { 'search_warehouse_name' :  $.trim($('[name="search_warehouse"]').val()) , 'search_month' : $.trim($('[name="search_month"]').val())  };
	$.ajax({
		type: "POST",
		url: module_url + '/historyFilter',
		async: false,
		data: searchFieldName,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		beforeSend: function() {
			showLoader();
		},
		success: function(response) {
			hideLoader();
			$(".history-ajax-view").html("");
			$(".history-ajax-view").html(response);
		},
		error: function() {
			hideLoader();
		}
	});
}
</script>         