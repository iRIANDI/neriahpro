    <!-- Centralized Interactive Fullscreen Diagram Modal -->
    <div 
        x-show="fullscreenDiagramModal" 
        x-cloak 
        style="display: none;"
        @keydown.escape.window="closeDiagramFullscreen()"
        class="fixed inset-0 z-[120] flex flex-col bg-zinc-100/98 dark:bg-zinc-950/98 backdrop-blur-md p-2 sm:p-4 print:hidden select-none transition-colors duration-200"
    >
        <!-- Fullscreen Header / Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-2 p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 shrink-0 font-mono text-xs shadow-xs rounded-none">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none inline-block animate-pulse"></span>
                <span class="font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wide" x-text="fullscreenDiagramTitle || 'DIAGRAM FULLSCREEN VIEWER'"></span>
                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 hidden xl:inline">&bull; Drag mouse untuk menggeser (Pan) &bull; Scroll roda mouse untuk Zoom (1% s/d 2500%) &bull; Dobel klik untuk Fit</span>
            </div>
            
            <div class="flex items-center gap-1.5 flex-wrap">
                <!-- Zoom Presets & Steppers -->
                <button type="button" @click="fitFullscreenDiagram()" class="px-2.5 py-1.5 bg-zinc-200 hover:bg-zinc-300 text-zinc-900 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 text-[10px] font-black uppercase transition rounded-none cursor-pointer border border-zinc-300 dark:border-zinc-700" title="Sesuaikan Diagram ke Layar (Auto-Fit)">
                    ⛶ FIT LAYAR
                </button>
                <button type="button" @click="zoomFullscreenDiagram(-1)" class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 text-zinc-900 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white font-bold transition rounded-none cursor-pointer border border-zinc-300 dark:border-zinc-700" title="Zoom Out (-)">
                    -
                </button>
                
                <!-- Zoom Percentage Indicator & Presets Dropdown -->
                <div class="relative" x-data="{ presetOpen: false }">
                    <button type="button" @click="presetOpen = !presetOpen" class="px-2.5 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-emerald-800 dark:text-emerald-400 text-[11px] font-black tabular-nums cursor-pointer flex items-center gap-1">
                        <span x-text="Math.round(fullscreenDiagramZoom * 100) + '%'">100%</span>
                        <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <!-- Quick Zoom Dropdown -->
                    <div x-show="presetOpen" @click.outside="presetOpen = false" x-cloak class="absolute top-full mt-1 right-0 w-36 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 shadow-xl z-30 py-1 font-mono text-[10px]">
                        <button type="button" @click="setFullscreenZoom(0.01); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">1% (Mikro)</button>
                        <button type="button" @click="setFullscreenZoom(0.05); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">5% (Mini)</button>
                        <button type="button" @click="setFullscreenZoom(0.15); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">15%</button>
                        <button type="button" @click="setFullscreenZoom(0.35); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">35%</button>
                        <button type="button" @click="setFullscreenZoom(0.50); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">50%</button>
                        <button type="button" @click="setFullscreenZoom(0.75); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">75%</button>
                        <button type="button" @click="setFullscreenZoom(1.00); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold text-emerald-600 dark:text-emerald-400">100% (1:1 Standar)</button>
                        <button type="button" @click="setFullscreenZoom(1.50); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">150%</button>
                        <button type="button" @click="setFullscreenZoom(2.00); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">200%</button>
                        <button type="button" @click="setFullscreenZoom(4.00); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">400%</button>
                        <button type="button" @click="setFullscreenZoom(8.00); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">800%</button>
                        <button type="button" @click="setFullscreenZoom(15.00); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">1500%</button>
                        <button type="button" @click="setFullscreenZoom(25.00); presetOpen = false" class="w-full text-left px-3 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200">2500% (Ultra)</button>
                    </div>
                </div>

                <button type="button" @click="zoomFullscreenDiagram(1)" class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 text-zinc-900 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white font-bold transition rounded-none cursor-pointer border border-zinc-300 dark:border-zinc-700" title="Zoom In (+)">
                    +
                </button>
                <button type="button" @click="resetFullscreenDiagram()" class="px-2.5 py-1.5 bg-zinc-200 hover:bg-zinc-300 text-zinc-900 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 text-[10px] font-bold transition rounded-none cursor-pointer border border-zinc-300 dark:border-zinc-700" title="Reset ke 100% (1:1)">
                    1:1
                </button>

                <!-- Copy Mermaid Button -->
                <button type="button" @click="window.copyMermaidCode(fullscreenDiagramSourceId, this)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[10px] uppercase transition rounded-none cursor-pointer flex items-center gap-1 border border-emerald-600 shadow-xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">SALIN MERMAID</span>
                </button>

                <!-- Close Button -->
                <button type="button" @click="closeDiagramFullscreen()" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-black uppercase transition rounded-none cursor-pointer ml-1 shadow-xs">
                    &times; TUTUP (ESC)
                </button>
            </div>
        </div>

        <!-- Fullscreen Canvas Area with Dot-Grid Texture -->
        <div 
            id="mermaid-fullscreen-canvas"
            class="flex-1 w-full h-full overflow-hidden bg-zinc-50 dark:bg-zinc-950 border-x border-b border-zinc-300 dark:border-zinc-800 flex items-center justify-center p-4 relative cursor-grab active:cursor-grabbing select-none"
            :style="document.documentElement.classList.contains('dark') ? 'background-image: radial-gradient(#27272a 1px, transparent 1px); background-size: 24px 24px;' : 'background-image: radial-gradient(#d4d4d8 1px, transparent 1px); background-size: 24px 24px;'"
            @wheel.prevent="onFullscreenWheel($event)"
            @mousedown="startFullscreenDrag($event)"
            @mousemove="onFullscreenDrag($event)"
            @mouseup="stopFullscreenDrag()"
            @mouseleave="stopFullscreenDrag()"
            @dblclick="fitFullscreenDiagram()"
        >
            <div 
                id="mermaid-fullscreen-target" 
                :style="`transform: translate(${fullscreenDiagramPanX}px, ${fullscreenDiagramPanY}px) scale(${fullscreenDiagramZoom}); transform-origin: center center; transition: ${fullscreenDiagramDragging ? 'none' : 'transform 0.08s ease-out'};`"
                class="w-auto h-auto flex items-center justify-center text-center text-zinc-900 dark:text-zinc-100 pointer-events-auto"
            >
            </div>
        </div>
    </div>
