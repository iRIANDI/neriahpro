    <script>
        function blueprintApp() {
            return {
                blueprintSlug: '{{ $blueprint->slug }}',
                roiRecalculating: false,
                fullRegenerating: false,
                sectionCollapses: {
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
                    if ({{ ($isScopeLocked ?? false) ? 'true' : 'false' }} && '{{ $blueprint->project_status }}' !== 'Retail License' && !{{ auth()->user()?->isSuperAdmin() ? 'true' : 'false' }}) {
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
                                },
                                body: JSON.stringify({
                                    filename: this.scaffoldActiveTab,
                                    prompt: this.tweakPrompt,
                                    ai_provider: this.tweakAiProvider,
                                })
                            });
                            const data = await res.json();
                            if (data && data.success) {
                                this.scaffoldFiles[this.scaffoldActiveTab] = data.content;
                                if (!this.scaffoldCustomizedFiles.includes(this.scaffoldActiveTab)) {
                                    this.scaffoldCustomizedFiles.push(this.scaffoldActiveTab);
                                }
                                if (window.showToast) {
                                    window.showToast({ 
                                        type: 'success', 
                                        title: 'BERKAS DIPERDALAM', 
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
                async elaborateDirectiveFromAi() {
                    if (this.elaboratingDirective) return;
                    this.elaboratingDirective = true;
                    if (window.showToast) {
                        window.showToast({
                            type: 'info',
                            title: 'MENGEKSTRAK STANDAR DUNIA & DOKUMEN ACUAN',
                            message: 'AI sedang menganalisis dokumen acuan proyek klien dan menyelaraskannya dengan standar industri global...'
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
                                    title: isEnLocale ? 'DIRECTIVE ELABORATION COMPILED' : 'INSTRUKSI PENAJAMAN BERHASIL DISUSUN',
                                    message: isEnLocale 
                                        ? (data.message_en || data.message || 'Refinement directive successfully compiled from Project Documents & Global Standards.')
                                        : (data.message_id || data.message || 'Instruksi penajaman berhasil dirumuskan berbasis Dokumen Acuan & Standar Industri Dunia.')
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
                        return `✨ ELABORATE SPEC: ${areaName.toUpperCase()} (WORLD STANDARDS)`;
                    } else {
                        return `✨ ELABORASI SPESIFIKASI: ${areaName.toUpperCase()} (STANDAR DUNIA)`;
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
                        ? `Synthesizing ${areaName} from Source & World Standards...` 
                        : `Menyusun ${areaName} dari Acuan & Standar Dunia...`;
                },

                scaffoldLoading: false,
                scaffoldMode: 'preview', // 'preview' or 'edit'
                scaffoldActiveTab: 'docker-compose.yml',
                scaffoldFiles: {},
                scaffoldCustomizedFiles: [],
                scaffoldSaveStatus: 'idle', // 'idle' | 'typing' | 'saving' | 'saved' | 'error'
                scaffoldLastSaved: null,
                scaffoldDebounceTimer: null,
                scaffoldIsScopeLocked: {{ ($isScopeLocked ?? false) ? 'true' : 'false' }},
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
                            const res = await fetch('{{ route('blueprint.scaffold.preview', $blueprint->slug) }}');
                            const data = await res.json();
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
                            if (window.showToast) window.showToast({ type: 'error', title: 'GAGAL MEMUAT', message: 'Gagal memuat pratinjau scaffold.' });
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
                executionTrack: '{{ ($blueprint->project_status === 'Retail License' || request('track') === 'retail') ? 'retail' : 'studio' }}',
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
                    'section-10': 'legal'
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
                        'section-10': '12'
                    };
                    return numbers[this.activeSectionId] || '01';
                },
                getActiveSectionTitle() {
                    const titles = {
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
                        'section-10': { id: '{{ $blueprint->isRetailLicense() ? "12. Kunci Scope & Lisensi" : "12. Kunci Scope & DP" }}', en: '{{ $blueprint->isRetailLicense() ? "12. Scope Freeze & License" : "12. Scope Freeze & DP" }}' }
                    };
                    const s = titles[this.activeSectionId] || { id: 'Daftar Isi PRD', en: 'PRD Directory' };
                    return this.locale === 'en' ? s.en : s.id;
                },
                getActiveSectionIndex() {
                    const order = ['section-1', 'section-1-5', 'section-2', 'section-3', 'section-3-5', 'section-3-8', 'section-4', 'section-5', 'section-6', 'section-7', 'section-8', 'section-9', 'section-10'];
                    const idx = order.indexOf(this.activeSectionId);
                    return idx >= 0 ? (idx + 1) : 1;
                },
                init() {
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
