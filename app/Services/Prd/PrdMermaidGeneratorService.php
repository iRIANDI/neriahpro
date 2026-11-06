<?php

namespace App\Services\Prd;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrdMermaidGeneratorService
{
    /**
     * Wrap text dynamically into multi-line safe labels for Mermaid nodes.
     * Prevents truncation artifacts, strips troublesome symbols, and wraps at clean word boundaries.
     */
    public static function wrapMermaidLabel(string $text, int $width = 28, int $maxTotalLength = 180): string
    {
        $clean = trim(preg_replace('/["\r\n]+/', ' ', $text));
        $clean = str_replace(['"', '`', '$', '[', ']', '{', '}'], ["'", '', '', '', '', '', ''], $clean);

        // Auto-heal chopped words if present
        $clean = preg_replace('/\btransfer\s+ban(?:\.\.\.|\b)/i', 'Transfer Bandara / Wisata', $clean);
        $clean = preg_replace('/\brefund\s+ot(?:om)?(?:\.\.\.|\b)/i', 'Refund Otomatis', $clean);

        // Strip all dots/ellipses (both inline and trailing)
        $clean = preg_replace('/\s*\.{2,}\s*/', ' ', $clean);
        $clean = preg_replace('/\s+dll\.?$/i', '', $clean);
        $clean = preg_replace('/\s+etc\.?$/i', '', $clean);
        $clean = rtrim($clean, " .,;:!?-");

        // If sentence contains lengthy parenthetical lists, condense them cleanly
        $clean = preg_replace_callback('/\(([^)]{25,})\)/', function($m) {
            $inside = $m[1];
            $parts = explode(',', $inside);
            if (count($parts) > 1) {
                return '(' . trim($parts[0]) . ' dll)';
            }
            return '(' . trim($inside) . ')';
        }, $clean);

        // If text still exceeds maxTotalLength, truncate gracefully at word boundary without slicing words in half and WITHOUT trailing dots
        if (mb_strlen($clean) > $maxTotalLength) {
            $truncated = mb_substr($clean, 0, $maxTotalLength);
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > (int)($maxTotalLength * 0.7)) {
                $clean = mb_substr($truncated, 0, $lastSpace);
            } else {
                $clean = $truncated;
            }
            $clean = rtrim($clean, " .,;:!?-");
        }

        if (mb_strlen($clean) <= $width) {
            return $clean;
        }

        // Wrap lines cleanly at space/word boundaries (cut_long_words = false)
        return wordwrap($clean, $width, '<br/>', false);
    }


    /**
     * Resolve workflow mermaid diagram with dynamic wrap validation.
     */
    public static function resolveWorkflowMermaid(?string $existingMermaid, array $workflowStages): string
    {
        if (empty($existingMermaid)) {
            return self::generateWorkflowMermaid($workflowStages);
        }
        // If the existing mermaid has truncated words with "..." or overly long lines (> 60 chars without <br/>) or missing <br/>, re-generate dynamically
        if (str_contains($existingMermaid, '...') || preg_match('/\["[^"]{60,}"\]/', $existingMermaid) || !str_contains($existingMermaid, '<br/>')) {
            return self::generateWorkflowMermaid($workflowStages);
        }
        return $existingMermaid;
    }


    /**
     * Generate Mermaid syntax for Workflow User Journey (Flowchart TD) with dynamic wrapping and phase grouping.
     */
    public static function generateWorkflowMermaid(array $workflowStages): string
    {
        $count = count($workflowStages);
        if ($count === 0) {
            return "flowchart TD\n    S1[\"<b>Step 1: Intake Proyek</b><br/><small>Aktor: Klien</small>\"]";
        }

        $code = "flowchart TD\n";
        $wIdx = 1;

        // Dynamic multi-stage grouping if > 3 steps to wrap gracefully and prevent horizontal stretching
        if ($count > 3) {
            $chunkSize = (int) ceil($count / 3);
            $phaseNames = [
                1 => '1. INTAKE & RESERVASI (Tahap Awal)',
                2 => '2. OPERASIONAL & EKSEKUSI (Tahap Lapangan)',
                3 => '3. SETTLEMENT & REVIEW (Tahap Penyelesaian)',
            ];

            $chunks = array_chunk($workflowStages, $chunkSize);
            $lastNodeInPrevPhase = null;

            foreach ($chunks as $cIdx => $chunk) {
                $pId = "PHASE_" . ($cIdx + 1);
                $pTitle = $phaseNames[$cIdx + 1] ?? ('Fase ' . ($cIdx + 1));
                $code .= "    subgraph {$pId} [\"{$pTitle}\"]\n";
                $code .= "        direction TB\n";

                $prevInChunk = null;
                foreach ($chunk as $w) {
                    $nodeId = "S" . $wIdx;
                    $actionClean = $w['action'] ?? ('Step ' . $wIdx);
                    $actorClean = $w['actor'] ?? 'Pengguna';
                    $wrappedAction = self::wrapMermaidLabel($actionClean, 28, 180);
                    $wrappedActor = self::wrapMermaidLabel($actorClean, 22, 60);

                    $code .= "        {$nodeId}[\"<b>Step {$wIdx}: {$wrappedAction}</b><br/><small>Aktor: {$wrappedActor}</small>\"]\n";
                    if ($prevInChunk) {
                        $code .= "        {$prevInChunk} --> {$nodeId}\n";
                    }
                    $prevInChunk = $nodeId;
                    $wIdx++;
                }
                $code .= "    end\n\n";

                if ($lastNodeInPrevPhase && !empty($chunk)) {
                    $firstInThisChunk = "S" . ($wIdx - count($chunk));
                    $code .= "    {$lastNodeInPrevPhase} ==>|Lanjut ke Tahap Berikutnya| {$firstInThisChunk}\n\n";
                }
                $lastNodeInPrevPhase = $prevInChunk;
            }
        } else {
            $prevNode = null;
            foreach ($workflowStages as $w) {
                $nodeId = "S" . $wIdx;
                $actionClean = $w['action'] ?? ('Step ' . $wIdx);
                $actorClean = $w['actor'] ?? 'Pengguna';
                $wrappedAction = self::wrapMermaidLabel($actionClean, 28, 180);
                $wrappedActor = self::wrapMermaidLabel($actorClean, 22, 60);

                $code .= "    {$nodeId}[\"<b>Step {$wIdx}: {$wrappedAction}</b><br/><small>Aktor: {$wrappedActor}</small>\"]\n";
                if ($prevNode) {
                    $code .= "    {$prevNode} -->|Lanjut| {$nodeId}\n";
                }
                $prevNode = $nodeId;
                $wIdx++;
            }
        }

        return trim($code);
    }


    /**
     * Generate Mermaid syntax for Database ERD Schema (erDiagram).
     */
    public static function generateErdMermaid(array $erdTables): string
    {
        // 1. Build lookup dictionary for all tables in this ERD
        $tableLookup = [];
        foreach ($erdTables as $t) {
            $raw = strtolower(trim((string)($t['name'] ?? '')));
            $clean = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $raw));
            if (!empty($clean)) {
                $tableLookup[$raw] = $clean;
                $tableLookup[rtrim($raw, 's')] = $clean;
            }
        }

        $code = "erDiagram\n";
        $emittedRelations = [];

        // 2. Dynamic Relational Detection: Scan every column for Foreign Keys
        foreach ($erdTables as $table) {
            $childClean = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? 'TABLE'));
            foreach ($table['columns'] ?? [] as $col) {
                $cname = strtolower(trim((string)($col['name'] ?? '')));
                $rawType = strtolower((string)($col['type'] ?? ''));
                $isFk = str_ends_with($cname, '_id') || str_contains($rawType, 'foreign');

                if ($isFk && $cname !== 'id') {
                    $targetClean = null;
                    if (in_array($cname, ['user_id', 'customer_id', 'driver_id', 'pic_id', 'client_id', 'admin_id'])) {
                        $targetClean = $tableLookup['users'] ?? 'USERS';
                    } else {
                        $base = preg_replace('/_id$/', '', $cname);
                        if (isset($tableLookup[$base])) {
                            $targetClean = $tableLookup[$base];
                        } elseif (isset($tableLookup[$base . 's'])) {
                            $targetClean = $tableLookup[$base . 's'];
                        } else {
                            foreach ($tableLookup as $k => $cln) {
                                if (str_contains($cln, strtoupper($base)) || str_contains(strtoupper($base), $cln)) {
                                    $targetClean = $cln;
                                    break;
                                }
                            }
                        }
                    }

                    if ($targetClean && $targetClean !== $childClean) {
                        $relKey = "{$targetClean}__TO__{$childClean}";
                        if (!isset($emittedRelations[$relKey])) {
                            $emittedRelations[$relKey] = true;
                            $code .= "    {$targetClean} ||--o{ {$childClean} : \"references/has\"\n";
                        }
                    }
                }
            }
        }

        // 3. Fallback core relationships if not already linked
        if (isset($tableLookup['users'])) {
            $userClean = $tableLookup['users'];
            if (isset($tableLookup['activity_logs']) && !isset($emittedRelations["{$userClean}__TO__ACTIVITY_LOGS"])) {
                $code .= "    {$userClean} ||--o{ ACTIVITY_LOGS : \"triggers\"\n";
                $emittedRelations["{$userClean}__TO__ACTIVITY_LOGS"] = true;
            }
            if (isset($tableLookup['system_notifications']) && !isset($emittedRelations["{$userClean}__TO__SYSTEM_NOTIFICATIONS"])) {
                $code .= "    {$userClean} ||--o{ SYSTEM_NOTIFICATIONS : \"receives\"\n";
                $emittedRelations["{$userClean}__TO__SYSTEM_NOTIFICATIONS"] = true;
            }

            foreach ($erdTables as $t) {
                $tname = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $t['name'] ?? ''));
                if ($tname !== $userClean && $tname !== 'ACTIVITY_LOGS' && $tname !== 'SYSTEM_NOTIFICATIONS') {
                    $hasRel = false;
                    foreach ($emittedRelations as $k => $v) {
                        if (str_ends_with($k, "__{$tname}") || str_starts_with($k, "{$tname}__")) {
                            $hasRel = true;
                            break;
                        }
                    }
                    if (!$hasRel) {
                        $emittedRelations["{$userClean}__TO__{$tname}"] = true;
                        $code .= "    {$userClean} ||--o{ {$tname} : \"manages\"\n";
                    }
                }
            }
        }
        $code .= "\n";

        foreach ($erdTables as $table) {
            $tname = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? 'TABLE'));
            $code .= "    {$tname} {\n";
            foreach ($table['columns'] ?? [] as $col) {
                $cname = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $col['name'] ?? 'col'));
                $rawType = strtolower($col['type'] ?? 'string');
                $type = 'string';
                if (str_contains($rawType, 'ulid')) $type = 'string';
                elseif (str_contains($rawType, 'int')) $type = 'int';
                elseif (str_contains($rawType, 'bool')) $type = 'boolean';
                elseif (str_contains($rawType, 'json')) $type = 'jsonb';
                elseif (str_contains($rawType, 'time') || str_contains($rawType, 'date')) $type = 'timestamp';

                $key = '';
                if (($col['index'] ?? '') === 'PRIMARY' || $cname === 'id') $key = 'PK';
                elseif (str_contains(strtolower($col['type'] ?? ''), 'foreign') || str_ends_with($cname, '_id')) $key = 'FK';
                elseif (($col['index'] ?? '') === 'UNIQUE') $key = 'UK';

                $code .= "        {$type} {$cname}" . ($key ? " {$key}" : '') . "\n";
            }
            $code .= "    }\n";
        }

        return trim($code);
    }


    /**
     * Generate Mermaid syntax for Feature & Entity Dependency Graph (Flowchart LR).
     */
    public static function generateFeatureDependencyMermaid(array $actorItems, array $mvpItems, array $erdTables): string
    {
        $code = "flowchart TB\n";
        $code .= "    subgraph ACTORS [\"👥 Aktor Sistem (Hak Akses RBAC)\"]\n";
        $code .= "        direction LR\n";
        $actCount = min(count($actorItems), 3);
        if ($actCount === 0) $actCount = 1;
        for ($idx = 0; $idx < $actCount; $idx++) {
            $aId = "A" . ($idx + 1);
            $actor = $actorItems[$idx] ?? [];
            $aName = self::wrapMermaidLabel($actor['name'] ?? ('Actor ' . ($idx + 1)), 24);
            $code .= "        {$aId}[\"{$aName}\"]\n";
        }
        $code .= "    end\n\n";

        $code .= "    subgraph FEATURES [\"⚡ Modul Fitur MVP (Fase 1)\"]\n";
        $code .= "        direction TB\n";
        $featCount = min(count($mvpItems), 4);
        if ($featCount === 0) $featCount = 1;
        for ($idx = 0; $idx < $featCount; $idx++) {
            $fId = "F" . ($idx + 1);
            $f = $mvpItems[$idx] ?? [];
            $fTitle = self::wrapMermaidLabel($f['title'] ?? ('Feature ' . ($idx + 1)), 28);
            $code .= "        {$fId}[\"{$fTitle}\"]\n";
        }
        $code .= "    end\n\n";

        $code .= "    subgraph DB [\"🗄️ Basis Data (PostgreSQL Strict ULID)\"]\n";
        $code .= "        direction LR\n";
        $dbCount = min(count($erdTables), 4);
        if ($dbCount === 0) $dbCount = 1;
        for ($idx = 0; $idx < $dbCount; $idx++) {
            $tId = "T" . ($idx + 1);
            $t = $erdTables[$idx] ?? [];
            $tName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $t['name'] ?? ('table_' . ($idx + 1))));
            $code .= "        {$tId}[(\"{$tName}\")]\n";
        }
        $code .= "    end\n\n";

        // Relasi Aktor -> Fitur
        $code .= "    ACTORS ==>|Otorisasi Akses| FEATURES\n";
        $code .= "    A1 --> F1\n";
        if ($actCount > 1 && $featCount > 1) {
            $code .= "    A2 --> F2\n";
        }
        if ($featCount > 2) {
            $code .= "    A1 --> F3\n";
        }
        if ($actCount > 2 && $featCount > 1) {
            $code .= "    A3 --> F2\n";
        }
        if ($featCount > 3) {
            $code .= ($actCount > 2 ? "    A3 --> F4\n" : "    A1 --> F4\n");
        }

        // Relasi Fitur -> Database
        $code .= "    FEATURES ==>|I/O Mutasi Data| DB\n";
        $code .= "    F1 --> T1\n";
        if ($featCount > 1 && $dbCount > 1) {
            $code .= "    F2 --> T2\n";
        }
        if ($featCount > 2 && $dbCount > 1) {
            $code .= "    F3 --> T2\n";
        }
        if ($featCount > 2 && $dbCount > 2) {
            $code .= "    F3 --> T3\n";
        }
        if ($featCount > 3 && $dbCount > 3) {
            $code .= "    F4 --> T4\n";
        }

        return trim($code);
    }


    /**
     * Generate Mermaid syntax for Sprint Roadmap & Execution Timeline (Gantt Chart).
     */
    public static function generateSprintGanttMermaid(VisionBlueprint $blueprint, string $targetWaktu): string
    {
        $projectName = preg_replace('/["\r\n]+/', '', $blueprint->nama_bisnis ?: 'Proyek');
        $startDate = now()->format('Y-m-d');
        $code = "gantt\n";
        $code .= "    title Roadmap Eksekusi & Sprint Delivery: {$projectName}\n";
        $code .= "    dateFormat YYYY-MM-DD\n";
        $code .= "    axisFormat %d %b\n\n";
        $code .= "    section Fase 0: Blueprint & Skema\n";
        $code .= "    Discovery PRD & Arsitektur Approval :done, p0_1, {$startDate}, 3d\n";
        $code .= "    Skema Basis Data ULID & RBAC Matrix  :done, p0_2, after p0_1, 2d\n\n";
        $code .= "    section Fase 1: DB & Admin Panel\n";
        $code .= "    PostgreSQL Migration & Model ULID   :active, p1_1, after p0_2, 3d\n";
        $code .= "    Filament v5 CRUD & RBAC Shield      :p1_2, after p1_1, 4d\n\n";
        $code .= "    section Fase 2: Frontend & Forms\n";
        $code .= "    Desain Sistem Anti-AI-Slop & Layout :p2_1, after p1_2, 3d\n";
        $code .= "    Formulir Interaktif & State Shimmer :p2_2, after p2_1, 4d\n\n";
        $code .= "    section Fase 3: Integrasi & Queue\n";
        $code .= "    API Contracts & Gateway Eksternal   :p3_1, after p2_2, 4d\n";
        $code .= "    Background Jobs & Notifikasi Alert  :p3_2, after p3_1, 3d\n\n";
        $code .= "    section Fase 4: UAT & Production\n";
        $code .= "    Automated Quality Gate & Testing   :crit, p4_1, after p3_2, 3d\n";
        $code .= "    Peluncuran Server Produksi (Go-Live):milestone, p4_2, after p4_1, 1d\n";

        return trim($code);
    }


    /**
     * Generate Mermaid syntax for Hosting, Security, & Infrastructure Topology (flowchart TB).
     */
    public static function generateInfrastructureMermaid(VisionBlueprint $blueprint, array $extraContext): string
    {
        $platform = preg_replace('/["\r\n]+/', '', $extraContext['target_platform'] ?? 'Modern Web & PWA');
        $hosting = preg_replace('/["\r\n]+/', '', $extraContext['preferensi_hosting'] ?? 'Managed Dedicated Cloud VPS');
        $migration = preg_replace('/["\r\n]+/', '', $extraContext['migrasi_data'] ?? 'Database Baru Bersih');
        $warranty = preg_replace('/["\r\n]+/', '', $extraContext['garansi_sla'] ?? '30 Hari Garansi Bug + Repo Git');
        $payment = preg_replace('/["\r\n]+/', '', $extraContext['termin_pembayaran'] ?? 'Termin Standar 50/50');

        $discoveryData = is_array($blueprint->discovery_data) ? $blueprint->discovery_data : (json_decode($blueprint->discovery_data ?? '[]', true) ?: []);
        $domainDetected = $discoveryData['meta']['detected_domain'] ?? '';
        $corpusText = strtolower(($blueprint->nama_bisnis ?? '') . ' ' . ($blueprint->deskripsi_ide ?? '') . ' ' . ($blueprint->masalah_solusi ?? '') . ' ' . ($extraContext['target_platform'] ?? ''));

        $isGame3D = $domainDetected === 'game_3d_simulation' 
            || str_contains($corpusText, 'pesawat') 
            || str_contains($corpusText, 'game') 
            || str_contains($corpusText, 'three.js') 
            || str_contains($corpusText, 'simulator');

        $isApiGateway = $domainDetected === 'api_gateway_messaging' 
            || str_contains($corpusText, 'openwa') 
            || str_contains($corpusText, 'baileys') 
            || (str_contains($corpusText, 'whatsapp') && (str_contains($corpusText, 'gateway') || str_contains($corpusText, 'api')));

        $isDecoupledRequested = $domainDetected === 'decoupled_saas'
            || str_contains($corpusText, 'decoupled')
            || str_contains($corpusText, 'terpisah')
            || str_contains($corpusText, 'frontend terpisah')
            || (str_contains($corpusText, 'next.js') && str_contains($corpusText, 'api'));

        $isBioinformatics = $domainDetected === 'bioinformatics_genomics'
            || str_contains($corpusText, 'crispr')
            || str_contains($corpusText, 'genom')
            || str_contains($corpusText, 'dna sequencing')
            || str_contains($corpusText, 'fasta')
            || str_contains($corpusText, 'bioinformatika');

        $isFintechLedger = $domainDetected === 'fintech_ledger_banking'
            || str_contains($corpusText, 'double-entry')
            || str_contains($corpusText, 'ledger')
            || str_contains($corpusText, 'core banking')
            || str_contains($corpusText, 'buku besar')
            || str_contains($corpusText, 'pembukuan berpasangan');

        $isIotRobotics = $domainDetected === 'iot_telemetry_robotics'
            || str_contains($corpusText, 'mqtt')
            || str_contains($corpusText, 'telemetri')
            || str_contains($corpusText, 'telemetry')
            || str_contains($corpusText, 'robotik')
            || str_contains($corpusText, 'drone')
            || str_contains($corpusText, 'emqx');

        $isAiAgentRag = $domainDetected === 'ai_agent_rag_pipeline'
            || str_contains($corpusText, 'rag')
            || str_contains($corpusText, 'vector database')
            || str_contains($corpusText, 'langgraph')
            || str_contains($corpusText, 'pgvector')
            || str_contains($corpusText, 'agentic');

        $isCybersecurity = $domainDetected === 'cybersecurity_siem_zerotrust'
            || str_contains($corpusText, 'siem')
            || str_contains($corpusText, 'zero trust')
            || str_contains($corpusText, 'soc')
            || str_contains($corpusText, 'honeypot')
            || str_contains($corpusText, 'threat hunting');

        if ($isGame3D) {
            $code = "flowchart TB\n";
            $code .= "    subgraph Clients[\"1. Client Runtime & 3D Rendering (60 FPS)\"]\n";
            $code .= "        Canvas3D[\"Three.js WebGL / WebGPU Viewport<br/><small>GLTF Models, PBR Shaders, HUD Telemetry</small>\"]\n";
            $code .= "        PhysicsLoop[\"Rapier / Cannon Physics Engine<br/><small>6-DOF Flight Dynamics & Collision Mesh</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph EdgeAssets[\"2. Global Asset CDN & Object Storage\"]\n";
            $code .= "        CloudflareCDN[\"Cloudflare Edge CDN<br/><small>Sub-20ms 3D Asset Cache & Brotli</small>\"]\n";
            $code .= "        R2Bucket[(\"Cloudflare R2 Object Storage<br/><small>GLB Models, Textures, Audio ($0 Egress)</small>\")]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph GameBackend[\"3. Dedicated Game & Telemetry Server\"]\n";
            $code .= "        WsServer[\"High-Throughput WebSocket Server<br/><small>State Synchronization, Anti-Cheat, Room Lobby</small>\"]\n";
            $code .= "        ScoreEngine[\"Flight Score & Telemetry Validator<br/><small>Rate Limiter & Input Verification</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph DataStorage[\"4. Persistence & Session Broker\"]\n";
            $code .= "        PgSql[(\"PostgreSQL 16 Engine<br/><small>Strict ULID PK, Pilot Profiles, Leaderboards O(1)</small>\")]\n";
            $code .= "        Redis[(\"Redis In-Memory Store<br/><small>Active Sessions, Heartbeat & Lobby Cache</small>\")]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph Handover[\"5. Serah Terima & Garansi\"]\n";
            $code .= "        Repo[\"Private GitHub Repo<br/><small>Three.js Source, Assets Pipeline, Docker</small>\"]\n";
            $code .= "        SlaNote[\"Garansi Bug: {$warranty}\"]\n";
            $code .= "    end\n\n";
            $code .= "    Clients --> EdgeAssets\n";
            $code .= "    Clients <--> GameBackend\n";
            $code .= "    GameBackend --> DataStorage\n";
            $code .= "    GameBackend -.-> Handover\n";
            return trim($code);
        }

        if ($isApiGateway) {
            $code = "flowchart TB\n";
            $code .= "    subgraph Consumers[\"1. Client Apps & API Subscribers\"]\n";
            $code .= "        ApiClients[\"External CRM, ERP, E-Commerce<br/><small>RESTful API & OpenAPI 3.1 Contract</small>\"]\n";
            $code .= "        WebhookReceivers[\"Client Webhook Endpoints<br/><small>HMAC-SHA256 Signed Event Listeners</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph IngressSecurity[\"2. Ingress & Cyber Defense\"]\n";
            $code .= "        Nginx[\"Nginx Reverse Proxy & HTTP/2<br/><small>SSL Termination, Token-Bucket Rate Limiter</small>\"]\n";
            $code .= "        Waf[\"Cyber Shield & Honeypot<br/><small>IP Whitelisting & Malicious Payload Blocker</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph GatewayCore[\"3. {$hosting}\"]\n";
            $code .= "        NodeEngine[\"Node.js High-Concurrency Engine<br/><small>Session Controller & API Handlers</small>\"]\n";
            $code .= "        WaWorkers[\"Chromium Sandbox Worker Pool<br/><small>OpenWA / Baileys Isolated Socket Sessions</small>\"]\n";
            $code .= "        BullQueue[\"BullMQ Distributed Job Queue<br/><small>Anti-Ban Rate Jitter & Auto Retries</small>\"]\n";
            $code .= "        WebhookDispatcher[\"HMAC Webhook Dispatcher<br/><small>Realtime Delivery Status & Incoming Inbound</small>\"]\n";
            $code .= "        AdminOps[\"Filament v5 Operations Cockpit<br/><small>Tenant Quotas, QR Codes, Realtime Telemetry</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph DataStorage[\"4. Persistence & Event Broker\"]\n";
            $code .= "        PgSql[(\"PostgreSQL 16 Engine<br/><small>Strict ULID PK, Tenants, Logs, Keyset O(1)</small>\")]\n";
            $code .= "        Redis[(\"Redis 7+ Cluster<br/><small>BullMQ State, Session Tokens & Rate Limits</small>\")]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph Handover[\"5. Serah Terima & SLA\"]\n";
            $code .= "        Repo[\"Private GitHub Repo<br/><small>Docker Compose, Node Daemon, Filament Admin</small>\"]\n";
            $code .= "        SlaNote[\"Garansi Bug: {$warranty}\"]\n";
            $code .= "    end\n\n";
            $code .= "    ApiClients --> IngressSecurity\n";
            $code .= "    IngressSecurity --> NodeEngine\n";
            $code .= "    NodeEngine --> BullQueue\n";
            $code .= "    BullQueue --> WaWorkers\n";
            $code .= "    WaWorkers --> WebhookDispatcher\n";
            $code .= "    WebhookDispatcher --> WebhookReceivers\n";
            $code .= "    NodeEngine --> DataStorage\n";
            $code .= "    AdminOps --> DataStorage\n";
            $code .= "    GatewayCore -.-> Handover\n";
            return trim($code);
        }

        if ($isDecoupledRequested) {
            $code = "flowchart TB\n";
            $code .= "    subgraph EdgeFrontend[\"1. Edge Frontend & Consumer Tier\"]\n";
            $code .= "        WebClient[\"Next.js 15 App Router / Nuxt 3<br/><small>Cloudflare Pages Edge SSR, Sub-20ms TTFB</small>\"]\n";
            $code .= "        MobileClient[\"Flutter 3.x / React Native App<br/><small>Encrypted SQLite Offline-First & Delta Sync</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph ApiIngress[\"2. Ingress & Security Tier\"]\n";
            $code .= "        ApiGateway[\"Nginx Reverse Proxy & HTTP/2<br/><small>SSL Termination, Token Bucket Rate Limiting</small>\"]\n";
            $code .= "        OpenApiSpec[\"OpenAPI 3.1 & Scalar Engine<br/><small>Strict Contract Validation & Zod Schema</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph BackendCore[\"3. Headless API & Ops ({$hosting})\"]\n";
            $code .= "        HeadlessApi[\"Headless Laravel 13 REST Engine<br/><small>Strict ACID Transactions, FormRequests & DTOs</small>\"]\n";
            $code .= "        FilamentOps[\"Filament v5 Enterprise Backoffice<br/><small>Internal Admin Operations & RBAC Shield</small>\"]\n";
            $code .= "        QueueWorkers[\"Background Queue Workers<br/><small>Async Tasks, Notifications, Webhooks</small>\"]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph Persistence[\"4. Enterprise Storage & Cache Tier\"]\n";
            $code .= "        PgSql[(\"PostgreSQL 16 Engine<br/><small>Strict ULID PK & Keyset Cursor O(1)</small>\")]\n";
            $code .= "        Redis[(\"Redis 7+ Cluster<br/><small>Token Blacklist, Cache & Queue Broker</small>\")]\n";
            $code .= "    end\n\n";
            $code .= "    subgraph Handover[\"5. Serah Terima & Garansi\"]\n";
            $code .= "        Repo[\"Private GitHub Repositories<br/><small>Frontend Web Repo + Headless Backend Repo</small>\"]\n";
            $code .= "        SlaNote[\"Garansi Bug: {$warranty}\"]\n";
            $code .= "    end\n\n";
            $code .= "    EdgeFrontend --> ApiIngress\n";
            $code .= "    ApiIngress --> HeadlessApi\n";
            $code .= "    HeadlessApi --> Persistence\n";
            $code .= "    FilamentOps --> Persistence\n";
            $code .= "    BackendCore -.-> Handover\n";
            return trim($code);
        }

        $code = "flowchart TB\n";
        $code .= "    subgraph Clients[\"1. Target Platform & Aksesibilitas Perangkat\"]\n";
        $code .= "        C1[\"{$platform}<br/><small>Aksesibilitas Browser Desktop, Tablet & PWA Mobile</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph EdgeLayer[\"2. Keamanan Jaringan & Reverse Proxy\"]\n";
        $code .= "        Nginx[\"Nginx Reverse Proxy & HTTP/2<br/><small>Lets Encrypt SSL & Gzip Compression</small>\"]\n";
        $code .= "        Waf[\"Cyber Threat Defense<br/><small>Rate Limiter, Anti-Bot & Honeypot</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph ServerHost[\"3. {$hosting}\"]\n";
        $code .= "        AppMonolith[\"Laravel 13 Modern Monolith<br/><small>PHP 8.5+ FPM & Filament v5 Admin Suite</small>\"]\n";
        $code .= "        Islands[\"Reactive Frontend Islands<br/><small>Livewire 4 & Flux UI Engine</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph DataStorage[\"4. Basis Data & Migrasi Data Warisan\"]\n";
        $code .= "        PgSql[(\"PostgreSQL 16 Engine<br/><small>Strict ULID PK & Keyset Cursor O(1)</small>\")]\n";
        $code .= "        Redis[(\"Redis In-Memory Cache<br/><small>Queue Jobs, Rate Limit & Session</small>\")]\n";
        $code .= "        DataScope[\"Strategi Data: {$migration}\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph Gateways[\"5. Integrasi Pembayaran & Transaksi\"]\n";
        $code .= "        Midtrans[\"Midtrans Snap Gateway<br/><small>{$payment}</small>\"]\n";
        $code .= "        Notifications[\"WhatsApp & Email Alert Queue<br/><small>Idempotency Webhooks</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph Handover[\"6. Serah Terima Repo & Garansi SLA\"]\n";
        $code .= "        Repo[\"Private GitHub Repository<br/><small>100% Hak Milik Source Code Klien</small>\"]\n";
        $code .= "        SlaNote[\"Garansi Bug: {$warranty}\"]\n";
        $code .= "    end\n\n";
        $code .= "    Clients --> EdgeLayer\n";
        $code .= "    EdgeLayer --> ServerHost\n";
        $code .= "    ServerHost --> DataStorage\n";
        $code .= "    ServerHost --> Gateways\n";
        $code .= "    ServerHost -.-> Handover\n";

        return trim($code);
    }


    /**
     * Generate Sequence Diagram for Mobile App, Local Database (SQLite), & Server Sync Engine.
     */
    public static function generateMobileSyncMermaid(string $businessName): string
    {
        $code = "sequenceDiagram\n";
        $code .= "    autonumber\n";
        $code .= "    actor User as 👤 Pengguna\n";
        $code .= "    participant Mobile as 📱 Mobile App\n";
        $code .= "    participant SQLite as 💾 SQLite Lokal\n";
        $code .= "    participant SyncWorker as ⚙️ Sync Worker\n";
        $code .= "    participant API as 🌐 REST API\n";
        $code .= "    participant ServerDB as 🗄️ PostgreSQL 16\n";
        $code .= "    participant Redis as ⚡ Redis Guard\n\n";
        $code .= "    Note over User,SQLite: FASE OFFLINE (Mutasi Lokal Tanpa Internet)\n";
        $code .= "    User->>Mobile: Input Transaksi / Formulir Baru\n";
        $code .= "    Mobile->>SQLite: INSERT (status='pending_push', mutation_id=ULID)\n";
        $code .= "    SQLite-->>Mobile: Commit Berhasil (O(1) Local Latency)\n";
        $code .= "    Mobile-->>User: Tampilkan UI Sukses Instan (Optimistic UI)\n\n";
        $code .= "    Note over SyncWorker,ServerDB: FASE SINKRONISASI PUSH (Saat Terhubung Online)\n";
        $code .= "    SyncWorker->>SQLite: Ambil antrean WHERE status='pending_push'\n";
        $code .= "    SQLite-->>SyncWorker: Daftar Batch Mutasi Lokal\n";
        $code .= "    SyncWorker->>API: POST /api/v1/sync/push (Header: X-Idempotency-Key)\n";
        $code .= "    API->>Redis: Cek Kunci Idempotensi (Mencegah Duplikasi Retry)\n";
        $code .= "    alt Mutasi Baru (Valid)\n";
        $code .= "        API->>ServerDB: Transaksi ACID (Insert/Update PostgreSQL)\n";
        $code .= "        ServerDB-->>API: Berhasil Disimpan\n";
        $code .= "        API-->>SyncWorker: 200 OK { status: 'synced', server_synced_at: ISO8601 }\n";
        $code .= "    else Mutasi Duplikat (Network Retry)\n";
        $code .= "        Redis-->>API: Key Sudah Diproses Sebelumnya\n";
        $code .= "        API-->>SyncWorker: 200 OK (Idempotent Cached Result)\n";
        $code .= "    end\n";
        $code .= "    SyncWorker->>SQLite: UPDATE status='synced', server_synced_at=now()\n\n";
        $code .= "    Note over SyncWorker,ServerDB: FASE SINKRONISASI PULL (Delta Update dari Server)\n";
        $code .= "    SyncWorker->>API: GET /api/v1/sync/pull?since={last_sync}&cursor={cursor}\n";
        $code .= "    API->>ServerDB: Query Delta (updated_at > last_sync & soft deletes)\n";
        $code .= "    ServerDB-->>API: Daftar Record Baru / Diperbarui\n";
        $code .= "    API-->>SyncWorker: 200 OK { delta_records: [...], has_more: false }\n";
        $code .= "    SyncWorker->>SQLite: UPSERT Delta ke SQLite (LWW Conflict Resolution)\n";
        $code .= "    SQLite-->>Mobile: Stream Reaktif Memicu Update Tampilan UI\n";

        return trim($code);
    }

}
