<x-filament-panels::page.simple>
    <style>
        .np-login-split-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            width: 100%;
            margin: 0 auto;
        }
        @media (min-width: 1024px) {
            .np-login-split-container {
                grid-template-columns: 1.25fr 1fr;
                gap: 2.25rem;
                align-items: stretch;
            }
        }
        .np-showcase-pane {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.75rem;
            border-radius: 1rem;
            background: linear-gradient(145deg, #18181b 0%, #09090b 100%);
            border: 1px solid #27272a;
            color: #f4f4f5;
            text-align: left;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        }
        .np-form-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1.75rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid #e4e4e7;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            text-align: left;
        }
        .dark .np-form-card {
            background: rgba(24, 24, 27, 0.95);
            border-color: #27272a;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
        }
    </style>

    <div class="w-full">
        <div class="np-login-split-container">
            <!-- Left Pane: Enterprise Architectural Showcase (Desktop Landscape Hero) -->
            <div class="np-showcase-pane">
                <div>
                    <!-- Status Header -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #27272a;">
                        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; font-family: monospace; font-size: 10px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                            <span style="width: 8px; height: 8px; border-radius: 9999px; background: #34d399; display: inline-block;"></span>
                            NERIAH PRO // ARCHITECTURAL HUB
                        </div>
                        <span style="font-family: monospace; font-size: 10px; color: #a1a1aa;">
                            v2.5 PROD
                        </span>
                    </div>

                    <!-- Vision & Mission Headline -->
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 900; line-height: 1.25; color: #ffffff; letter-spacing: -0.025em; margin: 0 0 0.5rem 0; text-transform: uppercase;">
                            Architecting High-Performance <span style="color: #f59e0b;">Digital Platforms</span> & Scope Lock OS
                        </h2>
                        <p style="font-size: 0.8125rem; line-height: 1.55; color: #a1a1aa; margin: 0;">
                            Pusat komando terpusat untuk perumusan otomatis arsitektur perangkat lunak berstandar enterprise, eliminasi mutlak scope creep lewat kontrak digital mengikat, dan akselerasi karir cerdas CV Pro Studio.
                        </p>
                    </div>

                    <!-- 3 Core Architectural Pillars -->
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <!-- Pillar 1 -->
                        <div style="padding: 0.75rem 0.875rem; border-radius: 0.625rem; background: rgba(39, 39, 42, 0.6); border: 1px solid #3f3f46; display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div style="padding: 0.375rem; border-radius: 0.375rem; background: rgba(245, 158, 11, 0.2); color: #fbbf24; flex-shrink: 0; margin-top: 0.125rem;">
                                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 0.8125rem; font-weight: 700; color: #f4f4f5; font-family: monospace;">
                                    1. Vision Blueprint & Ultimate PRD
                                </div>
                                <div style="font-size: 0.75rem; color: #a1a1aa; line-height: 1.4; margin-top: 0.125rem;">
                                    Sintesis PRD otomatis, diagram arsitektur sistem, skema basis data terdistribusi, dan performa tinggi skala enterprise.
                                </div>
                            </div>
                        </div>

                        <!-- Pillar 2 -->
                        <div style="padding: 0.75rem 0.875rem; border-radius: 0.625rem; background: rgba(39, 39, 42, 0.6); border: 1px solid #3f3f46; display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div style="padding: 0.375rem; border-radius: 0.375rem; background: rgba(245, 158, 11, 0.2); color: #fbbf24; flex-shrink: 0; margin-top: 0.125rem;">
                                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 0.8125rem; font-weight: 700; color: #f4f4f5; font-family: monospace;">
                                    2. Scope Lock OS & Digital Escrow
                                </div>
                                <div style="font-size: 0.75rem; color: #a1a1aa; line-height: 1.4; margin-top: 0.125rem;">
                                    Pemberantasan scope creep lewat kontrak berpayung hukum, milestone escrow DP Midtrans, dan SHA-256 audit.
                                </div>
                            </div>
                        </div>

                        <!-- Pillar 3 -->
                        <div style="padding: 0.75rem 0.875rem; border-radius: 0.625rem; background: rgba(39, 39, 42, 0.6); border: 1px solid #3f3f46; display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div style="padding: 0.375rem; border-radius: 0.375rem; background: rgba(245, 158, 11, 0.2); color: #fbbf24; flex-shrink: 0; margin-top: 0.125rem;">
                                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"></path>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 0.8125rem; font-weight: 700; color: #f4f4f5; font-family: monospace;">
                                    3. CV Pro Studio & AI Career Suite
                                </div>
                                <div style="font-size: 0.75rem; color: #a1a1aa; line-height: 1.4; margin-top: 0.125rem;">
                                    Microsoft MarkItDown multi-format scanner, ATS score linter, mock interview suara formula STAR, dan LinkedIn branding pack.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Security Note & Motto -->
                <div style="padding-top: 1rem; border-top: 1px solid #27272a; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-family: monospace; font-size: 10px; color: #10b981;">
                        <span style="width: 6px; height: 6px; border-radius: 9999px; background: #10b981; display: inline-block;"></span>
                        256-BIT ENCRYPTION &bull; RBAC AUDITED
                    </div>
                    <div style="font-size: 11px; font-style: italic; color: #71717a; font-family: monospace;">
                        &ldquo;Determinism over ambiguity. Scalability by design.&rdquo;
                    </div>
                </div>
            </div>

            <!-- Right Pane: Authentication Control Box -->
            <div class="np-form-card">
                <div style="margin-bottom: 1.25rem; text-align: center;">
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <div style="width: 36px; height: 36px; border-radius: 0.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: #09090b; font-family: monospace; font-weight: 900; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);">
                            NP
                        </div>
                        <div style="text-align: left;">
                            <div style="font-size: 1rem; font-weight: 900; line-height: 1; letter-spacing: -0.025em; text-transform: uppercase;" class="text-zinc-950 dark:text-white">
                                NERIAH<span style="color: #f59e0b;">PRO</span>
                            </div>
                            <div style="font-size: 9px; font-family: monospace; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #71717a;">
                                Scope Lock OS // Control Hub
                            </div>
                        </div>
                    </div>
                    <h1 style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.025em; margin: 0;" class="text-zinc-950 dark:text-white font-sans">
                        Neriah<span style="color: #f59e0b;">Pro</span> Control Hub
                    </h1>
                    <p style="font-size: 11px; color: #71717a; margin: 0.25rem 0 0 0;">
                        Masukkan kredensial terverifikasi untuk mengakses pusat kendali.
                    </p>
                </div>

                <!-- Form Content Rendered by Filament Livewire -->
                {{ $this->content }}
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
