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
    <link rel="stylesheet" href="{{ asset('assets/css/production-theme.css') }}">
    <!-- Title -->
    <title>OneTrack - Smart Transport & Shipment Tracking</title>
    <link rel="icon" href="{{ asset('assets/fav-icon.svg') }}" type="image/gif" sizes="20x20">
    <style>
        .home1-banner-section .banner-wrapper,
        .home1-banner-section .banner-img-area,
        .home1-banner-section .banner-video-area,
        .home1-banner-section .shipment-gif-bg {
            min-height: 640px;
        }

        .home1-banner-section .banner-img-area img,
        .home1-banner-section .banner-video-area video {
            width: 100%;
            height: 100%;
            min-height: 640px;
            object-fit: cover;
            object-position: center;
        }

        .shipment-gif-bg {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 78% 18%, rgba(255, 122, 69, .34), transparent 14%),
                linear-gradient(135deg, #10212b 0%, #1e4f5f 48%, #183024 100%);
        }

        .shipment-gif-bg.alt {
            background:
                radial-gradient(circle at 22% 20%, rgba(57, 162, 255, .32), transparent 16%),
                linear-gradient(135deg, #13203b 0%, #285066 50%, #123327 100%);
        }

        .shipment-gif-bg.green {
            background:
                radial-gradient(circle at 80% 16%, rgba(14, 143, 122, .38), transparent 14%),
                linear-gradient(135deg, #101820 0%, #264c45 52%, #14291f 100%);
        }

        .shipment-gif-bg .gif-sun {
            position: absolute;
            top: 88px;
            right: 13%;
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: #ff7a45;
            box-shadow: 0 0 60px rgba(255, 122, 69, .45);
            animation: sunPulse 2.4s ease-in-out infinite;
        }

        .shipment-gif-bg .gif-cloud {
            position: absolute;
            top: 130px;
            left: -160px;
            width: 124px;
            height: 38px;
            border-radius: 999px;
            background: rgba(255,255,255,.34);
            animation: cloudMove 12s linear infinite;
        }

        .shipment-gif-bg .gif-cloud:before,
        .shipment-gif-bg .gif-cloud:after {
            content: "";
            position: absolute;
            bottom: 12px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255,255,255,.34);
        }

        .shipment-gif-bg .gif-cloud:before { left: 20px; }
        .shipment-gif-bg .gif-cloud:after { right: 24px; }

        .shipment-gif-bg .gif-cloud.cloud-two {
            top: 210px;
            animation-duration: 16s;
            animation-delay: -5s;
            opacity: .72;
        }

        .shipment-gif-bg .gif-city {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 138px;
            height: 115px;
            background:
                linear-gradient(#243941 0 0) 6% 44px/50px 71px no-repeat,
                linear-gradient(#2b454e 0 0) 14% 16px/66px 99px no-repeat,
                linear-gradient(#20333a 0 0) 25% 62px/90px 53px no-repeat,
                linear-gradient(#2b454e 0 0) 47% 28px/58px 87px no-repeat,
                linear-gradient(#20333a 0 0) 61% 52px/120px 63px no-repeat,
                linear-gradient(#2b454e 0 0) 82% 22px/78px 93px no-repeat;
            opacity: .8;
        }

        .shipment-gif-bg .gif-road {
            position: absolute;
            left: -4%;
            right: -4%;
            bottom: 0;
            height: 174px;
            background: linear-gradient(180deg, #343f45 0%, #171d20 100%);
            transform: skewY(-2deg);
            transform-origin: left bottom;
            box-shadow: inset 0 8px 0 rgba(255,255,255,.06);
        }

        .shipment-gif-bg .gif-road:before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 72px;
            height: 7px;
            background: repeating-linear-gradient(90deg, #f7c948 0 66px, transparent 66px 112px);
            animation: laneMove .65s linear infinite;
        }

        .shipment-gif-bg .gif-truck {
            position: absolute;
            left: 50%;
            bottom: 110px;
            width: 268px;
            height: 96px;
            transform: translateX(-50%);
            animation: truckFloat .75s ease-in-out infinite;
            z-index: 2;
        }

        .gif-truck .box {
            position: absolute;
            left: 0;
            top: 18px;
            width: 162px;
            height: 66px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0e8f7a, #0a6c5c);
            box-shadow: 0 18px 36px rgba(0,0,0,.28);
        }

        .gif-truck .cab {
            position: absolute;
            right: 12px;
            top: 34px;
            width: 92px;
            height: 50px;
            border-radius: 10px 28px 8px 6px;
            background: linear-gradient(135deg, #ff7a45, #e85c2b);
            box-shadow: 0 18px 36px rgba(0,0,0,.28);
        }

        .gif-truck .cab:before {
            content: "";
            position: absolute;
            right: 21px;
            top: 9px;
            width: 32px;
            height: 19px;
            border-radius: 5px;
            background: #c9f3ff;
        }

        .gif-truck .light {
            position: absolute;
            right: 6px;
            bottom: 15px;
            width: 13px;
            height: 9px;
            border-radius: 4px;
            background: #fff7a8;
            box-shadow: 15px 0 28px rgba(255,247,168,.7);
        }

        .gif-truck .wheel {
            position: absolute;
            bottom: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #080c0f;
            border: 7px solid #aab5bb;
            animation: wheelSpin .5s linear infinite;
        }

        .gif-truck .wheel.left { left: 40px; }
        .gif-truck .wheel.right { right: 42px; }

        .shipment-gif-bg .gif-car {
            position: absolute;
            left: -120px;
            bottom: 72px;
            width: 96px;
            height: 38px;
            border-radius: 22px 28px 10px 10px;
            background: #39a2ff;
            z-index: 1;
            animation: carPass 3s linear infinite;
        }

        .shipment-gif-bg .gif-car:before {
            content: "";
            position: absolute;
            left: 28px;
            top: -13px;
            width: 43px;
            height: 20px;
            border-radius: 20px 20px 0 0;
            background: #c9f3ff;
        }

        .shipment-gif-bg .gif-car:after {
            content: "";
            position: absolute;
            left: 13px;
            right: 13px;
            bottom: -7px;
            height: 13px;
            background:
                radial-gradient(circle, #0c1114 0 6px, transparent 7px) left center/24px 13px no-repeat,
                radial-gradient(circle, #0c1114 0 6px, transparent 7px) right center/24px 13px no-repeat;
        }

        .shipment-gif-bg.air-scene {
            background:
                radial-gradient(circle at 18% 18%, rgba(255, 122, 69, .3), transparent 13%),
                linear-gradient(135deg, #12345f 0%, #21718b 48%, #163d55 100%);
        }

        .shipment-gif-bg.air-scene .gif-road,
        .shipment-gif-bg.air-scene .gif-car,
        .shipment-gif-bg.air-scene .gif-truck {
            display: none;
        }

        .gif-plane {
            position: absolute;
            left: -180px;
            top: 210px;
            width: 150px;
            height: 42px;
            border-radius: 50px 16px 16px 50px;
            background: linear-gradient(135deg, #fff, #c9f3ff);
            box-shadow: 0 18px 42px rgba(0,0,0,.22);
            animation: planeFly 5.6s linear infinite;
            z-index: 3;
        }

        .gif-plane:before {
            content: "";
            position: absolute;
            left: 44px;
            top: -30px;
            width: 62px;
            height: 34px;
            clip-path: polygon(0 100%, 100% 100%, 32% 0);
            background: #ff7a45;
        }

        .gif-plane:after {
            content: "";
            position: absolute;
            left: 58px;
            bottom: -24px;
            width: 56px;
            height: 28px;
            clip-path: polygon(0 0, 100% 0, 36% 100%);
            background: #0e8f7a;
        }

        .shipment-gif-bg.bike-scene {
            background:
                radial-gradient(circle at 82% 18%, rgba(255, 122, 69, .28), transparent 13%),
                linear-gradient(135deg, #1a2732 0%, #2d5d62 48%, #183924 100%);
        }

        .shipment-gif-bg.bike-scene .gif-truck,
        .shipment-gif-bg.bike-scene .gif-car {
            display: none;
        }

        .gif-bike {
            position: absolute;
            left: -150px;
            bottom: 102px;
            width: 130px;
            height: 58px;
            animation: bikeRide 3.8s linear infinite;
            z-index: 3;
        }

        .gif-bike .body {
            position: absolute;
            left: 30px;
            top: 18px;
            width: 72px;
            height: 18px;
            border-radius: 18px 38px 8px 8px;
            background: #ff7a45;
            transform: skewX(-12deg);
        }

        .gif-bike .seat {
            position: absolute;
            left: 54px;
            top: 9px;
            width: 36px;
            height: 10px;
            border-radius: 12px;
            background: #111;
        }

        .gif-bike .handle {
            position: absolute;
            right: 20px;
            top: 12px;
            width: 28px;
            height: 14px;
            border-top: 4px solid #111;
            border-right: 4px solid #111;
            transform: rotate(-12deg);
        }

        .gif-bike .bike-wheel {
            position: absolute;
            bottom: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #0c1114;
            border: 6px solid #cbd5d9;
            animation: wheelSpin .45s linear infinite;
        }

        .gif-bike .bike-wheel.left { left: 12px; }
        .gif-bike .bike-wheel.right { right: 8px; }

        .shipment-gif-bg.warehouse-scene {
            background:
                linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px) 0 0/80px 80px,
                linear-gradient(135deg, #18252b 0%, #385148 52%, #202a30 100%);
        }

        .shipment-gif-bg.warehouse-scene .gif-sun,
        .shipment-gif-bg.warehouse-scene .gif-cloud,
        .shipment-gif-bg.warehouse-scene .gif-city,
        .shipment-gif-bg.warehouse-scene .gif-car,
        .shipment-gif-bg.warehouse-scene .gif-truck {
            display: none;
        }

        .gif-box-stack {
            position: absolute;
            right: 12%;
            bottom: 120px;
            width: 168px;
            height: 126px;
            background:
                linear-gradient(#c8792a 0 0) 0 54px/74px 72px no-repeat,
                linear-gradient(#e7a13b 0 0) 82px 34px/76px 92px no-repeat,
                linear-gradient(#b86a25 0 0) 38px 0/74px 54px no-repeat;
            filter: drop-shadow(0 16px 28px rgba(0,0,0,.26));
            animation: boxPulse 1.4s ease-in-out infinite;
            z-index: 2;
        }

        .gif-forklift {
            position: absolute;
            left: -170px;
            bottom: 98px;
            width: 154px;
            height: 74px;
            animation: forkliftMove 4.6s linear infinite;
            z-index: 3;
        }

        .gif-forklift .base {
            position: absolute;
            left: 16px;
            bottom: 16px;
            width: 86px;
            height: 38px;
            border-radius: 10px 18px 6px 6px;
            background: #ff7a45;
        }

        .gif-forklift .mast {
            position: absolute;
            right: 30px;
            bottom: 18px;
            width: 8px;
            height: 58px;
            background: #111;
        }

        .gif-forklift .fork {
            position: absolute;
            right: 0;
            bottom: 22px;
            width: 44px;
            height: 6px;
            background: #111;
        }

        .gif-forklift .fork-wheel {
            position: absolute;
            bottom: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #0c1114;
            border: 5px solid #cbd5d9;
            animation: wheelSpin .5s linear infinite;
        }

        .gif-forklift .fork-wheel.left { left: 24px; }
        .gif-forklift .fork-wheel.right { right: 42px; }

        .shipment-gif-bg.payment-scene {
            background:
                radial-gradient(circle at 78% 18%, rgba(14, 143, 122, .36), transparent 14%),
                linear-gradient(135deg, #10212b 0%, #3b5260 48%, #172e25 100%);
        }

        .shipment-gif-bg.payment-scene .gif-car {
            display: none;
        }

        .gif-invoice {
            position: absolute;
            right: 12%;
            top: 160px;
            width: 116px;
            height: 146px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 20px 45px rgba(0,0,0,.24);
            animation: invoiceFloat 2s ease-in-out infinite;
            z-index: 3;
        }

        .gif-invoice:before {
            content: "";
            position: absolute;
            left: 20px;
            right: 20px;
            top: 32px;
            height: 58px;
            background:
                linear-gradient(#0e8f7a 0 0) 0 0/76px 8px no-repeat,
                linear-gradient(#d8e2e0 0 0) 0 24px/76px 7px no-repeat,
                linear-gradient(#d8e2e0 0 0) 0 46px/58px 7px no-repeat;
        }

        .gif-invoice:after {
            content: "₹";
            position: absolute;
            left: 50%;
            bottom: 18px;
            transform: translateX(-50%);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ff7a45;
            color: #10212b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 24px;
        }

        @keyframes sunPulse {
            0%, 100% { transform: scale(1); opacity: .92; }
            50% { transform: scale(1.08); opacity: 1; }
        }

        @keyframes cloudMove {
            to { transform: translateX(calc(100vw + 260px)); }
        }

        @keyframes laneMove {
            to { background-position: -112px 0; }
        }

        @keyframes truckFloat {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-6px); }
        }

        @keyframes wheelSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes carPass {
            to { transform: translateX(calc(100vw + 220px)); }
        }

        @keyframes planeFly {
            0% { transform: translateX(0) translateY(40px) rotate(-6deg); }
            45% { transform: translateX(52vw) translateY(-44px) rotate(-10deg); }
            100% { transform: translateX(calc(100vw + 260px)) translateY(-96px) rotate(-7deg); }
        }

        @keyframes bikeRide {
            0% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(calc(50vw + 80px)) translateY(-4px); }
            100% { transform: translateX(calc(100vw + 230px)) translateY(0); }
        }

        @keyframes forkliftMove {
            0% { transform: translateX(0); }
            45% { transform: translateX(45vw); }
            62% { transform: translateX(45vw); }
            100% { transform: translateX(calc(100vw + 240px)); }
        }

        @keyframes boxPulse {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes invoiceFloat {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-12px) rotate(2deg); }
        }

        @media (max-width: 767px) {
            .home1-banner-section .banner-wrapper,
            .home1-banner-section .banner-img-area,
            .home1-banner-section .banner-video-area,
            .home1-banner-section .shipment-gif-bg,
            .home1-banner-section .banner-img-area img,
            .home1-banner-section .banner-video-area video {
                min-height: 520px;
            }

            .shipment-gif-bg .gif-truck {
                width: 210px;
                transform: translateX(-50%) scale(.82);
            }
        }

        .web-user-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .web-user-chip,
        .web-cart-chip {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .94);
            color: #10212b;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
            line-height: 1;
        }

        .web-user-chip:hover,
        .web-cart-chip:hover {
            color: #0a6c5c;
        }

        .web-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            background: linear-gradient(135deg, #0e8f7a, #ff7a45);
        }

        .web-user-name {
            max-width: 118px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
            font-weight: 700;
        }

        .web-cart-chip {
            position: relative;
            width: 42px;
            justify-content: center;
            padding: 0;
            font-size: 18px;
        }

        .web-cart-count {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #0e8f7a;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        @media (max-width: 575px) {
            .web-user-name {
                display: none;
            }
        }

        .home1-banner-section .banner-pagination {
            position: absolute;
            left: 50%;
            bottom: 34px;
            transform: translateX(-50%);
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .home1-banner-section .banner-pagination .swiper-pagination-bullet {
            width: 11px;
            height: 11px;
            margin: 0;
            opacity: 1;
            background: rgba(255, 255, 255, .58);
            border: 2px solid rgba(255, 255, 255, .86);
        }

        .home1-banner-section .banner-pagination .swiper-pagination-bullet-active {
            width: 32px;
            border-radius: 999px;
            background: #ff7a45;
            border-color: #ff7a45;
        }
    </style>
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
                <a href="{{ url('/') }}" class="header-logo">
                    <img src="{{ asset('assets/img/header-logo.svg') }}" alt="">
                </a>
                <div class="main-menu">
                    <div class="mobile-logo-area d-xl-none d-flex align-items-center justify-content-between">
                        <a href="{{ url('/') }}" class="mobile-logo-wrap">
                            <img src="{{ asset('assets/img/home1/header-logo.svg') }}" alt="">
                        </a>
                        <div class="menu-close-btn">
                            <i class="bi bi-x"></i>
                        </div>
                    </div>
                    <ul class="menu-list">
                        <li class="{{ request()->is('/') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="{{ route('shipment.cart') }}" class="drop-down">
                                Shipment
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <i class="bi bi-plus dropdown-icon"></i>
                            <ul class="sub-menu">
                                <li><a href="{{ route('shipment.add_item') }}">Create Booking</a></li>
                                <li><a href="{{ route('shipment.cart') }}">My Booking</a></li>
                                @auth
                                    <li><a href="{{ route('shipment.leads') }}">Booking Requests</a></li>
                                @endauth
                            </ul>
                        </li>
                        <li class="{{ request()->routeIs('shipment.track') ? 'active' : '' }}">
                            <a href="{{ route('shipment.track') }}">Track & Trace</a>
                        </li>
                    </ul>
                    <!-- <a class="primary-btn1 btn-hover d-xl-none" href="{{ route('shipment.track') }}">
                        Track & Trace
                        <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path
                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                </path>
                            </g>
                        </svg>
                        <span></span>
                    </a> -->
                </div>
            </div>
            <div class="nav-right">
                <div class="contact-area">
                    <div class="search-and-login">
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
                                @php
                                    $cartCount = \App\Models\TransportCartItem::where('user_id', Auth::id())->count();
                                    $userInitial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
                                @endphp

                                <div class="web-user-actions">
                                    <!-- <a href="{{ route('shipment.leads') }}" class="web-user-chip" title="{{ Auth::user()->name }}">
                                        <span class="web-user-avatar">{{ $userInitial }}</span>
                                        <span class="web-user-name">{{ Auth::user()->name }}</span>
                                    </a> -->
                                    <div class="dropdown">
                                        <a href="#" class="web-user-chip dropdown-toggle text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="web-user-avatar">{{ $userInitial }}</span>
                                            <span class="web-user-name">{{ Auth::user()->name }}</span>
                                        </a>

                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3"
                                            aria-labelledby="userDropdown">

                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('user.profile') }}">
                                                    <i class="bi bi-person-circle me-2"></i>
                                                    Profile
                                                </a>
                                            </li>

                                            <li><hr class="dropdown-divider"></li>

                                            <li>
                                                <form action="{{ route('logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger py-2">
                                                        <i class="bi bi-box-arrow-right me-2"></i>
                                                        Logout
                                                    </button>
                                                </form>
                                            </li>

                                        </ul>
                                    </div>

                                    <a href="{{ route('shipment.cart') }}" class="web-cart-chip" title="My Cart">
                                        <i class="bi bi-cart3"></i>
                                        <span class="web-cart-count">{{ $cartCount }}</span>
                                    </a>
                                </div>

                            @endauth
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
                        <div class="shipment-gif-bg">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Doorstep Transport Service</span>
                                    <h1>Ship Your Home, Bike & Goods</h1>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg alt">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>r
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Route Based Premium</span>
                                    <h1>Know Your Transport Cost</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg air-scene">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                            <div class="gif-plane"></div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Live Tracking</span>
                                    <h1>Track Pickup To Delivery</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Admin Verified Requests</span>
                                    <h1>Reliable Shipment Approval</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
                                        Request A Quote
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg alt">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Secure Transport</span>
                                    <h1>Your Goods. Our Priority.</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
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
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg payment-scene">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                            <div class="gif-invoice"></div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Secure Payments</span>
                                    <h1>Invoice Ready Transport Billing</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.cart') }}">
                                        View Cart
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg alt">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Scheduled Pickup</span>
                                    <h1>Choose Dates. We Move It.</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
                                        Book Pickup
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Business & Office Moves</span>
                                    <h1>Bulk Goods With Clear Billing</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.track') }}">
                                        Track Shipment
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg alt">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Bike Transport</span>
                                    <h1>Move Your Bike City To City</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
                                        Add Bike
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg green">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Home Shifting</span>
                                    <h1>Boxes, Furniture And Appliances</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.add_item') }}">
                                        Start Moving
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="banner-wrapper">
                        <div class="shipment-gif-bg">
                            <div class="gif-sun"></div>
                            <div class="gif-cloud"></div>
                            <div class="gif-cloud cloud-two"></div>
                            <div class="gif-city"></div>
                            <div class="gif-road"></div>
                            <div class="gif-car"></div>
                            <div class="gif-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="light"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                        <div class="banner-content-wrap">
                            <div class="container">
                                <div class="banner-content">
                                    <span>Transparent Pricing</span>
                                    <h1>Route, Weight And Volume Based</h1>
                                    <!-- <a class="primary-btn1 btn-hover" href="{{ route('shipment.cart') }}">
                                        Check Cart
                                        <svg width="10" height="10" viewBox="0 0 10 10"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                                                </path>
                                            </g>
                                        </svg>
                                        <span></span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="banner-pagination"></div>
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
