<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamId = $user->team_id;

        if ($user->isPlayer()) {
            // Player view: show tasks assigned to this player
            $player = $user->player;
            if (!$player) {
                return redirect()->route('dashboard', ['slug' => $user->slug])
                    ->with('error', 'Profil pemain Anda tidak ditemukan.');
            }

            // Get tasks assigned to this player
            $tasks = $player->tasks()
                ->with('category', 'coach')
                ->orderBy('player_tasks.status', 'asc') // Pending first, then Completed
                ->orderBy('due_date', 'asc')
                ->get();

            return view('tasks.index', compact('tasks'));
        } else {
            // Coach / Management view: show all tasks created for this team
            $tasks = Task::where('team_id', $teamId)
                ->with('category', 'coach')
                ->withCount(['players as total_players'])
                ->withCount(['players as completed_players' => function ($query) {
                    $query->where('player_tasks.status', 'Selesai');
                }])
                ->orderBy('due_date', 'desc')
                ->get();

            // Load details for each task (all assigned players & status)
            foreach ($tasks as $task) {
                $task->assignments = DB::table('player_tasks')
                    ->join('players', 'player_tasks.player_id', '=', 'players.id')
                    ->leftJoin('users', 'players.user_id', '=', 'users.id')
                    ->where('player_tasks.task_id', $task->id)
                    ->select('players.name', 'player_tasks.status', 'player_tasks.proof_image', 'player_tasks.completed_at', 'player_tasks.started_at', 'player_tasks.start_proof_image')
                    ->orderBy('player_tasks.status', 'desc') // Selesai first, then Belum Selesai
                    ->get();
            }

            // List of all active players in this team for assigning tasks
            $players = Player::where('team_id', $teamId)->orderBy('name', 'asc')->get();
            $categories = TaskCategory::all();

            return view('tasks.index', compact('tasks', 'players', 'categories'));
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only Coach can create tasks
        if (!$user->isCoach()) {
            return back()->with('error', 'Hanya Pelatih yang dapat membuat tugas.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'task_category_id' => 'required|exists:task_categories,id',
            'due_date' => 'required|date|after_or_equal:now',
            'description' => 'nullable|string',
            'assign_type' => 'required|in:all,specific',
            'players' => 'required_if:assign_type,specific|array',
            'players.*' => 'exists:players,id',
        ], [
            'due_date.after_or_equal' => 'Tenggat waktu (deadline) tidak boleh kurang dari waktu sekarang.',
            'players.required_if' => 'Pilih setidaknya satu pemain jika menugaskan secara spesifik.',
        ]);

        DB::beginTransaction();
        try {
            // Create task
            $task = Task::create([
                'team_id' => $user->team_id,
                'coach_id' => $user->id,
                'task_category_id' => $request->task_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
            ]);

            // Assign players
            if ($request->assign_type === 'all') {
                $playerIds = Player::where('team_id', $user->team_id)->pluck('id')->toArray();
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

            try {
                // Send notifications to assigned users
                $assignedUsers = \App\Models\User::whereIn('id', function ($query) use ($playerIds) {
                    $query->select('user_id')
                        ->from('players')
                        ->whereIn('id', $playerIds)
                        ->whereNotNull('user_id');
                })->get();

                foreach ($assignedUsers as $assignedUser) {
                    $assignedUser->notify(new \App\Notifications\NewTaskNotification($task));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi tugas: ' . $e->getMessage());
            }

            return back()->with('success', 'Tugas berhasil dibuat dan ditugaskan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat tugas. Silakan coba lagi.');
        }
    }
    public function start(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPlayer() || !$user->player) {
            return response()->json(['success' => false, 'message' => 'Hanya pemain yang dapat memulai tugas.'], 403);
        }

        $request->validate([
            'start_proof_image' => 'required|string',
        ], [
            'start_proof_image.required' => 'Wajib mengambil foto bukti mulai latihan.',
        ]);

        $player = $user->player;
        $pivot = DB::table('player_tasks')
            ->where('task_id', $id)
            ->where('player_id', $player->id)
            ->first();

        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditugaskan kepada Anda.'], 404);
        }

        if ($pivot->status !== 'Belum Selesai') {
            return response()->json(['success' => false, 'message' => 'Tugas ini sudah dimulai atau diselesaikan.'], 400);
        }

        try {
            $path = $this->saveBase64Image($request->start_proof_image, 'Task Proof', 'start_proof', $id, $player->id);

            DB::table('player_tasks')
                ->where('task_id', $id)
                ->where('player_id', $player->id)
                ->update([
                    'status' => 'Mulai',
                    'start_proof_image' => $path,
                    'started_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Latihan dimulai! Waktu Anda sudah berjalan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memulai tugas: ' . $e->getMessage()], 400);
        }
    }

    public function complete(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPlayer() || !$user->player) {
            return response()->json(['success' => false, 'message' => 'Hanya pemain yang dapat menyelesaikan tugas.'], 403);
        }

        $request->validate([
            'proof_image' => 'required|string',
        ], [
            'proof_image.required' => 'Wajib mengambil foto bukti selesai latihan.',
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

        if ($pivot->status !== 'Mulai' || empty($pivot->started_at)) {
            return response()->json(['success' => false, 'message' => 'Anda harus memulai tugas terlebih dahulu sebelum menyelesaikannya.'], 400);
        }

        // Ambil data tugas untuk memeriksa kata kunci pada judul secara case-insensitive
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditemukan.'], 404);
        }

        $titleLower = strtolower($task->title);
        $minDuration = 30; // default minimal 30 menit

        if (str_contains($titleLower, 'makan')) {
            $minDuration = 5;
        } elseif (str_contains($titleLower, 'tidur')) {
            $minDuration = 60;
        } elseif (
            str_contains($titleLower, 'lari') || 
            str_contains($titleLower, 'joging') || 
            str_contains($titleLower, 'jogging') || 
            str_contains($titleLower, 'sprint') || 
            str_contains($titleLower, 'run')
        ) {
            $minDuration = 10;
        }

        // Validate duration (delta)
        $startedAt = \Carbon\Carbon::parse($pivot->started_at);
        $duration = round(abs(now()->diffInMinutes($startedAt)));

        if ($duration < $minDuration) {
            $requiredText = $minDuration >= 60 ? ($minDuration / 60) . ' jam' : $minDuration . ' menit';
            return response()->json([
                'success' => false, 
                'message' => 'Durasi latihan terlalu singkat (baru berjalan ' . $duration . ' menit). Harap lakukan latihan minimal selama ' . $requiredText . '!'
            ], 422);
        }

        try {
            $path = $this->saveBase64Image($request->proof_image, 'Task Proof', 'proof', $id, $player->id);

            DB::table('player_tasks')
                ->where('task_id', $id)
                ->where('player_id', $player->id)
                ->update([
                    'status' => 'Selesai',
                    'proof_image' => $path,
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Latihan selesai! Bukti penyelesaian berhasil dikirim.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyelesaikan tugas: ' . $e->getMessage()], 400);
        }
    }

    private function saveBase64Image($base64String, $directory, $prefix, $id, $playerId)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $ext = strtolower($type[1]);
            if (!in_array($ext, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                throw new \Exception('Format gambar tidak valid.');
            }
        } else {
            throw new \Exception('Data gambar tidak valid.');
        }

        $imageData = base64_decode($base64String);
        if ($imageData === false) {
            throw new \Exception('Gagal mendekode berkas gambar.');
        }

        if (strlen($imageData) > 3 * 1024 * 1024) {
            throw new \Exception('Ukuran gambar terlalu besar (Maksimal 3MB).');
        }

        $filename = $prefix . '_' . $id . '_' . $playerId . '_' . time() . '.' . $ext;

        Storage::disk('google')->put($directory . '/' . $filename, $imageData);
        return 'images/' . $directory . '/' . $filename;
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isCoach()) {
            return back()->with('error', 'Hanya Pelatih yang dapat menghapus tugas.');
        }

        $task = Task::where('team_id', $user->team_id)->findOrFail($id);

        // Delete all associated proof images from Google Drive and the server filesystem
        $startProofs = DB::table('player_tasks')
            ->where('task_id', $task->id)
            ->whereNotNull('start_proof_image')
            ->pluck('start_proof_image');

        $proofs = DB::table('player_tasks')
            ->where('task_id', $task->id)
            ->whereNotNull('proof_image')
            ->pluck('proof_image');

        $allProofs = $startProofs->concat($proofs);

        foreach ($allProofs as $proof) {
            $googlePath = str_replace('images/', '', $proof);
            if (Storage::disk('google')->exists($googlePath)) {
                Storage::disk('google')->delete($googlePath);
            }

            $fullPath = public_path($proof);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $task->delete(); // player_tasks is cascade deleted from DB level

        return back()->with('success', 'Tugas berhasil dihapus!');
    }
}
