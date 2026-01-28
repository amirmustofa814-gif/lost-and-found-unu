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
        // Tambahkan ke tabel barang hilang
        Schema::table('lost_items', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('description');
        });

        // Tambahkan ke tabel barang temuan
        Schema::table('found_items', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });

        Schema::table('found_items', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};