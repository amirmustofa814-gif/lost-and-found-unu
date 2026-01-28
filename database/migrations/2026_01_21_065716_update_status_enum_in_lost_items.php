<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- Jangan lupa import ini

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Kita ubah kolom status agar menerima kata 'ditemukan'
            // Perintah ini memodifikasi struktur kolom ENUM di database
            DB::statement("ALTER TABLE lost_items MODIFY COLUMN status ENUM('dicari', 'ditemukan', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'dicari'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Jika di-rollback, kembalikan ke pilihan status yang lama
            // (Hati-hati: data yang statusnya 'ditemukan' mungkin akan error atau terpotong jika di-rollback)
            DB::statement("ALTER TABLE lost_items MODIFY COLUMN status ENUM('dicari', 'selesai') NOT NULL DEFAULT 'dicari'");
        });
    }
};