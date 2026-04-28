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

// ==========================================
// 1. HALAMAN UTAMA (LANDING PAGE)
// ==========================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ==========================================
// 2. AREA AUTENTIKASI (Wisnu)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 3. AREA PENGENDARA (Byan & Azka/Aimee)
// ==========================================
Route::prefix('rider')->name('rider.')->middleware('auth')->group(function () {
    
    // Garasi Digital (Byan)
    Route::resource('vehicles', VehicleController::class);
    
    // Peta Lokasi SPKLU (Azka & Aimee) -> Nama rute diperbaiki menjadi map
    Route::get('/peta', [SpkluController::class, 'index'])->name('map');
    
});

// ==========================================
// 4. AREA VENDOR (Fakhri & Riehand)
// ==========================================
$vendorMiddleware = app()->environment('local') ? [] : ['auth'];

// Gunakan satu grup besar dengan prefix dan name yang seragam
Route::prefix('vendor')->name('vendor.')->middleware($vendorMiddleware)->group(function () {
    Route::resource('profile', VendorProfileController::class)->only(['create', 'store', 'show']);
    Route::resource('documents', VendorController::class)->only(['create', 'store', 'show', 'edit', 'update']);
    Route::get('status', [VendorController::class, 'status'])->name('status');

    // Pindahkan rute chargers ke sini agar otomatis memiliki nama 'vendor.chargers.*'
    Route::resource('chargers', ChargerMachineController::class);
    
});

// ==========================================
// 5. AREA ADMIN (Langgeng - PBI 9, 10, 11)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stations', [AdminDashboardController::class, 'stations'])->name('stations');
    
    // Otorisasi Pendaftaran (PBI 10)
    Route::patch('/vendors/{id}/approve', [AdminDashboardController::class, 'approve'])->name('vendors.approve');
    Route::patch('/vendors/{id}/reject', [AdminDashboardController::class, 'reject'])->name('vendors.reject');

    // Manajemen Status Akun (PBI 11)
    Route::patch('/vendors/{id}/suspend', [AdminDashboardController::class, 'suspend'])->name('vendors.suspend');
    Route::patch('/vendors/{id}/activate', [AdminDashboardController::class, 'activate'])->name('vendors.activate');
    
});

// ==========================================
// 6. AREA API (Azka/Aimee)
// ==========================================
// Route untuk mengambil data JSON relasi SPKLU dan Charger
Route::get('/api/spklus', [SpkluController::class, 'getSpkluData']);