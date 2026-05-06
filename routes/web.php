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
    
    // 1. Dashboard Utama (Monitoring & Statistik)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Fitur Izin/Sakit dari Dashboard
    Route::post('/dashboard/izin-sakit', [DashboardController::class, 'izinSakit'])->name('absensi.izinSakit');

    // 2. Fitur Absensi (Panel Modal & Utama)
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        
        /**
         * PENTING: Route Store diarahkan ke DashboardController::storeAbsen
         * Ini agar proteksi jarak 20 meter dan kalkulasi statistik hari ini berjalan.
         */
        Route::post('/store', [DashboardController::class, 'storeAbsen'])->name('store');
        
        // Menangani request dari tombol "Absen Pulang"
        Route::post('/pulang', [AbsensiController::class, 'pulang'])->name('pulang');
        
        // Update Absensi (untuk keperluan edit/admin)
        Route::put('/update/{id}', [AbsensiController::class, 'update'])->name('update');
    });

    // 3. Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// GROUP 2: KHUSUS ADMIN (Manajemen Karyawan PT Saltek)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('karyawan', KaryawanController::class);
});

require __DIR__.'/auth.php';