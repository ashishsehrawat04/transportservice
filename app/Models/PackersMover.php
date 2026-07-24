<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackersMover extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'price_per_km_per_kg',
        'min_charge',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cartItems()
    {
        return $this->hasMany(PackersMoverCartItem::class);
    }

    public function leads()
    {
        return $this->hasMany(PackersMoverLead::class);
    }
}
