<x-filament-panels::page>
<div class="smart-guide-root" x-data="smartGuideApp()" x-init="initScrollSpy()">

    <!-- Scoped Adaptive Styling for Filament v5 (Full Light & Dark Theme Fidelity) -->
    <style>
        .smart-guide-root {
            /* Light Theme Tokens (Default) */
            --sg-bg: #f8fafc;
            --sg-card-bg: #ffffff;
            --sg-subcard-bg: #f8fafc;
            --sg-border: #e2e8f0;
            --sg-border-subtle: #f1f5f9;
            --sg-text-title: #0f172a;
            --sg-text-body: #334155;
            --sg-text-muted: #64748b;
            --sg-nav-bg: rgba(255, 255, 255, 0.94);
            --sg-nav-border: #cbd5e1;
            --sg-nav-btn-color: #64748b;
            --sg-nav-btn-hover-bg: #f1f5f9;
            --sg-nav-btn-hover-color: #0f172a;
            --sg-search-bg: #ffffff;
            --sg-search-border: #cbd5e1;
            --sg-search-text: #0f172a;
            --sg-table-th-bg: #f1f5f9;
            --sg-table-th-text: #1e293b;
            --sg-table-td-border: #e2e8f0;
            --sg-table-td-text: #334155;
            --sg-table-tr-hover: rgba(0, 0, 0, 0.02);
            --sg-quote-bg: #f8fafc;
            --sg-quote-border: #e2e8f0;
            --sg-quote-text: #334155;
            --sg-trojan-bg: rgba(99, 102, 241, 0.05);
            --sg-trojan-border: rgba(99, 102, 241, 0.3);
            --sg-card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);

            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: var(--sg-text-body);
            background: var(--sg-bg);
            padding: 24px;
            border-radius: 4px;
            border: 1px solid var(--sg-border);
            position: relative;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }

        /* Dark Theme Tokens (Filament v5 dark mode integration) */
        :is(.dark, html.dark, body.dark) .smart-guide-root {
            --sg-bg: #09090b;
            --sg-card-bg: #121215;
            --sg-subcard-bg: #09090b;
            --sg-border: #27272a;
            --sg-border-subtle: #1f1f23;
            --sg-text-title: #ffffff;
            --sg-text-body: #e4e4e7;
            --sg-text-muted: #a1a1aa;
            --sg-nav-bg: rgba(18, 18, 21, 0.95);
            --sg-nav-border: #3f3f46;
            --sg-nav-btn-color: #a1a1aa;
            --sg-nav-btn-hover-bg: #27272a;
            --sg-nav-btn-hover-color: #ffffff;
            --sg-search-bg: #09090b;
            --sg-search-border: #3f3f46;
            --sg-search-text: #ffffff;
            --sg-table-th-bg: #09090b;
            --sg-table-th-text: #d4d4d8;
            --sg-table-td-border: #1f1f23;
            --sg-table-td-text: #e4e4e7;
            --sg-table-tr-hover: rgba(255, 255, 255, 0.02);
            --sg-quote-bg: #18181b;
            --sg-quote-border: #27272a;
            --sg-quote-text: #d4d4d8;
            --sg-trojan-bg: rgba(99, 102, 241, 0.04);
            --sg-trojan-border: rgba(99, 102, 241, 0.4);
            --sg-card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }

        .sg-card {
            background: var(--sg-card-bg);
            border: 1px solid var(--sg-border);
            border-radius: 4px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--sg-card-shadow);
            transition: border-color 0.2s, background-color 0.2s;
        }
        .sg-card:hover {
            border-color: #94a3b8;
        }
        :is(.dark, html.dark, body.dark) .sg-card:hover {
            border-color: #3f3f46;
        }

        .sg-subcard {
            background: var(--sg-subcard-bg);
            border: 1px solid var(--sg-border);
            border-radius: 3px;
            padding: 18px;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .sg-badge-confidential {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.35);
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        :is(.dark, html.dark, body.dark) .sg-badge-confidential {
            color: #f59e0b;
        }

        .sg-badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.35);
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 2px;
        }
        :is(.dark, html.dark, body.dark) .sg-badge-active {
            color: #10b981;
        }

        /* Floating Nav: Docks flush directly beneath Filament Topbar */
        .sg-floating-nav {
            position: sticky;
            top: var(--topbar-height, 4rem);
            z-index: 20;
            background: var(--sg-nav-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--sg-nav-border);
            border-radius: 0 0 4px 4px;
            padding: 8px 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            box-shadow: var(--sg-card-shadow);
            transition: background-color 0.2s, border-color 0.2s;
        }
        .sg-nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            max-width: 100%;
        }
        .sg-nav-btn {
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 2px;
            white-space: nowrap;
            background: transparent;
            color: var(--sg-nav-btn-color);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .sg-nav-btn:hover {
            color: var(--sg-nav-btn-hover-color);
            background: var(--sg-nav-btn-hover-bg);
        }
        .sg-nav-btn.active {
            color: #0284c7;
            background: rgba(14, 165, 233, 0.12);
            border-color: rgba(14, 165, 233, 0.4);
            font-weight: 700;
        }
        :is(.dark, html.dark, body.dark) .sg-nav-btn.active {
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.15);
            border-color: rgba(56, 189, 248, 0.4);
        }

        .sg-search-bar {
            display: flex;
            align-items: center;
            background: var(--sg-search-bg);
            border: 1px solid var(--sg-search-border);
            border-radius: 2px;
            padding: 4px 10px;
            width: 260px;
            transition: border-color 0.2s;
        }
        .sg-search-input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--sg-search-text);
            font-size: 12px;
            width: 100%;
        }
        .sg-lang-btn {
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 2px;
            cursor: pointer;
            border: 1px solid var(--sg-search-border);
            background: var(--sg-search-bg);
            color: var(--sg-text-muted);
            transition: all 0.15s;
        }
        .sg-lang-btn.active {
            background: #4f46e5;
            color: #ffffff;
            border-color: #6366f1;
        }

        .sg-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 16px;
        }
        .sg-table th {
            background: var(--sg-table-th-bg);
            color: var(--sg-table-th-text);
            font-family: ui-monospace, monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--sg-border);
        }
        .sg-table td {
            padding: 12px;
            border-bottom: 1px solid var(--sg-table-td-border);
            color: var(--sg-table-td-text);
        }
        .sg-table tr:hover td {
            background: var(--sg-table-tr-hover);
        }

        .sg-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .sg-accent-num {
            font-family: ui-monospace, monospace;
            font-size: 28px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 8px;
        }
        .sg-simulator-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 16px;
        }
        @media (max-width: 768px) {
            .sg-simulator-box {
                grid-template-columns: 1fr;
            }
            .sg-search-bar {
                width: 100%;
            }
        }
    </style>

    <!-- Top Floating Navigation with ScrollSpy & Search & Lang -->
    <div class="sg-floating-nav">
        <!-- Floating Index with ScrollSpy Effect -->
        <div class="sg-nav-links">
            <span style="font-size: 11px; font-family: ui-monospace, monospace; font-weight: 700; color: var(--sg-text-muted); margin-right: 4px;">INDEX:</span>
            <a href="#sec-overview" @click.prevent="scrollTo('sec-overview')" :class="activeSection === 'sec-overview' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                01. <span x-text="t[lang].nav_overview"></span>
            </a>
            <a href="#sec-cogs" @click.prevent="scrollTo('sec-cogs')" :class="activeSection === 'sec-cogs' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                02. <span x-text="t[lang].nav_cogs"></span>
            </a>
            <a href="#sec-pay-per-project" @click.prevent="scrollTo('sec-pay-per-project')" :class="activeSection === 'sec-pay-per-project' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                03. <span x-text="t[lang].nav_model"></span>
            </a>
            <a href="#sec-simulator" @click.prevent="scrollTo('sec-simulator')" :class="activeSection === 'sec-simulator' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                04. <span x-text="t[lang].nav_simulator"></span>
            </a>
            <a href="#sec-matrix" @click.prevent="scrollTo('sec-matrix')" :class="activeSection === 'sec-matrix' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                05. <span x-text="t[lang].nav_matrix"></span>
            </a>
            <a href="#sec-playbook" @click.prevent="scrollTo('sec-playbook')" :class="activeSection === 'sec-playbook' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                06. <span x-text="t[lang].nav_playbook"></span>
            </a>
            <a href="#sec-factory-pillars" @click.prevent="scrollTo('sec-factory-pillars')" :class="activeSection === 'sec-factory-pillars' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                07. <span x-text="t[lang].nav_factory_pillars"></span>
            </a>
            <a href="#sec-sectoral-ui" @click.prevent="scrollTo('sec-sectoral-ui')" :class="activeSection === 'sec-sectoral-ui' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                08. <span x-text="t[lang].nav_sectoral_ui"></span>
            </a>
            <a href="#sec-killer-moat" @click.prevent="scrollTo('sec-killer-moat')" :class="activeSection === 'sec-killer-moat' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                09. <span x-text="t[lang].nav_killer_moat"></span>
            </a>
            <a href="#sec-day2-ultimate" @click.prevent="scrollTo('sec-day2-ultimate')" :class="activeSection === 'sec-day2-ultimate' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                10. <span x-text="t[lang].nav_day2_ops"></span>
            </a>
            <a href="#sec-lifecycle-studio" @click.prevent="scrollTo('sec-lifecycle-studio')" :class="activeSection === 'sec-lifecycle-studio' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                11. <span x-text="t[lang].nav_lifecycle_studio"></span>
            </a>
            <a href="#sec-mitra-commercial-bridge" @click.prevent="scrollTo('sec-mitra-commercial-bridge')" :class="activeSection === 'sec-mitra-commercial-bridge' ? 'sg-nav-btn active' : 'sg-nav-btn'">
                12. <span x-text="t[lang].nav_mitra_bridge"></span>
            </a>
        </div>

        <!-- Right Controls: Bulletproof Search & Language Switcher -->
        <div style="display: flex; align-items: center; gap: 10px;">
            <!-- Realtime Search Filter -->
            <div class="sg-search-bar">
                <span style="color: var(--sg-text-muted); margin-right: 6px; font-size: 12px;">🔍</span>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    :placeholder="t[lang].search_placeholder" 
                    class="sg-search-input"
                />
                <button 
                    x-show="searchQuery" 
                    @click="searchQuery = ''" 
                    style="color: var(--sg-text-muted); background: transparent; border: none; cursor: pointer; font-size: 12px;"
                >&times;</button>
            </div>

            <!-- Tier 1 Language Toggle -->
            <div style="display: flex; gap: 4px;">
                <button 
                    @click="lang = 'id'" 
                    :class="lang === 'id' ? 'sg-lang-btn active' : 'sg-lang-btn'"
                >
                    ID
                </button>
                <button 
                    @click="lang = 'en'" 
                    :class="lang === 'en' ? 'sg-lang-btn active' : 'sg-lang-btn'"
                >
                    EN
                </button>
            </div>
        </div>
    </div>

    <!-- Active Search Filter Counter Banner -->
    <div x-show="searchQuery.trim() !== ''" style="margin-bottom: 16px; padding: 8px 14px; background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 2px; font-size: 12px; color: #0284c7;">
        Menyaring topik dengan kata kunci: "<strong x-text="searchQuery"></strong>" &bull; 
        <button @click="searchQuery = ''" style="text-decoration: underline; color: var(--sg-text-title); cursor: pointer; background: transparent; border: none; font-size: 12px;">Reset Pencarian</button>
    </div>

    <!-- 00. Confidential Founder Security Notice -->
    <div class="sg-card" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
            <div class="sg-badge-confidential">
                🔒 <span x-text="t[lang].confidential_badge"></span>
            </div>
            <div class="sg-badge-active">
                ✅ <span x-text="t[lang].access_verified"></span>: {{ auth()->user()?->name }} ({{ auth()->user()?->roles->pluck('name')->map(fn($r) => strtoupper(str_replace('_', ' ', $r)))->implode(', ') ?: 'AUTHORIZED' }})
            </div>
        </div>
        <h2 style="font-size: 18px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 8px 0;" x-text="t[lang].founder_title"></h2>
        <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.6; margin: 0;" x-text="t[lang].founder_desc"></p>
    </div>

    <!-- 01. Section Overview & Jawaban Strategis Founder -->
    <div id="sec-overview" class="sg-card" x-show="matchesSearch(t[lang].sec1_title + ' ' + t[lang].sec1_q + ' ' + t[lang].sec1_p1_text + ' ' + t[lang].sec1_p2_text + ' ' + t[lang].sec1_p3_text)">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #6366f1; text-transform: uppercase; font-weight: 700;">
                    01 // EXECUTIVE SUMMARY
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec1_q"></h3>
            </div>
            <span style="font-family: ui-monospace, monospace; font-size: 12px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 2px;">
                GROSS MARGIN: 98.6%
            </span>
        </div>

        <div class="sg-grid-3">
            <div class="sg-subcard">
                <div class="sg-accent-num" style="color: #6366f1;">01</div>
                <h4 style="font-size: 13px; font-weight: 700; color: var(--sg-text-title); margin-bottom: 6px;" x-text="t[lang].sec1_p1_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin: 0;" x-text="t[lang].sec1_p1_text"></p>
            </div>
            <div class="sg-subcard">
                <div class="sg-accent-num" style="color: #0284c7;">02</div>
                <h4 style="font-size: 13px; font-weight: 700; color: var(--sg-text-title); margin-bottom: 6px;" x-text="t[lang].sec1_p2_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin: 0;" x-text="t[lang].sec1_p2_text"></p>
            </div>
            <div class="sg-subcard">
                <div class="sg-accent-num" style="color: #10b981;">03</div>
                <h4 style="font-size: 13px; font-weight: 700; color: var(--sg-text-title); margin-bottom: 6px;" x-text="t[lang].sec1_p3_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin: 0;" x-text="t[lang].sec1_p3_text"></p>
            </div>
        </div>
    </div>

    <!-- 02. Section Unit Economics & COGS Riil -->
    <div id="sec-cogs" class="sg-card" x-show="matchesSearch(t[lang].sec2_title + ' ' + t[lang].sec2_desc + ' spark lite pro ultimate cogs margin')">
        <div style="border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #0284c7; text-transform: uppercase; font-weight: 700;">
                02 // UNIT ECONOMICS BREAKDOWN
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec2_title"></h3>
            <p style="font-size: 12px; color: var(--sg-text-muted); margin: 4px 0 0 0;" x-text="t[lang].sec2_desc"></p>
        </div>

        <div style="overflow-x: auto;">
            <table class="sg-table">
                <thead>
                    <tr>
                        <th x-text="t[lang].th_package"></th>
                        <th x-text="t[lang].th_price"></th>
                        <th x-text="t[lang].th_ai_cost"></th>
                        <th x-text="t[lang].th_payment_fee"></th>
                        <th x-text="t[lang].th_net_profit"></th>
                        <th x-text="t[lang].th_margin"></th>
                        <th x-text="t[lang].th_repeat_cycle"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 700; color: var(--sg-text-title);">Spark (Lead Magnet)</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">Rp 0</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 500 (10k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">Rp 0</td>
                        <td style="font-family: ui-monospace, monospace; color: #d97706;">-Rp 500 (CAC)</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">Free Tier</td>
                        <td style="color: var(--sg-text-muted);" x-text="t[lang].tb_spark_cycle"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #0284c7;">Starter AI Architect</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #0284c7;">Rp 99.000</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 1.000 (20k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 2.000 (Midtrans)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">Rp 96.000</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">97.0%</td>
                        <td style="color: var(--sg-text-muted);" x-text="t[lang].tb_paid_cycle"></td>
                    </tr>
                    <tr style="background: rgba(99, 102, 241, 0.08); border-left: 3px solid #6366f1;">
                        <td style="font-weight: 800; color: #6366f1;">★ Pro Blueprint (Core)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 800; color: #6366f1;">Rp 399.000</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 1.500 (30k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 4.000 (Midtrans)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 800; color: #10b981;">Rp 393.500</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 800; color: #10b981;">98.6%</td>
                        <td style="color: var(--sg-text-muted);" x-text="t[lang].tb_paid_cycle"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #9333ea;">Ultimate Enterprise</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #9333ea;">Rp 1.490.000</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 3.000 (60k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: var(--sg-text-muted);">~Rp 6.000 (Midtrans)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">Rp 1.481.000</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">99.4%</td>
                        <td style="color: var(--sg-text-muted);" x-text="t[lang].tb_ultimate_cycle"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 03. Section Pay-Per-Project Mechanics -->
    <div id="sec-pay-per-project" class="sg-card" x-show="matchesSearch(t[lang].sec3_title + ' pay per project lifetime lisensi entitas')">
        <div style="border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #9333ea; text-transform: uppercase; font-weight: 700;">
                03 // LEGAL & BUSINESS DEFINITION
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec3_title"></h3>
        </div>

        <div class="sg-grid-3">
            <div class="sg-subcard">
                <h4 style="font-size: 13px; font-weight: 700; color: #0284c7; margin-bottom: 6px;" x-text="t[lang].sec3_b1_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin: 0;" x-text="t[lang].sec3_b1_text"></p>
            </div>
            <div class="sg-subcard">
                <h4 style="font-size: 13px; font-weight: 700; color: #d97706; margin-bottom: 6px;" x-text="t[lang].sec3_b2_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin: 0;" x-text="t[lang].sec3_b2_text"></p>
            </div>
            <div class="sg-subcard">
                <h4 style="font-size: 13px; font-weight: 700; color: #10b981; margin-bottom: 6px;" x-text="t[lang].sec3_b3_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin: 0;" x-text="t[lang].sec3_b3_text"></p>
            </div>
        </div>
    </div>

    <!-- 04. Section Live Interactive Margin & Pipeline Simulator -->
    <div id="sec-simulator" class="sg-card" x-show="matchesSearch(t[lang].sec4_title + ' simulator kalkulator proyeksi laba omzet')">
        <div style="border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #10b981; text-transform: uppercase; font-weight: 700;">
                04 // REAL-TIME FINANCIAL SIMULATOR
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec4_title"></h3>
            <p style="font-size: 12px; color: var(--sg-text-muted); margin: 4px 0 0 0;" x-text="t[lang].sec4_desc"></p>
        </div>

        <div class="sg-simulator-box">
            <!-- Left: Slider & Direct Blueprint Revenue -->
            <div class="sg-subcard">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--sg-text-body);" x-text="t[lang].sim_sales_label"></span>
                    <span style="font-family: ui-monospace, monospace; font-size: 14px; font-weight: 900; color: #6366f1;" x-text="salesVolume + ' ' + t[lang].unit_projects"></span>
                </div>
                <input 
                    type="range" 
                    min="5" 
                    max="300" 
                    step="5" 
                    x-model="salesVolume" 
                    style="width: 100%; accent-color: #6366f1; cursor: pointer; margin-bottom: 16px;"
                />
                
                <div style="space-y: 8px; font-size: 12px;">
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid var(--sg-border-subtle);">
                        <span style="color: var(--sg-text-muted);" x-text="t[lang].sim_gross_rev"></span>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: var(--sg-text-title);" x-text="formatCurrency(salesVolume * 399000)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid var(--sg-border-subtle);">
                        <span style="color: var(--sg-text-muted);" x-text="t[lang].sim_ai_cost"></span>
                        <span style="font-family: ui-monospace, monospace; color: #ef4444;" x-text="formatCurrency(salesVolume * 1500)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid var(--sg-border-subtle);">
                        <span style="color: var(--sg-text-muted);" x-text="t[lang].sim_midtrans_cost"></span>
                        <span style="font-family: ui-monospace, monospace; color: #ef4444;" x-text="formatCurrency(salesVolume * 4000)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; font-weight: 800;">
                        <span style="color: #10b981;" x-text="t[lang].sim_net_cash"></span>
                        <span style="font-family: ui-monospace, monospace; color: #10b981;" x-text="formatCurrency(salesVolume * 393500)"></span>
                    </div>
                </div>
            </div>

            <!-- Right: Studio MVP Upsell Pipeline -->
            <div class="sg-subcard" style="border: 1px solid var(--sg-trojan-border); background: var(--sg-trojan-bg);">
                <div style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 800; color: #6366f1; text-transform: uppercase; margin-bottom: 4px;">
                    THE TROJAN HORSE EFFECT
                </div>
                <h4 style="font-size: 14px; font-weight: 800; color: var(--sg-text-title); margin-bottom: 8px;" x-text="t[lang].sim_upsell_title"></h4>
                <p style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5; margin-bottom: 12px;" x-text="t[lang].sim_upsell_desc"></p>

                <div style="background: var(--sg-card-bg); padding: 12px; border: 1px solid var(--sg-border); border-radius: 2px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                        <span style="color: var(--sg-text-muted);" x-text="t[lang].sim_upsell_clients"></span>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #6366f1;" x-text="Math.floor(salesVolume * 0.05) + ' Klien'"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 900; color: #10b981;">
                        <span x-text="t[lang].sim_upsell_pipeline"></span>
                        <span style="font-family: ui-monospace, monospace;" x-text="formatCurrency(Math.floor(salesVolume * 0.05) * 50000000)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 05. Section Technical Limits & Enforcement Matrix + 7-Pillar Factory OS -->
    <div id="sec-matrix" class="sg-card" x-show="matchesSearch(t[lang].sec5_title + ' batasan kuota reset spark lite pro ultimate window otp pilar factory studio mvp umkm')">
        <div style="border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #d97706; text-transform: uppercase; font-weight: 700;">
                05 // 10-PILLAR SOFTWARE FACTORY OS & VALUE LADDER MATRIX
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec5_title"></h3>
            <p style="font-size: 12px; color: var(--sg-text-muted); margin: 4px 0 0 0;" x-text="t[lang].sec5_desc"></p>
        </div>

        <!-- 10 Pillars of Software Factory OS Infographic Banner -->
        <div style="margin-bottom: 24px; padding: 16px; background: var(--sg-subcard-bg); border: 1px solid var(--sg-border); border-radius: 3px;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;" x-text="t[lang].sec5_banner_title || 'THE 10 PILLARS OF SOFTWARE FACTORY OS'">
                THE 10 PILLARS OF SOFTWARE FACTORY OS (ONE-STOP DEVELOPMENT)
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 10px; font-size: 11px;">
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #6366f1;" x-text="t[lang].pil1_title">Pilar 1 // Otak & Kontrak</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil1_desc">PRD 26 Parameter & OpenAPI 3.1 Contract Spec</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #0284c7;" x-text="t[lang].pil2_title">Pilar 2 // Desain UI/UX</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil2_desc">Design Tokens JSON & Wireframe 4 Layar Utama</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #10b981;" x-text="t[lang].pil3_title">Pilar 3 // Rangka Koding</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil3_desc">Docker Compose (PHP 8.4, PG 16, Redis 7) & API Routes</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #f59e0b;" x-text="t[lang].pil4_title">Pilar 4 // Data Awal Sistem</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil4_desc">Synthetic Mock Data Seeder (100+ Data Realistis)</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #8b5cf6;" x-text="t[lang].pil5_title">Pilar 5 // Panduan Agen AI</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil5_desc">AI Coding Rules (.cursorrules, CLAUDE.md, AGENTS.md, ECC)</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #ec4899;" x-text="t[lang].pil6_title">Pilar 6 // Jaminan Kualitas</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil6_desc">Automated Pest Tests & Playwright Browser Automation</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #14b8a6;" x-text="t[lang].pil7_title">Pilar 7 // Jalur Otomasi Server</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil7_desc">One-Click GitHub Actions CI/CD & deploy.sh</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #06b6d4;" x-text="t[lang].pil8_title">Pilar 8 // Telemetri & Pantauan</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil8_desc">Health Probes (/healthz, /livez, /readyz) & Metrik Terstruktur</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #6366f1;" x-text="t[lang].pil9_title">Pilar 9 // Pemulihan Bencana</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil9_desc">Backup Terenkripsi Otomatis (backup.sh & restore.sh Drills)</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="color: #0d9488;" x-text="t[lang].pil10_title">Pilar 10 // Dokumen API Hidup</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 2px;" x-text="t[lang].pil10_desc">Dokumentasi Interaktif Scalar OpenAPI (/docs/api)</div>
                </div>
            </div>
        </div>

        <!-- Section: Retail Packages Progressive Value Ladder & Upsell Psychology -->
        <div style="margin-bottom: 24px;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #0284c7; text-transform: uppercase; margin-bottom: 12px;">
                A. VALUE LADDER RETAIL (HAK UNDUH SELAMANYA PER PROYEK // SELF-SERVICE)
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
                <!-- Spark Free Card -->
                <div class="sg-subcard" style="border-top: 3px solid #64748b;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong style="font-size: 13px; color: var(--sg-text-title);">01. Spark / Free Audit</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981; font-size: 11px;">Rp 0</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; padding: 2px 6px; background: rgba(100, 116, 139, 0.1); color: var(--sg-text-muted); margin-bottom: 8px;">
                        TARGET: Founder pemula yang ingin memvalidasi ide & kelayakan MVP.
                    </div>
                    <div style="font-size: 11px; color: var(--sg-text-title); font-weight: 700; margin-bottom: 4px;">Deliverables:</div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li>Analisis Kelayakan Bisnis & Problem Framing</li>
                        <li>Executive Summary & Target Audiens</li>
                        <li>5 Fitur Esensial MVP Prioritas</li>
                        <li>Estimasi Kompleksitas & TCO Awal</li>
                        <li>Ekspor Ringkasan Dokumen Markdown (.md)</li>
                    </ul>
                    <div style="padding: 8px; background: var(--sg-card-bg); border: 1px solid var(--sg-border); font-size: 10px; font-family: ui-monospace, monospace;">
                        <span style="color: #d97706; font-weight: 700;">BATASAN / HOOK RASA TANGGUNG:</span>
                        <div style="color: var(--sg-text-muted); margin-top: 2px;">Tanpa skema SQL DDL, tanpa Docker, & tanpa AI rules. Dibatasi 2x audit tamu.</div>
                        <div style="color: #0284c7; margin-top: 4px; font-weight: 700;">👉 Upsell: Upgrade ke Starter AI Architect (Rp 99rb) untuk skema database siap import!</div>
                    </div>
                </div>

                <!-- Lite PRD Card -->
                <div class="sg-subcard" style="border-top: 3px solid #0284c7;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong style="font-size: 13px; color: #0284c7;">02. Starter AI Architect</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #0284c7; font-size: 11px;">Rp 99.000</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; padding: 2px 6px; background: rgba(2, 132, 199, 0.1); color: #0284c7; margin-bottom: 8px;">
                        TARGET: Freelance developer & solo founder yang butuh skema database SQL siap pakai.
                    </div>
                    <div style="font-size: 11px; color: var(--sg-text-title); font-weight: 700; margin-bottom: 4px;">Deliverables:</div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li><strong style="color: #0284c7;">Seperti di Paket Spark Free</strong>, ditambah:</li>
                        <li>Dokumen PRD Lengkap 6 Bab (26 Parameter)</li>
                        <li>Skema PostgreSQL Strict ULID DDL SQL (schema_complete.sql)</li>
                        <li>Standar Teknis Keyset Cursor Pagination O(1)</li>
                        <li>Work Breakdown Structure (WBS) 2 Sprint Linear/Jira Ready</li>
                        <li>Ekspor Resmi PDF & Markdown Ber-hash SHA-256</li>
                    </ul>
                    <div style="padding: 8px; background: var(--sg-card-bg); border: 1px solid var(--sg-border); font-size: 10px; font-family: ui-monospace, monospace;">
                        <span style="color: #d97706; font-weight: 700;">BATASAN / HOOK RASA TANGGUNG:</span>
                        <div style="color: var(--sg-text-muted); margin-top: 2px;">Tanpa Docker compose, tanpa AI agent rules, & WBS terpotong hanya 2 sprint.</div>
                        <div style="color: #10b981; margin-top: 4px; font-weight: 700;">👉 Upsell: Tambah Rp 300rb dapat Pro (Rp 399rb) lengkap dengan Docker & AI!</div>
                    </div>
                </div>

                <!-- Pro Production Card -->
                <div class="sg-subcard" style="border-top: 3px solid #10b981; background: rgba(16, 185, 129, 0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong style="font-size: 13px; color: #10b981;">03. Pro Agentic Blueprint ★</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981; font-size: 11px;">Rp 399.000</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; padding: 2px 6px; background: rgba(16, 185, 129, 0.1); color: #10b981; margin-bottom: 8px;">
                        TARGET: Software agensi, CTO, & startup yang ingin langsung koding hari ini dengan AI.
                    </div>
                    <div style="font-size: 11px; color: var(--sg-text-title); font-weight: 700; margin-bottom: 4px;">Deliverables:</div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li><strong style="color: #10b981;">Seperti di Paket Starter</strong>, ditambah:</li>
                        <li>Pilar 1 (Spec): OpenAPI 3.1 Spec & Idempotensi</li>
                        <li>Pilar 3 (Scaffold): Container Docker Siap Pakai</li>
                        <li>Pilar 4 (Seeder): Database Seeder Sintetik 25 Data</li>
                        <li>Pilar 5 (AI Agent): AI Coding Rules (.cursorrules, AGENTS.md)</li>
                        <li>6 Diagram Mermaid Lengkap & WBS 5 Sprint</li>
                        <li>White-Label Agency Export License</li>
                    </ul>
                    <div style="padding: 8px; background: var(--sg-card-bg); border: 1px solid var(--sg-border); font-size: 10px; font-family: ui-monospace, monospace;">
                        <span style="color: #d97706; font-weight: 700;">BATASAN / HOOK RASA TANGGUNG:</span>
                        <div style="color: var(--sg-text-muted); margin-top: 2px;">Tanpa wireframe UI, tanpa test suite otomatis, & tanpa pipeline CI/CD.</div>
                        <div style="color: #8b5cf6; margin-top: 4px; font-weight: 700;">👉 Upsell: Butuh 10 Pilar Factory OS komplit + Sesi 1-on-1? Upgrade ke Ultimate!</div>
                    </div>
                </div>

                <!-- Ultimate Factory OS Card -->
                <div class="sg-subcard" style="border-top: 3px solid #8b5cf6;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong style="font-size: 13px; color: #8b5cf6;">04. Ultimate Factory OS</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #8b5cf6; font-size: 11px;">Rp 1.490.000</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; padding: 2px 6px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; margin-bottom: 8px;">
                        TARGET: Enterprise product leaders, funded startups, & tim yang butuh 10 pilar lengkap.
                    </div>
                    <div style="font-size: 11px; color: var(--sg-text-title); font-weight: 700; margin-bottom: 4px;">Deliverables:</div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li><strong style="color: #8b5cf6;">Seperti di Paket Pro Agentic Blueprint</strong>, ditambah:</li>
                        <li>Pilar 2 (UI/UX): Tokens JSON, 4 Layar Wireframe & Status WCAG AAA</li>
                        <li>Pilar 6 (QA & E2E): Automated Pest Tests & Otomasi Browser Playwright</li>
                        <li>Pilar 7 (DevOps): CI/CD GitHub Actions & deploy.sh</li>
                        <li>Pilar 8-10 (Reliability): Health Probes (/healthz), Backup Enkripsi & Dokumentasi API Scalar</li>
                        <li>Sesi Konsultasi 60 Menit bersama Principal Architect</li>
                        <li>Legal NDA Digital & 1 Tahun Prioritas Advisory</li>
                    </ul>
                    <div style="padding: 8px; background: var(--sg-card-bg); border: 1px solid var(--sg-border); font-size: 10px; font-family: ui-monospace, monospace;">
                        <span style="color: #d97706; font-weight: 700;">BATASAN / STUDIO HOOK:</span>
                        <div style="color: var(--sg-text-muted); margin-top: 2px;">Koding tetap dieksekusi oleh tim developer internal klien sendiri.</div>
                        <div style="color: #10b981; margin-top: 4px; font-weight: 700;">👉 Trojan Horse: Ingin tim kami yang koding 100% turnkey? Biaya potong DP 50%!</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Studio Development Contracts -->
        <div style="margin-bottom: 24px;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #10b981; text-transform: uppercase; margin-bottom: 12px;">
                B. KONTRAK STUDIO & JASA PENGERJAAN PENUH (EXECUTED BY NERIAH PRO)
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 14px;">
                <!-- Studio 1: Blueprint Advisory -->
                <div class="sg-subcard" style="border-left: 3px solid #64748b;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <strong style="font-size: 13px; color: var(--sg-text-title);">Studio // Blueprint Advisory</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: var(--sg-text-title); font-size: 11px;">Rp 2.500.000</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; color: var(--sg-text-muted); margin-bottom: 8px;">
                        TARGET: Founder/CTO dengan tim koding sendiri yang butuh pendampingan arsitektur enterprise.
                    </div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li>Seluruh Output Spesifikasi 10 Pilar Software Factory OS</li>
                        <li>Sesi Discovery & Technical Scoping bersama Principal Architect</li>
                        <li>Non-Disclosure Agreement (NDA) Sah & 100% Hak Milik Dokumen</li>
                        <li><strong style="color: #10b981;">Jaminan Potong DP</strong>: Biaya Rp 2.5jt memotong DP jika lanjut koding</li>
                    </ul>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; color: #d97706;">
                        BATASAN: Neriah Pro tidak menulis baris koding. Koding dilakukan tim klien.
                    </div>
                </div>

                <!-- Studio 2: UMKM Digital Starter -->
                <div class="sg-subcard" style="border-left: 3px solid #f59e0b;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <strong style="font-size: 13px; color: #f59e0b;">Studio // UMKM Digital Starter</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #f59e0b; font-size: 11px;">Rp 3.750.000 (Subsidi)</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; color: var(--sg-text-muted); margin-bottom: 8px;">
                        TARGET: Pemilik bisnis lokal, ritel, & yayasan yang butuh web app transaksional siap pakai.
                    </div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li><strong style="color: #10b981;">100% Dikerjakan sampai Live oleh Tim Neriah Pro</strong></li>
                        <li>Engine Transaksi & Database Pelanggan Terpusat</li>
                        <li>Integrasi Pembayaran Otomatis QRIS & Transfer Bank (Midtrans)</li>
                        <li>Admin Dashboard Filament v5 Bahasa Indonesia & Notifikasi WhatsApp</li>
                        <li>Setup Domain Bisnis (.id/.com), Hosting Cepat & Sesi Pelatihan</li>
                    </ul>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; color: #d97706;">
                        BATASAN: Alur kerja ritel UMKM standar (maks 2 core flows). Kuota 2 slot/bulan.
                    </div>
                </div>

                <!-- Studio 3: Enterprise Monolith MVP -->
                <div class="sg-subcard" style="border-left: 3px solid #10b981; background: rgba(16, 185, 129, 0.04);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <strong style="font-size: 13px; color: #10b981;">Studio // Enterprise Monolith MVP</strong>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981; font-size: 11px;">Custom Quotation / Enterprise (DP 50%)</span>
                    </div>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; color: #10b981; margin-bottom: 8px;">
                        TARGET: Founder startup berdana & korporat yang butuh sistem siap produksi skala jutaan pengguna.
                    </div>
                    <ul style="font-size: 11px; color: var(--sg-text-muted); margin: 0 0 10px 16px; padding: 0; line-height: 1.5;">
                        <li><strong style="color: #10b981;">Managed Sprint Capacity</strong>: Slot terisolasi Batch 1, 2, atau Q1 dengan Anti-Collision Engine</li>
                        <li><strong style="color: #10b981;">Seluruh 10 Pilar Software Factory OS Dikodingkan Penuh</strong> (Laravel 13, Filament v5, React 19)</li>
                        <li>Arsitektur Database PostgreSQL 16 Strict ULID & Keyset Cursor Pagination O(1)</li>
                        <li>Synthetic Vital Data Seeder (100+ data uji) & Panduan Agen AI (.cursorrules, AGENTS.md)</li>
                        <li>Automated Test Suite ApiContractTest & Midtrans Snap Idempotent Webhook</li>
                        <li>Monitoring Master Gantt Timeline & Commercial Invoice Resmi di Customer Portal</li>
                        <li>Dedicated VPS Hardening, Redis Caching, Nginx HTTP/2, & Pipeline deploy.sh 6 Skenario</li>
                        <li>100% Repositori GitHub Privat & Kredensial Server diserahkan ke Klien (No Lock-in)</li>
                        <li><strong style="color: #10b981;">Garansi Perbaikan Bug & SLA Prioritas 3 Bulan Penuh</strong></li>
                    </ul>
                    <div style="font-size: 10px; font-family: ui-monospace, monospace; color: #10b981;">
                        TATA KELOLA: Managed Capacity (Maks 2-3 Proyek/Batch) & Scope WBS 5 Sprint (Zero Delay & Anti-AI-Slop).
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Backend Limit & Reset Cycle Matrix Table -->
        <div style="overflow-x: auto;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #d97706; text-transform: uppercase; margin-bottom: 8px;">
                C. TABEL PENEGAKAN BATASAN TEKNIS & SIKLUS RESET LISENSI
            </div>
            <table class="sg-table">
                <thead>
                    <tr>
                        <th x-text="t[lang].m_tier"></th>
                        <th x-text="t[lang].m_who_codes"></th>
                        <th x-text="t[lang].m_quota"></th>
                        <th x-text="t[lang].m_reset"></th>
                        <th x-text="t[lang].m_validity"></th>
                        <th x-text="t[lang].m_auth"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 700; color: var(--sg-text-title);">Spark (Free)</td>
                        <td style="color: #ef4444;" x-text="t[lang].m_self"></td>
                        <td>2x Analisis Ide</td>
                        <td style="color: #0284c7;">Auto-Reset tgl 1 tiap bulan</td>
                        <td>7 Hari Sesi Tamu</td>
                        <td style="color: #10b981;">Bebas (Guest Mode)</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #0284c7;">Lite (Rp 99k)</td>
                        <td style="color: #ef4444;" x-text="t[lang].m_self"></td>
                        <td>1 Entitas Proyek</td>
                        <td>Pay-Per-Project</td>
                        <td>30 Hari Jendela Revisi Form</td>
                        <td style="color: #d97706;">Wajib Login (OTP Email)</td>
                    </tr>
                    <tr style="background: rgba(99, 102, 241, 0.08);">
                        <td style="font-weight: 800; color: #6366f1;">Pro (Rp 399k)</td>
                        <td style="color: #ef4444;" x-text="t[lang].m_self"></td>
                        <td>1 Entitas Proyek</td>
                        <td>Pay-Per-Project</td>
                        <td>6 Bulan Unlimited AI Re-prompt</td>
                        <td style="color: #d97706;">Wajib Login (OTP Email)</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #9333ea;">Ultimate (Rp 1.49M)</td>
                        <td style="color: #ef4444;" x-text="t[lang].m_self"></td>
                        <td>1 Entitas Enterprise</td>
                        <td>Pay-Per-Project</td>
                        <td>1 Tahun Prioritas & 60 Hari Call</td>
                        <td style="color: #d97706;">Wajib Akun Terverifikasi</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: var(--sg-text-title);">Studio Advisory (Rp 2.5M)</td>
                        <td style="color: #ef4444;" x-text="t[lang].m_self"></td>
                        <td>1 Proyek Enterprise</td>
                        <td>Per Kontrak Advisory</td>
                        <td>Sesi Scoping & Garansi Potong DP</td>
                        <td style="color: #10b981;">NDA & Kontrak Sah</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #f59e0b;">Studio UMKM (Rp 3.75M)</td>
                        <td style="font-weight: 800; color: #10b981;" x-text="t[lang].m_neriah"></td>
                        <td>Turnkey Sistem Ritel</td>
                        <td>Per Kontrak UMKM</td>
                        <td>Garansi & Sesi Pelatihan Zoom</td>
                        <td style="color: #10b981;">Kontrak Usaha Sah</td>
                    </tr>
                    <tr style="background: rgba(16, 185, 129, 0.08); border-top: 2px solid #10b981;">
                        <td style="font-weight: 800; color: #10b981;">Studio Full MVP (Rp 50M)</td>
                        <td style="font-weight: 800; color: #10b981;" x-text="t[lang].m_neriah"></td>
                        <td>Turnkey Monolith System</td>
                        <td>Milestone 50/50 DP</td>
                        <td>SLA 3 Bulan Garansi Bug</td>
                        <td style="color: #10b981;">Kontrak Digital Sah</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 06. Section CS & Sales Playbook -->
    <div id="sec-playbook" class="sg-card" x-show="matchesSearch(t[lang].sec6_title + ' playbook script cs sales jawaban komplain docker ai')">
        <div style="border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #db2777; text-transform: uppercase; font-weight: 700;">
                06 // SALES & CS OBJECTION SCRIPTS
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec6_title"></h3>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #d97706; margin-bottom: 6px;" x-text="t[lang].sec6_q1"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a1"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 6px;" x-text="t[lang].sec6_q2"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a2"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #0284c7; margin-bottom: 6px;" x-text="t[lang].sec6_q3"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a3"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #8b5cf6; margin-bottom: 6px;" x-text="t[lang].sec6_q4"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a4"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #ec4899; margin-bottom: 6px;" x-text="t[lang].sec6_q5"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a5"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #14b8a6; margin-bottom: 6px;" x-text="t[lang].sec6_q6"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a6"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-bottom: 6px;" x-text="t[lang].sec6_q7"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a7"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 6px;" x-text="t[lang].sec6_q8"></div>
                <div style="font-size: 12px; color: var(--sg-quote-text); font-style: italic; background: var(--sg-quote-bg); padding: 12px; border: 1px solid var(--sg-quote-border); border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a8"></div>
            </div>
        </div>
    </div>

    <!-- 07. Section 10 Pilar Software Factory OS & AI Governance -->
    <div id="sec-factory-pillars" class="sg-card" x-show="matchesSearch('10 pilar software factory os postgresql strict ulid ai copilot rag cag kag 2fa audit log rbac')">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #10b981; text-transform: uppercase; font-weight: 700;">
                    07 // FACTORY OS & AI GOVERNANCE
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;">
                    Arsitektur 10 Pilar Software Factory OS, AI Copilot &amp; Keamanan Zero-Trust
                </h3>
            </div>
            <span style="font-family: ui-monospace, monospace; font-size: 12px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 2px;">
                100% PRODUCTION READY
            </span>
        </div>

        <p style="font-size: 13px; color: var(--sg-text-body); line-height: 1.6; margin-bottom: 16px;">
            Neriah Pro beroperasi dengan standar rekayasa software factory enterprise. Seluruh pengerjaan kode, arsitektur database, dan tata kelola AI tunduk pada 10 Pilar Software Factory OS:
        </p>

        <!-- 10 Pillars Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; margin-bottom: 20px;">
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #6366f1;">PILAR 01: PRD 26 PARAMETER</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Executive Summary, Target Persona, Problem-Solution Fit, RBAC Matrix, Kepatuhan UU PDP No. 27/2022, OWASP Threat Modeling, DDL PostgreSQL, dan WBS Mermaid Gantt.</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #10b981;">PILAR 02: STRICT ULID &amp; O(1) CURSOR</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Primary Key bisnis wajib ULID 26 karakter (->ulid('id')->primary()) untuk kompatibilitas PostgreSQL 16. Keyset Cursor Pagination O(1) (cursorPaginate()) tanpa degradasi performa jutaan baris.</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #0284c7;">PILAR 03: CONTAINERIZED DOCKER</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Environment production terisolasi: PHP 8.5+ FPM (OPcache/APCu), PostgreSQL 16 dengan pgvector, Redis 7 (Cache::rememberForever), dan Nginx TLS reverse proxy.</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #d97706;">PILAR 04: SYNTHETIC SEEDER 100+</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Seeder sintetis menghasilkan minimal 100+ baris data realistis untuk validasi cursor pagination, stres-testing database, dan pengujian edge-case sebelum rilis staging.</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #ec4899;">PILAR 05: AI AGENT RULES GOVERNANCE</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Zero Tolerance Dosa Hardcode, pelarangan dialog window.alert/confirm bawaan browser, sudut sudut subtle (rounded-none s/d rounded-md), dan tema sleek sharp brutalism monochrome.</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #8b5cf6;">PILAR 06: PEST APICONTRACTTEST</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Suite pengujian otomatis berbasis Pest PHP menguji endpoint API, otentikasi role RBAC, proteksi CSRF/Honeypot, dan verifikasi idempoten webhook Midtrans Snap (DLQ).</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #14b8a6;">PILAR 07: CI/CD SCRIPT deploy.sh</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Deployment script zero-downtime 6 skenario: 1 (UI/Blade/PHP), 2 (Migrasi Aman), 3 (Hancur &amp; Bangun DB Staging), 4 (Composer/Filament), 5 (Dump Autoload), 6 (Build Frontend Vite).</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #f59e0b;">PILAR 08: SHA-256 SCOPE LOCK &amp; ESCROW</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Pencegahan scope creep melalui Scope Lock digital berstempel SHA-256 dan Canvas Signature Pad. Sistem pembayaran termin Midtrans Snap (DP 50% &amp; Pelunasan 50%).</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #64748b;">PILAR 09: MANAGED SPRINT CAPACITY</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Filosofi managed capacity: Membatasi pengerjaan maksimal 2-3 proyek paralel per batch demi menjaga standar rekayasa enterprise anti-burnout dan anti-AI-slop.</div>
            </div>
            <div class="sg-subcard">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700; color: #10b981;">PILAR 10: CUSTOMER PORTAL &amp; ASSETS</div>
                <div style="font-size: 12px; color: var(--sg-text-body); margin-top: 4px;">Portal klien real-time (/customer/dashboard) menampilkan timeline Gantt, dokumen PRD terkunci, dan monitoring siklus hidup domain &amp; hosting dengan peringatan H-30/H-7.</div>
            </div>
        </div>

        <!-- AI Agent, 2FA & Audit Log Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
            <div class="sg-subcard" style="border-left: 3px solid #10b981;">
                <div style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 6px;">
                    🤖 Tri-Hybrid AI Copilot &amp; {{ $nativeToolsCount ?? 16 }} Native Tool Calling
                </div>
                <div style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5;">
                    Sinergi memori <strong>RAG</strong> (Dense 768-dim pgvector + Sparse Lexical RRF), <strong>CAG</strong> (Cache-Augmented Live Telemetry O(1)), dan <strong>KAG</strong> (Deterministic Rule Graph). Dilengkapi <strong>{{ $nativeToolsCount ?? 16 }} Native Tools</strong> tervalidasi RBAC (termasuk Sectoral UI Archetypes, Phased Prompt Sequencer, Brownfield SQL Importer, &amp; Slop Scanner).
                </div>
            </div>

            <div class="sg-subcard" style="border-left: 3px solid #6366f1;">
                <div style="font-size: 12px; font-weight: 700; color: #6366f1; margin-bottom: 6px;">
                    🔐 Otentikasi 2FA Password (TOTP RFC 6238) &amp; Granular RBAC
                </div>
                <div style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5;">
                    Keamanan akun admin lapis dua berstandar industri RFC 6238 dengan Time-based One-Time Password (TOTP) serta isolasi RBAC ketat (<code>super_admin</code>, <code>developer</code>, <code>investor</code>, <code>midtrans_reviewer</code>, <code>client_retail</code>, <code>client_partner</code>).
                </div>
            </div>

            <div class="sg-subcard" style="border-left: 3px solid #f59e0b;">
                <div style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-bottom: 6px;">
                    📋 Tamper-Evident SHA-256 Audit Trail &amp; CCTV Workflow Tracing
                </div>
                <div style="font-size: 12px; color: var(--sg-text-muted); line-height: 1.5;">
                    Setiap mutasi, eksekusi AI Native Tool, tarikan CLI Bootstrapper, dan audit Brownfield/Slop dikategorikan (<code>event_category</code>) serta disegel dengan hash kanonik <code>integrity_hash</code> (SHA-256) di tabel <code>audit_logs</code> berkompleksitas O(1) Cursor.
                </div>
            </div>
        </div>
    </div>

    <!-- 08. Section 12 Sectoral Industrial UI/UX Archetypes & Realistic Seeders -->
    <div id="sec-sectoral-ui" class="sg-card" x-show="matchesSearch('12 sektor ui ux archetype pos split terminal clinical cockpit gantt ledger kds bom seat map roster donor seeder indonesia')">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #0284c7; text-transform: uppercase; font-weight: 700;">
                    08 // 12 SECTORAL INDUSTRIAL UI/UX ARCHETYPES ({{ $stackPresetsCount ?? 9 }} STACK ADAPTERS M1–S2)
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec8_title"></h3>
            </div>
            <span class="sg-badge-active">ZERO GENERIC CRUD SLOP</span>
        </div>

        <p style="font-size: 13px; color: var(--sg-text-body); line-height: 1.6; margin-bottom: 16px;" x-text="t[lang].sec8_desc"></p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 14px;">
            @foreach(($sectorsCatalog ?? []) as $idx => $secItem)
                @php
                    $uiArch = $secItem['ui_ux_archetypes'] ?? [];
                    $seederContract = $secItem['realistic_seeder_contract'] ?? [];
                @endphp
                <div class="sg-subcard" style="border-top: 3px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px;">
                        <div style="font-size: 12px; font-weight: 800; color: var(--sg-text-title);">
                            {{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}. {{ $secItem['name']['id'] ?? $secItem['code'] }}
                        </div>
                        <span style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 700; padding: 2px 6px; background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.35);">
                            {{ $uiArch['primary_archetype'] ?? 'SECTOR_ARCHETYPE' }}
                        </span>
                    </div>
                    <div style="font-family: ui-monospace, monospace; font-size: 10px; color: #0284c7; font-weight: 700; margin-bottom: 6px;">
                        Secondary: {{ $uiArch['secondary_archetype'] ?? '-' }} &bull; Seeder: {{ $seederContract['seeder_class'] ?? 'RealisticSectorSeeder' }}
                    </div>
                    <div style="font-size: 11px; color: var(--sg-text-body); line-height: 1.5; margin-bottom: 8px;">
                        {{ $uiArch['headline']['id'] ?? '' }}
                    </div>
                    @if(!empty($uiArch['keyboard_shortcuts']))
                        @php
                            $formattedKeys = array_map(
                                fn ($ks) => is_array($ks) ? (($ks['key'] ?? '') . ' (' . ($ks['action'] ?? '') . ')') : (string) $ks,
                                array_slice($uiArch['keyboard_shortcuts'], 0, 3)
                            );
                        @endphp
                        <div style="font-family: ui-monospace, monospace; font-size: 10px; color: var(--sg-text-muted); padding-top: 6px; border-top: 1px dashed var(--sg-border);">
                            ⌨️ Shortcuts: {{ implode(' • ', $formattedKeys) }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- 09. Section 6 Killer Zero-to-Production Orchestration Moat -->
    <div id="sec-killer-moat" class="sg-card" x-show="matchesSearch('6 killer features phased prompt sequencer brownfield sql importer slop scanner verify-blueprint cli bootstrapper curl init retention moat')">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #8b5cf6; text-transform: uppercase; font-weight: 700;">
                    09 // ZERO-TO-PRODUCTION DEVELOPER RETENTION MOAT
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec9_title"></h3>
            </div>
            <span class="sg-badge-confidential">COMPETITIVE MOAT v2.5</span>
        </div>

        <p style="font-size: 13px; color: var(--sg-text-body); line-height: 1.6; margin-bottom: 16px;" x-text="t[lang].sec9_desc"></p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 14px;">
            @foreach(($killerFeatures ?? []) as $kItem)
                <div class="sg-subcard" style="border-left: 3px solid #8b5cf6;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <strong style="font-size: 12px; color: var(--sg-text-title);">{{ $kItem['title']['id'] ?? $kItem['code'] }}</strong>
                        <span style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 700; padding: 2px 6px; background: rgba(139, 92, 246, 0.12); color: #7c3aed; border: 1px solid rgba(139, 92, 246, 0.35);">
                            {{ $kItem['badge'] ?? 'ACTIVE' }}
                        </span>
                    </div>
                    <div style="font-size: 11.5px; color: var(--sg-text-muted); line-height: 1.55;">
                        {{ $kItem['desc']['id'] ?? '' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 10. Section Project OS v3.0 — The Final 8 Day-2 Production Layers & 10 Ultimate Perfection Pillars -->
    <div id="sec-day2-ultimate" class="sg-card" x-show="matchesSearch('day-2 operations thermal 58mm 80mm escpos a4 pdf qr initialize new tenant snapshot staged csv import queue lanes private disk signed url notification matrix blueprint doctor 10 pillars v3.0')">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #10b981; text-transform: uppercase; font-weight: 700;">
                    10 // DAY-2 OPERATIONS &amp; 100% ULTIMATE PERFECTION (PROJECT OS v3.0)
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec10_title"></h3>
            </div>
            <span class="sg-badge-confidential">ULTIMATE BLUEPRINT v3.0</span>
        </div>

        <p style="font-size: 13px; color: var(--sg-text-body); line-height: 1.6; margin-bottom: 16px;" x-text="t[lang].sec10_desc"></p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px; margin-bottom: 20px;">
            @foreach(($finalEightLayers ?? []) as $dLayer)
                <div class="sg-subcard" style="border-left: 3px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <strong style="font-size: 12px; color: var(--sg-text-title);">{{ $dLayer['title']['id'] ?? $dLayer['code'] }}</strong>
                        <span style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 700; padding: 2px 6px; background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.35);">
                            {{ $dLayer['badge'] ?? 'DAY-2' }}
                        </span>
                    </div>
                    <div style="font-size: 11.5px; color: var(--sg-text-muted); line-height: 1.55;">
                        {{ $dLayer['desc']['id'] ?? '' }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="padding: 14px; background: var(--sg-subcard-bg); border: 1px solid var(--sg-border); border-radius: 3px;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #0284c7; text-transform: uppercase; margin-bottom: 10px;">
                PETA KESEMPURNAAN 10 PILAR ULTIMATE BLUEPRINT NERIAHPRO.COM
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; font-size: 11px;">
                @foreach(($tenPerfectionPillars ?? []) as $tp)
                    <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                        <strong style="font-family: ui-monospace, monospace; color: #10b981;">{{ $tp['num'] }} // {{ $tp['title'] }}</strong>
                        <div style="color: var(--sg-text-muted); margin-top: 3px; line-height: 1.45;">{{ $tp['detail'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 11. Section Project OS v4.0 — Day-30 to Day-365 Lifecycle Retention Engines & 3-Pane Architect Studio -->
    <div id="sec-lifecycle-studio" class="sg-card" x-show="matchesSearch('day-30 day-365 delta change request v1.0 v1.1 mcp server bridge neriahpro-mcp decision matrix match phase 7 redteam security tenant isolation env.blueprint pos:check-env 3-pane architect studio 60s wizard slop diff v4.0')">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #059669; text-transform: uppercase; font-weight: 800;">
                    11 // DAY-30 TO DAY-365 RETENTION LIFECYCLE &amp; THE 3-PANE ARCHITECT STUDIO (v4.0)
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec11_title"></h3>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ url('/blueprint/studio') }}" target="_blank" style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; padding: 6px 12px; background: #059669; color: #ffffff; border: 1px solid #047857; border-radius: 2px; text-decoration: none;">
                    🖥️ LAUNCH 3-PANE ARCHITECT STUDIO &rarr;
                </a>
                <span class="sg-badge-confidential">LIFECYCLE MOAT v4.0</span>
            </div>
        </div>

        <p style="font-size: 13px; color: var(--sg-text-body); line-height: 1.6; margin-bottom: 16px;" x-text="t[lang].sec11_desc"></p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px; margin-bottom: 18px;">
            @foreach(($fiveLifecycleFeatures ?? []) as $lf)
                <div class="sg-subcard" style="border-left: 3px solid #059669;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <strong style="font-size: 12px; color: var(--sg-text-title);">{{ $lf['title']['id'] ?? $lf['code'] }}</strong>
                        <span style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 800; padding: 2px 6px; background: rgba(16, 185, 129, 0.14); color: #059669; border: 1px solid rgba(16, 185, 129, 0.4);">
                            {{ $lf['badge'] ?? 'DAY-30+' }}
                        </span>
                    </div>
                    <div style="font-family: ui-monospace, monospace; font-size: 10px; color: #0284c7; font-weight: 700; margin-bottom: 6px;">
                        Artefak: {{ $lf['artifact'] ?? '-' }}
                    </div>
                    <div style="font-size: 11.5px; color: var(--sg-text-muted); line-height: 1.55;">
                        {{ $lf['summary']['id'] ?? '' }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="padding: 14px; background: var(--sg-subcard-bg); border: 1px solid var(--sg-border); border-radius: 3px;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #059669; text-transform: uppercase; margin-bottom: 10px;">
                5 LAYAR UTAMA "THE ARCHITECT STUDIO" (/BLUEPRINT/STUDIO) — ZERO-SLOP MONOCHROME IDE
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; font-size: 11px;">
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="font-family: ui-monospace, monospace; color: #059669;">Layar 1 // 60s Architecture Wizard</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 3px;">3-Step Split-Screen: 12 Sektor + 9 Stack (M1–S2 Coolify RAM/CPU sizing) + Anti-Slop Toggles.</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="font-family: ui-monospace, monospace; color: #059669;">Layar 2 // 3-Pane Blueprint Studio</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 3px;">100vh Zero-Scroll: Explorer Tree (3) + Visual Canvas (5) + 7-Phase Prompt Dock Ctrl+Shift+C (4) + 64% Token Saved.</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="font-family: ui-monospace, monospace; color: #059669;">Layar 3 // Blueprint Doctor Score</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 3px;">Skor Determinisme tabular-nums 100.0% Zero-Assumption Ready + tombol [+ Buat Otomatis] resolusi instan.</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="font-family: ui-monospace, monospace; color: #059669;">Layar 4 // Side-by-Side Slop Diff</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 3px;">Bukti visual konversi tinggi: Raw AI Prompt Slop vs NeriahPro Project OS v4.0 Deterministic Contract.</div>
                </div>
                <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                    <strong style="font-family: ui-monospace, monospace; color: #059669;">Layar 5 // Execution &amp; MCP Tracker</strong>
                    <div style="color: var(--sg-text-muted); margin-top: 3px;">4 Badge 100% Pass + Sinkronisasi Fase 1–7 Verified hijau otomatis dari Claude Code/Cursor/Antigravity via MCP!</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 12. Section Project OS v5.0 — Mitra Commercial-to-Code Bridge & Arsitektur Satu Pintu Retail vs Mitra -->
    <div id="sec-mitra-commercial-bridge" class="sg-card" x-show="matchesSearch('mitra proyek commercial bridge rab wbs estimator man-hours spk pks sha-256 scope freeze paid change order addendum bast uat given-when-then satu pintu retail vs mitra wizard v5.0')">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--sg-border); padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #d97706; text-transform: uppercase; font-weight: 800;">
                    12 // COMMERCIAL-TO-CODE BRIDGE &amp; SATU PINTU RETAIL VS MITRA (PROJECT OS v5.0)
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--sg-text-title); margin: 4px 0 0 0;" x-text="t[lang].sec12_title"></h3>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ url('/blueprint/studio?mode=mitra') }}" target="_blank" style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; padding: 6px 12px; background: #d97706; color: #ffffff; border: 1px solid #b45309; border-radius: 2px; text-decoration: none;">
                    🤝 BUKA STUDIO MODE MITRA &rarr;
                </a>
                <span class="sg-badge-confidential">SATU PINTU v5.0</span>
            </div>
        </div>

        <p style="font-size: 13px; color: var(--sg-text-body); line-height: 1.6; margin-bottom: 16px;" x-text="t[lang].sec12_desc"></p>

        <!-- 5 Mitra Commercial-to-Code Bridge Engines -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px; margin-bottom: 18px;">
            @foreach(($fiveCommercialFeatures ?? []) as $cf)
                <div class="sg-subcard" style="border-left: 3px solid #d97706;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <strong style="font-size: 12px; color: var(--sg-text-title);">{{ $cf['title']['id'] ?? $cf['code'] }}</strong>
                        <span style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 800; padding: 2px 6px; background: rgba(245, 158, 11, 0.14); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.4);">
                            {{ $cf['badge'] ?? 'MITRA BRIDGE' }}
                        </span>
                    </div>
                    <div style="font-family: ui-monospace, monospace; font-size: 10px; color: #0284c7; font-weight: 700; margin-bottom: 6px;">
                        Artefak Hukum/Keuangan: {{ $cf['artifact'] ?? '-' }}
                    </div>
                    <div style="font-size: 11.5px; color: var(--sg-text-muted); line-height: 1.55;">
                        {{ $cf['summary']['id'] ?? '' }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Satu Pintu Package Classification Table (Retail vs Mitra across 12 Sectors) -->
        <div style="padding: 14px; background: var(--sg-subcard-bg); border: 1px solid var(--sg-border); border-radius: 3px; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #059669; text-transform: uppercase;">
                    KATALOG SATU PINTU (PRICING REGISTRY + MANAGESETTINGS) — 12 SEKTOR FUNGSIONAL RETAIL VS MITRA
                </div>
                <div style="font-family: ui-monospace, monospace; font-size: 11px; color: var(--sg-text-muted);">
                    Rate Estimator Aktif: <strong>Rp {{ number_format((int) ($estimatorConfig['man_hour_rate_idr'] ?? 350000), 0, ',', '.') }}/jam</strong> &bull; Termin: <strong>{{ $estimatorConfig['termin_scheme'] ?? '30_50_20' }}</strong>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="sg-table" style="margin-top: 4px;">
                    <thead>
                        <tr>
                            <th>Paket &amp; Kode</th>
                            <th>Mode Jalur</th>
                            <th>Harga Aktif (Satu Pintu)</th>
                            <th>Cakupan 12 Sektor &amp; Stack</th>
                            <th>Mesin v5.0 yang Terbuka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($satuPintuPackages ?? []) as $pkgCode => $pkgMeta)
                            <tr>
                                <td>
                                    <strong style="color: var(--sg-text-title);">{{ $pkgMeta['name_id'] ?? $pkgCode }}</strong>
                                    <div style="font-family: ui-monospace, monospace; font-size: 10px; color: var(--sg-text-muted);">{{ $pkgCode }}</div>
                                </td>
                                <td>
                                    <span style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 800; padding: 2px 6px; border: 1px solid {{ ($pkgMeta['track_mode'] ?? '') === 'MITRA_TURNKEY_PROJECT' ? 'rgba(245,158,11,0.45)' : 'rgba(16,185,129,0.45)' }}; color: {{ ($pkgMeta['track_mode'] ?? '') === 'MITRA_TURNKEY_PROJECT' ? '#d97706' : '#059669' }};">
                                        {{ $pkgMeta['track_mode'] ?? 'RETAIL_SELF_DEV' }}
                                    </span>
                                </td>
                                <td style="font-family: ui-monospace, monospace; font-weight: 800; color: var(--sg-text-title);">
                                    {{ $pkgMeta['formatted_price'] ?? ('Rp ' . number_format((int) ($pkgMeta['resolved_price'] ?? 0), 0, ',', '.')) }}
                                </td>
                                <td style="font-size: 11px;">
                                    {{ $pkgMeta['sector_scope'] ?? 'Semua 12 Sektor' }}<br>
                                    <span style="font-family: ui-monospace, monospace; font-size: 10px; color: var(--sg-text-muted);">{{ $pkgMeta['stack_scope'] ?? 'M1–S2' }}</span>
                                </td>
                                <td style="font-size: 11px; color: var(--sg-text-muted);">
                                    {{ implode(' • ', array_slice($pkgMeta['included_v5_engines'] ?? [], 0, 3)) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5-Step Guided Client Wizard -->
        <div style="padding: 14px; background: var(--sg-subcard-bg); border: 1px solid var(--sg-border); border-radius: 3px;">
            <div style="font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #0284c7; text-transform: uppercase; margin-bottom: 10px;">
                ALUR WIZARD TERPANDU SATU PINTU (END-TO-END DARI /PRICING SAMPAI DIGITAL BAST)
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 10px; font-size: 11px;">
                @foreach(($guidedJourneySteps ?? []) as $gStep)
                    <div style="padding: 10px; background: var(--sg-card-bg); border: 1px solid var(--sg-border);">
                        <strong style="font-family: ui-monospace, monospace; color: #d97706;">0{{ $gStep['step'] }} // {{ $gStep['title']['id'] ?? '' }}</strong>
                        <div style="font-family: ui-monospace, monospace; font-size: 10px; color: #059669; margin-top: 2px;">{{ $gStep['route'] ?? '' }}</div>
                        <div style="color: var(--sg-text-muted); margin-top: 4px; line-height: 1.45;">{{ $gStep['desc']['id'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<script>
function smartGuideApp() {
    return {
        lang: 'id',
        activeSection: 'sec-overview',
        searchQuery: '',
        salesVolume: 30,

        t: {
            id: {
                nav_overview: 'Ringkasan Eksekutif',
                nav_cogs: 'COGS & Margin Riil',
                nav_model: 'Model Pay-Per-Project',
                nav_simulator: 'Simulator Laba',
                nav_matrix: 'Matriks Batasan',
                nav_playbook: 'Playbook CS',
                nav_factory_pillars: '10 Pilar OS',
                nav_sectoral_ui: '12 Sektor UI/UX',
                nav_killer_moat: '6 Killer Moat',
                nav_day2_ops: '8 Layer Day-2 Ops',
                nav_lifecycle_studio: 'Day-30+ & 3-Pane Studio',
                nav_mitra_bridge: 'Mitra Bridge & Satu Pintu',
                search_placeholder: 'Cari topik / formula margin...',
                confidential_badge: 'DOKUMEN RAHASIA // EKSEKUTIF, FOUNDER & CALON INVESTOR DUE DILIGENCE',
                access_verified: 'Akses Terverifikasi',
                founder_title: 'Sistem Perlindungan Margin & Panduan Retensi Bisnis Neriah Pro',
                founder_desc: 'Halaman ini memuat validasi unit economics 98%+ margin, kalkulator laba kas, dan playbook retensi/upsell untuk evaluasi Founder dan Due Diligence Calon Investor. Reviewer Midtrans dan Pelanggan dilarang keras mengakses dokumen ini.',
                sec1_q: '“Kalau Lifetime Itu Apa Gak Rugi Saya? Bagaimana Marginnya? Apa Mereka Tidak Bayar Lagi?”',
                sec1_p1_title: 'Prinsip Pay-Per-Project',
                sec1_p1_text: 'Bukan Langganan Bikin Proyek Unlimited! 1 pembelian lisensi (Rp 399.000) strictly hanya berlaku untuk 1 Entitas Proyek. Klien yang ingin membuat proyek sistem baru wajib membeli lisensi baru.',
                sec1_p2_title: 'Zero Marginal Cost (O(1))',
                sec1_p2_text: 'Hak unduh selamanya hanya berlaku untuk berkas dokumen proyek yang sudah di-generate. Biaya download ulang dari PostgreSQL adalah Rp 0 tanpa memanggil AI lagi.',
                sec1_p3_title: 'Trojan Horse Upsell Rp 50 Juta',
                sec1_p3_text: 'Blueprint adalah mesin penyaring founder berdana (High-Intent Lead). Dari setiap 20 pembeli blueprint mandiri, 1-2 founder akan meng-upsell diri ke Kontrak Monolith MVP Rp 50 Juta.',
                sec2_title: 'Rincian Biaya Riil (COGS) vs Harga Jual per Transaksi',
                sec2_desc: 'Kalkulasi berbasis Google Gemini 1.5 Pro, gateway Midtrans Snap, dan database PostgreSQL 16.',
                th_package: 'Paket Retail',
                th_price: 'Harga Jual',
                th_ai_cost: 'Biaya AI',
                th_payment_fee: 'Biaya Payment',
                th_net_profit: 'Laba Bersih',
                th_margin: 'Gross Margin',
                th_repeat_cycle: 'Siklus Pembelian',
                tb_spark_cycle: 'Auto-reset tgl 1 awal bulan',
                tb_paid_cycle: 'Beli lagi per ide proyek baru',
                tb_ultimate_cycle: '+ Upsell Kontrak Studio MVP',
                sec3_title: 'Definisi Hukum & Mekanisme Lisensi "Hak Unduh Selamanya"',
                sec3_b1_title: '1 Pembelian = 1 ID Proyek (ULID)',
                sec3_b1_text: 'Setiap transaksi terikat erat ke primary key ULID proyek. Tidak bisa dipakai ulang untuk menimpa spesifikasi proyek yang berbeda.',
                sec3_b2_title: 'Jendela Revisi Terbatas',
                sec3_b2_text: 'Revisi AI gratis dibatasi oleh waktu (Lite: 30 hari, Pro: 6 bulan, Ultimate: 1 tahun). Setelah jendela berakhir, revisi AI terkunci.',
                sec3_b3_title: 'Unduh Statis Bebas Biaya',
                sec3_b3_text: 'Dokumen yang sudah scope locked disimpan dalam bentuk teks dan markdown statis. Biaya penyimpanan server per dokumen hanya ~Rp 10/bulan.',
                sec4_title: 'Kalkulator Proyeksi Omzet & Laba Bersih Neriah Pro',
                sec4_desc: 'Geser slider untuk melihat proyeksi laba kas riil dan pipeline kontrak Studio MVP.',
                sim_sales_label: 'Volume Penjualan Paket Pro / Bulan:',
                unit_projects: 'Proyek',
                sim_gross_rev: 'Omzet Kotor Blueprint:',
                sim_ai_cost: 'Total Biaya AI (Gemini):',
                sim_midtrans_cost: 'Total Biaya Midtrans (Payment):',
                sim_net_cash: 'Laba Bersih Tunai (Cash):',
                sim_upsell_title: 'Proyeksi Konversi Klien Turnkey (Rp 50 Juta / Proyek)',
                sim_upsell_desc: 'Jika 5% pembeli blueprint (1 dari 20 orang) menyewa tim Neriah Pro untuk koding sistem:',
                sim_upsell_clients: 'Estimasi Klien Studio MVP:',
                sim_upsell_pipeline: 'Pipeline Kontrak Studio:',
                sec5_banner_title: '10 PILAR SOFTWARE FACTORY OS (PENGEMBANGAN SISTEM SATU PINTU)',
                pil1_title: 'Pilar 1 // Otak & Kontrak',
                pil1_desc: 'Dokumen PRD 26 Parameter & Spesifikasi Kontrak OpenAPI 3.1 & ADR Suite',
                pil2_title: 'Pilar 2 // Desain UI/UX',
                pil2_desc: 'Token Desain (JSON), Wireframe 4 Layar Utama & 4 Micro-States WCAG AAA',
                pil3_title: 'Pilar 3 // Rangka Koding',
                pil3_desc: 'Docker Compose (PHP 8.4, PG 16, Redis 7) & Rute API Siap Pakai',
                pil4_title: 'Pilar 4 // Data Awal Sistem',
                pil4_desc: 'Generator Data Awal Sintetis (Mock Seeder 100+ Data Realistis & Mode Skala Beban)',
                pil5_title: 'Pilar 5 // Panduan Agen AI',
                pil5_desc: 'Aturan Koding Agen AI (.cursorrules, CLAUDE.md, AGENTS.md, ECC Rules & Anti-Collision)',
                pil6_title: 'Pilar 6 // Jaminan Kualitas',
                pil6_desc: 'Paket Uji Kontrak Otomatis (ApiContractTest Pest & Otomasi Browser Playwright E2E)',
                pil7_title: 'Pilar 7 // Jalur Otomasi Server',
                pil7_desc: 'Pipeline CI/CD GitHub Actions & Skrip deploy.sh VPS Cloud (6 Skenario & Smoke Test)',
                pil8_title: 'Pilar 8 // Telemetri & Pantauan',
                pil8_desc: 'Health Probes (/healthz, /livez, /readyz), Metrik Terstruktur & Error Listener',
                pil9_title: 'Pilar 9 // Pemulihan Bencana',
                pil9_desc: 'Backup Terenkripsi Otomatis (backup.sh & restore.sh Drills) & Runbook Pemulihan',
                pil10_title: 'Pilar 10 // Dokumen API Hidup',
                pil10_desc: 'Dokumentasi Interaktif Scalar OpenAPI (/docs/api) & Sinkronisasi Arsitektur',
                sec5_title: 'Matriks Penegakan Batasan Teknis & Siklus Reset',
                sec5_desc: 'Semua batasan telah dikunci di level backend PHP & controller middleware.',
                m_tier: 'Tingkat Paket',
                m_who_codes: 'Siapa yang Koding?',
                m_quota: 'Batas Kuota',
                m_reset: 'Siklus Reset',
                m_validity: 'Masa Berlaku Revisi',
                m_auth: 'Akses & Login',
                m_self: '100% Klien / Tim Sendiri (Neriah Pro = Rp 0 Koding)',
                m_neriah: '100% Tim Senior Architect Neriah Pro (Turnkey)',
                sec6_title: 'Playbook CS & Sales: Script Tanggapan Keberatan Klien',
                sec6_q1: 'Q: "Kenapa proyek kedua saya disuruh bayar lagi? Katanya lifetime?"',
                sec6_a1: '"Halo Kak, betul sekali! Lisensi yang Kakak beli memberikan hak unduh dan akses arsip SELAMANYA untuk proyek [Nama Proyek Pertama] tanpa biaya bulanan. Untuk membangun arsitektur sistem baru dengan spesifikasi, DDL, dan scope yang berbeda, sistem kami membutuhkan komputasi AI baru sehingga memerlukan 1 lisensi terpisah per entitas proyek."',
                sec6_q2: 'Q: "Saya sudah punya blueprint Pro, tapi tim saya bingung cara kodingnya. Bisa tolong kodingin?"',
                sec6_a2: '"Tentu bisa sekali Kak! Paket Blueprint adalah paket Self-Service untuk tim internal Kakak. Namun jika Kakak ingin sistem ini dibangun 100% turnkey dan siap pakai oleh Software Architect & Senior Engineer Neriah Pro, Kakak dapat meng-upgrade ke Kontrak Monolith MVP Studio (mulai Rp 50 Juta). Dokumen Blueprint Kakak akan langsung kami gunakan sebagai acuan sprint produksi!"',
                sec6_q3: 'Q: "Kenapa di Paket Starter belum ada Docker dan AI coding rules? Kan saya juga developer?"',
                sec6_a3: '"Pertanyaan bagus sekali Kak! Paket Starter AI Architect (Rp 99rb) memang dirancang sangat hemat dan lean khusus bagi developer yang hanya butuh spesifikasi PRD formal dan skema SQL DDL mentah untuk diimpor ke database lokal. Namun jika Kakak ingin menghemat 40+ jam kerja tanpa harus pusing setup Docker compose, synthetic mock data seeder, dan AI coding agent rules (.cursorrules) yang siap di-prompting di Cursor / Windsurf, selisih Rp 300rb ke Paket Pro (Rp 399rb) adalah investasi paling efisien yang langsung melipatgandakan kecepatan koding Kakak hari ini juga!"',
                sec6_q4: 'Q: "Apa bedanya beli Blueprint Ultimate (Rp 1.49 Jt) dibanding kontrak Studio MVP (Rp 50 Jt)?"',
                sec6_a4: '"Perbedaannya terletak pada SIAPA YANG MENULIS KODE Kak! Paket Ultimate Software Factory OS (Rp 1.49 Jt) memberikan seluruh 10 pilar cetak biru, wireframe, unit test suite, dan pipeline CI/CD lengkap untuk dieksekusi oleh tim programmer internal Kakak sendiri, ditambah 1 jam konsultasi arsitek. Sedangkan Kontrak Monolith MVP Studio (Rp 50 Jt) adalah layanan turnkey penuh di mana Senior Architect dan Engineer Neriah Pro yang menulis 100% kode aplikasi, memasang VPS, mengintegrasikan payment, dan memberikan garansi bug 3 bulan. Menariknya, biaya Rp 1.49 Jt ini otomatis memotong DP 50% jika Kakak memutuskan lanjut koding bersama kami!"',
                sec6_q5: 'Q: "Gimana kalau di tengah pengerjaan MVP Studio saya mau tambah fitur baru yang ngga ada di PRD awal?"',
                sec6_a5: '"Kami punya sistem Scope Locking Kak untuk menjaga kualitas dan timeline! Jika Kakak ingin tambah fitur di luar PRD yang disepakati, Kakak bisa langsung submit via modul \'Change Request (CR) / Addendum\' di Dashboard Portal Klien. Kami tidak menerima sisipan lewat chat WA/Email ya Kak agar tidak terjadi miskomunikasi. Nanti tim arsitek kami akan evaluasi CR tersebut dan menerbitkan Invoice Addendum plus estimasi waktu tambahan. Fiturnya langsung dikerjakan setelah Addendum disetujui!"',
                sec6_q6: 'Q: "Apa bedanya Project OS Neriah Pro dibanding sekadar minta ChatGPT / Claude bikin PRD sendiri?"',
                sec6_a6: '"ChatGPT biasa hanya menghasilkan esai umum yang memicu AI context-rot dan tabel CRUD generik! Project OS v2.5 Neriah Pro mengompilasi kontrak arsitektur deterministik: 12 Sectoral Industrial UI/UX Archetypes (misal Split POS bebas mouse, Clinical 3-Pane Cockpit, atau Spreadsheet Ledger seimbang), 9 Stack Adapters (M1–S2), 7 Dimensi Zero-Assumption, Phased Prompt Sequencer Fase 1–6 anti-overflow, skrip ./verify-blueprint.sh, hingga 1-Command CLI Bootstrapper (curl -sL neriahpro.com/init/ID | bash) yang langsung siap jalan di terminal developer!"',
                sec6_q7: 'Q: "Perusahaan kami sudah punya database SQL lama (Brownfield), apakah bisa dimodernisasi tanpa kehilangan data?"',
                sec6_a7: '"Sangat bisa Kak! Project OS memiliki fitur Brownfield SQL & Migration Importer yang memindai skema lama Kakak (mendeteksi risiko AUTO_INCREMENT, CASCADE delete, tipe FLOAT, atau ketiadaan tenant/lock), lalu menyusun rencana migrasi 4 tahap Dual-Column Shadow ULID Backfill (Zero-Data-Loss) beserta AI Refactor Prompt otomatis!"',
                sec8_title: 'Klasifikasi Ergonomi UI/UX 12 Sektor Fungsional & Realistic Domain Seeders',
                sec8_desc: 'AI coding agent tidak boleh membuat sekadar tabel CRUD generik untuk semua jenis bisnis! Kasir ritel butuh POS 100vh bebas mouse, dokter butuh Clinical 3-Pane Cockpit tanpa tumpukan modal, dan akuntan butuh Spreadsheet Ledger dengan kunci keseimbangan Debit = Kredit.',
                sec9_title: '6 Mesin "Killer" Zero-to-Production Orchestration (Alasan Developer Ketagihan)',
                sec9_desc: 'Enam kapabilitas orkestrasi eksklusif yang menjembatani blueprint arsitektur langsung ke repositori kode produksi tanpa halusinasi dan tanpa context overflow:',
                sec6_q8: 'Q: "Bagaimana jika kasir butuh cetak struk thermal 58/80mm, impor ribuan SKU Excel tanpa timeout, dan harga berubah tahun depan?"',
                sec6_a8: '"Project OS v3.0 sudah mengunci 8 Layer Day-2 Operations Kak! Meliputi CSS & ESC/POS Thermal 58/80mm + A4 PDF ber-QR SVG lokal, kolom Historical Snapshot (*_snapshot) agar kenaikan harga/PPN tidak pernah merusak laporan historis, Two-Step Staged CSV Importer anti-504 timeout, 4 jalur antrean Redis prioritas, serta Interactive Blueprint Doctor Pre-Flight Validator!"',
                sec10_title: '8 Lapisan Produksi Day-2 (Operasional Hari Kedua) & 10 Pilar Kesempurnaan Ultimate',
                sec10_desc: 'Menutup 100% celah operasional dunia nyata setelah Go-Live: dari cetak struk kasir thermal 58/80mm, onboarding tenant otomatis bebas layar kosong, pembekuan harga historis (*_snapshot), impor massal CSV bertahap, pemisahan 4 antrean Redis, hingga Blueprint Doctor.',
                sec11_title: '5 Mesin Retensi Siklus Hidup Day-30 s/d Day-365 & The 3-Pane Architect Studio',
                sec11_desc: 'Mengubah Neriah Pro dari generator blueprint satu kali menjadi Control Plane seumur hidup: evolusi iteratif v1.0 ke v1.1 (sumber pendapatan kredit berulang!), jembatan Live MCP langsung ke IDE developer, Decision Matrix PHP match(), Phase 7 Red-Team Tenant Shield, serta meja kerja 3-Pane Studio 100vh.',
                sec12_title: '5 Jembatan Komersial-ke-Kode "Mitra Proyek" & Ekosistem Satu Pintu Retail vs Mitra (v5.0)',
                sec12_desc: 'Menghilangkan 100% underpricing, sengketa scope creep, dan telat cairnya termin akhir: kalkulator RAB algoritmik dari jumlah tabel ULID + state machine + lock, pembekuan SPK SHA-256 otomatis, kalkulator Addendum Berbayar (v1.0 -> v1.1), 7 Milestone Gantt Live, serta UAT Given-When-Then & BAST Digital.'
            },
            en: {
                nav_overview: 'Executive Summary',
                nav_cogs: 'COGS & Real Margin',
                nav_model: 'Pay-Per-Project Model',
                nav_simulator: 'Profit Simulator',
                nav_matrix: 'Limit Matrix',
                nav_playbook: 'Sales Playbook',
                nav_factory_pillars: '10 OS Pillars',
                nav_sectoral_ui: '12 Sector UI/UX',
                nav_killer_moat: '6 Killer Moat',
                nav_day2_ops: '8 Day-2 Ops Layers',
                nav_lifecycle_studio: 'Day-30+ & 3-Pane Studio',
                nav_mitra_bridge: 'Mitra Bridge & One-Stop',
                search_placeholder: 'Search topic / margin formula...',
                confidential_badge: 'CONFIDENTIAL // EXECUTIVE, FOUNDER & INVESTOR DUE DILIGENCE',
                access_verified: 'Verified Access',
                founder_title: 'Neriah Pro Margin Shield & Retention Playbook',
                founder_desc: 'This document presents the 98%+ margin unit economics validation, net cash simulator, and retention playbook for Founder governance and prospective Investor Due Diligence. Midtrans reviewers and regular clients are strictly forbidden.',
                sec1_q: '“Does Lifetime Access Cause Us Losses? What Is the Margin? Will Clients Never Pay Again?”',
                sec1_p1_title: 'Pay-Per-Project Model',
                sec1_p1_text: 'NOT Unlimited Project Creation! 1 license (Rp 399,000) strictly applies to 1 Project Entity only. When clients want to architect a new software system next month, they MUST purchase a new license.',
                sec1_p2_title: 'Zero Marginal Cost (O(1))',
                sec1_p2_text: 'Lifetime download only applies to the archived documents of already synthesized projects. Downloading existing documents from PostgreSQL incurs Rp 0 AI cost.',
                sec1_p3_title: 'Trojan Horse to Rp 50M Studio',
                sec1_p3_text: 'The blueprint is a zero-CAC lead generator for well-funded founders. Out of every 20 self-service buyers, 1-2 founders will upsell into our Rp 50,000,000 Turnkey Monolith MVP contract.',
                sec2_title: 'Real COGS vs Selling Price per Transaction',
                sec2_desc: 'Calculations based on Google Gemini 1.5 Pro, Midtrans Snap gateway, and PostgreSQL 16 storage.',
                th_package: 'Retail Tier',
                th_price: 'Price',
                th_ai_cost: 'AI Cost',
                th_payment_fee: 'Payment Fee',
                th_net_profit: 'Net Cash Profit',
                th_margin: 'Gross Margin',
                th_repeat_cycle: 'Repeat Cycle',
                tb_spark_cycle: 'Auto-resets 1st of every month',
                tb_paid_cycle: 'New purchase per new project',
                tb_ultimate_cycle: '+ Studio MVP Contract Upsell',
                sec3_title: 'Legal Definition of "Lifetime Download Access"',
                sec3_b1_title: '1 Purchase = 1 Project ULID',
                sec3_b1_text: 'Every transaction is cryptographically linked to a single ULID primary key. It cannot be overwritten with a different project idea.',
                sec3_b2_title: 'Time-Capped AI Revision Windows',
                sec3_b2_text: 'Free AI re-prompting is capped by time (Lite: 30 days, Pro: 6 months, Ultimate: 1 year). Once expired, AI synthesis locks down.',
                sec3_b3_title: 'Zero-Cost Static Retrieval',
                sec3_b3_text: 'Scope-locked documents are stored as compressed markdown and schema text. Storage overhead is ~Rp 10/month.',
                sec4_title: 'Real-Time Revenue & Profit Simulator',
                sec4_desc: 'Drag the slider to project monthly cash flow and Studio MVP pipeline conversion.',
                sim_sales_label: 'Monthly Pro Package Sales Volume:',
                unit_projects: 'Projects',
                sim_gross_rev: 'Gross Blueprint Revenue:',
                sim_ai_cost: 'Total AI Compute Cost:',
                sim_midtrans_cost: 'Total Midtrans Fees:',
                sim_net_cash: 'Net Cash Flow (Profit):',
                sim_upsell_title: 'Turnkey Client Conversion Pipeline (Rp 50M / Project)',
                sim_upsell_desc: 'If 5% of blueprint buyers (1 in 20) hire Neriah Pro engineers to code their system turnkey:',
                sim_upsell_clients: 'Estimated Studio MVP Clients:',
                sim_upsell_pipeline: 'Studio MVP Pipeline Value:',
                sec5_banner_title: 'THE 10 PILLARS OF SOFTWARE FACTORY OS (ONE-STOP DEVELOPMENT)',
                pil1_title: 'Pillar 1 // Brain & Contract Spec',
                pil1_desc: '26-Parameter PRD Document, OpenAPI 3.1 Contract Spec & ADR Suite',
                pil2_title: 'Pillar 2 // Interface & UI/UX',
                pil2_desc: 'Design Tokens JSON, 4 Core Screens Wireframe Blueprint & WCAG AAA States',
                pil3_title: 'Pillar 3 // Scaffold Container Stack',
                pil3_desc: 'Docker Compose (PHP 8.4, PG 16, Redis 7) & API Routes',
                pil4_title: 'Pillar 4 // Synthetic Vital Data',
                pil4_desc: 'Synthetic Mock Data Seeder Engine (100+ Realistic Records & Scale Mode)',
                pil5_title: 'Pillar 5 // AI Agent Intelligence',
                pil5_desc: 'AI Coding Agent Directives (.cursorrules, CLAUDE.md, AGENTS.md, ECC & Anti-Collision)',
                pil6_title: 'Pillar 6 // Quality Assurance (QA)',
                pil6_desc: 'Automated Feature Contract-First Tests (Pest / PHPUnit) & Playwright Browser Automation',
                pil7_title: 'Pillar 7 // Server CI/CD Expressway',
                pil7_desc: 'One-Click Cloud GitHub Actions CI/CD & deploy.sh Pipeline (6 Scenarios & Smoke Tests)',
                pil8_title: 'Pillar 8 // Telemetry & Observability',
                pil8_desc: 'Health Probes (/healthz, /livez, /readyz), Structured Metrics & Error Alert Listener',
                pil9_title: 'Pillar 9 // Disaster Recovery & Drills',
                pil9_desc: 'Automated Encrypted Backups (backup.sh & restore.sh Drills) & Zero-Data-Loss Runbooks',
                pil10_title: 'Pillar 10 // Living Contract & API Docs',
                pil10_desc: 'Scalar Interactive OpenAPI Documentation (/docs/api) & Architecture Sync',
                sec5_title: 'Technical Limit & Reset Cycle Matrix',
                sec5_desc: 'All constraints are enforced at the PHP backend and middleware layer.',
                m_tier: 'Package Tier',
                m_who_codes: 'Who Does the Coding?',
                m_quota: 'Quota Limits',
                m_reset: 'Reset Cycle',
                m_validity: 'Revision Window',
                m_auth: 'Authentication',
                m_self: '100% Client / Own Developers (Neriah Pro = Rp 0 Coding)',
                m_neriah: '100% Neriah Pro Senior Architects (Turnkey)',
                sec6_title: 'Sales & CS Playbook: Objection Handling Scripts',
                sec6_q1: 'Q: "Why am I asked to pay again for my second project? Isn\'t it lifetime?"',
                sec6_a1: '"Hi! Exactly right! Your purchased license grants LIFETIME download and archive access to [Project Name] without monthly fees. To architect a brand new system with different DDL, specifications, and scope, our system initiates a new AI synthesis cycle requiring a separate license per project entity."',
                sec6_q2: 'Q: "I have the Pro blueprint, but my team doesn\'t know how to code it. Can you build it?"',
                sec6_a2: '"Absolutely! The Blueprint is a Self-Service package for internal execution. If you prefer our Senior Architects and Engineers to build this 100% turnkey, you can upgrade directly to our Monolith MVP Studio Contract (from Rp 50 Million). Your existing blueprint will serve as the exact production sprint specification!"',
                sec6_q3: 'Q: "Why doesn\'t the Starter package include Docker and AI coding rules? I\'m a developer too."',
                sec6_a3: '"Great question! The Starter AI Architect tier (Rp 99k) is designed to be ultra-lean for solo developers who only need the formal PRD specification and raw SQL DDL to import directly into local databases. However, if you want to save 40+ engineering hours and eliminate manual setup of Docker containers, synthetic mock data seeders, and AI coding agent rules (.cursorrules) ready for Cursor / Windsurf, the Rp 300k jump to Pro (Rp 399k) is the most cost-effective lever to immediately supercharge your development velocity today!"',
                sec6_q4: 'Q: "What is the difference between buying the Ultimate Blueprint (Rp 1.49M) vs hiring Studio MVP (Rp 50M)?"',
                sec6_a4: '"The core difference is WHO WRITES THE CODE! The Ultimate Software Factory OS (Rp 1.49M) provides all 10 pillars of blueprint specifications, wireframes, contract test suites, and CI/CD pipelines for your internal developers to execute, plus a 1-hour Principal Architect consultation. Meanwhile, the Studio Monolith MVP Contract (Rp 50M) is a 100% turnkey service where Neriah Pro senior architects and engineers write every line of production code, harden your VPS, integrate payment gateways, and back it with a 3-month SLA warranty. Best of all, your advisory investment is 100% credited toward the 50% Down Payment if you proceed with our engineering studio!"',
                sec6_q5: 'Q: "What if I want to add new features mid-development that weren\'t in the original PRD?"',
                sec6_a5: '"We enforce a strict Scope Locking protocol to ensure elite quality and on-time delivery! If you need to add features outside the agreed PRD, you can formally submit it via the \'Change Request (CR) / Addendum\' module in your Client Portal Dashboard. We do not accept arbitrary requests via WhatsApp/Email to prevent miscommunication. Our architects will evaluate your CR and issue an Addendum Invoice along with a revised timeline estimate. The new feature development will commence immediately upon Addendum approval!"',
                sec6_q6: 'Q: "How is Neriah Pro Project OS different from prompting ChatGPT or Claude for a PRD?"',
                sec6_a6: '"Raw LLM prompts produce generic essays that cause context-rot and boring CRUD tables! Project OS v2.5 compiles deterministic engineering contracts: 12 Sectoral Industrial UI/UX Archetypes (mouse-free Split POS, 3-Pane Clinical Cockpit, Balanced Spreadsheet Ledger), 9 Stack Adapters (M1–S2), 7 Zero-Assumption Dimensions, a 6-Phase Prompt Sequencer, ./verify-blueprint.sh slop scanner, and a 1-Command CLI Bootstrapper (curl -sL neriahpro.com/init/ID | bash)!"',
                sec6_q7: 'Q: "We already have a legacy SQL database (Brownfield). Can we modernize without data loss?"',
                sec6_a7: '"Yes! Project OS includes a Brownfield SQL & Migration Importer that audits legacy schemas for AUTO_INCREMENT, CASCADE delete, FLOAT precision drift, and missing locks, generating a 4-stage Dual-Column Shadow ULID Backfill plan (Zero-Data-Loss) and ready-to-run AI Refactor Prompt!"',
                sec8_title: '12 Functional Sectoral Industrial UI/UX Archetypes & Realistic Seeders',
                sec8_desc: 'AI coding agents are strictly forbidden from building generic CRUD tables for primary operational screens. Retail cashiers get a mouse-free 100vh Split POS, doctors get a 3-Pane Clinical Cockpit, and accountants get a balanced Spreadsheet Ledger.',
                sec9_title: '6 Killer Zero-to-Production Orchestration Engines (Developer Retention Moat)',
                sec9_desc: 'Six proprietary orchestration engines that bridge architectural blueprints directly into zero-slop production repositories without context overflow:',
                sec6_q8: 'Q: "What about 58/80mm thermal receipts, importing 5,000+ Excel SKUs without 504 timeouts, and historical price immutability?"',
                sec6_a8: '"Project OS v3.0 bakes in all 8 Day-2 Production Layers: ESC/POS 58/80mm thermal + page-break proof A4 PDFs with local SVG QR codes, frozen *_snapshot columns on every transaction line, Two-Step Staged CSV Importer on queue:low-batch, 4 segregated Redis queue lanes, and the Interactive Blueprint Doctor!"',
                sec10_title: 'The Final 8 Day-2 Production Layers & 10 Ultimate Perfection Pillars',
                sec10_desc: 'Eliminates 100% of Day-2 post-launch blind spots: thermal 58/80mm & A4 print contracts, atomic tenant zero-state provisioning, historical *_snapshot immutability, staged bulk CSV migration, 4-lane Redis queues, private disk Signed URLs, and Blueprint Doctor.',
                sec11_title: '5 Day-30 to Day-365 Lifecycle Retention Engines & The 3-Pane Architect Studio',
                sec11_desc: 'Transforms Neriah Pro from a one-off blueprint generator into a permanent engineering Control Plane: iterative v1.0 -> v1.1 Delta Prompts (recurring credit monetization), Live MCP IDE Bridge, PHP match() Decision Tables, Phase 7 Red-Team Suite, and the 100vh 3-Pane Architect Studio.',
                sec12_title: '5 Commercial-to-Code Bridges for Mitra Proyek & Unified Retail vs Mitra Architecture (v5.0)',
                sec12_desc: 'Eliminates underpricing, scope creep disputes, and delayed final settlements: algorithmic RAB/WBS man-hour estimator, cryptographic SPK SHA-256 Scope Freeze, Paid Change-Order Addendum calculator, 7-Milestone Live Gantt, and interactive Given-When-Then UAT + Digital BAST.'
            }
        },

        matchesSearch(text) {
            if (!this.searchQuery || this.searchQuery.trim() === '') return true;
            return text.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
        },

        formatCurrency(val) {
            return 'Rp ' + Number(val).toLocaleString('id-ID');
        },

        scrollTo(id) {
            this.activeSection = id;
            const el = document.getElementById(id);
            if (el) {
                const navHeight = 120;
                const elementPosition = el.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - navHeight;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        },

        initScrollSpy() {
            const sections = ['sec-overview', 'sec-cogs', 'sec-pay-per-project', 'sec-simulator', 'sec-matrix', 'sec-playbook', 'sec-factory-pillars', 'sec-sectoral-ui', 'sec-killer-moat', 'sec-day2-ultimate', 'sec-lifecycle-studio', 'sec-mitra-commercial-bridge'];
            window.addEventListener('scroll', () => {
                let current = this.activeSection;
                for (const id of sections) {
                    const el = document.getElementById(id);
                    if (el) {
                        const rect = el.getBoundingClientRect();
                        if (rect.top <= 200 && rect.bottom >= 120) {
                            current = id;
                            break;
                        }
                    }
                }
                this.activeSection = current;
            }, { passive: true });
        }
    };
}
</script>
</x-filament-panels::page>
