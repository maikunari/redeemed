<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DownloadCodeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File management routes
    Route::resource('files', FileController::class)->only(['index', 'store', 'destroy']);
    
    // Download code routes
    Route::post('/files/{file}/codes', [DownloadCodeController::class, 'store'])->name('codes.store');
    Route::get('/files/{file}/codes/export', [DownloadCodeController::class, 'export'])->name('codes.export');
    Route::get('/codes/{code}/qr', [DownloadCodeController::class, 'generateQrCode'])->name('codes.qr');
});

// Public routes for code redemption
Route::get('/redeem', function () {
    return Inertia::render('Redeem/Form');
})->name('codes.redeem-form');

Route::post('/redeem', [DownloadCodeController::class, 'redeem'])->name('codes.redeem');

require __DIR__.'/auth.php';
