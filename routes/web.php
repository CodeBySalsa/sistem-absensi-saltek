<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// GROUP 1: KARYAWAN & MONITORING (Akses Umum setelah Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Fitur Absensi
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::post('/masuk', [AbsensiController::class, 'store'])->name('store');
        Route::put('/pulang/{id}', [AbsensiController::class, 'update'])->name('update');
        Route::post('/izin-sakit', [AbsensiController::class, 'izinSakit'])->name('izinSakit');
    });

    // 3. Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// GROUP 2: KHUSUS ADMIN (Manajemen Karyawan)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('karyawan', KaryawanController::class);
    // Resource route di atas otomatis mencakup: index, create, store, edit, update, destroy
});

require __DIR__.'/auth.php';