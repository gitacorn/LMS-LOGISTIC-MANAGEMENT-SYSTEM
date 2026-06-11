<div class="modal fade bd-example-modal-lg" id="add-lookup-modal"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
             	{!! Form::open(array( 'id '=> 'add-lookup-form' , 'method' => 'post' ,  'url' => '')) !!}
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body add-lookup-modal-html">
                	<div class="row">
						<div class="col-md-12">
					    	<div class="form-group">
						    	<?php 
						    	$lookupLabelPlaceholder = trans('messages.value');
						    	if(isset($moduleName) && !empty($moduleName)){
						    		switch ($moduleName){
						    			case config('constants.BOOKING_PORTAL_LOOKUP'):
						    				$lookupLabelPlaceholder = trans('messages.booking-portal-name');
						    				break;
					    				case config('constants.DAILY_MAIL_LOOKUP'):
					    					$lookupLabelPlaceholder = trans('messages.email');
					    					break;
						    		}
						    	}
						    	?>
					        	<label class="control-label">{{ $lookupLabelPlaceholder }}<span class="text-danger">*</span></label>
					            <input type="text" name="module_value" class="form-control" placeholder="{{ $lookupLabelPlaceholder }}" >
					       	</div>
					   	</div>
					</div>
                </div>
                <input type="hidden" name="lookup_module_name" value="">
                <input type="hidden" name="lookup_module_record_id" value="">
                <input type="hidden" name="action_type" value="crud">
                <input type="hidden" name="request_type" value="">
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn bg-theme text-white action-button lookup-modal-action-button" onclick="addLookup()" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
var unique_lookup_value_msg = '';
$.validator.addMethod("validateUniqueLookupValue", function(value, element) {
    var result = true;
    
    $.ajax({
        type: "POST",
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: site_url + 'check-unique-lookup-value',
        dataType: "json",
        data: {
            "_token": "{{ csrf_token() }}",
            lookup_module_name: $.trim($("[name='lookup_module_name']").val()),
			module_value: $.trim($("[name='module_value']").val()),
			record_id : $.trim($("[name='lookup_module_record_id']").val())
        },
        success: function(response) {
        	unique_lookup_value_msg = response.message;
            if (response.status_code != 1) {
            	result = false;
            }
        }
    });
    
    return result;
}, function (params, element) {
	return unique_lookup_value_msg;
} );
</script>