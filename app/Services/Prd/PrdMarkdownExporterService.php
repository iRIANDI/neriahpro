<?php

namespace App\Services\Prd;

use App\Services\ProjectOsBlueprintCompilerService;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrdMarkdownExporterService
{
    /**
     * Convert entire Vision Blueprint PRD into a complete, professional Markdown document.
     */
    public static function toMarkdown(VisionBlueprint $blueprint, array $prd, bool $isEn = false): string
    {
        if (!$isEn && (($prd['meta']['output_locale'] ?? '') === 'en' || ($blueprint->user_metadata['output_locale'] ?? '') === 'en')) {
            $isEn = true;
        }

        $meta = $prd['meta'] ?? [];
        $exec = $prd['executive_summary'] ?? [];
        $actors = $prd['system_actors'] ?? [];
        $mvpFeatures = $prd['features']['mvp_phase1'] ?? [];
        $phase2Features = $prd['features']['phase2_roadmap'] ?? [];
        $workflow = $prd['workflow'] ?? [];
        $erd = $prd['erd_schema']['tables'] ?? [];
        $tech = $prd['tech_stack'] ?? [];
        $governance = $prd['governance_and_sla'] ?? [];
        $pricing = $prd['velocity_pricing_options'] ?? [];
        $businessRoi = $prd['business_roi_analysis'] ?? PrdGeneratorService::generateBusinessRoiAnalysis($blueprint, $prd['itemized_cost_breakdown'] ?? PrdGeneratorService::calculateItemizedEstimation($blueprint), $isEn);
        $slop = PrdGeneratorService::getAntiAiSlopDesignSystem();
        $scalability = PrdGeneratorService::getBackendScalabilityManifesto();
        $handoff = PrdGeneratorService::getAgentHandoffProtocol($blueprint->nama_bisnis ?: $blueprint->client_name);

        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $specId = strtoupper(substr($blueprint->id, 0, 10));

        $md = "# ULTIMATE PRODUCT REQUIREMENTS DOCUMENT (PRD) & SYSTEM BLUEPRINT\n";
        $md .= "## {$projectName}\n\n";

        $md .= "> **SPEC_ID**: `{$specId}`  \n";
        $md .= "> **Client PIC**: {$blueprint->client_name} ({$blueprint->email})  \n";
        $md .= "> **Generated**: " . ($meta['generated_at'] ?? now()->toIso8601String()) . "  \n";
        $md .= "> **Target Timeline**: " . ($blueprint->target_waktu ?? ($isEn ? '30 Business Days' : '30 Hari Kerja')) . "  \n";
        $md .= "> **Architecture Standard**: Modern Monolith (Laravel 13 + Filament v5 + PostgreSQL Strict ULID)  \n";

        if (!empty($meta['ai_telemetry'])) {
            $aiTel = $meta['ai_telemetry'];
            $md .= "> **Multi-AI Engine**: `{$aiTel['provider_name']}` ({$aiTel['model']})  \n";
            if (!empty($aiTel['fallback_occurred'])) {
                $md .= "> **Failover Event**: `{$aiTel['notification']}`  \n";
            }
            if (!empty($aiTel['strategic_guidance'])) {
                $md .= "> **AI Strategic Insights**: {$aiTel['strategic_guidance']}  \n";
            }
        }

        $md .= "\n---\n\n";
        if ($isEn) {
            $md .= "# ⚠️ STEP-BY-STEP RELAY EXECUTION GUIDE FOR DEVELOPERS (COCKPIT METHOD) ⚠️\n\n";
            $md .= "> **STOP! DO NOT COPY-PASTE THIS ENTIRE FILE AT ONCE INTO AI AGENTS (CURSOR / CLAUDE / WINDSURF)!**\n";
            $md .= "> Copying thousands of lines at once causes **AI Context Rot / Amnesia**, leading the agent to hallucinate, omit critical constraints, and degrade architecture integrity (e.g. forgetting ULID primary keys, corrupting styling, or ignoring Gherkin scenarios).\n\n";
            $md .= "**PROPER RELAY EXECUTION PROCEDURE (SPRINT COCKPIT METHOD):**\n";
            $md .= "1. **Step 0 (Foundation & Infrastructure):** First, copy only the *Tech Stack, Architecture Manifesto, Anti-AI-Slop Guidelines, and ERD Diagram*. Instruct the AI agent to build the initial `Migration`, `Model`, `Seeder`, empty `Filament Resource`, and foundational `UI/UX` tokens.\n";
            $md .= "2. **Step 1 (Specific Feature Slice):** Once the foundation is committed to Git, copy **only one** Feature Specification block (e.g., MVP Phase 1 - Module A) along with its Gherkin Scenarios into a fresh agent prompt.\n";
            $md .= "3. **Step 2 (Next Iteration):** After the preceding feature is fully implemented, verified with tests (`php artisan test`), and committed, repeat the relay for the next feature block.\n";
            $md .= "4. **Leverage the Cockpit Dashboard:** For optimal results, utilize the **Sprint Cockpit (0/6)** button in your Neriah Pro dashboard, which automatically chunks this PRD into surgical relay portions.\n\n";
        } else {
            $md .= "# ⚠️ PANDUAN EKSEKUSI ESTAFET UNTUK DEVELOPER (COCKPIT METHOD) ⚠️\n\n";
            $md .= "> **BERHENTI! JANGAN COPY-PASTE SELURUH FILE INI SEKALIGUS KE DALAM AI AGENT (CURSOR / CLAUDE / WINDSURF)!**\n";
            $md .= "> Menyalin ribuan baris PRD sekaligus akan menyebabkan **Context Rot / Amnesia AI**, di mana AI akan berhalusinasi, melewatkan aturan penting, dan merusak struktur arsitektur (seperti lupa menggunakan ULID, merusak styling, atau mengabaikan Gherkin).\n\n";
            $md .= "**CARA PENGGUNAAN YANG BENAR (METODE ESTAFET / SPRINT COCKPIT):**\n";
            $md .= "1. **Langkah 0 (Fondasi & Infrastruktur):** Copy bagian *Tech Stack, Architecture Manifesto, Aturan Anti-AI-Slop, dan ERD Diagram* terlebih dahulu. Minta AI membangun `Migration`, `Model`, `Seeder`, `Filament Resource` kosong, dan aturan `UI/UX` dasarnya.\n";
            $md .= "2. **Langkah 1 (Fitur Spesifik):** Setelah fondasi ter-commit ke Git, copy **hanya satu** blok spesifikasi Fitur (misal MVP Phase 1 - Modul A) beserta Skenario Gherkin-nya ke dalam prompt baru.\n";
            $md .= "3. **Langkah 2 (Iterasi Berikutnya):** Setelah fitur sebelumnya selesai, diuji (`php artisan test`), dan di-commit, ulangi proses estafet untuk blok fitur selanjutnya.\n";
            $md .= "4. **Gunakan Dashboard Cockpit:** Untuk pengalaman terbaik, gunakan UI tombol **Sprint Cockpit (0/6)** di dashboard Neriah Pro Anda, yang secara otomatis memotong PRD ini menjadi porsi estafet yang presisi.\n\n";
        }
        $md .= "---\n\n";

        // 1. Executive Discovery
        $md .= "## 1. Executive Technical Discovery & Problem Statement\n\n";
        $md .= "- **" . ($isEn ? 'Primary Problem' : 'Masalah Utama') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['problem_statement'] ?? $blueprint->masalah_utama, $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Objectives / Success Metrics' : 'Tujuan / Success Metrics') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['success_metrics'] ?? $blueprint->tujuan_utama, $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Target Audience' : 'Target Audiens') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['target_audience'] ?? $blueprint->target_audiens, $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Target Platform & Accessibility' : 'Target Platform & Aksesibilitas') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['target_platform'] ?? 'Modern Web Application Responsive & PWA', $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Legacy Data Migration Status' : 'Status Migrasi Data Warisan') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['legacy_data_migration'] ?? ($isEn ? 'Clean Start New Database' : 'Database Baru Bersih'), $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Hosting & Server Infrastructure' : 'Infrastruktur Hosting & Server') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['hosting_infrastructure'] ?? ($isEn ? 'Managed Dedicated Cloud VPS Neriah Pro' : 'Managed Dedicated Cloud VPS Neriah Pro'), $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Warranty, SLA & Git Handover' : 'Skema Garansi, SLA & Serah Terima Git') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['warranty_sla'] ?? ($isEn ? '30 Calendar Days Post-Launch Bug Warranty' : '30 Hari Garansi Bug Pascameluncur'), $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Payment Milestones' : 'Skema Termin Pembayaran') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['payment_milestones'] ?? ($isEn ? 'Standard 50/50 Milestones' : 'Termin Standar 50/50'), $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Target Scale' : 'Target Skala') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['target_scale'] ?? ($isEn ? '0 - 100,000 Users / Month' : '0 - 100.000 Pengguna / Bulan'), $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Market Reach' : 'Jangkauan Pasar') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['market_reach'] ?? ($isEn ? 'Domestic Indonesia & Global' : 'Domestik Indonesia'), $isEn) . "\n";
        $md .= "- **" . ($isEn ? 'Architecture Philosophy' : 'Filosofi Arsitektur') . "**: " . \App\Support\BlueprintPdfHelper::translateText($exec['architecture_philosophy'] ?? '', $isEn) . "\n\n";

        // 1.5 Business Feasibility, ROI & No-Regret Guarantees
        if (!empty($businessRoi)) {
            $roiEq = $businessRoi['deadly_roi_equation'] ?? [];
            $inaction = $businessRoi['cost_of_inaction'] ?? [];
            $winWin = $businessRoi['win_win_strategy'] ?? [];
            $guarantees = $businessRoi['why_neriah_pro_guarantees'] ?? [];

            $md .= "## 1.5 Analisis Kelayakan Bisnis, Proyeksi ROI & 5 Garansi Bebas Penyesalan Neriah Pro\n\n";
            $md .= "> **Domain Sektor Bisnis**: `{$businessRoi['domain_category']}` (`{$businessRoi['domain_badge']}`)  \n";
            $md .= "> **Benchmark Transaksi Pasar**: `{$businessRoi['ticket_benchmark']}`\n\n";

            $md .= "### A. Formula Balik Modal Cepat (Killer ROI Equation)\n";
            $md .= "- **Target BEP**: **" . ($roiEq['headline'] ?? '-') . "**\n";
            $md .= "- **Formula Hitungan Realistis**: `" . ($roiEq['formula'] ?? '-') . "`\n";
            $md .= "- **Proyeksi ROI Tahun ke-1**: **" . ($roiEq['projected_annual_roi'] ?? '-') . "**\n";
            $md .= "- **Rasional Analisis**: " . ($roiEq['narrative'] ?? '-') . "\n\n";

            if (!empty($inaction['risks'])) {
                $md .= "### B. " . ($inaction['title'] ?? 'Biaya Fatal Menunda (Cost of Inaction)') . "\n";
                foreach ($inaction['risks'] as $risk) {
                    $md .= "- ⚠️ {$risk}\n";
                }
                $md .= "\n";
            }

            if (!empty($winWin['recommendation'])) {
                $md .= "### C. " . ($winWin['title'] ?? 'Strategi Investasi Menang-Menang (Win-Win Solution)') . "\n";
                $md .= "{$winWin['recommendation']}\n\n";
            }

            if (!empty($guarantees)) {
                $md .= "### D. 5 Garansi Bebas Penyesalan Neriah Pro (No-Regret Investment Guarantees)\n\n";
                $md .= "| No | Garansi Perlindungan Klien | Deskripsi & Nilai Perlindungan | Badge Standar |\n";
                $md .= "|---|---|---|---|\n";
                $gIdx = 1;
                foreach ($guarantees as $g) {
                    $gTitle = $g['title'] ?? '-';
                    $gDesc = $g['desc'] ?? '-';
                    $gBadge = $g['badge'] ?? 'GUARANTEED';
                    $md .= "| `0{$gIdx}` | **{$gTitle}** | {$gDesc} | `{$gBadge}` |\n";
                    $gIdx++;
                }
                $md .= "\n";
            }
        }

        // 2. System Actors
        $md .= "## 2. Aktor Sistem & Matriks Hak Akses (RBAC)\n\n";
        $md .= "| Aktor | Peran & Batasan Tanggung Jawab | Hak Akses & Permissions |\n";
        $md .= "|---|---|---|\n";
        foreach ($actors as $actor) {
            $name = $actor['name'] ?? $actor['title'] ?? 'Aktor Sistem';
            $role = $actor['role'] ?? $actor['desc'] ?? '-';
            $badge = $actor['badge'] ?? 'ROLE';
            $perms = !empty($actor['permissions']) ? ('`' . implode('`, `', $actor['permissions']) . '`') : 'Hak Akses Standar';
            $md .= "| **{$name}** (`{$badge}`) | {$role} | {$perms} |\n";
        }
        $md .= "\n";

        // 3. Feature Breakdown
        $md .= "## 3. Spesifikasi Rinci Fitur & Task Breakdown (Engineering Specs)\n\n";
        
        $md .= "### A. Fitur Wajib (Fase 1 - MVP Peluncuran)\n\n";
        foreach ($mvpFeatures as $f) {
            $fid = $f['id'] ?? 'FEAT-MVP';
            $ftitle = $f['title'] ?? 'Fitur MVP';
            $fcat = $f['category_label'] ?? ($f['category'] ?? 'CORE');
            $md .= "#### [{$fid}] {$ftitle}\n";
            $md .= "**Kategori**: `{$fcat}`  \n";
            $md .= "**Deskripsi**: " . ($f['desc'] ?? '-') . "  \n";
            $md .= "**User Story**: *" . ($f['user_story'] ?? '-') . "*\n\n";

            if (!empty($f['acceptance_criteria'])) {
                $normAc = PrdGeneratorService::normalizeAcceptanceCriteria($f['acceptance_criteria'], $ftitle, $f['user_story'] ?? '');
                $md .= "**Acceptance Criteria (Gherkin Format)**:\n";
                foreach ($normAc as $ac) {
                    $md .= "- **Skenario: " . ($ac['scenario'] ?? 'Skenario') . "**\n";
                    $md .= "  - **Given**: " . ($ac['given'] ?? '-') . "\n";
                    $md .= "  - **When**: " . ($ac['when'] ?? '-') . "\n";
                    $md .= "  - **Then**: " . ($ac['then'] ?? '-') . "\n";
                }
                $md .= "\n";
            }

            if (!empty($f['frontend'])) {
                $fe = $f['frontend'];
                $md .= "**Frontend Tasks (Anti-AI-Slop Specs)**:\n";
                $md .= "- **Design Tokens**: " . ($fe['design_tokens'] ?? '-') . "\n";
                $md .= "- **States**: " . ($fe['states'] ?? '-') . "\n";
                if (!empty($fe['tasks'])) {
                    $md .= "- **Task Checklist**:\n";
                    foreach ($fe['tasks'] as $t) {
                        $md .= "  - [ ] {$t}\n";
                    }
                }
                $md .= "\n";
            }

            if (!empty($f['backend'])) {
                $be = $f['backend'];
                $md .= "**Backend Tasks (Scalability & Models)**:\n";
                $md .= "- **Model & Migration**: " . ($be['model_and_migration'] ?? '-') . "\n";
                $md .= "- **Scalability Guardrail**: " . ($be['scalability_guardrail'] ?? '-') . "\n";
                if (!empty($be['tasks'])) {
                    $md .= "- **Task Checklist**:\n";
                    foreach ($be['tasks'] as $t) {
                        $md .= "  - [ ] {$t}\n";
                    }
                }
                $md .= "\n";
            }

            if (!empty($f['integration'])) {
                $in = $f['integration'];
                $md .= "**API & Integration Contract**:\n";
                $md .= "- **Endpoint**: `{$in['endpoint']}`\n";
                $md .= "- **Request Payload**:\n```json\n" . ($in['request_schema'] ?? '{}') . "\n```\n";
                $md .= "- **Response Payload**:\n```json\n" . ($in['response_schema'] ?? '{}') . "\n```\n\n";
            }

            if (!empty($f['code_agent_directive'])) {
                $md .= "<details><summary>🤖 <strong>Prompt Handoff AI Code Agent (COPY-PASTE prompt ini ke Cursor/Claude/Aider untuk mengerjakan {$fid})</strong></summary>\n\n";
                $md .= "```markdown\n" . $f['code_agent_directive'] . "\n```\n";
                $md .= "</details>\n\n";
            }

            $md .= "---\n\n";
        }

        if (!empty($phase2Features)) {
            $md .= "### B. Fitur Tambahan (Fase 2 - Roadmap Masa Depan)\n\n";
            foreach ($phase2Features as $f) {
                $fid = $f['id'] ?? 'FEAT-ROADMAP';
                $ftitle = $f['title'] ?? 'Fitur Roadmap';
                $md .= "#### [{$fid}] {$ftitle}\n";
                $md .= "- **Deskripsi**: " . ($f['desc'] ?? '-') . "\n";
                $md .= "- **User Story**: *" . ($f['user_story'] ?? '-') . "*\n\n";
            }
        }

        // 4. User Flow
        $md .= "## 4. Alur Kerja Pengguna (User Flow) & Mermaid Pipeline\n\n";
        $md .= "```mermaid\nflowchart TD\n";
        $wIdx = 1;
        $prevNode = null;
        foreach ($workflow as $w) {
            $nodeId = "S" . $wIdx;
            $actionClean = addslashes($w['action'] ?? ('Step ' . $wIdx));
            $actorClean = addslashes($w['actor'] ?? 'Sistem');
            $md .= "    {$nodeId}[\"<b>Step {$wIdx}: {$actionClean}</b><br/>Aktor: {$actorClean}\"]\n";
            if ($prevNode) {
                $md .= "    {$prevNode} -->|Lanjut| {$nodeId}\n";
            }
            $prevNode = $nodeId;
            $wIdx++;
        }
        $md .= "```\n\n";

        $md .= "### Rincian Rekayasa Alur Bertahap (Step-by-Step Engineering Details)\n\n";
        $md .= "| Step | Aksi Utama | Aktor | Pemicu (Trigger) | Proses Backend & Database | Respon / Output & Edge Case |\n";
        $md .= "|---|---|---|---|---|---|\n";
        foreach ($workflow as $w) {
            $sNum = $w['step'] ?? 1;
            $sAct = $w['action'] ?? '-';
            $sActor = $w['actor'] ?? 'Pengguna';
            $sTrig = $w['trigger'] ?? '-';
            $sProc = $w['system_process'] ?? '-';
            $sOut = ($w['output_state'] ?? '-') . '<br/>**Edge Case**: ' . ($w['edge_case'] ?? '-');
            $md .= "| `0{$sNum}` | **{$sAct}** | `{$sActor}` | {$sTrig} | {$sProc} | {$sOut} |\n";
        }
        $md .= "\n";

        // 5. Database ERD
        $md .= "## 5. Skema Basis Data Dual-Engine (PostgreSQL 16 & MySQL 8.0+ Strict ULID) & Mermaid ERD\n\n";
        $md .= "> **Standar Dual-Engine Enterprise**: Project OS mendukung dua engine database tier-1 industri secara native:\n";
        $md .= "> 1. **PostgreSQL 16+**: Menggunakan `id VARCHAR(26) PRIMARY KEY`, payload dokumen `JSONB`, `TIMESTAMPTZ`, dan ekstensi `pgvector`.\n";
        $md .= "> 2. **MySQL 8.0+ / MariaDB**: Menggunakan `id CHAR(26) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY`, kolom dokumen `JSON`, `DATETIME(6)`, dan `ENGINE=InnoDB ROW_FORMAT=DYNAMIC`.\n";
        $md .= "> Kedua engine dioptimalkan dengan indeks komposit `(created_at, id)` untuk Keyset Cursor Pagination O(1) tanpa degradasi performa.\n\n";
        $md .= "```mermaid\n" . PrdGeneratorService::generateErdMermaid($erd) . "\n```\n\n";

        foreach ($erd as $table) {
            $tname = $table['name'] ?? 'table';
            $tdesc = $table['description'] ?? '';
            $md .= "### Tabel: `{$tname}`\n";
            $md .= "_{$tdesc}_\n\n";
            $md .= "| Kolom | Tipe Data | Indeks | Nullable | Keterangan |\n";
            $md .= "|---|---|---|---|---|\n";
            foreach ($table['columns'] ?? [] as $col) {
                $cname = $col['name'] ?? '';
                $ctype = $col['type'] ?? '';
                $cindex = $col['index'] ?? 'NONE';
                $cnull = ($col['nullable'] ?? false) ? 'YES' : 'NO';
                $cnotes = $col['notes'] ?? '';
                $md .= "| `{$cname}` | `{$ctype}` | `{$cindex}` | {$cnull} | {$cnotes} |\n";
            }
            $md .= "\n";
        }

        // 5.5 Virtual Architecture Studio: Virtual Charts Suite
        $featureDepMermaid = PrdGeneratorService::generateFeatureDependencyMermaid($actors, $mvpFeatures, $erd);
        $sprintGanttMermaid = PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?: '30 Hari Kerja');
        $extraContext = [
            'target_platform' => $exec['target_platform'] ?? 'Modern Web & PWA',
            'preferensi_hosting' => $exec['hosting_infrastructure'] ?? 'Managed Dedicated Cloud VPS',
            'migrasi_data' => $exec['legacy_data_migration'] ?? 'Database Baru Bersih',
            'garansi_sla' => $exec['warranty_sla'] ?? '30 Hari Garansi Bug',
            'termin_pembayaran' => $exec['payment_milestones'] ?? 'Termin Standar 50/50',
        ];
        $infraMermaid = PrdGeneratorService::generateInfrastructureMermaid($blueprint, $extraContext);

        $md .= "## 5.5 Visual Architecture Studio: Virtual Charts Suite\n\n";
        $md .= "### A. Feature & Entity Dependency Graph (Flowchart LR)\n\n";
        $md .= "```mermaid\n" . $featureDepMermaid . "\n```\n\n";
        $md .= "### B. Roadmap Eksekusi & Timeline Sprint (Gantt Chart)\n\n";
        $md .= "```mermaid\n" . $sprintGanttMermaid . "\n```\n\n";
        $md .= "### C. Hosting, Keamanan & Topologi Infrastruktur (Flowchart TB)\n\n";
        $md .= "```mermaid\n" . $infraMermaid . "\n```\n\n";

        // 5.6 Multi-Platform Mobile & Offline-First Sync Architecture
        $mobileSync = $prd['mobile_and_sync_architecture'] ?? PrdGeneratorService::generateMobileAndSyncArchitecture($projectName, $exec['target_platform'] ?? '', $erd, $mvpFeatures);
        $mobileSyncMermaid = $prd['virtual_charts']['mobile_sync_mermaid'] ?? PrdGeneratorService::generateMobileSyncMermaid($projectName);

        $md .= "## 5.6 Arsitektur Multi-Platform: Aplikasi Mobile, Database Lokal (Offline-First) & Protokol Sinkronisasi Server\n\n";
        $md .= "### A. Alur Kerja Sinkronisasi Data Offline-to-Online (Sequence Pipeline)\n\n";
        $md .= "```mermaid\n" . $mobileSyncMermaid . "\n```\n\n";

        $md .= "### B. Spesifikasi Platform Mobile & Database Lokal (Offline-First)\n\n";
        $md .= "- **Mobile Framework**: " . ($mobileSync['mobile_platform']['framework'] ?? 'Flutter 3.x / React Native') . "\n";
        $md .= "- **Pola Arsitektur Mobile**: " . ($mobileSync['mobile_platform']['architecture_pattern'] ?? 'Clean Architecture Feature-First') . "\n";
        $md .= "- **State Management & Reaktivitas**: " . ($mobileSync['mobile_platform']['state_management'] ?? 'Riverpod / Bloc') . "\n";
        $md .= "- **Ketahanan Jaringan (Offline-Resilience)**: " . ($mobileSync['mobile_platform']['offline_resilience'] ?? '100% Offline-First') . "\n";
        $md .= "- **Network Interceptor**: " . ($mobileSync['mobile_platform']['network_interceptor'] ?? 'Dio / Axios dengan Exponential Backoff') . "\n";
        $md .= "- **Engine Database Lokal (Client/Device)**: " . ($mobileSync['local_database']['engine'] ?? 'SQLite 3 Encrypted (Drift / Room)') . "\n";
        $md .= "- **Engine Database Server (Central Hub)**: " . ($mobileSync['server_database']['engine'] ?? 'PostgreSQL 16+ (Strict ULID Schema)') . "\n";
        $md .= "- **Protokol Sinkronisasi**: " . ($mobileSync['sync_protocol']['strategy'] ?? 'Bi-Directional Delta Sync with Idempotency Key') . "\n\n";

        $md .= "### C. Skema Standar Metadata Sinkronisasi Database Lokal (SQLite DDL)\n\n";
        $md .= "Setiap tabel lokal pada perangkat mobile wajib menyertakan kolom kontrol sinkronisasi berikut untuk mencegah tabrakan data dan amnesia status:\n\n";
        $md .= "```sql\n";
        $md .= "-- Kolom Standar Sync pada SQLite Lokal (Drift / Room / WatermelonDB)\n";
        $md .= "ALTER TABLE local_records ADD COLUMN sync_status TEXT CHECK(sync_status IN ('synced', 'pending_push', 'conflict', 'failed')) DEFAULT 'synced';\n";
        $md .= "ALTER TABLE local_records ADD COLUMN client_mutation_id TEXT UNIQUE; -- 26-char ULID per mutasi lokal\n";
        $md .= "ALTER TABLE local_records ADD COLUMN local_updated_at INTEGER NOT NULL; -- Unix Timestamp in milliseconds\n";
        $md .= "ALTER TABLE local_records ADD COLUMN server_synced_at TEXT NULL; -- ISO-8601 UTC timestamp saat berhasil tersinkron\n";
        $md .= "ALTER TABLE local_records ADD COLUMN is_deleted INTEGER DEFAULT 0; -- Tombstone flag untuk soft delete offline\n";
        $md .= "CREATE INDEX idx_local_sync ON local_records(sync_status, local_updated_at);\n";
        $md .= "```\n\n";

        $md .= "### D. Kontrak Spesifikasi API Sinkronisasi (Machine-Readable Endpoint Contracts)\n\n";
        $md .= "#### 1. Push Mutasi Offline ke Server: `POST /api/v1/sync/push`\n";
        $md .= "- **Headers Wajib**: `Authorization: Bearer <token>`, `X-Idempotency-Key: <client_mutation_id>`, `Content-Type: application/json`\n";
        $md .= "- **Request Payload Schema**:\n```json\n" . json_encode($mobileSync['sync_protocol']['push_request_schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n";
        $md .= "- **Response Payload Schema (200 OK)**:\n```json\n" . json_encode($mobileSync['sync_protocol']['push_response_schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n\n";

        $md .= "#### 2. Pull Delta Pembaruan dari Server: `GET /api/v1/sync/pull`\n";
        $md .= "- **Query Parameters**: `?since=<ISO8601>&cursor=<ULID>&limit=100`\n";
        $md .= "- **Response Payload Schema (200 OK)**:\n```json\n" . json_encode($mobileSync['sync_protocol']['pull_response_schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n\n";

        $md .= "### E. Kebijakan Resolusi Konflik (Conflict Resolution Engine)\n\n";
        $md .= "1. **Deterministic Last-Write-Wins (LWW)**: Resolusi konflik otomatis mengacu pada timestamp mikrodetik yang tertanam di dalam 10-byte pertama Primary Key ULID. Mutasi dengan timestamp tertinggi secara deterministik diterima sebagai state final.\n";
        $md .= "2. **Tombstone Soft Deletes**: Penghapusan data offline tidak langsung menghapus baris fisik SQLite melainkan menandai `is_deleted = 1` dengan `deleted_at = now()`. Saat disinkronkan ke server, server mencatat `deleted_at` dan mempropagasi penghapusan ini ke perangkat lain saat `GET /api/v1/sync/pull`.\n";
        $md .= "3. **Idempotency Protection**: Header `X-Idempotency-Key` di-cache di Redis server selama 24 jam. Jika koneksi seluler putus saat pengiriman dan mobile app melakukan retry, server mendeteksi mutasi duplikat dan langsung mengembalikan status sukses tanpa memicu duplikasi data.\n\n";

        // 6. Enterprise Governance & Disaster Recovery
        $md .= "## 6. Enterprise Governance & Disaster Recovery (World-Class Standards)\n\n";
        $md .= "### A. Metrik Ketahanan Bencana (Disaster Recovery SLA)\n";
        $md .= "- **RPO (Recovery Point Objective)**: 5 Menit. Sistem dijamin tidak akan kehilangan data transaksi lebih dari 5 menit jika terjadi kegagalan fatal pada datacenter. Diimplementasikan via PostgreSQL WAL Archiving & Point-in-Time Recovery (PITR).\n";
        $md .= "- **RTO (Recovery Time Objective)**: 1 Jam. Waktu maksimal yang dibutuhkan untuk menghidupkan kembali sistem di zona/region cadangan sejak sistem utama dinyatakan mati total.\n\n";
        $md .= "### B. Protokol Kepatuhan Hukum & Data Governance (UU PDP / GDPR)\n";
        $md .= "- **PII Data Masking**: Data sensitif (NIK, Nomor Kartu Kredit, Password) tidak akan dirender dalam bentuk teks murni pada Dasbor Admin, meskipun diakses oleh Superadmin.\n";
        $md .= "- **Right to be Forgotten**: Sistem menyediakan fungsi *Soft Delete* berjenjang (Tombstone) dan *Hard Purge* periodik untuk menghapus data pribadi pengguna (termasuk pada tabel log relasional) secara permanen apabila pengguna memintanya, guna mematuhi UU PDP Indonesia.\n\n";
        $md .= "### C. Pemisahan Deployment dan Release (Feature Flags)\n";
        $md .= "- **Dark Launching**: Sistem akan menggunakan arsitektur *Feature Toggles* (Feature Flags) untuk fitur-fitur berisiko tinggi (mis. integrasi payment gateway baru). Hal ini memungkinkan kode dideploy ke *Production* dengan aman (Zero Downtime), namun fitur tersebut dapat diaktifkan/dinonaktifkan secara dinamis (Canary Release) tanpa perlu melakukan *re-deploy* server.\n\n";
        $md .= "### D. Infrastructure as Code (IaC) Asset Handoff\n";
        $md .= "- **Immutable Infrastructure**: Konfigurasi server (Nginx, PostgreSQL, Redis, Worker) tidak dikonfigurasi secara manual (*ClickOps*). Seluruh topologi dideklarasikan dalam bentuk skrip Infrastructure as Code (mis. *Nixpacks, Docker Compose, atau Ansible*). File skrip infrastruktur ini menjadi bagian integral dari *Source Code* yang diserahterimakan kepada klien (Anti-Vendor Lock-in dan Anti-Drift).\n\n";

        // 7. Technology Stack & Architecture Decision
        $isDecoupled = ($prd['architecture_evaluation']['architecture_pattern_evaluation']['is_decoupled'] ?? false);
        $archTitle = $isDecoupled ? 'Enterprise Decoupled & Headless Architecture' : 'Modern Monolith (Decoupled-Ready)';
        $md .= "## 7. Keputusan Arsitektur & Rekomendasi Stack ({$archTitle})\n\n";
        $md .= "| Lapisan | Teknologi | Peran & Justifikasi Arsitektur |\n";
        $md .= "|---|---|---|\n";
        foreach ($tech as $layer => $info) {
            $md .= "| **" . ucfirst(str_replace('_', ' ', $layer)) . "** | " . ($info['name'] ?? '') . " | " . ($info['role'] ?? '') . " |\n";
        }
        $md .= "\n";

        // 7.5 Cetak Biru Arsitektur Decoupled & Ekosistem Multi-Pihak (Modern 2026+ Standards)
        if (!empty($prd['decoupled_tooling_strategy'])) {
            $ds = $prd['decoupled_tooling_strategy'];
            $md .= "## 7.5 Cetak Biru Arsitektur Decoupled & Ekosistem Multi-Pihak (2026+ Modern Tooling)\n\n";
            $md .= "> " . $ds['summary'] . "\n\n";
            $md .= "### A. Matriks Tools Multi-Pihak (Frontend, Mobile, Backend & API)\n\n";
            $md .= "| Kategori Pihak | Rekomendasi Utama (2026+) | Alternatif / DB Lokal | Peran Arsitektur |\n";
            $md .= "|---|---|---|---|\n";
            foreach ($ds['multi_party_tools_matrix'] as $k => $tool) {
                $tCat = $tool['category'] ?? ucfirst($k);
                $tPri = $tool['primary'] ?? '';
                $tAlt = $tool['alternatives'] ?? ($tool['local_database'] ?? ($tool['specification'] ?? ($tool['strategy'] ?? '-')));
                $tRole = $tool['role'] ?? ($tool['description'] ?? '');
                $md .= "| **{$tCat}** | `{$tPri}` | {$tAlt} | {$tRole} |\n";
            }
            $md .= "\n";

            $md .= "### B. Topologi Server, Cloud Edge & Efisiensi Biaya\n\n";
            $md .= "| Tingkatan Server | Provider / Teknologi | Spesifikasi & Cakupan | Estimasi Biaya |\n";
            $md .= "|---|---|---|---|\n";
            foreach ($ds['server_and_cloud_topology'] as $k => $srv) {
                $sTier = $srv['tier'] ?? ucfirst($k);
                $sProv = $srv['provider'] ?? ($srv['tools'] ?? '');
                $sSpec = $srv['specs'] ?? ($srv['coverage'] ?? ($srv['features'] ?? ($srv['killer_feature'] ?? '')));
                $sCost = $srv['cost_range'] ?? ($srv['sla_target'] ?? '-');
                $md .= "| **{$sTier}** | `{$sProv}` | {$sSpec} | **{$sCost}** |\n";
            }
            $md .= "\n";

            $md .= "### C. Protokol Keamanan, Autentikasi & Sinkronisasi\n\n";
            $md .= "- **Autentikasi Web**: " . ($ds['framework_strategy_and_protocols']['authentication']['web'] ?? '') . "\n";
            $md .= "- **Autentikasi Mobile**: " . ($ds['framework_strategy_and_protocols']['authentication']['mobile'] ?? '') . "\n";
            $md .= "- **Proteksi Idempotency**: " . ($ds['framework_strategy_and_protocols']['data_sync_and_idempotency']['idempotency'] ?? '') . "\n";
            $md .= "- **Paginasi Skalabilitas**: " . ($ds['framework_strategy_and_protocols']['data_sync_and_idempotency']['keyset_pagination'] ?? '') . "\n";
            $md .= "- **Zero-Trust CORS**: " . ($ds['framework_strategy_and_protocols']['cors_and_zero_trust']['cors'] ?? '') . "\n";
            $md .= "- **CI/CD Pipeline**: " . ($ds['framework_strategy_and_protocols']['devsecops_cicd']['strategy'] ?? '') . "\n\n";
        }


        // 8. Frontend Slop Engineering Guidelines
        $md .= "## 8. Panduan Rekayasa Frontend: Desain Sistem Anti-AI-Slop\n\n";
        $md .= "### " . $slop['philosophy']['title'] . "\n";
        $md .= $slop['philosophy']['description'] . "\n\n";
        $md .= "- **Display Typography**: " . $slop['typography']['display'] . "\n";
        $md .= "- **Body Typography**: " . $slop['typography']['body'] . "\n";
        $md .= "- **Monospaced Data**: " . $slop['typography']['mono'] . "\n";
        $md .= "- **Subtle Corners**: " . ($slop['subtle_corners']['description'] ?? 'Sudut tipis rounded-sm/md, dilarang efek kapsul rounded-full.') . "\n";
        $md .= "- **Dark Surface**: " . $slop['color_tokens']['dark_mode'] . "\n";
        $md .= "- **Light Surface**: " . $slop['color_tokens']['light_mode'] . "\n";
        $md .= "- **Interactive & 3D**: " . ($slop['interactive_and_3d']['description'] ?? 'Micro-animations 60fps & WebGL/Canvas interaktif.') . "\n";
        $md .= "- **Multi-Language (2-Tier)**: " . ($slop['multilingual_two_tier']['description'] ?? 'Dukungan Tier 1 ID/EN & Tier 2 Google Translate plugin.') . "\n";
        $md .= "- **Country Zone Phone**: " . ($slop['country_zone_phone_inputs']['description'] ?? 'Input nomor telepon wajib menggunakan country zone.') . "\n";
        $md .= "- **Thousand Separators**: " . ($slop['thousands_separator_formatting']['description'] ?? 'Pemisah ribuan otomatis untuk angka dan mata uang.') . "\n";
        $md .= "- **Local FontAwesome**: " . ($slop['local_fontawesome_library']['description'] ?? 'Library icon lokal SVG tanpa CDN eksternal.') . "\n";
        $md .= "- **Modular CMS Plugins**: " . ($slop['cms_plugins_and_isolated_layout']['description'] ?? 'Fitur berbasis plugin dengan navigasi/footer global terisolasi.') . "\n";
        $md .= "- **Educational UX**: " . ($slop['educational_plugin_ux']['description'] ?? 'Komponen informatif dan membimbing pengguna.') . "\n";
        $md .= "- **State Primitives**: " . $slop['component_primitives']['states'] . "\n";
        $md .= "- **Skeleton Loaders**: " . $slop['component_primitives']['skeletons'] . "\n";
        $md .= "- **Zero Native Dialogs**: " . $slop['component_primitives']['dialog_and_toasts'] . "\n\n";

        // 9. Backend Scalability Manifesto
        $md .= "## 9. Manifesto Skalabilitas Backend & Standar PostgreSQL\n\n";
        foreach ($scalability as $item) {
            $md .= "### " . $item['rule'] . "\n";
            $md .= $item['explanation'] . "\n\n";
        }

        // 9. AI Agent Handoff Protocol & Developer Education
        $eduDeck = PrdGeneratorService::getDeveloperEducationDeck($blueprint->nama_bisnis ?: $blueprint->client_name);
        $strat = $eduDeck['orchestration_strategy'] ?? [];
        $md .= "## 10. Panduan Edukasi Developer & Protokol Handoff AI Code Agent (Anti Context-Rot)\n\n";
        
        if (!empty($strat)) {
            $md .= "### " . ($strat['question'] ?? 'Strategi Orkestrasi AI: Apakah PRD Diberikan Sekaligus atau Sedikit demi Sedikit?') . "\n\n";
            $md .= "> 🚨 **" . ($strat['definitive_verdict'] ?? '') . "**\n\n";
            $md .= $strat['fatal_flaw_summary'] . "\n\n";
            $md .= "#### Alasan Teknis Mengapa \"Prompt Dumping\" Merusak Kode:\n";
            foreach ($strat['technical_reasons'] ?? [] as $tr) {
                $md .= "- **" . $tr['title'] . "**: " . $tr['desc'] . "\n";
            }
            $md .= "\n";
            $md .= "#### Metodologi Terbaik: " . ($strat['best_methodology'] ?? 'Vertical Slice Prompting') . "\n\n";
            $md .= ($strat['methodology_description'] ?? '') . "\n\n";
            $md .= "```text\n" . ($strat['workflow_ascii_art'] ?? '') . "\n```\n\n";
            $md .= "#### Keuntungan Pendekatan Ini:\n";
            foreach ($strat['benefits'] ?? [] as $b) {
                $md .= "- **" . $b['title'] . "**: " . $b['desc'] . "\n";
            }
            $md .= "\n";
            $md .= "> " . ($strat['card_handoff_guidance'] ?? '') . "\n\n";
        }

        $md .= "> " . $eduDeck['philosophy']['summary'] . "\n\n";
        $md .= "> ⚠️ **PERINGATAN KRUSIAL**: " . $eduDeck['philosophy']['warning'] . "\n\n";

        $md .= "### A. Pilihan Strategi Ingestion ke AI Agent\n\n";
        $md .= "#### 1. " . $eduDeck['modes']['single_shot']['name'] . " (`" . $eduDeck['modes']['single_shot']['badge'] . "`)\n";
        $md .= "- **Kapan Digunakan**: " . $eduDeck['modes']['single_shot']['when_to_use'] . "\n";
        $md .= "- **Risiko**: " . $eduDeck['modes']['single_shot']['risk'] . "\n";
        $md .= "- **Alur Kerja**:\n";
        foreach ($eduDeck['modes']['single_shot']['workflow'] as $wf) {
            $md .= "  - {$wf}\n";
        }
        $md .= "\n";

        $md .= "#### 2. " . $eduDeck['modes']['step_by_step']['name'] . " (`" . $eduDeck['modes']['step_by_step']['badge'] . "`)\n";
        $md .= "- **Kapan Digunakan**: " . $eduDeck['modes']['step_by_step']['when_to_use'] . "\n";
        $md .= "- **6 Tahap Eksekusi Presisi (Sprint-by-Sprint)**:\n";
        foreach ($eduDeck['modes']['step_by_step']['steps'] as $s) {
            $md .= "  - **Langkah {$s['step']}: {$s['title']}** (`{$s['target_tool']}`)  \n    {$s['instruction']}\n";
        }
        $md .= "\n";

        $md .= "### B. Playbook Per Tool IDE (Cursor, Claude Code, Windsurf, Devin)\n\n";
        foreach ($eduDeck['tool_guides'] as $tool) {
            $md .= "#### 🛠️ {$tool['name']} (`{$tool['command']}`)\n";
            $md .= "{$tool['usage']}\n\n";
        }

        $md .= "### C. Protokol Aturan Mutlak Handoff\n\n";
        foreach ($handoff['rules'] as $r) {
            $md .= "#### " . $r['rule'] . "\n";
            $md .= $r['desc'] . "\n\n";
        }

        // 9.5 AI-Shield & Autonomous Exploit Defense
        $aiShield = $prd['ai_security_blueprint'] ?? PrdGeneratorService::generateAiSecurityBlueprint($projectName, $extraContext ?? []);
        $md .= "## 10.5 AI-Shield & Secure Ingestion Pipeline (Pertahanan Eksploitasi Otonom AI)\n\n";
        $md .= "> **Latar Belakang & Vektor Serangan**: " . ($aiShield['incident_case_study']['context'] ?? '') . "\n";
        $md .= "> **Analisis Insiden Exploit Gym**: " . ($aiShield['incident_case_study']['attack_vector'] ?? '') . "\n\n";
        $md .= "| Kode Modul | Nama Modul Pertahanan | Peran & Tipe Arsitektur | Mekanisme Proteksi |\n";
        $md .= "|---|---|---|---|\n";
        foreach ($aiShield['defensive_modules'] ?? [] as $m) {
            $mCode = $m['code'] ?? 'SEC';
            $mName = $m['name'] ?? '-';
            $mType = $m['type'] ?? '-';
            $mDesc = $m['description'] ?? '-';
            $md .= "| `{$mCode}` | **{$mName}** | {$mType} | {$mDesc} |\n";
        }
        $md .= "\n";

        // 9.6 20 Agentic AI Concepts Matrix
        $agenticMatrix = $prd['agentic_ai_matrix'] ?? PrdGeneratorService::generateAgenticAiConceptsMatrix($projectName, $mvpFeatures);
        $md .= "## 10.6 Matriks 20 Konsep AI Agentic & Rekomendasi Terarah untuk Klien\n\n";
        $md .= "> **Efisiensi Investasi**: Mengeliminasi kompleksitas berlebih dan memastikan sistem klien hanya mengadopsi konsep AI yang bernilai nyata bagi bisnis.\n\n";
        $md .= "| No | Konsep Agentic AI | Pilar Arsitektur | Status Rekomendasi | Aplikasi Nyata untuk Sistem Klien |\n";
        $md .= "|---|---|---|---|---|\n";
        foreach ($agenticMatrix['concepts'] ?? [] as $c) {
            $cNum = sprintf('%02d', $c['id']);
            $cName = $c['name'];
            $cPillar = $c['pillar'];
            $cRec = !empty($c['recommended']) ? '✅ **DIREKOMENDASIKAN**' : '⚪ Opsional (Masa Depan)';
            $cApp = $c['client_application'];
            $md .= "| `{$cNum}` | **{$cName}**<br/><small>{$c['badge']}</small> | {$cPillar} | {$cRec} | {$cApp} |\n";
        }
        $md .= "\n";

        // 9.7 Server Hardware Capacity Sizing Engine
        $serverSizing = $prd['server_hardware_sizing'] ?? PrdGeneratorService::calculateServerHardwareSizing($projectName, $exec['problem_statement'] ?? '', $mvpFeatures, $extraContext ?? []);
        $md .= "## 10.7 Analisis Kapasitas Spesifikasi Server (Hardware Sizing Engine)\n\n";
        $md .= "- **Rekomendasi Paket Server**: **" . ($serverSizing['tier_name'] ?? 'Production VPS') . "**\n";
        $md .= "- **Profil Beban Kerja**: " . ($serverSizing['workload_profile'] ?? '-') . "\n";
        $md .= "- **Estimasi Investasi Server**: **" . ($serverSizing['estimated_monthly_investment']['idr'] ?? '-') . "** (" . ($serverSizing['estimated_monthly_investment']['usd'] ?? '-') . ")\n\n";
        $md .= "### Rincian Alokasi Komputasi & Memori Terukur:\n";
        $md .= "- **vCPU Dedicated**: " . ($serverSizing['specifications']['vcpu']['count'] ?? '-') . " (" . ($serverSizing['specifications']['vcpu']['architecture'] ?? '') . ")\n";
        $md .= "- **RAM Dedicated ECC**: " . ($serverSizing['specifications']['ram']['total'] ?? '-') . " (Postgres Buffer 28%, PHP-FPM 25%, Redis 15%)\n";
        $md .= "- **Penyimpanan NVMe PCIe 4.0**: " . ($serverSizing['specifications']['storage']['capacity'] ?? '-') . " (" . ($serverSizing['specifications']['storage']['speed'] ?? '') . ")\n";
        $md .= "- **Bandwidth Jaringan**: " . ($serverSizing['specifications']['network']['bandwidth'] ?? '-') . " (" . ($serverSizing['specifications']['network']['port_speed'] ?? '') . ")\n\n";

        // 11. Governance & DoD
        $md .= "## 11. Tata Kelola, Kualitas & Definition of Done (DoD)\n\n";
        foreach ($governance['definition_of_done'] ?? [] as $dod) {
            $md .= "- [x] {$dod}\n";
        }
        $md .= "\n";

        // 10.5 Itemized Scope Breakdown
        $itemized = $prd['itemized_cost_breakdown'] ?? PrdGeneratorService::calculateItemizedEstimation($blueprint);
        if (!empty($itemized['items'])) {
            $md .= "## 11.5 Rincian Biaya Spesifikasi Berdasarkan Poin Input Klien (Itemized Scope Metric)\n\n";
            $md .= "> **Transparansi Investasi**: Setiap komponen biaya diturunkan langsung secara matematis dari formulir spesifikasi yang diajukan oleh pihak klien.\n\n";
            $md .= "| Kategori | Kode | Komponen / Fitur Spesifik | Kompleksitas | Bobot Biaya |\n";
            $md .= "|---|---|---|---|---|\n";
            foreach ($itemized['items'] as $it) {
                $cCat = $it['category'] ?? '-';
                $cCode = $it['code'] ?? '-';
                $cTitle = $it['title'] ?? '-';
                $cComp = $it['complexity'] ?? '-';
                $cAmount = ($it['amount'] < 0 ? '-Rp ' : 'Rp ') . number_format(abs($it['amount']), 0, ',', '.');
                $md .= "| **{$cCat}** | `{$cCode}` | {$cTitle} | {$cComp} | **{$cAmount}** |\n";
            }
            $md .= "| **TOTAL BASE SCOPE (STANDARD VELOCITY)** | | | | **Rp " . number_format($itemized['base_subtotal'] ?? 0, 0, ',', '.') . "** |\n";
            $md .= "| **TERMIN DP 50% (DIBAYARKAN VIA MIDTRANS SNAP)** | | | | **Rp " . number_format($itemized['standard_dp'] ?? 0, 0, ',', '.') . "** |\n";
            $md .= "| **PELUNASAN SETELAH LOLOS UAT & SERAH TERIMA** | | | | **Rp " . number_format($itemized['standard_pelunasan'] ?? 0, 0, ',', '.') . "** |\n\n";
        }

        // 12. Velocity Pricing
        $md .= "## 12. Opsi Akselerasi Peluncuran (Velocity Pricing Continuum)\n\n";
        $md .= "| Opsi Paket | Estimasi Durasi | Nilai Investasi | Termin DP (50%) | Alokasi Squad & Engine |\n";
        $md .= "|---|---|---|---|---|\n";
        foreach ($pricing as $p) {
            $pname = $p['name'] ?? '';
            $pduration = $p['duration'] ?? '';
            $pamount = 'Rp ' . number_format($p['contract_amount'] ?? 0, 0, ',', '.');
            $pdp = 'Rp ' . number_format($p['dp_amount'] ?? 0, 0, ',', '.');
            $psquad = $p['squad_allocation'] ?? ($p['ai_quota_spec'] ?? '-');
            $md .= "| **{$pname}** | {$pduration} | {$pamount} | {$pdp} | {$psquad} |\n";
        }
        $md .= "\n";

        // 13. 10 Pilar Software Factory OS
        $pillars = $prd['software_factory_os_pillars'] ?? PrdGeneratorService::generateSoftwareFactoryOsPillars($projectName);
        $md .= "## 13. Matriks 10 Pilar Software Factory OS (100% Tingkat Kematangan & Standar Turnkey)\n\n";
        $md .= "> **Jaminan Mutu Rekayasa 100%**: Setiap pilar dirancang secara deterministik tanpa kompromi performa atau celah arsitektur.\n\n";
        $md .= "| No | Pilar Rekayasa | Kematangan | Status | Kemampuan Utama & Standar Dunia |\n";
        $md .= "|---|---|---|---|---|\n";
        foreach ($pillars as $pil) {
            $pNum = sprintf('%02d', $pil['pillar']);
            $pName = $pil['name'];
            $pScore = $pil['maturity_score'];
            $pStat = $pil['status'];
            $pStd = $pil['world_class_standard'];
            $md .= "| `{$pNum}` | **{$pName}** | `{$pScore}` | ✅ {$pStat} | {$pStd} |\n";
        }
        $md .= "\n";

        // 14. World Class Standard Advisory
        $advisory = $prd['world_class_standard_advisory'] ?? PrdGeneratorService::generateWorldClassStandardAdvisory($projectName);
        $md .= "## 14. Rekomendasi & Opsi Strategis Menuju World Class Enterprise Standard\n\n";
        $md .= "> " . ($advisory['overview'] ?? '') . "\n\n";
        foreach ($advisory['strategic_pillars'] ?? [] as $sp) {
            $md .= "### " . $sp['area'] . "\n\n";
            $md .= "- **Kapabilitas Saat Ini**: " . $sp['current_capability'] . "\n";
            $md .= "- **Opsi Peningkatan World Class**: " . $sp['world_class_option'] . "\n";
            $md .= "- **Dampak & Manfaat Bisnis**: " . $sp['benefit'] . "\n\n";
        }
        if (!empty($advisory['implementation_roadmap'])) {
            $md .= "### Roadmap Implementasi Bertahap Menuju Skala Enterprise:\n\n";
            foreach ($advisory['implementation_roadmap'] as $fase => $desc) {
                $md .= "- **{$fase}**: {$desc}\n";
            }
            $md .= "\n";
        }

        // 15. Composable 12-Sector Blueprint Contract & 4 Pillars of AI Assumption Rejection
        $univSchema = $prd['universal_blueprint_schema'] ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
        if (!empty($univSchema)) {
            $uMeta = $univSchema['project_metadata'] ?? [];
            $md .= "## 15. Kontrak Arsitektur Composable 12-Sektor, State Machine & Edge-Case Playbook (Zero-Assumption Schema v1.0)\n\n";
            $md .= "- **Sektor Fungsional Utama**: `" . ($uMeta['primary_sector'] ?? '-') . "` — " . ($uMeta['primary_sector_name']['id'] ?? '') . "\n";
            $md .= "- **Archetype Arsitektur**: `" . ($uMeta['architecture_archetype'] ?? 'COMPOSITE') . "`\n";
            $md .= "- **Core Primitives Aktif**: `" . implode('`, `', $uMeta['injected_primitives'] ?? []) . "`\n";
            $md .= "- **Standar Kepatuhan Sektor**: " . implode(' | ', $uMeta['compliance_standards'] ?? []) . "\n\n";

            if (!empty($univSchema['state_machines'])) {
                $md .= "### A. Deterministic State Machines & Transisi Atomik\n\n";
                foreach ($univSchema['state_machines'] as $sm) {
                    $md .= "#### Entity: `{$sm['entity']}` (`{$sm['enum_class']}` | Initial: `{$sm['initial_state']}`)\n\n";
                    $md .= "| From | To | Action Class (`DB::transaction`) | Guard Conditions | Side Effects |\n";
                    $md .= "|---|---|---|---|---|\n";
                    foreach (($sm['transitions'] ?? []) as $tr) {
                        $md .= "| `" . implode(', ', $tr['from'] ?? []) . "` | `{$tr['to']}` | `{$tr['action']}` | " . implode(', ', $tr['guards'] ?? []) . " | " . implode(', ', $tr['side_effects'] ?? []) . " |\n";
                    }
                    $md .= "\n";
                }
            }

            if (!empty($univSchema['domain_invariants']['concurrency_locks'])) {
                $md .= "### B. Kontrak Concurrency & Pessimistic Locking (`SELECT ... FOR UPDATE`)\n\n";
                $md .= "| Operasi Kritikal | Mekanisme Penguncian | Timeout | Fallback Deterministik |\n";
                $md .= "|---|---|---|---|\n";
                foreach ($univSchema['domain_invariants']['concurrency_locks'] as $lk) {
                    $md .= "| {$lk['operation']} | `{$lk['mechanism']}` | {$lk['timeout_seconds']}s | {$lk['fallback_action']} |\n";
                }
                $md .= "\n";
            }

            if (!empty($univSchema['edge_case_playbooks'])) {
                $md .= "### C. Edge-Case & Conflict Resolution Playbook\n\n";
                foreach ($univSchema['edge_case_playbooks'] as $idx => $play) {
                    $n = $idx + 1;
                    $md .= "{$n}. **Skenario (`{$play['source_module']}`):** {$play['scenario']}\n";
                    $md .= "   - **Aturan Resolusi Mutlak:** {$play['resolution_rule']}\n";
                }
                $md .= "\n";
            }

            // 16. Stack Topology Preset & 7 Zero-Assumption Dimensions
            $stackC = $univSchema['tech_stack_contract'] ?? [];
            $apiC = $univSchema['api_and_envelope_contract'] ?? [];
            $sagaC = $univSchema['distributed_saga_and_outbox_contract'] ?? [];
            $lifeC = $univSchema['data_lifecycle_and_pii_governance'] ?? [];

            $md .= "## 16. Topologi Tech Stack Terkurasi & 7 Dimensi Zero-Assumption (Schema v2.0.0)\n\n";
            $md .= "- **Preset Topologi & Tech Stack**: `[" . ($stackC['short_code'] ?? 'M1') . "] " . ($stackC['preset_code'] ?? 'STACK_M1_LARAVEL_MONOLITH') . "` (`" . ($stackC['topology'] ?? 'MONOLITH') . "`)\n";
            $md .= "- **Kontrak Tipe Antar-Layer**: " . ($stackC['inter_layer_type_contract']['mechanism'] ?? '-') . "\n";
            $md .= "- **Dimensi 1 (API & Error Envelope)**: `" . ($apiC['standard'] ?? 'RFC 9457 (application/problem+json)') . "` + Keyset Cursor O(1) (`meta.cursor.next`)\n";
            $md .= "- **Dimensi 2 (Distributed Saga & Outbox)**: Tabel wajib `" . ($sagaC['outbox_table'] ?? 'outbox_messages') . "` + `" . count($sagaC['compensating_actions'] ?? []) . "` Compensating Actions deterministik\n";
            $md .= "- **Dimensi 3 (Formula Matematika & Penny Rule)**: `" . count($univSchema['deterministic_formulas'] ?? []) . "` formula eksak (`DECIMAL(19,4)`, `HALF_UP`, selisih Rp 1 dialokasikan ke cicilan terakhir)\n";
            $md .= "- **Dimensi 4 (Integrasi Eksternal & Webhook Security)**: Tabel `" . ($univSchema['external_integration_contracts']['inbound_webhook_vault_table'] ?? 'webhook_inbound_logs') . "` + HMAC-SHA256 + Exponential Backoff\n";
            $md .= "- **Dimensi 5 (Data Lifecycle & UU PDP)**: `" . ($lifeC['cascade_delete_ban'] ?? 'ON DELETE RESTRICT') . "` (`IMMUTABLE_LEDGER` / `ANONYMIZE_PII` / `SOFT_ARCHIVE`)\n";
            $md .= "- **Dimensi 6 (UI Screen Archetypes)**: `" . count($univSchema['ui_screen_archetypes'] ?? []) . "` pola layar operasional + Empty/Skeleton/RFC 9457 Error States\n";
            $md .= "- **Dimensi 7 (Acceptance Test Matrix)**: `" . count($univSchema['acceptance_test_matrix'] ?? []) . "` skenario Given-When-Then pra-kompilasi\n\n";
        }

        return $md;
    }


    /**
     * Synthesize complete AI Coding Rules (.cursorrules / CLAUDE.md / AGENTS.md)
     * Tailored for Cursor, Claude Code, Antigravity, and Windsurf AI agents.
     */
    public static function toCursorrules(VisionBlueprint $blueprint, array $prd = [], bool $isEn = false): string
    {
        if (empty($prd)) {
            $prd = $blueprint->prd_content ?? PrdGeneratorService::generate($blueprint, null, $isEn ? 'en' : 'id');
        }

        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $specId = strtoupper(substr((string)$blueprint->id, 0, 10));
        $exec = $prd['executive_summary'] ?? [];
        $actors = $prd['system_actors'] ?? [];
        $erd = $prd['erd_schema']['tables'] ?? [];
        $mvpFeatures = $prd['features']['mvp_phase1'] ?? [];
        $univSchema = $prd['universal_blueprint_schema'] ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
        $uMeta = $univSchema['project_metadata'] ?? [];
        $stackC = $univSchema['tech_stack_contract'] ?? [];

        $rules = "# .cursorrules / CLAUDE.md — AI AGENT SYSTEM INSTRUCTIONS\n";
        $rules .= "# Project: {$projectName}\n";
        $rules .= "# Blueprint Spec ID: {$specId}\n";
        $rules .= "# Engine: Neriah Pro AI Project OS (Generated: " . ($blueprint->created_at?->format('Y-m-d') ?: date('Y-m-d')) . ")\n\n";
        $rules .= "<ZERO_ASSUMPTION_DIRECTIVES>\n";
        $rules .= "1. STRICT COMPLIANCE: You are an execution engine. Do NOT invent columns, states, roles, or workflows outside `.agents/BLUEPRINT_SCHEMA.json`, `.agents/STACK_TOPOLOGY_CONTRACT.md`, and `PROJECT_OS_EXECUTION_BLUEPRINT.md`.\n";
        $rules .= "2. MISSING REQUIREMENT PROTOCOL: If an edge case or relation is not explicitly defined in the Data Dictionary or State Machine, STOP and ask the developer before generating code.\n";
        $rules .= "3. ATOMICITY & SAGA OUTBOX: Every state transition MUST run inside an atomic DB transaction, write outbound events to `outbox_messages`, and execute compensating actions on downstream failure.\n";
        $rules .= "4. LOCKING: Every query modifying `inventory_batches`, `inventory_movements`, `allocatable_resources`, `bookings`, `accounts`, `journal_entries`, `event_seats`, or `fund_programs` MUST acquire `lockForUpdate()` first.\n";
        $rules .= "5. STACK TOPOLOGY LOCK: Target preset is `[" . ($stackC['short_code'] ?? 'M1') . "] " . ($stackC['preset_code'] ?? 'STACK_M1_LARAVEL_MONOLITH') . "` (`" . ($stackC['topology'] ?? 'MONOLITH') . "`). Follow its directory layout and inter-layer type contract (`" . ($stackC['inter_layer_type_contract']['mechanism'] ?? 'Type-Safe Contract') . "`).\n";
        $rules .= "6. API, WEBHOOK & PENNY MATH: Return errors strictly as RFC 9457 (`application/problem+json`), log inbound webhooks to `webhook_inbound_logs` with HMAC-SHA256 before async jobs, and use `DECIMAL(19,4)` `HALF_UP` with final-installment penny allocation (never IEEE-754 float).\n";
        $rules .= "7. DATA LIFECYCLE & UU PDP: Never use blind `ON DELETE CASCADE`. Enforce `ON DELETE RESTRICT`, `IMMUTABLE_LEDGER` contra-entries, and `ANONYMIZE_PII` (`[REDACTED_USER_<ULID>]`).\n";
        $rules .= "</ZERO_ASSUMPTION_DIRECTIVES>\n\n";
        $rules .= "You are pair programming on \"{$projectName}\", a mission-critical enterprise digital system.\n";
        $rules .= "Always adhere strictly to the architectural constraints, database schemas, and 6-Stage ECC Harness (`PLAN -> TEST -> IMPLEMENT -> REVIEW -> VERIFY -> REMEMBER & DEPLOY`) defined below.\n\n";
        $rules .= "---\n\n";

        // 1. Context
        $rules .= "## 1. PROJECT SPECIFICATION & COMPOSABLE SECTOR CONTEXT\n";
        $rules .= "- **Project Name**: {$projectName}\n";
        $rules .= "- **Primary Functional Sector**: `" . ($uMeta['primary_sector'] ?? 'SECTOR_08_PROFESSIONAL_PSA') . "` (" . ($uMeta['primary_sector_name']['en'] ?? 'Enterprise Sector') . ")\n";
        $rules .= "- **Architecture Archetype**: `" . ($uMeta['architecture_archetype'] ?? 'COMPOSITE') . "`\n";
        $rules .= "- **Stack Topology Preset**: `[" . ($stackC['short_code'] ?? 'M1') . "] " . ($stackC['preset_code'] ?? 'STACK_M1_LARAVEL_MONOLITH') . "` (`" . ($stackC['topology'] ?? 'MONOLITH') . "`)\n";
        $rules .= "- **Injected Core Primitives**: `" . implode('`, `', $uMeta['injected_primitives'] ?? []) . "`\n";
        $rules .= "- **Compliance Standards**: " . implode(' | ', $uMeta['compliance_standards'] ?? []) . "\n";
        $rules .= "- **Primary Problem**: " . ($blueprint->masalah_utama ?: 'Enterprise operational digitization and centralized workflows.') . "\n";
        $rules .= "- **Core Objective / Success Metrics**: " . ($blueprint->tujuan_utama ?: 'High efficiency, automated audit trails, and zero downtime.') . "\n";
        $rules .= "- **Target Audience**: " . ($blueprint->target_audiens ?: 'Internal operators and external customers.') . "\n";
        $rules .= "- **Target Platform**: " . ($blueprint->user_metadata['target_platform'] ?? 'Modern Web Application Responsive & PWA') . "\n";
        $rules .= "- **Tech Stack**: Laravel 13, Filament v5, Livewire 4, Flux UI, Alpine.js, Tailwind CSS, PostgreSQL 16 (Strict ULID + pgvector 768-dim), Redis 7, Railpack FrankenPHP.\n\n";
        $rules .= "---\n\n";

        // 2. Protocols
        $rules .= "## 2. STRICT ARCHITECTURAL PROTOCOLS (ZERO COMPROMISE)\n\n";
        $rules .= "### A. Database Primary Keys & PostgreSQL ULID Standard\n";
        $rules .= "- **Primary Keys**: ALWAYS use ULID (`->ulid('id')->primary()` and `HasUlids` trait) for all business domain tables.\n";
        $rules .= "- **Strict PostgreSQL Compatibility**: NEVER use `\$table->uuid('id')` for models using ULID (`HasUlids`), as PostgreSQL strictly rejects 26-char ULID strings with `SQLSTATE[22P02]`. ALWAYS use `->ulid('id')`.\n";
        $rules .= "- **Foreign Keys**: ALWAYS use `->foreignUlid('parent_id')` to match ULID primary keys.\n";
        $rules .= "- **Zero Auto-Increment**: NEVER use `\$table->id()` or auto-incrementing integers for business domain entities.\n\n";

        $rules .= "### B. Keyset Cursor Pagination O(1) & Ultimate Enterprise Table Standard\n";
        $rules .= "- **Tabel Wajib Search & Pagination O(1)**: Setiap tabel wajib menggunakan search berindeks dan pagination bulletproof untuk jutaan data dan viewer: Keyset Cursor Pagination O(1) (`cursorPaginate()`). Dilarang keras menggunakan offset pagination (`paginate()`).\n";
        $rules .= "- **Mandatory Keyset**: ALWAYS use `cursorPaginate()` with stable keyset fallbacks (e.g. `->orderBy('id', 'asc')`).\n";
        $rules .= "- **Floating Sticky Header**: Header tabel wajib melayang saat scroll (`sticky top-0 z-20 bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800`).\n";
        $rules .= "- **Freeze Column (Pin Kolom Kiri/Kanan)**: Wajib mendukung pinning kolom: Kolom seleksi & Primary ID beku di kiri (`sticky left-0 z-30`), dan kolom aksi beku di kanan (`sticky right-0 z-30`).\n";
        $rules .= "- **Multi-Select & Floating Batch Action Bar**: Checkbox di setiap baris + 'Select All' di header. Saat 1+ baris terpilih, munculkan floating batch bar di bawah layar (`sticky bottom-4 z-40 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-4 py-2.5 rounded-xs shadow-2xl`) untuk aksi massal (Export CSV, Update Status, Hapus).\n";
        $rules .= "- **Zebra Effect & Cell Focus Micro-Hover**: Baris selang-seling (`odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/60 dark:even:bg-zinc-900/40`), efek hover baris pekat (`hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80`), dan cell yang disorot kursor mouse mendapatkan focus ring aksen (`hover:ring-1 hover:ring-inset hover:ring-emerald-500/60 cursor-pointer`).\n";
        $rules .= "- **Density Switcher**: Dukung 3 tingkat kerapatan baris: Compact (`py-1.5 px-3 text-xs`), Normal (`py-3 px-4 text-xs`), Relaxed (`py-4 px-5 text-sm`).\n";
        $rules .= "- **Column Visibility Selector & Tri-State Sort**: Sediakan dropdown untuk mematikan/menghidupkan kolom dinamis, dan header yang dapat di-sort (Asc, Desc, None) dengan ikon panah aktif.\n";
        $rules .= "- **Quick Export**: Sediakan tombol ekspor 1-klik ke format CSV dan JSON untuk baris yang terpilih atau hasil query cursor saat ini.\n";
        $rules .= "- **Dual View (Grid & List View)**: Setiap membuat komponen list data, selalu sediakan tombol toggle tampilan Grid View dan List View switcher untuk fleksibilitas visual.\n";
        $rules .= "- **Instant Clear Button (\"X\") pada Search Field**: Field search selalu wajib ada icon \"X\" (tombol hapus instan) untuk mereset kata kunci secara instan dengan satu klik.\n\n";

        $rules .= "### C. Zero Tolerance for Magic Strings & Fuzzy Searches (\"Anti-Dosa Hardcode / Prefix WHERE LIKE\")\n";
        $rules .= "- NEVER identify entities, transactions, or state via fuzzy string matching (`WHERE LIKE 'retail_%'`, `LIKE '%APEX%'`, demo aliases, or slugs). Fuzzy prefix searching is fundamentally broken for long-term scalability.\n";
        $rules .= "- ALWAYS identify models, orders, and documents strictly via **Concrete Relational Foreign Keys** or **Exact Primary Key Lookups** (`find(\$id)`).\n";
        $rules .= "- Business states (DP settlement, contract signing, scope freeze) must derive strictly from concrete status columns/enums (`status === 'settlement'`), never string sniffing.\n\n";

        $rules .= "### D. Multi-Language 2-Tier Architecture (Database Native JSON & Whitelisted Global Plugin)\n";
        $rules .= "- **Tier 1 (Database Native JSON)**: Selalu sediakan multi-bahasa Tier 1 dari database di mana kolomnya adalah JSON (bukan string tunggal biasa) dan di-cast sebagai `'array'` (`{\"id\": \"...\", \"en\": \"...\"}`). Admin backend Filament bisa mengelola dua versi bahasa (ID & EN) secara resmi.\n";
        $rules .= "- **Tier 2 (Global Plugin Google Translate)**: Ditenagai oleh plugin Google Translate di frontend, di mana daftar opsi bahasa disimpan di database backend admin (`CmsGlobalSetting`) dan dapat ditentukan whitelist bahasa mana saja yang dimunculkan ke publik.\n";
        $rules .= "- **Single Unified Navbar Dropdown**: Di navigasi, dilarang membuat switch bahasa redundant (misal dobel tombol ID/EN dan tombol Global berdampingan). Cukup gunakan SATU dropdown terpadu yang memuat dua list group: Tier 1 (Native Precise) dan Tier 2 (Global Translate).\n\n";

        $rules .= "### E. Cache Serialization Safety\n";
        $rules .= "- NEVER cache raw Eloquent model instances in `Cache::rememberForever()`. Serializing Eloquent models across lifecycles causes `__PHP_Incomplete_Class` errors.\n";
        $rules .= "- ALWAYS cache primitive attribute arrays (`\$record->getAttributes()`) or JSON strings, and reconstitute models via `(new Model)->newFromBuilder(\$cachedAttributes)`.\n\n";

        $rules .= "### F. Managed Sprint Capacity, Anti-Collision Batch Engineering & Local Gantt Dashboard\n";
        $rules .= "- **Managed Capacity Discipline**: Tidak menerima proyek paralel tak terbatas demi menjaga kualitas rekayasa enterprise, nol bug, dan anti-AI-slop. Setiap proyek dikunci ke dalam batch waktu sprint terkelola (e.g. Batch 1, Batch 2, Batch Q1).\n";
        $rules .= "- **Anti-Collision Architecture**: Mencegah tabrakan jadwal kickoff antar klien (schedule collision) dan mencegah transaksi ganda (payment collision) menggunakan generator ULID unik dan webhook idempotency.\n";
        $rules .= "- **Zero CDN Latency Requirement**: Seluruh library JavaScript yang digunakan untuk merender visualisasi (seperti Mermaid.js, Alpine.js, Frappe Gantt) WAJIB diunduh dan disimpan secara lokal di dalam repository (`public/js/vendor/`). DILARANG KERAS memuat script via CDN eksternal.\n";
        $rules .= "- **Interactive Gantt & Timeline Monitoring**: Setiap blueprint wajib men-generate diagram Mermaid Gantt (`sprint_gantt_mermaid`) yang dapat dimonitor melalui backend admin dashboard untuk melacak ketercapaian milestone DoD (Definition of Done) per sprint.\n\n";

        $rules .= "### G. Strict Prohibition of Page-Level Locale Switcher Dropdowns (Standar Wajib TranslatableTabs In-Place)\n";
        $rules .= "- **HARAM HUKUMNYA** menggunakan dropdown pemilih bahasa di pojok kanan atas halaman (`LocaleSwitcher::make()`, `LaraZeus\\SpatieTranslatable`, atau page-level locale switchers) di seluruh resource dan page Filament:\n";
        $rules .= "  1. **Melenyapkan Dropdown List di Kanan Atas Halaman**:\n";
        $rules .= "     - Menghapus 100% penggunaan `LocaleSwitcher::make()` dan trait `LaraZeus\\SpatieTranslatable` dari seluruh halaman Filament (`CreateRecord`, `EditRecord`, `ListRecords`, `ManageRecords`, dan `ViewRecord`).\n";
        $rules .= "     - Pojok kanan atas halaman wajib bersih dan luas tanpa dropdown bahasa global yang membingungkan.\n";
        $rules .= "  2. **Tab Bahasa Tier 1 Langsung Pada Kotak Input Masing-Masing**:\n";
        $rules .= "     - Setiap field yang menyimpan teks dwibahasa (JSON / Spatie Translatable) wajib memiliki sub-tab independen langsung di dalam form-nya menggunakan `App\\Filament\\Forms\\Components\\TranslatableTabs`:\n";
        $rules .= "       - 🇮🇩 **Indonesia (ID)**\n";
        $rules .= "       - 🇬🇧 **English (EN)**\n";
        $rules .= "  3. **Fitur Google Translate 1-Klik di Setiap Input (`GoogleTranslateService`)**:\n";
        $rules .= "     - Di samping kanan label atau di dalam tab masing-masing input, terpasang tombol translasi cerdas berbasis `App\\Services\\Translation\\GoogleTranslateService`:\n";
        $rules .= "       - **Di Tab Indonesia**: Tombol `✨ Terjemahkan ke EN` → Mengklik tombol ini seketika menerjemahkan teks Indonesia dan otomatis mengisikannya ke tab Bahasa Inggris.\n";
        $rules .= "       - **Di Tab English**: Tombol `✨ Terjemahkan ke ID` dan `🔄 Tarik dari ID` → Untuk menyinkronkan atau memperbarui terjemahan jika narasi Indonesia diubah.\n";
        $rules .= "     - **Auto-Translate saat Simpan (`HasTranslatableTabs`)**:\n";
        $rules .= "       - Setiap Page `CreateRecord` dan `EditRecord` untuk model translatable wajib menggunakan trait `App\\Filament\\Concerns\\HasTranslatableTabs`.\n";
        $rules .= "       - Jika admin sedang buru-buru dan hanya mengisi tab Indonesia lalu menekan tombol Simpan, sistem secara otomatis menerjemahkan teks tersebut ke Bahasa Inggris di latar belakang sebelum disimpan ke database, sehingga data publik tidak pernah kosong.\n\n";

        $rules .= "### H. ECC (Everything Claude Code) Universal Architecture & Multi-Agent Collaboration Protocol\n";
        $rules .= "- **Native Antigravity & ECC Compatibility**: Project ini dirancang dengan struktur agen ECC (Everything Claude Code / Antigravity Native Layout) untuk menjamin hasil koding presisi tinggi dan bebas halusinasi.\n";
        $rules .= "- **Ponytail Decision Ladder**:\n";
        $rules .= "  1. *Rung 1 (Framework Native First)*: Maksimalkan fitur bawaan Laravel 13, Filament v5, dan Livewire 4 sebelum menulis kode kustom.\n";
        $rules .= "  2. *Rung 2 (Reuse Existing Codebase)*: Gunakan kembali service, trait, dan komponen yang sudah ada sebelum menciptakan komponen baru.\n";
        $rules .= "  3. *Rung 3 (Configuration Over Code)*: Utamakan konfigurasi environment dan file config daripada hardcoded logic.\n";
        $rules .= "  4. *Rung 4 (Surgical & Minimalist Diff)*: Terapkan perubahan kode yang presisi, hemat token, dan anti-regresi.\n";
        $rules .= "- **Specialized Subagents Delegation Hierarchy**:\n";
        $rules .= "  - `architect`: Perancangan arsitektur tingkat tinggi dan kepatuhan PRD.\n";
        $rules .= "  - `php-reviewer`: Peninjauan kepatuhan PHP 8.5+, Laravel 13, dan typing constraint Filament v5.\n";
        $rules .= "  - `database-reviewer`: Validasi skema PostgreSQL Strict ULID, indeks, dan keyset pagination O(1).\n";
        $rules .= "  - `security-reviewer`: Audit keamanan OWASP, RBAC, dan validasi webhook payment gateway.\n";
        $rules .= "  - `browser-qa`: Pengujian visual UI/UX, rasio kontras WCAG AAA, dan kepatuhan dark/light mode.\n";
        $rules .= "- **Automated Slash Workflows**: `/plan` (analisis & risiko arsitektur), `/build-fix` (resolusi error minimalis), `/code-review` (audit mendalam), `/checkpoint` (titik aman pemulihan), `/tdd-workflow` (pengujian TDD).\n\n";

        $rules .= "### I. Mandatory Clean Code, Anti-Bloat & Modular Decomposition Protocol (Anti-God Object)\n";
        $rules .= "- **Zero Tolerance for Monolithic God Objects**: Setiap file PHP, Controller, Service, Blade View, atau React Component DILARANG KERAS membengkak ribuan baris (>400 baris peringatan keras, >800 baris pelanggaran fatal). Selalu dekomposisi menjadi unit terkecil yang kohesif.\n";
        $rules .= "- **Single Responsibility Principle (SRP)**: Setiap class/komponen hanya boleh memiliki satu alasan untuk berubah. Pisahkan logika presentasi, state management, orchestration, dan infrastruktur ke dalam modul terisolasi.\n";
        $rules .= "- **Controller & Service Decomposition**:\n";
        $rules .= "  1. Controller gemuk (>500 baris) WAJIB dipecah menjadi Sub-Controllers terfokus di sub-namespace (misal: `DiscoveryController`, `CommercialController`, `ExportController`, `AuditController`).\n";
        $rules .= "  2. Service gemuk (>800 baris) WAJIB dipecah menjadi domain sub-services terfokus (misal: `EstimatorService`, `ErdSchemaService`, `ManifestoService`, `MarkdownExporterService`) dengan master orchestrator facade yang mempertahankan backwards compatibility.\n";
        $rules .= "- **Frontend Component & Custom Hooks Modularization**:\n";
        $rules .= "  1. React Island / Livewire component besar WAJIB dipecah menjadi sub-komponen UI modular (misal: header, tab panels, modals, status cards) di folder `components/` atau `partials/`.\n";
        $rules .= "  2. State management dan API data-fetching yang kompleks WAJIB diekstrak ke dalam custom hooks independen (misal: `useBlueprintState`, `useBlueprintApi`, `useBlueprintModals`).\n";
        $rules .= "- **Template & Stubs Isolation (`.stub`)**:\n";
        $rules .= "  1. DILARANG KERAS menanam string heredoc template ratusan baris di dalam file service PHP. Seluruh template generator boilerplate wajib diekstrak ke dalam file `.stub` terisolasi di `resources/stubs/` dan dimuat via `StubLoader`.\n";
        $rules .= "- **Surgical & Minimalist Diffs**:\n";
        $rules .= "  1. AI Coding Agent WAJIB membuat diff kode yang presisi, hemat token, dan anti-regresi. Hindari menulis ulang seluruh file jika hanya sebagian baris yang dimodifikasi.\n\n";

        $rules .= "---\n\n";

        // 3. UI/UX Protocols
        $rules .= "## 3. UI/UX & FRONTEND DIRECTIVES\n\n";
        $rules .= "### A. Subtle Round Corners & Zero-Capsule Standard\n";
        $rules .= "- **Border Radius Tipis / Opsi Tanpa Round Corner**: UI/UX border radius WAJIB subtil dan tipis (`rounded-none`, `rounded-xs` (2px), `rounded-sm` (4px), maksimal `rounded-md` (6px)). Sediakan opsi murni tanpa round corner (`rounded-none`) untuk nuansa sharp brutalism teknikal.\n";
        $rules .= "- **STRICT BAN ON CAPSULE/PILL SHAPES**: DILARANG KERAS menggunakan `rounded-full` atau bentuk kapsul/pil pada tombol, kartu, badge, atau input. Bentuk kapsul mereduksi area klik, membuang ruang visual, dan tampak seperti template murahan 'AI-Slop'.\n\n";

        $rules .= "### B. Clean Solid Brutalist Theme (Strict Ban on Gaudy Multi-Color Gradients)\n";
        $rules .= "- **Hindari Gradient Style**: Hindari gradient style dalam membuat theme UI.\n";
        $rules .= "- **Solid & Sharp Brutalism**: Gunakan warna solid, sleek monochrome, border presisi tajam (`border-zinc-200 dark:border-zinc-800`), dan background solid (`bg-white dark:bg-zinc-950`). Jangan gunakan background gradien warna-warni yang mencolok (\"AI slop / template murahan\").\n\n";

        $rules .= "### C. Comprehensive Dark & Light Mode Fidelity & Unique Hover Micro-Effects\n";
        $rules .= "- **100% Dark & Light Mode Compliance**: Selalu buat theme dark / light dan pastikan setiap komponen yang dibuat comply dengan theme ini secara seimbang.\n";
        $rules .= "- **Unique Hover Micro-Effects**: Saat kursor mouse menyentuh elemen interaktif (tombol, baris tabel, kartu, tab), WAJIB menyajikan efek hover unik yang tetap mematuhi tema: elevasi latar belakang halus (`hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80`), penegasan border (`hover:border-zinc-400 dark:hover:border-zinc-600`), atau focus ring aksen (`hover:ring-1 hover:ring-emerald-500/60`). Dilarang keras efek hover yang membuat teks kehilangan kontras atau bertabrakan warna.\n";
        $rules .= "- **Consistent Contrast**: Gunakan pasangan class Tailwind secara disiplin (`bg-white dark:bg-zinc-950`, `text-zinc-900 dark:text-zinc-100`, `border-zinc-200 dark:border-zinc-800`, `hover:bg-zinc-100 dark:hover:bg-zinc-900`). Dilarang keras membuat komponen yang hanya terlihat bagus di salah satu mode.\n\n";

        $rules .= "### D. High-Contrast Text Legibility & Strict Ban on Low-Contrast \"AI Slop\" (Anti-Tenggelam)\n";
        $rules .= "- **Wajib Kontras Tinggi & Anti-Tenggelam**: Seluruh font teks, tipografi, badge, tag, dan label WAJIB memiliki rasio kontras tinggi (WCAG AAA/AA) yang tajam terhadap latar belakangnya. Dilarang keras membuat teks atau tombol yang warnanya pudar atau tenggelam (\"AI slop\") sehingga sulit dibaca pengguna.\n";
        $rules .= "- **Latar Terang (Light Backgrounds)**: Pada background putih atau abu-abu terang, DILARANG KERAS menggunakan warna pastel pudar seperti `text-emerald-500` atau `text-amber-500` karena tidak terbaca (<3:1). WAJIB gunakan warna pekat: `text-zinc-950`, `text-emerald-950`, `text-amber-950`, `text-cyan-950` dengan border tegas.\n";
        $rules .= "- **Tombol Berwarna Solid**: Di dalam tombol berwarna solid (seperti `bg-emerald-600 text-black`), badge di dalamnya WAJIB menggunakan kontras solid ekstrem seperti `bg-black text-white font-black px-2 py-0.5`. DILARANG KERAS menggunakan `bg-black/20 text-black` yang membuat teks hitam bertabrakan dengan hijau tua transparan.\n";
        $rules .= "- **Dark Mode Legibility**: Pada dark mode, gunakan teks terang kontras tinggi (`dark:text-emerald-200`, `dark:text-amber-200`, `dark:text-cyan-200`) di atas background gelap (`dark:bg-emerald-950 dark:border-emerald-700`).\n\n";

        $rules .= "### E. Zero Native Browser Dialogs (No \"Modal Kampungan\")\n";
        $rules .= "- NEVER use native browser dialogs (`window.alert()`, `confirm()`, `prompt()`). They look cheap/unprofessional (\"modal kampungan\") and freeze the UI thread.\n";
        $rules .= "- ALWAYS use modern floating toast notifications: `window.showToast({ type: 'success'|'error'|'warning'|'info', title: '...', message: '...' })` or curated Tailwind + Alpine dialogs with backdrop-blur.\n\n";

        $rules .= "### F. Thousand Separators on Numbers & Currencies\n";
        $rules .= "- Setiap kali membuat input atau tampilan nominal angka atau currency yang mencapai ribuan (>= 1.000), WAJIB memformat pemisah ribuan (titik `.` untuk ID / koma `,` untuk EN) di seluruh komponen UI/UX.\n\n";

        $rules .= "### G. Country Zone Dialing Code Standard\n";
        $rules .= "- Setiap membuat input nomor telepon atau WhatsApp, WAJIB menggunakan country code dropdown yang bisa di-search (`config/country_zones.php`, e.g. +62, +65, +1, +44, +81) untuk menegakkan standar E.164 internasional dan mengeliminasi nomor telepon tidak valid.\n\n";

        $rules .= "### H. Alpine.js HTML Entity Encoding & Script Extraction Standard\n";
        $rules .= "- Ketika menulis inline JavaScript di dalam atribut Alpine.js (`x-data=\"...\"`, `x-init=\"...\"`, `@click=\"...\"`), NEVER use raw double quotes (`\"`) or single quotes (`'`) inside string literals or JSON outputs (`@json()`).\n";
        $rules .= "- MANDATORY HTML ENTITY ENCODING:\n";
        $rules .= "  - Tanda kutip ganda (\") → `&quot;`\n";
        $rules .= "  - Tanda kutip tunggal (') → `&apos;`\n";
        $rules .= "- Untuk komponen Alpine yang kompleks (>3 properti, nested objects, lifecycle hooks, atau method panjang), SELALU ekstrak komponen ke dalam blok `<script>` terpisah (`function componentName() { return { ... }; }`) dan hubungkan dengan `<tag x-data=\"componentName()\">`.\n\n";
        $rules .= "---\n\n";

        // 4. Actors
        $rules .= "## 4. SYSTEM ACTORS & RBAC ROLES\n\n";
        if (!empty($actors)) {
            foreach ($actors as $actor) {
                $name = $actor['role_name'] ?? ($actor['name'] ?? 'User');
                $desc = $actor['responsibilities'] ?? ($actor['desc'] ?? '-');
                $rules .= "- **{$name}**: {$desc}\n";
            }
        } else {
            $rules .= "- **Superadmin**: Full administrative control, configurations, and system monitoring.\n";
            $rules .= "- **Operator / Staff**: Operational data input, processing, and review.\n";
            $rules .= "- **Client / Customer**: Portal access, transaction submission, and report inspection.\n";
        }
        $rules .= "\n---\n\n";

        // 5. ERD Schema
        $rules .= "## 5. DATABASE ERD SCHEMA & ENTITIES\n\n";
        if (!empty($erd)) {
            foreach ($erd as $table) {
                $tname = $table['table_name'] ?? 'entity';
                $tdesc = $table['description'] ?? '';
                $rules .= "### Table: `{$tname}`" . ($tdesc ? " ({$tdesc})" : "") . "\n";
                $rules .= "```sql\n";
                $rules .= "-- Primary key: id (ULID VARCHAR 26)\n";
                if (!empty($table['columns'])) {
                    foreach ($table['columns'] as $col) {
                        $cname = $col['name'] ?? 'col';
                        $ctype = $col['type'] ?? 'VARCHAR(255)';
                        $cnote = !empty($col['note']) ? " -- {$col['note']}" : '';
                        $rules .= "{$cname} {$ctype}{$cnote}\n";
                    }
                }
                $rules .= "```\n\n";
            }
        } else {
            $rules .= "- All domain tables must follow standard ULID naming and migrations.\n\n";
        }
        $rules .= "---\n\n";

        // 6. Features
        $rules .= "## 6. MVP FEATURES & SPRINT DELIVERABLES\n\n";
        if (!empty($mvpFeatures)) {
            foreach ($mvpFeatures as $idx => $f) {
                $num = $idx + 1;
                $ftitle = $f['title'] ?? 'Feature';
                $fdesc = $f['desc'] ?? '';
                $rules .= "### Feature {$num}: {$ftitle}\n";
                if ($fdesc) {
                    $rules .= "- **Description**: {$fdesc}\n";
                }
                if (!empty($f['user_story'])) {
                    $rules .= "- **User Story**: {$f['user_story']}\n";
                }
                if (!empty($f['backend']['model_and_migration'])) {
                    $rules .= "- **Backend**: {$f['backend']['model_and_migration']}\n";
                }
                if (!empty($f['frontend']['design_tokens'])) {
                    $rules .= "- **Frontend**: {$f['frontend']['design_tokens']}\n";
                }
                $rules .= "\n";
            }
        } else {
            $fiturLines = array_filter(explode("\n", (string) $blueprint->fitur_wajib));
            foreach ($fiturLines as $idx => $line) {
                $num = $idx + 1;
                $rules .= "- **MVP {$num}**: " . trim($line) . "\n";
            }
            $rules .= "\n";
        }
        $rules .= "---\n\n";

        // 7. Workflow
        $rules .= "## 7. AI CODE-GEN EXECUTION WORKFLOW\n";
        $rules .= "1. **Vertical Slice Implementation**: Always implement one module end-to-end (Migration -> Model -> Policy/Form -> UI/View -> Test) before proceeding to the next.\n";
        $rules .= "2. **Minimalist & Surgical Diffs**: Never rewrite whole files unnecessarily. Keep diffs precise and token-efficient.\n";
        $rules .= "3. **Automated Verification**: Ensure all code syntax is strictly valid for PHP 8.5+ and runs PHPUnit / Pest tests cleanly.\n";
        $rules .= "4. **Git Discipline**: Every completed slice should be verified against regressions before concluding.\n\n";

        $customRules = $prd['custom_ai_agent_rules'] ?? null;
        if (!empty($customRules)) {
            $rules .= "---\n\n";
            $rules .= "## 8. CUSTOM AI AGENT DIRECTIVES (RETAIL EXTENSION)\n\n";
            $rules .= is_string($customRules) ? $customRules : json_encode($customRules, JSON_PRETTY_PRINT);
            $rules .= "\n\n";
        }

        return $rules;
    }


    /**
     * Synthesize high-impact AI strategic architecture guidance using Flagship PRD models
     * (DeepSeek-R1 / Claude 3.7 Sonnet / Gemini 2.5 Pro / GPT-4o / Grok) with automatic failover.
     */
    public static function synthesizeAiPrdTelemetry(
        VisionBlueprint $blueprint,
        string $businessName,
        string $masalah,
        string $tujuan,
        ?string $preferredAiProvider = null
    ): array {
        $prompt = "You are a Principal Systems Solutions Architect writing an executive summary for an Ultimate PRD. Project: '{$businessName}'. Core Problem: '{$masalah}'. Success Metrics: '{$tujuan}'. Provide 2-3 concise sentences of deep strategic technical guidance on scalability, concurrency, and rapid deployment for this domain.";
        $systemInstruction = "You are the world's leading Enterprise Systems Architect specializing in Laravel 13, Filament v5, PostgreSQL ULID, and high-concurrency systems.";

        try {
            $aiRes = \App\Services\Ai\MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'prd', $preferredAiProvider);

            return [
                'provider' => $aiRes['provider'] ?? 'deepseek',
                'provider_name' => $aiRes['provider_name'] ?? 'DeepSeek SOTA Reasoning',
                'model' => $aiRes['model'] ?? 'deepseek-reasoner',
                'fallback_occurred' => $aiRes['fallback_occurred'] ?? false,
                'failed_attempts' => $aiRes['failed_attempts'] ?? [],
                'notification' => $aiRes['notification'] ?? null,
                'strategic_guidance' => $aiRes['text'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'provider' => 'deterministic_heuristic',
                'provider_name' => 'Neriah Pro Deterministic Engine',
                'model' => 'Standard Engineering Rules',
                'fallback_occurred' => false,
                'failed_attempts' => [],
                'notification' => null,
                'strategic_guidance' => 'Sistem dirancang dengan arsitektur Modern Monolith berkinerja tinggi, database PostgreSQL Strict ULID, dan keyset pagination O(1) untuk menjamin latensi rendah pada beban tinggi.',
            ];
        }
    }

}
