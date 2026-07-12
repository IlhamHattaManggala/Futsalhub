@extends('layouts.app')

@section('title', 'Manajemen Pemain')
@section('header_title', 'Skuad Pemain Tim')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Skuad List Table -->
    <div class="xl:col-span-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Skuad Aktif</h3>
                <p class="text-xs text-slate-500">Pemain yang terdaftar dalam klub untuk kompetisi berjalan</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-150 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-futbol text-emerald-600 text-xs"></i>
                <span class="text-[11px] font-bold text-emerald-700">{{ count($players) }} Pemain</span>
            </div>
        </div>

        <div class="card-white rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6 text-center w-20">No.</th>
                            <th class="py-4 px-6">Nama Pemain</th>
                            <th class="py-4 px-6">Posisi</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($players as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Jersey No -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 items-center justify-center font-extrabold text-xs text-emerald-600">
                                        {{ $p->number }}
                                    </span>
                                </td>

                                <!-- Name & Linked Account status -->
                                <td class="py-4 px-6">
                                    <a href="{{ route('players.show', $p->id) }}" class="font-bold text-slate-900 hover:text-emerald-600 hover:underline transition-colors flex items-center gap-1.5 group">
                                        <span>{{ $p->name }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-emerald-500 opacity-0 group-hover:opacity-100 transition-all"></i>
                                    </a>
                                    @if($p->user)
                                        <div class="flex items-center gap-1 text-[10px] text-emerald-600 font-bold mt-0.5">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> 
                                            <span>Terikat:</span>
                                            <span class="text-slate-400 font-normal">({{ $p->user->email }})</span>
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-400 font-normal italic mt-0.5">
                                            <i class="fa-solid fa-circle-info text-[9px] mr-0.5"></i> Hanya Profil (Belum Ada Akun)
                                        </div>
                                    @endif
                                </td>

                                <!-- Position -->
                                <td class="py-4 px-6">
                                    @php
                                        $posColors = [
                                            'Anchor' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'Flank' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'Pivot' => 'bg-purple-50 text-purple-700 border-purple-100',
                                            'Goalkeeper' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        ];
                                        $posColor = $posColors[$p->position] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg border text-xs font-bold {{ $posColor }}">
                                        {{ $p->position }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('players.show', $p->id) }}" title="Detail Pemain"
                                            class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 hover:text-blue-700 flex items-center justify-center transition-all text-xs">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->isManagement())
                                            <form action="{{ route('players.destroy', $p->id) }}" method="POST" class="confirm-delete inline" data-message="Menghapus pemain juga akan menghapus akun login terkait (jika ada). Lanjutkan?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 hover:text-red-655 flex items-center justify-center transition-all text-xs" title="Hapus Pemain">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400 text-sm font-medium">
                                    <i class="fa-solid fa-user-xmark text-4xl mb-3 block text-slate-300"></i>
                                    Belum ada pemain terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Add Player Profile Form (Management only) -->
    <div class="xl:col-span-4">
        @if(Auth::user()->isManagement())
            <div class="card-white p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-user-plus text-emerald-600 mr-2"></i>Daftarkan Pemain</h3>
                    <p class="text-xs text-slate-500">Tambahkan profil pemain baru ke dalam skuad</p>
                </div>

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold space-y-1 shadow-sm">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('players.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="name" placeholder="Nama lengkap atlet" value="{{ old('name') }}" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">No. Punggung</label>
                            <input type="number" name="number" placeholder="1 - 99" value="{{ old('number') }}" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Posisi Futsal</label>
                            <select name="position" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                                <option value="Flank">Flank (Sayap)</option>
                                <option value="Anchor">Anchor (Bek)</option>
                                <option value="Pivot">Pivot (Striker)</option>
                                <option value="Goalkeeper">Goalkeeper (Kiper)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi (cm)</label>
                            <input type="number" name="height" placeholder="cm" value="{{ old('height') }}"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Berat (kg)</label>
                            <input type="number" name="weight" placeholder="kg" value="{{ old('weight') }}"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">No. Telp</label>
                            <input type="text" name="phone" id="phoneInput" placeholder="08..." value="{{ old('phone') }}"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tgl Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        </div>
                    </div>

                    <!-- Generate Account Toggle -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-150 space-y-3">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="create_account" id="createAccountToggle" value="1" onchange="toggleAccountFields()"
                                class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-white"
                                {{ old('create_account') ? 'checked' : '' }}>
                            <span class="ml-2.5 text-xs font-bold text-slate-800">Buat Akun Pemain Otomatis</span>
                        </label>
                        <p class="text-[10px] text-slate-500 leading-relaxed">Aktifkan untuk langsung membuat email & sandi login bagi pemain agar dapat melihat dasbor secara mandiri.</p>
                        
                        <div id="accountFields" class="hidden space-y-3 pt-2 border-t border-slate-200">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                                <input type="email" name="email" id="emailInput" placeholder="pemain@futsal.com" value="{{ old('email') }}"
                                    class="w-full bg-white border border-slate-250 rounded-xl px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kata Sandi Login</label>
                                <input type="password" name="password" id="passwordInput" placeholder="Min. 6 Karakter"
                                    class="w-full bg-white border border-slate-250 rounded-xl px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-xs transition-all">
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                        Daftarkan Atlet
                    </button>
                </form>
            </div>
        @else
            <!-- Display informational widget -->
            <div class="card-white p-6 rounded-3xl text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mx-auto text-xl">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Roster Pemain</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mt-2">
                        Hanya pengguna dengan peran **Manager** (Management) yang memiliki otoritas untuk menambah, mendaftarkan akun, atau mengeluarkan pemain dari skuad tim futsal ini.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleAccountFields() {
        const checked = document.getElementById('createAccountToggle').checked;
        const fields = document.getElementById('accountFields');
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');

        if (checked) {
            fields.classList.remove('hidden');
            emailInput.required = true;
            passwordInput.required = true;
        } else {
            fields.classList.add('hidden');
            emailInput.required = false;
            passwordInput.required = false;
            emailInput.value = '';
            passwordInput.value = '';
        }
    }
    
    // Call on load to keep state
    document.addEventListener("DOMContentLoaded", function() {
        toggleAccountFields();

        // Enforce digits only for Phone input in real-time
        const phoneInput = document.getElementById('phoneInput');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
@endsection
