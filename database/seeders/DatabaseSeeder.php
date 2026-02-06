<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun ADMIN
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@kampus.ac.id',
            'role' => 'admin', 
            'phone_number' => '081234567890',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat Akun MAHASISWA (Perhatikan role-nya 'mahasiswa')
        User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhs@kampus.ac.id',
            'role' => 'mahasiswa', 
            'phone_number' => '089876543210',
            'password' => Hash::make('password123'),
        ]);
        
        $this->call([
            CategorySeeder::class,
        ]);
    }
}