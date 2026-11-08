    <script>
        window.executeCopyFullPrdMarkdown = async function(btnEl) {
            const orig = btnEl ? btnEl.innerHTML : '';
            if (btnEl) btnEl.innerHTML = '<span class="text-emerald-400 font-bold animate-pulse">Mengambil MD...</span>';
            try {
                const activeLoc = (typeof Alpine !== 'undefined' && document.querySelector('[x-data]')?._x_dataStack?.[0]?.locale) || (document.cookie.includes('neriah_locale=en') ? 'en' : 'id');
                const res = await fetch("{{ route('blueprint.raw-md', $blueprint->slug) }}?locale=" + activeLoc);
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

        window.copyFullPrdMarkdown = function(btnEl) {
            window.dispatchEvent(new CustomEvent('open-raw-md-warning', { detail: { action: 'copy' } }));
        };

        window.submitChangeRequest = async function(slug, formEl) {
            const btn = formEl.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'MENGIRIM...';
            btn.disabled = true;

            const titleInput = formEl.querySelector('input[name="title"]');
            const descInput = formEl.querySelector('textarea[name="description"]');
            
            try {
                const response = await fetch(`/blueprint/${slug}/issues`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        feature_id: 'change_request_global',
                        title: titleInput.value,
                        description: descInput.value,
                        type: 'change_request'
                    })
                });

                if (response.ok) {
                    if (window.showToast) window.showToast({ type: 'success', title: 'Request Addendum Dikirim', message: 'Tim kami akan mereview request ini dan mengirimkan estimasi SP.' });
                    titleInput.value = '';
                    descInput.value = '';
                    document.getElementById('modal-cr').style.display = 'none';
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    if (window.showToast) window.showToast({ type: 'error', title: 'Error', message: 'Gagal mengirim tiket addendum.' });
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        };

        window.submitFeatureFeedback = async function(featureId, slug, formEl) {
            const btn = formEl.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '...';
            btn.disabled = true;

            const titleInput = formEl.querySelector('input[name="title"]');
            const title = titleInput.value;
            
            try {
                const response = await fetch(`/blueprint/${slug}/issues`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        feature_id: featureId,
                        title: title,
                        description: title, // Using title as description for quick feedback
                        type: 'bug'
                    })
                });

                if (response.ok) {
                    if (window.showToast) window.showToast({ type: 'success', title: 'Success', message: 'Tiket QA berhasil dikirim.' });
                    titleInput.value = '';
                    window.location.reload(); // Quick refresh to show new issue
                } else {
                    if (window.showToast) window.showToast({ type: 'error', title: 'Error', message: 'Gagal mengirim tiket.' });
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        };

        window.submitFeatureComment = async function(issueId, slug, formEl) {
            const btn = formEl.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '...';
            btn.disabled = true;

            const contentInput = formEl.querySelector('input[name="content"]');
            const content = contentInput.value;
            
            try {
                const response = await fetch(`/blueprint/${slug}/issues/${issueId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: content })
                });

                if (response.ok) {
                    if (window.showToast) window.showToast({ type: 'success', title: 'Success', message: 'Komentar ditambahkan.' });
                    contentInput.value = '';
                    window.location.reload(); // Quick refresh to show new comment
                } else {
                    if (window.showToast) window.showToast({ type: 'error', title: 'Error', message: 'Gagal menambahkan komentar.' });
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
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
            let isEn = false;
            try {
                const root = document.querySelector('[x-data]');
                if (root && window.Alpine) {
                    const data = window.Alpine.$data(root);
                    if (data && data.locale) {
                        isEn = data.locale === 'en';
                    }
                }
            } catch(e) {}
            if (!isEn) {
                isEn = document.cookie.includes('neriah_locale=en') || (document.documentElement.lang && document.documentElement.lang.startsWith('en'));
            }

            const business = "{{ addslashes($blueprint->nama_bisnis ?: $blueprint->client_name) }}";
            const platform = "{{ addslashes($blueprint->target_platform ?? ($prd['executive_summary']['target_platform'] ?? 'Responsive Modern Web & PWA')) }}";
            const migration = "{{ addslashes($blueprint->migrasi_data ?? ($prd['executive_summary']['legacy_data_migration'] ?? 'Database Baru Bersih')) }}";
            const hosting = "{{ addslashes($blueprint->preferensi_hosting ?? ($prd['executive_summary']['hosting_infrastructure'] ?? 'Managed Cloud VPS Neriah Pro')) }}";
            const warranty = "{{ addslashes($blueprint->garansi_sla ?? ($prd['executive_summary']['warranty_sla'] ?? '30 Hari Garansi Bug')) }}";
            const payment = "{{ addslashes($blueprint->user_metadata['termin_pembayaran'] ?? ($prd['executive_summary']['payment_milestones'] ?? 'Termin 1 (50% DP) + Termin 2 (50% Pelunasan)')) }}";
            const targetWaktu = "{{ addslashes($blueprint->target_waktu ?? '30 Hari Kerja') }}";

            let agentPrefix = "";
            if (agent === 'antigravity') {
                if (isEn) {
                    agentPrefix = `[ANTIGRAVITY IDE PROTOCOL - GOOGLE DEEPMIND]\n` +
                        `As Principal Software Architect in Google DeepMind Antigravity IDE, strictly adhere to .agents/AGENTS.md and Ponytail Decision Ladder:\n` +
                        `- Use ULID (HasUlids) for all business domain PostgreSQL tables.\n` +
                        `- Use Keyset cursor pagination O(1) (cursorPaginate()). Never use offset pagination.\n` +
                        `- Apply Anti-AI-Slop design: subtle border-radius (rounded-none to rounded-sm), STRICT BAN on rounded-full / capsule buttons.\n` +
                        `- Absolute ban on native window.alert()/confirm(). Mandatory window.showToast() or Alpine/Tailwind modals.\n` +
                        `- Run automated tests (php artisan test) and ensure exit code 0 before completing.\n\n`;
                } else {
                    agentPrefix = `[ANTIGRAVITY IDE PROTOCOL - GOOGLE DEEPMIND]\n` +
                        `Sebagai Principal Software Architect di Google DeepMind Antigravity IDE, patuhi protokol .agents/AGENTS.md dan Ponytail Decision Ladder:\n` +
                        `- Gunakan ULID (HasUlids) untuk semua tabel PostgreSQL domain bisnis.\n` +
                        `- Gunakan Keyset cursor pagination O(1) (cursorPaginate()). Dilarang offset pagination.\n` +
                        `- Terapkan desain Anti-AI-Slop: subtle border-radius (rounded-none s/d rounded-sm), DILARANG rounded-full / capsule buttons.\n` +
                        `- Larangan mutlak window.alert()/confirm(). Wajib gunakan window.showToast() atau modal Alpine/Tailwind.\n` +
                        `- Jalankan automated tests (php artisan test) dan pastikan exit code 0 sebelum selesai.\n\n`;
                }
            } else if (agent === 'cursor') {
                if (isEn) {
                    agentPrefix = `[CURSOR COMPOSER DIRECTIVE]\n` +
                        `Use context bounded files (@PRD.md). Apply line-by-line changes with surgical precision without modifying files outside the module.\n\n`;
                } else {
                    agentPrefix = `[CURSOR COMPOSER DIRECTIVE]\n` +
                        `Gunakan context bounded files (@PRD.md). Terapkan perubahan baris demi baris secara presisi tanpa memodifikasi file di luar modul.\n\n`;
                }
            } else if (agent === 'claude') {
                if (isEn) {
                    agentPrefix = `[CLAUDE CODE CLI AUTONOMOUS DIRECTIVE]\n` +
                        `Execute terminal autonomously. Never modify global config files outside task scope.\n\n`;
                } else {
                    agentPrefix = `[CLAUDE CODE CLI AUTONOMOUS DIRECTIVE]\n` +
                        `Jalankan eksekusi terminal otonom. Jangan ubah file konfigurasi global di luar scope task.\n\n`;
                }
            } else if (agent === 'windsurf') {
                if (isEn) {
                    agentPrefix = `[WINDSURF CASCADE FLOW DIRECTIVE]\n` +
                        `Follow Cascade agentic mode. Run verification tests and guarantee regression-free code.\n\n`;
                } else {
                    agentPrefix = `[WINDSURF CASCADE FLOW DIRECTIVE]\n` +
                        `Ikuti alur Cascade agentic mode. Lakukan pengujian dan pastikan kode bersih tanpa regresi.\n\n`;
                }
            } else {
                if (isEn) {
                    agentPrefix = `[AI CODING AGENT TASK DIRECTIVE]\n` +
                        `Implement the following module according to PRD specification following enterprise standards.\n\n`;
                } else {
                    agentPrefix = `[AI CODING AGENT TASK DIRECTIVE]\n` +
                        `Implementasikan modul berikut sesuai spesifikasi PRD dengan standar enterprise.\n\n`;
                }
            }

            let sprintBody = "";
            if (sprint === 'all') {
                if (isEn) {
                    sprintBody = `MASTER ARCHITECTURAL KICKOFF: PROJECT ${business}\n` +
                        `Key Contract Specifications:\n` +
                        `1. Target Platform: ${platform}\n` +
                        `2. Data Migration: ${migration}\n` +
                        `3. Infrastructure & Hosting: ${hosting}\n` +
                        `4. Warranty & SLA: ${warranty}\n` +
                        `5. Payment Milestone Schedule: ${payment}\n` +
                        `Target Duration: ${targetWaktu}\n\n` +
                        `First Execution Steps:\n` +
                        `1. Inspect ULID database models and migrations.\n` +
                        `2. Prepare action handlers and controller business logic.\n` +
                        `3. Build interactive user interface (React Islands / Blade).\n` +
                        `4. Run php artisan test to verify contract functionality.`;
                } else {
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
                }
            } else if (sprint === 'sprint1') {
                if (isEn) {
                    sprintBody = `SPRINT 1 TASK: DATABASE MIGRATION & ULID MODELS (${business})\n` +
                        `Scope:\n` +
                        `- Create PostgreSQL migrations with ->ulid('id')->primary().\n` +
                        `- Add HasUlids trait on all related Eloquent Models.\n` +
                        `- Multilingual columns must be JSON {'id': '...', 'en': '...'} cast as 'array'.\n` +
                        `- Ensure foreign keys use foreignUlid.\n` +
                        `- Run php artisan migrate and verify database schema.`;
                } else {
                    sprintBody = `SPRINT 1 TASK: DATABASE MIGRATION & ULID MODELS (${business})\n` +
                        `Scope:\n` +
                        `- Buat migration PostgreSQL dengan primary key ->ulid('id')->primary().\n` +
                        `- Tambahkan trait HasUlids pada semua Model Eloquent terkait.\n` +
                        `- Kolom multi-bahasa wajib bertipe JSON {'id': '...', 'en': '...'} dengan cast 'array'.\n` +
                        `- Pastikan foreign key menggunakan foreignUlid.\n` +
                        `- Jalankan php artisan migrate dan verifikasi skema database.`;
                }
            } else if (sprint === 'sprint2') {
                if (isEn) {
                    sprintBody = `SPRINT 2 TASK: CORE ENGINE & ACTION HANDLERS (${business})\n` +
                        `Scope:\n` +
                        `- Create controllers and FormRequests with strict validation rules.\n` +
                        `- Use Cursor Pagination O(1) for listing queries.\n` +
                        `- Save mutations inside ACID DB::transaction().\n` +
                        `- Dispatch custom events and listeners for audit trails.`;
                } else {
                    sprintBody = `SPRINT 2 TASK: CORE ENGINE & ACTION HANDLERS (${business})\n` +
                        `Scope:\n` +
                        `- Buat controller dan FormRequest dengan validasi ketat.\n` +
                        `- Gunakan Cursor Pagination O(1) pada query daftar record.\n` +
                        `- Simpan data dengan transaksi DB::transaction() ACID.\n` +
                        `- Buat custom events dan listeners untuk audit trail.`;
                }
            } else if (sprint === 'sprint3') {
                if (isEn) {
                    sprintBody = `SPRINT 3 TASK: FRONTEND UI & INTERACTIVE ISLANDS (${business})\n` +
                        `Scope:\n` +
                        `- Apply Modern Monolith sharp technical theme (subtle corners rounded-none to rounded-sm).\n` +
                        `- Strict ban on pill/capsule shapes (rounded-full).\n` +
                        `- Provide 2-tier multilingual support (toggle ID / EN).\n` +
                        `- All phone inputs must include Country Zone (+62, +65, etc.).\n` +
                        `- Thousand numbers must use dot/comma separators.\n` +
                        `- Notifications must use window.showToast, native alert() is banned.`;
                } else {
                    sprintBody = `SPRINT 3 TASK: FRONTEND UI & INTERACTIVE ISLANDS (${business})\n` +
                        `Scope:\n` +
                        `- Terapkan desain tajam bertema Modern Monolith (subtle border-radius rounded-none s/d rounded-sm).\n` +
                        `- Dilarang keras menggunakan pill/capsule shapes (rounded-full).\n` +
                        `- Sediakan dukungan multi-bahasa 2-tier (toggle ID / EN).\n` +
                        `- Semua input telepon wajib memiliki Country Zone (+62, +65, dst).\n` +
                        `- Format ribuan wajib menggunakan pemisah titik/koma.\n` +
                        `- Notifikasi wajib menggunakan window.showToast, dilarang alert() native.`;
                }
            } else if (sprint === 'sprint4') {
                if (isEn) {
                    sprintBody = `SPRINT 4 TASK: SECURITY QUALITY GATE & AUDIT (${business})\n` +
                        `Scope:\n` +
                        `- Implement anti-bot honeypots on all intake forms.\n` +
                        `- Add rate limiters on sensitive endpoints.\n` +
                        `- Write automated PHPUnit / Pest tests for happy path and failure cases.\n` +
                        `- Run php artisan test and guarantee 100% test pass rate.`;
                } else {
                    sprintBody = `SPRINT 4 TASK: SECURITY QUALITY GATE & AUDIT (${business})\n` +
                        `Scope:\n` +
                        `- Pasang honeypot anti-bot pada setiap formulir intake.\n` +
                        `- Pasang rate limiter pada endpoint sensitif.\n` +
                        `- Tulis automated PHPUnit test untuk skenario lolos dan skenario gagal.\n` +
                        `- Jalankan php artisan test dan pastikan semua pengujian lulus 100%.`;
                }
            } else if (sprint === 'sprint5') {
                if (isEn) {
                    sprintBody = `SPRINT 5 TASK: STAGING VALIDATION & DEPLOYMENT PREPARATION (${business})\n` +
                        `Scope:\n` +
                        `- Verify frontend build: npm run build.\n` +
                        `- Verify nixpacks.toml and Nginx configurations.\n` +
                        `- Prepare deployment script ./deploy.sh 2 (Safe Migration) or ./deploy.sh 6 (Assets).\n` +
                        `- Perform comprehensive UAT before GitHub private repo handover.`;
                } else {
                    sprintBody = `SPRINT 5 TASK: STAGING VALIDATION & DEPLOYMENT PREPARATION (${business})\n` +
                        `Scope:\n` +
                        `- Verifikasi build frontend: npm run build.\n` +
                        `- Verifikasi file nixpacks.toml dan konfigurasi Nginx.\n` +
                        `- Siapkan script deployment ./deploy.sh 2 (Migrasi Aman) atau ./deploy.sh 6 (Assets).\n` +
                        `- Lakukan UAT komprehensif sebelum serah terima kunci private repo GitHub.`;
                }
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

