@extends('layouts.app')

@section('title', 'Pengaturan Tim')
@section('header_title', 'Pengaturan Tim Futsal')

@section('content')
<div class="w-full">
    <div class="card-white p-4 sm:p-6 md:p-8 rounded-3xl space-y-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-people-roof text-emerald-600 mr-2"></i>Identitas Tim</h3>
                <p class="text-xs text-slate-550 mt-1">Perbarui nama klub futsal Anda dan deskripsi tim utama</p>
            </div>
            <div class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $team->isPremium() ? 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm animate-pulse' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                Plan: {{ strtoupper($team->plan) }}
            </div>
        </div>

        <form action="{{ route('settings.team.update') }}" method="POST" class="space-y-5 pt-2">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Tim / Klub</label>
                <input type="text" name="name" value="{{ old('name', $team->name) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-450 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">
                @error('name')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi / Motto Tim</label>
                <textarea name="description" rows="4" placeholder="Tuliskan slogan, sejarah singkat, atau deskripsi tim..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white text-sm transition-all font-bold">{{ old('description', $team->description) }}</textarea>
                @error('description')
                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Tim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
