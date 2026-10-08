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
        'subject' => 'array',
        'body_html' => 'array',
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
     * Get theme color and styling tokens for adaptive Light vs Dark email templates.
     */
    public static function getThemeTokens(string $theme = 'dark'): array
    {
        $isLight = ($theme === 'light');

        if ($isLight) {
            return [
                '{{theme_body_bg}}' => '#f4f4f5',
                '{{theme_card_bg}}' => '#ffffff',
                '{{theme_card_border}}' => '#e4e4e7',
                '{{theme_header_bg}}' => '#fafafa',
                '{{theme_header_border}}' => '#e4e4e7',
                '{{theme_badge_bg}}' => 'rgba(16, 185, 129, 0.1)',
                '{{theme_badge_border}}' => 'rgba(16, 185, 129, 0.3)',
                '{{theme_badge_text}}' => '#059669',
                '{{theme_title_color}}' => '#09090b',
                '{{theme_body_text}}' => '#3f3f46',
                '{{theme_bold_text}}' => '#09090b',
                '{{theme_box_bg}}' => '#f8fafc',
                '{{theme_box_border}}' => '#059669',
                '{{theme_otp_color}}' => '#059669',
                '{{theme_subtext}}' => '#71717a',
                '{{theme_alert_bg}}' => '#fef2f2',
                '{{theme_alert_border}}' => '#dc2626',
                '{{theme_alert_text}}' => '#991b1b',
                '{{theme_footer_bg}}' => '#f4f4f5',
                '{{theme_footer_border}}' => '#e4e4e7',
                '{{theme_footer_text}}' => '#71717a',
                '{{theme_meta_border}}' => '#e4e4e7',
                '{{theme_meta_label}}' => '#71717a',
                '{{theme_meta_value}}' => '#18181b',
                '{{theme_link_color}}' => '#0284c7',
                '{{theme_btn_bg}}' => '#059669',
                '{{theme_btn_text}}' => '#ffffff',
            ];
        }

        // Default: Dark Theme (Obsidian & Emerald)
        return [
            '{{theme_body_bg}}' => '#09090b',
            '{{theme_card_bg}}' => '#18181b',
            '{{theme_card_border}}' => '#27272a',
            '{{theme_header_bg}}' => '#121215',
            '{{theme_header_border}}' => '#27272a',
            '{{theme_badge_bg}}' => 'rgba(16, 185, 129, 0.15)',
            '{{theme_badge_border}}' => 'rgba(16, 185, 129, 0.3)',
            '{{theme_badge_text}}' => '#10b981',
            '{{theme_title_color}}' => '#ffffff',
            '{{theme_body_text}}' => '#d4d4d8',
            '{{theme_bold_text}}' => '#ffffff',
            '{{theme_box_bg}}' => '#09090b',
            '{{theme_box_border}}' => '#10b981',
            '{{theme_otp_color}}' => '#10b981',
            '{{theme_subtext}}' => '#a1a1aa',
            '{{theme_alert_bg}}' => 'rgba(239, 68, 68, 0.08)',
            '{{theme_alert_border}}' => '#ef4444',
            '{{theme_alert_text}}' => '#fca5a5',
            '{{theme_footer_bg}}' => '#0d0d0f',
            '{{theme_footer_border}}' => '#27272a',
            '{{theme_footer_text}}' => '#71717a',
            '{{theme_meta_border}}' => '#27272a',
            '{{theme_meta_label}}' => '#71717a',
            '{{theme_meta_value}}' => '#d4d4d8',
            '{{theme_link_color}}' => '#38bdf8',
            '{{theme_btn_bg}}' => '#10b981',
            '{{theme_btn_text}}' => '#000000',
        ];
    }

    /**
     * Render subject, body HTML, sender details with multilingual resolution and theme styling.
     */
    public static function renderTemplate(string $code, array $variables = [], string $locale = 'id', string $theme = 'dark'): array
    {
        // Mandatory Rule: If not Indonesian ('id'), it MUST strictly default to English ('en')
        $resolvedLocale = ($locale === 'id') ? 'id' : 'en';
        $resolvedTheme = ($theme === 'light') ? 'light' : 'dark';

        // Global system default variables
        $defaultVariables = [
            '{{app_name}}' => config('app.name', 'Neriah Pro'),
            '{{app_url}}' => config('app.url', 'https://neriahpro.com'),
            '{{support_email}}' => 'support@neriahpro.com',
            '{{year}}' => date('Y'),
        ];

        $themeTokens = static::getThemeTokens($resolvedTheme);

        // Build substitution map combining theme tokens, system defaults, and user variables
        $replacements = $themeTokens;
        foreach (array_merge($defaultVariables, $variables) as $key => $val) {
            $placeholder = str_starts_with($key, '{{') && str_ends_with($key, '}}') ? $key : ('{{' . $key . '}}');
            $replacements[$placeholder] = (string) $val;
        }

        $template = static::getCachedByCode($code);

        if ($template) {
            $rawSubject = $template->subject;
            $subjectText = is_array($rawSubject)
                ? ($rawSubject[$resolvedLocale] ?? $rawSubject['en'] ?? $rawSubject['id'] ?? '')
                : (string) $rawSubject;

            $rawBody = $template->body_html;
            $bodyText = is_array($rawBody)
                ? ($rawBody[$resolvedLocale] ?? $rawBody['en'] ?? $rawBody['id'] ?? '')
                : (string) $rawBody;

            $subject = str_replace(array_keys($replacements), array_values($replacements), $subjectText);
            $bodyHtml = str_replace(array_keys($replacements), array_values($replacements), $bodyText);

            return [
                'found' => true,
                'locale' => $resolvedLocale,
                'theme' => $resolvedTheme,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'sender_name' => $template->sender_name ?: 'Neriah Pro Support',
                'sender_email' => $template->sender_email ?: 'support@neriahpro.com',
                'reply_to_email' => $template->reply_to_email ?: 'support@neriahpro.com',
            ];
        }

        // Clean hardcoded fallback if template not yet in DB
        return static::fallbackTemplate($code, $replacements, $resolvedLocale, $resolvedTheme);
    }

    /**
     * Resilient default template if database table or record is not yet seeded.
     */
    protected static function fallbackTemplate(string $code, array $replacements, string $locale = 'id', string $theme = 'dark'): array
    {
        $tokens = static::getThemeTokens($theme);

        if ($code === 'customer_otp') {
            $otp = $replacements['{{otp}}'] ?? '123456';
            $email = $replacements['{{email}}'] ?? '';
            $ip = $replacements['{{ip_address}}'] ?? '127.0.0.1';
            $expiry = $replacements['{{expiry_minutes}}'] ?? '10';
            $supportEmail = $replacements['{{support_email}}'] ?? 'support@neriahpro.com';
            $requestedAt = $replacements['{{requested_at}}'] ?? (now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB');

            $isEn = ($locale === 'en');

            $subject = $isEn
                ? "[Neriah Pro] {$otp} is Your Login OTP Code"
                : "[Neriah Pro] {$otp} adalah Kode OTP Masuk Anda";

            $badgeText = $isEn ? 'Client Authentication // Zero-Password' : 'Autentikasi Klien // Zero-Password';
            $titleText = $isEn ? 'Neriah Pro &mdash; Project Access Verification' : 'Neriah Pro &mdash; Verifikasi Akses Proyek';
            $greetingText = $isEn ? 'Hello,' : 'Halo,';
            $introText = $isEn ? 'We received a login request for your Neriah Pro account with email address' : 'Kami menerima permintaan login ke akun Neriah Pro untuk alamat e-mail';
            $instructionText = $isEn ? 'Use the 6-digit OTP code below to verify your session' : 'Gunakan kode OTP 6-digit di bawah ini untuk memverifikasi sesi Anda';
            $expiryText = $isEn ? "Valid for <strong>{$expiry} minutes</strong> from the time this email was sent." : "Berlaku selama <strong>{$expiry} menit</strong> sejak email ini dikirimkan.";
            $warningLabel = $isEn ? 'Security Warning' : 'Peringatan Keamanan';
            $warningText = $isEn ? 'Never share this OTP code with anyone, including anyone claiming to represent Neriah Pro. Our team will never ask for your OTP code.' : 'Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Neriah Pro. Tim kami tidak pernah meminta kode OTP Anda.';
            $ignoreText = $isEn ? 'If you did not request this login, please ignore this email. Your account remains secure as the OTP cannot be used without direct access to this email.' : 'Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman karena kode OTP tidak dapat digunakan tanpa akses langsung ke email ini.';
            $emailLabel = $isEn ? 'Email Address' : 'Alamat E-mail';
            $ipLabel = $isEn ? 'Requester IP' : 'Alamat IP Peminta';
            $timeLabel = $isEn ? 'Requested At' : 'Waktu Permintaan';
            $helpLabel = $isEn ? 'Help & Support Center' : 'Pusat Bantuan & Kontak';

            $bodyHtml = <<<HTML
<!DOCTYPE html>
<html lang="{$locale}">
<head>
    <meta charset="utf-8">
    <title>{$subject}</title>
</head>
<body style="margin: 0; padding: 0; background-color: {$tokens['{{theme_body_bg}}']}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: {$tokens['{{theme_body_text}}']};">
    <div style="max-width: 560px; margin: 40px auto; background-color: {$tokens['{{theme_card_bg}}']}; border-radius: 4px; border: 1px solid {$tokens['{{theme_card_border}}']}; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="background-color: {$tokens['{{theme_header_bg}}']}; padding: 24px 32px; border-bottom: 1px solid {$tokens['{{theme_header_border}}']};">
            <div style="display: inline-block; background-color: {$tokens['{{theme_badge_bg}}']}; color: {$tokens['{{theme_badge_text}}']}; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 8px; border-radius: 2px; border: 1px solid {$tokens['{{theme_badge_border}}']}; margin-bottom: 8px;">
                {$badgeText}
            </div>
            <h1 style="color: {$tokens['{{theme_title_color}}']}; font-size: 18px; font-weight: 800; margin: 0; letter-spacing: -0.025em;">
                {$titleText}
            </h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: {$tokens['{{theme_body_text}}']};">
            <p style="margin-top: 0;">{$greetingText}</p>
            <p>{$introText}: <strong style="color: {$tokens['{{theme_bold_text}}']};">{$email}</strong>. {$instructionText}:</p>
            <div style="margin: 28px 0; padding: 24px; background-color: {$tokens['{{theme_box_bg}}']}; border: 1px dashed {$tokens['{{theme_box_border}}']}; border-radius: 4px; text-align: center;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 38px; font-weight: 900; letter-spacing: 0.25em; color: {$tokens['{{theme_otp_color}}']}; margin: 0;">{$otp}</div>
                <div style="margin-top: 10px; font-size: 12px; color: {$tokens['{{theme_subtext}}']};">{$expiryText}</div>
            </div>
            <div style="background-color: {$tokens['{{theme_alert_bg}}']}; border-left: 3px solid {$tokens['{{theme_alert_border}}']}; padding: 14px 16px; margin: 24px 0 16px 0; border-radius: 2px; font-size: 12px; color: {$tokens['{{theme_alert_text}}']}; line-height: 1.5;">
                <strong>{$warningLabel}:</strong> {$warningText}
            </div>
            <p style="font-size: 13px; color: {$tokens['{{theme_subtext}}']}; margin-top: 20px;">
                {$ignoreText}
            </p>
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid {$tokens['{{theme_meta_border}}']}; font-size: 11px; color: {$tokens['{{theme_meta_label}}']}; line-height: 1.8;">
                <div>{$emailLabel}: <span style="color: {$tokens['{{theme_meta_value}}']};">{$email}</span></div>
                <div>{$ipLabel}: <span style="color: {$tokens['{{theme_meta_value}}']};">{$ip}</span></div>
                <div>{$timeLabel}: <span style="color: {$tokens['{{theme_meta_value}}']};">{$requestedAt}</span></div>
                <div>{$helpLabel}: <a href="mailto:{$supportEmail}" style="color: {$tokens['{{theme_link_color}}']}; text-decoration: none;">{$supportEmail}</a></div>
            </div>
        </div>
        <div style="background-color: {$tokens['{{theme_footer_bg}}']}; padding: 20px 32px; text-align: center; border-top: 1px solid {$tokens['{{theme_footer_border}}']}; font-size: 11px; color: {$tokens['{{theme_footer_text}}']};">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
HTML;

            return [
                'found' => false,
                'locale' => $locale,
                'theme' => $theme,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'sender_name' => 'Neriah Pro Support',
                'sender_email' => 'support@neriahpro.com',
                'reply_to_email' => 'support@neriahpro.com',
            ];
        }

        return [
            'found' => false,
            'locale' => $locale,
            'theme' => $theme,
            'subject' => $locale === 'en' ? 'Neriah Pro System Notification' : 'Pemberitahuan Sistem Neriah Pro',
            'body_html' => $locale === 'en' ? '<p>System notification from Neriah Pro.</p>' : '<p>Pemberitahuan dari Neriah Pro.</p>',
            'sender_name' => 'Neriah Pro Support',
            'sender_email' => 'support@neriahpro.com',
            'reply_to_email' => 'support@neriahpro.com',
        ];
    }
}
