    <!-- AI AGENT INGESTION COCKPIT & PROMPT ASSISTANT MODAL (ANTIGRAVITY / CURSOR / CLAUDE / WINDSURF) -->
    <div 
        x-show="showAiPromptModal" 
        x-cloak 
        @keydown.escape.window="showAiPromptModal = false"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-xs flex items-start justify-center p-3 sm:p-6 no-print font-mono"
    >
        <div 
            @click.outside="showAiPromptModal = false" 
            class="bg-zinc-950 border-2 border-emerald-500/80 max-w-4xl w-full my-auto max-h-[92vh] flex flex-col p-5 sm:p-7 rounded-none text-zinc-100 shadow-2xl relative"
        >
            <!-- Sticky Header -->
            <div class="flex items-center justify-between border-b border-zinc-800 pb-3 mb-4 shrink-0">
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

            <!-- Scrollable Body Content -->
            <div class="overflow-y-auto flex-1 pr-1.5 space-y-4">
                <!-- Agent Selector Tabs -->
                <div>
                    <label class="text-[11px] text-zinc-400 uppercase font-bold block mb-2">1. PILIH AI CODING AGENT TARGET:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs">
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'antigravity'"
                        :class="selectedPromptAgent === 'antigravity' ? 'bg-emerald-500 text-black font-bold border-emerald-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Antigravity IDE</span>
                        <span class="text-[9px] opacity-75">Google DeepMind</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'cursor'"
                        :class="selectedPromptAgent === 'cursor' ? 'bg-sky-500 text-black font-bold border-sky-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Cursor Composer</span>
                        <span class="text-[9px] opacity-75">Cmd+I Multi-File</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'claude'"
                        :class="selectedPromptAgent === 'claude' ? 'bg-amber-500 text-black font-bold border-amber-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Claude Code CLI</span>
                        <span class="text-[9px] opacity-75">Terminal Autonomous</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'windsurf'"
                        :class="selectedPromptAgent === 'windsurf' ? 'bg-purple-500 text-black font-bold border-purple-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2.5 text-center border transition flex flex-col items-center gap-1 cursor-pointer"
                    >
                        <span class="text-[10px] uppercase tracking-wider font-mono">Windsurf Cascade</span>
                        <span class="text-[9px] opacity-75">Cascade Flow</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptAgent = 'devin'"
                        :class="selectedPromptAgent === 'devin' ? 'bg-rose-500 text-black font-bold border-rose-400' : 'bg-zinc-900 text-zinc-300 border-zinc-800 hover:bg-zinc-800'"
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
                        :class="selectedPromptSprint === 'all' ? 'bg-zinc-100 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Master Kickoff
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint1'"
                        :class="selectedPromptSprint === 'sprint1' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 1: DB &amp; ULID
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint2'"
                        :class="selectedPromptSprint === 'sprint2' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 2: Engine
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint3'"
                        :class="selectedPromptSprint === 'sprint3' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 3: UI React
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint4'"
                        :class="selectedPromptSprint === 'sprint4' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800'"
                        class="p-2 border transition text-center cursor-pointer"
                    >
                        Sprint 4: Security
                    </button>
                    <button 
                        type="button" 
                        @click="selectedPromptSprint = 'sprint5'"
                        :class="selectedPromptSprint === 'sprint5' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800'"
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
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-zinc-800 shrink-0">
                <button 
                    type="button" 
                    @click="showAiPromptModal = false"
                    class="w-full sm:w-auto px-4 py-2 border border-zinc-700 hover:bg-zinc-800 text-zinc-300 text-xs uppercase font-bold transition cursor-pointer"
                >
                    Tutup
                </button>
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <a 
                        href="{{ route('blueprint.download-cursorrules', $blueprint->slug) }}"
                        class="w-full sm:w-auto px-4 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-emerald-400 border border-emerald-500/30 text-xs font-mono uppercase font-bold transition flex items-center justify-center gap-1.5"
                        title="Unduh file .cursorrules langsung ke project"
                    >
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>UNDUH .CURSORRULES</span>
                    </a>
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
    </div>

