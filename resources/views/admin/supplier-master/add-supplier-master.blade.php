@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ config('constants.SUPPLIER_MASTER_URL')}}" class="category-add-link">{{ trans("messages.supplier-master") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card card-body mb-3">
                {!! Form::open(array( 'id '=> 'add-supplier-master-form' , 'method' => 'post' ,  'url' => 'supplier-master/add')) !!}
				 	
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="supplier_partner_name">{{ trans("messages.supplier-name") }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control"  name="supplier_partner_name" placeholder="{{ trans('messages.supplier-name') }}" autofocus="" value="{{old('supplier_partner_name',  ( (isset($recordInfo) && (!empty($recordInfo->v_supplier_name))) ?  $recordInfo->v_supplier_name : '' ) )}}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group pb-3 pt-3">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <span class="partner-tilte">
                                            {{ trans("messages.add-multi-address") }}
                                        </span>

                                    </div>
                                    <div class="card-body supplier">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>{{ trans("messages.sr-no") }}</th>
                                                        <?php /* ?>	
                                                        <th>{{ trans("messages.supplier-code") }} <span class="star">*</span></th>
                                                        <?php */ ?>
                                                        <th style="min-width:130px; ">{{ trans("messages.type") }}</th>
                                                        <th>{{ trans("messages.supplier-address") }} <span class="star">*</span></th>
                                                        <th>{{ trans("messages.supplier-country") }}<span class="star">*</span></th>
                                                        <th>{{ trans("messages.contact-person-name") }}</th>
                                                        <th>{{ trans("messages.contact-mobile") }}</th>
                                                        <th>{{ trans("messages.contact-email") }}</th>
                                                        <th>{{ trans("messages.timings") }}</th>
                                                        <th>{{ trans("messages.action") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="supplier-master-tbody">
                                                <?php 
                                                if(!empty($recordDetails)){
                                                	foreach ($recordDetails  as $countKey => $recordDetail){
                                                		$columIndex  = ( $countKey +  1 );
                                                		?>
                                                		<tr>
                                                        <td class="table-index text-center"><?php echo $columIndex ?></td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control supplier-code-row" onchange="checkSupplierCode(this);" name="edit_supplier_code_<?php echo $recordDetail->supplier_detail_id ?>" value="<?php echo (isset($recordDetail->v_supplier_code) ? $recordDetail->v_supplier_code : '' ); ?>"></td>
                                                        <?php */ ?>
                                                        <td>
                                                            <select name="edit_registered_collection_{{ $recordDetail->supplier_detail_id }}" class="form-control registered-collection-row">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                @if(!empty($registeredCollectionInfo))
                                                                	@foreach($registeredCollectionInfo as $key => $registeredCollection)
                                                                		{{ $selected = ''; }}
                                                                		@if( isset($recordDetail->e_record_status) && ( $recordDetail->e_record_status == $key ) )
                                                                			{{ $selected = "selected='selected'"; }}
                                                                		@endif
                                                                		<option value="{{ $key }}" {{ $selected }} data-registerd-status="{{ $key }}">{{ $registeredCollection }}</option>
                                                					@endforeach
                                                                @endif
                                                         	</select>
                                                        </td>
                                                        <td><textarea class="form-control supplier-address-row" name="edit_supplier_address_<?php echo $recordDetail->supplier_detail_id ?>" rows="1"><?php echo (isset($recordDetail->v_supplier_address) ? $recordDetail->v_supplier_address : '' ); ?></textarea></td>
                                                        <td>
                                                            <select name="edit_supplier_country_<?php echo $recordDetail->supplier_detail_id ?>" class="form-control supplier-country-row" onchange="checkUniqueCountryRegister(this)">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                <?php 
                                                				if(count($countryRecordDetails) > 0){
                                                					foreach ($countryRecordDetails as $countryRecordDetail){
                                                						$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
                                                						$selected = '';
                                                						if( isset($recordDetail->i_country_id) && ( $recordDetail->i_country_id == $countryRecordDetail->i_id ) ){
                                                							$selected = "selected='selected'";
                                                						}
                                                						?>
                                                						<option value="{{ $encodeCountryId }}" {{ $selected }} data-country-id="{{ $countryRecordDetail->i_id }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>
                                                						<?php 
                                                						}
                                                					}
                                                				?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control contact-person-name-row" name="edit_supplier_contact_person_name_<?php echo $recordDetail->supplier_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_person_name) ? $recordDetail->v_contact_person_name : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="edit_supplier_contact_mobile_<?php echo $recordDetail->supplier_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_mobile) ? $recordDetail->v_contact_mobile : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="edit_supplier_contact_email_<?php echo $recordDetail->supplier_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_email) ? $recordDetail->v_contact_email : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control" name="edit_timings_{{ $recordDetail->supplier_detail_id }}" value="{{ (isset($recordDetail->v_timings) ? $recordDetail->v_timings : '' ); }}"></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                		<?php
                                                	}
                                                } else {
                                                	?>
                                                    <tr>
                                                        <td class="table-index text-center">1</td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control supplier-code-row" onchange="checkSupplierCode(this);" name="supplier_code_1"></td>
                                                        <?php */ ?>
                                                        <td>
                                                            <select name="registered_collection_1" class="form-control registered-collection-row">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                @if(!empty($registeredCollectionInfo))
                                                                	@foreach($registeredCollectionInfo as $key => $registeredCollection)
                                                                		<option value="{{ $key }}" data-registerd-status="{{ $key }}">{{ $registeredCollection }}</option>
                                                					@endforeach
                                                                @endif
                                                         	</select>
                                                        </td>
                                                        <td><textarea class="form-control supplier-address-row" name="supplier_address_1" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="supplier_country_1" class="form-control supplier-country-row" onchange="checkUniqueCountryRegister(this)">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                <?php 
                                                				if(count($countryRecordDetails) > 0){
                                                					foreach ($countryRecordDetails as $countryRecordDetail){
                                                						$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
                                                						?>
                                                						<option value="{{ $encodeCountryId }}" data-country-id="{{ $countryRecordDetail->i_id }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>
                                                						<?php 
                                                						}
                                                					}
                                                				?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control contact-person-name-row" name="supplier_contact_person_name_1" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="supplier_contact_mobile_1" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="supplier_contact_email_1" value=""></td>
                                                        <td><input type="text" class="form-control" name="timings_1" value=""></td>
                                                       
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="table-index text-center">2</td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control supplier-code-row" onchange="checkSupplierCode(this);" name="supplier_code_2"></td>
                                                       	<?php */ ?>	
                                                       	<td>
                                                            <select name="registered_collection_2" class="form-control registered-collection-row">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                @if(!empty($registeredCollectionInfo))
                                                                	@foreach($registeredCollectionInfo as $key => $registeredCollection)
                                                                		<option value="{{ $key }}" data-registerd-status="{{ $key }}">{{ $registeredCollection }}</option>
                                                					@endforeach
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td><textarea class="form-control supplier-address-row" name="supplier_address_2" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="supplier_country_2" class="form-control supplier-country-row" onchange="checkUniqueCountryRegister(this)">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                <?php 
                                                				if(count($countryRecordDetails) > 0){
                                                					foreach ($countryRecordDetails as $countryRecordDetail){
                                                						$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
                                                						?>
                                                						<option value="{{ $encodeCountryId }}" data-country-id="{{ $countryRecordDetail->i_id }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>
                                                						<?php 
                                                						}
                                                					}
                                                				?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control contact-person-name-row" name="supplier_contact_person_name_2" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="supplier_contact_mobile_2" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="supplier_contact_email_2" value=""></td>
                                                        <td><input type="text" class="form-control" name="timings_2" value=""></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewSupplier(this);"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 submit-sticky">
                         <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
                            <input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
                             <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('update') }}">{{ trans("messages.update") }}</button>
                            
                            <?php } else {?>
                             <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                            
                            <?php }?>
                           <a href="{{ config('constants.SUPPLIER_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                        </div>
                    </div>
               		<input type="hidden" name="supplier_count" value="">
                  {!! Form::close() !!}
            </div>
        </div>
    </section>
</main>
<script>
var supplier_count = 2;
<?php if(!empty($recordDetails)){?>
supplier_count = '<?php echo count($recordDetails)?>';

<?php 
}?>
var unique_supplier_code = true;
var unique_country_wise_registerd = true;
      $("#add-supplier-master-form").validate({
        errorClass: "invalid-input",
        rules: {
        	supplier_partner_name: {
                required: true,
                validateUniqueSupplierName:true
            },
        },
        messages: {
        	supplier_partner_name: {
                required: "{{ trans('messages.require-supplier-name') }}"
            },
        },
        submitHandler: function(form) {
        	var supplier_code_status = false;
            var supplier_address_status = false;
            var supplier_country_status = false;

            var unique_supplier_code = true;
            var unique_country_wise_registerd = true;
            var unique_supplier_array = [];
            var unique_country_type = [];
            
            
            $('.supplier-master-tbody tr').each(function(){
            	var supplier_code = $.trim($(this).find('.supplier-code-row').val());
            	var supplier_address = $.trim($(this).find('.supplier-address-row').val());
            	var supplier_country = $.trim($(this).find('.supplier-country-row').val());
            	var country_id = $.trim($(this).find('.supplier-country-row option:selected').attr('data-country-id'));
            	var registered_collection = $.trim($(this).find('.registered-collection-row').val());
            	
            	if(supplier_address != "" && supplier_address != null){
            		supplier_address_status = true;
            		/* if( ( supplier_address == "" || supplier_address == null ) && (supplier_address_status != true) ){
            			$.trim($(this).find('.supplier-address-row').focus());
            			supplier_address_status = true;
                	} */
            		if( ( supplier_country == "" || supplier_country == null ) && ( supplier_country_status != true ) ){
						$.trim($(this).find('.supplier-country-row').focus());
						supplier_country_status = true;
					}

            		/* if( unique_supplier_code != false ){
						if( $.inArray( supplier_code ,unique_supplier_array  ) == -1 ){
							unique_supplier_array.push(supplier_code);
						} else {
							unique_supplier_code = false;
							$(this).find('.supplier-code-row').focus()
						}            		
            		} */
            		
            		if(( registered_collection !="" && registered_collection != null) && ( registered_collection == "{{ config('constants.REGISTERED_STATUS') }}") ){
            			if( unique_country_wise_registerd != false ){
	            			if( $.inArray( country_id ,unique_country_type  ) == -1 ){
	            				unique_country_type.push(country_id);
							}else {
								unique_country_wise_registerd = false;
							}  
            			} 
                	}
                }
        	})
        	/* if( unique_supplier_code != true ){
        		alertifyMessage("error","{{ trans('messages.error-unique-supplier-code') }} ");
        		return false;
            } */
            if( unique_country_wise_registerd != true ){
        		alertifyMessage("error","{{ trans('messages.error-unique-country') }} ");
        		return false;
            }
        	if( supplier_address_status != true ){
        		$.trim($('.supplier-code-row:first').focus());
           		alertifyMessage("error","{{ trans('messages.required-atleast-one-record') }} ");
           		return false;
            }
            /* if( supplier_address_status != false ){
            	alertifyMessage("error","{{ trans('messages.require-supplier-address') }} ");
            	return false;
            } */
            if( supplier_country_status != false ){
            	alertifyMessage("error","{{ trans('messages.require-supplier-country') }} ");
            	return false;
            } 
            var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
		          	confirm_box = "{{ trans('messages.update-supplier') }}";
		        	confirm_box_msg = "{{ trans ( 'messages.confirm-update-supplier-msg') }}";
		           
            <?php } else {?>
			            confirm_box = "{{ trans('messages.add-supplier') }}";
			        	confirm_box_msg = "{{ trans ( 'messages.confirm-add-supplier-msg') }}";
			           
            <?php }?>
           
             alertify.confirm(confirm_box,confirm_box_msg,function() {
				$("[name='supplier_count']").val(supplier_count);
        		showLoader()
                form.submit();
        	},function() {});
        }
    });
    var supplier_count = supplier_count;
    function addNewSupplier(thisitem){
    	supplier_count++;
		var html = '';
		html += '<tr>';
		html += '<td class="table-index text-center">'+supplier_count+'</td>';
		//html += '<td><input type="text" class="form-control supplier-code-row" onchange="checkSupplierCode(this);" name="supplier_code_'+supplier_count+'"></td>';
		html += '<td>';
		html += '<select name="registered_collection_'+supplier_count+'" class="form-control registered-collection-row">';
		html += '<option value="">{{ trans('messages.select') }}</option>';
      	@if(!empty($registeredCollectionInfo))
        	@foreach($registeredCollectionInfo as $key => $registeredCollection)
        		html += '<option value="{{ $key }}" data-registerd-status="{{ $key }}">{{ $registeredCollection }}</option>';
      		@endforeach
       	@endif
     	html += '</select>';
       	html += '</td>';
		html += '<td><textarea class="form-control supplier-address-row" name="supplier_address_'+supplier_count+'" rows="1"></textarea></td>';
		html += '<td>';
		html += '<select name="supplier_country_'+supplier_count+'" class="form-control supplier-country-row" onchange="checkUniqueCountryRegister(this)">';
		html += '<option value="">{{ trans('messages.select') }}</option>';
        <?php 
		if(count($countryRecordDetails) > 0){
			foreach ($countryRecordDetails as $countryRecordDetail){
				$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
				?>
				html += '<option value="{{ $encodeCountryId }}" data-country-id="{{ $countryRecordDetail->i_id }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>';
				<?php 
			}
		}
		?>
		html += '</select>';
		html += '</td>';
		html += '<td><input type="text" class="form-control contact-person-name-row" name="supplier_contact_person_name_'+supplier_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control contact-person-mobile-row" name="supplier_contact_mobile_'+supplier_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control contact-person-email-row" name="supplier_contact_email_'+supplier_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control" name="timings_'+supplier_count+'" value=""></td>';
		html += '<td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
		html += '</tr>';

		if( $('.supplier-master-tbody').find('tr').length > 0 ){
			$(html).insertAfter($('.supplier-master-tbody').find('tr:last'));	
		} else {
			$('.supplier-master-tbody').html(html);
		}
		reindexTable('supplier-master-tbody');
     }
    
    var supplier_module_url = '{{config("constants.SUPPLIER_MASTER_URL")}}' + '/';  
    $.validator.addMethod("validateUniqueSupplierName", function (value, element) {
   	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: supplier_module_url +'checkUniqueSupplierName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'supplier_partner_name': $.trim($("[name='supplier_partner_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
    }, '<?php echo trans("messages.error-unique-supplier-name")?>');  

    function checkSupplierCode(thisitem){
    	var supplier_code = $.trim($(thisitem).val());
		if( supplier_code != "" && supplier_code != null ){
			$.ajax({
	    		type: "POST",
	    		async: false,
	    		url: supplier_module_url +'checkUniqueSupplierCode',
	    		dataType: "json",
	    		data: {
	    			"_token": "{{ csrf_token() }}",
	    			'supplier_code': supplier_code ,'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
	    		},
	    		beforeSend: function() {
	    			
	    		},
	    		success: function (response) {
	    			if (response.status_code == 101) {
	    				$(thisitem).val("");
	    				unique_supplier_code = false;
	    				alertifyMessage("error","{{ trans('messages.error-unique-supplier-code') }} ");
	    			}  else {
	    				unique_supplier_code = true;
		    		}
	    		}
	    	});
		}
   	}
   	function checkUniqueCountryRegister(thisitem){
   		
   		var country_id = $.trim($(thisitem).parents('tr').find('.supplier-country-row option:selected').attr('data-country-id'));
   		var registered_collection = $.trim($(thisitem).parents('tr').find('.registered-collection-row option:selected').attr('data-registerd-status'));
		if((registered_collection !="" && registered_collection != null) && (registered_collection == "{{ config('constants.REGISTERED_STATUS') }}")){
       		$.ajax({
	    		type: "POST",
	    		async: false,
	    		url: supplier_module_url +'checkUniqueSupplierType',
	    		dataType: "json",
	    		data: {
	    			"_token": "{{ csrf_token() }}",
	    			'registered_collection': registered_collection ,'country_id':country_id,'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
	    		},
	    		beforeSend: function() {
	    			
	    		},
	    		success: function (response) {
	    			if (response.status_code == 101) {
	    				$(thisitem).val("");
	    				unique_country_wise_registerd = false;
	    				alertifyMessage("error","{{ trans('messages.error-unique-country') }} ");
	    			}  else {
	    				unique_country_wise_registerd = true;
		    		}
	    		}
	    	});
   		}
     }
</script>
@endsection