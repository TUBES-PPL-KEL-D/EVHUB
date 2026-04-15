<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Mengambil vendor dengan status Pending dan memuat relasi user (untuk mengambil email)
        $pendingVendors = Vendor::with('user')->where('status', 'Pending')->get();
        
        return view('admin.dashboard', compact('pendingVendors'));
    }
}