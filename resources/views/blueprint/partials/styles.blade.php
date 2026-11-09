    <style>
        :root {
            --global-nav-h: 88px;
            --toolbar-h: 46px;
        }
        .prd-scroll-target {
            scroll-margin-top: calc(var(--global-nav-h, 88px) + var(--toolbar-h, 46px) + 16px) !important;
        }
        [x-cloak] { display: none !important; }
        .goog-te-banner-frame { display: none !important; }
        body { top: 0 !important; }
        .skiptranslate { display: none !important; }
        #goog-gt-tt { display: none !important; }
        .goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }
        @media print {
            .no-print, [class*="no-print"], header, aside, footer, #goog-gt-tt, .skiptranslate, .goog-te-banner-frame, .fixed, button, [role="button"], #blueprint-toolbar {
                display: none !important;
            }
            * {
                background-image: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .accordion-body, .accordion-body [x-show] {
                display: block !important;
                opacity: 1 !important;
                transform: none !important;
            }
            /* Reveal all feature specification tabs in print preview (Complete PRD) */
            [x-show*="specTab"] {
                display: block !important;
                opacity: 1 !important;
                margin-top: 8px !important;
                margin-bottom: 8px !important;
            }
            [x-show*="specTab"] > * {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            html, body {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                font-size: 10pt !important;
                line-height: 1.45 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            *, *:before, *:after {
                color: #0f172a !important;
                text-shadow: none !important;
                box-shadow: none !important;
            }
            .bg-white, .dark\:bg-zinc-900, .dark\:bg-zinc-950, .bg-zinc-900, .bg-zinc-950, .bg-zinc-800, .dark\:bg-zinc-800, .bg-black, [class*="bg-zinc-"], [class*="bg-black"], [class*="bg-gradient"] {
                background-color: #ffffff !important;
                background: #ffffff !important;
                color: #0f172a !important;
                border-color: #cbd5e1 !important;
            }
            pre, code, th, .bg-zinc-100, .bg-zinc-50, .dark\:bg-zinc-800\/50, .bg-zinc-900\/50 {
                background-color: #f8fafc !important;
                color: #0f172a !important;
                border-color: #cbd5e1 !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 10mm 15mm 10mm;
            }
            .max-w-\[1480px\], main, .max-w-4xl, .lg\:max-w-5xl {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
            .print-break-inside-avoid, .break-inside-avoid, table, thead, tbody, tr, pre, code, blockquote,
            .pricing-card, .feature-card,
            .grid > *, [class*="grid"] > * {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            h1, h2, h3, h4, h5, h6 {
                break-after: avoid !important;
                page-break-after: avoid !important;
            }
            svg, img {
                max-width: 100% !important;
                height: auto !important;
            }
            #section-10 {
                display: block !important;
                background-color: #ffffff !important;
                border: 2px solid #059669 !important;
                color: #0f172a !important;
            }
        }
        .custom-prd-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #10b981 #18181b;
        }
        .custom-prd-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-prd-scrollbar::-webkit-scrollbar-track {
            background: #18181b;
        }
        .custom-prd-scrollbar::-webkit-scrollbar-thumb {
            background: #10b981;
        }
        .custom-prd-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #34d399;
        }

        /* Custom Range Slider (Brutalist Strict Subtle Radii - Anti-AI Slop) */
        :root {
            --slider-track-empty: #e4e4e7;
            --slider-track-border: #d4d4d8;
        }
        .dark {
            --slider-track-empty: #18181b;
            --slider-track-border: #27272a;
        }
        .custom-range-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 8px;
            outline: none !important;
            border: 1px solid var(--slider-track-border);
            border-radius: 0px !important;
            cursor: pointer;
            transition: border-color 0.15s ease;
        }
        .custom-range-slider:focus,
        .custom-range-slider:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            border-color: #52525b !important;
        }
        .custom-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 14px;
            height: 18px;
            border-radius: 0px !important;
            border: 1px solid #000000;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.5);
            transition: transform 0.1s ease;
        }
        .custom-range-slider::-webkit-slider-thumb:hover {
            transform: scaleY(1.15);
        }
        .custom-range-slider::-moz-range-thumb {
            width: 14px;
            height: 18px;
            border-radius: 0px !important;
            border: 1px solid #000000;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .custom-range-slider::-moz-range-thumb:hover {
            transform: scaleY(1.15);
        }
        .custom-range-slider.slider-emerald::-webkit-slider-thumb { background-color: #10b981; }
        .custom-range-slider.slider-emerald::-moz-range-thumb { background-color: #10b981; }
        .custom-range-slider.slider-sky::-webkit-slider-thumb { background-color: #0ea5e9; }
        .custom-range-slider.slider-sky::-moz-range-thumb { background-color: #0ea5e9; }
        .custom-range-slider.slider-amber::-webkit-slider-thumb { background-color: #f59e0b; }
        .custom-range-slider.slider-amber::-moz-range-thumb { background-color: #f59e0b; }

        /* High-Contrast Fullscreen & In-Card Mermaid Theme Fidelity (Dark Mode) */
        .dark #mermaid-fullscreen-target svg,
        .dark [id*="mermaid-"][id*="-target"] svg {
            color: #f4f4f5 !important;
            background-color: transparent !important;
        }

        /* 1. FLOWCHART NODES & CLUSTERS (Dark Mode) */
        .dark #mermaid-fullscreen-target svg .node rect,
        .dark #mermaid-fullscreen-target svg .node polygon,
        .dark #mermaid-fullscreen-target svg .node circle,
        .dark #mermaid-fullscreen-target svg .node path,
        .dark [id*="mermaid-"][id*="-target"] svg .node rect,
        .dark [id*="mermaid-"][id*="-target"] svg .node polygon,
        .dark [id*="mermaid-"][id*="-target"] svg .node circle,
        .dark [id*="mermaid-"][id*="-target"] svg .node path {
            fill: #18181b !important;
            stroke: #10b981 !important;
            stroke-width: 1.5px !important;
        }
        .dark #mermaid-fullscreen-target svg .cluster rect,
        .dark [id*="mermaid-"][id*="-target"] svg .cluster rect {
            fill: #18181b !important;
            stroke: #3f3f46 !important;
            stroke-width: 1.5px !important;
            stroke-dasharray: 4,4 !important;
        }
        .dark #mermaid-fullscreen-target svg .cluster text,
        .dark #mermaid-fullscreen-target svg .cluster span,
        .dark [id*="mermaid-"][id*="-target"] svg .cluster text,
        .dark [id*="mermaid-"][id*="-target"] svg .cluster span {
            fill: #34d399 !important;
            color: #34d399 !important;
            font-weight: 800 !important;
        }
        .dark #mermaid-fullscreen-target svg .node text,
        .dark #mermaid-fullscreen-target svg .node span,
        .dark #mermaid-fullscreen-target svg .node div,
        .dark #mermaid-fullscreen-target svg .node p,
        .dark [id*="mermaid-"][id*="-target"] svg .node text,
        .dark [id*="mermaid-"][id*="-target"] svg .node span,
        .dark [id*="mermaid-"][id*="-target"] svg .node div,
        .dark [id*="mermaid-"][id*="-target"] svg .node p {
            fill: #f4f4f5 !important;
            color: #f4f4f5 !important;
            font-weight: 700 !important;
        }
        .dark #mermaid-fullscreen-target svg .edgePath path,
        .dark #mermaid-fullscreen-target svg .flowchart-link,
        .dark [id*="mermaid-"][id*="-target"] svg .edgePath path,
        .dark [id*="mermaid-"][id*="-target"] svg .flowchart-link {
            stroke: #34d399 !important;
            stroke-width: 1.5px !important;
            fill: none !important;
        }
        .dark #mermaid-fullscreen-target svg .edgeLabel,
        .dark #mermaid-fullscreen-target svg .edgeLabel rect,
        .dark [id*="mermaid-"][id*="-target"] svg .edgeLabel,
        .dark [id*="mermaid-"][id*="-target"] svg .edgeLabel rect {
            fill: #09090b !important;
            background-color: #09090b !important;
            color: #f4f4f5 !important;
            stroke: #27272a !important;
        }
        .dark #mermaid-fullscreen-target svg marker path,
        .dark [id*="mermaid-"][id*="-target"] svg marker path {
            fill: #34d399 !important;
            stroke: #34d399 !important;
        }

        /* 2. ERD SCHEMAS (Dark Mode) */
        .dark #mermaid-fullscreen-target svg .er.entityBox,
        .dark #mermaid-fullscreen-target svg rect.entityBox,
        .dark [id*="mermaid-"][id*="-target"] svg .er.entityBox,
        .dark [id*="mermaid-"][id*="-target"] svg rect.entityBox {
            fill: #18181b !important;
            stroke: #10b981 !important;
            stroke-width: 1.5px !important;
        }
        .dark #mermaid-fullscreen-target svg rect.attributeBoxOdd,
        .dark [id*="mermaid-"][id*="-target"] svg rect.attributeBoxOdd {
            fill: #27272a !important;
            stroke: #3f3f46 !important;
            stroke-width: 1px !important;
        }
        .dark #mermaid-fullscreen-target svg rect.attributeBoxEven,
        .dark [id*="mermaid-"][id*="-target"] svg rect.attributeBoxEven {
            fill: #18181b !important;
            stroke: #3f3f46 !important;
            stroke-width: 1px !important;
        }
        .dark #mermaid-fullscreen-target svg text,
        .dark #mermaid-fullscreen-target svg .er.entityLabel,
        .dark #mermaid-fullscreen-target svg text.attributeBoxOdd,
        .dark #mermaid-fullscreen-target svg text.attributeBoxEven,
        .dark [id*="mermaid-"][id*="-target"] svg text,
        .dark [id*="mermaid-"][id*="-target"] svg .er.entityLabel,
        .dark [id*="mermaid-"][id*="-target"] svg text.attributeBoxOdd,
        .dark [id*="mermaid-"][id*="-target"] svg text.attributeBoxEven {
            fill: #f4f4f5 !important;
            color: #f4f4f5 !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .dark #mermaid-fullscreen-target svg path.er.relationshipLine,
        .dark [id*="mermaid-"][id*="-target"] svg path.er.relationshipLine {
            stroke: #34d399 !important;
            stroke-width: 1.5px !important;
        }
        .dark #mermaid-fullscreen-target svg rect.relationshipLabelBox,
        .dark [id*="mermaid-"][id*="-target"] svg rect.relationshipLabelBox {
            fill: #09090b !important;
            stroke: #27272a !important;
        }

        /* 3. GANTT CHARTS (Dark Mode) */
        .dark #mermaid-fullscreen-target svg rect.grid-row,
        .dark #mermaid-fullscreen-target svg .grid .grid-row,
        .dark #mermaid-fullscreen-target svg rect.section,
        .dark [id*="mermaid-"][id*="-target"] svg rect.grid-row,
        .dark [id*="mermaid-"][id*="-target"] svg .grid .grid-row,
        .dark [id*="mermaid-"][id*="-target"] svg rect.section {
            fill: #18181b !important;
            stroke: #27272a !important;
        }
        .dark #mermaid-fullscreen-target svg rect.section0,
        .dark #mermaid-fullscreen-target svg rect.section2,
        .dark [id*="mermaid-"][id*="-target"] svg rect.section0,
        .dark [id*="mermaid-"][id*="-target"] svg rect.section2 {
            fill: #202024 !important;
        }
        .dark #mermaid-fullscreen-target svg rect.task,
        .dark #mermaid-fullscreen-target svg rect.task0,
        .dark #mermaid-fullscreen-target svg rect.task1,
        .dark #mermaid-fullscreen-target svg rect.task2,
        .dark #mermaid-fullscreen-target svg rect.task3,
        .dark #mermaid-fullscreen-target svg .task,
        .dark [id*="mermaid-"][id*="-target"] svg rect.task,
        .dark [id*="mermaid-"][id*="-target"] svg rect.task0,
        .dark [id*="mermaid-"][id*="-target"] svg rect.task1,
        .dark [id*="mermaid-"][id*="-target"] svg rect.task2,
        .dark [id*="mermaid-"][id*="-target"] svg rect.task3,
        .dark [id*="mermaid-"][id*="-target"] svg .task {
            fill: #059669 !important;
            stroke: #10b981 !important;
            stroke-width: 1.5px !important;
        }
        .dark #mermaid-fullscreen-target svg rect.active,
        .dark [id*="mermaid-"][id*="-target"] svg rect.active {
            fill: #0284c7 !important;
            stroke: #38bdf8 !important;
        }
        .dark #mermaid-fullscreen-target svg rect.done,
        .dark [id*="mermaid-"][id*="-target"] svg rect.done {
            fill: #3f3f46 !important;
            stroke: #52525b !important;
        }
        .dark #mermaid-fullscreen-target svg rect.crit,
        .dark [id*="mermaid-"][id*="-target"] svg rect.crit {
            fill: #dc2626 !important;
            stroke: #f87171 !important;
        }
        .dark #mermaid-fullscreen-target svg .taskText,
        .dark #mermaid-fullscreen-target svg .taskTextOutsideRight,
        .dark #mermaid-fullscreen-target svg .taskTextOutsideLeft,
        .dark #mermaid-fullscreen-target svg .grid .tick text,
        .dark #mermaid-fullscreen-target svg text.titleText,
        .dark [id*="mermaid-"][id*="-target"] svg .taskText,
        .dark [id*="mermaid-"][id*="-target"] svg .taskTextOutsideRight,
        .dark [id*="mermaid-"][id*="-target"] svg .taskTextOutsideLeft,
        .dark [id*="mermaid-"][id*="-target"] svg .grid .tick text,
        .dark [id*="mermaid-"][id*="-target"] svg text.titleText {
            fill: #f4f4f5 !important;
            color: #f4f4f5 !important;
            font-weight: 700 !important;
            font-family: ui-monospace, monospace !important;
        }
        .dark #mermaid-fullscreen-target svg .grid .tick line,
        .dark [id*="mermaid-"][id*="-target"] svg .grid .tick line {
            stroke: #3f3f46 !important;
        }

        /* LIGHT MODE HIGH-CONTRAST CRISP FIDELITY */
        html:not(.dark) #mermaid-fullscreen-target svg,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg {
            color: #09090b !important;
            background-color: transparent !important;
        }

        /* 1. FLOWCHART NODES & CLUSTERS (Light Mode) */
        html:not(.dark) #mermaid-fullscreen-target svg .node rect,
        html:not(.dark) #mermaid-fullscreen-target svg .node polygon,
        html:not(.dark) #mermaid-fullscreen-target svg .node circle,
        html:not(.dark) #mermaid-fullscreen-target svg .node path,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node rect,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node polygon,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node circle,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node path {
            fill: #ffffff !important;
            stroke: #059669 !important;
            stroke-width: 1.5px !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .cluster rect,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .cluster rect {
            fill: #f8fafc !important;
            stroke: #cbd5e1 !important;
            stroke-width: 1.5px !important;
            stroke-dasharray: 4,4 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .cluster text,
        html:not(.dark) #mermaid-fullscreen-target svg .cluster span,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .cluster text,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .cluster span {
            fill: #0f172a !important;
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .node text,
        html:not(.dark) #mermaid-fullscreen-target svg .node span,
        html:not(.dark) #mermaid-fullscreen-target svg .node div,
        html:not(.dark) #mermaid-fullscreen-target svg .node p,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node text,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node span,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node div,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .node p {
            fill: #09090b !important;
            color: #09090b !important;
            font-weight: 700 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .edgePath path,
        html:not(.dark) #mermaid-fullscreen-target svg .flowchart-link,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .edgePath path,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .flowchart-link {
            stroke: #059669 !important;
            stroke-width: 1.5px !important;
            fill: none !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .edgeLabel,
        html:not(.dark) #mermaid-fullscreen-target svg .edgeLabel rect,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .edgeLabel,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .edgeLabel rect {
            fill: #ffffff !important;
            background-color: #ffffff !important;
            color: #09090b !important;
            stroke: #e4e4e7 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg marker path,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg marker path {
            fill: #059669 !important;
            stroke: #059669 !important;
        }

        /* 2. ERD SCHEMAS (Light Mode) */
        html:not(.dark) #mermaid-fullscreen-target svg .er.entityBox,
        html:not(.dark) #mermaid-fullscreen-target svg rect.entityBox,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .er.entityBox,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.entityBox {
            fill: #ffffff !important;
            stroke: #059669 !important;
            stroke-width: 1.5px !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.attributeBoxOdd,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.attributeBoxOdd {
            fill: #f8fafc !important;
            stroke: #e2e8f0 !important;
            stroke-width: 1px !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.attributeBoxEven,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.attributeBoxEven {
            fill: #ffffff !important;
            stroke: #e2e8f0 !important;
            stroke-width: 1px !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg text,
        html:not(.dark) #mermaid-fullscreen-target svg .er.entityLabel,
        html:not(.dark) #mermaid-fullscreen-target svg text.attributeBoxOdd,
        html:not(.dark) #mermaid-fullscreen-target svg text.attributeBoxEven,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg text,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .er.entityLabel,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg text.attributeBoxOdd,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg text.attributeBoxEven {
            fill: #09090b !important;
            color: #09090b !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg path.er.relationshipLine,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg path.er.relationshipLine {
            stroke: #059669 !important;
            stroke-width: 1.5px !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.relationshipLabelBox,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.relationshipLabelBox {
            fill: #ffffff !important;
            stroke: #e4e4e7 !important;
        }

        /* 3. GANTT CHARTS (Light Mode) */
        html:not(.dark) #mermaid-fullscreen-target svg rect.grid-row,
        html:not(.dark) #mermaid-fullscreen-target svg .grid .grid-row,
        html:not(.dark) #mermaid-fullscreen-target svg rect.section,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.grid-row,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .grid .grid-row,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.section {
            fill: #ffffff !important;
            stroke: #e4e4e7 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.section0,
        html:not(.dark) #mermaid-fullscreen-target svg rect.section2,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.section0,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.section2 {
            fill: #f8fafc !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.task,
        html:not(.dark) #mermaid-fullscreen-target svg rect.task0,
        html:not(.dark) #mermaid-fullscreen-target svg rect.task1,
        html:not(.dark) #mermaid-fullscreen-target svg rect.task2,
        html:not(.dark) #mermaid-fullscreen-target svg rect.task3,
        html:not(.dark) #mermaid-fullscreen-target svg .task,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.task,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.task0,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.task1,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.task2,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.task3,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .task {
            fill: #10b981 !important;
            stroke: #059669 !important;
            stroke-width: 1.5px !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.active,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.active {
            fill: #0284c7 !important;
            stroke: #0369a1 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.done,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.done {
            fill: #71717a !important;
            stroke: #52525b !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg rect.crit,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg rect.crit {
            fill: #ef4444 !important;
            stroke: #dc2626 !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .taskText,
        html:not(.dark) #mermaid-fullscreen-target svg .taskTextOutsideRight,
        html:not(.dark) #mermaid-fullscreen-target svg .taskTextOutsideLeft,
        html:not(.dark) #mermaid-fullscreen-target svg .grid .tick text,
        html:not(.dark) #mermaid-fullscreen-target svg text.titleText,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .taskText,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .taskTextOutsideRight,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .taskTextOutsideLeft,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .grid .tick text,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg text.titleText {
            fill: #09090b !important;
            color: #09090b !important;
            font-weight: 700 !important;
            font-family: ui-monospace, monospace !important;
        }
        html:not(.dark) #mermaid-fullscreen-target svg .grid .tick line,
        html:not(.dark) [id*="mermaid-"][id*="-target"] svg .grid .tick line {
            stroke: #d4d4d8 !important;
        }
    </style>
