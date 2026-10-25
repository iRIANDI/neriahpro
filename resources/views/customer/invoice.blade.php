@php
    $isEn = app()->getLocale() === 'en';
    $companyName = \App\Models\CmsGlobalSetting::getVal('developer_entity_name', 'Neriah Pro (Yoseph Iriandi)');
    $companyOwner = \App\Models\CmsGlobalSetting::getVal('company_owner_name', 'Yoseph Iriandi');
    $companyOwnerTitle = \App\Models\CmsGlobalSetting::getVal('company_owner_title', 'Independent Full-Stack Developer & Software Architect');
    $companyAddress = \App\Models\CmsGlobalSetting::getVal('company_address', 'Gianyar, Bali, Indonesia');
    $companyEmail = \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com');
    $companyPhone = \App\Models\CmsGlobalSetting::getVal('company_whatsapp', '6281380448253');
    $companyNpwp = \App\Models\CmsGlobalSetting::getVal('company_npwp', '');

    $invoiceNumber = 'INV-' . ($transaction->created_at ? $transaction->created_at->format('Ymd') : date('Ymd')) . '-' . strtoupper(substr($transaction->id, 0, 8));
    $orderRef = $transaction->midtrans_order_id ?: ('NPRO-' . substr($transaction->id, 0, 10));
    $txDetails = $transaction->customer_details ?? [];
    
    // Resolve client billing information
    $clientName = $user->name ?: ($txDetails['name'] ?? 'Pelanggan');
    $clientCompany = $user->company_name ?? ($txDetails['company'] ?? ($txDetails['company_name'] ?? '-'));
    $clientEmail = $user->email ?: ($txDetails['email'] ?? '-');
    $clientPhone = $user->phone ?: ($txDetails['phone'] ?? '-');
    $clientNpwp = $user->npwp ?? ($txDetails['npwp'] ?? '-');
    $clientAddress = $user->billing_address ?? ($txDetails['billing_address'] ?? '-');

    // Resolve item name and description
    $packageTier = $txDetails['package_tier'] ?? ($txDetails['tier'] ?? null);
    $packageRegistry = [
        'retail_spark' => ['name' => 'Paket Retail Spark (Free Idea Audit)', 'desc' => 'Audit ide arsitektur & kelayakan teknis software instant.'],
        'retail_lite' => ['name' => 'Lisensi Retail Lite (Starter AI Architect)', 'desc' => 'Dokumen PRD 26 parameter, skema DDL PostgreSQL Strict ULID, lisensi akses seumur hidup.'],
        'retail_pro' => ['name' => 'Lisensi Retail Pro (Engineering Directives)', 'desc' => 'Seluruh paket Lite + AI Coding Rules (.cursorrules & AGENTS.md), synthetic seeder 100+ baris, docker config.'],
        'retail_ultimate' => ['name' => 'Lisensi Retail Ultimate (Software Factory OS)', 'desc' => 'Seluruh paket Lite & Pro + Master Starter Repo ZIP (Laravel 13, Filament v5, Livewire 4) + 60m 1-on-1 Call.'],
        'full_mvp' => ['name' => 'Kontrak Studio: Enterprise Rapid Monolith MVP', 'desc' => 'Pengembangan 100% turnkey tim software engineer Neriah Pro, 5 Sprint pengerjaan, garansi SLA 3 bulan.'],
        'umkm_starter' => ['name' => 'Kontrak Studio: UMKM Digital Starter', 'desc' => 'Aplikasi web operasional & kasir terpadu, integrasi QRIS otomatis, garansi cloud hosting 1 bulan.'],
        'blueprint_advisory' => ['name' => 'Neriah Pro Studio: 1-on-1 Architectural Advisory', 'desc' => 'Sesi scoping teknis intensif 60 menit bersama Lead Architect, DDL SQL, WBS 5 Sprint (Memotong DP 50%).'],
    ];

    $resolvedItem = $packageRegistry[$packageTier] ?? [
        'name' => $txDetails['notes'] ?? 'Layanan Rekayasa Perangkat Lunak & Lisensi Digital Neriah Pro',
        'desc' => 'Pengembangan software enterprise dan lisensi spesifikasi sistem berbasis Project OS.'
    ];

    $isSettled = in_array($transaction->status, ['settlement', 'capture', 'success']);
    $totalAmount = (float) $transaction->total_idr;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoiceNumber }} - Bukti Pembayaran Sah & Official Commercial Invoice | Neriah Pro</title>

    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-paper {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
                margin: 0 auto !important;
                max-width: 100% !important;
                padding: 12mm 15mm !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen py-8">

    <!-- FLOATING TOP CONTROLS (NON-PRINT) -->
    <div class="max-w-4xl mx-auto px-4 mb-6 no-print flex items-center justify-between gap-4">
        <a 
            href="/customer/dashboard#billing" 
            class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none shadow-xs"
        >
            <span>&larr;</span>
            <span>{{ $isEn ? 'Back to Dashboard' : 'Kembali ke Dashboard' }}</span>
        </a>

        <div class="flex items-center gap-2">
            <button 
                type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-mono font-black uppercase tracking-wider transition rounded-none shadow-xs cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>{{ $isEn ? 'Print / Save as PDF' : 'Cetak / Simpan PDF' }}</span>
            </button>
        </div>
    </div>

    <!-- MAIN INVOICE & RECEIPT SHEET (A4 CONTAINER) -->
    <div class="invoice-paper max-w-4xl mx-auto bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-700 p-8 sm:p-12 shadow-xl rounded-none">
        
        <!-- HEADER ROW: COMPANY & INVOICE IDENTIFIERS -->
        <div class="border-b-2 border-zinc-900 dark:border-zinc-100 pb-6 flex flex-col sm:flex-row justify-between items-start gap-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 bg-black text-white dark:bg-white dark:text-black font-mono text-xs font-black tracking-widest uppercase">
                        NERIAH PRO
                    </span>
                    <span class="text-xs font-mono text-zinc-500 font-bold uppercase">
                        INDEPENDENT DEVELOPER
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                    {{ $companyName }}
                </h1>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 font-sans mt-1 max-w-md leading-relaxed">
                    <strong>Owner / Developer:</strong> {{ $companyOwner }} ({{ $companyOwnerTitle }})<br>
                    <strong>Domisili:</strong> {{ $companyAddress }}<br>
                    <strong>Email:</strong> {{ $companyEmail }} &bull; <strong>WA:</strong> +{{ $companyPhone }}
                </p>
            </div>

            <div class="sm:text-right">
                <div class="inline-block px-3 py-1 text-xs font-mono font-black uppercase tracking-wider mb-2 border {{ $isSettled ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-amber-100 text-amber-950 border-amber-400 dark:bg-amber-950 dark:text-amber-200' }}">
                    {{ $isSettled ? '✓ LUNAS // OFFICIAL COMMERCIAL INVOICE' : 'MENUNGGU PEMBAYARAN' }}
                </div>
                <div class="text-xs font-mono text-zinc-500 font-bold uppercase">NOMOR INVOICE / BUKTI BAYAR:</div>
                <div class="text-sm sm:text-base font-mono font-black text-zinc-900 dark:text-white">{{ $invoiceNumber }}</div>
                <div class="text-xs font-mono text-zinc-500 mt-1">
                    Tanggal: <strong>{{ $transaction->created_at ? $transaction->created_at->format('d M Y, H:i') : date('d M Y') }} WIB</strong>
                </div>
            </div>
        </div>

        <!-- 2-COLUMN INFO: ISSUED TO & TRANSACTION METADATA -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-zinc-200 dark:border-zinc-800 text-xs">
            <div>
                <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-1">
                    DITERBITKAN KEPADA (KLIEN / PERUSAHAAN):
                </span>
                <div class="font-bold text-sm text-zinc-900 dark:text-white uppercase">{{ $clientCompany !== '-' ? $clientCompany : $clientName }}</div>
                <div class="text-zinc-600 dark:text-zinc-400 font-sans mt-1 space-y-0.5">
                    <div><strong>PIC / Kontak:</strong> {{ $clientName }}</div>
                    <div><strong>Email:</strong> {{ $clientEmail }}</div>
                    <div><strong>WhatsApp:</strong> {{ $clientPhone }}</div>
                    <div><strong>NPWP Klien:</strong> {{ $clientNpwp }}</div>
                    @if($clientAddress !== '-')
                        <div><strong>Alamat Penagihan:</strong> {{ $clientAddress }}</div>
                    @endif
                </div>
            </div>

            <div class="sm:border-l sm:border-zinc-200 dark:sm:border-zinc-800 sm:pl-6">
                <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-1">
                    REFERENSI PEMBAYARAN MIDTRANS:
                </span>
                <div class="text-zinc-600 dark:text-zinc-400 font-mono space-y-1">
                    <div>Order ID: <strong class="text-zinc-900 dark:text-white">{{ $orderRef }}</strong></div>
                    <div>Gateway: <strong>Midtrans Payment Gateway (Snap API)</strong></div>
                    <div>ID Transaksi: <strong>{{ $transaction->midtrans_transaction_id ?: 'Verifikasi Gateway Otomatis' }}</strong></div>
                    <div>Status: <span class="font-bold {{ $isSettled ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600' }}">{{ strtoupper($transaction->status) }}</span></div>
                    <div>Jenis Dokumen: <strong>Official Commercial Invoice & Kwitansi Pembayaran</strong></div>
                </div>
            </div>
        </div>

        <!-- ITEM DETAILS TABLE -->
        <div class="py-6 border-b border-zinc-200 dark:border-zinc-800">
            <table class="w-full text-left font-mono text-xs">
                <thead>
                    <tr class="border-b-2 border-zinc-900 dark:border-zinc-100 text-zinc-900 dark:text-white uppercase text-[10px] font-black">
                        <th class="py-2 pr-3">NO</th>
                        <th class="py-2 pr-3">DESKRIPSI LAYANAN / LISENSI DIGITAL</th>
                        <th class="py-2 px-3 text-center">QTY</th>
                        <th class="py-2 pl-3 text-right">HARGA (IDR)</th>
                        <th class="py-2 pl-3 text-right">JUMLAH (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-800 dark:text-zinc-200">
                    <tr>
                        <td class="py-4 pr-3 font-bold align-top">01</td>
                        <td class="py-4 pr-3 align-top">
                            <div class="font-bold text-sm text-zinc-900 dark:text-white uppercase">
                                {{ $resolvedItem['name'] }}
                            </div>
                            <div class="text-xs text-zinc-500 font-sans mt-0.5 max-w-xl">
                                {{ $resolvedItem['desc'] }}
                            </div>
                            @if(!empty($txDetails['blueprint_slug']))
                                <div class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 mt-1">
                                    Spesifikasi Terkait: https://neriahpro.com/blueprint/{{ $txDetails['blueprint_slug'] }}
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-3 text-center font-bold align-top">1</td>
                        <td class="py-4 pl-3 text-right font-bold align-top">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 pl-3 text-right font-black text-zinc-900 dark:text-white align-top">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TOTALS & TAX BREAKDOWN -->
        <div class="py-6 border-b-2 border-zinc-900 dark:border-zinc-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs font-mono">
            <div class="text-zinc-500 max-w-md text-[11px] leading-relaxed">
                * Harga di atas telah mencakup seluruh hak akses lisensi digital seumur hidup (*lifetime license*), penyediaan arsip spesifikasi, dan dokumen pendukung sesuai pesanan.
            </div>

            <div class="w-full sm:w-72 space-y-1.5">
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Subtotal:</span>
                    <span class="font-bold">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>PPN (Sudah Termasuk):</span>
                    <span class="font-bold">Rp 0 (Termasuk)</span>
                </div>
                <div class="flex justify-between text-base font-black text-zinc-900 dark:text-white pt-2 border-t border-zinc-300 dark:border-zinc-700">
                    <span>TOTAL BAYAR:</span>
                    <span class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- LEGAL STAMP & ELECTRONIC VALIDATION SEAL -->
        <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 items-end text-xs">
            <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono text-zinc-500 leading-normal space-y-1">
                <div class="font-bold text-zinc-800 dark:text-zinc-200 uppercase">KEABSAHAN DOKUMEN ELEKTRONIK:</div>
                <p>
                    Dokumen ini merupakan Bukti Pembayaran Sah dan Official Commercial Invoice yang diterbitkan secara otomatis oleh sistem akuntansi <strong>Neriah Pro</strong> terintegrasi dengan Payment Gateway Midtrans.
                </p>
                <div class="text-zinc-400 truncate">
                    SHA-256 CHECK: {{ hash('sha256', $invoiceNumber . $totalAmount . $orderRef) }}
                </div>
            </div>

            <div class="text-center sm:text-right font-sans">
                <div class="text-xs text-zinc-500 font-mono">Diverifikasi & Disahkan Elektronik oleh:</div>
                <div class="mt-2 text-sm font-black uppercase text-zinc-900 dark:text-white font-mono">
                    {{ $companyName }}
                </div>
                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono font-bold mt-1">
                    ✓ TERVERIFIKASI SISTEM TERPADU
                </div>
                <div class="text-[10px] text-zinc-400 font-mono mt-0.5">
                    Jakarta, {{ $transaction->created_at ? $transaction->created_at->format('d M Y') : date('d M Y') }}
                </div>
            </div>
        </div>

    </div>

</body>
</html>
