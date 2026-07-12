<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    protected string $apiKey;
    protected string $privateKey;
    protected string $merchantCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = \App\Models\Setting::get('tripay_api_key') ?: (config('services.tripay.api_key') ?? env('TRIPAY_API_KEY'));
        $this->privateKey = \App\Models\Setting::get('tripay_private_key') ?: (config('services.tripay.private_key') ?? env('TRIPAY_PRIVATE_KEY'));
        $this->merchantCode = \App\Models\Setting::get('tripay_merchant_code') ?: (config('services.tripay.merchant_code') ?? env('TRIPAY_MERCHANT_CODE'));
        
        $mode = \App\Models\Setting::get('tripay_mode') ?: (config('services.tripay.mode') ?? env('TRIPAY_MODE', 'sandbox'));
        $this->baseUrl = $mode === 'production' 
            ? 'https://tripay.co.id/api/' 
            : 'https://tripay.co.id/api-sandbox/';
    }

    /**
     * Dapatkan daftar channel pembayaran aktif
     */
    public function getPaymentChannels(): array
    {
        try {
            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]);

            if (\App\Models\Setting::get('tripay_mode') === 'sandbox' || config('services.tripay.mode') === 'sandbox' || env('TRIPAY_MODE') === 'sandbox') {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($this->baseUrl . 'merchant/payment-channel');

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error('TriPay API Error (Payment Channels): ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('TriPay Exception (Payment Channels): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Buat transaksi closed payment baru ke TriPay
     */
    public function createTransaction(array $params): ?array
    {
        $merchantRef = $params['merchant_ref'];
        $amount = (int) $params['amount'];

        // Generate Signature
        // Formula: merchant_code + merchant_ref + amount
        $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);

        $payload = [
            'method' => $params['payment_method'],
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $params['customer_name'],
            'customer_email' => $params['customer_email'],
            'customer_phone' => $params['customer_phone'] ?? '081234567890',
            'order_items' => [
                [
                    'sku' => $params['sku'] ?? 'PREMIUM-SUB',
                    'name' => $params['item_name'] ?? 'Premium Plan Subscription 30 Days',
                    'price' => $amount,
                    'quantity' => 1,
                ]
            ],
            'callback_url' => route('tripay.callback'),
            'return_url' => route('subscription.payment', ['merchantRef' => $merchantRef]),
            'expired_time' => time() + (24 * 3600), // 24 jam kedaluwarsa
            'signature' => $signature,
        ];

        try {
            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]);

            if (\App\Models\Setting::get('tripay_mode') === 'sandbox' || config('services.tripay.mode') === 'sandbox' || env('TRIPAY_MODE') === 'sandbox') {
                $request = $request->withoutVerifying();
            }

            $response = $request->asForm()->post($this->baseUrl . 'transaction/create', $payload);

            if ($response->successful()) {
                return $response->json()['data'] ?? null;
            }

            Log::error('TriPay API Error (Create Transaction): ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('TriPay Exception (Create Transaction): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validasi signature dari callback webhook TriPay
     */
    public function verifyCallback(string $jsonPayload, string $headerSignature): bool
    {
        $calculatedSignature = hash_hmac('sha256', $jsonPayload, $this->privateKey);
        
        return hash_equals($headerSignature, $calculatedSignature);
    }
}
