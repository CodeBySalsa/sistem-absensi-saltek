<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController; 
use App\Http\Controllers\PimpinanController; // Sudah ada di sini
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// GROUP 1: KARYAWAN & MONITORING
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/izin-sakit', [DashboardController::class, 'izinSakit'])->name('absensi.izinSakit');

    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::post('/store', [DashboardController::class, 'storeAbsen'])->name('store');
        Route::post('/pulang', [AbsensiController::class, 'pulang'])->name('pulang');
        Route::put('/update/{id}', [AbsensiController::class, 'update'])->name('update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman Monitoring Pimpinan (SATU SAJA, GUNAKAN PimpinanController)
    Route::get('/pimpinan', [PimpinanController::class, 'index'])->name('pimpinan.index');
});

// GROUP 2: KHUSUS ADMIN
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('karyawan', KaryawanController::class);
});

use Illuminate\Support\Facades\Mail;

Route::get('/test-mail', function () {

    Mail::raw('Tes SMTP Railway', function ($message) {
        $message->to('salsa22bil@gmail.com')
                ->subject('Test SMTP');
    });

    return 'EMAIL BERHASIL';
});
require __DIR__.'/auth.php';