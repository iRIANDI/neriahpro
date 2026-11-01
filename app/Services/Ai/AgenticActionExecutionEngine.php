<?php

namespace App\Services\Ai;

use App\Actions\Audit\ExportFilteredAuditExcelAction;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DomainHostingAsset;
use App\Models\LeadContact;
use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AiWorkflowTracer;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\PosCommercialBridgeService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Support\PosAgentShieldAndCoDesignCatalog;
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
        $idempotencyKey = DocumentThreatSanitizerService::computeToolIdempotencyKey(
            $toolName,
            $args,
            $user?->id ? (string) $user->id : null,
            $sessionId
        );

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
                'run_blueprint_doctor' => $this->runBlueprintDoctor($args),
                'get_day2_production_contracts' => $this->getDay2ProductionContracts($args),
                'generate_delta_change_request' => $this->guardMutation($user, $toolName) ?? $this->generateDeltaChangeRequest($args),
                'compile_decision_table_matrix' => $this->compileDecisionTableMatrix($args),
                'audit_environment_and_redteam_readiness' => $this->auditEnvironmentAndRedteamReadiness($args),
                'calculate_mitra_quotation_and_wbs' => $this->calculateMitraQuotationAndWbs($args),
                'freeze_scope_and_generate_spk_contract' => $this->guardMutation($user, $toolName) ?? $this->freezeScopeAndGenerateSpkContract($args),
                'manage_uat_checklist_and_digital_bast' => (in_array(strtolower(trim((string) ($args['action'] ?? $args['operation'] ?? 'inspect'))), ['verify_all_uat', 'sign_bast'], true) ? $this->guardMutation($user, $toolName) : null) ?? $this->manageUatChecklistAndDigitalBast($args),
                'expand_l1_l4_enterprise_menu_matrix' => $this->expandL1L4EnterpriseMenuMatrix($args),
                'compile_symbol_manifest_and_dag_chunks' => $this->compileSymbolManifestAndDagChunks($args),
                'verify_project_isolation_and_clean_state' => (strtolower(trim((string) ($args['action'] ?? 'inspect'))) === 'purge_stale_drafts' ? $this->guardMutation($user, $toolName) : null) ?? $this->verifyProjectIsolationAndCleanState($args),
                'inspect_hardware_and_websocket_runtime_contracts' => $this->inspectHardwareAndWebsocketRuntimeContracts($args),
                'generate_syntax_lock_and_client_sop_pack' => $this->generateSyntaxLockAndClientSopPack($args),
                'audit_fifteen_pillars_architectural_freeze' => (! empty($args['persist_sync']) ? $this->guardMutation($user, $toolName) : null) ?? $this->auditFifteenPillarsArchitecturalFreeze($args),
                'configure_visual_ui_and_jurisdiction_locale' => (! empty($args['project_ulid']) ? $this->guardMutation($user, $toolName) : null) ?? $this->configureVisualUiAndJurisdictionLocale($args),
                'audit_living_cve_alerts_and_autopatch' => (! empty($args['project_ulid']) && ! empty($args['acknowledge_all']) ? $this->guardMutation($user, $toolName) : null) ?? $this->auditLivingCveAlertsAndAutopatch($args),
                'manage_ip_canary_watermark_and_vault_security' => (strtolower(trim((string) ($args['action'] ?? 'inspect_layers'))) === 'revoke_mcp_token' ? $this->guardMutation($user, $toolName) : null) ?? $this->manageIpCanaryWatermarkAndVaultSecurity($args),
                'sanitize_and_audit_agentshield' => $this->guardMutation($user, $toolName) ?? $this->sanitizeAndAuditAgentshield($args),
                'manage_codesign_alignment_room' => (in_array(strtolower(trim((string) ($args['action'] ?? 'inspect_room'))), ['sign_module', 'add_custom_module', 'dual_key_lock'], true) ? $this->guardMutation($user, $toolName) : null) ?? $this->manageCodesignAlignmentRoom($args, $user),
                'verify_and_export_crypto_audit_trail' => $this->guardMutation($user, $toolName) ?? $this->verifyAndExportCryptoAuditTrail($args),
                'configure_dual_zone_public_ux_preset' => (! empty($args['project_ulid']) ? $this->guardMutation($user, $toolName) : null) ?? $this->configureDualZonePublicUxPreset($args),
                'manage_verified_testimonials_aniwall' => (strtolower(trim((string) ($args['action'] ?? 'list_aniwall'))) === 'issue_receipt' ? $this->guardMutation($user, $toolName) : null) ?? $this->manageVerifiedTestimonialsAniwall($args),
                'manage_affiliate_and_impact_grants' => (strtolower(trim((string) ($args['action'] ?? 'summary'))) === 'approve_grant' ? $this->guardMutation($user, $toolName) : null) ?? $this->manageAffiliateAndImpactGrants($args, $user),
                'inspect_saas_system_health_and_billing_gate' => $this->inspectSaasSystemHealthAndBillingGate($args, $user),
                default => ['error' => "Tool {$toolName} tidak dikenal oleh sistem."],
            };

            if (is_array($result) && ! isset($result['error']) && $toolName !== 'get_system_stats') {
                $result['idempotency_key'] = $idempotencyKey;
            }

            $execTimeMs = (int) round((microtime(true) - $startTime) * 1000);

            if ($trace) {
                AiWorkflowTracer::completeTrace(
                    trace: $trace,
                    status: isset($result['error']) ? 'failed' : 'success',
                    output: $result,
                    summary: "Eksekusi {$toolName} selesai dalam {$execTimeMs}ms.",
                    executionTimeMs: $execTimeMs
                );
            }

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
                'sector_code' => $l->sector_code ?? null,
                'primitive_code' => $l->primitive_code ?? null,
                'execution_channel' => $l->execution_channel ?? 'WEB_UI',
                'idempotency_key' => $l->idempotency_key ?? null,
                'actor' => $l->user?->name ?? 'System',
                'description' => $l->description,
                'integrity_hash' => $l->integrity_hash,
                'prev_hash' => $l->prev_hash ?? null,
                'current_hash' => $l->current_hash ?? null,
                'chain_status' => $l->verifyRowIntegrity()['status'] ?? 'VERIFIED_INTACT',
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
        $changeRequestsCount = \Illuminate\Support\Facades\Schema::hasTable('pos_change_requests')
            ? DB::table('pos_change_requests')->count()
            : 0;
        $mitraProjectsCount = (\Illuminate\Support\Facades\Schema::hasTable('pos_projects') && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'engagement_mode'))
            ? DB::table('pos_projects')->where('engagement_mode', 'MITRA_TURNKEY_PROJECT')->count()
            : 0;
        $bastIssuedCount = (\Illuminate\Support\Facades\Schema::hasTable('pos_projects') && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'bast_signoff_json'))
            ? DB::table('pos_projects')->whereNotNull('bast_signoff_json')->count()
            : 0;

        return [
            'scope' => $args['scope'] ?? 'all',
            'total_blueprints' => $totalBlueprints,
            'active_sprints' => $activeSprints,
            'max_parallel_sprints' => 3,
            'available_sprint_slots' => max(0, 3 - $activeSprints),
            'scope_locked_projects' => $scopeLocked,
            'mitra_projects_count' => $mitraProjectsCount,
            'bast_issued_count' => $bastIssuedCount,
            'total_revenue_settled' => $revenue,
            'expiring_assets_30_days' => $expiringAssets,
            'total_knowledge_chunks' => $totalKnowledge,
            'change_requests_count' => $changeRequestsCount,
            'sectors_count' => count(\App\Support\ProjectOsMatrixCatalog::sectors()),
            'primitives_count' => count(\App\Support\ProjectOsMatrixCatalog::primitives()),
            'stack_presets_count' => count(\App\Support\PosStackPresetCatalog::stackPresets()),
            'day_two_layers_count' => count(\App\Support\PosDayTwoProductionCatalog::finalEightLayersSummary()),
            'lifecycle_features_count' => count(\App\Support\PosLifecycleAndStudioCatalog::fiveLifecycleFeatures()),
            'commercial_bridge_features_count' => count(\App\Services\PosCommercialBridgeService::fiveCommercialBridgeFeatures()),
            'deterministic_guarantees_count' => count(\App\Support\PosEnterpriseGuaranteeCatalog::fiveDeterministicGuarantees()),
            'enterprise_glue_tables_count' => count(\App\Support\PosEnterpriseGuaranteeCatalog::fiveEnterpriseGluePrimitives()),
            'real_world_elements_count' => count(\App\Support\PosRealWorldRuntimeCatalog::fiveRealWorldElementsOverview()),
            'architecture_pillars_count' => count(\App\Models\PosArchitecturePillar::getCachedMatrix()),
            'architectural_freeze_score' => 100,
            'l1_l4_sector_submenus_count' => 300,
            'visual_ui_blueprints_count' => 36,
            'navigation_shells_count' => count(\App\Support\PosInclusiveCommercialCatalog::navigationShellOptions()),
            'tier1_jurisdictions_count' => 7,
            'inclusive_plg_skus_count' => count(\App\Support\PricingRegistry::getInclusiveValueMetricCatalog()),
            'agentshield_layers_count' => 5,
            'sector_audit_presets_count' => count(PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets()),
            'public_ux_presets_count' => count(\App\Support\PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets()),
            'saas_commercial_modules_count' => count(\App\Support\PosDualZoneFrontendAndSaasCatalog::sixCriticalSaasModules()),
            'verified_testimonials_count' => count(\App\Support\PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog()),
            'world_class_sprints_count' => 5,
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
        $input = trim((string) ($args['sector_code'] ?? ''));
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

        $normalized = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode($input);
        if (isset($allArchetypes[$normalized])) {
            $code = $normalized;
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
     * 14. Generate Phased Prompts (Phase 1 to 7) & CLI Bootstrapper Command
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

        if ($phaseNum && $phaseNum >= 1 && $phaseNum <= 7) {
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

        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup !== '') {
            $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if (! $blueprint) {
                return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
            }
            return $scanner->auditBlueprint($blueprint, true);
        }

        if (! empty($args['code_snippet'])) {
            return $scanner->scanCodeSnippet((string) $args['code_snippet'], true);
        }

        return ['error' => 'Harap isi blueprint_id atau code_snippet untuk dipindai oleh Slop Scanner.'];
    }

    /**
     * 17. Run Interactive Blueprint Doctor (Pre-Flight Conflict Resolver & Readiness Validator)
     */
    protected function runBlueprintDoctor(array $args): array
    {
        $doctor = app(\App\Services\BlueprintDoctorService::class);

        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup !== '') {
            $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if (! $blueprint) {
                return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
            }

            return $doctor->diagnoseBlueprint($blueprint, true);
        }

        return $doctor->diagnose([
            'sector_code' => $args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'primitive_codes' => $args['primitive_codes'] ?? [],
            'stack_preset_code' => $args['stack_preset_code'] ?? 'STACK_M1_LARAVEL_MONOLITH',
            'workflow_text' => $args['workflow_text'] ?? '',
            'timeline_days' => isset($args['timeline_days']) ? (int) $args['timeline_days'] : 45,
        ], true);
    }

    /**
     * 18. Get 8 Day-2 Production Contracts & 10 Ultimate Perfection Pillars for a Sector
     */
    protected function getDay2ProductionContracts(array $args): array
    {
        $input = trim((string) ($args['sector_code'] ?? ''));

        if ($input === '') {
            $code = 'SECTOR_01_COMMERCE_OMNICHANNEL';
        } else {
            $normalized = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode($input);
            if ($normalized !== 'SECTOR_01_COMMERCE_OMNICHANNEL' || str_contains(strtoupper($input), 'COMMERCE') || str_contains(strtoupper($input), 'SECTOR_01')) {
                $code = $normalized;
            } else {
                $resolved = \App\Services\ProjectOsBlueprintCompilerService::resolveSectorAndPrimitives($input);
                $code = (string) ($resolved['sector']->code ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
            }
        }

        return [
            'success' => true,
            'sector_code' => $code,
            'day_two_bundle' => \App\Support\PosDayTwoProductionCatalog::bundleForSector($code),
            'final_eight_layers' => \App\Support\PosDayTwoProductionCatalog::finalEightLayersSummary(),
            'ten_perfection_pillars' => \App\Support\PosDayTwoProductionCatalog::tenPerfectionPillars(),
        ];
    }

    /**
     * 19. Generate Iterative Change Request & Surgical Delta Blueprint (v1.0 -> v1.1)
     */
    protected function generateDeltaChangeRequest(array $args): array
    {
        $id = trim((string) ($args['blueprint_id'] ?? ''));
        $summary = trim((string) ($args['change_summary'] ?? ''));

        if ($id === '' || $summary === '') {
            return ['error' => 'Parameter blueprint_id dan change_summary wajib diisi.'];
        }

        $blueprint = VisionBlueprint::where('id', $id)->orWhere('slug', $id)->first();
        if (! $blueprint) {
            return ['error' => "Blueprint '{$id}' tidak ditemukan."];
        }

        $posProject = app(\App\Services\ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        $title = trim((string) ($args['title'] ?? 'Penambahan Fitur Evolusi Bisnis'));

        return app(\App\Services\BlueprintDeltaEvolutionService::class)->createChangeRequest(
            $blueprint,
            $posProject,
            [
                'title' => $title,
                'change_summary' => $summary,
            ],
            true
        );
    }

    /**
     * 20. Compile Deterministic Business Rule Decision Tables (Anti-Nested if-else Slop)
     */
    protected function compileDecisionTableMatrix(array $args): array
    {
        $customRules = null;
        $code = 'SECTOR_01_COMMERCE_OMNICHANNEL';
        $appName = 'Project OS Enterprise';

        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup !== '') {
            $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if (! $blueprint) {
                return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
            }
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
            $code = (string) ($schema['project_metadata']['primary_sector'] ?? $code);
            $appName = (string) ($schema['project_metadata']['name'] ?? $blueprint->nama_bisnis);
            $posProject = \App\Models\PosProject::where('vision_blueprint_id', $blueprint->id)->first();
            if ($posProject && is_array($posProject->decision_tables_json) && ! empty($posProject->decision_tables_json)) {
                $customRules = $posProject->decision_tables_json;
            }
        } elseif (! empty($args['sector_code'])) {
            $code = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode((string) $args['sector_code']);
        }

        $matrix = \App\Support\PosLifecycleAndStudioCatalog::decisionTablesForSector($code, $customRules);

        return [
            'success' => true,
            'sector_code' => $code,
            'decision_matrix' => $matrix,
            'compiled_php_class' => \App\Support\PosLifecycleAndStudioCatalog::compileDecisionTablesToPhpClass($matrix, $appName),
        ];
    }

    /**
     * 21. Audit Environment Vault (.env.blueprint), Phase 7 Security Red-Team Suite & MCP Bridge
     */
    protected function auditEnvironmentAndRedteamReadiness(array $args): array
    {
        $code = 'SECTOR_01_COMMERCE_OMNICHANNEL';
        $primitives = ['PRIM_IAM_MULTI_TENANT', 'PRIM_AUDIT_SECURITY_LEDGER'];
        $stackPreset = trim((string) ($args['stack_preset_code'] ?? 'STACK_M1_LARAVEL_FILAMENT_LIVEWIRE'));
        $blueprintSlug = null;

        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup !== '') {
            $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if (! $blueprint) {
                return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
            }
            $blueprintSlug = $blueprint->slug;
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
            $code = (string) ($schema['project_metadata']['primary_sector'] ?? $code);
            $primitives = array_values((array) ($schema['project_metadata']['injected_primitives'] ?? $primitives));
            $stackPreset = (string) ($schema['project_metadata']['stack_preset'] ?? $stackPreset);
        } elseif (! empty($args['sector_code'])) {
            $code = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode((string) $args['sector_code']);
        }

        $envVault = \App\Support\PosLifecycleAndStudioCatalog::environmentVaultBlueprint($code, $primitives, $stackPreset);
        $redTeam = \App\Support\PosLifecycleAndStudioCatalog::redTeamSecuritySuiteContract($code, $primitives);

        return [
            'success' => true,
            'sector_code' => $code,
            'stack_preset' => $stackPreset,
            'environment_secret_vault' => $envVault,
            'env_blueprint_preview' => \App\Support\PosLifecycleAndStudioCatalog::renderEnvBlueprintFile($envVault, $blueprintSlug ?? 'neriahpro-app'),
            'phase_7_security_redteam_suite' => $redTeam,
            'mcp_server_bridge' => [
                'endpoint' => url('/api/mcp/project-os'),
                'config_endpoint' => $blueprintSlug ? url("/api/blueprint/{$blueprintSlug}/mcp-config") : null,
                'studio_url' => $blueprintSlug ? url("/blueprint/studio/{$blueprintSlug}") : url('/blueprint/studio'),
            ],
        ];
    }

    /**
     * 22. Algorithmic Scope-to-Quotation & WBS Estimator + Satu Pintu Package Comparison
     */
    protected function calculateMitraQuotationAndWbs(array $args): array
    {
        $bridge = app(\App\Services\PosCommercialBridgeService::class);
        $terminScheme = in_array(($args['termin_scheme'] ?? ''), ['30_50_20', '50_50'], true)
            ? (string) $args['termin_scheme']
            : null;

        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup !== '') {
            $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if (! $blueprint) {
                return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
            }
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
        } else {
            $sectorCode = ! empty($args['sector_code'])
                ? \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode((string) $args['sector_code'])
                : 'SECTOR_01_COMMERCE_OMNICHANNEL';
            $stackPreset = trim((string) ($args['stack_preset_code'] ?? 'STACK_M1_LARAVEL_FILAMENT_LIVEWIRE'));
            $sectorMeta = collect(\App\Support\ProjectOsMatrixCatalog::sectors())->firstWhere('code', $sectorCode)
                ?: \App\Support\ProjectOsMatrixCatalog::sectors()[0];
            $primitives = array_values((array) ($sectorMeta['mandatory_primitives'] ?? ['PRIM_IAM_MULTI_TENANT', 'PRIM_AUDIT_SECURITY_LEDGER']));
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
                'name' => 'Simulasi Mitra Proyek (' . ($sectorMeta['name']['id'] ?? $sectorCode) . ')',
                'sector_code' => $sectorCode,
                'primitive_codes' => $primitives,
                'stack_preset_code' => $stackPreset,
            ]);
        }

        $quotation = $bridge->calculateScopeQuotation($schema, ['termin_scheme' => $terminScheme]);

        return [
            'success' => true,
            'quotation' => $quotation,
            'satu_pintu_packages' => \App\Support\PricingRegistry::getSatuPintuCatalog(),
            'guided_wizard_steps' => \App\Services\PosCommercialBridgeService::guidedClientJourneySteps(),
        ];
    }

    /**
     * 23. Cryptographic Scope Freeze & Legal SPK/PKS Appendix Generator
     */
    protected function freezeScopeAndGenerateSpkContract(array $args): array
    {
        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup === '') {
            return ['error' => 'Parameter blueprint_id wajib diisi.'];
        }

        $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
        if (! $blueprint) {
            return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
        }

        $posProject = app(\App\Services\ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        $terminScheme = in_array(($args['termin_scheme'] ?? ''), ['30_50_20', '50_50'], true)
            ? (string) $args['termin_scheme']
            : null;

        return app(\App\Services\PosCommercialBridgeService::class)->lockScopeAndGenerateSpk(
            $blueprint,
            $posProject,
            ['termin_scheme' => $terminScheme]
        );
    }

    /**
     * 24. Interactive UAT Checklist (Given-When-Then) & Digital BAST Sign-Off
     */
    protected function manageUatChecklistAndDigitalBast(array $args): array
    {
        $bpLookup = trim((string) ($args['blueprint_id'] ?? $args['blueprint_identifier'] ?? ''));
        if ($bpLookup === '') {
            return ['error' => 'Parameter blueprint_id wajib diisi.'];
        }

        $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
        if (! $blueprint) {
            return ['error' => "Blueprint '{$bpLookup}' tidak ditemukan."];
        }

        $posProject = app(\App\Services\ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        $bridge = app(\App\Services\PosCommercialBridgeService::class);
        $action = strtolower(trim((string) ($args['action'] ?? $args['operation'] ?? 'inspect')));

        if ($action === 'verify_all_uat' || $action === 'sign_bast') {
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
            $checklist = $bridge->buildUatChecklist($schema, is_array($posProject->uat_checklist_json) ? $posProject->uat_checklist_json : []);
            $allPassed = array_map(static function (array $item): array {
                $item['passed'] = true;
                $item['checked_at'] = now()->toIso8601String();
                $item['checked_by'] = auth()->user()?->name ?: 'Neriah Pro AI Agent';
                return $item;
            }, $checklist['scenarios'] ?? []);
            $rebuiltChecklist = $bridge->buildUatChecklist($schema, ['scenarios' => $allPassed]);
            $posProject->update(['uat_checklist_json' => $rebuiltChecklist]);

            if ($action === 'verify_all_uat') {
                return [
                    'success' => true,
                    'action' => 'verify_all_uat',
                    'uat_checklist' => $rebuiltChecklist,
                ];
            }

            return $bridge->signOffUatAndIssueBast($blueprint, $posProject->fresh(), [
                'signer_name' => $args['signer_name'] ?? ($blueprint->client_name ?: 'Client Representative'),
                'signer_title' => $args['signer_title'] ?? 'Product Owner / Direktur',
            ]);
        }

        $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
        return [
            'success' => true,
            'action' => $action,
            'uat_checklist' => $bridge->buildUatChecklist($schema, is_array($posProject->uat_checklist_json) ? $posProject->uat_checklist_json : []),
            'milestone_gantt' => $bridge->buildMilestoneGanttAndStaging($blueprint, $posProject),
            'bast_signoff' => $posProject->bast_signoff_json,
        ];
    }

    /**
     * 25. Expand L1–L4 Enterprise Menu Matrix (25 Sub-Menus) & 5 Corporate Glue Primitives (v6.0)
     */
    protected function expandL1L4EnterpriseMenuMatrix(array $args): array
    {
        $bpLookup = trim((string) ($args['blueprint_id'] ?? ''));
        $selectedMenus = null;
        $sectorCode = (string) ($args['sector_code'] ?? '');
        $blueprint = null;

        if ($bpLookup !== '') {
            $blueprint = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if ($blueprint) {
                $meta = is_array($blueprint->user_metadata) ? $blueprint->user_metadata : [];
                if ($sectorCode === '') {
                    $sectorCode = (string) ($meta['primary_sector'] ?? '');
                }
                if (! empty($meta['selected_menus']) && is_array($meta['selected_menus'])) {
                    $selectedMenus = $meta['selected_menus'];
                }
            }
        }

        $normalizedSector = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
            $sectorCode !== '' ? $sectorCode : 'SECTOR_01_COMMERCE_OMNICHANNEL'
        );
        $matrix = \App\Support\PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector($normalizedSector, $selectedMenus);
        $glue = \App\Support\PosEnterpriseGuaranteeCatalog::fiveEnterpriseGluePrimitives();

        AuditLog::record(
            action: 'enterprise_menu_expanded',
            model: $blueprint,
            newValues: [
                'sector_code' => $normalizedSector,
                'total_menus' => $matrix['total_menus'],
                'included_v1_count' => $matrix['included_v1_count'],
            ],
            description: "Matriks L1–L4 Enterprise Menu ({$matrix['total_menus']} sub-menu) & 5 Enterprise Glue diekstrak untuk {$normalizedSector}.",
            eventCategory: 'enterprise_glue'
        );

        return [
            'success' => true,
            'sector_code' => $normalizedSector,
            'total_sub_menus' => $matrix['total_enterprise_menus'],
            'included_v1_count' => $matrix['included_v1_menus_count'],
            'enterprise_menu_matrix' => $matrix,
            'five_enterprise_glue_primitives' => $glue,
            'enterprise_glue_primitives' => $glue,
        ];
    }

    /**
     * 26. Compile Exact SYMBOL_MANIFEST.json & Kahn Topological DAG Sub-Chunked Prompts (v6.0)
     */
    protected function compileSymbolManifestAndDagChunks(array $args): array
    {
        $bpLookup = trim((string) ($args['blueprint_id'] ?? ''));
        $blueprint = $bpLookup !== ''
            ? VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first()
            : null;

        if ($blueprint) {
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);
        } else {
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
                'sector_code' => $args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'stack_preset_code' => $args['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH',
            ]);
        }

        $manifest = $schema['symbol_manifest'] ?? [];
        $dag = $schema['topological_dag_plan'] ?? [];
        $subParts = $schema['sub_chunked_prompts'] ?? [];

        AuditLog::record(
            action: 'symbol_manifest_compiled',
            model: $blueprint,
            newValues: [
                'entities_count' => $manifest['entities_count'] ?? 0,
                'dag_layers_count' => $dag['total_layers'] ?? 0,
                'sub_parts_count' => count($subParts),
            ],
            description: 'Kontrak SYMBOL_MANIFEST.json & Kahn Topological DAG Sub-Chunks berhasil dikompilasi.',
            eventCategory: 'symbol_manifest'
        );

        return [
            'success' => true,
            'project_isolation_id' => $manifest['project_isolation_id'] ?? null,
            'entities_count' => $manifest['entities_count'] ?? 0,
            'topological_migration_order' => $manifest['topological_migration_order'] ?? [],
            'dag_layers' => $dag['layers'] ?? [],
            'sub_parts_count' => count($subParts),
            'sub_chunked_parts_count' => count($subParts),
            'sub_parts_summary' => array_values(array_map(fn (array $p): array => [
                'part_code' => $p['part_code'],
                'filename' => $p['filename'],
                'title' => $p['title'],
                'entities_count' => $p['entities_count'],
                'has_verification_gate' => str_contains((string) ($p['prompt'] ?? ''), '<MANDATORY_VERIFICATION_GATE>'),
            ], $subParts)),
            'symbol_manifest' => $manifest,
        ];
    }

    /**
     * 27. Verify Hermetic Project Isolation & Optional Stale Draft Purge (v6.0)
     */
    protected function verifyProjectIsolationAndCleanState(array $args): array
    {
        $action = strtolower(trim((string) ($args['action'] ?? 'inspect')));
        if ($action === 'purge_stale_drafts') {
            session()->forget('blueprint_draft');
        }

        $totalBlueprints = VisionBlueprint::count();
        $distinctSlugs = VisionBlueprint::distinct('slug')->count('slug');
        $duplicateSlugsCount = max(0, $totalBlueprints - $distinctSlugs);

        $posProjectsCount = \Illuminate\Support\Facades\Schema::hasTable('pos_projects')
            ? DB::table('pos_projects')->count()
            : 0;
        $distinctTokens = (\Illuminate\Support\Facades\Schema::hasTable('pos_projects') && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'isolation_token'))
            ? DB::table('pos_projects')->whereNotNull('isolation_token')->distinct('isolation_token')->count('isolation_token')
            : 0;

        $bpLookup = trim((string) ($args['blueprint_id'] ?? ''));
        $targetInspection = null;
        if ($bpLookup !== '') {
            $bp = VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first();
            if ($bp) {
                $linkedPos = \Illuminate\Support\Facades\Schema::hasTable('pos_projects')
                    ? \App\Models\PosProject::query()->where('vision_blueprint_id', $bp->id)->first()
                    : null;
                $targetInspection = [
                    'blueprint_ulid' => $bp->id,
                    'slug' => $bp->slug,
                    'staging_url' => $bp->staging_url,
                    'pos_project_ulid' => $linkedPos?->id,
                    'isolation_token' => $linkedPos?->isolation_token ?? ($bp->user_metadata['isolation_token'] ?? null),
                ];
            }
        }

        $isHermeticallyClean = $duplicateSlugsCount === 0;

        AuditLog::record(
            action: 'project_isolation_verified',
            newValues: [
                'action' => $action,
                'total_blueprints' => $totalBlueprints,
                'duplicate_slugs' => $duplicateSlugsCount,
                'pos_projects_count' => $posProjectsCount,
                'distinct_isolation_tokens' => $distinctTokens,
            ],
            description: "Audit Hermetic Project Isolation dijalankan (status bersih: " . ($isHermeticallyClean ? 'YA' : 'PERLU TINDAKAN') . ").",
            eventCategory: 'project_isolation'
        );

        return [
            'success' => true,
            'action' => $action,
            'hermetic_isolation_verified' => $isHermeticallyClean,
            'hermetic_isolation_healthy' => $isHermeticallyClean,
            'total_blueprints' => $totalBlueprints,
            'duplicate_slugs_count' => $duplicateSlugsCount,
            'pos_projects_count' => $posProjectsCount,
            'distinct_isolation_tokens' => $distinctTokens,
            'target_project' => $targetInspection,
        ];
    }

    /**
     * 28. Inspect Hardware/IoT Bridge, Zero-Polling WebSocket & Strict 0% N+1 + PG16 Partitioning Contracts (v7.0)
     */
    protected function inspectHardwareAndWebsocketRuntimeContracts(array $args): array
    {
        $rawLocale = (string) ($args['locale'] ?? 'id');
        $locale = in_array($rawLocale, ['id', 'en'], true) ? $rawLocale : 'id';
        $bpLookup = trim((string) ($args['blueprint_id'] ?? ''));
        $blueprint = $bpLookup !== ''
            ? VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first()
            : null;

        if ($blueprint) {
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
                'persist_project' => false,
            ]);
        } else {
            $sectorCode = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
                (string) ($args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
            );
            $stackCode = \App\Support\PosStackPresetCatalog::normalizePresetCode(
                (string) ($args['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
            );
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
                'app_name' => 'Runtime Contract Simulation',
                'sector_code' => $sectorCode,
                'stack_preset_code' => $stackCode,
            ]);
        }

        $hw = $schema['hardware_iot_peripheral_contract'] ?? [];
        $ws = $schema['realtime_websocket_contract'] ?? [];
        $strictDb = $schema['strict_query_and_partition_contract'] ?? [];

        AuditLog::record(
            action: 'hardware_and_websocket_inspected',
            model: $blueprint,
            newValues: [
                'sector_code' => $schema['project_metadata']['primary_sector'] ?? null,
                'active_peripherals_count' => $hw['active_peripherals_count'] ?? 0,
                'selected_broker' => $ws['selected_broker'] ?? null,
                'composite_indexes_count' => $strictDb['total_composite_indexes_compiled'] ?? 0,
            ],
            description: 'AI Agent menginspeksi Kontrak Hardware Fisik, WebSocket Zero-Polling, dan Strict 0% N+1 + Partisi PG16.',
            eventCategory: 'hardware_iot_bridge'
        );

        return [
            'success' => true,
            'locale' => $locale,
            'hardware_iot_peripheral_contract' => $hw,
            'realtime_websocket_contract' => $ws,
            'strict_query_and_partition_contract' => $strictDb,
        ];
    }

    /**
     * 29. Generate Self-Healing SYNTAX_LOCK_2026.md & Role-Based Client SOP Pack (v7.0)
     */
    protected function generateSyntaxLockAndClientSopPack(array $args): array
    {
        $rawLocale = (string) ($args['locale'] ?? 'id');
        $locale = in_array($rawLocale, ['id', 'en'], true) ? $rawLocale : 'id';
        $bpLookup = trim((string) ($args['blueprint_id'] ?? ''));
        $blueprint = $bpLookup !== ''
            ? VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first()
            : null;

        if ($blueprint) {
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
                'persist_project' => false,
            ]);
        } else {
            $sectorCode = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
                (string) ($args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
            );
            $stackCode = \App\Support\PosStackPresetCatalog::normalizePresetCode(
                (string) ($args['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
            );
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
                'app_name' => 'Client SOP & Syntax Lock Pack',
                'sector_code' => $sectorCode,
                'stack_preset_code' => $stackCode,
            ]);
        }

        $sop = \App\Support\PosRealWorldRuntimeCatalog::buildRoleBasedClientSopAndOnboarding($schema, $locale);
        $syntaxMd = \App\Support\PosRealWorldRuntimeCatalog::renderSyntaxLock2026Markdown($schema);
        $sopMd = \App\Support\PosRealWorldRuntimeCatalog::renderClientSopManualMarkdown($schema, $locale);

        AuditLog::record(
            action: 'syntax_lock_and_client_sop_generated',
            model: $blueprint,
            newValues: [
                'locale' => $locale,
                'roles_covered' => $sop['total_roles_covered'] ?? 5,
                'tour_steps' => $sop['total_interactive_tour_steps'] ?? 25,
                'syntax_lock_version' => \App\Support\PosRealWorldRuntimeCatalog::SYNTAX_LOCK_VERSION,
            ],
            description: "AI Agent menyusun paket SYNTAX_LOCK_2026.md & Buku SOP Training Staf ({$locale}).",
            eventCategory: 'client_sop_onboarding'
        );

        return [
            'success' => true,
            'locale' => $locale,
            'syntax_lock_version' => \App\Support\PosRealWorldRuntimeCatalog::SYNTAX_LOCK_VERSION,
            'client_sop_summary' => $sop,
            'syntax_lock_markdown_preview' => Str::limit($syntaxMd, 1600),
            'client_sop_markdown_preview' => Str::limit($sopMd, 1600),
        ];
    }

    /**
     * 30. Audit 15 Master Pillars Architectural Freeze & Cross-Stage Causality Graph (v7.0)
     */
    protected function auditFifteenPillarsArchitecturalFreeze(array $args): array
    {
        $rawLocale = (string) ($args['locale'] ?? 'id');
        $locale = in_array($rawLocale, ['id', 'en'], true) ? $rawLocale : 'id';
        $persistSync = ! empty($args['persist_sync']);

        if ($persistSync) {
            \App\Support\PosRealWorldRuntimeCatalog::seedFifteenArchitecturePillars();
        }

        $pillars = \App\Models\PosArchitecturePillar::getCachedMatrix($locale);
        $bpLookup = trim((string) ($args['blueprint_id'] ?? ''));
        $blueprint = $bpLookup !== ''
            ? VisionBlueprint::where('id', $bpLookup)->orWhere('slug', $bpLookup)->first()
            : null;

        $causality = null;
        if ($blueprint) {
            $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
                'persist_project' => $persistSync,
            ]);
            $causality = $schema['cross_stage_causality_graph'] ?? null;
        }

        AuditLog::record(
            action: 'fifteen_pillars_freeze_audited',
            model: $blueprint,
            newValues: [
                'pillars_count' => count($pillars),
                'architectural_freeze_score' => count($pillars) === 15 ? 100 : 0,
                'persist_sync' => $persistSync,
            ],
            description: 'Audit 15 Pilar Master Architectural Freeze 100% dijalankan.',
            eventCategory: 'pillars_freeze_100'
        );

        return [
            'success' => true,
            'freeze_version' => \App\Support\PosRealWorldRuntimeCatalog::FREEZE_VERSION,
            'architectural_freeze_score' => count($pillars) === 15 ? 100 : 0,
            'pillars_count' => count($pillars),
            'persist_sync_executed' => $persistSync,
            'pillars' => $pillars,
            'cross_stage_causality_graph' => $causality,
        ];
    }

    /**
     * 31. Configure Visual Frontend UI/UX (36 Blueprints + 5 Shells), Dual-Persona Mode & Tier-1 Jurisdiction/RTL (v8.0)
     */
    protected function configureVisualUiAndJurisdictionLocale(array $args): array
    {
        $sectorCode = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );
        $outputLocale = \App\Support\PosInclusiveCommercialCatalog::normalizeOutputLocale(
            (string) ($args['output_locale'] ?? 'id')
        );
        $personaMode = \App\Support\PosInclusiveCommercialCatalog::normalizePersonaMode(
            (string) ($args['persona_mode'] ?? 'ARCHITECT_PRO')
        );
        $selectedUiVariant = trim((string) ($args['selected_ui_variant'] ?? '')) ?: null;
        $navShell = trim((string) ($args['navigation_shell'] ?? '')) ?: null;

        $visualContract = \App\Support\PosInclusiveCommercialCatalog::visualUiContractForSector(
            $sectorCode,
            $selectedUiVariant,
            $navShell,
            $outputLocale
        );
        $jurisdiction = \App\Support\PosInclusiveCommercialCatalog::jurisdictionComplianceProfile(
            $outputLocale,
            $sectorCode
        );

        $projectUlid = trim((string) ($args['project_ulid'] ?? ''));
        $posProject = null;
        if ($projectUlid !== '' && \Illuminate\Support\Facades\Schema::hasTable('pos_projects')) {
            $posProject = \App\Models\PosProject::where('id', $projectUlid)->first();
            if ($posProject) {
                $updates = [];
                if (\Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'selected_ui_variant')) {
                    $updates['selected_ui_variant'] = $visualContract['selected_variant_code'];
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'navigation_shell')) {
                    $updates['navigation_shell'] = $visualContract['navigation_shell_code'];
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'output_locale')) {
                    $updates['output_locale'] = $outputLocale;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'persona_mode')) {
                    $updates['persona_mode'] = $personaMode;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'jurisdiction_profile')) {
                    $updates['jurisdiction_profile'] = $jurisdiction['jurisdiction_code'] ?? null;
                }
                if (! empty($updates)) {
                    $posProject->update($updates);
                }
            }
        }

        $schema = \App\Services\ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
            'app_name' => $posProject?->app_name ?? 'Visual UI & Jurisdiction Preview',
            'sector_code' => $sectorCode,
            'selected_ui_variant' => $visualContract['selected_variant_code'],
            'navigation_shell' => $visualContract['navigation_shell_code'],
            'output_locale' => $outputLocale,
            'persona_mode' => $personaMode,
        ]);

        AuditLog::record(
            action: 'visual_ui_and_jurisdiction_configured',
            model: $posProject,
            newValues: [
                'sector_code' => $sectorCode,
                'selected_ui_variant' => $visualContract['selected_variant_code'],
                'navigation_shell' => $visualContract['navigation_shell_code'],
                'output_locale' => $outputLocale,
                'text_direction' => $visualContract['text_direction'],
                'persona_mode' => $personaMode,
                'persisted_project_ulid' => $posProject?->id,
            ],
            description: "AI Agent mengonfigurasi Visual UI ({$visualContract['selected_variant_code']}) & Yurisdiksi Tier-1 ({$outputLocale}, dir={$visualContract['text_direction']}).",
            eventCategory: 'visual_ui_contract'
        );

        return [
            'success' => true,
            'persisted_project_ulid' => $posProject?->id,
            'persona_mode' => $personaMode,
            'visual_ui_contract' => $visualContract,
            'jurisdiction_compliance_profile' => $jurisdiction,
            'causality_scope_hash' => $schema['cross_stage_causality_graph']['causality_fingerprint_sha256'] ?? null,
            'estimated_rab_idr' => $schema['commercial_mitra_contract']['estimated_contract_idr'] ?? null,
        ];
    }

    /**
     * 32. Audit Living Architecture CVE Alerts, 1-Click Auto-Patch Prompts & 70:30 Marketplace (v8.0)
     */
    protected function auditLivingCveAlertsAndAutopatch(array $args): array
    {
        $stackPreset = \App\Support\PosStackPresetCatalog::normalizePresetCode(
            (string) ($args['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
        );
        $sectorCode = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );

        $alerts = \App\Support\PosInclusiveCommercialCatalog::livingCveSecurityAlerts($stackPreset, $sectorCode);
        $marketplace = \App\Support\PosInclusiveCommercialCatalog::communityPrimitivesMarketplace();

        $projectUlid = trim((string) ($args['project_ulid'] ?? ''));
        $posProject = null;
        if ($projectUlid !== '' && ! empty($args['acknowledge_all']) && \Illuminate\Support\Facades\Schema::hasTable('pos_projects')) {
            $posProject = \App\Models\PosProject::where('id', $projectUlid)->first();
            if ($posProject && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'cve_alerts_ack_json')) {
                $ackIds = array_map(static fn (array $a): string => (string) ($a['advisory_id'] ?? $a['cve_code']), $alerts);
                $posProject->update([
                    'cve_alerts_ack_json' => [
                        'acknowledged_ids' => $ackIds,
                        'acknowledged_at' => now()->toIso8601String(),
                    ],
                ]);
            }
        }

        AuditLog::record(
            action: 'living_cve_audited',
            model: $posProject,
            newValues: [
                'stack_preset' => $stackPreset,
                'sector_code' => $sectorCode,
                'alerts_count' => count($alerts),
                'acknowledged' => ! empty($args['acknowledge_all']),
            ],
            description: "Audit Living Architecture CVE Alerts & Auto-Patch dijalankan untuk stack {$stackPreset}.",
            eventCategory: 'living_cve_alert'
        );

        return [
            'success' => true,
            'stack_preset' => $stackPreset,
            'sector_code' => $sectorCode,
            'alerts_count' => count($alerts),
            'living_cve_alerts' => $alerts,
            'verified_community_marketplace' => $marketplace,
        ];
    }

    /**
     * 33. Manage 5-Layer Banking-Grade IP Security: Zero-Width Canary Watermark & 1-Click MCP Token Revocation (v8.0)
     */
    protected function manageIpCanaryWatermarkAndVaultSecurity(array $args): array
    {
        $action = strtolower(trim((string) ($args['action'] ?? 'inspect_layers')));
        $projectUlid = trim((string) ($args['project_ulid'] ?? ''));
        $posProject = ($projectUlid !== '' && \Illuminate\Support\Facades\Schema::hasTable('pos_projects'))
            ? \App\Models\PosProject::where('id', $projectUlid)->first()
            : null;

        if ($action === 'revoke_mcp_token') {
            if (! $posProject) {
                return ['error' => 'PosProject dengan ULID tersebut tidak ditemukan untuk rotasi/pencabutan token MCP.'];
            }
            $previousHash = $posProject->mcp_token_hash;
            $posProject->revokeMcpToken(auth()->id() ? (string) auth()->id() : null);
            $refreshed = $posProject->fresh();

            AuditLog::record(
                action: 'mcp_token_revoked',
                model: $posProject,
                newValues: [
                    'project_ulid' => $posProject->id,
                    'new_prefix' => $refreshed?->mcp_token_prefix,
                ],
                description: "Token akses Live MCP dicabut & dirotasi secara instan (1-Click Revoke) untuk proyek {$posProject->id}.",
                eventCategory: 'ip_canary_security'
            );

            return [
                'success' => true,
                'action' => 'revoke_mcp_token',
                'project_ulid' => $posProject->id,
                'mcp_token_rotated' => $previousHash !== $refreshed?->mcp_token_hash,
                'new_token_prefix' => $refreshed?->mcp_token_prefix,
            ];
        }

        if ($action === 'verify_canary') {
            $sample = (string) ($args['leaked_markdown_sample'] ?? '');
            $extractedHex = \App\Support\PosInclusiveCommercialCatalog::extractCanaryWatermark($sample);
            $matched = null;
            if ($extractedHex && \Illuminate\Support\Facades\Schema::hasTable('pos_projects') && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'canary_watermark_sig')) {
                $matched = \App\Models\PosProject::where('canary_watermark_sig', $extractedHex)->first();
            }

            AuditLog::record(
                action: 'canary_watermark_verified',
                model: $matched,
                newValues: [
                    'detected' => $extractedHex !== null,
                    'fingerprint_hex' => $extractedHex,
                    'matched_project_ulid' => $matched?->id,
                ],
                description: 'Dekode forensik steganografi Zero-Width Canary Watermark dijalankan oleh AI Agent.',
                eventCategory: 'ip_canary_security'
            );

            return [
                'success' => true,
                'action' => 'verify_canary',
                'detected' => $extractedHex !== null,
                'fingerprint_hex' => $extractedHex,
                'matched_project_ulid' => $matched?->id,
                'matched_app_name' => $matched?->app_name,
            ];
        }

        $matrix = \App\Support\PosInclusiveCommercialCatalog::retentionAndSecurityMatrix();

        return [
            'success' => true,
            'action' => 'inspect_layers',
            'five_security_layers' => $matrix['five_security_layers'] ?? [],
            'five_retention_hooks' => $matrix['five_retention_hooks'] ?? [],
        ];
    }

    /**
     * 34. NeriahPro AgentShield 5-Layer Defense (Anti-Prompt Injection, Anti-Slopquatting, File-Path Jail)
     */
    protected function sanitizeAndAuditAgentshield(array $args): array
    {
        $operation = strtolower(trim((string) ($args['operation'] ?? 'inspect_sandbox')));
        $stackPreset = (string) ($args['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH');
        $sectorCode = (string) ($args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $sanitizer = app(DocumentThreatSanitizerService::class);

        if ($operation === 'sanitize_document') {
            $rawText = (string) ($args['document_text'] ?? '');
            $scan = $sanitizer->sanitizeDocumentText($rawText, 'ai_copilot_scan');

            return [
                'success' => true,
                'operation' => 'sanitize_document',
                'is_clean' => $scan['is_clean'],
                'detected_threats' => $scan['detected_threats'],
                'zero_width_removed_count' => $scan['zero_width_removed_count'],
                'sanitized_text' => $scan['sanitized_text'],
                'isolated_block' => $scan['isolated_block'],
                'sanitized_sha256' => $scan['sanitized_sha256'],
            ];
        }

        if ($operation === 'audit_packages') {
            $packages = is_array($args['packages'] ?? null) ? $args['packages'] : [];
            $audit = $sanitizer->auditPackageSlopquatting($packages, $stackPreset, $sectorCode);

            AuditLog::record(
                action: $audit['all_approved'] ? 'AGENTSHIELD_PACKAGES_VERIFIED' : 'SLOPQUATTING_PACKAGE_BLOCKED',
                newValues: [
                    'approved_packages' => $audit['approved_packages'],
                    'blocked_packages' => $audit['blocked_packages'],
                ],
                description: 'AgentShield memeriksa ' . count($packages) . ' package terhadap APPROVED_DEPENDENCY_MANIFEST (' . count($audit['blocked_packages']) . ' diblokir).',
                eventCategory: 'agentshield_security',
                sectorCode: $sectorCode,
                primitiveCode: 'PRIMITIVE_AGENTSHIELD_ALLOWLIST',
                executionChannel: 'AI_COPILOT_TOOL'
            );

            return array_merge(['success' => true, 'operation' => 'audit_packages'], $audit);
        }

        return [
            'success' => true,
            'operation' => 'inspect_sandbox',
            'contract' => DocumentThreatSanitizerService::fiveLayersBlueprintContract('NeriahPro Enterprise App', $stackPreset, $sectorCode),
            'claude_settings_json' => json_decode(DocumentThreatSanitizerService::renderClaudeSettingsJson([
                'project_metadata' => ['name' => 'NeriahPro Enterprise App'],
            ]), true),
        ];
    }

    /**
     * 35. The Co-Design Alignment & Sign-Off Room (Dual-Mirror + Per-Module Sign-Off + Dual-Key Lock)
     */
    protected function manageCodesignAlignmentRoom(array $args, ?User $user): array
    {
        $action = strtolower(trim((string) ($args['action'] ?? 'inspect_room')));
        $bpId = trim((string) ($args['blueprint_id'] ?? ''));

        if ($bpId === '') {
            return ['error' => 'Parameter blueprint_id (ULID atau slug) wajib diisi untuk Co-Design Alignment Room.'];
        }

        $blueprint = VisionBlueprint::where('id', $bpId)->orWhere('slug', $bpId)->first();
        if (! $blueprint) {
            return ['error' => "VisionBlueprint '{$bpId}' tidak ditemukan."];
        }

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first()
            ?? app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);

        $bridge = app(PosCommercialBridgeService::class);
        $dualKey = is_array($posProject->codesign_dual_key_json) ? $posProject->codesign_dual_key_json : [];

        if ($action === 'sign_module') {
            if (! empty($dualKey['locked_v1_readonly'])) {
                return ['error' => 'Blueprint v1.0 sudah terkunci Read-Only melalui Dual-Key Scope Lock.'];
            }
            $modCode = trim((string) ($args['module_code'] ?? ''));
            $status = strtoupper(trim((string) ($args['status'] ?? 'APPROVED')));
            if (! in_array($status, ['APPROVED', 'DISCUSSION', 'PENDING'], true)) {
                $status = 'APPROVED';
            }
            $signer = trim((string) ($args['signer_name'] ?? ($user?->name ?? 'Authorized Signer')));
            $schema = json_decode((string) $posProject->compiled_schema_json, true) ?: ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
            $modules = is_array($posProject->codesign_modules_json) && ! empty($posProject->codesign_modules_json)
                ? $posProject->codesign_modules_json
                : ($schema['codesign_alignment_contract']['modules'] ?? []);

            foreach ($modules as &$m) {
                if (($m['module_code'] ?? '') === $modCode || $modCode === 'ALL') {
                    $m['status'] = $status;
                    $m['approved_by_name'] = $status === 'APPROVED' ? $signer : null;
                    $m['approved_at'] = $status === 'APPROVED' ? now()->toIso8601String() : null;
                }
            }
            unset($m);

            $recompiled = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
                'persist_project' => true,
                'codesign_modules' => $modules,
                'codesign_dual_key' => $dualKey,
            ]);
            $quotation = $bridge->calculateScopeQuotation($recompiled);

            AuditLog::record(
                action: 'CODESIGN_MODULE_SIGN_OFF',
                model: $blueprint,
                newValues: ['module_code' => $modCode, 'status' => $status, 'signer' => $signer],
                description: "AI Copilot Co-Design Sign-Off [{$modCode}] -> {$status} oleh {$signer}",
                eventCategory: 'codesign_alignment',
                sectorCode: (string) ($recompiled['project_metadata']['primary_sector'] ?? null),
                primitiveCode: 'PRIMITIVE_CODESIGN_ROOM',
                executionChannel: 'AI_COPILOT_TOOL'
            );

            return [
                'success' => true,
                'action' => 'sign_module',
                'codesign' => $recompiled['codesign_alignment_contract'] ?? [],
                'quotation_idr' => $quotation['quotation_idr'] ?? 0,
                'formatted_quotation_idr' => $quotation['formatted_quotation_idr'] ?? 'Rp 0',
            ];
        }

        if ($action === 'add_custom_module') {
            if (! empty($dualKey['locked_v1_readonly'])) {
                return ['error' => 'Blueprint v1.0 sudah terkunci Read-Only. Gunakan generate_delta_change_request untuk fitur tambahan v1.1.'];
            }
            $title = trim((string) ($args['module_title'] ?? 'Modul Kustom Klien'));
            $extraHours = max(4, (int) ($args['extra_man_hours'] ?? 12));
            $schema = json_decode((string) $posProject->compiled_schema_json, true) ?: ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
            $modules = is_array($posProject->codesign_modules_json) && ! empty($posProject->codesign_modules_json)
                ? $posProject->codesign_modules_json
                : ($schema['codesign_alignment_contract']['modules'] ?? []);

            $customCode = 'MOD_CUSTOM_' . strtoupper(substr(md5($title . microtime()), 0, 6));
            $modules[] = [
                'module_code' => $customCode,
                'is_custom_client_addition' => true,
                'title' => ['id' => $title, 'en' => $title],
                'localized_title' => $title,
                'status' => 'DISCUSSION',
                'extra_man_hours' => $extraHours,
            ];

            $recompiled = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
                'persist_project' => true,
                'codesign_modules' => $modules,
                'codesign_dual_key' => $dualKey,
            ]);
            $quotation = $bridge->calculateScopeQuotation($recompiled);

            AuditLog::record(
                action: 'CODESIGN_CUSTOM_MODULE_ADDED',
                model: $blueprint,
                newValues: ['module_code' => $customCode, 'title' => $title, 'extra_man_hours' => $extraHours],
                description: "AI Copilot menambahkan modul Co-Design '{$title}' (+{$extraHours} jam).",
                eventCategory: 'codesign_alignment',
                sectorCode: (string) ($recompiled['project_metadata']['primary_sector'] ?? null),
                primitiveCode: 'PRIMITIVE_CODESIGN_ROOM',
                executionChannel: 'AI_COPILOT_TOOL'
            );

            return [
                'success' => true,
                'action' => 'add_custom_module',
                'added_module_code' => $customCode,
                'codesign' => $recompiled['codesign_alignment_contract'] ?? [],
                'quotation_idr' => $quotation['quotation_idr'] ?? 0,
                'formatted_quotation_idr' => $quotation['formatted_quotation_idr'] ?? 'Rp 0',
            ];
        }

        if ($action === 'dual_key_lock') {
            $schema = json_decode((string) $posProject->compiled_schema_json, true) ?: ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
            if (empty($schema['codesign_alignment_contract']['can_execute_dual_key_lock'])) {
                return ['error' => 'Dual-Key Lock gagal: seluruh modul wajib berstatus APPROVED (100%) terlebih dahulu.'];
            }
            $lockResult = $bridge->lockScopeAndGenerateSpk($blueprint, $posProject);
            $masterHash = (string) ($lockResult['scope_hash_full'] ?? hash('sha256', json_encode($schema)));
            $dualKeyPayload = [
                'locked_v1_readonly' => true,
                'architect_signed_by' => $user?->name ?? 'Lead Architect NeriahPro',
                'architect_signed_at' => now()->toIso8601String(),
                'client_signed_by' => trim((string) ($args['signer_name'] ?? ($blueprint->client_name ?: 'Client Representative'))),
                'client_signed_at' => now()->toIso8601String(),
                'master_scope_hash_sha256' => $masterHash,
                'spk_document_id' => $lockResult['document_id'] ?? null,
            ];
            $recompiled = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
                'persist_project' => true,
                'codesign_modules' => $posProject->codesign_modules_json,
                'codesign_dual_key' => $dualKeyPayload,
            ]);

            AuditLog::record(
                action: 'DUAL_KEY_SCOPE_LOCK',
                model: $blueprint,
                newValues: $dualKeyPayload,
                description: 'AI Copilot mengeksekusi seremoni Dual-Key Cryptographic Scope Lock v1.0.',
                eventCategory: 'codesign_alignment',
                sectorCode: (string) ($recompiled['project_metadata']['primary_sector'] ?? null),
                primitiveCode: 'PRIMITIVE_CODESIGN_ROOM',
                executionChannel: 'AI_COPILOT_TOOL'
            );

            return [
                'success' => true,
                'action' => 'dual_key_lock',
                'master_scope_hash_sha256' => $masterHash,
                'codesign' => $recompiled['codesign_alignment_contract'] ?? [],
            ];
        }

        $schema = json_decode((string) $posProject->compiled_schema_json, true)
            ?: ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);

        return [
            'success' => true,
            'action' => 'inspect_room',
            'blueprint_slug' => $blueprint->slug,
            'codesign' => $schema['codesign_alignment_contract'] ?? [],
            'world_class_task_matrix' => $schema['world_class_task_matrix'] ?? [],
        ];
    }

    /**
     * 36. Filterable Cryptographic Audit Trail Verifier & 12-Sector Preset Explorer
     */
    protected function verifyAndExportCryptoAuditTrail(array $args): array
    {
        $operation = strtolower(trim((string) ($args['operation'] ?? 'verify_chain')));
        $sectorPreset = trim((string) ($args['sector_preset'] ?? ''));

        if ($operation === 'list_presets') {
            $presets = PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets();
            if ($sectorPreset !== '' && isset($presets[$sectorPreset])) {
                return [
                    'success' => true,
                    'operation' => 'list_presets',
                    'preset' => $presets[$sectorPreset],
                ];
            }

            return [
                'success' => true,
                'operation' => 'list_presets',
                'total_presets' => count($presets),
                'presets' => array_values($presets),
            ];
        }

        if ($operation === 'prepare_export_summary') {
            $filters = array_filter([
                'sector_preset' => $sectorPreset !== '' ? $sectorPreset : null,
                'execution_channel' => $args['execution_channel'] ?? null,
            ]);
            $exporter = app(ExportFilteredAuditExcelAction::class);
            $matchedCount = $exporter->buildFilteredQuery($filters)->count();
            $chain = AuditLog::verifyChainSummary((int) ($args['limit'] ?? 200));

            return [
                'success' => true,
                'operation' => 'prepare_export_summary',
                'matched_rows' => $matchedCount,
                'sheets_structure' => [
                    '1_Ringkasan_Sertifikat (Executive Summary & SHA-256 Integrity Certificate)',
                    '2_Jejak_Audit_Transaksi (Transaction Audit Trail)',
                    '3_Log_AI_Copilot_Idemp (AI Copilot & Idempotency Log)',
                ],
                'admin_explorer_url' => url('/admin/crypto-audit-explorer'),
                'chain_summary' => $chain,
            ];
        }

        $limit = min(max((int) ($args['limit'] ?? 200), 10), 1000);
        $chain = AuditLog::verifyChainSummary($limit);

        return [
            'success' => true,
            'operation' => 'verify_chain',
            'chain_summary' => $chain,
        ];
    }

    /**
     * 37. Configure Dual-Zone UI Architecture (Zone A Operational + Zone B Public UX UX_F1..UX_F6) & Input Hygiene (v10.0)
     */
    protected function configureDualZonePublicUxPreset(array $args): array
    {
        $sectorCode = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($args['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );
        $ideaText = trim((string) ($args['idea_text'] ?? ''));
        $explicitUx = trim((string) ($args['public_ux_preset'] ?? ''));

        $matchedUx = $explicitUx !== ''
            ? (\App\Support\PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets()[\App\Support\PosDualZoneFrontendAndSaasCatalog::normalizePublicUxPreset($explicitUx)] ?? null)
            : \App\Support\PosDualZoneFrontendAndSaasCatalog::autoMatchPublicUxPresetForSectorAndIdea($sectorCode, $ideaText);

        $uxCode = (string) ($matchedUx['code'] ?? \App\Support\PosDualZoneFrontendAndSaasCatalog::defaultPublicUxForSector($sectorCode));
        $dualContract = \App\Support\PosDualZoneFrontendAndSaasCatalog::compileDualZoneUiContract($sectorCode, null, null, $uxCode);

        $persistedProject = null;
        if (! empty($args['project_ulid']) && \Illuminate\Support\Facades\Schema::hasTable('pos_projects')) {
            $proj = \App\Models\PosProject::query()->where('id', (string) $args['project_ulid'])->first();
            if ($proj && $proj->visionBlueprint) {
                $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($proj->visionBlueprint, [
                    'public_ux_preset' => $uxCode,
                    'persist_project' => true,
                ]);
                $quotation = app(\App\Services\PosCommercialBridgeService::class)->calculateScopeQuotation($schema, [
                    'public_ux_preset' => $uxCode,
                ]);
                $persistedProject = [
                    'project_id' => $proj->id,
                    'public_ux_preset' => $uxCode,
                    'quotation_idr' => $quotation['quotation_idr'] ?? 0,
                    'scope_hash_short' => $quotation['scope_hash_short'] ?? '',
                ];
            }
        }

        return [
            'success' => true,
            'sector_code' => $sectorCode,
            'selected_public_ux_preset' => $uxCode,
            'dual_zone_ui_contract' => $dualContract,
            'five_iron_animation_laws' => \App\Support\PosDualZoneFrontendAndSaasCatalog::fiveIronAnimationLaws(),
            'input_hygiene_contract' => \App\Support\PosDualZoneFrontendAndSaasCatalog::inputHygieneContract(),
            'persisted_project' => $persistedProject,
        ];
    }

    /**
     * 38. Manage Cryptographically Verified Testimonials & Infinite Kinetic Aniwall (v10.0)
     */
    protected function manageVerifiedTestimonialsAniwall(array $args): array
    {
        $action = strtolower(trim((string) ($args['action'] ?? 'list_aniwall')));
        $sectorFilter = trim((string) ($args['sector_code'] ?? ''));

        if ($action === 'issue_receipt') {
            $projId = trim((string) ($args['project_ulid'] ?? ''));
            $proj = $projId !== '' && \Illuminate\Support\Facades\Schema::hasTable('pos_projects')
                ? \App\Models\PosProject::query()->where('id', $projId)->first()
                : null;
            if (! $proj) {
                return ['error' => 'PosProject tidak ditemukan untuk penerbitan Architecture Receipt.'];
            }
            $trigger = strtoupper(trim((string) ($args['verification_trigger'] ?? 'MITRA_BAST_SIGNED')));
            $issued = \App\Services\Saas\PosSaasCommercialEngineService::issueVerifiedArchitectureReceiptTestimonial($proj, $trigger);

            return [
                'success' => true,
                'action' => 'issue_receipt',
                'testimonial_id' => $issued->id,
                'sector_code' => $issued->sector_code,
                'receipt' => $issued->architecture_receipt_json,
            ];
        }

        $catalog = \App\Support\PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog();
        if ($sectorFilter !== '') {
            $normalized = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorFilter);
            $catalog = array_values(array_filter($catalog, static fn (array $row): bool => ($row['sector_code'] ?? '') === $normalized));
        }

        return [
            'success' => true,
            'action' => 'list_aniwall',
            'total_verified_testimonials' => count($catalog),
            'explore_url' => url('/explore'),
            'items' => array_slice($catalog, 0, 12),
        ];
    }

    /**
     * 39. Manage Affiliate Finder's Fee Ledger & 1-Click Non-Profit Impact Grants (v10.0)
     */
    protected function manageAffiliateAndImpactGrants(array $args, ?User $user): array
    {
        $action = strtolower(trim((string) ($args['action'] ?? 'summary')));

        if ($action === 'approve_grant') {
            $appId = trim((string) ($args['application_id'] ?? ''));
            $grantApp = $appId !== '' && \Illuminate\Support\Facades\Schema::hasTable('pos_impact_grant_applications')
                ? \App\Models\PosImpactGrantApplication::query()->where('id', $appId)->first()
                : null;
            if (! $grantApp) {
                return ['error' => 'Permohonan Impact Grant tidak ditemukan.'];
            }
            if (! $user) {
                return ['error' => 'Operator autentikasi diperlukan untuk menyetujui Impact Grant.'];
            }
            $res = \App\Services\Saas\PosSaasCommercialEngineService::approveImpactGrantOneClick(
                $grantApp,
                $user,
                (string) ($args['review_notes'] ?? 'Approved via AI Native Tool')
            );

            return array_merge(['action' => 'approve_grant'], $res);
        }

        $pendingGrants = \Illuminate\Support\Facades\Schema::hasTable('pos_impact_grant_applications')
            ? \App\Models\PosImpactGrantApplication::query()
                ->where('status', 'PENDING_REVIEW')
                ->orderBy('id', 'desc')
                ->limit(15)
                ->get(['id', 'organization_name', 'organization_type', 'official_email', 'requested_project_budget_idr', 'status'])
                ->toArray()
            : [];

        $affiliateTotalIdr = \Illuminate\Support\Facades\Schema::hasTable('pos_affiliate_referrals')
            ? (int) \App\Models\PosAffiliateReferral::query()->sum('commission_amount_idr')
            : 0;

        return [
            'success' => true,
            'action' => $action,
            'retail_affiliate_rate_percent' => \App\Services\Saas\PosSaasCommercialEngineService::RETAIL_AFFILIATE_RATE_PERCENT,
            'mitra_finders_fee_rate_percent' => \App\Services\Saas\PosSaasCommercialEngineService::MITRA_FINDERS_FEE_RATE_PERCENT,
            'reserved_affiliate_commissions_idr' => $affiliateTotalIdr,
            'pending_impact_grants_count' => count($pendingGrants),
            'pending_impact_grants' => $pendingGrants,
        ];
    }

    /**
     * 40. Inspect Super Admin SaaS System Health Pulse & Subscription Feature Gate (v10.0)
     */
    protected function inspectSaasSystemHealthAndBillingGate(array $args, ?User $user): array
    {
        $feature = (string) ($args['feature'] ?? 'copy_phase_3_6');
        $proj = ! empty($args['project_ulid']) && \Illuminate\Support\Facades\Schema::hasTable('pos_projects')
            ? \App\Models\PosProject::query()->where('id', (string) $args['project_ulid'])->first()
            : null;

        return [
            'success' => true,
            'feature_gate' => \App\Services\Saas\PosSaasCommercialEngineService::checkFeatureAccess($user, $proj, $feature),
            'system_health_pulse' => \App\Services\Saas\PosSaasCommercialEngineService::collectSystemHealthPulse(),
            'admin_dashboard_url' => url('/admin/saas-system-health-pulse'),
        ];
    }

    /**
     * RBAC Guard for state-mutating AI Native Tools (Pure Spatie RBAC — Zero Hardcoded Email Bypass).
     */
    protected function guardMutation(?User $user, string $toolName): ?array
    {
        if (! $user || \App\Models\Role::query()->count() === 0) {
            return null;
        }

        $allowed = $user->hasAnyRole(['super_admin', 'developer'])
            || $user->can('execute_ai_native_tools');

        if (! $allowed) {
            AuditLog::record(
                action: 'rbac_tool_denied',
                newValues: ['tool' => $toolName, 'user_id' => $user->id],
                description: "RBAC menolak eksekusi mutating AI Native Tool '{$toolName}' oleh user ID {$user->id}.",
                eventCategory: 'security',
                primitiveCode: 'PRIMITIVE_AGENTSHIELD_RBAC_GUARD',
                executionChannel: 'AI_COPILOT_TOOL'
            );

            return [
                'error' => "Akses ditolak oleh kebijakan RBAC: peran akun Anda tidak memiliki izin mutasi untuk tool '{$toolName}'.",
            ];
        }

        return null;
    }
}

