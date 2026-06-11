@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (10)</h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<button type="button" class="btn btn btn-theme text-white button-actions-top-bar  d-flex align-items-center border btn-sm mr-2" data-toggle="modal" data-target="#add-dimension-modal"><i class=" fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans('messages.add-dimension') }} </span></a>
				<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
					<div class="col-xl-3 col-lg-6">
							<div class="form-group">
						        <label class="control-label" for="search_dimension_type">{{ trans("messages.dimension-type") }}</label>
								<select class="form-control" name="search_dimension_type">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.box") }}</option>
									<option value="">{{ trans("messages.pallet") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-4 col-lg-6">
							<div class="form-group">
								<label for="search_by_dimension" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_dimension" placeholder="{{ trans("messages.search-by") }} {{ trans("messages.dimension-name") }} , {{ trans("messages.dimension-size") }}">
							</div>
						</div>
						
						<div class="col-xl-3 col-lg-6">
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
						<table class="table table-sm table-bordered table-hover" id="dimension-table">
							<thead>
								<tr>
									<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.dimension-type") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.dimension-name") }}</th>
									<th class="text-left" width="55%">{{ trans("messages.dimension-size") }}</th>
									<th class="text-center" width="30%">{{ trans("messages.status") }}</th>
									<th class="actions-col" width="20%">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td class="sr-col" width="5%">1</td>
									<td class="text-left" width="55%">Box</td>
									<td class="text-left" width="55%">Box-1</td>
									<td class="text-left" width="30%">100*120*5</td>
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
									<td class="text-left" width="55%">Box</td>
									<td class="text-left" width="55%">Box-2</td>
									<td class="text-left" width="30%">100*120*10</td>
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
									<td class="sr-col" width="5%">3</td>
									<td class="text-left" width="55%">Pallet</td>
									<td class="text-left" width="55%">PALLATE-1</td>
									<td class="text-left" width="30%">120*100*120</td>
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




<div class="modal fade bd-example-modal-lg" id="add-dimension-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.add-dimension') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
			</div>
			<form method="post">
				<div class="modal-body add-lookup-modal-html">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="dimension_type" class="control-label">{{ trans('messages.dimension-type') }}<span class="text-danger">*</span></label>
								<div class="radio-boxes form-row p-1 bg-white">
									<div class="radio-box col-lg-4 col-6 mb-2">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="dimension_type" id="box" checked>
											<label class="form-check-label custom-type-label btn stock-btn" for="box">Box</label>
										</div>
									</div>
									<div class="radio-box col-lg-4 col-6 mb-2">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="dimension_type" id="pallet">
											<label class="form-check-label custom-type-label btn stock-btn" for="pallet">Pallet</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label for="dimension_name" class="control-label">{{ trans('messages.dimension-name') }}<span class="text-danger">*</span></label>
								<input type="text" name="dimension_name" class="form-control" placeholder="{{ trans('messages.dimension-name') }}"> 
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="dimension_size" class="control-label">{{ trans('messages.dimension-size') }}<span class="text-danger">*</span></label>
								<input type="text" name="dimension_size" class="form-control" placeholder="{{ trans('messages.dimension-size') }}">
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
			dimension_name: {
				required: true,
				noSpace: true
			},
			dimension_size: {
				required: true,
				noSpace: true
			},
			dimension_type: {
				required: true,
				noSpace: true
			},

		},
		messages: {
			dimension_name: {
				required: "{{ trans('messages.require-dimension-name') }}"
			},
			dimension_size: {
				required: "{{ trans('messages.require-dimension-size') }}"
			},
			dimension_type: {
				required: "{{ trans('messages.require-dimension-size') }}"
			},

		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
</script>
@endsection