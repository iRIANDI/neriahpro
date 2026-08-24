<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\PageController;

Route::post('/api/onboarding', [OnboardingController::class, 'store']);

Route::get('/document/{document}/preview', [DocumentController::class, 'preview'])
    ->name('document.preview')
    ->middleware(['web']);

// Fallback dynamic route for CMS pages (Must be at the very bottom)
Route::get('/{slug?}', [PageController::class, 'show'])->where('slug', '.*');

Route::get('/invite/{slug}', \App\Livewire\ClientInviteForm::class)->name('invite');
