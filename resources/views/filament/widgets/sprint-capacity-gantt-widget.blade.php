<x-filament-widgets::widget>
    <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs font-sans">
        {{-- Widget Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse shrink-0" style="width: 10px; height: 10px; display: inline-block;"></span>
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                        Sprint Capacity & Anti-Collision Engine // Managed Capacity
                    </h3>
                </div>
                <p class="text-xs text-zinc-500 font-mono mt-1">
                    Monitoring kapasitas slot pengerjaan rekayasa enterprise & pencegahan bentrok jadwal (Anti-Collision).
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="text-right font-mono text-xs hidden sm:block">
                    <span class="text-zinc-400 block text-[10px] uppercase">TOTAL UTILISASI</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $totalAssignedSlots }} / {{ $totalMaxSlots }} Slot</span>
                </div>
                <a
                    href="{{ $timelineUrl }}"
                    style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; white-space: nowrap;"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-bold rounded-none transition shadow-xs"
                >
                    <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Buka Timeline & Gantt &rarr;</span>
                </a>
            </div>
        </div>

        {{-- Visual Capacity Progress Bar / Utilization Chart --}}
        <div class="mt-4 pt-1">
            <div class="flex items-center justify-between text-xs font-mono mb-2">
                <span class="text-zinc-500 dark:text-zinc-400 uppercase text-[10px] tracking-wider font-bold">
                    BAGAN UTILISASI KAPASITAS GLOBAL (MAKSIMAL {{ $totalMaxSlots }} SLOT TERKELOLA):
                </span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold text-[11px] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-none bg-emerald-500 inline-block" style="width: 8px; height: 8px;"></span>
                    <span>100% TERISOLASI (ZERO SCHEDULE OVERLAP)</span>
                </span>
            </div>

            {{-- 10-Segment Capacity Meter --}}
            <div class="grid grid-cols-10 gap-1.5 h-3.5">
                @for($i = 0; $i < $totalMaxSlots; $i++)
                    @if($i < $totalAssignedSlots)
                        <div class="bg-emerald-500 h-full rounded-none" style="background-color: #10b981;" title="Slot {{ $i + 1 }}: Terisi (Aktif / Booking)"></div>
                    @else
                        <div class="bg-zinc-100 dark:bg-zinc-800 border border-dashed border-zinc-300 dark:border-zinc-700 h-full rounded-none" title="Slot {{ $i + 1 }}: Tersedia (Siap Dikunci)"></div>
                    @endif
                @endfor
            </div>

            <div class="flex items-center justify-between text-[10px] font-mono text-zinc-400 mt-1.5">
                <span>Slot 1 (Batch 1)</span>
                <span>Slot {{ min(5, $totalMaxSlots) }} (Batch 2)</span>
                <span>Slot {{ $totalMaxSlots }} (Batch Q1)</span>
            </div>
        </div>

        {{-- 3 Mini Batches Strip with Visual Slot Blocks --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 mt-5">
            @foreach($batches as $b)
                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col justify-between shadow-xs">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono">
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $b['label'] }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 font-bold uppercase {{ $b['is_full'] ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' }}">
                                {{ $b['urgency_badge'] }}
                            </span>
                        </div>
                        <div class="text-[11px] font-mono text-zinc-500 mt-0.5">
                            {{ $b['dates'] }}
                        </div>

                        {{-- Individual Slot Visualizer Blocks --}}
                        <div class="mt-3 space-y-1.5">
                            @php
                                $totalBatchSlots = (int) ($b['total_slots'] ?? 3);
                                $batchProjects = $b['projects'] ?? [];
                            @endphp
                            @for($s = 0; $s < $totalBatchSlots; $s++)
                                @if(isset($batchProjects[$s]))
                                    <div class="p-1.5 bg-white dark:bg-zinc-900 border border-emerald-500/40 text-[10px] font-mono flex items-center justify-between gap-1.5">
                                        <div class="truncate text-zinc-900 dark:text-zinc-100 font-bold" title="{{ $batchProjects[$s]['name'] }}">
                                            🔒 {{ $batchProjects[$s]['name'] }}
                                        </div>
                                        <span class="text-[9px] px-1 bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold shrink-0">
                                            {{ $batchProjects[$s]['status'] }}
                                        </span>
                                    </div>
                                @else
                                    <div class="p-1.5 bg-zinc-100/60 dark:bg-zinc-900/40 border border-dashed border-zinc-200 dark:border-zinc-800 text-[10px] font-mono text-zinc-400 flex items-center justify-between">
                                        <span>Slot {{ $s + 1 }}: [Tersedia]</span>
                                        <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold">Siap Dikunci</span>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] font-mono mt-3 pt-2.5 border-t border-zinc-200 dark:border-zinc-800">
                        <span class="text-zinc-500">Terisi: {{ $b['used_slots'] }}/{{ $b['total_slots'] }}</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sisa {{ $b['remaining_slots'] }} Slot</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
