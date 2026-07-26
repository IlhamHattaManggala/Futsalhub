<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Schedule;
use App\Models\Finance;
use App\Models\Announcement;
use App\Models\Tactic;
use App\Models\MatchGame;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $user->team;

        if ($team && $team->isFreeExpired()) {
            if (!$team->free_expiration_email_sent) {
                $manager = $team->users()->whereHas('role', function ($query) {
                    $query->where('name', 'management');
                })->first() ?? $team->users()->first();

                if ($manager) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($manager->email)
                            ->send(new \App\Mail\FreeTrialExpiredMail($manager, $team));
                        
                        $team->free_expiration_email_sent = true;
                        $team->save();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send free trial expiration email: ' . $e->getMessage());
                    }
                }
            }

            return view('dashboard.expired', compact('user', 'team'));
        }

        $teamId = $user->team_id;

        // General stats
        $totalPlayers = Player::where('team_id', $teamId)->onlyPlayers()->count();
        
        $coachRole = \App\Models\Role::where('name', 'coach')->first();
        $totalCoaches = $coachRole 
            ? \App\Models\User::where('team_id', $teamId)->where('role_id', $coachRole->id)->count()
            : 0;

        $income = Finance::where('team_id', $teamId)->where('type', 'Pemasukan')->sum('amount');
        $expense = Finance::where('team_id', $teamId)->where('type', 'Pengeluaran')->sum('amount');
        $balance = $income - $expense;

        // Upcoming schedules
        $upcomingSchedules = Schedule::where('team_id', $teamId)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        // Recent announcements
        $announcements = Announcement::where('team_id', $teamId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Recent tactics
        $tactics = Tactic::where('team_id', $teamId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Leaderboards (Top Scorers & Top Assists)
        // Aggregating from matches statistic with time filters
        $topFilter = $request->query('top_filter', 'all'); // 'week', 'month', 'year', 'all'

        $scorerQuery = DB::table('statistics')
            ->join('players', 'statistics.player_id', '=', 'players.id')
            ->join('matches', 'statistics.match_id', '=', 'matches.id')
            ->where('players.team_id', $teamId);

        $assistQuery = DB::table('statistics')
            ->join('players', 'statistics.player_id', '=', 'players.id')
            ->join('matches', 'statistics.match_id', '=', 'matches.id')
            ->where('players.team_id', $teamId);

        if ($topFilter === 'week') {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            $scorerQuery->whereBetween('matches.date', [$startOfWeek, $endOfWeek]);
            $assistQuery->whereBetween('matches.date', [$startOfWeek, $endOfWeek]);
        } elseif ($topFilter === 'month') {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $scorerQuery->whereBetween('matches.date', [$startOfMonth, $endOfMonth]);
            $assistQuery->whereBetween('matches.date', [$startOfMonth, $endOfMonth]);
        } elseif ($topFilter === 'year') {
            $startOfYear = now()->startOfYear();
            $endOfYear = now()->endOfYear();
            $scorerQuery->whereBetween('matches.date', [$startOfYear, $endOfYear]);
            $assistQuery->whereBetween('matches.date', [$startOfYear, $endOfYear]);
        }

        $topScorers = $scorerQuery
            ->select('players.name', 'players.number', 'players.position', DB::raw('SUM(statistics.goals) as total_goals'))
            ->groupBy('players.id', 'players.name', 'players.number', 'players.position')
            ->having('total_goals', '>', 0)
            ->orderBy('total_goals', 'desc')
            ->take(5)
            ->get();

        $topAssists = $assistQuery
            ->select('players.name', 'players.number', 'players.position', DB::raw('SUM(statistics.assists) as total_assists'))
            ->groupBy('players.id', 'players.name', 'players.number', 'players.position')
            ->having('total_assists', '>', 0)
            ->orderBy('total_assists', 'desc')
            ->take(5)
            ->get();

        // Finance Cashflow Monthly Data for Chart.js
        $dateFormat = config('database.default') === 'sqlite' 
            ? "strftime('%Y-%m', date) as month" 
            : "DATE_FORMAT(date, '%Y-%m') as month";

        $monthlyFinance = Finance::where('team_id', $teamId)
            ->select(
                DB::raw($dateFormat),
                DB::raw("SUM(CASE WHEN type = 'Pemasukan' THEN amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN type = 'Pengeluaran' THEN amount ELSE 0 END) as total_expense")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'topScorers' => $topScorers,
                'topAssists' => $topAssists,
            ]);
        }

        return view('dashboard.index', compact(
            'totalPlayers',
            'totalCoaches',
            'income',
            'expense',
            'balance',
            'upcomingSchedules',
            'announcements',
            'tactics',
            'topScorers',
            'topAssists',
            'monthlyFinance',
            'topFilter'
        ));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('settings.profile', compact('user'));
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

        return back()->with('success', 'Profile Anda berhasil diperbarui!');
    }

    public function closeAccount(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManagement()) {
            return back()->with('error', 'Hanya akun Manager yang dapat menutup akun melalui menu ini.');
        }

        if ($user->team_id) {
            // Lock all users associated with the team (including the manager themselves)
            \App\Models\User::where('team_id', $user->team_id)->update(['is_locked' => true]);
        } else {
            $user->is_locked = true;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Akun Anda beserta seluruh anggota tim telah berhasil ditutup.');
    }

    public function editTeam()
    {
        $team = Auth::user()->team;
        if (!$team) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki tim.');
        }
        return view('settings.team', compact('team'));
    }

    public function updateTeam(Request $request)
    {
        $team = Auth::user()->team;
        if (!$team) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki tim.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string',
        ]);

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Pengaturan Tim berhasil diperbarui!');
    }
}
