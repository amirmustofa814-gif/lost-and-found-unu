<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'created_by', 'updated_by'];

    // Relasi ke barang hilang
    public function lostItems()
    {
        return $this->hasMany(LostItem::class);
    }

    // Relasi ke barang temuan
    public function foundItems()
    {
        return $this->hasMany(FoundItem::class);
    }
}