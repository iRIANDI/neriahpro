<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/document/{document}/preview', [DocumentController::class, 'preview'])
    ->name('document.preview')
    ->middleware(['web']);

Route::get('/invite/{slug}', \App\Livewire\ClientInviteForm::class)->name('invite');
