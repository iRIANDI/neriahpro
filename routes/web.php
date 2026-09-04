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

// Fallback dynamic route for CMS pages (Must be at the very bottom)
Route::get('/{slug?}', [PageController::class, 'show'])->where('slug', '.*');
