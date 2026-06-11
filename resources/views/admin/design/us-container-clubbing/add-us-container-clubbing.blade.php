@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.add-usa-container-clubbing") }}</h1>
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
            <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
                <form method="post" id="add-usa-container-form">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="type">{{ trans("messages.type") }}<span class="text-danger">*</span></label>
                                    <select name="type" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="{{ trans('messages.amazon') }}">{{ trans("messages.amazon") }}</option>
                                        <option value="{{ trans('messages.customer') }}">{{ trans("messages.customer") }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="from_warehouse">{{ trans("messages.from-warehouse") }}<span class="text-danger">*</span></label>
                                    <select name="from_warehouse" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Warehouse 1">Warehouse 1</option>
                                        <option value="Warehouse 2">Warehouse 2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="to_location">{{ trans("messages.to-location") }}<span class="text-danger">*</span></label>
                                    <select name="to_location" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Location 1">Location 1</option>
                                        <option value="Location 2">Location 2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="box_pallet">{{ trans("messages.box-pallet") }}<span class="text-danger">*</span></label>
                                    <select name="box_pallet" class="form-control select2" multiple>
                                        <option value="{{ trans('messages.box') }}">{{ trans("messages.box") }}</option>
                                        <option value="{{ trans('messages.pallet') }}">{{ trans("messages.pallet") }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_box">{{ trans("messages.total-box") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="total_box" class="form-control" placeholder="{{ trans('messages.total-box') }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_pallet">{{ trans("messages.total-pallet") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="total_pallet" class="form-control" placeholder="{{ trans('messages.total-pallet') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="card shadow-none border">
                                        <div class="card-header">
                                            <span class="partner-tilte">
                                                {{ trans("messages.select-container") }}
                                            </span>
                                        </div>
                                        <div class="logistic-partner card-body logistic-partner-collection table-responsive-date">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered table-sm pb-4">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.select") }} </th>
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.sr-no") }}</th>
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.entry-no") }}</th>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.account") }}</th>
                                                            <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.fba") }} </th>
                                                            <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.ref-id") }}</th>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.status") }} </th>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.from") }} </th>
                                                           
                                                            <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.to") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.sku") }}
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.unit") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.products") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.boxes-pallets") }}</th>
                                                            <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.final-boxes-pallets") }}<span class="text-danger">*</span></th>
                                                            <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.number-of-box-pallet") }}<span class="text-danger">*</span></th>
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
                                                            <td class="text-left">CON-001-06012023</td>
                                                            <td class="text-left">154684646</td>
                                                            <td class="text-left">FBA16HZ7GP4R</td>
                                                            <td class="text-left">118CHX5Q</td>
                                                            <td class="text-left">In Transit</td>
                                                            <td class="text-left">SUBHA</td>
                                                            <td class="text-left">Neil Warehouse</td>
                                                            <td class="text-left">8</td>
                                                            <td class="text-left">1284</td>
                                                            <td class="text-left">Lorem, ipsum dolor.</td>
                                                            <td class="text-left">{{ trans("messages.box") }} - 4</td>
                                                            <td class="text-left">
                                                                <select name="final_boxes_pallets" class="form-control">
                                                                    <option value="">{{ trans("messages.select") }}</option>
                                                                    <option value="{{ trans('messages.box') }}">{{ trans("messages.box") }}</option>
                                                                    <option value="{{ trans('messages.pallet') }}">{{ trans("messages.pallet") }}</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-left"><input type="text" name="number_of_box_pallet" class="form-control" placeholder="{{ trans('messages.number-of-box-pallet') }}"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">
                                                                <div class="form-check form-check-inline pt-2 pb-2 mr-0">
                                                                    <input class="form-check-input" type="checkbox" id="checkbox2" name="checkbox2" value="1">
                                                                    <label class="form-check-label" for="checkbox2"></label>
                                                                </div>
                                                            </td>
                                                            <td class="text-center" style="width:70px;min-width:70px;">2</td>
                                                            <td class="text-left">CON-001-06012023</td>
                                                            <td class="text-left">154684646</td>
                                                            <td class="text-left">FBA16HZ7GP4R</td>
                                                            <td class="text-left">118CHX5Q</td>
                                                            <td class="text-left">In Transit</td> 
                                                            <td class="text-left">SUBHA</td>
                                                            <td class="text-left">Neil Warehouse</td>
                                                            <td class="text-left">8</td>
                                                            <td class="text-left">1284</td>
                                                            <td class="text-left">Lorem, ipsum dolor.</td>
                                                            <td class="text-left">{{ trans("messages.pallet") }} - 4</td>
                                                            <td class="text-left">
                                                                <select name="final_boxes_pallets" class="form-control">
                                                                    <option value="">{{ trans("messages.select") }}</option>
                                                                    <option value="{{ trans('messages.box') }}">{{ trans("messages.box") }}</option>
                                                                    <option value="{{ trans('messages.pallet') }}">{{ trans("messages.pallet") }}</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-left"><input type="text" name="number_of_box_pallet" class="form-control" placeholder="{{ trans('messages.number-of-box-pallet') }}"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.booking-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="booking_date" class="form-control" placeholder="{{ trans('messages.booking-date') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.booking-portal") }}</label>
                                    <select name="booking_portal" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Booking Portal">Booking Portal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.carrier-company") }}<span class="text-danger">*</span></label>
                                    <select name="carrier_company" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Carrier Company 1">Carrier Company 1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.tracking-no") }}</label>
                                    <input type="text" name="tracking_no" class="form-control" placeholder="{{ trans('messages.tracking-no') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.pro-number") }}</label>
                                    <input type="text" name="pro_number" class="form-control" placeholder="{{ trans('messages.pro-number') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.logistic-cost-usd") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="logistic_cost_usd" class="form-control" placeholder="{{ trans('messages.logistic-cost-usd') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.collection-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="collection_date" class="form-control" placeholder="{{ trans('messages.collection-date') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.delivery-date") }}</label>
                                    <input type="text" name="delivery_date" class="form-control" placeholder="{{ trans('messages.delivery-date') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.weight-lbs") }}</label>
                                    <input type="text" name="weight_lbs" class="form-control" placeholder="{{ trans('messages.weight-lbs') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.comments") }}</label>
                                    <input type="text" name="comments" class="form-control" placeholder="{{ trans('messages.comments') }}">
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
                                                                <th style="max-width:350px;min-width:350px;">{{ trans("messages.documents") }} </th>
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
                                                                        <input type="file" class="custom-file-input" id="document" multiple>
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
                                                                        <input type="file" class="custom-file-input" id="document" multiple>
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
                                                    <button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
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
                                                                <td class="text-left"></td>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="document" multiple>
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
                                                                <td class="text-left"></td>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="document" multiple>
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
                        <a href="{{ url('us-container-clubbing/design-us-container-clubbing') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>

                </form>
            </div>

        </div>
    </section>
</main>
<script>
    $("#add-usa-container-form").validate({
        errorClass: "invalid-input",
        rules: {
            warehouse: {
                required: true
            },
            from_warehouse: {
                required: true
            },
            to_location: {
                required: true
            },
            total_box: {
                required: true
            },
            
            total_pallet: {
                required: true
            },
            booking_date: {
                required: true
            },
            type: {
                required: true
            },
            name: {
                required: true
            },
            status: {
                required: true
            },
            inv_no: {
                required: true
            },
            collection_date: {
                required: true
            },
            carrier_company: {
                required: true
            },
            number_of_box_pallet: {
                required: true
            },
            final_boxes_pallets: {
                required: true
            },
            box_pallet: {
                required: true
            },
            logistic_cost_usd: {
                required: true
            },
            
            
            
        },
        messages: {
            
            warehouse: {
                required: "{{ trans('messages.require-warehouse') }}"
            },
            from_warehouse: {
                required: "{{ trans('messages.require-from-warehouse') }}"
            },
            to_location: {
                required: "{{ trans('messages.require-to-location') }}"
            },
            total_box: {
                required: "{{ trans('messages.require-total-box') }}"
            },
            total_pallet: {
                required: "{{ trans('messages.require-total-pallet') }}"
            },
            booking_date: {
                required: "{{ trans('messages.require-booking-date') }}"
            },
            type: {
                required: "{{ trans('messages.require-type') }}"
            },
            name: {
                required: "{{ trans('messages.require-name') }}"
            },
            status: {
                required: "{{ trans('messages.require-status') }}"
            },
            inv_no:{
                required: "{{ trans('messages.require-inv-no') }}"
            },
            carrier_company:{
                required: "{{ trans('messages.require-carrier-company') }}"
            },
            logistic_cost_usd:{
                required: "{{ trans('messages.require-logistic-cost-usd') }}"
            },
            collection_date:{
                required: "{{ trans('messages.require-collection-date') }}"
            },
            number_of_box_pallet:{
                required: "{{ trans('messages.require-number-of-box-pallet') }}"
            },
            final_boxes_pallets:{
                required: "{{ trans('messages.require-final-box-pallet') }}"
            },
            box_pallet:{
                required: "{{ trans('messages.require-box-pallet') }}"
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
        $("[name='collection_date'], [name='delivery_date'], [name='booking_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
        });
    });
</script>
@endsection