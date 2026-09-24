<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['create', 'show']);
    Route::resource('modules', ModuleController::class);
    Route::post('/modules/{module}/documents', [DocumentController::class, 'store'])->name('modules.documents.store');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents/{document}/reprocess', [DocumentController::class, 'reprocess'])->name('documents.reprocess');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/processing', [DocumentController::class, 'processing'])->name('processing.index');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
