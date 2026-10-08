<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Seed predefined enterprise multilingual and theme-adaptive email templates.
     */
    public function run(): void
    {
        $templates = [
            [
                'code' => 'customer_otp',
                'name' => 'Template OTP Autentikasi Klien (2-FA)',
                'subject' => [
                    'id' => '[Neriah Pro] {{otp}} adalah Kode OTP Masuk Anda',
                    'en' => '[Neriah Pro] {{otp}} is Your Login OTP Code',
                ],
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
                    '{{app_url}}',
                    '{{theme_body_bg}}',
                    '{{theme_card_bg}}',
                    '{{theme_title_color}}',
                    '{{theme_body_text}}',
                    '{{theme_otp_color}}',
                ],
                'body_html' => [
                    'id' => <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>[Neriah Pro] {{otp}} adalah Kode OTP Masuk Anda</title>
</head>
<body style="margin: 0; padding: 0; background-color: {{theme_body_bg}}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: {{theme_body_text}};">
    <div style="max-width: 560px; margin: 40px auto; background-color: {{theme_card_bg}}; border-radius: 4px; border: 1px solid {{theme_card_border}}; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="background-color: {{theme_header_bg}}; padding: 24px 32px; border-bottom: 1px solid {{theme_header_border}};">
            <div style="display: inline-block; background-color: {{theme_badge_bg}}; color: {{theme_badge_text}}; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 8px; border-radius: 2px; border: 1px solid {{theme_badge_border}}; margin-bottom: 8px;">Autentikasi Klien // Zero-Password</div>
            <h1 style="color: {{theme_title_color}}; font-size: 18px; font-weight: 800; margin: 0; letter-spacing: -0.025em;">Neriah Pro &mdash; Verifikasi Akses Proyek</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: {{theme_body_text}};">
            <p style="margin-top: 0;">Halo,</p>
            <p>Kami menerima permintaan login ke akun Neriah Pro untuk alamat e-mail: <strong style="color: {{theme_bold_text}};">{{email}}</strong>. Gunakan kode OTP 6-digit di bawah ini untuk memverifikasi sesi Anda:</p>
            <div style="margin: 28px 0; padding: 24px; background-color: {{theme_box_bg}}; border: 1px dashed {{theme_box_border}}; border-radius: 4px; text-align: center;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 38px; font-weight: 900; letter-spacing: 0.25em; color: {{theme_otp_color}}; margin: 0;">{{otp}}</div>
                <div style="margin-top: 10px; font-size: 12px; color: {{theme_subtext}};">Berlaku selama <strong>{{expiry_minutes}} menit</strong> sejak email ini dikirimkan.</div>
            </div>
            <div style="background-color: {{theme_alert_bg}}; border-left: 3px solid {{theme_alert_border}}; padding: 14px 16px; margin: 24px 0 16px 0; border-radius: 2px; font-size: 12px; color: {{theme_alert_text}}; line-height: 1.5;">
                <strong>Peringatan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Neriah Pro. Tim kami tidak pernah meminta kode OTP Anda.
            </div>
            <p style="font-size: 13px; color: {{theme_subtext}}; margin-top: 20px;">
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman karena kode OTP tidak dapat digunakan tanpa akses langsung ke email ini.
            </p>
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid {{theme_meta_border}}; font-size: 11px; color: {{theme_meta_label}}; line-height: 1.8;">
                <div>Alamat E-mail: <span style="color: {{theme_meta_value}};">{{email}}</span></div>
                <div>Alamat IP Peminta: <span style="color: {{theme_meta_value}};">{{ip_address}}</span></div>
                <div>Waktu Permintaan: <span style="color: {{theme_meta_value}};">{{requested_at}}</span></div>
                <div>Pusat Bantuan & Kontak: <a href="mailto:{{support_email}}" style="color: {{theme_link_color}}; text-decoration: none;">{{support_email}}</a></div>
            </div>
        </div>
        <div style="background-color: {{theme_footer_bg}}; padding: 20px 32px; text-align: center; border-top: 1px solid {{theme_footer_border}}; font-size: 11px; color: {{theme_footer_text}};">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
HTML,
                    'en' => <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>[Neriah Pro] {{otp}} is Your Login OTP Code</title>
</head>
<body style="margin: 0; padding: 0; background-color: {{theme_body_bg}}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: {{theme_body_text}};">
    <div style="max-width: 560px; margin: 40px auto; background-color: {{theme_card_bg}}; border-radius: 4px; border: 1px solid {{theme_card_border}}; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="background-color: {{theme_header_bg}}; padding: 24px 32px; border-bottom: 1px solid {{theme_header_border}};">
            <div style="display: inline-block; background-color: {{theme_badge_bg}}; color: {{theme_badge_text}}; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 8px; border-radius: 2px; border: 1px solid {{theme_badge_border}}; margin-bottom: 8px;">Client Authentication // Zero-Password</div>
            <h1 style="color: {{theme_title_color}}; font-size: 18px; font-weight: 800; margin: 0; letter-spacing: -0.025em;">Neriah Pro &mdash; Project Access Verification</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: {{theme_body_text}};">
            <p style="margin-top: 0;">Hello,</p>
            <p>We received a login request for your Neriah Pro account with email address: <strong style="color: {{theme_bold_text}};">{{email}}</strong>. Use the 6-digit OTP code below to verify your session:</p>
            <div style="margin: 28px 0; padding: 24px; background-color: {{theme_box_bg}}; border: 1px dashed {{theme_box_border}}; border-radius: 4px; text-align: center;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 38px; font-weight: 900; letter-spacing: 0.25em; color: {{theme_otp_color}}; margin: 0;">{{otp}}</div>
                <div style="margin-top: 10px; font-size: 12px; color: {{theme_subtext}};">Valid for <strong>{{expiry_minutes}} minutes</strong> from the time this email was sent.</div>
            </div>
            <div style="background-color: {{theme_alert_bg}}; border-left: 3px solid {{theme_alert_border}}; padding: 14px 16px; margin: 24px 0 16px 0; border-radius: 2px; font-size: 12px; color: {{theme_alert_text}}; line-height: 1.5;">
                <strong>Security Warning:</strong> Never share this OTP code with anyone, including anyone claiming to represent Neriah Pro. Our team will never ask for your OTP code.
            </div>
            <p style="font-size: 13px; color: {{theme_subtext}}; margin-top: 20px;">
                If you did not request this login, please ignore this email. Your account remains secure as the OTP cannot be used without direct access to this email.
            </p>
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid {{theme_meta_border}}; font-size: 11px; color: {{theme_meta_label}}; line-height: 1.8;">
                <div>Email Address: <span style="color: {{theme_meta_value}};">{{email}}</span></div>
                <div>Requester IP: <span style="color: {{theme_meta_value}};">{{ip_address}}</span></div>
                <div>Requested At: <span style="color: {{theme_meta_value}};">{{requested_at}}</span></div>
                <div>Help & Support: <a href="mailto:{{support_email}}" style="color: {{theme_link_color}}; text-decoration: none;">{{support_email}}</a></div>
            </div>
        </div>
        <div style="background-color: {{theme_footer_bg}}; padding: 20px 32px; text-align: center; border-top: 1px solid {{theme_footer_border}}; font-size: 11px; color: {{theme_footer_text}};">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
HTML,
                ],
                'is_active' => true,
            ],
            [
                'code' => 'client_welcome',
                'name' => 'Selamat Datang Klien Baru',
                'subject' => [
                    'id' => 'Selamat Datang di Workspace Proyek Neriah Pro, {{name}}!',
                    'en' => 'Welcome to Neriah Pro Project Workspace, {{name}}!',
                ],
                'sender_name' => 'Neriah Pro Support',
                'sender_email' => 'support@neriahpro.com',
                'reply_to_email' => 'support@neriahpro.com',
                'available_placeholders' => [
                    '{{name}}',
                    '{{email}}',
                    '{{support_email}}',
                    '{{app_url}}',
                    '{{theme_body_bg}}',
                    '{{theme_card_bg}}',
                    '{{theme_title_color}}',
                    '{{theme_body_text}}',
                    '{{theme_btn_bg}}',
                    '{{theme_btn_text}}',
                ],
                'body_html' => [
                    'id' => <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Selamat Datang di Neriah Pro</title>
</head>
<body style="margin: 0; padding: 0; background-color: {{theme_body_bg}}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: {{theme_body_text}};">
    <div style="max-width: 560px; margin: 40px auto; background-color: {{theme_card_bg}}; border-radius: 4px; border: 1px solid {{theme_card_border}}; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="background-color: {{theme_header_bg}}; padding: 24px 32px; border-bottom: 1px solid {{theme_header_border}};">
            <h1 style="color: {{theme_title_color}}; font-size: 18px; font-weight: 800; margin: 0;">Selamat Datang di Neriah Pro</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: {{theme_body_text}};">
            <p style="margin-top: 0;">Halo <strong style="color: {{theme_bold_text}};">{{name}}</strong>,</p>
            <p>Akun portal klien Anda telah aktif. Anda dapat memantau status sprint, cetak biru arsitektur, dan dokumen legal kontrak Anda kapan saja melalui dashboard kami.</p>
            <div style="margin: 24px 0; text-align: center;">
                <a href="{{app_url}}/customer/login" style="display: inline-block; background-color: {{theme_btn_bg}}; color: {{theme_btn_text}}; font-weight: 800; font-size: 13px; text-decoration: none; padding: 12px 24px; border-radius: 2px;">MASUK KE PORTAL KLIEN &rarr;</a>
            </div>
            <p style="font-size: 12px; color: {{theme_subtext}};">Butuh bantuan teknis? Hubungi tim kami di <a href="mailto:{{support_email}}" style="color: {{theme_link_color}};">{{support_email}}</a>.</p>
        </div>
        <div style="background-color: {{theme_footer_bg}}; padding: 20px 32px; text-align: center; border-top: 1px solid {{theme_footer_border}}; font-size: 11px; color: {{theme_footer_text}};">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture OS.
        </div>
    </div>
</body>
</html>
HTML,
                    'en' => <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to Neriah Pro</title>
</head>
<body style="margin: 0; padding: 0; background-color: {{theme_body_bg}}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: {{theme_body_text}};">
    <div style="max-width: 560px; margin: 40px auto; background-color: {{theme_card_bg}}; border-radius: 4px; border: 1px solid {{theme_card_border}}; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="background-color: {{theme_header_bg}}; padding: 24px 32px; border-bottom: 1px solid {{theme_header_border}};">
            <h1 style="color: {{theme_title_color}}; font-size: 18px; font-weight: 800; margin: 0;">Welcome to Neriah Pro</h1>
        </div>
        <div style="padding: 32px; font-size: 14px; line-height: 1.6; color: {{theme_body_text}};">
            <p style="margin-top: 0;">Hello <strong style="color: {{theme_bold_text}};">{{name}}</strong>,</p>
            <p>Your client portal workspace is now ready. You can inspect active sprint milestones, architectural blueprints, and legal contracts directly from your dashboard.</p>
            <div style="margin: 24px 0; text-align: center;">
                <a href="{{app_url}}/customer/login" style="display: inline-block; background-color: {{theme_btn_bg}}; color: {{theme_btn_text}}; font-weight: 800; font-size: 13px; text-decoration: none; padding: 12px 24px; border-radius: 2px;">ACCESS CLIENT WORKSPACE &rarr;</a>
            </div>
            <p style="font-size: 12px; color: {{theme_subtext}};">Need engineering assistance? Contact our team at <a href="mailto:{{support_email}}" style="color: {{theme_link_color}};">{{support_email}}</a>.</p>
        </div>
        <div style="background-color: {{theme_footer_bg}}; padding: 20px 32px; text-align: center; border-top: 1px solid {{theme_footer_border}}; font-size: 11px; color: {{theme_footer_text}};">
            &copy; 2026 PT Neriah Pro Solusindo &bull; Digital Architecture OS.
        </div>
    </div>
</body>
</html>
HTML,
                ],
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
