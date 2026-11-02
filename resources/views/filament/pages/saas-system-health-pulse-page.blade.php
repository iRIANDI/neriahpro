<x-filament-panels::page>
    @php
        $isEn = ($locale ?? 'id') === 'en';
        $db = $pulse['database'] ?? [];
        $queue = $pulse['queue_workers'] ?? [];
        $disk = $pulse['storage_disk'] ?? [];
        $llm = $pulse['llm_orchestrator'] ?? [];
        $saas = $pulse['saas_commercial_counters'] ?? [];
    @endphp

    <div class="space-y-6 font-sans">
        <!-- Top Pulse Telemetry Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 tabular-nums">
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
                <div class="font-mono text-[11px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                    OVERALL PULSE STATUS
                </div>
                <div class="text-2xl font-black text-zinc-950 dark:text-white mt-1">
                    {{ $pulse['overall_status'] ?? 'HEALTHY' }}
                </div>
                <div class="font-mono text-xs text-zinc-700 dark:text-zinc-400 mt-1">
                    DB {{ $db['driver'] ?? 'pgsql' }} · {{ $db['latency_ms'] ?? 0 }} ms
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
                <div class="font-mono text-[11px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                    QUEUE &amp; DLQ HEALTH
                </div>
                <div class="text-2xl font-black text-zinc-950 dark:text-white mt-1">
                    {{ number_format((int) ($queue['pending_jobs'] ?? 0)) }} / {{ number_format((int) ($queue['failed_jobs'] ?? 0)) }}
                </div>
                <div class="font-mono text-xs text-zinc-700 dark:text-zinc-400 mt-1">
                    Pending / Failed Jobs ({{ $queue['status'] ?? 'HEALTHY' }})
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
                <div class="font-mono text-[11px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                    VPS DISK CAPACITY
                </div>
                <div class="text-2xl font-black text-zinc-950 dark:text-white mt-1">
                    {{ $disk['used_percent'] ?? 0 }}% Used
                </div>
                <div class="font-mono text-xs text-zinc-700 dark:text-zinc-400 mt-1">
                    Free: {{ $disk['free_gb'] ?? 0 }} GB / {{ $disk['total_gb'] ?? 0 }} GB
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
                <div class="font-mono text-[11px] font-black uppercase text-cyan-900 dark:text-cyan-300">
                    MULTI-LLM FAILOVER SHIELD
                </div>
                <div class="text-2xl font-black text-zinc-950 dark:text-white mt-1">
                    {{ (int) ($llm['configured_providers_count'] ?? 0) }} Active Keys
                </div>
                <div class="font-mono text-xs text-zinc-700 dark:text-zinc-400 mt-1">
                    Gemini: {{ !empty($llm['gemini_primary_configured']) ? 'READY' : 'FALLBACK' }} · Audit Chain: {{ $pulse['audit_chain']['valid'] ?? true ? 'INTACT' : 'ALERT' }}
                </div>
            </div>
        </div>

        <!-- 1-Click Non-Profit Impact Grant Queue -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <div class="font-mono text-xs font-black uppercase text-emerald-900 dark:text-emerald-300">
                        SAAS_MOD_04 // 1-CLICK NON-PROFIT / NGO / ACADEMIC IMPACT GRANT WORKFLOW
                    </div>
                    <h3 class="text-lg font-black text-zinc-950 dark:text-white">
                        {{ $isEn ? 'Impact Grant Verification Queue' : 'Antrean Verifikasi Hibah Non-Profit & Yayasan' }}
                    </h3>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-mono text-xs font-black tabular-nums">
                    Pending: {{ (int) ($saas['pending_impact_grants'] ?? 0) }}
                </span>
            </div>

            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left font-mono text-xs tabular-nums">
                    <thead class="sticky top-0 bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-3 py-2">Organisasi</th>
                            <th class="px-3 py-2">Tipe</th>
                            <th class="px-3 py-2">Dokumen Legalitas &amp; SK</th>
                            <th class="px-3 py-2">PIC Email &amp; E.164</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Voucher Hibah</th>
                            <th class="px-3 py-2 text-right">Aksi 1-Klik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingGrants as $grant)
                            <tr class="odd:bg-white dark:odd:bg-zinc-900 even:bg-zinc-50 dark:even:bg-zinc-950/60 border-b border-zinc-200 dark:border-zinc-800">
                                <td class="px-3 py-2 font-bold text-zinc-950 dark:text-white">{{ $grant->organization_name }}</td>
                                <td class="px-3 py-2">{{ $grant->organization_type }}</td>
                                <td class="px-3 py-2">
                                    <div class="font-bold text-zinc-950 dark:text-zinc-100">{{ $grant->legal_decree_number ?? '-' }}</div>
                                    @if(!empty($grant->private_document_path) && \Illuminate\Support\Facades\Storage::disk('private')->exists($grant->private_document_path))
                                        <a href="{{ route('admin.impact-grant.document', $grant->id) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-cyan-600 dark:text-cyan-400 hover:underline font-bold mt-0.5">
                                            📋 Lihat Dokumen
                                        </a>
                                    @else
                                        <span class="text-[10px] text-zinc-400">Tidak ada file</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $grant->pic_email }} · {{ $grant->pic_phone_country_code }}{{ $grant->pic_phone }}</td>
                                <td class="px-3 py-2 font-black">{{ $grant->status }}</td>
                                <td class="px-3 py-2 text-emerald-900 dark:text-emerald-300 font-bold">{{ $grant->granted_voucher_code ?? '-' }}</td>
                                <td class="px-3 py-2 text-right">
                                    @if($grant->status === 'PENDING_REVIEW')
                                        <button
                                            type="button"
                                            wire:click="approveImpactGrant('{{ $grant->id }}')"
                                            class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black uppercase rounded-none cursor-pointer"
                                        >
                                            ✓ Approve 1-Click
                                        </button>
                                    @else
                                        <span class="text-zinc-600 dark:text-zinc-400">VERIFIED</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-zinc-600 dark:text-zinc-400">
                                    {{ $isEn ? 'No Impact Grant applications submitted yet.' : 'Belum ada pengajuan hibah Non-Profit yang masuk.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Affiliate & Mitra Finder's Fee Ledger -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
            <div class="font-mono text-xs font-black uppercase text-cyan-900 dark:text-cyan-300">
                SAAS_MOD_02 // AFFILIATE (20% RETAIL) &amp; FINDER'S FEE (7.5% MITRA) LEDGER
            </div>
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left font-mono text-xs tabular-nums">
                    <thead class="bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-3 py-2">Referral Code</th>
                            <th class="px-3 py-2">Track</th>
                            <th class="px-3 py-2">Gross (IDR)</th>
                            <th class="px-3 py-2">Commission (IDR)</th>
                            <th class="px-3 py-2">Payout Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReferrals as $ref)
                            <tr class="odd:bg-white dark:odd:bg-zinc-900 even:bg-zinc-50 dark:even:bg-zinc-950/60 border-b border-zinc-200 dark:border-zinc-800">
                                <td class="px-3 py-2 font-black">{{ $ref->referral_code }}</td>
                                <td class="px-3 py-2">{{ $ref->conversion_track }} ({{ $ref->commission_rate_percent }}%)</td>
                                <td class="px-3 py-2">Rp {{ number_format((int) $ref->gross_transaction_idr, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 font-black text-emerald-900 dark:text-emerald-300">Rp {{ number_format((int) $ref->commission_amount_idr, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">{{ $ref->payout_status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-zinc-600 dark:text-zinc-400">
                                    {{ $isEn ? 'No affiliate conversions recorded yet.' : 'Belum ada konversi komisi afiliasi tercatat.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- v11.0 SEO/GEO IndexNow Authority Bar & Non-Production Webhook Chaos Console -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                <div class="font-mono text-xs font-black uppercase text-emerald-900 dark:text-emerald-300">
                    PRIMITIVE_SEO_GEO_AUTHORITY_ENGINE // /LLMS.TXT &amp; INDEXNOW PING
                </div>
                <p class="text-xs text-zinc-700 dark:text-zinc-300">
                    {{ $isEn
                        ? 'Flush Forever-Cached 12-Sector Multi-Graph Schema.org JSON-LD, /llms.txt & /llms-full.txt and push instant IndexNow notification to Bing / ChatGPT Search / Perplexity.'
                        : 'Reset cache permanen Multi-Graph Schema.org JSON-LD 12 Sektor, /llms.txt & /llms-full.txt lalu kirim ping instan IndexNow ke indeks Bing / ChatGPT Search / Perplexity.' }}
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        wire:click="triggerIndexNowPing"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-xs font-black uppercase rounded-none cursor-pointer"
                    >
                        🚀 {{ $isEn ? 'Flush Cache & Ping IndexNow' : 'Reset Cache & Ping IndexNow 1-Klik' }}
                    </button>
                    <a href="{{ url('/llms.txt') }}" target="_blank" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-mono text-xs font-black rounded-none">
                        ↗ /llms.txt
                    </a>
                    <a href="{{ url('/llms-full.txt') }}" target="_blank" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-mono text-xs font-black rounded-none">
                        ↗ /llms-full.txt
                    </a>
                </div>
            </div>

            @if(!app()->isProduction())
                <div class="p-5 bg-white dark:bg-zinc-900 border border-amber-400 dark:border-amber-700 rounded-none space-y-3">
                    <div class="font-mono text-xs font-black uppercase text-amber-950 dark:text-amber-300">
                        SANDBOX CHAOS CONSOLE ({{ strtoupper(app()->environment()) }} ONLY — LOCKED 403 IN PROD)
                    </div>
                    <p class="text-xs text-zinc-700 dark:text-zinc-300">
                        {{ $isEn
                            ? 'Simulate payment gateway webhook edge-cases locally without live credentials to verify idempotency and state transitions.'
                            : 'Uji skenario ekstrem webhook pembayaran secara lokal tanpa kredensial bank asli untuk memverifikasi idempotensi dan transisi status.' }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="runAdminSandboxWebhook('qris_payment_success')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-xs font-black rounded-none cursor-pointer">
                            ⚡ QRIS Success
                        </button>
                        <button type="button" wire:click="runAdminSandboxWebhook('payment_expired')" class="px-3 py-1.5 bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 font-mono text-xs font-black rounded-none cursor-pointer">
                            ⏱️ Expired Payment
                        </button>
                        <button type="button" wire:click="runAdminSandboxWebhook('duplicate_webhook_attack')" class="px-3 py-1.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 font-mono text-xs font-black rounded-none cursor-pointer">
                            🔄 2x Duplicate Attack
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- v11.0 Post-BAST 90-Day SLA Bug-Warranty vs Paid Delta Triage Queue -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <div class="font-mono text-xs font-black uppercase text-emerald-900 dark:text-emerald-300">
                        V11_ZERO_FAILURE_03 // POST-BAST 90-DAY CONTRACTUAL SLA BUG-WARRANTY VS PAID DELTA V1.1
                    </div>
                    <h3 class="text-lg font-black text-zinc-950 dark:text-white">
                        {{ $isEn ? 'Post-BAST SLA Maintenance & Scope-Lock Triage Queue' : 'Antrean Triase Garansi Bug SLA Pasca-BAST vs Addendum Fitur Baru' }}
                    </h3>
                </div>
                <span class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-mono text-xs font-black tabular-nums">
                    UU PDP Logs: {{ count($recentPrivacyRequests ?? []) }}
                </span>
            </div>

            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left font-mono text-xs tabular-nums">
                    <thead class="bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-3 py-2">Ticket #</th>
                            <th class="px-3 py-2">Judul Laporan</th>
                            <th class="px-3 py-2">Pelapor (E.164)</th>
                            <th class="px-3 py-2">Klasifikasi AI Scope</th>
                            <th class="px-3 py-2 text-right">Estimasi Biaya (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recentSlaTickets ?? []) as $tkt)
                            <tr class="odd:bg-white dark:odd:bg-zinc-900 even:bg-zinc-50 dark:even:bg-zinc-950/60 border-b border-zinc-200 dark:border-zinc-800">
                                <td class="px-3 py-2 font-black text-zinc-950 dark:text-white">{{ $tkt->ticket_number }}</td>
                                <td class="px-3 py-2 font-sans font-bold text-zinc-950 dark:text-zinc-100">{{ $tkt->issue_title }}</td>
                                <td class="px-3 py-2">{{ $tkt->reporter_email }} · {{ $tkt->reporter_phone_country_code }}{{ $tkt->reporter_phone }}</td>
                                <td class="px-3 py-2">
                                    @if($tkt->classification === 'BUG_WARRANTY_COVERED')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black">GARANSI BUG RP 0</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-700 font-black">PAID DELTA V1.1</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right font-black text-zinc-950 dark:text-white">
                                    Rp {{ number_format((int) ($tkt->estimated_delta_idr ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-zinc-600 dark:text-zinc-400">
                                    {{ $isEn ? 'No Post-BAST SLA tickets submitted yet.' : 'Belum ada tiket SLA Pasca-BAST yang diajukan.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>

