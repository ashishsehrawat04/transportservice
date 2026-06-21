<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportServicePrice extends Model
{
    protected $fillable = [
        'item_type',
        'description',
        'calculation_type',
        'base_price',
        'weight_rate_per_kg',
        'volume_rate_per_cft',
        'distance_rate_per_km',
        'multiplier',
        'min_charge',
        'max_charge',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
