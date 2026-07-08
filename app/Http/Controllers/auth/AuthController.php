<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\TransportAuthSetting;
use App\Models\User;
use App\Models\UserLoginOtp;
use App\Services\GuestCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $settings = TransportAuthSetting::current();

        return view('web.login', compact('settings'));
    }

    public function showRegisterForm()
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->email_login_enabled) {
            return redirect()->route('login')->with('success', 'Registration is currently disabled by admin.');
        }

        return view('web.register', compact('settings'));
    }

    public function login(Request $request)
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->email_login_enabled) {
            return back()->withErrors([
                'email' => 'Email login is disabled by admin.',
            ])->withInput($request->only('email') + ['login_mode' => 'email']);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $user = auth()->user();

            if ($settings->admin_approval_required && $user->role !== 'admin' && $user->status !== 'approved') {
                auth()->logout();
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval.',
                ])->withInput($request->only('email') + ['login_mode' => 'email']);
            }

            if (!$user->slug) {
                $user->update([
                    'slug' => $this->generateSlug($user->name),
                ]);
            }

            app(GuestCartService::class)->mergeToUser($user->id);

            if ($user->role == 'admin') {
                return redirect()->intended('/admin');
            }
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email') + ['login_mode' => 'email']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    public function sendEmailOtp(Request $request)
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->email_login_enabled) {
            return response()->json([
                'message' => 'Email login is disabled by admin.',
                'errors' => ['email' => ['Email login is disabled by admin.']],
            ], 422);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'No account found for this email.',
                'errors' => ['email' => ['No account found for this email.']],
            ], 422);
        }

        $otp = (string) rand(100000, 999999);
        $user->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp, $user->name));

        return response()->json(['message' => 'OTP sent successfully. Check your email for the code.']);
    }

    public function verifyEmailOtp(Request $request)
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->email_login_enabled) {
            return back()->withErrors([
                'email' => 'Email login is disabled by admin.',
            ])->withInput(['login_mode' => 'email']);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user || !$user->otp || !$user->otp_expires_at || $user->otp_expires_at->lt(now()) || !Hash::check($validated['otp'], $user->otp)) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP.',
            ])->withInput($request->only('email') + ['login_mode' => 'email']);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        if ($settings->admin_approval_required && $user->role !== 'admin' && $user->status !== 'approved') {
            return redirect()->route('login')->with('success', 'OTP verified. Your account is pending admin approval.');
        }

        if (!$user->slug) {
            $user->update([
                'slug' => $this->generateSlug($user->name),
            ]);
        }

        auth()->login($user, $request->boolean('remember'));
        app(GuestCartService::class)->mergeToUser($user->id);

        if ($user->role == 'admin') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/');
    }

    public function requestMobileOtp(Request $request)
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->mobile_login_enabled) {
            return back()->withErrors([
                'mobile' => 'Mobile login is disabled by admin.',
            ])->withInput(['login_mode' => 'mobile']);
        }

        $validated = $request->validate([
            'mobile' => ['required', 'digits_between:10,15'],
        ]);

        $otp = (string) rand(100000, 999999);

        UserLoginOtp::create([
            'mobile' => $validated['mobile'],
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
        ]);

        return back()
            ->with('success', 'OTP sent successfully.')
            ->with('dev_otp', $otp)
            ->with('login_mode', 'mobile')
            ->withInput($request->only('mobile'));
    }

    public function verifyMobileOtp(Request $request)
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->mobile_login_enabled) {
            return back()->withErrors([
                'mobile' => 'Mobile login is disabled by admin.',
            ])->withInput(['login_mode' => 'mobile']);
        }

        $validated = $request->validate([
            'mobile' => ['required', 'digits_between:10,15'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otp = UserLoginOtp::where('mobile', $validated['mobile'])
            ->whereNull('verified_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (!$otp || !Hash::check($validated['otp'], $otp->otp_hash)) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP.',
            ])->withInput($request->only('mobile') + ['login_mode' => 'mobile']);
        }

        $otp->update(['verified_at' => now()]);

        $user = User::firstOrCreate(
            ['mobile' => $validated['mobile']],
            [
                'name' => 'Mobile User ' . substr($validated['mobile'], -4),
                'slug' => $this->generateSlug('mobile-user-' . substr($validated['mobile'], -4)),
                'login_type' => 'mobile',
                'status' => $settings->admin_approval_required ? 'pending' : 'approved',
            ]
        );

        if ($settings->admin_approval_required && $user->role !== 'admin' && $user->status !== 'approved') {
            return redirect()->route('login')->with('success', 'Mobile verified. Your account is pending admin approval.');
        }

        auth()->login($user, $request->boolean('remember'));
        app(GuestCartService::class)->mergeToUser($user->id);

        return redirect()->intended('/');
    }

    public function register(Request $request)
    {
        $settings = TransportAuthSetting::current();

        if (!$settings->email_login_enabled) {
            return back()->withErrors([
                'email' => 'Email registration is disabled by admin.',
            ])->withInput();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'slug' => $this->generateSlug($request->name),
            'login_type' => 'email',
            'status' => $settings->admin_approval_required ? 'pending' : 'approved',
        ]);

        app(GuestCartService::class)->mergeToUser($user->id);

        if ($settings->admin_approval_required && $user->status !== 'approved') {
            return redirect()->route('login')->with('success', 'Registration submitted. Your account is pending admin approval.');
        }

        auth()->login($user, $request->boolean('remember'));
        return redirect()->intended('/');
    }

    private function generateSlug(string $name): string
    {
        do {
            $slug = Str::slug($name) . time() . rand(1000, 9999);
        } while (User::where('slug', $slug)->exists());

        return $slug;
    }
}
