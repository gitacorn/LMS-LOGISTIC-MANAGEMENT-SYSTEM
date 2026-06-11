@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} <span>(10)</span> </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<a href="javascript:void(0);" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.export-to-excel') }}"><i class="fa fa-file-excel mr-md-2"></i> <span class="d-md-block d-none">{{ trans('messages.export-to-excel') }}</span></a>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			$tableSearchPlaceholder = "Search By Entry No., PO No. / Sales Invoice No., Logistic Entry No., Tracking No., Tracking Link";
			?>

			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center">
						<div class="col-xl-6 col-md-12">
							<div class="form-group">
								<label for="search_by_logistic_partner_name" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_logistic_partner_name" id="search_by_logistic_partner_name" placeholder="<?php echo $tableSearchPlaceholder ?>">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_company">{{ trans("messages.buyer-company") }}</label>
								<select name="search_buyer_company" class="form-control select2" multiple="">
									<option value="">ACORN SOLUTION LTD. UK</option>
									<option value="">LONDON LUXURY PRODUCT LTD. UK</option>
									<option value="">JAMBO SUPPLIES LTD. UK</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_user_company">{{ trans("messages.user-company") }}</label>
								<select name="search_user_company" class="form-control select2" multiple="">
									<option value="">ACORN SOLUTION LTD. UK</option>
									<option value="">LONDON LUXURY PRODUCT LTD. UK</option>
									<option value="">JAMBO SUPPLIES LTD. UK</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_name">{{ trans("messages.buyer-name") }}</label>
								<select name="search_buyer_name" class="form-control select2" multiple>
									<option value="">KARTIK SUTHAR (JD-BUYER)</option>
									<option value="">JIMIT PATEL (LOGISTIC)</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_name">{{ trans("messages.supplier-name") }}</label>
								<select name="search_supplier_name" class="form-control select2" multiple>
									<option value="">ABC (AHMEDABAD)</option>
									<option value="">XYZ (SURAT)</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_location">{{ trans("messages.supplier-location") }}</label>
								<select name="search_supplier_location" class="form-control select2" multiple>
									<option value="">ABC (AHMEDABAD)</option>
									<option value="">XYZ (SURAT)</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_payment_status" class="control-label">{{ trans("messages.payment-status") }}</label>
								<select name="search_payment_status" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.paid") }}</option>
									<option value="">{{ trans("messages.partial-paid") }}</option>
									<option value="">{{ trans("messages.no-paid") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_order_from_date">{{ trans("messages.order-from-date") }}</label>
								<input type="text" name="search_order_from_date" class="form-control date-format" placeholder="{{ trans('messages.order-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_order_to_date">{{ trans("messages.order-to-date") }}</label>
								<input type="text" name="search_order_to_date" class="form-control date-format" placeholder="{{ trans('messages.order-to-date') }}">
							</div>
						</div>

						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}</label>
								<select name="search_collection_delivery" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.collection") }}</option>
									<option value="">{{ trans("messages.delivery") }}</option>
								</select>
							</div>
						</div>


						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_delivery_type" class="control-label">{{ trans("messages.delivery-type") }}</label>
								<select name="search_delivery_type" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.full") }}</option>
									<option value="">{{ trans("messages.partial") }}</option>
									<option value="">{{ trans("messages.cancelled") }}</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3  col-md-4 col-sm-6">
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
								<label for="search_logistic_partner" class="control-label">{{ trans("messages.logistic-partner") }}</label>
								<select name="search_logistic_partner" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="ABC">ABC</option>
									<option value="XYZ">XYZ</option>
								</select>
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
						<!-- <div class="col-md-2">
							<div class="form-group">
								<label class="control-label" for="search_payment_from_date">{{ trans("messages.payment-from-date") }}</label>
								<input type="text" name="search_payment_from_date" class="form-control date-format" placeholder="{{ trans('messages.payment-from-date') }}">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label" for="search_payment_to_date">{{ trans("messages.payment-to-date") }}</label>
								<input type="text" name="search_payment_to_date" class="form-control date-format" placeholder="{{ trans('messages.payment-to-date') }}">
							</div>
						</div> -->


						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_form_date">{{ trans("messages.delivery-from-date") }}</label>
								<input type="text" name="search_delivery_form_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="delivery_to_date">{{ trans("messages.delivery-to-date") }}</label>
								<input type="text" name="delivery_to_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-to-date') }}">
							</div>
						</div>



						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select name="search_status" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="ABC (AHMEDABAD)">Delivered</option>
									<option value="XYZ (SURAT)">Pending</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-xl-3">
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
									<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.order-date") }}</th>
									<th class="text-left">{{ trans("messages.entry-no") }}</th>
									<th class="text-left">{{ trans("messages.buyer-company") }}</th>
									<th class="text-left">{{ trans("messages.user-company") }}</th>
									<th class="text-left">{{ trans("messages.buyer-name") }}</th>
									<th class="text-left">{{ trans("messages.supplier-name") }} <br> {{ trans("messages.supplier-location") }}</th>
									<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.po-no-sales-invoice-no") }} <br> {{ trans("messages.po-no-sales-invoice-amount") }}</th>
									<th class="text-left">{{ trans("messages.payment-status") }} <br> {{ trans("messages.amount") }}</th>
									<th class="text-left">{{ trans("messages.collection-delivery") }}</th>
									<th class="text-left">{{ trans("messages.delivery-type") }}</th>
									<th class="text-left">{{ trans("messages.logistic-entry-no") }}</th>
									<th class="text-left">{{ trans("messages.book-by") }}</th>
									<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
									<th class="text-left">{{ trans("messages.collection-date") }}</th>
									<th class="text-left">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
									<th class="text-left">{{ trans("messages.delivery-date") }}</th>
									<th class="text-left">{{ trans("messages.final-total") }}</th>
									<th class="text-center">{{ trans("messages.status") }}</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td>1</td>
									<td class="text-left">9/22/2022</td>
									<td class="text-left">GIB-001-14092022-1 </td>
									<td class="text-left">ACORN SOLUTION LTD. UK</td>
									<td class="text-left">ACORN SOLUTION LTD. UK</td>
									<td class="text-left">KARTIK SUTHAR</td>
									<td class="text-left">Reliance <br> Surat</td>
									<td class="text-left">PO-550 <br> 3000 $</td>
									<td class="text-left">Paid <br> 3000 $</td>
									<td class="text-left">Delivery</td>
									<td class="text-left">Full</td>
									<td class="text-left">GIL-001-14092022</td>
									<td class="text-left">JIMIT PATEL</td>
									<td class="text-left">FedEX</td>
									<td class="text-left">9/22/2022</td>
									<td class="text-left">556 <br> fedex.com/track</td>
									<td class="text-left">9/25/2022</td>
									<td class="text-left">5500</td>
									<td class="text-left">Delivered</td>
								</tr>
								<tr>
									<td>2</td>
									<td class="text-left">9/22/2022</td>
									<td class="text-left">GIB-001-14092022-1 </td>
									<td class="text-left">ACORN SOLUTION LTD. UK</td>
									<td class="text-left">ACORN SOLUTION LTD. UK</td>
									<td class="text-left">KARTIK SUTHAR</td>
									<td class="text-left">Reliance <br> Surat</td>
									<td class="text-left">PO-550 <br> 3000 $</td>
									<td class="text-left">Paid <br> 3000 $</td>
									<td class="text-left">Delivery</td>
									<td class="text-left">Full</td>
									<td class="text-left">GIL-001-14092022</td>
									<td class="text-left">JIMIT PATEL</td>
									<td class="text-left">FedEX</td>
									<td class="text-left">9/22/2022</td>
									<td class="text-left">556 <br> fedex.com/track</td>
									<td class="text-left">9/25/2022</td>
									<td class="text-left">5500</td>
									<td class="text-left">Delivered</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

	</section>
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