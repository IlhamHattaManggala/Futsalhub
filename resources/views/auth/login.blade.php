<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    <!-- Dynamic SEO Meta Tags -->
    <meta name="description" content="Login ke dasbor tim futsal Anda di {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Kelola papan taktik, kas keuangan, absensi, dan data statistik.">
    <meta name="keywords" content="login, {{ \App\Models\Setting::get('web_keywords', 'futsal, tim futsal, manajemen futsal, papan taktik') }}">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Login - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="og:description" content="Login ke dasbor tim futsal Anda di {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Kelola papan taktik, kas keuangan, absensi, dan data statistik.">
    <meta property="og:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Login - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="twitter:description" content="Login ke dasbor tim futsal Anda di {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Kelola papan taktik, kas keuangan, absensi, dan data statistik.">
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
    <link rel="apple-touch-icon" href="{{ asset('images/web_logo_1780410241.webp') }}">
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
        <!-- Back to Landing Page Button -->
        <div class="absolute top-6 left-6 z-20">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

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
                    Sistem informasi manajemen tim futsal modern yang mengintegrasikan pengelolaan pemain, keuangan, jadwal latihan, absensi, statistik, serta visualisasi taktik interaktif dalam satu platform multi-tenant.
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

            <!-- Right Side: Login Card -->
            <div class="md:col-span-6">
                <div class="white-panel rounded-3xl p-8 shadow-xl text-slate-800">
                    <div class="mb-8">
                        <h2 class="text-2xl font-extrabold text-slate-900">Selamat Datang</h2>
                        <p class="text-slate-500 text-sm mt-1">Silakan masuk untuk mengakses dasbor tim Anda</p>
                    </div>

                    @if (session('error_locked'))
                        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user-lock"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-900">{{ session('error_locked') }}</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        Akun ini ditutup oleh manager tim. Klik tombol di bawah untuk mendapatkan link reaktivasi aman ke email Anda guna memulihkan akun manager beserta seluruh roster tim.
                                    </p>
                                    <form action="{{ route('account.reactivate.send') }}" method="POST" class="pt-2">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ session('locked_email') }}">
                                        <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all active:scale-[0.98]">
                                            <i class="fa-solid fa-paper-plane text-[9px]"></i> Kirim Link Reaktivasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-650 text-sm font-semibold">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-150 text-emerald-650 text-sm font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                                placeholder="nama@futsal.com">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">Lupa Password?</a>
                            </div>
                            <input type="password" name="password" id="password" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                                placeholder="••••••••">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded bg-slate-50 border-slate-200 text-emerald-600 focus:ring-emerald-500">
                            <label for="remember" class="ml-2 text-sm text-slate-600 font-medium">Ingat Saya</label>
                        </div>

                        <button type="submit" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-md active:scale-[0.98]">
                            Masuk Dasbor
                        </button>
                    </form>

                    <div class="relative flex py-3 items-center">
                        <div class="flex-grow border-t border-slate-100"></div>
                        <span class="flex-shrink mx-4 text-slate-400 text-xs font-bold uppercase tracking-wider">Atau</span>
                        <div class="flex-grow border-t border-slate-100"></div>
                    </div>

                    <a href="{{ route('auth.google.redirect') }}" 
                        class="w-full flex items-center justify-center gap-3 py-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold transition-all shadow-sm active:scale-[0.98]">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 object-contain" alt="Google Logo">
                        <span>Masuk dengan Google</span>
                    </a>

                    <div class="mt-6 text-center text-xs text-slate-500 font-semibold">
                        Belum memiliki akun? <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 transition-colors">Daftar Tim Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent double submit and add loading animation
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
