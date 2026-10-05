<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->title }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.5; color: #222; margin: 20px; }
        .header { text-align: left; margin-bottom: 25px; border-bottom: 2px solid #10b981; padding-bottom: 12px; }
        .badge { display: inline-block; background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 2px 6px; margin-bottom: 6px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; color: #111; }
        .subtitle { font-size: 10px; color: #666; font-family: monospace; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 10px; }
        .meta-table .label { font-weight: bold; width: 25%; background-color: #f9fafb; color: #4b5563; }
        .parties-box { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .parties-box td { width: 50%; vertical-align: top; padding: 8px; border: 1px solid #e5e7eb; }
        .parties-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #059669; margin-bottom: 4px; }
        .clause-box { margin-bottom: 12px; padding: 8px 12px; border-left: 3px solid #10b981; background-color: #f9fafb; }
        .clause-title { font-weight: bold; font-size: 11px; color: #111; margin-bottom: 4px; }
        .clause-desc { font-size: 10.5px; color: #374151; text-align: justify; margin: 0; }
        .signature-section { margin-top: 35px; width: 100%; border-collapse: collapse; }
        .signature-box { width: 50%; vertical-align: top; text-align: center; padding: 10px; }
        .signature-image { max-width: 180px; max-height: 80px; margin: 8px 0; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 30px; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 8px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="header">
        <span class="badge">Surat Perjanjian Kerja Sama Resmi (E-Sign)</span>
        <div class="title">{{ $document->title }}</div>
        <div class="subtitle">DOKUMEN HUKUM &bull; ID: {{ strtoupper(substr($document->id, 0, 12)) }} &bull; TANGGAL: {{ ($document->signed_at ?: $document->created_at)->format('d F Y') }}</div>
    </div>

    <table class="parties-box">
        <tr>
            <td>
                <div class="parties-title">PIHAK PERTAMA (KLIEN / PEMESAN):</div>
                <strong>{{ $document->signer_name ?: ($document->related?->client_name ?: 'Alexander Wijaya') }}</strong><br>
                <span>Usaha: {{ $document->related?->nama_bisnis ?: 'Mitra Bisnis' }}</span><br>
                <span style="font-size: 9.5px; color: #666;">Email: {{ $document->signer_email ?: ($document->related?->email ?: '-') }}</span>
            </td>
            <td>
                <div class="parties-title">PIHAK KEDUA (PENGEMBANG SISTEM):</div>
                <strong>PT NERIAH PRO SOLUSINDO</strong><br>
                <span>Engineering &amp; Architecture Hub</span><br>
                <span style="font-size: 9.5px; color: #666;">Email: engineering@neriahpro.com &bull; Jakarta, Indonesia</span>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="label">Total Nilai Kontrak</td>
            <td><strong>Rp {{ number_format($document->contract_amount ?: 50000000, 0, ',', '.') }}</strong> (Termasuk 5 Sprint Kerja)</td>
            <td class="label">Termin Uang Muka (DP 50%)</td>
            <td><strong style="color: #059669;">Rp {{ number_format($document->dp_amount ?: (($document->contract_amount ?: 50000000) * 0.5), 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Status Ruang Lingkup</td>
            <td>SCOPE LOCKED (TERKUNCI SECARA KRIPTOGRAFIS)</td>
            <td class="label">Status Pembayaran</td>
            <td>{{ $document->status === 'signed' ? 'DP TERKONFIRMASI / LUNAS' : 'MENUNGGU DP' }}</td>
        </tr>
    </table>

    <h4 style="margin-top: 15px; margin-bottom: 8px; font-size: 11px; text-transform: uppercase;">Pasal-Pasal Kesepakatan:</h4>

    @php
        $clauses = $document->content_clauses;
        if (empty($clauses) || !is_array($clauses)) {
            $projectName = $document->related?->nama_bisnis ?: ($document->related?->client_name ?: 'Apex Logistics Global');
            $docId = strtoupper(substr($document->id, 0, 10));
            $clauses = [
                'pasal_1_ruang_lingkup' => [
                    'title' => 'Pasal 1: Ruang Lingkup Sistem & Spesifikasi PRD',
                    'description' => 'Pihak Kedua sepakat untuk merancang dan membangun sistem perangkat lunak untuk Pihak Pertama (' . $projectName . ') sesuai spesifikasi fitur pada Dokumen Ultimate PRD ID: ' . $docId . '.',
                ],
                'pasal_2_timeline_sprint' => [
                    'title' => 'Pasal 2: Alokasi Waktu Pengerjaan (5 Sprint Kerja)',
                    'description' => 'Pekerjaan dilaksanakan selama ' . ($document->related?->target_waktu ?: '30 Hari Kerja') . ' yang dibagi dalam 5 Sprint berurutan (Setup Arsitektur, Core MVP, User Flow, Security Audit, VPS Deployment).',
                ],
                'pasal_3_biaya_dan_dp' => [
                    'title' => 'Pasal 3: Nilai Kontrak & Ketentuan Pembayaran DP',
                    'description' => 'Total investasi adalah Rp ' . number_format($document->contract_amount ?: 50000000, 0, ',', '.') . ' dengan DP 50% sebesar Rp ' . number_format($document->dp_amount ?: 25000000, 0, ',', '.') . ' dibayarkan sebelum kickoff, dan Pelunasan 50% saat serah terima sistem.',
                ],
                'pasal_4_penguncian_scope' => [
                    'title' => 'Pasal 4: Penguncian Ruang Lingkup (Scope Freeze)',
                    'description' => 'Seluruh fitur di luar spesifikasi PRD ini dinyatakan sebagai ruang lingkup baru yang dituangkan dalam Addendum / Change Request (CR) terpisah.',
                ],
                'pasal_5_keabsahan_hukum' => [
                    'title' => 'Pasal 5: Tanda Tangan Elektronik & Integritas Dokumen (SHA-256)',
                    'description' => 'Surat perjanjian ini sah dan berkekuatan hukum tetap, ditandatangani secara digital dengan pencatatan audit trail IP address, timestamp, dan enkripsi hash SHA-256.',
                ],
            ];
        }
    @endphp

    @foreach($clauses as $key => $clause)
        <div class="clause-box">
            <div class="clause-title">{{ $clause['title'] ?? ('Pasal: ' . strtoupper($key)) }}</div>
            <p class="clause-desc">{{ $clause['description'] ?? (is_string($clause) ? $clause : json_encode($clause)) }}</p>
        </div>
    @endforeach

    <table class="signature-section">
        <tr>
            <td class="signature-box">
                <p><strong>PIHAK KEDUA (PENYEDIA SISTEM)</strong><br>PT NERIAH PRO SOLUSINDO</p>
                <div style="height: 60px; padding-top: 15px;">
                    <strong style="color: #059669; font-family: monospace; font-size: 11px;">[ VERIFIED CORPORATE KEY ]</strong>
                </div>
                <p style="margin: 0; font-size: 10px;">Engineering &amp; Architecture Lead</p>
            </td>
            <td class="signature-box">
                <p><strong>PIHAK PERTAMA (KLIEN / PEMESAN)</strong><br>{{ $document->related?->nama_bisnis ?: 'Mitra Pemesan' }}</p>
                @if($document->status === 'signed' && $document->digital_signature_image)
                    <img src="{{ $document->digital_signature_image }}" class="signature-image" alt="Tanda Tangan Digital">
                    <p style="font-weight: bold; margin: 0; font-size: 10.5px;">{{ $document->signer_name }}</p>
                    <p style="margin: 0; font-size: 9px; color: #666; font-family: monospace;">
                        IP: {{ $document->signer_ip_address }} &bull; {{ $document->signed_at?->format('d M Y H:i:s T') }}
                    </p>
                @else
                    <div style="height: 60px; border-bottom: 1px dashed #ccc; margin: 10px 30px;"></div>
                    <p style="font-size: 10px; color: #888;">( Menunggu Tanda Tangan )</p>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Dihasilkan secara otomatis oleh Neriah Pro Digital Legal Engine &bull; Dokumen Sah Dilindungi Kriptografi SHA-256
    </div>
</body>
</html>
