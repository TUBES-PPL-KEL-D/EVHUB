@extends('layouts.app')

@section('title', 'Galeri Foto SPKLU')

@section('content')
<div class="vendor-scope">
    <div class="mx-auto max-w-7xl space-y-6">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold mb-2">Terjadi kesalahan input:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-700">Kelola Galeri SPKLU</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $spklu->name }}</h1>
                    <p class="mt-2 text-sm text-slate-500">Upload foto kondisi fisik stasiun agar pengendara dapat melihatnya sebelum datang.</p>
                </div>
                <a href="{{ route('vendor.chargers.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kembali ke Daftar Mesin</a>
            </div>

            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5">
                <form action="{{ route('vendor.spklu.gallery.store', $spklu) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Pilih Foto</label>
                        <input type="file" name="photos[]" multiple accept="image/*" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <p class="mt-2 text-xs text-slate-500">Format yang didukung: JPG, JPEG, PNG, WEBP. Maksimal 5 MB per file.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Keterangan Foto</label>
                        <div class="space-y-3">
                            <input type="text" name="captions[]" placeholder="Contoh: Tampak depan SPKLU" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                            <input type="text" name="captions[]" placeholder="Contoh: Area parkir dan akses masuk" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                            <input type="text" name="captions[]" placeholder="Tambahan keterangan foto lain jika perlu" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">Unggah Foto</button>
                </form>
            </div>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Daftar Foto</h2>
                    <p class="mt-1 text-sm text-slate-500">Foto yang tampil di halaman detail SPKLU pengendara.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-slate-600">{{ $photos->count() }} Foto</span>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($photos as $photo)
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-sm">
                        <div class="h-52 bg-slate-100">
                            <img src="{{ asset('storage/' . $photo->image_path) }}" alt="{{ $photo->caption ?? $spklu->name }}" class="h-full w-full object-cover">
                        </div>
                        <div class="space-y-3 p-4">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $photo->caption ?? 'Tanpa keterangan' }}</p>
                                <p class="mt-1 text-xs text-slate-500">Urutan: {{ $photo->sort_order }}</p>
                            </div>
                            <form action="{{ route('vendor.spklu.gallery.destroy', [$spklu, $photo]) }}" method="POST" onsubmit="return confirm('Hapus foto ini dari galeri?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500 sm:col-span-2 xl:col-span-3">
                        Belum ada foto galeri untuk SPKLU ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection