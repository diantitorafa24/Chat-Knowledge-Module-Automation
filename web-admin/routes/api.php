<?php

use App\Http\Controllers\Api\ModuleApiController;
use App\Http\Controllers\Api\RagController;
use App\Http\Controllers\Api\AskController;
use App\Http\Middleware\ApiTokenAuth;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::middleware([ApiTokenAuth::class, 'throttle:60,1'])->group(function () {
    Route::get('/modules', [ModuleApiController::class, 'index']);
    Route::post('/rag/search', [RagController::class, 'search']);
    Route::post('/rag/ask', [AskController::class, 'ask']);
});
