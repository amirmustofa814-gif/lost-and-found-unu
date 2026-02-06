<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'nim' => 'ADMIN-001',       
            'email' => 'admin@kampus.ac.id',
            'phone_number' => '081299999999',
            'password' => Hash::make('password123'),
            'role' => 'admin',         
            'otp_code' => null,          
            'email_verified_at' => now(), 
        ]);
    }
}