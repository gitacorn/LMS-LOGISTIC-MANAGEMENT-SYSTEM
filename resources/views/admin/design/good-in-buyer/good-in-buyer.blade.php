@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} <span>(10)</span> </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<a href="{{ url('add-good-in-buyer') }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-buyer') }}"><i class=" fas fa-plus mr-md-2"></i> <span class="d-md-block d-none">{{ trans('messages.add-buyer') }}</span></a>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<?php
			$tableSearchPlaceholder = "Search By Entry No., PO No. / Sales Invoice No., Brand, Payment Terms, Booking Ref. No., Collection Ref. No., Goods Remarks";
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
								<label class="control-label" for="search_payment_from_date">{{ trans("messages.payment-from-date") }}</label>
								<input type="text" name="search_payment_from_date" class="form-control date-format" placeholder="{{ trans('messages.payment-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_payment_to_date">{{ trans("messages.payment-to-date") }}</label>
								<input type="text" name="search_payment_to_date" class="form-control date-format" placeholder="{{ trans('messages.payment-to-date') }}">
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
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_delivery_location" class="control-label">{{ trans("messages.delivery-location") }}</label>
								<select name="search_delivery_location" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">THE FUTURE CENTRE</option>
									<option value="">ASTUTE UNIT 2B</option>
									<option value="">BCG POLAND</option>
								</select>
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
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_custom_procedure_export" class="control-label">{{ trans("messages.custom-procedure-export") }}</label>
								<select name="search_custom_procedure_export" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Consigner Supplier">{{ trans("messages.consigner-supplier") }}</option>
									<option value="Consigner Ourside">{{ trans("messages.consigner-ourside") }}</option>
								</select>
							</div>
						</div>

						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_custom_procedure_import" class="control-label">{{ trans("messages.custom-procedure-import") }}</label>
								<select name="search_custom_procedure_import" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Consigner Supplier">{{ trans("messages.consigner-supplier") }}</option>
									<option value="Consigner Ourside">{{ trans("messages.consigner-ourside") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_dangerous_goods" class="control-label">{{ trans("messages.dangerous-goods") }}</label>
								<select name="search_dangerous_goods" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Yes">{{ trans("messages.yes") }}</option>
									<option value="No">{{ trans("messages.no") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="search_boxes_dimension">{{ trans("messages.boxes-dimension") }}</label>
                                    <select name="search_boxes_dimension" class="form-control">
                                        <option value="selest">{{ trans("messages.select") }}</option>
                                        <option value="PALLATE-1">PALLATE-1</option>
                                        <option value="PALLATE-2">PALLATE-2</option>
                                    </select>
                                </div>
                            </div>
							<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="search_pallets_dimension">{{ trans("messages.pallets-dimension") }}</label>
                                    <select name="search_pallets_dimension" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="PALLATE-1">PALLATE-1</option>
                                        <option value="PALLATE-2">PALLATE-2</option>
                                    </select>
                                </div>
                            </div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_pallets_type">{{ trans("messages.pallets-type") }}</label>
								<select name="search_pallets_type" class="form-control">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="Stackable">{{ trans("messages.stackable") }}</option>
									<option value="Not Stackable">{{ trans("messages.not-stackable") }}</option>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="">{{ trans("messages.enable") }}</option>
									<option value="">{{ trans("messages.disable") }}</option>
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
									<th style="max-width:150px;min-width:150px;">{{ trans("messages.actions") }}</th>
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
									<td class="actions-col">
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
										<button title="View" class="btn btn-sm bg-theme text-white mb-1"><i class="fa fa-eye"></i></button>
										<button title="Cancel" class="btn btn-sm btn-warning mb-1"><i class="fa fa-times"></i></button>

									</td>
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
									<td class="actions-col">
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
										<button title="View" class="btn btn-sm bg-theme text-white mb-1"><i class="fa fa-eye"></i></button>
										<button title="Cancel" class="btn btn-sm btn-warning mb-1"><i class="fa fa-times"></i></button>
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