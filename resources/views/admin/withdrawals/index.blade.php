@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-700">Admin Withdrawal</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Daftar Pengajuan Vendor</h1>
                <p class="mt-2 text-sm text-slate-500">Kelola status withdrawal vendor dari satu tempat.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kembali ke Admin</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-3xl bg-slate-900 p-5 text-white">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Total Pengajuan</p>
            <p class="mt-3 text-2xl font-black">{{ $withdrawals->count() }}</p>
        </div>
        <div class="rounded-3xl bg-amber-100 p-5 text-slate-900">
            <p class="text-xs uppercase tracking-[0.3em] text-amber-800">Pending</p>
            <p class="mt-3 text-2xl font-black">{{ $withdrawals->where('status', 'pending')->count() }}</p>
        </div>
        <div class="rounded-3xl bg-blue-100 p-5 text-slate-900">
            <p class="text-xs uppercase tracking-[0.3em] text-blue-800">Approved</p>
            <p class="mt-3 text-2xl font-black">{{ $withdrawals->where('status', 'approved')->count() }}</p>
        </div>
        <div class="rounded-3xl bg-emerald-100 p-5 text-slate-900">
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-800">Paid</p>
            <p class="mt-3 text-2xl font-black">{{ $withdrawals->where('status', 'paid')->count() }}</p>
        </div>
    </div>

    <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="space-y-4">
            @forelse ($withdrawals as $withdrawal)
                @php
                    $statusStyles = [
                        'pending' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-blue-100 text-blue-800',
                        'rejected' => 'bg-rose-100 text-rose-800',
                        'paid' => 'bg-emerald-100 text-emerald-800',
                    ];
                    $statusClass = $statusStyles[$withdrawal->status] ?? 'bg-slate-100 text-slate-700';
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-lg font-bold text-slate-900">{{ $withdrawal->vendor?->company_name ?? 'Vendor' }}</h2>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ strtoupper($withdrawal->status) }}</span>
                            </div>
                            <p class="text-sm text-slate-500">{{ $withdrawal->reference_code }} · Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                            <p class="text-sm text-slate-500">{{ $withdrawal->bank_name }} · {{ $withdrawal->bank_account_name }} · {{ $withdrawal->bank_account_number }}</p>
                            @if($withdrawal->notes)
                                <p class="text-sm text-slate-600">{{ $withdrawal->notes }}</p>
                            @endif
                            @if($withdrawal->admin_notes)
                                <p class="text-sm text-slate-600"><span class="font-semibold text-slate-700">Catatan admin:</span> {{ $withdrawal->admin_notes }}</p>
                            @endif
                            <p class="text-xs text-slate-400">Diajukan {{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @if ($withdrawal->status === 'pending')
                                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-emerald-700">Setujui</button>
                                </form>
                                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-2xl bg-rose-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-rose-700">Tolak</button>
                                </form>
                            @endif

                            @if ($withdrawal->status === 'approved')
                                <form action="{{ route('admin.withdrawals.paid', $withdrawal) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800">Tandai Paid</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                    Belum ada pengajuan withdrawal.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection