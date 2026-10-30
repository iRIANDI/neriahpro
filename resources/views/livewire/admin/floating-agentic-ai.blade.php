<div class="font-sans"
     x-data="{
         isDragging: false,
         startX: 0,
         startY: 0,
         offsetX: 0,
         offsetY: 0,
         scrollToBottom() {
             this.$nextTick(() => {
                 const box = document.getElementById('ai-chat-messages-box');
                 if (box) {
                     box.scrollTop = box.scrollHeight;
                 }
             });
         },
         initDrag(e) {
             if (e.target.closest('button') || e.target.closest('input') || e.target.closest('select') || e.target.closest('textarea') || e.target.closest('a')) return;
             this.isDragging = true;
             this.startX = e.clientX - (Number(this.offsetX) || 0);
             this.startY = e.clientY - (Number(this.offsetY) || 0);
             const moveHandler = (ev) => {
                 if (!this.isDragging) return;
                 this.offsetX = ev.clientX - this.startX;
                 this.offsetY = ev.clientY - this.startY;
             };
             const upHandler = () => {
                 this.isDragging = false;
                 window.removeEventListener('mousemove', moveHandler);
                 window.removeEventListener('mouseup', upHandler);
             };
             window.addEventListener('mousemove', moveHandler);
             window.addEventListener('mouseup', upHandler);
         },
         resetPos() {
             this.offsetX = 0;
             this.offsetY = 0;
         }
     }"
     x-on:toggle-ai-copilot.window="resetPos(); scrollToBottom()"
     x-on:open-ai-copilot.window="resetPos(); scrollToBottom()"
     x-init="$watch('$wire.messages', () => scrollToBottom())">

    <style>
        #neriahpro-floating-ai-window {
            position: fixed !important;
            z-index: 99999 !important;
            bottom: 24px !important;
            right: 24px !important;
            background-color: #ffffff !important;
            color: #09090b !important;
            display: flex !important;
            flex-direction: column !important;
            border: 1px solid #d4d4d8 !important;
            border-radius: 4px !important;
            overflow: hidden !important;
            box-shadow: 0 25px 60px -15px rgba(0,0,0,0.4), 0 0 0 1px rgba(0,0,0,0.08) !important;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            pointer-events: auto !important;
        }

        .dark #neriahpro-floating-ai-window,
        [data-theme="dark"] #neriahpro-floating-ai-window {
            background-color: #09090b !important;
            color: #f4f4f5 !important;
            border-color: #27272a !important;
            box-shadow: 0 25px 60px -15px rgba(0,0,0,0.95), 0 0 0 1px rgba(63, 63, 70, 0.8) !important;
        }

        #neriahpro-floating-ai-window.is-normal {
            width: 520px !important;
            max-width: calc(100vw - 2rem) !important;
            height: 700px !important;
            max-height: calc(100vh - 3rem) !important;
        }

        #neriahpro-floating-ai-window.is-maximized {
            top: 12px !important;
            bottom: 12px !important;
            left: 12px !important;
            right: 12px !important;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
        }

        #neriahpro-floating-ai-window.is-minimized {
            width: 340px !important;
            height: auto !important;
        }

        @media (max-width: 640px) {
            #neriahpro-floating-ai-window.is-normal {
                left: 12px !important;
                right: 12px !important;
                bottom: 12px !important;
                top: 12px !important;
                width: auto !important;
                height: auto !important;
                max-width: none !important;
                max-height: none !important;
            }
        }

        /* Scoped Internal Modal Classes to guarantee perfect styling even without external Tailwind */
        .ai-header-drag {
            padding: 12px 16px !important;
            background: #f4f4f5 !important;
            border-bottom: 1px solid #e4e4e7 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-shrink: 0 !important;
            user-select: none !important;
            cursor: move !important;
        }
        .dark .ai-header-drag {
            background: #18181b !important;
            border-color: #27272a !important;
        }

        .ai-win-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 28px !important;
            height: 28px !important;
            padding: 4px !important;
            border-radius: 2px !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            color: #71717a !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }
        .ai-win-btn:hover {
            background: #e4e4e7 !important;
            color: #09090b !important;
            border-color: #d4d4d8 !important;
        }
        .dark .ai-win-btn:hover {
            background: #27272a !important;
            color: #ffffff !important;
            border-color: #3f3f46 !important;
        }

        .ai-selector-strip {
            padding: 8px 12px !important;
            background: #fafafa !important;
            border-bottom: 1px solid #e4e4e7 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
            font-family: ui-monospace, monospace !important;
            font-size: 11px !important;
            flex-shrink: 0 !important;
        }
        .dark .ai-selector-strip {
            background: #121215 !important;
            border-color: #27272a !important;
        }
        .ai-select-input {
            font-family: ui-monospace, monospace !important;
            font-size: 11px !important;
            padding: 3px 8px !important;
            background: #ffffff !important;
            color: #09090b !important;
            border: 1px solid #d4d4d8 !important;
            border-radius: 2px !important;
            outline: none !important;
        }
        .dark .ai-select-input {
            background: #09090b !important;
            color: #f4f4f5 !important;
            border-color: #3f3f46 !important;
        }

        .ai-tabs-strip {
            display: flex !important;
            border-bottom: 1px solid #e4e4e7 !important;
            background: #f4f4f5 !important;
            font-family: ui-monospace, monospace !important;
            font-size: 11px !important;
            flex-shrink: 0 !important;
        }
        .dark .ai-tabs-strip {
            background: #18181b !important;
            border-color: #27272a !important;
        }
        .ai-tab-action {
            flex: 1 !important;
            padding: 8px 12px !important;
            text-align: center !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            color: #71717a !important;
            border-bottom: 2px solid transparent !important;
            background: transparent !important;
            transition: all 0.15s ease !important;
        }
        .ai-tab-action.is-active {
            border-bottom-color: #09090b !important;
            color: #09090b !important;
            background: #ffffff !important;
        }
        .dark .ai-tab-action.is-active {
            border-bottom-color: #10b981 !important;
            color: #ffffff !important;
            background: #09090b !important;
        }

        .ai-messages-scroll {
            flex: 1 1 0% !important;
            padding: 16px !important;
            overflow-y: auto !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
            background: #ffffff !important;
        }
        .dark .ai-messages-scroll {
            background: #09090b !important;
        }
        .ai-bubble-box {
            max-width: 88% !important;
            padding: 12px 14px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            line-height: 1.6 !important;
            word-break: break-word !important;
            white-space: pre-wrap !important;
        }
        .ai-bubble-box.user {
            background: #09090b !important;
            color: #ffffff !important;
            border: 1px solid #27272a !important;
            align-self: flex-end !important;
        }
        .dark .ai-bubble-box.user {
            background: #f4f4f5 !important;
            color: #09090b !important;
            border: 1px solid #e4e4e7 !important;
        }
        .ai-bubble-box.ai {
            background: #f4f4f5 !important;
            color: #09090b !important;
            border: 1px solid #e4e4e7 !important;
            align-self: flex-start !important;
        }
        .dark .ai-bubble-box.ai {
            background: #18181b !important;
            color: #f4f4f5 !important;
            border: 1px solid #27272a !important;
        }
        .ai-input-dock {
            padding: 12px 16px !important;
            background: #fafafa !important;
            border-top: 1px solid #e4e4e7 !important;
            flex-shrink: 0 !important;
        }
        .dark .ai-input-dock {
            background: #121215 !important;
            border-color: #27272a !important;
        }
        .ai-textarea {
            width: 100% !important;
            padding: 8px 12px !important;
            background: #ffffff !important;
            color: #09090b !important;
            border: 1px solid #d4d4d8 !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            resize: none !important;
            outline: none !important;
        }
        .dark .ai-textarea {
            background: #09090b !important;
            color: #f4f4f5 !important;
            border-color: #27272a !important;
        }
        .ai-send-btn {
            padding: 8px 18px !important;
            background: #09090b !important;
            color: #ffffff !important;
            font-family: ui-monospace, monospace !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            border-radius: 4px !important;
            border: 1px solid #27272a !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.15s ease !important;
        }
        .ai-send-btn:hover {
            background: #27272a !important;
        }
        .dark .ai-send-btn {
            background: #f4f4f5 !important;
            color: #09090b !important;
            border-color: #e4e4e7 !important;
        }
        .dark .ai-send-btn:hover {
            background: #e4e4e7 !important;
        }
    </style>

    {{-- ========================================================================= --}}
    {{-- 1. LAUNCHER BUTTON (Saat Window Tertutup)                                  --}}
    {{-- ========================================================================= --}}
    @if($windowState === 'closed')
        <div style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99999 !important; display: flex !important; align-items: center !important; pointer-events: auto !important;"
             class="fixed bottom-6 right-6 z-[99999]">
            <button wire:click="openWindow"
                    @click="resetPos()"
                    type="button"
                    title="Buka Neriah Pro AI Copilot (Pintasan: Ctrl+Shift+A)"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-zinc-950 dark:bg-zinc-900 text-white border border-zinc-700 dark:border-zinc-300 shadow-2xl rounded-xs hover:bg-black dark:hover:bg-zinc-800 hover:border-emerald-500 transition-all duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span class="inline-block w-2.5 h-2.5 bg-emerald-500 animate-pulse rounded-xs"></span>
                <span class="font-mono text-xs font-black tracking-wider uppercase text-white">AI Copilot</span>
                <span class="text-[9px] px-1.5 py-0.5 bg-zinc-800 text-emerald-400 font-mono font-bold border border-zinc-700 rounded-xs uppercase">
                    {{ $activeProvider }}
                </span>
            </button>
        </div>

    {{-- ========================================================================= --}}
    {{-- 2. FLOATING WINDOW (Saat Open, Minimized, atau Maximized)                  --}}
    {{-- ========================================================================= --}}
    @else
        <div id="neriahpro-floating-ai-window"
             :style="`transform: translate(${offsetX}px, ${offsetY}px)`"
             class="bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs flex flex-col overflow-hidden text-zinc-950 dark:text-zinc-100 {{ $windowState === 'maximized' ? 'is-maximized' : ($windowState === 'minimized' ? 'is-minimized' : 'is-normal') }}">

            {{-- Window Header (Draggable) --}}
            <div @mousedown="initDrag($event)"
                 class="ai-header-drag px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 select-none cursor-move">
                
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-xs animate-pulse"></span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-mono text-xs font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                                Neriah Pro AI Copilot
                            </h3>
                            <span class="text-[9px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 font-mono font-bold text-zinc-800 dark:text-zinc-200 rounded-xs border border-zinc-300 dark:border-zinc-700 uppercase">
                                {{ $activeProvider }}
                            </span>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-mono">
                            Multi-Agent • RAG pgvector • Native Tools
                        </p>
                    </div>
                </div>

                {{-- Window Controls --}}
                <div class="flex items-center gap-1">
                    <button type="button"
                            wire:click="createNewChat"
                            title="Buat Sesi Baru"
                            class="ai-win-btn p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button type="button"
                            wire:click="minimizeWindow"
                            title="{{ $windowState === 'minimized' ? 'Buka Penuh' : 'Minimize' }}"
                            class="ai-win-btn p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <button type="button"
                            wire:click="maximizeWindow"
                            title="{{ $windowState === 'maximized' ? 'Restore Ukuran' : 'Maximize Layar Penuh' }}"
                            class="ai-win-btn p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 rounded-xs transition-colors cursor-pointer hidden sm:inline-block">
                        <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </button>
                    <button type="button"
                            wire:click="closeWindow"
                            title="Tutup Window"
                            class="ai-win-btn p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            @if($windowState !== 'minimized')
                {{-- Quick Provider & Sub-Model Selector Bar --}}
                <div class="ai-selector-strip px-3 py-1.5 bg-zinc-50 dark:bg-zinc-900/70 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2 text-xs font-mono shrink-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] text-zinc-500 font-bold uppercase">Provider:</span>
                        <select wire:change="switchProvider($event.target.value)"
                                class="ai-select-input text-[11px] font-mono font-bold bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 px-2 py-0.5 rounded-xs focus:ring-1 focus:ring-emerald-500">
                            @foreach ($availableProviders as $pKey => $pLabel)
                                <option value="{{ $pKey }}" @selected($activeProvider === $pKey)>
                                    {{ strtoupper($pKey) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5 max-w-[50%]">
                        <select wire:change="switchSubModel($event.target.value)"
                                class="ai-select-input text-[10px] font-mono bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 px-1.5 py-0.5 rounded-xs truncate focus:ring-1 focus:ring-emerald-500">
                            @foreach ($dynamicModels as $dModel)
                                <option value="{{ $dModel['id'] }}" @selected($activeSubModel === $dModel['id'])>
                                    {{ $dModel['id'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Navigation Sub-Tabs --}}
                <div class="ai-tabs-strip flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-100/50 dark:bg-zinc-900/30 text-xs font-mono shrink-0">
                    <button type="button"
                            wire:click="setTab('chat')"
                            class="ai-tab-action flex-1 py-2 text-center border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'chat' ? 'is-active border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                        💬 Chat Copilot
                    </button>
                    <button type="button"
                            wire:click="setTab('knowledge')"
                            class="ai-tab-action flex-1 py-2 text-center border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'knowledge' ? 'is-active border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                        📚 Knowledge ({{ $totalKnowledgeChunks }})
                    </button>
                    <button type="button"
                            wire:click="setTab('cctv')"
                            class="ai-tab-action flex-1 py-2 text-center border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'cctv' ? 'is-active border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                        📡 CCTV Audit
                    </button>
                </div>

                {{-- TAB 1: CHAT INTERACTION --}}
                @if ($activeTab === 'chat')
                    {{-- Message History Stream --}}
                    <div id="ai-chat-messages-box"
                         class="ai-messages-scroll flex-1 p-4 overflow-y-auto space-y-4 text-xs font-sans">
                        
                        {{-- Quick Suggested Prompts --}}
                        @if(count($messages) <= 2)
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-2">
                                <p class="text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase">
                                    ⚡ Rekomendasi Perintah Cepat:
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button"
                                            wire:click="sendSuggestedPrompt('Periksa status PRD dan daftar blueprint arsitektur terkini')"
                                            class="text-[10px] font-mono px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 rounded-xs transition-colors cursor-pointer">
                                        🔍 Periksa status PRD & Blueprint
                                    </button>
                                    <button type="button"
                                            wire:click="sendSuggestedPrompt('Cek masa aktif domain dan hosting yang mendekati tenggat 30 hari')"
                                            class="text-[10px] font-mono px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 rounded-xs transition-colors cursor-pointer">
                                        🌐 Cek Domain & Hosting
                                    </button>
                                    <button type="button"
                                            wire:click="sendSuggestedPrompt('Berapa kapasitas sprint aktif dan statistik proyek saat ini?')"
                                            class="text-[10px] font-mono px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 rounded-xs transition-colors cursor-pointer">
                                        📊 Cek Kapasitas Sprint & Statistik
                                    </button>
                                </div>
                            </div>
                        @endif

                        @foreach ($messages as $msg)
                            <div class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                                <div class="flex items-center gap-1.5 mb-1 font-mono text-[10px] text-zinc-500">
                                    <span>{{ $msg['role'] === 'user' ? 'Lead Architect / Admin' : 'Autonomous AI (' . strtoupper($activeProvider) . ')' }}</span>
                                    <span>•</span>
                                    <span>{{ $msg['created_at'] ?? '' }}</span>
                                </div>

                                <div class="ai-bubble-box {{ $msg['role'] === 'user' ? 'user' : 'ai' }} max-w-[90%] p-3 rounded-xs border text-xs leading-relaxed {{ $msg['role'] === 'user' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 border-zinc-800 dark:border-zinc-200' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-300 dark:border-zinc-800' }}">
                                    <div class="prose prose-xs dark:prose-invert max-w-none break-words whitespace-pre-wrap font-sans">{{ $msg['content'] }}</div>

                                    {{-- Tool Calls Execution Audit --}}
                                    @if (!empty($msg['tool_calls']))
                                        <div class="mt-2.5 pt-2 border-t border-zinc-300 dark:border-zinc-800 font-mono text-[10px] space-y-1">
                                            <div class="text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1">
                                                <svg width="12" height="12" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px; max-width: 12px; max-height: 12px; display: inline-block; flex-shrink: 0;" class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Executed Native Tools:</span>
                                            </div>
                                            @foreach ($msg['tool_calls'] as $tCall)
                                                <div class="p-1.5 bg-zinc-200 dark:bg-zinc-950 rounded-xs border border-zinc-300 dark:border-zinc-800">
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">tool: {{ $tCall['tool'] ?? 'action' }}</span>
                                                    <div class="text-[9px] text-zinc-600 dark:text-zinc-400 overflow-x-auto">
                                                        args: {{ json_encode($tCall['args'] ?? [], JSON_UNESCAPED_UNICODE) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- Loading Skeleton Indicator --}}
                        @if ($isLoading)
                            <div class="flex items-center gap-2 p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs w-48 animate-pulse">
                                <span class="w-2 h-2 bg-emerald-500 rounded-xs animate-ping"></span>
                                <span class="font-mono text-xs text-zinc-600 dark:text-zinc-300">Memproses RAG & Tool...</span>
                            </div>
                        @endif
                    </div>

                    {{-- Chat Input Bar --}}
                    <div class="ai-input-dock p-3 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
                        <form wire:submit.prevent="sendMessage" class="space-y-2">
                            <div class="flex gap-2">
                                <textarea wire:model="inputMessage"
                                          wire:keydown.enter.exact.prevent="sendMessage"
                                          rows="2"
                                          placeholder="Ketik instruksi... (Enter untuk kirim, Shift+Enter untuk baris baru)"
                                          class="ai-textarea flex-1 p-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 text-zinc-900 dark:text-zinc-100 resize-none"></textarea>

                                <button type="submit"
                                        wire:loading.attr="disabled"
                                        class="ai-send-btn px-4 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 font-mono text-xs font-bold rounded-xs hover:bg-black dark:hover:bg-white transition-colors flex items-center justify-center cursor-pointer disabled:opacity-50">
                                    <span wire:loading.remove wire:target="sendMessage">Kirim</span>
                                    <span wire:loading wire:target="sendMessage">...</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-zinc-500 font-mono">
                                <span>Model: {{ $activeSubModel }}</span>
                                <span>10 Pilar Software Factory OS Ready</span>
                            </div>
                        </form>
                    </div>

                {{-- TAB 2: KNOWLEDGE BASE --}}
                @elseif ($activeTab === 'knowledge')
                    <div class="flex-1 p-4 overflow-y-auto space-y-4 text-xs font-mono">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100">Repositori Vektor 768 Dimensi</h4>
                                <p class="text-[10px] text-zinc-500">Tersinkronisasi ke 10 Pilar Software Factory OS</p>
                            </div>
                            <button type="button"
                                    wire:click="syncKnowledge"
                                    class="px-2.5 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[10px] font-bold rounded-xs hover:bg-black transition-colors cursor-pointer">
                                🔄 Sinkronisasi
                            </button>
                        </div>

                        <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-2">
                            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Total Chunks Terindeks: {{ $totalKnowledgeChunks }}</span>
                            <ul class="text-[10px] text-zinc-600 dark:text-zinc-400 space-y-1 list-disc pl-4">
                                <li>Pilar 1: PRD Komprehensif 26 Parameter</li>
                                <li>Pilar 2: PostgreSQL 16 Strict ULID & Keyset Cursor O(1)</li>
                                <li>Pilar 3: Dockerized Environment Terstandarisasi</li>
                                <li>Pilar 4: Synthetic Seeder & Anti-Zero Testing</li>
                                <li>Pilar 5: AI Coding Rules (.cursorrules & AGENTS.md)</li>
                                <li>Pilar 6: ApiContractTest Skeletal Suite (Pest)</li>
                                <li>Pilar 7: CI/CD Pipeline & Zero-Downtime Deployment</li>
                                <li>Pilar 8: Production Telemetry & Log Monitoring</li>
                                <li>Pilar 9: Estafet Sprint Task Anti-Context-Rot</li>
                                <li>Pilar 10: Managed Sprint Capacity & Anti-Collision Engine</li>
                            </ul>
                        </div>
                    </div>

                {{-- TAB 3: CCTV AUDIT TRACES --}}
                @elseif ($activeTab === 'cctv')
                    <div class="flex-1 p-4 overflow-y-auto space-y-3 text-xs font-mono">
                        <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">CCTV Telemetry Audit (12 Terakhir)</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">● Active Watch</span>
                        </div>

                        @forelse ($recentTraces as $trace)
                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-1 text-[10px]">
                                <div class="flex items-center justify-between font-bold">
                                    <span class="text-zinc-900 dark:text-zinc-100">{{ $trace->stage ?? 'action' }}</span>
                                    <span class="px-1.5 py-0.2 rounded-xs {{ $trace->status === 'success' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300' }}">
                                        {{ $trace->status }}
                                    </span>
                                </div>
                                <div class="text-zinc-500 truncate">
                                    {{ $trace->summary ?? $trace->notes ?? 'Aksi selesai diproses.' }}
                                </div>
                                <div class="text-[9px] text-zinc-400 flex justify-between">
                                    <span>{{ $trace->created_at?->diffForHumans() }}</span>
                                    <span>{{ $trace->execution_time_ms ? $trace->execution_time_ms . 'ms' : '' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-zinc-500 text-center py-6 text-[11px]">Belum ada jejak telemetri CCTV.</p>
                        @endforelse
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
