<!-- Benefits / Manfaat Section -->
<section id="manfaat" class="py-20 md:py-28 bg-[#fafbfc]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Side: Graphic list -->
            <div class="lg:col-span-5 space-y-6">
                <div class="text-xs font-bold uppercase tracking-wider text-teal-600 bg-teal-50 inline-flex px-3.5 py-1.5 rounded-full border border-teal-100">Keunggulan Platform</div>
                <h2 id="advMainTitle" class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ \App\Models\Setting::get('adv_title', 'Mengapa Tim Futsal Anda Harus Bergabung?') }}
                </h2>
                <p id="advMainSubtitle" class="text-slate-500 text-base leading-relaxed">
                    {{ \App\Models\Setting::get('adv_subtitle', 'Ucapkan selamat tinggal pada pencatatan manual di grup chat yang berantakan atau buku kas yang rawan hilang. Platform kami membawa pengelolaan tim futsal ke era digital.') }}
                </p>
                <div class="pt-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                        Buka Akun Demo <i class="fa-solid fa-arrow-trend-up"></i>
                    </a>
                </div>
            </div>

            <!-- Right Side: Beautiful timeline or list -->
            @php
                $advantages = [
                    [
                        'key' => 'adv1',
                        'default_icon' => 'fa-user-tie',
                        'default_title' => 'Membantu Tugas Manager Klub',
                        'default_desc' => 'Rekam keuangan bulanan dan absensi latihan secara terpusat untuk efisiensi pengambilan keputusan.',
                        'bg' => 'bg-emerald-50',
                        'text' => 'text-emerald-600'
                    ],
                    [
                        'key' => 'adv2',
                        'default_icon' => 'fa-clipboard-list',
                        'default_title' => 'Mendukung Analisis Pelatih',
                        'default_desc' => 'Rancang strategi pertandingan di papan taktik dan pantau statistik kontribusi pemain untuk menetapkan line-up terbaik.',
                        'bg' => 'bg-teal-50',
                        'text' => 'text-teal-600'
                    ],
                    [
                        'key' => 'adv3',
                        'default_icon' => 'fa-users',
                        'default_title' => 'Keterbukaan Informasi Bagi Pemain',
                        'default_desc' => 'Pemain dapat melihat pengumuman penting, rincian pengeluaran kas, serta statistik performa mereka secara transparan.',
                        'bg' => 'bg-blue-50',
                        'text' => 'text-blue-600'
                    ],
                ];
            @endphp
            <div class="lg:col-span-7 space-y-6">
                @foreach($advantages as $a)
                    @php
                        $icon = \App\Models\Setting::get($a['key'] . '_icon', $a['default_icon']);
                        $title = \App\Models\Setting::get($a['key'] . '_title', $a['default_title']);
                        $desc = \App\Models\Setting::get($a['key'] . '_desc', $a['default_desc']);
                    @endphp
                    <div class="flex gap-4 p-5 bg-white rounded-2xl border border-slate-100 shadow-sm" id="advCard_{{ $a['key'] }}">
                        <div class="w-10 h-10 shrink-0 rounded-xl {{ $a['bg'] }} {{ $a['text'] }} flex items-center justify-center font-bold text-sm animate-pulse-slow">
                            <i class="fa-solid {{ $icon }}" id="advIcon_{{ $a['key'] }}"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1" id="advTitle_{{ $a['key'] }}">{{ $title }}</h4>
                            <p class="text-slate-500 text-sm leading-relaxed" id="advDesc_{{ $a['key'] }}">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>
</section>
