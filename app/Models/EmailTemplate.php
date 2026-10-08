<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmailTemplate extends Model
{
    use HasUlids;

    protected $table = 'email_templates';

    protected $fillable = [
        'code',
        'name',
        'subject',
        'sender_name',
        'sender_email',
        'reply_to_email',
        'body_html',
        'available_placeholders',
        'is_active',
    ];

    protected $casts = [
        'available_placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $template) {
            Cache::forget("email_template_attr_{$template->code}");
            Cache::forget('email_template_all_codes');
        });

        static::deleted(function (self $template) {
            Cache::forget("email_template_attr_{$template->code}");
            Cache::forget('email_template_all_codes');
        });
    }

    /**
     * Retrieve cached template attributes safely avoiding Incomplete Class serialization.
     */
    public static function getCachedByCode(string $code): ?self
    {
        $cacheKey = "email_template_attr_{$code}";

        $attributes = Cache::rememberForever($cacheKey, function () use ($code) {
            $record = static::where('code', $code)->where('is_active', true)->first();
            return $record ? $record->getAttributes() : null;
        });

        if (!$attributes || !is_array($attributes)) {
            return null;
        }

        return (new static)->newFromBuilder($attributes);
    }

    /**
     * Render subject, body HTML, sender details with variable substitution.
     */
    public static function renderTemplate(string $code, array $variables = []): array
    {
        // Global system default variables
        $defaultVariables = [
            '{{app_name}}' => config('app.name', 'Neriah Pro'),
            '{{app_url}}' => config('app.url', 'https://neriahpro.com'),
            '{{support_email}}' => 'support@neriahpro.com',
            '{{year}}' => date('Y'),
        ];

        // Format user variables
        $replacements = [];
        foreach (array_merge($defaultVariables, $variables) as $key => $val) {
            $placeholder = str_starts_with($key, '{{') && str_ends_with($key, '}}') ? $key : ('{{' . $key . '}}');
            $replacements[$placeholder] = (string) $val;
        }

        $template = static::getCachedByCode($code);

        if ($template) {
            $subject = str_replace(array_keys($replacements), array_values($replacements), $template->subject);
            $bodyHtml = str_replace(array_keys($replacements), array_values($replacements), $template->body_html);

            return [
                'found' => true,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'sender_name' => $template->sender_name ?: 'Neriah Pro Support',
                'sender_email' => $template->sender_email ?: 'support@neriahpro.com',
                'reply_to_email' => $template->reply_to_email ?: 'support@neriahpro.com',
            ];
        }

        // Clean hardcoded fallback if template not yet in DB
        return static::fallbackTemplate($code, $replacements);
    }

    /**
     * Resilient default template if database table or record is not yet seeded.
     */
    protected static function fallbackTemplate(string $code, array $replacements): array
    {
        if ($code === 'customer_otp') {
            $otp = $replacements['{{otp}}'] ?? '123456';
            $email = $replacements['{{email}}'] ?? '';
            $ip = $replacements['{{ip_address}}'] ?? '127.0.0.1';
            $expiry = $replacements['{{expiry_minutes}}'] ?? '10';
            $supportEmail = $replacements['{{support_email}}'] ?? 'support@neriahpro.com';
            $requestedAt = $replacements['{{requested_at}}'] ?? (now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB');

            $subject = "[Neriah Pro] {$otp} adalah Kode OTP Masuk Anda";
            $bodyHtml = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kode OTP Masuk // Neriah Pro</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f4f4f5;">
    <div style="max-width: 560px; margin: 40px auto; background-color: #18181b; border-radius: 4px; border: 1px solid #27272a; overflow: hidden;">
        <div style="background-color: #121215; padding: 24px 32px; border-bottom: 1px solid #27272a;">
            <div style="display: inline-block; background-color: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 8px; border-radius: 2px; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 8px;">Autentikasi Klien // Zero-Password</div>
            <h1 style="color: #ffffff; font-size: 18px; font-weight: 800; margin: 0;">Neriah Pro &mdash; Verifikasi Akses Proyek</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: #d4d4d8;">
            <p>Halo,</p>
            <p>Kami menerima permintaan login ke akun Neriah Pro untuk alamat e-mail: <strong style="color: #ffffff;">{$email}</strong>. Gunakan kode OTP 6-digit di bawah ini untuk memverifikasi sesi Anda:</p>
            <div style="margin: 28px 0; padding: 24px; background-color: #09090b; border: 1px dashed #10b981; border-radius: 4px; text-align: center;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 38px; font-weight: 900; letter-spacing: 0.25em; color: #10b981;">{$otp}</div>
                <div style="margin-top: 10px; font-size: 12px; color: #a1a1aa;">Berlaku selama <strong>{$expiry} menit</strong> sejak email ini dikirimkan.</div>
            </div>
            <div style="background-color: rgba(239, 68, 68, 0.08); border-left: 3px solid #ef4444; padding: 14px 16px; margin: 24px 0 16px 0; font-size: 12px; color: #fca5a5;">
                <strong>Peringatan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Neriah Pro. Tim kami tidak pernah meminta kode OTP Anda.
            </div>
            <p style="font-size: 13px; color: #a1a1aa; margin-top: 20px;">
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman karena kode OTP tidak dapat digunakan tanpa akses langsung ke email ini.
            </p>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #27272a; font-size: 11px; color: #71717a; line-height: 1.8;">
                <div>Alamat E-mail: {$email}</div>
                <div>Alamat IP Peminta: {$ip}</div>
                <div>Waktu Permintaan: {$requestedAt}</div>
                <div>Pusat Bantuan & Kontak: <a href="mailto:{$supportEmail}" style="color: #38bdf8; text-decoration: none;">{$supportEmail}</a></div>
            </div>
        </div>
        <div style="background-color: #0d0d0f; padding: 20px 32px; text-align: center; border-top: 1px solid #27272a; font-size: 11px; color: #71717a;">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
HTML;

            return [
                'found' => false,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'sender_name' => 'Neriah Pro Support',
                'sender_email' => 'support@neriahpro.com',
                'reply_to_email' => 'support@neriahpro.com',
            ];
        }

        return [
            'found' => false,
            'subject' => 'Pemberitahuan Sistem Neriah Pro',
            'body_html' => '<p>Pemberitahuan dari Neriah Pro.</p>',
            'sender_name' => 'Neriah Pro Support',
            'sender_email' => 'support@neriahpro.com',
            'reply_to_email' => 'support@neriahpro.com',
        ];
    }
}
