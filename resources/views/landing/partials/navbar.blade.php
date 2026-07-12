<!-- Header / Navbar -->
<header class="sticky top-0 z-50 bg-[#fafbfc]/85 backdrop-blur-md border-b border-slate-100 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center gap-2.5">
                <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}" class="w-10 h-10 object-contain rounded-xl bg-white p-1 border border-slate-100" alt="Logo">
                <span class="text-xl font-extrabold text-slate-900 tracking-tight">{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</span>
            </div>

            <!-- Navigation menu (Clean Desktop) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#fitur" class="hover:text-emerald-600 transition-colors">Fitur Utama</a>
                <a href="#manfaat" class="hover:text-emerald-600 transition-colors">Manfaat Tim</a>
                <a href="#langganan" class="hover:text-emerald-600 transition-colors">Paket Langganan</a>
                <a href="#statistik" class="hover:text-emerald-600 transition-colors">Statistik & Data</a>
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
                    <a href="{{ route('register') }}" class="glow-btn px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-bold transition-all shadow-md active:scale-95">
                        Daftar Tim Baru
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
