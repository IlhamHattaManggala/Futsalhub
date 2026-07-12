@extends('layouts.app')

@section('title', 'Monitor Pembayaran')
@section('header_title', 'Monitor Transaksi Premium')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-150 p-4 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 border border-yellow-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-hourglass-half animate-pulse"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Menunggu Pembayaran</div>
                <div class="text-lg font-black text-slate-800 mt-1">{{ $unpaidCount }} Transaksi</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-150 p-4 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Sukses (Paid)</div>
                <div class="text-lg font-black text-slate-800 mt-1">{{ $paidCount }} Transaksi</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-150 p-4 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 border border-slate-200 flex items-center justify-center text-sm">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Transaksi</div>
                <div class="text-lg font-black text-slate-800 mt-1">{{ $totalCount }} Transaksi</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-150 shadow-sm">
        <form id="payments-filter-form" action="{{ route('superadmin.payments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative col-span-1 md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tim, pembuat, Ref, TriPay ID..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 text-xs transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs transition-all font-semibold">
                    <option value="">Semua Status</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid (Lunas)</option>
                    <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid (Belum Lunas)</option>
                    <option value="expired" @selected(request('status') === 'expired')>Expired (Kadaluwarsa)</option>
                    <option value="failed" @selected(request('status') === 'failed')>Failed (Gagal)</option>
                </select>
            </div>

            <!-- Method Filter & Actions -->
            <div class="flex gap-2">
                <select name="method" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs transition-all font-semibold">
                    <option value="">Semua Metode</option>
                    @foreach($paymentMethods as $m)
                        <option value="{{ $m }}" @selected(request('method') === $m)>{{ strtoupper($m) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs px-4 py-2.5 transition-colors">
                    Cari
                </button>
                <a href="{{ route('superadmin.payments.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs px-3 py-2.5 flex items-center justify-center transition-colors" title="Reset Filter" id="reset-filter-btn">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Payments Table Container -->
    <div id="payments-table-container" class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-150 p-6 shadow-sm overflow-hidden">
            <div class="mb-4">
                <h4 class="text-sm font-black text-slate-800">Daftar Transaksi Premium TriPay</h4>
                <p class="text-[10px] text-slate-400 mt-1">Daftar seluruh riwayat pembayaran otomatis via gateway TriPay Sandbox.</p>
            </div>

            @if($payments->isEmpty())
                <div class="text-center py-12 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <i class="fa-solid fa-inbox text-slate-350 text-2xl mb-1.5"></i>
                    <p class="text-[10px] text-slate-400 font-semibold">Belum ada transaksi pembayaran premium yang ditemukan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-150 bg-slate-50 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                <th class="p-3">Tim Futsal</th>
                                <th class="p-3">Ref Transaksi</th>
                                <th class="p-3 text-right">Nominal</th>
                                <th class="p-3 text-center">Status Pembayaran</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($payments as $payment)
                                <tr class="align-middle hover:bg-slate-50/50 transition-colors">
                                    <td class="p-3">
                                        <div class="font-extrabold text-slate-900">{{ $payment->team->name }}</div>
                                        <div class="text-[9px] text-slate-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('d M Y - H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-extrabold text-slate-800">{{ $payment->merchant_ref }}</div>
                                        <div class="text-[9px] text-slate-400 mt-0.5 uppercase tracking-wider font-bold">
                                            {{ $payment->payment_method ?: 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="p-3 text-right font-extrabold text-slate-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($payment->payment_status === 'paid')
                                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 leading-none">Paid</span>
                                        @elseif($payment->payment_status === 'unpaid')
                                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-yellow-50 text-yellow-600 border border-yellow-100 leading-none animate-pulse">Unpaid</span>
                                        @elseif($payment->payment_status === 'expired')
                                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-slate-50 text-slate-500 border border-slate-200 leading-none">Expired</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-red-50 text-red-600 border border-red-100 leading-none">{{ strtoupper($payment->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <button onclick="showPaymentDetail({{ $payment->id }})" title="Detail Transaksi"
                                            class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 hover:text-blue-700 flex items-center justify-center transition-all text-xs mx-auto">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Custom Light-Themed Pagination Links -->
        @if ($payments->lastPage() > 1)
            <div class="flex items-center justify-between border-t border-slate-100 pt-4 flex-wrap gap-4 font-semibold text-xs text-slate-600">
                <!-- Info -->
                <span class="text-xs text-slate-500 font-semibold">
                    Menampilkan {{ $payments->firstItem() }} sampai {{ $payments->lastItem() }} dari {{ $payments->total() }} hasil
                </span>

                <!-- Page Buttons -->
                <div class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($payments->onFirstPage())
                        <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-350 bg-slate-50 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </span>
                    @else
                        <a href="{{ $payments->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-655 hover:text-emerald-600 hover:border-emerald-500 bg-white flex items-center justify-center text-xs font-bold transition-all ajax-page-link">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </a>
                    @endif

                    {{-- Array of pages --}}
                    @for ($i = 1; $i <= $payments->lastPage(); $i++)
                        @if ($i == $payments->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-extrabold shadow-sm shadow-emerald-500/20">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $payments->url($i) }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-655 hover:text-emerald-600 hover:border-emerald-500 bg-white flex items-center justify-center text-xs font-bold transition-all ajax-page-link">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($payments->hasMorePages())
                        <a href="{{ $payments->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-655 hover:text-emerald-600 hover:border-emerald-500 bg-white flex items-center justify-center text-xs font-bold transition-all ajax-page-link">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    @else
                        <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-350 bg-slate-50 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function maskEmail(email) {
        if (!email) return '';
        var parts = email.split('@');
        if (parts.length !== 2) return email;
        var username = parts[0];
        var domain = parts[1];
        var keepLen = username.length > 4 ? 4 : (username.length > 1 ? 2 : 1);
        var visiblePart = username.substring(0, keepLen);
        var maskedPart = '*'.repeat(username.length - keepLen);
        return visiblePart + maskedPart + '@' + domain;
    }
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('payments-table-container');
    const filterForm = document.getElementById('payments-filter-form');
    
    if (!tableContainer) return;

    // Function to load payments via AJAX
    function loadPayments(url) {
        // Opacity transition for loading state
        tableContainer.style.opacity = '0.4';
        tableContainer.style.transition = 'opacity 0.2s ease-in-out';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Replace table inner content
            const newContent = doc.getElementById('payments-table-container');
            if (newContent) {
                tableContainer.innerHTML = newContent.innerHTML;
            }
            
            // Scroll container smoothly to top
            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(err => {
            console.error('AJAX Error:', err);
        })
        .finally(() => {
            // Restore opacity
            tableContainer.style.opacity = '1';
        });
    }

    // Capture clicks on pagination links using event delegation
    tableContainer.addEventListener('click', function(e) {
        const link = e.target.closest('.ajax-page-link');
        if (link) {
            e.preventDefault();
            const urlStr = link.getAttribute('href');
            if (urlStr) {
                let url = urlStr;
                try {
                    // Convert absolute URL to relative path to solve HTTPS Mixed Content blocking on proxies like Ngrok
                    const parsed = new URL(urlStr, window.location.origin);
                    url = parsed.pathname + parsed.search;
                } catch(err) {
                    // Fallback
                }
                loadPayments(url);
                // Update address bar without refreshing the page
                window.history.pushState({ path: url }, '', url);
            }
        }
    });

    // Capture form submission to apply filters via AJAX
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            const action = filterForm.getAttribute('action') || window.location.pathname;
            let url = `${action}?${params}`;
            try {
                const parsed = new URL(url, window.location.origin);
                url = parsed.pathname + parsed.search;
            } catch(err) {}
            
            loadPayments(url);
            window.history.pushState({ path: url }, '', url);
        });

        // Capture reset button click
        const resetBtn = document.getElementById('reset-filter-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const urlStr = resetBtn.getAttribute('href');
                if (urlStr) {
                    let url = urlStr;
                    try {
                        const parsed = new URL(urlStr, window.location.origin);
                        url = parsed.pathname + parsed.search;
                    } catch(err) {}
                    
                    // Clear fields visually
                    filterForm.querySelector('input[name="search"]').value = '';
                    filterForm.querySelector('select[name="status"]').value = '';
                    filterForm.querySelector('select[name="method"]').value = '';

                    loadPayments(url);
                    window.history.pushState({ path: url }, '', url);
                }
            });
        }
    }
});

// === DETAIL TRANSACTION ===
function showPaymentDetail(id) {
    Swal.fire({
        title: '<i class="fa-solid fa-spinner fa-spin text-emerald-500"></i>',
        html: 'Memuat detail transaksi...',
        showConfirmButton: false,
        allowOutsideClick: true,
        didOpen: () => {
            fetch(`/v1/superadmin/payments/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const statusColors = {
                    'paid': { bg: '#ecfdf5', border: '#a7f3d0', text: '#059669', label: 'LUNAS (PAID)' },
                    'unpaid': { bg: '#fef9c3', border: '#fef08a', text: '#ca8a04', label: 'BELUM LUNAS (UNPAID)' },
                    'expired': { bg: '#f1f5f9', border: '#cbd5e1', text: '#475569', label: 'KADALUWARSA (EXPIRED)' },
                    'failed': { bg: '#fef2f2', border: '#fecaca', text: '#dc2626', label: 'GAGAL' }
                };
                const sc = statusColors[data.payment_status] || { bg: '#f8fafc', border: '#e2e8f0', text: '#64748b', label: data.payment_status.toUpperCase() };

                let paymentActionHtml = '';
                if (data.payment_status === 'unpaid' && data.payment_url) {
                    paymentActionHtml = `
                        <div style="margin-top:14px; text-align:center;">
                            <a href="${data.payment_url}" target="_blank" style="display:inline-flex; align-items:center; gap:6px; background:#10b981; color:white; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:800; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Checkout TriPay
                            </a>
                        </div>
                    `;
                }

                let qrCodeHtml = '';
                if (data.payment_status === 'unpaid' && data.qr_url) {
                    qrCodeHtml = `
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:12px; margin-bottom:12px; text-align:center;">
                            <span style="color:#94a3b8; font-weight:700; font-size:10px; text-transform:uppercase; display:block; margin-bottom:6px;">QR Code QRIS</span>
                            <img src="${data.qr_url}" style="width:120px; height:120px; border-radius:8px; border:1px solid #cbd5e1; margin:0 auto; display:block;">
                        </div>
                    `;
                }

                Swal.update({
                    title: '',
                    html: `
                        <div style="text-align:left; font-family:inherit;">
                            <!-- Header Info -->
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; gap:10px;">
                                <div>
                                    <span style="font-size:10px; color:#94a3b8; font-weight:700; text-transform:uppercase; tracking-wider">Ref Transaksi</span>
                                    <div style="font-size:16px; font-weight:850; color:#0f172a;">${data.merchant_ref}</div>
                                </div>
                                <span style="background:${sc.bg}; color:${sc.text}; border:1px solid ${sc.border}; padding:4px 10px; border-radius:8px; font-size:10px; font-weight:800; text-transform:uppercase; display:inline-block;">${sc.label}</span>
                            </div>

                            <!-- Nominal -->
                            <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:14px; padding:14px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <span style="color:#059669; font-weight:700; font-size:9px; text-transform:uppercase;">Jumlah Tagihan</span>
                                    <div style="font-size:20px; font-weight:900; color:#065f46; margin-top:2px;">${data.amount}</div>
                                </div>
                                <div style="text-align:right;">
                                    <span style="color:#059669; font-weight:700; font-size:9px; text-transform:uppercase;">Metode</span>
                                    <div style="font-size:14px; font-weight:800; color:#065f46; margin-top:2px; text-transform:uppercase;">${data.payment_method}</div>
                                </div>
                            </div>

                            <!-- Payment details (Refs & codes) -->
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:12px; margin-bottom:12px;">
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:11px;">
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:9px; text-transform:uppercase;">TriPay ID Reference</span>
                                        <div style="font-weight:750; color:#334155; margin-top:1px;">${data.reference}</div>
                                    </div>
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:9px; text-transform:uppercase;">Kode Bayar / VA</span>
                                        <div style="font-weight:750; color:#334155; margin-top:1px; font-family:monospace; font-size:12px;">${data.pay_code}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Team & User Info -->
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:12px; margin-bottom:12px;">
                                <div style="font-size:11px; font-weight:800; color:#0f172a; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:8px;">
                                    <i class="fa-solid fa-users text-emerald-500" style="margin-right:4px;"></i>Detail Pengirim & Tim
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:11px; margin-bottom:6px;">
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:9px; text-transform:uppercase;">Nama Tim</span>
                                        <div style="font-weight:750; color:#334155; margin-top:1px;">${data.team_name}</div>
                                    </div>
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:9px; text-transform:uppercase;">Plan Pilihan</span>
                                        <div style="font-weight:800; color:#10b981; margin-top:1px;">${data.team_plan}</div>
                                    </div>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:11px;">
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:9px; text-transform:uppercase;">Pembuat Transaksi</span>
                                        <div style="font-weight:700; color:#334155; margin-top:1px;">${data.user_name}</div>
                                    </div>
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:9px; text-transform:uppercase;">Email</span>
                                        <div style="font-weight:700; color:#334155; margin-top:1px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${maskEmail(data.user_email)}">${maskEmail(data.user_email)}</div>
                                    </div>
                                </div>
                            </div>

                            ${qrCodeHtml}

                            <!-- Catatan Sistem -->
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:12px; margin-bottom:12px; font-size:11px;">
                                <span style="color:#94a3b8; font-weight:650; font-size:9px; text-transform:uppercase; display:block; margin-bottom:2px;">Catatan Transaksi / Sistem</span>
                                <span style="color:#475569; font-weight:600; line-height:1.4;">${data.admin_notes}</span>
                            </div>

                            <!-- Footer Timestamps -->
                            <div style="display:flex; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid #f1f5f9; font-size:10px; color:#94a3b8;">
                                <span><i class="fa-solid fa-calendar-plus" style="margin-right:3px;"></i>Dibuat: ${data.created_at}</span>
                                <span><i class="fa-solid fa-pen" style="margin-right:3px;"></i>Diperbarui: ${data.updated_at}</span>
                            </div>

                            ${paymentActionHtml}
                        </div>
                    `,
                    width: 480,
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#10b981',
                    customClass: { popup: 'rounded-3xl' }
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat detail transaksi.', 'error');
            });
        }
    });
}
</script>
@endsection
