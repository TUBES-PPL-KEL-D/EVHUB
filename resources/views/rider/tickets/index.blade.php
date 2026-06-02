@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-100 tracking-tight flex items-center gap-3">
            <div class="p-2 bg-emerald-500/10 text-emerald-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            Pusat Bantuan
        </h1>
        <p class="text-slate-400 font-medium mt-2">Sampaikan laporan kendala atau keluhan Anda terkait fasilitas SPKLU.</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-xl flex items-center space-x-3 mb-6">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-slate-900/50 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6">Buat Laporan Baru</h2>
        <form action="{{ route('rider.tickets.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="subject" class="block text-sm font-bold text-slate-300 mb-2">Subjek Laporan</label>
                <input type="text" id="subject" name="subject" required placeholder="Contoh: Mesin di SPKLU Merdeka tidak bisa di scan" class="w-full bg-slate-950/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
            </div>
            <div>
                <label for="description" class="block text-sm font-bold text-slate-300 mb-2">Deskripsi Detail</label>
                <textarea id="description" name="description" rows="4" required placeholder="Jelaskan kendala Anda secara rinci..." class="w-full bg-slate-950/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors"></textarea>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-black px-8 py-3 rounded-xl uppercase tracking-widest text-sm transition-all shadow-lg shadow-emerald-500/20">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    <div class="bg-slate-900/50 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6">Riwayat Laporan Saya</h2>
        
        @if($tickets->isEmpty())
            <div class="text-center py-10 border border-dashed border-slate-700 rounded-2xl">
                <p class="text-slate-500 font-medium">Anda belum pernah membuat laporan.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($tickets as $ticket)
                    <div class="bg-slate-800/40 border border-slate-700/40 rounded-2xl p-5 hover:bg-slate-800/60 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-white">{{ $ticket->subject }}</h3>
                                <p class="text-xs text-slate-500 mt-1 font-mono">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                @if($ticket->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Menunggu
                                    </span>
                                @elseif($ticket->status === 'processing')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        Diproses
                                    </span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        {{ $ticket->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ $ticket->description }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
