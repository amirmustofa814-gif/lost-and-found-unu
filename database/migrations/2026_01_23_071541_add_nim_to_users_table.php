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
        // Cek dulu: Kalau kolom 'nim' TIDAK ADA, baru kita buat.
        if (!Schema::hasColumn('users', 'nim')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nim')->unique()->after('name');
            });
        }
    }

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('nim');
    });
}
};
