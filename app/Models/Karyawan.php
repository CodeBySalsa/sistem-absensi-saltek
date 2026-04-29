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
     */
    protected $guarded = [];

    /**
     * Relasi ke User: Satu Karyawan terhubung ke satu akun User (untuk login).
     * Foreign key 'user_id' menghubungkan data profil ke tabel users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Absensi: Satu Karyawan bisa memiliki banyak data Absensi.
     * Ini digunakan untuk menghitung total hadir, izin, dan sakit di dashboard.
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }
}