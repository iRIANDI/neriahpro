@php
    $docLocale = $locale ?? (request()->query('locale') ?: (request()->cookie('neriah_locale') ?: (session('locale') ?: ($blueprint->user_metadata['output_locale'] ?? app()->getLocale()))));
    $isEn = in_array(strtolower((string)$docLocale), ['en'], true);

    $translate = function(?string $text) use ($isEn) {
        if (!$isEn || empty($text)) return (string) ($text ?: '');
        return \App\Support\BlueprintPdfHelper::translateText((string)$text, true);
    };

    $businessName = !empty(trim((string)$blueprint->nama_bisnis)) ? $blueprint->nama_bisnis : (!empty(trim((string)$blueprint->client_name)) ? $blueprint->client_name : ($isEn ? 'Enterprise Digital System' : 'Sistem Digital Enterprise'));
    $clientPic = !empty(trim((string)$blueprint->client_name)) ? $blueprint->client_name : $businessName;
    $email = !empty(trim((string)$blueprint->email)) ? $blueprint->email : \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com');
    $phone = !empty(trim((string)$blueprint->phone)) ? $blueprint->phone : '-';
    
    $rawMasalah = trim((string)$blueprint->masalah_utama);
    $masalah = !empty($rawMasalah) 
        ? ($isEn ? $translate($rawMasalah) : $rawMasalah)
        : ($isEn 
            ? "Automation of operational business workflows and administrative processes for {$businessName}, eliminating manual data entry redundancy, and centralizing organizational transaction databases."
            : "Otomatisasi proses bisnis dan administrasi operasional {$businessName}, eliminasi duplikasi input data manual, dan sentralisasi basis data transaksi organisasi.");
    
    $rawTujuan = trim((string)$blueprint->tujuan_utama);
    $tujuan = !empty($rawTujuan) 
        ? ($isEn ? $translate($rawTujuan) : $rawTujuan)
        : ($isEn 
            ? "Increase operational velocity for {$businessName}'s team, eliminate reporting errors, and provide a unified, reliable, and scalable enterprise digital platform."
            : "Meningkatkan efisiensi kerja tim operasional {$businessName}, meminimalisir kesalahan pelaporan, dan menyediakan platform digital terpadu yang andal dan scalable.");
    
    $targetWaktu = !empty(trim((string)$blueprint->target_waktu)) 
        ? ($isEn ? $translate($blueprint->target_waktu) : $blueprint->target_waktu)
        : ($isEn ? '14-30 Business Days (Phase 1 MVP)' : '14-30 Hari Kerja (Phase 1 MVP)');

    $rawTargetAudiens = trim((string)$blueprint->target_audiens);
    $targetAudiens = !empty($rawTargetAudiens) 
        ? ($isEn ? $translate($rawTargetAudiens) : $rawTargetAudiens)
        : ($isEn 
            ? 'Internal organizational staff, operational coordinators, management, and public end-customers.'
            : 'Pengguna internal organisasi, staf operasional, tim manajemen, dan pelanggan publik.');

    $rawAktor = trim((string)$blueprint->aktor_sistem);
    $aktor = !empty($rawAktor) 
        ? ($isEn ? $translate($rawAktor) : $rawAktor)
        : ($isEn 
            ? 'Superadmin (Full Control), Operator/Staff (Input & Validation), Client/Customer (Self-Service).'
            : 'Superadmin (Full Control), Operator/Staff (Input & Validasi), Klien/Pelanggan (Self-Service).');

    $rawAlurKerja = trim((string)$blueprint->alur_kerja);
    $alurKerja = !empty($rawAlurKerja) 
        ? ($isEn ? $translate($rawAlurKerja) : $rawAlurKerja)
        : ($isEn 
            ? 'User accesses system -> Validated data input -> State machine processing -> Instant notification & dashboard verification.'
            : 'Pengguna Mengakses Sistem -> Input Data Tervalidasi -> Pemrosesan State Machine -> Notifikasi & Verifikasi di Dasbor.');

    $rawFiturWajib = trim((string)$blueprint->fitur_wajib);
    $fiturWajib = !empty($rawFiturWajib) 
        ? ($isEn ? $translate($rawFiturWajib) : $rawFiturWajib)
        : ($isEn 
            ? 'Secure RBAC Authentication, Validated Intake Forms, Filament PHP v5 Operational Dashboard, Dynamic Data Ledger & PDF/Excel Export.'
            : 'Autentikasi Aman & RBAC, Formulir Intake Tervalidasi, Dasbor Administrasi Filament v5, Rekapitulasi Data & Ekspor PDF/Excel.');

    $rawFiturTambahan = trim((string)$blueprint->fitur_tambahan);
    $fiturTambahan = !empty($rawFiturTambahan) 
        ? ($isEn ? $translate($rawFiturTambahan) : $rawFiturTambahan)
        : ($isEn 
            ? 'Automated WhatsApp & Email Notifications (E.164), Cryptographic Activity Audit Trail, Dual Dark/Light Mode Theme.'
            : 'Notifikasi Otomatis WhatsApp/Email E.164, Audit Trail Activity Log, Dual Theme Dark/Light Mode.');

    $rawIntegrasi = trim((string)$blueprint->kebutuhan_integrasi);
    $integrasi = !empty($rawIntegrasi) 
        ? ($isEn ? $translate($rawIntegrasi) : $rawIntegrasi)
        : ($isEn 
            ? 'Midtrans Snap Payment Gateway, Amazon S3 Cloud Storage, WhatsApp Business API Gateway.'
            : 'Midtrans Snap Payment Gateway, Cloud Storage S3, WhatsApp API Gateway.');

    $targetPlatform = $blueprint->target_platform ?? ($prd['executive_summary']['target_platform'] ?? 'Responsive Modern Web & PWA');
    $migrasiData = $blueprint->migrasi_data ?? ($prd['executive_summary']['legacy_data_migration'] ?? ($isEn ? 'Clean Start New Database' : 'Database Baru Bersih (Clean Start)'));
    $preferensiHosting = $blueprint->preferensi_hosting ?? ($prd['executive_summary']['hosting_infrastructure'] ?? ($isEn ? 'Managed Dedicated Cloud VPS Neriah Pro (Hardened Linux)' : 'Managed Cloud VPS Neriah Pro (Hardened Linux)'));
    if ($isEn) {
        $targetPlatform = $translate($targetPlatform);
        $migrasiData = $translate($migrasiData);
        $preferensiHosting = $translate($preferensiHosting);
    }
    
    $rawOutOfScope = trim((string)($blueprint->out_of_scope ?? ($prd['executive_summary']['out_of_scope'] ?? '')));
    $outOfScope = !empty($rawOutOfScope)
        ? ($isEn ? $translate($rawOutOfScope) : $rawOutOfScope)
        : ($isEn
            ? "1. Native mobile iOS/Android app development outside responsive Progressive Web App (PWA).\n2. Unstructured legacy data migration without standardized official CSV/XLSX schema.\n3. Custom proprietary payment gateways outside official Midtrans Snap integration.\n4. Paid third-party API services without active API keys provided by the client."
            : "1. Pembuatan native mobile iOS/Android di luar Progressive Web App (PWA) responsif.\n2. Migrasi data tidak berstruktur tanpa format baku CSV/XLSX resmi.\n3. Custom payment gateway proprietary di luar integrasi resmi Midtrans Snap.\n4. Integrasi pihak ketiga berbayar tanpa penyediaan API key aktif dari pihak klien.");

    $detailedMvp = $prd['features']['mvp_phase1'] ?? $prd['engineering_specs']['mvp_specs'] ?? [];
    if (empty($detailedMvp) || !is_array($detailedMvp)) {
        $detailedMvp = $isEn ? [
            ['title' => 'Module 1: Authentication & Strict RBAC Mode', 'desc' => 'Multi-role permission access control with brute-force protection, CSRF Shield, and 26-character ULID identities.'],
            ['title' => 'Module 2: Core Data Intake & Validation Forms', 'desc' => 'Centralized validated data collection forms with Anti-Spam HoneyPot, XSS sanitization, and Curator file uploads.'],
            ['title' => 'Module 3: Filament v5 Administrative Dashboard', 'desc' => 'Filament PHP v5 operational control hub with instant filtering, indexed O(1) search, and Keyset Cursor Pagination.'],
            ['title' => 'Module 4: Automated Reconciliation & Export Engine', 'desc' => 'Automated operational and transaction reporting engine with official A4 PDF printing and XLSX spreadsheet exports.'],
        ] : [
            ['title' => 'Modul 1: Autentikasi & RBAC Strict Mode', 'desc' => 'Manajemen hak akses multi-role dengan proteksi brute-force, CSRF Shield, dan identitas ULID 26 karakter.'],
            ['title' => 'Modul 2: Core Data Intake & Form Validasi', 'desc' => 'Formulir pengumpulan data tervalidasi terpusat dengan proteksi Anti-Spam HoneyPot, sanitasi XSS, dan unggah berkas Curator.'],
            ['title' => 'Modul 3: Dasbor Administrasi Filament v5', 'desc' => 'Pusat kendali operasional berbasis Filament PHP v5 dengan tabel filter data instan, pencarian berindeks O(1), dan Keyset Cursor Pagination.'],
            ['title' => 'Modul 4: Engine Rekonsiliasi & Ekspor Laporan', 'desc' => 'Fitur rekap data transaksi dan operasional otomatis dengan dukungan cetak PDF resmi berstandar A4 dan ekspor spreadsheet XLSX.'],
        ];
    }

    $tables = $prd['erd_schema']['tables'] ?? [];
    if (empty($tables) || !is_array($tables)) {
        $tables = $isEn ? [
            [
                'table_name' => 'users',
                'foreign_keys' => [],
                'description' => 'Authentication user accounts with ULID PK, granular roles, and audit trail metadata.'
            ],
            [
                'table_name' => 'domain_records',
                'foreign_keys' => ['user_id (users.id)'],
                'description' => 'Primary entity recording operational business domain transactions and files.'
            ],
            [
                'table_name' => 'transactions',
                'foreign_keys' => ['user_id (users.id)'],
                'description' => 'Midtrans payment gateway ledger with idempotency protection and SHA-256 seal.'
            ],
            [
                'table_name' => 'activity_logs',
                'foreign_keys' => ['user_id (users.id)'],
                'description' => 'Immutable forensic audit trail logging all mutation events.'
            ]
        ] : [
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
<html lang="{{ $isEn ? 'en' : 'id' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $isEn ? 'Ultimate PRD & Architecture Blueprint' : 'Ultimate PRD & Cetak Biru Arsitektur' }} - {{ $businessName }}</title>
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
            margin-bottom: 4px;
        }
        .badge-dark {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7px;
            font-weight: bold;
            padding: 1px 4px;
            margin-right: 4px;
        }
        h1 {
            font-size: 14px;
            color: #0f172a;
            margin: 0 0 3px 0;
            font-weight: bold;
            letter-spacing: -0.3px;
        }
        .meta {
            font-size: 7.5px;
            color: #64748b;
            font-family: monospace;
        }
        .section {
            margin-bottom: 9px;
        }
        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #047857;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 8px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
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
        <span class="badge">{{ $isEn ? 'Neriah Pro • Project OS Ultimate PRD & Architecture Blueprint' : 'Neriah Pro • Project OS Ultimate PRD & Cetak Biru Arsitektur' }}</span>
        <h1>{{ $businessName }}</h1>
        <div class="meta">
            DOC_ID: {{ strtoupper(substr($blueprint->id, 0, 14)) }} &bull; 
            CREATED: {{ optional($blueprint->created_at)->format('d M Y') ?: date('d M Y') }} &bull; 
            STATUS: {{ strtoupper($isEn ? ($blueprint->project_status === 'prospecting' ? 'PROSPECTING' : ($blueprint->project_status ?: 'ACTIVE LICENSE')) : ($blueprint->project_status ?: 'LISENSI AKTIF')) }} &bull; 
            RELEASE_TARGET: {{ $targetWaktu }} &bull;
            TRACK: {{ strtoupper($blueprint->tipe_aplikasi ?: ($isEn ? 'Studio Turnkey MVP' : 'Studio Turnkey MVP')) }}
        </div>
    </div>

    <!-- 1. Executive Summary & 26 Parameter Discovery -->
    <div class="section">
        <div class="section-title">1. {{ $isEn ? 'Executive Summary & Business Requirements Discovery' : 'Ringkasan Eksekutif & Analisis Kebutuhan Bisnis' }}</div>
        <table>
            <tr>
                <th style="width: 25%;">{{ $isEn ? 'Client / Business Name' : 'Nama Klien / Bisnis' }}</th>
                <td><strong>{{ $businessName }}</strong> (PIC: {{ $clientPic }})</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Contact & Communication' : 'Kontak & Komunikasi' }}</th>
                <td>Email: {{ $email }} &bull; {{ $isEn ? 'Phone/WA:' : 'Telepon/WA:' }} {{ $phone }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Primary Business Problem' : 'Masalah Utama Bisnis' }}</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($masalah, true, true, $isEn) !!}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'System Objectives & Solutions' : 'Tujuan & Solusi Sistem' }}</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($tujuan, true, true, $isEn) !!}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Target Release Timeline' : 'Target Waktu Rilis' }}</th>
                <td><strong>{{ $targetWaktu }}</strong></td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Target Platform' : 'Target Platform' }}</th>
                <td>{{ $targetPlatform }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Out of Scope Boundaries' : 'Batasan Negatif (Out of Scope)' }}</th>
                <td style="color: #991b1b; background-color: #fef2f2;">
                    {!! \App\Support\BlueprintPdfHelper::formatPoints($outOfScope, true, true, $isEn) !!}
                </td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Hosting & Migration Infrastructure' : 'Infrastruktur Hosting & Migrasi' }}</th>
                <td>
                    <strong>{{ $isEn ? 'Hosting:' : 'Hosting:' }}</strong> {{ $preferensiHosting }} &bull; 
                    <strong>{{ $isEn ? 'Migration:' : 'Migrasi:' }}</strong> {{ $migrasiData }}
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. System Actors & Roles -->
    <div class="section">
        <div class="section-title">2. {{ $isEn ? 'System Users, Roles (RBAC) & Core Workflows' : 'Pengguna Sistem, Aktor (RBAC) & Alur Kerja Utama' }}</div>
        <table>
            <tr>
                <th style="width: 25%;">{{ $isEn ? 'Target Audience' : 'Target Pengguna' }}</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($targetAudiens, false, true, $isEn) !!}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'System Actors & Roles' : 'Aktor & Peran Sistem' }}</th>
                <td>
                    @php
                        $actorsList = $prd['system_actors'] ?? [];
                        $isGranular = is_array($actorsList) && count($actorsList) > 1 && !preg_match('/\d+[\.\)]/u', $actorsList[0]['name'] ?? '');
                    @endphp
                    @if($isGranular)
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0;">
                            @foreach($actorsList as $act)
                                @php
                                    $actName = $act['name'] ?? $act['title'] ?? ($isEn ? 'Actor' : 'Aktor');
                                    $actRole = $act['role'] ?? $act['desc'] ?? '';
                                    $actPerms = $act['permissions'] ?? [];
                                    if ($isEn) {
                                        $actName = \App\Support\BlueprintPdfHelper::translateText($actName, true);
                                        $actRole = \App\Support\BlueprintPdfHelper::translateText($actRole, true);
                                    }
                                @endphp
                                <tr>
                                    <td style="width: 28%; border: none; padding: 2px 4px 2px 0; font-weight: bold; color: #0f172a; font-size: 7.5px;">
                                        &bull; {{ $actName }}
                                    </td>
                                    <td style="border: none; padding: 2px 0; font-size: 7.5px; color: #334155;">
                                        {{ $actRole }}
                                        @if(!empty($actPerms))
                                            <div style="font-size: 6.5px; color: #047857; font-family: monospace; margin-top: 1px;">
                                                {{ $isEn ? 'Permissions:' : 'Izin:' }} {{ implode(', ', (array)$actPerms) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        {!! \App\Support\BlueprintPdfHelper::formatPoints($aktor, true, true, $isEn) !!}
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Core Workflows' : 'Alur Kerja Utama (Core Workflows)' }}</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($alurKerja, true, true, $isEn) !!}</td>
            </tr>
        </table>
    </div>

    <!-- 3. MVP Feature Scope & 6 Engineering Dimensions -->
    <div class="section">
        <div class="section-title">3. {{ $isEn ? 'Core MVP Feature Scope & Engineering Specifications (Phase 1 - Core MVP)' : 'Ruang Lingkup Fitur Inti & Spesifikasi Rekayasa (Fase 1 - Core MVP)' }}</div>
        @foreach($detailedMvp as $feat)
            @php
                $featTitle = is_array($feat) ? ($feat['title'] ?? $feat['name'] ?? ($isEn ? 'Core MVP Feature' : 'Fitur Inti MVP')) : ($isEn ? 'Core MVP Feature' : 'Fitur Inti MVP');
                $featUserStory = is_array($feat) ? ($feat['user_story'] ?? '') : '';
                $featDesc = is_array($feat) ? ($feat['desc'] ?? $feat['description'] ?? '') : (string)$feat;
                if ($isEn) {
                    $featTitle = \App\Support\BlueprintPdfHelper::translateText($featTitle, true);
                    if (!empty($featUserStory)) $featUserStory = \App\Support\BlueprintPdfHelper::translateText($featUserStory, true);
                    if (!empty($featDesc)) $featDesc = \App\Support\BlueprintPdfHelper::translateText($featDesc, true);
                }
                $featAc = is_array($feat) ? ($feat['acceptance_criteria'] ?? []) : [];
                $normAc = \App\Services\PrdGeneratorService::normalizeAcceptanceCriteria($featAc, $featTitle, $featUserStory);
                $featFe = is_array($feat) ? ($feat['frontend'] ?? []) : [];
                $featBe = is_array($feat) ? ($feat['backend'] ?? []) : [];
                $featApi = is_array($feat) ? ($feat['integration'] ?? ($feat['api_contracts'] ?? [])) : [];
                $featFiles = is_array($feat) ? ($feat['target_files'] ?? ($feat['bounded_target_files'] ?? [])) : [];
                $featDirective = is_array($feat) ? ($feat['code_agent_directive'] ?? ($feat['agent_prompt'] ?? '')) : '';
                if ($isEn && !empty($featDirective)) {
                    $featDirective = \App\Support\BlueprintPdfHelper::translateText(is_string($featDirective) ? $featDirective : json_encode($featDirective), true);
                }
            @endphp
            <div class="feature-card">
                <div class="feature-title">
                    <span class="badge-dark">{{ $isEn ? 'MODULE' : 'MODUL' }} {{ $loop->iteration }}</span> 
                    <strong>{{ $featTitle }}</strong>
                    @if(!empty($feat['category_label'] ?? $feat['category'] ?? null))
                        <span class="mini-badge">{{ $isEn ? \App\Support\BlueprintPdfHelper::translateText($feat['category_label'] ?? $feat['category'], true) : ($feat['category_label'] ?? $feat['category']) }}</span>
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
                        @php
                            $scenarioLabel = $ac['scenario'] ?? (($isEn ? 'Scenario ' : 'Skenario ') . ($acIdx + 1));
                            $givenVal = (!empty($ac['given']) && $ac['given'] !== '-') ? $ac['given'] : \App\Services\PrdGeneratorService::deriveGivenCondition($ac['scenario'] ?? '', $featTitle, $acIdx);
                            $whenVal = (!empty($ac['when']) && $ac['when'] !== '-') ? $ac['when'] : \App\Services\PrdGeneratorService::deriveWhenCondition($ac['scenario'] ?? '', $featTitle, $acIdx);
                            $thenVal = (!empty($ac['then']) && $ac['then'] !== '-') ? $ac['then'] : \App\Services\PrdGeneratorService::deriveThenCondition($ac['scenario'] ?? '', $featTitle, $acIdx);
                            if ($isEn) {
                                $scenarioLabel = \App\Support\BlueprintPdfHelper::translateText($scenarioLabel, true);
                                $givenVal = \App\Support\BlueprintPdfHelper::translateText($givenVal, true);
                                $whenVal = \App\Support\BlueprintPdfHelper::translateText($whenVal, true);
                                $thenVal = \App\Support\BlueprintPdfHelper::translateText($thenVal, true);
                            }
                        @endphp
                        <div class="gherkin-box">
                            <strong>{{ $scenarioLabel }}:</strong><br>
                            <span class="gherkin-given">GIVEN:</span> {{ $givenVal }}<br>
                            <span class="gherkin-when">WHEN:</span> {{ $whenVal }}<br>
                            <span class="gherkin-then">THEN:</span> {{ $thenVal }}
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
                                            &bull; {{ $isEn ? \App\Support\BlueprintPdfHelper::translateText($t, true) : $t }}<br>
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
                                            &bull; {{ $isEn ? \App\Support\BlueprintPdfHelper::translateText($t, true) : $t }}<br>
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
                    {{ $isEn ? 'Subsequent Roadmap (Phase 2 Development):' : 'Roadmap Lanjutan (Fase 2 Pengembangan):' }}
                </div>
                <div class="box" style="color: #475569;">
                    {!! \App\Support\BlueprintPdfHelper::formatPoints($fiturTambahan, true, true, $isEn) !!}
                </div>
            </div>
        @endif
    </div>

    <!-- 4. Architecture Standards & Database -->
    <div class="section">
        <div class="section-title">4. {{ $isEn ? 'Architecture Standards & Database Specifications' : 'Standar Rekayasa Arsitektur & Database' }}</div>
        <table>
            <tr>
                <th style="width: 25%;">{{ $isEn ? 'Architectural Pattern' : 'Pola Arsitektur' }}</th>
                <td>Modern Enterprise Monolith (Laravel 13 + Filament PHP v5 + Livewire 4)</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Database & Primary Key' : 'Basis Data & Primary Key' }}</th>
                <td>PostgreSQL 16 Strict Mode &bull; Distributed ULID 26-Character Primary Keys (<span class="code">-&gt;ulid('id')-&gt;primary()</span>)</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Pagination Strategy' : 'Strategi Paginasi' }}</th>
                <td>{{ $isEn ? 'Keyset Cursor Pagination O(1) Stability (Anti-Offset Bottleneck for Millions of Records)' : 'Keyset Cursor Pagination O(1) Stability (Anti-Offset Bottleneck untuk Skala Jutaan Record)' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'External Integrations' : 'Integrasi Eksternal' }}</th>
                <td>{!! \App\Support\BlueprintPdfHelper::formatPoints($integrasi, false, true, $isEn) !!}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Security Compliance & Data Privacy' : 'Kepatuhan Keamanan & UU PDP' }}</th>
                <td>{{ $isEn ? 'OWASP Top 10, AI Threat Shield Middleware, HoneyPot Anti-Bot, Anti-RCE Guard, CSRF Shield, XSS Sanitization' : 'OWASP Top 10, AI Threat Shield Middleware, HoneyPot Anti-Bot, Anti-RCE Guard, CSRF Shield, Sanitasi XSS' }}</td>
            </tr>
        </table>
    </div>

    <!-- 5. Database Relational Entities (ERD PostgreSQL) -->
    <div class="section">
        <div class="section-title">5. {{ $isEn ? 'Relational Database Entity Schema (PostgreSQL ERD)' : 'Skema Entitas Relasional Basis Data (ERD PostgreSQL)' }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">{{ $isEn ? 'Entity Table' : 'Tabel Entitas' }}</th>
                    <th style="width: 25%;">{{ $isEn ? 'Primary & Foreign Keys' : 'Primary & Foreign Keys' }}</th>
                    <th style="width: 55%;">{{ $isEn ? 'Database Columns & Description' : 'Kolom Basis Data & Keterangan' }}</th>
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
                        if ($isEn && !empty($tDesc)) {
                            $tDesc = \App\Support\BlueprintPdfHelper::translateText($tDesc, true);
                        }
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
                                <span style="color: #94a3b8; font-size: 7px;">{{ $isEn ? 'Self-Contained Relation' : 'Relasi Mandiri' }}</span>
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
                                        <span style="color: #64748b;">+{{ count($tCols) - 10 }} {{ $isEn ? 'more columns' : 'kolom lainnya' }}</span>
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
        <div class="section-title">6. {{ $isEn ? 'Engineering Execution Timeline (5 Sprints)' : 'Timeline Eksekusi Rekayasa (5 Sprint Kerja)' }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Sprint</th>
                    <th style="width: 35%;">{{ $isEn ? 'Sprint Focus' : 'Fokus Pengerjaan' }}</th>
                    <th style="width: 50%;">{{ $isEn ? 'Deliverables & QA Target' : 'Deliverable & Target QA' }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Sprint 1</strong></td>
                    <td>{{ $isEn ? 'System Foundation & Database Schema' : 'Fondasi Sistem & Database' }}</td>
                    <td>{{ $isEn ? 'ULID table schemas, PostgreSQL migrations, Model Observers, Redis Cache Store' : 'Skema tabel ULID, Migrasi PostgreSQL, Model Observers, Cache Store' }}</td>
                </tr>
                <tr>
                    <td><strong>Sprint 2</strong></td>
                    <td>{{ $isEn ? 'Core MVP Business Logic' : 'Logika Bisnis Inti (Core MVP)' }}</td>
                    <td>{{ $isEn ? 'Service layer, Action handlers, Data validation forms, State machine' : 'Service layer, Action handlers, Form validasi data, State machine' }}</td>
                </tr>
                <tr>
                    <td><strong>Sprint 3</strong></td>
                    <td>{{ $isEn ? 'Frontend & Responsive UI' : 'Frontend & Responsive UI' }}</td>
                    <td>{{ $isEn ? 'Responsive interface (zero capsule/pill), 60fps micro-animations, educational UX' : 'Antarmuka responsif tanpa capsule/pill, 60fps micro-animations, UX edukatif' }}</td>
                </tr>
                <tr>
                    <td><strong>Sprint 4</strong></td>
                    <td>{{ $isEn ? 'Security Audit & QA Automation' : 'Audit Keamanan & QA' }}</td>
                    <td>{{ $isEn ? 'Rate-limiting, AI Threat Shield, Automated Pest test suite, Anti-bot safeguards' : 'Rate-limiting, AI Threat Shield, Automated Pest test suite, Anti-bot' }}</td>
                </tr>
                <tr>
                    <td><strong>Sprint 5</strong></td>
                    <td>{{ $isEn ? 'Cloud Staging & Production Handover' : 'Staging Cloud & Serah Terima' }}</td>
                    <td>{{ $isEn ? 'Cloud VPS provisioning, Client UAT, Scope lock verification, Git repository handover' : 'Provisioning VPS Cloud, UAT Klien, Scope lock verification, Handover Git' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 7. SLA & Bug Warranty -->
    <div class="section">
        <div class="section-title">7. {{ $isEn ? 'Governance, SLA & Bug Warranty' : 'Tata Kelola, SLA & Garansi Perbaikan Bug' }}</div>
        <table>
            <tr>
                <th style="width: 25%;">{{ $isEn ? 'Bug Fix Warranty' : 'Garansi Perbaikan Bug' }}</th>
                <td><strong>{{ $isEn ? '30 Calendar Days Post-Launch' : '30 Hari Kalender Pascameluncur' }}</strong> &bull; {{ $isEn ? 'Complimentary bug fixes for errors or non-compliance with MVP specifications.' : 'Perbaikan gratis terhadap error atau ketidaksesuaian fungsi MVP.' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Repository Handover' : 'Penyerahan Repositori' }}</th>
                <td>{{ $isEn ? '100% full access to private GitHub Git repository upon final settlement.' : 'Akses 100% penuh ke repositori Git GitHub (Private Repository) setelah pelunasan final.' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Scope Change Governance' : 'Ketentuan Perubahan Scope' }}</th>
                <td>{{ $isEn ? 'Scope is cryptographically locked post-signing. New requirements are processed via formal Change Request (CR) or Addendum.' : 'Ruang lingkup terkunci setelah penandatanganan. Kebutuhan baru diproses via Change Request (CR) / Addendum resmi.' }}</td>
            </tr>
        </table>
    </div>

    <!-- 8. Cloud Infrastructure & Docker Containerization -->
    <div class="section">
        <div class="section-title">8. {{ $isEn ? 'Cloud Infrastructure Topology & Docker Containerization' : 'Topologi Infrastruktur Cloud & Kontainerisasi Docker' }}</div>
        <table>
            <tr>
                <th style="width: 25%;">{{ $isEn ? 'Runtime & PHP Engine' : 'Runtime & PHP Engine' }}</th>
                <td>PHP 8.5+ Hardened &bull; Railpack FrankenPHP / PHP-FPM High Performance</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Database & In-Memory Cache' : 'Basis Data & Cache' }}</th>
                <td>PostgreSQL 16+ Strict Mode &bull; Redis 7 (In-Memory Cache &amp; Idempotency Vault)</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Web Server & SSL' : 'Web Server & SSL' }}</th>
                <td>Nginx Hardened Reverse Proxy &bull; TLS 1.3 &bull; Let's Encrypt Auto-Renewal SSL</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Container Architecture' : 'Arsitektur Kontainer' }}</th>
                <td>{{ $isEn ? 'Isolated docker-compose.yml (app, db, redis, web, worker queue) • Zero Drift' : 'docker-compose.yml terisolasi (app, db, redis, web, worker queue) &bull; Zero Drift' }}</td>
            </tr>
        </table>
    </div>

    <!-- 9. 10-Pillar Software Factory OS Matrix -->
    <div class="section">
        <div class="section-title">9. {{ $isEn ? '10-Pillar Software Factory OS Matrix (100% Turnkey Maturity)' : 'Matriks 10 Pilar Software Factory OS (100% Turnkey Maturity)' }}</div>
        <table>
            <tr>
                <th style="width: 25%;">{{ $isEn ? 'Pillars 1 - 2' : 'Pilar 1 - 2' }}</th>
                <td>{{ $isEn ? 'Zero-Loss Intake & Discovery (MarkItDown OCR) • PRD 26 Parameter & WBS Gantt (OWASP & UU PDP Compliance)' : 'Zero-Loss Intake & Discovery (MarkItDown OCR) • PRD 26 Parameter & WBS Gantt (OWASP & UU PDP)' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Pillars 3 - 4' : 'Pilar 3 - 4' }}</th>
                <td>{{ $isEn ? 'PostgreSQL 16 Strict ULID & Keyset Cursor O(1) • Automated Scaffolding (25+ Files, API V1, OpenAPI 3.0)' : 'PostgreSQL 16 Strict ULID & Keyset Cursor O(1) • Automated Scaffolding (25+ Files, API V1, OpenAPI 3.0)' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Pillars 5 - 6' : 'Pilar 5 - 6' }}</th>
                <td>{{ $isEn ? 'Containerization Docker PHP 8.5+, PG 16, Redis 7 • Automated Pest API Testing & 100+ Synthetic Rows' : 'Containerization Docker PHP 8.5+, PG 16, Redis 7 • Automated Pest API Testing & 100+ Synthetic Rows' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Pillars 7 - 8' : 'Pilar 7 - 8' }}</th>
                <td>{{ $isEn ? 'CI/CD Pipeline Automated deploy.sh 6-Scenario • Legal Contract & Tamper-Proof SHA-256 Scope Lock' : 'CI/CD Pipeline Automated deploy.sh 6-Scenario • Legal Contract & Tamper-Proof SHA-256 Scope Lock' }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Pillars 9 - 10' : 'Pilar 9 - 10' }}</th>
                <td>{{ $isEn ? 'Managed Sprint Capacity (Anti-Collision 2-3 Batch) • Client Transparency Portal & Expiry Alert' : 'Managed Sprint Capacity (Anti-Collision 2-3 Batch) • Client Transparency Portal & Expiry Alert' }}</td>
            </tr>
        </table>
    </div>

    <!-- 10. Cryptographic Checksum & Contract Seal -->
    <div class="section">
        <div class="section-title">10. {{ $isEn ? 'Cryptographic Integrity & Scope Freeze Seal' : 'Integritas Kriptografis & Segel Scope Freeze' }}</div>
        <div class="box" style="font-family: monospace; font-size: 7.5px; word-break: break-all;">
            <strong>SHA-256 SPECIFICATION CHECKSUM:</strong><br>
            {{ $blueprint->document_sha256 ?: hash('sha256', $blueprint->id . $businessName . $blueprint->created_at) }}<br><br>
            @if($blueprint->signed_agreement)
                <strong>{{ $isEn ? 'CONTRACT STATUS:' : 'STATUS KONTRAK:' }}</strong> {{ $isEn ? 'LOCKED (SIGNED & VERIFIED)' : 'TERKUNCI (SIGNED & VERIFIED)' }}<br>
                <strong>{{ $isEn ? 'SIGNATORY:' : 'PENANDATANGAN:' }}</strong> {{ $clientPic }}<br>
                <strong>{{ $isEn ? 'SIGN TIME:' : 'WAKTU TTD:' }}</strong> {{ optional($blueprint->signed_at)->format('d M Y H:i:s T') ?: date('d M Y H:i:s') }}<br>
                <strong>IP AUDIT:</strong> {{ $blueprint->signer_ip ?: 'Recorded' }}
            @else
                <strong>{{ $isEn ? 'SPECIFICATION STATUS:' : 'STATUS SPESIFIKASI:' }}</strong> {{ $isEn ? 'VERIFIED OFFICIAL LICENSE (COPYRIGHT NERIAH PRO)' : 'LISENSI RESMI TERVERIFIKASI (HAK CIPTA NERIAH PRO)' }}<br>
                <strong>{{ $isEn ? 'SYSTEM VERIFICATION:' : 'VERIFIKASI SISTEM:' }}</strong> {{ $isEn ? 'Active integrity seal • Guarantees authentic content registered on neriahpro.com.' : 'Segel integritas aktif • Menjamin konten otentik dan terdaftar di neriahpro.com.' }}
            @endif
        </div>
    </div>

    <div class="footer">
        {{ $isEn ? 'Generated by Neriah Pro Blueprint Engine • Legally Binding Document • neriahpro.com' : 'Dihasilkan oleh Neriah Pro Blueprint Engine • Dokumen Mengikat Secara Hukum • neriahpro.com' }}
    </div>
</body>
</html>
