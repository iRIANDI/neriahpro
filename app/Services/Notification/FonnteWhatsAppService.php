<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\CmsGlobalSetting;
use App\Models\LeadContact;
use App\Models\PosImpactGrantApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteWhatsAppService
{
    public const DEFAULT_ADMIN_WHATSAPP = '081380448253';
    public const FONNTE_SEND_ENDPOINT = 'https://api.fonnte.com/send';

    /**
     * Send an arbitrary WhatsApp message via Fonnte API.
     *
     * @return array{success: bool, error?: string, data?: mixed}
     */
    public static function sendMessage(string $target, string $message, ?string $token = null): array
    {
        $resolvedToken = trim((string) ($token ?: (CmsGlobalSetting::getVal('fonnte_api_token') ?: config('services.fonnte.token', ''))));

        $cleanTarget = preg_replace('/[^\d]/', '', $target) ?? '';
        if (str_starts_with($cleanTarget, '08')) {
            $cleanTarget = '628'.substr($cleanTarget, 2);
        } elseif (str_starts_with($cleanTarget, '8')) {
            $cleanTarget = '628'.substr($cleanTarget, 1);
        }

        if (empty($cleanTarget)) {
            Log::warning('[FonnteWhatsAppService] Cannot dispatch WhatsApp message: Target phone number is empty.');

            return [
                'success' => false,
                'error' => 'EMPTY_TARGET_PHONE',
            ];
        }

        if (empty($resolvedToken)) {
            Log::info("[FonnteWhatsAppService] WhatsApp notification skipped: Fonnte API Token is not configured. Target: {$cleanTarget}");

            return [
                'success' => false,
                'error' => 'TOKEN_NOT_CONFIGURED',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $resolvedToken,
                ])
                ->post(self::FONNTE_SEND_ENDPOINT, [
                    'target' => $cleanTarget,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if ($response->successful()) {
                $body = $response->json();
                Log::info("[FonnteWhatsAppService] Message successfully dispatched to {$cleanTarget}.", [
                    'response' => $body,
                ]);

                return [
                    'success' => true,
                    'data' => $body,
                ];
            }

            Log::warning("[FonnteWhatsAppService] Fonnte API returned non-200 for {$cleanTarget}: ".$response->body());

            return [
                'success' => false,
                'error' => 'HTTP_ERROR_'.$response->status(),
                'data' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('[FonnteWhatsAppService] Failed to dispatch WhatsApp message: '.$e->getMessage(), [
                'target' => $cleanTarget,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch WhatsApp notification to Admin when a new Impact & Non-Profit Grant application is submitted.
     *
     * @param  array<string, mixed>|null  $docMeta
     * @return array{success: bool, error?: string, data?: mixed}
     */
    public static function notifyNewGrantApplication(PosImpactGrantApplication $app, ?array $docMeta = null): array
    {
        $isEnabled = (bool) CmsGlobalSetting::getVal('enable_admin_whatsapp_notifications', true);
        if (! $isEnabled) {
            return ['success' => false, 'error' => 'NOTIFICATIONS_DISABLED'];
        }

        $adminPhone = (string) (CmsGlobalSetting::getVal('admin_notification_whatsapp')
            ?: config('services.fonnte.admin_whatsapp', self::DEFAULT_ADMIN_WHATSAPP));

        $adminBaseUrl = rtrim(config('app.url', 'http://localhost'), '/');
        $adminPath = config('app.admin_path', 'admin');
        $pulseUrl = "{$adminBaseUrl}/{$adminPath}/saas-system-health-pulse";

        $docName = $docMeta['original_name'] ?? basename((string) ($app->private_document_path ?? 'Dokumen-Legalitas.pdf'));
        $docBytes = (int) ($docMeta['size_bytes'] ?? 0);
        $docSizeKb = $docBytes > 0 ? round($docBytes / 1024, 1).' KB' : 'Tersimpan di Vault';
        $docHash = $docMeta['sha256'] ?? null;
        $docHashShort = $docHash ? substr((string) $docHash, 0, 16).'...' : 'Verified SHA-256';

        $notes = is_array($app->mission_statement)
            ? ($app->mission_statement['id'] ?? $app->mission_statement['en'] ?? '-')
            : (string) ($app->mission_statement ?: '-');

        $message = implode("\n", [
            '🏛️ *PENGAJUAN HIBAH CSR / IMPACT GRANT (RP 0)*',
            '----------------------------------------',
            'Ada pendaftar baru mengajukan subsidi lisensi CSR 100% dan mengunggah berkas legalitas resmi:',
            '',
            "🏢 *Lembaga:* {$app->organization_name}",
            "🏷️ *Kategori:* {$app->organization_type}",
            "👤 *Pemohon:* {$app->applicant_name}",
            "📧 *Email Resmi:* {$app->official_email}",
            "📱 *WhatsApp:* {$app->phone_country_code}{$app->phone_number}",
            "📜 *No. SK / Akta:* {$app->legal_decree_number}",
            "📂 *Dokumen:* {$docName} ({$docSizeKb})",
            "🔒 *Vault Hash:* {$docHashShort}",
            "📝 *Kebutuhan:* {$notes}",
            '⏳ *Status:* *PENDING REVIEW (Verifikasi ISAK 35)*',
            '----------------------------------------',
            '🔍 *Tinjau & 1-Click Approve di Admin:*',
            $pulseUrl,
        ]);

        return self::sendMessage($adminPhone, $message);
    }

    /**
     * Dispatch WhatsApp notification to Admin when a new Lead Contact or Paid Order is placed.
     *
     * @return array{success: bool, error?: string, data?: mixed}
     */
    public static function notifyNewLead(LeadContact $lead, string $displayPackage, bool $isRetail, ?string $orderId = null, int $grossAmount = 0): array
    {
        $isEnabled = (bool) CmsGlobalSetting::getVal('enable_admin_whatsapp_notifications', true);
        if (! $isEnabled) {
            return ['success' => false, 'error' => 'NOTIFICATIONS_DISABLED'];
        }

        $adminPhone = (string) (CmsGlobalSetting::getVal('admin_notification_whatsapp')
            ?: config('services.fonnte.admin_whatsapp', self::DEFAULT_ADMIN_WHATSAPP));

        $adminBaseUrl = rtrim(config('app.url', 'http://localhost'), '/');
        $adminPath = config('app.admin_path', 'admin');
        $leadsUrl = "{$adminBaseUrl}/{$adminPath}/lead-contacts";

        $cleanPhone = $lead->phone_country_code ? "{$lead->phone_country_code}{$lead->phone}" : ($lead->phone ?? '-');
        $company = $lead->company_name ?: '-';
        $formattedPrice = $grossAmount > 0 ? 'Rp '.number_format($grossAmount, 0, ',', '.') : 'Konsultasi / Free';

        $title = $isRetail ? '💳 *PESANAN LISENSI SAAS DIGITAL MASUK!*' : '🎯 *PROSPEK PROYEK / MITRA STUDIO MASUK!*';

        $lines = [
            $title,
            '----------------------------------------',
            "📦 *Paket:* {$displayPackage} (".($isRetail ? 'Retail Self-Service' : 'Mitra Turnkey Studio').')',
            "👤 *Nama Klien:* {$lead->name}",
            "🏢 *Perusahaan/Usaha:* {$company}",
            "📧 *Email:* {$lead->email}",
            "📱 *WhatsApp:* {$cleanPhone}",
        ];

        if ($orderId) {
            $lines[] = "🏷️ *Order ID:* {$orderId}";
            $lines[] = "💰 *Total Transaksi:* {$formattedPrice}";
        }

        if (! empty($lead->notes)) {
            $lines[] = "📝 *Catatan:* {$lead->notes}";
        }

        $lines[] = '----------------------------------------';
        $lines[] = '🔍 *Buka Detail Lead di Admin:*';
        $lines[] = $leadsUrl;

        return self::sendMessage($adminPhone, implode("\n", $lines));
    }
}
