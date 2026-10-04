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
        window.payBlueprintSnap = async function(tier, agreeSignOff, onStart, onFinish) {
            if (!agreeSignOff) {
                if (window.showToast) {
                    window.showToast({
                        type: 'warning',
                        title: 'PERSETUJUAN KONTRAK & SCOPE',
                        message: 'Silakan centang persetujuan syarat spesifikasi scope dokumen sebelum melanjutkan pembayaran.',
                        duration: 4000
                    });
                }
                if (onFinish) onFinish();
                return;
            }
            if (onStart) onStart();
            try {
                const res = await fetch('{{ route('blueprint.snap-token', $blueprint->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tier: tier, agree_sign_off: agreeSignOff })
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

        window.validateBlueprintVoucher = async function(slug, code, onStart, onFinish) {
            if (onStart) onStart();
            try {
                const res = await fetch('/blueprint/' + encodeURIComponent(slug) + '/voucher/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: code })
                });
                const data = await res.json();
                if (!data.valid) {
                    throw new Error(data.message || 'Kode voucher tidak valid.');
                }
                return data;
            } finally {
                if (onFinish) onFinish();
            }
        };

        window.claimBlueprintVoucher = async function(slug, code, agreeSignOff, onStart, onFinish) {
            if (!agreeSignOff) {
                if (window.showToast) {
                    window.showToast({
                        type: 'warning',
                        title: 'PERSETUJUAN KONTRAK & SCOPE',
                        message: 'Silakan centang persetujuan syarat spesifikasi scope dokumen sebelum mengklaim voucher.'
                    });
                }
                return null;
            }
            if (onStart) onStart();
            try {
                const res = await fetch('/blueprint/' + encodeURIComponent(slug) + '/voucher/claim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: code, agree_sign_off: agreeSignOff })
                });
                const data = await res.json();
                if (!data.success) {
                    throw new Error(data.message || 'Gagal mengklaim voucher.');
                }
                return data;
            } finally {
                if (onFinish) onFinish();
            }
        };

        window.jumpToSection = function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const yOffset = -80;
            const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });

            // Visual pulse highlight
            el.classList.add('outline-2', 'outline-emerald-500', 'transition-all');
            setTimeout(() => {
                el.classList.remove('outline-2', 'outline-emerald-500');
            }, 2000);
        };

        window.setupBlueprintScrollSpy = function(onActiveChange) {
            const sectionIds = [
                'section-1', 'section-2', 'section-3', 'section-3-5',
                'section-3-8', 'section-4', 'section-5', 'section-6',
                'section-7', 'section-8', 'section-9', 'section-10'
            ];
            let ticking = false;
            const update = () => {
                const scrollPosition = window.scrollY + 160;
                let current = sectionIds[0];
                for (let i = sectionIds.length - 1; i >= 0; i--) {
                    const el = document.getElementById(sectionIds[i]);
                    if (el && el.offsetTop <= scrollPosition) {
                        current = sectionIds[i];
                        break;
                    }
                }
                onActiveChange(current);
                ticking = false;
            };
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(update);
                    ticking = true;
                }
            }, { passive: true });
            update();
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

        window.copyCockpitStepPrompt = function(btnEl, preId) {
            const el = document.getElementById(preId);
            if (!el) return;
            const text = el.innerText || el.textContent;
            navigator.clipboard.writeText(text.trim()).then(() => {
                const orig = btnEl.innerHTML;
                btnEl.innerHTML = '<span class="text-black dark:text-emerald-950 font-black">✓ PROMPT DISALIN!</span>';
                if (window.showToast) {
                    window.showToast({
                        type: 'success',
                        title: 'PROMPT TAHAP DISALIN',
                        message: 'Prompt vertikal siap di-paste ke Cursor / Claude Code / Antigravity!'
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
    </script>
@php
    $itemizedScope = $prd['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint);
    $pricingTiers = $prd['velocity_pricing_options'] ?? ($itemizedScope['velocity_tiers'] ?? \App\Services\PrdGeneratorService::generateVelocityPricingOptions(
        $blueprint->target_waktu ?? '30 Hari Kerja',
        $blueprint->user_metadata['kisaran_budget'] ?? null,
        $blueprint->nama_bisnis ?? $blueprint->client_name,
        $blueprint->masalah_utama ?? ''
    ));
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

    $mvpEngineeringSpecs = $prd['features']['mvp_phase1'] ?? ($prd['engineering_specs']['mvp_specs'] ?? []);
    $totalDevSteps = count($mvpEngineeringSpecs) + 2;

    $erdTablesList = $prd['erd_schema']['tables'] ?? [];
    $erdSummary = [];
    foreach ($erdTablesList as $t) {
        $cols = array_map(fn($c) => $c['name'] . ' (' . $c['type'] . ')', $t['columns'] ?? []);
        $erdSummary[] = "- Tabel: " . $t['name'] . " [PK: " . $t['primary_key'] . "]\n  Kolom: " . implode(', ', array_slice($cols, 0, 8));
    }
    $erdText = implode("\n", $erdSummary);

    $hasSync = !empty($prd['mobile_and_sync_architecture']);
    $syncText = $hasSync 
        ? "\n\nArsitektur Sinkronisasi Offline-First (Bab 5.6):\n- Endpoint Push: POST /api/v1/sync/push\n- Endpoint Pull: GET /api/v1/sync/pull\n- Resolusi Konflik: Last-Write-Wins (LWW) berdasarkan updated_at ULID\n- SQLite Local DDL & SQLite client support" 
        : "";

    $foundationPrompt = "[FONDASI GLOBAL & SETUP BASIS DATA: " . ($blueprint->nama_bisnis ?: $blueprint->client_name) . "]\n"
        . "Tugas Arsitektur: Inisialisasi struktur database PostgreSQL, model Eloquent ULID, dan base stack project.\n\n"
        . "Spesifikasi Bab 5 (ERD Schema):\n" . $erdText . $syncText . "\n\n"
        . "Aturan Wajib Kepatuhan (.agents/AGENTS.md):\n"
        . "1. Gunakan ULID (->ulid('id')->primary()) untuk semua primary key tabel bisnis. Dilarang AUTO_INCREMENT.\n"
        . "2. Model Eloquent wajib menyertakan trait HasUlids.\n"
        . "3. Keyset Cursor Pagination O(1) (cursorPaginate()). Dilarang offset pagination.\n"
        . "4. Kolom multi-bahasa bertipe JSON dengan cast 'array'.\n"
        . "5. Jalankan verifikasi terminal: php artisan migrate:status && php artisan test\n"
        . "6. Setelah exit code 0, lakukan commit git: git commit -m 'chore(db): setup ULID migrations and base foundation'";
@endphp
</head>
<body x-data="{ 
    userMenuOpen: false, 
    paymentModalOpen: false, 
    flowTab: 'visual', 
    erdTab: 'visual', 
    erdLang: 'id',
    chartStudioTab: 'workflow',
    devEducationMode: 'step_by_step',
    devActiveStep: 0,
    devCompletedSteps: (() => {
        try {
            return JSON.parse(localStorage.getItem('neriah_dev_progress_{{ $blueprint->slug }}') || '{}');
        } catch(e) {
            return {};
        }
    })(),
    isStepCompleted(k) {
        return !!this.devCompletedSteps[k];
    },
    toggleStepCompleted(k, label) {
        this.devCompletedSteps[k] = !this.devCompletedSteps[k];
        try {
            localStorage.setItem('neriah_dev_progress_{{ $blueprint->slug }}', JSON.stringify(this.devCompletedSteps));
        } catch(e){}
        if (window.showToast) {
            if (this.devCompletedSteps[k]) {
                window.showToast({
                    type: 'success',
                    title: 'TAHAP TERVERIFIKASI',
                    message: (label || k) + ' ditandai selesai & disimpan ke riwayat sprint.'
                });
            } else {
                window.showToast({
                    type: 'info',
                    title: 'STATUS DIPERBARUI',
                    message: (label || k) + ' dikembalikan ke status antrean (pending).'
                });
            }
        }
    },
    resetDevProgress() {
        this.devCompletedSteps = {};
        try {
            localStorage.removeItem('neriah_dev_progress_{{ $blueprint->slug }}');
        } catch(e){}
        if (window.showToast) {
            window.showToast({
                type: 'warning',
                title: 'PROGRESS DIRESET',
                message: 'Semua progres sprint developer telah dibersihkan.'
            });
        }
    },
    getDevCompletedCount() {
        return Object.values(this.devCompletedSteps).filter(Boolean).length;
    },
    getDevProgressPercentage() {
        const total = {{ $totalDevSteps }};
        if (!total || total <= 0) return 0;
        return Math.min(100, Math.round((this.getDevCompletedCount() / total) * 100));
    },
    selectedIdeTool: 'antigravity_ide',
    selectedTier: '{{ $defaultSelectedTier }}',
    tierAmounts: {{ json_encode($alpineTiers) }},
    isPayingSnap: false,
    devPlaybookOpen: true,
    activeDevPhase: 1,
    copyMasterPromptSuccess: false,
    locale: localStorage.getItem('neriah_blueprint_lang') || '{{ app()->getLocale() === "en" ? "en" : "id" }}',
    setLocale(l) {
        this.locale = l;
        try { localStorage.setItem('neriah_blueprint_lang', l); } catch(e){}
    },
    showAiPromptModal: false,
    selectedPromptAgent: 'antigravity',
    selectedPromptSprint: 'all',
    generateAgentPrompt(agent, sprint) {
        return window.getBlueprintAgentPrompt ? window.getBlueprintAgentPrompt(agent, sprint) : '';
    },
    agreeSignOff: false,
    voucherCode: '',
    appliedVoucher: null,
    isValidatingVoucher: false,
    isClaimingVoucher: false,
    async applyVoucher() {
        const c = this.voucherCode.trim().toUpperCase();
        if (!c) {
            if (window.showToast) {
                window.showToast({ type: 'warning', title: 'KODE VOUCHER', message: 'Silakan masukkan kode voucher terlebih dahulu.' });
            }
            return;
        }
        try {
            const v = await window.validateBlueprintVoucher('{{ $blueprint->slug }}', c, () => { this.isValidatingVoucher = true; }, () => { this.isValidatingVoucher = false; });
            this.appliedVoucher = v;
            if (window.showToast) {
                window.showToast({ type: 'success', title: 'VOUCHER VALID', message: v.message || 'Potongan voucher berhasil diaplikasikan!' });
            }
        } catch(err) {
            this.appliedVoucher = null;
            if (window.showToast) {
                window.showToast({ type: 'error', title: 'VOUCHER TIDAK VALID', message: err.message || 'Kode voucher tidak valid.' });
            }
        }
    },
    resetVoucher() {
        this.appliedVoucher = null;
        this.voucherCode = '';
    },
    async submitClaimVoucher() {
        if (!this.appliedVoucher || !this.voucherCode.trim()) return;
        try {
            const res = await window.claimBlueprintVoucher('{{ $blueprint->slug }}', this.voucherCode.trim().toUpperCase(), this.agreeSignOff, () => { this.isClaimingVoucher = true; }, () => { this.isClaimingVoucher = false; });
            if (res && res.success) {
                if (window.showToast) {
                    window.showToast({ type: 'success', title: 'PELAYANAN GRATIS AKTIF', message: res.message, duration: 4000 });
                }
                setTimeout(() => { window.location.reload(); }, 1800);
            }
        } catch(err) {
            if (window.showToast) {
                window.showToast({ type: 'error', title: 'KLAIM GAGAL', message: err.message || 'Gagal memproses klaim voucher.' });
            }
        }
    },
    activeSectionId: 'section-1',
    indexSearchQuery: '',
    indexAccordionOpen: true,
    floatingIndexOpen: false,
    accordionGroups: {
        scope: true,
        studio: true,
        infra: true,
        legal: true
    },
    toggleAccordionGroup(grp) {
        this.accordionGroups[grp] = !this.accordionGroups[grp];
    },
    expandAllGroups() {
        this.accordionGroups.scope = true;
        this.accordionGroups.studio = true;
        this.accordionGroups.infra = true;
        this.accordionGroups.legal = true;
    },
    collapseAllGroups() {
        this.accordionGroups.scope = false;
        this.accordionGroups.studio = false;
        this.accordionGroups.infra = false;
        this.accordionGroups.legal = false;
    },
    jumpTo(id) {
        window.jumpToSection(id);
        this.activeSectionId = id;
        this.floatingIndexOpen = false;
    },
    getActiveSectionTitle() {
        const titles = {
            'section-1': { id: '01. Executive Discovery', en: '01. Executive Discovery' },
            'section-2': { id: '02. RBAC & Aktor Sistem', en: '02. RBAC & System Actors' },
            'section-3': { id: '03. Rekayasa Fitur MVP', en: '03. Feature Engineering' },
            'section-3-5': { id: '04. Edukasi Handoff AI', en: '04. AI Handoff Playbook' },
            'section-3-8': { id: '05. Virtual Studio Charts', en: '05. Virtual Charts Studio' },
            'section-4': { id: '06. Alur Kerja User Flow', en: '06. Core User Flow' },
            'section-5': { id: '07. Database ERD', en: '07. Database ERD Blueprint' },
            'section-6': { id: '08. Evaluasi Infra VPS', en: '08. Cloud VPS & Infra' },
            'section-7': { id: '09. Velocity Pricing', en: '09. Velocity Pricing' },
            'section-8': { id: '10. Timeline Sprint Gantt', en: '10. Sprint Timeline' },
            'section-9': { id: '11. Tata Kelola & SLA', en: '11. Governance & SLA' },
            'section-10': { id: '12. Kunci Scope & DP', en: '12. Scope Freeze & DP' }
        };
        const s = titles[this.activeSectionId] || { id: 'Daftar Isi PRD', en: 'PRD Directory' };
        return this.locale === 'en' ? s.en : s.id;
    },
    getActiveSectionIndex() {
        const order = ['section-1', 'section-2', 'section-3', 'section-3-5', 'section-3-8', 'section-4', 'section-5', 'section-6', 'section-7', 'section-8', 'section-9', 'section-10'];
        const idx = order.indexOf(this.activeSectionId);
        return idx >= 0 ? (idx + 1) : 1;
    }
}" 
x-init="
    $nextTick(() => {
        window.setupBlueprintScrollSpy(id => { activeSectionId = id; });
    });
    $watch('flowTab', val => {
        if (val === 'mermaid') $nextTick(() => window.renderMermaidDiagram('mermaid-flow-target', 'mermaid-flow-source'));
    });
    $watch('erdTab', val => {
        if (val === 'mermaid') $nextTick(() => window.renderMermaidDiagram('mermaid-erd-target', 'mermaid-erd-source'));
    });
    $watch('chartStudioTab', val => {
        if (val === 'workflow') $nextTick(() => window.renderMermaidDiagram('mermaid-studio-flow-target', 'mermaid-studio-flow-source'));
        if (val === 'erd') $nextTick(() => window.renderMermaidDiagram('mermaid-studio-erd-target', 'mermaid-studio-erd-source'));
        if (val === 'feature_dep') $nextTick(() => window.renderMermaidDiagram('mermaid-studio-featdep-target', 'mermaid-studio-featdep-source'));
        if (val === 'gantt') $nextTick(() => window.renderMermaidDiagram('mermaid-studio-gantt-target', 'mermaid-studio-gantt-source'));
        if (val === 'infra') $nextTick(() => window.renderMermaidDiagram('mermaid-studio-infra-target', 'mermaid-studio-infra-source'));
    });
    $nextTick(() => {
        window.renderMermaidDiagram('mermaid-studio-flow-target', 'mermaid-studio-flow-source');
    });
" class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Header Navigation Bar (Sharp Precision Theme) -->
    <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 py-3 px-6 sticky top-0 z-50 no-print transition-colors">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="text-sm font-black uppercase tracking-tight flex items-center gap-2 text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">N</span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // PRD SPEC</span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Dual-Language Toggle Button (ID/EN) -->
                <button @click="setLocale(locale === 'id' ? 'en' : 'id')" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5" title="Ganti Bahasa / Switch Language">
                    <span class="w-2 h-2 rounded-none" :class="locale === 'en' ? 'bg-sky-500' : 'bg-emerald-500'"></span>
                    <span x-text="locale === 'id' ? 'ID ➔ EN' : 'EN ➔ ID'">ID ➔ EN</span>
                </button>

                <button onclick="toggleTheme()" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono rounded-none border border-zinc-300 dark:border-zinc-700 transition">
                    THEME
                </button>
                <button onclick="window.print()" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span class="hidden sm:inline" x-text="locale === 'en' ? 'Print' : 'Cetak'">Cetak</span>
                </button>
                <a href="{{ route('blueprint.download-pdf', $blueprint->slug) }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <span>PDF</span>
                </a>
                <a href="{{ route('blueprint.download-md', $blueprint->slug) }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <span>MD</span>
                </a>

                <!-- Dedicated AI AGENT HELPER Trigger -->
                <button type="button" @click="showAiPromptModal = true" class="px-3 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono uppercase font-bold rounded-none border border-emerald-500/30 flex items-center gap-1.5 transition" title="Buka Pusat Helper Prompt AI Agent (Antigravity, Cursor, Claude Code, Windsurf)">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span>PROMPT AGENT</span>
                </button>

                <!-- AI Orchestration Sprint Cockpit Button in Header -->
                <button type="button" @click="jumpTo('section-3-5')" class="px-2.5 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none border border-zinc-700 dark:border-emerald-400 flex items-center gap-1.5 transition" title="Buka AI Agent Sprint Execution Cockpit">
                    <span class="w-1.5 h-1.5 rounded-none" :class="getDevCompletedCount() > 0 ? 'bg-emerald-400 dark:bg-black animate-pulse' : 'bg-amber-400 dark:bg-black'"></span>
                    <span class="hidden lg:inline">COCKPIT SPRINT:</span>
                    <span x-text="getDevCompletedCount() + '/' + {{ $totalDevSteps }}"></span>
                </button>

                <a href="{{ route('blueprint.create', ['slug' => $blueprint->slug]) }}" class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono uppercase font-bold rounded-none border border-emerald-500/30 transition flex items-center gap-1" title="Lengkapi / Tambah Kebutuhan di Studio">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span class="hidden md:inline" x-text="locale === 'en' ? 'EDIT SPEC' : 'LENGKAPI SPESIFIKASI'">LENGKAPI SPESIFIKASI</span>
                </a>

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
                $blueprintSections = [
                    [
                        'id' => 'section-1',
                        'num' => '01',
                        'group' => 'scope',
                        'title_id' => 'Executive Technical Discovery',
                        'title_en' => 'Executive Technical Discovery',
                        'subtitle_id' => '26 Parameter Arsitektur & Analisis Kebutuhan Bisnis',
                        'subtitle_en' => '26 Architecture Parameters & Business Needs Analysis',
                        'badge' => '26 PARAMS',
                    ],
                    [
                        'id' => 'section-2',
                        'num' => '02',
                        'group' => 'scope',
                        'title_id' => 'Pengguna & Hak Akses (RBAC)',
                        'title_en' => 'Users & Access Control (RBAC)',
                        'subtitle_id' => 'Aktor Sistem, Peran Pengguna & Batasan Otoritas',
                        'subtitle_en' => 'System Actors, User Roles & Authorization Boundaries',
                        'badge' => 'RBAC',
                    ],
                    [
                        'id' => 'section-3',
                        'num' => '03',
                        'group' => 'scope',
                        'title_id' => 'Spesifikasi Rekayasa Fitur',
                        'title_en' => 'Feature Engineering Specs',
                        'subtitle_id' => 'Vertical Slices, Gherkin Scenarios & Agent Matrix',
                        'subtitle_en' => 'Vertical Slices, Gherkin Scenarios & Agent Matrix',
                        'badge' => 'MVP FASE 1',
                    ],
                    [
                        'id' => 'section-3-5',
                        'num' => '04',
                        'group' => 'studio',
                        'title_id' => 'Pusat Edukasi Developer AI',
                        'title_en' => 'AI Developer Education Hub',
                        'subtitle_id' => 'Protokol Handoff AI Coding Agent (Cursor, Claude, Windsurf)',
                        'subtitle_en' => 'AI Coding Agent Handoff Protocols (Cursor, Claude, Windsurf)',
                        'badge' => 'DEV PLAYBOOK',
                    ],
                    [
                        'id' => 'section-3-8',
                        'num' => '05',
                        'group' => 'studio',
                        'title_id' => 'Virtual Architecture Studio',
                        'title_en' => 'Virtual Architecture Studio',
                        'subtitle_id' => 'Command Center 4 Diagram Visual (Workflow, ERD, Dep, Gantt)',
                        'subtitle_en' => 'Command Center for 4 Visual Diagrams (Workflow, ERD, Dep, Gantt)',
                        'badge' => '4 CHARTS',
                    ],
                    [
                        'id' => 'section-4',
                        'num' => '06',
                        'group' => 'studio',
                        'title_id' => 'Alur Kerja Utama (User Flow)',
                        'title_en' => 'Core User Workflow',
                        'subtitle_id' => 'Diagram Alur Transaksi, Interaksi Visual & Validasi Bisnis',
                        'subtitle_en' => 'Transaction Flowchart, Visual Interaction & Business Validation',
                        'badge' => 'FLOWCHART',
                    ],
                    [
                        'id' => 'section-5',
                        'num' => '07',
                        'group' => 'studio',
                        'title_id' => 'Database ERD & Skema Relasi',
                        'title_en' => 'Database ERD & Relational Schema',
                        'subtitle_id' => 'Topologi PostgreSQL Strict, Entitas Kunci & Tipe Data ULID',
                        'subtitle_en' => 'PostgreSQL Strict Topology, Key Entities & ULID Datatypes',
                        'badge' => 'POSTGRESQL',
                    ],
                    [
                        'id' => 'section-6',
                        'num' => '08',
                        'group' => 'infra',
                        'title_id' => 'Evaluasi Arsitektur & Infra',
                        'title_en' => 'Architecture & Infra Evaluation',
                        'subtitle_id' => 'Spesifikasi Cloud VPS, AI Database, Redis & Keamanan',
                        'subtitle_en' => 'Cloud VPS Specs, AI Database, Redis & Security Stack',
                        'badge' => 'MANAGED VPS',
                    ],
                    [
                        'id' => 'section-7',
                        'num' => '09',
                        'group' => 'legal',
                        'title_id' => 'Opsi Velocity Pengerjaan & Harga',
                        'title_en' => 'Delivery Velocity & Pricing',
                        'subtitle_id' => 'Termin Pembayaran, Pilihan Sprint & Akselerasi AI Gemini Ultra',
                        'subtitle_en' => 'Payment Milestones, Sprint Velocity & Gemini Ultra Cloud',
                        'badge' => 'PRICING TIERS',
                    ],
                    [
                        'id' => 'section-8',
                        'num' => '10',
                        'group' => 'legal',
                        'title_id' => 'Timeline & Gantt Milestone',
                        'title_en' => 'Timeline & Gantt Milestones',
                        'subtitle_id' => 'Alokasi Hari Pengerjaan per Sprint dari Kickoff sampai Delivery',
                        'subtitle_en' => 'Daily Sprint Allocations from Kickoff to Production Delivery',
                        'badge' => 'SPRINTS',
                    ],
                    [
                        'id' => 'section-9',
                        'num' => '11',
                        'group' => 'legal',
                        'title_id' => 'Tata Kelola, Kualitas & SLA',
                        'title_en' => 'Governance, Quality & SLA',
                        'subtitle_id' => 'Definition of Done (DoD), Garansi Bug 30 Hari & Penyerahan Repo',
                        'subtitle_en' => 'Definition of Done (DoD), 30-Day Bug Warranty & Repo Handover',
                        'badge' => 'SLA 30 HARI',
                    ],
                    [
                        'id' => 'section-10',
                        'num' => '12',
                        'group' => 'legal',
                        'title_id' => 'Kunci Scope & Pembayaran DP',
                        'title_en' => 'Scope Lock & DP Payment',
                        'subtitle_id' => 'Segel Integritas SHA-256, Kontrak Digital & Midtrans / Voucher',
                        'subtitle_en' => 'SHA-256 Integrity Seal, Digital Contract & Midtrans / Voucher',
                        'badge' => 'LEGAL & DP',
                    ],
                ];

                $accordionGroupDefs = [
                    'scope' => [
                        'title_id' => 'Ruang Lingkup & Kebutuhan Bisnis',
                        'title_en' => 'Scope & Business Requirements',
                        'desc_id' => 'Spesifikasi discovery, aktor pengguna, dan dekomposisi fitur MVP',
                        'desc_en' => 'Discovery specs, user actors, and MVP feature decomposition',
                        'count' => 3,
                    ],
                    'studio' => [
                        'title_id' => 'Studio Visual & Diagram Arsitektur',
                        'title_en' => 'Visual Studio & Architecture Diagrams',
                        'desc_id' => 'Handoff developer, 4 chart studio, user flowchart, dan skema database ERD',
                        'desc_en' => 'Developer handoff, 4 chart studio, user flow, and database ERD schema',
                        'count' => 4,
                    ],
                    'infra' => [
                        'title_id' => 'Infrastruktur Cloud & AI Database',
                        'title_en' => 'Cloud Infrastructure & AI Database',
                        'desc_id' => 'Evaluasi arsitektur VPS, PostgreSQL strict, Redis cache & backup',
                        'desc_en' => 'VPS architecture evaluation, PostgreSQL strict, Redis cache & backup',
                        'count' => 1,
                    ],
                    'legal' => [
                        'title_id' => 'Investasi, SLA, Kontrak & Pembayaran',
                        'title_en' => 'Investment, SLA, Contracts & Payment',
                        'desc_id' => 'Opsi velocity pricing, timeline sprint, SLA garansi, dan kunci scope DP',
                        'desc_en' => 'Velocity pricing tiers, sprint timeline, SLA warranty, and DP scope lock',
                        'count' => 4,
                    ],
                ];
            @endphp

            <!-- INTERACTIVE ARCHITECTURE INDEX & ACCORDION TABLE OF CONTENTS (QUICK JUMP) -->
            <div class="bg-white dark:bg-zinc-900 border-2 border-emerald-500/70 p-5 sm:p-7 mb-8 rounded-none shadow-sm relative no-print">
                <!-- Header with Title, Accordion Toggle & Search -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5 mb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold tracking-widest uppercase">
                                QUICK JUMP DIRECTORY // SCROLL-SPY ACTIVE
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span x-show="locale === 'en'">PRD Table of Contents &amp; Architecture Index</span>
                            <span x-show="locale !== 'en'">Daftar Isi PRD &amp; Index Navigasi Arsitektur</span>
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-1">
                            <span x-show="locale === 'en'">Click any section below to jump instantly without tedious scrolling. Live indicator tracks your viewport.</span>
                            <span x-show="locale !== 'en'">Klik blok di bawah untuk langsung berpindah ke spesifikasi yang ingin difokuskan tanpa lelah scrolling. Indikator aktif mengikuti posisi layar.</span>
                        </p>
                    </div>

                    <!-- Search & Accordion Controls -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
                        <div class="relative min-w-[200px] flex-1 sm:flex-initial">
                            <input 
                                type="text" 
                                x-model="indexSearchQuery" 
                                placeholder="Cari blok/fitur/ERD..." 
                                class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:outline-none focus:border-emerald-500"
                            />
                            <button 
                                type="button" 
                                x-show="indexSearchQuery" 
                                @click="indexSearchQuery = ''" 
                                class="absolute right-2.5 top-2 text-zinc-400 hover:text-zinc-600 text-xs"
                            >&times;</button>
                        </div>
                        <button 
                            type="button" 
                            @click="expandAllGroups()" 
                            class="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 transition"
                            title="Buka Semua Grup"
                        >
                            <span x-show="locale === 'en'">Expand All</span>
                            <span x-show="locale !== 'en'">Buka Semua</span>
                        </button>
                        <button 
                            type="button" 
                            @click="collapseAllGroups()" 
                            class="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 transition"
                            title="Tutup Semua Grup"
                        >
                            <span x-show="locale === 'en'">Collapse</span>
                            <span x-show="locale !== 'en'">Tutup Semua</span>
                        </button>
                        <button 
                            type="button" 
                            @click="indexAccordionOpen = !indexAccordionOpen" 
                            class="px-3 py-1.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold border border-zinc-900 dark:border-emerald-500 transition flex items-center gap-1.5"
                        >
                            <span x-show="indexAccordionOpen">&blacktriangle; <span x-show="locale === 'en'">Hide Index</span><span x-show="locale !== 'en'">Tutup Panel</span></span>
                            <span x-show="!indexAccordionOpen">&blacktriangledown; <span x-show="locale === 'en'">Show Index</span><span x-show="locale !== 'en'">Buka Panel</span></span>
                        </button>
                    </div>
                </div>

                <!-- Accordion Body -->
                <div x-show="indexAccordionOpen" x-transition.opacity class="space-y-4">
                    @foreach($accordionGroupDefs as $groupKey => $groupDef)
                        <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/40 rounded-none overflow-hidden">
                            <!-- Accordion Group Header -->
                            <button 
                                type="button" 
                                @click="toggleAccordionGroup('{{ $groupKey }}')"
                                class="w-full p-3.5 bg-zinc-100/70 dark:bg-zinc-900/90 hover:bg-zinc-200/60 dark:hover:bg-zinc-800/80 transition flex items-center justify-between text-left font-mono"
                            >
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 h-6 bg-zinc-800 dark:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center rounded-none border border-zinc-700">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                            <span x-show="locale === 'en'">{{ $groupDef['title_en'] }}</span>
                                            <span x-show="locale !== 'en'">{{ $groupDef['title_id'] }}</span>
                                            <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-normal border border-zinc-300 dark:border-zinc-700">
                                                {{ $groupDef['count'] }} <span x-show="locale === 'en'">Sections</span><span x-show="locale !== 'en'">Bagian</span>
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans mt-0.5 hidden sm:block">
                                            <span x-show="locale === 'en'">{{ $groupDef['desc_en'] }}</span>
                                            <span x-show="locale !== 'en'">{{ $groupDef['desc_id'] }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg 
                                        class="w-4 h-4 text-zinc-500 transition-transform duration-200" 
                                        :class="accordionGroups['{{ $groupKey }}'] ? 'rotate-180' : ''" 
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>

                            <!-- Accordion Items Grid -->
                            <div 
                                x-show="accordionGroups['{{ $groupKey }}'] || indexSearchQuery.trim() !== ''" 
                                x-transition 
                                class="p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 font-mono text-xs"
                            >
                                @foreach($blueprintSections as $sec)
                                    @if($sec['group'] === $groupKey)
                                        <div 
                                            x-show="!indexSearchQuery || '{{ strtolower($sec['title_id'] . ' ' . $sec['title_en'] . ' ' . $sec['subtitle_id'] . ' ' . $sec['subtitle_en'] . ' ' . $sec['badge'] . ' ' . $sec['num']) }}'.includes(indexSearchQuery.toLowerCase())"
                                            @click="jumpTo('{{ $sec['id'] }}')"
                                            :class="activeSectionId === '{{ $sec['id'] }}' 
                                                ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400 font-bold shadow-xs' 
                                                : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                            class="p-3 border rounded-none cursor-pointer transition flex flex-col justify-between group relative select-none"
                                        >
                                            <div>
                                                <div class="flex items-center justify-between gap-1 mb-1.5">
                                                    <span class="flex items-center gap-1.5">
                                                        <span 
                                                            :class="activeSectionId === '{{ $sec['id'] }}' ? 'bg-emerald-500 text-black' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                                                            class="w-5 h-5 flex items-center justify-center font-bold text-[10px] rounded-none"
                                                        >
                                                            {{ $sec['num'] }}
                                                        </span>
                                                        <span x-show="activeSectionId === '{{ $sec['id'] }}'" class="flex items-center gap-1 text-[10px] text-emerald-500 font-bold uppercase tracking-wider">
                                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-none animate-ping"></span>
                                                            <span class="hidden sm:inline">ACTIVE</span>
                                                        </span>
                                                    </span>
                                                    <span class="px-1.5 py-0.2 text-[9px] bg-zinc-100 dark:bg-zinc-800/80 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700/60 font-bold">
                                                        {{ $sec['badge'] }}
                                                    </span>
                                                </div>
                                                <div class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-500 transition-colors text-xs leading-snug">
                                                    <span x-show="locale === 'en'">{{ $sec['title_en'] }}</span>
                                                    <span x-show="locale !== 'en'">{{ $sec['title_id'] }}</span>
                                                </div>
                                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 font-sans mt-1 leading-snug line-clamp-2">
                                                    <span x-show="locale === 'en'">{{ $sec['subtitle_en'] }}</span>
                                                    <span x-show="locale !== 'en'">{{ $sec['subtitle_id'] }}</span>
                                                </p>
                                            </div>
                                            <div class="mt-2.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-[10px] text-zinc-400 group-hover:text-emerald-500">
                                                <span><span x-show="locale === 'en'">Jump to section</span><span x-show="locale !== 'en'">Fokuskan blok</span></span>
                                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
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
            <section id="section-1" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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

                <div class="bg-zinc-50 dark:bg-zinc-950 border-l-4 border-emerald-500 p-4 font-sans text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed rounded-none mb-6">
                    <strong class="font-mono uppercase text-xs text-emerald-600 dark:text-emerald-400 block mb-1">Filosofi Arsitektur & Efisiensi Biaya</strong>
                    {{ $prd['executive_summary']['architecture_philosophy'] ?? 'Sistem menggunakan arsitektur Modern Monolith (Laravel 13 & Filament PHP) untuk memangkas biaya server, menjamin isolasi data, dan mempercepat peluncuran fitur hingga 3x lipat.' }}
                </div>

                <!-- 5 CRITICAL GOVERNANCE PARAMETERS GRID (ANTI-DISPUTE SHIELD) -->
                <div class="pt-5 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span x-text="locale === 'en' ? '5 Core Architectural Governance Parameters (Dispute Prevention)' : '5 Pilar Tata Kelola Arsitektur & Anti-Sengketa Klien'">5 Pilar Tata Kelola Arsitektur & Anti-Sengketa Klien</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase font-bold">
                            CONTRACTUAL LOCK
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs font-mono">
                        <!-- 1. Target Platform -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '1. Target Platform & Accessibility' : '1. Target Platform & Aksesibilitas'">1. Target Platform & Aksesibilitas</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->target_platform ?? ($prd['executive_summary']['target_platform'] ?? 'Responsive Modern Web & PWA') }}
                            </span>
                        </div>

                        <!-- 2. Legacy Data Migration -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '2. Legacy Data Migration Scope' : '2. Migrasi Data Warisan'">2. Migrasi Data Warisan</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->migrasi_data ?? ($prd['executive_summary']['legacy_data_migration'] ?? 'Database Baru Bersih (Clean Start)') }}
                            </span>
                        </div>

                        <!-- 3. Hosting Infrastructure -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '3. Server & Hosting Infrastructure' : '3. Infrastruktur Hosting & Server'">3. Infrastruktur Hosting & Server</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->preferensi_hosting ?? ($prd['executive_summary']['hosting_infrastructure'] ?? 'Managed Cloud VPS Neriah Pro') }}
                            </span>
                        </div>

                        <!-- 4. Warranty & SLA -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '4. Warranty, SLA & Git Handover' : '4. Garansi, SLA & Serah Terima Git'">4. Garansi, SLA & Serah Terima Git</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->garansi_sla ?? ($prd['executive_summary']['warranty_sla'] ?? '30 Hari Garansi Bug + Transfer Repo Git') }}
                            </span>
                        </div>

                        <!-- 5. Payment Milestones -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition sm:col-span-2 lg:col-span-2">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '5. Payment Milestone Schedule' : '5. Skema Termin Pembayaran'">5. Skema Termin Pembayaran</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 block text-xs leading-snug">
                                {{ $blueprint->user_metadata['termin_pembayaran'] ?? ($prd['executive_summary']['payment_milestones'] ?? 'Termin 1 (50% DP Kickoff) + Termin 2 (50% Pelunasan setelah UAT Lolos)') }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: PENGGUNA & HAK AKSES (RBAC) -->
            <section id="section-2" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
            <section id="section-3" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
                            @php
                                $featKey = $spec['feature_id'] ?? ($spec['id'] ?? ('FEAT-SPEC-' . $specIdx));
                            @endphp
                            <div 
                                id="card-feat-{{ $specIdx }}"
                                x-data="{ specTab: &apos;gherkin&apos;, expanded: true }" 
                                class="bg-zinc-50 dark:bg-zinc-950 border-2 rounded-none transition scroll-mt-24"
                                :class="isStepCompleted('{{ $featKey }}') ? 'border-emerald-500/80 shadow-xs' : 'border-zinc-200 dark:border-zinc-800'"
                            >
                                <!-- Feature Spec Header Bar -->
                                <div class="p-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start sm:items-center gap-3">
                                        <span class="px-2 py-0.5 font-mono font-bold text-xs uppercase tracking-wider" :class="isStepCompleted('{{ $featKey }}') ? 'bg-emerald-500 text-black' : 'bg-zinc-900 dark:bg-zinc-800 text-white'">
                                            {{ $featKey }}
                                        </span>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight">{{ $spec['title'] ?? 'Spesifikasi Fitur' }}</h3>
                                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                    {{ $spec['category'] ?? 'CORE DOMAIN' }}
                                                </span>
                                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold">
                                                    {{ $spec['complexity_label'] ?? 'Standard (3 SP)' }}
                                                </span>
                                                <span class="px-2 py-0.5 bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30 text-[10px] font-mono font-bold">
                                                    {{ $spec['sprint_phase'] ?? 'Sprint 1' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 no-print self-end sm:self-auto">
                                        <!-- Step Verification Toggle in Feature Card -->
                                        <button 
                                            type="button" 
                                            @click="toggleStepCompleted('{{ $featKey }}', '{{ addslashes($spec['title'] ?? '') }}')"
                                            :class="isStepCompleted('{{ $featKey }}') ? 'bg-emerald-500 text-black border-emerald-500 font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 border-zinc-300 dark:border-zinc-700'"
                                            class="px-2.5 py-1 text-xs font-mono border flex items-center gap-1.5 transition rounded-none"
                                            title="Tandai Status Pengerjaan Fitur Ini di Cockpit Sprint"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted('{{ $featKey }}') ? 'bg-black' : 'bg-zinc-400'"></span>
                                            <span x-text="isStepCompleted('{{ $featKey }}') ? '✓ VERIFIED & COMMITTED' : 'TANDAI SELESAI'"></span>
                                        </button>

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
                                            <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
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

                                    @if(!empty($spec['target_files']))
                                        <!-- Bounded Target Files for AI Agents -->
                                        <div class="p-3 bg-zinc-100 dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800 font-mono text-xs mb-4">
                                            <div class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                <span>BOUNDED TARGET FILES (ISOLASI RUANG LINGKUP AGENT):</span>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($spec['target_files'] as $tf)
                                                    <span class="px-2 py-0.5 bg-white dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 text-[11px] border border-zinc-300 dark:border-zinc-800 select-all font-mono">
                                                        {{ $tf }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

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
                <!-- SECTION 3.5: PUSAT ORKESTRASI & SPRINT COCKPIT AI CODING AGENT -->
            <section id="section-3-5" class="bg-white dark:bg-zinc-900 border-2 border-emerald-500/50 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">&para;</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Pusat Orkestrasi AI Agent: Strategi Vertical Slice &amp; Interactive Sprint Cockpit</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Panduan taktis membimbing AI Agent (Cursor Composer, Claude Code CLI, Windsurf Cascade, Devin, Antigravity IDE) membaca PRD per fitur vertikal secara terpandu sampai tuntas.</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        VERTICAL SLICE ENGINE // O(1) CONTEXT
                    </span>
                </div>

                <!-- Executive Warning & Rationale Against Prompt Dumping -->
                <div class="p-5 bg-zinc-950 border border-zinc-800 font-mono text-xs mb-6 text-zinc-300 space-y-4">
                    <div class="border-b border-zinc-800 pb-3">
                        <div class="text-amber-400 font-bold uppercase text-xs sm:text-sm tracking-wide mb-1">
                            Strategi Orkestrasi AI: Apakah PRD Diberikan Sekaligus atau Sedikit demi Sedikit?
                        </div>
                        <div class="p-2.5 bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 font-black text-xs sm:text-sm uppercase tracking-tight mt-2">
                            🚨 JAWABAN TEGAS: JANGAN PERNAH MEMBERIKAN SELURUH DOKUMEN PRD SEKALIGUS DALAM SATU PROMPT KODING!
                        </div>
                    </div>

                    <p class="font-sans text-xs text-zinc-300 leading-relaxed">
                        Memberikan seluruh dokumen PRD (ribuan baris) ke dalam jendela obrolan AI yang sedang mengedit kode aktif adalah <strong>kesalahan paling fatal</strong> yang sering dilakukan developer. Ini adalah penyebab nomor satu mengapa kode menjadi berantakan, amnesia migrasi, dan banyak file terhapus secara tidak sengaja.
                    </p>

                    <!-- 4 Technical Breakdown Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                        <div class="p-3 bg-zinc-900/80 border border-zinc-800">
                            <span class="text-rose-400 font-bold block mb-1">1. Attention Drift &amp; Context Rot:</span>
                            <p class="font-sans text-[11px] text-zinc-400 leading-relaxed">
                                Walaupun model AI modern memiliki context window besar (200K hingga 2M token), kemampuan penalaran logika menurun seiring bertambahnya token. AI akan mengalami <em>instruction dilution</em> (mengabaikan aturan-aturan kecil di tengah dokumen).
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-900/80 border border-zinc-800">
                            <span class="text-amber-400 font-bold block mb-1">2. Shallow Code &amp; Mock Implementation:</span>
                            <p class="font-sans text-[11px] text-zinc-400 leading-relaxed">
                                Jika AI diminta mengimplementasikan 10 fitur sekaligus, AI akan kehabisan token output. Akibatnya, AI mulai memotong kode, meninggalkan komentar berbahaya seperti <code class="text-amber-300">// TODO: implement logic here</code>, atau membuat fungsi dummy/mock yang tidak bekerja.
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-900/80 border border-zinc-800">
                            <span class="text-sky-400 font-bold block mb-1">3. Amnesia Migrasi &amp; Regresi:</span>
                            <p class="font-sans text-[11px] text-zinc-400 leading-relaxed">
                                AI akan lupa relasi foreign key dari modul yang dibuat 5 menit lalu dan membuat duplikasi fungsi yang memecah kode sebelumnya.
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-900/80 border border-zinc-800">
                            <span class="text-emerald-400 font-bold block mb-1">4. Audit Diff yang Mustahil:</span>
                            <p class="font-sans text-[11px] text-zinc-400 leading-relaxed">
                                Jika 1 prompt menghasilkan perubahan pada 40 file sekaligus, Anda sebagai manusia tidak akan bisa mereview bug secara teliti sebelum menekan Accept All.
                            </p>
                        </div>
                    </div>

                    <!-- ASCII Flowchart Diagram -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-zinc-400 mb-1.5">
                            <span class="text-emerald-400 font-bold uppercase text-[11px]">Metodologi Terbaik: &quot;Vertical Slice Prompting&quot; (Per Fitur Vertikal)</span>
                            <span class="text-[10px] text-zinc-500 font-mono">STANDAR EMAS NERIAH PRO</span>
                        </div>
                        <pre class="bg-black p-4 text-[11px] text-emerald-400/95 overflow-x-auto select-all leading-relaxed whitespace-pre font-mono border border-zinc-800">+---------------------------------------------------------------------------------+
|                        ALUR KERJA ORKESTRASI AI AGENT                           |
+---------------------------------------------------------------------------------+
|                                                                                 |
|  [ LANGKAH 1: FONDASI GLOBAL (1 Kali di Awal) ]                                 |
|  - Input ke AI: Bab 5 (ERD Schema), Bab 5.6 (Sync Spec), Bab 6 (Tech Stack)     |
|  - Instruksi AI: &quot;Buat migrasi database, model ULID, dan setup base project&quot;    |
|  - Verifikasi: Jalankan `php artisan migrate` -&gt; Commit Git                     |
|                                                                                 |
|  [ LANGKAH 2: EKSEKUSI PER FITUR (Iterasi Berulang) ]                           |
|  - Buka kartu fitur PRD (misal: FEAT-MVP-01)                                    |
|  - Klik tombol &quot;Salin Prompt Handoff AI Code Agent&quot; yang sudah tersedia         |
|  - Paste ke Cursor / Claude Code / Antigravity                                  |
|  - AI hanya bekerja di 3-4 file yang ditentukan (Model -&gt; Controller -&gt; UI)     |
|  - Verifikasi: Jalankan `php artisan test` -&gt; Commit Git                        |
|                                                                                 |
|  [ LANGKAH 3: FITUR SELANJUTNYA ]                                               |
|  - Ambil kartu fitur berikutnya (FEAT-MVP-02)                                   |
|  - Ulangi Langkah 2                                                             |
|                                                                                 |
+---------------------------------------------------------------------------------+</pre>
                    </div>

                    <!-- 3 Benefits Highlights -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1 text-[11px]">
                        <div class="p-2.5 bg-emerald-950/40 border border-emerald-500/30 text-emerald-300">
                            <strong class="block text-emerald-400 font-bold mb-0.5">&check; Zero Context-Rot:</strong>
                            <span class="text-zinc-300">AI fokus 100% pada satu masalah spesifik dalam batasan file yang ketat.</span>
                        </div>
                        <div class="p-2.5 bg-sky-950/40 border border-sky-500/30 text-sky-300">
                            <strong class="block text-sky-400 font-bold mb-0.5">&check; Kualitas Kode Penuh:</strong>
                            <span class="text-zinc-300">Tidak ada pemotongan kode atau // TODO. AI menuliskan validasi lengkap.</span>
                        </div>
                        <div class="p-2.5 bg-amber-950/40 border border-amber-500/30 text-amber-300">
                            <strong class="block text-amber-400 font-bold mb-0.5">&check; Troubleshooting Mudah:</strong>
                            <span class="text-zinc-300">Jika error, Anda tahu persis modul mana yang bermasalah. Riwayat Git rapi per fitur.</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-zinc-400 italic pt-1 border-t border-zinc-800">
                        &quot;Di dalam halaman <code class="text-emerald-400">show.blade.php</code> pada setiap kartu fitur (Bab 3), tim kami telah menyediakan tombol <strong>&apos;Salin Prompt Handoff AI Code Agent&apos;</strong> yang siap Anda gunakan untuk disalin ke AI Agent per fitur secara terpandu.&quot;
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- INTERACTIVE SPRINT EXECUTION COCKPIT (THE LIVE STEP-BY-STEP ORCHESTRATOR) -->
                <!-- ========================================================================= -->
                <div class="bg-zinc-950 border-2 border-emerald-500/60 p-5 sm:p-6 mb-8 rounded-none font-mono text-xs">
                    <!-- Cockpit Progress Top Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500 inline-block animate-pulse"></span>
                            <span class="font-bold text-white uppercase text-xs tracking-wider">COCKPIT PELAKSANAAN SPRINT AI DEVELOPER</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-emerald-400 font-bold">
                                PROGRES SPRINT: <span x-text="getDevCompletedCount()"></span> / {{ $totalDevSteps }} TAHAP SELESAI (<span x-text="getDevProgressPercentage()"></span>%)
                            </span>
                            <button 
                                type="button" 
                                @click="resetDevProgress()" 
                                class="text-[10px] text-zinc-500 hover:text-rose-400 underline transition"
                                title="Reset data pengerjaan sprint lokal"
                            >
                                Reset Progres
                            </button>
                        </div>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="w-full bg-zinc-900 border border-zinc-800 h-2.5 overflow-hidden mb-5">
                        <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-sky-400 h-full transition-all duration-300" :style="&apos;width: &apos; + getDevProgressPercentage() + &apos;%&apos;"></div>
                    </div>

                    <!-- Step Selection Tabs (Horizontal Scrollable) -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-3 mb-5 border-b border-zinc-800 scrollbar-thin">
                        <!-- Step 0: Fondasi Global -->
                        <button 
                            type="button" 
                            @click="devActiveStep = 0"
                            :class="devActiveStep === 0 ? &apos;bg-emerald-500 text-black font-bold border-emerald-400&apos; : (isStepCompleted(&apos;step_foundation&apos;) ? &apos;bg-zinc-900 text-emerald-400 border-emerald-500/40&apos; : &apos;bg-zinc-900 text-zinc-400 border-zinc-800 hover:text-zinc-200&apos;)"
                            class="px-3 py-1.5 border text-xs whitespace-nowrap flex items-center gap-1.5 transition shrink-0"
                        >
                            <span x-show="isStepCompleted(&apos;step_foundation&apos;)" class="text-xs">&check;</span>
                            <span>01. FONDASI GLOBAL</span>
                        </button>

                        <!-- Step 1..N: Each MVP Feature -->
                        @foreach($mvpEngineeringSpecs as $specIdx => $spec)
                            @php
                                $sKey = $spec['feature_id'] ?? ($spec['id'] ?? ('FEAT-SPEC-' . $specIdx));
                            @endphp
                            <button 
                                type="button" 
                                @click="devActiveStep = {{ $specIdx + 1 }}"
                                :class="devActiveStep === {{ $specIdx + 1 }} ? &apos;bg-emerald-500 text-black font-bold border-emerald-400&apos; : (isStepCompleted(&apos;{{ $sKey }}&apos;) ? &apos;bg-zinc-900 text-emerald-400 border-emerald-500/40&apos; : &apos;bg-zinc-900 text-zinc-400 border-zinc-800 hover:text-zinc-200&apos;)"
                                class="px-3 py-1.5 border text-xs whitespace-nowrap flex items-center gap-1.5 transition shrink-0"
                            >
                                <span x-show="isStepCompleted(&apos;{{ $sKey }}&apos;)" class="text-xs">&check;</span>
                                <span>{{ sprintf('%02d', $specIdx + 2) }}. {{ $spec['id'] ?? ('FITUR ' . ($specIdx + 1)) }}</span>
                            </button>
                        @endforeach

                        <!-- Step Final: Quality Gate & Deploy -->
                        <button 
                            type="button" 
                            @click="devActiveStep = {{ $totalDevSteps - 1 }}"
                            :class="devActiveStep === {{ $totalDevSteps - 1 }} ? &apos;bg-emerald-500 text-black font-bold border-emerald-400&apos; : (isStepCompleted(&apos;step_deployment&apos;) ? &apos;bg-zinc-900 text-emerald-400 border-emerald-500/40&apos; : &apos;bg-zinc-900 text-zinc-400 border-zinc-800 hover:text-zinc-200&apos;)"
                            class="px-3 py-1.5 border text-xs whitespace-nowrap flex items-center gap-1.5 transition shrink-0"
                        >
                            <span x-show="isStepCompleted(&apos;step_deployment&apos;)" class="text-xs">&check;</span>
                            <span>{{ sprintf('%02d', $totalDevSteps) }}. QUALITY GATE &amp; DEPLOY</span>
                        </button>
                    </div>

                    <!-- ============================================ -->
                    <!-- ACTIVE STEP DETAIL DISPLAY PANE -->
                    <!-- ============================================ -->
                    
                    <!-- PANE 0: LANGKAH 1 - FONDASI GLOBAL (1 Kali di Awal) -->
                    <div x-show="devActiveStep === 0" class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800 pb-3">
                            <div>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">TAHAP 01 // FONDASI ARSITEKTUR</span>
                                <h3 class="text-sm sm:text-base font-black text-white mt-1 uppercase">LANGKAH 1: FONDASI GLOBAL &amp; SETUP BASIS DATA (1 KALI DI AWAL)</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleStepCompleted(&apos;step_foundation&apos;, &apos;Langkah 1: Fondasi Global&apos;)"
                                    :class="isStepCompleted(&apos;step_foundation&apos;) ? &apos;bg-emerald-500 text-black font-bold border-emerald-500&apos; : &apos;bg-zinc-900 text-zinc-300 border-zinc-700 hover:border-emerald-500&apos;"
                                    class="px-3 py-1.5 border text-xs font-mono flex items-center gap-1.5 transition"
                                >
                                    <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted(&apos;step_foundation&apos;) ? &apos;bg-black&apos; : &apos;bg-zinc-500&apos;"></span>
                                    <span x-text="isStepCompleted(&apos;step_foundation&apos;) ? &apos;&check; TAHAP 1 TERVERIFIKASI &amp; COMMITTED&apos; : &apos;TANDAI TAHAP 1 SELESAI&apos;"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Target Context Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="p-3 bg-zinc-900 border border-zinc-800">
                                <span class="text-zinc-500 text-[10px] block font-bold mb-0.5">DOKUMEN INPUT KE AI:</span>
                                <span class="text-zinc-200 text-xs font-bold block">Bab 5 (ERD PostgreSQL ULID)</span>
                                @if($hasSync)
                                    <span class="text-emerald-400 text-[10px] block mt-1">+ Bab 5.6 (Sync Engine Spec)</span>
                                @endif
                                <span class="text-sky-400 text-[10px] block mt-0.5">+ Bab 6 (Tech Stack &amp; VPS)</span>
                            </div>
                            <div class="p-3 bg-zinc-900 border border-zinc-800">
                                <span class="text-zinc-500 text-[10px] block font-bold mb-0.5">TARGET BOUNDED FILES:</span>
                                <span class="text-zinc-300 text-[11px] block font-mono">database/migrations/*_create_*.php</span>
                                <span class="text-zinc-300 text-[11px] block font-mono">app/Models/*.php</span>
                            </div>
                            <div class="p-3 bg-zinc-900 border border-zinc-800">
                                <span class="text-zinc-500 text-[10px] block font-bold mb-0.5">TERMINAL VERIFICATION GATE:</span>
                                <code class="text-emerald-400 text-[11px] block font-mono select-all">php artisan migrate:status</code>
                                <code class="text-amber-400 text-[11px] block font-mono select-all">git commit -m &quot;chore(db): setup ULID migrations&quot;</code>
                            </div>
                        </div>

                        <!-- Prompt Pre-Crafted Box -->
                        <div class="p-3.5 bg-zinc-900/90 border border-zinc-800 space-y-2">
                            <div class="flex items-center justify-between text-zinc-400">
                                <span class="text-emerald-400 font-bold uppercase text-[11px]">Prompt Fondasi Global Siap Di-Paste ke AI Agent:</span>
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        onclick="copyCockpitStepPrompt(this, &apos;prompt-step-foundation&apos;)"
                                        class="px-2.5 py-1 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition"
                                    >
                                        SALIN PROMPT FONDASI (LANGKAH 1)
                                    </button>
                                </div>
                            </div>
                            <pre id="prompt-step-foundation" class="bg-black p-3 text-[11px] text-emerald-300/90 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap font-mono">{{ $foundationPrompt }}</pre>
                        </div>

                        <!-- Action Next Footer -->
                        <div class="flex items-center justify-between pt-2 border-t border-zinc-800">
                            <button 
                                type="button" 
                                @click="jumpTo(&apos;section-5&apos;)" 
                                class="text-xs text-sky-400 hover:text-sky-300 underline font-mono flex items-center gap-1"
                            >
                                <span>&rarr; Lihat Skema ERD di Bab 5</span>
                            </button>
                            <button 
                                type="button" 
                                @click="if (!isStepCompleted(&apos;step_foundation&apos;)) { toggleStepCompleted(&apos;step_foundation&apos;, &apos;Langkah 1: Fondasi Global&apos;); } devActiveStep = 1;" 
                                class="px-4 py-2 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition flex items-center gap-2"
                            >
                                <span>Lanjut ke Langkah 2 (Fitur MVP 01)</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- PANES 1..N: LANGKAH 2 - EKSEKUSI PER FITUR VERTICAL SLICE -->
                    @foreach($mvpEngineeringSpecs as $specIdx => $spec)
                        @php
                            $stepNum = $specIdx + 1;
                            $featKey = $spec['feature_id'] ?? ($spec['id'] ?? ('FEAT-SPEC-' . $specIdx));
                            $featTitle = $spec['title'] ?? ('Fitur ' . $stepNum);
                            $cleanSlug = Str::studly(Str::slug($featTitle));
                            $testCmd = 'php artisan test --filter=' . $cleanSlug . 'Test';
                            $promptCodeId = 'prompt-step-feat-' . $specIdx;
                        @endphp
                        <div x-show="devActiveStep === {{ $stepNum }}" class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800 pb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">TAHAP 02.{{ sprintf('%02d', $stepNum) }} // VERTICAL SLICE</span>
                                        <span class="px-2 py-0.5 bg-zinc-800 text-zinc-300 text-[10px]">{{ $spec['category'] ?? 'CORE DOMAIN' }}</span>
                                        <span class="px-2 py-0.5 bg-sky-500/20 text-sky-300 text-[10px]">{{ $spec['complexity_label'] ?? 'Standard' }}</span>
                                    </div>
                                    <h3 class="text-sm sm:text-base font-black text-white uppercase">{{ $featKey }}: {{ $featTitle }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="toggleStepCompleted(&apos;{{ $featKey }}&apos;, &apos;{{ addslashes($featTitle) }}&apos;)"
                                        :class="isStepCompleted(&apos;{{ $featKey }}&apos;) ? &apos;bg-emerald-500 text-black font-bold border-emerald-500&apos; : &apos;bg-zinc-900 text-zinc-300 border-zinc-700 hover:border-emerald-500&apos;"
                                        class="px-3 py-1.5 border text-xs font-mono flex items-center gap-1.5 transition"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted(&apos;{{ $featKey }}&apos;) ? &apos;bg-black&apos; : &apos;bg-zinc-500&apos;"></span>
                                        <span x-text="isStepCompleted(&apos;{{ $featKey }}&apos;) ? &apos;&check; FITUR TERVERIFIKASI &amp; COMMITTED&apos; : &apos;TANDAI FITUR SELESAI&apos;"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Target Bounded Files & User Story -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                                    <span class="text-amber-400 text-[10px] block font-bold">BOUNDED TARGET FILES (ISOLASI FILE AGENT):</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($spec['target_files'] ?? [] as $tf)
                                            <span class="px-2 py-0.5 bg-black text-zinc-300 text-[10px] font-mono border border-zinc-800 select-all">
                                                {{ $tf }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                                    <span class="text-zinc-500 text-[10px] block font-bold">USER STORY:</span>
                                    <p class="font-sans text-[11px] text-zinc-300 italic leading-relaxed">
                                        &quot;{{ $spec['user_story'] ?? 'Pengguna dapat menjalankan alur kerja ini dengan aman dan tervalidasi.' }}&quot;
                                    </p>
                                    <div class="pt-1 border-t border-zinc-800 flex items-center justify-between text-[10px]">
                                        <span class="text-zinc-500">VERIFIKASI TEST:</span>
                                        <code class="text-emerald-400 font-mono select-all">{{ $testCmd }}</code>
                                    </div>
                                </div>
                            </div>

                            <!-- Pre-Crafted Vertical Slice Prompt -->
                            <div class="p-3.5 bg-zinc-900/90 border border-zinc-800 space-y-2">
                                <div class="flex items-center justify-between text-zinc-400">
                                    <span class="text-emerald-400 font-bold uppercase text-[11px]">Prompt Directive Siap Di-Paste ke Cursor / Claude Code / Antigravity:</span>
                                    <button 
                                        type="button" 
                                        onclick="copyCockpitStepPrompt(this, &apos;{{ $promptCodeId }}&apos;)"
                                        class="px-2.5 py-1 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition"
                                    >
                                        SALIN PROMPT FITUR INI
                                    </button>
                                </div>
                                <pre id="{{ $promptCodeId }}" class="bg-black p-3 text-[11px] text-emerald-300/90 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap font-mono">{{ $spec['code_agent_directive'] ?? ($spec['agent_directive_prompt'] ?? '') }}</pre>
                            </div>

                            <!-- Footer Nav -->
                            <div class="flex items-center justify-between pt-2 border-t border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <button 
                                        type="button" 
                                        @click="devActiveStep = {{ $stepNum - 1 }}" 
                                        class="text-xs text-zinc-400 hover:text-zinc-200 underline font-mono"
                                    >
                                        &larr; Tahap Sebelumnya
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="jumpTo(&apos;card-feat-{{ $specIdx }}&apos;)" 
                                        class="text-xs text-sky-400 hover:text-sky-300 underline font-mono"
                                    >
                                        &loz; Lihat Kartu Spesifikasi Lengkap (Bab 3)
                                    </button>
                                </div>
                                <button 
                                    type="button" 
                                    @click="if (!isStepCompleted(&apos;{{ $featKey }}&apos;)) { toggleStepCompleted(&apos;{{ $featKey }}&apos;, &apos;{{ addslashes($featTitle) }}&apos;); } devActiveStep = {{ $stepNum < count($mvpEngineeringSpecs) ? ($stepNum + 1) : ($totalDevSteps - 1) }};" 
                                    class="px-4 py-2 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition flex items-center gap-2"
                                >
                                    <span>Tandai Selesai &amp; Lanjut</span>
                                    <span>&rarr;</span>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <!-- PANE LAST: LANGKAH AKHIR - AUTOMATED QUALITY GATE & DEPLOYMENT -->
                    <div x-show="devActiveStep === {{ $totalDevSteps - 1 }}" class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800 pb-3">
                            <div>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">TAHAP AKHIR // PRODUCTION VERIFICATION</span>
                                <h3 class="text-sm sm:text-base font-black text-white mt-1 uppercase">LANGKAH 3: AUTOMATED QUALITY GATE &amp; PRODUCTION DEPLOYMENT</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleStepCompleted(&apos;step_deployment&apos;, &apos;Langkah Akhir: Quality Gate &amp; Deployment&apos;)"
                                    :class="isStepCompleted(&apos;step_deployment&apos;) ? &apos;bg-emerald-500 text-black font-bold border-emerald-500&apos; : &apos;bg-zinc-900 text-zinc-300 border-zinc-700 hover:border-emerald-500&apos;"
                                    class="px-3 py-1.5 border text-xs font-mono flex items-center gap-1.5 transition"
                                >
                                    <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted(&apos;step_deployment&apos;) ? &apos;bg-black&apos; : &apos;bg-zinc-500&apos;"></span>
                                    <span x-text="isStepCompleted(&apos;step_deployment&apos;) ? &apos;&check; SELURUH SPRINT PRODUCTION READY&apos; : &apos;TANDAI QUALITY GATE SELESAI&apos;"></span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Quality Gate Verification Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                                <span class="text-emerald-400 font-bold block">1. AUTOMATED TESTS</span>
                                <code class="text-zinc-300 block text-[11px] font-mono select-all">php artisan test</code>
                                <p class="text-[10px] text-zinc-400">Pastikan 100% assertions lulus dengan exit code 0.</p>
                            </div>
                            <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                                <span class="text-sky-400 font-bold block">2. ASSET BUNDLE</span>
                                <code class="text-zinc-300 block text-[11px] font-mono select-all">npm run build</code>
                                <p class="text-[10px] text-zinc-400">Sinkronisasi public/build/manifest.json untuk produksi.</p>
                            </div>
                            <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                                <span class="text-amber-400 font-bold block">3. GIT SYNC</span>
                                <code class="text-zinc-300 block text-[11px] font-mono select-all">git push origin main</code>
                                <p class="text-[10px] text-zinc-400">Sinkronkan seluruh commit riwayat per fitur ke repository.</p>
                            </div>
                            <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                                <span class="text-rose-400 font-bold block">4. DEPLOY SCRIPT</span>
                                <code class="text-zinc-300 block text-[11px] font-mono select-all">./deploy.sh 6</code>
                                <p class="text-[10px] text-zinc-400">Skenario 6 (Assets) atau Skenario 2 (Migrasi Aman).</p>
                            </div>
                        </div>

                        <!-- Quality Gate Completion Banner -->
                        <div class="p-4 bg-emerald-950/30 border border-emerald-500/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-emerald-400 font-bold text-xs uppercase block">STATUS FINAL SPRINT:</span>
                                <p class="text-zinc-300 text-xs font-sans mt-0.5">
                                    Ketika seluruh tahapan telah terverifikasi, aplikasi siap diserahterimakan kepada klien dengan garansi integritas 100% bebas amnesia arsitektur.
                                </p>
                            </div>
                            <button 
                                type="button" 
                                @click="jumpTo(&apos;section-10&apos;)" 
                                class="px-4 py-2 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition shrink-0 uppercase"
                            >
                                Kunci Scope &amp; Serah Terima &rarr;
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tool IDE Selector Tabs (Cursor, Claude Code, Windsurf, Devin, Antigravity) -->
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        <span>PLAYBOOK PER TOOL IDE / TERMINAL (PILIH TOOL ANDA):</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5 font-mono text-xs mb-4">
                        <button 
                            type="button" 
                            @click="selectedIdeTool = &apos;antigravity_ide&apos;" 
                            :class="selectedIdeTool === &apos;antigravity_ide&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400&apos;"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Google Antigravity IDE</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = &apos;cursor&apos;" 
                            :class="selectedIdeTool === &apos;cursor&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400&apos;"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Cursor Composer (Cmd+I)</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = &apos;claude&apos;" 
                            :class="selectedIdeTool === &apos;claude&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400&apos;"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Claude Code CLI</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = &apos;windsurf&apos;" 
                            :class="selectedIdeTool === &apos;windsurf&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400&apos;"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Windsurf Cascade</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = &apos;devin&apos;" 
                            :class="selectedIdeTool === &apos;devin&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400&apos;"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Devin &amp; Copilot Workspace</span>
                        </button>
                    </div>

                    <!-- Antigravity IDE Guide -->
                    <div x-show="selectedIdeTool === &apos;antigravity_ide&apos;" class="p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-300 border-b border-zinc-800 pb-2">
                            <span class="font-bold text-emerald-400 uppercase">1. CARA MENGGUNAKAN DI GOOGLE DEEPMIND ANTIGRAVITY IDE:</span>
                            <span class="text-[10px] text-zinc-500">Autonomous Agentic Coding</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-400 text-xs font-sans">
                            <li>Buka workspace di Antigravity IDE. Pastikan file <code class="text-emerald-400">.agents/AGENTS.md</code> dan Ponytail Decision Ladder aktif.</li>
                            <li>Buka tab <strong>Cockpit Pelaksanaan Sprint</strong> di atas, pilih tahap yang sedang berjalan (Langkah 1 Fondasi atau Fitur spesifik).</li>
                            <li>Klik tombol <strong>SALIN PROMPT</strong>, lalu tempelkan ke prompt bar Antigravity IDE.</li>
                            <li>AI akan memproses diff secara terisolasi pada target files bounded, menjalankan verifikasi pengujian terminal, dan melaporkan ringkasan perubahan.</li>
                        </ol>
                    </div>

                    <!-- Cursor Composer Tool Guide -->
                    <div x-show="selectedIdeTool === &apos;cursor&apos;" x-cloak class="p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-300 border-b border-zinc-800 pb-2">
                            <span class="font-bold text-emerald-400 uppercase">2. CARA MENGGUNAKAN DI CURSOR COMPOSER (Cmd+I):</span>
                            <span class="text-[10px] text-zinc-500">Shortcut: Cmd+I (Mac) / Ctrl+I (Win)</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-400 text-xs font-sans">
                            <li>Buka Cursor Composer dengan menekan <code class="text-emerald-400">Cmd+I</code>.</li>
                            <li>Ketik simbol <code class="text-cyan-400">@</code> untuk melampirkan file yang menjadi batas target modul (lihat <em>BOUNDED TARGET FILES</em> pada kartu fitur).</li>
                            <li>Salin <strong>PROMPT FITUR</strong> dari Cockpit Pelaksanaan Sprint di atas, lalu tempel ke Composer.</li>
                            <li>Tekan Enter, tinjau perubahan diff baris per baris, dan jalankan perintah verifikasi terminal <code class="text-amber-400">php artisan test --filter=...</code> sebelum menekan Accept All.</li>
                        </ol>
                    </div>

                    <!-- Claude Code CLI Tool Guide -->
                    <div x-show="selectedIdeTool === &apos;claude&apos;" x-cloak class="p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-300 border-b border-zinc-800 pb-2">
                            <span class="font-bold text-cyan-400 uppercase">3. CARA MENGGUNAKAN DI CLAUDE CODE CLI (claude):</span>
                            <span class="text-[10px] text-zinc-500">Terminal Command</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-400 text-xs font-sans">
                            <li>Jalankan perintah <code class="text-cyan-400">claude</code> pada terminal root direktori proyek.</li>
                            <li>Beri perintah terpandu dengan prompt dari Cockpit: <code class="text-emerald-400 select-all">claude &quot;[Tempel prompt fitur dari Cockpit di sini]&quot;</code></li>
                            <li>Biarkan Claude Code membaca file bounded, mengeksekusi diff, dan menjalankan loop pengujian terminal secara otonom.</li>
                        </ol>
                    </div>

                    <!-- Windsurf Cascade Tool Guide -->
                    <div x-show="selectedIdeTool === &apos;windsurf&apos;" x-cloak class="p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-300 border-b border-zinc-800 pb-2">
                            <span class="font-bold text-sky-400 uppercase">4. CARA MENGGUNAKAN DI WINDSURF CASCADE:</span>
                            <span class="text-[10px] text-zinc-500">Flow-Based Agent</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-400 text-xs font-sans">
                            <li>Buka panel Cascade di Windsurf dan aktifkan mode <strong>Agentic Write</strong>.</li>
                            <li>Tempelkan prompt dari Cockpit Pelaksanaan Sprint. Pastikan batasan target file terkunci.</li>
                            <li>Pantau cascade flow hingga build sukses dan verifikasi tes lolos.</li>
                        </ol>
                    </div>

                    <!-- Devin & Copilot Tool Guide -->
                    <div x-show="selectedIdeTool === &apos;devin&apos;" x-cloak class="p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-300 border-b border-zinc-800 pb-2">
                            <span class="font-bold text-amber-400 uppercase">5. CARA MENGGUNAKAN DI DEVIN &amp; GITHUB COPILOT:</span>
                            <span class="text-[10px] text-zinc-500">Autonomous Agent / Workspace</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-400 text-xs font-sans">
                            <li>Buat issue/task baru dengan judul ID Fitur (cth: FEAT-MVP-01).</li>
                            <li>Salin User Story dan seluruh tabel skenario Gherkin (Given-When-Then) dari Cockpit ke dalam task description.</li>
                            <li>Biarkan Devin / Copilot menyelesaikan issue dan membuka Pull Request terisolasi.</li>
                        </ol>
                    </div>
                </div>

                <!-- 3 Pillars of Engineering Manifesto -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono text-xs mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <!-- 1. Frontend Anti-AI-Slop -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-emerald-500"></span>
                                <h3>1. FRONTEND: ANTI AI-SLOP UI/UX</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Palet Kurasi:</strong> Base Zinc monokrom dengan aksen tajam Emerald &amp; Amber. Zero generic pastel.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Zero Native Popups:</strong> Dilarang keras <code>alert()</code> atau <code>confirm()</code>. Gunakan Floating Toast &amp; Modal.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Sudut Tipis:</strong> Border radius halus (<code>rounded-none/sm</code>), dilarang tombol kapsul <code>rounded-full</code>.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2. Backend Scalability Manifesto -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sky-600 dark:text-sky-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-sky-500"></span>
                                <h3>2. BACKEND: ENTERPRISE SCALABILITY</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Strict ULID Primary Keys:</strong> Gunakan <code>ulid(&apos;id&apos;)</code> (VARCHAR(26)). Hindari AUTO_INCREMENT.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Keyset Cursor Pagination O(1):</strong> Hindari <code>paginate()</code> OFFSET. Wajib gunakan <code>cursorPaginate()</code>.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Atomic Transactions:</strong> Enkapsulasi logika mutasi dalam Single Action Class di dalam <code>DB::transaction()</code>.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3. Agent Handoff & Anti Context-Rot Protocol -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-amber-500"></span>
                                <h3>3. INTEGRASI: ZERO CONTEXT-ROT</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>One-Feature-At-A-Time:</strong> Salin prompt per fitur vertikal, bukan seluruh PRD.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Bounded File Scoping:</strong> Batasi target file pada prompt agar AI tidak merusak file lain.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Terminal Verification:</strong> Wajibkan AI menjalankan tes (<code>php artisan test</code>) sebelum menandai task selesai.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 3.8: VIRTUAL ARCHITECTURE STUDIO (THE 4 VIRTUAL CHARTS COMMAND CENTER) -->
            <section id="section-3-8" class="bg-white dark:bg-zinc-900 border-2 border-sky-500/50 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-sky-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">&loz;</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Virtual Architecture Studio (Visual Chart Center)</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Visualisasi komprehensif diagram alur, topologi basis data, peta dependensi, dan timeline sprint standar AI Agent.</p>
                        </div>
                    </div>
                    
                    <!-- 4 Virtual Charts Switcher -->
                    <div class="flex flex-wrap items-center gap-1 font-mono text-xs no-print">
                        <button 
                            type="button"
                            @click="chartStudioTab = 'workflow'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-flow-target', 'mermaid-studio-flow-source'))" 
                            :class="chartStudioTab === 'workflow' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>1. WORKFLOW (ALUR)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'erd'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-erd-target', 'mermaid-studio-erd-source'))" 
                            :class="chartStudioTab === 'erd' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>2. ERD (DATABASE)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'feature_dep'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-featdep-target', 'mermaid-studio-featdep-source'))" 
                            :class="chartStudioTab === 'feature_dep' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>3. DEPENDENCY (FITUR)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'gantt'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-gantt-target', 'mermaid-studio-gantt-source'))" 
                            :class="chartStudioTab === 'gantt' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>4. GANTT (ROADMAP)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'infra'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-infra-target', 'mermaid-studio-infra-source'))" 
                            :class="chartStudioTab === 'infra' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>5. INFRASTRUKTUR</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'mobile_sync'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-mobilesync-target', 'mermaid-studio-mobilesync-source'))" 
                            :class="chartStudioTab === 'mobile_sync' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>6. MOBILE &amp; SYNC</span>
                        </button>
                    </div>
                </div>

                <!-- Chart 1: Workflow State Machine -->
                <div x-show="chartStudioTab === 'workflow'" class="space-y-4">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-800 text-xs font-mono">
                            <span class="text-sky-400 font-bold uppercase">DIAGRAM 1: ALUR KERJA SISTEM (WORKFLOW STATE MACHINE)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-studio-flow-source', this)"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-sky-500 hover:text-black text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID UNTUK AI AGENT</span>
                            </button>
                        </div>
                        <script type="text/plain" id="mermaid-studio-flow-source">{!! $prd['virtual_charts']['workflow_mermaid'] ?? '' !!}</script>
                        <div id="mermaid-studio-flow-target" class="overflow-x-auto min-h-[160px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-none bg-sky-500 animate-ping"></span>
                                <span>Memuat visualisasi alur kerja Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Database ERD Topology -->
                <div x-show="chartStudioTab === 'erd'" x-cloak class="space-y-4">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-800 text-xs font-mono">
                            <span class="text-emerald-400 font-bold uppercase">DIAGRAM 2: SKEMA BASIS DATA RELASIONAL (POSTGRESQL STRICT ULID)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-studio-erd-source', this)"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-emerald-500 hover:text-black text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID UNTUK AI AGENT</span>
                            </button>
                        </div>
                        <script type="text/plain" id="mermaid-studio-erd-source">{!! $prd['virtual_charts']['erd_mermaid'] ?? '' !!}</script>
                        <div id="mermaid-studio-erd-target" class="overflow-x-auto min-h-[220px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
                                <span>Memuat topologi ERD Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 3: Feature & Entity Dependency Graph -->
                <div x-show="chartStudioTab === 'feature_dep'" x-cloak class="space-y-4">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-800 text-xs font-mono">
                            <span class="text-amber-400 font-bold uppercase">DIAGRAM 3: PETA KETERGANTUNGAN (AKTOR &rarr; FITUR &rarr; ENTITAS BASIS DATA)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-studio-featdep-source', this)"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-amber-500 hover:text-black text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID UNTUK AI AGENT</span>
                            </button>
                        </div>
                        <script type="text/plain" id="mermaid-studio-featdep-source">{!! $prd['virtual_charts']['feature_dependency_mermaid'] ?? '' !!}</script>
                        <div id="mermaid-studio-featdep-target" class="overflow-x-auto min-h-[180px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-none bg-amber-500 animate-ping"></span>
                                <span>Memuat peta ketergantungan fitur Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 4: Sprint Delivery Roadmap (Gantt Timeline) -->
                <div x-show="chartStudioTab === 'gantt'" x-cloak class="space-y-4">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-800 text-xs font-mono">
                            <span class="text-rose-400 font-bold uppercase">DIAGRAM 4: ROADMAP EKSEKUSI &amp; TIMELINE SPRINT (GANTT CHART)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-studio-gantt-source', this)"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-rose-500 hover:text-black text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID UNTUK AI AGENT</span>
                            </button>
                        </div>
                        <script type="text/plain" id="mermaid-studio-gantt-source">{!! $prd['virtual_charts']['sprint_gantt_mermaid'] ?? '' !!}</script>
                        <div id="mermaid-studio-gantt-target" class="overflow-x-auto min-h-[200px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-none bg-rose-500 animate-ping"></span>
                                <span>Memuat timeline roadmap Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 5: Infrastructure, Security & Hosting Topology -->
                <div x-show="chartStudioTab === 'infra'" x-cloak class="space-y-4">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-800 text-xs font-mono">
                            <span class="text-violet-400 font-bold uppercase">DIAGRAM 5: TOPOLOGI INFRASTRUKTUR, KEAMANAN &amp; HOSTING</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-studio-infra-source', this)"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-violet-500 hover:text-black text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID UNTUK AI AGENT</span>
                            </button>
                        </div>
                        <script type="text/plain" id="mermaid-studio-infra-source">{!! $prd['virtual_charts']['infrastructure_mermaid'] ?? '' !!}</script>
                        <div id="mermaid-studio-infra-target" class="overflow-x-auto min-h-[200px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-none bg-violet-500 animate-ping"></span>
                                <span>Memuat topologi infrastruktur Mermaid...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 6: Mobile App & Local SQLite Sync Sequence -->
                <div x-show="chartStudioTab === 'mobile_sync'" x-cloak class="space-y-4">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <div class="flex items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-800 text-xs font-mono">
                            <span class="text-emerald-400 font-bold uppercase">DIAGRAM 6: ALUR SINKRONISASI MOBILE &amp; LOCAL SQLITE (OFFLINE-FIRST)</span>
                            <button 
                                type="button"
                                onclick="window.copyMermaidCode('mermaid-studio-mobilesync-source', this)"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-emerald-500 hover:text-black text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-700"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>SALIN KODE MERMAID UNTUK AI AGENT</span>
                            </button>
                        </div>
                        <script type="text/plain" id="mermaid-studio-mobilesync-source">{!! $prd['virtual_charts']['mobile_sync_mermaid'] ?? '' !!}</script>
                        <div id="mermaid-studio-mobilesync-target" class="overflow-x-auto min-h-[200px] flex items-center justify-center p-2 text-center">
                            <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
                                <span>Memuat topologi sinkronisasi Mobile &amp; SQLite Mermaid...</span>
                            </div>
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
            <section id="section-4" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
                                                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-pulse"></span>
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
                                <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
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
            <section id="section-5" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
                                <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
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
            <section id="section-6" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
            <section id="section-7" class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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

                <!-- Itemized Scope Breakdown Table (Transparansi Poin Input Klien) -->
                @if(!empty($itemizedScope['items']))
                    <div class="mb-6 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-4 font-mono text-xs">
                        <div class="flex items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500"></span>
                                <strong class="uppercase text-zinc-900 dark:text-zinc-100 font-bold">Rincian Komponen Biaya Berdasarkan Input Anda:</strong>
                            </div>
                            <span class="text-[10px] text-zinc-500 font-bold">{{ count($itemizedScope['items']) }} Komponen Teranalisis</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-zinc-200 dark:border-zinc-800 text-[10px] text-zinc-400 uppercase">
                                        <th class="py-2 pr-3">Kategori</th>
                                        <th class="py-2 pr-3">Kode</th>
                                        <th class="py-2 pr-3">Komponen / Spesifikasi Fitur</th>
                                        <th class="py-2 pr-3">Kompleksitas</th>
                                        <th class="py-2 text-right">Bobot Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900 text-[11px]">
                                    @foreach($itemizedScope['items'] as $it)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                                            <td class="py-2 pr-3 text-zinc-500">{{ $it['category'] }}</td>
                                            <td class="py-2 pr-3"><span class="px-1 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9px] font-bold">{{ $it['code'] }}</span></td>
                                            <td class="py-2 pr-3 font-bold text-zinc-800 dark:text-zinc-200">
                                                {{ $it['title'] }}
                                                <span class="block text-[10px] text-zinc-400 font-normal line-clamp-1">{{ $it['desc'] }}</span>
                                            </td>
                                            <td class="py-2 pr-3 text-zinc-500 text-[10px]">{{ $it['complexity'] }}</td>
                                            <td class="py-2 text-right font-bold whitespace-nowrap {{ ($it['amount'] ?? 0) < 0 ? 'text-amber-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                                                {{ ($it['amount'] ?? 0) < 0 ? '-Rp ' : 'Rp ' }}{{ number_format(abs($it['amount'] ?? 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-zinc-200 dark:border-zinc-800 text-xs font-bold">
                                        <td colspan="4" class="py-2 text-zinc-900 dark:text-zinc-100 uppercase">Subtotal Base Scope (Pace Standard 30 Hari)</td>
                                        <td class="py-2 text-right text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                            Rp {{ number_format($itemizedScope['base_subtotal'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

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
                                    <span x-show="selectedTier === '{{ $tierItem['id'] }}'" class="w-2 h-2 rounded-none {{ $isEmergencyOrEnterprise ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
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
            </section>

            <!-- SECTION 08: TIMELINE & GANTT MILESTONE (ALIGNED TO VELOCITY) -->
            <section id="section-8" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
            <section id="section-9" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid scroll-mt-24">
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
            <section id="section-10" class="bg-zinc-900 text-white border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none no-print scroll-mt-24">
                <!-- Staging Sandbox Environment Banner (If Provisioned) -->
                @if($blueprint->staging_url)
                    <div class="mb-6 p-4 sm:p-5 bg-gradient-to-r from-emerald-950/80 via-zinc-950 to-zinc-950 border border-emerald-500 text-zinc-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-none bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-400">
                                        <span x-show="locale === 'en'">ACTIVE STAGING DEMO SANDBOX PROVISIONED</span>
                                        <span x-show="locale !== 'en'">LINGKUNGAN DEMO STAGING TELAH DIAKTIFKAN</span>
                                    </span>
                                    @if($blueprint->is_free_grant)
                                        <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-mono font-black uppercase">
                                            PELAYANAN GRATIS (RP 0)
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-lg font-black tracking-tight text-white font-mono">
                                    <a href="{{ $blueprint->staging_url }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-400 underline decoration-emerald-500/50 underline-offset-4 flex items-center gap-1.5 break-all">
                                        <span>{{ $blueprint->staging_url }}</span>
                                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </h4>
                                <p class="text-xs text-zinc-400 font-sans">
                                    <span x-show="locale === 'en'">Isolated deployment container ready. Sprint milestones and live feature testing can be tracked directly here.</span>
                                    <span x-show="locale !== 'en'">Sandbox deployment terisolasi telah dialokasikan khusus. Progres sprint dan demo berkala dapat dipantau langsung di link ini.</span>
                                </p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2 flex-shrink-0 font-mono">
                                <a 
                                    href="{{ $blueprint->staging_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="bg-emerald-500 hover:bg-emerald-400 text-black font-bold text-xs uppercase px-4 py-2.5 transition flex items-center gap-2 rounded-none"
                                >
                                    <span x-show="locale === 'en'">Open Demo &rarr;</span>
                                    <span x-show="locale !== 'en'">Buka Staging &rarr;</span>
                                </a>
                                @if($blueprint->staging_provisioned_at)
                                    <span class="text-[10px] text-zinc-500">
                                        Aktif: {{ $blueprint->staging_provisioned_at->format('d M Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-6 mb-6">
                    <div>
                        <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-widest bg-emerald-500 text-black inline-block mb-2 rounded-none">
                            LEGAL &amp; PAYMENT PROTOCOL
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight flex items-center gap-2">
                            <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs inline-flex items-center justify-center rounded-none">10</span>
                            <span x-show="locale === 'en'">Scope Lock, Digital Sign-Off &amp; Escrow</span>
                            <span x-show="locale !== 'en'">Kunci Scope Proyek, Persetujuan Digital &amp; DP</span>
                        </h3>
                        <p class="text-zinc-400 text-xs mt-1 font-sans">
                            <span x-show="locale === 'en'">Project officially kicks off upon digital contract sign-off, cryptographic hash lock, and DP confirmation or free voucher grant.</span>
                            <span x-show="locale !== 'en'">Pengerjaan proyek resmi dimulai setelah penandatanganan digital, penguncian hash SHA-256, dan konfirmasi DP via Midtrans atau voucher pelayanan.</span>
                        </p>
                    </div>
                    <div class="text-left sm:text-right font-mono">
                        <span class="text-zinc-400 text-xs block">TERMIN TERPILIH: <span class="text-white font-bold" x-text="tierAmounts[selectedTier].name"></span></span>
                        @if($blueprint->is_free_grant)
                            <span class="text-xl font-black text-emerald-400">RP 0 (PELAYANAN KASIH)</span>
                            <span class="text-[10px] text-zinc-400 block">VOUCHER: {{ $blueprint->voucher_code }}</span>
                        @else
                            <span class="text-xl font-black text-emerald-400" x-text="'DP (50%): Rp ' + tierAmounts[selectedTier].dp.toLocaleString('id-ID')"></span>
                            <span class="text-[10px] text-zinc-400 block" x-text="'Total Kontrak: Rp ' + tierAmounts[selectedTier].contract.toLocaleString('id-ID')"></span>
                        @endif
                    </div>
                </div>

                <!-- Digital Sign-Off Cryptographic Audit Trail Seal -->
                <div class="p-4 bg-zinc-950 border border-zinc-800 text-xs font-mono mb-6 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800/80 pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span class="font-bold text-white uppercase tracking-wider text-[11px]">
                                SHA-256 SPECIFICATION INTEGRITY SEAL
                            </span>
                            @if($blueprint->signed_agreement)
                                <span class="px-2 py-0.2 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-bold">
                                    SIGNED &amp; LOCKED
                                </span>
                            @else
                                <span class="px-2 py-0.2 bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-bold">
                                    READY FOR SIGN-OFF
                                </span>
                            @endif
                        </div>
                        <div class="text-[10px] text-zinc-400">
                            SINGLE SOURCE OF TRUTH (ANTI-DISPUTE)
                        </div>
                    </div>

                    <div class="bg-zinc-900/90 p-3 border border-zinc-800 text-[11px] leading-relaxed">
                        <div class="flex items-center justify-between text-zinc-400 mb-1">
                            <span class="text-zinc-500 uppercase">Cryptographic Document Checksum (SHA-256):</span>
                            <button 
                                type="button" 
                                @click="navigator.clipboard.writeText('{{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}'); if(window.showToast) window.showToast({ type: 'success', title: 'SHA-256 DISALIN', message: 'Cryptographic hash berhasil disalin ke clipboard.' })"
                                class="text-emerald-400 hover:text-emerald-300 text-[10px] font-bold uppercase underline"
                            >
                                Salin Hash
                            </button>
                        </div>
                        <code class="text-emerald-400 break-all select-all font-mono text-[11px] block">
                            {{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}
                        </code>
                        @if($blueprint->signed_agreement && $blueprint->signed_at)
                            <div class="mt-2 pt-2 border-t border-zinc-800/80 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-zinc-400">
                                <span>Penandatangan: <strong class="text-zinc-200">{{ $blueprint->client_name ?: $blueprint->nama_bisnis }}</strong></span>
                                <span>Waktu: <strong class="text-zinc-200">{{ $blueprint->signed_at->format('d M Y H:i:s T') }}</strong></span>
                                <span>IP Audit: <strong class="text-zinc-200">{{ $blueprint->signer_ip ?: 'Recorded' }}</strong></span>
                            </div>
                        @endif
                    </div>

                    <p class="text-zinc-400 text-xs font-sans leading-relaxed">
                        Seluruh fitur dan arsitektur dalam PRD ini terikat secara kriptografis. Setiap perubahan di kemudian hari wajib melalui kesepakatan Change Request (CR) / Addendum tanpa mengubah basis dokumen utama.
                    </p>
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
                            <span>Tanda Tangani Kontrak &amp; Kunci Scope</span>
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </form>

                    <button 
                        type="button" 
                        @click="paymentModalOpen = true"
                        class="w-full h-full bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-700 transition flex items-center justify-center gap-2"
                    >
                        <span>Bayar DP / Klaim Voucher</span>
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

            <!-- FLOATING QUICK-ACCESS DOCK & SCROLL-SPY NAVIGATOR -->
            <aside 
                class="fixed bottom-5 right-4 sm:right-6 z-40 font-mono no-print flex flex-col items-end"
                @keydown.escape.window="floatingIndexOpen = false"
            >
                <!-- Floating Accordion Drawer Popover -->
                <div 
                    x-show="floatingIndexOpen" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    @click.outside="floatingIndexOpen = false"
                    class="mb-3 w-[92vw] sm:w-[380px] max-h-[75vh] flex flex-col bg-white dark:bg-zinc-950 border-2 border-emerald-500 shadow-2xl rounded-none overflow-hidden"
                >
                    <!-- Drawer Header -->
                    <div class="p-3.5 bg-zinc-900 text-white flex items-center justify-between border-b border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-emerald-400 rounded-none animate-pulse"></span>
                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-emerald-400">
                                    <span x-show="locale === 'en'">Quick PRD Directory</span>
                                    <span x-show="locale !== 'en'">Index Navigasi Cepat PRD</span>
                                </h3>
                                <p class="text-[10px] text-zinc-400">
                                    <span x-show="locale === 'en'">Jump to section without scrolling</span>
                                    <span x-show="locale !== 'en'">Pindah cepat tanpa lelah scrolling</span>
                                </p>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            @click="floatingIndexOpen = false" 
                            class="text-zinc-400 hover:text-white text-base px-2 py-0.5 leading-none transition"
                        >
                            &times;
                        </button>
                    </div>

                    <!-- Search & Quick Actions Bar -->
                    <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
                        <div class="relative flex-1">
                            <input 
                                type="text" 
                                x-model="indexSearchQuery" 
                                :placeholder="locale === 'en' ? 'Filter sections...' : 'Cari blok/fitur/ERD...'"
                                class="w-full bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 px-2.5 py-1 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:outline-none focus:border-emerald-500"
                            />
                            <button 
                                type="button" 
                                x-show="indexSearchQuery" 
                                @click="indexSearchQuery = ''" 
                                class="absolute right-2 top-1 text-zinc-400 hover:text-zinc-600 text-xs"
                            >&times;</button>
                        </div>
                        <button 
                            type="button" 
                            @click="expandAllGroups()" 
                            class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] border border-zinc-300 dark:border-zinc-700 transition"
                            :title="locale === 'en' ? 'Expand All' : 'Buka Semua'"
                        >
                            &boxplus;
                        </button>
                        <button 
                            type="button" 
                            @click="collapseAllGroups()" 
                            class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] border border-zinc-300 dark:border-zinc-700 transition"
                            :title="locale === 'en' ? 'Collapse All' : 'Tutup Semua'"
                        >
                            &boxminus;
                        </button>
                    </div>

                    <!-- Drawer Accordion Body (Scrollable) -->
                    <div class="p-2 overflow-y-auto flex-1 space-y-2 text-xs divide-y divide-zinc-100 dark:divide-zinc-900">
                        @foreach($accordionGroupDefs as $groupKey => $groupDef)
                            <div class="pt-2 first:pt-0">
                                <!-- Group Header Toggle -->
                                <button 
                                    type="button" 
                                    @click="toggleAccordionGroup('{{ $groupKey }}')"
                                    class="w-full px-2 py-1.5 bg-zinc-100 dark:bg-zinc-900/80 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-left flex items-center justify-between transition border border-zinc-200 dark:border-zinc-800"
                                >
                                    <span class="font-bold text-[11px] text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                        <span class="text-emerald-500 font-mono">#{{ $loop->iteration }}</span>
                                        <span x-show="locale === 'en'">{{ $groupDef['title_en'] }}</span>
                                        <span x-show="locale !== 'en'">{{ $groupDef['title_id'] }}</span>
                                    </span>
                                    <svg 
                                        class="w-3.5 h-3.5 text-zinc-500 transition-transform duration-200" 
                                        :class="accordionGroups['{{ $groupKey }}'] ? 'rotate-180' : ''" 
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <!-- Group Items -->
                                <div 
                                    x-show="accordionGroups['{{ $groupKey }}'] || indexSearchQuery.trim() !== ''" 
                                    x-transition
                                    class="mt-1 space-y-1 pl-1"
                                >
                                    @foreach($blueprintSections as $sec)
                                        @if($sec['group'] === $groupKey)
                                            <div 
                                                x-show="!indexSearchQuery || '{{ strtolower($sec['title_id'] . ' ' . $sec['title_en'] . ' ' . $sec['subtitle_id'] . ' ' . $sec['subtitle_en'] . ' ' . $sec['badge'] . ' ' . $sec['num']) }}'.includes(indexSearchQuery.toLowerCase())"
                                                @click="jumpTo('{{ $sec['id'] }}')"
                                                :class="activeSectionId === '{{ $sec['id'] }}' 
                                                    ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400 font-bold' 
                                                    : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                                class="px-2.5 py-1.5 border rounded-none cursor-pointer transition flex items-center justify-between text-xs group"
                                            >
                                                <div class="flex items-center gap-2 truncate pr-1">
                                                    <span 
                                                        :class="activeSectionId === '{{ $sec['id'] }}' ? 'bg-emerald-500 text-black' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                                                        class="w-4 h-4 flex-shrink-0 flex items-center justify-center font-bold text-[9px] rounded-none"
                                                    >
                                                        {{ $sec['num'] }}
                                                    </span>
                                                    <span class="truncate text-[11px] group-hover:text-emerald-500">
                                                        <span x-show="locale === 'en'">{{ $sec['title_en'] }}</span>
                                                        <span x-show="locale !== 'en'">{{ $sec['title_id'] }}</span>
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    <span x-show="activeSectionId === '{{ $sec['id'] }}'" class="w-1.5 h-1.5 bg-emerald-500 rounded-none animate-ping"></span>
                                                    <span class="text-[9px] text-zinc-400 uppercase font-mono">{{ $sec['badge'] }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Drawer Footer with Back to Top -->
                    <div class="p-2.5 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[11px]">
                        <span class="text-zinc-500 dark:text-zinc-400">
                            <span x-show="locale === 'en'">Active:</span>
                            <span x-show="locale !== 'en'">Aktif:</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 ml-1" x-text="getActiveSectionTitle()"></strong>
                        </span>
                        <button 
                            type="button" 
                            @click="window.scrollTo({ top: 0, behavior: 'smooth' }); floatingIndexOpen = false;" 
                            class="px-2 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold uppercase transition flex items-center gap-1 hover:opacity-90"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            <span x-show="locale === 'en'">Top</span>
                            <span x-show="locale !== 'en'">Atas</span>
                        </button>
                    </div>
                </div>

                <!-- Main Dock Pill-Free Bar -->
                <div class="flex items-center bg-zinc-950/95 text-white border-2 border-emerald-500 shadow-2xl backdrop-blur-md rounded-none overflow-hidden select-none">
                    <!-- Live Radar & Active Section Status -->
                    <button 
                        type="button" 
                        @click="floatingIndexOpen = !floatingIndexOpen"
                        class="px-3.5 py-2 hover:bg-zinc-800/80 transition flex items-center gap-2.5 text-left border-r border-zinc-800 group"
                        :title="locale === 'en' ? 'Click to toggle PRD section index' : 'Klik untuk buka/tutup index bagian PRD'"
                    >
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5 text-[9px] text-zinc-400 leading-none">
                                <span class="text-emerald-400 font-bold" x-text="'[' + (getActiveSectionIndex() < 10 ? '0' : '') + getActiveSectionIndex() + ' / 12]'"></span>
                                <span class="hidden sm:inline uppercase">SPY ACTIVE</span>
                            </div>
                            <span class="text-xs font-bold text-zinc-100 group-hover:text-emerald-400 transition-colors max-w-[150px] sm:max-w-[210px] truncate leading-tight mt-0.5" x-text="getActiveSectionTitle()"></span>
                        </div>
                        <svg 
                            class="w-3.5 h-3.5 text-emerald-400 transition-transform duration-200" 
                            :class="floatingIndexOpen ? 'rotate-180' : ''" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </button>

                    <!-- Quick Jump Index Button -->
                    <button 
                        type="button" 
                        @click="floatingIndexOpen = !floatingIndexOpen"
                        :class="floatingIndexOpen ? 'bg-emerald-500 text-black font-black' : 'text-zinc-200 hover:bg-zinc-800/80 hover:text-emerald-400'"
                        class="px-3 py-2 text-xs font-bold uppercase tracking-wider transition border-r border-zinc-800 flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span class="hidden sm:inline" x-show="locale === 'en'">INDEX</span>
                        <span class="hidden sm:inline" x-show="locale !== 'en'">DAFTAR ISI</span>
                    </button>

                    <!-- Scroll to Top Button -->
                    <button 
                        type="button" 
                        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                        class="px-2.5 py-2 text-zinc-400 hover:text-white hover:bg-zinc-800/80 transition"
                        :title="locale === 'en' ? 'Back to top' : 'Kembali ke atas'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </button>
                </div>
            </aside>

        </main>
    @endif

    <!-- Interactive Midtrans Escrow & Voucher Payment Modal -->
    <div 
        x-show="paymentModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 font-mono text-xs"
        @keydown.escape.window="paymentModalOpen = false"
    >
        <div 
            class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 max-w-lg w-full p-6 shadow-2xl relative rounded-none"
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
                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-bold uppercase tracking-wider rounded-none">
                    MIDTRANS SECURE ESCROW &amp; VOUCHER PORTAL
                </span>
                <h3 class="text-xl font-black uppercase text-zinc-900 dark:text-zinc-100 mt-2">
                    Instruksi Pembayaran DP &amp; Klaim
                </h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-sans">
                    Proyek: <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong>
                </p>
            </div>

            <!-- Invoice Summary Card -->
            <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 mb-4 space-y-2 rounded-none">
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

                <!-- Voucher Status Row (If Applied) -->
                <template x-if="appliedVoucher">
                    <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-2">
                        <span x-text="'Potongan Voucher (' + appliedVoucher.code + ')'"></span>
                        <span x-text="appliedVoucher.is_free_bypass ? '-100% (FREE BYPASS)' : '-' + appliedVoucher.discount_value + '%'"></span>
                    </div>
                </template>

                <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex justify-between items-baseline">
                    <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Total Tagihan DP</span>
                    <template x-if="appliedVoucher && appliedVoucher.is_free_bypass">
                        <span class="text-lg font-black text-emerald-500">RP 0 (GRATIS)</span>
                    </template>
                    <template x-if="!appliedVoucher || !appliedVoucher.is_free_bypass">
                        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + tierAmounts[selectedTier].dp.toLocaleString('id-ID')"></span>
                    </template>
                </div>
                <div class="text-[10px] text-zinc-400 pt-1 flex justify-between">
                    <span>ORDER ID: NPRO-DP-{{ strtoupper(substr($blueprint->id, 0, 8)) }}</span>
                    <span>SPEC ID: {{ strtoupper(substr($blueprint->id, 0, 8)) }}</span>
                </div>
            </div>

            <!-- Voucher Promo / Pelayanan Input Block -->
            <div class="border border-dashed border-zinc-300 dark:border-zinc-700 p-3 mb-4 bg-zinc-50 dark:bg-zinc-950/60 rounded-none">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-[11px] uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        <span>Punya Kode Promo / Voucher Pelayanan?</span>
                    </span>
                    <span x-show="appliedVoucher" class="text-[10px] px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/40 rounded-none">
                        VOUCHER AKTIF
                    </span>
                </div>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        x-model="voucherCode" 
                        :disabled="appliedVoucher !== null || isValidatingVoucher"
                        @keydown.enter.prevent="applyVoucher()"
                        placeholder="Contoh: PELAYANAN-KASIH"
                        class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2.5 py-2 text-xs font-mono uppercase focus:outline-none focus:border-emerald-500 rounded-none"
                    />
                    <button 
                        type="button" 
                        x-show="!appliedVoucher"
                        @click="applyVoucher()"
                        :disabled="isValidatingVoucher || !voucherCode.trim()"
                        class="bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase px-3 py-2 disabled:opacity-50 transition border border-zinc-600 rounded-none"
                    >
                        <span x-show="!isValidatingVoucher">Terapkan</span>
                        <span x-show="isValidatingVoucher">Cek...</span>
                    </button>
                    <button 
                        type="button" 
                        x-show="appliedVoucher" 
                        @click="resetVoucher()"
                        class="bg-rose-900/40 hover:bg-rose-800/60 text-rose-300 font-mono font-bold text-xs uppercase px-2.5 py-2 border border-rose-700/50 rounded-none"
                    >
                        Batal
                    </button>
                </div>
                <template x-if="appliedVoucher">
                    <div class="mt-2.5 p-2 bg-emerald-950/60 border border-emerald-600/60 text-emerald-300 text-[11px] font-mono leading-relaxed rounded-none">
                        <div class="font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="appliedVoucher.code + ' BERHASIL DIGUNAKAN'"></span>
                        </div>
                        <div class="text-[10px] text-emerald-400 mt-0.5" x-text="appliedVoucher.description"></div>
                        <div x-show="appliedVoucher.is_free_bypass" class="mt-1 font-bold text-emerald-200">
                            &bull; Subsidi Pelayanan: Tagihan Menjadi Rp 0 &amp; Auto-Provision Staging Sandbox.
                        </div>
                    </div>
                </template>
            </div>

            <!-- Mandatory Legal Sign-Off Checkbox -->
            <label class="flex items-start gap-2.5 p-3 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 cursor-pointer mb-4 select-none rounded-none">
                <input 
                    type="checkbox" 
                    x-model="agreeSignOff" 
                    class="mt-0.5 rounded-none border-zinc-400 text-emerald-600 focus:ring-emerald-500"
                />
                <span class="text-[11px] leading-snug text-zinc-700 dark:text-zinc-300 font-sans">
                    <strong class="font-mono text-emerald-600 dark:text-emerald-400 block uppercase font-bold text-[10px] mb-0.5">
                        Persetujuan Syarat &amp; Ketentuan Scope (Digital Sign-Off Lock)
                    </strong>
                    Dengan membayar DP atau mengklaim voucher ini, saya menyetujui spesifikasi scope di dokumen 
                    <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">{{ strtoupper(substr($blueprint->id, 0, 10)) }}</span> 
                    sebagai acuan tunggal pengerjaan proyek (Scope Freeze). Saya memahami bahwa fitur di luar dokumen ini memerlukan Addendum tersendiri.
                </span>
            </label>

            <!-- Payment Methods Info (Only show if not free bypass) -->
            <div x-show="!appliedVoucher || !appliedVoucher.is_free_bypass" class="space-y-2 mb-4">
                <div class="text-[11px] font-bold uppercase text-zinc-700 dark:text-zinc-300">
                    Kanal Pembayaran Otomatis:
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-[10px]">
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">QRIS (GoPay/OVO)</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">BCA VA</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">Mandiri Bill</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">Kartu Kredit</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
                <!-- Free Voucher Bypass Claim Button -->
                <template x-if="appliedVoucher && appliedVoucher.is_free_bypass">
                    <button 
                        type="button" 
                        @click="submitClaimVoucher()"
                        :disabled="isClaimingVoucher || !agreeSignOff"
                        class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-4 px-4 text-center block transition shadow-lg cursor-pointer disabled:opacity-50 rounded-none"
                    >
                        <span x-show="!isClaimingVoucher">KLAIM PELAYANAN GRATIS SEKARANG (Rp 0) &rarr;</span>
                        <span x-show="isClaimingVoucher" class="inline-block animate-pulse">Mengaktifkan Sandbox &amp; Kontrak...</span>
                    </button>
                </template>

                <!-- Standard Midtrans Snap Payment Button -->
                <template x-if="!appliedVoucher || !appliedVoucher.is_free_bypass">
                    <button 
                        type="button" 
                        @click="isPayingSnap = true; window.payBlueprintSnap(selectedTier, agreeSignOff, () => { isPayingSnap = true }, () => { isPayingSnap = false })"
                        :disabled="isPayingSnap || !agreeSignOff"
                        class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-3.5 px-4 text-center block transition shadow-lg cursor-pointer disabled:opacity-50 rounded-none"
                    >
                        <span x-show="!isPayingSnap">Bayar Sekarang via Midtrans Snap &rarr;</span>
                        <span x-show="isPayingSnap" class="inline-block animate-pulse">Membuat Sesi Snap...</span>
                    </button>
                </template>
                
                <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                    @csrf
                    <input type="hidden" name="tier" :value="selectedTier">
                    <button 
                        type="submit" 
                        class="w-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs uppercase tracking-wider py-2.5 px-4 text-center block transition border border-zinc-300 dark:border-zinc-700 rounded-none"
                    >
                        Simpan ke Cart Belanja
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- AI AGENT INGESTION COCKPIT & PROMPT ASSISTANT MODAL (ANTIGRAVITY / CURSOR / CLAUDE / WINDSURF) -->
    <div 
        x-show="showAiPromptModal" 
        x-cloak 
        @keydown.escape.window="showAiPromptModal = false"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 no-print font-mono"
    >
        <div 
            @click.outside="showAiPromptModal = false" 
            class="bg-zinc-950 border-2 border-emerald-500/80 max-w-4xl w-full p-6 sm:p-8 rounded-none text-zinc-100 shadow-2xl relative"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-zinc-800 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-emerald-500 text-black flex items-center justify-center font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black uppercase text-white tracking-tight flex items-center gap-2">
                            <span>AI AGENT INGESTION COCKPIT</span>
                            <span class="px-2 py-0.5 text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold">ORCHESTRATOR</span>
                        </h3>
                        <p class="text-xs text-zinc-400 font-mono mt-0.5">
                            Pilih AI Coding Agent &amp; Tahapan Sprint untuk mengumpankan PRD secara bertahap tanpa context-rot.
                        </p>
                    </div>
                </div>
                <button @click="showAiPromptModal = false" class="text-zinc-400 hover:text-white text-xl font-bold p-1 cursor-pointer">
                    &times;
                </button>
            </div>

            <!-- Agent Selector Tabs -->
            <div class="mb-5">
                <label class="text-[11px] text-zinc-400 uppercase font-bold block mb-2">1. PILIH AI CODING AGENT TARGET:</label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs">
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'antigravity'"
                        :class="selectedPromptAgent === 'antigravity' ? 'bg-emerald-500 text-black font-bold border-emerald-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Antigravity IDE</span>
                        <span class="text-[9px] opacity-75">Google DeepMind</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'cursor'"
                        :class="selectedPromptAgent === 'cursor' ? 'bg-sky-500 text-black font-bold border-sky-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Cursor Composer</span>
                        <span class="text-[9px] opacity-75">Cmd+I Multi-File</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'claude'"
                        :class="selectedPromptAgent === 'claude' ? 'bg-amber-500 text-black font-bold border-amber-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Claude Code CLI</span>
                        <span class="text-[9px] opacity-75">Terminal Autonomous</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'windsurf'"
                        :class="selectedPromptAgent === 'windsurf' ? 'bg-purple-500 text-black font-bold border-purple-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Windsurf Cascade</span>
                        <span class="text-[9px] opacity-75">Cascade Flow</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'devin'"
                        :class="selectedPromptAgent === 'devin' ? 'bg-rose-500 text-black font-bold border-rose-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Devin / Copilot</span>
                        <span class="text-[9px] opacity-75">Task-Driven</span>
                    </button>
                </div>
            </div>

            <!-- Sprint / Scope Selector -->
            <div class="mb-5">
                <label class="text-[11px] text-zinc-400 uppercase font-bold block mb-2">2. PILIH TAHAPAN INGESTION (BERTAHAP VS MASTER):</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2 text-[11px]">
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'all'"
                        :class="selectedPromptSprint === 'all' ? 'bg-zinc-100 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Master Kickoff
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint1'"
                        :class="selectedPromptSprint === 'sprint1' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 1: DB &amp; ULID
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint2'"
                        :class="selectedPromptSprint === 'sprint2' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 2: Engine
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint3'"
                        :class="selectedPromptSprint === 'sprint3' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 3: UI React
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint4'"
                        :class="selectedPromptSprint === 'sprint4' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 4: Security
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint5'"
                        :class="selectedPromptSprint === 'sprint5' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-850'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 5: Staging
                    </button>
                </div>
            </div>

            <!-- Agent Rule & Context Box -->
            <div class="mb-4 bg-zinc-900/80 border border-zinc-800 p-4 text-xs space-y-2">
                <div class="flex items-center justify-between text-[11px] text-zinc-400 border-b border-zinc-800/80 pb-2">
                    <span class="font-bold uppercase text-emerald-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="selectedPromptAgent === 'antigravity' ? 'Panduan Khusus Antigravity IDE (Google DeepMind)' : (selectedPromptAgent === 'cursor' ? 'Panduan Khusus Cursor Composer' : (selectedPromptAgent === 'claude' ? 'Panduan Khusus Claude Code CLI' : 'Panduan Agen'))"></span>
                    </span>
                    <span class="font-mono text-zinc-400 text-[10px]">Anti Context-Rot Protocol</span>
                </div>
                <p class="text-zinc-300 text-[11px] leading-relaxed" x-show="selectedPromptAgent === 'antigravity'">
                    <strong>Antigravity IDE Protocol:</strong> Antigravity bekerja secara otonom mengacu pada <code>.agents/AGENTS.md</code> dan <code>Ponytail Decision Ladder</code>. Prompt di bawah secara otomatis menginstruksikan Antigravity untuk menjalankan vertical slice dengan primary key ULID PostgreSQL (<code>HasUlids</code>), Keyset cursor pagination O(1), UI bebas capsule/pill shapes (subtle corners), zero native dialogs (wajib <code>window.showToast</code>), dan automated PHPUnit test verification sebelum menutup task.
                </p>
                <p class="text-zinc-300 text-[11px] leading-relaxed" x-show="selectedPromptAgent === 'cursor'">
                    <strong>Cursor Composer Protocol:</strong> Buka Composer (<code>Cmd+I</code> atau <code>Ctrl+I</code>). Buat file <code>PRD.md</code> atau lampirkan dokumen via <code>@PRD.md</code>. Berikan prompt per modul vertikal agar model tidak kehabisan output tokens atau merusak file global.
                </p>
                <p class="text-zinc-300 text-[11px] leading-relaxed" x-show="selectedPromptAgent === 'claude'">
                    <strong>Claude Code CLI Protocol:</strong> Buka terminal di folder root proyek dan jalankan perintah <code>claude</code>. Masukkan prompt di bawah langsung ke dalam command prompt Claude CLI. Claude Code akan mengaudit git diff secara mandiri.
                </p>
                <p class="text-zinc-300 text-[11px] leading-relaxed" x-show="selectedPromptAgent === 'windsurf' || selectedPromptAgent === 'devin'">
                    <strong>Agentic Task Flow:</strong> Masukkan prompt ke agent chat window. Pastikan agen tidak membuat file di luar modul yang sedang dikerjakan.
                </p>
            </div>

            <!-- Dynamic Prompt Textarea / Preview -->
            <div class="mb-5 relative">
                <div class="flex items-center justify-between mb-1.5 text-xs">
                    <span class="text-zinc-400 text-[10px] uppercase font-bold">HASIL GENERATE PROMPT SIAP COPY:</span>
                    <span class="text-[10px] text-emerald-400 font-mono">100% Parameterized &amp; Dispute-Proof</span>
                </div>
                <div class="bg-black border border-zinc-800 p-4 max-h-56 overflow-y-auto font-mono text-[11px] text-emerald-400 leading-relaxed whitespace-pre-wrap select-all" id="ai-agent-cockpit-prompt-text" x-text="generateAgentPrompt(selectedPromptAgent, selectedPromptSprint)"></div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-zinc-800">
                <button 
                    type="button" 
                    @click="showAiPromptModal = false"
                    class="w-full sm:w-auto px-4 py-2 border border-zinc-700 hover:bg-zinc-800 text-zinc-300 text-xs uppercase font-bold transition cursor-pointer"
                >
                    Tutup
                </button>
                <button 
                    type="button" 
                    onclick="window.copyCockpitPrompt(this)"
                    class="w-full sm:w-auto px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-mono uppercase font-bold transition flex items-center justify-center gap-2 shadow-lg cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span>SALIN PROMPT UNTUK <span x-text="selectedPromptAgent.toUpperCase()">AGENT</span></span>
                </button>
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

        window.getBlueprintAgentPrompt = function(agent, sprint) {
            const business = "{{ addslashes($blueprint->nama_bisnis ?: $blueprint->client_name) }}";
            const platform = "{{ addslashes($blueprint->target_platform ?? ($prd['executive_summary']['target_platform'] ?? 'Responsive Modern Web & PWA')) }}";
            const migration = "{{ addslashes($blueprint->migrasi_data ?? ($prd['executive_summary']['legacy_data_migration'] ?? 'Database Baru Bersih')) }}";
            const hosting = "{{ addslashes($blueprint->preferensi_hosting ?? ($prd['executive_summary']['hosting_infrastructure'] ?? 'Managed Cloud VPS Neriah Pro')) }}";
            const warranty = "{{ addslashes($blueprint->garansi_sla ?? ($prd['executive_summary']['warranty_sla'] ?? '30 Hari Garansi Bug')) }}";
            const payment = "{{ addslashes($blueprint->user_metadata['termin_pembayaran'] ?? ($prd['executive_summary']['payment_milestones'] ?? 'Termin 1 (50% DP) + Termin 2 (50% Pelunasan)')) }}";
            const targetWaktu = "{{ addslashes($blueprint->target_waktu ?? '30 Hari Kerja') }}";

            let agentPrefix = "";
            if (agent === 'antigravity') {
                agentPrefix = `[ANTIGRAVITY IDE PROTOCOL - GOOGLE DEEPMIND]\n` +
                    `Sebagai Principal Software Architect di Google DeepMind Antigravity IDE, patuhi protokol .agents/AGENTS.md dan Ponytail Decision Ladder:\n` +
                    `- Gunakan ULID (HasUlids) untuk semua tabel PostgreSQL domain bisnis.\n` +
                    `- Gunakan Keyset cursor pagination O(1) (cursorPaginate()). Dilarang offset pagination.\n` +
                    `- Terapkan desain Anti-AI-Slop: subtle border-radius (rounded-none s/d rounded-sm), DILARANG rounded-full / capsule buttons.\n` +
                    `- Larangan mutlak window.alert()/confirm(). Wajib gunakan window.showToast() atau modal Alpine/Tailwind.\n` +
                    `- Jalankan automated tests (php artisan test) dan pastikan exit code 0 sebelum selesai.\n\n`;
            } else if (agent === 'cursor') {
                agentPrefix = `[CURSOR COMPOSER DIRECTIVE]\n` +
                    `Gunakan context bounded files (@PRD.md). Terapkan perubahan baris demi baris secara presisi tanpa memodifikasi file di luar modul.\n\n`;
            } else if (agent === 'claude') {
                agentPrefix = `[CLAUDE CODE CLI AUTONOMOUS DIRECTIVE]\n` +
                    `Jalankan eksekusi terminal otonom. Jangan ubah file konfigurasi global di luar scope task.\n\n`;
            } else if (agent === 'windsurf') {
                agentPrefix = `[WINDSURF CASCADE FLOW DIRECTIVE]\n` +
                    `Ikuti alur Cascade agentic mode. Lakukan pengujian dan pastikan kode bersih tanpa regresi.\n\n`;
            } else {
                agentPrefix = `[AI CODING AGENT TASK DIRECTIVE]\n` +
                    `Implementasikan modul berikut sesuai spesifikasi PRD dengan standar enterprise.\n\n`;
            }

            let sprintBody = "";
            if (sprint === 'all') {
                sprintBody = `MASTER ARCHITECTURAL KICKOFF: PROYEK ${business}\n` +
                    `Spesifikasi Kunci Kontrak:\n` +
                    `1. Target Platform: ${platform}\n` +
                    `2. Migrasi Data: ${migration}\n` +
                    `3. Infrastruktur & Hosting: ${hosting}\n` +
                    `4. Garansi & SLA: ${warranty}\n` +
                    `5. Skema Termin Pembayaran: ${payment}\n` +
                    `Durasi Target: ${targetWaktu}\n\n` +
                    `Langkah Pertama:\n` +
                    `1. Periksa model dan migration database ULID.\n` +
                    `2. Siapkan action handlers dan controller business logic.\n` +
                    `3. Buat antarmuka pengguna interaktif (React Islands / Blade).\n` +
                    `4. Jalankan pengujian php artisan test untuk memverifikasi fungsionalitas.`;
            } else if (sprint === 'sprint1') {
                sprintBody = `SPRINT 1 TASK: DATABASE MIGRATION & ULID MODELS (${business})\n` +
                    `Scope:\n` +
                    `- Buat migration PostgreSQL dengan primary key ->ulid('id')->primary().\n` +
                    `- Tambahkan trait HasUlids pada semua Model Eloquent terkait.\n` +
                    `- Kolom multi-bahasa wajib bertipe JSON {'id': '...', 'en': '...'} dengan cast 'array'.\n` +
                    `- Pastikan foreign key menggunakan foreignUlid.\n` +
                    `- Jalankan php artisan migrate dan verifikasi skema database.`;
            } else if (sprint === 'sprint2') {
                sprintBody = `SPRINT 2 TASK: CORE ENGINE & ACTION HANDLERS (${business})\n` +
                    `Scope:\n` +
                    `- Buat controller dan FormRequest dengan validasi ketat.\n` +
                    `- Gunakan Cursor Pagination O(1) pada query daftar record.\n` +
                    `- Simpan data dengan transaksi DB::transaction() ACID.\n` +
                    `- Buat custom events dan listeners untuk audit trail.`;
            } else if (sprint === 'sprint3') {
                sprintBody = `SPRINT 3 TASK: FRONTEND UI & INTERACTIVE ISLANDS (${business})\n` +
                    `Scope:\n` +
                    `- Terapkan desain tajam bertema Modern Monolith (subtle border-radius rounded-none s/d rounded-sm).\n` +
                    `- Dilarang keras menggunakan pill/capsule shapes (rounded-full).\n` +
                    `- Sediakan dukungan multi-bahasa 2-tier (toggle ID / EN).\n` +
                    `- Semua input telepon wajib memiliki Country Zone (+62, +65, dst).\n` +
                    `- Format ribuan wajib menggunakan pemisah titik/koma.\n` +
                    `- Notifikasi wajib menggunakan window.showToast, dilarang alert() native.`;
            } else if (sprint === 'sprint4') {
                sprintBody = `SPRINT 4 TASK: SECURITY QUALITY GATE & AUDIT (${business})\n` +
                    `Scope:\n` +
                    `- Pasang honeypot anti-bot pada setiap formulir intake.\n` +
                    `- Pasang rate limiter pada endpoint sensitif.\n` +
                    `- Tulis automated PHPUnit test untuk skenario lolos dan skenario gagal.\n` +
                    `- Jalankan php artisan test dan pastikan semua pengujian lulus 100%.`;
            } else if (sprint === 'sprint5') {
                sprintBody = `SPRINT 5 TASK: STAGING VALIDATION & DEPLOYMENT PREPARATION (${business})\n` +
                    `Scope:\n` +
                    `- Verifikasi build frontend: npm run build.\n` +
                    `- Verifikasi file nixpacks.toml dan konfigurasi Nginx.\n` +
                    `- Siapkan script deployment ./deploy.sh 2 (Migrasi Aman) atau ./deploy.sh 6 (Assets).\n` +
                    `- Lakukan UAT komprehensif sebelum serah terima kunci private repo GitHub.`;
            }

            return agentPrefix + sprintBody;
        };

        window.copyCockpitPrompt = function(btnEl) {
            const textEl = document.getElementById('ai-agent-cockpit-prompt-text');
            if (!textEl) return;
            const text = textEl.innerText || textEl.textContent;
            navigator.clipboard.writeText(text.trim()).then(() => {
                const orig = btnEl.innerHTML;
                btnEl.innerHTML = '<span class="text-black font-bold">✓ PROMPT DISALIN!</span>';
                if (window.showToast) {
                    window.showToast({
                        type: 'success',
                        title: 'PROMPT AGENT TERSALIN',
                        message: 'Prompt siap ditempelkan ke terminal atau AI IDE pilihan Anda.'
                    });
                }
                setTimeout(() => { btnEl.innerHTML = orig; }, 2000);
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
