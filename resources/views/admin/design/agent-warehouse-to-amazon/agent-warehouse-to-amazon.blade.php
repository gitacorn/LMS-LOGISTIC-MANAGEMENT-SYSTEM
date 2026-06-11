@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} <span>(2)</span> </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<a href="{{ url('add-agent-warehouse-to-amazon') }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-agent-warehouse-to-amazon') }}"><i class=" fas fa-plus mr-md-1"></i> <span class="d-md-block d-none">{{ trans('messages.add') }}</span></a>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			$tableSearchPlaceholder = "Search By";
			?>

			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-6 col-lg-6 col-md-12">
							<div class="form-group">
								<label for="search_by_agent_warehouse" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_agent_warehouse" id="search_by_logistic_partner_name" placeholder="<?php echo $tableSearchPlaceholder ?>">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_way_of_transport">{{ trans("messages.way-of-transport") }}</label>
								<select name="search_way_of_transport" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Air">Air</option>
									<option value="Sea">Sea</option>
									<option value="Truck">Truck</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_from">{{ trans("messages.from") }}</label>
								<select name="search_from" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="FedEx - Mumbai">FedEx - Mumbai</option>
									<option value="Delhivery - Ahmedabad">Delhivery - Ahmedabad</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_to">{{ trans("messages.to") }}</label>
								<select name="search_to" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Amazon Location">Amazon Location</option>
									<option value="Warehouse">Warehouse</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_select_containers" class="control-label">{{ trans("messages.select-containers") }}</label>
								<select name="search_select_containers" class="form-control select2" multiple>
									<option value="CON-001-21092022">CON-001-21092022</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_book_by" class="control-label">{{ trans("messages.book-by") }}</label>
								<select name="search_book_by" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="KARTIK SUTHAR (JD-BUYER)">KARTIK SUTHAR (JD-BUYER)</option>
									<option value="JIMIT PATEL (LOGISTIC)">JIMIT PATEL (LOGISTIC)</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_logistic_partner">{{ trans("messages.logistic-partner") }}</label>
								<select name="search_logistic_partner" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Day Light">Day Light</option>

								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_booking_from_date">{{ trans("messages.booking-from-date") }}</label>
								<input type="text" name="search_booking_from_date" class="form-control date-format" placeholder="{{ trans('messages.booking-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_booking_to_date">{{ trans("messages.booking-to-date") }}</label>
								<input type="text" name="search_booking_to_date" class="form-control date-format" placeholder="{{ trans('messages.booking-to-date') }}">
							</div>
						</div>

						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_collection_form_date">{{ trans("messages.collection-from-date") }}</label>
								<input type="text" name="search_collection_form_date" class="form-control date-format" placeholder="{{ trans('messages.collection-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_collection_to_date">{{ trans("messages.collection-to-date") }}</label>
								<input type="text" name="search_collection_to_date" class="form-control date-format" placeholder="{{ trans('messages.collection-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_form_date">{{ trans("messages.delivery-from-date") }}</label>
								<input type="text" name="search_delivery_form_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_to_date">{{ trans("messages.delivery-to-date") }}</label>
								<input type="text" name="search_delivery_to_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">Delivered </option>
									<option value="">pending </option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body">
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover" id="user-table">
							<thead>
								<tr>
									<th class="sr-col">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.entry-no") }}</th>
									<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.way-of-transport") }} <br> {{ trans("messages.from") }} <br> {{ trans("messages.to") }} </th>
									<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.container-number") }} <br> {{ trans("messages.view-fba") }} </th>
									<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.book-by") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
									<th class="text-left">{{ trans("messages.booking-date") }}</th>
									<th class="text-left">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
									<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.collection-date") }} <br> {{ trans("messages.delivery-date") }}</th>
									<th class="text-left">{{ trans("messages.status") }}</th>
									<th style="max-width:100px;min-width:100px;">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td>1</td>
									<td class="text-left">AWA-001-22092022 </td>
									<td class="text-left">Road <br> Agent Warehouse <br> Amazon</td>
									<td class="text-left">GCNU4804946 - <a href="{{ url('view-fba-sheet') }}" target="_blank"> View FBA</a> <br> GCNU4804946 - <a href="{{ url('view-fba-sheet') }}" target="_blank"> View FBA</a></td>
									<td class="text-left">Smit Patel</td>
									<td class="text-left">Day Light</td>
									<td class="text-left">09/09/2022</td>
									<td class="text-left">123456 <br> <a href="#"> https://www.google.com</a></td>
									<td class="text-left">12/09/2022 <br> 09/08/2022</td>
									<td class="text-left">delivered</td>
									<td>
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>

									</td>
								</tr>
								<tr>
									<td>2</td>
									<td class="text-left">AWA-001-22092022 </td>
									<td class="text-left">Road <br> Agent Warehouse <br> Amazon</td>
									<td class="text-left">GCNU4804946 - <a href="{{ url('view-fba-sheet') }}" target="_blank"> View FBA</a> <br> GCNU4804946 - <a href="{{ url('view-fba-sheet') }}" target="_blank"> View FBA</a></td>
									<td class="text-left">Smit Patel</td>
									<td class="text-left">Day Light</td>
									<td class="text-left">15/10/2022</td>
									<td class="text-left">123456 <br> <a href="#"> https://www.google.com</a></td>
									<td class="text-left">09/10/2022 <br> 09/07/2022</td>
									<td class="text-left">delivered</td>
									<td>
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>

									</td>
								</tr>
							</tbody>
						</table>
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
				<form method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group mb-0">
									<label for="upload_file" class="control-label">Upload File <span class="text-danger">*</span></label>
									<div class="custom-file">
										<input type="file" class="custom-file-input" id="upload_file" name="upload_file">
										<label class="custom-file-label" for="upload_file" name="upload_file">Choose file...</label>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="submit" class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
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
						<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">FBA <span>- f_ampp_1664255873_021022.xlsx - 27-09-2022</span></h5>
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

<script>
	$("form").validate({
		errorClass: "invalid-input",
		rules: {
			upload_file: {
				required: true
			}

		},
		messages: {
			upload_file: {
				required: "{{ trans('messages.require-upload-file') }}"
			}
		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
</script>

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
			// minDate: moment().startOf('d'),
			format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

		});

	});
</script>
@endsection