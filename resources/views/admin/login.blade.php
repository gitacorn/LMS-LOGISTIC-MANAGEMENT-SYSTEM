@extends('includes/login-header')

@section('pageTitle', $pageTitle )

@section('content')

<div class="row align-items-center">
	<div class="col-lg-6 d-lg-block d-none col-12 set-motadata-img  align-self-center mx-auto login-page-image px-0 login-page-video">
		<div class="left-login-col">
    	<div class="left-login-video-div">
				<video width="100%" height="auto" autoplay muted loop>
					<source src="{{ asset ('public/images/login-video.mp4') }}" type="video/mp4"/>
				</video>
			</div>
		</div>
   	</div>
    <div class="col-lg-6 col-12 motadata-color main-heading d-flex h-100 ">
		<div class="card card-login mx-auto my-5 border-0 bg-transparents shadow-none">
		    <img src="{{ asset ('images/logo.png') }}" class="img-fluid mx-auto d-block login-logo">
		    <div class="card-body pt-5 card-login-form">
		        <h3 class=" motadata-heading login-title text-center">{{ trans("messages.login-title") }}</h3>
		        <span class="sub-login-title text-center">LOGISTIC MANAGEMENT SYSTEM</span>
		        {{ Wild_tiger::readMessage() }}
		        <div class="body-form-info">
		            {!! Form::open(array( 'id '=> 'login-form' , 'method' => 'post' , 'url' => 'login/checkLogin')) !!}
		            <div class="form-group {{ (!empty(old('login_email')) ? 'focused' : '' ) }}">
		                <input id="email" class="form-input form-control " autocomplete="off" type="email" placeholder="{{ trans('messages.email-address') }}" name="login_email" value="{{ old('login_email') }}">
		                <span class="fa fa-envelope email-icon"></span>
		            </div>
		            <div class="form-group mb-4">
		                <input id="password" class="form-input form-control" autocomplete="new-password" placeholder="{{ trans('messages.password') }}" type="password" name="login_password" value="">
		                <span class="fa fa-lock email-icon"></span>
		            </div>
		
		            <div class="form-group">
		                <button type="submit" class="btn-submit border login-btn bg-theme">{{ trans("messages.login") }}</button>
		            </div>
		            {!! Form::close() !!}
		        </div>
		    </div>
		</div>
	</div>
</div>


<script>
    $("#login-form").validate({
        errorClass: "invalid-input",
        rules: {
            login_email: {
                required: true,
                noSpace: true,
                email_regex: false
            },
            login_password: {
                required: true,
                noSpace: true
            },
        },
        messages: {
            login_email: {
                required: '{{ trans("messages.required-login-username") }}'
            },
            login_password: {
                required: '{{ trans("messages.required-login-password") }}'
            },
        },
        submitHandler: function(form) {
            showLoader();
            form.submit();
        },
    });
</script>
@endsection