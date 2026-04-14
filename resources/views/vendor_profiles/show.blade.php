@extends('layouts.app')

@section('title', 'Profil Vendor')

@section('content')
	<div class="mx-auto max-w-4xl">
		@if (session('success'))
			<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
				{{ session('success') }}
			</div>
		@endif

		<div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
			<div class="border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-6 text-white">
				<p class="text-sm font-medium uppercase tracking-[0.3em] text-emerald-100">Profil Vendor</p>
				<h1 class="mt-2 text-3xl font-bold">{{ $vendorProfile->company_name }}</h1>
				<p class="mt-2 text-emerald-50">Ringkasan profil perusahaan yang berhasil disimpan untuk pendaftaran vendor.</p>
			</div>

			<div class="grid gap-6 px-6 py-6 md:grid-cols-2">
				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-medium text-slate-500">Nama Perusahaan</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendorProfile->company_name }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-medium text-slate-500">Email Perusahaan</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendorProfile->company_email ?? '-' }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-medium text-slate-500">Nomor Telepon</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendorProfile->company_phone ?? '-' }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5 md:col-span-2">
					<p class="text-sm font-medium text-slate-500">Alamat Perusahaan</p>
					<p class="mt-1 whitespace-pre-line text-lg font-semibold text-slate-900">{{ $vendorProfile->company_address }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5 md:col-span-2">
					<p class="text-sm font-medium text-slate-500">Deskripsi Perusahaan</p>
					<p class="mt-1 whitespace-pre-line text-slate-700">{{ $vendorProfile->company_description ?? 'Belum ada deskripsi.' }}</p>
				</div>
			</div>

			<div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
				<p class="text-sm text-slate-500">Profil vendor PBI 1 sudah tersimpan.</p>
				<a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Kembali ke Beranda</a>
			</div>
		</div>
	</div>
@endsection
