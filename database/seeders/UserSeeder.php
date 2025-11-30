<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Kasir
        DB::table('users')->insert([
            'name' => 'Kasir Warungin',
            'email' => 'kasir@warungin.com',
            'password' => Hash::make('password123'), // Passwordnya: password123
            'role' => 'cashier',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Buat Akun Owner (Opsional)
        DB::table('users')->insert([
            'name' => 'Pak Bos',
            'email' => 'owner@warungin.com',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}