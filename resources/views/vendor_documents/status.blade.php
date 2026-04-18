@extends('layouts.app')

@section('title', 'Status Pendaftaran Vendor')

@section('content')
    @php
        $statusStyles = [
            'Pending' => 'bg-amber-100 text-amber-800',
            'Approved' => 'bg-emerald-100 text-emerald-800',
            'Rejected' => 'bg-red-100 text-red-800',
            'Suspended' => 'bg-slate-200 text-slate-800',
        ];
        $statusClass = $statusStyles[$vendor->status] ?? 'bg-slate-100 text-slate-700';
        $isApproved = $vendor->status === 'Approved';
        $isRejected = $vendor->status === 'Rejected';
    @endphp

    <div class="mx-auto max-w-4xl">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-gradient-to-r from-cyan-700 to-sky-600 px-6 py-6 text-white">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-sky-100">PBI 3</p>
                <h1 class="mt-2 text-3xl font-bold">Status Pendaftaran Vendor</h1>
                <p class="mt-2 text-sky-50">Pantau status pendaftaran perusahaan Anda secara real-time.</p>
            </div>

            <div class="space-y-6 px-6 py-6">
                <div class="rounded-2xl bg-slate-50 p-5">
                    <p class="text-sm font-medium text-slate-500">Nama Perusahaan</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendor->company_name }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <p class="text-sm font-medium text-slate-500">Status Saat Ini</p>
                    <p class="mt-2 inline-flex rounded-full px-4 py-1.5 text-sm font-semibold {{ $statusClass }}">{{ $vendor->status }}</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    @if ($isApproved)
                        <p class="text-base font-semibold text-emerald-700">Pendaftaran Anda sudah disetujui.</p>
                        <p class="mt-2 text-sm text-slate-600">Vendor dapat melanjutkan ke proses operasional berikutnya di EV-HUB.</p>
                    @elseif ($isRejected)
                        <p class="text-base font-semibold text-red-700">Dokumen legalitas Anda ditolak.</p>
                        <p class="mt-2 text-sm text-slate-600">Silakan perbaiki dokumen dan unggah ulang melalui tombol perbaikan.</p>
                    @else
                        <p class="text-base font-semibold text-amber-700">Pendaftaran Anda masih dalam proses review.</p>
                        <p class="mt-2 text-sm text-slate-600">Status akan berubah menjadi Approved setelah verifikasi admin selesai.</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">Terakhir diperbarui: {{ $vendor->updated_at->format('d M Y H:i') }}</p>
                <div class="flex gap-3">
                    @if ($isRejected)
                        <a href="{{ route('vendor.documents.edit', $vendor) }}" class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Perbaiki & Upload Ulang</a>
                    @endif
                    <a href="{{ route('vendor.documents.show', $vendor) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Detail Dokumen</a>
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
@endsection