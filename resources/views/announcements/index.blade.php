@extends('layouts.app')

@section('title', 'Papan Pengumuman')
@section('header_title', 'Pengumuman Resmi Tim')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Bulletin feed -->
    <div class="xl:col-span-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Papan Informasi</h3>
                <p class="text-xs text-slate-500">Pengumuman penting dari pihak manajemen dan pelatih</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-150 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-bullhorn text-emerald-600 text-xs"></i>
                <span class="text-[11px] font-bold text-emerald-700">{{ count($announcements) }} Pengumuman</span>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($announcements as $a)
                <div class="card-white p-6 rounded-3xl space-y-4 border border-slate-100 shadow-sm relative overflow-hidden card-white-hover transition-all duration-300">
                    <!-- Accent background glow -->
                    <div class="absolute w-24 h-24 bg-emerald-500/[0.02] rounded-full blur-2xl top-0 right-0 pointer-events-none"></div>

                    <div class="flex justify-between items-start gap-4">
                        <h4 class="text-base font-extrabold text-slate-900 leading-tight">
                            <i class="fa-solid fa-bullhorn text-emerald-600 mr-2.5"></i>{{ $a->title }}
                        </h4>
                        <span class="text-[10px] text-slate-500 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md shrink-0 font-semibold">
                            <i class="fa-regular fa-clock mr-1 text-slate-400"></i> {{ $a->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line font-medium pl-6">
                        {{ $a->content }}
                    </p>

                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 pl-6">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-[10px] font-extrabold">
                                {{ substr($a->user->name, 0, 1) }}
                            </div>
                            <span>Diposting oleh: <strong class="text-slate-800 font-bold">{{ $a->user->name }}</strong></span>
                        </div>
                        @php
                            $annWaText = "📢 *PENGUMUMAN TIM RESMI* 📢\n\n"
                                       . "*Judul:* " . $a->title . "\n\n"
                                       . $a->content . "\n\n"
                                       . "Diposting oleh: *" . $a->user->name . "*\n"
                                       . "⚽ FutsalHub";
                            $annWaUrl = "https://api.whatsapp.com/send?text=" . urlencode($annWaText);
                        @endphp
                        <a href="{{ $annWaUrl }}" target="_blank" 
                            class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-250 rounded-xl font-bold transition-all flex items-center gap-1.5 w-fit shadow-sm text-[11px]">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> Bagikan WA
                        </a>
                    </div>
                </div>
            @empty
                <div class="card-white p-12 text-center rounded-3xl text-slate-400">
                    <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-4 text-2xl shadow-sm">
                        <i class="fa-solid fa-message-slash text-slate-300"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Pengumuman</h4>
                    <p class="text-xs max-w-sm mx-auto text-slate-500 leading-relaxed">
                        Tidak ada pengumuman resmi yang dibagikan untuk tim ini saat ini.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Create Bulletin Panel (Coach/Manager only) -->
    <div class="xl:col-span-4">
        @if(Auth::user()->isCoach() || Auth::user()->isManagement())
            <div class="card-white p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-pen-to-square text-emerald-600 mr-2"></i>Post Pengumuman</h3>
                    <p class="text-xs text-slate-500">Buat informasi pengumuman baru untuk seluruh anggota tim</p>
                </div>
                
                <form action="{{ route('announcements.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Judul Pengumuman</label>
                        <input type="text" name="title" placeholder="Misal: Info Jersey & Pendaftaran Uji Coba" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Isi Konten Pengumuman</label>
                        <textarea name="content" rows="6" placeholder="Tuliskan isi pesan pengumuman tim futsal Anda secara lengkap..." required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all"></textarea>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                        Siarkan Pengumuman
                    </button>
                </form>
            </div>
        @else
            <!-- Display informational widget -->
            <div class="card-white p-6 rounded-3xl text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mx-auto text-xl shadow-sm">
                    <i class="fa-solid fa-circle-info text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Pemberitahuan Tim</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mt-2">
                        Sebagai pemain, Anda akan menerima pemberitahuan di sini. Pastikan Anda aktif memantau papan informasi untuk jadwal mendesak atau perubahan agenda dari manajer/pelatih.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
