    <script>
        function blueprintApp() {
            return {
                blueprintSlug: '{{ $blueprint->slug }}',
                roiRecalculating: false,
                fullRegenerating: false,
                sectionCollapses: {
                    'section-00-execution-guide': false,
                    'section-1': false,
                    'section-1-5': false,
                    'section-2': false,
                    'section-3': false,
                    'section-3-5': false,
                    'section-3-8': false,
                    'section-4': false,
                    'section-5': false,
                    'section-6': false,
                    'section-7': false,
                    'section-8': false,
                    'section-9': false,
                    'section-10': false,
                    'section-13': false,
                },
                toggleSection(id) {
                    this.sectionCollapses[id] = !this.sectionCollapses[id];
                    if (!this.sectionCollapses[id]) {
                        if (id === 'section-3-8' || id === 'section-4' || id === 'section-5') {
                            setTimeout(() => { if (window.reRenderActiveMermaid) window.reRenderActiveMermaid(); }, 120);
                        }
                    }
                },
                expandAllSections() {
                    for (const sec in this.sectionCollapses) {
                        this.sectionCollapses[sec] = false;
                    }
                    setTimeout(() => { if (window.reRenderActiveMermaid) window.reRenderActiveMermaid(); }, 150);
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: 'SELURUH SEKSI DIBUKA',
                            message: '13 seksi arsitektur kini ditampilkan penuh.'
                        });
                    }
                },
                collapseAllSections() {
                    for (const sec in this.sectionCollapses) {
                        this.sectionCollapses[sec] = true;
                    }
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: 'SELURUH SEKSI DICIUTKAN',
                            message: 'Seluruh seksi telah diciutkan untuk mempermudah navigasi ringkas.'
                        });
                    }
                },
                async regenerateFullBlueprint() {
                    if (this.fullRegenerating) return;
                    this.fullRegenerating = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('/api/blueprint/' + this.blueprintSlug + '/regenerate-prd', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'PRD & ARSITEKTUR DISINTESIS ULANG',
                                    message: data.message || 'Seluruh spesifikasi dan visual chart telah diselaraskan dengan standar enterprise.'
                                });
                            }
                            setTimeout(() => {
                                window.location.href = window.location.pathname + '?regenerated=1';
                            }, 800);
                        } else {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'error',
                                    title: 'GAGAL MENSINTESIS ULANG',
                                    message: data.message || 'Terjadi kesalahan sistem saat memproses regenerasi.'
                                });
                            }
                            this.fullRegenerating = false;
                        }
                    } catch (err) {
                        console.error('Regenerate PRD error:', err);
                        window.location.href = '/blueprint/' + this.blueprintSlug + '/regenerate-full';
                    }
                },
                async recalculateRoi() {
                    if (this.roiRecalculating) return;
                    this.roiRecalculating = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('/api/blueprint/' + this.blueprintSlug + '/recalculate-roi', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'ROI & Kelayakan Diperbarui',
                                    message: 'Formula ROI dan grafik telah diselaraskan dengan visi proyek!'
                                });
                            }
                            setTimeout(() => {
                                window.location.href = window.location.pathname + '?regenerate_roi=1#section-1-5';
                            }, 500);
                        } else {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'error',
                                    title: 'Gagal Menghitung Ulang',
                                    message: data.message || 'Terjadi kesalahan saat memproses data.'
                                });
                            }
                            this.roiRecalculating = false;
                        }
                    } catch (err) {
                        console.error('ROI recalculation error:', err);
                        window.location.href = '/blueprint/' + this.blueprintSlug + '/recalculate-roi';
                    }
                },
                userMenuOpen: false, 
                paymentModalOpen: false, 
                taskEditorOpen: false,
                taskEditorSaving: false,
                taskEditorTab: 'mvp',
                analyzingTaskGaps: false,
                taskGapResult: null,

                // AI Architecture Advisory & Tech Stack Evolution State
                advisoryModalOpen: false,
                advisoryScanning: false,
                advisoryIntegrating: false,
                advisoryTab: 'missing_features',
                advisoryResult: null,
                advisorySelectedFeatures: [],
                advisorySelectedTech: [],
                isPurchasedPackage: {{ ($blueprint->isPurchasedPackage() ?? false) ? 'true' : 'false' }},

                openAdvisoryModal() {
                    this.advisoryModalOpen = true;
                    if (this.isPurchasedPackage && !this.advisoryResult && !this.advisoryScanning) {
                        this.scanArchitectureAdvisory();
                    }
                },

                async scanArchitectureAdvisory() {
                    if (this.advisoryScanning) return;
                    if (!this.isPurchasedPackage) {
                        this.advisoryModalOpen = true;
                        return;
                    }
                    this.advisoryScanning = true;
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: this.isEn ? 'SCANNING ARCHITECTURE GAPS' : 'MEMINDAI CELAH ARSITEKTUR',
                            message: this.isEn 
                                ? 'AI is inspecting project corpus and world-class 2026 engineering standards...' 
                                : 'AI sedang menganalisis dokumen acuan dan standar rekayasa dunia 2026...'
                        });
                    }
                    try {
                        const res = await fetch('{{ route('blueprint.architecture-advisory.scan', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Locale': this.isEn ? 'en' : 'id'
                            },
                            body: JSON.stringify({
                                locale: this.isEn ? 'en' : 'id'
                            })
                        });
                        const data = await res.json();
                        if (res.status === 403 || data.code === 'PREMIUM_LICENSE_REQUIRED') {
                            this.isPurchasedPackage = false;
                            this.advisoryResult = null;
                            if (window.showToast) {
                                window.showToast({
                                    type: 'warning',
                                    title: this.isEn ? 'PREMIUM LICENSE REQUIRED' : 'LISENSI RESMI DIPERLUKAN',
                                    message: data.message || 'Fitur ini eksklusif untuk lisensi berbayar.'
                                });
                            }
                            return;
                        }
                        if (data && data.success) {
                            this.advisoryResult = data;
                            this.advisorySelectedFeatures = (data.missing_features || []).map(f => f.id);
                            this.advisorySelectedTech = (data.tech_upgrades || []).map(t => t.id);
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: this.isEn ? 'ADVISORY SCAN COMPLETE' : 'PEMINDAIAN ARSITEKTUR SELESAI',
                                    message: this.isEn 
                                        ? `Discovered ${data.missing_features?.length || 0} missing features & ${data.tech_upgrades?.length || 0} tech upgrades.`
                                        : `Ditemukan ${data.missing_features?.length || 0} fitur esensial belum ada & ${data.tech_upgrades?.length || 0} modernisasi teknologi.`
                                });
                            }
                        } else {
                            throw new Error(data.message || 'Gagal memindai saran arsitektur.');
                        }
                    } catch (err) {
                        console.error('Advisory scan failed:', err);
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: this.isEn ? 'SCAN FAILED' : 'GAGAL MEMINDAI',
                                message: err.message
                            });
                        }
                    } finally {
                        this.advisoryScanning = false;
                    }
                },

                toggleAdvisoryFeature(id) {
                    const idx = this.advisorySelectedFeatures.indexOf(id);
                    if (idx > -1) {
                        this.advisorySelectedFeatures.splice(idx, 1);
                    } else {
                        this.advisorySelectedFeatures.push(id);
                    }
                },

                toggleAdvisoryTech(id) {
                    const idx = this.advisorySelectedTech.indexOf(id);
                    if (idx > -1) {
                        this.advisorySelectedTech.splice(idx, 1);
                    } else {
                        this.advisorySelectedTech.push(id);
                    }
                },

                selectAllAdvisoryFeatures(select) {
                    if (!this.advisoryResult?.missing_features) return;
                    if (select) {
                        this.advisorySelectedFeatures = this.advisoryResult.missing_features.map(f => f.id);
                    } else {
                        this.advisorySelectedFeatures = [];
                    }
                },

                async submitAdvisoryIntegration() {
                    if (this.advisoryIntegrating) return;
                    if (this.advisorySelectedFeatures.length === 0 && this.advisorySelectedTech.length === 0) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: this.isEn ? 'NO ITEMS SELECTED' : 'BELUM ADA YANG DIPILIH',
                                message: this.isEn ? 'Please select at least 1 feature or tech upgrade to integrate.' : 'Silakan pilih minimal 1 fitur atau pembaruan teknologi untuk diintegrasikan.'
                            });
                        }
                        return;
                    }

                    this.advisoryIntegrating = true;
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: this.isEn ? 'INTEGRATING INTO BLUEPRINT' : 'MENGINTEGRASIKAN KE BLUEPRINT',
                            message: this.isEn 
                                ? 'Generating 6-dimensional feature specs and sealing SHA-256 hash...' 
                                : 'Menyusun spesifikasi 6 dimensi dan memperbarui segel kriptografis SHA-256...'
                        });
                    }

                    try {
                        const res = await fetch('{{ route('blueprint.architecture-advisory.integrate', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Locale': this.isEn ? 'en' : 'id'
                            },
                            body: JSON.stringify({
                                selected_features: this.advisorySelectedFeatures,
                                selected_tech_upgrades: this.advisorySelectedTech,
                                cached_recommendations: this.advisoryResult?.missing_features || [],
                                locale: this.isEn ? 'en' : 'id'
                            })
                        });

                        const data = await res.json();
                        if (data && data.success) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: this.isEn ? 'ARCHITECTURE INTEGRATED' : 'ARSITEKTUR TERINTEGRASI',
                                    message: data.message || 'Blueprint berhasil diperbarui dengan fitur baru.'
                                });
                            }
                            this.advisoryModalOpen = false;
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            throw new Error(data.message || 'Gagal mengintegrasikan rekomendasi arsitektur.');
                        }
                    } catch (err) {
                        console.error('Integration failed:', err);
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: this.isEn ? 'INTEGRATION FAILED' : 'GAGAL INTEGRASI',
                                message: err.message
                            });
                        }
                    } finally {
                        this.advisoryIntegrating = false;
                    }
                },
                mvpTasks: @json($prd['features']['mvp_phase1'] ?? []),
                phase2Tasks: @json($prd['features']['phase2_roadmap'] ?? []),
                newTaskTitle: '',
                newTaskDesc: '',
                newTaskCategory: 'CORE DOMAIN',
                newTaskSprint: 'Sprint 1-2',
                newTaskTarget: 'mvp',
                addNewTask() {
                    const title = this.newTaskTitle.trim();
                    if (!title) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'NAMA TASK DIPERLUKAN',
                                message: 'Silakan isi nama task atau judul fitur terlebih dahulu.'
                            });
                        }
                        return;
                    }
                    const item = {
                        title: title,
                        desc: this.newTaskDesc.trim() || 'Dielaborasi secara visual sebelum dokumen dikunci.',
                        category: this.newTaskCategory || 'CORE DOMAIN',
                        sprint_phase: this.newTaskSprint || (this.newTaskTarget === 'mvp' ? 'Sprint 1-2' : 'Fase 2 Roadmap')
                    };
                    if (this.newTaskTarget === 'mvp') {
                        this.mvpTasks.push(item);
                    } else {
                        this.phase2Tasks.push(item);
                    }
                    this.newTaskTitle = '';
                    this.newTaskDesc = '';
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: 'TASK DITAMBAHKAN',
                            message: 'Task baru berhasil dimasukkan ke daftar ' + (this.newTaskTarget === 'mvp' ? 'Fase 1 (MVP)' : 'Fase 2 (Roadmap)') + '.'
                        });
                    }
                },
                removeMvpTask(idx) {
                    if (this.mvpTasks.length <= 1) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'MINIMAL 1 TASK',
                                message: 'Daftar MVP wajib memiliki minimal satu spesifikasi fitur.'
                            });
                        }
                        return;
                    }
                    this.mvpTasks.splice(idx, 1);
                },
                removePhase2Task(idx) {
                    this.phase2Tasks.splice(idx, 1);
                },
                moveTaskToPhase2(idx) {
                    if (this.mvpTasks.length <= 1) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'MINIMAL 1 TASK',
                                message: 'Daftar MVP wajib memiliki minimal satu spesifikasi fitur.'
                            });
                        }
                        return;
                    }
                    const item = this.mvpTasks.splice(idx, 1)[0];
                    item.sprint_phase = 'Fase 2 Roadmap';
                    this.phase2Tasks.push(item);
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: 'PINDAH KE ROADMAP',
                            message: '"' + item.title + '" dipindahkan ke Fase 2 (Roadmap Susulan).'
                        });
                    }
                },
                moveTaskToMvp(idx) {
                    const item = this.phase2Tasks.splice(idx, 1)[0];
                    item.sprint_phase = 'Sprint 1-2';
                    this.mvpTasks.push(item);
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: 'PINDAH KE MVP',
                            message: '"' + item.title + '" dipromosikan menjadi Fitur Wajib (Fase 1 MVP).'
                        });
                    }
                },
                async analyzeTaskGapsWithAi() {
                    if (this.analyzingTaskGaps) return;
                    this.analyzingTaskGaps = true;
                    this.taskGapResult = null;
                    const phase = this.taskEditorTab;
                    const currentTasks = phase === 'mvp' ? this.mvpTasks : this.phase2Tasks;
                    const isEnLocale = this.isEn;

                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: isEnLocale ? 'ANALYZING TASK GAPS' : 'MENGANALISIS GAP TASK & ARSITEKTUR',
                            message: isEnLocale 
                                ? 'AI is inspecting Project Documents to identify missing requirements without assumptions...' 
                                : 'AI sedang memeriksa Dokumen Acuan Proyek untuk mencari celah kebutuhan arsitektur tanpa asumsi...'
                        });
                    }

                    try {
                        const res = await fetch('{{ route('blueprint.tasks.analyze-gap', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Locale': isEnLocale ? 'en' : 'id'
                            },
                            body: JSON.stringify({
                                phase: phase,
                                current_tasks: currentTasks,
                                ai_provider: this.tweakAiProvider || 'auto',
                                locale: isEnLocale ? 'en' : 'id'
                            })
                        });

                        const data = await res.json();
                        if (data && data.success) {
                            this.taskGapResult = data;
                            if (window.showToast) {
                                if (data.is_maximal) {
                                    window.showToast({
                                        type: 'success',
                                        title: isEnLocale ? '100% COMPLETE & MAXIMAL' : 'ARSITEKTUR 100% LENGKAP & MAKSIMAL',
                                        message: data.summary || (isEnLocale 
                                            ? 'All domain requirements for this phase are fully covered!' 
                                            : 'Seluruh kebutuhan arsitektur untuk fase ini telah terpenuhi sempurna!')
                                    });
                                } else {
                                    const count = data.recommendations?.length || 0;
                                    window.showToast({
                                        type: 'warning',
                                        title: isEnLocale ? 'ARCHITECTURE GAPS FOUND' : 'DITEMUKAN CELAH KEBUTUHAN',
                                        message: isEnLocale 
                                            ? `Found ${count} essential tasks not yet covered in this phase.` 
                                            : `Ditemukan ${count} task esensial yang belum tercover di fase ini.`
                                    });
                                }
                            }
                        } else {
                            throw new Error(data.message || (isEnLocale ? 'Failed to analyze task gaps.' : 'Gagal menganalisis celah task.'));
                        }
                    } catch (err) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: isEnLocale ? 'GAP ANALYSIS FAILED' : 'ANALISIS GAP GAGAL',
                                message: err.message
                            });
                        }
                    } finally {
                        this.analyzingTaskGaps = false;
                    }
                },

                applyRecommendedTasks() {
                    if (!this.taskGapResult || !this.taskGapResult.recommendations) return;
                    const selected = this.taskGapResult.recommendations.filter(r => r.selected);
                    if (selected.length === 0) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: this.isEn ? 'NO TASKS SELECTED' : 'TIDAK ADA TASK DIPILIH',
                                message: this.isEn ? 'Please check at least one task to add.' : 'Silakan centang minimal satu task untuk ditambahkan.'
                            });
                        }
                        return;
                    }

                    const phase = this.taskEditorTab;
                    let addedCount = 0;
                    selected.forEach(r => {
                        const item = {
                            title: r.title,
                            desc: r.desc || '',
                            category: r.category || 'CORE DOMAIN',
                            sprint_phase: r.sprint_phase || (phase === 'mvp' ? 'Sprint 1-2' : 'Fase 2 Roadmap')
                        };
                        if (phase === 'mvp') {
                            this.mvpTasks.push(item);
                        } else {
                            this.phase2Tasks.push(item);
                        }
                        addedCount++;
                    });

                    this.taskGapResult = null;

                    if (window.showToast) {
                        window.showToast({
                            type: 'success',
                            title: this.isEn ? 'TASKS APPENDED' : 'TASK DITAMBAHKAN',
                            message: this.isEn 
                                ? `${addedCount} tasks successfully added. Click Save Changes to freeze.` 
                                : `${addedCount} task arsitektur berhasil dimasukkan ke daftar. Klik Simpan Perubahan untuk mengunci.`
                        });
                    }
                },

                async submitSaveTasks() {
                    if (this.mvpTasks.length === 0) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'MINIMAL 1 TASK',
                                message: 'Daftar MVP harus memiliki minimal 1 task.'
                            });
                        }
                        return;
                    }
                    try {
                        const res = await window.saveBlueprintTasks('{{ $blueprint->slug }}', this.mvpTasks, this.phase2Tasks, () => { this.taskEditorSaving = true; }, () => { this.taskEditorSaving = false; });
                        if (res && res.success) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'TASK BERHASIL DISIMPAN',
                                    message: res.message || 'Perubahan spesifikasi fitur berhasil disimpan. Memuat ulang...'
                                });
                            }
                            this.taskEditorOpen = false;
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } catch(err) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'GAGAL MENYIMPAN',
                                message: err.message || 'Terjadi kesalahan sistem saat menyimpan perubahan task.'
                            });
                        }
                    }
                },
                scaffoldModalOpen: false,
                showExecutionGuideModal: false,
                executionGuideTab: 'choice-a',
                workspaceDir: (function() {
                    var saved = localStorage.getItem('neriah_workspace_{{ $blueprint->slug }}');
                    return saved !== null ? saved : '';
                })(),
                sanitizeFolderName(val) {
                    if (!val) return '';
                    // 1. Replace spaces with hyphens
                    var str = String(val).replace(/\s+/g, '-');
                    // 2. Remove all characters except a-z, 0-9, hyphen (-), and underscore (_)
                    str = str.replace(/[^a-zA-Z0-9_-]/g, '');
                    // 3. Prevent multiple consecutive hyphens (e.g. --- becomes -)
                    str = str.replace(/-+/g, '-');
                    // 4. Force lowercase
                    return str.toLowerCase();
                },
                setWorkspaceDir(val) {
                    var sanitized = this.sanitizeFolderName(val);
                    this.workspaceDir = sanitized;
                    localStorage.setItem('neriah_workspace_{{ $blueprint->slug }}', sanitized);
                },
                handleFolderInput(e) {
                    var raw = (e && e.target) ? e.target.value : (e || '');
                    var sanitized = this.sanitizeFolderName(raw);
                    this.setWorkspaceDir(sanitized);
                },
                insertHyphen(e) {
                    var target = e.target;
                    var start = target.selectionStart || 0;
                    var end = target.selectionEnd || 0;
                    var current = target.value || '';
                    if (start > 0 && current.charAt(start - 1) === '-') {
                        return;
                    }
                    var nextVal = current.substring(0, start) + '-' + current.substring(end);
                    this.setWorkspaceDir(nextVal);
                    this.$nextTick(function() {
                        if (target && target.setSelectionRange) {
                            target.setSelectionRange(start + 1, start + 1);
                        }
                    });
                },
                getEffectiveFolder() {
                    var clean = (this.workspaceDir || '').trim().replace(/^-+|-+$/g, '');
                    return clean ? clean : '{{ \App\Services\ScaffoldGeneratorService::deriveShortSlug($blueprint->slug ?? "app", $blueprint) }}';
                },
                getScaffoldDownloadUrl() {
                    var folder = (this.workspaceDir || '').trim().replace(/^-+|-+$/g, '');
                    var base = '{{ route("blueprint.export-scaffold", $blueprint->slug) }}';
                    return folder ? (base + '?folder=' + encodeURIComponent(folder)) : base;
                },
                scaffoldJobStatus: '{{ !empty($blueprint->user_metadata['scaffold_ready']) ? 'completed' : 'idle' }}',
                scaffoldJobProgress: {{ !empty($blueprint->user_metadata['scaffold_ready']) ? 100 : 0 }},
                scaffoldJobMessage: '{{ !empty($blueprint->user_metadata['scaffold_ready']) ? 'Arsip Starter Kit ZIP siap diunduh' : '' }}',
                scaffoldJobPolling: false,

                downloadCenterModalOpen: false,
                artifactsList: [],
                artifactsLoading: false,
                requestingType: null,
                activeDownloadsCount: 0,
                countdownTickerInterval: null,
                artifactsPolling: false,

                openDownloadCenterModal() {
                    this.downloadCenterModalOpen = true;
                    this.fetchDownloadArtifacts();
                    this.ensureCountdownTicker();
                },

                triggerPdfDownload() {
                    this.openDownloadCenterModal();
                    this.requestDownloadArtifact('pdf');
                },

                triggerMdDownload() {
                    this.openDownloadCenterModal();
                    this.requestDownloadArtifact('markdown');
                },

                ensureCountdownTicker() {
                    if (this.countdownTickerInterval) return;
                    this.countdownTickerInterval = setInterval(() => {
                        if (!this.artifactsList || !this.artifactsList.length) return;
                        this.artifactsList.forEach(item => {
                            if (item.remaining_seconds && item.remaining_seconds > 0) {
                                item.remaining_seconds -= 1;
                                item.percentage_remaining = Math.max(0, Math.round((item.remaining_seconds / (24 * 3600)) * 100));
                            } else if (item.status === 'completed' && !item.is_expired) {
                                item.is_expired = true;
                                item.status = 'expired';
                            }
                        });
                        this.updateActiveDownloadsCount();
                    }, 1000);
                },

                formatSecondsCountdown(secs) {
                    if (!secs || secs <= 0) return this.locale === 'en' ? '00h 00m 00s' : '00j 00m 00d';
                    const h = Math.floor(secs / 3600);
                    const m = Math.floor((secs % 3600) / 60);
                    const s = secs % 60;
                    const pad = (n) => String(n).padStart(2, '0');
                    if (this.locale === 'en') {
                        return `${pad(h)}h ${pad(m)}m ${pad(s)}s`;
                    }
                    return `${pad(h)}j ${pad(m)}m ${pad(s)}d`;
                },

                updateActiveDownloadsCount() {
                    this.activeDownloadsCount = this.artifactsList.filter(a => a.status === 'completed' && !a.is_expired).length;
                },

                async fetchDownloadArtifacts() {
                    this.artifactsLoading = true;
                    try {
                        const url = '{{ route('blueprint.artifacts.list', $blueprint->slug) }}?locale=' + (this.locale || 'id');
                        const res = await fetch(url, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.artifactsList = data.artifacts || [];
                            this.updateActiveDownloadsCount();

                            // If any artifact is queued or processing, start polling
                            const hasRunning = this.artifactsList.some(a => a.status === 'queued' || a.status === 'processing');
                            if (hasRunning) {
                                this.pollArtifactsStatus();
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to fetch download artifacts:', e);
                    } finally {
                        this.artifactsLoading = false;
                    }
                },

                async requestDownloadArtifact(type, force = false) {
                    this.requestingType = type;
                    const typeLabel = type === 'pdf' ? 'PDF' : (type === 'markdown' ? 'Markdown' : 'Starter Kit');
                    
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: this.locale === 'en' ? 'GENERATION QUEUED' : 'GENERASI DIJADWALKAN',
                            message: this.locale === 'en' 
                                ? `Rendering ${typeLabel} in background queue...` 
                                : `Memproses berkas ${typeLabel} di antrean server...`
                        });
                    }

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('{{ route('blueprint.artifacts.request', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({
                                artifact_type: type,
                                locale: this.locale,
                                force: force ? 1 : 0
                            })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            await this.fetchDownloadArtifacts();
                            this.ensureCountdownTicker();

                            if (data.artifact && data.artifact.status === 'completed') {
                                if (window.showToast) {
                                    window.showToast({
                                        type: 'success',
                                        title: this.locale === 'en' ? 'FILE READY' : 'BERKAS SIAP DIUNDUH',
                                        message: this.locale === 'en' ? 'Artifact is ready to download.' : 'Berkas unduhan siap diunduh!'
                                    });
                                }
                            } else {
                                this.pollArtifactsStatus();
                            }
                        } else {
                            throw new Error(data.message || 'Gagal menjadwalkan pembuatan berkas.');
                        }
                    } catch (err) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: this.locale === 'en' ? 'REQUEST FAILED' : 'PERMINTAAN GAGAL',
                                message: err.message || (this.locale === 'en' ? 'Error generating artifact' : 'Terjadi kesalahan sistem.')
                            });
                        }
                    } finally {
                        this.requestingType = null;
                    }
                },

                async pollArtifactsStatus() {
                    if (this.artifactsPolling) return;
                    this.artifactsPolling = true;

                    const checkRunning = async () => {
                        const runningItems = this.artifactsList.filter(a => a.status === 'queued' || a.status === 'processing');
                        if (!runningItems.length) {
                            this.artifactsPolling = false;
                            return;
                        }

                        for (const item of runningItems) {
                            try {
                                const pollUrl = item.poll_url + '?locale=' + (this.locale || 'id');
                                const res = await fetch(pollUrl, { headers: { 'Accept': 'application/json' } });
                                const data = await res.json();
                                if (data && data.success && data.artifact) {
                                    const idx = this.artifactsList.findIndex(a => a.id === item.id);
                                    if (idx !== -1) {
                                        this.artifactsList[idx] = data.artifact;
                                        if (data.artifact.status === 'completed') {
                                            this.updateActiveDownloadsCount();
                                            if (window.showToast) {
                                                window.showToast({
                                                    type: 'success',
                                                    title: this.locale === 'en' ? 'FILE READY' : 'BERKAS SIAP DIUNDUH',
                                                    message: (this.locale === 'en' ? 'Download ready: ' : 'Berkas siap diunduh: ') + data.artifact.filename
                                                });
                                            }
                                        }
                                    }
                                }
                            } catch (e) {
                                console.warn('Poll artifact failed:', e);
                            }
                        }

                        const stillRunning = this.artifactsList.some(a => a.status === 'queued' || a.status === 'processing');
                        if (stillRunning) {
                            setTimeout(checkRunning, 2500);
                        } else {
                            this.artifactsPolling = false;
                        }
                    };

                    checkRunning();
                },

                async deleteDownloadArtifact(id) {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const url = '{{ route('blueprint.artifacts.delete', ['slug' => $blueprint->slug, 'id' => 'ARTIFACT_ID']) }}'.replace('ARTIFACT_ID', id);
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            }
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.artifactsList = this.artifactsList.filter(a => a.id !== id);
                            this.updateActiveDownloadsCount();
                            if (window.showToast) {
                                window.showToast({
                                    type: 'info',
                                    title: this.locale === 'en' ? 'PURGED' : 'DIHAPUS',
                                    message: this.locale === 'en' ? 'Artifact purged from server.' : 'Berkas berhasil dihapus dari server.'
                                });
                            }
                        }
                    } catch (e) {
                        console.error('Failed to delete artifact:', e);
                    }
                },

                copyExecutionPrompt(text, title = 'PROMPT DISALIN') {
                    if (!text) return;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(() => {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: title,
                                    message: 'Perintah berhasil disalin ke clipboard! Silakan tempelkan ke chat AI Agent.'
                                });
                            }
                        }).catch(() => {
                            this.fallbackCopyText(text, title);
                        });
                    } else {
                        this.fallbackCopyText(text, title);
                    }
                },

                fallbackCopyText(text, title) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.focus();
                    ta.select();
                    try {
                        document.execCommand('copy');
                        if (window.showToast) {
                            window.showToast({
                                type: 'success',
                                title: title,
                                message: 'Perintah berhasil disalin ke clipboard!'
                            });
                        }
                    } catch (e) {
                        console.error('Fallback copy failed', e);
                    }
                    document.body.removeChild(ta);
                },

                async startBackgroundScaffoldGeneration(instant = false) {
                    if (!instant && (this.scaffoldJobStatus === 'processing' || this.scaffoldJobStatus === 'queued')) return;
                    this.scaffoldJobStatus = instant ? 'processing' : 'queued';
                    this.scaffoldJobProgress = instant ? 25 : 5;
                    this.scaffoldJobMessage = instant 
                        ? (this.locale === 'en' ? 'Processing scaffold generation synchronously...' : 'Memproses generasi scaffold secara instan...')
                        : (this.locale === 'en' ? 'Scheduling job to server queue...' : 'Menjadwalkan job ke antrean server...');

                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: instant ? (this.locale === 'en' ? 'PROCESSING SCAFFOLD' : 'MEMPROSES SCAFFOLD') : (this.locale === 'en' ? 'BACKGROUND JOB QUEUED' : 'BACKGROUND JOB DIJADWALKAN'),
                            message: instant 
                                ? (this.locale === 'en' ? 'Generating 22+ files and packaging ZIP starter kit now.' : 'Membuat 22+ file dan mengemas ZIP starter kit sekarang.')
                                : (this.locale === 'en' ? 'Scaffold generation queued in server background.' : 'Pembuatan 22+ file & Docker compose sedang diproses di antrean server.')
                        });
                    }

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const folder = this.getEffectiveFolder();
                        const url = '{{ route('blueprint.scaffold.generate-async', $blueprint->slug) }}' + (instant ? '?instant=1' : '');
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({
                                folder: folder
                            })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            if (data.status === 'completed') {
                                this.scaffoldJobStatus = 'completed';
                                this.scaffoldJobProgress = 100;
                                this.scaffoldJobMessage = data.message || (this.locale === 'en' ? 'Scaffold starter kit ready to download.' : 'Scaffold starter kit siap diunduh.');
                                if (window.showToast) {
                                    window.showToast({
                                        type: 'success',
                                        title: this.locale === 'en' ? 'SCAFFOLD READY' : 'SCAFFOLD SELESAI DIGENERATE',
                                        message: this.locale === 'en' ? 'Starter kit ZIP archive is ready to download!' : 'Arsip ZIP Starter Kit siap diunduh!'
                                    });
                                }
                            } else {
                                this.pollScaffoldJobStatus();
                            }
                        } else {
                            throw new Error(data.message || 'Gagal menjadwalkan job');
                        }
                    } catch (err) {
                        this.scaffoldJobStatus = 'failed';
                        this.scaffoldJobMessage = err.message || (this.locale === 'en' ? 'Failed to start background job.' : 'Gagal memulai background job.');
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: this.locale === 'en' ? 'FAILED TO START JOB' : 'GAGAL MEMULAI JOB',
                                message: err.message || (this.locale === 'en' ? 'System error occurred.' : 'Terjadi kesalahan sistem.')
                            });
                        }
                    }
                },

                async pollScaffoldJobStatus() {
                    if (this.scaffoldJobPolling) return;
                    this.scaffoldJobPolling = true;

                    const checkStatus = async () => {
                        try {
                            const res = await fetch('{{ route('blueprint.scaffold.job-status', $blueprint->slug) }}', {
                                headers: { 'Accept': 'application/json' }
                            });
                            const data = await res.json();
                            if (data && data.success) {
                                this.scaffoldJobStatus = data.status || this.scaffoldJobStatus;
                                this.scaffoldJobProgress = data.progress !== undefined ? data.progress : this.scaffoldJobProgress;
                                this.scaffoldJobMessage = data.message || this.scaffoldJobMessage;

                                if (data.status === 'completed') {
                                    this.scaffoldJobPolling = false;
                                    if (window.showToast) {
                                        window.showToast({
                                            type: 'success',
                                            title: 'SCAFFOLD SELESAI DIGENERATE',
                                            message: 'Arsip ZIP Starter Kit siap diunduh!'
                                        });
                                    }
                                    return;
                                }

                                if (data.status === 'failed') {
                                    this.scaffoldJobPolling = false;
                                    return;
                                }
                            }
                        } catch (err) {
                            console.warn('Scaffold status check failed:', err);
                        }

                        // Poll again in 2.5 seconds if still running
                        if (this.scaffoldJobStatus === 'processing' || this.scaffoldJobStatus === 'queued') {
                            setTimeout(checkStatus, 2500);
                        } else {
                            this.scaffoldJobPolling = false;
                        }
                    };

                    checkStatus();
                },
                crModalOpen: false,
                prdData: @json($prd),

                // Diagram Interactive Viewer State (Zoom, Pan, Fullscreen Modal)
                fullscreenDiagramModal: false,
                fullscreenDiagramTitle: '',
                fullscreenDiagramSourceId: '',
                fullscreenDiagramZoom: 1,
                fullscreenDiagramPanX: 0,
                fullscreenDiagramPanY: 0,
                fullscreenDiagramDragging: false,
                fullscreenDiagramNaturalWidth: 1200,
                fullscreenDiagramNaturalHeight: 800,
                fsDragStartX: 0,
                fsDragStartY: 0,

                zoomDiagram(targetId, delta) {
                    const el = document.getElementById(targetId);
                    if (!el) return;
                    let currentZoom = parseFloat(el.getAttribute('data-zoom') || '1');
                    currentZoom = Math.min(3.5, Math.max(0.4, +(currentZoom + delta).toFixed(2)));
                    el.setAttribute('data-zoom', currentZoom);
                    this.applyDiagramTransform(targetId);
                },
                resetDiagram(targetId) {
                    const el = document.getElementById(targetId);
                    if (!el) return;
                    el.setAttribute('data-zoom', '1');
                    el.setAttribute('data-pan-x', '0');
                    el.setAttribute('data-pan-y', '0');
                    this.applyDiagramTransform(targetId);
                },
                applyDiagramTransform(targetId) {
                    const el = document.getElementById(targetId);
                    if (!el) return;
                    const zoom = parseFloat(el.getAttribute('data-zoom') || '1');
                    const px = parseFloat(el.getAttribute('data-pan-x') || '0');
                    const py = parseFloat(el.getAttribute('data-pan-y') || '0');
                    const svg = el.querySelector('svg');
                    if (svg) {
                        svg.style.transform = `translate(${px}px, ${py}px) scale(${zoom})`;
                        svg.style.transformOrigin = 'center center';
                        svg.style.transition = el.getAttribute('data-dragging') === 'true' ? 'none' : 'transform 0.15s ease-out';
                        if (zoom === 1 && px === 0 && py === 0) {
                            svg.style.maxWidth = '100%';
                            svg.style.maxHeight = '100%';
                            svg.style.width = 'auto';
                            svg.style.height = 'auto';
                        } else {
                            svg.style.maxWidth = 'none';
                            svg.style.maxHeight = 'none';
                        }
                    }
                },
                startDiagramDrag(targetId, e) {
                    const el = document.getElementById(targetId);
                    if (!el) return;
                    el.setAttribute('data-dragging', 'true');
                    const px = parseFloat(el.getAttribute('data-pan-x') || '0');
                    const py = parseFloat(el.getAttribute('data-pan-y') || '0');
                    el.setAttribute('data-start-x', e.clientX - px);
                    el.setAttribute('data-start-y', e.clientY - py);
                },
                onDiagramDrag(targetId, e) {
                    const el = document.getElementById(targetId);
                    if (!el || el.getAttribute('data-dragging') !== 'true') return;
                    const startX = parseFloat(el.getAttribute('data-start-x') || '0');
                    const startY = parseFloat(el.getAttribute('data-start-y') || '0');
                    el.setAttribute('data-pan-x', e.clientX - startX);
                    el.setAttribute('data-pan-y', e.clientY - startY);
                    this.applyDiagramTransform(targetId);
                },
                stopDiagramDrag(targetId) {
                    const el = document.getElementById(targetId);
                    if (!el) return;
                    el.setAttribute('data-dragging', 'false');
                    this.applyDiagramTransform(targetId);
                },
                openDiagramFullscreen(title, sourceId) {
                    this.fullscreenDiagramTitle = title;
                    this.fullscreenDiagramSourceId = sourceId;
                    this.fullscreenDiagramPanX = 0;
                    this.fullscreenDiagramPanY = 0;
                    this.fullscreenDiagramModal = true;
                    this.$nextTick(async () => {
                        const fsTarget = document.getElementById('mermaid-fullscreen-target');
                        if (!fsTarget) return;

                        // 1. Instant preview: If existing SVG is available, clone it and ensure scoped styles match
                        const targetId = sourceId.replace(/-source$/, '-target');
                        const origTarget = document.getElementById(targetId);
                        const existingSvg = origTarget ? origTarget.querySelector('svg') : null;

                        if (existingSvg) {
                            const origSvgId = existingSvg.id;
                            const newSvgId = 'fs_svg_' + Date.now();
                            const clonedSvg = existingSvg.cloneNode(true);
                            clonedSvg.id = newSvgId;
                            if (origSvgId) {
                                clonedSvg.querySelectorAll('style').forEach(st => {
                                    if (st && st.textContent) {
                                        st.textContent = st.textContent.split('#' + origSvgId).join('#' + newSvgId);
                                    }
                                });
                            }
                            fsTarget.innerHTML = '';
                            fsTarget.appendChild(clonedSvg);
                        } else {
                            fsTarget.innerHTML = '<div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center justify-center gap-2 py-16"><span class="w-2.5 h-2.5 bg-emerald-500 animate-ping"></span><span>Menyiapkan diagram resolusi tinggi...</span></div>';
                        }

                        // 2. Perform pristine full-definition fresh render directly for fullscreen
                        await window.renderMermaidDiagram('mermaid-fullscreen-target', sourceId, true);
                        const activeSvg = fsTarget.querySelector('svg');

                        if (activeSvg) {
                            // Determine natural SVG dimensions from viewBox
                            let natW = 1200;
                            let natH = 800;
                            const vbAttr = activeSvg.getAttribute('viewBox');
                            if (vbAttr) {
                                const parts = vbAttr.trim().split(/[\s,]+/).map(Number);
                                if (parts.length >= 4 && parts[2] > 0 && parts[3] > 0) {
                                    natW = parts[2];
                                    natH = parts[3];
                                }
                            } else if (activeSvg.viewBox && activeSvg.viewBox.baseVal && activeSvg.viewBox.baseVal.width > 0) {
                                natW = activeSvg.viewBox.baseVal.width;
                                natH = activeSvg.viewBox.baseVal.height;
                            }

                            this.fullscreenDiagramNaturalWidth = natW;
                            this.fullscreenDiagramNaturalHeight = natH;

                            activeSvg.setAttribute('width', natW);
                            activeSvg.setAttribute('height', natH);
                            activeSvg.style.width = natW + 'px';
                            activeSvg.style.height = natH + 'px';
                            activeSvg.style.maxWidth = 'none';
                            activeSvg.style.maxHeight = 'none';
                            activeSvg.style.display = 'block';
                            activeSvg.style.margin = 'auto';
                            activeSvg.style.transform = 'none';
                            activeSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');

                            requestAnimationFrame(() => {
                                setTimeout(() => {
                                    this.fitFullscreenDiagram();
                                }, 50);
                            });
                        }
                    });
                },
                closeDiagramFullscreen() {
                    this.fullscreenDiagramModal = false;
                    this.fullscreenDiagramZoom = 1;
                    this.fullscreenDiagramPanX = 0;
                    this.fullscreenDiagramPanY = 0;
                    const fsTarget = document.getElementById('mermaid-fullscreen-target');
                    if (fsTarget) fsTarget.innerHTML = '';
                },
                fitFullscreenDiagram() {
                    const canvas = document.getElementById('mermaid-fullscreen-canvas');
                    let canvasW = canvas && canvas.clientWidth > 400 ? canvas.clientWidth : (window.innerWidth - 64);
                    let canvasH = canvas && canvas.clientHeight > 300 ? canvas.clientHeight : (window.innerHeight - 140);
                    
                    canvasW = Math.max(300, canvasW - 48);
                    canvasH = Math.max(200, canvasH - 48);

                    const natW = this.fullscreenDiagramNaturalWidth || 1200;
                    const natH = this.fullscreenDiagramNaturalHeight || 800;

                    const scaleX = canvasW / natW;
                    const scaleY = canvasH / natH;
                    const fitScale = Math.min(scaleX, scaleY);
                    
                    this.fullscreenDiagramZoom = +Math.max(0.01, Math.min(3.0, fitScale)).toFixed(3);
                    this.fullscreenDiagramPanX = 0;
                    this.fullscreenDiagramPanY = 0;
                },
                zoomFullscreenDiagram(delta) {
                    const factor = delta > 0 ? 1.25 : 0.8;
                    const newZoom = +(this.fullscreenDiagramZoom * factor).toFixed(3);
                    this.fullscreenDiagramZoom = Math.min(30.0, Math.max(0.01, newZoom));
                },
                setFullscreenZoom(val) {
                    this.fullscreenDiagramZoom = Math.min(30.0, Math.max(0.01, +val.toFixed(3)));
                },
                onFullscreenWheel(e) {
                    const factor = e.deltaY < 0 ? 1.18 : 0.847;
                    const newZoom = Math.min(30.0, Math.max(0.01, +(this.fullscreenDiagramZoom * factor).toFixed(3)));
                    this.fullscreenDiagramZoom = newZoom;
                },
                resetFullscreenDiagram() {
                    this.fullscreenDiagramZoom = 1;
                    this.fullscreenDiagramPanX = 0;
                    this.fullscreenDiagramPanY = 0;
                },
                startFullscreenDrag(e) {
                    this.fullscreenDiagramDragging = true;
                    this.fsDragStartX = e.clientX - this.fullscreenDiagramPanX;
                    this.fsDragStartY = e.clientY - this.fullscreenDiagramPanY;
                },
                onFullscreenDrag(e) {
                    if (!this.fullscreenDiagramDragging) return;
                    this.fullscreenDiagramPanX = e.clientX - this.fsDragStartX;
                    this.fullscreenDiagramPanY = e.clientY - this.fsDragStartY;
                },
                stopFullscreenDrag() {
                    this.fullscreenDiagramDragging = false;
                },

                // Tweak & Grounded AI Deepener Feature
                tweakModalOpen: false,
                tweakSegmentKey: '',
                tweakSegmentTitle: '',
                tweakMode: 'ai',
                tweakContent: '',
                tweakPrompt: '',
                tweakLoading: false,
                tweakAiProvider: 'auto',
                tweakFeatureId: null,
                tweakSpecIndex: null,
                elaboratingDirective: false,
                sourceCorpusLoading: false,
                sourceCorpusData: null,
                sourceCorpusExpanded: false,
                sourceCorpusFullView: false,
                async loadSourceCorpus() {
                    if (this.sourceCorpusData) return;
                    this.sourceCorpusLoading = true;
                    try {
                        const res = await fetch('{{ route('api.blueprint.source-corpus', $blueprint->slug) }}');
                        const json = await res.json();
                        if (json && json.success) {
                            this.sourceCorpusData = json.data;
                        }
                    } catch(e) {}
                    finally {
                        this.sourceCorpusLoading = false;
                    }
                },
                openTweakFeature(featKey, specIdx, customTitle = null) {
                    let spec = null;
                    if (this.prdData?.features?.mvp_phase1?.[specIdx]) {
                        spec = this.prdData.features.mvp_phase1[specIdx];
                    } else if (this.prdData?.engineering_specs?.mvp_specs?.[specIdx]) {
                        spec = this.prdData.engineering_specs.mvp_specs[specIdx];
                    } else if (this.prdData?.features?.[featKey]?.[specIdx]) {
                        spec = this.prdData.features[featKey][specIdx];
                    } else if (Array.isArray(this.prdData?.features) && this.prdData.features[specIdx]) {
                        spec = this.prdData.features[specIdx];
                    }
                    const title = customTitle || (spec?.title ? ('Fitur: ' + spec.title) : (featKey ? ('Fitur: ' + featKey) : 'Perdalam Fitur'));
                    this.openTweakModal('feature_single', title, spec || {}, featKey, specIdx);
                },
                openTweakModal(key, title, content = null, featureId = null, specIndex = null) {
                    @php
                        $canEditBlueprint = !($isScopeLocked ?? false)
                            || $blueprint->project_status === 'Retail License'
                            || (method_exists($blueprint, 'isRetailTrack') && $blueprint->isRetailTrack())
                            || (method_exists($blueprint, 'hasPaidRetailLicense') && $blueprint->hasPaidRetailLicense())
                            || (auth()->user()?->isSuperAdmin() ?? false);
                    @endphp
                    if (!{{ $canEditBlueprint ? 'true' : 'false' }}) {
                        if (window.showToast) {
                            window.showToast({ type: 'warning', title: 'AKSES DITOLAK', message: 'Dokumen berada dalam status Scope Lock (Kontrak Studio).' });
                        }
                        return;
                    }
                    if (content === null || content === undefined) {
                        if (key === 'system_actors') {
                            content = this.prdData?.system_actors ?? [];
                        } else if (key === 'features') {
                            content = this.prdData?.engineering_specs?.mvp_specs ?? (this.prdData?.features?.mvp_phase1 ?? (this.prdData?.features ?? []));
                        } else if (key === 'alurs_kerja') {
                            content = this.prdData?.alurs_kerja ?? [];
                        } else if (key === 'database_erd') {
                            content = this.prdData?.erd_schema?.tables ?? (this.prdData?.database_erd ?? []);
                        } else if (key === 'executive_summary') {
                            content = this.prdData?.executive_summary ?? [];
                        } else if (key === 'custom_ai_agent_rules') {
                            content = this.prdData?.custom_ai_agent_rules ?? '';
                        }
                    }
                    this.tweakSegmentKey = key;
                    this.tweakSegmentTitle = title;
                    this.tweakContent = typeof content === 'object' ? JSON.stringify(content, null, 2) : (content || '');
                    this.tweakMode = 'ai';
                    this.tweakPrompt = '';
                    this.tweakFeatureId = featureId;
                    this.tweakSpecIndex = specIndex;
                    this.tweakModalOpen = true;
                    this.loadSourceCorpus();

                    if (window.showToast) {
                        if (key === 'database_erd') {
                            const tableList = Array.isArray(content) ? content : (content?.tables || []);
                            window.showToast({
                                type: 'info',
                                title: 'STUDIO ELABORASI ERD TERBUKA',
                                message: 'Memuat ' + tableList.length + ' tabel skema database Strict ULID. Siap untuk dielaborasi dan diselaraskan dengan Dokumen Acuan.'
                            });
                        } else if (key === 'system_actors') {
                            const count = Array.isArray(content) ? content.length : 0;
                            window.showToast({
                                type: 'info',
                                title: 'STUDIO ELABORASI RBAC TERBUKA',
                                message: 'Memuat ' + count + ' peran pengguna & hak akses. Siap untuk dielaborasi berbasis Dokumen Acuan.'
                            });
                        } else if (key === 'features' || key === 'feature_single') {
                            window.showToast({
                                type: 'info',
                                title: 'STUDIO ELABORASI FITUR TERBUKA',
                                message: 'Editor pendalaman spesifikasi modul MVP siap menerima instruksi penyempurnaan AI.'
                            });
                        } else if (key === 'alurs_kerja') {
                            window.showToast({
                                type: 'info',
                                title: 'STUDIO ELABORASI ALUR KERJA TERBUKA',
                                message: 'Memuat tahapan user flow dan logika bisnis untuk dielaborasi lebih dalam.'
                            });
                        } else if (key === 'scaffold_file') {
                            window.showToast({
                                type: 'info',
                                title: 'STUDIO ELABORASI BERKAS TERBUKA',
                                message: 'Berkas ' + this.scaffoldActiveTab + ' siap dielaborasi berbasis Dokumen Acuan.'
                            });
                        }
                    }
                },
                async submitTweak() {
                    this.tweakLoading = true;
                    try {
                        if (this.tweakSegmentKey === 'scaffold_file') {
                            const res = await fetch('{{ route('blueprint.scaffold.refine-file', $blueprint->slug) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Locale': this.isEn ? 'en' : 'id',
                                },
                                body: JSON.stringify({
                                    filename: this.scaffoldActiveTab,
                                    prompt: this.tweakPrompt,
                                    ai_provider: this.tweakAiProvider,
                                    locale: this.isEn ? 'en' : 'id',
                                })
                            });
                            const data = await res.json();
                            if (data && data.success) {
                                this.scaffoldFiles[this.scaffoldActiveTab] = data.content;
                                if (!this.scaffoldCustomizedFiles.includes(this.scaffoldActiveTab)) {
                                    this.scaffoldCustomizedFiles.push(this.scaffoldActiveTab);
                                }
                                if (Array.isArray(data.synced_files)) {
                                    data.synced_files.forEach((sf) => {
                                        this.scaffoldFiles[sf] = data.content;
                                        if (!this.scaffoldCustomizedFiles.includes(sf)) {
                                            this.scaffoldCustomizedFiles.push(sf);
                                        }
                                        this.scaffoldTelemetry[sf] = {
                                            maturity_score: data.maturity_score || 95,
                                            is_maximal: !!data.is_maximal,
                                            added_lines: data.added_lines || 0,
                                            line_count: data.line_count || 0,
                                            iterations: data.iterations || 1,
                                            synced_files: data.synced_files || [sf],
                                        };
                                    });
                                }
                                if (window.showToast) {
                                    window.showToast({ 
                                        type: 'success', 
                                        title: this.isEn ? 'SCAFFOLD FILE DEEPENED' : 'BERKAS SCAFFOLD DIPERDALAM', 
                                        message: data.message || ('Berkas ' + this.scaffoldActiveTab + ' berhasil diperdalam dari Dokumen Acuan.') 
                                    });
                                }
                                this.tweakModalOpen = false;
                                return;
                            } else {
                                throw new Error(data.message || 'Gagal memperdalam file scaffold.');
                            }
                        }

                        const res = await fetch('{{ route('api.blueprint.tweak-segment', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                segment_key: this.tweakSegmentKey,
                                mode: this.tweakMode,
                                prompt: this.tweakPrompt,
                                content: this.tweakContent,
                                ai_provider: this.tweakAiProvider,
                                feature_id: this.tweakFeatureId,
                                spec_index: this.tweakSpecIndex,
                            })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            if (window.showToast) {
                                window.showToast({ 
                                    type: 'success', 
                                    title: 'BERHASIL DIPERDALAM', 
                                    message: data.message || 'Segmen berhasil diperbarui berdasarkan Dokumen Acuan.' 
                                });
                            }
                            this.tweakModalOpen = false;
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            throw new Error(data.message || 'Gagal memperdalam segmen.');
                        }
                    } catch(err) {
                        if (window.showToast) {
                            window.showToast({ type: 'error', title: 'ERROR PENAJAMAN', message: err.message });
                        }
                    } finally {
                        this.tweakLoading = false;
                    }
                },
                async autoElaborateFeatureTo100() {
                    if (this.tweakLoading) return;
                    this.tweakMode = 'ai';
                    
                    const featName = this.tweakSegmentTitle || this.tweakFeatureId || (this.isEn ? 'This Feature' : 'Fitur Ini');
                    const featIdTag = this.tweakFeatureId ? ` [${this.tweakFeatureId}]` : '';
                    
                    const dynamicPrompt = this.isEn
                        ? `Deepen all 6 dimensions of '${featName}'${featIdTag} to 100% production maturity: synthesize grounded English user story for relevant personas, guarantee 4+ bounded target files specifically for ${featName}, provide 3 concrete Gherkin scenarios (Happy Path, 422 Validation Error, and Concurrency Locking), frontend anti-AI-slop states, backend PostgreSQL/MySQL Strict ULID & Keyset cursor O(1), and RFC 9457 API schemas tailored to ${featName}.`
                        : `Perdalam seluruh 6 dimensi fitur '${featName}'${featIdTag} ke kematangan produksi 100%: lengkapi user story persona aktor yang relevan, pastikan 4+ bounded target files khusus untuk ${featName}, sediakan 3 skenario Gherkin konkret (Happy Path, 422 Validasi Error, dan Konkurensi Database Locking), frontend anti-AI-slop states, backend PostgreSQL/MySQL Strict ULID & Keyset cursor O(1), dan skema API RFC 9457 khusus ${featName}.`;
                    
                    if (this.tweakPrompt && this.tweakPrompt.trim().length > 15) {
                        if (!this.tweakPrompt.includes(featName)) {
                            this.tweakPrompt = `${this.tweakPrompt.trim()}\n\n${dynamicPrompt}`;
                        }
                    } else {
                        this.tweakPrompt = dynamicPrompt;
                    }

                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: this.isEn ? `AUTO-ELABORATING: ${featName.toUpperCase()}` : `MEMPERDALAM: ${featName.toUpperCase()}`,
                            message: this.isEn 
                                ? `Synthesizing 6 dimensions of '${featName}' to achieve 100% maximal production maturity...` 
                                : `Membedah dokumen acuan dan menyempurnakan 6 dimensi '${featName}' menuju kematangan 100%...`
                        });
                    }
                    await this.submitTweak();
                },
                async elaborateDirectiveFromAi() {
                    if (this.elaboratingDirective) return;
                    this.elaboratingDirective = true;
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: this.isEn ? 'COMPILING REFINEMENT DIRECTIVE' : 'MENYUSUN DRAF INSTRUKSI ARSITEKTUR',
                            message: this.isEn
                                ? 'AI is analyzing grounded project corpus and world engineering standards...'
                                : 'AI sedang menganalisis dokumen acuan proyek klien dan menyelaraskannya dengan standar industri global...'
                        });
                    }
                    try {
                        const isEnLocale = this.isEn;
                        const res = await fetch('{{ route('api.blueprint.elaborate-directive', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Locale': isEnLocale ? 'en' : 'id'
                            },
                            body: JSON.stringify({
                                segment_key: this.tweakSegmentKey,
                                ai_provider: this.tweakAiProvider,
                                custom_title: this.tweakSegmentTitle,
                                feature_id: this.tweakFeatureId,
                                feature_content: this.tweakContent,
                                locale: isEnLocale ? 'en' : 'id'
                            })
                        });
                        const data = await res.json();
                        if (data && data.success && data.directive) {
                            this.tweakPrompt = data.directive;
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: isEnLocale ? 'DIRECTIVE COMPILED TO PROMPT' : 'DRAF INSTRUKSI BERHASIL DISUSUN',
                                    message: isEnLocale 
                                        ? 'Refinement directive successfully compiled into prompt box! Click \'EXECUTE AI ELABORATION\' below or use \'1-CLICK ELABORATE\' above to apply.'
                                        : 'Draf instruksi berhasil disusun ke dalam kotak teks! Klik \'EKSEKUSI PENDALAMAN AI\' di bawah atau gunakan tombol \'1-KLIK PERDALAM\' di atas untuk menerapkan.'
                                });
                            }
                        } else {
                            throw new Error(data.message || (isEnLocale ? 'Failed to compile refinement directive.' : 'Gagal menyusun instruksi penajaman.'));
                        }
                    } catch(err) {
                        if (window.showToast) {
                            const isEnLocale = this.isEn;
                            window.showToast({
                                type: 'error',
                                title: isEnLocale ? 'DIRECTIVE COMPILATION FAILED' : 'GAGAL MENYUSUN INSTRUKSI',
                                message: err.message
                            });
                        }
                    } finally {
                        this.elaboratingDirective = false;
                    }
                },

                getElaborateButtonLabel() {
                    const isEn = this.isEn;
                    let areaName = '';
                    if (this.tweakSegmentKey === 'scaffold_file') {
                        areaName = this.scaffoldActiveTab || (isEn ? 'Code File' : 'Berkas Kode');
                    } else if (this.tweakSegmentKey === 'database_erd') {
                        areaName = isEn ? 'Database ERD' : 'Basis Data (ERD)';
                    } else if (this.tweakSegmentKey === 'system_actors') {
                        areaName = isEn ? 'RBAC & Actors' : 'Aktor & RBAC';
                    } else if (this.tweakSegmentKey === 'alurs_kerja') {
                        areaName = isEn ? 'Business Workflow' : 'Alur Kerja Sistem';
                    } else if (this.tweakSegmentKey === 'features') {
                        areaName = isEn ? 'MVP Features' : 'Katalog Fitur MVP';
                    } else if (this.tweakSegmentKey === 'executive_summary') {
                        areaName = isEn ? 'Executive Summary' : 'Ringkasan Eksekutif';
                    } else if (this.tweakSegmentKey === 'custom_ai_agent_rules') {
                        areaName = isEn ? 'AI Rules' : 'Aturan AI Agent';
                    } else if (this.tweakSegmentTitle) {
                        let raw = this.tweakSegmentTitle.replace(/^fitur:\s*/i, '').trim();
                        if (raw.length > 28) {
                            const parenMatch = raw.match(/\(([^)]+)\)/);
                            if (parenMatch && parenMatch[1].length <= 25) {
                                raw = parenMatch[1];
                            } else {
                                raw = raw.slice(0, 26).trim() + '…';
                            }
                        }
                        areaName = raw;
                    } else if (this.tweakFeatureId) {
                        areaName = this.tweakFeatureId;
                    } else {
                        areaName = isEn ? 'Specification' : 'Spesifikasi';
                    }

                    if (isEn) {
                        return `📝 COMPILE DIRECTIVE: ${areaName.toUpperCase()}`;
                    } else {
                        return `📝 SUSUN DRAF INSTRUKSI: ${areaName.toUpperCase()}`;
                    }
                },

                getElaboratingLoadingLabel() {
                    const isEn = this.isEn;
                    let areaName = '';
                    if (this.tweakSegmentKey === 'scaffold_file') {
                        areaName = this.scaffoldActiveTab || 'File';
                    } else if (this.tweakSegmentTitle) {
                        let raw = this.tweakSegmentTitle.replace(/^fitur:\s*/i, '').trim();
                        if (raw.length > 22) raw = raw.slice(0, 20).trim() + '…';
                        areaName = raw;
                    } else {
                        areaName = isEn ? 'Spec' : 'Modul';
                    }
                    return isEn 
                        ? `Compiling ${areaName} Directive from Corpus...` 
                        : `Menyusun Draf Instruksi ${areaName}...`;
                },

                scaffoldLoading: false,
                scaffoldMode: 'preview', // 'preview' or 'edit'
                scaffoldActiveTab: 'docker-compose.yml',
                scaffoldFiles: {},
                scaffoldCustomizedFiles: [],
                scaffoldSaveStatus: 'idle', // 'idle' | 'typing' | 'saving' | 'saved' | 'error'
                scaffoldLastSaved: null,
                scaffoldDebounceTimer: null,
                @php
                    $isScaffoldLockedForUser = ($isScopeLocked ?? false)
                        && $blueprint->project_status !== 'Retail License'
                        && !(method_exists($blueprint, 'isRetailTrack') && $blueprint->isRetailTrack())
                        && !(method_exists($blueprint, 'hasPaidRetailLicense') && $blueprint->hasPaidRetailLicense())
                        && !(auth()->user()?->isSuperAdmin() ?? false);
                @endphp
                scaffoldIsScopeLocked: {{ $isScaffoldLockedForUser ? 'true' : 'false' }},
                scaffoldAiPanelOpen: true,
                scaffoldAiPrompt: '',
                scaffoldAiLoading: false,
                scaffoldElaboratingDirective: false,
                scaffoldTelemetry: @js($blueprint->user_metadata['scaffold_elaboration_telemetry'] ?? []),
                getScaffoldPresetChips(tabId) {
                    const lower = String(tabId || '').toLowerCase();
                    const isEn = this.isEn;
                    if (lower.endsWith('.sql')) {
                        return isEn ? [
                            { label: '+ Strict ULID & FK Constraints', prompt: 'Enforce CHAR(26) Strict ULID primary/foreign keys and explicit ON DELETE RESTRICT/CASCADE rules across all project tables.' },
                            { label: '+ O(1) Keyset Cursor Indexes', prompt: 'Add composite indexes on (created_at DESC, id DESC) and status filter columns for sub-10ms O(1) cursor pagination.' },
                            { label: '+ pgvector HNSW + Hybrid RAG', prompt: 'Add pgvector vector(1536) columns, HNSW indexes, and pg_trgm GIN indexes for 18-Technique Hybrid RAG.' },
                            { label: '+ SHA-256 Audit Triggers', prompt: 'Add immutable audit log table and PostgreSQL trigger functions recording JSONB diffs and SHA-256 checksums.' }
                        ] : [
                            { label: '+ Strict ULID & Relasi FK', prompt: 'Pastikan seluruh tabel proyek menggunakan CHAR(26) PRIMARY KEY & FOREIGN KEY dengan aturan ON DELETE RESTRICT/CASCADE.' },
                            { label: '+ Indeks Keyset Cursor O(1)', prompt: 'Tambahkan composite index (created_at DESC, id DESC) dan kolom filter status untuk paginasi cursor O(1) sub-10ms.' },
                            { label: '+ pgvector HNSW + Hybrid RAG', prompt: 'Tambahkan tabel & kolom vector(1536) beserta indeks HNSW pgvector dan pg_trgm untuk 18 Teknik Hybrid RAG.' },
                            { label: '+ Trigger Audit SHA-256', prompt: 'Tambahkan tabel audit trail immutable dan fungsi trigger PostgreSQL pencatat diff JSONB & hash SHA-256.' }
                        ];
                    }
                    if (lower === 'openapi.json') {
                        return isEn ? [
                            { label: '+ Full Domain CRUD Endpoints', prompt: 'Expand OpenAPI 3.1.0 paths for all project ERD entities with complete request/response schemas.' },
                            { label: '+ O(1) Cursor & RFC 9457 Errors', prompt: 'Add cursor/limit query parameters, X-Idempotency-Key headers, and RFC 9457 Problem Details error schemas.' }
                        ] : [
                            { label: '+ Endpoint CRUD Seluruh Tabel', prompt: 'Lengkapi path OpenAPI 3.1.0 untuk seluruh entitas tabel proyek beserta skema JSON request/response.' },
                            { label: '+ Cursor O(1) & Error RFC 9457', prompt: 'Standarisasi parameter cursor ULID 26-karakter, header X-Idempotency-Key, dan skema error RFC 9457 Problem Details.' }
                        ];
                    }
                    if (lower.includes('seeder')) {
                        return isEn ? [
                            { label: '+ Realistic Domain Seed Data', prompt: 'Generate 100% realistic domain seed records with 26-char Str::ulid() keys, RBAC actors, and authentic pricing.' },
                            { label: '+ Multi-State Lifecycle Rows', prompt: 'Add seed transactions covering Draft, Pending, Confirmed, Completed, and Refunded states.' }
                        ] : [
                            { label: '+ Data Sintetis Kontekstual', prompt: 'Bangkitkan data seeder realistis sesuai domain bisnis klien dengan Str::ulid() 26-karakter, aktor RBAC, dan tarif nyata.' },
                            { label: '+ Variasi Siklus State Machine', prompt: 'Tambahkan sampel data di seluruh siklus status (Draft, Verifikasi, Terkonfirmasi, Selesai, Batal/Refund).' }
                        ];
                    }
                    if (lower.includes('test') || lower.endsWith('.spec.ts')) {
                        return isEn ? [
                            { label: '+ Cursor O(1) & ULID Assertions', prompt: 'Add contract tests asserting O(1) keyset cursor pagination (next_cursor) and 26-char ULID validation.' },
                            { label: '+ RBAC Isolation & Idempotency', prompt: 'Add tests for 403 Forbidden cross-role isolation, X-Idempotency-Key replay protection, and zero native dialogs.' }
                        ] : [
                            { label: '+ Uji Cursor O(1) & ULID 26-Char', prompt: 'Tambahkan pengujian otomatis untuk memastikan paginasi Keyset Cursor O(1) (next_cursor) dan validasi ULID 26-karakter.' },
                            { label: '+ Uji Isolasi RBAC & Idempotensi', prompt: 'Tambahkan pengujian 403 Forbidden antar peran RBAC, proteksi X-Idempotency-Key, dan larangan dialog native browser.' }
                        ];
                    }
                    return isEn ? [
                        { label: '+ Deepen from Source Corpus', prompt: 'Deepen this file using concrete entities, workflows, and business rules from the Project Source Corpus.' },
                        { label: '+ 18 RAG + 20 Agentic AI Stack', prompt: 'Integrate 18-Technique Production RAG, 20 Agentic AI Concepts, Laravel 13, Filament v5 Schema, and Strict ULID rules.' }
                    ] : [
                        { label: '+ Perdalam dari Dokumen Acuan', prompt: 'Perdalam isi berkas ini menggunakan entitas, alur kerja, dan aturan bisnis konkret dari Dokumen Acuan Proyek.' },
                        { label: '+ 18 Teknik RAG & 20 Agentic AI', prompt: 'Integrasikan arsitektur 18 Teknik Production RAG, 20 Konsep Agentic AI, Laravel 13, Filament v5 Schema, dan Strict ULID.' }
                    ];
                },
                async elaborateScaffoldDirectiveInPlace() {
                    if (this.scaffoldElaboratingDirective) return;
                    this.scaffoldElaboratingDirective = true;
                    try {
                        const isEnLocale = this.isEn;
                        const res = await fetch('{{ route('api.blueprint.elaborate-directive', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Locale': isEnLocale ? 'en' : 'id'
                            },
                            body: JSON.stringify({
                                segment_key: 'scaffold_file',
                                ai_provider: this.tweakAiProvider,
                                custom_title: this.scaffoldActiveTab,
                                feature_content: this.getScaffoldCode(this.scaffoldActiveTab),
                                locale: isEnLocale ? 'en' : 'id'
                            })
                        });
                        const data = await res.json();
                        if (data && data.success && data.directive) {
                            this.scaffoldAiPrompt = data.directive;
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: isEnLocale ? 'FILE DIRECTIVE READY' : 'INSTRUKSI AI BERKAS SIAP',
                                    message: isEnLocale
                                        ? ('Compiled specific architectural directives for ' + this.scaffoldActiveTab)
                                        : ('Instruksi penajaman spesifik untuk ' + this.scaffoldActiveTab + ' berhasil dirumuskan.')
                                });
                            }
                        } else {
                            throw new Error(data.message || 'Gagal merumuskan instruksi berkas.');
                        }
                    } catch (err) {
                        if (window.showToast) {
                            window.showToast({ type: 'error', title: 'ERROR INSTRUKSI AI', message: err.message });
                        }
                    } finally {
                        this.scaffoldElaboratingDirective = false;
                    }
                },
                async refineActiveScaffoldFileInPlace() {
                    if (this.scaffoldAiLoading || this.scaffoldIsScopeLocked) return;
                    this.scaffoldAiLoading = true;
                    const targetTab = this.scaffoldActiveTab;
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: this.isEn ? 'AI DEEPENING SCAFFOLD FILE...' : 'AI SEDANG MEMPERDALAM BERKAS...',
                            message: this.isEn
                                ? ('Elaborating ' + targetTab + ' from Source Corpus, ERD, & Modern Tech Stack...')
                                : ('Mengelaborasi ' + targetTab + ' berbasis Dokumen Acuan, Tabel ERD, & Modern Tech Stack...')
                        });
                    }
                    try {
                        const res = await fetch('{{ route('blueprint.scaffold.refine-file', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Locale': this.isEn ? 'en' : 'id',
                            },
                            body: JSON.stringify({
                                filename: targetTab,
                                prompt: this.scaffoldAiPrompt,
                                ai_provider: this.tweakAiProvider,
                                locale: this.isEn ? 'en' : 'id',
                            })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.scaffoldFiles[targetTab] = data.content;
                            if (!this.scaffoldCustomizedFiles.includes(targetTab)) {
                                this.scaffoldCustomizedFiles.push(targetTab);
                            }
                            const syncedList = Array.isArray(data.synced_files) ? data.synced_files : [targetTab];
                            syncedList.forEach((sf) => {
                                this.scaffoldFiles[sf] = data.content;
                                if (!this.scaffoldCustomizedFiles.includes(sf)) {
                                    this.scaffoldCustomizedFiles.push(sf);
                                }
                                this.scaffoldTelemetry[sf] = {
                                    maturity_score: data.maturity_score || 95,
                                    is_maximal: !!data.is_maximal,
                                    added_lines: data.added_lines || 0,
                                    line_count: data.line_count || 0,
                                    iterations: data.iterations || 1,
                                    synced_files: syncedList,
                                };
                            });
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: data.is_maximal
                                        ? (this.isEn ? '100% OPTIMAL / MAXIMAL REACHED' : '100% OPTIMAL / MAKSIMAL TERCAPAI')
                                        : (this.isEn ? 'SCAFFOLD FILE DEEPENED' : 'BERKAS SCAFFOLD DIPERDALAM'),
                                    message: data.message
                                });
                            }
                        } else {
                            throw new Error(data.message || 'Gagal memperdalam berkas scaffold.');
                        }
                    } catch (err) {
                        if (window.showToast) {
                            window.showToast({ type: 'error', title: 'GAGAL ELABORASI BERKAS', message: err.message });
                        }
                    } finally {
                        this.scaffoldAiLoading = false;
                    }
                },
                collaborators: [],
                collaboratorCursor: { x: 0, y: 0, visible: false, name: '' },
                getScaffoldCode(tabId) {
                    if (this.scaffoldFiles && this.scaffoldFiles[tabId] && this.scaffoldFiles[tabId] !== 'Memuat berkas...') {
                        return this.scaffoldFiles[tabId];
                    }
                    const aliasMap = {
                        'docs/architecture/ADR-001-postgresql-ulid.md': 'docs/adr/ADR-001-architectural-paradigm.md',
                        'docs/adr/ADR-001-architectural-paradigm.md': 'docs/architecture/ADR-001-postgresql-ulid.md',
                        'tests/e2e/smoke.spec.ts': 'tests/e2e/critical-flows.spec.ts',
                        'tests/e2e/critical-flows.spec.ts': 'tests/e2e/smoke.spec.ts',
                        'docs/runbooks/disaster-recovery.md': 'docs/disaster-recovery-runbook.md',
                        'docs/disaster-recovery-runbook.md': 'docs/runbooks/disaster-recovery.md',
                    };
                    const alias = aliasMap[tabId];
                    if (alias && this.scaffoldFiles && this.scaffoldFiles[alias] && this.scaffoldFiles[alias] !== 'Memuat berkas...') {
                        this.scaffoldFiles[tabId] = this.scaffoldFiles[alias];
                        return this.scaffoldFiles[tabId];
                    }
                    if (this.scaffoldLoading) {
                        return '// Memuat berkas ' + tabId + '...';
                    }
                    return '// Berkas ' + tabId + ' siap dikompilasi atau diekspor.';
                },
                async openScaffoldModal() {
                    this.scaffoldModalOpen = true;
                    if (Object.keys(this.scaffoldFiles).length === 0) {
                        this.scaffoldLoading = true;
                        try {
                            const folderParam = '?folder=' + encodeURIComponent(this.getEffectiveFolder());
                            const res = await fetch('{{ route('blueprint.scaffold.preview', $blueprint->slug) }}' + folderParam, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                throw new Error(data.message || ('Server returned HTTP ' + res.status));
                            }
                            if (data && data.files) {
                                for (const [k, v] of Object.entries(data.files)) {
                                    if (v === 'Memuat berkas...' || !v) {
                                        delete data.files[k];
                                    }
                                }
                                this.scaffoldFiles = data.files;
                                this.scaffoldCustomizedFiles = data.customized_files || [];
                                const keys = Object.keys(data.files);
                                if (keys.length > 0 && !this.scaffoldFiles[this.scaffoldActiveTab]) {
                                    const code = this.getScaffoldCode(this.scaffoldActiveTab);
                                    if (!code || code.startsWith('// Berkas')) {
                                        this.scaffoldActiveTab = keys[0];
                                    }
                                }
                            }
                        } catch(e) {
                            if (window.showToast) window.showToast({ type: 'error', title: 'GAGAL MEMUAT', message: e.message || 'Gagal memuat pratinjau scaffold.' });
                        } finally {
                            this.scaffoldLoading = false;
                        }
                    }
                },
                onScaffoldCodeInput() {
                    this.scaffoldSaveStatus = 'typing';
                    clearTimeout(this.scaffoldDebounceTimer);
                    this.scaffoldDebounceTimer = setTimeout(() => {
                        this.autoSaveActiveScaffoldFile();
                    }, 1000);
                },
                async autoSaveActiveScaffoldFile(showToastFeedback = false) {
                    if (this.scaffoldIsScopeLocked) return;
                    const filename = this.scaffoldActiveTab;
                    const content = this.scaffoldFiles[filename] !== undefined ? this.scaffoldFiles[filename] : this.getScaffoldCode(filename);
                    if (!content || typeof content !== 'string' || content.trim() === 'Memuat berkas...' || content.trim().startsWith('// Berkas ') || content.trim().startsWith('// Memuat berkas')) {
                        return;
                    }
                    this.scaffoldSaveStatus = 'saving';

                    try {
                        const res = await fetch('{{ route('blueprint.scaffold.save-file', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ filename, content })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.scaffoldSaveStatus = 'saved';
                            this.scaffoldLastSaved = data.saved_at;
                            if (!this.scaffoldCustomizedFiles.includes(filename)) {
                                this.scaffoldCustomizedFiles.push(filename);
                            }
                            if (showToastFeedback && window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'TERSINKRON KE CLOUD',
                                    message: 'Perubahan pada berkas ' + filename + ' berhasil disimpan otomatis.'
                                });
                            }
                        } else {
                            throw new Error(data.message || 'Gagal menyimpan.');
                        }
                    } catch(err) {
                        this.scaffoldSaveStatus = 'error';
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'GAGAL AUTO-SAVE',
                                message: 'Tidak dapat menyimpan otomatis berkas ' + filename + '. Cek koneksi Anda.'
                            });
                        }
                    }
                },
                async resetActiveScaffoldFile() {
                    if (this.scaffoldIsScopeLocked) return;
                    const filename = this.scaffoldActiveTab;
                    this.scaffoldLoading = true;
                    try {
                        const res = await fetch('{{ route('blueprint.scaffold.reset-file', $blueprint->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ filename })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.scaffoldFiles[filename] = data.content;
                            this.scaffoldCustomizedFiles = this.scaffoldCustomizedFiles.filter(f => f !== filename);
                            this.scaffoldSaveStatus = 'saved';
                            this.scaffoldLastSaved = data.saved_at;
                            if (window.showToast) {
                                window.showToast({
                                    type: 'info',
                                    title: 'RESET KE DEFAULT AI',
                                    message: 'Berkas ' + filename + ' berhasil dikembalikan ke sintesis PRD awal.'
                                });
                            }
                        }
                    } catch(e) {
                        if (window.showToast) window.showToast({ type: 'error', title: 'GAGAL RESET', message: 'Gagal mengembalikan berkas ke default.' });
                    } finally {
                        this.scaffoldLoading = false;
                    }
                },
                insertTabInEditor(event) {
                    const textarea = event.target;
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const val = textarea.value;
                    textarea.value = val.substring(0, start) + '    ' + val.substring(end);
                    textarea.selectionStart = textarea.selectionEnd = start + 4;
                    this.scaffoldFiles[this.scaffoldActiveTab] = textarea.value;
                    this.onScaffoldCodeInput();
                },
                copyActiveScaffold() {
                    const code = this.getScaffoldCode(this.scaffoldActiveTab);
                    if (!code || code.startsWith('// Berkas')) return;
                    navigator.clipboard.writeText(code).then(() => {
                        if (window.showToast) window.showToast({ type: 'success', title: 'KODE DISALIN', message: 'File ' + this.scaffoldActiveTab + ' berhasil disalin ke clipboard!' });
                    });
                },
                activeHostingSpectrum: 'umkm_lean',
                flowTab: 'visual', 
                erdTab: 'visual', 
                erdLang: 'id',
                chartStudioTab: 'workflow',
                devEducationMode: 'step_by_step',
                devActiveStep: 0,
                devCompletedSteps: (() => {
                    const dbSteps = @json($blueprint->user_metadata['dev_checkpoints'] ?? []);
                    const normalized = {};
                    for (const [k, v] of Object.entries(dbSteps)) {
                        normalized[k] = typeof v === 'object' ? !!v.completed : !!v;
                    }
                    try {
                        const local = JSON.parse(localStorage.getItem('neriah_dev_progress_{{ $blueprint->slug }}') || '{}');
                        return Object.assign({}, local, normalized);
                    } catch(e) {
                        return normalized;
                    }
                })(),
                isStepCompleted(k) {
                    return !!this.devCompletedSteps[k];
                },
                async toggleStepCompleted(k, label) {
                    this.devCompletedSteps[k] = !this.devCompletedSteps[k];
                    const isNowDone = this.devCompletedSteps[k];
                    try {
                        localStorage.setItem('neriah_dev_progress_{{ $blueprint->slug }}', JSON.stringify(this.devCompletedSteps));
                    } catch(e){}

                    // Persist to server database & synchronize customer dashboard timeline
                    try {
                        fetch('/api/blueprint/{{ $blueprint->slug }}/checkpoint', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                step_key: k,
                                is_completed: isNowDone,
                                notes: (label || k) + (isNowDone ? ' terverifikasi selesai.' : ' dikembalikan ke antrean.')
                            })
                        }).catch(() => {});
                    } catch(e){}

                    if (window.showToast) {
                        if (isNowDone) {
                            window.showToast({
                                type: 'success',
                                title: 'CHECKPOINT DISINKRONKAN',
                                message: (label || k) + ' ditandai selesai. Timeline proyek di dashboard pelanggan telah otomatis terupdate!'
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
                tierAmounts: @json($alpineTiers),
                getDiscountAmount(tierKey) {
                    if (!this.appliedVoucher) return 0;
                    const contract = this.tierAmounts[tierKey]?.contract || 0;
                    if (this.appliedVoucher.is_free_bypass) return contract;
                    if (this.appliedVoucher.discount_type === 'percent') {
                        return Math.round(contract * (parseFloat(this.appliedVoucher.discount_value) / 100));
                    }
                    if (this.appliedVoucher.discount_type === 'fixed') {
                        return Math.min(contract, parseFloat(this.appliedVoucher.discount_value));
                    }
                    return 0;
                },
                getDiscountedContract(tierKey) {
                    const contract = this.tierAmounts[tierKey]?.contract || 0;
                    return Math.max(0, contract - this.getDiscountAmount(tierKey));
                },
                getDiscountedDp(tierKey) {
                    if (!this.appliedVoucher) return this.tierAmounts[tierKey]?.dp || 0;
                    if (this.appliedVoucher.is_free_bypass) return 0;
                    const finalContract = this.getDiscountedContract(tierKey);
                    return Math.round(finalContract * 0.50);
                },
                isPayingSnap: false,
                executionTrack: '{{ ($isRetailLicense || ($blueprint->user_metadata['track'] ?? '') === 'retail' || request('track') === 'retail') ? 'retail' : 'studio' }}',
                retailCheckingOut: false,
                agreeRetailTerms: false,
                async checkoutRetail(packageTier) {
                    if (!this.agreeRetailTerms) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'PERSETUJUAN DIPERLUKAN',
                                message: 'Silakan centang persetujuan Syarat & Ketentuan serta Kebijakan Refund terlebih dahulu.'
                            });
                        }
                        return;
                    }
                    this.retailCheckingOut = true;
                    try {
                        const response = await fetch('/api/pricing/inquiry', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: '{{ addslashes($blueprint->client_name) }}',
                                email: '{{ addslashes($blueprint->email) }}',
                                phone: '{{ addslashes($blueprint->phone ?? '') }}',
                                company: '{{ addslashes($blueprint->nama_bisnis ?? '') }}',
                                country_code: '{{ addslashes($blueprint->country_code ?? '+62') }}',
                                package_tier: packageTier,
                                blueprint_slug: '{{ $blueprint->slug }}',
                                notes: 'Checkout Lisensi Retail dari PRD Blueprint: {{ addslashes($blueprint->nama_bisnis) }}'
                            })
                        });

                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal memproses checkout lisensi.');
                        }

                        if (result.snap_token && window.snap && typeof window.snap.pay === 'function') {
                            window.snap.pay(result.snap_token, {
                                onSuccess: function (snapRes) {
                                    if (window.showToast) {
                                        window.showToast({
                                            type: 'success',
                                            title: 'PEMBAYARAN LISENSI BERHASIL',
                                            message: 'Lisensi arsitektur Anda telah aktif! Mengarahkan ke Dashboard...'
                                        });
                                    }
                                    setTimeout(function() {
                                        window.location.href = '/customer/dashboard#licenses';
                                    }, 1800);
                                },
                                onPending: function (snapRes) {
                                    if (window.showToast) {
                                        window.showToast({
                                            type: 'info',
                                            title: 'MENUNGGU PEMBAYARAN',
                                            message: 'Silakan selesaikan pembayaran QRIS / Virtual Account Anda.'
                                        });
                                    }
                                },
                                onError: function (snapRes) {
                                    if (window.showToast) {
                                        window.showToast({
                                            type: 'error',
                                            title: 'PEMBAYARAN DIBATALKAN',
                                            message: 'Transaksi dibatalkan atau ditolak.'
                                        });
                                    }
                                }
                            });
                        } else if (result.redirect_url) {
                            window.location.href = result.redirect_url;
                        }
                    } catch (err) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'CHECKOUT GAGAL',
                                message: err.message || 'Terjadi kesalahan sistem saat menghubungi gateway pembayaran.'
                            });
                        }
                    } finally {
                        this.retailCheckingOut = false;
                    }
                },
                devPlaybookOpen: true,
                activeDevPhase: 1,
                copyMasterPromptSuccess: false,
                isDarkMode: document.documentElement.classList.contains('dark'),
                toggleTheme() {
                    if (this.isDarkMode) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('neriah_theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('neriah_theme', 'dark');
                    }
                    this.isDarkMode = !this.isDarkMode;
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new Event('theme-changed'));
                    }
                },
                locale: '{{ $blueprint->user_metadata['output_locale'] ?? '' }}' || localStorage.getItem('neriah_blueprint_lang') || '{{ ($isEn ?? false) ? "en" : "id" }}',
                get isEn() {
                    return this.locale === 'en' || (document.documentElement.lang && document.documentElement.lang.startsWith('en')) || (document.cookie && document.cookie.includes('neriah_locale=en'));
                },
                setLocale(l) {
                    this.locale = l;
                    try { localStorage.setItem('neriah_blueprint_lang', l); } catch(e){}
                },
                showAiPromptModal: false,
                showLegalTermsModal: false,
                legalTermsTab: 'id',
                openLegalTermsModal(tab) {
                    this.legalTermsTab = tab || (this.locale === 'en' ? 'en' : 'id');
                    this.showLegalTermsModal = true;
                },
                closeLegalTermsModal() {
                    this.showLegalTermsModal = false;
                },
                showRawMdWarningModal: false,
                rawMdActionType: 'download',
                openRawMdWarning(action) {
                    this.rawMdActionType = action || 'download';
                    this.showRawMdWarningModal = true;
                },
                proceedWithRawMd() {
                    this.showRawMdWarningModal = false;
                    const activeLoc = this.locale || (document.cookie.includes('neriah_locale=en') ? 'en' : 'id');
                    if (this.rawMdActionType === 'download') {
                        window.location.href = "{{ route('blueprint.download-md', $blueprint->slug) }}?locale=" + activeLoc;
                    } else if (this.rawMdActionType === 'copy') {
                        if (window.executeCopyFullPrdMarkdown) {
                            window.executeCopyFullPrdMarkdown();
                        }
                    }
                },
                legalTermsTab: 'id',
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
                navMode: (() => {
                    try {
                        return localStorage.getItem('neriah_blueprint_nav_mode') || 'sidebar';
                    } catch (e) {
                        return 'sidebar';
                    }
                })(),
                setNavMode(mode) {
                    this.navMode = mode;
                    try {
                        localStorage.setItem('neriah_blueprint_nav_mode', mode);
                    } catch (e) {}
                    this.$nextTick(() => {
                        if (window.reRenderActiveMermaid) window.reRenderActiveMermaid();
                    });
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: mode === 'sidebar' ? 'MODE 1 AKTIF' : 'MODE 2 AKTIF',
                            message: mode === 'sidebar' 
                                ? 'Navigasi sticky index di kiri aktif. Dock kanan bawah disembunyikan.' 
                                : 'Konten melebar penuh ke layar. Navigasi index berpindah ke dock kanan bawah.'
                        });
                    }
                },
                toggleNavMode() {
                    this.setNavMode(this.navMode === 'sidebar' ? 'focus' : 'sidebar');
                },
                autoSyncAccordion: true,
                sectionGroupMap: {
                    'section-00-execution-guide': 'scope',
                    'section-1': 'scope',
                    'section-1-5': 'scope',
                    'section-2': 'scope',
                    'section-3': 'scope',
                    'section-3-5': 'studio',
                    'section-3-8': 'studio',
                    'section-4': 'studio',
                    'section-5': 'studio',
                    'section-6': 'infra',
                    'section-7': 'legal',
                    'section-8': 'legal',
                    'section-9': 'legal',
                    'section-10': 'legal',
                    'section-13': 'infra'
                },
                accordionGroups: {
                    scope: true,
                    studio: false,
                    infra: false,
                    legal: false
                },
                syncAccordionToSection(secId) {
                    if (!this.autoSyncAccordion) return;
                    const targetGroup = this.sectionGroupMap[secId];
                    if (!targetGroup) return;
                    for (const grp in this.accordionGroups) {
                        this.accordionGroups[grp] = (grp === targetGroup);
                    }
                    this.$nextTick(() => {
                        const activeItems = document.querySelectorAll(`[data-spy-sec="${secId}"]`);
                        activeItems.forEach(el => {
                            const scrollContainer = el.closest('.custom-prd-scrollbar');
                            if (scrollContainer) {
                                const cRect = scrollContainer.getBoundingClientRect();
                                const iRect = el.getBoundingClientRect();
                                if (iRect.top < cRect.top + 15 || iRect.bottom > cRect.bottom - 15) {
                                    const relativeOffset = iRect.top - cRect.top + scrollContainer.scrollTop;
                                    scrollContainer.scrollTo({ top: Math.max(0, relativeOffset - 40), behavior: 'smooth' });
                                }
                            }
                        });
                    });
                },
                toggleAccordionGroup(grp) {
                    this.accordionGroups[grp] = !this.accordionGroups[grp];
                },
                expandAllGroups() {
                    this.accordionGroups.scope = true;
                    this.accordionGroups.studio = true;
                    this.accordionGroups.infra = true;
                    this.accordionGroups.legal = true;
                    this.autoSyncAccordion = false;
                    this.expandAllSections();
                },
                collapseAllGroups() {
                    this.accordionGroups.scope = false;
                    this.accordionGroups.studio = false;
                    this.accordionGroups.infra = false;
                    this.accordionGroups.legal = false;
                    this.autoSyncAccordion = false;
                    this.collapseAllSections();
                },
                toggleAutoSync() {
                    this.autoSyncAccordion = !this.autoSyncAccordion;
                    if (this.autoSyncAccordion) {
                        this.syncAccordionToSection(this.activeSectionId);
                    }
                },
                jumpTo(id) {
                    if (this.sectionCollapses && this.sectionCollapses[id]) {
                        this.sectionCollapses[id] = false;
                    }
                    window.jumpToSection(id);
                    this.activeSectionId = id;
                    this.autoSyncAccordion = true;
                    this.syncAccordionToSection(id);
                    this.floatingIndexOpen = false;
                    if (id === 'section-3-8' || id === 'section-4' || id === 'section-5') {
                        setTimeout(() => { if (window.reRenderActiveMermaid) window.reRenderActiveMermaid(); }, 120);
                    }
                },
                getActiveSectionNumber() {
                    const numbers = {
                        'section-00-execution-guide': '00',
                        'section-1': '01',
                        'section-1-5': '01.5',
                        'section-2': '02',
                        'section-3': '03',
                        'section-3-5': '04',
                        'section-3-8': '05',
                        'section-4': '06',
                        'section-5': '07',
                        'section-6': '08',
                        'section-7': '09',
                        'section-8': '10',
                        'section-9': '11',
                        'section-10': '12',
                        'section-13': '13'
                    };
                    return numbers[this.activeSectionId] || '01';
                },
                getActiveSectionTitle() {
                    const titles = {
                        'section-00-execution-guide': { id: '00. Peta Jalan & 3 Cara Mulai', en: '00. Roadmap & 3 Ways to Start' },
                        'section-1': { id: '01. Executive Discovery', en: '01. Executive Discovery' },
                        'section-1-5': { id: '01.5 Analisis ROI & Garansi', en: '01.5 Business ROI & Guarantees' },
                        'section-2': { id: '02. RBAC & Aktor Sistem', en: '02. RBAC & System Actors' },
                        'section-3': { id: '03. Rekayasa Fitur MVP', en: '03. Feature Engineering' },
                        'section-3-5': { id: '04. Edukasi Developer AI', en: '04. AI Developer Hub' },
                        'section-3-8': { id: '05. Virtual Studio Charts', en: '05. Virtual Charts Studio' },
                        'section-4': { id: '06. Alur Kerja User Flow', en: '06. Core User Flow' },
                        'section-5': { id: '07. Database ERD', en: '07. Database ERD Blueprint' },
                        'section-6': { id: '08. Evaluasi Infra VPS', en: '08. Cloud VPS & Infra' },
                        'section-7': { id: '09. Velocity Pricing', en: '09. Velocity Pricing' },
                        'section-8': { id: '10. Timeline Sprint Gantt', en: '10. Sprint Timeline' },
                        'section-9': { id: '11. Tata Kelola & SLA', en: '11. Governance & SLA' },
                        'section-10': { id: '{{ $blueprint->isRetailLicense() ? "12. Kunci Scope & Lisensi" : "12. Kunci Scope & DP" }}', en: '{{ $blueprint->isRetailLicense() ? "12. Scope Freeze & License" : "12. Scope Freeze & DP" }}' },
                        'section-13': { id: '13. Ekspor Repo Project OS', en: '13. Export Project OS Repo' }
                    };
                    const s = titles[this.activeSectionId] || { id: 'Daftar Isi PRD', en: 'PRD Directory' };
                    return this.locale === 'en' ? s.en : s.id;
                },
                getActiveSectionIndex() {
                    const order = [
                        'section-00-execution-guide',
                        'section-1', 'section-1-5', 'section-2', 'section-3',
                        'section-3-5', 'section-3-8', 'section-4', 'section-5',
                        'section-6', 'section-7', 'section-8', 'section-9',
                        'section-10', 'section-13'
                    ];
                    const idx = order.indexOf(this.activeSectionId);
                    return idx >= 0 ? (idx + 1) : 1;
                },
                init() {
                    // Initialize Download Center Artifacts list & countdown ticker
                    this.fetchDownloadArtifacts();
                    this.ensureCountdownTicker();

                    window.openDiagramFullscreen = (title, sourceId) => this.openDiagramFullscreen(title, sourceId);
                    window.closeDiagramFullscreen = () => this.closeDiagramFullscreen();
                    window.addEventListener('theme-changed', () => {
                        if (this.fullscreenDiagramModal && this.fullscreenDiagramSourceId) {
                            window.renderMermaidDiagram('mermaid-fullscreen-target', this.fullscreenDiagramSourceId, true);
                        }
                    });
                    this.$nextTick(() => {
                        window.setupBlueprintScrollSpy(id => { 
                            this.activeSectionId = id; 
                            this.syncAccordionToSection(id);
                        });
                        this.syncAccordionToSection(this.activeSectionId);
                    });
                    this.$watch('flowTab', val => {
                        if (val === 'mermaid') this.$nextTick(() => window.renderMermaidDiagram('mermaid-flow-target', 'mermaid-flow-source'));
                    });
                    this.$watch('erdTab', val => {
                        if (val === 'mermaid') this.$nextTick(() => window.renderMermaidDiagram('mermaid-erd-target', 'mermaid-erd-source'));
                    });
                    this.$watch('chartStudioTab', val => {
                        if (val === 'workflow') this.$nextTick(() => window.renderMermaidDiagram('mermaid-studio-flow-target', 'mermaid-studio-flow-source'));
                        if (val === 'erd') this.$nextTick(() => window.renderMermaidDiagram('mermaid-studio-erd-target', 'mermaid-studio-erd-source'));
                        if (val === 'feature_dep') this.$nextTick(() => window.renderMermaidDiagram('mermaid-studio-featdep-target', 'mermaid-studio-featdep-source'));
                        if (val === 'gantt') this.$nextTick(() => window.renderMermaidDiagram('mermaid-studio-gantt-target', 'mermaid-studio-gantt-source'));
                        if (val === 'infra') this.$nextTick(() => window.renderMermaidDiagram('mermaid-studio-infra-target', 'mermaid-studio-infra-source'));
                    });
                    this.$nextTick(() => {
                        window.renderMermaidDiagram('mermaid-studio-flow-target', 'mermaid-studio-flow-source');
                        // Initialize Real-time Collaborative Presence Heartbeat
                        try {
                            const cid = 'c_' + Math.random().toString(36).substring(2, 9);
                            let hasMoved = false;
                            const syncPresence = (mx, my) => {
                                if (mx === undefined || my === undefined) return;
                                fetch('{{ route('api.blueprint.presence.update', $blueprint->slug) }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                    body: JSON.stringify({ client_id: cid, x: mx, y: my, section: this.activeSectionId })
                                }).then(r => r.json()).then(d => {
                                    if (d && d.collaborators && d.collaborators.length > 0) {
                                        this.collaborators = d.collaborators;
                                        const nowTs = Math.floor(Date.now() / 1000);
                                        const other = d.collaborators.find(c => c.id !== cid && (nowTs - (c.last_seen || 0)) <= 8);
                                        if (other && other.x !== undefined && other.y !== undefined && other.x > 0 && other.y > 0) {
                                            this.collaboratorCursor = { x: other.x, y: other.y, visible: true, name: other.name || 'Kolaborator' };
                                        } else {
                                            this.collaboratorCursor.visible = false;
                                        }
                                    } else {
                                        this.collaboratorCursor.visible = false;
                                    }
                                }).catch(() => {
                                    this.collaboratorCursor.visible = false;
                                });
                            };
                            window.addEventListener('mousemove', e => {
                                window.lastMouseX = Math.round((e.clientX / window.innerWidth) * 100);
                                window.lastMouseY = Math.round((e.clientY / window.innerHeight) * 100);
                                if (!hasMoved) {
                                    hasMoved = true;
                                    syncPresence(window.lastMouseX, window.lastMouseY);
                                }
                            }, { passive: true });
                            setInterval(() => {
                                if (window.lastMouseX !== undefined && window.lastMouseY !== undefined) {
                                    syncPresence(window.lastMouseX, window.lastMouseY);
                                }
                            }, 4000);
                        } catch(e) {}
                    });
                }
            };
        }
    </script>
