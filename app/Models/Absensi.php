<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Tabel yang terhubung dengan model ini.
     * Secara default Laravel akan mencari tabel 'absensis'.
     */
    protected $table = 'absensis';

    /**
     * guarded digunakan agar semua kolom bisa diisi secara massal (Mass Assignment).
     */
    protected $guarded = [];

    /**
     * Menghubungkan data Absensi ke User.
     * Digunakan untuk mengambil data akun dari tabel users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Menghubungkan data Absensi ke Karyawan.
     * RELASI PENTING: Inilah yang menarik data 'nama' ke tabel Dashboard.
     * Pastikan kolom 'karyawan_id' di tabel absensi berisi ID yang ada di tabel karyawans.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}