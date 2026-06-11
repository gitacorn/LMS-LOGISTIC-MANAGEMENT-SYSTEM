@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/dataTables.bootstrap4.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset ('js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/dataTables.bootstrap4.js') }}"></script>


<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			@if(checkPermission(config('permission_constants.EXCEL_EXPORT_UK_TO_AMAZON_USA_FBA')) != false)
				<button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-md-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
			@endif
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-6 col-md-12">
							<div class="form-group">
								<label for="search_by_fba_no" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_fba_no" id="search_by_fba_no" placeholder="{{ trans("messages.search-by") }}">
							</div>
						</div>
						
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-xl-3">
							<button type="button" title="{{ trans('messages.search') }}" onclick="filterData()" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body card-pagination-items-class">
					<div class="table-responsive fba-report-table fixed-tabel">
						<table class="table table-sm table-bordered table-hover" id="fba-table">
							<thead>
								<tr>
									<th class="sr-col text-center">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.entry-no") }}</th>
									<th class="text-left">{{ trans("messages.container-no") }} <br> {{ trans("messages.status") }}</th>
									<th class="text-left">{{ trans("messages.way-of-transport") }}</th>
									<th class="text-left">{{ trans("messages.from-port-airport") }}</th>
									<th class="text-left">{{ trans("messages.to-port-airport") }}</th>
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
							<tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'tracking-goods-in/fba-report-list')
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<script>

 	var fba_report_url = '{{config("constants.FBA_REPORT_URL")}}' + '/';
 	function searchField(){
	 	var search_by_fba_no = $.trim($('[name="search_by_fba_no"]').val());
	 
	 	var searchData = {
	     	'search_by_fba_no':search_by_fba_no
	        
	 	}
	     return searchData;
	 }
	 function filterData(){
	 	var searchFieldName = searchField();
	 	searchAjax(fba_report_url + 'fbaReportFilter' , searchFieldName);
	 }
	 function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = fba_report_url + 'fbaReportFilter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}
</script>

@endsection