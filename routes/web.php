<?php

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController; // Import Controller Absensi
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Rute untuk Fitur Karyawan
 */
Route::get('/karyawan', [KaryawanController::class, 'index']);

/**
 * Rute untuk Fitur Absensi
 */
// Menampilkan halaman atau daftar absensi
Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');

// Menjalankan fungsi simpan absen saat tombol diklik
Route::post('/absen-masuk', [AbsensiController::class, 'store'])->name('absen.masuk');