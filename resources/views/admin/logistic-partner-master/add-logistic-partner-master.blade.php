
@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ config('constants.LOGISTIC_PARTNER_MASTER_URL')}}" class="category-add-link">{{ trans("messages.logistic-partner-master") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card card-body mb-3">
                	@include('admin/common-form-validation-error')
                	{!! Form::open(array( 'id '=> 'add-logistic-partner-master-form' , 'method' => 'post' ,  'url' => 'logistic-partner-master/add')) !!}
				 	<div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="logistic_partner_name">{{ trans("messages.logistic-partner-name") }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control"  name="logistic_partner_name" value="{{old('logistic_partner_name',  ( (isset($recordInfo) && (!empty($recordInfo->v_logistic_partner_name))) ?  $recordInfo->v_logistic_partner_name : '' ) )}}" placeholder="{{ trans('messages.logistic-partner-name') }}" autofocus="">
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
                                    <div class="card-body logistic-partner">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>{{ trans("messages.sr-no") }}</th>
                                                        <?php /* ?>
                                                        <th>{{ trans("messages.logistic-partner-code") }} <span class="star">*</span></th>
                                                        <?php */ ?>
                                                        <th>{{ trans("messages.logistic-partner-address") }} <span class="star">*</span></th>
                                                        <th>{{ trans("messages.logistic-partner-country") }}<span class="star">*</span></th>
                                                        <th>{{ trans("messages.contact-person-name") }}</th>
                                                        <th>{{ trans("messages.contact-mobile") }}</th>
                                                        <th>{{ trans("messages.contact-email") }}</th>
                                                        <th>{{ trans("messages.action") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="logistic-partner-master-tbody">
                                                	<?php 
                                                	if(!empty($recordDetails)){
                                                		foreach ($recordDetails as $countKey => $recordDetail){
                                                			$columIndex  = ( $countKey +  1 );
                                                			?>
                                                		<tr>
                                                        <td class="table-index text-center"><?php echo $columIndex ?></td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control logistic-partner-code-row" onchange="checkLogisticPartnerCode(this);" name="edit_logistic_partner_code_<?php echo $recordDetail->logistic_detail_id ?>" value="<?php echo (isset($recordDetail->v_logistic_partner_code) ? $recordDetail->v_logistic_partner_code : '' ); ?>" ></td>
                                                        <?php */ ?>
                                                        <td><textarea class="form-control logistic-partner-address-row" name="edit_logistic_partner_address_<?php echo $recordDetail->logistic_detail_id ?>" rows="1"><?php echo (isset($recordDetail->v_logistic_partner_address) ? $recordDetail->v_logistic_partner_address : '' ); ?></textarea></td>
                                                        <td>
                                                            <select name="edit_logistic_partner_country_<?php echo $recordDetail->logistic_detail_id ?>" class="form-control logistic-partner-country-row">
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
                                                        <td><input type="text" class="form-control contact-person-name-row" name="edit_logistic_partner_contact_person_name_<?php echo $recordDetail->logistic_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_person_name) ? $recordDetail->v_contact_person_name : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="edit_logistic_partner_contact_mobile_<?php echo $recordDetail->logistic_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_mobile) ? $recordDetail->v_contact_mobile : '' ); ?>"></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="edit_logistic_partner_contact_email_<?php echo $recordDetail->logistic_detail_id ?>" value="<?php echo (isset($recordDetail->v_contact_email) ? $recordDetail->v_contact_email : '' ); ?>"></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                		<?php 
                                                		}
                                                	} else{  
                                                	?>
                                                	<tr>
                                                        <td class="table-index text-center">1</td>
                                                        <?php /* ?>
                                                        <td><input type="text" class="form-control logistic-partner-code-row" onchange="checkLogisticPartnerCode(this);" name="logistic_partner_code_1"></td>
                                                        <?php */ ?>
                                                        <td><textarea class="form-control logistic-partner-address-row" name="logistic_partner_address_1" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="logistic_partner_country_1" class="form-control logistic-partner-country-row">
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
                                                        <td><input type="text" class="form-control contact-person-name-row" name="logistic_partner_contact_person_name_1" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="logistic_partner_contact_mobile_1" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="logistic_partner_contact_email_1" value=""></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="table-index text-center">2</td>
                                                         <?php /* ?>
                                                        <td><input type="text" class="form-control logistic-partner-code-row" onchange="checkLogisticPartnerCode(this);" name="logistic_partner_code_2"></td>
                                                        <?php */ ?>
                                                        <td><textarea class="form-control logistic-partner-address-row" name="logistic_partner_address_2" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="logistic_partner_country_2" class="form-control logistic-partner-country-row">
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
                                                        <td><input type="text" class="form-control contact-person-name-row" name="logistic_partner_contact_person_name_2" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-mobile-row" name="logistic_partner_contact_mobile_2" value=""></td>
                                                        <td><input type="text" class="form-control contact-person-email-row" name="logistic_partner_contact_email_2" value=""></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewLogisticPartner(this);"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-md-12 submit-sticky">
                        <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
                            <input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
                            <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
                            <?php } else {?>
                             <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                            
                            <?php }?>
                            <a href="{{ config('constants.LOGISTIC_PARTNER_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                        </div>
                    </div>
                    <input type="hidden" name="logistic_partner_count" value="">
                   {!! Form::close() !!}
            </div>
        </div>
    </section>
</main>
<script>
var logistic_partner_count = 2;
<?php if(!empty($recordDetails)){?>
logistic_partner_count = '<?php echo count($recordDetails)?>';
<?php 
}?>
	var unique_partner_code = true;
    $("#add-logistic-partner-master-form").validate({
        errorClass: "invalid-input",
        rules: {
            logistic_partner_name: {
                required: true,
                validateUniqueLogisticPartnerName:true
            },
        },
        messages: {
            logistic_partner_name: {
                required: "{{ trans('messages.require-logistic-partner-name') }}"
            },
        },
        submitHandler: function(form) {
            var logistic_partner_address_status = false;
            var logistic_partner_country_status = false;
            var logistic_partner_master_code = false;
            var unique_partner_code = true;
            var unique_partner_array = [];
            var logistic_partner_master_address = false;
            $('.logistic-partner-master-tbody tr').each(function(){
            	var logistic_partner_code = $.trim($(this).find('.logistic-partner-code-row').val());
            	var logistic_partner_address = $.trim($(this).find('.logistic-partner-address-row').val());
            	var logistic_partner_country = $.trim($(this).find('.logistic-partner-country-row').val());
				
            	if(logistic_partner_address != "" && logistic_partner_address != null){
            		logistic_partner_master_address = true;
            		/* if( ( logistic_partner_address == "" || logistic_partner_address == null ) && (logistic_partner_address_status != true) ){
            			$.trim($(this).find('.logistic-partner-address-row').focus());
            			logistic_partner_address_status = true;
                	} */
            		if( ( logistic_partner_country == "" || logistic_partner_country == null ) && ( logistic_partner_country_status != true && logistic_partner_address_status != true ) ){
						$.trim($(this).find('.logistic-partner-country-row').focus());
						logistic_partner_country_status = true;
					}

            		/* if( unique_partner_code != false ){
						if( $.inArray( logistic_partner_code ,unique_partner_array  ) == -1 ){
							unique_partner_array.push(logistic_partner_code);
						} else {
							unique_partner_code = false;
							$(this).find('.logistic-partner-code-row').focus()
						}            		
            		} */
					
                }
        	})
        	
        	if( unique_partner_code != true ){
        		alertifyMessage("error","{{ trans('messages.error-unique-logistic-partner-code') }} ");
        		return false;
            }
        	
        	/* if( logistic_partner_master_code != true ){
        		$.trim($('.logistic-partner-code-row:first').focus());
           		alertifyMessage("error","{{ trans('messages.required-atleast-one-record') }} ");
           		return false;
            } */

            if( logistic_partner_master_address != true ){
        		$.trim($('.logistic-partner-address-row:first').focus());
           		alertifyMessage("error","{{ trans('messages.required-atleast-one-record') }} ");
           		return false;
            }
            if( logistic_partner_address_status != false ){
            	alertifyMessage("error","{{ trans('messages.require-logistic-partner-address') }} ");
            	return false;
            }
            if( logistic_partner_country_status != false ){
            	alertifyMessage("error","{{ trans('messages.require-logistic-partner-country') }} ");
            	return false;
            } 
            var confirm_box = "";
            var confirm_box_msg = "";
            <?php
            if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
		    	confirm_box = "{{ trans('messages.update-logistic-partner') }}";
		        confirm_box_msg = "{{ trans ( 'messages.confirm-logistic-update-msg') }}";
		    <?php 
		    } else {?>
            	confirm_box = "{{ trans('messages.add-logistic-partner') }}";
			    confirm_box_msg = "{{ trans ( 'messages.confirm-logistic-msg') }}";
			 <?php 
			 }?>

			 if( unique_partner_code != true){
				 alertifyMessage("error","{{ trans('messages.error-unique-logistic-partner-code') }} ");
				 return true; 
		     }
             alertify.confirm(confirm_box,confirm_box_msg,function() {
				$("[name='logistic_partner_count']").val(logistic_partner_count);
        		showLoader()
                form.submit();
        	},function() {});	
            	
        }
    });
	var logistic_partner_count = logistic_partner_count;
	
    function addNewLogisticPartner(thisitem){
    	logistic_partner_count++;
		var html = '';
		html += '<tr>';
		html += '<td class="table-index text-center">'+logistic_partner_count+'</td>';
		//html += '<td><input type="text" class="form-control logistic-partner-code-row" onchange="checkLogisticPartnerCode(this);" name="logistic_partner_code_'+logistic_partner_count+'"></td>';
		html += '<td><textarea class="form-control logistic-partner-address-row" name="logistic_partner_address_'+logistic_partner_count+'" rows="1"></textarea></td>';
		html += '<td>';
		html += '<select name="logistic_partner_country_'+logistic_partner_count+'" class="form-control logistic-partner-country-row">';
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
		html += '<td><input type="text" class="form-control contact-person-name-row" name="logistic_partner_contact_person_name_'+logistic_partner_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control contact-person-mobile-row" name="logistic_partner_contact_mobile_'+logistic_partner_count+'" value=""></td>';
		html += '<td><input type="text" class="form-control contact-person-email-row" name="logistic_partner_contact_email_'+logistic_partner_count+'" value=""></td>';
		html += '<td><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
		html += '</tr>';
		
		if( $('.logistic-partner-master-tbody').find('tr').length > 0 ){
			$(html).insertAfter($('.logistic-partner-master-tbody').find('tr:last'));	
		} else {
			$('.logistic-partner-master-tbody').html(html);
		}
		reindexTable('logistic-partner-master-tbody');
    }
    var logistic_partner_module_url = '{{config("constants.LOGISTIC_PARTNER_MASTER_URL")}}' + '/'; 


    function checkLogisticPartnerCode(thisitem){
		var logistic_partner_code = $.trim($(thisitem).val());
		if( logistic_partner_code != "" && logistic_partner_code != null ){
			$.ajax({
	    		type: "POST",
	    		async: false,
	    		url: logistic_partner_module_url +'checkUniqueLogisticPartnerCode',
	    		dataType: "json",
	    		data: {
	    			"_token": "{{ csrf_token() }}",
	    			'logistic_partner_code': logistic_partner_code ,'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
	    		},
	    		beforeSend: function() {
	    			
	    		},
	    		success: function (response) {
	    			if (response.status_code == 101) {
	    				$(thisitem).val("");
	    				unique_partner_code = false;
	    				alertifyMessage("error","{{ trans('messages.error-unique-logistic-partner-code') }} ");
	    			}  else {
	    				unique_partner_code = true;
		    		}
	    		}
	    	});
		}
    }
     
    $.validator.addMethod("validateUniqueLogisticPartnerName", function (value, element) {
   	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: logistic_partner_module_url +'checkUniqueLogisticPartnerName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'logistic_partner_name': $.trim($("[name='logistic_partner_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
    }, '<?php echo trans("messages.error-unique-logistic-partner-name")?>');  
</script>
@endsection