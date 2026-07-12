@extends('layouts.app')

@section('title', 'Masa Uji Coba Habis')
@section('header_title', 'Akses Layanan Ditangguhkan')

@section('content')
<div class="flex items-center justify-center py-10 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-100 shadow-xl p-8 text-center relative overflow-hidden">
        <!-- Accent Top Bar -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-red-500 to-amber-500"></div>

        <!-- Lock Icon with Animated Glow -->
        <div class="relative mx-auto w-24 h-24 mb-6">
            <div class="absolute inset-0 bg-red-100 rounded-full scale-110 opacity-40 animate-ping"></div>
            <div class="relative w-24 h-24 bg-red-50 border border-red-100 text-red-500 rounded-full flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-lock text-4xl"></i>
            </div>
        </div>

        <!-- Badge -->
        <span class="inline-block px-3 py-1 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-full border border-amber-100 mb-4">
            Free Trial Expired (2 Bulan)
        </span>

        <!-- Heading -->
        <h2 class="text-xl font-extrabold text-slate-900 mb-3 tracking-tight">
            Masa Penggunaan Gratis Habis
        </h2>

        <!-- Description based on Role -->
        @if(Auth::user()->isManagement())
            <p class="text-slate-500 text-xs leading-relaxed mb-6">
                Akun tim <strong>{{ $team->name }}</strong> Anda sudah habis untuk penggunaan gratis. 
                Silakan melakukan upgrade ke Premium untuk melanjutkan menggunakan layanan kami dan membuka kembali akses ke seluruh fitur operasional tim futsal Anda.
            </p>

            <!-- Upgrade CTA -->
            <div class="space-y-3 mb-6">
                <a href="{{ route('subscription.upgrade') }}" 
                   class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-650 text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 text-xs">
                    <i class="fa-solid fa-gem text-sm"></i>
                    <span>Upgrade ke Premium Sekarang</span>
                </a>
            </div>
        @else
            <p class="text-slate-500 text-xs leading-relaxed mb-6">
                Masa penggunaan gratis selama 2 bulan untuk tim <strong>{{ $team->name }}</strong> telah berakhir. 
                Akses operasional tim ditangguhkan sementara. Silakan hubungi <strong>Manager</strong> tim Anda untuk melakukan upgrade agar Anda dapat kembali mengakses dashboard dan seluruh fitur layanan kami.
            </p>
        @endif

        <!-- Team Details Box -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/70 text-left space-y-2.5 mb-6 text-xs">
            <div class="flex justify-between text-slate-450 font-semibold">
                <span>Nama Tim Futsal:</span>
                <span class="text-slate-800 font-bold">{{ $team->name }}</span>
            </div>
            <div class="flex justify-between text-slate-450 font-semibold">
                <span>Tanggal Registrasi:</span>
                <span class="text-slate-800 font-bold">
                    {{ $team->created_at ? $team->created_at->translatedFormat('d M Y') : '-' }}
                </span>
            </div>
            <div class="flex justify-between text-slate-450 font-semibold">
                <span>Status Layanan:</span>
                <span class="text-red-600 font-bold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Ditangguhkan (Locked)
                </span>
            </div>
        </div>

        <!-- Footer Help -->
        <p class="text-[10px] text-slate-400">
            Butuh bantuan? Silakan hubungi tim dukungan {{ config('app.name', 'FutsalHub') }}.
        </p>
    </div>
</div>
@endsection
