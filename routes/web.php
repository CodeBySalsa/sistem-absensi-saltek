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
    
    // 1. Dashboard Utama (Monitoring)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Fitur Izin/Sakit dari Dashboard (DIARAHKAN KE DashboardController SESUAI KODE SEBELUMNYA)
    Route::post('/dashboard/izin-sakit', [DashboardController::class, 'izinSakit'])->name('absensi.izinSakit');

    // 2. Fitur Absensi (Panel Modal & Utama)
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        
        // Simpan Absensi Masuk (Digunakan oleh tombol di Modal)
        // Pastikan di AbsensiController@store sudah ada logika jam 08:30 untuk status 'Terlambat'
        Route::post('/store', [AbsensiController::class, 'store'])->name('store');
        
        // Update Absensi Pulang
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