<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SpkluController;
use App\Http\Controllers\VendorProfileController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ChargerMachineController;
use App\Http\Controllers\Admin\AdminDashboardController;

// 1. HALAMAN UTAMA (LANDING PAGE)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. AREA AUTENTIKASI PENGENDARA EV (Wisnu)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Area Profil (Memerlukan data user, biarkan auth aktif)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// PENTING: SEMENTARA MASA TESTING, MIDDLEWARE 'auth' DIMATIKAN
// PADA SEMUA RUTE DI BAWAH INI
// ==========================================

// 3. AREA PENGENDARA (RIDER)
Route::prefix('rider')->name('rider.')->group(function () {
    // Garasi Digital (Byan)
    Route::resource('vehicles', VehicleController::class);
    // Pemetaan SPKLU (Azka & Aimee)
    Route::get('/peta', [SpkluController::class, 'index'])->name('map');
Route::get('/spklu/markers', [SpkluController::class, 'getDynamicMarkers'])->name('api.spklu.markers');
});

// 4. AREA VENDOR (MITRA SPKLU)
Route::prefix('vendor')->name('vendor.')->group(function () {
    // Pendaftaran Vendor (Fakhri)
    Route::resource('profile', VendorProfileController::class)->only(['create', 'store', 'show']);
    Route::resource('documents', VendorController::class)->only(['create', 'store', 'show', 'edit', 'update']);
    Route::get('status', [VendorController::class, 'status'])->name('status');
    // Manajemen Mesin (Riehand)
    Route::resource('chargers', ChargerMachineController::class);
});

// 5. AREA PANEL ADMIN & VERIFIKASI (Langgeng)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stations', [AdminDashboardController::class, 'stations'])->name('stations');
    Route::patch('/vendors/{id}/approve', [AdminDashboardController::class, 'approve'])->name('vendors.approve');
    Route::patch('/vendors/{id}/reject', [AdminDashboardController::class, 'reject'])->name('vendors.reject');
    Route::patch('/vendors/{id}/suspend', [AdminDashboardController::class, 'suspend'])->name('vendors.suspend');
    Route::patch('/vendors/{id}/activate', [AdminDashboardController::class, 'activate'])->name('vendors.activate');
    Route::delete('/vendors/{id}/destroy', [AdminDashboardController::class, 'destroy'])->name('vendors.destroy');
});

// 6. AREA API (LAYANAN DATA FRONTEND)
Route::get('/api/spklus', [SpkluController::class, 'getSpkluData']);