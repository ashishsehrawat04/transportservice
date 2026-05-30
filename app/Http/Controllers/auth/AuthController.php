<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('web.login');
    }

    public function showRegisterForm()
    {
        return view('web.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials)) {

        $slug = Str::slug($request->name);

            $random = rand(1000, 9999);
            $slug = $slug . time() . $random;


            while (User::where('slug', $slug)->exists()) {

                $random = rand(1000, 9999);

                $slug = Str::slug($request->name) . time() . $random;
            }

                auth()->user()->update([
                    'slug' => $slug
                ]);
              //dd(auth()->user());

              if(auth()->user()->role == 'admin') {
                    return redirect()->intended('/admin');
              }
            return redirect()->intended('/');
        }

        // dd(Auth::user());

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $slug = Str::slug($request->name);
        $random = rand(1000, 9999);
        $slug = $slug . time() . $random;

        while (User::where('slug', $slug)->exists()) {

            $random = rand(1000, 9999);

            $slug = Str::slug($request->name) . time() . $random;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'slug' => $slug
        ]);

        auth()->login($user);

        return redirect()->intended('/');
    }
}
