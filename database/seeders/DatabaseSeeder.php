<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions (Penting: membersihkan cache sebelum seeding)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Role Spatie Dulu
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleMahasiswa = Role::create(['name' => 'mahasiswa']);

        // Buat Akun ADMIN
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@kampus.ac.id',
            'phone_number' => '081234567890',
            'password' => Hash::make('password123'),
        ]);

        // Assign Role Spatie ke User Admin
        $admin->assignRole($roleAdmin);

        // Buat Akun MAHASISWA (Perhatikan role-nya 'mahasiswa')
        $mahasiswa = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhs@kampus.ac.id', 
            'phone_number' => '089876543210',
            'password' => Hash::make('password123'),
        ]);
        
        // Assign Role Spatie ke User Mahasiswa
        $mahasiswa->assignRole($roleMahasiswa);

        $this->call([
            CategorySeeder::class,
        ]);
    }
}