<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    /**
     * guarded digunakan agar semua kolom bisa diisi secara massal.
     */
    protected $guarded = [];

    /**
     * Menghubungkan Karyawan ke tabel User (untuk login).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menghubungkan Karyawan ke banyak data Absensi.
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}