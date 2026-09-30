<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #18181b;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e4e4e7;
        }
        .header {
            background-color: #09090b;
            padding: 24px 32px;
            text-align: left;
            border-bottom: 2px solid #4f46e5;
        }
        .header-title {
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin: 0;
        }
        .header-subtitle {
            color: #a1a1aa;
            font-size: 12px;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .content {
            padding: 32px;
            font-size: 15px;
            line-height: 1.6;
            color: #27272a;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 16px;
        }
        .cta-container {
            margin: 32px 0 24px 0;
            text-align: center;
        }
        .cta-button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 6px;
            letter-spacing: 0.025em;
        }
        .footer {
            background-color: #fafafa;
            padding: 24px 32px;
            border-top: 1px solid #e4e4e7;
            font-size: 12px;
            color: #71717a;
            text-align: center;
            line-height: 1.5;
        }
        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="header-title">NERIAH PRO</h1>
            <div class="header-subtitle">Digital Architecture & Enterprise Software Solutions</div>
        </div>

        <!-- Body Content -->
        <div class="content">
            <p>Halo <strong>{{ $recipientName }}</strong>,</p>

            <div style="margin-top: 16px; margin-bottom: 24px;">
                {!! nl2br($renderedBody) !!}
            </div>

            @if($campaign->cta_url)
                <div class="cta-container">
                    <a href="{{ $campaign->cta_url }}" target="_blank" class="cta-button">
                        {{ $campaign->cta_label ?: 'Kunjungi Layanan Kami' }} →
                    </a>
                </div>
            @endif

            <p style="margin-top: 24px; font-size: 13px; color: #52525b;">
                Hormat kami,<br>
                <strong>Tim Neriah Pro</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 8px 0;">
                Anda menerima email ini karena Anda terdaftar sebagai klien atau mitra terpercaya <strong>Neriah Pro</strong>.
            </p>
            <p style="margin: 0;">
                © {{ date('Y') }} Neriah Pro. Hak cipta dilindungi undang-undang.<br>
                <a href="{{ url('/') }}">Kunjungi Portal Neriah Pro</a> &bull; <a href="{{ route('cv-pro.index') }}">CV Pro SaaS Studio</a> &bull; <a href="{{ url('/blueprint') }}">Project OS Blueprint</a>
            </p>
        </div>
    </div>
</body>
</html>
