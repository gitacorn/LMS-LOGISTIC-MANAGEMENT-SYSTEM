@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
		<?php 
			if((checkPermission(config('permission_constants.ADD_LOGISTIC_PARTNER')) != false)){
        	?>
			<a href="{{ config('constants.LOGISTIC_PARTNER_MASTER_URL') . '/create' }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-logistic-partner') }}"><i class=" fas fa-plus mr-md-2"></i> <span class="d-md-block d-none">{{ trans('messages.add-logistic-partner') }}</span></a>
			<?php 
			}?>
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
								<label for="search_by_logistic_partner_name" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.logistic-partner-name') }}, {{ trans('messages.code') }}, {{ trans('messages.address') }}, {{ trans('messages.contact-person-name') }}, {{ trans('messages.contact-mobile') }}, {{ trans('messages.contact-email')  }}">
							</div>
						</div>
						<div class="col-xl-3 col-md-4">
							<div class="form-group">
								<label class="control-label" for="search_logistic_partner_country">{{ trans("messages.logistic-partner-country") }}</label>
								<select class="form-control" name="search_logistic_partner_country" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
										if(!empty($countryRecordDetails)){
											foreach ($countryRecordDetails as $countryRecordDetail){
												?>
												<option value="<?php echo Wild_tiger::encode($countryRecordDetail->i_id)?>"><?php echo $countryRecordDetail->v_country_name?></option>
	                         
												<?php 
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status" onchange="filterData()">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.ENABLE_STATUS')}}">{{ trans("messages.enable") }}</option>
									<option value="{{ config('constants.DISABLE_STATUS')}}">{{ trans("messages.disable") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData()">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper">
				<div class="card card-body">
				{{ Wild_tiger::readMessage() }}
					<div class="table-responsive fixed-table-x">
						<table class="table table-sm table-bordered table-hover" id="user-table">
							<thead>
								<tr>
									<th class="sr-col">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner-name") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner-code") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner-address") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner-country") }}</th>
									<th class="text-left">{{ trans("messages.contact-person-name") }}</th>
                                    <th class="text-left">{{ trans("messages.contact-mobile") }}</th>
                                    <th class="text-left">{{ trans("messages.contact-email") }}</th>
									<th class="text-center">{{ trans("messages.status") }}</th>
									<?php 
									if( (checkPermission(config('permission_constants.EDIT_LOGISTIC_PARTNER')) != false) || (checkPermission(config('permission_constants.DELETE_LOGISTIC_PARTNER')) != false) ){?>
	                                	<th class="actions-col">{{ trans("messages.actions") }}</th>
									<?php 
									}?>
								</tr>
							</thead>

							<tbody class="ajax-view">
                            	 @include( config('constants.AJAX_VIEW_FOLDER')  . 'logistic-partner-master/logistic-partner-master-list')
                            </tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
</main>
<script>
var partner_module_url = '{{config("constants.LOGISTIC_PARTNER_MASTER_URL")}}' + '/';

function searchField(){
	var search_by_logistic_partner_name = $.trim($('[name="search_by_logistic_partner_name"]').val());
	var search_logistic_partner_country = $.trim($('[name="search_logistic_partner_country"]').val());
	var search_status = $.trim($('[name="search_status"]').val());
	var searchData = {
            'search_by_logistic_partner_name':search_by_logistic_partner_name,
            'search_logistic_partner_country': search_logistic_partner_country,
            'search_status': search_status,
        }
        return searchData;
}
function filterData(){
	var searchFieldName = searchField();

	searchAjax(partner_module_url + 'filter' , searchFieldName);
}
var paginationUrl = partner_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection