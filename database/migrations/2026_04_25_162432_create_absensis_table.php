<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            // ID Karyawan dan ID User (Penting untuk relasi)
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->string('tanggal'); // Pakai string agar SQLite lebih stabil
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable(); // Kita samakan jadi jam_pulang sesuai Controller
            
            $table->string('status'); // Hadir, Terlambat, Izin, Sakit, Selesai
            
            // KOLOM BARU UNTUK GPS (Tadi ini yang kurang)
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            $table->text('keterangan')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};