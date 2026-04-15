<?php

use App\Http\Controllers\VendorProfileController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Area Pengendara
Route::prefix('rider')->group(function () {
    //
});

// Area Vendor (Fakhri & Riehand)
$vendorMiddleware = app()->environment('local') ? [] : ['auth'];

Route::prefix('vendor')->name('vendor.')->middleware($vendorMiddleware)->group(function () {
    Route::resource('profile', VendorProfileController::class)->only(['create', 'store', 'show']);
    Route::resource('documents', VendorController::class)->only(['create', 'store', 'show', 'edit', 'update']);
    Route::get('status', [VendorController::class, 'status'])->name('status');
});

// Area Admin
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});