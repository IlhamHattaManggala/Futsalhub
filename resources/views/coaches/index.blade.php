@extends('layouts.app')

@section('title', 'Manajemen Pelatih')
@section('header_title', 'Staf Pelatih Tim')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Coach List Table -->
    <div class="xl:col-span-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Pelatih Aktif</h3>
                <p class="text-xs text-slate-500">Pelatih yang mengelola taktik dan agenda latihan tim</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-150 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-user-tie text-emerald-600 text-xs"></i>
                <span class="text-[11px] font-bold text-emerald-700">{{ count($coaches) }} Pelatih</span>
            </div>
        </div>

        <div class="card-white rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6 text-center w-20">No.</th>
                            <th class="py-4 px-6">Nama Pelatih</th>
                            <th class="py-4 px-6">Alamat Email</th>
                            <th class="py-4 px-6">Peran</th>
                            @if(Auth::user()->isManagement())
                                <th class="py-4 px-6 text-center">Status Akun</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($coaches as $index => $c)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Index -->
                                <td class="py-4 px-6 text-center font-bold text-slate-500">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Name -->
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $c->name }}
                                </td>

                                <!-- Email -->
                                <td class="py-4 px-6 text-slate-600 font-medium">
                                    {{ $c->email }}
                                </td>

                                <!-- Role label -->
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-lg border text-xs font-bold bg-yellow-50 text-yellow-600 border-yellow-100">
                                        Pelatih (Coach)
                                    </span>
                                </td>

                                <!-- Status Akun -->
                                @if(Auth::user()->isManagement())
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('coaches.toggle-status', $c->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ !$c->is_locked ? 'bg-emerald-500' : 'bg-slate-200' }}" title="{{ !$c->is_locked ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ !$c->is_locked ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                            </button>
                                        </form>
                                    </td>
                                @endif

                                <!-- Action -->
                                @if(Auth::user()->isManagement())
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('coaches.destroy', $c->id) }}" method="POST" class="confirm-delete" data-message="Apakah Anda yakin ingin menghapus akun pelatih ini? Aksi ini tidak dapat dibatalkan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 border border-red-100 hover:border-red-200 hover:bg-red-100 text-red-655 rounded-xl transition-all" title="Hapus Pelatih">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->isManagement() ? 6 : 4 }}" class="py-12 text-center text-slate-400 text-sm font-medium">
                                    <i class="fa-solid fa-user-tie-slash text-4xl mb-3 block text-slate-300"></i>
                                    Belum ada pelatih terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Add Coach Form (Management only) -->
    <div class="xl:col-span-4">
        @if(Auth::user()->isManagement())
            <div class="card-white p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-user-plus text-emerald-600 mr-2"></i>Daftarkan Pelatih</h3>
                    <p class="text-xs text-slate-500">Tambahkan akun staf pelatih baru ke dalam tim</p>
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

                <form action="{{ route('coaches.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap Pelatih</label>
                        <input type="text" name="name" placeholder="Nama lengkap pelatih" value="{{ old('name') }}" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Email</label>
                        <input type="email" name="email" placeholder="pelatih@futsal.com" value="{{ old('email') }}" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi</label>
                        <input type="password" name="password" placeholder="Minimal 6 Karakter" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <button type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                        Buat Akun Pelatih
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
                    <h3 class="text-sm font-bold text-slate-900">Manajemen Pelatih</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mt-2">
                        Hanya pengguna dengan peran **Manager** (Management) yang memiliki otoritas untuk mendaftarkan akun pelatih baru bagi tim futsal ini.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
