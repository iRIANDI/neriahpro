@php
    $wl = is_array($whiteLabel ?? null) ? $whiteLabel : (is_array($posProject?->white_label_branding_json ?? null) ? $posProject->white_label_branding_json : []);
    $isWhiteLabel = !empty($wl['enabled']) && !empty($wl['agency_name']);
    $partnerEntityName = $isWhiteLabel ? $wl['agency_name'] : ($devInfo['entity_name'] ?? 'Neriah Pro Engineering Studio');
    $partnerEmail = $isWhiteLabel ? ($wl['agency_email'] ?? ($devInfo['email'] ?? 'hello@agency.partner')) : ($devInfo['email'] ?? 'support@neriahpro.com');
    $partnerPicName = $isWhiteLabel ? ($wl['pic_name'] ?? 'Lead Principal Architect') : ($devInfo['pic_name'] ?? 'Yoseph Iriandi');
    $partnerPicTitle = $isWhiteLabel ? ($wl['pic_title'] ?? 'Principal Engineering Partner') : ($devInfo['pic_title'] ?? 'Lead Software Architect');
    $partnerSealText = $isWhiteLabel ? (strtoupper($wl['agency_name']) . ' VERIFIED DIGITAL SEAL') : ($devInfo['seal_text'] ?? 'NERIAH PRO DIGITAL SEAL');
    $docLocale = (string) ($locale ?? (request()->query('locale') ?: ($posProject?->output_locale ?? ($schema['project_metadata']['output_locale'] ?? 'id'))));
    $isEn = $docLocale === 'en';
    $isRtlDoc = $docLocale === 'ar';
    $visualUi = is_array($schema['visual_frontend_ui_contract'] ?? null) ? $schema['visual_frontend_ui_contract'] : [];
    $jurisdiction = is_array($schema['jurisdiction_compliance_profile'] ?? null) ? $schema['jurisdiction_compliance_profile'] : [];
@endphp
<!DOCTYPE html>
<html lang="{{ $docLocale }}" dir="{{ $isRtlDoc ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $isWhiteLabel ? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $partnerEntityName)) . ' - ' : 'neriahpro.com - ' }}{{ strtoupper($docType) }} Resmi {{ $projectName }}</title>
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
            <strong>{{ strtoupper($partnerEntityName) }} — {{ $isEn ? 'MITRA COMMERCIAL & LEGAL PACK' : 'MITRA COMMERCIAL & LEGAL PACK' }}</strong>
            <span class="mono" style="margin-left: 8px; color: #a1a1aa;">[{{ strtoupper($docType) }}]</span>
        </div>
        <div>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/rab?locale=' . ($isEn ? 'en' : 'id')) }}" class="screen-btn">{{ $isEn ? '1. BOQ & WBS' : '1. RAB & WBS' }}</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/spk-appendix?locale=' . ($isEn ? 'en' : 'id')) }}" class="screen-btn">{{ $isEn ? '2. SPK Appendix SHA-256' : '2. Lampiran SPK SHA-256' }}</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/bast?locale=' . ($isEn ? 'en' : 'id')) }}" class="screen-btn">{{ $isEn ? '3. BAST & UAT' : '3. BAST & UAT' }}</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/sop-training?locale=' . ($isEn ? 'en' : 'id')) }}" class="screen-btn">{{ $isEn ? '4. SOP & Training' : '4. Buku SOP & Training Staf' }}</a>
            <a href="{{ url('/blueprint/' . $blueprint->slug . '/mitra-document/' . $docType . '?download=pdf&locale=' . ($isEn ? 'en' : 'id')) }}" class="screen-btn">{{ $isEn ? 'Download PDF' : 'Unduh PDF' }}</a>
            <button type="button" onclick="window.print()" class="screen-btn screen-btn-primary">{{ $isEn ? 'Print A4 Now' : 'Cetak A4 Sekarang' }}</button>
        </div>
    </div>
    @endif

    <div class="page-sheet">
        <div class="doc-header">
            @if($docType === 'rab')
                <div class="tag">{{ $isEn ? 'Official Commercial Document • Algorithmic Scope-to-Quotation' : 'Dokumen Resmi Komersial • Algorithmic Scope-to-Quotation' }}</div>
                <h1>{{ $isEn ? 'Bill of Quantities (BOQ/RAB) & Work Breakdown Structure (WBS)' : 'Rencana Anggaran Biaya (RAB) & Work Breakdown Structure (WBS)' }}</h1>
            @elseif($docType === 'spk-appendix')
                <div class="tag">{{ $isEn ? 'Contract Legal Appendix • Cryptographic Scope Freeze' : 'Lampiran Hukum Kontrak SPK/PKS • Cryptographic Scope Freeze' }}</div>
                <h1>{{ $isEn ? 'Technical Scope Appendix & SHA-256 Checksum Seal' : 'Lampiran Ruang Lingkup Teknis & Kunci Hash SHA-256' }}</h1>
            @elseif($docType === 'sop-training')
                <div class="tag">{{ $isEn ? 'Operational Handover Appendix • Auto-Generated Role SOP & Hardware Guide' : 'Lampiran Serah Terima Operasional • Auto-Generated Role SOP & Hardware Guide' }}</div>
                <h1>{{ $isEn ? 'Client Operations Manual, Role SOPs & Training Guide' : 'Buku Manual Operasional Klien, SOP Per Role Staf & Materi Training BAST' }}</h1>
            @else
                <div class="tag">{{ $isEn ? 'System Acceptance Certificate • UAT Given-When-Then Verified' : 'Berita Acara Serah Terima Sistem • UAT Given-When-Then Verified' }}</div>
                <h1>{{ $isEn ? 'Digital Acceptance Certificate (BAST) & UAT Recapitulation' : 'Berita Acara Serah Terima (BAST) Digital & Rekapitulasi UAT' }}</h1>
            @endif
            <div class="mono" style="font-size: 10px; color: #3f3f46;">
                {{ $isEn ? 'PROJECT:' : 'PROYEK:' }} {{ $projectName }} &bull; {{ $isEn ? 'VERSION:' : 'VERSI:' }} {{ $posProject?->current_version ?: 'v1.0' }} &bull; {{ $isEn ? 'DATE:' : 'TANGGAL:' }} {{ now()->format('d M Y H:i') }}
            </div>
        </div>

        <table>
            <tr>
                <th style="width: 22%;">{{ $isEn ? 'Client / Ordering Party' : 'Klien / Pemesan' }}</th>
                <td style="width: 28%;"><strong>{{ $blueprint->client_name ?: $projectName }}</strong><br><span style="color:#52525b;">{{ $blueprint->email ?: '-' }}</span></td>
                <th style="width: 22%;">{{ $isEn ? 'Executing Partner' : 'Mitra Pelaksana' }}</th>
                <td style="width: 28%;"><strong>{{ $partnerEntityName }}</strong><br><span style="color:#52525b;">{{ $partnerEmail }}</span></td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Functional Sector' : 'Sektor Fungsional' }}</th>
                <td class="mono">{{ $quotation['sector_code'] }}</td>
                <th>{{ $isEn ? 'Architecture Stack' : 'Arsitektur Stack' }}</th>
                <td class="mono">{{ $quotation['stack_preset'] }} ({{ $quotation['stack_multiplier'] }}x Multiplier)</td>
            </tr>
            <tr>
                <th>Visual Frontend UI/UX</th>
                <td class="mono">{{ $visualUi['selected_variant_code'] ?? ($quotation['selected_ui_variant'] ?? 'SECTOR_DEFAULT') }} ({{ $visualUi['navigation_shell_code'] ?? ($quotation['navigation_shell'] ?? 'SIDEBAR_ERP_DENSE') }})</td>
                <th>{{ $isEn ? 'Jurisdiction & Language' : 'Yurisdiksi & Bahasa' }}</th>
                <td class="mono">{{ strtoupper($docLocale) }} ({{ $isRtlDoc ? 'RTL' : 'LTR' }}) &bull; {{ $jurisdiction['jurisdiction_code'] ?? ($quotation['jurisdiction_code'] ?? 'ID_NKRI_STATUTORY') }}</td>
            </tr>
            <tr>
                <th>{{ $isEn ? 'Estimated Workload' : 'Estimasi Beban Kerja' }}</th>
                <td class="mono"><strong>{{ $quotation['man_hours'] }} {{ $isEn ? 'Man-Hours' : 'Jam Kerja' }} ({{ $quotation['timeline_days'] }} {{ $isEn ? 'Business Days' : 'Hari Kerja' }})</strong></td>
                <th>{{ $isEn ? 'Project Investment' : 'Nilai Investasi Proyek' }}</th>
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

        @elseif($docType === 'sop-training')
            @php
                $sopData = $schema['client_sop_and_onboarding'] ?? \App\Support\PosRealWorldRuntimeCatalog::buildRoleBasedClientSopAndOnboarding($schema, 'id');
                $hwData = $schema['hardware_iot_peripheral_contract'] ?? \App\Support\PosRealWorldRuntimeCatalog::hardwarePeripheralContractForSector($quotation['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL', [], $schema['enterprise_menu_matrix'] ?? [], 'id');
            @endphp
            <h2>1. Kesiapan Perangkat Keras Fisik &amp; Layar Real-Time ({{ $hwData['active_peripherals_count'] ?? 0 }} Periferal Aktif)</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 28%;">Perangkat / Jembatan Fisik</th>
                        <th style="width: 18%;">Status Proyek</th>
                        <th style="width: 54%;">Kontrak Operasional Lapangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($hwData['peripherals'] ?? []) as $p)
                        <tr>
                            <td><strong>{{ $p['title']['id'] ?? ($p['localized_title'] ?? '-') }}</strong></td>
                            <td class="mono">{{ !empty($p['active_by_default']) ? 'AKTIF WAJIB' : 'OPSIONAL SIAP' }}</td>
                            <td>{{ $p['contract']['id'] ?? ($p['localized_contract'] ?? '-') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>2. Panduan SOP Harian Langkah-demi-Langkah per Role / Departemen ({{ $sopData['total_interactive_tour_steps'] ?? 0 }} Menu Aktif v1.0)</h2>
            @foreach(($sopData['role_playbooks'] ?? []) as $role)
                <table>
                    <thead>
                        <tr>
                            <th colspan="3">
                                {{ $role['role_title']['id'] ?? $role['localized_role_title'] }} <span class="mono">[{{ $role['role_code'] }}]</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">
                                <strong>Misi Harian:</strong> {{ $role['daily_mission']['id'] ?? $role['localized_daily_mission'] }}<br>
                                <span style="color:#047857;"><strong>Aturan State Machine:</strong> {{ $role['state_machine_note']['id'] ?? '' }}</span>
                            </td>
                        </tr>
                        @foreach(($role['steps'] ?? []) as $step)
                            <tr>
                                <td class="mono" style="width: 12%;"><strong>#{{ $step['step_number'] }}</strong> ({{ $step['menu_code'] }})</td>
                                <td style="width: 34%;"><strong>{{ $step['title']['id'] ?? '' }}</strong><br><span class="mono" style="color:#52525b;">{{ $step['route_slug'] }}</span></td>
                                <td style="width: 54%;">{{ $step['instruction']['id'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach

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
                    <span>{{ $partnerEntityName }}</span>
                    <div style="height: 52px; padding-top: 12px;" class="mono">
                        <strong style="color:#047857;">[ {{ $partnerSealText }} ]</strong>
                    </div>
                    <strong>{{ $partnerPicName }}</strong><br>
                    <span style="font-size: 9.5px; color:#52525b;">{{ $partnerPicTitle }}</span>
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
            @if($isWhiteLabel)
                Architecture Engineered &amp; Scope-Verified by {{ $partnerEntityName }} &bull; 100% Zero-Slop Enterprise Standard &bull; Dilindungi Hash SHA-256
            @else
                Architecture Verified by NeriahPro Project OS v8.0 — 100% Zero-Slop Certified &bull; Dilindungi Hash Kriptografis SHA-256 &bull; neriahpro.com
            @endif
        </div>
    </div>
</body>
</html>

