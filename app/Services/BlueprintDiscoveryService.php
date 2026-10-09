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
     * Parse structured key-value briefs (e.g. from spreadsheets, tables, or labeled prompts).
     * Supports separators: tab (\t), colon (:), equals (=), dash (-/—).
     */
    public function parseStructuredBrief(string $corpus): array
    {
        $sectionMap = [
            'namaBisnis' => ['nama bisnis', 'nama usaha', 'nama proyek', 'nama brand', 'nama aplikasi', 'nama platform', 'project name', 'business name', 'company name', 'master blueprint', 'blueprint'],
            'masalahUtama' => ['masalah utama', 'masalah', 'permasalahan', 'kendala', 'problem statement', 'pain points', 'core problem', 'profil sekolah', 'profil usaha', 'profil bisnis', 'profil lembaga', 'profil perusahaan', 'latar belakang', 'background', 'deskripsi proyek', 'deskripsi bisnis', 'tentang sekolah', 'tentang usaha'],
            'tujuanUtama' => ['tujuan utama', 'tujuan proyek', 'tujuan bisnis', 'tujuan', 'goals', 'goal', 'primary goal', 'kpi', 'target capaian', 'visi & misi', 'visi misi'],
            'fiturWajib' => ['fitur wajib', 'fitur inti', 'fitur utama', 'fitur mvp', 'fitur fase 1', 'core features', 'mvp features', 'must-have', 'modul wajib', 'modul inti', 'modul utama', 'modul sistem', 'daftar modul', 'modul aplikasi', 'fitur sistem', 'fitur'],
            'fiturTambahan' => ['fitur tambahan', 'fitur fase 2', 'fitur lanjutan', 'roadmap', 'future features', 'nice-to-have', 'modul tambahan', 'fase 2'],
            'aktorSistem' => ['pengguna dan role', 'pengguna & role', 'aktor sistem', 'aktor & role', 'aktor', 'role pengguna', 'hak akses', 'user roles', 'system actors', 'rbac', 'pengguna', 'user role', 'roles', 'role', 'pengguna sistem'],
            'alurKerja' => ['alur kerja', 'alur bisnis', 'alur operasional', 'workflow', 'user journey', 'proses transaksi', 'user flow', 'alur pendaftaran', 'alur proses', 'alur sistem'],
            'kebutuhanIntegrasi' => ['kebutuhan integrasi', 'integrasi api', 'integrasi', 'third party', 'third-party integrations', 'integrations', 'koneksi api', 'gerbang pembayaran'],
            'outOfScope' => ['out of scope', 'batasan', 'ruang lingkup negatif', 'di luar lingkup', 'exclusions', 'non-scope'],
            'kisaranBudget' => ['alokasi budget', 'kisaran budget', 'budget range', 'budget', 'anggaran', 'estimasi investasi', 'biaya'],
            'targetWaktu' => ['target waktu', 'target rilis', 'timeline', 'durasi kerja', 'durasi', 'target hari'],
            'targetPlatform' => ['target platform', 'platform', 'perangkat'],
            'referensiDesain' => ['referensi desain', 'design reference', 'gaya desain', 'desain'],
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
                continue;
            }

            $matchedSection = null;
            $matchedValue = null;

            // Strip markdown headings (##, ###) and bullets (- , * )
            $cleanLine = trim(preg_replace('/^(?:#+\s*|[-*]\s*)+/u', '', $trimmed));
            $cleanLineWithoutColons = trim(rtrim($cleanLine, " :\t-—"));

            // 2. Check structured key-value line: "Header : Value" or "Header - Value" or "Header\tValue"
            if (preg_match('/^([A-Za-z0-9\s\/\(\)&]+?)(?:\t|:\s*|\s*=\s*|\s+[-—]\s+)([\s\S]*)$/u', $cleanLine, $m)) {
                $headerCandidate = strtolower(trim(preg_replace('/\s*\([^)]*\)/', '', $m[1])));
                foreach ($sectionMap as $secKey => $synonyms) {
                    foreach ($synonyms as $syn) {
                        if ($headerCandidate === $syn || str_starts_with($headerCandidate, $syn)) {
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
                        if ($candidateLower === $syn || str_starts_with($candidateLower, $syn)) {
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
                if (!empty($parsed[$currentSection])) {
                    $parsed[$currentSection] .= "\n" . $trimmed;
                } else {
                    $parsed[$currentSection] = $trimmed;
                }
            }
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

        // 2. Synthesize Business / Project Name
        $namaBisnis = !empty($brief['namaBisnis']) ? $brief['namaBisnis'] : $this->extractProjectName($corpus, $domain, $isEn);

        // 3. Synthesize Core Problem
        $masalahUtama = !empty($brief['masalahUtama']) ? $brief['masalahUtama'] : $this->synthesizeProblem($corpus, $domain, $isEn);

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
        $trackRecommendation = $this->evaluateTrackRecommendation($baseData, $corpus, $domain, $isEn);

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
        $studioMvpPrice = \App\Models\CmsGlobalSetting::getVal('pricing_studio_mvp_price', '50.000.000');

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
                        $isEn ? '100% turnkey coding across 7 Software Factory OS pillars' : '100% turnkey koding mencakup 7 Pilar Software Factory OS',
                        $isEn ? 'Managed Capacity & Anti-Collision sprint batch' : 'Alokasi slot sprint terisolasi dengan Master Gantt real-time',
                        $isEn ? 'Legally binding SLA contract & 100% copyright transfer' : 'Kontrak legal SLA bersertifikat & serah terima hak cipta 100%',
                        $isEn ? 'Staged 50% DP escrow payment via Midtrans Snap' : 'Skema pembayaran bertahap DP 50% via Midtrans Snap',
                    ],
                    'starting_price' => "Rp {$studioUmkmPrice}",
                    'max_price' => "Rp {$studioMvpPrice}",
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
            'fnb_culinary' => ['katering', 'catering', 'makanan', 'kuliner', 'dapur', 'resto', 'restoran', 'cafe', 'kafe', 'kopi', 'bakery', 'roti', 'kue', 'menu mingguan', 'paket diet', 'nasi kotak', 'makan siang', 'resep', 'chef', 'dine in', 'takeaway', 'food'],
            'services_workshop' => ['bengkel', 'servis mobil', 'servis motor', 'montir', 'sparepart', 'onderdil', 'salon', 'barbershop', 'spa', 'laundry', 'cuci sepatu', 'cuci mobil', 'perbaikan', 'reparasi', 'mekanik', 'jasa cuci'],
            'property' => ['villa', 'resort', 'homestay', 'properti', 'perumahan', 'apartemen', 'booking villa', 'kavling', 'tanah', 'kost', 'kontrakan', 'real estate', 'agen properti', 'katalog properti', 'listing villa', 'listing properti', 'sewa villa', 'jual villa'],
            'clinic' => ['klinik', 'pasien', 'dokter', 'rekam medis', 'obat', 'apotek', 'rumah sakit', 'antrean poli', 'kesehatan', 'diagnosis', 'medical', 'dental', 'gigi', 'fisioterapi'],
            'finance' => ['keuangan', 'invoice', 'faktur', 'tagihan', 'pembayaran', 'akuntansi', 'kasir', 'pos', 'pembukuan', 'laporan keuangan', 'escrow', 'pajak'],
            'hr' => ['hrd', 'karyawan', 'rekrutmen', 'pelamar', 'lowongan', 'gaji', 'payroll', 'absensi', 'cuti', 'kinerja', 'talent'],
            'education' => ['sekolah', 'kursus', 'siswa', 'guru', 'kelas', 'ujian', 'materi ajar', 'bimbel', 'lms', 'akademik', 'pembelajaran', 'les privat', 'kampus'],
            'logistics' => ['logistik', 'ekspedisi', 'armada truk', 'truk kontainer', 'kontainer', 'pengiriman kargo', 'gudang kargo', 'cargo', 'surat jalan', 'resi ekspedisi', 'freight'],
            'marketplace' => ['marketplace', 'jual beli online', 'toko online', 'ecommerce', 'e-commerce', 'multi vendor', 'keranjang belanja', 'checkout online'],
        ];

        $scores = [];
        foreach ($patterns as $domain => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                if (str_contains($combinedText, $kw)) {
                    $score += str_contains($kw, ' ') ? 3 : 1;
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

        // Domain-tailored defaults
        $defaultsId = [
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
            'fnb_culinary' => "1. Superadmin (Culinary / Catering Owner): Master menu setup, subscription pricing, and revenue audits.\n2. Kitchen Head / Chef: Monitors kitchen batch production queues and daily portion lists.\n3. Delivery Courier: Receives drop-off addresses and recipient contact info via WhatsApp.\n4. Customer (Corporate / Individual): Selects weekly menus, configures meal plans, and receives receipts.",
            'services_workshop' => "1. Superadmin (Workshop Owner / General Manager): Service catalog pricing, labor rates, and spare parts inventory.\n2. Service Advisor: Intake queue registration, Work Order (WO) drafting, and diagnostic symptom verification.\n3. Lead Mechanic: Updates live repair milestones and records installed spare parts.\n4. Vehicle Owner: Online appointment booking, repair status tracker, and digital maintenance logbook.",
            'logistics' => "1. Superadmin: Full control over master fleet catalog, regional rate matrices, and role authorization.\n2. Dispatch Operator: Registers manifests, assigns drivers, and verifies delivery routes.\n3. Driver / Fleet Contractor: Updates transit waypoints, uploads proof of delivery (POD) photo.\n4. Corporate Client: Real-time shipment tracking and automated e-POD / invoice downloads.",
            'marketplace' => "1. Superadmin: Vendor KYC verification, transaction escrow audits, and platform settings.\n2. Vendor Partner: Manages equipment availability, confirms booking requests, and updates terms.\n3. Customer: Searches availability, reserves dates, and completes payment checkout.\n4. Financial Officer: Reconciles escrow releases and vendor settlements.",
            'clinic' => "1. Superadmin: Clinic branches, doctor schedules, and security policy management.\n2. Receptionist: Patient intake, appointment queue management, and card printing.\n3. Physician: Diagnostic inputs, Electronic Medical Records (EMR), and digital prescriptions.\n4. Pharmacist: Prescription validation, medication dispensing, and inventory tracking.",
            'finance' => "1. Superadmin: Bank accounts, payment gateway webhooks, and security audit log.\n2. Finance Operator: Invoice drafting, installment scheduling, and receipt verifications.\n3. Client Payer: Reviews itemized billing, executes payments via VA/QRIS, downloads receipts.\n4. Accounting Auditor: Generates monthly reconciliation reports in Excel and PDF.",
            'hr' => "1. Superadmin: Company roles and access policy control.\n2. HR Recruiter: Job vacancy posting, candidate resume screening, and interview scheduling.\n3. Hiring Manager: Reviews candidates, submits evaluation notes, approves hires.\n4. Candidate: Profile submission, CV upload, and application status tracking.",
            'education' => "1. Superadmin: Academic calendar, user credentials, and curriculum control.\n2. Instructor: Uploads study materials, creates assignments, records student grades.\n3. Student: Reviews course materials, submits homework, checks academic report cards.",
            'property' => "1. Superadmin (Villa Manager / Broker): Full administration over villa inventory, HD photo curation via Curator, pricing tiers, and WhatsApp routing.\n2. Web Visitor (Prospective Tenant / Buyer): Explores catalog, applies location/amenity filters, and initiates survey chats via WhatsApp.\n3. Field Host / Staff: Receives direct inquiry appointments and coordinates physical property walkthroughs.",
            'custom_portal' => "1. Superadmin: Full master administration, security audit trails, and configuration.\n2. Operational Staff: Day-to-day transaction input and verification.\n3. Supervisor / Approver: Multi-tier approval workflow authorization.\n4. External Client: Self-service submission and real-time status tracker.",
        ];

        return $isEn ? ($actorsEn[$domain] ?? $actorsEn['custom_portal']) : ($actorsId[$domain] ?? $actorsId['custom_portal']);
    }

    protected function synthesizeMvpFeatures(string $corpus, string $domain, bool $isEn): string
    {
        $featuresId = [
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

        $prompt = <<<PROMPT
You are a Principal Software Solutions Architect synthesizing a client's project vision.
Analyze the following idea text and attached document contents:
{$corpus}
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
                    foreach (['namaBisnis', 'masalahUtama', 'tujuanUtama', 'targetAudiens', 'kebutuhanIntegrasi', 'outOfScope'] as $field) {
                        if (!empty($parsed[$field]) && is_string($parsed[$field])) {
                            // If user provided explicit brief for this field, do not overwrite if AI diverged drastically
                            if (!empty($brief[$field]) && in_array($field, ['namaBisnis'])) {
                                $result[$field] = $brief[$field];
                            } else {
                                $result[$field] = trim($parsed[$field]);
                            }
                        }
                    }

                    foreach (['aktorSistem', 'fiturWajib', 'fiturTambahan', 'alurKerja'] as $listField) {
                        if (!empty($parsed[$listField])) {
                            if (is_array($parsed[$listField])) {
                                $lines = [];
                                foreach ($parsed[$listField] as $i => $item) {
                                    $lines[] = ($i + 1) . '. ' . ltrim(preg_replace('/^\d+[\.\)]\s*/', '', (string)$item));
                                }
                                $result[$listField] = implode("\n", $lines);
                            } elseif (is_string($parsed[$listField])) {
                                $result[$listField] = trim($parsed[$listField]);
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
}

