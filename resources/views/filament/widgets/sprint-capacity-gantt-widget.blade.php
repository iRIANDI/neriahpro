<x-filament-widgets::widget>
    <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                        Sprint Capacity & Anti-Collision Engine // Managed Capacity
                    </h3>
                </div>
                <p class="text-xs text-zinc-500 font-mono mt-1">
                    Monitoring kapasitas slot pengerjaan rekayasa enterprise & pencegahan bentrok jadwal (Anti-Collision).
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right font-mono text-xs hidden sm:block">
                    <span class="text-zinc-400 block text-[10px]">TOTAL UTILISASI</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $totalAssignedSlots }} / {{ $totalMaxSlots }} Slot</span>
                </div>
                <a
                    href="{{ $timelineUrl }}"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-bold rounded-none flex items-center gap-2 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Buka Timeline & Gantt &rarr;</span>
                </a>
            </div>
        </div>

        {{-- 3 Mini Batches Strip --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
            @foreach($batches as $b)
                <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $b['label'] }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 font-bold {{ $b['is_full'] ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                            {{ $b['urgency_badge'] }}
                        </span>
                    </div>
                    <div class="text-[11px] font-mono text-zinc-500 mt-0.5">
                        {{ $b['dates'] }}
                    </div>
                    <div class="flex items-center justify-between text-[11px] font-mono mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                        <span class="text-zinc-500">Terisi: {{ $b['used_slots'] }}/{{ $b['total_slots'] }}</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sisa {{ $b['remaining_slots'] }} Slot</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
