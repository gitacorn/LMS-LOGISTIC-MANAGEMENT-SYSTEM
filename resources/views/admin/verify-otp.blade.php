
@extends('includes/login-header')

@section('pageTitle', $pageTitle )

@section('content')

<style>

        .otp-main-div .form-control {font-size: 14px; height: auto;}

        .login-heading-content {position: relative;}
        .login-heading-content:before {content: "";width: 22%;height: 50%;position: fixed;top: 0;left: 50%;bottom: 0;-webkit-clip-path: polygon(0 0, 100% 0, 0% 100%, 0% 100%);clip-path: polygon(0 0, 50% 50%, 0% 100%, 0% 100%);background: #e4b6b8;opacity: 0.1;z-index: 1;}
        .login-heading-content:after {content: "";width: 20%;height: 70%;position: fixed;/* top: 0; */right: 0;bottom: 17%;-webkit-clip-path: polygon(0 0, 100% 50%, 100% 100%);clip-path: polygon(0 0, 100% 50%, 100% 100%);background: #d8001dd4;opacity: 0.1;z-index: 1;}
     
        .form-group {position: relative;margin-bottom: 15px}

        .otp-main-div .form-control {border: 1px solid #858585c2;color: #535353;}

        .otp-icon {font-size: 65px;color: var(--primary-color);text-align: center;margin-bottom: 5px;}
        .otp-main-div{position: absolute; transform: translate(-50% , -50% ); top: 50%; left: 50%;z-index: 5}

        .login-items {
          width: 100%;}
        .card.card-otp {border-radius: 20px; background-color: #fff;}
        .box-wrapper-login {display: flex;align-items: center;height: 100%;}
        .back-button{font-size: 18px;}
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }

        @media (max-width: 767px) {
            .sub-login-title {font-size: 20px;}
            .login-logo {max-width: 200px;}
            .card-login {width: 100%;box-shadow: 0px 0px 10px 0px rgb(0 0 0 / 10%) !important;padding: 30px 20px;}
           .otp-main-div .login-heading-content:after {display: none;}
            .otp-main-div{width: 70%;}

        }
        @media (max-width: 575px) {
          .otp-main-div{width: 92%;}
        }
    </style>

<div class="row align-items-center h-100vh mx-auto otp-main-div">
  <div class="col-lg-10 col-sm-11 col-12 d-flex align-items-center justify-content-center border-outer mx-auto">
    <div class="login-items">
      {!! Form::open(array( 'id '=> 'verify-otp-form' , 'method' => 'post' ,  'url' => 'login/checkOtp')) !!}
      	
        <div class="card card-otp shadow-lg bg-transparents border-0">
        
          <div class="card-body px-lg-5 pt-lg-5">
            <div class="otp-icon text-center">
              <i class="fa fa-lock" aria-hidden="true"></i>
            </div>
            <div class="text-center mb-lg-3">
              <h3 class="font-weight-bold login-account">
                <span>{{ trans("messages.enter-otp") }}</span>
              </h3>
              <label>{{ trans('messages.security-purpose') }}</label>
            </div>
            <hr>
            <div class="form-group mt-4">
              <label for="inputUsername">{{ trans("messages.enter-otp") }}<span class="text-danger">*</span></label>
              <input class="form-control" name="login_otp" type="text" maxlength="6" minlength="6" onkeyup="onlyNumberWithSpaceAndPlusSign(this)" placeholder="{{ trans('messages.enter-otp') }}" autofocus />
              @if($errors->has('login_otp'))
    			<label id="login_otp-error" class="invalid-input" for="login_otp">{{ $errors->first('login_otp') }}</label>
			  @endif
             
            </div>
            
            <input type="hidden" name="user_id" value="<?php echo (isset($userId) ? Wild_tiger::encode($userId) : 0 ); ?>">
            <button class="btn bg-theme login-button btn-block text-white" type="submit" title="verify-otp">{{ trans("messages.verify-otp") }}</button>
          </div>
          <a href="{{ config('constants.LOGIN_URL') }}" class="text-center back-to-login py-4 back-button"><i class="fa fa-arrow-left mr-2"></i>{{ trans("messages.back-to-login") }}</a>
        </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>


<script>
  $("#verify-otp-form").validate({
    errorClass: "invalid-input",
    rules: {
      login_otp: {
        required: true,
        noSpace: true
      },
    },
    messages: {
      login_otp: {
        required: "{{ trans('messages.required-otp') }}"
      },
    },
    submitHandler: function(form) {
      showLoader()
      form.submit();
    }
  });
	
</script>
@endsection