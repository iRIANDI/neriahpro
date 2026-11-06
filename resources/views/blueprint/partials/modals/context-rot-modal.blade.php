    <!-- CONTEXT-ROT PREVENTION & RAW MD EDUCATIONAL WARNING MODAL -->
    <div 
        x-show="showRawMdWarningModal" 
        x-cloak 
        @keydown.escape.window="showRawMdWarningModal = false"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 no-print font-sans"
    >
        <div 
            @click.outside="showRawMdWarningModal = false" 
            class="bg-white dark:bg-zinc-950 border-2 border-amber-500 max-w-xl w-full my-auto flex flex-col p-6 rounded-none text-zinc-900 dark:text-zinc-100 shadow-2xl relative"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-amber-500 rounded-none inline-block animate-pulse"></span>
                    <h3 class="text-sm sm:text-base font-mono font-black uppercase text-zinc-900 dark:text-white tracking-wide">
                        PERINGATAN ARSITEKTUR: RISIKO CONTEXT-ROT
                    </h3>
                </div>
                <button type="button" @click="showRawMdWarningModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 font-mono text-sm cursor-pointer">&times;</button>
            </div>

            <!-- Body -->
            <div class="space-y-4 text-xs">
                <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-400 dark:border-amber-700 text-amber-950 dark:text-amber-200 leading-relaxed font-sans">
                    <strong class="font-mono uppercase block mb-1 text-amber-950 dark:text-amber-300 font-black">⚠️ Perhatian Pengembang &amp; Pengguna AI Coding Agent:</strong>
                    Menyalin atau memasukkan seluruh dokumen Master PRD (ribuan baris teks) sekaligus ke dalam context window AI Agent (Cursor Composer, Claude Code, Windsurf, Antigravity IDE) akan memicu <strong>Context Amnesia, Halusinasi Relasional, dan Pemotongan Kode Parsial (Truncation)</strong>.
                </div>

                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                    <span class="font-mono font-black uppercase text-zinc-900 dark:text-white block text-[11px]">
                        Rekomendasi Alur Rekayasa Perangkat Lunak Otonom (10/10 Enterprise Flow):
                    </span>
                    <ol class="list-decimal list-inside space-y-1.5 text-zinc-700 dark:text-zinc-300 font-sans leading-normal">
                        <li>Buka <strong>Cockpit Pelaksanaan Sprint (Bab 3.5)</strong> untuk mengeksekusi fitur per vertikal slice secara terisolasi.</li>
                        <li>Unduh <strong>Initial Repo Scaffold (.ZIP 10 Pilar)</strong> di Langkah 1 Cockpit untuk fondasi Docker, SQL DDL, Seeder, dan Rules.</li>
                        <li>Gunakan Master PRD (.MD) ini hanya untuk arsip dokumen, audit kontrak, atau knowledge-base offline.</li>
                    </ol>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-2.5 pt-4 mt-4 border-t border-zinc-200 dark:border-zinc-800">
                <button 
                    type="button" 
                    @click="showRawMdWarningModal = false; jumpTo('section-3-5')" 
                    class="w-full sm:w-auto px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase rounded-none transition cursor-pointer flex items-center justify-center gap-1.5 shadow-none"
                >
                    <span>&larr; BUKA COCKPIT SPRINT (DISARANKAN)</span>
                </button>
                <button 
                    type="button" 
                    @click="proceedWithRawMd()" 
                    class="w-full sm:w-auto px-4 py-2.5 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-xs uppercase font-bold rounded-none transition cursor-pointer"
                >
                    <span x-text="rawMdActionType === 'download' ? 'TETAP UNDUH RAW .MD' : 'TETAP SALIN RAW .MD'">TETAP LANJUTKAN</span>
                </button>
            </div>
        </div>
    </div>
