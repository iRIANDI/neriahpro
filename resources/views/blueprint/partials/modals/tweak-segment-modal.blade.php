    <!-- Tweak Segment / AI Grounded Architecture Deepener Modal -->
    <div x-show="tweakModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6" style="display: none;">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="tweakModalOpen = false" x-transition.opacity></div>
        <div class="relative bg-white dark:bg-zinc-950 border-2 border-zinc-900 dark:border-zinc-700 w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl rounded-none" x-transition>
            
            <!-- Header -->
            <div class="p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 shrink-0 flex items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 font-mono text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>AI ARCHITECTURE DEEPENER // GROUNDED SPEC TWEAKER</span>
                    </div>
                    <h3 class="font-black text-lg sm:text-xl text-zinc-900 dark:text-zinc-100 uppercase tracking-tight" x-text="tweakSegmentTitle"></h3>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                        Lengkapi dan perdalam segmen spesifikasi ini dengan berpijak langsung pada Dokumen Acuan Proyek (MarkItDown Corpus, Berkas Lampiran, &amp; Brief Awal).
                    </p>
                </div>
                <button type="button" @click="tweakModalOpen = false" class="p-1.5 text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 transition rounded-none font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 custom-prd-scrollbar space-y-4">
                
                <!-- Grounded Source Corpus Indicator Panel -->
                <div class="bg-zinc-100/80 dark:bg-zinc-900/70 border border-zinc-300 dark:border-zinc-700 p-3 sm:p-4 rounded-none">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <div>
                                <div class="font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                                    <span>GROUNDED SOURCE CORPUS</span>
                                    <span class="px-1.5 py-0.2 bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-mono font-bold">TERKUNCI KE DOKUMEN ACUAN</span>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    <template x-if="sourceCorpusLoading">
                                        <span>Memindai berkas spesifikasi &amp; MarkItDown...</span>
                                    </template>
                                    <template x-if="!sourceCorpusLoading && sourceCorpusData">
                                        <span x-text="sourceCorpusData.source_files.length + ' berkas acuan terdeteksi (' + (sourceCorpusData.word_count || 0).toLocaleString() + ' kata, ' + (sourceCorpusData.character_count || 0).toLocaleString() + ' karakter)'"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            @click="sourceCorpusExpanded = !sourceCorpusExpanded" 
                            class="px-2.5 py-1 text-xs font-mono font-bold border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 flex items-center gap-1.5 transition self-start sm:self-auto shrink-0"
                        >
                            <svg class="w-3.5 h-3.5 transition-transform" :class="sourceCorpusExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            <span x-text="sourceCorpusExpanded ? 'Tutup Ringkasan Acuan' : 'Lihat Ringkasan Acuan'"></span>
                        </button>
                    </div>

                    <!-- Collapsible Corpus Preview -->
                    <div x-show="sourceCorpusExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-3 pt-3 border-t border-zinc-300 dark:border-zinc-700 space-y-2">
                        <template x-if="sourceCorpusData && sourceCorpusData.source_files">
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <template x-for="f in sourceCorpusData.source_files" :key="f.name">
                                    <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono text-zinc-800 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span x-text="f.name + ' (' + f.size_kb + ' KB)'"></span>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <div class="max-h-40 overflow-y-auto bg-zinc-950 text-zinc-300 font-mono text-[11px] p-3 border border-zinc-800 custom-prd-scrollbar select-all leading-relaxed whitespace-pre-wrap" x-text="sourceCorpusData ? sourceCorpusData.corpus_text.slice(0, 1500) + (sourceCorpusData.corpus_text.length > 1500 ? '\n\n[... sisa ' + (sourceCorpusData.character_count - 1500).toLocaleString() + ' karakter lainnya terindeks untuk AI ...]' : '') : 'Belum ada konten.'"></div>
                    </div>
                </div>

                <!-- Segment Maturity & Guidance Status Banner -->
                <div x-show="tweakSegmentKey === 'database_erd'" class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs font-mono text-emerald-950 dark:text-emerald-200">
                        <span class="font-bold uppercase tracking-wider block mb-0.5">STATUS SKEMA BASIS DATA: 100% TERVERIFIKASI &amp; SKALABEL</span>
                        <span>Skema ERD ini telah memenuhi standar enterprise (Strict ULID PK/FK, Keyset Cursor O(1), dan Multi-Bahasa Tier 1 JSONB). Anda dapat menambahkan entitas kustom spesifik melalui form instruksi di bawah, atau membiarkan skema prima ini tetap terjaga.</span>
                    </div>
                </div>

                <!-- Mode Switch Tabs -->
                <div class="flex gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <button 
                        type="button" 
                        @click="tweakMode = 'ai'" 
                        :class="tweakMode === 'ai' ? 'bg-emerald-600 text-black font-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700'" 
                        class="px-4 py-2 text-xs font-mono uppercase tracking-wider transition flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>AI DEEPENER DARI ACUAN (REKOMENDASI)</span>
                    </button>
                    <button 
                        type="button" 
                        @click="tweakMode = 'manual'" 
                        :class="tweakMode === 'manual' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-black font-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700'" 
                        class="px-4 py-2 text-xs font-mono uppercase tracking-wider transition flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>EDITOR MANUAL (JSON / CODE)</span>
                    </button>
                </div>

                <!-- AI Mode -->
                <div x-show="tweakMode === 'ai'" class="space-y-4">
                    <!-- Model / Provider Selector -->
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300 mb-1.5">
                            PILIH ENGINE MULTI-MODEL AI:
                        </label>
                        <select x-model="tweakAiProvider" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-xs font-mono px-3 py-2.5 outline-none focus:border-emerald-500 rounded-none">
                            <option value="auto">Auto Failover Multi-Provider (RelayRouter / DeepSeek-R1 / Gemini / Claude)</option>
                            <option value="relayrouter">RelayRouter AI (Universal 1-Key: Claude 3.7 / GPT-4o / DeepSeek)</option>
                            <option value="gemini">Google Gemini 2.5 Flash / Pro (High Context Speed)</option>
                            <option value="deepseek">DeepSeek-R1 Reasoning Engine (Ultra Deep Logic)</option>
                            <option value="anthropic">Anthropic Claude 3.7 Sonnet (Hybrid Architecture Thinking)</option>
                            <option value="openai">OpenAI GPT-4o / o3-mini (High Precision Structured Output)</option>
                        </select>
                    </div>

                    <!-- Directive Textarea with Responsive Dynamic Elaborator Button -->
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-2">
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-mono font-bold uppercase text-zinc-800 dark:text-zinc-200">
                                    <span x-text="isEn ? 'SPECIAL REFINEMENT DIRECTIVE (OPTIONAL):' : 'INSTRUKSI PENAJAMAN KHUSUS (OPSIONAL):'">
                                        INSTRUKSI PENAJAMAN KHUSUS (OPSIONAL):
                                    </span>
                                </label>
                                <span class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 block mt-0.5" x-text="isEn ? 'Leave empty for auto-deepening, or click the dynamic button to auto-synthesize from project corpus & world standards:' : 'Kosongkan untuk pendalaman otomatis, atau klik tombol dinamis untuk merumuskan instruksi dari acuan & standar dunia:'">
                                    Kosongkan untuk pendalaman otomatis, atau klik tombol dinamis untuk merumuskan instruksi dari acuan &amp; standar dunia:
                                </span>
                            </div>

                            <!-- Dynamic Area-Specific AI Elaborator Button -->
                            <button 
                                type="button" 
                                @click="elaborateDirectiveFromAi()" 
                                :disabled="elaboratingDirective"
                                class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 active:bg-emerald-500/30 text-emerald-800 dark:text-emerald-300 font-mono text-xs font-bold border border-emerald-500/50 hover:border-emerald-500 flex items-center gap-2 transition cursor-pointer disabled:opacity-50 shadow-xs self-start sm:self-auto shrink-0 max-w-full rounded-none"
                                :title="isEn ? 'AI compiles automatic refinement directive based on project documents and global engineering standards' : 'AI menyusun instruksi penajaman otomatis berdasarkan ide acuan proyek dan standar rekayasa dunia'"
                            >
                                <template x-if="!elaboratingDirective">
                                    <span class="flex items-center gap-1.5 truncate">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span class="truncate uppercase tracking-tight" x-text="getElaborateButtonLabel()">✨ ELABORASI DARI IDE &amp; STANDAR DUNIA</span>
                                    </span>
                                </template>
                                <template x-if="elaboratingDirective">
                                    <span class="flex items-center gap-2 animate-pulse text-zinc-600 dark:text-zinc-400 truncate">
                                        <svg class="animate-spin w-3.5 h-3.5 text-emerald-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="truncate font-medium" x-text="getElaboratingLoadingLabel()">Menyusun dari Acuan &amp; Standar Dunia...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                        <textarea 
                            x-model="tweakPrompt" 
                            rows="4" 
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 p-3 text-xs sm:text-sm font-sans focus:outline-none focus:border-emerald-500 rounded-none leading-relaxed" 
                            :placeholder="isEn ? 'Example directive: &quot;Deepen vehicle capacity matching, provide inventory hold state machine with 15-minute TTL, and calculate dynamic holiday surcharges...&quot;' : 'Contoh instruksi: &quot;Fokus perdalam modul rekonsiliasi pembayaran kasir, sertakan tabel ledger transaksi keuangan, serta validasi skenario timeout webhook Midtrans...&quot;'"
                        ></textarea>
                    </div>

                    <!-- AI Architecture Standards Checklist -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 mb-0.5">✓ Strict ULID &amp; O(1)</div>
                            <div>Skema database &amp; entitas patuh standar performa jutaan data.</div>
                        </div>
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 mb-0.5">✓ Gherkin &amp; Edge Cases</div>
                            <div>Skenario pengujian konkret bebas placeholder generik.</div>
                        </div>
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 mb-0.5">✓ Auto-Sync Diagram</div>
                            <div>Diagram Mermaid &amp; DDL SQL PostgreSQL terupdate otomatis.</div>
                        </div>
                    </div>
                </div>

                <!-- Manual Mode -->
                <div x-show="tweakMode === 'manual'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                            EDITOR KONTEN STRUKTURAL (JSON / MARKDOWN):
                        </label>
                        <span class="text-[10px] font-mono text-zinc-400">Edit langsung data mentah segmen ini</span>
                    </div>
                    <textarea 
                        x-model="tweakContent" 
                        rows="14" 
                        class="w-full bg-zinc-950 text-emerald-400 border border-zinc-700 font-mono text-xs p-3 focus:outline-none focus:border-emerald-500 custom-prd-scrollbar leading-relaxed rounded-none" 
                        placeholder="Ketik data JSON atau aturan bisnis baru di sini..."
                    ></textarea>
                </div>

            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-between gap-3 shrink-0">
                <div class="text-[11px] font-mono text-zinc-500 hidden sm:block">
                    Perubahan akan disimpan permanen ke <code class="text-emerald-600 dark:text-emerald-400">prd_content</code> &amp; <code class="text-emerald-600 dark:text-emerald-400">prd.md</code>.
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button 
                        type="button" 
                        @click="tweakModalOpen = false" 
                        class="px-4 py-2 text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 transition rounded-none"
                    >
                        Batal
                    </button>
                    
                    <button 
                        type="button" 
                        @click="submitTweak()" 
                        :disabled="tweakLoading" 
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black uppercase text-xs transition flex items-center justify-center gap-2 disabled:opacity-50 min-w-[170px] rounded-none shadow-[2px_2px_0px_#065f46]"
                    >
                        <template x-if="!tweakLoading">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span x-text="tweakMode === 'manual' ? 'SIMPAN PERUBAHAN' : 'EKSEKUSI PENDALAMAN AI'"></span>
                            </span>
                        </template>
                        <template x-if="tweakLoading">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="tweakMode === 'manual' ? 'Menyimpan...' : 'Membedah Acuan...'"></span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>

