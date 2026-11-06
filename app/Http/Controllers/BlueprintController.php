<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Blueprint\BlueprintAuditController;
use App\Http\Controllers\Blueprint\BlueprintCollaborationController;
use App\Http\Controllers\Blueprint\BlueprintCommercialController;
use App\Http\Controllers\Blueprint\BlueprintDiscoveryController;
use App\Http\Controllers\Blueprint\BlueprintExportController;
use App\Http\Controllers\Blueprint\BlueprintRefinementController;
use App\Http\Controllers\Blueprint\BlueprintScaffoldController;
use App\Http\Controllers\Blueprint\BlueprintViewController;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDiscoveryService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintKpiCascadeService;
use App\Services\BlueprintRefinementService;
use App\Services\BlueprintSlopScannerService;
use App\Services\BrownfieldMigrationAuditorService;
use App\Services\PosCommercialBridgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * BlueprintController (High-Performance Facade & Dispatcher)
 *
 * Decomposed into 8 domain-specialized sub-controllers under App\Http\Controllers\Blueprint\:
 * 1. BlueprintDiscoveryController     - Idea intake, form discovery, matrix catalog, topological preview
 * 2. BlueprintViewController          - Show blueprint, recalculate ROI, full regeneration
 * 3. BlueprintCommercialController    - Contract, Midtrans Snap tokens, vouchers, pricing, SPK, scope unlock
 * 4. BlueprintExportController        - PDF, Markdown, Cursorrules, Schema JSON downloads
 * 5. BlueprintScaffoldController      - Code scaffolding export, preview, save, reset, AI refinement
 * 6. BlueprintRefinementController    - Segment tweaking, directive elaboration, KPI cascade
 * 7. BlueprintCollaborationController - Real-time presence, tasks, checkpoints, UAT, co-sign ceremonies
 * 8. BlueprintAuditController         - CLI init script, brownfield migration, slop scanner, doctor, delta CRs
 *
 * Retains 100% backward-compatibility for existing routes, callers, and tests.
 */
class BlueprintController extends Controller
{
    // =========================================================================
    // DISCOVERY (BlueprintDiscoveryController)
    // =========================================================================

    /**
     * Show the public Project OS & Vision Blueprint Questionnaire form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        return app(BlueprintDiscoveryController::class)->create($request);
    }

    /**
     * Explicitly reset and purge current blueprint draft from session and cache.
     */
    public function resetDraft(Request $request): JsonResponse
    {
        return app(BlueprintDiscoveryController::class)->resetDraft($request);
    }

    /**
     * Analyze user's raw idea text and uploaded documents/images via MarkItDown & AI Engine.
     */
    public function analyzeIdea(Request $request, BlueprintDiscoveryService $discoveryService): JsonResponse
    {
        return app(BlueprintDiscoveryController::class)->analyzeIdea($request, $discoveryService);
    }

    /**
     * Proactively integrate additional requirements or ideas into the active blueprint.
     */
    public function supplementIdea(Request $request, BlueprintDiscoveryService $discoveryService): JsonResponse
    {
        return app(BlueprintDiscoveryController::class)->supplementIdea($request, $discoveryService);
    }

    /**
     * Auto-save blueprint draft in background (Dual-Tier Persistence: Cache & Session + Database when editing slug).
     */
    public function autoSave(Request $request): JsonResponse
    {
        return app(BlueprintDiscoveryController::class)->autoSave($request);
    }

    /**
     * Return O(1) cached 12-Sector + 12-Primitive + Rule Pack Composable Matrix catalog + v8.0 Inclusive Catalog.
     */
    public function matrixCatalog(): JsonResponse
    {
        return app(BlueprintDiscoveryController::class)->matrixCatalog();
    }

    /**
     * Project OS v7.0: Live Topological DAG, L1–L4 Menu Matrix, 5 Enterprise Glue, 5 Real-World Elements & 15 Pillars Preview API.
     */
    public function topologicalPreview(Request $request): JsonResponse
    {
        return app(BlueprintDiscoveryController::class)->topologicalPreview($request);
    }

    // =========================================================================
    // VIEW (BlueprintViewController)
    // =========================================================================

    /**
     * Show the generated Ultimate PRD & Blueprint for a specific project slug.
     */
    public function show(string $slug): View
    {
        return app(BlueprintViewController::class)->show($slug);
    }

    /**
     * Recalculate and regenerate Business Feasibility & ROI Analysis for this blueprint (JSON API).
     */
    public function recalculateRoi(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintViewController::class)->recalculateRoi($request, $slug);
    }

    /**
     * Recalculate ROI and redirect back to the blueprint view.
     */
    public function recalculateRoiRedirect(string $slug)
    {
        return app(BlueprintViewController::class)->recalculateRoiRedirect($slug);
    }

    /**
     * Regenerate Full PRD, Architecture Specs, ERD & Diagrams (API Endpoint).
     */
    public function regenerateFullBlueprint(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintViewController::class)->regenerateFullBlueprint($request, $slug);
    }

    /**
     * Web redirect fallback for full regeneration.
     */
    public function regenerateFullBlueprintRedirect(Request $request, string $slug)
    {
        return app(BlueprintViewController::class)->regenerateFullBlueprintRedirect($request, $slug);
    }

    // =========================================================================
    // COMMERCIAL (BlueprintCommercialController)
    // =========================================================================

    /**
     * Generate contract and redirect to sign page
     */
    public function generateContract(string $slug)
    {
        return app(BlueprintCommercialController::class)->generateContract($slug);
    }

    /**
     * Get Midtrans Snap Token for this blueprint proposal (Termin DP 50%).
     */
    public function getSnapToken(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCommercialController::class)->getSnapToken($request, $slug);
    }

    /**
     * Get Midtrans Snap Token for Final Project Settlement / Pelunasan 50% (Milestone 2).
     */
    public function getPelunasanSnapToken(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCommercialController::class)->getPelunasanSnapToken($request, $slug);
    }

    /**
     * Validate a promo voucher code for a blueprint proposal.
     */
    public function validateVoucher(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCommercialController::class)->validateVoucher($request, $slug);
    }

    /**
     * Claim voucher to bypass payment (Rp 0 Free Grant) or apply grant.
     */
    public function claimVoucher(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCommercialController::class)->claimVoucher($request, $slug);
    }

    /**
     * 1-Click VIP Owner Bypass for a single blueprint.
     * Instantly unlocks all 15-pillar Project OS features, signed contract, staging sandbox, and full active sprint status.
     * Strictly gated to Super Administrator (Solo Founder).
     */
    public function vipOwnerBypass(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        return app(BlueprintCommercialController::class)->vipOwnerBypass($request, $slug);
    }

    /**
     * Handle public consultation and booking inquiry from the Pricing Page.
     */
    public function pricingInquiry(Request $request): JsonResponse
    {
        return app(BlueprintCommercialController::class)->pricingInquiry($request);
    }

    /**
     * Mitra Commercial #1: Satu Pintu Live Scope-to-Quotation & WBS Estimator API.
     */
    public function commercialEstimate(Request $request, PosCommercialBridgeService $bridge): JsonResponse
    {
        return app(BlueprintCommercialController::class)->commercialEstimate($request, $bridge);
    }

    /**
     * Mitra Commercial #2: Cryptographic Scope Freeze (SHA-256) & Official SPK Generator.
     */
    public function lockScopeAndGenerateSpk( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintCommercialController::class)->lockScopeAndGenerateSpk($request, $slug, $bridge);
    }

    /**
     * Unlock Scope Helper: Membuka kunci scope agar pengguna/admin dapat melanjutkan elaborasi.
     * Dapat dipanggil melalui AJAX atau tombol web biasa.
     */
    public function unlockScope( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse|RedirectResponse
    {
        return app(BlueprintCommercialController::class)->unlockScope($request, $slug, $bridge);
    }

    /**
     * Unlock Scope GET Helper (for quick 1-click links).
     */
    public function unlockScopeRedirect( Request $request, string $slug, PosCommercialBridgeService $bridge ): RedirectResponse
    {
        return app(BlueprintCommercialController::class)->unlockScopeRedirect($request, $slug, $bridge);
    }

    /**
     * Render or stream PDF for Official A4 Mitra Commercial Documents (`rab`, `spk-appendix`, `bast`, `sop-training`).
     */
    public function mitraDocument( Request $request, string $slug, string $docType, PosCommercialBridgeService $bridge )
    {
        return app(BlueprintCommercialController::class)->mitraDocument($request, $slug, $docType, $bridge);
    }

    // =========================================================================
    // EXPORT (BlueprintExportController)
    // =========================================================================

    /**
     * Download PRD as PDF
     */
    public function downloadPdf(string $slug)
    {
        return app(BlueprintExportController::class)->downloadPdf($slug);
    }

    /**
     * Download PRD as Markdown (with Invisible Zero-Width Canary Watermark & Viral/White-Label Seal v8.0)
     */
    public function downloadMd(string $slug)
    {
        return app(BlueprintExportController::class)->downloadMd($slug);
    }

    /**
     * Get Raw PRD Markdown for AI Code Agent clipboard copy (with Invisible Zero-Width Canary Watermark v8.0)
     */
    public function rawMd(string $slug)
    {
        return app(BlueprintExportController::class)->rawMd($slug);
    }

    /**
     * Download compiled Universal Blueprint Schema v1.0 JSON (.agents/BLUEPRINT_SCHEMA.json).
     */
    public function downloadSchemaJson(string $slug)
    {
        return app(BlueprintExportController::class)->downloadSchemaJson($slug);
    }

    /**
     * Download AI IDE Rules (.cursorrules / CLAUDE.md / AGENTS.md / PROJECT_OS_EXECUTION_BLUEPRINT.md)
     */
    public function downloadCursorrules(Request $request, string $slug)
    {
        return app(BlueprintExportController::class)->downloadCursorrules($request, $slug);
    }

    // =========================================================================
    // SCAFFOLD (BlueprintScaffoldController)
    // =========================================================================

    /**
     * Download Project Scaffold & Boilerplate ZIP Archive.
     */
    public function exportScaffold(string $slug)
    {
        return app(BlueprintScaffoldController::class)->exportScaffold($slug);
    }

    /**
     * Preview Project Scaffold Files in JSON format for in-browser viewer.
     */
    public function previewScaffold(string $slug): JsonResponse
    {
        return app(BlueprintScaffoldController::class)->previewScaffold($slug);
    }

    /**
     * Auto-save or update an elaborated scaffold file for this blueprint.
     */
    public function saveScaffoldFile(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintScaffoldController::class)->saveScaffoldFile($request, $slug);
    }

    /**
     * Reset an elaborated scaffold file back to default AI synthesis.
     */
    public function resetScaffoldFile(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintScaffoldController::class)->resetScaffoldFile($request, $slug);
    }

    /**
     * Refine an active scaffold file using Grounded Multi-Model AI based on Source Corpus.
     */
    public function refineScaffoldFile(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintScaffoldController::class)->refineScaffoldFile($request, $slug);
    }

    // =========================================================================
    // REFINEMENT (BlueprintRefinementController)
    // =========================================================================

    /**
     * Partial / Granular Segment Edit & AI Regeneration.
     * Allows Retail users or SuperAdmins to modify specific PRD segments without resetting the entire document.
     */
    /**
     * Retrieve the grounded source specification corpus (MarkItDown, uploaded files, original brief).
     */
    public function getSourceCorpus(Request $request, string $slug, BlueprintRefinementService $refinementService): JsonResponse
    {
        return app(BlueprintRefinementController::class)->getSourceCorpus($request, $slug, $refinementService);
    }

    /**
     * Partial / Granular Segment Edit & Grounded AI Refinement.
     * Allows Retail users or SuperAdmins to modify or deepen specific PRD segments based on Source Corpus.
     */
    public function tweakSegment(Request $request, string $slug, BlueprintRefinementService $refinementService): JsonResponse
    {
        return app(BlueprintRefinementController::class)->tweakSegment($request, $slug, $refinementService);
    }

    /**
     * Generate an AI-elaborated directive instruction grounded in project source corpus & world-class standards.
     */
    public function elaborateDirective(Request $request, string $slug, BlueprintRefinementService $refinementService): JsonResponse
    {
        return app(BlueprintRefinementController::class)->elaborateDirective($request, $slug, $refinementService);
    }

    /**
     * Dedicated AI KPI Elaboration Engine with Cascading Architectural Impact.
     * Deep-dives into business metrics and cascades changes to Features, Actors, and UAT.
     */
    public function elaborateKpiCascade(Request $request, BlueprintKpiCascadeService $kpiCascadeService, ?string $slug = null): JsonResponse
    {
        return app(BlueprintRefinementController::class)->elaborateKpiCascade($request, $kpiCascadeService, $slug);
    }

    /**
     * Slug-specific wrapper for KPI Cascade elaboration.
     */
    public function elaborateKpiCascadeSlug(Request $request, string $slug, BlueprintKpiCascadeService $kpiCascadeService): JsonResponse
    {
        return app(BlueprintRefinementController::class)->elaborateKpiCascadeSlug($request, $slug, $kpiCascadeService);
    }

    // =========================================================================
    // COLLABORATION (BlueprintCollaborationController)
    // =========================================================================

    /**
     * Update and elaborate technical tasks and features for a blueprint proposal.
     * Guarded against scope lock (only allowed before contract is signed).
     */
    public function updateTasks(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->updateTasks($request, $slug);
    }

    /**
     * Update and persist developer/architect execution checkpoints for a blueprint.
     * Synchronizes live progress with the customer dashboard.
     */
    public function updateCheckpoint(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->updateCheckpoint($request, $slug);
    }

    /**
     * Real-time Live Presence & Collaborative Cursor Heartbeat.
     * Records collaborator coordinates, active section, and role into cache.
     */
    public function updatePresence(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->updatePresence($request, $slug);
    }

    /**
     * Get active collaborators on this blueprint.
     */
    public function getPresence(string $slug): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->getPresence($slug);
    }

    /**
     * Mitra Commercial #5a: Toggle Interactive Given-When-Then UAT Scenario.
     */
    public function toggleUatScenario( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->toggleUatScenario($request, $slug, $bridge);
    }

    /**
     * Mitra Commercial #5b: Client Digital UAT Sign-Off & Official BAST Issuance.
     */
    public function signOffUatAndIssueBast( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->signOffUatAndIssueBast($request, $slug, $bridge);
    }

    /**
     * Project OS v9.0 — Co-Design Alignment Room: Per-Module Clause Sign-Off (Client / Architect).
     */
    public function codesignSignModule( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->codesignSignModule($request, $slug, $bridge);
    }

    /**
     * Project OS v9.0 — Co-Design Alignment Room: Contextual Annotation OR Add Custom Client Requirement (+ Live RAB Impact).
     */
    public function codesignAnnotate( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->codesignAnnotate($request, $slug, $bridge);
    }

    /**
     * Project OS v9.0 — Co-Design Alignment Room: Dual-Key Cryptographic Scope Lock Ceremony (Architect + Client).
     */
    public function codesignDualKeyLock( Request $request, string $slug, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintCollaborationController::class)->codesignDualKeyLock($request, $slug, $bridge);
    }

    // =========================================================================
    // AUDIT (BlueprintAuditController)
    // =========================================================================

    /**
     * Killer Feature #6: One-Command CLI Bootstrapper (`curl -sL https://neriahpro.com/init/{identifier} | bash`).
     * Resolves VisionBlueprint (by exact ULID `id` or exact `slug`) or PosProject (by exact ULID `id`).
     */
    public function cliInitScript(Request $request, string $identifier)
    {
        return app(BlueprintAuditController::class)->cliInitScript($request, $identifier);
    }

    /**
     * Killer Feature #4: Reverse-Engineer / Brownfield SQL & Migration Importer API.
     */
    public function brownfieldAudit(Request $request, BrownfieldMigrationAuditorService $auditor): JsonResponse
    {
        return app(BlueprintAuditController::class)->brownfieldAudit($request, $auditor);
    }

    /**
     * Killer Feature #5: Blueprint Drift Auditor & Slop Scanner API.
     */
    public function slopAudit(Request $request, string $slug, BlueprintSlopScannerService $scanner): JsonResponse
    {
        return app(BlueprintAuditController::class)->slopAudit($request, $slug, $scanner);
    }

    /**
     * Layer 8: Interactive Pre-Flight Blueprint Doctor API (before or during blueprint creation).
     */
    public function blueprintDoctor(Request $request, BlueprintDoctorService $doctor): JsonResponse
    {
        return app(BlueprintAuditController::class)->blueprintDoctor($request, $doctor);
    }

    /**
     * Layer 8: Blueprint Doctor Live Audit on a persisted VisionBlueprint.
     */
    public function blueprintSlugDoctor(Request $request, string $slug, BlueprintDoctorService $doctor): JsonResponse
    {
        return app(BlueprintAuditController::class)->blueprintSlugDoctor($request, $slug, $doctor);
    }

    /**
     * Lifecycle #1: Create a Surgical Delta Change Request (v1.0 -> v1.1) for Day-30+ Feature Evolution.
     */
    public function createDeltaChangeRequest( Request $request, string $slug, BlueprintDeltaEvolutionService $deltaService ): JsonResponse
    {
        return app(BlueprintAuditController::class)->createDeltaChangeRequest($request, $slug, $deltaService);
    }

    /**
     * Lifecycle #1: O(1) Keyset Cursor-Paginated Change Request History for a Blueprint.
     */
    public function listDeltaChangeRequests(Request $request, string $slug): JsonResponse
    {
        return app(BlueprintAuditController::class)->listDeltaChangeRequests($request, $slug);
    }

    /**
     * Mitra Commercial #3: Client Approves Paid Change-Order Addendum & Generates Delta Invoice.
     */
    public function approveDeltaAddendum( Request $request, string $slug, string $crId, PosCommercialBridgeService $bridge ): JsonResponse
    {
        return app(BlueprintAuditController::class)->approveDeltaAddendum($request, $slug, $crId, $bridge);
    }
}
