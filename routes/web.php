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
// Penanggung Jawab: Langgeng Yongi Surya (Technical Setup)
// ==========================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ==========================================
// 2. AREA AUTENTIKASI PENGENDARA EV
// Penanggung Jawab: Wisnu Cakra P. P. (PBI 1, 2, 3, 4)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Area Profil juga milik Wisnu (Butuh login untuk akses data spesifik user)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 3. AREA PENGENDARA (RIDER)
// Status Auth: DIMATIKAN SEMENTARA UNTUK TESTING
// ==========================================
Route::prefix('rider')->name('rider.')->group(function () {
    
    // MODUL GARASI DIGITAL
    // Penanggung Jawab: M. Byan Burika (PBI 19, 20, 21, 22)
    Route::resource('vehicles', VehicleController::class);
    
    // MODUL PEMETAAN LOKASI SPKLU
    // Penanggung Jawab: M. Azka As-Sidqi & Aimee Clarissa A. S. (PBI 23, 24, 25, 26)
    Route::get('/peta', [SpkluController::class, 'index'])->name('map');
    
});

// ==========================================
// 4. AREA VENDOR (MITRA SPKLU)
// Status Auth: DIMATIKAN SEMENTARA UNTUK TESTING
// ==========================================
Route::prefix('vendor')->name('vendor.')->group(function () {
    
    // MODUL PENDAFTARAN MITRA VENDOR
    // Penanggung Jawab: Fakhri M. Habibi (PBI 5, 6, 7, 8)
    Route::resource('profile', VendorProfileController::class)->only(['create', 'store', 'show']);
    Route::resource('documents', VendorController::class)->only(['create', 'store', 'show', 'edit', 'update']);
    Route::get('status', [VendorController::class, 'status'])->name('status');
    
    // MODUL MANAJEMEN MESIN CHARGER
    // Penanggung Jawab: Riehand Muhammad (PBI 15, 16, 17, 18)
    Route::resource('chargers', ChargerMachineController::class);
    
});

// ==========================================
// 5. AREA PANEL ADMIN & VERIFIKASI
// Penanggung Jawab: Langgeng Yongi Surya (PBI 9, 10, 11, 12)
// Status Auth: DIMATIKAN SEMENTARA UNTUK TESTING
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Read: Tampilan Dashboard Antrean & Riwayat
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stations', [AdminDashboardController::class, 'stations'])->name('stations');
    
    // Update: Otorisasi Pendaftaran (Approve/Reject)
    Route::patch('/vendors/{id}/approve', [AdminDashboardController::class, 'approve'])->name('vendors.approve');
    Route::patch('/vendors/{id}/reject', [AdminDashboardController::class, 'reject'])->name('vendors.reject');

    // Update: Manajemen Status Akun Aktif (Suspend/Activate)
    Route::patch('/vendors/{id}/suspend', [AdminDashboardController::class, 'suspend'])->name('vendors.suspend');
    Route::patch('/vendors/{id}/activate', [AdminDashboardController::class, 'activate'])->name('vendors.activate');
    
    // Delete: Hapus Permanen Akun Pelanggar/Ditolak
    Route::delete('/vendors/{id}/destroy', [AdminDashboardController::class, 'destroy'])->name('vendors.destroy');
});

// ==========================================
// 6. AREA API (LAYANAN DATA FRONTEND)
// Penanggung Jawab: M. Azka As-Sidqi & Aimee Clarissa A. S. (PBI 27, 28, 30)
// ==========================================
// Mengambil data titik koordinat dan list charger untuk dirender di Peta Digital
Route::get('/api/spklus', [SpkluController::class, 'getSpkluData']);