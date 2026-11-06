            <section id="section-3" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-6">
                    <div class="flex items-center gap-2 cursor-pointer select-none" @click="toggleSection('section-3')">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">03</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Spesifikasi Rekayasa Fitur & Agent Task Matrix</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Dekomposisi vertikal per fitur: Frontend Anti-AI-Slop, Backend Keyset O(1) &amp; ULID, API Contracts, dan Agent Directive Prompt.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 no-print">
                        @if(!($isScopeLocked ?? false) || ($blueprint->project_status === 'Retail License') || (auth()->user()?->isSuperAdmin() ?? false))
                            <button 
                                type="button" 
                                @click="openTweakModal('features', 'Seluruh Fitur MVP (Fase 1)')" 
                                class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs border border-emerald-500/30 flex items-center gap-1.5 transition cursor-pointer"
                                title="Perdalam dan sinkronkan seluruh fitur dari Dokumen Acuan Proyek (MarkItDown Corpus)"
                            >
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>PERDALAM DARI DOKUMEN ACUAN</span>
                            </button>
                            <button type="button" @click="taskEditorOpen = true" class="px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-mono font-bold border border-amber-500/30 flex items-center gap-1.5 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>ELABORASI TASK</span>
                            </button>
                        @else
                            <div class="px-3 py-1.5 bg-zinc-800 text-zinc-400 text-xs font-mono font-bold border border-zinc-700 flex items-center gap-1.5 cursor-not-allowed" title="Dokumen ini telah ditandatangani secara digital dengan integritas SHA-256 (Scope Freeze). Setiap perubahan task harus melalui Addendum.">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>SCOPE FREEZE (TERKUNCI)</span>
                            </div>
                        @endif
                        <button type="button" onclick="copyFullPrdMarkdown(this)" class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono font-bold border border-emerald-500/30 flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN SEMUA PROMPT AGENT</span>
                        </button>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-3')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700"
                            :title="sectionCollapses['section-3'] ? 'Buka seksi ini' : 'Ciutkan seksi ini'"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-3'] ? 'BUKA' : 'CIUTKAN'"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-3'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-3']" @click="toggleSection('section-3')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Seksi 03 diciutkan (Spesifikasi Rekayasa Fitur &amp; Agent Task Matrix). Klik untuk membuka...</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-3']" x-transition class="accordion-body">

                <!-- High-Level MVP Scope Summary -->
                <div class="grid md:grid-cols-2 gap-4 mb-8">
                    <!-- MVP Phase 1 -->
                    <div class="border border-emerald-500/40 bg-emerald-500/5 p-4 rounded-none">
                        <div class="flex items-center justify-between gap-2 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-bold uppercase tracking-wider mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500 inline-block"></span>
                                <h3>Fitur Wajib (Fase 1 - MVP Peluncuran)</h3>
                            </div>
                            <span class="px-1.5 py-0.5 bg-emerald-500 text-black text-[10px] font-bold">{{ count($prd['features']['mvp_phase1'] ?? []) }} FITUR</span>
                        </div>
                        <ul class="space-y-2">
                            @foreach($prd['features']['mvp_phase1'] ?? [] as $fitur)
                                @php
                                    $craftedFitur = \App\Services\BlueprintDiscoveryService::polishFeatureSpecification($fitur, $blueprint->domain_category ?? 'custom_portal', $isEn ?? false);
                                @endphp
                                <li class="text-xs bg-white dark:bg-zinc-900 p-2.5 border border-emerald-500/20 rounded-none">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $craftedFitur['title'] ?? '-' }}</div>
                                    <div class="text-zinc-500 dark:text-zinc-400 mt-0.5 font-sans text-[11px]">{{ $craftedFitur['desc'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Phase 2 Roadmap -->
                    <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-none">
                        <div class="flex items-center justify-between gap-2 text-zinc-600 dark:text-zinc-400 font-mono text-xs font-bold uppercase tracking-wider mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-zinc-400 inline-block"></span>
                                <h3>Fitur Tambahan (Fase 2 - Roadmap)</h3>
                            </div>
                            <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[10px] font-bold">{{ count($prd['features']['phase2_roadmap'] ?? []) }} FITUR</span>
                        </div>
                        @if(empty($prd['features']['phase2_roadmap']))
                            <p class="text-xs text-zinc-400 italic font-mono p-3">Belum ada fitur susulan. Fokus 100% pada rilis Fase 1 MVP.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($prd['features']['phase2_roadmap'] as $fitur)
                                    @php
                                        $craftedFitur = \App\Services\BlueprintDiscoveryService::polishFeatureSpecification($fitur, $blueprint->domain_category ?? 'custom_portal', $isEn ?? false);
                                    @endphp
                                    <li class="text-xs bg-white dark:bg-zinc-900 p-2.5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $craftedFitur['title'] ?? '-' }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400 mt-0.5 font-sans text-[11px]">{{ $craftedFitur['desc'] ?? '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- MODULE: CHANGE REQUEST (CR) & ADDENDUM -->
                <div class="mb-8 p-5 bg-amber-500/10 border-2 border-amber-500/50 flex flex-col gap-4 rounded-none">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                            <h4 class="text-amber-600 dark:text-amber-500 font-bold font-mono text-sm uppercase">🚀 Modul Change Request (CR) / Addendum</h4>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs font-sans mt-1 max-w-2xl">
                                {{ $isEn ? 'Project already in progress or scope frozen? Request additional features anytime. Neriah Pro Team will estimate Story Points (SP) and issue a mini-SPK.' : 'Proyek sudah berjalan atau scope terkunci? Jangan khawatir. Anda bisa meminta fitur tambahan (Addendum) kapan saja. Tim Neriah Pro akan menghitung estimasi Story Points (SP) dan menerbitkan mini-SPK.' }}
                            </p>
                        </div>
                        <button type="button" @click="crModalOpen = true" class="px-5 py-3 bg-amber-500 hover:bg-amber-400 text-black font-black uppercase text-xs transition border border-amber-600 shadow-[2px_2px_0px_#b45309] active:shadow-[0px_0px_0px_#b45309] active:translate-y-[2px] whitespace-nowrap cursor-pointer">
                            {{ $isEn ? '+ REQUEST ADDENDUM' : '+ AJUKAN FITUR TAMBAHAN' }}
                        </button>
                    </div>

                    @php
                        try {
                            $crIssues = \Illuminate\Support\Facades\Schema::hasTable('project_issues')
                                ? $blueprint->issues()->where('type', 'change_request')->latest()->get()
                                : collect();
                        } catch (\Throwable $e) {
                            $crIssues = collect();
                        }
                    @endphp
                    @if($crIssues->count() > 0)
                        <div class="mt-4 border-t border-amber-500/20 pt-4 space-y-3">
                            <h5 class="font-mono text-[10px] uppercase font-bold text-amber-700 dark:text-amber-500 tracking-wider">Draft & Active Change Requests:</h5>
                            @foreach($crIssues as $cr)
                                <div class="bg-white/50 dark:bg-zinc-900/50 border border-amber-500/30 p-3 rounded-none flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 {{ $cr->status === 'open' ? 'bg-amber-500 text-black' : 'bg-emerald-500 text-black' }} font-bold font-mono text-[9px] uppercase">{{ $cr->status }}</span>
                                            <span class="font-mono text-[10px] text-zinc-500">{{ $cr->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <h6 class="font-bold text-sm text-zinc-900 dark:text-white">{{ $isEn ? ($cr->title['en'] ?? '') : ($cr->title['id'] ?? '') }}</h6>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 font-sans mt-1">{{ $isEn ? ($cr->description['en'] ?? '') : ($cr->description['id'] ?? '') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="block font-mono text-[10px] text-zinc-500">Estimasi Biaya:</span>
                                        <span class="font-bold text-sm text-zinc-900 dark:text-white">{{ $cr->status === 'open' ? 'Menunggu Penilaian' : 'Tersedia di Dashboard' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Deep Vertical Slice Engineering Hub -->
                @php
                    $mvpEngineeringSpecs = $prd['features']['mvp_phase1'] ?? ($prd['engineering_specs']['mvp_specs'] ?? []);
                @endphp
                @if(!empty($mvpEngineeringSpecs))
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>DEKOMPOSISI VERTIKAL PER FITUR (SIAP OVER KE AI CODE AGENT)</span>
                        </div>

                        @foreach($mvpEngineeringSpecs as $specIdx => $spec)
                            @php
                                $featKey = $spec['feature_id'] ?? ($spec['id'] ?? ('FEAT-SPEC-' . $specIdx));
                                $spec = \App\Services\BlueprintDiscoveryService::polishFeatureSpecification($spec, $blueprint->domain_category ?? 'custom_portal', $isEn ?? false);
                                $craftedSpec = $spec;
                            @endphp
                            <div 
                                id="card-feat-{{ $specIdx }}"
                                x-data="{ specTab: &apos;gherkin&apos;, expanded: true }" 
                                class="bg-zinc-50 dark:bg-zinc-950 border-2 rounded-none transition prd-scroll-target"
                                :class="isStepCompleted('{{ $featKey }}') ? 'border-emerald-500/80 shadow-xs' : 'border-zinc-200 dark:border-zinc-800'"
                            >
                                <!-- Feature Spec Header Bar -->
                                <div class="p-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start sm:items-center gap-3">
                                        <span class="px-2 py-0.5 font-mono font-bold text-xs uppercase tracking-wider" :class="isStepCompleted('{{ $featKey }}') ? 'bg-emerald-500 text-black' : 'bg-zinc-900 dark:bg-zinc-800 text-white'">
                                            {{ $featKey }}
                                        </span>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight">{{ $craftedSpec['title'] ?? ($spec['title'] ?? 'Spesifikasi Fitur') }}</h3>
                                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                    {{ $spec['category'] ?? 'CORE DOMAIN' }}
                                                </span>
                                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold">
                                                    {{ $spec['complexity_label'] ?? 'Standard (3 SP)' }}
                                                </span>
                                                <span class="px-2 py-0.5 bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30 text-[10px] font-mono font-bold">
                                                    {{ $spec['sprint_phase'] ?? 'Sprint 1' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 no-print self-end sm:self-auto">
                                        <!-- Step Verification Toggle in Feature Card -->
                                        <button 
                                            type="button" 
                                            @click="toggleStepCompleted('{{ $featKey }}', '{{ addslashes($craftedSpec['title'] ?? ($spec['title'] ?? '')) }}')"
                                            :class="isStepCompleted('{{ $featKey }}') ? 'bg-emerald-500 text-black border-emerald-500 font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 border-zinc-300 dark:border-zinc-700'"
                                            class="px-2.5 py-1 text-xs font-mono border flex items-center gap-1.5 transition rounded-none"
                                            title="Tandai Status Pengerjaan Fitur Ini di Cockpit Sprint"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted('{{ $featKey }}') ? 'bg-black' : 'bg-zinc-400'"></span>
                                            <span x-text="isStepCompleted('{{ $featKey }}') ? '✓ VERIFIED & COMMITTED' : 'TANDAI SELESAI'"></span>
                                        </button>

                                        <button 
                                            type="button" 
                                            onclick="copyFeaturePrompt(this, 'prompt-code-{{ $specIdx }}')" 
                                            class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono font-bold border border-emerald-500/30 flex items-center gap-1 transition"
                                            title="Salin Prompt Directive Khusus Fitur Ini"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            <span>SALIN PROMPT FITUR</span>
                                        </button>
                                        @if(!($isScopeLocked ?? false) || ($blueprint->project_status === 'Retail License') || (auth()->user()?->isSuperAdmin() ?? false))
                                            <button 
                                                type="button" 
                                                @click="openTweakFeature('{{ $featKey }}', {{ $specIdx }}, '{{ addslashes($craftedSpec['title'] ?? ($spec['title'] ?? '')) }}')" 
                                                class="px-2.5 py-1 bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 text-xs font-mono font-bold border border-sky-500/30 flex items-center gap-1 transition cursor-pointer"
                                                title="Perdalam rincian Gherkin, edge cases, dan Acceptance Criteria fitur ini dari Dokumen Acuan"
                                            >
                                                <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                <span>PERDALAM FITUR</span>
                                            </button>
                                        @endif
                                        <button 
                                            type="button" 
                                            @click="expanded = !expanded" 
                                            class="p-1 text-zinc-400 hover:text-zinc-200"
                                            title="Buka / Tutup Detail"
                                        >
                                            <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Spec Tab Body -->
                                <div x-show="expanded" class="p-4 sm:p-5">
                                    <!-- User Story Pill -->
                                    <div class="p-3 bg-zinc-100 dark:bg-zinc-900 border-l-4 border-emerald-500 font-mono text-xs text-zinc-800 dark:text-zinc-200 mb-4">
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold block mb-0.5">USER STORY:</span>
                                        &quot;{{ $craftedSpec['user_story'] ?? ($spec['user_story'] ?? '') }}&quot;
                                    </div>

                                    @if(!empty($spec['target_files']))
                                        <!-- Bounded Target Files for AI Agents -->
                                        <div class="p-3 bg-zinc-100 dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800 font-mono text-xs mb-4">
                                            <div class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                <span>BOUNDED TARGET FILES (ISOLASI RUANG LINGKUP AGENT):</span>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($spec['target_files'] as $tf)
                                                    @php
                                                        $displayTf = \App\Services\PrdGeneratorService::sanitizeFilePath($tf, $craftedSpec['title'] ?? ($spec['title'] ?? ''), $spec['related_table'] ?? null);
                                                    @endphp
                                                    <span class="px-2 py-0.5 bg-white dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 text-[11px] border border-zinc-300 dark:border-zinc-800 select-all font-mono">
                                                        {{ $displayTf }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Navigation Sub-Tabs -->
                                    <div class="flex flex-wrap items-center gap-1 font-mono text-xs border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-4 no-print">
                                        <button 
                                            @click="specTab = &apos;gherkin&apos;" 
                                            :class="specTab === &apos;gherkin&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            1. GHERKIN &amp; ACCEPTANCE CRITERIA
                                        </button>
                                        <button 
                                            @click="specTab = &apos;frontend&apos;" 
                                            :class="specTab === &apos;frontend&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            2. FRONTEND (ANTI-AI-SLOP)
                                        </button>
                                        <button 
                                            @click="specTab = &apos;backend&apos;" 
                                            :class="specTab === &apos;backend&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            3. BACKEND (KEYSET &amp; ULID)
                                        </button>
                                        <button 
                                            @click="specTab = &apos;integration&apos;" 
                                            :class="specTab === &apos;integration&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            4. API &amp; KONTRAK
                                        </button>
                                        <button 
                                            @click="specTab = &apos;prompt&apos;" 
                                            :class="specTab === &apos;prompt&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition"
                                        >
                                            5. PROMPT AGENT DIRECTIVE
                                        </button>
                                        <button 
                                            @click="specTab = &apos;uat&apos;" 
                                            :class="specTab === &apos;uat&apos; ? &apos;bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold&apos; : &apos;bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800&apos;"
                                            class="px-3 py-1 border border-zinc-200 dark:border-zinc-800 transition flex items-center gap-1.5"
                                        >
                                            <span class="w-1.5 h-1.5 bg-sky-500 rounded-none animate-pulse block"></span>
                                            6. UAT &amp; FEEDBACK THREAD
                                        </button>
                                    </div>

                                    <!-- Tab 1: Gherkin -->
                                    <div x-show="specTab === &apos;gherkin&apos;" class="space-y-3 font-mono text-xs">
                                        @php
                                            $normalizedAc = \App\Services\PrdGeneratorService::normalizeAcceptanceCriteria(
                                                $spec['acceptance_criteria'] ?? [], 
                                                $spec['title'] ?? '', 
                                                $spec['user_story'] ?? ''
                                            );
                                        @endphp
                                        @foreach($normalizedAc as $acIdx => $ac)
                                            <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                                <div class="font-bold text-zinc-900 dark:text-white flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 bg-emerald-500 inline-block"></span>
                                                        <span class="uppercase tracking-tight text-xs">{{ $ac['scenario'] ?? ('Skenario ' . ($acIdx + 1)) }}</span>
                                                    </div>
                                                    <span class="text-[9px] font-mono px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase font-bold tracking-wider">
                                                        GHERKIN VERIFIED
                                                    </span>
                                                </div>
                                                <div class="pl-3 space-y-1.5 text-zinc-700 dark:text-zinc-300 text-[11px] leading-relaxed border-l-2 border-emerald-500/40 font-mono">
                                                    <div><strong class="text-emerald-600 dark:text-emerald-400 font-black">GIVEN:</strong> {{ (!empty($ac['given']) && $ac['given'] !== '-') ? $ac['given'] : \App\Services\PrdGeneratorService::deriveGivenCondition($ac['scenario'] ?? '', $spec['title'] ?? 'Fitur', $acIdx) }}</div>
                                                    <div><strong class="text-sky-600 dark:text-sky-400 font-black">WHEN:</strong> {{ (!empty($ac['when']) && $ac['when'] !== '-') ? $ac['when'] : \App\Services\PrdGeneratorService::deriveWhenCondition($ac['scenario'] ?? '', $spec['title'] ?? 'Fitur', $acIdx) }}</div>
                                                    <div><strong class="text-amber-600 dark:text-amber-400 font-black">THEN:</strong> {{ (!empty($ac['then']) && $ac['then'] !== '-') ? $ac['then'] : \App\Services\PrdGeneratorService::deriveThenCondition($ac['scenario'] ?? '', $spec['title'] ?? 'Fitur', $acIdx) }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Tab 2: Frontend Tasks -->
                                    @php
                                        $feData = $spec['frontend'] ?? [];
                                        $feComponents = is_array($feData['components'] ?? null) ? implode(', ', $feData['components']) : ($feData['components'] ?? 'Blade / Alpine');
                                    @endphp
                                    <div x-show="specTab === &apos;frontend&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                <strong class="text-zinc-900 dark:text-white uppercase">KOMPONEN &amp; TAMPILAN ANTARMUKA</strong>
                                                <span class="text-[10px] px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 font-bold border border-zinc-200 dark:border-zinc-700">
                                                    {{ $feComponents }}
                                                </span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <span class="text-zinc-400 text-[10px] block font-bold">DAFTAR TASK FRONTEND:</span>
                                                <ul class="space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                                    @foreach((array)($feData['tasks'] ?? []) as $feTask)
                                                        <li class="flex items-start gap-1.5">
                                                            <span class="text-emerald-500 font-bold">&bull;</span>
                                                            <span>{{ $feTask }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-zinc-400 text-[10px] block font-bold mb-0.5">STATE KELENGKAPAN (UI STATES):</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $feData['states'] ?? 'Default, Loading Skeleton, Empty State, Error Toast' }}</span>
                                                </div>
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-emerald-500 text-[10px] block font-bold mb-0.5">ANTI-AI-SLOP DIRECTIVE:</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $feData['anti_ai_slop'] ?? 'Palet Zinc monokrom dengan aksen tajam. Dilarang window.alert() / prompt().' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 3: Backend Tasks -->
                                    @php
                                        $beData = $spec['backend'] ?? [];
                                        $beTargets = is_array($beData['target_files'] ?? null) ? implode(', ', $beData['target_files']) : ($beData['target_files'] ?? 'app/Models, app/Actions');
                                    @endphp
                                    <div x-show="specTab === &apos;backend&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                <strong class="text-zinc-900 dark:text-white uppercase">LOGIKA BISNIS &amp; PERSISTENSI DATA</strong>
                                                <span class="text-[10px] px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-sky-600 dark:text-sky-400 font-bold border border-zinc-200 dark:border-zinc-700">
                                                    {{ $beTargets }}
                                                </span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <span class="text-zinc-400 text-[10px] block font-bold">DAFTAR TASK BACKEND:</span>
                                                <ul class="space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                                    @foreach((array)($beData['tasks'] ?? []) as $beTask)
                                                        <li class="flex items-start gap-1.5">
                                                            <span class="text-sky-500 font-bold">&bull;</span>
                                                            <span>{{ $beTask }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-amber-500 text-[10px] block font-bold mb-0.5">STANDAR DATABASE (STRICT ULID):</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $beData['ulid_migration_rules'] ?? 'Wajib gunakan ->ulid("id")->primary() (VARCHAR(26)). Dilarang AUTO_INCREMENT.' }}</span>
                                                </div>
                                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <span class="text-emerald-500 text-[10px] block font-bold mb-0.5">QUERY STRATEGY O(1):</span>
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $beData['query_strategy'] ?? 'Keyset Cursor Pagination cursorPaginate() dengan ->orderBy("id", "asc").' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 4: API & Integration Tasks -->
                                    @php
                                        $intData = $spec['integration'] ?? [];
                                        $intMiddleware = is_array($intData['middleware'] ?? null) ? implode(', ', $intData['middleware']) : ($intData['middleware'] ?? 'web');
                                    @endphp
                                    <div x-show="specTab === &apos;integration&apos;" class="space-y-3 font-mono text-xs">
                                        <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">POST</span>
                                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $intData['endpoint'] ?? '/api/v1/resource' }}</span>
                                                </div>
                                                <span class="text-[10px] text-zinc-400">MIDDLEWARE: <strong class="text-zinc-200">{{ $intMiddleware }}</strong></span>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-zinc-400 text-[10px] block font-bold">TASK INTEGRASI:</span>
                                                <ul class="space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                                    @foreach((array)($intData['tasks'] ?? []) as $intTask)
                                                        <li class="flex items-start gap-1.5">
                                                            <span class="text-emerald-500 font-bold">&check;</span>
                                                            <span>{{ $intTask }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <div class="text-[10px] text-zinc-400 font-bold mb-1">REQUEST SCHEMA:</div>
                                                    <pre class="bg-black/60 p-2 text-[10px] text-zinc-300 overflow-x-auto select-all leading-tight">{{ $intData['request_schema'] ?? '{}' }}</pre>
                                                </div>
                                                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                                    <div class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold mb-1">RESPONSE SUCCESS (200/201):</div>
                                                    <pre class="bg-zinc-100 dark:bg-black/60 p-2 text-[10px] text-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 overflow-x-auto select-all leading-tight">{{ $intData['response_schema'] ?? '{}' }}</pre>
                                                </div>
                                            </div>
                                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 pt-1">
                                                IDEMPOTENCY POLICY: <span class="text-zinc-700 dark:text-zinc-300 font-bold">X-Idempotency-Key Header Mandatory</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 5: AI Code Agent Directive Prompt -->
                                    <div x-show="specTab === 'prompt'" class="space-y-3 font-mono text-xs">
                                        <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                                                <span class="text-emerald-700 dark:text-emerald-400 font-bold uppercase text-[11px]">Prompt Directive Siap Di-Paste ke Cursor Composer / Claude Code / Antigravity:</span>
                                                <button 
                                                    type="button" 
                                                    onclick="copyFeaturePrompt(this, 'prompt-code-{{ $specIdx }}')" 
                                                    class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] hover:bg-emerald-400 transition"
                                                >
                                                    SALIN PROMPT
                                                </button>
                                            </div>
                                            <pre id="prompt-code-{{ $specIdx }}" class="bg-zinc-950 p-3 text-[11px] text-emerald-300/90 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap font-mono border border-zinc-800">{{ $spec['code_agent_directive'] ?? ($spec['agent_directive_prompt'] ?? '') }}</pre>
                                        </div>
                                    </div>
                                    <!-- Tab 6: UAT & Thread -->
                                    @php
                                        // Retrieve issues for this specific feature
                                        try {
                                            $featureIssues = \Illuminate\Support\Facades\Schema::hasTable('project_issues')
                                                ? $blueprint->issues()->where('feature_id', $featKey)->with('comments')->latest()->get()
                                                : collect();
                                        } catch (\Throwable $e) {
                                            $featureIssues = collect();
                                        }
                                    @endphp
                                    <div x-show="specTab === 'uat'" class="space-y-4 font-mono text-xs" style="display: none;">
                                        <!-- Micro Chat / Thread Container -->
                                        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                                            <div class="p-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                                <div class="flex items-center gap-2 text-zinc-800 dark:text-zinc-200 font-bold uppercase tracking-wider">
                                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                                                    {{ $isEn ? 'Feature QA Thread' : 'Thread QA Fitur' }}
                                                </div>
                                                <span class="px-2 py-0.5 bg-amber-500 text-black font-bold text-[9px] uppercase">
                                                    {{ $isEn ? 'LIVE SYNC' : 'SINKRONISASI LIVE' }}
                                                </span>
                                            </div>
                                            
                                            <!-- Messages Area -->
                                            <div class="p-4 bg-zinc-50 dark:bg-zinc-950 min-h-[150px] max-h-[300px] overflow-y-auto custom-prd-scrollbar space-y-4">
                                                @if($featureIssues->isEmpty())
                                                    <div class="text-center text-zinc-400 dark:text-zinc-500 text-[10px] py-4">
                                                        {{ $isEn ? 'No feedback recorded yet. Be the first to start the thread.' : 'Belum ada tiket atau masukan. Mulai thread QA pertama.' }}
                                                    </div>
                                                @else
                                                    @foreach($featureIssues as $issue)
                                                        @php
                                                            $issueTitle = is_array($issue->title) ? ($issue->title[app()->getLocale()] ?? $issue->title['id'] ?? '') : $issue->title;
                                                            $issueDesc = is_array($issue->description) ? ($issue->description[app()->getLocale()] ?? $issue->description['id'] ?? '') : $issue->description;
                                                        @endphp
                                                        <!-- Original Issue -->
                                                        <div class="flex gap-3">
                                                            <div class="w-6 h-6 bg-zinc-200 dark:bg-zinc-800 rounded-none shrink-0 flex items-center justify-center font-bold text-[10px] text-zinc-500">
                                                                {{ substr($issue->user?->name ?? 'U', 0, 1) }}
                                                            </div>
                                                            <div class="flex-1 space-y-1">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $issue->user?->name ?? 'Customer' }}</span>
                                                                    <span class="text-[9px] text-zinc-400">{{ $issue->created_at->diffForHumans() }}</span>
                                                                    @if($issue->type === 'bug')
                                                                        <span class="px-1.5 py-0.5 bg-red-500/10 text-red-500 text-[9px] font-bold">BUG</span>
                                                                    @else
                                                                        <span class="px-1.5 py-0.5 bg-sky-500/10 text-sky-500 text-[9px] font-bold">FEEDBACK</span>
                                                                    @endif
                                                                </div>
                                                                <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                                                    <strong class="block mb-1 text-zinc-900 dark:text-white">{{ $issueTitle }}</strong>
                                                                    {{ $issueDesc }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Comments -->
                                                        @foreach($issue->comments as $comment)
                                                            @php
                                                                $commentContent = is_array($comment->content) ? ($comment->content[app()->getLocale()] ?? $comment->content['id'] ?? '') : $comment->content;
                                                                $isOwner = $comment->user_id === $blueprint->user_id;
                                                            @endphp
                                                            <div class="flex gap-3 ml-8">
                                                                <div class="w-6 h-6 {{ $isOwner ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500' : 'bg-emerald-500 text-black' }} rounded-none shrink-0 flex items-center justify-center font-bold text-[10px]">
                                                                    {{ $isOwner ? substr($comment->user?->name ?? 'C', 0, 1) : 'S' }}
                                                                </div>
                                                                <div class="flex-1 space-y-1">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="font-bold text-zinc-900 dark:text-white">{{ $isOwner ? ($comment->user?->name ?? 'Customer') : 'Studio Team' }}</span>
                                                                        <span class="text-[9px] text-zinc-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                    <div class="p-2.5 {{ $isOwner ? 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300' : 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/50 text-emerald-900 dark:text-emerald-100' }} border">
                                                                        {{ $commentContent }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        <!-- Reply Form for Issue -->
                                                        <div class="ml-8 mt-2">
                                                            <form class="flex gap-2" onsubmit="event.preventDefault(); submitFeatureComment('{{ $issue->id }}', '{{ $blueprint->slug }}', this);">
                                                                <input type="text" name="content" class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 px-2 py-1.5 text-[10px] focus:border-emerald-500 focus:ring-0 rounded-none transition" placeholder="{{ $isEn ? 'Reply to this thread...' : 'Balas di thread ini...' }}" required>
                                                                <button type="submit" class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold uppercase tracking-wider transition rounded-none shrink-0 cursor-pointer text-[10px]">
                                                                    {{ $isEn ? 'REPLY' : 'BALAS' }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                        <hr class="border-zinc-200 dark:border-zinc-800 my-4">
                                                    @endforeach
                                                @endif
                                            </div>

                                            <!-- Input Area (New Issue) -->
                                            <div class="p-3 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
                                                <form class="flex gap-2" onsubmit="event.preventDefault(); submitFeatureFeedback('{{ $featKey }}', '{{ $blueprint->slug }}', this);">
                                                    <input type="text" name="title" class="w-full bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-0 rounded-none transition" placeholder="{{ $isEn ? 'Type new feedback or issue title...' : 'Ketik judul tiket atau masukan baru...' }}" required>
                                                    <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase tracking-wider transition rounded-none shrink-0 cursor-pointer">
                                                        {{ $isEn ? 'START THREAD' : 'BUAT TIKET' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            </section>

            <!-- SECTION 3.5: PUSAT ORKESTRASI & SPRINT COCKPIT AI CODING AGENT -->
