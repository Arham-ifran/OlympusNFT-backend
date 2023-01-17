@extends("auth.layouts.layout")
@section('content')
<main id="main">
    <section class="account-page login">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="account-form">
                        <div class="login-logo">
                            <a href="{{url('/')}}" class="logo mr-auto fixed-logo"><img src="{{ _asset('frontend/assets/img/logo.svg') }}" alt="{{SITE_NAME}}" width="150px" class="img-fluid" /></a>

                            <h3 class="acc-title">{{ __('Verify Your Email Address') }}</h3>
                            @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                            @endif

                            {{ __('Before proceeding, please check your email for a verification link.') }}
                            {{ __('If you did not receive the email') }},


                            <form action="{{ route('verification.resend') }}" id="verify-form" name="verify-form" method="POST" class="form">
                                @csrf

                                <div class="form-group">
                                    <button class="btn btn-success btn-round btn-block" type="submit">{{ __('click here to request another') }}</button>
                                </div>
                            </form>
                            <a href="{{url('register')}}" class="bottom-link">Register an Account </a>
                            <div class="for-pass">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection