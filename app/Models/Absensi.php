<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    /**
     * guarded digunakan agar semua kolom bisa diisi secara massal (Mass Assignment).
     */
    protected $guarded = [];

    /**
     * Menghubungkan data Absensi kembali ke si Karyawan.
     * Relasi: Satu data absensi dimiliki oleh satu Karyawan.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}