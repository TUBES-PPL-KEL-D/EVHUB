<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Mengambil data berdasarkan masing-masing status
        $pendingVendors = Vendor::with('user')->where('status', 'Pending')->get();
        $approvedVendors = Vendor::with('user')->where('status', 'Approved')->get();
        $rejectedVendors = Vendor::with('user')->where('status', 'Rejected')->get();
        
        // Mengirim ketiga kelompok data tersebut ke View
        return view('admin.dashboard', compact('pendingVendors', 'approvedVendors', 'rejectedVendors'));
    }

    public function approve($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Approved']);
        
        return redirect()->route('admin.dashboard')
            ->with('success', "Pendaftaran vendor {$vendor->company_name} berhasil disetujui.");
    }

    public function reject($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Rejected']);
        
        return redirect()->route('admin.dashboard')
            ->with('success', "Pendaftaran vendor {$vendor->company_name} telah ditolak.");
    }

    public function stations()
    {
        $approvedVendors = Vendor::with('user')->where('status', 'Approved')->get();
        $rejectedVendors = Vendor::with('user')->where('status', 'Rejected')->get();
        
        return view('admin.stations', compact('approvedVendors', 'rejectedVendors'));
    }
}