<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(\App\Models\Setting::get('web_favicon', 'favicon.ico')) }}">
    
    <!-- Google Font & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (compiled via cdn just in case/standalone view) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0b1329;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glow-emerald {
            box-shadow: 0 0 50px -10px rgba(16, 185, 129, 0.15);
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.15;
            animation: pulse 8s infinite alternate;
        }
        @keyframes pulse {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.2) translate(20px, 20px); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden px-4">

    <!-- Decorative Animated Background Blobs -->
    <div class="blob bg-emerald-500 w-[300px] h-[300px] top-[-50px] left-[-50px]"></div>
    <div class="blob bg-teal-500 w-[350px] h-[350px] bottom-[-80px] right-[-80px]"></div>

    <!-- Main Card Container (Wide & Compact Centered Layout) -->
    <div class="glass-card glow-emerald w-full max-w-2xl rounded-[28px] py-6 px-8 md:py-8 md:px-10 text-center relative z-10 animate-fade-in shadow-2xl">
        
        <!-- Logo & Branding Badge -->
        <div class="flex justify-center mb-5">
            <div class="bg-slate-950/60 px-4 py-2 rounded-2xl border border-slate-800 shadow-inner flex items-center gap-2.5">
                <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}" 
                     class="h-8 w-auto object-contain" 
                     alt="Logo">
                <span class="text-white font-extrabold text-xs tracking-wider uppercase bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
                    {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}
                </span>
            </div>
        </div>

        <!-- Animated Centerpiece Visual (Compacted) -->
        <div class="relative flex justify-center mb-5">
            <!-- Glowing outer ring -->
            <div class="absolute w-20 h-20 bg-emerald-500/10 rounded-full blur-xl animate-pulse"></div>
            <div class="relative flex items-center justify-center w-16 h-16 bg-slate-950/40 rounded-full border border-slate-800/80 shadow-inner">
                <!-- Outer rotating gear -->
                <i class="fa-solid fa-cog text-emerald-500 text-4xl animate-[spin_10s_linear_infinite]"></i>
                <!-- Inner wrench icon -->
                <i class="fa-solid fa-wrench text-emerald-300 text-xs absolute animate-pulse"></i>
            </div>
        </div>

        <!-- Status Message & Content (Tightened Spacing) -->
        <div class="space-y-3 max-w-lg mx-auto">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="w-1 h-1 rounded-full bg-emerald-400 animate-ping"></span>
                Maintenance Mode
            </span>
            <h1 class="text-xl md:text-2xl font-extrabold text-white tracking-tight leading-tight">
                Sistem Sedang Dipelihara
            </h1>
            <p class="text-xs text-slate-350 font-medium leading-relaxed text-slate-300">
                Kami sedang melakukan pemeliharaan rutin dan pembaruan sistem berkala untuk menghadirkan performa terbaik serta fitur yang lebih optimal bagi seluruh tim.
            </p>
        </div>

        <!-- Separator -->
        <div class="my-5 border-t border-slate-800/60 max-w-md mx-auto"></div>

        <!-- Footer Info & Actions (Clean & Centered) -->
        <div class="space-y-4">
            <div class="text-[10px] font-semibold flex items-center justify-center gap-2 text-slate-400">
                <i class="fa-solid fa-clock text-emerald-500 text-[10px] shrink-0 animate-pulse"></i>
                <span>Kembali beberapa saat lagi. Terima kasih atas kesabaran Anda.</span>
            </div>

            <div class="pt-0.5">
                @auth
                    <!-- Logout form with premium solid red button for ultimate contrast & readability -->
                    <form action="{{ route('logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-red-950/30 cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Keluar dari Akun
                        </button>
                    </form>
                @else
                    <!-- Login redirection link with premium solid emerald button for guests -->
                    <a href="{{ url('/login') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-950/30">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Halaman Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Small Copyright bottom -->
    <div class="mt-6 text-center relative z-10">
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. All Rights Reserved.
        </p>
    </div>

</body>
</html>
