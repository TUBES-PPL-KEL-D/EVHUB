<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ChargerMachineController;

Route::get('/', function () {
    return view('welcome');
});

// Area Pengendara
Route::prefix('rider')->middleware('auth')->group(function () {
    Route::resource('vehicles', VehicleController::class);
});

// Area Vendor
Route::prefix('vendor')->group(function () {
    Route::resource('chargers', ChargerMachineController::class);
});

// Area Admin
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});
