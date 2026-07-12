<!-- Hero Section -->
<section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden">
    <!-- Abstract background lights with parallax -->
    <div class="absolute w-[500px] h-[500px] bg-emerald-500/[0.03] rounded-full blur-[120px] top-10 left-10 pointer-events-none parallax-layer" data-speed="0.12"></div>
    <div class="absolute w-[600px] h-[600px] bg-teal-500/[0.03] rounded-full blur-[140px] bottom-10 right-10 pointer-events-none parallax-layer" data-speed="0.2"></div>
    
    <!-- Extra Floating Tactical Nodes for Parallax Depth -->
    <div class="absolute top-[20%] right-[10%] w-14 h-14 rounded-full border border-emerald-500/10 flex items-center justify-center text-emerald-500/10 text-xs font-bold font-mono tracking-wider parallax-layer select-none pointer-events-none hidden md:flex" data-speed="0.3">P</div>
    <div class="absolute bottom-[25%] left-[5%] w-10 h-10 rounded-full border border-teal-500/10 flex items-center justify-center text-teal-500/10 text-xs font-bold font-mono tracking-wider parallax-layer select-none pointer-events-none hidden md:flex" data-speed="0.22">A</div>
    <div class="absolute top-[45%] left-[2%] w-8 h-8 rounded-full border border-slate-400/10 flex items-center justify-center text-slate-400/10 text-xs font-bold font-mono tracking-wider parallax-layer select-none pointer-events-none hidden md:flex" data-speed="0.15">F</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Side: Copywriting -->
            <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-bold tracking-wider uppercase mx-auto lg:mx-0">
                    <i class="fa-solid fa-trophy"></i> Multi-Tenant Futsal Platform
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.1] sm:leading-tight">
                    Rancang Taktik & <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">
                        Kelola Tim Futsal
                    </span> <br>
                    Secara Modern.
                </h1>
                <p class="text-slate-600 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Platform inovatif yang menyatukan pembuatan papan taktik 2D interaktif, pelacakan statistik kontribusi pemain, transparansi kas keuangan, serta pencatatan absensi tim dalam satu dasbor multi-tenant yang aman.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('login') }}" class="glow-btn w-full sm:w-auto text-center px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-lg shadow-emerald-500/10 active:scale-[0.98]">
                        Mulai Sekarang <i class="fa-solid fa-circle-play ml-2"></i>
                    </a>
                    <a href="#fitur" class="w-full sm:w-auto text-center px-8 py-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold transition-all">
                        Pelajari Fitur
                    </a>
                </div>
            </div>

            <!-- Right Side: Graphic Visual representation of tactical board -->
            <div class="lg:col-span-6 flex justify-center">
                <div class="relative w-full max-w-[480px]">
                    <!-- Visual representation of Pitch Container -->
                    <div class="w-full aspect-[4/3] rounded-3xl p-3 bg-slate-900 shadow-2xl relative overflow-hidden border border-slate-800">
                        <!-- Tactical field lines -->
                        <div class="w-full h-full rounded-2xl tactical-pitch relative flex items-center justify-center border border-slate-800">
                            <!-- Mid line -->
                            <div class="absolute inset-y-0 left-1/2 w-0.5 bg-slate-800"></div>
                            <!-- Center Circle -->
                            <div class="absolute w-24 h-24 rounded-full border-2 border-slate-800 flex items-center justify-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            </div>
                            <!-- Penalty Areas -->
                            <div class="absolute inset-y-0 left-0 w-16 border-r border-t border-b border-slate-800 rounded-r-3xl"></div>
                            <div class="absolute inset-y-0 right-0 w-16 border-l border-t border-b border-slate-800 rounded-l-3xl"></div>
                            
                            <!-- Goalkeepers -->
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-blue-500/90 text-white text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg">1</div>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-red-500/90 text-white text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg">99</div>

                            <!-- Players (Formation Diamond 1-2-1) -->
                            <div class="absolute left-1/4 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg transition-all hover:scale-110 cursor-pointer">4</div> <!-- Anchor -->
                            <div class="absolute left-[40%] top-1/4 w-6 h-6 rounded-full bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg transition-all hover:scale-110 cursor-pointer">7</div> <!-- Left Flank -->
                            <div class="absolute left-[40%] bottom-1/4 w-6 h-6 rounded-full bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg transition-all hover:scale-110 cursor-pointer">11</div> <!-- Right Flank -->
                            <div class="absolute right-1/3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg transition-all hover:scale-110 cursor-pointer">9</div> <!-- Pivot -->

                            <!-- Opponents -->
                            <div class="absolute right-1/4 top-[35%] w-6 h-6 rounded-full bg-slate-700 text-slate-300 text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg">5</div>
                            <div class="absolute right-[45%] top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-700 text-slate-300 text-[10px] font-bold flex items-center justify-center border-2 border-slate-900 shadow-lg">10</div>

                            <!-- Ball -->
                            <div class="absolute left-[48%] top-[45%] w-4 h-4 rounded-full bg-yellow-400 border border-slate-950 flex items-center justify-center shadow-lg animate-bounce">
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-950"></div>
                            </div>

                            <!-- Tactical Drawing Line & Arrow -->
                            <svg class="absolute inset-0 w-full h-full pointer-events-none z-10" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 2 L 8 5 L 0 8 z" fill="#10b981"/>
                                    </marker>
                                </defs>
                                <!-- Drawing line from Anchor (left 1/4 = 25%, top 50%) to Flank (left 40%, top 25%) -->
                                <path d="M 110 150 Q 140 100 180 80" fill="none" stroke="#10b981" stroke-width="2" stroke-dasharray="4,4" marker-end="url(#arrow)" />
                                <!-- Play move pivot -->
                                <path d="M 180 80 L 290 140" fill="none" stroke="#10b981" stroke-width="1.5" stroke-dasharray="2,2" />
                            </svg>
                        </div>
                    </div>

                    <!-- Floating Card: Interactive board tag -->
                    <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-lg flex items-center gap-3 max-w-[220px] parallax-layer z-20" data-speed="-0.08">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <i class="fa-solid fa-pen-ruler"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800">Tactical Board 2D</h4>
                            <p class="text-[10px] text-slate-500">Gambar rute & skema secara real-time</p>
                        </div>
                    </div>

                    <!-- Floating Card: Stats tracker tag -->
                    <div class="absolute -top-6 -left-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-lg flex items-center gap-3 max-w-[220px] parallax-layer z-20" data-speed="0.08">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800">Analisis Performa</h4>
                            <p class="text-[10px] text-slate-500">Gol, assist, kartu, & menit bermain</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
