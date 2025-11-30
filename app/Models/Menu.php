<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;
    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'category',
        'image_path',
        'is_available',
    ];

    // Mengubah tipe data otomatis saat diambil dari database
    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // Helper sederhana untuk cek stok
    public function hasStock($quantity = 1)
    {
        return $this->stock >= $quantity;
    }
}