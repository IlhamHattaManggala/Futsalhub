<!-- Key Features Section -->
<section id="fitur" class="py-20 md:py-28 bg-white border-y border-slate-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4 mb-16 md:mb-24">
            <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 inline-flex px-3.5 py-1.5 rounded-full border border-emerald-100">Fitur Kunci</div>
            <h2 id="featMainTitle" class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                {{ \App\Models\Setting::get('feat_title', 'Semua Kebutuhan Tim Futsal dalam Satu Tempat') }}
            </h2>
            <p id="featMainSubtitle" class="text-slate-500 text-base md:text-lg">
                {{ \App\Models\Setting::get('feat_subtitle', 'Platform kami mengintegrasikan manajemen operasional dan taktis klub agar pelatih, manajer, dan pemain dapat fokus berprestasi.') }}
            </p>
        </div>

        <!-- Features Grid -->
        @php
            $features = [
                [
                    'key' => 'feat1',
                    'default_icon' => 'fa-map',
                    'default_title' => 'Papan Taktik Interaktif',
                    'default_desc' => 'Visualisasikan formasi menyerang dan bertahan (diamond, y-form, dll.) dengan menggeser ikon pemain dan menggambar rute pergerakan bola di lapangan virtual secara dinamis.',
                    'bg' => 'bg-emerald-50',
                    'border' => 'border-emerald-100',
                    'text' => 'text-emerald-600'
                ],
                [
                    'key' => 'feat2',
                    'default_icon' => 'fa-chart-line',
                    'default_title' => 'Statistik Kontribusi Pemain',
                    'default_desc' => 'Catat data performa tiap individu di setiap pertandingan, termasuk jumlah gol, assist, kartu pelanggaran, hingga akumulasi menit bermain untuk melacak pemain terbaik tim.',
                    'bg' => 'bg-teal-50',
                    'border' => 'border-teal-100',
                    'text' => 'text-teal-600'
                ],
                [
                    'key' => 'feat3',
                    'default_icon' => 'fa-money-bill-transfer',
                    'default_title' => 'Pembukuan Keuangan & Kas',
                    'default_desc' => 'Manajemen kas tim yang transparan. Rekam pemasukan dari iuran patungan bulanan serta pengeluaran sewa lapangan atau turnamen demi kesehatan finansial tim.',
                    'bg' => 'bg-amber-50',
                    'border' => 'border-amber-100',
                    'text' => 'text-amber-600'
                ],
                [
                    'key' => 'feat4',
                    'default_icon' => 'fa-calendar-check',
                    'default_title' => 'Agenda & Absensi Digital',
                    'default_desc' => 'Jadwalkan latihan rutin atau sparring dengan mudah. Pemain dapat melakukan konfirmasi kehadiran (Hadir, Izin, Sakit) beserta catatan pendukung langsung di platform.',
                    'bg' => 'bg-blue-50',
                    'border' => 'border-blue-100',
                    'text' => 'text-blue-600'
                ],
                [
                    'key' => 'feat5',
                    'default_icon' => 'fa-cubes',
                    'default_title' => 'Multi-Tenant Terisolasi',
                    'default_desc' => 'Platform kami dirancang untuk menampung banyak tim. Data taktik, keuangan, dan pemain tim Anda tersimpan secara terisolasi dan aman tanpa bisa diakses oleh klub lain.',
                    'bg' => 'bg-purple-50',
                    'border' => 'border-purple-100',
                    'text' => 'text-purple-600'
                ],
                [
                    'key' => 'feat6',
                    'default_icon' => 'fa-gauge-high',
                    'default_title' => 'Dasbor Ringkasan Real-time',
                    'default_desc' => 'Dapatkan gambaran instan mengenai agenda latihan terdekat, kas aktif tim saat ini, pengumuman darurat manajemen, dan taktik terbaru yang siap diterapkan di pertandingan.',
                    'bg' => 'bg-rose-50',
                    'border' => 'border-rose-100',
                    'text' => 'text-rose-600'
                ],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($features as $f)
                @php
                    $icon = \App\Models\Setting::get($f['key'] . '_icon', $f['default_icon']);
                    $title = \App\Models\Setting::get($f['key'] . '_title', $f['default_title']);
                    $desc = \App\Models\Setting::get($f['key'] . '_desc', $f['default_desc']);
                @endphp
                <div class="white-card p-8 rounded-3xl" id="card_{{ $f['key'] }}">
                    <div class="w-12 h-12 rounded-2xl {{ $f['bg'] }} border {{ $f['border'] }} flex items-center justify-center {{ $f['text'] }} mb-6 text-xl">
                        <i class="fa-solid {{ $icon }}" id="icon_{{ $f['key'] }}"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2" id="title_{{ $f['key'] }}">{{ $title }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed" id="desc_{{ $f['key'] }}">
                        {{ $desc }}
                    </p>
                </div>
            @endforeach
        </div>

    </div>
</section>
