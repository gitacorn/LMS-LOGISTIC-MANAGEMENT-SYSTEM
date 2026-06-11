@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (2)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" data-toggle="modal" data-target="#add-warehouse-modal"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-warehouse') }} </span></a>
				<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-5 col-lg-6">
							<div class="form-group">
								<label for="search_by_warehouse" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_warehouse" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.warehouse-name") }} , {{ trans("messages.warehouse-code") }} , {{ trans("messages.warehouse-address") }}">
							</div>
						</div>
						<div class=" col-xl-3">
							<div class="form-group">
								
								<label for="country_name" class="control-label">{{ trans('messages.country-name') }}<span class="text-danger">*</span></label>
								<select class="form-control" name="search_status">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">GERMANY</option>
									<option value="">FRANCE</option>
								</select>
							</div>
							
						</div>
						<div class="col-xl-2 col-lg-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.enable") }}</option>
									<option value="">{{ trans("messages.disable") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-6 d-flex align-items-end gap pt-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body">
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover" id="country-table">
							<thead>
								<tr>
									<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.warehouse-name") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.warehouse-code") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.warehouse-address") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.country-name") }}</th>
									<th class="text-center" width="30%">{{ trans("messages.status") }}</th>
									<th class="actions-col" width="20%">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td class="sr-col" width="5%">1</td>
									<td class="text-left" width="55%">ACORN SOLUTION LTD. UK</td>
									<td class="text-left" width="30%">ASL UK</td>
									<td class="text-left" width="30%"> AUK</td>
									<td class="text-left" width="30%">EUROPE</td>
									<td class="actions-col" width="20%">
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="customSwitch1">
											<label class="custom-control-label" for="customSwitch1">Enable</label>
										</div>
									</td>
									<td class="actions-col">
										<a title="Edit Record" href="#" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
									</td>
								</tr>
								<tr>
									<td class="sr-col" width="5%">2</td>
									<td class="text-left" width="55%">LONDON LUXURY PRODUCT LTD. UK</td>
									<td class="text-left" width="30%">LLP UK</td>
									<td class="text-left" width="30%">Lp-UK</td>
									<td class="text-left" width="30%">LLP EUROPE</td>
									<td class="actions-col" width="20%">
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="customSwitch2">
											<label class="custom-control-label" for="customSwitch2">Disable</label>
										</div>
									</td>
									<td class="actions-col">
										<a title="Edit Record" href="#" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
									</td>
								</tr>
								<tr>
									<td class="sr-col" width="5%">3</td>
									<td class="text-left" width="55%">BEAUTY STORE LLC USA</td>
									<td class="text-left" width="30%">BSLLC USA</td>
									<td class="text-left" width="30%">BSC-USA</td>
									<td class="text-left" width="30%">USA</td>
									<td class="actions-col" width="20%">
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="customSwitch2">
											<label class="custom-control-label" for="customSwitch2">Disable</label>
										</div>
									</td>
									<td class="actions-col">
										<a title="Edit Record" href="#" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
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
</main>




<div class="modal fade bd-example-modal-lg" id="add-warehouse-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-warehouse') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
			<form method="post">
				<div class="modal-body add-lookup-modal-html">
					<div class="row">
						

						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_name" class="control-label">{{ trans('messages.warehouse-name') }}<span class="text-danger">*</span></label>
								<input type="text" name="warehouse_name" class="form-control" placeholder="{{ trans('messages.warehouse-name') }}"> 
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_code" class="control-label">{{ trans('messages.warehouse-code') }}<span class="text-danger">*</span></label>
								<input type="text" name="warehouse_code" class="form-control" placeholder="{{ trans('messages.warehouse-code') }}">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_short_code" class="control-label">{{ trans('messages.warehouse-address') }}<span class="text-danger">*</span></label>
								<textarea type="text" name="warehouse_short_code" class="form-control" placeholder="{{ trans('messages.warehouse-address') }}"></textarea>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								
								<label for="select_country_name" class="control-label">{{ trans('messages.country-name') }}<span class="text-danger">*</span></label>
								<select class="form-control" name="select_country_name">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">GERMANY</option>
									<option value="">FRANCE</option>
								</select>
							</div>
							
						</div>
					</div>
				</div>
				<div class="modal-footer justify-content-center">
					<button type="submit" class="btn bg-theme text-white action-button lookup-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
			</form>
		</div>

	</div>
</div>



<script>
	$("form").validate({
		errorClass: "invalid-input",
		rules: {
			warehouse_name: {
				required: true,
				noSpace: true
			},
			warehouse_code: {
				required: true,
				noSpace: true
			},
			warehouse_short_code:{
				required: true,
				noSpace: true
			},
			select_country_name: {
				required: true,
				noSpace: true
			},
		},
		messages: {
			warehouse_name: {
				required: "{{ trans('messages.require-warehouse-name') }}"
			},
			warehouse_code: {
				required: "{{ trans('messages.require-warehouse-code') }}"
			},
			warehouse_short_code: {
				required: "{{ trans('messages.require-warehouse-address') }}"
			},
			select_country_name: {
				required: "{{ trans('messages.select-country') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
</script>
@endsection