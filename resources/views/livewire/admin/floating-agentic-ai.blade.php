<div class="font-sans"
     x-data="{
         isDragging: false,
         isDrawerOpen: false,
         activeTab: '{{ $activeTab }}',
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
     @keydown.escape.window="isDrawerOpen = false"
     x-on:close-ai-drawer.window="isDrawerOpen = false"
     x-on:toggle-ai-copilot.window="resetPos(); isDrawerOpen = false; scrollToBottom()"
     x-on:open-ai-copilot.window="resetPos(); isDrawerOpen = false; scrollToBottom()"
     x-on:scroll-ai-to-bottom.window="scrollToBottom()"
     x-init="$watch('$wire.messages', () => scrollToBottom()); $watch('$wire.activeTab', val => { if (val) activeTab = val; })">

    <style>
        #ai-drawer-panel {
            position: absolute !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            z-index: 50 !important;
            width: 288px !important;
            max-width: 85% !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4) !important;
        }

        #ai-drawer-backdrop {
            position: absolute !important;
            inset: 0 !important;
            background: rgba(0, 0, 0, 0.5) !important;
            z-index: 45 !important;
            backdrop-filter: blur(2px) !important;
            -webkit-backdrop-filter: blur(2px) !important;
            cursor: pointer !important;
        }

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

        /* World-Class Unified Composer Box (Linear / Vercel / Cursor Precision) */
        .np-ai-composer {
            background-color: #ffffff !important;
            border: 1px solid #d4d4d8 !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            transition: border-color 150ms ease, box-shadow 150ms ease !important;
        }
        .np-ai-composer:focus-within {
            border-color: #18181b !important;
            box-shadow: 0 0 0 1px rgba(24, 24, 27, 0.12) !important;
        }
        .dark .np-ai-composer,
        [data-theme="dark"] .np-ai-composer {
            background-color: #09090b !important;
            border-color: #3f3f46 !important;
        }
        .dark .np-ai-composer:focus-within,
        [data-theme="dark"] .np-ai-composer:focus-within {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.28) !important;
        }
        .np-ai-composer textarea.np-ai-textarea {
            border: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            resize: none !important;
            font-size: 12.5px !important;
            line-height: 1.55 !important;
            color: #18181b !important;
        }
        .dark .np-ai-composer textarea.np-ai-textarea,
        [data-theme="dark"] .np-ai-composer textarea.np-ai-textarea {
            color: #f4f4f5 !important;
        }
        .np-ai-toolbar {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
            padding: 6px 10px !important;
            background-color: #fafafa !important;
            border-top: 1px solid #e4e4e7 !important;
        }
        .dark .np-ai-toolbar,
        [data-theme="dark"] .np-ai-toolbar {
            background-color: #121215 !important;
            border-top-color: #27272a !important;
        }
        .np-ai-attach-btn {
            height: 32px !important;
            padding: 0 10px !important;
            border-radius: 5px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background-color: #ffffff !important;
            border: 1px solid #d4d4d8 !important;
            color: #18181b !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: background-color 150ms ease, border-color 150ms ease !important;
        }
        .np-ai-attach-btn:hover {
            background-color: #f4f4f5 !important;
            border-color: #a1a1aa !important;
        }
        .dark .np-ai-attach-btn,
        [data-theme="dark"] .np-ai-attach-btn {
            background-color: #18181b !important;
            border-color: #3f3f46 !important;
            color: #e4e4e7 !important;
        }
        .dark .np-ai-attach-btn:hover,
        [data-theme="dark"] .np-ai-attach-btn:hover {
            background-color: #27272a !important;
            border-color: #52525b !important;
        }
        .np-ai-send-btn {
            height: 32px !important;
            padding: 0 14px !important;
            border-radius: 5px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            background-color: #09090b !important;
            border: 1px solid #09090b !important;
            color: #ffffff !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: background-color 150ms ease, border-color 150ms ease !important;
        }
        .np-ai-send-btn:hover:not(:disabled) {
            background-color: #27272a !important;
            border-color: #27272a !important;
        }
        .dark .np-ai-send-btn,
        [data-theme="dark"] .np-ai-send-btn {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: #09090b !important;
            font-weight: 700 !important;
        }
        .dark .np-ai-send-btn:hover:not(:disabled),
        [data-theme="dark"] .np-ai-send-btn:hover:not(:disabled) {
            background-color: #34d399 !important;
            border-color: #34d399 !important;
        }
        .np-ai-send-btn:disabled {
            opacity: 0.45 !important;
            cursor: not-allowed !important;
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
                            @click="isDrawerOpen = !isDrawerOpen"
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
                            wire:click="exportSessionMarkdown"
                            wire:loading.attr="disabled"
                            title="Unduh Seluruh Percakapan Sesi Ini (.MD)"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs transition-colors cursor-pointer">
                        <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </button>
                    <button type="button"
                            @click="isDrawerOpen = false"
                            wire:click="createNewChat(true)"
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
                    <div id="ai-drawer-panel"
                         x-show="isDrawerOpen"
                         x-cloak
                         style="display: none;"
                         :style="isDrawerOpen ? 'display: flex;' : 'display: none;'"
                         class="bg-zinc-100 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 flex-col shadow-2xl">
                        
                        {{-- Drawer Header --}}
                        <div class="p-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950/60">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg width="13" height="13" class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Riwayat Sesi</span>
                                </span>
                                <button type="button"
                                        @click="isDrawerOpen = false"
                                        wire:click="createNewChat(true)"
                                        title="Mulai Percakapan Baru"
                                        class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-[10px] font-bold rounded-xs transition-colors cursor-pointer">
                                    + Baru
                                </button>
                            </div>
                            <button type="button"
                                    @click="isDrawerOpen = false"
                                    title="Tutup Menu Riwayat"
                                    class="flex items-center gap-1 px-2.5 py-1 text-xs font-mono font-bold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 border border-zinc-300 dark:border-zinc-700 rounded-xs transition-colors cursor-pointer">
                                <svg width="12" height="12" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Tutup</span>
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
                                                    @click="isDrawerOpen = false"
                                                    wire:click="switchSession('{{ $sess['id'] }}')"
                                                    class="flex-1 text-left truncate flex items-center gap-1.5 cursor-pointer">
                                                <span class="text-[10px] text-zinc-400">💬</span>
                                                <span class="truncate">{{ $sess['title'] ?? 'Sesi' }}</span>
                                            </button>

                                            <div class="flex items-center gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity shrink-0">
                                                <button type="button"
                                                        wire:click.stop="exportSessionMarkdown('{{ $sess['id'] }}')"
                                                        title="Unduh sesi ini sebagai file .MD"
                                                        class="p-1 text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xs cursor-pointer">
                                                    <svg width="12" height="12" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </button>
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
                    <div id="ai-drawer-backdrop"
                         x-show="isDrawerOpen"
                         x-cloak
                         style="display: none;"
                         @click="isDrawerOpen = false"></div>

                    {{-- Navigation Sub-Tabs & Sesi Control --}}
                    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40 px-3 text-xs font-mono shrink-0">
                        <div class="flex items-center">
                            <button type="button"
                                    @click="activeTab = 'chat'; $wire.setTab('chat'); scrollToBottom()"
                                    :class="activeTab === 'chat' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950 font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200'"
                                    class="py-2 px-3 border-b-2 font-mono text-xs transition-colors cursor-pointer">
                                💬 Chat
                            </button>
                            <button type="button"
                                    @click="activeTab = 'knowledge'; $wire.setTab('knowledge')"
                                    :class="activeTab === 'knowledge' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950 font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200'"
                                    class="py-2 px-3 border-b-2 font-mono text-xs transition-colors cursor-pointer">
                                📚 Knowledge ({{ $totalKnowledgeChunks }})
                            </button>
                            <button type="button"
                                    @click="activeTab = 'cctv'; $wire.setTab('cctv')"
                                    :class="activeTab === 'cctv' ? 'border-zinc-900 dark:border-zinc-100 text-zinc-950 dark:text-white bg-white dark:bg-zinc-950 font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200'"
                                    class="py-2 px-3 border-b-2 font-mono text-xs transition-colors cursor-pointer">
                                📡 CCTV Audit
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5 py-1">
                            {{-- Unduh Seluruh Sesi (.MD) --}}
                            <button type="button"
                                    wire:click="exportSessionMarkdown"
                                    wire:loading.attr="disabled"
                                    title="Ekspor seluruh percakapan sesi ini ke berkas Markdown (.md)"
                                    class="inline-flex items-center gap-1 text-[10px] font-mono font-bold px-2 py-0.5 rounded-xs bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 transition-all cursor-pointer">
                                <svg width="11" height="11" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh Sesi (.MD)</span>
                            </button>

                            {{-- Turn Message Count --}}
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-xs border border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400">
                                {{ $sessionMessageCount }}/{{ $maxSessionMessages }} Pesan
                            </span>
                        </div>
                    </div>

                    {{-- TAB 1: CHAT INTERACTION --}}
                    <div x-show="activeTab === 'chat'" class="flex-1 flex flex-col overflow-hidden">
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
                                        <button type="button"
                                                wire:click="sendSuggestedPrompt('Tampilkan kontrak UI/UX Archetype, pintasan keyboard, dan seeder Indonesia untuk SECTOR_01_COMMERCE_OMNICHANNEL')"
                                                class="text-[10px] font-mono px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 rounded-xs transition-colors cursor-pointer">
                                            🎨 Cek UI/UX Sektor & Seeder
                                        </button>
                                        <button type="button"
                                                wire:click="sendSuggestedPrompt('Jalankan pemeriksaan blueprint slop scanner terkini dan laporkan skor kepatuhannya')"
                                                class="text-[10px] font-mono px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 rounded-xs transition-colors cursor-pointer">
                                            🛡️ Audit Anti-Slop Blueprint
                                        </button>
                                        <button type="button"
                                                wire:click="sendSuggestedPrompt('Jalankan Blueprint Doctor pada sektor kesehatan apotek FEFO dengan target 30 hari dan berikan rekomendasi Auto-Fix')"
                                                class="text-[10px] font-mono px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 rounded-xs transition-colors cursor-pointer">
                                            🩺 Jalankan Blueprint Doctor
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
                                                <div class="font-mono text-[10px] font-bold text-emerald-500 dark:text-emerald-600 flex items-center justify-between">
                                                    <span>Berkas Terlampir (Tersimpan &amp; Dikonversi ke .MD):</span>
                                                </div>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($msg['attachments'] as $att)
                                                        @if(is_array($att) && !isset($att['_metadata']))
                                                            @if(($att['type'] ?? '') === 'image')
                                                                <div class="rounded-sm overflow-hidden border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 max-w-[210px]">
                                                                    <a href="{{ $att['url'] ?? '#' }}" target="_blank" title="Lihat Gambar Penuh">
                                                                        <img src="{{ $att['url'] ?? '#' }}" alt="{{ $att['name'] ?? 'Image' }}" class="w-full h-auto object-cover max-h-32 transition-opacity hover:opacity-90" />
                                                                    </a>
                                                                    <div class="px-2 py-1 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-1 font-mono text-[9px] text-zinc-700 dark:text-zinc-300">
                                                                        <span class="truncate max-w-[115px] font-semibold">{{ $att['name'] ?? 'Gambar' }}</span>
                                                                        @if(!empty($att['md_url']))
                                                                            <a href="{{ $att['md_url'] }}" target="_blank" class="px-1.5 py-0.5 rounded-xs bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black shrink-0" title="Buka hasil ekstraksi teks OCR (.md)">
                                                                                OCR .MD ✓
                                                                            </a>
                                                                        @else
                                                                            <span class="text-emerald-600 dark:text-emerald-400 font-bold shrink-0">OCR ✓</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-sm bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 font-mono text-[11px] text-zinc-800 dark:text-zinc-200 hover:border-zinc-400 dark:hover:border-zinc-600 transition-colors">
                                                                    <a href="{{ $att['url'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-1.5" title="Buka berkas asli">
                                                                        <span class="px-1 py-0.5 rounded-xs bg-zinc-100 dark:bg-zinc-800 text-[9px] font-bold uppercase text-zinc-700 dark:text-zinc-300">{{ strtoupper($att['ext'] ?? 'DOC') }}</span>
                                                                        <span class="truncate max-w-[150px] font-semibold">{{ $att['name'] ?? 'Dokumen' }}</span>
                                                                        <span class="text-[9px] text-zinc-500">({{ $att['size'] ?? '' }})</span>
                                                                    </a>
                                                                    @if(!empty($att['md_url']))
                                                                        <a href="{{ $att['md_url'] }}" target="_blank" class="px-1.5 py-0.5 rounded-xs bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 text-[9px] font-black ml-0.5" title="Lihat hasil konversi MarkItDown Lokal (.md) — 0 Token AI">
                                                                            MD LOKAL ✓
                                                                        </a>
                                                                    @else
                                                                        <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold ml-0.5">MD ✓</span>
                                                                    @endif
                                                                </div>
                                                            @endif
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

                                        {{-- Message Bubble Action Bar (Salin Pesan & Unduh .MD) --}}
                                        <div class="mt-2.5 pt-2 flex items-center justify-between border-t {{ $msg['role'] === 'user' ? 'border-zinc-800 dark:border-zinc-300/30 text-zinc-300 dark:text-zinc-600' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400' }} text-[10px] font-mono"
                                             x-data="{ copied: false }">
                                            <div class="flex items-center gap-1.5 opacity-80">
                                                <span>{{ $msg['created_at'] ?? '' }}</span>
                                                @if(!empty($msg['provider']) && $msg['provider'] !== 'system')
                                                    <span>• {{ ucfirst($msg['provider']) }}</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2">
                                                {{-- Tombol Salin Pesan --}}
                                                <button type="button"
                                                        @click="navigator.clipboard.writeText(@js($msg['content'])); copied = true; setTimeout(() => copied = false, 2000)"
                                                        class="inline-flex items-center gap-1 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors cursor-pointer"
                                                        title="Salin isi pesan ke clipboard">
                                                    <svg x-show="!copied" width="12" height="12" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                    <svg x-show="copied" x-cloak width="12" height="12" class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span x-show="!copied">Salin</span>
                                                    <span x-show="copied" x-cloak class="text-emerald-500 font-bold">Tersalin!</span>
                                                </button>

                                                <span>•</span>

                                                {{-- Tombol Unduh Pesan Tunggal (.MD) --}}
                                                <button type="button"
                                                        wire:click="exportMessageMarkdown('{{ $msg['id'] ?? '' }}')"
                                                        wire:loading.attr="disabled"
                                                        class="inline-flex items-center gap-1 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors cursor-pointer"
                                                        title="Unduh pesan ini sebagai berkas Markdown (.md)">
                                                    <svg width="12" height="12" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>Unduh .MD</span>
                                                </button>
                                            </div>
                                        </div>
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

                        {{-- Upload Error Alert --}}
                        @if($uploadErrorMessage)
                            <div class="px-3.5 py-2 bg-rose-50 dark:bg-rose-950/90 border-t border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 font-mono text-[11px] flex items-center justify-between gap-2 shrink-0">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <svg width="14" height="14" class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    <span class="truncate">{{ $uploadErrorMessage }}</span>
                                </div>
                                <button type="button" wire:click="$set('uploadErrorMessage', null)" class="p-0.5 text-rose-700 hover:text-rose-950 dark:text-rose-300 dark:hover:text-white cursor-pointer shrink-0">
                                    <svg width="13" height="13" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif

                        {{-- World-Class Unified Composer Box (Linear / Vercel / Cursor Precision) --}}
                        <div class="p-3 bg-zinc-50/90 dark:bg-zinc-900/90 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
                            <form wire:submit.prevent="sendMessage" class="space-y-1.5">
                                <div class="np-ai-composer rounded-md bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700/90 transition-colors overflow-hidden">

                                    {{-- Embedded Multimodal Attachment Chips Tray --}}
                                    @if(!empty($uploadedFiles))
                                        <div class="px-3 py-2 bg-zinc-50 dark:bg-zinc-900/80 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center gap-1.5 overflow-x-auto">
                                            <span class="text-[10px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider shrink-0 tabular-nums mr-1">
                                                Siap Konversi ({{ count($uploadedFiles) }}/{{ $maxFiles }}):
                                            </span>
                                            @foreach($uploadedFiles as $idx => $f)
                                                @php
                                                    $fName = $f->getClientOriginalName();
                                                    $fExt = strtolower($f->getClientOriginalExtension());
                                                    $fIsImg = in_array($fExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true);
                                                    $fKb = round(($f->getSize() ?: 0) / 1024, 1);
                                                @endphp
                                                <div class="inline-flex items-center gap-1.5 pl-2 pr-1.5 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-sm shrink-0 text-zinc-900 dark:text-zinc-100">
                                                    <span class="px-1 py-0.5 rounded-xs text-[9px] font-mono font-black uppercase {{ $fIsImg ? 'bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700' : 'bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700' }}">
                                                        {{ $fIsImg ? 'OCR' : strtoupper($fExt ?: 'MD') }}
                                                    </span>
                                                    <span class="max-w-[135px] truncate text-[11px] font-medium">{{ $fName }}</span>
                                                    <span class="text-[10px] font-mono text-zinc-500 tabular-nums">{{ $fKb }}KB</span>
                                                    <button type="button"
                                                            wire:click="removeUploadedFile({{ $idx }})"
                                                            title="Hapus lampiran"
                                                            class="p-0.5 rounded-xs text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                                                        <svg width="12" height="12" style="width:12px;height:12px;" class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Seamless Auto-Expanding Textarea --}}
                                    <div class="px-3 pt-2.5 pb-2">
                                        <textarea wire:model="inputMessage"
                                                  wire:keydown.enter.exact.prevent="sendMessage"
                                                  rows="2"
                                                  @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 136) + 'px'"
                                                  placeholder="Ketik instruksi arsitektur, analisis data, atau lampirkan dokumen/gambar..."
                                                  {{ $sessionMessageCount >= $maxSessionMessages ? 'disabled' : '' }}
                                                  class="np-ai-textarea w-full bg-transparent border-0 p-0 text-xs leading-relaxed text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 resize-none font-sans min-h-[42px] max-h-[136px]"></textarea>
                                    </div>

                                    {{-- Integrated Action Toolbar (Attach + Engine Status + Shortcut + Primary Send) --}}
                                    <div class="np-ai-toolbar px-2.5 py-1.5 bg-zinc-50/80 dark:bg-zinc-900/60 border-t border-zinc-200/70 dark:border-zinc-800/80 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            {{-- Hidden Native File Input --}}
                                            <input type="file"
                                                   wire:model="uploadedFiles"
                                                   id="agentic-ai-file-picker"
                                                   multiple
                                                   accept=".txt,.csv,.tsv,.pdf,.docx,.xlsx,.pptx,.json,.xml,.md,.html,.jpg,.jpeg,.png,.webp,.gif"
                                                   class="hidden"
                                                   style="display:none;"
                                                   {{ $sessionMessageCount >= $maxSessionMessages ? 'disabled' : '' }} />

                                            {{-- Crisp World-Class Attach Button --}}
                                            <label for="agentic-ai-file-picker"
                                                   title="Lampirkan Dokumen (PDF, DOCX, XLSX, PPTX, CSV, JSON) -> 100% MarkItDown Lokal (0 Token AI) | Gambar (PNG, JPG, WEBP) -> 3-Tier OCR"
                                                   class="np-ai-attach-btn shrink-0 select-none {{ $sessionMessageCount >= $maxSessionMessages ? 'opacity-40 pointer-events-none' : '' }}">
                                                <span wire:loading.remove wire:target="uploadedFiles" class="inline-flex items-center gap-1.5">
                                                    <svg width="14" height="14" style="width:14px;height:14px;" class="w-3.5 h-3.5 text-zinc-600 dark:text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    <span>Lampirkan</span>
                                                </span>
                                                <span wire:loading wire:target="uploadedFiles" class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400">
                                                    <svg width="14" height="14" style="width:14px;height:14px;" class="w-3.5 h-3.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path></svg>
                                                    <span>Memuat...</span>
                                                </span>
                                                @if(!empty($uploadedFiles))
                                                    <span class="px-1.5 py-0.5 rounded-xs bg-zinc-900 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[10px] font-black tabular-nums">
                                                        {{ count($uploadedFiles) }}
                                                    </span>
                                                @endif
                                            </label>

                                            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-xs bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-bold truncate" title="Dokumen dikonversi lokal oleh MarkItDown (0 Token AI) &amp; Gambar diekstraksi via OCR">
                                                Local MarkItDown + OCR
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2.5 shrink-0">
                                            <span class="hidden sm:inline-block text-[10px] font-mono text-zinc-500 dark:text-zinc-400 select-none">
                                                Enter &#8629;
                                            </span>

                                            {{-- Crisp World-Class Primary Send Button --}}
                                            <button type="submit"
                                                    wire:loading.attr="disabled"
                                                    {{ $sessionMessageCount >= $maxSessionMessages ? 'disabled' : '' }}
                                                    title="Kirim Instruksi ke Autonomous AI Copilot (Enter)"
                                                    class="np-ai-send-btn shrink-0">
                                                <span wire:loading.remove wire:target="sendMessage" class="inline-flex items-center gap-1.5">
                                                    <span>Kirim</span>
                                                    <svg width="13" height="13" style="width:13px;height:13px;" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 12h14M13 6l6 6-6 6"/></svg>
                                                </span>
                                                <span wire:loading wire:target="sendMessage" class="inline-flex items-center gap-1.5">
                                                    <svg width="13" height="13" style="width:13px;height:13px;" class="w-3.5 h-3.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path></svg>
                                                    <span>Memproses</span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between px-0.5 text-[10px] text-zinc-500 dark:text-zinc-400 font-mono">
                                    <span class="truncate">Model: <strong class="text-zinc-800 dark:text-zinc-200">{{ $activeSubModel }}</strong></span>
                                    <span class="truncate">Project OS v3.0 • 18 Native Tools • RAG+CAG+KAG</span>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- TAB 2: KNOWLEDGE BASE --}}
                    <div x-show="activeTab === 'knowledge'"
                         x-cloak
                         style="display: none;"
                         class="flex-1 p-4 overflow-y-auto space-y-4 text-xs font-mono bg-white dark:bg-zinc-950">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100">Repositori Vektor 768 Dimensi (Tri-Hybrid v3.0)</h4>
                                <p class="text-[10px] text-zinc-600 dark:text-zinc-400">10 Ultimate Pillars + 12 Sektor UI/UX + 9 Stack + 8 Day-2 Ops Layers</p>
                            </div>
                            <button type="button"
                                    wire:click="syncKnowledge"
                                    wire:loading.attr="disabled"
                                    wire:target="syncKnowledge"
                                    class="px-2.5 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 text-[10px] font-bold rounded-xs hover:bg-black dark:hover:bg-white transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5 shrink-0">
                                <span wire:loading.remove wire:target="syncKnowledge">🔄 Sinkronisasi</span>
                                <span wire:loading wire:target="syncKnowledge" class="inline-flex items-center gap-1">
                                    <svg class="animate-spin w-3 h-3 text-white dark:text-zinc-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Menyinkronkan...</span>
                                </span>
                            </button>
                        </div>

                        <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-2">
                            <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">Total Chunks Terindeks: {{ $totalKnowledgeChunks }}</span>
                            <ul class="text-[10px] text-zinc-700 dark:text-zinc-300 space-y-1 list-disc pl-4">
                                <li>10 Pilar Software Factory OS &amp; Unit Economics</li>
                                <li>12 Sectoral Industrial UI/UX Archetypes (POS Split, Clinical 3-Pane, KDS, ISAK 35)</li>
                                <li>12 Realistic Indonesian Domain Seeders (Bebas Lorem Ipsum)</li>
                                <li>9 Curated Tech Stack Topology Adapters (M1–M4, D1–D3, S1–S2)</li>
                                <li>6 Killer Engines: Phased Prompts (1–6), Brownfield SQL Audit, verify-blueprint.sh &amp; CLI /init/{ULID}</li>
                                <li>8 Layer Day-2 Ops: Thermal 58/80mm &amp; A4 QR Print, Snapshot *_snapshot, Staged CSV, 4-Lane Queue &amp; Blueprint Doctor</li>
                                <li>18 Native Function Calling Tools + RBAC Guards + SHA-256 Audit Trail</li>
                            </ul>
                        </div>
                    </div>

                    {{-- TAB 3: CCTV AUDIT TRACES --}}
                    <div x-show="activeTab === 'cctv'"
                         x-cloak
                         style="display: none;"
                         class="flex-1 p-4 overflow-y-auto space-y-3 text-xs font-mono bg-white dark:bg-zinc-950">
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
                </div>
            @endif
        </div>
    @endif
</div>
