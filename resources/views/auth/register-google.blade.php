<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Pendaftaran - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
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
</head>
<body class="bg-slate-50 font-sans">
    <div class="min-h-screen w-full flex items-center justify-center p-4 relative overflow-x-hidden bg-slate-50">
        <!-- Background lights -->
        <div class="absolute w-[500px] h-[500px] bg-emerald-500/[0.04] rounded-full blur-[100px] -top-40 -left-40 pointer-events-none"></div>
        <div class="absolute w-[500px] h-[500px] bg-teal-500/[0.04] rounded-full blur-[100px] -bottom-40 -right-40 pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">
        <div class="white-panel rounded-3xl p-8 shadow-xl text-slate-800">
            <!-- Brand -->
            <div class="flex items-center justify-center gap-2 mb-6">
                <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}" class="w-7 h-7 object-contain rounded" alt="Logo">
                <span class="font-extrabold text-slate-900 tracking-wide">{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</span>
            </div>

            <!-- Header -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-extrabold text-slate-900">Langkah Terakhir</h2>
                <p class="text-slate-500 text-sm mt-1">Lengkapi pendaftaran dengan membuat tim futsal baru</p>
            </div>

            <!-- Google User Info -->
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 mb-6">
                @if(!empty($googleData['avatar']))
                    <img src="{{ $googleData['avatar'] }}" class="w-12 h-12 rounded-full object-cover border-2 border-emerald-500" alt="Avatar">
                @else
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-lg border-2 border-emerald-500">
                        {{ strtoupper(substr($googleData['name'], 0, 1)) }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <div class="font-bold text-slate-800 text-sm truncate">{{ $googleData['name'] }}</div>
                    <div class="text-slate-500 text-xs truncate">{{ $googleData['email'] }}</div>
                </div>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-650 text-xs font-bold space-y-0.5">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('register.google.complete.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="team_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Tim / Klub Futsal Baru</label>
                    <input type="text" name="team_name" id="team_name" value="{{ old('team_name') }}" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all text-sm"
                        placeholder="Contoh: FC Antigravity" autofocus>
                </div>

                <button type="submit" 
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-md active:scale-[0.98] text-sm">
                    Selesaikan Pendaftaran
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-400 font-semibold">
                Bukan akun Anda? <a href="{{ route('register') }}" class="text-red-500 hover:text-red-600 transition-colors">Batal</a>
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
