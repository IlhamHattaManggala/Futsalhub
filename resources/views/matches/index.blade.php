@extends('layouts.app')

@section('title', 'Pertandingan & Hasil')
@section('header_title', 'Hasil & Jadwal Tanding')

@section('content')
<!-- Team Performance Record Widgets -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="card-white p-4 rounded-2xl text-center border-l-4 border-slate-450">
        <span class="text-[10px] uppercase font-bold text-slate-500">Tanding</span>
        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $played }}</div>
    </div>
    <div class="card-white p-4 rounded-2xl text-center border-l-4 border-emerald-500">
        <span class="text-[10px] uppercase font-bold text-emerald-600">Menang (W)</span>
        <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $wins }}</div>
    </div>
    <div class="card-white p-4 rounded-2xl text-center border-l-4 border-slate-400">
        <span class="text-[10px] uppercase font-bold text-slate-650">Seri (D)</span>
        <div class="text-2xl font-extrabold text-slate-600 mt-1">{{ $draws }}</div>
    </div>
    <div class="card-white p-4 rounded-2xl text-center border-l-4 border-red-500">
        <span class="text-[10px] uppercase font-bold text-red-600">Kalah (L)</span>
        <div class="text-2xl font-extrabold text-red-600 mt-1">{{ $losses }}</div>
    </div>
    <div class="card-white p-4 rounded-2xl text-center border-l-4 border-teal-500 hidden md:block">
        <span class="text-[10px] uppercase font-bold text-slate-500">Gol (F:A)</span>
        <div class="text-sm font-extrabold text-slate-900 mt-2">
            <span class="text-emerald-600">{{ $goalsFor }}</span> <span class="text-slate-400">:</span> <span class="text-red-600">{{ $goalsAgainst }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Matches list -->
    <div class="xl:col-span-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Hasil Pertandingan</h3>
                <p class="text-xs text-slate-500">Daftar rekam jejak laga uji coba dan kompetisi resmi</p>
            </div>
            <div class="bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-trophy text-slate-500 text-xs"></i>
                <span class="text-[11px] font-bold text-slate-700">{{ count($matches) }} Laga</span>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($matches as $m)
                @php
                    $isFinished = $m->status === 'Selesai';
                    $win = $isFinished && $m->score_team > $m->score_opponent;
                    $draw = $isFinished && $m->score_team === $m->score_opponent;
                    $loss = $isFinished && $m->score_team < $m->score_opponent;
                    
                    $outcomeText = 'Terjadwal';
                    $outcomeClass = 'bg-slate-50 text-slate-600 border border-slate-200';
                    
                    if ($isFinished) {
                        if ($win) {
                            $outcomeText = 'Menang';
                            $outcomeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-150';
                        } elseif ($draw) {
                            $outcomeText = 'Seri';
                            $outcomeClass = 'bg-slate-50 text-slate-700 border border-slate-200';
                        } else {
                            $outcomeText = 'Kalah';
                            $outcomeClass = 'bg-red-50 text-red-700 border border-red-150';
                        }
                    }
                @endphp
                <div class="card-white p-4 sm:p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4 card-white-hover transition-all duration-300">
                    <!-- Left: Match Core Info -->
                    <div class="flex items-center gap-4 flex-1 w-full">
                        <!-- Outcome status badge -->
                        <span class="w-20 text-center px-2 py-1 text-[10px] font-bold border rounded-md uppercase tracking-wider shrink-0 {{ $outcomeClass }}">
                            {{ $outcomeText }}
                        </span>

                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-sm text-slate-900">vs {{ $m->opponent }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1 font-semibold">
                                <span><i class="fa-regular fa-calendar text-emerald-600 mr-1"></i> {{ $m->date->isoFormat('D MMM YYYY') }}</span>
                                <span><i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i> {{ $m->location }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Middle: Scores Display -->
                    <div class="flex items-center justify-center gap-6 px-6 py-2 bg-slate-50 rounded-2xl border border-slate-200 w-full md:w-auto min-w-[140px] text-center">
                        @if($isFinished)
                            <span class="text-2xl font-extrabold text-slate-900">{{ $m->score_team }}</span>
                            <span class="text-xs text-slate-400 font-extrabold">VS</span>
                            <span class="text-2xl font-extrabold text-slate-500">{{ $m->score_opponent }}</span>
                        @else
                            <span class="text-xs text-slate-400 uppercase tracking-widest font-extrabold">VS</span>
                        @endif
                    </div>

                    <!-- Right: Coach Actions -->
                    <div class="w-full md:w-auto flex justify-end">
                        @if(Auth::user()->isCoach() && $isFinished)
                            @if(Auth::user()->team && Auth::user()->team->isPremium())
                                <a href="{{ route('matches.stats', $m->id) }}" 
                                    class="w-full justify-center md:w-auto px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 shadow-md shadow-emerald-600/10">
                                    <i class="fa-solid fa-file-invoice"></i> Input Stat Skuad
                                </a>
                            @else
                                <span title="Fitur statistik performa hanya tersedia untuk paket Premium"
                                    class="w-full justify-center md:w-auto inline-flex px-3.5 py-2 bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold rounded-xl items-center gap-1.5 cursor-not-allowed select-none">
                                    <i class="fa-solid fa-lock text-[10px]"></i> Stat Skuad (Premium)
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="card-white p-12 text-center rounded-3xl text-slate-400">
                    <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-4 text-2xl shadow-sm">
                        <i class="fa-solid fa-trophy text-slate-350"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Rekaman Laga</h4>
                    <p class="text-xs max-w-sm mx-auto text-slate-500 leading-relaxed">
                        Belum ada pertandingan persahabatan atau turnamen yang diinput ke dalam sistem.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Add Match Record Form (Coach & Management only) -->
    <div class="xl:col-span-4">
        @if(Auth::user()->isCoach() || Auth::user()->isManagement())
            <div class="card-white p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-square-plus text-emerald-600 mr-2"></i>Rekam Laga Baru</h3>
                    <p class="text-xs text-slate-500">Tambahkan agenda laga yang sudah selesai maupun terjadwal</p>
                </div>
                
                <form action="{{ route('matches.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Tim Lawan</label>
                        <input type="text" name="opponent" placeholder="Misal: Garuda FC" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Laga</label>
                            <input type="date" name="date" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Status Laga</label>
                            <select name="status" id="matchStatus" onchange="toggleScoreInputs()" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                                <option value="Selesai">Selesai</option>
                                <option value="Terjadwal">Terjadwal</option>
                            </select>
                        </div>
                    </div>

                    <div id="scoreFields" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Skor Tim Kita</label>
                            <input type="number" name="score_team" id="scoreTeamInput" placeholder="Skor kita" min="0" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Skor Lawan</label>
                            <input type="number" name="score_opponent" id="scoreOpponentInput" placeholder="Skor lawan" min="0" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Lokasi Lapangan</label>
                        <input type="text" name="location" placeholder="Misal: Champion Futsal Court B" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Evaluasi Singkat Pertandingan</label>
                        <textarea name="notes" rows="3" placeholder="Bagaimana performa pertahanan/serangan, dll..."
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all"></textarea>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                        Simpan Rekam Pertandingan
                    </button>
                </form>
            </div>
        @else
            <!-- Display informational widget -->
            <div class="card-white p-6 rounded-3xl text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mx-auto text-xl shadow-sm">
                    <i class="fa-solid fa-ranking-star text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Statistik & Rekor Laga</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mt-2">
                        Halaman ini menampilkan seluruh performa kemenangan/kekalahan tim. Hanya manajer & pelatih yang berwenang mencatat skor hasil pertandingan futsal di sini.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleScoreInputs() {
        const status = document.getElementById('matchStatus').value;
        const scoreFields = document.getElementById('scoreFields');
        const scoreTeamInput = document.getElementById('scoreTeamInput');
        const scoreOpponentInput = document.getElementById('scoreOpponentInput');

        if (status === 'Selesai') {
            scoreFields.classList.remove('hidden');
            scoreTeamInput.required = true;
            scoreOpponentInput.required = true;
        } else {
            scoreFields.classList.add('hidden');
            scoreTeamInput.required = false;
            scoreOpponentInput.required = false;
            scoreTeamInput.value = '';
            scoreOpponentInput.value = '';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        toggleScoreInputs();
    });
</script>
@endsection

@section('scripts')
<script>
    function toggleScoreInputs() {
        const status = document.getElementById('matchStatus').value;
        const scoreFields = document.getElementById('scoreFields');
        const scoreTeamInput = document.getElementById('scoreTeamInput');
        const scoreOpponentInput = document.getElementById('scoreOpponentInput');

        if (status === 'Selesai') {
            scoreFields.classList.remove('hidden');
            scoreTeamInput.required = true;
            scoreOpponentInput.required = true;
        } else {
            scoreFields.classList.add('hidden');
            scoreTeamInput.required = false;
            scoreOpponentInput.required = false;
            scoreTeamInput.value = '';
            scoreOpponentInput.value = '';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        toggleScoreInputs();
    });
</script>
@endsection
