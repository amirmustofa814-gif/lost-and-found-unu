<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key ke LostItem ATAU FoundItem (Nullable)
            $table->foreignId('lost_item_id')->nullable()->constrained('lost_items')->onDelete('cascade');
            $table->foreignId('found_item_id')->nullable()->constrained('found_items')->onDelete('cascade');
            
            $table->string('image_path'); // Lokasi file
            $table->boolean('is_primary')->default(false); // Penanda foto utama
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
};