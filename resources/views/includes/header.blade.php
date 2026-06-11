<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- generic meta -->
    <meta name="description" content="{{ config('constants.SITE_DESCRIPTION') }}" />
    <meta name="keywords" content="{{ config('constants.SITE_KEYWORDS') }}" />
    <meta name="author" content="Motadata" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- og meta -->
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ config('constants.SITE_TITLE') }}" />
    <meta property="og:description" content="{{ config('constants.SITE_DESCRIPTION') }}" />
    <meta property="og:url" content="{!! url('/') !!}" />
    <meta property="og:site_name" content="{{ config('constants.SITE_TITLE') }}" />
    <meta property="og:image" content="{{ asset('images/icon.png') }}" />
    <meta property="og:image:width" content="200" />
    <meta property="og:image:height" content="200" />
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{!! url('/') !!}">
    <meta property="twitter:title" content="{{ config('constants.SITE_TITLE')}}">
    <meta property="twitter:description" content="{{ config('constants.SITE_DESCRIPTION') }}">
    <meta property="twitter:image" content="{{ asset('images/logo.png') }}">
    <!-- theme-color: for chrome mobile -->
    <meta name="theme-color" content="#0063c1">
    <!-- favicon -->
    <!-- <link rel="icon" href="{{ asset('images/motadata-favicon.png') }}"> -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- preconnect -->
    <link href='https://fonts.gstatic.com' crossorigin='anonymous' rel='preconnect' />
    <!-- page title -->
    <title>@yield('pageTitle') | {{ config('constants.SITE_TITLE', 'Laravel Starter For Admin Panel')}} </title>
    <!-- css -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset ('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/alertify.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/alertify.bs.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/fontawesome/css/all.min.css') }}">

    <!-- main css -->

    <link rel="stylesheet" href="{{ asset ('css/main.css') . '?ver=' .config('constants.CSS_JS_VERSION') }}">
    <link rel="stylesheet" href="{{ asset ('css/style.css') . '?ver=' .config('constants.CSS_JS_VERSION') }}">

    <!-- scripts -->
    <script src="{{ asset ('js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset ('js/moment.min.js') }}"></script>
    <script src="{{ asset ('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset ('js/select2.js') }}"></script>
    <script src="{{ asset ('js/bootstrap.bundle.min.js') }}"></script>
    <!-- plugins -->
    <script type="text/javascript" src="{{ asset ('js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/validator-additional-methods.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/alertify.min.js') }}"></script>
    <!-- main script -->

    <script>
        var site_url = "{{ url('/') }}" + '/';
        var unique_shipment_id = true;
        var unique_shipment_array = [];
        //console.log(site_url)
    </script>
    <script src="{{ asset ('js/messages.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
    <script src="{{ asset ('js/common.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
    <script src="{{ asset ('js/common-functions.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
    
    <!-- Announcement Loading Script -->
    <script>
        $(document).ready(function() {
            loadAnnouncements();
                $(window).on('resize', function() {
                updateNavbarWrapperHeight();
            });
        });
        
        function updateNavbarWrapperHeight() {
            var topOffset = $("#navMain").attr("data-offset") ? parseInt($("#navMain").attr("data-offset")) : 0;
            if ($(".main-navbar-wrapper").hasClass("fallen-nav") || $(".main-navbar-wrapper").hasClass("notch-nav")) {
                $(".main-navbar-wrapper").css("min-height", $("#navMain").outerHeight() + topOffset);
            } else {
                $(".main-navbar-wrapper").css("min-height", $("#navMain").outerHeight());
            }
        }
        
        function loadAnnouncements() {
            $.ajax({
                url: site_url + 'announcement/get-active',
                type: 'GET',
                success: function(response) {
                    if(response.length > 0) {
                        var announcementText = '';
                        $.each(response, function(index, announcement) {
                            if(announcementText) announcementText += ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; • &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                            announcementText += announcement.v_marquee_text;
                        });
                        
                        // Repeat the announcements stream to make it run continuously with no gaps
                        var separator = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; • &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                        var repeatedText = announcementText + separator + announcementText + separator + announcementText;
                        
                        $('#announcement-content').html(repeatedText);
                        $('#announcement-marquee').show();
                        updateNavbarWrapperHeight();
                    } else {
                        $('#announcement-marquee').hide();
                        updateNavbarWrapperHeight();
                    }
                },
                error: function() {
                    $('#announcement-marquee').hide();
                    updateNavbarWrapperHeight();
                }
            });
        }
        
        // Refresh announcements every 5 minutes
        setInterval(loadAnnouncements, 300000);
    </script>
    <script>
        $(function() {
            $('input[type="text"]').attr('autocomplete', 'off');
        });
        $(document).ajaxSuccess(function() {
            $('input[type="text"]').attr('autocomplete', 'off');
        });
    </script>
</head>
<?php //echo '<pre>';print_r($notificationDetails);die;
?>

<body class="">
    <!-- navbar start -->
    <div class="main-navbar-wrapper">
        <nav class="navbar twt-navbar twt-navbar-common navbar-expand-lg nav-light p-0 flex-lg-column bg-white" id="navMain">
            <div class="nav-top w-100">
                <div class="container-fluid d-flex align-items-center justify-content-between px-lg-5 header-top-div">
                    <a class="navbar-brand py-2" href="{!! url('/') !!}">
                        <img src="{{ asset ('images/logo.png') }}" alt="" class="brand-logo-img">
                    </a>
                    <img src="{{ asset ('images/header-gif1.gif') }}" alt="header-gif" class="header-gif header-gif1">
                    <div class="header-text d-xl-block d-none">
                        <span class="heading-content">LOGISTIC MANAGEMENT SYSTEM</span>
                    </div>
                    <img src="{{ asset ('images/header-gif2.gif') }}" alt="header-gif" class="header-gif header-gif2">
                    <div class="d-flex align-items-center">
                        <div class="align-self-end d-flex align-items-center actions-nav">
                            <ul class="nav custom-flex-direction align-items-center">
                                <li class="nav-item my-account login-admin-icon mr-2">
                                    <div class="dropdown login-btn ml-auto">
                                        <a class="dropdown-toggle custom-dropdown-toggle drop-menu d-inline-block" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <div class="d-flex align-items-center pr-xl-0 pr-3 login-dropdown">
                                                <div class="user-header-icon">
                                                    <i class="far fa-user user-icon-header"></i>
                                                </div>
                                                <span class="line-height-low pr-lg-0 pr-5 text-theme">{{ session()->get('name') ? session()->get('name'): trans("messages.admin") }}<i class="fas fa-chevron-down ml-2"></i></span>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right login-dropdown" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="{{ url('change-password') }}"><span class="icon-dropdown text-muted mr-2"> <i class="fas fa-lock "></i> </span>{{ trans("messages.change-password") }}</a>
                                        </div>

                                    </div>
                                </li>
                                <li class="nav-item my-account login-admin-icon mb-1 d-flex align-items-center"><a class="nav-link logout-btn d-flex align-items-center" href="{{ url('logout') }}" title="{{ trans('messages.logout') }}"><i class="fas fa-power-off logout-icon"></i><span class="d-lg-block d-none">{{ trans("messages.logout") }} &nbsp;</span> ( <span id="logout-time-indicator" class="color-full logout-time-indicator"></span>  ) </a></li>
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn navbar-toggler border-0 px-0 ml-auto" id="slide-toggle">
                        <span class="navbar-dash"></span>
                        <span class="navbar-dash"></span>
                        <span class="navbar-dash"></span>
                    </button>
                </div>
            </div>
            <div class="nav-bottom w-100 bg-dark-alpha-2">
                <div class="container-fluid">
                    <div class="slide navbar-slide" id="slideNav">
                        <ul class="navbar-nav ml-auto p-0" id="elastic_parents" data-targets=".nav-item">
                            <li class="nav-item"><a class="nav-link" href="{{ url('dashboard') }}" title="Dashboard"><i class="fa fa-desktop pr-2"></i>DASHBOARD</a></li>
                            <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_IN_BUYER')) != false) || (checkPermission(config('permission_constants.VIEW_GOODS_IN_LOGISTIC')) != false) || (strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))) { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="menudropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-level-down-alt pr-2"></i>
                                        <span class="nav-text">{{ trans('messages.goods-in') }}</span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="menudropdown">
                                        <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_IN_BUYER')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.GOODS_IN_BUYER_MASTER_URL') }}" title="{{ trans('messages.buyer') }}">{{ trans('messages.buyer') }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_IN_LOGISTIC')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.GOODS_IN_LOGITIC_MASTER_URL') }}" title="{{ trans('messages.logistic') }}">{{ trans('messages.logistic') }}</a>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>
                            <?php
                            if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_GOODS_OUT_TO_AMAZON')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false) ||
                                (strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                            ) {
                            ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="menudropdown1" role="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-level-up-alt pr-2"></i>
                                        <span class="nav-text">{{ trans('messages.goods-out') }}</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="menudropdown1">
                                        <?php
                                        if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false) ||
                                            (checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false) ||
                                            (checkPermission(config('permission_constants.VIEW_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false) ||
                                            (checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false) ||
                                            (strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                                        ) {
                                        ?>
                                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" title="{{ trans('messages.usa') }}" href="javascript:void(0);">{{ trans('messages.usa') }}</a>
                                                <ul class="dropdown-menu sub-dropdown-menu">
                                                    <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_UK_OTHER_COUNTRY_TO_US_PORT')) != false)) { ?>
                                                        <li><a class="dropdown-item" href="{{ config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL') }}">{{ trans('messages.uk-other-country-us-port') }}</a></li>
                                                    <?php } ?>
                                                    <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false)) { ?>
                                                        <li><a class="dropdown-item" href="{{ config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_URL') }}">{{ trans('messages.us-port-to-agent-warehouse') }}</a></li>
                                                    <?php } ?>
                                                    <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_AGENT_WAREHOUSE_TO_AMAZON')) != false)) { ?>
                                                        <li><a class="dropdown-item" href="{{ config('constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL') }}">{{ trans('messages.agent-warehouse-to-amazon-warehouse-customer') }}</a></li>
                                                    <?php } ?>
                                                    <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_US_WAREHOUSE_TO_AMAZON_MASTER')) != false)) { ?>
                                                        <li><a class="dropdown-item" href="{{ config('constants.US_WAREHOUSE_TO_AMAZON_MASTER_URL') }}">{{ trans('messages.us-warehouse-to-amazon-customer-uk-warehouse') }}</a></li>
                                                    <?php } ?>
                                                    <?php if ((checkPermission(config('permission_constants.VIEW_USA_CONTAINER_CLUBBING')) != false)) { ?>
                                                    	<li><a class="dropdown-item" href="{{ config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') }}">{{ trans('messages.usa-container-clubbing') }}</a></li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>
                                        <?php
                                        if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_TO_AMAZON')) != false) ||
                                            (strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                                        ) {
                                        ?>
                                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" title="{{ trans('messages.europe') }}" href="javascript:void(0);">{{ trans('messages.europe') }}</a>
                                                <ul class="dropdown-menu sub-dropdown-menu">
                                                    <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_TO_AMAZON')) != false)) { ?>
                                                        <li><a class="dropdown-item" href="{{ config('constants.EUROPE_TO_AMAZON_MASTER_URL') }}">{{ trans('messages.to-amazon') }}</a></li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php
                                        } ?>
                                    </ul>
                                </li>
                            <?php
                            } ?>

                            <?php if ((checkPermission(config('permission_constants.VIEW_GOODS_OUT_INTERNAL_TRANSFER')) != false) || (strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))) { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="menudropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="far fa-file pr-2"></i>
                                        <span class="nav-text">{{ trans('messages.transfer') }}</span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="menudropdown">
                                        <a class="dropdown-item" href="{{ config('constants.EUROPE_INTERNAL_TRANSFER_MASTER_URL') }}" title="{{ trans('messages.internal-transfer') }}">{{ trans('messages.internal-transfer') }}</a>
                                    </div>
                                </li>
                            <?php } ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="menudropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                    <i class="far fa-file pr-2"></i>
                                    <span class="nav-text">{{ trans('messages.reports') }}</span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="menudropdown1">
                                	<li>
                                		<?php if ((checkPermission(config('permission_constants.VIEW_TRACKING_GOODS_IN_REPORT')) != false)) { ?>
                                       	 <a class="dropdown-item" href="{{ config('constants.TRACKING_GOODS_IN_MASTER_URL') }}" title="{{ trans('messages.tracking-goods-in') }}">{{ trans('messages.tracking-goods-in') }}</a>
                                    	<?php } ?>
                                	</li>
                                	<?php /* ?>
                                	<li>
                                		<?php if ((checkPermission(config('permission_constants.VIEW_TRACKING_GOODS_OUT_REPORT')) != false)) { ?>
                                       	 <a class="dropdown-item" href="{{ config('constants.TRACKING_GOODS_OUT_MASTER_URL') }}" title="{{ trans('messages.tracking-goods-out') }}">{{ trans('messages.tracking-goods-out') }}</a>
                                    	<?php } ?>
                                	</li>
                                	<?php */ ?>
                                	<li>
                                		<a class="dropdown-item" href="{{ url('login-history') }}" title="{{ trans('messages.login-history') }}">{{ trans('messages.login-history') }}</a>
                                	</li>
                                	@if((checkPermission(config('permission_constants.VIEW_UK_TO_AMAZON_USA_FBA')) != false) || (checkPermission(config('permission_constants.VIEW_US_WAREHOUSE_FBA')) != false) || (checkPermission(config('permission_constants.VIEW_AMAZON_EU_FBA')) != false))
	                                	<li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" title="{{ trans('messages.fba-report') }}" href="javascript:void(0);">{{ trans('messages.fba-report') }}</a>
	                                    	<ul class="dropdown-menu sub-dropdown-menu">
	                                    		@if(checkPermission(config('permission_constants.VIEW_UK_TO_AMAZON_USA_FBA')) != false)
		                                        	<li>
		                                            	<a class="dropdown-item" href="{{ config('constants.FBA_REPORT_URL') }}" title="{{ trans('messages.uk-to-amazon-usa') }}">{{ trans('messages.uk-to-amazon-usa') }}</a>
		                                            </li>
	                                            @endif
	                                            @if(checkPermission(config('permission_constants.VIEW_US_WAREHOUSE_FBA')) != false)
		                                           <li>
		                                            	<a class="dropdown-item" href="{{ config('constants.US_WAREHOUSE_FBA_REPORT_URL') }}" title="{{ trans('messages.us-warehouse') }}">{{ trans('messages.us-warehouse') }}</a>
		                                            </li>
		                                    	@endif
	                                            @if(checkPermission(config('permission_constants.VIEW_AMAZON_EU_FBA')) != false)
		                                            <li>
		                                            	<a class="dropdown-item" href="{{ config('constants.EUROPE_TO_AMAZON_REPORT_URL') }}" title="{{ trans('messages.amazon-eu') }}">{{ trans('messages.amazon-eu') }}</a>
		                                            </li>
                                            	@endif
	                                      	</ul>
	                                  	</li>
                                  	@endif
                                </ul>
                            </li>
                            <?php if ((checkPermission(config('permission_constants.VIEW_LOCATION')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_DOCUMENT_TYPE')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_LOGISTIC_PARTNER')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_COUNTRY')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_DIMENSION')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_STATUS')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_COMPANY')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_CURRENCY')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_WAREHOUSE')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_SUPPLIER')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_PORT')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_CUSTOMER')) != false) ||
                                (checkPermission(config('permission_constants.VIEW_EMPLOYEE_MASTER')) != false) ||
                                (strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN'))) ||
                            	( strtolower(session()->get('role')) == strtolower(config('constants.ROLE_USER')) && ( checkUserRoleBase(session()->get('user_id')) ) )
                            ) { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="menudropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-list-ul"></i>
                                        <span class="nav-text">{{ trans('messages.master') }}</span>
                                    </a>
                                    <div class="dropdown-menu master-dropdown-menu" aria-labelledby="menudropdown">

                                        <?php if ((checkPermission(config('permission_constants.VIEW_SUPPLIER')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.SUPPLIER_MASTER_URL') }}" title="{{ trans('messages.supplier-master') }}">{{ trans("messages.supplier-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_LOGISTIC_PARTNER')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.LOGISTIC_PARTNER_MASTER_URL') }}" title="{{ trans('messages.logistic-partner-master') }}">{{ trans("messages.logistic-partner-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_CUSTOMER')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.CUSTOMER_MASTER_URL') }}" title="{{ trans('messages.customer-master') }}">{{ trans("messages.customer-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_EMPLOYEE_MASTER')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.USERS_URL') }}" title="{{ trans('messages.employee-master') }}">{{ trans('messages.employee-master') }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_LOCATION')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.LOCATION_MASTER_URL') }}" title="{{ trans('messages.master-module' , [ 'module' => trans('messages.location') ]) }}">{{ trans('messages.master-module' , [ 'module' => trans('messages.location') ]) }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_WAREHOUSE')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.WAREHOUSE_MASTER_URL') }}" title="{{ trans('messages.warehouse-master') }}">{{ trans("messages.warehouse-master") }}</a>
                                        <?php } ?>
                                        <?php if( strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')) || ( strtolower(session()->get('role')) == strtolower(config('constants.ROLE_USER')) && ( checkUserRoleBase(session()->get('user_id')) )   ) ){?>
                                        	<a class="dropdown-item" href="{{ config('constants.WAREHOUSE_PALLET_MASTER_URL') }}" title="{{ trans('messages.warehouse-pallet-limit') }}">{{ trans("messages.warehouse-pallet-limit") }}</a>
                                        <?php } ?>	
                                        <?php if ((checkPermission(config('permission_constants.VIEW_COMPANY')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.COMPANY_MASTER_URL') }}" title="{{ trans('messages.company-master') }}">{{ trans("messages.company-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_STATUS')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.STATUS_MASTER_URL') }}" title="{{ trans('messages.status-master') }}">{{ trans("messages.status-master") }}</a>
                                        <?php } ?>
                                        <a class="dropdown-item" href="{{ url('announcement') }}" title="{{ trans('messages.announcements') }}"><i class="fas fa-bullhorn mr-1"></i>Announcements</a>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_DIMENSION')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.DIMENSION_MASTER_URL') }}" title="{{ trans('messages.dimension-master') }}">{{ trans("messages.dimension-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_DOCUMENT_TYPE')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.DOCUMENT_TYPE_MASTER_URL') }}" title="{{ trans('messages.document-type-master') }}">{{ trans("messages.document-type-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_CURRENCY')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.CURRENCY_MASTER_URL') }}" title="{{ trans('messages.currency-master') }}">{{ trans("messages.currency-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_PORT')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.PORT_MASTER_URL') }}" title="{{ trans('messages.port-master') }}">{{ trans("messages.port-master") }}</a>
                                        <?php } ?>
                                        <?php if ((checkPermission(config('permission_constants.VIEW_COUNTRY')) != false)) { ?>
                                            <a class="dropdown-item" href="{{ config('constants.COUNTRY_MASTER_URL') }}" title="{{ trans('messages.country-master') }}">{{ trans("messages.country-master") }}</a>
                                        <?php } ?>
											<a class="dropdown-item" href="{{ config('constants.PAYMENT_TERMS_MASTER_URL') }}" title="{{ trans ( 'messages.payment-terms-master' )  }}">{{ trans("messages.payment-terms-master") }}</a>
											<a class="dropdown-item" href="{{ config('constants.GOODS_REMARK_MASTER_URL') }}" title="{{ trans('messages.goods-remark-master') }}">{{ trans("messages.goods-remark-master") }}</a>
											<a class="dropdown-item" href="{{ config('constants.DANGEROUS_GOODS_MASTER_URL') }}" title="{{ trans ( 'messages.dangerous-goods-master' ) }}">{{ trans ( 'messages.dangerous-goods-master' ) }}</a>
											<a class="dropdown-item" href="{{ config('constants.BOOKING_PORTAL_MASTER_URL') }}" title="{{ trans ( 'messages.master-module', [ 'module' => trans('messages.booking-portal') ] ) }}">{{ trans ( 'messages.master-module', [ 'module' => trans('messages.booking-portal') ] ) }}</a>
											<a class="dropdown-item" href="{{ config('constants.DAILY_MAIL_MASTER_URL') }}" title="{{ trans ( 'messages.master-module', [ 'module' => trans('messages.daily-mail') ] ) }}">{{ trans ( 'messages.master-module', [ 'module' => trans('messages.daily-mail') ] ) }}</a>
                                    </div>
                                </li>
                            <?php } ?>
                            
                            <!-- <li class="nav-item ml-auto d-none"><a class="nav-link" href="javascript:void(0)"><i class="fas fa-download"></i> {{ trans("messages.download") }}</a></li> -->
                            <?php /* <div class="ml-auto header-top-counter">
                                <input type="hidden" id="set-time" value="5" />
                                <div id="countdown">
                                    <i class="fa fa-clock power-off-icon"></i>
                                    <div id='tiles' class="color-full"></div>
                                    <!-- <div id="left" class="countdown-label">Time Remaining</div> -->
                                </div>
                            </div> */ ?>
                        </ul>

                    </div>
                </div>
            </div>
            <!-- Announcement Marquee -->
            <div id="announcement-marquee" class="announcement-marquee w-100" style="display: none;">
                <div class="marquee-container">
                    <div class="marquee-label">
                        <i class="fa fa-bullhorn"></i> NEWS
                    </div>
                    <div class="marquee-content-wrap">
                        <marquee behavior="scroll" direction="left" scrollamount="5">
                            <span id="announcement-content" class="announcement-content"></span>
                        </marquee>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Common Forms for CRUD Operations -->
    {!! Form::open(array( 'id '=> 'delete-record-form' , 'method' => 'post' ,  'url' => 'removeRecord' )) !!}
        <input type="hidden" name="delete_record_id" value="">
        <input type="hidden" name="delete_module_name" value="">
    {!! Form::close() !!}

    {!! Form::open(array( 'id '=> 'manage-session-messages-form' , 'method' => 'post' ,  'url' => 'manage-session-messages'  )) !!}
        <input type="hidden" name="session_redirect_module_url" value="">
        <input type="hidden" name="session_redirect_module_name" value="">
        <input type="hidden" name="session_redirect_module_action" value="add">
    {!! Form::close() !!}

    @yield('content')
    <!-- nav end  -->

    @include('admin/add-lookup-modal')
    <script type="text/javascript" src="{{ asset ('js/jquery.cookie.js') }} "></script>
    <div class="modal fade document-folder logout-alert-modal" id="logout-alert-modal" tabindex="-1" aria-labelledby="staticBackdropLabel" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered px-sm-5">
            <div class="modal-content">
                <div class="modal-body p-0">

                    <div class="row no-gutters justify-content-center">
                        <div class="col-12">
                            <div class="logout-mess-popup">
                                <div>
                                    <div class="password-icon">
                                        <i class="fa fa-power-off" aria-hidden="true"></i>
                                    </div>
                                    <div class="title-text mb-3">
                                        <h1 class="background text-uppercase">{{ trans("messages.logout-alert") }}</h1>
                                    </div>
                                    <label>You will be logged out from system in next <br> <span class="text-bold h5"> {{ config('constants.FORCE_LOGOUT_AFTER_POPUP') }} Minutes. </span> </label>
                                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn submit bg-theme login-button text-white font-weight-bold mt-4">{{ trans("messages.ok") }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <footer class="main-footer-section d-none d-lg-block">
        <div class="card card-body bg-theme border-0 rounded-0 shadow py-2">
            <div class="copyright">
                <div class="container-fluid">
                    <p class="mb-0 text-white d-flex flex-wrap flex-direction-column justify-content-center">
                        <span class="text-white pr-1">&copy; <?php echo Date('Y') ?></span>
                        <span><span class="text-white"> Developed by</span><a href="http://thewildtigers.com/" target="_blank" rel="noopener noreferrer" class="pl-1 text-white">The WildTigers Technologies</a></span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script>
		function pad(n) {
            return (n < 10 ? "0" : "") + n;
        }
    </script>

    <script>
        function redirectPreviousPage() {
            //console.log('previous url')
            //console.log('{{ url()->previous() }}')
            //window.location.href = '{{ url()->previous() }}';
            window.history.back();
        }
    </script>
    <!-- Notification -->
    <script>
        // before script
        var detect_open_notification = false;
        $('.icon_wrap').on('click', function() {
            detect_open_notification = true;
            if ($(this).parent().hasClass('active')) {
                $(this).parent().removeClass('active');
            } else {
                $(this).parent().addClass('active');
            }
        });

        $(function(){
            $('.notifications').removeClass('active');
        });

        $('.main-navbar-wrapper').click(function(e) {
            if (detect_open_notification != true) {
                if ($('.notifications').hasClass('active') != false) {
                    $('.notifications').removeClass('active');
                }
            } else {
                detect_open_notification = false;
            }
        });

        $.validator.addMethod("validateUniqueEmail", function(value, element) {
            var result = true;
            ajaxResponse = $.ajax({
                type: "POST",
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: site_url + 'checkUniqueUserEmail',
                dataType: "json",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'email': $.trim($("[name='email']").val()),
                    'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
                },
                beforeSend: function() {
                    //block ui
                    //showLoader();
                },
                success: function(response) {
                    if (response.status_code == 1) {
                        return false;
                    } else {
                        result = false;
                        return true;
                    }
                }
            });
            return result;
        }, 'This Email is already in use. Please try another email.');

        var cookie_prefix_name = '{{ config("constants.LOGIN_COOKIE_NAME") }}';

        var user_login_time = '{{ session()->has("login_time") ? session()->get("login_time") : null  }}';
		//console.log("user_login_time = "  + user_login_time );
        var current_user_login_time = user_login_time;

        var logout_alert_status = '{{ session()->has("showLogoutAlert") ? session()->get("showLogoutAlert") : config("constants.SELECTION_NO")  }}';

        setInterval(function() {
            var current_time = parseInt(new Date().getTime() / 1000);
            var logout_alert_duration = '{{ config("constants.LOGOUT_TIME_DURATION") }}';
            //console.log("remaining sec = " + ( parseInt(current_user_login_time) + parseInt( logout_alert_duration *  60 ) - parseInt( current_time ) ) );
            if (parseInt(current_time) >= parseInt(current_user_login_time) + (parseInt(logout_alert_duration) * 60)) {
                if (logout_alert_status == "" || logout_alert_status == null || logout_alert_status == undefined || logout_alert_status == '{{ config("constants.SELECTION_NO")  }}') {

                    $.ajax({
                        type: "POST",
                        async: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: site_url + 'set-logout-status',
                        dataType: "json",
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        beforeSend: function() {
                            //block ui
                            //showLoader();
                        },
                        success: function(response) {
                            if (response.status_code == 1) {

                            }

                        }
                    });
                    logout_alert_status = '{{ config("constants.SELECTION_YES")  }}';
                    openBootstrapModal('logout-alert-modal');
                }
            }
            var force_logout_after_popup = '{{ config("constants.FORCE_LOGOUT_AFTER_POPUP") }}';
            if (parseInt(current_time) >= (parseInt(current_user_login_time) + (parseInt(logout_alert_duration) * 60) + (parseInt(logout_alert_duration) * 60))) {
                //window.location =  site_url  + "force-logout";
            }
            var current_time = new Date().getTime();

            var logout_time_limit = ( ( parseInt( force_logout_after_popup ) + parseInt(logout_alert_duration) ) * 60 );
			var seconds_left = ( ( parseInt(user_login_time) + logout_time_limit ) - parseInt(current_time/1000)  );

			if (seconds_left >= 0) {
				 var days = pad(parseInt(seconds_left / 86400));
	             seconds_left = seconds_left % 86400;

	             var hours = pad(parseInt(seconds_left / 3600));
	             seconds_left = seconds_left % 3600;

	             var minutes = pad(parseInt(seconds_left / 60));
	             seconds = pad(parseInt(seconds_left % 60));

	             $(".logout-time-indicator").html("</span><span>" + minutes + ":</span><span>" + seconds + "</span>");
			} else {
				$(".logout-time-indicator").html("-");
			}
		}, 1000);
    </script>

    <!-- Page-specific scripts will be yielded here -->
    @yield('scripts')

    
    


</body>

</html>