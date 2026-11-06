<?php

namespace App\Services;

use App\Models\VisionBlueprint;
use App\Services\Ai\MultiAiModelManager;
use App\Support\ProjectOsMatrixCatalog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlueprintKpiCascadeService
{
    /**
     * Elaborate high-level business goals/KPIs into rigorous SMART quantitative metrics,
     * and calculate the cascading impact across features (fiturUtama), actors (aktorSistem),
     * UAT acceptance tests (skenarioGherkin), and business ROI.
     */
    public function elaborateAndCascade(array $payload, ?VisionBlueprint $blueprint = null): array
    {
        $rawKpis = $payload['tujuanUtama'] ?? $payload['tujuan_utama'] ?? '';
        $problem = $payload['masalahUtama'] ?? $payload['masalah_utama'] ?? '';
        $businessName = $payload['namaBisnis'] ?? $payload['nama_bisnis'] ?? ($blueprint?->nama_bisnis ?: 'Sistem Enterprise');
        $businessDesc = $payload['deskripsiBisnis'] ?? $payload['deskripsi_bisnis'] ?? '';
        $currentFeatures = $payload['fiturUtama'] ?? $payload['fitur_utama'] ?? '';
        $currentActors = $payload['aktorSistem'] ?? $payload['aktor_sistem'] ?? '';
        $currentGherkin = $payload['skenarioGherkin'] ?? $payload['skenario_gherkin'] ?? '';
        $sector = $payload['primarySector'] ?? $payload['primary_sector'] ?? ($blueprint?->primary_sector ?: 'tourism_travel');
        $locale = $payload['locale'] ?? (function_exists('app') && app()->bound('translator') ? app()->getLocale() : 'id');
        $isEn = $locale === 'en';

        // 1. Parse current KPIs into items
        $kpiItems = $this->extractItems($rawKpis);
        if (empty($kpiItems)) {
            $kpiItems = $isEn ? [
                'Automate core customer transaction bookings 24/7 with instant calculation',
                'Eliminate manual data reconciliation bottlenecks and duplicate operational entries',
                'Provide centralized real-time dashboard for resource allocation and live tracking',
            ] : [
                'Otomatisasi reservasi dan transaksi pelanggan 24/7 dengan kalkulasi instan',
                'Menghilangkan inefisiensi rekonsiliasi manual dan duplikasi input operasional',
                'Dasbor terpusat real-time untuk alokasi armada/sumber daya dan pelacakan langsung',
            ];
        }

        // 2. Attempt AI Synthesis via MultiAiModelManager
        $aiResult = $this->attemptAiSynthesis(
            $businessName,
            $businessDesc,
            $problem,
            $kpiItems,
            $currentFeatures,
            $currentActors,
            $sector,
            $isEn
        );

        if (!empty($aiResult) && !empty($aiResult['kpis'])) {
            return [
                'success' => true,
                'mode' => 'ai_synthesis',
                'kpis' => $aiResult['kpis'],
                'cascaded_impacts' => $aiResult['cascaded_impacts'],
                'sector' => $sector,
                'locale' => $locale,
            ];
        }

        // 3. Fallback: High-Performance Deterministic Architecture Engine
        $deterministicResult = $this->generateDeterministicCascade(
            $businessName,
            $problem,
            $kpiItems,
            $sector,
            $isEn
        );

        return [
            'success' => true,
            'mode' => 'deterministic_heuristic',
            'kpis' => $deterministicResult['kpis'],
            'cascaded_impacts' => $deterministicResult['cascaded_impacts'],
            'sector' => $sector,
            'locale' => $locale,
        ];
    }

    /**
     * Parse numbered lists or multi-line text into array of strings.
     */
    protected function extractItems(mixed $input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }

        if (!is_string($input) || trim($input) === '') {
            return [];
        }

        $lines = preg_split('/[\r\n]+/', trim($input));
        $items = [];
        foreach ($lines as $line) {
            $cleaned = trim(preg_replace('/^\d+[\.\)]\s*/', '', trim($line)));
            if (!empty($cleaned)) {
                $items[] = $cleaned;
            }
        }

        return $items;
    }

    /**
     * Invoke MultiAiModelManager to elaborate KPIs and compute cascading impact.
     */
    protected function attemptAiSynthesis(
        string $businessName,
        string $businessDesc,
        string $problem,
        array $kpiItems,
        string $currentFeatures,
        string $currentActors,
        string $sector,
        bool $isEn
    ): ?array {
        $kpiListStr = implode("\n", array_map(fn($k, $idx) => ($idx + 1) . '. ' . $k, $kpiItems, array_keys($kpiItems)));

        $langPrompt = $isEn
            ? "Provide the entire response in Professional English."
            : "Sajikan seluruh respon dalam Bahasa Indonesia formal arsitektur enterprise.";

        $systemInstruction = <<<PROMPT
You are a Principal Enterprise Systems Architect and Balanced Scorecard Strategist for {$businessName}.
Your mission is to perform deep KPI elaboration and calculate the direct architectural cascading impact across system modules, RBAC roles, UAT tests, and ROI.

Strict Engineering Ground Rules:
1. Every business KPI must become SMART (Specific, Measurable, Attainable, Relevant, Time-bound) with concrete numerical targets, baselines, and database verification instruments.
2. CASCADING IMPACT: For every KPI, specify:
   - Features/Modules: The exact software modules required to hit this KPI (using Strict ULID, State Machine, and Concurrency Locks).
   - System Actors: The RBAC roles required to operate or govern the KPI.
   - Acceptance Criteria: A concrete Gherkin UAT scenario proving this KPI is achieved.
   - ROI Projection: Economic value or time savings statement.
{$langPrompt}

Return ONLY valid JSON matching this schema:
{
  "kpis": [
    {
      "id": 1,
      "title": "Short Descriptive Title",
      "baseline": "Current manual baseline with quantified pain",
      "target": "Precise numeric target (e.g. <60s, 0% double-booking, 99.8% uptime)",
      "metric": "Key measurable indicator",
      "verification": "Database/Audit mechanism (e.g. PostgreSQL Strict ULID audit log, Webhook status)",
      "synthesized_text": "Complete narrative string ready to replace the numbered KPI line in the blueprint."
    }
  ],
  "cascaded_impacts": {
    "features": [
      {
        "code": "FEAT_KPI_01",
        "title": "Module Title",
        "category": "Core Transaction / Automation / Reporting",
        "rationale": "Why this feature is mathematically required by the KPI",
        "spec": "Technical specification (Strict ULID, FOR UPDATE locks, idempotency key)",
        "formatted_line": "Complete line ready to be appended to Core Features (fiturUtama)."
      }
    ],
    "actors": [
      {
        "role": "Role Name",
        "access": "Access scope and permissions",
        "formatted_line": "Role Name: Operational responsibility and access level"
      }
    ],
    "acceptance_criteria": "Scenario: ...\\nGiven ...\\nWhen ...\\nThen ...",
    "roi_summary": "Quantified monthly operational savings or revenue increase projection."
  }
}
PROMPT;

        $userPrompt = <<<USER_PROMPT
BUSINESS CONTEXT:
Business Name: {$businessName}
Sector: {$sector}
Overview: {$businessDesc}
Core Pain Points: {$problem}

CURRENT KPIS TO ELABORATE:
{$kpiListStr}

EXISTING FEATURES (REFERENCE):
{$currentFeatures}

EXISTING ACTORS (REFERENCE):
{$currentActors}

Perform the deep KPI elaboration and compute the architectural cascade. Return ONLY the strict JSON object.
USER_PROMPT;

        try {
            $aiResponse = MultiAiModelManager::executeWithFailover(
                $userPrompt,
                $systemInstruction,
                'prd'
            );

            $text = $aiResponse['text'] ?? '';
            if (empty($text)) {
                return null;
            }

            // Clean deepseek reasoning tags
            $cleaned = preg_replace('/<think>.*?<\/think>/s', '', $text);
            if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $cleaned, $m)) {
                $cleaned = $m[1];
            } elseif (preg_match('/(\{.*\})/s', $cleaned, $m)) {
                $cleaned = $m[1];
            }

            $cleaned = trim($cleaned);
            $sanitized = preg_replace('/,\s*([\}\]])/', '$1', $cleaned);
            $parsed = json_decode($sanitized, true);

            if (json_last_error() === JSON_ERROR_NONE && !empty($parsed['kpis'])) {
                return $parsed;
            }
        } catch (\Throwable $e) {
            if (class_exists(\Illuminate\Support\Facades\Log::class) && \Illuminate\Support\Facades\Facade::getFacadeApplication()) {
                Log::warning("AI KPI Cascade generation encountered error: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Deterministic domain-driven fallback ensuring high enterprise quality even when offline.
     */
    protected function generateDeterministicCascade(
        string $businessName,
        string $problem,
        array $kpiItems,
        string $sector,
        bool $isEn
    ): array {
        $elaboratedKpis = [];
        $cascadedFeatures = [];
        $cascadedActors = [];

        foreach ($kpiItems as $idx => $rawKpi) {
            $num = $idx + 1;
            $cleanTitle = Str::limit($rawKpi, 70);

            if ($isEn) {
                $title = "Automated Operational Execution & SLA Guarantee (KPI #{$num})";
                $baseline = "Manual workflows with 2–4 hours processing turnaround and 25-35% drop-off risk.";
                $target = "Sub-60s instant execution, 0% concurrency collision, and >99.5% completion rate.";
                $metric = "Transaction Response Time <1.5s & Keyset Cursor O(1) query latency <15ms.";
                $verification = "PostgreSQL Strict ULID event-sourcing ledger and Idempotent Webhook signatures.";
                $synthesized = "{$rawKpi} [Target: <60s transaction completion, 0% duplicate collision, verified via Strict ULID idempotency ledger].";

                $featTitle = "Concurrency-Safe Execution Engine & Real-Time Lock (KPI #{$num})";
                $featSpec = "Pessimistic locking (FOR UPDATE) with Strict ULID and automated state-machine verification to guarantee KPI #{$num}.";
                $featLine = "{$featTitle}: {$featSpec}";

                $actorRole = $num === 1 ? "System Dispatcher / Operations Operator" : "Executive Auditor / Quality Assurance Lead";
                $actorLine = "{$actorRole}: Real-time governance, transaction validation, and KPI SLA compliance oversight.";
            } else {
                $title = "Otomatisasi Eksekusi & Jaminan SLA Real-Time (KPI #{$num})";
                $baseline = "Proses manual konvensional membutuhkan 2–4 jam dengan risiko drop-off dan human-error 25–35%.";
                $target = "Eksekusi instan <60 detik, 0% bentrok transaksi ganda, dan rasio keberhasilan >99.5%.";
                $metric = "Waktu respon transaksi <1.5s & Latensi query Keyset Cursor O(1) <15ms.";
                $verification = "Buku besar transaksi PostgreSQL Strict ULID dan verifikasi Webhook Idempoten.";
                $synthesized = "{$rawKpi} [Target: Penyelesaian transaksi <60 detik, 0% insiden jadwal bentrok, diverifikasi via Strict ULID ledger].";

                $featTitle = "Engine Penguncian Konkurensi & Alokasi Real-Time (KPI #{$num})";
                $featSpec = "Pessimistic locking (FOR UPDATE) dengan Strict ULID dan mesin state terverifikasi otomatis untuk menjamin pencapaian KPI #{$num}.";
                $featLine = "{$featTitle}: {$featSpec}";

                $actorRole = $num === 1 ? "Operator Pelaksana / Dispatcher Operasional" : "Auditor Eksekutif / Pengawas Mutu Layanan";
                $actorLine = "{$actorRole}: Mengawasi operasional harian, validasi transaksi kritis, dan kepatuhan terhadap target SLA KPI.";
            }

            $elaboratedKpis[] = [
                'id' => $num,
                'title' => $title,
                'baseline' => $baseline,
                'target' => $target,
                'metric' => $metric,
                'verification' => $verification,
                'synthesized_text' => $synthesized,
            ];

            $cascadedFeatures[] = [
                'code' => "FEAT_KPI_{$num}",
                'title' => $featTitle,
                'category' => "Core Engine",
                'rationale' => $isEn ? "Directly required to satisfy KPI #{$num} performance and consistency targets." : "Wajib ada secara komputasi untuk memenuhi target konsistensi dan kecepatan KPI #{$num}.",
                'spec' => $featSpec,
                'formatted_line' => $featLine,
            ];

            $cascadedActors[] = [
                'role' => $actorRole,
                'access' => "Read/Write Operational Cockpit",
                'formatted_line' => $actorLine,
            ];
        }

        $acceptanceCriteria = $isEn
            ? "Scenario: High-concurrency transaction execution under peak load\nGiven 2 clients initiate transactions for the exact same inventory slot simultaneously\nWhen the system processes both checkout payloads\nThen exactly 1 transaction succeeds with Strict ULID lock and the second receives a graceful collision fallback with zero double-booking."
            : "Skenario: Eksekusi transaksi berkecepatan tinggi saat lonjakan beban\nGiven 2 pelanggan melakukan transaksi untuk slot armada/sumber daya yang sama secara bersamaan\nWhen sistem memproses kedua muatan checkout\nThen tepat 1 transaksi tercepat berhasil dikunci dengan Strict ULID dan transaksi kedua dialihkan secara elegan tanpa terjadi double-booking.";

        $roiSummary = $isEn
            ? "Eliminates ~3.5 hours of daily manual reconciliations, preventing operational leakage estimated at Rp 12,000,000+/month."
            : "Menghemat rata-rata 3,5 jam kerja manual per hari dan mencegah potensi kebocoran pendapatan akibat jadwal bentrok senilai estimasi Rp 12.000.000/bulan.";

        return [
            'kpis' => $elaboratedKpis,
            'cascaded_impacts' => [
                'features' => $cascadedFeatures,
                'actors' => $cascadedActors,
                'acceptance_criteria' => $acceptanceCriteria,
                'roi_summary' => $roiSummary,
            ],
        ];
    }
}
