<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseAddress extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'pickup_address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(WarehouseCartItem::class, 'item_id');
    }
}
