<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invite/{slug}', \App\Livewire\ClientInviteForm::class)->name('invite');
