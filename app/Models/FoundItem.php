<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use HasFactory;

    // INI KUNCI UTAMANYA. Pastikan ejaannya 100% sama dengan migration.
    protected $fillable = [
        'user_id',
        'category_id',
        'item_name',      
        'description',
        'phone_number',
        'location_found',  
        'date_found', 
        'time_found',     
        'current_position',
        'status',
        'created_by',
        'updated_by'
    ];

    // Relasi ke User (Penemu)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relasi ke Foto
    public function images()
    {
        return $this->hasMany(ItemImage::class, 'found_item_id');
    }

    // Helper Foto Utama
    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class, 'found_item_id')->oldestOfMany(); // atau ->latest()
    }
}