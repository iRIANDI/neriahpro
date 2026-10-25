<?php

namespace App\Services;

use App\Exceptions\SecurityException;
use App\Jobs\ProcessSecureDataset;
use App\Services\Ai\MultiAiModelManager;
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
     * @param string|null $projectName
     * @param string|null $preferredAiProvider
     * @return array
     * @throws \InvalidArgumentException
     */
    public function synthesize(
        string $rawIdeaText, 
        array $uploadedFiles = [], 
        string $locale = 'id', 
        ?string $projectName = null,
        ?string $preferredAiProvider = null
    ): array
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
                    // Sandboxed inspection against executable scripts & dataset loader exploits
                    ProcessSecureDataset::inspectAndSanitizeUploadedFile($file);

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

        // 2.5 Structured Key-Value Brief Parsing
        $brief = $this->parseStructuredBrief($corpusText);

        // 3. Extract & Synthesize Architectural Blueprint
        $result = $this->performArchitecturalAnalysis($rawIdeaText, $allDocsMarkdown, $corpusText, $fileSummaries, $isEn, $brief);

        // 3.5 Deep AI Enhancement across Multi-AI Providers (DeepSeek-R1, Claude 3.7, Gemini 2.5, ChatGPT, Grok, Groq)
        $result = $this->enhanceWithMultiAi($result, $corpusText, $locale, $preferredAiProvider, $brief);

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
        if (mb_strlen($cleanRaw) > 100000) {
            throw new \InvalidArgumentException($isEn
                ? 'Text input exceeds the maximum allowed size (100,000 characters). Please attach as a document instead.'
                : 'Uraian teks melebihi batas maksimal 100.000 karakter. Silakan lampirkan dalam bentuk dokumen.');
        }

        // Check 3: Repetitive character spam (e.g. "aaaaaaaaaaaaaa" or "11111111111111111")
        if (preg_match('/([a-zA-Z0-9])\1{20,}/u', $cleanRaw)) {
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
     * Parse structured key-value briefs (e.g. from spreadsheets, tables, or labeled prompts).
     * Supports separators: tab (\t), colon (:), equals (=), dash (-/—).
     */
    public function parseStructuredBrief(string $corpus): array
    {
        $sectionMap = [
            'namaBisnis' => ['nama bisnis', 'nama usaha', 'nama proyek', 'nama brand', 'nama aplikasi', 'nama platform', 'project name', 'business name', 'company name'],
            'masalahUtama' => ['masalah utama', 'permasalahan', 'kendala utama', 'problem statement', 'pain points', 'core problem', 'profil sekolah', 'profil usaha', 'profil bisnis', 'profil lembaga', 'profil perusahaan', 'latar belakang', 'background', 'deskripsi proyek', 'deskripsi bisnis', 'tentang sekolah', 'tentang usaha'],
            'tujuanUtama' => ['tujuan utama', 'tujuan proyek', 'tujuan bisnis', 'primary goal', 'kpi', 'target capaian', 'visi & misi', 'visi misi'],
            'fiturWajib' => ['fitur wajib', 'fitur inti', 'fitur utama', 'fitur mvp', 'fitur fase 1', 'core features', 'mvp features', 'must-have', 'modul wajib', 'modul inti', 'modul utama'],
            'fiturTambahan' => ['fitur tambahan', 'fitur fase 2', 'fitur lanjutan', 'roadmap', 'future features', 'nice-to-have', 'modul tambahan', 'fase 2'],
            'aktorSistem' => ['pengguna dan role', 'pengguna & role', 'aktor sistem', 'aktor & role', 'role pengguna rbac', 'user roles', 'system actors', 'matriks wewenang rbac'],
            'alurKerja' => ['alur kerja', 'alur bisnis', 'alur operasional', 'workflow sistem', 'user journey', 'proses transaksi', 'user flow', 'alur pendaftaran', 'alur proses', 'alur sistem'],
            'kebutuhanIntegrasi' => ['kebutuhan integrasi', 'integrasi api', 'third-party integrations', 'integrations', 'koneksi api', 'gerbang pembayaran'],
            'outOfScope' => ['out of scope', 'batasan sistem', 'ruang lingkup negatif', 'di luar lingkup', 'exclusions', 'non-scope'],
            'kisaranBudget' => ['alokasi budget', 'kisaran budget', 'budget range', 'anggaran biaya', 'estimasi investasi'],
            'targetWaktu' => ['target waktu', 'target rilis', 'timeline', 'durasi kerja', 'target hari'],
            'targetPlatform' => ['target platform', 'platform perangkat', 'sasaran perangkat'],
            'referensiDesain' => ['referensi desain', 'design reference', 'gaya desain'],
        ];

        $lines = preg_split('/\r\n|\r|\n/', $corpus);
        $parsed = [];
        $currentSection = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            // 1. Detect H1 title e.g. "# Master Blueprint Sistem Informasi SEKOLAH ADVENT"
            if (preg_match('/^#\s+([^\n\r]+)/u', $trimmed, $h1Match)) {
                $rawTitle = trim($h1Match[1]);
                $cleanTitle = trim(preg_replace('/^(?:Master\s+Blueprint(?:\s+Sistem\s+Informasi)?|Dokumen\s+Spesifikasi|PRD)\s*[-—:\s]*/iu', '', $rawTitle));
                if (!empty($cleanTitle) && mb_strlen($cleanTitle) >= 3 && empty($parsed['namaBisnis'])) {
                    $parsed['namaBisnis'] = $cleanTitle;
                }
                $currentSection = null;
                continue;
            }

            // Boundary Reset: Reset section if encountering major chapters/headings/sections
            if (preg_match('/^(?:BAGIAN\s+\d+|BAB\s+\d+|SECTION\s+\d+|MODUL\s+\d+|##+\s+|---)/iu', $trimmed)) {
                $currentSection = null;
            }

            $matchedSection = null;
            $matchedValue = null;

            // Strip markdown headings (##, ###) and bullets (- , * )
            $cleanLine = trim(preg_replace('/^(?:#+\s*|[-*]\s*)+/u', '', $trimmed));
            $cleanLineWithoutColons = trim(rtrim($cleanLine, " :\t-—"));

            // 2. Check structured key-value line: "Header : Value" or "Header - Value" or "Header\tValue"
            if (preg_match('/^([A-Za-z0-9\s\/\(\)&]{3,40}?)(?:\t|:\s*|\s*=\s*|\s+[-—]\s+)([\s\S]*)$/u', $cleanLine, $m)) {
                $headerCandidate = strtolower(trim(preg_replace('/\s*\([^)]*\)/', '', $m[1])));
                foreach ($sectionMap as $secKey => $synonyms) {
                    foreach ($synonyms as $syn) {
                        if ($headerCandidate === $syn || (strlen($syn) >= 5 && str_starts_with($headerCandidate, $syn))) {
                            $matchedSection = $secKey;
                            $matchedValue = trim($m[2]);
                            break 2;
                        }
                    }
                }
            }

            // 3. Check pure section heading: "### Fitur Wajib:" or "## Profil Sekolah"
            if (!$matchedSection) {
                $candidateLower = strtolower(trim(preg_replace('/\s*\([^)]*\)/', '', $cleanLineWithoutColons)));
                foreach ($sectionMap as $secKey => $synonyms) {
                    foreach ($synonyms as $syn) {
                        if ($candidateLower === $syn || (strlen($syn) >= 5 && str_starts_with($candidateLower, $syn))) {
                            $matchedSection = $secKey;
                            $matchedValue = '';
                            break 2;
                        }
                    }
                }
            }

            if ($matchedSection) {
                $currentSection = $matchedSection;
                if ($currentSection === 'namaBisnis') {
                    $matchedValue = trim(preg_replace('/\s*\([^)]*(?:teman|contoh|opsional|field)[^)]*\)/i', '', $matchedValue));
                }
                if (!empty($matchedValue)) {
                    $parsed[$currentSection] = $matchedValue;
                }
            } elseif ($currentSection) {
                // Prevent single section from swallowing huge documents (limit to 2500 chars max)
                if (isset($parsed[$currentSection]) && strlen($parsed[$currentSection]) > 2500) {
                    $currentSection = null;
                    continue;
                }
                if (!empty($parsed[$currentSection])) {
                    $parsed[$currentSection] .= "\n" . $trimmed;
                } else {
                    $parsed[$currentSection] = $trimmed;
                }
            }
        }

        // Special extraction: If document is Adventist Education Blueprint, inject canonical 15 RBAC roles
        if (preg_match('/BAGIAN\s+2:.*?Aktor\s+RBAC/iu', $corpus) || (preg_match('/sekolah\s+advent|adventist/i', $corpus) && (str_contains($corpus, 'RBAC') || str_contains($corpus, 'Aktor')))) {
            $parsed['aktorSistem'] = implode("\n", [
                "1. Direktur Pendidikan Uni / Divisi (Super-Tenant UIKB/UIKT/SSD): Monitoring executive dashboard lintas konferens, master curriculum repository, dan approval akreditasi AAA.",
                "2. Direktur Pendidikan Konferens/Daerah & Pengurus Yayasan (Regional Tenant): Parameter skala upah denominasi, mutasi guru antar-sekolah, dan pengawasan laporan keuangan konsolidasi SunPlus & ISAK 35.",
                "3. Majelis Sekolah (School Board - Unit Sekolah Governance): Pengawasan anggaran operasional (OpEx), laporan tunggakan SPP, dan persetujuan penerimaan beasiswa internal.",
                "4. Kepala Sekolah & Wakil Kepala (Manajemen Eksekutif): Kalender akademik, rombel, supervisi klinis RPP berbasis IFL, dan pengesahan rapor digital & sertifikat kelulusan.",
                "5. Admin Sekolah & Operator Dapodik (Operasional): Data induk siswa & pegawai (NISN, NIK, KK, NUPTK), Dapodik Sync Middleware, dan inventaris sarpras.",
                "6. Bendahara Sekolah (School Treasurer) & Kasir (Keuangan): Tarif SPP, skema cicilan/beasiswa/work-study, petty cash, payroll, dan ekspor jurnal SunPlus GC & ISAK 35.",
                "7. Education Consultant / Tim Admisi PPDB (Penerimaan Siswa): Admission CRM Funnel, konsultasi pemilihan jalur (Day School/Boarding), verifikasi berkas, dan tes penempatan.",
                "8. Guru / Fasilitator Kelas & Pembina Ekstrakurikuler (Akademik): Modul ajar LMS dengan Integrasi Iman & Pembelajaran (IFL), presensi harian, penilaian Tiga Segi (Kepala, Hati, Tangan), dan ujian CBT.",
                "9. Pendeta Sekolah (School Chaplain) & Guru BK (Pastoral & Konseling): Spiritual Journey Ledger (Care Groups, baptisan, Pekan Doa), Confidential Counseling Vault, dan Restorative Discipline Tracker.",
                "10. Pamong Asrama / Deans (Dean of Men & Dean of Women): Penempatan kamar/loker, verifikasi Digital Exeat Pass (2FA), presensi ibadah pagi/petang (Vespers), dan inspeksi kebersihan kamar.",
                "11. Supervisor / Mandor Kerja (Work-Study Supervisor): Penugasan unit kerja kampus (pertanian, bakery, kafetaria, pertamanan), validasi timesheet presensi kerja tangan pengurang SPP.",
                "12. Petugas Medis UKS / Perawat Klinik & Manajer Kafetaria (Kesehatan & Gizi): Rekam medis elektronik, rujukan Rumah Sakit Advent, dan menu gizi vegetarian NEWSTART.",
                "13. Petugas Keamanan (Satpam Gerbang) & Kru Bus Sekolah (Keamanan & Armada): Pemindaian QR Code Exeat Pass, One-Time Token penjemput harian, dan GPS pelacak rute bus sekolah.",
                "14. Pendeta Jemaat Lokal & Pemimpin Pathfinder (Ekosistem Gereja Pendukung): Verifikasi keanggotaan jemaat aktif (Constituent Discount) dan pengujian lencana kepakaran kepanduan (Honor Badges).",
                "15. Orang Tua / Wali & Murid / Siswa (Parent & Student Portal): Pemantauan akademik & karakter real-time, persetujuan izin pesiar asrama (2FA), pembayaran SPP, dan akses materi LMS & CBT.",
            ]);
        }

        return $parsed;
    }

    /**
     * Clean and normalize multi-item strings into standardized numbered lists (1. ...\n2. ...).
     */
    protected function formatListIfNeeded(string $text): string
    {
        $text = trim($text);
        if (empty($text)) {
            return '';
        }

        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));

        // Multi-line numbered list
        if (count($lines) > 1 && preg_match('/^\d+[\.\)]\s*/', $lines[0])) {
            $formatted = [];
            foreach ($lines as $i => $line) {
                $clean = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                $formatted[] = ($i + 1) . '. ' . $clean;
            }
            return implode("\n", $formatted);
        }

        // Single line handling
        if (count($lines) === 1) {
            // Check internal inline numbering e.g. "1. Item A 2. Item B"
            if (preg_match_all('/(?:\d+[\.\)]|\b[A-Za-z]\))\s*([^1-9]+?)(?=(?:\d+[\.\)]|\b[A-Za-z]\))|$)/u', $lines[0], $matches)) {
                $items = array_values(array_filter(array_map('trim', $matches[1])));
                if (count($items) > 1) {
                    $formatted = [];
                    foreach ($items as $i => $item) {
                        $formatted[] = ($i + 1) . '. ' . $item;
                    }
                    return implode("\n", $formatted);
                }
            }

            // Comma separated items e.g. "Superadmin, Visitor"
            if (str_contains($lines[0], ',') && !str_starts_with($lines[0], '1.')) {
                $parts = array_map('trim', explode(',', $lines[0]));
                if (count($parts) >= 2 && mb_strlen($lines[0]) < 250) {
                    $formatted = [];
                    foreach ($parts as $i => $part) {
                        $formatted[] = ($i + 1) . '. ' . $part;
                    }
                    return implode("\n", $formatted);
                }
            }
        }

        return $text;
    }

    /**
     * Comprehensive Architectural Analysis & Structuring
     */
    protected function performArchitecturalAnalysis(
        string $rawText, 
        string $docsMarkdown, 
        string $corpus, 
        array $fileSummaries, 
        bool $isEn,
        array $brief = []
    ): array {
        $textLower = strtolower($corpus);
        $noPaymentGateway = (bool) preg_match('/(?:tanpa|tidak\s*pakai|tanpa\s*adanya|no|without)\s*(?:payment\s*gateway|midtrans|gerbang\s*pembayaran)/i', $corpus);

        // 1. Detect Domain & Industry
        $domain = $this->detectDomain($textLower, $brief);
        if ($domain === 'custom_portal' && !empty($fileSummaries)) {
            $fileNamesCombined = implode(' ', array_column($fileSummaries, 'name'));
            $fileDomain = $this->detectDomain($fileNamesCombined, []);
            if ($fileDomain !== 'custom_portal') {
                $domain = $fileDomain;
            }
        }

        // 2. Synthesize Business / Project Name
        $namaBisnis = !empty($brief['namaBisnis']) ? $brief['namaBisnis'] : $this->extractProjectName($corpus, $domain, $isEn);
        $genericNames = [
            'Platform Operasional & Portal Bisnis Terintegrasi',
            'Centralized Enterprise Business Management Platform',
            'Portal Pembelajaran & Administrasi Akademik Digital',
            'Digital Academic Administration & Learning Portal',
            'Platform Katering & Pemesanan Kuliner Harian Terpadu',
            'Integrated Catering & Culinary Ordering Platform',
            'Sistem Manajemen Logistik & Pelacakan Armada Terintegrasi',
            'Integrated Fleet Tracking & Logistics Management Engine',
            'Sistem Informasi Manajemen Klinik & Rekam Medis Elektronik',
            'Clinical Information System & Electronic Medical Records',
        ];
        if ((empty($namaBisnis) || in_array($namaBisnis, $genericNames)) && !empty($fileSummaries)) {
            foreach ($fileSummaries as $fs) {
                $base = pathinfo($fs['name'], PATHINFO_FILENAME);
                $clean = trim(preg_replace('/^(?:Master\s+Blueprint(?:\s+Sistem\s+Informasi)?|Dokumen\s+Spesifikasi|PRD)\s*[-—:\s]*/iu', '', $base));
                if (!empty($clean) && mb_strlen($clean) >= 3) {
                    $namaBisnis = $clean;
                    break;
                }
            }
        }

        // 3. Synthesize Core Problem
        if ($domain === 'education_adventist' && (empty($brief['masalahUtama']) || mb_strlen($brief['masalahUtama']) < 150)) {
            $masalahUtama = $this->synthesizeProblem($corpus, $domain, $isEn);
        } else {
            $masalahUtama = !empty($brief['masalahUtama']) ? $brief['masalahUtama'] : $this->synthesizeProblem($corpus, $domain, $isEn);
        }

        // 4. Synthesize KPIs & Target Outcomes
        $tujuanUtama = !empty($brief['tujuanUtama']) ? $this->formatListIfNeeded($brief['tujuanUtama']) : $this->synthesizeGoal($corpus, $domain, $isEn);

        // 5. Target Audience & System Actors (RBAC)
        $targetAudiens = !empty($brief['targetAudiens']) ? $brief['targetAudiens'] : $this->synthesizeAudience($corpus, $domain, $isEn);
        $aktorSistem = !empty($brief['aktorSistem']) ? $this->formatListIfNeeded($brief['aktorSistem']) : $this->synthesizeActors($corpus, $domain, $isEn);

        // 6. Decompose MVP Core Features (Fase 1)
        $fiturWajib = !empty($brief['fiturWajib']) ? $this->formatListIfNeeded($brief['fiturWajib']) : $this->synthesizeMvpFeatures($corpus, $domain, $isEn);

        // 7. Decompose Phase 2 Roadmap Features
        $fiturTambahan = !empty($brief['fiturTambahan']) ? $this->formatListIfNeeded($brief['fiturTambahan']) : $this->synthesizeRoadmapFeatures($corpus, $domain, $isEn);

        // 8. User Workflow
        $alurKerja = !empty($brief['alurKerja']) ? $this->formatListIfNeeded($brief['alurKerja']) : $this->synthesizeWorkflow($corpus, $domain, $isEn);

        // 9. Third-party Integrations
        if (!empty($brief['kebutuhanIntegrasi'])) {
            $kebutuhanIntegrasi = $brief['kebutuhanIntegrasi'];
        } else {
            $kebutuhanIntegrasi = $this->synthesizeIntegrations($corpus, $domain, $isEn);
        }

        // Clean payment gateways if user specifically requested without payment gateway
        if ($noPaymentGateway) {
            $kebutuhanIntegrasi = preg_replace('/\s*\([^)]*(?:payment\s*gateway|midtrans|tanpa)[^)]*\)/i', '', $kebutuhanIntegrasi);
            $kebutuhanIntegrasi = trim(preg_replace('/\b(?:midtrans(?:\s*snap)?|stripe|xendit|payment\s*gateway|qris\s*otomatis)[,\s]*/i', '', $kebutuhanIntegrasi), ", \t\n\r");
            if (empty($kebutuhanIntegrasi)) {
                $kebutuhanIntegrasi = $isEn ? 'WhatsApp Click-to-Chat API, Google Maps Embed' : 'WhatsApp Click-to-Chat API, Google Maps Embed';
            }
        }

        // 10. Design & Aesthetic References
        $referensiDesain = !empty($brief['referensiDesain']) ? $brief['referensiDesain'] : ($isEn
            ? "Clean Modern Monolith (Linear.app & Stripe inspired), sharp rectangular borders, dark/light mode fidelity, fast data table UX."
            : "Modern Monolith Presisi Sharp (Inspirasi Linear.app & Stripe), sudut tipis elegan non-kapsul, dukungan dark/light mode, fokus kecepatan manipulasi data.");

        // 11. Negative Scope Boundary (Out-of-Scope Anti Scope Creep)
        $outOfScope = !empty($brief['outOfScope']) 
            ? $this->formatListIfNeeded($brief['outOfScope']) 
            : $this->synthesizeOutOfScope($corpus, $domain, $isEn);

        if ($noPaymentGateway && !stripos($outOfScope, 'payment gateway')) {
            $outOfScope = $this->appendNumberedItem($outOfScope, $isEn 
                ? 'No online payment gateway integration in Phase 1 (leads and reservations routed directly via WhatsApp chat or bank transfer).'
                : 'Tidak mencakup integrasi payment gateway otomatis di Fase 1 (seluruh inquiry dan reservasi diarahkan langsung via WhatsApp chat atau transfer manual).');
        }

        // 12. Determine Timeline, User Scale & Budget
        $durasiHari = "30";
        $targetWaktu = $isEn ? "30 Working Days (Phase 1 MVP)" : "30 Hari Kerja (Fase 1 MVP)";

        if (!empty($brief['targetWaktu'])) {
            $targetWaktu = $brief['targetWaktu'];
            if (preg_match('/(\d+)\s*(?:-|sampai|hingga)\s*(\d+)\s*hari/i', $targetWaktu, $dm)) {
                $durasiHari = (string) $dm[2];
            } elseif (preg_match('/(\d+)\s*hari/i', $targetWaktu, $dm)) {
                $durasiHari = (string) $dm[1];
            }
        } elseif (preg_match('/(?:target|waktu|selesai|jalan|deadline|durasi)\s*[^.\n\r]*?(\d+)(?:\s*(?:-|sampai|hingga)\s*(\d+))?\s*hari/i', $corpus, $tm)) {
            $minDays = $tm[1];
            $maxDays = !empty($tm[2]) ? $tm[2] : $minDays;
            $targetWaktu = ($minDays === $maxDays) 
                ? ($isEn ? "{$minDays} Working Days" : "{$minDays} Hari Kerja")
                : ($isEn ? "{$minDays} - {$maxDays} Working Days" : "{$minDays} - {$maxDays} Hari Kerja");
            $durasiHari = (string) $maxDays;
        }

        $skalaPengguna = "0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)";
        $jangkauanPasar = $isEn 
            ? "Domestic Indonesia (IDR, WIB/WITA/WIT, PDP Act Compliance)"
            : "Domestik Indonesia (IDR, Zona WIB/WITA/WIT)";
        $kepatuhanKeamanan = "Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)";

        $kisaranBudget = "Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)";
        if (!empty($brief['kisaranBudget'])) {
            $kisaranBudget = $brief['kisaranBudget'];
        } elseif (preg_match('/(?:budget|anggaran|biaya|dana)\s*[^.\n\r]*?(\d+)\s*(?:-|sampai|hingga)\s*(\d+)\s*(?:jt|juta|jutaan)/i', $corpus, $jtm)) {
            $b1 = number_format((int)$jtm[1] * 1000000, 0, ',', '.');
            $b2 = number_format((int)$jtm[2] * 1000000, 0, ',', '.');
            $kisaranBudget = "Rp {$b1} - Rp {$b2} (Sesuai Alokasi Klien)";
        } elseif (preg_match('/(?:budget|anggaran|biaya|dana)\s*[^.\n\r]*?(\d+)\s*(?:jt|juta|jutaan)/i', $corpus, $jtm)) {
            $b1 = number_format((int)$jtm[1] * 1000000, 0, ',', '.');
            $kisaranBudget = "Rp {$b1} (Sesuai Alokasi Klien)";
        } elseif (preg_match('/(?:rp\.?\s*[\d\.]+\s*(?:-|sampai|hingga)\s*rp\.?\s*[\d\.]+|budget\s*[:=]?\s*[\d\w\s\.\-]+)/i', $corpus, $bm)) {
            $kisaranBudget = trim($bm[0]);
        } elseif ($domain === 'property' && (str_contains($corpus, '5') || str_contains($corpus, '7'))) {
            $kisaranBudget = "Rp 5.000.000 - Rp 7.000.000 (Starter Catalog & Lead Gen)";
        }

        // 13. Critical Enterprise Architectural Parameters
        $targetPlatform = !empty($brief['targetPlatform']) ? $brief['targetPlatform'] : ($isEn
            ? "Responsive Modern Web Application & PWA (Optimized for Desktop, Tablet & Mobile Browser)"
            : "Modern Web Application Responsive & PWA (Optimal untuk Browser Desktop, Tablet & Ponsel Lapangan)");

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

        if ($noPaymentGateway) {
            $terminPembayaran = $isEn
                ? "Standard 50/50 Milestones: 50% Kickoff Down Payment & 50% Final Settlement Post-UAT Acceptance & Key Handover (via Bank Transfer / Official Invoice)"
                : "Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Transfer Bank Resmi / Invoice Manual)";
        }

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
        $firstLine = trim(explode("\n", $corpus)[0] ?? '');
        if (preg_match('/^[A-Z][A-Za-z\s\'.]{2,40}$/u', $firstLine) && !preg_match('/^(?:Master|Blueprint|Dokumen|Project|Sistem|Spesifikasi|Bab|Daftar|Pendahuluan)/i', $firstLine)) {
            $clientName = ucwords(strtolower($firstLine));
        }

        if (empty($clientName) && preg_match('/\b(?:nama\s*saya|atas\s*nama|pic|kontak\s*person)\s*[:=\s]\s*([A-Za-z\s]{3,30})/i', $corpus, $nameMatch)) {
            $clientName = trim($nameMatch[1]);
        }

        // Domain-specific architectural enforcement for Adventist Education
        if ($domain === 'education_adventist') {
            $durasiHari = "45";
            $targetWaktu = $isEn ? "45 Working Days (Phase 1 Enterprise MVP Monolith - 5 Scheduled Sprints)" : "45 Hari Kerja (Fase 1 Enterprise MVP Monolith - 5 Sprint Terjadwal)";
            $kisaranBudget = "Rp 50.000.000 (Studio Monolith MVP Turnkey Enterprise - 10 Pilar Software Factory OS)";
            $skalaPengguna = "10.000 - 100.000 Pengguna / Bulan (Multi-Tenant Sekolah se-Indonesia, Dedicated VPS Monolith Keyset O(1))";
            $jangkauanPasar = $isEn ? "National Indonesia (UIKB & UIKT) & Regional SSD (IDR, WIB/WITA/WIT, PDP Act & SSD Working Policy Compliance)" : "Nasional Indonesia (UIKB & UIKT) & Regional SSD (IDR, Zona WIB/WITA/WIT, Kepatuhan UU PDP & SSD Working Policy)";
            $kepatuhanKeamanan = "Standar OWASP Top 10, Sandboxing Berkas, Enkripsi Catatan Pastoral/BK (Confidential Counseling Vault), Audit Trail GCAS, Kepatuhan UU PDP";
            $garansiSla = $isEn ? "90 Days Full Post-Launch Bug Warranty + 99.9% SLA Response + Full Private GitHub Repository Handover" : "90 Hari Garansi Bug Pascameluncur Penuh + SLA Response 99.9% + Penyerahan Akses Penuh Private Repository GitHub";
            $terminPembayaran = $isEn ? "Standard 50/50 Milestones: 50% Kickoff Down Payment & 50% Final Settlement Post-UAT Acceptance & Key Handover (via Midtrans Snap / Official Church Denomination Bank Transfer)" : "Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap / Transfer Bank Resmi Denominasi)";
            $clientName = (empty($clientName) || mb_strlen($clientName) < 4) ? 'Yoseph Iriandi' : $clientName;
            $email = empty($email) ? 'yoseph.iriandi.tambunan@gmail.com' : $email;
            $phone = empty($phone) ? '081260840428' : $phone;
        } elseif ($domain === 'api_gateway_messaging') {
            $durasiHari = "40";
            $targetWaktu = $isEn ? "40 Working Days (Phase 1 WhatsApp Gateway & High-Throughput Engine)" : "40 Hari Kerja (Fase 1 WhatsApp Gateway & High-Throughput Engine)";
            $kisaranBudget = "Rp 35.000.000 - Rp 50.000.000 (Enterprise Messaging Gateway, Multi-Session Sandbox & Queue)";
            $targetPlatform = $isEn 
                ? "Decoupled High-Concurrency API Gateway & Microservices (Node.js/Go Core Engine + Headless WhatsApp Engine [OpenWA/Baileys] + Redis Queue + Webhook Dispatcher + Filament v5 Admin Portal)"
                : "Decoupled High-Concurrency API Gateway & Microservices (Node.js Core Engine + Headless WhatsApp [OpenWA/Baileys] + Redis Queue + Webhook Dispatcher + Dasbor Filament v5)";
            $preferensiHosting = $isEn
                ? "Dedicated Cloud VPS / Dedicated Server (Multi-Core CPU, min. 8GB - 16GB RAM for Headless Chromium Sessions, Redis 7 Cluster, BullMQ, Nginx Reverse Proxy with Rate Limiting)"
                : "Dedicated Cloud VPS / Dedicated Server (Multi-Core CPU, min. 8GB - 16GB RAM untuk Sesi Headless Chromium, Redis 7 Cluster, BullMQ, Nginx Reverse Proxy dengan Rate Limiting)";
            $skalaPengguna = "100.000 - 1.000.000 Pesan / Hari (High-Throughput Concurrent Async Worker Pools)";
            $kepatuhanKeamanan = "HMAC Webhook Signatures, API Token Cryptographic Rotations, IP Whitelisting, Anti-Ban Session Rate Limiting, TLS 1.3 End-to-End Encryption";
        } elseif ($domain === 'bioinformatics_genomics') {
            $durasiHari = "50";
            $targetWaktu = $isEn ? "50 Working Days (Phase 1 Genomics Pipeline MVP - 5 Scheduled Sprints)" : "50 Hari Kerja (Fase 1 Pipeline Genomik MVP - 5 Sprint Terjadwal)";
            $kisaranBudget = "Rp 45.000.000 - Rp 70.000.000 (Scientific Pipeline, pgvector & Nextflow Automation)";
            $targetPlatform = $isEn 
                ? "Polyglot Scientific Architecture: Python 3.12 / Rust Compute Engine + FastAPI + PostgreSQL 16 pgvector + Next.js 15 UI" 
                : "Arsitektur Ilmiah Polyglot: Python 3.12 / Rust Compute Engine + FastAPI + PostgreSQL 16 pgvector + Next.js 15 UI";
            $preferensiHosting = $isEn
                ? "High-Compute Cloud VPS / Compute Node (min. 16GB-32GB RAM, Dedicated CPU Cores for Alignment/FASTA parsing) + S3/R2 Object Bucket"
                : "High-Compute Cloud VPS / Node Komputasi (min. 16GB-32GB RAM, Dedicated CPU untuk Alignment/FASTA) + S3/R2 Bucket";
            $skalaPengguna = "1.000 - 50.000 Analisis Genomik / Bulan (Batch Worker Pool, Keyset O(1))";
            $kepatuhanKeamanan = "HIPAA Ready, Cryptographic Checksums (SHA256) Data Integrity, End-to-End Encryption for Biological Data";
        } elseif ($domain === 'fintech_ledger_banking') {
            $durasiHari = "45";
            $targetWaktu = $isEn ? "45 Working Days (Phase 1 Core Banking & Double-Entry Ledger MVP)" : "45 Hari Kerja (Fase 1 Core Banking & Pembukuan Berpasangan MVP)";
            $kisaranBudget = "Rp 50.000.000 - Rp 75.000.000 (Double-Entry Engine, Strict Serializable Isolation & Audit Vault)";
            $targetPlatform = $isEn
                ? "Strict Financial Ledger Monolith / Microservices (PHP 8.4+ / Go Gin + PostgreSQL 16 Strict ULID B-Tree + Redis Streams + Filament v5 Admin)"
                : "Monolit Finansial Ketat (PHP 8.4+ / Go + PostgreSQL 16 Strict ULID + Redis Streams + Dasbor Filament v5)";
            $preferensiHosting = $isEn
                ? "Dedicated Hardened VPS / Bare Metal (Multi-Zone PostgreSQL Replication, Write-Ahead Logging, Zero-Downtime Backup)"
                : "Dedicated Hardened VPS (Replikasi Multi-Zone PostgreSQL, WAL Archiving, Backup Zero-Downtime)";
            $skalaPengguna = "100.000 - 1.000.000 Transaksi / Bulan (Strict Serializable Isolation, Keyset Cursor O(1))";
            $kepatuhanKeamanan = "PCI-DSS Ready, ISO 27001 Controls, Immutable Audit Vault, Idempotent Transaction Signatures, TLS 1.3 Strict";
        } elseif ($domain === 'iot_telemetry_robotics') {
            $durasiHari = "40";
            $targetWaktu = $isEn ? "40 Working Days (Phase 1 IoT Telemetry & Fleet Robotics MVP)" : "40 Hari Kerja (Fase 1 Telemetri IoT & Armada Robotika MVP)";
            $kisaranBudget = "Rp 35.000.000 - Rp 55.000.000 (MQTT Broker, Timeseries Engine & 3D Twin HUD)";
            $targetPlatform = $isEn
                ? "Distributed IoT Event Bus: EMQX/Mosquitto MQTT Broker + Node.js/Go Consumer + TimescaleDB/PostgreSQL 16 + React 19 HUD"
                : "Bus Event IoT Terdistribusi: EMQX MQTT Broker + Go/Node.js Consumer + PostgreSQL 16 Timescale + Dasbor HUD React 19";
            $preferensiHosting = $isEn
                ? "High-IOPS Cloud VPS (High Packet-Per-Second Capacity, Docker EMQX Broker, Redis Cluster)"
                : "High-IOPS Cloud VPS (Kapasitas Packet-Per-Second Tinggi, Docker EMQX Broker, Redis Cluster)";
            $skalaPengguna = "10.000 - 100.000 Sensor Node Terhubung (Throughput Telemetri Asinkron)";
            $kepatuhanKeamanan = "mTLS Device Authentication, Hardware Secure Element (ATECC608A) Ready, Encrypted Firmware OTA Pipeline";
        } elseif ($domain === 'ai_agent_rag_pipeline') {
            $durasiHari = "35";
            $targetWaktu = $isEn ? "35 Working Days (Phase 1 Autonomous AI Agent & RAG Pipeline MVP)" : "35 Hari Kerja (Fase 1 Pipeline AI Agentic & RAG Otonom MVP)";
            $kisaranBudget = "Rp 35.000.000 - Rp 50.000.000 (LangGraph, pgvector HNSW Indexing & MCP Server Sandboxes)";
            $targetPlatform = $isEn
                ? "Agentic Execution Architecture: FastAPI / Python 3.12 + LangGraph + PostgreSQL 16 pgvector + SSE Streaming + React 19"
                : "Arsitektur Eksekusi Agentic: FastAPI / Python 3.12 + LangGraph + PostgreSQL 16 pgvector + SSE Streaming + React 19";
            $preferensiHosting = $isEn
                ? "Cloud VPS with High-Memory / Vector Worker Node (pgvector HNSW Cache, LiteLLM Proxy)"
                : "Cloud VPS High-Memory / Node Worker Vektor (pgvector HNSW Cache, LiteLLM Proxy)";
            $skalaPengguna = "10.000 - 250.000 Kueri RAG / Bulan (HNSW Sub-10ms Vector Search, Keyset History)";
            $kepatuhanKeamanan = "Anti-Prompt Injection Filters, Tool Execution Sandboxing, PII Data Redaction, Token Quota Hard-Limits";
        } elseif ($domain === 'cybersecurity_siem_zerotrust') {
            $durasiHari = "45";
            $targetWaktu = $isEn ? "45 Working Days (Phase 1 SIEM & Zero-Trust Defense MVP)" : "45 Hari Kerja (Fase 1 SIEM & Pertahanan Zero-Trust MVP)";
            $kisaranBudget = "Rp 40.000.000 - Rp 65.000.000 (AI Threat Detection, Honeypot Engine & Automated Quarantine)";
            $targetPlatform = $isEn
                ? "Zero-Trust Security Gateway: Go/Rust Log Collector + PostgreSQL 16 Partitioned Logs + eBPF/WAF Firewall + Filament v5 SOC"
                : "Gateway Keamanan Zero-Trust: Go/Rust Log Collector + PostgreSQL 16 Partitioned Logs + eBPF/WAF Firewall + Dasbor SOC Filament v5";
            $preferensiHosting = $isEn
                ? "Isolated Hardened VPS (Suricata/WAF Integration, Multi-Interface Traffic Ingestion)"
                : "Dedicated Hardened VPS Terisolasi (Integrasi WAF/Suricata, Ingesti Trafik Multi-Interface)";
            $skalaPengguna = "10.000.000+ Log Events / Hari (Partitioned Tables, B-Tree Keyset O(1))";
            $kepatuhanKeamanan = "Zero-Trust Architecture, Dynamic IP Quarantine, Cryptographic Log Chaining, Automated Threat Mitigation";
        } elseif ($domain === 'game_3d_simulation' || $domain === 'game_engine_simulation') {
            $durasiHari = "45";
            $targetWaktu = $isEn ? "45 Working Days (Phase 1 3D Simulation Game MVP)" : "45 Hari Kerja (Fase 1 3D Flight Simulation Game MVP)";
            $kisaranBudget = "Rp 35.000.000 - Rp 60.000.000 (Custom 3D WebGL Engine, Mesh Rigging & Physics Simulation)";
            $targetPlatform = $isEn
                ? "WebGL / WebGPU 3D Browser Simulation & Decoupled Game Engine (Three.js / Babylon.js + 6-DOF Physics Engine [Rapier/Cannon.js] + WebGL Shaders + Headless REST API Backend for Multiplayer/Scores)"
                : "WebGL / WebGPU 3D Browser Simulation & Decoupled Game Engine (Three.js + 6-DOF Physics Engine [Rapier/Cannon.js] + WebGL Shaders + Headless REST API Backend untuk Skor/Multiplayer)";
            $preferensiHosting = $isEn
                ? "High-Bandwidth CDN Storage (Cloudflare R2 / AWS S3 for Compressed GLTF/GLB 3D Meshes & Audio Assets) + Lightweight VPS for Game State & Leaderboard API"
                : "High-Bandwidth CDN Storage (Cloudflare R2 untuk Aset 3D GLTF/GLB Terkompresi & Audio) + Dedicated VPS Ringan untuk Game State & Leaderboard API";
            $skalaPengguna = "60 FPS Client-Side Rendering (WebGL 2.0 / WebGPU Hardware Acceleration, Low Memory Footprint < 150MB Assets)";
            $kepatuhanKeamanan = "Client-Server Game State Validation (Anti-Cheat Server Authority), Asset Cryptographic Checksums, Rate-Limited Score Submissions";
        } elseif ($domain === 'decoupled_saas') {
            $durasiHari = "35";
            $targetWaktu = $isEn ? "35 Working Days (Phase 1 Decoupled SaaS Architecture)" : "35 Hari Kerja (Fase 1 Arsitektur Decoupled SaaS)";
            $kisaranBudget = "Rp 30.000.000 - Rp 45.000.000 (Decoupled SPA Frontend + Headless REST API Backend)";
            $targetPlatform = $isEn
                ? "Decoupled Architecture: Standalone Frontend SPA/PWA (Next.js / Vite React / Vue) + Headless RESTful/GraphQL API Backend (PostgreSQL 16 Strict ULID, Keyset O(1))"
                : "Arsitektur Decoupled: Standalone Frontend SPA/PWA (Next.js / Vite React / Vue) + Headless RESTful API Backend (PostgreSQL 16 Strict ULID, Keyset O(1))";
            $preferensiHosting = $isEn
                ? "Decoupled Cloud Hosting: Edge CDN (Vercel / Cloudflare Pages) for Frontend SPA + Dedicated VPS (PHP/Node.js, PostgreSQL 16, Redis 7) for Headless API Engine"
                : "Decoupled Cloud Hosting: Edge CDN (Vercel / Cloudflare Pages) untuk Frontend SPA + Dedicated VPS (PHP/Node.js, PostgreSQL 16, Redis 7) untuk Headless API Engine";
            $skalaPengguna = "0 - 250.000 Pengguna / Bulan (Stateless Decoupled Scalability, Distributed Edge Caching)";
            $kepatuhanKeamanan = "OAuth2 / JWT Token Revocation Blacklist, Strict CORS Policies, Rate Limiting, OWASP Top 10 API Security";
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
            'kesiapanAset' => ($domain === 'education_adventist') ? 'Aset Logo Yayasan, Kurikulum Alkitab Encounter, Bagan Akun SunPlus, dan Dokumen SSD Working Policy Telah Siap' : 'Sedang Disiapkan Tim Internal',
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
        $trackRecommendation = $this->evaluateTrackRecommendation($baseData, $corpus, $domain, $isEn);

        // Align fields 21, 22, 23 if retail track is recommended to prevent leaking Studio 50/50 terms into self-service licenses
        if (($trackRecommendation['recommended_track'] ?? '') === 'retail') {
            if (empty($brief['kisaranBudget']) && !preg_match('/(?:budget|anggaran|biaya|alokasi|Rp|IDR)/i', $corpus)) {
                $baseData['kisaranBudget'] = $isEn
                    ? 'Spark: $0 / Free (Architectural Audit & Blueprint Access)'
                    : 'Spark: Rp 0 (Akses Audit Arsitektur & Cetak Biru Gratis)';
            }
            if (empty($brief['garansiSla'])) {
                $baseData['garansiSla'] = $isEn
                    ? 'Self-Service Delivery: Instant PRD Fortune 500, Strict ULID DDL & Docker Starter Kit Handover'
                    : 'Lisensi Mandiri: Penyerahan Langsung Dokumen PRD Fortune 500, Skema DDL Strict ULID & Docker Starter Kit';
            }
            if (empty($brief['terminPembayaran'])) {
                $baseData['terminPembayaran'] = $isEn
                    ? 'Spark Package (Free): $0 / Free Instant Architectural Audit & Blueprint Access'
                    : 'Paket Spark (Gratis): Rp 0 / Akses Audit Arsitektur & Cetak Biru Gratis 100%';
            }
        }

        $completeness = $this->calculateCompleteness($baseData, $isEn);

        return array_merge($baseData, [
            'domain' => $domain,
            'proactive_suggestions' => $proactiveSuggestions,
            'completeness' => $completeness,
            'track_recommendation' => $trackRecommendation,
            '_meta' => [
                'synthesized_by' => 'neriah_agentic_markitdown_engine',
                'files_processed' => count($fileSummaries),
                'file_details' => $fileSummaries,
                'has_documents' => !empty($fileSummaries),
                'converted_markdown' => $docsMarkdown,
                'combined_markdown_corpus' => $corpus,
                'raw_idea_text' => !empty($rawText) ? $rawText : ($masalahUtama ?: $corpus),
                'domain' => $domain,
                'track_recommendation' => $trackRecommendation,
                'created_at' => now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Evaluate system architecture complexity and recommend optimal execution track (Retail vs Studio).
     */
    public function evaluateTrackRecommendation(array $baseData, string $corpus, string $domain, bool $isEn): array
    {
        $featuresRaw = $baseData['fiturWajib'] ?? '';
        $featuresCount = count(array_filter(preg_split('/\r\n|\r|\n/', (string) $featuresRaw)));
        $actorsRaw = $baseData['aktorSistem'] ?? '';
        $actorsCount = count(array_filter(preg_split('/\r\n|\r|\n/', (string) $actorsRaw)));
        $integrationsRaw = $baseData['kebutuhanIntegrasi'] ?? '';
        $integrationsCount = count(array_filter(preg_split('/\r\n|\r|\n/', (string) $integrationsRaw)));

        // Complexity evaluation
        $score = 2; // base
        if ($featuresCount > 5) $score += 2;
        if ($actorsCount > 2) $score += 2;
        if ($integrationsCount > 2) $score += 2;
        if (preg_match('/(iot|gps|pembayaran bertahap|multi vendor|multi tenant|escrow|real-time|ai|llm|rekam medis|rekonsiliasi)/i', $corpus)) {
            $score += 2;
        }

        $complexityLevel = 'standard';
        $complexityLabel = $isEn ? 'Standard / MVP Scale' : 'Standar / MVP Inti';
        if ($score >= 7) {
            $complexityLevel = 'enterprise';
            $complexityLabel = $isEn ? 'Enterprise / Distributed Scale' : 'Skala Enterprise Terdistribusi';
        } elseif ($score >= 4) {
            $complexityLevel = 'medium';
            $complexityLabel = $isEn ? 'Medium Commercial' : 'Menengah Komersial';
        }

        // Track intent detection
        $corpusLower = strtolower($corpus);
        $wantsSelfService = (bool) preg_match('/(tim sendiri|programmer|developer|koding sendiri|coding sendiri|punya engineer|hanya butuh blueprint|hanya butuh prd|arsitektur saja|api saja)/i', $corpusLower);
        $wantsTurnkey = (bool) preg_match('/(terima beres|tidak bisa koding|tidak ada tim it|butuh tim|full stack|bantu buatkan|kerjakan sampai|siap pakai)/i', $corpusLower);

        $recommendedTrack = 'retail';
        $rationale = '';

        if ($wantsTurnkey && !$wantsSelfService) {
            $recommendedTrack = 'studio';
            $rationale = $isEn
                ? 'Your brief indicates a requirement for turnkey delivery. Neriah Pro Dedicated Studio provides senior engineers to code, test (Pest ApiContractTest), and deploy live with SLA guarantees.'
                : 'Kebutuhan Anda menunjukkan preferensi pengerjaan siap pakai (terima beres). Dedicated Studio Neriah Pro menyediakan Senior Architect & Fullstack untuk mengoding, menguji dengan Pest, hingga deploy live ke VPS bergaransi SLA.';
        } elseif ($wantsSelfService && !$wantsTurnkey) {
            $recommendedTrack = 'retail';
            $rationale = $isEn
                ? 'Your team possesses internal engineering capability. The Software Factory OS Retail License delivers instant Fortune 500 architecture (PRD 26 parameters, Strict ULID DDL, Docker, AI directives) with zero development agency markup.'
                : 'Tim Anda terdeteksi memiliki kapabilitas koding sendiri. Lisensi Digital Retail Software Factory OS memberikan cetak biru arsitektur kelas enterprise instan (PRD 26 parameter, DDL ULID, Docker, aturan AI) tanpa biaya agensi.';
        } else {
            // Neutral / Balanced recommendation
            if ($complexityLevel === 'enterprise') {
                $recommendedTrack = 'studio';
                $rationale = $isEn
                    ? 'Due to distributed architecture and multi-role integration requirements, we recommend Dedicated Studio for guaranteed SLA delivery, with Retail Ultimate as a viable self-service alternative if you have a senior team.'
                    : 'Mengingat tingginya kompleksitas integrasi dan multi-role, Jalur Turnkey Studio disarankan untuk jaminan SLA pengerjaan. Namun jika Anda memiliki tim internal, Jalur Mandiri Lisensi Ultimate siap diunduh seketika.';
            } else {
                $recommendedTrack = 'retail';
                $rationale = $isEn
                    ? 'Both paths are available: Acquire a self-service Retail License for immediate independent coding, or engage our Dedicated Studio for end-to-end turnkey delivery.'
                    : 'Kedua jalur terbuka: Ambil Lisensi Retail untuk koding mandiri instan berbiaya hemat, atau serahkan pengerjaan ke Dedicated Studio Neriah Pro jika ingin terima beres.';
            }
        }

        $retailLitePrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_lite_price', '190.000');
        $retailProPrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_pro_price', '490.000');
        $retailUltimatePrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_ultimate_price', '1.490.000');
        $studioUmkmPrice = \App\Models\CmsGlobalSetting::getVal('pricing_studio_umkm_price', '3.750.000');
        $studioMvpPrice = \App\Models\CmsGlobalSetting::getVal('pricing_studio_mvp_price', 'Custom Quotation');

        return [
            'recommended_track' => $recommendedTrack, // 'retail' | 'studio'
            'complexity' => [
                'score' => $score,
                'level' => $complexityLevel,
                'label' => $complexityLabel,
                'summary' => $isEn
                    ? "{$featuresCount} Must-Have Features • {$actorsCount} Roles • {$integrationsCount} Integration Rails"
                    : "{$featuresCount} Fitur Wajib • {$actorsCount} Aktor Sistem • {$integrationsCount} Rel Integrasi",
            ],
            'rationale' => $rationale,
            'tracks' => [
                'retail' => [
                    'id' => 'retail',
                    'title' => $isEn ? 'Self-Service Track (Digital Retail License)' : 'Jalur Mandiri (Lisensi Digital Retail)',
                    'badge' => $isEn ? 'FOR TEAMS WITH INTERNAL CODERS' : 'UNTUK TIM DENGAN PROGRAMMER SENDIRI',
                    'tagline' => $isEn
                        ? 'Acquire enterprise blueprint and build with your own engineers'
                        : 'Miliki cetak biru arsitektur enterprise untuk dikerjakan tim Anda sendiri',
                    'highlights' => [
                        $isEn ? 'PRD with 26 architectural parameters' : 'Dokumen PRD 26 parameter lengkap',
                        $isEn ? 'PostgreSQL 16 Strict ULID DDL schema (O(1) keyset)' : 'Skema DDL PostgreSQL Strict ULID (O(1) keyset)',
                        $isEn ? 'Docker compose & modern monolith config' : 'Docker container & arsitektur modern monolith',
                        $isEn ? 'AI Coding Directives (.cursorrules & AGENTS.md)' : 'Aturan AI coding agent (.cursorrules & AGENTS.md)',
                    ],
                    'starting_price' => "Rp {$retailLitePrice}",
                    'max_price' => "Rp {$retailUltimatePrice}",
                    'cta_label' => $isEn ? 'Choose Retail License →' : 'Pilih Jalur Mandiri (Lisensi Retail) →',
                ],
                'studio' => [
                    'id' => 'studio',
                    'title' => $isEn ? 'Turnkey Track (Project Studio Dedicated)' : 'Jalur Turnkey (Project Studio Dedicated)',
                    'badge' => $isEn ? '100% CODED BY NERIAH PRO ENGINEERS' : 'DIKERJAKAN PENUH TIM NERIAH PRO',
                    'tagline' => $isEn
                        ? 'End-to-end coding, testing, and cloud deployment with SLA contract'
                        : 'Koding penuh, pengujian Pest, hingga live deployment di server VPS',
                    'highlights' => [
                        $isEn ? '100% turnkey coding across 10 Software Factory OS pillars' : '100% turnkey koding mencakup 10 Pilar Software Factory OS',
                        $isEn ? 'Managed Capacity & Anti-Collision sprint batch' : 'Alokasi slot sprint terisolasi dengan Master Gantt real-time',
                        $isEn ? 'Legally binding SLA contract & 100% copyright transfer' : 'Kontrak legal SLA bersertifikat & serah terima hak cipta 100%',
                        $isEn ? 'Staged 50% DP milestone payment via Midtrans Snap' : 'Skema pembayaran bertahap DP 50% via Midtrans Snap',
                    ],
                    'starting_price' => "Rp {$studioUmkmPrice}",
                    'max_price' => str_contains((string) $studioMvpPrice, 'Custom') ? $studioMvpPrice : "Rp {$studioMvpPrice}",
                    'cta_label' => $isEn ? 'Choose Dedicated Studio →' : 'Pilih Jalur Turnkey (Project Studio) →',
                ]
            ]
        ];
    }

    protected function detectDomain(string $text, array $brief = []): string
    {
        $combinedText = strtolower($text);
        if (!empty($brief)) {
            $combinedText .= ' ' . strtolower(implode(' ', $brief));
        }

        $patterns = [
            'api_gateway_messaging' => ['openwa', 'open-wa', 'baileys', 'whatsapp gateway', 'wa gateway', 'api gateway', 'message broker', 'rabbitmq', 'kafka', 'socket.io', 'webhook dispatcher', 'rate limiter', 'multi-tenant api', 'blast message', 'sms gateway', 'telegram bot', 'pesan otomatis whatsapp', 'broadcast whatsapp', 'session whatsapp', 'qr whatsapp', 'whatsapp api'],
            'game_3d_simulation' => ['video game', 'game 3d', 'game pesawat', 'flight simulator', 'aircraft', 'three.js', 'threejs', 'webgl', 'webgpu', 'babylon.js', 'godot', 'unity', 'game loop', 'physics engine', 'gltf', 'mesh', 'shader', 'multiplayer websocket', 'canvas 3d', 'kokpit pesawat', 'cockpit', 'aerodinamika', 'aerodynamics', 'flight controls'],
            'decoupled_saas' => ['headless', 'decoupled', 'microservices', 'nextjs', 'next.js', 'nuxt', 'fastapi', 'nestjs', 'go gin', 'grpc', 'frontend terpisah', 'separated frontend', 'backend terpisah'],
            'education_adventist' => ['sekolah advent', 'adventist', 'gmahk', 'konferens', 'uni indonesia', 'uikb', 'uikt', 'ssd', 'sunplus', 'sabat', 'shabbat', 'dean of men', 'dean of women', 'work-study', 'kerja tangan', 'pendeta sekolah', 'kurikulum alkitab', 'encounter', 'buku induk advent', 'gcas', 'pendidikan advent', 'day school', 'boarding school', 'sekolah asrama', 'majelis sekolah', 'school board', 'exeat', 'segitiga emas pendidikan advent', 'ifl', 'integrasi iman', 'newstart', 'pathfinder'],
            'fnb_culinary' => ['katering', 'catering', 'makanan', 'kuliner', 'dapur', 'resto', 'restoran', 'cafe', 'kafe', 'kopi', 'bakery', 'roti', 'kue', 'menu mingguan', 'paket diet', 'nasi kotak', 'makan siang', 'resep', 'chef', 'dine in', 'takeaway', 'food'],
            'services_workshop' => ['bengkel', 'servis mobil', 'servis motor', 'montir', 'sparepart', 'onderdil', 'salon', 'barbershop', 'spa', 'laundry', 'cuci sepatu', 'cuci mobil', 'perbaikan', 'reparasi', 'mekanik', 'jasa cuci'],
            'property' => ['villa', 'resort', 'homestay', 'properti', 'perumahan', 'apartemen', 'booking villa', 'kavling', 'tanah', 'kost', 'kontrakan', 'real estate', 'agen properti', 'katalog properti', 'listing villa', 'listing properti', 'sewa villa', 'jual villa'],
            'clinic' => ['klinik', 'pasien', 'dokter', 'rekam medis', 'obat', 'apotek', 'rumah sakit', 'antrean poli', 'kesehatan', 'diagnosis', 'medical', 'dental', 'gigi', 'fisioterapi'],
            'finance' => ['keuangan', 'invoice', 'faktur', 'tagihan', 'pembayaran', 'akuntansi', 'kasir', 'pos', 'pembukuan', 'laporan keuangan', 'escrow', 'pajak'],
            'hr' => ['hrd', 'karyawan', 'rekrutmen', 'pelamar', 'lowongan', 'gaji', 'payroll', 'absensi', 'cuti', 'kinerja', 'talent'],
            'education' => ['sekolah', 'kursus', 'siswa', 'murid', 'guru', 'kelas', 'ujian', 'materi ajar', 'bimbel', 'lms', 'akademik', 'pembelajaran', 'les privat', 'kampus', 'pendidikan', 'paud', 'raport', 'kurikulum'],
            'logistics' => ['logistik', 'ekspedisi', 'armada truk', 'truk kontainer', 'kontainer', 'pengiriman kargo', 'gudang kargo', 'cargo', 'surat jalan', 'resi ekspedisi', 'freight'],
            'marketplace' => ['marketplace', 'jual beli online', 'toko online', 'ecommerce', 'e-commerce', 'multi vendor', 'keranjang belanja', 'checkout online'],
        ];

        // Enrich patterns dynamically from Universal Domain Lexicon knowledge base
        $lexiconDomains = config('domain_lexicon.domains', []);
        foreach ($lexiconDomains as $lexKey => $lexData) {
            if (!empty($lexData['keywords']) && is_array($lexData['keywords'])) {
                $patterns[$lexKey] = array_unique(array_merge($patterns[$lexKey] ?? [], $lexData['keywords']));
            }
        }

        $scores = [];
        foreach ($patterns as $domain => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                $count = substr_count($combinedText, $kw);
                if ($count > 0) {
                    $multiplier = str_contains($kw, ' ') ? 3 : 1;
                    $score += min($count, 30) * $multiplier;
                }
            }
            if ($score > 0) {
                $scores[$domain] = $score;
            }
        }

        if (!empty($scores)) {
            arsort($scores);
            return array_key_first($scores);
        }

        return 'custom_portal';
    }

    protected function extractProjectName(string $corpus, string $domain, bool $isEn): string
    {
        // 1. Try extracting explicit key-value name
        if (preg_match('/(?:nama\s*(?:proyek|bisnis|aplikasi|platform|usaha|katering|toko|bengkel)|project\s*name)\s*[:=\t]\s*([^\n\r,.]+)/i', $corpus, $m)) {
            $name = trim($m[1]);
            if (mb_strlen($name) >= 3 && mb_strlen($name) <= 60) {
                return $name;
            }
        }

        // 2. Try extracting from conversational storytelling patterns (e.g. "namanya Dapur Bu Ani", "usaha saya Katering Berkah", "brand kami ...")
        if (preg_match('/(?:namanya|nama\s*usahanya|nama\s*tokonya|nama\s*brandnya|nama\s*bisnisnya|nama\s*kateringnya|nama\s*bengkelnya|nama\s*kliniknya)\s*[:=\s]+["\']?([A-Z0-9][A-Za-z0-9\s&\'\-]{2,35}?)(?=[.,\n\r]|\s+(?:dan|yang|di|dengan|selama|sejak|karena)|$)/iu', $corpus, $m)) {
            $name = trim(rtrim($m[1], ".,\n\r"));
            if (mb_strlen($name) >= 3 && mb_strlen($name) <= 50) {
                return $name;
            }
        }

        if (preg_match('/(?:usaha|bisnis|katering|toko|bengkel|klinik|brand|pt|cv)\s+(?:saya|kami)\s+(?:namanya\s+)?["\']?([A-Z0-9][A-Za-z0-9\s&\'\-]{2,35}?)(?=[.,\n\r]|\s+(?:dan|yang|di|dengan|selama|sejak|karena)|$)/iu', $corpus, $m)) {
            $name = trim(rtrim($m[1], ".,\n\r"));
            if (mb_strlen($name) >= 3 && mb_strlen($name) <= 50) {
                return $name;
            }
        }

        // 3. Try extracting first heading from markitdown
        if (preg_match('/^#\s+([^\n\r]+)/m', $corpus, $hMatch)) {
            $candidate = trim($hMatch[1]);
            if (mb_strlen($candidate) >= 4 && mb_strlen($candidate) <= 60 && !stripos($candidate, 'error') && !stripos($candidate, 'scanned')) {
                return $candidate;
            }
        }

        // Dynamic check against Universal Domain Lexicon
        $lexiconDomains = config('domain_lexicon.domains', []);
        if (isset($lexiconDomains[$domain]['title'])) {
            return $isEn 
                ? ($lexiconDomains[$domain]['title']['en'] ?? $lexiconDomains[$domain]['title']['id']) 
                : ($lexiconDomains[$domain]['title']['id'] ?? $lexiconDomains[$domain]['title']['en']);
        }

        // Domain-tailored defaults
        $defaultsId = [
            'api_gateway_messaging' => 'SaaS WhatsApp API Gateway & High-Throughput Message Dispatcher',
            'game_3d_simulation' => 'FlightSim 3D - High-Fidelity WebGL Aircraft Flight Simulator',
            'decoupled_saas' => 'Decoupled Enterprise SaaS Platform (Edge Next.js + Headless API Engine)',
            'education_adventist' => 'Sistem Informasi Manajemen SEKOLAH ADVENT (Adventist Education Enterprise SaaS)',
            'fnb_culinary' => 'Platform Katering & Pemesanan Kuliner Harian Terpadu',
            'services_workshop' => 'Sistem Manajemen Reservasi Servis & Operasional Bengkel',
            'property' => 'Platform Katalog Properti & Reservasi Villa Terpadu',
            'logistics' => 'Sistem Manajemen Logistik & Pelacakan Armada Terintegrasi',
            'marketplace' => 'Platform Marketplace & Reservasi Layanan Terpusat',
            'clinic' => 'Sistem Informasi Manajemen Klinik & Rekam Medis Elektronik',
            'finance' => 'Sistem Penagihan Terpadu & Otomatisasi Faktur Komersial',
            'hr' => 'Platform Manajemen Talenta & Rekrutmen Terpadu',
            'education' => 'Portal Pembelajaran & Administrasi Akademik Digital',
            'custom_portal' => 'Platform Operasional & Portal Bisnis Terintegrasi',
        ];

        $defaultsEn = [
            'api_gateway_messaging' => 'Enterprise WhatsApp API Gateway & Multi-Tenant Messaging SaaS',
            'game_3d_simulation' => '3D Aircraft Flight Simulator & Aerial Combat WebGL Game',
            'decoupled_saas' => 'Decoupled Multi-Tier Enterprise Platform (Next.js Edge + Headless API)',
            'education_adventist' => 'Adventist Education Management Information System (Enterprise SaaS)',
            'fnb_culinary' => 'Integrated Catering & Culinary Ordering Platform',
            'services_workshop' => 'Automotive Service & Workshop Management System',
            'property' => 'Real Estate Asset Management & Villa Catalog Portal',
            'logistics' => 'Integrated Fleet Tracking & Logistics Management Engine',
            'marketplace' => 'Centralized Service Marketplace & Booking Platform',
            'clinic' => 'Clinical Information System & Electronic Medical Records',
            'finance' => 'Unified Commercial Invoicing & Financial Operations Hub',
            'hr' => 'Enterprise Talent Acquisition & People Operations Platform',
            'education' => 'Digital Academic Administration & Learning Portal',
            'custom_portal' => 'Centralized Enterprise Business Management Platform',
        ];

        return $isEn ? ($defaultsEn[$domain] ?? $defaultsEn['custom_portal']) : ($defaultsId[$domain] ?? $defaultsId['custom_portal']);
    }

    protected function synthesizeProblem(string $corpus, string $domain, bool $isEn): string
    {
        if ($domain === 'education_adventist') {
            return $isEn
                ? "1. Operational fragmentation across 4 Adventist educational modalities (Day School, Boarding Campuses, Hybrid, and Accredited Institutions) under Conference/Union governance without unified data integration.\n2. Dual accreditation and compliance burdens: Meeting national Ministry of Education standards (Dapodik) alongside denomination-wide AAA accreditation and Encounter Bible Curriculum.\n3. Denominational financial auditing mandates: Incompatibility of commercial school software with SunPlus GCAS chart of accounts and ISAK 35 non-profit compliance.\n4. Character governance & Sabbath enforcement: Vocational student Work-Study tracking for tuition credits, boarding Digital Exeat pass management, and automated Sabbath transactional quietness."
                : "1. Fragmentasi Tata Kelola 4 Moda Sekolah (Day School, Boarding Campuses, Sekolah Hibrida, dan Lembaga Terakreditasi) di bawah naungan Konferens/Uni GMAHK yang masih dikelola terpisah tanpa integrasi data terpusat.\n2. Beban Regulasi & Akreditasi Ganda: Kewajiban mematuhi regulasi nasional Kemendikdasmen (Dapodik, Kurikulum Merdeka) sekaligus standar denominasi global AAA (Adventist Accrediting Association) dan Kurikulum Alkitab Encounter.\n3. Kepatuhan Audit Keuangan Khusus Denominasi: Kebutuhan integrasi buku besar ke standar SunPlus GCAS (General Conference Auditing Service) dan pelaporan nirlaba ISAK 35.\n4. Manajemen Karakter & Siklus Sabat: Pengelolaan jam kerja siswa (Work-Study) sebagai pengurang SPP, sistem perizinan asrama (Digital Exeat Pass), serta otomatisasi sistem hening (Shabbat Mode) pada hari Sabat.";
        }

        // 1. Try extracting explicit conversational pain points (e.g. "selama ini orderan cuma lewat WA dan buku tulis...")
        if (preg_match('/(?:selama\s*ini|kendala(?:nya)?|masalah(?:nya)?|susahnya|sering(?:kali)?|kesulitan)\s*[:=\s,]+([^.\n\r]+(?:\.[^.\n\r]+)?)/iu', $corpus, $pm)) {
            $extractedPain = trim($pm[0]);
            if (mb_strlen($extractedPain) >= 20 && mb_strlen($extractedPain) <= 350) {
                return ucfirst($extractedPain);
            }
        }

        $firstParagraph = trim(preg_split('/\n\s*\n/', $corpus)[0] ?? '');
        if (mb_strlen($firstParagraph) > 40 && mb_strlen($firstParagraph) < 400 && !str_contains($firstParagraph, '###')) {
            return $firstParagraph;
        }

        $problemsId = [
            'bioinformatics_genomics' => "1. Volume Data Raksasa & Latensi Pipeline: File sekuensing FASTQ/BAM/VCF mencapai puluhan gigabyte per sampel, menuntut pipeline komputasi terdistribusi yang efisien tanpa membebani server utama.\n2. Kompleksitas Algoritma Pencarian & Prediksi: Analisis rekombinasi genetik dan desain target CRISPR-Cas9 membutuhkan kalkulasi off-target yang presisi serta pemindaian database genom referensi berkecepatan tinggi.\n3. Fragmentasi Format Data Hayati: Ketidakcocokan format metadata antar lab dan ketiadaan sistem audit jejak komputasi (provenance tracking) yang memenuhi standar reproducibility ilmiah.\n4. Kebutuhan Visualisasi Genomik Interaktif: Peneliti membutuhkan tampilan track genom interaktif di browser tanpa harus menginstal software desktop yang berat.",
            'fintech_ledger_banking' => "1. Integritas Saldo & Risiko Double-Spending: Sistem pencatatan saldo single-entry tradisional rentan terhadap kesalahan mutasi, phantom reads, dan kehilangan audit trail saat lonjakan transaksi konkuren tinggi.\n2. Latensi Rekonsiliasi Multi-Gateway: Proses pencocokan mutasi bank, QRIS, dan switching antar lembaga keuangan membutuhkan waktu berjam-jam jika dilakukan manual.\n3. Kepatuhan Regulasi & Standar Audit Finansial: Tuntutan standar perbankan (ISO 8583/20022, OJK, BI) yang mewajibkan isolasi transaksi serializable ketat dan pembukuan berpasangan (double-entry).\n4. Ketiadaan Idempotency Key Terstandar: Duplikasi pembayaran akibat network retry yang memotong saldo pengguna lebih dari satu kali.",
            'iot_telemetry_robotics' => "1. Lonjakan Throughput Telemetri Sensor: Ribuan perangkat IoT/drone mengirimkan data koordinat, suhu, dan kecepatan setiap 100ms, membebani database relasional biasa.\n2. Ketiadaan Manajemen Antrean Perintah Hardware: Kesulitan mengirim instruksi kendali (commands/tele-operations) ke perangkat yang berada di jaringan seluler tidak stabil tanpa mekanisme antrean persisten.\n3. Latensi Visualisasi Digital Twin 3D: Operator membutuhkan visualisasi posisi armada secara real-time pada peta dan model 3D tanpa freeze UI browser.\n4. Risiko Firmware Corrupt pada Pembaruan OTA: Pembaruan firmware over-the-air (OTA) tanpa validasi checksum SHA-256 dan rollback otomatis dapat membuat hardware bricked permanen.",
            'ai_agent_rag_pipeline' => "1. Halusinasi LLM & Ketiadaan Konteks Domain Khusus: Model AI generik memberikan jawaban tidak akurat karena tidak memiliki akses ke dokumen internal dan SOP perusahaan.\n2. Biaya Token Selangit & Batasan Context Window: Mengirim seluruh dokumen ke dalam prompt menghabiskan kuota token dan memicu degradasi perhatian (attention drift).\n3. Ketiadaan Eksekusi Alat Aman (Tool Calling / MCP): Agen AI kesulitan mengambil tindakan nyata (memanggil API, query database) tanpa sistem sandbox yang terproteksi.\n4. Latensi Streaming Respon: Pengguna menunggu terlalu lama jika respon AI tidak di-stream secara bertahap via Server-Sent Events (SSE).",
            'cybersecurity_siem_zerotrust' => "1. Keterlambatan Deteksi Serangan Siber: Analisis log keamanan terfragmentasi antar server, menyebabkan insiden pembobolan baru disadari setelah berhari-hari.\n2. Volume Log SIEM Masif: Jutaan request HTTP/TCP per menit menuntut filter berkecepatan tinggi agar tidak menenggelamkan peringatan bahaya sesungguhnya (false positive).\n3. Ketiadaan Mitigasi Karantina Otomatis: Waktu tanggap manual oleh tim SOC terlalu lambat untuk menghentikan serangan RCE atau brute-force otomatis.\n4. Kepatuhan Standar Zero-Trust: Ketiadaan verifikasi identitas berkelanjutan (continuous trust validation) dan audit mTLS.",
            'api_gateway_messaging' => "1. Keterbatasan API Resmi & Biaya Percakapan Selangit: Tingginya biaya Meta WhatsApp Cloud API per sesi percakapan untuk use case internal, blasting massal, dan notifikasi transaksional volume tinggi.\n2. Ketidakstabilan Sesi Multi-Device & Browser Crash: Masalah umum pada library OpenWA / Baileys di mana instance Chromium headless mengalami memory leak atau sesi terputus saat traffic lonjak tanpa sistem antrean (BullMQ/Redis).\n3. Risiko Pemblokiran Nomor (Banned) & Ketiadaan Rate Limiting Cerdas: Pengiriman pesan massal tanpa throttling adaptif dan rotasi nomor/proxy menyebabkan akun WhatsApp terblokir oleh algoritma anti-spam Meta.\n4. Ketiadaan Webhook Dispatcher Handal: Kegagalan retry saat server downstream klien offline menyebabkan hilangnya pesan masuk dan callback status pengiriman (sent, delivered, read).",
            'game_3d_simulation' => "1. Keterbatasan Komputasi & Latensi Rendering Web: Kesulitan menjalankan simulasi penerbangan 3D dengan model aerodinamika nyata (lift, drag, thrust, gravity) pada 60 FPS stabil di browser pengguna tanpa instalasi software berat.\n2. Latensi Jaringan Sinkronisasi Multiplayer: Delay dan desinkronisasi posisi pesawat antarpemain pada kecepatan tinggi (Mach 1+) tanpa algoritma client-side prediction, dead reckoning, dan lag compensation.\n3. Manajemen Aset 3D Berat (GLTF/GLB) & Bandwidth: Ukuran file tekstur 4K dan mesh 3D pesawat yang besar memperlambat loading awal (First Contentful Paint) dan menghabiskan memori GPU browser (WebGL Context Loss).\n4. Presisi Kontrol Flight Stick & Sensor: Ketiadaan integrasi responsif terhadap input keyboard, joystick/gamepad HOTAS (Gamepad API), dan sensor akselerometer perangkat mobile.",
            'decoupled_saas' => "1. Keterikatan Monolitik yang Membatasi Skala Frontend: Kecepatan iterasi UI/UX terhambat karena siklus rilis frontend dan backend menyatu, serta tidak dapat memanfaatkan edge caching global (Cloudflare Pages/Vercel).\n2. Latensi Global untuk Pengguna Internasional: Pengguna di berbagai belahan dunia mengalami TTFB lambat jika seluruh rendering SSR dilakukan dari satu server terpusat.\n3. Ambang Beban API yang Tidak Terisolasi: Lonjakan traffic pada halaman publik dapat mengganggu kinerja proses background dan mutasi data database inti jika tidak dipisahkan arsitekturnya.",
            'education_adventist' => "1. Fragmentasi Tata Kelola 4 Moda Sekolah (Day School, Boarding Campuses, Sekolah Hibrida, dan Lembaga Terakreditasi) di bawah naungan Konferens/Uni GMAHK yang masih dikelola terpisah tanpa integrasi data terpusat.\n2. Beban Regulasi & Akreditasi Ganda: Kewajiban mematuhi regulasi nasional Kemendikdasmen (Dapodik, Kurikulum Merdeka) sekaligus standar denominasi global AAA (Adventist Accrediting Association) dan Kurikulum Alkitab Encounter.\n3. Kepatuhan Audit Keuangan Khusus Denominasi: Kebutuhan integrasi buku besar ke standar SunPlus GCAS (General Conference Auditing Service) dan pelaporan nirlaba ISAK 35.\n4. Manajemen Karakter & Siklus Sabat: Pengelolaan jam kerja siswa (Work-Study) sebagai pengurang SPP, sistem perizinan asrama (Digital Exeat Pass), serta otomatisasi sistem hening (Shabbat Mode) pada hari Sabat.",
            'fnb_culinary' => 'Pencatatan pesanan katering dan langganan makanan harian saat ini masih manual via chat WhatsApp dan catatan buku fisik, rentan salah alamat pengiriman, pesanan tercatat ganda, dan rekap porsi dapur berantakan.',
            'services_workshop' => 'Penjadwalan antrean servis dan riwayat perawatan kendaraan pelanggan masih dicatat manual, menyebabkan antrean menumpuk di bengkel, stok suku cadang tidak termonitor akurat, dan riwayat servis kendaraan hilang.',
            'logistics' => 'Pencatatan manifes, status pengiriman armada, dan pelacakan surat jalan saat ini masih manual via spreadsheet dan pesan instan, menyebabkan lambatnya rekonsiliasi dan risiko kehilangan bukti serah terima.',
            'marketplace' => 'Koordinasi transaksi antara penyedia jasa/vendor dan pelanggan masih terfragmentasi tanpa adanya verifikasi ketersediaan armada/stok real-time, kalkulasi tarif transparan, serta sistem penjamin transaksi yang aman.',
            'clinic' => 'Data rekam medis pasien dan riwayat pemeriksaan masih terpisah dalam berkas kertas atau sistem offline, memperlambat proses pendaftaran, rujukan dokter, dan rekonsiliasi stok obat.',
            'finance' => 'Penerbitan faktur dan penagihan piutang pelanggan sering terlambat karena proses validasi manual bertingkat, menyulitkan monitoring arus kas dan penutupan buku bulanan.',
            'hr' => 'Proses seleksi berkas pelamar dan evaluasi kinerja karyawan terhambat karena data tercecer di email, formulir terpisah, dan tidak adanya alur persetujuan (approval) berjenjang.',
            'education' => 'Distribusi materi, pelacakan absensi, dan administrasi nilai siswa sulit dikontrol terpusat oleh manajemen dan guru secara efisien.',
            'property' => 'Pemasaran dan penyewaan properti/villa saat ini masih terfragmentasi via media sosial dan chat instan yang berantakan, menyulitkan calon penyewa/investor memeriksa ketersediaan unit, galeri foto HD, spesifikasi, dan lokasi secara akurat.',
            'custom_portal' => 'Operasional bisnis masih mengandalkan rekap manual yang rentan human-error, data tercecer di berbagai platform, dan manajemen kesulitan mendapatkan visibilitas analitik secara real-time.',
        ];

        $problemsEn = [
            'bioinformatics_genomics' => "1. Massive Sequencing Data Volume & Pipeline Latency: Multi-gigabyte FASTQ/BAM/VCF datasets per sample requiring distributed compute pipelines that avoid choking the main web origin.\n2. Algorithmic Complexity in Off-Target Scoring: CRISPR-Cas9 target design requires exact guide-RNA scoring and high-speed genomic variant lookup across reference genomes.\n3. Biological Metadata Fragmentation: Disparate lab formats and lack of reproducible computational provenance tracking.\n4. Interactive In-Browser Genomic Visualization: Demands smooth, responsive track rendering for chromosome coordinates without heavy native desktop client installations.",
            'fintech_ledger_banking' => "1. Ledger Integrity & Double-Spending Vulnerabilities: Single-entry systems cannot maintain audit integrity or prevent concurrency anomalies during high-volume spikes.\n2. Multi-Channel Settlement Latency: Reconciling bank transfers, QRIS, and payment gateway batches takes hours when processed manually.\n3. Strict Financial Compliance & Auditing: Compliance with ISO 8583/20022 and banking regulations requires strict serializable transaction isolation and double-entry bookkeeping.\n4. Missing Idempotency Standards: Duplicate debits caused by network retries without cryptographic idempotency locks.",
            'iot_telemetry_robotics' => "1. Massive Ingestion Velocity: Thousands of IoT nodes and drones streaming GPS, telemetry, and sensor packets every 100ms, overwhelming conventional relational engines.\n2. Unreliable Hardware Command Queuing: Difficulty delivering remote commands to devices on intermittent connectivity without persistent queuing.\n3. 3D Digital Twin Latency: Operators require real-time 3D spatial fleet tracking without browser frame drops.\n4. Firmware Brick Risks during OTA Updates: Over-the-air updates without cryptographic verification and automated rollback risk bricking remote hardware.",
            'ai_agent_rag_pipeline' => "1. LLM Hallucinations & Domain Blindspots: Generic models provide ungrounded outputs without enterprise knowledge context.\n2. Token Inefficiency & Context Rot: Ingesting entire documents into prompts drives massive token costs and degrades retrieval precision.\n3. Unsandboxed Tool Calling & Agent Drift: Autonomous agents executing external APIs without strict governance and parameter validation.\n4. Slow Response Time to First Token: High latency when completions are not streamed incrementally via Server-Sent Events (SSE).",
            'cybersecurity_siem_zerotrust' => "1. Delayed Cyber Threat Discovery: Siloed logs across servers prevent SOC teams from catching breaches in real time.\n2. Overwhelming SIEM Log Volume: Millions of events per minute swamp analyst queues with noisy false positives.\n3. Lack of Automated Quarantine: Manual mitigation is too slow to stop automated brute-force and RCE payloads.\n4. Zero-Trust Non-Compliance: Missing continuous token verification and end-to-end mTLS encryption.",
            'api_gateway_messaging' => "1. Official API Cost & Limitations: Prohibitive Meta WhatsApp Cloud API conversation-based fees for high-volume broadcast, internal notifications, and transactional dispatch.\n2. Multi-Device Session Instability & Headless Crashes: Frequent Chromium process memory leaks and dropped sessions in OpenWA / Baileys under high concurrency without persistent Redis queues.\n3. Account Ban Risks & Lack of Adaptive Rate Limiting: Unthrottled message blasting triggering Meta spam detection algorithms without randomized jitter delays and proxy rotations.\n4. Flaky Webhook Delivery: Message loss during downstream subscriber downtime due to missing retry mechanisms and idempotency checks.",
            'game_3d_simulation' => "1. Computational & Web Rendering Bottlenecks: Delivering a high-fidelity 3D flight simulation with real aerodynamic formulas (lift, drag, thrust, gravity) maintaining solid 60 FPS in browsers without native downloads.\n2. High-Speed Multiplayer Network Desync: Aircraft positional desynchronization and jitter at high airspeeds without client-side prediction, dead reckoning, and lag compensation.\n3. Heavy 3D Asset & Mesh Bandwidth Overhead: Large 4K textures and GLTF aircraft meshes degrading initial load times and risking WebGL context losses.\n4. Input Control Fidelity & Sensor Precision: Integrating multi-axis flight controls across Keyboard, Gamepad API (HOTAS flight sticks), and mobile touch sensors.",
            'decoupled_saas' => "1. Monolithic Deployment Couplings: Frontend release velocity hindered by unified backend cycles, missing out on global edge distribution (Cloudflare Pages/Vercel).\n2. Geographic Edge Latency: International users experiencing slow TTFB when rendering pages from a single centralized origin server.\n3. Unisolated Public Traffic Spikes: Public marketing traffic surges impacting critical background jobs and database write pipelines.",
            'education_adventist' => "1. Operational fragmentation across 4 Adventist educational modalities (Day School, Boarding Campuses, Hybrid, and Accredited Institutions) under Conference/Union governance without unified data integration.\n2. Dual accreditation and compliance burdens: Meeting national Ministry of Education standards (Dapodik) alongside denomination-wide AAA accreditation and Encounter Bible Curriculum.\n3. Denominational financial auditing mandates: Incompatibility of commercial school software with SunPlus GCAS chart of accounts and ISAK 35 non-profit compliance.\n4. Character governance & Sabbath enforcement: Vocational student Work-Study tracking for tuition credits, boarding Digital Exeat pass management, and automated Sabbath transactional quietness.",
            'fnb_culinary' => 'Catering orders and meal subscriptions are managed manually via chat and physical paper ledgers, causing delivery address mix-ups, duplicate billing, and disorganized kitchen portion batching.',
            'services_workshop' => 'Workshop service appointment queues and vehicle maintenance histories are recorded manually, resulting in physical queue congestion, untracked parts, and missing maintenance logs.',
            'logistics' => 'Manifest logging, shipment status tracking, and delivery receipts are managed manually through disconnected spreadsheets, leading to delayed reconciliation and lost records.',
            'marketplace' => 'Service bookings and vendor interactions are highly fragmented without real-time inventory validation, transparent rate calculation, or centralized payment escrow.',
            'clinic' => 'Patient medical histories and registration queues remain tied to physical folders or siloed offline databases, hindering doctor handoffs and medicine stock tracking.',
            'finance' => 'Commercial billing and account receivables reconciliation suffer from slow multi-tier manual approvals, impacting monthly cash-flow reporting.',
            'hr' => 'Candidate screening and personnel evaluations are hindered by scattered resumes in email inboxes without structured pipeline tracking.',
            'education' => 'Course material distribution, student attendance tracking, and grading lack a centralized platform for faculty and administrators.',
            'property' => 'Property and villa marketing is fragmented across scattered social media feeds and messaging groups, preventing prospective tenants from inspecting live unit availability, HD galleries, and map locations.',
            'custom_portal' => 'Core business operations rely on error-prone manual spreadsheets, resulting in data silos and lack of real-time executive visibility.',
        ];

        return $isEn ? ($problemsEn[$domain] ?? $problemsEn['custom_portal']) : ($problemsId[$domain] ?? $problemsId['custom_portal']);
    }

    protected function synthesizeGoal(string $corpus, string $domain, bool $isEn): string
    {
        // 1. Try extracting conversational desires (e.g. "Saya pengen punya web buat langganan catering...")
        if (preg_match('/(?:saya\s*pengen|kami\s*ingin|tujuan(?:nya)?|pengen\s*punya|rencana\s*mau)\s*[:=\s,]+([^.\n\r]+)/iu', $corpus, $gm)) {
            $wish = trim($gm[1]);
            if (mb_strlen($wish) >= 20 && mb_strlen($wish) <= 300) {
                $parts = preg_split('/,\s*|\s+terus\s+|\s+dan\s+/i', $wish);
                if (count($parts) >= 2) {
                    $numbered = [];
                    foreach (array_slice($parts, 0, 3) as $idx => $part) {
                        $cleanedPart = trim($part);
                        if (!empty($cleanedPart)) {
                            $numbered[] = ($idx + 1) . '. ' . ucfirst($cleanedPart);
                        }
                    }
                    if (count($numbered) >= 2) {
                        return implode("\n", $numbered);
                    }
                }
            }
        }

        $goalsId = [
            'bioinformatics_genomics' => "1. Pipeline Terdistribusi Skalabilitas Tinggi: Membangun pipeline analisis genomik berbasis Worker Pool terdistribusi untuk memproses sampel DNA/RNA secara otomatis.\n2. Desain CRISPR-Cas9 Presisi: Menyediakan modul desain target panduan CRISPR-Cas9 dengan evaluasi efisiensi on-target dan mitigasi off-target secara real-time.\n3. Repositori Varian Strict ULID: Mengintegrasikan repositori anotasi varian genetik dengan skema PostgreSQL Strict ULID dan pencarian keyset O(1).\n4. Visualisasi Genom Interaktif: Menyajikan viewer genom interaktif berbasis web untuk visualisasi varian dan hasil alignment secara visual.",
            'fintech_ledger_banking' => "1. Zero Mathematical Imbalance: Menerapkan engine buku besar berpasangan (Double-Entry Ledger) dengan jaminan zero mathematical imbalance (Total Debit = Total Kredit).\n2. Rekonsiliasi Otomatis Multi-Gateway: Membangun sistem rekonsiliasi otomatis multi-gateway dengan pemrosesan batch O(1) dan pelaporan selisih real-time.\n3. Isolasi Transaksi Tingkat Perbankan: Menerapkan isolasi transaksi tingkat perbankan dengan Idempotency Key unik berbasis Strict ULID untuk mencegah transaksi ganda.\n4. Audit Trail Siap Auditor: Menyediakan dasbor neraca keuangan, mutasi akun, dan audit trail yang siap diperiksa oleh auditor eksternal.",
            'iot_telemetry_robotics' => "1. Ingestion Latensi Ultra-Rendah: Membangun broker telemetri berlatensi ultra-rendah (MQTT/WebSockets) yang mampu menampung 10.000+ packet per detik.\n2. Antrean Perintah Hardware Dua Arah: Menyediakan antrean perintah kendali hardware dengan acknowledgements (ACK) dan retry otomatis saat koneksi terputus.\n3. Kokpit Digital Twin 3D: Menyajikan antarmuka Digital Twin 3D real-time dengan telemetri HUD posisi, orientasi pitch/roll/yaw, dan status baterai.\n4. Manajemen Firmware OTA Aman: Mengelola distribusi firmware OTA terenkripsi dengan verifikasi hash SHA-256 dan pelacakan status armada secara terpusat.",
            'ai_agent_rag_pipeline' => "1. RAG Enterprise HNSW Indexing: Membangun pipeline RAG enterprise dengan chunking semantik, embedding terindeks pgvector (HNSW), dan pencarian hybrid.\n2. Tool Calling Sandboxed (MCP): Mengintegrasikan AI Agent Orchestrator dengan kapabilitas tool calling terisolasi (Model Context Protocol / MCP).\n3. Streaming Real-Time (SSE): Menyediakan antarmuka chat interaktif dengan streaming respons real-time (SSE) dan sitasi sumber dokumen yang transparan.\n4. Audit Log Token & Akurasi: Menghadirkan dasbor audit log penggunaan token, latensi per query, dan evaluasi akurasi ground truth.",
            'cybersecurity_siem_zerotrust' => "1. Deteksi Ancaman Sub-Detik: Mengidentifikasi anomali trafik, brute force, dan payload RCE dalam hitungan milidetik menggunakan stream analytics.\n2. Karantina Host & IP Otomatis: Mengisolasi alamat IP penyerang secara otomatis ke tabel karantina terpusat tanpa menunggu intervensi manual.\n3. Arsitektur Zero-Trust & mTLS: Menerapkan verifikasi token kriptografis di setiap endpoint dan inspeksi payload berbasis AI-ThreatShield.\n4. Forensik SIEM Terpusat: Menyediakan dasbor audit trail forensik dengan pencarian keyset O(1) yang tahan manipulasi.",
            'api_gateway_messaging' => "1. Throughput & Skalabilitas Tinggi: Mampu memproses 500+ pesan per detik (TPS) dengan latensi pengiriman rata-rata < 1,5 detik menggunakan arsitektur Worker Pool & Redis Streams.\n2. Session Multi-Tenant Berisolasi: Mengisolasi sesi WhatsApp per akun/device dalam container Chromium sandboxed dengan konsumsi RAM terkontrol (<350MB per sesi aktif).\n3. Rate Limiter Adaptif & Anti-Ban Intelligence: Algoritma token-bucket adaptif dengan jeda dinamis (jitter) dan rotasi identitas perangkat untuk memastikan tingkat keberhasilan pengiriman >99,5% tanpa terblokir.\n4. SLA Uptime 99,99% & Webhook Idempoten: Webhook dispatcher dengan exponential backoff retry (hingga 5 kali) dan idempotency key untuk menjamin zero message loss.",
            'game_3d_simulation' => "1. Rendering 60 FPS Stabil & Realisme Grafis: Menghasilkan visual penerbangan 3D interaktif pada 60 FPS stabil menggunakan Three.js/WebGL dengan pencahayaan PBR dan volumetric clouds.\n2. Model Fisika Aerodinamika Presisi: Simulasi kalkulasi 6-DOF (Six Degrees of Freedom: Pitch, Roll, Yaw, Lift, Drag, Thrust, Stall, Gravitasi) dengan respon kemudi realistis.\n3. Sub-50ms Multiplayer Synchronization: Sinkronisasi posisi dan orientasi pesawat antarpemain dengan WebSockets + Dead Reckoning pada latensi jaringan <50ms.\n4. Asset Streaming Cepat & Web Vitals: Waktu muat awal 3D asset <3 detik melalui kompresi Draco/Meshopt dan edge CDN caching.",
            'decoupled_saas' => "1. Kecepatan Global Sub-20ms TTFB: Mengoptimalkan rendering halaman publik via Cloudflare Pages / Vercel Edge SSR untuk performa SEO Web Vitals sempurna.\n2. Isolasi Beban Backend & Keamanan: Memisahkan traffic publik dari Core API Engine dan Background Worker sehingga lonjakan visitor tidak mempengaruhi stabilitas transaksi.\n3. Modularitas Rilis Independen: Tim frontend dan backend dapat mendeploy pembaruan secara mandiri melalui kontrak OpenAPI 3.1 yang divalidasi secara otomatis.",
            'education_adventist' => "1. Sentralisasi Tata Kelola Multi-Tenant: Mengintegrasikan seluruh jenjang pendidikan (TK, SD, SMP, SMA/SMK) di wilayah Uni (UIKB/UIKT) dalam satu cloud modern monolith terproteksi.\n2. Smart Billing & Integrasi SunPlus: Otomatisasi penagihan SPP, pemotongan kredit Work-Study siswa asrama, rekonsiliasi pembayaran online VA/QRIS, dan ekspor jurnal akuntansi SunPlus & ISAK 35.\n3. Pembelajaran Holistik Tiga Segi (Kepala, Hati, Tangan): LMS berstandar Kurikulum Merdeka & IFL (Integrasi Iman dan Pembelajaran), penilaian spiritual journey, dan bank soal CBT terenkripsi.\n4. Shabbat Automation Gate: Otomatisasi penguncian transaksi finansial dan asesmen akademik mulai terbenam matahari hari Jumat hingga matahari terbenam hari Sabtu.",
            'fnb_culinary' => "1. Digitalisasi alur pemesanan dan paket langganan katering harian/mingguan hingga 100% tersistem.\n2. Otomatisasi rekap porsi dapur dan pembuatan nota pesanan digital seketika.\n3. Integrasi notifikasi WhatsApp rincian pesanan dan rute antar langsung ke kurir katering.",
            'services_workshop' => "1. Mengurangi antrean fisik di bengkel dengan sistem booking reservasi servis online berjadwal.\n2. Sentralisasi buku riwayat servis (service logbook) dan estimasi biaya transparan bagi pelanggan.\n3. Otomatisasi pengingat servis berkala (service reminder) via pesan WhatsApp.",
            'logistics' => '1. Otomatisasi pencatatan resi dan pelacakan armada hingga 100% digital.\n2. Waktu rekonsiliasi laporan pengiriman dipangkas dari 3 hari menjadi real-time.\n3. Akses visibilitas langsung bagi pelanggan untuk memeriksa posisi kiriman.',
            'marketplace' => '1. Mengintegrasikan proses booking, verifikasi armada/jasa, dan pembayaran dalam 1 portal terpusat.\n2. Mengurangi waktu tunggu konfirmasi pesanan hingga di bawah 15 menit.\n3. Menjamin transparansi transaksi dengan faktur digital dan histori transaksi lengkap.',
            'clinic' => '1. Digitalisasi 100% rekam medis dan data pemeriksaan pasien sesuai standar kepatuhan medis.\n2. Mengurangi waktu antrean pendaftaran hingga 70% melalui booking online.\n3. Otomatisasi mutasi stok obat dan laporan keuangan klinik harian.',
            'finance' => '1. Mengotomatiskan siklus penerbitan invoice dan pengingat jatuh tempo via WhatsApp & Email.\n2. Mempercepat rekonsiliasi piutang hingga 80% dengan integrasi payment gateway.\n3. Menyediakan dasbor laporan arus kas real-time yang siap diaudit.',
            'hr' => '1. Sentralisasi pendaftaran dan kurasi profil kandidat ke dalam basis data terstruktur.\n2. Mempercepat tahapan screening dan penugasan interview hingga 50%.\n3. Otomatisasi rekap evaluasi dan riwayat kepegawaian dalam format PDF resmi.',
            'education' => '1. Sentralisasi materi ajar, bank soal, dan rekaman evaluasi dalam satu portal terproteksi.\n2. Efisiensi absensi dan pelaporan akademik berkala kepada wali murid.',
            'property' => "1. Digitalisasi katalog unit villa/properti dengan galeri foto HD dan filter lokasi/fasilitas yang cepat dibuka di HP.\n2. Mempercepat proses inquiry dan jadwal survei calon penyewa langsung via direct chat WhatsApp.\n3. Dasbor admin terpadu untuk update ketersediaan unit, harga sewa/beli, dan foto secara real-time.",
            'custom_portal' => '1. Menghilangkan proses rekapitulasi data manual dan duplikasi input.\n2. Menjamin integritas data transaksi dengan sistem verifikasi berjenjang.\n3. Menghasilkan laporan analitik eksekutif otomatis format PDF dan Excel setiap hari.',
        ];

        $goalsEn = [
            'bioinformatics_genomics' => "1. Distributed High-Scale Pipeline: Construct a distributed worker pool genomics pipeline for automated DNA/RNA sample processing.\n2. Precise CRISPR-Cas9 Target Design: Deliver real-time CRISPR-Cas9 guide RNA design with on-target efficiency and off-target risk scoring.\n3. Strict ULID Genetic Variant Repository: Integrate a structured genetic variant repository powered by PostgreSQL Strict ULID with O(1) keyset lookup.\n4. Interactive In-Browser Visualizer: Provide an interactive web-based genome track visualizer for effortless alignment inspection.",
            'fintech_ledger_banking' => "1. Absolute Zero Imbalance: Implement a double-entry ledger engine guaranteeing absolute zero mathematical imbalance (Debit = Credit).\n2. Automated Multi-Gateway Reconciliation: Build automated multi-gateway batch reconciliation with real-time discrepancy detection.\n3. Banking-Grade Isolation & Idempotency: Enforce banking-grade serializable transaction isolation with unique ULID idempotency keys.\n4. Audit-Ready Dashboards: Provide a real-time trial balance dashboard, account statements, and external audit trails.",
            'iot_telemetry_robotics' => "1. Ultra-Low Latency Ingestion: Deploy an ultra-low-latency telemetry broker (MQTT/WebSockets) capable of handling 10,000+ packets/sec.\n2. Persistent Hardware Command Queues: Provide persistent hardware command queues with two-way ACK validation and offline retries.\n3. 3D Digital Twin HUD: Deliver a 3D Digital Twin cockpit with real-time HUD metrics (pitch/roll/yaw, battery, signal).\n4. Cryptographic OTA Firmware Releases: Orchestrate cryptographic OTA firmware releases with automated validation and fleet-wide status tracking.",
            'ai_agent_rag_pipeline' => "1. Enterprise RAG & HNSW Indexing: Build an enterprise RAG pipeline with semantic chunking, pgvector (HNSW) indexing, and hybrid retrieval.\n2. Sandboxed Tool Execution (MCP): Integrate an AI Agent Orchestrator with sandboxed Model Context Protocol (MCP) tool execution.\n3. Real-Time Streaming Completions (SSE): Provide a real-time SSE streaming conversational interface with transparent source citations.\n4. Token Economics & Accuracy Auditing: Deliver an audit cockpit tracking token economics, model latency, and retrieval accuracy metrics.",
            'cybersecurity_siem_zerotrust' => "1. Sub-Second Threat Interception: Detect anomalous traffic, brute force, and RCE payloads in milliseconds using streaming telemetry.\n2. Automated Threat Host Quarantine: Automatically isolate attacking IPs into a centralized blacklist without waiting for human intervention.\n3. Zero-Trust mTLS Architecture: Enforce continuous cryptographic identity validation across every endpoint with AI-ThreatShield payload inspections.\n4. Tamper-Proof Forensic Logs: Provide forensic audit trail dashboards with O(1) keyset queries resistant to tampering.",
            'api_gateway_messaging' => "1. High Throughput & Scalability: Capability to process 500+ TPS with average latency < 1.5s via Worker Pool & Redis Streams architecture.\n2. Sandboxed Multi-Tenant Sessions: Isolated Chromium browser contexts per active device with strict RAM capping (<350MB per active session).\n3. Adaptive Rate Limiting & Anti-Ban Intelligence: Token-bucket throttling with dynamic jitter intervals and fingerprint randomization guaranteeing >99.5% delivery success without bans.\n4. 99.99% Uptime & Idempotent Webhooks: Webhook delivery engine with exponential backoff (up to 5 retries) and unique idempotency keys ensuring zero message loss.",
            'game_3d_simulation' => "1. 60 FPS Visual Fidelity: Deliver silky smooth 60 FPS 3D interactive graphics using Three.js/WebGL with PBR lighting and volumetric cloud shaders.\n2. Comprehensive 6-DOF Flight Physics: Realistic aerodynamics simulation (Pitch, Roll, Yaw, Lift, Drag, Thrust, Stall, G-Forces) with authentic handling feedback.\n3. Sub-50ms Multiplayer Synchronization: High-speed multiplayer synchronization via WebSockets + Dead Reckoning maintaining client positions under 50ms latency.\n4. Fast Asset Streaming & Optimized Web Vitals: Initial 3D bundle load times under 3 seconds through Draco/Meshopt mesh compression and global edge CDN caching.",
            'decoupled_saas' => "1. Sub-20ms Global TTFB: Maximize public web speed via Cloudflare Pages / Vercel Edge SSR for flawless Core Web Vitals and SEO rankings.\n2. Backend Isolation & Security: Fully isolate public consumer web traffic from the Core API Engine and async workers so traffic spikes never compromise transactional integrity.\n3. Independent Modular Releases: Enable frontend and backend engineering teams to deploy autonomously against strictly validated OpenAPI 3.1 contracts.",
            'education_adventist' => "1. Multi-Tenant Centralized Governance: Unify all educational levels across Union territories (UIKB/UIKT) within a single hardened cloud monolith.\n2. Smart Billing & SunPlus Integration: Automated tuition invoices, boarding student Work-Study credit deductions, online VA/QRIS reconciliation, and automated export of SunPlus & ISAK 35 accounting ledgers.\n3. Holistic Head-Heart-Hand Learning: Curriculum LMS integrated with Faith-Learning Integration (IFL), spiritual journey portfolios, and encrypted CBT examinations.\n4. Shabbat Automation Gate: Automated quiet-mode locking financial transactions and academic submissions from Friday sunset through Saturday sunset.",
            'fnb_culinary' => "1. 100% digitization of meal subscription workflows and scheduled daily orders.\n2. Automated kitchen batch portioning and instant digital order receipts.\n3. Automated WhatsApp dispatch integration alerting delivery drivers and customers.",
            'services_workshop' => "1. Reduce physical workshop waiting congestion with scheduled online service reservations.\n2. Centralized digital service passport and transparent repair estimates for vehicle owners.\n3. Automated proactive service maintenance reminders via WhatsApp.",
            'logistics' => '1. 100% digitization of manifests and automated dispatch logging.\n2. Reduction of delivery audit time from 3 days to real-time.\n3. Direct live tracking visibility for enterprise clients.',
            'marketplace' => '1. Streamlined service booking, vendor verification, and payment gateway escrow in one hub.\n2. Lower confirmation turnaround to under 15 minutes.\n3. Complete transaction auditing with automated digital receipts.',
            'clinic' => '1. Complete digitization of medical records and patient histories compliant with health regulations.\n2. 70% reduction in patient check-in waiting times via self-service intake.\n3. Automated pharmacy inventory tracking and daily revenue analytics.',
            'finance' => '1. Automated invoice generation and automated payment reminders via WhatsApp & Email.\n2. 80% faster accounts receivable reconciliation via instant payment webhooks.\n3. Real-time cash flow executive dashboard ready for financial audits.',
            'hr' => '1. Centralization of candidate submissions into a searchable talent repository.\n2. 50% faster recruitment pipeline turnaround.\n3. Automated personnel evaluation reporting in exportable PDF formats.',
            'education' => '1. Unified repository for learning resources, syllabus tracking, and student assessments.\n2. Automated attendance and academic progress reporting.',
            'property' => "1. High-speed digital showcase for villa and property listings with HD photos and mobile filters.\n2. Accelerate prospective tenant inquiry turnaround with direct WhatsApp survey scheduling.\n3. Unified administrator control panel to manage unit pricing, photos, and live occupancy status.",
            'custom_portal' => '1. Eliminate error-prone manual spreadsheets and redundant data entry.\n2. Guarantee transactional integrity with multi-tier validation workflows.\n3. Automated generation of executive analytics reports in PDF and Excel formats daily.',
        ];

        return $isEn ? ($goalsEn[$domain] ?? $goalsEn['custom_portal']) : ($goalsId[$domain] ?? $goalsId['custom_portal']);
    }

    protected function synthesizeAudience(string $corpus, string $domain, bool $isEn): string
    {
        $audiencesId = [
            'bioinformatics_genomics' => 'Peneliti genomika, bioinformatikawan, ahli biologi molekuler, teknisi laboratorium sekuensing, dan analis rekayasa genetika.',
            'fintech_ledger_banking' => 'Lembaga keuangan, perbankan digital, akuntan bersertifikasi, auditor keuangan eksternal, dan entitas pengelola dana amanah (escrow).',
            'iot_telemetry_robotics' => 'Operator armada armada drone/robotik, teknisi sensor IoT, manajer logistik industri, dan insinyur embedded systems.',
            'ai_agent_rag_pipeline' => 'Pengembang aplikasi AI, enterprise knowledge workers, tim operasional bisnis, dan analis data korporat.',
            'cybersecurity_siem_zerotrust' => 'Security Operations Center (SOC) analysts, chief information security officers (CISO), penetration testers, and network defense engineers.',
            'education_adventist' => 'Siswa/i Advent & umum, orang tua/wali murid, guru berlisensi denominasi, pembina asrama (deans), staf tata usaha & kasir, pendeta sekolah & jemaat lokal, pengurus majelis sekolah, pimpinan Departemen Pendidikan Uni/Konferens GMAHK, serta auditor GCAS & asesor akreditasi AAA.',
            'api_gateway_messaging' => 'Developer perangkat lunak, penyedia solusi SaaS, pengelola bisnis e-commerce/retail, admin tim customer support, dan manajer DevOps/infrastruktur integrasi API WhatsApp.',
            'game_3d_simulation' => 'Pemain game simulasi penerbangan, penggemar aviasi & 3D WebGL game, pengembang game indie, kompetitor leaderboard global, dan admin turnamen komunitas.',
            'decoupled_saas' => 'Pengguna frontend web & mobile, developer integrasi pihak ketiga (API consumers), product manager, tim operasional bisnis, dan system administrator.',
            'fnb_culinary' => 'Pelanggan individu, karyawan perkantoran (B2B catering), tim operasional dapur, dan kurir pengantaran.',
            'services_workshop' => 'Pemilik kendaraan bermotor, service advisor, mekanik teknisi, dan manajer operasional bengkel.',
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
            'bioinformatics_genomics' => 'Genomics researchers, bioinformaticians, molecular biologists, core sequencing technicians, and genetic engineering teams.',
            'fintech_ledger_banking' => 'Financial institutions, digital banking operators, certified public accountants, external financial auditors, and escrow managers.',
            'iot_telemetry_robotics' => 'Robotics and drone fleet operators, IoT sensor technicians, industrial logistics managers, and embedded firmware engineers.',
            'ai_agent_rag_pipeline' => 'AI engineers, enterprise knowledge managers, customer workflow architects, and corporate analytics teams.',
            'cybersecurity_siem_zerotrust' => 'SOC analysts, CISOs, penetration testers, vulnerability researchers, and network defense architects.',
            'education_adventist' => 'Adventist & general students, parents/guardians, denomination-credentialed faculty, boarding deans, bursars & registrars, school chaplains & local pastors, school board members, Conference/Union Education Directors, GCAS financial auditors, and AAA accreditation evaluators.',
            'api_gateway_messaging' => 'Software engineers, SaaS product builders, e-commerce & retail marketing automation teams, customer service supervisors, and DevOps messaging infrastructure architects.',
            'game_3d_simulation' => 'Flight simulation enthusiasts, browser-based 3D WebGL gamers, indie game developers, competitive leaderboard challengers, and aviation community tournament hosts.',
            'decoupled_saas' => 'Modern web & mobile end-users, third-party API integration developers, product managers, business operations teams, and system administrators.',
            'fnb_culinary' => 'Individual meal subscribers, corporate office workers (B2B catering), kitchen culinary staff, and delivery couriers.',
            'services_workshop' => 'Vehicle owners, workshop service advisors, certified mechanics, and operations managers.',
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
            'bioinformatics_genomics' => "1. Principal Investigator (PI) / Lab Director: Otorisasi proyek riset, persetujuan protokol etik, dan review analisis sekuensing final.\n2. Bioinformatician / Data Scientist: Menjalankan pipeline sekuensing, menyetel parameter filter varian, dan mendesain target sgRNA CRISPR.\n3. Lab Technician: Mengunggah file mentah FASTQ/BAM dari mesin sekuenser dan memverifikasi kualitas sampel.\n4. Clinical Geneticist: Meninjau anotasi varian patogenik dan menerbitkan ringkasan temuan biologis.",
            'fintech_ledger_banking' => "1. Chief Financial Officer (CFO) / Controller: Pengawasan neraca saldo, otorisasi penyesuaian buku besar, dan persetujuan rekonsiliasi akhir bulan.\n2. Treasury & Settlement Officer: Memantau likuiditas multi-akun, batch settlement payment gateway, dan investigasi selisih dana.\n3. Compliance & Risk Auditor: Akses audit trail forensik read-only dan verifikasi kepatuhan regulasi perbankan.\n4. Programmatic Banking Consumer (API): Mesin klien otomatis yang memicu transfer atau posting transaksi dengan idempotency key.",
            'iot_telemetry_robotics' => "1. Fleet Operations Controller: Memantau posisi armada real-time pada peta 3D Digital Twin dan memicu perintah darurat.\n2. Hardware Engineer: Registrasi serial perangkat IoT, kalibrasi sensor, dan pemantauan level baterai/sinyal.\n3. Field Technician: Penanganan fisik unit di lapangan dan verifikasi checklist pra-penerbangan / instalasi.\n4. Automated Edge Device: Perangkat keras yang mengirimkan paket telemetri dan memproses perintah remote.",
            'ai_agent_rag_pipeline' => "1. AI System Architect: Manajemen model LLM, konfigurasi embedding, penyetelan chunking, dan registrasi tools MCP.\n2. Knowledge Base Curator: Mengunggah dokumen referensi, memverifikasi hasil ekstraksi, dan menandai konten usang.\n3. End-User (Chat Consumer): Berinteraksi dengan antarmuka chat cerdas dan mendapatkan jawaban berdasar konteks transparan.\n4. Compliance Auditor: Memantau log percakapan, mengevaluasi akurasi jawaban, dan memeriksa insiden bias/halusinasi.",
            'cybersecurity_siem_zerotrust' => "1. SOC Lead Analyst: Triase insiden keamanan, eskalasi peringatan kritis, dan investigasi forensik serangan.\n2. Security Engineer: Konfigurasi aturan WAF/ThreatShield, whitelist IP CIDR, dan audit sertifikat mTLS.\n3. Network Administrator: Eksekusi pemblokiran firewall dan isolasi subnet host yang terinfeksi.\n4. Automated Threat Sentinel: Mesin pencegat real-time yang memblokir serangan dan mencatat bukti ke tabel karantina.",
            'education_adventist' => "1. Direktur Pendidikan Uni / Divisi (Super-Tenant UIKB/UIKT/SSD): Monitoring executive dashboard lintas konferens, master curriculum repository, dan approval akreditasi AAA.\n2. Direktur Pendidikan Konferens/Daerah & Pengurus Yayasan (Regional Tenant): Parameter skala upah denominasi, mutasi guru antar-sekolah, dan pengawasan laporan keuangan konsolidasi SunPlus & ISAK 35.\n3. Majelis Sekolah (School Board - Unit Sekolah Governance): Pengawasan anggaran operasional (OpEx), laporan tunggakan SPP, dan persetujuan penerimaan beasiswa internal.\n4. Kepala Sekolah & Wakil Kepala (Manajemen Eksekutif): Kalender akademik, rombel, supervisi klinis RPP berbasis IFL, dan pengesahan rapor digital & sertifikat kelulusan.\n5. Admin Sekolah & Operator Dapodik (Operasional): Data induk siswa & pegawai (NISN, NIK, KK, NUPTK), Dapodik Sync Middleware, dan inventaris sarpras.\n6. Bendahara Sekolah (School Treasurer) & Kasir (Keuangan): Tarif SPP, skema cicilan/beasiswa/work-study, petty cash, payroll, dan ekspor jurnal SunPlus GC & ISAK 35.\n7. Education Consultant / Tim Admisi PPDB (Penerimaan Siswa): Admission CRM Funnel, konsultasi pemilihan jalur (Day School/Boarding), verifikasi berkas, dan tes penempatan.\n8. Guru / Fasilitator Kelas & Pembina Ekstrakurikuler (Akademik): Modul ajar LMS dengan Integrasi Iman & Pembelajaran (IFL), presensi harian, penilaian Tiga Segi (Kepala, Hati, Tangan), dan ujian CBT.\n9. Pendeta Sekolah (School Chaplain) & Guru BK (Pastoral & Konseling): Spiritual Journey Ledger (Care Groups, baptisan, Pekan Doa), Confidential Counseling Vault, dan Restorative Discipline Tracker.\n10. Pamong Asrama / Deans (Dean of Men & Dean of Women): Penempatan kamar/loker, verifikasi Digital Exeat Pass (2FA), presensi ibadah pagi/petang (Vespers), dan inspeksi kebersihan kamar.\n11. Supervisor / Mandor Kerja (Work-Study Supervisor): Penugasan unit kerja kampus (pertanian, bakery, kafetaria, pertamanan), validasi timesheet presensi kerja tangan pengurang SPP.\n12. Petugas Medis UKS / Perawat Klinik & Manajer Kafetaria (Kesehatan & Gizi): Rekam medis elektronik, rujukan Rumah Sakit Advent, dan menu gizi vegetarian NEWSTART.\n13. Petugas Keamanan (Satpam Gerbang) & Kru Bus Sekolah (Keamanan & Armada): Pemindaian QR Code Exeat Pass, One-Time Token penjemput harian, dan GPS pelacak rute bus sekolah.\n14. Pendeta Jemaat Lokal & Pemimpin Pathfinder (Ekosistem Gereja Pendukung): Verifikasi keanggotaan jemaat aktif (Constituent Discount) dan pengujian lencana kepakaran kepanduan (Honor Badges).\n15. Orang Tua / Wali & Murid / Siswa (Parent & Student Portal): Pemantauan akademik & karakter real-time, persetujuan izin pesiar asrama (2FA), pembayaran SPP, dan akses materi LMS & CBT.",
            'api_gateway_messaging' => "1. Superadmin (Platform Master): Manajemen multi-tenant, kuota pengiriman pesan, pemantauan status worker pool, dan rotasi master API key.\n2. Tenant / Developer (API Consumer): Pembuatan token API, konfigurasi webhook URL & secret HMAC, pemindaian QR code sesi WhatsApp, dan pemantauan delivery log.\n3. Support Agent / CS Dispatcher: Mengirim pesan blast broadcast, merespons pesan masuk via shared inbox, dan mengelola kontak kontak pelanggan.\n4. Automated Consumer System (Client App / Backend): Melakukan pemanggilan RESTful API endpoint untuk mengirim OTP, notifikasi tagihan, atau media dokumen secara programmatic.",
            'game_3d_simulation' => "1. Player / Pilot (End-User): Mengendalikan pesawat dengan kontrol 6-DOF (pitch, roll, yaw, throttle), menyelesaikan misi penerbangan, dan mencetak skor kecepatan/manuver.\n2. Competitive Challenger: Memantau leaderboard global, menonton replay penerbangan, dan menantang rekor pemain lain.\n3. Superadmin / Game Master: Manajemen parameter aerodinamika pesawat, kurasi medan peta 3D, pengelolaan event misi musiman, dan moderasi leaderboard anti-cheat.\n4. Spectator / Guest: Menonton simulasi penerbangan langsung via camera orbital 3D tanpa perlu login.",
            'decoupled_saas' => "1. Superadmin (Tenant Master): Mengelola konfigurasi headless backend, hak akses RBAC granular, dan audit trail keamanan.\n2. Frontend End-User: Berinteraksi dengan antarmuka SPA modern (Vite/Next.js), melakukan transaksi, dan melihat data real-time via REST/GraphQL API.\n3. API Developer / Integrator: Memanfaatkan dokumentasi OpenAPI/Swagger publik, sandbox testing, dan mengintegrasikan webhook event bisnis.\n4. Operations Specialist: Mengelola data bisnis harian melalui antarmuka dasbor headless yang terisolasi.",
            'fnb_culinary' => "1. Superadmin (Pemilik Katering / Resto): Kontrol penuh master menu, paket langganan, dan rekap penjualan.\n2. Tim Dapur (Chef / Kitchen Head): Memantau daftar pesanan siap masak dan rekap porsi harian.\n3. Kurir Pengantaran: Menerima rincian alamat antar dan nomor kontak pemesan via WhatsApp.\n4. Pelanggan (Individu / Perkantoran): Memilih menu mingguan, langganan harian, dan menerima nota otomatis.",
            'services_workshop' => "1. Superadmin (Owner / Kepala Bengkel): Manajemen master layanan jasa, tarif servis, dan stok sparepart.\n2. Service Advisor / Front Desk: Pendaftaran antrean servis, input Work Order (WO), dan verifikasi keluhan.\n3. Mekanik / Teknisi: Memperbarui progres servis dan mencatat suku cadang yang digunakan.\n4. Pelanggan: Booking jadwal servis online, pantau status pengerjaan, dan cek riwayat servis kendaraan.",
            'logistics' => "1. Superadmin: Mengendalikan master data armada, tarif wilayah, dan hak akses staf.\n2. Staff Dispatcher: Menginput manifes, menugaskan pengemudi, dan memverifikasi status jalan.\n3. Driver / Vendor: Memperbarui titik lokasi, unggah foto bukti serah terima (POD).\n4. Klien / Customer: Memantau status pengiriman live dan mengunduh invoice/e-POD.",
            'marketplace' => "1. Superadmin: Validasi identitas vendor, audit transaksi pembayaran, dan manajemen sistem.\n2. Vendor / Mitra: Mengelola katalog ketersediaan, menerima pesanan sewa, dan konfirmasi unit.\n3. Customer / Klien: Mencari unit/layanan, reservasi jadwal, dan melakukan pembayaran aman.\n4. Finance Officer: Rekonsiliasi pembayaran bertahap dan pencairan dana ke vendor mitra.",
            'clinic' => "1. Superadmin: Manajemen dokter, tarif tindakan medis, dan konfigurasi sistem.\n2. Resepsionis / Front Desk: Pendaftaran pasien baru, antrean poli, dan cetak kartu rekam medis.\n3. Dokter: Menginput rekam medis elektronik (RME), diagnosis, dan resep digital.\n4. Apoteker: Validasi resep, penyerahan obat, dan rekonsiliasi mutasi stok obat.",
            'finance' => "1. Superadmin: Konfigurasi akun bank, payment gateway, dan audit log keuangan.\n2. Finance Operator: Penerbitan tagihan, kustomisasi termin pembayaran, dan input bukti bayar.\n3. Client Payer: Melihat rincian tagihan, melakukan pembayaran via VA/QRIS, dan unduh faktur.\n4. Accounting Auditor: Unduh laporan rekonsiliasi harian/bulanan format Excel dan PDF.",
            'hr' => "1. Superadmin: Pengaturan struktur organisasi dan otorisasi modul.\n2. HR Recruiter: Publikasi lowongan, seleksi berkas pelamar, dan penjadwalan interview.\n3. Hiring Manager: Memberikan feedback evaluasi kandidat dan approval rekrutmen.\n4. Pelamar: Mengunggah CV/dokumen, mengisi formulir profil, dan memantau status seleksi.",
            'education' => "1. Superadmin: Manajemen tahun ajaran, kurikulum, dan akun pengguna.\n2. Pengajar: Mengunggah materi ajar, membagikan tugas, dan menginput nilai siswa.\n3. Siswa / Peserta: Mengakses modul materi, mengumpulkan tugas, dan melihat kartu hasil studi.",
            'property' => "1. Superadmin (Pengelola Villa / Broker): Kendali penuh master listing villa, galeri foto HD via Curator, update tarif sewa/beli, dan nomor WhatsApp admin.\n2. Pengunjung Web (Calon Penyewa / Investor): Menjelajahi katalog villa, filter fasilitas & lokasi, serta mengajukan jadwal survei via WhatsApp.\n3. Staf Lapangan / Host: Menerima prospek survei fisik unit dan memperbarui status ketersediaan unit.",
            'custom_portal' => "1. Superadmin: Hak akses penuh ke seluruh pengaturan sistem dan audit keamanan.\n2. Operator / Staff: Penginputan dan verifikasi data transaksi harian.\n3. Approver / Supervisor: Otorisasi persetujuan data bertingkat sebelum eksekusi.\n4. Klien / Pengguna Umum: Pengisian formulir terarah dan pelacakan status transaksi mandiri.",
        ];

        $actorsEn = [
            'education_adventist' => "1. Union / Division Education Director (Super-Tenant UIKB/UIKT/SSD): Multi-conference executive governance, master curriculum repository, and AAA accreditation endorsements.\n2. Conference Education Director & Foundation Board (Regional Tenant): Denominational wage scale settings, inter-school faculty transfers, and SunPlus / ISAK 35 financial monitoring.\n3. School Board (Governance): Monthly operational budget oversight, tuition receivable aging reports, and scholarship approvals.\n4. Principal & Vice Principals (Executive Management): Academic calendar, course loads, IFL syllabus clinical supervision, and competency report cards.\n5. Registrar & Dapodik Operator (Operations): Master student/staff records (NISN/NUPTK), Dapodik Sync Middleware, and campus asset inventories.\n6. School Treasurer & Cashier (Finance): Tuition pricing tiers, Work-Study credit deductions, petty cash, payroll batching, and SunPlus GC export.\n7. Admissions & Education Consultants (PPDB): Funnel CRM for prospective parents, Day vs Boarding placement, and entrance assessments.\n8. Credentialed Teachers & Club Mentors (Academics): Faith-Learning Integration (IFL) lesson plans, 3-component grading (Head, Heart, Hand), and CBT exams.\n9. School Chaplain & Guidance Counselors (Pastoral): Spiritual Journey Ledger (Care Groups, baptism classes, Week of Prayer), counseling vault, and restorative discipline.\n10. Boarding Deans (Men & Women Dormitories): Room allocations, Digital Exeat pass 2FA authorizations, morning/evening worship (Vespers) roll call, and white-glove audits.\n11. Work-Study Supervisors (Vocational): Vocational campus work allocations (hydroponics, bakery, cafeteria, campus grounds) and timesheet tuition credit approvals.\n12. Campus Clinic Nurses & Cafeteria Managers (Health & Nutrition): Electronic health records, hospital referrals, and vegetarian NEWSTART meal planning.\n13. Security Officers & Bus Drivers (Security & Transit): Exeat QR gate verification, pickup authorization tokens, and bus RFID GPS tracking.\n14. Local Church Pastors & Pathfinder Directors (Church Ecosystem): Constituent discount verification and Pathfinder honor badge approvals.\n15. Parents & Students (Family Portal): Real-time academic tracking, dormitory exeat approvals (2FA), tuition payments, and student LMS access.",
            'api_gateway_messaging' => "1. Superadmin (Platform Master): Multi-tenant management, throughput rate limits, worker pool telemetry, and master credential rotations.\n2. Tenant / Developer (API Consumer): API key generation, HMAC webhook endpoint configuration, WhatsApp session QR scanning, and delivery log audit.\n3. Customer Support Dispatcher: Broadcast campaign scheduling, live two-way chat handling via shared team inbox, and contact list management.\n4. Programmatic Client Machine: Direct RESTful caller dispatching high-priority transactional OTPs, billing receipts, and rich media documents.",
            'game_3d_simulation' => "1. Flight Pilot (Player): Navigates aircraft with 6-DOF controls (pitch, roll, yaw, thrust), executes aerial waypoints, and logs flight performance scores.\n2. Ranked Competitor: Analyzes global leaderboards, watches telemetry flight replays, and challenges high scores.\n3. Game Master / Admin: Aircraft aerodynamics tuning, 3D terrain heightmap management, seasonal mission creation, and anti-cheat telemetry auditing.\n4. Spectator / Visitor: Observes live aircraft maneuvers via WebGL orbital cinematic camera with zero installation required.",
            'decoupled_saas' => "1. Superadmin (Tenant Master): Headless backend governance, granular RBAC permissions, and security audit log monitoring.\n2. Frontend End-User: Interacts with decoupled SPA/PWA interface (Vite/Next.js), submitting transactions and receiving real-time API updates.\n3. API Integrator / Developer: Consumes public OpenAPI/Swagger specifications, sandbox credentials, and event-driven webhooks.\n4. Operations Specialist: Manages daily domain entities through an isolated headless control dashboard.",
            'fnb_culinary' => "1. Superadmin (Culinary / Catering Owner): Master menu setup, subscription pricing, and revenue audits.\n2. Kitchen Head / Chef: Monitors kitchen batch production queues and daily portion lists.\n3. Delivery Courier: Receives drop-off addresses and recipient contact info via WhatsApp.\n4. Customer (Corporate / Individual): Selects weekly menus, configures meal plans, and receives receipts.",
            'services_workshop' => "1. Superadmin (Workshop Owner / General Manager): Service catalog pricing, labor rates, and spare parts inventory.\n2. Service Advisor: Intake queue registration, Work Order (WO) drafting, and diagnostic symptom verification.\n3. Lead Mechanic: Updates live repair milestones and records installed spare parts.\n4. Vehicle Owner: Online appointment booking, repair status tracker, and digital maintenance logbook.",
            'logistics' => "1. Superadmin: Full control over master fleet catalog, regional rate matrices, and role authorization.\n2. Dispatch Operator: Registers manifests, assigns drivers, and verifies delivery routes.\n3. Driver / Fleet Contractor: Updates transit waypoints, uploads proof of delivery (POD) photo.\n4. Corporate Client: Real-time shipment tracking and automated e-POD / invoice downloads.",
            'marketplace' => "1. Superadmin: Vendor KYC verification, transaction escrow audits, and platform settings.\n2. Vendor Partner: Manages equipment availability, confirms booking requests, and updates terms.\n3. Customer: Searches availability, reserves dates, and completes payment checkout.\n4. Financial Officer: Reconciles escrow releases and vendor settlements.",
            'clinic' => "1. Superadmin: Clinic branches, doctor schedules, and security policy management.\n2. Receptionist: Patient intake, appointment queue management, and card printing.\n3. Physician: Diagnostic inputs, Electronic Medical Records (EMR), and digital prescriptions.\n4. Pharmacist: Prescription validation, medication dispensing, and inventory tracking.",
            'finance' => "1. Superadmin: Bank accounts, payment gateway webhooks, and security audit log.\n2. Finance Operator: Invoice drafting, installment scheduling, and receipt verifications.\n3. Client Payer: Reviews itemized billing, executes payments via VA/QRIS, downloads receipts.\n4. Accounting Auditor: Generates monthly reconciliation reports in Excel and PDF.",
            'hr' => "1. Superadmin: Company roles and access policy control.\n2. HR Recruiter: Job vacancy posting, candidate resume screening, and interview scheduling.\n3. Hiring Manager: Reviews candidates, submits evaluation notes, approves hires.\n4. Candidate: Profile submission, CV upload, and application status tracking.",
            'education' => "1. Superadmin: Academic calendar, user credentials, and curriculum control.\n2. Instructor: Uploads study materials, creates assignments, records student grades.\n3. Student: Reviews course materials, submits homework, checks academic report cards.",
            'property' => "1. Superadmin (Villa Manager / Broker): Full administration over villa inventory, HD media curation via Curator, pricing tiers, and WhatsApp routing.\n2. Web Visitor (Prospective Tenant / Buyer): Explores catalog, applies location/amenity filters, and initiates survey chats via WhatsApp.\n3. Field Host / Staff: Receives direct inquiry appointments and coordinates physical property walkthroughs.",
            'custom_portal' => "1. Superadmin: Full master administration, security audit trails, and configuration.\n2. Operational Staff: Day-to-day transaction input and verification.\n3. Supervisor / Approver: Multi-tier approval workflow authorization.\n4. External Client: Self-service submission and real-time status tracker.",
        ];

        return $isEn ? ($actorsEn[$domain] ?? $actorsEn['custom_portal']) : ($actorsId[$domain] ?? $actorsId['custom_portal']);
    }

    protected function synthesizeMvpFeatures(string $corpus, string $domain, bool $isEn): string
    {
        $featuresId = [
            'bioinformatics_genomics' => "1. Mesin Ingesti Data Sekuensing Genom (FASTA/FASTQ/BAM): Pipeline asinkron berbasis Python/Rust untuk upload batch sampel, pembersihan data mentah, dan validasi checksum SHA-256.\n2. Pencarian Varian Genetik Terindeks Keyset O(1): Query instan jutaan varian kromosom, mutasi patogenik, dan anotasi VCF menggunakan PostgreSQL 16 Strict ULID dan indeks B-Tree.\n3. Perancang Target Panduan CRISPR (sgRNA Design): Algoritma scoring efisiensi on-target dan mitigasi off-target dengan visualisasi posisi PAM.\n4. Antrean Komputasi Terdistribusi (Nextflow & Celery): Orkestrasi job komputasi bioinformatika intensif dengan isolasi worker pool dan laporan telemetri real-time.\n5. Genome Browser Interaktif (IGV.js / WebGL): Visualisasi lintasan kromosom, cakupan pembacaan (depth of coverage), dan variasi struktural langsung di browser.\n6. Vault Kepatuhan HIPAA & Audit Forensik: Manajemen akses ketat data biologis terenkripsi dengan log audit forensik permanen.",
            'fintech_ledger_banking' => "1. Mesin Pembukuan Berpasangan (Double-Entry Ledger): Eksekusi transaksi atomik debit-kredit dengan isolasi Strict Serializable dan zero-loss consistency.\n2. Rekonsiliasi Multi-Gateway & Batch Settlement: Pencocokan otomatis mutasi payment gateway (Midtrans, BI-FAST, QRIS) dengan toleransi selisih nol.\n3. Rekening Koran Keyset Cursor O(1): Penarikan riwayat mutasi jutaan baris tanpa degradasi performa menggunakan kursor stabil Strict ULID.\n4. Manajemen Akun & Chart of Accounts (COA): Hierarki akun finansial berstandar PSAK/IFRS dengan saldo terkunci dan reserved balance.\n5. Audit Vault Kriptografis Finansial: Log mutasi saldo tidak dapat diubah (immutable) dengan hash chaining untuk kepatuhan regulasi OJK & BI.\n6. Idempotency Key Gateway: Pencegahan transaksi ganda (double-spend/double-charge) melalui validasi kunci idempoten seketika.",
            'iot_telemetry_robotics' => "1. Ingesti Telemetri IoT Skala Tinggi: Penyerapan jutaan paket sensor per detik via broker MQTT (EMQX) dan antrean terdistribusi Redis Streams.\n2. Dasbor 3D Digital Twin & HUD Real-time: Visualisasi orientasi armada, telemetri baterai/GPS, dan status sensor di canvas 3D interaktif.\n3. Pemantauan Geofencing & Alert Darurat: Deteksi pelanggaran batas geografis dan anomali sensor dengan notifikasi instan berprioritas tinggi.\n4. Pipeline Pembaruan Firmware OTA (Over-The-Air): Distribusi firmware terenkripsi ke armada perangkat IoT dengan verifikasi checksum dan rollback otomatis.\n5. Analitik Sensor Deret Waktu (Timeseries Analytics): Agregasi metrik suhu, getaran, dan kecepatan dengan retensi data teroptimasi.\n6. Autentikasi Perangkat mTLS & Secure Key Store: Pertukaran sertifikat timbal balik untuk menjamin hanya unit robotik sah yang dapat terhubung.",
            'ai_agent_rag_pipeline' => "1. Mesin Ekstraksi & Chunking Dokumen: Pemrosesan multi-format (PDF, DOCX, CSV) dengan semantic chunking dan pelestarian konteks tabel.\n2. Database Vektor HNSW Berkecepatan Tinggi: Indeks semantik berdimensi tinggi menggunakan PostgreSQL 16 pgvector dengan waktu retrieval < 10ms.\n3. Alur Kerja Agen Otonom (LangGraph / Multi-Agent): Orkestrasi pemecahan tugas kompleks dengan perencanaan mandiri, pemanggilan tools, dan evaluasi hasil.\n4. Streaming Jawaban Real-time (SSE): Pengiriman teks respons token-by-token secara instan melalui Server-Sent Events tanpa lag.\n5. Guardrails Keamanan Prompt & Anti-Halusinasi: Verifikasi sitasi sumber dokumen, pemfilteran prompt injection, dan sensor data sensitif (PII).\n6. Riwayat Percakapan Keyset O(1): Penyimpanan thread sesi multi-turn terisolasi dengan retensi konteks dinamis hemat token.",
            'cybersecurity_siem_zerotrust' => "1. Mesin Pengumpul Log Keamanan Terdistribusi: Penyerapan jutaan log peristiwa keamanan per detik dari firewall, WAF, dan server aplikasi.\n2. Deteksi Ancaman Bertenaga AI & Analisis Anomali: Identifikasi otomatis pola brute force, SQL injection, RCE, dan serangan zero-day.\n3. Karantina IP Otomatis & Isolasi Host: Eksekusi pemblokiran dinamis pada layer WAF dan firewall tanpa jeda waktu saat serangan terdeteksi.\n4. Dasbor Security Operations Center (SOC) Brutalist: Visualisasi real-time matriks ancaman, level keparahan CVE, dan riwayat alamat IP terisolasi.\n5. Log Audit Forensik Tidak Dapat Diubah (Immutable Logs): Pencatatan jejak forensik dengan cryptographic hash chaining untuk kebutuhan pembuktian insiden.\n6. Gerbang Zero-Trust & Autentikasi mTLS: Pemeriksaan identitas dan postur perangkat ketat pada setiap permintaan akses sumber daya.",
            'game_engine_simulation' => "1. Mesin Simulasi 3D WebGL / Three.js 60 FPS: Rendering kokpit dan model 3D pesawat berperforma tinggi dengan pencahayaan dinamis, bayangan real-time, dan shader atmosferik langit.\n2. Model Fisika Aerodinamika 6-DOF Realistis: Simulasi gaya angkat, gaya hambat, daya dorong, gravitasi, serta kontrol kemudi pitch, roll, dan yaw responsif.\n3. Peta Medan Dunia Nyata & Sistem Ketinggian: Penjelajahan wilayah terbang dengan bentang alam 3D, landasan pacu bandara, dan indikator navigasi instrumen kokpit (HUD / Altimeter / Speedometer).\n4. Sistem Misi & Checkpoint Lintasan Udara: Serangkaian misi latihan terbang, manuver cincin udara, dan pendaratan darurat dengan kalkulasi skor waktu.\n5. Leaderboard Global & Verifikasi Anti-Cheat: Penyimpanan skor rekor waktu tertinggi dengan validasi telemetri fisika di sisi server.\n6. Dukungan Input Multi-Perangkat: Kontrol fleksibel menggunakan keyboard/mouse atau joystick gamepad penerbangan standar industri.",
            'education_adventist' => "1. Sakelar Multi-Moda Pendidikan: Pengaturan fleksibel 4 moda operasional (Day School, Boarding Campus, Hibrida, dan Perguruan Tinggi) dengan pemisahan modul otomatis.\n2. LMS Kurikulum Holistik & Encounter Bible: Manajemen pembelajaran berbasis Kurikulum Merdeka yang terintegrasi nilai IFL, evaluasi Tiga Segi (Kepala, Hati, Tangan), dan materi Alkitab Encounter.\n3. Sistem Manajemen Asrama Terpadu (Boarding Vault): Ploting kamar santri, verifikasi izin pesiar (Digital Exeat Pass 2FA oleh orang tua dan dean), presensi ibadah pagi/petang (Vespers), dan audit kebersihan kamar.\n4. Buku Besar Kerja Tangan (Work-Study Ledger): Pencatatan presensi jam kerja vokasi kampus (pertanian, bakery, perpustakaan) yang otomatis terkonversi menjadi kredit pengurang tagihan SPP.\n5. Smart Billing Multi-Channel & Ekspor SunPlus: Penagihan SPP, asrama, dan katering otomatis via Virtual Account & QRIS, dengan ekspor jurnal otomatis ke format SunPlus GC dan ISAK 35.\n6. Shabbat Automation Gate: Modul kepatuhan hari Sabat yang otomatis menonaktifkan transaksi pembayaran, pengiriman tugas, dan ujian daring dari Jumat sore hingga Sabtu petang.",
            'api_gateway_messaging' => "1. Engine Multi-Sesi WhatsApp Terisolasi: Manajemen koneksi WhatsApp terdistribusi (OpenWA / Baileys) dengan auto-restart, isolasi memori per sesi, dan scan QR code dinamis di dasbor.\n2. RESTful API Gateway Standar OpenAPI: Endpoint pengiriman pesan teks, gambar, dokumen PDF, tombol interaktif, dan template broadcast dengan otentikasi Bearer API Key.\n3. Antrean Pesan BullMQ & Redis Worker: Manajemen lonjakan trafik (rate limiting) dengan antrean terdistribusi, retry otomatis eksponensial, dan proteksi anti-ban nomor WhatsApp.\n4. Webhook Dispatcher Cepat & Handshake HMAC: Pengiriman event pesan masuk (incoming message) dan delivery receipt (sent, delivered, read) ke server klien dengan tanda tangan cryptographic HMAC-SHA256.\n5. Dasbor Monitoring & Sesi Pengguna: Dasbor real-time untuk melihat status kesehatan sesi WhatsApp, grafik throughput pesan per detik, dan audit log kegagalan pengiriman.\n6. Shared Team Inbox & Kontak: Antarmuka obrolan langsung untuk tim customer service merespons pesan masuk dari pelanggan secara kolaboratif.",
            'game_3d_simulation' => "1. Mesin Simulasi 3D WebGL / Three.js 60 FPS: Rendering kokpit dan model 3D pesawat berperforma tinggi dengan pencahayaan dinamis, bayangan real-time, dan shader atmosferik langit.\n2. Model Fisika Aerodinamika 6-DOF Realistis: Simulasi gaya angkat (lift), gaya hambat (drag), daya dorong (thrust), gravitasi, serta kontrol kemudi pitch, roll, dan yaw responsif.\n3. Peta Medan Dunia Nyata & Sistem Ketinggian (Terrain Heightmap): Penjelajahan wilayah terbang dengan bentang alam 3D, landasan pacu bandara, dan indikator navigasi instrumen kokpit (HUD / Altimeter / Speedometer).\n4. Sistem Misi & Checkpoint Lintasan Udara: Serangkaian misi latihan terbang, manuver cincin udara (air-rings checkpoint), dan pendaratan darurat dengan kalkulasi skor waktu.\n5. Leaderboard Global & Verifikasi Anti-Cheat: Penyimpanan skor rekor waktu tertinggi dengan validasi telemetri fisika di sisi server untuk mencegah manipulasi client-side.\n6. Dukungan Input Multi-Perangkat (Keyboard, Mouse, & Gamepad API): Kontrol fleksibel menggunakan keyboard/mouse atau joystick gamepad penerbangan standar industri.",
            'decoupled_saas' => "1. Arsitektur Decoupled API-First: Backend RESTful headless terisolasi dengan skema PostgreSQL 16 Strict ULID dan Keyset Cursor Pagination O(1) melayani frontend SPA modern.\n2. Frontend Modern SPA / Next.js / Vite: Antarmuka reaktif ultra-cepat dengan optimasi bundle code-splitting, state management tersinkronisasi, dan transisi fluid tanpa reload.\n3. Autentikasi Stateless JWT / Bearer dengan Refresh Token Rotation: Mekanisme login aman lintas domain dengan proteksi CSRF, rotasi token otomatis, dan blacklist token di Redis.\n4. OpenAPI 3.0 / Swagger Live Documentation: Dokumentasi interaktif otomatis untuk seluruh endpoint REST API lengkap dengan skema request/response JSON yang valid.\n5. Event-Driven Webhook Dispatcher: Notifikasi event asinkron ke layanan pihak ketiga dengan payload terenkripsi dan mekanisme retry bertahap.\n6. Headless Admin Backoffice: Dasbor pengelolaan master data terisolasi untuk staf operasional dengan enkripsi data dan audit trail terperinci.",
            'fnb_culinary' => "1. Autentikasi Modern & Dasbor Pengelola (Filament v5): Manajemen menu mingguan, paket diet/reguler, dan rekapitulasi pesanan harian.\n2. Katalog Menu Mingguan & Kalender Langganan: Tampilan menu bervariasi dengan foto lezat via Curator Picker dan pilihan paket diet vs reguler.\n3. Formulir Pemesanan & Alamat Pengiriman: Input jadwal kirim, alamat kantor/rumah, dan kalkulasi total biaya transparan.\n4. Penerbitan & Pengiriman Nota Otomatis: Nota transaksi instan ber-QR Code dalam format PDF resmi terkirim otomatis.\n5. Integrasi WhatsApp Pesanan & Kurir: Notifikasi konfirmasi pesanan ke pelanggan dan rute penugasan antar ke kurir katering.\n6. Rekapitulasi Porsi Dapur (Kitchen Batching): Dasbor rekap total porsi bahan baku dan menu yang harus dimasak setiap hari.",
            'services_workshop' => "1. Autentikasi Pengguna & Dasbor Bengkel (Filament v5): Pusat kendali Work Order, antrean servis, dan performa teknisi.\n2. Reservasi & Booking Antrean Servis Online: Pelanggan memilih jenis perawatan, plat nomor kendaraan, dan jadwal kedatangan.\n3. Manajemen Perintah Kerja (Digital Work Order): Pencatatan keluhan awal, suku cadang terpakai, dan estimasi waktu selesai.\n4. Buku Riwayat Servis Digital (Service Passport): Catatan histori perawatan kendaraan tersimpan rapi berdasarkan plat nomor.\n5. Faktur Biaya & Estimasi Transparan: Rincian biaya jasa montir dan sparepart tercetak otomatis dalam format PDF resmi.\n6. Integrasi Notifikasi WhatsApp: Update otomatis status servis kendaraan (Diterima -> Dikerjakan -> Selesai Siap Diambil).",
            'logistics' => "1. Autentikasi Pengguna & RBAC: Manajemen akses aman untuk Superadmin, Dispatcher, Vendor, dan Klien dengan PostgreSQL ULID.\n2. Manajemen Master Data: Pengelolaan data armada truk, jenis kontainer, kapasitas muatan, dan tarif wilayah.\n3. Modul Pencatatan Manifes & Resi: Penerbitan nomor surat jalan otomatis dengan QR Code verifikasi.\n4. Pelacakan Status Pengiriman Real-Time: Update status bertahap (Diterima -> Muat -> Dalam Perjalanan -> Terkirim) lengkap dengan bukti foto serah terima (e-POD).\n5. Dasbor Admin Filament v5: Tabel filter data pengiriman dengan pencarian instan, status badge, dan metrik operasional harian.\n6. Ekspor Laporan & Surat Jalan: Cetak otomatis Surat Jalan, Berita Acara, dan rekapitulasi data format PDF dan Excel.",
            'marketplace' => "1. Autentikasi & Verifikasi Akun: Login aman dengan pemisahan peran Pelanggan dan Vendor Mitra.\n2. Manajemen Katalog & Ketersediaan: Input detail layanan/unit sewa dengan galeri foto via Curator Picker dan tarif harian/bulanan.\n3. Alur Reservasi & Booking: Formulir pemilihan tanggal sewa, kalkulasi harga otomatis, dan konfirmasi ketersediaan.\n4. Integrasi Pembayaran Midtrans: Dukungan pembayaran multi-channel (Virtual Account, QRIS, Kartu Kredit) dengan webhook otomatis.\n5. Pusat Kendali Admin (Filament PHP): Audit pesanan masuk, verifikasi berkas legal vendor, dan pemantauan transaksi.\n6. Faktur Digital & Notifikasi: Penerbitan invoice resmi otomatis dan notifikasi status pesanan.",
            'clinic' => "1. Modul Autentikasi & Hak Akses Medis: Akses terisolasi untuk Resepsionis, Dokter, dan Apoteker.\n2. Pendaftaran Pasien & Antrean: Input data pasien dengan nomor rekam medis unik dan antrean digital poli.\n3. Rekam Medis Elektronik (RME): Form pencatatan keluhan, anamnesis, diagnosis standar ICD-10, dan resep obat digital.\n4. Manajemen Stok Apotek: Pencatatan otomatis pengurangan stok saat obat diresepkan serta peringatan stok menipis.\n5. Dasbor Manajemen & Kasir: Perhitungan total billing perawatan obat dan cetak kuitansi pembayaran.\n6. Laporan Medis & Keuangan: Ekspor data kunjungan pasien dan rekapitulasi penjualan farmasi ke PDF/Excel.",
            'finance' => "1. Autentikasi Finansial & Audit Trail: Hak akses ketat untuk Operator Keuangan dan Auditor dengan logging aktivitas.\n2. Pembuat Faktur Komersial: Pembuatan invoice dinamis dengan perhitungan PPN, diskon, dan skema termin bertahap.\n3. Gateway Pembayaran Terotomatisasi: Integrasi Midtrans Virtual Account dan QRIS dengan rekonsiliasi seketika.\n4. Portal Pembayaran Klien: Halaman khusus bagi klien untuk melihat rincian faktur dan melakukan pembayaran langsung.\n5. Dasbor Piutang & Aging Schedule: Pemantauan tagihan belum terbayar, jatuh tempo, dan metrik kas masuk.\n6. Ekspor Rekonsiliasi Akuntansi: Ekspor data jurnal transaksi siap import ke software akuntansi dalam format CSV dan PDF.",
            'property' => "1. Autentikasi Pengguna & Dasbor Pengelola (Filament v5): Manajemen unit villa, upload galeri foto resolusi tinggi via Curator Picker, update tarif sewa/jual, dan status ketersediaan.\n2. Katalog Listing Villa Interaktif: Filter pencarian cepat berdasarkan lokasi di Bali, jumlah kamar, fasilitas unggulan, dan kategori (Sewa Harian/Bulanan/Tahunan atau Beli Hak Milik/Leasehold).\n3. Halaman Detail Villa Responsif: Tampilan galeri foto HD swipeable di HP, spesifikasi lengkap unit, fasilitas, harga transparan, dan sematan peta interaktif (Google Maps Embed).\n4. Direct WhatsApp Inquiry & Booking Lead: Tombol Click-to-Chat WhatsApp otomatis membawa detail nama villa, tanggal estimasi survei/sewa langsung ke broker/owner.\n5. Optimasi Mobile-First & Kecepatan Akses: Desain bersih ultra-cepat dibuka dari browser smartphone dengan O(1) query performa tanpa lag.\n6. Manajemen Pertanyaan & Status Prospek: Pencatatan ringkasan leads masuk dari pengunjung untuk evaluasi efektivitas listing.",
            'custom_portal' => "1. Autentikasi Modern & RBAC: Pengelolaan peran pengguna dengan ULID primary keys untuk skalabilitas jutaan data.\n2. Formulir Intake & Validasi Data: Input data terstruktur dengan validasi ketat dan proteksi Anti-Spam berjenjang.\n3. Dasbor Administrasi Filament v5: Pusat manajemen data dengan filter canggih, metrik analitik, dan tabel dinamis kilat.\n4. Alur Kerja Persetujuan (Workflow): Mekanisme review data bertingkat dengan pencatatan riwayat audit (audit trail).\n5. Sistem Notifikasi Terpadu: Notifikasi status via sistem internal dan template email resmi.\n6. Modul Laporan & Ekspor: Ekspor rekonsiliasi data komprehensif ke format PDF siap cetak dan spreadsheet Excel.",
        ];

        $featuresEn = [
            'bioinformatics_genomics' => "1. Genomic Sequence Ingestion Engine (FASTA/FASTQ/BAM): High-throughput async pipeline for raw sample batch upload, quality trimming, and SHA-256 integrity verification.\n2. Keyset O(1) Genetic Variant Search Engine: Sub-millisecond lookup across millions of chromosome variants and VCF annotations via PostgreSQL 16 Strict ULID B-Tree indices.\n3. CRISPR Guide RNA Target Designer (sgRNA Engine): Algorithmic on-target efficiency and off-target risk scoring with interactive PAM site visualization.\n4. Distributed Compute Queue (Nextflow & Celery): Orchestrated bioinformatic workload execution with isolated compute nodes and live telemetry streaming.\n5. Interactive Genome Browser (IGV.js / WebGL): Seamless in-browser chromosome track visualization, read depth coverage, and structural variant mapping.\n6. HIPAA Compliance Vault & Forensic Audit: Secure role-gated genomic biological data vault with immutable cryptographic audit logging.",
            'fintech_ledger_banking' => "1. Atomic Double-Entry Ledger Core: Strict debit-credit transaction balancing enforced at the database level under Strict Serializable isolation.\n2. Multi-Gateway Automated Reconciliation: High-speed settlement matching across payment switches and banking rails with zero variance tolerance.\n3. Keyset Cursor O(1) Account Statements: Ultra-fast pagination across millions of ledger journal postings using immutable ULID pointers.\n4. Chart of Accounts (COA) & Multi-Currency Engine: Hierarchical accounts complying with PSAK/IFRS standards featuring reserved balance holds.\n5. Immutable Cryptographic Audit Vault: Tamper-evident financial event ledger chained with cryptographic hashes for regulatory compliance.\n6. Idempotent Transaction Gateway: Sub-second deduplication ensuring zero double-billing and zero duplicate postings across distributed nodes.",
            'iot_telemetry_robotics' => "1. High-Throughput Telemetry Ingestion Bus: Ingesting millions of sensor payloads per second via EMQX MQTT broker and Redis Streams.\n2. 3D Digital Twin Cockpit & Real-time HUD: Interactive 3D visualization of fleet orientation, GPS coordinates, and real-time sensor metrics.\n3. Real-Time Geofencing & Anomaly Alerts: Sub-second spatial boundary tracking and automated emergency alerts for anomalous sensor spikes.\n4. Encrypted OTA Firmware Deployment: Secure over-the-air firmware updates with cryptographic checksum verification and automated fallback.\n5. Keyset Timeseries Sensor Analytics: High-density timeseries logging with automated downsampling and O(1) query response times.\n6. mTLS Hardware Device Authentication: Mutual TLS authentication and secure cryptographic key onboarding preventing rogue device injection.",
            'ai_agent_rag_pipeline' => "1. Multi-Format Knowledge Chunking Engine: Deep semantic document parsing (PDF, DOCX, CSV) with structural table and metadata preservation.\n2. High-Performance HNSW Vector Search: Low-latency semantic retrieval powered by PostgreSQL 16 pgvector with sub-10ms query execution.\n3. Autonomous Agentic Workflows (LangGraph): Multi-step cognitive task decomposition, tool sandboxing, and autonomous reflection loops.\n4. Token-by-Token SSE Response Streaming: Real-time interactive response streaming via Server-Sent Events with zero buffering.\n5. Anti-Hallucination & Prompt Guardrails: Grounded source citation enforcement, prompt injection neutralization, and PII masking.\n6. O(1) Keyset Conversation History: Isolated multi-turn conversation threads with token-optimized context compaction.",
            'cybersecurity_siem_zerotrust' => "1. Distributed Security Event Ingestion Engine: High-volume ingestion of system logs, network flows, and WAF telemetry with zero packet drop.\n2. AI-Driven Threat Hunting & Anomaly Detection: Automated pattern matching against known CVE exploits, SQL injections, and behavioral anomalies.\n3. Automated IP Quarantine & Host Containment: Real-time dynamic threat mitigation applying firewall blocking and token revocations instantaneously.\n4. Brutalist Security Operations Center (SOC) Console: Real-time threat matrix visualizing attack vectors, active quarantines, and forensic timelines.\n5. Immutable Cryptographic Forensics Vault: Tamper-proof security audit logging secured by hash chaining for forensic investigations.\n6. Zero-Trust Access Controller & mTLS Verification: Continuous identity verification, token introspection, and device posture compliance enforcement.",
            'game_engine_simulation' => "1. 60 FPS WebGL / Three.js 3D Engine: High-performance browser rendering featuring dynamic atmospheric skyboxes, directional lighting, and optimized low-draw-call 3D aircraft models.\n2. 6-DOF Aerodynamic Physics Simulation: Realistic computation of lift, drag, engine thrust, and gravity with responsive pitch, roll, yaw, and stall flight mechanics.\n3. Procedural / Heightmap Terrain & HUD Flight Instruments: Scenic flight environments with airport runways, visual waypoint radar, and realistic cockpit Heads-Up Display.\n4. Flight Waypoint Missions & Timed Courses: Air-ring aerobatic courses, landing challenges, and mission objective tracking with precise millisecond scoring.\n5. Cryptographic Anti-Cheat Global Leaderboard: High score ranking secured by server-side flight telemetry playback and physics verification.\n6. Multi-Input Gamepad & Flight Stick Support: Fluid flight controls supporting standard keyboard/mouse, touch controls, and hardware USB Flight Sticks.",
            'education_adventist' => "1. Multi-Modality Educational Switch: Dynamic toggling between 4 operational typologies (Day School, Boarding Campus, Hybrid, and Higher Ed) with automated UI adaptation.\n2. Holistic LMS & Encounter Bible Curriculum: Modern curriculum management integrated with Faith-Learning Integration (IFL), 3-component grading, and Encounter materials.\n3. Integrated Boarding Management (Boarding Vault): Room allocations, Digital Exeat Pass 2FA workflows (parent + dean), worship attendance tracking, and room audits.\n4. Vocational Work-Study Ledger: Campus vocational timesheet tracking converting approved labor hours into automated tuition bill credits.\n5. Multi-Channel Smart Billing & SunPlus Exporter: Automated tuition invoicing via Virtual Accounts & QRIS with one-click export into SunPlus GC and ISAK 35.\n6. Shabbat Automation Gate: Automated quiet-mode engine shutting down e-commerce checkout and academic assignments between Friday sunset and Saturday sunset.",
            'api_gateway_messaging' => "1. Isolated Multi-Session WhatsApp Engine: Distributed WhatsApp socket runner (OpenWA / Baileys) with automatic recovery, isolated session sandboxes, and dynamic browser QR intake.\n2. Enterprise RESTful Messaging Gateway: High-speed endpoints for text, media documents, location, and interactive templates with Bearer Token auth.\n3. BullMQ Redis High-Concurrency Queue: Asynchronous rate-limiting queue buffer with exponential retry strategies to safeguard SIM accounts from spam bans.\n4. HMAC Cryptographic Webhook Dispatcher: Real-time event streaming for incoming messages and delivery receipts (sent, delivered, read) secured with HMAC-SHA256 signatures.\n5. Real-Time Telemetry & Session Cockpit: Live metrics monitoring session connection states, throughput per second, memory allocations, and delivery failures.\n6. Shared Customer Support Inbox: Real-time multi-agent live chat interface for human teams to respond to customer inquiries collaboratively.",
            'game_3d_simulation' => "1. 60 FPS WebGL / Three.js 3D Engine: High-performance browser rendering featuring dynamic atmospheric skyboxes, directional lighting, and optimized low-draw-call 3D aircraft models.\n2. 6-DOF Aerodynamic Physics Simulation: Realistic computation of lift, drag, engine thrust, and gravity with responsive pitch, roll, yaw, and stall flight mechanics.\n3. Procedural / Heightmap Terrain & HUD Flight Instruments: Scenic flight environments with airport runways, visual waypoint radar, and realistic cockpit Heads-Up Display (Altimeter, Airspeed, Horizon).\n4. Flight Waypoint Missions & Timed Courses: Air-ring aerobatic courses, landing challenges, and mission objective tracking with precise millisecond scoring.\n5. Cryptographic Anti-Cheat Global Leaderboard: High score ranking secured by server-side flight telemetry playback and physics verification to prevent client memory tampering.\n6. Multi-Input Gamepad & Flight Stick Support: Fluid flight controls supporting standard keyboard/mouse, touch controls, and hardware USB Flight Sticks via HTML5 Gamepad API.",
            'decoupled_saas' => "1. Decoupled API-First Core Engine: Isolated headless RESTful backend powered by PostgreSQL 16 Strict ULID and Keyset Cursor Pagination O(1) serving dedicated frontend SPAs.\n2. High-Performance Modern Frontend SPA: Lightning-fast reactive interface (Next.js / Vite React) with client-side routing, route-level code splitting, and zero layout shift.\n3. Stateless JWT / OAuth2 Auth with Refresh Token Rotation: Secure cross-origin authentication with HTTP-only cookies, Redis token blacklisting, and CSRF protection.\n4. OpenAPI 3.0 / Swagger Interactive Playground: Automated, living documentation reflecting exact endpoint schemas, query parameters, and JSON payloads.\n5. Asynchronous Event-Driven Webhooks: Automated event streaming to subscriber services featuring exponential retry backoffs and signature verification.\n6. Headless Operations Admin Hub: Decoupled administrative backoffice enabling operators to manage core domain entities with full auditability.",
            'fnb_culinary' => "1. Modern Auth & Kitchen Management Hub (Filament v5): Weekly menu rotation, diet/regular meal packages, and daily order batching.\n2. Interactive Weekly Menu & Subscription Calendar: Appetizing dish showcase curated via Curator Picker and diet tier selectors.\n3. Delivery Scheduling & Address Portal: Delivery time intake, office drop-off notes, and automated transparent invoice calculation.\n4. Automated PDF Receipt Issuance: QR-coded official purchase receipts and instant PDF generation.\n5. WhatsApp Notification Dispatch: Automated order confirmation to customers and dispatch instructions to internal drivers.\n6. Kitchen Batch Production Dashboard: Ingredient and portion aggregation ledger for culinary kitchen staff.",
            'services_workshop' => "1. Workshop Manager Hub (Filament v5): Work Order intake, vehicle service queue tracker, and mechanic productivity.\n2. Online Service Appointment Booking: Vehicle plate number intake, service type selection, and scheduled arrival time.\n3. Digital Work Order Management: Diagnostic symptom intake, installed spare parts recording, and estimated completion timer.\n4. Vehicle Service Passport (Digital Logbook): Historical maintenance records organized by license plate numbers.\n5. Itemized Estimates & Official Invoicing: Transparent labor rate and parts calculation with exportable PDF receipts.\n6. Automated WhatsApp Progress Alerts: Real-time service milestones dispatched directly to car owner WhatsApp numbers.",
            'logistics' => "1. User Authentication & RBAC: Strict access authorization for Superadmin, Dispatcher, Vendor, and Client using PostgreSQL ULID.\n2. Master Fleet & Route Catalog: Fleet specifications, container capacities, and regional rate tables.\n3. Automated Manifest & Waybill Generation: Instant assignment with unique QR code verification.\n4. Live Shipment Progression: Multi-stage status updates (Accepted -> In-Transit -> Delivered) with mobile photo e-POD upload.\n5. Filament v5 Command Center: Real-time dispatch filter table, operational KPI metric cards, and bulk status triggers.\n6. Document Generation & Export: Instant printable PDF waybills and XLSX dispatch reconciliations.",
            'marketplace' => "1. Verified User Profiles & RBAC: Dual-role onboarding for Customers and Verified Vendors.\n2. Service & Asset Availability Catalog: Media management via Curator Picker and dynamic tier pricing.\n3. Booking & Reservation Pipeline: Interactive calendar picker, pricing calculator, and confirmation lock.\n4. Midtrans Payment Engine: Multi-channel checkout (Virtual Account, QRIS, Cards) with automated webhook settlement.\n5. Command Center (Filament PHP): Booking inspection, vendor credential review, and revenue tracking.\n6. Digital Invoicing & Receipts: Automated PDF receipt generation and instant status notifications.",
            'clinic' => "1. Role-Segregated Clinical Auth: Isolated portals for Receptionists, Physicians, and Pharmacists.\n2. Patient Intake & Queue Management: Patient registration with automated medical record numbers.\n3. Electronic Medical Records (EMR): Diagnostic documentation, anamnesis logs, and digital prescription issuance.\n4. Pharmacy Dispensary & Inventory Sync: Automated real-time deduction upon prescription dispensing with low-stock alerts.\n5. Cashier & Billing Hub: Aggregated billing calculation and instant invoice receipt generation.\n6. Clinical & Revenue Analytics: Exportable patient visit metrics and pharmacy ledger in PDF and Excel formats.",
            'finance' => "1. Financial-Grade RBAC & Audit Trails: Timestamped activity logging for billing officers and auditors.\n2. Commercial Invoice Generator: Dynamic billing engine with multi-currency, tax calculation, and milestone stages.\n3. Automated Payment Gateway: Midtrans Virtual Account & QRIS webhooks with instant ledger reconciliation.\n4. Client Payment Hub: Direct client portal for invoice review and instant payment settlement.\n5. Accounts Receivable & Aging Dashboard: Overdue tracking, aging buckets, and cash inflow analytics.\n6. Financial Export Suite: Downloadable transaction audit journals in Excel and printable PDF formats.",
            'property' => "1. Property Manager Admin Hub (Filament v5): Villa inventory management, HD media curation via Curator Picker, dynamic pricing (rental & sales), and live availability toggling.\n2. Interactive Villa Catalog & Listing: High-speed mobile filters by Bali location, bedroom count, luxury amenities, and transaction type (Daily/Monthly/Yearly Rent vs Freehold/Leasehold Sale).\n3. Responsive Property Showcase: Swipeable HD photo gallery, full architectural specs, transparent pricing, and Google Maps location embed.\n4. Direct WhatsApp Inquiry & Survey Leads: 1-click WhatsApp Click-to-Chat pre-filling villa name and prospective dates directly to the broker/owner.\n5. Ultra-Fast Mobile Optimization: Clean responsive layout optimized for mobile browsers with sub-second page loads.\n6. Lead Activity Logging: Basic prospective tenant inquiry tracking for marketing conversion visibility.",
            'custom_portal' => "1. Modern Authentication & RBAC: Role-based control with distributed PostgreSQL ULID identifiers.\n2. Structured Data Intake & Validation: Robust form validation with multi-layer anti-spam protection.\n3. Filament v5 Enterprise Dashboard: Instant filterable data tables, metric widgets, and bulk processing.\n4. Multi-Tier Approval Workflow: Step-by-step verification pipeline with complete audit trails.\n5. Unified Notification Engine: Email and in-app status updates for critical milestones.\n6. Reporting & Export Suite: Instant export of filtered data into standardized PDF reports and Excel workbooks.",
        ];

        return $isEn ? ($featuresEn[$domain] ?? $featuresEn['custom_portal']) : ($featuresId[$domain] ?? $featuresId['custom_portal']);
    }

    protected function synthesizeRoadmapFeatures(string $corpus, string $domain, bool $isEn): string
    {
        $roadmapId = [
            'bioinformatics_genomics' => "1. Akselerasi Penyejajaran GPU Clustered (CUDA Minimap2): Akselerasi sekuens DNA 10x lipat dengan klaster worker GPU terdistribusi.\n2. Prediktor Lipatan Protein 3D (AlphaFold): Visualisasi konformasi molekuler protein 3D interaktif di browser lengkap dengan peta panas pLDDT.\n3. Ekspor Rekam Medis HL7/FHIR & VCF: Standar interoperabilitas data genomik klinis internasional untuk integrasi sistem rumah sakit.",
            'fintech_ledger_banking' => "1. Deteksi Penipuan AI Real-Time: Analisis pola transaksi anomali berbasis machine learning untuk mendeteksi penarikan mencurigakan dalam waktu < 50ms.\n2. Penagihan Auto-Debit Terjadwal BI-FAST: Otomatisasi penarikan berkala langsung terhubung ke jaringan kliring perbankan nasional.\n3. Partisi Fisik Buku Besar Multi-Tenant: Skalabilitas partisi database fisik per entitas bisnis untuk menangani volume di atas 10 juta transaksi per hari.",
            'iot_telemetry_robotics' => "1. Perencana Navigasi Titik Armada Otonom: Algoritma penyusunan rute misi otomatis dengan deteksi dan penghindaran rintangan dinamis.\n2. Pemeliharaan Prediktif Berbasis AI: Prediksi kerusakan motor penggerak atau keausan mekanik berdasarkan anomali getaran sensor.\n3. Fallback Telemetri Satelit LEO: Failover otomatis pengiriman paket telemetri ke jaringan satelit saat koneksi seluler/LoRa terputus.",
            'ai_agent_rag_pipeline' => "1. Klaster LLM Lokal Mandiri (Self-Hosted): Menjalankan model bahasa open-weights (DeepSeek / Llama 3) pada server lokal berkeamanan tinggi tanpa internet.\n2. Evaluasi Agen Otonom (Automated Eval Harness): Pengujian regresi otomatis kualitas jawaban dan tingkat halusinasi pada setiap pembaruan dokumen referensi.\n3. Multi-Modal Vision & Audio Agent: Agen cerdas yang mampu menganalisis foto teknis, bagan arsitektur, dan rekaman suara inspeksi lapangan.",
            'cybersecurity_siem_zerotrust' => "1. Pustaka Honeypot Cerdas (Decoy Deception Network): Pemasangan token perangkap palsu untuk menjebak dan merekam teknik penyerang jaringan internal.\n2. Orkestrasi Respon Keamanan Otomatis (SOAR Engine): Skrip playbook mitigasi otomatis yang mencabut kredensial, memblokir port, dan memformat ulang node terkompromi.\n3. Enkripsi Pasca-Kuantum (Post-Quantum Cryptography): Peningkatan protokol pertukaran kunci menggunakan algoritma tahan komputer kuantum (ML-KEM / Dilithium).",
            'game_engine_simulation' => "1. Mode Multiplayer Real-Time: Pertempuran udara atau formasi terbang bersama pemain lain secara sinkron dengan latensi rendah.\n2. Editor Desain Pesawat & Livery: Pemain dapat mendesain skin pesawat dan mengatur konfigurasi mesin di hanggar 3D.\n3. Sistem Cuaca Dinamis & Turbulensi Angin: Hujan badai real-time, petir, dan simulasi turbulensi angin ekstrem.\n4. Ekspor Replay Video Sinematik: Perekaman rekaman kamera sinematik penerbangan yang dapat diunduh.",
            'education_adventist' => "1. Middleware Sinkronisasi Dapodik Kemendikdasmen: Ekspor-impor dua arah data NISN, rombel, dan nilai rapor ke server pusat Kemendikdasmen tanpa entri ganda.\n2. Portal Alumni & Pelacak Misi ADRA: Jaringan alumni sekolah Advent global, donasi beasiswa anak asuh, dan pendaftaran relawan misi pelayanan masyarakat (ADRA/Youth).\n3. Integrasi Waktu Sabat Lokal Berbasis Geografis (Adventist Nusantara Time): Perhitungan waktu matahari terbenam otomatis berdasarkan koordinat GPS masing-masing unit sekolah (WIB, WITA, WIT).\n4. Safe Exam Browser & Lock-Down Exam Engine: Penguncian layar perangkat siswa saat ujian standarisasi denominasi untuk mencegah kecurangan.",
            'api_gateway_messaging' => "1. AI Auto-Responder & WhatsApp Chatbot Builder: Integrasi LLM (DeepSeek / OpenAI) untuk membalas pertanyaan pelanggan umum secara cerdas 24/7.\n2. Kampanye WhatsApp Broadcast Cerdas (Smart Throttle): Pengiriman blast pesan dengan variasi jeda acak (anti-spam humanizer) dan segmentasi tag pelanggan.\n3. Gateway Multi-Kanal (Omnichannel API): Perluasan endpoint gateway mencakup Telegram Bot, Instagram Direct Message, dan SMS OTP.\n4. Klaster Multi-Server WhatsApp Terdistribusi: Orkestrasi kontainer Docker per nomor WhatsApp di berbagai VPS cloud dengan load balancing otomatis.",
            'game_3d_simulation' => "1. Mode Multiplayer Real-Time (WebSockets / WebRTC): Pertempuran udara (dogfight) atau formasi terbang bersama pemain lain secara sinkron dengan latensi rendah.\n2. Editor Desain Pesawat & Kustomisasi Livery: Pemain dapat mendesain skin pesawat dan mengatur konfigurasi mesin di hanggar 3D.\n3. Sistem Cuaca Dinamis & Turbulensi Angin: Hujan badai real-time, petir, kabut tebal, dan simulasi turbulensi angin ekstrem yang memengaruhi kendali pesawat.\n4. Ekspor Replay Video Penerbangan: Fitur perekaman rekaman kamera sinematik penerbangan yang dapat diunduh atau dibagikan ke media sosial.",
            'decoupled_saas' => "1. Integrasi GraphQL Federation: Endpoint GraphQL fleksibel untuk query data kompleks dengan satu request bagi klien mobile.\n2. Server-Driven UI (SDUI): Kemampuan mengubah layout dan komponen frontend langsung dari konfigurasi JSON backend tanpa deploy ulang aplikasi.\n3. Sinkronisasi Data Offline-First (Local IndexedDB + CRDT): Dukungan manipulasi data saat koneksi terputus dengan auto-merge saat kembali online.\n4. Global Edge Worker Deployment: Distribusi API gateway ke edge server Cloudflare Workers untuk memangkas latensi global hingga < 30ms.",
            'fnb_culinary' => "1. Integrasi Payment Gateway Multi-Channel: Pembayaran otomatis via QRIS dan Virtual Account Bank BCA/Mandiri.\n2. PWA Pelanggan (1-Klik Reorder): Kemudahan repeat order paket makan siang langsung dari layar ponsel tanpa download app store.\n3. Optimasi Rute Pengantaran Kurir (Delivery Dispatch AI): Pengelompokan alamat antar dalam satu rute wilayah terdekat untuk efisiensi ongkos kirim.",
            'services_workshop' => "1. Pengingat Servis Rutin Otomatis (WhatsApp CRM): Pengingat servis berkala otomatis (ganti oli/tune up) berdasarkan kilometer estimasi.\n2. Integrasi Pembayaran Midtrans (DP Booking Servis): Pembayaran uang muka reservasi servis atau pembelian sparepart via QRIS.\n3. Inventori Barcode Scanner: Pemindaian barcode sparepart saat dipasang ke kendaraan untuk akurasi stok gudang bengkel.",
            'logistics' => "1. Integrasi GPS IoT Telemetri: Pembacaan sensor GPS armada real-time dan pemantauan suhu muatan kontainer.\n2. Notifikasi WhatsApp Gateway: Kirim nomor resi dan link tracking live otomatis ke nomor WhatsApp penerima.\n3. Algoritma Optimasi Rute (Route Dispatcher AI): Rekomendasi rute terpendek untuk efisiensi bahan bakar armada.",
            'marketplace' => "1. Integrasi Escrow Multi-Vendor Otomatis: Pencairan dana otomatis ke rekening bank vendor setelah pesanan selesai.\n2. Notifikasi WhatsApp Bisnis: Notifikasi pengingat pembayaran dan konfirmasi penjemputan unit secara instan.\n3. Aplikasi Mobile PWA Teroptimasi: Akses offline dan notifikasi push untuk mitra di lapangan.",
            'clinic' => "1. Integrasi SatuSehat Kemenkes: Penyelarasan data riwayat medis pasien dengan platform SatuSehat nasional.\n2. Notifikasi Pengingat Kontrol WhatsApp: Pengingat otomatis jadwal kontrol ulang pasien dan resep rutin.\n3. Portal Pasien Mandiri (PWA): Pasien dapat melihat riwayat hasil lab dan mengunduh resep digital sendiri.",
            'finance' => "1. Auto-Debit Recurring Billing: Tagihan langganan otomatis via kartu kredit dan e-wallet.\n2. Rekonsiliasi Bank Otomatis (Open Finance API): Penarikan mutasi rekening koran BCA/Mandiri secara otomatis.\n3. Analisis Prediksi Arus Kas (AI Cashflow Forecast): Proyeksi potensi piutang macet berdasarkan riwayat pembayaran klien.",
            'property' => "1. Integrasi Kalender Reservasi Real-Time (iCal Sync): Sinkronisasi ketersediaan unit otomatis dengan Airbnb, Booking.com, dan VRBO.\n2. Virtual Tour 360 Derajat: Penjelajahan unit villa interaktif 3D panoramic langsung dari browser calon penyewa.\n3. Multi-Currency & Multi-Language Toggle: Konversi mata uang otomatis (IDR, USD, AUD, EUR) dan dukungan multibahasa untuk wisman mancanegara.\n4. Integrasi Payment Gateway Booking Fee: Opsi pembayaran DP/booking fee otomatis jika di kemudian hari pemilik ingin menerima pembayaran online.",
            'custom_portal' => "1. Integrasi WhatsApp Cloud API: Otomatisasi pengiriman notifikasi dan alert transaksi penting ke ponsel klien.\n2. Aplikasi Mobile PWA Offline-Sync: Akses aplikasi cepat untuk operator lapangan dengan sinkronisasi otomatis saat online.\n3. AI Agentic Decision Engine: Analisis prediktif dan asisten cerdas untuk merangkum anomali data operasional.",
        ];

        $roadmapEn = [
            'bioinformatics_genomics' => "1. Clustered GPU Alignment Acceleration (CUDA Minimap2): 10x faster DNA sequence alignment leveraging distributed GPU worker clusters.\n2. 3D Protein Structure Folding Predictor (AlphaFold): In-browser 3D molecular protein conformation viewer with pLDDT confidence heatmaps.\n3. HL7/FHIR & VCF Clinical Export Suite: International clinical genetic interoperability standards for hospital EMR integration.",
            'fintech_ledger_banking' => "1. Real-Time AI Fraud Detection Sentinel: Machine learning transaction scoring detecting suspicious velocity and anomalies in < 50ms.\n2. Open Banking BI-FAST Direct Debit Scheduler: Automated recurring debit authorization directly across national clearing networks.\n3. Multi-Tenant Ledger Physical Partitioning: Database shard isolation scaling throughput beyond 10M+ daily ledger postings.",
            'iot_telemetry_robotics' => "1. Autonomous Fleet Waypoint Navigation Planner: Algorithmic mission path generation with dynamic obstacle avoidance.\n2. AI Predictive Maintenance Engine: Machine learning failure forecasting analyzing vibration anomalies to prevent in-field breakdowns.\n3. LEO Satellite Fallback Telemetry: Automatic telemetry packet failover to satellite links when terrestrial cellular or LoRaWAN drops.",
            'ai_agent_rag_pipeline' => "1. Self-Hosted On-Premise LLM Cluster: Running open-weights models (DeepSeek / Llama 3) within air-gapped private VPC clouds.\n2. Automated Agentic Eval Harness: Continuous regression testing benchmarking hallucination rates and citation accuracy on every knowledge ingestion.\n3. Multi-Modal Vision & Audio Agent: Cognitive agent interpreting schematics, architecture blueprints, and field audio recordings.",
            'cybersecurity_siem_zerotrust' => "1. Intelligent Deception & Honeypot Decoys: Deploying canary tokens and decoy services to trap, profile, and attribute lateral movement attacks.\n2. Automated SOAR Playbook Orchestrator: Dynamic response playbooks triggering immediate token revocations and micro-segmentation upon breach confirmation.\n3. Post-Quantum Cryptography (PQC) Readiness: Future-proof key encapsulation mechanisms resistant to quantum computing decryption.",
            'game_engine_simulation' => "1. Real-Time WebRTC / WebSocket Multiplayer: Synchronous multiplayer dogfights and aerial squadron formation flying with sub-50ms latency.\n2. 3D Hangar Aircraft Customizer: Interactive livery painting and aerodynamic engine configuration in real-time WebGL hangar preview.\n3. Dynamic Atmospheric Weather & Turbulence: Volumetric storm clouds, lightning effects, and crosswind turbulence physics.\n4. Cinematic Replay Recorder: Flight telemetry video recording engine rendering cinematic flight highlights for instant sharing.",
            'education_adventist' => "1. Dapodik Ministry Synchronization Middleware: Bi-directional sync for national student IDs (NISN), classroom rosters, and grade cards.\n2. Alumni Network & ADRA Mission Tracker: Global alumni portal, student scholarship endowment funds, and community mission volunteer registration.\n3. Geo-Coordinates Sunset Engine (Adventist Nusantara Time): Automated sunset calculations adjusting Shabbat quiet-mode according to local GPS coordinates (WIB, WITA, WIT).\n4. Safe Exam Browser Lock-Down Engine: High-security browser confinement for standardized denomination-wide CBT examinations.",
            'api_gateway_messaging' => "1. Autonomous AI Customer Agent: LLM-powered conversational agent (DeepSeek / OpenAI) answering inbound customer questions 24/7 with zero human intervention.\n2. Smart Anti-Ban Broadcast Throttle: Intelligent broadcast campaigns with humanized delay variations, spintax variations, and dynamic tag segmentation.\n3. Omnichannel Messaging Convergence: Unified API gateway extending coverage to Telegram Bots, Instagram DMs, and transactional SMS fallback.\n4. Distributed Container Cluster: Dynamic Docker container orchestration spawning dedicated WhatsApp session nodes across multiple VPS instances with auto-scaling.",
            'game_3d_simulation' => "1. Real-Time WebRTC / WebSocket Multiplayer: Synchronous multiplayer dogfights and aerial squadron formation flying with sub-50ms client-side prediction.\n2. 3D Hangar Aircraft Customizer: Interactive livery painting and aerodynamic engine configuration in real-time WebGL hangar preview.\n3. Dynamic Atmospheric Weather & Turbulence: Volumetric storm clouds, lightning effects, and crosswind turbulence physics shifting flight dynamics.\n4. Cinematic Replay Recorder: Flight telemetry video recording engine rendering cinematic flight highlights for instant sharing.",
            'decoupled_saas' => "1. GraphQL Federation Gateway: Unified GraphQL schema providing flexible client querying for data-dense mobile applications.\n2. Server-Driven UI Engine: Dynamic frontend layout reconfiguration driven by backend JSON schema without requiring client re-deployment.\n3. Offline-First CRDT Data Sync: Local IndexedDB persistence with conflict-free replicated data types auto-synchronizing on reconnection.\n4. Global Edge API Deployment: Edge distribution via Cloudflare Workers reducing global API latency to under 30ms worldwide.",
            'fnb_culinary' => "1. Multi-Channel Payment Gateway: Automated QRIS and Bank Virtual Account checkout.\n2. 1-Click PWA Reorder: Lightweight mobile shortcut for recurring corporate lunch ordering.\n3. AI Delivery Route Dispatcher: Cluster delivery waypoints by geographic vicinity to optimize driver fuel.",
            'services_workshop' => "1. Proactive Maintenance CRM: Scheduled mileage-based oil change and tune-up notifications via WhatsApp.\n2. Booking Fee Payment Gateway: Instant online deposit settlement via QRIS.\n3. Barcode Inventory Scanner: Instant parts barcode scanning upon vehicle mounting to prevent warehouse leakage.",
            'logistics' => "1. IoT GPS Telemetry Integration: Direct sensor integration for live vehicle coordinate and container temperature logging.\n2. Automated WhatsApp Gateway: Instant notification dispatch with live tracking links to recipient phone numbers.\n3. AI Route Optimization Engine: Automated best-route dispatch recommendations to minimize fuel consumption.",
            'marketplace' => "1. Automated Multi-Vendor Escrow Payouts: Automated bank disbursements to vendor accounts upon verified completion.\n2. Official WhatsApp Notifications: Automated reminders for pending payments and booking pickup confirmations.\n3. Mobile PWA Field Companion: Offline-first access with background sync for mobile field coordinators.",
            'clinic' => "1. SatuSehat Ministry of Health Compliance: Bi-directional EMR synchronization with national health services.\n2. Automated WhatsApp Check-up Reminders: Proactive appointment reminders for recurring patient checkups.\n3. Patient Self-Service Portal (PWA): Direct access for patients to view test results and digital prescription history.",
            'finance' => "1. Automated Recurring Billing: Subscription auto-debit via credit cards and digital wallets.\n2. Open Finance Bank Feed Sync: Automated daily bank account statement ingestion and reconciliation.\n3. AI Predictive Cashflow Analytics: Automated risk scoring and payment delay probability indicators.",
            'property' => "1. Real-Time Calendar Sync (iCal Engine): Two-way calendar synchronization with Airbnb, Booking.com, and VRBO to prevent double booking.\n2. 360-Degree Interactive Virtual Tour: Immersive 3D panoramic walkthroughs directly accessible within mobile browsers.\n3. Multi-Currency & Dual-Language Toggle: Real-time currency conversions (IDR, USD, AUD, EUR) and localized copy for international expatriates.\n4. Online Booking Fee Payment Gateway: Automated reservation deposit processing when the owner decides to enable direct digital checkout.",
            'custom_portal' => "1. WhatsApp Cloud API Webhooks: Automated delivery of high-priority operational alerts and status reports.\n2. PWA Offline-First Engine: Progressive web application with background synchronization for field workers.\n3. AI Agentic Decision Intelligence: Automated anomaly detection and executive summary generation.",
        ];

        return $isEn ? ($roadmapEn[$domain] ?? $roadmapEn['custom_portal']) : ($roadmapId[$domain] ?? $roadmapId['custom_portal']);
    }

    protected function synthesizeWorkflow(string $corpus, string $domain, bool $isEn): string
    {
        $workflowId = [
            'bioinformatics_genomics' => "1. Pengunggahan Sampel & Ekstraksi Metadata: Laboran mengunggah file mentah FASTQ/BAM; sistem memvalidasi checksum SHA-256 dan mengekstrak metrik kualitas pembacaan.\n2. Eksekusi Pipeline Varian Terdistribusi: Sistem memicu worker Nextflow untuk melakukan alignment dan variant calling, mencatat progres ke database.\n3. Filter & Anotasi Varian Keyset O(1): Bioinformatikawan menelusuri varian genetik dengan filter kromosom, pathogenicity score, dan anotasi ClinVar seketika.\n4. Desain Target sgRNA CRISPR: Pengguna memasukkan sekuens target gen; algoritma menghitung on-target score dan memvisualisasikan situs pemotongan PAM.\n5. Peninjauan & Persetujuan Klinis: Tim ahli genetika meninjau kandidat varian dan menandatangani laporan hasil analisis secara digital.\n6. Pengarsipan Aman & Ekspor: Data hasil komputasi diarsipkan ke cold storage terenkripsi dan ringkasan temuan diekspor dalam format VCF standar.",
            'fintech_ledger_banking' => "1. Pembentukan Transaksi & Validasi Kunci Idempoten: Klien mengirim instruksi transfer; gateway memvalidasi idempotency key untuk memastikan nol duplikasi.\n2. Verifikasi Saldo & Kunci Akun Serial: Sistem memeriksa saldo tersedia dan mengunci akun terkait di bawah isolasi Strict Serializable.\n3. Posting Jurnal Berpasangan Atomik: Sistem membukukan sepasang entri debit dan kredit secara atomik dalam satu transaksi database.\n4. Rekonsiliasi Otomatis Settlement: Mesin rekonsiliasi mencocokkan mutasi bank eksternal dengan catatan jurnal internal dan menandai selisih nol.\n5. Pencatatan Jejak Audit Kriptografis: Hash transaksi dirantai ke blok audit log permanen yang tidak dapat dimanipulasi.\n6. Penerbitan Rekening Koran Keyset: Nasabah atau auditor mengunduh mutasi rekening koran berbasis kursor ULID O(1) tanpa beban server.",
            'iot_telemetry_robotics' => "1. Autentikasi Perangkat & Koneksi MQTT: Perangkat robotik melakukan handshake TLS mutual (mTLS) dan tersambung ke broker EMQX terdistribusi.\n2. Ingesti Telemetri & Parsing Deret Waktu: Sensor memancarkan koordinat GPS, level daya, dan suhu; consumer memvalidasi dan mencatatnya ke PostgreSQL Timescale.\n3. Visualisasi 3D HUD & Digital Twin: Dasbor web menerima pembaruan posisi via WebSockets dan merender orientasi robotik pada canvas 3D real-time.\n4. Evaluasi Geofencing & Pemicu Peringatan: Sistem mengevaluasi koordinat terhadap poligon wilayah aman; pelanggaran batas memicu notifikasi darurat seketika.\n5. Pengiriman Perintah Kontrol Jarak Jauh: Operator mengirim instruksi misi atau stop darurat melalui antrean berprioritas tinggi ke perangkat.\n6. Penjadwalan & Distribusi Firmware OTA: Admin merilis pembaruan firmware; unit robotik mengunduh paket terenkripsi, memverifikasi hash, dan melakukan flashing aman.",
            'ai_agent_rag_pipeline' => "1. Ingesti Dokumen & Pemotongan Semantik (Chunking): Dokumen pengetahuan diunggah, diekstrak teks dan tabelnya, lalu dipotong menjadi segmen semantik kohesif.\n2. Pembuatan Embedding & Pengindeksan pgvector: Setiap segmen teks dikonversi menjadi vektor dimensi tinggi dan disimpan di PostgreSQL 16 dengan indeks HNSW.\n3. Kueri Pengguna & Pengambilan Konteks (Retrieval): Pengguna mengajukan pertanyaan; sistem mencari chunk dokumen paling relevan menggunakan cosine similarity < 10ms.\n4. Penalaran Agen & Pemanggilan Tools (Agentic Loop): LLM menyusun strategi jawaban, memvalidasi bukti referensi, dan mengeksekusi tool jika diperlukan data eksternal.\n5. Streaming Jawaban Ber-Sitasi (SSE): Respons dikirimkan token-by-token secara langsung ke layar pengguna lengkap dengan sitasi nomor halaman dokumen sumber.\n6. Audit Kualitas & Penyimpanan Riwayat Keyset: Pertanyaan, konteks terambil, dan jawaban dicatat ke audit vault dengan pagination O(1) untuk evaluasi akurasi berkelanjutan.",
            'cybersecurity_siem_zerotrust' => "1. Ingesti Log Keamanan Real-Time: Agen pengumpul mengumpulkan log sistem, aliran jaringan, dan trafik HTTP ke antrean berkecepatan tinggi.\n2. Normalisasi & Pencocokan Pola Serangan: Sistem menormalkan log ke skema standar dan mencocokkan signature ancaman serta anomali frekuensi request.\n3. Peringatan Keamanan & Triase Otomatis: Saat ancaman terdeteksi (seperti SQLi atau brute force), level keparahan dinilai dan insiden dibuat secara otomatis.\n4. Eksekusi Karantina IP & Blokir WAF: Mesin karantina secara instan menyuntikkan IP penyerang ke tabel blacklist WAF dan firewall tanpa menunggu admin manual.\n5. Investigasi Forensik Dasbor SOC: Tim keamanan menganalisis diagram rantai serangan, sampel payload berbahaya, dan kronologi peristiwa di dasbor brutalist.\n6. Resolusi Insiden & Pembaruan Aturan Keamanan: Setelah ancaman dinetralisir, IP karantina ditinjau, laporan forensik diunduh, dan aturan firewall disempurnakan.",
            'game_engine_simulation' => "1. Akses Game di Browser: Pemain membuka URL game di browser modern; mesin 3D menginisialisasi WebGL canvas dan memuat aset 3D pesawat secara instan via CDN.\n2. Kalibrasi Kontrol & Pemilihan Misi: Pemain memilih skenario penerbangan serta memilih skema kendali.\n3. Peluncuran di Landasan Pacu: Pemain menaikkan throttle mesin hingga kecepatan lepas landas tercapai, lalu menarik hidung pesawat ke udara.\n4. Manuver 6-DOF & Navigasi Radar: Pemain mengendalikan pesawat melewati cincin checkpoint udara dengan memantau ketinggian dan kecepatan di HUD.\n5. Pendaratan & Perhitungan Skor: Pesawat mendarat di landasan pacu tujuan; sistem menghitung efisiensi bahan bakar dan kehalusan pendaratan.\n6. Verifikasi & Publikasi Leaderboard: Server memverifikasi log telemetri dan mempublikasikan skor rekor baru ke papan peringkat global.",
            'education_adventist' => "1. Admisi & Pendaftaran PPDB: Calon wali murid mendaftar online, memilih jalur (Day School atau Asrama), mengunggah berkas, dan menerima jadwal wawancara.\n2. Penempatan Asrama & Verifikasi Jemaat: Bagi siswa asrama, Dean menetapkan kamar; pendeta jemaat lokal memverifikasi status anggota untuk aktivasi potongan jemaat.\n3. Pembelajaran & Ibadah Harian: Siswa mengakses materi LMS berbasis IFL, mengikuti ibadah chapel harian, dan dinilai secara komprehensif pada aspek akademis dan karakter.\n4. Kerja Tangan (Work-Study): Siswa asrama menjalankan tugas kerja harian di unit kampus; supervisor menyetujui timesheet yang langsung memotong saldo tagihan SPP di sistem kasir.\n5. Penagihan & Pembayaran SPP: Wali murid menerima invoice bersih pasca-pemotongan work-study dan membayar via Virtual Account/QRIS dengan notifikasi WhatsApp seketika.\n6. Siklus Hening Sabat: Pada hari Jumat petang saat matahari terbenam, sistem mengaktifkan Shabbat Mode, menghentikan penagihan dan tugas hingga Sabat berakhir.",
            'api_gateway_messaging' => "1. Registrasi & Pembentukan Akun: Developer membuat akun di dasbor, memperoleh API Key unik, dan memilih paket kuota throughput pesan.\n2. Pairing Sesi WhatsApp via QR Code: Developer membuka tab sesi di dasbor, memindai QR code via aplikasi WhatsApp di smartphone hingga status berubah menjadi 'Connected'.\n3. Pengujian REST API di Sandbox: Developer mengirim request HTTP POST ke endpoint pesan dengan Bearer token dan payload nomor telepon serta teks pesan.\n4. Pemrosesan Antrean BullMQ: Gateway memvalidasi payload, memasukkan pesan ke antrean Redis, dan worker mengirimkan paket pesan ke socket WhatsApp yang terhubung.\n5. Pengiriman Webhook Real-time: Saat pesan berhasil terkirim dan dibaca oleh penerima, gateway menembakkan webhook ber-tanda tangan HMAC ke URL server developer.\n6. Pemantauan Telemetri & Log: Tim teknis memantau grafik keberhasilan pengiriman, latensi pengantaran, dan status memori sesi di dasbor Filament.",
            'game_3d_simulation' => "1. Akses Game di Browser: Pemain membuka URL game di browser modern; mesin 3D menginisialisasi WebGL canvas dan memuat aset 3D pesawat secara instan via CDN.\n2. Kalibrasi Kontrol & Pemilihan Misi: Pemain memilih skenario penerbangan (Free Flight, Checkpoint Course, atau Carrier Landing) serta memilih skema kendali (Keyboard atau Flight Stick).\n3. Peluncuran di Landasan Pacu: Pemain menaikkan throttle mesin hingga kecepatan lepas landas tercapai, lalu menarik hidung pesawat (pitch up) ke udara.\n4. Manuver 6-DOF & Navigasi Radar: Pemain mengendalikan pesawat melewati cincin checkpoint udara dengan memantau ketinggian dan kecepatan di Heads-Up Display (HUD).\n5. Pendaratan & Perhitungan Skor: Pesawat mendarat di landasan pacu tujuan; sistem menghitung efisiensi bahan bakar, waktu tempuh, dan kehalusan pendaratan (G-force).\n6. Verifikasi & Publikasi Leaderboard: Server memverifikasi log telemetri dan mempublikasikan skor rekor baru ke papan peringkat global.",
            'decoupled_saas' => "1. Otentikasi & Inisialisasi Klien: Pengguna mengakses frontend SPA; aplikasi meminta token JWT stateless melalui endpoint `/api/v1/auth/login` backend headless.\n2. Konsumsi API & Rendering Reaktif: Frontend mengambil data bisnis melalui request RESTful dengan Keyset Cursor Pagination O(1) dan merender UI secara responsif tanpa reload.\n3. Manipulasi Data & Mutasi State: Pengguna melakukan aksi (create/update/delete); frontend mengirim payload JSON terstruktur ke backend API terisolasi.\n4. Validasi Skema & Pemrosesan Database: Backend memvalidasi data menggunakan FormRequest ketat, mengeksekusi operasi transaksi di PostgreSQL 16 Strict ULID, dan mengembalikan response JSON standar.\n5. Penyiaran Event Asinkron: Backend mendispatch event bisnis ke antrean Redis untuk mengirim email notifikasi dan menembakkan webhook ke subscriber eksternal.\n6. Refresh Token & Sesi Aman: Saat token akses mendekati kedaluwarsa, frontend secara transparan merotasi token melalui refresh token endpoint tanpa menginterupsi pengguna.",
            'fnb_culinary' => "1. Pelanggan membuka website katering dan memilih paket (Harian, Mingguan, Diet, atau Reguler).\n2. Pelanggan menentukan tanggal langganan dan memasukkan alamat pengiriman serta nomor WhatsApp aktif.\n3. Sistem menerbitkan nota pesanan otomatis dan mengirimkan konfirmasi via WhatsApp.\n4. Tim dapur memantau rekap porsi di dasbor admin dan menyiapkan masakan sesuai pesanan.\n5. Kurir menerima rincian antaran via WhatsApp dan mengantarkan makanan tepat waktu ke alamat pelanggan.",
            'services_workshop' => "1. Pelanggan mengakses portal bengkel dan memilih jenis layanan servis serta jadwal kedatangan.\n2. Service advisor menerima kendaraan di bengkel dan mengonfirmasi Work Order (WO) di sistem.\n3. Mekanik mengerjakan servis dan memperbarui status pengerjaan serta suku cadang yang diganti.\n4. Sistem mengirimkan notifikasi WhatsApp ke pelanggan bahwa kendaraan telah selesai diservis.\n5. Pelanggan melakukan pembayaran, menerima kuitansi resmi, dan catatan riwayat servis kendaraan otomatis tersimpan.",
            'logistics' => "1. Klien / Dispatcher membuat pesanan pengiriman baru di portal.\n2. Sistem menerbitkan Surat Jalan unik ber-QR Code dan menugaskan armada yang tersedia.\n3. Pengemudi melakukan check-in keberangkatan dan status diperbarui menjadi 'Dalam Perjalanan'.\n4. Barang tiba di tujuan, penerima menandatangani secara digital atau pengemudi mengunggah foto e-POD.\n5. Sistem secara otomatis mencatat pengiriman selesai dan mengirimkan rekapitulasi faktur ke klien.",
            'marketplace' => "1. Pengguna mencari layanan atau unit sewa yang tersedia sesuai tanggal.\n2. Pengguna mengisi detail durasi dan sistem menghitung total biaya secara transparan.\n3. Pengguna melakukan pembayaran melalui Virtual Account atau QRIS Midtrans.\n4. Pembayaran terverifikasi otomatis via webhook, vendor menerima notifikasi pesanan.\n5. Vendor menyerahkan unit/layanan dan menyelesaikan transaksi di dasbor.",
            'clinic' => "1. Pasien mendaftar online atau melalui staf resepsionis di lokasi klinik.\n2. Pasien dipanggil menuju ruang dokter sesuai nomor antrean digital.\n3. Dokter memeriksa pasien dan menginput diagnosis serta resep langsung di Rekam Medis Elektronik.\n4. Apotek menerima resep secara real-time dan menyiapkan obat.\n5. Pasien melakukan pembayaran di kasir dan menerima obat beserta kuitansi resmi.",
            'finance' => "1. Tim finance menyusun draf faktur dan menetapkan tanggal jatuh tempo.\n2. Invoice dikirim otomatis ke email dan WhatsApp klien dengan link pembayaran unik.\n3. Klien membuka portal faktur dan membayar melalui channel pembayaran pilihan.\n4. Sistem payment gateway mengirimkan callback dan status faktur berubah menjadi 'Lunas' seketika.\n5. Sistem mencatat jurnal pelunasan dan menghasilkan kuitansi pembayaran resmi.",
            'property' => "1. Calon penyewa / investor membuka katalog web villa di browser smartphone atau desktop.\n2. Pengunjung memfilter properti berdasarkan lokasi di Bali, rentang harga, atau opsi sewa vs beli.\n3. Pengunjung membuka halaman detail villa untuk melihat foto HD, fasilitas, dan sematan Google Maps.\n4. Pengunjung menekan tombol 'Inquiry via WhatsApp' untuk langsung terhubung dengan tim broker/pengelola dengan pesan otomatis terisi detail villa.\n5. Pengelola villa / admin login ke dasbor Filament untuk memperbarui unit yang tersewa/terjual atau menambahkan listing baru.",
            'custom_portal' => "1. Pengguna mengakses portal dan mengisi data transaksi pada formulir terstruktur.\n2. Sistem memvalidasi data dan menyimpan rekaman dengan identifier unik terenkripsi.\n3. Staf / Operator menerima notifikasi dan melakukan verifikasi kelengkapan data di dasbor Filament.\n4. Supervisor menyetujui transaksi melalui alur persetujuan berjenjang.\n5. Sistem menghasilkan dokumen bukti resmi (PDF) dan memperbarui analitik operasional secara real-time.",
        ];

        $workflowEn = [
            'bioinformatics_genomics' => "1. Sample Upload & Integrity Handshake: Lab technician uploads raw FASTQ/BAM runs; system verifies SHA-256 checksums and computes quality metrics.\n2. Distributed Variant Pipeline Execution: System dispatches Nextflow compute workers, performing read alignment and variant calling with live telemetry.\n3. Keyset O(1) Variant Filtering & Annotation: Bioinformatician queries chromosome positions, pathogenicity scores, and ClinVar cross-references with zero lag.\n4. CRISPR Guide RNA Target Modeling: User provides gene symbol; algorithm computes on-target efficiency and renders PAM cut sites in the genome browser.\n5. Clinical Review & Sign-Off: Geneticist reviews pathogenic classifications and electronically certifies final finding summaries.\n6. Encrypted Archival & Standard VCF Export: Pipeline outputs are archived to encrypted cold storage with standardized VCF and PDF report downloads.",
            'fintech_ledger_banking' => "1. Transaction Intake & Idempotency Check: Client dispatches transfer request; gateway validates idempotency keys to guarantee zero duplicate charges.\n2. Balance Verification & Row Locking: System evaluates available balance and acquires atomic row locks under Strict Serializable isolation.\n3. Atomic Double-Entry Journal Posting: System writes paired debit and credit postings within a single database transaction boundary.\n4. Automated Settlement Reconciliation: Engine reconciles external banking feeds against internal postings, verifying zero variance.\n5. Cryptographic Audit Chaining: Transaction hashes are appended to an immutable audit chain ensuring tamper-evident forensics.\n6. Keyset O(1) Statement Generation: Client or auditor streams account statements via stable ULID cursors with O(1) execution efficiency.",
            'iot_telemetry_robotics' => "1. Mutual TLS Handshake & MQTT Connection: Robotic node negotiates mTLS credentials and registers with distributed EMQX cluster.\n2. Telemetry Ingestion & Timeseries Persistence: Sensors stream GPS coordinates, battery voltage, and telemetry packets into partitioned database logs.\n3. 3D Digital Twin HUD Visualization: Browser cockpit consumes WebSocket telemetry, rendering 6-DOF robotic spatial orientation in real time.\n4. Spatial Geofencing & Incident Triggering: Coordinates are evaluated against geographic polygons; boundary breaches fire high-priority emergency alerts.\n5. Mission Dispatch & Remote Commands: Operator transmits waypoint commands or emergency abort signals through priority command queues.\n6. OTA Firmware Distribution Pipeline: System pushes encrypted firmware payloads; devices verify cryptographic signatures and execute fail-safe flashes.",
            'ai_agent_rag_pipeline' => "1. Document Ingestion & Semantic Chunking: Knowledge files are parsed into semantically cohesive passages while preserving tabular structure.\n2. High-Dimensional Embedding & HNSW Indexing: Passages are converted to vector embeddings and indexed in PostgreSQL 16 pgvector via HNSW.\n3. User Query & Sub-10ms Semantic Retrieval: User enters prompt; engine retrieves top-k relevant chunks via vector similarity lookup in < 10ms.\n4. Agent Reasoning & Tool Invocation: Model orchestrates reasoning steps, cross-verifies retrieved context, and invokes sandboxed tools if needed.\n5. Token-by-Token SSE Stream with Grounded Citations: Response streams live to frontend via Server-Sent Events accompanied by exact document citations.\n6. Keyset Audit Logging & Continuous Evaluation: Turn history, retrieved chunks, and completion latency are logged for hallucination benchmarking.",
            'cybersecurity_siem_zerotrust' => "1. Real-Time Telemetry & Log Ingestion: Collector daemons stream firewall logs, network flows, and HTTP traces into high-throughput message queues.\n2. Log Normalization & Attack Pattern Matching: System normalizes events and evaluates behavioral heuristics against known vulnerability signatures.\n3. Automated Severity Scoring & Alert Triage: High-risk indicators (SQL injection, credential stuffing) trigger automated incident ticket creation.\n4. Automated IP Quarantine & Perimeter Blocking: Threat containment daemon injects rogue IP into WAF and edge firewall blacklist instantly.\n5. Brutalist SOC Forensic Investigation: Security engineers inspect attack vectors, payload snippets, and blast radiuses in the SOC cockpit.\n6. Incident Remediation & Policy Hardening: Post-mitigation review authorizes IP unblocking or rule tuning while exporting tamper-evident compliance dossiers.",
            'game_engine_simulation' => "1. Instant Browser Initialization: Player opens game URL in modern browser; 3D engine instantiates WebGL canvas and streams assets via CDN.\n2. Mission Selection & Input Calibration: Player selects game mode and configures input preference.\n3. Runway Takeoff Sequence: Player accelerates engine throttle to takeoff speed and executes pitch-up rotation into dynamic 3D airspace.\n4. 6-DOF Aerobatic Navigation: Player flies through navigational waypoints while monitoring airspeed and altitude on the cockpit HUD.\n5. Touchdown & Performance Scoring: Player manages approach descent and touches down on target runway; system grades landing smoothness and elapsed time.\n6. Anti-Cheat Telemetry Audit & Leaderboard: Server verifies encrypted physics playback telemetry and records verified records to global leaderboard.",
            'education_adventist' => "1. Admissions & Enrollment: Prospective parents apply online, select modality (Day School vs Boarding), submit documents, and schedule diagnostic interviews.\n2. Boarding Assignment & Pastor Verification: Deans allocate dormitory rooms while local church pastors verify active membership for constituent discounts.\n3. Holistic Learning & Daily Chapel: Students engage in IFL-infused LMS modules, attend morning/evening chapel, and accumulate multidimensional evaluation marks.\n4. Vocational Work-Study Logging: Students fulfill campus assignments; supervisors approve timesheets which immediately deduct from tuition balances.\n5. Tuition Invoicing & Payment: Parents receive net invoices reflecting Work-Study credits and settle balances via Virtual Accounts or QRIS with instant WhatsApp receipts.\n6. Sabbath Quiet-Mode Cycle: Upon Friday sunset, the system automatically activates Shabbat Mode, suspending financial transactions and assignments until Saturday sunset.",
            'api_gateway_messaging' => "1. Developer Onboarding & Credential Generation: Developer signs up on the control dashboard, generates unique Bearer API tokens, and configures throughput quotas.\n2. WhatsApp Device Pairing via QR: Developer initiates a new WhatsApp session in the dashboard and scans the dynamic QR code with their mobile device until status reflects 'Connected'.\n3. API Sandbox Testing: Developer submits HTTP POST requests to the messaging endpoints with payload parameters (recipient, message body, attachments).\n4. Asynchronous Queue Processing: Gateway ingests requests into Redis BullMQ, throttling dispatch rates to safeguard device numbers from anti-spam flags.\n5. HMAC Real-Time Webhook Streaming: As delivery events occur (sent, delivered, read), the gateway dispatches cryptographic HMAC-signed webhooks to the developer's server.\n6. Live Telemetry & Log Auditing: Operations team monitors live throughput per second, worker node latency, and connection health directly in the admin cockpit.",
            'game_3d_simulation' => "1. Instant Browser Initialization: Player opens game URL in any modern browser; 3D engine instantiates WebGL canvas and streams lightweight GLTF assets via high-speed CDN.\n2. Mission Selection & Input Calibration: Player selects game mode (Free Flight, Aerobatic Ring Course, Carrier Landing) and configures input preference (Keyboard/Mouse or USB Gamepad).\n3. Runway Takeoff Sequence: Player accelerates engine throttle to takeoff speed (V1/Vr) and executes pitch-up rotation into dynamic 3D airspace.\n4. 6-DOF Aerobatic Navigation: Player flies through navigational waypoints while monitoring airspeed, altitude, and artificial horizon on the cockpit HUD.\n5. Touchdown & Performance Scoring: Player manages approach descent and touches down on the target runway; system grades landing smoothness (G-Force) and elapsed time.\n6. Anti-Cheat Telemetry Audit & Leaderboard: Server verifies encrypted physics playback telemetry and records verified records to the global competitive leaderboard.",
            'decoupled_saas' => "1. Client Auth Handshake: User loads modern SPA frontend; client requests stateless JWT credentials via the headless backend `/api/v1/auth/login` endpoint.\n2. API Ingestion & Reactive Rendering: Frontend retrieves resources using O(1) keyset-paginated REST endpoints, hydrating client state with zero page reloads.\n3. Data Mutation & Optimistic UI: User performs actions; frontend submits structured JSON payloads to isolated backend controllers while providing instant UI feedback.\n4. Server Validation & Strict ULID Persistence: Backend validates input via strict FormRequests, persists records into PostgreSQL 16 using ULID primary keys, and returns standard JSON responses.\n5. Event Dispatch & Webhook Delivery: Backend triggers asynchronous domain events via Redis BullMQ for notifications and external subscriber webhooks.\n6. Silent Token Rotation: Prior to token expiration, frontend silently triggers refresh token rotation endpoints to maintain unbroken secure user sessions.",
            'fnb_culinary' => "1. Customer opens the responsive web portal and selects meal package (Daily, Weekly, Diet, or Regular).\n2. Customer configures delivery dates, office/home drop-off address, and WhatsApp contact.\n3. System issues automated itemized receipt and alerts customer via WhatsApp.\n4. Kitchen team inspects portion batching in the control panel and prepares daily meals.\n5. Delivery driver receives route dispatch via WhatsApp and completes on-time doorstep delivery.",
            'services_workshop' => "1. Customer accesses workshop portal and selects required service package and preferred arrival slot.\n2. Service advisor admits vehicle, inspecting symptoms and confirming digital Work Order (WO).\n3. Assigned mechanic executes repairs, logging replaced parts and labor hours in the system.\n4. System dispatches real-time WhatsApp alert notifying vehicle owner that maintenance is completed.\n5. Customer completes payment, receives official digital invoice, and maintenance record logs into vehicle passport.",
            'logistics' => "1. Client / Dispatcher registers a new shipment order on the portal.\n2. System issues a unique QR-coded waybill and assigns available fleet assets.\n3. Driver confirms departure, transitioning status to 'In Transit'.\n4. Consignment arrives at destination, recipient signs electronically, and driver uploads e-POD.\n5. System automatically logs completion and sends reconciliation invoice to the client.",
            'marketplace' => "1. User selects desired service or equipment availability for specified dates.\n2. User provides required details and system transparently calculates total pricing.\n3. User executes payment via Midtrans Virtual Account or QRIS.\n4. Settlement verifies via automated webhook, alerting the vendor partner instantly.\n5. Vendor delivers unit/service and confirms completion in the dashboard.",
            'clinic' => "1. Patient checks in online or via front-desk registration.\n2. Patient is queued and routed to physician examination room.\n3. Physician records examination notes, diagnosis, and digital prescriptions in EMR.\n4. Dispensary receives prescription instantly and prepares medication packages.\n5. Patient completes billing settlement at checkout and receives dispensed medicine.",
            'finance' => "1. Finance team drafts invoice and configures due dates and milestones.\n2. Invoice dispatches automatically to client via email and WhatsApp with a direct payment link.\n3. Client reviews itemized breakdown and completes checkout.\n4. Webhook callback verifies payment, instantly marking invoice as 'Paid'.\n5. System reconciles accounts receivable and issues official payment receipt.",
            'property' => "1. Prospective tenant/investor opens the responsive villa catalog on mobile or desktop.\n2. Visitor filters listings by Bali region, budget range, and rent vs purchase requirements.\n3. Visitor inspects the detail showcase, reviewing HD galleries, amenities, and Google Maps pin.\n4. Visitor clicks 'Inquiry via WhatsApp', launching an instant WhatsApp chat with broker pre-populated with villa details.\n5. Villa manager logs into the Filament control panel to update availability, adjust rates, or add new listings.",
            'custom_portal' => "1. User submits transaction data through the structured form.\n2. System validates payload and persists record with encrypted ULID identifiers.\n3. Operational staff receives alert and inspects data in the Filament control panel.\n4. Supervisor reviews and authorizes record through multi-tier workflow.\n5. System generates official PDF summary and updates real-time analytics dashboards.",
        ];

        return $isEn ? ($workflowEn[$domain] ?? $workflowEn['custom_portal']) : ($workflowId[$domain] ?? $workflowId['custom_portal']);
    }

    protected function synthesizeIntegrations(string $corpus, string $domain, bool $isEn): string
    {
        $integrationsId = [
            'bioinformatics_genomics' => 'Python 3.12 / Rust Compute Engine, Nextflow Pipeline Engine, PostgreSQL 16 + pgvector, Celery / Redis Streams Worker Pool, IGV.js Genome Visualizer, Cloudflare R2 / AWS S3 Storage.',
            'fintech_ledger_banking' => 'PostgreSQL 16 Strict ULID (Strict Serializable), Redis Streams (Zero-Loss Queue), Midtrans Core API (VA & QRIS), BI-FAST Open Banking API, Filament v5 Financial Cockpit, Mailgun SMTP.',
            'iot_telemetry_robotics' => 'EMQX / Mosquitto MQTT Broker, PostgreSQL 16 + TimescaleDB, Redis 7 PubSub, Three.js 3D Digital Twin HUD, WebSockets Real-time Bus, Cloudflare R2 OTA Firmware Storage.',
            'ai_agent_rag_pipeline' => 'PostgreSQL 16 + pgvector (HNSW Indexing), LangGraph Agent Engine, FastAPI Python 3.12, Redis Streams, SSE Streaming Protocol, LiteLLM Proxy / OpenRouter, Cloudflare R2 Knowledge Store.',
            'cybersecurity_siem_zerotrust' => 'PostgreSQL 16 Partitioned Logs, Go/Rust Log Collector, Redis Streams, Suricata IDS/IPS Connector, Cloudflare WAF API, Filament v5 SOC Cockpit.',
            'game_engine_simulation' => 'Three.js / WebGL 2.0 Engine, WebGPU Hardware Shaders, Rapier / Cannon.js 3D Physics, Cloudflare R2 Storage (3D GLTF Assets & Audio CDN), HTML5 Gamepad API, Socket.io / WebSockets (Leaderboard Live Feed).',
            'education_adventist' => 'Midtrans Snap & Virtual Account Bank, QRIS Nasional, WhatsApp Business Cloud API, Google Maps Embed, SunPlus Accounting Exporter, Dapodik Kemendikdasmen Middleware.',
            'api_gateway_messaging' => 'OpenWA / Baileys WhatsApp Engine, Redis 7 (BullMQ Queue & Session Store), Cloudflare R2 Storage (Media Attachment Cache), Midtrans API (Billing Tagihan Otomatis), Mailgun Transactional SMTP, Prometheus & Grafana Telemetry.',
            'game_3d_simulation' => 'Three.js / WebGL 2.0 Engine, WebGPU Hardware Shaders, Rapier / Cannon.js 3D Physics, Cloudflare R2 Storage (3D GLTF Assets & Audio CDN), HTML5 Gamepad API, Socket.io / WebSockets (Leaderboard Live Feed).',
            'decoupled_saas' => 'Next.js / Vite React Frontend SPA, Headless RESTful / GraphQL API Engine, PostgreSQL 16 Strict ULID, Redis 7 Cluster (Cache & Queues), Midtrans Snap API, Cloudflare R2 Storage, Mailgun SMTP.',
            'fnb_culinary' => 'WhatsApp Cloud API, Google Maps Embed API, Cloudflare R2 Storage (Foto Menu HD), Mailgun Transactional Email.',
            'services_workshop' => 'WhatsApp Cloud API, Google Maps Embed API, Cloudflare R2 Storage, Midtrans Payment Gateway.',
            'logistics' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mapbox / OpenStreetMap Routing API, S3 / Cloudflare R2 Object Storage.',
            'marketplace' => 'Midtrans Snap Payment Gateway, WhatsApp Business Cloud API, Google Maps Autocomplete, Cloudflare R2.',
            'clinic' => 'SatuSehat Kemenkes API, Midtrans QRIS/VA, WhatsApp Gateway Pengingat Pasien, Cloud Backup Storage.',
            'finance' => 'Midtrans Core API (VA & QRIS), WhatsApp Notification Gateway, Mailgun Transactional Email, Jurnal/Xero Export API.',
            'property' => 'WhatsApp Click-to-Chat API, Google Maps Embed, Cloudflare R2 / S3 Storage (Optimasi Foto HD), Mailgun Inquiry Notification.',
            'custom_portal' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mailgun SMTP, Cloudflare Object Storage R2.',
        ];

        $integrationsEn = [
            'bioinformatics_genomics' => 'Python 3.12 / Rust Compute Engine, Nextflow Pipeline Orchestrator, PostgreSQL 16 + pgvector, Celery / Redis Streams Worker Queue, IGV.js Genome Browser, Cloudflare R2 / AWS S3 Storage.',
            'fintech_ledger_banking' => 'PostgreSQL 16 Strict ULID (Strict Serializable), Redis Streams (Zero-Loss Queue), Midtrans Core API (VA & QRIS), BI-FAST Open Banking API, Filament v5 Financial Cockpit, Mailgun SMTP.',
            'iot_telemetry_robotics' => 'EMQX / Mosquitto MQTT Cluster, PostgreSQL 16 + TimescaleDB, Redis 7 PubSub, Three.js 3D Digital Twin HUD, WebSockets Telemetry Engine, Cloudflare R2 OTA Firmware CDN.',
            'ai_agent_rag_pipeline' => 'PostgreSQL 16 + pgvector (HNSW Indexing), LangGraph Agent Engine, FastAPI Python 3.12, Redis Streams, SSE Streaming Protocol, LiteLLM Proxy / OpenRouter, Cloudflare R2 Knowledge Store.',
            'cybersecurity_siem_zerotrust' => 'PostgreSQL 16 Partitioned Logs, Go/Rust Log Collector, Redis Streams, Suricata IDS/IPS Connector, Cloudflare WAF API, Filament v5 SOC Cockpit.',
            'game_engine_simulation' => 'Three.js / WebGL 2.0 Engine, WebGPU Accelerated Shaders, Rapier / Cannon.js Physics Engine, Cloudflare R2 Asset CDN (GLTF Models & Audio), HTML5 Gamepad API, Socket.io Real-Time Leaderboard.',
            'education_adventist' => 'Midtrans Snap & Bank Virtual Accounts, National QRIS, WhatsApp Business Cloud API, Google Maps Embed, SunPlus Accounting Exporter, Ministry Dapodik Middleware.',
            'api_gateway_messaging' => 'OpenWA / Baileys WhatsApp Engine, Redis 7 (BullMQ Distributed Queue & Session Cache), Cloudflare R2 Media Storage, Midtrans Invoicing API, Mailgun Transactional SMTP, Prometheus & Grafana Metrics.',
            'game_3d_simulation' => 'Three.js / WebGL 2.0 Engine, WebGPU Accelerated Shaders, Rapier / Cannon.js Physics Engine, Cloudflare R2 Asset CDN (GLTF Models & Audio), HTML5 Gamepad API, Socket.io Real-Time Leaderboard.',
            'decoupled_saas' => 'Next.js / Vite React Frontend SPA, Headless RESTful / GraphQL API Engine, PostgreSQL 16 Strict ULID, Redis 7 Cluster (Cache & Queues), Midtrans Snap API, Cloudflare R2 Storage, Mailgun SMTP.',
            'fnb_culinary' => 'WhatsApp Business Cloud API, Google Maps Embed API, Cloudflare R2 Storage (HD Food Images), Mailgun SMTP.',
            'services_workshop' => 'WhatsApp Business Cloud API, Google Maps Embed API, Cloudflare R2 Storage, Midtrans Payment Gateway.',
            'logistics' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mapbox / OpenStreetMap Routing API, Cloudflare R2 Storage.',
            'marketplace' => 'Midtrans Snap Payment Gateway, WhatsApp Business API, Google Places Autocomplete, Cloudflare R2.',
            'clinic' => 'SatuSehat MOH API, Midtrans QRIS/VA, Patient WhatsApp Dispatcher, Encrypted Cloud Storage.',
            'finance' => 'Midtrans Core API (VA & QRIS), WhatsApp Notification Gateway, Mailgun SMTP, Accounting Export Webhooks.',
            'property' => 'WhatsApp Click-to-Chat API, Google Maps Embed API, Cloudflare R2 Storage (HD Photo CDN), Mailgun Notification Webhooks.',
            'custom_portal' => 'Midtrans Payment Gateway, WhatsApp Cloud API, Mailgun Transactional SMTP, Cloudflare R2.',
        ];

        return $isEn ? ($integrationsEn[$domain] ?? $integrationsEn['custom_portal']) : ($integrationsId[$domain] ?? $integrationsId['custom_portal']);
    }

    protected function synthesizeOutOfScope(string $corpus, string $domain, bool $isEn): string
    {
        $outOfScopeId = [
            'bioinformatics_genomics' => "1. Tidak menyediakan mesin fisik sekuensing DNA generasi berikutnya (NGS sequencer hardware) di lokasi fisik klien (fokus pada platform perangkat lunak analisis bioinformatika dan pipeline data).\n2. Tidak menggantikan uji validasi laboratorium basah (wet-lab validation) atau diagnosis klinis medis akhir yang diwajibkan oleh badan regulasi kesehatan.\n3. Tidak menyediakan hosting data biologis di server publik tanpa enkripsi (seluruh data dienkripsi dengan standar ketat).",
            'fintech_ledger_banking' => "1. Tidak bertindak sebagai bank komersial penerima simpanan atau penyalur kredit perbankan berlisensi (fokus pada platform perangkat lunak ledger dan sistem rekonsiliasi).\n2. Tidak mengelola uang tunai fisik atau mesin ATM.\n3. Lisensi jasa pembayaran (PJP) atau perbankan merupakan tanggung jawab entitas bisnis klien.",
            'iot_telemetry_robotics' => "1. Tidak mencakup perakitan fisik sasis robot atau produksi perangkat keras sirkuit PCB (fokus pada sistem telemetri, firmware OTA, dan cloud platform).\n2. Tidak melayani pengurusan izin frekuensi radio atau sertifikasi tipe perangkat di kementerian komunikasi.\n3. Kalibrasi mekanis fisik unit robot di lapangan menjadi tanggung jawab teknisi lokal klien.",
            'ai_agent_rag_pipeline' => "1. Tidak melatih model dasar (foundation model pre-training) bernilai jutaan dolar dari awal (memanfaatkan model open-weights canggih dan fine-tuning/RAG terarah).\n2. Tidak bertanggung jawab atas penggunaan agen cerdas untuk tujuan ilegal atau melanggar etika.\n3. Batasan token dan biaya API pihak ketiga dibayarkan langsung oleh klien ke penyedia.",
            'cybersecurity_siem_zerotrust' => "1. Tidak menjamin kekebalan 100% terhadap serangan rekayasa sosial (phishing/social engineering) atau kelalaian kredensial staf manusia internal.\n2. Tidak menyediakan personil fisik penjaga keamanan di lokasi kantor klien (fokus pada sistem keamanan siber digital).\n3. Investigasi forensik kriminal kepolisian merupakan domain aparat penegak hukum resmi.",
            'game_engine_simulation' => "1. Tidak memerlukan unduhan instalasi file executable di komputer (100% berjalan instan di dalam browser modern melalui teknologi WebGL / WebGPU tanpa plugin).\n2. Tidak mensimulasikan kerusakan struktural aerodinamika skala militer ultra-kompleks.\n3. Tidak menyediakan transaksi pasar jual-beli NFT atau aset kripto di dalam game pada Fase 1.\n4. Tidak mencakup dukungan headset VR imersif penuh di rilis awal.",
            'education_adventist' => "1. Tidak menyediakan instalasi server fisik on-premise per sekolah (seluruh infrastruktur berjalan tersentralisasi di Cloud Monolith Neriah Pro dengan performa keyset O(1)).\n2. Tidak menggantikan sistem core banking perbankan komersial (transaksi diproses via payment gateway resmi Midtrans dan Virtual Account bank).\n3. Tidak mencakup penyediaan hardware fisik pengawas (CCTV, mesin biometrik absensi fisik, turnstile gate); sistem menyediakan API webhook dan scan QR/RFID berbasis web.\n4. Tidak memproses transaksi kartu kredit valuta asing pada peluncuran Fase 1 (fokus pada mata uang Rupiah via VA, QRIS, dan transfer bank resmi).",
            'api_gateway_messaging' => "1. Tidak menyediakan kartu SIM fisik atau nomor WhatsApp resmi dari Neriah Pro (nomor WhatsApp dan perangkat ponsel disediakan oleh masing-masing tenant/klien).\n2. Tidak menjamin kekebalan 100% terhadap pemblokiran oleh pihak WhatsApp/Meta jika pengguna melanggar Kebijakan Penggunaan Wajar WhatsApp (spamming/pesan massal ilegal tanpa opt-in).\n3. Tidak menyediakan fitur video call atau voice call streaming antar pengguna di Fase 1 (fokus pada perpesanan teks, media gambar, audio, dan dokumen PDF).\n4. Tidak membangun aplikasi native mobile store Android/iOS mandiri di rilis awal (disediakan dasbor manajemen web responsif performa tinggi).",
            'game_3d_simulation' => "1. Tidak memerlukan unduhan instalasi file executable (.exe / .dmg) di komputer (100% berjalan instan di dalam browser modern melalui teknologi WebGL / WebGPU tanpa plugin).\n2. Tidak mensimulasikan kerusakan struktural aerodinamika skala militer ultra-kompleks (fokus pada manuver penerbangan dinamis, balapan udara, dan misi checkpoint yang menghibur).\n3. Tidak menyediakan transaksi pasar jual-beli NFT atau aset kripto di dalam game pada Fase 1.\n4. Tidak mencakup dukungan headset VR imersif penuh di rilis awal (fokus pada tampilan layar browser desktop dan perangkat mobile).",
            'decoupled_saas' => "1. Tidak menggabungkan kode frontend dan backend dalam satu monolit blade PHP di Fase 1 (arsitektur terisolasi secara decoupled murni antara Client SPA dan Headless API).\n2. Tidak membangun aplikasi native mobile store Android/iOS dari awal di Fase 1 (menggunakan Progressive Web App / responsive SPA berkecepatan tinggi yang dapat diinstal di homescreen ponsel).\n3. Tidak menyediakan skema custom hardware on-premise di luar infrastruktur cloud standar berstandar enterprise.",
            'fnb_culinary' => "1. Tidak membangun aplikasi native Play Store / App Store di Fase 1 (fokus pada Web App responsif mobile yang cepat dibuka di browser smartphone).\n2. Tidak mengelola armada logistik pihak ketiga secara langsung (fokus pada operasional katering dan kurir internal).\n3. Penanganan kompensasi pembatalan mendadak di luar jam operasional diatur dalam S&K resmi katering.",
            'services_workshop' => "1. Tidak mencakup integrasi perangkat keras scan OBD-II / mesin diagnostik ECU secara langsung di Fase 1.\n2. Tidak menyediakan aplikasi native mobile store di rilis awal (berbasis Web Responsive PWA performa tinggi).\n3. Pengurusan klaim asuransi pihak ketiga di luar bengkel ditangani secara manual.",
            'logistics' => "1. Tidak membuat aplikasi native iOS & Android khusus app store pada Fase 1 MVP (fokus pada Progressive Web App / Web Responsive yang ringan dan dapat diakses dari browser smartphone).\n2. Tidak mencakup integrasi perangkat keras sensor telemetri pihak ketiga (IoT) yang belum terstandarisasi di tahap awal.\n3. Tidak melayani pengurusan bea cukai dan regulasi pengiriman lintas negara (fokus pada pengiriman domestik Indonesia).",
            'marketplace' => "1. Tidak menyediakan aplikasi native mobile store iOS/Android di rilis awal (menggunakan Web Responsive PWA performa tinggi).\n2. Tidak mengelola logistik fisik atau asuransi barang secara langsung (tanggung jawab vendor dan pihak ketiga).\n3. Tidak menyediakan skema kredit cicilan tanpa agunan (BNPL) pihak ketiga selain saluran pembayaran resmi Midtrans.",
            'clinic' => "1. Tidak menyediakan integrasi mesin radiologi / PACS imaging langsung di Fase 1 (fokus pada data rekam medis teks, diagnosa, dan laboratorium).\n2. Tidak mencakup aplikasi native mobile pasien di Google Play / App Store pada tahap MVP.\n3. Tidak melakukan pemotongan klaim BPJS otomatis secara langsung sebelum bridging resmi tersedia.",
            'finance' => "1. Tidak menyediakan software akuntansi full double-entry internal (fokus pada billing, invoicing, penagihan, dan ekspor jurnal).\n2. Tidak melayani fungsi perbankan simpan pinjam komersial.",
            'property' => "1. Tidak mencakup integrasi payment gateway atau transaksi kartu kredit di Fase 1 (fokus pada katalog cepat dan direct inquiry WhatsApp).\n2. Tidak membangun aplikasi native Play Store / App Store khusus (fokus pada Progressive Web Application responsif yang ringan dibuka di browser smartphone).\n3. Tidak mengelola perizinan legalitas sertifikat tanah/IMB secara langsung (tanggung jawab pihak notaris dan broker rekanan).",
            'custom_portal' => "1. Tidak membangun aplikasi native mobile iOS/Android mandiri di Fase 1 MVP (difokuskan pada Progressive Web App berperforma tinggi dan responsif di semua perangkat).\n2. Tidak mencakup integrasi perangkat keras fisik Bluetooth/Thermal khusus tanpa API standar.\n3. Fitur di luar spesifikasi yang disetujui akan diakomodasi melalui Change Request (CR) terpisah.",
        ];

        $outOfScopeEn = [
            'bioinformatics_genomics' => "1. Excludes physical next-generation DNA sequencer hardware procurement (focused strictly on bioinformatics computational pipelines and data engineering).\n2. Does not substitute mandatory wet-lab experimental validation or formal medical diagnosis required by regulatory authorities.\n3. Excludes unencrypted public storage hosting (biological data is strictly protected via zero-knowledge encryption).",
            'fintech_ledger_banking' => "1. Does not operate as a licensed commercial depository bank or credit issuer (focused strictly on software ledger and reconciliation engineering).\n2. Excludes physical cash handling or automated teller machine (ATM) hardware maintenance.\n3. Regulatory banking and money transmitter licenses are the sole responsibility of the client entity.",
            'iot_telemetry_robotics' => "1. Excludes physical robotics chassis manufacturing and custom PCB circuit fabrication (focused on telemetry systems, OTA firmware pipelines, and cloud software).\n2. Excludes regulatory radio frequency license acquisition or local device compliance certifications.\n3. Physical mechanical calibration on-site remains the responsibility of client operations.",
            'ai_agent_rag_pipeline' => "1. Excludes pre-training foundational LLMs from scratch (focuses on production RAG, agentic tools, and targeted adapter fine-tuning).\n2. Prohibits deployment for unauthorized automated legal, medical, or unethical decision-making without human oversight.\n3. Third-party model API token costs are directly settled by the client with respective providers.",
            'cybersecurity_siem_zerotrust' => "1. Does not guarantee 100% immunity against human social engineering, insider credential theft, or phishing negligence.\n2. Excludes physical on-premise security guard personnel (focused strictly on digital cybersecurity systems).\n3. Criminal forensic law-enforcement investigations remain under the jurisdiction of official law enforcement authorities.",
            'game_engine_simulation' => "1. Requires zero desktop installer downloads; executes 100% client-side directly within modern browsers via WebGL / WebGPU acceleration.\n2. Excludes ultra-complex military-grade stress damage physics.\n3. Excludes cryptocurrency or NFT asset marketplace trading in Phase 1.\n4. Excludes immersive VR headset peripherals in MVP release.",
            'education_adventist' => "1. Excludes physical on-premise local server hosting per school (centralized on hardened Neriah Pro Cloud VPS Monolith with O(1) keyset scalability).\n2. Excludes commercial core banking system replacement (settlements processed through licensed Midtrans payment gateways and bank Virtual Accounts).\n3. Excludes physical hardware procurement (surveillance cameras, turnstile hardware gates, standalone biometric readers); web-based QR/RFID scanners and webhooks are provided.\n4. Excludes cross-border foreign currency credit card billing in Phase 1 (focused strictly on Indonesian Rupiah via VA, QRIS, and official bank wires).",
            'api_gateway_messaging' => "1. Excludes physical SIM card or WhatsApp phone number procurement (devices and WhatsApp phone lines are supplied by each tenant).\n2. Does not guarantee 100% immunity from Meta WhatsApp bans if users violate WhatsApp's Fair Use Policy (unsolicited mass spam without explicit user opt-in).\n3. Excludes real-time voice and video call streaming in Phase 1 MVP (focused strictly on text, images, voice notes, documents, and interactive templates).\n4. Excludes native mobile app store binaries in initial release (delivered via high-speed responsive web control cockpit).",
            'game_3d_simulation' => "1. Requires zero desktop installer downloads (.exe / .dmg); executes 100% client-side directly within modern browsers via WebGL / WebGPU acceleration.\n2. Excludes ultra-complex military-grade stress damage physics (tuned for accessible, dynamic flight maneuverability and precision aerobatic racing).\n3. Excludes cryptocurrency or NFT asset marketplace trading in Phase 1.\n4. Excludes immersive VR headset peripherals in MVP release (tuned for desktop monitors, laptops, and mobile screens).\n5. Excludes local real-time hardware physics rigs outside browser capabilities.",
            'decoupled_saas' => "1. Excludes bundling frontend templates within backend server views (strictly separated as decoupled frontend SPA and headless REST/GraphQL API).\n2. Excludes standalone native iOS/Android mobile store distribution in Phase 1 (delivered as an ultra-fast responsive SPA / PWA).\n3. Excludes bespoke on-premise hardware deployments outside standard cloud hosting topologies.",
            'fnb_culinary' => "1. No native mobile app store binaries in Phase 1 (focus is on ultra-fast responsive Web App accessible from smartphone browsers).\n2. Platform does not directly manage third-party freight carriers (scoped to in-house kitchen and local couriers).\n3. Last-minute cancellation compensations outside kitchen cutoff hours are governed by standard operating policies.",
            'services_workshop' => "1. Excludes direct OBD-II vehicle diagnostic hardware scanner integration in Phase 1 MVP.\n2. Excludes native mobile app store distribution in MVP release (delivered as high-performance responsive Web PWA).\n3. Third-party insurance claim paperwork is handled manually outside the platform.",
            'logistics' => "1. No native iOS/Android binary store application in Phase 1 MVP (focus is on lightweight, high-performance Progressive Web App accessible via mobile browsers).\n2. Excludes proprietary non-standard IoT telemetry hardware sensor interfacing in initial release.\n3. Excludes international customs declaration processing (scoped strictly to domestic Indonesian operations).",
            'marketplace' => "1. No native mobile app store binaries in Phase 1 MVP (delivered as a fast Responsive Web PWA).\n2. Platform does not directly manage physical inventory custody or third-party transit insurance.\n3. No custom third-party Buy-Now-Pay-Later (BNPL) credit underwriting outside standard Midtrans channels.",
            'clinic' => "1. Excludes direct integration with physical radiology / PACS machinery in Phase 1 MVP (focus on text clinical records, diagnoses, and lab results).\n2. Excludes native mobile patient app store distribution in MVP release.\n3. Excludes direct unbridged national health insurance (BPJS) claim underwriting.",
            'finance' => "1. Excludes full internal double-entry ledger bookkeeping replacement (focuses on invoicing, automated receivables, and journal exports).\n2. Does not function as a licensed banking deposit/loan custodian.",
            'property' => "1. No online payment gateway or credit card processing in Phase 1 (leads and bookings are routed directly to WhatsApp and bank transfer).\n2. No native iOS/Android binary store apps in Phase 1 (delivered as an ultra-fast responsive Web Application).\n3. Platform does not provide legal title underwriting or notary licensing services.",
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

        // 1. Analyze Payment Gateway Intent & Financial Rails
        $isNoPaymentRequested = (bool) preg_match('/(?:tanpa|bebas|tidak\s+perlu|no)\s+(?:payment|gateway|pembayaran|pg|transaksi\s+online)|manual\s+transfer|cash\s+on\s+delivery|hanya\s+inquiry|inquiry\s+only|katalog\s+only/i', $corpusLower);
        $hasBri = str_contains($corpusLower, 'bri') || str_contains($corpusLower, 'briva');
        $hasBni = str_contains($corpusLower, 'bni');
        $hasMandiri = str_contains($corpusLower, 'mandiri') || str_contains($corpusLower, 'livin');
        $hasBca = str_contains($corpusLower, 'bca') || str_contains($corpusLower, 'klikpay') || str_contains($corpusLower, 'oneklik');
        $hasStripe = str_contains($corpusLower, 'stripe');
        $hasPaypal = str_contains($corpusLower, 'paypal');
        $hasWise = str_contains($corpusLower, 'wise') || str_contains($corpusLower, 'transferwise');
        $hasXendit = str_contains($corpusLower, 'xendit');
        $hasMidtrans = str_contains($corpusLower, 'midtrans');
        $isGlobalMarket = (bool) preg_match('/(?:global|internasional|international|usd|eur|aud|sgd|turis|foreigner|cross-border|ekspor|worldwide|mancanegara)/i', $corpusLower);

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
        ];

        // 2. Dynamic Payment Rails Recommendation (Strictly honest, non-assumptive)
        if ($isNoPaymentRequested) {
            $allSuggestions['payment_rail'] = [
                'id' => 'manual_transfer_proof',
                'category' => 'integration',
                'title' => $isEn ? 'Manual Bank Transfer & WhatsApp Slip Verification' : 'Verifikasi Manual Bukti Transfer & WhatsApp',
                'desc' => $isEn ? 'Direct manual bank wire with customer slip upload and automated WhatsApp dispatch to cashier.' : 'Pembayaran transfer manual bank dengan unggah bukti transfer/slip setoran dan verifikasi manual oleh staf/admin.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'Manual Bank Transfer Verification (Receipt Upload & WhatsApp Dispatch)' : 'Verifikasi Manual Bukti Transfer Bank (Upload Bukti Bayar & Konfirmasi WhatsApp)',
                'badge' => 'Transaksi Manual',
            ];
        } elseif ($hasBri) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_bri_api',
                'category' => 'integration',
                'title' => 'Bank BRI Open API (BRIVA & Direct Debit)',
                'desc' => $isEn ? 'Direct Host-to-Host (H2H) Virtual Account & cash management via official BRI Open API.' : 'Integrasi direct Host-to-Host (H2H) Virtual Account BRIVA & Corporate API resmi Bank BRI.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Bank BRI Open API (BRIVA Host-to-Host Virtual Account & Direct Debit API)',
                'badge' => 'Bank API',
            ];
        } elseif ($hasBni) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_bni_api',
                'category' => 'integration',
                'title' => 'Bank BNI Open API & Corporate VA',
                'desc' => $isEn ? 'Direct bank integration with BNI Corporate API for instant automated account reconciliation.' : 'Integrasi direct API Bank BNI untuk Virtual Account corporate dan rekonsiliasi mutasi otomatis.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Bank BNI Open API & Corporate Virtual Account H2H',
                'badge' => 'Bank API',
            ];
        } elseif ($hasMandiri) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_mandiri_api',
                'category' => 'integration',
                'title' => 'Bank Mandiri Direct API (MCM / Bill Payment)',
                'desc' => $isEn ? 'Direct corporate integration via Mandiri Cash Management API.' : 'Integrasi direct Host-to-Host Virtual Account & Bill Payment Bank Mandiri.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Bank Mandiri Direct API (Mandiri Cash Management & Corporate VA)',
                'badge' => 'Bank API',
            ];
        } elseif ($hasBca) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_bca_api',
                'category' => 'integration',
                'title' => 'BCA Open API (OneKlik & BCA Virtual Account)',
                'desc' => $isEn ? 'Direct integration with Bank Central Asia API for instant settlements.' : 'Integrasi direct Host-to-Host BCA Virtual Account & OneKlik.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'BCA Open API (BCA Virtual Account H2H & OneKlik)',
                'badge' => 'Bank API',
            ];
        } elseif ($hasStripe || $isGlobalMarket) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_stripe',
                'category' => 'integration',
                'title' => $isEn ? 'Stripe Global Payments (Multi-Currency & Cards)' : 'Stripe Global Payments (Multi-Valas & Kartu Kredit)',
                'desc' => $isEn ? 'Accept international payments via Visa, MasterCard, Amex, Apple Pay, and Google Pay in foreign currencies (USD/EUR).' : 'Dukungan pembayaran internasional (Kartu Kredit/Debit Visa/MasterCard, Apple Pay, Google Pay) dalam berbagai mata uang asing.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Stripe Global Payments API (Multi-Currency Checkout, Visa/Mastercard/Amex, Apple Pay)',
                'badge' => 'Pembayaran Global',
            ];
        } elseif ($hasPaypal) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_paypal',
                'category' => 'integration',
                'title' => $isEn ? 'PayPal Commerce Platform' : 'PayPal Commerce Platform (Checkout Global)',
                'desc' => $isEn ? 'Global digital wallet checkout trusted by international buyers.' : 'Pembayaran dompet digital global paling terpercaya untuk wisatawan & pembeli mancanegara.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'PayPal Commerce Platform API',
                'badge' => 'Pembayaran Global',
            ];
        } elseif ($hasWise) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_wise',
                'category' => 'integration',
                'title' => $isEn ? 'Wise Multi-Currency & Payouts API' : 'Wise Multi-Currency & Payouts API',
                'desc' => $isEn ? 'Low-cost cross-border payments with real mid-market exchange rates.' : 'Transfer uang lintas negara dengan kurs riil pasar menengah tanpa markup biaya tersembunyi.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Wise Business Multi-Currency API & International Payouts',
                'badge' => 'Pembayaran Global',
            ];
        } elseif ($hasXendit) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_xendit',
                'category' => 'integration',
                'title' => 'Xendit Payment Infrastructure (Multi-Channel)',
                'desc' => $isEn ? 'Indonesian and SEA payment infrastructure for VA, QRIS, e-Wallets, and retail.' : 'Infrastruktur pembayaran digital untuk Virtual Account, QRIS Dinamis, e-Wallet, dan gerai retail.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Xendit Payment Infrastructure (Virtual Account, QRIS Dinamis, e-Wallet)',
                'badge' => 'Pembayaran',
            ];
        } elseif ($hasMidtrans) {
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_midtrans',
                'category' => 'integration',
                'title' => 'Midtrans Payment Gateway (Snap API & QRIS)',
                'desc' => $isEn ? 'Accept Bank Virtual Accounts, QRIS, and Credit Cards via Midtrans Snap.' : 'Pembayaran multi-channel otomatis via Virtual Account Bank, QRIS Dinamis, dan Kartu Kredit.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Midtrans Payment Gateway (Snap API, QRIS, Virtual Account Multi-Bank)',
                'badge' => 'Pembayaran',
            ];
        } else {
            // General configurable payment rails (never lock into single vendor)
            $allSuggestions['payment_rail'] = [
                'id' => 'payment_multi_rail',
                'category' => 'integration',
                'title' => $isEn ? 'Payment Rails (Direct Bank API / QRIS / Global)' : 'Rel Pembayaran (Direct Bank API / QRIS / Global)',
                'desc' => $isEn ? 'Configurable payment rails: Direct Bank API (BRI/BNI), QRIS Dinamis, or Global Gateway (Stripe/Wise).' : 'Dukungan gateway fleksibel sesuai pasar: Direct Bank API (BRI/BNI), QRIS Dinamis, atau Global (Stripe/Wise).',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Integrasi Rel Pembayaran Terpilih (Direct Bank API BRI/BNI, QRIS Nasional, atau Global Stripe/Wise)',
                'badge' => 'Pembayaran Fleksibel',
            ];
        }

        // 3. Universal Enterprise Architecture Suggestions
        $allSuggestions['excel_export'] = [
            'id' => 'excel_export',
            'category' => 'feature',
            'title' => $isEn ? 'Comprehensive Excel & PDF Reporting' : 'Ekspor Laporan Excel (.xlsx) & PDF',
            'desc' => $isEn ? 'Allow managers to export audit reconciliations, financial ledgers, and operational tables to Excel.' : 'Fitur unduh laporan operasional, rekapitulasi data harian/bulanan, dan audit transaksi ke format Excel dan PDF.',
            'target_field' => 'fiturWajib',
            'addition' => $isEn ? 'Comprehensive Data Export Suite: One-click export of operational ledgers and metrics to Microsoft Excel (.xlsx) and printable PDF.' : 'Modul Ekspor Laporan Komprehensif: Unduh rekapitulasi operasional dan riwayat data ke format Microsoft Excel (.xlsx) dan PDF resmi siap cetak.',
            'badge' => 'Fitur MVP',
        ];

        $allSuggestions['approval_role'] = [
            'id' => 'approval_role',
            'category' => 'actor',
            'title' => $isEn ? 'Supervisor / Manager Approval Tier' : 'Tingkat Akses Supervisor / Approval',
            'desc' => $isEn ? 'Prevent operational errors by requiring a manager authorization step before critical actions are executed.' : 'Cegah salah eksekusi dengan otorisasi persetujuan (approval) berjenjang oleh Supervisor atau Manajer.',
            'target_field' => 'aktorSistem',
            'addition' => $isEn ? 'Supervisor / Manager: Multi-tier review and authorization before high-value or critical transactions are executed.' : 'Supervisor / Manajer: Otorisasi persetujuan berjenjang sebelum transaksi bernilai tinggi atau perubahan data krusial dieksekusi.',
            'badge' => 'Aktor & RBAC',
        ];

        $allSuggestions['refund_flow'] = [
            'id' => 'refund_flow',
            'category' => 'workflow',
            'title' => $isEn ? 'Cancellation & Refund Workflow' : 'Alur Pembatalan & Pengembalian Dana',
            'desc' => $isEn ? 'Establish transparent guidelines and automated steps for customer order cancellation and refund claims.' : 'Definisikan alur resmi penanganan pembatalan pesanan, verifikasi alasan, dan pencatatan pengembalian dana (refund).',
            'target_field' => 'alurKerja',
            'addition' => $isEn ? 'Cancellation & Refund Procedure: Client submits request with reason -> Admin inspects validity -> Automated refund ledger adjustment and notification dispatch.' : 'Alur Pembatalan & Pengembalian Dana: Klien mengajukan pembatalan dengan alasan -> Staf/Admin memverifikasi keabsahan -> Penyesuaian saldo dan pengiriman bukti refund otomatis.',
            'badge' => 'Alur Kerja',
        ];

        $allSuggestions['audit_trail'] = [
            'id' => 'audit_trail',
            'category' => 'security',
            'title' => $isEn ? 'Immutable Security Audit Trail' : 'Audit Trail & Rekam Jejak Keamanan',
            'desc' => $isEn ? 'Record who created, edited, or deleted records with user ULID and timestamp to ensure high compliance.' : 'Pencatatan riwayat setiap kali data diubah atau dihapus, lengkap dengan identitas pengguna, IP, dan timestamp.',
            'target_field' => 'kepatuhanKeamanan',
            'addition' => $isEn ? 'Immutable Security Audit Trail: Complete forensic logging of who modified or deleted critical records with timestamps and IP records.' : 'Audit Trail & Rekam Jejak Forensik: Pencatatan otomatis setiap aksi perubahan/penghapusan data krusial lengkap dengan identitas pengguna dan timestamp.',
            'badge' => 'Keamanan',
        ];

        $allSuggestions['google_sso'] = [
            'id' => 'google_sso',
            'category' => 'feature',
            'title' => $isEn ? '1-Click Google Sign-In (OAuth)' : 'Login 1-Klik Google (Google SSO)',
            'desc' => $isEn ? 'Allow users to register and sign in effortlessly using their Google account without memorizing passwords.' : 'Permudah klien dan staf masuk ke sistem dengan sekali klik menggunakan akun Google resmi tanpa menghafal password baru.',
            'target_field' => 'fiturWajib',
            'addition' => $isEn ? 'Single Sign-On (SSO): 1-click Google OAuth 2.0 authentication for frictionless client onboarding.' : 'Autentikasi 1-Klik Google Sign-In (OAuth 2.0) untuk mempercepat pendaftaran dan kenyamanan login pengguna.',
            'badge' => 'Fitur MVP',
        ];

        // 4. Domain-Specific Architectural Additions (Prioritized for Domain Relevance)
        $domainSuggestions = [];
        if ($domain === 'property') {
            $domainSuggestions['google_maps'] = [
                'id' => 'google_maps_embed',
                'category' => 'integration',
                'title' => $isEn ? 'Google Maps Embed & Amenities POI' : 'Google Maps Embed & Fasilitas Sekitar (POI)',
                'desc' => $isEn ? 'Embed interactive maps with neighborhood POIs (beaches, airports, cafes) to help prospective buyers survey locations.' : 'Peta interaktif titik lokasi properti/villa dan jarak ke fasilitas terdekat (pantai, bandara, restoran) untuk survey.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'Google Maps Embed API & Geolocation Amenities POI',
                'badge' => 'Maps & Lokasi',
            ];
            $domainSuggestions['channel_sync'] = [
                'id' => 'ical_channel_sync',
                'category' => 'integration',
                'title' => $isEn ? 'Calendar & iCal Channel Sync' : 'Kalender Ketersediaan & Sinkronisasi iCal',
                'desc' => $isEn ? 'Real-time booking calendar with 2-way iCal sync to Airbnb / Booking.com preventing double bookings.' : 'Kalender booking interaktif dengan sinkronisasi iCal dua arah (Airbnb/Booking.com) mencegah overbooking.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => 'iCal Two-Way Availability Calendar Sync (Pencegahan Double Booking)',
                'badge' => 'Kalender & Sync',
            ];
            $domainSuggestions['currency_converter'] = [
                'id' => 'currency_converter',
                'category' => 'feature',
                'title' => $isEn ? 'Multi-Currency Price Display (IDR/USD/AUD)' : 'Tampilan Multi-Valas (IDR, USD, AUD)',
                'desc' => $isEn ? 'Live currency exchange display for foreign tourists & international investors surveying villa listings.' : 'Estimasi harga sewa/beli villa dalam valuta asing secara real-time untuk calon penyewa mancanegara.',
                'target_field' => 'fiturWajib',
                'addition' => 'Modul Konversi Kurs Multi-Valas (IDR, USD, AUD, EUR) Real-Time',
                'badge' => 'Fitur MVP',
            ];
            $domainSuggestions['virtual_tour'] = [
                'id' => 'virtual_tour_360',
                'category' => 'feature',
                'title' => $isEn ? '360° Virtual Tour & Video Walkthrough' : 'Tur Virtual 360° & Video Walkthrough',
                'desc' => $isEn ? 'Interactive panorama viewer and HD video walkthrough for immersive remote property inspection.' : 'Penyematan tur panorama 360 derajat dan video walkthrough HD untuk inspeksi properti jarak jauh.',
                'target_field' => 'fiturWajib',
                'addition' => 'Modul Tur Virtual Interaktif Panorama 360° & Video Walkthrough HD',
                'badge' => 'Fitur MVP',
            ];
        } elseif ($domain === 'logistics') {
            $domainSuggestions['pod_signature'] = [
                'id' => 'pod_signature',
                'category' => 'feature',
                'title' => $isEn ? 'Digital Signature on Delivery (e-POD)' : 'Tanda Tangan Digital Driver (e-POD)',
                'desc' => $isEn ? 'Allow driver to capture recipient signature on screen upon package handover.' : 'Penerima menandatangani langsung serah terima barang di layar smartphone kurir/driver sebagai bukti sah.',
                'target_field' => 'fiturWajib',
                'addition' => $isEn ? 'Digital Signature & Proof of Delivery (e-POD): Recipient signs on mobile touchscreen upon parcel receipt with GPS timestamp.' : 'Tanda Tangan Digital & Bukti Serah Terima (e-POD): Penerima menandatangani langsung di layar smartphone kurir dilengkapi koordinat GPS dan foto fisik.',
                'badge' => 'Fitur MVP',
            ];
        } elseif ($domain === 'clinic') {
            $domainSuggestions['satusehat'] = [
                'id' => 'satusehat_integration',
                'category' => 'integration',
                'title' => $isEn ? 'SatuSehat Kemenkes (FHIR API)' : 'Integrasi SatuSehat Kemenkes (FHIR)',
                'desc' => $isEn ? 'Synchronize patient clinical encounters with the national health data exchange.' : 'Sinkronisasi rekam medis dan data kunjungan pasien dengan platform SatuSehat Kementerian Kesehatan RI.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'SatuSehat Ministry of Health FHIR API bi-directional medical record bridging' : 'SatuSehat Kemenkes RI (FHIR Interoperability API) untuk standardisasi rekam medis nasional',
                'badge' => 'Integrasi',
            ];
        } elseif ($domain === 'marketplace') {
            $domainSuggestions['courier_rates'] = [
                'id' => 'courier_rates',
                'category' => 'integration',
                'title' => $isEn ? 'Automated Courier Shipping Rates' : 'Kalkulasi Ongkir Kurir Otomatis',
                'desc' => $isEn ? 'Calculate real-time shipping costs for JNE, SiCepat, J&T based on destination sub-district.' : 'Hitung tarif ongkos kirim real-time (JNE, SiCepat, J&T) secara otomatis berdasarkan kota/kecamatan tujuan.',
                'target_field' => 'kebutuhanIntegrasi',
                'addition' => $isEn ? 'Multi-courier Shipping API (JNE, SiCepat, J&T) for automated destination freight calculation' : 'API Ekspedisi Multi-Kurir (JNE, SiCepat, J&T) untuk kalkulasi ongkos kirim otomatis berdasarkan kecamatan tujuan',
                'badge' => 'Integrasi',
            ];
        }

        // Prioritize domain-specific suggestions before generic features
        $orderedCatalog = array_merge($domainSuggestions, $allSuggestions);

        $suggestions = [];
        foreach ($orderedCatalog as $key => $item) {
            $keyword = strtolower($item['id']);
            if (!str_contains($corpusLower, $keyword) && count($suggestions) < 8) {
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

    /**
     * Deep Multi-Model AI Enhancement (DeepSeek-R1, Claude 3.7, Gemini 2.5, OpenAI, Grok, Groq)
     * with automatic failover and token circuit breaker.
     */
    protected function enhanceWithMultiAi(array $result, string $corpus, string $locale, ?string $preferredProvider = null, array $brief = []): array
    {
        $isEn = ($locale === 'en');
        $langName = $isEn ? 'English' : 'Indonesian';
        $noPaymentGateway = (bool) preg_match('/(?:tanpa|tidak\s*pakai|tanpa\s*adanya|no|without)\s*(?:payment\s*gateway|midtrans|gerbang\s*pembayaran)/i', $corpus);

        $briefGuidance = "";
        if (!empty($brief)) {
            $briefGuidance = "\nCLIENT EXPLICIT BRIEF (MUST HONOR, ADOPT, AND PRESERVE):\n";
            foreach ($brief as $k => $v) {
                if (is_string($v) && trim($v) !== '') {
                    $briefGuidance .= "- {$k}: " . trim($v) . "\n";
                }
            }
            $briefGuidance .= "CRITICAL INSTRUCTION: Adopt the client's explicit business name, core problem, goals, actors, and features directly. Elaborate on them with architectural rigor, but DO NOT overwrite or delete their specific items. If they explicitly requested 'tanpa payment gateway', DO NOT include any payment gateway in integrations or workflow.\n";
        }

        // Hierarchical Map-Reduce synthesis for large documents to preserve all chapters/pages without middle-page loss
        $promptCorpus = $this->extractComprehensiveCorpusDigest($corpus, $isEn);

        $prompt = <<<PROMPT
You are a Principal Software Solutions Architect synthesizing a client's project vision.
Analyze the following idea text and attached document contents:
{$promptCorpus}
{$briefGuidance}

Provide an architectural synthesis in valid JSON format with keys:
- "namaBisnis": Clean, professional project or company name.
- "masalahUtama": Deep, compelling problem statement (pain points, root causes, inefficiencies).
- "tujuanUtama": Clear measurable success metrics and target outcomes (KPIs, automation goals).
- "targetAudiens": Key target audience demographics and user segments.
- "aktorSistem": Numbered list of system actors and roles with RBAC responsibilities (e.g., 1. Superadmin, 2. Operator, 3. Customer).
- "fiturWajib": Numbered list (1 to 6) of mission-critical Phase 1 MVP features.
- "fiturTambahan": Numbered list (1 to 4) of Phase 2 roadmap features.
- "alurKerja": Numbered step-by-step user and transaction workflow.
- "kebutuhanIntegrasi": Comma-separated list of required third-party services and integrations (e.g. WhatsApp Click-to-Chat API, Google Maps Embed, Cloudflare R2).
- "outOfScope": Clear boundaries of what is explicitly excluded in Phase 1 to prevent scope creep.
- "strategicInsight": 1-2 sentence executive architectural verdict or technical competitive advantage.

Language: Strictly write values in {$langName}.
Output JSON ONLY, no markdown fences or conversational text.
PROMPT;

        $systemInstruction = "You are an elite Enterprise Software Solutions Architect specializing in Laravel 13 Modern Monolith, PostgreSQL, and strict O(1) performance.";

        try {
            $aiRes = MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'discovery', $preferredProvider);

            if (!empty($aiRes['text'])) {
                $jsonText = trim($aiRes['text']);
                if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $jsonText, $m)) {
                    $jsonText = trim($m[1]);
                }
                $parsed = json_decode($jsonText, true);

                if (is_array($parsed)) {
                    $isAdventist = ($result['domain'] ?? '') === 'education_adventist' || stripos($corpus, 'sekolah advent') !== false;

                    foreach (['namaBisnis', 'masalahUtama', 'tujuanUtama', 'targetAudiens', 'kebutuhanIntegrasi', 'outOfScope'] as $field) {
                        if (!empty($parsed[$field]) && is_string($parsed[$field])) {
                            // If user provided explicit brief for this field, do not overwrite if AI diverged drastically
                            if (!empty($brief[$field]) && in_array($field, ['namaBisnis'])) {
                                $result[$field] = $brief[$field];
                            } else {
                                $val = trim($parsed[$field]);
                                if (mb_strlen($val) > 2500) {
                                    $val = mb_substr($val, 0, 2500);
                                }
                                $result[$field] = $val;
                            }
                        }
                    }

                    foreach (['aktorSistem', 'fiturWajib', 'fiturTambahan', 'alurKerja'] as $listField) {
                        // For Adventist Education, protect the canonical 15 RBAC actors
                        if ($listField === 'aktorSistem' && $isAdventist && !empty($result['aktorSistem'])) {
                            continue;
                        }

                        if (!empty($parsed[$listField])) {
                            $val = null;
                            if (is_array($parsed[$listField])) {
                                $lines = [];
                                foreach ($parsed[$listField] as $i => $item) {
                                    $lines[] = ($i + 1) . '. ' . ltrim(preg_replace('/^\d+[\.\)]\s*/', '', (string)$item));
                                }
                                $val = implode("\n", $lines);
                            } elseif (is_string($parsed[$listField])) {
                                $val = trim($parsed[$listField]);
                            }

                            if ($val !== null) {
                                if (mb_strlen($val) > 2500) {
                                    $val = mb_substr($val, 0, 2500);
                                }
                                $result[$listField] = $val;
                            }
                        }
                    }

                    // Enforce constraints post-AI
                    if ($noPaymentGateway) {
                        $result['kebutuhanIntegrasi'] = preg_replace('/\s*\([^)]*(?:payment\s*gateway|midtrans|tanpa)[^)]*\)/i', '', $result['kebutuhanIntegrasi'] ?? '');
                        $result['kebutuhanIntegrasi'] = trim(preg_replace('/\b(?:midtrans(?:\s*snap)?|stripe|xendit|payment\s*gateway|qris\s*otomatis)[,\s]*/i', '', $result['kebutuhanIntegrasi']), ", \t\n\r");
                        if (empty($result['kebutuhanIntegrasi'])) {
                            $result['kebutuhanIntegrasi'] = $isEn ? 'WhatsApp Click-to-Chat API, Google Maps Embed' : 'WhatsApp Click-to-Chat API, Google Maps Embed';
                        }
                        if (!empty($result['outOfScope']) && !stripos($result['outOfScope'], 'payment gateway')) {
                            $result['outOfScope'] = $this->appendNumberedItem($result['outOfScope'], $isEn 
                                ? 'No online payment gateway integration in Phase 1 (leads and bookings routed directly via WhatsApp chat).'
                                : 'Tidak mencakup integrasi payment gateway otomatis di Fase 1 (seluruh inquiry dan reservasi diarahkan langsung via WhatsApp chat).');
                        }
                    }

                    // If brief had explicit features, ensure they are present
                    if (!empty($brief['fiturWajib']) && !empty($result['fiturWajib'])) {
                        // User's explicit features are king
                        $result['fiturWajib'] = $this->formatListIfNeeded($brief['fiturWajib']);
                    }
                    if (!empty($brief['namaBisnis'])) {
                        $result['namaBisnis'] = $brief['namaBisnis'];
                    }
                    if (!empty($brief['kisaranBudget'])) {
                        $result['kisaranBudget'] = $brief['kisaranBudget'];
                    }
                    if (!empty($brief['targetWaktu'])) {
                        $result['targetWaktu'] = $brief['targetWaktu'];
                    }

                    $result['_meta']['ai_telemetry'] = [
                        'provider' => $aiRes['provider'],
                        'provider_name' => $aiRes['provider_name'],
                        'model' => $aiRes['model'],
                        'fallback_occurred' => $aiRes['fallback_occurred'],
                        'failed_attempts' => $aiRes['failed_attempts'],
                        'notification' => $aiRes['notification'],
                        'strategic_insight' => $parsed['strategicInsight'] ?? null,
                    ];

                    return $result;
                }
            }

            $result['_meta']['ai_telemetry'] = [
                'provider' => $aiRes['provider'] ?? 'deterministic_heuristic',
                'provider_name' => $aiRes['provider_name'] ?? 'Neriah Pro Deterministic Engine',
                'model' => $aiRes['model'] ?? 'Heuristic Rules',
                'fallback_occurred' => false,
                'failed_attempts' => $aiRes['failed_attempts'] ?? [],
                'notification' => null,
                'strategic_insight' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('MultiAi enhancement notice: ' . $e->getMessage());
            $result['_meta']['ai_telemetry'] = [
                'provider' => 'deterministic_heuristic',
                'provider_name' => 'Neriah Pro Deterministic Engine',
                'model' => 'Heuristic Rules',
                'fallback_occurred' => false,
                'failed_attempts' => [],
                'notification' => null,
                'strategic_insight' => null,
            ];
        }

        return $result;
    }

    protected function appendNumberedItem(string $existingText, string $newItem): string
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", trim($existingText)))));
        $nextNum = count($lines) + 1;
        $cleanedItem = preg_replace('/^\d+[\.\)]\s*/', '', $newItem);
        $lines[] = "{$nextNum}. {$cleanedItem}";
        return implode("\n", $lines);
    }

    /**
     * Hierarchical Sectional Digest for Large Documents (>15,000 characters).
     * Prevents truncation of middle pages (e.g. pages 10-30 in 40-page TOR/SOP documents)
     * by breaking the document into logical sections/chapters and preserving key architectural specs.
     */
    public static function extractComprehensiveCorpusDigest(string $corpus, bool $isEn = false, int $maxBudget = 14000): string
    {
        $totalLength = mb_strlen($corpus);
        if ($totalLength <= $maxBudget) {
            return $corpus;
        }

        // Split corpus into distinct sections/chapters by Markdown headers or numbered chapters (case-insensitive)
        $sections = preg_split('/(?=(?:^#+\s+|^(?:bagian|bab|section|modul|chapter)\s+\d+[:\.]|\n{2,}#+|\n{2,}(?:bagian|bab|section|modul|chapter)\s+\d+[:\.]))/miu', $corpus);
        $sections = array_values(array_filter(array_map('trim', $sections ?: [])));

        // If no clean headers found, split into chunk sections of ~2500 chars
        if (count($sections) <= 2) {
            $sections = [];
            $len = mb_strlen($corpus);
            $chunkSize = 2500;
            for ($offset = 0; $offset < $len; $offset += $chunkSize) {
                $sections[] = mb_substr($corpus, $offset, $chunkSize);
            }
        }

        $digestParts = [];
        $totalSections = count($sections);
        $headerNote = $isEn
            ? "### [HIERARCHICAL CORPUS DIGEST: Preserving all {$totalSections} document chapters/sections across {$totalLength} characters without middle-page loss]\n\n"
            : "### [INTISARI DOKUMEN HIERARKIS: Merangkum seluruh {$totalSections} bab/bagian dari {$totalLength} karakter dokumen tanpa kehilangan halaman tengah]\n\n";
        $digestParts[] = $headerNote;

        // Allocate budget per section
        $budgetRemaining = max(1000, $maxBudget - mb_strlen($headerNote) - ($totalSections * 40));
        $charBudgetPerSection = max(300, (int) floor($budgetRemaining / max(1, $totalSections)));

        foreach ($sections as $index => $sec) {
            $secNum = $index + 1;
            $lines = preg_split('/\r\n|\r|\n/', $sec);
            $firstLine = trim($lines[0] ?? "Chapter {$secNum}");

            if (mb_strlen($sec) <= $charBudgetPerSection) {
                $digestParts[] = "#### Section {$secNum}: {$firstLine}\n" . $sec . "\n";
                continue;
            }

            // Extract high-value architectural lines: numbered lists, bullet points, table headers, and key technical phrases
            $highValueLines = [];
            $highValueLines[] = $firstLine;

            foreach ($lines as $lineIdx => $line) {
                if ($lineIdx === 0) continue;
                $tLine = trim($line);
                if (empty($tLine)) continue;

                // Priority: numbered items (1. 2. 3.), bullets (- *), table rows (|), or key architectural markers
                if (preg_match('/^(?:\d+[\.\)]|[-*]|\#|\|)/u', $tLine) || 
                    preg_match('/\b(?:fitur|modul|tabel|kolom|relasi|aktor|role|alur|integrasi|api|endpoint|database|schema|persyaratan|workflow|requirement|specification)\b/iu', $tLine)) {
                    $highValueLines[] = $tLine;
                }
            }

            $condensed = implode("\n", $highValueLines);
            if (mb_strlen($condensed) > $charBudgetPerSection) {
                $condensed = mb_substr($condensed, 0, $charBudgetPerSection) . "\n... [Section {$secNum} summarized] ...";
            }

            $digestParts[] = "#### Section {$secNum}: {$firstLine}\n" . $condensed . "\n";
        }

        $finalDigest = implode("\n", $digestParts);
        if (mb_strlen($finalDigest) > $maxBudget) {
            $finalDigest = mb_substr($finalDigest, 0, $maxBudget - 60) . "\n... [Digest bounded to model context budget] ...";
        }

        return $finalDigest;
    }
}

