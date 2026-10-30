<div class="fixed bottom-6 right-6 z-50 font-sans" x-data="{ open: @entangle('windowState').live }">

    {{-- Floating Trigger Button --}}
    <button type="button"
            x-show="open === 'closed'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="scale-90 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            wire:click="toggleWindow"
            class="flex items-center gap-2.5 px-4 py-2.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border border-zinc-800 dark:border-zinc-200 shadow-2xl rounded-xs hover:bg-black dark:hover:bg-white transition-all duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <span class="inline-block w-2.5 h-2.5 bg-emerald-500 animate-pulse rounded-xs"></span>
        <span class="font-mono text-xs font-bold tracking-wider uppercase">AI Copilot</span>
        <span class="text-[10px] px-1.5 py-0.5 bg-zinc-800 dark:bg-zinc-200 font-mono text-zinc-300 dark:text-zinc-700 rounded-xs">Agentic</span>
    </button>

    {{-- Copilot Floating Window --}}
    <div x-show="open !== 'closed'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="w-[420px] sm:w-[500px] h-[640px] max-h-[85vh] bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 shadow-2xl rounded-xs flex flex-col overflow-hidden text-zinc-950 dark:text-zinc-100">

        {{-- Window Header --}}
        <div class="px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-xs"></span>
                <div>
                    <h3 class="font-mono text-xs font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                        Neriah Pro AI Copilot
                    </h3>
                    <p class="text-[10px] text-zinc-500 font-mono">
                        Multi-Agent • RAG pgvector • Native Tools
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="button"
                        wire:click="createNewChat"
                        title="Buat Sesi Baru"
                        class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 rounded-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <button type="button"
                        wire:click="closeWindow"
                        title="Tutup Window"
                        class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 rounded-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Navigation Sub-Tabs --}}
        <div class="flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 text-xs font-mono shrink-0">
            <button type="button"
                    wire:click="$set('activeTab', 'chat')"
                    class="flex-1 py-2 text-center border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'chat' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                Chat Copilot
            </button>
            <button type="button"
                    wire:click="$set('activeTab', 'knowledge')"
                    class="flex-1 py-2 text-center border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'knowledge' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                Knowledge ({{ $totalKnowledgeChunks }})
            </button>
            <button type="button"
                    wire:click="$set('activeTab', 'cctv')"
                    class="flex-1 py-2 text-center border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'cctv' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                CCTV Audit
            </button>
        </div>

        {{-- TAB 1: CHAT INTERACTION --}}
        @if ($activeTab === 'chat')
            {{-- Message History Stream --}}
            <div class="flex-1 p-4 overflow-y-auto space-y-4 text-xs font-sans">
                @foreach ($messages as $msg)
                    <div class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center gap-1.5 mb-1 font-mono text-[10px] text-zinc-500">
                            <span>{{ $msg['role'] === 'user' ? 'Lead Architect / Admin' : 'Autonomous AI' }}</span>
                            <span>•</span>
                            <span>{{ $msg['created_at'] ?? '' }}</span>
                        </div>

                        <div class="max-w-[88%] p-3 rounded-xs border text-xs leading-relaxed {{ $msg['role'] === 'user' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 border-zinc-800 dark:border-zinc-200' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-300 dark:border-zinc-800' }}">
                            <div class="prose prose-xs dark:prose-invert max-w-none break-words whitespace-pre-wrap font-sans">{{ $msg['content'] }}</div>

                            {{-- Tool Calls Execution Audit --}}
                            @if (!empty($msg['tool_calls']))
                                <div class="mt-2.5 pt-2 border-t border-zinc-300 dark:border-zinc-800 font-mono text-[10px] space-y-1">
                                    <div class="text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Executed Native Tools:</span>
                                    </div>
                                    @foreach ($msg['tool_calls'] as $tc)
                                        <div class="p-1.5 bg-zinc-200/60 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs text-zinc-800 dark:text-zinc-300">
                                            <span class="font-bold text-zinc-950 dark:text-white">{{ $tc['tool'] }}</span>
                                            @if(isset($tc['result']['message']))
                                                <p class="text-[9px] text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $tc['result']['message'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($isLoading)
                    <div class="flex items-center gap-2 p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs w-fit">
                        <span class="w-2 h-2 bg-emerald-500 rounded-xs animate-ping"></span>
                        <span class="font-mono text-xs text-zinc-600 dark:text-zinc-400 animate-pulse">Menghubungi Agentic Engine &amp; PostgreSQL...</span>
                    </div>
                @endif
            </div>

            {{-- Input Box --}}
            <div class="p-3 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
                <form wire:submit.prevent="sendMessage" class="flex gap-2">
                    <input type="text"
                           wire:model="inputMessage"
                           placeholder="Ketik instruksi (e.g. cari blueprint, cek domain, buat kontrak)..."
                           class="flex-1 px-3 py-2 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs text-xs text-zinc-950 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-emerald-500 placeholder:text-zinc-400"
                           {{ $isLoading ? 'disabled' : '' }}>

                    <button type="submit"
                            {{ $isLoading ? 'disabled' : '' }}
                            class="px-4 py-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 font-mono text-xs font-bold rounded-xs hover:bg-black dark:hover:bg-white transition-all cursor-pointer disabled:opacity-50 flex items-center gap-1.5">
                        <span>Kirim</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>
            </div>
        @endif

        {{-- TAB 2: KNOWLEDGE BASE (RAG • CAG • KAG) --}}
        @if ($activeTab === 'knowledge')
            <div class="flex-1 p-4 overflow-y-auto space-y-4 text-xs font-sans">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono font-bold text-xs uppercase text-zinc-900 dark:text-white">Tri-Hybrid AI Memory</span>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-200 font-mono text-[10px] font-bold border border-emerald-400 dark:border-emerald-700 rounded-xs">pgvector Ready</span>
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-400 text-xs leading-relaxed">
                        Arsitektur memori Neriah Pro mengombinasikan <strong>RAG</strong> (Dense 768-dim + Sparse Lexical RRF), <strong>CAG</strong> (Cache-Augmented Generation O(1)), dan <strong>KAG</strong> (Graph Knowledge Rule Engine).
                    </p>

                    <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <span class="font-mono text-zinc-500 text-[11px]">Total Chunks: <strong>{{ $totalKnowledgeChunks }}</strong></span>
                        <button type="button"
                                wire:click="syncKnowledge"
                                class="px-3 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 font-mono text-[11px] font-bold rounded-xs hover:bg-black dark:hover:bg-white transition-all">
                            Sinkronkan 10 Pilar
                        </button>
                    </div>
                </div>

                {{-- 10 Pillars Preview --}}
                <div class="space-y-2">
                    <h4 class="font-mono text-xs font-bold uppercase text-zinc-700 dark:text-zinc-300">10 Pilar Software Factory OS</h4>
                    <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 rounded-xs font-mono text-[11px] space-y-1">
                        <div>1. PRD 26 Parameter &amp; WBS Gantt</div>
                        <div>2. PostgreSQL 16 Strict ULID &amp; Keyset Cursor O(1)</div>
                        <div>3. Production Docker Environment</div>
                        <div>4. Synthetic Seeder 100+ Baris</div>
                        <div>5. AI Agent Rules &amp; Engineering Governance</div>
                        <div>6. Pest ApiContractTest &amp; Defense Suite</div>
                        <div>7. CI/CD Pipeline &amp; deploy.sh 6 Skenario</div>
                        <div>8. Commercialization &amp; SHA-256 Scope Lock</div>
                        <div>9. Managed Sprint Capacity (Max 2-3 Proyek)</div>
                        <div>10. Customer Portal &amp; Domain/Hosting Lifecycle</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 3: CCTV AUDIT TRACES --}}
        @if ($activeTab === 'cctv')
            <div class="flex-1 p-4 overflow-y-auto space-y-2.5 font-mono text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="font-bold uppercase text-zinc-900 dark:text-white">CCTV Workflow Traces</span>
                    <span class="text-[10px] text-zinc-500">Auto-recorded</span>
                </div>

                @forelse ($recentTraces as $trace)
                    <div class="p-2 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">{{ $trace->title }}</span>
                            <span class="text-[9px] px-1.5 py-0.2 {{ $trace->status === 'success' ? 'bg-emerald-100 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700' : 'bg-rose-100 text-rose-950 dark:bg-rose-950 dark:text-rose-200 border border-rose-400 dark:border-rose-700' }} rounded-xs font-bold">
                                {{ strtoupper($trace->status) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-zinc-500">{{ $trace->summary ?? 'Tidak ada ringkasan.' }}</p>
                        <div class="text-[9px] text-zinc-400 flex items-center justify-between pt-1 border-t border-zinc-200 dark:border-zinc-800">
                            <span>Stage: {{ $trace->stage }}</span>
                            <span>{{ $trace->execution_time_ms ? $trace->execution_time_ms . 'ms' : '' }} • {{ $trace->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-xs italic">Belum ada jejak trace CCTV yang terekam.</p>
                @endforelse
            </div>
        @endif
    </div>
</div>

