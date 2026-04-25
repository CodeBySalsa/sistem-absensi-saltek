<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel karyawans.
     */
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique(); // Nomor Induk Pegawai, harus unik
            $table->string('nama_lengkap');
            $table->string('jabatan');
            $table->string('no_hp')->nullable(); // Boleh kosong dulu
            
            // Menghubungkan ke tabel users (untuk login)
            // Jika user dihapus, data karyawan di sini juga ikut terhapus (cascade)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            $table->timestamps(); // Mencatat waktu data dibuat & diupdate
        });
    }

    /**
     * Batalkan migrasi (Hapus tabel).
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};