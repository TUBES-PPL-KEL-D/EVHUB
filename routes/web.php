<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SpkluController;

Route::get('/', function () {
    return view('welcome');
});

// Area Pengendara
Route::prefix('rider')->middleware('auth')->group(function () {
    Route::resource('vehicles', VehicleController::class);
    Route::get('/peta', [SpkluController::class, 'index'])->name('welcome');
});

// Area Vendor
Route::prefix('vendor')->group(function () {
    //
});

// Area Admin
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});







