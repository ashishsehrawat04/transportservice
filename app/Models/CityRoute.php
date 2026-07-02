<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityRoute extends Model
{
    protected $fillable = [
        'from_city',
        'to_city',
        'distance_km',
        'base_rate_per_km',
        'min_charge',
        'is_active',
        'base_rate_per_volume',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
