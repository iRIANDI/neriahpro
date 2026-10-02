<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $blueprint->nama_bisnis ?? $blueprint->client_name }} - Ultimate PRD & Architecture Blueprint | Neriah Pro</title>
    <meta name="description" content="Product Requirements Document (PRD) & skema arsitektur database untuk {{ $blueprint->nama_bisnis }}.">

    <!-- Schema.org JSON-LD Structured Data (Mandatory & Cached Forever) -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::projectOsApplication(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            'Beranda' => url('/'),
            'Project OS Blueprint' => url('/blueprint'),
            $blueprint->nama_bisnis ?: 'Spesifikasi Sistem' => request()->url(),
        ]),
    ]) !!}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Vite React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('neriah_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('neriah_theme', 'dark');
            }
        }
    </script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
    <!-- Alpine.js & Mermaid UMD Bundle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <!-- Midtrans Snap JS (In-Page Popup Modal) -->
    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        window.payBlueprintSnap = async function(tier, onStart, onFinish) {
            if (onStart) onStart();
            try {
                const res = await fetch('{{ route('blueprint.snap-token', $blueprint->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tier: tier })
                });
                const data = await res.json();
                if (!data.success || !data.token) {
                    throw new Error(data.message || 'Gagal memproses sesi Snap Midtrans.');
                }
                if (window.snap && window.snap.pay) {
                    window.snap.pay(data.token, {
                        onSuccess: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'PEMBAYARAN DP BERHASIL',
                                    message: 'Pembayaran DP berhasil dikonfirmasi oleh Midtrans! Memperbarui proposal...',
                                    duration: 3500
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 1800);
                        },
                        onPending: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'warning',
                                    title: 'MENUNGGU PEMBAYARAN',
                                    message: 'Instruksi pembayaran telah dibuat. Silakan transfer sesuai rincian pada prompt.',
                                    duration: 5000
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 2200);
                        },
                        onError: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'error',
                                    title: 'PEMBAYARAN DIBATALKAN',
                                    message: 'Sesi transaksi dibatalkan atau ditolak oleh payment provider.'
                                });
                            }
                            if (onFinish) onFinish();
                        },
                        onClose: function() {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'info',
                                    title: 'PROMPT DITUTUP',
                                    message: 'Prompt pembayaran ditutup. Anda dapat menekan tombol bayar kembali untuk melanjutkan.'
                                });
                            }
                            if (onFinish) onFinish();
                        }
                    });
                } else if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    if (window.showToast) {
                        window.showToast({
                            type: 'error',
                            title: 'KONEKSI SNAP MIDTRANS',
                            message: 'Snap script belum selesai dimuat. Silakan periksa koneksi internet Anda.'
                        });
                    }
                    if (onFinish) onFinish();
                }
            } catch (err) {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'KENDALA PEMBAYARAN',
                        message: err.message || 'Terjadi kesalahan sistem saat menghubungi gateway Midtrans.'
                    });
                }
                if (onFinish) onFinish();
            }
        };
        // Initialize Mermaid with startOnLoad: false to prevent 0-width rendering in hidden tabs
        if (window.mermaid) {
            try {
                mermaid.initialize({
                    startOnLoad: false,
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'neutral',
                    securityLevel: 'loose',
                    fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace'
                });
            } catch(e) {
                console.warn('Mermaid pre-init notice:', e);
            }
        }

        window.renderMermaidDiagram = async function(containerId, sourceId) {
            const container = document.getElementById(containerId);
            const source = document.getElementById(sourceId);
            if (!container || !source || !window.mermaid) return;

            // If already rendered with an SVG, no need to re-render
            if (container.querySelector('svg')) return;

            try {
                const isDark = document.documentElement.classList.contains('dark');
                mermaid.initialize({
                    startOnLoad: false,
                    theme: isDark ? 'dark' : 'neutral',
                    securityLevel: 'loose',
                    fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace'
                });

                const id = 'render-' + containerId + '-' + Math.floor(Math.random() * 100000);
                const code = source.textContent.trim();
                const { svg } = await mermaid.render(id, code);
                container.innerHTML = svg;
            } catch (err) {
                console.warn('Mermaid rendering notice for ' + containerId + ':', err);
                container.innerHTML = `
                    <div class="p-4 bg-zinc-900 border border-zinc-700 text-xs font-mono space-y-2 text-left w-full">
                        <div class="flex items-center justify-between text-zinc-400 border-b border-zinc-800 pb-2">
                            <span class="font-bold uppercase text-emerald-400">Kode Sumber Diagram Mermaid:</span>
                            <span class="text-[10px] text-zinc-500">Render fallback aktif</span>
                        </div>
                        <pre class="bg-black/60 p-3 text-[11px] text-zinc-300 overflow-x-auto select-all leading-relaxed">${source.textContent.trim()}</pre>
                    </div>
                `;
            }
        };

        window.copyMermaidCode = function(sourceId, btnEl) {
            const source = document.getElementById(sourceId);
            if (!source) return;
            navigator.clipboard.writeText(source.textContent.trim()).then(() => {
                const orig = btnEl.innerHTML;
                btnEl.innerHTML = '<span class="text-emerald-400 font-bold">✓ KODE DISALIN!</span>';
                setTimeout(() => { btnEl.innerHTML = orig; }, 2000);
            });
        };

        window.copyMasterOrchestrationPrompt = function(btnEl) {
            const promptText = `### MASTER AGENTIC ORCHESTRATION PROMPT // SYSTEM ENGINE
Project: {{ addslashes($blueprint->nama_bisnis ?: $blueprint->client_name) }}
Target Timeline: {{ $targetDays ?? 30 }} Hari Kerja
Client PIC: {{ addslashes($blueprint->client_name) }} ({{ $blueprint->email }})

#### ARCHITECTURAL GUARDRAILS & CORE DIRECTIVES:
1. Framework & Engine: Laravel 13, Filament v5, Livewire 4, PostgreSQL 16+.
2. Primary Keys: Strict ULID (->ulid('id')->primary(), VARCHAR 26). NEVER use AUTO_INCREMENT, ->id(), or ->uuid().
3. Keyset Cursor Pagination: ALWAYS use cursorPaginate() with explicit ->orderBy('id', 'asc'). NEVER use offset paginate().
4. Anti-AI-Slop & Precision UI:
   - Zero Native Dialogs: ZERO window.alert() or confirm(). Always use window.showToast and backdrop-blur modals.
   - Subtle Round Corners: Use thin borders (rounded-xs, rounded-sm, max rounded-md). STRICTLY AVOID capsule/pill shapes (rounded-full).
   - Thousand Separators: Format any number >= 1,000 with thousand separators (titik untuk format ID, koma untuk EN).
   - Local FontAwesome Icons: Use local SVG helper \\App\\Support\\FontAwesome::svg('name'), avoiding external CDN fonts.
5. Multi-Language (2-Tier):
   - Backend: Filament v5 dual-locale (ID & EN) with database columns cast as array JSON (title->id, title->en).
   - Frontend: Tier 1 native ID/EN language toggle + Tier 2 Google Translate plugin configured via admin.
6. Decoupled Layout & Global Settings:
   - Page plugins are modular and configurable.
   - Global Navigation, Footer, and Global Alert are isolated outside page plugins.
   - Centralized Global Settings with strict privacy protection.
   - Phone Inputs: Always use config('country_zones') selector with E.164 standard.
7. Bulletproof Scalability:
   - Cache forever (Cache::rememberForever) on Redis/memory for CMS pages, global settings, and Schema.org.
   - Event-driven cache reset on Eloquent model saved/deleted hooks.
8. SEO Tab Title Standard: [NAMA DOMAIN - NAMA PAGE] + Schema.org JSON-LD.
9. Anti-AI Malware Suite: Honeypot form traps, adaptive rate limiting, strict CSP.
10. Admin AI Agentic Engine: Structured knowledge base, RAG, and native tool calling.

#### VERTICAL SLICE SPRINT EXECUTION SEQUENCE:
Step 1: Database Migration (ULID primary keys + JSON multi-language + proper indexes).
Step 2: Eloquent Model (HasUlids + casts array + relationships).
Step 3: Business Actions & Service classes (isolated logic).
Step 4: Filament v5 Resource (Schema form & Table) + Frontend Island.
Step 5: Automated Verification Gate: Execute "php artisan test --filter=[Model]Test" and "npm run build". Ensure exit code 0!`;

            navigator.clipboard.writeText(promptText).then(() => {
                const orig = btnEl.innerHTML;
                btnEl.innerHTML = '<span class="text-black font-bold">✓ PROMPT MASTER DISALIN!</span>';
                if (window.showToast) {
                    window.showToast({
                        type: 'success',
                        title: 'MASTER PROMPT DISALIN',
                        message: 'Master directive AI telah disalin ke clipboard! Buka IDE dan tempel ke Cursor Composer / Claude Code.',
                        duration: 3500
                    });
                }
                setTimeout(() => { btnEl.innerHTML = orig; }, 2500);
            });
        };

        window.copyFullPrdMarkdown = function(btnEl) {
            window.location.href = '{{ route('blueprint.download-md', $blueprint->slug) }}';
        };
    </script>
@php
    $pricingTiers = $prd['velocity_pricing_options'] ?? \App\Services\PrdGeneratorService::generateVelocityPricingOptions(
        $blueprint->target_waktu ?? '30 Hari Kerja',
        $blueprint->user_metadata['kisaran_budget'] ?? null,
        $blueprint->nama_bisnis ?? $blueprint->client_name,
        $blueprint->masalah_utama ?? ''
    );
    $alpineTiers = [];
    foreach ($pricingTiers as $t) {
        $alpineTiers[$t['id']] = [
            'contract' => (float)$t['contract_amount'],
            'dp' => (float)$t['dp_amount'],
            'days' => $t['duration'],
            'name' => $t['name'],
        ];
    }
    $tierKeys = array_keys($alpineTiers);
    $defaultSelectedTier = count($tierKeys) >= 2 ? $tierKeys[1] : ($tierKeys[0] ?? '');
@endphp
</head>
<body x-data="{ 
    userMenuOpen: false, 
    paymentModalOpen: false, 
    flowTab: 'visual', 
    erdTab: 'visual', 
    erdLang: 'id',
    selectedTier: '{{ $defaultSelectedTier }}',
    tierAmounts: {{ json_encode($alpineTiers) }},
    isPayingSnap: false,
    devPlaybookOpen: true,
    activeDevPhase: 1,
    copyMasterPromptSuccess: false
}" 
x-init="
    $watch('flowTab', val => {
        if (val === 'mermaid') $nextTick(() => window.renderMermaidDiagram('mermaid-flow-target', 'mermaid-flow-source'));
    });
    $watch('erdTab', val => {
        if (val === 'mermaid') $nextTick(() => window.renderMermaidDiagram('mermaid-erd-target', 'mermaid-erd-source'));
    });
" class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Header Navigation Bar (Sharp Precision Theme) -->
    <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 py-3 px-6 sticky top-0 z-50 no-print transition-colors">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="text-sm font-black uppercase tracking-tight flex items-center gap-2 text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">N</span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // PRD SPEC</span>
            </a>

            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono rounded-none border border-zinc-300 dark:border-zinc-700 transition">
                    THEME
                </button>
                <button onclick="window.print()" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span class="hidden sm:inline">Cetak</span>
                </button>
                <a href="{{ route('blueprint.download-pdf', $blueprint->slug) }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <span>PDF</span>
                </a>
                <a href="{{ route('blueprint.download-md', $blueprint->slug) }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <span>MD</span>
                </a>
                <button type="button" onclick="copyFullPrdMarkdown(this)" class="px-3 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono uppercase font-bold rounded-none border border-emerald-500/30 flex items-center gap-1.5 transition" title="Salin Dokumen PRD Ultimate Lengkap untuk AI Code Agent">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span>PROMPT AGENT</span>
                </button>

                <a href="{{ route('blueprint.show', $blueprint->slug) }}?regenerate=1" class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-mono uppercase font-bold border border-amber-500/30 transition flex items-center gap-1" title="Sintesis Ulang PRD">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span class="hidden md:inline">REGENERATE</span>
                </a>
                
                <div class="h-6 w-px bg-zinc-300 dark:bg-zinc-700 mx-1 hidden sm:block"></div>

                @php
                    $rawCart = session('neriah_cart', []);
                    $cartCount = count($rawCart);
                    $minRemaining = null;
                    $now = \Illuminate\Support\Carbon::now();
                    foreach ($rawCart as $c) {
                        try {
                            $exp = isset($c['expires_at'])
                                ? \Illuminate\Support\Carbon::parse($c['expires_at'])
                                : (isset($c['added_at']) ? \Illuminate\Support\Carbon::parse($c['added_at'])->addHours(24) : $now->copy()->addHours(24));
                            $rem = max(0, (int) $now->diffInSeconds($exp, false));
                            if ($rem > 0 && ($minRemaining === null || $rem < $minRemaining)) {
                                $minRemaining = $rem;
                            }
                        } catch (\Throwable $e) {
                            // ignore malformed entry
                        }
                    }
                @endphp

                <!-- Cart Navigation Button with Anti-Ghost Hold Timer -->
                <a href="{{ route('cart.index') }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>CART</span>
                    @if($cartCount > 0)
                        <span class="px-1.5 py-0.2 bg-emerald-500 text-black text-[10px] font-bold">{{ $cartCount }}</span>
                        @if($minRemaining)
                            <span class="text-[10px] text-amber-500 font-bold hidden sm:inline" id="nav-cart-timer" data-rem="{{ $minRemaining }}">⏱️ {{ gmdate('H:i:s', $minRemaining) }}</span>
                        @endif
                    @endif
                </a>

                @auth
                    <!-- User Dropdown Navigation Board -->
                    <div class="relative" @click.outside="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen" class="px-3 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black hover:bg-black dark:hover:bg-emerald-400 text-xs font-mono uppercase font-bold border border-zinc-900 dark:border-emerald-500 flex items-center gap-1.5 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="max-w-[110px] truncate">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            <svg class="w-3 h-3 transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Navigation Board Dropdown Menu -->
                        <div x-show="userMenuOpen" x-cloak x-transition.opacity.duration.150ms class="absolute right-0 top-full mt-1.5 w-64 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 shadow-2xl z-50 font-mono">
                            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                                <div class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold">AKTOR TERAUTENTIKASI</div>
                                <div class="text-xs font-bold text-zinc-900 dark:text-white truncate mt-0.5">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 truncate">{{ Auth::user()->email }}</div>
                                <span class="inline-block mt-2 px-2 py-0.5 text-[9px] bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 uppercase font-bold">
                                    ROLE: {{ Auth::user()->roles->first()?->name ?? (Auth::user()->role ?? 'AUTHENTICATED') }}
                                </span>
                            </div>
                            <div class="py-1 text-xs">
                                <a href="{{ url('/admin') }}" class="flex items-center gap-2.5 px-4 py-2.5 font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border-b border-zinc-100 dark:border-zinc-800 transition">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>ADMIN CONTROL PANEL</span>
                                </a>
                                <a href="{{ route('cart.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800 transition">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>CART BELANJA & ESCROW</span>
                                </a>
                            </div>
                            <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-3 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>LOG OUT</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="px-3 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        LOGIN
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if(!$blueprint->is_published)
        <!-- PRIVATE DRAFT SHIELD (SHARP BRUTALIST) -->
        <main class="flex-1 flex items-center justify-center p-6">
            <div class="max-w-xl w-full bg-white dark:bg-zinc-900 border-2 border-amber-500 rounded-none p-8 text-center shadow-none">
                <div class="w-12 h-12 bg-amber-500 text-black flex items-center justify-center mx-auto mb-4 font-mono font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <span class="inline-block px-3 py-1 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-xs font-mono font-bold uppercase tracking-widest border border-amber-300 dark:border-amber-800 mb-3 rounded-none">
                    ACCESS RESTRICTED // PRIVATE DRAFT
                </span>
                <h1 class="text-xl sm:text-2xl font-black uppercase text-zinc-900 dark:text-zinc-100 mb-3">
                    Dokumen Masih Dalam Tinjauan Arsitektur
                </h1>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs sm:text-sm leading-relaxed mb-6 font-sans">
                    Spesifikasi PRD & Blueprint untuk proyek <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong> saat ini masih dalam proses penyesuaian arsitektural internal oleh tim pengembang Neriah Pro.
                </p>
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 text-xs text-zinc-500 dark:text-zinc-400 mb-6 text-left space-y-1 font-mono rounded-none">
                    <div>PROJECT_NAME: <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $blueprint->nama_bisnis }}</span></div>
                    <div>CLIENT_PIC: <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $blueprint->client_name }}</span></div>
                    <div>ACCESS_MODE: <span class="text-rose-600 font-bold">LOCKED_PRIVATE</span></div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="/admin/login" class="px-5 py-2.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none transition">
                        Login Administrator
                    </a>
                    <a href="/" class="px-5 py-2.5 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>
    @else
        <!-- PUBLISHED FULL ULTIMATE PRD (SHARP BRUTALIST TECHNICAL THEME) -->
        <main class="flex-1 py-10 px-4 sm:px-6 max-w-5xl mx-auto w-full">
            
            <!-- Document Hero Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none relative">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black rounded-none">
                                SPEC_ID: {{ strtoupper(substr($blueprint->id, 0, 10)) }}
                            </span>
                            <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 rounded-none">
                                DISTRIBUTED ULID ARCHITECTURE
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight">
                            {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}
                        </h1>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-mono">
                            PREPARED BY NERIAH PRO TECH HUB // SYNCHRONIZED: {{ $blueprint->updated_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i') }} UTC
                        </p>
                    </div>

                    <div class="flex flex-col items-start md:items-end gap-1 font-mono text-xs">
                        <span class="text-zinc-400 uppercase tracking-widest">STATUS KONTRAK</span>
                        <span class="px-3 py-1 font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-none">
                            {{ strtoupper($blueprint->project_status) }}
                        </span>
                    </div>
                </div>

                <!-- Meta Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">PIC KLIEN</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate block">{{ $blueprint->client_name }}</span>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">EMAIL RESMI</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate block">{{ $blueprint->email }}</span>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">DURASI PROYEK</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $blueprint->target_waktu ?? '30 Hari Kerja' }}</span>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">KESIAPAN ASET</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $blueprint->kesiapan_aset ?? 'Sedang Disiapkan' }}</span>
                    </div>
                </div>
            </div>

            @php
                $targetDays = 30;
                if (preg_match('/(\d+)/', $blueprint->target_waktu ?? '', $m)) {
                    $targetDays = max(5, (int)$m[1]);
                }
                $createdDate = $blueprint->created_at ?? now()->subDays(3);
                $elapsedDays = max(1, (int)$createdDate->diffInDays(now()));
                $remainingDays = max(0, $targetDays - $elapsedDays);
                $progressPercent = min(100, max(5, (int)round(($elapsedDays / $targetDays) * 100)));
                $estimatedFinishDate = $createdDate->copy()->addDays($targetDays);
                
                // 5 Milestone Windows
                $m1End = max(2, (int)round($targetDays * 0.10));
                $m2End = max($m1End + 3, (int)round($targetDays * 0.35));
                $m3End = max($m2End + 4, (int)round($targetDays * 0.70));
                $m4End = max($m3End + 3, (int)round($targetDays * 0.88));
                $m5End = $targetDays;
            @endphp

            <!-- VISUAL SPRINT TIMELINE & PROJECT STATUS TRACKER (CLIENT TRANSPARENCY) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight">
                                Timeline Pengerjaan & Status Sprint Proyek
                            </h2>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs font-mono">
                                Sinkronisasi Real-Time Kontrak: {{ $targetDays }} Hari Kerja // Estimasi Serah Terima: {{ $estimatedFinishDate->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 font-mono text-xs">
                        <span class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 font-bold">
                            Hari Ke-{{ $elapsedDays }} Dari {{ $targetDays }}
                        </span>
                        <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold">
                            {{ $progressPercent }}% PROGRESS
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-3 mb-6 relative overflow-hidden rounded-none">
                    <div class="bg-emerald-500 h-full transition-all duration-500 ease-out" style="width: {{ $progressPercent }}%;"></div>
                </div>

                <!-- 5 Milestone Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs font-mono">
                    <!-- Stage 1 -->
                    @php $s1Done = $elapsedDays >= $m1End; @endphp
                    <div class="p-3.5 border {{ $s1Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40 text-emerald-900 dark:text-emerald-300' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400' }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">Tahap 1 (Hari 1-{{ $m1End }})</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s1Done ? 'bg-emerald-500 text-black' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300' }}">
                                {{ $s1Done ? 'SELESAI' : 'AKTIF' }}
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Discovery & Scope Lock</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">Kuesioner ide bisnis, sintesis PRD spec, dan persetujuan kontrak digital.</p>
                    </div>

                    <!-- Stage 2 -->
                    @php 
                        $s2Done = $elapsedDays > $m2End; 
                        $s2Active = !$s2Done && $elapsedDays >= $m1End;
                    @endphp
                    <div class="p-3.5 border {{ $s2Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s2Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">Tahap 2 (Hari {{ $m1End + 1 }}-{{ $m2End }})</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s2Done ? 'bg-emerald-500 text-black' : ($s2Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                {{ $s2Done ? 'SELESAI' : ($s2Active ? 'SEDANG JALAN' : 'TERJADWAL') }}
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Database & Core Engine</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">Migrasi PostgreSQL ULID, Eloquent Model, RBAC Shield & Action Handlers.</p>
                    </div>

                    <!-- Stage 3 -->
                    @php 
                        $s3Done = $elapsedDays > $m3End; 
                        $s3Active = !$s3Done && $elapsedDays > $m2End;
                    @endphp
                    <div class="p-3.5 border {{ $s3Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s3Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">Tahap 3 (Hari {{ $m2End + 1 }}-{{ $m3End }})</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s3Done ? 'bg-emerald-500 text-black' : ($s3Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                {{ $s3Done ? 'SELESAI' : ($s3Active ? 'SEDANG JALAN' : 'TERJADWAL') }}
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Frontend React Islands</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">Desain interaktif 60fps, 2-tier multi bahasa, format ribuan & fontawesome lokal.</p>
                    </div>

                    <!-- Stage 4 -->
                    @php 
                        $s4Done = $elapsedDays > $m4End; 
                        $s4Active = !$s4Done && $elapsedDays > $m3End;
                    @endphp
                    <div class="p-3.5 border {{ $s4Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s4Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">Tahap 4 (Hari {{ $m3End + 1 }}-{{ $m4End }})</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s4Done ? 'bg-emerald-500 text-black' : ($s4Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                {{ $s4Done ? 'SELESAI' : ($s4Active ? 'SEDANG JALAN' : 'TERJADWAL') }}
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Security & QA Gate</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">Anti-AI malware defense, honeypot traps, automated PHPUnit tests 100% lulus.</p>
                    </div>

                    <!-- Stage 5 -->
                    @php 
                        $s5Done = $elapsedDays >= $m5End; 
                        $s5Active = !$s5Done && $elapsedDays > $m4End;
                    @endphp
                    <div class="p-3.5 border {{ $s5Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s5Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">Tahap 5 (Hari {{ $m4End + 1 }}-{{ $m5End }})</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s5Done ? 'bg-emerald-500 text-black' : ($s5Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                {{ $s5Done ? 'SELESAI' : ($s5Active ? 'SEDANG JALAN' : 'TERJADWAL') }}
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Staging & Delivery</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">UAT klien di Staging, deploy production via deploy.sh, serah terima kredensial.</p>
                    </div>
                </div>
            </div>

            <!-- DEVELOPER AI EXECUTION COCKPIT & SPRINT PLAYBOOK (HIGH-RETENTION HELPER FOR YOSEPH) -->
            <div class="bg-zinc-900 text-white border-2 border-emerald-500/60 p-6 sm:p-8 mb-8 rounded-none shadow-xl print-break-inside-avoid">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800 pb-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="w-9 h-9 bg-emerald-500 text-black font-mono font-bold text-sm flex items-center justify-center shrink-0 rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold tracking-widest uppercase border border-emerald-500/40">
                                    DEV PLAYBOOK // KHUSUS DEVELOPER
                                </span>
                                <span class="text-[10px] text-zinc-400 font-mono hidden sm:inline">HIGH-RETENTION GUIDE</span>
                            </div>
                            <h2 class="text-lg sm:text-xl font-black uppercase tracking-tight text-white mt-1">
                                Panduan Eksekusi AI Coding Agent Dalam IDE (Start to Finish)
                            </h2>
                            <p class="text-zinc-400 text-xs font-mono mt-0.5">
                                Prosedur baku mengumpankan PRD ke Cursor / Claude Code / Antigravity agar tepat sasaran tanpa halusinasi.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="devPlaybookOpen = !devPlaybookOpen" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-mono uppercase font-bold border border-zinc-700 transition flex items-center gap-1.5">
                            <span x-text="devPlaybookOpen ? 'SEMBUNYIKAN DETAIL' : 'TAMPILKAN PANDUAN'"></span>
                            <svg class="w-3.5 h-3.5 transition-transform" :class="devPlaybookOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <div x-show="devPlaybookOpen" x-transition.opacity.duration.200ms class="space-y-6">
                    <!-- Phase Navigation Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 font-mono text-xs">
                        <button type="button" @click="activeDevPhase = 1" :class="activeDevPhase === 1 ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-750'" class="p-3 text-left border border-zinc-700 transition flex items-center justify-between">
                            <span>1. PRODUKSI APLIKASI</span>
                            <span class="text-[10px] opacity-75">Vertical Slice</span>
                        </button>
                        <button type="button" @click="activeDevPhase = 2" :class="activeDevPhase === 2 ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-750'" class="p-3 text-left border border-zinc-700 transition flex items-center justify-between">
                            <span>2. QUALITY TESTING GATE</span>
                            <span class="text-[10px] opacity-75">Audit & Test</span>
                        </button>
                        <button type="button" @click="activeDevPhase = 3" :class="activeDevPhase === 3 ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-750'" class="p-3 text-left border border-zinc-700 transition flex items-center justify-between">
                            <span>3. DELIVERY & HANDOFF</span>
                            <span class="text-[10px] opacity-75">Deploy & Scope Lock</span>
                        </button>
                    </div>

                    <!-- Phase 1 Content -->
                    <div x-show="activeDevPhase === 1" class="bg-black/40 border border-zinc-800 p-5 space-y-4 font-mono text-xs leading-relaxed">
                        <div class="flex items-center gap-2 text-emerald-400 font-bold uppercase tracking-wider text-xs border-b border-zinc-800 pb-2">
                            <span>Langkah Fase 1: Rekayasa Vertikal (Vertical Slice Prompting)</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-400">1.1</span> Ekstraksi Directive Fitur dari PRD
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Scroll ke <strong>Area 03 (Spesifikasi Rinci Fitur)</strong> di bawah. Klik tombol <strong>"SALIN PROMPT AGENT"</strong> pada salah satu kartu fitur. Jangan pernah memberikan seluruh dokumen PRD dalam satu prompt raksasa (cegah <em>context-rot</em>).
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-400">1.2</span> Standar Primary Key ULID & JSON
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Instruksikan AI membuat tabel dengan <code class="text-amber-300">->ulid('id')->primary()</code> (VARCHAR 26) dan trait <code class="text-amber-300">HasUlids</code>. Gunakan casting <code class="text-amber-300">'array'</code> untuk field multi-bahasa (<code class="text-zinc-300">title->id</code>, <code class="text-zinc-300">title->en</code>).
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-400">1.3</span> Country Zone & Pemisah Ribuan
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Untuk form intake nomor telepon, wajib gunakan selector <code class="text-emerald-300">config('country_zones')</code>. Untuk display angka/uang di atas 1.000, wajib ada pemisah ribuan otomatis (titik format ID / koma format EN).
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-400">1.4</span> UI/UX Anti-AI-Slop & Icon Lokal
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Sudut border wajib tipis (<code class="text-emerald-300">rounded-sm/md</code>, dilarang pill <code class="text-rose-400">rounded-full</code>). Gunakan icon SVG FontAwesome lokal via <code class="text-emerald-300">\App\Support\FontAwesome::svg('name')</code> tanpa CDN luar.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 2 Content -->
                    <div x-show="activeDevPhase === 2" class="bg-black/40 border border-zinc-800 p-5 space-y-4 font-mono text-xs leading-relaxed">
                        <div class="flex items-center gap-2 text-amber-400 font-bold uppercase tracking-wider text-xs border-b border-zinc-800 pb-2">
                            <span>Langkah Fase 2: Quality Testing Gate & Otomasi Verifikasi</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-400">2.1</span> Eksekusi Unit & Feature Tests
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Wajibkan AI menjalankan pengujian terminal lokal:
                                    <pre class="bg-black p-2 text-emerald-400 text-[10px] mt-1 select-all">php artisan test --filter=[Model]Test</pre>
                                    Seluruh assertion wajib passed 100% sebelum beralih ke tugas berikutnya.
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-400">2.2</span> Frontend Vite Compilation Gate
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Validasi kompilasi bundle frontend React Islands & Tailwind:
                                    <pre class="bg-black p-2 text-emerald-400 text-[10px] mt-1 select-all">npm run build</pre>
                                    Memastikan tidak ada syntax error TypeScript/JSX dan manifest.json tersinkronisasi.
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-400">2.3</span> Audit Anti-AI Malware & CSP
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Pastikan middleware security memeriksa bot malicious, honeypot fields di form publik aktif, dan Content-Security-Policy tidak memblokir script internal.
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-400">2.4</span> Larangan Dialog JS Native
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Grep codebase untuk memastikan <code class="text-rose-400">window.alert</code> atau <code class="text-rose-400">window.confirm</code> bernilai 0. Seluruh feedback aksi wajib menggunakan <code class="text-emerald-300">window.showToast</code>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 3 Content -->
                    <div x-show="activeDevPhase === 3" class="bg-black/40 border border-zinc-800 p-5 space-y-4 font-mono text-xs leading-relaxed">
                        <div class="flex items-center gap-2 text-cyan-400 font-bold uppercase tracking-wider text-xs border-b border-zinc-800 pb-2">
                            <span>Langkah Fase 3: Deployment, Scope Lock & Serah Terima Klien</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-400">3.1</span> Git Sync ke Repositori Resmi
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Commit perubahan bersih dan push ke origin main:
                                    <pre class="bg-black p-2 text-cyan-400 text-[10px] mt-1 select-all">git add . ; git commit -m "feat(modul): deskripsi" ; git push origin main</pre>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-400">3.2</span> Eksekusi Deployment Script Server
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Di terminal SSH server (Coolify / VPS), jalankan nomor skenario yang sesuai:
                                    <pre class="bg-black p-2 text-amber-400 text-[10px] mt-1 select-all">./deploy.sh 2   # Skenario 2: Migrasi Aman</pre>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-400">3.3</span> Kunci Scope Kontrak Digital
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Ubah status dokumen kontrak digital menjadi <code class="text-emerald-400">LOCKED_SIGNED</code> di admin panel untuk mengunci scope fitur agar terhindar dari scope creep yang tidak terbayar.
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-400">3.4</span> Update Timeline Sprint Proyek
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    Perbarui progress milestone pada dashboard klien sehingga klien dapat memantau secara transparan pencapaian fitur harian secara mandiri.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Master Orchestration Prompt Box -->
                    <div class="pt-2 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-zinc-400 text-[11px] font-mono">
                            ⚡ <strong>Master Agentic Prompt</strong>: Ingin memulai sprint dari awal? Salin seluruh ringkasan arsitektur sistem proyek ini ke AI Coding Agent Anda.
                        </div>
                        <button type="button" onclick="copyMasterOrchestrationPrompt(this)" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-bold uppercase tracking-wider text-xs font-mono transition shrink-0 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN MASTER PROMPT AI IDE</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: EXECUTIVE TECHNICAL DISCOVERY -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">01</span>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Executive Technical Discovery</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-2">Masalah Utama yang Diselesaikan</h3>
                        <p class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed font-sans">
                            {{ $blueprint->masalah_utama ?? ($prd['executive_summary']['problem_statement'] ?? '-') }}
                        </p>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">Tolak Ukur Kesuksesan (KPI)</h3>
                        <p class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed font-sans">
                            {{ $blueprint->tujuan_utama ?? ($prd['executive_summary']['success_metrics'] ?? '-') }}
                        </p>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-950 border-l-4 border-emerald-500 p-4 font-sans text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed rounded-none">
                    <strong class="font-mono uppercase text-xs text-emerald-600 dark:text-emerald-400 block mb-1">Filosofi Arsitektur & Efisiensi Biaya</strong>
                    {{ $prd['executive_summary']['architecture_philosophy'] ?? 'Sistem menggunakan arsitektur Modern Monolith (Laravel 13 & Filament PHP) untuk memangkas biaya server, menjamin isolasi data, dan mempercepat peluncuran fitur hingga 3x lipat.' }}
                </div>
            </section>

            <!-- SECTION 2: PENGGUNA & HAK AKSES (RBAC) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">02</span>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Pengguna & Hak Akses (Aktor / RBAC)</h2>
                </div>
                <div class="mb-4 text-xs font-mono text-zinc-500 dark:text-zinc-400">
                    TARGET_AUDIENCE: <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $blueprint->target_audiens ?? ($prd['executive_summary']['target_audience'] ?? '-') }}</span>
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($prd['system_actors'] ?? [] as $actor)
                        @php
                            $actorName = $actor['name'] ?? $actor['title'] ?? 'Aktor Sistem';
                            $actorRole = $actor['role'] ?? $actor['desc'] ?? 'Akses dan fungsi interaksi standar dalam sistem.';
                            $actorBadge = $actor['badge'] ?? 'ROLE';
                            $permissions = $actor['permissions'] ?? [];
                        @endphp
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-block px-2.5 py-1 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-900 dark:bg-zinc-800 text-white dark:text-zinc-100 rounded-none">
                                        {{ $actorName }}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                        {{ $actorBadge }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-sans mb-4">
                                    {{ $actorRole }}
                                </p>
                            </div>
                            @if(!empty($permissions))
                                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800/80">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase block mb-1.5 font-bold">Otorisasi &amp; Hak Akses:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($permissions as $perm)
                                            <span class="text-[9px] font-mono px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                                {{ $perm }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 3: SPESIFIKASI REKAYASA FITUR (DEEP VERTICAL SLICES & AGENT DIRECTIVE) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">03</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Spesifikasi Rekayasa Fitur & Agent Task Matrix</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Dekomposisi vertikal per fitur: Frontend Anti-AI-Slop, Backend Keyset O(1) &amp; ULID, API Contracts, dan Agent Directive Prompt.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 no-print">
                        <button type="button" onclick="copyFullPrdMarkdown(this)" class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono font-bold border border-emerald-500/30 flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN SEMUA PROMPT AGENT</span>
                        </button>
                    </div>
                </div>

                <!-- High-Level MVP Scope Summary -->
                <div class="grid md:grid-cols-2 gap-4 mb-8">
                    <!-- MVP Phase 1 -->
                    <div class="border border-emerald-500/40 bg-emerald-500/5 p-4 rounded-none">
                        <div class="flex items-center justify-between gap-2 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-bold uppercase tracking-wider mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500 inline-block"></span>
                                <h3>Fitur Wajib (Fase 1 - MVP Peluncuran)</h3>
                            </div>
                            <span class="px-1.5 py-0.5 bg-emerald-500 text-black text-[10px] font-bold">{{ count($prd['features']['mvp_phase1'] ?? []) }} FITUR</span>
                        </div>
                        <ul class="space-y-2">
                            @foreach($prd['features']['mvp_phase1'] ?? [] as $fitur)
                                <li class="text-xs bg-white dark:bg-zinc-900 p-2.5 border border-emerald-500/20 rounded-none">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $fitur['title'] ?? '-' }}</div>
                                    <div class="text-zinc-500 dark:text-zinc-400 mt-0.5 font-sans text-[11px]">{{ $fitur['desc'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Phase 2 Roadmap -->
                    <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-none">
                        <div class="flex items-center justify-between gap-2 text-zinc-600 dark:text-zinc-400 font-mono text-xs font-bold uppercase tracking-wider mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-zinc-400 inline-block"></span>
                                <h3>Fitur Tambahan (Fase 2 - Roadmap)</h3>
                            </div>
                            <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[10px] font-bold">{{ count($prd['features']['phase2_roadmap'] ?? []) }} FITUR</span>
                        </div>
                        @if(empty($prd['features']['phase2_roadmap']))
                            <p class="text-xs text-zinc-400 italic font-mono p-3">Belum ada fitur susulan. Fokus 100% pada rilis Fase 1 MVP.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($prd['features']['phase2_roadmap'] as $fitur)
                                    <li class="text-xs bg-white dark:bg-zinc-900 p-2.5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $fitur['title'] ?? '-' }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400 mt-0.5 font-sans text-[11px]">{{ $fitur['desc'] ?? '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Deep Vertical Slice Engineering Hub -->
                @php
                    $mvpEngineeringSpecs = $prd['features']['mvp_phase1'] ?? ($prd['engineering_specs']['mvp_specs'] ?? []);
                @endphp
                @if(!empty($mvpEngineeringSpecs))
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>DEKOMPOSISI VERTIKAL PER FITUR (SIAP OVER KE AI CODE AGENT)</span>
                        </div>

                        @foreach($mvpEngineeringSpecs as $specIdx => $spec)
                            <div 
                                x-data="{ specTab: &apos;gherkin&apos;, expanded: true }" 
                                class="bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 rounded-none transition"
                            >
                                <!-- Feature Spec Header Bar -->
                                <div class="p-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start sm:items-center gap-3">
                                        <span class="px-2 py-0.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs uppercase tracking-wider">
                                            {{ $spec['feature_id'] ?? ($spec['id'] ?? 'FEAT-SPEC') }}
                                        </span>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight">{{ $spec['title'] ?? 'Spesifikasi Fitur' }}</h3>
                                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                    {{ $spec['category'] ?? 'CORE DOMAIN' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 no-print self-end sm:self-auto">
                                        <button 
                                            type="button" 
                                            onclick="copyFeaturePrompt(this, 'prompt-code-{{ $specIdx }}')" 
                                            class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono font-bold border border-emerald-500/30 flex items-center gap-1 transition"
                                            title="Salin Prompt Directive Khusus Fitur Ini"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            <span>SALIN PROMPT FITUR</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="expanded = !expanded" 
                                            class="p-1 text-zinc-400 hover:text-zinc-200"
                                            title="Buka / Tutup Detail"
                                        >
                                            <svg class="w-4 h-4 transition-transform" :class="expanded ? &apos;rotate-180&apos; : &apos;&apos;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Spec Tab Body -->
                                <div x-show="expanded" class="p-4 sm:p-5">
                                    <!-- User Story Pill -->
                                    <div class="p-3 bg-zinc-100 dark:bg-zinc-900 border-l-4 border-emerald-500 font-mono text-xs text-zinc-800 dark:text-zinc-200 mb-4">
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold block mb-0.5">USER STORY:</span>
                                        &quot;{{ $spec['user_story'] ?? '' }}&quot;
                                    </div>

                                    <!-- Navigation Sub-Tabs -->
                                    <div class="flex flex-wrap items-center gap-1 font-mono text-xs border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-4 no-print">
                                        <button 
                                            @click="specTab = &apos;gherkin&apos;" 
                                            :class="specTab === &apos;gherkin&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            1. GHERKIN &amp; ACCEPTANCE CRITERIA
                                        </button>
                                        <button 
                                            @click="specTab = &apos;frontend&apos;" 
                                            :class="specTab === &apos;frontend&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            2. FRONTEND (ANTI-AI-SLOP)
                                        </button>
                                        <button 
                                            @click="specTab = &apos;backend&apos;" 
                                            :class="specTab === &apos;backend&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            3. BACKEND (KEYSET &amp; ULID)
                                        </button>
                                        <button 
                                            @click="specTab = &apos;integration&apos;" 
                                            :class="specTab === &apos;integration&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            4. API &amp; KONTRAK
                                        </button>
                                        <button 
                                            @click="specTab = &apos;prompt&apos;" 
                                            :class="specTab === &apos;prompt&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            5. PROMPT AGENT DIRECTIVE
                                        </button>
                                    </div>

                                    <!-- Tab 1: Gherkin -->
                                    <div x-show="specTab === &apos;gherkin&apos;" class="space-y-3 font-mono text-xs">
                                        @foreach($spec['acceptance_criteria'] ?? [] as $ac)
                                            <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                                <div class="font-bold text-zinc-900 dark:text-white mb-2 flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 inline-block"></span>
                                                    <span>{{ $ac['rule'] ?? 'Skenario Validasi' }}</span>
                                                </div>
                                                <div class="pl-3 space-y-1 text-zinc-600 dark:text-zinc-400 text-[11px] leading-relaxed">
                                                    <div><strong class="text-emerald-500">GIVEN:</strong> {{ $ac['given'] ?? '-' }}</div>
                                                    <div><strong class="text-sky-500">WHEN:</strong> {{ $ac['when'] ?? '-' }}</div>
                                                    <div><strong class="text-amber-500">THEN:</strong> {{ $ac['then'] ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Tab 2: Frontend Tasks -->
                                    @php
                                        $feData = $spec['frontend'] ?? [];
                                        $feComponents = is_array($feData['components'] ?? null) ? implode(', ', $feData['components']) : ($feData['components'] ?? 'Blade / Alpine');
                                    @endphp
                                    <div x-show="specTab === &apos;frontend&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                <strong class="text-zinc-900 dark:text-white uppercase">KOMPONEN &amp; TAMPILAN ANTARMUKA</strong>
                                                <span class="text-[10px] px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 font-bold border border-zinc-200 dark:border-zinc-700">
                                                    {{ $feComponents }}
                                                </span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <span class="text-zinc-400 text-[10px] block font-bold">DAFTAR TASK FRONTEND:</span>
                                                <ul class="space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                                    @foreach((array)($feData['tasks'] ?? []) as $feTask)
                                                        <li class="flex items-start gap-1.5">
                                                            <span class="text-emerald-500 font-bold">&bull;</span>
                                                            <span>{{ $feTask }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-zinc-400 text-[10px] block font-bold mb-0.5">STATE KELENGKAPAN (UI STATES):</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $feData['states'] ?? 'Default, Loading Skeleton, Empty State, Error Toast' }}</span>
                                                </div>
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-emerald-500 text-[10px] block font-bold mb-0.5">ANTI-AI-SLOP DIRECTIVE:</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $feData['anti_ai_slop'] ?? 'Palet Zinc monokrom dengan aksen tajam. Dilarang window.alert() / prompt().' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 3: Backend Tasks -->
                                    @php
                                        $beData = $spec['backend'] ?? [];
                                        $beTargets = is_array($beData['target_files'] ?? null) ? implode(', ', $beData['target_files']) : ($beData['target_files'] ?? 'app/Models, app/Actions');
                                    @endphp
                                    <div x-show="specTab === &apos;backend&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                <strong class="text-zinc-900 dark:text-white uppercase">LOGIKA BISNIS &amp; PERSISTENSI DATA</strong>
                                                <span class="text-[10px] px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-sky-600 dark:text-sky-400 font-bold border border-zinc-200 dark:border-zinc-700">
                                                    {{ $beTargets }}
                                                </span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <span class="text-zinc-400 text-[10px] block font-bold">DAFTAR TASK BACKEND:</span>
                                                <ul class="space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                                    @foreach((array)($beData['tasks'] ?? []) as $beTask)
                                                        <li class="flex items-start gap-1.5">
                                                            <span class="text-sky-500 font-bold">&bull;</span>
                                                            <span>{{ $beTask }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-amber-500 text-[10px] block font-bold mb-0.5">STANDAR DATABASE (STRICT ULID):</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $beData['ulid_migration_rules'] ?? 'Wajib gunakan ->ulid("id")->primary() (VARCHAR(26)). Dilarang AUTO_INCREMENT.' }}</span>
                                                </div>
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-emerald-500 text-[10px] block font-bold mb-0.5">QUERY STRATEGY O(1):</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $beData['query_strategy'] ?? 'Keyset Cursor Pagination cursorPaginate() dengan ->orderBy("id", "asc").' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 4: API & Integration Tasks -->
                                    @php
                                        $intData = $spec['integration'] ?? [];
                                        $intMiddleware = is_array($intData['middleware'] ?? null) ? implode(', ', $intData['middleware']) : ($intData['middleware'] ?? 'web');
                                    @endphp
                                    <div x-show="specTab === &apos;integration&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">POST</span>
                                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $intData['endpoint'] ?? '/api/v1/resource' }}</span>
                                                </div>
                                                <span class="text-[10px] text-zinc-400">MIDDLEWARE: <strong class="text-zinc-200">{{ $intMiddleware }}</strong></span>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-zinc-400 text-[10px] block font-bold">TASK INTEGRASI:</span>
                                                <ul class="space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                                    @foreach((array)($intData['tasks'] ?? []) as $intTask)
                                                        <li class="flex items-start gap-1.5">
                                                            <span class="text-emerald-500 font-bold">&check;</span>
                                                            <span>{{ $intTask }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <div class="text-[10px] text-zinc-400 font-bold mb-1">REQUEST SCHEMA:</div>
                                                    <pre class="bg-black/60 p-2 text-[10px] text-zinc-300 overflow-x-auto select-all leading-tight">{{ $intData['request_schema'] ?? '{}' }}</pre>
                                                </div>
                                                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <div class="text-[10px] text-emerald-400 font-bold mb-1">RESPONSE SUCCESS (200/201):</div>
                                                    <pre class="bg-black/60 p-2 text-[10px] text-zinc-300 overflow-x-auto select-all leading-tight">{{ $intData['response_schema'] ?? '{}' }}</pre>
                                                </div>
                                            </div>
                                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 pt-1">
                                                IDEMPOTENCY POLICY: <span class="text-zinc-700 dark:text-zinc-300 font-bold">X-Idempotency-Key Header Mandatory</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 5: AI Code Agent Directive Prompt -->
                                    <div x-show="specTab === &apos;prompt&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-4 bg-zinc-950 border border-zinc-800 space-y-2">
                                            <div class="flex items-center justify-between text-zinc-400 border-b border-zinc-800 pb-2">
                                                <span class="text-emerald-400 font-bold uppercase text-[11px]">Prompt Directive Siap Di-Paste ke Cursor Composer / Claude Code / Antigravity:</span>
                                                <button 
                                                    type="button" 
                                                    onclick="copyFeaturePrompt(this, 'prompt-code-{{ $specIdx }}')" 
                                                    class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] hover:bg-emerald-400 transition"
                                                >
                                                    SALIN PROMPT
                                                </button>
                                            </div>
                                            <pre id="prompt-code-{{ $specIdx }}" class="bg-black p-3 text-[11px] text-emerald-300/90 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap">{{ $spec['code_agent_directive'] ?? ($spec['agent_directive_prompt'] ?? '') }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- SECTION 3.5: PANDUAN REKAYASA & REKOMENDASI TOOLS MODERN (ANTI-AI-SLOP & SCALABILITY) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">&para;</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Panduan Rekayasa &amp; Rekomendasi Tools Modern</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Prinsip rekayasa anti-AI-slop, bulletproof database scalability, dan protokol handoff anti context-rot.</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        ANTI AI-SLOP CERTIFIED
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono text-xs">
                    <!-- 1. Frontend Anti-AI-Slop -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-emerald-500"></span>
                                <h3>1. FRONTEND: ANTI AI-SLOP UI/UX</h3>
                            </div>
                            <p class="font-sans text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                AI slop menghasilkan web generik bertabur warna ungu/cyan gradien murahan, ketiadaan state loading/empty, dan popup <code>alert()</code> kampungan yang merusak reputasi profesional.
                            </p>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Palet Kurasi:</strong> Base Zinc/Slate monokrom dengan aksen tajam Emerald (Success), Amber (Warning), Rose (Danger). Zero generic pastel.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Zero Native Popups:</strong> Dilarang keras <code>alert()</code> atau <code>confirm()</code>. Gunakan Floating Toast &amp; Modal Backdrop Blur.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>State Completeness:</strong> Wajib memiliki Skeleton Loader saat fetch data, Empty State dengan ilustrasi/ajakan aksi, dan inline error form validation.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Tipografi Tajam:</strong> Inter / Outfit untuk body text, JetBrains Mono untuk metrik finansial dan data teknis.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-[10px] text-zinc-500">
                            <strong>TOOLS REKOMENDASI:</strong> Tailwind CSS v4 / Vanilla CSS, Alpine.js / Livewire 4, Lucide Icons, Headless UI.
                        </div>
                    </div>

                    <!-- 2. Backend Scalability Manifesto -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sky-600 dark:text-sky-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-sky-500"></span>
                                <h3>2. BACKEND: ENTERPRISE SCALABILITY</h3>
                            </div>
                            <p class="font-sans text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Menjamin aplikasi mampu menampung jutaan baris data tanpa degradasi performa menggunakan pola algoritma kompleksitas O(1).
                            </p>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Strict ULID Primary Keys:</strong> Gunakan <code>ulid(&apos;id&apos;)</code> (VARCHAR(26)). Hindari AUTO_INCREMENT dan UUID v4 standar agar kompatibel 100% dengan PostgreSQL dan B-Tree Indexing.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Keyset Cursor Pagination O(1):</strong> Hindari <code>paginate()</code> (OFFSET). Wajib gunakan <code>cursorPaginate()</code> dengan pointer <code>orderBy(&apos;id&apos;, &apos;asc&apos;)</code>.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Atomic Transactions &amp; Action Classes:</strong> Enkapsulasi logika mutasi data dalam Single Action Class di dalam <code>DB::transaction()</code>.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Redis Queue Resiliency:</strong> Proses email, notifikasi, dan kalkulasi berat via background queue dengan fallback gracefully ke driver database.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-[10px] text-zinc-500">
                            <strong>TOOLS REKOMENDASI:</strong> Laravel 13 (PHP 8.4/8.5), Filament v5, PostgreSQL 16+, Redis + Predis, Pest PHP.
                        </div>
                    </div>

                    <!-- 3. Agent Handoff & Anti Context-Rot Protocol -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-amber-500"></span>
                                <h3>3. INTEGRASI: ANTI CONTEXT-ROT PROTOCOL</h3>
                            </div>
                            <p class="font-sans text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Mencegah fenomena <em>Context Rot</em> (AI Agent amnesia/halusinasi saat disuapi PRD raksasa sekaligus) dengan metode Vertical Slice Prompting.
                            </p>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>One-Feature-At-A-Time:</strong> Jangan berikan seluruh dokumen PRD ke prompt AI. Salin satu per satu directive fitur dari Tab 5 di atas.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Bounded File Scoping:</strong> Batasi jangkauan file target pada prompt (misal hanya 1 migration, 1 model, 1 component) untuk mencegah AI mengedit file lain tanpa izin.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Terminal Verification Loop:</strong> Wajibkan AI menjalankan verifikasi terminal otomatis (<code>php artisan test --filter=...</code>) sebelum mengakhiri task.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Git Surgical Sync:</strong> Lakukan commit per vertical slice agar rollback mudah jika terjadi regresi.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-[10px] text-zinc-500">
                            <strong>TOOLS REKOMENDASI:</strong> Cursor Composer, Claude Code, GitHub Copilot, Antigravity IDE, Aider.
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 4: ALUR KERJA (USER FLOW) -->
            @php
                $rawWorkflows = $prd['workflow'] ?? [];
                // Dynamic fallback for single-string workflows with comma-separated steps
                if (count($rawWorkflows) === 1 && strlen($rawWorkflows[0]['action'] ?? '') > 35) {
                    $singleText = $rawWorkflows[0]['action'];
                    $parts = preg_split('/,\s*(?:lalu\s+|kemudian\s+|setelah itu\s+|selanjutnya\s+)?|\s+(?:lalu|kemudian|setelah itu|selanjutnya)\s+/i', $singleText);
                    $expanded = [];
                    $sIdx = 1;
                    foreach ($parts as $p) {
                        $p = trim($p, " \t\n\r\0\x0B-•*1234567890.),");
                        if (strlen($p) > 3) {
                            $lowerP = strtolower($p);
                            $actor = 'Pengguna / Pengunjung';
                            $badge = 'DISCOVERY';
                            if (str_contains($lowerP, 'filter') || str_contains($lowerP, 'cari') || str_contains($lowerP, 'search')) {
                                $actor = 'Sistem / Search Engine';
                                $badge = 'QUERY_FILTER';
                            } elseif (str_contains($lowerP, 'notifikasi') || str_contains($lowerP, 'langganan') || str_contains($lowerP, 'berlangganan')) {
                                $actor = 'Notification Engine';
                                $badge = 'NOTIFICATION';
                            } elseif (str_contains($lowerP, 'katalog') || str_contains($lowerP, 'daftar')) {
                                $actor = 'Katalog Properti';
                                $badge = 'CATALOG';
                            }
                            $expanded[] = [
                                'step' => $sIdx++,
                                'action' => $p,
                                'description' => 'Tahapan validasi, interaksi antarmuka, dan transmisi data alur kerja.',
                                'actor' => $actor,
                                'badge' => $badge,
                            ];
                        }
                    }
                    if (count($expanded) > 1) {
                        $rawWorkflows = $expanded;
                    }
                }
            @endphp
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">04</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Alur Kerja Utama (User Flow)</h2>
                    </div>

                    <!-- Flow View Tabs -->
                    <div class="flex items-center gap-1 font-mono text-xs no-print">
                        <button 
                            @click="flowTab = 'visual'" 
                            :class="flowTab === 'visual' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span>VISUAL PIPELINE</span>
                        </button>
                        <button 
                            @click="flowTab = 'mermaid'; $nextTick(() => window.renderMermaidDiagram('mermaid-flow-target', 'mermaid-flow-source'))" 
                            :class="flowTab === 'mermaid' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span>MERMAID CHART</span>
                        </button>
                    </div>
                </div>

                <!-- 1. Interactive Visual Flowchart (Pipeline Nodes & Connectors) -->
                <div x-show="flowTab === 'visual'" class="mb-6">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative overflow-hidden">
                        <div class="absolute top-0 right-0 px-3 py-1 bg-emerald-500/10 border-b border-l border-emerald-500/30 text-[10px] font-mono text-emerald-400 uppercase tracking-widest font-bold">
                            INTERACTIVE PROCESS PIPELINE
                        </div>

                        <!-- Horizontal Scroll Pipeline Container -->
                        <div class="overflow-x-auto pb-4 pt-2">
                            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 min-w-[700px]">
                                @foreach($rawWorkflows as $index => $flow)
                                    <!-- Node Card -->
                                    <div class="flex-1 bg-zinc-900 border border-zinc-700 p-4 relative group hover:border-emerald-500 transition shadow-lg flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-2 font-mono">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span class="text-xs font-bold text-white">STEP 0{{ $flow['step'] ?? ($index + 1) }}</span>
                                                </div>
                                                <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-800 text-emerald-400 border border-emerald-500/30 font-bold">
                                                    {{ $flow['badge'] ?? 'PROCESS' }}
                                                </span>
                                            </div>

                                            <h4 class="font-mono font-bold text-xs uppercase text-zinc-100 mb-2 leading-snug">
                                                {{ $flow['action'] ?? '-' }}
                                            </h4>

                                            <p class="text-[11px] text-zinc-400 font-sans leading-relaxed mb-3">
                                                {{ $flow['description'] ?? 'Tahapan validasi dan transmisi alur kerja.' }}
                                            </p>
                                        </div>

                                        <div class="space-y-1.5 pt-2 border-t border-zinc-800 text-[10px] font-mono">
                                            <div class="flex items-center justify-between text-zinc-400">
                                                <span class="text-zinc-500">AKTOR:</span>
                                                <span class="text-emerald-400 font-bold truncate max-w-[140px]">{{ $flow['actor'] ?? 'Pengguna' }}</span>
                                            </div>
                                            @if(!empty($flow['trigger']))
                                                <div class="text-[10px] text-zinc-400">
                                                    <span class="text-zinc-500 block">TRIGGER:</span>
                                                    <span class="text-zinc-300 font-sans text-[11px]">{{ $flow['trigger'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!$loop->last)
                                        <!-- Flow Connector Arrow -->
                                        <div class="flex items-center justify-center text-emerald-500 px-1 py-1">
                                            <div class="hidden lg:flex items-center gap-0.5">
                                                <div class="w-4 h-0.5 bg-emerald-500/60"></div>
                                                <svg class="w-4 h-4 text-emerald-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                            </div>
                                            <div class="flex lg:hidden items-center justify-center my-1">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Mermaid Flowchart View (Code & Native Mermaid SVG) -->
                <div x-show="flowTab === 'mermaid'" x-cloak class="mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <span class="text-zinc-500 font-bold uppercase">ALUR KERJA MERMAID (FLOWCHART TD)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-flow-source', this)"
                                class="px-2.5 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID</span>
                            </button>
                        </div>

                        <script type="text/plain" id="mermaid-flow-source">flowchart TD
@foreach($rawWorkflows as $index => $flow)
@php
    $safeText = preg_replace('/["\r\n]+/', '', $flow['action'] ?? 'Step');
    $safeActor = preg_replace('/["\r\n]+/', '', $flow['actor'] ?? 'Pengguna');
@endphp
    S{{ $index + 1 }}["<b>Step {{ $index + 1 }}: {{ $safeText }}</b><br/><small>Aktor: {{ $safeActor }}</small>"]
    @if(!$loop->last)
    S{{ $index + 1 }} -->|Lanjut| S{{ $index + 2 }}
    @endif
@endforeach
                        </script>

                        <div id="mermaid-flow-target" class="overflow-x-auto min-h-[140px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>Memuat visualisasi alur kerja Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Steps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($rawWorkflows as $flow)
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-7 h-7 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono font-bold flex items-center justify-center rounded-none">
                                        0{{ $flow['step'] ?? $loop->iteration }}
                                    </div>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase">
                                        {{ $flow['badge'] ?? 'STEP' }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm mb-2 font-mono uppercase">{{ $flow['action'] ?? '-' }}</h4>
                                <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-sans mb-4">{{ $flow['description'] ?? '' }}</p>

                                <div class="space-y-2.5 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-[11px]">
                                    <div>
                                        <span class="font-mono text-[10px] uppercase text-zinc-400 font-bold block">Pemicu &amp; Trigger:</span>
                                        <span class="text-zinc-700 dark:text-zinc-300 font-sans">{{ $flow['trigger'] ?? 'Aksi langsung pengguna pada antarmuka.' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-mono text-[10px] uppercase text-zinc-400 font-bold block">Komputasi &amp; Basis Data:</span>
                                        <span class="text-zinc-700 dark:text-zinc-300 font-sans">{{ $flow['system_process'] ?? 'Validasi request FormRequest & query database terindeks.' }}</span>
                                    </div>
                                    @if(!empty($flow['output_state']))
                                        <div>
                                            <span class="font-mono text-[10px] uppercase text-zinc-400 font-bold block">Hasil &amp; State Response:</span>
                                            <span class="text-zinc-700 dark:text-zinc-300 font-sans">{{ $flow['output_state'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($flow['edge_case']))
                                        <div class="bg-amber-500/5 border border-amber-500/20 p-2 text-[10px]">
                                            <span class="font-mono uppercase text-amber-600 dark:text-amber-400 font-bold block">Mitigasi &amp; Edge Case:</span>
                                            <span class="text-zinc-600 dark:text-zinc-300 font-sans">{{ $flow['edge_case'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono flex items-center justify-between">
                                <span class="text-zinc-500 uppercase">AKTOR UTAMA:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $flow['actor'] ?? 'Pengguna' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 5: ENTERPRISE DATABASE ERD & SCHEMA BLUEPRINT -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">05</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Database ERD & Schema (PostgreSQL Strict)</h2>
                    </div>

                    <!-- ERD Tabs & Multi-Language Selector -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-xs no-print">
                        <!-- Multi-Language Tier 1 Toggle -->
                        <div class="flex items-center border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 p-0.5">
                            <span class="px-2 text-[10px] text-zinc-400 font-bold uppercase">LABEL TIER 1:</span>
                            <button 
                                @click="erdLang = 'id'" 
                                :class="erdLang === 'id' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-2 py-0.5 text-[10px] font-mono transition"
                            >
                                ID
                            </button>
                            <button 
                                @click="erdLang = 'en'" 
                                :class="erdLang === 'en' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-2 py-0.5 text-[10px] font-mono transition"
                            >
                                EN
                            </button>
                        </div>

                        <!-- ERD View Tabs -->
                        <button 
                            @click="erdTab = 'visual'" 
                            :class="erdTab === 'visual' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"></path></svg>
                            <span>TOPOLOGY</span>
                        </button>
                        <button 
                            @click="erdTab = 'mermaid'; $nextTick(() => window.renderMermaidDiagram('mermaid-erd-target', 'mermaid-erd-source'))" 
                            :class="erdTab === 'mermaid' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span>MERMAID ERD</span>
                        </button>
                        <button 
                            @click="erdTab = 'table'" 
                            :class="erdTab === 'table' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>DICTIONARY</span>
                        </button>
                    </div>
                </div>

                @php
                    $rawTables = $prd['erd_schema']['tables'] ?? [];
                    $sanitizedTables = [];
                    foreach ($rawTables as $t) {
                        $cleanTName = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $t['name'] ?? 'ENTITY'));
                        $cleanTName = trim($cleanTName, '_');
                        if (empty($cleanTName)) $cleanTName = 'SYSTEM_RECORD';
                        
                        $cleanCols = [];
                        foreach ($t['columns'] ?? [] as $c) {
                            $colName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $c['name'] ?? 'col'));
                            $colName = trim($colName, '_');
                            if (empty($colName)) continue;

                            $rawType = strtolower($c['type'] ?? 'string');
                            if (str_contains($rawType, 'ulid')) {
                                $colType = 'string';
                            } elseif (str_contains($rawType, 'int')) {
                                $colType = 'int';
                            } elseif (str_contains($rawType, 'bool')) {
                                $colType = 'boolean';
                            } elseif (str_contains($rawType, 'json')) {
                                $colType = 'jsonb';
                            } elseif (str_contains($rawType, 'time') || str_contains($rawType, 'date')) {
                                $colType = 'timestamp';
                            } else {
                                $colType = 'string';
                            }

                            $keyAttr = '';
                            if (($c['index'] ?? '') === 'PRIMARY' || $colName === 'id') {
                                $keyAttr = 'PK';
                            } elseif (str_contains(strtolower($c['type'] ?? ''), 'foreign') || str_ends_with($colName, '_id')) {
                                $keyAttr = 'FK';
                            } elseif (($c['index'] ?? '') === 'UNIQUE') {
                                $keyAttr = 'UK';
                            }
                            $cleanCols[] = [
                                'name' => $colName,
                                'type' => $colType,
                                'key' => $keyAttr,
                            ];
                        }
                        $sanitizedTables[] = [
                            'name' => $cleanTName,
                            'raw_name' => $t['name'],
                            'columns' => $cleanCols,
                        ];
                    }
                    $domainTableClean = $sanitizedTables[1]['name'] ?? 'PROJECT_RECORDS';
                    $domainTableRaw = $rawTables[1]['name'] ?? 'project_records';
                @endphp

                <!-- 1. Interactive Visual ERD Topology (Connected Entity Relationship Cards) -->
                <div x-show="erdTab === 'visual'" class="mb-6">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <!-- Cardinality Banner -->
                        <div class="mb-6 p-3 bg-zinc-900 border border-zinc-800 flex flex-wrap items-center justify-between gap-3 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">RELATIONAL GRAPH</span>
                                <span class="text-zinc-300 text-[11px]">Strict PostgreSQL Foreign Key Cardinalities</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-[11px] text-zinc-400">
                                <div><strong class="text-emerald-400">users (1)</strong> &bull;--&lt; <strong>{{ $domainTableRaw }} (N)</strong></div>
                                <div><strong class="text-emerald-400">users (1)</strong> &bull;--&lt; <strong>activity_logs (N)</strong></div>
                                <div><strong class="text-emerald-400">{{ $domainTableRaw }} (1)</strong> &bull;--&lt; <strong>system_notifications (N)</strong></div>
                            </div>
                        </div>

                        <!-- Entity Schema Cards (2x2 Grid) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                                <div class="bg-zinc-900 border-2 border-zinc-800 hover:border-emerald-500/60 transition shadow-xl font-mono text-xs">
                                    <!-- Entity Card Header -->
                                    <div class="p-3.5 bg-zinc-950 border-b border-zinc-800 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"></path></svg>
                                            <span class="font-black text-white uppercase text-sm tracking-wide">{{ $table['name'] }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-zinc-800 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold">
                                            PK: ULID
                                        </span>
                                    </div>

                                    <div class="px-3.5 py-2 bg-zinc-900/60 border-b border-zinc-800 text-[11px] text-zinc-400 font-sans">
                                        {{ $table['description'] }}
                                    </div>

                                    <!-- Columns Attributes List -->
                                    <div class="divide-y divide-zinc-800/80">
                                        @foreach($table['columns'] ?? [] as $col)
                                             <div class="px-3.5 py-2 flex items-center justify-between gap-2 hover:bg-zinc-800/40 transition">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.2 bg-emerald-500 text-black font-black text-[9px]">PK</span>
                                                    @elseif(str_contains(strtolower($col['type']), 'foreign') || str_contains($col['name'], '_id'))
                                                        <span class="px-1.5 py-0.2 bg-sky-500 text-white font-black text-[9px]">FK</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-1.5 py-0.2 bg-purple-500 text-white font-black text-[9px]">UK</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-1.5 py-0.2 bg-zinc-700 text-zinc-300 font-bold text-[9px]">IDX</span>
                                                    @else
                                                        <span class="w-4 inline-block text-zinc-600 text-center">&bull;</span>
                                                    @endif

                                                    <span class="font-bold text-zinc-100 truncate">{{ $col['name'] }}</span>
                                                </div>

                                                <div class="flex items-center gap-2 text-right">
                                                    <span class="text-zinc-500 text-[11px] font-sans truncate max-w-[130px]" x-text="erdLang === 'id' ? '{{ $col['label']['id'] ?? ($col['notes'] ?? '-') }}' : '{{ $col['label']['en'] ?? ($col['name'] ?? '-') }}'"></span>
                                                    <span class="text-emerald-400 text-[10px] font-bold">{{ $col['type'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 2. Mermaid ERD Diagram View (Safe Render with Dynamic Relations) -->
                <div x-show="erdTab === 'mermaid'" x-cloak class="mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <span class="text-zinc-500 font-bold uppercase">SKEMA BASIS DATA MERMAID (POSTGRESQL STRICT ERD)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-erd-source', this)"
                                class="px-2.5 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID ERD</span>
                            </button>
                        </div>

                        <script type="text/plain" id="mermaid-erd-source">erDiagram
    USERS ||--o{ {{ $domainTableClean }} : "manages"
    USERS ||--o{ ACTIVITY_LOGS : "logs"
    USERS ||--o{ SYSTEM_NOTIFICATIONS : "receives"
    {{ $domainTableClean }} ||--o{ SYSTEM_NOTIFICATIONS : "triggers"

@foreach($sanitizedTables as $st)
    {{ $st['name'] }} {
@foreach($st['columns'] as $c)
        {{ $c['type'] }} {{ $c['name'] }} {{ $c['key'] }}
@endforeach
    }
@endforeach
                        </script>

                        <div id="mermaid-erd-target" class="overflow-x-auto min-h-[260px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>Memuat skema relasi database Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PostgreSQL Strict Data Dictionary Table -->
                <div x-show="erdTab === 'table'" x-cloak class="space-y-6">
                    @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-none overflow-hidden">
                            <div class="bg-zinc-900 text-white px-4 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 font-mono text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-400 font-bold">TABLE: {{ $table['name'] }}</span>
                                    <span class="text-zinc-400 hidden sm:inline">&bull; {{ $table['description'] }}</span>
                                </div>
                                <span class="text-zinc-400">
                                    PK: {{ $table['primary_key'] ?? 'id (ULID)' }}
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 uppercase font-mono font-bold">
                                            <th class="py-2 px-3">Column Name</th>
                                            <th class="py-2 px-3">Tier 1 Label</th>
                                            <th class="py-2 px-3">Data Type</th>
                                            <th class="py-2 px-3">Index</th>
                                            <th class="py-2 px-3">Nullable</th>
                                            <th class="py-2 px-3">Description / Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono">
                                        @foreach($table['columns'] ?? [] as $col)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition">
                                                <td class="py-2 px-3 font-bold text-zinc-900 dark:text-zinc-100">{{ $col['name'] }}</td>
                                                <td class="py-2 px-3 text-zinc-700 dark:text-zinc-300 font-sans" x-text="erdLang === 'id' ? '{{ $col['label']['id'] ?? ($col['notes'] ?? '-') }}' : '{{ $col['label']['en'] ?? ($col['name'] ?? '-') }}'"></td>
                                                <td class="py-2 px-3 text-emerald-600 dark:text-emerald-400 font-bold">{{ $col['type'] }}</td>
                                                <td class="py-2 px-3">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">PRIMARY</span>
                                                    @elseif(str_contains(strtolower($col['type']), 'foreign') || str_contains($col['name'], '_id'))
                                                        <span class="px-1.5 py-0.5 bg-sky-500 text-white font-bold text-[10px]">FOREIGN</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-1.5 py-0.5 bg-purple-500 text-white font-bold text-[10px]">UNIQUE</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-1.5 py-0.5 bg-blue-500 text-white font-bold text-[10px]">INDEX</span>
                                                    @else
                                                        <span class="text-zinc-400 text-[10px]">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 text-zinc-500">{{ $col['nullable'] ? 'YES' : 'NO' }}</td>
                                                <td class="py-2 px-3 font-sans text-zinc-600 dark:text-zinc-400">{{ $col['notes'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 06: EVALUASI ARSITEKTUR & REKOMENDASI INFRASTRUKTUR (HOSTING, POLA SISTEM & AI DATABASE) -->
            @php
                $archEval = $prd['architecture_evaluation'] ?? \App\Services\PrdGeneratorService::evaluateArchitecture(
                    $blueprint->nama_bisnis ?? $blueprint->client_name,
                    $blueprint->masalah_utama ?? '',
                    $prd['features']['mvp_phase1'] ?? [],
                    $blueprint->alur_kerja ?? '',
                    $blueprint->user_metadata ?? []
                );
            @endphp
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">06</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Evaluasi Arsitektur & Infrastruktur (AI Database Ready)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        HIGH INTEGRITY ARCHITECTURE
                    </span>
                </div>

                <!-- Client Architectural Parameters Summary -->
                <div class="mb-8 p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs">
                    <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider mb-2">PARAMETER PENILAIAN DARI KUESIONER KLIEN:</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-[11px]">
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">TARGET SKALA / TRAFIK</span>
                            <strong class="text-zinc-200">{{ $archEval['scope_boundaries']['client_scale'] ?? ($blueprint->user_metadata['skala_pengguna'] ?? '0 - 100k User / Bulan') }}</strong>
                        </div>
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">JANGKAUAN PASAR</span>
                            <strong class="text-zinc-200">{{ $archEval['scope_boundaries']['client_market'] ?? ($blueprint->user_metadata['jangkauan_pasar'] ?? 'Domestik Indonesia (IDR)') }}</strong>
                        </div>
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">STANDAR KEPATUHAN</span>
                            <strong class="text-zinc-200">{{ $archEval['scope_boundaries']['client_compliance'] ?? ($blueprint->user_metadata['kepatuhan_keamanan'] ?? 'OWASP Top 10 & Enkripsi') }}</strong>
                        </div>
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">RENCANA ANGGARAN KLIEN</span>
                            <strong class="text-emerald-400">{{ $archEval['scope_boundaries']['client_budget'] ?? ($blueprint->user_metadata['kisaran_budget'] ?? 'Rp 50M - Rp 100M') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- 1. Evaluasi Lingkungan Hosting: Tangga Solusi Bertahap (Hosting Ladder) -->
                @php
                    $isLeanVerdict = str_contains($archEval['hosting_evaluation']['verdict'] ?? '', 'CLOUD STARTER');
                    $cloudStarter = $archEval['hosting_evaluation']['cloud_starter'] ?? [];
                    $dedicatedVps = $archEval['hosting_evaluation']['dedicated_vps'] ?? [];
                @endphp
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            1. Evaluasi Lingkungan Hosting: Tangga Solusi Bertahap (Hosting Ladder)
                        </h3>
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold uppercase border {{ $isLeanVerdict ? 'bg-sky-500/10 text-sky-400 border-sky-500/30' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' }}">
                            {{ $archEval['hosting_evaluation']['verdict'] ?? 'REKOMENDASI: ADAPTIF' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Cloud Starter / Shared Efisien -->
                        <div class="p-5 {{ $isLeanVerdict ? 'bg-emerald-500/5 dark:bg-emerald-950/20 border-2 border-emerald-500' : 'bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800' }} font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="font-black uppercase {{ $isLeanVerdict ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }} text-sm">
                                    {{ $cloudStarter['title'] ?? 'Cloud Starter / Shared Efisien (< Rp 100.000 / bln)' }}
                                </span>
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase {{ $isLeanVerdict ? 'bg-emerald-500 text-black' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400' }}">
                                    {{ $isLeanVerdict ? 'PILIHAN CERDAS FASE 1' : 'OPSI VALIDASI LEAN' }}
                                </span>
                            </div>
                            <p class="text-zinc-600 dark:text-zinc-400 font-sans text-xs mb-3">
                                Pilihan ekonomis untuk menjaga modal usaha tetap aman di tahap peluncuran awal:
                            </p>
                            <ul class="space-y-2 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                @foreach($cloudStarter['reasons'] ?? [] as $reason)
                                    <li class="flex items-start gap-2">
                                        <span class="{{ $isLeanVerdict ? 'text-emerald-500' : 'text-zinc-400' }} font-bold">&check;</span>
                                        <span>{{ $reason }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Dedicated VPS -->
                        <div class="p-5 {{ !$isLeanVerdict ? 'bg-emerald-500/5 dark:bg-emerald-950/20 border-2 border-emerald-500' : 'bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800' }} font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="font-black uppercase {{ !$isLeanVerdict ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }} text-sm">
                                    {{ $dedicatedVps['title'] ?? 'Dedicated VPS Container (Nixpacks & Docker)' }}
                                </span>
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase {{ !$isLeanVerdict ? 'bg-emerald-500 text-black' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400' }}">
                                    {{ !$isLeanVerdict ? 'REKOMENDASI SCALE-UP' : 'UPGRADE FASE 2' }}
                                </span>
                            </div>
                            <p class="text-zinc-600 dark:text-zinc-400 font-sans text-xs mb-3">
                                Menjamin kecepatan respons sub-detik, isolasi komputasi penuh, dan kesiapan AI:
                            </p>
                            <ul class="space-y-2 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                @foreach($dedicatedVps['reasons'] ?? [] as $reason)
                                    <li class="flex items-start gap-2">
                                        <span class="{{ !$isLeanVerdict ? 'text-emerald-500' : 'text-zinc-400' }} font-bold">&check;</span>
                                        <span>{{ $reason }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 2. Monolith vs Decoupled Assessment -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        2. Penilaian Pola Arsitektur: Modern Monolith vs Decoupled (Microservices)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Modern Monolith -->
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-emerald-500/60 font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Modern Monolith (Laravel 13 + Filament v5)</span>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-bold">REKOMENDASI FASE 1</span>
                            </div>
                            <ul class="space-y-1.5 text-zinc-600 dark:text-zinc-400 text-[11px]">
                                <li>&bull; <strong>Zero Network Latency:</strong> Komunikasi antar modul berjalan intra-process O(1) tanpa overhead HTTP network antar-microservices.</li>
                                <li>&bull; <strong>Pangkas Biaya Infrastruktur 60-70%:</strong> Satu kesatuan container deployment menghemat anggaran server staging & produksi dibanding kluster microservices.</li>
                                <li>&bull; <strong>ACID Strict Integrity:</strong> Integritas transaksi finansial tanpa rumitnya distributed 2-phase commit.</li>
                                <li>&bull; <strong>Rapid Time-to-Market:</strong> Sinkronisasi instan antara model bisnis Eloquent dan dashboard Filament.</li>
                                <li>&bull; <strong>Island Architecture Frontend:</strong> Memberikan fluiditas interaksi 60fps setara SPA dengan stabilitas dan kecepatan SEO Server-Side Rendering.</li>
                            </ul>
                        </div>

                        <!-- Decoupled -->
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold uppercase text-zinc-700 dark:text-zinc-300">Decoupled / Microservices Cluster</span>
                                <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9px] font-bold">FASE ROADMAP LANJUTAN</span>
                            </div>
                            <ul class="space-y-1.5 text-zinc-500 text-[11px]">
                                <li>&bull; Hanya dianjurkan jika tim rekayasa sudah berkembang menjadi lebih dari 15-20 developer di repositori terpisah.</li>
                                <li>&bull; Menambah biaya operasional server terpisah (Backend API server + Frontend Next.js node cluster terpisah).</li>
                                <li>&bull; Meningkatkan latensi round-trip HTTP dan beban autentikasi token JWT di setiap request interaksi.</li>
                                <li>&bull; Membutuhkan orkestrasi Kubernetes kompleks yang tidak efisien untuk peluncuran perdana (MVP).</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3. Global Scale Analysis (Matrix) -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        3. Analisis Skala Jangkauan Pengguna Dunia (Global Reach Matrix)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 font-mono text-xs">
                        @foreach($archEval['global_scale_analysis']['tiers'] ?? [] as $scaleTier)
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold block w-fit mb-2">
                                    {{ $scaleTier['status'] }}
                                </span>
                                <h4 class="font-black text-sm text-zinc-900 dark:text-zinc-100 mb-1">{{ $scaleTier['scale'] }}</h4>
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold mb-2">{{ $scaleTier['architecture'] }}</div>
                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-normal">{{ $scaleTier['verdict'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Budget & TCO Efficiency Analysis -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        4. Analisis Anggaran Klien & Efisiensi Modal (TCO Comparison)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-mono text-xs">
                        <div class="p-5 bg-emerald-500/5 dark:bg-emerald-950/20 border-2 border-emerald-500">
                            <span class="text-[9px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block mb-1">EFISIENSI MODAL TINGGI (90%)</span>
                            <h4 class="font-black text-base text-zinc-900 dark:text-zinc-100 mb-2">Modern Monolith Standard</h4>
                            <div class="space-y-1.5 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <div>&bull; Biaya Server Bulanan: <strong>Rp 350.000 - Rp 1.500.000 / bln</strong></div>
                                <div>&bull; DevOps Headcount: <strong>0 FTE (Automated Nixpacks CI/CD)</strong></div>
                                <div class="pt-2 border-t border-emerald-500/30 text-emerald-700 dark:text-emerald-300">
                                    90% anggaran klien dialokasikan murni untuk fitur bisnis & akuisisi pengguna, bukan untuk beban overhead infrastruktur.
                                </div>
                            </div>
                        </div>

                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                            <span class="text-[9px] uppercase font-bold text-zinc-400 block mb-1">OVERHEAD MODAL TINGGI (CAPITAL HEAVY)</span>
                            <h4 class="font-black text-base text-zinc-700 dark:text-zinc-300 mb-2">Decoupled Microservices</h4>
                            <div class="space-y-1.5 text-[11px] text-zinc-500">
                                <div>&bull; Biaya Kluster Cloud: <strong>Rp 8.000.000 - Rp 25.000.000+ / bln</strong></div>
                                <div>&bull; DevOps Headcount: <strong>1-2 Dedicated DevOps Engineers (Rp 20-40 jt/bln)</strong></div>
                                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                    60% anggaran tersedot hanya untuk memelihara kluster Kubernetes, API Gateway, Service Mesh, dan distributed tracing.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. The 4 Decoupling Triggers (Warning / Criteria checklist) -->
                <div class="mb-8 p-5 bg-zinc-950 border-2 border-amber-500/70 text-zinc-200 font-mono text-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 bg-amber-500"></span>
                        <h4 class="font-black text-sm uppercase text-amber-400">4 Faktor Penentu Mutlak Kapan Sistem Wajib Decoupled</h4>
                    </div>
                    <p class="text-zinc-400 text-xs mb-4 font-sans">
                        Sistem tidak boleh dipecah menjadi microservices kecuali satu atau lebih pemicu mutlak berikut terpenuhi:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                        @foreach($archEval['decoupling_threshold_triggers']['triggers'] ?? [] as $trigger)
                            <div class="p-3 bg-zinc-900 border border-zinc-800">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-1.5 py-0.2 bg-amber-500 text-black font-black text-[9px]">{{ $trigger['number'] }}</span>
                                    <span class="font-bold text-white text-xs">{{ $trigger['title'] }}</span>
                                </div>
                                <p class="text-zinc-400 text-[10px] leading-relaxed">{{ $trigger['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 6. Strict Scope Lock Boundaries (In-Scope vs Strict Out-of-Scope) -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        6. Matriks Batasan Ruang Lingkup (Strict Scope Lock & Anti-Feature Creep)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-mono text-xs">
                        <div class="p-5 bg-emerald-500/5 dark:bg-emerald-950/20 border border-emerald-500/40">
                            <span class="text-[9px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block mb-2">&check; IN-SCOPE (FASE 1 - MVP DIJAMIN KONTRAK)</span>
                            <ul class="space-y-1.5 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                @foreach($archEval['scope_boundaries']['in_scope'] ?? [] as $inScope)
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-500 font-bold">&bull;</span>
                                        <span>{{ $inScope }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="p-5 bg-rose-500/5 dark:bg-rose-950/20 border border-rose-500/40">
                            <span class="text-[9px] uppercase font-bold text-rose-600 dark:text-rose-400 block mb-2">&times; STRICT OUT-OF-SCOPE (DIKUNCI DARI KONTRAK UTAMA)</span>
                            <ul class="space-y-1.5 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                @foreach($archEval['scope_boundaries']['out_of_scope'] ?? [] as $outScope)
                                    <li class="flex items-start gap-2">
                                        <span class="text-rose-500 font-bold">&times;</span>
                                        <span>{{ $outScope }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 7. AI-Ready PostgreSQL Database Blueprint -->
                <div class="mb-8 p-5 bg-zinc-950 border border-zinc-800 text-zinc-200 font-mono text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500"></span>
                            <span class="font-bold uppercase text-sm text-white">7. Basis Data PostgreSQL 16+ (pgvector & Strict ULID)</span>
                        </div>
                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-bold">
                            AI-READY DATABASE ENGINE
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-[11px] leading-relaxed">
                        <div class="space-y-2">
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Ekstensi pgvector Co-Location:</strong>
                                Vector embeddings (1536-dim / 3072-dim) disimpan berdampingan langsung dengan data transaksi dan pengguna tanpa memerlukan SaaS database vektor terpisah seperti Pinecone atau Milvus.
                            </div>
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Indeks HNSW (Hierarchical Navigable Small World):</strong>
                                Pencarian kedekatan semantik vektor dengan kompleksitas sub-millisecond O(log N) untuk RAG (Retrieval-Augmented Generation) berkecepatan tinggi.
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Strict ULID Primary Key Standard:</strong>
                                Format string 26-karakter bebas sequence lock yang menjamin pembagian partisi terdistribusi dan keystone cursor pagination O(1) tanpa degradasi performa.
                            </div>
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Dynamic JSONB Indexing:</strong>
                                Mendukung penyimpanan context window percakapan agen AI Gemini Ultra serta fleksibilitas metadata dokumen.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8. Recommended Tools Grid -->
                <div>
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        8. Rekomendasi Stack & Tools Rekayasa Perangkat Lunak
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 font-mono text-xs">
                        @foreach($archEval['recommended_tools'] ?? [] as $tool)
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-[9px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ $tool['category'] }}</span>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">{{ $tool['name'] }}</h4>
                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-normal">{{ $tool['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 9. Panduan Edukatif Hulu ke Hilir: Do's & Don'ts Proyek -->
                @php
                    $stratGuidance = $archEval['strategic_guidance'] ?? [];
                @endphp
                @if(!empty($stratGuidance))
                    <div class="mt-8 pt-8 border-t border-zinc-200 dark:border-zinc-800">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500"></span>
                                9. Panduan Edukatif Hulu ke Hilir: Do's & Don'ts Rekayasa Sistem
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-mono font-bold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700">
                                EDUKATIF & REALISTIS
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-sans mb-4">
                            {{ $stratGuidance['subtitle'] ?? 'Edukasi komprehensif bagi pemangku kepentingan agar investasi teknologi tepat sasaran, efisien, dan bebas risiko scope creep.' }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- DO'S (Prinsip Sukses) -->
                            <div class="p-5 bg-emerald-500/5 dark:bg-emerald-950/20 border-2 border-emerald-500/50 font-mono text-xs">
                                <div class="flex items-center justify-between gap-2 mb-3 pb-2 border-b border-emerald-500/30">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 bg-emerald-500 inline-block"></span>
                                        <span class="font-black uppercase text-emerald-600 dark:text-emerald-400 text-sm">DO'S (LAKUKAN) - 5 PRINSIP SUKSES</span>
                                    </div>
                                    <span class="text-[9px] uppercase px-1.5 py-0.2 bg-emerald-500 text-black font-bold">BEST PRACTICE</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($stratGuidance['dos'] ?? [] as $do)
                                        <div class="p-2.5 bg-white dark:bg-zinc-900 border border-emerald-500/20">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                    {{ $do['tag'] }}
                                                </span>
                                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $do['title'] }}</h4>
                                            </div>
                                            <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">{{ $do['desc'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- DON'TS (Jebakan Pemborosan) -->
                            <div class="p-5 bg-rose-500/5 dark:bg-rose-950/20 border-2 border-rose-500/50 font-mono text-xs">
                                <div class="flex items-center justify-between gap-2 mb-3 pb-2 border-b border-rose-500/30">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 bg-rose-500 inline-block"></span>
                                        <span class="font-black uppercase text-rose-600 dark:text-rose-400 text-sm">DON'TS (HINDARI) - 4 JEBAKAN RISIKO</span>
                                    </div>
                                    <span class="text-[9px] uppercase px-1.5 py-0.2 bg-rose-500 text-white font-bold">AVOID WASTE</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($stratGuidance['donts'] ?? [] as $dont)
                                        <div class="p-2.5 bg-white dark:bg-zinc-900 border border-rose-500/20">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                                    {{ $dont['tag'] }}
                                                </span>
                                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $dont['title'] }}</h4>
                                            </div>
                                            <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">{{ $dont['desc'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 10. Matriks Evaluasi Pro's & Con's (Trade-Off 3 Dimensi) -->
                    <div class="mt-8 pt-8 border-t border-zinc-200 dark:border-zinc-800">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500"></span>
                                10. Matriks Evaluasi Pro's & Con's (Trade-Off 3 Dimensi Arsitektur)
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-mono font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                TRANSPARAN & TERCERAHKAN
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-sans mb-4">
                            Transparansi kelebihan dan kekurangan setiap keputusan teknologi agar klien memahami konsekuensi bisnis dan teknis sejak awal.
                        </p>

                        <div class="space-y-6">
                            @foreach($stratGuidance['pros_and_cons'] ?? [] as $pc)
                                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none font-mono text-xs">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                                        <span class="w-2 h-2 bg-emerald-500"></span>
                                        <span class="text-zinc-400 text-[10px] uppercase">DIMENSI:</span>
                                        <h4 class="font-black text-sm uppercase text-zinc-900 dark:text-zinc-100">{{ $pc['dimension'] }}</h4>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                        <!-- Option A -->
                                        <div class="p-4 bg-white dark:bg-zinc-900 border border-emerald-500/40">
                                            <div class="flex items-center justify-between gap-2 mb-2 pb-1 border-b border-zinc-100 dark:border-zinc-800">
                                                <h5 class="font-bold text-xs text-emerald-600 dark:text-emerald-400 uppercase">{{ $pc['option_a']['name'] }}</h5>
                                                <span class="text-[9px] px-1.5 py-0.2 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold">OPSI A</span>
                                            </div>
                                            <div class="space-y-2 text-[11px] font-sans">
                                                <div>
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono font-bold text-[10px] block mb-1">KELEBIHAN (PRO'S):</span>
                                                    <ul class="space-y-1 text-zinc-600 dark:text-zinc-300">
                                                        @foreach($pc['option_a']['pros'] as $pro)
                                                            <li class="flex items-start gap-1.5">
                                                                <span class="text-emerald-500 font-bold">&check;</span>
                                                                <span>{{ $pro }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="pt-1.5 border-t border-zinc-100 dark:border-zinc-800">
                                                    <span class="text-amber-600 dark:text-amber-400 font-mono font-bold text-[10px] block mb-1">KETERBATASAN (CON'S):</span>
                                                    <ul class="space-y-1 text-zinc-500 dark:text-zinc-400">
                                                        @foreach($pc['option_a']['cons'] as $con)
                                                            <li class="flex items-start gap-1.5">
                                                                <span class="text-amber-500 font-bold">&bull;</span>
                                                                <span>{{ $con }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 p-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-500/30 text-[10px] font-mono text-emerald-700 dark:text-emerald-300">
                                                {{ $pc['option_a']['verdict'] }}
                                            </div>
                                        </div>

                                        <!-- Option B -->
                                        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <div class="flex items-center justify-between gap-2 mb-2 pb-1 border-b border-zinc-100 dark:border-zinc-800">
                                                <h5 class="font-bold text-xs text-zinc-700 dark:text-zinc-300 uppercase">{{ $pc['option_b']['name'] }}</h5>
                                                <span class="text-[9px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold">OPSI B</span>
                                            </div>
                                            <div class="space-y-2 text-[11px] font-sans">
                                                <div>
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono font-bold text-[10px] block mb-1">KELEBIHAN (PRO'S):</span>
                                                    <ul class="space-y-1 text-zinc-600 dark:text-zinc-300">
                                                        @foreach($pc['option_b']['pros'] as $pro)
                                                            <li class="flex items-start gap-1.5">
                                                                <span class="text-emerald-500 font-bold">&check;</span>
                                                                <span>{{ $pro }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="pt-1.5 border-t border-zinc-100 dark:border-zinc-800">
                                                    <span class="text-rose-600 dark:text-rose-400 font-mono font-bold text-[10px] block mb-1">KETERBATASAN (CON'S):</span>
                                                    <ul class="space-y-1 text-zinc-500 dark:text-zinc-400">
                                                        @foreach($pc['option_b']['cons'] as $con)
                                                            <li class="flex items-start gap-1.5">
                                                                <span class="text-rose-500 font-bold">&times;</span>
                                                                <span>{{ $con }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 p-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-[10px] font-mono text-zinc-600 dark:text-zinc-400">
                                                {{ $pc['option_b']['verdict'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <!-- SECTION 07: OPSI VELOCITY PENGERJAAN & AKSESORIS AI GEMINI ULTRA (PRICING & SPRINT SELECTION) -->
            <section class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">07</span>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Opsi Velocity & Akselerasi AI Gemini Ultra</h2>
                        </div>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs font-mono">
                            Pilih kecepatan penyelesaian sistem. Kecepatan akselerasi melibatkan alokasi komputasi cloud Swarm AI Gemini Ultra dan paralel engineering squad.
                        </p>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 border border-emerald-300 dark:border-emerald-800 self-start sm:self-auto">
                        PILIH PAKET UNTUK KONTRAK
                    </span>
                </div>

                <!-- Mathematical Cost Formula Breakdown Banner -->
                <div class="mb-6 p-4 bg-zinc-950 border border-zinc-800 text-zinc-300 font-mono text-xs">
                    <div class="flex items-center justify-between gap-2 border-b border-zinc-800 pb-2 mb-3">
                        <strong class="uppercase text-emerald-400 font-bold tracking-wider">FORMULA LEVEL BIAYA AKSELERASI SWARM AI:</strong>
                        <span class="text-[10px] text-zinc-400">TRANSPARENT PRICING MODEL</span>
                    </div>
                    <div class="p-3 bg-zinc-900 border border-zinc-800 font-mono text-center text-xs sm:text-sm text-emerald-400 font-bold mb-3 overflow-x-auto">
                        Total Investasi = Base Engineering Fee + (&Delta; Velocity Factor &times; Sewa Swarm AI Ultra Cloud) + Dedicated Concurrency Squad
                    </div>
                    <p class="text-zinc-400 text-[11px] leading-relaxed font-sans">
                        Pengerjaan kilat tidak sekadar menambah jam kerja manusia, melainkan mengalokasikan <strong>Swarm AI Agent Parallel Workers (Gemini Ultra)</strong> dengan kuota inferensi jutaan token per menit untuk auto-synthesize skema database, unit test otomatis, dan refactoring real-time tanpa antrean cloud.
                    </p>
                </div>

                <!-- Dynamic Comparative Velocity & Budget Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 font-mono">
                    @foreach($pricingTiers as $tierItem)
                        @php
                            $isMiddleRecommended = ($loop->iteration === 2);
                            $isHighSpeed = ($loop->iteration === 3);
                            $isEmergencyOrEnterprise = $isHighSpeed;
                        @endphp
                        <div 
                            @click="selectedTier = '{{ $tierItem['id'] }}'"
                            :class="selectedTier === '{{ $tierItem['id'] }}' 
                                ? '{{ $isHighSpeed ? 'border-2 border-amber-500 bg-amber-500/10 dark:bg-amber-950/30 shadow-lg' : 'border-2 border-emerald-500 bg-emerald-500/10 dark:bg-emerald-950/30 shadow-lg' }}' 
                                : 'border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 hover:border-zinc-400'"
                            class="p-5 cursor-pointer transition relative flex flex-col justify-between"
                        >
                            @if($isMiddleRecommended)
                                <div class="absolute -top-3 right-4 px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-black uppercase tracking-wider">
                                    ⚡ RECOMMENDED
                                </div>
                            @elseif($isHighSpeed)
                                <div class="absolute -top-3 right-4 px-2 py-0.5 bg-amber-500 text-black text-[9px] font-black uppercase tracking-wider">
                                    🚀 TOP SPEED // SPRINT
                                </div>
                            @endif

                            <div>
                                <div class="flex items-center justify-between gap-1 mb-2">
                                    <span class="text-[9px] uppercase px-1.5 py-0.5 {{ $isEmergencyOrEnterprise ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/40' }} font-bold">
                                        {{ $tierItem['badge'] }}
                                    </span>
                                    <span x-show="selectedTier === '{{ $tierItem['id'] }}'" class="w-2 h-2 rounded-full {{ $isEmergencyOrEnterprise ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                </div>
                                <h3 class="text-base font-black uppercase text-zinc-900 dark:text-zinc-100">{{ $tierItem['name'] }}</h3>
                                <div class="text-xl font-black {{ $isEmergencyOrEnterprise ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }} my-2">
                                    Rp {{ number_format($tierItem['contract_amount'], 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mb-2">
                                    Termin DP 50%: Rp {{ number_format($tierItem['dp_amount'], 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-zinc-700 dark:text-zinc-300 mb-3 bg-zinc-100 dark:bg-zinc-900 p-2 border border-zinc-200 dark:border-zinc-800">
                                    <strong>Formula:</strong> {{ $tierItem['cost_formula'] }}
                                </div>
                                <ul class="space-y-1.5 text-[11px] text-zinc-600 dark:text-zinc-400 mb-4 border-t border-zinc-200 dark:border-zinc-800 pt-3">
                                    <li>&bull; Durasi: <strong>{{ $tierItem['duration'] }}</strong> ({{ $tierItem['speed_multiplier'] }})</li>
                                    <li>&bull; Alokasi: {{ $tierItem['squad_allocation'] }}</li>
                                    <li>&bull; Engine: {{ $tierItem['ai_quota_spec'] }}</li>
                                    @foreach($tierItem['ai_swarm_specs'] ?? [] as $spec)
                                        <li>&bull; {{ $spec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button 
                                type="button" 
                                :class="selectedTier === '{{ $tierItem['id'] }}' 
                                    ? '{{ $isEmergencyOrEnterprise ? 'bg-amber-500 text-black font-bold' : 'bg-emerald-500 text-black font-bold' }}' 
                                    : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                                class="w-full py-2 text-xs uppercase tracking-wider transition"
                            >
                                <span x-text="selectedTier === '{{ $tierItem['id'] }}' ? '&check; PAKET TERPILIH' : 'PILIH PAKET'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>

            <!-- SECTION 08: TIMELINE & GANTT MILESTONE (ALIGNED TO VELOCITY) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">08</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Timeline & Milestone Proyek (Velocity Aligned)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        Target: <span x-text="tierAmounts[selectedTier]?.days || '-'"></span>
                    </span>
                </div>

                <div class="space-y-2.5 font-mono text-xs">
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 1: Architecture Blueprint, PostgreSQL Schema & ULID Migration</span>
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="'Hari 1 - ' + Math.max(2, Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.25))"></span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 2: Core Business Logic & Filament Admin v5 Engine</span>
                        </div>
                        <span class="text-zinc-500" x-text="'Hari ' + (Math.max(2, Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.25)) + 1) + ' - ' + Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.60)"></span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 3: Frontend Island UI (React 19) & User Flow Pipeline</span>
                        </div>
                        <span class="text-zinc-500" x-text="'Hari ' + (Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.60) + 1) + ' - ' + Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.85)"></span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 4: End-to-End Testing, Security Audit & Production Handover</span>
                        </div>
                        <span class="text-emerald-500 font-bold" x-text="'Hari ' + (Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.85) + 1) + ' - ' + (tierAmounts[selectedTier]?.days || 'Selesai')"></span>
                    </div>
                </div>
            </section>

            <!-- SECTION 09: STANDAR TATA KELOLA, KUALITAS & SLA SERAH TERIMA (GOVERNANCE & SLA) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">09</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Tata Kelola, Kualitas & SLA Serah Terima</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        ENTERPRISE SERVICE LEVEL AGREEMENT
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-mono text-xs mb-6">
                    <!-- 1. Definition of Done (DoD) -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <h3 class="font-bold uppercase text-zinc-900 dark:text-zinc-100 text-xs">1. Kriteria Penyelesaian Resmi (Definition of Done)</h3>
                        </div>
                        <ul class="space-y-2 text-zinc-600 dark:text-zinc-400 text-[11px]">
                            @foreach($prd['governance_and_sla']['definition_of_done'] ?? [
                                'Seluruh fitur MVP Fase 1 berjalan sesuai spesifikasi di server Staging & Production.',
                                'Lolos audit keamanan dasar (CSRF token, sanitasi input XSS, proteksi SQL Injection, & HTTPS SSL).',
                                'Skema basis data relasional PostgreSQL dengan Primary Key ULID terverifikasi.',
                                'Dasbor admin Filament v5 dapat diakses oleh peran Superadmin / Staff yang ditunjuk.',
                                'Sesi pelatihan administrasi singkat dan serah terima kredensial resmi sistem.'
                            ] as $dod)
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span>{{ $dod }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- 2. Browser & Device Support Matrix -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <h3 class="font-bold uppercase text-zinc-900 dark:text-zinc-100 text-xs">2. Matriks Dukungan Browser & Perangkat</h3>
                        </div>
                        <div class="space-y-3 text-[11px]">
                            <div class="p-2.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300">
                                <strong class="block mb-1">&check; PERANGKAT & BROWSER DIDUKUNG RESMI:</strong>
                                {{ $prd['governance_and_sla']['browser_device_matrix']['supported'] ?? 'Google Chrome, Apple Safari, Mozilla Firefox, Microsoft Edge (rilis 2 tahun terakhir); iOS Safari 15+; Android Chrome 100+.' }}
                            </div>
                            <div class="p-2.5 bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300">
                                <strong class="block mb-1">&times; PERAMBAN DI LUAR RUANG LINGKUP (EXCLUDED):</strong>
                                {{ $prd['governance_and_sla']['browser_device_matrix']['unsupported'] ?? 'Internet Explorer 11, Opera Mini data-saving mode, UC Browser legacy rendering engine, dan peramban ponsel non-standar.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-mono text-xs">
                    <!-- 3. Warranty & Bug-Fix Period -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold block w-fit mb-2">
                            GARANSI PENUH 30 HARI
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Garansi Perbaikan Bug</h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Gratis perbaikan terhadap bug, error, atau ketidaksesuaian fungsi MVP Fase 1 selama 30 hari kalender setelah Go-Live.
                        </p>
                    </div>

                    <!-- 4. Content Bottleneck Protocol -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold block w-fit mb-2">
                            BATAS MATERI 7 HARI
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Klausul Konten Klien</h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Jika materi teks/foto belum diberikan dalam 7 hari kerja, developer berhak menggunakan dummy/placeholder demi menjaga ketepatan waktu rilis.
                        </p>
                    </div>

                    <!-- 5. Third-Party Recurring Transparency -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold block w-fit mb-2">
                            BIAYA BERULANG JUJUR
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Estimasi Pihak Ketiga</h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Domain tahunan (Rp 150rb-250rb/thn) & hosting (Rp 50rb-150rb/bln) dibayarkan langsung ke penyedia cloud resmi tanpa markup tersembunyi.
                        </p>
                    </div>
                </div>
            </section>

            <!-- SECTION 10: SCOPE FREEZE, DIGITAL CONTRACT & DP MIDTRANS (CRUCIAL) -->
            <section class="bg-zinc-900 text-white border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none no-print">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-6 mb-6">
                    <div>
                        <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-widest bg-emerald-500 text-black inline-block mb-2 rounded-none">
                            LEGAL & PAYMENT PROTOCOL
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight flex items-center gap-2">
                            <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs inline-flex items-center justify-center rounded-none">10</span>
                            <span>Kunci Scope Proyek & Pembayaran DP</span>
                        </h3>
                        <p class="text-zinc-400 text-xs mt-1 font-sans">
                            Pengerjaan proyek resmi dimulai setelah penandatanganan kontrak digital dan konfirmasi DP via Midtrans Escrow.
                        </p>
                    </div>
                    <div class="text-left sm:text-right font-mono">
                        <span class="text-zinc-400 text-xs block">TERMIN TERPILIH: <span class="text-white font-bold" x-text="tierAmounts[selectedTier].name"></span></span>
                        <span class="text-xl font-black text-emerald-400" x-text="'DP (50%): Rp ' + tierAmounts[selectedTier].dp.toLocaleString('id-ID')"></span>
                        <span class="text-[10px] text-zinc-400 block" x-text="'Total Kontrak: Rp ' + tierAmounts[selectedTier].contract.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <!-- Scope Lock Policy Notice -->
                <div class="p-4 bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs font-mono leading-relaxed mb-6">
                    <strong class="text-amber-400 block mb-1 uppercase font-bold">&bull; Batasan Ruang Lingkup & Ketentuan Tambah Fitur (Change Request)</strong>
                    Seluruh fitur yang tertera di atas terkunci secara hukum dalam Kontrak Induk. Apabila di kemudian hari Klien menghendaki penambahan fitur baru di luar spesifikasi ini, penambahan tersebut akan diakomodasikan melalui <strong>Change Request (CR) / Addendum</strong> terpisah dengan perhitungan biaya dan tambahan hari kerja tersendiri tanpa mengganggu jadwal kontrak utama.
                </div>

                <!-- Action Buttons: Sign Contract & Pay DP -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <form method="POST" action="{{ route('blueprint.generate-contract', $blueprint->slug) }}" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" :value="selectedTier">
                        <button 
                            type="submit"
                            class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2"
                        >
                            <span>Tanda Tangani Kontrak & Kunci Scope</span>
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </form>

                    <button 
                        type="button" 
                        @click="paymentModalOpen = true"
                        class="w-full h-full bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-700 transition flex items-center justify-center gap-2"
                    >
                        <span>Instruksi Bayar DP (Midtrans)</span>
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </button>

                    <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" :value="selectedTier">
                        <button 
                            type="submit"
                            class="w-full h-full bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-700 transition flex items-center justify-center gap-2"
                        >
                            <span>Tambahkan ke Cart</span>
                            <svg class="w-4 h-4 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </section>

        </main>
    @endif

    <!-- Interactive Midtrans Escrow Payment Modal -->
    <div 
        x-show="paymentModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 font-mono text-xs"
        @keydown.escape.window="paymentModalOpen = false"
    >
        <div 
            class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 max-w-lg w-full p-6 shadow-2xl relative"
            @click.outside="paymentModalOpen = false"
        >
            <!-- Close Button -->
            <button 
                @click="paymentModalOpen = false" 
                class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-lg font-bold"
            >
                &times;
            </button>

            <!-- Modal Header -->
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-4">
                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-bold uppercase tracking-wider">
                    MIDTRANS SECURE ESCROW PAYMENT
                </span>
                <h3 class="text-xl font-black uppercase text-zinc-900 dark:text-zinc-100 mt-2">
                    Instruksi Pembayaran DP (50%)
                </h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-sans">
                    Proyek: <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong>
                </p>
            </div>

            <!-- Invoice Summary Card -->
            <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 mb-4 space-y-2">
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Opsi Velocity Terpilih</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100" x-text="tierAmounts[selectedTier].name"></span>
                </div>
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Nilai Total Kontrak</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100" x-text="'Rp ' + tierAmounts[selectedTier].contract.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Termin DP (Uang Muka)</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">50% di Muka</span>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex justify-between items-baseline">
                    <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Total Tagihan DP</span>
                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + tierAmounts[selectedTier].dp.toLocaleString('id-ID')"></span>
                </div>
                <div class="text-[10px] text-zinc-400 pt-1">
                    ORDER ID: NPRO-DP-{{ strtoupper(substr($blueprint->id, 0, 8)) }}
                </div>
            </div>

            <!-- Payment Methods Info -->
            <div class="space-y-2 mb-6">
                <div class="text-[11px] font-bold uppercase text-zinc-700 dark:text-zinc-300">
                    Kanal Pembayaran Otomatis:
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-[10px]">
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">QRIS (GoPay/OVO)</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">BCA VA</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">Mandiri Bill</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">Kartu Kredit</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
                <button 
                    type="button" 
                    @click="isPayingSnap = true; window.payBlueprintSnap(selectedTier, () => { isPayingSnap = true }, () => { isPayingSnap = false })"
                    :disabled="isPayingSnap"
                    class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-3.5 px-4 text-center block transition shadow-lg cursor-pointer disabled:opacity-50"
                >
                    <span x-show="!isPayingSnap">Bayar Sekarang via Midtrans Snap &rarr;</span>
                    <span x-show="isPayingSnap" class="inline-block animate-pulse">Membuat Sesi Snap...</span>
                </button>
                
                <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                    @csrf
                    <input type="hidden" name="tier" :value="selectedTier">
                    <button 
                        type="submit" 
                        class="w-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs uppercase tracking-wider py-2.5 px-4 text-center block transition border border-zinc-300 dark:border-zinc-700"
                    >
                        Simpan ke Cart Belanja
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Global Footer -->
    <footer class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 py-6 px-4 text-center text-xs border-t border-zinc-200 dark:border-zinc-800 font-mono no-print">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} NERIAH PRO HUB &bull; ULTIMATE PRD & ARCHITECTURE BLUEPRINT</p>
            <p class="text-zinc-400 dark:text-zinc-500">ENTERPRISE ARCHITECTURE // HIGH-INTEGRITY DATA</p>
        </div>
    </footer>

    <script>
        window.copyFullPrdMarkdown = async function(btnEl) {
            const orig = btnEl ? btnEl.innerHTML : '';
            if (btnEl) btnEl.innerHTML = '<span class="text-emerald-400 font-bold animate-pulse">Mengambil MD...</span>';
            try {
                const res = await fetch("{{ route('blueprint.raw-md', $blueprint->slug) }}");
                if (!res.ok) throw new Error('Gagal mengunduh teks markdown');
                const text = await res.text();
                await navigator.clipboard.writeText(text);
                if (window.showToast) {
                    window.showToast({
                        type: 'success',
                        title: 'PRD ULTIMATE DISALIN!',
                        message: 'Spesifikasi PRD lengkap beserta seluruh prompt agent berhasil disalin ke clipboard.'
                    });
                }
                if (btnEl) {
                    btnEl.innerHTML = '<span class="text-emerald-400 font-bold">✓ TERSALIN!</span>';
                    setTimeout(() => { btnEl.innerHTML = orig; }, 2500);
                }
            } catch (err) {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'GAGAL MENYALIN',
                        message: err.message || 'Tidak dapat menyalin konten ke clipboard.'
                    });
                }
                if (btnEl) btnEl.innerHTML = orig;
            }
        };

        window.copyFeaturePrompt = function(btnEl, codeId) {
            const el = document.getElementById(codeId);
            if (!el) return;
            const code = el.innerText || el.textContent;
            navigator.clipboard.writeText(code.trim()).then(() => {
                const orig = btnEl.innerHTML;
                btnEl.innerHTML = '<span class="text-emerald-400 font-bold">✓ PROMPT DISALIN!</span>';
                if (window.showToast) {
                    window.showToast({
                        type: 'success',
                        title: 'PROMPT FITUR DISALIN',
                        message: 'Prompt agent untuk fitur ini siap di-paste ke Cursor Composer / Claude Code / Antigravity.'
                    });
                }
                setTimeout(() => { btnEl.innerHTML = orig; }, 2200);
            }).catch(err => {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'GAGAL MENYALIN',
                        message: 'Browser memblokir akses clipboard.'
                    });
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const timerEl = document.getElementById('nav-cart-timer');
            if (timerEl) {
                let remaining = parseInt(timerEl.getAttribute('data-rem'), 10);
                if (!isNaN(remaining) && remaining > 0) {
                    setInterval(function() {
                        remaining--;
                        if (remaining <= 0) {
                            timerEl.textContent = '⏱️ EXPIRED';
                            timerEl.classList.remove('text-amber-500');
                            timerEl.classList.add('text-rose-500');
                        } else {
                            const h = Math.floor(remaining / 3600);
                            const m = Math.floor((remaining % 3600) / 60);
                            const s = Math.floor(remaining % 60);
                            timerEl.textContent = '⏱️ ' + [
                                h.toString().padStart(2, '0'),
                                m.toString().padStart(2, '0'),
                                s.toString().padStart(2, '0')
                            ].join(':');
                        }
                    }, 1000);
                }
            }
        });
    </script>
</body>
</html>
