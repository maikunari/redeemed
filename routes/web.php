<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DownloadCodeController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Redeem/Form');
})->name('codes.show-form');

Route::get('/dashboard', function () {
    $settings = app(SettingsController::class)->index();
    return Inertia::render('Dashboard', [
        'settings' => $settings
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // File management routes
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::patch('/files/{file}', [FileController::class, 'update'])->name('files.update');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::post('/files/{file}/thumbnail', [FileController::class, 'updateThumbnail'])->name('files.update-thumbnail');
    
    // Download code routes
    Route::get('/codes', [DownloadCodeController::class, 'allCodes'])->name('codes.index');
    Route::get('/files/{file}/codes', [DownloadCodeController::class, 'index'])->name('codes.file');
    Route::post('/files/{file}/codes', [DownloadCodeController::class, 'store'])->name('codes.store');
    Route::get('/files/{file}/codes/export', [DownloadCodeController::class, 'export'])->name('codes.export');
    Route::get('/codes/{code}/qr', [DownloadCodeController::class, 'generateQr'])->name('codes.qr');
});

// Public route for code redemption
Route::post('/redeem', [DownloadCodeController::class, 'redeem'])->name('codes.redeem');

require __DIR__.'/auth.php';
