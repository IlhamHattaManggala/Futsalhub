<?php

namespace App\Http\Controllers;

use App\Models\PremiumPayment;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayCallbackController extends Controller
{
    protected TripayService $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    public function handleCallback(Request $request)
    {
        $signature = $request->header('X-Callback-Signature');
        $jsonPayload = $request->getContent();

        if (empty($signature)) {
            return response()->json([
                'success' => false,
                'message' => 'Signature header missing'
            ], 400);
        }

        // Validasi signature
        if (!$this->tripayService->verifyCallback($jsonPayload, $signature)) {
            Log::warning('TriPay Webhook: Invalid Signature', [
                'signature' => $signature,
                'payload' => $jsonPayload
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 403);
        }

        $data = json_decode($jsonPayload, true);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON payload'
            ], 400);
        }

        $merchantRef = $data['merchant_ref'] ?? null;
        $reference = $data['reference'] ?? null;
        $status = strtoupper($data['status'] ?? '');

        Log::info('TriPay Webhook Received', [
            'merchant_ref' => $merchantRef,
            'reference' => $reference,
            'status' => $status
        ]);

        // Cari transaksi di database
        $payment = PremiumPayment::where('merchant_ref', $merchantRef)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment reference not found'
            ], 404);
        }

        // Jika transaksi sudah lunas sebelumnya di database kita, abaikan agar tidak dobel proses
        if ($payment->payment_status === 'paid' || $payment->status === 'approved') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already processed'
            ]);
        }

        // Perbarui status detail dari TriPay
        $payment->payment_status = strtolower($status);
        $payment->reference = $reference;

        if ($status === 'PAID') {
            $payment->status = 'approved';
            $payment->admin_notes = 'Pembayaran sukses diverifikasi otomatis oleh sistem TriPay.';
            $payment->save();

            // Aktifkan paket premium untuk tim
            $team = $payment->team;
            if ($team) {
                $currentExpiry = $team->premium_until;
                if ($currentExpiry && now()->lt($currentExpiry)) {
                    $team->premium_until = $currentExpiry->addDays(30);
                } else {
                    $team->premium_until = now()->addDays(30);
                }
                $team->plan = 'premium';
                $team->save();

                Log::info('Premium Plan Activated for Team', [
                    'team_id' => $team->id,
                    'team_name' => $team->name,
                    'premium_until' => $team->premium_until->toDateTimeString()
                ]);
            }
        } elseif ($status === 'EXPIRED' || $status === 'FAILED') {
            $payment->status = 'rejected';
            $payment->admin_notes = $status === 'EXPIRED' 
                ? 'Pembayaran kedaluwarsa. Silakan ajukan transaksi kembali.' 
                : 'Pembayaran gagal. Silakan hubungi administrasi atau coba lagi.';
            $payment->save();

            Log::info('Payment Failed or Expired', [
                'merchant_ref' => $merchantRef,
                'status' => $status
            ]);
        } else {
            // Unpaid atau status transisi lainnya
            $payment->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook processed successfully'
        ]);
    }
}
