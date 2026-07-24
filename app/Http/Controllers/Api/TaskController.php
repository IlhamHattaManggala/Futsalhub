<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamId = $user->team_id;

        if ($user->isPlayer()) {
            $player = $user->player;
            if (!$player) {
                return response()->json(['success' => false, 'message' => 'Profil pemain Anda tidak ditemukan.'], 404);
            }

            $tasks = $player->tasks()
                ->with('category', 'coach')
                ->orderBy('player_tasks.status', 'asc')
                ->orderBy('due_date', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar tugas pemain berhasil diambil.',
                'data' => [
                    'tasks' => $tasks->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'title' => $t->title,
                            'category' => $t->category->name,
                            'description' => $t->description,
                            'due_date' => $t->due_date,
                            'coach_name' => $t->coach->name,
                            'status' => $t->pivot->status,
                            'proof_image' => $t->pivot->proof_image ? asset($t->pivot->proof_image) : null,
                            'completed_at' => $t->pivot->completed_at
                        ];
                    })
                ]
            ]);
        } else {
            $tasks = Task::where('team_id', $teamId)
                ->with('category', 'coach')
                ->withCount(['players as total_players'])
                ->withCount(['players as completed_players' => function ($query) {
                    $query->where('player_tasks.status', 'Selesai');
                }])
                ->orderBy('due_date', 'desc')
                ->get();

            $categories = TaskCategory::all();

            $dataTasks = [];
            foreach ($tasks as $task) {
                $assignments = DB::table('player_tasks')
                    ->join('players', 'player_tasks.player_id', '=', 'players.id')
                    ->where('player_tasks.task_id', $task->id)
                    ->select('players.name', 'player_tasks.status', 'player_tasks.proof_image', 'player_tasks.completed_at')
                    ->orderBy('player_tasks.status', 'desc')
                    ->get();

                $dataTasks[] = [
                    'id' => $task->id,
                    'title' => $task->title,
                    'category' => $task->category->name,
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'coach_name' => $task->coach->name,
                    'total_players' => $task->total_players,
                    'completed_players' => $task->completed_players,
                    'assignments' => $assignments->map(function ($a) {
                        return [
                            'player_name' => $a->name,
                            'status' => $a->status,
                            'proof_image' => $a->proof_image ? asset($a->proof_image) : null,
                            'completed_at' => $a->completed_at
                        ];
                    })
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar rekap tugas tim berhasil diambil.',
                'data' => [
                    'tasks' => $dataTasks,
                    'categories' => $categories
                ]
            ]);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Hanya Pelatih atau Manajer yang dapat membuat tugas.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'task_category_id' => 'required|exists:task_categories,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'assign_type' => 'required|in:all,specific',
            'players' => 'required_if:assign_type,specific|array',
            'players.*' => 'exists:players,id',
        ]);

        DB::beginTransaction();
        try {
            $task = Task::create([
                'team_id' => $user->team_id,
                'coach_id' => $user->id,
                'task_category_id' => $request->task_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
            ]);

            if ($request->assign_type === 'all') {
                $playerIds = Player::where('team_id', $user->team_id)->onlyPlayers()->pluck('id')->toArray();
            } else {
                $playerIds = $request->players;
            }

            foreach ($playerIds as $playerId) {
                DB::table('player_tasks')->insert([
                    'task_id' => $task->id,
                    'player_id' => $playerId,
                    'status' => 'Belum Selesai',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dibuat.',
                'data' => ['task' => $task]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membuat tugas.'], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPlayer() || !$user->player) {
            return response()->json(['success' => false, 'message' => 'Hanya pemain yang dapat menyelesaikan tugas.'], 403);
        }

        $request->validate([
            'proof_image' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $player = $user->player;
        $pivot = DB::table('player_tasks')
            ->where('task_id', $id)
            ->where('player_id', $player->id)
            ->first();

        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditugaskan kepada Anda.'], 404);
        }

        if ($pivot->status === 'Selesai') {
            return response()->json(['success' => false, 'message' => 'Tugas ini sudah diselesaikan sebelumnya.'], 400);
        }

        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $filename = 'proof_' . $id . '_' . $player->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Save inside Google Drive disk under Task Proof folder
            Storage::disk('google')->putFileAs('Task Proof', $file, $filename);
            $path = 'images/Task Proof/' . $filename;

            DB::table('player_tasks')
                ->where('task_id', $id)
                ->where('player_id', $player->id)
                ->update([
                    'status' => 'Selesai',
                    'proof_image' => $path,
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Tugas berhasil diselesaikan! Foto bukti telah dikirim.']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah foto bukti.'], 400);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Hanya Pelatih atau Manajer yang dapat menghapus tugas.'], 403);
        }

        $task = Task::where('team_id', $user->team_id)->findOrFail($id);

        // Delete all associated proof images from Google Drive and the server filesystem
        $proofs = DB::table('player_tasks')
            ->where('task_id', $task->id)
            ->whereNotNull('proof_image')
            ->pluck('proof_image');

        foreach ($proofs as $proof) {
            $googlePath = str_replace('images/', '', $proof);
            if (Storage::disk('google')->exists($googlePath)) {
                Storage::disk('google')->delete($googlePath);
            }

            $fullPath = public_path($proof);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $task->delete();

        return response()->json(['success' => true, 'message' => 'Tugas berhasil dihapus!']);
    }
}

