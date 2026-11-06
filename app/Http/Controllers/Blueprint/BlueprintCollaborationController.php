<?php

namespace App\Http\Controllers\Blueprint;

use App\Http\Controllers\Blueprint\Concerns\AuthorizesBlueprintAccess;
use App\Http\Controllers\Controller;
use App\Mail\BlueprintReadyNotificationMail;
use App\Models\AuditLog;
use App\Models\BlueprintVoucher;
use App\Models\CmsGlobalSetting;
use App\Models\Document;
use App\Models\LeadContact;
use App\Models\PosChangeRequest;
use App\Models\PosImpactGrantApplication;
use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDiscoveryService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintKpiCascadeService;
use App\Services\BlueprintRefinementService;
use App\Services\BlueprintSlopScannerService;
use App\Services\BrownfieldMigrationAuditorService;
use App\Services\Legal\LegalDocumentVaultService;
use App\Services\MidtransSnapService;
use App\Services\PosCommercialBridgeService;
use App\Services\PrdGeneratorService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Services\TopologicalPromptChunker;
use App\Support\ContractLegalHelper;
use App\Support\InputHygieneStandard;
use App\Support\PosAgentShieldAndCoDesignCatalog;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosRealWorldRuntimeCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\PricingRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlueprintCollaborationController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Update and elaborate technical tasks and features for a blueprint proposal.
     * Guarded against scope lock (only allowed before contract is signed).
     */
    public function updateTasks(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // 1. Guard against Scope Freeze (locked document)
        $isScopeLocked = (bool) $blueprint->signed_agreement || $blueprint->documents()->where('scope_locked', true)->exists();
        if ($isScopeLocked) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen PRD telah ditandatangani dan terkunci secara digital (Scope Freeze). Setiap perubahan atau penambahan task baru harus diajukan melalui Addendum / Change Request (CR) resmi.',
            ], 403);
        }

        $request->validate([
            'mvp_tasks' => 'required|array|min:1',
            'mvp_tasks.*.title' => 'required|string|max:255',
            'mvp_tasks.*.desc' => 'nullable|string|max:1000',
            'mvp_tasks.*.category' => 'nullable|string|max:100',
            'mvp_tasks.*.sprint_phase' => 'nullable|string|max:50',
            'phase2_tasks' => 'nullable|array',
            'phase2_tasks.*.title' => 'required|string|max:255',
            'phase2_tasks.*.desc' => 'nullable|string|max:1000',
            'phase2_tasks.*.category' => 'nullable|string|max:100',
            'phase2_tasks.*.sprint_phase' => 'nullable|string|max:50',
        ]);

        $mvpTasks = $request->input('mvp_tasks', []);
        $phase2Tasks = $request->input('phase2_tasks', []);

        // Format into normalized text lines for database columns
        $fiturWajibLines = [];
        foreach ($mvpTasks as $idx => $t) {
            $title = trim($t['title'] ?? '');
            $desc = trim($t['desc'] ?? '');
            $fiturWajibLines[] = $title.(! empty($desc) ? ' - '.$desc : '');
        }

        $fiturTambahanLines = [];
        foreach ($phase2Tasks as $idx => $t) {
            $title = trim($t['title'] ?? '');
            $desc = trim($t['desc'] ?? '');
            $fiturTambahanLines[] = $title.(! empty($desc) ? ' - '.$desc : '');
        }

        $blueprint->fitur_wajib = implode("\n", $fiturWajibLines);
        $blueprint->fitur_tambahan = implode("\n", $fiturTambahanLines);
        $blueprint->save();

        // Regenerate complete Ultimate PRD with deep vertical slice engineering specs, BDD, diagrams, and costs
        $prd = $blueprint->generateAndSavePrd();
        $blueprint->refresh();
        $blueprint->document_sha256 = $blueprint->calculatePrdHash();
        $blueprint->save();

        $itemized = $prd['itemized_cost_breakdown'] ?? PrdGeneratorService::calculateItemizedEstimation($blueprint);

        return response()->json([
            'success' => true,
            'message' => 'Daftar task & spesifikasi fitur berhasil dielaborasi dan disimpan!',
            'document_sha256' => $blueprint->document_sha256,
            'mvp_count' => count($mvpTasks),
            'phase2_count' => count($phase2Tasks),
            'itemized_summary' => [
                'base_total' => $itemized['base_contract_amount'] ?? 0,
                'items_count' => count($itemized['items'] ?? []),
            ],
        ]);
    }

    /**
     * Update and persist developer/architect execution checkpoints for a blueprint.
     * Synchronizes live progress with the customer dashboard.
     */
    public function updateCheckpoint(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'step_key' => 'required|string|max:100',
            'is_completed' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $stepKey = $validated['step_key'];
        $isCompleted = (bool) $validated['is_completed'];
        $notes = $validated['notes'] ?? null;

        $metadata = $blueprint->user_metadata ?? [];
        $checkpoints = $metadata['dev_checkpoints'] ?? [];

        $checkpoints[$stepKey] = [
            'completed' => $isCompleted,
            'completed_at' => $isCompleted ? now()->toIso8601String() : null,
            'notes' => $notes,
            'updated_by' => auth()->user()?->name ?? 'Lead Architect / AI Agent',
        ];

        $metadata['dev_checkpoints'] = $checkpoints;

        // Calculate progress percentage based on total steps
        $totalSteps = 6;
        $completedCount = count(array_filter($checkpoints, fn ($c) => ! empty($c['completed'])));
        $progressPercent = min(100, (int) round(($completedCount / max(1, $totalSteps)) * 100));

        $metadata['dev_progress_percent'] = $progressPercent;
        $metadata['dev_last_checkpoint_at'] = now()->toIso8601String();

        $blueprint->user_metadata = $metadata;
        $blueprint->save();

        return response()->json([
            'success' => true,
            'message' => "Checkpoint {$stepKey} berhasil disinkronkan ke database dan timeline pelanggan.",
            'step_key' => $stepKey,
            'is_completed' => $isCompleted,
            'completed_count' => $completedCount,
            'progress_percent' => $progressPercent,
            'updated_at' => now()->format('d M Y, H:i').' WIB',
        ]);
    }

    /**
     * Real-time Live Presence & Collaborative Cursor Heartbeat.
     * Records collaborator coordinates, active section, and role into cache.
     */
    public function updatePresence(Request $request, string $slug): JsonResponse
    {
        try {
            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            if (!$blueprint) {
                return response()->json([
                    'success' => false,
                    'slug' => $slug,
                    'collaborators' => [],
                    'timestamp' => now()->timestamp,
                ], 200);
            }

            $clientId = $request->input('client_id') ?: session()->getId();
            $isArchitect = auth()->user()?->isSuperAdmin() || (auth()->user() && str_contains(auth()->user()->email, 'neriahpro'));
            $defaultName = auth()->check() ? auth()->user()->name : 'Tamu / Reviewer';
            $name = $request->input('name') ?: $defaultName;
            $role = $request->input('role') ?: ($isArchitect ? 'Lead Architect (Neriah Pro)' : (auth()->check() ? 'Klien / Stakeholder' : 'Tamu'));
            $x = (float) $request->input('x', 0);
            $y = (float) $request->input('y', 0);
            $section = $request->input('section', 'Section 01');

            $cacheKey = "blueprint_presence_{$blueprint->slug}";
            $presenceList = Cache::get($cacheKey, []);

            // Update current collaborator's state
            $presenceList[$clientId] = [
                'id' => $clientId,
                'name' => $name,
                'role' => $role,
                'x' => $x,
                'y' => $y,
                'section' => $section,
                'is_architect' => auth()->user()?->isSuperAdmin() || str_contains($role, 'Architect'),
                'last_seen' => now()->timestamp,
            ];

            // Purge expired participants (inactive > 12 seconds)
            $now = now()->timestamp;
            foreach ($presenceList as $id => $p) {
                if (($now - ($p['last_seen'] ?? 0)) > 12) {
                    unset($presenceList[$id]);
                }
            }

            Cache::put($cacheKey, $presenceList, now()->addMinutes(5));

            return response()->json([
                'success' => true,
                'slug' => $blueprint->slug,
                'collaborators' => array_values($presenceList),
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'slug' => $slug,
                'collaborators' => [],
                'timestamp' => now()->timestamp,
            ], 200);
        }
    }

    /**
     * Get active collaborators on this blueprint.
     */
    public function getPresence(string $slug): JsonResponse
    {
        try {
            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            if (!$blueprint) {
                return response()->json([
                    'success' => false,
                    'slug' => $slug,
                    'collaborators' => [],
                    'timestamp' => now()->timestamp,
                ], 200);
            }

            $cacheKey = "blueprint_presence_{$blueprint->slug}";
            $presenceList = Cache::get($cacheKey, []);

            $now = now()->timestamp;
            foreach ($presenceList as $id => $p) {
                if (($now - ($p['last_seen'] ?? 0)) > 12) {
                    unset($presenceList[$id]);
                }
            }

            return response()->json([
                'success' => true,
                'slug' => $blueprint->slug,
                'collaborators' => array_values($presenceList),
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'slug' => $slug,
                'collaborators' => [],
                'timestamp' => now()->timestamp,
            ], 200);
        }
    }

    /**
     * Project OS v9.0 — Co-Design Alignment Room: Per-Module Clause Sign-Off (Client / Architect).
     */
    public function codesignSignModule(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'module_code' => 'required|string|max:80',
            'status' => 'required|string|in:APPROVED,DISCUSSION,PENDING',
            'signer_name' => 'nullable|string|max:160',
            'extra_man_hours' => 'nullable|integer|min:0|max:400',
        ]);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first()
            ?? app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);

        $dualKey = is_array($posProject->codesign_dual_key_json) ? $posProject->codesign_dual_key_json : [];
        if (! empty($dualKey['locked_v1_readonly'])) {
            return response()->json([
                'success' => false,
                'message' => 'Blueprint v1.0 telah dikunci secara kriptografis (Read-Only Dual-Key Lock). Perubahan hanya dapat dilakukan melalui Paid Change Request v1.1.',
            ], 422);
        }

        $schema = ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $existingModules = is_array($posProject->codesign_modules_json) && ! empty($posProject->codesign_modules_json)
            ? $posProject->codesign_modules_json
            : ($schema['codesign_alignment_contract']['modules'] ?? []);

        $signerName = ($validated['signer_name'] ?? null) ?: (auth()->user()?->name ?: ($blueprint->client_name ?: 'Client Representative'));
        $found = false;
        foreach ($existingModules as &$mod) {
            if (($mod['module_code'] ?? '') === $validated['module_code']) {
                $mod['status'] = $validated['status'];
                if (array_key_exists('extra_man_hours', $validated) && $validated['extra_man_hours'] !== null) {
                    $mod['extra_man_hours'] = (int) $validated['extra_man_hours'];
                }
                if ($validated['status'] === 'APPROVED') {
                    $mod['approved_by_name'] = $signerName;
                    $mod['approved_by_ip'] = $request->ip();
                    $mod['approved_at'] = now()->toIso8601String();
                } else {
                    $mod['approved_by_name'] = null;
                    $mod['approved_by_ip'] = null;
                    $mod['approved_at'] = null;
                }
                $found = true;
                break;
            }
        }
        unset($mod);

        if (! $found) {
            return response()->json([
                'success' => false,
                'message' => "Modul '{$validated['module_code']}' tidak ditemukan dalam Co-Design Room.",
            ], 404);
        }

        $recompiledSchema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
            'persist_project' => true,
            'codesign_modules' => $existingModules,
            'codesign_dual_key' => $dualKey,
        ]);
        $quotation = $bridge->calculateScopeQuotation($recompiledSchema);
        if (Schema::hasColumn('pos_projects', 'commercial_quotation_json')) {
            $posProject->refresh();
            $posProject->forceFill(['commercial_quotation_json' => $quotation])->saveQuietly();
        }

        AuditLog::record(
            action: 'CODESIGN_MODULE_SIGN_OFF',
            model: $blueprint,
            newValues: [
                'module_code' => $validated['module_code'],
                'status' => $validated['status'],
                'signer_name' => $signerName,
                'signer_ip' => $request->ip(),
                'approval_progress_percent' => $recompiledSchema['codesign_alignment_contract']['approval_progress_percent'] ?? 0,
                'scope_hash_short' => $quotation['scope_hash_short'] ?? '',
            ],
            description: "Co-Design Sign-Off Modul [{$validated['module_code']}] -> {$validated['status']} oleh {$signerName} ({$request->ip()})",
            eventCategory: 'codesign_alignment',
            sectorCode: (string) ($recompiledSchema['project_metadata']['primary_sector'] ?? null),
            primitiveCode: 'PRIMITIVE_CODESIGN_ROOM',
            executionChannel: 'WEB_UI'
        );

        return response()->json([
            'success' => true,
            'codesign' => $recompiledSchema['codesign_alignment_contract'] ?? [],
            'quotation' => $quotation,
            'causality' => $recompiledSchema['cross_stage_causality_graph'] ?? [],
        ]);
    }

    /**
     * Project OS v9.0 — Co-Design Alignment Room: Contextual Annotation OR Add Custom Client Requirement (+ Live RAB Impact).
     */
    public function codesignAnnotate(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'add_custom_module' => 'nullable|boolean',
            'module_code' => 'nullable|string|max:80',
            'module_title' => 'nullable|string|max:180',
            'note' => 'nullable|string|max:2000',
            'author_name' => 'nullable|string|max:160',
            'author_role' => 'nullable|string|max:64',
            'extra_man_hours' => 'nullable|integer|min:0|max:400',
            'widgets' => 'nullable|array',
            'widgets.*' => 'string|max:120',
            'ulid_tables' => 'nullable|array',
            'ulid_tables.*' => 'string|max:80',
        ]);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first()
            ?? app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);

        $dualKey = is_array($posProject->codesign_dual_key_json) ? $posProject->codesign_dual_key_json : [];
        if (! empty($dualKey['locked_v1_readonly'])) {
            return response()->json([
                'success' => false,
                'message' => 'Blueprint v1.0 sudah terkunci Read-Only. Silakan ajukan Paid Change Request v1.1 untuk penambahan fitur baru.',
            ], 422);
        }

        $schema = ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $existingModules = is_array($posProject->codesign_modules_json) && ! empty($posProject->codesign_modules_json)
            ? $posProject->codesign_modules_json
            : ($schema['codesign_alignment_contract']['modules'] ?? []);

        $authorName = ($validated['author_name'] ?? null) ?: (auth()->user()?->name ?: ($blueprint->client_name ?: 'Collaborator'));
        $authorRole = ($validated['author_role'] ?? null) ?: (auth()->user()?->isSuperAdmin() ? 'Lead Architect' : 'Client Partner');

        if (! empty($validated['add_custom_module'])) {
            $titleClean = trim((string) ($validated['module_title'] ?? 'Modul Tambahan Klien'));
            $customCode = 'MOD_CUSTOM_'.strtoupper(substr(md5($titleClean.microtime()), 0, 6));
            $extraHours = max(4, (int) ($validated['extra_man_hours'] ?? 12));
            $existingModules[] = [
                'module_code' => $customCode,
                'is_custom_client_addition' => true,
                'title' => ['id' => $titleClean, 'en' => $titleClean],
                'localized_title' => $titleClean,
                'status' => 'DISCUSSION',
                'extra_man_hours' => $extraHours,
                'client_business_lens' => [
                    'wireframe_widgets' => ! empty($validated['widgets']) ? $validated['widgets'] : [$titleClean, 'O(1) Cursor Table', 'Form Approval'],
                ],
                'architect_technical_lens' => [
                    'ulid_tables' => ! empty($validated['ulid_tables']) ? $validated['ulid_tables'] : [Str::snake($titleClean).'_records'],
                ],
                'annotations' => ! empty($validated['note']) ? [[
                    'author_name' => $authorName,
                    'author_role' => $authorRole,
                    'note' => trim((string) $validated['note']),
                    'extra_man_hours' => $extraHours,
                    'created_at' => now()->toIso8601String(),
                ]] : [],
            ];
            $auditAction = 'CODESIGN_CUSTOM_MODULE_ADDED';
            $targetModuleCode = $customCode;
        } else {
            $targetModuleCode = (string) ($validated['module_code'] ?? ($existingModules[0]['module_code'] ?? 'MOD_DIV_01'));
            $noteClean = trim((string) ($validated['note'] ?? ''));
            $deltaHours = (int) ($validated['extra_man_hours'] ?? 0);
            foreach ($existingModules as &$mod) {
                if (($mod['module_code'] ?? '') === $targetModuleCode) {
                    $mod['annotations'] = array_values((array) ($mod['annotations'] ?? []));
                    if ($noteClean !== '') {
                        $mod['annotations'][] = [
                            'author_name' => $authorName,
                            'author_role' => $authorRole,
                            'note' => $noteClean,
                            'extra_man_hours' => $deltaHours,
                            'created_at' => now()->toIso8601String(),
                        ];
                    }
                    if ($deltaHours > 0) {
                        $mod['extra_man_hours'] = ((int) ($mod['extra_man_hours'] ?? 0)) + $deltaHours;
                    }
                    if (($mod['status'] ?? 'PENDING') === 'APPROVED' && $deltaHours > 0) {
                        // Require re-approval when scope hours expand
                        $mod['status'] = 'DISCUSSION';
                    }
                    break;
                }
            }
            unset($mod);
            $auditAction = 'CODESIGN_ANNOTATION_ADDED';
        }

        $recompiledSchema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
            'persist_project' => true,
            'codesign_modules' => $existingModules,
            'codesign_dual_key' => $dualKey,
        ]);
        $quotation = $bridge->calculateScopeQuotation($recompiledSchema);
        if (Schema::hasColumn('pos_projects', 'commercial_quotation_json')) {
            $posProject->refresh();
            $posProject->forceFill(['commercial_quotation_json' => $quotation])->saveQuietly();
        }

        AuditLog::record(
            action: $auditAction,
            model: $blueprint,
            newValues: [
                'module_code' => $targetModuleCode,
                'author_name' => $authorName,
                'extra_codesign_man_hours' => $recompiledSchema['codesign_alignment_contract']['extra_codesign_man_hours'] ?? 0,
                'updated_quotation_idr' => $quotation['quotation_idr'] ?? 0,
                'scope_hash_short' => $quotation['scope_hash_short'] ?? '',
            ],
            description: "Co-Design Kolaborasi [{$auditAction}] pada modul {$targetModuleCode} oleh {$authorName} -> Estimasi RAB baru: ".($quotation['formatted_quotation_idr'] ?? ''),
            eventCategory: 'codesign_alignment',
            sectorCode: (string) ($recompiledSchema['project_metadata']['primary_sector'] ?? null),
            primitiveCode: 'PRIMITIVE_CODESIGN_ROOM',
            executionChannel: 'WEB_UI'
        );

        return response()->json([
            'success' => true,
            'codesign' => $recompiledSchema['codesign_alignment_contract'] ?? [],
            'quotation' => $quotation,
            'causality' => $recompiledSchema['cross_stage_causality_graph'] ?? [],
        ]);
    }

    /**
     * Project OS v9.0 — Co-Design Alignment Room: Dual-Key Cryptographic Scope Lock Ceremony (Architect + Client).
     */
    public function codesignDualKeyLock(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'architect_name' => 'nullable|string|max:160',
            'client_name' => 'nullable|string|max:160',
            'termin_scheme' => 'nullable|string|in:30_50_20,50_50',
        ]);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first()
            ?? app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);

        $schema = ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);

        $codesign = $schema['codesign_alignment_contract'] ?? [];
        if (empty($codesign['can_execute_dual_key_lock'])) {
            return response()->json([
                'success' => false,
                'message' => 'Dual-Key Scope Lock ditolak: Seluruh modul bisnis (100%) wajib berstatus APPROVED terlebih dahulu sebelum mengunci Blueprint v1.0.',
                'approved_modules_count' => $codesign['approved_modules_count'] ?? 0,
                'total_modules_count' => $codesign['total_modules_count'] ?? 0,
            ], 422);
        }

        $lockResult = $bridge->lockScopeAndGenerateSpk($blueprint, $posProject, array_filter([
            'termin_scheme' => $validated['termin_scheme'] ?? null,
        ], static fn ($v) => $v !== null));

        $masterHash = (string) ($lockResult['scope_hash_full'] ?? $lockResult['scope_hash'] ?? hash('sha256', json_encode($schema)));
        $architectSigner = ($validated['architect_name'] ?? null) ?: 'Lead Architect NeriahPro';
        $clientSigner = ($validated['client_name'] ?? null) ?: ($blueprint->client_name ?: 'Client Authorized Signer');

        $dualKeyPayload = [
            'locked_v1_readonly' => true,
            'architect_signed_by' => $architectSigner,
            'architect_signed_at' => now()->toIso8601String(),
            'client_signed_by' => $clientSigner,
            'client_signed_at' => now()->toIso8601String(),
            'client_ip' => $request->ip(),
            'master_scope_hash_sha256' => $masterHash,
            'spk_document_id' => $lockResult['document_id'] ?? null,
        ];

        $recompiledSchema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
            'persist_project' => true,
            'codesign_modules' => $posProject->codesign_modules_json,
            'codesign_dual_key' => $dualKeyPayload,
        ]);

        AuditLog::record(
            action: 'DUAL_KEY_SCOPE_LOCK',
            model: $blueprint,
            newValues: $dualKeyPayload,
            description: "Dual-Key Cryptographic Scope Lock v1.0 disahkan oleh [{$architectSigner}] & [{$clientSigner}] — Master SHA-256: ".substr($masterHash, 0, 16),
            eventCategory: 'codesign_alignment',
            sectorCode: (string) ($recompiledSchema['project_metadata']['primary_sector'] ?? null),
            primitiveCode: 'PRIMITIVE_CODESIGN_ROOM',
            executionChannel: 'WEB_UI'
        );

        return response()->json([
            'success' => true,
            'message' => 'Blueprint v1.0 resmi dikunci Read-Only melalui seremoni Dual-Key Cryptographic Scope Lock!',
            'master_scope_hash_sha256' => $masterHash,
            'spk_lock' => $lockResult,
            'codesign' => $recompiledSchema['codesign_alignment_contract'] ?? [],
        ]);
    }

    /**
     * Mitra Commercial #5a: Toggle Interactive Given-When-Then UAT Scenario.
     */
    public function toggleUatScenario(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'scenario_id' => 'required|string|max:80',
            'passed' => 'nullable|boolean',
            'actor_name' => 'nullable|string|max:160',
        ]);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        if (! $posProject) {
            $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        }

        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $uat = $bridge->toggleUatScenario(
            $posProject,
            $schema,
            $validated['scenario_id'],
            array_key_exists('passed', $validated) ? (bool) $validated['passed'] : null,
            $validated['actor_name'] ?? null
        );

        return response()->json([
            'success' => true,
            'uat' => $uat,
        ]);
    }

    /**
     * Mitra Commercial #5b: Client Digital UAT Sign-Off & Official BAST Issuance.
     */
    public function signOffUatAndIssueBast(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'signer_name' => 'nullable|string|max:160',
            'signer_email' => 'nullable|email|max:160',
            'mark_all_passed' => 'nullable|boolean',
        ]);
        $validated['signer_ip'] = $request->ip();

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $result = $bridge->signOffUatAndIssueBast($blueprint, $posProject, $validated);

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

}
