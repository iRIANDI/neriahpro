<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>neriahpro.com - {{ strtoupper($docType) }} Resmi {{ $projectName }}</title>
    <style>
        @page { size: A4 portrait; margin: 14mm 15mm 15mm 15mm; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #09090b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .screen-bar {
            background: #09090b;
            color: #f4f4f5;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #27272a;
        }
        .screen-btn {
            display: inline-block;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid #3f3f46;
            color: #f4f4f5;
            background: #18181b;
            cursor: pointer;
            margin-left: 6px;
        }
        .screen-btn-primary {
            background: #059669;
            border-color: #10b981;
            color: #ffffff;
        }
        .page-sheet {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px 24px 36px 24px;
        }
        @media print {
            .screen-bar { display: none !important; }
            .page-sheet { padding: 0; max-width: 100%; }
        }
        .doc-header {
            border-bottom: 2px solid #09090b;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: #ecfdf5;
            color: #064e3b;
            border: 1px solid #6ee7b7;
            margin-bottom: 6px;
        }
        h1 { font-size: 17px; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: -0.01em; }
        h2 { font-size: 12px; margin: 16px 0 6px 0; text-transform: uppercase; border-bottom: 1px solid #d4d4d8; padding-bottom: 4px; }
        .mono { font-family: "Courier New", Courier, monospace; font-variant-numeric: tabular-nums; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #d4d4d8; padding: 6px 8px; text-align: left; vertical-align: top; font-size: 10.5px; }
        th { background: #f4f4f5; font-weight: 700; text-transform: uppercase; font-size: 9.5px; color: #18181b; }
        .num { text-align: right; font-variant-numeric: tabular-nums; font-family: "Courier New", Courier, monospace; }
        .hash-box {
            background: #f4f4f5;
            border: 1px solid #a1a1aa;
            padding: 8px 10px;
            font-family: "Courier New", Courier, monospace;
            font-size: 9.5px;
            word-break: break-all;
            margin-bottom: 14px;
        }
        .sig-table td { width: 50%; text-align: center; padding: 12px; }
        .footer-note {
            margin-top: 22px;
            padding-top: 8px;
            border-top: 1px solid #d4d4d8;
            font-size: 9px;
            color: #52525b;
            text-align: center;
            font-family: "Courier New", Courier, monospace;
        }
    </style>
</head>
<body>
    @if(empty($isPdfStream))
    <div class="screen-bar">
        <div>
            <strong>NERIAH PRO — MITRA COMMERCIAL &amp; LEGAL PACK</strong>
            <span class="mono" style="margin-left: 8px; color: #a1a1aa;">[{{ strtoupper($docType) }}]</span>
        </div>
        <div>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/rab') }}" class="screen-btn">1. RAB &amp; WBS</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/spk-appendix') }}" class="screen-btn">2. Lampiran SPK SHA-256</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/bast') }}" class="screen-btn">3. BAST &amp; UAT</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/' . $docType . '?download=pdf') }}" class="screen-btn">Unduh PDF</a>
            <button type="button" onclick="window.print()" class="screen-btn screen-btn-primary">Cetak A4 Sekarang</button>
        </div>
    </div>
    @endif

    <div class="page-sheet">
        <div class="doc-header">
            @if($docType === 'rab')
                <div class="tag">Dokumen Resmi Komersial &bull; Algorithmic Scope-to-Quotation</div>
                <h1>Rencana Anggaran Biaya (RAB) &amp; Work Breakdown Structure (WBS)</h1>
            @elseif($docType === 'spk-appendix')
                <div class="tag">Lampiran Hukum Kontrak SPK/PKS &bull; Cryptographic Scope Freeze</div>
                <h1>Lampiran Ruang Lingkup Teknis &amp; Kunci Hash SHA-256</h1>
            @else
                <div class="tag">Berita Acara Serah Terima Sistem &bull; UAT Given-When-Then Verified</div>
                <h1>Berita Acara Serah Terima (BAST) Digital &amp; Rekapitulasi UAT</h1>
            @endif
            <div class="mono" style="font-size: 10px; color: #3f3f46;">
                PROYEK: {{ $projectName }} &bull; VERSI: {{ $posProject?->current_version ?: 'v1.0' }} &bull; TANGGAL: {{ now()->format('d M Y H:i') }} WIB
            </div>
        </div>

        <table>
            <tr>
                <th style="width: 22%;">Klien / Pemesan</th>
                <td style="width: 28%;"><strong>{{ $blueprint->client_name ?: $projectName }}</strong><br><span style="color:#52525b;">{{ $blueprint->email ?: '-' }}</span></td>
                <th style="width: 22%;">Mitra Pelaksana</th>
                <td style="width: 28%;"><strong>{{ $devInfo['entity_name'] ?? 'Neriah Pro Engineering Studio' }}</strong><br><span style="color:#52525b;">{{ $devInfo['email'] ?? 'support@neriahpro.com' }}</span></td>
            </tr>
            <tr>
                <th>Sektor Fungsional</th>
                <td class="mono">{{ $quotation['sector_code'] }}</td>
                <th>Arsitektur Stack</th>
                <td class="mono">{{ $quotation['stack_preset'] }} ({{ $quotation['stack_multiplier'] }}x Multiplier)</td>
            </tr>
            <tr>
                <th>Estimasi Beban Kerja</th>
                <td class="mono"><strong>{{ $quotation['man_hours'] }} Jam Kerja ({{ $quotation['timeline_days'] }} Hari Kerja)</strong></td>
                <th>Nilai Investasi Proyek</th>
                <td class="mono"><strong style="color:#047857;">{{ $quotation['formatted_quotation_idr'] }}</strong></td>
            </tr>
        </table>

        <div class="hash-box">
            <strong>CRYPTOGRAPHIC BLUEPRINT SCOPE HASH (SHA-256):</strong><br>
            {{ $quotation['scope_hash_full'] }}
        </div>

        @if($docType === 'rab')
            <h2>1. Rincian Work Breakdown Structure (WBS 7-Milestone)</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">Fase</th>
                        <th style="width: 48%;">Milestone &amp; Deliverable Teknis</th>
                        <th style="width: 14%;" class="num">Jam Kerja</th>
                        <th style="width: 12%;" class="num">Durasi</th>
                        <th style="width: 18%;" class="num">Alokasi Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation['wbs_7_milestones'] as $m)
                        <tr>
                            <td class="mono">P0{{ $m['phase'] }}</td>
                            <td>{{ $m['title'] }} <span style="color:#52525b; font-size:9.5px;">[{{ $m['termin_gate'] }}]</span></td>
                            <td class="num">{{ $m['man_hours'] }} Jam</td>
                            <td class="num">{{ $m['working_days'] }} Hari</td>
                            <td class="num">{{ $m['formatted_cost'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>2. Jadwal Termin Pembayaran &amp; Milestone Serah Terima</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Termin</th>
                        <th style="width: 62%;">Deskripsi Milestone Penagihan</th>
                        <th style="width: 10%;" class="num">Porsi</th>
                        <th style="width: 18%;" class="num">Nilai Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation['termin_schedule'] as $t)
                        <tr>
                            <td class="mono">T-{{ $t['termin_number'] }}</td>
                            <td>{{ $t['label'] }}</td>
                            <td class="num">{{ $t['percentage'] }}%</td>
                            <td class="num"><strong>{{ $t['formatted_amount'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @elseif($docType === 'spk-appendix')
            <h2>1. Daftar Tabel Strict ULID yang Dikunci dalam Kontrak ({{ count($schema['data_dictionary'] ?? []) }} Tabel)</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 28%;">Nama Tabel (PostgreSQL 16)</th>
                        <th style="width: 22%;">Kebijakan Integritas</th>
                        <th style="width: 50%;">Kolom Terkunci</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($schema['data_dictionary'] ?? []) as $tbl)
                        <tr>
                            <td class="mono"><strong>{{ $tbl['table'] ?? '-' }}</strong></td>
                            <td class="mono">{{ $tbl['deletion_policy'] ?? 'RESTRICT_OR_IMMUTABLE' }}</td>
                            <td class="mono">
                                {{ implode(', ', array_map(fn ($c) => is_array($c) ? ($c['name'] ?? '') : (string) $c, (array) ($tbl['columns'] ?? []))) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>2. Kontrak State Machine &amp; Pessimistic Concurrency Locks</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30%;">Entitas / State Enum</th>
                        <th style="width: 70%;">Daftar Status Sah (Zero Ambiguity)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($schema['state_machines'] ?? []) as $sm)
                        <tr>
                            <td class="mono"><strong>{{ $sm['entity'] ?? '-' }}</strong><br><span style="color:#52525b;">{{ $sm['enum_class'] ?? '' }}</span></td>
                            <td class="mono">{{ implode(' -> ', (array) ($sm['states'] ?? [])) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(!empty($changeRequests) && count($changeRequests) > 0)
                <h2>3. Riwayat Addendum Change-Order (Paid Delta Evolution)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Versi</th>
                            <th>Judul Perubahan</th>
                            <th class="num">Tambahan Jam/Hari</th>
                            <th class="num">Nilai Addendum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($changeRequests as $cr)
                            <tr>
                                <td class="mono">{{ $cr->from_version }} &rarr; {{ $cr->to_version }}</td>
                                <td>{{ $cr->title }}</td>
                                <td class="num">+{{ $cr->delta_man_hours }}j / +{{ $cr->delta_working_days }}h</td>
                                <td class="num">Rp {{ number_format((float) $cr->delta_quotation_idr, 0, ',', '.') }}</td>
                                <td class="mono">{{ $cr->addendum_status ?: $cr->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        @else
            @php $bast = $posProject?->bast_signoff_json ?? null; @endphp
            @if(!empty($bast))
                <div class="hash-box" style="background: #ecfdf5; border-color: #10b981; color: #064e3b;">
                    <strong>NOMOR BAST RESMI: {{ $bast['bast_number'] ?? '-' }}</strong><br>
                    DISAHKAN OLEH: {{ $bast['signer_name'] ?? '-' }} ({{ $bast['signer_email'] ?? '-' }}) &bull; IP: {{ $bast['signer_ip'] ?? '-' }} &bull; WAKTU: {{ $bast['signed_at'] ?? '-' }}<br>
                    BAST SHA-256 SEAL: {{ $bast['bast_sha256'] ?? '-' }}
                </div>
            @endif

            <h2>1. Lembar Hasil Uji Terima Klien (UAT Given-When-Then: {{ $uat['passed_scenarios'] }}/{{ $uat['total_scenarios'] }} Lolos)</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 74%;">Skenario Pengujian (Given &bull; When &bull; Then)</th>
                        <th style="width: 16%;">Status Uji</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($uat['scenarios'] ?? []) as $sc)
                        <tr>
                            <td class="mono"><strong>{{ $sc['id'] }}</strong></td>
                            <td>
                                <strong>{{ $sc['title'] }}</strong><br>
                                <span style="color:#3f3f46;"><strong>Given:</strong> {{ $sc['given'] }}</span><br>
                                <span style="color:#3f3f46;"><strong>When:</strong> {{ $sc['when'] }}</span><br>
                                <span style="color:#047857;"><strong>Then:</strong> {{ $sc['then'] }}</span>
                            </td>
                            <td class="mono">
                                @if(!empty($sc['passed']))
                                    <strong style="color:#047857;">[x] LULUS UAT</strong>
                                @else
                                    <span style="color:#71717a;">[ ] BELUM DIUJI</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <table class="sig-table" style="margin-top: 22px;">
            <tr>
                <td>
                    <strong>PIHAK KEDUA (MITRA REKAYASA SISTEM)</strong><br>
                    <span>{{ $devInfo['entity_name'] ?? 'Neriah Pro' }}</span>
                    <div style="height: 52px; padding-top: 12px;" class="mono">
                        <strong style="color:#047857;">[ {{ $devInfo['seal_text'] ?? 'NERIAH PRO DIGITAL SEAL' }} ]</strong>
                    </div>
                    <strong>{{ $devInfo['pic_name'] ?? 'Yoseph Iriandi' }}</strong><br>
                    <span style="font-size: 9.5px; color:#52525b;">{{ $devInfo['pic_title'] ?? 'Lead Software Architect' }}</span>
                </td>
                <td>
                    <strong>PIHAK PERTAMA (PERWAKILAN KLIEN RESMI)</strong><br>
                    <span>{{ $projectName }}</span>
                    <div style="height: 52px; padding-top: 12px;" class="mono">
                        @if(!empty($posProject?->bast_signoff_json['bast_sha256']))
                            <strong style="color:#047857;">[ DIGITAL E-SIGN VERIFIED ]</strong>
                        @else
                            <span style="color:#71717a;">( Tanda Tangan / Persetujuan Digital )</span>
                        @endif
                    </div>
                    <strong>{{ $posProject?->bast_signoff_json['signer_name'] ?? ($blueprint->client_name ?: 'PIC Klien') }}</strong><br>
                    <span style="font-size: 9.5px; color:#52525b;">{{ $posProject?->bast_signoff_json['signed_at'] ?? 'Sesuai Log Audit Kriptografis' }}</span>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Dokumen Resmi Project OS v5.0 Commercial-to-Code Bridge &bull; Dilindungi Hash Kriptografis SHA-256 &bull; neriahpro.com
        </div>
    </div>
</body>
</html>

