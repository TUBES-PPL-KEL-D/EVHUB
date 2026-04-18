<?php

use App\Http\Controllers\VendorProfileController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ChargerMachineController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Area Autentikasi (Wisnu)
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

// Area Pengendara (Byan)
Route::prefix('rider')->middleware('auth')->group(function () {
    Route::resource('vehicles', VehicleController::class);
});

// Area Vendor (Fakhri & Riehand)
$vendorMiddleware = app()->environment('local') ? [] : ['auth'];

Route::prefix('vendor')->name('vendor.')->middleware($vendorMiddleware)->group(function () {
    Route::resource('profile', VendorProfileController::class)->only(['create', 'store', 'show']);
    Route::resource('documents', VendorController::class)->only(['create', 'store', 'show', 'edit', 'update']);
    Route::get('status', [VendorController::class, 'status'])->name('status');
// Area Vendor (Riehand)
Route::prefix('vendor')->group(function () {
    Route::resource('chargers', ChargerMachineController::class);
});

// Area Admin (Langgeng - PBI 9 & PBI 10)
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/stations', [AdminDashboardController::class, 'stations'])->name('admin.stations'); // Rute baru
    Route::patch('/vendors/{id}/approve', [AdminDashboardController::class, 'approve'])->name('admin.vendors.approve');
    Route::patch('/vendors/{id}/reject', [AdminDashboardController::class, 'reject'])->name('admin.vendors.reject');
});