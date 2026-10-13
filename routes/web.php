<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\VisionBlueprintController;
use App\Http\Controllers\PageController;

use App\Http\Controllers\BlueprintController;

Route::post('/api/onboarding', [OnboardingController::class, 'store']);
Route::post('/api/vision-blueprint', [VisionBlueprintController::class, 'store'])->middleware('throttle:30,1');

Route::get('/document/{document}/preview', [DocumentController::class, 'preview'])
    ->name('document.preview')
    ->middleware(['web']);

Route::get('/document/{document}/sign', \App\Livewire\DocumentSignature::class)
    ->name('document.sign')
    ->middleware(['web']);

// Multi-AI Model Orchestrator & Health Telemetry
Route::get('/api/ai/models', function () {
    return response()->json([
        'success' => true,
        'models' => \App\Services\Ai\MultiAiModelManager::getCatalog(),
    ]);
})->name('api.ai.models');

// Project OS: Tech Proposal & Ultimate PRD Routes
Route::post('/api/blueprint/analyze-idea', [BlueprintController::class, 'analyzeIdea'])->name('api.blueprint.analyze-idea')->middleware('throttle:15,1');
Route::post('/api/blueprint/supplement-idea', [BlueprintController::class, 'supplementIdea'])->name('api.blueprint.supplement-idea')->middleware('throttle:30,1');
Route::post('/api/blueprint/autosave', [BlueprintController::class, 'autoSave'])->name('api.blueprint.autosave')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/tweak-segment', [BlueprintController::class, 'tweakSegment'])->name('api.blueprint.tweak-segment')->middleware('throttle:60,1');
Route::get('/api/blueprint/{slug}/source-corpus', [BlueprintController::class, 'getSourceCorpus'])->name('api.blueprint.source-corpus')->middleware('throttle:60,1');
Route::post('/api/blueprint/reset', [BlueprintController::class, 'resetDraft'])->name('api.blueprint.reset')->middleware('throttle:30,1');
Route::get('/blueprint', [BlueprintController::class, 'create'])->name('blueprint.create');
Route::get('/blueprint/{slug}', [BlueprintController::class, 'show'])->name('blueprint.show');
Route::post('/blueprint/{slug}/generate-contract', [BlueprintController::class, 'generateContract'])->name('blueprint.generate-contract');
Route::post('/blueprint/{slug}/snap-token', [BlueprintController::class, 'getSnapToken'])->name('blueprint.snap-token');
Route::post('/blueprint/{slug}/pelunasan-snap-token', [BlueprintController::class, 'getPelunasanSnapToken'])->name('blueprint.pelunasan-snap-token');
Route::post('/blueprint/{slug}/voucher/validate', [BlueprintController::class, 'validateVoucher'])->name('blueprint.voucher.validate');
Route::post('/blueprint/{slug}/voucher/claim', [BlueprintController::class, 'claimVoucher'])->name('blueprint.voucher.claim');
Route::get('/blueprint/{slug}/download/pdf', [BlueprintController::class, 'downloadPdf'])->name('blueprint.download-pdf');
Route::get('/blueprint/{slug}/download/md', [BlueprintController::class, 'downloadMd'])->name('blueprint.download-md');
Route::get('/blueprint/{slug}/download/cursorrules', [BlueprintController::class, 'downloadCursorrules'])->name('blueprint.download-cursorrules');
Route::get('/blueprint/{slug}/raw-md', [BlueprintController::class, 'rawMd'])->name('blueprint.raw-md');
Route::get('/blueprint/{slug}/export/scaffold', [BlueprintController::class, 'exportScaffold'])->name('blueprint.export-scaffold');
Route::get('/blueprint/{slug}/scaffold/preview', [BlueprintController::class, 'previewScaffold'])->name('blueprint.scaffold.preview');
Route::get('/blueprint/{slug}/scaffold-preview', [BlueprintController::class, 'previewScaffold'])->name('blueprint.scaffold-preview');
Route::post('/blueprint/{slug}/scaffold/save-file', [BlueprintController::class, 'saveScaffoldFile'])->name('blueprint.scaffold.save-file');
Route::post('/blueprint/{slug}/scaffold/reset-file', [BlueprintController::class, 'resetScaffoldFile'])->name('blueprint.scaffold.reset-file');
Route::post('/blueprint/{slug}/scaffold/refine-file', [BlueprintController::class, 'refineScaffoldFile'])->name('blueprint.scaffold.refine-file')->middleware('throttle:60,1');
Route::post('/api/blueprint/{slug}/presence', [BlueprintController::class, 'updatePresence'])->name('api.blueprint.presence.update');
Route::get('/api/blueprint/{slug}/presence', [BlueprintController::class, 'getPresence'])->name('api.blueprint.presence.get');
Route::post('/blueprint/{slug}/tasks/update', [BlueprintController::class, 'updateTasks'])->name('blueprint.tasks.update');
Route::post('/api/blueprint/{slug}/checkpoint', [BlueprintController::class, 'updateCheckpoint'])->name('api.blueprint.checkpoint');
Route::post('/api/pricing/inquiry', [BlueprintController::class, 'pricingInquiry'])->name('api.pricing.inquiry')->middleware('throttle:30,1');

// Project Thread / UAT Ticketing Routes
Route::post('/blueprint/{slug}/issues', [App\Http\Controllers\ProjectThreadController::class, 'storeIssue'])->name('blueprint.issues.store');
Route::post('/blueprint/{slug}/issues/{issueId}/comments', [App\Http\Controllers\ProjectThreadController::class, 'storeComment'])->name('blueprint.issues.comments.store');
// Payment Gateway Webhooks (Midtrans DLQ Handler & Notification Aliases)
Route::match(['get', 'post'], '/api/webhook/midtrans', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('get')) {
        return app(\App\Http\Controllers\MidtransWebhookController::class)->status();
    }
    return app(\App\Http\Controllers\MidtransWebhookController::class)->handle($request);
})->name('webhook.midtrans');

Route::match(['get', 'post'], '/webhook/midtrans', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('get')) {
        return app(\App\Http\Controllers\MidtransWebhookController::class)->status();
    }
    return app(\App\Http\Controllers\MidtransWebhookController::class)->handle($request);
});

Route::match(['get', 'post'], '/api/midtrans/notification', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('get')) {
        return app(\App\Http\Controllers\MidtransWebhookController::class)->status();
    }
    return app(\App\Http\Controllers\MidtransWebhookController::class)->handle($request);
});

Route::match(['get', 'post'], '/api/midtrans/webhook', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('get')) {
        return app(\App\Http\Controllers\MidtransWebhookController::class)->status();
    }
    return app(\App\Http\Controllers\MidtransWebhookController::class)->handle($request);
});

// Cart & Project Escrow Checkout Routes
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::get('/api/cart', [\App\Http\Controllers\CartController::class, 'apiCart'])->name('api.cart');
Route::post('/cart/add/{slug}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{slug}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/snap-token', [\App\Http\Controllers\CartController::class, 'getSnapToken'])->name('cart.snap-token');
Route::post('/cart/voucher/apply', [\App\Http\Controllers\CartController::class, 'applyVoucher'])->name('cart.voucher.apply');
Route::post('/cart/voucher/remove', [\App\Http\Controllers\CartController::class, 'removeVoucher'])->name('cart.voucher.remove');
Route::post('/cart/claim-free-grant', [\App\Http\Controllers\CartController::class, 'claimFreeGrant'])->name('cart.claim-free');
Route::post('/cart/reset-pending', [\App\Http\Controllers\CartController::class, 'resetPendingOrder'])->name('cart.reset-pending');

Route::get('/invite/{slug}', \App\Livewire\ClientInviteForm::class)->name('invite');

// Customer & Client Dashboard Portal
Route::get('/customer/dashboard', [\App\Http\Controllers\CustomerDashboardController::class, 'index'])->name('customer.dashboard');
Route::get('/customer/transaction/{id}/invoice', [\App\Http\Controllers\CustomerDashboardController::class, 'invoice'])->name('customer.transaction.invoice');
Route::post('/customer/transaction/{id}/sync', [\App\Http\Controllers\CustomerDashboardController::class, 'syncTransaction'])->name('customer.transaction.sync')->middleware('auth');
Route::get('/portal', fn () => redirect()->route('customer.dashboard'))->name('portal');

// Customer Passwordless OTP Authentication Routes
Route::get('/customer/login', function () {
    if (auth()->check()) {
        return redirect()->route('customer.dashboard');
    }
    return view('auth.customer-login');
})->name('customer.login');

Route::prefix('api/customer')->group(function () {
    Route::post('/otp/request', [\App\Http\Controllers\CustomerAuthController::class, 'requestOtp'])->middleware('throttle:10,1');
    Route::post('/otp/verify', [\App\Http\Controllers\CustomerAuthController::class, 'verifyOtp'])->middleware('throttle:15,1');
    Route::get('/status', [\App\Http\Controllers\CustomerAuthController::class, 'status']);
    Route::post('/logout', [\App\Http\Controllers\CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::post('/profile', [\App\Http\Controllers\CustomerDashboardController::class, 'updateProfile'])->middleware('auth')->name('customer.profile.update');
});

// Authentication Aliases for Standard Web Routes
Route::get('/login', function () {
    return redirect()->to('/admin/login');
})->name('login');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->to('/');
})->name('logout');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'], true)) {
        session(['locale' => $locale]);
        cookie()->queue(cookie('neriah_locale', $locale, 60 * 24 * 365, '/', null, false, false));
        cookie()->queue(cookie('neriah_locale_manual', '1', 60 * 24 * 365, '/', null, false, false));
        cookie()->queue(cookie()->forget('googtrans'));
    }
    return redirect()->back();
})->name('lang.switch');

// CV Pro Enterprise SaaS Studio & Public Routes
Route::get('/cv-pro', [\App\Http\Controllers\CvProController::class, 'index'])->name('cv-pro.index');
Route::get('/cv/{slug}', [\App\Http\Controllers\CvProController::class, 'show'])->name('cv-pro.show');

// CV Pro APIs
Route::prefix('api/cv-pro')->group(function () {
    Route::post('/save', [\App\Http\Controllers\Api\CvProApiController::class, 'save'])->middleware('throttle:60,1');
    Route::post('/lint', [\App\Http\Controllers\Api\CvProApiController::class, 'lint'])->middleware('throttle:60,1');
    Route::post('/interview/generate', [\App\Http\Controllers\Api\CvProApiController::class, 'generateInterview'])->middleware('throttle:30,1');
    Route::post('/interview/evaluate', [\App\Http\Controllers\Api\CvProApiController::class, 'evaluateAnswer'])->middleware('throttle:30,1');
    Route::post('/outreach/generate', [\App\Http\Controllers\Api\CvProApiController::class, 'generateOutreach'])->middleware('throttle:30,1');
    Route::post('/upload-cv', [\App\Http\Controllers\Api\CvProApiController::class, 'uploadCv'])->middleware('throttle:30,1');
    Route::post('/tailor', [\App\Http\Controllers\Api\CvProApiController::class, 'tailor'])->middleware('throttle:30,1');
    Route::post('/linkedin', [\App\Http\Controllers\Api\CvProApiController::class, 'generateLinkedIn'])->middleware('throttle:30,1');
    Route::post('/ai-helper', [\App\Http\Controllers\Api\CvProApiController::class, 'aiHelper'])->middleware('throttle:60,1');
    Route::post('/realtime-copilot', [\App\Http\Controllers\Api\CvProApiController::class, 'realtimeCopilot'])->middleware('throttle:30,1');
    Route::post('/portfolio/generate', [\App\Http\Controllers\Api\CvProApiController::class, 'generatePortfolio'])->middleware('throttle:30,1');
    Route::post('/sosmed/generate', [\App\Http\Controllers\Api\CvProApiController::class, 'generateSosmed'])->middleware('throttle:30,1');
    Route::post('/transcript/analyze', [\App\Http\Controllers\Api\CvProApiController::class, 'analyzeTranscript'])->middleware('throttle:30,1');
    Route::get('/pricing', [\App\Http\Controllers\Api\CvProApiController::class, 'pricing'])->middleware('throttle:60,1');
    Route::get('/quota', [\App\Http\Controllers\Api\CvProApiController::class, 'quota'])->middleware('throttle:60,1');
    Route::post('/topup', [\App\Http\Controllers\Api\CvProApiController::class, 'topup'])->middleware('throttle:30,1');
});

// Dynamic Search Engine Architecture: Sitemap.xml & Robots.txt
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

// Legal Policies (Terms of Service, Privacy Policy, Refund & Warranty Policy)
Route::get('/terms', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'terms')->name('legal.terms');
Route::get('/terms-and-conditions', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'terms');
Route::get('/syarat-ketentuan', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'terms');

Route::get('/privacy-policy', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'privacy-policy')->name('legal.privacy');
Route::get('/privacy', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'privacy-policy');
Route::get('/kebijakan-privasi', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'privacy-policy');

Route::get('/refund-policy', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'refund-policy')->name('legal.refund');
Route::get('/refund', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'refund-policy');
Route::get('/kebijakan-pengembalian', [\App\Http\Controllers\LegalPolicyController::class, 'show'])->defaults('type', 'refund-policy');

// High-Retention Customer Smart Guide (Rights & Obligations Matrix)
Route::get('/smart-guide', [\App\Http\Controllers\LegalPolicyController::class, 'guide'])->name('legal.smart-guide');
Route::get('/panduan-klien', [\App\Http\Controllers\LegalPolicyController::class, 'guide']);
Route::get('/customer-guide', [\App\Http\Controllers\LegalPolicyController::class, 'guide']);

// Fallback dynamic route for CMS pages (Must be at the very bottom)
Route::get('/{slug?}', [PageController::class, 'show'])->where('slug', '.*');
