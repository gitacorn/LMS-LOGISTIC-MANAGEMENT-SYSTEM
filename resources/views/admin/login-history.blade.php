@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.all-login-history") }} (<span class="total-record-count"></span>)</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid visit-history">
        <div class="collapse" id="filter">
            <div class="card mb-3">
                <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">{{ trans("messages.search-by") }}</label>
                            <div class="date">
                                <input type="text" class="form-control mb-3" id="search_by" name="search_by" placeholder="Search by User Name" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="control-label">{{ trans("messages.start-date") }}</label>
                            <div class="date">
                                <input type="text" class="form-control date mb-3" name="search_start_date"  placeholder="DD-MM-YYYY" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="control-label">{{ trans("messages.end-date") }}</label>
                            <div class="date">
                                <input type="text" class="form-control date mb-3" name="search_end_date"  placeholder="DD-MM-YYYY" />
                            </div>
                        </div>
                        <div class="col-md-3 pt-3">
                            <a class="btn btn-theme text-white mt-md-3" href="javascript:void(0)" onclick="filterData()" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</a>
                            <button class="btn btn-outline-secondary reset-wild-tigers mt-md-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="table-responsive fixed-table-x">
                    <table class="table table-hover table-bordered table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="min-width: 80px;max-width:80px;">{{ trans("messages.username") }}</th>
                                <th class="text-center" style="min-width: 80px;max-width:80px;">{{ trans("messages.login-date") }}</th>
                                <th class="actions-col" style="min-width: 200px;max-width:200px;">{{ trans("messages.ip-address") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'login-history/login-history-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    var login_history_url = '{{ config("constants.LOGIN_HISTORY_URL") }}' + '/';

    $(document).ready(function() {

        //init date time picker
        $("[name='search_start_date'],[name='search_end_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });

    });

    function searchField() {
        var search_start_date = $.trim($("[name='search_start_date']").val());
        var search_end_date = $.trim($("[name='search_end_date']").val());
        var search_by = $.trim($("[name='search_by']").val());

        var searchData = {
            'search_by': search_by,
            'search_start_date': search_start_date,
            'search_end_date': search_end_date
        }

        return searchData;
    }


    //filter login history listing
    function filterData() {

        var searchFieldName = searchField();

        searchAjax(login_history_url + 'filter', searchFieldName);

    }
    var paginationUrl = login_history_url + 'filter'
    //daepicker management
    $(function() {
        $("[name='search_start_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var incrementDay = moment(new Date(e.date));
            $("[name='search_end_date']").data('DateTimePicker').minDate(incrementDay);
        }else{
            $("[name='search_end_date']").data('DateTimePicker').minDate(false);
        }
            $(this).data("DateTimePicker").hide();
        });

        $("[name='search_end_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
            var decrementDay = moment(new Date(e.date));
            $("[name='search_start_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
            $("[name='search_start_date']").data('DateTimePicker').maxDate(false);
        }
            $(this).data("DateTimePicker").hide();
        });
    });
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>


@endsection