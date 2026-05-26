@extends('layouts.app')

@section('title', 'Profil Vendor')

@section('content')
	<div class="vendor-scope" style="color: #0f172a;">
		<div class="mx-auto max-w-4xl">
		@if (session('success'))
			<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
				{{ session('success') }}
			</div>
		@endif

		<div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
			<div class="border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-6 text-slate-900">
				<p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-700">Profil Vendor</p>
				<h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $vendorProfile->company_name }}</h1>
				<p class="mt-2 text-slate-700">Ringkasan profil perusahaan yang berhasil disimpan untuk pendaftaran vendor.</p>
			</div>

			<div class="grid gap-6 px-6 py-6 md:grid-cols-2">
				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-semibold text-slate-700">Nama Perusahaan</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendorProfile->company_name }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-semibold text-slate-700">Email Perusahaan</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendorProfile->company_email ?? '-' }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-semibold text-slate-700">Nomor Telepon</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">{{ $vendorProfile->company_phone ?? '-' }}</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5">
					<p class="text-sm font-semibold text-slate-700">Jam Operasional</p>
					<p class="mt-1 text-lg font-semibold text-slate-900">
						@if ($vendorProfile->opens_at || $vendorProfile->closes_at)
							{{ $vendorProfile->opens_at ?? '-' }} — {{ $vendorProfile->closes_at ?? '-' }}
						@else
							Belum diatur
						@endif
					</p>
				</div>

				<div class="rounded-2xl bg-slate-50 p-5 md:col-span-2">
					<p class="text-sm font-semibold text-slate-700">Alamat Perusahaan</p>
					<p class="mt-1 whitespace-pre-line text-lg font-semibold text-slate-900">{{ $vendorProfile->company_address }}</p>
				</div>

				@if ($vendorProfile->latitude && $vendorProfile->longitude)
					<div class="rounded-2xl bg-slate-50 p-5 md:col-span-2">
						<p class="text-sm font-semibold text-slate-700">Lokasi Geografis</p>
						<p class="mt-2 text-sm text-slate-600">
							<strong>Latitude:</strong> {{ $vendorProfile->latitude }} | 
							<strong>Longitude:</strong> {{ $vendorProfile->longitude }}
						</p>
						<div id="map" class="mt-3 h-64 w-full rounded-xl border border-slate-200"></div>
					</div>
				@endif

				<div class="rounded-2xl bg-slate-50 p-5 md:col-span-2">
					<p class="text-sm font-semibold text-slate-700">Deskripsi Perusahaan</p>
					<p class="mt-1 whitespace-pre-line text-slate-700">{{ $vendorProfile->company_description ?? 'Belum ada deskripsi.' }}</p>
				</div>
			</div>

			<div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
				<p class="text-sm font-medium text-slate-600">Profil vendor PBI 1 sudah tersimpan.</p>
				<div class="flex gap-3">
					<a href="{{ route('vendor.profile.create') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Perbaiki Profil</a>
					<a href="{{ route('vendor.documents.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Lanjut Upload Legalitas</a>
				</div>
			</div>
		</div>
		</div>
	</div>

<!-- Leaflet CSS & JS untuk tampilkan map lokasi vendor -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		@if ($vendorProfile->latitude && $vendorProfile->longitude)
			var lat = {{ $vendorProfile->latitude }};
			var lng = {{ $vendorProfile->longitude }};
			var mapElement = document.getElementById('map');
			if (mapElement) {
				var map = L.map('map').setView([lat, lng], 14);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 19,
					attribution: '© OpenStreetMap'
				}).addTo(map);
				L.marker([lat, lng]).addTo(map).bindPopup('<strong>{{ $vendorProfile->company_name }}</strong><br>{{ $vendorProfile->company_address }}');
			}
		@endif
	});
</script>

@endsection
