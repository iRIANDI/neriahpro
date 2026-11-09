@php
    $activeLocale = $locale ?? app()->getLocale();
    $isEn = ($activeLocale === 'en');
@endphp

<!-- AI AGENT SUITE & ORCHESTRATION MODAL -->
<div 
    x-show="showAiSuiteModal" 
    x-cloak 
    @keydown.escape.window="showAiSuiteModal = false"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-5 print:hidden"
    style="display: none;"
>
    <!-- Backdrop Overlay -->
    <div 
        class="fixed inset-0 bg-black/85 backdrop-blur-md transition-opacity" 
        @click="showAiSuiteModal = false" 
        x-transition.opacity
    ></div>

    <!-- Modal Dialog Window -->
    <div 
        @click.stop
        class="relative z-10 bg-white dark:bg-zinc-950 border-2 border-emerald-500 w-full max-w-2xl rounded-none shadow-2xl flex flex-col max-h-[88vh] overflow-hidden text-zinc-900 dark:text-zinc-100"
        x-transition
    >
        <!-- Modal Header -->
        <div class="px-4 sm:px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50 dark:bg-zinc-900/60">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 bg-emerald-500 rounded-none inline-block animate-pulse"></span>
                <div>
                    <h3 class="text-sm sm:text-base font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2 flex-wrap">
                        <span x-show="locale === 'en'">AI AGENT SUITE &amp; ORCHESTRATION</span>
                        <span x-show="locale !== 'en'">SUITE AI &amp; ORKESTRASI ARSITEKTUR</span>
                        <span class="text-[10px] px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-950 dark:text-emerald-300 border border-emerald-400 font-bold rounded-none">
                            ENGINE ACTIVE
                        </span>
                    </h3>
                    <p class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 mt-0.5">
                        <span x-show="locale === 'en'">Intelligent engineering tools, prompt helpers, radar advisories, and sprint cockpits.</span>
                        <span x-show="locale !== 'en'">Peralatan kecerdasan buatan, asisten prompt, radar arsitektur, dan cockpit eksekusi sprint.</span>
                    </p>
                </div>
            </div>

            <button 
                type="button" 
                @click="showAiSuiteModal = false" 
                class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-2xl font-bold p-1 leading-none cursor-pointer transition"
                aria-label="Tutup Modal"
            >
                &times;
            </button>
        </div>

        <!-- Scrollable AI Tools List Body (Portrait & Desktop Optimized) -->
        <div class="p-4 sm:p-6 overflow-y-auto custom-prd-scrollbar flex-1 space-y-3 font-mono text-xs">

            <!-- 1. Tech Radar & Advisory -->
            <div class="p-4 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <span class="text-2xl p-2 bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-400 text-emerald-700 dark:text-emerald-300 shrink-0">✨</span>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-black text-sm text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">Tech Radar &amp; Engineering Advisory</span>
                                <span x-show="locale !== 'en'">Saran Rekayasa &amp; Tech Radar</span>
                            </span>
                            @if($blueprint->isPurchasedPackage())
                                <span class="text-[9px] bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-1.5 py-0.5 font-bold border border-emerald-500/30">READY</span>
                            @else
                                <span class="text-[9px] bg-amber-500/20 text-amber-600 dark:text-amber-400 px-1.5 py-0.5 font-bold border border-amber-500/30">PRO REPAIR</span>
                            @endif
                        </div>
                        <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                            <span x-show="locale === 'en'">Deep architectural scan: stack evaluation, dependency health, anti-patterns, &amp; enterprise upgrade path.</span>
                            <span x-show="locale !== 'en'">Audit mendalam arsitektur: evaluasi stack, radar dependensi, deteksi anti-pattern, &amp; modul enterprise.</span>
                        </p>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="showAiSuiteModal = false; openAdvisoryModal()"
                    class="px-3.5 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-black uppercase text-xs shrink-0 cursor-pointer transition text-center"
                >
                    <span x-show="locale === 'en'">OPEN RADAR &rarr;</span>
                    <span x-show="locale !== 'en'">BUKA RADAR &rarr;</span>
                </button>
            </div>

            <!-- 2. Prompt Agent Helper -->
            <div class="p-4 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <span class="text-2xl p-2 bg-purple-100 dark:bg-purple-950/80 border border-purple-400 text-purple-700 dark:text-purple-300 shrink-0">💬</span>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-black text-sm text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">Prompt Agent Helper</span>
                                <span x-show="locale !== 'en'">Asisten Prompt AI IDE</span>
                            </span>
                            <span class="text-[9px] bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 px-1.5 py-0.5 font-bold border border-purple-400">AI IDE COCKPIT</span>
                        </div>
                        <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                            <span x-show="locale === 'en'">Copy-paste ready system prompts tailored for Claude Code, Cursor, Windsurf, &amp; Antigravity IDE.</span>
                            <span x-show="locale !== 'en'">Salin prompt sistem siap tempel yang dioptimasi untuk Claude Code, Cursor, Windsurf, &amp; Antigravity IDE.</span>
                        </p>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="showAiSuiteModal = false; showAiPromptModal = true"
                    class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-black uppercase text-xs shrink-0 cursor-pointer transition text-center"
                >
                    <span x-show="locale === 'en'">GET PROMPT &rarr;</span>
                    <span x-show="locale !== 'en'">SALIN PROMPT &rarr;</span>
                </button>
            </div>

            <!-- 3. AI Sprint Cockpit -->
            <div class="p-4 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <span class="text-2xl p-2 bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-400 text-emerald-700 dark:text-emerald-300 shrink-0">🎯</span>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-black text-sm text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">AI Sprint Cockpit &amp; Execution</span>
                                <span x-show="locale !== 'en'">Cockpit Eksekusi Sprint AI</span>
                            </span>
                            <span class="text-[9px] bg-emerald-500 text-black px-1.5 py-0.5 font-black" x-text="getDevCompletedCount() + '/' + {{ $totalDevSteps }} + ' DONE'"></span>
                        </div>
                        <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                            <span x-show="locale === 'en'">Feature-by-feature implementation checklist, Gherkin test assertions, &amp; vertical slice tracker.</span>
                            <span x-show="locale !== 'en'">Checklist implementasi per fitur, skenario tes Gherkin, dan pelacak progres vertical slice sistem.</span>
                        </p>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="showAiSuiteModal = false; jumpTo('section-3-5')"
                    class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-xs shrink-0 cursor-pointer transition text-center"
                >
                    <span x-show="locale === 'en'">JUMP TO COCKPIT &rarr;</span>
                    <span x-show="locale !== 'en'">BUKA COCKPIT &rarr;</span>
                </button>
            </div>

            <!-- 4. Developer Execution Guide & Docker Runtime -->
            <div class="p-4 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <span class="text-2xl p-2 bg-zinc-200 dark:bg-zinc-800 border border-zinc-400 dark:border-zinc-600 text-zinc-900 dark:text-zinc-100 shrink-0">🚀</span>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-black text-sm text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">Execution Guide &amp; Docker / Coolify Stack</span>
                                <span x-show="locale !== 'en'">Panduan Eksekusi &amp; Stack Docker / Coolify</span>
                            </span>
                            <span class="text-[9px] bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 px-1.5 py-0.5 font-bold">3 WAYS</span>
                        </div>
                        <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                            <span x-show="locale === 'en'">Comprehensive setup for Docker dev container, Antigravity IDE, &amp; Coolify one-click deployment.</span>
                            <span x-show="locale !== 'en'">Petunjuk lengkap runtime Docker compose lokal, sesi chat Antigravity, &amp; deploy Coolify.</span>
                        </p>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="showAiSuiteModal = false; showExecutionGuideModal = true"
                    class="px-3.5 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-black uppercase text-xs shrink-0 cursor-pointer transition text-center"
                >
                    <span x-show="locale === 'en'">VIEW GUIDE &rarr;</span>
                    <span x-show="locale !== 'en'">LIHAT PANDUAN &rarr;</span>
                </button>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-4 sm:px-6 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/60 flex items-center justify-between font-mono text-xs">
            <span class="text-zinc-500 text-[11px]">
                <span x-show="locale === 'en'">Zero-hallucination agent runtime verified.</span>
                <span x-show="locale !== 'en'">Terverifikasi runtime AI zero-hallucination.</span>
            </span>
            <button 
                type="button" 
                @click="showAiSuiteModal = false"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-bold uppercase rounded-none cursor-pointer"
            >
                <span x-show="locale === 'en'">CLOSE</span>
                <span x-show="locale !== 'en'">TUTUP</span>
            </button>
        </div>
    </div>
</div>

