@extends('layouts.app')

@section('title', 'Upload Legalitas Vendor')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Upload Dokumen Legalitas Vendor</h1>
            <p class="mt-2 text-slate-600">Unggah dokumen legalitas untuk melanjutkan proses pendaftaran vendor.</p>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

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
                <h2 class="text-lg font-semibold text-slate-900">Form Upload Legalitas</h2>
                <p class="mt-1 text-sm text-slate-500">Perusahaan: {{ $vendorProfile->company_name }}</p>
            </div>

            <form action="{{ route('vendor.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 px-6 py-6">
                @csrf

                <div class="rounded-2xl bg-emerald-50 p-4 text-sm text-emerald-800">
                    Format yang diterima: PDF, JPG, JPEG, PNG. Ukuran maksimal 5 MB.
                </div>

                <div>
                    <label for="legality_document" class="mb-2 block text-sm font-medium text-slate-700">Dokumen Legalitas</label>
                    <input
                        type="file"
                        name="legality_document"
                        id="legality_document"
                        class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-700"
                        required
                    >
                    <p class="mt-2 text-sm text-slate-500">Contoh: SK, NIB, NPWP, atau surat legalitas lain sesuai kebutuhan PBI.</p>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Dokumen ini akan dipakai untuk verifikasi vendor.</p>
                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Upload Dokumen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection