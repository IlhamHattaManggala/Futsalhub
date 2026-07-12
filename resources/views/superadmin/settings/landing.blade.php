@extends('layouts.app')

@section('title', 'Kustomisasi Landing Page')
@section('header_title', 'Kustomisasi Konten Landing Page')

@section('content')
<div class="w-full">
    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-xs font-bold space-y-1 shadow-sm animate-fade-in">
            <div class="flex items-center gap-2 mb-1 text-rose-900">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                <span class="font-extrabold text-[13px]">Gagal memperbarui konten landing page:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5 font-semibold text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.settings.landing.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: Fitur Kunci -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-table-cells"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Bagian 1: Pengaturan Fitur Kunci</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kelola teks pembuka dan ke-6 kartu modul fitur di halaman depan</p>
                </div>
            </div>

            <!-- Header Title & Subtitle -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Utama Fitur (Title)</label>
                        <input type="text" name="feat_title" value="{{ old('feat_title', $settings['feat_title']) }}" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                        @error('feat_title')
                            <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sub-judul Fitur (Subtitle)</label>
                        <textarea name="feat_subtitle" rows="2" required placeholder="Tuliskan deskripsi ringkas pembuka fitur..."
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">{{ old('feat_subtitle', $settings['feat_subtitle']) }}</textarea>
                        @error('feat_subtitle')
                            <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dynamic 6 Cards Editor -->
            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-emerald-600"></i> Edit Detail 6 Kartu Fitur Kunci:
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $cardMeta = [
                            ['num' => 1, 'label' => 'Kartu 1: Taktik', 'color' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                            ['num' => 2, 'label' => 'Kartu 2: Statistik', 'color' => 'bg-teal-50 text-teal-600 border-teal-100'],
                            ['num' => 3, 'label' => 'Kartu 3: Keuangan', 'color' => 'bg-amber-50 text-amber-600 border-amber-100'],
                            ['num' => 4, 'label' => 'Kartu 4: Agenda', 'color' => 'bg-blue-50 text-blue-600 border-blue-100'],
                            ['num' => 5, 'label' => 'Kartu 5: Tenant', 'color' => 'bg-purple-50 text-purple-600 border-purple-100'],
                            ['num' => 6, 'label' => 'Kartu 6: Dasbor', 'color' => 'bg-rose-50 text-rose-600 border-rose-100'],
                        ];

                        $iconList = config('icons', []);
                    @endphp

                    @foreach($cardMeta as $c)
                        @php
                            $i = $c['num'];
                            $iconKey = "feat{$i}_icon";
                            $titleKey = "feat{$i}_title";
                            $descKey = "feat{$i}_desc";
                            
                            $rawIcon = old($iconKey, $settings[$iconKey]);
                            // Clean icon prefix for label matching
                            $cleanIcon = str_replace('fa-solid ', '', $rawIcon);
                            $selectedLabel = $iconList[$cleanIcon] ?? ($iconList[$rawIcon] ?? 'Pilih Ikon...');
                        @endphp
                        <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/30 hover:bg-white hover:border-emerald-300 hover:shadow-md transition-all duration-300 space-y-3.5 relative group">
                            <div class="flex items-center justify-between mb-1 pb-2 border-b border-slate-100">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $c['label'] }}</span>
                                <div class="w-8 h-8 rounded-xl {{ $c['color'] }} flex items-center justify-center text-sm shadow-inner transition-transform group-hover:scale-110">
                                    <i id="previewIcon_{{ $i }}" class="fa-solid {{ $rawIcon }}"></i>
                                </div>
                            </div>

                            <!-- Icon Custom Dropdown Select -->
                            <div class="space-y-1.5" id="dropdownContainer_{{ $i }}">
                                <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest">Pilih Ikon Fitur</label>
                                <div class="relative">
                                    <button type="button" onclick="toggleDropdown({{ $i }})" id="dropdownBtn_{{ $i }}"
                                        class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 flex items-center justify-between text-slate-900 focus:outline-none focus:border-emerald-500 text-xs font-bold transition-all shadow-sm">
                                        <span class="flex items-center gap-2" id="dropdownSelected_{{ $i }}">
                                            <i class="fa-solid {{ $rawIcon }} text-emerald-600"></i>
                                            <span>{{ $selectedLabel }}</span>
                                        </span>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                    </button>
                                    
                                    <!-- Dropdown List Panel -->
                                    <div id="dropdownList_{{ $i }}" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-slate-250 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto p-1.5 space-y-0.5 animate-fade-in border-slate-200">
                                        @foreach($iconList as $val => $label)
                                            <button type="button" onclick="selectIcon({{ $i }}, '{{ $val }}', '{{ $label }}')" 
                                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-[11px] text-left font-bold text-slate-700 hover:bg-emerald-50/50 hover:text-emerald-800 transition-colors">
                                                <span class="w-5 h-5 rounded bg-slate-50 flex items-center justify-center text-slate-500 text-[10px] border border-slate-100">
                                                    <i class="fa-solid {{ $val }}"></i>
                                                </span>
                                                <span>{{ $label }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Hidden Input for Form Submission -->
                                <input type="hidden" name="{{ $iconKey }}" id="iconInput_{{ $i }}" value="{{ $rawIcon }}">
                                @error($iconKey)
                                    <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Judul Fitur</label>
                                <input type="text" name="{{ $titleKey }}" value="{{ old($titleKey, $settings[$titleKey]) }}" required
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs font-bold transition-colors">
                                @error($titleKey)
                                    <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Deskripsi Fitur</label>
                                <textarea name="{{ $descKey }}" rows="4" required placeholder="Tulis deskripsi kartu..."
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs font-semibold leading-relaxed transition-colors">{{ old($descKey, $settings[$descKey]) }}</textarea>
                                @error($descKey)
                                    <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- SECTION 2: Keunggulan Platform -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-square-poll-vertical"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Bagian 2: Pengaturan Keunggulan Platform</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kelola slogan pembuka dan ke-3 kartu manfaat di halaman depan</p>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Utama Keunggulan (Title)</label>
                        <input type="text" name="adv_title" value="{{ old('adv_title', $settings['adv_title']) }}" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                        @error('adv_title')
                            <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sub-judul Keunggulan (Subtitle)</label>
                        <textarea name="adv_subtitle" rows="2" required placeholder="Tuliskan deskripsi ringkas mengapa harus bergabung..."
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">{{ old('adv_subtitle', $settings['adv_subtitle']) }}</textarea>
                        @error('adv_subtitle')
                            <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dynamic 3 Cards Editor -->
            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-teal-600"></i> Edit Detail 3 Kartu Keunggulan:
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $advCardMeta = [
                            ['num' => 1, 'label' => 'Manfaat 1: Manager', 'color' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                            ['num' => 2, 'label' => 'Manfaat 2: Pelatih', 'color' => 'bg-teal-50 text-teal-600 border-teal-100'],
                            ['num' => 3, 'label' => 'Manfaat 3: Pemain', 'color' => 'bg-blue-50 text-blue-600 border-blue-100'],
                        ];
                    @endphp

                    @foreach($advCardMeta as $c)
                        @php
                            $i = $c['num'];
                            $iconKey = "adv{$i}_icon";
                            $titleKey = "adv{$i}_title";
                            $descKey = "adv{$i}_desc";
                            
                            $rawIcon = old($iconKey, $settings[$iconKey]);
                            $cleanIcon = str_replace('fa-solid ', '', $rawIcon);
                            $selectedLabel = $iconList[$cleanIcon] ?? ($iconList[$rawIcon] ?? 'Pilih Ikon...');
                        @endphp
                        <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/30 hover:bg-white hover:border-teal-300 hover:shadow-md transition-all duration-300 space-y-3.5 relative group">
                            <div class="flex items-center justify-between mb-1 pb-2 border-b border-slate-100">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $c['label'] }}</span>
                                <div class="w-8 h-8 rounded-xl {{ $c['color'] }} flex items-center justify-center text-sm shadow-inner transition-transform group-hover:scale-110">
                                    <i id="advPreviewIcon_{{ $i }}" class="fa-solid {{ $rawIcon }}"></i>
                                </div>
                            </div>

                            <!-- Icon Custom Dropdown Select -->
                            <div class="space-y-1.5" id="advDropdownContainer_{{ $i }}">
                                <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest">Pilih Ikon Manfaat</label>
                                <div class="relative">
                                    <button type="button" onclick="toggleAdvDropdown({{ $i }})" id="advDropdownBtn_{{ $i }}"
                                        class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 flex items-center justify-between text-slate-900 focus:outline-none focus:border-teal-500 text-xs font-bold transition-all shadow-sm">
                                        <span class="flex items-center gap-2" id="advDropdownSelected_{{ $i }}">
                                            <i class="fa-solid {{ $rawIcon }} text-teal-600"></i>
                                            <span>{{ $selectedLabel }}</span>
                                        </span>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                    </button>
                                    
                                    <!-- Dropdown List Panel -->
                                    <div id="advDropdownList_{{ $i }}" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-slate-250 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto p-1.5 space-y-0.5 animate-fade-in border-slate-200">
                                        @foreach($iconList as $val => $label)
                                            <button type="button" onclick="selectAdvIcon({{ $i }}, '{{ $val }}', '{{ $label }}')" 
                                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-[11px] text-left font-bold text-slate-700 hover:bg-teal-50/50 hover:text-teal-800 transition-colors">
                                                <span class="w-5 h-5 rounded bg-slate-50 flex items-center justify-center text-slate-500 text-[10px] border border-slate-100">
                                                    <i class="fa-solid {{ $val }}"></i>
                                                </span>
                                                <span>{{ $label }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Hidden Input for Form Submission -->
                                <input type="hidden" name="{{ $iconKey }}" id="advIconInput_{{ $i }}" value="{{ $rawIcon }}">
                                @error($iconKey)
                                    <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Judul Manfaat</label>
                                <input type="text" name="{{ $titleKey }}" value="{{ old($titleKey, $settings[$titleKey]) }}" required
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 focus:outline-none focus:border-teal-500 text-xs font-bold transition-colors">
                                @error($titleKey)
                                    <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Deskripsi Manfaat</label>
                                <textarea name="{{ $descKey }}" rows="4" required placeholder="Tulis deskripsi kartu..."
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 focus:outline-none focus:border-teal-500 text-xs font-semibold leading-relaxed transition-colors">{{ old($descKey, $settings[$descKey]) }}</textarea>
                                @error($descKey)
                                    <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- SECTION 3: Statistik Halaman Depan -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Bagian 3: Pengaturan Statistik Halaman Depan</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kelola 3 angka metrik/statistik pencapaian yang tampil di atas CTA</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $statMeta = [
                        ['num' => 1, 'label' => 'Statistik 1 (Kiri)'],
                        ['num' => 2, 'label' => 'Statistik 2 (Tengah)'],
                        ['num' => 3, 'label' => 'Statistik 3 (Kanan)'],
                    ];
                @endphp

                @foreach($statMeta as $s)
                    @php
                        $i = $s['num'];
                        $valKey = "stat{$i}_val";
                        $labelKey = "stat{$i}_label";
                        $descKey = "stat{$i}_desc";
                    @endphp
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/30 hover:bg-white hover:border-blue-300 hover:shadow-md transition-all duration-300 space-y-3.5 relative">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block pb-2 border-b border-slate-100">{{ $s['label'] }}</span>
                        
                        <!-- Value -->
                        <div>
                            <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Nilai / Angka Metrik</label>
                            <input type="text" name="{{ $valKey }}" value="{{ old($valKey, $settings[$valKey]) }}" required
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 font-extrabold text-xs focus:outline-none focus:border-blue-500 transition-colors">
                            @error($valKey)
                                <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Label -->
                        <div>
                            <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Label / Nama Statistik</label>
                            <input type="text" name="{{ $labelKey }}" value="{{ old($labelKey, $settings[$labelKey]) }}" required
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 font-bold text-xs focus:outline-none focus:border-blue-500 transition-colors">
                            @error($labelKey)
                                <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-[9px] font-black text-slate-550 uppercase tracking-widest mb-1.5">Penjelasan Singkat</label>
                            <textarea name="{{ $descKey }}" rows="3" required placeholder="Tulis penjelasan singkat..."
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-900 focus:outline-none focus:border-blue-500 text-xs font-semibold leading-relaxed transition-colors">{{ old($descKey, $settings[$descKey]) }}</textarea>
                            @error($descKey)
                                <span class="text-red-500 text-[8px] font-bold mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-6 flex justify-end">
            <button type="submit" 
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Konten Landing Page
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let activeDropdownId = null;
    let activeAdvDropdownId = null;

    function toggleDropdown(id) {
        closeAllDropdowns();
        const dropdownList = document.getElementById(`dropdownList_${id}`);
        if (!dropdownList) return;
        dropdownList.classList.toggle('hidden');
        if (!dropdownList.classList.contains('hidden')) {
            activeDropdownId = id;
        }
    }

    function toggleAdvDropdown(id) {
        closeAllDropdowns();
        const dropdownList = document.getElementById(`advDropdownList_${id}`);
        if (!dropdownList) return;
        dropdownList.classList.toggle('hidden');
        if (!dropdownList.classList.contains('hidden')) {
            activeAdvDropdownId = id;
        }
    }

    function selectIcon(id, value, label) {
        const input = document.getElementById(`iconInput_${id}`);
        if (input) input.value = value;

        const displaySpan = document.getElementById(`dropdownSelected_${id}`);
        if (displaySpan) {
            displaySpan.innerHTML = `
                <i class="fa-solid ${value} text-emerald-600"></i>
                <span>${label}</span>
            `;
        }

        closeAllDropdowns();
        updateIconPreview(id, value);
    }

    function selectAdvIcon(id, value, label) {
        const input = document.getElementById(`advIconInput_${id}`);
        if (input) input.value = value;

        const displaySpan = document.getElementById(`advDropdownSelected_${id}`);
        if (displaySpan) {
            displaySpan.innerHTML = `
                <i class="fa-solid ${value} text-teal-650"></i>
                <span>${label}</span>
            `;
        }

        closeAllDropdowns();
        updateAdvIconPreview(id, value);
    }

    function closeAllDropdowns() {
        document.querySelectorAll('[id^="dropdownList_"]').forEach(list => list.classList.add('hidden'));
        document.querySelectorAll('[id^="advDropdownList_"]').forEach(list => list.classList.add('hidden'));
        activeDropdownId = null;
        activeAdvDropdownId = null;
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (activeDropdownId !== null) {
            const container = document.getElementById(`dropdownContainer_${activeDropdownId}`);
            if (container && !container.contains(event.target)) {
                closeAllDropdowns();
            }
        }
        if (activeAdvDropdownId !== null) {
            const container = document.getElementById(`advDropdownContainer_${activeAdvDropdownId}`);
            if (container && !container.contains(event.target)) {
                closeAllDropdowns();
            }
        }
    });

    function updateIconPreview(id, value) {
        const previewElement = document.getElementById(`previewIcon_${id}`);
        if (previewElement) {
            previewElement.className = '';
            
            let iconClass = value.trim();
            if (iconClass) {
                if (!iconClass.startsWith('fa-') && !iconClass.startsWith('fa-solid') && !iconClass.startsWith('fa-regular') && !iconClass.startsWith('fa-brands')) {
                    iconClass = 'fa-' + iconClass;
                }
                
                if (iconClass.includes(' ')) {
                    const classes = iconClass.split(/\s+/);
                    classes.forEach(c => {
                        if (c) previewElement.classList.add(c);
                    });
                } else {
                    previewElement.classList.add('fa-solid');
                    previewElement.classList.add(iconClass);
                }
            } else {
                previewElement.classList.add('fa-solid', 'fa-question');
            }
        }
    }

    function updateAdvIconPreview(id, value) {
        const previewElement = document.getElementById(`advPreviewIcon_${id}`);
        if (previewElement) {
            previewElement.className = '';
            
            let iconClass = value.trim();
            if (iconClass) {
                if (!iconClass.startsWith('fa-') && !iconClass.startsWith('fa-solid') && !iconClass.startsWith('fa-regular') && !iconClass.startsWith('fa-brands')) {
                    iconClass = 'fa-' + iconClass;
                }
                
                if (iconClass.includes(' ')) {
                    const classes = iconClass.split(/\s+/);
                    classes.forEach(c => {
                        if (c) previewElement.classList.add(c);
                    });
                } else {
                    previewElement.classList.add('fa-solid');
                    previewElement.classList.add(iconClass);
                }
            } else {
                previewElement.classList.add('fa-solid', 'fa-question');
            }
        }
    }
</script>
@endsection
