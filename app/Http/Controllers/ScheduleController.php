<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Player;
use App\Models\Attendance;
use App\Models\MatchGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamId = $user->team_id;
        $schedules = Schedule::where('team_id', $teamId)
            ->orderBy('start_time', 'desc')
            ->get();

        // Ambil data absensi milik pemain yang sedang login
        $myAttendances = collect();
        if ($user->isPlayer() && $user->player) {
            $myAttendances = \App\Models\Attendance::where('player_id', $user->player->id)
                ->whereIn('schedule_id', $schedules->pluck('id'))
                ->get()
                ->keyBy('schedule_id');
        }

        return view('schedules.index', compact('schedules', 'myAttendances'));
    }

    public function store(Request $request)
    {
        // Only Coach or Management can create schedules
        if (!Auth::user()->isCoach() && !Auth::user()->isManagement()) {
            return back()->with('error', 'Hanya Pelatih atau Manajer yang dapat membuat jadwal.');
        }

        if ($request->has('dues_amount') && $request->dues_amount !== null) {
            $cleanDues = preg_replace('/[^0-9]/', '', $request->dues_amount);
            $request->merge(['dues_amount' => $cleanDues !== '' ? $cleanDues : null]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:Latihan,Pertandingan',
            'start_time' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'opponent' => 'required_if:type,Pertandingan|nullable|string|max:255',
            'dues_amount' => 'nullable|numeric|min:0',
        ]);

        $schedule = Schedule::create([
            'team_id' => Auth::user()->team_id,
            'title' => $request->title,
            'type' => $request->type,
            'start_time' => $request->start_time,
            'location' => $request->location,
            'description' => $request->description,
            'opponent' => $request->opponent,
            'dues_amount' => $request->dues_amount ?? 0.00,
        ]);

        if ($schedule->type === 'Pertandingan') {
            MatchGame::create([
                'team_id' => Auth::user()->team_id,
                'schedule_id' => $schedule->id,
                'opponent' => $schedule->opponent,
                'date' => \Carbon\Carbon::parse($schedule->start_time)->format('Y-m-d'),
                'location' => $schedule->location,
                'status' => 'Terjadwal',
            ]);
        }

        try {
            // Send notifications to all players in the team
            $playerUsers = \App\Models\User::where('team_id', Auth::user()->team_id)
                ->whereHas('role', function ($query) {
                    $query->where('name', 'player');
                })
                ->get();

            foreach ($playerUsers as $playerUser) {
                $playerUser->notify(new \App\Notifications\NewScheduleNotification($schedule));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi jadwal: ' . $e->getMessage());
        }

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        // Only Coach or Management can delete schedules
        if (!Auth::user()->isCoach() && !Auth::user()->isManagement()) {
            return back()->with('error', 'Hanya Pelatih atau Manajer yang dapat menghapus jadwal.');
        }

        $teamId = Auth::user()->team_id;
        $schedule = Schedule::where('team_id', $teamId)->findOrFail($id);

        // Clean up any uploaded payment receipts for this schedule to avoid junk files
        $attendancesWithReceipts = Attendance::where('schedule_id', $schedule->id)
            ->whereNotNull('payment_receipt')
            ->get();

        foreach ($attendancesWithReceipts as $att) {
            $googlePath = str_replace('images/', '', $att->payment_receipt);
            if (Storage::disk('google')->exists($googlePath)) {
                Storage::disk('google')->delete($googlePath);
            }

            $filePath = public_path($att->payment_receipt);
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }


        // Delete associated attendances
        Attendance::where('schedule_id', $schedule->id)->delete();

        // Delete the schedule
        $schedule->delete();

        return back()->with('success', 'Jadwal agenda berhasil dihapus!');
    }

    public function edit($id)
    {
        // Only Coach or Management can edit schedules
        if (!Auth::user()->isCoach() && !Auth::user()->isManagement()) {
            return redirect()->route('schedules.index')->with('error', 'Hanya Pelatih atau Manajer yang dapat mengedit jadwal.');
        }

        $teamId = Auth::user()->team_id;
        $schedule = Schedule::where('team_id', $teamId)->findOrFail($id);

        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        // Only Coach or Management can edit schedules
        if (!Auth::user()->isCoach() && !Auth::user()->isManagement()) {
            return redirect()->route('schedules.index')->with('error', 'Hanya Pelatih atau Manajer yang dapat mengedit jadwal.');
        }

        $teamId = Auth::user()->team_id;
        $schedule = Schedule::where('team_id', $teamId)->findOrFail($id);

        if ($request->has('dues_amount') && $request->dues_amount !== null) {
            $cleanDues = preg_replace('/[^0-9]/', '', $request->dues_amount);
            $request->merge(['dues_amount' => $cleanDues !== '' ? $cleanDues : null]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:Latihan,Pertandingan',
            'start_time' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'opponent' => 'required_if:type,Pertandingan|nullable|string|max:255',
            'dues_amount' => 'nullable|numeric|min:0',
        ]);

        $schedule->update([
            'title' => $request->title,
            'type' => $request->type,
            'start_time' => $request->start_time,
            'location' => $request->location,
            'description' => $request->description,
            'opponent' => $request->type === 'Pertandingan' ? $request->opponent : null,
            'dues_amount' => $request->dues_amount ?? 0.00,
        ]);

        if ($schedule->type === 'Pertandingan') {
            $match = MatchGame::where('schedule_id', $schedule->id)->first();
            $matchDate = \Carbon\Carbon::parse($schedule->start_time)->format('Y-m-d');
            if ($match) {
                $match->update([
                    'opponent' => $schedule->opponent,
                    'date' => $matchDate,
                    'location' => $schedule->location,
                ]);
            } else {
                MatchGame::create([
                    'team_id' => $teamId,
                    'schedule_id' => $schedule->id,
                    'opponent' => $schedule->opponent,
                    'date' => $matchDate,
                    'location' => $schedule->location,
                    'status' => 'Terjadwal',
                ]);
            }
        } else {
            MatchGame::where('schedule_id', $schedule->id)->delete();
        }

        return redirect()->route('schedules.index')->with('success', 'Jadwal agenda berhasil diperbarui!');
    }

    public function attendance($id)
    {
        // Only Coach can record/edit attendance
        if (!Auth::user()->isCoach()) {
            return redirect()->route('schedules.index')->with('error', 'Hanya Pelatih (Coach) yang dapat mencatat absensi.');
        }

        $teamId = Auth::user()->team_id;
        $schedule = Schedule::where('team_id', $teamId)->findOrFail($id);
        $players = Player::where('team_id', $teamId)->onlyPlayers()->orderBy('number', 'asc')->get();

        // Get existing attendances
        $attendances = Attendance::where('schedule_id', $schedule->id)->get()->keyBy('player_id');

        return view('schedules.attendance', compact('schedule', 'players', 'attendances'));
    }

    public function saveAttendance(Request $request, $id)
    {
        if (!Auth::user()->isCoach()) {
            return redirect()->route('schedules.index')->with('error', 'Hanya Pelatih (Coach) yang dapat mencatat absensi.');
        }

        $teamId = Auth::user()->team_id;
        $schedule = Schedule::where('team_id', $teamId)->findOrFail($id);

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|string|in:Hadir,Izin,Alpa,Cedera',
            'attendance.*.notes' => 'nullable|string|max:255',
            'attendance.*.is_dues_paid' => 'nullable|boolean',
        ]);

        foreach ($request->attendance as $playerId => $data) {
            Attendance::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'player_id' => $playerId,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? '',
                    'is_dues_paid' => isset($data['is_dues_paid']) && $data['is_dues_paid'] == 1,
                ]
            );
        }

        return redirect()->route('schedules.index')->with('success', 'Absensi berhasil disimpan!');
    }

    /**
     * Unggah bukti transfer iuran pemain (hanya Pemain)
     */
    public function uploadReceipt(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPlayer()) {
            return response()->json(['success' => false, 'message' => 'Hanya pemain yang dapat mengunggah bukti pembayaran.'], 403);
        }

        $request->validate([
            'receipt' => 'required|file|mimes:jpeg,png,jpg,webp,svg,avif|max:2048',
        ], [
            'receipt.required' => 'Pilih berkas bukti transfer terlebih dahulu.',
            'receipt.file' => 'Berkas harus berupa gambar.',
            'receipt.mimes' => 'Format harus JPEG, PNG, JPG, WEBP, SVG, atau AVIF.',
            'receipt.max' => 'Ukuran maksimal berkas adalah 2MB.',
        ]);

        $schedule = Schedule::where('team_id', $user->team_id)->findOrFail($id);
        $player = $user->player;

        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Profil pemain Anda tidak ditemukan.'], 404);
        }

        if ($request->hasFile('receipt')) {
            // Dapatkan atau buat entri absensi baru
            $attendance = Attendance::firstOrNew([
                'schedule_id' => $schedule->id,
                'player_id' => $player->id,
            ]);

            // Jika entri absensi baru dibuat, set default kehadiran ke "Hadir"
            if (!$attendance->exists) {
                $attendance->status = 'Hadir';
            }

            // Hapus bukti transfer lama jika ada
            if ($attendance->payment_receipt) {
                $googleOldPath = str_replace('images/', '', $attendance->payment_receipt);
                if (Storage::disk('google')->exists($googleOldPath)) {
                    Storage::disk('google')->delete($googleOldPath);
                }

                $oldPath = public_path($attendance->payment_receipt);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Simpan gambar bukti transfer baru ke Google Drive disk under receipts folder
            $file = $request->file('receipt');
            $filename = 'receipt_' . $schedule->id . '_' . $player->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            Storage::disk('google')->putFileAs('Receipts', $file, $filename);

            $attendance->payment_receipt = 'images/Receipts/' . $filename;

            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi dari pelatih.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal memproses pengunggahan berkas.'], 400);
    }

    /**
     * Catat absensi mandiri pemain via pemindaian QR Code (Signed URL)
     */
    public function scanAttendance(Request $request, $id)
    {
        // 1. Verifikasi tanda tangan rute bertanda tangan Laravel
        if (!$request->hasValidSignature()) {
            abort(403, 'Tautan absensi tidak sah atau telah kedaluwarsa.');
        }

        $user = Auth::user();
        $teamId = $user->team_id;

        // 2. Cari jadwal agenda, pastikan milik tim pengguna yang sedang login
        $schedule = Schedule::where('team_id', $teamId)->findOrFail($id);

        // 3. Pastikan pengakses adalah Pemain (Player)
        if (!$user->isPlayer() || !$user->player) {
            return redirect()->route('schedules.index')
                ->with('error', 'Hanya pemain (Player) yang dapat mencatatkan absensi secara mandiri.');
        }

        // 4. Validasi rentang waktu absensi (maksimal 12 jam sebelum/sesudah start_time agenda)
        $hoursDiff = now()->diffInHours($schedule->start_time, false);
        if (abs($hoursDiff) > 12) {
            return redirect()->route('schedules.index')
                ->with('error', 'Absensi QR Code hanya dapat dilakukan dalam rentang 12 jam sebelum atau sesudah waktu mulai agenda.');
        }

        // 5. Catat atau perbarui absensi
        $attendance = Attendance::firstOrNew([
            'schedule_id' => $schedule->id,
            'player_id' => $user->player->id,
        ]);

        $attendance->status = 'Hadir';
        if (empty($attendance->notes)) {
            $attendance->notes = 'Hadir via QR Code';
        }
        $attendance->save();

        return redirect()->route('schedules.index')
            ->with('success', 'Absensi berhasil! Anda telah dicatat HADIR pada agenda: ' . $schedule->title);
    }
}
