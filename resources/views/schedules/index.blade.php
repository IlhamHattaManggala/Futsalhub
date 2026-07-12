@extends('layouts.app')

@section('title', 'Jadwal Tim')
@section('header_title', 'Jadwal & Agenda Tim')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Schedules List -->
    <div class="xl:col-span-8 space-y-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Agenda Terjadwal</h3>
            <p class="text-xs text-slate-500">Jadwal latihan rutin dan pertandingan mendatang</p>
        </div>

        <div class="space-y-4">
            @forelse($schedules as $s)
                @php
                    $isMatch = $s->type === 'Pertandingan';
                    $badgeClass = $isMatch ? 'bg-red-50 text-red-600 border-red-100' : 'bg-blue-50 text-blue-600 border-blue-100';
                    $hasPassed = $s->start_time->isPast();
                @endphp
                <div class="card-white p-6 rounded-3xl border {{ $hasPassed ? 'opacity-60' : '' }} flex flex-col md:flex-row justify-between items-start md:items-center gap-4 card-white-hover">
                    <!-- Left: Date Widget -->
                    <div class="flex gap-4 items-center w-full md:w-auto">
                        <div class="text-center px-3 py-2 rounded-2xl bg-slate-100 border border-slate-200 flex flex-col justify-center min-w-[65px] h-[65px]">
                            <span class="text-[10px] uppercase font-bold text-slate-500">{{ $s->start_time->isoFormat('MMM') }}</span>
                            <span class="text-2xl font-black text-slate-800 leading-none mt-0.5">{{ $s->start_time->isoFormat('D') }}</span>
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-bold border rounded-md {{ $badgeClass }}">
                                    {{ $s->type }}
                                </span>
                                @if($hasPassed)
                                    <span class="text-[9px] text-slate-450 uppercase tracking-widest font-bold">Selesai</span>
                                @endif
                            </div>
                            <h4 class="text-base font-bold text-slate-900 mt-1.5 leading-tight">{{ $s->title }}</h4>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                                <span><i class="fa-regular fa-clock text-emerald-600 mr-1.5"></i>{{ $s->start_time->isoFormat('HH:mm') }} WIB</span>
                                <span><i class="fa-solid fa-location-dot text-emerald-600 mr-1.5"></i>{{ $s->location }}</span>
                                @if($s->dues_amount > 0)
                                    <span><i class="fa-solid fa-coins text-emerald-600 mr-1.5"></i>Iuran: <strong>Rp {{ number_format($s->dues_amount, 0, ',', '.') }}</strong></span>
                                @endif
                            </div>

                            @if(Auth::user()->isPlayer())
                                @php
                                    $att = $myAttendances[$s->id] ?? null;
                                    $attColors = [
                                        'Hadir' => 'bg-emerald-50 text-emerald-700 border-emerald-150',
                                        'Izin' => 'bg-blue-50 text-blue-700 border-blue-150',
                                        'Alpa' => 'bg-red-50 text-red-700 border-red-150',
                                        'Cedera' => 'bg-amber-50 text-amber-700 border-amber-150',
                                    ];
                                @endphp
                                <div class="mt-2.5 flex items-center gap-2 flex-wrap">
                                    @if($att)
                                        @php
                                            $attColor = $attColors[$att->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                        @endphp
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kehadiran Anda:</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold border rounded-md uppercase {{ $attColor }}">
                                            {{ $att->status }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kehadiran Anda:</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold border rounded-md uppercase bg-slate-50 text-slate-500 border-slate-200">
                                            Belum Dicatat
                                        </span>
                                    @endif

                                    @if($s->dues_amount > 0)
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Status Iuran:</span>
                                        @if($att && $att->is_dues_paid)
                                            <span class="px-2 py-0.5 text-[10px] font-bold border border-emerald-150 rounded-md uppercase bg-emerald-50 text-emerald-750">
                                                <i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i>Lunas
                                            </span>
                                        @elseif($att && $att->payment_receipt)
                                            <span class="px-2 py-0.5 text-[10px] font-bold border border-yellow-200 rounded-md uppercase bg-yellow-50 text-yellow-750 animate-pulse">
                                                <i class="fa-solid fa-hourglass-half mr-1 text-yellow-600"></i>Menunggu Verifikasi
                                            </span>
                                        @else
                                            <div class="flex items-center gap-1.5">
                                                <span class="px-2 py-0.5 text-[10px] font-bold border border-red-150 rounded-md uppercase bg-red-50 text-red-750">
                                                    <i class="fa-solid fa-circle-xmark mr-1 text-red-600"></i>Belum Lunas
                                                </span>
                                                @if(Auth::user()->team && Auth::user()->team->qris_image)
                                                    <button type="button" onclick="showQrisModal('{{ asset(Auth::user()->team->qris_image) }}', '{{ number_format($s->dues_amount, 0, ',', '.') }}', '{{ $s->title }}', '{{ route('schedules.attendance.receipt', $s->id) }}')" 
                                                        class="px-2.5 py-1 text-[9px] font-black bg-emerald-500 hover:bg-emerald-600 text-white rounded-md flex items-center gap-1 transition-all shadow-sm">
                                                        <i class="fa-solid fa-qrcode"></i> Bayar via QRIS
                                                    </button>
                                                @else
                                                    <span class="text-[9px] text-slate-450 italic">(QRIS Tim Belum Diunggah Manajer)</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endif

                                    @if($att && $att->notes)
                                        <span class="text-[10px] text-slate-400 italic">({{ $att->notes }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Middle: Description Details -->
                    <div class="flex-1 w-full md:max-w-xs text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                        <div class="font-bold text-slate-500 mb-0.5">Catatan / Detail:</div>
                        <p class="line-clamp-2">
                            {{ $s->description ?: 'Tidak ada detail catatan.' }}
                        </p>
                        @if($isMatch && $s->opponent)
                            <div class="mt-1 text-[10px] text-red-650 font-bold">
                                <i class="fa-solid fa-shield-halved mr-1"></i> Lawan: <strong>{{ $s->opponent }}</strong>
                            </div>
                        @endif
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="w-full md:w-auto flex md:flex-col gap-2 justify-end">
                        @if(Auth::user()->isCoach())
                            <a href="{{ route('schedules.attendance', $s->id) }}" 
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm font-bold">
                                <i class="fa-solid fa-clipboard-user"></i> Absensi
                            </a>
                             <button type="button" onclick="showQrAttendance('{{ URL::signedRoute('schedules.scan', ['slug' => (Auth::user()->isSuperAdmin() ? 'superadmin' : Auth::user()->slug), 'id' => $s->id]) }}', '{{ $s->title }}')"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-qrcode text-emerald-600"></i> QR Absensi
                            </button>
                        @endif
                        
                        @if(Auth::user()->isCoach() || Auth::user()->isManagement())
                            @php
                                $waText = "🔊 *AGENDA TIM BARU* 🔊\n\n"
                                        . "*Kegiatan:* " . $s->title . "\n"
                                        . "*Tipe:* " . $s->type . "\n"
                                        . "*Hari/Tanggal:* " . $s->start_time->isoFormat('dddd, D MMMM YYYY') . "\n"
                                        . "*Waktu:* " . $s->start_time->isoFormat('HH:mm') . " WIB\n"
                                        . "*Lokasi:* " . $s->location . "\n";
                                if($s->dues_amount > 0) {
                                    $waText .= "*Iuran:* Rp " . number_format($s->dues_amount, 0, ',', '.') . "\n";
                                }
                                if($s->description) {
                                    $waText .= "*Catatan:* " . $s->description . "\n";
                                }
                                 $waText .= "\nMohon segera konfirmasi kehadiran Anda di aplikasi FutsalHub! ⚽";
                                $waUrl = "https://api.whatsapp.com/send?text=" . urlencode($waText);
                            @endphp
                            <a href="{{ $waUrl }}" target="_blank" 
                                class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-250 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> Bagikan WA
                            </a>

                            <a href="{{ route('schedules.edit', $s->id) }}" 
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-250 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Edit Agenda
                            </a>
                            
                            <form action="{{ route('schedules.destroy', $s->id) }}" method="POST" class="confirm-delete" data-message="Apakah Anda yakin ingin menghapus jadwal agenda &quot;{{ $s->title }}&quot; ini? Semua data absensi dan bukti transfer iuran terkait juga akan terhapus secara permanen.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-150 text-red-650 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card-white p-12 text-center rounded-3xl text-slate-500">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-4 text-slate-300"></i>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Belum Ada Jadwal</h4>
                    <p class="text-xs max-w-sm mx-auto">
                        Tidak ada latihan rutin atau pertandingan futsal yang terdaftar untuk tim ini saat ini.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Add Schedule Form (Coach & Management only) -->
    <div class="xl:col-span-4">
        @if(Auth::user()->isCoach() || Auth::user()->isManagement())
            <div class="card-white p-6 rounded-3xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-calendar-plus text-emerald-600 mr-2"></i>Tambah Jadwal Agenda</h3>
                <p class="text-xs text-slate-500">Buat jadwal agenda baru untuk seluruh skuad tim</p>
                
                <form action="{{ route('schedules.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Agenda</label>
                        <input type="text" name="title" placeholder="Misal: Latihan Fisik & Taktik Bertahan" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Agenda</label>
                        <select name="type" id="scheduleType" onchange="toggleOpponentInput()" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                            <option value="Latihan">Latihan Tim</option>
                            <option value="Pertandingan">Pertandingan (Match)</option>
                        </select>
                    </div>

                    <div id="opponentWrapper" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lawan (Opponent)</label>
                        <input type="text" name="opponent" id="opponentInput" placeholder="Nama Klub Lawan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Waktu & Tanggal Mulai</label>
                        <input type="datetime-local" name="start_time" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lokasi Kegiatan</label>
                        <input type="text" name="location" placeholder="Misal: Champion Futsal Court A" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Besaran Iuran per Pemain (Opsional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</div>
                            <input type="text" name="dues_amount" id="duesAmountInput" placeholder="Contoh: 15.000 (Kosongkan jika gratis)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 placeholder-slate-450 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Pakaian latihan, kebutuhan logistik, dll..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all"></textarea>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                        Jadwalkan Agenda
                    </button>
                </form>
            </div>
        @else
            <!-- Player Info Widget -->
            <div class="card-white p-6 rounded-3xl text-center space-y-4">
                <i class="fa-solid fa-clock-rotate-left text-3xl text-emerald-600 animate-pulse"></i>
                <h3 class="text-sm font-bold text-slate-800">Informasi Jadwal</h3>
                <p class="text-xs text-slate-550 leading-relaxed">
                    Sebagai pemain, Anda dapat memantau jadwal terkini yang dirancang oleh pelatih & manajer di sini. Hubungi staf pelatih jika Anda berhalangan hadir agar dapat dicatat dalam absensi tim.
                </p>
                <button type="button" onclick="openQrScanner()" 
                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center gap-2">
                    <i class="fa-solid fa-camera"></i> Pindai Absensi (Buka Kamera)
                </button>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode/html5-qrcode.min.js"></script>
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

    /**
     * Tampilkan Modal QRIS Tim & Unggah Bukti Transfer (Pemain)
     */
    function showQrisModal(imageUrl, amount, agendaTitle, uploadUrl) {
        Swal.fire({
            title: 'QRIS Pembayaran Iuran',
            html: `
                <div class="text-center space-y-3 font-sans">
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                        Silakan scan QRIS tim di bawah ini untuk melakukan pembayaran iuran agenda: 
                        <br><strong class="text-slate-800 text-sm">${agendaTitle}</strong>
                    </p>
                    <div class="bg-slate-100 p-2 rounded-2xl border border-slate-200 inline-block">
                        <img src="${imageUrl}" class="w-48 h-auto mx-auto rounded-xl shadow-sm border border-slate-200" alt="QRIS Tim">
                    </div>
                    <div class="bg-emerald-50 border border-emerald-150 px-4 py-2 rounded-xl inline-block w-full">
                        <div class="text-[9px] text-emerald-600 font-black uppercase tracking-wider">Nominal Transfer</div>
                        <div class="text-base font-black text-emerald-800 mt-0.5">Rp ${amount}</div>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold leading-relaxed">
                        Punya bukti transfer? Klik <strong>"Unggah Bukti Transfer"</strong> di bawah untuk mengirim bukti transfer langsung ke Pelatih/Manajer.
                    </p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Unggah Bukti Transfer',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Modal langkah kedua: memilih file
                Swal.fire({
                    title: 'Unggah Bukti Transfer',
                    text: 'Pilih file gambar bukti transfer Anda (Maksimal 2MB)',
                    input: 'file',
                    inputAttributes: {
                        'accept': 'image/*',
                        'aria-label': 'Pilih bukti transfer Anda'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Kirim Bukti',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#10b981',
                    showLoaderOnConfirm: true,
                    preConfirm: (file) => {
                        if (!file) {
                            Swal.showValidationMessage('Anda harus memilih file gambar terlebih dahulu!');
                            return false;
                        }
                        
                        const formData = new FormData();
                        formData.append('receipt', file);
                        formData.append('_token', '{{ csrf_token() }}');
                        
                        return fetch(uploadUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { 
                                    throw new Error(err.message || 'Gagal mengunggah file.'); 
                                });
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Error: ${error.message}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    customClass: {
                        popup: 'rounded-3xl border border-slate-100 shadow-xl'
                    }
                }).then((uploadResult) => {
                    if (uploadResult.isConfirmed && uploadResult.value && uploadResult.value.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: uploadResult.value.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            customClass: {
                                popup: 'rounded-3xl border border-slate-100 shadow-xl'
                            }
                        }).then(() => {
                            // Muat ulang halaman agar perubahan badge langsung tercermin
                            window.location.reload();
                        });
                    }
                });
            }
        });
    }

    // Auto format Rupiah ketika mengetik besaran iuran
    document.addEventListener('DOMContentLoaded', function() {
        const duesInput = document.getElementById('duesAmountInput');
        if (duesInput) {
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

    /**
     * Tampilkan QR Code Absensi Mandiri (Coach)
     */
    function showQrAttendance(url, agendaTitle) {
        Swal.fire({
            title: 'QR Code Absensi Mandiri',
            html: `
                <div class="text-center space-y-4 font-sans">
                    <p class="text-xs text-slate-555 font-semibold leading-relaxed">
                        Tunjukkan QR Code ini kepada Pemain untuk memindai absensi secara mandiri.
                        <br><strong class="text-slate-800 text-sm">${agendaTitle}</strong>
                    </p>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 inline-block shadow-sm">
                        <div id="attendanceQrCode" class="w-48 h-48 mx-auto flex items-center justify-center bg-slate-50 rounded-xl">
                            <!-- QR Code will be rendered here -->
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold max-w-xs mx-auto">
                        Tautan ini dilindungi dengan tanda tangan digital keamanan Laravel (Signed URL).
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl'
            },
            didOpen: () => {
                new QRCode(document.getElementById("attendanceQrCode"), {
                    text: url,
                    width: 192,
                    height: 192,
                    colorDark : "#0f172a",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            }
        });
    }

    /**
     * Buka Kamera Pemindai QR Code Absensi Mandiri (Player)
     */
    let html5QrCodeScanner = null;
    function openQrScanner() {
        Swal.fire({
            title: 'Pindai QR Code Absensi',
            html: `
                <div class="text-center space-y-4 font-sans">
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                        Arahkan kamera HP Anda ke QR Code yang ditampilkan oleh Pelatih.
                    </p>
                    <div class="bg-slate-900 overflow-hidden rounded-2xl border border-slate-700 relative w-full aspect-square max-w-[320px] mx-auto shadow-inner flex items-center justify-center">
                        <div id="qrReader" class="w-full h-full"></div>
                        <!-- Decorative scanner scanning line -->
                        <div class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_8px_rgba(52,211,153,0.8)]" id="scannerScanLine" style="top: 15%; animation: scanEffect 2s linear infinite;"></div>
                    </div>
                    <button type="button" id="btnToggleCamera" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] uppercase tracking-wider rounded-xl transition-all shadow-sm">
                        <i class="fa-solid fa-camera-rotate mr-1"></i> Ganti Kamera
                    </button>
                    <style>
                        @keyframes scanEffect {
                            0% { top: 15%; }
                            50% { top: 85%; }
                            100% { top: 15%; }
                        }
                        #qrReader video {
                            object-fit: cover !important;
                            border-radius: 1rem;
                        }
                    </style>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Tutup Kamera',
            cancelButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-xl',
                cancelButton: 'rounded-xl text-xs font-bold shadow-sm'
            },
            didOpen: () => {
                html5QrCodeScanner = new Html5Qrcode("qrReader");
                let currentCameraMode = "environment";

                function startScanning(mode) {
                    html5QrCodeScanner.start(
                        { facingMode: mode },
                        {
                            fps: 15,
                            qrbox: (width, height) => {
                                const side = Math.min(width, height) * 0.75;
                                return { width: side, height: side };
                            }
                        },
                        (decodedText) => {
                            // On success scan
                            Swal.showLoading();
                            html5QrCodeScanner.stop().then(() => {
                                window.location.href = decodedText;
                            }).catch(() => {
                                window.location.href = decodedText;
                            });
                        },
                        (errorMessage) => {
                            // parse error, ignore as it spams the log during scanning
                        }
                    ).catch((err) => {
                        console.error(err);
                        const qrContainer = document.getElementById("qrReader");
                        const scanLine = document.getElementById("scannerScanLine");
                        if (scanLine) scanLine.style.display = 'none';
                        if (qrContainer) {
                            qrContainer.innerHTML = `
                                <div class="p-6 text-center text-xs text-red-500 font-bold flex flex-col items-center justify-center h-full">
                                    <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
                                    Gagal mengakses kamera.<br>
                                    Pastikan izin kamera telah diberikan.
                                </div>
                            `;
                        }
                    });
                }

                startScanning(currentCameraMode);

                const toggleBtn = document.getElementById("btnToggleCamera");
                if (toggleBtn) {
                    toggleBtn.addEventListener("click", () => {
                        currentCameraMode = (currentCameraMode === "environment") ? "user" : "environment";
                        html5QrCodeScanner.stop().then(() => {
                            startScanning(currentCameraMode);
                        });
                    });
                }
            },
            willClose: () => {
                if (html5QrCodeScanner) {
                    try {
                        if (html5QrCodeScanner.isScanning) {
                            html5QrCodeScanner.stop().catch(err => console.error(err));
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        });
    }
</script>
@endsection
