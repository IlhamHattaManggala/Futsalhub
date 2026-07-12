<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class FinanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isCoach() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer atau Pelatih yang dapat mengakses rekap keuangan.'
            ], 403);
        }

        $teamId = $user->team_id;
        $finances = Finance::where('team_id', $teamId)
            ->orderBy('date', 'desc')
            ->get();

        $income = Finance::where('team_id', $teamId)->where('type', 'Pemasukan')->sum('amount');
        $expense = Finance::where('team_id', $teamId)->where('type', 'Pengeluaran')->sum('amount');
        $balance = $income - $expense;

        return response()->json([
            'success' => true,
            'message' => 'Rekap keuangan berhasil diambil.',
            'data' => [
                'finances' => $finances,
                'summary' => [
                    'income' => (float)$income,
                    'expense' => (float)$expense,
                    'balance' => (float)$balance,
                    'qris_image' => $user->team && $user->team->qris_image ? asset($user->team->qris_image) : null
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer (Management) yang dapat mencatat transaksi keuangan.'
            ], 403);
        }

        $team = $user->team;
        if ($team && !$team->canAddFinance()) {
            return response()->json([
                'success' => false,
                'message' => 'Tim Anda telah mencapai batas maksimal 10 transaksi keuangan untuk paket Free. Silakan upgrade ke Premium.'
            ], 400);
        }

        if ($request->has('amount') && $request->amount !== null) {
            $cleanAmount = preg_replace('/[^0-9]/', '', $request->amount);
            $request->merge(['amount' => $cleanAmount !== '' ? $cleanAmount : null]);
        }

        $request->validate([
            'type' => 'required|string|in:Pemasukan,Pengeluaran',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $finance = Finance::create([
            'team_id' => $user->team_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi keuangan berhasil dicatat!',
            'data' => [
                'finance' => $finance
            ]
        ], 201);
    }

    public function updateQris(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManagement() && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Manajer (Management) yang dapat mengunggah QRIS Tim.'
            ], 403);
        }

        $request->validate([
            'qris_image' => 'required|file|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ], [
            'qris_image.required' => 'Silakan pilih gambar QRIS terlebih dahulu.',
            'qris_image.file' => 'Berkas harus berupa gambar.',
            'qris_image.mimes' => 'Format gambar harus JPEG, PNG, JPG, WEBP, atau SVG.',
            'qris_image.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        $team = $user->team;

        if ($request->hasFile('qris_image')) {
            // Hapus gambar lama jika ada
            if ($team->qris_image) {
                $googleOldPath = str_replace('images/', '', $team->qris_image);
                if (Storage::disk('google')->exists($googleOldPath)) {
                    Storage::disk('google')->delete($googleOldPath);
                }

                $oldPath = public_path($team->qris_image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Simpan gambar baru ke Google Drive disk under QRIS folder
            $file = $request->file('qris_image');
            $filename = 'qris_' . $team->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            Storage::disk('google')->putFileAs('QRIS', $file, $filename);

            $team->update([
                'qris_image' => 'images/QRIS/' . $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QRIS Tim berhasil diunggah!',
                'data' => [
                    'qris_image' => asset($team->qris_image)
                ]
            ]);
        }


        return response()->json([
            'success' => false,
            'message' => 'Gagal mengunggah file.'
        ], 400);
    }
}
