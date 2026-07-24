<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\User;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PlayerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer yang dapat mengakses daftar pemain.'
            ], 403);
        }

        $players = Player::where('team_id', $user->team_id)
            ->onlyPlayers()
            ->orderBy('number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pemain berhasil diambil.',
            'data' => [
                'players' => $players
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer yang dapat menambahkan pemain.'
            ], 403);
        }

        $team = $user->team;
        if ($team && !$team->canAddPlayer()) {
            return response()->json([
                'success' => false,
                'message' => 'Tim Anda telah mencapai batas maksimal 7 pemain untuk paket Free. Silakan upgrade ke Premium.'
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1|max:99',
            'position' => 'required|string|in:Anchor,Flank,Pivot,Goalkeeper',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:150',
            'create_account' => 'nullable|boolean',
            'email' => 'required_if:create_account,1|nullable|email|unique:users,email',
            'password' => 'required_if:create_account,1|nullable|string|min:6',
        ]);

        // Check if jersey number already exists in this team
        $exists = Player::where('team_id', $team->id)->where('number', $request->number)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor punggung ' . $request->number . ' sudah terdaftar di tim ini.'
            ], 400);
        }

        $userId = null;
        if ($request->create_account) {
            $playerRole = Role::where('name', 'player')->first();
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $playerRole->id,
                'team_id' => $team->id,
            ]);
            $userId = $newUser->id;
        }

        $player = Player::create([
            'team_id' => $team->id,
            'user_id' => $userId,
            'name' => $request->name,
            'number' => $request->number,
            'position' => $request->position,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemain berhasil ditambahkan!',
            'data' => [
                'player' => $player
            ]
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $player = Player::where('team_id', $user->team_id)->findOrFail($id);

        // Attendance Calculations
        $attendances = Attendance::where('player_id', $player->id)->get();
        $totalAgendas = Schedule::where('team_id', $user->team_id)->count();
        
        $presentCount = $attendances->where('status', 'Hadir')->count();
        $excusedCount = $attendances->where('status', 'Izin')->count();
        $absentCount = $attendances->where('status', 'Alpa')->count();
        $injuredCount = $attendances->where('status', 'Cedera')->count();
        $notRecordedCount = max(0, $totalAgendas - $attendances->count());

        $attendanceRate = $totalAgendas > 0 ? round(($presentCount / $totalAgendas) * 100) : 100;

        // Match Statistics Calculations
        $stats = Statistic::where('player_id', $player->id)->get();
        $matchesPlayed = $stats->where('minutes_played', '>', 0)->count();
        
        $totalGoals = $stats->sum('goals');
        $totalAssists = $stats->sum('assists');
        $totalYellow = $stats->sum('yellow_cards');
        $totalRed = $stats->sum('red_cards');
        $totalMinutes = $stats->sum('minutes_played');
        $avgMinutes = $matchesPlayed > 0 ? round($totalMinutes / $matchesPlayed, 1) : 0;

        // Dues Payment Status
        $totalDuesAgendas = Schedule::where('team_id', $user->team_id)->where('dues_amount', '>', 0)->count();
        $paidDuesCount = $attendances->where('is_dues_paid', 1)->count();
        $unpaidDuesCount = max(0, $totalDuesAgendas - $paidDuesCount);

        // Radar Chart capability indices (scale: 30 to 100)
        $scoringMetric = min(100, max(30, ($totalGoals * 25))); 
        $playmakingMetric = min(100, max(30, ($totalAssists * 25)));
        $playtimeMetric = $matchesPlayed > 0 ? min(100, max(30, round(($totalMinutes / ($matchesPlayed * 40)) * 100))) : 30;
        $disciplineMetric = max(30, 100 - ($totalYellow * 15) - ($totalRed * 40));
        $attendanceMetric = max(30, $attendanceRate);

        $radarStats = [
            'scoring' => $scoringMetric,
            'playmaking' => $playmakingMetric,
            'playtime' => $playtimeMetric,
            'discipline' => $disciplineMetric,
            'attendance' => $attendanceMetric
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail profil dan statistik pemain berhasil diambil.',
            'data' => [
                'player' => $player,
                'metrics' => [
                    'total_agendas' => $totalAgendas,
                    'present_count' => $presentCount,
                    'excused_count' => $excusedCount,
                    'absent_count' => $absentCount,
                    'injured_count' => $injuredCount,
                    'not_recorded_count' => $notRecordedCount,
                    'attendance_rate' => $attendanceRate,
                    'matches_played' => $matchesPlayed,
                    'total_goals' => $totalGoals,
                    'total_assists' => $totalAssists,
                    'total_yellow' => $totalYellow,
                    'total_red' => $totalRed,
                    'total_minutes' => $totalMinutes,
                    'avg_minutes' => $avgMinutes,
                    'total_dues_agendas' => $totalDuesAgendas,
                    'paid_dues_count' => $paidDuesCount,
                    'unpaid_dues_count' => $unpaidDuesCount,
                ],
                'radar_stats' => $radarStats
            ]
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer yang dapat menghapus pemain.'
            ], 403);
        }

        $player = Player::where('team_id', $user->team_id)->findOrFail($id);

        // Delete linked user if any
        if ($player->user) {
            $player->user->delete();
        }

        $player->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemain berhasil dihapus.'
        ]);
    }
}
