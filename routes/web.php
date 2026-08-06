<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StudentPhotoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. ROUTE PUBLIK (Bisa diakses siapa saja tanpa login)
|--------------------------------------------------------------------------
*/
// Tampilan awal
Route::get('/', [SearchController::class, 'index'])->name('home');

// Proses pencarian (POST) agar URL tetap bersih tanpa ?nis=xxxx
Route::post('/', [SearchController::class, 'search'])
    ->middleware(['throttle:search'])
    ->name('search');

/*
|--------------------------------------------------------------------------
| 2. ROUTE ADMIN (Wajib Login)
|--------------------------------------------------------------------------
| Semua route di dalam grup ini dilindungi oleh middleware 'auth'.
| Hanya user (admin) yang sudah login yang bisa mengaksesnya.
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Data Foto Siswa (Langkah 9)
    Route::resource('photos', StudentPhotoController::class)->except(['create', 'edit', 'show']);

    // Route Impor Excel (Langkah 10)
    Route::get('/photos/import', [ImportController::class, 'index'])->name('photos.import');
    Route::post('/photos/import', [ImportController::class, 'store'])->name('photos.import.store');

    // Manajemen Profil Admin (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 3. ROUTE AUTENTIKASI
|--------------------------------------------------------------------------
| Memuat route login, logout, & register bawaan Laravel Breeze
*/
require __DIR__.'/auth.php';