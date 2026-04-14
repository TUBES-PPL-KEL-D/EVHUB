<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Area Pengendara
Route::prefix('rider')->group(function () {
    //
});

// Area Vendor
Route::prefix('vendor')->group(function () {
    //
});

// Area Admin
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});