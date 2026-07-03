<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Login OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .otp-section {
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .otp-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 4px;
            text-align: center;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
        }
        .expires-in {
            font-size: 13px;
            color: #e74c3c;
            text-align: center;
            margin-top: 10px;
            font-weight: 500;
        }
        .message {
            font-size: 14px;
            color: #555;
            margin: 20px 0;
            line-height: 1.8;
        }
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 13px;
            color: #856404;
        }
        .security-notice strong {
            display: block;
            margin-bottom: 5px;
        }
        .footer {
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
            font-weight: 500;
        }
        .button:hover {
            background-color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <p style="margin: 0 0 5px 0; font-size: 12px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">📦 Cargo Logistics</p>
            <h1>{{ $appName }}</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Login Verification</p>
        </div>

        <!-- Content -->
        <div class="content">
            @if($userName)
                <div class="greeting">
                    Hello <strong>{{ $userName }}</strong>,
                </div>
            @else
                <div class="greeting">
                    Hello,
                </div>
            @endif

            <div class="message">
                You've requested to log in to your Cargo account. Use the One-Time Password (OTP) below to complete your login.
            </div>

            <!-- OTP Section -->
            <div class="otp-section">
                <div class="otp-label">Your Login OTP</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="expires-in">⏱️ Expires in {{ $expiresIn }} minutes</div>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <strong>🔒 Security Notice</strong>
                <div>Never share this OTP with anyone. {{ $appName }} team will never ask for your OTP via email, phone, or any other means.</div>
            </div>

            <div class="message">
                <strong>Didn't request this OTP?</strong><br>
                If you didn't attempt to log in to your account, you can ignore this email. Your account is safe, and no changes have been made.
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="button">Go to {{ $appName }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                This is an automated message, please do not reply to this email.
            </p>
            <p style="margin: 0;">
                © {{ date('Y') }} {{ $appName }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
