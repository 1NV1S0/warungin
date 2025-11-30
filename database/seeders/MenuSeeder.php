<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Menu 1: Makanan (Stok Banyak)
        Menu::create([
            'name' => 'Nasi Goreng Spesial',
            'slug' => 'nasi-goreng-spesial',
            'description' => 'Nasi goreng dengan telur, ayam suwir, dan kerupuk.',
            'price' => 25000,
            'stock' => 50,
            'category' => 'makanan',
            'is_available' => true,
        ]);

        // Menu 2: Makanan (Stok Dikit - buat ngetes warning)
        Menu::create([
            'name' => 'Ayam Bakar Madu',
            'slug' => 'ayam-bakar-madu',
            'description' => 'Ayam bakar dengan olesan madu.',
            'price' => 30000,
            'stock' => 3, 
            'category' => 'makanan',
            'is_available' => true,
        ]);

        // Menu 3: Minuman
        Menu::create([
            'name' => 'Es Teh Manis',
            'slug' => 'es-teh-manis',
            'description' => 'Teh manis dingin segar.',
            'price' => 5000,
            'stock' => 100,
            'category' => 'minuman',
            'is_available' => true,
        ]);

        // Menu 4: Minuman (Stok Habis - buat ngetes disable button)
        Menu::create([
            'name' => 'Jus Alpukat',
            'slug' => 'jus-alpukat',
            'description' => 'Jus alpukat kental.',
            'price' => 15000,
            'stock' => 0, 
            'category' => 'minuman',
            'is_available' => true,
        ]);
        
        // Menu 5: Snack
        Menu::create([
            'name' => 'Kentang Goreng',
            'slug' => 'kentang-goreng',
            'description' => 'French fries renyah.',
            'price' => 12000,
            'stock' => 20,
            'category' => 'snack',
            'is_available' => true,
        ]);
    }
}