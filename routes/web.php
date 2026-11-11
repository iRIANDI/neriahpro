<?php

use App\Http\Controllers\Api\CvProApiController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\VisionBlueprintController;
use App\Http\Controllers\Blueprint\BlueprintArtifactController;
use App\Http\Controllers\Blueprint\BlueprintAuditController;
use App\Http\Controllers\Blueprint\BlueprintCollaborationController;
use App\Http\Controllers\Blueprint\BlueprintCommercialController;
use App\Http\Controllers\Blueprint\BlueprintDiscoveryController;
use App\Http\Controllers\Blueprint\BlueprintExportController;
use App\Http\Controllers\Blueprint\BlueprintRefinementController;
use App\Http\Controllers\Blueprint\BlueprintScaffoldController;
use App\Http\Controllers\Blueprint\BlueprintViewController;
use App\Http\Controllers\BlueprintController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CvProController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HealthProbeController;
use App\Http\Controllers\LegalPolicyController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectOsMcpController;
use App\Http\Controllers\ProjectThreadController;
use App\Http\Controllers\PublicExploreArchitectureController;
use App\Http\Controllers\SitemapController;
use App\Livewire\BlueprintArchitectStudio;
use App\Livewire\ClientInviteForm;
use App\Livewire\DocumentSignature;
use App\Services\Ai\MultiAiModelManager;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Health Probes & SRE Telemetry Endpoints
Route::get('/healthz', [HealthProbeController::class, 'healthz'])->name('healthz');
Route::get('/livez', [HealthProbeController::class, 'livez'])->name('livez');
Route::get('/readyz', [HealthProbeController::class, 'readyz'])->name('readyz');

Route::post('/api/onboarding', [OnboardingController::class, 'store']);
Route::post('/api/vision-blueprint', [VisionBlueprintController::class, 'store'])->middleware('throttle:30,1');
Route::post('/api/blueprint/report-incident', [\App\Http\Controllers\Api\ClientIncidentController::class, 'report'])->middleware('throttle:30,1');
Route::post('/api/telemetry/client-error', [\App\Http\Controllers\Api\ClientIncidentController::class, 'recordTelemetry'])->middleware('throttle:60,1');

Route::get('/document/{document}/preview', [DocumentController::class, 'preview'])
    ->name('document.preview')
    ->middleware(['web']);

Route::get('/document/{document}/sign', DocumentSignature::class)
    ->name('document.sign')
    ->middleware(['web']);

// Multi-AI Model Orchestrator & Health Telemetry
Route::get('/api/ai/models', function () {
    return response()->json([
        'success' => true,
        'models' => MultiAiModelManager::getCatalog(),
    ]);
})->name('api.ai.models');

// Project OS: Tech Proposal & Ultimate PRD Routes
Route::get('/init/{identifier}', [BlueprintAuditController::class, 'cliInitScript'])->name('blueprint.cli-init')->middleware('throttle:30,1');
Route::get('/api/blueprint/matrix-catalog', [BlueprintDiscoveryController::class, 'matrixCatalog'])->name('api.blueprint.matrix-catalog');
Route::post('/api/blueprint/brownfield-audit', [BlueprintAuditController::class, 'brownfieldAudit'])->name('api.blueprint.brownfield-audit')->middleware('throttle:20,1');
Route::post('/api/blueprint/doctor', [BlueprintAuditController::class, 'blueprintDoctor'])->name('api.blueprint.doctor')->middleware('throttle:40,1');
Route::post('/api/blueprint/{slug}/doctor', [BlueprintAuditController::class, 'blueprintSlugDoctor'])->name('api.blueprint.slug-doctor')->middleware('throttle:30,1');
Route::post('/api/blueprint/{slug}/slop-audit', [BlueprintAuditController::class, 'slopAudit'])->name('api.blueprint.slop-audit')->middleware('throttle:30,1');
Route::post('/api/blueprint/analyze-idea', [BlueprintDiscoveryController::class, 'analyzeIdea'])->name('api.blueprint.analyze-idea')->middleware('throttle:15,1');
Route::post('/api/blueprint/elaborate-kpi-cascade', [BlueprintRefinementController::class, 'elaborateKpiCascade'])->name('api.blueprint.elaborate-kpi-cascade')->middleware('throttle:30,1');
Route::post('/api/blueprint/{slug}/elaborate-kpi-cascade', [BlueprintRefinementController::class, 'elaborateKpiCascadeSlug'])->name('api.blueprint.elaborate-kpi-cascade-slug')->middleware('throttle:30,1');
Route::post('/api/blueprint/supplement-idea', [BlueprintDiscoveryController::class, 'supplementIdea'])->name('api.blueprint.supplement-idea')->middleware('throttle:30,1');
Route::post('/api/blueprint/autosave', [BlueprintDiscoveryController::class, 'autoSave'])->name('api.blueprint.autosave')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/tweak-segment', [BlueprintRefinementController::class, 'tweakSegment'])->name('api.blueprint.tweak-segment')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/elaborate-directive', [BlueprintRefinementController::class, 'elaborateDirective'])->name('api.blueprint.elaborate-directive')->middleware('throttle:30,1');
Route::get('/api/blueprint/{slug}/source-corpus', [BlueprintRefinementController::class, 'getSourceCorpus'])->name('api.blueprint.source-corpus')->middleware('throttle:60,1');
Route::post('/api/blueprint/reset', [BlueprintDiscoveryController::class, 'resetDraft'])->name('api.blueprint.reset')->middleware('throttle:30,1');
Route::post('/api/blueprint/{slug}/delta-request', [BlueprintAuditController::class, 'createDeltaChangeRequest'])->name('api.blueprint.delta.create')->middleware('throttle:30,1');
Route::get('/api/blueprint/{slug}/delta-requests', [BlueprintAuditController::class, 'listDeltaChangeRequests'])->name('api.blueprint.delta.list')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/delta-requests/{crId}/approve', [BlueprintAuditController::class, 'approveDeltaAddendum'])->name('api.blueprint.delta.approve')->middleware('throttle:30,1');
Route::post('/api/blueprint/commercial-estimate', [BlueprintCommercialController::class, 'commercialEstimate'])->name('api.blueprint.commercial-estimate')->middleware('throttle:60,1');
Route::post('/api/blueprint/topological-preview', [BlueprintDiscoveryController::class, 'topologicalPreview'])->name('api.blueprint.topological-preview')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/lock-scope-spk', [BlueprintCommercialController::class, 'lockScopeAndGenerateSpk'])->name('api.blueprint.lock-scope-spk')->middleware('throttle:30,1');
Route::post('/api/blueprint/{slug}/unlock-scope', [BlueprintCommercialController::class, 'unlockScope'])->name('api.blueprint.unlock-scope')->middleware('throttle:30,1');
Route::post('/blueprint/{slug}/unlock-scope', [BlueprintCommercialController::class, 'unlockScope'])->name('blueprint.unlock-scope')->middleware('throttle:30,1');
Route::get('/blueprint/{slug}/unlock-scope', [BlueprintCommercialController::class, 'unlockScopeRedirect'])->name('blueprint.unlock-scope.get')->middleware('throttle:30,1');
Route::post('/api/blueprint/{slug}/codesign/sign-module', [BlueprintCollaborationController::class, 'codesignSignModule'])->name('api.blueprint.codesign.sign-module')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/codesign/annotate', [BlueprintCollaborationController::class, 'codesignAnnotate'])->name('api.blueprint.codesign.annotate')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/codesign/dual-key-lock', [BlueprintCollaborationController::class, 'codesignDualKeyLock'])->name('api.blueprint.codesign.dual-key-lock')->middleware('throttle:20,1');
Route::post('/api/blueprint/{slug}/uat/toggle', [BlueprintCollaborationController::class, 'toggleUatScenario'])->name('api.blueprint.uat.toggle')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/uat/sign-bast', [BlueprintCollaborationController::class, 'signOffUatAndIssueBast'])->name('api.blueprint.uat.sign-bast')->middleware('throttle:20,1');
Route::get('/blueprint/{slug}/mitra-document/{docType}', [BlueprintCommercialController::class, 'mitraDocument'])->name('blueprint.mitra-document');
Route::get('/api/blueprint/{slug}/mcp-config', [ProjectOsMcpController::class, 'mcpConfig'])->name('api.blueprint.mcp-config')->middleware('throttle:30,1');
Route::post('/api/mcp/project-os', [ProjectOsMcpController::class, 'handleRpc'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('api.mcp.project-os')
    ->middleware('throttle:120,1');
Route::get('/blueprint', [BlueprintDiscoveryController::class, 'create'])->name('blueprint.create');
Route::get('/blueprint/studio/{slug?}', BlueprintArchitectStudio::class)->name('blueprint.studio');
Route::get('/blueprint/{slug}', [BlueprintViewController::class, 'show'])->name('blueprint.show');
Route::post('/api/blueprint/{slug}/recalculate-roi', [BlueprintViewController::class, 'recalculateRoi'])->name('api.blueprint.recalculate-roi')->middleware('throttle:30,1');
Route::get('/blueprint/{slug}/recalculate-roi', [BlueprintViewController::class, 'recalculateRoiRedirect'])->name('blueprint.recalculate-roi.get');
Route::post('/api/blueprint/{slug}/regenerate-prd', [BlueprintViewController::class, 'regenerateFullBlueprint'])->name('api.blueprint.regenerate-prd')->middleware('throttle:15,1');
Route::get('/blueprint/{slug}/regenerate-full', [BlueprintViewController::class, 'regenerateFullBlueprintRedirect'])->name('blueprint.regenerate-full.get');
Route::post('/blueprint/{slug}/generate-contract', [BlueprintCommercialController::class, 'generateContract'])->name('blueprint.generate-contract');
Route::post('/blueprint/{slug}/snap-token', [BlueprintCommercialController::class, 'getSnapToken'])->name('blueprint.snap-token');
Route::post('/blueprint/{slug}/pelunasan-snap-token', [BlueprintCommercialController::class, 'getPelunasanSnapToken'])->name('blueprint.pelunasan-snap-token');
Route::post('/blueprint/{slug}/voucher/validate', [BlueprintCommercialController::class, 'validateVoucher'])->name('blueprint.voucher.validate');
Route::post('/blueprint/{slug}/voucher/claim', [BlueprintCommercialController::class, 'claimVoucher'])->name('blueprint.voucher.claim');
Route::post('/blueprint/{slug}/vip-owner-bypass', [BlueprintCommercialController::class, 'vipOwnerBypass'])->name('blueprint.vip-bypass');
Route::get('/blueprint/{slug}/download/pdf', [BlueprintExportController::class, 'downloadPdf'])->name('blueprint.download-pdf');
Route::get('/blueprint/{slug}/download/md', [BlueprintExportController::class, 'downloadMd'])->name('blueprint.download-md')->middleware('throttle:30,1');
Route::get('/blueprint/{slug}/download/cursorrules', [BlueprintExportController::class, 'downloadCursorrules'])->name('blueprint.download-cursorrules')->middleware('throttle:30,1');
Route::get('/blueprint/{slug}/download/schema-json', [BlueprintExportController::class, 'downloadSchemaJson'])->name('blueprint.download-schema-json')->middleware('throttle:30,1');
Route::get('/blueprint/{slug}/raw-md', [BlueprintExportController::class, 'rawMd'])->name('blueprint.raw-md')->middleware('throttle:30,1');
Route::get('/blueprint/{slug}/export/scaffold', [BlueprintScaffoldController::class, 'exportScaffold'])->name('blueprint.export-scaffold');
Route::post('/blueprint/{slug}/scaffold/custom-folder', [BlueprintScaffoldController::class, 'saveCustomFolder'])->name('blueprint.scaffold.save-custom-folder')->middleware('throttle:60,1');
Route::post('/blueprint/{slug}/scaffold/generate-async', [BlueprintScaffoldController::class, 'generateAsync'])->name('blueprint.scaffold.generate-async');
Route::get('/blueprint/{slug}/scaffold/job-status', [BlueprintScaffoldController::class, 'getJobStatus'])->name('blueprint.scaffold.job-status');
Route::get('/blueprint/{slug}/scaffold/preview', [BlueprintScaffoldController::class, 'previewScaffold'])->name('blueprint.scaffold.preview');
Route::get('/blueprint/{slug}/scaffold-preview', [BlueprintScaffoldController::class, 'previewScaffold'])->name('blueprint.scaffold-preview');
Route::post('/blueprint/{slug}/scaffold/save-file', [BlueprintScaffoldController::class, 'saveScaffoldFile'])->name('blueprint.scaffold.save-file');
Route::post('/blueprint/{slug}/scaffold/reset-file', [BlueprintScaffoldController::class, 'resetScaffoldFile'])->name('blueprint.scaffold.reset-file');
Route::post('/blueprint/{slug}/scaffold/refine-file', [BlueprintScaffoldController::class, 'refineScaffoldFile'])->name('blueprint.scaffold.refine-file')->middleware('throttle:60,1');

// Asynchronous Blueprint Download Artifacts & 24h Lifecycle Download Center
Route::post('/blueprint/{slug}/artifacts/request', [BlueprintArtifactController::class, 'requestArtifact'])->name('blueprint.artifacts.request');
Route::get('/blueprint/{slug}/artifacts', [BlueprintArtifactController::class, 'listArtifacts'])->name('blueprint.artifacts.list');
Route::get('/blueprint/{slug}/artifacts/{id}/status', [BlueprintArtifactController::class, 'getArtifactStatus'])->name('blueprint.artifacts.status');
Route::get('/blueprint/{slug}/artifacts/{id}/download/{token}', [BlueprintArtifactController::class, 'downloadArtifact'])->name('blueprint.artifacts.download');
Route::delete('/blueprint/{slug}/artifacts/{id}', [BlueprintArtifactController::class, 'deleteArtifact'])->name('blueprint.artifacts.delete');
Route::post('/blueprint/{slug}/artifacts/cleanup-expired', [BlueprintArtifactController::class, 'cleanupExpired'])->name('blueprint.artifacts.cleanup');
Route::post('/api/blueprint/{slug}/presence', [BlueprintCollaborationController::class, 'updatePresence'])->name('api.blueprint.presence.update');
Route::get('/api/blueprint/{slug}/presence', [BlueprintCollaborationController::class, 'getPresence'])->name('api.blueprint.presence.get');
Route::post('/blueprint/{slug}/tasks/update', [BlueprintCollaborationController::class, 'updateTasks'])->name('blueprint.tasks.update');
Route::post('/blueprint/{slug}/tasks/analyze-gap', [BlueprintCollaborationController::class, 'analyzeTaskGap'])->name('blueprint.tasks.analyze-gap');
Route::post('/blueprint/{slug}/architecture-advisory/scan', [BlueprintCollaborationController::class, 'scanArchitectureAdvisory'])->name('blueprint.architecture-advisory.scan')->middleware('throttle:30,1');
Route::post('/blueprint/{slug}/architecture-advisory/integrate', [BlueprintCollaborationController::class, 'integrateArchitectureAdvisory'])->name('blueprint.architecture-advisory.integrate')->middleware('throttle:30,1');
Route::post('/api/blueprint/{slug}/checkpoint', [BlueprintCollaborationController::class, 'updateCheckpoint'])->name('api.blueprint.checkpoint');
Route::post('/api/pricing/inquiry', [BlueprintCommercialController::class, 'pricingInquiry'])->name('api.pricing.inquiry')->middleware('throttle:30,1');
Route::get('/api/currency/rates', [BlueprintCommercialController::class, 'currencyRates'])->name('api.currency.rates');

// Project Thread / UAT Ticketing Routes
Route::post('/blueprint/{slug}/issues', [ProjectThreadController::class, 'storeIssue'])->name('blueprint.issues.store');
Route::post('/blueprint/{slug}/issues/{issueId}/comments', [ProjectThreadController::class, 'storeComment'])->name('blueprint.issues.comments.store');
// Payment Gateway Webhooks (Midtrans DLQ Handler & Notification Aliases)
Route::match(['get', 'post'], '/api/webhook/midtrans', function (Request $request) {
    if ($request->isMethod('get')) {
        return app(MidtransWebhookController::class)->status($request);
    }

    return app(MidtransWebhookController::class)->handle($request);
})->name('webhook.midtrans');

Route::match(['get', 'post'], '/webhook/midtrans', function (Request $request) {
    if ($request->isMethod('get')) {
        return app(MidtransWebhookController::class)->status($request);
    }

    return app(MidtransWebhookController::class)->handle($request);
});

Route::match(['get', 'post'], '/api/midtrans/notification', function (Request $request) {
    if ($request->isMethod('get')) {
        return app(MidtransWebhookController::class)->status($request);
    }

    return app(MidtransWebhookController::class)->handle($request);
});

Route::match(['get', 'post'], '/api/midtrans/webhook', function (Request $request) {
    if ($request->isMethod('get')) {
        return app(MidtransWebhookController::class)->status($request);
    }

    return app(MidtransWebhookController::class)->handle($request);
});

// Cart & Project Checkout Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/api/cart', [CartController::class, 'apiCart'])->name('api.cart');
Route::post('/cart/add/{slug}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/snap-token', [CartController::class, 'getSnapToken'])->name('cart.snap-token');
Route::post('/cart/voucher/apply', [CartController::class, 'applyVoucher'])->name('cart.voucher.apply');
Route::post('/cart/voucher/remove', [CartController::class, 'removeVoucher'])->name('cart.voucher.remove');
Route::post('/cart/claim-free-grant', [CartController::class, 'claimFreeGrant'])->name('cart.claim-free');
Route::post('/cart/vip-owner-bypass', [CartController::class, 'vipOwnerBypass'])->name('cart.vip-bypass');
Route::post('/cart/reset-pending', [CartController::class, 'resetPendingOrder'])->name('cart.reset-pending');

Route::get('/invite/{slug}', ClientInviteForm::class)->name('invite');

// Customer & Client Dashboard Portal
Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
Route::get('/customer/transaction/{id}/invoice', [CustomerDashboardController::class, 'invoice'])->name('customer.transaction.invoice');
Route::post('/customer/transaction/{id}/sync', [CustomerDashboardController::class, 'syncTransaction'])->name('customer.transaction.sync')->middleware('auth');
Route::get('/portal', fn () => redirect()->route('customer.dashboard'))->name('portal');

// Customer Passwordless OTP Authentication Routes
Route::get('/customer/login', function () {
    if (auth()->check()) {
        return redirect()->route('customer.dashboard');
    }

    return view('auth.customer-login');
})->name('customer.login');

Route::prefix('api/customer')->group(function () {
    Route::post('/otp/request', [CustomerAuthController::class, 'requestOtp'])->middleware('throttle:10,1');
    Route::post('/otp/verify', [CustomerAuthController::class, 'verifyOtp'])->middleware('throttle:15,1');
    Route::get('/status', [CustomerAuthController::class, 'status']);
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::post('/profile', [CustomerDashboardController::class, 'updateProfile'])->middleware('auth')->name('customer.profile.update');
});

// Authentication Aliases for Standard Web Routes
Route::get('/login', function () {
    return redirect()->to('/admin/login');
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/');
})->name('logout');

Route::get('/lang/{locale}', function (\Illuminate\Http\Request $request, string $locale) {
    if (in_array($locale, ['id', 'en'], true)) {
        session(['locale' => $locale, 'neriah_locale' => $locale]);
        cookie()->queue(cookie('neriah_locale', $locale, 60 * 24 * 365, '/', null, false, false));
        cookie()->queue(cookie('neriah_locale_manual', '1', 60 * 24 * 365, '/', null, false, false));
        cookie()->queue(cookie()->forget('googtrans'));
        \Illuminate\Support\Facades\App::setLocale($locale);
    }

    if ($request->filled('redirect')) {
        return redirect()->to($request->query('redirect'));
    }

    $backUrl = url()->previous();
    if ($backUrl) {
        $parsed = parse_url($backUrl);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            unset($queryParams['locale'], $queryParams['lang']);
            $newQuery = http_build_query($queryParams);
            $cleanUrl = ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? '') . ($parsed['path'] ?? '/');
            if ($newQuery !== '') {
                $cleanUrl .= '?' . $newQuery;
            }
            return redirect()->to($cleanUrl);
        }
    }

    return redirect()->back();
})->name('lang.switch');

// CV Pro Enterprise SaaS Studio & Public Routes
Route::get('/cv-pro', [CvProController::class, 'index'])->name('cv-pro.index');
Route::get('/cv/{slug}', [CvProController::class, 'show'])->name('cv-pro.show');

// CV Pro APIs
Route::prefix('api/cv-pro')->group(function () {
    Route::post('/save', [CvProApiController::class, 'save'])->middleware('throttle:60,1');
    Route::post('/lint', [CvProApiController::class, 'lint'])->middleware('throttle:60,1');
    Route::post('/interview/generate', [CvProApiController::class, 'generateInterview'])->middleware('throttle:30,1');
    Route::post('/interview/evaluate', [CvProApiController::class, 'evaluateAnswer'])->middleware('throttle:30,1');
    Route::post('/outreach/generate', [CvProApiController::class, 'generateOutreach'])->middleware('throttle:30,1');
    Route::post('/upload-cv', [CvProApiController::class, 'uploadCv'])->middleware('throttle:30,1');
    Route::post('/tailor', [CvProApiController::class, 'tailor'])->middleware('throttle:30,1');
    Route::post('/linkedin', [CvProApiController::class, 'generateLinkedIn'])->middleware('throttle:30,1');
    Route::post('/ai-helper', [CvProApiController::class, 'aiHelper'])->middleware('throttle:60,1');
    Route::post('/realtime-copilot', [CvProApiController::class, 'realtimeCopilot'])->middleware('throttle:30,1');
    Route::post('/portfolio/generate', [CvProApiController::class, 'generatePortfolio'])->middleware('throttle:30,1');
    Route::post('/sosmed/generate', [CvProApiController::class, 'generateSosmed'])->middleware('throttle:30,1');
    Route::post('/transcript/analyze', [CvProApiController::class, 'analyzeTranscript'])->middleware('throttle:30,1');
    Route::get('/pricing', [CvProApiController::class, 'pricing'])->middleware('throttle:60,1');
    Route::get('/quota', [CvProApiController::class, 'quota'])->middleware('throttle:60,1');
    Route::post('/topup', [CvProApiController::class, 'topup'])->middleware('throttle:30,1');
    Route::post('/purge-data', [CvProApiController::class, 'purgeData'])->middleware('throttle:30,1');
    Route::get('/compliance-status', [CvProApiController::class, 'complianceStatus'])->middleware('throttle:60,1');
});

// Dynamic Search Engine & GEO AI Citation Architecture: Sitemap.xml, Robots.txt, llms.txt & IndexNow
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [PublicExploreArchitectureController::class, 'llmsTxt'])->name('geo.llms');
Route::get('/llms-full.txt', [PublicExploreArchitectureController::class, 'llmsFullTxt'])->name('geo.llms-full');
Route::get('/indexnow-key.txt', [PublicExploreArchitectureController::class, 'indexNowKeyTxt'])->name('geo.indexnow-key');

// Legal Policies (Terms of Service, Privacy Policy, Refund & Warranty Policy)
Route::get('/terms', [LegalPolicyController::class, 'show'])->defaults('type', 'terms')->name('legal.terms');
Route::get('/terms-and-conditions', [LegalPolicyController::class, 'show'])->defaults('type', 'terms');
Route::get('/syarat-ketentuan', [LegalPolicyController::class, 'show'])->defaults('type', 'terms');

Route::get('/privacy-policy', [LegalPolicyController::class, 'show'])->defaults('type', 'privacy-policy')->name('legal.privacy');
Route::get('/privacy', [LegalPolicyController::class, 'show'])->defaults('type', 'privacy-policy');
Route::get('/kebijakan-privasi', [LegalPolicyController::class, 'show'])->defaults('type', 'privacy-policy');

Route::get('/refund-policy', [LegalPolicyController::class, 'show'])->defaults('type', 'refund-policy')->name('legal.refund');
Route::get('/refund', [LegalPolicyController::class, 'show'])->defaults('type', 'refund-policy');
Route::get('/kebijakan-pengembalian', [LegalPolicyController::class, 'show'])->defaults('type', 'refund-policy');

// High-Retention Customer Smart Guide (Rights & Obligations Matrix)
Route::get('/smart-guide', [LegalPolicyController::class, 'guide'])->name('legal.smart-guide');
Route::get('/panduan-klien', [LegalPolicyController::class, 'guide']);
Route::get('/customer-guide', [LegalPolicyController::class, 'guide']);

// Project OS v10.0 & v11.0 — Programmatic SEO/GEO Architecture Library & Verified Aniwall Showcase
Route::get('/explore', [PublicExploreArchitectureController::class, 'index'])->name('explore.index');
Route::get('/explore/og/{slug}.png', [PublicExploreArchitectureController::class, 'openGraphPng'])->name('explore.og.png');
Route::get('/explore/og/{slug}.svg', [PublicExploreArchitectureController::class, 'openGraphSvg'])->name('explore.og');
Route::get('/explore/{sector}', [PublicExploreArchitectureController::class, 'index'])->name('explore.sector');
Route::get('/explore/{sector}/{slug}', [PublicExploreArchitectureController::class, 'show'])->name('explore.show');

// Project OS v10.0 — SaaS Commercial, Billing Webhook, Multi-Seat Workspace & Impact Grant APIs
Route::prefix('api/saas')->group(function () {
    Route::get('/feature-gate/{slug?}', [PublicExploreArchitectureController::class, 'featureGate'])
        ->middleware('throttle:60,1')
        ->name('api.saas.feature-gate');
    Route::post('/billing/webhook', [PublicExploreArchitectureController::class, 'billingWebhook'])
        ->withoutMiddleware([ValidateCsrfToken::class])
        ->middleware('throttle:60,1')
        ->name('api.saas.billing.webhook');
    Route::middleware('auth')->group(function () {
        Route::post('/workspace', [PublicExploreArchitectureController::class, 'upsertWorkspace'])
            ->middleware('throttle:30,1')
            ->name('api.saas.workspace.upsert');
        Route::post('/workspaces', [PublicExploreArchitectureController::class, 'upsertWorkspace'])
            ->middleware('throttle:30,1')
            ->name('api.saas.workspaces.upsert');
        Route::post('/workspace/invite', [PublicExploreArchitectureController::class, 'inviteWorkspaceMember'])
            ->middleware('throttle:20,1')
            ->name('api.saas.workspace.invite');
        Route::post('/workspaces/{workspaceId}/members', [PublicExploreArchitectureController::class, 'inviteWorkspaceMember'])
            ->middleware('throttle:20,1')
            ->name('api.saas.workspaces.members.invite');
        Route::post('/impact-grant', [PublicExploreArchitectureController::class, 'submitImpactGrant'])
            ->middleware('throttle:10,1')
            ->name('api.saas.impact-grant.submit');
        Route::post('/impact-grants', [PublicExploreArchitectureController::class, 'submitImpactGrant'])
            ->middleware('throttle:10,1')
            ->name('api.saas.impact-grants.submit');
        Route::get('/impact-grants/{id}/document', [PublicExploreArchitectureController::class, 'downloadGrantDocument'])
            ->middleware(['auth', 'throttle:30,1'])
            ->name('admin.impact-grant.document');
        Route::post('/testimonials/submit', [PublicExploreArchitectureController::class, 'updateTestimonialQuote'])
            ->middleware('throttle:20,1')
            ->name('api.saas.testimonials.submit');
        Route::post('/sla-tickets', [PublicExploreArchitectureController::class, 'submitSlaMaintenanceTicket'])
            ->middleware('throttle:20,1')
            ->name('api.saas.sla-tickets.submit');
        Route::match(['get', 'post'], '/privacy/export', [PublicExploreArchitectureController::class, 'exportPrivacyPortability'])
            ->middleware('throttle:10,1')
            ->name('api.saas.privacy.export');
        Route::post('/privacy/redact', [PublicExploreArchitectureController::class, 'redactPrivacyRightToErasure'])
            ->middleware('throttle:5,1')
            ->name('api.saas.privacy.redact');
        Route::post('/sandbox/simulate-webhook', [PublicExploreArchitectureController::class, 'simulateSandboxWebhook'])
            ->middleware('throttle:30,1')
            ->name('api.saas.sandbox.simulate-webhook');
        Route::get('/holding/{workspaceId}/consolidated', [PublicExploreArchitectureController::class, 'holdingConsolidationReport'])
            ->middleware('throttle:30,1')
            ->name('api.saas.holding.consolidated');
    });
});

// Fallback dynamic route for CMS pages (Must be at the very bottom)
Route::get('/{slug?}', [PageController::class, 'show'])->where('slug', '.*');
