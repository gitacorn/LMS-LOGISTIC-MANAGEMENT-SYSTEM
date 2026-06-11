@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <!-- <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.add-logistic-partner") }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ url('logistic-partner-master') }}" class="category-add-link">{{ trans("messages.logistic-partner-master") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ trans("messages.add-logistic-partner") }}</li>
            </ol>
        </nav>
    </div> -->

    <section class="dashboard-tab-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div class="dashboard-tab-mdiv">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-goods-in-tab" data-toggle="pill" data-target="#pills-goods-in" type="button" role="tab" aria-controls="pills-goods-in" aria-selected="true" title="{{ trans('messages.goods-in') }}"><i class="fas fa-level-down-alt"></i> {{ trans("messages.goods-in") }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-goods-out-tab" data-toggle="pill" data-target="#pills-goods-out" type="button" role="tab" aria-controls="pills-goods-out" aria-selected="false" title="{{ trans('messages.goods-out') }}"><i class="fas fa-level-up-alt"></i>{{ trans("messages.goods-out") }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-goods-in" role="tabpanel" aria-labelledby="pills-goods-in-tab">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h2 class="h3 mr-3 header-title" id="pageTitle">{{ trans("messages.goods-in-dashboard") }}</h2>
                    </div>
                </div>
                <div class="row mb-30 row-gap30">
                    <div class="col-xl-5">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-chart-bar"></i> {{ trans("messages.statistics") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" class="custom-control-input" id="custom-switch-case-statistics">
                                        <label class="custom-control-label" for="custom-switch-case-statistics"></label>
                                    </div>
                                </h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#statistics-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="statistics-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <form action="post" id="statistics-filter-form">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_status">{{ trans("messages.status")}}<span class="text-danger">*</span></label>
                                                        <select class="form-control" name="search_status">
                                                            <option value="{{ trans('messages.in-transit')}}">{{ trans("messages.in-transit")}}</option>
                                                            <option value="{{ trans('messages.delivered')}}">{{ trans("messages.delivered")}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 buyer-delivery-date">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_buyer_delivery_from_date">{{ trans("messages.buyer-delivery-from-date") }}</label>
                                                        <input type="text" class="form-control" name="search_buyer_delivery_from_date"  placeholder="DD-MM-YYYY" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 buyer-delivery-date">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_buyer_delivery_to_date">{{ trans("messages.buyer-delivery-to-date") }}</label>
                                                        <input type="text" class="form-control" name="search_buyer_delivery_to_date"  placeholder="DD-MM-YYYY" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 logistic-delivery-date">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_logistic_delivery_from_date">{{ trans("messages.logistic-delivery-from-date") }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="search_logistic_delivery_from_date"  placeholder="DD-MM-YYYY" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 logistic-delivery-date">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_logistic_delivery_to_date">{{ trans("messages.logistic-delivery-to-date") }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="search_logistic_delivery_to_date"  placeholder="DD-MM-YYYY" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_from_country">{{ trans("messages.from-country")}}</label>
                                                        <select class="form-control select2" multiple name="search_from_country">
                                                            <option value="usa">USA</option>
                                                            <option value="europe">Europe</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_to_warehouse">{{ trans("messages.to-warehouse")}}</label>
                                                        <select class="form-control select2" multiple name="search_to_warehouse">
                                                            <option value="ASTUTE HEALTHCARE LTD">ASTUTE HEALTHCARE LTD</option>
                                                            <option value="BEAUTY CARE GLOBAL SP Z00">BEAUTY CARE GLOBAL SP Z00</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                                    <button type="submit" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
                                                    <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                                </div>				
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row row-gap30">
                                    <div class="col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title statistics-title-units">{{ trans("messages.total-in-transit-units") }}</h5>
                                            <h6 class="statistics-count">870883</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title statistics-title-boxes">{{ trans("messages.total-in-transit-boxes") }}</h5>
                                            <h6 class="statistics-count">1701</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title statistics-title-pallets">{{ trans("messages.total-in-transit-pallets") }}</h5>
                                            <h6 class="statistics-count">1701</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="statistics-item totle">
                                            <h5 class="statistics-title statistics-title-po-amount">{{ trans("messages.total-in-transit-po-amount-with-vat") }}</h5>
                                            <h6 class="statistics-count">£ 1,08,89,714.28</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-5">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-body">
                                <canvas id="totalTransactionsChart" width="100%" class="chart-300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-8 col-md-7">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-calendar-day"></i> {{ trans("messages.avg-days-and-cost-summary") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" class="custom-control-input" id="custom-switch-case-cost-summary">
                                        <label class="custom-control-label" for="custom-switch-case-cost-summary"></label>
                                    </div>
                                </h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#avg-days-and-cost-summary-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="avg-days-and-cost-summary-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <form action="post" id="avg-days-and-cost-summary-filter-form">
                                            <div class="row align-items-center">
                                                <div class="col-xl-4 col-lg-6 col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_avg_days_country">{{ trans("messages.country")}}</label>
                                                        <select class="form-control select2" multiple name="search_avg_days_country">
                                                            <option value="usa">USA</option>
                                                            <option value="europe">Europe</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-6 col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_from_month">{{ trans("messages.from-month") }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="search_from_month"  placeholder="MM-YYYY" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-6 col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_to_month">{{ trans("messages.to-month") }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="search_to_month"  placeholder="MM-YYYY" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                                    <button type="submit" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
                                                    <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                                </div>				
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="table-responsive table-head-sticky table-max-10td">
                                    <table class="table table-sm table-bordered table-hover new-table">
                                        <tr>
                                            <th class="sr-col align-middle" style="width: 40px; min-width: 40px; max-width: 40px;">{{ trans("messages.sr-no") }}</th>
                                            <th class="text-left align-middle" style="min-width:170px;max-width:170px;">{{ trans("messages.warehouse") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.pallet") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.avg-days") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.avg-cost-pallet") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.total-cost") }}</th>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">1</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">2</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">3</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">4</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">5</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">6</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">7</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">8</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">9</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center"  style="width: 40px; min-width: 40px; max-width: 40px;">10</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-gap30">
                    <div class="col-12">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-calendar-day"></i> {{ trans("messages.buyer-delivery") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" class="custom-control-input" id="custom-switch-case-buyer-delivery">
                                        <label class="custom-control-label" for="custom-switch-case-buyer-delivery"></label>
                                    </div>
                                </h4>
                            </div>
                            <div class="dashboard-card-body border-color">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover new-table border-color-table">
                                        <tr>
                                            <th class="sr-col text-center align-middle sticky-left border-color" rowspan="2">
                                                <div class="border-color-div border-right-0 text-center justify-content-center">    
                                                   {{ trans("messages.sr-no") }}
                                                </div>
                                            </th>
                                            <th class="title-row text-center align-middle sticky-left sticky-sr border-color" rowspan="2">
                                                <div class="border-color-div text-center justify-content-center">
                                                    {{ trans("messages.warehouse-date") }}
                                                </div>
                                            </th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">10-Oct-24</th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">11-Oct-24</th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">12-Oct-24</th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">13-Oct-24</th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">14-Oct-24</th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">15-Oct-24</th>
                                            <th class="text-left text-center align-middle border-color" colspan="4">16-Oct-24</th>
                                        </tr>
                                        <tr>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.units") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.box") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title border-color">{{ trans("messages.value-vat") }}</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left border-color">
                                                <div class="border-color-div border-right-0 border-top-0 text-center justify-content-center">
                                                    1
                                                </div>
                                            </td>
                                            <td class="td-bg table-title sticky-left sticky-sr border-color">
                                                <div class="border-color-div border-top-0">
                                                    TFC
                                                </div>
                                            </td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left border-color"> <div class="border-color-div border-right-0 border-top-0 text-center justify-content-center"> 2 </div> </td>
                                            <td class="td-bg table-title sticky-left sticky-sr border-color"> <div class="border-color-div border-top-0"> Unit-2B </div></td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left border-color"> <div class="border-color-div border-right-0 border-top-0 text-center justify-content-center"> 3 </div> </td>
                                            <td class="td-bg table-title sticky-left sticky-sr border-color"> <div class="border-color-div border-top-0"> Hitchin </div></td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left border-color"> <div class="border-color-div border-right-0 border-top-0 text-center justify-content-center"> 4 </div> </td>
                                            <td class="td-bg table-title sticky-left sticky-sr border-color"> <div class="border-color-div border-top-0"> Jambo-BV</div></td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left border-color"> <div class="border-color-div border-right-0 border-top-0 text-center justify-content-center"> 5 </div> </td>
                                            <td class="td-bg table-title sticky-left sticky-sr border-color"> <div class="border-color-div border-top-0"> Raj-WH(BCG)</div></td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100 border-color-right">5000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-building"></i> {{ trans("messages.top-suppliers-company-wise") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" class="custom-control-input" id="custom-switch-case-top-suppliers-company">
                                        <label class="custom-control-label" for="custom-switch-case-top-suppliers-company"></label>
                                    </div>
                                </h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#top-suppliers-company-wise-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="top-suppliers-company-wise-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <form action="post" id="top-suppliers-company-wise-filter-form">
                                            <div class="row align-items-center">
                                                <div class="col-xl-4 col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_country">{{ trans("messages.company") }}</label>
                                                        <select class="form-control" name="search_country">
                                                            <option value="">{{ trans("messages.select") }}</option>
                                                            <option value="Company 1">Company 1</option>
                                                            <option value="Company 2">Company 2</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_top_company_from_month">{{ trans("messages.from-month") }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="search_top_company_from_month"  placeholder="MM-YYYY" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_top_company_to_month">{{ trans("messages.to-month") }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="search_top_company_to_month"  placeholder="MM-YYYY" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                                    <button type="submit" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
                                                    <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                                </div>				
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="table-responsive table-head-sticky table-max-10td">
                                    <table class="table table-sm table-bordered table-hover new-table mb-0">
                                        <tr>
                                            <th class="sr-col align-middle">{{ trans("messages.sr-no") }}</th>
                                            <th class="text-left align-middle" style="min-width:200px;max-width:200px;">{{ trans("messages.supplier") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.units") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.box") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.pallet") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.value-vat") }}</th>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">1</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">2</td>
                                            <td>Lorem Ipsum sample Lorem</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">3</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">4</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">5</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">6</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">7</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">8</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">9</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">10</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">11</td>
                                            <td>Lorem Ipsum</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr class="total-tr">
                                            <td class="text-center td-bg table-title" colspan="2">{{ trans("messages.total")}}</td>
                                            <td class="min-td-100 td-bg table-title">20</td>
                                            <td class="min-td-100 td-bg table-title">50</td>
                                            <td class="min-td-100 td-bg table-title">50</td>
                                            <td class="min-td-100 td-bg table-title">5000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="dashboard-new-card h-auto">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-building"></i> {{ trans("messages.top-suppliers") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" class="custom-control-input" id="custom-switch-case-top-suppliers">
                                        <label class="custom-control-label" for="custom-switch-case-top-suppliers"></label>
                                    </div>
                                </h4>
                            </div>
                            <div class="dashboard-card-body">
                                <canvas id="topSuppliersChart" style="height: 350px; max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-goods-out" role="tabpanel" aria-labelledby="pills-goods-out-tab">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h2 class="h3 mr-3 header-title" id="pageTitle">{{ trans("messages.goods-out-dashboard") }}</h2>
                    </div>
                </div>
                <div class="row mb-30">
                    <div class="col-12">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-chart-bar"></i> {{ trans("messages.statistics") }}</h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#goods-out-statistics-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="goods-out-statistics-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <div class="row align-items-center">
                                            <div class="col-xl-2 col-lg-4 col-md-12">
                                                <div class="form-group">
                    								<label class="control-label" for="search_lorem">Lorem ipsum</label>
                                                    <select class="form-control" name="search_lorem">
                                                        <option value="">{{ trans("messages.select") }}</option>
                                                        <option value="Lorem">Lorem</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-4 col-md-12">
                                                <div class="form-group">
                    								<label class="control-label" for="search_month">{{ trans("messages.buyer-delivery-from-date") }}</label>
                                                    <input type="text" class="form-control" name="search_goods_out_buyer_delivery_from_date"  placeholder="DD-MM-YYYY" />
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-4 col-md-12">
                                                <div class="form-group">
                    								<label class="control-label" for="search_month">{{ trans("messages.buyer-delivery-to-date") }}</label>
                                                    <input type="text" class="form-control" name="search_goods_out_buyer_delivery_to_date"  placeholder="DD-MM-YYYY" />
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                                <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
                                                <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                            </div>				
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-gap30">
                                    <div class="col-xl col-lg-4 col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title">{{ trans("messages.total-units") }}</h5>
                                            <h6 class="statistics-count">870883</h6>
                                        </div>
                                    </div>
                                    <div class="col-xl col-lg-4 col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title">{{ trans("messages.total-boxes") }}</h5>
                                            <h6 class="statistics-count">1701</h6>
                                        </div>
                                    </div>
                                    <div class="col-xl col-lg-4 col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title">{{ trans("messages.total-pallets") }}</h5>
                                            <h6 class="statistics-count">1701</h6>
                                        </div>
                                    </div>
                                    <div class="col-xl col-lg-4 col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title">{{ trans("messages.shipment-value") }}</h5>
                                            <h6 class="statistics-count">£ 1,08,89,714.28</h6>
                                        </div>
                                    </div>
                                    <div class="col-xl col-lg-4 col-sm-6">
                                        <div class="statistics-item">
                                            <h5 class="statistics-title">{{ trans("messages.total-transactions") }}</h5>
                                            <h6 class="statistics-count">£ 1,08,89,714.28</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-gap30">
                    <div class="col-12">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-calendar-day"></i> Lorem Lipsum</h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#lorem-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="lorem-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <div class="row align-items-center">
                                            <div class="col-xl-2 col-lg-6 col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label" for="search_lorem">Lorem ipsum</label>
                                                    <select class="form-control" name="search_lorem">
                                                        <option value="">{{ trans("messages.select") }}</option>
                                                        <option value="Lorem ipsum">Lorem ipsum</option>
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
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover new-table">
                                        <tr>
                                            <th class="sr-col text-center align-middle sticky-left" rowspan="2">{{ trans("messages.sr-no") }}</th>
                                            <th class="title-row text-center align-middle sticky-left sticky-sr" rowspan="2">{{ trans("messages.warehouse-date") }}</th>
                                            <th class="text-left text-center align-middle" colspan="4">10-Oct-24</th>
                                            <th class="text-left text-center align-middle" colspan="4">11-Oct-24</th>
                                            <th class="text-left text-center align-middle" colspan="4">12-Oct-24</th>
                                            <th class="text-left text-center align-middle" colspan="4">13-Oct-24</th>
                                            <th class="text-left text-center align-middle" colspan="4">14-Oct-24</th>
                                            <th class="text-left text-center align-middle" colspan="4">15-Oct-24</th>
                                        </tr>
                                        <tr>
                                            <td class="table-title">{{ trans("messages.units") }}</td>
                                            <td class="table-title">{{ trans("messages.box") }}</td>
                                            <td class="table-title">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title">{{ trans("messages.units") }}</td>
                                            <td class="table-title">{{ trans("messages.box") }}</td>
                                            <td class="table-title">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title">{{ trans("messages.units") }}</td>
                                            <td class="table-title">{{ trans("messages.box") }}</td>
                                            <td class="table-title">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title">{{ trans("messages.units") }}</td>
                                            <td class="table-title">{{ trans("messages.box") }}</td>
                                            <td class="table-title">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title">{{ trans("messages.units") }}</td>
                                            <td class="table-title">{{ trans("messages.box") }}</td>
                                            <td class="table-title">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title">{{ trans("messages.value-vat") }}</td>
                                            <td class="table-title">{{ trans("messages.units") }}</td>
                                            <td class="table-title">{{ trans("messages.box") }}</td>
                                            <td class="table-title">{{ trans("messages.pallet") }}</td>
                                            <td class="table-title">{{ trans("messages.value-vat") }}</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left">1</td>
                                            <td class="td-bg table-title sticky-left sticky-sr">TFC</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left">2</td>
                                            <td class="td-bg table-title sticky-left sticky-sr">Unit-2B</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left">3</td>
                                            <td class="td-bg table-title sticky-left sticky-sr">Hitchin</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left">4</td>
                                            <td class="td-bg table-title sticky-left sticky-sr">Jambo-BV</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center td-bg sticky-left">5</td>
                                            <td class="td-bg table-title sticky-left sticky-sr">Raj-WH(BCG)</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-building"></i> {{ trans("messages.in-transit-shipments") }}</h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#in-transit-shipments-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="in-transit-shipments-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <div class="row align-items-center">
                                            <div class="col-xl-4 col-lg-6 col-md-12">
                                                <div class="form-group">
                    								<label class="control-label" for="search_lorem">Lorem ipsum</label>
                                                    <select class="form-control" name="search_lorem">
                                                        <option value="">{{ trans("messages.select") }}</option>
                                                        <option value="Lorem ipsum">Lorem ipsum</option>
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
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover new-table">
                                        <tr>
                                            <th class="sr-col text-center align-middle">{{ trans("messages.sr-no") }}</th>
                                            <th class="text-left align-middle" style="min-width:200px;max-width:200px;">{{ trans("messages.shipment-no") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.units") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.pallet") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.value") }}</th>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">1</td>
                                            <td>CON-2B-097-2024</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td>6000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">2</td>
                                            <td>Con/Air-2</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td>6000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">3</td>
                                            <td>Con/Air-3</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td>6000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-building"></i> {{ trans("messages.account-wise-statistics") }}</h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#account-wise-statistics-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="account-wise-statistics-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <div class="row align-items-center">
                                            <div class="col-xl-4 col-lg-6 col-md-12">
                                                <div class="form-group">
                    								<label class="control-label" for="search_lorem">Lorem ipsum</label>
                                                    <select class="form-control" name="search_lorem">
                                                        <option value="">{{ trans("messages.select") }}</option>
                                                        <option value="Lorem ipsum">Lorem ipsum</option>
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
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover new-table">
                                        <tr>
                                            <th class="sr-col text-center align-middle">{{ trans("messages.sr-no") }}</th>
                                            <th class="text-left align-middle" style="min-width:200px;max-width:200px;">{{ trans("messages.account") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.units") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.box") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.pallet") }}</th>
                                            <th class="text-left align-middle min-td-100">{{ trans("messages.value-vat") }}</th>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">1</td>
                                            <td>LLP</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">2</td>
                                            <td>Y</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="sr-col text-center">3</td>
                                            <td>z</td>
                                            <td class="min-td-100">20</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">50</td>
                                            <td class="min-td-100">5000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>


<script>
    $(document).ready(function() {

        //init date time picker
        $("[name='search_from_month'],[name='search_to_month'],[name='search_top_company_from_month'],[name='search_top_company_to_month']").datetimepicker({
            useCurrent: false,
            viewMode: 'months',
            ignoreReadonly: true,
            format: "MM-YYYY",
        });
    });
    $(function(){
        $("[name='search_from_month']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var incrementDay = moment((e.date)).startOf('d');
            $("[name='search_to_month']").data('DateTimePicker').minDate(incrementDay);
            }else{
                $("[name='search_to_month']").data('DateTimePicker').minDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_to_month']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var decrementDay = moment((e.date)).endOf('d');
            $("[name='search_from_month']").data('DateTimePicker').maxDate(decrementDay);
            }else{
                $("[name='search_from_month']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
        $("[name='search_top_company_from_month']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var incrementDay = moment((e.date)).startOf('d');
            $("[name='search_top_company_to_month']").data('DateTimePicker').minDate(incrementDay);
            }else{
                $("[name='search_top_company_to_month']").data('DateTimePicker').minDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_top_company_to_month']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var decrementDay = moment((e.date)).endOf('d');
            $("[name='search_top_company_from_month']").data('DateTimePicker').maxDate(decrementDay);
            }else{
                $("[name='search_top_company_from_month']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    })
</script>

<script>
    $(document).ready(function() {

        //init date time picker
        $("[name='search_buyer_delivery_from_date'],[name='search_buyer_delivery_to_date'],[name='search_logistic_delivery_from_date'],[name='search_logistic_delivery_to_date'],[name='search_goods_out_buyer_delivery_from_date'],[name='search_goods_out_buyer_delivery_to_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: "DD-MM-YYYY",
        });

    });
    $(function(){

        $("[name='search_buyer_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var incrementDay = moment((e.date)).startOf('d');
            $("[name='search_buyer_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
            }else{
                $("[name='search_buyer_delivery_to_date']").data('DateTimePicker').minDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_buyer_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var decrementDay = moment((e.date)).endOf('d');
            $("[name='search_buyer_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
            }else{
                $("[name='search_buyer_delivery_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_logistic_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var incrementDay = moment((e.date)).startOf('d');
            $("[name='search_logistic_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
            }else{
                $("[name='search_logistic_delivery_to_date']").data('DateTimePicker').minDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_logistic_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var decrementDay = moment((e.date)).endOf('d');
            $("[name='search_logistic_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
            }else{
                $("[name='search_logistic_delivery_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });


        $("[name='search_goods_out_buyer_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var incrementDay = moment((e.date)).startOf('d');
            $("[name='search_goods_out_buyer_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
            }else{
                $("[name='search_goods_out_buyer_delivery_to_date']").data('DateTimePicker').minDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_goods_out_buyer_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
            if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var decrementDay = moment((e.date)).endOf('d');
            $("[name='search_goods_out_buyer_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
            }else{
                $("[name='search_goods_out_buyer_delivery_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    })
</script>


<script>
    $("#top-suppliers-company-wise-filter-form").validate({
        errorClass: "invalid-input",
        rules: {
            search_top_company_from_month: {
                required: true
            },
            search_top_company_to_month: {
                required: true
            },
        },
        messages: {
            search_top_company_from_month: {
                required: "{{ trans('messages.require-from-month') }}"
            },
            search_top_company_to_month: {
                required: "{{ trans('messages.require-to-month') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });

    $("#avg-days-and-cost-summary-filter-form").validate({
        errorClass: "invalid-input",
        rules: {
            search_from_month: {
                required: true
            },
            search_to_month: {
                required: true
            },
        },
        messages: {
            search_from_month: {
                required: "{{ trans('messages.require-from-month') }}"
            },
            search_to_month: {
                required: "{{ trans('messages.require-to-month') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });

    $("#statistics-filter-form").validate({
        errorClass: "invalid-input",
        rules: {
            search_status: {
                required: true
            },
            search_logistic_delivery_from_date: {
                required: true
            },
            search_logistic_delivery_to_date: {
                required: true
            },
        },
        messages: {
            search_status: {
                required: "{{ trans('messages.require-status') }}"
            },
            search_logistic_delivery_from_date: {
                required: "{{ trans('messages.require-logistic-delivery-from-date') }}"
            },
            search_logistic_delivery_to_date: {
                required: "{{ trans('messages.require-logistic-delivery-to-date') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });
    
</script>

<script src="{{ asset ('js/chart.js') }}"></script>


<script>
    $(document).ready(function(){
        // var selected_value = $.trim($("[name='search_status']").val());
        // console.log(selected_value);
        
        $(".logistic-delivery-date").hide();
        $("[name='search_status']").on('change', function(){
            var selected_value = $.trim($("[name='search_status']").val());
            if( selected_value != "" && selected_value != null && selected_value == "{{ trans('messages.in-transit')}}"){
                $(".logistic-delivery-date").hide();
                $(".buyer-delivery-date").show();
                $(".statistics-title-units").html('{{ trans("messages.total-in-transit-units") }}');
                $(".statistics-title-boxes").html('{{ trans("messages.total-in-transit-boxes") }}');
                $(".statistics-title-pallets").html('{{ trans("messages.total-in-transit-pallets") }}');
                $(".statistics-title-po-amount").html('{{ trans("messages.total-in-transit-po-amount-with-vat") }}');
            } else {
                $(".logistic-delivery-date").show();
                $(".buyer-delivery-date").hide();
                $(".statistics-title-units").html('{{ trans("messages.total-delivered-units") }}');
                $(".statistics-title-boxes").html('{{ trans("messages.total-delivered-boxes") }}');
                $(".statistics-title-pallets").html('{{ trans("messages.total-delivered-pallets") }}');
                $(".statistics-title-po-amount").html('{{ trans("messages.total-delivered-po-amount-with-vat") }}');
            }
        });
    })
</script>


<script>
    const data = {
        labels: [
            'Delivery Numbers',
            'Collection Numbers'
        ],
        datasets: [{
            data: [110,80], // Adjust values as needed
            backgroundColor: [
                '#8D191A',
                '#FFC7C7'
            ],
            borderColor: [
                '#8D191A',
                '#FFC7C7'
            ],
            borderWidth: 1
        }]
    };

    const totalTransactionsctx = document.getElementById('totalTransactionsChart').getContext('2d');
    
    const totalTransactions = data.datasets[0].data.reduce((a, b) => a + b, 0); // Calculate total

    const totalTransactionsChart = new Chart(totalTransactionsctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true, 
            plugins: {
                legend: {
                    onClick: null,
                    position: 'top',
                    labels: {
                        font: {
                            size: '14px',
                            weight:'500',
                        },
                        family:'Roboto',
                        color:'#000'
                    }
                },
                title: {
                    display: false,
                    text: 'Total Transactions: ' + totalTransactions // Add a title with total transactions
                }
            },
            cutout: '70%' // Make the doughnut ring thicker
        },
        plugins: [{
            beforeDraw: function(chart) {
                const width = chart.width,
                      height = chart.height,
                      ctx = chart.ctx;

                ctx.restore();

                // Set font size and styles
                const fontSize = (height / 160).toFixed(2);
                ctx.font = 13 + "px Roboto";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#000";

                // Draw the label ("Total")
                const labelText = "Total Transactions";
                const labelX = Math.round((width - ctx.measureText(labelText).width) / 2);
                const labelY = height / 2 - 1 + 8; // Position slightly above the center
                ctx.fillText(labelText, labelX, labelY);

                // Draw the total count
                ctx.font = "bold " + (fontSize * 1.4) + "em Roboto"; // Larger font for count
                const countText = totalTransactions;
                const countX = Math.round((width - ctx.measureText(countText).width) / 2);
                const countY = height / 1.8 + 15 + 8; // Position slightly below the center
                ctx.fillText(countText, countX, countY);

                ctx.save();
            }
        }]
    });
    
</script>

<script>
    const totalTransactionsPieData = {
        labels: [
            'Delivery Numbers',
            'Collection Numbers'
        ],
        datasets: [{
            data: [100,50], // Replace with actual Collection Numbers value
            backgroundColor: [
                '#FFA0A1',
                '#FFE0E0'
            ],
            borderColor: [
                '#FFA0A1',
                '#FFE0E0'
            ],
            borderWidth: 1
        }]
    };
</script>
<script>

  const topSuppliersctx = document.getElementById('topSuppliersChart').getContext('2d');
  const topSuppliersChart = new 
 Chart(topSuppliersctx, {
    type: 'bar',
    data: {
      labels: ['Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum'], 

      datasets: [{
        label: 'Lorem',
        data: [210, 210, 150, 210, 110, 160, 180, 130, 230, 250],
        backgroundColor: '#8D191A',
        borderColor: '#8D191A',
        borderWidth: 1,
        barThickness: 16,
        maxBarThickness: 16,
      }]
    },
    options: {
        plugins: {
            legend: {
                display: false,
                position: 'bottom',
                onClick: null,
            }
        },
        scales: {
            
            y: {
                beginAtZero: true,
                barThickness: 16,  // Adjust this value as needed
                maxBarThickness: 16,
                ticks: {
                    font: {
                        size: '14px',
                        weight:'600',
                    },
                    family:'Roboto',
                    color:'#000', 
                },
                border: {
                    display: false // Remove x-axis border
                }
            },
            x:{
                ticks: {
                    font: {
                        size: '12px',
                        weight:'500',
                    },
                    family:'Roboto',
                    color:'#000', 
                },
                grid: {
                    display: false,
                },
            }
        },
    },

  });
</script>
@endsection