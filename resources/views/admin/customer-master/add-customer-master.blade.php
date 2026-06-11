@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ config('constants.CUSTOMER_MASTER_URL')}}" class="category-add-link">{{ trans("messages.customer-master") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card card-body mb-3">
                {!! Form::open(array( 'id '=> 'add-customer-master-form' , 'method' => 'post' ,  'url' => 'customer-master/add')) !!}
				 	
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="customer_partner_name">{{ trans("messages.customer-name") }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control"  name="customer_partner_name" placeholder="{{ trans('messages.customer-name') }}" autofocus="" value="{{old('customer_partner_name',  ( (isset($recordInfo) && (!empty($recordInfo->v_customer_name))) ?  $recordInfo->v_customer_name : '' ) )}}">
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
                                    <div class="card-body customer">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>{{ trans("messages.sr-no") }}</th>
                                                        <?php /* ?>	
                                                        <th>{{ trans("messages.customer-code") }} <span class="star">*</span></th>
                                                        <?php */ ?>
                                                        <th>{{ trans("messages.customer-address") }} <span class="star">*</span></th>
                                                        <th>{{ trans("messages.customer-country") }}<span class="star">*</span></th>
                                                        <th>{{ trans("messages.contact-person-name") }}</th>
                                                        <th>{{ trans("messages.contact-mobile") }}</th>
                                                        <th>{{ trans("messages.contact-email") }}</th>
                                                        <th>{{ trans("messages.action") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="customer-master-tbody">
                                                <?php 
                                                if(!empty($recordDetails)){
                                                	foreach ($recordDetails  as $countKey => $recordDetail){
                                                		$columIndex  = ( $countKey +  1 );
                                                		?>
                                                		<tr>
                                                        <td class="table-index text-center"><?php echo $columIndex ?></td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control customer-code-row" onchange="checkCustomerCode(this);" name="edit_customer_code_<?php echo $recordDetail->customer_detail_id ?>" value="<?php echo (isset($recordDetail->v_customer_code) ? $recordDetail->v_customer_code : '' ); ?>"></td>
                                                        <?php */ ?>
                                                        <td><textarea class="form-control customer-address-row" name="edit_customer_address_<?php echo $recordDetail->customer_detail_id ?>" rows="1"><?php echo (isset($recordDetail->v_customer_address) ? $recordDetail->v_customer_address : '' ); ?></textarea></td>
                                                        <td>
                                                            <select name="edit_customer_country_<?php echo $recordDetail->customer_detail_id ?>" class="form-control customer-country-row">
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
                                                						<option value="{{ $encodeCountryId }}" {{ $selected }}>{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>
                                                						<?php 
                                                						}
                                                					}
                                                				?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control contact-person-name-row" name="edit_customer_contact_person_name_<?php echo $recordDetail->customer_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_person_name) ? $recordDetail->v_contact_person_name : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="edit_customer_contact_mobile_<?php echo $recordDetail->customer_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_mobile) ? $recordDetail->v_contact_mobile : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="edit_customer_contact_email_<?php echo $recordDetail->customer_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_email) ? $recordDetail->v_contact_email : '' ); ?>"></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                		<?php
                                                	}
                                                } else {
                                                	?>
                                                    <tr>
                                                        <td class="table-index text-center">1</td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control customer-code-row" onchange="checkCustomerCode(this);" name="customer_code_1"></td>
                                                        <?php */ ?>
                                                        <td><textarea class="form-control customer-address-row" name="customer_address_1" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="customer_country_1" class="form-control customer-country-row">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                <?php 
                                                				if(count($countryRecordDetails) > 0){
                                                					foreach ($countryRecordDetails as $countryRecordDetail){
                                                						$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
                                                						?>
                                                						<option value="{{ $encodeCountryId }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>
                                                						<?php 
                                                						}
                                                					}
                                                				?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control contact-person-name-row" name="customer_contact_person_name_1" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="customer_contact_mobile_1" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="customer_contact_email_1" value=""></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="table-index text-center">2</td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control customer-code-row" onchange="checkCustomerCode(this);" name="customer_code_2"></td>
                                                        <?php */ ?>
                                                        <td><textarea class="form-control customer-address-row" name="customer_address_2" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="customer_country_2" class="form-control customer-country-row">
                                                                <option value="">{{ trans('messages.select') }}</option>
                                                                <?php 
                                                				if(count($countryRecordDetails) > 0){
                                                					foreach ($countryRecordDetails as $countryRecordDetail){
                                                						$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
                                                						?>
                                                						<option value="{{ $encodeCountryId }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>
                                                						<?php 
                                                						}
                                                					}
                                                				?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control contact-person-name-row" name="customer_contact_person_name_2" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="customer_contact_mobile_2" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="customer_contact_email_2" value=""></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewCustomer(this);"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
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
                           <a href="{{ config('constants.CUSTOMER_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                        </div>
                    </div>
               		<input type="hidden" name="customer_count" value="">
                  {!! Form::close() !!}
            </div>
        </div>
    </section>
</main>
<script>
var unique_customer_code = true;
      $("#add-customer-master-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
        rules: {
        	customer_partner_name: {
                required: true,
                validateUniqueCustomerName:true
            },
        },
        messages: {
        	customer_partner_name: {
                required: "{{ trans('messages.require-customer-name') }}"
            },
        },
        submitHandler: function(form) {
        	var customer_code_status = false;
            var customer_address_status = false;
            var customer_country_status = false;

            var unique_customer_code = true;
            var unique_customer_array = [];
            
            $('.customer-master-tbody tr').each(function(){
            	var customer_code = $.trim($(this).find('.customer-code-row').val());
            	var customer_address = $.trim($(this).find('.customer-address-row').val());
            	var customer_country = $.trim($(this).find('.customer-country-row').val());
				
            	if(customer_address != "" && customer_address != null){
            		customer_address_status = true;
            		/* if( ( customer_address == "" || customer_address == null ) && (customer_address_status != true) ){
            			$.trim($(this).find('.customer-address-row').focus());
            			customer_address_status = true;
                	} */
            		if( ( customer_country == "" || customer_country == null ) && ( customer_country_status != true ) ){
						$.trim($(this).find('.customer-country-row').focus());
						customer_country_status = true;
					}

            		/* if( unique_customer_code != false ){
						if( $.inArray( customer_code ,unique_customer_array  ) == -1 ){
							unique_customer_array.push(customer_code);
						} else {
							unique_customer_code = false;
							$(this).find('.customer-code-row').focus()
						}            		
            		} */
                }
        	})
        	
        	/* if( unique_customer_code != true ){
        		alertifyMessage("error","{{ trans('messages.error-unique-customer-code') }} ");
        		return false;
            }
        	
        	if( customer_code_status != true ){
        		$.trim($('.customer-code-row:first').focus());
           		alertifyMessage("error","{{ trans('messages.required-atleast-one-record') }} ");
           		return false;
            } */

			console.log("customer_address_status");
            console.log(customer_address_status);
            
            if( customer_address_status != true ){
            	alertifyMessage("error","{{ trans('messages.require-customer-address') }} ");
            	return false;
            }
            if( customer_country_status != false ){
            	alertifyMessage("error","{{ trans('messages.require-customer-country') }} ");
            	return false;
            } 
            var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
		          	confirm_box = "{{ trans('messages.update-customer') }}";
		        	confirm_box_msg = "{{ trans ( 'messages.confirm-update-customer-msg') }}";
		           
            <?php } else {?>
			            confirm_box = "{{ trans('messages.add-customer') }}";
			        	confirm_box_msg = "{{ trans ( 'messages.confirm-add-customer-msg') }}";
			           
            <?php }?>
           
             alertify.confirm(confirm_box,confirm_box_msg,function() {
				$("[name='customer_count']").val(customer_count);
        		showLoader()
                form.submit();
        	},function() {});
        }
    });
    var customer_count = 2;
    function addNewCustomer(thisitem){
    	customer_count++;
		var html = '';
		html += '<tr>';
		html += '<td class="table-index text-center">'+customer_count+'</td>';
		//html += '<td><input type="text" class="form-control customer-code-row" onchange="checkCustomerCode(this);" name="customer_code_'+customer_count+'"></td>';
		html += '<td><textarea class="form-control customer-address-row" name="customer_address_'+customer_count+'" rows="1"></textarea></td>';
		html += '<td>';
		html += '<select name="customer_country_'+customer_count+'" class="form-control customer-country-row">';
		html += '<option value="">{{ trans('messages.select') }}</option>';
        <?php 
		if(count($countryRecordDetails) > 0){
			foreach ($countryRecordDetails as $countryRecordDetail){
				$encodeCountryId  = Wild_tiger::encode($countryRecordDetail->i_id);
				?>
				html += '<option value="{{ $encodeCountryId }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name : '' ) }}</option>';
				<?php 
			}
		}
		?>
		html += '</select>';
		html += '</td>';
		html += '<td><input type="text" class="form-control contact-person-name-row" name="customer_contact_person_name_'+customer_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control contact-person-mobile-row" name="customer_contact_mobile_'+customer_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control contact-person-email-row" name="customer_contact_email_'+customer_count+'" value=""></td>';
		html += '<td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
		html += '</tr>';

		if( $('.customer-master-tbody').find('tr').length > 0 ){
			$(html).insertAfter($('.customer-master-tbody').find('tr:last'));	
		} else {
			$('.customer-master-tbody').html(html);
		}
		reindexTable('customer-master-tbody');
     }
    
    var customer_module_url = '{{config("constants.CUSTOMER_MASTER_URL")}}' + '/';  
    $.validator.addMethod("validateUniqueCustomerName", function (value, element) {
   	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: customer_module_url +'checkUniqueCustomerName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'customer_partner_name': $.trim($("[name='customer_partner_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
    }, '<?php echo trans("messages.error-unique-customer-name")?>'); 

    function checkCustomerCode(thisitem) {

		var customer_code = $.trim($(thisitem).val());
		if( customer_code != "" && customer_code != null ){
			$.ajax({
	    		type: "POST",
	    		async: false,
	    		url: customer_module_url +'checkUniqueCustomerCode',
	    		dataType: "json",
	    		data: {
	    			"_token": "{{ csrf_token() }}",
	    			'customer_code': customer_code ,'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
	    		},
	    		beforeSend: function() {
	    			
	    		},
	    		success: function (response) {
	    			if (response.status_code == 101) {
	    				$(thisitem).val("");
	    				unique_customer_code = false;
	    				alertifyMessage("error","{{ trans('messages.error-unique-customer-code') }} ");
	    			}  else {
	    				unique_customer_code = true;
		    		}
	    		}
	    	});
		}

    } 
</script>
@endsection