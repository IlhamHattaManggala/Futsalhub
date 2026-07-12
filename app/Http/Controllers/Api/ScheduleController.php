<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Player;
use App\Models\Attendance;
use App\Models\MatchGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $myAttendances = collect();
        if ($user->isPlayer() && $user->player) {
            $myAttendances = Attendance::where('player_id', $user->player->id)
                ->whereIn('schedule_id', $schedules->pluck('id'))
                ->get()
                ->keyBy('schedule_id');
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar jadwal berhasil diambil.',
            'data' => [
                'schedules' => $schedules->map(function ($s) use ($myAttendances) {
                    $att = $myAttendances[$s->id] ?? null;
                    return [
                        'id' => $s->id,
                        'title' => $s->title,
                        'type' => $s->type,
                        'start_time' => $s->start_time,
                        'location' => $s->location,
                        'description' => $s->description,
                        'opponent' => $s->opponent,
                        'dues_amount' => $s->dues_amount,
                        'my_attendance' => $att ? [
                            'status' => $att->status,
                            'is_dues_paid' => $att->is_dues_paid,
                            'payment_receipt' => $att->payment_receipt ? asset($att->payment_receipt) : null,
                            'notes' => $att->notes
                        ] : null
                    ];
                })
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Hanya Pelatih atau Manajer yang dapat membuat jadwal.'], 403);
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

        DB::beginTransaction();
        try {
            $schedule = Schedule::create([
                'team_id' => $user->team_id,
                'title' => $request->title,
                'type' => $request->type,
                'start_time' => $request->start_time,
                'location' => $request->location,
                'description' => $request->description,
                'opponent' => $request->opponent,
                'dues_amount' => $request->dues_amount,
            ]);

            if ($schedule->type === 'Pertandingan') {
                MatchGame::create([
                    'team_id' => $user->team_id,
                    'schedule_id' => $schedule->id,
                    'opponent' => $schedule->opponent,
                    'date' => $schedule->start_time->format('Y-m-d H:i:s'),
                    'location' => $schedule->location,
                    'status' => 'Terjadwal',
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jadwal agenda berhasil dibuat.',
                'data' => ['schedule' => $schedule]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membuat jadwal.'], 500);
        }
    }

    public function attendance($id)
    {
        $user = Auth::user();
        if (!$user->isCoach() && !$user->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Hanya Pelatih atau Manajer yang dapat melihat absensi.'], 403);
        }

        $schedule = Schedule::where('team_id', $user->team_id)->findOrFail($id);
        $players = Player::where('team_id', $user->team_id)->orderBy('number', 'asc')->get();
        $attendances = Attendance::where('schedule_id', $schedule->id)->get()->keyBy('player_id');

        return response()->json([
            'success' => true,
            'message' => 'Daftar absensi pemain berhasil diambil.',
            'data' => [
                'schedule' => $schedule,
                'players' => $players->map(function ($p) use ($attendances) {
                    $att = $attendances[$p->id] ?? null;
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'number' => $p->number,
                        'position' => $p->position,
                        'attendance' => $att ? [
                            'status' => $att->status,
                            'is_dues_paid' => $att->is_dues_paid,
                            'payment_receipt' => $att->payment_receipt ? asset($att->payment_receipt) : null,
                            'notes' => $att->notes
                        ] : null
                    ];
                })
            ]
        ]);
    }

    public function saveAttendance(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isCoach()) {
            return response()->json(['success' => false, 'message' => 'Hanya Pelatih (Coach) yang dapat mencatat absensi.'], 403);
        }

        $schedule = Schedule::where('team_id', $user->team_id)->findOrFail($id);

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.player_id' => 'required|exists:players,id',
            'attendance.*.status' => 'required|string|in:Hadir,Izin,Alpa,Cedera',
            'attendance.*.notes' => 'nullable|string|max:255',
            'attendance.*.is_dues_paid' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $data) {
                Attendance::updateOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'player_id' => $data['player_id'],
                    ],
                    [
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? '',
                        'is_dues_paid' => isset($data['is_dues_paid']) && $data['is_dues_paid'] == 1,
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Absensi berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan absensi.'], 500);
        }
    }

    public function scanAttendance(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['success' => false, 'message' => 'Tautan absensi tidak sah atau telah kedaluwarsa.'], 403);
        }

        $user = Auth::user();
        $schedule = Schedule::where('team_id', $user->team_id)->findOrFail($id);

        if (!$user->isPlayer() || !$user->player) {
            return response()->json(['success' => false, 'message' => 'Hanya pemain (Player) yang dapat melakukan absensi.'], 403);
        }

        $hoursDiff = now()->diffInHours($schedule->start_time, false);
        if (abs($hoursDiff) > 12) {
            return response()->json(['success' => false, 'message' => 'Absensi QR Code hanya dapat dilakukan dalam rentang 12 jam sebelum/sesudah agenda.'], 400);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'schedule_id' => $schedule->id,
                'player_id' => $user->player->id,
            ],
            [
                'status' => 'Hadir',
                'notes' => 'Hadir via QR Code API',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil! Anda telah dicatat HADIR pada agenda: ' . $schedule->title
        ]);
    }

    public function uploadReceipt(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPlayer() || !$user->player) {
            return response()->json(['success' => false, 'message' => 'Hanya pemain yang dapat mengunggah bukti pembayaran.'], 403);
        }

        $request->validate([
            'receipt' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $schedule = Schedule::where('team_id', $user->team_id)->findOrFail($id);
        $player = $user->player;

        if ($request->hasFile('receipt')) {
            $attendance = Attendance::firstOrNew([
                'schedule_id' => $schedule->id,
                'player_id' => $player->id,
            ]);

            if (!$attendance->exists) {
                $attendance->status = 'Hadir';
            }

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

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah berkas.'], 400);
    }
}
