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
                            <form action="{{ route('password.update') }}" id="reset-password-form" name="reset-password-form" method="POST" class="form">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
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
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Please Enter your Password">
                                    <span class="form-icon"><i class="bx bx-lock"></i></span>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">

                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                                    <span class="form-icon"><i class="bx bx-lock"></i></span>
                                    @error('new-password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-success btn-round btn-block" type="submit">{{ __('Reset Password') }}</button>
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