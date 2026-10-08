<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Seed predefined enterprise email templates.
     */
    public function run(): void
    {
        $templates = [
            [
                'code' => 'customer_otp',
                'name' => 'Template OTP Autentikasi Klien',
                'subject' => '[Neriah Pro] {{otp}} adalah Kode OTP Masuk Anda',
                'sender_name' => 'Neriah Pro Support',
                'sender_email' => 'support@neriahpro.com',
                'reply_to_email' => 'support@neriahpro.com',
                'available_placeholders' => [
                    '{{otp}}',
                    '{{email}}',
                    '{{ip_address}}',
                    '{{requested_at}}',
                    '{{expiry_minutes}}',
                    '{{support_email}}',
                    '{{app_name}}',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kode OTP Masuk // Neriah Pro</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f4f4f5;">
    <div style="max-width: 560px; margin: 40px auto; background-color: #18181b; border-radius: 4px; border: 1px solid #27272a; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
        <div style="background-color: #121215; padding: 24px 32px; border-bottom: 1px solid #27272a;">
            <div style="display: inline-block; background-color: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 8px; border-radius: 2px; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 8px;">Autentikasi Klien // Zero-Password</div>
            <h1 style="color: #ffffff; font-size: 18px; font-weight: 800; margin: 0; letter-spacing: -0.025em;">Neriah Pro &mdash; Verifikasi Akses Proyek</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: #d4d4d8;">
            <p style="margin-top: 0;">Halo,</p>
            <p>Kami menerima permintaan login ke akun Neriah Pro untuk alamat e-mail: <strong style="color: #ffffff;">{{email}}</strong>. Gunakan kode OTP 6-digit di bawah ini untuk memverifikasi sesi Anda:</p>
            <div style="margin: 28px 0; padding: 24px; background-color: #09090b; border: 1px dashed #10b981; border-radius: 4px; text-align: center;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 38px; font-weight: 900; letter-spacing: 0.25em; color: #10b981; margin: 0;">{{otp}}</div>
                <div style="margin-top: 10px; font-size: 12px; color: #a1a1aa;">Berlaku selama <strong>{{expiry_minutes}} menit</strong> sejak email ini dikirimkan.</div>
            </div>
            <div style="background-color: rgba(239, 68, 68, 0.08); border-left: 3px solid #ef4444; padding: 14px 16px; margin: 24px 0 16px 0; border-radius: 2px; font-size: 12px; color: #fca5a5; line-height: 1.5;">
                <strong>Peringatan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Neriah Pro. Tim kami tidak pernah meminta kode OTP Anda.
            </div>
            <p style="font-size: 13px; color: #a1a1aa; margin-top: 20px;">
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman karena kode OTP tidak dapat digunakan tanpa akses langsung ke email ini.
            </p>
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #27272a; font-size: 11px; color: #71717a; line-height: 1.8;">
                <div>Alamat E-mail: <span style="color: #d4d4d8;">{{email}}</span></div>
                <div>Alamat IP Peminta: <span style="color: #d4d4d8;">{{ip_address}}</span></div>
                <div>Waktu Permintaan: <span style="color: #d4d4d8;">{{requested_at}}</span></div>
                <div>Pusat Bantuan & Kontak: <a href="mailto:{{support_email}}" style="color: #38bdf8; text-decoration: none;">{{support_email}}</a></div>
            </div>
        </div>
        <div style="background-color: #0d0d0f; padding: 20px 32px; text-align: center; border-top: 1px solid #27272a; font-size: 11px; color: #71717a;">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
HTML,
                'is_active' => true,
            ],
            [
                'code' => 'client_welcome',
                'name' => 'Selamat Datang Klien Baru',
                'subject' => 'Selamat Datang di Workspace Proyek Neriah Pro, {{name}}!',
                'sender_name' => 'Neriah Pro Support',
                'sender_email' => 'support@neriahpro.com',
                'reply_to_email' => 'support@neriahpro.com',
                'available_placeholders' => [
                    '{{name}}',
                    '{{email}}',
                    '{{support_email}}',
                    '{{app_url}}',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Selamat Datang // Neriah Pro</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f4f4f5;">
    <div style="max-width: 560px; margin: 40px auto; background-color: #18181b; border-radius: 4px; border: 1px solid #27272a; overflow: hidden;">
        <div style="background-color: #121215; padding: 24px 32px; border-bottom: 1px solid #27272a;">
            <h1 style="color: #ffffff; font-size: 18px; font-weight: 800; margin: 0;">Selamat Datang di Neriah Pro</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: #d4d4d8;">
            <p>Halo <strong>{{name}}</strong>,</p>
            <p>Akun portal klien Anda telah aktif. Anda dapat memantau status sprint, cetak biru arsitektur, dan dokumen legal kontrak Anda kapan saja melalui dashboard kami.</p>
            <div style="margin: 24px 0; text-align: center;">
                <a href="{{app_url}}/customer/login" style="display: inline-block; background-color: #10b981; color: #000000; font-weight: 800; font-size: 13px; text-decoration: none; padding: 12px 24px; border-radius: 2px;">MASUK KE PORTAL KLIEN &rarr;</a>
            </div>
            <p style="font-size: 12px; color: #a1a1aa;">Butuh bantuan teknis? Hubungi tim kami di <a href="mailto:{{support_email}}" style="color: #38bdf8;">{{support_email}}</a>.</p>
        </div>
        <div style="background-color: #0d0d0f; padding: 20px 32px; text-align: center; border-top: 1px solid #27272a; font-size: 11px; color: #71717a;">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture OS.
        </div>
    </div>
</body>
</html>
HTML,
                'is_active' => true,
            ]
        ];

        foreach ($templates as $t) {
            EmailTemplate::updateOrCreate(
                ['code' => $t['code']],
                $t
            );
        }
    }
}
