@extends('layouts.app')

@section('title', 'Pendaftaran Vendor')

@php
	$profile = $vendorProfile ?? null;
@endphp

@section('content')
	<div class="mx-auto max-w-4xl">
		<div class="mb-6">
			<h1 class="mt-2 text-3xl font-bold text-slate-900">Isi Profil Entitas Perusahaan Vendor</h1>
			<p class="mt-2 text-slate-600">Lengkapi data perusahaan untuk memulai pendaftaran vendor baru di EV-HUB.</p>
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
				<h2 class="text-lg font-semibold text-slate-900">{{ $profile ? 'Perbaiki Profil Vendor' : 'Form Profil Vendor' }}</h2>
			</div>

			<form action="{{ route('vendor.profile.store') }}" method="POST" class="space-y-6 px-6 py-6">
				@csrf

				<div class="grid gap-6 md:grid-cols-2">
					<div class="md:col-span-2">
						<label for="company_name" class="mb-2 block text-sm font-medium text-slate-700">Nama Perusahaan</label>
						<input
							type="text"
							name="company_name"
							id="company_name"
							value="{{ old('company_name', $profile?->company_name) }}"
							class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
							placeholder="Contoh: PT Energi Hijau Nusantara"
							required
						>
					</div>

					<div>
						<label for="company_email" class="mb-2 block text-sm font-medium text-slate-700">Email Perusahaan</label>
						<input
							type="email"
							name="company_email"
							id="company_email"
							value="{{ old('company_email', $profile?->company_email) }}"
							class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
							placeholder="vendor@perusahaan.com"
						>
					</div>

					<div>
						<label for="company_phone" class="mb-2 block text-sm font-medium text-slate-700">Nomor Telepon</label>
						<input
							type="text"
							name="company_phone"
							id="company_phone"
							value="{{ old('company_phone', $profile?->company_phone) }}"
							class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
							placeholder="08xxxxxxxxxx"
						>
					</div>

					<div class="md:col-span-2">
						<label for="company_address" class="mb-2 block text-sm font-medium text-slate-700">Alamat Perusahaan</label>
						<textarea
							name="company_address"
							id="company_address"
							rows="4"
							class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
							placeholder="Masukkan alamat lengkap perusahaan"
							required
						>{{ old('company_address', $profile?->company_address) }}</textarea>
					</div>

					<div class="md:col-span-2">
						<label for="company_description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Perusahaan</label>
						<textarea
							name="company_description"
							id="company_description"
							rows="4"
							class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
							placeholder="Jelaskan singkat bidang usaha atau layanan perusahaan"
						>{{ old('company_description', $profile?->company_description) }}</textarea>
					</div>

				</div>

				<div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
					<p class="text-sm text-slate-500">Data ini akan menjadi profil dasar vendor baru.</p>
					<div class="flex gap-3">
						<a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
						<button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">{{ $profile ? 'Simpan Perbaikan Profil' : 'Simpan Profil' }}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
