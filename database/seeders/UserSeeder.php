<?php

namespace Database\Seeders;

use App\Models\User; // Pastikan baris ini ada
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat Admin Utama
        User::create([
            'name'      => 'Administrator',
            'username'  => 'admin',
            'email'     => 'admin@smkn7be.sch.id',
            'password'  => Hash::make('password'), // Password default: password
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Membuat Contoh Petugas Gudang
        User::create([
            'name'      => 'Petugas Gudang',
            'username'  => 'petugas',
            'email'     => 'petugas@smkn7be.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'petugas',
            'is_active' => true,
        ]);

        // Membuat Contoh Owner (Kepala Sekolah/Manajemen)
        User::create([
            'name'      => 'Owner Inventory',
            'username'  => 'owner',
            'email'     => 'owner@smkn7be.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'owner',
            'is_active' => true,
        ]);
    }
}