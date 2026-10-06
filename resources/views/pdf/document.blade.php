<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10.5px; line-height: 1.45; color: #111827; margin: 0; padding: 0; }
        .header { text-align: left; margin-bottom: 18px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .badge { display: inline-block; background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 8.5px; font-weight: bold; text-transform: uppercase; padding: 2px 6px; margin-bottom: 5px; }
        .title { font-size: 15px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; color: #111827; }
        .subtitle { font-size: 9.5px; color: #4b5563; font-family: monospace; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .meta-table td { padding: 5px 8px; border: 1px solid #d1d5db; font-size: 9.5px; }
        .meta-table .label { font-weight: bold; width: 25%; background-color: #f9fafb; color: #374151; }
        .parties-box { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .parties-box td { width: 50%; vertical-align: top; padding: 8px 10px; border: 1px solid #d1d5db; }
        .parties-title { font-size: 9.5px; font-weight: bold; text-transform: uppercase; color: #059669; margin-bottom: 4px; }
        .clause-box { margin-bottom: 10px; padding: 7px 10px; border-left: 3px solid #10b981; background-color: #f9fafb; page-break-inside: avoid; }
        .clause-title { font-weight: bold; font-size: 10.5px; color: #111827; margin-bottom: 3px; }
        .clause-desc { font-size: 10px; color: #374151; text-align: justify; margin: 0; line-height: 1.4; }
        .signature-section { margin-top: 25px; width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        .signature-box { width: 50%; vertical-align: top; text-align: center; padding: 8px 12px; border: 1px solid #e5e7eb; }
        .signature-image { max-width: 160px; max-height: 70px; margin: 6px 0; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 25px; font-size: 8.5px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 5px; font-family: monospace; }
    </style>
</head>
<body>
    @php
        $devInfo = \App\Support\ContractLegalHelper::getDeveloperInfo();
        $clauses = \App\Support\ContractLegalHelper::getDynamicClauses($document, 'id');
        $projectName = $document->related?->nama_bisnis ?: ($document->related?->client_name ?: 'Mitra Bisnis');
    @endphp

    <div class="header">
        <span class="badge">Surat Perjanjian Kerja Sama Resmi (Digital E-Sign)</span>
        <div class="title">{{ $document->title }}</div>
        <div class="subtitle">DOKUMEN HUKUM &bull; ID: {{ strtoupper(substr($document->id, 0, 12)) }} &bull; TANGGAL: {{ ($document->signed_at ?: $document->created_at)->format('d F Y') }}</div>
    </div>

    <table class="parties-box">
        <tr>
            <td>
                <div class="parties-title">PIHAK PERTAMA (KLIEN / PEMESAN):</div>
                <strong>{{ $document->signer_name ?: ($document->related?->client_name ?: 'Alexander Wijaya') }}</strong><br>
                <span>Usaha: {{ $projectName }}</span><br>
                <span style="font-size: 9px; color: #4b5563;">Email: {{ $document->signer_email ?: ($document->related?->email ?: '-') }}</span>
            </td>
            <td>
                <div class="parties-title">PIHAK KEDUA (PENGEMBANG SISTEM):</div>
                <strong>{{ $devInfo['entity_name'] }}</strong><br>
                <span>Engineering &amp; Architecture Studio</span><br>
                <span style="font-size: 9px; color: #4b5563;">Email: {{ $devInfo['email'] }} &bull; {{ $devInfo['location'] }}</span>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="label">Total Nilai Kontrak</td>
            <td><strong>Rp {{ number_format($document->contract_amount ?: 50000000, 0, ',', '.') }}</strong> (5 Sprint Kerja)</td>
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

    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #111827; margin-bottom: 6px;">
        Pasal-Pasal Kesepakatan Resmi:
    </div>

    @foreach($clauses as $key => $clause)
        <div class="clause-box">
            <div class="clause-title">{{ $clause['title'] ?? ('Pasal: ' . strtoupper($key)) }}</div>
            <p class="clause-desc">{{ $clause['description'] ?? (is_string($clause) ? $clause : json_encode($clause)) }}</p>
        </div>
    @endforeach

    <table class="signature-section">
        <tr>
            <td class="signature-box">
                <p style="margin: 0 0 4px 0;"><strong>PIHAK KEDUA (PENYEDIA SISTEM)</strong><br>{{ $devInfo['entity_name'] }}</p>
                <div style="min-height: 55px; padding-top: 5px;">
                    @if(!empty($devInfo['signature_image']))
                        <img src="{{ $devInfo['signature_image'] }}" class="signature-image" alt="Tanda Tangan Developer">
                    @else
                        <strong style="color: #059669; font-family: monospace; font-size: 10.5px;">[ {{ $devInfo['seal_text'] }} ]</strong>
                    @endif
                </div>
                <p style="margin: 2px 0 0 0; font-weight: bold; font-size: 10px;">{{ $devInfo['pic_name'] }}</p>
                <p style="margin: 0; font-size: 9px; color: #6b7280;">{{ $devInfo['pic_title'] }}</p>
            </td>
            <td class="signature-box">
                <p style="margin: 0 0 4px 0;"><strong>PIHAK PERTAMA (KLIEN / PEMESAN)</strong><br>{{ $projectName }}</p>
                <div style="min-height: 55px; padding-top: 5px;">
                    @if($document->status === 'signed' && $document->digital_signature_image)
                        <img src="{{ $document->digital_signature_image }}" class="signature-image" alt="Tanda Tangan Digital">
                    @else
                        <div style="height: 45px; border-bottom: 1px dashed #ccc; margin: 5px 25px;"></div>
                        <p style="font-size: 9px; color: #888; margin: 2px 0;">( Menunggu Tanda Tangan )</p>
                    @endif
                </div>
                <p style="font-weight: bold; margin: 2px 0 0 0; font-size: 10px;">{{ $document->signer_name ?: ($document->related?->client_name ?: 'Alexander Wijaya') }}</p>
                <p style="margin: 0; font-size: 8.5px; color: #6b7280; font-family: monospace;">
                    IP: {{ $document->signer_ip_address ?: 'Recorded' }} &bull; {{ ($document->signed_at ?: now())->format('d M Y H:i:s T') }}
                </p>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dihasilkan oleh Neriah Pro Digital Legal Engine &bull; Dokumen Sah Dilindungi Kriptografi SHA-256 &bull; neriahpro.com
    </div>
</body>
</html>
