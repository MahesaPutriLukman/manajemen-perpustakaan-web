<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@library.com',
            'role' => 'admin',
            'password' => Hash::make('abc'), 
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'petugas@library.com',
            'role' => 'pegawai',
            'password' => Hash::make('def'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Contoh Mahasiswa',
            'email' => 'mahasiswa@unhas.ac.id', 
            'role' => 'mahasiswa',
            'password' => Hash::make('ghi'),
            'email_verified_at' => now(),
        ]);
    }
}