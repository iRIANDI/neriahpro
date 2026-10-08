@php
    $isEn = app()->getLocale() === 'en';
@endphp
<x-filament-widgets::widget>
    <div style="background: linear-gradient(135deg, #09090b 0%, #18181b 100%); border: 1px solid #27272a; padding: 24px; position: relative; overflow: hidden; font-family: ui-sans-serif, system-ui, sans-serif;">
        <!-- Top Tech Glow Bar -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #10b981, #06b6d4, #8b5cf6, #f59e0b);"></div>

        <!-- Header Row -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 20px;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 0;"></span>
                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 11px; font-weight: 700; letter-spacing: 0.15em; color: #10b981; text-transform: uppercase;">
                        NERIAH PRO // PROJECT OS & DIGITAL ARCHITECTURE PLATFORM
                    </span>
                </div>
                <h2 style="font-size: 22px; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; margin: 0; text-transform: uppercase;">
                    {{ $isEn ? 'Software Engineering Architecture Platform & Project OS' : 'Platform Arsitektur Rekayasa Perangkat Lunak & Project OS' }}
                </h2>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #000000; background: #10b981; padding: 4px 10px; text-transform: uppercase;">
                    MIDTRANS COMPLIANCE READY
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #38bdf8; background: #0369a1; padding: 4px 10px; text-transform: uppercase;">
                    POSTGRESQL STRICT ULID
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #facc15; background: #854d0e; padding: 4px 10px; text-transform: uppercase;">
                    MARKITDOWN INGESTION
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #f87171; background: #7f1d1d; padding: 4px 10px; text-transform: uppercase;">
                    AI-SHIELD ACTIVE
                </span>
            </div>
        </div>

        <!-- Compliance & Core Purpose Statement -->
        <div style="background: rgba(24, 24, 27, 0.75); border: 1px solid #3f3f46; padding: 18px; margin-bottom: 22px;">
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #27272a; border: 1px solid #52525b; display: flex; align-items: center; justify-content: center; color: #10b981; font-weight: 900; font-family: ui-monospace, monospace; font-size: 14px;">
                    OS
                </div>
                <div style="font-size: 13px; line-height: 1.6; color: #d4d4d8;">
                    <p style="margin: 0 0 10px 0; font-weight: 700; color: #ffffff;">
                        {{ $isEn ? 'Digital Service Information & Midtrans Merchant Account Verification:' : 'Keterangan Layanan Digital & Verifikasi Akun Merchant Midtrans:' }}
                    </p>
                    <p style="margin: 0 0 10px 0;">
                        @if($isEn)
                            The <strong>neriahpro.com</strong> platform is the official operational instrument of <strong>Yoseph Iriandi Tambunan</strong> (Web Developer & Software Architect) for client system specification design (Product Requirements Document / PRD, Entity Relationship Diagram / ERD PostgreSQL Strict ULID, Tech Stack Selection, MVP Sprint Timeline, Scaffold Code Boilerplate .zip), SHA-256 hash-backed digital partnership agreements, and Down Payment billing (DP 50%) via the Midtrans Snap payment gateway.
                        @else
                            Platform <strong>neriahpro.com</strong> adalah instrumen operasional resmi milik <strong>Yoseph Iriandi Tambunan</strong> (Web Developer & Software Architect) untuk melayani klien dalam perancangan spesifikasi sistem (Product Requirements Document / PRD, Entity Relationship Diagram / ERD PostgreSQL Strict ULID, Pemilihan Tech Stack, Timeline Sprint MVP, Boilerplate Scaffold Kode .zip), pengikatan kontrak kerja sama digital berintegritas hash SHA-256, serta penagihan uang muka Down Payment (DP 50%) via payment gateway Midtrans Snap.
                        @endif
                    </p>
                    <p style="margin: 0; color: #a1a1aa;">
                        @if($isEn)
                            <strong style="color: #10b981;">Core Purpose & Scope Freeze:</strong> Ensuring zero assumption in engineering development, legally freezing project scope to prevent scope creep, and providing crystal-clear deliverables before any 50% Down Payment commitment via Midtrans Snap is processed.
                        @else
                            <strong style="color: #10b981;">Tujuan Utama & Scope Freeze:</strong> Memastikan seluruh pengerjaan kode tidak berasumsi atas kebutuhan bisnis klien, mengunci ruang lingkup pekerjaan (scope freeze) secara legal untuk mencegah penambahan lingkup tak terkendali (scope creep), serta memberikan dasar deliverable yang jelas dan transparan sebelum komitmen pembayaran Down Payment (DP 50%) via Midtrans Snap diproses.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 6 Core Pillars of Modern Project OS -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
            <!-- Pillar 1 -->
            <div style="background: #18181b; border: 1px solid #27272a; padding: 16px; border-left: 3px solid #10b981;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700; margin-bottom: 4px;">
                    {{ $isEn ? '01 // DISCOVERY & MARKITDOWN STUDIO' : '01 // DISCOVERY & STUDIO MARKITDOWN' }}
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $isEn ? 'Quick Idea Studio & Multi-Doc Ingestion' : 'Studio Ide Cepat & Ingesti Multi-Dokumen' }}
                </div>
                <div style="font-size: 11px; color: #a1a1aa; line-height: 1.45;">
                    {{ $isEn 
                        ? 'Brainstorm ideas via interactive textarea or attach project briefs (PDF, DOCX, XLSX, PPTX, CSV, TXT, Wireframe). Microsoft MarkItDown transforms documents into local server Markdown, saving >80% AI tokens.' 
                        : 'Curahkan ide via textarea interaktif atau lampirkan berkas (PDF, DOCX, XLSX, PPTX, CSV, TXT, Wireframe). Microsoft MarkItDown mengonversi berkas ke Markdown lokal di server guna menghemat >80% token AI.' }}
                </div>
            </div>

            <!-- Pillar 2 -->
            <div style="background: #18181b; border: 1px solid #27272a; padding: 16px; border-left: 3px solid #06b6d4;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #06b6d4; font-weight: 700; margin-bottom: 4px;">
                    {{ $isEn ? '02 // 26 ARCHITECTURAL PARAMETERS' : '02 // 26 PARAMETER ARSITEKTUR' }}
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $isEn ? 'Core 26-Parameter Architectural Questionnaire (Blocks A-F)' : 'Kuesioner 26 Parameter Arsitektur Inti (Blok A-F)' }}
                </div>
                <div style="font-size: 11px; color: #a1a1aa; line-height: 1.45;">
                    {{ $isEn 
                        ? 'Complete structuring: Business Identity, RBAC Matrix, Core MVP Features & Workflows, Gateway Integrations, UI/UX, Cloud VPS Hosting, Traffic Scale, OWASP Standards, and Budget & 30-Day SLA Bug Warranty.' 
                        : 'Strukturisasi lengkap: Identitas Bisnis, Matriks Hak Akses RBAC, Fitur Inti MVP & Alur Kerja, Integrasi Gateway, UI/UX, Hosting Cloud VPS, Skala Trafik, Standar OWASP, hingga Anggaran & Garansi SLA Bug 30 Hari.' }}
                </div>
            </div>

            <!-- Pillar 3 -->
            <div style="background: #18181b; border: 1px solid #27272a; padding: 16px; border-left: 3px solid #8b5cf6;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #8b5cf6; font-weight: 700; margin-bottom: 4px;">
                    {{ $isEn ? '03 // ULID ERD & CODE SCAFFOLD' : '03 // ERD ULID & RANGKA KODE' }}
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $isEn ? 'Ultimate PRD, ERD & Boilerplate .zip' : 'PRD Mutakhir, ERD & Rangka Boilerplate .zip' }}
                </div>
                <div style="font-size: 11px; color: #a1a1aa; line-height: 1.45;">
                    {{ $isEn 
                        ? 'Synthesis of PRD documents, Mermaid Flowcharts & PostgreSQL Strict ULID ERDs, and 1-click scaffold boilerplate generator (.zip: docker-compose, openapi.json, SQL schema, Laravel 13 & Next.js routes).' 
                        : 'Sintesis dokumen PRD, visualisasi Mermaid Flowchart & PostgreSQL Strict ULID ERD, serta generator 1-klik paket rangka boilerplate (.zip: docker-compose, openapi.json, skema SQL, rute Laravel 13 & Next.js).' }}
                </div>
            </div>

            <!-- Pillar 4 -->
            <div style="background: #18181b; border: 1px solid #27272a; padding: 16px; border-left: 3px solid #f59e0b;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #f59e0b; font-weight: 700; margin-bottom: 4px;">
                    {{ $isEn ? '04 // SCOPE LOCK & E-SIGNATURE' : '04 // PENGUNCIAN RUANG LINGKUP & E-SIGNATURE' }}
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $isEn ? 'Digital Contract & Scope Freeze Protocol' : 'Kontrak Kerja Digital & Protokol Scope Freeze' }}
                </div>
                <div style="font-size: 11px; color: #a1a1aa; line-height: 1.45;">
                    {{ $isEn 
                        ? 'Automated issuance of official partnership agreements with SHA-256 hash encryption, scope lock (scope freeze), and isolated Change Request (CR) clauses to protect both parties from scope creep.' 
                        : 'Penerbitan surat perjanjian kerja sama resmi otomatis dengan enkripsi hash SHA-256, penguncian ruang lingkup (scope freeze), dan klausul Change Request (CR) terpisah guna melindungi kedua belah pihak dari penambahan lingkup tak terkendali (scope creep).' }}
                </div>
            </div>

            <!-- Pillar 5 -->
            <div style="background: #18181b; border: 1px solid #27272a; padding: 16px; border-left: 3px solid #10b981;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700; margin-bottom: 4px;">
                    {{ $isEn ? '05 // MIDTRANS SNAP & CART HOLD' : '05 // MIDTRANS SNAP & RESERVASI SLOT' }}
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $isEn ? 'Midtrans Snap Settlement & Anti-Ghost Hold' : 'Pembayaran Midtrans Snap & Reservasi Anti-Ghost' }}
                </div>
                <div style="font-size: 11px; color: #a1a1aa; line-height: 1.45;">
                    {{ $isEn 
                        ? 'Legally binding 50% Down Payment billing tied directly to the contract document number, backed by 100% bypass promo vouchers (Rp 0) for instant preview and a 24-hour Anti-Ghost Cart hold reservation.' 
                        : 'Penagihan Uang Muka (DP) 50% yang sah dan terikat dengan nomor dokumen kontrak, didukung voucher promo bypass 100% (Rp 0) untuk review instan dan reservasi slot Anti-Ghost Hold 24 jam.' }}
                </div>
            </div>

            <!-- Pillar 6 -->
            <div style="background: #18181b; border: 1px solid #27272a; padding: 16px; border-left: 3px solid #ef4444;">
                <div style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #ef4444; font-weight: 700; margin-bottom: 4px;">
                    {{ $isEn ? '06 // ACTIVE CYBER DEFENSE' : '06 // PERTAHANAN SIBER AKTIF' }}
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $isEn ? 'AI-Shield & Secure Ingestion Pipeline' : 'AI-Shield & Jalur Ingesti Aman' }}
                </div>
                <div style="font-size: 11px; color: #a1a1aa; line-height: 1.45;">
                    {{ $isEn 
                        ? 'Active internal firewall preventing server-level RCE, dataset loader vulnerability mitigations (Exploit Gym / Hugging Face incident vectors), in-memory finfo_buffer file upload isolation, and automatic attacker IP quarantine.' 
                        : 'Firewall internal aktif pencegah RCE tingkat server, mitigasi celah dataset loader (vektor insiden Exploit Gym / Hugging Face), isolasi unggahan berkas via finfo_buffer in-memory, dan karantina otomatis IP penyerang.' }}
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
