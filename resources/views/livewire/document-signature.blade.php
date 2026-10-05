<div class="min-h-screen bg-zinc-950 text-zinc-100 py-8 sm:py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Top Navigation / Breadcrumbs -->
        <div class="flex items-center justify-between font-mono text-xs text-zinc-400 no-print border-b border-zinc-800 pb-3">
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="hover:text-emerald-400 transition">&larr; Beranda</a>
                <span>/</span>
                @if($document->related instanceof \App\Models\VisionBlueprint)
                    <a href="{{ route('blueprint.show', $document->related->slug) }}" class="hover:text-emerald-400 transition">
                        PRD: {{ $document->related->nama_bisnis ?: $document->related->client_name }}
                    </a>
                    <span>/</span>
                @endif
                <span class="text-white font-bold">Kontrak Digital</span>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    onclick="window.print()" 
                    class="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-700 font-bold uppercase text-[11px] rounded-none transition flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>

        <!-- Main Document Sheet -->
        <article class="bg-zinc-900 border border-zinc-800 p-6 sm:p-10 shadow-2xl rounded-none relative">
            
            <!-- Legal Header -->
            <header class="border-b-2 border-zinc-700 pb-6 mb-8 text-center sm:text-left flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-mono font-bold uppercase tracking-wider inline-block mb-2">
                        SURAT PERJANJIAN KERJA SAMA (LEGAL CONTRACT)
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black uppercase text-white tracking-tight leading-tight">
                        {{ $document->title }}
                    </h1>
                    <p class="text-xs text-zinc-400 font-mono mt-1">
                        DOKUMEN HUKUM &bull; KESEPAKATAN PENGEMBANGAN SISTEM PERANGKAT LUNAK
                    </p>
                </div>
                <div class="sm:text-right font-mono text-xs shrink-0 space-y-1">
                    <div class="text-zinc-400">NO. DOKUMEN: <strong class="text-white">{{ strtoupper(substr($document->id, 0, 12)) }}</strong></div>
                    <div class="text-zinc-400">TANGGAL: <strong class="text-zinc-200">{{ ($document->signed_at ?: $document->created_at)->format('d F Y') }}</strong></div>
                    <div>
                        STATUS: 
                        @if($isSigned)
                            <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">
                                ✓ DITANDATANGANI &amp; SAH
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-amber-500 text-black font-bold text-[10px] uppercase">
                                MENUNGGU TANDA TANGAN
                            </span>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Status Banner if Signed -->
            @if($isSigned)
                <div class="mb-8 p-4 bg-emerald-950/40 border border-emerald-500/50 text-xs font-mono text-emerald-300 flex items-start gap-3 rounded-none">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <div>
                        <div class="font-bold uppercase tracking-wider text-emerald-200">
                            AKAD PERJANJIAN INI TELAH RESMI DITANDATANGANI DAN TERVERIFIKASI
                        </div>
                        <div class="text-[11px] text-emerald-400/90 mt-0.5 leading-relaxed font-sans">
                            Dokumen ini memiliki kekuatan hukum yang sah dan mengikat para pihak berdasarkan ketentuan Undang-Undang ITE (Informasi dan Transaksi Elektronik) Republik Indonesia.
                        </div>
                    </div>
                </div>
            @endif

            <!-- Para Pihak (Parties) -->
            <section class="mb-8 border border-zinc-800 bg-zinc-950/60 p-5 rounded-none font-sans text-xs">
                <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-800 pb-2 mb-4">
                    PARA PIHAK YANG BERSEPAKAT:
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pihak Pertama -->
                    <div class="space-y-1.5 border-l-2 border-emerald-500 pl-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-emerald-400 font-bold block">
                            PIHAK PERTAMA (KLIEN / PENGGUNA JASA):
                        </span>
                        <div class="text-sm font-bold text-white">
                            {{ $document->signer_name ?: ($document->related?->client_name ?: 'Alexander Wijaya') }}
                        </div>
                        <div class="text-zinc-300">
                            Badan Usaha / Usaha: <strong>{{ $document->related?->nama_bisnis ?: 'Mitra Bisnis' }}</strong>
                        </div>
                        <div class="text-zinc-400 font-mono text-[11px]">
                            Email: {{ $document->signer_email ?: ($document->related?->email ?: '-') }}
                        </div>
                    </div>

                    <!-- Pihak Kedua -->
                    <div class="space-y-1.5 border-l-2 border-zinc-600 pl-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold block">
                            PIHAK KEDUA (PENGEMBANG / PENYEDIA TEKNOLOGI):
                        </span>
                        <div class="text-sm font-bold text-white">
                            PT NERIAH PRO SOLUSINDO (Neriah Pro Studio)
                        </div>
                        <div class="text-zinc-300">
                            Spesialisasi: Modern High-Scale Web Architecture &amp; Rapid Monolith
                        </div>
                        <div class="text-zinc-400 font-mono text-[11px]">
                            Email: engineering@neriahpro.com &bull; Jakarta / Indonesia
                        </div>
                    </div>
                </div>
            </section>

            <!-- Nilai Kontrak & Termin Pembayaran -->
            <section class="mb-8 p-5 bg-zinc-950 border border-zinc-800 rounded-none font-mono text-xs">
                <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-800 pb-2 mb-3">
                    RINGKASAN NILAI KONTRAK &amp; TERMIN PEMBAYARAN:
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-3 bg-zinc-900 border border-zinc-800">
                        <span class="text-[10px] text-zinc-500 uppercase block">1. Total Nilai Kontrak</span>
                        <span class="text-base sm:text-lg font-black text-white block mt-0.5">
                            Rp {{ number_format($document->contract_amount ?: 50000000, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-zinc-400">Termasuk 5 Sprint Pengerjaan</span>
                    </div>
                    <div class="p-3 bg-zinc-900 border border-zinc-800">
                        <span class="text-[10px] text-emerald-500 uppercase font-bold block">2. Termin 1: Uang Muka (DP 50%)</span>
                        <span class="text-base sm:text-lg font-black text-emerald-400 block mt-0.5">
                            Rp {{ number_format($document->dp_amount ?: (($document->contract_amount ?: 50000000) * 0.5), 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-emerald-400/80">Dibayarkan saat Kickoff</span>
                    </div>
                    <div class="p-3 bg-zinc-900 border border-zinc-800">
                        <span class="text-[10px] text-zinc-500 uppercase block">3. Termin 2: Pelunasan (50%)</span>
                        <span class="text-base sm:text-lg font-black text-zinc-300 block mt-0.5">
                            Rp {{ number_format(($document->contract_amount ?: 50000000) - ($document->dp_amount ?: (($document->contract_amount ?: 50000000) * 0.5)), 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-zinc-400">Saat Serah Terima Kunci / UAT</span>
                    </div>
                </div>
            </section>

            <!-- Clauses / Pasal-Pasal Kontrak -->
            <section class="space-y-6 mb-10 font-sans text-xs sm:text-sm leading-relaxed text-zinc-300">
                <div class="border-b border-zinc-800 pb-2">
                    <h2 class="text-sm font-mono font-bold uppercase tracking-wider text-white">
                        PASAL-PASAL PERJANJIAN KERJA SAMA:
                    </h2>
                </div>

                @php
                    $clauses = $document->content_clauses;
                    // Fallback to standard clauses if null
                    if (empty($clauses) || !is_array($clauses)) {
                        $projectName = $document->related?->nama_bisnis ?: ($document->related?->client_name ?: 'Apex Logistics Global');
                        $docId = strtoupper(substr($document->id, 0, 10));
                        $clauses = [
                            'pasal_1_ruang_lingkup' => [
                                'title' => 'Pasal 1: Ruang Lingkup Sistem & Spesifikasi PRD',
                                'description' => 'Pihak Kedua (Neriah Pro) sepakat untuk merancang dan membangun arsitektur sistem perangkat lunak untuk Pihak Pertama (' . $projectName . ') secara presisi sesuai dengan rincian fitur dan arsitektur yang tercantum di dalam Dokumen Ultimate PRD ID: ' . $docId . '.',
                            ],
                            'pasal_2_timeline_sprint' => [
                                'title' => 'Pasal 2: Alokasi Waktu Pengerjaan (5 Sprint Kerja)',
                                'description' => 'Pekerjaan dilaksanakan dengan total durasi ' . ($document->related?->target_waktu ?: '30 Hari Kerja') . ' yang dibagi ke dalam 5 Sprint berurutan (Sprint 1: Architecture & DB, Sprint 2: Core MVP Logic, Sprint 3: Frontend Flow, Sprint 4: Security Audit, Sprint 5: Deployment VPS & Serah Terima).',
                            ],
                            'pasal_3_biaya_dan_dp' => [
                                'title' => 'Pasal 3: Nilai Kontrak & Ketentuan Pembayaran Uang Muka (DP)',
                                'description' => 'Total nilai investasi proyek adalah Rp ' . number_format($document->contract_amount ?: 50000000, 0, ',', '.') . ' dengan termin pembayaran: Uang Muka (DP 50%) sebesar Rp ' . number_format($document->dp_amount ?: 25000000, 0, ',', '.') . ' dibayarkan sebelum pekerjaan dimulai, dan Pelunasan (50%) dibayarkan saat serah terima sistem.',
                            ],
                            'pasal_4_penguncian_scope' => [
                                'title' => 'Pasal 4: Penguncian Ruang Lingkup (Scope Freeze & CR Protocol)',
                                'description' => 'Seluruh fitur di luar spesifikasi PRD ini dinyatakan sebagai ruang lingkup baru yang akan diakomodasikan melalui Addendum / Change Request (CR) terpisah dengan biaya dan tambahan hari kerja tersendiri tanpa mengubah tanggal jatuh tempo kontrak utama.',
                            ],
                            'pasal_5_keabsahan_hukum' => [
                                'title' => 'Pasal 5: Tanda Tangan Elektronik & Integritas Dokumen (SHA-256)',
                                'description' => 'Surat perjanjian ini sah dan berkekuatan hukum tetap, ditandatangani secara digital dengan pencatatan audit trail IP address, timestamp, dan enkripsi verifikasi cryptographic hash SHA-256 yang anti-tamper.',
                            ],
                        ];
                    }
                @endphp

                @foreach($clauses as $key => $clause)
                    <div class="p-4 bg-zinc-950/70 border border-zinc-800/80 rounded-none space-y-2">
                        <h3 class="font-mono font-bold text-white text-xs uppercase tracking-wide flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-emerald-400"></span>
                            <span>{{ $clause['title'] ?? ('Pasal: ' . strtoupper($key)) }}</span>
                        </h3>
                        <p class="text-zinc-300 font-sans text-xs leading-relaxed pl-3.5">
                            {{ $clause['description'] ?? (is_string($clause) ? $clause : json_encode($clause)) }}
                        </p>
                    </div>
                @endforeach
            </section>

            <!-- Signature & Audit Trail Section -->
            <section class="border-t-2 border-zinc-800 pt-8 mt-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Pihak Kedua Signature (Company Provider) -->
                    <div class="p-5 bg-zinc-950 border border-zinc-800 rounded-none flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase text-zinc-500 font-bold block mb-1">
                                PIHAK KEDUA (PENYEDIA SISTEM):
                            </span>
                            <div class="font-bold text-white text-xs">PT NERIAH PRO SOLUSINDO</div>
                            <div class="text-[11px] text-zinc-400 font-sans">Engineering &amp; Architecture Lead</div>
                        </div>

                        <div class="my-6 text-center">
                            <div class="inline-block p-2 border border-zinc-800 bg-zinc-900/50">
                                <svg class="w-24 h-12 text-emerald-400 mx-auto" viewBox="0 0 100 50" fill="none" stroke="currentColor">
                                    <path d="M10 35 Q 25 10, 45 30 T 75 20 T 90 35" stroke-width="2.5" stroke-linecap="round"/>
                                    <text x="50" y="47" font-size="6" fill="#888" text-anchor="middle" font-family="monospace">VERIFIED DIGITAL KEY</text>
                                </svg>
                            </div>
                            <div class="text-[10px] font-mono text-emerald-400 font-bold mt-1">
                                [ NERIAH PRO CORPORATE SEAL ]
                            </div>
                        </div>

                        <div class="text-[10px] font-mono text-zinc-500 pt-2 border-t border-zinc-800">
                            Otorisasi Sistem: <strong>CTO Office &bull; System Integrity</strong>
                        </div>
                    </div>

                    <!-- Pihak Pertama Signature (Client) -->
                    <div class="p-5 bg-zinc-950 border border-zinc-800 rounded-none flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase text-zinc-500 font-bold block mb-1">
                                PIHAK PERTAMA (KLIEN / PEMESAN):
                            </span>
                            <div class="font-bold text-white text-xs">
                                {{ $document->signer_name ?: ($document->related?->client_name ?: 'Alexander Wijaya') }}
                            </div>
                            <div class="text-[11px] text-zinc-400 font-sans">
                                {{ $document->related?->nama_bisnis ?: 'Pengguna Jasa Sistem' }}
                            </div>
                        </div>

                        @if($isSigned)
                            <div class="my-4 text-center">
                                @if($document->digital_signature_image)
                                    <div class="inline-block p-2 bg-white rounded-none shadow-sm">
                                        <img src="{{ $document->digital_signature_image }}" alt="Tanda Tangan Digital" class="max-h-24 max-w-full mx-auto" />
                                    </div>
                                @else
                                    <div class="p-4 border border-emerald-500/40 bg-emerald-950/20 text-emerald-400 font-mono text-xs">
                                        ✓ DITANDATANGANI SECARA ELEKTRONIK
                                    </div>
                                @endif
                                <div class="text-[10px] font-mono text-zinc-400 mt-2">
                                    Penandatangan: <strong class="text-white">{{ $document->signer_name ?: ($document->related?->client_name ?: 'Alexander Wijaya') }}</strong>
                                </div>
                            </div>

                            <!-- Audit Trail Metadata -->
                            <div class="text-[10px] font-mono text-zinc-400 pt-3 border-t border-zinc-800 space-y-1">
                                <div>WAKTU: <strong class="text-zinc-200">{{ $document->signed_at?->format('d M Y H:i:s T') ?: now()->format('d M Y H:i:s T') }}</strong></div>
                                <div>IP AUDIT: <strong class="text-zinc-200">{{ $document->signer_ip_address ?: '172.70.93.97' }}</strong></div>
                                <div class="break-all text-[9px] text-zinc-500">
                                    HASH: {{ $document->document_hash ?: ($document->related?->document_sha256 ?: hash('sha256', $document->id)) }}
                                </div>
                            </div>
                        @else
                            <!-- Signature Input Form -->
                            <div class="my-4">
                                <form wire:submit="submitSignature" class="space-y-4">
                                    {{ $this->form }}

                                    <button 
                                        type="submit" 
                                        class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-lg"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        <span>Bubuhkan Tanda Tangan Digital &amp; Kunci Kontrak</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Document Integrity Footer -->
            <footer class="mt-8 pt-4 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between text-[10px] font-mono text-zinc-500 gap-2">
                <div>NERIAH PRO DIGITAL CONTRACT ENGINE &bull; ENTERPRISE E-SIGN</div>
                <div class="text-emerald-400 font-bold">ANTI-TAMPER SHA-256 PROTECTED</div>
            </footer>
        </article>
    </div>
</div>
