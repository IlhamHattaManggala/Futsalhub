@extends('layouts.app')

@section('title', 'Pencatatan Absensi')
@section('header_title', 'Pencatatan Absensi Pemain')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-clipboard-user text-emerald-600 mr-2"></i>Absensi Agenda: {{ $schedule->title }}</h3>
        <p class="text-xs text-slate-500">
            <i class="fa-solid fa-calendar mr-1 text-emerald-600"></i> {{ $schedule->start_time->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB | 
            <i class="fa-solid fa-location-dot text-red-500 mx-1"></i> {{ $schedule->location }}
        </p>
    </div>
    <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-sm transition-all">
        <i class="fa-solid fa-arrow-left mr-1"></i> Batal / Kembali
    </a>
</div>

<div class="card-white rounded-3xl overflow-hidden">
    <form action="{{ route('schedules.attendance.save', $schedule->id) }}" method="POST">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-750 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 text-center w-20">No.</th>
                        <th class="py-4 px-6">Nama Pemain</th>
                        <th class="py-4 px-6">Posisi</th>
                        <th class="py-4 px-6 text-center min-w-[320px]">Status Absensi</th>
                        @if($schedule->dues_amount > 0)
                            <th class="py-4 px-6 text-center w-48">Status Iuran (Rp {{ number_format($schedule->dues_amount, 0, ',', '.') }})</th>
                        @endif
                        <th class="py-4 px-6">Catatan Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700 bg-white">
                    @forelse($players as $p)
                        @php
                            $existAtt = $attendances->get($p->id);
                            $activeStatus = $existAtt ? $existAtt->status : 'Hadir';
                            $activeNote = $existAtt ? $existAtt->notes : '';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Jersey Number -->
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex w-8 h-8 rounded-full bg-slate-50 border border-slate-200 items-center justify-center font-bold text-xs text-slate-800">
                                    {{ $p->number }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $p->name }}
                            </td>

                            <!-- Position -->
                            <td class="py-4 px-6 text-xs text-slate-500">
                                <span class="px-2.5 py-1 rounded-md bg-slate-50 border border-slate-150 text-slate-700">
                                    {{ $p->position }}
                                </span>
                            </td>

                            <!-- Attendance Radios -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3 text-xs font-bold">
                                    <!-- Hadir -->
                                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:border-emerald-500/20 transition-all text-slate-500 [&:has(input:checked)]:bg-emerald-50 [&:has(input:checked)]:border-emerald-300 [&:has(input:checked)]:text-emerald-700">
                                        <input type="radio" name="attendance[{{ $p->id }}][status]" value="Hadir" 
                                            class="w-3.5 h-3.5 text-emerald-600 bg-white border-slate-300 focus:ring-emerald-500"
                                            {{ $activeStatus === 'Hadir' ? 'checked' : '' }}>
                                        <span>Hadir</span>
                                    </label>

                                    <!-- Izin -->
                                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:border-yellow-500/20 transition-all text-slate-500 [&:has(input:checked)]:bg-yellow-50 [&:has(input:checked)]:border-yellow-300 [&:has(input:checked)]:text-yellow-750">
                                        <input type="radio" name="attendance[{{ $p->id }}][status]" value="Izin" 
                                            class="w-3.5 h-3.5 text-yellow-600 bg-white border-slate-300 focus:ring-yellow-500"
                                            {{ $activeStatus === 'Izin' ? 'checked' : '' }}>
                                        <span>Izin</span>
                                    </label>

                                    <!-- Alpa -->
                                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:border-red-500/20 transition-all text-slate-500 [&:has(input:checked)]:bg-red-50 [&:has(input:checked)]:border-red-300 [&:has(input:checked)]:text-red-700">
                                        <input type="radio" name="attendance[{{ $p->id }}][status]" value="Alpa" 
                                            class="w-3.5 h-3.5 text-red-650 bg-white border-slate-300 focus:ring-red-500"
                                            {{ $activeStatus === 'Alpa' ? 'checked' : '' }}>
                                        <span>Alpa</span>
                                    </label>

                                    <!-- Cedera -->
                                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:border-orange-500/20 transition-all text-slate-500 [&:has(input:checked)]:bg-orange-50 [&:has(input:checked)]:border-orange-300 [&:has(input:checked)]:text-orange-750">
                                        <input type="radio" name="attendance[{{ $p->id }}][status]" value="Cedera" 
                                            class="w-3.5 h-3.5 text-orange-600 bg-white border-slate-300 focus:ring-orange-500"
                                            {{ $activeStatus === 'Cedera' ? 'checked' : '' }}>
                                        <span>Cedera</span>
                                    </label>
                                </div>
                            </td>

                            @if($schedule->dues_amount > 0)
                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <input type="hidden" name="attendance[{{ $p->id }}][is_dues_paid]" value="0">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:border-emerald-500/20 transition-all text-slate-500 [&:has(input:checked)]:bg-emerald-50 [&:has(input:checked)]:border-emerald-300 [&:has(input:checked)]:text-emerald-700 text-xs font-bold shadow-sm">
                                            <input type="checkbox" name="attendance[{{ $p->id }}][is_dues_paid]" value="1" 
                                                class="w-3.5 h-3.5 text-emerald-600 bg-white border-slate-300 rounded focus:ring-emerald-500"
                                                {{ $existAtt && $existAtt->is_dues_paid ? 'checked' : '' }}>
                                            <span>Lunas</span>
                                        </label>

                                        @if($existAtt && $existAtt->payment_receipt)
                                            @if(!$existAtt->is_dues_paid)
                                                <span class="px-2 py-0.5 text-[9px] font-black border border-yellow-200 rounded uppercase bg-yellow-50 text-yellow-750 animate-pulse mt-0.5">
                                                    Menunggu Verifikasi
                                                </span>
                                            @endif
                                            <button type="button" onclick="viewReceiptModal('{{ asset($existAtt->payment_receipt) }}', '{{ $p->name }}')" 
                                                class="px-2 py-0.5 text-[9px] font-black bg-blue-50 text-blue-600 border border-blue-150 hover:bg-blue-100 rounded transition-all flex items-center justify-center gap-1 mt-0.5 shadow-sm">
                                                <i class="fa-solid fa-receipt"></i> Bukti Transfer
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif

                            <!-- Notes -->
                            <td class="py-4 px-6">
                                <input type="text" name="attendance[{{ $p->id }}][notes]" value="{{ $activeNote }}" placeholder="Catatan opsional..." 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada pemain yang terdaftar dalam tim ini. Silakan tambahkan pemain terlebih dahulu di menu Manajemen Pemain.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($players) > 0)
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold tracking-wide transition-all shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Rekap Absensi
                </button>
            </div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script>
    /**
     * Tampilkan Popup Pratinjau Bukti Transfer Pemain (Pelatih)
     */
    function viewReceiptModal(imageUrl, playerName) {
        Swal.fire({
            title: 'Bukti Transfer Pemain',
            html: `
                <div class="text-center space-y-3 font-sans">
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                        Bukti pembayaran iuran yang diunggah oleh:<br><strong class="text-slate-800 text-sm">${playerName}</strong>
                    </p>
                    <div class="bg-slate-100 p-2.5 rounded-2xl border border-slate-200 inline-block">
                        <img src="${imageUrl}" class="max-w-xs h-auto mx-auto rounded-xl shadow-md border border-slate-200" alt="Bukti Transfer">
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold leading-relaxed">
                        *Periksa keaslian bukti transfer di atas. Jika sudah sesuai, silakan centang opsi "Lunas" untuk pemain ini dan klik Simpan.
                    </p>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#10b981',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl'
            }
        });
    }
</script>
@endsection
