<?php

use App\Http\Controllers\KaryawanController; // Baris ini sangat penting!
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk menampilkan daftar karyawan
Route::get('/karyawan', [KaryawanController::class, 'index']);