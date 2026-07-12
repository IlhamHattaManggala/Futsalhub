<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    <!-- Dynamic SEO Meta Tags -->
    <meta name="description" content="Kebijakan Privasi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Baca bagaimana kami melindungi, mengumpulkan, dan menggunakan data tim futsal Anda.">
    <meta name="keywords" content="kebijakan privasi, data protection, {{ \App\Models\Setting::get('web_keywords', 'futsal, tim futsal, manajemen futsal, papan taktik') }}">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Kebijakan Privasi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="og:description" content="Kebijakan Privasi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Baca bagaimana kami melindungi, mengumpulkan, dan menggunakan data tim futsal Anda.">
    <meta property="og:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Kebijakan Privasi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="twitter:description" content="Kebijakan Privasi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Baca bagaimana kami melindungi, mengumpulkan, dan menggunakan data tim futsal Anda.">
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
            <span class="text-[10px] uppercase font-black tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100/50">LEGALITAS & PRIVASI</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mt-4 leading-tight">Kebijakan Privasi</h1>
            <p class="text-xs text-slate-400 mt-2 font-medium">Terakhir diperbarui: 20 Juni 2026</p>
        </div>

        <!-- Body -->
        <div class="text-slate-650 text-sm md:text-base leading-relaxed space-y-8 max-w-5xl">
            <p>
                Kami di <strong>{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</strong> menghargai privasi Anda. Kebijakan Privasi ini dirancang untuk menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi pribadi Anda saat menggunakan platform manajemen tim futsal multi-tenant kami.
            </p>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">1. Informasi yang Kami Kumpulkan</h3>
            <p>
                Kami mengumpulkan beberapa jenis informasi untuk kebutuhan penyediaan dan peningkatan layanan operasional tim futsal Anda:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2">
                <li><strong>Informasi Pendaftaran Akun:</strong> Nama lengkap, alamat email, kata sandi, peran hak akses (Manajer, Pelatih, Pemain), dan keterikatan tenant tim futsal.</li>
                <li><strong>Informasi Tim Futsal:</strong> Nama tim/klub, deskripsi tim, data logo tim, serta status keaktifan keanggotaan paket premium.</li>
                <li><strong>Data Operasional Klub:</strong> Jadwal kegiatan latihan/tanding, daftar kehadiran absensi, rekap data kas keuangan tim (pemasukan/pengeluaran), serta bukti transfer iuran pemain.</li>
                <li><strong>Informasi Transaksi Premium:</strong> Data tagihan pembayaran premium via gerbang pembayaran otomatis TriPay (seperti channel pembayaran, nominal, referensi transaksi, status, serta catatan sistem). Kami tidak menyimpan kredensial detail kartu atau akun bank Anda.</li>
                <li><strong>Data Statistik Pemain:</strong> Jumlah pertandingan dimainkan, gol dicetak, umpan assist, serta kartu kuning dan merah.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">2. Cara Kami Menggunakan Informasi Anda</h3>
            <p>
                Informasi yang kami kumpulkan digunakan untuk tujuan operasional dan administratif sebagai berikut:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2">
                <li>Menyediakan, mengoperasikan, dan memelihara seluruh fitur di platform (Papan Taktik 2D, Kas Keuangan, Absensi Latihan, dan Statistik).</li>
                <li>Mengelola pendaftaran akun multi-tenant dan memvalidasi akses peran pengguna.</li>
                <li>Memproses transaksi pembaruan plan premium secara otomatis dan aman via TriPay sandbox API.</li>
                <li>Menghubungi Anda terkait pengumuman pemeliharaan sistem, tagihan pembayaran, atau bantuan dukungan teknis.</li>
                <li>Menganalisis penggunaan sistem secara agregat untuk meningkatkan kenyamanan antarmuka pengguna (UX).</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">3. Keamanan Data</h3>
            <p>
                Kami berkomitmen untuk menjaga keamanan data Anda secara serius. Seluruh lalu lintas komunikasi data dilindungi menggunakan enkripsi SSL (Secure Sockets Layer). Kata sandi akun Anda disimpan menggunakan algoritma hashing aman yang tidak dapat didekripsi (bcrypt). Namun, mohon diingat bahwa tidak ada metode transmisi melalui Internet yang 100% aman, sehingga kami tidak dapat menjamin keamanan mutlak data Anda.
            </p>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">4. Hak-Hak Pengguna</h3>
            <p>
                Sebagai pengguna platform, Anda berhak untuk:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2">
                <li>Mengakses dan memperbarui informasi profil akun Anda di halaman Pengaturan Profile.</li>
                <li>Meminta penghapusan akun beserta riwayat data tim Anda dengan menghubungi tim Superadmin kami.</li>
                <li>Menolak memberikan data opsional seperti avatar foto atau nomor punggung, meskipun hal ini dapat membatasi beberapa fungsionalitas visual di dalam dasbor.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">5. Hubungi Kami</h3>
            <p>
                Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini atau pengelolaan data di platform kami, Anda dapat menghubungi kami melalui:
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
