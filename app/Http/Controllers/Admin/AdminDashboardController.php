<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Ticket;
use App\Models\VendorWarning; // Model baru untuk PBI 35
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    // ... (fungsi index, approve, dan reject biarkan persis seperti sebelumnya) ...
    public function index()
    {
        $pendingVendors = Vendor::with('user')->where('status', 'Pending')->get();
        $recentTickets = Ticket::with('user')->where('status', 'pending')->latest()->take(5)->get();
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

    // --- REVISI UNTUK PBI 35 ---
    public function stations()
    {
        // Tambahkan withCount('warnings') agar kita bisa menampilkan jumlah pelanggaran di UI
        $approvedVendors = Vendor::with('user')->withCount('warnings')->where('status', 'Approved')->get();
        $suspendedVendors = Vendor::with('user')->where('status', 'Suspended')->get();
        $rejectedVendors = Vendor::with('user')->where('status', 'Rejected')->get();
        
        return view('admin.stations', compact('approvedVendors', 'suspendedVendors', 'rejectedVendors'));
    }

    // FITUR BARU PBI 35: Kirim Peringatan dan Otomatis Suspend
    public function sendWarning(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:255']);
        $vendor = Vendor::findOrFail($id);

        // 1. Catat peringatan ke database
        VendorWarning::create([
            'vendor_id' => $vendor->id,
            'message' => $request->message
        ]);

        // 2. Hitung total peringatan
        $totalWarnings = $vendor->warnings()->count();

        // 3. Logika Otomatis: Jika peringatan mencapai 3, langsung Suspend
        if ($totalWarnings >= 3) {
            $vendor->update(['status' => 'Suspended']);
            return redirect()->route('admin.stations')->with('success', "Peringatan ke-3 dikirim. Sistem secara OTOMATIS membekukan akun {$vendor->company_name} karena batas pelanggaran.");
        }

        return redirect()->route('admin.stations')->with('success', "Surat Peringatan berhasil dikirim ke {$vendor->company_name}. Total pelanggaran saat ini: $totalWarnings/3.");
    }

    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Suspended']);
        return redirect()->route('admin.stations')->with('success', "Akun vendor {$vendor->company_name} telah dibekukan sementara.");
    }

    public function activate($id)
    {
        // Jika diaktifkan kembali, kita bisa mereset (menghapus) riwayat peringatannya agar bersih
        $vendor = Vendor::findOrFail($id);
        $vendor->warnings()->delete(); 
        $vendor->update(['status' => 'Approved']);
        
        return redirect()->route('admin.stations')->with('success', "Akun vendor {$vendor->company_name} diaktifkan kembali dan riwayat pelanggaran diputihkan.");
    }

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