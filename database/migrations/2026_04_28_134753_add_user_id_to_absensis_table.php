<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // 1. Tambahkan user_id (Opsional, jika ingin double tracking)
            if (!Schema::hasColumn('absensis', 'user_id')) {
                $table->foreignId('user_id')
                      ->after('id')
                      ->nullable() 
                      ->constrained()
                      ->onDelete('cascade');
            }

            // 2. TAMBAHKAN KOLOM GPS (Penting untuk fitur Map)
            // Menggunakan string agar lebih fleksibel menangkap koordinat dari browser
            $table->string('latitude')->after('status')->nullable();
            $table->string('longitude')->after('latitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'latitude', 'longitude']);
        });
    }
};