<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class FinanceController extends Controller
{
    public function index()
    {
        $teamId = Auth::user()->team_id;
        $finances = Finance::where('team_id', $teamId)
            ->orderBy('date', 'desc')
            ->get();

        $income = Finance::where('team_id', $teamId)->where('type', 'Pemasukan')->sum('amount');
        $expense = Finance::where('team_id', $teamId)->where('type', 'Pengeluaran')->sum('amount');
        $balance = $income - $expense;

        return view('finances.index', compact('finances', 'income', 'expense', 'balance'));
    }

    public function store(Request $request)
    {
        // Only Management can record finances
        if (!Auth::user()->isManagement()) {
            return back()->with('error', 'Hanya Manajer (Management) yang dapat mencatat transaksi keuangan.');
        }

        $team = Auth::user()->team;
        if ($team && !$team->canAddFinance()) {
            return back()->with('error', 'Tim Anda telah mencapai batas maksimal 10 transaksi keuangan untuk paket Free. Silakan upgrade ke Premium.');
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

        Finance::create([
            'team_id' => Auth::user()->team_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Transaksi keuangan berhasil dicatat!');
    }

    /**
     * Unggah / Update QRIS pembayaran tim (hanya Manajer)
     */
    public function updateQris(Request $request)
    {
        if (!Auth::user()->isManagement()) {
            return back()->with('error', 'Hanya Manajer (Management) yang dapat mengunggah QRIS Tim.');
        }

        $request->validate([
            'qris_image' => 'required|file|mimes:jpeg,png,jpg,webp,svg,avif|max:2048',
        ], [
            'qris_image.required' => 'Silakan pilih gambar QRIS terlebih dahulu.',
            'qris_image.file' => 'Berkas harus berupa gambar.',
            'qris_image.mimes' => 'Format gambar harus JPEG, PNG, JPG, WEBP, SVG, atau AVIF.',
            'qris_image.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        $team = Auth::user()->team;

        if ($request->hasFile('qris_image')) {
            $team = Auth::user()->team;

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


            return back()->with('success', 'QRIS Tim berhasil diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    public function export()
    {
        $team = Auth::user()->team;
        $teamId = $team->id;
        $finances = Finance::where('team_id', $teamId)
            ->orderBy('date', 'asc')
            ->get();

        $income = Finance::where('team_id', $teamId)->where('type', 'Pemasukan')->sum('amount');
        $expense = Finance::where('team_id', $teamId)->where('type', 'Pengeluaran')->sum('amount');
        $balance = $income - $expense;

        return view('finances.export', compact('finances', 'income', 'expense', 'balance', 'team'));
    }
}
