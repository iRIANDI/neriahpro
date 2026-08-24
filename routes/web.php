<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

use App\Http\Controllers\Api\OnboardingController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/onboarding', [OnboardingController::class, 'store']);

Route::get('/document/{document}/preview', [DocumentController::class, 'preview'])
    ->name('document.preview')
    ->middleware(['web']);

Route::get('/invite/{slug}', \App\Livewire\ClientInviteForm::class)->name('invite');
