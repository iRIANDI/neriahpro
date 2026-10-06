<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ultimate PRD - {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; line-height: 1.45; color: #1f2937; margin: 0; padding: 0; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 15px; }
        .badge { display: inline-block; background-color: #ecfdf5; color: #047857; font-size: 8.5px; font-weight: bold; text-transform: uppercase; padding: 2px 6px; margin-bottom: 4px; }
        h1 { font-size: 16px; margin: 0 0 3px 0; text-transform: uppercase; color: #111827; }
        .meta { font-size: 9px; color: #6b7280; font-family: monospace; }
        .section { margin-bottom: 15px; page-break-inside: avoid; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #065f46; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 7px; font-size: 9.5px; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; color: #374151; }
        .box { background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 8px 10px; margin-bottom: 8px; font-size: 10px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 22px; font-size: 8.5px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 4px; font-family: monospace; }
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
            <strong>Masalah Utama yang Diselesaikan:</strong><br>
            {{ $blueprint->masalah_utama }}
        </div>
        <div class="box">
            <strong>Tujuan Bisnis &amp; Solusi Sistem:</strong><br>
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
                <td>{{ $blueprint->aktor_sistem ?: 'Superadmin, Operator, Client' }}</td>
            </tr>
            <tr>
                <th>Alur Kerja Inti</th>
                <td>{{ $blueprint->alur_kerja ?: 'Intake Data -> Validasi -> Dasbor Verifikasi' }}</td>
            </tr>
        </table>
    </div>

    <!-- Fitur & Spesifikasi Teknis -->
    <div class="section">
        <div class="section-title">3. Ruang Lingkup Fitur (MVP Scope)</div>
        <div class="box">
            <strong>Fitur Wajib (Fase 1 / Core MVP):</strong><br>
            {{ $blueprint->fitur_wajib }}
        </div>
        @if($blueprint->fitur_tambahan)
            <div class="box">
                <strong>Fitur Tambahan (Fase 2 / Pengembangan):</strong><br>
                {{ $blueprint->fitur_tambahan }}
            </div>
        @endif
    </div>

    <!-- Standar Rekayasa & Tech Stack -->
    <div class="section">
        <div class="section-title">4. Standar Rekayasa Arsitektur &amp; Database</div>
        <table>
            <tr>
                <th style="width: 30%;">Arsitektur Monolith</th>
                <td>Laravel 13 Modern Monolith + Filament v5 Enterprise Panel</td>
            </tr>
            <tr>
                <th>Primary Key &amp; Database</th>
                <td>PostgreSQL 16 Strict Mode &bull; Distributed ULID 26-Character Primary Keys</td>
            </tr>
            <tr>
                <th>Strategi Paginasi</th>
                <td>Keyset Cursor Pagination O(1) Stability (Anti-Offset Bottleneck)</td>
            </tr>
            <tr>
                <th>Integrasi Eksternal</th>
                <td>{{ $blueprint->kebutuhan_integrasi ?: 'Midtrans Payment Gateway, Cloud Storage' }}</td>
            </tr>
            <tr>
                <th>Target Waktu Peluncuran</th>
                <td>{{ $blueprint->target_waktu ?: '30 Hari Kerja' }}</td>
            </tr>
        </table>
    </div>

    <!-- Timeline 5 Sprint Rekayasa -->
    <div class="section">
        <div class="section-title">5. Timeline Eksekusi Rekayasa (5 Sprint Kerja)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Sprint</th>
                    <th style="width: 35%;">Fokus Modul</th>
                    <th style="width: 45%;">Target Deliverable</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Sprint 1</strong></td>
                    <td>Arsitektur &amp; Database</td>
                    <td>Skema tabel ULID, Migrasi PostgreSQL, Model Observers, Cache Store</td>
                </tr>
                <tr>
                    <td><strong>Sprint 2</strong></td>
                    <td>Core Business Logic MVP</td>
                    <td>Service layer, Action handlers, Form validasi intake, State machine</td>
                </tr>
                <tr>
                    <td><strong>Sprint 3</strong></td>
                    <td>Frontend Flow &amp; UI</td>
                    <td>Antarmuka responsif tanpa capsule/pill, Micro-animations 60fps, UX edukatif</td>
                </tr>
                <tr>
                    <td><strong>Sprint 4</strong></td>
                    <td>Security Audit &amp; QA</td>
                    <td>Rate-limiting, AI Threat Shield, Automated PHPUnit test suite, Anti-bot</td>
                </tr>
                <tr>
                    <td><strong>Sprint 5</strong></td>
                    <td>VPS Staging &amp; Serah Terima</td>
                    <td>Provisioning VPS Cloud, UAT Klien, Scope lock verification, Handover Git</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Checksum & Scope Lock -->
    <div class="section">
        <div class="section-title">6. Integritas Kriptografis &amp; Scope Freeze</div>
        <div class="box" style="font-family: monospace; font-size: 9px; word-break: break-all;">
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
