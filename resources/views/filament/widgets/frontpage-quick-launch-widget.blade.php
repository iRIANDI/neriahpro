@php
    $user = filament()->auth()->user();
    $isMidtransReviewer = $user && ($user->hasRole('midtrans_reviewer') || $user->email === 'reviewer.midtrans@neriahpro.com');
@endphp

<x-filament-widgets::widget>
    <div style="background: linear-gradient(135deg, #09090b 0%, #18181b 100%); border: 1px solid #27272a; padding: 24px; position: relative; overflow: hidden; font-family: ui-sans-serif, system-ui, sans-serif;">
        <!-- Top Accent Line -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #10b981, #06b6d4, #6366f1);"></div>

        <!-- Banner & Primary Frontpage Button -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 22px; padding-bottom: 20px; border-bottom: 1px solid #27272a;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 0;"></span>
                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 11px; font-weight: 700; letter-spacing: 0.15em; color: #10b981; text-transform: uppercase;">
                        {{ $isMidtransReviewer ? 'PORTAL REVIEWER & WORKSPACE PROJECT OS' : 'PORTAL PUBLIK & AKSES CEPAT FRONTPAGE' }}
                    </span>
                </div>
                <h3 style="font-size: 18px; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; margin: 0; text-transform: uppercase;">
                    {{ $isMidtransReviewer ? 'Akses Cepat Pengujian Layanan Project OS' : 'Akses Langsung Halaman Publik Website Neriah Pro' }}
                </h3>
                <p style="font-size: 12px; color: #a1a1aa; margin: 4px 0 0 0;">
                    {{ $isMidtransReviewer ? 'Tautan langsung untuk memverifikasi alur Product Requirements Document (PRD), perancangan ERD PostgreSQL ULID, kontrak kerja sama digital, dan penagihan DP Midtrans Snap.' : 'Navigasi sekali klik untuk membuka, meninjau, dan menguji seluruh antarmuka publik live tanpa keluar dari sesi admin.' }}
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if ($isMidtransReviewer)
                    <a href="/blueprint" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 8px; background: #10b981; color: #000000; font-family: ui-monospace, SFMono-Regular, monospace; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 18px; text-decoration: none; border: 1px solid #059669; transition: all 0.2s ease;">
                        <span>Uji Alur Kuesioner Blueprint (/blueprint)</span>
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @else
                    <a href="/" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 8px; background: #10b981; color: #000000; font-family: ui-monospace, SFMono-Regular, monospace; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 18px; text-decoration: none; border: 1px solid #059669; transition: all 0.2s ease;">
                        <span>Buka Beranda Utama Website (neriahpro.com)</span>
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Telemetry Badges -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
            @if ($isMidtransReviewer)
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    ● MIDTRANS COMPLIANCE: VERIFIED
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #38bdf8; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    SCOPE: PROJECT OS & PRD ONLY
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #facc15; background: rgba(250, 204, 21, 0.1); border: 1px solid rgba(250, 204, 21, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    POSTGRESQL STRICT ULID
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #a855f7; background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    MARKITDOWN INGESTION ACTIVE
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #f87171; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    CYBER SHIELD: ACTIVE
                </span>
            @else
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    ● LIVE PORTAL: ONLINE
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #38bdf8; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    MULTI-LOCALE: ID / EN
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #c084fc; background: rgba(192, 132, 252, 0.1); border: 1px solid rgba(192, 132, 252, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    FRONTEND: REACT 19 ISLANDS
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #facc15; background: rgba(250, 204, 21, 0.1); border: 1px solid rgba(250, 204, 21, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    O(1) CURSOR PAGINATION
                </span>
                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; font-weight: 700; color: #f87171; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.3); padding: 4px 8px; text-transform: uppercase;">
                    CYBER SHIELD: ACTIVE
                </span>
            @endif
        </div>

        <!-- 6 Direct Action Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px; margin-bottom: 22px;">
            @if ($isMidtransReviewer)
                <!-- Reviewer Card 1: Project OS Kuesioner -->
                <a href="/blueprint" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700;">PROYEK & SPESIFIKASI</span>
                        <span style="font-size: 12px; color: #10b981;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Kuesioner Blueprint & PRD</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Studio kuesioner 26 parameter, multi-doc ingestion MarkItDown, dan sintesis PRD.</div>
                </a>

                <!-- Reviewer Card 2: Vision Blueprints Admin List -->
                <a href="/admin/vision-blueprints" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#38bdf8'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #38bdf8; font-weight: 700;">PROJECT BLUEPRINTS</span>
                        <span style="font-size: 12px; color: #38bdf8;">→</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Daftar Blueprint Proyek</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Data komprehensif seluruh submission blueprint, ringkasan eksekutif, dan status penguncian scope.</div>
                </a>

                <!-- Reviewer Card 3: Digital Contracts -->
                <a href="/admin/documents" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700;">KONTRAK & LEGALITAS</span>
                        <span style="font-size: 12px; color: #10b981;">→</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Kontrak Perjanjian Digital</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Surat perjanjian kerja sama digital berkekuatan hukum, tanda tangan elektronik, & hash SHA-256.</div>
                </a>

                <!-- Reviewer Card 4: Domain & Hosting Assets -->
                <a href="/admin/domain-hosting-assets" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#a855f7'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #a855f7; font-weight: 700;">INFRASTRUKTUR PROYEK</span>
                        <span style="font-size: 12px; color: #a855f7;">→</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Aset Domain & Server VPS</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Pencatatan alokasi domain, hosting cloud VPS, sertifikat SSL, dan masa perpanjangan server klien.</div>
                </a>

                <!-- Reviewer Card 5: Cart & Midtrans Snap -->
                <a href="/cart" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#f59e0b'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #f59e0b; font-weight: 700;">COMMERCE & CHECKOUT</span>
                        <span style="font-size: 12px; color: #f59e0b;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Cart DP & Snap Settlement</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Alur pembayaran uang muka (DP 50%) proyek via Midtrans Snap terikat dengan dokumen kontrak.</div>
                </a>

                <!-- Reviewer Card 6: Onboarding Form -->
                <a href="/#onboarding" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700;">LEAD INTAKE</span>
                        <span style="font-size: 12px; color: #10b981;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Form Onboarding Klien</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Antarmuka kuesioner intake cepat kebutuhan bisnis dan identitas PIC calon klien.</div>
                </a>
            @else
                <!-- Superadmin Card 1: Project OS -->
                <a href="/blueprint" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700;">PROYEK & SPESIFIKASI</span>
                        <span style="font-size: 12px; color: #10b981;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Project OS / PRD Workspace</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Studio kuesioner 26 parameter, upload dokumen MarkItDown, dan sintesis Ultimate PRD.</div>
                </a>

                <!-- Superadmin Card 2: CV Pro -->
                <a href="/cv-pro" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #8b5cf6; font-weight: 700;">KARIR & SAAS</span>
                        <span style="font-size: 12px; color: #8b5cf6;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Studio CV Pro Enterprise</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Visual Resume ATS, Job Hub Kanban, Mock Interview AI, Keuangan Pro, & LinkedIn suite.</div>
                </a>

                <!-- Superadmin Card 3: Pricing -->
                <a href="/pricing" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#06b6d4'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #06b6d4; font-weight: 700;">BILLING & PAKET</span>
                        <span style="font-size: 12px; color: #06b6d4;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Katalog Harga & Kuota</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Katalog paket langganan CV Pro, kuota a la carte, dan transparansi biaya API AI.</div>
                </a>

                <!-- Superadmin Card 4: Cart -->
                <a href="/cart" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#f59e0b'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #f59e0b; font-weight: 700;">COMMERCE & CHECKOUT</span>
                        <span style="font-size: 12px; color: #f59e0b;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Cart & Anti-Ghost Hold</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Keranjang belanja layanan, timer reservasi slot pengerjaan DP, & checkout Midtrans Snap.</div>
                </a>

                <!-- Superadmin Card 5: Client Onboarding -->
                <a href="/#onboarding" target="_blank" rel="noopener noreferrer" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #10b981; font-weight: 700;">LEAD INTAKE</span>
                        <span style="font-size: 12px; color: #10b981;">↗</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Form Onboarding Klien</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Antarmuka kuesioner intake cepat kebutuhan bisnis dan identitas PIC calon klien.</div>
                </a>

                <!-- Superadmin Card 6: Security Threats -->
                <a href="/admin/security-threats" style="display: block; background: #18181b; border: 1px solid #27272a; padding: 16px; text-decoration: none; transition: border-color 0.2s ease;" onmouseover="this.style.borderColor='#ef4444'" onmouseout="this.style.borderColor='#27272a'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 10px; color: #ef4444; font-weight: 700;">SECURITY AUDIT</span>
                        <span style="font-size: 12px; color: #ef4444;">→</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-bottom: 4px;">AI Threat Shield Dashboard</div>
                    <div style="font-size: 11px; color: #a1a1aa; line-height: 1.4;">Forensik intrusi siber, log pencegatan RCE/Dataset Exploit, & manajemen pemblokiran IP.</div>
                </a>
            @endif
        </div>

        <!-- Bottom Session Bar -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; padding-top: 18px; border-top: 1px solid #27272a;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 34px; height: 34px; background: #27272a; border: 1px solid #3f3f46; display: flex; align-items: center; justify-content: center; font-family: ui-monospace, monospace; font-weight: 900; font-size: 13px; color: #10b981;">
                    {{ strtoupper(substr($user?->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #ffffff;">
                        {{ $user?->name ?? 'Administrator' }}
                        <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 9px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 2px 6px; margin-left: 6px; text-transform: uppercase;">
                            {{ $isMidtransReviewer ? 'MIDTRANS REVIEWER' : 'SUPER ADMIN' }}
                        </span>
                    </div>
                    <div style="font-size: 11px; font-family: ui-monospace, monospace; color: #71717a;">
                        {{ $user?->email ?? 'admin@neriahpro.com' }} • Timezone: {{ config('app.timezone', 'Asia/Jakarta') }}
                    </div>
                </div>
            </div>

            <form action="{{ filament()->getLogoutUrl() }}" method="post" style="margin: 0;">
                @csrf
                <button type="submit" style="background: #27272a; hover:background: #3f3f46; color: #d4d4d8; border: 1px solid #3f3f46; font-family: ui-monospace, SFMono-Regular, monospace; font-size: 11px; font-weight: 700; padding: 8px 14px; text-transform: uppercase; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Sign Out (Keluar)</span>
                </button>
            </form>
        </div>
    </div>
</x-filament-widgets::widget>
