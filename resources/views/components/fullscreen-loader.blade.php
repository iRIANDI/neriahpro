@props([
    'feature' => null,
    'titleId' => null,
    'titleEn' => null,
    'subtitleId' => null,
    'subtitleEn' => null,
    'badge' => null,
])

@php
    use App\Support\BrandLogoHelper;

    // 1. Resolve Active Brand Logo dynamically from Backend Admin Settings
    $brandLogoUrl = BrandLogoHelper::getLogoUrl();

    // 2. Resolve Active Locale (Tier 1 Dual-Locale ID / EN)
    $activeLocale = request()->query('locale')
        ?: (request()->query('lang')
        ?: (request()->cookie('neriah_locale')
        ?: (session('locale')
        ?: app()->getLocale())));
    $isEn = ($activeLocale === 'en');

    // 3. Auto-detect context feature from Current Route Name and Request Path
    $routeName = Route::currentRouteName() ?? '';
    $path = trim(request()->path(), '/');

    if ($feature) {
        $contextKey = $feature;
    } elseif (str_starts_with($routeName, 'blueprint.') || str_starts_with($path, 'blueprint') || str_starts_with($path, 'init')) {
        $contextKey = 'project_os';
    } elseif (str_starts_with($routeName, 'cv-pro.') || str_starts_with($path, 'cv-pro') || str_starts_with($path, 'cv/')) {
        $contextKey = 'cv_pro';
    } elseif (str_starts_with($routeName, 'customer.') || str_starts_with($path, 'customer') || $path === 'portal') {
        $contextKey = 'customer_portal';
    } elseif (str_starts_with($routeName, 'cart.') || str_starts_with($path, 'cart')) {
        $contextKey = 'checkout';
    } elseif (str_starts_with($routeName, 'explore.') || str_starts_with($path, 'explore')) {
        $contextKey = 'explore';
    } elseif (str_starts_with($routeName, 'legal.') || in_array($path, ['terms', 'privacy-policy', 'refund-policy', 'smart-guide', 'panduan-klien', 'customer-guide'])) {
        $contextKey = 'legal';
    } else {
        $contextKey = 'general';
    }

    // 4. Curated Context Matrix (Bilingual Tier 1 & Brutalist Badges)
    $contexts = [
        'project_os' => [
            'badge' => 'PROJECT OS // ARCHITECTURE HUB',
            'title_id' => 'Menyiapkan Studio Project OS...',
            'title_en' => 'Preparing Project OS Architecture Studio...',
            'subtitle_id' => 'Mengompilasi 26 parameter spesifikasi, matriks RBAC, diagram arsitektur, dan cetak biru sistem...',
            'subtitle_en' => 'Compiling 26 specification parameters, RBAC matrix, architecture diagrams, and system blueprint...',
            'step_id' => 'MENYUSUN CETAK BIRU ARSITEKTUR...',
            'step_en' => 'COMPILING ARCHITECTURE BLUEPRINT...',
        ],
        'cv_pro' => [
            'badge' => 'CV PRO // AI RESUME & ATS ENGINE',
            'title_id' => 'Menyiapkan Studio CV Pro...',
            'title_en' => 'Preparing CV Pro Studio...',
            'subtitle_id' => 'Memuat simulasi mock interview suara AI, scanner MarkItDown dokumen, dan audit skor ATS...',
            'subtitle_en' => 'Loading AI voice mock interview simulation, MarkItDown document scanner, and ATS scoring audit...',
            'step_id' => 'MEMUAT CO-PILOT RESUME & AUDIT ATS...',
            'step_en' => 'LOADING RESUME CO-PILOT & ATS AUDIT...',
        ],
        'customer_portal' => [
            'badge' => 'CLIENT PORTAL // SECURE DASHBOARD',
            'title_id' => 'Menyiapkan Dashboard Klien...',
            'title_en' => 'Preparing Client Dashboard...',
            'subtitle_id' => 'Menghubungkan sesi autentikasi terenkripsi, status lisensi komersial, dan repositori proyek...',
            'subtitle_en' => 'Connecting encrypted session, commercial license status, and active project repository...',
            'step_id' => 'MENYINKRONKAN SESI ENKRIPSI KLIEN...',
            'step_en' => 'SYNCHRONIZING CLIENT ENCRYPTED SESSION...',
        ],
        'checkout' => [
            'badge' => 'COMMERCIAL // SPRINT CAPACITY',
            'title_id' => 'Menyiapkan Ringkasan Pemesanan...',
            'title_en' => 'Preparing Order Summary...',
            'subtitle_id' => 'Memvalidasi slot sprint terencana, anti-collision slot, dan rincian termin pembayaran resmi...',
            'subtitle_en' => 'Validating managed sprint slot, anti-collision protection, and official payment milestone terms...',
            'step_id' => 'MEMVALIDASI KAPASITAS SPRINT & TERMIN...',
            'step_en' => 'VALIDATING SPRINT CAPACITY & TERMS...',
        ],
        'explore' => [
            'badge' => 'EXPLORE // SOLUTIONS CATALOG',
            'title_id' => 'Menyiapkan Katalog Solusi Digital...',
            'title_en' => 'Preparing Digital Solutions Catalog...',
            'subtitle_id' => 'Memuat inventaris aplikasi siap pakai, arsitektur enterprise terverifikasi, dan demo interaktif...',
            'subtitle_en' => 'Loading production-grade application inventory, verified architecture, and interactive demos...',
            'step_id' => 'MEMUAT INVENTARIS PRODUK DIGITAL...',
            'step_en' => 'LOADING DIGITAL PRODUCT INVENTORY...',
        ],
        'legal' => [
            'badge' => 'COMPLIANCE // SERVICE GUARANTEE',
            'title_id' => 'Menyiapkan Dokumen Legalitas...',
            'title_en' => 'Preparing Legal Compliance Documents...',
            'subtitle_id' => 'Memuat kesepakatan lisensi resmi, klausul anti-slop, dan jaminan pengembang tanpa penyesalan...',
            'subtitle_en' => 'Loading official licensing agreements, anti-slop clauses, and developer no-regret guarantees...',
            'step_id' => 'MEMVERIFIKASI JAMINAN KONTRAK HUKUM...',
            'step_en' => 'VERIFYING LEGAL CONTRACT GUARANTEE...',
        ],
        'general' => [
            'badge' => 'NERIAH PRO // LIFE SOLUTION APPS',
            'title_id' => 'Menyiapkan Layanan Digital Neriah Pro...',
            'title_en' => 'Loading Neriah Pro Digital Services...',
            'subtitle_id' => 'Memuat arsitektur sistem enterprise, modul solusi hidup, dan integrasi kecerdasan AI...',
            'subtitle_en' => 'Loading enterprise system architecture, life solution modules, and AI intelligence...',
            'step_id' => 'MENYIAPKAN KOMPONEN APLIKASI...',
            'step_en' => 'PREPARING APPLICATION COMPONENTS...',
        ],
    ];

    $ctx = $contexts[$contextKey] ?? $contexts['general'];

    $displayBadge = $badge ?? $ctx['badge'];
    $displayTitleId = $titleId ?? $ctx['title_id'];
    $displayTitleEn = $titleEn ?? $ctx['title_en'];
    $displaySubtitleId = $subtitleId ?? $ctx['subtitle_id'];
    $displaySubtitleEn = $subtitleEn ?? $ctx['subtitle_en'];
    $displayStepId = $ctx['step_id'];
    $displayStepEn = $ctx['step_en'];
@endphp

<!-- ========================================================================= -->
<!-- NERIAH PRO ENTERPRISE FULL-SCREEN LOADER (ANTI-POP-IN CURTAIN)             -->
<!-- 100% Dark & Light Mode | Dynamic Backend Logo | Tier 1 Bilingual Support -->
<!-- ========================================================================= -->
<div 
    id="neriah-fullscreen-loader" 
    class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 select-none transition-opacity duration-300 pointer-events-auto"
    style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: var(--neriah-loader-bg, #ffffff);"
    aria-live="polite"
    aria-busy="true"
    role="status"
>
    <!-- Background Backdrop Grid Overlay (Subtle Brutalist Tech Aesthetic) -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]" style="background-image: linear-gradient(to right, currentColor 1px, transparent 1px), linear-gradient(to bottom, currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>

    <!-- Centered Loader Card -->
    <div class="relative z-10 max-w-md w-[calc(100%-2rem)] sm:w-full mx-4 p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl rounded-xs flex flex-col items-center text-center">
        
        <!-- Precision Top Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-zinc-950 dark:bg-emerald-500 rounded-t-xs"></div>

        <!-- Dynamic Brand Logo Section -->
        <div class="w-full flex flex-col items-center justify-center mb-4">
            <div class="flex items-center justify-center gap-3">
                <!-- Image Logo from Backend Settings -->
                <img 
                    id="neriah-loader-logo-img"
                    src="{{ $brandLogoUrl }}" 
                    alt="Neriah Pro Official Brand Logo" 
                    class="h-10 sm:h-12 w-auto max-w-[190px] object-contain transition-transform duration-300"
                    onerror="this.style.display='none'; document.getElementById('neriah-loader-monogram').style.display='flex';"
                />
                
                <!-- Fallback Monogram Emblem (Clean Brutalist NP) -->
                <div 
                    id="neriah-loader-monogram" 
                    class="hidden w-10 h-10 bg-zinc-950 dark:bg-emerald-500 text-white dark:text-zinc-950 items-center justify-center font-mono font-black text-sm rounded-none border border-zinc-800 dark:border-emerald-400"
                >
                    NP
                </div>
            </div>

            <!-- Dynamic Feature Badge -->
            <div class="mt-3.5 inline-flex items-center px-2.5 py-0.5 text-[10px] font-mono font-bold tracking-wider uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-950 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-none shadow-2xs">
                <span class="inline-block w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-none mr-1.5 animate-pulse"></span>
                <span id="neriah-loader-badge">{{ $displayBadge }}</span>
            </div>
        </div>

        <!-- Dynamic Title (Tier 1 Dual-Locale Aware) -->
        <h2 
            id="neriah-loader-title" 
            class="text-base sm:text-lg font-black tracking-tight text-zinc-950 dark:text-zinc-50 uppercase font-sans mt-1 leading-snug"
            data-title-id="{{ $displayTitleId }}"
            data-title-en="{{ $displayTitleEn }}"
        >
            {{ $isEn ? $displayTitleEn : $displayTitleId }}
        </h2>

        <!-- Dynamic Subtitle (Tier 1 Dual-Locale Aware) -->
        <p 
            id="neriah-loader-subtitle" 
            class="text-xs font-mono text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed max-w-sm"
            data-subtitle-id="{{ $displaySubtitleId }}"
            data-subtitle-en="{{ $displaySubtitleEn }}"
        >
            {{ $isEn ? $displaySubtitleEn : $displaySubtitleId }}
        </p>

        <!-- Precision Loading Progress Track -->
        <div class="w-full mt-6">
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 border border-zinc-200 dark:border-zinc-700 rounded-none overflow-hidden relative">
                <div 
                    id="neriah-loader-progress-bar" 
                    class="h-full bg-zinc-950 dark:bg-emerald-500 transition-all duration-300 ease-out"
                    style="width: 20%;"
                ></div>
            </div>

            <!-- Micro Live Ticker -->
            <div class="flex items-center justify-between mt-2 font-mono text-[10px] text-zinc-500 dark:text-zinc-400">
                <span 
                    id="neriah-loader-step" 
                    class="font-bold tracking-wider truncate max-w-[280px] text-left uppercase"
                    data-step-id="{{ $displayStepId }}"
                    data-step-en="{{ $displayStepEn }}"
                >
                    {{ $isEn ? $displayStepEn : $displayStepId }}
                </span>
                <span id="neriah-loader-pct" class="font-bold tabular-nums ml-2">25%</span>
            </div>
        </div>

        <!-- Security & Architecture Shield Marker -->
        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 w-full flex items-center justify-center gap-1.5 text-[10px] font-mono text-zinc-400 dark:text-zinc-500">
            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
            <span class="tracking-widest uppercase">Verified System Architecture // Neriah Pro</span>
        </div>

    </div>
</div>

<script>
(function() {
    // 1. Dark Mode Auto-Detection before DOM Paint to Eliminate Bright White Flash
    const isDark = (
        localStorage.getItem('neriah_theme') === 'dark' || 
        (!localStorage.getItem('neriah_theme') && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    );
    const loader = document.getElementById('neriah-fullscreen-loader');
    if (loader) {
        if (isDark) {
            loader.style.backgroundColor = '#09090b';
            document.documentElement.classList.add('dark');
        } else {
            loader.style.backgroundColor = '#ffffff';
        }
    }

    // 2. Client-Side Dual-Locale Synchronization
    try {
        const match = document.cookie.match(new RegExp('(^| )neriah_locale=([^;]+)'));
        const clientLang = match ? match[2] : (localStorage.getItem('neriah_blueprint_lang') || '{{ $activeLocale }}');
        const isClientEn = (clientLang === 'en');

        const titleEl = document.getElementById('neriah-loader-title');
        const subtitleEl = document.getElementById('neriah-loader-subtitle');
        const stepEl = document.getElementById('neriah-loader-step');

        if (titleEl && isClientEn) {
            titleEl.textContent = titleEl.getAttribute('data-title-en');
        }
        if (subtitleEl && isClientEn) {
            subtitleEl.textContent = subtitleEl.getAttribute('data-subtitle-en');
        }
        if (stepEl && isClientEn) {
            stepEl.textContent = String(stepEl.getAttribute('data-step-en') || '').replace(/&amp;/gi, '&');
        }
    } catch(e) {}

    // 3. Precision Multi-Phase Progress Stepper
    let currentPct = 25;
    const bar = document.getElementById('neriah-loader-progress-bar');
    const pctEl = document.getElementById('neriah-loader-pct');
    const stepEl = document.getElementById('neriah-loader-step');

    const updateProgress = (targetPct, stepText) => {
        if (!bar || !pctEl) return;
        currentPct = targetPct;
        bar.style.width = targetPct + '%';
        pctEl.textContent = targetPct + '%';
        if (stepText && stepEl) {
            // Prevent any accidental HTML entity escaping from leaking into textContent
            stepEl.textContent = String(stepText).replace(/&amp;/gi, '&');
        }
    };

    const textStage1 = @json($isEn ? 'HYDRATING INTERACTIVE MODULES...' : 'MEMUAT MODUL INTERAKTIF...');
    const textStage2 = @json($isEn ? 'VERIFYING INTEGRITY & SECURITY...' : 'MEMVERIFIKASI INTEGRITAS KOMPONEN...');
    const textStage3 = @json($isEn ? 'READY // LAUNCHING VIEW...' : 'SIAP // MENAMPILKAN HALAMAN...');

    // Stage 1: Quick burst to 55%
    setTimeout(() => {
        updateProgress(55, textStage1);
    }, 120);

    // Stage 2: Advance to 85%
    setTimeout(() => {
        updateProgress(85, textStage2);
    }, 320);

    // 4. Smooth Dismissal Controller (Ensures Zero Pop-in)
    let isDismissed = false;
    function dismissLoader() {
        if (isDismissed) return;
        isDismissed = true;

        updateProgress(100, textStage3);

        setTimeout(() => {
            const targetLoader = document.getElementById('neriah-fullscreen-loader');
            if (!targetLoader) return;
            
            targetLoader.classList.remove('pointer-events-auto');
            targetLoader.classList.add('opacity-0', 'pointer-events-none');

            // Dispatch global event so other components know loader has finished
            window.dispatchEvent(new CustomEvent('neriah-loader:finished'));

            setTimeout(() => {
                if (targetLoader.parentNode) {
                    targetLoader.parentNode.removeChild(targetLoader);
                } else {
                    targetLoader.style.display = 'none';
                }
            }, 350);
        }, 180);
    }

    // Expose programmatic dismissal API
    window.dismissNeriahLoader = dismissLoader;
    window.addEventListener('neriah-loader:dismiss', dismissLoader);

    // 5. Multi-trigger detection (window.load + Alpine + React hydration)
    function onReady() {
        if (document.readyState === 'complete') {
            setTimeout(dismissLoader, 160);
        } else {
            window.addEventListener('load', () => {
                setTimeout(dismissLoader, 160);
            });
        }
    }

    // Alpine.js integration
    document.addEventListener('alpine:initialized', () => {
        setTimeout(dismissLoader, 200);
    });

    // React Islands Ready integration
    window.addEventListener('neriah-islands:ready', () => {
        setTimeout(dismissLoader, 160);
    });

    // DOM Ready Trigger
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }

    // 6. Enterprise Safety Valve: Absolute maximum timeout fallback (2.5s)
    // Guarantees the user is NEVER permanently stuck under any network or JS fault
    setTimeout(dismissLoader, 2500);
})();
</script>

