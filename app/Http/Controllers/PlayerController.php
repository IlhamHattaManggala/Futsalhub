<?php

namespace App\Http\Controllers;

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
        $teamId = Auth::user()->team_id;
        $players = Player::where('team_id', $teamId)
            ->onlyPlayers()
            ->orderBy('number', 'asc')
            ->get();

        return view('players.index', compact('players'));
    }

    public function store(Request $request)
    {
        $team = Auth::user()->team;
        if ($team && !$team->canAddPlayer()) {
            return back()->withInput()->with('error', 'Tim Anda telah mencapai batas maksimal 7 pemain untuk paket Free. Silakan upgrade ke Premium.');
        }

        $teamId = $team->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1|max:99',
            'position' => 'required|string|in:Anchor,Flank,Pivot,Goalkeeper',
            'phone' => 'nullable|regex:/^[0-9]+$/|max:20',
            'birth_date' => 'nullable|date',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:150',
            'create_account' => 'nullable|boolean',
            'email' => 'required_if:create_account,1|nullable|email|unique:users,email',
            'password' => 'required_if:create_account,1|nullable|string|min:6',
        ], [
            'number.required' => 'Nomor punggung wajib diisi.',
            'number.integer' => 'Nomor punggung harus berupa angka.',
            'number.min' => 'Nomor punggung minimal adalah 1.',
            'number.max' => 'Nomor punggung maksimal adalah 99.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'height.integer' => 'Tinggi badan harus berupa angka.',
            'height.min' => 'Tinggi badan minimal 100 cm.',
            'height.max' => 'Tinggi badan maksimal 250 cm.',
            'weight.integer' => 'Berat badan harus berupa angka.',
            'weight.min' => 'Berat badan minimal 30 kg.',
            'weight.max' => 'Berat badan maksimal 150 kg.',
            'email.required_if' => 'Email wajib diisi jika membuat akun pemain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required_if' => 'Kata sandi wajib diisi jika membuat akun pemain.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        // Check if jersey number already exists in this team
        $exists = Player::where('team_id', $teamId)->where('number', $request->number)->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Nomor punggung ' . $request->number . ' sudah terdaftar di tim ini.');
        }

        $userId = null;
        if ($request->create_account) {
            $playerRole = Role::where('name', 'player')->first();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $playerRole->id,
                'team_id' => $teamId,
            ]);
            $userId = $user->id;
        }

        Player::create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'name' => $request->name,
            'number' => $request->number,
            'position' => $request->position,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
        ]);

        return back()->with('success', 'Pemain berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $teamId = Auth::user()->team_id;
        $player = Player::where('team_id', $teamId)->findOrFail($id);

        // Delete linked user if any
        if ($player->user) {
            $player->user->delete();
        }

        $player->delete();

        return back()->with('success', 'Pemain berhasil dihapus.');
    }

    public function show($id)
    {
        $teamId = Auth::user()->team_id;
        $player = Player::where('team_id', $teamId)->findOrFail($id);

        // Attendance Calculations
        $attendances = Attendance::where('player_id', $player->id)->get();
        $totalAgendas = Schedule::where('team_id', $teamId)->count();
        
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
        $totalDuesAgendas = Schedule::where('team_id', $teamId)->where('dues_amount', '>', 0)->count();
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

        return view('players.show', compact(
            'player',
            'totalAgendas',
            'presentCount',
            'excusedCount',
            'absentCount',
            'injuredCount',
            'notRecordedCount',
            'attendanceRate',
            'matchesPlayed',
            'totalGoals',
            'totalAssists',
            'totalYellow',
            'totalRed',
            'totalMinutes',
            'avgMinutes',
            'totalDuesAgendas',
            'paidDuesCount',
            'unpaidDuesCount',
            'radarStats'
        ));
    }
}
