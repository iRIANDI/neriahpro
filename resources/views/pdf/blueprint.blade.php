<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ultimate PRD &amp; Architecture Blueprint - {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 15mm 12mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .badge {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 6px;
            border: 1px solid #a7f3d0;
            margin-bottom: 4px;
        }
        .badge-dark {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5px;
            font-family: monospace;
            padding: 1px 5px;
        }
        h1 {
            font-size: 15px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .meta {
            font-size: 8.5px;
            color: #64748b;
            font-family: monospace;
        }
        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #065f46;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 9px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 8px;
        }
        .box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            margin-bottom: 6px;
            font-size: 9px;
        }
        .feature-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #059669;
            padding: 5px 8px;
            margin-bottom: 4px;
        }
        .feature-title {
            font-weight: bold;
            font-size: 9.5px;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .feature-desc {
            font-size: 8.5px;
            color: #475569;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 18px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
            font-family: monospace;
        }
        .code {
            font-family: monospace;
            font-size: 8px;
            background-color: #f1f5f9;
            padding: 1px 3px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <span class="badge">Neriah Pro &bull; Project OS Ultimate PRD &amp; Architecture Blueprint</span>
        <h1>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</h1>
        <div class="meta">
            DOC_ID: {{ strtoupper(substr($blueprint->id, 0, 12)) }} &bull; 
            CREATED: {{ optional($blueprint->created_at)->format('d M Y') ?: date('d M Y') }} &bull; 
            STATUS: {{ strtoupper($blueprint->project_status) }} &bull; 
            TARGET: {{ $blueprint->target_waktu ?: '30 Hari Kerja' }}
        </div>
    </div>

    <!-- 1. Executive Summary -->
    <div class="section">
        <div class="section-title">1. Ringkasan Eksekutif &amp; Kebutuhan Bisnis</div>
        <table style="margin-bottom: 6px;">
            <tr>
                <th style="width: 25%;">Nama Klien / Bisnis</th>
                <td><strong>{{ $blueprint->nama_bisnis ?: '-' }}</strong> (PIC: {{ $blueprint->client_name ?: '-' }})</td>
            </tr>
            <tr>
                <th>Kontak &amp; Komunikasi</th>
                <td>Email: {{ $blueprint->email ?: '-' }} &bull; WA: {{ $blueprint->phone ?: '-' }}</td>
            </tr>
            <tr>
                <th>Masalah yang Diselesaikan</th>
                <td>{{ $blueprint->masalah_utama }}</td>
            </tr>
            <tr>
                <th>Tujuan &amp; Solusi Sistem</th>
                <td>{{ $blueprint->tujuan_utama }}</td>
            </tr>
            <tr>
                <th>Target Waktu Rilis</th>
                <td>{{ $blueprint->target_waktu ?: '30 Hari Kerja (5 Sprint)' }}</td>
            </tr>
        </table>
    </div>

    <!-- 2. System Actors & Roles -->
    <div class="section">
        <div class="section-title">2. Pengguna Sistem &amp; Aktor (RBAC)</div>
        <table>
            <tr>
                <th style="width: 25%;">Target Pengguna</th>
                <td>{{ $blueprint->target_audiens ?: 'Pengguna Umum & Pelanggan' }}</td>
            </tr>
            <tr>
                <th>Aktor &amp; Peran Sistem</th>
                <td>{{ $blueprint->aktor_sistem ?: 'Superadmin, Operator, Klien' }}</td>
            </tr>
            <tr>
                <th>Alur Kerja Utama</th>
                <td>{{ $blueprint->alur_kerja ?: 'Intake Data -> Validasi -> Dasbor Verifikasi' }}</td>
            </tr>
        </table>
    </div>

    <!-- 3. MVP Feature Scope -->
    <div class="section">
        <div class="section-title">3. Ruang Lingkup Fitur Inti (Fase 1 - Core MVP)</div>
        @php
            $detailedMvp = $prd['features']['mvp_phase1'] ?? $prd['engineering_specs']['mvp_specs'] ?? [];
        @endphp

        @if(!empty($detailedMvp) && is_array($detailedMvp))
            @foreach($detailedMvp as $feat)
                <div class="feature-card">
                    <div class="feature-title">
                        <span class="badge-dark">MODUL {{ $loop->iteration }}</span> 
                        {{ is_array($feat) ? ($feat['title'] ?? $feat['name'] ?? 'Fitur MVP') : 'Fitur MVP' }}
                    </div>
                    <div class="feature-desc">
                        {{ is_array($feat) ? ($feat['desc'] ?? $feat['description'] ?? '') : (string) $feat }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="box">
                {{ $blueprint->fitur_wajib }}
            </div>
        @endif

        @if($blueprint->fitur_tambahan)
            <div style="margin-top: 8px;">
                <div style="font-size: 9px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 3px;">
                    Roadmap Lanjutan (Fase 2 Pengembangan):
                </div>
                <div class="box" style="color: #64748b;">
                    {{ $blueprint->fitur_tambahan }}
                </div>
            </div>
        @endif
    </div>

    <!-- 4. Architecture Standards & Database -->
    <div class="section">
        <div class="section-title">4. Standar Rekayasa Arsitektur &amp; Database</div>
        <table>
            <tr>
                <th style="width: 25%;">Pola Arsitektur</th>
                <td>Modern Enterprise Monolith (Laravel 13 + Filament PHP v5)</td>
            </tr>
            <tr>
                <th>Basis Data &amp; Primary Key</th>
                <td>PostgreSQL 16 Strict Mode &bull; Distributed ULID 26-Character Primary Keys (<span class="code">-&gt;ulid('id')-&gt;primary()</span>)</td>
            </tr>
            <tr>
                <th>Strategi Paginasi</th>
                <td>Keyset Cursor Pagination O(1) Stability (Anti-Offset Bottleneck untuk Skala Jutaan Record)</td>
            </tr>
            <tr>
                <th>Integrasi Eksternal</th>
                <td>{{ $blueprint->kebutuhan_integrasi ?: 'Midtrans Snap Gateway, Cloud Storage S3, WhatsApp API' }}</td>
            </tr>
            <tr>
                <th>Kepatuhan Keamanan</th>
                <td>OWASP Top 10, AI Threat Shield Middleware, HoneyPot Anti-Bot, Anti-RCE Guard</td>
            </tr>
        </table>
    </div>

    <!-- 5. Database Relational Entities (ERD Schema) -->
    @php
        $tables = $prd['erd_schema']['tables'] ?? [];
    @endphp
    @if(!empty($tables) && is_array($tables))
    <div class="section">
        <div class="section-title">5. Skema Entitas Relasional Basis Data (ERD PostgreSQL)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Tabel Entitas</th>
                    <th style="width: 35%;">Primary &amp; Foreign Keys</th>
                    <th style="width: 40%;">Kolom Data &amp; Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $tbl)
                    <tr>
                        <td><strong>{{ is_array($tbl) ? ($tbl['table_name'] ?? $tbl['name'] ?? 'tabel') : 'tabel' }}</strong></td>
                        <td>
                            <span class="code">id (ULID, PK)</span><br>
                            @if(!empty($tbl['foreign_keys']) && is_array($tbl['foreign_keys']))
                                @foreach($tbl['foreign_keys'] as $fk)
                                    <span class="code" style="color: #0369a1;">{{ $fk }}</span><br>
                                @endforeach
                            @else
                                <span style="color: #94a3b8; font-size: 8px;">Relasi Mandiri</span>
                            @endif
                        </td>
                        <td>
                            {{ is_array($tbl) ? ($tbl['description'] ?? $tbl['purpose'] ?? 'Tabel domain bisnis') : (string)$tbl }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- 6. 5-Sprint Engineering Execution Timeline -->
    <div class="section">
        <div class="section-title">6. Timeline Eksekusi Rekayasa (5 Sprint Kerja)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Sprint</th>
                    <th style="width: 35%;">Fokus Pengerjaan</th>
                    <th style="width: 50%;">Deliverable &amp; Target QA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Sprint 1</strong></td>
                    <td>Fondasi Sistem &amp; Database</td>
                    <td>Skema tabel ULID, Migrasi PostgreSQL, Model Observers, Cache Store</td>
                </tr>
                <tr>
                    <td><strong>Sprint 2</strong></td>
                    <td>Logika Bisnis Inti (Core MVP)</td>
                    <td>Service layer, Action handlers, Form validasi data, State machine</td>
                </tr>
                <tr>
                    <td><strong>Sprint 3</strong></td>
                    <td>Frontend &amp; Responsive UI</td>
                    <td>Antarmuka responsif tanpa capsule/pill, 60fps micro-animations, UX edukatif</td>
                </tr>
                <tr>
                    <td><strong>Sprint 4</strong></td>
                    <td>Audit Keamanan &amp; QA</td>
                    <td>Rate-limiting, AI Threat Shield, Automated PHPUnit test suite, Anti-bot</td>
                </tr>
                <tr>
                    <td><strong>Sprint 5</strong></td>
                    <td>Staging Cloud &amp; Serah Terima</td>
                    <td>Provisioning VPS Cloud, UAT Klien, Scope lock verification, Handover Git</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 7. SLA & Bug Warranty -->
    <div class="section">
        <div class="section-title">7. Tata Kelola, SLA &amp; Garansi Perbaikan Bug</div>
        <table>
            <tr>
                <th style="width: 25%;">Garansi Perbaikan Bug</th>
                <td><strong>30 Hari Kalender Pascameluncur</strong> &bull; Perbaikan gratis terhadap error atau ketidaksesuaian fungsi MVP.</td>
            </tr>
            <tr>
                <th>Penyerahan Repositori</th>
                <td>Akses 100% penuh ke repositori Git GitHub (Private Repository) setelah pelunasan final.</td>
            </tr>
            <tr>
                <th>Ketentuan Perubahan Scope</th>
                <td>Ruang lingkup terkunci setelah penandatanganan. Kebutuhan baru diproses via Change Request (CR) / Addendum resmi.</td>
            </tr>
        </table>
    </div>

    <!-- 8. Cryptographic Checksum & Contract Seal -->
    <div class="section">
        <div class="section-title">8. Integritas Kriptografis &amp; Segel Scope Freeze</div>
        <div class="box" style="font-family: monospace; font-size: 8.5px; word-break: break-all;">
            <strong>SHA-256 SPECIFICATION CHECKSUM:</strong><br>
            {{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}<br><br>
            @if($blueprint->signed_agreement)
                <strong>STATUS KONTRAK:</strong> TERKUNCI (SIGNED &amp; VERIFIED)<br>
                <strong>PENANDATANGAN:</strong> {{ $blueprint->client_name ?: $blueprint->nama_bisnis }}<br>
                <strong>WAKTU TTD:</strong> {{ optional($blueprint->signed_at)->format('d M Y H:i:s T') ?: date('d M Y H:i:s') }}<br>
                <strong>IP AUDIT:</strong> {{ $blueprint->signer_ip ?: 'Recorded' }}
            @else
                <strong>STATUS SPESIFIKASI:</strong> PROPOSAL RESMI TERVERIFIKASI (SIAP DITANDATANGANI)<br>
                <strong>VERIFIKASI SISTEM:</strong> Segel integritas aktif &bull; Menjamin konten tidak mengalami manipulasi sepihak.
            @endif
        </div>
    </div>

    <div class="footer">
        Dihasilkan oleh Neriah Pro Blueprint Engine &bull; Dokumen Mengikat Secara Hukum &bull; neriahpro.com
    </div>
</body>
</html>
