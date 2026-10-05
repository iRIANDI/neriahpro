<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ultimate PRD - {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.5; color: #1f2937; margin: 25px; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 12px; margin-bottom: 20px; }
        .badge { display: inline-block; background-color: #ecfdf5; color: #047857; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 2px 6px; margin-bottom: 5px; }
        h1 { font-size: 18px; margin: 0 0 5px 0; text-transform: uppercase; color: #111827; }
        .meta { font-size: 10px; color: #6b7280; font-family: monospace; }
        .section { margin-bottom: 22px; }
        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #065f46; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; font-size: 10px; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; color: #374151; }
        .box { background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; margin-bottom: 12px; font-size: 10.5px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 25px; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="header">
        <span class="badge">Neriah Pro &bull; Project OS Ultimate PRD</span>
        <h1>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</h1>
        <div class="meta">
            DOKUMEN SPESIFIKASI TEKNIK &bull; ID: {{ strtoupper(substr($blueprint->id, 0, 12)) }} &bull; CREATED: {{ $blueprint->created_at->format('d M Y') }} &bull; STATUS: {{ strtoupper($blueprint->project_status) }}
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">1. Ringkasan Eksekutif &amp; Masalah Utama</div>
        <div class="box">
            <strong>Masalah Utama:</strong><br>
            {{ $blueprint->masalah_utama }}
        </div>
        <div class="box">
            <strong>Tujuan Utama &amp; Solusi Sistem:</strong><br>
            {{ $blueprint->tujuan_utama }}
        </div>
    </div>

    <!-- Target Audiens & Aktor -->
    <div class="section">
        <div class="section-title">2. Pengguna Sistem &amp; Aktor</div>
        <table>
            <tr>
                <th style="width: 30%;">Target Audiens</th>
                <td>{{ $blueprint->target_audiens ?: 'Pengguna Umum' }}</td>
            </tr>
            <tr>
                <th>Aktor &amp; Peran Sistem</th>
                <td>{{ $blueprint->aktor_sistem ?: 'Superadmin, User' }}</td>
            </tr>
            <tr>
                <th>Alur Kerja Inti</th>
                <td>{{ $blueprint->alur_kerja ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Fitur & Spesifikasi Teknis -->
    <div class="section">
        <div class="section-title">3. Ruang Lingkup Fitur (MVP Scope)</div>
        <div class="box">
            <strong>Fitur Wajib (Fase 1):</strong><br>
            {{ $blueprint->fitur_wajib }}
        </div>
        @if($blueprint->fitur_tambahan)
            <div class="box">
                <strong>Fitur Tambahan (Fase 2 / Pengembangan):</strong><br>
                {{ $blueprint->fitur_tambahan }}
            </div>
        @endif
    </div>

    <!-- Integrasi & Aset -->
    <div class="section">
        <div class="section-title">4. Kebutuhan Integrasi &amp; Desain</div>
        <table>
            <tr>
                <th style="width: 30%;">Kebutuhan Integrasi</th>
                <td>{{ $blueprint->kebutuhan_integrasi ?: 'Midtrans Payment Gateway, Cloud Storage' }}</td>
            </tr>
            <tr>
                <th>Target Timeline</th>
                <td>{{ $blueprint->target_waktu ?: '30 Hari Kerja' }}</td>
            </tr>
            <tr>
                <th>Referensi Desain</th>
                <td>{{ $blueprint->referensi_desain ?: 'Clean, Monokromatik High-Performance' }}</td>
            </tr>
        </table>
    </div>

    <!-- Checksum & Scope Lock -->
    <div class="section">
        <div class="section-title">5. Integritas Kriptografis &amp; Scope Freeze</div>
        <div class="box" style="font-family: monospace; font-size: 9.5px; word-break: break-all;">
            <strong>SHA-256 SPECIFICATION CHECKSUM:</strong><br>
            {{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}<br><br>
            @if($blueprint->signed_agreement)
                <strong>STATUS KONTRAK:</strong> TERKUNCI (SIGNED &amp; VERIFIED)<br>
                <strong>PENANDATANGAN:</strong> {{ $blueprint->client_name ?: $blueprint->nama_bisnis }}<br>
                <strong>WAKTU:</strong> {{ $blueprint->signed_at?->format('d M Y H:i:s T') }}<br>
                <strong>IP AUDIT:</strong> {{ $blueprint->signer_ip ?: 'Recorded' }}
            @endif
        </div>
    </div>

    <div class="footer">
        Dihasilkan oleh Neriah Pro Blueprint Engine &bull; Dokumen Mengikat Secara Hukum &bull; neriahpro.com
    </div>
</body>
</html>
