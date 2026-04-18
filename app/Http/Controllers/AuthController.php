<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password), // Password akan otomatis di-hash karena cast di model
            'role' => 'rider', 
            'status' => 'aktif'
        ]);

        Auth::login($user);

        return redirect()->intended('/rider/vehicles')->with('success', 'Akun berhasil dibuat dan Anda telah login.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->status !== 'aktif') {
            return back()->withErrors([
                'email' => 'Akun tidak ditemukan atau sudah tidak aktif.',
            ])->onlyInput('email');
        }

        if (Hash::check($request->password, $user->password)) {
            // Jika benar, lakukan login manual
            Auth::login($user, $request->remember);
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'vendor') {
                return redirect('/vendor/chargers');
            } elseif ($user->role === 'rider') {
                return redirect('/rider/vehicles');
            }

        }

        return back()->withErrors([
            'email' => 'Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}