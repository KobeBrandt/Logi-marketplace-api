<?php

use App\Http\Controllers\IconPackController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('cache.headers:public;max_age=300;etag')
    ->group(function () {
        Route::get('/plugins', [PluginController::class, 'index']);
        Route::get('/plugins/{name}', [PluginController::class, 'show'])->where('name', '[^/]+');

        Route::get('/icon-packs', [IconPackController::class, 'index']);
        Route::get('/icon-packs/{name}', [IconPackController::class, 'show'])->where('name', '[^/]+');

        Route::get('/profiles', [ProfileController::class, 'index']);
        Route::get('/profiles/{name}', [ProfileController::class, 'show'])->where('name', '[^/]+');
    });
