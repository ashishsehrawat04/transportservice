<!doctype html>
<html lang="en">

<head>
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
</head>

<body class="tt-magic-cursor transport-home">

    <div id="magic-cursor">
        <div id="ball"></div>
    </div>

    <!-- Back To Top -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
        <svg class="arrow" width="22" height="25" viewBox="0 0 24 23" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M0.556131 11.4439L11.8139 0.186067L13.9214 2.29352L13.9422 20.6852L9.70638 20.7061L9.76793 8.22168L3.6064 14.4941L0.556131 11.4439Z" />
            <path d="M23.1276 11.4999L16.0288 4.40105L15.9991 10.4203L20.1031 14.5243L23.1276 11.4999Z" />
        </svg>
    </div>

    <!-- header Section Start-->
    <header class="header-area style-1">
        <div class="container-fluid d-flex flex-nowrap align-items-center justify-content-between">
            <div class="logo-and-menu-area">
                <a href="index.html" class="header-logo">
                    <img src="{{ asset('assets/img/header-logo.svg') }}" alt="">
                </a>
                <div class="main-menu">
                    <div class="mobile-logo-area d-xl-none d-flex align-items-center justify-content-between">
                        <a href="index.html" class="mobile-logo-wrap">
                            <img src="{{ asset('assets/img/home1/header-logo.svg') }}" alt="">
                        </a>
                        <div class="menu-close-btn">
                            <i class="bi bi-x"></i>
                        </div>
                    </div>
                    <ul class="menu-list">
                        <li class="menu-item-has-children active">
                            <a href="index.html" class="drop-down">
                                Home
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <i class="bi bi-plus dropdown-icon"></i>
                            <ul class="sub-menu">
                                <li class="active"><a href="index.html">Main Home</a></li>
                                <li><a href="maritime-transport.html">Maritime Transport</a></li>
                                <li><a href="international-logistics.html">International Logistics</a></li>
                                <li><a href="courier-service.html">Courier Service</a></li>
                                <li><a href="freight.html">Freight</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="industries-details.html" class="drop-down">
                                Industries
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <i class="bi bi-plus dropdown-icon"></i>
                            <ul class="sub-menu">
                                <li><a href="industries-details.html">Renewable Energy</a></li>
                                <li><a href="industries-details.html">Retail & E-commerce</a></li>
                                <li><a href="industries-details.html">Energy and Oil & Gas</a></li>
                                <li><a href="industries-details.html">Healthcare & Pharmaceuticals</a></li>
                                <li><a href="industries-details.html">Fashion and Textiles</a></li>
                                <li><a href="industries-details.html">Aerospace and Defense</a></li>
                                <li><a href="industries-details.html">Forestry and Paper</a></li>
                                <li><a href="industries-details.html">Sports and Entertainment</a></li>
                                <li><a href="industries-details.html">Agriculture</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#" class="drop-down">
                                Company
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <i class="bi bi-plus dropdown-icon"></i>
                            <ul class="sub-menu">
                                <li><a href="about.html">About Company</a></li>
                                <li><a href="history.html"> Our History</a></li>
                                <li class="menu-item-has-children">
                                    <a href="#" class="drop-down">
                                        Solution
                                        <i class="bi-caret-right-fill dropdown-icon"></i>
                                    </a>
                                    <i class="bi bi-plus dropdown-icon"></i>
                                    <ul class="sub-menu">
                                        <li><a href="solution-01.html">Solution Style 1</a></li>
                                        <li><a href="solution-02.html">Solution Style 2</a></li>
                                        <li><a href="solution-details.html">Solution Details</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#" class="drop-down">
                                        Industries
                                        <i class="bi-caret-right-fill dropdown-icon"></i>
                                    </a>
                                    <i class="bi bi-plus dropdown-icon"></i>
                                    <ul class="sub-menu">
                                        <li><a href="industries-01.html">Industries Style 1</a></li>
                                        <li><a href="industries-02.html">Industries Style 2</a></li>
                                        <li><a href="industries-details.html">Industries Details</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#" class="drop-down">
                                        Career
                                        <i class="bi-caret-right-fill dropdown-icon"></i>
                                    </a>
                                    <i class="bi bi-plus dropdown-icon"></i>
                                    <ul class="sub-menu">
                                        <li><a href="career.html">Career</a></li>
                                        <li><a href="career-details.html">Career Details</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#" class="drop-down">
                                        Our Team
                                        <i class="bi-caret-right-fill dropdown-icon"></i>
                                    </a>
                                    <i class="bi bi-plus dropdown-icon"></i>
                                    <ul class="sub-menu">
                                        <li><a href="team.html">Team</a></li>
                                        <li><a href="team-details.html">Team Details</a></li>
                                    </ul>
                                </li>
                                <li><a href="faq.html">FAQ's</a></li>
                                <li><a href="{{ route('shipment.track') }}">Track & Trace</a></li>
                                <li><a href="certification.html">Certification</a></li>
                                <li><a href="terms-and-conditions.html">Terms & Conditions</a></li>
                                <li><a href="pricing-plan.html">Pricing Plan</a></li>
                                <li><a href="global-network.html">Global Network</a></li>
                                <li><a href="get-in-touch.html">Get In Touch</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="{{ route('shipment.cart') }}" class="drop-down">
                                Shipment
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <i class="bi bi-plus dropdown-icon"></i>
                            <ul class="sub-menu">
                                <li><a href="{{ route('shipment.add_item') }}">Add Item</a></li>
                                <li><a href="{{ route('shipment.cart') }}">My Cart</a></li>
                                @auth
                                    <li><a href="{{ route('shipment.leads') }}">My Leads</a></li>
                                @endauth
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#" class="drop-down">
                                Media
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <i class="bi bi-plus dropdown-icon"></i>
                            <ul class="sub-menu">
                                <li><a href="news-and-insight.html">News & Insight</a></li>
                                <li><a href="news-standard.html">Insight Standard</a></li>
                                <li><a href="news-details.html">Insight Details</a></li>
                            </ul>
                        </li>
                    </ul>
                    <a class="primary-btn1 btn-hover d-xl-none" href="{{ route('shipment.track') }}">
                        Track & Trace
                        <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path
                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                </path>
                            </g>
                        </svg>
                        <span></span>
                    </a>
                </div>
            </div>
            <div class="nav-right">
                <div class="contact-area">
                    <div class="search-and-login">
                        <div class="search-bar">
                            <div class="search-btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path
                                            d="M15.7417 14.6098L13.486 12.3621C14.7088 10.8514 15.3054 8.9291 15.1526 6.99153C14.9998 5.05396 14.1093 3.24888 12.6648 1.94851C11.2203 0.648146 9.33193 -0.0483622 7.38901 0.00261294C5.44609 0.0535881 3.59681 0.84816 2.22248 2.22248C0.84816 3.59681 0.0535881 5.44609 0.00261294 7.38901C-0.0483622 9.33193 0.648146 11.2203 1.94851 12.6648C3.24888 14.1093 5.05396 14.9998 6.99153 15.1526C8.9291 15.3054 10.8514 14.7088 12.3621 13.486L14.6098 15.7417C14.6839 15.8164 14.7721 15.8757 14.8692 15.9161C14.9664 15.9566 15.0705 15.9774 15.1758 15.9774C15.281 15.9774 15.3852 15.9566 15.4823 15.9161C15.5794 15.8757 15.6676 15.8164 15.7417 15.7417C15.8164 15.6676 15.8757 15.5794 15.9161 15.4823C15.9566 15.3852 15.9774 15.281 15.9774 15.1758C15.9774 15.0705 15.9566 14.9664 15.9161 14.8692C15.8757 14.7721 15.8164 14.6839 15.7417 14.6098ZM1.62572 7.60368C1.62572 6.42135 1.97632 5.26557 2.63319 4.2825C3.29005 3.29943 4.22368 2.53322 5.31601 2.08076C6.40834 1.62831 7.61031 1.50992 8.76992 1.74058C9.92953 1.97124 10.9947 2.54059 11.8307 3.37662C12.6668 4.21266 13.2361 5.27783 13.4668 6.43744C13.6974 7.59705 13.579 8.79902 13.1266 9.89134C12.6741 10.9837 11.9079 11.9173 10.9249 12.5742C9.94178 13.231 8.78601 13.5816 7.60368 13.5816C6.01822 13.5816 4.49771 12.9518 3.37662 11.8307C2.25554 10.7096 1.62572 9.18913 1.62572 7.60368Z" />
                                    </g>
                                </svg>
                            </div>
                            <div class="search-input">
                                <div class="search-close"></div>
                                <form>
                                    <div class="search-group">
                                        <div class="form-inner2">
                                            <input type="text" placeholder="Enter your keywords">
                                            <button type="submit"><i class="bi bi-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="quick-search">
                                        <ul>
                                            <li>Quick Search :</li>
                                            <li><a href="solution-details.html">Freight Transportation</a></li>
                                            <li><a href="solution-details.html">Logistics & Distribution</a></li>
                                            <li><a href="solution-details.html">Ground Transportation</a></li>
                                            <li><a href="solution-details.html">Reverse Logistics</a></li>
                                            <li><a href="solution-details.html">Renewable Energy</a></li>
                                            <li><a href="solution-details.html">Retail & E-commerce</a></li>
                                            <li><a href="solution-details.html">Fashion and Textiles</a></li>
                                        </ul>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @guest

                            <a href="{{ route('login') }}" class="login-btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M8.00093 8.3084C10.2867 8.3084 12.1551 6.43993 12.1551 4.1542C12.1551 1.86847 10.2867 0 8.00093 0C5.7152 0 3.84676 1.86847 3.84676 4.1542C3.84676 6.43993 5.71523 8.3084 8.00093 8.3084ZM15.1302 11.6281C15.0213 11.356 14.8762 11.102 14.713 10.8662C13.8785 9.63264 12.5905 8.81632 11.1393 8.61677C10.9579 8.59865 10.7584 8.6349 10.6132 8.74375C9.85131 9.30611 8.94429 9.59635 8.00096 9.59635C7.05763 9.59635 6.15061 9.30611 5.3887 8.74375C5.24356 8.6349 5.04402 8.58049 4.86263 8.61677C3.41138 8.81632 2.10527 9.63264 1.28895 10.8662C1.12568 11.102 0.980543 11.3742 0.871724 11.6281C0.817314 11.737 0.835439 11.864 0.889849 11.9728C1.03499 12.2268 1.21638 12.4808 1.37964 12.6984C1.6336 13.0431 1.90572 13.3515 2.21412 13.6417C2.46808 13.8957 2.75832 14.1315 3.0486 14.3674C4.48169 15.4377 6.20506 16 7.98284 16C9.76061 16 11.484 15.4376 12.9171 14.3674C13.2073 14.1497 13.4976 13.8957 13.7516 13.6417C14.0418 13.3515 14.332 13.0431 14.586 12.6984C14.7674 12.4626 14.9307 12.2268 15.0758 11.9728C15.1665 11.864 15.1846 11.7369 15.1302 11.6281Z"/>
                                    </g>
                                </svg>
                            </a>

                            @endguest


                            @auth

                            <a href="#" class="login-btn">
                                {{ Auth::user()->name }}
                            </a>

                            @endauth
                        <!-- <a href="{{ route('login') }}" class="login-btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path
                                        d="M8.00093 8.3084C10.2867 8.3084 12.1551 6.43993 12.1551 4.1542C12.1551 1.86847 10.2867 0 8.00093 0C5.7152 0 3.84676 1.86847 3.84676 4.1542C3.84676 6.43993 5.71523 8.3084 8.00093 8.3084ZM15.1302 11.6281C15.0213 11.356 14.8762 11.102 14.713 10.8662C13.8785 9.63264 12.5905 8.81632 11.1393 8.61677C10.9579 8.59865 10.7584 8.6349 10.6132 8.74375C9.85131 9.30611 8.94429 9.59635 8.00096 9.59635C7.05763 9.59635 6.15061 9.30611 5.3887 8.74375C5.24356 8.6349 5.04402 8.58049 4.86263 8.61677C3.41138 8.81632 2.10527 9.63264 1.28895 10.8662C1.12568 11.102 0.980543 11.3742 0.871724 11.6281C0.817314 11.737 0.835439 11.864 0.889849 11.9728C1.03499 12.2268 1.21638 12.4808 1.37964 12.6984C1.6336 13.0431 1.90572 13.3515 2.21412 13.6417C2.46808 13.8957 2.75832 14.1315 3.0486 14.3674C4.48169 15.4377 6.20506 16 7.98284 16C9.76061 16 11.484 15.4376 12.9171 14.3674C13.2073 14.1497 13.4976 13.8957 13.7516 13.6417C14.0418 13.3515 14.332 13.0431 14.586 12.6984C14.7674 12.4626 14.9307 12.2268 15.0758 11.9728C15.1665 11.864 15.1846 11.7369 15.1302 11.6281Z" />
                                </g>
                            </svg>
                        </a> -->
                    </div>
                    <a class="primary-btn1 white-bg btn-hover d-xl-flex d-none" href="{{ route('shipment.track') }}">
                        Track & Trace
                        <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path
                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                            </g>
                        </svg>
                        <span></span>
                    </a>
                </div>
                <div class="sidebar-button mobile-menu-btn">
                    <svg width="20" height="18" viewBox="0 0 20 18" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M1.29445 2.8421H10.5237C11.2389 2.8421 11.8182 2.2062 11.8182 1.42105C11.8182 0.635903 11.2389 0 10.5237 0H1.29445C0.579249 0 0 0.635903 0 1.42105C0 2.2062 0.579249 2.8421 1.29445 2.8421Z">
                        </path>
                        <path
                            d="M1.23002 10.421H18.77C19.4496 10.421 20 9.78506 20 8.99991C20 8.21476 19.4496 7.57886 18.77 7.57886H1.23002C0.550421 7.57886 0 8.21476 0 8.99991C0 9.78506 0.550421 10.421 1.23002 10.421Z">
                        </path>
                        <path
                            d="M18.8052 15.1579H10.2858C9.62563 15.1579 9.09094 15.7938 9.09094 16.5789C9.09094 17.3641 9.62563 18 10.2858 18H18.8052C19.4653 18 20 17.3641 20 16.5789C20 15.7938 19.4653 15.1579 18.8052 15.1579Z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </header>
    <!-- header Section End-->

    <!-- home1 Banner Section Start-->
    <div class="home1-banner-section">
        <div class="swiper home1-banner-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="banner-img-area">
                            <img src="{{ asset('assets/img/home1/banner-img1.jpg') }}" alt="">
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>EST. 1996 - Land Transport</span>
                                    <h1>Freight. Transit. Carriers</h1>
                                    <a class="primary-btn1 btn-hover" href="get-in-touch.html">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="banner-img-area">
                            <img src="{{ asset('assets/img/home1/banner-img2.jpg') }}" alt="Banner-img2">
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>EST. 1996 - Land Transport</span>
                                    <h1>Deliver. Connect. Grow.</h1>
                                    <a class="primary-btn1 btn-hover" href="get-in-touch.html">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="banner-video-area">
                            <video autoplay loop muted playsinline src="{{ asset('assets/video/home1-banner-video.mp4') }}"></video>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>EST. 1996 - Land Transport</span>
                                    <h1>Fast. Reliable. Global.</h1>
                                    <a class="primary-btn1 btn-hover" href="get-in-touch.html">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="banner-img-area">
                            <img src="{{ asset('assets/img/home1/banner-img3.jpg') }}" alt="">
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>EST. 1996 - Land Transport</span>
                                    <h1>Speed. Trust. Excellence</h1>
                                    <a class="primary-btn1 btn-hover" href="get-in-touch.html">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="banner-img-area">
                            <img src="{{ asset('assets/img/home1/banner-img4.jpg') }}" alt="">
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>EST. 1996 - Land Transport</span>
                                    <h1>Your Cargo. Our Priority.</h1>
                                    <a class="primary-btn1 btn-hover" href="get-in-touch.html">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-btn-grp-area">
            <div class="slider-btn banner-slider-prev">
                <img src="{{ asset('assets/img/home1/pagination-img.png') }}" alt="">
                <div class="arrow">
                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M15 20C15 15.2941 6.37992 11.5686 4 10C6.81263 8.43137 15 4.70588 15 0"
                                stroke-width="2" />
                        </g>
                    </svg>
                </div>
            </div>
            <div class="slider-btn banner-slider-next">
                <img src="{{ asset('assets/img/home1/pagination-img.png') }}" alt="">
                <div class="arrow">
                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M5 20C5 15.2941 13.6201 11.5686 16 10C13.1874 8.43137 5 4.70588 5 0"
                                stroke-width="2" />
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @section('content')
    @show
