@props(['vehicle', 'station'])

@php
    $matchingStatus = $vehicle->getMatchingStatus($station);
    $isCompatible = $matchingStatus['is_compatible'];
    $count = $matchingStatus['count'];
@endphp

<div class="flex items-center gap-3 p-4 rounded-lg {{ $isCompatible ? 'bg-emerald-500/5 border border-emerald-500/20' : 'bg-slate-500/5 border border-slate-500/20' }}">
    <div class="flex-1">
        @if($isCompatible)
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-semibold text-emerald-600">Cocok!</span>
            </div>
            <p class="text-sm text-emerald-600 mt-1">{{ $count }} charger tersedia dengan konektor {{ $vehicle->connector_type }}</p>
        @else
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="font-semibold text-slate-600">Tidak Cocok</span>
            </div>
            <p class="text-sm text-slate-600 mt-1">Stasiun ini tidak memiliki konektor {{ $vehicle->connector_type }}</p>
        @endif
    </div>
</div>
