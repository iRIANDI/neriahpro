<x-filament-widgets::widget>
    <style>
        .sg-widget-container {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .sg-widget-container {
            background-color: #18181b;
            border-color: #27272a;
        }

        .sg-header {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e4e4e7;
        }
        @media (min-width: 640px) {
            .sg-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .dark .sg-header {
            border-bottom-color: #27272a;
        }

        .sg-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sg-pulse-dot {
            width: 10px;
            height: 10px;
            background-color: #10b981;
            flex-shrink: 0;
            animation: sg-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes sg-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }

        .sg-title-text {
            font-size: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #18181b;
            margin: 0;
        }
        .dark .sg-title-text { color: #f4f4f5; }

        .sg-subtitle {
            font-size: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            color: #71717a;
            margin: 4px 0 0 0;
        }
        .dark .sg-subtitle { color: #a1a1aa; }

        .sg-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .sg-util-text {
            display: none;
            text-align: right;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 12px;
        }
        @media (min-width: 640px) {
            .sg-util-text { display: block; }
        }
        .sg-util-label {
            display: block;
            color: #a1a1aa;
            font-size: 10px;
            text-transform: uppercase;
        }
        .sg-util-value {
            font-weight: 700;
            color: #18181b;
        }
        .dark .sg-util-value { color: #f4f4f5; }

        .sg-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
            padding: 8px 14px;
            background-color: #059669;
            color: #000000;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 12px;
            font-weight: 700;
            transition: background-color 0.2s;
        }
        .sg-btn:hover { background-color: #10b981; }

        .sg-chart-section {
            margin-top: 16px;
            padding-top: 4px;
        }
        .sg-chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            margin-bottom: 8px;
        }
        .sg-chart-title {
            color: #71717a;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }
        .dark .sg-chart-title { color: #a1a1aa; }
        .sg-chart-legend {
            color: #059669;
            font-weight: 700;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dark .sg-chart-legend { color: #34d399; }
        .sg-legend-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            display: inline-block;
        }

        .sg-capacity-grid {
            display: grid;
            grid-template-columns: repeat(10, minmax(0, 1fr));
            gap: 6px;
            height: 14px;
        }
        .sg-slot-filled {
            background-color: #10b981;
            height: 100%;
        }
        .sg-slot-empty {
            background-color: #f4f4f5;
            border: 1px dashed #d4d4d8;
            height: 100%;
            box-sizing: border-box;
        }
        .dark .sg-slot-empty {
            background-color: #27272a;
            border-color: #3f3f46;
        }
        .sg-chart-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            color: #a1a1aa;
            margin-top: 6px;
        }

        .sg-batches-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-top: 20px;
        }
        @media (min-width: 768px) {
            .sg-batches-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .sg-batch-card {
            padding: 14px;
            background-color: #fafafa;
            border: 1px solid #e4e4e7;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .sg-batch-card {
            background-color: #09090b;
            border-color: #27272a;
        }

        .sg-batch-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
        .sg-batch-title {
            font-weight: 700;
            color: #18181b;
        }
        .dark .sg-batch-title { color: #f4f4f5; }

        .sg-badge-red {
            background-color: rgba(244, 63, 94, 0.1);
            color: #e11d48;
            border: 1px solid rgba(244, 63, 94, 0.3);
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .dark .sg-badge-red { color: #fb7185; }

        .sg-badge-green {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .dark .sg-badge-green { color: #34d399; }

        .sg-batch-dates {
            font-size: 11px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            color: #71717a;
            margin-top: 2px;
        }
        .dark .sg-batch-dates { color: #a1a1aa; }

        .sg-slots-container {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .sg-slot-item-filled {
            padding: 6px;
            background-color: #ffffff;
            border: 1px solid rgba(16, 185, 129, 0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            font-size: 10px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
        .dark .sg-slot-item-filled { background-color: #18181b; }
        .sg-slot-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #18181b;
            font-weight: 700;
        }
        .dark .sg-slot-name { color: #f4f4f5; }
        .sg-slot-status {
            font-size: 9px;
            padding: 0 4px;
            background-color: rgba(16, 185, 129, 0.2);
            color: #047857;
            font-weight: 700;
            flex-shrink: 0;
        }
        .dark .sg-slot-status { color: #6ee7b7; }

        .sg-slot-item-empty {
            padding: 6px;
            background-color: rgba(244, 244, 245, 0.6);
            border: 1px dashed #e4e4e7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            color: #a1a1aa;
        }
        .dark .sg-slot-item-empty {
            background-color: rgba(24, 24, 27, 0.4);
            border-color: #27272a;
        }
        .sg-slot-ready {
            font-size: 9px;
            color: #059669;
            font-weight: 700;
        }
        .dark .sg-slot-ready { color: #34d399; }

        .sg-batch-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #e4e4e7;
        }
        .dark .sg-batch-footer { border-top-color: #27272a; }
        .sg-footer-used { color: #71717a; }
        .sg-footer-remain { color: #059669; font-weight: 700; }
        .dark .sg-footer-remain { color: #34d399; }
    </style>

    <div class="sg-widget-container">
        <!-- Widget Header -->
        <div class="sg-header">
            <div>
                <div class="sg-title-row">
                    <span class="sg-pulse-dot"></span>
                    <h3 class="sg-title-text">
                        Sprint Capacity & Anti-Collision Engine // Managed Capacity
                    </h3>
                </div>
                <p class="sg-subtitle">
                    Monitoring kapasitas slot pengerjaan rekayasa enterprise & pencegahan bentrok jadwal (Anti-Collision).
                </p>
            </div>

            <div class="sg-header-right">
                <div class="sg-util-text">
                    <span class="sg-util-label">TOTAL UTILISASI</span>
                    <span class="sg-util-value">{{ $totalAssignedSlots }} / {{ $totalMaxSlots }} Slot</span>
                </div>
                <a href="{{ $timelineUrl }}" class="sg-btn">
                    <svg width="14" height="14" style="flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Buka Timeline & Gantt &rarr;</span>
                </a>
            </div>
        </div>

        <!-- Visual Capacity Progress Bar / Utilization Chart -->
        <div class="sg-chart-section">
            <div class="sg-chart-header">
                <span class="sg-chart-title">
                    BAGAN UTILISASI KAPASITAS GLOBAL (MAKSIMAL {{ $totalMaxSlots }} SLOT TERKELOLA):
                </span>
                <span class="sg-chart-legend">
                    <span class="sg-legend-dot"></span>
                    <span>100% TERISOLASI (ZERO SCHEDULE OVERLAP)</span>
                </span>
            </div>

            <!-- 10-Segment Capacity Meter -->
            <div class="sg-capacity-grid">
                @for($i = 0; $i < $totalMaxSlots; $i++)
                    @if($i < $totalAssignedSlots)
                        <div class="sg-slot-filled" title="Slot {{ $i + 1 }}: Terisi (Aktif / Booking)"></div>
                    @else
                        <div class="sg-slot-empty" title="Slot {{ $i + 1 }}: Tersedia (Siap Dikunci)"></div>
                    @endif
                @endfor
            </div>

            <div class="sg-chart-footer">
                <span>Slot 1 (Batch 1)</span>
                <span>Slot {{ min(5, $totalMaxSlots) }} (Batch 2)</span>
                <span>Slot {{ $totalMaxSlots }} (Batch Q1)</span>
            </div>
        </div>

        <!-- 3 Mini Batches Strip with Visual Slot Blocks -->
        <div class="sg-batches-grid">
            @foreach($batches as $b)
                <div class="sg-batch-card">
                    <div>
                        <div class="sg-batch-header">
                            <span class="sg-batch-title">{{ $b['label'] }}</span>
                            <span class="{{ $b['is_full'] ? 'sg-badge-red' : 'sg-badge-green' }}">
                                {{ $b['urgency_badge'] }}
                            </span>
                        </div>
                        <div class="sg-batch-dates">
                            {{ $b['dates'] }}
                        </div>

                        <!-- Individual Slot Visualizer Blocks -->
                        <div class="sg-slots-container">
                            @php
                                $totalBatchSlots = (int) ($b['total_slots'] ?? 3);
                                $batchProjects = $b['projects'] ?? [];
                            @endphp
                            @for($s = 0; $s < $totalBatchSlots; $s++)
                                @if(isset($batchProjects[$s]))
                                    <div class="sg-slot-item-filled">
                                        <div class="sg-slot-name" title="{{ $batchProjects[$s]['name'] }}">
                                            🔒 {{ $batchProjects[$s]['name'] }}
                                        </div>
                                        <span class="sg-slot-status">
                                            {{ $batchProjects[$s]['status'] }}
                                        </span>
                                    </div>
                                @else
                                    <div class="sg-slot-item-empty">
                                        <span>Slot {{ $s + 1 }}: [Tersedia]</span>
                                        <span class="sg-slot-ready">Siap Dikunci</span>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>

                    <div class="sg-batch-footer">
                        <span class="sg-footer-used">Terisi: {{ $b['used_slots'] }}/{{ $b['total_slots'] }}</span>
                        <span class="sg-footer-remain">Sisa {{ $b['remaining_slots'] }} Slot</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
