<?php

declare(strict_types=1);

namespace App\Services\Blueprint;

use App\Models\Transaction;
use App\Models\VisionBlueprint;
use App\Services\Ai\MultiAiModelManager;
use App\Services\BlueprintDiscoveryService;
use App\Services\Entitlement\BlueprintEntitlementGateService;
use App\Services\Prd\PrdFeatureSpecificationService;
use App\Services\PrdGeneratorService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * AI Architecture Advisory & Tech Stack Evolution Service.
 * 
 * Provides domain-specific missing architecture feature discovery and 2026 modern tech stack upgrades.
 * Strictly gated: Only accessible by officially purchased licenses (Retail Pro/Ultimate or Mitra Turnkey).
 * Free demo sandboxes are blocked server-side with zero leak.
 */
class BlueprintArchitectureAdvisorService
{
    public function __construct(
        protected BlueprintEntitlementGateService $gateService
    ) {}

    /**
     * Scan current blueprint for missing essential features and modern tech stack recommendations.
     * Guaranteed zero-leak: Throws AuthorizationException if the blueprint is on a free demo/spark sandbox.
     *
     * @throws AuthorizationException
     */
    public function scan(VisionBlueprint $blueprint, string $locale = 'id', ?string $aiProvider = null): array
    {
        $isEn = ($locale === 'en');

        // 1. STRICT ZERO-LEAK SERVER-SIDE GATE
        if (! $this->gateService->isPurchasedPackage($blueprint, auth()->user())) {
            throw new AuthorizationException(
                $isEn
                    ? 'AI Architecture Advisory & Tech Radar is an exclusive premium feature for officially purchased licenses (Retail Pro/Ultimate or Mitra Turnkey). Free demo sandboxes cannot access proprietary advisory intelligence.'
                    : 'Fitur AI Architecture Advisory & Tech Radar eksklusif untuk proyek berlisensi resmi (Retail Pro/Ultimate atau Mitra Turnkey). Versi demo gratis tidak dapat mengakses inteligensi arsitektur ini.'
            );
        }

        $prd = $blueprint->prd_content ?? [];
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: ($isEn ? 'Enterprise System' : 'Sistem Enterprise'));
        $sector = $prd['universal_blueprint_schema']['sectoral_ui_ux_archetype']['archetype_label'] 
            ?? ($prd['project_metadata']['domain_category'] ?? 'Enterprise Digital Platform');

        // Extract existing feature titles
        $mvpFeatures = $prd['features']['mvp_phase1'] ?? ($prd['engineering_specs']['mvp_specs'] ?? []);
        $roadmapFeatures = $prd['features']['roadmap_phase2'] ?? [];
        $existingTitles = [];
        $existingCorpus = '';

        foreach (array_merge($mvpFeatures, $roadmapFeatures) as $f) {
            if (! is_array($f)) continue;
            $title = $f['title'] ?? '';
            $desc = $f['desc'] ?? '';
            if (! empty($title)) {
                $existingTitles[] = $title;
                $existingCorpus .= mb_strtolower($title . ' ' . $desc . ' ');
            }
        }

        // 2. Discover Domain-Specific Missing Essential Features
        $missingFeatures = $this->discoverMissingFeatures($blueprint, $existingTitles, $existingCorpus, $locale, $aiProvider);

        // 3. Compile Modern 2026 Tech Stack Upgrades
        $techUpgrades = $this->compileTechStackUpgrades($blueprint, $locale);

        return [
            'success' => true,
            'is_purchased' => true,
            'business_name' => $businessName,
            'sector' => $sector,
            'current_feature_count' => count($existingTitles),
            'missing_features' => $missingFeatures,
            'tech_upgrades' => $techUpgrades,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Integrate selected recommendations directly into the blueprint's PRD content, ERD, and Tech Stack.
     * Recalculates SHA-256 seal and itemized costs.
     *
     * @throws AuthorizationException
     */
    public function integrate(
        VisionBlueprint $blueprint,
        array $selectedFeatureIds,
        array $selectedTechUpgrades,
        array $cachedRecommendations,
        string $locale = 'id'
    ): array {
        $isEn = ($locale === 'en');

        // 1. STRICT ZERO-LEAK SERVER-SIDE GATE
        if (! $this->gateService->isPurchasedPackage($blueprint, auth()->user())) {
            throw new AuthorizationException(
                $isEn
                    ? 'AI Architecture Advisory integration is an exclusive premium feature for officially purchased licenses.'
                    : 'Integrasi AI Architecture Advisory eksklusif untuk proyek berlisensi resmi.'
            );
        }

        // 2. Guard against Scope Freeze (for non-retail turnkey projects)
        $isRetail = $blueprint->isRetailLicense();
        $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
        if (! $isRetail && ! $isSuperAdmin && $blueprint->isScopeFrozen()) {
            throw new \RuntimeException(
                $isEn
                    ? 'This blueprint is digitally locked with SHA-256 seal. New features must be processed via formal Addendum.'
                    : 'Dokumen blueprint ini telah terkunci secara digital (Scope Freeze). Penambahan fitur baru harus melalui Addendum resmi.'
            );
        }

        $prd = $blueprint->prd_content ?? [];
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise');
        $erdTables = $prd['erd_schema']['tables'] ?? ($prd['database_erd'] ?? []);
        $actors = $prd['system_actors'] ?? [];
        $clientActor = $actors[0]['name'] ?? ($isEn ? 'Client / User' : 'Klien / Pengguna');
        $primaryActor = $actors[1]['name'] ?? ($isEn ? 'System Administrator' : 'Administrator');

        $mvpFeatures = $prd['features']['mvp_phase1'] ?? ($prd['engineering_specs']['mvp_specs'] ?? []);
        $roadmapFeatures = $prd['features']['roadmap_phase2'] ?? [];

        $integratedFeatures = [];
        $nextMvpIndex = count($mvpFeatures) + 1;
        $nextRoadmapIndex = count($roadmapFeatures) + 1;

        // Map cached recommendations by ID
        $recMap = [];
        foreach ($cachedRecommendations as $rec) {
            if (isset($rec['id'])) {
                $recMap[$rec['id']] = $rec;
            }
        }

        // Process each approved missing feature
        foreach ($selectedFeatureIds as $featId) {
            $rec = $recMap[$featId] ?? null;
            if (! $rec) continue;

            $title = $rec['title'] ?? 'New Essential Feature';
            $desc = $rec['desc'] ?? ($rec['why_critical'] ?? '');
            $phase = $rec['proposed_phase'] ?? 'mvp';

            $targetIndex = ($phase === 'phase2') ? $nextRoadmapIndex++ : $nextMvpIndex++;
            $prefix = ($phase === 'phase2') ? 'RDM' : 'MVP';

            // Generate full 6-dimensional feature specification
            $domainSpec = PrdFeatureSpecificationService::analyzeFeatureDomain(
                $title,
                $desc,
                $businessName,
                $clientActor,
                $primaryActor,
                $targetIndex,
                $prefix,
                $erdTables,
                $isEn
            );

            $pascal = PrdGeneratorService::sanitizeEntityClassName($title, $domainSpec['related_table'] ?? null);

            $backend = $domainSpec['backend'] ?? [];
            if (! is_array($backend)) {
                $backend = [];
            }
            if (empty($backend['target_files'])) {
                $backend['target_files'] = [
                    "app/Models/{$pascal}.php",
                    "app/Services/{$pascal}Service.php",
                    "app/Http/Controllers/{$pascal}Controller.php",
                    "tests/Feature/{$pascal}ContractTest.php",
                ];
            }
            if (empty($backend['architecture'])) {
                $backend['architecture'] = 'Laravel 13 Service Layer with Strict ULID and Keyset Cursor O(1).';
            }
            if (empty($backend['ulid_migration_rules'])) {
                $backend['ulid_migration_rules'] = 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid.';
            }
            if (empty($backend['query_strategy'])) {
                $backend['query_strategy'] = 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.';
            }

            $formattedFeature = [
                'id' => sprintf('FEAT-%s-%02d', $prefix, $targetIndex),
                'index' => $targetIndex,
                'tier' => ($phase === 'phase2') ? 'phase2' : 'mvp',
                'title' => $title,
                'desc' => $desc,
                'category' => $domainSpec['category'] ?? 'CORE_DOMAIN',
                'category_label' => $domainSpec['category_label'] ?? 'Modul Rekayasa',
                'related_table' => $domainSpec['related_table'] ?? Str::snake(Str::plural($pascal)),
                'story_points' => 5,
                'complexity_label' => $isEn ? 'Standard (5 SP)' : 'Standar (5 SP)',
                'sprint_phase' => ($phase === 'phase2') ? 'Fase 2 Roadmap' : 'Sprint 2',
                'user_story' => $domainSpec['user_story'] ?? ($isEn ? "As an authorized actor, I want to execute {$title}." : "Sebagai aktor berwenang, saya ingin menjalankan {$title}."),
                'acceptance_criteria' => $domainSpec['acceptance_criteria'] ?? [],
                'frontend' => $domainSpec['frontend'] ?? [
                    'design_tokens' => 'Clean solid monochrome, high-contrast, WCAG AAA.',
                    'states' => 'Loading skeleton, active state, error callout.',
                    'components' => ["{$pascal}View.blade.php"],
                    'tasks' => ['Build user-facing interface component.'],
                ],
                'backend' => $backend,
                'integration' => $domainSpec['integration'] ?? [
                    'endpoint' => 'POST /api/v1/' . Str::kebab(Str::plural($pascal)),
                    'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                ],
                'target_files' => $backend['target_files'],
            ];

            if ($phase === 'phase2') {
                $roadmapFeatures[] = $formattedFeature;
            } else {
                $mvpFeatures[] = $formattedFeature;
            }

            // If feature references a new table not yet in ERD, synthesize table
            $targetTableName = $formattedFeature['related_table'];
            $existingTableNames = collect($erdTables)->map(fn($t) => $t['name'] ?? ($t['table_name'] ?? ''))->all();
            if (! in_array($targetTableName, $existingTableNames, true)) {
                $erdTables[] = [
                    'name' => $targetTableName,
                    'table_name' => $targetTableName,
                    'description' => "Ledger data persistensi untuk modul {$title}",
                    'primary_key' => 'id',
                    'columns' => [
                        ['name' => 'id', 'type' => 'VARCHAR(26)', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'Strict ULID Primary Key'],
                        ['name' => 'session_id', 'type' => 'VARCHAR(26)', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Foreign ULID Sesi'],
                        ['name' => 'status', 'type' => 'VARCHAR(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Status state machine'],
                        ['name' => 'payload', 'type' => 'JSONB', 'index' => '', 'nullable' => true, 'notes' => 'Metadata JSON terenkripsi'],
                        ['name' => 'created_at', 'type' => 'TIMESTAMP', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Waktu pembuatan berindeks O(1)'],
                        ['name' => 'updated_at', 'type' => 'TIMESTAMP', 'index' => '', 'nullable' => false, 'notes' => 'Waktu pembaruan'],
                    ],
                ];
            }

            $integratedFeatures[] = $title;
        }

        // Apply updated features to PRD
        $prd['features']['mvp_phase1'] = $mvpFeatures;
        $prd['engineering_specs']['mvp_specs'] = $mvpFeatures;
        $prd['features']['roadmap_phase2'] = $roadmapFeatures;
        $prd['erd_schema']['tables'] = $erdTables;
        $prd['database_erd'] = $erdTables;

        // Apply selected tech upgrades to universal blueprint schema
        if (! empty($selectedTechUpgrades)) {
            $currentStack = $prd['universal_blueprint_schema']['technology_stack'] ?? [];
            foreach ($selectedTechUpgrades as $upKey) {
                $currentStack[$upKey] = [
                    'active' => true,
                    'integrated_at' => now()->toIso8601String(),
                    'standard' => '2026_ENTERPRISE_WORLD_CLASS',
                ];
            }
            $prd['universal_blueprint_schema']['technology_stack'] = $currentStack;
        }

        // Regenerate ERD mermaid diagrams
        $prd['virtual_charts']['erd_mermaid'] = PrdGeneratorService::generateErdMermaid($erdTables);
        $prd['virtual_charts']['gantt_mermaid'] = PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja');

        // Recalculate itemized cost breakdown
        $itemized = PrdGeneratorService::calculateItemizedEstimation($blueprint, [], $isEn);
        $prd['itemized_cost_breakdown'] = $itemized;

        // Update blueprint
        $blueprint->prd_content = $prd;
        $blueprint->document_sha256 = $blueprint->calculatePrdHash();
        $blueprint->save();

        return [
            'success' => true,
            'message' => $isEn 
                ? 'Selected architecture features & tech upgrades have been successfully integrated into your Blueprint!' 
                : 'Fitur arsitektur dan modernisasi teknologi terpilih berhasil disetujui & diintegrasikan ke dalam Blueprint Anda!',
            'added_features' => $integratedFeatures,
            'added_count' => count($integratedFeatures),
            'document_sha256' => $blueprint->document_sha256,
        ];
    }

    /**
     * Discover missing features tailored to the blueprint's domain corpus.
     */
    protected function discoverMissingFeatures(
        VisionBlueprint $blueprint,
        array $existingTitles,
        string $existingCorpus,
        string $locale,
        ?string $aiProvider = null
    ): array {
        $isEn = ($locale === 'en');
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise');
        $corpus = mb_strtolower($blueprint->nama_bisnis . ' ' . $blueprint->masalah_utama . ' ' . $blueprint->fitur_wajib . ' ' . $existingCorpus);

        // Pre-defined high-value domain catalogs
        $catalogs = [
            'audio_church' => [
                'match' => preg_match('/(?:audio|language|bahasa|penerjemah|interpreter|church|gereja|ibadah|khotbah|webrtc|stream)/i', $corpus),
                'items' => [
                    [
                        'id' => 'ADVISORY-FEAT-QNA',
                        'title' => $isEn ? 'Attendee Real-Time Q&A & Prayer Request Channel' : 'Kanal Interaktif Tanya Jawab & Permohonan Doa Jemaat',
                        'desc' => $isEn
                            ? 'Dedicated text channel allowing attendees to submit sermon questions or prayer requests directly to the multimedia desk without interrupting live audio.'
                            : 'Kanal teks interaktif yang memungkinkan jemaat mengirimkan pertanyaan khotbah atau permohonan doa langsung ke meja operator multimedia tanpa mengganggu streaming suara khotbah.',
                        'why_critical' => $isEn
                            ? 'Without a structured Q&A channel, attendee engagement drops and prayer requests are lost across unmonitored external chat apps.'
                            : 'Tanpa kanal interaktif khusus, permohonan doa jemaat tercecer di aplikasi pesan eksternal dan tidak dapat dimoderasi secara terpusat oleh tim pastoral gereja.',
                        'proposed_phase' => 'mvp',
                        'category' => 'INTERACTION_ENGAGEMENT',
                        'impact_rating' => 'HIGH VALUE',
                    ],
                    [
                        'id' => 'ADVISORY-FEAT-TELEMETRY',
                        'title' => $isEn ? 'Live Audio Stream Quality Telemetry & Jitter Alert' : 'Telemetri Kualitas Audio Real-Time & Peringatan Jitter Jaringan',
                        'desc' => $isEn
                            ? 'Real-time telemetry monitor visualizing listener packet loss, audio buffer health, and silent stream detection with automated operator sound alerts.'
                            : 'Dasbor telemetri real-time yang memvisualisasikan packet loss pendengar, kesehatan buffer audio Opus, dan deteksi audio hening (silent stream) dengan alarm notifikasi otomatis ke operator.',
                        'why_critical' => $isEn
                            ? 'Operators need immediate notification when an interpreter microphone drops or local Wi-Fi degrades before complaints from the congregation.'
                            : 'Operator perlu mendeteksi segera saat mikrofon penerjemah terputus atau koneksi Wi-Fi jemaat terdegradasi sebelum timbul komplain dari ruang ibadah.',
                        'proposed_phase' => 'mvp',
                        'category' => 'RELIABILITY_OBSERVABILITY',
                        'impact_rating' => 'MISSION CRITICAL',
                    ],
                    [
                        'id' => 'ADVISORY-FEAT-ARCHIVE',
                        'title' => $isEn ? 'Automated Sermon Audio Recording & Multi-Language Archive' : 'Perekaman Otomatis Audio Khotbah & Arsip Multi-Bahasa',
                        'desc' => $isEn
                            ? 'Automated background audio recording of all language channels during live sessions, generating downloadable MP3/AAC archives post-service.'
                            : 'Perekaman otomatis latar belakang untuk seluruh kanal bahasa selama sesi ibadah langsung, menghasilkan arsip audio MP3/AAC siap unduh pasca-ibadah.',
                        'why_critical' => $isEn
                            ? 'Preserves pastoral records and enables congregation members who missed the live sermon to listen on-demand.'
                            : 'Mendokumentasikan arsip khotbah gereja dan memungkinkan jemaat yang berhalangan hadir untuk mendengarkan kembali rekaman terjemahan secara on-demand.',
                        'proposed_phase' => 'phase2',
                        'category' => 'CONTENT_ARCHIVE',
                        'impact_rating' => 'RECOMMENDED',
                    ],
                ],
            ],
            'fleet_logistics' => [
                'match' => preg_match('/(?:bandara|airport|armada|fleet|driver|supir|transfer|mobil|kendaraan|logistik|antar jemput)/i', $corpus),
                'items' => [
                    [
                        'id' => 'ADVISORY-FEAT-GEOFENCE',
                        'title' => $isEn ? 'Anti-Fraud GPS Geofence & Arrival Milestone Verification' : 'Geofence GPS Anti-Fraud & Verifikasi Kehadiran Titik Temu',
                        'desc' => $isEn
                            ? 'Automated GPS radius validation ensuring drivers have physically reached the airport/hotel meeting point before milestone status updates.'
                            : 'Validasi radius GPS otomatis yang memastikan pengemudi telah benar-benar tiba di area titik temu lobi/bandara sebelum dapat memperbarui status penjemputan.',
                        'why_critical' => $isEn
                            ? 'Prevents premature driver arrival claims and provides tamper-proof evidence in customer disputes.'
                            : 'Mencegah klaim kedatangan sepihak oleh pengemudi nakal dan menyediakan bukti sah telemetri GPS saat terjadi sengketa dengan tamu.',
                        'proposed_phase' => 'mvp',
                        'category' => 'SECURITY_INTEGRITY',
                        'impact_rating' => 'HIGH VALUE',
                    ],
                    [
                        'id' => 'ADVISORY-FEAT-SURCHARGE',
                        'title' => $isEn ? 'Dynamic Toll & High-Season Surcharge Rebalance' : 'Kalkulator Otomatis Biaya Tol & Surcharge Musim Liburan',
                        'desc' => $isEn
                            ? 'Automated adjustment calculating real-time expressway toll tariffs and seasonal peak holiday surcharges.'
                            : 'Kalkulasi otomatis penyesuaian tarif tol jalan tol bandara dan penyesuaian biaya tambahan (surcharge) saat musim liburan puncak (High Season Natal/Tahun Baru).',
                        'why_critical' => $isEn
                            ? 'Protects operating margins from unpredictable road expenses and seasonal price surges.'
                            : 'Melindungi margin operasional mitra transportasi dari lonjakan biaya rute perjalanan dan fluktuasi tarif musiman.',
                        'proposed_phase' => 'mvp',
                        'category' => 'COMMERCIAL_PRICING',
                        'impact_rating' => 'RECOMMENDED',
                    ],
                ],
            ],
            'tour_travel' => [
                'match' => preg_match('/(?:tour|wisata|itinerary|destinasi|hotel|travel|bromo|ijen|bali)/i', $corpus),
                'items' => [
                    [
                        'id' => 'ADVISORY-FEAT-TRAFFIC_BUFFER',
                        'title' => $isEn ? 'Live Traffic Delay Buffer & POI Hours Conflict Guard' : 'Buffer Kemacetan Jalan Riil & Pencegah Bentrok Jam Buka Wisata',
                        'desc' => $isEn
                            ? 'Intelligent transit duration calculator that syncs destination operating hours with live traffic congestion estimates.'
                            : 'Kalkulator durasi transit cerdas yang menyinkronkan jam buka/tutup destinasi wisata dengan estimasi kemacetan jalan riil guna mencegah tamu tiba saat loket sudah tutup.',
                        'why_critical' => $isEn
                            ? 'Eliminates irrational itineraries where travelers arrive at attractions after closing hours due to uncalculated traffic jams.'
                            : 'Mengeliminasi kekecewaan wisatawan yang tiba di objek wisata setelah jam operasional berakhir akibat rute perjalanan yang tidak memperhitungkan kemacetan lalu lintas.',
                        'proposed_phase' => 'mvp',
                        'category' => 'ROUTE_INTELLIGENCE',
                        'impact_rating' => 'HIGH VALUE',
                    ],
                ],
            ],
        ];

        $recommendations = [];

        // Check matched domain catalogs
        foreach ($catalogs as $cat) {
            if ($cat['match']) {
                foreach ($cat['items'] as $item) {
                    $itemTitle = mb_strtolower($item['title']);
                    $isDuplicate = false;
                    foreach ($existingTitles as $et) {
                        $etLower = mb_strtolower($et);
                        if (str_contains($itemTitle, $etLower) || str_contains($etLower, $itemTitle)) {
                            $isDuplicate = true;
                            break;
                        }
                    }
                    if (! $isDuplicate) {
                        $item['selected'] = true;
                        $recommendations[] = $item;
                    }
                }
            }
        }

        // Universal enterprise safeguards if fewer than 2 items discovered
        if (count($recommendations) < 2) {
            $universalItems = [
                [
                    'id' => 'ADVISORY-FEAT-AUDIT',
                    'title' => $isEn ? 'Cryptographic Audit Trail & Data Privacy Export (UU PDP)' : 'Audit Trail Kriptografis & Ekspor Privasi Data (UU PDP No. 27/2022)',
                    'desc' => $isEn
                        ? 'Tamper-proof audit logging recording all administrator mutations, with one-click data portability exports compliant with Indonesian Personal Data Protection regulations.'
                        : 'Pencatatan log audit kriptografis yang merekam seluruh mutasi data oleh admin, dilengkapi fitur ekspor portabilitas data sesuai amanat UU Perlindungan Data Pribadi No. 27/2022.',
                    'why_critical' => $isEn
                        ? 'Legal requirement under UU PDP with severe financial penalties for data mismanagement without proper access trails.'
                        : 'Kewajiban hukum berdasarkan UU PDP No. 27/2022 untuk mencegah sanksi hukum dan memverifikasi integritas data transaksi operasional.',
                    'proposed_phase' => 'mvp',
                    'category' => 'LEGAL_COMPLIANCE',
                    'impact_rating' => 'MISSION CRITICAL',
                    'selected' => true,
                ],
                [
                    'id' => 'ADVISORY-FEAT-RECONCILE',
                    'title' => $isEn ? 'Automated Financial Reconciliation & Midtrans Settlement Ledger' : 'Rekonsiliasi Keuangan Otomatis & Ledger Settlement Gateway',
                    'desc' => $isEn
                        ? 'Double-entry settlement ledger matching payment gateway webhook payouts against local order records with automatic discrepancy alerts.'
                        : 'Buku kas rekonsiliasi ganda yang mencocokkan pencairan dana dari payment gateway terhadap rekap pesanan internal dengan deteksi anomali otomatis.',
                    'why_critical' => $isEn
                        ? 'Eliminates revenue leakage and reconciles bank settlement variances without manual accountant spreadsheets.'
                        : 'Mencegah kebocoran pendapatan dan mengeliminasi proses pembukuan manual yang rawan selisih saldo antara payment gateway dan bank.',
                    'proposed_phase' => 'phase2',
                    'category' => 'FINANCIAL_INTEGRITY',
                    'impact_rating' => 'HIGH VALUE',
                    'selected' => true,
                ],
            ];

            foreach ($universalItems as $uItem) {
                $uLower = mb_strtolower($uItem['title']);
                $isDup = false;
                foreach ($existingTitles as $et) {
                    if (str_contains($uLower, mb_strtolower($et))) {
                        $isDup = true;
                        break;
                    }
                }
                if (! $isDup) {
                    $recommendations[] = $uItem;
                }
            }
        }

        return array_slice($recommendations, 0, 4);
    }

    /**
     * Compile modern 2026 technology stack upgrades.
     */
    protected function compileTechStackUpgrades(VisionBlueprint $blueprint, string $locale): array
    {
        $isEn = ($locale === 'en');

        return [
            [
                'id' => 'TECH-UPGRADE-ULID-CURSOR',
                'name' => 'PostgreSQL 16 Strict ULID & Keyset Cursor Pagination O(1)',
                'summary' => $isEn
                    ? 'Sub-10ms query response time at millions of rows with zero-offset memory exhaustion.'
                    : 'Waktu respons query sub-10ms pada jutaan baris data tanpa degradasi memori offset pagination.',
                'why_critical' => $isEn
                    ? 'Prevents catastrophic database CPU spikes and slow response times when data scales.'
                    : 'Mencegah lonjakan CPU database dan query lambat saat data pengguna bertumbuh ke skala enterprise.',
                'standards' => ['PostgreSQL 16', 'ULID 26-char PK', 'Keyset O(1)'],
                'selected' => true,
            ],
            [
                'id' => 'TECH-UPGRADE-REVERB',
                'name' => 'Laravel Reverb Real-Time WebSockets (<100ms)',
                'summary' => $isEn
                    ? 'Instant bidirectional event broadcasting eliminating polling loops and conserving server CPU.'
                    : 'Siaran event dua arah instan (<100ms) tanpa membebani server dengan polling berulang.',
                'why_critical' => $isEn
                    ? 'Guarantees instant synchronization for chat toggles, status updates, and live attendee presence.'
                    : 'Menjamin sinkronisasi seketika untuk kontrol buka/tutup, status kehadiran, dan interaksi pengguna.',
                'standards' => ['Laravel Reverb', 'WebSockets', 'Pusher Protocol'],
                'selected' => true,
            ],
            [
                'id' => 'TECH-UPGRADE-RFC9457',
                'name' => 'RFC 9457 Problem Details API Error Standard',
                'summary' => $isEn
                    ? 'Standardized machine-readable JSON error contracts (type, title, status, detail, instance).'
                    : 'Format kontrak respons error JSON terstandar internasional untuk kemudahan integrasi API.',
                'why_critical' => $isEn
                    ? 'Prevents silent frontend UI crashes from unexpected error responses and ambiguous messages.'
                    : 'Mencegah antarmuka web mengalami silent crash akibat format error API yang tidak konsisten.',
                'standards' => ['RFC 9457', 'HTTP Status Exact', 'API Contract'],
                'selected' => true,
            ],
            [
                'id' => 'TECH-UPGRADE-WAKELOCK',
                'name' => 'PWA Screen WakeLock API & Background MediaSession',
                'summary' => $isEn
                    ? 'Prevents mobile screens from sleeping and terminating live audio sessions during long services.'
                    : 'Mencegah layar smartphone mati otomatis yang dapat memutus audio streaming saat ibadah.',
                'why_critical' => $isEn
                    ? 'Mobile operating systems aggressively kill background web processes unless WakeLock is maintained.'
                    : 'Sistem operasi ponsel mematikan proses web di latar belakang jika API WakeLock tidak diaktifkan.',
                'standards' => ['Screen WakeLock API', 'MediaSession API', 'PWA Spec'],
                'selected' => true,
            ],
            [
                'id' => 'TECH-UPGRADE-RATE-LIMIT',
                'name' => 'Redis 7 Sliding-Window Rate Limiter & Concurrency Lock',
                'summary' => $isEn
                    ? 'Precision anti-spam protection (1 msg / 3s) and mutex locking against race conditions.'
                    : 'Proteksi anti-spam presisi dan penguncian atomik untuk mencegah race condition pada mutasi data.',
                'why_critical' => $isEn
                    ? 'Protects servers from automated spam floods and duplicate concurrent requests.'
                    : 'Melindungi server dari serangan spam bot dan eksekusi data ganda akibat klik berulang.',
                'standards' => ['Redis 7', 'Sliding Window', 'Atomic Mutex'],
                'selected' => true,
            ],
        ];
    }
}
