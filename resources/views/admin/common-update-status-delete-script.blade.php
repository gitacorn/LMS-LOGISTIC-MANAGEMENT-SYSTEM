{!! Form::open(array( 'id '=> 'delete-record-form' , 'method' => 'post' ,  'url' => 'removeRecord' )) !!}
		 <input type="hidden" name="delete_record_id" value="">
		 <input type="hidden" name="delete_module_name" value="">
		 {!! Form::close() !!}
{!! Form::open(array( 'id '=> 'manage-session-messages-form' , 'method' => 'post' ,  'url' => 'manage-session-messages'  )) !!}
	<input type="hidden" name="session_redirect_module_url" value="">
	<input type="hidden" name="session_redirect_module_name" value="">
	<input type="hidden" name="session_redirect_module_action" value="add">
{!! Form::close() !!}
<script>
function updateRecordStatus(thisitem , moduleName){
	var recordId = $(thisitem).attr("data-record-id");
	var	hitURL = site_url + moduleName + "/updateStatus";
	var	currentRow = $(thisitem);
	var	status = $(thisitem).parents('.status-class').find('.record-status').text();
	
	//temper module name for lookup_module
	
	if( moduleName == "{{ config('constants.LOOKUP_MODULE') }}" ){
		moduleName = $(thisitem).attr("data-another-module-name");
	}
	status = $.trim(status);
	var confirm_update_msg = '';
	if(status.toLowerCase() == '{{ strtolower(config("constants.ENABLE_STATUS")) }}'){
        doStatus = 'disable';
        confirm_update_msg = "{{ trans ( 'messages.update-status-msg', [ 'module' => trans('messages.disable') ] ) }}";
    } else if (status.toLowerCase() == '{{ strtolower(config("constants.DISABLE_STATUS")) }}'){
        doStatus = 'enable';
        confirm_update_msg = "{{ trans ( 'messages.update-status-msg', [ 'module' => trans('messages.enable') ] ) }}";
    } else {
    	alertifyMessage('error','{{ trans("messages.system-error") }}');
    }
	
	 
	moduleName = moduleName.replace(/_/g, ' ');
	
	alertify.confirm('{{ trans("messages.update-status") }}', confirm_update_msg , function(){	
		
		//ajax reqeust
	   jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { "_token": "{{ csrf_token() }}", 'record_id' : recordId , 'current_status' : status , 'lookup_module_name' : moduleName  },
			beforeSend: function() {
		        //block ui
				showLoader();
		    },success:function(response){
		    	hideLoader();
				if(response.status_code == 1) {
					if( $(document).find('.filter-button').length  > 0  ){
						filterData();
					}
					alertifyMessage('success',response.message);
					$(thisitem).parents('.status-class').find('.record-status' ).text(response.data.update_status) ;
				} else if(response.status_code == 101) {
					alertifyMessage('error',response.message);
				} else {
					alertifyMessage('error','{{ trans("messages.system-error") }}');
				}
		    },error:function(){
		    	
		    }
	   });
	}, function(){
		
		if(status.toLowerCase() == '{{ strtolower(config("constants.ENABLE_STATUS")) }}'){
			$(thisitem).prop('checked', true);
		} else if (status.toLowerCase() == '{{ strtolower(config("constants.DISABLE_STATUS")) }}'){
			$(thisitem).prop('checked', false);
		}
	});;
}

function deleteRecord(thisitem){
	
	var module_name = $.trim($(thisitem).attr('data-module-name'));

	
	alertify.confirm('{{ trans("messages.delete-record") }}', '{{ trans("messages.delete-record-msg") }}' , function () {
		
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var module_name = $.trim($(thisitem).attr('data-module-name'));
		
		if( module_name != '' && module_name != null && record_id != null &&  record_id != "" ){
			if( module_name == "{{ config('constants.LOOKUP_MODULE') }}" ){
				$("[name='delete_module_name']").val( $.trim($(thisitem).attr('data-another-module-name')));
			} else {
				$("[name='delete_module_name']").val(module_name);
			}
			$("[name='delete_record_id']").val(record_id);
			
			var deleteUrl = site_url + module_name + "/delete/" + record_id ;
			$("#delete-record-form").attr('action' , deleteUrl );
			showLoader();
			$("#delete-record-form").submit();
		}
	}, function () { });
}

function removeLogisticTableRrecord(thisitem, tbody_class_name = null){
	alertify.confirm('{{ trans("messages.delete-record") }}', '{{ trans("messages.delete-record-msg") }}' , function () {
		if(tbody_class_name == null ||  tbody_class_name == ''){
			tbody_class_name = $(thisitem).parents('tbody').attr('class');
		}
		$(thisitem).parents('tr').remove();
		reindexTable(tbody_class_name)
	}, function () { });
}

function reindexTable(tbody_class_name){
	var table_index = 1;
	$('.'+tbody_class_name+ ' tr').each(function(){
		$(this).find('.table-index').html(table_index);
		table_index++;
	})
	
}	

function removeUploadedFile(thisitem){
	var record_id = $.trim($(thisitem).attr("data-record-id"));
	var module_name = $.trim($(thisitem).attr("data-field-name"));
	var file_name = $.trim($(thisitem).attr("data-file-name"));
	var form_name = $.trim($(thisitem).parents('form').attr('id'));
	var field_name = 'remove_' + module_name + '_'+ record_id;

	alertify.confirm('{{ trans("messages.delete-file") }}', '{{ trans("messages.delete-file-msg") }}' , function () {
		
		if( module_name != '' && module_name != null && record_id != null &&  record_id != "" ){
			if($("#" + form_name ).find("[name='"+field_name+"']").length  > 0 ){
				var read_previous_value = $("#" + form_name ).find("[name='"+field_name+"']").val();
				var updated_value = read_previous_value + ',' + file_name;
				$("#" + form_name ).find("[name='"+field_name+"']").val(updated_value);
			} else {
				$("<input type='hidden'/>")
			     .attr("name", field_name )
			     .attr("value", file_name )
			     .appendTo("#" + form_name );
			}
			$(thisitem).parents('.download-link-items').remove();
		}
	}, function () { });
	
}

var agent_warehouse_module_url = '{{config("constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL")}}' + '/';

function getPortToagentContainerDetails(thisitem){
	var logistic_parner_detail_id = $.trim($("[name='logistic_partner_name']").val());
	var data_record_filter_id =  $.trim($(thisitem).attr("data-record-id"));
	
	if(logistic_parner_detail_id !="" && logistic_parner_detail_id != null){
		$.ajax({
			type: "POST",
			url: agent_warehouse_module_url + 'getPortToagentContainerDetails',
			data: {
				"_token": "{{ csrf_token() }}",
				'logistic_parner_detail_id': logistic_parner_detail_id,'data_record_filter_id':data_record_filter_id,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				$(thisitem).parents('.warehouse-amazon-list').find('.container-list').html('');
				if(response !="" && response != null){
					$(thisitem).parents('.warehouse-amazon-list').find('.container-list').html(response);
				}
			},
			error: function() {
				hideLoader();
			}
		});

	}
}

var agent_warehouse_transporter_count = 2;
function addNewInvoiceRow(thisitem){
	agent_warehouse_transporter_count++;
	var html =""; 
	html += '<tr>';
	html += '<td class="table-index">'+agent_warehouse_transporter_count+'</td>';
	html += '<td class="text-left">';
	html += '<select name="name_'+agent_warehouse_transporter_count+'" class="form-control agent-warehouse-transporter-name select2">';
	html += '<option value="">{{ trans("messages.select") }}</option>';
    <?php 
    if(!empty($logisticPartnerDetails)){
    	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
    		$encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
    		?>
    		html += '<option value="{{ $encodeLogisticPartnerId }}"><?php echo  (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) ?></option>';
    		<?php 
    	}
    }
    ?>
    html += '</select>';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control agent-warehouse-transporter-inv-no" name="inv_no_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.inv-no') }}">';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control agent-to-warehouse-freight" name="freight_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control agent-to-warehouse-custom" name="custom_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control agent-to-warehouse-duty" name="duty_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control agent-to-warehouse-other" name="other_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control agent-to-warehouse-vat" name="vat_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<div class="input-group align-items-center flex-nowrap">';
	html += '<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>';
	html += '<div class="input-group-prepend">';
	html += '<select class="form-control ml-2" name="currency_id_'+agent_warehouse_transporter_count+'" onchange="getTotalNumberOfValue(this)">';
	html += '<option  value="">{{trans('messages.currency')}}</option>';
    <?php 
    if(!empty($currencyRecordDetails)){
    	foreach ($currencyRecordDetails as $currencyRecordDetail){
    		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
       		?>
       		html += '<option value="{{ $encodeCurrencyrId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>';
        	<?php 
   		}
  	} 
   ?>
   html += '</select>';
   html += '</div>';
   html += '</div>';
   html += '</td>';
   html += '<td class="text-left">';
   html += '<input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_'+agent_warehouse_transporter_count+'" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
   html += '</td>';
   html += '<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>';
   html += '<td class="text-left">';
   html += '<div class="custom-file">';
   html += '<input type="file" class="custom-file-input" id="invoice_'+agent_warehouse_transporter_count+'" name="invoice_file_'+agent_warehouse_transporter_count+'[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
   html += '<label class="custom-file-label" for="invoice_'+agent_warehouse_transporter_count+'">{{ trans('messages.choose-file') }}</label>';
   html += '</div>';
   html += '</td>';
   html += '<td class="actions-col">';
   html += '</td>';
   html += '</tr>';
   if( $('.agent-to-warehouse-transport-tbody').find('tr').length > 0 ){
  		$(html).insertAfter($('.agent-to-warehouse-transport-tbody').find('tr:last'));	
	} else {
		$('.agent-to-warehouse-transport-tbody').html(html);
	}
	reindexTable('agent-to-warehouse-transport-tbody');

	$(function(){
   	   	$('.select2').select2();
   	})
}

var agent_warehouse_document_type_count = 2;
function addNewDocumentRow(thisitem){
	agent_warehouse_document_type_count++;
	var html =""; 
	html += '<tr>';
	html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+agent_warehouse_document_type_count+'</td>';
	html += '<td class="text-left">';
	html += '<select name="type_'+agent_warehouse_document_type_count+'" class="form-control warehouse-document-type">';
	html += '<option value="">{{ trans("messages.select") }}</option>';
    <?php 
    if(!empty($documentTypeRecordDetails)){
    	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
       		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
        	?>
            html += '<option value="{{ $encodevDocumentTypeId }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>';
            <?php 
  		}
	} 
   ?>
   html += '</select>';
   html += '</td>';
   html += '<td class="text-left">';
   html += '<div class="custom-file">';
   html += '<input type="file" class="custom-file-input warehouse-document-file" id="document_'+agent_warehouse_document_type_count+'" name="file_'+agent_warehouse_document_type_count+'[]" multiple  onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
   html += '<label class="custom-file-label" for="document_'+agent_warehouse_document_type_count+'">{{ trans('messages.choose-file') }}</label>';
   html += '</div>';
   html += '</td>';
   html += '<td class="text-left">';
   html += '<input type="text" class="form-control" name="remarks_'+agent_warehouse_document_type_count+'">';
   html += '</td>';
   html += '<td class="actions-col">';
   html += '</td>';
   html += '<td style="width:70px;min-width:70px;">';
   <?php if( isset($documentForm) && empty($documentForm) ) { ?>
   html += '<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>';
   <?php } ?>
   html += '</td>';
   html += '</tr>';
   if( $('.agent-to-warehouse-document-tbody').find('tr').length > 0 ){
		$(html).insertAfter($('.agent-to-warehouse-document-tbody').find('tr:last'));	
   	} else {
   		$('.agent-to-warehouse-document-tbody').html(html);
   	}
   	reindexTable('agent-to-warehouse-document-tbody');
   	
}
function getTotalNumberOfValue(thisitem){
	var agent_to_warehouse_freight =  $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-freight').val());
   	var agent_to_warehouse_custom =  $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-custom').val());
   	var agent_to_warehouse_duty =  $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-duty').val());
   	var agent_to_warehouse_other =  $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-other').val());
   	var agent_to_warehouse_vat =  $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-vat').val());
	var agent_to_warehouse_con_rate = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-con-rate').val());

	agent_to_warehouse_freight = ( parseFloat(agent_to_warehouse_freight) > 0.00 ? parseFloat(agent_to_warehouse_freight).toFixed(2) : 0.00 );
	agent_to_warehouse_custom = ( parseFloat(agent_to_warehouse_custom) > 0.00 ? parseFloat(agent_to_warehouse_custom).toFixed(2) : 0.00 );
	agent_to_warehouse_duty = ( parseFloat(agent_to_warehouse_duty) > 0.00 ? parseFloat(agent_to_warehouse_duty).toFixed(2) : 0.00 );
	agent_to_warehouse_other = ( parseFloat(agent_to_warehouse_other) > 0.00 ? parseFloat(agent_to_warehouse_other).toFixed(2) : 0.00 );
	agent_to_warehouse_vat = ( parseFloat(agent_to_warehouse_vat) > 0.00 ? parseFloat(agent_to_warehouse_vat).toFixed(2) : 0.00 );
	agent_to_warehouse_con_rate = ( parseFloat(agent_to_warehouse_con_rate) > 0.00 ? parseFloat(agent_to_warehouse_con_rate).toFixed(4) : 0.00 );
	   
   	var total_value = (parseFloat(agent_to_warehouse_freight)+ parseFloat(agent_to_warehouse_custom) + parseFloat(agent_to_warehouse_duty) + parseFloat(agent_to_warehouse_other) + parseFloat(agent_to_warehouse_vat));
   	var total_con_rate = (parseFloat(total_value) * parseFloat(agent_to_warehouse_con_rate));

   	total_value  = ( parseFloat(total_value) > 0.00 ?  parseFloat(total_value) : 0.00 );
   	total_value = (parseFloat(total_value) > 0.00 ? total_value.toFixed(2) :0.00);
   	//console.log("total_value = " +  total_value ); 
   	total_con_rate  = ( parseFloat(total_con_rate) > 0.00 ?  parseFloat(total_con_rate) : 0.00 );
   	total_con_rate = (parseFloat(total_con_rate) > 0.00 ? total_con_rate.toFixed(2) :0.00);
   	
	$(thisitem).parents('tr').find('.agent-warehouse-total-value').html(total_value);
	
	$(thisitem).parents('tr').find('.agent-warehouse-final-rate').html(total_con_rate);

	if($('[name="logistic_cost_usd"]').length > 0){
		calculateTotalTransportCharge()
	}
	
}

function calculateTotalTransportCharge(){
	let total_charge = 0;
	if($('.agent-to-warehouse-transport-tbody tr').length > 0){
		$('.agent-to-warehouse-transport-tbody tr').each(function(){
			let get_final_total = parseFloat($(this).find('.agent-warehouse-final-rate').text());
			if(get_final_total > 0){
				total_charge += get_final_total;
			}
		})
	}
	
	$('[name="logistic_cost_usd"]').val(total_charge.toFixed(2));
}

function validFile(thisitem , allowed_file_type = 'image'){
	var filedId = $(thisitem).attr("id");
	var validImageTypes = [];
	var validExtensions = [];
	var message = '';
	
	switch(allowed_file_type){
		case 'pdf_doc':
			validExtensions = [ 'pdf', 'doc', 'docx' ];
			message = '{{ trans("messages.error-invalid-file") }}';
			break;
		case 'excel':
			validExtensions = [ 'xls', 'xlsx' ];
			message = '{{ trans("messages.error-invalid-file") }}';
			break;
		case 'pdf_doc_ppt_html_xls':
			validExtensions = [ 'pdf', 'doc', 'docx' , 'html' , 'ppt' , 'pptx' , 'xls' , 'xlsx' ];
			message = '{{ trans("messages.error-invalid-file") }}';
			break;
		case 'pdf_doc_jpg_png_jpeg_xls':
			validExtensions = [ 'pdf', 'doc', 'docx' , 'png' , 'jpg' , 'jpeg' , 'xls' , 'xlsx' ];
			message = '{{ trans("messages.invalid-file-select") }}';
			break;
		case 'pdf_doc_ppt_html':
			validExtensions = [ 'pdf', 'doc', 'docx' , 'html' , 'ppt' , 'pptx' ];
			message = '{{ trans("messages.error-invalid-file") }}';
			break;
		case 'image':
			validImageTypes = [ 'image/jpg', 'image/jpeg', 'image/png' ];
			validExtensions = [ 'jpg', 'jpeg', 'png' ];
			message = '{{ trans("messages.invalid-image") }}';
			break;
		case 'image_pdf':
			validImageTypes = [ 'image/jpg', 'image/jpeg', 'image/png', 'application/pdf' ] ;
			validExtensions = [ 'jpg', 'jpeg', 'png', 'pdf' ];
			message = '{{ trans("messages.invalid-image-pdf") }}';
			break;
		case 'cdr':
			validImageTypes = [ 'application/octet-stream', 'application/coreldraw', 'application/x-coreldraw', 'application/vnd.corel-draw', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr' ];
			validExtensions = [ 'cdr' ];
			message = '{{ trans("messages.invalid-cdr") }}';
			break;
		case 'pdf_cdr':
			validImageTypes = [ 'application/pdf', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/pdf' ];
			validExtensions = [ 'pdf', 'cdr' ];
			message = '{{ trans("messages.invalid-pdf-cdr") }}';
			break;
		case 'pdf_cdr_jpg':
			validImageTypes = [ 'image/jpg', 'image/jpeg', 'application/pdf', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/pdf' ];
			validExtensions = [ 'pdf', 'cdr' , 'jpg' ];
			message = '{{ trans("messages.invalid-pdf-cdr-jpg") }}';
			break;
		case 'pdf_cdr_jpg_png_jpeg':
			validImageTypes = [ 'image/jpg', 'image/jpeg', 'image/png',  'application/pdf', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/pdf' ];
			validExtensions = [ 'pdf', 'cdr' , 'jpg' , 'png' , 'jpeg' ];
			message = '{{ trans("messages.invalid-pdf-cdr-jpg") }}';
			break;
		case 'csv':
			validExtensions = [ 'csv' ];
			message = '{{ trans("messages.error-invalid-file") }}';
			break;
	}
	
	var input = this;
	
	if (thisitem.files && thisitem.files[0]) {

		var filesAmount = thisitem.files.length;

		for (i = 0; i < filesAmount; i++) {

			var fileName = $.trim(thisitem.files[i].name);
			
			var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
			fileNameExt = ( ( fileNameExt != "" && fileNameExt != null ) ?  fileNameExt.toLowerCase() : '' );
			var fileType = thisitem.files[i]["type"];
		       
			var reader = new FileReader();
			
			if ( $.inArray(fileNameExt, validExtensions) == -1 ) {
				//console.log('invalid file');
				alertifyMessage("error", message );
				$("." + filedId + "-preview-div").hide();
				$("." + filedId + "-preview").attr("src", "");
				$(thisitem).attr('data-has-file' , 'no');
				$(thisitem).attr('data-valid-file' , 'no');
				$(thisitem).siblings(".custom-file-label").html("{{ trans('messages.choose-file') }}");
				$(thisitem).blur();
				$(thisitem).val("");
				return false;
			}
			
			var invalidImageTypes = [ 'application/pdf', 'application/octet-stream', 'application/coreldraw', 'application/x-coreldraw', 'application/vnd.corel-draw', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr' ];
		    
			if ( $.inArray(fileNameExt, validExtensions) != -1 ) {
				//console.log('valid file');
				$("." + filedId + "-preview-div").parent('div').show();
				$("." + filedId + "-preview-div").show();
				$("." + filedId + "-preview").show();
				$("." + filedId + "-preview").attr("src", "");
				
				if( allowed_file_type ==  'image' ){
					reader.onload = function (e) {
						$("." + filedId + "-preview").attr("src", e.target.result);
					}
					reader.readAsDataURL(thisitem.files[i]);
					$('.submit-button').prop('disabled' , false);
				}
				$(thisitem).attr('data-has-file' , 'yes');
				$(thisitem).attr('data-valid-file' , 'yes');
				
				if( $(thisitem).attr("multiple") == "multiple" ){
					if( thisitem.files.length > 1 ){
						$(thisitem).siblings(".custom-file-label").html("Multiple Files Selected"); 
					} else {
						$(thisitem).siblings(".custom-file-label").html(thisitem.files[0]["name"]);
					}
					
				}  else {
					$(thisitem).siblings(".custom-file-label").html(thisitem.files[0]["name"]);
				}
				
				
				
			}
			
		}
   		
    	
   	}
}

function dataExportIntoExcel(export_info){
	
	if( export_info != "" && export_info != null ){
		var paginationUrl = export_info.url;
		var searchData = export_info.searchData;
		searchData.custom_export_action = 'export';
		
		$.ajax({
	        url: paginationUrl,
	        type: 'post',
	        dataType : 'json',
	        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	        data: searchData,
	        beforeSend: function() {
	            //block ui
	            showLoader();
	        },
	        success: function (response) {
	            hideLoader();
	            if( response.status_code == 1 ){	
					var opResult = response;
		            var $a = $("<a>");
		            $a.attr("href", opResult.data);
		            $("body").append($a);
		            $a.attr("download", response.file_name);
		            $a[0].click();
		            $a.remove();
				} else if( response.status_code == 101 ){
					alertifyMessage('error' , '{{ trans("messages.no-records-available-to-export") }}');
				}
	        }
	    });
	} else {
		alertifyMessage('error' , '{{ trans("messages.no-records-available-to-export") }}');
	}
}

var europe_to_amazon_module_url = '{{config("constants.EUROPE_TO_AMAZON_MASTER_URL")}}' + '/';

function checkUniqueShipmentId(thisitem){
	var shipment_no = $.trim($(thisitem).parents('tr').find('.amazon-shipment-id-status').val());
	var shipment_record_id = $.trim($(thisitem).parents('tr').find('.amazon-shipment-id-status').attr('data-shipment-id'));
	var shipment_record_type = $.trim($(thisitem).parents('tr').find('.amazon-shipment-id-status').attr('data-record-type'));
	
	if(shipment_no !="" && shipment_no != null){
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: europe_to_amazon_module_url +'checkUniqueShipmentId',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			shipment_record_type : shipment_record_type ,
    			shipment_record_id  : shipment_record_id,
    			shipment_no: shipment_no,'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
    		     },
    		beforeSend: function() {
    			
    		},
    		success: function (response) {
				if (response.status_code == 101) {
    				$(thisitem).val("");
    				unique_shipment_id = false;
    				alertifyMessage("error","{{ trans('messages.error-unique-shipment-id') }} ");
    			}  else {
    				unique_shipment_id = true;
	    		}
    		}
    	});
	}
}

$(function() {
	$('.modal').on('hidden.bs.modal', function() {
		if ($(this).find('form').length > 0) {
			$(this).find('form').validate().resetForm();
			$(this).find('form').trigger("reset");
			$(this).find('form .custom-file-label').html("{{ trans('messages.choose-file') }}");
		}
	});
});

function showCollectionDeliveryData(thisitem){
	var search_collection_delivery = $.trim($('[name="search_collection_delivery"]').val());
	if(search_collection_delivery !="" && search_collection_delivery != null){
		if(search_collection_delivery == '{{config("constants.COLLECTION")}}'){
			$('.delivery-collection-row').hide();
			$('.delivery-collection-location').show();
		} else {
			$('.delivery-collection-row').show();
			$('.delivery-collection-location').hide();
		}
	} else {
		$('.delivery-collection-row').hide();
		$('.delivery-collection-location').hide();
	}
}

var good_in_buyer_module_url = '{{config("constants.GOODS_IN_BUYER_MASTER_URL")}}' + '/';
function getSupplierDetails(thisitem){
	var supplier_country_id = $.trim($('[name="search_supplier_country"]').val());
	
	$.ajax({
    	type:'post',
    	data:{"_token": "{{ csrf_token() }}",'supplier_country_id':supplier_country_id},
    	url: good_in_buyer_module_url + 'getSupplierDetails',
    	beforeSend: function() {
    		//block ui
    		showLoader();
    	},
    	success: function(response) {
    		hideLoader();
    		if(response != "" && response != null){
    			$('.supplier-name-list').html(response);
			} else {
				$('.supplier-name-list').html("");
				$('.supplier-location-list').html("");
			}
    		filterData();
    	},
    	error: function() {
			hideLoader();
		}
    });
}

function getDimensionInfo(thisitem , filterRecord = false){
	var pallet_box_type = $.trim($(thisitem).val());
	
	$.ajax({
    	type:'post',
    	data:{ "_token" : "{{ csrf_token() }}" , 'pallet_box_type' : pallet_box_type },
    	url: good_in_buyer_module_url + 'get-dimension-details',
    	beforeSend: function() {
    		showLoader();
    	},
    	success: function(response){
    		hideLoader();
    		if(response != "" && response != null){
	    		$(thisitem).parents('.dependent-div-class').find('.pallet-box-dimension-div').html(response);
			}
			if(filterRecord != false){
				filterData();
			}
    	},
    	error: function() {
			hideLoader();
		}
    });
}

var good_out_country_port_module_url = '{{config("constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL")}}' + '/';
function relatedWarehouseByWarehouseCountry(thisitem, filter_request = false){
	var from_warehouse_country = $.trim($(thisitem).val());
	var record_id = $.trim($('[name="record_id"]').val());
	
	$.ajax({
    	type:'post',
    	data:{'from_warehouse_country':from_warehouse_country,'record_id':record_id,'filter_request':filter_request},
    	url: good_out_country_port_module_url + 'related-warehouse-by-warehouse-country',
    	headers : {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	 	},
    	beforeSend: function() {
    		showLoader();
    	},
    	success: function(response) {
    		hideLoader();
    		$('.warehouse-name-list').html("");
    		if(response != "" && response != null){
    			$(thisitem).parents('.dependent-field-div').find('.warehouse-name-list').html(response);
			}
    		if(filter_request != false){
				filterData();
			}
    	},
    	error: function() {
			hideLoader();
		}
    });
}

function warehouseTypeWiseLocation(thisItem, filter_request = false){
	$('.own-warehouse-location').hide();
	$('.agent-warehouse-location').hide();
	if($(thisItem).val() === '{{ config("constants.OWN_WAREHOUSE_TYPE") }}'){
		$('.own-warehouse-location').show();
	}
	if($(thisItem).val() === '{{ config("constants.AGENT_WAREHOUSE_TYPE") }}'){
		$('.agent-warehouse-location').show();
	}
	if(filter_request != false){
		$('[name="search_to_own_location"]').val('');
		$('[name="to_agent_location"]').val('');
		$('[name="search_to_own_location"]').trigger('change');
		$('[name="to_agent_location"]').trigger('change');
		filterData();
	}
}
</script>

