@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.add-logistic") }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ url('good-in-logistic') }}" class="category-add-link">{{ trans("messages.logistic") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ trans("messages.add-logistic") }}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card document-card mb-3">
                <ul class="document-items">
                    <li class="document-list"><a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
                    <li class="document-list"><a href="#transporter" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
                    <li class="document-list"><a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>

                </ul>
            </div>
            <div class="card mb-3" id="details">
                <h3 class="title-goods"><i class="fas fa-level-down-alt list-icon mr-2"></i>{{ trans("messages.goods-in-entry-details") }}</h3>
                <form method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="supplier_name">{{ trans("messages.supplier-name") }}<span class="text-danger">*</span></label>
                                    <select name="supplier_name" class="form-control select2">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="ABC (AHMEDABAD)">ABC (AHMEDABAD)</option>
                                        <option value="XYZ (SURAT)">XYZ (SURAT)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label for="collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}<span class="text-danger">*</span></label>
                                    <select name="collection_delivery" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Collection">{{ trans("messages.collection") }}</option>
                                        <option value="Delivery">{{ trans("messages.delivery") }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group mb-0">
                                    <div class="logistic-partner">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th style="max-width:70px;min-width:70px;">{{ trans("messages.select") }} </th>
                                                        <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                        <th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.entry-no") }} </th>
                                                        <th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.supplier-location") }} </th>
                                                        <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.order-date") }} </th>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.po-no-sales-invoice-no") }}</th>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.po-no-sales-invoice-amount") }}</th>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.current-buyer-delivery-status") }}</th>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.current-logistic") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center"><input class="" type="radio" name="radio1" id="radio1" value="option1" checked></td>
                                                        <td class="text-center" style="width:70px;min-width:70px;">1</td>
                                                        <td class="text-left">45</td>
                                                        <td class="text-left">Ahmedabad</td>
                                                        <td class="text-left">07/06/2022</td>
                                                        <td class="text-left">5687</td>
                                                        <td class="text-left">2000</td>
                                                        <td class="text-left">Cancelled</td>
                                                        <td class="text-left">Full</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center"><input class="" type="radio" name="radio1" id="radio1" value="option2"></td>
                                                        <td class="text-center" style="width:70px;min-width:70px;">2</td>
                                                        <td class="text-left">45</td>
                                                        <td class="text-left">Surat</td>
                                                        <td class="text-left">07/06/2022</td>
                                                        <td class="text-left">5687</td>
                                                        <td class="text-left">2000</td>
                                                        <td class="text-left">Full</td>
                                                        <td class="text-left">Partial</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center"><input class="" type="radio" name="radio1" id="radio1" value="option3"></td>
                                                        <td class="text-center" style="width:70px;min-width:70px;">3</td>
                                                        <td class="text-left">45</td>
                                                        <td class="text-left">Ahmedabad</td>
                                                        <td class="text-left">07/06/2022</td>
                                                        <td class="text-left">5687</td>
                                                        <td class="text-left">2000</td>
                                                        <td class="text-left">Partial</td>
                                                        <td class="text-left">Cancelled</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="delivery_type" class="control-label">{{ trans("messages.delivery-type") }}<span class="text-danger">*</span></label>
                                    <select name="delivery_type" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Full">{{ trans("messages.full") }}</option>
                                        <option value="Partial">{{ trans("messages.partial") }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="logistic-partner logistic-partner-collection">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th style="width:50px;min-width:50px;">{{ trans("messages.select") }} </th>
                                                        <th style="width:50px;min-width:50px;">{{ trans("messages.sr-no") }}</th>
                                                        <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.entry-no") }} </th>
                                                        <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.order-date") }} </th>
                                                        <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.po-no-sales-invoice-no") }}</th>
                                                        <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.po-no-sales-invoice-amount") }}</th>
                                                        <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.supplier-location-collection") }}</th>
                                                        <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.delivery-type") }}<span class="text-danger">*</span></th>
                                                        <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.delivery-location") }}<span class="text-danger">*</span></th>
                                                        <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.delivery-remarks") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            <div class="form-check form-check-inline pt-2 pb-2 mr-0">
                                                                <input class="form-check-input" type="checkbox" id="checkbox1" name="checkbox1" value="1" checked>
                                                                <label class="form-check-label" for="checkbox1"></label>
                                                            </div>
                                                        </td>
                                                        <td class="text-center" style="width:70px;min-width:70px;">1</td>
                                                        <td class="text-left">45</td>
                                                        <td class="text-left">07/06/2022</td>
                                                        <td class="text-left">5687</td>
                                                        <td class="text-left">2000</td>
                                                        <td class="text-left">Ahmedabad</td>
                                                        <td class="text-left">
                                                            <select name="collection_delivery_type" class="form-control">
                                                                <option value="">{{ trans("messages.select") }}</option>
                                                                <option value="Full">{{ trans("messages.full") }}</option>
                                                                <option value="Partial">{{ trans("messages.partial") }}</option>
                                                                <option value="Cancelled">{{ trans("messages.cancelled") }}</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-left">
                                                            <select name="delivery_location" class="form-control">
                                                                <option value="">{{ trans("messages.select") }}</option>
                                                                <option value="THE FUTURE CENTRE">THE FUTURE CENTRE</option>
                                                                <option value="ASTUTE UNIT 2B">ASTUTE UNIT 2B</option>
                                                                <option value="BCG POLAND">BCG POLAND</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="delivery_remarks" class="form-control" placeholder="{{ trans('messages.delivery-remarks') }}"></td>

                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <div class="form-check form-check-inline pt-2 pb-2 mr-0">
                                                                <input class="form-check-input" type="checkbox" id="checkbox2" name="checkbox2" value="1">
                                                                <label class="form-check-label" for="checkbox2"></label>
                                                            </div>
                                                        </td>
                                                        <td class="text-center" style="width:70px;min-width:70px;">2</td>
                                                        <td class="text-left">45</td>
                                                        <td class="text-left">07/06/2022</td>
                                                        <td class="text-left">5687</td>
                                                        <td class="text-left">2000</td>
                                                        <td class="text-left">Surat</td>
                                                        <td class="text-left">
                                                            <select name="collection_delivery_type" class="form-control">
                                                                <option value="">{{ trans("messages.select") }}</option>
                                                                <option value="Full">{{ trans("messages.full") }}</option>
                                                                <option value="Partial">{{ trans("messages.partial") }}</option>
                                                                <option value="Cancelled">{{ trans("messages.cancelled") }}</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-left">
                                                            <select name="delivery_location" class="form-control">
                                                                <option value="">{{ trans("messages.select") }}</option>
                                                                <option value="THE FUTURE CENTRE">THE FUTURE CENTRE</option>
                                                                <option value="ASTUTE UNIT 2B">ASTUTE UNIT 2B</option>
                                                                <option value="BCG POLAND">BCG POLAND</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="delivery_remarks" class="form-control" placeholder="{{ trans('messages.delivery-remarks') }}"></td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                    <select name="book_by" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="KARTIK SUTHAR (JD-BUYER)">KARTIK SUTHAR (JD-BUYER)</option>
                                        <option value="JIMIT PATEL (LOGISTIC)">JIMIT PATEL (LOGISTIC)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label for="logistic_partner" class="control-label">{{ trans("messages.logistic-partner") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="ABC">ABC</option>
                                        <option value="XYZ">XYZ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="collection_date">{{ trans("messages.collection-date") }}</label>
                                    <input type="text" name="collection_date" class="form-control date-format" placeholder="{{ trans('messages.collection-date') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }}</label>
                                    <input type="text" name="delivery_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-date') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label for="booking_ref_no" class="control-label">{{ trans("messages.booking-ref-no") }}</label>
                                    <input type="text" name="booking_ref_no" class="form-control" placeholder="{{ trans('messages.booking-ref-no') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label for="tracking_no" class="control-label">{{ trans("messages.tracking-no") }}</label>
                                    <input type="text" name="tracking_no" class="form-control" placeholder="{{ trans('messages.tracking-no') }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-4">
                                <div class="form-group">
                                    <label for="tracking_link" class="control-label">{{ trans("messages.tracking-link") }}</label>
                                    <input type="text" name="tracking_link" class="form-control" placeholder="{{ trans('messages.tracking-link') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label for="insurance_status" class="control-label">{{ trans("messages.insurance-status") }}</label>
                                    <select name="insurance_status" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="In House">{{ trans("messages.in-house") }}</option>
                                        <option value="Third Party">{{ trans("messages.third-party") }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="insurance_status_comments" class="control-label">{{ trans("messages.insurance-status-comments") }}</label>
                                    <input type="text" name="insurance_status_comments" class="form-control" placeholder="{{ trans('messages.insurance-status-comments') }}">
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
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
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
                    <div id="transporter">
                        <h4 class="title-goods"><i class="fa fa-file-invoice mr-2"></i> {{ trans("messages.transporter-invoice") }}</h4>
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group pb-3 pt-3">
                                        <div class="card shadow-none border">
                                            <div class="card-body logistic-partner">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-sm pb-4">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                                <th class="text-left" style="width:240px;min-width:240px;">{{ trans("messages.name") }} <span class="text-danger">*</span></th>
                                                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.inv-no") }} <span class="text-danger">*</span></th>
                                                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.freight") }}</th>
                                                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.custom") }}</th>
                                                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.duty") }}</th>
                                                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.other") }}</th>
                                                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.vat") }}</th>
                                                                <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.total") }}</th>
                                                                <th class="text-left" style="width:110px;min-width:110px;">{{ trans("messages.cov-rate") }}</th>
                                                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.final-total") }}</th>
                                                                <th class="text-left" style="width:250px;min-width:250px;">{{ trans("messages.attach-documents") }}</th>
                                                                <th class="text-center" style="width:80px;min-width:80px;">{{ trans("messages.documents") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="name" placeholder="{{ trans('messages.name') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="inv_no" placeholder="{{ trans('messages.inv-no') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="freight" placeholder="{{ trans('messages.freight') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="custom" placeholder="{{ trans('messages.custom') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="duty" placeholder="{{ trans('messages.duty') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="other" placeholder="{{ trans('messages.other') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="vat" placeholder="{{ trans('messages.vat') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="input-group align-items-center flex-nowrap">
                                                                        <label class="mb-0" for="">25000</label>
                                                                        <div class="input-group-prepend">
                                                                            <select class="form-control ml-2" name="amount">
                                                                                <option selected value="">Currency</option>
                                                                                <option value="GBP">GBP</option>
                                                                                <option value="USD">USD</option>
                                                                                <option value="EURO">EURO</option>
                                                                                <option value="ZLOTY">ZLOTY</option>
                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                </td>

                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="cov-rate" placeholder="{{ trans('messages.cov-rate') }}">
                                                                </td>
                                                                <td class="text-left">20050</td>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="document">
                                                                        <label class="custom-file-label" for="document">Choose file</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>2</td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="name" placeholder="{{ trans('messages.name') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="inv_no" placeholder="{{ trans('messages.inv-no') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="freight" placeholder="{{ trans('messages.freight') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="custom" placeholder="{{ trans('messages.custom') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="duty" placeholder="{{ trans('messages.duty') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="other" placeholder="{{ trans('messages.other') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="vat" placeholder="{{ trans('messages.vat') }}">
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="input-group align-items-center flex-nowrap">
                                                                        <label class="mb-0" for="">250</label>
                                                                        <div class="input-group-prepend">
                                                                            <select class="form-control ml-2" name="amount">
                                                                                <option selected value="">Currency</option>
                                                                                <option value="GBP">GBP</option>
                                                                                <option value="USD">USD</option>
                                                                                <option value="EURO">EURO</option>
                                                                                <option value="ZLOTY">ZLOTY</option>
                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="cov-rate" placeholder="{{ trans('messages.cov-rate') }}">
                                                                </td>
                                                                <td class="text-left">150</td>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="document">
                                                                        <label class="custom-file-label" for="document">Choose file</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-download"></i></a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="status">
                        <h4 class="title-goods"><i class="fab fa-stack-overflow mr-2"></i> {{ trans("messages.status") }}</h4>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="status">{{ trans("messages.status") }}<span class="text-danger">*</span></label>
                                        <select name="status" class="form-control">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="status_comments" class="control-label">{{ trans("messages.status-comments") }}</label>
                                        <input type="text" name="status_comments" class="form-control" placeholder="{{ trans('messages.status-comments') }}">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 submit-sticky">
                        <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                        <a href="{{ url('good-in-logistic') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
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
            supplier_name: {
                required: true
            },
            collection_delivery: {
                required: true
            },
            delivery_type: {
                required: true
            },
            collection_delivery_type: {
                required: true
            },
            delivery_location: {
                required: true
            },
            // delivery_date: {
            //     required: true
            // },
            book_by: {
                required: true
            },
            logistic_partner: {
                required: true
            },
            // collection_date: {
            //     required: true
            // },
            // booking_ref_no: {
            //     required: true
            // },
            // tracking_no: {
            //     required: true
            // },
            // tracking_link: {
            //     required: true
            // },
            // insurance_status: {
            //     required: true
            // },
            type: {
                required: true
            },
            name: {
                required: true
            },
            inv_no: {
                required: true
            },
            status: {
                required: true
            },

        },
        messages: {
            supplier_name: {
                required: "{{ trans('messages.require-supplier-name') }}"
            },
            collection_delivery: {
                required: "{{ trans('messages.require-collection-delivery') }}"
            },
            delivery_type: {
                required: "{{ trans('messages.require-delivery-type') }}"
            },
            collection_delivery_type: {
                required: "{{ trans('messages.require-delivery-type') }}"
            },
            delivery_location: {
                required: "{{ trans('messages.require-delivery-location') }}"
            },
            // delivery_date: {
            //     required: "{{ trans('messages.require-delivery-date') }}"
            // },
            book_by: {
                required: "{{ trans('messages.require-book-by') }}"
            },
            logistic_partner: {
                required: "{{ trans('messages.require-logistic-partner') }}"
            },
            // collection_date: {
            //     required: "{{ trans('messages.require-collection-date') }}"
            // },
            // booking_ref_no: {
            //     required: "{{ trans('messages.require-booking-ref-no') }}"
            // },
            // tracking_no: {
            //     required: "{{ trans('messages.require-tracking-no') }}"
            // },
            // tracking_link: {
            //     required: "{{ trans('messages.require-tracking-link') }}"
            // },
            // insurance_status: {
            //     required: "{{ trans('messages.require-insurance-status') }}"
            // },
            type: {
                required: "{{ trans('messages.require-type') }}"
            },
            name: {
                required: "{{ trans('messages.require-name') }}"
            },
            inv_no: {
                required: "{{ trans('messages.require-inv-no') }}"
            },
            status: {
                required: "{{ trans('messages.require-status') }}"
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