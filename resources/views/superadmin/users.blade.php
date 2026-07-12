@extends('layouts.app')

@section('title', 'Monitor Platform Users')
@section('header_title', 'Kelola Pengguna Platform')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Left Column: Users list table -->
    <div class="xl:col-span-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Pengguna</h3>
                <p class="text-xs text-slate-500">Seluruh akun yang terdaftar dalam platform beserta hak aksesnya</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-150 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-users text-emerald-600 text-xs"></i>
                <span id="users-count-badge" class="text-[11px] font-bold text-emerald-700">{{ $users->total() }} Pengguna</span>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 rounded-3xl border border-slate-150 shadow-sm">
            <form id="users-filter-form" action="{{ route('superadmin.users') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 text-xs transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                </div>

                <!-- Team -->
                <div>
                    <select name="team_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs transition-all">
                        <option value="">Semua Tim</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" @selected(request('team_id') == $team->id)>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Role -->
                <div>
                    <select name="role_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 text-xs transition-all">
                        <option value="">Semua Peran</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs py-2.5 transition-colors">
                        Cari & Filter
                    </button>
                    @if(request()->anyFilled(['search', 'team_id', 'role_id']))
                        <a href="{{ route('superadmin.users') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs px-3 py-2.5 flex items-center justify-center transition-colors" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div id="users-table-container" class="space-y-6">
            <div class="card-white rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">Nama Pengguna</th>
                                <th class="py-4 px-6">Tenant Tim</th>
                                <th class="py-4 px-6 text-center">Peran Akses (Role)</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @forelse($users as $u)
                                @php
                                    $roleColors = [
                                        'superadmin' => 'bg-red-50 border-red-150 text-red-700',
                                        'management' => 'bg-teal-50 border-teal-150 text-teal-700',
                                        'coach' => 'bg-yellow-50 border-yellow-150 text-yellow-800',
                                        'player' => 'bg-blue-50 border-blue-150 text-blue-700',
                                    ];
                                    $roleLabel = [
                                        'superadmin' => 'Superadmin',
                                        'management' => 'Manager',
                                        'coach' => 'Pelatih',
                                        'player' => 'Pemain',
                                    ];
                                    $roleClass = $roleColors[$u->role->name] ?? 'bg-slate-50 border-slate-200 text-slate-600';
                                    $roleName = $roleLabel[$u->role->name] ?? 'User';
                                    $emailColor = $u->role->name === 'superadmin' ? 'text-red-650' : ($u->role->name === 'management' ? 'text-teal-655' : ($u->role->name === 'coach' ? 'text-yellow-800' : 'text-slate-655'));
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Name & Email combined -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900">{{ $u->name }}</span>
                                            @if($u->is_locked)
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-[9px] font-black bg-rose-50 text-rose-600 border border-rose-100 rounded-md uppercase tracking-wider">
                                                    <i class="fa-solid fa-lock text-[8px]"></i> Terkunci
                                                </span>
                                            @endif
                                        </div>
                                        @php
                                            $emailParts = explode('@', $u->email);
                                            $username = $emailParts[0] ?? '';
                                            $domain = $emailParts[1] ?? '';
                                            $keepLen = strlen($username) > 4 ? 4 : (strlen($username) > 1 ? 2 : 1);
                                            $maskedEmail = substr($username, 0, $keepLen) . str_repeat('*', strlen($username) - $keepLen) . '@' . $domain;
                                        @endphp
                                        <div class="text-xs text-slate-400 font-normal mt-0.5">{{ $maskedEmail }}</div>
                                    </td>

                                    <!-- Team -->
                                    <td class="py-4 px-6 text-xs font-semibold">
                                        @if($u->team)
                                            <span class="text-slate-900">
                                                {{ $u->team->name }}
                                            </span>
                                        @else
                                            <span class="italic text-slate-400 font-medium">Global</span>
                                        @endif
                                    </td>

                                    <!-- Role -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold border rounded-md uppercase tracking-wider {{ $roleClass }}">
                                            {{ $roleName }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button onclick="showUserDetail({{ $u->id }})" title="Detail Pengguna"
                                                class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 hover:text-blue-700 flex items-center justify-center transition-all text-xs">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            @if($u->id !== auth()->id())
                                                @if($u->is_locked)
                                                    <button onclick="toggleUserLock({{ $u->id }}, 'unlock')" title="Buka Kunci Akun"
                                                        class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center transition-all text-xs">
                                                        <i class="fa-solid fa-unlock-keyhole"></i>
                                                    </button>
                                                @else
                                                    <button onclick="toggleUserLock({{ $u->id }}, 'lock')" title="Kunci Akun"
                                                        class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center transition-all text-xs">
                                                        <i class="fa-solid fa-user-lock"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-400 text-sm font-medium">
                                        <i class="fa-solid fa-users-gear text-4xl mb-3 block text-slate-300"></i>
                                        Belum ada pengguna.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Custom Light-Themed Pagination Links -->
            @if ($users->lastPage() > 1)
                <div class="flex items-center justify-between border-t border-slate-100 pt-4 flex-wrap gap-4 font-semibold text-xs text-slate-600">
                    <!-- Info -->
                    <span class="text-xs text-slate-500 font-semibold">
                        Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} hasil
                    </span>

                    <!-- Page Buttons -->
                    <div class="flex items-center gap-1.5">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-350 bg-slate-50 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-655 hover:text-emerald-600 hover:border-emerald-500 bg-white flex items-center justify-center text-xs font-bold transition-all ajax-page-link">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </a>
                        @endif

                        {{-- Array of pages --}}
                        @for ($i = 1; $i <= $users->lastPage(); $i++)
                            @if ($i == $users->currentPage())
                                <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-extrabold shadow-sm shadow-emerald-500/20">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $users->url($i) }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-655 hover:text-emerald-600 hover:border-emerald-500 bg-white flex items-center justify-center text-xs font-bold transition-all ajax-page-link">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-655 hover:text-emerald-600 hover:border-emerald-500 bg-white flex items-center justify-center text-xs font-bold transition-all ajax-page-link">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-350 bg-slate-50 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Add User Form -->
    <div class="xl:col-span-4">
        <div class="card-white p-6 rounded-3xl space-y-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-user-plus text-emerald-600 mr-2"></i>Daftarkan Pengguna</h3>
                <p class="text-xs text-slate-500">Tambahkan akun pengguna baru global ke dalam platform</p>
            </div>
            
            <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama lengkap pengguna" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Email</label>
                    <input type="email" name="email" placeholder="email@domain.com" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Peran Akses (Role)</label>
                    <select name="role_id" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Keterikatan Tenant Tim</label>
                    <select name="team_id"
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm transition-all">
                        <option value="">Global / Superadmin (Tanpa Tim)</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <span class="block text-[10px] text-slate-500 mt-1.5 leading-relaxed">Hanya kosongkan keterikatan tim jika memilih peran 'Superadmin'.</span>
                </div>

                <button type="submit" 
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/10">
                    Simpan Akun Pengguna
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Hidden forms for edit & delete -->
<form id="editUserForm" method="POST" style="display:none;">
    @csrf
    @method('PUT')
</form>

@endsection

@section('scripts')
<script>
    function maskEmail(email) {
        if (!email) return '';
        var parts = email.split('@');
        if (parts.length !== 2) return email;
        var username = parts[0];
        var domain = parts[1];
        var keepLen = username.length > 4 ? 4 : (username.length > 1 ? 2 : 1);
        var visiblePart = username.substring(0, keepLen);
        var maskedPart = '*'.repeat(username.length - keepLen);
        return visiblePart + maskedPart + '@' + domain;
    }
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('users-table-container');
    const filterForm = document.getElementById('users-filter-form');
    
    if (!tableContainer) return;

    // Function to load users via AJAX
    function loadUsers(url) {
        // Opacity transition for loading state
        tableContainer.style.opacity = '0.4';
        tableContainer.style.transition = 'opacity 0.2s ease-in-out';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Replace table inner content
            const newContent = doc.getElementById('users-table-container');
            if (newContent) {
                tableContainer.innerHTML = newContent.innerHTML;
            }
            
            // Replace count badge
            const newBadge = doc.getElementById('users-count-badge');
            const currentBadge = document.getElementById('users-count-badge');
            if (newBadge && currentBadge) {
                currentBadge.innerHTML = newBadge.innerHTML;
            }
            
            // Scroll container smoothly to top
            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(err => {
            console.error('AJAX Error:', err);
        })
        .finally(() => {
            // Restore opacity
            tableContainer.style.opacity = '1';
        });
    }

    // Capture clicks on pagination links using event delegation
    tableContainer.addEventListener('click', function(e) {
        const link = e.target.closest('.ajax-page-link');
        if (link) {
            e.preventDefault();
            const urlStr = link.getAttribute('href');
            if (urlStr) {
                let url = urlStr;
                try {
                    // Convert absolute URL to relative path to solve HTTPS Mixed Content blocking on proxies like Ngrok
                    const parsed = new URL(urlStr, window.location.origin);
                    url = parsed.pathname + parsed.search;
                } catch(err) {
                    // Fallback
                }
                loadUsers(url);
                // Update address bar without refreshing the page
                window.history.pushState({ path: url }, '', url);
            }
        }
    });

    // Capture form submission to apply filters via AJAX
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            const action = filterForm.getAttribute('action') || window.location.pathname;
            let url = `${action}?${params}`;
            try {
                const parsed = new URL(url, window.location.origin);
                url = parsed.pathname + parsed.search;
            } catch(err) {}
            
            loadUsers(url);
            window.history.pushState({ path: url }, '', url);
        });

        // Capture reset button click
        const resetBtn = filterForm.querySelector('a[title="Reset Filter"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const urlStr = resetBtn.getAttribute('href');
                if (urlStr) {
                    let url = urlStr;
                    try {
                        const parsed = new URL(urlStr, window.location.origin);
                        url = parsed.pathname + parsed.search;
                    } catch(err) {}
                    
                    // Clear fields visually
                    filterForm.querySelector('input[name="search"]').value = '';
                    filterForm.querySelector('select[name="team_id"]').value = '';
                    filterForm.querySelector('select[name="role_id"]').value = '';

                    loadUsers(url);
                    window.history.pushState({ path: url }, '', url);
                }
            });
        }
    }
});

// === DETAIL USER ===
function showUserDetail(id) {
    Swal.fire({
        title: '<i class="fa-solid fa-spinner fa-spin text-emerald-500"></i>',
        html: 'Memuat detail pengguna...',
        showConfirmButton: false,
        allowOutsideClick: true,
        didOpen: () => {
            fetch(`/v1/superadmin/users/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const roleColors = {
                    'superadmin': { bg: '#fef2f2', border: '#fecaca', text: '#dc2626' },
                    'management': { bg: '#f0fdfa', border: '#99f6e4', text: '#0d9488' },
                    'coach': { bg: '#fefce8', border: '#fde68a', text: '#a16207' },
                    'player': { bg: '#eff6ff', border: '#bfdbfe', text: '#2563eb' },
                };
                const roleLabels = { 'superadmin': 'Superadmin', 'management': 'Manager', 'coach': 'Pelatih', 'player': 'Pemain' };
                const rc = roleColors[data.role] || { bg: '#f8fafc', border: '#e2e8f0', text: '#64748b' };
                const roleDisplay = roleLabels[data.role] || data.role;

                const avatarHtml = data.avatar
                    ? `<img src="${data.avatar}" style="width:56px; height:56px; border-radius:50%; object-fit:cover; border:3px solid ${rc.border};">`
                    : `<div style="width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg, #10b981, #14b8a6); display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800; color:white; border:3px solid #a7f3d0;">${data.name.charAt(0)}</div>`;

                let playerHtml = '';
                if (data.player) {
                    const p = data.player;
                    playerHtml = `
                        <div style="border-top:1px solid #f1f5f9; padding-top:14px; margin-top:14px;">
                            <div style="font-size:12px; font-weight:800; color:#0f172a; margin-bottom:10px;">
                                <i class="fa-solid fa-futbol text-emerald-500" style="margin-right:4px;"></i>Data Pemain
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:6px; margin-bottom:10px;">
                                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:8px; text-align:center;">
                                    <div style="font-size:16px; font-weight:800; color:#16a34a;">${p.number || '-'}</div>
                                    <div style="font-size:9px; font-weight:700; color:#16a34a;">NO. PUNGGUNG</div>
                                </div>
                                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:8px; text-align:center;">
                                    <div style="font-size:11px; font-weight:800; color:#2563eb;">${p.position || '-'}</div>
                                    <div style="font-size:9px; font-weight:700; color:#2563eb;">POSISI</div>
                                </div>
                                <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:8px; text-align:center;">
                                    <div style="font-size:11px; font-weight:800; color:#ca8a04;">${p.matches}</div>
                                    <div style="font-size:9px; font-weight:700; color:#ca8a04;">LAGA</div>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:6px;">
                                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:6px; text-align:center;">
                                    <div style="font-size:14px; font-weight:700; color:#16a34a;">${p.goals}</div>
                                    <div style="font-size:8px; color:#64748b; font-weight:600;">Gol</div>
                                </div>
                                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:6px; text-align:center;">
                                    <div style="font-size:14px; font-weight:700; color:#2563eb;">${p.assists}</div>
                                    <div style="font-size:8px; color:#64748b; font-weight:600;">Assist</div>
                                </div>
                                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:6px; text-align:center;">
                                    <div style="font-size:14px; font-weight:700; color:#eab308;">${p.yellow_cards}</div>
                                    <div style="font-size:8px; color:#64748b; font-weight:600;">Kuning</div>
                                </div>
                                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:6px; text-align:center;">
                                    <div style="font-size:14px; font-weight:700; color:#dc2626;">${p.red_cards}</div>
                                    <div style="font-size:8px; color:#64748b; font-weight:600;">Merah</div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                Swal.update({
                    title: '',
                    html: `
                        <div style="text-align:left; font-family:inherit;">
                            <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                                ${avatarHtml}
                                <div>
                                    <div style="font-size:17px; font-weight:800; color:#0f172a;">${data.name}</div>
                                    <div style="font-size:12px; color:#64748b; margin-top:2px;">${maskEmail(data.email)}</div>
                                    <div style="display:flex; align-items:center; gap:6px; margin-top:6px;">
                                        <span style="background:${rc.bg}; color:${rc.text}; border:1px solid ${rc.border}; padding:2px 10px; border-radius:6px; font-size:10px; font-weight:800; text-transform:uppercase;">${roleDisplay}</span>
                                        <span style="font-size:10px; color:#94a3b8; font-weight:600;">ID #${data.id}</span>
                                    </div>
                                </div>
                            </div>

                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px; margin-bottom:14px;">
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:12px;">
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:10px; text-transform:uppercase;">Tim Futsal</span>
                                        <div style="font-weight:700; color:#0f172a; margin-top:2px;">${data.team}</div>
                                    </div>
                                    <div>
                                        <span style="color:#94a3b8; font-weight:600; font-size:10px; text-transform:uppercase;">Slug URL</span>
                                        <div style="font-weight:700; color:#0f172a; margin-top:2px; font-family:monospace; font-size:11px;">${data.slug || '-'}</div>
                                    </div>
                                </div>
                            </div>

                            ${playerHtml}

                            <div style="display:flex; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid #f1f5f9;">
                                <span style="font-size:10px; color:#94a3b8;"><i class="fa-solid fa-calendar-plus" style="margin-right:3px;"></i>Dibuat: ${data.created_at}</span>
                                <span style="font-size:10px; color:#94a3b8;"><i class="fa-solid fa-pen" style="margin-right:3px;"></i>Diperbarui: ${data.updated_at}</span>
                            </div>
                        </div>
                    `,
                    width: 520,
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#10b981',
                    customClass: { popup: 'rounded-3xl' }
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat detail pengguna.', 'error');
            });
        }
    });
}

// === EDIT USER (UBAH PASSWORD) ===
function showEditUser(id) {
    // Fetch current data first
    Swal.fire({
        title: '<i class="fa-solid fa-spinner fa-spin text-amber-500"></i>',
        html: 'Memuat data pengguna...',
        showConfirmButton: false,
        allowOutsideClick: true,
        didOpen: () => {
            fetch(`/v1/superadmin/users/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                Swal.update({
                    title: '<span style="font-size:16px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-key text-amber-500" style="margin-right:6px;"></i>Ubah Password</span>',
                    html: `
                        <div style="text-align:left; font-family:inherit;">
                            <div style="margin-bottom:14px; padding-bottom:10px; border-b:1px solid #f1f5f9;">
                                <span style="font-size:12px; color:#64748b;">Pengguna: <strong style="color:#0f172a;">${data.name}</strong> (${maskEmail(data.email)})</span>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Kata Sandi Baru</label>
                                <input id="swal-edit-password" type="password" placeholder="Masukkan kata sandi baru" required
                                    style="width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:12px; font-size:13px; color:#0f172a; background:#f8fafc; outline:none;"
                                    onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.1)'"
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </div>
                            <div style="margin-bottom:12px;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Konfirmasi Kata Sandi Baru</label>
                                <input id="swal-edit-password-confirm" type="password" placeholder="Konfirmasi kata sandi baru" required
                                    style="width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:12px; font-size:13px; color:#0f172a; background:#f8fafc; outline:none;"
                                    onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.1)'"
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </div>
                        </div>
                    `,
                    width: 420,
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fa-solid fa-check" style="margin-right:4px;"></i>Simpan Perubahan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#94a3b8',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' },
                    preConfirm: () => {
                        const password = document.getElementById('swal-edit-password').value.trim();
                        const confirm = document.getElementById('swal-edit-password-confirm').value.trim();
                        if (!password) {
                            Swal.showValidationMessage('Kata sandi baru tidak boleh kosong!');
                            return false;
                        }
                        if (password.length < 6) {
                            Swal.showValidationMessage('Kata sandi baru minimal 6 karakter!');
                            return false;
                        }
                        if (password !== confirm) {
                            Swal.showValidationMessage('Konfirmasi kata sandi tidak cocok!');
                            return false;
                        }
                        return {
                            name: data.name,
                            email: data.email,
                            password: password,
                            role_id: data.role_id,
                            team_id: data.team_id || '',
                        };
                    }
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat data pengguna.', 'error');
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.getElementById('editUserForm');
            form.action = `/v1/superadmin/users/${id}`;

            // Clear old dynamic inputs
            form.querySelectorAll('.dynamic-input').forEach(el => el.remove());

            // Add new inputs
            ['name', 'email', 'password', 'role_id', 'team_id'].forEach(field => {
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

function toggleUserLock(id, action) {
    const actionText = action === 'unlock' ? 'membuka kunci' : 'mengunci';
    const confirmBtnColor = action === 'unlock' ? '#10b981' : '#ef4444';
    
    Swal.fire({
        title: action === 'unlock' ? 'Buka Kunci Akun?' : 'Kunci Akun?',
        text: `Apakah Anda yakin ingin ${actionText} akun pengguna ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: confirmBtnColor,
        cancelButtonColor: '#94a3b8',
        confirmButtonText: action === 'unlock' ? 'Ya, Buka Kunci' : 'Ya, Kunci Akun',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/v1/superadmin/users/${id}/toggle-lock`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}


</script>
@endsection
