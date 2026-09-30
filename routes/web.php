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

// Project OS: Tech Proposal & Ultimate PRD Routes
Route::get('/blueprint', [BlueprintController::class, 'create'])->name('blueprint.create');
Route::get('/blueprint/{slug}', [BlueprintController::class, 'show'])->name('blueprint.show');

Route::get('/invite/{slug}', \App\Livewire\ClientInviteForm::class)->name('invite');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        cookie()->queue(cookie()->forever('neriah_locale', $locale));
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
});

// Fallback dynamic route for CMS pages (Must be at the very bottom)
Route::get('/{slug?}', [PageController::class, 'show'])->where('slug', '.*');
