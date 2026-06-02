@extends('layouts.app')
@section('title', 'Galeri SPKLU')

@section('content')
<div class="space-y-6 animate-fade-in-up pb-10">

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <div class="bg-emerald-500 p-1.5 rounded-full text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-500/10 border border-rose-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <div class="bg-rose-500 p-1.5 rounded-full text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        <div class="text-sm text-rose-400 font-bold">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Media Stasiun</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Galeri Foto SPKLU</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola foto-foto stasiun <span class="font-bold text-slate-200">{{ $spklu->name }}</span> yang akan ditampilkan pada peta pengendara.</p>
        </div>
        <a href="{{ route('vendor.chargers.index') }}" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-colors shrink-0">
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-1 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl">
            <h2 class="text-lg font-bold text-white mb-1">Unggah Foto Baru</h2>
            <p class="text-xs text-slate-400 mb-5">Tambahkan sudut foto baru untuk stasiun ini.</p>

            <form action="{{ route('vendor.spklu.gallery.store', $spklu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Pilih Berkas Gambar</label>
                    <input type="file" name="photo" accept="image/jpeg, image/png, image/jpg" required class="w-full px-4 py-2 text-sm border border-slate-700 rounded-xl bg-slate-950 text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 focus:outline-none transition-colors cursor-pointer">
                    <p class="mt-2 text-[10px] text-slate-500 font-medium">Format: JPG, JPEG, PNG. Maks: 5MB.</p>
                </div>
                
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-900 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/10 mt-2">
                    Unggah & Simpan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <h2 class="text-lg font-bold text-white mb-1">Etalase Galeri Saat Ini</h2>
            <p class="text-xs text-slate-400 mb-5">Foto-foto ini dapat digeser (swipe) oleh pengendara saat membuka detail lokasi di peta.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @forelse($photos as $photo)
                    <div class="group relative rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-md aspect-square">
                        <img src="{{ asset('storage/' . $photo->image_path) }}" alt="Foto Stasiun" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        
                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 backdrop-blur-sm">
                            <a href="{{ asset('storage/' . $photo->image_path) }}" target="_blank" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-slate-700 transition-colors">
                                Lihat Penuh
                            </a>
                            <form action="{{ route('vendor.spklu.gallery.destroy', [$spklu->id, $photo->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus foto ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center border border-dashed border-slate-700/60 rounded-2xl">
                        <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada foto yang diunggah.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection