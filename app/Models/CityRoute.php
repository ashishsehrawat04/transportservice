<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityRoute extends Model
{
    protected $fillable = [
        'from_city',
        'to_city',
        'rate_per_weight',
        'transit_days',
        'min_charge',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
