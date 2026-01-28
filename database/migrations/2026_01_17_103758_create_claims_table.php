<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            
            // Relasi: Barang apa yang diklaim & Siapa yang klaim
            $table->foreignId('found_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Proses Validasi
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('proof_description')->nullable(); // Deskripsi bukti kepemilikan
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable(); // Nama Admin/Petugas (Manual text atau ID)

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};