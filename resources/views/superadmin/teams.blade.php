@extends('layouts.app')

@section('title', 'Monitor Tenant Tim')
@section('header_title', 'Kelola Tenant Tim Futsal')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Left Column: Teams list table -->
        <div class="xl:col-span-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Tenant Terdaftar</h3>
                    <p class="text-xs text-slate-500">Seluruh tim futsal terisolasi yang menggunakan layanan platform</p>
                </div>
                <div class="bg-emerald-50 border border-emerald-150 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-600 text-xs"></i>
                    <span class="text-[11px] font-bold text-emerald-700">{{ count($teams) }} Tenant</span>
                </div>
            </div>

            <div class="card-white rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <th class="py-4 px-6 text-center w-20">ID</th>
                                <th class="py-4 px-6">Nama Tim</th>
                                <th class="py-4 px-6">Paket</th>
                                <th class="py-4 px-6">Pemain</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @forelse($teams as $team)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- ID -->
                                    <td class="py-4 px-6 text-center text-xs font-bold text-slate-500">
                                        #{{ $team->id }}
                                    </td>

                                    <!-- Name -->
                                    <td class="py-4 px-6 font-bold text-slate-900">
                                        {{ $team->name }}
                                    </td>

                                    <!-- Plan -->
                                    <td class="py-4 px-6 text-xs font-semibold">
                                        @if($team->isPremium())
                                            <span
                                                class="px-2.5 py-1 rounded-lg bg-amber-55 border border-amber-100 font-bold text-amber-700 flex items-center gap-1 w-fit shadow-sm">
                                                <i class="fa-solid fa-gem text-[10px]"></i> Premium
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 font-bold text-slate-500 flex items-center gap-1 w-fit">
                                                <i class="fa-solid fa-circle-play text-[10px]"></i> Free
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Players count -->
                                    <td class="py-4 px-6 text-xs">
                                        <span
                                            class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-100 font-bold text-emerald-700">
                                            {{ $team->players()->count() }} Atlet
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button onclick="showTeamDetail({{ $team->id }})" title="Detail Tim"
                                                class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 hover:text-blue-700 flex items-center justify-center transition-all text-xs">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button onclick="showEditTeam({{ $team->id }}, '{{ addslashes($team->name) }}', '{{ addslashes($team->description ?? '') }}', '{{ $team->plan }}')" title="Ubah Paket Langganan"
                                                class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 text-amber-600 hover:bg-amber-100 hover:text-amber-700 flex items-center justify-center transition-all text-xs">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 text-sm font-medium">
                                        <i class="fa-solid fa-shield-halved text-4xl mb-3 block text-slate-300"></i>
                                        Belum ada tim terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Add Team Form -->
        <div class="xl:col-span-4">
            <div class="card-white p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900"><i
                            class="fa-solid fa-plus text-emerald-600 mr-2"></i>Daftarkan Tim Baru</h3>
                    <p class="text-xs text-slate-500">Tambahkan tim futsal baru ke dalam platform multi-tenant</p>
                </div>

                <form action="{{ route('superadmin.teams.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Tim
                            Futsal</label>
                        <input type="text" name="name" placeholder="Misal: Red Wolves Futsal" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Paket
                            Langganan</label>
                        <select name="plan" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                            <option value="free">Free / Gratis (Rp 0)</option>
                            <option value="premium">Premium (Rp
                                {{ number_format(\App\Models\Setting::get('platform_fee', '100000'), 0, ',', '.') }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi /
                            Keterangan Tim</label>
                        <textarea name="description" rows="5"
                            placeholder="Keterangan mengenai profil singkat atau asal tim..."
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                        Daftarkan Tenant Tim
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden forms for edit & delete -->
    <form id="editTeamForm" method="POST" style="display:none;">
        @csrf
        @method('PUT')
    </form>

@endsection

@section('scripts')
<script>
    // === DETAIL TEAM ===
    function showTeamDetail(id) {
        Swal.fire({
            title: '<i class="fa-solid fa-spinner fa-spin text-emerald-500"></i>',
            html: 'Memuat detail tim...',
            showConfirmButton: false,
            allowOutsideClick: true,
            didOpen: () => {
                fetch(`/v1/superadmin/teams/${id}`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const planBadge = data.is_premium
                        ? '<span style="background:linear-gradient(135deg,#f59e0b,#d97706); color:white; padding:2px 10px; border-radius:6px; font-size:10px; font-weight:800;"><i class="fa-solid fa-gem" style="margin-right:3px;"></i>PREMIUM</span>'
                        : '<span style="background:#f1f5f9; color:#64748b; padding:2px 10px; border-radius:6px; font-size:10px; font-weight:800;">FREE</span>';

                    const premiumInfo = data.premium_until
                        ? ` <span style="font-size:10px; color:#0d9488; font-weight:600;">· hingga ${data.premium_until}</span>` : '';

                    // Compact members list
                    let membersHtml = '<span style="color:#94a3b8; font-size:11px;">Belum ada anggota.</span>';
                    if (data.members && data.members.length > 0) {
                        membersHtml = data.members.map(m => {
                            const c = m.role==='management'?'#0d9488':m.role==='coach'?'#a16207':'#2563eb';
                            return `<span style="font-size:11px; font-weight:600; color:#334155;">${m.name}</span> <span style="font-size:9px; font-weight:700; color:${c}; text-transform:uppercase;">(${m.role})</span>`;
                        }).join('<span style="color:#cbd5e1; margin:0 3px;">·</span>');
                    }

                    Swal.update({
                        title: `<span style="font-size:17px; font-weight:800; color:#0f172a;">${data.name}</span>`,
                        html: `
                            <div style="text-align:left; font-family:inherit;">
                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                    ${planBadge}
                                    <span style="color:#94a3b8; font-size:10px; font-weight:600;">ID #${data.id}</span>
                                    ${premiumInfo}
                                </div>

                                <p style="color:#475569; font-size:11px; margin:10px 0 12px; line-height:1.4;">${data.description || '<i style="color:#cbd5e1;">Tidak ada deskripsi</i>'}</p>
                                
                                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px;">
                                    <div style="flex:1; min-width:60px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:800; color:#16a34a;">${data.players_count}</div>
                                        <div style="font-size:8px; font-weight:700; color:#16a34a; text-transform:uppercase;">Pemain</div>
                                    </div>
                                    <div style="flex:1; min-width:60px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:800; color:#2563eb;">${data.users_count}</div>
                                        <div style="font-size:8px; font-weight:700; color:#2563eb; text-transform:uppercase;">Pengguna</div>
                                    </div>
                                    <div style="flex:1; min-width:60px; background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:800; color:#ca8a04;">${data.tactics_count}</div>
                                        <div style="font-size:8px; font-weight:700; color:#ca8a04; text-transform:uppercase;">Taktik</div>
                                    </div>
                                    <div style="flex:1; min-width:60px; background:#fdf2f8; border:1px solid #fbcfe8; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:800; color:#db2777;">${data.matches_count}</div>
                                        <div style="font-size:8px; font-weight:700; color:#db2777; text-transform:uppercase;">Laga</div>
                                    </div>
                                    <div style="flex:1; min-width:60px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:700; color:#334155;">${data.schedules_count}</div>
                                        <div style="font-size:8px; color:#64748b; font-weight:600;">Jadwal</div>
                                    </div>
                                    <div style="flex:1; min-width:60px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:700; color:#334155;">${data.finances_count}</div>
                                        <div style="font-size:8px; color:#64748b; font-weight:600;">Kas</div>
                                    </div>
                                    <div style="flex:1; min-width:60px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:7px 4px; text-align:center;">
                                        <div style="font-size:15px; font-weight:700; color:#334155;">${data.announcements_count}</div>
                                        <div style="font-size:8px; color:#64748b; font-weight:600;">Info</div>
                                    </div>
                                </div>

                                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px;">
                                    <div style="font-size:11px; font-weight:800; color:#0f172a; margin-bottom:5px;">
                                        <i class="fa-solid fa-users text-emerald-500" style="margin-right:3px; font-size:10px;"></i>Anggota (${data.users_count})
                                    </div>
                                    <div style="line-height:1.7;">${membersHtml}</div>
                                </div>
                            </div>
                        `,
                        width: 500,
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#10b981',
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(() => {
                    Swal.fire('Error', 'Gagal memuat detail tim.', 'error');
                });
            }
        });
    }

    // === EDIT TEAM (UBAH PAKET) ===
    function showEditTeam(id, name, description, plan) {
        Swal.fire({
            title: '<span style="font-size:16px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-pen-to-square text-amber-500" style="margin-right:6px;"></i>Ubah Paket Langganan</span>',
            html: `
                <div style="text-align:left; font-family:inherit;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Paket Langganan untuk Tim: <strong style="color:#0f172a;">${name}</strong></label>
                        <select id="swal-edit-plan"
                            style="width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:12px; font-size:13px; color:#0f172a; background:#f8fafc; outline:none;">
                            <option value="free" ${plan==='free'?'selected':''}>Free / Gratis</option>
                            <option value="premium" ${plan==='premium'?'selected':''}>Premium</option>
                        </select>
                    </div>
                </div>
            `,
            width: 420,
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check" style="margin-right:4px;"></i>Simpan Perubahan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#94a3b8',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' },
            preConfirm: () => {
                return {
                    name: name,
                    plan: document.getElementById('swal-edit-plan').value,
                    description: description,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('editTeamForm');
                form.action = `/v1/superadmin/teams/${id}`;

                // Clear old dynamic inputs
                form.querySelectorAll('.dynamic-input').forEach(el => el.remove());

                // Add new inputs
                ['name', 'plan', 'description'].forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = field;
                    input.value = result.value[field];
                    input.classList.add('dynamic-input');
                    form.appendChild(input);
                });

                form.submit();
            }
        });
    }


</script>
@endsection