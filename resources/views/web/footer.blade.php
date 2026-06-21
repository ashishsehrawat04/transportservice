 <footer class="footer-section">
        <div class="container">
            <div class="company-logo-and-contact-area">
                <div class="row gy-5">
                    <div class="col-lg-4">
                        <div class="footer-logo-and-social">
                            <div class="logo-area">
                                <a href="#"><img src="{{ asset('assets/img/home1/Footer-logo-h1.svg') }}" alt=""></a>
                            </div>
                            <p>Fast route pricing, shipment requests, tracking, invoices, and delivery updates from one transport dashboard.</p>
                            <ul class="social-list">
                                <li><a href="https://www.facebook.com/"><i class="bx bxl-facebook"></i></a></li>
                                <li><a href="https://www.linkedin.com/"><i class="bx bxl-linkedin"></i></a></li>
                                <li><a href="https://www.youtube.com/"><i class="bx bxl-youtube"></i></a></li>
                                <li><a href="https://www.instagram.com/"><i class="bx bxl-instagram-alt"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="contact-area">
                            <h2>Move goods with clear pricing and live shipment control.</h2>
                            <ul class="mail-and-call">
                                <li>
                                    <div class="icon">
                                        <img src="{{ asset('assets/img/home1/icon/footer-mail.svg') }}" alt="">
                                    </div>
                                    <div class="content">
                                        <p>Send Us Mail</p>
                                        <a href="mailto:support@onetrack.test">support@onetrack.test</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="{{ asset('assets/img/home1/icon/footer-call-icon.svg') }}" alt="">
                                    </div>
                                    <div class="content">
                                        <p>Collaborate!</p>
                                        <a href="tel:+919000000001">(+91) 90000 00001</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-menu">
            <div class="container">
                <div class="row gy-5 justify-content-between">
                    <div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h3>Download App</h3>
                            </div>
                            <div class="store">
                                <a href="#"><img src="{{ asset('assets/img/home1/icon/play-store.svg') }}" alt="Play-store"></a>
                                <a href="#"><img src="{{ asset('assets/img/home1/icon/apple-store.svg') }}" alt="apple-store"></a>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-xl-2 col-lg-3 col-md-4 col-sm-6 d-flex justify-content-lg-start justify-content-md-center justify-content-sm-center">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h3>Company</h3>
                            </div>
                            <ul class="widget-list">
                                <li><a href="{{ url('/') }}">Home</a></li>
                                <li><a href="{{ route('shipment.add_item') }}">Create Shipment</a></li>
                                <li><a href="{{ route('shipment.cart') }}">Cart</a></li>
                                <li><a href="{{ route('shipment.track') }}">Track Shipment</a></li>
                                <li><a href="{{ route('login') }}">Customer Login</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 d-flex justify-content-lg-center justify-content-md-end">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h3>Solutions</h3>
                            </div>
                            <ul class="widget-list">
                                <li><a href="{{ route('shipment.add_item') }}">Route Pricing</a></li>
                                <li><a href="{{ route('shipment.add_item') }}">Goods Transport</a></li>
                                <li><a href="{{ route('shipment.add_item') }}">Bike Transport</a></li>
                                <li><a href="{{ route('shipment.track') }}">Status Tracking</a></li>
                                <li><a href="{{ route('shipment.cart') }}">Lead Checkout</a></li>
                            </ul>
                        </div>
                    </div>
                    <div
                        class="col-xl-2 col-lg-3 col-md-4 col-sm-6 d-flex justify-content-lg-end justify-content-md-start justify-content-sm-center">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h3>Support</h3>
                            </div>
                            <ul class="widget-list">
                                <li><a href="{{ route('shipment.add_item') }}">Request a quote</a></li>
                                <li><a href="{{ route('shipment.track') }}">Track & Trace</a></li>
                                @auth
                                    <li><a href="{{ route('shipment.leads') }}">Shipment Requests</a></li>
                                    <li><a href="{{ route('user.profile') }}">Profile</a></li>
                                @else
                                    <li><a href="{{ route('register') }}">Create Account</a></li>
                                    <li><a href="{{ route('login') }}">Login</a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>Copyright 2026 <a href="{{ url('/') }}">OneTrack</a> | All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->
    <!--  Main jQuery  -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

    <!-- Popper and Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <!-- Swiper slider JS -->
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/slick.js') }}"></script>
    <!-- Waypoints JS -->
    <script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
    <!-- Counterup JS -->
    <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
    <!-- Wow JS -->
    <script src="{{ asset('assets/js/wow.min.js') }}"></script>
    <!-- Gsap  JS -->
    <script src="{{ asset('assets/js/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.fancybox.min.js') }}"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/select-dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>

</html>
