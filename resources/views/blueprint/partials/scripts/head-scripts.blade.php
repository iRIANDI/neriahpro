    <!-- Alpine.js & Mermaid UMD Bundle (Local Vendor JS) -->
    <script defer src="{{ asset('js/vendor/alpine.min.js') }}"></script>
    <script src="{{ asset('js/vendor/mermaid.min.js') }}"></script>
    <script>
        // Dynamic Viewport & Sticky Navigation Synchronization
        function syncNavMetrics() {
            try {
                const header = document.querySelector('header.fixed');
                if (header) {
                    const h = Math.round(header.getBoundingClientRect().height);
                    if (h > 0) {
                        document.documentElement.style.setProperty('--global-nav-h', h + 'px');
                    }
                }
                const toolbar = document.getElementById('blueprint-toolbar');
                if (toolbar) {
                    const th = Math.round(toolbar.getBoundingClientRect().height);
                    if (th > 0) {
                        document.documentElement.style.setProperty('--toolbar-h', th + 'px');
                    }
                }
            } catch (e) {}
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncNavMetrics);
        } else {
            syncNavMetrics();
        }
        window.addEventListener('load', syncNavMetrics);
        window.addEventListener('resize', syncNavMetrics);
        window.addEventListener('scroll', syncNavMetrics, { passive: true });
    </script>
    <!-- Midtrans Snap JS (In-Page Popup Modal) -->
    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        window.payBlueprintSnap = async function(tier, agreeSignOff, voucherCode, onStart, onFinish) {
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
                    body: JSON.stringify({ tier: tier, agree_sign_off: agreeSignOff, voucher_code: voucherCode || '' })
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

        window.payPelunasanSnap = async function(slug, onStart, onFinish) {
            if (onStart) onStart();
            try {
                if (window.showToast) {
                    window.showToast({
                        type: 'info',
                        title: 'MEMBUAT SESI PELUNASAN',
                        message: 'Menghubungkan ke gateway Midtrans Sandbox...'
                    });
                }
                const res = await fetch('/blueprint/' + encodeURIComponent(slug) + '/pelunasan-snap-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                if (!data.success || !data.token) {
                    throw new Error(data.message || 'Gagal memproses sesi Snap Midtrans untuk pelunasan.');
                }
                if (window.snap && window.snap.pay) {
                    window.snap.pay(data.token, {
                        onSuccess: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'PELUNASAN BERHASIL',
                                    message: 'Pelunasan proyek 50% berhasil diverifikasi oleh Midtrans! Memperbarui proposal...',
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
                                    message: 'Instruksi pembayaran pelunasan telah dibuat. Silakan transfer sesuai rincian.',
                                    duration: 5000
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 2200);
                        },
                        onError: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'error',
                                    title: 'PELUNASAN DIBATALKAN',
                                    message: 'Sesi transaksi pelunasan dibatalkan atau ditolak.'
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
                        title: 'KENDALA PELUNASAN',
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

        window.saveBlueprintTasks = async function(slug, mvpTasks, phase2Tasks, onStart, onFinish) {
            if (onStart) onStart();
            const currentLocale = (new URLSearchParams(window.location.search).get('locale')) || (document.documentElement.lang === 'en' ? 'en' : 'id');
            try {
                const res = await fetch('/blueprint/' + encodeURIComponent(slug) + '/tasks/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Locale': currentLocale,
                    },
                    body: JSON.stringify({
                        mvp_tasks: mvpTasks,
                        phase2_tasks: phase2Tasks,
                        locale: currentLocale
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal menyimpan perubahan task.');
                }
                return data;
            } finally {
                if (onFinish) onFinish();
            }
        };

        let isProgrammaticScroll = false;
        let programmaticScrollTimer = null;
        window.isProgrammaticScrolling = function() {
            return isProgrammaticScroll;
        };

        window.syncNavMetrics = function() {
            try {
                const globalHeader = document.querySelector('header.fixed') || document.querySelector('nav');
                const toolbar = document.getElementById('blueprint-toolbar');
                if (globalHeader) {
                    const h = globalHeader.getBoundingClientRect().height || globalHeader.offsetHeight;
                    if (h > 0) {
                        document.documentElement.style.setProperty('--global-nav-h', Math.round(h) + 'px');
                    }
                }
                if (toolbar) {
                    const th = toolbar.getBoundingClientRect().height || toolbar.offsetHeight;
                    if (th > 0) {
                        document.documentElement.style.setProperty('--toolbar-h', Math.round(th) + 'px');
                    }
                }
            } catch (e) {}
        };
        window.addEventListener('resize', window.syncNavMetrics);
        window.addEventListener('scroll', window.syncNavMetrics, { passive: true });
        window.addEventListener('load', window.syncNavMetrics);
        document.addEventListener('DOMContentLoaded', window.syncNavMetrics);
        setTimeout(window.syncNavMetrics, 50);
        setTimeout(window.syncNavMetrics, 300);
        setTimeout(window.syncNavMetrics, 1000);

        window.jumpToSection = function(id) {
            let el = document.getElementById(id);
            if (!el) {
                if (id === 'section-13') el = document.getElementById('sub-sec-6-13-export');
                else if (id === 'sub-sec-6-13-export') el = document.getElementById('section-13');
            }
            if (!el) return;

            // Lock scroll spy while smooth scrolling to target
            isProgrammaticScroll = true;
            clearTimeout(programmaticScrollTimer);
            programmaticScrollTimer = setTimeout(() => {
                isProgrammaticScroll = false;
            }, 1400);

            // Compute exact offset: global nav + toolbar (exact bottom of toolbar, matching the user's line)
            window.syncNavMetrics();
            const globalHeader = document.querySelector('header.fixed') || document.querySelector('nav');
            const toolbar = document.getElementById('blueprint-toolbar');
            const globalH = (globalHeader ? (globalHeader.getBoundingClientRect().height || globalHeader.offsetHeight) : 0) 
                || parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--global-nav-h')) 
                || 88;
            const toolbarH = (toolbar ? (toolbar.getBoundingClientRect().height || toolbar.offsetHeight) : 0) 
                || parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--toolbar-h')) 
                || 46;

            const stickyOffset = Math.round(globalH + toolbarH);

            const elAbsoluteTop = el.getBoundingClientRect().top + window.pageYOffset;
            const targetY = Math.max(0, Math.round(elAbsoluteTop - stickyOffset));
            window.scrollTo({ top: targetY, behavior: 'smooth' });

            // Visual pulse highlight
            el.classList.add('outline-2', 'outline-emerald-500', 'transition-all');
            setTimeout(() => {
                el.classList.remove('outline-2', 'outline-emerald-500');
            }, 1800);

            // High-precision alignment handler on scroll completion
            const settleScroll = () => {
                window.removeEventListener('scrollend', settleScroll);
                clearTimeout(programmaticScrollTimer);
                isProgrammaticScroll = false;

                if (el && el.offsetParent !== null) {
                    const currentTop = Math.round(el.getBoundingClientRect().top);
                    const diff = currentTop - stickyOffset;
                    // If within 2px to 60px of the line, micro-adjust to exactly flush
                    if (Math.abs(diff) >= 2 && Math.abs(diff) <= 60) {
                        window.scrollBy({ top: diff, behavior: 'instant' });
                    }
                }
            };

            if ('onscrollend' in window) {
                window.addEventListener('scrollend', settleScroll, { once: true });
            }
            setTimeout(settleScroll, 950);
        };

        window.setupBlueprintScrollSpy = function(onActiveChange) {
            const sectionIds = [
                'section-00-execution-guide',
                'section-1', 'section-1-5', 'section-2', 'section-3', 'section-3-5',
                'section-3-8', 'section-4', 'section-5', 'section-6', 'section-13',
                'section-7', 'section-8', 'section-9', 'section-10'
            ];
            let ticking = false;
            let lastActive = null;

            const update = () => {
                if (isProgrammaticScroll) {
                    ticking = false;
                    return;
                }

                let current = sectionIds[0];
                const isBottom = (window.innerHeight + window.pageYOffset) >= (document.body.offsetHeight - 120);
                
                if (isBottom) {
                    current = sectionIds[sectionIds.length - 1];
                } else if (window.pageYOffset < 400) {
                    current = sectionIds[0];
                } else {
                    for (let i = sectionIds.length - 1; i >= 0; i--) {
                        let el = document.getElementById(sectionIds[i]);
                        if (!el && sectionIds[i] === 'section-13') {
                            el = document.getElementById('sub-sec-6-13-export');
                        }
                        if (el && el.offsetParent !== null) {
                            const rect = el.getBoundingClientRect();
                            if (rect.top <= 220) {
                                current = sectionIds[i];
                                break;
                            }
                        }
                    }
                }

                if (current !== lastActive) {
                    lastActive = current;
                    onActiveChange(current);
                }
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

        window.renderMermaidDiagram = async function(containerId, sourceId, force = false) {
            const container = document.getElementById(containerId);
            const source = document.getElementById(sourceId);
            if (!container || !source || !window.mermaid) return;

            // If already rendered with an SVG and not forcing re-render, return
            if (!force && container.querySelector('svg')) return;

            try {
                const isDark = document.documentElement.classList.contains('dark');
                mermaid.initialize({
                    startOnLoad: false,
                    theme: isDark ? 'dark' : 'neutral',
                    securityLevel: 'loose',
                    fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
                    flowchart: {
                        useMaxWidth: true,
                        htmlLabels: true,
                        curve: 'basis'
                    },
                    er: {
                        useMaxWidth: true
                    },
                    gantt: {
                        useMaxWidth: true,
                        fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace'
                    }
                });

                const safeId = 'render_' + containerId.replace(/[^a-zA-Z0-9]/g, '_') + '_' + Math.floor(Math.random() * 1000000);
                const code = source.textContent.trim();
                const { svg } = await mermaid.render(safeId, code);
                container.innerHTML = svg;
                const renderedSvg = container.querySelector('svg');
                if (renderedSvg) {
                    if (containerId === 'mermaid-fullscreen-target') {
                        let natW = 1200;
                        let natH = 800;
                        const vbAttr = renderedSvg.getAttribute('viewBox');
                        if (vbAttr) {
                            const parts = vbAttr.trim().split(/[\s,]+/).map(Number);
                            if (parts.length >= 4 && parts[2] > 0 && parts[3] > 0) {
                                natW = parts[2];
                                natH = parts[3];
                            }
                        } else if (renderedSvg.viewBox && renderedSvg.viewBox.baseVal && renderedSvg.viewBox.baseVal.width > 0) {
                            natW = renderedSvg.viewBox.baseVal.width;
                            natH = renderedSvg.viewBox.baseVal.height;
                        }
                        renderedSvg.setAttribute('width', natW);
                        renderedSvg.setAttribute('height', natH);
                        renderedSvg.style.width = natW + 'px';
                        renderedSvg.style.height = natH + 'px';
                        renderedSvg.style.maxWidth = 'none';
                        renderedSvg.style.maxHeight = 'none';
                    } else {
                        renderedSvg.removeAttribute('width');
                        renderedSvg.removeAttribute('height');
                        renderedSvg.style.setProperty('max-width', '100%', 'important');
                        renderedSvg.style.setProperty('max-height', '100%', 'important');
                        renderedSvg.style.setProperty('width', 'auto', 'important');
                        renderedSvg.style.setProperty('height', '100%', 'important');
                        renderedSvg.style.setProperty('object-fit', 'contain', 'important');
                    }
                    renderedSvg.style.display = 'block';
                    renderedSvg.style.margin = 'auto';
                }
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

        window.reRenderActiveMermaid = function() {
            const diagramPairs = [
                ['mermaid-studio-flow-target', 'mermaid-studio-flow-source'],
                ['mermaid-studio-erd-target', 'mermaid-studio-erd-source'],
                ['mermaid-studio-featdep-target', 'mermaid-studio-featdep-source'],
                ['mermaid-studio-gantt-target', 'mermaid-studio-gantt-source'],
                ['mermaid-studio-infra-target', 'mermaid-studio-infra-source'],
                ['mermaid-studio-mobilesync-target', 'mermaid-studio-mobilesync-source'],
                ['mermaid-flow-target', 'mermaid-flow-source'],
                ['mermaid-erd-target', 'mermaid-erd-source'],
            ];
            for (const [tId, sId] of diagramPairs) {
                const el = document.getElementById(tId);
                if (el && el.querySelector('svg')) {
                    window.renderMermaidDiagram(tId, sId, true);
                }
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
            window.dispatchEvent(new CustomEvent('open-raw-md-warning', { detail: { action: 'copy' } }));
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
