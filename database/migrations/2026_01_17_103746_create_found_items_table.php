<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('found_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User & Kategori
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            // Data Barang (Tanpa image_path)
            $table->string('item_name');
            $table->text('description');
            $table->string('location_found');
            $table->date('date_found');
            
            // Posisi & Status
            $table->string('current_position')->default('Pos Satpam'); 
            $table->enum('status', ['tersedia', 'diklaim', 'diambil'])->default('tersedia');

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_items');
    }
};