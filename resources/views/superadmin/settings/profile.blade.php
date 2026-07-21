@extends('layouts.app')

@section('title', 'Pengaturan Profile Superadmin')
@section('header_title', 'Pengaturan Profile Superadmin')

@section('content')
<div class="w-full">
    <div class="card-white p-4 sm:p-6 md:p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-user-shield text-red-600 mr-2"></i>Informasi Profile Superadmin</h3>
                <p class="text-xs text-slate-555 mt-1">Perbarui nama superadmin, email, atau ganti password keamanan root Anda</p>
            </div>
            <span class="px-2.5 py-0.5 text-[9px] font-black tracking-widest uppercase bg-red-150 text-red-700 rounded-md border border-red-200 shadow-sm leading-none shrink-0">Root Admin</span>
        </div>

        <form action="{{ route('superadmin.settings.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5 pt-2">
            @csrf
            @method('PUT')

            <!-- Avatar Upload Row -->
            <div class="flex flex-col sm:flex-row items-center gap-5 pb-4 border-b border-slate-100">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-slate-200 shadow-sm bg-slate-50 flex items-center justify-center shrink-0">
                        @if($user->avatar)
                            <img id="avatarPreview" src="{{ asset($user->avatar) }}" class="w-full h-full object-cover" alt="Preview Avatar">
                        @else
                            <div id="avatarInitials" class="w-full h-full bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 text-emerald-700 text-3xl font-extrabold flex items-center justify-center">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <img id="avatarPreview" src="#" class="w-full h-full object-cover hidden" alt="Preview Avatar">
                        @endif
                    </div>
                </div>
                <div class="space-y-1.5 text-center sm:text-left">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Profil Admin</label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden">
                    <div class="flex items-center justify-center sm:justify-start gap-2">
                        <button type="button" onclick="document.getElementById('avatarInput').click()" 
                            class="px-4 py-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-cloud-arrow-up text-red-600"></i> Pilih Foto
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-450">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</p>
                    @error('avatar')
                        <span class="text-red-500 text-[10px] font-bold block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Administrator</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                @error('name')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email Root</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                @error('email')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">Ganti Password Root (Opsional)</h4>
                <p class="text-[10px] text-slate-450 mb-4 font-medium">Kosongkan kolom password jika Anda tidak ingin mengganti password keamanan</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password Baru</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-405 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                        @error('password')
                            <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-405 focus:outline-none focus:border-red-500 focus:bg-white text-sm transition-all font-bold">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Root
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('avatarPreview');
                const initials = document.getElementById('avatarInitials');
                
                preview.src = event.target.result;
                preview.classList.remove('hidden');
                
                if (initials) {
                    initials.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
