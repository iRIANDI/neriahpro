<?php

namespace App\Services\Ai;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DomainHostingAsset;
use App\Models\LeadContact;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\Ai\AiWorkflowTracer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgenticActionExecutionEngine
{
    protected VectorEmbeddingService $vectorService;

    public function __construct(VectorEmbeddingService $vectorService)
    {
        $this->vectorService = $vectorService;
    }

    /**
     * Eksekusi tool function calling yang dipicu oleh LLM.
     */
    public function execute(string $toolName, array $args, ?string $sessionId = null): array
    {
        $startTime = microtime(true);
        $user = Auth::user();

        $trace = AiWorkflowTracer::startTrace(
            stage: 'execution',
            title: "Tool Execution: {$toolName}",
            sessionId: $sessionId,
            user: $user,
            input: $args,
            actionType: $toolName
        );

        try {
            $result = match ($toolName) {
                'search_blueprints' => $this->searchBlueprints($args),
                'get_blueprint_details' => $this->getBlueprintDetails($args),
                'create_blueprint' => $this->guardMutation($user, $toolName) ?? $this->createBlueprint($args, $user),
                'update_blueprint_status' => $this->guardMutation($user, $toolName) ?? $this->updateBlueprintStatus($args, $user),
                'convert_to_contract' => $this->guardMutation($user, $toolName) ?? $this->convertToContract($args, $user),
                'search_transactions' => $this->searchTransactions($args),
                'manage_domain_hosting' => (($args['action'] ?? '') === 'create' ? $this->guardMutation($user, $toolName) : null) ?? $this->manageDomainHosting($args, $user),
                'query_knowledge_base' => $this->queryKnowledgeBase($args),
                'sync_knowledge_base' => $this->guardMutation($user, $toolName) ?? $this->syncKnowledgeBase($args),
                'get_audit_logs' => $this->getAuditLogs($args),
                'get_system_stats' => $this->getSystemStats($args),
                'search_leads' => $this->searchLeads($args),
                'get_sector_ui_archetype' => $this->getSectorUiArchetype($args),
                'generate_phased_prompts' => $this->generatePhasedPrompts($args),
                'audit_brownfield_schema' => $this->auditBrownfieldSchema($args),
                'run_blueprint_slop_scanner' => $this->runBlueprintSlopScanner($args),
                default => ['error' => "Tool {$toolName} tidak dikenal oleh sistem."],
            };

            $execTimeMs = (int) round((microtime(true) - $startTime) * 1000);

            AiWorkflowTracer::completeTrace(
                trace: $trace,
                status: isset($result['error']) ? 'failed' : 'success',
                output: $result,
                summary: "Eksekusi {$toolName} selesai dalam {$execTimeMs}ms.",
                executionTimeMs: $execTimeMs
            );

            return $result;
        } catch (\Throwable $e) {
            $execTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            Log::error("AgenticActionExecutionEngine::execute error ({$toolName}): " . $e->getMessage());

            if ($trace) {
                AiWorkflowTracer::failTrace($trace, $e->getMessage(), $execTimeMs);
            }

            return [
                'error' => "Gagal mengeksekusi aksi {$toolName}: " . $e->getMessage(),
            ];
        }
    }

    /**
     * 1. Search Blueprints
     */
    protected function searchBlueprints(array $args): array
    {
        $query = VisionBlueprint::query();

        if (!empty($args['query'])) {
            $term = '%' . trim($args['query']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_bisnis', 'LIKE', $term)
                  ->orWhere('client_name', 'LIKE', $term)
                  ->orWhere('slug', 'LIKE', $term);
            });
        }

        if (!empty($args['status'])) {
            $query->where('project_status', $args['status']);
        }

        $limit = min(max((int) ($args['limit'] ?? 5), 1), 20);
        $blueprints = $query->orderBy('id', 'desc')->limit($limit)->get();

        return [
            'total_found' => $blueprints->count(),
            'blueprints' => $blueprints->map(fn ($b) => [
                'id' => $b->id,
                'title' => $b->nama_bisnis,
                'slug' => $b->slug,
                'client_name' => $b->client_name,
                'email' => $b->email,
                'project_status' => $b->project_status,
                'is_signed' => $b->signed_agreement,
                'created_at' => $b->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    /**
     * 2. Get Blueprint Details
     */
    protected function getBlueprintDetails(array $args): array
    {
        $id = $args['blueprint_id'] ?? null;
        if (!$id) {
            return ['error' => 'Parameter blueprint_id wajib diisi.'];
        }

        $b = VisionBlueprint::where('id', $id)->orWhere('slug', $id)->first();
        if (!$b) {
            return ['error' => "Blueprint dengan ID atau Slug '{$id}' tidak ditemukan."];
        }

        return [
            'id' => $b->id,
            'title' => $b->nama_bisnis,
            'slug' => $b->slug,
            'client_name' => $b->client_name,
            'email' => $b->email,
            'phone' => $b->phone,
            'project_status' => $b->project_status,
            'signed_agreement' => $b->signed_agreement,
            'signed_at' => $b->signed_at?->format('Y-m-d H:i'),
            'sha256_hash' => $b->document_sha256,
            'masalah_utama' => $b->masalah_utama,
            'has_prd' => !empty($b->prd_content),
            'staging_url' => $b->staging_url,
        ];
    }

    /**
     * 3. Create Blueprint
     */
    protected function createBlueprint(array $args, ?User $user): array
    {
        $title = trim($args['title'] ?? ($args['nama_bisnis'] ?? ''));
        $clientName = trim($args['client_name'] ?? '');

        if (empty($title) || empty($clientName)) {
            return ['error' => 'Judul proyek dan nama klien wajib diisi.'];
        }

        $slug = Str::slug($title) . '-' . strtolower(Str::random(5));

        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => $title,
            'slug' => $slug,
            'client_name' => $clientName,
            'email' => $args['client_email'] ?? ($args['email'] ?? ($user?->email ?? 'klien@neriahpro.com')),
            'phone' => $args['client_whatsapp'] ?? ($args['phone'] ?? null),
            'project_status' => 'Draft',
            'masalah_utama' => $args['idea_summary'] ?? ($args['masalah_utama'] ?? "Draf inisiatif arsitektur untuk {$clientName}."),
            'signed_agreement' => false,
        ]);

        AuditLog::record(
            action: 'create',
            model: $blueprint,
            newValues: $blueprint->toArray(),
            description: "AI Agent membuat blueprint baru: {$blueprint->nama_bisnis}"
        );

        return [
            'success' => true,
            'message' => "Blueprint '{$blueprint->nama_bisnis}' berhasil dibuat.",
            'blueprint_id' => $blueprint->id,
            'slug' => $blueprint->slug,
            'status' => $blueprint->project_status,
        ];
    }

    /**
     * 4. Update Blueprint Status
     */
    protected function updateBlueprintStatus(array $args, ?User $user): array
    {
        $id = $args['blueprint_id'] ?? null;
        $status = $args['status'] ?? null;

        $blueprint = VisionBlueprint::where('id', $id)->orWhere('slug', $id)->first();
        if (!$blueprint) {
            return ['error' => "Blueprint '{$id}' tidak ditemukan."];
        }

        $oldStatus = $blueprint->project_status;
        $blueprint->update([
            'project_status' => $status,
        ]);

        AuditLog::record(
            action: 'update',
            model: $blueprint,
            oldValues: ['project_status' => $oldStatus],
            newValues: ['project_status' => $status, 'notes' => $args['notes'] ?? null],
            description: "AI Agent memperbarui status blueprint {$blueprint->nama_bisnis} dari '{$oldStatus}' ke '{$status}'."
        );

        return [
            'success' => true,
            'message' => "Status blueprint berhasil diperbarui menjadi '{$status}'.",
            'blueprint_id' => $blueprint->id,
            'old_status' => $oldStatus,
            'new_status' => $status,
        ];
    }

    /**
     * 5. Convert to Contract
     */
    protected function convertToContract(array $args, ?User $user): array
    {
        $id = $args['blueprint_id'] ?? null;
        $blueprint = VisionBlueprint::where('id', $id)->orWhere('slug', $id)->first();
        if (!$blueprint) {
            return ['error' => "Blueprint '{$id}' tidak ditemukan."];
        }

        $title = $args['contract_title'] ?? "Kontrak Kerja Turnkey Software Factory: {$blueprint->nama_bisnis}";
        $rawPayload = json_encode([
            'blueprint_id' => $blueprint->id,
            'nama_bisnis' => $blueprint->nama_bisnis,
            'client_name' => $blueprint->client_name,
            'locked_at' => now()->toIso8601String(),
        ]);
        $sealHash = hash('sha256', $rawPayload);

        $document = Document::create([
            'title' => $title,
            'type' => 'turnkey_contract',
            'related_id' => $blueprint->id,
            'related_type' => VisionBlueprint::class,
            'scope_locked' => true,
            'scope_hash' => $sealHash,
            'status' => 'pending_signature',
            'content' => "Perjanjian Kerja Sama Pengembangan Perangkat Lunak untuk {$blueprint->nama_bisnis}.",
        ]);

        $blueprint->update([
            'signed_agreement' => true,
            'signed_at' => now(),
            'document_sha256' => $sealHash,
            'project_status' => 'Scope Locked',
        ]);

        AuditLog::record(
            action: 'create',
            model: $document,
            newValues: $document->toArray(),
            description: "AI Agent mengonversi blueprint {$blueprint->nama_bisnis} ke Kontrak Legal berstempel SHA-256."
        );

        return [
            'success' => true,
            'message' => "Kontrak legal berstempel SHA-256 berhasil diterbitkan.",
            'document_id' => $document->id,
            'sha256_seal' => $sealHash,
            'status' => $document->status,
        ];
    }

    /**
     * 6. Search Transactions
     */
    protected function searchTransactions(array $args): array
    {
        $query = Transaction::query();

        if (!empty($args['query'])) {
            $term = '%' . trim($args['query']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('midtrans_order_id', 'LIKE', $term)
                  ->orWhere('midtrans_transaction_id', 'LIKE', $term);
            });
        }

        if (!empty($args['status'])) {
            $query->where('status', $args['status']);
        }

        $limit = min(max((int) ($args['limit'] ?? 5), 1), 20);
        $transactions = $query->orderBy('id', 'desc')->limit($limit)->get();

        return [
            'total_found' => $transactions->count(),
            'transactions' => $transactions->map(fn ($t) => [
                'id' => $t->id,
                'midtrans_order_id' => $t->midtrans_order_id,
                'total_idr' => (float) $t->total_idr,
                'status' => $t->status,
                'customer_name' => $t->customer_details['name'] ?? null,
                'package_tier' => $t->customer_details['package_tier'] ?? null,
                'created_at' => $t->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    /**
     * 7. Manage Domain & Hosting
     */
    protected function manageDomainHosting(array $args, ?User $user): array
    {
        $action = $args['action'] ?? 'list_expiring';

        if ($action === 'list_expiring') {
            $expiring = DomainHostingAsset::where('expiry_date', '<=', now()->addDays(30))
                ->orderBy('expiry_date', 'asc')
                ->limit(10)
                ->get();

            return [
                'expiring_count' => $expiring->count(),
                'assets' => $expiring->map(fn ($a) => [
                    'id' => $a->id,
                    'service_name' => $a->service_name,
                    'domain_name' => $a->domain_name,
                    'asset_type' => $a->asset_type,
                    'expiry_date' => $a->expiry_date?->format('Y-m-d'),
                    'days_remaining' => now()->diffInDays($a->expiry_date, false),
                    'cost_price' => $a->cost_price,
                    'client_price' => $a->client_price,
                    'auto_renewal' => $a->auto_renewal,
                ])->toArray(),
            ];
        }

        if ($action === 'create') {
            $asset = DomainHostingAsset::create([
                'service_name' => $args['service_name'] ?? ($args['domain_name'] ?? 'Layanan Infrastruktur'),
                'domain_name' => $args['domain_name'] ?? 'host.neriahpro.com',
                'asset_type' => $args['asset_type'] ?? 'domain',
                'provider' => 'Cloud Provider',
                'registration_date' => now(),
                'expiry_date' => !empty($args['expires_at']) ? $args['expires_at'] : now()->addYear(),
                'cost_price' => $args['cost_price'] ?? 0,
                'client_price' => $args['client_price'] ?? 0,
                'subscription_status' => 'active',
                'auto_renewal' => true,
            ]);

            AuditLog::record(
                action: 'create',
                model: $asset,
                newValues: $asset->toArray(),
                description: "AI Agent mencatat aset infrastruktur baru: {$asset->domain_name}"
            );

            return [
                'success' => true,
                'message' => "Aset '{$asset->domain_name}' berhasil dicatat.",
                'asset_id' => $asset->id,
            ];
        }

        $search = $args['search'] ?? '';
        $assets = DomainHostingAsset::where('domain_name', 'LIKE', "%{$search}%")
            ->orWhere('service_name', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get();

        return [
            'total_found' => $assets->count(),
            'assets' => $assets->toArray(),
        ];
    }

    /**
     * 8. Query Knowledge Base (Hybrid RAG RRF)
     */
    protected function queryKnowledgeBase(array $args): array
    {
        $query = $args['query'] ?? '';
        $limit = min(max((int) ($args['limit'] ?? 4), 1), 10);

        $results = $this->vectorService->retrieveRelevantContext($query, $limit);

        return [
            'query' => $query,
            'total_chunks' => count($results),
            'chunks' => $results,
        ];
    }

    /**
     * 9. Sync Knowledge Base
     */
    protected function syncKnowledgeBase(array $args): array
    {
        $force = (bool) ($args['force_refresh'] ?? true);
        return $this->vectorService->syncSystemKnowledgeBase($force);
    }

    /**
     * 10. Get Audit Logs
     */
    protected function getAuditLogs(array $args): array
    {
        $query = AuditLog::with('user:id,name,email');

        if (!empty($args['action'])) {
            $query->where('action', $args['action']);
        }

        if (!empty($args['event_category']) && \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'event_category')) {
            $query->where('event_category', $args['event_category']);
        }

        $limit = min(max((int) ($args['limit'] ?? 5), 1), 20);
        $logs = $query->orderBy('id', 'desc')->limit($limit)->get();

        return [
            'total_logs' => $logs->count(),
            'logs' => $logs->map(fn ($l) => [
                'id' => $l->id,
                'action' => $l->action,
                'event_category' => $l->event_category ?? 'system',
                'actor' => $l->user?->name ?? 'System',
                'description' => $l->description,
                'integrity_hash' => $l->integrity_hash,
                'ip_address' => $l->ip_address,
                'created_at' => $l->created_at?->format('Y-m-d H:i:s'),
            ])->toArray(),
        ];
    }

    /**
     * 11. Get System Stats
     */
    protected function getSystemStats(array $args = []): array
    {
        $activeSprints = VisionBlueprint::where('project_status', 'Active Sprint')->count();
        $totalBlueprints = VisionBlueprint::count();
        $scopeLocked = VisionBlueprint::where('signed_agreement', true)->count();
        $revenue = (float) Transaction::where('status', 'settlement')->sum('total_idr');
        $expiringAssets = DomainHostingAsset::where('expiry_date', '<=', now()->addDays(30))->count();
        $totalKnowledge = \Illuminate\Support\Facades\Schema::hasTable('ai_knowledge_vectors')
            ? DB::table('ai_knowledge_vectors')->count()
            : 0;

        return [
            'scope' => $args['scope'] ?? 'all',
            'total_blueprints' => $totalBlueprints,
            'active_sprints' => $activeSprints,
            'max_parallel_sprints' => 3,
            'available_sprint_slots' => max(0, 3 - $activeSprints),
            'scope_locked_projects' => $scopeLocked,
            'total_revenue_settled' => $revenue,
            'expiring_assets_30_days' => $expiringAssets,
            'total_knowledge_chunks' => $totalKnowledge,
            'sectors_count' => count(\App\Support\ProjectOsMatrixCatalog::sectors()),
            'primitives_count' => count(\App\Support\ProjectOsMatrixCatalog::primitives()),
            'stack_presets_count' => count(\App\Support\PosStackPresetCatalog::presets()),
            'native_tools_count' => count(GeminiToolRegistry::getDeclarations()),
        ];
    }

    /**
     * 12. Search Leads
     */
    protected function searchLeads(array $args): array
    {
        $limit = min(max((int) ($args['limit'] ?? 5), 1), 20);
        $leads = LeadContact::orderBy('id', 'desc')->limit($limit)->get();

        return [
            'total_leads' => $leads->count(),
            'leads' => $leads->map(fn ($lead) => [
                'id' => $lead->id,
                'name' => $lead->name ?? $lead->client_name,
                'email' => $lead->email ?? $lead->client_email,
                'service_requested' => $lead->service_type ?? 'Software Architecture Consulting',
                'status' => $lead->status ?? 'new',
                'created_at' => $lead->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    /**
     * 13. Get Sectoral Industrial UI/UX Archetype & Realistic Seeder
     */
    protected function getSectorUiArchetype(array $args): array
    {
        $input = trim((string) ($args['sector_code'] ?? $args['sector_code_or_keyword'] ?? ''));
        $allArchetypes = \App\Support\PosSectorUiArchetypeCatalog::sectorUiArchetypes();

        if ($input === '') {
            return [
                'total_sectors' => count($allArchetypes),
                'sectors' => collect(\App\Support\ProjectOsMatrixCatalog::sectors())->map(function (array $s) use ($allArchetypes) {
                    $arch = $allArchetypes[$s['code']] ?? [];
                    return [
                        'code' => $s['code'],
                        'name' => $s['name']['id'] ?? $s['code'],
                        'primary_archetype' => $arch['primary_archetype'] ?? '',
                        'secondary_archetype' => $arch['secondary_archetype'] ?? '',
                        'headline' => $arch['headline']['id'] ?? '',
                    ];
                })->values()->all(),
            ];
        }

        $upper = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode($input);
        if (isset($allArchetypes[$upper])) {
            $code = $upper;
        } else {
            $resolved = \App\Services\ProjectOsBlueprintCompilerService::resolveSectorAndPrimitives($input);
            $code = (string) ($resolved['sector']->code ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        }

        return [
            'sector_code' => $code,
            'ui_ux_archetype' => \App\Support\PosSectorUiArchetypeCatalog::forSector($code),
            'realistic_domain_seeder' => \App\Support\PosSectorUiArchetypeCatalog::seederForSector($code),
        ];
    }

    /**
     * 14. Generate Phased Prompts (Phase 1 to 6) & CLI Bootstrapper Command
     */
    protected function generatePhasedPrompts(array $args): array
    {
        $id = trim((string) ($args['blueprint_id'] ?? ''));
        if ($id === '') {
            return ['error' => 'Parameter blueprint_id wajib diisi.'];
        }

        $blueprint = VisionBlueprint::where('id', $id)->orWhere('slug', $id)->first();
        if (! $blueprint) {
            return ['error' => "Blueprint '{$id}' tidak ditemukan."];
        }

        $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        $phases = $schema['phased_prompt_sequencer']
            ?? \App\Services\ProjectOsBlueprintCompilerService::compilePhasedPrompts($schema);

        $cliCommand = 'curl -sL ' . url('/init/' . $blueprint->id) . ' | bash';
        $phaseNum = isset($args['phase_number']) ? (int) $args['phase_number'] : null;

        if ($phaseNum && $phaseNum >= 1 && $phaseNum <= 6) {
            return [
                'blueprint_id' => $blueprint->id,
                'slug' => $blueprint->slug,
                'cli_bootstrap_command' => $cliCommand,
                'selected_phase' => $phases[$phaseNum - 1] ?? null,
            ];
        }

        return [
            'blueprint_id' => $blueprint->id,
            'slug' => $blueprint->slug,
            'cli_bootstrap_command' => $cliCommand,
            'total_phases' => count($phases),
            'phases' => $phases,
        ];
    }

    /**
     * 15. Audit Brownfield SQL / Migration Schema
     */
    protected function auditBrownfieldSchema(array $args): array
    {
        $schemaText = (string) ($args['schema_text'] ?? '');
        $hint = isset($args['project_hint']) ? (string) $args['project_hint'] : null;

        return app(\App\Services\BrownfieldMigrationAuditorService::class)->auditSchema($schemaText, $hint, true);
    }

    /**
     * 16. Run Blueprint Drift Auditor & Slop Scanner
     */
    protected function runBlueprintSlopScanner(array $args): array
    {
        $scanner = app(\App\Services\BlueprintSlopScannerService::class);

        if (! empty($args['blueprint_id'])) {
            $id = trim((string) $args['blueprint_id']);
            $blueprint = VisionBlueprint::where('id', $id)->orWhere('slug', $id)->first();
            if (! $blueprint) {
                return ['error' => "Blueprint '{$id}' tidak ditemukan."];
            }
            return $scanner->auditBlueprint($blueprint, true);
        }

        if (! empty($args['code_snippet'])) {
            return $scanner->scanCodeSnippet((string) $args['code_snippet'], true);
        }

        return ['error' => 'Harap isi blueprint_id atau code_snippet untuk dipindai oleh Slop Scanner.'];
    }

    /**
     * RBAC Guard for state-mutating AI Native Tools.
     */
    protected function guardMutation(?User $user, string $toolName): ?array
    {
        if (! $user) {
            return null;
        }

        $allowed = $user->hasAnyRole(['super_admin', 'developer'])
            || $user->can('execute_ai_native_tools')
            || $user->email === 'yoseph.iriandi.tambunan@gmail.com';

        if (! $allowed) {
            AuditLog::record(
                action: 'rbac_tool_denied',
                newValues: ['tool' => $toolName, 'user_id' => $user->id],
                description: "RBAC menolak eksekusi mutating AI Native Tool '{$toolName}' oleh {$user->email}.",
                eventCategory: 'security'
            );

            return [
                'error' => "Akses ditolak oleh kebijakan RBAC: peran akun Anda tidak memiliki izin mutasi untuk tool '{$toolName}'.",
            ];
        }

        return null;
    }
}

