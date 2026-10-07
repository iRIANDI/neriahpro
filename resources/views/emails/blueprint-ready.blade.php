<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Biru Arsitektur Siap // Neriah Pro</title>
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
            max-width: 600px;
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
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 2px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            margin-bottom: 8px;
        }
        .header-title {
            color: #ffffff;
            font-size: 20px;
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
        .card-project {
            margin: 20px 0;
            padding: 20px;
            background-color: #09090b;
            border: 1px solid #27272a;
            border-radius: 4px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #18181b;
            font-size: 13px;
        }
        .meta-label {
            color: #a1a1aa;
        }
        .meta-value {
            color: #ffffff;
            font-weight: 600;
        }
        .cta-btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 24px;
            border-radius: 4px;
            margin: 24px 0;
            border: 1px solid #6366f1;
        }
        .hash-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            color: #38bdf8;
            background-color: #09090b;
            padding: 6px 8px;
            border-radius: 2px;
            border: 1px solid #27272a;
            word-break: break-all;
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
            <div class="brand-badge">Project OS // Digital Architecture Engine</div>
            <h1 class="header-title">Cetak Biru Arsitektur Siap &bull; {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</h1>
        </div>
        <div class="content">
            <p>Halo <strong style="color: #ffffff;">{{ $blueprint->client_name ?: 'Rekan Founder' }}</strong>,</p>
            <p>Cetak biru arsitektur perangkat lunak Anda telah berhasil disintesis dan dipublikasikan di platform <strong>Neriah Pro Scope Lock OS</strong>.</p>

            <div class="card-project">
                <div class="meta-row">
                    <span class="meta-label">Nama Proyek:</span>
                    <span class="meta-value">{{ $blueprint->nama_bisnis }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Paket Lisensi:</span>
                    <span class="meta-value">{{ $tierName }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Status Dokumen:</span>
                    <span class="meta-value" style="color: #34d399;">Scope Locked &amp; Ready for Execution</span>
                </div>
                <div class="meta-row" style="border-bottom: none;">
                    <span class="meta-label">Hak Unduh:</span>
                    <span class="meta-value" style="color: #38bdf8;">Selamanya (PDF, Markdown, Zip Scaffold)</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $accessUrl }}" class="cta-btn">Buka Dokumen Arsitektur Proyek &rarr;</a>
            </div>

            <p style="font-size: 13px; color: #a1a1aa;">
                Integritas Dokumen Kriptografis (SHA-256):
            </p>
            <div class="hash-code">
                {{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}
            </div>

            <p style="font-size: 12px; color: #71717a; margin-top: 20px;">
                Catatan: Paket ini adalah <strong>Self-Service Blueprint</strong> yang Anda dan tim developer gunakan secara mandiri. Jika Anda membutuhkan tim Neriah Pro untuk mengoding sistem ini secara turnkey, Anda dapat mengajukan konsultasi kontrak Studio MVP langsung dari dalam dokumen blueprint.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PT Neriah Pro Solusindo &bull; Digital Architecture & Enterprise Scope Lock OS.
        </div>
    </div>
</body>
</html>
