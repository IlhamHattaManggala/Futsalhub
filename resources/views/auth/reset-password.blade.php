<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setel Ulang Kata Sandi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Setel Ulang Kata Sandi - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
    <meta property="og:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

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
                    Sistem informasi manajemen tim futsal modern yang mengintegrasikan pengelolaan pemain, keuangan, jadwal latihan, absensi, statistik, serta visualisasi taktik interaktif dalam satu platform multi-tenant.
                </p>
            </div>
     
            <!-- Right Side: Reset Password Card -->
            <div class="md:col-span-6">
                <div class="white-panel rounded-3xl p-8 shadow-xl text-slate-800">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold text-slate-900">Setel Ulang Sandi</h2>
                        <p class="text-slate-500 text-sm mt-1">Masukkan kata sandi baru untuk mengamankan kembali akun Anda</p>
                    </div>
     
                    @if ($errors->any())
                        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
     
                    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
     
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all font-bold"
                                placeholder="nama@futsal.com">
                        </div>
     
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                                placeholder="Minimal 6 karakter">
                        </div>
     
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                                placeholder="Ulangi kata sandi baru">
                        </div>
     
                        <button type="submit" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-md active:scale-[0.98]">
                            Setel Ulang Sandi
                        </button>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition-colors"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
