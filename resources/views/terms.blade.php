<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    <!-- Dynamic SEO Meta Tags -->
    <meta name="description" content="Syarat & Ketentuan Penggunaan Layanan {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Baca hak dan kewajiban saat menggunakan platform multi-tenant futsal kami.">
    <meta name="keywords" content="syarat ketentuan, terms of service, {{ \App\Models\Setting::get('web_keywords', 'futsal, tim futsal, manajemen futsal, papan taktik') }}">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Syarat & Ketentuan - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="og:description" content="Syarat & Ketentuan Penggunaan Layanan {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Baca hak dan kewajiban saat menggunakan platform multi-tenant futsal kami.">
    <meta property="og:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Syarat & Ketentuan - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="twitter:description" content="Syarat & Ketentuan Penggunaan Layanan {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Baca hak dan kewajiban saat menggunakan platform multi-tenant futsal kami.">
    <meta property="twitter:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Favicon -->
    <link class="favicon" rel="icon" type="image/x-icon" href="{{ asset(\App\Models\Setting::get('web_favicon', 'favicon.ico')) }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Custom Landing Page Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="antialiased bg-slate-50 min-h-screen flex flex-col">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-[#fafbfc]/85 backdrop-blur-md border-b border-slate-100 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}" class="w-10 h-10 object-contain rounded-xl bg-white p-1 border border-slate-100" alt="Logo">
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight">{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</span>
                </a>

                <!-- Navigation menu (Redirects to landing hash) -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="{{ route('landing') }}#fitur" class="hover:text-emerald-600 transition-colors">Fitur Utama</a>
                    <a href="{{ route('landing') }}#manfaat" class="hover:text-emerald-600 transition-colors">Manfaat Tim</a>
                    <a href="{{ route('landing') }}#langganan" class="hover:text-emerald-600 transition-colors">Paket Langganan</a>
                    <a href="{{ route('landing') }}#statistik" class="hover:text-emerald-600 transition-colors">Statistik & Data</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    @auth
                        @php
                            $user = auth()->user();
                            $correctSlug = $user->isSuperAdmin() ? 'superadmin' : ($user->slug ?? 'user');
                            $dashboardUrl = $user->isSuperAdmin() 
                                ? route('superadmin.dashboard') 
                                : route('dashboard', ['slug' => $correctSlug]);
                        @endphp
                        <a href="{{ $dashboardUrl }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-850 text-white text-sm font-bold transition-all shadow-md flex items-center gap-1.5 active:scale-95">
                            Dashboard <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 text-slate-700 hover:text-emerald-600 text-sm font-bold transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-bold transition-all shadow-md active:scale-95">
                            Daftar Tim Baru
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full space-y-10">
        <!-- Header Title -->
        <div class="border-b border-slate-200 pb-8">
            <span class="text-[10px] uppercase font-black tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100/50">PERATURAN LAYANAN</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mt-4 leading-tight">Syarat & Ketentuan</h1>
            <p class="text-xs text-slate-400 mt-2 font-medium">Terakhir diperbarui: 20 Juni 2026</p>
        </div>

        <!-- Body -->
        <div class="text-slate-650 text-sm md:text-base leading-relaxed space-y-8 max-w-5xl">
            <p>
                Selamat datang di <strong>{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</strong>. Syarat dan Ketentuan berikut mengatur penggunaan Anda atas platform dan layanan kami. Dengan mendaftar, mengakses, atau menggunakan platform kami, Anda setuju untuk terikat oleh ketentuan di bawah ini.
            </p>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">1. Pendaftaran Akun & Tenant Tim</h3>
            <p>
                Layanan kami berbasis multi-tenant yang berarti setiap klub futsal memiliki area dasbor tersendiri.
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2">
                <li>Pendaftar tim pertama secara otomatis akan didaftarkan sebagai pemilik dengan peran <strong>Manager (Management)</strong>.</li>
                <li>Manager bertanggung jawab penuh atas validitas data akun pendaftaran tim, persetujuan penambahan pelatih/pemain, serta pengelolaan anggaran iuran kas tim.</li>
                <li>Setiap pengguna wajib menjaga keamanan kredensial akunnya (email dan kata sandi) dan tidak membagikannya kepada pihak lain.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">2. Batasan Penggunaan Akun Free (Gratis)</h3>
            <p>
                Platform kami menyediakan paket gratis dengan batasan waktu dan operasional sebagai berikut:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2">
                <li>Masa aktif penggunaan gratis: <strong>Maksimal 2 Bulan</strong> terhitung sejak tanggal tim didaftarkan. Setelah melewati batas waktu tersebut, seluruh fitur operasional tim akan ditangguhkan (locked) kecuali melakukan upgrade ke Premium.</li>
                <li>Jumlah maksimal anggota pemain terdaftar: <strong>7 Orang</strong>.</li>
                <li>Akses papan taktik: <strong>Hanya tersedia untuk tim Premium</strong>.</li>
                <li>Jumlah maksimal riwayat entri kas keuangan: <strong>10 Entri</strong>.</li>
                <li>Jumlah maksimal akun Pelatih (Coach) dan Manajer (Management): masing-masing <strong>1 Orang</strong>.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">3. Peningkatan Paket Premium</h3>
            <p>
                Untuk membuka batasan di atas menjadi tanpa batas, Manajer tim dapat mengajukan peningkatan plan ke **Premium Team Suite** secara bulanan:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2">
                <li>Pembayaran tagihan diproses secara aman menggunakan mata uang Rupiah via integrasi sandbox TriPay.</li>
                <li>Setiap transaksi yang berhasil (berstatus paid) secara otomatis akan memperpanjang masa aktif premium tim selama **30 hari** terhitung sejak pembayaran diterima.</li>
                <li>Kami tidak menyediakan sistem pengembalian dana (*refund*) untuk paket premium yang telah dibayarkan dan berhasil diverifikasi oleh sistem.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">4. Konten Pengguna & Papan Taktik</h3>
            <p>
                Anda memegang hak milik penuh atas semua konten taktik, data keuangan, serta materi logistik yang Anda unggah ke dalam platform. Namun, Anda dilarang mengunggah konten yang melanggar hukum, berbau SARA, pornografi, atau melanggar hak cipta pihak ketiga. Kami berhak menonaktifkan akun tenant tim yang terbukti melakukan pelanggaran penyalahgunaan sistem.
            </p>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">5. Batasan Tanggung Jawab</h3>
            <p>
                Platform disediakan "sebagaimana adanya". Kami tidak bertanggung jawab atas kerugian materiil maupun non-materiil yang diakibatkan oleh gangguan jaringan internet, kegagalan sistem sandbox pembayaran, atau hilangnya data yang disebabkan oleh kesalahan pengguna.
            </p>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">6. Hubungi Kami</h3>
            <p>
                Apabila ada ketentuan di dalam syarat ini yang kurang dipahami atau ingin diklarifikasi, Anda dapat berkorespondensi ke admin platform kami melalui:
            </p>
            <div class="bg-slate-100 border border-slate-200 rounded-2xl p-5 space-y-1.5 text-xs font-semibold text-slate-700 max-w-md">
                <div class="flex items-center gap-2"><i class="fa-solid fa-envelope text-emerald-650"></i> Email: support@futsalhub.com</div>
                <div class="flex items-center gap-2"><i class="fa-solid fa-clock text-emerald-650"></i> Jam Kerja: Senin - Jumat, 09.00 - 18.00 WIB</div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('landing.partials.footer')

    <!-- Custom Landing Page Scripts -->
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
