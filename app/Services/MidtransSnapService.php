<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransSnapService
{
    /**
     * Request a Midtrans Snap Token via Midtrans API.
     */
    public static function createSnapToken(array $params): array
    {
        $serverKey = config('midtrans.server_key');
        $apiUrl = config('midtrans.api_url');
        $clientKey = config('midtrans.client_key');
        $snapUrl = config('midtrans.snap_url');

        if (empty($serverKey)) {
            return [
                'success' => false,
                'error' => 'MIDTRANS_SERVER_KEY is not configured in .env',
                'status_code' => 500,
                'client_key' => $clientKey,
                'snap_url' => $snapUrl,
            ];
        }

        try {
            $authHeader = 'Basic ' . base64_encode($serverKey . ':');

            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(10)->post($apiUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'token' => $data['token'] ?? null,
                    'redirect_url' => $data['redirect_url'] ?? null,
                    'client_key' => $clientKey,
                    'snap_url' => $snapUrl,
                ];
            }

            $errorBody = $response->json() ?? [];
            $errorMessage = $errorBody['error_messages'][0] ?? ($errorBody['error'] ?? 'Gagal membuat sesi pembayaran Midtrans (' . $response->status() . ')');

            Log::warning('Midtrans Snap API Warning', [
                'status' => $response->status(),
                'body' => $response->body(),
                'order_id' => $params['transaction_details']['order_id'] ?? null,
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
                'status_code' => $response->status(),
                'client_key' => $clientKey,
                'snap_url' => $snapUrl,
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
                'client_key' => $clientKey,
                'snap_url' => $snapUrl,
            ];
        }
    }
}
