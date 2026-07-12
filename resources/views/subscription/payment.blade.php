@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('header_title', 'Detail Transaksi Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('subscription.upgrade') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 transition-colors">
            <i class="fa-solid fa-arrow-left-long"></i> Kembali ke Layanan Upgrade
        </a>
        <div class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest">
            Merchant Ref: {{ $payment->merchant_ref }}
        </div>
    </div>

    <!-- Main Payment Status Box -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        
        <!-- Left: QRIS/VA info & Status -->
        <div class="md:col-span-7 space-y-6">
            <div class="card-white p-6 rounded-3xl space-y-6">
                <!-- Status Badge header -->
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Status Pembayaran</span>
                        <div class="mt-1 flex items-center gap-2">
                            @if($payment->payment_status === 'paid')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-sm shadow-emerald-500/10 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> LUNAS (PAID)
                                </span>
                            @elseif($payment->payment_status === 'unpaid')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-500 text-white shadow-sm shadow-yellow-500/10 flex items-center gap-1.5 animate-pulse">
                                    <i class="fa-regular fa-clock"></i> MENUNGGU PEMBAYARAN
                                </span>
                            @elseif($payment->payment_status === 'expired')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-500 text-white shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-xmark"></i> KEDALUWARSA (EXPIRED)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation"></i> GAGAL ({{ strtoupper($payment->payment_status) }})
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Metode Pembayaran</span>
                        <div class="text-sm font-extrabold text-slate-800 mt-1 uppercase">
                            {{ $payment->payment_method }}
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Main Info Display -->
                @if($payment->payment_status === 'unpaid')
                    <!-- QRIS Scan Image -->
                    @if($payment->qr_url)
                        <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-200 rounded-3xl text-center">
                            <div class="mb-3">
                                <h4 class="text-xs font-extrabold text-slate-800">Scan Kode QRIS</h4>
                                <p class="text-[9px] text-slate-400 mt-0.5">Pindai kode QRIS di bawah menggunakan aplikasi e-wallet Anda</p>
                            </div>
                            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm relative group shrink-0">
                                <img src="{{ $payment->qr_url }}" class="w-48 h-48 object-cover rounded-xl" alt="QRIS QR Code">
                                <div class="absolute inset-0 bg-emerald-950/60 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300">
                                    <a href="{{ $payment->qr_url }}" target="_blank" class="px-3 py-1.5 bg-white text-emerald-950 rounded-xl font-bold text-xs shadow hover:scale-105 transition-transform flex items-center gap-1">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Penuh
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Virtual Account / Pay Code info -->
                    @if($payment->pay_code)
                        <div class="bg-slate-50/50 border border-slate-200 rounded-2xl p-5 space-y-4">
                            <div>
                                <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Kode Bayar / Virtual Account</span>
                                <div class="mt-1.5 flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3.5 shadow-sm">
                                    <span class="text-base font-black text-slate-900 tracking-widest" id="payCodeText">{{ $payment->pay_code }}</span>
                                    <button type="button" onclick="copyToClipboard('payCodeText', this)" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition-all flex items-center gap-1 focus:outline-none">
                                        <i class="fa-regular fa-copy"></i> Salin
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Total Amount Card -->
                <div class="bg-emerald-50/50 border border-emerald-100/50 rounded-2xl p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-black text-emerald-800 tracking-wider">Jumlah Harus Dibayar</span>
                        <div class="text-lg font-black text-slate-900 mt-1" id="amountText">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    @if($payment->payment_status === 'unpaid')
                        <button type="button" onclick="copyToClipboard('amountText', this)" class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/10 transition-all flex items-center gap-1 focus:outline-none">
                            <i class="fa-regular fa-copy"></i> Salin Nominal
                        </button>
                    @endif
                </div>

                <!-- Utilities for reload status -->
                @if($payment->payment_status === 'unpaid')
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="button" onclick="location.reload()" class="flex-1 py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm focus:outline-none">
                            <i class="fa-solid fa-rotate-right"></i> Cek Status Pembayaran
                        </button>
                        @if($payment->payment_url)
                            <a href="{{ $payment->payment_url }}" target="_blank" class="flex-1 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/10">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Checkout TriPay
                            </a>
                        @endif
                    </div>
                    <div class="text-center text-[10px] text-slate-400 mt-2 font-medium">
                        <i class="fa-solid fa-spinner animate-spin text-emerald-500 mr-1"></i> Halaman mengecek status pembayaran otomatis secara berkala.
                    </div>
                @else
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Catatan Sistem</span>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $payment->admin_notes ?: 'Tidak ada catatan transaksi.' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Instructions Accoridon -->
        <div class="md:col-span-5 space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-widest px-1">Langkah Cara Pembayaran</h3>
            
            @if(!empty($payment->payment_instructions))
                <div class="space-y-3">
                    @foreach($payment->payment_instructions as $index => $instruction)
                        <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                            <button type="button" class="w-full flex items-center justify-between p-4 font-bold text-xs text-slate-800 focus:outline-none hover:bg-slate-50 transition-colors" onclick="toggleInstruction({{ $index }})">
                                <span>{{ $instruction['title'] }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transform transition-transform duration-200" id="icon-{{ $index }}"></i>
                            </button>
                            <div class="hidden p-4 pt-0 border-t border-slate-100 text-xs text-slate-600 bg-slate-50/50" id="content-{{ $index }}">
                                <ol class="list-decimal list-inside space-y-2.5 pt-3">
                                    @foreach($instruction['steps'] as $step)
                                        <li class="leading-relaxed font-semibold pl-1 text-[11px] list-item">{!! $step !!}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card-white p-6 rounded-3xl text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-circle-info text-2xl mb-2 text-slate-350 block"></i>
                    Instruksi cara pembayaran tidak tersedia untuk transaksi ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleInstruction(index) {
        const content = document.getElementById(`content-${index}`);
        const icon = document.getElementById(`icon-${index}`);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    function copyToClipboard(elementId, btn) {
        const textElement = document.getElementById(elementId);
        let textToCopy = textElement.innerText;
        
        // Remove "Rp" and dots if it's amount text to make copying exact amount clean
        if (elementId === 'amountText') {
            textToCopy = textToCopy.replace('Rp', '').replace(/\./g, '').trim();
        }

        navigator.clipboard.writeText(textToCopy).then(() => {
            const originalHtml = btn.innerHTML;
            btn.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-slate-700', 'bg-emerald-500', 'hover:bg-emerald-600', 'text-white');
            btn.classList.add('bg-emerald-600', 'text-white');
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin';
            
            setTimeout(() => {
                btn.classList.remove('bg-emerald-600', 'text-white');
                if (elementId === 'amountText') {
                    btn.classList.add('bg-emerald-500', 'hover:bg-emerald-600', 'text-white');
                } else {
                    btn.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-slate-700');
                }
                btn.innerHTML = originalHtml;
            }, 1800);
        });
    }

    @if($payment->payment_status === 'unpaid')
        // Poll status via AJAX every 6 seconds to avoid heavy full page reloads
        const pollInterval = setInterval(() => {
            fetch(window.location.href, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.payment_status && data.payment_status !== 'unpaid') {
                    clearInterval(pollInterval);
                    
                    let titleText = 'Pembayaran Berhasil!';
                    let textText = 'Selamat! Tim Anda sekarang aktif sebagai premium.';
                    let iconType = 'success';
                    let confirmColor = '#10b981';

                    if (data.payment_status === 'expired') {
                        titleText = 'Transaksi Kedaluwarsa';
                        textText = 'Waktu batas pembayaran Anda telah habis.';
                        iconType = 'warning';
                        confirmColor = '#64748b';
                    } else if (data.payment_status === 'failed') {
                        titleText = 'Pembayaran Gagal';
                        textText = data.admin_notes || 'Transaksi gagal diproses.';
                        iconType = 'error';
                        confirmColor = '#dc2626';
                    }

                    Swal.fire({
                        title: titleText,
                        text: textText,
                        icon: iconType,
                        confirmButtonText: 'Oke',
                        confirmButtonColor: confirmColor,
                        allowOutsideClick: false,
                        customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl' }
                    }).then(() => {
                        location.reload(); // Reload to show the updated paid view status
                    });
                }
            })
            .catch(err => console.error('Polling error:', err));
        }, 6000);
    @endif
</script>
@endsection
