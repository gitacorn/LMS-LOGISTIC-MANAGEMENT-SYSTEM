@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.add-buyer") }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ url('good-in-buyer') }}" class="category-add-link">{{ trans("messages.buyer") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ trans("messages.add-buyer") }}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card document-card mb-3">
                <ul class="document-items">
                    <li class="document-list"><a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>

                </ul>
            </div>
            <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-down-alt list-icon mr-2"></i>{{ trans("messages.goods-in-entry-details") }}</h3>
                <form method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="buyer_company">{{ trans("messages.buyer-company") }}<span class="text-danger">*</span></label>
                                    <select name="buyer_company" class="form-control select2">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="ACORN SOLUTION LTD. UK">ACORN SOLUTION LTD. UK</option>
                                        <option value="LONDON LUXURY PRODUCT LTD. UK">LONDON LUXURY PRODUCT LTD. UK</option>
                                        <option value="JAMBO SUPPLIES LTD. UK">JAMBO SUPPLIES LTD. UK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="user_company">{{ trans("messages.user-company") }}<span class="text-danger">*</span></label>
                                    <select name="user_company" class="form-control select2" multiple="">
                                        <option value="ACORN SOLUTION LTD. UK">ACORN SOLUTION LTD. UK</option>
                                        <option value="LONDON LUXURY PRODUCT LTD. UK">LONDON LUXURY PRODUCT LTD. UK</option>
                                        <option value="JAMBO SUPPLIES LTD. UK">JAMBO SUPPLIES LTD. UK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="buyer_name">{{ trans("messages.buyer-name") }}<span class="text-danger">*</span></label>
                                    <select name="buyer_name" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="KARTIK SUTHAR (JD-BUYER)">KARTIK SUTHAR (JD-BUYER)</option>
                                        <option value="JIMIT PATEL (LOGISTIC)">JIMIT PATEL (LOGISTIC)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="supplier_name">{{ trans("messages.supplier-name") }}<span class="text-danger">*</span></label>
                                    <select name="supplier_name" class="form-control select2">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="ABC (AHMEDABAD)">ABC (AHMEDABAD)</option>
                                        <option value="XYZ (SURAT)">XYZ (SURAT)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="supplier_location">{{ trans("messages.supplier-location") }}<span class="text-danger">*</span></label>
                                    <select name="supplier_location" class="form-control select2" multiple="">
                                        <option value="ABC (AHMEDABAD)">ABC (AHMEDABAD)</option>
                                        <option value="XYZ (SURAT)">XYZ (SURAT)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="order_date">{{ trans("messages.order-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="order_date" class="form-control date-format" placeholder="{{ trans('messages.order-date') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_no_sales_invoice_no">{{ trans("messages.po-no-sales-invoice-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="po_no_sales_invoice_no" class="form-control" placeholder="{{ trans('messages.po-no-sales-invoice-no') }}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_no_sales_invoice_amount">{{ trans("messages.po-no-sales-invoice-amount") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control mr-2" name="po_no_sales_invoice_amount">
                                                <option selected value="">Currency</option>
                                                <option value="GBP">GBP</option>
                                                <option value="USD">USD</option>
                                                <option value="EURO">EURO</option>
                                                <option value="ZLOTY">ZLOTY</option>
                                            </select>
                                        </div>
                                        <input tpe="text" class="form-control" aria-label="Text input with dropdown button" name="po_no_sales_invoice_amount" placeholder="{{ trans('messages.amount') }}">
                                    </div>
                                    <label style="display:none" id="po_no_sales_invoice_amount-error" class="invalid-input" for="po_no_sales_invoice_amount">Please Enter PO No. / Sales Invoice Amount</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="brand">{{ trans("messages.brand") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="brand" class="form-control" placeholder="{{ trans('messages.brand') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="payment_status" class="control-label">{{ trans("messages.payment-status") }}<span class="star">*</span></label>
                                    <select name="payment_status" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Paid">{{ trans("messages.paid") }}</option>
                                        <option value="Partial Paid">{{ trans("messages.partial-paid") }}</option>
                                        <option value="No Paid">{{ trans("messages.no-paid") }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="payment_date">{{ trans("messages.payment-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="payment_date" class="form-control date-format" placeholder="{{ trans('messages.payment-date') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="amount">{{ trans("messages.amount") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control mr-2" name="amount">
                                                <option selected value="">Currency</option>
                                                <option value="GBP">GBP</option>
                                                <option value="USD">USD</option>
                                                <option value="EURO">EURO</option>
                                                <option value="ZLOTY">ZLOTY</option>
                                            </select>
                                        </div>
                                        <input type="text" class="form-control" aria-label="Text input with dropdown button" name="po_no_sales_invoice_amount" placeholder="{{ trans('messages.amount') }}">
                                    </div>
                                    <label style="display:none" id="amount-error" class="invalid-input" for="amount">Please Select Amount</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="payment_terms">{{ trans("messages.payment-terms") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="payment_terms" class="form-control" placeholder="{{ trans('messages.payment-terms') }}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}<span class="star">*</span></label>
                                    <select name="collection_delivery" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Collection">{{ trans("messages.collection") }}</option>
                                        <option value="Delivery">{{ trans("messages.delivery") }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="delivery_type" class="control-label">{{ trans("messages.delivery-type") }}<span class="star">*</span></label>
                                    <select name="delivery_type" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Full">{{ trans("messages.full") }}</option>
                                        <option value="Partial">{{ trans("messages.partial") }}</option>
                                        <option value="cancelled">{{ trans("messages.cancelled") }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="booking_ref_no" class="control-label">{{ trans("messages.booking-ref-no") }}<span class="star">*</span></label>
                                    <input type="text" name="booking_ref_no" class="form-control" placeholder="{{ trans('messages.booking-ref-no') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="collection_reference_no" class="control-label">{{ trans("messages.collection-reference-no") }}</label>
                                    <input type="text" name="collection_reference_no" class="form-control" placeholder="{{ trans('messages.collection-reference-no') }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="delivery_location" class="control-label">{{ trans("messages.delivery-location") }}<span class="star">*</span></label>
                                    <select name="delivery_location" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="THE FUTURE CENTRE">THE FUTURE CENTRE</option>
                                        <option value="ASTUTE UNIT 2B">ASTUTE UNIT 2B</option>
                                        <option value="BCG POLAND">BCG POLAND</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="delivery_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-date') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_remarks">{{ trans("messages.delivery-remarks") }}</label>
                                    <input type="text" name="delivery_remarks" class="form-control" placeholder="{{ trans('messages.delivery-remarks') }}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="custom_procedure_export" class="control-label">{{ trans("messages.custom-procedure-export") }}<span class="star">*</span></label>
                                    <select name="custom_procedure_export" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Consigner Supplier">{{ trans("messages.consigner-supplier") }}</option>
                                        <option value="Consigner Ourside">{{ trans("messages.consigner-ourside") }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="custom_procedure_import" class="control-label">{{ trans("messages.custom-procedure-import") }}<span class="star">*</span></label>
                                    <select name="custom_procedure_import" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Consigner Supplier">{{ trans("messages.consigner-supplier") }}</option>
                                        <option value="Consigner Ourside">{{ trans("messages.consigner-ourside") }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="dangerous_goods" class="control-label">{{ trans("messages.dangerous-goods") }}<span class="star">*</span></label>
                                    <select name="dangerous_goods" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Yes">{{ trans("messages.yes") }}</option>
                                        <option value="No">{{ trans("messages.no") }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="goods_remarks">{{ trans("messages.goods-remarks") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="goods_remarks" class="form-control" placeholder="{{ trans('messages.goods-remarks') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="no_of_boxes">{{ trans("messages.no-of-boxes") }}</label>
                                    <input type="text" class="form-control" name="no_of_boxes" placeholder="{{ trans('messages.no-of-boxes') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="boxes_dimension">{{ trans("messages.boxes-dimension") }}</label>
                                    <select name="boxes_dimension" class="form-control">
                                        <option value="selest">{{ trans("messages.select") }}</option>
                                        <option value="PALLATE-1">PALLATE-1</option>
                                        <option value="PALLATE-2">PALLATE-2</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="no_of_pallets">{{ trans("messages.no-of-pallets") }}</label>
                                    <input type="text" class="form-control" name="no_of_pallets" placeholder="{{ trans('messages.no-of-pallets') }}">
                                </div>
                            </div>


                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallets_dimension">{{ trans("messages.pallets-dimension") }}</label>
                                    <select name="pallets_dimension" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="PALLATE-1">PALLATE-1</option>
                                        <option value="PALLATE-2">PALLATE-2</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallets_type">{{ trans("messages.pallets-type") }}</label>
                                    <select name="pallets_type" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Stackable">{{ trans("messages.stackable") }}</option>
                                        <option value="Not Stackable">{{ trans("messages.not-stackable") }}</option>
                                    </select>
                                </div>
                            </div>



                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="weight">{{ trans("messages.weight") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="weight" placeholder="{{ trans('messages.weight') }}">
                                        <div class="input-group-prepend">
                                            <select class="form-control ml-2" name="weight">
                                                <option selected>{{ trans("messages.select") }}</option>
                                                <option value="Kgs">{{ trans("messages.kgs") }}</option>
                                                <option value="Lbd">{{ trans("messages.lbs") }}</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="documents">
                        <h4 class="title-goods"><i class="fa fa-file list-icon mr-2"></i> {{ trans("messages.documents") }}</h4>
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group pb-3 pt-3">
                                        <div class="card shadow-none border">
                                            <div class="card-header">
                                                <span class="partner-tilte">
                                                    {{ trans("messages.attach-documents") }}
                                                </span>
                                            </div>
                                            <div class="card-body logistic-partner">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-sm pb-4">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                                <th style="max-width:250px;min-width:200px;">{{ trans("messages.type") }} <span class="text-danger">*</span></th>
                                                                <th style="max-width:250px;min-width:250px;">{{ trans("messages.documents") }} </th>
                                                                <th style="max-width:250px;min-width:200px;">{{ trans("messages.remarks") }} </th>
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.view") }}</th>
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.action") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center" style="width:70px;min-width:70px;">1</td>
                                                                <td class="text-left">
                                                                    <select name="type" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="PAN Card">PAN Card</option>
                                                                        <option value="Aadhar Card">Aadhar Card</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="document">
                                                                        <label class="custom-file-label" for="document">Choose file</label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="remarks">
                                                                </td>
                                                                <td class="actions-col">
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                </td>

                                                                <td style="width:70px;min-width:70px;"></td>
                                                            <tr>
                                                                <td class="text-center" style="width:70px;min-width:70px;">2</td>
                                                                <td class="text-left">
                                                                    <select name="type" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="PAN Card">PAN Card</option>
                                                                        <option value="Aadhar Card">Aadhar Card</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="document">
                                                                        <label class="custom-file-label" for="document">Choose file</label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="remarks">
                                                                </td>
                                                                <td class="actions-col">
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                </td>

                                                                <td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table"><i class="fa fa-trash fa-fw"></i></button></td>
                                                            </tr>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 submit-sticky">
                        <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                        <a href="{{ url('good-in-buyer') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
            </div>
            </form>
        </div>
        </div>
        </div>
    </section>
</main>
<script>
    $("form").validate({
        errorClass: "invalid-input",
        rules: {
            buyer_company: {
                required: true
            },
            user_company: {
                required: true
            },
            buyer_name: {
                required: true
            },
            supplier_name: {
                required: true
            },
            supplier_location: {
                required: true
            },
            order_date: {
                required: true
            },
            po_no_sales_invoice_no: {
                required: true
            },
            po_no_sales_invoice_amount: {
                required: true
            },
            brand: {
                required: true
            },
            payment_status: {
                required: true
            },
            payment_date: {
                required: true
            },
            amount: {
                required: true
            },
            payment_terms: {
                required: true
            },
            collection_delivery: {
                required: true
            },
            delivery_type: {
                required: true
            },
            booking_ref_no: {
                required: true
            },
            delivery_location: {
                required: true
            },
            delivery_date: {
                required: true
            },
            delivery_remarks: {
                required: true
            },
            custom_procedure_export: {
                required: true
            },
            custom_procedure_import: {
                required: true
            },
            dangerous_goods: {
                required: true
            },
            goods_remarks: {
                required: true
            },
            // no_of_boxes: {
            //     required: true
            // },
            // no_of_pallets: {
            //     required: true
            // },
            // pallets_type: {
            //     required: true
            // },
            boxes_dimension: {
                required: true
            },
            // pallets_dimension: {
            //     required: true
            // },
            weight: {
                required: true
            },
            type: {
                required: true
            },
        },
        messages: {
            buyer_company: {
                required: "{{ trans('messages.require-buyer-company') }}"
            },
            user_company: {
                required: "{{ trans('messages.require-user-company') }}"
            },
            buyer_name: {
                required: "{{ trans('messages.require-buyer-name') }}"
            },
            supplier_name: {
                required: "{{ trans('messages.require-supplier-name') }}"
            },
            supplier_location: {
                required: "{{ trans('messages.require-supplier-location') }}"
            },
            order_date: {
                required: "{{ trans('messages.require-order-date') }}"
            },
            po_no_sales_invoice_no: {
                required: "{{ trans('messages.require-po-no-sales-invoice-no') }}"
            },
            po_no_sales_invoice_amount: {
                required: "{{ trans('messages.require-po-no-sales-invoice-amount') }}"
            },
            brand: {
                required: "{{ trans('messages.require-brand') }}"
            },
            payment_status: {
                required: "{{ trans('messages.require-payment-status') }}"
            },
            payment_date: {
                required: "{{ trans('messages.require-payment-date') }}"
            },
            amount: {
                required: "{{ trans('messages.require-amount') }}"
            },
            payment_terms: {
                required: "{{ trans('messages.require-payment-terms') }}"
            },
            collection_delivery: {
                required: "{{ trans('messages.require-collection-delivery') }}"
            },
            delivery_type: {
                required: "{{ trans('messages.require-delivery-type') }}"
            },
            booking_ref_no: {
                required: "{{ trans('messages.require-booking-ref-no') }}"
            },
            delivery_location: {
                required: "{{ trans('messages.require-delivery-location') }}"
            },
            delivery_date: {
                required: "{{ trans('messages.require-delivery-date') }}"
            },
            delivery_remarks: {
                required: "{{ trans('messages.require-delivery-remarks') }}"
            },
            custom_procedure_export: {
                required: "{{ trans('messages.require-custom-procedure-export') }}"
            },
            custom_procedure_import: {
                required: "{{ trans('messages.require-custom-procedure-import') }}"
            },
            dangerous_goods: {
                required: "{{ trans('messages.require-dangerous-goods') }}"
            },
            goods_remarks: {
                required: "{{ trans('messages.require-goods-remarks') }}"
            },
            // no_of_boxes: {
            //     required: "{{ trans('messages.require-no-of-boxes') }}"
            // },
            // no_of_pallets: {
            //     required: "{{ trans('messages.require-no-of-pallets') }}"
            // },
            pallets_type: {
                required: "{{ trans('messages.require-pallets-type') }}"
            },
            // boxes_dimension: {
            //     required: "{{ trans('messages.require-box-dimension') }}"
            // },
            // pallets_dimension: {
            //     required: "{{ trans('messages.require-pallets-dimension') }}"
            // },
            weight: {
                required: "{{ trans('messages.require-weight') }}"
            },
            type: {
                required: "{{ trans('messages.require-type') }}"
            },
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
            minDate: moment().startOf('d'),
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });

    });
</script>
@endsection