<x-filament-panels::page>
<div class="smart-guide-root" x-data="smartGuideApp()" x-init="initScrollSpy()">

    <!-- Scoped Resilient Styling for Filament v5 Compatibility -->
    <style>
        .smart-guide-root {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #f4f4f5;
            background: #09090b;
            padding: 24px;
            border-radius: 4px;
            border: 1px solid #27272a;
            position: relative;
        }
        .sg-card {
            background: #121215;
            border: 1px solid #27272a;
            border-radius: 4px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            transition: border-color 0.2s;
        }
        .sg-card:hover {
            border-color: #3f3f46;
        }
        .sg-badge-confidential {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .sg-badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 2px;
        }
        .sg-floating-nav {
            position: sticky;
            top: 16px;
            z-index: 40;
            background: rgba(18, 18, 21, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid #3f3f46;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
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
            color: #a1a1aa;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .sg-nav-btn:hover {
            color: #ffffff;
            background: #27272a;
        }
        .sg-nav-btn.active {
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.15);
            border-color: rgba(56, 189, 248, 0.4);
            font-weight: 700;
        }
        .sg-search-bar {
            display: flex;
            align-items: center;
            background: #09090b;
            border: 1px solid #3f3f46;
            border-radius: 2px;
            padding: 4px 10px;
            width: 260px;
        }
        .sg-search-input {
            background: transparent;
            border: none;
            outline: none;
            color: #ffffff;
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
            border: 1px solid #3f3f46;
            background: #09090b;
            color: #a1a1aa;
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
            background: #09090b;
            color: #d4d4d8;
            font-family: ui-monospace, monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #27272a;
        }
        .sg-table td {
            padding: 12px;
            border-bottom: 1px solid #1f1f23;
            color: #e4e4e7;
        }
        .sg-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
        .sg-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .sg-subcard {
            background: #09090b;
            border: 1px solid #27272a;
            border-radius: 3px;
            padding: 18px;
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
            <span style="font-size: 11px; font-family: ui-monospace, monospace; font-weight: 700; color: #71717a; margin-right: 4px;">INDEX:</span>
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
        </div>

        <!-- Right Controls: Bulletproof Search & Language Switcher -->
        <div style="display: flex; align-items: center; gap: 10px;">
            <!-- Realtime Search Filter -->
            <div class="sg-search-bar">
                <span style="color: #71717a; margin-right: 6px; font-size: 12px;">🔍</span>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    :placeholder="t[lang].search_placeholder" 
                    class="sg-search-input"
                />
                <button 
                    x-show="searchQuery" 
                    @click="searchQuery = ''" 
                    style="color: #a1a1aa; background: transparent; border: none; cursor: pointer; font-size: 11px;"
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
    <div x-show="searchQuery.trim() !== ''" style="margin-bottom: 16px; padding: 8px 14px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 2px; font-size: 12px; color: #38bdf8;">
        Menyaring topik dengan kata kunci: "<strong x-text="searchQuery"></strong>" &bull; 
        <button @click="searchQuery = ''" style="text-decoration: underline; color: #ffffff; cursor: pointer; background: transparent; border: none; font-size: 12px;">Reset Pencarian</button>
    </div>

    <!-- 00. Confidential Founder Security Notice -->
    <div class="sg-card" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
            <div class="sg-badge-confidential">
                🔒 <span x-text="t[lang].confidential_badge"></span>
            </div>
            <div class="sg-badge-active">
                ✅ <span x-text="t[lang].access_verified"></span>: yoseph.iriandi.tambunan@gmail.com
            </div>
        </div>
        <h2 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 4px 0 8px 0;" x-text="t[lang].founder_title"></h2>
        <p style="font-size: 12px; color: #a1a1aa; line-height: 1.6; margin: 0;" x-text="t[lang].founder_desc"></p>
    </div>

    <!-- 01. Section Overview & Jawaban Strategis Founder -->
    <div id="sec-overview" class="sg-card" x-show="matchesSearch(t[lang].sec1_title + ' ' + t[lang].sec1_q + ' ' + t[lang].sec1_p1_text + ' ' + t[lang].sec1_p2_text + ' ' + t[lang].sec1_p3_text)">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 16px;">
            <div>
                <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #818cf8; text-transform: uppercase; font-weight: 700;">
                    01 // EXECUTIVE SUMMARY
                </span>
                <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 4px 0 0 0;" x-text="t[lang].sec1_q"></h3>
            </div>
            <span style="font-family: ui-monospace, monospace; font-size: 12px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 2px;">
                GROSS MARGIN: 98.6%
            </span>
        </div>

        <div class="sg-grid-3">
            <div class="sg-subcard">
                <div class="sg-accent-num" style="color: #818cf8;">01</div>
                <h4 style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;" x-text="t[lang].sec1_p1_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0;" x-text="t[lang].sec1_p1_text"></p>
            </div>
            <div class="sg-subcard">
                <div class="sg-accent-num" style="color: #38bdf8;">02</div>
                <h4 style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;" x-text="t[lang].sec1_p2_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0;" x-text="t[lang].sec1_p2_text"></p>
            </div>
            <div class="sg-subcard">
                <div class="sg-accent-num" style="color: #34d399;">03</div>
                <h4 style="font-size: 13px; font-weight: 700; color: #ffffff; margin-bottom: 6px;" x-text="t[lang].sec1_p3_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0;" x-text="t[lang].sec1_p3_text"></p>
            </div>
        </div>
    </div>

    <!-- 02. Section Unit Economics & COGS Riil -->
    <div id="sec-cogs" class="sg-card" x-show="matchesSearch(t[lang].sec2_title + ' ' + t[lang].sec2_desc + ' spark lite pro ultimate cogs margin')">
        <div style="border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #38bdf8; text-transform: uppercase; font-weight: 700;">
                02 // UNIT ECONOMICS BREAKDOWN
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 4px 0 0 0;" x-text="t[lang].sec2_title"></h3>
            <p style="font-size: 12px; color: #a1a1aa; margin: 4px 0 0 0;" x-text="t[lang].sec2_desc"></p>
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
                        <td style="font-weight: 700; color: #ffffff;">Spark (Lead Magnet)</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">Rp 0</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 500 (10k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">Rp 0</td>
                        <td style="font-family: ui-monospace, monospace; color: #f59e0b;">-Rp 500 (CAC)</td>
                        <td style="font-family: ui-monospace, monospace; color: #71717a;">Free Tier</td>
                        <td style="color: #a1a1aa;" x-text="t[lang].tb_spark_cycle"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #38bdf8;">Lite Blueprint</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #38bdf8;">Rp 99.000</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 1.000 (20k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 2.000 (Midtrans)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">Rp 96.000</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">97.0%</td>
                        <td style="color: #a1a1aa;" x-text="t[lang].tb_paid_cycle"></td>
                    </tr>
                    <tr style="background: rgba(79, 70, 229, 0.08); border-left: 3px solid #6366f1;">
                        <td style="font-weight: 800; color: #a5b4fc;">★ Pro Blueprint (Core)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 800; color: #818cf8;">Rp 399.000</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 1.500 (30k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 4.000 (Midtrans)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 800; color: #10b981;">Rp 393.500</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 800; color: #10b981;">98.6%</td>
                        <td style="color: #a1a1aa;" x-text="t[lang].tb_paid_cycle"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #c084fc;">Ultimate Enterprise</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #c084fc;">Rp 1.490.000</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 3.000 (60k tok)</td>
                        <td style="font-family: ui-monospace, monospace; color: #a1a1aa;">~Rp 6.000 (Midtrans)</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">Rp 1.481.000</td>
                        <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #10b981;">99.4%</td>
                        <td style="color: #a1a1aa;" x-text="t[lang].tb_ultimate_cycle"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 03. Section Pay-Per-Project Mechanics -->
    <div id="sec-pay-per-project" class="sg-card" x-show="matchesSearch(t[lang].sec3_title + ' pay per project lifetime lisensi entitas')">
        <div style="border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #a855f7; text-transform: uppercase; font-weight: 700;">
                03 // LEGAL & BUSINESS DEFINITION
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 4px 0 0 0;" x-text="t[lang].sec3_title"></h3>
        </div>

        <div class="sg-grid-3">
            <div class="sg-subcard">
                <h4 style="font-size: 13px; font-weight: 700; color: #38bdf8; margin-bottom: 6px;" x-text="t[lang].sec3_b1_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0;" x-text="t[lang].sec3_b1_text"></p>
            </div>
            <div class="sg-subcard">
                <h4 style="font-size: 13px; font-weight: 700; color: #f59e0b; margin-bottom: 6px;" x-text="t[lang].sec3_b2_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0;" x-text="t[lang].sec3_b2_text"></p>
            </div>
            <div class="sg-subcard">
                <h4 style="font-size: 13px; font-weight: 700; color: #10b981; margin-bottom: 6px;" x-text="t[lang].sec3_b3_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0;" x-text="t[lang].sec3_b3_text"></p>
            </div>
        </div>
    </div>

    <!-- 04. Section Live Interactive Margin & Pipeline Simulator -->
    <div id="sec-simulator" class="sg-card" x-show="matchesSearch(t[lang].sec4_title + ' simulator kalkulator proyeksi laba omzet')">
        <div style="border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #10b981; text-transform: uppercase; font-weight: 700;">
                04 // REAL-TIME FINANCIAL SIMULATOR
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 4px 0 0 0;" x-text="t[lang].sec4_title"></h3>
            <p style="font-size: 12px; color: #a1a1aa; margin: 4px 0 0 0;" x-text="t[lang].sec4_desc"></p>
        </div>

        <div class="sg-simulator-box">
            <!-- Left: Slider & Direct Blueprint Revenue -->
            <div class="sg-subcard">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 12px; font-weight: 700; color: #d4d4d8;" x-text="t[lang].sim_sales_label"></span>
                    <span style="font-family: ui-monospace, monospace; font-size: 14px; font-weight: 900; color: #818cf8;" x-text="salesVolume + ' ' + t[lang].unit_projects"></span>
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
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #1f1f23;">
                        <span style="color: #a1a1aa;" x-text="t[lang].sim_gross_rev"></span>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #ffffff;" x-text="formatCurrency(salesVolume * 399000)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #1f1f23;">
                        <span style="color: #a1a1aa;" x-text="t[lang].sim_ai_cost"></span>
                        <span style="font-family: ui-monospace, monospace; color: #f87171;" x-text="formatCurrency(salesVolume * 1500)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #1f1f23;">
                        <span style="color: #a1a1aa;" x-text="t[lang].sim_midtrans_cost"></span>
                        <span style="font-family: ui-monospace, monospace; color: #f87171;" x-text="formatCurrency(salesVolume * 4000)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; font-weight: 800;">
                        <span style="color: #34d399;" x-text="t[lang].sim_net_cash"></span>
                        <span style="font-family: ui-monospace, monospace; color: #34d399;" x-text="formatCurrency(salesVolume * 393500)"></span>
                    </div>
                </div>
            </div>

            <!-- Right: Studio MVP Upsell Pipeline -->
            <div class="sg-subcard" style="border: 1px solid rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.04);">
                <div style="font-family: ui-monospace, monospace; font-size: 10px; font-weight: 800; color: #a5b4fc; text-transform: uppercase; margin-bottom: 4px;">
                    THE TROJAN HORSE EFFECT
                </div>
                <h4 style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 8px;" x-text="t[lang].sim_upsell_title"></h4>
                <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin-bottom: 12px;" x-text="t[lang].sim_upsell_desc"></p>

                <div style="background: #09090b; padding: 12px; border: 1px solid #27272a; border-radius: 2px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                        <span style="color: #a1a1aa;" x-text="t[lang].sim_upsell_clients"></span>
                        <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #818cf8;" x-text="Math.floor(salesVolume * 0.05) + ' Klien'"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 900; color: #10b981;">
                        <span x-text="t[lang].sim_upsell_pipeline"></span>
                        <span style="font-family: ui-monospace, monospace;" x-text="formatCurrency(Math.floor(salesVolume * 0.05) * 50000000)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 05. Section Technical Limits & Enforcement Matrix -->
    <div id="sec-matrix" class="sg-card" x-show="matchesSearch(t[lang].sec5_title + ' batasan kuota reset spark lite pro ultimate window otp')">
        <div style="border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #f59e0b; text-transform: uppercase; font-weight: 700;">
                05 // BACKEND ENFORCEMENT MATRIX
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 4px 0 0 0;" x-text="t[lang].sec5_title"></h3>
            <p style="font-size: 12px; color: #a1a1aa; margin: 4px 0 0 0;" x-text="t[lang].sec5_desc"></p>
        </div>

        <div style="overflow-x: auto;">
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
                        <td style="font-weight: 700; color: #a1a1aa;">Spark (Free)</td>
                        <td style="color: #fca5a5;" x-text="t[lang].m_self"></td>
                        <td>2x Analisis Ide</td>
                        <td style="color: #38bdf8;">Auto-Reset tgl 1 tiap bulan</td>
                        <td>7 Hari Sesi Tamu</td>
                        <td style="color: #10b981;">Bebas (Guest Mode)</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #38bdf8;">Lite (Rp 99k)</td>
                        <td style="color: #fca5a5;" x-text="t[lang].m_self"></td>
                        <td>1 Entitas Proyek</td>
                        <td>Pay-Per-Project</td>
                        <td>30 Hari Jendela Revisi Form</td>
                        <td style="color: #f59e0b;">Wajib Login (OTP Email)</td>
                    </tr>
                    <tr style="background: rgba(79, 70, 229, 0.08);">
                        <td style="font-weight: 800; color: #818cf8;">Pro (Rp 399k)</td>
                        <td style="color: #fca5a5;" x-text="t[lang].m_self"></td>
                        <td>1 Entitas Proyek</td>
                        <td>Pay-Per-Project</td>
                        <td>6 Bulan Unlimited AI Re-prompt</td>
                        <td style="color: #f59e0b;">Wajib Login (OTP Email)</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #c084fc;">Ultimate (Rp 1.49M)</td>
                        <td style="color: #fca5a5;" x-text="t[lang].m_self"></td>
                        <td>1 Entitas Enterprise</td>
                        <td>Pay-Per-Project</td>
                        <td>1 Tahun Prioritas & 60 Hari Call</td>
                        <td style="color: #f59e0b;">Wajib Akun Terverifikasi</td>
                    </tr>
                    <tr style="background: rgba(16, 185, 129, 0.08); border-top: 2px solid #10b981;">
                        <td style="font-weight: 800; color: #34d399;">Studio MVP (Rp 50M)</td>
                        <td style="font-weight: 800; color: #34d399;" x-text="t[lang].m_neriah"></td>
                        <td>Turnkey Monolith System</td>
                        <td>Milestone 50/50 DP</td>
                        <td>SLA 30 Hari Garansi Bug</td>
                        <td style="color: #10b981;">Kontrak Digital Sah</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 06. Section CS & Sales Playbook -->
    <div id="sec-playbook" class="sg-card" x-show="matchesSearch(t[lang].sec6_title + ' playbook script cs sales jawaban komplain')">
        <div style="border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 16px;">
            <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #ec4899; text-transform: uppercase; font-weight: 700;">
                06 // SALES & CS OBJECTION SCRIPTS
            </span>
            <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 4px 0 0 0;" x-text="t[lang].sec6_title"></h3>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-bottom: 6px;" x-text="t[lang].sec6_q1"></div>
                <div style="font-size: 12px; color: #d4d4d8; font-style: italic; background: #18181b; padding: 12px; border: 1px solid #27272a; border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a1"></div>
            </div>
            <div class="sg-subcard">
                <div style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 6px;" x-text="t[lang].sec6_q2"></div>
                <div style="font-size: 12px; color: #d4d4d8; font-style: italic; background: #18181b; padding: 12px; border: 1px solid #27272a; border-radius: 2px; line-height: 1.5;" x-text="t[lang].sec6_a2"></div>
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
                search_placeholder: 'Cari topik / formula margin...',
                confidential_badge: 'RAHASIA FOUNDER // HANYA YOSEPH IRIANDI TAMBUNAN',
                access_verified: 'Akses Terverifikasi',
                founder_title: 'Sistem Perlindungan Margin & Panduan Retensi Bisnis Neriah Pro',
                founder_desc: 'Halaman ini memuat rumus unit economics, strategi trojan horse upsell Studio Rp 50 Juta, dan penegakan batasan teknis per paket. Reviewer Midtrans dan staf lainnya diblokir 100% dari URL ini dengan HTTP 403 Forbidden.',
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
                sec6_a2: '"Tentu bisa sekali Kak! Paket Blueprint adalah paket Self-Service untuk tim internal Kakak. Namun jika Kakak ingin sistem ini dibangun 100% turnkey dan siap pakai oleh Software Architect & Senior Engineer Neriah Pro, Kakak dapat meng-upgrade ke Kontrak Monolith MVP Studio (mulai Rp 50 Juta). Dokumen Blueprint Kakak akan langsung kami gunakan sebagai acuan sprint produksi!"'
            },
            en: {
                nav_overview: 'Executive Summary',
                nav_cogs: 'COGS & Real Margin',
                nav_model: 'Pay-Per-Project Model',
                nav_simulator: 'Profit Simulator',
                nav_matrix: 'Limit Matrix',
                nav_playbook: 'Sales Playbook',
                search_placeholder: 'Search topic / margin formula...',
                confidential_badge: 'CONFIDENTIAL // FOUNDER YOSEPH IRIANDI TAMBUNAN ONLY',
                access_verified: 'Verified Access',
                founder_title: 'Neriah Pro Margin Shield & Retention Playbook',
                founder_desc: 'This page details the unit economics formulas, the Trojan horse upsell strategy to Rp 50M Studio contracts, and technical tier limits. Midtrans reviewers and other staff are blocked with HTTP 403 Forbidden.',
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
                sec6_a2: '"Absolutely! The Blueprint is a Self-Service package for internal execution. If you prefer our Senior Architects and Engineers to build this 100% turnkey, you can upgrade directly to our Monolith MVP Studio Contract (from Rp 50 Million). Your existing blueprint will serve as the exact production sprint specification!"'
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
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },

        initScrollSpy() {
            const sections = ['sec-overview', 'sec-cogs', 'sec-pay-per-project', 'sec-simulator', 'sec-matrix', 'sec-playbook'];
            window.addEventListener('scroll', () => {
                let current = this.activeSection;
                for (const id of sections) {
                    const el = document.getElementById(id);
                    if (el) {
                        const rect = el.getBoundingClientRect();
                        if (rect.top <= 180 && rect.bottom >= 180) {
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
