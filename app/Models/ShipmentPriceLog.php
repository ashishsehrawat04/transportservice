<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentPriceLog extends Model
{
    protected $fillable = [
        'user_id',
        'volume_cft',
        'distance_km',
        'calculation_type',
        'base_price',
        'weight_charge',
        'volume_charge',
        'distance_charge',
        'multiplier_applied',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_payment',
        'user_name',
        'user_email',
    ];
}
