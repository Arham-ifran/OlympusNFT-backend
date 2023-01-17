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
                            <form action="{{ route('password.email') }}" id="email-form" name="email-form" method="POST" class="form">
                                @csrf
                                <h3 class="acc-title">{{ __('Reset Password') }}</h3>

                                <div class="form-group">
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" placeholder="Please Enter your Email" required />
                                    <span class="form-icon"><i class="bx bx-mail-send"></i></span>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-success btn-round btn-block" type="submit">{{ __('Send Password Reset Link') }}</button>
                                </div>
                                <a href="{{url('register')}}" class="bottom-link">Register an Account </a>
                                <div class="for-pass">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection