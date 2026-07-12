<!-- Interactive Metric / Statistik Section -->
@php
    $landingStats = $landingStats ?? app(\App\Http\Controllers\SuperadminController::class)->getLandingApi()->getData(true);
@endphp

<section id="statistik" class="py-20 md:py-24 bg-slate-900 text-white relative overflow-hidden">
    <div class="absolute w-[500px] h-[500px] bg-emerald-500/[0.05] rounded-full blur-[120px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            
            <div class="space-y-2">
                <div id="stat1_val" class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">
                    {{ $landingStats['stat1_val'] }}
                </div>
                <div id="stat1_label" class="text-slate-400 text-sm font-semibold tracking-wider uppercase">
                    {{ $landingStats['stat1_label'] }}
                </div>
                <p id="stat1_desc" class="text-slate-500 text-xs mt-1">
                    {{ $landingStats['stat1_desc'] }}
                </p>
            </div>

            <div class="space-y-2">
                <div id="stat2_val" class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">
                    {{ $landingStats['stat2_val'] }}
                </div>
                <div id="stat2_label" class="text-slate-400 text-sm font-semibold tracking-wider uppercase">
                    {{ $landingStats['stat2_label'] }}
                </div>
                <p id="stat2_desc" class="text-slate-500 text-xs mt-1">
                    {{ $landingStats['stat2_desc'] }}
                </p>
            </div>

            <div class="space-y-2">
                <div id="stat3_val" class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">
                    {{ $landingStats['stat3_val'] }}
                </div>
                <div id="stat3_label" class="text-slate-400 text-sm font-semibold tracking-wider uppercase">
                    {{ $landingStats['stat3_label'] }}
                </div>
                <p id="stat3_desc" class="text-slate-500 text-xs mt-1">
                    {{ $landingStats['stat3_desc'] }}
                </p>
            </div>

        </div>
    </div>
</section>
