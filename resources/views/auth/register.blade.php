<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tim Baru - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    <!-- Dynamic SEO Meta Tags -->
    <meta name="description" content="Daftarkan klub futsal Anda di {{ \App\Models\Setting::get('web_name', 'FutsalHub') }} sekarang untuk mengaktifkan papan taktik interaktif dan manajemen tim.">
    <meta name="keywords" content="register, daftar tim, {{ \App\Models\Setting::get('web_keywords', 'futsal, tim futsal, manajemen futsal, papan taktik') }}">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Daftar Tim Baru - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="og:description" content="Daftarkan klub futsal Anda di {{ \App\Models\Setting::get('web_name', 'FutsalHub') }} sekarang untuk mengaktifkan papan taktik interaktif dan manajemen tim.">
    <meta property="og:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Daftar Tim Baru - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="twitter:description" content="Daftarkan klub futsal Anda di {{ \App\Models\Setting::get('web_name', 'FutsalHub') }} sekarang untuk mengaktifkan papan taktik interaktif dan manajemen tim.">
    <meta property="twitter:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(\App\Models\Setting::get('web_favicon', 'favicon.ico')) }}">
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
    <style>
        body {
            background: radial-gradient(circle at center, #ffffff 0%, #f8fafc 100%);
            font-family: 'Outfit', sans-serif;
        }
        .white-panel {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
    </style>
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FutsalHub">
    <link class="apple-touch-icon-register" rel="apple-touch-icon" href="{{ asset('images/web_logo_1780410241.webp') }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('serviceworker.js') }}")
                    .then(reg => console.log('PWA Service Worker registered successfully.'))
                    .catch(err => console.error('PWA Service Worker registration failed.', err));
            });
        }
    </script>
</head>
<body class="bg-slate-50 font-sans">
    <div class="min-h-screen w-full flex items-center justify-center p-4 relative overflow-x-hidden bg-slate-50">
        <!-- Background lights -->
        <div class="absolute w-[500px] h-[500px] bg-emerald-500/[0.04] rounded-full blur-[100px] -top-40 -left-40 pointer-events-none"></div>
        <div class="absolute w-[500px] h-[500px] bg-teal-500/[0.04] rounded-full blur-[100px] -bottom-40 -right-40 pointer-events-none"></div>

        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
            <!-- Left Side: App Branding & Intro -->
            <div class="md:col-span-6 text-slate-800 space-y-6 text-center md:text-left px-4">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-semibold tracking-wider uppercase">
                    <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}" class="w-5 h-5 object-contain rounded" alt="Logo">
                    <span>{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-tight text-slate-900 break-words">
                    Rancang Bangun <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">
                        Tactical Board & Team Management
                    </span>
                </h1>
                <p class="text-slate-600 text-base max-w-md mx-auto md:mx-0">
                    Daftarkan klub futsal Anda hari ini untuk mendapatkan papan taktik 2D interaktif mandiri, pencatatan kas terpusat, jadwal latihan, absensi, serta pemantauan data performa.
                </p>
                <div class="hidden md:grid grid-cols-3 gap-4 pt-4">
                    <div class="p-3 bg-white rounded-2xl border border-slate-100 text-center shadow-sm">
                        <div class="text-2xl font-bold text-emerald-600 mb-1"><i class="fa-solid fa-fire-flame-simple"></i></div>
                        <div class="text-xs font-bold text-slate-700">Tactical Board</div>
                    </div>
                    <div class="p-3 bg-white rounded-2xl border border-slate-100 text-center shadow-sm">
                        <div class="text-2xl font-bold text-emerald-600 mb-1"><i class="fa-solid fa-chart-line"></i></div>
                        <div class="text-xs font-bold text-slate-700">Statistik Pemain</div>
                    </div>
                    <div class="p-3 bg-white rounded-2xl border border-slate-100 text-center shadow-sm">
                        <div class="text-2xl font-bold text-emerald-600 mb-1"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div class="text-xs font-bold text-slate-700">Keuangan Kas</div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Register Card -->
            <div class="md:col-span-6">
                <div class="white-panel rounded-3xl p-8 shadow-xl text-slate-800">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold text-slate-900">Pendaftaran Tim</h2>
                        <p class="text-slate-500 text-sm mt-1">Buat tim baru dan akun manajer Anda sekarang</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-650 text-xs font-bold space-y-0.5">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                <b>@endforeach</b>
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="team_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Tim / Klub Futsal</label>
                            <input type="text" name="team_name" id="team_name" value="{{ old('team_name') }}" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"
                                placeholder="Contoh: FC Antigravity">
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Manajer</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"
                                placeholder="Nama Manajer">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Manajer</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"
                                placeholder="manager@domain.com">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                                <input type="password" name="password" id="password" required 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"
                                    placeholder="••••••••">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" 
                                class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-md active:scale-[0.98] text-sm">
                                Daftarkan Tim Baru
                            </button>
                        </div>
                    </form>

                    <div class="relative flex py-3 items-center">
                        <div class="flex-grow border-t border-slate-100"></div>
                        <span class="flex-shrink mx-4 text-slate-400 text-xs font-bold uppercase tracking-wider">Atau</span>
                        <div class="flex-grow border-t border-slate-100"></div>
                    </div>

                    <a href="{{ route('auth.google.redirect') }}" 
                        class="w-full flex items-center justify-center gap-3 py-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold transition-all shadow-sm active:scale-[0.98] text-sm">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 object-contain" alt="Google Logo">
                        <span>Daftar dengan Google</span>
                    </a>

                    <div class="mt-6 text-center text-xs text-slate-500 font-semibold">
                        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 transition-colors">Masuk Dasbor</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                if (submitBtn.classList.contains('form-submitting')) {
                    e.preventDefault();
                    return;
                }
                submitBtn.classList.add('form-submitting');

                const rect = submitBtn.getBoundingClientRect();
                if (rect.width > 0) {
                    submitBtn.style.width = rect.width + 'px';
                }

                submitBtn.innerHTML = `
                    <span class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memproses...</span>
                    </span>
                `;

                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                }, 10);
            }
        });
    </script>
</body>
</html>
