<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DownloadCodeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return Inertia::render('Redeem/Form');
})->name('codes.show-form');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
    
    // FTP upload routes
    Route::get('/files/ftp/scan', [FileController::class, 'scanFtpStaging'])->name('files.scan-ftp');
    Route::post('/files/ftp/process', [FileController::class, 'processFtpFiles'])->name('files.process-ftp');
    Route::get('/files/ftp/history', [FileController::class, 'getFtpProcessingHistory'])->name('files.ftp-history');
    
    // Download code routes
    Route::get('/codes', [DownloadCodeController::class, 'allCodes'])->name('codes.index');
    Route::get('/files/{file}/codes', [DownloadCodeController::class, 'index'])->name('codes.file');
    Route::post('/files/{file}/codes', [DownloadCodeController::class, 'store'])->name('codes.store');
    Route::get('/files/{file}/codes/export', [DownloadCodeController::class, 'export'])->name('codes.export');
Route::post('/files/{file}/codes/export-cards', [DownloadCodeController::class, 'exportCards'])->name('codes.export-cards');
    Route::get('/codes/{code}/qr', [DownloadCodeController::class, 'generateQr'])->name('codes.qr');
    Route::delete('/codes/{code}', [DownloadCodeController::class, 'destroy'])->name('codes.destroy');
    Route::patch('/codes/{code}/renew', [DownloadCodeController::class, 'renew'])->name('codes.renew');
});

// Public route for contact support
Route::post('/contact', [\App\Http\Controllers\SupportController::class, 'store'])->name('support.send');

// Public route for code redemption
Route::post('/redeem', [DownloadCodeController::class, 'redeem'])->name('codes.redeem');

Route::get('/test-spaces', function () {
    try {
        $result = Storage::disk('spaces')->put('test-laravel.txt', 'hello from laravel');
        return $result ? 'Success' : 'Failure';
    } catch (\Throwable $e) {
        return 'Exception: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine();
    }
});

Route::get('/hello', function () {
    return 'Hello World';
});

require __DIR__.'/auth.php';
