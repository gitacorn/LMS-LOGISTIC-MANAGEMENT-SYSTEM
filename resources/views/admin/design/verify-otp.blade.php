@extends('admin/design/verify-otp-header')

@section('pageTitle', $pageTitle )

@section('content')

<div class="row align-items-center h-100vh mx-auto otp-main-div">
  <div class="col-lg-10 col-sm-11 col-12 d-flex align-items-center justify-content-center border-outer mx-auto">
    <div class="login-items">
      <form method="post" id="verify-otp-form">
        <div class="card card-otp shadow-lg bg-transparents border-0">
          <div class="card-body px-lg-5 pt-lg-5">
            <div class="otp-icon text-center">
              <i class="fa fa-lock" aria-hidden="true"></i>
            </div>
            <div class="text-center mb-lg-3">
              <h3 class="font-weight-bold login-account">
                <span>{{ trans("messages.enter-otp") }}</span>
              </h3>
              <label>{{ trans("messages.security-purpose") }}</label>
            </div>
            <hr>
            <div class="form-group mt-4">
              <label for="inputUsername">{{ trans("messages.enter-otp") }}<span class="text-danger">*</span></label>
              <input class="form-control" name="login_otp" type="text" placeholder="{{ trans('messages.enter-otp') }}" autofocus />
            </div>
            <input type="hidden" name="user_id" value="<?php echo (isset($userId) ? $userId : ''); ?>">
            <button class="btn bg-theme login-button btn-block text-white" type="submit" title="verify-otp">{{ trans("messages.verify-otp") }}</button>
          </div>
          <a href="{{ url('login') }}" class="text-center back-to-login py-4 back-button"><i class="fa fa-arrow-left mr-2"></i>{{ trans("messages.back-to-login") }}</a>
        </div>
      </form>
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