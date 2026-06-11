@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} <span>(2)</span> </h1>
	</div>

	<section class="inner-wrapper-common-section main-listing-section view-fba-sheet-class">
		<div class="container-fluid">
			<div class="filter-result-wrapper">
				<div class="card card-body">
					<div class="view-fba-sheet">
						<div class="row">
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.entry-no") }} : </span><span>AIR-001-21092022</span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.container-no") }} : </span><span>GCNU4804946</span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.way-of-transport") }} : </span><span>Sea</span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.from-port-airport") }} : </span><span>UK</span></p>
							</div>
							<div class="col-xl-2 col-lg-4 col-sm-6">
								<p><span class="font-weight-bold">{{ trans("messages.to-port-airport") }} : </span><span>NEWYORK</span></p>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover text-left" id="user-table">
							<thead>
								<tr>
									<th class="sr-col">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.fba-po-invoice") }}</th>
									<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.destination") }}</th>
									<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.ref-id") }}</th>
									<th class="text-left">{{ trans("messages.company") }}</th>
									<th class="text-left">{{ trans("messages.products") }}</th>
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
								</tr>
							</thead>

							<tbody class="border-class">
								<tr>
									<td>1</td>
									<td class="text-left">FBA16HZ7GP4R</td>
									<td class="text-left">Amazon</td>
									<td class="text-left">118CHX5Q</td>
									<td class="text-left">SUBHA</td>
									<td class="text-left">MARMITE</td>
									<td class="text-left">CLT2</td>
									<td class="text-left">8</td>
									<td class="text-left">1284</td>
									<td class="text-left">Amazon.com Services, Inc., 10240 Old Dowd Rd, Charlotte, NC, US, 28214-8082</td>
									<td>1284</td>
									<td class="text-left">24 BOXES</td>
									<td class="text-left">1 OF 4</td>
									<td class="text-left">4</td>
									<td class="text-left">120X100X140</td>
									<td class="text-left">398</td>
									<td class="text-left">1</td>

								</tr>
								<tr>
									<td>2</td>
									<td class="text-left">FBA16HZ7GP4R</td>
									<td class="text-left">Amazon</td>
									<td class="text-left">118CHX5Q</td>
									<td class="text-left">SUBHA</td>
									<td class="text-left">MARMITE</td>
									<td class="text-left">CLT2</td>
									<td class="text-left">8</td>
									<td class="text-left">1284</td>
									<td class="text-left">Amazon.com Services, Inc., 10240 Old Dowd Rd, Charlotte, NC, US, 28214-8082</td>
									<td>1284</td>
									<td>24 BOXES</td>
									<td>1 OF 4</td>
									<td class="text-left">4</td>
									<td>120X100X140</td>
									<td>420</td>
									<td>2</td>
								</tr>
								<tr>
									<td>3</td>
									<td class="text-left">FBA16HZ7GP4R</td>
									<td class="text-left">Amazon</td>
									<td class="text-left">118CHX5Q</td>
									<td class="text-left">SUBHA</td>
									<td class="text-left">MARMITE</td>
									<td class="text-left">CLT2</td>
									<td class="text-left">8</td>
									<td class="text-left">1284</td>
									<td class="text-left">Amazon.com Services, Inc., 10240 Old Dowd Rd, Charlotte, NC, US, 28214-8082</td>
									<td>1284</td>
									<td>24 BOXES</td>
									<td>1 OF 4</td>
									<td class="text-left">4</td>
									<td>120X100X140</td>
									<td>302</td>
									<td>3</td>
								</tr>
								<tr>
									<td>4</td>
									<td class="text-left">FBA16HZ7GP4R</td>
									<td class="text-left">Amazon</td>
									<td class="text-left">118CHX5Q</td>
									<td class="text-left">SUBHA</td>
									<td class="text-left">MARMITE</td>
									<td class="text-left">CLT2</td>
									<td class="text-left">8</td>
									<td class="text-left">1284</td>
									<td class="text-left">Amazon.com Services, Inc., 10240 Old Dowd Rd, Charlotte, NC, US, 28214-8082</td>
									<td>1284</td>
									<td>24 BOXES</td>
									<td>1 OF 4</td>
									<td class="text-left">4</td>
									<td>120X100X140</td>
									<td>497</td>
									<td>4</td>
								</tr>
							</tbody>
							<tbody class="border-class">
								<tr>
									<td>5</td>
									<td>FBA16K0CL6RY</td>
									<td>Warehouse</td>
									<td>457D7A4J</td>
									<td>SUBHA</td>
									<td class="text-left">EUTHYMOL</td>
									<td class="text-left">CLT2</td>
									<td class="text-left">4</td>
									<td class="text-left">324</td>
									<td class="text-left">Amazon.com Services, Inc., 10240 Old Dowd Rd, Charlotte, NC, US, 28214-8082</td>
									<td>324</td>
									<td>20 BOX</td>
									<td>1 OF 1</td>
									<td>1</td>
									<td>120X100X140</td>
									<td>282</td>
									<td>5</td>

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
</script>
@endsection