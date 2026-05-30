

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/jquery-ui.css') }}" rel="stylesheet">
    <!-- Bootstrap Icon CSS -->
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">
    <!-- CSS -->
    <link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet">
    <!-- FancyBox CSS -->
    <link href="{{ asset('assets/css/jquery.fancybox.min.css') }}" rel="stylesheet">
    <!-- Swiper slider CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <!-- Slick slider CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <!-- BoxIcon  CSS -->
    <link href="{{ asset('assets/css/boxicons.min.css') }}" rel="stylesheet">
    <!--  Style CSS  -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- Title -->
    <title>OneTrack - Logistics & Transportation</title>
    <link rel="icon" href="{{ asset('assets/fav-icon.svg') }}" type="image/gif" sizes="20x20">


<div class="login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="login-wrapper">
                    <div class="form-title text-center">
                        <h2>Login OneTrack</h2>
                        <span>Don’t have an account! <a href="{{ route('register') }}">Sign Up </a> now</span>
                    </div>
                    <div class="login-registration-form">
                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="form-inner mb-20">
                                <label>Email*</label>
                                <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="info@example.com">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-inner mb-20">
                                <label>Password*</label>
                                <input id="password" name="password" type="password" placeholder="*** *** **">
                                <i class="bi bi-eye-slash bi-eye" id="togglePassword"></i>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-remember-forget">
                                <a href="#">Forget Password</a>
                            </div>
                            <button type="submit" class="primary-btn2 btn-hover">
                                Login Now
                                <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path
                                            d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                    </g>
                                </svg>
                                <span></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="login-bottom">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-12">
                    <div class="login-bottom-wrap">
                        <p>Copyright 2025 <a href="https://www.egenslab.com/">Egens Lab</a> | All Right Reserved.
                        </p>
                        <p>Read Our Business Policy, <a href="terms-and-conditions.html">Terms & Condition</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
