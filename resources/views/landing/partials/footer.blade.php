<!-- Footer -->
<footer class="bg-gradient-to-b from-slate-900 to-slate-950 text-slate-400 border-t border-slate-800/80 relative overflow-hidden">
    <!-- Subtle background pattern or ambient glow -->
    <div class="absolute w-[300px] h-[300px] bg-emerald-500/[0.02] rounded-full blur-[100px] -bottom-20 -left-20 pointer-events-none"></div>
    <div class="absolute w-[300px] h-[300px] bg-teal-500/[0.02] rounded-full blur-[100px] -top-20 -right-20 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
            
            <!-- Column 1: Branding & Description (Colspan 4) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}" class="w-10 h-10 object-contain rounded-xl bg-white p-1 shadow-md" alt="Logo">
                    <span class="text-xl font-extrabold text-white tracking-tight">{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed font-normal">
                    {{ \App\Models\Setting::get('web_description', 'Sistem Informasi Manajemen Multi-Tenant Tim Futsal Terintegrasi. Menggabungkan inovasi visual taktis dengan manajemen operasional klub secara real-time.') }}
                </p>
                <!-- Social Links -->
                <div class="flex items-center gap-3.5 pt-2">
                    <a href="https://instagram.com" target="_blank" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-slate-400 transition-all duration-300 hover:-translate-y-1 shadow-sm">
                        <i class="fa-brands fa-instagram text-lg"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-slate-400 transition-all duration-300 hover:-translate-y-1 shadow-sm">
                        <i class="fa-brands fa-x text-base"></i>
                    </a>
                    <a href="https://facebook.com" target="_blank" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-slate-400 transition-all duration-300 hover:-translate-y-1 shadow-sm">
                        <i class="fa-brands fa-facebook-f text-base"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-slate-400 transition-all duration-300 hover:-translate-y-1 shadow-sm">
                        <i class="fa-brands fa-youtube text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2: Navigasi Layanan (Colspan 2) -->
            <div class="lg:col-span-2 lg:col-start-6 space-y-4">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Navigasi</h4>
                <ul class="space-y-2.5 text-sm font-semibold">
                    <li>
                        <a href="#fitur" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Fitur Utama</a>
                    </li>
                    <li>
                        <a href="#manfaat" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Keunggulan</a>
                    </li>
                    <li>
                        <a href="#langganan" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Harga Paket</a>
                    </li>
                    <li>
                        <a href="#statistik" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Metrik Data</a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Fitur Unggulan (Colspan 2) -->
            <div class="lg:col-span-2 space-y-4">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Fitur Kunci</h4>
                <ul class="space-y-2.5 text-sm font-semibold">
                    <li>
                        <a href="#fitur" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Papan Taktik 2D</a>
                    </li>
                    <li>
                        <a href="#fitur" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Manajemen Kas</a>
                    </li>
                    <li>
                        <a href="#fitur" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Absensi Latihan</a>
                    </li>
                    <li>
                        <a href="#fitur" class="hover:text-emerald-400 hover:translate-x-1 inline-block transition-all duration-300">Statistik Pemain</a>
                    </li>
                </ul>
            </div>

            <!-- Column 4: Dukungan & Hubungi (Colspan 3) -->
            <div class="lg:col-span-3 space-y-4">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Hubungi Kami</h4>
                <p class="text-sm text-slate-400 leading-relaxed font-normal">
                    Punya pertanyaan atau butuh bantuan integrasi? Hubungi tim support kami:
                </p>
                <div class="space-y-2.5 text-sm">
                    <a href="mailto:support@futsalhub.com" class="flex items-center gap-2.5 hover:text-emerald-400 transition-colors">
                        <i class="fa-solid fa-envelope text-slate-500"></i>
                        <span>support@futsalhub.com</span>
                    </a>
                    <div class="flex items-center gap-2.5 text-slate-400">
                        <i class="fa-solid fa-clock text-slate-500"></i>
                        <span>Senin - Jumat, 09.00 - 18.00 WIB</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Divider -->
        <div class="border-t border-slate-800/80 my-10"></div>

        <!-- Bottom Row: Credit, License, Security & Badges -->
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6 text-xs font-medium text-slate-500">
            <!-- Left: University Logo & Researcher Credit -->
            <div class="flex items-center gap-3 order-1">
                <img src="{{ asset(\App\Models\Setting::get('university_logo', 'images/Logo Universitas Harkat Negeri.webp')) }}" class="w-10 h-10 object-contain rounded-lg bg-white p-1 shadow-md" alt="Logo Universitas">
                <div class="text-left leading-tight">
                    <p class="font-semibold text-slate-300">{{ \App\Models\Setting::get('researcher_name', 'Ilham Hatta Manggala') }}</p>
                    <p>Peneliti &amp; Developer</p>
                </div>
            </div>

            <!-- Center: Copyright -->
            <div class="text-center order-2">
                &copy; 2026 {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}. Hak Cipta Dilindungi.
            </div>

            <!-- Right: Links & Security Badge -->
            <div class="flex flex-wrap items-center justify-center gap-6 order-3">
                <a href="{{ route('privacy') }}" class="hover:text-slate-350 transition-colors">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-slate-350 transition-colors">Syarat & Ketentuan</a>
                <span class="flex items-center gap-1.5 text-slate-500 select-none">
                    <i class="fa-solid fa-shield-halved text-emerald-600/85"></i> SSL Secure Connection
                </span>
            </div>
        </div>
    </div>
</footer>
