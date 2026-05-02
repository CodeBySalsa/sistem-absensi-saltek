<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    use HasFactory;

    /**
     * guarded digunakan agar semua kolom bisa diisi secara massal (Mass Assignment).
     * Berdasarkan image_49e2a1.png, tabel ini memiliki kolom: 
     * nip, nama_lengkap, jabatan, no_hp, dan user_id.
     */
    protected $guarded = [];

    /**
     * Relasi ke User: Satu Karyawan terhubung ke satu akun User (untuk login).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Absensi: Satu Karyawan memiliki banyak data Absensi.
     * Nama fungsi diubah menjadi 'absensi' agar sinkron dengan 
     * DashboardController yang menggunakan withCount(['absensi']).
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }
}