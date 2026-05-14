<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DriveController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root: redirect to dashboard if logged in, otherwise to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DriveController::class, 'index'])->name('dashboard');
    Route::get('/recent', [DriveController::class, 'recent'])->name('recent');
    Route::get('/starred', [DriveController::class, 'starred'])->name('starred');
    Route::get('/trash', [DriveController::class, 'trash'])->name('trash');
    
    Route::post('/folders', [DriveController::class, 'createFolder'])->name('folders.create');
    Route::put('/folders/{folder}', [DriveController::class, 'renameFolder'])->name('folders.rename');
    Route::delete('/folders/{folder}', [DriveController::class, 'deleteFolder'])->name('folders.delete');
    Route::post('/folders/{id}/restore', [DriveController::class, 'restoreFolder'])->name('folders.restore');
    Route::delete('/folders/{id}/force-delete', [DriveController::class, 'forceDeleteFolder'])->name('folders.force_delete');
    Route::post('/folders/{folder}/star', [DriveController::class, 'toggleStarFolder'])->name('folders.star');
    
    Route::post('/files', [DriveController::class, 'uploadFile'])->name('files.upload');
    Route::get('/files/{file}/download', [DriveController::class, 'downloadFile'])->name('files.download');
    Route::put('/files/{file}', [DriveController::class, 'renameFile'])->name('files.rename');
    Route::delete('/files/{file}', [DriveController::class, 'deleteFile'])->name('files.delete');
    Route::post('/files/{id}/restore', [DriveController::class, 'restoreFile'])->name('files.restore');
    Route::delete('/files/{id}/force-delete', [DriveController::class, 'forceDeleteFile'])->name('files.force_delete');
    Route::post('/files/{file}/star', [DriveController::class, 'toggleStarFile'])->name('files.star');
    Route::get('/live-search', [DriveController::class, 'liveSearch'])->name('live-search');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
