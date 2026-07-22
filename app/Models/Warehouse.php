<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'price_per_day_per_kg',
        'min_charge',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cartItems()
    {
        return $this->hasMany(WarehouseCartItem::class);
    }

    public function leads()
    {
        return $this->hasMany(WarehouseLead::class);
    }
}
