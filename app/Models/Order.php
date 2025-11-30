<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'guest_token',
        'customer_name',
        'customer_phone',
        'table_id',
        'cashier_id',
        'total_amount',
        'order_type',
        'status',
        'booking_time',
        'payment_method'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'booking_time' => 'datetime',
    ];

    // Relasi: 1 Order punya BANYAK Order Items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi: 1 Order milik 1 Meja
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    
    // Relasi: 1 Order diurus oleh 1 Kasir (User)
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}