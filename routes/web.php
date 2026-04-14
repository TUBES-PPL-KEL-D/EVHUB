<?php

use App\Http\Controllers\VendorProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome'); // Base map SPKLU akan dirender di sini
});

// Area Pengendara (Byan & Wisnu)
Route::prefix('rider')->group(function () {
    // Rute Garasi Digital & Profil masuk di sini
});

// Area Vendor (Fakhri & Riehand)
Route::prefix('vendor')->name('vendor.')->middleware('auth')->group(function () {
    Route::resource('profile', VendorProfileController::class)->only(['create', 'store', 'show']);
});

// Area Admin (Langgeng Yongi S.)
Route::prefix('admin')->group(function () {
    // Rute Verifikasi Vendor & Panel Manajemen Pengguna masuk di sini
});