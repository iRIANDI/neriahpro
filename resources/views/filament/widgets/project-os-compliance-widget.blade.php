@php
    $isEn = app()->getLocale() === 'en';
@endphp
<x-filament-widgets::widget>
    <style>
        .poc-container {
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            border-top: 3px solid #059669;
            padding: 24px;
            position: relative;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .poc-container {
            background-color: #09090b;
            border-color: #27272a;
            border-top-color: #10b981;
        }

        .poc-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
        }

        .poc-sublabel {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.15em;
            color: #065f46;
            text-transform: uppercase;
        }
        .dark .poc-sublabel {
            color: #10b981;
        }

        .poc-title {
            font-size: 20px;
            font-weight: 900;
            color: #09090b;
            letter-spacing: -0.02em;
            margin: 4px 0 0 0;
            text-transform: uppercase;
        }
        @media (min-width: 640px) {
            .poc-title { font-size: 22px; }
        }
        .dark .poc-title {
            color: #ffffff;
        }

        .poc-badges-wrap {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .poc-badge {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            text-transform: uppercase;
            border-radius: 0;
            letter-spacing: 0.05em;
        }

        .poc-badge-emerald {
            background-color: #d1fae5;
            color: #064e3b;
            border: 1px solid #34d399;
        }
        .dark .poc-badge-emerald {
            background-color: rgba(6, 78, 59, 0.7);
            color: #a7f3d0;
            border-color: #059669;
        }

        .poc-badge-sky {
            background-color: #e0f2fe;
            color: #0c4a6e;
            border: 1px solid #38bdf8;
        }
        .dark .poc-badge-sky {
            background-color: rgba(12, 74, 110, 0.7);
            color: #bae6fd;
            border-color: #0284c7;
        }

        .poc-badge-amber {
            background-color: #fef3c7;
            color: #78350f;
            border: 1px solid #fbbf24;
        }
        .dark .poc-badge-amber {
            background-color: rgba(120, 53, 15, 0.7);
            color: #fde68a;
            border-color: #d97706;
        }

        .poc-badge-rose {
            background-color: #ffe4e6;
            color: #881337;
            border: 1px solid #fb7185;
        }
        .dark .poc-badge-rose {
            background-color: rgba(136, 19, 55, 0.7);
            color: #fecdd3;
            border-color: #e11d48;
        }

        .poc-statement-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 18px;
            margin-bottom: 22px;
        }
        .dark .poc-statement-box {
            background-color: #18181b;
            border-color: #27272a;
        }

        .poc-os-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background-color: #e2e8f0;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #065f46;
            font-weight: 900;
            font-family: ui-monospace, Menlo, Monaco, monospace;
            font-size: 14px;
        }
        .dark .poc-os-icon {
            background-color: #27272a;
            border-color: #3f3f46;
            color: #10b981;
        }

        .poc-statement-heading {
            margin: 0 0 8px 0;
            font-weight: 800;
            color: #09090b;
            font-size: 14px;
        }
        .dark .poc-statement-heading {
            color: #ffffff;
        }

        .poc-statement-p {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.6;
        }
        .dark .poc-statement-p {
            color: #d4d4d8;
        }
        .poc-statement-p strong {
            color: #09090b;
        }
        .dark .poc-statement-p strong {
            color: #ffffff;
        }

        .poc-statement-core {
            margin: 0;
            color: #334155;
            font-size: 13px;
            line-height: 1.6;
        }
        .dark .poc-statement-core {
            color: #a1a1aa;
        }
        .poc-statement-core strong {
            color: #065f46;
        }
        .dark .poc-statement-core strong {
            color: #10b981;
        }

        .poc-pillars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 14px;
        }

        .poc-pillar-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 16px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }
        .dark .poc-pillar-card {
            background-color: #18181b;
            border-color: #27272a;
        }

        .poc-pillar-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 4px;
            letter-spacing: 0.05em;
        }

        .poc-pillar-title {
            font-size: 13px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 6px;
        }
        .dark .poc-pillar-title {
            color: #ffffff;
        }

        .poc-pillar-desc {
            font-size: 11px;
            color: #475569;
            line-height: 1.5;
        }
        .dark .poc-pillar-desc {
            color: #a1a1aa;
        }
    </style>

    <div class="poc-container">
        <!-- Header Row -->
        <div class="poc-header">
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background-color: #059669;"></span>
                    <span class="poc-sublabel">
                        NERIAH PRO // PROJECT OS & DIGITAL ARCHITECTURE PLATFORM
                    </span>
                </div>
                <h2 class="poc-title">
                    {{ $isEn ? 'Software Engineering Architecture Platform & Project OS' : 'Platform Arsitektur Rekayasa Perangkat Lunak & Project OS' }}
                </h2>
            </div>
            <div class="poc-badges-wrap">
                <span class="poc-badge poc-badge-emerald">
                    MIDTRANS COMPLIANCE READY
                </span>
                <span class="poc-badge poc-badge-sky">
                    SATU PINTU RETAIL &amp; MITRA v6.0
                </span>
                <span class="poc-badge poc-badge-sky">
                    POSTGRESQL STRICT ULID
                </span>
                <span class="poc-badge poc-badge-amber">
                    MARKITDOWN INGESTION
                </span>
                <span class="poc-badge poc-badge-rose">
                    AI-SHIELD ACTIVE
                </span>
            </div>
        </div>

        <!-- Compliance & Core Purpose Statement -->
        <div class="poc-statement-box">
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div class="poc-os-icon">
                    OS
                </div>
                <div>
                    <p class="poc-statement-heading">
                        {{ $isEn ? 'Digital Service Information & Midtrans Merchant Account Verification:' : 'Keterangan Layanan Digital & Verifikasi Akun Merchant Midtrans:' }}
                    </p>
                    <p class="poc-statement-p">
                        @if($isEn)
                            The <strong>neriahpro.com</strong> platform is the official operational instrument of <strong>Yoseph Iriandi Tambunan</strong> (Web Developer & Software Architect) for client system specification design (Product Requirements Document / PRD, Entity Relationship Diagram / ERD PostgreSQL Strict ULID, Tech Stack Selection, MVP Sprint Timeline, Scaffold Code Boilerplate .zip), SHA-256 hash-backed digital partnership agreements, and Down Payment billing (DP 50%) via the Midtrans Snap payment gateway.
                        @else
                            Platform <strong>neriahpro.com</strong> adalah instrumen operasional resmi milik <strong>Yoseph Iriandi Tambunan</strong> (Web Developer & Software Architect) untuk melayani klien dalam perancangan spesifikasi sistem (Product Requirements Document / PRD, Entity Relationship Diagram / ERD PostgreSQL Strict ULID, Pemilihan Tech Stack, Timeline Sprint MVP, Boilerplate Scaffold Kode .zip), pengikatan kontrak kerja sama digital berintegritas hash SHA-256, serta penagihan uang muka Down Payment (DP 50%) via payment gateway Midtrans Snap.
                        @endif
                    </p>
                    <p class="poc-statement-core">
                        @if($isEn)
                            <strong>Core Purpose & Scope Freeze:</strong> Ensuring zero assumption in engineering development, legally freezing project scope to prevent scope creep, and providing crystal-clear deliverables before any 50% Down Payment commitment via Midtrans Snap is processed.
                        @else
                            <strong>Tujuan Utama & Scope Freeze:</strong> Memastikan seluruh pengerjaan kode tidak berasumsi atas kebutuhan bisnis klien, mengunci ruang lingkup pekerjaan (scope freeze) secara legal untuk mencegah penambahan lingkup tak terkendali (scope creep), serta memberikan dasar deliverable yang jelas dan transparan sebelum komitmen pembayaran Down Payment (DP 50%) via Midtrans Snap diproses.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 6 Core Pillars of Modern Project OS -->
        <div class="poc-pillars-grid">
            <!-- Pillar 1 -->
            <div class="poc-pillar-card" style="border-left: 3px solid #059669;">
                <div class="poc-pillar-tag" style="color: #059669;">
                    {{ $isEn ? '01 // DISCOVERY & MARKITDOWN STUDIO' : '01 // DISCOVERY & STUDIO MARKITDOWN' }}
                </div>
                <div class="poc-pillar-title">
                    {{ $isEn ? 'Quick Idea Studio & Multi-Doc Ingestion' : 'Studio Ide Cepat & Ingesti Multi-Dokumen' }}
                </div>
                <div class="poc-pillar-desc">
                    {{ $isEn 
                        ? 'Brainstorm ideas via interactive textarea or attach project briefs (PDF, DOCX, XLSX, PPTX, CSV, TXT, Wireframe). Microsoft MarkItDown transforms documents into local server Markdown, saving >80% AI tokens.' 
                        : 'Curahkan ide via textarea interaktif atau lampirkan berkas (PDF, DOCX, XLSX, PPTX, CSV, TXT, Wireframe). Microsoft MarkItDown mengonversi berkas ke Markdown lokal di server guna menghemat >80% token AI.' }}
                </div>
            </div>

            <!-- Pillar 2 -->
            <div class="poc-pillar-card" style="border-left: 3px solid #0284c7;">
                <div class="poc-pillar-tag" style="color: #0284c7;">
                    {{ $isEn ? '02 // 26 ARCHITECTURAL PARAMETERS' : '02 // 26 PARAMETER ARSITEKTUR' }}
                </div>
                <div class="poc-pillar-title">
                    {{ $isEn ? 'Core 26-Parameter Architectural Questionnaire (Blocks A-F)' : 'Kuesioner 26 Parameter Arsitektur Inti (Blok A-F)' }}
                </div>
                <div class="poc-pillar-desc">
                    {{ $isEn 
                        ? 'Complete structuring: Business Identity, RBAC Matrix, Core MVP Features & Workflows, Gateway Integrations, UI/UX, Cloud VPS Hosting, Traffic Scale, OWASP Standards, and Budget & 30-Day SLA Bug Warranty.' 
                        : 'Strukturisasi lengkap: Identitas Bisnis, Matriks Hak Akses RBAC, Fitur Inti MVP & Alur Kerja, Integrasi Gateway, UI/UX, Hosting Cloud VPS, Skala Trafik, Standar OWASP, hingga Anggaran & Garansi SLA Bug 30 Hari.' }}
                </div>
            </div>

            <!-- Pillar 3 -->
            <div class="poc-pillar-card" style="border-left: 3px solid #7c3aed;">
                <div class="poc-pillar-tag" style="color: #7c3aed;">
                    {{ $isEn ? '03 // ULID ERD & CODE SCAFFOLD' : '03 // ERD ULID & RANGKA KODE' }}
                </div>
                <div class="poc-pillar-title">
                    {{ $isEn ? 'Ultimate PRD, ERD & Boilerplate .zip' : 'PRD Mutakhir, ERD & Rangka Boilerplate .zip' }}
                </div>
                <div class="poc-pillar-desc">
                    {{ $isEn 
                        ? 'Synthesis of PRD documents, Mermaid Flowcharts & PostgreSQL Strict ULID ERDs, and 1-click scaffold boilerplate generator (.zip: docker-compose, openapi.json, SQL schema, Laravel 13 & Next.js routes).' 
                        : 'Sintesis dokumen PRD, visualisasi Mermaid Flowchart & PostgreSQL Strict ULID ERD, serta generator 1-klik paket rangka boilerplate (.zip: docker-compose, openapi.json, skema SQL, rute Laravel 13 & Next.js).' }}
                </div>
            </div>

            <!-- Pillar 4 -->
            <div class="poc-pillar-card" style="border-left: 3px solid #d97706;">
                <div class="poc-pillar-tag" style="color: #d97706;">
                    {{ $isEn ? '04 // SCOPE LOCK & E-SIGNATURE' : '04 // PENGUNCIAN RUANG LINGKUP & E-SIGNATURE' }}
                </div>
                <div class="poc-pillar-title">
                    {{ $isEn ? 'Digital Contract & Scope Freeze Protocol' : 'Kontrak Kerja Digital & Protokol Scope Freeze' }}
                </div>
                <div class="poc-pillar-desc">
                    {{ $isEn 
                        ? 'Automated issuance of official partnership agreements with SHA-256 hash encryption, scope lock (scope freeze), and isolated Change Request (CR) clauses to protect both parties from scope creep.' 
                        : 'Penerbitan surat perjanjian kerja sama resmi otomatis dengan enkripsi hash SHA-256, penguncian ruang lingkup (scope freeze), dan klausul Change Request (CR) terpisah guna melindungi kedua belah pihak dari penambahan lingkup tak terkendali (scope creep).' }}
                </div>
            </div>

            <!-- Pillar 5 -->
            <div class="poc-pillar-card" style="border-left: 3px solid #059669;">
                <div class="poc-pillar-tag" style="color: #059669;">
                    {{ $isEn ? '05 // MIDTRANS SNAP & CART HOLD' : '05 // MIDTRANS SNAP & RESERVASI SLOT' }}
                </div>
                <div class="poc-pillar-title">
                    {{ $isEn ? 'Midtrans Snap Settlement & Anti-Ghost Hold' : 'Pembayaran Midtrans Snap & Reservasi Anti-Ghost' }}
                </div>
                <div class="poc-pillar-desc">
                    {{ $isEn 
                        ? 'Legally binding 50% Down Payment billing tied directly to the contract document number, backed by 100% bypass promo vouchers (Rp 0) for instant preview and a 24-hour Anti-Ghost Cart hold reservation.' 
                        : 'Penagihan Uang Muka (DP) 50% yang sah dan terikat dengan nomor dokumen kontrak, didukung voucher promo bypass 100% (Rp 0) untuk review instan dan reservasi slot Anti-Ghost Hold 24 jam.' }}
                </div>
            </div>

            <!-- Pillar 6 -->
            <div class="poc-pillar-card" style="border-left: 3px solid #dc2626;">
                <div class="poc-pillar-tag" style="color: #dc2626;">
                    {{ $isEn ? '06 // RED-TEAM SECURITY, DAY-2 OPS & DAY-30+ MCP v4.0' : '06 // KEAMANAN RED-TEAM, DAY-2 OPS & DAY-30+ MCP v4.0' }}
                </div>
                <div class="poc-pillar-title">
                    {{ $isEn ? 'Phase 7 Red-Team, Delta v1.1, Decision Matrix match() & Live MCP' : 'Fase 7 Red-Team, Delta v1.1, Decision Matrix match() & Live MCP' }}
                </div>
                <div class="poc-pillar-desc">
                    {{ $isEn 
                        ? 'Phase 7 Tenant-Leakage Red-Team tests, Surgical Delta CR (v1.0 -> v1.1), Decision Matrix PHP match(), .env.blueprint (pos:check-env), Live MCP Server Bridge (neriahpro-mcp), and 3-Pane Studio.' 
                        : 'Uji penetrasi otomatis Fase 7 (IDOR & Prompt Injection), evolusi Delta aditif v1.1, Tabel Keputusan PHP match(), brankas .env.blueprint, jembatan MCP langsung ke IDE, dan 3-Pane Studio.' }}
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
