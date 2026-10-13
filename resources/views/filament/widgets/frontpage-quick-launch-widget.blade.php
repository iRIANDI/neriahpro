@php
    $user = filament()->auth()->user();
    $isMidtransReviewer = $user && ($user->hasRole('midtrans_reviewer') || $user->email === 'reviewer.midtrans@neriahpro.com');
    $adminBase = '/' . trim(config('app.admin_path', 'admin'), '/');
@endphp

<x-filament-widgets::widget>
    <style>
        .fql-container {
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            border-top: 3px solid #059669;
            padding: 24px;
            position: relative;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .fql-container {
            background-color: #09090b;
            border-color: #27272a;
            border-top-color: #10b981;
        }

        .fql-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e4e4e7;
        }
        .dark .fql-header {
            border-bottom-color: #27272a;
        }

        .fql-sublabel {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.15em;
            color: #065f46;
            text-transform: uppercase;
        }
        .dark .fql-sublabel {
            color: #10b981;
        }

        .fql-title {
            font-size: 18px;
            font-weight: 900;
            color: #09090b;
            letter-spacing: -0.02em;
            margin: 4px 0 0 0;
            text-transform: uppercase;
        }
        .dark .fql-title {
            color: #ffffff;
        }

        .fql-subtitle {
            font-size: 12px;
            color: #475569;
            margin: 4px 0 0 0;
            line-height: 1.4;
        }
        .dark .fql-subtitle {
            color: #a1a1aa;
        }

        .fql-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #059669;
            color: #000000;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 18px;
            text-decoration: none;
            border: 1px solid #047857;
            transition: background-color 0.15s ease;
        }
        .fql-btn-primary:hover {
            background-color: #10b981;
        }

        .fql-badges-wrap {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .fql-badge {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            text-transform: uppercase;
            border-radius: 0;
            letter-spacing: 0.05em;
        }

        .fql-badge-emerald {
            background-color: #d1fae5;
            color: #064e3b;
            border: 1px solid #34d399;
        }
        .dark .fql-badge-emerald {
            background-color: rgba(6, 78, 59, 0.7);
            color: #a7f3d0;
            border-color: #059669;
        }

        .fql-badge-sky {
            background-color: #e0f2fe;
            color: #0c4a6e;
            border: 1px solid #38bdf8;
        }
        .dark .fql-badge-sky {
            background-color: rgba(12, 74, 110, 0.7);
            color: #bae6fd;
            border-color: #0284c7;
        }

        .fql-badge-amber {
            background-color: #fef3c7;
            color: #78350f;
            border: 1px solid #fbbf24;
        }
        .dark .fql-badge-amber {
            background-color: rgba(120, 53, 15, 0.7);
            color: #fde68a;
            border-color: #d97706;
        }

        .fql-badge-purple {
            background-color: #f3e8ff;
            color: #581c87;
            border: 1px solid #c084fc;
        }
        .dark .fql-badge-purple {
            background-color: rgba(88, 28, 135, 0.7);
            color: #e9d5ff;
            border-color: #9333ea;
        }

        .fql-badge-rose {
            background-color: #ffe4e6;
            color: #881337;
            border: 1px solid #fb7185;
        }
        .dark .fql-badge-rose {
            background-color: rgba(136, 19, 55, 0.7);
            color: #fecdd3;
            border-color: #e11d48;
        }

        .fql-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .fql-card {
            display: block;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 16px;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }
        .dark .fql-card {
            background-color: #18181b;
            border-color: #27272a;
        }

        .fql-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .fql-card-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
        }

        .fql-card-title {
            font-size: 14px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 4px;
        }
        .dark .fql-card-title {
            color: #ffffff;
        }

        .fql-card-desc {
            font-size: 11px;
            color: #475569;
            line-height: 1.45;
        }
        .dark .fql-card-desc {
            color: #a1a1aa;
        }

        .fql-session-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding-top: 18px;
            border-top: 1px solid #e4e4e7;
        }
        .dark .fql-session-bar {
            border-top-color: #27272a;
        }

        .fql-avatar {
            width: 34px;
            height: 34px;
            background-color: #e2e8f0;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: ui-monospace, Menlo, Monaco, monospace;
            font-weight: 900;
            font-size: 13px;
            color: #065f46;
        }
        .dark .fql-avatar {
            background-color: #27272a;
            border-color: #3f3f46;
            color: #10b981;
        }

        .fql-user-name {
            font-size: 12px;
            font-weight: 800;
            color: #09090b;
        }
        .dark .fql-user-name {
            color: #ffffff;
        }

        .fql-user-email {
            font-size: 11px;
            font-family: ui-monospace, monospace;
            color: #64748b;
        }
        .dark .fql-user-email {
            color: #71717a;
        }

        .fql-signout-btn {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 800;
            padding: 8px 14px;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .fql-signout-btn:hover {
            background-color: #e2e8f0;
        }
        .dark .fql-signout-btn {
            background-color: #27272a;
            color: #d4d4d8;
            border-color: #3f3f46;
        }
        .dark .fql-signout-btn:hover {
            background-color: #3f3f46;
            color: #ffffff;
        }
    </style>

    <div class="fql-container">
        <!-- Banner & Primary Frontpage Button -->
        <div class="fql-header">
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background-color: #059669;"></span>
                    <span class="fql-sublabel">
                        {{ $isMidtransReviewer ? 'PORTAL REVIEWER & WORKSPACE PROJECT OS' : 'PORTAL PUBLIK & AKSES CEPAT FRONTPAGE' }}
                    </span>
                </div>
                <h3 class="fql-title">
                    {{ $isMidtransReviewer ? 'Akses Cepat Pengujian Layanan Project OS' : 'Akses Langsung Halaman Publik Website Neriah Pro' }}
                </h3>
                <p class="fql-subtitle">
                    {{ $isMidtransReviewer ? 'Tautan langsung untuk memverifikasi alur Product Requirements Document (PRD), perancangan ERD PostgreSQL ULID, kontrak kerja sama digital, dan penagihan DP Midtrans Snap.' : 'Navigasi sekali klik untuk membuka, meninjau, dan menguji seluruh antarmuka publik live tanpa keluar dari sesi admin.' }}
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if ($isMidtransReviewer)
                    <a href="/blueprint" target="_blank" rel="noopener noreferrer" class="fql-btn-primary">
                        <span>Uji Alur Kuesioner Blueprint (/blueprint)</span>
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @else
                    <a href="/" target="_blank" rel="noopener noreferrer" class="fql-btn-primary">
                        <span>Buka Beranda Utama Website (neriahpro.com)</span>
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Telemetry Badges -->
        <div class="fql-badges-wrap">
            @if ($isMidtransReviewer)
                <span class="fql-badge fql-badge-emerald">
                    ● MIDTRANS COMPLIANCE: VERIFIED
                </span>
                <span class="fql-badge fql-badge-sky">
                    SCOPE: PROJECT OS & PRD ONLY
                </span>
                <span class="fql-badge fql-badge-amber">
                    POSTGRESQL STRICT ULID
                </span>
                <span class="fql-badge fql-badge-purple">
                    MARKITDOWN INGESTION ACTIVE
                </span>
                <span class="fql-badge fql-badge-rose">
                    CYBER SHIELD: ACTIVE
                </span>
            @else
                <span class="fql-badge fql-badge-emerald">
                    ● LIVE PORTAL: ONLINE
                </span>
                <span class="fql-badge fql-badge-sky">
                    MULTI-LOCALE: ID / EN
                </span>
                <span class="fql-badge fql-badge-purple">
                    FRONTEND: REACT 19 ISLANDS
                </span>
                <span class="fql-badge fql-badge-amber">
                    O(1) CURSOR PAGINATION
                </span>
                <span class="fql-badge fql-badge-rose">
                    CYBER SHIELD: ACTIVE
                </span>
            @endif
        </div>

        <!-- Direct Action Cards Grid -->
        <div class="fql-cards-grid">
            @if ($isMidtransReviewer)
                <!-- Reviewer Card 1: Project OS Kuesioner -->
                <a href="/blueprint" target="_blank" rel="noopener noreferrer" class="fql-card" onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #059669;">PROYEK & SPESIFIKASI</span>
                        <span style="font-size: 12px; color: #059669;">↗</span>
                    </div>
                    <div class="fql-card-title">Kuesioner Blueprint & PRD</div>
                    <div class="fql-card-desc">Studio kuesioner 26 parameter, multi-doc ingestion MarkItDown, dan sintesis PRD.</div>
                </a>

                <!-- Reviewer Card 2: Vision Blueprints Admin List -->
                <a href="{{ $adminBase }}/vision-blueprints" class="fql-card" onmouseover="this.style.borderColor='#0284c7'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #0284c7;">PROJECT BLUEPRINTS</span>
                        <span style="font-size: 12px; color: #0284c7;">→</span>
                    </div>
                    <div class="fql-card-title">Daftar Blueprint Proyek</div>
                    <div class="fql-card-desc">Data komprehensif submission blueprint klien, ringkasan eksekutif, dan status penguncian scope.</div>
                </a>

                <!-- Reviewer Card 3: Digital Contracts -->
                <a href="{{ $adminBase }}/documents" class="fql-card" onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #059669;">KONTRAK & LEGALITAS</span>
                        <span style="font-size: 12px; color: #059669;">→</span>
                    </div>
                    <div class="fql-card-title">Kontrak Perjanjian Digital</div>
                    <div class="fql-card-desc">Surat perjanjian kerja sama digital berkekuatan hukum, klausul garansi, tanda tangan digital, & hash SHA-256.</div>
                </a>

                <!-- Reviewer Card 4: Midtrans Transactions -->
                <a href="{{ $adminBase }}/transactions" class="fql-card" onmouseover="this.style.borderColor='#d97706'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #d97706;">MIDTRANS SETTLEMENT</span>
                        <span style="font-size: 12px; color: #d97706;">→</span>
                    </div>
                    <div class="fql-card-title">Riwayat Transaksi Midtrans</div>
                    <div class="fql-card-desc">Audit mutasi pembayaran DP klien, order ID Midtrans Snap, status settlement, dan total IDR.</div>
                </a>

                <!-- Reviewer Card 5: Products & Services Catalog -->
                <a href="{{ $adminBase }}/products" class="fql-card" onmouseover="this.style.borderColor='#0284c7'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #0284c7;">COMMERCE & BILLING</span>
                        <span style="font-size: 12px; color: #0284c7;">→</span>
                    </div>
                    <div class="fql-card-title">Katalog Produk & Layanan</div>
                    <div class="fql-card-desc">Paket jasa web development resmi (Modern Monolith, PRD Architecture, Contract E-Sign) & tarif IDR.</div>
                </a>

                <!-- Reviewer Card 6: Legal Policies & Refund -->
                <a href="{{ $adminBase }}/legal-policies" class="fql-card" onmouseover="this.style.borderColor='#7c3aed'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #7c3aed;">KEPATUHAN REGULASI</span>
                        <span style="font-size: 12px; color: #7c3aed;">→</span>
                    </div>
                    <div class="fql-card-title">Legalitas & Kebijakan Refund</div>
                    <div class="fql-card-desc">Syarat & Ketentuan kontrak kerja sama, Kebijakan Privasi, dan Kebijakan Pembatalan / Pengembalian DP.</div>
                </a>

                <!-- Reviewer Card 7: Domain & Hosting Assets -->
                <a href="{{ $adminBase }}/domain-hosting-assets" class="fql-card" onmouseover="this.style.borderColor='#0284c7'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #0284c7;">INFRASTRUKTUR PROYEK</span>
                        <span style="font-size: 12px; color: #0284c7;">→</span>
                    </div>
                    <div class="fql-card-title">Aset Domain & Server VPS</div>
                    <div class="fql-card-desc">Pencatatan alokasi domain, hosting cloud VPS, sertifikat SSL, dan masa perpanjangan server klien.</div>
                </a>

                <!-- Reviewer Card 8: CRM Leads -->
                <a href="{{ $adminBase }}/lead-contacts" class="fql-card" onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #059669;">LEAD INTAKE CRM</span>
                        <span style="font-size: 12px; color: #059669;">→</span>
                    </div>
                    <div class="fql-card-title">Database CRM Leads Klien</div>
                    <div class="fql-card-desc">Data intake calon klien yang mengajukan konsultasi arsitektur web development via formulir onboarding.</div>
                </a>
            @else
                <!-- Superadmin Card 1: Project OS -->
                <a href="/blueprint" target="_blank" rel="noopener noreferrer" class="fql-card" onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #059669;">PROYEK & SPESIFIKASI</span>
                        <span style="font-size: 12px; color: #059669;">↗</span>
                    </div>
                    <div class="fql-card-title">Project OS / PRD Workspace</div>
                    <div class="fql-card-desc">Studio kuesioner 26 parameter, upload dokumen MarkItDown, dan sintesis Ultimate PRD.</div>
                </a>

                <!-- Superadmin Card 2: CV Pro -->
                <a href="/cv-pro" target="_blank" rel="noopener noreferrer" class="fql-card" onmouseover="this.style.borderColor='#7c3aed'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #7c3aed;">KARIR & SAAS</span>
                        <span style="font-size: 12px; color: #7c3aed;">↗</span>
                    </div>
                    <div class="fql-card-title">Studio CV Pro Enterprise</div>
                    <div class="fql-card-desc">Visual Resume ATS, Job Hub Kanban, Mock Interview AI, Keuangan Pro, & LinkedIn suite.</div>
                </a>

                <!-- Superadmin Card 3: Pricing -->
                <a href="/pricing" target="_blank" rel="noopener noreferrer" class="fql-card" onmouseover="this.style.borderColor='#0284c7'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #0284c7;">BILLING & PAKET</span>
                        <span style="font-size: 12px; color: #0284c7;">↗</span>
                    </div>
                    <div class="fql-card-title">Katalog Harga & Kuota</div>
                    <div class="fql-card-desc">Katalog paket langganan CV Pro, kuota a la carte, dan transparansi biaya API AI.</div>
                </a>

                <!-- Superadmin Card 4: Cart -->
                <a href="/cart" target="_blank" rel="noopener noreferrer" class="fql-card" onmouseover="this.style.borderColor='#d97706'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #d97706;">COMMERCE & CHECKOUT</span>
                        <span style="font-size: 12px; color: #d97706;">↗</span>
                    </div>
                    <div class="fql-card-title">Cart & Anti-Ghost Hold</div>
                    <div class="fql-card-desc">Keranjang belanja layanan, timer reservasi slot pengerjaan DP, & checkout Midtrans Snap.</div>
                </a>

                <!-- Superadmin Card 5: Client Onboarding -->
                <a href="/#onboarding" target="_blank" rel="noopener noreferrer" class="fql-card" onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #059669;">LEAD INTAKE</span>
                        <span style="font-size: 12px; color: #059669;">↗</span>
                    </div>
                    <div class="fql-card-title">Form Onboarding Klien</div>
                    <div class="fql-card-desc">Antarmuka kuesioner intake cepat kebutuhan bisnis dan identitas PIC calon klien.</div>
                </a>

                <!-- Superadmin Card 6: Security Threats -->
                <a href="{{ $adminBase }}/security-threats" class="fql-card" onmouseover="this.style.borderColor='#dc2626'" onmouseout="this.style.borderColor=''">
                    <div class="fql-card-top">
                        <span class="fql-card-tag" style="color: #dc2626;">SECURITY AUDIT</span>
                        <span style="font-size: 12px; color: #dc2626;">→</span>
                    </div>
                    <div class="fql-card-title">AI Threat Shield Dashboard</div>
                    <div class="fql-card-desc">Forensik intrusi siber, log pencegatan RCE/Dataset Exploit, & manajemen pemblokiran IP.</div>
                </a>
            @endif
        </div>

        <!-- Bottom Session Bar -->
        <div class="fql-session-bar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="fql-avatar">
                    {{ strtoupper(substr($user?->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div class="fql-user-name">
                        {{ $user?->name ?? 'Administrator' }}
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 9px; font-weight: 800; color: #065f46; background-color: #d1fae5; border: 1px solid #34d399; padding: 2px 6px; margin-left: 6px; text-transform: uppercase;">
                            {{ $isMidtransReviewer ? 'MIDTRANS REVIEWER' : 'SUPER ADMIN' }}
                        </span>
                    </div>
                    <div class="fql-user-email">
                        {{ $user?->email ?? \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com') }} • Timezone: {{ config('app.timezone', 'Asia/Jakarta') }}
                    </div>
                </div>
            </div>

            <form action="{{ filament()->getLogoutUrl() }}" method="post" style="margin: 0;">
                @csrf
                <button type="submit" class="fql-signout-btn">
                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Sign Out (Keluar)</span>
                </button>
            </form>
        </div>
    </div>
</x-filament-widgets::widget>
