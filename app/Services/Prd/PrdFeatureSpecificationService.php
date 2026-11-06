<?php

namespace App\Services\Prd;

use App\Services\BlueprintDiscoveryService;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrdFeatureSpecificationService
{
    /**
     * Parse multiline text or comma-separated text into structured items.
     */
    public static function parseItems(string $text, string $domain = 'custom_portal', bool $isEn = false): array
    {
        $lines = \App\Support\BlueprintPdfHelper::extractItems($text);
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
            if (empty($line)) {
                continue;
            }

            // Check if line contains casual conversation or raw conversational bullet points
            $lower = mb_strtolower($line);
            $hasConversationalMarker = str_contains($lower, 'berdasarkan') || 
                                       str_contains($lower, 'poin no') || 
                                       str_contains($lower, 'ada juga') || 
                                       str_contains($lower, 'saya mau') || 
                                       str_contains($lower, 'kami ingin') || 
                                       str_contains($lower, 'tolong buatkan') ||
                                       str_contains($lower, 'sediakan fitur') ||
                                       str_contains($lower, 'alias wisata');

            if ($hasConversationalMarker || (!str_contains($line, ':') && !str_contains($line, ' - '))) {
                $crafted = BlueprintDiscoveryService::craftFeatureFromRawItem($line, $domain, $isEn);
                if (str_contains($crafted, ':')) {
                    [$title, $desc] = explode(':', $crafted, 2);
                    $items[] = [
                        'title' => trim($title),
                        'desc' => trim($desc),
                    ];
                    continue;
                }
            }

            // Check if there's a colon or dash separator for title vs description
            if (str_contains($line, ':')) {
                [$title, $desc] = explode(':', $line, 2);
                $items[] = [
                    'title' => trim($title),
                    'desc' => trim($desc),
                ];
            } elseif (str_contains($line, ' - ')) {
                [$title, $desc] = explode(' - ', $line, 2);
                $items[] = [
                    'title' => trim($title),
                    'desc' => trim($desc),
                ];
            } else {
                $items[] = [
                    'title' => $line,
                    'desc' => 'Spesifikasi fitur utama untuk operasional sistem.',
                ];
            }
        }

        return $items;
    }


    /**
     * Parse system actors with intelligent role inference and RBAC normalization.
     */
    public static function parseActors(string $text, string $businessName = '', string $audiens = '', bool $isEn = false): array
    {
        $normalizedLines = \App\Support\BlueprintPdfHelper::extractItems($text);
        $actors = [];
        $hasAdmin = false;

        foreach ($normalizedLines as $line) {
            $line = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
            if (empty($line)) {
                continue;
            }

            $name = '';
            $role = '';

            if (str_contains($line, ':')) {
                [$n, $r] = explode(':', $line, 2);
                $name = trim($n);
                $role = trim($r);
            } elseif (str_contains($line, ' - ')) {
                [$n, $r] = explode(' - ', $line, 2);
                $name = trim($n);
                $role = trim($r);
            } elseif (preg_match('/^(.*?)\s*\((.*?)\)$/', $line, $matches)) {
                $name = trim($matches[1]);
                $role = trim($matches[2]);
            } else {
                $name = $line;
                $role = '';
            }

            if (empty($name)) {
                continue;
            }

            $lower = strtolower($name . ' ' . $role);
            if (str_contains($lower, 'admin')) {
                $hasAdmin = true;
            }

            if (empty($role) || strlen($role) < 10) {
                if (str_contains($lower, 'pembeli') || str_contains($lower, 'buyer') || str_contains($lower, 'konsumen') || str_contains($lower, 'pelanggan')) {
                    $role = $isEn
                        ? "Explore {$businessName} public catalog, perform structured search & filters, submit inquiries/orders, and track transaction status in real-time."
                        : "Menjelajahi katalog {$businessName}, melakukan pencarian & filter spesifikasi mendalam, mengajukan penawaran/inquiry, serta menerima notifikasi status pesanan secara real-time.";
                    $badge = $isEn ? 'BUYER / CLIENT' : 'PEMBELI / KLIEN';
                    $permissions = $isEn
                        ? ['Public Catalog', 'Search Filters', 'Submit Order / Inquiry', 'Order History']
                        : ['Katalog Publik', 'Filter Pencarian', 'Ajukan Order / Inquiry', 'Riwayat Pesanan'];
                } elseif (str_contains($lower, 'member') || str_contains($lower, 'mitra') || str_contains($lower, 'partner') || str_contains($lower, 'agen') || str_contains($lower, 'penjual')) {
                    $role = $isEn
                        ? "Manage verified partner profile, publish listings/assets, monitor performance metrics, and handle inbound inquiries."
                        : "Mengelola profil mitra terverifikasi, mempublikasikan listing/produk, memantau analitik performa komisi, dan berinteraksi langsung dengan calon pembeli.";
                    $badge = $isEn ? 'PARTNER / MEMBER' : 'MITRA / MEMBER';
                    $permissions = $isEn
                        ? ['Profile Management', 'Publish Listings', 'Partner Cockpit', 'Inbound Inquiries']
                        : ['Manajemen Profil', 'Posting Listing / Aset', 'Dasbor Mitra', 'Chat / Inquiry Inbound'];
                } elseif (str_contains($lower, 'superadmin') || str_contains($lower, 'owner') || str_contains($lower, 'direktur')) {
                    $role = $isEn
                        ? "Full control over {$businessName} platform configuration, Spatie Shield RBAC permissions, forensic audit trail, and billing settlement."
                        : "Memegang kendali penuh atas konfigurasi platform {$businessName}, manajemen peran Spatie Shield RBAC, audit log forensik, dan rekonsiliasi keuangan.";
                    $badge = 'SUPERADMIN';
                    $permissions = $isEn
                        ? ['Full Access', 'System Config', 'RBAC Shield', 'Audit Log', 'Billing Settlement']
                        : ['Full Access', 'Konfigurasi Sistem', 'RBAC Shield', 'Audit Log', 'Billing Settlement'];
                } elseif (str_contains($lower, 'staff') || str_contains($lower, 'operator') || str_contains($lower, 'admin')) {
                    $role = $isEn
                        ? "Process daily operational submissions, verify document validity, handle service inquiries, and export compliance reports."
                        : "Memproses validasi berkas pengguna harian, memverifikasi kelaikan transaksi, menindaklanjuti keluhan layanan, dan mengekspor laporan berkala.";
                    $badge = 'OPERATOR';
                    $permissions = $isEn
                        ? ['Submission Review', 'Document Verification', 'PDF/Excel Export', 'Operational Updates']
                        : ['Review Data Masuk', 'Verifikasi Dokumen', 'Ekspor PDF/Excel', 'Update Status Operasional'];
                } elseif (str_contains($lower, 'tamu') || str_contains($lower, 'guest') || str_contains($lower, 'pengunjung')) {
                    $role = $isEn
                        ? "Access landing page, review general product information, and initiate account registration."
                        : "Mengakses halaman pendaratan (landing page), mengecek informasi umum, dan melakukan registrasi akun awal.";
                    $badge = $isEn ? 'GUEST / PUBLIC' : 'GUEST / PUBLIK';
                    $permissions = $isEn
                        ? ['Landing Page Access', 'Documentation', 'Account Registration']
                        : ['Akses Landing Page', 'Baca Dokumentasi', 'Registrasi Akun'];
                } else {
                    $role = $isEn
                        ? "Registered user operating authorized interactive features within {$businessName}."
                        : "Pengguna terdaftar dengan hak akses interaktif terhadap modul sistem {$businessName} sesuai batasan otorisasi.";
                    $badge = $isEn ? 'USER' : 'PENGGUNA';
                    $permissions = $isEn
                        ? ['Core Modules', 'Form Submission', 'Notification Stream']
                        : ['Akses Modul Inti', 'Input Form', 'Lihat Notifikasi'];
                }
            } else {
                if (str_contains($lower, 'superadmin')) {
                    $badge = 'SUPERADMIN';
                    $permissions = $isEn ? ['Full Access', 'System Config', 'RBAC Shield', 'Audit Log'] : ['Full Access', 'Konfigurasi Sistem', 'RBAC Shield', 'Audit Log'];
                } elseif (str_contains($lower, 'admin') || str_contains($lower, 'staff') || str_contains($lower, 'operator')) {
                    $badge = $isEn ? 'ADMIN / STAFF' : 'ADMIN / STAFF';
                    $permissions = $isEn ? ['Review Submissions', 'Document Verification', 'Report Export'] : ['Review Data', 'Verifikasi Berkas', 'Ekspor Laporan'];
                } elseif (str_contains($lower, 'mitra') || str_contains($lower, 'member')) {
                    $badge = $isEn ? 'PARTNER / MEMBER' : 'MITRA / MEMBER';
                    $permissions = $isEn ? ['Partner Dashboard', 'Manage Listings', 'Inbound Inquiries'] : ['Dasbor Mitra', 'Kelola Listing', 'Inquiry Inbound'];
                } else {
                    $badge = $isEn ? 'CLIENT / USER' : 'KLIEN / USER';
                    $permissions = $isEn ? ['Form Access', 'Status Tracker', 'Transaction History'] : ['Akses Formulir', 'Lihat Status', 'Riwayat Transaksi'];
                }
            }

            $actors[] = [
                'name' => $name,
                'title' => $name,
                'role' => $role,
                'desc' => $role,
                'badge' => $badge,
                'permissions' => $permissions,
            ];
        }

        if (empty($actors)) {
            $actors = [
                [
                    'name' => 'Superadmin',
                    'title' => 'Superadmin',
                    'role' => $isEn ? 'Full control over system configuration, audit logs, role authorizations, and platform data.' : 'Akses penuh seluruh konfigurasi, audit log, otorisasi peran, dan data sistem.',
                    'desc' => $isEn ? 'Full control over system configuration, audit logs, role authorizations, and platform data.' : 'Akses penuh seluruh konfigurasi, audit log, otorisasi peran, dan data sistem.',
                    'badge' => 'SUPERADMIN',
                    'permissions' => ['Full Control', 'System Config', 'RBAC Shield', 'Audit Log'],
                ],
                [
                    'name' => $isEn ? 'Staff / Operator' : 'Staff / Operator',
                    'title' => $isEn ? 'Staff / Operator' : 'Staff / Operator',
                    'role' => $isEn ? 'Process daily transactional submissions, verify document compliance, and export periodical reports.' : 'Memproses data masuk harian, verifikasi kelayakan berkas, dan ekspor laporan berkala.',
                    'desc' => $isEn ? 'Process daily transactional submissions, verify document compliance, and export periodical reports.' : 'Memproses data masuk harian, verifikasi kelayakan berkas, dan ekspor laporan berkala.',
                    'badge' => 'OPERATOR',
                    'permissions' => ['Data Processing', 'Document Verification', 'Report Export'],
                ],
                [
                    'name' => $isEn ? 'User / Client' : 'Pengguna / Klien',
                    'title' => $isEn ? 'User / Client' : 'Pengguna / Klien',
                    'role' => $isEn ? 'Access public portal features, submit structured forms, and track transaction histories.' : 'Mengakses fitur publik/portal, mengisi data formulir, dan melihat status riwayat transaksi.',
                    'desc' => $isEn ? 'Access public portal features, submit structured forms, and track transaction histories.' : 'Mengakses fitur publik/portal, mengisi data formulir, dan melihat status riwayat transaksi.',
                    'badge' => 'CLIENT',
                    'permissions' => ['Portal Access', 'Form Submission', 'Notification Stream'],
                ],
            ];
            $hasAdmin = true;
        }

        // Always ensure Superadmin is present for enterprise RBAC compliance
        if (!$hasAdmin) {
            array_unshift($actors, [
                'name' => $isEn ? 'Platform Superadmin' : 'Superadmin Platform',
                'title' => $isEn ? 'Platform Superadmin' : 'Superadmin Platform',
                'role' => $isEn ? "Full governance over {$businessName}, account verifications, module configurations, transaction oversight, and audit logs." : "Kendali penuh atas seluruh tata kelola {$businessName}, verifikasi akun pengguna, pengaturan modul, pemantauan transaksi, dan audit trail.",
                'desc' => $isEn ? "Full governance over {$businessName}, account verifications, module configurations, transaction oversight, and audit logs." : "Kendali penuh atas seluruh tata kelola {$businessName}, verifikasi akun pengguna, pengaturan modul, pemantauan transaksi, dan audit trail.",
                'badge' => 'SUPERADMIN',
                'permissions' => $isEn ? ['Full Root Access', 'Global Config', 'RBAC Security Shield', 'Forensic Audit'] : ['Akses Penuh / Root', 'Konfigurasi Global', 'RBAC Security Shield', 'Audit Forensik'],
            ]);
        }

        return $actors;
    }


    /**
     * Parse workflow text into sequenced step cards with deep engineering parameters.
     */
    public static function parseWorkflow(string $text, string $businessName = '', bool $isEn = false): array
    {
        $rawSteps = \App\Support\BlueprintPdfHelper::extractItems($text);

        $steps = [];
        foreach ($rawSteps as $s) {
            $cleaned = trim($s, " \t\n\r\0\x0B-•*1234567890.)");
            if (!empty($cleaned)) {
                $crafted = BlueprintDiscoveryService::craftWorkflowStepAction($cleaned, 'custom_portal', $isEn);
                if (!empty($crafted)) {
                    $steps[] = $crafted;
                }
            }
        }

        // 2. Filter out introductory preambles (e.g. "saya mau buat website berupa...", "Konsep antara lain...")
        if (count($steps) > 1) {
            $firstLower = strtolower($steps[0]);
            if (str_contains($firstLower, 'buat website') || str_contains($firstLower, 'konsep antara lain') || str_contains($firstLower, 'berupa') || str_contains($firstLower, 'atas nama') || str_contains($firstLower, 'build website') || str_contains($firstLower, 'i want')) {
                array_shift($steps);
            }
        }

        // 3. If it resulted in only 1 long step, intelligently split by numbered patterns or transitional keywords
        if (count($steps) === 1 && strlen($steps[0]) > 35) {
            $textSingle = $steps[0];
            $splitParts = preg_split('/(?:\r?\n|\s+)(?=\d+[\.\)]\s+)|\s*;\s*|,\s*(?:lalu\s+|kemudian\s+|setelah itu\s+|selanjutnya\s+|then\s+|after that\s+)?|\s+(?:lalu|kemudian|setelah itu|selanjutnya|then|after that)\s+/i', $textSingle);
            
            $refined = [];
            foreach ($splitParts as $part) {
                $part = trim($part, " \t\n\r\0\x0B-•*1234567890.),");
                if (strlen($part) > 3) {
                    $refined[] = BlueprintDiscoveryService::craftWorkflowStepAction($part, 'custom_portal', $isEn);
                }
            }
            if (count($refined) > 1) {
                $steps = $refined;
            }
        }

        $stages = [];
        $index = 1;
        foreach ($steps as $step) {
            $step = BlueprintDiscoveryService::craftWorkflowStepAction($step, 'custom_portal', $isEn);
            if (empty($step)) {
                continue;
            }

            $lower = strtolower($step);
            $actor = $isEn ? 'User / Visitor' : 'Pengguna / Pengunjung';
            $badge = 'INTERACTION';
            $type = 'client';

            $description = $isEn
                ? "User interaction within {$businessName} module with validated data transmission."
                : "Interaksi pengguna pada modul {$businessName} dengan transmisi data tervalidasi.";
            $trigger = $isEn
                ? "User executes action '{$step}' through the web interface."
                : "Pengguna menjalankan aksi '{$step}' melalui antarmuka web.";
            $systemProcess = $isEn
                ? "Controller validates FormRequest payload, executes indexed Eloquent query, and updates state machine."
                : "Controller memvalidasi request FormRequest, menjalankan query Eloquent berindeks, dan memperbarui state.";
            $outputState = $isEn
                ? "Interface updates reactively (HTTP 200 OK) with visual confirmation."
                : "Antarmuka diperbarui secara reaktif (HTTP 200 OK) dengan notifikasi visual.";
            $edgeCase = $isEn
                ? "Validation errors handled inline with automated retry mechanism."
                : "Penanganan validasi gagal dengan error state inline dan retry mechanism.";

            if (str_contains($lower, 'buka') || str_contains($lower, 'katalog') || str_contains($lower, 'lihat') || str_contains($lower, 'daftar') || str_contains($lower, 'browse') || str_contains($lower, 'catalog') || str_contains($lower, 'view') || str_contains($lower, 'explore')) {
                $actor = $isEn ? 'User / Visitor' : 'Pengguna / Pengunjung';
                $badge = 'DISCOVERY';
                $type = 'client';
                $description = $isEn
                    ? "User accesses {$businessName} public portal; system renders dynamic catalog with skeleton loaders and pre-fetches CDN assets."
                    : "Pengguna mengakses portal publik {$businessName}, sistem memuat katalog dinamis dengan skeleton loader, dan melakukan prefetching aset CDN.";
                $trigger = $isEn
                    ? "Visitor opens catalog URL on desktop or mobile device."
                    : "Pengunjung membuka URL beranda / katalog dari peramban desktop maupun ponsel.";
                $systemProcess = $isEn
                    ? "HTTP GET route invokes controller, reads Cache::rememberForever() for CMS & Schema.org JSON-LD, renders reactive components."
                    : "HTTP GET route memanggil Controller, membaca Cache::rememberForever() untuk konfigurasi CMS & Schema.org JSON-LD, render Blade & React Islands.";
                $outputState = $isEn
                    ? "Instant sub-100ms TTFB page load with optimized Core Web Vitals (LCP < 1.2s, CLS = 0)."
                    : "Halaman ter-render instan (<100ms TTFB) dengan Core Web Vitals optimal (LCP < 1.2s, CLS = 0).";
                $edgeCase = $isEn
                    ? "Graceful offline fallback displayed via Service Worker if network connection drops."
                    : "Jika terjadi gangguan jaringan, service worker menampilkan graceful offline fallback dan tombol muat ulang.";
            } elseif (str_contains($lower, 'filter') || str_contains($lower, 'cari') || str_contains($lower, 'search') || str_contains($lower, 'advance')) {
                $actor = $isEn ? 'System / Search Engine' : 'Sistem / Search Engine';
                $badge = 'QUERY_FILTER';
                $type = 'filter';
                $description = $isEn
                    ? "User applies advanced filter criteria; system executes sub-10ms indexed queries."
                    : "Pengguna menerapkan parameter filter lanjutan (kategori, rentang budget, spesifikasi). Sistem mengeksekusi query database terindeks secara instan.";
                $trigger = $isEn
                    ? "Keystroke input on search field or interactive filter chip selection."
                    : "Input teks pada kolom pencarian atau interaksi toggle/chip filter oleh pengguna.";
                $systemProcess = $isEn
                    ? "Keyset Cursor Pagination O(1) executed with PostgreSQL B-Tree indexes and 300ms debounce."
                    : "Eksekusi Keyset Cursor Pagination O(1) dengan PostgreSQL B-Tree Indexing, debounce 300ms untuk menekan beban server.";
                $outputState = $isEn
                    ? "Filtered records update reactively without full page reloads."
                    : "Daftar entitas hasil filter diperbarui secara reaktif tanpa reload halaman penuh (Full Page Refresh = 0).";
                $edgeCase = $isEn
                    ? "Displays smart recommendations and 'Reset Filters' CTA if 0 records match."
                    : "Jika hasil pencarian 0 record, sistem menampilkan rekomendasi cerdas dan tombol 'Reset Semua Filter'.";
            } elseif (str_contains($lower, 'langganan') || str_contains($lower, 'berlangganan') || str_contains($lower, 'order') || str_contains($lower, 'beli') || str_contains($lower, 'transaksi') || str_contains($lower, 'pay') || str_contains($lower, 'checkout') || str_contains($lower, 'subscribe')) {
                $actor = $isEn ? 'Payment & Transaction Engine' : 'Payment & Transaction Engine';
                $badge = 'TRANSACTION';
                $type = 'billing';
                $description = $isEn
                    ? "User initiates booking or transaction; system locks calculation, issues ULID invoice, and opens payment gateway."
                    : "Pengguna memilih paket berlangganan atau mengajukan transaksi. Sistem mengunci kalkulasi harga, menerbitkan invoice ber-ULID, dan membuka gateway pembayaran.";
                $trigger = $isEn
                    ? "User clicks 'Checkout' or 'Pay Now' button."
                    : "Klik tombol 'Berlangganan Sekarang' atau 'Checkout' oleh pengguna terautentikasi.";
                $systemProcess = $isEn
                    ? "Issues new transactional ULID, verifies inventory with ACID transaction, and initiates payment gateway handshake."
                    : "Penerbitan ULID transaksi baru, verifikasi kuota/stok dengan ACID database transaction, handshake API ke Payment Gateway (Midtrans Snap).";
                $outputState = $isEn
                    ? "Payment modal renders on client screen; database registers PENDING_PAYMENT status."
                    : "Snap payment modal terbuka di layar klien; database mencatat status PENDING_PAYMENT.";
                $edgeCase = $isEn
                    ? "Idempotency key prevents duplicate charges if client clicks repeatedly."
                    : "Idempotency key mencegah double charge jika klien menekan tombol bayar berulang kali.";
            } elseif (str_contains($lower, 'notifikasi') || str_contains($lower, 'email') || str_contains($lower, 'alert') || str_contains($lower, 'wa') || str_contains($lower, 'notify')) {
                $actor = $isEn ? 'Notification Engine' : 'Notification Engine';
                $badge = 'NOTIFICATION';
                $type = 'notification';
                $description = $isEn
                    ? "System dispatches automated notifications via transactional email and WhatsApp."
                    : "Sistem mendistribusikan notifikasi status transaksi otomatis ke email profesional pengguna (Resend API) dan nomor WhatsApp.";
                $trigger = $isEn
                    ? "Domain model event fired (e.g. OrderSettled, InquiryCreated)."
                    : "Event model tersimpan (OrderSettled, InquiryCreated, atau StatusUpdated).";
                $systemProcess = $isEn
                    ? "Dispatches asynchronous job to Redis queue workers, renders responsive template, retries up to 3x."
                    : "Dispatched asynchronous Job ke Redis Queue Workers, render email template responsif, fallback retry 3x dengan backoff eksponensial.";
                $outputState = $isEn
                    ? "Notification delivered to user contact channels within <5 seconds."
                    : "Notifikasi terkirim ke inbox email dan nomor kontak pengguna dalam tempo <5 detik.";
                $edgeCase = $isEn
                    ? "Delivery failures recorded in notification_logs with automated resend capability in admin."
                    : "Pencatatan kegagalan transmisi ke tabel `notification_logs` dan tombol Resend otomatis di panel admin.";
            } elseif (str_contains($lower, 'validasi') || str_contains($lower, 'simpan') || str_contains($lower, 'database') || str_contains($lower, 'isi') || str_contains($lower, 'save') || str_contains($lower, 'store')) {
                $actor = $isEn ? 'PostgreSQL / Laravel ORM' : 'PostgreSQL / Laravel ORM';
                $badge = 'DATABASE';
                $type = 'database';
                $description = $isEn
                    ? "Structured form data persisted with strict validation, anti-XSS sanitization, and ULID foreign key integrity."
                    : "Penyimpanan data formulir terstruktur dengan validasi ketat, sanitasi anti-XSS, dan penguncian relasi foreign key ber-ULID.";
                $trigger = $isEn
                    ? "Form transmission via HTTP POST / Action Handler."
                    : "Pengiriman formulir via HTTP POST / Action Handler.";
                $systemProcess = $isEn
                    ? "Sanitizes input payload, hashes sensitive fields, and executes atomic write to PostgreSQL with ULID primary key."
                    : "Sanitasi payload input, hashing berkas sensitif, penyimpanan ke tabel PostgreSQL Strict dengan ULID primary key.";
                $outputState = $isEn
                    ? "Toast confirmation appears; audit trail records timestamp and client IP."
                    : "Flash session atau toast notifikasi sukses muncul, audit trail log mencatat IP dan timestamp pengirim.";
                $edgeCase = $isEn
                    ? "HTTP 422 Unprocessable Entity response with specific field error mapping on invalid input."
                    : "Penolakan instan dengan HTTP 422 Unprocessable Entity dan pemetaan pesan error spesifik jika input tidak valid.";
            } elseif (str_contains($lower, 'admin') || str_contains($lower, 'verifikasi') || str_contains($lower, 'dasbor') || str_contains($lower, 'approval') || str_contains($lower, 'verify') || str_contains($lower, 'review')) {
                $actor = $isEn ? 'Administrator / Operator' : 'Administrator / Operator';
                $badge = 'APPROVAL';
                $type = 'admin';
                $description = $isEn
                    ? "Management reviews and verifies inbound submissions through the Filament v5 administrative cockpit."
                    : "Pengelola meninjau dan memverifikasi data transaksi atau berkas masuk melalui panel kendali Filament v5 enterprise.";
                $trigger = $isEn
                    ? "Admin accesses management dashboard at /admin."
                    : "Admin membuka antarmuka manajemen data di dashboard `/admin`.";
                $systemProcess = $isEn
                    ? "Verifies RBAC authorization, checks document integrity, and executes approval with database lock."
                    : "Verifikasi otorisasi Spatie Shield RBAC, evaluasi integritas dokumen, eksekusi approval dengan database lock.";
                $outputState = $isEn
                    ? "Record status updates to APPROVED / ACTIVE and triggers next workflow phase."
                    : "Status record diperbarui menjadi APPROVED / ACTIVE, memicu event notifikasi tahap berikutnya.";
                $edgeCase = $isEn
                    ? "Rejection requires mandatory explanation note forwarded to client."
                    : "Jika data ditolak, admin wajib menyertakan alasan penolakan yang otomatis diteruskan ke pengguna.";
            }

            $stages[] = [
                'step' => $index++,
                'action' => $step,
                'description' => $description,
                'actor' => $actor,
                'badge' => $badge,
                'type' => $type,
                'trigger' => $trigger,
                'system_process' => $systemProcess,
                'output_state' => $outputState,
                'edge_case' => $edgeCase,
            ];
        }

        if (empty($stages)) {
            $stages = [
                [
                    'step' => 1,
                    'action' => $isEn ? 'Portal Access & Account Authentication' : 'Akses Portal & Registrasi Akun',
                    'description' => $isEn ? 'User accesses application and authenticates with credentials.' : 'Pengguna membuka aplikasi dan memasukkan kredensial identitas terotentikasi.',
                    'actor' => $isEn ? 'User / Visitor' : 'Pengguna',
                    'badge' => 'AUTH',
                    'type' => 'client',
                    'trigger' => $isEn ? 'User accesses login / register page.' : 'Pengguna mengakses halaman login / register.',
                    'system_process' => $isEn ? 'Credential sanitization, bcrypt verification, encrypted session issuance.' : 'Sanitasi kredensial, verifikasi password bcrypt, penerbitan session terenkripsi.',
                    'output_state' => $isEn ? 'User directed to primary dashboard with valid session token.' : 'Pengguna diarahkan ke dasbor utama dengan token sesi valid.',
                    'edge_case' => $isEn ? 'Rate limiter restricts login attempts to max 5x per minute.' : 'Rate limiter membatasi percobaan login maksimal 5x per menit.',
                ],
                [
                    'step' => 2,
                    'action' => $isEn ? 'Service Selection & Structured Form Submission' : 'Pengisian Data / Form Transaksi',
                    'description' => $isEn ? 'Client-side validation and structured data transmission to Laravel backend.' : 'Validasi sisi klien dan transmisi data ke backend Laravel.',
                    'actor' => $isEn ? 'Client / System' : 'Klien / Sistem',
                    'badge' => 'INPUT',
                    'type' => 'client',
                    'trigger' => $isEn ? 'User completes form and clicks submit.' : 'Pengguna mengisi formulir dan menekan submit.',
                    'system_process' => $isEn ? 'FormRequest validation, XSS sanitization, ACID database write.' : 'Validasi FormRequest, sanitasi XSS, penyimpanan ACID database.',
                    'output_state' => $isEn ? 'Success toast displays and new ULID record is persisted.' : 'Toast sukses muncul dan record baru ber-ULID terbit.',
                    'edge_case' => $isEn ? 'HTTP 422 rejection if payload fails validation criteria.' : 'Penolakan HTTP 422 jika format data tidak sesuai spesifikasi.',
                ],
                [
                    'step' => 3,
                    'action' => $isEn ? 'Automated Verification & Notification Dispatch' : 'Verifikasi & Notifikasi Otomatis',
                    'description' => $isEn ? 'System dispatches instant notification via email and records audit log.' : 'Sistem mengirimkan konfirmasi instan via email Resend dan mencatat audit trail.',
                    'actor' => $isEn ? 'Notification Engine' : 'Notification Engine',
                    'badge' => 'NOTIFICATION',
                    'type' => 'notification',
                    'trigger' => $isEn ? 'Model saved domain event dispatched.' : 'Event model tersimpan di database.',
                    'system_process' => $isEn ? 'Queue worker processes asynchronous email dispatch.' : 'Queue worker memproses pengiriman email asinkron.',
                    'output_state' => $isEn ? 'Confirmation email arrives in user inbox within seconds.' : 'Email konfirmasi mendarat di inbox pengguna dalam hitungan detik.',
                    'edge_case' => $isEn ? 'Automatic 3x retry if notification gateway times out.' : 'Retry otomatis 3x jika koneksi API gateway mengalami timeout.',
                ],
                [
                    'step' => 4,
                    'action' => $isEn ? 'Operational Cockpit Review & Approval' : 'Approval & Manajemen Dasbor Admin',
                    'description' => $isEn ? 'Staff processes submissions through high-speed Filament tables.' : 'Pengelola memproses data melalui tabel Filament berkecepatan tinggi.',
                    'actor' => $isEn ? 'Admin / Operator' : 'Admin / Operator',
                    'badge' => 'APPROVAL',
                    'type' => 'admin',
                    'trigger' => $isEn ? 'Operator loads new submissions queue panel.' : 'Operator membuka panel antrean data baru.',
                    'system_process' => $isEn ? 'RBAC validation, file inspection, and transaction status update.' : 'Pengecekan RBAC, review berkas, pembaruan status transaksi.',
                    'output_state' => $isEn ? 'Status transitions to VERIFIED with forensic audit log.' : 'Status berubah menjadi VERIFIED dan tercatat di audit log.',
                    'edge_case' => $isEn ? 'Audit log records explicit rejection reason if verification is denied.' : 'Log audit forensik mencatat alasan penolakan jika verifikasi dibatalkan.',
                ],
            ];
        }

        return $stages;
    }


    /**
     * Generate granular engineering specifications for each feature.
     */
    public static function generateDetailedFeatureSpecs(
        array $rawItems, 
        string $businessName, 
        array $actorItems, 
        string $tier = 'mvp',
        ?VisionBlueprint $blueprint = null,
        array $erdTables = [],
        ?string $preferredAiProvider = null
    ): array
    {
        // 1. Attempt Dynamic AI Reasoning from Blueprint & Document Corpus if Blueprint provided
        if ($blueprint) {
            try {
                $aiSpecs = self::synthesizeFeatureSpecsWithAi(
                    $businessName,
                    $rawItems,
                    $actorItems,
                    $blueprint,
                    $erdTables,
                    $tier,
                    $preferredAiProvider
                );
                if (!empty($aiSpecs) && is_array($aiSpecs) && count($aiSpecs) >= count($rawItems)) {
                    return $aiSpecs;
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::info("AI Feature Spec synthesis fallback to deterministic heuristics: " . $e->getMessage());
            }
        }

        // 2. Deterministic Heuristic Fallback Engine (with concrete ERD column injection)
        $specs = [];
        $index = 1;
        $prefix = ($tier === 'mvp') ? 'MVP' : 'ROADMAP';

        $primaryActor = $actorItems[0]['name'] ?? 'Superadmin';
        $clientActor = $actorItems[count($actorItems) - 1]['name'] ?? 'Pengguna / Klien';

        foreach ($rawItems as $item) {
            $title = $item['title'] ?? ('Fitur ' . $index);
            $desc = $item['desc'] ?? 'Spesifikasi fungsional inti untuk operasional sistem.';
            $slug = Str::slug($title);
            $cleanSlug = strtoupper(substr(str_replace('-', '_', $slug), 0, 14));
            $featureId = sprintf('FEAT-%s-%02d-%s', $prefix, $index, $cleanSlug);

            $analysis = self::analyzeFeatureDomain($title, $desc, $businessName, $clientActor, $primaryActor, $index, $prefix, $erdTables);

            // Compute Story Points, Complexity Sizing, and Sprint Phase
            $storyPoints = 3;
            $complexityLabel = 'Standard (3 SP)';
            $sprintPhase = 'Sprint 1 (Fondasi & CRUD)';

            $cat = $analysis['category'] ?? '';
            if (str_contains($cat, 'SECURITY') || str_contains($cat, 'PAYMENT') || str_contains($cat, 'INTEGRATION')) {
                $storyPoints = 5;
                $complexityLabel = 'High (5 SP)';
                $sprintPhase = 'Sprint 2 (Integrasi & Mutasi)';
            } elseif (str_contains($cat, 'CANVAS') || str_contains($cat, 'AI') || str_contains($cat, 'NOTIFICATION')) {
                $storyPoints = 8;
                $complexityLabel = 'Complex (8 SP)';
                $sprintPhase = 'Sprint 3 (Fitur Lanjutan & Async)';
            }

            $modelName = Str::studly(Str::singular(explode('-', $slug)[0] ?? 'Record'));
            $boundedFiles = $analysis['target_files'] ?? [
                "app/Models/{$modelName}.php",
                "database/migrations/xxxx_create_" . strtolower(Str::plural($modelName)) . "_table.php",
                "app/Filament/Resources/{$modelName}Resource.php",
                "resources/views/" . strtolower($cleanSlug) . "/index.blade.php",
            ];

            $specs[] = [
                'id' => $featureId,
                'index' => $index,
                'tier' => $tier,
                'title' => $title,
                'desc' => $desc,
                'category' => $analysis['category'],
                'category_label' => $analysis['category_label'],
                'story_points' => $storyPoints,
                'complexity_label' => $complexityLabel,
                'sprint_phase' => $sprintPhase,
                'target_files' => $boundedFiles,
                'related_table' => $analysis['related_table'] ?? null,
                'related_table_data' => $analysis['related_table_data'] ?? null,
                'user_story' => $analysis['user_story'],
                'acceptance_criteria' => $analysis['acceptance_criteria'],
                'frontend' => $analysis['frontend'],
                'backend' => $analysis['backend'],
                'integration' => $analysis['integration'],
                'code_agent_directive' => $analysis['code_agent_directive'],
            ];

            $index++;
        }

        return $specs;
    }


    /**
     * Synthesize granular engineering specifications with deep LLM reasoning from uploaded documents / corpus.
     */
    public static function synthesizeFeatureSpecsWithAi(
        string $businessName,
        array $rawItems,
        array $actorItems,
        VisionBlueprint $blueprint,
        array $erdTables,
        string $tier = 'mvp',
        ?string $preferredAiProvider = null
    ): array {
        $masalah = $blueprint->masalah_utama ?: 'Otomatisasi proses bisnis operasional.';
        $tujuan = $blueprint->tujuan_utama ?: 'Efisiensi dan akurasi data.';
        $slug = $blueprint->slug ?: Str::slug($businessName);

        // Fetch corpus if available from disk or metadata
        $corpusContent = "";
        try {
            $corpusFile = storage_path("app/projects/{$slug}/markdown/synthesized_corpus.md");
            if (file_exists($corpusFile)) {
                $corpusContent = mb_substr(file_get_contents($corpusFile), 0, 8000);
            }
        } catch (\Throwable $e) {}

        if (empty($corpusContent) && !empty($blueprint->user_metadata['raw_corpus'])) {
            $corpusContent = mb_substr((string) $blueprint->user_metadata['raw_corpus'], 0, 8000);
        }

        $featuresStr = implode("\n", array_map(function($idx, $item) {
            return ($idx + 1) . ". " . ($item['title'] ?? '') . ": " . ($item['desc'] ?? '');
        }, array_keys($rawItems), $rawItems));

        $actorsStr = implode("\n", array_map(function($idx, $act) {
            return ($idx + 1) . ". " . ($act['name'] ?? '') . " (" . ($act['badge'] ?? '') . "): " . ($act['role'] ?? '');
        }, array_keys($actorItems), $actorItems));

        $erdSummaryStr = implode("\n", array_map(function($t) {
            $colNames = implode(', ', array_map(fn($c) => $c['name'] . ' (' . $c['type'] . ')', array_slice($t['columns'] ?? [], 0, 8)));
            return "- Table `{$t['name']}`: {$colNames}";
        }, $erdTables));

        $prefix = ($tier === 'mvp') ? 'MVP' : 'ROADMAP';

        $prompt = <<<PROMPT
You are a Principal Software Architect and Product Specification Engineer specializing in Laravel 13 (PHP 8.5+), Filament v5, and PostgreSQL 16 Strict ULID.
Analyze this system specification and attached client documentation:
Project: "{$businessName}"
Problem Statement: "{$masalah}"
Success Metrics: "{$tujuan}"
Features to elaborate:
{$featuresStr}
System Actors & RBAC:
{$actorsStr}
Database Tables Available:
{$erdSummaryStr}
Attached Brief / Corpus Context:
{$corpusContent}

Task:
Decompose each feature into a deep, rigorous, domain-specific engineering specification that 100% mirrors the client's actual business domain and documents.
Avoid generic descriptions. Use concrete business terms, actual domain logic, real validation rules, and accurate edge cases.

MANDATORY RULES:
1. Every feature must have a concrete User Story: "Sebagai [Aktor], saya ingin [Aksi] sehingga [Manfaat Bisnis]".
2. Every feature must have 3 concrete Acceptance Criteria scenarios (Given-When-Then) reflecting the true business domain.
3. Every feature must specify a primary table name from the Database Tables list.
4. STATE-OF-THE-ART ARCHITECTURE & MODERN IMPLEMENTATION RULE:
   Whenever a feature involves AI, search, matching, recommendations, catalog filtering, or document processing:
   - Enforce the 18 RAG Concepts (Brij Kishore Pandey): Hybrid Search (combining PostgreSQL pgvector HNSW cosine similarity + PostgreSQL full-text search tsvector BM25), Cross-Encoder Reranking, Chunking with 50-token overlap, Parent-Child / Contextual Retrieval, Corrective RAG (CRAG) for ambiguity verification, and Grounded Citations.
   - Enforce the 20 Agentic AI Concepts (Balawant Kadam): Tool Use (function calling), Model Context Protocol (MCP), Agentic Workflows, Guardrails (PII filter & UU PDP compliance), and Observability (token & latency telemetry).
   - Primitive SQL LIKE queries and ungrounded hallucinating prompts are strictly prohibited.
5. Output JSON Array ONLY with schema:
[
  {
    "title": "Exact or refined feature title",
    "desc": "Detailed functional explanation",
    "category": "SECURITY_RBAC|DISCOVERY_CATALOG|DATA_INTAKE_TRANSACTION|ADMIN_GOVERNANCE|EXPORT_REPORTING|NOTIFICATION_GATEWAY|CORE_FEATURE",
    "category_label": "Indonesian readable category label",
    "story_points": 3,
    "complexity_label": "Standard (3 SP)|High (5 SP)|Complex (8 SP)",
    "sprint_phase": "Sprint 1 (Fondasi & CRUD)|Sprint 2 (Integrasi & Mutasi)|Sprint 3 (Fitur Lanjutan & Async)",
    "related_table": "table_name_from_db_list",
    "target_files": ["app/Models/Example.php", "database/migrations/xxxx_create_examples_table.php", "app/Filament/Resources/ExampleResource.php", "resources/views/example/index.blade.php"],
    "user_story": "Sebagai ...",
    "acceptance_criteria": [
      {
        "scenario": "Scenario title",
        "given": "Given condition",
        "when": "When action occurs",
        "then": "Then expected outcome"
      }
    ],
    "frontend": {
      "design_tokens": "Styling tokens and theme rules",
      "states": "Interactive states (loading, toast feedback)",
      "components": ["Component1.blade.php", "Component2.js"],
      "tasks": ["Task 1", "Task 2"]
    },
    "backend": {
      "model_and_migration": "Model and schema notes",
      "scalability_guardrail": "Keyset cursor pagination O(1) and index strategy",
      "tasks": ["Task 1", "Task 2"]
    },
    "integration": {
      "endpoint": "/api/v1/resource-name",
      "middleware": ["web", "auth"],
      "request_schema": "{\"field\": \"value\"}",
      "response_schema": "{\"success\": true, \"data\": {}}",
      "tasks": ["Task 1", "Task 2"]
    },
    "mission": "Concrete mission directive describing what the AI code agent must implement."
  }
]
PROMPT;

        $systemInstruction = "You are the world's leading Software Architect and Product Specification Engineer. You output valid JSON array ONLY, zero markdown fences, zero conversational preamble.";

        $aiRes = \App\Services\Ai\MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'discovery', $preferredAiProvider);

        if (empty($aiRes['text'])) {
            return [];
        }

        $jsonText = trim($aiRes['text']);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $jsonText, $m)) {
            $jsonText = trim($m[1]);
        }

        $decoded = json_decode($jsonText, true);
        if (!is_array($decoded) || empty($decoded)) {
            return [];
        }

        $specs = [];
        $index = 1;

        foreach ($decoded as $item) {
            $title = $item['title'] ?? ('Fitur ' . $index);
            $desc = $item['desc'] ?? 'Spesifikasi fungsional inti untuk operasional sistem.';
            $slug = Str::slug($title);
            $cleanSlug = strtoupper(substr(str_replace('-', '_', $slug), 0, 14));
            $featureId = sprintf('FEAT-%s-%02d-%s', $prefix, $index, $cleanSlug);

            $relatedTableName = $item['related_table'] ?? '';
            $relatedTableObj = null;
            if (!empty($relatedTableName)) {
                foreach ($erdTables as $tbl) {
                    if (strtolower($tbl['name'] ?? '') === strtolower($relatedTableName)) {
                        $relatedTableObj = $tbl;
                        break;
                    }
                }
            }
            if (!$relatedTableObj) {
                $relatedTableObj = PrdGeneratorService::matchRelatedTable($title, $desc, $erdTables);
            }

            $modelName = Str::studly(Str::singular(explode('-', $slug)[0] ?? 'Record'));
            $verifyCommand = "php artisan test --filter=" . $modelName . "Test";

            $targetFiles = !empty($item['target_files']) && is_array($item['target_files'])
                ? $item['target_files']
                : [
                    "app/Models/{$modelName}.php",
                    "database/migrations/xxxx_create_" . strtolower(Str::plural($modelName)) . "_table.php",
                    "app/Filament/Resources/{$modelName}Resource.php",
                    "resources/views/" . strtolower($cleanSlug) . "/index.blade.php",
                ];

            $mission = $item['mission'] ?? "Implementasikan modul {$title} sesuai spesifikasi arsitektur Laravel 13 (PHP 8.5+), Filament v5, dan PostgreSQL ULID.";
            $acceptanceCriteria = self::normalizeAcceptanceCriteria($item['acceptance_criteria'] ?? [], $title, $item['user_story'] ?? '');
            $integration = $item['integration'] ?? null;

            $agentPrompt = self::buildAgentPrompt(
                $featureId,
                $title,
                $businessName,
                $mission,
                $targetFiles,
                $verifyCommand,
                $relatedTableObj,
                $acceptanceCriteria,
                $integration,
                $index
            );

            $specs[] = [
                'id' => $featureId,
                'index' => $index,
                'tier' => $tier,
                'title' => $title,
                'desc' => $desc,
                'category' => $item['category'] ?? 'CORE_FEATURE',
                'category_label' => $item['category_label'] ?? 'Modul Inti Bisnis',
                'story_points' => $item['story_points'] ?? 3,
                'complexity_label' => $item['complexity_label'] ?? 'Standard (3 SP)',
                'sprint_phase' => $item['sprint_phase'] ?? 'Sprint 1 (Fondasi & CRUD)',
                'target_files' => $targetFiles,
                'related_table' => $relatedTableObj['name'] ?? null,
                'related_table_data' => $relatedTableObj,
                'user_story' => $item['user_story'] ?? "Sebagai Pengguna, saya ingin menggunakan fitur {$title}.",
                'acceptance_criteria' => $acceptanceCriteria,
                'frontend' => $item['frontend'] ?? [],
                'backend' => $item['backend'] ?? [],
                'integration' => $integration,
                'code_agent_directive' => $agentPrompt,
            ];

            $index++;
        }

        return $specs;
    }


    /**
     * Intelligently analyze feature domain and extract concrete engineering tasks.
     * Serves as a deterministic graceful fallback when LLM API reasoning is unavailable.
     */
    public static function analyzeFeatureDomain(string $title, string $desc, string $businessName, string $clientActor, string $primaryActor, int $index, string $prefix, array $erdTables = [], bool $isEn = false): array
    {
        $lower = strtolower($title . ' ' . $desc);
        $titleSlug = Str::slug($title);
        $modelName = Str::studly(Str::singular(explode('-', $titleSlug)[0] ?? 'Record'));
        if (in_array(strtolower($modelName), ['fitur', 'manajemen', 'sistem', 'data'])) {
            $modelName = 'BusinessRecord';
        }

        $relatedTable = PrdGeneratorService::matchRelatedTable($title, $desc, $erdTables);

        // Archetype T1: AIRPORT & HOTEL TRANSFER DISPATCH ENGINE (TRANSFER IN/OUT DPS)
        if (preg_match('/(?:bandara|airport|transfer in|transfer out|ngurah rai|\bdps\b|flight|penerbangan|antar jemput)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'DPS Ngurah Rai Flight Arrival Radar Sync & Delay Recalibration' : 'Sinkronisasi Radar Kedatangan DPS & Rekalibrasi Delay Pesawat',
                    'given' => $isEn ? "Passenger scheduled on flight GA-401 with estimated landing at 14:30 WITA." : "Penumpang terjadwal pada nomor penerbangan GA-401 dengan estimasi mendarat pukul 14:30 WITA.",
                    'when' => $isEn ? "Airport radar feeds detect a 45-minute flight delay from departure origin." : "Sistem pelacak radar bandara mendeteksi penundaan penerbangan (delay) selama 45 menit dari bandara asal.",
                    'then' => $isEn ? "System automatically updates pickup schedule to 15:15 WITA and dispatches real-time WhatsApp alert to assigned driver and traveler." : "Sistem otomatis memundurkan jadwal penjemputan ke 15:15 WITA dan mengirim update WhatsApp otomatis ke pengemudi serta tamu.",
                ],
                [
                    'scenario' => $isEn ? 'Baggage Clearance Buffer & Meeting Point Verification' : 'Buffer Pengambilan Bagasi & Verifikasi Titik Temu Pengemudi',
                    'given' => $isEn ? "Flight touchdown confirmed at Ngurah Rai International Airport." : "Penerbangan telah terkonfirmasi mendarat resmi di Bandara Internasional I Gusti Ngurah Rai.",
                    'when' => $isEn ? "Passenger completes immigration and proceeds to baggage claim area." : "Penumpang menyelesaikan proses imigrasi dan menuju area klaim bagasi bandara.",
                    'then' => $isEn ? "System applies a mandatory 45-60 minute baggage grace buffer, verifies driver GPS presence at Meeting Point, and renders Digital Paging Board." : "Sistem menerapkan buffer standar 45-60 menit, memverifikasi kehadiran driver di Meeting Point via GPS, dan menampilkan Digital Paging Board.",
                ],
                [
                    'scenario' => $isEn ? 'Passenger No-Show Mitigation & Dispute-Proof Audit Trail' : 'Mitigasi No-Show Tamu & Audit Trail Pembatalan Terverifikasi',
                    'given' => $isEn ? "Assigned driver has waited beyond the 90-minute maximum grace threshold." : "Pengemudi telah menunggu di titik temu melebihi batas toleransi maksimal 90 menit.",
                    'when' => $isEn ? "Driver triggers uncontactable passenger status via mobile companion." : "Pengemudi memicu status tamu tidak dapat dihubungi melalui web/aplikasi companion.",
                    'then' => $isEn ? "System logs GPS telemetry timestamps, captures meeting point photo verification, and dispatches dispute-proof cancellation notice protecting partner compensation." : "Sistem mencatat log telemetri GPS, mengunggah foto bukti kehadiran di lobi bandara, dan menerbitkan pembatalan terverifikasi guna melindungi komisi mitra.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/transfers/dispatch',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'passenger_name' => 'Michael Smith',
                    'flight_number' => 'GA-401',
                    'flight_origin' => 'CGK',
                    'scheduled_touchdown' => '2026-11-08 14:30:00',
                    'destination_hotel' => 'The Mulia Resort Nusa Dua',
                    'passenger_count' => 4,
                    'cabin_bags' => 2,
                    'checkin_bags' => 3,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'TRANSFER_DISPATCHED',
                    'data' => [
                        'transfer_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                        'flight_status' => 'ON_SCHEDULE',
                        'driver' => [
                            'name' => 'Wayan Suartana',
                            'phone' => '+6281234567890',
                            'vehicle' => 'Innova Reborn (DK 1888 XY)',
                        ],
                        'pickup_buffer_minutes' => 45,
                        'meeting_point' => 'DPS International Arrival Hall Pick-Up Zone',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful transfer dispatch route with Sanctum auth and rate limiting.',
                    'Integrate Flight Radar webhook handler for Ngurah Rai flight status synchronization.',
                    'Enforce X-Idempotency-Key header to eliminate duplicate transfer booking submissions.',
                ] : [
                    'Daftarkan endpoint API dispatch transfer bandara dengan otentikasi Sanctum dan rate limiting.',
                    'Integrasikan webhook handler Flight Radar untuk sinkronisasi delay pesawat Ngurah Rai real-time.',
                    'Terapkan header X-Idempotency-Key untuk mencegah pemesanan transfer ganda.',
                ],
            ];
            $targetFiles = ['app/Models/AirportTransfer.php', 'app/Services/TransferDispatchService.php', 'app/Http/Controllers/AirportTransferController.php', 'tests/Feature/AirportTransferContractTest.php'];
            $mission = "Bangun modul Reservasi & Dispatch Transfer Bandara Ngurah Rai dengan pelacakan radar delay, digital paging board, dan proteksi no-show.";

            return [
                'category' => 'LOGISTICS_DISPATCH',
                'category_label' => $isEn ? 'Airport Transfer & Flight Dispatch' : 'Transfer Bandara & Dispatch Radar',
                'related_table' => $relatedTable['name'] ?? 'airport_transfers',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler arriving at Ngurah Rai Airport, I want my pickup to automatically synchronize with my flight's real-time landing time, so that a driver is reliably waiting for me without coordination stress."
                    : "Sebagai wisatawan yang mendarat di Bandara Ngurah Rai, saya ingin penjemputan saya otomatis sinkron dengan waktu mendarat pesawat aktual, sehingga pengemudi siap menyambut di lobi tanpa rasa cemas.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['AirportTransferDispatchIsland.blade.php', 'FlightRadarTracker.blade.php', 'DigitalPagingBoard.blade.php'],
                    'tasks' => $isEn ? [
                        'Build responsive flight intake form with airline IATA auto-suggest.',
                        'Render dynamic flight radar status pill with real-time landing estimates.',
                        'Generate high-contrast Digital Paging Board for airport arrival greeting.',
                    ] : [
                        'Bangun formulir reservasi penjemputan bandara dengan auto-suggest kode penerbangan IATA.',
                        'Tampilkan pill status radar kedatangan pesawat dengan estimasi waktu mendarat live.',
                        'Hasilkan Digital Paging Board beresolusi tinggi untuk papan nama penyambutan tamu di lobi.',
                    ],
                    'states' => $isEn ? 'Default, Tracking Flight, Driver Dispatched, Delayed Buffer, Error Toast' : 'Default, Melacak Radar, Supir Ditugaskan, Penyesuaian Delay, Error Toast',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/AirportTransfer.php', 'app/Services/TransferDispatchService.php', 'app/Http/Controllers/AirportTransferController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for airport_transfers table enforcing strict 26-char ULID primary key.',
                        'Implement TransferDispatchService with atomic DB::transaction() and lockForUpdate().',
                        'Construct flight tracking radar scheduler to poll/receive arrival state transitions.',
                    ] : [
                        'Migration tabel airport_transfers dengan Strict ULID 26 karakter PostgreSQL 16.',
                        'Implementasikan TransferDispatchService atomik dibungkus DB::transaction() dan lockForUpdate().',
                        'Bangun service sinkronisasi radar kedatangan maskapai untuk pembaruan status delay otomatis.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-AIRPORT_TRANSFER', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=AirportTransferContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T2: INTERACTIVE DAY TOUR CATALOG & FLEET CONFIGURATOR
        if (preg_match('/(?:katalog|catalog|(?<!multi-)\bday tour\b|paket wisata|fleet configurator|konfigurasi armada|pilihan armada)/i', $title . ' ' . $desc) && !preg_match('/(?:overland|bromo|ijen)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Vehicle Volumetric Capacity Matching & Smart Upgrade Suggestion' : 'Kalkulasi Kapasitas Volumetrik Armada & Rekomendasi Upgrade',
                    'given' => $isEn ? "Client group consists of 5 adults and 4 large 28-inch check-in suitcases." : "Rombongan tamu terdiri dari 5 orang dewasa dan 4 koper bagasi besar ukuran 28-inci.",
                    'when' => $isEn ? "Client attempts to select standard compact vehicle (max 4 pax + 2 bags)." : "Tamu mencoba memilih armada tipe Avanza (kapasitas maksimal 4 penumpang + 2 bagasi).",
                    'then' => $isEn ? "System validates volumetric overcapacity and automatically suggests upgrading to Innova Reborn or HiAce Commuter with cost-benefit comparison." : "Sistem memvalidasi kelebihan muatan volumetrik dan otomatis menyarankan upgrade ke Innova Reborn atau HiAce Commuter beserta perbandingan tarif hemat.",
                ],
                [
                    'scenario' => $isEn ? 'Duration Tiering (Half Day vs Full Day) & Overtime Tariff Formulation' : 'Formulasi Tarif Berjenjang & Kalkulasi Biaya Lembur (Overtime)',
                    'given' => $isEn ? "Full Day tour package booked for standard 10-hour duration." : "Paket sewa Full Day berdurasi standar 10 jam telah dipesan.",
                    'when' => $isEn ? "Actual trip execution extends to 12 hours due to distant destination transit." : "Perjalanan di lapangan berlangsung selama 12 jam akibat rute destinasi yang jauh.",
                    'then' => $isEn ? "System calculates overtime surcharge at IDR 75,000/hour and displays itemized operational breakdown separating all-inclusive items from toll retributions." : "Sistem menghitung biaya lembur otomatis Rp 75.000/jam dan menampilkan invoice transparan yang memisahkan komponen All-in versus tiket tol Bali Mandara.",
                ],
                [
                    'scenario' => $isEn ? 'Transient Inventory Hold (15-Minute TTL) Preventing Concurrent Overbooking' : 'Penguncian Inventori Sementara (TTL 15 Menit) Anti-Overbooking',
                    'given' => $isEn ? "Available fleet inventory for HiAce Commuter on high-season date is 1 unit." : "Sisa ketersediaan unit HiAce Commuter pada tanggal high-season tersisa 1 unit.",
                    'when' => $isEn ? "Customer proceeds to checkout and enters payment window." : "Pelanggan masuk ke tahap checkout pembayaran.",
                    'then' => $isEn ? "System acquires a 15-minute Inventory TTL Hold locking the vehicle slot; if checkout expires without payment, slot is automatically released." : "Sistem mengunci inventori unit selama 15 menit (TTL Hold); jika pembayaran tidak diselesaikan, slot unit otomatis dilepas kembali ke katalog.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/tours/configure-fleet',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'tour_package_id' => 'tour-ubud-kintamani-sunset',
                    'vehicle_category' => 'innova_reborn',
                    'adult_passengers' => 4,
                    'child_passengers' => 1,
                    'cabin_bags' => 2,
                    'checkin_bags' => 2,
                    'duration_tier' => 'full_day_10h',
                    'booking_date' => '2026-11-10',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'FLEET_CONFIGURED',
                    'data' => [
                        'quote_id' => '01J7K9M0P1Q2R3S4T5U6V7W8BB',
                        'base_tariff_idr' => 850000,
                        'overtime_rate_per_hour_idr' => 75000,
                        'all_inclusive_items' => ['multilingual_driver', 'fuel', 'destination_parking'],
                        'client_payable_expenses' => ['bali_mandara_toll_idr_13000', 'village_retribution'],
                        'inventory_hold_ttl_seconds' => 900,
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful fleet configuration endpoint with sub-10ms response latency.',
                    'Implement Redis / database TTL lock for 15-minute inventory reservation hold.',
                    'Format response with Keyset Cursor Pagination O(1) indexed by Bali geographical zones.',
                ] : [
                    'Daftarkan endpoint konfigurator armada dengan latensi respons sub-10ms.',
                    'Terapkan penguncian TTL inventori sementara 15 menit menggunakan Redis atau database lock.',
                    'Format respons katalog dengan Keyset Cursor Pagination O(1) berindeks zona destinasi Bali.',
                ],
            ];
            $targetFiles = ['app/Models/TourBooking.php', 'app/Services/FleetConfiguratorService.php', 'app/Http/Controllers/TourBookingController.php', 'tests/Feature/TourBookingContractTest.php'];
            $mission = "Implementasikan Katalog Paket Day Tour & Konfigurator Armada dengan kalkulasi volumetrik bagasi, tarif dinamis, dan penguncian TTL inventori.";

            return [
                'category' => 'TOUR_CATALOG_FLEET',
                'category_label' => $isEn ? 'Tour Catalog & Fleet Configurator' : 'Katalog Day Tour & Konfigurator Armada',
                'related_table' => $relatedTable['name'] ?? 'tour_bookings',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler planning day trips in Bali, I want to configure vehicle types, passenger counts, and luggage capacities with transparent pricing, so that our group travels comfortably without vehicle capacity surprises."
                    : "Sebagai wisatawan yang merencanakan day tour di Bali, saya ingin mengonfigurasi tipe kendaraan, jumlah penumpang, dan bagasi dengan kalkulasi tarif transparan, agar rombongan nyaman tanpa kendala kapasitas di lapangan.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['DayTourCatalogIsland.blade.php', 'FleetCapacitySelector.blade.php', 'LuggageVolumetricCalculator.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct responsive tour catalog grid with filter tabs by Bali regional zones.',
                        'Implement real-time luggage & passenger volumetric capacity gauge (Safe / Full / Over-Capacity).',
                        'Integrate instant cost breakdown displaying All-Inclusive vs On-The-Road extras.',
                    ] : [
                        'Bangun grid katalog paket tour responsif dengan filter zona geografis Bali.',
                        'Implementasikan indikator kapasitas bagasi dan penumpang visual (Aman / Penuh / Over-Capacity).',
                        'Integrasikan rincian kalkulasi harga instan yang membedakan komponen All-In versus biaya tol di jalan.',
                    ],
                    'states' => $isEn ? 'Default, Capacity Warning, Inventory Holding, Confirmed, Error Toast' : 'Default, Peringatan Kapasitas, Menahan Inventori, Terkonfirmasi, Error Toast',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/TourBooking.php', 'app/Services/FleetConfiguratorService.php', 'app/Http/Controllers/TourBookingController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for tour_bookings table with strict 26-character ULID primary key.',
                        'Implement FleetConfiguratorService calculating vehicle assignments and luggage dimensions.',
                        'Enforce 15-minute transient TTL inventory holding state on selected vehicle dates.',
                    ] : [
                        'Migration tabel tour_bookings dengan Primary Key Strict ULID 26 karakter PostgreSQL 16.',
                        'Implementasikan FleetConfiguratorService yang menghitung pencocokan tipe armada dan dimensi bagasi.',
                        'Terapkan state holding inventori sementara (TTL 15 menit) pada unit kendaraan yang dipilih.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-TOUR_CATALOG', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=TourBookingContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T3: AI-POWERED WISHLIST & ITINERARY GENERATOR (ZERO-ASSUMPTION ENGINE)
        if (preg_match('/(?:\bwishlist\b|\bwish list\b|\bai itinerary\b|generator itinerary|smart itinerary|itinerary generator)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Zero-Assumption Entity Extraction & Intelligent Geo-Clustering' : 'Ekstraksi Wishlist Tanpa Asumsi & Klastering Geografis Cerdas',
                    'given' => $isEn ? "Traveler inputs free-text wish list: 'Tanah Lot sunset, Kecak dance at Uluwatu, Tirta Empul holy spring, and Ayung River rafting'." : "Wisatawan menginput wish list bebas: 'Sunset Tanah Lot, Tari Kecak Uluwatu, Tirta Empul, dan Rafting Sungai Ayung'.",
                    'when' => $isEn ? "Itinerary generator engine executes geographical clustering." : "Mesin generator itinerary menganalisis entitas lokasi dan melakukan pengelompokan geografis.",
                    'then' => $isEn ? "Destinations are rationally clustered into zones (Gianyar/Ubud Day 1, Tabanan/Badung Day 2) eliminating transit fatigue and illogical zig-zag routes." : "Destinasi dikelompokkan secara rasional (Gianyar/Ubud Hari 1, Tabanan/Badung Hari 2) guna mengeliminasi rute zig-zag dan menghemat waktu tempuh.",
                ],
                [
                    'scenario' => $isEn ? 'POI Operating Hours & Real-Traffic Buffer Synchronization' : 'Validasi Jam Operasional POI & Buffer Kemacetan Riil',
                    'given' => $isEn ? "Uluwatu Kecak performance starts strictly at 18:00 WITA." : "Pementasan Tari Kecak Uluwatu dimulai tepat pukul 18:00 WITA.",
                    'when' => $isEn ? "System sequences destination itinerary visits." : "Sistem menyusun urutan kunjungan destinasi dalam jadwal harian.",
                    'then' => $isEn ? "Arrival is scheduled by 16:30 WITA with historical Bali traffic buffers, guaranteeing guaranteed ticket acquisition before cutoff." : "Kedatangan di lokasi dijadwalkan pukul 16:30 WITA dengan buffer kemacetan lalu lintas Bali, menjamin ketersediaan tiket sebelum loket ditutup.",
                ],
                [
                    'scenario' => $isEn ? 'Integrated Real-Budget Breakdown (Domestic vs Foreign & Guide Options)' : 'Kalkulasi Anggaran Riil (Tarif Domestik vs Mancanegara & Opsi HPI)',
                    'given' => $isEn ? "Synthesized itinerary comprises 4 cultural attractions and 2 natural sites." : "Itinerary yang dihasilkan mencakup 4 objek wisata budaya dan 2 destinasi alam.",
                    'when' => $isEn ? "Client selects traveler nationality profile (Domestic vs Foreign Tourist)." : "Tamu memilih profil kewarganegaraan (Wisatawan Domestik vs Wisatawan Mancanegara).",
                    'then' => $isEn ? "System itemizes exact admission tickets, estimated road mileage, and optional certified HPI regional tourist guide fees." : "Sistem merinci tiket masuk resmi (tarif WNI vs WNA), estimasi biaya bahan bakar, dan opsi jasa pemandu lokal berlisensi (HPI).",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/itinerary/synthesize-wishlist',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'raw_wishlist_text' => 'Tanah Lot sunset, Kecak Uluwatu, Tirta Empul holy bath, Ayung rafting',
                    'duration_days' => 2,
                    'traveler_origin' => 'The Stones Legian',
                    'traveler_profile' => 'family_with_children',
                    'include_hpi_guide' => true,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'ITINERARY_SYNTHESIZED',
                    'data' => [
                        'itinerary_id' => '01J7K9M0P1Q2R3S4T5U6V7W8CC',
                        'geo_clusters' => ['Central Gianyar (Ubud)', 'South Badung & Tabanan'],
                        'daily_schedule' => [
                            ['day' => 1, 'route' => ['Tirta Empul', 'Ayung Rafting'], 'transit_minutes' => 50],
                            ['day' => 2, 'route' => ['Tanah Lot Sunset', 'Uluwatu Kecak'], 'transit_minutes' => 65],
                        ],
                        'admission_tickets_total_idr' => 450000,
                        'hpi_guide_cost_idr' => 350000,
                        'google_maps_route_url' => 'https://maps.google.com/?dir=...',
                        'status' => 'DRAFT_AI',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful itinerary synthesis endpoint with Sanctum authentication.',
                    'Implement state machine (DRAFT_AI -> CLIENT_CUSTOMIZING -> DISPATCHER_VERIFIED -> LOCKED_CONFIRMED).',
                    'Generate printable PDF itinerary voucher with anti-tamper QR verification.',
                ] : [
                    'Daftarkan endpoint sintesis itinerary dengan otentikasi token Sanctum.',
                    'Terapkan state machine perjalanan (DRAFT_AI -> CLIENT_CUSTOMIZING -> DISPATCHER_VERIFIED -> LOCKED_CONFIRMED).',
                    'Hasilkan voucher PDF siap cetak dengan kode QR verifikasi anti-pemalsuan.',
                ],
            ];
            $targetFiles = ['app/Models/Itinerary.php', 'app/Services/ItinerarySynthesisService.php', 'app/Http/Controllers/ItineraryController.php', 'tests/Feature/ItineraryContractTest.php'];
            $mission = "Bangun Mesin Generator Itinerary Cerdas Berbasis AI & Database dengan klastering geografis, jam operasional POI, dan kalkulasi tiket resmi.";

            return [
                'category' => 'AI_ITINERARY_PLANNER',
                'category_label' => $isEn ? 'AI Wishlist & Itinerary Planner' : 'Generator Itinerary Cerdas & Wishlist',
                'related_table' => $relatedTable['name'] ?? 'itineraries',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler with custom dream vacation ideas, I want an intelligent engine to analyze my wishlist against verified attraction databases, so that I receive an optimal, conflict-free travel schedule with transparent costs."
                    : "Sebagai wisatawan dengan daftar destinasi impian, saya ingin mesin cerdas menganalisis wishlist saya terhadap data resmi destinasi, sehingga menghasilkan rencana perjalanan hemat waktu dengan transparansi biaya.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['WishlistIntakeIsland.blade.php', 'InteractiveItineraryTimeline.blade.php', 'GeoClusterRouteMap.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct free-text wishlist input with instant landmark tag chips.',
                        'Render interactive day-by-day timeline with drag-and-drop itinerary reordering.',
                        'Embed Google Maps GPS routing link with verified transit durations.',
                    ] : [
                        'Bangun kotak input wish list bebas dengan chips deteksi landmark otomatis.',
                        'Tampilkan timeline interaktif jadwal harian yang mendukung penyesuaian urutan kunjungan.',
                        'Sematkan tautan rute GPS Google Maps interaktif dengan durasi tempuh akurat.',
                    ],
                    'states' => $isEn ? 'Default, Analyzing Wishlist, Geo-Clustered, Customizing, Locked Confirmed' : 'Default, Menganalisis Wishlist, Klastering Lokasi, Disesuaikan Tamu, Terkunci Final',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/Itinerary.php', 'app/Services/ItinerarySynthesisService.php', 'app/Http/Controllers/ItineraryController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for itineraries table with Strict ULID 26-char primary key and JSONB routes.',
                        'Implement ItinerarySynthesisService with geographical clustering and operating hours validation.',
                        'Enforce state machine transitions locking itinerary upon customer confirmation.',
                    ] : [
                        'Migration tabel itineraries dengan Strict ULID 26 karakter PostgreSQL 16 dan rute JSONB.',
                        'Implementasikan ItinerarySynthesisService dengan algoritma klastering lokasi dan validasi jam buka POI.',
                        'Terapkan transisi state machine yang mengunci itinerary saat disetujui tamu.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-ITINERARY_AI', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=ItineraryContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T4: OVERLAND MULTI-DAY TOUR (BROMO & IJEN NATIONAL PARKS)
        if (preg_match('/(?:overland|bromo|ijen|jawa timur|luar bali|multi-day|selat bali|ferry|penyeberangan)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Multi-Day Stage Progression & Bali Strait Ferry Handoff' : 'Manajemen Etape Perjalanan Multi-Hari & Penyeberangan Feri',
                    'given' => $isEn ? "Overland booking confirmed for 3-Day/2-Night Bromo & Ijen expedition departing Bali." : "Pemesanan overland terkonfirmasi untuk ekspedisi 3-Hari/2-Malam Bromo & Ijen dari Bali.",
                    'when' => $isEn ? "Tour vehicle reaches Gilimanuk Port for Bali Strait ferry crossing to Ketapang, Banyuwangi." : "Armada tour tiba di Pelabuhan Gilimanuk untuk penyeberangan feri Selat Bali ke Ketapang, Banyuwangi.",
                    'then' => $isEn ? "System records automated stage check-in, validates ferry boarding e-ticket, and syncs vehicle handoff with Banyuwangi operations." : "Sistem mencatat check-in etape otomatis, memvalidasi tiket digital penyeberangan feri, dan menyinkronkan penjemputan armada Banyuwangi.",
                ],
                [
                    'scenario' => $isEn ? 'Regional Partner Dispatch & Conservation E-Ticketing' : 'Koordinasi Mitra Lokal & E-Tiket Konservasi TNBTS/Ijen',
                    'given' => $isEn ? "Expedition itinerary schedules midnight ascent to Mount Bromo Sunrise and Ijen Crater Blue Fire." : "Jadwal ekspedisi merencanakan pendakian dini hari Sunrise Bromo dan Api Biru Kawah Ijen.",
                    'when' => $isEn ? "System dispatches operational vouchers 24 hours prior to stage execution." : "Sistem menerbitkan voucher operasional H-1 sebelum etape pendakian dimulai.",
                    'then' => $isEn ? "Automated partner vouchers are dispatched to Sukapura 4x4 Jeep operators and certified Ijen Crater guides with specialized gas mask equipment checklists." : "Voucher kemitraan otomatis terkirim ke paguyuban Jeep 4x4 Sukapura dan pemandu resmi Kawah Ijen dengan checklist masker gas standar.",
                ],
                [
                    'scenario' => $isEn ? 'Volcanic Hazard Contingency & Driver Rotation Safety' : 'Protokol Mitigasi Erupsi Vulkanik & Pencegahan Kelelahan Pengemudi',
                    'given' => $isEn ? "Overland drive exceeds 8 continuous hours or regional vulcanology elevates hazard status to Alert (Siaga)." : "Durasi perjalanan overland melebihi 8 jam atau status vulkanik PVMBG naik ke level Waspada/Siaga.",
                    'when' => $isEn ? "Telematics sensor or supervisor triggers safety alert." : "Sensor telemetri armada atau supervisor memicu peringatan keselamatan operasional.",
                    'then' => $isEn ? "System enforces mandatory rest interval, triggers secondary driver rotation, or executes automated itinerary rerouting with client WhatsApp notification." : "Sistem mewajibkan istirahat pengemudi, mengaktifkan sopir cadangan, atau mengalihkan rute aman dengan notifikasi WhatsApp otomatis ke tamu.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/tours/overland-booking',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'package_id' => '01J7K9M0P1Q2R3S4T5U6V7W8DD',
                    'departure_date' => '2026-12-15',
                    'passengers_adult' => 4,
                    'passengers_child' => 0,
                    'pickup_location' => 'Seminyak Beach Resort',
                    'overland_tier' => 'bromo_ijen_bluefire_3d2n',
                    'jeep_4x4_required' => true,
                    'gas_masks_count' => 4,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'OVERLAND_BOOKING_INITIALIZED',
                    'data' => [
                        'booking_id' => '01J7K9M0P1Q2R3S4T5U6V7W8EE',
                        'stages' => [
                            ['stage' => 1, 'route' => 'Bali -> Gilimanuk -> Banyuwangi', 'status' => 'SCHEDULED'],
                            ['stage' => 2, 'route' => 'Banyuwangi -> Mount Ijen Blue Fire', 'status' => 'SCHEDULED'],
                            ['stage' => 3, 'route' => 'Probolinggo -> Mount Bromo Sunrise -> Bali', 'status' => 'SCHEDULED'],
                        ],
                        'ferry_crossing_ticket' => 'FERRY-GLM-KTP-8849',
                        'jeep_partner' => 'Paguyuban Sukapura 4x4 (Unit #24)',
                        'certified_guide' => 'Wayan Bromo Tours (HPI Lic. #7721)',
                        'status' => 'CONFIRMED',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful overland booking endpoint with Sanctum authentication.',
                    'Implement multi-stage journey lifecycle with ferry and conservation park voucher linking.',
                    'Integrate volcanic hazard alert webhook for emergency itinerary suspension.',
                ] : [
                    'Daftarkan endpoint pemesanan overland dengan otentikasi token Sanctum.',
                    'Implementasikan siklus hidup etape perjalanan multi-hari terintegrasi tiket feri & TNBTS.',
                    'Integrasikan webhook notifikasi status vulkanik PVMBG untuk penangguhan darurat.',
                ],
            ];
            $targetFiles = ['app/Models/OverlandTour.php', 'app/Services/OverlandDispatchService.php', 'app/Http/Controllers/OverlandTourController.php', 'tests/Feature/OverlandTourContractTest.php'];
            $mission = "Bangun modul Ekspedisi Overland Tour Multi-Hari (Bromo & Ijen) dengan manajemen etape perjalanan, koordinasi Jeep 4x4 lokal, dan tiket feri.";

            return [
                'category' => 'OVERLAND_EXPEDITION',
                'category_label' => $isEn ? 'Overland Multi-Day Expedition' : 'Ekspedisi Overland Multi-Hari',
                'related_table' => $relatedTable['name'] ?? 'overland_tours',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler embarking on an overland adventure to Mount Bromo and Ijen Crater, I want guaranteed multi-day vehicle staging, ferry crossing tickets, and certified local guides, so that my expedition is seamless, safe, and conflict-free."
                    : "Sebagai wisatawan ekspedisi overland ke Gunung Bromo dan Kawah Ijen, saya ingin kepastian armada multi-hari, tiket feri, dan pemandu lokal bersertifikat, sehingga petualangan berjalan aman, nyaman, dan bebas kendala.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['OverlandStageTimeline.blade.php', 'FerryVoucherCard.blade.php', 'VolcanicSafetyAlert.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct multi-day overland itinerary timeline with stage progression trackers.',
                        'Display certified gear checklist (gas masks, warm jackets) with toggle confirmation.',
                        'Implement real-time hazard status banner with amber/rose safety warning accents.',
                    ] : [
                        'Bangun timeline jadwal overland multi-hari dengan indikator progres etape perjalanan.',
                        'Tampilkan checklist perlengkapan wajib (masker gas, jaket tebal) dengan toggle verifikasi.',
                        'Sediakan banner status keamanan vulkanik real-time dengan aksen amber/rose.',
                    ],
                    'states' => $isEn ? 'Default, Staging Active, Ferry Boarding, Summit Trekking, Return to Base' : 'Default, Persiapan Etape, Penyeberangan Feri, Pendakian Puncak, Kembali ke Base',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/OverlandTour.php', 'app/Services/OverlandDispatchService.php', 'app/Http/Controllers/OverlandTourController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for overland_tours and overland_stages tables with Strict ULID primary keys.',
                        'Implement OverlandDispatchService managing driver rotation and ferry booking vouchers.',
                        'Enforce atomic stage updates with DB::transaction and pessimistic concurrency locking.',
                    ] : [
                        'Migration tabel overland_tours dan overland_stages dengan Strict ULID 26 karakter PostgreSQL 16.',
                        'Implementasikan OverlandDispatchService yang mengelola rotasi supir dan voucher feri.',
                        'Terapkan update etape atomik dengan DB::transaction dan pessimistic locking.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-OVERLAND', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=OverlandTourContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T5: CENTRAL OPERATIONAL FLEET DISPATCHER & KANBAN
        if (preg_match('/(?:dispatcher|dasbor pusat|monitoring armada|penugasan armada|fleet center|penugasan supir|alokasi supir)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Real-Time Fleet Status Kanban & Zero-Polling Live Updates' : 'Papan Kendali Kanban Unit & Status Armada Real-Time',
                    'given' => $isEn ? "Fleet dispatcher opens the operational command center on Filament v5 admin dashboard." : "Dispatcher armada membuka pusat kendali operasional pada dasbor admin Filament v5.",
                    'when' => $isEn ? "Field drivers update trip milestones via mobile device or GPS beacons." : "Pengemudi di lapangan memperbarui status perjalanan melalui perangkat mobile atau sinyal GPS.",
                    'then' => $isEn ? "Fleet Kanban cards transition instantly across swimlanes (Available -> Dispatched -> In-Transit -> Maintenance) with zero page reloads." : "Kartu Kanban armada berpindah seketika antar-kolom (Tersedia -> Ditugaskan -> Dalam Perjalanan -> Perawatan) secara instan tanpa reload.",
                ],
                [
                    'scenario' => $isEn ? 'Intelligent Driver Matchmaking (Language, Capacity & Proximity)' : 'Algoritma Penugasan Pengemudi Cerdas (Bahasa, Kapasitas & Lokasi)',
                    'given' => $isEn ? "New booking arrives from foreign guests requesting English/Mandarin speaking guide with HiAce vehicle." : "Pesanan baru masuk dari tamu asing yang membutuhkan supir fasih Bahasa Inggris/Mandarin dengan unit HiAce.",
                    'when' => $isEn ? "Dispatcher executes automated dispatch recommendation." : "Dispatcher mengeksekusi rekomendasi penugasan pengemudi otomatis.",
                    'then' => $isEn ? "System ranks available drivers by language qualification, current GPS proximity, and fair workload distribution hours." : "Sistem meranking supir yang tersedia berdasarkan kualifikasi bahasa, jarak GPS terdekat, dan rotasi jam kerja yang seimbang.",
                ],
                [
                    'scenario' => $isEn ? 'Emergency Backup Auto-Reassignment Protocol' : 'Prosedur Auto-Reassignment Penggantian Armada Darurat',
                    'given' => $isEn ? "Assigned vehicle reports mechanical breakdown during en-route transit." : "Armada yang bertugas mengalami kendala teknis atau mogok di jalan saat menuju titik penjemputan.",
                    'when' => $isEn ? "Driver reports emergency malfunction via one-click SOS button." : "Pengemudi menekan tombol SOS darurat di aplikasi operasional.",
                    'then' => $isEn ? "System instantly alerts nearest standby unit within 15 km radius, re-routes pickup dispatch, and updates client WhatsApp ETA." : "Sistem seketika menugaskan unit cadangan terdekat dalam radius 15 km, mengalihkan order penjemputan, dan mengabari tamu via WhatsApp.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/dispatcher/assign-driver',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'booking_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                    'driver_id' => '01J7K9M0P1Q2R3S4T5U6V7W8FF',
                    'vehicle_id' => '01J7K9M0P1Q2R3S4T5U6V7W8GG',
                    'assignment_mode' => 'AUTOMATED_MATCH',
                    'dispatch_notes' => 'Airport pickup DPS international terminal gate 3',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'DISPATCH_CONFIRMED',
                    'data' => [
                        'dispatch_id' => '01J7K9M0P1Q2R3S4T5U6V7W8HH',
                        'driver_name' => 'Ketut Sudarma',
                        'driver_phone' => '+6281234567890',
                        'vehicle_plate' => 'DK 1842 AB',
                        'vehicle_model' => 'Toyota HiAce Commuter 2024',
                        'dispatched_at' => '2026-11-07T10:00:00+08:00',
                        'status' => 'DISPATCHED',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register driver dispatch assignment endpoint with RBAC permission check (operator/superadmin).',
                    'Implement real-time WebSocket event broadcasting for fleet state updates.',
                    'Record comprehensive dispatch audit trail with GPS departure coordinates.',
                ] : [
                    'Daftarkan endpoint penugasan supir dengan validasi hak akses RBAC (operator/superadmin).',
                    'Implementasikan event broadcasting WebSocket real-time untuk pembaruan status armada.',
                    'Catat audit trail penugasan lengkap dengan koordinat GPS titik keberangkatan.',
                ],
            ];
            $targetFiles = ['app/Models/FleetDispatch.php', 'app/Services/FleetDispatchService.php', 'app/Http/Controllers/FleetDispatchController.php', 'tests/Feature/FleetDispatchContractTest.php'];
            $mission = "Bangun Pusat Kendali Dispatcher Armada Real-Time dengan visualisasi Kanban, algoritma pencocokan pengemudi, dan penanganan darurat.";

            return [
                'category' => 'FLEET_DISPATCH_CENTER',
                'category_label' => $isEn ? 'Operational Fleet Dispatcher' : 'Pusat Kendali Dispatcher Armada',
                'related_table' => $relatedTable['name'] ?? 'fleet_dispatches',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As an operational fleet dispatcher, I want a centralized live monitoring board to assign verified drivers and track trip milestones, so that fleet utilization is maximized and clients experience zero pickup delays."
                    : "Sebagai dispatcher operasional, saya ingin dasbor pemantauan terpusat untuk menugaskan supir dan memantau status perjalanan, sehingga utilisasi armada optimal dan penjemputan tamu tepat waktu.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['FleetKanbanBoard.blade.php', 'DriverAssignmentModal.blade.php', 'EmergencyReassignmentAlert.blade.php'],
                    'tasks' => $isEn ? [
                        'Build drag-and-drop Fleet Kanban board adhering strictly to WCAG AAA contrast.',
                        'Render driver matching card displaying foreign language badges and star ratings.',
                        'Integrate instant toast alerts for breakdown reports and emergency replacements.',
                    ] : [
                        'Bangun papan Kanban armada responsif dengan rasio kontras tinggi WCAG AAA.',
                        'Tampilkan kartu rekomendasi supir dengan badge kemampuan bahasa dan rating ulasan.',
                        'Integrasikan notifikasi toast melayang saat ada laporan kendala teknis atau unit pengganti.',
                    ],
                    'states' => $isEn ? 'Available, Dispatched, In-Transit, Maintenance, Emergency Replaced' : 'Tersedia, Ditugaskan, Dalam Perjalanan, Perawatan, Unit Pengganti Ditugaskan',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/FleetDispatch.php', 'app/Services/FleetDispatchService.php', 'app/Http/Controllers/FleetDispatchController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for fleet_dispatches table with Strict ULID primary and foreign keys.',
                        'Implement FleetDispatchService with atomic driver allocation and pessimistic locking.',
                        'Enforce driver rest-time validation preventing overtime dispatch.',
                    ] : [
                        'Migration tabel fleet_dispatches dengan Strict ULID 26 karakter standar PostgreSQL 16.',
                        'Implementasikan FleetDispatchService dengan alokasi supir atomik dan pessimistic locking.',
                        'Terapkan validasi batas jam istirahat pengemudi guna mencegah overwork.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-DISPATCH', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=FleetDispatchContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T6: CRYPTOGRAPHIC QR CODE E-VOUCHER & WHATSAPP CONFIRMATION
        if (preg_match('/(?:voucher|qr code|e-voucher|tiket digital|boarding pass|barcode)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Tamper-Proof HMAC SHA-256 QR Voucher Generation' : 'Penerbitan E-Voucher Ber-QR Code HMAC SHA-256 Anti-Pemalsuan',
                    'given' => $isEn ? "Reservation payment confirmed via payment gateway callback." : "Pembayaran pemesanan terkonfirmasi lunas dari callback payment gateway.",
                    'when' => $isEn ? "System executes automated e-voucher issuance." : "Sistem menjalankan proses penerbitan e-voucher otomatis.",
                    'then' => $isEn ? "System generates cryptographic HMAC SHA-256 QR code embedding reservation ULID without exposing sensitive personal data." : "Sistem membuat kode QR ber-tanda tangan kriptografi HMAC SHA-256 yang memuat ULID reservasi tanpa mengekspos data pribadi tamu.",
                ],
                [
                    'scenario' => $isEn ? 'Automated WhatsApp Cloud API Dispatch with PDF Attachment' : 'Pengiriman WhatsApp Business Cloud API Otomatis dengan Lampiran PDF',
                    'given' => $isEn ? "Customer provides verified WhatsApp contact number during checkout." : "Tamu memasukkan nomor kontak WhatsApp terverifikasi saat pemesanan.",
                    'when' => $isEn ? "Voucher generation pipeline finishes PDF rendering." : "Pipeline penerbitan voucher menyelesaikan kompilasi berkas PDF resmi.",
                    'then' => $isEn ? "System dispatches instant WhatsApp push notification with official printable PDF voucher and direct driver contact button." : "Sistem seketika mengirimkan push WhatsApp berisi berkas PDF voucher siap cetak dan tombol kontak langsung ke pengemudi.",
                ],
                [
                    'scenario' => $isEn ? 'Sub-Second Driver & Lobby Staff QR Scanner Validation' : 'Validasi Scanner QR Cepat oleh Pengemudi & Staf Lobi Hotel',
                    'given' => $isEn ? "Passenger presents mobile or printed QR voucher to driver at pickup point." : "Tamu menunjukkan QR voucher di layar ponsel atau kertas cetak kepada pengemudi saat penjemputan.",
                    'when' => $isEn ? "Driver scans voucher using web scanner camera module." : "Pengemudi memindai kode QR menggunakan modul kamera scanner web.",
                    'then' => $isEn ? "System validates cryptographic signature in <1.0 second, confirms boarding status, and transitions reservation to IN_TRANSIT." : "Sistem memvalidasi tanda tangan kriptografi dalam <1,0 detik, memverifikasi keabsahan tiket, dan mengubah status pesanan ke IN_TRANSIT.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/vouchers/verify-qr',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'voucher_ulid' => '01J7K9M0P1Q2R3S4T5U6V7W8JJ',
                    'hmac_signature' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                    'scanned_by_driver_id' => '01J7K9M0P1Q2R3S4T5U6V7W8FF',
                    'gps_latitude' => -8.7482,
                    'gps_longitude' => 115.1672,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'VOUCHER_VERIFIED',
                    'data' => [
                        'voucher_id' => '01J7K9M0P1Q2R3S4T5U6V7W8JJ',
                        'passenger_name' => 'Yoseph Iriandi',
                        'pickup_time' => '14:30 WITA',
                        'destination' => 'Grand Hyatt Nusa Dua',
                        'service_tier' => 'AIRPORT_TRANSFER_IN',
                        'status' => 'BOARDED_IN_TRANSIT',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register voucher verification endpoint with HMAC SHA-256 signature validation.',
                    'Implement WhatsApp Business Cloud API client with exponential backoff retry.',
                    'Generate lightweight HTML5 canvas QR scanner with offline fallback capability.',
                ] : [
                    'Daftarkan endpoint verifikasi voucher dengan validasi tanda tangan HMAC SHA-256.',
                    'Implementasikan klien WhatsApp Business Cloud API dengan mekanisme retry bertahap.',
                    'Bangun modul scanner QR kamera HTML5 ringan dengan kapabilitas offline fallback.',
                ],
            ];
            $targetFiles = ['app/Models/Voucher.php', 'app/Services/VoucherIssuanceService.php', 'app/Http/Controllers/VoucherController.php', 'tests/Feature/VoucherContractTest.php'];
            $mission = "Bangun modul E-Voucher Kriptografis Ber-QR Code HMAC SHA-256 dengan otomatisasi notifikasi WhatsApp dan pemindai cepat staf lapangan.";

            return [
                'category' => 'DIGITAL_VOUCHER_QR',
                'category_label' => $isEn ? 'Digital E-Voucher & QR Code' : 'E-Voucher Digital & Kode QR',
                'related_table' => $relatedTable['name'] ?? 'vouchers',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler, I want to receive an instant official PDF e-voucher on WhatsApp with a secure QR code upon payment, so that I can seamlessly board my reserved vehicle without paper hassle."
                    : "Sebagai wisatawan, saya ingin menerima e-voucher resmi PDF ber-QR Code di WhatsApp sesaat setelah bayar, sehingga proses penjemputan berjalan lancar tanpa repot mencetak kertas.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['VoucherDigitalCard.blade.php', 'QrCodeScannerModal.blade.php', 'WhatsAppShareButton.blade.php'],
                    'tasks' => $isEn ? [
                        'Build mobile-first printable digital e-voucher layout adhering strictly to WCAG AAA.',
                        'Integrate fast HTML5 QR scanner with audio/haptic feedback on successful scan.',
                        'Provide one-click WhatsApp re-send button for administrative dispatchers.',
                    ] : [
                        'Bangun layout e-voucher mobile-first siap cetak dengan rasio kontras WCAG AAA.',
                        'Integrasikan scanner QR kamera HTML5 cepat dengan feedback visual dan getar saat berhasil.',
                        'Sediakan tombol kirim ulang WhatsApp 1-klik untuk dispatcher dan admin.',
                    ],
                    'states' => $isEn ? 'Issued, Delivered WhatsApp, Scanned Verified, Redeemed, Cancelled' : 'Diterbitkan, Terkirim WhatsApp, Terverifikasi Scan, Digunakan, Dibatalkan',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/Voucher.php', 'app/Services/VoucherIssuanceService.php', 'app/Http/Controllers/VoucherController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for vouchers table with Strict ULID primary key and HMAC hash columns.',
                        'Implement VoucherIssuanceService with cryptographic signature generation and PDF rendering.',
                        'Enforce rate limiting on QR verification endpoints to prevent brute-force token scans.',
                    ] : [
                        'Migration tabel vouchers dengan Strict ULID 26 karakter dan kolom hash HMAC.',
                        'Implementasikan VoucherIssuanceService dengan pembuatan signature kriptografi dan PDF.',
                        'Terapkan rate limiting ketat pada endpoint verifikasi QR guna mencegah brute force.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-VOUCHER', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=VoucherContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T7: TWO-STAGE PAYMENT GATEWAY (50% DP & 50% SETTLEMENT)
        if (preg_match('/(?:pembayaran|payment|midtrans|qris|\bdp\b|termin|pelunasan|checkout|transaksi)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Two-Stage Milestone Automation (50% DP Scope Freeze & 50% Settlement)' : 'Otomatisasi Termin 2 Tahap (DP 50% & Pelunasan 50%)',
                    'given' => $isEn ? "Traveler selects custom day tour or overland booking with total cost IDR 5,000,000." : "Wisatawan memilih paket tour harian atau overland dengan total biaya Rp 5.000.000.",
                    'when' => $isEn ? "Client checks out choosing Two-Stage Milestone payment structure." : "Tamu melakukan checkout dengan skema pembayaran termin 2 tahap.",
                    'then' => $isEn ? "System generates 50% DP order (IDR 2,500,000), locks schedule on payment confirmation, and schedules 50% balance reminder H-2 prior to departure." : "Sistem membuat tagihan DP 50% (Rp 2.500.000), mengunci slot jadwal saat DP lunas, dan menjadwalkan tagihan pelunasan 50% pada H-2 keberangkatan.",
                ],
                [
                    'scenario' => $isEn ? 'Idempotent Webhook Processing with Pessimistic Locking' : 'Pemrosesan Webhook Idempoten dengan Pessimistic Locking',
                    'given' => $isEn ? "Payment gateway dispatches duplicate HTTP webhook notifications concurrently." : "Payment gateway mengirimkan duplikasi callback webhook secara serentak karena retries jaringan.",
                    'when' => $isEn ? "System processes incoming payment status update." : "Sistem memproses callback webhook mutasi pembayaran.",
                    'then' => $isEn ? "System executes atomic DB::transaction with pessimistic lock (lockForUpdate) on order record, preventing double confirmation or balance distortion." : "Sistem mengeksekusi DB::transaction atomik dengan lockForUpdate pada record order, mencegah konfirmasi ganda atau distorsi saldo.",
                ],
                [
                    'scenario' => $isEn ? 'Tiered Cancellation & Refund Penalty Calculation' : 'Formula Pembatalan & Pemotongan Refund Otomatis Berjenjang',
                    'given' => $isEn ? "Confirmed reservation with 50% DP or full settlement requests cancellation." : "Pemesanan terkonfirmasi mengajukan pembatalan perjalanan karena force majeure atau perubahan jadwal.",
                    'when' => $isEn ? "Client submits cancellation request through client portal." : "Tamu mengajukan pembatalan melalui portal klien.",
                    'then' => $isEn ? "System enforces automated tiered deduction policy: >=H-7 (100% refund), H-3 to H-6 (50% refund), <=H-2 (non-refundable), logging audit trail." : "Sistem menerapkan kebijakan refund berjenjang: >=H-7 (refund 100%), H-3 s/d H-6 (refund 50%), <=H-2 (hangus), lengkap dengan catatan audit.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/payments/create-charge',
                'middleware' => ['api', 'auth:sanctum', 'throttle:30,1'],
                'request_schema' => json_encode([
                    'booking_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                    'payment_stage' => 'DOWN_PAYMENT_50',
                    'payment_method' => 'qris',
                    'amount_idr' => 2500000,
                    'customer_email' => 'client@gayabalical.com',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'CHARGE_CREATED',
                    'data' => [
                        'transaction_id' => 'TRX-GBC-2026-9941',
                        'order_id' => '01J7K9M0P1Q2R3S4T5U6V7W8KK',
                        'payment_type' => 'qris',
                        'gross_amount' => 2500000,
                        'qr_string' => '00020101021226...IDR',
                        'qr_image_url' => 'https://api.midtrans.com/v2/qris/01J.../qr-code',
                        'expiry_time' => '2026-11-07 15:30:00 WIB',
                        'status' => 'PENDING',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register payment charge generation endpoint with Sanctum authentication.',
                    'Implement Midtrans/Xendit webhook controller with HMAC SHA-512 signature validation.',
                    'Automate DP scope freeze and settlement milestone invoice reminders.',
                ] : [
                    'Daftarkan endpoint pembuatan pembayaran dengan otentikasi Sanctum.',
                    'Implementasikan webhook controller Midtrans/Xendit dengan verifikasi signature HMAC SHA-512.',
                    'Otomatisasi penguncian scope DP dan pengingat pelunasan sisa termin.',
                ],
            ];
            $targetFiles = ['app/Models/Payment.php', 'app/Services/PaymentService.php', 'app/Http/Controllers/PaymentController.php', 'tests/Feature/PaymentContractTest.php'];
            $mission = "Bangun modul Payment Gateway Termin 2 Tahap (DP 50% & Pelunasan) dengan proteksi webhook idempoten dan formula refund transparan.";

            return [
                'category' => 'FINANCIAL_PAYMENT_GATEWAY',
                'category_label' => $isEn ? 'Two-Stage Payment Gateway' : 'Gerbang Pembayaran Termin 2 Tahap',
                'related_table' => $relatedTable['name'] ?? 'payments',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler, I want to secure my tour reservation with a flexible 50% down payment and settle the remaining balance before departure, so that my travel budget is manageable and payment is 100% secure."
                    : "Sebagai wisatawan, saya ingin mengamankan pesanan tour dengan uang muka (DP) 50% fleksibel dan melunasi sisanya sebelum jalan, sehingga anggaran liburan terencana dengan aman.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['PaymentStageCard.blade.php', 'QrisDisplayModal.blade.php', 'RefundCalculationSummary.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct clear Two-Stage milestone payment card displaying DP and settlement due dates.',
                        'Render dynamic QRIS barcode modal with countdown expiration timer.',
                        'Integrate instant toast feedback upon webhook confirmation without page refresh.',
                    ] : [
                        'Bangun kartu pembayaran termin 2 tahap yang memperjelas jatuh tempo DP dan pelunasan.',
                        'Tampilkan modal QRIS dinamis dengan penghitung waktu mundur batas kedaluwarsa.',
                        'Integrasikan notifikasi toast melayang instan saat webhook pembayaran berhasil dikonfirmasi.',
                    ],
                    'states' => $isEn ? 'Pending DP, DP Settled, Pending Balance, Fully Paid, Refunded' : 'Menunggu DP, DP Lunas, Menunggu Pelunasan, Lunas Penuh, Refund Diproses',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/Payment.php', 'app/Services/PaymentService.php', 'app/Http/Controllers/PaymentController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for payments table with Strict ULID primary and foreign keys.',
                        'Implement PaymentService with atomic DB::transaction and pessimistic lockForUpdate.',
                        'Enforce webhook signature verification discarding tampered callbacks.',
                    ] : [
                        'Migration tabel payments dengan Strict ULID 26 karakter standar PostgreSQL 16.',
                        'Implementasikan PaymentService dengan transaksi atomik dan pessimistic lockForUpdate.',
                        'Terapkan verifikasi signature webhook ketat untuk menolak callback palsu.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(sprintf('FEAT-%s-%02d-PAYMENT', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=PaymentContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Domain 1: AUTH & RBAC
        if (str_contains($lower, 'autentikasi') || str_contains($lower, 'login') || str_contains($lower, 'rbac') || str_contains($lower, 'user') || str_contains($lower, 'pengguna')) {
            $ac = [
                [
                    'scenario' => 'Login Berhasil dengan Kredensial Valid',
                    'given' => "Pengguna telah terdaftar di database dengan peran yang sah ({$primaryActor} atau {$clientActor}).",
                    'when' => 'Pengguna memasukkan email dan kata sandi yang sesuai pada form login.',
                    'then' => 'Sistem mengautentikasi sesi, meregenerasi Session ID untuk mencegah session fixation, dan mengarahkan pengguna ke dasbor sesuai peran.',
                ],
                [
                    'scenario' => 'Blokir Akses Ilegal (RBAC Boundary)',
                    'given' => "Pengguna terautentikasi dengan peran {$clientActor}.",
                    'when' => 'Pengguna mencoba mengakses URL atau endpoint khusus milik Superadmin.',
                    'then' => 'Sistem menolak request dengan status HTTP 403 Forbidden, menampilkan halaman error yang ramah, dan mencatat log audit percobaan akses.',
                ],
                [
                    'scenario' => 'Proteksi Brute-Force Rate Limiting',
                    'given' => 'Form login menerima permintaan percobaan otentikasi bertubi-tubi.',
                    'when' => 'Terjadi 5 kali kegagalan login dalam kurun waktu 1 menit dari IP yang sama.',
                    'then' => 'Sistem mengunci sementara proses login selama 60 detik dengan respons HTTP 429 Too Many Requests.',
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/auth/login & POST /logout',
                'middleware' => ['web', 'throttle:5,1'],
                'request_schema' => '{"email": "admin@' . Str::slug($businessName) . '.com", "password": "SecurePassword123!", "remember": true}',
                'response_schema' => '{"success": true, "user": {"id": "01J...ULID", "name": "Admin", "role": "superadmin"}, "redirect_url": "/admin"}',
                'tasks' => [
                    'Daftarkan route auth dengan pembatasan laju (throttling 5 request/menit).',
                    'Pastikan token CSRF divalidasi pada setiap request mutasi.',
                ],
            ];
            $mission = "Implementasikan modul Otentikasi & RBAC dengan Laravel 13 (PHP 8.5+), Filament v5, dan PostgreSQL Strict ULID.";
            $targetFiles = ['app/Models/User.php', 'database/migrations/xxxx_create_users_table.php', 'app/Http/Requests/Auth/LoginRequest.php', 'resources/views/auth/login.blade.php'];
            $verifyCommand = "php artisan test --filter=AuthenticationTest";

            return [
                'category' => 'SECURITY_RBAC',
                'category_label' => 'Otentikasi & Keamanan Akses',
                'related_table' => $relatedTable['name'] ?? 'users',
                'related_table_data' => $relatedTable,
                'user_story' => "Sebagai {$primaryActor} atau {$clientActor}, saya ingin dapat mengautentikasi diri secara aman dengan sesi terisolasi dan izin akses berbasis peran (RBAC), sehingga data sensitif organisasi terlindungi dari akses ilegal.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'Form auth high-contrast (bukan template abu-abu hambar), surface zinc-900 di dark mode / crisp white di light mode, focus-ring emerald-500 dengan transition-all duration-200.',
                    'states' => 'Loading pulse bar pada tombol submit saat auth diproses, error banner inline dengan warna rose-500, auto-focus pada field email.',
                    'components' => ['LoginForm.blade.php', 'PasswordInputToggle.js', 'AuthToastNotifier.js'],
                    'tasks' => [
                        'Desain formulir otentikasi responsif dengan WCAG AAA contrast ratio.',
                        'Implementasikan toggle visibilitas kata sandi (eye icon SVG fluid).',
                        'Integrasikan notifikasi toast floating saat login gagal tanpa dialog modal native.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => 'Tabel `users` dengan `->ulid("id")->primary()`, kolom `role`, `status`, `remember_token`, timestamps.',
                    'scalability_guardrail' => 'Index pada kolom `email`, password hashing Argon2id / Bcrypt 12 rounds, keyset cursor pagination untuk listing user.',
                    'tasks' => [
                        'Buat migration tabel `users` dengan Primary Key ULID standar PostgreSQL.',
                        'Implementasikan Form Request `LoginRequest` dengan validasi email ketat dan sanitasi string.',
                        'Terapkan middleware `CheckRole` atau Filament Shield Policy untuk isolasi hak akses.',
                    ],
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-AUTH_RBAC', $prefix, $index),
                    $title,
                    $businessName,
                    $mission,
                    $targetFiles,
                    $verifyCommand,
                    $relatedTable,
                    $ac,
                    $integration,
                    $index
                ),
            ];
        }

        // Domain 2: KATALOG / LISTING / PENCARIAN / FILTER
        if (str_contains($lower, 'katalog') || str_contains($lower, 'filter') || str_contains($lower, 'cari') || str_contains($lower, 'search') || str_contains($lower, 'produk') || str_contains($lower, 'properti') || str_contains($lower, 'daftar')) {
            $ac = [
                [
                    'scenario' => 'Filter Reaktif Sub-100ms Tanpa Reload Halaman Penuh',
                    'given' => 'Katalog memiliki data yang siap ditampilkan.',
                    'when' => 'Pengguna memilih parameter filter (kategori, kisaran harga, atau lokasi).',
                    'then' => 'Daftar item diperbarui secara reaktif dalam waktu <100ms dengan mempertahankan posisi scroll dan riwayat URL query string.',
                ],
                [
                    'scenario' => 'Ketersediaan Skeleton Loader dan Empty State',
                    'given' => 'Pencarian dilakukan dengan kata kunci spesifik.',
                    'when' => 'Data sedang diunduh dari server ATAU tidak ada hasil yang cocok.',
                    'then' => 'Sistem menampilkan Skeleton Loader shimmer (anti layout-shift) saat loading, atau Empty State empatik dengan tombol Reset Filter jika data nihil.',
                ],
                [
                    'scenario' => 'Stabilitas Paginasi Keyset Cursor O(1)',
                    'given' => 'Katalog menampung puluhan ribu data.',
                    'when' => 'Pengguna melakukan navigasi halaman selanjutnya (Load More).',
                    'then' => 'Sistem menggunakan cursorPaginate() berbasis pointer ID ULID sehingga performa query database tetap O(1) konstan tanpa beban memory leak.',
                ],
            ];
            $integration = [
                'endpoint' => 'GET /api/v1/catalog?cursor=...&category=...&q=...',
                'middleware' => ['web', 'throttle:60,1'],
                'request_schema' => '{"q": "Jakarta", "category_id": "01J...", "min_price": 50000000, "cursor": "eyJpZCI..."}',
                'response_schema' => '{"data": [{"id": "01J...", "title": "Unit A", "price": 150000000}], "next_cursor": "eyJpZCI...", "has_more": true}',
                'tasks' => [
                    'Daftarkan endpoint API / Livewire Action dengan response JSON terformat.',
                    'Integrasikan ETag dan HTTP Cache-Control (max-age=60) untuk optimasi bandwidth.',
                ],
            ];
            $mission = "Bangun modul Katalog & Filter Data Interaktif dengan Keyset Cursor Pagination O(1) dan UI Anti-AI-Slop.";
            $targetFiles = ['app/Models/CatalogItem.php', 'database/migrations/xxxx_create_catalog_items_table.php', 'app/Http/Controllers/CatalogController.php', 'resources/views/catalog/index.blade.php'];
            $verifyCommand = "php artisan test --filter=CatalogFilterTest";

            return [
                'category' => 'DISCOVERY_CATALOG',
                'category_label' => 'Katalog & Pencarian O(1)',
                'related_table' => $relatedTable['name'] ?? 'catalog_items',
                'related_table_data' => $relatedTable,
                'user_story' => "Sebagai {$clientActor}, saya ingin menelusuri katalog data {$businessName} dengan filter dinamis dan pencarian instan, sehingga saya dapat menemukan informasi yang paling cocok dengan kebutuhan saya dalam waktu singkat.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'Grid asimetris modern (1 kolom mobile, 2 kolom tablet, 3-4 kolom desktop), aspect-ratio 16:9/4:3 gambar dengan object-cover (anti squish), badge kategori HSL berkarakter.',
                    'states' => 'Skeleton cards beranimasi shimmer saat fetch data, hover card lift effect (hover:-translate-y-1 hover:shadow-lg transition-all duration-300), empty state dengan tombol Reset.',
                    'components' => ['CatalogGrid.blade.php', 'CatalogCard.blade.php', 'CatalogFilterBar.blade.php', 'SkeletonCard.blade.php'],
                    'tasks' => [
                        'Bangun komponen grid katalog yang mobile-first dan responsif.',
                        'Implementasikan filter drawer interaktif dengan binding URL query string.',
                        'Sediakan skeleton loader shimmer untuk mencegah cumulative layout shift (CLS = 0).',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Tabel `catalog_items` dengan `->ulid('id')->primary()`, indexes compound `[status, created_at]`, foreignUlid, soft-deletes.",
                    'scalability_guardrail' => "Hindari standard paginate() (OFFSET). Wajib gunakan cursorPaginate() dengan ->orderBy('id', 'desc') untuk stabilitas kursor O(1).",
                    'tasks' => [
                        'Buat migration tabel katalog dengan ULID primary key dan indeks pada kolom-kolom filter.',
                        'Implementasikan Query Scope `scopeFilter()` di Model untuk menangani parameter pencarian secara efisien.',
                        'Pastikan eager loading `with(...)` diterapkan untuk mematikan ancaman N+1 query problem.',
                    ],
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-CATALOG_FILTER', $prefix, $index),
                    $title,
                    $businessName,
                    $mission,
                    $targetFiles,
                    $verifyCommand,
                    $relatedTable,
                    $ac,
                    $integration,
                    $index
                ),
            ];
        }

        // Domain 3: INTAKE / FORMULIR / PESANAN / RESERVASI / TRANSAKSI / DOA
        if (str_contains($lower, 'form') || str_contains($lower, 'intake') || str_contains($lower, 'reservasi') || str_contains($lower, 'pesan') || str_contains($lower, 'transaksi') || str_contains($lower, 'doa') || str_contains($lower, 'warta') || str_contains($lower, 'konsultasi')) {
            $ac = [
                [
                    'scenario' => 'Validasi Data Masukan Secara Real-Time',
                    'given' => 'Pengguna sedang mengisi kolom nomor telepon, email, dan data wajib.',
                    'when' => 'Pengguna mengetik format yang keliru (misal menginput angka 0 di awal nomor WhatsApp ber-country code).',
                    'then' => 'Sistem otomatis memfilter atau menampilkan petunjuk inline ramah sebelum formulir dikirim.',
                ],
                [
                    'scenario' => 'Pencegahan Double-Submit via Idempotency Key',
                    'given' => 'Formulir siap dikirim dengan payload valid.',
                    'when' => 'Pengguna menekan tombol submit berkali-kali karena koneksi internet lambat.',
                    'then' => 'Sistem memproses request pertama dengan token idempotency unik, mendisabled tombol, dan mengabaikan request duplikat tanpa error.',
                ],
                [
                    'scenario' => 'Pencatatan Transaksi & Penerbitan Kode Referensi',
                    'given' => 'Data formulir berhasil lolos validasi server-side.',
                    'when' => 'Transaksi disimpan ke database.',
                    'then' => 'Sistem membungkus operasi dalam DB::transaction, menghasilkan kode referensi unik, mencatat IP & timestamp audit trail, serta mendispatch event background notifikasi.',
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/intake',
                'middleware' => ['web', 'throttle:15,1'],
                'request_schema' => '{"name": "Budi Santoso", "country_code": "62", "phone": "81234567890", "notes": "Pengajuan minat unit A"}',
                'response_schema' => '{"success": true, "reference_code": "NPRO-TRX-2026-9812", "message": "Pendaftaran berhasil dicatat."}',
                'tasks' => [
                    'Daftarkan endpoint API intake dengan header `X-Idempotency-Key`.',
                    'Hubungkan submission dengan queue worker untuk pengiriman notifikasi instan.',
                ],
            ];
            $mission = "Bangun modul Formulir Intake & Transaksi dengan validasi server-side ketat, idempotency guard, dan UI modern.";
            $targetFiles = ['app/Models/Intake.php', 'database/migrations/xxxx_create_intakes_table.php', 'app/Http/Requests/StoreIntakeRequest.php', 'app/Actions/CreateIntakeAction.php'];
            $verifyCommand = "php artisan test --filter=IntakeSubmissionTest";

            return [
                'category' => 'DATA_INTAKE_TRANSACTION',
                'category_label' => 'Formulir Intake & Transaksi Terverifikasi',
                'related_table' => $relatedTable['name'] ?? 'intakes',
                'related_table_data' => $relatedTable,
                'user_story' => "Sebagai {$clientActor}, saya ingin mengisi formulir intake/reservasi dengan alur yang terpandu dan validasi instan, sehingga permintaan saya tercatat akurat dan langsung mendapatkan konfirmasi resmi.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'Floating input labels, selector kode negara bendera interaktif, step progress bar indikatif, validasi error inline dengan aksen rose-500, focus ring emerald-500.',
                    'states' => 'Tombol kirim memiliki state loading spinner micro, konfirmasi sukses menggunakan modal modern backdrop-blur atau redirect halaman terima kasih ber-QR code.',
                    'components' => ['IntakeForm.blade.php', 'PhoneCountryCodePicker.blade.php', 'StepProgressBar.blade.php'],
                    'tasks' => [
                        'Buat form intake multi-langkah yang nyaman diakses lewat smartphone.',
                        'Pasang filter anti angka 0 di awal untuk nomor telepon setelah country code.',
                        'Tampilkan feedback konfirmasi menggunakan floating Toast sistem, bukan alert native.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Tabel `transactions` / `intakes` dengan `->ulid('id')->primary()`, `reference_code` unique, `status` enum, audit metadata JSONB.",
                    'scalability_guardrail' => 'Gunakan DB::transaction untuk integritas data ACID, dispatch event asinkron agar response time HTTP tetap <80ms.',
                    'tasks' => [
                        'Migration tabel intake dengan Primary Key ULID dan foreign keys tervalidasi.',
                        'Form Request `StoreIntakeRequest` dengan sanitasi anti-XSS dan rules ketat.',
                        'Action class `CreateIntakeAction` yang menangani logika bisnis secara terisolasi.',
                    ],
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-DATA_INTAKE', $prefix, $index),
                    $title,
                    $businessName,
                    $mission,
                    $targetFiles,
                    $verifyCommand,
                    $relatedTable,
                    $ac,
                    $integration,
                    $index
                ),
            ];
        }

        // Domain 4: DASBOR / ADMIN / GOVERNANCE / FILAMENT
        if (str_contains($lower, 'dasbor') || str_contains($lower, 'admin') || str_contains($lower, 'rekap') || str_contains($lower, 'kelola') || str_contains($lower, 'approval')) {
            $ac = [
                [
                    'scenario' => 'Pemuatan Dasbor Agregasi Cepat (<150ms)',
                    'given' => 'Database memiliki puluhan ribu catatan transaksi.',
                    'when' => 'Admin membuka halaman utama dasbor operasional.',
                    'then' => 'Widget ringkasan metrik menampilkan data agregasi dari cache Redis / query terindeks tanpa menyebabkan slow query pada server.',
                ],
                [
                    'scenario' => 'Tabel Administrasi dengan Filter Cepat & Inline Actions',
                    'given' => 'Admin sedang mengelola antrean permohonan masuk.',
                    'when' => 'Admin memfilter berdasarkan status (Pending, Disetujui, Ditolak).',
                    'then' => 'Tabel Filament v5 menampilkan data relevan secara instan dengan aksi persetujuan (Approve/Reject) langsung dari baris tabel.',
                ],
                [
                    'scenario' => 'Audit Trail Pencatatan Setiap Aksi Perubahan',
                    'given' => 'Data penting mengalami perubahan status oleh staf admin.',
                    'when' => 'Perubahan disimpan ke sistem.',
                    'then' => 'Sistem mencatat identitas admin, alamat IP, timestamp waktu, dan diff nilai sebelum/sesudah di tabel audit trail.',
                ],
            ];
            $integration = [
                'endpoint' => 'GET /admin/resources & POST /admin/resources/action',
                'middleware' => ['web', 'auth:web'],
                'request_schema' => '{"action": "approve", "record_id": "01J...ULID", "reason": "Dokumen lengkap"}',
                'response_schema' => '{"success": true, "message": "Status berhasil diperbarui."}',
                'tasks' => [
                    'Integrasikan audit trail observer pada model untuk mencatat aktivitas admin.',
                    'Hubungkan trigger approval dengan background job dispatch notifikasi.',
                ],
            ];
            $mission = "Implementasikan Dasbor Administrasi Filament v5 dengan schema form aman dan tabel filter instan.";
            $targetFiles = ['app/Filament/Resources/RecordResource.php', 'app/Filament/Widgets/StatsOverview.php', 'app/Policies/RecordPolicy.php'];
            $verifyCommand = "php artisan test --filter=AdminPanelAccessTest";

            return [
                'category' => 'GOVERNANCE_OPERATIONS',
                'category_label' => 'Dasbor Operasional & Tata Kelola',
                'related_table' => $relatedTable['name'] ?? null,
                'related_table_data' => $relatedTable,
                'user_story' => "Sebagai {$primaryActor}, saya ingin memantau KPI bisnis, meninjau data masuk, dan memproses status approval dalam satu panel kendali terpusat, sehingga operasional harian berjalan tanpa hambatan.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'Antarmuka Filament v5 Enterprise dengan dark mode onyx / light mode crisp, widget card dengan indikator pertumbuhan tren persentase, status badge bersahabat.',
                    'states' => 'Skeleton table rows saat data berpindah halaman, modal dialog konfirmasi yang elegan untuk aksi destruktif (tanpa window.confirm).',
                    'components' => ['AdminResourceTable.php', 'StatsOverviewWidget.php', 'ActionConfirmationModal.blade.php'],
                    'tasks' => [
                        'Konfigurasi Filament v5 Resource lengkap dengan form input dan kolom tabel.',
                        'Tambahkan widget kartu statistik metrik utama (Total Data, Pending, Disetujui).',
                        'Terapkan custom badge semantik untuk indikasi status yang mudah terbaca.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Filament Resource Class `App\Filament\Resources\...Resource.php` dengan implementasi method form Schema v5.",
                    'scalability_guardrail' => "Wajib menggunakan signature `form(\Filament\Schemas\Schema \$form): \Filament\Schemas\Schema` (Filament v5). Terapkan eager loading relasi untuk mencegah N+1.",
                    'tasks' => [
                        'Buat Filament Resource dengan schema form dan table builder standar Filament v5.',
                        'Pasang Policy autorisasi hak akses berbasis peran pada Model terkait.',
                        'Optimasi query tabel admin dengan indeks kolom status dan created_at.',
                    ],
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-ADMIN_OPS', $prefix, $index),
                    $title,
                    $businessName,
                    $mission,
                    $targetFiles,
                    $verifyCommand,
                    $relatedTable,
                    $ac,
                    $integration,
                    $index
                ),
            ];
        }

        // Domain 5: EKSPOR / LAPORAN / PDF / EXCEL
        if (str_contains($lower, 'ekspor') || str_contains($lower, 'laporan') || str_contains($lower, 'pdf') || str_contains($lower, 'excel') || str_contains($lower, 'cetak')) {
            $ac = [
                [
                    'scenario' => 'Ekspor Excel Tanpa Kehabisan Memori Server (Chunking)',
                    'given' => 'Terdapat ribuan baris data yang diekspor.',
                    'when' => 'Admin menekan tombol Ekspor Excel.',
                    'then' => 'Sistem men-streaming data menggunakan generator chunk (cursor), menghasilkan file .XLSX dengan header beku (freeze panes) dalam <3 detik tanpa lonjakan RAM.',
                ],
                [
                    'scenario' => 'Cetak Dokumen PDF Terstandarisasi',
                    'given' => 'Admin atau pengguna mencetak tanda bukti atau rangkuman laporan.',
                    'when' => 'File PDF diunduh.',
                    'then' => 'Tata letak mematuhi standar A4 cetak rapi, dilengkapi kop resmi, nomor halaman dinamis, dan verifikasi cryptographic hash atau QR code.',
                ],
            ];
            $integration = [
                'endpoint' => 'GET /admin/reports/export?format=xlsx&start_date=2026-01-01&end_date=2026-12-31',
                'middleware' => ['web', 'auth:web'],
                'request_schema' => 'Query parameters: format (xlsx|pdf), start_date, end_date, status',
                'response_schema' => 'Binary file stream with Content-Disposition attachment',
                'tasks' => [
                    'Daftarkan route download laporan dengan proteksi otorisasi admin.',
                ],
            ];
            $mission = "Bangun fitur Ekspor Laporan PDF & Excel hemat memori menggunakan streaming cursor.";
            $targetFiles = ['app/Services/ReportExportService.php', 'app/Http/Controllers/ReportExportController.php', 'resources/views/pdf/report.blade.php'];
            $verifyCommand = "php artisan test --filter=ReportExportTest";

            return [
                'category' => 'REPORTING_EXPORT',
                'category_label' => 'Pelaporan & Ekspor Data (PDF / Excel)',
                'related_table' => $relatedTable['name'] ?? null,
                'related_table_data' => $relatedTable,
                'user_story' => "Sebagai {$primaryActor}, saya ingin mengunduh laporan rekapitulasi data dalam format PDF dan Excel terformat rapi, sehingga saya dapat menyajikan data resmi ke pemangku kepentingan tanpa perlu merapikan dokumen manual.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'Modal pemilih filter tanggal (Date Range Picker), dropdown format (Excel XLSX, PDF Dokumen) dengan icon representatif, download progress feedback.',
                    'states' => 'Tombol unduh beralih ke state animasi progress saat file disintesis, toast notifikasi sukses saat file mulai terunduh.',
                    'components' => ['ExportReportModal.blade.php', 'ReportFormatSelector.blade.php'],
                    'tasks' => [
                        'Buat modal dialog interaktif untuk memilih periode dan kolom ekspor.',
                        'Sediakan tombol unduh cepat dengan indikator visual proses unduhan.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Service class `App\Services\ReportExportService.php` dengan engine streaming (FastExcel / DomPDF).",
                    'scalability_guardrail' => 'Dilarang memuat seluruh koleksi ke memory array ($query->get()). Wajib gunakan $query->cursor() untuk membatasi konsumsi RAM <32MB.',
                    'tasks' => [
                        'Implementasikan export handler dengan streaming response langsung ke browser.',
                        'Desain template Blade khusus PDF (`resources/views/pdf/report.blade.php`) dengan styling ramah DomPDF.',
                    ],
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-EXPORT_REPORT', $prefix, $index),
                    $title,
                    $businessName,
                    $mission,
                    $targetFiles,
                    $verifyCommand,
                    $relatedTable,
                    $ac,
                    $integration,
                    $index
                ),
            ];
        }

        // Domain 6: NOTIFIKASI & WHATSAPP GATEWAY
        if (str_contains($lower, 'notifikasi') || str_contains($lower, 'whatsapp') || str_contains($lower, 'gateway') || str_contains($lower, 'email') || str_contains($lower, 'wa')) {
            $ac = [
                [
                    'scenario' => 'Pengiriman Notifikasi WhatsApp via Background Queue',
                    'given' => 'Terjadi event pemicu (misal formulir baru atau update status).',
                    'when' => 'Event didispatch oleh sistem.',
                    'then' => 'Pesan WhatsApp dikirimkan melalui background queue job dalam <5 detik tanpa memperlambat respon halaman web bagi pengguna.',
                ],
                [
                    'scenario' => 'Ketahanan Terhadap API Gateway Downtime (Retry Backoff)',
                    'given' => 'API provider WhatsApp mengalami network timeout atau error sementara.',
                    'when' => 'Job pengiriman notifikasi dieksekusi.',
                    'then' => 'Sistem otomatis mencoba ulang (retry 3x dengan jeda bertingkat) dan mencatat status di log sistem tanpa merusak data transaksi utama.',
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/webhooks/whatsapp/delivery-report',
                'middleware' => ['api'],
                'request_schema' => '{"message_id": "WA-12345", "status": "DELIVERED", "timestamp": 1790866000}',
                'response_schema' => '{"success": true}',
                'tasks' => [
                    'Daftarkan webhook listener untuk menerima status laporan pengiriman (DLR) dari gateway WhatsApp.',
                ],
            ];
            $mission = "Bangun sistem Notifikasi WhatsApp asinkron dengan Queue Jobs dan mekanisme retry cerdas.";
            $targetFiles = ['app/Services/WhatsAppGatewayService.php', 'app/Jobs/SendWhatsAppNotificationJob.php', 'database/migrations/xxxx_create_notification_logs_table.php'];
            $verifyCommand = "php artisan test --filter=WhatsAppNotificationTest";

            return [
                'category' => 'COMMUNICATION_DISPATCH',
                'category_label' => 'Notifikasi WhatsApp & Gateway Terotomasi',
                'related_table' => $relatedTable['name'] ?? 'notification_logs',
                'related_table_data' => $relatedTable,
                'user_story' => "Sebagai {$clientActor} dan {$primaryActor}, saya ingin menerima notifikasi status real-time via WhatsApp dan Email setiap kali ada aktivitas transaksi penting, sehingga seluruh pihak mendapatkan konfirmasi instan tanpa koordinasi manual.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'Badge status notifikasi terkirim (Warna emerald untuk Terkirim, amber untuk Dalam Antrean, rose untuk Gagal kirim), preview template pesan.',
                    'states' => 'Indikator live sync status pesan di tabel admin.',
                    'components' => ['WhatsAppPreviewModal.blade.php', 'DeliveryStatusBadge.blade.php'],
                    'tasks' => [
                        'Tampilkan riwayat pengiriman notifikasi di panel admin.',
                        'Sediakan tombol kirim ulang pesan (Resend WhatsApp) jika terjadi kegagalan transmisi.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Tabel `notification_logs` dengan `->ulid('id')->primary()`, target_phone, message_body, status, error_details, timestamps.",
                    'scalability_guardrail' => 'Wajib menggunakan Laravel Queues (ShouldQueue) pada Redis/Database connection. Dilarang melakukan synchronous HTTP call di controller.',
                    'tasks' => [
                        'Buat Service class `WhatsAppGatewayService` dengan format nomor E.164 otomatis.',
                        'Buat Job class `SendWhatsAppNotificationJob` yang mengimplementasikan `ShouldQueue`.',
                    ],
                ],
                'integration' => $integration,
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-WA_NOTIF', $prefix, $index),
                    $title,
                    $businessName,
                    $mission,
                    $targetFiles,
                    $verifyCommand,
                    $relatedTable,
                    $ac,
                    $integration,
                    $index
                ),
            ];
        }

        // Domain 7: GENERAL BUSINESS CORE SPECIFICATION
        $cleanSlug = strtoupper(substr(str_replace('-', '_', $titleSlug), 0, 14));
        $ac = [
            [
                'scenario' => 'Eksekusi Fungsional Utama Berjalan Normal',
                'given' => "Pengguna memiliki hak akses terhadap modul {$title}.",
                'when' => 'Pengguna menjalankan alur utama fitur sesuai petunjuk sistem.',
                'then' => 'Sistem memproses data secara akurat, menyimpan hasil dengan integritas ACID, dan memperbarui status tampilan secara reaktif.',
            ],
            [
                'scenario' => 'Penanganan Error dan Validasi Input',
                'given' => 'Data yang diberikan tidak memenuhi syarat spesifikasi.',
                'when' => 'Data dikirimkan ke server.',
                'then' => 'Sistem mengembalikan pesan peringatan yang jelas dan mencegah penyimpanan data yang rusak.',
            ],
        ];
        $integration = [
            'endpoint' => '/api/v1/' . Str::kebab(Str::plural($modelName)),
            'middleware' => ['web'],
            'request_schema' => '{"title": "Sample Record", "status": "active"}',
            'response_schema' => '{"success": true, "data": {"id": "01J...", "title": "Sample Record"}}',
            'tasks' => [
                'Daftarkan endpoint RESTful atau Livewire Handler dengan kontrak data ketat.',
            ],
        ];
        $mission = "Implementasikan modul {$title} sesuai spesifikasi arsitektur Laravel 13 (PHP 8.5+), Filament v5, dan PostgreSQL Strict ULID.";
        $targetFiles = ['app/Models/' . $modelName . '.php', 'app/Actions/' . $modelName . 'Action.php'];
        $verifyCommand = "php artisan test --filter=" . $modelName . "Test";

        return [
            'category' => 'CORE_FEATURE',
            'category_label' => 'Modul Inti Bisnis',
            'related_table' => $relatedTable['name'] ?? Str::snake(Str::plural($modelName)),
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$clientActor}, saya ingin menggunakan fitur {$title} pada sistem {$businessName}, sehingga {$desc}.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Desain komponen berkarakter dengan tipografi tajam, margin konsisten, transisi interaksi halus, dan kompatibilitas dark/light mode.',
                'states' => 'Loading skeleton saat proses komputasi berlangsung, feedback aksi menggunakan sistem Toast, bukan dialog alert native.',
                'components' => [$modelName . 'Component.blade.php', $modelName . 'Card.blade.php'],
                'tasks' => [
                    "Bangun antarmuka pengguna untuk fitur {$title} dengan prinsip Anti-AI-Slop.",
                    'Pastikan pengalaman navigasi mulus di perangkat seluler maupun desktop.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Tabel `" . Str::snake(Str::plural($modelName)) . "` dengan `->ulid('id')->primary()`, proper indexation, soft deletes.",
                'scalability_guardrail' => 'Terapkan Keyset Cursor Pagination O(1) untuk seluruh endpoint listing data.',
                'tasks' => [
                    "Migration tabel untuk fitur {$title} dengan Primary Key ULID standar PostgreSQL.",
                    "Service atau Action class untuk merangkum logika bisnis secara independen.",
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => self::buildAgentPrompt(
                sprintf('FEAT-%s-%02d-%s', $prefix, $index, $cleanSlug),
                $title,
                $businessName,
                $mission,
                $targetFiles,
                $verifyCommand,
                $relatedTable,
                $ac,
                $integration,
                $index
            ),
        ];
    }


    /**
     * Build an exact copy-pasteable prompt template for AI Coding Agents.
     * Injects concrete ERD table schemas, Acceptance Criteria (Given-When-Then),
     * API integration payloads, and prerequisite step gating.
     */
    public static function buildAgentPrompt(
        string $featureId,
        string $title,
        string $businessName,
        string $mission,
        array $targetFiles,
        string $verifyCommand,
        ?array $relatedTable = null,
        array $acceptanceCriteria = [],
        ?array $integration = null,
        int $stepIndex = 1
    ): string {
        $filesStr = implode("\n- ", $targetFiles);

        // 1. Prerequisite step dependency warning
        $stepPrerequisite = "";
        if ($stepIndex > 1) {
            $stepPrerequisite = <<<DEP
#### ⚠️ PREREQUISITE STEP DEPENDENCY (STRICT GATING):
- **Prerequisite**: STEP 1 (Fondasi Global, Database Schema ULID, Base Layout, and Core Auth) MUST be 100% completed and verified before starting this feature.
- **Strict Rule**: Do NOT create or run isolated feature logic that references unmigrated tables or unconfigured base classes. Ensure baseline database tables and user roles exist first.
DEP;
        } else {
            $stepPrerequisite = <<<DEP
#### 🚀 STEP 1 - FOUNDATIONAL STEP (GLOBAL FOUNDATION):
- **Role**: Foundational root of the system.
- **Strict Rule**: Create core database migrations with ULID primary keys, authentication/RBAC guardrails, base layouts, and central configuration before implementing downstream domain features.
DEP;
        }

        // 2. Concrete ERD schema section
        $schemaSection = "";
        if (!empty($relatedTable) && !empty($relatedTable['name'])) {
            $tblName = $relatedTable['name'];
            $tblDesc = $relatedTable['description'] ?? 'Domain operational table.';
            $colsFormatted = [];
            foreach ($relatedTable['columns'] ?? [] as $col) {
                $cName = $col['name'] ?? 'column';
                $cType = $col['type'] ?? 'string';
                $cNullable = !empty($col['nullable']) ? 'nullable' : 'required';
                $cIndex = !empty($col['is_index']) ? ' [INDEXED]' : '';
                $cDesc = $col['description'] ?? '';
                $colsFormatted[] = "  * `{$cName}` ({$cType}, {$cNullable}{$cIndex}): {$cDesc}";
            }
            $colsStr = !empty($colsFormatted) ? implode("\n", $colsFormatted) : "  * `id` (ULID, primary key)\n  * timestamps";

            $schemaSection = <<<SCHEMA
#### Concrete Database Table Target (`{$tblName}`):
- **Table**: `{$tblName}`
- **Purpose**: {$tblDesc}
- **Primary Key Standard**: ALWAYS use ULID (`CHAR(26)` / `->ulid('id')->primary()`). NEVER use AUTO_INCREMENT, ->id(), or ->uuid().
- **Table Columns Specification**:
{$colsStr}
SCHEMA;
        } else {
            $schemaSection = <<<SCHEMA
#### Database Schema Specification:
- Use PostgreSQL 16 Strict ULID (`->ulid('id')->primary()`) on business domain models.
- Foreign keys must use `->foreignUlid('parent_id')`.
- All list queries must use Keyset Cursor Pagination (`cursorPaginate()`).
SCHEMA;
        }

        // 3. Concrete Acceptance Criteria (DoD)
        $acSection = "";
        $normalizedAc = self::normalizeAcceptanceCriteria($acceptanceCriteria, $title);
        if (!empty($normalizedAc)) {
            $scenarios = [];
            foreach ($normalizedAc as $ac) {
                $scName = $ac['scenario'] ?? 'Skenario Fungsional';
                $scGiven = $ac['given'] ?? 'Kondisi awal valid.';
                $scWhen = $ac['when'] ?? 'Aksi pengguna dijalankan.';
                $scThen = $ac['then'] ?? 'Sistem memberikan respons yang diharapkan.';
                $scenarios[] = "- **Scenario: {$scName}**\n  - **Given**: {$scGiven}\n  - **When**: {$scWhen}\n  - **Then**: {$scThen}";
            }
            $acStr = implode("\n", $scenarios);
            $acSection = <<<AC
#### Concrete Acceptance Criteria (Definition of Done):
{$acStr}
AC;
        }

        // 4. API & Integration Contract
        $integrationSection = "";
        if (!empty($integration) && !empty($integration['endpoint'])) {
            $mwStr = is_array($integration['middleware'] ?? null)
                ? implode(', ', $integration['middleware'])
                : ($integration['middleware'] ?? 'web');
            $reqSchema = is_string($integration['request_schema'] ?? '')
                ? $integration['request_schema']
                : json_encode($integration['request_schema'] ?? [], JSON_PRETTY_PRINT);
            $resSchema = is_string($integration['response_schema'] ?? '')
                ? $integration['response_schema']
                : json_encode($integration['response_schema'] ?? [], JSON_PRETTY_PRINT);

            $integrationSection = <<<INTG
#### API & Integration Contract:
- **Endpoint**: `{$integration['endpoint']}`
- **Middleware**: `[{$mwStr}]`
- **Request Contract**:
```json
{$reqSchema}
```
- **Response Contract**:
```json
{$resSchema}
```
INTG;
        }

        return <<<PROMPT
### AI CODE AGENT MISSION // SPEC-DRIVEN DIRECTIVE
**Feature ID**: `{$featureId}`
**Feature Title**: {$title}
**Project Scope**: {$businessName}

{$stepPrerequisite}

#### Context & Architectural Guardrails:
1. **Framework & Engine**: Laravel 13 (PHP 8.5+), Filament v5, Livewire 4, PostgreSQL 16+.
2. **Primary Key Standard**: ALWAYS use ULID (`->ulid('id')->primary()`) on business tables. NEVER use AUTO_INCREMENT, ->id(), or ->uuid().
3. **Pagination Rule**: ALWAYS use Keyset Cursor Pagination (`cursorPaginate()`) with `->orderBy('id', 'asc')`. NEVER use offset `paginate()`.
4. **Anti-AI-Slop & Precision UI**:
   - **Zero Native Dialogs**: Strict ban on `window.alert()`, `confirm()`, and `prompt()`. ALWAYS use centralized floating Toast (`window.showToast`) and curated Tailwind backdrop-blur modals.
   - **Subtle Round Corners**: Use thin, crisp borders/corners (`rounded-none`, `rounded-xs`, `rounded-sm`, max `rounded-md`). STRICTLY AVOID capsule/pill shapes (`rounded-full`).
   - **Interactive & 3D**: High-contrast typography pairing, loading skeletons, Framer Motion or Three.js 60fps micro-animations, avoiding generic AI slop.
   - **Thousand Separators**: Every number or currency >= 1,000 must use thousand separator masking (e.g. `10.000.000` / `10,000,000`).
   - **Local FontAwesome Icons**: Use representative local SVG icons (`config/fontawesome.php`), never external icon fonts with CDN latency.
5. **Multi-Language Architecture (Frontend & Backend)**:
   - **Backend**: Filament v5 native dual-language (English & Indonesia) with multilingual database columns stored as JSON (`{"id": "...", "en": "..."}`).
   - **Frontend**: 2-Tier Language support: Tier 1 Native ID/EN switcher + Tier 2 Google Translate plugin configured from backend admin.
6. **Decoupled CMS & Global Settings**:
   - **Modular Page Plugins**: Page features are built as configurable, educational, and user-friendly plugins.
   - **Isolated Global Layout**: Top/sidebar navigation, footer, and global alerts MUST be decoupled outside page plugins so they can be adjusted independently.
   - **Centralized Global Settings**: Manage Company Profile, Contact Info, Social Links, and SEO Schema.org with strict privacy (never exposing internal API secrets).
   - **Phone Inputs**: ALWAYS use Country Zone selector (`config/country_zones.php`, e.g. +62, +65, +1) with E.164 format.
7. **Bulletproof Scalability (Millions of Records & Viewers)**:
   - Cache forever (`Cache::rememberForever()`) on Redis/memory for CMS pages, global settings, and Schema.org.
   - Automatic Event-Driven Reset: Invalidate caches immediately on model `saved` and `deleted` hooks.
8. **SEO Optimization Standard**: Tab browser titles MUST follow `[NAMA DOMAIN - NAMA PAGE]` alongside comprehensive Schema.org JSON-LD structured data.
9. **Anti-AI Malware Security Suite**: Modern multi-layered defense (Honeypot bot traps, CSP, rate limiting) with educational feedback for visitors.
10. **Admin AI Agentic Engine**: Knowledge base, RAG (Retrieval-Augmented Generation), and native tool calling to prevent hallucination and empower executive decision-making.
11. **Filament v5 Form Rule**: Always use `\Filament\Schemas\Schema` method signature for `form()`.
12. **Filament v5 Curator Image Standard**: Every image upload input in Filament v5 MUST use Curator Picker and specify a shallow directory mapping (maximum 1-2 levels, e.g. ->directory('products') or ->directory('branding')). Deep recursive directory nesting is strictly forbidden to optimize Linux OS filesystem inodes and eliminate RAM overhead during folder scans.
13. **Full-Featured Rich Text Standard**: Every textarea for lengthy descriptions/articles MUST use a standardized full-featured Rich Text Editor class (e.g. `\App\Support\FilamentRichEditor::make(...)`) with all toolbar capabilities enabled (H1-H6, formatting, lists, tables, links, code) AND integrated Curator media picker for image insertions.
14. **Strict Clean Code & Anti-Bloat Architecture (Anti-God Object)**:
    - **Single Responsibility Principle**: Every PHP class, Controller, Service, Blade View, or React Component MUST NEVER exceed 400 lines (>400 lines is a strict warning, >800 lines is a fatal architectural violation). Always decompose into cohesive, single-responsibility units.
    - **Modular Sub-Services & Sub-Controllers**: Decompose large controllers into focused Sub-Controllers under dedicated sub-namespaces, and large services into domain sub-services.
    - **Frontend Components & Custom Hooks**: Extract large components into atomic sub-components (`components/`, `modals/`) and encapsulate state/API logic into custom hooks.
    - **Template Isolation (.stub)**: Never write multi-hundred-line heredocs in PHP. Extract templates to `.stub` files and load via `StubLoader`.
    - **Surgical & Minimalist Diffs**: Always generate precise, token-conserving diffs to prevent regressions.

{$schemaSection}

{$acSection}

{$integrationSection}

#### Target Files to Create / Modify:
- {$filesStr}

#### Mission Description:
{$mission}

#### Verification & Quality Gate:
Execute the following verification command and ensure exit code 0 before marking the task complete:
```bash
{$verifyCommand}
```
PROMPT;
    }


    /**
     * Intelligently normalize acceptance criteria from any shape (array of strings, array of objects, single string, or empty)
     * into a structured, production-ready array of Gherkin scenario objects:
     * [
     *     [
     *         'scenario' => 'Skenario 1 (Happy Path)',
     *         'given' => '...',
     *         'when' => '...',
     *         'then' => '...',
     *     ]
     * ]
     */
    public static function normalizeAcceptanceCriteria(mixed $rawAc, string $featureTitle = '', string $userStory = ''): array
    {
        $featureTitle = trim($featureTitle);
        $titleLabel = $featureTitle ?: 'Fitur';

        // 1. If null or empty, provide standard 3-tier Gherkin scenarios
        if (empty($rawAc)) {
            return self::getDefaultAcceptanceCriteria($titleLabel, $userStory);
        }

        // 2. If it's a JSON string, decode first
        if (is_string($rawAc)) {
            $trimmed = trim($rawAc);
            if ((str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) ||
                (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}'))) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    $rawAc = $decoded;
                }
            } else {
                // Multiline or bulleted string
                $lines = preg_split('/[\r\n]+/', $trimmed);
                $rawAc = array_values(array_filter(array_map('trim', $lines)));
            }
        }

        // 3. If single associative object (not list)
        if (is_array($rawAc) && !isset($rawAc[0]) && !empty($rawAc)) {
            $rawAc = [$rawAc];
        }

        if (!is_array($rawAc) || empty($rawAc)) {
            return self::getDefaultAcceptanceCriteria($titleLabel, $userStory);
        }

        $normalized = [];

        foreach ($rawAc as $idx => $item) {
            $defaultIndexTitle = 'Skenario ' . ($idx + 1);

            // Case A: Item is an associative array / object
            if (is_array($item)) {
                $scenarioTitle = trim($item['scenario'] ?? ($item['rule'] ?? ($item['title'] ?? ($item['name'] ?? ''))));
                if (empty($scenarioTitle)) {
                    $scenarioTitle = $defaultIndexTitle;
                }

                $given = trim((string)($item['given'] ?? ''));
                $when = trim((string)($item['when'] ?? ''));
                $then = trim((string)($item['then'] ?? ''));

                // Check for fallback in case fields are missing or equal to '-'
                if ($given === '' || $given === '-') {
                    $given = self::deriveGivenCondition($scenarioTitle, $titleLabel, $idx);
                }
                if ($when === '' || $when === '-') {
                    $when = self::deriveWhenCondition($scenarioTitle, $titleLabel, $idx);
                }
                if ($then === '' || $then === '-') {
                    $then = !empty($item['description']) && $item['description'] !== '-'
                        ? trim((string)$item['description'])
                        : self::deriveThenCondition($scenarioTitle, $titleLabel, $idx);
                }

                $normalized[] = [
                    'scenario' => $scenarioTitle,
                    'given' => $given,
                    'when' => $when,
                    'then' => $then,
                ];
                continue;
            }

            // Case B: Item is a string
            if (is_string($item)) {
                $str = trim($item, " \t\n\r\0\x0B-•*");
                if (empty($str)) continue;

                // Check if string contains explicit Given/When/Then
                if (preg_match('/(?:Given|GIVEN)\s*:\s*(.*?)\s*(?:When|WHEN)\s*:\s*(.*?)\s*(?:Then|THEN)\s*:\s*(.*)/is', $str, $gm) ||
                    preg_match('/(?:Given|GIVEN)\s+(.*?)\s+(?:When|WHEN)\s+(.*?)\s+(?:Then|THEN)\s+(.*)/is', $str, $gm)) {
                    $normalized[] = [
                        'scenario' => $defaultIndexTitle,
                        'given' => trim($gm[1]),
                        'when' => trim($gm[2]),
                        'then' => trim($gm[3]),
                    ];
                    continue;
                }

                // Check if string follows "Title: Body" format
                $scenarioTitle = $defaultIndexTitle;
                $body = $str;

                if (preg_match('/^([^\:\r\n]{3,60})\s*:\s*(.*)$/us', $str, $parts)) {
                    $scenarioTitle = trim($parts[1]);
                    $body = trim($parts[2]);
                }

                // Intelligently derive Given/When/Then based on scenario type
                $given = self::deriveGivenCondition($scenarioTitle, $titleLabel, $idx);
                $when = self::deriveWhenCondition($scenarioTitle, $titleLabel, $idx);
                $then = !empty($body) ? $body : self::deriveThenCondition($scenarioTitle, $titleLabel, $idx);

                $normalized[] = [
                    'scenario' => $scenarioTitle,
                    'given' => $given,
                    'when' => $when,
                    'then' => $then,
                ];
            }
        }

        if (empty($normalized)) {
            return self::getDefaultAcceptanceCriteria($titleLabel, $userStory);
        }

        return $normalized;
    }


    /**
     * Generate 3 default Gherkin acceptance criteria scenarios for a feature.
     */
    public static function getDefaultAcceptanceCriteria(string $title, string $userStory = ''): array
    {
        return [
            [
                'scenario' => 'Skenario 1 (Happy Path - Eksekusi Sukses)',
                'given' => "Pengguna terautentikasi dan memasukkan parameter data {$title} yang valid.",
                'when' => "Pengguna mengirimkan permintaan eksekusi atau transaksi ke server.",
                'then' => "Sistem berhasil memproses data {$title} dengan status 200 OK dan menyimpan relasi database PostgreSQL Strict ULID.",
            ],
            [
                'scenario' => 'Skenario 2 (Validation Error & Integritas Data)',
                'given' => "Pengguna mengakses formulir atau endpoint modul {$title}.",
                'when' => "Terdapat parameter wajib yang kosong, tidak valid, atau format melanggar aturan form request.",
                'then' => "Sistem menolak mutasi data dan mengembalikan respons HTTP 422 Unprocessable Content dengan pesan error terstruktur.",
            ],
            [
                'scenario' => 'Skenario 3 (Edge Case & Perlindungan Konkurensi)',
                'given' => "Dua atau lebih sesi pengguna melakukan aksi transaksi secara simultan pada data {$title} yang sama.",
                'when' => "Permintaan mutasi data paralel diterima oleh server secara bersamaan.",
                'then' => "Sistem menerapkan database transaction locking untuk mencegah race condition dan menjamin integritas konsistensi ACID.",
            ],
        ];
    }


    public static function deriveGivenCondition(string $scenarioTitle, string $title, int $idx): string
    {
        $lower = strtolower($scenarioTitle);
        if (str_contains($lower, 'validation') || str_contains($lower, 'error') || str_contains($lower, '422') || str_contains($lower, 'validasi')) {
            return "Pengguna mengirimkan request pada antarmuka atau endpoint {$title}.";
        }
        if (str_contains($lower, 'edge') || str_contains($lower, 'concurrency') || str_contains($lower, 'race') || str_contains($lower, 'simultan')) {
            return "Dua atau lebih sesi pengguna melakukan aksi transaksi secara bersamaan pada data {$title} yang sama.";
        }
        if (str_contains($lower, 'auth') || str_contains($lower, 'security') || str_contains($lower, 'rbac') || str_contains($lower, 'akses')) {
            return "Pengguna mencoba mengakses fungsi {$title} dengan kredensial atau peran otorisasi tertentu.";
        }
        return "Pengguna terautentikasi dan memiliki hak akses valid pada modul {$title}.";
    }


    public static function deriveWhenCondition(string $scenarioTitle, string $title, int $idx): string
    {
        $lower = strtolower($scenarioTitle);
        if (str_contains($lower, 'validation') || str_contains($lower, 'error') || str_contains($lower, '422') || str_contains($lower, 'validasi')) {
            return "Terdapat parameter wajib yang kosong, format data salah, atau melanggar aturan integritas form.";
        }
        if (str_contains($lower, 'edge') || str_contains($lower, 'concurrency') || str_contains($lower, 'race') || str_contains($lower, 'simultan')) {
            return "Percobaan mutasi data simultan dikirimkan ke server secara paralel.";
        }
        if (str_contains($lower, 'auth') || str_contains($lower, 'security') || str_contains($lower, 'rbac') || str_contains($lower, 'akses')) {
            return "Sistem melakukan evaluasi terhadap token sesi dan kebijakan izin (Spatie/Shield RBAC).";
        }
        return "Pengguna mengirimkan permintaan eksekusi atau transaksi ke sistem.";
    }


    public static function deriveThenCondition(string $scenarioTitle, string $title, int $idx): string
    {
        $lower = strtolower($scenarioTitle);
        if (str_contains($lower, 'validation') || str_contains($lower, 'error') || str_contains($lower, '422') || str_contains($lower, 'validasi')) {
            return "Sistem mengembalikan respons HTTP 422 Unprocessable Content dengan detail validasi inline dan rollback transaksi.";
        }
        if (str_contains($lower, 'edge') || str_contains($lower, 'concurrency') || str_contains($lower, 'race') || str_contains($lower, 'simultan')) {
            return "Sistem menerapkan database transaction locking untuk mencegah race condition dan menjamin integritas ACID.";
        }
        return "Sistem memproses transaksi secara akurat dengan status 200 OK dan menyimpan state ke basis data PostgreSQL Strict ULID.";
    }


    /**
     * Sanitize entity class name to strict PSR-4 PascalCase standards (concise, 1-3 words max, anti-run-on).
     */
    public static function sanitizeEntityClassName(string $title, ?string $relatedTable = null): string
    {
        // 1. If relatedTable is provided and valid, prioritize table singular name
        if (!empty($relatedTable) && is_string($relatedTable)) {
            $cleanTable = preg_replace('/^(sys_|pt_|app_)/i', '', strtolower(trim($relatedTable)));
            $singularTable = Str::studly(Str::singular($cleanTable));
            if (!empty($singularTable) && strlen($singularTable) >= 3 && strlen($singularTable) <= 28) {
                return $singularTable;
            }
        }

        // 2. Strip bracketed/parenthetical explanations (e.g. "(Day Tour di Bali...)")
        $clean = preg_replace('/\(.*?\)|\[.*?\]|\{.*?\}/u', ' ', $title);
        
        // Strip everything after colon, semicolon, slash, or dash with space
        $clean = preg_replace('/[:;\/].*$/u', ' ', $clean);
        $clean = preg_replace('/\s+-\s+.*$/u', ' ', $clean);

        // Stop words (Indonesian & English connector/generic words)
        $stopWords = [
            'dan', 'atau', 'di', 'ke', 'dari', 'untuk', 'dengan', 'yang', 'pada', 
            'in', 'out', 'hotel', 'and', 'or', 'of', 'for', 'with', 'by', 'the',
            'pilihan', 'jenis', 'jumlah', 'rute', 'harga', 'sistem', 'fitur', 'modul',
            'day', 'tour', 'bali', 'layanan', 'data', 'informasi', 'kelola', 'manajemen',
            'spesifikasi', 'rekayasa', 'aplikasi', 'platform'
        ];

        // Split words
        $words = preg_split('/[\s_\-]+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $meaningfulWords = [];
        foreach ($words as $w) {
            $wClean = preg_replace('/[^a-zA-Z0-9]/', '', $w);
            if (empty($wClean)) continue;
            if (in_array(strtolower($wClean), $stopWords) && count($meaningfulWords) >= 1) {
                continue;
            }
            $meaningfulWords[] = ucfirst(strtolower($wClean));
            if (count($meaningfulWords) >= 2) {
                break;
            }
        }

        if (empty($meaningfulWords)) {
            return 'ModulLayanan';
        }

        $result = implode('', $meaningfulWords);
        $result = substr($result, 0, 28);
        if (preg_match('/^[0-9]/', $result)) {
            $result = 'Modul' . $result;
        }

        return $result;
    }


    /**
     * Dynamically sanitize long run-on file paths into clean PSR-4 paths.
     */
    public static function sanitizeFilePath(string $filePath, string $featureTitle = '', ?string $relatedTable = null): string
    {
        $filePath = trim($filePath);
        $filename = basename($filePath);
        $dirname = dirname($filePath);
        
        $isBlade = str_ends_with($filename, '.blade.php');
        $baseNameWithoutExt = preg_replace('/(\.blade)?\.php$/', '', $filename);
        
        // If the filename without suffix is excessively long (> 30 characters), clean it
        if (strlen($baseNameWithoutExt) > 30) {
            // Preserve Laravel database migration files
            if (str_contains($filePath, 'migrations') || preg_match('/^(xxxx|\d{4}_\d{2}_\d{2}_\d{6})_create_/', $filename)) {
                $cleanTable = !empty($relatedTable) ? $relatedTable : Str::snake(Str::plural(self::sanitizeEntityClassName($featureTitle ?: $baseNameWithoutExt)));
                $prefix = preg_match('/^(xxxx|\d{4}_\d{2}_\d{2}_\d{6})_/', $filename, $m) ? $m[1] : 'xxxx';
                return ($dirname === '.' || $dirname === '') ? "{$prefix}_create_{$cleanTable}_table.php" : "{$dirname}/{$prefix}_create_{$cleanTable}_table.php";
            }

            $cleanEntity = self::sanitizeEntityClassName($featureTitle ?: $baseNameWithoutExt, $relatedTable);

            if ($isBlade) {
                $filename = Str::kebab($cleanEntity) . '.blade.php';
            } elseif (str_ends_with($baseNameWithoutExt, 'Controller')) {
                $filename = "{$cleanEntity}Controller.php";
            } elseif (str_ends_with($baseNameWithoutExt, 'Service')) {
                $filename = "{$cleanEntity}Service.php";
            } elseif (str_ends_with($baseNameWithoutExt, 'ContractTest')) {
                $filename = "{$cleanEntity}ContractTest.php";
            } elseif (str_ends_with($baseNameWithoutExt, 'Test')) {
                $filename = "{$cleanEntity}Test.php";
            } elseif (str_ends_with($baseNameWithoutExt, 'Resource')) {
                $filename = "{$cleanEntity}Resource.php";
            } else {
                $filename = "{$cleanEntity}.php";
            }
            
            return ($dirname === '.' || $dirname === '') ? $filename : "{$dirname}/{$filename}";
        }

        return $filePath;
    }

}
