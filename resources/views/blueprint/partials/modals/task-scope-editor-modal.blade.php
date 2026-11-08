    <!-- INTERACTIVE TASK & SCOPE EDITOR MODAL (PRE-SIGN ELABORATION) -->
    <div 
        x-show="taskEditorOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 font-mono text-xs no-print"
        @keydown.escape.window="taskEditorOpen = false"
    >
        <div 
            class="bg-white dark:bg-zinc-900 border-2 border-amber-500/80 max-w-4xl w-full shadow-2xl relative rounded-none flex flex-col max-h-[90vh] overflow-hidden"
            @click.outside="taskEditorOpen = false"
        >
            <!-- Fixed Modal Header (Sticky Top) -->
            <div class="p-4 sm:p-5 pb-3 border-b border-zinc-200 dark:border-zinc-800 shrink-0 flex items-center justify-between gap-3 bg-zinc-50 dark:bg-zinc-950">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-amber-500 text-black flex items-center justify-center font-bold text-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span>Interactive Task &amp; Scope Editor</span>
                            <span class="px-2 py-0.5 text-[10px] bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40 font-bold">PRE-SIGN ELABORATION</span>
                        </h3>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-0.5 font-sans" x-text="isEn ? 'Elaborate, refine, add, or reprioritize technical tasks & acceptance criteria before document locking.' : 'Elaborasi, sesuaikan, tambah, atau kurangi task teknis & kriteria fitur sebelum dokumen dikunci dan ditandatangani.'">
                        </p>
                    </div>
                </div>

                <!-- Close Button -->
                <button 
                    type="button"
                    @click="taskEditorOpen = false" 
                    class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-xl font-bold cursor-pointer p-1 shrink-0 transition"
                    title="Tutup / Close"
                >
                    &times;
                </button>
            </div>

            <!-- Scrollable Modal Body (Seamless Vertical Scroll) -->
            <div class="p-4 sm:p-6 flex-1 overflow-y-auto custom-prd-scrollbar space-y-4">
                <!-- Explanatory Notice -->
                <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 text-amber-900 dark:text-amber-300 text-[11px] font-sans leading-relaxed">
                    <strong x-text="isEn ? 'Synchronization Notice:' : 'Catatan Sinkronisasi:'"></strong>
                    <span x-text="isEn ? ' Any task modifications below will dynamically recalculate the itemized cost breakdown, sprint roadmap, and re-compute the SHA-256 cryptographic integrity hash upon clicking Save Changes.' : ' Setiap penambahan atau perubahan task di bawah ini akan memperbarui rincian estimasi biaya (itemized breakdown), sprint roadmap, dan menghitung ulang kode integritas kriptografis SHA-256 secara otomatis saat Anda menekan tombol Simpan Perubahan.'"></span>
                </div>

                <!-- Quick Add Task Bar -->
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-3 sm:p-4 rounded-none space-y-3">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-amber-500 inline-block"></span>
                        <span x-text="isEn ? 'ADD NEW TASK / FEATURE:' : 'TAMBAH TASK / FITUR BARU:'"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                        <div class="sm:col-span-5">
                            <input 
                                type="text" 
                                x-model="newTaskTitle" 
                                :placeholder="isEn ? 'Task / Feature Title (e.g. Sub-150ms WebRTC Streaming)' : 'Judul Task / Fitur (mis: Integrasi Pembayaran Midtrans Snap)'" 
                                class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-amber-500 rounded-none"
                                @keydown.enter="addNewTask()"
                            />
                        </div>
                        <div class="sm:col-span-4">
                            <input 
                                type="text" 
                                x-model="newTaskDesc" 
                                :placeholder="isEn ? 'Brief description & acceptance criteria' : 'Deskripsi singkat & kriteria acceptance'" 
                                class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-amber-500 rounded-none"
                                @keydown.enter="addNewTask()"
                            />
                        </div>
                        <div class="sm:col-span-3 flex gap-2">
                            <select 
                                x-model="newTaskTarget" 
                                class="w-1/2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-2 text-[11px] text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-amber-500 rounded-none"
                            >
                                <option value="mvp" x-text="isEn ? 'Phase 1 (MVP)' : 'Fase 1 (MVP)'"></option>
                                <option value="phase2" x-text="isEn ? 'Phase 2 (Roadmap)' : 'Fase 2 (Roadmap)'"></option>
                            </select>
                            <button 
                                type="button" 
                                @click="addNewTask()" 
                                class="w-1/2 bg-amber-500 hover:bg-amber-400 text-black font-bold text-xs px-3 py-2 transition flex items-center justify-center gap-1 rounded-none cursor-pointer"
                            >
                                <span x-text="isEn ? '+ ADD' : '+ TAMBAH'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab Switcher & Dynamic AI Gap Analyzer -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="taskEditorTab = 'mvp'; taskGapResult = null;" 
                            class="px-3 py-1.5 text-xs font-bold transition flex items-center gap-2 border rounded-none cursor-pointer"
                            :class="taskEditorTab === 'mvp' ? 'bg-emerald-500 text-black border-emerald-500 font-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:text-zinc-900 dark:hover:text-zinc-100'"
                        >
                            <span x-text="isEn ? 'PHASE 1: MANDATORY (MVP)' : 'FASE 1: FITUR WAJIB (MVP)'"></span>
                            <span class="px-1.5 py-0.2 text-[10px] font-bold" :class="taskEditorTab === 'mvp' ? 'bg-black text-emerald-400' : 'bg-zinc-300 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200'" x-text="mvpTasks.length"></span>
                        </button>
                        <button 
                            type="button" 
                            @click="taskEditorTab = 'phase2'; taskGapResult = null;" 
                            class="px-3 py-1.5 text-xs font-bold transition flex items-center gap-2 border rounded-none cursor-pointer"
                            :class="taskEditorTab === 'phase2' ? 'bg-sky-500 text-black border-sky-500 font-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:text-zinc-900 dark:hover:text-zinc-100'"
                        >
                            <span x-text="isEn ? 'PHASE 2: ROADMAP' : 'FASE 2: ROADMAP SUSULAN'"></span>
                            <span class="px-1.5 py-0.2 text-[10px] font-bold" :class="taskEditorTab === 'phase2' ? 'bg-black text-sky-400' : 'bg-zinc-300 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200'" x-text="phase2Tasks.length"></span>
                        </button>
                    </div>

                    <!-- Dynamic AI Gap Analyzer Trigger Button -->
                    <button 
                        type="button" 
                        @click="analyzeTaskGapsWithAi()" 
                        :disabled="analyzingTaskGaps"
                        class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 active:bg-emerald-500/30 text-emerald-800 dark:text-emerald-300 font-mono text-xs font-bold border border-emerald-500/50 hover:border-emerald-500 flex items-center gap-2 transition cursor-pointer disabled:opacity-50 shrink-0 rounded-none shadow-xs"
                        :title="isEn ? 'AI compares existing tasks against the Project Documents to discover missing requirements without assumptions' : 'AI membandingkan task saat ini dengan Dokumen Acuan untuk mencari celah kebutuhan penting tanpa asumsi'"
                    >
                        <template x-if="!analyzingTaskGaps">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span class="uppercase tracking-tight" x-text="taskEditorTab === 'mvp' ? (isEn ? '✨ AI GAP ANALYSIS: PHASE 1 (MVP)' : '✨ ANALISIS GAP FASE 1 (MVP) DENGAN AI') : (isEn ? '✨ AI GAP ANALYSIS: PHASE 2 (ROADMAP)' : '✨ ANALISIS GAP FASE 2 (ROADMAP) DENGAN AI')"></span>
                            </span>
                        </template>
                        <template x-if="analyzingTaskGaps">
                            <span class="flex items-center gap-2 animate-pulse text-zinc-600 dark:text-zinc-400">
                                <svg class="animate-spin w-3.5 h-3.5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isEn ? 'Analyzing Source Corpus & Architecture Gaps...' : 'Membedah Dokumen Acuan & Celah Arsitektur...'"></span>
                            </span>
                        </template>
                    </button>
                </div>

                <!-- AI Gap Analysis Result: CASE A (100% Maximal & Complete) -->
                <div 
                    x-show="taskGapResult && taskGapResult.is_maximal" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="p-3.5 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-400 dark:border-emerald-700 flex items-start gap-3 rounded-none"
                >
                    <span class="w-6 h-6 bg-emerald-500 text-black flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                    <div class="flex-1 text-xs font-mono text-emerald-950 dark:text-emerald-200">
                        <div class="font-black uppercase tracking-wider mb-1 flex items-center justify-between">
                            <span x-text="isEn ? '✓ THIS PHASE ARCHITECTURE IS 100% MAXIMAL & COMPLETE' : '✓ ARSITEKTUR FASE INI SUDAH 100% MAKSIMAL & LENGKAP'"></span>
                            <button type="button" @click="taskGapResult = null" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs font-bold cursor-pointer" x-text="isEn ? '× CLOSE' : '× TUTUP'"></button>
                        </div>
                        <p class="leading-relaxed font-sans text-[11px]" x-text="taskGapResult?.summary || (isEn ? 'All essential functional and operational requirements from the Project Source Documents are fully covered in this phase without assumptions. No extra tasks are needed.' : 'Seluruh kebutuhan fungsional dan operasional berdasarkan Dokumen Acuan telah terakomodasi sempurna pada fase ini tanpa asumsi fiktif. Tidak diperlukan penambahan task lagi.')"></p>
                    </div>
                </div>

                <!-- AI Gap Analysis Result: CASE B (Gaps Found & Recommendations Available) -->
                <div 
                    x-show="taskGapResult && !taskGapResult.is_maximal && taskGapResult.recommendations?.length > 0" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-500 flex flex-col gap-2.5 rounded-none"
                >
                    <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                        <div class="text-xs font-mono font-black text-amber-950 dark:text-amber-300 uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 inline-block animate-ping"></span>
                            <span x-text="isEn ? ('FOUND ' + taskGapResult.recommendations.length + ' ESSENTIAL ARCHITECTURE TASKS NOT YET COVERED:') : ('DITEMUKAN ' + taskGapResult.recommendations.length + ' TASK KRUSIAL BELUM TERCOVER (TANPA ASUMSI):')"></span>
                        </div>
                        <button type="button" @click="taskGapResult = null" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-xs font-bold cursor-pointer" x-text="isEn ? '× CLOSE' : '× TUTUP'"></button>
                    </div>
                    <p class="text-[11px] text-zinc-700 dark:text-zinc-300 font-sans" x-text="taskGapResult?.summary"></p>
                    
                    <!-- Recommendations List (Internal Scroll to keep clean) -->
                    <div class="space-y-2 max-h-60 overflow-y-auto custom-prd-scrollbar pr-1">
                        <template x-for="(rec, rIdx) in taskGapResult.recommendations" :key="'rec-' + rIdx">
                            <div class="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 flex items-start gap-2.5 rounded-none hover:border-amber-500 transition">
                                <input type="checkbox" x-model="rec.selected" class="mt-1 accent-amber-500 cursor-pointer" />
                                <div class="flex-1 text-xs">
                                    <div class="flex flex-wrap items-center justify-between gap-1.5 mb-0.5">
                                        <strong class="text-zinc-900 dark:text-zinc-100 font-bold" x-text="rec.title"></strong>
                                        <span class="px-1.5 py-0.2 text-[9px] bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono uppercase" x-text="rec.category"></span>
                                    </div>
                                    <p class="text-zinc-600 dark:text-zinc-400 text-[11px] font-sans leading-relaxed" x-text="rec.desc"></p>
                                    <div class="text-[10px] text-amber-800 dark:text-amber-400 font-mono mt-1 flex items-center gap-1 font-bold">
                                        <span x-text="isEn ? '⚠️ Urgency:' : '⚠️ Urgensi:'"></span>
                                        <span x-text="rec.urgency_reason"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-amber-500/30">
                        <span class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400" x-text="isEn ? 'Select tasks above to append them into this phase.' : 'Centang task di atas untuk memasukkannya ke daftar fase ini.'"></span>
                        <button 
                            type="button" 
                            @click="applyRecommendedTasks()" 
                            class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-black font-black text-xs uppercase transition rounded-none cursor-pointer flex items-center justify-center gap-1.5 shadow-sm"
                        >
                            <span x-text="isEn ? '+ INSERT SELECTED TASKS INTO LIST' : '+ MASUKKAN TASK TERPILIH KE DAFTAR'"></span>
                        </button>
                    </div>
                </div>

                <!-- Task List Section -->
                <div class="space-y-2">
                    <!-- MVP Task List -->
                    <div x-show="taskEditorTab === 'mvp'" class="space-y-2">
                        <template x-for="(task, idx) in mvpTasks" :key="'mvp-' + idx">
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-emerald-500/30 rounded-none flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-2.5 flex-1">
                                    <span class="px-1.5 py-0.5 bg-emerald-500 text-black font-bold text-[10px] shrink-0 mt-1" x-text="String(idx + 1).padStart(2, '0')"></span>
                                    <div class="flex-1 space-y-1">
                                        <input 
                                            type="text" 
                                            x-model="task.title" 
                                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs font-bold text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-emerald-500 rounded-none"
                                            :placeholder="isEn ? 'Task Title' : 'Judul Task'"
                                        />
                                        <textarea 
                                            x-model="task.desc" 
                                            rows="2" 
                                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-2 py-1 text-[11px] text-zinc-600 dark:text-zinc-300 focus:outline-none focus:border-emerald-500 font-sans rounded-none"
                                            :placeholder="isEn ? 'Details / Acceptance Criteria' : 'Rincian / Kriteria Acceptance'"
                                        ></textarea>
                                    </div>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-1.5 shrink-0 self-end sm:self-center">
                                    <button 
                                        type="button" 
                                        @click="moveTaskToPhase2(idx)" 
                                        class="px-2 py-1 bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                        :title="isEn ? 'Move to Phase 2 Roadmap' : 'Pindahkan ke Fase 2 Roadmap'"
                                        x-text="isEn ? '→ TO ROADMAP' : '→ KE ROADMAP'"
                                    >
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="removeMvpTask(idx)" 
                                        class="px-2 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                        :title="isEn ? 'Delete Task' : 'Hapus Task'"
                                        x-text="isEn ? '× REMOVE' : '× HAPUS'"
                                    >
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Phase 2 Roadmap Task List -->
                    <div x-show="taskEditorTab === 'phase2'" class="space-y-2">
                        <template x-if="phase2Tasks.length === 0">
                            <div class="p-6 text-center text-zinc-400 border border-dashed border-zinc-300 dark:border-zinc-800 font-sans text-xs" x-text="isEn ? 'No tasks in Phase 2 (Roadmap) yet. You can move tasks from Phase 1 or add new ones.' : 'Belum ada task pada Fase 2 (Roadmap). Anda dapat memindahkan task dari Fase 1 atau menambahkan task baru.'">
                            </div>
                        </template>
                        <template x-for="(task, idx) in phase2Tasks" :key="'p2-' + idx">
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-sky-500/30 rounded-none flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-2.5 flex-1">
                                    <span class="px-1.5 py-0.5 bg-sky-500 text-black font-bold text-[10px] shrink-0 mt-1" x-text="String(idx + 1).padStart(2, '0')"></span>
                                    <div class="flex-1 space-y-1">
                                        <input 
                                            type="text" 
                                            x-model="task.title" 
                                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs font-bold text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 rounded-none"
                                            :placeholder="isEn ? 'Task Title' : 'Judul Task'"
                                        />
                                        <textarea 
                                            x-model="task.desc" 
                                            rows="2" 
                                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-2 py-1 text-[11px] text-zinc-600 dark:text-zinc-300 focus:outline-none focus:border-sky-500 font-sans rounded-none"
                                            :placeholder="isEn ? 'Details / Acceptance Criteria' : 'Rincian / Kriteria Acceptance'"
                                        ></textarea>
                                    </div>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-1.5 shrink-0 self-end sm:self-center">
                                    <button 
                                        type="button" 
                                        @click="moveTaskToMvp(idx)" 
                                        class="px-2 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                        :title="isEn ? 'Promote to Phase 1 MVP' : 'Promosikan ke Fase 1 MVP'"
                                        x-text="isEn ? '← TO MVP' : '← KE MVP'"
                                    >
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="removePhase2Task(idx)" 
                                        class="px-2 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                        :title="isEn ? 'Delete Task' : 'Hapus Task'"
                                        x-text="isEn ? '× REMOVE' : '× HAPUS'"
                                    >
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Fixed Modal Footer Actions (Sticky Bottom) -->
            <div class="px-4 sm:px-6 py-3 border-t border-zinc-200 dark:border-zinc-800 shrink-0 bg-zinc-50 dark:bg-zinc-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    <span x-text="isEn ? ('Total: ' + mvpTasks.length + ' MVP Tasks, ' + phase2Tasks.length + ' Roadmap Tasks') : ('Total: ' + mvpTasks.length + ' Fitur MVP, ' + phase2Tasks.length + ' Fitur Roadmap')"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="taskEditorOpen = false" 
                        class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold transition border border-zinc-300 dark:border-zinc-700 rounded-none cursor-pointer text-xs uppercase"
                        x-text="isEn ? 'CLOSE' : 'TUTUP'"
                    >
                    </button>
                    <button 
                        type="button" 
                        @click="submitSaveTasks()" 
                        :disabled="taskEditorSaving"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-400 text-black font-black uppercase tracking-wider transition shadow-md rounded-none cursor-pointer disabled:opacity-50 flex items-center gap-1.5 text-xs"
                    >
                        <span x-show="!taskEditorSaving" x-text="isEn ? 'SAVE CHANGES & UPDATE PRD →' : 'SIMPAN PERUBAHAN & PERBARUI PRD →'"></span>
                        <span x-show="taskEditorSaving" class="inline-block animate-pulse" x-text="isEn ? 'Saving & Recalculating Hash...' : 'Menyimpan & Menghitung Ulang Hash...'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
