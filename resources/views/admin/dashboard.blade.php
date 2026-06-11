@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">

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
                <div class="row mb-30 align-items-stretch" style="margin-right: 0; margin-left: 0;">
                    <div class="col-xl-3 col-md-12 d-flex">
                        <div class="dashboard-new-card w-100 h-100">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-chart-bar"></i> {{ trans("messages.statistics") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" checked data-fetch="{{config('constants.SELECTION_YES')}}" class="custom-control-input" id="custom-switch-case-statistics" onclick="toggleSwitch(this);">
                                        <label class="custom-control-label" for="custom-switch-case-statistics"></label>
                                    </div>
                                </h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#statistics-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="statistics-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_status">{{ trans("messages.status")}}<span class="text-danger">*</span></label>
                                                        <select class="form-control" name="search_status" onchange="setDeliveryDate(this);filterStatisticsData();">
                                                            <option value="{{ config('constants.IN_TRANSIT_STATUS') }}">{{ trans("messages.in-transit")}}</option>
                                                            <option value="{{ config('constants.DELIVERED_STATUS') }}">{{ trans("messages.delivered")}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 buyer-delivery-date">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_from_date" id="statistics-from-date">{{ trans("messages.buyer-delivery-from-date") }}</label>
                                                        <input type="text" class="form-control" name="search_buyer_delivery_from_date"  placeholder="DD-MM-YYYY" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 buyer-delivery-date">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_to_date" id="statistics-to-date">{{ trans("messages.buyer-delivery-to-date") }}</label>
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
                                                        <select class="form-control select2" multiple name="search_from_country" onchange="filterStatisticsData();">
                                                            @if(!empty($countryDetails))
                                                            	@foreach($countryDetails as $country)
                                                            		@php
                                                            			$encodedId = trim(Wild_tiger::encode($country->i_id));
                                                            		@endphp	
                                                            		<option value="{{ $encodedId }}">{{$country->v_country_name}}</option>
                                                            	@endforeach
                                                            @endif		
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="search_to_warehouse">{{ trans("messages.to-warehouse")}}</label>
                                                        <select class="form-control select2" onchange="filterStatisticsData();" multiple name="search_to_warehouse">
                                                             @if(!empty($wareHouseDetails))
                                                            	@foreach($wareHouseDetails as $wareHouseDetail)
                                                            		@php
                                                            			$encodedId = trim(Wild_tiger::encode($wareHouseDetail->i_id));
                                                            		@endphp	
                                                            		<option value="{{ $encodedId }}">{{(!empty($wareHouseDetail->v_warehouse_name) ? $wareHouseDetail->v_warehouse_name .(!empty($wareHouseDetail->v_warehouse_code) ? ' (' .$wareHouseDetail->v_warehouse_code .')' : '' ): '' )}}</option>
                                                            	@endforeach
                                                            @endif	
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                                    <button type="button" onclick="filterStatisticsData();" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
                                                    <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="row row-gap30 statistics-ajax-view switch-html">
                                    @include( config('constants.AJAX_VIEW_FOLDER') . 'dashboard/dashboard-statistics')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-5 d-flex">
                        <div class="dashboard-new-card w-100" style="min-height: 280px;">
                            <div class="dashboard-card-body w-100">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-center mb-4">
                                        <canvas id="totalTransactionsChart" width="180" height="180" style="max-height: 180px !important; height: 180px !important; width: 180px !important;"></canvas>
                                    </div>
                                    <div class="col-12">
                                        <hr style="border-top: 1px solid #dee2e6; margin: 10px 0;">
                                    </div>
                                    <div class="col-12 d-flex justify-content-center">
                                        <canvas id="secondDonutChart" width="180" height="180" style="max-height: 180px !important; height: 180px !important; width: 180px !important;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-8 col-md-2 d-flex">
                        <div class="dashboard-new-card w-100" style="min-height: 280px;">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-building"></i> TOP COMPANIES
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" data-fetch="{{config('constants.SELECTION_NO')}}" class="custom-control-input" id="custom-switch-case-chart-top-suppliers" onclick="toggleSwitch(this);">
                                        <label class="custom-control-label" for="custom-switch-case-chart-top-suppliers"></label>
                                    </div>
                                </h4>
                                <!-- Date filter removed as requested -->
                            </div>
                            <div class="dashboard-card-body" style="min-height: auto; padding: 15px;">
                                <div class="chart-wrap">
                                    <div class="switch-html top-suppliers-ajax-view">
                                        @include( config('constants.AJAX_VIEW_FOLDER') . 'dashboard/dashboard-top-supplier-company')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-gap30" style="margin-right: 0; margin-left: 0;">
                    <div class="col-12 d-flex">
                        <div class="dashboard-new-card w-100">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-calendar-day"></i> {{ trans("messages.buyer-delivery") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" data-fetch="{{config('constants.SELECTION_NO')}}" class="custom-control-input" id="custom-switch-case-buyer-delivery" onclick="toggleSwitch(this)">
                                        <label class="custom-control-label" for="custom-switch-case-buyer-delivery"></label>
                                    </div>
                                </h4>
                            </div>
                            <div class="dashboard-card-body border-color min-height">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover new-table buyer-delivery-ajax-view switch-html">
                                        @include( config('constants.AJAX_VIEW_FOLDER') . 'dashboard/dashboard-buyer-delivery' )
                                    </table>
                                </div>
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
                <div class="row mb-30 row-gap30">
                    <div class="col-xl-5">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-chart-bar"></i> {{ trans("messages.statistics") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" checked data-fetch="{{config('constants.SELECTION_YES')}}" class="custom-control-input" id="custom-switch-case-goods-out-statistics" onclick="toggleSwitch(this);">
                                        <label class="custom-control-label" for="custom-switch-case-goods-out-statistics"></label>
                                    </div>
                                </h4>
                                <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#goods-out-statistics-filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="collapse dashboard-card-collapse" id="goods-out-statistics-filter">
                                    <div class="dashboard-card-collapse-mdiv">
                                        <div class="row align-items-center">
                                           <div class="col-md-6">
                                                <div class="form-group">
                    								<label class="control-label" for="search_location">{{ trans("messages.to-country")}}</label>
                                                    <select class="form-control" name="search_location" id="goods_out_search_location" onchange="filterGoodsOutStatistics();">
    <option value="EU" selected>EU</option>
    <option value="USA">USA</option>
    <option value="UK">UK</option>
</select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label" for="search_goods_out_from_warehouse">{{ trans("messages.from-warehouse")}}</label>
                                                    <select class="form-control select2" multiple name="search_goods_out_from_warehouse" id="search_goods_out_from_warehouse" onchange="filterGoodsOutStatistics();">
                                                        @if(!empty($wareHouseDetails))
                                                            @foreach($wareHouseDetails as $wareHouseDetail)
                                                                @php
                                                                    $encodedId = trim(Wild_tiger::encode($wareHouseDetail->i_id));
                                                                @endphp	
                                                                <option value="{{ $encodedId }}">{{(!empty($wareHouseDetail->v_warehouse_name) ? $wareHouseDetail->v_warehouse_name .(!empty($wareHouseDetail->v_warehouse_code) ? ' (' .$wareHouseDetail->v_warehouse_code .')' : '' ): '' )}}</option>
                                                            @endforeach
                                                        @endif	
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                    								<label class="control-label" for="search_status">{{ trans("messages.status")}}</label>
                                                    <select class="form-control" name="search_status" id="goods_out_search_status" onchange="filterGoodsOutStatistics();">
    <option value="{{ config('constants.IN_TRANSIT_STATUS') }}" selected>{{ trans("messages.in-transit")}}</option>
    <option value="{{ config('constants.DELIVERED_STATUS') }}">{{ trans("messages.delivered")}}</option>
</select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                    								<label class="control-label" for="search_month">{{ trans("messages.collection-from-date") }}</label>
                                                    <input type="text" class="form-control" name="search_goods_out_buyer_delivery_from_date"  placeholder="DD-MM-YYYY" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                    								<label class="control-label" for="search_month">{{ trans("messages.collection-to-date") }}</label>
                                                    <input type="text" class="form-control" name="search_goods_out_buyer_delivery_to_date"  placeholder="DD-MM-YYYY" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center gap pt-md-3">
                                                <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterGoodsOutStatistics();">{{ trans("messages.search") }}</button>
                                                <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                            </div>				
                                        </div>
                                    </div>
                                </div>
                                <div id="goods-out-statistics" class="row row-gap30 goods-out-statistics-ajax-view switch-html">
                                    @include( config('constants.AJAX_VIEW_FOLDER') . 'dashboard/dashboard-goods-out-statistics')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-5 d-flex">
                        <div class="dashboard-new-card w-100" style="min-height: 280px;">
                            <div class="dashboard-card-body w-100">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-center mb-4">
                                        <canvas id="goodsOutTotalTransactionsChart" width="180" height="180" style="max-height: 180px !important; height: 180px !important; width: 180px !important;"></canvas>
                                    </div>
                                    <div class="col-12">
                                        <hr style="border-top: 1px solid #dee2e6; margin: 10px 0;">
                                    </div>
                                    <div class="col-12 d-flex justify-content-center">
                                        <canvas id="goodsOutModeOfTransportChart" width="180" height="180" style="max-height: 180px !important; height: 180px !important; width: 180px !important;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-8 col-md-7">
                        <div class="dashboard-new-card">
                            <div class="dashboard-card-head">
                                <h4 class="dashboard-card-title"><i class="fas fa-chart-pie"></i> {{ trans("messages.goods-out-distribution") }}
                                    <div class="custom-control custom-switch d-flex">
                                        <input type="checkbox" checked data-fetch="{{config('constants.SELECTION_YES')}}" class="custom-control-input" id="custom-switch-case-goods-out-charts" onclick="toggleSwitch(this)">
                                        <label class="custom-control-label" for="custom-switch-case-goods-out-charts"></label>
                                    </div>
                                </h4>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <canvas id="goodsOutLocationChart" width="100%" class="chart-250"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <canvas id="goodsOutStatusChart" width="100%" class="chart-250"></canvas>
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
	var module_url = '{{ config("constants.DASHBOARD_URL") }}';
	function filterStatisticsData(thisitem){
		console.log('filterStatisticsData called');
		
		var search_field = {
			 'search_status' : $.trim($('[name="search_status"]').val()),
			 'search_from_date' : ( $.trim($('[name="search_status"]').val()) == '{{ config("constants.IN_TRANSIT_STATUS") }}' ? ( $.trim($('[name="search_buyer_delivery_from_date"]').val()) ) : ( $.trim($('[name="search_logistic_delivery_from_date"]').val()) )),
			 'search_to_date' : ( $.trim($('[name="search_status"]').val()) == '{{ config("constants.IN_TRANSIT_STATUS") }}' ? ( $.trim($('[name="search_buyer_delivery_to_date"]').val()) ) : ( $.trim($('[name="search_logistic_delivery_to_date"]').val()) )),
			 'search_from_country' : $.trim($('[name="search_from_country"]').val()),
			 'search_to_warehouse' : $.trim($('[name="search_to_warehouse"]').val()),
		}
		
		console.log('Filter search_field:', search_field);
		
		var toggle = $('#custom-switch-case-statistics');
	    if (!toggle.prop("checked")) {
	        toggle.prop("checked", true);
		    toggleSwitch(toggle); 
	    }
	    
	    // Update statistics cards
		commonAjax( module_url + '/get-statistics-filter', search_field , 'statistics-ajax-view');
		
		// Update donut chart with same filters
		console.log('Calling updateDonutChartWithFilters...');
		updateDonutChartWithFilters(search_field);
		
		// Update top suppliers chart with same filters
		updateTopSuppliersChartWithFilters(search_field);
	}

	// Function to update donut chart with statistics filters
	function updateDonutChartWithFilters(search_field) {
		console.log('Updating donut chart with filters:', search_field);
		
		$.ajax({
			url: module_url + '/get-donut-chart-filter',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type: 'post',
			data: search_field,
			beforeSend: function() {
				console.log('Sending AJAX request for donut chart...');
			},
				success: function(response) {
				console.log('Donut chart AJAX response:', response);
				
				// Check if response is valid
				if (!response) {
					console.error('Empty response from server');
					return;
				}
				
				if (response.deliveryCount !== undefined && response.collectionCount !== undefined) {
					// Parse values as integers
					var deliveryCount = parseInt(response.deliveryCount) || 0;
					var collectionCount = parseInt(response.collectionCount) || 0;
					
					console.log('Parsed values:', { delivery: deliveryCount, collection: collectionCount });
					
					// Update donut chart data
					if (totalTransactionsChart) {
						console.log('Before update - Current chart data:', totalTransactionsChart.data.datasets[0].data);
						
						// Update the data
						totalTransactionsChart.data.datasets[0].data = [deliveryCount, collectionCount];
						
						console.log('After update - New chart data:', totalTransactionsChart.data.datasets[0].data);
						
						// Update the chart with 'active' mode for full redraw
						totalTransactionsChart.update('active');
						
						console.log('Donut chart updated successfully');
					} else {
						console.error('totalTransactionsChart is null or undefined');
					}
				} else {
					console.error('Invalid response format - missing deliveryCount or collectionCount:', response);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error updating donut chart:', { status: status, error: error, responseText: xhr && xhr.responseText ? xhr.responseText : null });
			}
		});
	}

	// Function to update top suppliers chart with statistics filters
	function updateTopSuppliersChartWithFilters(search_field) {
		console.log('Updating top suppliers chart with filters:', search_field);
		
		// Add month filters to match statistics date range
		if (search_field.search_from_date) {
			var fromDate = new Date(search_field.search_from_date.split('-').reverse().join('-'));
			search_field.search_from_month = ('0' + (fromDate.getMonth() + 1)).slice(-2) + '-' + fromDate.getFullYear();
		}
		if (search_field.search_to_date) {
			var toDate = new Date(search_field.search_to_date.split('-').reverse().join('-'));
			search_field.search_to_month = ('0' + (toDate.getMonth() + 1)).slice(-2) + '-' + toDate.getFullYear();
		}
		
		// IMPORTANT: Add isChart flag to get chart view instead of table view
		search_field.isChart = true;
		
		console.log('Modified search_field for suppliers:', search_field);
		
		$.ajax({
			url: module_url + '/dashboard-supplier-company',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type: 'post',
			data: search_field,
			beforeSend: function() {
				console.log('Sending AJAX request for top suppliers...');
				// Show loading state
				$('.top-suppliers-ajax-view').html('<div style="text-align: center; padding: 20px; color: #666;">Loading...</div>');
			},
			success: function(response) {
				console.log('Top suppliers AJAX response length:', response ? response.length : 0);
				console.log('Top suppliers AJAX response preview:', response ? response.substring(0, 200) : 'empty');
				
				// Check if response contains canvas element
				if (response && response.includes('topSuppliersChart')) {
					console.log('Response contains chart - updating view');
					$('.top-suppliers-ajax-view').html(response);
				} else if (response && response.length > 0) {
					console.log('Response does not contain chart - still updating view');
					$('.top-suppliers-ajax-view').html(response);
				} else {
					console.error('Empty or invalid response for top suppliers');
					$('.top-suppliers-ajax-view').html('<div style="text-align: center; padding: 20px; color: #d32f2f;">No data available</div>');
				}
			},
			error: function(xhr, status, error) {
				console.error('Error updating top suppliers chart:', { status: status, error: error, responseText: xhr && xhr.responseText ? xhr.responseText : null });
				$('.top-suppliers-ajax-view').html('<div style="text-align: center; padding: 20px; color: #d32f2f;">Error loading data</div>');
			}
		});
	}



	function filterAvgSummary(){
		var search_field = {
			 'search_avg_days_country' : $.trim($('[name="search_avg_days_country"]').val()),
			 'search_from_month' : $.trim($('[name="search_from_month"]').val()),
			 'search_to_month' : $.trim($('[name="search_to_month"]').val())
		}

		var toggle = $('#custom-switch-case-cost-summary');
	    if (!toggle.prop("checked")) {
	        toggle.prop("checked", true);
		    toggleSwitch(toggle); 
	    }
		
		commonAjax( module_url + '/dashboard-avg-days', search_field , 'avg-summary-ajax-view');

	}

	function filterCompanySuppliers(){
		var search_field = {
			 'search_company' : $.trim($('[name="search_company"]').val()),
			 'search_from_month' : $.trim($('[name="search_top_company_from_month"]').val()),
			 'search_to_month' : $.trim($('[name="search_top_company_to_month"]').val()),
		}

		var toggle = $('#custom-switch-case-top-suppliers-company');
	    if (!toggle.prop("checked")) {
	        toggle.prop("checked", true);
		    toggleSwitch(toggle); 
	    }
		
		commonAjax( module_url + '/dashboard-supplier-company', search_field , 'top-suppliers-company-ajax-view');
	}

	function filterGoodsOutStatistics(){
		var search_location = $.trim($('#goods_out_search_location').val());
		var search_status   = $.trim($('#goods_out_search_status').val());
		var search_from_date = $.trim($('[name="search_goods_out_buyer_delivery_from_date"]').val());
		var search_to_date   = $.trim($('[name="search_goods_out_buyer_delivery_to_date"]').val());
        var search_from_warehouse = $('#search_goods_out_from_warehouse').val();

		var search_field = {
			'search_location'                            : search_location,
			'search_status'                              : search_status,
			'search_goods_out_buyer_delivery_from_date'  : search_from_date,
			'search_goods_out_buyer_delivery_to_date'    : search_to_date,
            'search_goods_out_from_warehouse'            : search_from_warehouse ? search_from_warehouse.join(',') : '',
		};

		var toggle = $('#custom-switch-case-goods-out-statistics');
		if (!toggle.prop('checked')) {
			toggle.prop('checked', true);
			toggleSwitch(toggle);
		}

		/* Use the HTML-returning route so the blade view re-renders with correct data */
		$.ajax({
			type: 'POST',
			url: module_url + '/get-goods-out-statistics-filter',
			data: search_field,
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			beforeSend: function() { showLoader(); },
			success: function(response) {
				hideLoader();
				if (response) {
					$('#goods-out-statistics').html(response);
				}
			},
			error: function(xhr, status, error) {
				hideLoader();
				console.error('Goods Out Filter error:', error);
			}
		});
        updateGoodsOutCharts(search_field);
	}

	function filterBuyerDelivery(){
		commonAjax( module_url + '/dashboard-buyer-delivery', '', 'buyer-delivery-ajax-view' );
	}
	
	function filterTopCompanySuppliers(){
		var search_field = {
			 isChart : true
		}
		commonAjax( module_url + '/dashboard-supplier-company', search_field , 'top-suppliers-ajax-view');
	}
	
	function commonAjax( ajax_url, search_data, view ){

		$.ajax({
			url : ajax_url,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type : 'post',
			data : search_data,
			beforeSend: function() {
				showLoader();
			},
			success : function(response){
				hideLoader();
				if(response != '' && response != null && view != '' && view != null){
					$('.' + view).html(response);
				}
			},
			error : function(xhr, status, error){
				hideLoader();
				console.error('AJAX error', { url: ajax_url, status: status, error: error, responseText: xhr && xhr.responseText ? xhr.responseText : null });
			}
		});
		
	}

	function setDeliveryDate(thisitem){
		var selected_value = $.trim($(thisitem).val());
		if( selected_value != null && selected_value != '' && selected_value == "{{config('constants.DELIVERED_STATUS')}}"  ){
			$(".logistic-delivery-date").show();
			$(".buyer-delivery-date").hide();

			// Copy date from buyer delivery date if available
			var fromDate = $.trim($('[name="search_buyer_delivery_from_date"]').val());
			var toDate = $.trim($('[name="search_buyer_delivery_to_date"]').val());
			
			if (fromDate != '') {
				$('[name="search_logistic_delivery_from_date"]').val(fromDate);
			} else if ($.trim($('[name="search_logistic_delivery_from_date"]').val()) == '') {
				$('[name="search_logistic_delivery_from_date"]').val( '{{ date("01-m-Y") }}' );
			}
			
			if (toDate != '') {
				$('[name="search_logistic_delivery_to_date"]').val(toDate);
			} else if ($.trim($('[name="search_logistic_delivery_to_date"]').val()) == '') {
				$('[name="search_logistic_delivery_to_date"]').val( '{{ date("d-m-Y") }}' );
			}
		} else if ( selected_value != null && selected_value != "" && selected_value == "{{config('constants.IN_TRANSIT_STATUS')}}" ){
			$(".logistic-delivery-date").hide();
			$(".buyer-delivery-date").show();
            			
			// Copy date from logistic delivery date if available
			var fromDate = $.trim($('[name="search_logistic_delivery_from_date"]').val());
			var toDate = $.trim($('[name="search_logistic_delivery_to_date"]').val());
			
			if (fromDate != '') {
				$('[name="search_buyer_delivery_from_date"]').val(fromDate);
			}
			if (toDate != '') {
				$('[name="search_buyer_delivery_to_date"]').val(toDate);
			}
		}
		changeLabel(selected_value);
	}

    $(document).ready(function(){
    	$('[name="search_from_month"],[name="search_to_month"],[name="search_top_company_from_month"],[name="search_top_company_to_month"]').val( '{{ date( "m-Y",strtotime("-1 month") ) }}' );
    	$(".logistic-delivery-date").hide();


	    function handleToggle(toggle) {
	        if (!toggle.prop("checked")) {
	            toggle.prop("checked", true);
	            toggleSwitch(toggle);
	        }
	    }
		
    	handleToggle($('#custom-switch-case-buyer-delivery'));
	    handleToggle($('#custom-switch-case-top-suppliers-company'));
	    handleToggle($('#custom-switch-case-chart-top-suppliers'));
    	
    });

	function toggleSwitch(thisitem){

		var fetch_status = $.trim($(thisitem).attr('data-fetch'));
 		var switch_id = $.trim($(thisitem).attr('id'));
		var switch_value = $.trim($(thisitem).prop("checked"));
		
		if(switch_id !== null && switch_id !== ''  && fetch_status != '{{ config("constants.SELECTION_YES") }}'){
			$(thisitem).closest('.dashboard-new-card').find('.switch-html').show();
			switch(switch_id){
				case 'custom-switch-case-top-suppliers-company':
					filterCompanySuppliers();
				break;
				case 'custom-switch-case-chart-top-suppliers':
					filterTopCompanySuppliers();
				break;	
				case 'custom-switch-case-statistics':
					filterStatisticsData();
				break;		
				case 'custom-switch-case-cost-summary':
					filterAvgSummary();
				break;	
				case 'custom-switch-case-buyer-delivery':
					filterBuyerDelivery();
			}
			$(thisitem).attr('data-fetch' , '{{ config("constants.SELECTION_YES") }}');
		}

		if(switch_value !== 'true'){
			$(thisitem).closest('.dashboard-new-card').find('.switch-html').hide();
		} else {
			$(thisitem).closest('.dashboard-new-card').find('.switch-html').show();
		}
		
	}
    
</script>


<script>
    const delivery_count = {{ ( isset($recordDetails) && (!empty($recordDetails)) ? (int)$recordDetails["deliveryCount"] : 0)  }};
    const collection_count = {{ ( isset($recordDetails) && (!empty($recordDetails)) ? (int)$recordDetails["collectionCount"] : 0)  }};

    const totalTransactionsCanvas = document.getElementById('totalTransactionsChart');
    const totalTransactions = (parseInt(delivery_count) + parseInt(collection_count)).toLocaleString('en-GB');

    let totalTransactionsChart = null;
    if (totalTransactionsCanvas && totalTransactionsCanvas.getContext) {
        const totalTransactionsctx = totalTransactionsCanvas.getContext('2d');

        totalTransactionsChart = new Chart(totalTransactionsctx, {
            type: 'doughnut',
            data: {
                labels: ['DELIVERY NUMBERS', 'COLLECTION NUMBERS'],
                datasets: [{
                    data: [delivery_count, collection_count],
                    backgroundColor: ['#8D191A', '#FFC7C7'],
                    borderColor: ['#8D191A', '#FFC7C7'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10,
                                weight: '500'
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            maxWidth: 100
                        },
                        align: 'center',
                        fullSize: true
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#8D191A',
                        borderWidth: 1,
                        cornerRadius: 4,
                        displayColors: true,
                        callbacks: {
                            title: function() {
                                return null;
                            },
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                return label + ': ' + value.toLocaleString('en-GB');
                            }
                        }
                    }
                },
                cutout: '70%',
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                }
            },
            plugins: [{
                id: 'customLegendStyling',
                afterLayout(chart) {
                    setTimeout(() => {
                        const legendItems = document.querySelectorAll('[role="legend"] tbody tr, [role="legend"] li');
                        legendItems.forEach(item => {
                            item.style.display = 'block';
                            item.style.width = '100%';
                        });
                    }, 0);
                }
            }, {
                id: 'centerText',
                afterDatasetsDraw(chart, args, options) {
					const {ctx, chartArea: {left, top, width, height}} = chart;
					
					// Get current data values directly from chart
					const data = chart.data.datasets[0].data;
					const deliveryVal = parseInt(data[0]) || 0;
					const collectionVal = parseInt(data[1]) || 0;
					const totalCount = deliveryVal + collectionVal;
					
					// Clear the center area first
					const centerX = left + width / 2;
					const centerY = top + height / 2;
					const radius = Math.min(width, height) / 2 * 0.5; // 50% of chart radius
					
					ctx.save();
					
					// Clear the center circle
					ctx.beginPath();
					ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
					ctx.fillStyle = '#ffffff';
					ctx.fill();
					
					// Draw label text in two lines to fit within the donut hole
					ctx.font = "10px Roboto";
					ctx.textBaseline = "middle";
					ctx.fillStyle = "#000";
					ctx.textAlign = "center";
					ctx.fillText("TOTAL", centerX, centerY - 14);
					ctx.fillText("TRANSACTIONS", centerX, centerY - 2);
					
					// Draw count text
					ctx.font = "bold 16px Roboto";
					const countText = totalCount.toLocaleString('en-GB');
					ctx.fillText(countText, centerX, centerY + 14);
					
					ctx.restore();
				}
            }]
        });
    }

    // Initialize Second Donut Chart
    const secondDonutCanvas = document.getElementById('secondDonutChart');
    let secondDonutChart = null;
    if (secondDonutCanvas && secondDonutCanvas.getContext('2d')) {
        const secondDonutctx = secondDonutCanvas.getContext('2d');

        secondDonutChart = new Chart(secondDonutctx, {
            type: 'doughnut',
            data: {
                labels: ['TOTAL IN-TRANSIT PO AMOUNT WITH VAT', 'TOTAL IN-TRANSIT UNITS'],
                datasets: [{
                    label: 'Outer Ring - Total Value',
                    data: [{{ isset($recordDetails) && isset($recordDetails['totalAmount']) ? (is_object($recordDetails['totalAmount']) ? (float)($recordDetails['totalAmount']->total_amount ?? 0) : (float)$recordDetails['totalAmount']) : 0 }}, 0],
                    backgroundColor: ['#8D191A', 'transparent'],
                    borderColor: ['#8D191A', 'transparent'],
                    borderWidth: 1,
                    cutout: '40%'
                }, {
                    label: 'Inner Ring - Total Units',
                    data: [0, {{ isset($recordDetails) && isset($recordDetails['totalUnits']) ? (is_object($recordDetails['totalUnits']) ? (int)($recordDetails['totalUnits']->total_units ?? 0) : (int)$recordDetails['totalUnits']) : 0 }}],
                    backgroundColor: ['transparent', '#FFC7C7'],
                    borderColor: ['transparent', '#FFC7C7'],
                    borderWidth: 1,
                    cutout: '50%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10,
                                weight: '500'
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            maxWidth: 100,
                            generateLabels: function(chart) {
                                const labels = chart.data.labels;
                                return labels.map((label, i) => {
                                    return {
                                        text: label,
                                        fillStyle: i === 0 ? '#8D191A' : '#FFC7C7',
                                        strokeStyle: i === 0 ? '#8D191A' : '#FFC7C7',
                                        lineWidth: 1,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                        },
                        align: 'center',
                        fullSize: true
                    }
                }
            }
        });
    }

    // Initialize Additional Donut Chart
    const additionalDonutCanvas = document.getElementById('additionalDonutChart');
    let additionalDonutChart = null;
    if (additionalDonutCanvas && additionalDonutCanvas.getContext('2d')) {
        const additionalDonutctx = additionalDonutCanvas.getContext('2d');

        additionalDonutChart = new Chart(additionalDonutctx, {
            type: 'doughnut',
            data: {
                labels: ['DATA POINT 1', 'DATA POINT 2'],
                datasets: [{
                    data: [40, 60],
                    backgroundColor: ['#8D191A', '#FFC7C7'],
                    borderColor: ['#8D191A', '#FFC7C7'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10,
                                weight: '500'
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            maxWidth: 100
                        },
                        align: 'center',
                        fullSize: true
                    }
                }
            }
        });
    }

    // Update concentric chart when statistics filter changes
    function updateConcentricChart() {
        // Get updated statistics data from the DOM
        const unitsElement = $('.statistics-ajax-view').find('.statistics-title-units').next('.statistics-count');
        const amountElement = $('.statistics-ajax-view').find('.statistics-title-po-amount').next('.statistics-count');
        
        let updatedUnits = 0;
        let updatedAmount = 0;
        
        // Parse units value
        if (unitsElement.length > 0) {
            const unitsText = unitsElement.text().trim();
            updatedUnits = parseInt(unitsText.replace(/,/g, '')) || 0;
        }
        
        // Parse amount value (remove £ and commas)
        if (amountElement.length > 0) {
            const amountText = amountElement.text().trim();
            updatedAmount = parseFloat(amountText.replace(/[£,]/g, '')) || 0;
        }
        
        if (secondDonutChart) {
            // Update the chart data
            secondDonutChart.data.datasets[0].data = [updatedAmount, 0];  // Outer ring - Total Value
            secondDonutChart.data.datasets[1].data = [0, updatedUnits];   // Inner ring - Total Units
            secondDonutChart.update();
        }
    }

    // Listen for statistics filter changes
    $(document).on('change', '[name="search_status"], [name="search_from_date"], [name="search_to_date"], [name="search_from_country"], [name="search_to_warehouse"]', function() {
        setTimeout(updateConcentricChart, 1000); // Wait for AJAX to complete
    });

    // Listen for statistics AJAX completion
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.url && settings.url.includes('dashboard/get-statistics-filter')) {
            setTimeout(updateConcentricChart, 500);
        }
    });

    // Open Good-In Buyer filtered view in new tab when user clicks a slice
    if (totalTransactionsChart) {
        totalTransactionsCanvas.addEventListener('click', function(evt) {
            try {
                const points = totalTransactionsChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points && points.length > 0) {
                    const idx = points[0].index;
                    const label = totalTransactionsChart.data.labels[idx] || '';
                    let paramVal = '';
                    if (/COLLECTION/i.test(label)) {
                        paramVal = '{{ config("constants.COLLECTION") }}';
                    } else if (/DELIVERY/i.test(label)) {
                        paramVal = '{{ config("constants.DELIVERY") }}';
                    }
                    if (paramVal) {
                        // Build URL with all applied filters
                        let url = site_url + 'good-in-buyer?search_collection_delivery=' + encodeURIComponent(paramVal);
                        
                        // Add status filter
                        const searchStatus = $.trim($('[name="search_status"]').val());
                        if (searchStatus) {
                            url += '&search_status=' + encodeURIComponent(searchStatus);
                        }
                        
                        // Add date range filters based on status
                        if (searchStatus === '{{ config("constants.IN_TRANSIT_STATUS") }}') {
                            const fromDate = $.trim($('[name="search_buyer_delivery_from_date"]').val());
                            const toDate = $.trim($('[name="search_buyer_delivery_to_date"]').val());
                            if (fromDate) url += '&search_buyer_delivery_from_date=' + encodeURIComponent(fromDate);
                            if (toDate) url += '&search_buyer_delivery_to_date=' + encodeURIComponent(toDate);
                        } else {
                            const fromDate = $.trim($('[name="search_logistic_delivery_from_date"]').val());
                            const toDate = $.trim($('[name="search_logistic_delivery_to_date"]').val());
                            if (fromDate) url += '&search_logistic_delivery_from_date=' + encodeURIComponent(fromDate);
                            if (toDate) url += '&search_logistic_delivery_to_date=' + encodeURIComponent(toDate);
                        }
                        
                        // Add country filter
                        const searchCountry = $.trim($('[name="search_from_country"]').val());
                        if (searchCountry) {
                            url += '&search_from_country=' + encodeURIComponent(searchCountry);
                        }
                        
                        // Add warehouse filter
                        const searchWarehouse = $.trim($('[name="search_to_warehouse"]').val());
                        if (searchWarehouse) {
                            url += '&search_to_warehouse=' + encodeURIComponent(searchWarehouse);
                        }
                        
                        window.open(url, '_blank');
                    }
                }
            } catch (e) {
                console.error('Error opening filtered Good-In Buyer view', e);
            }
        });
    }

    // Goods Out Charts
    const goodsOutTotalUnits = {{ ( isset($goodsOutStatistics) && (!empty($goodsOutStatistics['totalUnits'])) ? (int)$goodsOutStatistics['totalUnits'] : 0) }};
    const goodsOutShipmentValue = {{ ( isset($goodsOutStatistics) && (!empty($goodsOutStatistics['shipmentValue'])) ? (float)$goodsOutStatistics['shipmentValue'] : 0) }};

    // Goods Out Total Transactions Chart
    const goodsOutTotalTransactionsData = {
        labels: ['TOTAL UNITS', 'SHIPMENT VALUE (£)'],
        datasets: [{
            data: [goodsOutTotalUnits, goodsOutShipmentValue],
            backgroundColor: [
                '#2E7D32',
                '#81C784'
            ],
            borderColor: [
                '#2E7D32',
                '#81C784'
            ],
            borderWidth: 1
        }]
    };

    let goodsOutTotalTransactionsChart;
    const goodsOutTotalTransactionsCtx = document.getElementById('goodsOutTotalTransactionsChart');
    if (goodsOutTotalTransactionsCtx) {
        goodsOutTotalTransactionsChart = new Chart(goodsOutTotalTransactionsCtx.getContext('2d'), {
            type: 'doughnut',
            data: goodsOutTotalTransactionsData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        onClick: null,
                        position: 'top',
                        labels: {
                            font: {
                                size: '12px',
                                weight:'500',
                            },
                            family:'Roboto',
                            color:'#000'
                        }
                    }
                },
                cutout: '70%'
            },
            plugins: [{
                afterDatasetsDraw(chart) {
                    const {ctx, chartArea: {left, top, width, height}} = chart;
                    ctx.save();
                    ctx.font = "bold 16px Roboto";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#000";
                    const text = "GOODS OUT";
                    const textX = left + (width - ctx.measureText(text).width) / 2;
                    const textY = top + height / 2;
                    ctx.fillText(text, textX, textY);
                    ctx.restore();
                }
            }]
        });

        goodsOutTotalTransactionsCtx.addEventListener('click', function(evt) {
            try {
                const points = goodsOutTotalTransactionsChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points && points.length > 0) {
                    let url = site_url + 'tracking-goods-out?';
                    
                    // Add location filter
                    const searchLocation = $.trim($('#goods_out_search_location').val());
                    if (searchLocation) {
                        url += '&search_to_country=' + encodeURIComponent(searchLocation);
                    }
                    
                    // Add status filter
                    const searchStatus = $.trim($('#goods_out_search_status').val());
                    if (searchStatus) {
                        url += '&search_status=' + encodeURIComponent(searchStatus);
                    }
                    
                    // Add date filters
                    const fromDate = $.trim($('[name="search_goods_out_buyer_delivery_from_date"]').val());
                    const toDate = $.trim($('[name="search_goods_out_buyer_delivery_to_date"]').val());
                    if (fromDate) url += '&search_collection_from_date=' + encodeURIComponent(fromDate);
                    if (toDate) url += '&search_collection_to_date=' + encodeURIComponent(toDate);
                    
                    // Add warehouse filter
                    const searchWarehouse = $.trim($('#search_goods_out_from_warehouse').val());
                    if (searchWarehouse) {
                        url += '&search_from_warehouse=' + encodeURIComponent(searchWarehouse);
                    }
                    
                    window.open(url, '_blank');
                }
            } catch (e) {
                console.error('Error opening filtered Goods Out view', e);
            }
        });
    }

    // Goods Out Mode of Transport Chart (Sea, Air, Road)
    const goodsOutSeaCount = 40;
    const goodsOutAirCount = 35;
    const goodsOutRoadCount = 25;
    
    const goodsOutModeOfTransportCanvas = document.getElementById('goodsOutModeOfTransportChart');
    const goodsOutTotalModeCount = (parseInt(goodsOutSeaCount) + parseInt(goodsOutAirCount) + parseInt(goodsOutRoadCount)).toLocaleString('en-GB');

    let goodsOutModeOfTransportChart = null;
    if (goodsOutModeOfTransportCanvas && goodsOutModeOfTransportCanvas.getContext) {
        const goodsOutModeOfTransportCtx = goodsOutModeOfTransportCanvas.getContext('2d');

        goodsOutModeOfTransportChart = new Chart(goodsOutModeOfTransportCtx, {
            type: 'doughnut',
            data: {
                labels: ['BY SEA', 'BY AIR', 'BY ROAD'],
                datasets: [{
                    data: [goodsOutSeaCount, goodsOutAirCount, goodsOutRoadCount],
                    backgroundColor: ['#8D191A', '#FFC7C7', '#E57373'],
                    borderColor: ['#8D191A', '#FFC7C7', '#E57373'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10,
                                weight: '500'
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            maxWidth: 100
                        },
                        align: 'center',
                        fullSize: true
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#8D191A',
                        borderWidth: 1,
                        cornerRadius: 4,
                        displayColors: true,
                        callbacks: {
                            title: function() { return null; },
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                return label + ': ' + value.toLocaleString('en-GB');
                            }
                        }
                    }
                },
                cutout: '70%'
            },
            plugins: [{
                id: 'customLegendStyling',
                afterLayout(chart) {
                    setTimeout(() => {
                        const legendItems = document.querySelectorAll('[role="legend"] tbody tr, [role="legend"] li');
                        legendItems.forEach(item => {
                            item.style.display = 'block';
                            item.style.width = '100%';
                        });
                    }, 0);
                }
            }, {
                id: 'centerText',
                afterDatasetsDraw(chart, args, options) {
                    const {ctx, chartArea: {left, top, width, height}} = chart;
                    
                    const data = chart.data.datasets[0].data;
                    const seaVal = parseInt(data[0]) || 0;
                    const airVal = parseInt(data[1]) || 0;
                    const roadVal = parseInt(data[2]) || 0;
                    const totalCount = seaVal + airVal + roadVal;
                    
                    const centerX = left + width / 2;
                    const centerY = top + height / 2;
                    const radius = Math.min(width, height) / 2 * 0.5;
                    
                    ctx.save();
                    
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                    ctx.fillStyle = '#ffffff';
                    ctx.fill();
                    
                    // Draw label text in two lines to fit within the donut hole
                    ctx.font = "10px Roboto";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#000";
                    ctx.textAlign = "center";
                    ctx.fillText("TOTAL", centerX, centerY - 14);
                    ctx.fillText("TRANSACTIONS", centerX, centerY - 2);
                    
                    ctx.font = "bold 16px Roboto";
                    const countText = totalCount.toLocaleString('en-GB');
                    ctx.fillText(countText, centerX, centerY + 14);

                    
                    ctx.restore();
                }
            }]
        });
    }

    // (Outer ring removed) — keep Total Transactions chart simple

    // Goods Out Location Chart
    const goodsOutLocationData = {
        labels: ['EU', 'USA', 'UK', 'OTHER'],
        datasets: [{
            data: [45, 30, 20, 5],
            backgroundColor: [
                '#1976D2',
                '#42A5F5',
                '#90CAF9',
                '#E3F2FD'
            ],
            borderColor: [
                '#1976D2',
                '#42A5F5',
                '#90CAF9',
                '#E3F2FD'
            ],
            borderWidth: 1
        }]
    };

    let goodsOutLocationChart;
    const goodsOutLocationCtx = document.getElementById('goodsOutLocationChart');
    if (goodsOutLocationCtx) {
        goodsOutLocationChart = new Chart(goodsOutLocationCtx.getContext('2d'), {
            type: 'pie',
            data: goodsOutLocationData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: '11px',
                                weight:'500',
                            },
                            family:'Roboto',
                            color:'#000'
                        }
                    },
                    title: {
                        display: true,
                        text: 'LOCATION-WISE DISTRIBUTION',
                        font: {
                            size: '14px',
                            weight:'bold',
                        },
                        family:'Roboto',
                        color:'#000'
                    }
                }
            }
        });
    }

    // Goods Out Status Chart
    const goodsOutStatusData = {
        labels: ['IN TRANSIT', 'DELIVERED', 'PENDING', 'OTHER'],
        datasets: [{
            data: [40, 35, 20, 5],
            backgroundColor: [
                '#FF9800',
                '#4CAF50',
                '#F44336',
                '#9E9E9E'
            ],
            borderColor: [
                '#FF9800',
                '#4CAF50',
                '#F44336',
                '#9E9E9E'
            ],
            borderWidth: 1
        }]
    };

    let goodsOutStatusChart;
    const goodsOutStatusCtx = document.getElementById('goodsOutStatusChart');
    if (goodsOutStatusCtx) {
        goodsOutStatusChart = new Chart(goodsOutStatusCtx.getContext('2d'), {
            type: 'pie',
            data: goodsOutStatusData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: '11px',
                                weight:'500',
                            },
                            family:'Roboto',
                            color:'#000'
                        }
                    },
                    title: {
                        display: true,
                        text: 'STATUS-WISE DISTRIBUTION',
                        font: {
                            size: '14px',
                            weight:'bold',
                        },
                        family:'Roboto',
                        color:'#000'
                    }
                }
            }
        });
    }

    // Function to update Goods Out charts based on filters
    function updateGoodsOutCharts(filters) {
        // Make AJAX call to get chart data
        $.ajax({
            url: module_url + '/filter-goods-out-statistics',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            data: filters,
            success: function(response) {
                if (response.status_code == 1) {
                    const data = response.data;
                    
                    // Update Total Transactions Chart (goods out middle ring)
                    if (goodsOutTotalTransactionsChart) {
                        goodsOutTotalTransactionsChart.data.datasets[0].data = [data.totalUnits, data.shipmentValue];
                        goodsOutTotalTransactionsChart.update();
                    }

                    // No changes to main Total Transactions chart for Goods Out filter (outer ring removed)
                    
                    // Update Location Chart (mock data for now - can be enhanced with real data)
                    if (goodsOutLocationChart && filters.search_location) {
                        const locationIndex = goodsOutLocationChart.data.labels.indexOf(filters.search_location.toUpperCase());
                        if (locationIndex !== -1) {
                            // Highlight selected location
                            const newData = [5, 5, 5, 5];
                            newData[locationIndex] = 60;
                            goodsOutLocationChart.data.datasets[0].data = newData;
                            goodsOutLocationChart.update();
                        }
                    }

                    // Update Mode of Transport Chart (mock data for now)
                    if (goodsOutModeOfTransportChart) {
                        goodsOutModeOfTransportChart.data.datasets[0].data = [
                            Math.floor(Math.random() * 50) + 10,
                            Math.floor(Math.random() * 50) + 10,
                            Math.floor(Math.random() * 50) + 10
                        ];
                        goodsOutModeOfTransportChart.update();
                    }
                    
                    // Update Status Chart (mock data for now - can be enhanced with real data)
                    if (goodsOutStatusChart && filters.search_status) {
                        const statusLabels = {
                            '{{ config("constants.IN_TRANSIT_STATUS") }}': 'In Transit',
                            '{{ config("constants.DELIVERED_STATUS") }}': 'Delivered'
                        };
                        const statusLabel = statusLabels[filters.search_status] || 'In Transit';
                        const statusIndex = goodsOutStatusChart.data.labels.indexOf(statusLabel.toUpperCase());
                        if (statusIndex !== -1) {
                            // Highlight selected status
                            const newData = [10, 10, 10, 10];
                            newData[statusIndex] = 70;
                            goodsOutStatusChart.data.datasets[0].data = newData;
                            goodsOutStatusChart.update();
                        }
                    }
                }
            }
        });
    }
    
    // Function to open BUYER DELIVERY details in new tab
    function openBuyerDelivery(warehouse, deliveryDate) {
        const url = `/good-in-buyer?warehouse=${encodeURIComponent(warehouse)}&delivery_date=${encodeURIComponent(deliveryDate)}`;
        window.open(url, '_blank');
    }
    
</script>
@endsection