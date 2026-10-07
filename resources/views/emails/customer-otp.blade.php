<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Masuk // Neriah Pro</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #09090b;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #f4f4f5;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 560px;
            margin: 40px auto;
            background-color: #18181b;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #27272a;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            background-color: #121215;
            padding: 24px 32px;
            border-bottom: 1px solid #27272a;
        }
        .brand-badge {
            display: inline-block;
            background-color: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 2px;
            border: 1px solid rgba(99, 102, 241, 0.3);
            margin-bottom: 8px;
        }
        .header-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin: 0;
        }
        .content {
            padding: 32px;
            font-size: 14px;
            line-height: 1.6;
            color: #d4d4d8;
        }
        .otp-box {
            margin: 28px 0;
            padding: 24px;
            background-color: #09090b;
            border: 1px dashed #4f46e5;
            border-radius: 4px;
            text-align: center;
        }
        .otp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 38px;
            font-weight: 900;
            letter-spacing: 0.25em;
            color: #38bdf8;
            margin: 0;
            padding: 0;
        }
        .otp-expiry {
            margin-top: 10px;
            font-size: 12px;
            color: #a1a1aa;
        }
        .security-notice {
            background-color: rgba(239, 68, 68, 0.08);
            border-left: 3px solid #ef4444;
            padding: 14px 16px;
            margin: 24px 0 16px 0;
            border-radius: 2px;
            font-size: 12px;
            color: #fca5a5;
            line-height: 1.5;
        }
        .meta-list {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #27272a;
            font-size: 11px;
            color: #71717a;
            line-height: 1.6;
        }
        .footer {
            background-color: #0d0d0f;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #27272a;
            font-size: 11px;
            color: #71717a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="brand-badge">Autentikasi Klien // Zero-Password</div>
            <h1 class="header-title">Neriah Pro &mdash; Verifikasi Akses Proyek</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Kami menerima permintaan login ke akun Neriah Pro untuk alamat e-mail: <strong style="color: #ffffff;">{{ $email }}</strong>. Gunakan kode OTP 6-digit di bawah ini untuk memverifikasi sesi Anda:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">Berlaku selama <strong>{{ $expiryMinutes }} menit</strong> sejak email ini dikirimkan.</div>
            </div>

            <div class="security-notice">
                <strong>Peringatan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Neriah Pro. Tim kami tidak pernah meminta kode OTP Anda.
            </div>

            <p style="font-size: 13px; color: #a1a1aa; margin-top: 20px;">
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman karena kode OTP tidak dapat digunakan tanpa akses langsung ke email ini.
            </p>

            <div class="meta-list">
                <div>Alamat E-mail: {{ $email }}</div>
                @if($ipAddress)
                <div>Alamat IP Peminta: {{ $ipAddress }}</div>
                @endif
                <div>Waktu Permintaan: {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB</div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
