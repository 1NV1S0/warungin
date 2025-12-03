<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'price_at_time',
        'subtotal',
        'note',
    ];

    // Relasi: Item ini terhubung ke Menu apa?
    public function menu()
    {
        return $this->belongsTo(Menu::class)->withTrashed();
    }
    
    // Relasi: Item ini milik Order yang mana?
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}