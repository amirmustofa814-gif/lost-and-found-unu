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
        Schema::table('found_items', function (Blueprint $table) {
            // Tambah kolom Waktu (Time) setelah Tanggal
            $table->time('time_found')->nullable()->after('date_found');
        });
    }

    public function down(): void
    {
        Schema::table('found_items', function (Blueprint $table) {
            $table->dropColumn('time_found');
        });
    }
};
