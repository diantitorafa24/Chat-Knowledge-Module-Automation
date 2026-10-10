<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatLogController;
use App\Http\Controllers\ChunkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FeedbackListController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleVersionController;
use App\Http\Controllers\VersionListController;
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
    Route::post('/modules/{module}/versions/{version}/activate', [ModuleVersionController::class, 'activate'])->name('versions.activate');
    Route::post('/modules/{module}/versions/{version}/deactivate', [ModuleVersionController::class, 'deactivate'])->name('versions.deactivate');
    Route::get('/versions', [VersionListController::class, 'index'])->name('versions.index');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents/{document}/reprocess', [DocumentController::class, 'reprocess'])->name('documents.reprocess');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/processing', [DocumentController::class, 'processing'])->name('processing.index');
    Route::get('/chunks', [ChunkController::class, 'index'])->name('chunks.index');
    Route::get('/chat-logs', [ChatLogController::class, 'index'])->name('chat-logs.index');
    Route::get('/unanswered-questions', [ChatLogController::class, 'unanswered'])->name('chat-logs.unanswered');
    Route::get('/feedback', [FeedbackListController::class, 'index'])->name('feedback.index');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

Route::get('/fix-images', function (\App\Services\ImageVisionService $vision) {
    $images = \App\Models\DocumentImage::where('description', '(Deskripsi gambar tidak tersedia)')->get();
    $count = 0;
    foreach ($images as $img) {
        if (!\Illuminate\Support\Facades\Storage::exists($img->storage_path)) continue;
        
        $data = base64_encode(\Illuminate\Support\Facades\Storage::get($img->storage_path));
        $desc = $vision->describe($data, $img->mime_type, $img->section);
        
        if ($desc !== '(Deskripsi gambar tidak tersedia)') {
            $img->update(['description' => $desc]);
            
            // Update the chunk text as well!
            $chunk = \App\Models\DocumentChunk::where('document_id', $img->document_id)
                ->where('text', 'LIKE', '%[IMG_REF:' . $img->id . ']%')
                ->first();
                
            if ($chunk) {
                $chunk->update(['text' => "Lampiran Gambar: " . $desc . " [IMG_REF:" . $img->id . "]"]);
                // update qdrant point
                app(\App\Services\ChunkIndexer::class)->index($chunk->document);
            }
            $count++;
        }
    }
    return "Fixed $count images";
});
