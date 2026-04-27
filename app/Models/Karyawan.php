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
     * Kita tambahkan 'user_id' agar lebih spesifik mencari foreign key-nya.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Absensi: Satu Karyawan bisa memiliki banyak data Absensi.
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }
}