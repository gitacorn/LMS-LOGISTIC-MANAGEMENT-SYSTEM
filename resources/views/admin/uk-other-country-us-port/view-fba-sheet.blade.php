@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.view-fba-sheet") }} (<span class="total-record-count"></span>) </h1>
	</div>

	<section class="inner-wrapper-common-section main-listing-section view-fba-sheet-class">
		<div class="container-fluid">
			<div class="filter-result-wrapper">
				{{ Wild_tiger::readMessage() }}
				<div class="card card-body">
					<div class="view-fba-sheet">
						<div class="row">
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.entry-no") }} : </span><span><?php echo (!empty($getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']->v_country_to_port_record_no) ? $getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']->v_country_to_port_record_no  : '') ?></span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.container-no") }} : </span><span><?php echo (!empty($getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']->v_container_air_waybill_no) ? $getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']->v_container_air_waybill_no  : '') ?></span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.way-of-transport") }} : </span><span><?php echo (!empty($getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']->e_transport_way) ? $getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']->e_transport_way  : '') ?></span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.from-port-airport") }} : </span><span><?php echo (!empty($getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']['fromPortInfo']->v_warehouse_name) ? $getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']['fromPortInfo']->v_warehouse_name  : '') ?></span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.to-port-airport") }} : </span><span><?php echo (!empty($getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']['toPortInfo']->v_warehouse_name) ? $getFbaRecordInfo[0]['fbaSheetMaster']['countryToPortMaster']['toPortInfo']->v_warehouse_name  : '') ?></span></p>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div class="fixed-table-container">
							<div class="fixed-tabel-body">
								<table class="table custom-header-sticky table-sm table-bordered table-hover text-left" id="user-table">
									<thead>
										<tr>
											<th class="sr-col">{{ trans("messages.sr-no") }}</th>
											<th class="text-left">{{ trans("messages.fba-po-invoice") }}</th>
											<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.destination") }}</th>
											<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.ref-id") }}</th>
											<th class="text-left">{{ trans("messages.company") }}</th>
											<th class="text-left">{{ trans("messages.products") }}</th>
											<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.fba-value") }}</th>
											<th class="text-left">{{ trans("messages.location") }}</th>
											<th class="text-left">{{ trans("messages.sku") }}</th>
											<th class="text-left" style="max-width:70px;min-width:70px;">{{ trans("messages.units") }}</th>
											<th class="text-left" style="max-width:250px;min-width:250px;">{{ trans("messages.amazon-address") }}</th>
											<th class="text-left">{{ trans("messages.boxes-units") }}</th>
											<th class="text-left" style="max-width:110px;min-width:110px;">{{ trans("messages.boxes") }}</th>
											<th class="text-left" style="max-width:80px;min-width:80px;">{{ trans("messages.pallet") }}</th>
											<th class="text-left" style="max-width:70px;min-width:70px;">{{ trans("messages.Total-no-of-pallets") }}</th>
											<th class="text-left" style="max-width:110px;min-width:110px;">{{ trans("messages.pallet-dimension") }}</th>
											<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.pallet-weight") }}</th>
											<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.pallet-number") }}</th>
											<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.delivery-status") }}</th>

										</tr>
									</thead>


									@include( config('constants.AJAX_VIEW_FOLDER') . 'uk-other-country-us-port/view-fba-sheet-list')

								</table>
							</div>

						</div>

					</div>
				</div>
			</div>

		</div>

	</section>



	<div class="modal fade bd-example-modal-lg" id="upload-file" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">Upload FBA Sheet</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
				</div>
				<form>
					<div class="modal-body add-dimension-modal-html">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="dimension_name" class="control-label">Upload File<span class="text-danger">*</span></label>
									<div class="custom-file mb-3">
										<input type="file" class="custom-file-input" id="upload_file">
										<label class="custom-file-label" for="upload_file">Choose file...</label>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="modal fade bd-example-modal-lg" id="track-file-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">Error List <span>- f_ampp_1664255873_021022.xlsx - 27-09-2022</span></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-hover mb-0">
								<thead>
									<tr>
										<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
										<th class="text-left">{{ trans("messages.description") }}</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="sr-col">1</td>
										<td class="text-left">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
									</tr>
									<tr>
										<td class="sr-col">2</td>
										<td class="text-left">It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</td>
									</tr>
									<tr>
										<td class="sr-col">3</td>
										<td class="text-left">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
									</tr>

								</tbody>
							</table>
						</div>
						<div class="notes-class pt-2">
							<p class="track-notes-title mb-0"><b style="color: red;">Note :</b> <span>Please Correct Above Errors and Upload Again.</span></p>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</main>

<div class="modal fade bd-example-modal-lg" id="add-fba-sheet-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
			{!! Form::open(array( 'id '=> 'add-fba-sheet-form' , 'method' => 'post' , 'url' => 'addFBASheetDetails')) !!}
			<div class="modal-body add-fba-sheet-modal-html">

			</div>
			<input type="hidden" name="record_id" value="">
			<input type="hidden" name="selection_status" value="">
			<input type="hidden" name="fba_master_id" value="">
			<input type="hidden" name="fba_po_invoice_no" value="">
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn bg-theme text-white action-button fba-modal-action-button" title="{{ trans('messages.submit') }}" onclick="addFBASheetModelDetails()">{{ trans('messages.submit') }}</button>
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
			</div>
			{!! Form::close() !!}
		</div>

	</div>
</div>
<script>
	$(document).ready(function() {

		//init date time picker
		$(".date-format").datetimepicker({
			useCurrent: false,
			viewMode: 'days',
			ignoreReadonly: true,
			widgetPositioning: {
				vertical: 'bottom'
			},
			minDate: moment().startOf('d'),
			format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

		});

	});
	$("#add-fba-sheet-form").validate({
		errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false,
		rules: {
			fba_no: {
				required: true,
				noSpace: true,
				validateUniqueFBAInvoiceNo: true
			},
			ref_id: {
				required: true,
				noSpace: true
			},
			company: {
				required: false,
				noSpace: true
			},
			products: {
				required: false,
				noSpace: true
			},
			location: {
				required: false,
				noSpace: true
			},


		},
		messages: {
			fba_no: {
				required: "{{ trans('messages.require-fba-invoice') }}"
			},
			ref_id: {
				required: "{{ trans('messages.require-ref-id') }}"
			},
			company: {
				required: "{{ trans('messages.require-compnay') }}"
			},
			products: {
				required: "{{ trans('messages.require-product') }}"
			},
			location: {
				required: "{{ trans('messages.require-location') }}"
			}
		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
	var good_out_country_port_module_url = '{{config("constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL")}}' + '/';
	var current_row = '';
	var clone_request = '';

	function editFBASheetModel(thisitem) {
		current_row = thisitem;
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var data_status = $.trim($(thisitem).attr('data-status'));
		var data_ref_no = $.trim($(thisitem).attr('data-ref-no'));
		var data_fba_no = $.trim($(thisitem).attr('data-fba-no'));
		var ref_row = $.trim($(thisitem).parents('tr').find('.ref-row').html());
		var fba_master_id = $.trim($(thisitem).attr('data-fba-master-id'));

		clone_request = data_status;

		$.ajax({
			type: "POST",
			url: good_out_country_port_module_url + 'editFBASheetModel',
			data: {
				"_token": "{{ csrf_token() }}",
				record_id: record_id,
				data_status: data_status,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if (record_id != "" && record_id != null) {

					$('.add-fba-sheet-modal-html').html(response);
					$("[name='record_id']").val(record_id);
					$("[name='selection_status']").val(data_status);
					$("[name='fba_master_id']").val(fba_master_id);
					$("[name='fba_po_invoice_no']").val(data_fba_no);
					if (data_status == "{{config('constants.SELECTION_YES') }}") {
						var header_name = "{{ trans('messages.update-fba-sheet') }}";
						var button_name = "{{ trans('messages.update') }}";
						$('.fba-modal-action-button').attr('title', "{{ trans('messages.update') }}");
					} else {
						var header_name = "{{ trans('messages.add-fba-sheet') }}";
						var button_name = "{{ trans('messages.submit') }}";
						$('.fba-modal-action-button').attr('title', "{{ trans('messages.submit') }}");

					}
					locationMasterInfo(thisitem);
					$("#add-fba-sheet-modal").find('.fba-modal-action-button').html(button_name);

					$(function() {
						$(".select2").select2();
					})

					$("#add-fba-sheet-modal").find('.twt-modal-header-name').html(header_name + ' - ' + data_fba_no + ' - ' + ref_row);

				}
				openBootstrapModal('add-fba-sheet-modal');
			},
			error: function() {
				hideLoader();
			}
		});
	}

	function addFBASheetModelDetails() {
		if ($('#add-fba-sheet-form').valid() != true) {
			return false;
		}
		var destination = $.trim($('[name="destination"]').val());
		var units = $.trim($('[name="units"]').val());
		var record_id = $.trim($('[name="record_id"]').val());
		var ref_id = $.trim($('[name="ref_id"]').val());
		var company = $.trim($('[name="company"]').val());
		var products = $.trim($('[name="products"]').val());
		var location = $.trim($('[name="location"]').val());
		var sku = $.trim($('[name="sku"]').val());
		var boxes_units = $.trim($('[name="boxes_units"]').val());
		var amazon_address = $.trim($('[name="amazon_address"]').val());
		var boxes = $.trim($('[name="boxes"]').val());
		var pallet = $.trim($('[name="pallet"]').val());
		var total_no_of_pallets = $.trim($('[name="total_no_of_pallets"]').val());
		var pallet_dimension = $.trim($('[name="pallet_dimension"]').val());
		var pallet_weight = $.trim($('[name="pallet_weight"]').val());
		var pallet_number = $.trim($('[name="pallet_number"]').val());
		var data_status = $.trim($('[name="selection_status"]').val());
		var fba_po_invoice_no = $.trim($('[name="fba_po_invoice_no"]').val());
		var fba_master_id = $.trim($('[name="fba_master_id"]').val());
		var fba_value = $.trim($('[name="fba_value"]').val());
		var fba_no = $.trim($('[name="fba_no"]').val());

		var confirm_box = "";
		var confirm_box_msg = "";
		confirm_box = "{{ trans('messages.fba-sheet') }}";

		if (data_status == "{{config('constants.SELECTION_YES') }}") {
			confirm_box_msg = "{{ trans ( 'messages.confirm-update-fba-sheet-msg') }}";
		} else {
			confirm_box_msg = "{{ trans ( 'messages.confirm-add-fba-sheet-msg') }}";
		}

		alertify.confirm(confirm_box, confirm_box_msg, function() {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: good_out_country_port_module_url + 'addFBASheetModelDetails',
				data: {
					"_token": "{{ csrf_token() }}",
					'record_id': record_id,
					'destination': destination,
					'units': units,
					'ref_id': ref_id,
					'company': company,
					'products': products,
					'location': location,
					'sku': sku,
					'boxes_units': boxes_units,
					'amazon_address': amazon_address,
					'boxes': boxes,
					'pallet': pallet,
					'total_no_of_pallets': total_no_of_pallets,
					'pallet_dimension': pallet_dimension,
					'pallet_weight': pallet_weight,
					'pallet_number': pallet_number,
					'data_status': data_status,
					'fba_po_invoice_no': fba_po_invoice_no,
					'fba_master_id': fba_master_id,
					'fba_value': fba_value,
					'fba_no': fba_no,

				},
				beforeSend: function() {
					//block ui
					//showLoader();
				},
				success: function(response) {
					hideLoader();
					if (response.status_code == 1) {

						$("#add-fba-sheet-modal").modal('hide');
						alertifyMessage('success', response.message);

						if (data_status == "{{config('constants.SELECTION_YES') }}") {
							$(current_row).parents('tr').find('.destination-row').html(destination);
							$(current_row).parents('tr').find('.ref-row').html(ref_id);
							$(current_row).parents('tr').find('.company-row').html(company);
							$(current_row).parents('tr').find('.product-row').html(products);
							$(current_row).parents('tr').find('.location-code-row').html(location);
							$(current_row).parents('tr').find('.sku-row').html(sku);
							$(current_row).parents('tr').find('.untits-row').html(boxes_units);
							$(current_row).parents('tr').find('.amazon-address-row').html(amazon_address);
							$(current_row).parents('tr').find('.boxes-units').html(boxes_units);
							$(current_row).parents('tr').find('.boxes-row').html(boxes);
							$(current_row).parents('tr').find('.pallet-row').html(pallet);
							$(current_row).parents('tr').find('.total-no-of-pallet').html(total_no_of_pallets);
							$(current_row).parents('tr').find('.pallet-dimension').html(pallet_dimension);
							$(current_row).parents('tr').find('.pallet-weight').html(pallet_weight);
							$(current_row).parents('tr').find('.pallet-no').html(pallet_number);
							$(current_row).parents('tr').find('.fba-value-row').html(fba_value);
						} else {

							$("[name='session_redirect_module_name']").val("{{ trans('messages.fba-sheet') }}");
							$("#manage-session-messages-form").submit();
						}

					} else {
						alertifyMessage('error', response.message);
					}

				},
				error: function() {
					hideLoader();
				}
			});
		}, function() {});
	}

	function deleteFBARecord(thisitem) {
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var module_name = $.trim($(thisitem).attr('data-module-name'));

		alertify.confirm("{{ trans('messages.delete-record') }}", "{{ trans('messages.delete-record-msg') }}", function() {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: good_out_country_port_module_url + 'deleteFBARecord',
				data: {
					"_token": "{{ csrf_token() }}",
					'record_id': record_id,
					'module_name': module_name,


				},
				beforeSend: function() {
					//block ui
					//showLoader();
				},
				success: function(response) {
					hideLoader();
					if (response.status_code == 1) {
						alertifyMessage('success', response.message);
						$(thisitem).parents('tr').remove();
						var row_span = $(thisitem).parents('.fba-recod-start').find('.common-row-for-no').attr('rowspan');
						if (row_span > 1) {
							$(thisitem).parents('.fba-recod-start').find('.common-row-for-no').attr('rowspan', parseInt(row_span - 1));
						}

					} else {
						alertifyMessage('error', response.message);
					}
				},
				error: function() {
					hideLoader();
				}
			});
		}, function() {});
	}

	$.validator.addMethod("validateUniqueFBAInvoiceNo", function(value, element) {

		var result = true;
		$.ajax({
			type: "POST",
			async: false,
			url: good_out_country_port_module_url + 'checkUniqueFBAInvoiceNo',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'fba_no': $.trim($("[name='fba_no']").val()),
				'fba_po_invoice_no': $.trim($("[name='fba_po_invoice_no']").val()),
				'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
			},
			beforeSend: function() {

			},
			success: function(response) {

				if (response.status_code == 1) {
					return false;
				} else {
					result = false;
					return true;
				}
			}
		});
		return result;
	}, '<?php echo trans("messages.error-unique-fba-invoice") ?>');

	function locationMasterInfo(thisitem) {
		var destination_type = $.trim($("[name='destination']").find('option:selected').val());

		$.ajax({
			url: good_out_country_port_module_url + 'getDestinationTypeDetails',
			type: 'POST',
			data: {
				'destination_type': destination_type,
				"_token": "{{csrf_token()}}"
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if (response != "" && response != null) {
					//$(thisitem).parents('.dependant-field-selection').find('.location-master-info-list').html(response);
					$('.location-master-info-list').html(response);
					var current_selected_value = $.trim($("#add-fba-sheet-form").find(".location-master-info-list").attr("data-value"));

					if (current_selected_value != "" && current_selected_value != null) {
						$('.location-master-info-list').val(current_selected_value);
					}
				}
			},
			error: function(errorResponse) {
				hideLoader();
			}
		});

	}
</script>
@endsection