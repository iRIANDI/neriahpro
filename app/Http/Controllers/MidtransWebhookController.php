<?php

namespace App\Http\Controllers;

use App\Models\PaymentWebhookLog;
use App\Models\VisionBlueprint;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming Midtrans payment gateway webhooks.
     * Records all payloads to the Dead-Letter Queue (DLQ) audit log table.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        // 1. Instantly record raw payload to DLQ audit log
        $webhookLog = PaymentWebhookLog::create([
            'gateway' => 'midtrans',
            'event_type' => $transactionStatus ?: 'unknown',
            'order_id' => $orderId,
            'status' => 'pending',
            'raw_payload' => $payload,
            'raw_headers' => $request->headers->all(),
            'ip_address' => $request->ip(),
        ]);

        try {
            // 2. Validate cryptographic signature if server key is present
            $serverKey = config('midtrans.server_key');
            if (!empty($serverKey) && !empty($signatureKey)) {
                $computedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
                if (!hash_equals($computedSignature, $signatureKey)) {
                    $webhookLog->markAsFailed('Cryptographic signature mismatch.');
                    Log::warning('Midtrans Webhook signature verification failed', [
                        'order_id' => $orderId,
                        'ip' => $request->ip(),
                    ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid signature key.',
                    ], 403);
                }
            }

            // 3. Process Blueprint or Document fulfillment
            $isPaid = in_array($transactionStatus, ['settlement', 'capture']) 
                && ($fraudStatus === 'accept' || empty($fraudStatus) || $fraudStatus === 'success');

            if ($isPaid && $orderId) {
                $this->fulfillOrder($orderId, $request);
            }

            // 4. Mark DLQ log as successfully processed
            $webhookLog->markAsProcessed();

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully.',
            ], 200);

        } catch (\Throwable $e) {
            $webhookLog->markAsFailed($e->getMessage() . "\n" . $e->getTraceAsString());
            Log::error('Midtrans Webhook processing failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal webhook processor error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fulfill blueprint project or contract based on order ID.
     */
    protected function fulfillOrder(string $orderId, Request $request): void
    {
        // Check for attached digital contract document
        $document = Document::where('midtrans_order_id', $orderId)->first();
        if ($document) {
            $document->update([
                'status' => 'signed',
                'signed_at' => now(),
                'signer_ip_address' => $request->ip(),
            ]);

            if ($document->related instanceof VisionBlueprint) {
                $blueprint = $document->related;
                $blueprint->update([
                    'project_status' => 'In Development (DP Paid)',
                ]);
                $blueprint->provisionStagingUrl();
                if (!$blueprint->signed_agreement) {
                    $blueprint->recordSignOff($request->ip(), $request->userAgent());
                }
                return;
            }
        }

        // Direct matching for NP-BP-{first8Id}-{time}
        if (str_starts_with($orderId, 'NP-BP-')) {
            $parts = explode('-', $orderId);
            if (isset($parts[2])) {
                $shortId = $parts[2];
                $blueprint = VisionBlueprint::where('id', 'LIKE', $shortId . '%')->first();
                if ($blueprint) {
                    $blueprint->update([
                        'project_status' => 'In Development (DP Paid)',
                    ]);
                    $blueprint->provisionStagingUrl();
                    if (!$blueprint->signed_agreement) {
                        $blueprint->recordSignOff($request->ip(), $request->userAgent());
                    }
                }
            }
        }
    }
}
