<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SuperadminController extends Controller
{
    public function index()
    {
        $totalTeams = Team::count();
        $totalUsers = User::count();
        $recentTeams = Team::orderBy('created_at', 'desc')->take(5)->get();
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        
        $totalRevenue = \App\Models\PremiumPayment::where('payment_status', 'paid')->sum('amount');

        return view('superadmin.index', compact('totalTeams', 'totalUsers', 'recentTeams', 'recentUsers', 'totalRevenue'));
    }

    public function teams()
    {
        $teams = Team::orderBy('created_at', 'desc')->get();
        return view('superadmin.teams', compact('teams'));
    }

    public function storeTeam(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'description' => 'nullable|string',
            'plan' => 'required|string|in:free,premium',
        ]);

        Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'plan' => $request->plan,
        ]);

        return back()->with('success', 'Tenant tim futsal baru berhasil terdaftar!');
    }

    public function showTeam($id)
    {
        $team = Team::withCount(['players', 'users', 'schedules', 'matches', 'finances', 'announcements', 'tactics'])->findOrFail($id);
        
        $members = User::with('role')->where('team_id', $team->id)->get()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role ? $u->role->name : '-',
                'created_at' => $u->created_at->isoFormat('D MMM YYYY'),
            ];
        });

        return response()->json([
            'id' => $team->id,
            'name' => $team->name,
            'description' => $team->description,
            'plan' => $team->plan,
            'is_premium' => $team->isPremium(),
            'premium_until' => $team->premium_until ? $team->premium_until->isoFormat('D MMM YYYY') : null,
            'logo' => $team->logo ? asset($team->logo) : null,
            'qris_image' => $team->qris_image ? asset($team->qris_image) : null,
            'players_count' => $team->players_count,
            'users_count' => $team->users_count,
            'schedules_count' => $team->schedules_count,
            'matches_count' => $team->matches_count,
            'finances_count' => $team->finances_count,
            'announcements_count' => $team->announcements_count,
            'tactics_count' => $team->tactics_count,
            'created_at' => $team->created_at->isoFormat('D MMM YYYY, HH:mm'),
            'updated_at' => $team->updated_at->isoFormat('D MMM YYYY, HH:mm'),
            'members' => $members,
        ]);
    }

    public function updateTeam(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string',
            'plan' => 'required|string|in:free,premium',
        ]);

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'plan' => $request->plan,
        ]);

        return back()->with('success', 'Data tenant "' . $team->name . '" berhasil diperbarui!');
    }

    public function destroyTeam($id)
    {
        $team = Team::findOrFail($id);
        $teamName = $team->name;

        // Delete all related data
        $team->tactics()->delete();
        $team->announcements()->delete();
        $team->finances()->delete();

        foreach ($team->schedules as $schedule) {
            $schedule->attendances()->delete();
        }
        $team->schedules()->delete();

        foreach ($team->matches as $match) {
            $match->statistics()->delete();
        }
        $team->matches()->delete();

        $team->players()->delete();

        // Detach users from this team (set team_id to null) or delete them
        User::where('team_id', $team->id)->update(['team_id' => null]);

        $team->delete();

        return back()->with('success', 'Tenant "' . $teamName . '" beserta seluruh datanya berhasil dihapus!');
    }

    public function users(Request $request)
    {
        $query = User::with(['team', 'role'])->orderBy('created_at', 'desc');

        // Search by Name or Email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by Team (Tenant)
        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        // Filter by Role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Paginate users (10 per page) and keep query strings
        $users = $query->paginate(10)->withQueryString();

        $teams = Team::orderBy('name', 'asc')->get();
        $roles = Role::orderBy('name', 'asc')->get();

        return view('superadmin.users', compact('users', 'teams', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        if ($request->team_id) {
            $team = Team::findOrFail($request->team_id);
            if ($team->isFree()) {
                if ($role->name === 'coach') {
                    $coachCount = $team->users()->whereHas('role', function($q) {
                        $q->where('name', 'coach');
                    })->count();
                    if ($coachCount >= 1) {
                        return back()->withInput()->with('error', 'Tim ' . $team->name . ' menggunakan paket Free yang membatasi maksimal 1 Coach. Silakan upgrade tim ke Premium terlebih dahulu.');
                    }
                } elseif ($role->name === 'management') {
                    $managementCount = $team->users()->whereHas('role', function($q) {
                        $q->where('name', 'management');
                    })->count();
                    if ($managementCount >= 1) {
                        return back()->withInput()->with('error', 'Tim ' . $team->name . ' menggunakan paket Free yang membatasi maksimal 1 akun Management. Silakan upgrade tim ke Premium terlebih dahulu.');
                    }
                }
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'team_id' => $request->team_id,
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function showUser($id)
    {
        $user = User::with(['team', 'role', 'player'])->findOrFail($id);

        $playerStats = null;
        if ($user->player) {
            $p = $user->player;
            $totalGoals = \App\Models\Statistic::where('player_id', $p->id)->sum('goals');
            $totalAssists = \App\Models\Statistic::where('player_id', $p->id)->sum('assists');
            $totalMatches = \App\Models\Statistic::where('player_id', $p->id)->count();
            $totalMinutes = \App\Models\Statistic::where('player_id', $p->id)->sum('minutes_played');
            $yellowCards = \App\Models\Statistic::where('player_id', $p->id)->sum('yellow_cards');
            $redCards = \App\Models\Statistic::where('player_id', $p->id)->sum('red_cards');

            $playerStats = [
                'number' => $p->number,
                'position' => $p->position,
                'phone' => $p->phone,
                'birth_date' => $p->birth_date,
                'height' => $p->height,
                'weight' => $p->weight,
                'goals' => $totalGoals,
                'assists' => $totalAssists,
                'matches' => $totalMatches,
                'minutes_played' => $totalMinutes,
                'yellow_cards' => $yellowCards,
                'red_cards' => $redCards,
            ];
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'slug' => $user->slug,
            'avatar' => $user->avatar ? asset($user->avatar) : null,
            'role' => $user->role ? $user->role->name : '-',
            'role_id' => $user->role_id,
            'team' => $user->team ? $user->team->name : 'Global (Tanpa Tim)',
            'team_id' => $user->team_id,
            'player' => $playerStats,
            'created_at' => $user->created_at->isoFormat('D MMM YYYY, HH:mm'),
            'updated_at' => $user->updated_at->isoFormat('D MMM YYYY, HH:mm'),
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        abort(403, 'Superadmin tidak diperbolehkan mengubah atau mengedit data pengguna.');
    }

    public function destroyUser($id)
    {
        abort(403, 'Superadmin tidak diperbolehkan menghapus pengguna.');
    }

    public function toggleUserLock($id)
    {
        $user = User::findOrFail($id);

        // Prevent locking oneself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengunci akun Anda sendiri!');
        }

        $user->is_locked = !$user->is_locked;
        $user->save();

        // Send email and cascade lock status
        if (!$user->is_locked) {
            // Send email to the unlocked user
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountUnlockedMail($user));
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email unlock ke ' . $user->email . ': ' . $e->getMessage());
            }

            // Cascade to the whole team if the user is a manager (role: management)
            if ($user->isManagement() && $user->team_id) {
                // Find currently locked team members to notify them before unlocking
                $teamUsers = User::where('team_id', $user->team_id)
                    ->where('id', '!=', $user->id)
                    ->where('is_locked', true)
                    ->get();

                foreach ($teamUsers as $tu) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($tu->email)->send(new \App\Mail\AccountUnlockedMail($tu));
                    } catch (\Exception $e) {
                        \Log::error('Gagal mengirim email unlock ke ' . $tu->email . ': ' . $e->getMessage());
                    }
                }

                // Unlock all team members in the DB
                User::where('team_id', $user->team_id)->update(['is_locked' => false]);
            }
        } else {
            // Lock the whole team if the user is a manager
            if ($user->isManagement() && $user->team_id) {
                User::where('team_id', $user->team_id)->update(['is_locked' => true]);
            }
        }

        $statusStr = $user->is_locked ? 'dikunci' : 'dibuka kuncinya';
        $cascadeStr = ($user->isManagement() && $user->team_id) ? ' beserta seluruh anggota timnya' : '';
        return back()->with('success', 'Akun "' . $user->name . '"' . $cascadeStr . ' berhasil ' . $statusStr . '!');
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('superadmin.settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = 'avatar_' . $user->id . '_' . time() . '.' . $avatarFile->getClientOriginalExtension();
            
            // Delete old avatar
            if ($user->avatar) {
                $googleOldPath = str_replace('images/', '', $user->avatar);
                if (\Illuminate\Support\Facades\Storage::disk('google')->exists($googleOldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($googleOldPath);
                }
                
                $localPath = public_path($user->avatar);
                if (file_exists($localPath) && is_file($localPath)) {
                    @unlink($localPath);
                }
            }
            
            // Upload to Google Drive under Avatars folder
            \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('Avatars', $avatarFile, $avatarName);
            $user->avatar = 'images/Avatars/' . $avatarName;
        }

        $user->save();

        return back()->with('success', 'Profile Superadmin berhasil diperbarui!');
    }

    public function editWebsite()
    {
        $settings = [
            'web_logo' => \App\Models\Setting::get('web_logo', 'images/logo.png'),
            'web_favicon' => \App\Models\Setting::get('web_favicon', 'favicon.ico'),
            'web_name' => \App\Models\Setting::get('web_name', 'FutsalHub'),
            'web_description' => \App\Models\Setting::get('web_description', ''),
            'web_keywords' => \App\Models\Setting::get('web_keywords', ''),
            'tripay_api_key' => \App\Models\Setting::get('tripay_api_key', ''),
            'tripay_private_key' => \App\Models\Setting::get('tripay_private_key', ''),
            'tripay_merchant_code' => \App\Models\Setting::get('tripay_merchant_code', ''),
            'tripay_merchant_name' => \App\Models\Setting::get('tripay_merchant_name', 'FutsalHub Sandbox'),
            'tripay_mode' => \App\Models\Setting::get('tripay_mode', 'sandbox'),
            'maintenance_mode' => \App\Models\Setting::get('maintenance_mode', '0') === '1',
            'platform_fee' => \App\Models\Setting::get('platform_fee', '5000'),
            'university_logo' => \App\Models\Setting::get('university_logo', 'images/Logo Universitas Harkat Negeri.webp'),
            'researcher_name' => \App\Models\Setting::get('researcher_name', 'Ilham Hatta Manggala'),
        ];
        return view('superadmin.settings.website', compact('settings'));
    }

    public function updateWebsite(Request $request)
    {
        $request->validate([
            'web_name' => 'required|string|max:255',
            'web_description' => 'nullable|string',
            'web_keywords' => 'nullable|string',
            'platform_fee' => 'required|string|max:255',
            'tripay_api_key' => 'nullable|string|max:255',
            'tripay_private_key' => 'nullable|string|max:255',
            'tripay_merchant_code' => 'nullable|string|max:255',
            'tripay_merchant_name' => 'nullable|string|max:255',
            'tripay_mode' => 'required|string|in:sandbox,production',
            'web_logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg,avif,jfif,ico|max:2048',
            'web_favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,webp,svg,avif,gif|max:1024',
            'researcher_name' => 'required|string|max:255',
            'university_logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg,avif,jfif|max:2048',
        ]);

        // Save text settings
        \App\Models\Setting::set('web_name', $request->web_name);
        \App\Models\Setting::set('web_description', $request->web_description);
        \App\Models\Setting::set('web_keywords', $request->web_keywords);
        
        // Clean platform fee
        $cleanFee = preg_replace('/[^0-9]/', '', $request->platform_fee);
        \App\Models\Setting::set('platform_fee', $cleanFee !== '' ? $cleanFee : '0');

        \App\Models\Setting::set('tripay_api_key', $request->tripay_api_key);
        \App\Models\Setting::set('tripay_private_key', $request->tripay_private_key);
        \App\Models\Setting::set('tripay_merchant_code', $request->tripay_merchant_code);
        \App\Models\Setting::set('tripay_merchant_name', $request->tripay_merchant_name);
        \App\Models\Setting::set('tripay_mode', $request->tripay_mode);

        // Save switch checkboxes
        \App\Models\Setting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');

        // Save researcher/developer credit
        \App\Models\Setting::set('researcher_name', $request->researcher_name);

        // Handle Web Logo Upload (save to Google Drive under Logos folder)
        if ($request->hasFile('web_logo')) {
            $logoFile = $request->file('web_logo');
            $logoName = 'web_logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            
            // Delete old logo
            $oldLogo = \App\Models\Setting::get('web_logo');
            if ($oldLogo && $oldLogo !== 'images/logo.png') {
                $googleOldPath = str_replace('images/', '', $oldLogo);
                if (\Illuminate\Support\Facades\Storage::disk('google')->exists($googleOldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($googleOldPath);
                }
                
                $localPath = public_path($oldLogo);
                if (file_exists($localPath) && is_file($localPath)) {
                    @unlink($localPath);
                }
            }
            
            // Upload to Google Drive
            \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('Logos', $logoFile, $logoName);
            \App\Models\Setting::set('web_logo', 'images/Logos/' . $logoName);
        }

        // Handle Web Favicon Upload (save to Google Drive under Logos folder)
        if ($request->hasFile('web_favicon')) {
            $faviconFile = $request->file('web_favicon');
            $faviconName = 'favicon_' . time() . '.' . $faviconFile->getClientOriginalExtension();
            
            // Delete old favicon
            $oldFavicon = \App\Models\Setting::get('web_favicon');
            if ($oldFavicon && $oldFavicon !== 'favicon.ico') {
                if (strpos($oldFavicon, 'images/') === 0) {
                    $googleOldPath = str_replace('images/', '', $oldFavicon);
                    if (\Illuminate\Support\Facades\Storage::disk('google')->exists($googleOldPath)) {
                        \Illuminate\Support\Facades\Storage::disk('google')->delete($googleOldPath);
                    }
                }
                
                $localPath = public_path($oldFavicon);
                if (file_exists($localPath) && is_file($localPath)) {
                    @unlink($localPath);
                }
                
                if (strpos($oldFavicon, 'images/') !== 0) {
                    $localLegacyPath = public_path($oldFavicon);
                    if (file_exists($localLegacyPath) && is_file($localLegacyPath)) {
                        @unlink($localLegacyPath);
                    }
                }
            }
            
            // Upload to Google Drive
            \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('Logos', $faviconFile, $faviconName);
            \App\Models\Setting::set('web_favicon', 'images/Logos/' . $faviconName);
        }

        // Handle University Logo Upload (save to Google Drive under Logos folder)
        if ($request->hasFile('university_logo')) {
            $uniLogoFile = $request->file('university_logo');
            $uniLogoName = 'university_logo_' . time() . '.' . $uniLogoFile->getClientOriginalExtension();

            // Delete old university logo (only if it's a previously uploaded one, not the seeded default)
            $oldUniLogo = \App\Models\Setting::get('university_logo');
            if ($oldUniLogo && $oldUniLogo !== 'images/Logo Universitas Harkat Negeri.webp') {
                $googleOldPath = str_replace('images/', '', $oldUniLogo);
                if (\Illuminate\Support\Facades\Storage::disk('google')->exists($googleOldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($googleOldPath);
                }

                $localPath = public_path($oldUniLogo);
                if (file_exists($localPath) && is_file($localPath)) {
                    @unlink($localPath);
                }
            }

            // Upload to Google Drive
            \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('Logos', $uniLogoFile, $uniLogoName);
            \App\Models\Setting::set('university_logo', 'images/Logos/' . $uniLogoName);
        }

        return back()->with('success', 'Pengaturan Website & Integrasi TriPay berhasil diperbarui!');
    }

    public function editLanding()
    {
        $settings = [
            'feat_title' => \App\Models\Setting::get('feat_title', 'Semua Kebutuhan Tim Futsal dalam Satu Tempat'),
            'feat_subtitle' => \App\Models\Setting::get('feat_subtitle', 'Platform kami mengintegrasikan manajemen operasional dan taktis klub agar pelatih, manajer, dan pemain dapat fokus berprestasi.'),
            'adv_title' => \App\Models\Setting::get('adv_title', 'Mengapa Tim Futsal Anda Harus Bergabung?'),
            'adv_subtitle' => \App\Models\Setting::get('adv_subtitle', 'Ucapkan selamat tinggal pada pencatatan manual di grup chat yang berantakan atau buku kas yang rawan hilang. Platform kami membawa pengelolaan tim futsal ke era digital.'),
            'cta_title' => \App\Models\Setting::get('cta_title', 'Siap Membawa Tim Futsal Anda ke Level Profesional?'),
            'cta_subtitle' => \App\Models\Setting::get('cta_subtitle', 'Daftarkan tim Anda sekarang atau masuk menggunakan akun demonstrasi untuk merasakan kehebatan integrasi papan taktik dan modul manajemen olahraga kami.'),
            
            // Feature 1
            'feat1_icon' => \App\Models\Setting::get('feat1_icon', 'fa-map'),
            'feat1_title' => \App\Models\Setting::get('feat1_title', 'Papan Taktik Interaktif'),
            'feat1_desc' => \App\Models\Setting::get('feat1_desc', 'Visualisasikan formasi menyerang dan bertahan (diamond, y-form, dll.) dengan menggeser ikon pemain dan menggambar rute pergerakan bola di lapangan virtual secara dinamis.'),
            
            // Feature 2
            'feat2_icon' => \App\Models\Setting::get('feat2_icon', 'fa-chart-line'),
            'feat2_title' => \App\Models\Setting::get('feat2_title', 'Statistik Kontribusi Pemain'),
            'feat2_desc' => \App\Models\Setting::get('feat2_desc', 'Catat data performa tiap individu di setiap pertandingan, termasuk jumlah gol, assist, kartu pelanggaran, hingga akumulasi menit bermain untuk melacak pemain terbaik tim.'),
            
            // Feature 3
            'feat3_icon' => \App\Models\Setting::get('feat3_icon', 'fa-money-bill-transfer'),
            'feat3_title' => \App\Models\Setting::get('feat3_title', 'Pembukuan Keuangan & Kas'),
            'feat3_desc' => \App\Models\Setting::get('feat3_desc', 'Manajemen kas tim yang transparan. Rekam pemasukan dari iuran patungan bulanan serta pengeluaran sewa lapangan atau turnamen demi kesehatan finansial tim.'),
            
            // Feature 4
            'feat4_icon' => \App\Models\Setting::get('feat4_icon', 'fa-calendar-check'),
            'feat4_title' => \App\Models\Setting::get('feat4_title', 'Agenda & Absensi Digital'),
            'feat4_desc' => \App\Models\Setting::get('feat4_desc', 'Jadwalkan latihan rutin atau sparring dengan mudah. Pemain dapat melakukan konfirmasi kehadiran (Hadir, Izin, Sakit) beserta catatan pendukung langsung di platform.'),
            
            // Feature 5
            'feat5_icon' => \App\Models\Setting::get('feat5_icon', 'fa-cubes'),
            'feat5_title' => \App\Models\Setting::get('feat5_title', 'Multi-Tenant Terisolasi'),
            'feat5_desc' => \App\Models\Setting::get('feat5_desc', 'Platform kami dirancang untuk menampung banyak tim. Data taktik, keuangan, dan pemain tim Anda tersimpan secara terisolasi dan aman tanpa bisa diakses oleh klub lain.'),
            
            // Feature 6
            'feat6_icon' => \App\Models\Setting::get('feat6_icon', 'fa-gauge-high'),
            'feat6_title' => \App\Models\Setting::get('feat6_title', 'Dasbor Ringkasan Real-time'),
            'feat6_desc' => \App\Models\Setting::get('feat6_desc', 'Dapatkan gambaran instan mengenai agenda latihan terdekat, kas aktif tim saat ini, pengumuman darurat manajemen, dan taktik terbaru yang siap diterapkan di pertandingan.'),
            
            // Advantages Card 1
            'adv1_icon' => \App\Models\Setting::get('adv1_icon', 'fa-user-tie'),
            'adv1_title' => \App\Models\Setting::get('adv1_title', 'Membantu Tugas Manager Klub'),
            'adv1_desc' => \App\Models\Setting::get('adv1_desc', 'Rekam keuangan bulanan dan absensi latihan secara terpusat untuk efisiensi pengambilan keputusan.'),
            
            // Advantages Card 2
            'adv2_icon' => \App\Models\Setting::get('adv2_icon', 'fa-clipboard-list'),
            'adv2_title' => \App\Models\Setting::get('adv2_title', 'Mendukung Analisis Pelatih'),
            'adv2_desc' => \App\Models\Setting::get('adv2_desc', 'Rancang strategi pertandingan di papan taktik dan pantau statistik kontribusi pemain untuk menetapkan line-up terbaik.'),
            
            // Advantages Card 3
            'adv3_icon' => \App\Models\Setting::get('adv3_icon', 'fa-users'),
            'adv3_title' => \App\Models\Setting::get('adv3_title', 'Keterbukaan Informasi Bagi Pemain'),
            'adv3_desc' => \App\Models\Setting::get('adv3_desc', 'Pemain dapat melihat pengumuman penting, rincian pengeluaran kas, serta statistik performa mereka secara transparan.'),
            
            // Statistics Card 1
            'stat1_val' => \App\Models\Setting::get('stat1_val', '50+'),
            'stat1_label' => \App\Models\Setting::get('stat1_label', 'Tim Futsal Terdaftar'),
            'stat1_desc' => \App\Models\Setting::get('stat1_desc', 'Mengelola klub futsal di berbagai tingkatan kompetisi regional.'),
            
            // Statistics Card 2
            'stat2_val' => \App\Models\Setting::get('stat2_val', '1,200+'),
            'stat2_label' => \App\Models\Setting::get('stat2_label', 'Taktik Dirancang'),
            'stat2_desc' => \App\Models\Setting::get('stat2_desc', 'Skema permainan, transisi, dan kick-off set-pieces tersimpan rapi.'),
            
            // Statistics Card 3
            'stat3_val' => \App\Models\Setting::get('stat3_val', '98%'),
            'stat3_label' => \App\Models\Setting::get('stat3_label', 'Kehadiran Terpantau'),
            'stat3_desc' => \App\Models\Setting::get('stat3_desc', 'Disiplin latihan terpantau secara transparan melalui sistem absensi digital.'),
        ];
        return view('superadmin.settings.landing', compact('settings'));
    }

    public function updateLanding(Request $request)
    {
        $request->validate([
            'feat_title' => 'required|string|max:255',
            'feat_subtitle' => 'required|string|max:1000',
            'adv_title' => 'required|string|max:255',
            'adv_subtitle' => 'required|string|max:1000',
            
            'feat1_icon' => 'required|string|max:255',
            'feat1_title' => 'required|string|max:255',
            'feat1_desc' => 'required|string|max:1000',
            
            'feat2_icon' => 'required|string|max:255',
            'feat2_title' => 'required|string|max:255',
            'feat2_desc' => 'required|string|max:1000',
            
            'feat3_icon' => 'required|string|max:255',
            'feat3_title' => 'required|string|max:255',
            'feat3_desc' => 'required|string|max:1000',
            
            'feat4_icon' => 'required|string|max:255',
            'feat4_title' => 'required|string|max:255',
            'feat4_desc' => 'required|string|max:1000',
            
            'feat5_icon' => 'required|string|max:255',
            'feat5_title' => 'required|string|max:255',
            'feat5_desc' => 'required|string|max:1000',
            
            'feat6_icon' => 'required|string|max:255',
            'feat6_title' => 'required|string|max:255',
            'feat6_desc' => 'required|string|max:1000',
            
            'adv1_icon' => 'required|string|max:255',
            'adv1_title' => 'required|string|max:255',
            'adv1_desc' => 'required|string|max:1000',
            
            'adv2_icon' => 'required|string|max:255',
            'adv2_title' => 'required|string|max:255',
            'adv2_desc' => 'required|string|max:1000',
            
            'adv3_icon' => 'required|string|max:255',
            'adv3_title' => 'required|string|max:255',
            'adv3_desc' => 'required|string|max:1000',
            
            'stat1_val' => 'required|string|max:255',
            'stat1_label' => 'required|string|max:255',
            'stat1_desc' => 'required|string|max:1000',
            
            'stat2_val' => 'required|string|max:255',
            'stat2_label' => 'required|string|max:255',
            'stat2_desc' => 'required|string|max:1000',
            
            'stat3_val' => 'required|string|max:255',
            'stat3_label' => 'required|string|max:255',
            'stat3_desc' => 'required|string|max:1000',
        ]);

        \App\Models\Setting::set('feat_title', $request->feat_title);
        \App\Models\Setting::set('feat_subtitle', $request->feat_subtitle);
        \App\Models\Setting::set('adv_title', $request->adv_title);
        \App\Models\Setting::set('adv_subtitle', $request->adv_subtitle);

        for ($i = 1; $i <= 6; $i++) {
            $iconVal = $request->input("feat{$i}_icon");
            if ($iconVal && !str_starts_with($iconVal, 'fa-') && !str_starts_with($iconVal, 'fa-solid') && !str_starts_with($iconVal, 'fa-regular') && !str_starts_with($iconVal, 'fa-brands')) {
                $iconVal = 'fa-' . $iconVal;
            }
            \App\Models\Setting::set("feat{$i}_icon", $iconVal);
            \App\Models\Setting::set("feat{$i}_title", $request->input("feat{$i}_title"));
            \App\Models\Setting::set("feat{$i}_desc", $request->input("feat{$i}_desc"));
        }

        for ($i = 1; $i <= 3; $i++) {
            $iconVal = $request->input("adv{$i}_icon");
            if ($iconVal && !str_starts_with($iconVal, 'fa-') && !str_starts_with($iconVal, 'fa-solid') && !str_starts_with($iconVal, 'fa-regular') && !str_starts_with($iconVal, 'fa-brands')) {
                $iconVal = 'fa-' . $iconVal;
            }
            \App\Models\Setting::set("adv{$i}_icon", $iconVal);
            \App\Models\Setting::set("adv{$i}_title", $request->input("adv{$i}_title"));
            \App\Models\Setting::set("adv{$i}_desc", $request->input("adv{$i}_desc"));
        }

        for ($i = 1; $i <= 3; $i++) {
            \App\Models\Setting::set("stat{$i}_val", $request->input("stat{$i}_val"));
            \App\Models\Setting::set("stat{$i}_label", $request->input("stat{$i}_label"));
            \App\Models\Setting::set("stat{$i}_desc", $request->input("stat{$i}_desc"));
        }

        return back()->with('success', 'Konten halaman landing page berhasil diperbarui!');
    }

    public function getLandingApi()
    {
        try {
            $totalTeams = Team::count();
            $totalPlayers = \App\Models\Player::count();
            $totalSchedules = \App\Models\Schedule::count();
        } catch (\Exception $e) {
            $totalTeams = 0;
            $totalPlayers = 0;
            $totalSchedules = 0;
        }

        $stat1Val = \App\Models\Setting::get('stat1_val', '50+');
        if ($stat1Val === '50+') {
            $stat1Val = $totalTeams;
        }

        $stat2Val = \App\Models\Setting::get('stat2_val', '1,200+');
        $stat2Label = \App\Models\Setting::get('stat2_label', 'Taktik Dirancang');
        $stat2Desc = \App\Models\Setting::get('stat2_desc', 'Skema permainan, transisi, dan kick-off set-pieces tersimpan rapi.');

        if ($stat2Val === '1,200+') {
            $stat2Val = $totalPlayers;
        }
        if ($stat2Label === 'Taktik Dirancang') {
            $stat2Label = 'Pemain Terdaftar';
            $stat2Desc = 'Atlet futsal yang mengelola performa dan kontribusi statistik mereka.';
        }

        $stat3Val = \App\Models\Setting::get('stat3_val', '98%');
        $stat3Label = \App\Models\Setting::get('stat3_label', 'Kehadiran Terpantau');
        $stat3Desc = \App\Models\Setting::get('stat3_desc', 'Disiplin latihan terpantau secara transparan melalui sistem absensi digital.');

        if ($stat3Val === '98%') {
            $stat3Val = $totalSchedules;
        }
        if ($stat3Label === 'Kehadiran Terpantau') {
            $stat3Label = 'Kegiatan Terjadwal';
            $stat3Desc = 'Total agenda latihan, tanding, dan absensi yang berhasil terorganisir.';
        }

        return response()->json([
            'feat_title' => \App\Models\Setting::get('feat_title', 'Semua Kebutuhan Tim Futsal dalam Satu Tempat'),
            'feat_subtitle' => \App\Models\Setting::get('feat_subtitle', 'Platform kami mengintegrasikan manajemen operasional dan taktis klub agar pelatih, manajer, dan pemain dapat fokus berprestasi.'),
            'adv_title' => \App\Models\Setting::get('adv_title', 'Mengapa Tim Futsal Anda Harus Bergabung?'),
            'adv_subtitle' => \App\Models\Setting::get('adv_subtitle', 'Ucapkan selamat tinggal pada pencatatan manual di grup chat yang berantakan atau buku kas yang rawan hilang. Platform kami membawa pengelolaan tim futsal ke era digital.'),
            
            // Features
            'feat1_icon' => \App\Models\Setting::get('feat1_icon', 'fa-map'),
            'feat1_title' => \App\Models\Setting::get('feat1_title', 'Papan Taktik Interaktif'),
            'feat1_desc' => \App\Models\Setting::get('feat1_desc', 'Visualisasikan formasi menyerang dan bertahan (diamond, y-form, dll.) dengan menggeser ikon pemain dan menggambar rute pergerakan bola di lapangan virtual secara dinamis.'),
            
            'feat2_icon' => \App\Models\Setting::get('feat2_icon', 'fa-chart-line'),
            'feat2_title' => \App\Models\Setting::get('feat2_title', 'Statistik Kontribusi Pemain'),
            'feat2_desc' => \App\Models\Setting::get('feat2_desc', 'Catat data performa tiap individu di setiap pertandingan, termasuk jumlah gol, assist, kartu pelanggaran, hingga akumulasi menit bermain untuk melacak pemain terbaik tim.'),
            
            'feat3_icon' => \App\Models\Setting::get('feat3_icon', 'fa-money-bill-transfer'),
            'feat3_title' => \App\Models\Setting::get('feat3_title', 'Pembukuan Keuangan & Kas'),
            'feat3_desc' => \App\Models\Setting::get('feat3_desc', 'Manajemen kas tim yang transparan. Rekam pemasukan dari iuran patungan bulanan serta pengeluaran sewa lapangan atau turnamen demi kesehatan finansial tim.'),
            
            'feat4_icon' => \App\Models\Setting::get('feat4_icon', 'fa-calendar-check'),
            'feat4_title' => \App\Models\Setting::get('feat4_title', 'Agenda & Absensi Digital'),
            'feat4_desc' => \App\Models\Setting::get('feat4_desc', 'Jadwalkan latihan rutin atau sparring dengan mudah. Pemain dapat melakukan konfirmasi kehadiran (Hadir, Izin, Sakit) beserta catatan pendukung langsung di platform.'),
            
            'feat5_icon' => \App\Models\Setting::get('feat5_icon', 'fa-cubes'),
            'feat5_title' => \App\Models\Setting::get('feat5_title', 'Multi-Tenant Terisolasi'),
            'feat5_desc' => \App\Models\Setting::get('feat5_desc', 'Platform kami dirancang untuk menampung banyak tim. Data taktik, keuangan, dan pemain tim Anda tersimpan secara terisolasi dan aman tanpa bisa diakses oleh klub lain.'),
            
            'feat6_icon' => \App\Models\Setting::get('feat6_icon', 'fa-gauge-high'),
            'feat6_title' => \App\Models\Setting::get('feat6_title', 'Dasbor Ringkasan Real-time'),
            'feat6_desc' => \App\Models\Setting::get('feat6_desc', 'Dapatkan gambaran instan mengenai agenda latihan terdekat, kas aktif tim saat ini, pengumuman darurat manajemen, dan taktik terbaru yang siap diterapkan di pertandingan.'),
            
            // Advantages
            'adv1_icon' => \App\Models\Setting::get('adv1_icon', 'fa-user-tie'),
            'adv1_title' => \App\Models\Setting::get('adv1_title', 'Membantu Tugas Manager Klub'),
            'adv1_desc' => \App\Models\Setting::get('adv1_desc', 'Rekam keuangan bulanan dan absensi latihan secara terpusat untuk efisiensi pengambilan keputusan.'),
            
            'adv2_icon' => \App\Models\Setting::get('adv2_icon', 'fa-clipboard-list'),
            'adv2_title' => \App\Models\Setting::get('adv2_title', 'Mendukung Analisis Pelatih'),
            'adv2_desc' => \App\Models\Setting::get('adv2_desc', 'Rancang strategi pertandingan di papan taktik dan pantau statistik kontribusi pemain untuk menetapkan line-up terbaik.'),
            
            'adv3_icon' => \App\Models\Setting::get('adv3_icon', 'fa-users'),
            'adv3_title' => \App\Models\Setting::get('adv3_title', 'Keterbukaan Informasi Bagi Pemain'),
            'adv3_desc' => \App\Models\Setting::get('adv3_desc', 'Pemain dapat melihat pengumuman penting, rincian pengeluaran kas, serta statistik performa mereka secara transparan.'),
            
            // Stats
            'stat1_val' => $stat1Val,
            'stat1_label' => \App\Models\Setting::get('stat1_label', 'Tim Futsal Terdaftar'),
            'stat1_desc' => \App\Models\Setting::get('stat1_desc', 'Mengelola klub futsal di berbagai tingkatan kompetisi regional.'),
            
            'stat2_val' => $stat2Val,
            'stat2_label' => $stat2Label,
            'stat2_desc' => $stat2Desc,
            
            'stat3_val' => $stat3Val,
            'stat3_label' => $stat3Label,
            'stat3_desc' => $stat3Desc,
        ]);
    }
}
