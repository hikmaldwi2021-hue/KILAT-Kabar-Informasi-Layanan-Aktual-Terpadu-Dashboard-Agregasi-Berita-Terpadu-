<?php

use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\RingkasanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['api.token', 'throttle:api-client'])
    ->group(function () {

        Route::get('/berita', [BeritaController::class, 'index']);
        Route::get('/berita/{id}', [BeritaController::class, 'show']);
        
        Route::get('/ringkasan', [RingkasanController::class, 'index']);
        
    });