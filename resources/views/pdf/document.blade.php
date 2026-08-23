<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .content { margin-bottom: 40px; text-align: justify; }
        .signature-section { margin-top: 50px; width: 100%; }
        .signature-box { width: 45%; float: right; text-align: center; }
        .signature-image { max-width: 200px; max-height: 100px; margin: 10px 0; border-bottom: 1px solid #ccc; }
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 50px; font-size: 10px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $document->title }}</div>
        <div>Dokumen Legal Terverifikasi Secara Digital</div>
    </div>

    <div class="content">
        <p>Pada hari ini, disepakati bahwa dokumen dengan rincian berikut sah dan mengikat:</p>
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 30%;"><strong>ID Dokumen:</strong></td>
                <td>{{ $document->id }}</td>
            </tr>
            <tr>
                <td><strong>Tipe Dokumen:</strong></td>
                <td>{{ strtoupper(str_replace('_', ' ', $document->document_type)) }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ strtoupper($document->status) }}</td>
            </tr>
        </table>

        <h3>Pasal 1: Persetujuan</h3>
        <p>Dengan membubuhkan tanda tangan secara elektronik pada dokumen ini, pihak yang bertanda tangan di bawah ini menyatakan telah membaca, memahami, dan menyetujui seluruh isi dokumen ini tanpa ada paksaan dari pihak manapun.</p>
        
        <h3>Pasal 2: Validitas Elektronik</h3>
        <p>Dokumen ini ditandatangani secara digital. Berdasarkan undang-undang yang berlaku terkait Informasi dan Transaksi Elektronik, tanda tangan digital yang tertera pada halaman ini memiliki kekuatan hukum yang sah dan mengikat, sama dengan tanda tangan basah pada dokumen fisik.</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>DITANDATANGANI OLEH:</strong></p>
            @if($document->status === 'signed' && $document->digital_signature_image)
                <img src="{{ $document->digital_signature_image }}" class="signature-image" alt="Tanda Tangan Digital">
                <p style="font-weight: bold; margin-bottom: 0;">{{ $document->signer_name }}</p>
                <p style="margin-top: 0; font-size: 10px;">Email: {{ $document->signer_email }}<br>IP: {{ $document->signer_ip_address }}<br>Waktu: {{ $document->signed_at?->format('d M Y H:i:s') }}</p>
                @if($document->document_hash)
                    <p style="font-size: 8px; word-break: break-all; color: #888;">Hash: {{ $document->document_hash }}</p>
                @endif
            @else
                <div style="height: 100px; border-bottom: 1px dashed #ccc; margin: 10px 0;"></div>
                <p>( Belum Ditandatangani )</p>
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        Dihasilkan oleh Sistem Neriah Pro pada {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
