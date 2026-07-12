@extends('layouts.app')

@section('title', 'Kas Keuangan')
@section('header_title', 'Keuangan Kas Tim')

@section('content')
<!-- Financial Status Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Balance -->
    <div class="card-white p-6 rounded-3xl border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Saldo Kas</span>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1.5">
                Rp {{ number_format($balance, 0, ',', '.') }}
            </h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl shadow-sm">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>

    <!-- Income -->
    <div class="card-white p-6 rounded-3xl border-l-4 border-teal-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pemasukan</span>
            <h3 class="text-2xl font-extrabold text-teal-600 mt-1.5">
                Rp {{ number_format($income, 0, ',', '.') }}
            </h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-650 text-xl shadow-sm">
            <i class="fa-solid fa-circle-down"></i>
        </div>
    </div>

    <!-- Expense -->
    <div class="card-white p-6 rounded-3xl border-l-4 border-red-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pengeluaran</span>
            <h3 class="text-2xl font-extrabold text-red-600 mt-1.5">
                Rp {{ number_format($expense, 0, ',', '.') }}
            </h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-600 text-xl shadow-sm">
            <i class="fa-solid fa-circle-up"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Finances History list table -->
    <div class="xl:col-span-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Riwayat Transaksi</h3>
                <p class="text-xs text-slate-500">Arus kas keluar masuk yang tercatat di dalam kas tim</p>
            </div>
            <div class="flex items-center gap-3 w-fit">
                <a href="{{ route('finances.export') }}" target="_blank" 
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Cetak Kas (PDF)
                </a>
                <div class="bg-slate-100 border border-slate-200 px-3 py-2 rounded-xl flex items-center gap-1.5 shadow-inner">
                    <i class="fa-solid fa-receipt text-slate-500 text-xs"></i>
                    <span class="text-[11px] font-bold text-slate-700">{{ count($finances) }} Transaksi</span>
                </div>
            </div>
        </div>

        <div class="card-white rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6">Tanggal</th>
                            <th class="py-4 px-6">Deskripsi Transaksi</th>
                            <th class="py-4 px-6">Kategori</th>
                            <th class="py-4 px-6 text-center w-28">Tipe</th>
                            <th class="py-4 px-6 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($finances as $f)
                            @php
                                $isIncome = $f->type === 'Pemasukan';
                                $typeClass = $isIncome ? 'bg-emerald-50 text-emerald-700 border-emerald-150' : 'bg-red-50 text-red-700 border-red-150';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Date -->
                                <td class="py-4 px-6 text-xs text-slate-500 font-bold">
                                    {{ $f->date->isoFormat('D MMM YYYY') }}
                                </td>

                                <!-- Description -->
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $f->description }}
                                </td>

                                <!-- Category -->
                                <td class="py-4 px-6 text-xs font-semibold">
                                    <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-700 whitespace-nowrap">
                                        {{ $f->category }}
                                    </span>
                                </td>

                                <!-- Type -->
                                <td class="py-4 px-6 text-center">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold border rounded-md uppercase tracking-wider {{ $typeClass }}">
                                        {{ $f->type }}
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="py-4 px-6 text-right font-extrabold @if($isIncome) text-emerald-600 @else text-red-600 @endif">
                                    @if($isIncome) + @else - @endif Rp {{ number_format($f->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-sm font-medium">
                                    <i class="fa-solid fa-receipt text-4xl mb-3 block text-slate-300"></i>
                                    Belum ada transaksi tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Add cashflow transaction form (Management only) -->
    <div class="xl:col-span-4">
        @if(Auth::user()->isManagement())
            <div class="card-white p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-wallet text-emerald-600 mr-2"></i>Catat Transaksi</h3>
                    <p class="text-xs text-slate-500">Tambahkan pencatatan kas masuk atau keluar baru</p>
                </div>
                
                <form action="{{ route('finances.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe Transaksi</label>
                        <select name="type" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                            <option value="Pemasukan">Pemasukan (Kas Masuk)</option>
                            <option value="Pengeluaran">Pengeluaran (Kas Keluar)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah Uang (Rupiah)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</div>
                            <input type="text" name="amount" id="amountInput" placeholder="Contoh: 50.000" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-bold transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Kategori Keuangan</label>
                        <select name="category" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                            <option value="Iuran Pemain">Iuran Pemain (Kas Patungan)</option>
                            <option value="Sewa Lapangan">Sewa Lapangan</option>
                            <option value="Peralatan">Peralatan (Jersey/Bola/Alat)</option>
                            <option value="Sponsor">Dana Sponsor / Donatur</option>
                            <option value="Uang Pendaftaran Turnamen">Uang Pendaftaran Turnamen</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Transaksi</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi Uraian Singkat</label>
                        <textarea name="description" rows="3" placeholder="Tuliskan keterangan detail transaksi..." required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all"></textarea>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                        Simpan Transaksi Kas
                    </button>
                </form>
            </div>

            <!-- QRIS Team Card (Management only) -->
            <div class="card-white p-6 rounded-3xl space-y-4 mt-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-qrcode text-emerald-600 mr-2"></i>QRIS Pembayaran Tim</h3>
                    <p class="text-xs text-slate-500">Unggah kode QRIS tim Anda untuk memudahkan pemain membayar iuran latihan.</p>
                </div>

                <form action="{{ route('finances.qris.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                    @csrf
                    
                    @if(Auth::user()->team->qris_image)
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">QRIS Tim Saat Ini</div>
                            <img src="{{ asset(Auth::user()->team->qris_image) }}" class="w-40 h-auto mx-auto rounded-xl shadow-sm border border-slate-200" alt="QRIS Tim">
                        </div>
                    @else
                        <div class="border border-dashed border-slate-200 bg-slate-50/50 p-6 rounded-2xl text-center text-slate-400 text-xs font-semibold">
                            <i class="fa-solid fa-qrcode text-3xl mb-2 block text-slate-350"></i>
                            Belum ada QRIS terunggah. Unggah gambar di bawah untuk mengaktifkan pembayaran iuran QRIS bagi pemain.
                        </div>
                    @endif

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih File Gambar QRIS</label>
                        <input type="file" name="qris_image" accept="image/*" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs transition-all file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer">
                        <span class="block text-[9px] text-slate-400 mt-1.5 leading-relaxed">Mendukung format PNG, JPG, atau JPEG. Ukuran maksimal 2MB.</span>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Unggah QRIS Tim
                    </button>
                </form>
            </div>
        @else
            <!-- Display informational widget -->
            <div class="card-white p-6 rounded-3xl text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mx-auto text-xl shadow-sm">
                    <i class="fa-solid fa-coins text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Kas Transparansi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mt-2">
                        Halaman ini menampilkan transparansi kas tim. Anda dapat memantau keluar masuknya kas untuk iuran, sewa lapangan, dan sponsor. Hanya Manajer yang dapat mencatat kas di sini.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto format Rupiah ketika mengetik nominal kas keuangan
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amountInput');
        if (amountInput) {
            amountInput.addEventListener('input', function(e) {
                let cleanVal = this.value.replace(/[^0-9]/g, '');
                this.value = formatRupiah(cleanVal);
            });
        }
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        let number_string = angka.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }
</script>
@endsection
