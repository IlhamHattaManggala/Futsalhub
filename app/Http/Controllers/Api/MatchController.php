<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Player;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamId = $user->team_id;
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

        return response()->json([
            'success' => true,
            'message' => 'Daftar pertandingan berhasil diambil.',
            'data' => [
                'matches' => $matches,
                'summary' => [
                    'played' => $played,
                    'wins' => $wins,
                    'draws' => $draws,
                    'losses' => $losses,
                    'goals_for' => $goalsFor,
                    'goals_against' => $goalsAgainst,
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Pelatih atau Manajer yang dapat merekam/menjadwalkan pertandingan.'
            ], 403);
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

        $match = MatchGame::create([
            'team_id' => $user->team_id,
            'opponent' => $request->opponent,
            'date' => $request->date,
            'location' => $request->location,
            'status' => $request->status,
            'score_team' => $request->score_team,
            'score_opponent' => $request->score_opponent,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil disimpan!',
            'data' => [
                'match' => $match
            ]
        ], 201);
    }

    public function stats($id)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Pelatih (Coach) yang dapat menginput statistik pemain.'
            ], 403);
        }

        $team = $user->team;
        if ($team && $team->isFree()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur Statistik & Performa Pemain hanya tersedia untuk paket Premium. Silakan hubungi Manajemen untuk upgrade.'
            ], 403);
        }

        $match = MatchGame::where('team_id', $user->team_id)->findOrFail($id);

        if ($match->status !== 'Selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Statistik hanya dapat diinput untuk pertandingan yang sudah Selesai.'
            ], 400);
        }

        $players = Player::where('team_id', $user->team_id)->orderBy('number', 'asc')->get();
        $statistics = Statistic::where('match_id', $match->id)->get()->keyBy('player_id');

        return response()->json([
            'success' => true,
            'message' => 'Statistik pertandingan berhasil diambil.',
            'data' => [
                'match' => $match,
                'players' => $players->map(function ($p) use ($statistics) {
                    $stat = $statistics[$p->id] ?? null;
                    return [
                        'player_id' => $p->id,
                        'name' => $p->name,
                        'number' => $p->number,
                        'position' => $p->position,
                        'stats' => $stat ? [
                            'goals' => $stat->goals,
                            'assists' => $stat->assists,
                            'yellow_cards' => $stat->yellow_cards,
                            'red_cards' => $stat->red_cards,
                            'minutes_played' => $stat->minutes_played,
                        ] : null
                    ];
                })
            ]
        ]);
    }

    public function saveStats(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Pelatih (Coach) yang dapat menginput statistik pemain.'
            ], 403);
        }

        $team = $user->team;
        if ($team && $team->isFree()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur Statistik & Performa Pemain hanya tersedia untuk paket Premium. Silakan hubungi Manajemen untuk upgrade.'
            ], 403);
        }

        $match = MatchGame::where('team_id', $user->team_id)->findOrFail($id);

        $request->validate([
            'stats' => 'required|array',
            'stats.*.player_id' => 'required|exists:players,id',
            'stats.*.goals' => 'required|integer|min:0',
            'stats.*.assists' => 'required|integer|min:0',
            'stats.*.yellow_cards' => 'required|integer|min:0|max:2',
            'stats.*.red_cards' => 'required|integer|min:0|max:1',
            'stats.*.minutes_played' => 'required|integer|min:0|max:100',
        ]);

        foreach ($request->stats as $data) {
            Statistic::updateOrCreate(
                [
                    'match_id' => $match->id,
                    'player_id' => $data['player_id'],
                ],
                [
                    'goals' => $data['goals'],
                    'assists' => $data['assists'],
                    'yellow_cards' => $data['yellow_cards'],
                    'red_cards' => $data['red_cards'],
                    'minutes_played' => $data['minutes_played'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Statistik pertandingan berhasil disimpan!'
        ]);
    }
}
