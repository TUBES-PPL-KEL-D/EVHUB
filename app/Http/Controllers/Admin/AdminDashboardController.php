<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Ticket; // Tambahan model Ticket untuk PBI 9
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Menarik data vendor yang menunggu persetujuan (PBI Sprint 1)
        $pendingVendors = Vendor::with('user')->where('status', 'Pending')->get();

        // 2. Menarik data laporan kendala terbaru (Improvement PBI 9 - Sprint 2)
        // Kita ambil 5 tiket terbaru yang statusnya masih 'pending'
        $recentTickets = Ticket::with('user')
            ->where('status', 'pending') 
            ->latest()
            ->take(5)
            ->get();

        // Mengirimkan kedua variabel tersebut ke halaman view dashboard
        return view('admin.dashboard', compact('pendingVendors', 'recentTickets'));
    }

    public function approve($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Approved']);
        return redirect()->route('admin.dashboard')->with('success', "Vendor {$vendor->company_name} telah diaktifkan.");
    }

    public function reject($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Rejected']);
        return redirect()->route('admin.dashboard')->with('success', "Pendaftaran {$vendor->company_name} telah ditolak.");
    }

    public function stations()
    {
        $approvedVendors = Vendor::with('user')->where('status', 'Approved')->get();
        $suspendedVendors = Vendor::with('user')->where('status', 'Suspended')->get();
        $rejectedVendors = Vendor::with('user')->where('status', 'Rejected')->get();
        
        return view('admin.stations', compact('approvedVendors', 'suspendedVendors', 'rejectedVendors'));
    }

    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Suspended']);
        return redirect()->route('admin.stations')->with('success', "Akun vendor {$vendor->company_name} telah dibekukan sementara.");
    }

    public function activate($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Approved']);
        return redirect()->route('admin.stations')->with('success', "Akun vendor {$vendor->company_name} telah diaktifkan kembali.");
    }

    // PBI 12: Delete
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $companyName = $vendor->company_name;

        if ($vendor->legality_document_path) {
            Storage::disk('public')->delete($vendor->legality_document_path);
        }

        $user = $vendor->user;
        $vendor->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.stations')->with('success', "Data vendor $companyName beserta akunnya telah dihapus permanen dari sistem.");
    }
}