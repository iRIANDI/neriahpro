<?php

namespace App\Services;

use App\Services\MarkItDown\MarkItDownService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlueprintDiscoveryService
{
    public function __construct(
        protected MarkItDownService $markItDownService
    ) {}

    /**
     * Synthesize raw user brain dump text and uploaded files into a structured Blueprint schema.
     *
     * @param string $rawIdeaText
     * @param array<UploadedFile> $uploadedFiles
     * @param string $locale 'id' | 'en'
     * @return array
     * @throws \InvalidArgumentException
     */
    public function synthesize(string $rawIdeaText, array $uploadedFiles = [], string $locale = 'id', ?string $projectName = null): array
    {
        $isEn = ($locale === 'en');

        // Pre-determine or prepare project folder
        $cleanRaw = trim($rawIdeaText);
        $tempSlug = $projectName ? Str::slug($projectName) : null;
        if (!$tempSlug && !empty($cleanRaw)) {
            $firstWords = Str::words($cleanRaw, 3, '');
            $tempSlug = Str::slug($firstWords);
        }
        if (!$tempSlug || strlen($tempSlug) < 3) {
            $tempSlug = 'project-' . now()->format('Ymd-His');
        }

        $projectFolder = "projects/{$tempSlug}";
        $absProjectDir = storage_path("app/{$projectFolder}");
        $docsDir = "{$absProjectDir}/documents";
        $mdDir = "{$absProjectDir}/markdown";

        if (!is_dir($absProjectDir)) {
            @mkdir($absProjectDir, 0775, true);
        }
        if (!is_dir($mdDir)) {
            @mkdir($mdDir, 0775, true);
        }
        if (!empty($uploadedFiles) && !is_dir($docsDir)) {
            @mkdir($docsDir, 0775, true);
        }

        // 1. Process files using Microsoft MarkItDown replica service
        $convertedDocs = [];
        $fileSummaries = [];

        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                try {
                    $originalName = $file->getClientOriginalName();
                    $fileExt = strtolower($file->getClientOriginalExtension());
                    $fileBase = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBaseName = Str::slug($fileBase) ?: 'document';
                    $safeFilename = $safeBaseName . '-' . substr(md5($originalName . microtime()), 0, 6) . '.' . $fileExt;
                    $storedDocPath = "{$docsDir}/{$safeFilename}";

                    // Store original physical file in project's documents folder
                    $file->move($docsDir, $safeFilename);

                    // Convert from stored file using MarkItDown
                    $conversion = $this->markItDownService->convert($storedDocPath);
                    $fileSizeKb = file_exists($storedDocPath) ? round(filesize($storedDocPath) / 1024, 1) : 0;
                    $convertedMarkdown = $conversion['markdown'] ?? '';

                    // Save converted Markdown into project's markdown folder
                    $savedMdFilename = "{$safeBaseName}.md";
                    file_put_contents("{$mdDir}/{$savedMdFilename}", $convertedMarkdown);

                    $fileSummaries[] = [
                        'name' => $originalName,
                        'stored_file' => $safeFilename,
                        'extension' => $fileExt,
                        'size_kb' => $fileSizeKb,
                        'document_path' => "{$projectFolder}/documents/{$safeFilename}",
                        'markdown_path' => "{$projectFolder}/markdown/{$savedMdFilename}",
                        'engine' => $conversion['engine'] ?? 'markitdown',
                    ];

                    $convertedDocs[] = "### [Dokumen Lampiran: {$originalName} ({$fileSizeKb} KB)]\n" . $convertedMarkdown;
                } catch (\Throwable $e) {
                    Log::warning("MarkItDown conversion failed for file: " . $file->getClientOriginalName(), [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $allDocsMarkdown = implode("\n\n---\n\n", $convertedDocs);
        $corpusText = trim($rawIdeaText . "\n\n" . $allDocsMarkdown);

        // 2. Anti-Spam & Sanity Verification
        $this->validateAntiSpam($rawIdeaText, $corpusText, !empty($uploadedFiles), $isEn);

        // 3. Extract & Synthesize Architectural Blueprint
        $result = $this->performArchitecturalAnalysis($rawIdeaText, $allDocsMarkdown, $corpusText, $fileSummaries, $isEn);

        // If a business name was synthesized and differs from tempSlug, cleanly re-map folder
        $synthesizedName = $projectName ?: ($result['namaBisnis'] ?? null);
        if ($synthesizedName) {
            $finalSlug = Str::slug($synthesizedName);
            if ($finalSlug && $finalSlug !== $tempSlug && is_dir($absProjectDir)) {
                $finalProjectDir = storage_path("app/projects/{$finalSlug}");
                if (!is_dir($finalProjectDir)) {
                    @rename($absProjectDir, $finalProjectDir);
                    $projectFolder = "projects/{$finalSlug}";
                    $absProjectDir = $finalProjectDir;
                    $mdDir = "{$absProjectDir}/markdown";
                    $docsDir = "{$absProjectDir}/documents";
                }
            }
        }

        // Save combined synthesized corpus markdown into project's markdown folder
        if (is_dir($mdDir)) {
            file_put_contents("{$mdDir}/synthesized_corpus.md", $corpusText);

            $manifest = [
                'project_name' => $synthesizedName ?: $tempSlug,
                'project_slug' => basename($projectFolder),
                'locale' => $locale,
                'synthesized_at' => now()->toIso8601String(),
                'files_count' => count($fileSummaries),
                'files' => $fileSummaries,
            ];
            file_put_contents("{$absProjectDir}/manifest.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $result['_meta']['project_directory'] = $projectFolder;
        $result['_meta']['project_slug'] = basename($projectFolder);
        $result['_meta']['stored_files'] = $fileSummaries;

        return $result;
    }

    /**
     * Anti-Spam protection heuristics
     */
    protected function validateAntiSpam(string $rawText, string $corpus, bool $hasFiles, bool $isEn): void
    {
        $cleanRaw = trim($rawText);

        // Check 1: Minimum content requirement
        if (!$hasFiles && mb_strlen($cleanRaw) < 15) {
            throw new \InvalidArgumentException($isEn 
                ? 'Please describe your project idea with at least 15 characters, or attach a specification document.' 
                : 'Mohon ceritakan ide proyek Anda minimal 15 karakter, atau lampirkan berkas spesifikasi/dokumen pendukung.');
        }

        // Check 2: Maximum text limit to prevent memory exhaustion
        if (mb_strlen($cleanRaw) > 25000) {
            throw new \InvalidArgumentException($isEn
                ? 'Text input exceeds the maximum allowed size (25,000 characters). Please attach as a document instead.'
                : 'Uraian teks melebihi batas maksimal 25.000 karakter. Silakan lampirkan dalam bentuk dokumen.');
        }

        // Check 3: Repetitive character spam (e.g. "aaaaaaaaaaaaaa" or "11111111111111111")
        if (preg_match('/(.)\1{20,}/u', $cleanRaw)) {
            throw new \InvalidArgumentException($isEn
                ? 'Spam pattern detected (excessive repetitive characters). Please provide a valid project description.'
                : 'Pola spam terdeteksi (pengulangan karakter berlebih). Mohon masukkan deskripsi proyek yang valid.');
        }

        // Check 4: Obvious casino / illegal spam keywords
        $spamKeywords = ['slot gacor', 'judi online', 'sbobet', 'poker online', 'viagra', 'cialis', 'casino online'];
        foreach ($spamKeywords as $keyword) {
            if (stripos($cleanRaw, $keyword) !== false) {
                throw new \InvalidArgumentException($isEn
                    ? 'Prohibited content detected. Please submit valid software or business system ideas.'
                    : 'Konten terlarang terdeteksi. Mohon hanya masukkan ide perangkat lunak atau sistem bisnis yang sah.');
            }
        }
    }

    /**
     * Comprehensive Architectural Analysis & Structuring
     */
    protected function performArchitecturalAnalysis(
        string $rawText, 
        string $docsMarkdown, 
        string $corpus, 
        array $fileSummaries, 
        bool $isEn
    ): array {
        $textLower = strtolower($corpus);

        // 1. Detect Domain & Industry
        $domain = $this->detectDomain($textLower);

        // 2. Synthesize Business / Project Name
        $namaBisnis = $this->extractProjectName($corpus, $domain, $isEn);

        // 3. Synthesize Core Problem
        $masalahUtama = $this->synthesizeProblem($corpus, $domain, $isEn);

        // 4. Synthesize KPIs & Target Outcomes
        $tujuanUtama = $this->synthesizeGoal($corpus, $domain, $isEn);

        // 5. Target Audience & System Actors (RBAC)
        $targetAudiens = $this->synthesizeAudience($corpus, $domain, $isEn);
        $aktorSistem = $this->synthesizeActors($corpus, $domain, $isEn);

        // 6. Decompose MVP Core Features (Fase 1)
        $fiturWajib = $this->synthesizeMvpFeatures($corpus, $domain, $isEn);

        // 7. Decompose Phase 2 Roadmap Features
        $fiturTambahan = $this->synthesizeRoadmapFeatures($corpus, $domain, $isEn);

        // 8. User Workflow
        $alurKerja = $this->synthesizeWorkflow($corpus, $domain, $isEn);

        // 9. Third-party Integrations
        $kebutuhanIntegrasi = $this->synthesizeIntegrations($corpus, $domain, $isEn);

        // 10. Design & Aesthetic References
        $referensiDesain = $isEn
            ? "Clean Modern Monolith (Linear.app & Stripe inspired), sharp rectangular borders, dark/light mode fidelity, fast data table UX."
            : "Modern Monolith Presisi Sharp (Inspirasi Linear.app & Stripe), sudut tipis elegan non-kapsul, dukungan dark/light mode, fokus kecepatan manipulasi data.";

        // 11. Negative Scope Boundary (Out-of-Scope Anti Scope Creep)
        $outOfScope = $this->synthesizeOutOfScope($corpus, $domain, $isEn);

        // 12. Determine Timeline, User Scale & Budget
        $durasiHari = "30";
        $targetWaktu = $isEn ? "30 Working Days (Phase 1 MVP)" : "30 Hari Kerja (Fase 1 MVP)";
        $skalaPengguna = "0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)";
        $jangkauanPasar = $isEn 
            ? "Domestic Indonesia (IDR, WIB/WITA/WIT, PDP Act Compliance)"
            : "Domestik Indonesia (IDR, Zona WIB/WITA/WIT)";
        $kepatuhanKeamanan = "Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)";
        $kisaranBudget = "Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)";

        // 13. Critical Enterprise Architectural Parameters
        $targetPlatform = $isEn
            ? "Responsive Modern Web Application & PWA (Optimized for Desktop, Tablet & Mobile Browser)"
            : "Modern Web Application Responsive & PWA (Optimal untuk Browser Desktop, Tablet & Ponsel Lapangan)";

        if (preg_match('/(android|ios|playstore|app store|mobile app|native app)/i', $corpus)) {
            $targetPlatform = $isEn
                ? "Mobile-First Web App & PWA with Mobile App Readiness (Desktop Admin + Mobile Field PWA)"
                : "Mobile-First Web App & PWA Siap Pasang Layar Utama Ponsel (Dasbor Admin Desktop + PWA Lapangan)";
        }

        $migrasiData = $isEn
            ? "Clean Database Start (Form Intake & Standard CSV/Excel Master Data Import)"
            : "Database Baru Bersih (Input Mandiri & Dukungan Impor Template Excel/CSV Master Data)";

        if (preg_match('/(migrasi|data lama|excel lama|database lama|import data|impor)/i', $corpus)) {
            $migrasiData = $isEn
                ? "Legacy Data Migration Required (Data Cleansing & Batch Importing from Existing Excel/Spreadsheet)"
                : "Perlu Migrasi Data Warisan (Pembersihan & Impor Data Massal dari Spreadsheet/Database Lama)";
        }

        $preferensiHosting = $isEn
            ? "Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Automated Nightly Backups)"
            : "Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Nginx, Backup Harian Otomatis)";

        $garansiSla = $isEn
            ? "30 Days Post-Launch Bug Warranty + Full Private GitHub Repository Handover"
            : "30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repository GitHub";

        $terminPembayaran = $isEn
            ? "Standard 50/50 Milestones: 50% Kickoff & Sprint Down Payment + 50% Final Settlement Post-UAT Acceptance & Key Handover (via Midtrans Snap)"
            : "Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)";

        // Extract contact clues if user typed email or phone in text
        $email = '';
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $corpus, $eMatch)) {
            $email = $eMatch[0];
        }

        $phone = '';
        if (preg_match('/(\+?62|08|\+?1|\+?44)[\s\d-]{8,14}/', $corpus, $pMatch)) {
            $phone = trim($pMatch[0]);
        }

        $clientName = '';
        if (preg_match('/(?:nama\s*saya|saya|atas\s*nama|pic|kontak)\s*[:=]?\s*([a-zA-Z\s]{3,30})/i', $corpus, $nameMatch)) {
            $clientName = trim($nameMatch[1]);
        }

        $baseData = [
            'namaBisnis' => $namaBisnis,
            'clientName' => $clientName,
            'email' => $email,
            'phone' => $phone,
            'masalahUtama' => $masalahUtama,
            'tujuanUtama' => $tujuanUtama,
            'targetAudiens' => $targetAudiens,
            'aktorSistem' => $aktorSistem,
            'fiturWajib' => $fiturWajib,
            'fiturTambahan' => $fiturTambahan,
            'alurKerja' => $alurKerja,
            'kebutuhanIntegrasi' => $kebutuhanIntegrasi,
            'referensiDesain' => $referensiDesain,
            'kesiapanAset' => 'Sedang Disiapkan Tim Internal',
            'durasiHari' => $durasiHari,
            'targetWaktu' => $targetWaktu,
            'skalaPengguna' => $skalaPengguna,
            'jangkauanPasar' => $jangkauanPasar,
            'outOfScope' => $outOfScope,
            'kepatuhanKeamanan' => $kepatuhanKeamanan,
            'kisaranBudget' => $kisaranBudget,
            'targetPlatform' => $targetPlatform,
            'migrasiData' => $migrasiData,
            'preferensiHosting' => $preferensiHosting,
            'garansiSla' => $garansiSla,
            'terminPembayaran' => $terminPembayaran,
        ];

        $proactiveSuggestions = $this->generateProactiveSuggestions($corpus, $domain, $isEn);
        $completeness = $this->calculateCompleteness($baseData, $isEn);

        return array_merge($baseData, [
            'domain' => $domain,
            'proactive_suggestions' => $proactiveSuggestions,
            'completeness' => $completeness,
            '_meta' => [
                'synthesized_by' => 'neriah_agentic_markitdown_engine',
                'files_processed' => count($fileSummaries),
                'file_details' => $fileSummaries,
                'has_documents' => !empty($fileSummaries),
                'converted_markdown' => $docsMarkdown,
                'combined_markdown_corpus' => $corpus,
                'raw_idea_text' => $rawText,
                'domain' => $domain,
                'created_at' => now()->toIso8601String(),
            ]
        ]);
    }

    protected function detectDomain(string $text): string
    {
        $patterns = [
            'logistics' => ['logistik', 'ekspedisi', 'armada', 'truk', 'kontainer', 'pengiriman', 'gudang', 'cargo', 'resi', 'tracking', 'kurir', 'shipping', 'freight'],
            'marketplace' => ['sewa', 'rental', 'marketplace', 'jual beli', 'toko online', 'ecommerce', 'e-commerce', 'katalog', 'produk', 'keranjang', 'checkout', 'vendor'],
            'clinic' => ['klinik', 'pasien', 'dokter', 'rekam medis', 'obat', 'apotek', 'rumah sakit', 'antrean', 'poliklinik', 'kesehatan', 'diagnosis', 'medical'],
            'finance' => ['keuangan', 'invoice', 'faktur', 'tagihan', 'pembayaran', 'akuntansi', 'kasir', 'pos', 'pembukuan', 'laporan keuangan', 'escrow'],
            'hr' => ['hrd', 'karyawan', 'rekrutmen', 'pelamar', 'lowongan', 'gaji', 'payroll', 'absensi', 'cuti', 'kinerja', 'talent'],
            'education' => ['sekolah', 'kursus', 'siswa', 'guru', 'kelas', 'ujian', 'materi', 'bimbel', 'lms', 'akademik', 'pembelajaran'],
            'property' => ['properti', 'perumahan', 'apartemen', 'booking', 'kavling', 'agen', 'penyewa', 'kontrakan', 'real estate'],
        ];

        foreach ($patterns as $domain => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    return $domain;
                }
            }
        }

        return 'custom_portal';
    }

    protected function extractProjectName(string $corpus, string $domain, bool $isEn): string
    {
        // Try extracting explicit title or project name
        if (preg_match('/(?:nama\s*(?:proyek|bisnis|aplikasi|platform)|project\s*name)\s*[:=]\s*([^\n\r,.]+)/i', $corpus, $m)) {
            $name = trim($m[1]);
            if (mb_strlen($name) >= 3 && mb_strlen($name) <= 60) {
                return $name;
            }
        }

        // Try extracting first heading from markitdown
        if (preg_match('/^#\s+([^\n\r]+)/m', $corpus, $hMatch)) {
            $candidate = trim($hMatch[1]);
            if (mb_strlen($candidate) >= 4 && mb_strlen($candidate) <= 60 && !stripos($candidate, 'error') && !stripos($candidate, 'scanned')) {
                return $candidate;
            }
        }

        // Domain-tailored defaults
        $defaultsId = [
            'logistics' => 'Sistem Manajemen Logistik & Pelacakan Armada Terintegrasi',
            'marketplace' => 'Platform Marketplace & Reservasi Layanan Terpusat',
            'clinic' => 'Sistem Informasi Manajemen Klinik & Rekam Medis Elektronik',
            'finance' => 'Sistem Penagihan Terpadu & Otomatisasi Faktur Komersial',
            'hr' => 'Platform Manajemen Talenta & Rekrutmen Terpadu',
            'education' => 'Portal Pembelajaran & Administrasi Akademik Digital',
            'property' => 'Platform Manajemen Aset Properti & Portal Penyewa',
            'custom_portal' => 'Platform Operasional & Portal Bisnis Terintegrasi',
        ];

        $defaultsEn = [
            'logistics' => 'Integrated Fleet Tracking & Logistics Management Engine',
            'marketplace' => 'Centralized Service Marketplace & Booking Platform',
            'clinic' => 'Clinical Information System & Electronic Medical Records',
            'finance' => 'Unified Commercial Invoicing & Financial Operations Hub',
            'hr' => 'Enterprise Talent Acquisition & People Operations Platform',
            'education' => 'Digital Academic Administration & Learning Portal',
            'property' => 'Real Estate Asset Management & Tenant Operation Portal',
            'custom_portal' => 'Centralized Enterprise Business Management Platform',
        ];

        return $isEn ? ($defaultsEn[$domain] ?? $defaultsEn['custom_portal']) : ($defaultsId[$domain] ?? $defaultsId['custom_portal']);
    }

    protected function synthesizeProblem(string $corpus, string $domain, bool $isEn): string
    {
        $firstParagraph = trim(preg_split('/\n\s*\n/', $corpus)[0] ?? '');
        if (mb_strlen($firstParagraph) > 40 && mb_strlen($firstParagraph) < 400) {
            return $firstParagraph;
        }

        $problemsId = [
            'logistics' => 'Pencatatan manifes, status pengiriman armada, dan pelacakan surat jalan saat ini masih manual via spreadsheet dan pesan instan, menyebabkan lambatnya rekonsiliasi dan risiko kehilangan bukti serah terima.',
            'marketplace' => 'Koordinasi transaksi antara penyedia jasa/vendor dan pelanggan masih terfragmentasi tanpa adanya verifikasi ketersediaan armada/stok real-time, kalkulasi tarif transparan, serta sistem penjamin transaksi yang aman.',
            'clinic' => 'Data rekam medis pasien dan riwayat pemeriksaan masih terpisah dalam berkas kertas atau sistem offline, memperlambat proses pendaftaran, rujukan dokter, dan rekonsiliasi stok obat.',
            'finance' => 'Penerbitan faktur dan penagihan piutang pelanggan sering terlambat karena proses validasi manual bertingkat, menyulitkan monitoring arus kas dan penutupan buku bulanan.',
            'hr' => 'Proses seleksi berkas pelamar dan evaluasi kinerja karyawan terhambat karena data tercecer di email, formulir terpisah, dan tidak adanya alur persetujuan (approval) berjenjang.',
            'education' => 'Distribusi materi, pelacakan absensi, dan administrasi nilai siswa sulit dikontrol terpusat oleh manajemen dan guru secara efisien.',
            'property' => 'Pencatatan unit sewa, masa jatuh tempo kontrak, dan pelaporan keluhan pemeliharaan fasilitas sering terlewat karena tidak adanya portal digital mandiri bagi penyewa.',
            'custom_portal' => 'Operasional bisnis masih mengandalkan rekap manual yang rentan human-error, data tercecer di berbagai platform, dan manajemen kesulitan mendapatkan visibilitas analitik secara real-time.',
        ];

        $problemsEn = [
            'logistics' => 'Manifest logging, shipment status tracking, and delivery receipts are managed manually through disconnected spreadsheets, leading to delayed reconciliation and lost records.',
            'marketplace' => 'Service bookings and vendor interactions are highly fragmented without real-time inventory validation, transparent rate calculation, or centralized payment escrow.',
            'clinic' => 'Patient medical histories and registration queues remain tied to physical folders or siloed offline databases, hindering doctor handoffs and medicine stock tracking.',
            'finance' => 'Commercial billing and account receivables reconciliation suffer from slow multi-tier manual approvals, impacting monthly cash-flow reporting.',
            'hr' => 'Candidate screening and personnel evaluations are hindered by scattered resumes in email inboxes without structured pipeline tracking.',
            'education' => 'Course material distribution, student attendance tracking, and grading lack a centralized platform for faculty and administrators.',
            'property' => 'Unit lease monitoring, contract expiration alerts, and maintenance request dispatches are frequently dropped due to lack of a tenant self-service portal.',
            'custom_portal' => 'Core business operations rely on error-prone manual spreadsheets, resulting in data silos and lack of real-time executive visibility.',
        ];

        return $isEn ? ($problemsEn[$domain] ?? $problemsEn['custom_portal']) : ($problemsId[$domain] ?? $problemsId['custom_portal']);
    }

    protected function synthesizeGoal(string $corpus, string $domain, bool $isEn): string
    {
        $goalsId = [
            'logistics' => '1. Otomatisasi pencatatan resi dan pelacakan armada hingga 100% digital.\n2. Waktu rekonsiliasi laporan pengiriman dipangkas dari 3 hari menjadi real-time.\n3. Akses visibilitas langsung bagi pelanggan untuk memeriksa posisi kiriman.',
            'marketplace' => '1. Mengintegrasikan proses booking, verifikasi armada/jasa, dan pembayaran dalam 1 portal terpusat.\n2. Mengurangi waktu tunggu konfirmasi pesanan hingga di bawah 15 menit.\n3. Menjamin transparansi transaksi dengan faktur digital dan histori transaksi lengkap.',
            'clinic' => '1. Digitalisasi 100% rekam medis dan data pemeriksaan pasien sesuai standar kepatuhan medis.\n2. Mengurangi waktu antrean pendaftaran hingga 70% melalui booking online.\n3. Otomatisasi mutasi stok obat dan laporan keuangan klinik harian.',
            'finance' => '1. Mengotomatiskan siklus penerbitan invoice dan pengingat jatuh tempo via WhatsApp & Email.\n2. Mempercepat rekonsiliasi piutang hingga 80% dengan integrasi payment gateway.\n3. Menyediakan dasbor laporan arus kas real-time yang siap diaudit.',
            'hr' => '1. Sentralisasi pendaftaran dan kurasi profil kandidat ke dalam basis data terstruktur.\n2. Mempercepat tahapan screening dan penugasan interview hingga 50%.\n3. Otomatisasi rekap evaluasi dan riwayat kepegawaian dalam format PDF resmi.',
            'education' => '1. Sentralisasi materi ajar, bank soal, dan rekaman evaluasi dalam satu portal terproteksi.\n2. Efisiensi absensi dan pelaporan akademik berkala kepada wali murid.',
            'property' => '1. Dasbor manajemen unit sewa dengan pengingat otomatis perpanjangan kontrak.\n2. Tiketing perbaikan fasilitas yang transparan dan dapat dipantau langsung oleh penyewa.',
            'custom_portal' => '1. Menghilangkan proses rekapitulasi data manual dan duplikasi input.\n2. Menjamin integritas data transaksi dengan sistem verifikasi berjenjang.\n3. Menghasilkan laporan analitik eksekutif otomatis format PDF dan Excel setiap hari.',
        ];

        $goalsEn = [
            'logistics' => '1. 100% digitization of manifests and automated dispatch logging.\n2. Reduction of delivery audit time from 3 days to real-time.\n3. Direct live tracking visibility for enterprise clients.',
            'marketplace' => '1. Streamlined service booking, vendor verification, and payment gateway escrow in one hub.\n2. Lower confirmation turnaround to under 15 minutes.\n3. Complete transaction auditing with automated digital receipts.',
            'clinic' => '1. Complete digitization of medical records and patient histories compliant with health regulations.\n2. 70% reduction in patient check-in waiting times via self-service intake.\n3. Automated pharmacy inventory tracking and daily revenue analytics.',
            'finance' => '1. Automated invoice generation and automated payment reminders via WhatsApp & Email.\n2. 80% faster accounts receivable reconciliation via instant payment webhooks.\n3. Real-time cash flow executive dashboard ready for financial audits.',
            'hr' => '1. Centralization of candidate submissions into a searchable talent repository.\n2. 50% faster recruitment pipeline turnaround.\n3. Automated personnel evaluation reporting in exportable PDF formats.',
            'education' => '1. Unified repository for learning resources, syllabus tracking, and student assessments.\n2. Automated attendance and academic progress reporting.',
            'property' => '1. Unified property lease tracking with automated renewal notification workflows.\n2. Transparent maintenance ticket logging and resolution tracking for tenants.',
            'custom_portal' => '1. Eliminate error-prone manual spreadsheets and redundant data entry.\n2. Guarantee transactional integrity with multi-tier validation workflows.\n3. Automated generation of executive analytics reports in PDF and Excel formats daily.',
        ];

        return $isEn ? ($goalsEn[$domain] ?? $goalsEn['custom_portal']) : ($goalsId[$domain] ?? $goalsId['custom_portal']);
    }

    protected function synthesizeAudience(string $corpus, string $domain, bool $isEn): string
    {
        $audiencesId = [
            'logistics' => 'Klien korporat (B2B), manajer pengiriman, staf operasional logistik, dan pengemudi/vendor armada.',
            'marketplace' => 'Pelanggan pencari layanan/persewaan, vendor pemilik armada/jasa, dan admin verifikator.',
            'clinic' => 'Pasien umum, dokter spesialis/umum, staf resepsionis, perawat, dan apoteker.',
            'finance' => 'Klien pembayar invoice, staf penagihan (finance), manajer akuntansi, dan direksi perusahaan.',
            'hr' => 'Calon pelamar kerja, manajer departemen perekrut, dan tim HR internal.',
            'education' => 'Siswa, mahasiswa, pengajar/instruktur, staf tata usaha, dan orang tua wali.',
            'property' => 'Penyewa unit hunian/komersial, calon prospek, dan tim manajemen pengelola gedung.',
            'custom_portal' => 'Pengguna internal organisasi, staf operasional harian, pimpinan manajemen, dan mitra eksternal.',
        ];

        $audiencesEn = [
            'logistics' => 'Corporate B2B clients, freight managers, dispatch operators, and fleet drivers/contractors.',
            'marketplace' => 'Service renters, vehicle/equipment owners, and platform dispatch administrators.',
            'clinic' => 'Outpatients, physicians, reception staff, nurses, and pharmacy dispensary staff.',
            'finance' => 'Billing clients, accounts receivable officers, financial controllers, and executive directors.',
            'hr' => 'Job applicants, departmental hiring managers, and internal HR business partners.',
            'education' => 'Students, educators, academic administration staff, and guardians.',
            'property' => 'Tenants, prospective lessees, property agents, and building management operators.',
            'custom_portal' => 'Internal enterprise staff, operational operators, executive managers, and verified external partners.',
        ];

        return $isEn ? ($audiencesEn[$domain] ?? $audiencesEn['custom_portal']) : ($audiencesId[$domain] ?? $audiencesId['custom_portal']);
    }

    protected function synthesizeActors(string $corpus, string $domain, bool $isEn): string
    {
        $actorsId = [
            'logistics' => "1. Superadmin: Mengendalikan master data armada, tarif wilayah, dan hak akses staf.\n2. Staff Dispatcher: Menginput manifes, menugaskan pengemudi, dan memverifikasi status jalan.\n3. Driver / Vendor: Memperbarui titik lokasi, unggah foto bukti serah terima (POD).\n4. Klien / Customer: Memantau status pengiriman live dan mengunduh invoice/e-POD.",
            'marketplace' => "1. Superadmin: Validasi identitas vendor, audit transaksi pembayaran, dan manajemen sistem.\n2. Vendor / Mitra: Mengelola katalog ketersediaan, menerima pesanan sewa, dan konfirmasi unit.\n3. Customer / Klien: Mencari unit/layanan, reservasi jadwal, dan melakukan pembayaran aman.\n4. Finance Officer: Rekonsiliasi pembayaran bertahap dan pencairan dana ke vendor mitra.",
            'clinic' => "1. Superadmin: Manajemen dokter, tarif tindakan medis, dan konfigurasi sistem.\n2. Resepsionis / Front Desk: Pendaftaran pasien baru, antrean poli, dan cetak kartu rekam medis.\n3. Dokter: Menginput rekam medis elektronik (RME), diagnosis, dan resep digital.\n4. Apoteker: Validasi resep, penyerahan obat, dan rekonsiliasi mutasi stok obat.",
            'finance' => "1. Superadmin: Konfigurasi akun bank, payment gateway, dan audit log keuangan.\n2. Finance Operator: Penerbitan tagihan, kustomisasi termin pembayaran, dan input bukti bayar.\n3. Client Payer: Melihat rincian tagihan, melakukan pembayaran via VA/QRIS, dan unduh faktur.\n4. Accounting Auditor: Unduh laporan rekonsiliasi harian/bulanan format Excel dan PDF.",
            'hr' => "1. Superadmin: Pengaturan struktur organisasi dan otorisasi modul.\n2. HR Recruiter: Publikasi lowongan, seleksi berkas pelamar, dan penjadwalan interview.\n3. Hiring Manager: Memberikan feedback evaluasi kandidat dan approval rekrutmen.\n4. Pelamar: Mengunggah CV/dokumen, mengisi formulir profil, dan memantau status seleksi.",
            'education' => "1. Superadmin: Manajemen tahun ajaran, kurikulum, dan akun pengguna.\n2. Pengajar: Mengunggah materi ajar, membagikan tugas, dan menginput nilai siswa.\n3. Siswa / Peserta: Mengakses modul materi, mengumpulkan tugas, dan melihat kartu hasil studi.",
            'property' => "1. Property Manager: Kelola data unit, jadwal sewa, dan master biaya pemeliharaan.\n2. Penyewa (Tenant): Melihat jadwal tagihan sewa, bayar tagihan, dan ajukan tiket perbaikan.\n3. Teknisi Pemeliharaan: Menerima tiket komplain dan memperbarui status penanganan fisik.",
            'custom_portal' => "1. Superadmin: Hak akses penuh ke seluruh pengaturan sistem dan audit keamanan.\n2. Operator / Staff: Penginputan dan verifikasi data transaksi harian.\n3. Approver / Supervisor: Otorisasi persetujuan data bertingkat sebelum eksekusi.\n4. Klien / Pengguna Umum: Pengisian formulir terarah dan pelacakan status transaksi mandiri.",
        ];

        $actorsEn = [
            'logistics' => "1. Superadmin: Full control over master fleet catalog, regional rate matrices, and role authorization.\n2. Dispatch Operator: Registers manifests, assigns drivers, and verifies delivery routes.\n3. Driver / Fleet Contractor: Updates transit waypoints, uploads proof of delivery (POD) photo.\n4. Corporate Client: Real-time shipment tracking and automated e-POD / invoice downloads.",
            'marketplace' => "1. Superadmin: Vendor KYC verification, transaction escrow audits, and platform settings.\n2. Vendor Partner: Manages equipment availability, confirms booking requests, and updates terms.\n3. Customer: Searches availability, reserves dates, and completes payment checkout.\n4. Financial Officer: Reconciles escrow releases and vendor settlements.",
            'clinic' => "1. Superadmin: Clinic branches, doctor schedules, and security policy management.\n2. Receptionist: Patient intake, appointment queue management, and card printing.\n3. Physician: Diagnostic inputs, Electronic Medical Records (EMR), and digital prescriptions.\n4. Pharmacist: Prescription validation, medication dispensing, and inventory tracking.",
            'finance' => "1. Superadmin: Bank accounts, payment gateway webhooks, and security audit log.\n2. Finance Operator: Invoice drafting, installment scheduling, and receipt verifications.\n3. Client Payer: Reviews itemized billing, executes payments via VA/QRIS, downloads receipts.\n4. Accounting Auditor: Generates monthly reconciliation reports in Excel and PDF.",
            'hr' => "1. Superadmin: Company roles and access policy control.\n2. HR Recruiter: Job vacancy posting, candidate resume screening, and interview scheduling.\n3. Hiring Manager: Reviews candidates, submits evaluation notes, approves hires.\n4. Candidate: Profile submission, CV upload, and application status tracking.",
            'education' => "1. Superadmin: Academic calendar, user credentials, and curriculum control.\n2. Instructor: Uploads study materials, creates assignments, records student grades.\n3. Student: Reviews course materials, submits homework, checks academic report cards.",
            'property' => "1. Property Manager: Unit catalog, lease expiration tracking, maintenance supervision.\n2. Tenant: Checks billing schedule, pays rent online, submits maintenance tickets.\n3. Maintenance Technician: Receives repair dispatches and updates resolution logs.",
            'custom_portal' => "1. Superadmin: Full master administration, security audit trails, and configuration.\n2. Operational Staff: Day-to-day transaction input and verification.\n3. Supervisor / Approver: Multi-tier approval workflow authorization.\n4. External Client: Self-service submission and real-time status tracker.",
        ];

        return $isEn ? ($actorsEn[$domain] ?? $actorsEn['custom_portal']) : ($actorsId[$domain] ?? $actorsId['custom_portal']);
    }

    protected function synthesizeMvpFeatures(string $corpus, string $domain, bool $isEn): string
    {
        $featuresId = [
            'logistics' => "1. Autentikasi Pengguna & RBAC: Manajemen akses aman untuk Superadmin, Dispatcher, Vendor, dan Klien dengan PostgreSQL ULID.\n2. Manajemen Master Data: Pengelolaan data armada truk, jenis kontainer, kapasitas muatan, dan tarif wilayah.\n3. Modul Pencatatan Manifes & Resi: Penerbitan nomor surat jalan otomatis dengan QR Code verifikasi.\n4. Pelacakan Status Pengiriman Real-Time: Update status bertahap (Diterima -> Muat -> Dalam Perjalanan -> Terkirim) lengkap dengan bukti foto serah terima (e-POD).\n5. Dasbor Admin Filament v5: Tabel filter data pengiriman dengan pencarian instan, status badge, dan metrik operasional harian.\n6. Ekspor Laporan & Surat Jalan: Cetak otomatis Surat Jalan, Berita Acara, dan rekapitulasi data format PDF dan Excel.",
            'marketplace' => "1. Autentikasi & Verifikasi Akun: Login aman dengan pemisahan peran Pelanggan dan Vendor Mitra.\n2. Manajemen Katalog & Ketersediaan: Input detail layanan/unit sewa dengan galeri foto via Curator Picker dan tarif harian/bulanan.\n3. Alur Reservasi & Booking: Formulir pemilihan tanggal sewa, kalkulasi harga otomatis, dan konfirmasi ketersediaan.\n4. Integrasi Pembayaran Midtrans: Dukungan pembayaran multi-channel (Virtual Account, QRIS, Kartu Kredit) dengan webhook otomatis.\n5. Pusat Kendali Admin (Filament PHP): Audit pesanan masuk, verifikasi berkas legal vendor, dan pemantauan transaksi.\n6. Faktur Digital & Notifikasi: Penerbitan invoice resmi otomatis dan notifikasi status pesanan.",
            'clinic' => "1. Modul Autentikasi & Hak Akses Medis: Akses terisolasi untuk Resepsionis, Dokter, dan Apoteker.\n2. Pendaftaran Pasien & Antrean: Input data pasien dengan nomor rekam medis unik dan antrean digital poli.\n3. Rekam Medis Elektronik (RME): Form pencatatan keluhan, anamnesis, diagnosis standar ICD-10, dan resep obat digital.\n4. Manajemen Stok Apotek: Pencatatan otomatis pengurangan stok saat obat diresepkan serta peringatan stok menipis.\n5. Dasbor Manajemen & Kasir: Perhitungan total billing perawatan obat dan cetak kuitansi pembayaran.\n6. Laporan Medis & Keuangan: Ekspor data kunjungan pasien dan rekapitulasi penjualan farmasi ke PDF/Excel.",
            'finance' => "1. Autentikasi Finansial & Audit Trail: Hak akses ketat untuk Operator Keuangan dan Auditor dengan logging aktivitas.\n2. Pembuat Faktur Komersial: Pembuatan invoice dinamis dengan perhitungan PPN, diskon, dan skema termin bertahap.\n3. Gateway Pembayaran Terotomatisasi: Integrasi Midtrans Virtual Account dan QRIS dengan rekonsiliasi seketika.\n4. Portal Pembayaran Klien: Halaman khusus bagi klien untuk melihat rincian faktur dan melakukan pembayaran langsung.\n5. Dasbor Piutang & Aging Schedule: Pemantauan tagihan belum terbayar, jatuh tempo, dan metrik kas masuk.\n6. Ekspor Rekonsiliasi Akuntansi: Ekspor data jurnal transaksi siap import ke software akuntansi dalam format CSV dan PDF.",
            'custom_portal' => "1. Autentikasi Modern & RBAC: Pengelolaan peran pengguna dengan ULID primary keys untuk skalabilitas jutaan data.\n2. Formulir Intake & Validasi Data: Input data terstruktur dengan validasi ketat dan proteksi Anti-Spam berjenjang.\n3. Dasbor Administrasi Filament v5: Pusat manajemen data dengan filter canggih, metrik analitik, dan tabel dinamis kilat.\n4. Alur Kerja Persetujuan (Workflow): Mekanisme review data bertingkat dengan pencatatan riwayat audit (audit trail).\n5. Sistem Notifikasi Terpadu: Notifikasi status via sistem internal dan template email resmi.\n6. Modul Laporan & Ekspor: Ekspor rekonsiliasi data komprehensif ke format PDF siap cetak dan spreadsheet Excel.",
        ];

        $featuresEn = [
            'logistics' => "1. User Authentication & RBAC: Strict access authorization for Superadmin, Dispatcher, Vendor, and Client using PostgreSQL ULID.\n2. Master Fleet & Route Catalog: Fleet specifications, container capacities, and regional rate tables.\n3. Automated Manifest & Waybill Generation: Instant assignment with unique QR code verification.\n4. Live Shipment Progression: Multi-stage status updates (Accepted -> In-Transit -> Delivered) with mobile photo e-POD upload.\n5. Filament v5 Command Center: Real-time dispatch filter table, operational KPI metric cards, and bulk status triggers.\n6. Document Generation & Export: Instant printable PDF waybills and XLSX dispatch reconciliations.",
            'marketplace' => "1. Verified User Profiles & RBAC: Dual-role onboarding for Customers and Verified Vendors.\n2. Service & Asset Availability Catalog: Media management via Curator Picker and dynamic tier pricing.\n3. Booking & Reservation Pipeline: Interactive calendar picker, pricing calculator, and confirmation lock.\n4. Midtrans Payment Engine: Multi-channel checkout (Virtual Account, QRIS, Cards) with automated webhook settlement.\n5. Command Center (Filament PHP): Booking inspection, vendor credential review, and revenue tracking.\n6. Digital Invoicing & Receipts: Automated PDF receipt generation and instant status notifications.",
            'clinic' => "1. Role-Segregated Clinical Auth: Isolated portals for Receptionists, Physicians, and Pharmacists.\n2. Patient Intake & Queue Management: Patient registration with automated medical record numbers.\n3. Electronic Medical Records (EMR): Diagnostic documentation, anamnesis logs, and digital prescription issuance.\n4. Pharmacy Dispensary & Inventory Sync: Automated real-time deduction upon prescription dispensing with low-stock alerts.\n5. Cashier & Billing Hub: Aggregated billing calculation and instant invoice receipt generation.\n6. Clinical & Revenue Analytics: Exportable patient visit metrics and pharmacy ledger in PDF and Excel formats.",
            'finance' => "1. Financial-Grade RBAC & Audit Trails: Timestamped activity logging for billing officers and auditors.\n2. Commercial Invoice Generator: Dynamic billing engine with multi-currency, tax calculation, and milestone stages.\n3. Automated Payment Gateway: Midtrans Virtual Account & QRIS webhooks with instant ledger reconciliation.\n4. Client Payment Hub: Direct client portal for invoice review and instant payment settlement.\n5. Accounts Receivable & Aging Dashboard: Overdue tracking, aging buckets, and cash inflow analytics.\n6. Financial Export Suite: Downloadable transaction audit journals in Excel and printable PDF formats.",
            'custom_portal' => "1. Modern Authentication & RBAC: Role-based control with distributed PostgreSQL ULID identifiers.\n2. Structured Data Intake & Validation: Robust form validation with multi-layer anti-spam protection.\n3. Filament v5 Enterprise Dashboard: Instant filterable data tables, metric widgets, and bulk processing.\n4. Multi-Tier Approval Workflow: Step-by-step verification pipeline with complete audit trails.\n5. Unified Notification Engine: Email and in-app status updates for critical milestones.\n6. Reporting & Export Suite: Instant export of filtered data into standardized PDF reports and Excel workbooks.",
        ];

        return $isEn ? ($featuresEn[$domain] ?? $featuresEn['custom_portal']) : ($featuresId[$domain] ?? $featuresId['custom_portal']);
    }

    protected function synthesizeRoadmapFeatures(string $corpus, string $domain, bool $isEn): string
    {
        $roadmapId = [
            'logistics' => "1. Integrasi GPS IoT Telemetri: Pembacaan sensor GPS armada real-time dan pemantauan suhu muatan kontainer.\n2. Notifikasi WhatsApp Gateway: Kirim nomor resi dan link tracking live otomatis ke nomor WhatsApp penerima.\n3. Algoritma Optimasi Rute (Route Dispatcher AI): Rekomendasi rute terpendek untuk efisiensi bahan bakar armada.",
            'marketplace' => "1. Integrasi Escrow Multi-Vendor Otomatis: Pencairan dana otomatis ke rekening bank vendor setelah pesanan selesai.\n2. Notifikasi WhatsApp Bisnis: Notifikasi pengingat pembayaran dan konfirmasi penjemputan unit secara instan.\n3. Aplikasi Mobile PWA Teroptimasi: Akses offline dan notifikasi push untuk mitra di lapangan.",
            'clinic' => "1. Integrasi SatuSehat Kemenkes: Penyelarasan data riwayat medis pasien dengan platform SatuSehat nasional.\n2. Notifikasi Pengingat Kontrol WhatsApp: Pengingat otomatis jadwal kontrol ulang pasien dan resep rutin.\n3. Portal Pasien Mandiri (PWA): Pasien dapat melihat riwayat hasil lab dan mengunduh resep digital sendiri.",
            'finance' => "1. Auto-Debit Recurring Billing: Tagihan langganan otomatis via kartu kredit dan e-wallet.\n2. Rekonsiliasi Bank Otomatis (Open Finance API): Penarikan mutasi rekening koran BCA/Mandiri secara otomatis.\n3. Analisis Prediksi Arus Kas (AI Cashflow Forecast): Proyeksi potensi piutang macet berdasarkan riwayat pembayaran klien.",
            'custom_portal' => "1. Integrasi WhatsApp Cloud API: Otomatisasi pengiriman notifikasi dan alert transaksi penting ke ponsel klien.\n2. Aplikasi Mobile PWA Offline-Sync: Akses aplikasi cepat untuk operator lapangan dengan sinkronisasi otomatis saat online.\n3. AI Agentic Decision Engine: Analisis prediktif dan asisten cerdas untuk merangkum anomali data operasional.",
        ];

        $roadmapEn = [
            'logistics' => "1. IoT GPS Telemetry Integration: Direct sensor integration for live vehicle coordinate and container temperature logging.\n2. Automated WhatsApp Gateway: Instant notification dispatch with live tracking links to recipient phone numbers.\n3. AI Route Optimization Engine: Automated best-route dispatch recommendations to minimize fuel consumption.",
            'marketplace' => "1. Automated Multi-Vendor Escrow Payouts: Automated bank disbursements to vendor accounts upon verified completion.\n2. Official WhatsApp Notifications: Automated reminders for pending payments and booking pickup confirmations.\n3. Mobile PWA Field Companion: Offline-first access with background sync for mobile field coordinators.",
            'clinic' => "1. SatuSehat Ministry of Health Compliance: Bi-directional EMR synchronization with national health services.\n2. Automated WhatsApp Check-up Reminders: Proactive appointment reminders for recurring patient checkups.\n3. Patient Self-Service Portal (PWA): Direct access for patients to view test results and digital prescription history.",
            'finance' => "1. Automated Recurring Billing: Subscription auto-debit via credit cards and digital wallets.\n2. Open Finance Bank Feed Sync: Automated daily bank account statement ingestion and reconciliation.\n3. AI Predictive Cashflow Analytics: Automated risk scoring and payment delay probability indicators.",
            'custom_portal' => "1. WhatsApp Cloud API Webhooks: Automated delivery of high-priority operational alerts and status reports.\n2. PWA Offline-First Engine: Progressive web application with background synchronization for field workers.\n3. AI Agentic Decision Intelligence: Automated anomaly detection and executive summary generation.",
        ];

        return $isEn ? ($roadmapEn[$domain] ?? $roadmapEn['custom_portal']) : ($roadmapId[$domain] ?? $roadmapId['custom_portal']);
    }

    protected function synthesizeWorkflow(string $corpus, string $domain, bool $isEn): string
    {
        $workflowId = [
            'logistics' => "1. Klien / Dispatcher membuat pesanan pengiriman baru di portal.\n2. Sistem menerbitkan Surat Jalan unik ber-QR Code dan menugaskan armada yang tersedia.\n3. Pengemudi melakukan check-in keberangkatan dan status diperbarui menjadi 'Dalam Perjalanan'.\n4. Barang tiba di tujuan, penerima menandatangani secara digital atau pengemudi mengunggah foto e-POD.\n5. Sistem secara otomatis mencatat pengiriman selesai dan mengirimkan rekapitulasi faktur ke klien.",
            'marketplace' => "1. Pengguna mencari layanan atau unit sewa yang tersedia sesuai tanggal.\n2. Pengguna mengisi detail durasi dan sistem menghitung total biaya secara transparan.\n3. Pengguna melakukan pembayaran melalui Virtual Account atau QRIS Midtrans.\n4. Pembayaran terverifikasi otomatis via webhook, vendor menerima notifikasi pesanan.\n5. Vendor menyerahkan unit/layanan dan menyelesaikan transaksi di dasbor.",
            'clinic' => "1. Pasien mendaftar online atau melalui staf resepsionis di lokasi klinik.\n2. Pasien dipanggil menuju ruang dokter sesuai nomor antrean digital.\n3. Dokter memeriksa pasien dan menginput diagnosis serta resep langsung di Rekam Medis Elektronik.\n4. Apotek menerima resep secara real-time dan menyiapkan obat.\n5. Pasien melakukan pembayaran di kasir dan menerima obat beserta kuitansi resmi.",
            'finance' => "1. Tim finance menyusun draf faktur dan menetapkan tanggal jatuh tempo.\n2. Invoice dikirim otomatis ke email dan WhatsApp klien dengan link pembayaran unik.\n3. Klien membuka portal faktur dan membayar melalui channel pembayaran pilihan.\n4. Sistem payment gateway mengirimkan callback dan status faktur berubah menjadi 'Lunas' seketika.\n5. Sistem mencatat jurnal pelunasan dan menghasilkan kuitansi pembayaran resmi.",
            'custom_portal' => "1. Pengguna mengakses portal dan mengisi data transaksi pada formulir terstruktur.\n2. Sistem memvalidasi data dan menyimpan rekaman dengan identifier unik terenkripsi.\n3. Staf / Operator menerima notifikasi dan melakukan verifikasi kelengkapan data di dasbor Filament.\n4. Supervisor menyetujui transaksi melalui alur persetujuan berjenjang.\n5. Sistem menghasilkan dokumen bukti resmi (PDF) dan memperbarui analitik operasional secara real-time.",
        ];

        $workflowEn = [
            'logistics' => "1. Client / Dispatcher registers a new shipment order on the portal.\n2. System issues a unique QR-coded waybill and assigns available fleet assets.\n3. Driver confirms departure, transitioning status to 'In Transit'.\n4. Consignment arrives at destination, recipient signs electronically, and driver uploads e-POD.\n5. System automatically logs completion and sends reconciliation invoice to the client.",
            'marketplace' => "1. User selects desired service or equipment availability for specified dates.\n2. User provides required details and system transparently calculates total pricing.\n3. User executes payment via Midtrans Virtual Account or QRIS.\n4. Settlement verifies via automated webhook, alerting the vendor partner instantly.\n5. Vendor delivers unit/service and confirms completion in the dashboard.",
            'clinic' => "1. Patient checks in online or via front-desk registration.\n2. Patient is queued and routed to physician examination room.\n3. Physician records examination notes, diagnosis, and digital prescriptions in EMR.\n4. Dispensary receives prescription instantly and prepares medication packages.\n5. Patient completes billing settlement at checkout and receives dispensed medicine.",
            'finance' => "1. Finance team drafts invoice and configures due dates and milestones.\n2. Invoice dispatches automatically to client via email and WhatsApp with a direct payment link.\n3. Client reviews itemized breakdown and completes checkout.\n4. Webhook callback verifies payment, instantly marking invoice as 'Paid'.\n5. System reconciles accounts receivable and issues official payment receipt.",
            'custom_portal' => "1. User submits transaction data through the structured form.\n2. System validates payload and persists record with encrypted ULID identifiers.\n3. Operational staff receives alert and inspects data in the Filament control panel.\n4. Supervisor reviews and authorizes record through multi-tier workflow.\n5. System generates official PDF summary and updates real-time analytics dashboards.",
        ];

        return $isEn ? ($workflowEn[$domain] ?? $workflowEn['custom_portal']) : ($workflowId[$domain] ?? $workflowId['custom_portal']);
    }

    protected function synthesizeIntegrations(string $corpus, string $domain, bool $isEn): string
    {
        $integrationsId = [
            'logistics' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mapbox / OpenStreetMap Routing API, S3 / Cloudflare R2 Object Storage.',
            'marketplace' => 'Midtrans Snap Payment Gateway, WhatsApp Business Cloud API, Google Maps Autocomplete, Cloudflare R2.',
            'clinic' => 'SatuSehat Kemenkes API, Midtrans QRIS/VA, WhatsApp Gateway Pengingat Pasien, Cloud Backup Storage.',
            'finance' => 'Midtrans Core API (VA & QRIS), WhatsApp Notification Gateway, Mailgun Transactional Email, Jurnal/Xero Export API.',
            'custom_portal' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mailgun SMTP, Cloudflare Object Storage R2.',
        ];

        $integrationsEn = [
            'logistics' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mapbox / OpenStreetMap Routing API, Cloudflare R2 Storage.',
            'marketplace' => 'Midtrans Snap Payment Gateway, WhatsApp Business API, Google Places Autocomplete, Cloudflare R2.',
            'clinic' => 'SatuSehat MOH API, Midtrans QRIS/VA, Patient WhatsApp Dispatcher, Encrypted Cloud Storage.',
            'finance' => 'Midtrans Core API (VA & QRIS), WhatsApp Notification Gateway, Mailgun SMTP, Accounting Export Webhooks.',
            'custom_portal' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mailgun Transactional SMTP, Cloudflare R2.',
        ];

        return $isEn ? ($integrationsEn[$domain] ?? $integrationsEn['custom_portal']) : ($integrationsId[$domain] ?? $integrationsId['custom_portal']);
    }

    protected function synthesizeOutOfScope(string $corpus, string $domain, bool $isEn): string
    {
        $outOfScopeId = [
            'logistics' => "1. Tidak membuat aplikasi native iOS & Android khusus app store pada Fase 1 MVP (fokus pada Progressive Web App / Web Responsive yang ringan dan dapat diakses dari browser smartphone).\n2. Tidak mencakup integrasi perangkat keras sensor telemetri pihak ketiga (IoT) yang belum terstandarisasi di tahap awal.\n3. Tidak melayani pengurusan bea cukai dan regulasi pengiriman lintas negara (fokus pada pengiriman domestik Indonesia).",
            'marketplace' => "1. Tidak menyediakan aplikasi native mobile store iOS/Android di rilis awal (menggunakan Web Responsive PWA performa tinggi).\n2. Tidak mengelola logistik fisik atau asuransi barang secara langsung (tanggung jawab vendor dan pihak ketiga).\n3. Tidak menyediakan skema kredit cicilan tanpa agunan (BNPL) pihak ketiga selain saluran pembayaran resmi Midtrans.",
            'clinic' => "1. Tidak menyediakan integrasi mesin radiologi / PACS imaging langsung di Fase 1 (fokus pada data rekam medis teks, diagnosa, dan laboratorium).\n2. Tidak mencakup aplikasi native mobile pasien di Google Play / App Store pada tahap MVP.\n3. Tidak melakukan pemotongan klaim BPJS otomatis secara langsung sebelum bridging resmi tersedia.",
            'finance' => "1. Tidak menyediakan software akuntansi full double-entry internal (fokus pada billing, invoicing, penagihan, dan ekspor jurnal).\n2. Tidak melayani fungsi perbankan simpan pinjam komersial.",
            'custom_portal' => "1. Tidak membangun aplikasi native mobile iOS/Android mandiri di Fase 1 MVP (difokuskan pada Progressive Web App berperforma tinggi dan responsif di semua perangkat).\n2. Tidak mencakup integrasi perangkat keras fisik Bluetooth/Thermal khusus tanpa API standar.\n3. Fitur di luar spesifikasi yang disetujui akan diakomodasi melalui Change Request (CR) terpisah.",
        ];

        $outOfScopeEn = [
            'logistics' => "1. No native iOS/Android binary store application in Phase 1 MVP (focus is on lightweight, high-performance Progressive Web App accessible via mobile browsers).\n2. Excludes proprietary non-standard IoT telemetry hardware sensor interfacing in initial release.\n3. Excludes international customs declaration processing (scoped strictly to domestic Indonesian operations).",
            'marketplace' => "1. No native mobile app store binaries in Phase 1 MVP (delivered as a fast Responsive Web PWA).\n2. Platform does not directly manage physical inventory custody or third-party transit insurance.\n3. No custom third-party Buy-Now-Pay-Later (BNPL) credit underwriting outside standard Midtrans channels.",
            'clinic' => "1. Excludes direct integration with physical radiology / PACS machinery in Phase 1 MVP (focus on text clinical records, diagnoses, and lab results).\n2. Excludes native mobile patient app store distribution in MVP release.\n3. Excludes direct unbridged national health insurance (BPJS) claim underwriting.",
            'finance' => "1. Excludes full internal double-entry ledger bookkeeping replacement (focuses on invoicing, automated receivables, and journal exports).\n2. Does not function as a licensed banking deposit/loan custodian.",
            'custom_portal' => "1. No native mobile app store application development in Phase 1 MVP (focused on high-performance Responsive Web PWA).\n2. No direct proprietary physical hardware interfacing without standard web protocols.\n3. Any additions outside this scope will be accommodated through a formal Change Request (CR).",
        ];

        return $isEn ? ($outOfScopeEn[$domain] ?? $outOfScopeEn['custom_portal']) : ($outOfScopeId[$domain] ?? $outOfScopeId['custom_portal']);
    }

    /**
     * Generate proactive, contextual suggestion chips to guide the client on potential blindspots.
     */
    public function generateProactiveSuggestions(string $corpus, string $domain, bool $isEn): array
    {
        $corpusLower = strtolower($corpus);

        $allSuggestions = [
            'whatsapp' => [
                'id' => 'whatsapp_notif',
                'category' => 'integration',
                'title' => $isEn ? 'Automated WhatsApp Alerts' : 'Notifikasi WhatsApp Otomatis',
                'desc' => $isEn ? 'Dispatch real-time transaction updates, receipts, and order statuses to user WhatsApp numbers.' : 'Kirimkan update status pesanan, resi pengiriman, dan tanda terima otomatis ke WhatsApp pengguna.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'Official WhatsApp Business Cloud API for automated customer alerts' : 'WhatsApp Cloud API untuk notifikasi transaksi & pembaruan status real-time',
                'badge' => 'Integrasi',
            ],
            'payment' => [
                'id' => 'payment_midtrans',
                'category' => 'integration',
                'title' => $isEn ? 'Midtrans Payment Gateway (QRIS & VA)' : 'Midtrans Payment Gateway (QRIS & VA)',
                'desc' => $isEn ? 'Enable instant checkout via Bank Virtual Accounts (BCA/Mandiri/BRI), QRIS, and Credit Cards.' : 'Dukungan pembayaran multi-channel otomatis via Virtual Account Bank, QRIS, dan Kartu Kredit.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'Midtrans Payment Gateway (Snap API, Virtual Accounts, QRIS, Credit Card)' : 'Midtrans Payment Gateway (Snap API, QRIS, Virtual Account Bank BCA/Mandiri/BRI, Kartu Kredit)',
                'badge' => 'Pembayaran',
            ],
            'excel_export' => [
                'id' => 'excel_export',
                'category' => 'feature',
                'title' => $isEn ? 'Comprehensive Excel & PDF Reporting' : 'Ekspor Laporan Excel (.xlsx) & PDF',
                'desc' => $isEn ? 'Allow managers to export audit reconciliations, financial ledgers, and operational tables to Excel.' : 'Fitur unduh laporan operasional, rekapitulasi data harian/bulanan, dan audit transaksi ke format Excel dan PDF.',
                'target_field' => 'fiturWajib',
                'addition' => $isEn ? 'Comprehensive Data Export Suite: One-click export of operational ledgers and metrics to Microsoft Excel (.xlsx) and printable PDF.' : 'Modul Ekspor Laporan Komprehensif: Unduh rekapitulasi operasional dan riwayat data ke format Microsoft Excel (.xlsx) dan PDF resmi siap cetak.',
                'badge' => 'Fitur MVP',
            ],
            'approval_role' => [
                'id' => 'approval_role',
                'category' => 'actor',
                'title' => $isEn ? 'Supervisor / Manager Approval Tier' : 'Tingkat Akses Supervisor / Approval',
                'desc' => $isEn ? 'Prevent operational errors by requiring a manager authorization step before critical actions are executed.' : 'Cegah salah eksekusi dengan otorisasi persetujuan (approval) berjenjang oleh Supervisor atau Manajer.',
                'target_field' => 'aktorSistem',
                'addition' => $isEn ? 'Supervisor / Manager: Multi-tier review and authorization before high-value or critical transactions are executed.' : 'Supervisor / Manajer: Otorisasi persetujuan berjenjang sebelum transaksi bernilai tinggi atau perubahan data krusial dieksekusi.',
                'badge' => 'Aktor & RBAC',
            ],
            'refund_flow' => [
                'id' => 'refund_flow',
                'category' => 'workflow',
                'title' => $isEn ? 'Cancellation & Refund Workflow' : 'Alur Pembatalan & Pengembalian Dana',
                'desc' => $isEn ? 'Establish transparent guidelines and automated steps for customer order cancellation and refund claims.' : 'Definisikan alur resmi penanganan pembatalan pesanan, verifikasi alasan, dan pencatatan pengembalian dana (refund).',
                'target_field' => 'alurKerja',
                'addition' => $isEn ? 'Cancellation & Refund Procedure: Client submits request with reason -> Admin inspects validity -> Automated refund ledger adjustment and notification dispatch.' : 'Alur Pembatalan & Pengembalian Dana: Klien mengajukan pembatalan dengan alasan -> Staf/Admin memverifikasi keabsahan -> Penyesuaian saldo dan pengiriman bukti refund otomatis.',
                'badge' => 'Alur Kerja',
            ],
            'audit_trail' => [
                'id' => 'audit_trail',
                'category' => 'security',
                'title' => $isEn ? 'Immutable Security Audit Trail' : 'Audit Trail & Rekam Jejak Keamanan',
                'desc' => $isEn ? 'Record who created, edited, or deleted records with user ULID and timestamp to ensure high compliance.' : 'Pencatatan riwayat setiap kali data diubah atau dihapus, lengkap dengan identitas pengguna, IP, dan timestamp.',
                'target_field' => 'kepatuhanKeamanan',
                'addition' => $isEn ? 'Immutable Security Audit Trail: Complete forensic logging of who modified or deleted critical records with timestamps and IP records.' : 'Audit Trail & Rekam Jejak Forensik: Pencatatan otomatis setiap aksi perubahan/penghapusan data krusial lengkap dengan identitas pengguna dan timestamp.',
                'badge' => 'Keamanan',
            ],
            'google_sso' => [
                'id' => 'google_sso',
                'category' => 'feature',
                'title' => $isEn ? '1-Click Google Sign-In (OAuth)' : 'Login 1-Klik Google (Google SSO)',
                'desc' => $isEn ? 'Allow users to register and sign in effortlessly using their Google account without memorizing passwords.' : 'Permudah klien dan staf masuk ke sistem dengan sekali klik menggunakan akun Google resmi tanpa menghafal password baru.',
                'target_field' => 'fiturWajib',
                'addition' => $isEn ? 'Single Sign-On (SSO): 1-click Google OAuth 2.0 authentication for frictionless client onboarding.' : 'Autentikasi 1-Klik Google Sign-In (OAuth 2.0) untuk mempercepat pendaftaran dan kenyamanan login pengguna.',
                'badge' => 'Fitur MVP',
            ],
        ];

        if ($domain === 'logistics') {
            $allSuggestions['pod_signature'] = [
                'id' => 'pod_signature',
                'category' => 'feature',
                'title' => $isEn ? 'Digital Signature on Delivery (e-POD)' : 'Tanda Tangan Digital Driver (e-POD)',
                'desc' => $isEn ? 'Allow driver to capture recipient signature on screen upon package handover.' : 'Penerima menandatangani langsung serah terima barang di layar smartphone kurir/driver sebagai bukti sah.',
                'target_field' => 'fiturWajib',
                'addition' => $isEn ? 'Digital Signature & Proof of Delivery (e-POD): Recipient signs on mobile touchscreen upon parcel receipt with GPS timestamp.' : 'Tanda Tangan Digital & Bukti Serah Terima (e-POD): Penerima menandatangani langsung di layar smartphone kurir dilengkapi koordinat GPS dan foto fisik.',
                'badge' => 'Fitur MVP',
            ];
        } elseif ($domain === 'clinic') {
            $allSuggestions['satusehat'] = [
                'id' => 'satusehat_integration',
                'category' => 'integration',
                'title' => $isEn ? 'SatuSehat Kemenkes (FHIR API)' : 'Integrasi SatuSehat Kemenkes (FHIR)',
                'desc' => $isEn ? 'Synchronize patient clinical encounters with the national health data exchange.' : 'Sinkronisasi rekam medis dan data kunjungan pasien dengan platform SatuSehat Kementerian Kesehatan RI.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'SatuSehat Ministry of Health FHIR API bi-directional medical record bridging' : 'SatuSehat Kemenkes RI (FHIR Interoperability API) untuk standardisasi rekam medis nasional',
                'badge' => 'Integrasi',
            ];
        } elseif ($domain === 'marketplace') {
            $allSuggestions['courier_rates'] = [
                'id' => 'courier_rates',
                'category' => 'integration',
                'title' => $isEn ? 'Automated Courier Shipping Rates' : 'Kalkulasi Ongkir Kurir Otomatis',
                'desc' => $isEn ? 'Calculate real-time shipping costs for JNE, SiCepat, J&T based on destination sub-district.' : 'Hitung tarif ongkos kirim real-time (JNE, SiCepat, J&T) secara otomatis berdasarkan kota/kecamatan tujuan.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'Multi-courier Shipping API (JNE, SiCepat, J&T) for automated destination freight calculation' : 'API Ekspedisi Multi-Kurir (JNE, SiCepat, J&T) untuk kalkulasi ongkos kirim otomatis berdasarkan kecamatan tujuan',
                'badge' => 'Integrasi',
            ];
        }

        $suggestions = [];
        foreach ($allSuggestions as $key => $item) {
            $keyword = strtolower($item['id']);
            if (!str_contains($corpusLower, $keyword) && count($suggestions) < 6) {
                $suggestions[] = $item;
            }
        }

        return $suggestions;
    }

    /**
     * Calculate readiness and completeness score across the blueprint specifications.
     */
    public function calculateCompleteness(array $data, bool $isEn): array
    {
        $checklist = [
            [
                'key' => 'namaBisnis',
                'label' => $isEn ? 'Project Name & Identity' : 'Identitas & Nama Proyek',
                'weight' => 10,
                'completed' => !empty($data['namaBisnis']) && mb_strlen($data['namaBisnis']) >= 3,
                'tip' => $isEn ? 'Specify a clear project name' : 'Nama proyek telah terdefinisi dengan jelas',
            ],
            [
                'key' => 'masalahUtama',
                'label' => $isEn ? 'Core Problem & Pain Point' : 'Masalah Utama & Solusi Bisnis',
                'weight' => 10,
                'completed' => !empty($data['masalahUtama']) && mb_strlen($data['masalahUtama']) >= 15,
                'tip' => $isEn ? 'Describe the core friction being solved' : 'Uraian masalah utama telah tercakup',
            ],
            [
                'key' => 'tujuanUtama',
                'label' => $isEn ? 'Success Metrics (KPIs)' : 'Tolak Ukur Sukses (KPI)',
                'weight' => 10,
                'completed' => !empty($data['tujuanUtama']) && mb_strlen($data['tujuanUtama']) >= 15,
                'tip' => $isEn ? 'Define measurable outcome goals' : 'Target kuantitatif keberhasilan terdefinisi',
            ],
            [
                'key' => 'aktorSistem',
                'label' => $isEn ? 'System Actors & RBAC' : 'Pengguna & Aktor Sistem (RBAC)',
                'weight' => 15,
                'completed' => !empty($data['aktorSistem']) && mb_strlen($data['aktorSistem']) >= 15,
                'tip' => $isEn ? 'List user roles and authorizations' : 'Peran pengguna dan hak akses telah terstruktur',
            ],
            [
                'key' => 'fiturWajib',
                'label' => $isEn ? 'Phase 1 MVP Features' : 'Fitur Wajib MVP (Fase 1)',
                'weight' => 25,
                'completed' => !empty($data['fiturWajib']) && mb_strlen($data['fiturWajib']) >= 30,
                'tip' => $isEn ? 'Specify core operational MVP features' : 'Fitur utama fase 1 telah dirinci dengan baik',
            ],
            [
                'key' => 'alurKerja',
                'label' => $isEn ? 'Primary User Workflow' : 'Alur Kerja Utama (User Flow)',
                'weight' => 15,
                'completed' => !empty($data['alurKerja']) && mb_strlen($data['alurKerja']) >= 20,
                'tip' => $isEn ? 'Sequence step-by-step user interaction' : 'Langkah alur proses dari awal hingga selesai',
            ],
            [
                'key' => 'kebutuhanIntegrasi',
                'label' => $isEn ? 'Third-Party Integrations' : 'Integrasi & Layanan Pihak Ketiga',
                'weight' => 10,
                'completed' => !empty($data['kebutuhanIntegrasi']) && mb_strlen($data['kebutuhanIntegrasi']) >= 5,
                'tip' => $isEn ? 'Declare required external APIs (Payment, WhatsApp, Maps)' : 'Kebutuhan payment gateway / WhatsApp / API telah ditentukan',
            ],
            [
                'key' => 'outOfScope',
                'label' => $isEn ? 'Negative Boundary (Out of Scope)' : 'Batasan Negatif (Out of Scope)',
                'weight' => 5,
                'completed' => !empty($data['outOfScope']) && mb_strlen($data['outOfScope']) >= 15,
                'tip' => $isEn ? 'Prevent scope creep with explicit boundaries' : 'Batasan yang tidak dikerjakan tertera jelas',
            ],
        ];

        $score = 0;
        foreach ($checklist as $item) {
            if ($item['completed']) {
                $score += $item['weight'];
            }
        }

        $status = $isEn
            ? ($score >= 90 ? 'Ready to Lock' : ($score >= 70 ? 'Substantially Complete' : 'Needs More Detail'))
            : ($score >= 90 ? 'Sangat Siap Dikunci' : ($score >= 70 ? 'Hampir Sempurna' : 'Perlu Dilengkapi'));

        return [
            'score' => min(100, $score),
            'status' => $status,
            'checklist' => $checklist,
        ];
    }

    /**
     * Proactively integrate a client's additional idea / requirement into the appropriate blueprint field.
     */
    public function supplementIdea(array $currentBlueprint, string $supplementText, string $locale = 'id'): array
    {
        $isEn = ($locale === 'en');
        $cleanText = trim($supplementText);

        if (mb_strlen($cleanText) < 3) {
            throw new \InvalidArgumentException($isEn 
                ? 'Please provide a valid idea description.' 
                : 'Mohon masukkan uraian ide atau kebutuhan yang valid.');
        }

        $textLower = strtolower($cleanText);
        $affectedFields = [];
        $data = $currentBlueprint;

        // Classification heuristics
        $isIntegration = str_contains($textLower, 'whatsapp') || 
                         str_contains($textLower, 'midtrans') || 
                         str_contains($textLower, 'payment') || 
                         str_contains($textLower, 'qris') || 
                         str_contains($textLower, 'api') || 
                         str_contains($textLower, 'maps') || 
                         str_contains($textLower, 'email') || 
                         str_contains($textLower, 'storage') ||
                         str_contains($textLower, 's3') ||
                         str_contains($textLower, 'ongkir');

        $hasActionVerb = str_contains($textLower, 'cetak') || 
                         str_contains($textLower, 'tampilkan') || 
                         str_contains($textLower, 'sistem') || 
                         str_contains($textLower, 'fitur') || 
                         str_contains($textLower, 'modul') || 
                         str_contains($textLower, 'laporan') || 
                         str_contains($textLower, 'ekspor') || 
                         str_contains($textLower, 'upload') || 
                         str_contains($textLower, 'scan') || 
                         str_contains($textLower, 'notifikasi') ||
                         str_contains($textLower, 'tambah fitur');

        $isRole = (str_contains($textLower, 'role') || 
                   str_contains($textLower, 'aktor') || 
                   str_contains($textLower, 'tipe user') || 
                   str_contains($textLower, 'hak akses') ||
                   str_contains($textLower, 'tambah user') ||
                   str_contains($textLower, 'tambah pengguna') ||
                   str_contains($textLower, 'tambah staff')) && !$hasActionVerb;

        $isWorkflow = (str_contains($textLower, 'alur') || 
                       str_contains($textLower, 'langkah') || 
                       str_contains($textLower, 'flow') || 
                       str_contains($textLower, 'setelah') || 
                       str_contains($textLower, 'kemudian') || 
                       str_contains($textLower, 'refund') || 
                       str_contains($textLower, 'retur') || 
                       str_contains($textLower, 'pembatalan')) && !str_contains($textLower, 'cetak');

        $isOutOfScope = str_contains($textLower, 'tidak perlu') || 
                        str_contains($textLower, 'jangan') || 
                        str_contains($textLower, 'exclude') || 
                        str_contains($textLower, 'di luar') || 
                        str_contains($textLower, 'out of scope') || 
                        str_contains($textLower, 'bukan prioritas');

        $isRoadmap = str_contains($textLower, 'fase 2') || 
                     str_contains($textLower, 'nanti') || 
                     str_contains($textLower, 'tahap berikutnya') || 
                     str_contains($textLower, 'future') || 
                     str_contains($textLower, 'roadmap');

        // Apply to respective fields
        if ($isOutOfScope) {
            $data['outOfScope'] = $this->appendNumberedItem($data['outOfScope'] ?? '', $cleanText);
            $affectedFields[] = 'outOfScope';
        } elseif ($isRoadmap) {
            $data['fiturTambahan'] = $this->appendNumberedItem($data['fiturTambahan'] ?? '', $cleanText);
            $affectedFields[] = 'fiturTambahan';
        } elseif ($isRole) {
            $data['aktorSistem'] = $this->appendNumberedItem($data['aktorSistem'] ?? '', $cleanText);
            $affectedFields[] = 'aktorSistem';
        } elseif ($isWorkflow) {
            $data['alurKerja'] = $this->appendNumberedItem($data['alurKerja'] ?? '', $cleanText);
            $affectedFields[] = 'alurKerja';
        } else {
            // Default to MVP features
            $data['fiturWajib'] = $this->appendNumberedItem($data['fiturWajib'] ?? '', $cleanText);
            $affectedFields[] = 'fiturWajib';
        }

        // If it also mentions integrations, append to kebutuhanIntegrasi
        if ($isIntegration) {
            $existing = trim($data['kebutuhanIntegrasi'] ?? '');
            if ($existing) {
                $data['kebutuhanIntegrasi'] = rtrim($existing, ',.') . ', ' . $cleanText;
            } else {
                $data['kebutuhanIntegrasi'] = $cleanText;
            }
            if (!in_array('kebutuhanIntegrasi', $affectedFields)) {
                $affectedFields[] = 'kebutuhanIntegrasi';
            }
        }

        // Recompute completeness score
        $data['completeness'] = $this->calculateCompleteness($data, $isEn);

        $fieldNameLabelsId = [
            'fiturWajib' => 'Fitur Wajib MVP',
            'fiturTambahan' => 'Fitur Tambahan (Roadmap)',
            'aktorSistem' => 'Aktor & Tingkatan Pengguna',
            'alurKerja' => 'Alur Kerja Utama',
            'kebutuhanIntegrasi' => 'Integrasi Pihak Ketiga',
            'outOfScope' => 'Batasan (Out of Scope)',
        ];

        $labels = array_map(fn($f) => $fieldNameLabelsId[$f] ?? $f, $affectedFields);
        $fieldsString = implode(' & ', $labels);

        $message = $isEn
            ? "Your supplementary requirement was intelligently placed into [{$fieldsString}]!"
            : "Ide tambahan Anda berhasil disematkan ke bagian [{$fieldsString}]!";

        return [
            'message' => $message,
            'affected_fields' => $affectedFields,
            'data' => $data,
        ];
    }

    protected function appendNumberedItem(string $existingText, string $newItem): string
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", trim($existingText)))));
        $nextNum = count($lines) + 1;
        $cleanedItem = preg_replace('/^\d+[\.\)]\s*/', '', $newItem);
        $lines[] = "{$nextNum}. {$cleanedItem}";
        return implode("\n", $lines);
    }
}

