<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('claims', function (Blueprint $table) {
        // Cek dulu, kalau kolom description belum ada, buat dulu
        if (!Schema::hasColumn('claims', 'description')) {
            $table->text('description')->nullable()->after('found_item_id');
        }

        // Buat kolom foto bukti
        if (!Schema::hasColumn('claims', 'proof_image')) {
            $table->string('proof_image')->nullable()->after('description');
        }
    });
}

public function down()
{
    Schema::table('claims', function (Blueprint $table) {
        $table->dropColumn(['proof_image']);
    });
}
};