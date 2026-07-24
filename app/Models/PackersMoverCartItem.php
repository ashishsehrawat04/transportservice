<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackersMoverCartItem extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'packers_mover_id',
        'item_name',
        'item_type',
        'quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'weight_kg',
        'pickup_date',
        'distance_km',
        'estimated_total',
        'charge_basis',
        'charge_weight_kg',
        'volumetric_weight_kg',
        'packers_mover_lead_id',
        'booking_status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'charge_weight_kg' => 'decimal:2',
        'volumetric_weight_kg' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function packersMover()
    {
        return $this->belongsTo(PackersMover::class);
    }

    public function packersMoverAddress()
    {
        return $this->hasOne(PackersMoverAddress::class, 'item_id');
    }

    public function packersMoverLead()
    {
        return $this->belongsTo(PackersMoverLead::class);
    }
}
