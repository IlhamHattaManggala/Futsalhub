@extends('layouts.app')

@section('title', 'Monitor Platform')
@section('header_title', 'Dasbor Superadmin Platform')

@section('content')
<!-- Monitoring Cards -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Tenants -->
    <div class="card-white p-6 rounded-3xl flex items-center justify-between card-white-hover transition-all duration-300">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Tim Futsal (Tenants)</span>
            <h3 class="text-3xl font-extrabold text-slate-900">
                {{ $totalTeams }} <span class="text-xs font-semibold text-slate-400">Tim</span>
            </h3>
            <a href="{{ route('superadmin.teams') }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 transition-colors flex items-center gap-0.5">
                <span>Kelola Tenant Tim</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-sm">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
    </div>

    <!-- Total Platform Users -->
    <div class="card-white p-6 rounded-3xl flex items-center justify-between card-white-hover transition-all duration-300">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pengguna Terdaftar</span>
            <h3 class="text-3xl font-extrabold text-slate-900">
                {{ $totalUsers }} <span class="text-xs font-semibold text-slate-400">Akun</span>
            </h3>
            <a href="{{ route('superadmin.users') }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 transition-colors flex items-center gap-0.5">
                <span>Kelola Akun Pengguna</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-2xl shadow-sm">
            <i class="fa-solid fa-users-gear"></i>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="card-white p-6 rounded-3xl flex items-center justify-between card-white-hover transition-all duration-300">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pendapatan Premium</span>
            <h3 class="text-3xl font-extrabold text-slate-900">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </h3>
            <a href="{{ route('superadmin.payments.index') }}" class="text-[11px] font-bold text-amber-600 hover:text-amber-700 transition-colors flex items-center gap-0.5">
                <span>Monitor Transaksi</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-2xl shadow-sm">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Registered Teams -->
    <div class="card-white p-6 rounded-3xl space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-base font-extrabold text-slate-900"><i class="fa-solid fa-shield text-emerald-600 mr-2"></i>Tim Baru Terdaftar</h3>
            <a href="{{ route('superadmin.teams') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($recentTeams as $team)
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center">
                    <div>
                        <div class="font-bold text-sm text-slate-900">{{ $team->name }}</div>
                        <div class="text-[10px] text-slate-500 mt-0.5 font-medium">ID Tenant: #{{ $team->id }} | Terdaftar {{ $team->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="px-2.5 py-1 text-[9px] font-bold border rounded bg-white border-slate-200 text-slate-700 shadow-sm">
                        {{ $team->players_count ?? $team->players()->count() }} Pemain
                    </span>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-xs font-semibold">Belum ada tim futsal terdaftar.</div>
            @endforelse
        </div>
    </div>

    <!-- Recent Registered Users -->
    <div class="card-white p-6 rounded-3xl space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-base font-extrabold text-slate-900"><i class="fa-solid fa-users-gear text-emerald-600 mr-2"></i>Pengguna Baru</h3>
            <a href="{{ route('superadmin.users') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($recentUsers as $user)
                @php
                    $roleColors = [
                        'superadmin' => 'bg-red-50 border-red-150 text-red-700',
                        'management' => 'bg-teal-50 border-teal-150 text-teal-700',
                        'coach' => 'bg-yellow-50 border-yellow-150 text-yellow-800',
                        'player' => 'bg-blue-50 border-blue-150 text-blue-700',
                    ];
                    $roleLabel = [
                        'superadmin' => 'Superadmin',
                        'management' => 'Manager',
                        'coach' => 'Pelatih',
                        'player' => 'Pemain',
                    ];
                    $roleClass = $roleColors[$user->role->name] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                    $roleName = $roleLabel[$user->role->name] ?? 'User';
                @endphp
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center">
                    <div>
                        <div class="font-bold text-sm text-slate-900">{{ $user->name }}</div>
                        @php
                            $emailParts = explode('@', $user->email);
                            $username = $emailParts[0] ?? '';
                            $domain = $emailParts[1] ?? '';
                            $keepLen = strlen($username) > 4 ? 4 : (strlen($username) > 1 ? 2 : 1);
                            $maskedEmail = substr($username, 0, $keepLen) . str_repeat('*', strlen($username) - $keepLen) . '@' . $domain;
                        @endphp
                        <div class="text-[10px] text-slate-500 mt-0.5 font-medium">{{ $maskedEmail }} | Tim: {{ $user->team ? $user->team->name : 'Global' }}</div>
                    </div>
                    <span class="px-2.5 py-0.5 text-[9px] font-bold border rounded {{ $roleClass }}">
                        {{ $roleName }}
                    </span>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-xs font-semibold">Belum ada pengguna.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
