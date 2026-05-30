
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
 <div class="register-account">
        <div class="container-fluid">
            <div class="row register-slider-row">
                <div class="col-xl-6 register-column">
                    <div class="register-slider-area">
                        <div class="swiper register-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="register-slider-wrapper">
                                        <img src="assets/img/innerpages/register-img1.png" alt="">
                                        <div class="register-content">
                                            <h2>Connecting the World, One Shipment at a Time.</h2>
                                        </div>

                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="register-slider-wrapper">
                                        <img src="assets/img/innerpages/register-img2.png" alt="">
                                        <div class="register-content">
                                            <h2>Delivering Trust, One Package at a Time.</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="register-slider-wrapper">
                                        <img src="assets/img/innerpages/register-img3.png" alt="">
                                        <div class="register-content">
                                            <h2>Logistics Made Simple, Shipping Made Secure.</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-pagination7 paginations">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="register-area">
                        <div class="register-wrapper">
                            <div class="register-content text-center">
                                <h2>Register Account</h2>
                                <span>Already have an account? <a href="{{ route('login') }}">Login</a> now</span>
                            </div>
                            <div class="contact-form-wrapper">
                                <form action="{{ route('register') }}" method="POST">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="form-inner">
                                                <label>Full Name</label>
                                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Mr. Daniel Scoot">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-inner">
                                                <label>Email</label>
                                                <input type="email" name="email" value="{{ old('email') }}" placeholder="info@example.com">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-inner">
                                                <label>Password</label>
                                                <input type="password" name="password" placeholder="********">
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-inner">
                                                <label>Confirm Password</label>
                                                <input type="password" name="password_confirmation" placeholder="********">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-inner2 two">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="contactCheck22">
                                                    <label class="form-check-label" for="contactCheck22">
                                                        I consent to my data being processed according to the privacy policy
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="primary-btn2 btn-hover">
                                        Register Now
                                        <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z"></path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="register-bottom-wrap">
                            <p>Copyright 2025 <a href="https://www.egenslab.com/">Egens Lab</a> | All Right Reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
