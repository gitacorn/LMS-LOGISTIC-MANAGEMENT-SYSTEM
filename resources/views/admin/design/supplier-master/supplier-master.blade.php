@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} <span>(10)</span> </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<a href="{{ url('add-supplier-master') }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-supplier') }}"><i class=" fas fa-plus mr-md-2"></i> <span class="d-md-block d-none">{{ trans('messages.add-supplier') }}</span></a>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-5 col-md-12">
							<div class="form-group">
								<label for="search_by_supplier_name" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_supplier_name" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.supplier-name') }}, {{ trans('messages.code') }}, {{ trans('messages.address') }}">
							</div>
						</div>
						<div class="col-xl-3 col-md-4">
							<div class="form-group">
								<label class="control-label" for="search_supplier_country">{{ trans("messages.supplier-country") }}</label>
								<select class="form-control" name="search_supplier_country">
									<option value="">Select</option>
									<option value="">India</option>
									<option value="">Canada</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.enable") }}</option>
									<option value="">{{ trans("messages.disable") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-3">
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
									<th class="text-left">{{ trans("messages.supplier-partner-name") }}</th>
									<th class="text-left">{{ trans("messages.supplier-code") }}</th>
									<th class="text-left">{{ trans("messages.supplier-address") }}</th>
									<th class="text-left">{{ trans("messages.supplier-country") }}</th>
									<th class="text-center">{{ trans("messages.status") }}</th>
									<th class="actions-col">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td>1</td>
									<td class="text-left">ABC</td>
									<td class="text-left">2589</td>
									<td class="text-left">Ahmedabad, Gujrat, India</td>
									<td class="text-left">India</td>
									<td>
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="customSwitch1">
											<label class="custom-control-label" for="customSwitch1">Enable</label>
										</div>
									</td>
									<td class="actions-col">
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
									</td>
								</tr>
								<tr>
									<td>2</td>
									<td class="text-left">NayanLal</td>
									<td class="text-left">2589</td>
									<td class="text-left">Ahmedabad, Gujrat, India</td>
									<td class="text-left">Canada</td>

									<td>
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="customSwitch2">
											<label class="custom-control-label" for="customSwitch2">Disable</label>
										</div>
									</td>
									<td class="actions-col">
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="{{ trans('messages.delete') }}" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
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


@endsection