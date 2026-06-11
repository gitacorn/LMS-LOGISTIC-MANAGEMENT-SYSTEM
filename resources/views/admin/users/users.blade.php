@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/dataTables.bootstrap4.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset ('js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/dataTables.bootstrap4.js') }}"></script>


<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.employee-master") }} (<span class="total-record-count"></span>)</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	<?php if((checkPermission(config('permission_constants.ADD_EMPLOYEE_MASTER')) != false)){?>
            	<a href="{{ config('constants.USERS_URL') . '/create'  }}" class="btn btn-theme text-white button-actions-top-bar d-flex align-items-center  border btn-sm mr-2" title="{{ trans('messages.add-employee') }}"><i class="fas fa-plus mr-md-2"></i><span class="d-md-block d-none"> {{ trans("messages.add-employee") }}</span></a>
            <?php } ?>
            <button class="btn btn button-actions-top-bar filter-btn d-flex align-items-center  border btn-sm" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i><span class="d-md-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>

    <section class="inner-wrapper-common-section main-listing-section">
        <div class="container-fluid">
            <?php
            $tableSearchPlaceholder = "Search By Name, Email, Contact No.";
            ?>
            <div class="collapse" id="filter">
                <div class="card card-body mb-3">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="search_user" class="control-label">{{ trans("messages.search-by") }}</label>
                                <input type="text" class="form-control twt-enter-search custom-input" name="search_user" id="search_user" placeholder="<?php echo $tableSearchPlaceholder ?>">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="search_role">{{ trans("messages.role") }}</label>
                                <select class="form-control" name="search_role" onchange="filterData(this);">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="{{ config('constants.LOGISTIC') }}">{{ trans("messages.logistic") }}</option>
                                    <option value="{{ config('constants.BUYER') }}">{{ trans("messages.buyer") }}</option>
                                     <option value="{{ config('constants.GOODS_IN_WAREHOUSE') }}">{{ trans("messages.goods-in-warehouse") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                                <select class="form-control" name="search_status" onchange="filterData(this);">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="{{ config('constants.ENABLE_STATUS') }}">{{ trans("messages.enable") }}</option>
                                    <option value="{{ config('constants.DISABLE_STATUS') }}">{{ trans("messages.disable") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="search_added_password">{{ trans("messages.password-added") }}</label>
                                <select class="form-control" name="search_added_password" onchange="filterData(this);">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="{{ config('constants.SELECTION_YES') }}">{{ trans("messages.yes") }}</option>
                                    <option value="{{ config('constants.SELECTION_NO') }}">{{ trans("messages.no") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="search_permission_given">{{ trans("messages.permission-given") }}</label>
                                <select class="form-control" name="search_permission_given" onchange="filterData(this);">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="{{ config('constants.SELECTION_YES') }}">{{ trans("messages.yes") }}</option>
                                    <option value="{{ config('constants.SELECTION_NO') }}">{{ trans("messages.no") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="search_warehouse_name">{{ trans("messages.warehouse") }}</label>
                                <select class="form-control" name="search_warehouse_name" onchange="filterData(this);">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    @if(!empty($warehouseDetails))
										@foreach ($warehouseDetails as $warehouseDetail)
											{{ $encodeId = Wild_tiger::encode($warehouseDetail->i_id);}}
											<option value="{{ $encodeId }}" >{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        @endforeach
	                               @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 d-flex align-items-end gap pt-3">
                            <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData(this);">{{ trans("messages.search") }}</button>
                            <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-result-wrapper">
                <div class="card card-body shadow-sm">
                    {{ Wild_tiger::readMessage() }}
                    <div class="table-responsive fixed-table-x">
                        <table class="table table-sm table-bordered table-hover" id="user-table">
                            <thead>
                                <tr>
                                    <th>{{ trans("messages.sr-no") }}</th>
                                    <th class="text-left">{{ trans("messages.name") }}</th>
                                    <th class="text-left">{{ trans("messages.email") }}</th>
                                    <th class="text-left">{{ trans("messages.contact-no") }}</th>
                                    <th class="text-left">{{ trans("messages.department-name") }}</th>
                                    <th class="text-left">{{ trans("messages.role") }}</th>
                                     <th class="text-left">{{ trans("messages.warehouse") }}</th>
                                    <th>{{ trans("messages.status") }}</th>
                                    <?php if( (checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != false) || (checkPermission(config('permission_constants.DELETE_EMPLOYEE_MASTER')) != false) ){?>
                                    <th>{{ trans("messages.actions") }}</th>
                                    <?php } ?>
                                </tr>
                            </thead>

                            <tbody class="ajax-view">
								@include( config('constants.AJAX_VIEW_FOLDER') . 'user-master/user-master-list')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </section>
</main>



<script>
var user_module_url = '{{ config("constants.USERS_URL") }}' + '/';
    function searchField() {

        var search_user = $.trim($("[name='search_user']").val());
        var search_status = $.trim($("[name='search_status']").val());
        var search_role = $.trim($("[name='search_role']").val());
        var search_added_password = $.trim($("[name='search_added_password']").val());
        var search_permission_given =  $.trim($("[name='search_permission_given']").val());
        var search_warehouse_name =  $.trim($("[name='search_warehouse_name']").val());
        
        var searchData = {
            'search_user': search_user,
            'search_status': search_status,
            'search_role':search_role,
            'search_added_password':search_added_password,
            'search_permission_given':search_permission_given,
            'search_warehouse_name':search_warehouse_name
        }
        return searchData;
    }

    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(user_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = user_module_url + 'filter'
   
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection