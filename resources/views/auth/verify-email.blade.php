<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Verifikasi Email - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}">
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
                    Verifikasi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">
                        Alamat Email Anda
                    </span>
                </h1>
                <p class="text-slate-600 text-base max-w-md mx-auto md:mx-0">
                    Sebelum menggunakan layanan {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}, silakan periksa kotak masuk email Anda dan klik link tautan verifikasi yang kami kirimkan. Hal ini penting untuk memastikan email Anda aktif dan valid.
                </p>
            </div>
 
        <!-- Right Side: Email Verification Notice Card -->
        <div class="md:col-span-6">
            <div class="white-panel rounded-3xl p-8 shadow-xl text-slate-800">
                <div class="mb-6 text-center md:text-left">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-sm mb-4 mx-auto md:mx-0">
                        <i class="fa-regular fa-envelope-open"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Konfirmasi Email</h2>
                    <p class="text-slate-500 text-sm mt-1 leading-relaxed">
                        Kami telah mengirimkan link verifikasi ke email Anda. Silakan klik link tersebut untuk mengaktifkan akun Anda.
                    </p>
                </div>

                @if (session('message'))
                    <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-bold shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-sm text-emerald-600"></i>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                <div class="space-y-4">
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10 active:scale-[0.99] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <form action="{{ route('logout') }}" method="POST" class="pt-2 border-t border-slate-100">
                        @csrf
                        <button type="submit" 
                            class="w-full py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs uppercase tracking-wider transition-all border border-slate-200 active:scale-[0.99] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i> Gunakan Akun Lain / Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
