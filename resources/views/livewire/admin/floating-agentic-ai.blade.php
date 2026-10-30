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
             if (e.target.closest('button') || e.target.closest('input') || e.target.closest('select') || e.target.closest('textarea') || e.target.closest('label') || e.target.closest('a')) return;
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
     x-on:scroll-ai-to-bottom.window="scrollToBottom()"
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
            border: 1px solid #e4e4e7 !important;
            border-radius: 4px !important;
            overflow: hidden !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35), 0 0 0 1px rgba(0,0,0,0.06) !important;
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
            width: 540px !important;
            max-width: calc(100vw - 2rem) !important;
            height: 720px !important;
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

        .ai-chat-bubble p {
            margin-bottom: 0.75rem !important;
            line-height: 1.65 !important;
        }
        .ai-chat-bubble p:last-child {
            margin-bottom: 0 !important;
        }
        .ai-chat-bubble ul, .ai-chat-bubble ol {
            margin-top: 0.4rem !important;
            margin-bottom: 0.75rem !important;
            padding-left: 1.25rem !important;
        }
        .ai-chat-bubble li {
            margin-bottom: 0.3rem !important;
            line-height: 1.55 !important;
        }
        .ai-chat-bubble table {
            width: 100% !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            border-collapse: collapse !important;
            font-size: 11px !important;
        }
        .ai-chat-bubble th {
            background-color: #f4f4f5 !important;
            color: #18181b !important;
            padding: 0.4rem 0.5rem !important;
            border: 1px solid #d4d4d8 !important;
            font-weight: 700 !important;
            text-align: left !important;
        }
        .dark .ai-chat-bubble th {
            background-color: #18181b !important;
            color: #10b981 !important;
            border-color: #3f3f46 !important;
        }
        .ai-chat-bubble td {
            padding: 0.4rem 0.5rem !important;
            border: 1px solid #e4e4e7 !important;
            vertical-align: top !important;
        }
        .dark .ai-chat-bubble td {
            border-color: #27272a !important;
        }
        .ai-chat-bubble pre {
            background: #18181b !important;
            color: #f4f4f5 !important;
            padding: 0.75rem !important;
            border-radius: 2px !important;
            overflow-x: auto !important;
            font-size: 11px !important;
            margin: 0.5rem 0 !important;
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
                    title="Buka Neriah Pro AI Copilot"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-zinc-950 dark:bg-zinc-900 text-white border border-zinc-700 dark:border-zinc-300 shadow-2xl rounded-xs hover:bg-black dark:hover:bg-zinc-800 hover:border-emerald-500 transition-all duration-150 cursor-pointer focus:outline-none focus:ring-1 focus:ring-emerald-500">
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
             :style="'{{ $windowState }}' !== 'maximized' ? `transform: translate(${offsetX}px, ${offsetY}px)` : 'none'"
             class="bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs flex flex-col overflow-hidden text-zinc-950 dark:text-zinc-100 relative {{ $windowState === 'maximized' ? 'is-maximized' : ($windowState === 'minimized' ? 'is-minimized' : 'is-normal') }}">

            {{-- Window Header (Draggable) --}}
            <div @mousedown="if ('{{ $windowState }}' !== 'maximized') initDrag($event)"
                 class="px-3.5 py-2.5 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 select-none cursor-move">
                
                <div class="flex items-center gap-2 truncate">
                    {{-- Hamburger Drawer Button --}}
                    <button type="button"
                            wire:click="toggleDrawer"
                            title="Menu Riwayat Sesi & Model AI"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs transition-colors cursor-pointer shrink-0">
                        <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <span class="w-2 h-2 bg-emerald-500 rounded-xs animate-pulse shrink-0"></span>

                    <div class="truncate">
                        <div class="flex items-center gap-1.5">
                            <h3 class="font-mono text-xs font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100 truncate">
                                Neriah Pro AI Copilot
                            </h3>
                            <span class="text-[9px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 font-mono font-bold text-zinc-800 dark:text-zinc-200 rounded-xs border border-zinc-300 dark:border-zinc-700 uppercase shrink-0">
                                {{ $activeProvider }}
                            </span>
                            @if ($isLoading)
                                <span class="text-[9px] font-mono text-emerald-600 dark:text-emerald-400 animate-pulse font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-xs animate-ping"></span>
                                    <span>mengetik...</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Window Controls --}}
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button"
                            wire:click="createNewChat(false)"
                            title="Buat Sesi Percakapan Baru"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button type="button"
                            wire:click="minimizeWindow"
                            title="{{ $windowState === 'minimized' ? 'Buka Penuh' : 'Minimize' }}"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <button type="button"
                            wire:click="maximizeWindow"
                            title="{{ $windowState === 'maximized' ? 'Restore Ukuran' : 'Maximize Layar Penuh' }}"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs transition-colors cursor-pointer hidden sm:inline-block">
                        <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </button>
                    <button type="button"
                            wire:click="closeWindow"
                            title="Tutup Jendela"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            @if($windowState !== 'minimized')
                {{-- Main Workspace Container --}}
                <div class="relative flex-1 flex flex-col overflow-hidden">

                    {{-- ============================================================= --}}
                    {{-- SIDE DRAWER (Menu Riwayat Sesi, Buat Baru, Hapus & Model AI)  --}}
                    {{-- ============================================================= --}}
                    <div class="absolute inset-y-0 left-0 z-30 w-72 bg-zinc-100 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 flex flex-col transition-transform duration-200 ease-in-out shadow-2xl {{ $isDrawerOpen ? 'translate-x-0' : '-translate-x-full' }}">
                        
                        {{-- Drawer Header --}}
                        <div class="p-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950/60">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg width="13" height="13" class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Riwayat Sesi</span>
                                </span>
                                <button type="button"
                                        wire:click="createNewChat(false)"
                                        title="Mulai Percakapan Baru"
                                        class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-[10px] font-bold rounded-xs transition-colors cursor-pointer">
                                    + Baru
                                </button>
                            </div>
                            <button type="button"
                                    wire:click="toggleDrawer"
                                    title="Tutup Menu"
                                    class="p-1 text-zinc-500 hover:text-zinc-950 dark:hover:text-white rounded-xs">
                                <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                        </div>

                        {{-- Session List --}}
                        <div class="flex-1 overflow-y-auto p-2 space-y-1">
                            @forelse($sessions as $sess)
                                <div wire:key="session-item-{{ $sess['id'] }}"
                                     class="group flex items-center justify-between p-2 rounded-xs text-xs font-mono transition-all {{ $currentSessionId === $sess['id'] ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-950 dark:text-white font-bold border-l-2 border-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                                    
                                    @if($renamingSessionId === $sess['id'])
                                        <div class="flex-1 flex items-center gap-1">
                                            <input type="text"
                                                   wire:model="renamingSessionTitle"
                                                   wire:keydown.enter="saveRenameSession"
                                                   wire:keydown.escape="cancelRenameSession"
                                                   autofocus
                                                   class="flex-1 bg-white dark:bg-zinc-950 border border-emerald-500 rounded-xs px-2 py-0.5 text-xs text-zinc-900 dark:text-white outline-none">
                                            <button type="button"
                                                    wire:click.stop="saveRenameSession"
                                                    title="Simpan nama"
                                                    class="p-1 text-emerald-600 hover:text-emerald-500">
                                                ✓
                                            </button>
                                            <button type="button"
                                                    wire:click.stop="cancelRenameSession"
                                                    title="Batal"
                                                    class="p-1 text-rose-500 hover:text-rose-400">
                                                ✕
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex-1 min-w-0 flex items-center justify-between gap-1">
                                            <button type="button"
                                                    wire:click="switchSession('{{ $sess['id'] }}')"
                                                    class="flex-1 text-left truncate flex items-center gap-1.5 cursor-pointer">
                                                <span class="text-[10px] text-zinc-400">💬</span>
                                                <span class="truncate">{{ $sess['title'] ?? 'Sesi' }}</span>
                                            </button>

                                            <div class="flex items-center gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity shrink-0">
                                                <button type="button"
                                                        wire:click.stop="startRenameSession('{{ $sess['id'] }}')"
                                                        title="Ganti Judul"
                                                        class="p-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 rounded-xs cursor-pointer">
                                                    ✏️
                                                </button>
                                                <button type="button"
                                                        wire:click.stop="deleteSession('{{ $sess['id'] }}')"
                                                        title="Hapus Sesi Percakapan"
                                                        class="p-1 text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 rounded-xs cursor-pointer">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs font-mono text-zinc-400 p-3 text-center">Belum ada riwayat percakapan.</p>
                            @endforelse
                        </div>

                        {{-- Drawer Footer: Provider & Model Selector (Hanya Yang Memiliki API Key) --}}
                        <div class="p-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/80 space-y-2">
                            <div>
                                <label class="block text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider mb-1">
                                    Provider AI (Terkonfigurasi):
                                </label>
                                <div class="grid grid-cols-2 gap-1 font-mono text-[11px]">
                                    @foreach($availableProviders as $pKey => $pLabel)
                                        <button type="button"
                                                wire:click="switchProvider('{{ $pKey }}')"
                                                class="px-2 py-1.5 rounded-xs border text-left font-bold truncate transition-all cursor-pointer {{ $activeProvider === $pKey ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 border-zinc-900 dark:border-zinc-100 shadow-xs' : 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-zinc-500' }}">
                                            {{ strtoupper($pKey) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            @if(!empty($dynamicModels))
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">
                                            Model Server:
                                        </label>
                                        <button type="button"
                                                wire:click="refreshDynamicModels"
                                                title="Sinkronkan Model dari Server"
                                                class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                                            🔄 Sinkron
                                        </button>
                                    </div>
                                    <select wire:change="switchSubModel($event.target.value)"
                                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xs px-2 py-1 text-xs font-mono text-zinc-900 dark:text-white outline-none focus:ring-1 focus:ring-emerald-500">
                                        @foreach($dynamicModels as $dm)
                                            <option value="{{ $dm['id'] }}" @selected($activeSubModel === $dm['id'])>
                                                {{ $dm['id'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Drawer Backdrop --}}
                    @if($isDrawerOpen)
                        <div wire:click="toggleDrawer" class="absolute inset-0 bg-black/40 z-20 transition-opacity"></div>
                    @endif

                    {{-- Navigation Sub-Tabs & Sesi Control --}}
                    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40 px-3 text-xs font-mono shrink-0">
                        <div class="flex items-center">
                            <button type="button"
                                    wire:click="setTab('chat')"
                                    class="py-2 px-3 border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'chat' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                                💬 Chat
                            </button>
                            <button type="button"
                                    wire:click="setTab('knowledge')"
                                    class="py-2 px-3 border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'knowledge' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                                📚 Knowledge ({{ $totalKnowledgeChunks }})
                            </button>
                            <button type="button"
                                    wire:click="setTab('cctv')"
                                    class="py-2 px-3 border-b-2 font-bold transition-colors cursor-pointer {{ $activeTab === 'cctv' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                                📡 CCTV Audit
                            </button>
                        </div>

                        <div class="flex items-center gap-2 py-1">
                            {{-- Turn Message Count --}}
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-xs border border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400">
                                {{ $sessionMessageCount }}/{{ $maxSessionMessages }} Pesan
                            </span>
                        </div>
                    </div>

                    {{-- TAB 1: CHAT INTERACTION --}}
                    @if ($activeTab === 'chat')
                        {{-- Message History Stream --}}
                        <div id="ai-chat-messages-box"
                             class="flex-1 p-4 overflow-y-auto space-y-4 text-xs font-sans bg-white dark:bg-zinc-950">
                            
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

                                    <div class="max-w-[90%] p-3.5 rounded-xs border text-xs leading-relaxed {{ $msg['role'] === 'user' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 border-zinc-800 dark:border-zinc-200' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-300 dark:border-zinc-800' }}">
                                        
                                        {{-- Render Message Body via Markdown --}}
                                        <div class="prose prose-xs dark:prose-invert max-w-none break-words whitespace-pre-wrap font-sans ai-chat-bubble">
                                            {!! \Illuminate\Support\Str::markdown($msg['content'] ?? '', [
                                                'html_input' => 'strip',
                                                'allow_unsafe_links' => false,
                                            ]) !!}
                                        </div>

                                        {{-- Attachments Display in Message Bubble --}}
                                        @if(!empty($msg['attachments']))
                                            <div class="mt-3 pt-2 border-t {{ $msg['role'] === 'user' ? 'border-zinc-700 dark:border-zinc-300' : 'border-zinc-300 dark:border-zinc-800' }} space-y-1.5">
                                                <div class="font-mono text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                    📄 Berkas Terlampir (Tersimpan di Sesi Ini):
                                                </div>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($msg['attachments'] as $att)
                                                        @if(($att['type'] ?? '') === 'image')
                                                            <div class="rounded-xs overflow-hidden border border-zinc-300 dark:border-zinc-700 max-w-[200px]">
                                                                <a href="{{ $att['url'] ?? '#' }}" target="_blank" title="Lihat Gambar Penuh">
                                                                    <img src="{{ $att['url'] ?? '#' }}" alt="{{ $att['name'] ?? 'Image' }}" class="w-full h-auto object-cover max-h-32 hover:scale-105 transition-transform" />
                                                                </a>
                                                            </div>
                                                        @else
                                                            <a href="{{ $att['url'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-1 rounded-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 font-mono text-[11px] text-zinc-800 dark:text-zinc-200 hover:border-emerald-500 transition-colors">
                                                                <span>📄</span>
                                                                <span class="truncate max-w-[160px] font-bold">{{ $att['name'] ?? 'Dokumen' }}</span>
                                                                <span class="text-[9px] opacity-70">({{ $att['size'] ?? '' }})</span>
                                                                <span class="text-[9px] text-emerald-500 font-bold ml-0.5">MD ✓</span>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Tool Calls Execution Audit --}}
                                        @if (!empty($msg['tool_calls']))
                                            <div class="mt-2.5 pt-2 border-t border-zinc-300 dark:border-zinc-800 font-mono text-[10px] space-y-1">
                                                <div class="text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1">
                                                    <svg width="12" height="12" class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Eksekusi Native Tool Terverifikasi:</span>
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
                                <div class="flex items-center gap-2 p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs w-56 animate-pulse font-mono text-xs">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-xs animate-ping"></span>
                                    <span class="text-zinc-600 dark:text-zinc-300">Memproses MarkItDown & AI...</span>
                                </div>
                            @endif
                        </div>

                        {{-- Multimodal File Attachment Preview Bar --}}
                        @if(!empty($uploadedFiles))
                            <div class="px-3 py-2 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2 overflow-x-auto shrink-0 font-mono text-xs">
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase shrink-0">
                                    Lampiran ({{ count($uploadedFiles) }}/{{ $maxFiles }}):
                                </span>
                                @foreach($uploadedFiles as $idx => $f)
                                    <div class="flex items-center gap-1.5 px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs shrink-0 text-zinc-900 dark:text-zinc-100">
                                        <span class="text-[10px]">📄</span>
                                        <span class="max-w-[130px] truncate text-[11px] font-bold">{{ $f->getClientOriginalName() }}</span>
                                        <button type="button"
                                                wire:click="removeUploadedFile({{ $idx }})"
                                                title="Hapus file"
                                                class="text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 ml-1 cursor-pointer">
                                            ✕
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Upload Error Alert --}}
                        @if($uploadErrorMessage)
                            <div class="px-3 py-1.5 bg-rose-100 dark:bg-rose-950 border-t border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 font-mono text-xs flex items-center justify-between shrink-0">
                                <span>⚠️ {{ $uploadErrorMessage }}</span>
                                <button type="button" wire:click="$set('uploadErrorMessage', null)" class="text-rose-600 hover:text-rose-900 dark:hover:text-white cursor-pointer">✕</button>
                            </div>
                        @endif

                        {{-- Chat Input Bar --}}
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
                            <form wire:submit.prevent="sendMessage" class="space-y-2">
                                <div class="flex items-end gap-2">
                                    {{-- File Upload Picker Button --}}
                                    <div class="relative shrink-0 mb-0.5">
                                        <input type="file"
                                               wire:model="uploadedFiles"
                                               id="agentic-ai-file-picker"
                                               multiple
                                               accept=".txt,.csv,.tsv,.pdf,.docx,.xlsx,.pptx,.json,.md,.html,.jpg,.jpeg,.png,.webp,.gif"
                                               class="hidden"
                                               {{ $sessionMessageCount >= $maxSessionMessages ? 'disabled' : '' }} />

                                        <label for="agentic-ai-file-picker"
                                               title="Lampirkan Dokumen (PDF, DOCX, XLSX, TXT, CSV, Gambar) - Otomatis Dikonversi MarkItDown"
                                               class="cursor-pointer w-9 h-9 rounded-xs flex items-center justify-center bg-white dark:bg-zinc-950 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 transition-colors {{ $sessionMessageCount >= $maxSessionMessages ? 'opacity-50 pointer-events-none' : '' }}">
                                            <div wire:loading.remove wire:target="uploadedFiles">
                                                <svg width="15" height="15" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            </div>
                                            <div wire:loading wire:target="uploadedFiles">
                                                <span class="animate-spin text-xs">⏳</span>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Auto-resizing Textarea --}}
                                    <div class="flex-1 min-w-0">
                                        <textarea wire:model="inputMessage"
                                                  wire:keydown.enter.exact.prevent="sendMessage"
                                                  rows="2"
                                                  placeholder="Ketik instruksi... (Enter kirim, Shift+Enter baris baru)"
                                                  class="w-full p-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 text-zinc-900 dark:text-zinc-100 resize-none font-sans"></textarea>
                                    </div>

                                    {{-- Send Button --}}
                                    <button type="submit"
                                            wire:loading.attr="disabled"
                                            class="h-9 px-4 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 font-mono text-xs font-bold rounded-xs hover:bg-black dark:hover:bg-white transition-colors flex items-center justify-center cursor-pointer disabled:opacity-50 shrink-0">
                                        <span wire:loading.remove wire:target="sendMessage">Kirim</span>
                                        <span wire:loading wire:target="sendMessage">...</span>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between text-[10px] text-zinc-500 font-mono">
                                    <span>Model: {{ $activeSubModel }}</span>
                                    <span>10 Pilar Software Factory OS Ready • MarkItDown Converted</span>
                                </div>
                            </form>
                        </div>

                    {{-- TAB 2: KNOWLEDGE BASE --}}
                    @elseif ($activeTab === 'knowledge')
                        <div class="flex-1 p-4 overflow-y-auto space-y-4 text-xs font-mono bg-white dark:bg-zinc-950">
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
                        <div class="flex-1 p-4 overflow-y-auto space-y-3 text-xs font-mono bg-white dark:bg-zinc-950">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">CCTV Telemetry Audit (12 Terakhir)</span>
                                <button type="button"
                                        wire:click="clearCctvTraces"
                                        class="text-[10px] text-rose-600 dark:text-rose-400 hover:underline cursor-pointer">
                                    Hapus Jejak
                                </button>
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
                </div>
            @endif
        </div>
    @endif
</div>
