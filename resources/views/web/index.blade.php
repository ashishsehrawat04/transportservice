@include('web.header')

<style>
    .transport-home-content {
        background: #f7f9f8;
    }

    .home-band {
        padding: 82px 0;
    }

    .home-section-title span {
        color: #0e8f7a;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
    }

    .home-section-title h2 {
        margin: 8px 0 0;
        font-size: clamp(30px, 4vw, 46px);
        line-height: 1.12;
        color: #10212b;
    }

    .home-section-title p {
        max-width: 680px;
        margin: 14px 0 0;
        color: #5d6b72;
    }

    .service-tile,
    .step-tile,
    .stat-tile {
        height: 100%;
        border: 1px solid #e1e8e5;
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 16px 38px rgba(16, 33, 43, .06);
    }

    .tile-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: #0e8f7a;
        font-size: 22px;
        margin-bottom: 18px;
    }

    .service-tile h4,
    .step-tile h4 {
        margin-bottom: 10px;
        color: #10212b;
    }

    .service-tile p,
    .step-tile p {
        margin-bottom: 0;
        color: #627179;
    }

    .route-panel {
        background: #10212b;
        color: #fff;
        border-radius: 8px;
        padding: 38px;
        position: relative;
        overflow: hidden;
    }

    .route-panel:before {
        content: "";
        position: absolute;
        inset: auto -12% -46px -12%;
        height: 150px;
        background: linear-gradient(180deg, #333f45, #151b1f);
        transform: skewY(-2deg);
    }

    .route-panel > * {
        position: relative;
        z-index: 1;
    }

    .route-panel h2 {
        color: #fff;
        margin-bottom: 12px;
    }

    .route-panel p {
        color: rgba(255,255,255,.76);
        margin-bottom: 0;
    }

    .route-line {
        margin-top: 28px;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 18px;
        align-items: center;
    }

    .route-city {
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 8px;
        padding: 16px;
    }

    .route-city small {
        display: block;
        color: rgba(255,255,255,.62);
        margin-bottom: 4px;
    }

    .stat-tile strong {
        display: block;
        color: #10212b;
        font-size: 34px;
        line-height: 1;
    }

    .stat-tile span {
        display: block;
        margin-top: 8px;
        color: #627179;
    }

    .home-cta {
        background: #0e8f7a;
        color: #fff;
        padding: 54px 0;
    }

    .home-cta h2 {
        color: #fff;
        margin: 0;
    }

    .home-cta p {
        margin: 10px 0 0;
        color: rgba(255,255,255,.82);
    }

    @media (max-width: 767px) {
        .home-band {
            padding: 58px 0;
        }

        .route-panel {
            padding: 26px;
        }

        .route-line {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="transport-home-content">
    <section class="home-band">
        <div class="container">
            <div class="home-section-title text-center mb-5">
                <span>Transport CRM Flow</span>
                <h2>From quote to delivery, everything stays trackable.</h2>
                <p class="mx-auto">Customers add shipment items, get route based pricing, save leads, and track pickup status while the admin manages approvals, payments, and delivery updates.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-tile">
                        <div class="tile-icon"><i class="bi bi-box-seam"></i></div>
                        <h4>Add Shipment Items</h4>
                        <p>Capture dimensions, weight, quantity, pickup date, delivery date, and route in one clean flow.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-tile">
                        <div class="tile-icon"><i class="bi bi-calculator"></i></div>
                        <h4>Auto Price Calculation</h4>
                        <p>Pricing uses service rates, route distance, per-km charges, minimum charge, tax, discount, and max cap.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-tile">
                        <div class="tile-icon"><i class="bi bi-truck"></i></div>
                        <h4>Track The Lead</h4>
                        <p>Every request gets tracking details so customers can follow approval, dispatch, and delivery status.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-band pt-0">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="route-panel">
                        <span style="color:#ff7a45; font-weight:700;">Live Route Pricing</span>
                        <h2>Route rate and item price work together.</h2>
                        <p>Admin can define city routes and transport price rules separately. The final charge is built from route distance, route rate, item weight, volume, and service caps.</p>
                        <div class="route-line">
                            <div class="route-city">
                                <small>Pickup</small>
                                <strong>Delhi</strong>
                            </div>
                            <i class="bi bi-arrow-right-circle" style="font-size:34px;color:#ff7a45;"></i>
                            <div class="route-city">
                                <small>Delivery</small>
                                <strong>Jaipur</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="home-section-title mb-4">
                        <span>Services</span>
                        <h2>Built for home, bike, office and fragile goods.</h2>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="service-tile">
                                <div class="tile-icon"><i class="bi bi-bicycle"></i></div>
                                <h4>Bike Transport</h4>
                                <p>Two-wheeler movement with route based billing.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="service-tile">
                                <div class="tile-icon"><i class="bi bi-house-door"></i></div>
                                <h4>Home Goods</h4>
                                <p>Boxes, furniture and household shifting support.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="service-tile">
                                <div class="tile-icon"><i class="bi bi-pc-display"></i></div>
                                <h4>Electronics</h4>
                                <p>Fragile item details with volume and weight pricing.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="service-tile">
                                <div class="tile-icon"><i class="bi bi-receipt"></i></div>
                                <h4>Invoices</h4>
                                <p>Payment records and invoice numbers for admin CRM.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-band pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-tile">
                        <strong>24/7</strong>
                        <span>Tracking ready</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-tile">
                        <strong>3+</strong>
                        <span>Demo routes</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-tile">
                        <strong>4</strong>
                        <span>Status stages</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-tile">
                        <strong>100%</strong>
                        <span>CRM managed</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-cta">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Ready to check your transport cost?</h2>
                    <p>Add an item, choose your route, and let the system calculate the shipment amount.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="primary-btn1 white-bg btn-hover" href="{{ route('shipment.add_item') }}">
                        Request A Quote
                        <span></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('web.footer')
