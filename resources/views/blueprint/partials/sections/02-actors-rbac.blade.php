@php 
    $isActorsImpacted = in_array('target_actors', $prd['_impacted_segments'] ?? []); 
    $isEn = ($isEn ?? false) || ($locale ?? 'id') === 'en';
@endphp
            <section id="section-2" class="bg-white dark:bg-zinc-900 border {{ $isActorsImpacted ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-800' }} p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target transition-all">
                <div class="flex flex-col gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center justify-between gap-2 cursor-pointer select-none" @click="toggleSection('section-2')">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">02</span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">{{ $isEn ? 'Users & Access Rights (Actors / RBAC)' : 'Pengguna & Hak Akses (Aktor / RBAC)' }}</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">{{ $isEn ? 'System Actors, User Roles & Permission Boundaries' : 'Aktor Sistem, Peran Pengguna & Batasan Otoritas' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!($isScopeLocked ?? false) || ($blueprint->project_status === 'Retail License') || (auth()->user()?->isSuperAdmin() ?? false))
                                <button 
                                    type="button" 
                                    @click.stop="openTweakModal('system_actors', '{{ $isEn ? 'Users & Access Rights (Actors / RBAC)' : 'Pengguna & Hak Akses (Aktor / RBAC)' }}')" 
                                    class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs border border-emerald-500/30 flex items-center gap-1.5 transition shrink-0 cursor-pointer"
                                    title="{{ $isEn ? 'Synchronize and deepen RBAC roles and permissions from grounded project documents' : 'Sinkronkan dan perdalam peran serta matriks izin RBAC dari Dokumen Acuan Proyek' }}"
                                >
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>{{ $isEn ? 'ELABORATE FROM GROUNDED CORPUS' : 'PERDALAM DARI DOKUMEN ACUAN' }}</span>
                                </button>
                            @endif
                            <button 
                                type="button" 
                                @click.stop="toggleSection('section-2')" 
                                class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700"
                                :title="sectionCollapses['section-2'] ? (isEn ? 'Expand this section' : 'Buka seksi ini') : (isEn ? 'Collapse this section' : 'Ciutkan seksi ini')"
                            >
                                <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-2'] ? (isEn ? 'EXPAND' : 'BUKA') : (isEn ? 'COLLAPSE' : 'CIUTKAN')"></span>
                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-2'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>
                    @if($isActorsImpacted)
                    <div class="flex items-start gap-2 p-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 text-xs font-mono text-amber-700 dark:text-amber-400 mt-1">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p x-html="locale === 'en' ? '<strong>NEEDS REVIEW:</strong> System actors segment is detected as <em>stale</em> due to upstream changes. Click TWEAK to adjust actors and RBAC permissions.' : '<strong>NEEDS REVIEW:</strong> Segmen aktor sistem terdeteksi <em>stale</em> karena perubahan di segmen hulu. Klik TWEAK untuk menyesuaikan aktor dan hak akses RBAC.'"></p>
                    </div>
                    @endif
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-2']" @click="toggleSection('section-2')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-4">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ $isEn ? 'Section 02 collapsed (Users & RBAC Access). Click to expand...' : 'Seksi 02 diciutkan (Pengguna & Hak Akses RBAC). Klik untuk membuka...' }}</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-2']" x-transition class="accordion-body">
                <div class="mb-4 text-xs font-mono text-zinc-500 dark:text-zinc-400">
                    TARGET_AUDIENCE: <div class="inline-block text-zinc-900 dark:text-zinc-100 font-bold">{!! \App\Support\BlueprintPdfHelper::formatWebPoints($blueprint->target_audiens ?? ($prd['executive_summary']['target_audience'] ?? '-'), false) !!}</div>
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($prd['system_actors'] ?? [] as $actor)
                        @php
                            $actorName = $actor['name'] ?? $actor['title'] ?? 'Aktor Sistem';
                            $actorRole = $actor['role'] ?? $actor['desc'] ?? 'Akses dan fungsi interaksi standar dalam sistem.';
                            $actorBadge = $actor['badge'] ?? 'ROLE';
                            $permissions = $actor['permissions'] ?? [];
                        @endphp
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-block px-2.5 py-1 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-900 dark:bg-zinc-800 text-white dark:text-zinc-100 rounded-none">
                                        {{ $actorName }}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                        {{ $actorBadge }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-sans mb-4">
                                    {{ $actorRole }}
                                </p>
                            </div>
                            @if(!empty($permissions))
                                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800/80">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase block mb-1.5 font-bold">{{ $isEn ? 'Authorization & Access Rights:' : 'Otorisasi & Hak Akses:' }}</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($permissions as $perm)
                                            <span class="text-[9px] font-mono px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                                {{ $perm }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                </div>
            </section>

            <!-- SECTION 3: SPESIFIKASI REKAYASA FITUR (DEEP VERTICAL SLICES & AGENT DIRECTIVE) -->
