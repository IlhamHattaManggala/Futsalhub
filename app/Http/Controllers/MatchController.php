<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Player;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    public function index()
    {
        $teamId = Auth::user()->team_id;
        $matches = MatchGame::where('team_id', $teamId)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate team record
        $played = 0;
        $wins = 0;
        $draws = 0;
        $losses = 0;
        $goalsFor = 0;
        $goalsAgainst = 0;

        foreach ($matches as $m) {
            if ($m->status === 'Selesai') {
                $played++;
                $goalsFor += $m->score_team;
                $goalsAgainst += $m->score_opponent;
                if ($m->score_team > $m->score_opponent) {
                    $wins++;
                } elseif ($m->score_team === $m->score_opponent) {
                    $draws++;
                } else {
                    $losses++;
                }
            }
        }

        return view('matches.index', compact('matches', 'played', 'wins', 'draws', 'losses', 'goalsFor', 'goalsAgainst'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isCoach() && !Auth::user()->isManagement()) {
            return back()->with('error', 'Hanya Pelatih atau Manajer yang dapat merekam pertandingan.');
        }

        $request->validate([
            'opponent' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|string|in:Terjadwal,Selesai',
            'score_team' => 'required_if:status,Selesai|nullable|integer|min:0',
            'score_opponent' => 'required_if:status,Selesai|nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        MatchGame::create([
            'team_id' => Auth::user()->team_id,
            'opponent' => $request->opponent,
            'date' => $request->date,
            'location' => $request->location,
            'status' => $request->status,
            'score_team' => $request->score_team,
            'score_opponent' => $request->score_opponent,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Pertandingan berhasil disimpan!');
    }

    public function stats($id)
    {
        if (!Auth::user()->isCoach()) {
            return redirect()->route('matches.index')->with('error', 'Hanya Pelatih (Coach) yang dapat menginput statistik pemain.');
        }

        $team = Auth::user()->team;
        if ($team && $team->isFree()) {
            return redirect()->route('matches.index')->with('error', 'Fitur Statistik & Performa Pemain hanya tersedia untuk paket Premium. Silakan hubungi Manajemen untuk upgrade.');
        }

        $teamId = Auth::user()->team_id;
        $match = MatchGame::where('team_id', $teamId)->findOrFail($id);

        if ($match->status !== 'Selesai') {
            return redirect()->route('matches.index')->with('error', 'Statistik hanya dapat diinput untuk pertandingan yang sudah Selesai.');
        }

        $players = Player::where('team_id', $teamId)->onlyPlayers()->orderBy('number', 'asc')->get();
        
        // Get existing stats
        $statistics = Statistic::where('match_id', $match->id)->get()->keyBy('player_id');

        return view('matches.stats', compact('match', 'players', 'statistics'));
    }

    public function saveStats(Request $request, $id)
    {
        if (!Auth::user()->isCoach()) {
            return redirect()->route('matches.index')->with('error', 'Hanya Pelatih (Coach) yang dapat menginput statistik pemain.');
        }

        $team = Auth::user()->team;
        if ($team && $team->isFree()) {
            return redirect()->route('matches.index')->with('error', 'Fitur Statistik & Performa Pemain hanya tersedia untuk paket Premium. Silakan hubungi Manajemen untuk upgrade.');
        }

        $teamId = Auth::user()->team_id;
        $match = MatchGame::where('team_id', $teamId)->findOrFail($id);

        $request->validate([
            'possession_team' => 'nullable|integer|min:0|max:100',
            'possession_opponent' => 'nullable|integer|min:0|max:100',
            'shoot_on_target_team' => 'nullable|integer|min:0',
            'shoot_on_target_opponent' => 'nullable|integer|min:0',
            'shoot_off_target_team' => 'nullable|integer|min:0',
            'shoot_off_target_opponent' => 'nullable|integer|min:0',
            'stats' => 'required|array',
            'stats.*.goals' => 'required|integer|min:0',
            'stats.*.assists' => 'required|integer|min:0',
            'stats.*.yellow_cards' => 'required|integer|min:0|max:2',
            'stats.*.red_cards' => 'required|integer|min:0|max:1',
            'stats.*.minutes_played' => 'required|integer|min:0|max:100',
            'stats.*.clearance' => 'nullable|integer|min:0',
            'stats.*.save' => 'nullable|integer|min:0',
            'stats.*.shoot_on_target' => 'nullable|integer|min:0',
            'stats.*.shoot_off_target' => 'nullable|integer|min:0',
        ]);

        // Update team stats
        $match->update([
            'possession_team' => $request->possession_team,
            'possession_opponent' => $request->possession_opponent,
            'shoot_on_target_team' => $request->shoot_on_target_team,
            'shoot_on_target_opponent' => $request->shoot_on_target_opponent,
            'shoot_off_target_team' => $request->shoot_off_target_team,
            'shoot_off_target_opponent' => $request->shoot_off_target_opponent,
        ]);

        foreach ($request->stats as $playerId => $data) {
            Statistic::updateOrCreate(
                [
                    'match_id' => $match->id,
                    'player_id' => $playerId,
                ],
                [
                    'goals' => $data['goals'],
                    'assists' => $data['assists'],
                    'yellow_cards' => $data['yellow_cards'],
                    'red_cards' => $data['red_cards'],
                    'minutes_played' => $data['minutes_played'],
                    'clearance' => $data['clearance'] ?? 0,
                    'save' => $data['save'] ?? 0,
                    'shoot_on_target' => $data['shoot_on_target'] ?? 0,
                    'shoot_off_target' => $data['shoot_off_target'] ?? 0,
                ]
            );
        }

        return redirect()->route('matches.index')->with('success', 'Statistik pertandingan berhasil disimpan!');
    }
}
