<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tactic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TacticController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isPlayer()) {
            return response()->json(['success' => false, 'message' => 'Pemain tidak diizinkan mengakses papan taktik.'], 403);
        }

        // Must be premium check
        $team = $user->team;
        if (!$team || $team->isFree()) {
            return response()->json(['success' => false, 'message' => 'Tactical board hanya tersedia untuk tim Premium.'], 403);
        }

        $tactic = Tactic::where('team_id', $team->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Data papan taktik berhasil diambil.',
            'data' => [
                'tactic' => $tactic
            ]
        ]);
    }

    public function save(Request $request)
    {
        $user = Auth::user();
        if (!$user->isCoach()) {
            return response()->json(['success' => false, 'message' => 'Hanya Pelatih (Coach) yang dapat menyimpan taktik.'], 403);
        }

        $team = $user->team;
        if (!$team || $team->isFree()) {
            return response()->json(['success' => false, 'message' => 'Tactical board hanya tersedia untuk tim Premium.'], 403);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'formation' => 'nullable|string|max:255',
            'canvas_data' => 'required|json',
        ]);

        $tactic = Tactic::updateOrCreate(
            ['team_id' => $team->id],
            [
                'coach_id' => $user->id,
                'title' => $request->title ?? 'Taktik Tim',
                'description' => $request->description,
                'formation' => $request->formation ?? 'Custom',
                'canvas_data' => json_decode($request->canvas_data, true),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Taktik berhasil disimpan.',
            'data' => [
                'tactic' => $tactic
            ]
        ]);
    }
}
