@extends('layouts.app')

@section('title', 'Tugas Pemain')
@section('header_title', 'Tugas & Instruksi Pemain')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-list-check text-emerald-600 mr-2"></i>Tugas Pemain</h3>
    <p class="text-xs text-slate-500">
        @if(Auth::user()->isPlayer())
            Pantau tugas pribadi dari staf kepelatihan dan kirim bukti foto penyelesaian Anda secara berkala
        @else
            Berikan tugas harian atau instruksi mandiri untuk memantau kedisiplinan fisik dan lifestyle pemain Anda
        @endif
    </p>
</div>

@if(Auth::user()->isPlayer())
    <!-- ========================================== -->
    <!-- PLAYER VIEW: PERSONAL TASKS DASHBOARD -->
    <!-- ========================================== -->
    <div class="space-y-6">
        <!-- Tabs Header -->
        <div class="flex border-b border-slate-200 gap-4">
            <button onclick="switchTab('active')" id="tabActiveBtn" class="pb-3 text-sm font-bold border-b-2 border-emerald-500 text-emerald-600 transition-all">
                <i class="fa-solid fa-hourglass-half mr-1.5"></i>Tugas Aktif
            </button>
            <button onclick="switchTab('completed')" id="tabCompletedBtn" class="pb-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition-all">
                <i class="fa-solid fa-circle-check mr-1.5"></i>Riwayat Selesai
            </button>
        </div>

        <!-- Tab 1: Active Tasks -->
        <div id="tabActive" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $activeCount = 0;
            @endphp
            @foreach($tasks as $t)
                @if($t->pivot->status === 'Belum Selesai' || $t->pivot->status === 'Mulai')
                    @php
                        $activeCount++;
                        $isOverdue = $t->due_date->isPast();
                        // Color mapping based on category
                        $badgeColors = [
                            'Fisik' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'Teknik' => 'bg-purple-50 text-purple-700 border-purple-100',
                            'Taktik' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'Gaya Hidup' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'Analisis' => 'bg-cyan-50 text-cyan-700 border-cyan-100'
                        ];
                        $badgeColor = $badgeColors[$t->category->name] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                    @endphp
                    <div class="card-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-200">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold border rounded-md uppercase {{ $badgeColor }}">
                                        {{ $t->category->name }}
                                    </span>
                                    @if($t->pivot->status === 'Mulai')
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 rounded-md uppercase">
                                            Sedang Dikerjakan
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-450 font-bold">
                                    Dibuat: {{ $t->coach->name }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">{{ $t->title }}</h4>
                            <p class="text-xs text-slate-500 whitespace-pre-line leading-relaxed">
                                {{ $t->description ?: 'Tidak ada instruksi tambahan.' }}
                            </p>
                            @if($t->pivot->status === 'Mulai')
                                <div class="p-2.5 bg-slate-50 border border-slate-150 rounded-xl mt-3 flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-semibold text-slate-500">
                                        <i class="fa-regular fa-clock text-amber-500 mr-1"></i> Mulai:
                                    </span>
                                    <span class="text-[10px] font-extrabold text-slate-800">
                                        {{ \Carbon\Carbon::parse($t->pivot->started_at)->isoFormat('D MMM YYYY, HH:mm') }} WIB
                                    </span>
                                </div>
                            @endif
                        </div>
 
                        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="text-xs font-semibold {{ $isOverdue ? 'text-red-500 animate-pulse' : 'text-slate-500' }}">
                                <i class="fa-regular fa-calendar mr-1.5"></i>
                                @if($isOverdue)
                                    Terlewat: {{ $t->due_date->isoFormat('D MMM YYYY, HH:mm') }} WIB
                                @else
                                    Tenggat: {{ $t->due_date->isoFormat('D MMM YYYY, HH:mm') }} WIB
                                @endif
                            </div>
                            @if($t->pivot->status === 'Belum Selesai')
                                <button type="button" onclick="startTaskModal('{{ $t->id }}', '{{ $t->title }}')" 
                                    class="px-4 py-2 text-xs font-black bg-slate-900 hover:bg-slate-850 text-white rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5 active:scale-95">
                                    <i class="fa-solid fa-play"></i> Mulai Latihan
                                </button>
                            @else
                                <button type="button" onclick="uploadProofModal('{{ $t->id }}', '{{ $t->title }}')" 
                                    class="px-4 py-2 text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5 active:scale-95 animate-pulse">
                                    <i class="fa-solid fa-circle-check"></i> Selesaikan Latihan
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            @if($activeCount === 0)
                <div class="col-span-full card-white p-12 text-center rounded-3xl text-slate-550 border border-slate-200">
                    <i class="fa-solid fa-clipboard-check text-4xl mb-4 text-slate-300"></i>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Semua Tugas Beres!</h4>
                    <p class="text-xs max-w-sm mx-auto">
                        Tidak ada tugas aktif yang belum diselesaikan saat ini. Tetap jaga kondisi fisik Anda!
                    </p>
                </div>
            @endif
        </div>

        <!-- Tab 2: Completed Tasks History -->
        <div id="tabCompleted" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $completedCount = 0;
            @endphp
            @foreach($tasks as $t)
                @if($t->pivot->status === 'Selesai')
                    @php
                        $completedCount++;
                        $badgeColors = [
                            'Fisik' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'Teknik' => 'bg-purple-50 text-purple-700 border-purple-100',
                            'Taktik' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'Gaya Hidup' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'Analisis' => 'bg-cyan-50 text-cyan-700 border-cyan-100'
                        ];
                        $badgeColor = $badgeColors[$t->category->name] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                        $completedTime = \Carbon\Carbon::parse($t->pivot->completed_at);
                    @endphp
                    <div class="card-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-200 bg-slate-50/50">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold border rounded-md uppercase {{ $badgeColor }}">
                                    {{ $t->category->name }}
                                </span>
                                <span class="px-2 py-0.5 text-[9px] font-extrabold bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-md uppercase">
                                    <i class="fa-solid fa-check mr-1"></i>Selesai
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-800 line-through">{{ $t->title }}</h4>
                            <p class="text-xs text-slate-450 line-clamp-2">
                                {{ $t->description ?: 'Tidak ada instruksi tambahan.' }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col gap-3 text-xs text-slate-500">
                            <div class="flex flex-col gap-1.5">
                                <div>
                                    <i class="fa-solid fa-calendar-check text-emerald-550 mr-1.5"></i>
                                    Selesai pada: {{ $completedTime->isoFormat('D MMM YYYY, HH:mm') }} WIB
                                </div>
                                @if($t->pivot->started_at)
                                    <div>
                                        <i class="fa-regular fa-clock text-amber-500 mr-1.5"></i>
                                        Durasi Latihan: <strong class="text-slate-800">{{ round(abs(\Carbon\Carbon::parse($t->pivot->started_at)->diffInMinutes($completedTime))) }} Menit</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @if($t->pivot->start_proof_image)
                                    <button type="button" onclick="viewProofModal('{{ asset($t->pivot->start_proof_image) }}', 'Foto Awal - {{ $t->title }}')" 
                                        class="px-3 py-1.5 text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl transition-all shadow-sm flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-image text-amber-500"></i> Foto Awal
                                    </button>
                                @endif
                                @if($t->pivot->proof_image)
                                    <button type="button" onclick="viewProofModal('{{ asset($t->pivot->proof_image) }}', 'Foto Akhir - {{ $t->title }}')" 
                                        class="px-3 py-1.5 text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl transition-all shadow-sm flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-image text-emerald-600"></i> Foto Akhir
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if($completedCount === 0)
                <div class="col-span-full card-white p-12 text-center rounded-3xl text-slate-550 border border-slate-200">
                    <i class="fa-solid fa-clock-rotate-left text-4xl mb-4 text-slate-300"></i>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Riwayat</h4>
                    <p class="text-xs max-w-sm mx-auto">
                        Anda belum pernah menyelesaikan tugas. Selesaikan tugas aktif pertama Anda dan kirim buktinya sekarang!
                    </p>
                </div>
            @endif
        </div>
    </div>
@else
    <!-- ========================================== -->
    <!-- COACH & MANAGEMENT VIEW: CREATE & TRACK TASKS -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 @if(Auth::user()->isCoach()) xl:grid-cols-12 @endif gap-8 items-start">
        <!-- Left/Full Column: Tasks List & Track Progress -->
        <div class="@if(Auth::user()->isCoach()) xl:col-span-8 @endif space-y-6">
            <h4 class="text-sm font-black text-slate-450 uppercase tracking-widest">Daftar Penugasan Tim</h4>

            <div class="space-y-4">
                @forelse($tasks as $t)
                    @php
                        $badgeColors = [
                            'Fisik' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'Teknik' => 'bg-purple-50 text-purple-700 border-purple-100',
                            'Taktik' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'Gaya Hidup' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'Analisis' => 'bg-cyan-50 text-cyan-700 border-cyan-100'
                        ];
                        $badgeColor = $badgeColors[$t->category->name] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                        $percent = $t->total_players > 0 ? round(($t->completed_players / $t->total_players) * 100) : 0;
                        $hasPassed = $t->due_date->isPast();
                    @endphp
                    <div class="card-white p-6 rounded-3xl border border-slate-200/70 hover:border-slate-300 transition-all duration-200 shadow-sm space-y-4">
                        <!-- Top header card -->
                        <div class="flex flex-wrap justify-between items-start gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold border rounded-md uppercase {{ $badgeColor }}">
                                        {{ $t->category->name }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold">
                                        Oleh: {{ $t->coach->name }}
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-slate-900 mt-1">{{ $t->title }}</h4>
                            </div>
                            @if(Auth::user()->isCoach())
                            <!-- Delete action -->
                            <form action="{{ route('tasks.destroy', $t->id) }}" method="POST" class="confirm-delete" data-message="Apakah Anda yakin ingin menghapus tugas &quot;{{ $t->title }}&quot; ini? Seluruh data progres pemain dan berkas foto bukti terkait akan dihapus permanen.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-650 bg-slate-50 hover:bg-red-50 rounded-xl transition-all">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>

                        <!-- Description & Due Date -->
                        <div class="text-xs text-slate-600 bg-slate-50/50 border border-slate-100 p-3 rounded-2xl">
                            <div class="font-bold text-slate-450 mb-0.5">Instruksi:</div>
                            <p class="whitespace-pre-line">{{ $t->description ?: 'Tidak ada deskripsi instruksi.' }}</p>
                            <div class="mt-2 text-[10px] {{ $hasPassed ? 'text-red-550' : 'text-slate-500' }} font-bold">
                                <i class="fa-regular fa-clock mr-1.5"></i>Tenggat: {{ $t->due_date->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB 
                                @if($hasPassed) <span class="uppercase text-[9px] tracking-wider ml-1 text-red-600">(Berakhir)</span> @endif
                            </div>
                        </div>

                        <!-- Progress Bar Section -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-600">Progres Skuad</span>
                                <span class="font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
                                    {{ $t->completed_players }} / {{ $t->total_players }} Pemain ({{ $percent }}%)
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-200/50 shadow-inner">
                                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <!-- Collapsible Track List -->
                        <details class="group border-t border-slate-100 pt-3">
                            <summary class="list-none flex items-center justify-between cursor-pointer text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors select-none">
                                <span><i class="fa-solid fa-user-group text-slate-400 mr-1.5"></i>Tinjau Status Pemain</span>
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-250 group-open:rotate-180"></i>
                            </summary>
                            <div class="mt-3 divide-y divide-slate-100 bg-slate-50/50 rounded-2xl border border-slate-100 px-4 py-2">
                                @forelse($t->assignments as $a)
                                    @php
                                        $isDone = $a->status === 'Selesai';
                                        $isStarted = $a->status === 'Mulai';
                                        $doneTime = $a->completed_at ? \Carbon\Carbon::parse($a->completed_at) : null;
                                        $startTime = $a->started_at ? \Carbon\Carbon::parse($a->started_at) : null;
                                    @endphp
                                    <div class="flex items-center justify-between py-2.5 text-xs gap-3">
                                        <div class="flex items-center gap-2">
                                            @if($isDone)
                                                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                            @elseif($isStarted)
                                                <i class="fa-solid fa-person-running text-amber-500 text-sm animate-pulse"></i>
                                            @else
                                                <i class="fa-solid fa-circle-notch text-slate-300 text-sm"></i>
                                            @endif
                                            <span class="font-semibold text-slate-700">{{ $a->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($isDone)
                                                <div class="text-right">
                                                    <span class="block text-[10px] text-slate-400 italic">
                                                        Selesai: {{ $doneTime->isoFormat('D MMM, HH:mm') }}
                                                    </span>
                                                    @if($startTime)
                                                        <span class="block text-[9px] text-slate-400">
                                                            Durasi: <strong class="text-slate-600">{{ round(abs($startTime->diffInMinutes($doneTime))) }} Min</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex gap-1.5">
                                                    @if($a->start_proof_image)
                                                        <button type="button" onclick="viewProofModal('{{ asset($a->start_proof_image) }}', 'Foto Awal: {{ $t->title }} - {{ $a->name }}')" 
                                                            class="px-2 py-0.5 text-[9px] font-black border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-md transition-all shadow-sm flex items-center gap-0.5">
                                                            <i class="fa-solid fa-image text-amber-500"></i> Awal
                                                        </button>
                                                    @endif
                                                    @if($a->proof_image)
                                                        <button type="button" onclick="viewProofModal('{{ asset($a->proof_image) }}', 'Foto Akhir: {{ $t->title }} - {{ $a->name }}')" 
                                                            class="px-2 py-0.5 text-[9px] font-black border border-emerald-150 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md transition-all shadow-sm flex items-center gap-0.5">
                                                            <i class="fa-solid fa-image text-emerald-600"></i> Akhir
                                                        </button>
                                                    @endif
                                                </div>
                                            @elseif($isStarted)
                                                <span class="text-[10px] text-amber-600 italic">
                                                    Mulai: {{ $startTime->isoFormat('HH:mm') }}
                                                </span>
                                                @if($a->start_proof_image)
                                                    <button type="button" onclick="viewProofModal('{{ asset($a->start_proof_image) }}', 'Foto Awal: {{ $t->title }} - {{ $a->name }}')" 
                                                        class="px-2 py-0.5 text-[9px] font-black border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-md transition-all shadow-sm flex items-center gap-0.5">
                                                        <i class="fa-solid fa-image text-amber-500"></i> Awal
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-[9px] font-black border border-slate-200 bg-slate-100 text-slate-500 rounded-md px-1.5 py-0.5 uppercase tracking-wider">
                                                    Belum
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-xs text-slate-400 italic">Tugas ini tidak ditugaskan ke pemain manapun.</div>
                                @endforelse
                            </div>
                        </details>
                    </div>
                @empty
                    <div class="card-white p-12 text-center rounded-3xl text-slate-500">
                        <i class="fa-solid fa-list-check text-4xl mb-4 text-slate-300"></i>
                        <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Tugas</h4>
                        <p class="text-xs max-w-sm mx-auto">
                            Anda belum pernah membuat tugas untuk pemain. Rancang tugas di formulir sebelah kanan.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        @if(Auth::user()->isCoach())
        <!-- Right Column: Create New Task Form -->
        <div class="xl:col-span-4">
            <div class="card-white p-6 rounded-3xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-calendar-plus text-emerald-600 mr-2"></i>Buat Tugas Baru</h3>
                <p class="text-xs text-slate-500">Tugaskan latihan mandiri atau instruksi fisik untuk tim</p>

                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori Tugas</label>
                        <select name="task_category_id" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Tugas / Aktivitas</label>
                        <input type="text" name="title" placeholder="Misal: Tidur Jam 10 Malam" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tenggat Waktu</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="date" id="due_date_date" required min="{{ date('Y-m-d') }}"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                            </div>
                            <div>
                                <input type="time" id="due_date_time" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                            </div>
                        </div>
                        <input type="hidden" name="due_date" id="due_date">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Instruksi / Catatan Tambahan</label>
                        <textarea name="description" rows="3" placeholder="Pemain wajib melampirkan screenshot / foto jam tidur Anda dari HP."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"></textarea>
                    </div>

                    <!-- Assignment Type -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tugaskan Kepada</label>
                        <div class="flex gap-4 items-center">
                            <label class="inline-flex items-center text-xs font-semibold text-slate-600 cursor-pointer">
                                <input type="radio" name="assign_type" value="all" checked onchange="togglePlayerCheckboxes()" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="ml-2">Semua Pemain</span>
                            </label>
                            <label class="inline-flex items-center text-xs font-semibold text-slate-600 cursor-pointer">
                                <input type="radio" name="assign_type" value="specific" onchange="togglePlayerCheckboxes()" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="ml-2">Pilih Pemain Spesifik</span>
                            </label>
                        </div>
                    </div>

                    <!-- Target Players checkboxes (specific selection) -->
                    <div id="playerSelector" class="hidden space-y-2.5 max-h-[180px] overflow-y-auto border border-slate-250 bg-slate-50 rounded-2xl p-3">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Roster Pemain</span>
                        @foreach($players as $p)
                            <label class="flex items-center justify-between hover:bg-slate-100 rounded-xl p-1.5 transition-colors cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 text-xs font-bold text-slate-700">
                                    <input type="checkbox" name="players[]" value="{{ $p->id }}" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                    <span>{{ $p->name }}</span>
                                </div>
                                <span class="text-[10px] font-black bg-slate-200 text-slate-500 px-1.5 py-0.5 rounded-lg border border-slate-250 uppercase">
                                    #{{ $p->number ?: '?' }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                        Publikasikan Tugas
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
@endif
@endsection

@section('scripts')
<script>
    // Stitch date and time inputs for due_date
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('due_date_date');
        const timeInput = document.getElementById('due_date_time');
        const hiddenInput = document.getElementById('due_date');

        if (dateInput && timeInput && hiddenInput) {
            function updateDueDate() {
                if (dateInput.value && timeInput.value) {
                    hiddenInput.value = dateInput.value + ' ' + timeInput.value + ':00';
                } else {
                    hiddenInput.value = '';
                }
            }

            dateInput.addEventListener('change', updateDueDate);
            timeInput.addEventListener('change', updateDueDate);

            const form = dateInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    updateDueDate();
                    if (!hiddenInput.value) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Eror!',
                            text: 'Silakan isi tanggal dan jam tenggat waktu.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }

                    // Client-side past date and time check
                    const selectedDateTime = new Date(hiddenInput.value.replace(' ', 'T'));
                    const now = new Date();
                    if (selectedDateTime < now) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Eror!',
                            text: 'Tenggat waktu (deadline) tidak boleh kurang dari waktu sekarang.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        }
    });

    // Toggle active tab (for Player view)
    function switchTab(tab) {
        const tabActive = document.getElementById('tabActive');
        const tabCompleted = document.getElementById('tabCompleted');
        const tabActiveBtn = document.getElementById('tabActiveBtn');
        const tabCompletedBtn = document.getElementById('tabCompletedBtn');

        if (tab === 'active') {
            tabActive.classList.remove('hidden');
            tabActive.classList.add('grid');
            tabCompleted.classList.add('hidden');
            tabCompleted.classList.remove('grid');

            tabActiveBtn.className = "pb-3 text-sm font-bold border-b-2 border-emerald-500 text-emerald-600 transition-all";
            tabCompletedBtn.className = "pb-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition-all";
        } else {
            tabActive.classList.add('hidden');
            tabActive.classList.remove('grid');
            tabCompleted.classList.remove('hidden');
            tabCompleted.classList.add('grid');

            tabActiveBtn.className = "pb-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition-all";
            tabCompletedBtn.className = "pb-3 text-sm font-bold border-b-2 border-emerald-500 text-emerald-600 transition-all";
        }
    }

    // Toggle player checkboxes selector (for Coach view)
    function togglePlayerCheckboxes() {
        const typeVal = document.querySelector('input[name="assign_type"]:checked').value;
        const selector = document.getElementById('playerSelector');
        if (typeVal === 'specific') {
            selector.classList.remove('hidden');
        } else {
            selector.classList.add('hidden');
            // Uncheck all
            const checkboxes = selector.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(c => c.checked = false);
        }
    }

    // Camera state variables
    let activeStream = null;
    let currentFacingMode = 'user'; // default to front camera for PWA selfie
    let videoDevices = [];
    let currentDeviceIndex = 0;

    function stopActiveStream() {
        if (activeStream) {
            activeStream.getTracks().forEach(track => {
                track.stop();
            });
            activeStream = null;
        }
    }

    async function initCamera(videoEl, errorEl, switchBtn) {
        stopActiveStream();
        
        try {
            if (videoDevices.length === 0) {
                // Request temporary permission to let device labels populate
                const initialStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                initialStream.getTracks().forEach(track => track.stop());
                
                const allDevices = await navigator.mediaDevices.enumerateDevices();
                videoDevices = allDevices.filter(device => device.kind === 'videoinput');
            }
            
            let constraints = {
                audio: false,
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                }
            };
            
            if (videoDevices.length > 1) {
                const device = videoDevices[currentDeviceIndex];
                constraints.video.deviceId = { exact: device.deviceId };
            } else {
                constraints.video.facingMode = currentFacingMode;
            }
            
            activeStream = await navigator.mediaDevices.getUserMedia(constraints);
            videoEl.srcObject = activeStream;
            videoEl.play();
            
            // Mirroring logic for selfies
            const isFront = currentFacingMode === 'user' || 
                (videoDevices[currentDeviceIndex] && videoDevices[currentDeviceIndex].label.toLowerCase().includes('front'));
            if (isFront) {
                videoEl.classList.add('mirrored');
            } else {
                videoEl.classList.remove('mirrored');
            }
            
            if (videoDevices.length > 1 && switchBtn) {
                switchBtn.classList.remove('hidden');
            }
            errorEl.classList.add('hidden');
        } catch (err) {
            console.error('Gagal mengakses kamera:', err);
            errorEl.classList.remove('hidden');
            errorEl.querySelector('span').textContent = 'Gagal mengakses kamera: ' + (err.message || 'Izin ditolak atau sedang digunakan oleh aplikasi lain.');
        }
    }

    // Modal to view uploaded proof image (SweetAlert)
    function viewProofModal(imageUrl, taskTitle) {
        Swal.fire({
            title: 'Tinjau Foto Bukti',
            text: taskTitle,
            imageUrl: imageUrl,
            imageAlt: 'Foto Bukti Tugas',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#64748b',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl',
                image: 'rounded-2xl max-w-full max-h-[350px] object-contain border border-slate-200 shadow-sm'
            }
        });
    }

    // Modal to start task and capture initial photo
    function startTaskModal(taskId, taskTitle) {
        let capturedBase64 = null;
        currentFacingMode = 'user';
        currentDeviceIndex = 0;
        
        Swal.fire({
            title: 'Mulai Latihan & Foto Awal',
            html: `
                <div class="text-center space-y-4 font-sans">
                    <style>
                        .mirrored {
                            transform: scaleX(-1);
                        }
                    </style>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Silakan posisikan diri Anda di depan kamera dan ambil foto awal sebelum memulai latihan:
                        <br><strong class="text-slate-800 text-sm">${taskTitle}</strong>
                    </p>
                    
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 aspect-video shadow-sm">
                        <video id="cameraPreview" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <img id="capturedImagePreview" class="hidden w-full h-full object-cover">
                        
                        <div id="cameraControls" class="absolute bottom-3 left-0 right-0 flex justify-center items-center gap-3 px-4">
                            <button id="btnSwitchCamera" type="button" class="hidden p-2.5 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/20 text-white hover:bg-slate-900/80 active:scale-95 transition-all" title="Ganti Kamera">
                                <i class="fa-solid fa-camera-rotate text-sm"></i>
                            </button>
                            <button id="btnCapture" type="button" class="px-5 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-dot"></i> Ambil Foto
                            </button>
                            <button id="btnRetake" type="button" class="hidden px-5 py-2.5 rounded-full bg-rose-500 hover:bg-rose-650 active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Ulangi Foto
                            </button>
                        </div>
                    </div>
                    
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                    <div id="cameraError" class="hidden p-3 rounded-xl bg-red-50 border border-red-150 text-red-700 text-[11px] leading-normal text-left">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <span>Gagal mengakses kamera. Harap pastikan izin kamera diberikan dan situs diakses melalui koneksi aman (HTTPS).</span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim & Mulai Latihan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            showLoaderOnConfirm: true,
            didOpen: () => {
                const video = Swal.getPopup().querySelector('#cameraPreview');
                const imgPreview = Swal.getPopup().querySelector('#capturedImagePreview');
                const btnCapture = Swal.getPopup().querySelector('#btnCapture');
                const btnRetake = Swal.getPopup().querySelector('#btnRetake');
                const btnSwitch = Swal.getPopup().querySelector('#btnSwitchCamera');
                const errorEl = Swal.getPopup().querySelector('#cameraError');
                const canvas = Swal.getPopup().querySelector('#cameraCanvas');
                
                initCamera(video, errorEl, btnSwitch);
                
                btnSwitch.addEventListener('click', () => {
                    currentDeviceIndex = (currentDeviceIndex + 1) % videoDevices.length;
                    if (videoDevices[currentDeviceIndex].label.toLowerCase().includes('back') || videoDevices[currentDeviceIndex].label.toLowerCase().includes('rear')) {
                        currentFacingMode = 'environment';
                    } else {
                        currentFacingMode = 'user';
                    }
                    initCamera(video, errorEl, btnSwitch);
                });
                
                btnCapture.addEventListener('click', () => {
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    
                    const isFront = currentFacingMode === 'user' || 
                        (videoDevices[currentDeviceIndex] && videoDevices[currentDeviceIndex].label.toLowerCase().includes('front'));
                    if (isFront) {
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                    }
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    capturedBase64 = canvas.toDataURL('image/jpeg', 0.8);
                    imgPreview.src = capturedBase64;
                    imgPreview.classList.remove('hidden');
                    video.classList.add('hidden');
                    
                    btnCapture.classList.add('hidden');
                    btnRetake.classList.remove('hidden');
                    btnSwitch.classList.add('hidden');
                    
                    stopActiveStream();
                });
                
                btnRetake.addEventListener('click', () => {
                    capturedBase64 = null;
                    imgPreview.classList.add('hidden');
                    video.classList.remove('hidden');
                    
                    btnCapture.classList.remove('hidden');
                    btnRetake.classList.add('hidden');
                    
                    initCamera(video, errorEl, btnSwitch);
                });
            },
            willClose: () => {
                stopActiveStream();
            },
            preConfirm: () => {
                if (!capturedBase64) {
                    Swal.showValidationMessage('Anda wajib mengambil foto bukti awal sebelum memulai!');
                    return false;
                }
                
                const startUrl = `{{ url('v1/' . (Auth::user()->isSuperAdmin() ? 'superadmin' : Auth::user()->slug) . '/tasks') }}/${taskId}/start`;
                
                return fetch(startUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        start_proof_image: capturedBase64
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Gagal memulai latihan.');
                        });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Error: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    customClass: {
                        popup: 'rounded-3xl border border-slate-100 shadow-xl'
                    }
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    // Modal to complete task and capture final photo
    function uploadProofModal(taskId, taskTitle) {
        let capturedBase64 = null;
        currentFacingMode = 'user';
        currentDeviceIndex = 0;
        
        Swal.fire({
            title: 'Selesaikan Latihan & Foto Akhir',
            html: `
                <div class="text-center space-y-4 font-sans">
                    <style>
                        .mirrored {
                            transform: scaleX(-1);
                        }
                    </style>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Silakan posisikan diri Anda di depan kamera dan ambil foto bukti akhir penyelesaian latihan:
                        <br><strong class="text-slate-800 text-sm">${taskTitle}</strong>
                    </p>
                    
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 aspect-video shadow-sm">
                        <video id="cameraPreview" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <img id="capturedImagePreview" class="hidden w-full h-full object-cover">
                        
                        <div id="cameraControls" class="absolute bottom-3 left-0 right-0 flex justify-center items-center gap-3 px-4">
                            <button id="btnSwitchCamera" type="button" class="hidden p-2.5 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/20 text-white hover:bg-slate-900/80 active:scale-95 transition-all" title="Ganti Kamera">
                                <i class="fa-solid fa-camera-rotate text-sm"></i>
                            </button>
                            <button id="btnCapture" type="button" class="px-5 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-dot"></i> Ambil Foto
                            </button>
                            <button id="btnRetake" type="button" class="hidden px-5 py-2.5 rounded-full bg-rose-500 hover:bg-rose-600 active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Ulangi Foto
                            </button>
                        </div>
                    </div>
                    
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                    <div id="cameraError" class="hidden p-3 rounded-xl bg-red-50 border border-red-150 text-red-700 text-[11px] leading-normal text-left">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <span>Gagal mengakses kamera. Harap pastikan izin kamera diberikan dan situs diakses melalui koneksi aman (HTTPS).</span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim & Selesaikan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            showLoaderOnConfirm: true,
            didOpen: () => {
                const video = Swal.getPopup().querySelector('#cameraPreview');
                const imgPreview = Swal.getPopup().querySelector('#capturedImagePreview');
                const btnCapture = Swal.getPopup().querySelector('#btnCapture');
                const btnRetake = Swal.getPopup().querySelector('#btnRetake');
                const btnSwitch = Swal.getPopup().querySelector('#btnSwitchCamera');
                const errorEl = Swal.getPopup().querySelector('#cameraError');
                const canvas = Swal.getPopup().querySelector('#cameraCanvas');
                
                initCamera(video, errorEl, btnSwitch);
                
                btnSwitch.addEventListener('click', () => {
                    currentDeviceIndex = (currentDeviceIndex + 1) % videoDevices.length;
                    if (videoDevices[currentDeviceIndex].label.toLowerCase().includes('back') || videoDevices[currentDeviceIndex].label.toLowerCase().includes('rear')) {
                        currentFacingMode = 'environment';
                    } else {
                        currentFacingMode = 'user';
                    }
                    initCamera(video, errorEl, btnSwitch);
                });
                
                btnCapture.addEventListener('click', () => {
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    
                    const isFront = currentFacingMode === 'user' || 
                        (videoDevices[currentDeviceIndex] && videoDevices[currentDeviceIndex].label.toLowerCase().includes('front'));
                    if (isFront) {
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                    }
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    capturedBase64 = canvas.toDataURL('image/jpeg', 0.8);
                    imgPreview.src = capturedBase64;
                    imgPreview.classList.remove('hidden');
                    video.classList.add('hidden');
                    
                    btnCapture.classList.add('hidden');
                    btnRetake.classList.remove('hidden');
                    btnSwitch.classList.add('hidden');
                    
                    stopActiveStream();
                });
                
                btnRetake.addEventListener('click', () => {
                    capturedBase64 = null;
                    imgPreview.classList.add('hidden');
                    video.classList.remove('hidden');
                    
                    btnCapture.classList.remove('hidden');
                    btnRetake.classList.add('hidden');
                    
                    initCamera(video, errorEl, btnSwitch);
                });
            },
            willClose: () => {
                stopActiveStream();
            },
            preConfirm: () => {
                if (!capturedBase64) {
                    Swal.showValidationMessage('Anda wajib mengambil foto bukti akhir sebelum menyelesaikan!');
                    return false;
                }
                
                const uploadUrl = `{{ url('v1/' . (Auth::user()->isSuperAdmin() ? 'superadmin' : Auth::user()->slug) . '/tasks') }}/${taskId}/complete`;
                
                return fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        proof_image: capturedBase64
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Gagal menyelesaikan latihan.');
                        });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Error: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    customClass: {
                        popup: 'rounded-3xl border border-slate-100 shadow-xl'
                    }
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }
</script>
@endsection
