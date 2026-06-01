<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected function redirectVendor(User $user)
    {
        $vendor = $user->vendor;

        if ($vendor && $vendor->status === 'Approved') {
            return redirect()->route('vendor.dashboard')->with('success', 'Selamat datang di konsol manajemen vendor.');
        }

        return redirect()->route('vendor.status')->with('success', 'Silakan cek status pendaftaran vendor Anda terlebih dahulu.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Ambil data peran user yang berhasil login
            $role = strtolower(Auth::user()->role ?? 'rider');

            // Logika Otomatis Pengalihan Rute Berdasarkan Peran
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Pusat kendali admin berhasil diakses.');
            } elseif ($role === 'vendor') {
                return $this->redirectVendor(Auth::user());
            }

            // Jalur default untuk pengendara umum (Rider)
            return redirect()->route('rider.map')->with('success', 'Sesi masuk berhasil dibuat.');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validasi ketat: Hanya mengizinkan pilihan input 'rider' atau 'vendor' dari luar
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:rider,vendor' 
        ]);

        // Pembuatan entitas user baru ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, 
        ]);

        // Otomatis login setelah pendaftaran sukses
        Auth::login($user);

        // Jalur pengalihan instan pasca-registrasi
        if ($user->role === 'vendor') {
            return $this->redirectVendor($user);
        }

        return redirect()->route('rider.map')->with('success', 'Akun pengendara berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}