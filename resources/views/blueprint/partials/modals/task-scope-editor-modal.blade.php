    <!-- INTERACTIVE TASK & SCOPE EDITOR MODAL (PRE-SIGN ELABORATION) -->
    <div 
        x-show="taskEditorOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 font-mono text-xs no-print"
        @keydown.escape.window="taskEditorOpen = false"
    >
        <div 
            class="bg-white dark:bg-zinc-900 border-2 border-amber-500/80 max-w-4xl w-full p-4 sm:p-6 shadow-2xl relative rounded-none flex flex-col max-h-[92vh]"
            @click.outside="taskEditorOpen = false"
        >
            <!-- Close Button -->
            <button 
                @click="taskEditorOpen = false" 
                class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-lg font-bold cursor-pointer"
            >
                &times;
            </button>

            <!-- Modal Header -->
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 flex items-center gap-3">
                <span class="w-8 h-8 bg-amber-500 text-black flex items-center justify-center font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </span>
                <div>
                    <h3 class="text-base sm:text-lg font-black uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span>Interactive Task &amp; Scope Editor</span>
                        <span class="px-2 py-0.5 text-[10px] bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40 font-bold">PRE-SIGN ELABORATION</span>
                    </h3>
                    <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-0.5 font-sans">
                        Elaborasi, sesuaikan, tambah, atau kurangi task teknis &amp; kriteria fitur sebelum dokumen dikunci dan ditandatangani.
                    </p>
                </div>
            </div>

            <!-- Explanatory Notice -->
            <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 text-amber-900 dark:text-amber-300 text-[11px] mb-4 font-sans leading-relaxed">
                <strong>Catatan Sinkronisasi:</strong> Setiap penambahan atau perubahan task di bawah ini akan memperbarui rincian estimasi biaya (itemized breakdown), sprint roadmap, dan menghitung ulang kode integritas kriptografis SHA-256 secara otomatis saat Anda menekan tombol <strong>Simpan Perubahan</strong>.
            </div>

            <!-- Quick Add Task Bar -->
            <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-3 sm:p-4 mb-4 rounded-none space-y-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-amber-500 inline-block"></span>
                    <span>TAMBAH TASK / FITUR BARU:</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                    <div class="sm:col-span-5">
                        <input 
                            type="text" 
                            x-model="newTaskTitle" 
                            placeholder="Judul Task / Fitur (mis: Integrasi Pembayaran Midtrans Snap)" 
                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-amber-500 rounded-none"
                            @keydown.enter="addNewTask()"
                        />
                    </div>
                    <div class="sm:col-span-4">
                        <input 
                            type="text" 
                            x-model="newTaskDesc" 
                            placeholder="Deskripsi singkat & kriteria acceptance" 
                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-amber-500 rounded-none"
                            @keydown.enter="addNewTask()"
                        />
                    </div>
                    <div class="sm:col-span-3 flex gap-2">
                        <select 
                            x-model="newTaskTarget" 
                            class="w-1/2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-2 text-[11px] text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-amber-500 rounded-none"
                        >
                            <option value="mvp">Fase 1 (MVP)</option>
                            <option value="phase2">Fase 2 (Roadmap)</option>
                        </select>
                        <button 
                            type="button" 
                            @click="addNewTask()" 
                            class="w-1/2 bg-amber-500 hover:bg-amber-400 text-black font-bold text-xs px-3 py-2 transition flex items-center justify-center gap-1 rounded-none cursor-pointer"
                        >
                            <span>+ TAMBAH</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher: Fase 1 MVP vs Fase 2 Roadmap -->
            <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-3">
                <button 
                    type="button" 
                    @click="taskEditorTab = 'mvp'" 
                    class="px-3 py-1.5 text-xs font-bold transition flex items-center gap-2 border rounded-none cursor-pointer"
                    :class="taskEditorTab === 'mvp' ? 'bg-emerald-500 text-black border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:text-zinc-900 dark:hover:text-zinc-100'"
                >
                    <span>FASE 1: FITUR WAJIB (MVP)</span>
                    <span class="px-1.5 py-0.2 text-[10px] font-bold" :class="taskEditorTab === 'mvp' ? 'bg-black text-emerald-400' : 'bg-zinc-300 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200'" x-text="mvpTasks.length"></span>
                </button>
                <button 
                    type="button" 
                    @click="taskEditorTab = 'phase2'" 
                    class="px-3 py-1.5 text-xs font-bold transition flex items-center gap-2 border rounded-none cursor-pointer"
                    :class="taskEditorTab === 'phase2' ? 'bg-sky-500 text-black border-sky-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:text-zinc-900 dark:hover:text-zinc-100'"
                >
                    <span>FASE 2: ROADMAP SUSULAN</span>
                    <span class="px-1.5 py-0.2 text-[10px] font-bold" :class="taskEditorTab === 'phase2' ? 'bg-black text-sky-400' : 'bg-zinc-300 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200'" x-text="phase2Tasks.length"></span>
                </button>
            </div>

            <!-- Task List Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto custom-prd-scrollbar space-y-2 pr-1 min-h-[240px]">
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
                                        placeholder="Judul Task"
                                    />
                                    <textarea 
                                        x-model="task.desc" 
                                        rows="2" 
                                        class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-2 py-1 text-[11px] text-zinc-600 dark:text-zinc-300 focus:outline-none focus:border-emerald-500 font-sans rounded-none"
                                        placeholder="Rincian / Kriteria Acceptance"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-1.5 shrink-0 self-end sm:self-center">
                                <button 
                                    type="button" 
                                    @click="moveTaskToPhase2(idx)" 
                                    class="px-2 py-1 bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                    title="Pindahkan ke Fase 2 Roadmap"
                                >
                                    &rarr; KE ROADMAP
                                </button>
                                <button 
                                    type="button" 
                                    @click="removeMvpTask(idx)" 
                                    class="px-2 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                    title="Hapus Task"
                                >
                                    &times; HAPUS
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Phase 2 Roadmap Task List -->
                <div x-show="taskEditorTab === 'phase2'" class="space-y-2">
                    <template x-if="phase2Tasks.length === 0">
                        <div class="p-6 text-center text-zinc-400 border border-dashed border-zinc-300 dark:border-zinc-800">
                            Belum ada task pada Fase 2 (Roadmap). Anda dapat memindahkan task dari Fase 1 atau menambahkan task baru.
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
                                        placeholder="Judul Task"
                                    />
                                    <textarea 
                                        x-model="task.desc" 
                                        rows="2" 
                                        class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-2 py-1 text-[11px] text-zinc-600 dark:text-zinc-300 focus:outline-none focus:border-sky-500 font-sans rounded-none"
                                        placeholder="Rincian / Kriteria Acceptance"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-1.5 shrink-0 self-end sm:self-center">
                                <button 
                                    type="button" 
                                    @click="moveTaskToMvp(idx)" 
                                    class="px-2 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                    title="Promosikan ke Fase 1 MVP"
                                >
                                    &larr; KE MVP
                                </button>
                                <button 
                                    type="button" 
                                    @click="removePhase2Task(idx)" 
                                    class="px-2 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-[10px] font-bold transition rounded-none cursor-pointer"
                                    title="Hapus Task"
                                >
                                    &times; HAPUS
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3 mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    Total: <strong class="text-zinc-800 dark:text-zinc-200" x-text="mvpTasks.length"></strong> Fitur MVP, <strong class="text-zinc-800 dark:text-zinc-200" x-text="phase2Tasks.length"></strong> Fitur Roadmap
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="taskEditorOpen = false" 
                        class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold transition border border-zinc-300 dark:border-zinc-700 rounded-none cursor-pointer"
                    >
                        TUTUP
                    </button>
                    <button 
                        type="button" 
                        @click="submitSaveTasks()" 
                        :disabled="taskEditorSaving"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-400 text-black font-black uppercase tracking-wider transition shadow-md rounded-none cursor-pointer disabled:opacity-50 flex items-center gap-1.5"
                    >
                        <span x-show="!taskEditorSaving">SIMPAN PERUBAHAN &amp; PERBARUI PRD &rarr;</span>
                        <span x-show="taskEditorSaving" class="inline-block animate-pulse">Menyimpan &amp; Menghitung Ulang Hash...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

