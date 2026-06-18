<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/boxicons.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/production-theme.css') }}">
    <title>Register - OneTrack</title>
    <link rel="icon" href="{{ asset('assets/fav-icon.svg') }}" type="image/gif" sizes="20x20">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 16% 14%, rgba(255, 183, 3, .25), transparent 15%),
                linear-gradient(135deg, #10212b 0%, #24545e 48%, #143324 100%);
        }

        .register-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 48px 0;
        }

        .register-panel {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 26px 70px rgba(0, 0, 0, .22);
            overflow: hidden;
        }

        .register-copy {
            height: 100%;
            min-height: 620px;
            position: relative;
            overflow: hidden;
            padding: 42px;
            color: #fff;
            background:
                radial-gradient(circle at 76% 16%, rgba(255, 183, 3, .38), transparent 13%),
                linear-gradient(135deg, #111f29 0%, #1e5663 54%, #143421 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .register-copy h1 {
            color: #fff;
            font-size: clamp(34px, 4vw, 56px);
            line-height: 1.04;
            margin: 18px 0 14px;
        }

        .register-copy p {
            color: rgba(255,255,255,.78);
            max-width: 460px;
        }

        .register-road {
            position: absolute;
            left: -8%;
            right: -8%;
            bottom: 0;
            height: 166px;
            background: linear-gradient(180deg, #364249, #171d20);
            transform: skewY(-2deg);
            box-shadow: inset 0 8px 0 rgba(255,255,255,.06);
        }

        .register-road:before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 74px;
            height: 6px;
            background: repeating-linear-gradient(90deg, #f7c948 0 58px, transparent 58px 102px);
            animation: laneMove 1s linear infinite;
        }

        .register-truck {
            position: absolute;
            left: 50%;
            bottom: 96px;
            width: 236px;
            height: 86px;
            transform: translateX(-50%);
            animation: truckBounce 1.1s ease-in-out infinite;
            z-index: 2;
        }

        .register-truck .box {
            position: absolute;
            left: 0;
            top: 16px;
            width: 144px;
            height: 60px;
            border-radius: 9px;
            background: linear-gradient(135deg, #18a058, #0b7b43);
        }

        .register-truck .cab {
            position: absolute;
            right: 8px;
            top: 30px;
            width: 82px;
            height: 46px;
            border-radius: 9px 24px 7px 5px;
            background: linear-gradient(135deg, #ffb703, #f47c20);
        }

        .register-truck .cab:before {
            content: "";
            position: absolute;
            right: 19px;
            top: 8px;
            width: 29px;
            height: 17px;
            border-radius: 5px;
            background: #c9f3ff;
        }

        .register-truck .wheel {
            position: absolute;
            bottom: 0;
            width: 31px;
            height: 31px;
            border-radius: 50%;
            background: #080c0f;
            border: 6px solid #aab5bb;
            animation: wheelSpin .55s linear infinite;
        }

        .register-truck .wheel.left { left: 35px; }
        .register-truck .wheel.right { right: 38px; }

        .register-feature-list {
            position: relative;
            z-index: 3;
            display: grid;
            gap: 12px;
            max-width: 420px;
        }

        .register-feature-list div {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.86);
        }

        .register-feature-list i {
            color: #ffb703;
        }

        .register-form-area {
            padding: 44px;
        }

        .register-title span {
            color: #18a058;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .register-title h2 {
            margin: 8px 0 8px;
            color: #10212b;
        }

        .approval-note {
            margin: 20px 0;
            padding: 14px;
            border-radius: 8px;
            background: #f1f7f4;
            color: #52636b;
            font-size: 14px;
        }

        .terms-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 18px 0 24px;
            color: #53646c;
            font-size: 14px;
        }

        @keyframes laneMove {
            to { background-position: -102px 0; }
        }

        @keyframes truckBounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-6px); }
        }

        @keyframes wheelSpin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 991px) {
            .register-copy {
                min-height: 390px;
            }

            .register-form-area {
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <main class="register-shell">
        <div class="container">
            <div class="register-panel">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="register-copy">
                            <div style="position:relative;z-index:3;">
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('assets/img/header-logo.svg') }}" alt="OneTrack" style="max-width:180px;">
                                </a>
                                <h1>Create your shipment account.</h1>
                                <p>Add items to cart, save transport leads, track every request, and keep payment records in one place.</p>
                            </div>

                            <div class="register-feature-list">
                                <div><i class="bi bi-check-circle-fill"></i> Route based shipment calculation</div>
                                <div><i class="bi bi-check-circle-fill"></i> Cart, leads and tracking in one account</div>
                                <div><i class="bi bi-check-circle-fill"></i> Admin approval managed from CRM</div>
                            </div>

                            <div class="register-road"></div>
                            <div class="register-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="register-form-area">
                            <div class="register-title">
                                <span>Customer Access</span>
                                <h2>Register Account</h2>
                                <p class="mb-0">Already registered? <a href="{{ route('login') }}">Login</a></p>
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger mt-3">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(!($settings->email_login_enabled ?? false))
                                <div class="alert alert-warning mt-4">
                                    Email registration is currently disabled by admin.
                                </div>
                            @else
                                <div class="approval-note">
                                    @if($settings->admin_approval_required)
                                        Your account will be created as pending and admin will approve it from CRM.
                                    @else
                                        Your account will be active immediately after registration.
                                    @endif
                                </div>

                                <form action="{{ route('register.submit') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-inner">
                                                <label>Full Name</label>
                                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Demo Customer">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-inner">
                                                <label>Email</label>
                                                <input type="email" name="email" value="{{ old('email') }}" placeholder="customer@example.com">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-inner">
                                                <label>Password</label>
                                                <input type="password" name="password" placeholder="********">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-inner">
                                                <label>Confirm Password</label>
                                                <input type="password" name="password_confirmation" placeholder="********">
                                            </div>
                                        </div>
                                    </div>

                                    <label class="terms-box">
                                        <input type="checkbox" required>
                                        <span>I agree to create an account for shipment cart, leads, tracking and payment updates.</span>
                                    </label>

                                    <button type="submit" class="primary-btn2 btn-hover w-100">
                                        Register Now
                                        <span></span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
