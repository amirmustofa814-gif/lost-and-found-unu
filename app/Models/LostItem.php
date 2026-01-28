<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_name',
        'description',
        'phone_number',
        'location_lost',
        'date_lost',
        // 'image_path' DIHAPUS DARI SINI
        'status',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke banyak foto
    public function images()
    {
        return $this->hasMany(ItemImage::class, 'lost_item_id');
    }

    // Helper: Ambil foto utama saja (untuk thumbnail)
    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class, 'lost_item_id')->where('is_primary', true)->latestOfMany();
    }
}