@extends('layouts.app')

@section('title', 'Status Legalitas Vendor')

@section('content')
    <div class="mx-auto max-w-4xl">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-6 text-white">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-emerald-100">Status PBI 2</p>
                <h1 class="mt-2 text-3xl font-bold">Dokumen Legalitas Vendor</h1>
                <p class="mt-2 text-emerald-50">Dokumen berhasil tersimpan dan sedang menunggu proses berikutnya.</p>
            </div>

            <div class="grid gap-6 px-6 py-6 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-5">
                    <p class="text-sm font-medium text-slate-500">Nama Perusahaan</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendor->company_name }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <p class="text-sm font-medium text-slate-500">Status Registrasi</p>
                    <p class="mt-1 inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800">{{ $vendor->status }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5 md:col-span-2">
                    <p class="text-sm font-medium text-slate-500">File Dokumen</p>
                    <p class="mt-1 text-slate-900">{{ basename($vendor->legality_document_path) }}</p>
                    <a href="{{ asset('storage/' . $vendor->legality_document_path) }}" target="_blank" class="mt-3 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Lihat Dokumen</a>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">Vendor dapat melanjutkan ke proses review berikutnya.</p>
                <a href="{{ route('vendor.documents.create') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Upload Ulang</a>
            </div>
        </div>
    </div>
@endsection