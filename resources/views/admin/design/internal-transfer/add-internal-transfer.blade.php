@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.add-internal-transfer") }}</h1>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card document-card mb-3">
                <ul class="document-items">
                    <li class="document-list"><a href="#details"   class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
                    <li class="document-list"><a href="#transporter" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
                    <li class="document-list"><a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>
                </ul>
            </div>
    <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
                <form method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="way_of_transport">{{ trans("messages.way-of-transport") }}<span class="text-danger">*</span></label>
                                    <select name="way_of_transport" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Air">Air</option>
                                        <option value="Sea">Sea</option>
                                        <option value="Truck">Truck</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                    <select name="book_by" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="KARTIK SUTHAR (JD-BUYER)">KARTIK SUTHAR (JD-BUYER)</option>
                                        <option value="JIMIT PATEL (LOGISTIC)">JIMIT PATEL (LOGISTIC)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="logistic_partner">{{ trans("messages.logistic-partner") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner" class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="Airport">NEWYORK</option>
                                        <option value="Port">UK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="booking_date">{{ trans("messages.booking-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="booking_date" class="form-control date-format" placeholder="{{ trans('messages.booking-date') }}">

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="reference_no" class="control-label">{{ trans("messages.reference-no") }}</label>
                                    <input type="text" name="reference_no" class="form-control" placeholder="{{ trans('messages.reference-no') }}">

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="No_of_pallets">{{ trans("messages.no-of-pallets") }}</label>
                                    <input type="number" name="No_of_pallets" class="form-control" placeholder="{{ trans('messages.no-of-pallets') }}">

                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="weight">{{ trans("messages.weight") }}</label>
                                    <input type="text" name="wight" class="form-control" placeholder="{{ trans('messages.weight') }}">
                                </div>
                            </div>
                        </div>
                        <div class="pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group pb-3 pt-3">
                                        <div class="card shadow-none border">
                                            <div class="card-header">
                                                <span class="partner-tilte">
                                                    {{ trans("messages.transfer-details") }}
                                                </span>
                                            </div>
                                            <div class="card-body logistic-partner">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-sm pb-4">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                                <th style="max-width:200px;min-width:200px;">{{ trans("messages.invoice-no-ref-no") }} <span class="text-danger">*</span></th>
                                                                <th style="max-width:200px;min-width:200px;">{{ trans("messages.account") }}<span class="text-danger">*</span></th>
                                                                <th style="max-width:220px;min-width:220px;">{{ trans("messages.from-warehouse") }} <span class="text-danger">*</span></th>
                                                                <th style="width:200px;min-width:200px;">{{ trans("messages.to-warehouse") }} <span class="text-danger">*</span></th>
                                                                <th style="width:120px;min-width:120px;">{{ trans("messages.unit") }}<span class="text-danger">*</span></th>
                                                                <th style="width:75px;min-width:75pxpx;">{{ trans("messages.action") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center">1</td>
                                                                <td class="text-left">
                                                                <input type="text" class="form-control" name="invoice_no_ref_no">
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="account" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="PAN Card">12345</option>
                                                                        <option value="Aadhar Card">12345</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="from_warehouse" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="Uk-1">Uk-1</option>
                                                                        <option value="Uk-2">Uk-2</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="to_warehouse" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="US-1">US-1</option>
                                                                        <option value="US-2">US-2</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                <input type="text" class="form-control" name="unit">
                                                                </td>
                                                                <td class="text-left">
                                                                <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1 d-none"><i class="fa fa-trash fa-fw"></i></a>
                                                                
                                                                </td>                                                     
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center" >2</td>
                                                                <td class="text-left">
                                                                <input type="text" class="form-control" name="invoice_no_ref_no">
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="account" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="PAN Card">12345</option>
                                                                        <option value="Aadhar Card">12345</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="from-warehouse" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="Uk-1">Uk-1</option>
                                                                        <option value="Uk-2">Uk-2</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="to-warehouse" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="US-1">US-1</option>
                                                                        <option value="US-2">US-2</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                <input type="text" class="form-control" name="unit">
                                                                </td>
                                                                <td class="actions-col" style="width:75px;min-width:75px;">
                                                                    <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></a>
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
                        <div class="row">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="tracking_no">{{ trans("messages.tracking-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="tracking_no" class="form-control" placeholder="{{ trans('messages.tracking-no') }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="tracking_link">{{ trans("messages.tracking-link") }}</label>
                                    <input type="text" name="tracking_link" class="form-control" placeholder="{{ trans('messages.tracking-link') }}">
                                </div>
                            </div>     
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="collection_date">{{ trans("messages.collection-date") }}</label>
                                    <input type="text" name="collection_date" class="form-control date-format" placeholder="{{ trans('messages.collection-date') }}">
                                </div>
                            </div>  
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }}</label>
                                    <input type="text" name="delivery_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-date') }}">
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
                        <a href="{{ url('internal-transfer') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
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
            way_of_transport: {
                required: true
            },
            book_by: {
                required: true
            },
            logistic_partner: {
                required: true
            },
            booking_date: {
                required: true
            },
            invoice_no_ref_no: {
                required: true
            },
            account: {
                required: true
            },
            from_warehouse: {
                required: true
            },
            to_warehouse: {
                required: true
            },
            unit: {
                required: true
            },
            total_amount: {
                required: true
            },
            total_curruncy: {
                required: true
            },
            total_pallets: {
                required: true
            },
            tracking_no: {
                required: true
            },
            insurance_status: {
                required: true
            },
            dangerous_goods: {
                required: true
            },
            goods_remarks: {
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

        },
        messages: {
            way_of_transport: {
                required: "{{ trans('messages.require-way-of-transport') }}"
            },
            book_by: {
                required: "{{ trans('messages.require-book-by') }}"
            },
            logistic_partner: {
                required: "{{ trans('messages.require-logistic-partner') }}"
            },
            booking_date: {
                required: "{{ trans('messages.require-booking-date') }}"
            },
            invoice_no_ref_no: {
                required: "{{ trans('messages.require-invoice-no-ref-no') }}"
            },
            account: {
                required: "{{ trans('messages.require-account') }}"
            },
            from_warehouse: {
                required: "{{ trans('messages.require-from-warehouse') }}"
            },
            to_warehouse: {
                required: "{{ trans('messages.require-to-warehouse') }}"
            },
            unit: {
                required: "{{ trans('messages.require-unit') }}"
            },
            total_amount: {
                required: "{{ trans('messages.require-amount') }}"
            },
            total_curruncy: {
                required: "{{ trans('messages.require-currency') }}"
            },
            total_pallets: {
                required: "{{ trans('messages.require-total-pallets') }}"
            },
            tracking_no: {
                required: "{{ trans('messages.require-tracking-no') }}"
            },
            insurance_status: {
                required: "{{ trans('messages.require-insurance-status') }}"
            },
            dangerous_goods: {
                required: "{{ trans('messages.require-dangerous-goods') }}"
            },
            goods_remarks: {
                required: "{{ trans('messages.require-goods-remarks') }}"
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
            // minDate: moment().startOf('d'),
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });

    });
</script>
@endsection