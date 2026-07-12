@extends('layouts.app')

@section('title', 'Pengaturan Website Platform')
@section('header_title', 'Pengaturan Website Platform')

@section('content')
<div class="w-full">
    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-xs font-bold space-y-1 shadow-sm animate-fade-in">
            <div class="flex items-center gap-2 mb-1 text-rose-900">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                <span class="font-extrabold text-[13px]">Gagal memperbarui pengaturan website:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5 font-semibold text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.settings.website.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: Identitas & Branding -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-650 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-circle-nodes"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Identitas & Branding Website</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kelola logo, favicon, dan nama utama platform futsal Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logo Upload -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Logo Website</label>
                    <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <img src="{{ asset($settings['web_logo']) }}" class="w-16 h-16 object-contain bg-white rounded-xl border border-slate-200 p-1" alt="Logo">
                        <div class="flex-1">
                            <input type="file" name="web_logo" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:cursor-pointer">
                            <span class="block text-[9px] text-slate-400 mt-1 leading-normal">PNG, JPG, JPEG, WEBP, atau JFIF. Maks 2MB.</span>
                        </div>
                    </div>
                    @error('web_logo')
                        <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Favicon Upload -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Favicon Website</label>
                    <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <img src="{{ asset($settings['web_favicon']) }}" class="w-10 h-10 object-contain bg-white rounded-xl border border-slate-200 p-1.5" alt="Favicon">
                        <div class="flex-1">
                            <input type="file" name="web_favicon"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:cursor-pointer">
                            <span class="block text-[9px] text-slate-400 mt-1 leading-normal">Format ICO, PNG, GIF, JPG, atau WEBP. Maks 1MB.</span>
                        </div>
                    </div>
                    @error('web_favicon')
                        <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Website / Aplikasi</label>
                <input type="text" name="web_name" value="{{ old('web_name', $settings['web_name']) }}" required
                    class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                @error('web_name')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- SECTION 2: SEO (Search Engine Optimization) -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Optimasi Mesin Pencari (SEO)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Atur kata kunci dan deskripsi pencarian Google untuk meningkatkan rangking platform</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">SEO Description</label>
                <textarea name="web_description" rows="3" placeholder="Tuliskan deskripsi ringkas mengenai platform FutsalHub..."
                    class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">{{ old('web_description', $settings['web_description']) }}</textarea>
                @error('web_description')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">SEO Keywords (Pisahkan dengan koma)</label>
                <input type="text" name="web_keywords" value="{{ old('web_keywords', $settings['web_keywords']) }}" placeholder="futsal, tim futsal, papan taktik, dll"
                    class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                @error('web_keywords')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- SECTION 3: Integrasi TriPay Gateway -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Integrasi Gateway Pembayaran TriPay</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kelola kunci API kredensial akun merchant TriPay Anda</p>
                    </div>
                </div>
                <div>
                    <select name="tripay_mode" 
                        class="bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl px-3 py-1.5 text-slate-800 text-[11px] font-black focus:outline-none transition-all">
                        <option value="sandbox" {{ old('tripay_mode', $settings['tripay_mode']) === 'sandbox' ? 'selected' : '' }}>SANDBOX MODE</option>
                        <option value="production" {{ old('tripay_mode', $settings['tripay_mode']) === 'production' ? 'selected' : '' }}>PRODUCTION MODE</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">TriPay Merchant Name</label>
                    <input type="text" name="tripay_merchant_name" value="{{ old('tripay_merchant_name', $settings['tripay_merchant_name']) }}" placeholder="Masukkan nama Merchant"
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                    @error('tripay_merchant_name')
                        <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">TriPay Merchant Code</label>
                    <input type="text" name="tripay_merchant_code" value="{{ old('tripay_merchant_code', $settings['tripay_merchant_code']) }}" placeholder="Contoh: T12345"
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                    @error('tripay_merchant_code')
                        <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">TriPay API Key</label>
                    <input type="password" name="tripay_api_key" value="{{ old('tripay_api_key', $settings['tripay_api_key']) }}" placeholder="API Key"
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                    @error('tripay_api_key')
                        <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">TriPay Private Key</label>
                    <input type="password" name="tripay_private_key" value="{{ old('tripay_private_key', $settings['tripay_private_key']) }}" placeholder="Private Key"
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                    @error('tripay_private_key')
                        <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 4: Keuangan & Operasional Sistem -->
        <div class="card-white p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-gears"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pengaturan Sistem & Keuangan</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kelola besaran biaya transaksi premium global dan mode pemeliharaan root</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Biaya Upgrade Paket Premium</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</div>
                    <input type="text" name="platform_fee" id="platformFeeInput" value="{{ old('platform_fee', number_format($settings['platform_fee'], 0, ',', '.')) }}" placeholder="Contoh: 100.000" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                </div>
                @error('platform_fee')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Mode Pemeliharaan Website (Maintenance Mode)</div>
                        <div class="text-[10px] text-slate-450 mt-0.5 font-semibold">Mengunci aplikasi untuk seluruh pengguna tenant guna perbaikan atau pembaharuan sistem global</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi Platform
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Auto format Rupiah ketika mengetik nominal platform fee
    document.addEventListener('DOMContentLoaded', function() {
        const feeInput = document.getElementById('platformFeeInput');
        if (feeInput) {
            if (feeInput.value) {
                let cleanVal = feeInput.value.replace(/[^0-9]/g, '');
                feeInput.value = formatRupiah(cleanVal);
            }

            feeInput.addEventListener('input', function(e) {
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
