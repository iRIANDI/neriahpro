@php
    $businessName = !empty(trim((string)$blueprint->nama_bisnis)) ? $blueprint->nama_bisnis : (!empty(trim((string)$blueprint->client_name)) ? $blueprint->client_name : 'Enterprise Digital System');
    $clientPic = !empty(trim((string)$blueprint->client_name)) ? $blueprint->client_name : $businessName;
    $email = !empty(trim((string)$blueprint->email)) ? $blueprint->email : \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com');
    $phone = !empty(trim((string)$blueprint->phone)) ? $blueprint->phone : '-';
    
    $rawMasalah = trim((string)$blueprint->masalah_utama);
    $masalah = !empty($rawMasalah) 
        ? $rawMasalah 
        : "Otomatisasi proses bisnis dan administrasi operasional {$businessName}, eliminasi duplikasi input data manual, dan sentralisasi basis data transaksi organisasi.";
    
    $rawTujuan = trim((string)$blueprint->tujuan_utama);
    $tujuan = !empty($rawTujuan) 
        ? $rawTujuan 
        : "Meningkatkan efisiensi kerja tim operasional {$businessName}, meminimalisir kesalahan pelaporan, dan menyediakan platform digital terpadu yang andal dan scalable.";
    
    $targetWaktu = !empty(trim((string)$blueprint->target_waktu)) ? $blueprint->target_waktu : '14-30 Hari Kerja (Phase 1 MVP)';
    $targetAudiens = !empty(trim((string)$blueprint->target_audiens)) ? $blueprint->target_audiens : 'Pengguna internal organisasi, staf operasional, tim manajemen, dan pelanggan publik.';
    $aktor = !empty(trim((string)$blueprint->aktor_sistem)) ? $blueprint->aktor_sistem : 'Superadmin (Full Control), Operator/Staff (Input & Validasi), Klien/Pelanggan (Self-Service).';
    $alurKerja = !empty(trim((string)$blueprint->alur_kerja)) ? $blueprint->alur_kerja : 'Pengguna Mengakses Sistem -> Input Data Tervalidasi -> Pemrosesan State Machine -> Notifikasi & Verifikasi di Dasbor.';
    $fiturWajib = !empty(trim((string)$blueprint->fitur_wajib)) ? $blueprint->fitur_wajib : 'Autentikasi Aman & RBAC, Formulir Intake Tervalidasi, Dasbor Administrasi Filament v5, Rekapitulasi Data & Ekspor PDF/Excel.';
    $fiturTambahan = !empty(trim((string)$blueprint->fitur_tambahan)) ? $blueprint->fitur_tambahan : 'Notifikasi Otomatis WhatsApp/Email E.164, Audit Trail Activity Log, Dual Theme Dark/Light Mode.';
    $integrasi = !empty(trim((string)$blueprint->kebutuhan_integrasi)) ? $blueprint->kebutuhan_integrasi : 'Midtrans Snap Payment Gateway, Cloud Storage S3, WhatsApp API Gateway.';

    $targetPlatform = $blueprint->target_platform ?? ($prd['executive_summary']['target_platform'] ?? 'Responsive Modern Web & PWA');
    $migrasiData = $blueprint->migrasi_data ?? ($prd['executive_summary']['legacy_data_migration'] ?? 'Database Baru Bersih (Clean Start)');
    $preferensiHosting = $blueprint->preferensi_hosting ?? ($prd['executive_summary']['hosting_infrastructure'] ?? 'Managed Cloud VPS Neriah Pro (Hardened Linux)');
    
    $rawOutOfScope = trim((string)($blueprint->out_of_scope ?? ($prd['executive_summary']['out_of_scope'] ?? '')));
    $outOfScope = !empty($rawOutOfScope)
        ? $rawOutOfScope
        : "1. Pembuatan native mobile iOS/Android di luar Progressive Web App (PWA) responsif.\n2. Migrasi data tidak berstruktur tanpa format baku CSV/XLSX resmi.\n3. Custom payment gateway proprietary di luar integrasi resmi Midtrans Snap.\n4. Integrasi pihak ketiga berbayar tanpa penyediaan API key aktif dari pihak klien.";

    $detailedMvp = $prd['features']['mvp_phase1'] ?? $prd['engineering_specs']['mvp_specs'] ?? [];
    if (empty($detailedMvp) || !is_array($detailedMvp)) {
        $detailedMvp = [
            ['title' => 'Modul 1: Autentikasi & RBAC Strict Mode', 'desc' => 'Manajemen hak akses multi-role dengan proteksi brute-force, CSRF Shield, dan identitas ULID 26 karakter.'],
            ['title' => 'Modul 2: Core Data Intake & Form Validasi', 'desc' => 'Formulir pengumpulan data tervalidasi terpusat dengan proteksi Anti-Spam HoneyPot, sanitasi XSS, dan unggah berkas Curator.'],
            ['title' => 'Modul 3: Dasbor Administrasi Filament v5', 'desc' => 'Pusat kendali operasional berbasis Filament PHP v5 dengan tabel filter data instan, pencarian berindeks O(1), dan Keyset Cursor Pagination.'],
            ['title' => 'Modul 4: Engine Rekonsiliasi & Ekspor Laporan', 'desc' => 'Fitur rekap data transaksi dan operasional otomatis dengan dukungan cetak PDF resmi berstandar A4 dan ekspor spreadsheet XLSX.'],
        ];
    }

    $tables = $prd['erd_schema']['tables'] ?? [];
    if (empty($tables) || !is_array($tables)) {
        $tables = [
            [
                'table_name' => 'users',
                'foreign_keys' => [],
                'description' => 'Tabel akun otentikasi dengan ULID PK, role granular, dan audit metadata.'
            ],
            [
                'table_name' => 'domain_records',
                'foreign_keys' => ['user_id (users.id)'],
                'description' => 'Entitas utama pencatatan data transaksi dan berkas operasional bisnis.'
            ],
            [
                'table_name' => 'transactions',
                'foreign_keys' => ['user_id (users.id)'],
                'description' => 'Log transaksi pembayaran gateway Midtrans dengan proteksi idempotency dan SHA-256 seal.'
            ],
            [
                'table_name' => 'activity_logs',
                'foreign_keys' => ['user_id (users.id)'],
                'description' => 'Jejak audit forensik immutable mencatat seluruh aktivitas perubahan data.'
            ]
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ultimate PRD &amp; Architecture Blueprint - {{ $businessName }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 9mm 11mm 12mm 11mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 6px;
            border: 1px solid #a7f3d0;
            margin-bottom: 3px;
        }
        .badge-dark {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7px;
            font-family: monospace;
            padding: 1.5px 5px;
            font-weight: bold;
        }
        h1 {
            font-size: 13.5px;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: -0.3px;
        }
        .meta {
            font-size: 7.5px;
            color: #64748b;
            font-family: monospace;
        }
        .section {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #065f46;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 3.5px 5px;
            font-size: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 7.5px;
        }
        .box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            margin-bottom: 4px;
            font-size: 8px;
        }
        .feature-card {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #059669;
            padding: 5px 7px;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }
        .feature-title {
            font-weight: bold;
            font-size: 8.5px;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .feature-desc {
            font-size: 7.5px;
            color: #334155;
            line-height: 1.35;
        }
        .gherkin-box {
            background-color: #f8fafc;
            border-left: 2px solid #10b981;
            padding: 2.5px 5px;
            margin: 2px 0;
            font-family: monospace;
            font-size: 7px;
            line-height: 1.3;
        }
        .gherkin-given { color: #047857; font-weight: bold; }
        .gherkin-when { color: #0284c7; font-weight: bold; }
        .gherkin-then { color: #b45309; font-weight: bold; }
        .mini-badge {
            display: inline-block;
            font-size: 6.5px;
            font-weight: bold;
            padding: 1px 4px;
            background: #e2e8f0;
            color: #334155;
            text-transform: uppercase;
            margin-left: 3px;
        }
        .target-file-badge {
            display: inline-block;
            font-family: monospace;
            font-size: 6.5px;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 1px 3px;
            margin: 1px 1px;
        }
        .agent-prompt-box {
            background-color: #0f172a;
            color: #e2e8f0;
            font-family: monospace;
            font-size: 6.5px;
            padding: 3px 5px;
            line-height: 1.3;
            margin-top: 3px;
            word-break: break-all;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 14px;
            font-size: 7px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #cbd5e1;
            padding-top: 2px;
            font-family: monospace;
        }
        .code {
            font-family: monospace;
            font-size: 7px;
            background-color: #f1f5f9;
            padding: 1px 3px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <span class="badge">Neriah Pro &bull; Project OS Ultimate PRD &amp; Architecture Blueprint</span>
        <h1>{{ $businessName }}</h1>
        <div class="meta">
            DOC_ID: {{ strtoupper(substr($blueprint->id, 0, 14)) }} &bull; 
            CREATED: {{ optional($blueprint->created_at)->format('d M Y') ?: date('d M Y') }} &bull; 
            STATUS: {{ strtoupper($blueprint->project_status ?: 'Active License') }} &bull; 
            RELEASE_TARGET: {{ $targetWaktu }} &bull;
            TRACK: {{ strtoupper($blueprint->tipe_aplikasi ?: 'Studio Turnkey MVP') }}
        </div>
    </div>

    <!-- 1. Executive Summary & 26 Parameter Discovery -->
    <div class="section">
        <div class="section-title">1. Ringkasan Eksekutif &amp; Analisis Kebutuhan Bisnis</div>
        <table>
            <tr>
                <th style="width: 25%;">Nama Klien / Bisnis</th>
                <td><strong>{{ $businessName }}</strong> (PIC: {{ $clientPic }})</td>
            </tr>
            <tr>
                <th>Kontak &amp; Komunikasi</th>
                <td>Email: {{ $email }} &bull; Telepon/WA: {{ $phone }}</td>
            </tr>
            <tr>
                <th>Masalah Utama Bisnis</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($masalah, true, true) !!}</td>
            </tr>
            <tr>
                <th>Tujuan &amp; Solusi Sistem</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($tujuan, true, true) !!}</td>
            </tr>
            <tr>
                <th>Target Waktu Rilis</th>
                <td><strong>{{ $targetWaktu }}</strong></td>
            </tr>
            <tr>
                <th>Target Platform</th>
                <td>{{ $targetPlatform }}</td>
            </tr>
            <tr>
                <th>Batasan Negatif (Out of Scope)</th>
                <td style="color: #991b1b; background-color: #fef2f2;">
                    {!! \App\Support\BlueprintPdfHelper::formatPoints($outOfScope, true, true) !!}
                </td>
            </tr>
            <tr>
                <th>Infrastruktur Hosting &amp; Migrasi</th>
                <td>
                    <strong>Hosting:</strong> {{ $preferensiHosting }} &bull; 
                    <strong>Migrasi:</strong> {{ $migrasiData }}
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. System Actors & Roles -->
    <div class="section">
        <div class="section-title">2. Pengguna Sistem, Aktor (RBAC) &amp; Alur Kerja Utama</div>
        <table>
            <tr>
                <th style="width: 25%;">Target Pengguna</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($targetAudiens, false, true) !!}</td>
            </tr>
            <tr>
                <th>Aktor &amp; Peran Sistem</th>
                <td>
                    @php
                        $actorsList = $prd['system_actors'] ?? [];
                        $isGranular = is_array($actorsList) && count($actorsList) > 1 && !preg_match('/\d+[\.\)]/u', $actorsList[0]['name'] ?? '');
                    @endphp
                    @if($isGranular)
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0;">
                            @foreach($actorsList as $act)
                                @php
                                    $actName = $act['name'] ?? $act['title'] ?? 'Aktor';
                                    $actRole = $act['role'] ?? $act['desc'] ?? '';
                                    $actPerms = $act['permissions'] ?? [];
                                @endphp
                                <tr>
                                    <td style="width: 28%; border: none; padding: 2px 4px 2px 0; font-weight: bold; color: #0f172a; font-size: 7.5px;">
                                        &bull; {{ $actName }}
                                    </td>
                                    <td style="border: none; padding: 2px 0; font-size: 7.5px; color: #334155;">
                                        {{ $actRole }}
                                        @if(!empty($actPerms))
                                            <div style="font-size: 6.5px; color: #047857; font-family: monospace; margin-top: 1px;">
                                                Izin: {{ implode(', ', (array)$actPerms) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        {!! \App\Support\BlueprintPdfHelper::formatPoints($aktor, true, true) !!}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Alur Kerja Utama (Core Workflows)</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($alurKerja, true, true) !!}</td>
            </tr>
        </table>
    </div>

    <!-- 3. MVP Feature Scope & 6 Engineering Dimensions -->
    <div class="section">
        <div class="section-title">3. Ruang Lingkup Fitur Inti &amp; Spesifikasi Rekayasa (Fase 1 - Core MVP)</div>
        @foreach($detailedMvp as $feat)
            @php
                $featTitle = is_array($feat) ? ($feat['title'] ?? $feat['name'] ?? 'Fitur Inti MVP') : 'Fitur Inti MVP';
                $featUserStory = is_array($feat) ? ($feat['user_story'] ?? '') : '';
                $featDesc = is_array($feat) ? ($feat['desc'] ?? $feat['description'] ?? '') : (string)$feat;
                $featAc = is_array($feat) ? ($feat['acceptance_criteria'] ?? []) : [];
                $normAc = \App\Services\PrdGeneratorService::normalizeAcceptanceCriteria($featAc, $featTitle, $featUserStory);
                $featFe = is_array($feat) ? ($feat['frontend'] ?? []) : [];
                $featBe = is_array($feat) ? ($feat['backend'] ?? []) : [];
                $featApi = is_array($feat) ? ($feat['integration'] ?? ($feat['api_contracts'] ?? [])) : [];
                $featFiles = is_array($feat) ? ($feat['target_files'] ?? ($feat['bounded_target_files'] ?? [])) : [];
                $featDirective = is_array($feat) ? ($feat['code_agent_directive'] ?? ($feat['agent_prompt'] ?? '')) : '';
            @endphp
            <div class="feature-card">
                <div class="feature-title">
                    <span class="badge-dark">MODUL {{ $loop->iteration }}</span> 
                    <strong>{{ $featTitle }}</strong>
                    @if(!empty($feat['category_label'] ?? $feat['category'] ?? null))
                        <span class="mini-badge">{{ $feat['category_label'] ?? $feat['category'] }}</span>
                    @endif
                </div>
                <div class="feature-desc" style="margin-bottom: 2px;">
                    {{ $featDesc }}
                </div>
                @if(!empty($featUserStory))
                    <div style="font-size: 7px; font-style: italic; color: #475569; margin-bottom: 2px;">
                        <strong>User Story:</strong> "{{ $featUserStory }}"
                    </div>
                @endif

                @if(!empty($featFiles) && is_array($featFiles))
                    <div style="margin: 2px 0 3px 0;">
                        <span style="font-size: 6.5px; font-weight: bold; color: #b45309; text-transform: uppercase;">Bounded Target Files:</span>
                        @foreach(array_slice($featFiles, 0, 4) as $file)
                            <span class="target-file-badge">{{ $file }}</span>
                        @endforeach
                    </div>
                @endif

                @if(!empty($normAc))
                    <div style="font-size: 7px; font-weight: bold; color: #065f46; margin-top: 2px; text-transform: uppercase;">
                        Acceptance Criteria (Gherkin Scenarios):
                    </div>
                    @foreach($normAc as $acIdx => $ac)
                        <div class="gherkin-box">
                            <strong>{{ $ac['scenario'] ?? ('Skenario ' . ($acIdx + 1)) }}:</strong><br>
                            <span class="gherkin-given">GIVEN:</span> {{ (!empty($ac['given']) && $ac['given'] !== '-') ? $ac['given'] : \App\Services\PrdGeneratorService::deriveGivenCondition($ac['scenario'] ?? '', $featTitle, $acIdx) }}<br>
                            <span class="gherkin-when">WHEN:</span> {{ (!empty($ac['when']) && $ac['when'] !== '-') ? $ac['when'] : \App\Services\PrdGeneratorService::deriveWhenCondition($ac['scenario'] ?? '', $featTitle, $acIdx) }}<br>
                            <span class="gherkin-then">THEN:</span> {{ (!empty($ac['then']) && $ac['then'] !== '-') ? $ac['then'] : \App\Services\PrdGeneratorService::deriveThenCondition($ac['scenario'] ?? '', $featTitle, $acIdx) }}
                        </div>
                    @endforeach
                @endif

                @if(!empty($featFe['tasks']) || !empty($featBe['tasks']) || !empty($featApi['endpoint']))
                    <table style="margin-top: 3px; font-size: 7px; margin-bottom: 2px;">
                        <tr>
                            @if(!empty($featFe))
                                <td style="width: 33%; vertical-align: top;">
                                    <strong style="color: #047857;">Frontend (Anti-AI-Slop):</strong><br>
                                    @if(is_array($featFe['tasks'] ?? null))
                                        @foreach(array_slice($featFe['tasks'], 0, 3) as $t)
                                            &bull; {{ $t }}<br>
                                        @endforeach
                                    @else
                                        {{ $featFe['components'] ?? 'Blade / Alpine.js UI' }}
                                    @endif
                                </td>
                            @endif
                            @if(!empty($featBe))
                                <td style="width: 33%; vertical-align: top;">
                                    <strong style="color: #047857;">Backend (Strict ULID &amp; O(1)):</strong><br>
                                    @if(is_array($featBe['tasks'] ?? null))
                                        @foreach(array_slice($featBe['tasks'], 0, 3) as $t)
                                            &bull; {{ $t }}<br>
                                        @endforeach
                                    @else
                                        {{ $featBe['model_and_migration'] ?? 'PostgreSQL Strict ULID Service' }}
                                    @endif
                                </td>
                            @endif
                            @if(!empty($featApi['endpoint']))
                                <td style="width: 34%; vertical-align: top;">
                                    <strong style="color: #047857;">API Contract:</strong><br>
                                    <span class="code">{{ $featApi['endpoint'] }}</span><br>
                                    <span style="color: #64748b;">Auth: Bearer ULID &bull; 200 OK</span>
                                </td>
                            @endif
                        </tr>
                    </table>
                @endif

                @if(!empty($featDirective))
                    <div class="agent-prompt-box">
                        <strong>AI AGENT DIRECTIVE:</strong><br>
                        {{ Str::limit(is_string($featDirective) ? $featDirective : json_encode($featDirective), 220) }}
                    </div>
                @endif
            </div>
        @endforeach

        @if($fiturTambahan)
            <div style="margin-top: 4px;">
                <div style="font-size: 7.5px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 2px;">
                    Roadmap Lanjutan (Fase 2 Pengembangan):
                </div>
                <div class="box" style="color: #475569;">
                    {!! \App\Support\BlueprintPdfHelper::formatPoints($fiturTambahan, true, true) !!}
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
                <td>Modern Enterprise Monolith (Laravel 13 + Filament PHP v5 + Livewire 4)</td>
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
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($integrasi, false, true) !!}</td>
            </tr>
            <tr>
                <th>Kepatuhan Keamanan &amp; UU PDP</th>
                <td>OWASP Top 10, AI Threat Shield Middleware, HoneyPot Anti-Bot, Anti-RCE Guard, CSRF Shield, Sanitasi XSS</td>
            </tr>
        </table>
    </div>

    <!-- 5. Database Relational Entities (ERD PostgreSQL) -->
    <div class="section">
        <div class="section-title">5. Skema Entitas Relasional Basis Data (ERD PostgreSQL)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Tabel Entitas</th>
                    <th style="width: 25%;">Primary &amp; Foreign Keys</th>
                    <th style="width: 55%;">Kolom Basis Data &amp; Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $tbl)
                    @php
                        $tName = is_array($tbl) ? ($tbl['name'] ?? $tbl['table_name'] ?? 'tabel') : 'tabel';
                        $tPk = is_array($tbl) ? ($tbl['primary_key'] ?? ($tbl['pk'] ?? 'id (ULID, PK)')) : 'id (ULID, PK)';
                        $tFks = is_array($tbl) ? ($tbl['foreign_keys'] ?? []) : [];
                        $tCols = is_array($tbl) ? ($tbl['columns'] ?? []) : [];
                        $tDesc = is_array($tbl) ? ($tbl['description'] ?? $tbl['purpose'] ?? '') : (string)$tbl;
                    @endphp
                    <tr>
                        <td><strong>{{ $tName }}</strong></td>
                        <td>
                            <span class="code">{{ $tPk }}</span><br>
                            @if(!empty($tFks) && is_array($tFks))
                                @foreach($tFks as $fk)
                                    <span class="code" style="color: #0369a1;">{{ $fk }}</span><br>
                                @endforeach
                            @else
                                <span style="color: #94a3b8; font-size: 7px;">Relasi Mandiri</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($tDesc))
                                <div style="font-size: 7.5px; color: #475569; margin-bottom: 2px;">{{ $tDesc }}</div>
                            @endif
                            @if(!empty($tCols))
                                <div style="font-size: 6.5px; font-family: monospace; color: #1e293b; line-height: 1.25;">
                                    @foreach(array_slice($tCols, 0, 10) as $c)
                                        <span class="code">{{ $c['name'] ?? 'col' }}</span> ({{ $c['type'] ?? 'varchar' }}){{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                    @if(count($tCols) > 10)
                                        <span style="color: #64748b;">+{{ count($tCols) - 10 }} kolom lainnya</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
                    <td>Rate-limiting, AI Threat Shield, Automated Pest test suite, Anti-bot</td>
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

    <!-- 8. Cloud Infrastructure & Docker Containerization -->
    <div class="section">
        <div class="section-title">8. Topologi Infrastruktur Cloud &amp; Kontainerisasi Docker</div>
        <table>
            <tr>
                <th style="width: 25%;">Runtime &amp; PHP Engine</th>
                <td>PHP 8.5+ Hardened &bull; Railpack FrankenPHP / PHP-FPM High Performance</td>
            </tr>
            <tr>
                <th>Basis Data &amp; Cache</th>
                <td>PostgreSQL 16+ Strict Mode &bull; Redis 7 (In-Memory Cache &amp; Idempotency Vault)</td>
            </tr>
            <tr>
                <th>Web Server &amp; SSL</th>
                <td>Nginx Hardened Reverse Proxy &bull; TLS 1.3 &bull; Let's Encrypt Auto-Renewal SSL</td>
            </tr>
            <tr>
                <th>Arsitektur Kontainer</th>
                <td>docker-compose.yml terisolasi (app, db, redis, web, worker queue) &bull; Zero Drift</td>
            </tr>
        </table>
    </div>

    <!-- 9. 10-Pillar Software Factory OS Matrix -->
    <div class="section">
        <div class="section-title">9. Matriks 10 Pilar Software Factory OS (100% Turnkey Maturity)</div>
        <table>
            <tr>
                <th style="width: 25%;">Pilar 1 - 2</th>
                <td>Zero-Loss Intake &amp; Discovery (MarkItDown OCR) &bull; PRD 26 Parameter &amp; WBS Gantt (OWASP &amp; UU PDP)</td>
            </tr>
            <tr>
                <th>Pilar 3 - 4</th>
                <td>PostgreSQL 16 Strict ULID &amp; Keyset Cursor O(1) &bull; Automated Scaffolding (25+ Files, API V1, OpenAPI 3.0)</td>
            </tr>
            <tr>
                <th>Pilar 5 - 6</th>
                <td>Containerization Docker PHP 8.5+, PG 16, Redis 7 &bull; Automated Pest API Testing &amp; 100+ Synthetic Rows</td>
            </tr>
            <tr>
                <th>Pilar 7 - 8</th>
                <td>CI/CD Pipeline Automated deploy.sh 6-Scenario &bull; Legal Contract &amp; Tamper-Proof SHA-256 Scope Lock</td>
            </tr>
            <tr>
                <th>Pilar 9 - 10</th>
                <td>Managed Sprint Capacity (Anti-Collision 2-3 Batch) &bull; Client Transparency Portal &amp; Expiry Alert</td>
            </tr>
        </table>
    </div>

    <!-- 10. Cryptographic Checksum & Contract Seal -->
    <div class="section">
        <div class="section-title">10. Integritas Kriptografis &amp; Segel Scope Freeze</div>
        <div class="box" style="font-family: monospace; font-size: 7.5px; word-break: break-all;">
            <strong>SHA-256 SPECIFICATION CHECKSUM:</strong><br>
            {{ $blueprint->document_sha256 ?: hash('sha256', $blueprint->id . $businessName . $blueprint->created_at) }}<br><br>
            @if($blueprint->signed_agreement)
                <strong>STATUS KONTRAK:</strong> TERKUNCI (SIGNED &amp; VERIFIED)<br>
                <strong>PENANDATANGAN:</strong> {{ $clientPic }}<br>
                <strong>WAKTU TTD:</strong> {{ optional($blueprint->signed_at)->format('d M Y H:i:s T') ?: date('d M Y H:i:s') }}<br>
                <strong>IP AUDIT:</strong> {{ $blueprint->signer_ip ?: 'Recorded' }}
            @else
                <strong>STATUS SPESIFIKASI:</strong> LISENSI RESMI TERVERIFIKASI (HAK CIPTA NERIAH PRO)<br>
                <strong>VERIFIKASI SISTEM:</strong> Segel integritas aktif &bull; Menjamin konten otentik dan terdaftar di neriahpro.com.
            @endif
        </div>
    </div>

    <div class="footer">
        Dihasilkan oleh Neriah Pro Blueprint Engine &bull; Dokumen Mengikat Secara Hukum &bull; neriahpro.com
    </div>
</body>
</html>
