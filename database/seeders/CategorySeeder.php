<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik (HP, Laptop, dll)',
            'Dokumen Penting (KTM, KTP, STNK)',
            'Kunci (Motor, Kost, Rumah)',
            'Dompet & Tas',
            'Pakaian & Aksesoris',
            'Alat Tulis & Buku',
            'Lainnya'
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => Str::slug($cat) 
            ]);
        }
    }
}