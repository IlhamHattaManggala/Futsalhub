<!-- Call to Action Banner -->
<section class="py-20 md:py-28 bg-white text-slate-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
        <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
            {!! nl2br(e(\App\Models\Setting::get('cta_title', "Siap Membawa Tim Futsal Anda \nke Level Profesional?"))) !!}
        </h2>
        <p class="text-slate-500 text-base md:text-lg max-w-2xl mx-auto">
            {{ \App\Models\Setting::get('cta_subtitle', 'Daftarkan tim Anda sekarang atau masuk menggunakan akun demonstrasi untuk merasakan kehebatan integrasi papan taktik dan modul manajemen olahraga kami.') }}
        </p>
        <div class="pt-2">
            <a href="{{ route('login') }}" class="glow-btn inline-block px-10 py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-xl shadow-emerald-500/10 active:scale-95 text-lg">
                Gabung Sekarang
            </a>
        </div>
    </div>
</section>
