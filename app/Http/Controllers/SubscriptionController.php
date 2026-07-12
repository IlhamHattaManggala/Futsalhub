<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\PremiumPayment;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected TripayService $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    /**
     * Tampilkan halaman upgrade premium untuk Tim
     */
    public function showUpgrade()
    {
        $user = Auth::user();
        $team = $user->team;

        if (!$team) {
            return redirect()->route('dashboard')->with('error', 'Anda harus tergabung dalam sebuah tim terlebih dahulu.');
        }

        if (!$user->isManagement()) {
            abort(403, 'Hanya Manajer yang diizinkan mengakses halaman peningkatan plan tim.');
        }

        // Ambil riwayat pembayaran premium untuk tim ini
        $payments = PremiumPayment::where('team_id', $team->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil daftar channel pembayaran aktif dari TriPay Sandbox
        $channels = $this->tripayService->getPaymentChannels();
        if (empty($channels)) {
            // Fallback mock channels jika offline / sandbox error
            $channels = [
                ['code' => 'QRIS2', 'name' => 'QRIS (Simulasi)', 'category' => 'Instant Payment', 'active' => true],
                ['code' => 'BCAVA', 'name' => 'BCA Virtual Account (Simulasi)', 'category' => 'Virtual Account', 'active' => true],
                ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account (Simulasi)', 'category' => 'Virtual Account', 'active' => true],
                ['code' => 'BRIVA', 'name' => 'BRI Virtual Account (Simulasi)', 'category' => 'Virtual Account', 'active' => true],
                ['code' => 'ALFAMART', 'name' => 'Alfamart (Simulasi)', 'category' => 'Convenience Store', 'active' => true],
            ];
        }

        $price = (float) \App\Models\Setting::get('platform_fee', '100000');
        if ($price <= 0) {
            $price = 100000;
        }

        return view('subscription.upgrade', compact('team', 'payments', 'price', 'channels'));
    }

    /**
     * Proses pengajuan upgrade premium (buat transaksi TriPay)
     */
    public function submitUpgrade(Request $request)
    {
        $user = Auth::user();
        $team = $user->team;

        if (!$team) {
            return back()->with('error', 'Tim tidak ditemukan.');
        }

        // Hanya Management yang boleh mengajukan upgrade premium
        if (!$user->isManagement()) {
            return back()->with('error', 'Hanya Manajer yang diizinkan mengajukan peningkatan plan tim.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ], [
            'payment_method.required' => 'Silakan pilih metode pembayaran.',
        ]);

        $amount = (float) \App\Models\Setting::get('platform_fee', '100000');
        if ($amount <= 0) {
            $amount = 100000.00;
        }
        $merchantRef = 'TRX-' . time() . '-' . rand(1000, 9999);

        // Cari nomor HP user dari profile player jika ada, atau gunakan dummy
        $phone = $user->player ? $user->player->phone : '081234567890';

        // Hitung transaksi ke TriPay
        $tripayTrx = $this->tripayService->createTransaction([
            'payment_method' => $request->payment_method,
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $phone,
            'sku' => 'PREMIUM-30D',
            'item_name' => 'Paket Premium Tim Futsal - 30 Hari'
        ]);

        if (!$tripayTrx) {
            return back()->with('error', 'Gagal membuat transaksi ke TriPay Sandbox. Pastikan konfigurasi API Key benar.');
        }

        PremiumPayment::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'reference' => $tripayTrx['reference'] ?? null,
            'merchant_ref' => $merchantRef,
            'payment_method' => $request->payment_method,
            'payment_instructions' => $tripayTrx['instructions'] ?? null,
            'qr_url' => $tripayTrx['qr_url'] ?? null,
            'pay_code' => $tripayTrx['pay_code'] ?? null,
            'payment_url' => $tripayTrx['checkout_url'] ?? null,
        ]);

        return redirect()->route('subscription.payment', $merchantRef)->with('success', 'Transaksi berhasil dibuat! Silakan ikuti instruksi pembayaran di bawah.');
    }

    /**
     * Tampilkan instruksi detail pembayaran transaksi
     */
    public function showPayment($merchantRef)
    {
        $payment = PremiumPayment::where('merchant_ref', $merchantRef)
            ->with(['team', 'user'])
            ->firstOrFail();

        // Pastikan keamanan tenant (hanya anggota tim yang sama yang boleh melihat detail)
        if (!Auth::user()->isSuperAdmin() && $payment->team_id !== Auth::user()->team_id) {
            abort(403, 'Anda tidak diizinkan melihat detail transaksi ini.');
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'payment_status' => $payment->payment_status,
                'admin_notes' => $payment->admin_notes,
            ]);
        }

        return view('subscription.payment', compact('payment'));
    }


    /**
     * Tampilkan halaman kelola pembayaran premium untuk Superadmin
     */
    public function adminIndex(Request $request)
    {
        // 1. Hitung statistik transaksi global (secara keseluruhan)
        $unpaidCount = PremiumPayment::where('payment_status', 'unpaid')->count();
        $paidCount = PremiumPayment::where('payment_status', 'paid')->count();
        $totalCount = PremiumPayment::count();

        // 2. Query transaksi premium untuk tabel dengan relasi
        $query = PremiumPayment::with(['team', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter: Pencarian (Ref, TriPay ID, Nama Tim, Pembuat Transaksi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merchant_ref', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('team', function($teamQuery) use ($search) {
                      $teamQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: Status Pembayaran
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter: Metode Pembayaran
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Paginate hasil pencarian (10 per halaman) dan pertahankan query string
        $payments = $query->paginate(10)->withQueryString();

        // Ambil daftar metode pembayaran unik untuk dropdown filter
        $paymentMethods = PremiumPayment::whereNotNull('payment_method')
            ->where('payment_method', '<>', '')
            ->distinct()
            ->pluck('payment_method');

        return view('superadmin.payments', compact(
            'payments',
            'unpaidCount',
            'paidCount',
            'totalCount',
            'paymentMethods'
        ));
    }

    /**
     * Tampilkan detail transaksi untuk Superadmin via AJAX
     */
    public function showPaymentAdmin($id)
    {
        $payment = PremiumPayment::with(['team', 'user'])->findOrFail($id);
        
        return response()->json([
            'id' => $payment->id,
            'created_at' => $payment->created_at->translatedFormat('d M Y - H:i') . ' WIB',
            'updated_at' => $payment->updated_at->translatedFormat('d M Y - H:i') . ' WIB',
            'team_name' => $payment->team->name,
            'team_plan' => strtoupper($payment->team->plan),
            'user_name' => $payment->user->name,
            'user_email' => $payment->user->email,
            'amount' => 'Rp ' . number_format($payment->amount, 0, ',', '.'),
            'payment_status' => $payment->payment_status,
            'payment_method' => $payment->payment_method ?: 'N/A',
            'merchant_ref' => $payment->merchant_ref,
            'reference' => $payment->reference ?: '-',
            'pay_code' => $payment->pay_code ?: '-',
            'qr_url' => $payment->qr_url ?: null,
            'payment_url' => $payment->payment_url ?: null,
            'admin_notes' => $payment->admin_notes ?: 'Tidak ada catatan.',
        ]);
    }
}

