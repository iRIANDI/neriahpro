@php
    $isFlowImpacted = in_array('workflow', $prd['_impacted_segments'] ?? []);
    $rawWorkflows = $rawWorkflows ?? ($prd['workflow'] ?? []);
    $isEn = ($isEn ?? false) || ($locale ?? 'id') === 'en';
@endphp
            <section id="section-4" class="bg-white dark:bg-zinc-900 border {{ $isFlowImpacted ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-800' }} p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target transition-all">
                <div class="flex flex-col gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 cursor-pointer select-none" @click="toggleSection('section-4')">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">06</span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">
                                    <span x-show="locale === 'en'">Core System Workflows (User Journey)</span>
                                    <span x-show="locale !== 'en'">Alur Kerja Utama (User Flow)</span>
                                </h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                    <span x-show="locale === 'en'">Transaction Flow Diagrams, Visual State Machine &amp; Business Validations</span>
                                    <span x-show="locale !== 'en'">Diagram Alur Transaksi, Interaksi Visual &amp; Validasi Bisnis</span>
                                </p>
                            </div>
                        </div>

                        <!-- Actions & Tabs -->
                        <div class="flex flex-wrap items-center gap-2 no-print">
                            <!-- Flow View Tabs -->
                            <div class="flex items-center gap-1 font-mono text-xs">
                                <button 
                                    @click.stop="flowTab = 'visual'" 
                                    :class="flowTab === 'visual' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                                    class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span>VISUAL PIPELINE</span>
                                </button>
                                <button 
                                    @click.stop="flowTab = 'mermaid'; $nextTick(() => window.renderMermaidDiagram('mermaid-flow-target', 'mermaid-flow-source'))" 
                                    :class="flowTab === 'mermaid' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                                    class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                    <span>MERMAID CHART</span>
                                </button>
                                <button 
                                    @click.stop="flowTab = 'detailed'" 
                                    :class="flowTab === 'detailed' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                                    class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                    <span x-text="locale === 'en' ? 'DETAILED STEPS (GRID)' : 'RINCIAN LANGKAH (GRID)'">RINCIAN LANGKAH (GRID)</span>
                                </button>
                            </div>

                            @if(!($isScopeLocked ?? false) || ($blueprint->project_status === 'Retail License') || (auth()->user()?->isSuperAdmin() ?? false))
                                <button 
                                    type="button" 
                                    @click.stop="openTweakModal('alurs_kerja', locale === 'en' ? 'Core System Workflows (User Journey)' : 'Alur Kerja Utama (User Flow)')" 
                                    class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs border border-emerald-500/30 flex items-center gap-1.5 transition shrink-0 cursor-pointer"
                                    :title="locale === 'en' ? 'Expand branches and exception flows from grounded project documents' : 'Perluas percabangan dan exception flow dari Dokumen Acuan Proyek'"
                                >
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span x-text="locale === 'en' ? 'ELABORATE WORKFLOW FROM GROUNDED CORPUS' : 'PERDALAM ALUR KERJA DARI DOKUMEN ACUAN'">PERDALAM ALUR KERJA DARI DOKUMEN ACUAN</span>
                                </button>
                            @endif

                            <button 
                                type="button" 
                                @click.stop="toggleSection('section-4')" 
                                class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                                :title="sectionCollapses['section-4'] ? (locale === 'en' ? 'Expand this section' : 'Buka seksi ini') : (locale === 'en' ? 'Collapse this section' : 'Ciutkan seksi ini')"
                            >
                                <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-4'] ? (locale === 'en' ? 'EXPAND' : 'BUKA') : (locale === 'en' ? 'COLLAPSE' : 'CIUTKAN')"></span>
                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-4'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>
                    @if($isFlowImpacted)
                    <div class="flex items-start gap-2 p-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 text-xs font-mono text-amber-700 dark:text-amber-400 mt-1">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p x-html="locale === 'en' ? '<strong>NEEDS REVIEW:</strong> User workflows are detected as <em>stale</em> due to upstream changes in Actors or Features. Click TWEAK to adjust flowchart steps and actors.' : '<strong>NEEDS REVIEW:</strong> Alur kerja sistem terdeteksi <em>stale</em> karena perubahan di segmen hulu (Aktor atau Fitur). Klik TWEAK untuk menyesuaikan tahapan alur kerja dan aktor.'"></p>
                    </div>
                    @endif
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-4']" @click="toggleSection('section-4')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-show="locale === 'en'">Section 06 collapsed (Core System Workflows / User Journey). Click to expand...</span>
                        <span x-show="locale !== 'en'">Seksi 06 diciutkan (Alur Kerja Utama / User Flow). Klik untuk membuka...</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold" x-text="locale === 'en' ? '[ + EXPAND ]' : '[ + BUKA ]'">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-4']" x-transition class="accordion-body">

                <!-- 1. Interactive Visual Flowchart (Pipeline Nodes & Connectors) -->
                <div x-show="flowTab === 'visual'" class="mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none relative overflow-hidden">
                        <div class="absolute top-0 right-0 px-3 py-1 bg-emerald-500/10 border-b border-l border-emerald-500/30 text-[10px] font-mono text-emerald-700 dark:text-emerald-400 uppercase tracking-widest font-bold">
                            INTERACTIVE PROCESS PIPELINE
                        </div>

                        <!-- Horizontal Scroll Pipeline Container -->
                        <div class="overflow-x-auto pb-4 pt-2">
                            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 min-w-0 lg:min-w-[700px] w-full">
                                @foreach($rawWorkflows as $index => $flow)
                                    <!-- Node Card -->
                                    <div class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 p-4 relative group hover:border-emerald-500 transition shadow-sm dark:shadow-lg flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-2 font-mono">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-pulse"></span>
                                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">STEP 0{{ $flow['step'] ?? ($index + 1) }}</span>
                                                </div>
                                                <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30 font-bold">
                                                    {{ $flow['badge'] ?? 'PROCESS' }}
                                                </span>
                                            </div>

                                            <h4 class="font-mono font-bold text-xs uppercase text-zinc-900 dark:text-zinc-100 mb-2 leading-snug">
                                                {{ $flow['action'] ?? '-' }}
                                            </h4>

                                            <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-3">
                                                {{ $flow['description'] ?? 'Tahapan validasi dan transmisi alur kerja.' }}
                                            </p>
                                        </div>

                                        <div class="space-y-1.5 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono">
                                            <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                                <span class="text-zinc-500 dark:text-zinc-400" x-text="locale === 'en' ? 'ACTOR:' : 'AKTOR:'">AKTOR:</span>
                                                <span class="text-emerald-700 dark:text-emerald-400 font-bold truncate max-w-[140px]">{{ $flow['actor'] ?? 'Pengguna' }}</span>
                                            </div>
                                            @if(!empty($flow['trigger']))
                                                <div class="text-[10px] text-zinc-600 dark:text-zinc-400">
                                                    <span class="text-zinc-500 dark:text-zinc-400 block" x-text="locale === 'en' ? 'TRIGGER:' : 'PEMICU:'">TRIGGER:</span>
                                                    <span class="text-zinc-800 dark:text-zinc-300 font-sans text-[11px]">{{ $flow['trigger'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!$loop->last)
                                        <!-- Flow Connector Arrow -->
                                        <div class="flex items-center justify-center text-emerald-500 px-1 py-1">
                                            <div class="hidden lg:flex items-center gap-0.5">
                                                <div class="w-4 h-0.5 bg-emerald-500/60"></div>
                                                <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                            </div>
                                            <div class="flex lg:hidden items-center justify-center my-1">
                                                <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Mermaid Flowchart View (Code & Native Mermaid SVG) -->
                <div x-show="flowTab === 'mermaid'" x-cloak class="mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase">
                                    <span x-show="locale === 'en'">MERMAID WORKFLOW (FLOWCHART TD)</span>
                                    <span x-show="locale !== 'en'">ALUR KERJA MERMAID (FLOWCHART TD)</span>
                                </span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline" x-text="locale === 'en' ? '(Drag to Pan • Scroll to Zoom)' : '(Drag untuk Pan • Scroll untuk Zoom)'">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-flow-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" :title="locale === 'en' ? 'Zoom Out (-)' : 'Perkecil Zoom (-)'">-</button>
                                <button type="button" @click="resetDiagram('mermaid-flow-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" :title="locale === 'en' ? 'Fit to Screen / Reset Zoom (1:1)' : 'Fit to Screen / Reset Zoom (1:1)'">FIT (1:1)</button>
                                <button type="button" @click="zoomDiagram('mermaid-flow-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" :title="locale === 'en' ? 'Zoom In (+)' : 'Perbesar Zoom (+)'">+</button>
                                <button type="button" @click="openDiagramFullscreen(locale === 'en' ? 'System Workflow Flowchart' : 'Diagram Alur Kerja Sistem (Workflow Flowchart)', 'mermaid-flow-source')" class="px-2.5 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" :title="locale === 'en' ? 'Open Fullscreen' : 'Buka di Layar Penuh'">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span x-text="locale === 'en' ? 'FULLSCREEN' : 'LAYAR PENUH'">LAYAR PENUH</span>
                                </button>
                                <button 
                                    type="button"
                                    onclick="window.copyMermaidCode('mermaid-flow-source', this)"
                                    class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span x-text="locale === 'en' ? 'COPY MERMAID CODE' : 'SALIN KODE MERMAID'">SALIN KODE MERMAID</span>
                                </button>
                            </div>
                        </div>

                        <script type="text/plain" id="mermaid-flow-source">{!! \App\Services\PrdGeneratorService::generateWorkflowMermaid($rawWorkflows) !!}</script>

                        <div 
                            class="overflow-hidden h-[460px] sm:h-[520px] lg:h-[560px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-flow-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-flow-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-flow-target')"
                            @mouseleave="stopDiagramDrag('mermaid-flow-target')"
                            @wheel.prevent="zoomDiagram('mermaid-flow-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-flow-target" class="w-full h-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
                                    <span x-show="locale === 'en'">Loading Mermaid workflow visualization...</span>
                                    <span x-show="locale !== 'en'">Memuat visualisasi alur kerja Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Steps Grid -->
                <div x-show="flowTab === 'detailed'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($rawWorkflows as $flow)
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-7 h-7 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono font-bold flex items-center justify-center rounded-none">
                                        0{{ $flow['step'] ?? $loop->iteration }}
                                    </div>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase">
                                        {{ $flow['badge'] ?? 'STEP' }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm mb-2 font-mono uppercase">{{ $flow['action'] ?? '-' }}</h4>
                                <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-sans mb-4">{{ $flow['description'] ?? '' }}</p>

                                <div class="space-y-2.5 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-[11px]">
                                    <div>
                                        <span class="font-mono text-[10px] uppercase text-zinc-400 font-bold block">
                                            <span x-show="locale === 'en'">Trigger &amp; Event:</span>
                                            <span x-show="locale !== 'en'">Pemicu &amp; Trigger:</span>
                                        </span>
                                        <span class="text-zinc-700 dark:text-zinc-300 font-sans">{{ $flow['trigger'] ?? 'Aksi langsung pengguna pada antarmuka.' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-mono text-[10px] uppercase text-zinc-400 font-bold block">
                                            <span x-show="locale === 'en'">Computation &amp; Database:</span>
                                            <span x-show="locale !== 'en'">Komputasi &amp; Basis Data:</span>
                                        </span>
                                        <span class="text-zinc-700 dark:text-zinc-300 font-sans">{{ $flow['system_process'] ?? 'Validasi request FormRequest & query database terindeks.' }}</span>
                                    </div>
                                    @if(!empty($flow['output_state']))
                                        <div>
                                            <span class="font-mono text-[10px] uppercase text-zinc-400 font-bold block">
                                                <span x-show="locale === 'en'">Output &amp; State Response:</span>
                                                <span x-show="locale !== 'en'">Hasil &amp; State Response:</span>
                                            </span>
                                            <span class="text-zinc-700 dark:text-zinc-300 font-sans">{{ $flow['output_state'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($flow['edge_case']))
                                        <div class="bg-amber-500/5 border border-amber-500/20 p-2 text-[10px]">
                                            <span class="font-mono uppercase text-amber-600 dark:text-amber-400 font-bold block">
                                                <span x-show="locale === 'en'">Mitigation &amp; Edge Case:</span>
                                                <span x-show="locale !== 'en'">Mitigasi &amp; Edge Case:</span>
                                            </span>
                                            <span class="text-zinc-600 dark:text-zinc-300 font-sans">{{ $flow['edge_case'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono flex items-center justify-between">
                                <span class="text-zinc-500 uppercase" x-text="locale === 'en' ? 'PRIMARY ACTOR:' : 'AKTOR UTAMA:'">AKTOR UTAMA:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $flow['actor'] ?? 'Pengguna' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </section>

            <!-- SECTION 5: ENTERPRISE DATABASE ERD & SCHEMA BLUEPRINT -->
            @php $isErdImpacted = in_array('database_erd', $prd['_impacted_segments'] ?? []); @endphp
