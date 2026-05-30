<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/boxicons.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <title>Login - OneTrack</title>
    <link rel="icon" href="{{ asset('assets/fav-icon.svg') }}" type="image/gif" sizes="20x20">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 84% 16%, rgba(255, 183, 3, .28), transparent 16%),
                linear-gradient(135deg, #10212b 0%, #24545e 48%, #143324 100%);
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 48px 0;
        }

        .auth-panel {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 26px 70px rgba(0, 0, 0, .22);
            overflow: hidden;
        }

        .auth-scene {
            height: 100%;
            min-height: 520px;
            background:
                radial-gradient(circle at 78% 16%, rgba(255, 183, 3, .42), transparent 13%),
                linear-gradient(135deg, #111f29 0%, #1e5663 54%, #143421 100%);
            position: relative;
            overflow: hidden;
            color: #fff;
            padding: 42px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .auth-scene h1 {
            color: #fff;
            font-size: clamp(34px, 4vw, 56px);
            line-height: 1.04;
            margin-top: 16px;
        }

        .auth-scene p {
            color: rgba(255,255,255,.78);
            max-width: 440px;
        }

        .auth-road {
            position: absolute;
            left: -8%;
            right: -8%;
            bottom: 0;
            height: 155px;
            background: linear-gradient(180deg, #364249, #171d20);
            transform: skewY(-2deg);
            box-shadow: inset 0 8px 0 rgba(255,255,255,.06);
        }

        .auth-road:before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 68px;
            height: 6px;
            background: repeating-linear-gradient(90deg, #f7c948 0 58px, transparent 58px 102px);
            animation: authLane 1s linear infinite;
        }

        .auth-truck {
            position: absolute;
            left: 50%;
            bottom: 88px;
            width: 228px;
            height: 82px;
            transform: translateX(-50%);
            animation: authTruck 1.1s ease-in-out infinite;
            z-index: 2;
        }

        .auth-truck .box {
            position: absolute;
            left: 0;
            top: 15px;
            width: 138px;
            height: 58px;
            border-radius: 9px;
            background: linear-gradient(135deg, #18a058, #0b7b43);
        }

        .auth-truck .cab {
            position: absolute;
            right: 8px;
            top: 28px;
            width: 78px;
            height: 45px;
            border-radius: 9px 24px 7px 5px;
            background: linear-gradient(135deg, #ffb703, #f47c20);
        }

        .auth-truck .cab:before {
            content: "";
            position: absolute;
            right: 18px;
            top: 8px;
            width: 28px;
            height: 17px;
            border-radius: 5px;
            background: #c9f3ff;
        }

        .auth-truck .wheel {
            position: absolute;
            bottom: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #080c0f;
            border: 6px solid #aab5bb;
            animation: authWheel .55s linear infinite;
        }

        .auth-truck .wheel.left { left: 34px; }
        .auth-truck .wheel.right { right: 36px; }

        .auth-form-area {
            padding: 44px;
        }

        .auth-title span {
            color: #18a058;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .auth-title h2 {
            margin: 8px 0 8px;
            color: #10212b;
        }

        .auth-mode-switch {
            display: flex;
            gap: 8px;
            padding: 5px;
            border-radius: 999px;
            background: #eef4f1;
            margin: 24px 0;
        }

        .auth-mode-btn {
            flex: 1;
            border: 0;
            border-radius: 999px;
            padding: 11px 16px;
            font-weight: 700;
            color: #4f6068;
            background: transparent;
        }

        .auth-mode-btn.active {
            background: #18a058;
            color: #fff;
            box-shadow: 0 10px 24px rgba(24, 160, 88, .24);
        }

        .auth-method {
            display: none;
        }

        .auth-method.active {
            display: block;
        }

        .auth-note {
            margin-top: 20px;
            padding: 14px;
            border-radius: 8px;
            background: #f1f7f4;
            color: #52636b;
            font-size: 14px;
        }

        @keyframes authLane {
            to { background-position: -102px 0; }
        }

        @keyframes authTruck {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-6px); }
        }

        @keyframes authWheel {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 991px) {
            .auth-scene {
                min-height: 360px;
            }

            .auth-form-area {
                padding: 30px;
            }
        }
    </style>
</head>

@php
    $emailEnabled = $settings->email_login_enabled ?? false;
    $mobileEnabled = $settings->mobile_login_enabled ?? false;
    $activeMode = old('login_mode', session('login_mode', $emailEnabled ? 'email' : 'mobile'));
    if (!$mobileEnabled && $activeMode === 'mobile') {
        $activeMode = 'email';
    }
    if (!$emailEnabled && $activeMode === 'email') {
        $activeMode = 'mobile';
    }
@endphp

<body>
    <main class="auth-shell">
        <div class="container">
            <div class="auth-panel">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="auth-scene">
                            <div>
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('assets/img/header-logo.svg') }}" alt="OneTrack" style="max-width:180px;">
                                </a>
                                <h1>One login for customers and admins.</h1>
                                <p>Admin controls which login methods are active. The same login page sends users to the website and admins to CRM.</p>
                            </div>
                            <div class="auth-road"></div>
                            <div class="auth-truck">
                                <div class="box"></div>
                                <div class="cab"></div>
                                <div class="wheel left"></div>
                                <div class="wheel right"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="auth-form-area">
                            <div class="auth-title">
                                <span>Secure Access</span>
                                <h2>Login</h2>
                                @if($emailEnabled)
                                    <p class="mb-0">New customer? <a href="{{ route('register') }}">Create account</a></p>
                                @else
                                    <p class="mb-0">Registration is currently managed by admin.</p>
                                @endif
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif
                            @if(session('dev_otp'))
                                <div class="alert alert-info mt-3">Development OTP: {{ session('dev_otp') }}</div>
                            @endif

                            @if(!$emailEnabled && !$mobileEnabled)
                                <div class="alert alert-warning mt-4">Login is currently disabled by admin.</div>
                            @else
                                @if($emailEnabled && $mobileEnabled)
                                    <div class="auth-mode-switch">
                                        <button type="button" class="auth-mode-btn {{ $activeMode === 'email' ? 'active' : '' }}" data-auth-mode="email">
                                            <i class="bi bi-envelope"></i> Email
                                        </button>
                                        <button type="button" class="auth-mode-btn {{ $activeMode === 'mobile' ? 'active' : '' }}" data-auth-mode="mobile">
                                            <i class="bi bi-phone"></i> Mobile OTP
                                        </button>
                                    </div>
                                @endif

                                @if($emailEnabled)
                                    <div class="auth-method {{ $activeMode === 'email' ? 'active' : '' }}" data-auth-panel="email">
                                        <form action="{{ route('login') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="login_mode" value="email">
                                            <div class="form-inner mb-20">
                                                <label>Email</label>
                                                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@transport.test">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-inner mb-20">
                                                <label>Password</label>
                                                <input id="password" name="password" type="password" placeholder="********">
                                                <i class="bi bi-eye-slash bi-eye" id="togglePassword"></i>
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <button type="submit" class="primary-btn2 btn-hover w-100">
                                                Login Now
                                                <span></span>
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if($mobileEnabled)
                                    <div class="auth-method {{ $activeMode === 'mobile' ? 'active' : '' }}" data-auth-panel="mobile">
                                        <form action="{{ route('login.mobile.send_otp') }}" method="POST" class="mb-3">
                                            @csrf
                                            <input type="hidden" name="login_mode" value="mobile">
                                            <div class="form-inner mb-20">
                                                <label>Mobile Number</label>
                                                <input type="text" name="mobile" value="{{ old('mobile') }}" placeholder="9876543210">
                                                @error('mobile')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <button type="submit" class="primary-btn2 btn-hover w-100">
                                                Send OTP
                                                <span></span>
                                            </button>
                                        </form>

                                        <form action="{{ route('login.mobile.verify') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="login_mode" value="mobile">
                                            <div class="form-inner mb-20">
                                                <label>Mobile Number</label>
                                                <input type="text" name="mobile" value="{{ old('mobile') }}" placeholder="9876543210">
                                            </div>
                                            <div class="form-inner mb-20">
                                                <label>OTP</label>
                                                <input type="text" name="otp" placeholder="6 digit OTP">
                                                @error('otp')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <button type="submit" class="primary-btn2 btn-hover w-100">
                                                Verify & Login
                                                <span></span>
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <div class="auth-note">
                                    Admin login and customer login use this same page. Redirect is automatic after login.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.querySelectorAll('[data-auth-mode]').forEach(function (button) {
            button.addEventListener('click', function () {
                const mode = button.getAttribute('data-auth-mode');

                document.querySelectorAll('[data-auth-mode]').forEach(function (item) {
                    item.classList.toggle('active', item === button);
                });

                document.querySelectorAll('[data-auth-panel]').forEach(function (panel) {
                    panel.classList.toggle('active', panel.getAttribute('data-auth-panel') === mode);
                });
            });
        });

        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function () {
                password.type = password.type === 'password' ? 'text' : 'password';
                togglePassword.classList.toggle('bi-eye');
                togglePassword.classList.toggle('bi-eye-slash');
            });
        }
    </script>
</body>

</html>
