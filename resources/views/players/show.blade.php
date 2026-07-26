@extends('layouts.app')

@section('title', 'Profil Pemain - ' . $player->name)
@section('header_title', 'Profil Detail Atlet')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-id-card text-emerald-600 mr-2"></i>Kartu Profil Pemain</h3>
        <p class="text-xs text-slate-500">Statistik performa lapangan dan kehadiran individu pemain</p>
    </div>
    <a href="{{ route('players.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center gap-1.5 w-fit">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Roster
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Left Column: Radar Chart & Bio -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Player Bio Card -->
        <div class="card-white p-6 rounded-3xl text-center relative overflow-hidden">
            <!-- Decorative Accent -->
            <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            
            <div class="mt-4 flex flex-col items-center">
                <!-- Avatar placeholder or actual custom avatar -->
                @if($player->user && $player->user->avatar && file_exists(public_path($player->user->avatar)))
                    <img src="{{ asset($player->user->avatar) }}" class="w-24 h-24 rounded-full object-cover border-4 border-slate-50 shadow-md" alt="Avatar">
                @else
                    <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 border-4 border-slate-50 flex items-center justify-center text-emerald-700 text-3xl font-extrabold shadow-md">
                        {{ substr($player->name, 0, 1) }}
                    </div>
                @endif
                
                <span class="mt-3.5 inline-flex w-8 h-8 rounded-full bg-emerald-50 border border-emerald-150 items-center justify-center font-black text-sm text-emerald-600 shadow-sm">
                    {{ $player->number }}
                </span>
                <h4 class="text-lg font-extrabold text-slate-900 mt-2">{{ $player->name }}</h4>
                
                @php
                    $posColors = [
                        'Anchor' => 'bg-blue-50 text-blue-700 border-blue-100',
                        'Flank' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'Pivot' => 'bg-purple-50 text-purple-700 border-purple-100',
                        'Goalkeeper' => 'bg-amber-50 text-amber-700 border-amber-100',
                        'Keeper' => 'bg-amber-50 text-amber-700 border-amber-100',
                    ];
                    $posColor = $posColors[$player->position] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                @endphp
                <span class="mt-1.5 px-3 py-1 rounded-full border text-xs font-bold {{ $posColor }} shadow-sm">
                    {{ $player->position }}
                </span>
            </div>

            <!-- Physical Specs Grid -->
            <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-100 text-xs">
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-150/60">
                    <span class="text-slate-400 font-bold block uppercase tracking-wider text-[9px] mb-1">Tinggi Badan</span>
                    <strong class="text-slate-800 text-sm font-extrabold">{{ $player->height ?: '-' }} cm</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-150/60">
                    <span class="text-slate-400 font-bold block uppercase tracking-wider text-[9px] mb-1">Berat Badan</span>
                    <strong class="text-slate-800 text-sm font-extrabold">{{ $player->weight ?: '-' }} kg</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-150/60">
                    <span class="text-slate-400 font-bold block uppercase tracking-wider text-[9px] mb-1">Tanggal Lahir</span>
                    <strong class="text-slate-800 text-xs font-extrabold">
                        {{ $player->birth_date ? \Carbon\Carbon::parse($player->birth_date)->isoFormat('D MMM YYYY') : '-' }}
                    </strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-150/60">
                    <span class="text-slate-400 font-bold block uppercase tracking-wider text-[9px] mb-1">Akun Login</span>
                    <div class="truncate text-slate-800 text-[10px] font-bold" title="{{ $player->user ? $player->user->email : 'Profil Saja' }}">
                        @if($player->user)
                            <span class="text-emerald-600 font-extrabold"><i class="fa-solid fa-circle-check text-[9px] mr-0.5 animate-pulse"></i>{{ $player->user->email }}</span>
                        @else
                            <span class="text-slate-400 font-medium italic">Profil Saja</span>
                        @endif
                    </div>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-150/60 col-span-2">
                    <span class="text-slate-400 font-bold block uppercase tracking-wider text-[9px] mb-1">Hubungan Kontak</span>
                    <strong class="text-slate-800 text-sm font-extrabold">
                        @if($player->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $player->phone) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 hover:underline">
                                <i class="fa-brands fa-whatsapp mr-1 text-emerald-500"></i>{{ $player->phone }}
                            </a>
                        @else
                            -
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        <!-- Capability Radar Card -->
        <div class="card-white p-6 rounded-3xl space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2.5"><i class="fa-solid fa-chart-radar text-emerald-600 mr-2"></i>Radar Kemampuan</h3>
            <div class="relative h-64 w-full">
                <canvas id="playerCapabilitiesChart"></canvas>
            </div>
            <p class="text-[10px] text-slate-400 leading-relaxed text-center font-medium">
                *Kemampuan dihitung secara otomatis berdasarkan rasio gol, assist, disiplin kartu, menit bermain, dan tingkat absensi kehadiran.
            </p>
        </div>
    </div>

    <!-- Right Column: Statistics Cards -->
    <div class="lg:col-span-8 space-y-6">
        <!-- Stats summary cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="card-white p-5 rounded-3xl border border-slate-100 relative overflow-hidden flex flex-col justify-between h-28 shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Gol Tercipta</span>
                <div class="flex justify-between items-end">
                    <span class="text-3xl font-black text-slate-800">{{ $totalGoals }}</span>
                    <i class="fa-solid fa-futbol text-2xl text-emerald-500/20 mb-1"></i>
                </div>
            </div>

            <div class="card-white p-5 rounded-3xl border border-slate-100 relative overflow-hidden flex flex-col justify-between h-28 shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Assist Umpan</span>
                <div class="flex justify-between items-end">
                    <span class="text-3xl font-black text-slate-800">{{ $totalAssists }}</span>
                    <i class="fa-solid fa-hands-holding-circle text-2xl text-teal-500/20 mb-1"></i>
                </div>
            </div>

            <div class="card-white p-5 rounded-3xl border border-slate-100 relative overflow-hidden flex flex-col justify-between h-28 shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Pertandingan</span>
                <div class="flex justify-between items-end">
                    <span class="text-3xl font-black text-slate-800">{{ $matchesPlayed }}</span>
                    <i class="fa-solid fa-shield-halved text-2xl text-indigo-500/20 mb-1"></i>
                </div>
            </div>

            <div class="card-white p-5 rounded-3xl border border-slate-100 relative overflow-hidden flex flex-col justify-between h-28 shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Menit Bermain</span>
                <div class="flex justify-between items-end">
                    <span class="text-2xl font-black text-slate-800">{{ $totalMinutes }} <span class="text-xs font-bold text-slate-400">min</span></span>
                    <i class="fa-regular fa-clock text-2xl text-amber-500/20 mb-0.5"></i>
                </div>
            </div>
        </div>

        <!-- Attendance Analysis Card -->
        <div class="card-white p-6 rounded-3xl space-y-5">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-clipboard-user text-emerald-600 mr-2"></i>Analisis Kehadiran Skuad</h3>
                <span class="text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-150 px-3 py-1 rounded-full">
                    Kehadiran: {{ $attendanceRate }}%
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
                <div class="bg-emerald-50/50 p-3.5 rounded-2xl border border-emerald-100">
                    <span class="text-2xl font-black text-emerald-700">{{ $presentCount }}</span>
                    <span class="text-[9px] uppercase font-extrabold text-emerald-600 block mt-1.5">Hadir</span>
                </div>
                <div class="bg-blue-50/50 p-3.5 rounded-2xl border border-blue-100">
                    <span class="text-2xl font-black text-blue-700">{{ $excusedCount }}</span>
                    <span class="text-[9px] uppercase font-extrabold text-blue-600 block mt-1.5">Izin</span>
                </div>
                <div class="bg-red-50/50 p-3.5 rounded-2xl border border-red-100">
                    <span class="text-2xl font-black text-red-700">{{ $absentCount }}</span>
                    <span class="text-[9px] uppercase font-extrabold text-red-600 block mt-1.5">Alpa</span>
                </div>
                <div class="bg-amber-50/50 p-3.5 rounded-2xl border border-amber-100">
                    <span class="text-2xl font-black text-amber-700">{{ $injuredCount }}</span>
                    <span class="text-[9px] uppercase font-extrabold text-amber-600 block mt-1.5">Cedera</span>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 col-span-2 sm:col-span-1">
                    <span class="text-2xl font-black text-slate-655">{{ $notRecordedCount }}</span>
                    <span class="text-[9px] uppercase font-extrabold text-slate-500 block mt-1.5">Belum Absen</span>
                </div>
            </div>

            <p class="text-xs text-slate-500 italic text-center font-medium leading-relaxed">
                Agenda Tim Tercatat: {{ $totalAgendas }} kegiatan | Pemain diwajibkan mengonfirmasi kehadiran di setiap sesi latihan dan pertandingan demi kelancaran manajemen skuad.
            </p>
        </div>

        <!-- Discipline & Kas Finance Widget -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Disiplin Kartu -->
            <div class="card-white p-6 rounded-3xl space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2.5"><i class="fa-solid fa-triangle-exclamation text-emerald-600 mr-2"></i>Rekam Pelanggaran & Kartu</h3>
                <div class="flex items-center justify-around py-2">
                    <div class="text-center px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-2xl min-w-[95px]">
                        <span class="inline-block w-6 h-8 bg-yellow-400 rounded shadow-sm mb-1.5"></span>
                        <div class="text-[10px] font-bold text-yellow-750 uppercase">Kuning</div>
                        <div class="text-xl font-black text-yellow-800 mt-0.5">{{ $totalYellow }}</div>
                    </div>

                    <div class="text-center px-4 py-3 bg-red-50 border border-red-200 rounded-2xl min-w-[95px]">
                        <span class="inline-block w-6 h-8 bg-red-500 rounded shadow-sm mb-1.5"></span>
                        <div class="text-[10px] font-bold text-red-700 uppercase">Merah</div>
                        <div class="text-xl font-black text-red-800 mt-0.5">{{ $totalRed }}</div>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 text-center italic font-medium leading-relaxed">
                    *Akumulasi kartu mempengaruhi kalkulasi indeks disiplin pemain di lapangan.
                </p>
            </div>

            <!-- Iuran Kas Kegiatan -->
            <div class="card-white p-6 rounded-3xl space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2.5"><i class="fa-solid fa-receipt text-emerald-600 mr-2"></i>Status Iuran Agenda</h3>
                <div class="flex items-center justify-around py-2">
                    <div class="text-center">
                        <div class="text-2xl font-black text-emerald-600">{{ $paidDuesCount }}</div>
                        <div class="text-[10px] font-bold text-slate-450 uppercase tracking-wider mt-1.5">Lunas</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-black text-red-655">{{ $unpaidDuesCount }}</div>
                        <div class="text-[10px] font-bold text-slate-450 uppercase tracking-wider mt-1.5">Belum Lunas</div>
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-150 p-2.5 rounded-xl text-center text-[10px] text-slate-500 font-bold">
                    Jumlah Agenda Wajib Iuran: {{ $totalDuesAgendas }} kali
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('playerCapabilitiesChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Scoring (Gol)', 'Playmaking (Assist)', 'Playtime (Menit)', 'Disiplin (Kartu)', 'Kehadiran (Absensi)'],
                datasets: [{
                    label: 'Indeks Atlet',
                    data: [
                        {{ $radarStats['scoring'] }},
                        {{ $radarStats['playmaking'] }},
                        {{ $radarStats['playtime'] }},
                        {{ $radarStats['discipline'] }},
                        {{ $radarStats['attendance'] }}
                    ],
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(16, 185, 129, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(226, 232, 240, 0.8)'
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.8)'
                        },
                        pointLabels: {
                            font: {
                                family: 'Outfit',
                                size: 10,
                                weight: 'bold'
                            },
                            color: '#475569'
                        },
                        ticks: {
                            display: false,
                            stepSize: 20
                        },
                        min: 0,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection
