            <section id="section-3-8" class="bg-white dark:bg-zinc-900 border-2 border-sky-500/50 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-sky-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">05</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Virtual Architecture Studio (Visual Chart Center)</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Visualisasi komprehensif diagram alur, topologi basis data, peta dependensi, dan timeline sprint standar AI Agent.</p>
                        </div>
                    </div>
                    
                    <!-- 4 Virtual Charts Switcher -->
                    <div class="flex flex-wrap items-center gap-1 font-mono text-xs no-print">
                        <button 
                            type="button"
                            @click="chartStudioTab = 'workflow'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-flow-target', 'mermaid-studio-flow-source'))" 
                            :class="chartStudioTab === 'workflow' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>1. WORKFLOW (ALUR)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'erd'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-erd-target', 'mermaid-studio-erd-source'))" 
                            :class="chartStudioTab === 'erd' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>2. ERD (DATABASE)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'feature_dep'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-featdep-target', 'mermaid-studio-featdep-source'))" 
                            :class="chartStudioTab === 'feature_dep' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>3. DEPENDENCY (FITUR)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'gantt'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-gantt-target', 'mermaid-studio-gantt-source'))" 
                            :class="chartStudioTab === 'gantt' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>4. GANTT (ROADMAP)</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'infra'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-infra-target', 'mermaid-studio-infra-source'))" 
                            :class="chartStudioTab === 'infra' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>5. INFRASTRUKTUR</span>
                        </button>
                        <button 
                            type="button"
                            @click="chartStudioTab = 'mobile_sync'; $nextTick(() => window.renderMermaidDiagram('mermaid-studio-mobilesync-target', 'mermaid-studio-mobilesync-source'))" 
                            :class="chartStudioTab === 'mobile_sync' ? 'bg-sky-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <span>6. MOBILE &amp; SYNC</span>
                        </button>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-3-8')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                            :title="sectionCollapses['section-3-8'] ? 'Buka seksi ini' : 'Ciutkan seksi ini'"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-3-8'] ? 'BUKA' : 'CIUTKAN'"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-3-8'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-3-8']" @click="toggleSection('section-3-8')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-sky-600 dark:hover:text-sky-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Seksi 05 diciutkan (Virtual Architecture Studio - 6 Diagram Visual). Klik untuk membuka...</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-3-8']" x-transition class="accordion-body">

                <!-- Chart 1: Workflow State Machine -->
                <div x-show="chartStudioTab === 'workflow'" class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-sky-600 dark:text-sky-400 font-bold uppercase">DIAGRAM 1: ALUR KERJA SISTEM (WORKFLOW STATE MACHINE)</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-studio-flow-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-studio-flow-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-studio-flow-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Diagram 1: Alur Kerja Sistem (Workflow)', 'mermaid-studio-flow-source')" class="px-2.5 py-0.5 bg-sky-600 hover:bg-sky-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-studio-flow-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-sky-500 hover:text-white dark:hover:bg-sky-500 dark:hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE</span>
                                </button>
                            </div>
                        </div>
                        <script type="text/plain" id="mermaid-studio-flow-source">{!! \App\Services\PrdGeneratorService::resolveWorkflowMermaid($prd['virtual_charts']['workflow_mermaid'] ?? null, $prd['workflow'] ?? []) !!}</script>
                        <div 
                            class="overflow-hidden min-h-[220px] max-h-[550px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-studio-flow-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-studio-flow-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-studio-flow-target')"
                            @mouseleave="stopDiagramDrag('mermaid-studio-flow-target')"
                            @wheel.prevent="zoomDiagram('mermaid-studio-flow-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-studio-flow-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-sky-500 animate-ping"></span>
                                    <span>Memuat visualisasi alur kerja Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Database ERD Topology -->
                <div x-show="chartStudioTab === 'erd'" x-cloak class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase">DIAGRAM 2: SKEMA BASIS DATA RELASIONAL (POSTGRESQL STRICT ULID)</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-studio-erd-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-studio-erd-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-studio-erd-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Diagram 2: Skema Basis Data Relasional (ERD)', 'mermaid-studio-erd-source')" class="px-2.5 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-studio-erd-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE</span>
                                </button>
                            </div>
                        </div>
                        <script type="text/plain" id="mermaid-studio-erd-source">{!! !empty($prd['virtual_charts']['erd_mermaid']) ? $prd['virtual_charts']['erd_mermaid'] : \App\Services\PrdGeneratorService::generateErdMermaid($prd['erd_schema']['tables'] ?? []) !!}</script>
                        <div 
                            class="overflow-hidden min-h-[220px] max-h-[550px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-studio-erd-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-studio-erd-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-studio-erd-target')"
                            @mouseleave="stopDiagramDrag('mermaid-studio-erd-target')"
                            @wheel.prevent="zoomDiagram('mermaid-studio-erd-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-studio-erd-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
                                    <span>Memuat topologi ERD Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 3: Feature & Entity Dependency Graph -->
                <div x-show="chartStudioTab === 'feature_dep'" x-cloak class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-amber-600 dark:text-amber-400 font-bold uppercase">DIAGRAM 3: PETA KETERGANTUNGAN (AKTOR &rarr; FITUR &rarr; ENTITAS BASIS DATA)</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-studio-featdep-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-studio-featdep-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-studio-featdep-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Diagram 3: Peta Ketergantungan Fitur', 'mermaid-studio-featdep-source')" class="px-2.5 py-0.5 bg-amber-600 hover:bg-amber-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-studio-featdep-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 dark:hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE</span>
                                </button>
                            </div>
                        </div>
                        <script type="text/plain" id="mermaid-studio-featdep-source">{!! !empty($prd['virtual_charts']['feature_dependency_mermaid']) ? $prd['virtual_charts']['feature_dependency_mermaid'] : (!empty($prd['virtual_charts']['feature_dep_mermaid']) ? $prd['virtual_charts']['feature_dep_mermaid'] : \App\Services\PrdGeneratorService::generateFeatureDependencyMermaid($prd['target_actors']['items'] ?? [], $prd['engineering_specs']['mvp_phase_1']['items'] ?? [], $prd['erd_schema']['tables'] ?? [])) !!}</script>
                        <div 
                            class="overflow-hidden min-h-[220px] max-h-[550px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-studio-featdep-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-studio-featdep-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-studio-featdep-target')"
                            @mouseleave="stopDiagramDrag('mermaid-studio-featdep-target')"
                            @wheel.prevent="zoomDiagram('mermaid-studio-featdep-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-studio-featdep-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-amber-500 animate-ping"></span>
                                    <span>Memuat peta ketergantungan fitur Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 4: Sprint Delivery Roadmap (Gantt Timeline) -->
                <div x-show="chartStudioTab === 'gantt'" x-cloak class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-rose-600 dark:text-rose-400 font-bold uppercase">DIAGRAM 4: ROADMAP EKSEKUSI &amp; TIMELINE SPRINT (GANTT CHART)</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-studio-gantt-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-studio-gantt-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-studio-gantt-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Diagram 4: Timeline Roadmap & Gantt Chart', 'mermaid-studio-gantt-source')" class="px-2.5 py-0.5 bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-studio-gantt-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-500 dark:hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE</span>
                                </button>
                            </div>
                        </div>
                        <script type="text/plain" id="mermaid-studio-gantt-source">{!! !empty($prd['virtual_charts']['sprint_gantt_mermaid']) ? $prd['virtual_charts']['sprint_gantt_mermaid'] : (!empty($prd['virtual_charts']['gantt_mermaid']) ? $prd['virtual_charts']['gantt_mermaid'] : \App\Services\PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja')) !!}</script>
                        <div 
                            class="overflow-hidden min-h-[220px] max-h-[550px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-studio-gantt-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-studio-gantt-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-studio-gantt-target')"
                            @mouseleave="stopDiagramDrag('mermaid-studio-gantt-target')"
                            @wheel.prevent="zoomDiagram('mermaid-studio-gantt-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-studio-gantt-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-rose-500 animate-ping"></span>
                                    <span>Memuat timeline roadmap Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 5: Infrastructure, Security & Hosting Topology -->
                <div x-show="chartStudioTab === 'infra'" x-cloak class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-violet-600 dark:text-violet-400 font-bold uppercase">DIAGRAM 5: TOPOLOGI INFRASTRUKTUR, KEAMANAN &amp; HOSTING</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-studio-infra-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-studio-infra-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-studio-infra-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Diagram 5: Topologi Infrastruktur & Keamanan', 'mermaid-studio-infra-source')" class="px-2.5 py-0.5 bg-violet-600 hover:bg-violet-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-studio-infra-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-violet-500 hover:text-white dark:hover:bg-violet-500 dark:hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE</span>
                                </button>
                            </div>
                        </div>
                        <script type="text/plain" id="mermaid-studio-infra-source">{!! !empty($prd['virtual_charts']['infrastructure_mermaid']) ? $prd['virtual_charts']['infrastructure_mermaid'] : (!empty($prd['virtual_charts']['infra_mermaid']) ? $prd['virtual_charts']['infra_mermaid'] : \App\Services\PrdGeneratorService::generateInfrastructureMermaid($blueprint, $blueprint->user_metadata ?? [])) !!}</script>
                        <div 
                            class="overflow-hidden min-h-[220px] max-h-[550px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-studio-infra-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-studio-infra-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-studio-infra-target')"
                            @mouseleave="stopDiagramDrag('mermaid-studio-infra-target')"
                            @wheel.prevent="zoomDiagram('mermaid-studio-infra-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-studio-infra-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-violet-500 animate-ping"></span>
                                    <span>Memuat topologi infrastruktur Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 6: Mobile App & Local SQLite Sync Sequence -->
                <div x-show="chartStudioTab === 'mobile_sync'" x-cloak class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase">DIAGRAM 6: ALUR SINKRONISASI MOBILE &amp; LOCAL SQLITE (OFFLINE-FIRST)</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-studio-mobilesync-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-studio-mobilesync-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-studio-mobilesync-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Diagram 6: Sinkronisasi Mobile & Local SQLite', 'mermaid-studio-mobilesync-source')" class="px-2.5 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-studio-mobilesync-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE</span>
                                </button>
                            </div>
                        </div>
                        <script type="text/plain" id="mermaid-studio-mobilesync-source">{!! !empty($prd['virtual_charts']['mobile_sync_mermaid']) ? $prd['virtual_charts']['mobile_sync_mermaid'] : \App\Services\PrdGeneratorService::generateMobileSyncMermaid($blueprint->nama_bisnis) !!}</script>
                        <div 
                            class="overflow-hidden min-h-[220px] max-h-[550px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-studio-mobilesync-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-studio-mobilesync-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-studio-mobilesync-target')"
                            @mouseleave="stopDiagramDrag('mermaid-studio-mobilesync-target')"
                            @wheel.prevent="zoomDiagram('mermaid-studio-mobilesync-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-studio-mobilesync-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
                                    <span>Memuat topologi sinkronisasi Mobile &amp; SQLite Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </section>

            <!-- SECTION 4: ALUR KERJA (USER FLOW) -->
            @php
                $rawWorkflows = $prd['workflow'] ?? [];

                // Check if any workflow item (especially step 0) contains embedded numbered list (e.g. 1. ... 2. ... 3. ...)
                if (!empty($rawWorkflows) && isset($rawWorkflows[0]['action']) && preg_match('/\b(?:2|3)[\.\)]\s+/', $rawWorkflows[0]['action'])) {
                    $numberedParts = preg_split('/(?:\r?\n|\s+)(?=\d+[\.\)]\s+)/', $rawWorkflows[0]['action']);
                    if (count($numberedParts) > 1) {
                        $rebuilt = [];
                        $sIdx = 1;
                        foreach ($numberedParts as $np) {
                            $cleaned = preg_replace('/^\d+[\.\)]\s*/', '', trim($np));
                            if (strlen($cleaned) > 5) {
                                $lowerC = strtolower($cleaned);
                                $actor = 'Pengguna / Wisatawan';
                                $badge = 'DISCOVERY';
                                if (str_contains($lowerC, 'ai') || str_contains($lowerC, 'analisis') || str_contains($lowerC, 'itinerary') || str_contains($lowerC, 'rekomendasi')) {
                                    $actor = 'AI Recommendation Engine';
                                    $badge = 'AI_ENGINE';
                                } elseif (str_contains($lowerC, 'pesan') || str_contains($lowerC, 'pemesanan') || str_contains($lowerC, 'invoice') || str_contains($lowerC, 'voucher')) {
                                    $actor = 'Booking & Payment Gateway';
                                    $badge = 'BOOKING';
                                } elseif (str_contains($lowerC, 'admin') || str_contains($lowerC, 'dispatcher') || str_contains($lowerC, 'verifikasi')) {
                                    $actor = 'Admin / Dispatcher';
                                    $badge = 'DISPATCH';
                                } elseif (str_contains($lowerC, 'supir') || str_contains($lowerC, 'driver') || str_contains($lowerC, 'jemput') || str_contains($lowerC, 'perjalanan')) {
                                    $actor = 'Supir / Mitra Lapangan';
                                    $badge = 'EXECUTION';
                                } elseif (str_contains($lowerC, 'batal') || str_contains($lowerC, 'pembatalan') || str_contains($lowerC, 'refund') || str_contains($lowerC, 'kembali')) {
                                    $actor = 'Refund & Reconciliation Engine';
                                    $badge = 'SETTLEMENT';
                                }

                                $rebuilt[] = [
                                    'step' => $sIdx++,
                                    'action' => $cleaned,
                                    'description' => 'Tahapan alur interaksi dan validasi data sistem.',
                                    'actor' => $actor,
                                    'badge' => $badge,
                                    'computation' => 'Validasi integritas request FormRequest dan state machine transisi data.',
                                    'response' => 'Status terverifikasi dengan respon HTTP 200 OK.',
                                    'mitigation' => 'Penanganan error inline dan rollback transaksi database.',
                                ];
                            }
                        }
                        if (count($rawWorkflows) > 1) {
                            for ($k = 1; $k < count($rawWorkflows); $k++) {
                                $otherStep = $rawWorkflows[$k];
                                $otherStep['step'] = $sIdx++;
                                $rebuilt[] = $otherStep;
                            }
                        }
                        $rawWorkflows = $rebuilt;
                    }
                } elseif (count($rawWorkflows) === 1 && strlen($rawWorkflows[0]['action'] ?? '') > 35) {
                    $singleText = $rawWorkflows[0]['action'];
                    $parts = preg_split('/,\s*(?:lalu\s+|kemudian\s+|setelah itu\s+|selanjutnya\s+)?|\s+(?:lalu|kemudian|setelah itu|selanjutnya)\s+/i', $singleText);
                    $expanded = [];
                    $sIdx = 1;
                    foreach ($parts as $p) {
                        $p = trim($p, " \t\n\r\0\x0B-•*1234567890.),");
                        if (strlen($p) > 3) {
                            $lowerP = strtolower($p);
                            $actor = 'Pengguna / Pengunjung';
                            $badge = 'DISCOVERY';
                            if (str_contains($lowerP, 'filter') || str_contains($lowerP, 'cari') || str_contains($lowerP, 'search')) {
                                $actor = 'Sistem / Search Engine';
                                $badge = 'QUERY_FILTER';
                            } elseif (str_contains($lowerP, 'notifikasi') || str_contains($lowerP, 'langganan') || str_contains($lowerP, 'berlangganan')) {
                                $actor = 'Notification Engine';
                                $badge = 'NOTIFICATION';
                            } elseif (str_contains($lowerP, 'katalog') || str_contains($lowerP, 'daftar')) {
                                $actor = 'Katalog Properti';
                                $badge = 'CATALOG';
                            }
                            $expanded[] = [
                                'step' => $sIdx++,
                                'action' => $p,
                                'description' => 'Tahapan validasi, interaksi antarmuka, dan transmisi data alur kerja.',
                                'actor' => $actor,
                                'badge' => $badge,
                            ];
                        }
                    }
                    if (count($expanded) > 1) {
                        $rawWorkflows = $expanded;
                    }
                }
            @endphp
            @php $isFlowImpacted = in_array('alurs_kerja', $prd['_impacted_segments'] ?? []); @endphp
