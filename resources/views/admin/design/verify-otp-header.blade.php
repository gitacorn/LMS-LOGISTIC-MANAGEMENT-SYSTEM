
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- generic meta -->
    <meta name="description" content="{{ config('constants.SITE_DESCRIPTION') }}" />
    <meta name="keywords" content="{{ config('constants.SITE_KEYWORDS') }}" />
    <meta name="author" content="" />
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
    <title>@yield('pageTitle') | {{ config('', 'Logistic Management System')}}</title>
    <!-- css -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset ('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/select2.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset ('css/main.css') . '?ver=' .config('constants.CSS_JS_VERSION') }}">
    <link rel="stylesheet" href="{{ asset ('css/style.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset ('css/login.css') }}"> -->
    <!-- scripts -->
    <script src="{{ asset ('js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset ('js/bootstrap.bundle.min.js') }}"></script>
    <!-- plugins -->
    <script type="text/javascript" src="{{ asset ('js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/validator-additional-methods.js') }}"></script>
    <script src="{{ asset ('js/select2.js') }}"></script>
    <!-- main script -->
    <script>
        var site_url = "{{ url('/') }}" + '/';
    </script>
    <script src="{{ asset ('js/common.js') }}"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap');

        body {font-family: 'Jost', sans-serif;}

        .bg-grad-sharp {background: #fff;}
        .invalid-feedback {font-size: 90%;}
        .login-btn {width: 100%;display: block;padding: 6px 0;color: #fff;border-radius: 0.25rem;}

        /* .form-control {font-size: 14px;} */

        .login-page {position: relative;height: 100vh;/* display: flex;align-items: center; */background-repeat: no-repeat;z-index: 1;overflow: hidden;}
        .login-page:before {position: absolute;content: "";top: 0;left: 0;width: 35%;height: 100%;background-position: left;background-size: cover;}
        .login-page-image {position: relative;/* height: 100vh; */}
        .login-page:after {position: absolute;content: "";top: 0;left: 0;background: rgb(141 25 26 / 25%);width: 50%;height: 100%;z-index: -1;}
        .login-heading-content {position: relative;}
        .login-heading-content:before {content: "";width: 22%;height: 50%;position: fixed;top: 0;left: 50%;bottom: 0;-webkit-clip-path: polygon(0 0, 100% 0, 0% 100%, 0% 100%);clip-path: polygon(0 0, 50% 50%, 0% 100%, 0% 100%);background: #e4b6b8;opacity: 0.1;z-index: 1;}
        .login-heading-content:after {content: "";width: 20%;height: 70%;position: fixed;/* top: 0; */right: 0;bottom: 17%;-webkit-clip-path: polygon(0 0, 100% 50%, 100% 100%);clip-path: polygon(0 0, 100% 50%, 100% 100%);background: #d8001dd4;opacity: 0.1;z-index: 1;}
        .wrapper {padding-top: 0;width: 100%;}
        .login-title {font-size: 38px;font-weight: 700;padding: 8px 0;}
        .sub-login-title {font-size: 25px;font-weight: 400;padding-bottom: 20px;display: block;}
        .card-login {width: 450px;position: relative;}
        .form-group {position: relative;margin-bottom: 15px}

        /* .form-control {border: 1px solid #858585c2;font-size: 18px;color: #535353;} */

        .email-icon {font-size: 22px;position: absolute;top: 18px;right: 16px;color: #8b8a8a;}
        .login-btn {font-size: 18px;padding: 12px 0;}
        .login-logo {max-width: 255px;}

        .login-image {max-width: 700px;}
        .otp-icon {font-size: 65px;color: var(--primary-color);text-align: center;margin-bottom: 5px;}
        .otp-main-div{position: absolute; transform: translate(-50% , -50% ); top: 50%; left: 50%;z-index: 5}

        .login-items {
          width: 100%;}
        .card.card-otp {border-radius: 20px; background-color: #fff;}
        .box-wrapper-login {display: flex;align-items: center;height: 100%;}
        .back-button{font-size: 18px;}

        @media (min-width: 992px) {
            .bg-grad-sharp {background: linear-gradient(to right, #fff 71%, var(--primary-color) 71%)}
        }

        @media (max-width:1407px) {
            .login-image {max-width: 500px;}
            .card-login{width: 415px;}
        }

        @media (max-width: 992px) {
            .login-page:after {display: none;}
            .login-heading-content:before {display: none;}
        }

        @media (max-width: 767px) {
            .sub-login-title {font-size: 20px;}
            .login-logo {max-width: 200px;}
            .card-login {width: 100%;box-shadow: 0px 0px 10px 0px rgb(0 0 0 / 10%) !important;padding: 30px 20px;}
            .login-heading-content:after {display: none;}
            .otp-main-div{width: 70%;}

        }
        @media (max-width: 575px) {
          .otp-main-div{width: 92%;}
        }
    </style>

</head>

<body class="">
    <!-- navbar start -->
    <main class="page-height">
        <section>
            <div class="page-wrapper login-page" style="background-image:url('<?php echo  asset('images/shape1.png') ?>')">
                <div class="box-wrapper-login">
                    <div class="wrapper">
                        <div class="row">
                            <div class="col-lg-12 m-auto">
                                <div class="row align-items-center">
                                    <div class="col-12 main-heading d-flex h-100 login-heading-content">

                                        @yield('content')

                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- nav end  -->
</body>

</html>
