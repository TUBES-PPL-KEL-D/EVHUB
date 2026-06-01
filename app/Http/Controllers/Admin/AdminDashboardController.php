<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Ticket;
use App\Models\VendorWarning;
use App\Models\Spklu; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SpkluExport;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));

        // Data Antrean & Laporan Masuk
        $pendingVendors = Vendor::with('user')->where('status', 'Pending')->get();
        $recentTickets = Ticket::with('user')->where('status', 'pending')->latest()->take(5)->get();

        // Data Kelompok Vendor Berdasarkan Status
        $approvedVendors = Vendor::with(['user', 'warnings'])->withCount('warnings')->where('status', 'Approved')->get();
        $suspendedVendors = Vendor::with('user')->where('status', 'Suspended')->get();
        $rejectedVendors = Vendor::with('user')->where('status', 'Rejected')->get();

        // Pemrosesan Kurva Analitik Pertumbuhan SPKLU
        $spkluGrowth = Spklu::select(
            DB::raw('count(id) as total'),
            DB::raw('MONTH(created_at) as month')
        )
        ->whereYear('created_at', $selectedYear)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $chartLabels = [];
        $chartData = [];

        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = Carbon::create()->month($i)->translatedFormat('F');
            $chartData[$i] = 0;
        }

        foreach ($spkluGrowth as $growth) {
            $chartData[$growth->month] = $growth->total;
        }

        $chartData = array_values($chartData);

        return view('admin.dashboard', compact(
            'pendingVendors', 
            'recentTickets', 
            'approvedVendors', 
            'suspendedVendors', 
            'rejectedVendors', 
            'chartLabels', 
            'chartData', 
            'selectedYear'
        ));
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

    public function sendWarning(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:255']);
        $vendor = Vendor::findOrFail($id);

        VendorWarning::create([
            'vendor_id' => $vendor->id,
            'message' => $request->message
        ]);

        $totalWarnings = $vendor->warnings()->count();

        if ($totalWarnings >= 3) {
            $vendor->update(['status' => 'Suspended']);
            return redirect()->route('admin.dashboard')->with('success', "Peringatan ke-3 dikirim. Sistem secara OTOMATIS membekukan akun {$vendor->company_name}.");
        }

        return redirect()->route('admin.dashboard')->with('success', "Surat Peringatan berhasil dikirim ke {$vendor->company_name}. Total pelanggaran: $totalWarnings/3.");
    }

    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'Suspended']);
        return redirect()->route('admin.dashboard')->with('success', "Akun vendor {$vendor->company_name} telah dibekukan sementara.");
    }

    public function activate($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->warnings()->delete(); 
        $vendor->update(['status' => 'Approved']);
        return redirect()->route('admin.dashboard')->with('success', "Akun vendor {$vendor->company_name} diaktifkan kembali.");
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

        return redirect()->route('admin.dashboard')->with('success', "Data vendor $companyName telah dihapus permanen.");
    }

    public function exportSpklu()
    {
        return Excel::download(new SpkluExport, 'Rekap_Audit_SPKLU_EVHUB.xlsx');
    }
}