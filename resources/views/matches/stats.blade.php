@extends('layouts.app')

@section('title', 'Statistik Skuad')
@section('header_title', 'Statistik Pertandingan Pemain')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-file-invoice text-emerald-600 mr-2"></i>Input Statistik vs {{ $match->opponent }}</h3>
        <p class="text-xs text-slate-500 mt-1 font-medium">
            <i class="fa-solid fa-calendar mr-1 text-slate-400"></i> Tanggal Laga: {{ $match->date->isoFormat('dddd, D MMMM YYYY') }} | 
            <i class="fa-solid fa-location-dot text-red-500 mx-1"></i> {{ $match->location }} |
            <strong class="text-emerald-700 ml-2">Hasil Akhir: {{ $match->score_team }} - {{ $match->score_opponent }}</strong>
        </p>
    </div>
    <a href="{{ route('matches.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
        <i class="fa-solid fa-arrow-left"></i> Batal / Kembali
    </a>
</div>

<div class="card-white rounded-3xl overflow-hidden shadow-sm">
    <form action="{{ route('matches.stats.save', $match->id) }}" method="POST">
        @csrf
        
        <!-- Team vs Opponent Collective Stats -->
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-bar"></i> Statistik Kolektif Tim vs Lawan
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ball Possession -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block text-center">Ball Possession (%)</span>
                    <div class="flex items-center justify-between gap-3">
                        <div class="w-1/2">
                            <label class="block text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Tim Kita</label>
                            <input type="number" name="possession_team" value="{{ $match->possession_team ?? 50 }}" min="0" max="100" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-center font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm">
                        </div>
                        <span class="text-xs font-black text-slate-400 mt-5">:</span>
                        <div class="w-1/2">
                            <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lawan</label>
                            <input type="number" name="possession_opponent" value="{{ $match->possession_opponent ?? 50 }}" min="0" max="100" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-center font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Shoot On Target -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block text-center">Shoot On Target</span>
                    <div class="flex items-center justify-between gap-3">
                        <div class="w-1/2">
                            <label class="block text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Tim Kita</label>
                            <input type="number" name="shoot_on_target_team" id="shootOnTargetTeam" value="{{ $match->shoot_on_target_team ?? 0 }}" min="0" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-center font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm">
                        </div>
                        <span class="text-xs font-black text-slate-400 mt-5">:</span>
                        <div class="w-1/2">
                            <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lawan</label>
                            <input type="number" name="shoot_on_target_opponent" value="{{ $match->shoot_on_target_opponent ?? 0 }}" min="0" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-center font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Shoot Off Target -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block text-center">Shoot Off Target</span>
                    <div class="flex items-center justify-between gap-3">
                        <div class="w-1/2">
                            <label class="block text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Tim Kita</label>
                            <input type="number" name="shoot_off_target_team" id="shootOffTargetTeam" value="{{ $match->shoot_off_target_team ?? 0 }}" min="0" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-center font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm">
                        </div>
                        <span class="text-xs font-black text-slate-400 mt-5">:</span>
                        <div class="w-1/2">
                            <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lawan</label>
                            <input type="number" name="shoot_off_target_opponent" value="{{ $match->shoot_off_target_opponent ?? 0 }}" min="0" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-center font-bold text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 text-center w-20">No.</th>
                        <th class="py-4 px-6">Nama Pemain</th>
                        <th class="py-4 px-6">Posisi</th>
                        <th class="py-4 px-6 text-center w-24">Gol (Goals)</th>
                        <th class="py-4 px-6 text-center w-24">Assist</th>
                        <th class="py-4 px-6 text-center w-48">Clearance / Save</th>
                        <th class="py-4 px-6 text-center w-48">Shots On / Off Target</th>
                        <th class="py-4 px-6 text-center w-28">Kartu Kuning</th>
                        <th class="py-4 px-6 text-center w-28">Kartu Merah</th>
                        <th class="py-4 px-6 text-center w-36">Menit Bermain</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700 font-semibold">
                    @forelse($players as $p)
                        @php
                            $existStat = $statistics->get($p->id);
                            $goals = $existStat ? $existStat->goals : 0;
                            $assists = $existStat ? $existStat->assists : 0;
                            $yellow = $existStat ? $existStat->yellow_cards : 0;
                            $red = $existStat ? $existStat->red_cards : 0;
                            $minutes = $existStat ? $existStat->minutes_played : 0;
                            $clearance = $existStat ? $existStat->clearance : 0;
                            $save = $existStat ? $existStat->save : 0;
                            $shootOnTarget = $existStat ? $existStat->shoot_on_target : 0;
                            $shootOffTarget = $existStat ? $existStat->shoot_off_target : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Jersey Number -->
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex w-8 h-8 rounded-full bg-slate-50 border border-slate-200 items-center justify-center font-extrabold text-xs text-slate-700">
                                    {{ $p->number }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $p->name }}
                            </td>

                            <!-- Position -->
                            <td class="py-4 px-6 text-xs text-slate-650">
                                @php
                                    $posColors = [
                                        'Anchor' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'Flank' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'Pivot' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'Goalkeeper' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'Keeper' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    ];
                                    $posColor = $posColors[$p->position] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg border text-xs font-bold {{ $posColor }}">
                                    {{ $p->position }}
                                </span>
                            </td>

                            <!-- Goals -->
                            <td class="py-4 px-6 text-center">
                                <input type="number" name="stats[{{ $p->id }}][goals]" value="{{ $goals }}" min="0" required
                                    class="w-16 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-bold transition-all">
                            </td>

                            <!-- Assists -->
                            <td class="py-4 px-6 text-center">
                                <input type="number" name="stats[{{ $p->id }}][assists]" value="{{ $assists }}" min="0" required
                                    class="w-16 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-bold transition-all">
                            </td>

                            <!-- Clearance / Save -->
                            <td class="py-4 px-6 text-center">
                                @if(in_array($p->position, ['Goalkeeper', 'Keeper']))
                                    <div class="flex items-center gap-1.5 justify-center">
                                        <div class="inline-flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400 font-bold">Clr:</span>
                                            <input type="number" name="stats[{{ $p->id }}][clearance]" value="{{ $clearance }}" min="0" required
                                                class="w-12 bg-slate-50 border border-slate-200 rounded-lg py-1 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold transition-all">
                                        </div>
                                        <div class="inline-flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400 font-bold">Sav:</span>
                                            <input type="number" name="stats[{{ $p->id }}][save]" value="{{ $save }}" min="0" required
                                                class="w-12 bg-slate-50 border border-slate-200 rounded-lg py-1 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold transition-all">
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-350">-</span>
                                @endif
                            </td>

                            <!-- Shots On / Off Target -->
                            <td class="py-4 px-6 text-center">
                                @if(!in_array($p->position, ['Goalkeeper', 'Keeper']))
                                    <div class="flex items-center gap-1.5 justify-center">
                                        <div class="inline-flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400 font-bold">On:</span>
                                            <input type="number" name="stats[{{ $p->id }}][shoot_on_target]" value="{{ $shootOnTarget }}" min="0" required
                                                class="player-shoot-on-target w-12 bg-slate-50 border border-slate-200 rounded-lg py-1 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold transition-all">
                                        </div>
                                        <div class="inline-flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400 font-bold">Off:</span>
                                            <input type="number" name="stats[{{ $p->id }}][shoot_off_target]" value="{{ $shootOffTarget }}" min="0" required
                                                class="player-shoot-off-target w-12 bg-slate-50 border border-slate-200 rounded-lg py-1 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold transition-all">
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-350">-</span>
                                @endif
                            </td>

                            <!-- Yellow Cards -->
                            <td class="py-4 px-6 text-center">
                                <select name="stats[{{ $p->id }}][yellow_cards]" required
                                    class="w-16 bg-slate-50 border border-slate-200 rounded-xl px-1.5 py-1.5 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold transition-all">
                                    <option value="0" {{ $yellow == 0 ? 'selected' : '' }}>0</option>
                                    <option value="1" {{ $yellow == 1 ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ $yellow == 2 ? 'selected' : '' }}>2</option>
                                </select>
                            </td>

                            <!-- Red Cards -->
                            <td class="py-4 px-6 text-center">
                                <select name="stats[{{ $p->id }}][red_cards]" required
                                    class="w-16 bg-slate-50 border border-slate-200 rounded-xl px-1.5 py-1.5 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold transition-all">
                                    <option value="0" {{ $red == 0 ? 'selected' : '' }}>0</option>
                                    <option value="1" {{ $red == 1 ? 'selected' : '' }}>1</option>
                                </select>
                            </td>

                            <!-- Minutes Played -->
                            <td class="py-4 px-6 text-center">
                                <div class="inline-flex items-center gap-1 justify-center">
                                    <input type="number" name="stats[{{ $p->id }}][minutes_played]" value="{{ $minutes }}" min="0" max="100" required
                                        class="w-16 bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-center text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-bold transition-all">
                                    <span class="text-[10px] text-slate-450 font-bold">Min</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400 text-sm font-medium">
                                <i class="fa-solid fa-users-slash text-4xl mb-3 block text-slate-300"></i>
                                Belum ada pemain yang terdaftar dalam skuad. Tambahkan pemain terlebih dahulu di menu Manajemen Pemain.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($players) > 0)
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold tracking-wide transition-all shadow-lg shadow-emerald-600/10 hover:shadow-emerald-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> Simpan Statistik Pemain
                </button>
            </div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const shootOnTargetTeamInput = document.getElementById('shootOnTargetTeam');
        const shootOffTargetTeamInput = document.getElementById('shootOffTargetTeam');

        function updateCollectiveShots() {
            let totalOn = 0;
            let totalOff = 0;

            document.querySelectorAll('.player-shoot-on-target').forEach(function(input) {
                const val = parseInt(input.value) || 0;
                totalOn += val;
            });

            document.querySelectorAll('.player-shoot-off-target').forEach(function(input) {
                const val = parseInt(input.value) || 0;
                totalOff += val;
            });

            if (shootOnTargetTeamInput) {
                shootOnTargetTeamInput.value = totalOn;
            }
            if (shootOffTargetTeamInput) {
                shootOffTargetTeamInput.value = totalOff;
            }
        }

        // Add event listeners to all player shoot inputs
        document.querySelectorAll('.player-shoot-on-target, .player-shoot-off-target').forEach(function(input) {
            input.addEventListener('input', updateCollectiveShots);
            input.addEventListener('change', updateCollectiveShots);
        });
    });
</script>
@endsection
