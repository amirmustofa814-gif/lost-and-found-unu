<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    // Pastikan semua kolom ini ada agar bisa disimpan (Mass Assignment)
    protected $fillable = [
        'found_item_id',
        'user_id',
        'status',
        'description',
        'proof_image_path',  // <--- KOREKSI: Harus 'proof_image_path' (Sesuai Controller)
        'handover_photo_path',
        'verified_at',
        'verified_by',
        'created_by',
        'updated_by'
    ];

    // Relasi: Barang apa yang diklaim
    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'found_item_id');
    }

    // Relasi: Siapa yang mengklaim
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}