@extends('layouts.app')

@section('title', 'Perbaikan Dokumen Legalitas')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-red-600">PBI 4</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Perbaiki & Unggah Ulang Dokumen</h1>
            <p class="mt-2 text-slate-600">Status dokumen Anda ditolak. Silakan unggah dokumen legalitas yang sudah diperbaiki.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Ada kesalahan pada form:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Form Perbaikan Dokumen</h2>
                <p class="mt-1 text-sm text-slate-500">Perusahaan: {{ $vendor->company_name }}</p>
            </div>

            <form action="{{ route('vendor.documents.update', $vendor) }}" method="POST" enctype="multipart/form-data" class="space-y-6 px-6 py-6">
                @csrf
                @method('PUT')

                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Pastikan dokumen legalitas terbaru jelas terbaca dan sesuai ketentuan verifikasi.
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                    <p class="font-semibold text-slate-800">Dokumen saat ini</p>
                    <p class="mt-1">{{ basename($vendor->legality_document_path) }}</p>
                </div>

                <div>
                    <label for="legality_document" class="mb-2 block text-sm font-medium text-slate-700">Dokumen Legalitas Baru</label>
                    <input
                        type="file"
                        name="legality_document"
                        id="legality_document"
                        class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 file:mr-4 file:rounded-xl file:border-0 file:bg-red-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-red-700"
                        required
                    >
                    <p class="mt-2 text-sm text-slate-500">Format: PDF, JPG, JPEG, PNG. Maksimal 5 MB.</p>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Setelah upload ulang, status akan kembali menjadi Pending.</p>
                    <div class="flex gap-3">
                        <a href="{{ route('vendor.status') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Simpan Perbaikan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection