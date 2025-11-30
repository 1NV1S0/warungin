<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // Loop untuk membuat Meja 1 sampai 5
        for ($i = 1; $i <= 5; $i++) {
            DB::table('tables')->insert([
                'table_number' => 'Meja ' . $i,
                'capacity' => 4,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tambah 1 Meja VIP manual
        DB::table('tables')->insert([
            'table_number' => 'VIP 1',
            'capacity' => 8,
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}