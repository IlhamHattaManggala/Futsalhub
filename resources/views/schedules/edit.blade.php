@extends('layouts.app')

@section('title', 'Edit Jadwal Tim')
@section('header_title', 'Edit Agenda Tim')

@section('content')
<div class="w-full">
    <div class="card-white p-4 sm:p-6 md:p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
        <!-- Title & Subtitle -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-xl font-bold text-slate-900"><i class="fa-solid fa-calendar-days text-emerald-600 mr-2"></i>Edit Jadwal Agenda</h3>
                <p class="text-xs text-slate-550 mt-1">Perbarui detail waktu, lokasi, tipe, atau iuran kegiatan tim Anda</p>
            </div>
            <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 shadow-sm border border-slate-200">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('schedules.update', $schedule->id) }}" method="POST" class="space-y-5 pt-2">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Agenda</label>
                <input type="text" name="title" value="{{ old('title', $schedule->title) }}" placeholder="Misal: Latihan Fisik & Taktik Bertahan" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-405 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Agenda</label>
                    <select name="type" id="scheduleType" onchange="toggleOpponentInput()" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                        <option value="Latihan" {{ old('type', $schedule->type) === 'Latihan' ? 'selected' : '' }}>Latihan Tim</option>
                        <option value="Pertandingan" {{ old('type', $schedule->type) === 'Pertandingan' ? 'selected' : '' }}>Pertandingan (Match)</option>
                    </select>
                </div>

                <div id="opponentWrapper" class="{{ old('type', $schedule->type) === 'Pertandingan' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lawan (Opponent)</label>
                    <input type="text" name="opponent" id="opponentInput" value="{{ old('opponent', $schedule->opponent) }}" placeholder="Nama Klub Lawan"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-405 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Waktu & Tanggal Mulai</label>
                    <input type="datetime-local" name="start_time" value="{{ old('start_time', $schedule->start_time->format('Y-m-d\TH:i')) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lokasi Kegiatan</label>
                    <input type="text" name="location" value="{{ old('location', $schedule->location) }}" placeholder="Misal: Champion Futsal Court A" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-405 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Besaran Iuran per Pemain (Opsional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</div>
                    <input type="text" name="dues_amount" id="duesAmountInput" 
                        value="{{ old('dues_amount', $schedule->dues_amount > 0 ? number_format($schedule->dues_amount, 0, ',', '.') : '') }}" 
                        placeholder="Contoh: 15.000 (Kosongkan jika gratis)"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 placeholder-slate-450 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Pakaian latihan, kebutuhan logistik, dll..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">{{ old('description', $schedule->description) }}</textarea>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-100">
                <button type="submit" 
                    class="flex-1 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('schedules.index') }}" 
                    class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold text-xs uppercase tracking-wider transition-all text-center border border-slate-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleOpponentInput() {
        const type = document.getElementById('scheduleType').value;
        const opponentWrapper = document.getElementById('opponentWrapper');
        const opponentInput = document.getElementById('opponentInput');

        if (type === 'Pertandingan') {
            opponentWrapper.classList.remove('hidden');
            opponentInput.required = true;
        } else {
            opponentWrapper.classList.add('hidden');
            opponentInput.required = false;
            opponentInput.value = '';
        }
    }

    // Auto format Rupiah ketika mengetik nominal iuran
    document.addEventListener('DOMContentLoaded', function() {
        const duesInput = document.getElementById('duesAmountInput');
        if (duesInput) {
            // Jalankan sekali saat load jika ada nilai bawaan
            if (duesInput.value) {
                let cleanVal = duesInput.value.replace(/[^0-9]/g, '');
                duesInput.value = formatRupiah(cleanVal);
            }

            duesInput.addEventListener('input', function(e) {
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
