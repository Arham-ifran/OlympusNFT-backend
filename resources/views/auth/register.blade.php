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
                            <form action="{{ route('register') }}" id="register-form" name="register-form" method="POST" class="form">
                                @csrf
                                <h3 class="acc-title">Register to Nesux Art</h3>
                                <div class="form-group">
                                    <input class="form-control @error('firstname') is-invalid @enderror" type="text" id="firstname" name="firstname" placeholder="Please Enter your First Name" required />
                                    <span class="form-icon"><i class="bx bx-mail-send"></i></span>
                                    @error('firstname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input class="form-control @error('lastname') is-invalid @enderror" type="text" id="lastname" name="lastname" placeholder="Please Enter your Last Name" required />
                                    <span class="form-icon"><i class="bx bx-mail-send"></i></span>
                                    @error('lastname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
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
                                    <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" placeholder="Please Enter your Passowrd" required />
                                    <span class="form-icon"><i class="bx bx-lock"></i></span>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>


                                <small> By registering you agree to Nesux Art's <a href="{{url('terms-of-use')}}">Terms of Using the NFT Platform</a> and <a href="{{url('privacy-policy')}}">Privacy Policy.</a>

                                    <div class="form-group">
                                        <button class="btn btn-success btn-round btn-block" type="submit">Signup</button>
                                    </div>
                                    <a href="{{url('login')}}" class="bottom-link">Already have an Account?</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection