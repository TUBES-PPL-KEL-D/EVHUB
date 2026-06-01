@extends('layouts.app') 
@section('title', 'Manajemen Stasiun & Vendor') 

@section('content') 
<div class="space-y-10 animate-fade-in-up"> 
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4"> 
        <div> 
            <h1 class="text-4xl font-extrabold text-white tracking-tight">Manajemen <span class="text-emerald-500">Stasiun</span></h1> 
            <p class="text-slate-400 font-medium mt-2">Kontrol operasional dan status kemitraan vendor SPKLU.</p> 
        </div> 
        <div> 
            <a href="{{ route('admin.stations.export') }}" class="inline-flex items-center bg-emerald-500 text-white px-5 py-2.5 rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/30"> 
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
                Export Excel 
            </a> 
        </div> 
    </div> 

    @if(session('success')) 
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm"> 
        <div class="bg-emerald-500 p-1.5 rounded-full text-white"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> 
        </div> 
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p> 
    </div> 
    @endif 

    <div class="grid grid-cols-1 gap-10"> 
        <div class="bg-slate-800/40 border border-slate-700/50 rounded-[2.5rem] p-8 backdrop-blur-sm shadow-xl"> 
            <div class="flex items-center justify-between mb-8"> 
                <div class="flex items-center space-x-3"> 
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-400"> 
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
                    </div> 
                    <h2 class="text-xl font-extrabold text-white tracking-tight">Vendor Aktif</h2> 
                </div> 
            </div> 

            <div class="space-y-4"> 
                @forelse($approvedVendors as $vendor) 
                <div class="bg-slate-800/50 hover:bg-slate-700/40 transition-all border border-slate-700/50 p-5 rounded-3xl flex flex-col xl:flex-row justify-between items-center gap-4"> 
                    <div class="flex items-center space-x-4 w-full xl:w-auto"> 
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center font-black text-emerald-400 text-lg flex-shrink-0"> 
                            {{ substr($vendor->company_name, 0, 1) }} 
                        </div> 
                        <div> 
                            <div class="font-extrabold text-white text-lg">{{ $vendor->company_name }}</div> 
                            <div class="flex items-center space-x-3 mt-1"> 
                                <span class="text-xs text-slate-400 font-bold tracking-wide uppercase">{{ $vendor->user->email ?? '-' }}</span> 
                                @if(isset($vendor->warnings_count) && $vendor->warnings_count > 0) 
                                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] px-2 py-0.5 rounded-full font-bold"> 
                                        {{ $vendor->warnings_count }}/3 WARNING 
                                    </span> 
                                @endif 
                            </div> 
                        </div> 
                    </div> 

                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full xl:w-auto"> 
                        <form action="{{ route('admin.vendors.warning', $vendor->id) }}" method="POST" class="flex w-full sm:w-auto shadow-sm"> 
                            @csrf 
                            <input type="text" name="message" placeholder="Alasan peringatan..." required class="text-xs px-3 py-2 border border-slate-600 rounded-l-2xl focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 w-full sm:w-48 bg-slate-900/50 text-white placeholder-slate-500"> 
                            <button type="submit" class="bg-amber-500 text-white px-4 py-2.5 rounded-r-2xl text-xs font-black uppercase tracking-widest hover:bg-amber-600 transition-colors">WARN</button> 
                        </form> 

                        <form action="{{ route('admin.vendors.suspend', $vendor->id) }}" method="POST" class="w-full sm:w-auto"> 
                            @csrf 
                            @method('PATCH') 
                            <button type="submit" class="bg-rose-500/10 text-rose-400 px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white border border-rose-500/20 transition-colors w-full h-full" onclick="return confirm('Apakah Anda yakin ingin membekukan vendor ini secara paksa?')">SUSPEND</button> 
                        </form> 
                    </div> 
                </div> 
                @empty 
                <div class="text-center py-10 bg-slate-800/50 rounded-3xl border border-dashed border-slate-600 text-slate-500 font-bold uppercase tracking-widest text-xs">Belum ada vendor aktif</div> 
                @endforelse 
            </div> 
        </div> 

        <div class="bg-slate-800/40 border border-slate-700/50 rounded-[2.5rem] p-8 backdrop-blur-sm shadow-xl"> 
            <div class="flex items-center space-x-3 mb-8"> 
                <div class="w-10 h-10 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-400"> 
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> 
                </div> 
                <h2 class="text-xl font-extrabold text-white tracking-tight">Daftar Suspend</h2> 
            </div> 
            <div class="space-y-4"> 
                @forelse($suspendedVendors as $vendor) 
                <div class="bg-rose-900/10 border border-rose-500/20 p-5 rounded-3xl flex flex-col sm:flex-row justify-between items-center gap-4"> 
                    <div class="flex items-center space-x-4"> 
                        <div class="w-12 h-12 bg-rose-500/20 text-rose-400 rounded-2xl flex items-center justify-center font-black text-lg">!</div> 
                        <div> 
                            <div class="font-extrabold text-white text-lg italic">{{ $vendor->company_name }}</div> 
                            <div class="text-xs text-rose-400 font-black tracking-widest uppercase">Akun Ditangguhkan</div> 
                        </div> 
                    </div> 
                    <div class="flex space-x-2"> 
                        <form action="{{ route('admin.vendors.activate', $vendor->id) }}" method="POST"> 
                            @csrf 
                            @method('PATCH') 
                            <button type="submit" class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-colors">RE_ACTIVATE</button> 
                        </form> 
                        <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Hapus PERMANEN vendor ini? Data dan file legalitas akan musnah.');"> 
                            @csrf 
                            @method('DELETE') 
                            <button type="submit" class="bg-slate-900 text-white px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 transition-colors shadow-lg">HAPUS</button> 
                        </form> 
                    </div> 
                </div> 
                @empty 
                <div class="text-center py-10 bg-slate-800/50 rounded-3xl border border-dashed border-slate-600 text-slate-500 font-bold uppercase tracking-widest text-xs">Tidak ada akun yang dibekukan</div> 
                @endforelse 
            </div> 
        </div> 

        <div class="bg-slate-800/40 border border-slate-700/50 rounded-[2.5rem] p-8 backdrop-blur-sm shadow-xl"> 
            <div class="flex items-center space-x-3 mb-8"> 
                <div class="w-10 h-10 bg-slate-700/50 rounded-2xl flex items-center justify-center text-slate-400"> 
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> 
                </div> 
                <h2 class="text-xl font-extrabold text-white tracking-tight">Riwayat Penolakan</h2> 
            </div> 
            <div class="space-y-4"> 
                @forelse($rejectedVendors as $vendor) 
                <div class="bg-slate-800/50 border border-slate-700/50 p-5 rounded-3xl flex flex-col sm:flex-row justify-between items-center gap-4 hover:bg-slate-700/40 transition-colors"> 
                    <div> 
                        <div class="font-extrabold text-white text-lg">{{ $vendor->company_name }}</div> 
                        <div class="text-xs text-slate-400 font-bold tracking-wide uppercase">{{ $vendor->user->email ?? '-' }}</div> 
                    </div> 
                    <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Bersihkan data pendaftaran yang ditolak ini dari database?');"> 
                        @csrf 
                        @method('DELETE') 
                        <button type="submit" class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-colors">HAPUS PERMANEN</button> 
                    </form> 
                </div> 
                @empty 
                <div class="text-center py-10 bg-slate-800/50 rounded-3xl border border-dashed border-slate-600 text-slate-500 font-bold uppercase tracking-widest text-xs">Belum ada riwayat penolakan</div> 
                @endforelse 
            </div> 
        </div> 
    </div> 
</div> 
@endsection