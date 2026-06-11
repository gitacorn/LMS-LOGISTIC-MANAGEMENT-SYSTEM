<div class="modal fade" id="csv-import-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title twt-modal-header-name" id="exampleModalLongTitle">{{ trans('messages.import') }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
				</div>
				{!! Form::open(array( 'id '=> 'upload-csv-form' , 'method' => 'post' , 'files' => true )) !!}
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group mb-0">
								<label for="upload_csv_file" class="control-label">{{trans('messages.upload-file')}} <span class="text-danger">*</span></label>
								<div class="custom-file">
									<input type="file" class="custom-file-input" name="upload_csv_file" onchange="validFile(this,'csv');">
									<label class="custom-file-label">{{ trans('messages.choose-file') }}</label>
								</div>
							</div>
						</div>
						<div class="col-lg-12 mb-3">
	                        <div class="d-flex align-items-center">
	                        	<a href="" download class="text-theme btn shadow-none p-0 text-decoration-underline sample-file-download" title="{{ trans('messages.download-sample-file') }}">
	                    		<span class="text-theme ml-1">{{ trans('messages.download-sample-file') }} </span></a>
	                    	</div>
                     	</div>
					</div>
				</div>
				<div class="modal-footer justify-content-center">
					<input type="hidden" name="shipment_type" value="">
					<input type="hidden" name="shipment_request" value="">
					<button type="button" onclick="uploadCSVFile()" class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
<script>
	var us_warehouse_module_url = '{{config("constants.US_WAREHOUSE_TO_AMAZON_MASTER_URL")}}' + '/';

	$("#upload-csv-form").validate({
		 errorClass: "invalid-input",
		rules: {
			upload_csv_file: {
				required : true,  
				extension : 'csv'
			},
		},
		messages: {
			upload_csv_file: {
				required : "{{ trans('messages.required-upload-csv-file') }}",
				extension : "{{ trans('messages.only-allowed-file-types' , [ 'fileTypes' => 'csv' ] )  }}"	
			},
		},
	});
	function openCSVImportModel(thisitem){
		var to = $.trim($("[name='to']").val());
		var amazon = $.trim($(thisitem).attr('data-shipment-type'));
		
		var sample_file_link = "";
		
		if(amazon != "" && amazon != null){
			$("[name='shipment_type']").val(amazon);
			sample_file_link = '{{ config("constants.EUROPE_AMAZON_CSV_SAMPLE_FILE") }}';
			$("[name='shipment_request']").val('{{ config("constants.EUROPE_TO_AMAZON") }}');
		} else {
			$("[name='shipment_type']").val(to);
			$("[name='shipment_request']").val('{{ config("constants.US_TO_WAREHOUSE_AMAZON") }}');
		}
		if(to !="" && to != null){
			switch(to){
			case '{{ config("constants.AMAZON_FBA_SHEET") }}':
				sample_file_link = '{{ config("constants.AMAZON_CSV_SAMPLE_FILE") }}';
				break;
			case '{{ config("constants.CUSTOMER_FBA_SHEET") }}':
				sample_file_link = '{{ config("constants.CUSTOMER_CSV_SAMPLE_FILE") }}';
				break;
			case '{{ config("constants.UK_WAREHOUSE_FBA_SHEET") }}':
				sample_file_link = '{{ config("constants.WAREHOUSE_CSV_SAMPLE_FILE") }}';
				break;
			}
		}
		$('.sample-file-download').attr("href","{{ config('constants.SAMPLE_FILE_STORAGE_FILE_PATH') }}" + sample_file_link);
		openBootstrapModal('csv-import-modal');
	}

	function uploadCSVFile(){
		if($("#upload-csv-form").valid() != true ){
			return false;
		}
		var formData = new FormData( $('#upload-csv-form')[0] );
		var shipment_type = $.trim($("[name='shipment_type']").val());
		var shipment_request = $.trim($("[name='shipment_request']").val());

		if(shipment_type != '' && shipment_type != null){
			switch(shipment_type){
				case '{{ config("constants.AMAZON_FBA_SHEET") }}':
					if( $(document).find('.shipment-details-amazon-tbody').length > 0 ){
						formData.append('import_csv_add_row_count' , us_warehouse_to_amazon_shipment_amazon_count);
					} else {
						formData.append('import_csv_add_row_count' , europe_to_amazon_shipment_count);
					}
					break;
				case '{{ config("constants.CUSTOMER_FBA_SHEET") }}':
					formData.append('import_csv_add_row_count' , us_warehouse_to_amazon_shipment_customer_count);
					break;
				case '{{ config("constants.UK_WAREHOUSE_FBA_SHEET") }}':
					formData.append('import_csv_add_row_count' , us_warehouse_to_amazon_shipment_ukwarehouse_count);
					break;
			}
		}
		
		$.ajax({
			type : 'post',
			url :  us_warehouse_module_url + 'uploadCSVFile',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data : formData,
			dataType: 'json',
			processData: false,
			contentType: false,
			beforeSend : function(){
				showLoader();
			},
			success : function(response){
				hideLoader();
				if(response.status_code == 1 ){
					$("#csv-import-modal").modal('hide');
					$("#upload-csv-form").validate().resetForm();
					if( shipment_type != "" && shipment_type != null ){
						var response_html = response.data.html;
						var import_sheet_count = response.data.import_sheet_count;
						
						var tbody_class = '';
						switch(shipment_type){
							case '{{ config("constants.AMAZON_FBA_SHEET") }}':
								if( $(document).find('.shipment-details-amazon-tbody').length > 0 ){
									if( $('.shipment-details-amazon-tbody').find('tr.filled-record').length > 0 ){
										$(response_html).insertAfter($('.shipment-details-amazon-tbody').find('tr.filled-record:last'))
									} else {
										$('.shipment-details-amazon-tbody').html(response_html);
									}
									us_warehouse_to_amazon_shipment_amazon_count = us_warehouse_to_amazon_shipment_amazon_count + import_sheet_count;	
									tbody_class = 'shipment-details-amazon-tbody';
								} else {
									if( $('.europe-to-amazon-tbody').find('tr.filled-record').length > 0 ){
										$(response_html).insertAfter($('.europe-to-amazon-tbody').find('tr.filled-record:last'))
									} else {
										$('.europe-to-amazon-tbody').html(response_html);
									}
									europe_to_amazon_shipment_count = europe_to_amazon_shipment_count + import_sheet_count;
									tbody_class = 'europe-to-amazon-tbody';
								}
								$(".amazon-shipment-id-status").trigger("change");
								//$('.europe-to-amazon-tbody').html(response.data.html);
								break;
							case '{{ config("constants.CUSTOMER_FBA_SHEET") }}':
								if( $('.shipment-details-customer-record-tbody').find('tr.filled-record').length > 0 ){
									$(response_html).insertAfter($('.shipment-details-customer-record-tbody').find('tr.filled-record:last'))
								} else {
									$('.shipment-details-customer-record-tbody').html(response_html);
								}
								us_warehouse_to_amazon_shipment_customer_count = us_warehouse_to_amazon_shipment_customer_count + import_sheet_count;
								$(".amazon-shipment-id-status").trigger("change");
								//$('.shipment-details-customer-record-tbody').html(response.data.html);
								tbody_class = 'shipment-details-customer-record-tbody';
								break;
							case '{{ config("constants.UK_WAREHOUSE_FBA_SHEET") }}':
								if( $('.shipment-details-uk-warehouse-tbody').find('tr.filled-record').length > 0 ){
									$(response_html).insertAfter($('.shipment-details-uk-warehouse-tbody').find('tr.filled-record:last'))
								} else {
									$('.shipment-details-uk-warehouse-tbody').html(response_html);
								}
								us_warehouse_to_amazon_shipment_ukwarehouse_count = us_warehouse_to_amazon_shipment_ukwarehouse_count + import_sheet_count;
								$(".amazon-shipment-id-status").trigger("change");
								//$('.shipment-details-uk-warehouse-tbody').html(response.data.html);
								tbody_class = 'shipment-details-uk-warehouse-tbody';
								break;
						}
						$('.select2').select2();
					} else {
						$('.shipment-details-amazon-tbody').html("");
						$('.shipment-details-customer-record-tbody').html("");
						$('.shipment-details-uk-warehouse-tbody').html("");
						$('.europe-to-amazon-tbody').html("");
					}
					reindexTable(tbody_class);
				} else {
					alertifyMessage("error", response.message );
					$("#csv-import-modal").modal('hide');
					$('.shipment-details-amazon-tbody').html("");
					$('.shipment-details-customer-record-tbody').html("");
					$('.shipment-details-uk-warehouse-tbody').html("");
					$('.europe-to-amazon-tbody').html("");
				}
			}
		});
	}
</script>