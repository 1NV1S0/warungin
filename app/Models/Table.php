<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'table_number',
        'capacity',
        'status' // available, occupied, reserved
    ];

    // Relasi: 1 Meja bisa punya BANYAK Order (History orderan meja itu)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}