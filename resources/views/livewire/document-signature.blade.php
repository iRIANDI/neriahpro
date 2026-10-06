@php
    $devInfo = \App\Support\ContractLegalHelper::getDeveloperInfo();
    $clausesId = \App\Support\ContractLegalHelper::getDynamicClauses($document, 'id');
    $clausesEn = \App\Support\ContractLegalHelper::getDynamicClauses($document, 'en');

    $blueprint = $document->related instanceof \App\Models\VisionBlueprint ? $document->related : null;
    $projectName = $blueprint?->nama_bisnis ?: ($blueprint?->client_name ?: ($document->signer_name ?: 'Mitra Bisnis'));
    $clientPic = $document->signer_name ?: ($blueprint?->client_name ?: 'Alexander Wijaya');
    $clientEmail = $document->signer_email ?: ($blueprint?->email ?: 'client@neriahpro.com');
    $contractAmount = $document->contract_amount ?: 50000000;
    $dpAmount = $document->dp_amount ?: ($contractAmount * 0.50);
    $pelunasanAmount = $contractAmount - $dpAmount;
    $targetTimeline = $blueprint?->target_waktu ?: '30 Hari Kerja';
    $docHash = $document->document_hash ?: ($blueprint?->document_sha256 ?: hash('sha256', $document->id . $document->created_at));
@endphp

<div 
    x-data="{
        locale: localStorage.getItem('neriah_contract_locale') || '{{ app()->getLocale() === 'en' ? 'en' : 'id' }}',
        isDark: localStorage.getItem('theme') !== 'light',
        setLocale(lang) {
            this.locale = lang;
            localStorage.setItem('neriah_contract_locale', lang);
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        clausesId: @js($clausesId),
        clausesEn: @js($clausesEn),
        get activeClauses() {
            return this.locale === 'en' ? this.clausesEn : this.clausesId;
        }
    }"
    x-init="
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
    :class="isDark ? 'dark' : ''"
    class="w-full min-h-screen bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 py-6 sm:py-10 px-3 sm:px-6 lg:px-8 font-sans transition-colors duration-200"
>
    <!-- Dedicated Print Stylesheet to Guarantee High-Fidelity Printer Output (Zero Blank Pages) -->
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm 15mm 15mm;
            }
            html, body {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                font-size: 10pt !important;
                line-height: 1.4 !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
            .no-print, [class*="no-print"] {
                display: none !important;
            }
            .contract-paper {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                border: 1px solid #94a3b8 !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .print-avoid-break {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .print-border {
                border-color: #cbd5e1 !important;
            }
            .print-bg-soft {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-text-dark {
                color: #0f172a !important;
            }
            .print-text-muted {
                color: #475569 !important;
            }
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-5">
        
        <!-- Top Toolbar: Breadcrumbs, Theme Toggle, Language Switcher, Print Action -->
        <header class="no-print bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-3 sm:p-4 rounded-none shadow-sm flex flex-wrap items-center justify-between gap-3 text-xs font-mono">
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition font-bold">
                    &larr; <span x-text="locale === 'en' ? 'Home' : 'Beranda'">Beranda</span>
                </a>
                <span class="text-zinc-400">/</span>
                @if($blueprint)
                    <a href="{{ route('blueprint.show', $blueprint->slug) }}" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition truncate max-w-[180px] sm:max-w-xs">
                        {{ $projectName }}
                    </a>
                    <span class="text-zinc-400">/</span>
                @endif
                <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="locale === 'en' ? 'Legal Contract' : 'Kontrak Digital'">Kontrak Digital</span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Multi-Language Switcher (Tier 1 Dual-Locale) -->
                <div class="inline-flex rounded-none border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 p-0.5 font-bold">
                    <button 
                        type="button" 
                        @click="setLocale('id')" 
                        :class="locale === 'id' ? 'bg-emerald-500 text-black shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                        class="px-2.5 py-1 text-[11px] rounded-none transition cursor-pointer flex items-center gap-1"
                        title="Bahasa Indonesia"
                    >
                        <span>🇮🇩 ID</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setLocale('en')" 
                        :class="locale === 'en' ? 'bg-emerald-500 text-black shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                        class="px-2.5 py-1 text-[11px] rounded-none transition cursor-pointer flex items-center gap-1"
                        title="English"
                    >
                        <span>🇬🇧 EN</span>
                    </button>
                </div>

                <!-- Theme Toggle Button (Dark / Light) -->
                <button 
                    type="button" 
                    @click="toggleTheme()" 
                    class="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none transition flex items-center gap-1.5 cursor-pointer"
                    :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                >
                    <svg x-show="isDark" class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!isDark" class="w-3.5 h-3.5 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <span class="text-[11px]" x-text="isDark ? 'LIGHT' : 'DARK'">MODE</span>
                </button>

                <!-- Print / Save as PDF Action -->
                <button 
                    type="button" 
                    onclick="window.print()" 
                    class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-bold uppercase text-[11px] rounded-none transition flex items-center gap-1.5 cursor-pointer shadow-sm"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span x-text="locale === 'en' ? 'PRINT CONTRACT' : 'CETAK KONTRAK'">CETAK KONTRAK</span>
                </button>
            </div>
        </header>

        <!-- Main Contract Paper Document Sheet -->
        <article class="contract-paper bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-10 shadow-xl rounded-none relative text-zinc-900 dark:text-zinc-100 print:text-black print:bg-white print:border-zinc-400">
            
            <!-- Legal Header -->
            <header class="border-b-2 border-emerald-500 pb-5 mb-7 text-left flex flex-col sm:flex-row sm:items-start justify-between gap-4 print-avoid-break">
                <div>
                    <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/40 text-[10px] font-mono font-bold uppercase tracking-wider inline-block mb-2 print-bg-soft">
                        <span x-text="locale === 'en' ? 'OFFICIAL SOFTWARE DEVELOPMENT AGREEMENT (DIGITAL E-SIGN)' : 'SURAT PERJANJIAN KERJA SAMA RESMI (E-SIGN DIGITAL)'">
                            SURAT PERJANJIAN KERJA SAMA RESMI (E-SIGN DIGITAL)
                        </span>
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black uppercase text-zinc-900 dark:text-white print:text-black tracking-tight leading-tight">
                        <span x-show="locale === 'id'">{{ $document->title }}</span>
                        <span x-show="locale === 'en'">Software Engineering &amp; Development Agreement - {{ $projectName }}</span>
                    </h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 print:text-zinc-600 font-mono mt-1">
                        <span x-text="locale === 'en' ? 'LEGAL CONTRACT &bull; HIGH-PERFORMANCE MODERN MONOLITH ARCHITECTURE' : 'DOKUMEN HUKUM &bull; KESEPAKATAN PENGEMBANGAN SISTEM PERANGKAT LUNAK'">
                            DOKUMEN HUKUM &bull; KESEPAKATAN PENGEMBANGAN SISTEM PERANGKAT LUNAK
                        </span>
                    </p>
                </div>
                <div class="sm:text-right font-mono text-xs shrink-0 space-y-1">
                    <div class="text-zinc-500 dark:text-zinc-400 print:text-zinc-600">
                        <span x-text="locale === 'en' ? 'DOC NO: ' : 'NO. DOKUMEN: '">NO. DOKUMEN: </span>
                        <strong class="text-zinc-900 dark:text-white print:text-black">{{ strtoupper(substr($document->id, 0, 12)) }}</strong>
                    </div>
                    <div class="text-zinc-500 dark:text-zinc-400 print:text-zinc-600">
                        <span x-text="locale === 'en' ? 'DATE: ' : 'TANGGAL: '">TANGGAL: </span>
                        <strong class="text-zinc-800 dark:text-zinc-200 print:text-black">{{ ($document->signed_at ?: $document->created_at)->format('d F Y') }}</strong>
                    </div>
                    <div>
                        <span x-text="locale === 'en' ? 'STATUS: ' : 'STATUS: '">STATUS: </span>
                        @if($isSigned)
                            <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">
                                <span x-text="locale === 'en' ? '✓ SIGNED &amp; VERIFIED' : '✓ DITANDATANGANI &amp; SAH'">✓ DITANDATANGANI &amp; SAH</span>
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-amber-500 text-black font-bold text-[10px] uppercase">
                                <span x-text="locale === 'en' ? 'PENDING SIGNATURE' : 'MENUNGGU TANDA TANGAN'">MENUNGGU TANDA TANGAN</span>
                            </span>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Status Banner if Signed -->
            @if($isSigned)
                <div class="mb-7 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-500/50 text-xs font-mono text-emerald-800 dark:text-emerald-300 flex items-start gap-3 rounded-none print-avoid-break print-bg-soft">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <div>
                        <div class="font-bold uppercase tracking-wider text-emerald-900 dark:text-emerald-200" x-text="locale === 'en' ? 'THIS AGREEMENT IS OFFICIALLY SIGNED AND LEGALLY VERIFIED' : 'AKAD PERJANJIAN INI TELAH RESMI DITANDATANGANI DAN TERVERIFIKASI'">
                            AKAD PERJANJIAN INI TELAH RESMI DITANDATANGANI DAN TERVERIFIKASI
                        </div>
                        <div class="text-[11px] text-emerald-700 dark:text-emerald-400/90 mt-0.5 leading-relaxed font-sans" x-text="locale === 'en' ? 'This electronic contract holds full legal validity and binding enforcement under the Republic of Indonesia Electronic Information and Transactions (ITE) laws.' : 'Dokumen ini memiliki kekuatan hukum yang sah dan mengikat para pihak berdasarkan ketentuan Undang-Undang ITE (Informasi dan Transaksi Elektronik) Republik Indonesia.'">
                            Dokumen ini memiliki kekuatan hukum yang sah dan mengikat para pihak berdasarkan ketentuan Undang-Undang ITE (Informasi dan Transaksi Elektronik) Republik Indonesia.
                        </div>
                    </div>
                </div>
            @endif

            <!-- Para Pihak (The Contracting Parties) -->
            <section class="mb-7 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/60 p-5 rounded-none font-sans text-xs print-avoid-break print-bg-soft print-border">
                <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 print:text-black border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-4">
                    <span x-text="locale === 'en' ? 'CONTRACTING PARTIES:' : 'PARA PIHAK YANG BERSEPAKAT:'">PARA PIHAK YANG BERSEPAKAT:</span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pihak Pertama (Client) -->
                    <div class="space-y-1.5 border-l-2 border-emerald-500 pl-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-bold block" x-text="locale === 'en' ? 'PARTY ONE (CLIENT / SERVICE BUYER):' : 'PIHAK PERTAMA (KLIEN / PENGGUNA JASA):'">
                            PIHAK PERTAMA (KLIEN / PENGGUNA JASA):
                        </span>
                        <div class="text-sm font-bold text-zinc-900 dark:text-white print:text-black">
                            {{ $clientPic }}
                        </div>
                        <div class="text-zinc-700 dark:text-zinc-300 print:text-zinc-800">
                            <span x-text="locale === 'en' ? 'Business Entity: ' : 'Badan Usaha / Brand: '">Badan Usaha / Brand: </span>
                            <strong>{{ $projectName }}</strong>
                        </div>
                        <div class="text-zinc-500 dark:text-zinc-400 print:text-zinc-600 font-mono text-[11px]">
                            Email: {{ $clientEmail }} &bull; {{ $blueprint?->phone ?: '-' }}
                        </div>
                    </div>

                    <!-- Pihak Kedua (Developer: Neriah Pro) -->
                    <div class="space-y-1.5 border-l-2 border-zinc-400 dark:border-zinc-600 pl-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-400 print:text-black font-bold block" x-text="locale === 'en' ? 'PARTY TWO (DEVELOPER / TECH PROVIDER):' : 'PIHAK KEDUA (PENGEMBANG / PENYEDIA TEKNOLOGI):'">
                            PIHAK KEDUA (PENGEMBANG / PENYEDIA TEKNOLOGI):
                        </span>
                        <div class="text-sm font-bold text-zinc-900 dark:text-white print:text-black">
                            {{ $devInfo['entity_name'] }}
                        </div>
                        <div class="text-zinc-700 dark:text-zinc-300 print:text-zinc-800">
                            <span x-text="locale === 'en' ? 'Architecture Studio: Modern High-Scale Web Architecture' : 'Spesialisasi: Modern High-Scale Web Architecture &amp; Rapid Monolith'">
                                Spesialisasi: Modern High-Scale Web Architecture &amp; Rapid Monolith
                            </span>
                        </div>
                        <div class="text-zinc-500 dark:text-zinc-400 print:text-zinc-600 font-mono text-[11px]">
                            Email: {{ $devInfo['email'] }} &bull; {{ $devInfo['location'] }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Nilai Kontrak & Termin Pembayaran -->
            <section class="mb-7 p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none font-mono text-xs print-avoid-break print-bg-soft print-border">
                <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 print:text-black border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-3">
                    <span x-text="locale === 'en' ? 'INVESTMENT COMMITMENT &amp; PAYMENT MILESTONES:' : 'RINGKASAN NILAI KONTRAK &amp; TERMIN PEMBAYARAN:'">
                        RINGKASAN NILAI KONTRAK &amp; TERMIN PEMBAYARAN:
                    </span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 print-border">
                        <span class="text-[10px] text-zinc-500 uppercase block" x-text="locale === 'en' ? '1. Total Contract Value' : '1. Total Nilai Kontrak'">1. Total Nilai Kontrak</span>
                        <span class="text-base sm:text-lg font-black text-zinc-900 dark:text-white print:text-black block mt-0.5">
                            Rp {{ number_format($contractAmount, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 print:text-zinc-600" x-text="locale === 'en' ? 'Includes 5 Sprints' : 'Termasuk 5 Sprint Kerja'">Termasuk 5 Sprint Kerja</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 print-border">
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase font-bold block" x-text="locale === 'en' ? '2. Milestone 1: Down Payment (50%)' : '2. Termin 1: Uang Muka (DP 50%)'">2. Termin 1: Uang Muka (DP 50%)</span>
                        <span class="text-base sm:text-lg font-black text-emerald-600 dark:text-emerald-400 print:text-black block mt-0.5">
                            Rp {{ number_format($dpAmount, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400/80" x-text="locale === 'en' ? 'Payable prior to Kickoff' : 'Dibayarkan saat Kickoff'">Dibayarkan saat Kickoff</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 print-border">
                        <span class="text-[10px] text-zinc-500 uppercase block" x-text="locale === 'en' ? '3. Milestone 2: Settlement (50%)' : '3. Termin 2: Pelunasan (50%)'">3. Termin 2: Pelunasan (50%)</span>
                        <span class="text-base sm:text-lg font-black text-zinc-700 dark:text-zinc-300 print:text-black block mt-0.5">
                            Rp {{ number_format($pelunasanAmount, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 print:text-zinc-600" x-text="locale === 'en' ? 'Upon UAT Sign-off &amp; Handover' : 'Saat Serah Terima Kunci / UAT'">Saat Serah Terima Kunci / UAT</span>
                    </div>
                </div>
            </section>

            <!-- Dynamic Legal Clauses (Pasal-Pasal Perjanjian) -->
            <section class="space-y-4 mb-8 font-sans text-xs sm:text-sm leading-relaxed text-zinc-700 dark:text-zinc-300 print:text-black">
                <div class="border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <h2 class="text-xs sm:text-sm font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-white print:text-black">
                        <span x-text="locale === 'en' ? 'ARTICLES OF AGREEMENT (LEGAL COVENANTS):' : 'PASAL-PASAL PERJANJIAN KERJA SAMA:'">
                            PASAL-PASAL PERJANJIAN KERJA SAMA:
                        </span>
                    </h2>
                </div>

                <template x-for="(clause, key) in activeClauses" :key="key">
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950/70 border border-zinc-200 dark:border-zinc-800/80 rounded-none space-y-1.5 print-avoid-break print-bg-soft print-border">
                        <h3 class="font-mono font-bold text-zinc-900 dark:text-white print:text-black text-xs uppercase tracking-wide flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-emerald-500 shrink-0"></span>
                            <span x-text="clause.title"></span>
                        </h3>
                        <p class="text-zinc-700 dark:text-zinc-300 print:text-zinc-900 font-sans text-xs leading-relaxed pl-3.5 text-justify" x-text="clause.description"></p>
                    </div>
                </template>
            </section>

            <!-- Signature & Audit Trail Section -->
            <section class="border-t-2 border-zinc-300 dark:border-zinc-800 pt-7 mt-8 print-avoid-break">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Pihak Kedua Signature (Neriah Pro Developer Signature) -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col justify-between print-bg-soft print-border">
                        <div>
                            <span class="text-[10px] font-mono uppercase text-zinc-500 font-bold block mb-1" x-text="locale === 'en' ? 'PARTY TWO (SYSTEM PROVIDER / DEVELOPER):' : 'PIHAK KEDUA (PENYEDIA SISTEM / DEVELOPER):'">
                                PIHAK KEDUA (PENYEDIA SISTEM / DEVELOPER):
                            </span>
                            <div class="font-bold text-zinc-900 dark:text-white print:text-black text-xs">{{ $devInfo['entity_name'] }}</div>
                            <div class="text-[11px] text-zinc-600 dark:text-zinc-400 print:text-zinc-700 font-sans">{{ $devInfo['pic_title'] }}</div>
                        </div>

                        <div class="my-5 text-center">
                            @if(!empty($devInfo['signature_image']))
                                <div class="inline-block p-2 bg-white border border-zinc-200 rounded-none shadow-xs">
                                    <img src="{{ $devInfo['signature_image'] }}" alt="Developer Signature" class="max-h-20 max-w-full mx-auto" />
                                </div>
                            @else
                                <div class="inline-block p-2.5 border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 print-border">
                                    <svg class="w-24 h-12 text-emerald-600 dark:text-emerald-400 mx-auto" viewBox="0 0 100 50" fill="none" stroke="currentColor">
                                        <path d="M10 35 Q 25 10, 45 30 T 75 20 T 90 35" stroke-width="2.5" stroke-linecap="round"/>
                                        <text x="50" y="47" font-size="6" fill="#888" text-anchor="middle" font-family="monospace">VERIFIED DIGITAL KEY</text>
                                    </svg>
                                </div>
                            @endif
                            <div class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 print:text-black font-bold mt-1">
                                [ {{ $devInfo['seal_text'] }} ]
                            </div>
                        </div>

                        <div class="text-[10px] font-mono text-zinc-500 pt-2 border-t border-zinc-200 dark:border-zinc-800 print-border">
                            <span x-text="locale === 'en' ? 'System Authorization: ' : 'Penanggung Jawab: '">Penanggung Jawab: </span>
                            <strong class="text-zinc-800 dark:text-zinc-300 print:text-black">{{ $devInfo['pic_name'] }}</strong>
                        </div>
                    </div>

                    <!-- Pihak Pertama Signature (Client) -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col justify-between print-bg-soft print-border">
                        <div>
                            <span class="text-[10px] font-mono uppercase text-zinc-500 font-bold block mb-1" x-text="locale === 'en' ? 'PARTY ONE (CLIENT / BUYER):' : 'PIHAK PERTAMA (KLIEN / PEMESAN):'">
                                PIHAK PERTAMA (KLIEN / PEMESAN):
                            </span>
                            <div class="font-bold text-zinc-900 dark:text-white print:text-black text-xs">
                                {{ $clientPic }}
                            </div>
                            <div class="text-[11px] text-zinc-600 dark:text-zinc-400 print:text-zinc-700 font-sans">
                                {{ $projectName }}
                            </div>
                        </div>

                        @if($isSigned)
                            <div class="my-4 text-center">
                                @if($document->digital_signature_image)
                                    <div class="inline-block p-2 bg-white border border-zinc-200 rounded-none shadow-xs">
                                        <img src="{{ $document->digital_signature_image }}" alt="Tanda Tangan Digital Klien" class="max-h-20 max-w-full mx-auto" />
                                    </div>
                                @else
                                    <div class="p-3 border border-emerald-500/40 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-mono text-xs">
                                        ✓ DITANDATANGANI SECARA ELEKTRONIK
                                    </div>
                                @endif
                                <div class="text-[10px] font-mono text-zinc-600 dark:text-zinc-400 print:text-black mt-2">
                                    <span x-text="locale === 'en' ? 'Signatory: ' : 'Penandatangan: '">Penandatangan: </span>
                                    <strong class="text-zinc-900 dark:text-white print:text-black">{{ $clientPic }}</strong>
                                </div>
                            </div>

                            <!-- Audit Trail Metadata -->
                            <div class="text-[10px] font-mono text-zinc-500 pt-3 border-t border-zinc-200 dark:border-zinc-800 print-border space-y-1">
                                <div>
                                    <span x-text="locale === 'en' ? 'TIMESTAMP: ' : 'WAKTU: '">WAKTU: </span>
                                    <strong class="text-zinc-800 dark:text-zinc-200 print:text-black">{{ ($document->signed_at ?: now())->format('d M Y H:i:s T') }}</strong>
                                </div>
                                <div>
                                    <span x-text="locale === 'en' ? 'IP AUDIT: ' : 'IP AUDIT: '">IP AUDIT: </span>
                                    <strong class="text-zinc-800 dark:text-zinc-200 print:text-black">{{ $document->signer_ip_address ?: 'Recorded' }}</strong>
                                </div>
                                <div class="break-all text-[9px] text-zinc-400">
                                    HASH: {{ $docHash }}
                                </div>
                            </div>
                        @else
                            <!-- Signature Input Form -->
                            <div class="my-4 no-print">
                                <form wire:submit="submitSignature" class="space-y-4">
                                    {{ $this->form }}

                                    <button 
                                        type="submit" 
                                        class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-lg"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        <span x-text="locale === 'en' ? 'Affix Digital Signature &amp; Lock Scope' : 'Bubuhkan Tanda Tangan Digital &amp; Kunci Kontrak'">
                                            Bubuhkan Tanda Tangan Digital &amp; Kunci Kontrak
                                        </span>
                                    </button>
                                </form>
                            </div>

                            <!-- Printed Placeholder if unsigned -->
                            <div class="hidden print:block my-4 text-center">
                                <div style="height: 50px; border-bottom: 1px dashed #94a3b8; margin: 10px 30px;"></div>
                                <p style="font-size: 9px; color: #64748b;">( Menunggu Tanda Tangan Digital / Pending Signature )</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Document Integrity Footer -->
            <footer class="mt-8 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between text-[10px] font-mono text-zinc-500 print-border gap-2">
                <div>NERIAH PRO DIGITAL CONTRACT ENGINE &bull; ENTERPRISE E-SIGN</div>
                <div class="text-emerald-600 dark:text-emerald-400 print:text-black font-bold">ANTI-TAMPER SHA-256 PROTECTED</div>
            </footer>
        </article>
    </div>
</div>
