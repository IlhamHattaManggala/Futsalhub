@extends('layouts.app')

@section('title', 'Upgrade Premium')
@section('header_title', 'Upgrade Tim ke Premium')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @if($team->isPremium())
        <!-- Active Premium Status Alert -->
        <div class="relative overflow-hidden p-5 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 shadow-[0_4px_20px_rgba(16,185,129,0.03)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/[0.03] rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center gap-3.5 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 shrink-0">
                    <i class="fa-solid fa-gem text-base animate-[bounce_2.5s_infinite]"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-xs leading-tight">Tim Anda Aktif Premium!</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        Masa aktif premium berakhir pada: 
                        <span class="text-emerald-700 font-extrabold">{{ \Carbon\Carbon::parse($team->premium_until)->translatedFormat('d F Y (H:i)') }} WIB</span>
                    </p>
                </div>
            </div>
            <div class="relative z-10 shrink-0">
                <span class="px-3 py-1 rounded-full bg-emerald-500 text-white font-extrabold text-[9px] uppercase tracking-wider shadow-xs shadow-emerald-500/10">
                    Premium Aktif
                </span>
            </div>
        </div>
    @endif

    <!-- Native Dashboard Layout (Col 12 Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN: Payment Methods, Form & History (Col Span 8) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card 1: Pilih Metode Pembayaran TriPay -->
            <div class="card-white p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Metode Pembayaran Premium</h3>
                        <p class="text-[10px] text-slate-500">Pilih channel pembayaran otomatis melalui TriPay Sandbox</p>
                    </div>
                    <i class="fa-solid fa-wallet text-emerald-600 text-base"></i>
                </div>

                @if(Auth::user()->isManagement() || Auth::user()->isCoach())
                    <form action="{{ route('subscription.submit') }}" method="POST" class="space-y-4 pt-2">
                        @csrf
                        
                        @php
                            $groupedChannels = collect($channels)->groupBy('category');
                        @endphp

                        <div class="space-y-6">
                            @foreach($groupedChannels as $category => $methods)
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">{{ $category }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($methods as $m)
                                            <label class="relative flex items-center justify-between p-3.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl cursor-pointer transition-all select-none group border-selector">
                                                <input type="radio" name="payment_method" value="{{ $m['code'] }}" required class="hidden method-radio">
                                                <div class="flex items-center gap-3">
                                                    @if(!empty($m['icon_url']))
                                                        <img src="{{ $m['icon_url'] }}" alt="{{ $m['name'] }}" class="h-6 w-auto object-contain">
                                                    @else
                                                        <i class="fa-solid fa-wallet text-slate-400 text-lg"></i>
                                                    @endif
                                                    <div>
                                                        <div class="text-xs font-bold text-slate-800">{{ $m['name'] }}</div>
                                                        <div class="text-[9px] text-slate-400">
                                                            Fee: 
                                                            @if(($m['fee_flat'] ?? 0) > 0 && ($m['fee_percent'] ?? 0) > 0)
                                                                Rp {{ number_format($m['fee_flat'], 0, ',', '.') }} + {{ $m['fee_percent'] }}%
                                                            @elseif(($m['fee_flat'] ?? 0) > 0)
                                                                Rp {{ number_format($m['fee_flat'], 0, ',', '.') }}
                                                            @elseif(($m['fee_percent'] ?? 0) > 0)
                                                                {{ $m['fee_percent'] }}%
                                                            @else
                                                                Gratis
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center inner-circle shrink-0">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-500 hidden checked-dot"></div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" 
                                class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-gem text-[10px]"></i> Bayar Sekarang (Rp {{ number_format($price, 0, ',', '.') }})
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-center">
                        <i class="fa-solid fa-circle-info text-slate-450 text-xl mb-2"></i>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">
                            Akun Anda memiliki peran sebagai <strong class="text-slate-800">Pemain</strong>. Hanya akun <strong class="text-slate-800">Pelatih (Coach)</strong> atau <strong class="text-slate-800">Manajer (Management)</strong> yang dapat mengajukan upgrade premium tim.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Card 2: Riwayat Pembayaran Tim -->
            <div class="card-white rounded-2xl overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100 bg-white">
                    <h3 class="text-sm font-extrabold text-slate-900">Riwayat Pembayaran Premium</h3>
                    <p class="text-[10px] text-slate-500">Daftar transaksi penambahan masa aktif premium tim Anda</p>
                </div>

                @if($payments->isEmpty())
                    <div class="text-center py-10 text-slate-400 text-xs font-semibold">
                        <i class="fa-solid fa-receipt text-3xl mb-3 text-slate-350 block"></i>
                        Belum ada riwayat transaksi pengajuan upgrade premium.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-5">Ref / Channel</th>
                                    <th class="py-3 px-5">Pembuat</th>
                                    <th class="py-3 px-5 text-right">Nominal</th>
                                    <th class="py-3 px-5 text-center">Status</th>
                                    <th class="py-3 px-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3.5 px-5">
                                            <div class="font-extrabold text-slate-800">{{ $payment->merchant_ref }}</div>
                                            <div class="text-[9px] text-slate-400 mt-0.5 uppercase">{{ $payment->payment_method }} - {{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('d M Y - H:i') }}</div>
                                        </td>
                                        <td class="py-3.5 px-5">
                                            {{ $payment->user->name }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right font-extrabold text-slate-900">
                                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-center">
                                            @if($payment->payment_status === 'paid')
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 leading-none">Lunas</span>
                                            @elseif($payment->payment_status === 'unpaid')
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-yellow-50 text-yellow-600 border border-yellow-100 leading-none">Unpaid</span>
                                            @elseif($payment->payment_status === 'expired')
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-200 leading-none">Expired</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-600 border border-red-100 leading-none">{{ strtoupper($payment->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-5 text-center">
                                            @if($payment->payment_status === 'unpaid')
                                                <a href="{{ route('subscription.payment', $payment->merchant_ref) }}" 
                                                    class="inline-block px-2.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[9px] rounded-lg shadow-sm transition-all">
                                                    Bayar
                                                </a>
                                            @else
                                                <a href="{{ route('subscription.payment', $payment->merchant_ref) }}" 
                                                    class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-250 border border-slate-200 text-slate-650 font-bold text-[9px] rounded-lg transition-all">
                                                    Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN: Premium Summary & Limitation Info (Col Span 4) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Card 1: Premium Summary Card (Dark Theme) -->
            <div class="bg-gradient-to-br from-emerald-950 to-emerald-900 rounded-2xl p-6 shadow-md text-white relative overflow-hidden group shadow-emerald-950/15">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/[0.04] rounded-full blur-xl pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-500/[0.06] rounded-bl-full flex items-center justify-center">
                    <i class="fa-solid fa-gem text-emerald-400 text-lg animate-[pulse_3s_infinite]"></i>
                </div>
                <div class="mb-4">
                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 border border-emerald-400/20 text-emerald-300 font-bold text-[8px] uppercase tracking-wider">
                        Premium Plan
                    </span>
                    <h4 class="text-base font-black text-white mt-1.5 leading-snug">Premium Team Suite</h4>
                    <p class="text-[10px] text-emerald-250/70 mt-0.5">Buka seluruh batasan penggunaan tim tanpa batasan entri</p>
                </div>

                <div class="space-y-3.5 my-5">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm shrink-0"></i>
                        <span class="text-[10px] text-emerald-100">Anggota Tim <strong>Tanpa Batas</strong> (Pemain & Official)</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm shrink-0"></i>
                        <span class="text-[10px] text-emerald-100">Simpan Taktik Papan Tulis <strong>Tanpa Batas</strong></span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm shrink-0"></i>
                        <span class="text-[10px] text-emerald-100">Pencatatan Transaksi Kas <strong>Tanpa Batas</strong></span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm shrink-0"></i>
                        <span class="text-[10px] text-emerald-100">Bebas Tambah Akun Management & Coach</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-emerald-800/40 flex items-baseline justify-center gap-1 text-center">
                    <span class="text-[10px] font-bold text-emerald-300">Rp</span>
                    <span class="text-xl font-black text-white">{{ number_format($price, 0, ',', '.') }}</span>
                    <span class="text-[10px] font-medium text-emerald-300">/ Bulan</span>
                </div>
            </div>

            <!-- Card 2: Free Limitation Info -->
            <div class="card-white p-5 rounded-2xl">
                <h4 class="text-xs font-black text-slate-800 mb-4"><i class="fa-solid fa-ban text-slate-450 mr-2"></i>Batasan Akun Free</h4>
                
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-group text-slate-400 text-xs w-4"></i>
                            <span class="text-xs text-slate-600">Maksimal Pemain</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">7 Orang</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-palette text-slate-400 text-xs w-4"></i>
                            <span class="text-xs text-slate-600">Papan Taktik</span>
                        </div>
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-100">Hanya Premium</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-slate-400 text-xs w-4"></i>
                            <span class="text-xs text-slate-600">Maksimal Kas</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">10 Entri</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users-gear text-slate-400 text-xs w-4"></i>
                            <span class="text-xs text-slate-600">Maksimal Coach/Manager</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">Masing-masing 1</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.method-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Reset all borders and checked-dots
                document.querySelectorAll('.border-selector').forEach(el => {
                    el.classList.remove('border-emerald-500', 'bg-emerald-50/10');
                    el.classList.add('border-slate-200', 'bg-slate-50');
                    el.querySelector('.checked-dot').classList.add('hidden');
                    el.querySelector('.inner-circle').classList.remove('border-emerald-500');
                    el.querySelector('.inner-circle').classList.add('border-slate-300');
                });
                
                // Style selected element
                if (this.checked) {
                    const parent = this.closest('.border-selector');
                    parent.classList.add('border-emerald-500', 'bg-emerald-50/10');
                    parent.classList.remove('border-slate-200', 'bg-slate-50');
                    parent.querySelector('.checked-dot').classList.remove('hidden');
                    parent.querySelector('.inner-circle').classList.add('border-emerald-500');
                    parent.querySelector('.inner-circle').classList.remove('border-slate-300');
                }
            });
        });
    });
</script>
@endsection
