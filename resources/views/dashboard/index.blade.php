@extends('layouts.app')

@section('title', 'Dasbor Tim')
@section('header_title', 'Dasbor Analitik Tim')

@section('content')
<!-- Stats Widget Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <!-- Balance Card -->
    <div class="card-white p-5 rounded-2xl flex items-center justify-between card-white-hover transition-all">
        <div class="space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Kas Tim</span>
            <h3 class="text-2xl font-extrabold text-slate-900 leading-tight">
                Rp {{ number_format($balance, 0, ',', '.') }}
            </h3>
            <div class="text-[10px] text-emerald-600 font-bold">
                <i class="fa-solid fa-trend-up"></i> Saldo Kas Aktif
            </div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-lg shadow-sm shrink-0">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>

    <!-- Players Card -->
    <div class="card-white p-5 rounded-2xl flex items-center justify-between card-white-hover transition-all">
        <div class="space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pemain Skuad</span>
            <h3 class="text-2xl font-extrabold text-slate-900 leading-tight">
                {{ $totalPlayers }} <span class="text-xs font-semibold text-slate-400">Atlet</span>
            </h3>
            <div class="text-[10px] text-slate-500">
                Terdaftar kompetisi aktif
            </div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-lg shadow-sm shrink-0">
            <i class="fa-solid fa-user-group"></i>
        </div>
    </div>

    <!-- Active Tactics Card -->
    <div class="card-white p-5 rounded-2xl flex items-center justify-between card-white-hover transition-all">
        <div class="space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Strategi Tim</span>
            <h3 class="text-lg font-extrabold text-slate-900 leading-tight">
                @if(count($tactics) > 0)
                    Taktik Aktif
                @else
                    Belum Dibuat
                @endif
            </h3>
            @if(Auth::user()->isCoach())
                <a href="{{ route('tactics.index') }}" class="text-[10px] text-emerald-600 font-bold hover:text-emerald-700 transition-colors flex items-center gap-0.5">
                    <span>Buka Board</span> <i class="fa-solid fa-arrow-right text-[8px]"></i>
                </a>
            @else
                <div class="text-[10px] text-slate-450 font-bold mt-1">
                    <i class="fa-solid fa-lock text-[8px] mr-1 text-slate-400"></i> Khusus Pelatih
                </div>
            @endif
        </div>
        <div class="w-11 h-11 rounded-xl bg-yellow-50 border border-yellow-100 flex items-center justify-center text-yellow-600 text-lg shadow-sm shrink-0">
            <i class="fa-solid fa-compass-drafting"></i>
        </div>
    </div>
</div>

<!-- Charts & Schedules Row -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    <!-- Finance Cashflow Chart -->
    <div class="lg:col-span-8 card-white p-5 rounded-2xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Visualisasi Arus Kas Bulanan</h3>
                <p class="text-[10px] text-slate-500">Pemasukan vs Pengeluaran Tim</p>
            </div>
            <i class="fa-solid fa-chart-line text-emerald-600 text-base"></i>
        </div>
        <div class="h-64 w-full relative">
            @if(count($monthlyFinance) > 0)
                <canvas id="cashflowChart"></canvas>
            @else
                <div class="h-full flex items-center justify-center text-slate-450 text-xs font-semibold">
                    Belum ada data keuangan untuk menampilkan grafik.
                </div>
            @endif
        </div>
    </div>

    <!-- Upcoming Schedules -->
    <div class="lg:col-span-4 card-white p-5 rounded-2xl flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Jadwal Terdekat</h3>
                <p class="text-[10px] text-slate-500">Latihan & Pertandingan</p>
            </div>
            <i class="fa-solid fa-calendar-week text-emerald-600"></i>
        </div>
        <div class="space-y-3 flex-1 overflow-y-auto">
            @forelse($upcomingSchedules as $s)
                @php
                    $isMatch = $s->type === 'Pertandingan';
                    $badgeClass = $isMatch ? 'bg-red-50 text-red-750 border-red-100' : 'bg-blue-50 text-blue-750 border-blue-100';
                @endphp
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex gap-2.5 items-center">
                    <div class="text-center px-1.5 py-1 rounded-lg bg-white border border-slate-200 flex flex-col justify-center min-w-[42px] shrink-0">
                        <span class="text-[8px] uppercase font-bold text-slate-500">{{ $s->start_time->isoFormat('MMM') }}</span>
                        <span class="text-sm font-extrabold text-slate-800 leading-none mt-0.5">{{ $s->start_time->isoFormat('D') }}</span>
                    </div>
                    <div class="overflow-hidden flex-1 min-w-0">
                        <div class="font-bold text-xs text-slate-800 truncate">{{ $s->title }}</div>
                        <div class="text-[9px] text-slate-500 truncate mt-0.5">
                            <i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i> {{ $s->location }}
                        </div>
                    </div>
                    <span class="px-2 py-0.5 text-[8px] font-bold border rounded-md shrink-0 {{ $badgeClass }}">
                        {{ $s->type }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs font-semibold flex-1 flex flex-col justify-center">
                    <i class="fa-solid fa-calendar-xmark text-xl mb-2 text-slate-300"></i>
                    Tidak ada jadwal mendatang.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Leaderboard & Bulletins Row -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Top Scorers & Assists Leaderboards -->
    <div class="lg:col-span-8 card-white p-5 rounded-2xl">
        <h3 class="text-sm font-extrabold text-slate-900 mb-4"><i class="fa-solid fa-award text-yellow-500 mr-2"></i>Statistik Performa Pemain (Top Stats)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Top Scorer Table -->
            <div>
                <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2.5 flex items-center gap-1">
                    <i class="fa-solid fa-fire text-orange-500"></i>
                    <span>Top Score</span>
                </h4>
                <div class="space-y-2">
                    @forelse($topScorers as $idx => $ts)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0 shadow-sm">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate">{{ $ts->name }}</div>
                                    <div class="text-[9px] text-slate-550 mt-0.5">Jersey No.{{ $ts->number }} | {{ $ts->position }}</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 font-extrabold text-xs shrink-0">
                                {{ $ts->total_goals }} Gol
                            </span>
                        </div>
                    @empty
                        <div class="text-slate-400 text-[10px] font-semibold py-4 text-center">Belum ada statistik gol.</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Assist Table -->
            <div>
                <h4 class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2.5 flex items-center gap-1">
                    <i class="fa-solid fa-handshake text-blue-500"></i>
                    <span>Top Assist</span>
                </h4>
                <div class="space-y-2">
                    @forelse($topAssists as $idx => $ta)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0 shadow-sm">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate">{{ $ta->name }}</div>
                                    <div class="text-[9px] text-slate-555 mt-0.5">Jersey No.{{ $ta->number }} | {{ $ta->position }}</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 font-extrabold text-xs shrink-0">
                                {{ $ta->total_assists }} Assist
                            </span>
                        </div>
                    @empty
                        <div class="text-slate-400 text-[10px] font-semibold py-4 text-center">Belum ada statistik assist.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Announcements & Bulletins -->
    <div class="lg:col-span-4 card-white p-5 rounded-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold text-slate-900">Pengumuman Terbaru</h3>
            <i class="fa-solid fa-bullhorn text-emerald-600"></i>
        </div>
        <div class="space-y-3">
            @forelse($announcements as $a)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-xs text-slate-800 truncate max-w-[140px]">{{ $a->title }}</h4>
                        <span class="text-[8px] font-semibold text-slate-400">{{ $a->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[10px] text-slate-600 line-clamp-2 leading-normal font-medium">
                        {{ $a->content }}
                    </p>
                    <div class="text-[9px] text-slate-500 pt-0.5 flex items-center gap-1 font-semibold">
                        <i class="fa-regular fa-user text-emerald-600 text-[8px]"></i> <span>Oleh: <strong class="text-slate-700">{{ $a->user->name }}</strong></span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs font-semibold">
                    <i class="fa-solid fa-message-slash text-xl mb-2 text-slate-300"></i>
                    Belum ada pengumuman tim.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize Cashflow Chart with Chart.js
    @if(count($monthlyFinance) > 0)
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('cashflowChart').getContext('2d');
        
        // Prepare data from PHP
        const rawData = @json($monthlyFinance);
        const labels = rawData.map(item => item.month);
        const incomes = rawData.map(item => parseFloat(item.total_income));
        const expenses = rawData.map(item => parseFloat(item.total_expense));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan (Rp)',
                        data: incomes,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#ffffff',
                        pointRadius: 4,
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: expenses,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#dc2626',
                        pointBorderColor: '#ffffff',
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#475569',
                            font: {
                                family: 'Outfit',
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            color: '#475569',
                            font: {
                                family: 'Outfit'
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            color: '#475569',
                            font: {
                                family: 'Outfit'
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
    @endif
</script>
@endsection
