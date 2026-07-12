<?php

namespace App\Http\Controllers;

use App\Models\Tactic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TacticController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->team || !$user->team->isPremium()) {
            return redirect()->route('dashboard')->with('error', 'Fitur Tactical Board hanya tersedia untuk tim Premium. Silakan hubungi Manager tim untuk melakukan upgrade.');
        }

        // Load the single tactic record for the team
        $tactic = Tactic::where('team_id', $user->team_id)->first();

        return view('tactics.index', compact('tactic'));
    }

    public function save(Request $request)
    {
        $user = Auth::user();
        // Only Coach can save
        if (!$user->isCoach()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only Premium team can save
        if (!$user->team || !$user->team->isPremium()) {
            return response()->json(['error' => 'Fitur Tactical Board hanya tersedia untuk tim Premium.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formation' => 'required|string',
            'canvas_data' => 'required|json',
        ]);

        // Save or update the single tactic record for this team
        $tactic = Tactic::updateOrCreate(
            ['team_id' => $user->team_id],
            [
                'coach_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'formation' => $request->formation,
                'canvas_data' => json_decode($request->canvas_data, true),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Strategi berhasil disimpan!',
            'redirect' => route('tactics.index')
        ]);
    }
}
