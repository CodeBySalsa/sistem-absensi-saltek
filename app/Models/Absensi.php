<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang didefinisikan secara eksplisit.
     */
    protected $table = 'absensis';

    /**
     * Mass assignment protection.
     * Semua kolom yang dikirim dari DashboardController sudah terdaftar di sini.
     */
    protected $fillable = [
        'karyawan_id',
        'user_id',     
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',      // Status: Hadir, Terlambat, Izin, Sakit, Selesai
        'latitude',    // Koordinat lokasi presensi PT Saltek
        'longitude',   // Koordinat lokasi presensi PT Saltek
        'keterangan',  // Catatan alasan izin/sakit atau info sistem
    ];

    /**
     * Casting atribut agar otomatis menjadi tipe data tertentu.
     * Ini memudahkan manipulasi tanggal di Dashboard.
     */
    protected $casts = [
        //'tanggal' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Menghubungkan data Absensi ke User.
     * Digunakan untuk mengambil data user (email/nama akun) langsung dari tabel absensi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Menghubungkan data Absensi ke profil Karyawan.
     * Relasi ini sangat penting untuk Dashboard Monitoring Admin.
     */
    public function karyawan(): BelongsTo
    {
        /**
         * withDefault() sangat membantu untuk mencegah aplikasi crash 
         * jika relasi data sedang bermasalah.
         */
        return $this->belongsTo(Karyawan::class, 'karyawan_id')->withDefault([
            'nama_lengkap' => 'Karyawan Tidak Ditemukan',
            'jabatan' => '-'
        ]);
    }
}