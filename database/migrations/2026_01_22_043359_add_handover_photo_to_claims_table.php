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
    Schema::table('claims', function (Blueprint $table) {
        // Kolom untuk foto bukti serah terima (saat barang diambil)
        $table->string('handover_photo_path')->nullable()->after('proof_image_path');
    });
}

public function down(): void
{
    Schema::table('claims', function (Blueprint $table) {
        $table->dropColumn('handover_photo_path');
    });
}
};
